<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/PaymentService.php';
require_once __DIR__ . '/payment/PaymentStatusList.php';
require_once __DIR__ . '/payment/PaymentTypeEnum.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/DocsClientActivities.php';
require_once __DIR__ . '/../Classes/Settings.php';
require_once __DIR__ . '/../Classes/DocsPayments.php';
require_once __DIR__ . '/../Classes/Token.php';
require_once __DIR__ . '/../Classes/Transaction.php';
require_once __DIR__ . '/../Classes/docs2item.php';
require_once __DIR__ . '/../../app/helpers/GroupNumberHelper.php';

class DocumentService
{
    /**
     * @param $DocsId
     * @return array|void
     */
    public static function cancelDocument($DocsId)
    {
        try {
            $StatusPay = '';
            $CompanyNum = Auth::user()->CompanyNum;

            /// בדיקת תשלום אשראי
            /** @var Settings $SettingsInfo */
            $SettingsInfo = Settings::getSettings($CompanyNum);

            $DocsInfo = Docs::where('CompanyNum', '=', $CompanyNum)->where('id', '=', $DocsId)->first();

            if (empty($DocsInfo)) {
                json_message(lang('error_service_ajax'), false);
                exit;
            }

            $clientActivities = DocsClientActivities::getClientActivities($DocsId);

            $DocsType = $DocsInfo->TypeHeader;

            $client = new Client($DocsInfo->ClientId);

            $paymentSystem = PaymentService::getPaymentSystemByType($DocsInfo->TypeShva);

            $TempPaymentInfos = DocsPayment::getPaymentsByDocsId($DocsId, $CompanyNum);


            foreach ($TempPaymentInfos as $TempPaymentInfo) {
                if ($TempPaymentInfo->TypePayment != '3' || ($TempPaymentInfo->YaadCode == '0'))
                {
                    $StatusPay = lang('docs_receipt_0');
                    $TempPaymentInfo->RefAction = 1;
                    $TempPaymentInfo->save();
                }
                elseif ($DocsInfo->TypeShva == PaymentTypeEnum::TYPE_YAAD && $TempPaymentInfo->RefAction == '0') {
                    // YaadSarig
                    $tokenModel = Token::where('L4digit', '=', $TempPaymentInfo->L4digit)
                        ->where('ClientId', '=', $DocsInfo->ClientId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('Status', '=', '0')
                        ->where('Type', '=', '0')
                        ->orderBy('id', 'DESC')
                        ->first();

                    if (!$tokenModel) {
                        LoggerService::info([
                            'message' => 'Token model not found',
                            'docsPaymentModel' => $TempPaymentInfo,
                        ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
                        continue;
                    }

                    $order = OrderService::createOrder($client, $TempPaymentInfo->getSum(true),
                        $TempPaymentInfo->getPaymentsNumber(true), OrderLogin::TYPE_PAYMENT_CANCELED);

                    $order->PaymentMethod = PaymentService::getPaymentMethodByType($SettingsInfo->TypeShva);
                    $order->save();

                    if ($tokenModel->Token == '') {
                        $tokenData = $paymentSystem->getTokenFromYaad($TempPaymentInfo->YaadCode, $order, ['L4digit' => $TempPaymentInfo->L4digits]);
                        $tokenModel->Token = $tokenData['Token'];
                        $tokenModel->save();
                    }

                    $order->TokenId = $tokenModel->id;
                    $order->save();

                    try {
                        $refundData = $paymentSystem->makeRefundWithToken($order, $order->token());
                        $TempPaymentInfo->updateRefAction(true);

                        $StatusPay = lang('docs_receipt_0');
                    } catch (LogicException $e) {
                        // payment error
                        if (is_numeric($e->getMessage())) {
                            $CCode = $e->getMessage();
                            $StatusPay = PaymentStatusList::getErrorMessage($CCode);
                        } else {
                            $StatusPay = $e->getMessage();
                        }

                        LoggerService::error([
                            'message' => 'Error while making refund',
                            'response' => $e->getMessage(),
                            'docsPaymentModel' => $TempPaymentInfo,
                        ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
                    } catch (Throwable $e) {
                        // unexpected error
                        LoggerService::error($e, LoggerService::CATEGORY_YAADSARIG);

                        $StatusPay = lang('unknow_error_meshulam');
                    }
                }
                else if ($DocsInfo->TypeShva == PaymentTypeEnum::TYPE_MESHULAM && $TempPaymentInfo->RefAction == '0' && $TempPaymentInfo->TransactionId != '0')
                {
                    // Meshulam

                    $UserId = Auth::user()->id;
                    //// חיוב אשראי שמור משולם API

                    try {
                        $order = OrderService::createOrder($client, $TempPaymentInfo->getSum(true), 1, OrderLogin::TYPE_PAYMENT_CANCELED);

                        $refundData = $paymentSystem->makeRefund($SettingsInfo->MeshulamAPI, $SettingsInfo->MeshulamUserId, $TempPaymentInfo->YaadCode, $TempPaymentInfo->PayToken, $TempPaymentInfo->getSum(true), $TempPaymentInfo->MeshulamPageCode);

                        $TempPaymentInfo->updateRefAction(true);


                        $StatusPay = lang('docs_receipt_0');

                        $transaction = new Transaction();
                        $transaction->CompanyNum = $CompanyNum;
                        $transaction->ClientId = $TempPaymentInfo->ClientId;
                        $transaction->UpdateTransactionDetails = serialize($refundData);
                        $transaction->UserId = $UserId;
                        $transaction->save();

                        $order->TransactionId = $transaction->id;
                        $order->save();
                    } catch (Throwable $e) {
                        DB::table('transaction_error')->insertGetId(
                            array('CompanyNum' => $CompanyNum, 'ClientId' => $TempPaymentInfo->ClientId, 'UpdateTransactionDetails' => $e->getMessage(), 'UserId' => $UserId));

                        $StatusPay = $e->getMessage();
                    }
                }
                elseif ($DocsInfo->TypeShva == PaymentTypeEnum::TYPE_TRANZILA && $TempPaymentInfo->RefAction == '0') {
                    // Tranzila
                    $tokenModel = Token::where('L4digit', '=', $TempPaymentInfo->L4digit)
                        ->where('ClientId', '=', $DocsInfo->ClientId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('Status', '=', '0')
                        ->where('Type', '=', PaymentTypeEnum::TYPE_TRANZILA)
                        ->orderBy('id', 'DESC')
                        ->first();

                    if (!$tokenModel) {
                        LoggerService::info([
                            'message' => 'Token model not found',
                            'docsPaymentModel' => $TempPaymentInfo,
                        ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
                        continue;
                    }

                    $order = OrderService::createOrder($client, $TempPaymentInfo->getSum(true), 1, OrderLogin::TYPE_PAYMENT_CANCELED);

                    $order->PaymentMethod = PaymentService::getPaymentMethodByType($SettingsInfo->TypeShva);
                    $order->TokenId = $tokenModel->id;
                    $order->save();

                    try {
                        $refundData = $paymentSystem->makeRefundWithToken($order, $order->token(), $TempPaymentInfo->YaadCode, $TempPaymentInfo->ACode);
                        $TempPaymentInfo->updateRefAction(true);

                        $StatusPay = lang('docs_receipt_0');
                    } catch (LogicException $e) {
                        // payment error
                        $StatusPay = $e->getMessage();

                        LoggerService::error([
                            'message' => 'Error while making refund',
                            'response' => $e->getMessage(),
                            'docsPaymentModel' => $TempPaymentInfo,
                        ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
                    } catch (Throwable $e) {
                        // unexpected error
                        LoggerService::error($e, LoggerService::CATEGORY_TRANZILA);

                        $StatusPay = lang('unknow_error_meshulam');
                    }
                }
            }

/// בדיקת זיכוי מלא
//            $TempPaymentCount = DB::table('docs_payment')->where('RefAction', '=', '0')->where('DocsId', '=', $DocsId)->where('CompanyNum', '=', $CompanyNum)->count();
            if ($StatusPay === lang('docs_receipt_0')) {
                $StatusNew = 1;
                $Dates = date('Y-m-d H:i:s');
                //// בדיקת סניפים
                if ($SettingsInfo->BrandsMain != '0' && $SettingsInfo->MainAccounting == '1') {
                    $TrueCompanyNum = $SettingsInfo->BrandsMain;
                } else {
                    $TrueCompanyNum = $CompanyNum;
                }

                $UserDate = date('Y-m-d');
                $DocDate = date('Y-m-d');
                $DocMonth = date("m", strtotime($UserDate));
                $DocYear = date("Y", strtotime($UserDate));
                $DocTime = date('H:i:s');
                $UserId = Auth::user()->id;
                if ($DocsType == '320') {
                    ///// יצירת חשבונית זיכוי
                    $TypeHeader = '330';
                    /// סוג מסמך וקבלת ID
                    $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
                    $TypeDoc = $GetDocsId->id;
                    $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->first();
                    /// בדיקת מספור מסמך + תאריך אחרון
                    $DocsCountGets = DB::table('docs')->where('TrueCompanyNum', '=', $TrueCompanyNum)->where('TypeHeader', '=', $TypeHeader)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
                    if ($DocsCountGets && $DocsCountGets->TypeNumber == '') {
                        $TypeNumber = isset($DocsTableNew) ? $DocsTableNew->TypeNumber : '';
                    } else {
                        $TypeNumber = $DocsCountGets->TypeNumber + 1;
                    }
                    /// סוג מסמך וקבלת ID
                    $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('CompanyNum', '=', $CompanyNum)->first();
                    $TypeDoc = $GetDocsId->id;
                    /// מחולל מספר מסמך
                    $RandomNumber = GroupNumberHelper::generate();

                    $Minus = '-';
                    $PayStatus = '0';
                    $Refound = '1';
                    $BalanceAmount = '0.00';
                    $TypeHeader = isset($DocsTableNew) ? $DocsTableNew->TypeHeader : '';
                    $Accounts = isset($DocsTableNew) ? $DocsTableNew->Accounts : '';
                    $DocId = DB::table('docs')->insertGetId(
                        array('CompanyNum' => $DocsInfo->CompanyNum,
                            'Brands' => $DocsInfo->Brands,
                            'TrueCompanyNum' => $DocsInfo->TrueCompanyNum,
                            'TypeDoc' => $TypeDoc,
                            'TypeHeader' => $TypeHeader,
                            'TypeNumber' => $TypeNumber,
                            'ClientId' => $DocsInfo->ClientId,
                            'UserDate' => $UserDate,
                            'Dates' => $Dates,
                            'Amount' => $Minus . '' . $DocsInfo->Amount,
                            'Vat' => $DocsInfo->Vat,
                            'VatAmount' => $Minus . '' . $DocsInfo->VatAmount,
                            'DiscountType' => $DocsInfo->DiscountType,
                            'Discount' => $DocsInfo->Discount,
                            'DiscountAmount' => $Minus . '' . $DocsInfo->DiscountAmount,
                            'PaymentRole' => $DocsInfo->PaymentRole,
                            'Remarks' => $DocsInfo->Remarks,
                            'Company' => $DocsInfo->Company,
                            'CompanyId' => $DocsInfo->CompanyId,
                            'ContactName' => $DocsInfo->ContactName,
                            'Mobile' => $DocsInfo->Mobile,
                            'Phone' => $DocsInfo->Phone,
                            'Fax' => $DocsInfo->Fax,
                            'Email' => $DocsInfo->Email,
                            'UserId' => $UserId,
                            'DocConvert' => '0',
                            'PaymentTime' => $DocsInfo->PaymentTime,
                            'BalanceAmount' => $BalanceAmount,
                            'Street' => $DocsInfo->Street,
                            'Number' => $DocsInfo->Number,
                            'PostCode' => $DocsInfo->PostCode,
                            'City' => $DocsInfo->City,
                            'Accounts' => $Accounts,
                            'DocDate' => $DocDate,
                            'DocMonth' => $DocMonth,
                            'DocYear' => $DocYear,
                            'DocTime' => $DocTime,
                            'RandomUrl' => $RandomNumber,
                            'PayStatus' => $PayStatus,
                            'ManualInvoice' => '1',
                            'BusinessCompanyId' => $DocsInfo->BusinessCompanyId,
                            'BusinessType' => $DocsInfo->BusinessType,
                            'Refound' => $Refound,
                            'CpaType' => $SettingsInfo->CpaType
                        ));
                    /// שמירת נתוני מסמך פריטים
                    $TempDocListsInfo = DB::table('docslist')->where('DocsId', '=', $DocsId)->where('CompanyNum', '=', $CompanyNum)->get();
                    foreach ($TempDocListsInfo as $TempDocListInfo) {
                        $DocsList = DB::table('docslist')->insertGetId(
                            array('CompanyNum' => $TempDocListInfo->CompanyNum, 'Brands' => $TempDocListInfo->Brands, 'TrueCompanyNum' => $TempDocListInfo->TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $TypeHeader, 'TypeNumber' => $TypeNumber, 'DocsId' => $DocId, 'ClientId' => $TempDocListInfo->ClientId, 'ItemId' => $TempDocListInfo->ItemId, 'SKU' => $TempDocListInfo->SKU, 'ItemName' => $TempDocListInfo->ItemName, 'ItemText' => $TempDocListInfo->ItemText, 'ItemPrice' => $Minus . $TempDocListInfo->ItemPrice, 'ItemPriceVat' => $Minus . $TempDocListInfo->ItemPriceVat, 'ItemPriceVatDiscount' => $Minus . $TempDocListInfo->ItemPriceVatDiscount, 'ItemQuantity' => $Minus . $TempDocListInfo->ItemQuantity, 'ItemDiscountType' => $TempDocListInfo->ItemDiscountType, 'ItemDiscount' => $TempDocListInfo->ItemDiscount, 'ItemDiscountAmount' => $TempDocListInfo->ItemDiscountAmount, 'Itemtotal' => $Minus . $TempDocListInfo->Itemtotal, 'ItemTable' => $TempDocListInfo->ItemTable, 'Dates' => $Dates, 'UserDate' => $UserDate, 'TypeDocBasis' => $TempDocListInfo->TypeDocBasis, 'TypeDocBasisNumber' => $TempDocListInfo->TypeDocBasisNumber, 'Vat' => $TempDocListInfo->Vat, 'VatAmount' => $Minus . $TempDocListInfo->VatAmount, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'BusinessCompanyId' => $TempDocListInfo->BusinessCompanyId, 'BusinessType' => $TempDocListInfo->BusinessType));
                    }
                }
                /////  יצירת קבלת החזר
                $TypeHeader = '400';
                /// סוג מסמך וקבלת ID
                $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
                $TypeDoc = $GetDocsId->id;
                /// בדיקת מספור מסמך + תאריך אחרון
                $DocsCountGets = DB::table('docs')->where('TrueCompanyNum', '=', $TrueCompanyNum)->where('TypeHeader', '=', $TypeHeader)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
                if ($DocsCountGets && $DocsCountGets->TypeNumber == '') {
                    $TypeNumber = isset($DocsTableNew) ? $DocsTableNew->TypeNumber : '';
                } else {
                    $TypeNumber = $DocsCountGets->TypeNumber + 1;
                }
                /// סוג מסמך וקבלת ID
                $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('CompanyNum', '=', $CompanyNum)->first();
                $TypeDoc = $GetDocsId->id;
                $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->first();
                /// מחולל מספר מסמך
                $RandomNumber = GroupNumberHelper::generate();
                $Minus = '-';
                $PayStatus = '0';
                $Refound = '1';
                $BalanceAmount = '0.00';
                if ($DocsType == '400') {
                    $newAmount = str_replace("-", "", $DocsInfo->Amount);
                } else {
                    $newAmount = $Minus . $DocsInfo->Amount;
                }
                $TypeHeader = isset($DocsTableNew) ? $DocsTableNew->TypeHeader : '';
                $Accounts = isset($DocsTableNew) ? $DocsTableNew->Accounts : '';

                // create $activityJson
                $activityJson = '{"data": [';
                $NewAmount = abs($DocsInfo->Amount);
                $NewAmountFinal = abs($DocsInfo->Amount);
                $countActivities = count($clientActivities);
                foreach ($clientActivities as $key => $ActivityInfo) {
                    $ItemText = htmlentities($ActivityInfo->ItemText);
                    $BalanceMoney = $ActivityInfo->BalanceRefoundMoney;
                    $ItemId = $ActivityInfo->ItemId;
                    $BalanceRefoundMoney = (($ActivityInfo->ItemPriceVatDiscount + $ActivityInfo->VatAmount) - $ActivityInfo->BalanceMoney) - ($ActivityInfo->TrueBalanceRefoundMoney);
                    /// בדיקת מצב תקבול לעומת תשלום פעילות
                    if ($NewAmount >= $BalanceRefoundMoney) {
                        /// תשלום מלא
                        $NewAmount = $NewAmount - $BalanceRefoundMoney; ///50
                        $NewAmounts = str_replace('-', "", $NewAmount - $NewAmountFinal); // 40
                        $NewAmountFinal = $DocsInfo->Amount - $NewAmounts;
                        $activityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "0", "FixNewAmount": "' . $NewAmounts . '"}';
                    } else {
                        $FixNewAmount = $NewAmount;
                        $NewAmount = $BalanceRefoundMoney - $NewAmount;
                        $activityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "' . $NewAmount . '", "FixNewAmount": "' . $FixNewAmount . '"}';
                        $NewAmount = '0';
                    }
                    if ($key < ($countActivities - 1))
                        $activityJson .= ',';
                }
                $activityJson .= ']}';

                $DocId = DB::table('docs')->insertGetId(
                    array('CompanyNum' => $DocsInfo->CompanyNum,
                        'Brands' => $DocsInfo->Brands,
                        'TrueCompanyNum' => $DocsInfo->TrueCompanyNum,
                        'TypeDoc' => $TypeDoc,
                        'TypeHeader' => $TypeHeader,
                        'TypeNumber' => $TypeNumber,
                        'ClientId' => $DocsInfo->ClientId,
                        'UserDate' => $UserDate,
                        'Dates' => $Dates,
                        'Amount' => $newAmount,
                        'Vat' => $DocsInfo->Vat,
                        'VatAmount' => $newAmount,
                        'DiscountType' => $DocsInfo->DiscountType,
                        'Discount' => $DocsInfo->Discount,
                        'PaymentRole' => $DocsInfo->PaymentRole,
                        'Remarks' => $DocsInfo->Remarks,
                        'Company' => $DocsInfo->Company,
                        'CompanyId' => $DocsInfo->CompanyId,
                        'ContactName' => $DocsInfo->ContactName,
                        'Mobile' => $DocsInfo->Mobile,
                        'Phone' => $DocsInfo->Phone,
                        'Fax' => $DocsInfo->Fax,
                        'Email' => $DocsInfo->Email,
                        'UserId' => $UserId,
                        'DocConvert' => '0',
                        'PaymentTime' => $DocsInfo->PaymentTime,
                        'BalanceAmount' => $BalanceAmount,
                        'Street' => $DocsInfo->Street,
                        'Number' => $DocsInfo->Number,
                        'PostCode' => $DocsInfo->PostCode,
                        'City' => $DocsInfo->City,
                        'Accounts' => $Accounts,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'RandomUrl' => $RandomNumber,
                        'PayStatus' => $PayStatus,
                        'ManualInvoice' => '1',
                        'BusinessCompanyId' => $DocsInfo->BusinessCompanyId,
                        'BusinessType' => $DocsInfo->BusinessType,
                        'Refound' => $Refound,
                        'ActivityJson' => $activityJson,
                        'CpaType' => $SettingsInfo->CpaType
                    ));

                $ReturnAmountLeft = -$DocsInfo->Amount;
                foreach ($clientActivities as $clientActivity) {
                    DocsClientActivities::saveRelation($DocId, $clientActivity->id);
                    //todo- shon after check all payment
//                    $clientActivity->TrueBalanceRefoundMoney =  $clientActivity->ItemPriceVatDiscount + $clientActivity->VatAmount;
//                    $clientActivity->save();
                    if ($clientActivity->isForMeeting == 1 && $clientActivity->isPaymentForSingleClass == 1) {
                        $ReturnAmount = min($ReturnAmountLeft, $clientActivity->ItemPrice - $clientActivity->BalanceMoney);

                        $clientActivity->BalanceMoney += $ReturnAmount;
                        $clientActivity->save();
                        $client->updateBalanceAmount();

                        $ReturnAmountLeft -= $ReturnAmount;
                    }
                }

                $GetAmounts = DB::table('docs_payment')->where('DocsId', '=', $DocsId)->where('CompanyNum', '=', $CompanyNum)->get();

                foreach ($GetAmounts as $TempDocPaymentInfo) {
                    $DocsList = DB::table('docs_payment')->insertGetId(array(
                        'CompanyNum' => $TempDocPaymentInfo->CompanyNum,
                        'Brands' => $TempDocPaymentInfo->Brands,
                        'TrueCompanyNum' => $TempDocPaymentInfo->TrueCompanyNum,
                        'TypeDoc' => $TypeDoc,
                        'TypeHeader' => $TypeHeader,
                        'TypeNumber' => $TypeNumber,
                        'DocsId' => $DocId,
                        'ClientId' => $TempDocPaymentInfo->ClientId,
                        'TypePayment' => $TempDocPaymentInfo->TypePayment,
                        'Amount' => $Minus . '' . $TempDocPaymentInfo->Amount,
                        'L4digit' => $TempDocPaymentInfo->L4digit,
                        'YaadCode' => $TempDocPaymentInfo->YaadCode,
                        'CCode' => $TempDocPaymentInfo->CCode,
                        'ACode' => $TempDocPaymentInfo->ACode,
                        'Bank' => $TempDocPaymentInfo->Bank,
                        'Payments' => $TempDocPaymentInfo->Payments,
                        'Brand' => $TempDocPaymentInfo->Brand,
                        'BrandName' => $TempDocPaymentInfo->BrandName,
                        'Issuer' => $TempDocPaymentInfo->Issuer,
                        'tashType' => $TempDocPaymentInfo->tashType,
                        'CheckBank' => $TempDocPaymentInfo->CheckBank,
                        'CheckBankSnif' => $TempDocPaymentInfo->CheckBankSnif,
                        'CheckBankCode' => $TempDocPaymentInfo->CheckBankCode,
                        'CheckNumber' => $TempDocPaymentInfo->CheckNumber,
                        'CheckDate' => $TempDocPaymentInfo->CheckDate,
                        'BankNumber' => $TempDocPaymentInfo->BankNumber,
                        'BankDate' => $TempDocPaymentInfo->BankDate,
                        'Dates' => $Dates,
                        'UserId' => $UserId,
                        'Excess' => $Minus . '' . $TempDocPaymentInfo->Excess,
                        'UserDate' => $UserDate,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'ActivityJson' => $activityJson,
                        'CreditType' => $TempDocPaymentInfo->CreditType,
                        'BusinessCompanyId' => $SettingsInfo->CompanyId,
                        'BusinessType' => $SettingsInfo->BusinessType,
                        'Refound' => $Refound,
                        'StatusInvoice' => $DocsType == '320' ? '1' : '0',
                    ));
                }

                if(!empty($DocItems = docs2item::getDocsItemsByDocId((int)$CompanyNum, (int)$DocsInfo->id))){
                    foreach($DocItems as $DocItem){
                        $Doc2Item = new docs2item();
                        $Doc2Item->__set('CompanyNum', $DocItem->CompanyNum);
                        $Doc2Item->__set('TrueCompanyNum', $DocItem->CompanyNum);
                        $Doc2Item->__set('Brands', $DocItem->Brands);
                        $Doc2Item->__set('ClientId', $DocItem->ClientId);
                        $Doc2Item->__set('ItemId', $DocItem->ItemId);
                        $Doc2Item->__set('DocsId', $DocId);
                        $Doc2Item->__set('Amount', $DocItem->Amount * -1);
                        $Doc2Item->__set('Department', $DocItem->Department);
                        $Doc2Item->__set('MemberShip', $DocItem->MemberShip);
                        $Doc2Item->__set('ItemName', $DocItem->ItemName);
                        $Doc2Item->__set('UserDate', date('Y-m-d'));
                        $Doc2Item->__set('BusinessType', $DocItem->BusinessType);
                        $Doc2Item->__set('BusinessCompanyId', $DocItem->BusinessCompanyId);
                        $Doc2Item->save();
                    }
                }
                
                ///// עדכון כל השדות של המסמך המקורי
                DB::table('docs')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $DocsId)
                    ->update(array('Refound' => '1', 'RefAction' => '1'));
                DB::table('docs_payment')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('DocsId', '=', $DocsId)
                    ->update(array('RefAction' => '1'));
            } else {
                $StatusNew = 0;
            }
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_PAYMENT_PROCESS_REFUND);

            $StatusPay = 'שגיאה ' . $e->getCode();
            $StatusNew = 0;
        }

        return [
            'StatusPay' => $StatusPay,
            'StatusNew' => $StatusNew,
        ];
    }
}
