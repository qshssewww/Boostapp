<?php
require_once __DIR__ . '/../../Classes/Company.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/DocsTable.php';
require_once __DIR__ . '/../../Classes/docs2item.php';
require_once __DIR__ . '/../../Classes/DocsPayments.php';
require_once __DIR__ . '/../../Classes/DocsLinkToInvoice.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../Classes/Notificationcontent.php';
require_once __DIR__ . '/../../Classes/TempReceiptPayment.php';
require_once __DIR__ . '/../../Classes/TempReceiptPaymentClient.php';
require_once __DIR__ . '/../PaymentService.php';
require_once __DIR__ . '/../payment/PaymentSystem.php';
require_once __DIR__ . '/../../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../../../app/enums/Docs/DocPaymentTypeEnum.php';
require_once __DIR__ . '/../../../app/controllers/responses/BaseResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/IdResponse.php';
require_once __DIR__ . '/../LoggerService.php';

class ReceiptService
{
    // move to utils
    /**
     * @param $url
     * @return bool|string
     */
    private static function getTinyUrl($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * @param $getAmount
     * @param $finalInvoiceId
     * @param $companyNum
     * @param $clientId
     * @return string
     */
    private static function clientActivityInfo($getAmount, $finalInvoiceId, $companyNum, $clientId): string
    {
        /// עדכון מנוי בתשלום מלא / תשלום חלקי
        $activityJson = '';
        $activityJson .= '{"data": [';
        $newAmount = $getAmount;
        $newTempId = explode(',', $finalInvoiceId);

        foreach ($newTempId as $check) {
            $activityId = $check;
            $activityInfo = DB::table('client_activities')->where('CompanyNum', '=', $companyNum)->where('id', '=', $activityId)->first();
            $itemText = htmlentities($activityInfo->ItemText);
            $balanceMoney = $activityInfo->BalanceMoney;
            $itemId = $activityInfo->ItemId;
            $trueClientId = $activityInfo->ClientId;

            /// בדיקת מצב תקבול לעומת תשלום פעילות
            if ($newAmount >= $balanceMoney) {
                /// סוגר פעילות ותשלום מלא
                $newAmount = $newAmount - $balanceMoney;
                DB::table('client_activities')
                    ->where('id', $activityId)
                    ->where('ClientId', '=', $trueClientId)
                    ->where('CompanyNum', '=', $companyNum)
                    ->update(array('BalanceMoney' => '0', 'TruePays' => $clientId));

                $activityJson .= '{"ItemText": "' . $itemText . '", "ItemId": "' . $itemId . '", "OldBalanceMoney": "' . $balanceMoney . '", "NewAmount": "0"},';
            } else {
                $newAmount = $balanceMoney - $newAmount;
                DB::table('client_activities')
                    ->where('id', $activityId)
                    ->where('ClientId', '=', $trueClientId)
                    ->where('CompanyNum', '=', $companyNum)
                    ->update(array('BalanceMoney' => $newAmount, 'TruePays' => $clientId));

                $activityJson .= '{"ItemText": "' . $itemText . '", "ItemId": "' . $itemId . '", "OldBalanceMoney": "' . $balanceMoney . '", "NewAmount": "' . $newAmount . '"},';

                $newAmount = 0;
            }
        }

        $activityJson = rtrim($activityJson, ',');
        $activityJson .= ']}';
        return $activityJson;
    }

    /**
     * @param $clientId
     * @param $clientGroupNumber
     * @param $companyNum
     * @param $clientActivityId
     * @param bool $isCard
     * @param bool $isDocs
     */
    public static function saveReceipts($clientId, $clientGroupNumber, $companyNum, $clientActivityId, bool $isCard = false, bool $isDocs = false): void
    {
        if (!$isDocs) {
            $TempReceipt = new TempReceiptPaymentClient();
        } else {
            $TempReceipt = new TempReceiptPayment();
        }

        $tempPaymentsInfo = $TempReceipt::getReceiptPaymentWithOrWithOutCard($clientId, $clientGroupNumber, $companyNum, $clientActivityId, $isCard);
        //if done with a payment delete all temps
        if (empty($tempPaymentsInfo)) {
            $tempPaymentsInfo = $TempReceipt::getReceiptPaymentTempByClientId($clientId, $clientGroupNumber, $companyNum);
            foreach ($tempPaymentsInfo as $tempsPayment) {
                $tempsPayment->delete();
            }
            return;
        }

        /**
         * INFO: we call "new Company($companyNum)" instead of Company::getInstance() because
         * method getInstance() uses current user's CompanyNum, but payment systems on response
         * are not logged in, so Auth::user() is NULL
         *
         * @var $settingsInfo Company
         */
        $settingsInfo = (new Company($companyNum));
        $finalInvoiceId = $clientActivityId;
        $cpaType = $settingsInfo->CpaType;
        $userDate = date('Y-m-d');
        $dates = date('Y-m-d H:i:s');

        //todo-change to 305 or 300
        $typeHeader = '400'; /// קבלה
        ///
        $manualInvoice = '0';
        $docConvert = '0';
        $paymentRole = '1';

        //// בדיקת סניפים
        if ($settingsInfo->BrandsMain != '0' && $settingsInfo->MainAccounting == '1') {
            $trueCompanyNum = $settingsInfo->BrandsMain;
        } else {
            $trueCompanyNum = $companyNum;
        }

        //todo-add all docs type
        /// סוג מסמך וקבלת ID
        $docsTableNew = DB::table('docstable')
            ->where('TypeHeader', '=', $typeHeader)
            ->where('TrueCompanyNum', '=', $trueCompanyNum)
            ->first();
        $typeDoc = $docsTableNew->id;//87 for TypeHeader=400 קבלה / 83 for TypeHeader=305 חשבונית-מס

        if ($docsTableNew->Status == '1') {
            throw new LogicException('docstable status error');
        }

        //checking the receipts amount
        $getAmount = 0; //todo-ony from param remove if
        if ($isCard) {
            $getAmount = $tempPaymentsInfo[0]->Amount;
        } else {
            foreach ($tempPaymentsInfo as $tempsPayment) {
                $getAmount += $tempsPayment->Amount;
            }
        }

        //get client Activity info and update the db
        $activityJson = self::clientActivityInfo($getAmount, $finalInvoiceId, $companyNum, $clientId);//todo-only-in-400/320

        //todo-only-in-400/320
        if ($isCard) {
            if ($order = $tempPaymentsInfo[0]->order()) {
                $order->Description = $activityJson;
                $order->save();
            }
        }


        //todo function get docs number
        $docsCountGets = DB::table('docs')
            ->where('TrueCompanyNum', '=', $trueCompanyNum)
            ->where('TypeHeader', '=', $typeHeader)
            ->orderBy('TypeNumber', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
        if (property_exists($docsCountGets, 'TypeNumber')) {
            $typeNumber = $docsCountGets->TypeNumber + 1;
        } else {
            $typeNumber = $docsTableNew->TypeNumber;
        }
        //todo function get remark
        $remarks = '';
        if (property_exists($docsTableNew, 'DocsRemarks')) {
            $remarks = $docsTableNew->DocsRemarks;
        }

        //todo function in client
        /// פרטי לקוח
        $clientDocInfo = DB::table('client')
            ->where('id', '=', $clientId)
            ->where('CompanyNum', '=', $companyNum)
            ->first();
        $brands = $clientDocInfo->Brands;
        if (property_exists($clientDocInfo, 'Company') && !$clientDocInfo->Company == '') {
            $Company = htmlentities($clientDocInfo->Company);
        } else {
            $Company = htmlentities($clientDocInfo->CompanyName);
        }

        $paymentRole = '1';
        $PaymentTime = $userDate;
        $DocDate = date('Y-m-d');
        $DocMonth = date("m", strtotime($userDate));
        $DocYear = date("Y", strtotime($userDate));
        $DocTime = date('H:i:s');
        /// מחולל מספר מסמך
        $randomNumber = GroupNumberHelper::generate();
        $docsGetAmount = '-' . $getAmount;
        $city = '';

        //todo function in client
        if ($clientDocInfo->City != '0') {
            $businessSettingsCity = DB::table('cities')->where('CityId', '=', $clientDocInfo->City)->first();
            if (property_exists($businessSettingsCity, 'City')) {
                $city = $businessSettingsCity->City;
            }
        }
        if ($clientDocInfo->Street == '0' || $clientDocInfo->Street == '99999999') {
            $street = $clientDocInfo->StreetH;
        } else {
            $businessSettingsStreet = DB::table('street')->where('id', '=', $clientDocInfo->Street)->first();
            if (property_exists($businessSettingsStreet, 'Street')) {
                $street = $businessSettingsStreet->Street;
            }
        }
        //todo-only-in-400/320
        if ($isCard && $tempPaymentsInfo[0]->PaymentType == 3) {
            $divisionArr = self::divideToPayments($docsGetAmount, $tempPaymentsInfo[0]->Payments);
            $docsGetAmount = $divisionArr["firstPayment"];
        }

        //todo-need go deep
        $docId = DB::table('docs')->insertGetId(
            array('CompanyNum' => $companyNum,
                'Brands' => $brands,
                'TrueCompanyNum' => $trueCompanyNum,
                'TypeDoc' => $typeDoc,
                'TypeHeader' => $docsTableNew->TypeHeader,
                'TypeNumber' => $typeNumber,
                'ClientId' => $clientId,
                'UserDate' => $userDate,
                'Dates' => $dates,
                'Amount' => $docsGetAmount,
                'Vat' => '0',
                'VatAmount' => '0',
                'DiscountType' => '1',
                'Discount' => '0',
                'DiscountAmount' => '0',
                'PaymentRole' => '1',
                'Remarks' => $remarks,
                'Company' => $Company,
                'CompanyId' => $clientDocInfo->CompanyId,
                'ContactName' => $clientDocInfo->CompanyName,
                'Mobile' => $clientDocInfo->ContactMobile,
                'Phone' => $clientDocInfo->ContactPhone,
                'Fax' => $clientDocInfo->ContactFax,
                'Email' => $clientDocInfo->Email,
                'UserId' => Auth::user()->id,
                'ManualInvoice' => $manualInvoice,
                'DocConvert' => $docConvert,
                'PaymentTime' => $PaymentTime,
                'BalanceAmount' => '0',
                'Street' => $street,
                'Number' => $clientDocInfo->Number,
                'PostCode' => $clientDocInfo->PostCode,
                'City' => $city,
                'Accounts' => $docsTableNew->Accounts,
                'DocDate' => $DocDate,
                'DocMonth' => $DocMonth,
                'DocYear' => $DocYear,
                'DocTime' => $DocTime,
                'RandomUrl' => $randomNumber,
                'ActivityJson' => $activityJson,
                'Status' => '1',
                'BusinessCompanyId' => $settingsInfo->CompanyId,
                'BusinessType' => $settingsInfo->BusinessType,
                'TypeShva' => $settingsInfo->TypeShva,
                'CpaType' => $settingsInfo->CpaType
            ));

        DocsClientActivities::saveRelation($docId, $clientActivityId);

        foreach ($tempPaymentsInfo as $tempDocPaymentInfo) {
            //if not credit card make all docs payment in one receipt (docs)
            if ($tempDocPaymentInfo->TypePayment != '3') {
                $docsList = DB::table('docs_payment')->insertGetId(
                    array('CompanyNum' => $companyNum,
                        'Brands' => $brands,
                        'TrueCompanyNum' => $trueCompanyNum,
                        'TypeDoc' => $typeDoc,
                        'TypeHeader' => $typeHeader,
                        'TypeNumber' => $typeNumber,
                        'DocsId' => $docId,
                        'ClientId' => $clientId,
                        'TypePayment' => $tempDocPaymentInfo->TypePayment,
                        'Amount' => $tempDocPaymentInfo->Amount,
                        'L4digit' => $tempDocPaymentInfo->L4digit,
                        'YaadCode' => $tempDocPaymentInfo->YaadCode,
                        'CCode' => $tempDocPaymentInfo->CCode,
                        'ACode' => $tempDocPaymentInfo->ACode,
                        'Bank' => $tempDocPaymentInfo->Bank,
                        'Payments' => $tempDocPaymentInfo->Payments,
                        'Brand' => $tempDocPaymentInfo->Brand,
                        'BrandName' => $tempDocPaymentInfo->BrandName,
                        'Issuer' => $tempDocPaymentInfo->Issuer,
                        'tashType' => $tempDocPaymentInfo->tashType,
                        'CheckBank' => $tempDocPaymentInfo->CheckBank,
                        'CheckBankSnif' => $tempDocPaymentInfo->CheckBankSnif,
                        'CheckBankCode' => $tempDocPaymentInfo->CheckBankCode,
                        'CheckNumber' => $tempDocPaymentInfo->CheckNumber,
                        'CheckDate' => $tempDocPaymentInfo->CheckDate,
                        'BankNumber' => $tempDocPaymentInfo->BankNumber,
                        'BankDate' => $tempDocPaymentInfo->BankDate,
                        'Dates' => $dates,
                        'UserId' => Auth::user()->id,
                        'Excess' => $tempDocPaymentInfo->Excess,
                        'UserDate' => $userDate,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'CreditType' => $tempDocPaymentInfo->CreditType,
                        'ActivityJson' => $activityJson,
                        'BusinessCompanyId' => $settingsInfo->CompanyId,
                        'BusinessType' => $settingsInfo->BusinessType,
                        'PayToken' => $tempDocPaymentInfo->PayToken,
                        'TransactionId' => $tempDocPaymentInfo->TransactionId,
                        'MeshulamPageCode' => $tempDocPaymentInfo->MeshulamPageCode,
                    ));
            } /// פירוט תקבולי אשראי - כל תשלום בשורה נפרדת
            else {
                /// תשלום אחד
                if ($tempDocPaymentInfo->Payments == '1' || $tempDocPaymentInfo->PaymentType == 1) {
                    $userDate = $tempDocPaymentInfo->UserDate;
                    $creditDate = date('Y-m-d');
                    $docsList = DB::table('docs_payment')->insertGetId(
                        array('CompanyNum' => $companyNum,
                            'Brands' => $brands,
                            'TrueCompanyNum' => $trueCompanyNum,
                            'TypeDoc' => $typeDoc,
                            'TypeHeader' => $typeHeader,
                            'TypeNumber' => $typeNumber,
                            'DocsId' => $docId,
                            'ClientId' => $clientId,
                            'TypePayment' => $tempDocPaymentInfo->TypePayment,
                            'Amount' => $tempDocPaymentInfo->Amount,
                            'L4digit' => $tempDocPaymentInfo->L4digit,
                            'YaadCode' => $tempDocPaymentInfo->YaadCode,
                            'CCode' => $tempDocPaymentInfo->CCode,
                            'ACode' => $tempDocPaymentInfo->ACode,
                            'Bank' => $tempDocPaymentInfo->Bank,
                            'Payments' => $tempDocPaymentInfo->Payments,
                            'Brand' => $tempDocPaymentInfo->Brand,
                            'BrandName' => $tempDocPaymentInfo->BrandName,
                            'Issuer' => $tempDocPaymentInfo->Issuer,
                            'tashType' => $tempDocPaymentInfo->tashType,
                            'CheckBank' => $tempDocPaymentInfo->CheckBank,
                            'CheckBankSnif' => $tempDocPaymentInfo->CheckBankSnif,
                            'CheckBankCode' => $tempDocPaymentInfo->CheckBankCode,
                            'CheckNumber' => $tempDocPaymentInfo->CheckNumber,
                            'CheckDate' => $creditDate,
                            'BankNumber' => $tempDocPaymentInfo->BankNumber,
                            'BankDate' => $tempDocPaymentInfo->BankDate,
                            'Dates' => $dates,
                            'UserId' => Auth::user()->id,
                            'Excess' => $tempDocPaymentInfo->Excess,
                            'UserDate' => $userDate,
                            'DocDate' => $DocDate,
                            'DocMonth' => $DocMonth,
                            'DocYear' => $DocYear,
                            'DocTime' => $DocTime,
                            'CreditType' => $tempDocPaymentInfo->CreditType,
                            'ActivityJson' => $activityJson,
                            'BusinessCompanyId' => $settingsInfo->CompanyId,
                            'BusinessType' => $settingsInfo->BusinessType,
                            'PayToken' => $tempDocPaymentInfo->PayToken,
                            'TransactionId' => $tempDocPaymentInfo->TransactionId,
                            'MeshulamPageCode' => $tempDocPaymentInfo->MeshulamPageCode,
                        ));
                    break;
                } elseif ($tempDocPaymentInfo->PaymentType == 3) {
                    /// הוספת פרטים להוראת קבע
                    $lastPayment = $userDate;
                    $numDate = 1;
                    $numPayment = $tempDocPaymentInfo->Payments;

                    $firstPayment = $divisionArr["firstPayment"];
                    $secondPayment = $divisionArr["secondPayment"];

                    $payTokenId = DB::table('boostapp.paytoken')->insertGetId(
                        array(
                            'CompanyNum' => $companyNum,
                            'Brands' => $brands,
                            'ClientId' => $clientId,
                            'TokenId' => $tempDocPaymentInfo->TokenId,
                            'TypeKeva' => 1,/// on hok = 0, on limited payments = 1
                            'NumDate' => $numDate,
                            'TypePayment' => 3,
                            'Amount' => $secondPayment,
                            'NumPayment' => $numPayment,     /// on hok = 999, on limited payments = num of payments
                            'LastPayment' => $lastPayment,
                            'NextPayment' => null,
                            'CountPayment' => 0,
                            'tashType' => 0,
                            'Tash' => 1,
                            'Text' => '',
                            'ItemId' => (int)$finalInvoiceId,
                            'PageId' => 0,
                            'UserId' => 0
                        ));

                    $nextPayment = $lastPayment;
                    $trueDayNum = date("d", strtotime($nextPayment));
                    //$TrueDatePayment = $nextPayment;
                    //// מחזור חיוב ראשון

                    DB::table('boostapp.payment')->insertGetId(
                        array('CompanyNum' => $companyNum,
                            'ClientId' => $clientId,
                            'TypeKeva' => 1,/// on hok = 0, on limited payments = 1
                            'Amount' => $firstPayment,
                            'Date' => $nextPayment,
                            'Status' => 1,
                            'Error' => 'עסקה מאושרת',
                            'NumTry' => 1,
                            'L4digit' => $tempDocPaymentInfo->L4digit,
                            'YaadCode' => $tempDocPaymentInfo->YaadCode,
                            'CCode' => $tempDocPaymentInfo->CCode,
                            'ACode' => $tempDocPaymentInfo->ACode,
                            'Bank' => $tempDocPaymentInfo->Bank,
                            'Brand' => $tempDocPaymentInfo->Brand,
                            'Issuer' => $tempDocPaymentInfo->Issuer ?? '0',
                            'BrandName' => $tempDocPaymentInfo->BrandName,
                            'tashType' => $tempDocPaymentInfo->tashType,
                            'TryDate' => null,
                            'LastDate' => null,
                            'KevaId' => $payTokenId,
                            'RandomUrl' => $randomTokenNumber ?? null,
                            'NumPayment' => 1,
                            'TrueDayNum' => $trueDayNum));

                    //// קליטת מחזורי חיוב
                    if ($numPayment > 24) {
                        $numPayment = 24;
                    }
                    for ($i = 2; $i <= $numPayment; $i++) {
                        $fixDateDay = date("d", strtotime($nextPayment));
                        $lastPayment = date("Y-m", strtotime($nextPayment));
                        $fixTypePayment = 'month';
                        $ItemsTime = '+' . $numDate . ' ' . $fixTypePayment;
                        $time = strtotime($lastPayment);
                        $nextPayment = date("Y-m", strtotime($ItemsTime, $time));
                        $nextPaymentLasatDay = date("t", strtotime($nextPayment));

                        if ($fixDateDay <= $nextPaymentLasatDay) {
                            $nextPayment .= '-' . $trueDayNum;
                        } else {
                            $nextPayment .= '-' . $nextPaymentLasatDay;
                        }

                        /// מחולל מספר מסמך
                        $randomTokenNumber = GroupNumberHelper::generate();
                        $AddPayment = DB::table('boostapp.payment')->insertGetId([
                            'CompanyNum' => $companyNum,
                            'ClientId' => $clientId,
                            'TypeKeva' => 1,/// on hok = 0, on limited payments = 1
                            'Amount' => $secondPayment,
                            'Date' => $nextPayment,
                            'Status' => 0,
                            'NumTry' => 0,
                            'TryDate' => null,
                            'LastDate' => null,
                            'KevaId' => $payTokenId,
                            'RandomUrl' => $randomTokenNumber,
                            'NumPayment' => $i,
                            'TrueDayNum' => $trueDayNum
                        ]);
                    }
                    ///add docs payment
                    $docsPaymentArr = [
                        'CompanyNum' => $companyNum,
                        'Brands' => $brands,
                        'TrueCompanyNum' => $trueCompanyNum,
                        'TypeDoc' => $typeDoc,
                        'TypeHeader' => $typeHeader,
                        'TypeNumber' => $typeNumber,
                        'DocsId' => $docId,
                        'ClientId' => $clientId,
                        'TypePayment' => '3',
                        'Amount' => $firstPayment,
                        'L4digit' => $tempDocPaymentInfo->L4digit,
                        'YaadCode' => $tempDocPaymentInfo->YaadCode,
                        'CCode' => $tempDocPaymentInfo->CCode,
                        'ACode' => $tempDocPaymentInfo->ACode,
                        'Bank' => $tempDocPaymentInfo->Bank,
                        'Brand' => $tempDocPaymentInfo->Brand,
                        'Issuer' => $tempDocPaymentInfo->Issuer ?? '0',
                        'BrandName' => $tempDocPaymentInfo->BrandName,
                        'tashType' => $tempDocPaymentInfo->tashType,
                        'Payments' => 1,    /// hok periodic payment
                        'CheckDate' => date("Y-m-d"),
                        'Dates' => $dates,
                        'UserId' => '0',
                        'UserDate' => $userDate,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'CreditType' => 'עסקה שבוצע באתר, על ידי הסטודיו',
                        'ActivityJson' => $activityJson,
                        'BusinessCompanyId' => $settingsInfo->CompanyId,
                        'BusinessType' => $settingsInfo->BusinessType,
                        'PayToken' => $tempDocPaymentInfo->PayToken,
                        'TransactionId' => $tempDocPaymentInfo->TransactionId,
                        'MeshulamPageCode' => $tempDocPaymentInfo->MeshulamPageCode,
                    ];
                    $DocsList = DB::table('boostapp.docs_payment')->insertGetId($docsPaymentArr);
                    break;
                } else {
                    $userDate = $tempDocPaymentInfo->UserDate;
                    $userId = Auth::user()->id;
                    $payments = $tempDocPaymentInfo->Payments;
                    $amount = $tempDocPaymentInfo->Amount;
                    $money = $amount;
                    $Payment = $payments;
                    $myMoney = $money / $Payment;
                    $myMoney = number_format((float)$myMoney, 2, '.', '');
                    list($whole, $decimal) = explode('.', $myMoney);
                    $checkPayment = $whole * ($Payment - 1);
                    $firstPayment = $money - $checkPayment;
                    $firstPayment = number_format((float)$firstPayment, 2, '.', '');
                    $secondPayment = $whole;
                    $secondPayment = number_format((float)$secondPayment, 2, '.', '');
                    $count = $payments;

                    for ($i = 1; $i <= $count; $i++) {
                        if ($i == 1) {
                            $fixAmount = $firstPayment;
                        } else {
                            $fixAmount = $secondPayment;
                        }
                        $paymentsNew = $i;
                        // $timenew = strtotime(date("Y-m-02", strtotime($userDate)));
                        // $final = date("Y-m", strtotime($addDate, $timenew)) . '-02';
                        // $creditDate = $final;
                        $add = $i - 1;
                        $addDate = '+' . $add . ' month';
                        $creditDate = date('Y-m-d', strtotime($addDate, strtotime($userDate)));
                        $docsList = DB::table('docs_payment')->insertGetId(
                            array('CompanyNum' => $companyNum,
                                'Brands' => $brands,
                                'TrueCompanyNum' => $trueCompanyNum,
                                'TypeDoc' => $typeDoc,
                                'TypeHeader' => $typeHeader,
                                'TypeNumber' => $typeNumber,
                                'DocsId' => $docId,
                                'ClientId' => $clientId,
                                'TypePayment' => $tempDocPaymentInfo->TypePayment,
                                'Amount' => $fixAmount,
                                'L4digit' => $tempDocPaymentInfo->L4digit,
                                'YaadCode' => $tempDocPaymentInfo->YaadCode,
                                'CCode' => $tempDocPaymentInfo->CCode,
                                'ACode' => $tempDocPaymentInfo->ACode,
                                'Bank' => $tempDocPaymentInfo->Bank,
                                'Payments' => $paymentsNew,
                                'Brand' => $tempDocPaymentInfo->Brand,
                                'BrandName' => $tempDocPaymentInfo->BrandName,
                                'Issuer' => $tempDocPaymentInfo->Issuer,
                                'tashType' => $tempDocPaymentInfo->tashType,
                                'CheckBank' => $tempDocPaymentInfo->CheckBank,
                                'CheckBankSnif' => $tempDocPaymentInfo->CheckBankSnif,
                                'CheckBankCode' => $tempDocPaymentInfo->CheckBankCode,
                                'CheckNumber' => $tempDocPaymentInfo->CheckNumber,
                                'CheckDate' => $creditDate,
                                'BankNumber' => $tempDocPaymentInfo->BankNumber,
                                'BankDate' => $tempDocPaymentInfo->BankDate,
                                'Dates' => $dates,
                                'UserId' => $userId,
                                'Excess' => $tempDocPaymentInfo->Excess,
                                'UserDate' => $userDate,
                                'DocDate' => $DocDate,
                                'DocMonth' => $DocMonth,
                                'DocYear' => $DocYear,
                                'DocTime' => $DocTime,
                                'CreditType' => $tempDocPaymentInfo->CreditType,
                                'ActivityJson' => $activityJson,
                                'BusinessCompanyId' => $settingsInfo->CompanyId,
                                'BusinessType' => $settingsInfo->BusinessType,
                                'PayToken' => $tempDocPaymentInfo->PayToken,
                                'TransactionId' => $tempDocPaymentInfo->TransactionId,
                                'MeshulamPageCode' => $tempDocPaymentInfo->MeshulamPageCode,
                                ));
                    }
                    break;
                }
            }
        }
        //// עדכון מספר קבלה
        $newTempId = explode(',', $finalInvoiceId);
        foreach ($newTempId as $check) {
            $activityId = $check;
            $activityInfo = DB::table('client_activities')->where('CompanyNum', '=', $companyNum)->where('id', '=', $activityId)->first();

            $trueClientId = $activityInfo->ClientId;
            $itemId = $activityInfo->ItemId;
            $receiptIdJson = '';
            $receiptIdJson .= '{"data": [';

            if ($activityInfo->ReceiptId != '') {
                $Loops = json_decode($activityInfo->ReceiptId, true);
                foreach ($Loops['data'] as $key => $val) {
                    $docIdDB = $val['DocId'];
                    $receiptIdJson .= '{"DocId": "' . $docIdDB . '"},';
                }
            }

            $receiptIdJson .= '{"DocId": "' . $docId . '"}';
            $receiptIdJson .= ']}';
            DB::table('client_activities')
                ->where('id', $activityId)
                ->where('ClientId', '=', $trueClientId)
                ->where('CompanyNum', '=', $companyNum)
                ->update(array('ReceiptId' => $receiptIdJson));
        }
        //////  עדכון טבלת דוח מכירות
        $DocsInfo = DB::table('docs')->where('CompanyNum', '=', $companyNum)->where('id', '=', $docId)->first();
        if (isset($DocsInfo->ActivityJson) && $DocsInfo->ActivityJson != '') {
            $Loops = json_decode($DocsInfo->ActivityJson, true);
            foreach ($Loops['data'] as $key => $val) {
                $itemId = $val['ItemId'];
                $newAmount = $val['NewAmount'];
                $oldBalanceMoney = $val['OldBalanceMoney'];
                if ($newAmount != $oldBalanceMoney) {
                    $fixPrice = $oldBalanceMoney - $newAmount;
                    $activityInfo = DB::table('items')
                        ->where('CompanyNum', '=', $companyNum)
                        ->where('id', '=', $itemId)
                        ->first();

                    if ($fixPrice != '0.00' || $activityInfo->ItemName != '') {
                        DB::table('docs2item')->insertGetId(
                            array('CompanyNum' => $companyNum,
                                'TrueCompanyNum' => $trueCompanyNum,
                                'Brands' => $brands,
                                'ClientId' => $clientId,
                                'ItemId' => $itemId,
                                'DocsId' => $docId,
                                'Amount' => $fixPrice,
                                'Department' => $activityInfo->Department,
                                'MemberShip' => $activityInfo->MemberShip,
                                'ItemName' => htmlentities($activityInfo->ItemName),
                                'UserDate' => $userDate,
                                'BusinessCompanyId' => $settingsInfo->CompanyId,
                                'BusinessType' => $settingsInfo->BusinessType)
                        );
                    }
                }
            }

        }

        //// עדכון חוב לקוח
        $balanceAmount = DB::table('client_activities')->where('ClientId', '=', $clientId)->where('CompanyNum', $companyNum)->where('CancelStatus', '=', '0')->where('isDisplayed', 1)->sum('BalanceMoney');
        $checkClientInfoer = DB::table('client')->where('CompanyNum', $companyNum)->where('PayClientId', $clientId)->get();

        foreach ($checkClientInfoer as $checkClientInfo) {
            $balanceAmount += DB::table('client_activities')->where('ClientId', '=', $checkClientInfo->id)->where('CompanyNum', $companyNum)->where('CancelStatus', '=', '0')->where('isDisplayed', 1)->sum('BalanceMoney');
        }

        DB::table('client')
            ->where('id', '=', $clientId)
            ->where('CompanyNum', '=', $companyNum)
            ->update(array('BalanceAmount' => $balanceAmount));


        ///// שליחת מסמך ללקוח
        $Date = date('Y-m-d');
        $Time = date('H:i:s');
        $dates = date('Y-m-d H:i:s');
        $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $companyNum)->where('Type', '=', '23')->first();
        $ClientInfo = DB::table('client')->where('id', '=', $clientId)->where('CompanyNum', '=', $companyNum)->first();
//        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/liza_logs.txt', '[' . __FILE__ . '] [line: ' . __LINE__ . '] [' . date("Y-m-d H:i:s") . '] $ClientInfo: ' . print_r($ClientInfo->Email, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
        $companyInfo = DB::table('settings')->where('CompanyNum', '=', $companyNum)->first();

        /// עדכון תבנית הודעה
        $DocUrl = $randomNumber;
        $FullLinks = 'https://new.boostapp.co.il/office/PDF/DocsClient.php?RandomUrl=' . $DocUrl . '&ClientId=' . $clientId;
        $trueFullLinks = self::getTinyUrl($FullLinks);
        $docUrlTrue = '<a href="' . $trueFullLinks . '">' . lang('view_doc_ajax') . '</a>';
        $DocsTypeInfo = DB::table('docstable')->where('CompanyNum', '=', $companyNum)->where('id', '=', $typeDoc)->first();
        $typeDocName = $DocsTypeInfo->TypeTitleSingle;

        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $companyInfo->AppName, $Template->Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName, $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName, $Content2);
        $Content4 = str_replace(Notificationcontent::REPLACE_ARR["doc_number"], $typeNumber, $Content3);
        $Content5 = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $typeDocName, $Content4);
        $Content6 = str_replace(Notificationcontent::REPLACE_ARR["doc_link"], $docUrlTrue, $Content5);
        $ContentTrue = $Content6;

        $subject1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $companyInfo->AppName, $Template->Subject);
        $subject2 = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $typeDocName, $subject1);
        $subjectTrue = $subject2;

        $textNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
        $subject = $subjectTrue;

        if ($ClientInfo->GetEmail == 0) {
            $AddNotification = DB::table('appnotification')->insertGetId(
                array(
                    'CompanyNum' => $companyNum,
                    'ClientId' => $clientId,
                    'TrueClientId' => '0',
                    'Subject' => $subject,
                    'Text' => $textNotification,
                    'Dates' => $dates,
                    'UserId' => '0',
                    'Type' => '2',
                    'Date' => $Date,
                    'Time' => $Time,
                    'ClassId' => '0'
                )
            );
        }

        if (!$isCard) {
            foreach ($tempPaymentsInfo as $tempsPayment) {
                $tempsPayment->delete();
            }
        }
    }

    /**
     * @param OrderLogin $order
     */
    public static function saveReceiptAfterPayWithCard(OrderLogin $order): void
    {
        $tempPaymentInfo = $order->tempReceipt();
        $companyNum = $order->CompanyNum;
        $clientId = $order->ClientId;
        $clientGroupNumber = $tempPaymentInfo->TypeDoc;
        $clientActivityId = $tempPaymentInfo->ClientActivityId;
        self::saveReceipts($clientId, $clientGroupNumber, $companyNum, $clientActivityId, true, true);
    }

    /**
     * @param $totalAmount
     * @param $numOfPayments
     * @return array
     */
    private static function divideToPayments($totalAmount, $numOfPayments): array
    {
        $Amount = $totalAmount / $numOfPayments;
        $roundedAmount = ceil(round($Amount, 2));
        $restOfPayments = $roundedAmount * ($numOfPayments - 1);
        $firstPayment = $totalAmount - $restOfPayments;
        $firstPayment = number_format((float)$firstPayment, 2, '.', '');
        $secondPayment = number_format($roundedAmount, 2, '.', '');
        return ["firstPayment" => $firstPayment, "secondPayment" => $secondPayment];
    }

    /**
     * @param Docs $Doc
     * @param array $clientActivitiesIds
     * @param Docs $DocInvoice
     * @param array $paymentDataArray
     * @return Docs
     */
    public static function createReceiptByActivitiesIds(Docs $Doc, array $clientActivitiesIds, ?Docs $DocInvoice , array $paymentDataArray = []): Docs
    {
        // todo: override cpaType for cart only!!
        $useCpaType = false;

        try {
            // create ActivityJson
            $totalAmountPayment = $paymentDataArray['paymentTotalAmount']; //כום תשלומים
            //קבלה רגילה
            if ($DocInvoice !== null) {
                $Doc->ActivityJson = DocsService::getActivityJson($DocInvoice, $totalAmountPayment);
                $Doc->Amount = abs($totalAmountPayment) * -1;
                $Doc->BalanceAmount = abs($totalAmountPayment);
            } else { //חשבונית מס קבלה
                $Doc->PayStatus = Docs::PAY_STATUS_CLOSE;
                $DocInvoice = $Doc;
            }
            $Doc->save();

            if ($Doc->id === 0) {
                throw new LogicException('create doc error');
            }
            $docsLinkToInvoiceId = DocsLinkToInvoice::createDocsLinkToInvoice($DocInvoice->id, $Doc->id);
            if ($docsLinkToInvoiceId === 0) {
                throw new LogicException('create DocsLinkToInvoice error');
            }

            $docs2itemIdArray = [];
            foreach ($clientActivitiesIds as $clientActivityId) {
                /** @var ClientActivities $ClientActivity */
                $ClientActivity = ClientActivities::find($clientActivityId);
                if ($ClientActivity === null) {
                    throw new LogicException('clientActivity id not valid');
                }
                //// עדכון מספר קבלה
                if (!$ClientActivity->addReceiptIdToJson($Doc->id)) {
                    LoggerService::error('error in update ReceiptId, ClientActivityId = ' . $ClientActivity->id, LoggerService::CATEGORY_DOCS);
                }
                if (docs2item::existsForDocsId($DocInvoice->id, $DocInvoice->CompanyNum)) {
                    continue;
                }
                $Docs2item = new docs2item();
                $Docs2item->setPropertiesByDocs($DocInvoice);
                $Docs2item->setPropertiesByClientActivity($ClientActivity);
                //////  עדכון טבלת דוח מכירות
                if (!$Docs2item->save()) {
                    LoggerService::error('error in create Docs2item, docId = ' . $Doc->id . ' ClientActivityId = ' . $ClientActivity->id, LoggerService::CATEGORY_DOCS);
                }
                empty($Docs2item->id) ? null : $docs2itemIdArray[] = $Docs2item->id;

            }
            //for each payment
            $docsPaymentIdArray = [];
            foreach ($paymentDataArray['paymentData'] ?? [] as $paymentData) {
                $DocsPayment = new DocsPayment();
                $DocsPayment->setPropertiesByDocs($Doc);
                $DocsPayment->InvoiceId = $DocInvoice->id;
                $DocsPayment->setPropertiesByData($paymentData);
                if ($useCpaType === true && (int)$DocsPayment->TypePayment === DocPaymentTypeEnum::CREDIT_CARD && $DocsPayment->Payments > 1) {
                    if ( !$DocsPayment->createOnMultiplePayment($docsPaymentIdArray)) {
                        throw new LogicException('error in add DocsPayment on createOnMultiplePayment doc id' . $Doc->id ?? "");
                    }
                } else {
                    $DocsPayment->saveAfterValidation();
                    if (!empty($DocsPayment->id)) {
                        $docsPaymentIdArray[] = $DocsPayment->id;
                    }
                }
            }
        } catch (Exception | \Throwable $e) {
            LoggerService::error($e);
            if (isset($docsLinkToInvoiceId) && $docsLinkToInvoiceId !== 0) {
                DocsLinkToInvoice::removeById($docsLinkToInvoiceId);
            }
            if (!empty($docs2itemIdArray)) {
                foreach ($docs2itemIdArray as $docs2itemId) {
                    $Docs2item = docs2item::find($docs2itemId);
                    $Docs2item->delete();
                }
            }
            if (!empty($docsPaymentIdArray)) {
                foreach ($docsPaymentIdArray as $docsPaymentId) {
                    DocsPayment::where('id', $docsPaymentId)->delete();
                }
            }
            if (!empty($Doc->id)) {
                $Doc->removeDocsAndDocList();
            }
        }
        return $Doc;
    }

    /**
     * @param Docs|null $Receipt
     * @param Docs|null $DocInvoice
     * @param string $remarksText
     * @return IdResponse
     */
    public static function cancelReceipts(Docs $Receipt = null, Docs $DocInvoice = null, string $remarksText = ''): IdResponse
    {
        $Response = new IdResponse();
        try {
            if($Receipt === null || !$Receipt->isReceiptDocs()){
                throw new LogicException('Receipt not valid');
            }
            if($DocInvoice === null){
                throw new LogicException('$DocInvoice not valid');
            }
            //todo add auth??
            $companyNum = $Receipt->CompanyNum;
//            $userId = Auth::user()->id;
            /** @var Settings $SettingsInfo */
            $SettingsInfo = Settings::getSettings($companyNum);
            if ($SettingsInfo === null) {
                throw new LogicException('Settings id =' . $companyNum . ' not valid');
            }
            $paymentSystem = PaymentService::getPaymentSystemByType($Receipt->TypeShva);
            if((int)$Receipt->TypeShva !== $paymentSystem->getTypeShva()) {
                throw new LogicException('paymentSystem not valid');
            }
            $Client = new Client($Receipt->ClientId);
            if($Client === null) {
                throw new LogicException('client not valid');
            }

            $refundAmount = 0;
            $DocsPaymentArray = DocsPayment::getPaymentsByDocsId($Receipt->id, $companyNum);
            foreach ($DocsPaymentArray as $DocsPayment) {
                if (!$DocsPayment->isCreditCardPayment()) {
                    $refundAmount += abs($DocsPayment->getSum());
                    $DocsPayment->updateRefAction();
                } else {
                   if (self::refundDocsPayment($Response, $DocsPayment, $paymentSystem, $Client, $SettingsInfo) === null) {
                       $Response->setMessage('לא בוצע זיכוי מלא - יש להשלים ידינית את הפעולה');//todo-alex
                       break; //not success refund
                   }
                    $refundAmount += abs($DocsPayment->getSum(false));//change if not full
                }
            }
            if((int)$Receipt->TypeHeader !== DocsTable::TYPE_HESHBONIT_MAS_KABLA) {
                if ($refundAmount >= abs($Receipt->BalanceAmount)) {
                    $Receipt->BalanceAmount = 0;
                    $Receipt->PayStatus = Docs::PAY_STATUS_CANCELED;
                } else {
                    $Receipt->BalanceAmount -= $refundAmount;
                }
                $Receipt->save();
            }

            $typeHeader = DocsTable::TYPE_KABALA; // todo change to new docsTable
            $docDetailsArray = [
                'PaymentRole' => $Receipt->PaymentRole,
                'Amount' => abs($refundAmount),
                'BalanceAmount' => $Receipt->Amount + abs($refundAmount),
                'PayStatus' => Docs::PAY_STATUS_CLOSE,
                'Refound'=> Docs::REFUND_STATUS_ON,
            ];
            !empty($remarksText) ? $docDetailsArray['Remarks'] = $remarksText : null;
            $Doc = DocsService::createBaseDoc($typeHeader, $Client, $SettingsInfo, $docDetailsArray);
            $Doc->ActivityJson = DocsService::getActivityJson($DocInvoice, abs($refundAmount), $Doc->Refound); //todo-not sure id upade refoud

            $Doc->save();
            if ($Doc->id === 0) {
                throw new LogicException('create doc error');
            }
            $docsLinkToInvoiceId = DocsLinkToInvoice::createDocsLinkToInvoice($DocInvoice->id, $Doc->id);
            if ($docsLinkToInvoiceId === 0) {
                throw new LogicException('create DocsLinkToInvoice error', 406);
            }

            //for each payment
            foreach ($DocsPaymentArray as $DocsPayment) {
                $docsPaymentArray = $DocsPayment->toArray();
                unset($docsPaymentArray['id']);
                if((int)$DocsPayment->RefAction === DocsPayment::AFTER_REF_ACTION) {
                    $NewDocsPayment = new DocsPayment($docsPaymentArray);
                    $NewDocsPayment->setPropertiesByDocs($Doc);
                    if((int)$Receipt->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA || $NewDocsPayment->CheckDate <= date('Y-m-d')) {
                        $NewDocsPayment->StatusInvoice = 1;
                    } else {
                        $NewDocsPayment->StatusInvoice = 0;
                    }
                    $NewDocsPayment->Amount *= -1;
                    $NewDocsPayment->ActivityJson = $Doc->ActivityJson;
                    $NewDocsPayment->Refound = Docs::REFUND_STATUS_ON;
                    try {
                        $NewDocsPayment->saveAfterValidation();
                    } catch (Exception | \Throwable $e) {
                        $Response->setMessage($Response->getMessage() . ' לא נוצר תקבול זיכוי בצורה מלא');//todo-alex
                        LoggerService::error('docsPayment -' . $DocsPayment->id . $e->getMessage(), LoggerService::CATEGORY_PAYMENT_PROCESS_CANCEL);
                    }
                }
            }

        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_PAYMENT_PROCESS_REFUND);
            if(!empty($Doc) && $Doc->id !== 0) {
                $Response->setError('הופקה קבלת זיכוי אך אחת הפעולות נכשלה!');
                $Response->setId($Doc->id ?? 0);
            } else {
                $Response->setError('הזיכוי לא בוצע במלאו ולא נוצר מסמך כלל');
            }
            return $Response;
        }
        $Response->setId(($Doc->id ?? 0));
        return $Response;
    }

    /**
     * @param Docs $Doc
     * @param Docs $DocInvoice
     * @param array $paymentDataArray
     * @return Docs|null
     */
    public static function createRefundReceipt(Docs $Doc, Docs $DocInvoice , array $paymentDataArray = []): ?Docs
    {
        // todo: override cpaType for cart only!!
        $useCpaType = false;
        try {
            //like docs
            $docsLinkToInvoiceId = DocsLinkToInvoice::createDocsLinkToInvoice($DocInvoice->id, $Doc->id);
            if ($docsLinkToInvoiceId === 0) {
                throw new LogicException('create DocsLinkToInvoice error');
            }
            //$docs2itemIdArray not sure if need to add
            $docsPaymentIdArray = [];
            $totalAmountRefund = 0;
            foreach ($paymentDataArray as $paymentData) {
                $DocsPayment = new DocsPayment();
                $DocsPayment->setPropertiesByDocs($Doc);
                $DocsPayment->setPropertiesByData($paymentData);
                if ($useCpaType === true && (int)$DocsPayment->TypePayment === DocPaymentTypeEnum::CREDIT_CARD && $DocsPayment->Payments > 1) {
                    if (!$DocsPayment->createOnMultiplePayment($docsPaymentIdArray)) {
                        throw new LogicException('error in add DocsPayment on createOnMultiplePayment doc id' . $Doc->id ?? "");
                    }
                } else {
                    $DocsPayment->StatusInvoice = 1;
                }
                $DocsPayment->Amount = -1 * abs($DocsPayment->Amount);
                $totalAmountRefund += $DocsPayment->Amount;
                $DocsPayment->Refound = Docs::REFUND_STATUS_ON;
                $DocsPayment->saveAfterValidation();
                if (!empty($DocsPayment->id)) {
                    $docsPaymentIdArray[] = $DocsPayment->id;
                }
            }
            $Doc->ActivityJson = DocsService::getActivityJson($DocInvoice, $totalAmountRefund, Docs::REFUND_STATUS_ON);
            $Doc->save();

        } catch (Exception | \Throwable $e) {
            LoggerService::error($e);
            if (isset($docsLinkToInvoiceId) && $docsLinkToInvoiceId !== 0) {
                DocsLinkToInvoice::removeById($docsLinkToInvoiceId);
            }
//            if (!empty($docs2itemIdArray)) {
//                foreach ($docs2itemIdArray as $docs2itemId) {
//                    $Docs2item = docs2item::find($docs2itemId);
//                    $Docs2item->delete();
//                }
//            }
            if (!empty($docsPaymentIdArray)) {
                foreach ($docsPaymentIdArray as $docsPaymentId) {
                    DocsPayment::where('id', $docsPaymentId)->delete();
                }
            }
            if (!empty($Doc->id)) {
                $Doc->removeDocsAndDocList();
            }
            return null;
        }
        return $Doc;
    }

    /**
     * @param $Response
     * @param DocsPayment $DocsPayment
     * @param PaymentSystem $PaymentSystem
     * @param Client $Client
     * @param $SettingsInfo
     * @param float|null $amount -- if null take from $DocsPayment->Amount (full refund)
     * @return OrderLogin|null
     */
    public static function refundDocsPayment($Response , DocsPayment $DocsPayment, PaymentSystem $PaymentSystem, Client $Client, $SettingsInfo, ?float $amount = null): ?OrderLogin
    {
        $OrderLogin = null;
        //check valid docs_paymnet
        if (!$DocsPayment->isValidCreditCardPayment($PaymentSystem->getTypeShva())) {
            $ResponseRefund = new BaseResponse();
            $ResponseRefund->setError('docsPayment -'. $DocsPayment->id . ' not valid');
        } else {
            $OrderLogin = $PaymentSystem->createOrderRefund($Client, $DocsPayment, true);
            if($amount !== null) {
                $OrderLogin->Amount = $amount;
            }
            switch ($PaymentSystem->getTypeShva()) {
                case PaymentSystem::TYPE_YAAD:
                    $ResponseRefund = $PaymentSystem->refundPayment($OrderLogin);
                    break;
                case PaymentSystem::TYPE_MESHULAM:
                    $ResponseRefund = $PaymentSystem->refundPayment($DocsPayment, $SettingsInfo, $OrderLogin);
                    break;
                case PaymentSystem::TYPE_TRANZILA:
                    $ResponseRefund = $PaymentSystem->refundPayment($DocsPayment, $OrderLogin);
                    break;
                default:
                    $ResponseRefund = new BaseResponse();
                    $ResponseRefund->setError('לא נמצא מערכת תשלומים לזיכוי');
            }
        }
        if(!$ResponseRefund->isSuccess()){
            $Response->setMessage($ResponseRefund->getMessage());//todo-alex
            LoggerService::error('docsPayment -'. $DocsPayment->id . $ResponseRefund->message , LoggerService::CATEGORY_PAYMENT_PROCESS_CANCEL);
            return null;
        }
        $OrderLogin->Status = OrderLogin::STATUS_REFUND;
        $OrderLogin->save();
        $DocsPayment->updateRefAction(false);
        return $OrderLogin;
    }



}