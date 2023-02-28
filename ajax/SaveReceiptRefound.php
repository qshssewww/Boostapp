<?php
if (Auth::guest()) exit;

$validator = Validator::make(
    array('ClientId' => $_POST['ClientId'], 'GroupNumber' => $_POST['GroupNumber']),
    array('ClientId' => 'Required', 'GroupNumber' => 'Required')
);

if (!$validator->passes()) {
    json_message($validator->errors()->toArray(), false);
    exit;
}


$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$CpaType = $SettingsInfo->CpaType;

$ClientId = $_POST['ClientId'];
$UserDate = date('Y-m-d');
$TypeHeader = '400'; /// קבלה

$FinalinvoiceId = $_POST['FinalinvoiceId']; // מנויים לתשלום
$TrueFinalinvoicenum = $_POST['TrueFinalinvoicenum']; /// סה"כ כסף
$ClientGroupNumber = $_POST['GroupNumber'];

$ManualInvoice = '0';
$DocConvert = '0';
$PaymentRole = '1';
$Dates = date('Y-m-d H:i:s');

if (empty($FinalinvoiceId)) {
    json_message(lang('error_generate_document_subscription_ajax'), false);
    exit;
}


/// בדיקת תקבול קיים
$CheckPayments = DB::table('temp_receipt_payment_client')->where('TempId', '=', $ClientId)->where('TypeDoc', '=', $ClientGroupNumber)->where('CompanyNum', '=', $CompanyNum)->count();

if (@$CheckPayments == '0') {
    json_message(lang('error_doc_without_refund_ajax'), false);
    exit;
}


//// בדיקת סניפים
if (@$SettingsInfo->BrandsMain != '0' && @$SettingsInfo->MainAccounting == '1') {
    $TrueCompanyNum = $SettingsInfo->BrandsMain;
} else {
    $TrueCompanyNum = $CompanyNum;
}

/// סוג מסמך וקבלת ID
$GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
$TypeDoc = $GetDocsId->id;

/// בדיקת מספור מסמך + תאריך אחרון
$DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
if ($DocsTableNew->Status == '1') {

    json_message(lang('error_doc_type_ajax'), false);
    exit;
}

$GetAmount = DB::table('temp_receipt_payment_client')->where('TempId', '=', $ClientId)->where('TypeDoc', '=', $ClientGroupNumber)->where('CompanyNum', '=', $CompanyNum)->sum('Amount');


/// עדכון מנוי בתשלום מלא / תשלום חלקי

$json = new StdClass();
$json->data = array();


// $ActivityJson = '';
// $ActivityJson .= '{"data": [';

$NewAmount = $GetAmount;
$NewAmountFinal = $GetAmount;
$NewTempId = explode(',', $FinalinvoiceId);
$Newcount = count($NewTempId);
$Newi = '1';
foreach ($NewTempId as $check) {

    $ActivityId = $check;
    $ActivityInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityId)->first();

    $ItemText = htmlentities($ActivityInfo->ItemText);
    $BalanceMoney = $ActivityInfo->BalanceRefoundMoney;
    $ItemId = $ActivityInfo->ItemId;
    $TrueClientId = $ActivityInfo->ClientId;

    $BalanceRefoundMoney = (($ActivityInfo->ItemPriceVatDiscount + $ActivityInfo->VatAmount) - $ActivityInfo->BalanceMoney) - ($ActivityInfo->TrueBalanceRefoundMoney);

    $FixBalanceRefoundMoney = ($ActivityInfo->TrueBalanceRefoundMoney) - ($BalanceMoney);


    /// בדיקת מצב תקבול לעומת תשלום פעילות
    if ($NewAmount >= $BalanceRefoundMoney) {

        /// סוגר פעילות ותשלום מלא
        $NewAmount = $NewAmount - $BalanceRefoundMoney; ///50
        $NewAmounts = str_replace('-', "", $NewAmount - $NewAmountFinal); // 40
        $TrueBalanceRefoundMoney = $ActivityInfo->TrueBalanceRefoundMoney + $NewAmounts;
        $NewAmountFinal = $GetAmount - $NewAmounts;

        DB::table('client_activities')
            ->where('id', $ActivityId)
            ->where('ClientId', '=', $TrueClientId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array('BalanceRefoundMoney' => $NewAmounts, 'TrueBalanceRefoundMoney' => $TrueBalanceRefoundMoney));

        // if ($Newcount == $Newi) {
        $temp = new stdClass();
        $temp->ItemText = $ItemText;
        $temp->ItemId = $ItemId;
        $temp->OldBalanceMoney = $BalanceMoney;
        $temp->NewAmount = "0"; // @TODO check in it is int.
        $temp->FixNewAmount = $NewAmounts;
        $json->data[] = $temp;
        // $ActivityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "0", "FixNewAmount": "' . $NewAmounts . '"}';
        // } else {
        // $ActivityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "0", "FixNewAmount": "' . $NewAmounts . '"},';
        // }
    } else {
        $NewAmount = $NewAmount;

        $TrueBalanceRefoundMoney = $ActivityInfo->TrueBalanceRefoundMoney + $NewAmount;

        DB::table('client_activities')
            ->where('id', $ActivityId)
            ->where('ClientId', '=', $TrueClientId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array('BalanceRefoundMoney' => $NewAmount, 'TrueBalanceRefoundMoney' => $TrueBalanceRefoundMoney));

        $FixNewAmount = $NewAmount;
        $NewAmount = $BalanceRefoundMoney - $NewAmount;

        $temp = new stdClass();
        $temp->ItemText = $ItemText;
        $temp->ItemId = $ItemId;
        $temp->OldBalanceMoney = $BalanceMoney;
        $temp->NewAmount = $NewAmount; // @TODO check in it is int.
        $temp->FixNewAmount = $FixNewAmount;
        $json->data[] = $temp;

        // if ($Newcount == $Newi) {
        //     $ActivityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "' . $NewAmount . '", "FixNewAmount": "' . $FixNewAmount . '"}';
        // } else {
        //     $ActivityJson .= '{"ItemText": "' . $ItemText . '", "ItemId": "' . $ItemId . '", "OldBalanceMoney": "' . $BalanceMoney . '", "NewAmount": "' . $NewAmount . '", "FixNewAmount": "' . $FixNewAmount . '"},';
        // }


        $NewAmount = '0';
    }




    ++$Newi;
}

// $ActivityJson .= ']}';
$ActivityJson = json_encode($json);



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

$Remarks = @$DocsTableNew->DocsRemarks;

/// פרטי לקוח
$ClientDocInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->first();
$Brands = $ClientDocInfo->Brands;
if ($ClientDocInfo->Company == '') {
    $Company = htmlentities($ClientDocInfo->CompanyName);
} else {
    $Company = htmlentities($ClientDocInfo->Company);
}


$PaymentRole = '1';
$PaymentTime = $UserDate;

$DocDate = date('Y-m-d');
$DocMonth = date("m", strtotime($UserDate));
$DocYear = date("Y", strtotime($UserDate));
$DocTime = date('H:i:s');

/// מחולל מספר מסמך
$GroupNumber = rand(1262055681, 1262055681);
$GroupNumber = uniqid() . '' . strtotime(date('YmdHis')) . '' . $GroupNumber . '' . rand(1, 9999999);
$RandomNumber = uniqid($GroupNumber);
$RandomNumber;

$DocsGetAmount = $GetAmount;
$City = '';

if ($ClientDocInfo->City != '0') {
    $BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $ClientDocInfo->City)->first();
    $City = $BusinessSettingsCity->City;
}
if ($ClientDocInfo->Street == '0' || $ClientDocInfo->Street == '99999999') {
    $Street = $ClientDocInfo->StreetH;
} else {
    $BusinessSettingsStreet = DB::table('street')->where('id', '=', $ClientDocInfo->Street)->first();
    $Street = @$BusinessSettingsStreet->Street;
}

$DocId = DB::table('docs')->insertGetId(
    array('CompanyNum' => $CompanyNum,
        'Brands' => $Brands,
        'TrueCompanyNum' => $TrueCompanyNum,
        'TypeDoc' => $TypeDoc,
        'TypeHeader' => $DocsTableNew->TypeHeader,
        'TypeNumber' => $TypeNumber,
        'ClientId' => $ClientId,
        'UserDate' => $UserDate,
        'Dates' => $Dates,
        'Amount' => $DocsGetAmount,
        'Vat' => '0',
        'VatAmount' => '0',
        'DiscountType' => '1',
        'Discount' => '0',
        'DiscountAmount' => '0',
        'PaymentRole' => '1',
        'Remarks' => $Remarks,
        'Company' => $Company,
        'CompanyId' => $ClientDocInfo->CompanyId,
        'ContactName' => $ClientDocInfo->CompanyName,
        'Mobile' => $ClientDocInfo->ContactMobile,
        'Phone' => $ClientDocInfo->ContactPhone,
        'Fax' => $ClientDocInfo->ContactFax,
        'Email' => $ClientDocInfo->Email,
        'UserId' => $UserId,
        'ManualInvoice' => $ManualInvoice,
        'DocConvert' => $DocConvert,
        'PaymentTime' => $PaymentTime,
        'BalanceAmount' => '0',
        'Street' => $Street,
        'Number' => $ClientDocInfo->Number,
        'PostCode' => $ClientDocInfo->PostCode,
        'City' => $City,
        'Accounts' => $DocsTableNew->Accounts,
        'DocDate' => $DocDate,
        'DocMonth' => $DocMonth,
        'DocYear' => $DocYear,
        'DocTime' => $DocTime,
        'RandomUrl' => $RandomNumber,
        'ActivityJson' => $ActivityJson,
        'Status' => '1',
        'Refound' => '1',
        'BusinessCompanyId' => $SettingsInfo->CompanyId,
        'BusinessType' => $SettingsInfo->BusinessType,
        'TypeShva' => $SettingsInfo->TypeShva,
        'CpaType' => $SettingsInfo->CpaType
    )
);

//
//            $DataLog = "'CompanyNum' => $CompanyNum, 'Brands' => $Brands, 'TrueCompanyNum' => $TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $DocsTableNew->TypeHeader, 'TypeNumber' => $TypeNumber, 'ClientId' => $ClientId, 'UserDate' => $UserDate, 'Dates' => $Dates, 'Amount' => $DocsGetAmount, 'Vat' => '0', 'VatAmount' => '0', 'DiscountType' => '1', 'Discount' => '0', 'DiscountAmount' => '0', 'PaymentRole' => '1', 'Company' => $Company, 'CompanyId' => $ClientDocInfo->CompanyId, 'ContactName' => $ClientDocInfo->CompanyName, 'Mobile' => $ClientDocInfo->ContactMobile, 'Phone' => $ClientDocInfo->ContactPhone, 'Fax' => $ClientDocInfo->ContactFax, 'Email' => $ClientDocInfo->Email, 'UserId' => $UserId, 'ManualInvoice' => $ManualInvoice, 'DocConvert' => $DocConvert, 'PaymentTime' => $PaymentTime, 'BalanceAmount' => '0', 'Street' => $Street, 'Number' => $ClientDocInfo->Number, 'PostCode' => $ClientDocInfo->PostCode, 'City' => $City, 'Accounts' => $DocsTableNew->Accounts, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'RandomUrl' => $RandomNumber, 'ActivityJson' => $ActivityJson, 'Status' => '1', 'Refound' => '1'";
//
//            DB::table('logfix')->insertGetId(
//            array('DataLog' => $DataLog) );
//


/// שמירת נתוני מסמך תקבולים
$TempDocPaymentsInfo = DB::table('temp_receipt_payment_client')->where('TempId', '=', $ClientId)->where('TypeDoc', '=', $ClientGroupNumber)->get();
foreach ($TempDocPaymentsInfo as $TempDocPaymentInfo) {

    $RefoundAmount = '-' . $TempDocPaymentInfo->Amount;

    if ($TempDocPaymentInfo->TypePayment != '3') {
        $DocsList = DB::table('docs_payment')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'Brands' => $Brands, 'TrueCompanyNum' => $TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $TypeHeader, 'TypeNumber' => $TypeNumber, 'DocsId' => $DocId, 'ClientId' => $ClientId, 'TypePayment' => $TempDocPaymentInfo->TypePayment, 'Amount' => $RefoundAmount, 'L4digit' => $TempDocPaymentInfo->L4digit, 'YaadCode' => $TempDocPaymentInfo->YaadCode, 'CCode' => $TempDocPaymentInfo->CCode, 'ACode' => $TempDocPaymentInfo->ACode, 'Bank' => $TempDocPaymentInfo->Bank, 'Payments' => $TempDocPaymentInfo->Payments, 'Brand' => $TempDocPaymentInfo->Brand, 'BrandName' => $TempDocPaymentInfo->BrandName, 'Issuer' => $TempDocPaymentInfo->Issuer, 'tashType' => $TempDocPaymentInfo->tashType, 'CheckBank' => $TempDocPaymentInfo->CheckBank, 'CheckBankSnif' => $TempDocPaymentInfo->CheckBankSnif, 'CheckBankCode' => $TempDocPaymentInfo->CheckBankCode, 'CheckNumber' => $TempDocPaymentInfo->CheckNumber, 'CheckDate' => $TempDocPaymentInfo->CheckDate, 'BankNumber' => $TempDocPaymentInfo->BankNumber, 'BankDate' => $TempDocPaymentInfo->BankDate, 'Dates' => $Dates, 'UserId' => $UserId, 'Excess' => $TempDocPaymentInfo->Excess, 'UserDate' => $UserDate, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'CreditType' => $TempDocPaymentInfo->CreditType, 'ActivityJson' => $ActivityJson, 'Refound' => '1', 'BusinessCompanyId' => $SettingsInfo->CompanyId, 'BusinessType' => $SettingsInfo->BusinessType, 'PayToken' => $TempDocPaymentInfo->PayToken, 'TransactionId' => $TempDocPaymentInfo->TransactionId)
        );
    }


    /// פירוט תקבולי אשראי - כל תשלום בשורה נפרדת
    else {

        /// תשלום אחד
        if ($TempDocPaymentInfo->Payments == '1' || $CpaType == '1') {
            $UserDate = $TempDocPaymentInfo->UserDate;
            // $i = '1';
            // $timenew = strtotime(date("Y-m-02", strtotime($UserDate)));
            // $AddDate = '+' . $i . ' month';
            // $final = date("Y-m", strtotime($AddDate, $timenew)) . '-02';
            // $CreditDate = $final;

            // if ($CpaType == '1') {
            //     $CreditDate = date('Y-m-d');
            // }
            $CreditDate = date('Y-m-d');

            $DocsList = DB::table('docs_payment')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'Brands' => $Brands, 'TrueCompanyNum' => $TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $TypeHeader, 'TypeNumber' => $TypeNumber, 'DocsId' => $DocId, 'ClientId' => $ClientId, 'TypePayment' => $TempDocPaymentInfo->TypePayment, 'Amount' => $RefoundAmount, 'L4digit' => $TempDocPaymentInfo->L4digit, 'YaadCode' => $TempDocPaymentInfo->YaadCode, 'CCode' => $TempDocPaymentInfo->CCode, 'ACode' => $TempDocPaymentInfo->ACode, 'Bank' => $TempDocPaymentInfo->Bank, 'Payments' => $TempDocPaymentInfo->Payments, 'Brand' => $TempDocPaymentInfo->Brand, 'BrandName' => $TempDocPaymentInfo->BrandName, 'Issuer' => $TempDocPaymentInfo->Issuer, 'tashType' => $TempDocPaymentInfo->tashType, 'CheckBank' => $TempDocPaymentInfo->CheckBank, 'CheckBankSnif' => $TempDocPaymentInfo->CheckBankSnif, 'CheckBankCode' => $TempDocPaymentInfo->CheckBankCode, 'CheckNumber' => $TempDocPaymentInfo->CheckNumber, 'CheckDate' => $CreditDate, 'BankNumber' => $TempDocPaymentInfo->BankNumber, 'BankDate' => $TempDocPaymentInfo->BankDate, 'Dates' => $Dates, 'UserId' => $UserId, 'Excess' => $TempDocPaymentInfo->Excess, 'UserDate' => $UserDate, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'CreditType' => $TempDocPaymentInfo->CreditType, 'ActivityJson' => $ActivityJson, 'Refound' => '1', 'BusinessCompanyId' => $SettingsInfo->CompanyId, 'BusinessType' => $SettingsInfo->BusinessType, 'PayToken' => $TempDocPaymentInfo->PayToken, 'TransactionId' => $TempDocPaymentInfo->TransactionId)
            );
        } else {
            $UserDate = $TempDocPaymentInfo->UserDate;
            $Payments = $TempDocPaymentInfo->Payments;
            $Amount = $TempDocPaymentInfo->Amount;

            $Money = $Amount;
            $Payment = $Payments;

            $MyMoney = $Money / $Payment;
            $MyMoney = number_format((float) $MyMoney, 2, '.', '');

            list($whole, $decimal) = explode('.', $MyMoney);

            $CehckPayment = $whole * ($Payment - 1);
            $FirstPayment = $Money - $CehckPayment;
            $FirstPayment = number_format((float) $FirstPayment, 2, '.', '');
            $SecendPayment = $whole;
            $SecendPayment = number_format((float) $SecendPayment, 2, '.', '');

            $count = $Payments;
            for ($i = 1; $i <= $count; $i++) {

                if ($i == 1) {
                    $FixAmount = '-' . $FirstPayment;
                } else {
                    $FixAmount = '-' . $SecendPayment;
                }

                $PaymentsNew = $i;
                // $timenew = strtotime(date("Y-m-02", strtotime($UserDate)));
                // $AddDate = '+' . $i . ' month';
                // $final = date("Y-m", strtotime($AddDate, $timenew)) . '-02';
                // $CreditDate = $final;
                
                $add = $i - 1;
                $AddDate = '+'.$add.' month';
                $CreditDate = date('Y-m-d', strtotime($AddDate, strtotime($UserDate)));

                $DocsList = DB::table('docs_payment')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'Brands' => $Brands, 'TrueCompanyNum' => $TrueCompanyNum, 'TypeDoc' => $TypeDoc, 'TypeHeader' => $TypeHeader, 'TypeNumber' => $TypeNumber, 'DocsId' => $DocId, 'ClientId' => $ClientId, 'TypePayment' => $TempDocPaymentInfo->TypePayment, 'Amount' => $FixAmount, 'L4digit' => $TempDocPaymentInfo->L4digit, 'YaadCode' => $TempDocPaymentInfo->YaadCode, 'CCode' => $TempDocPaymentInfo->CCode, 'ACode' => $TempDocPaymentInfo->ACode, 'Bank' => $TempDocPaymentInfo->Bank, 'Payments' => $PaymentsNew, 'Brand' => $TempDocPaymentInfo->Brand, 'BrandName' => $TempDocPaymentInfo->BrandName, 'Issuer' => $TempDocPaymentInfo->Issuer, 'tashType' => $TempDocPaymentInfo->tashType, 'CheckBank' => $TempDocPaymentInfo->CheckBank, 'CheckBankSnif' => $TempDocPaymentInfo->CheckBankSnif, 'CheckBankCode' => $TempDocPaymentInfo->CheckBankCode, 'CheckNumber' => $TempDocPaymentInfo->CheckNumber, 'CheckDate' => $CreditDate, 'BankNumber' => $TempDocPaymentInfo->BankNumber, 'BankDate' => $TempDocPaymentInfo->BankDate, 'Dates' => $Dates, 'UserId' => $UserId, 'Excess' => $TempDocPaymentInfo->Excess, 'UserDate' => $UserDate, 'DocDate' => $DocDate, 'DocMonth' => $DocMonth, 'DocYear' => $DocYear, 'DocTime' => $DocTime, 'CreditType' => $TempDocPaymentInfo->CreditType, 'ActivityJson' => $ActivityJson, 'Refound' => '1', 'BusinessCompanyId' => $SettingsInfo->CompanyId, 'BusinessType' => $SettingsInfo->BusinessType, 'PayToken' => $TempDocPaymentInfo->PayToken, 'TransactionId' => $TempDocPaymentInfo->TransactionId)
                );
            }
        }
    }
}


/// מחיקת נתונים

DB::table('temp_receipt_payment_client')->where('TempId', '=', $ClientId)->where('TypeDoc', '=', $ClientGroupNumber)->delete();



//// עדכון מספר קבלה


$NewTempId = explode(',', $FinalinvoiceId);
foreach ($NewTempId as $check) {

    $ActivityId = $check;
    $ActivityInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityId)->first();
    $ItemId = $ActivityInfo->ItemId;

    $TrueClientId = $ActivityInfo->ClientId;
    $ReceiptIdJson = '';
    $ReceiptIdJson .= '{"data": [';

    if ($ActivityInfo->ReceiptId != '') {
        $Loops =  json_decode($ActivityInfo->ReceiptId, true);
        foreach ($Loops['data'] as $key => $val) {

            $DocIdDB = $val['DocId'];

            $ReceiptIdJson .= '{"DocId": "' . $DocIdDB . '"},';
        }
    }

    $ReceiptIdJson .= '{"DocId": "' . $DocId . '"}';

    $ReceiptIdJson .= ']}';

    DB::table('client_activities')
        ->where('id', $ActivityId)
        ->where('ClientId', '=', $TrueClientId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('ReceiptId' => $ReceiptIdJson));
}


//////  עדכון טבלת דוח מכירות

$DocsInfo = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $DocId)->first();

if ($DocsInfo->ActivityJson != '') {
    $Loops =  json_decode($DocsInfo->ActivityJson, true);
    foreach ($Loops['data'] as $key => $val) {

        $ItemId = $val['ItemId'];
        $NewAmount = $val['FixNewAmount'];
        $OldBalanceMoney = $val['OldBalanceMoney'];

        if ($NewAmount != '0') {

            $FixPrice = $NewAmount;
            $FixPrice = '-' . $FixPrice;

            $ActivityInfo = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ItemId)->first();

            if ($NewAmount != '0.00' || @$ActivityInfo->ItemName != '') {
                DB::table('docs2item')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'TrueCompanyNum' => $TrueCompanyNum, 'Brands' => $Brands, 'ClientId' => $ClientId,  'ItemId' => $ItemId, 'DocsId' => $DocId, 'Amount' => $FixPrice, 'Department' => $ActivityInfo->Department, 'MemberShip' => $ActivityInfo->MemberShip, 'ItemName' => htmlentities($ActivityInfo->ItemName), 'UserDate' => $UserDate, 'BusinessCompanyId' => $SettingsInfo->CompanyId, 'BusinessType' => $SettingsInfo->BusinessType)
                );
            }
        }
    }
}



json_message();