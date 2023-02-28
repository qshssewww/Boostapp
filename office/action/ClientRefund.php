<?php

require_once '../../app/init.php';
require_once '../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/DocsPayments.php';
require_once __DIR__ . '/../Classes/Token.php';
require_once __DIR__ . '/../Classes/TempReceiptPaymentClient.php';
require_once __DIR__ . '/../services/LoggerService.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../services/payment/PaymentTypeEnum.php';

if (Auth::guest()) {
    exit;
}

$StatusPay = '';
$DocsId = $_POST['DocsId'];
$amount = isset($_POST['refund_amount']) ? (float)$_POST['refund_amount'] : 0.00;
$clientActivityId = $_POST['clientActivityId'] ?? null;
$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;
$resArr = ['error' => false, 'msg' => ''];
/// בדיקת תשלום אשראי

$CompanyNum = Auth::user()->CompanyNum;

$studioSettings = Settings::getSettings($CompanyNum);

$success = false;

$DocsInfo = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $DocsId)->first();
if (empty($DocsInfo)) {
    $resArr['error'] = true;
    $resArr['msg'] = 'התגלתה שגיאה, אנא פנה לתמיכה';
    echo json_encode($resArr, true);
    exit;
}
$DocsType = $DocsInfo->TypeHeader;
$docAmount = abs((float)$DocsInfo->Amount);

if ($amount > $docAmount) {
    $resArr['error'] = true;
    $resArr['msg'] = 'סכום הזיכוי גדול מסכום העסקה';
    echo json_encode($resArr, true);
    exit;
}

if ($DocsInfo->Refound == 2 && $amount > 0 && $amount > $docAmount - $DocsInfo->refundAmount) {
    $resArr['error'] = true;
    $resArr['msg'] = 'סכום הזיכוי גדול מסכום היתרה לזיכוי';
    echo json_encode($resArr, true);
    exit;
}

$fullRefund = $amount == $docAmount;



$paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

if ($fullRefund) {
    // full refund

    $TempPaymentInfos = DocsPayment::getPaymentsByDocsId($DocsId, $CompanyNum);

    foreach ($TempPaymentInfos as $TempPaymentInfo) {
        if ($TempPaymentInfo->TypePayment != 3 || ($TempPaymentInfo->TypePayment == 3 && $TempPaymentInfo->YaadCode == 0)) {
            $resArr['msg'] = 'בוטל בהצלחה';
            DB::table('docs_payment')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('id', '=', $TempPaymentInfo->id)
                ->update(array('RefAction' => 1));
        } elseif ($TempPaymentInfo->TypePayment == 3 && $TempPaymentInfo->RefAction == 0 && $TempPaymentInfo->TransactionId != '0' && ($DocsInfo->TypeShva == PaymentTypeEnum::TYPE_MESHULAM || $DocsInfo->TypeShva == PaymentTypeEnum::TYPE_TRANZILA)) {
            try {
                $paymentResult = PaymentService::refundByDocPayment($TempPaymentInfo, $TempPaymentInfo->getSum(true), (int)$studioSettings->TypeShva);
                $TempPaymentInfo->updateRefAction(true);

                $resArr['msg'] = 'בוטל בהצלחה';
                $success = true;
            } catch (LogicException $e) {
                // if payment is failed
                $resArr['msg'] = $e->getMessage();
                $resArr['error'] = true;

                DB::table('transaction_error')->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClientId' => $TempPaymentInfo->ClientId,
                    'UpdateTransactionDetails' => $e->getMessage(),
                    'UserId' => $UserId,
                ]);
            } catch (Throwable $e) {
                // if we've got any other errors
                LoggerService::error($e);

                $resArr['msg'] = 'התגלתה שגיאה, אנא פנה לתמיכה.';
                $resArr['error'] = true;
            }
        }
    }
}
else {
    // partial refund
    $docsPayment = DocsPayment::where('RefAction', '=', 0)
        ->where('DocsId', '=', $DocsId)
        ->where('TypePayment', '=', 3)
        ->where('CompanyNum', '=', $CompanyNum)
        ->first();
    if (!empty($docsPayment)) {
        if ($docsPayment->TypePayment != 3 || ($docsPayment->TypePayment == 3 && $docsPayment->YaadCode == 0)) {
            $resArr['msg'] = 'לא נמצא מספר עסקה לביטול במסוף';
            $resArr['error'] = true;
            $update = DB::table('docs_payment')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('id', '=', $docsPayment->id)
                ->update(array('RefAction' => 1));
        } elseif ($docsPayment->TypePayment == 3 && $docsPayment->TransactionId != 0 && ($DocsInfo->TypeShva == PaymentTypeEnum::TYPE_MESHULAM || $DocsInfo->TypeShva == PaymentTypeEnum::TYPE_TRANZILA)) {
            try {
                $paymentResult = PaymentService::refundByDocPayment($docsPayment, $amount, (int)$studioSettings->TypeShva);
                $docsPayment->updateRefAction(true);
                $resArr['msg'] = 'בוצע זיכוי חלקי בסך ' . $amount;
                $success = true;
            } catch (LogicException $e) {
                // if payment is failed

                DB::table('transaction_error')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'ClientId' => $docsPayment->ClientId, 'UpdateTransactionDetails' => $e->getMessage(), 'UserId' => $UserId));

                $resArr['msg'] = $e->getMessage();
                $resArr['error'] = true;
            } catch (Throwable $e) {
                // if we've got any other errors
                LoggerService::error($e);

                $resArr['msg'] = 'התגלתה שגיאה, אנא פנה לתמיכה.';
                $resArr['error'] = true;
            }
        }
    }
}

/// בדיקת זיכוי מלא
//$TempPaymentCount = DB::table('docs_payment')->where('RefAction', '=', 0)->where('DocsId', '=', $DocsId)->where('CompanyNum', '=', $CompanyNum)->count();

if ($success) {
    /** @var ClientActivities $ClientActivity */
    $ClientActivity = (new ClientActivities($clientActivityId));

    $NewAmount = $amount;
    $NewAmountFinal = $amount;
    $BalanceMoney = $ClientActivity->BalanceRefoundMoney;

    $BalanceRefoundMoney = (($ClientActivity->ItemPriceVatDiscount + $ClientActivity->VatAmount) - $ClientActivity->BalanceMoney) - ($ClientActivity->TrueBalanceRefoundMoney);
    $FixBalanceRefoundMoney = ($ClientActivity->TrueBalanceRefoundMoney) - ($BalanceMoney);

    $NewAmount = $NewAmount - $BalanceRefoundMoney; ///50
    $NewAmounts = str_replace('-', "", $NewAmount - $NewAmountFinal); // 40
    $TrueBalanceRefoundMoney = $ClientActivity->TrueBalanceRefoundMoney + $NewAmounts;

    DB::table('client_activities')
        ->where('id', $clientActivityId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('BalanceRefoundMoney' => $NewAmounts, 'TrueBalanceRefoundMoney' => $TrueBalanceRefoundMoney));


    // $StatusNew = '1';
    $Dates = date('Y-m-d H:i:s');

    //// בדיקת סניפים
    if ($studioSettings->BrandsMain != '0' && $studioSettings->MainAccounting == '1') {
        $TrueCompanyNum = $studioSettings->BrandsMain;
    } else {
        $TrueCompanyNum = $CompanyNum;
    }

    $UserDate = date('Y-m-d');
    $DocDate = date('Y-m-d');
    $DocMonth = date("m", strtotime($UserDate));
    $DocYear = date("Y", strtotime($UserDate));
    $DocTime = date('H:i:s');

    if ($DocsType == '320') {
        ///// יצירת חשבונית זיכוי
        $TypeHeader = '330';
        /// סוג מסמך וקבלת ID
        $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
        $TypeDoc = $GetDocsId->id;
        /// בדיקת מספור מסמך + תאריך אחרון
        $DocsCountGets = DB::table('docs')->where('TrueCompanyNum', '=', $TrueCompanyNum)->where('TypeHeader', '=', $TypeHeader)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
        if (@$DocsCountGets->TypeNumber == '') {
            $TypeNumber = $GetDocsId->TypeNumber;
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
        $Refound = $fullRefund ? 1 : 2;
        $BalanceAmount = '0.00';

        $DocId = DB::table('docs')->insertGetId(
            array('CompanyNum' => $DocsInfo->CompanyNum,
                'Brands' => $DocsInfo->Brands,
                'TrueCompanyNum' => $DocsInfo->TrueCompanyNum,
                'TypeDoc' => $TypeDoc,
                'TypeHeader' => $DocsTableNew->TypeHeader,
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
                'Accounts' => $DocsTableNew->Accounts,
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
                'refundAmount' => !$fullRefund ? $amount : 0.00,
                'CpaType' => $studioSettings->CpaType
            ));

        /// שמירת נתוני מסמך פריטים
        $TempDocListsInfo = DB::table('docslist')->where('DocsId', '=', $DocsId)->where('CompanyNum', '=', $CompanyNum)->get();
        foreach ($TempDocListsInfo as $TempDocListInfo) {

            $DocsList = DB::table('docslist')->insertGetId(
                array('CompanyNum' => $TempDocListInfo->CompanyNum, 'Brands' => $TempDocListInfo->Brands, 'TrueCompanyNum' => $TempDocListInfo->TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $DocsTableNew->TypeHeader, 'TypeNumber' => $TypeNumber, 'DocsId' => $DocId, 'ClientId' => $TempDocListInfo->ClientId, 'ItemId' => $TempDocListInfo->ItemId, 'SKU' => $TempDocListInfo->SKU, 'ItemName' => $TempDocListInfo->ItemName, 'ItemText' => $TempDocListInfo->ItemText, 'ItemPrice' => $Minus . $TempDocListInfo->ItemPrice, 'ItemPriceVat' => $Minus . $TempDocListInfo->ItemPriceVat, 'ItemPriceVatDiscount' => $Minus . $TempDocListInfo->ItemPriceVatDiscount, 'ItemQuantity' => $Minus . $TempDocListInfo->ItemQuantity, 'ItemDiscountType' => $TempDocListInfo->ItemDiscountType, 'ItemDiscount' => $TempDocListInfo->ItemDiscount, 'ItemDiscountAmount' => $TempDocListInfo->ItemDiscountAmount, 'Itemtotal' => $Minus . $TempDocListInfo->Itemtotal, 'ItemTable' => $TempDocListInfo->ItemTable, 'Dates' => $Dates, 'UserDate' => $UserDate, 'TypeDocBasis' => $TempDocListInfo->TypeDocBasis, 'TypeDocBasisNumber' => $TempDocListInfo->TypeDocBasisNumber, 'Vat' => $TempDocListInfo->Vat, 'VatAmount' => $Minus . $TempDocListInfo->VatAmount, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'BusinessCompanyId' => $TempDocListInfo->BusinessCompanyId, 'BusinessType' => $TempDocListInfo->BusinessType));
        }
    }

    /////  יצירת קבלת החזר
    $TypeHeader = '400';
    /// סוג מסמך וקבלת ID
    $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
    $TypeDoc = $GetDocsId->id;

    /// בדיקת מספור מסמך + תאריך אחרון
    $DocsCountGets = DB::table('docs')->where('TrueCompanyNum', '=', $TrueCompanyNum)->where('TypeHeader', '=', $TypeHeader)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
    if (@$DocsCountGets->TypeNumber == '') {
        $TypeNumber = $DocsTableNew->TypeNumber;
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
    $Refound = $fullRefund ? 1 : 2;
    $BalanceAmount = '0.00';

    $docsAmount = $fullRefund ? $DocsInfo->Amount : $amount;

    if ($DocsType == '400') {
        $newAmount = str_replace("-", "", $docsAmount);
    } else {
        $newAmount = $Minus . $docsAmount;
    }

    $DocId = DB::table('docs')->insertGetId(
        array('CompanyNum' => $DocsInfo->CompanyNum,
            'Brands' => $DocsInfo->Brands,
            'TrueCompanyNum' => $DocsInfo->TrueCompanyNum,
            'TypeDoc' => $TypeDoc,
            'TypeHeader' => $DocsTableNew->TypeHeader,
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
            'Accounts' => $DocsTableNew->Accounts,
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
            'refundAmount' => !$fullRefund ? $amount : 0.00,
            'CpaType' => $studioSettings->CpaType
        ));

    $tempAmount = $amount;

    $GetAmounts = DocsPayment::where('DocsId', '=', $DocsId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('TypePayment', '=', 3)
        ->get();

    foreach ($GetAmounts as $TempDocPaymentInfo) {
        if (!$fullRefund && $tempAmount <= 0) {
            break;
        }
        $refundAmount = $tempAmount > $TempDocPaymentInfo->Amount ? $TempDocPaymentInfo->Amount : $tempAmount;
        $tempAmount -= $refundAmount;

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
            'Amount' => - (abs($refundAmount)),
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
            'ActivityJson' => $TempDocPaymentInfo->ActivityJson,
            'CreditType' => $TempDocPaymentInfo->CreditType,
            'BusinessCompanyId' => $studioSettings->CompanyId,
            'BusinessType' => $studioSettings->BusinessType,
            'Refound' => $Refound
        ));
    }
    
    ///// עדכון כל השדות של המסמך המקורי
    $updateDocs = DB::table('docs')
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('id', '=', $DocsId)
        ->update(array('Refound' => $Refound, 'RefAction' => 1, 'refundAmount' => !$fullRefund ? $amount : 0.00));
} else {
    $resArr['error'] = true;
    $resArr['msg'] = empty($resArr['msg']) ? 'התגלתה שגיאה בעת ביצוע הפעולה.' : $resArr['msg'];
}

echo json_encode($resArr, true);
// json_message(array('Status' => $StatusPay, 'StatusNew' => $StatusNew));