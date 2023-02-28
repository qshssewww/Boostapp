<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
//$_SERVER['DOCUMENT_ROOT'] = '..';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/DocsLinkToInvoice.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Docs.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// מחולל חשבוניות אוטומטי ///////////////////////////////////////////////////////
try {
    $GetInvoices = DB::table('docs_payment')
        ->where('StatusInvoice', '=', '0')
        ->where('CheckDate', '<=', $ThisDate)
        ->whereNotNull('ActivityJson')
        ->groupBy('DocsId')
        ->get();

    foreach ($GetInvoices as $GetInvoice) {

        $CompanyNum = $GetInvoice->CompanyNum;


        $InvoiceInfo = DB::table('docs')->where('id', '=', $GetInvoice->DocsId)->where('CompanyNum', '=', $CompanyNum)->first();

        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $GetInvoice->CompanyNum)->first();
        if ($SettingsInfo && $InvoiceInfo) {


            if ($GetInvoice->Refound == '0') {
                $TypeHeader = '305'; ///// חשבונית מס
                $Minus = '';
            } else {
                $TypeHeader = '330'; ///// חשבונית מס זיכוי
                $Minus = '-';
            }

            $UserDate = date('Y-m-d');
            $Dates = date('Y-m-d H:i:s');
            $DocDate = date('Y-m-d');
            $DocMonth = date("m", strtotime($UserDate));
            $DocYear = date("Y", strtotime($UserDate));
            $DocTime = date('H:i:s');

            $CompanyNum = $GetInvoice->CompanyNum;
            $Brands = $GetInvoice->Brands;


            $Vat = $SettingsInfo->Vat;


            $SumAmounts = DB::table('docs_payment')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('DocsId', '=', $GetInvoice->DocsId)
                ->where('StatusInvoice', '=', '0')
                ->where('CheckDate', '<=', $ThisDate)
                ->whereNotNull('ActivityJson')
                ->sum('Amount');

            $SumAmounts = abs((float)$SumAmounts);

            //// חישוב מע"מ

            if ($SettingsInfo->CompanyVat == 0) {

                $Vats = 1 + $Vat/100;
                $TotalVatDovPrice = $SumAmounts / $Vats;
                $TotalVatDovPrice = round($SumAmounts - $TotalVatDovPrice, 2);

                $VatAmount = $TotalVatDovPrice;

            } else {
                $Vat = '0';
                $VatAmount = '0.00';
            }


            //// בדיקת סניפים
            if ($SettingsInfo->BrandsMain != 0 && $SettingsInfo->MainAccounting == 1) {
                $TrueCompanyNum = $SettingsInfo->BrandsMain;
            } else {
                $TrueCompanyNum = $CompanyNum;
            }

            /// סוג מסמך וקבלת ID 
            $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
            $TypeDoc = $GetDocsId->id ?? 0;

            /// בדיקת מספור מסמך + תאריך אחרון
            $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
            if (isset($DocsTableNew) && $DocsTableNew->Status != '1') { //// לא ניתן להנפיק סוג מסמך זה

                $DocsCountGets = DB::table('docs')
                    ->where('TrueCompanyNum', '=', $TrueCompanyNum)
                    ->where('TypeHeader', '=', $TypeHeader)
                    ->orderBy('TypeNumber', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!$DocsCountGets) {
                    $TypeNumber = $DocsTableNew->TypeNumber;
                } else {
                    $TypeNumber = $DocsCountGets->TypeNumber + 1;
                }

                /// סוג מסמך וקבלת ID
                $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('CompanyNum', '=', $CompanyNum)->first();
                $TypeDoc = $GetDocsId->id;
                $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->first();

                $remarks = empty($InvoiceInfo->Remarks) ? '' : $InvoiceInfo->Remarks;
                $remarks .= empty($GetDocsId->DocsRemarks) ? '' : '<br>' .  $GetDocsId->DocsRemarks;

                /// מחולל מספר מסמך
                $GroupNumber = rand(1262055681, 1262055681);
                $GroupNumber = uniqid() . '' . strtotime(date('YmdHis')) . '' . $GroupNumber . '' . rand(1, 9999999);
                $RandomNumber = uniqid($GroupNumber);

                $DocId = DB::table('docs')->insertGetId(array(
                        'CompanyNum' => $CompanyNum,
                        'Brands' => $Brands,
                        'TrueCompanyNum' => $TrueCompanyNum,
                        'TypeDoc' => $TypeDoc,
                        'TypeHeader' => $DocsTableNew->TypeHeader,
                        'TypeNumber' => $TypeNumber,
                        'ClientId' => $InvoiceInfo->ClientId,
                        'UserDate' => $UserDate,
                        'Dates' => $Dates,
                        'Amount' => $Minus . $SumAmounts,
                        'Vat' => $Vat,
                        'VatAmount' => $Minus . $VatAmount,
                        'DiscountType' => $InvoiceInfo->DiscountType,
                        'Discount' => $InvoiceInfo->Discount,
                        'DiscountAmount' => $InvoiceInfo->DiscountAmount,
                        'PaymentRole' => $InvoiceInfo->PaymentRole,
                        'Remarks' => $remarks,
                        'Company' => $InvoiceInfo->Company,
                        'CompanyId' => $InvoiceInfo->CompanyId,
                        'ContactName' => $InvoiceInfo->ContactName,
                        'Mobile' => $InvoiceInfo->Mobile,
                        'Phone' => $InvoiceInfo->Phone,
                        'Fax' => $InvoiceInfo->Fax,
                        'Email' => $InvoiceInfo->Email,
                        'UserId' => $InvoiceInfo->UserId,
                        'ManualInvoice' => $InvoiceInfo->ManualInvoice,
                        'DocConvert' => $InvoiceInfo->DocConvert,
                        'PaymentTime' => $InvoiceInfo->PaymentTime,
                        'PayStatus' => Docs::PAY_STATUS_CLOSE,
                        'BalanceAmount' => '0',
                        'Street' => $InvoiceInfo->Street,
                        'Number' => $InvoiceInfo->Number,
                        'PostCode' => $InvoiceInfo->PostCode,
                        'City' => $InvoiceInfo->City,
                        'Accounts' => $DocsTableNew->Accounts,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'RandomUrl' => $RandomNumber,
                        'Status' => Docs::STATUS_SOURCE,
                        'BusinessCompanyId' => $SettingsInfo->CompanyId,
                        'BusinessType' => $SettingsInfo->BusinessType,
                        'CpaType' => $SettingsInfo->CpaType
                ));
                $docsLinkToInvoiceId = DocsLinkToInvoice::createDocsLinkToInvoice($DocId, $GetInvoice->DocsId);
                //update client activity invoice id
                ClientActivities::where('ClientId', $InvoiceInfo->ClientId)
                    ->where('InvoiceId', '=', $GetInvoice->DocsId)->update(['InvoiceId' => $DocId]);

                if ($docsLinkToInvoiceId === 0) {
                    throw new LogicException('create DocsLinkToInvoice error');
                }
                ////// פירוט הפריטים

                $Loops = json_decode($GetInvoice->ActivityJson, true);
                foreach ($Loops['data'] as $key => $val) {

                    $ItemId = $val['ItemId'];
                    $ItemText = htmlentities($val['ItemText']);
                    $ItemPrice = $val['OldBalanceMoney'];
                    $itemVat = 1 + ($Vat / 100);
                    $priceWithoutTax = round(($ItemPrice / $itemVat), 2);

                    $DocsList = DB::table('docslist')->insertGetId(array(
                        'CompanyNum' => $CompanyNum,
                        'Brands' => $Brands,
                        'TrueCompanyNum' => $TrueCompanyNum,
                        'TypeDoc' => $TypeDoc,
                        'TypeHeader' => $DocsTableNew->TypeHeader,
                        'TypeNumber' => $TypeNumber,
                        'DocsId' => $DocId,
                        'ClientId' => $InvoiceInfo->ClientId,
                        'ItemId' => $ItemId ?? 0,
                        'SKU' => '0',
                        'ItemName' => $ItemText ?? '',
                        'ItemText' => $ItemText ?? '',
                        'ItemPrice' => $Minus . $ItemPrice,
                        'ItemPriceVat' => $Minus . $priceWithoutTax,
                        'ItemPriceVatDiscount' => $Minus . $priceWithoutTax,
                        'ItemQuantity' => $Minus . '1',
                        'ItemDiscountType' => '0',
                        'ItemDiscount' => '0',
                        'ItemDiscountAmount' => '0',
                        'Itemtotal' => $Minus . $ItemPrice,
                        'ItemTable' => 'items',
                        'Dates' => $Dates,
                        'UserDate' => $UserDate,
                        'TypeDocBasis' => '0',
                        'TypeDocBasisNumber' => '0',
                        'Vat' => $Vat,
                        'VatAmount' => $Minus . $VatAmount,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'BusinessCompanyId' => $SettingsInfo->CompanyId,
                        'BusinessType' => $SettingsInfo->BusinessType
                    ));
                }

                /// עדכון סטטוס חדש
                DB::table('docs_payment')
                    ->where('DocsId', $GetInvoice->DocsId)
                    ->where('CheckDate', '<=', $ThisDate)
                    ->where('StatusInvoice', '0')
                    ->where('CompanyNum', $GetInvoice->CompanyNum)
                    ->update(array('StatusInvoice' => '1', 'InvoiceId' => $DocId));


            } else {


                /// חברה לא רשאית להנפיק חשבוניות מס
                DB::table('docs_payment')
                    ->where('DocsId', $GetInvoice->DocsId)
                    ->where('CheckDate', '<=', $ThisDate)
                    ->where('StatusInvoice', '0')
                    ->where('CompanyNum', $GetInvoice->CompanyNum)
                    ->update(array('StatusInvoice' => '1'));


            }

        } else {

            DB::table('docs_payment')
                ->where('DocsId', $GetInvoice->DocsId)
                ->where('CheckDate', '<=', $ThisDate)
                ->where('StatusInvoice', '0')
                ->where('CompanyNum', $GetInvoice->CompanyNum)
                ->update(array('StatusInvoice' => '1'));


        }


    }

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetInvoice)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetInvoice),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


