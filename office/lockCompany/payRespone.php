<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] ."/app/init.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/YaadUtils.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/Utils.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/YaadNumbers.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/Settings.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/Functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftToken.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftPayToken.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftPayment.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftDocsTable.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftDocs.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftCity.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftStreet.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftItem.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftDocsList.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/247SoftNew/SoftDocsPayment.php";


try {
    $yaad = new YaadUtils();
    $order = YaadNumbers::get($_REQUEST["Order"]);
    $func = new Functions();
    $company = Settings::getSettings($order->CompanyNum);
    $company->YaadNumber = YaadUtils::SOFT_YAAD_NUMBER;
    $softClient = SoftClient::getRow($company->CompanyNum,"FixCompanyNum");
    $token = $yaad->getToken($_REQUEST["Id"],$company->YaadNumber);
    $YaadCode = $_REQUEST["Id"] ?? 0;
    if(!$YaadCode) {
        $arr = array(
            "message" => "transaction id missing",
            "file_path" => "payRespone.php",
        );
        if(isset($company)) {
            $util = new Utils();
            $arr["data"] = json_encode(["company" => $util->createArrayFromObj($company), "POST" => $_REQUEST], JSON_PRETTY_PRINT);
        }
        DB::table("boostapp.update_payment_log")->insertGetId($arr);
        return json_encode(["message" => "transaction id missing", "status" => 0], JSON_UNESCAPED_UNICODE);
    }
    if(empty($token)) {
        $arr = array(
            "message" => "cannot get token from transaction",
            "file_path" => "payRespone.php",
        );
        if(isset($company)) {
            $util = new Utils();
            $arr["data"] = json_encode(["company" => $util->createArrayFromObj($company), "POST" => $_REQUEST], JSON_PRETTY_PRINT);
        }
        DB::table("boostapp.update_payment_log")->insertGetId($arr);
        return json_encode(["message" => "cannot get token from transaction", "status" => 0], JSON_UNESCAPED_UNICODE);
    }
    $tokenInfo = SoftToken::getRow($softClient->id,"ClientId");


    $tokenArr = array(
        "CompanyNum" => 100,
        "ClientId" => $softClient->id,
        "Token" => $token["Token"],
        "Tokef" => $token["Tokef"],
        "YaadCode" => $company->YaadNumber,
        "Status" => 0,
        "L4digit" => $_REQUEST["L4digit"],
    );
    if($tokenInfo) {
        SoftToken::update($tokenInfo->id,$tokenArr);
        $tokenId = $tokenInfo->id;
    } else {
        $tokenId = SoftToken::insert($tokenArr);
    }

    $paytoken = SoftPayToken::getPayTokenByClientId($softClient->id);
    $payment = SoftPayment::getDeclinedPayment($softClient->id);
    $addTime = '+'.$paytoken->NumDate.' '. Functions::getValidType($paytoken->TypePayment);
    $now = date('Y-m-d');

    $nextPayment = date('Y-m-d', strtotime($addTime,strtotime($now)));
    $paytokenArr = array(
        "LastPayment" => $now,
        "NextPayment" => $nextPayment,
        "TokenId" => $tokenId
    );

    SoftPayToken::update($paytoken->id,$paytokenArr);

    $paymentArr = array(
        "Status" => 1,
        "Error" => "עסקה מאושרת",
        "NumTry" => 1,
        "Date" => $now,
        "L4digit" => $_REQUEST["L4digit"],
        "CCode" => $_REQUEST["CCode"],
        "ACode" => $_REQUEST["ACode"],
        "Bank" => $_REQUEST["Bank"],
        "Brand" => $_REQUEST["Brand"],
        "Payments" => $_REQUEST["Payments"],
        "Issuer" => $_REQUEST["Issuer"],
        "BrandName" => $func->getSlikaBrandName($_REQUEST["Issuer"],$_REQUEST["Brand"],0),
    );
    SoftPayment::update($payment->id, $paymentArr);

    /// מסמך חשבונית מס קבלה בלבד
    $TypeHeader = 320;
    $TypeDoc = 5;

    $docTable = SoftDocsTable::getRow($TypeHeader,'TypeHeader');
    $lastDoc = SoftDocs::getLastDoc();
    if($softClient->City != "0"){
        $city = SoftCity::getRow($softClient->City,"CityId");
    }
    if($softClient->Street != "0"){
        $street = SoftStreet::getRow($softClient->Street);
    }
    $item = SoftItem::getRow($paytoken->ItemId);
    $ActivityJson = '';
    $ActivityJson .= '{"data": [';
    $ActivityJson .= '{"ItemText": "'.$item->ItemName.'", "ItemId": "'.$paytoken->ItemId.'", "OldBalanceMoney": "'.$paytoken->Amount.'", "NewAmount": "0"}';
    $ActivityJson .= ']}';

    $amount = $_REQUEST['Amount'];
    $vatNumber = $softClient->VatNumber;
    $vatAmount = $vatNumber != 0 ? $amount - ($amount / (1 + round($vatNumber / 100, 2))) : 0;

    $docArr = array('CompanyNum' => 100, 'TrueCompanyNum' => 100,
        'TypeDoc' => $docTable->id, 'TypeHeader' => $TypeHeader, 'TypeNumber' => $lastDoc->TypeNumber + 1,
        'ClientId' => $softClient->id, 'UserDate' => date('Y-m-d'), 'Dates' => date('Y-m-d H:i:s'), 'Amount' => "-" . $amount,
        'Vat' => $vatNumber, 'VatAmount' => $vatAmount, 'DiscountType' => '1', 'Discount' => '0', 'DiscountAmount' => '0',
        'PaymentRole' => '1', 'Company' => $softClient->CompanyName, 'CompanyId' => $softClient->CompanyId,
        'ContactName' => $softClient->CompanyName, 'Mobile' => $softClient->ContactMobile,
        'Phone' => $softClient->ContactPhone, 'Fax' => $softClient->ContactFax, 'Email' => $softClient->Email,
        'UserId' => '0', 'ManualInvoice' => 0, 'DocConvert' => 0,
        'PaymentTime' => date('Y-m-d'), 'BalanceAmount' => '0', 'Street' => isset($street) ? $street->Street : $softClient->StreetH,
        'Number' => $softClient->Number, 'PostCode' => $softClient->PostCode, 'City' => isset($city) ? $city->City : '', 'Accounts' => $docTable->Accounts,
        'DocDate' => date('Y-m-d'), 'DocMonth' => date("m", strtotime(date('Y-m-d'))), 'DocYear' => date("Y", strtotime(date('Y-m-d'))), 'DocTime' => date('H:i:s'),
        'RandomUrl' => $func->createRandomNumber(), 'ActivityJson' => $ActivityJson, 'Status' => '1', 'AutoPayment' => '1', 'AutoPaymentId' => $paytoken->id);
    $docId = SoftDocs::insert($docArr);

    $noVatAmount = $amount - $vatAmount;

    $itemText = 'שירות חודשי מערכת בוסטאפ';
    $docListArr = array('CompanyNum' => 100, 'TrueCompanyNum' => 100,
        'TypeDoc' => $docTable->id, 'TypeHeader' => $TypeHeader,
        'TypeNumber' => $lastDoc->TypeNumber + 1, 'DocsId' => $docId, 'ItemId' => $paytoken->ItemId,
        'SKU' => '0', 'ItemName' => $itemText, 'ItemText' => $itemText,
        'ItemPrice' => $noVatAmount, 'ItemPriceVat' => $noVatAmount,
        'ItemPriceVatDiscount' => $noVatAmount,
        'ItemQuantity' => '1', 'ItemDiscountType' => '0', 'ItemDiscount' => '0', 'ItemDiscountAmount' => '0',
        'Itemtotal' => $noVatAmount, 'ItemTable' => 'items', 'Dates' => date('Y-m-d H:i:s'), 'UserDate' =>  date('Y-m-d'),
        'TypeDocBasis' => '0', 'TypeDocBasisNumber' => '0', 'Vat' => $softClient->VatNumber, 'VatAmount' => $vatAmount, 'DocDate' =>  date('Y-m-d'),
        'DocMonth' => date("m", strtotime(date('Y-m-d'))), 'DocYear' => date("Y", strtotime(date('Y-m-d'))), 'DocTime' => date('H:i:s'));
    $docListId = SoftDocsList::insert($docListArr);
    if ($paytoken->tashType=='0'){
        $tashTypeDB = '1';
    }

    else if ($paytoken->tashType=='1') {
        $tashTypeDB = '2';

    }
    else if ($paytoken->tashType=='2') {
        $tashTypeDB = '4';
    }
    else if ($paytoken->tashType=='6') {
        $tashTypeDB = '3';
    }
    else {
        $tashTypeDB = '5';
    }
    $creditType = "חיוב ועדכון כרטיס אשראי";
    $docPaymentArr = array('CompanyNum' => 100,
        'TrueCompanyNum' => 100, 'TypeDoc' => $TypeDoc,
        'TypeHeader' => $TypeHeader, 'TypeNumber' => $lastDoc->TypeNumber, 'DocsId' => $docId,
        'ClientId' => $softClient->id, 'TypePayment' => '3', 'Amount' => $_REQUEST["Amount"],
        'L4digit' => $_REQUEST["L4digit"], 'YaadCode' => $YaadCode, 'CCode' => $_REQUEST["CCode"], 'ACode' =>  $_REQUEST["ACode"],
        'Bank' => $_REQUEST["Bank"], 'Payments' => $_REQUEST["Payments"], 'Brand' => $_REQUEST["Brand"], 'BrandName' => $func->getSlikaBrandName($_REQUEST["Issuer"],$_REQUEST["Brand"],0),
        'Issuer' => $_REQUEST["Issuer"], 'tashType' => $tashTypeDB,'CheckDate' => date("Y-m", strtotime("+1 month", strtotime(date('Y-m-d')))).'-02', 'Dates' => date('Y-m-d H:i:s'),
        'UserId' => '0', 'UserDate' =>  date('Y-m-d'), 'DocDate' =>  date('Y-m-d'), 'DocMonth' => date("m", strtotime(date('Y-m-d'))),
        'DocYear' => date("Y", strtotime(date('Y-m-d'))), 'DocTime' => date('H:i:s'), 'CreditType' => $creditType, 'ActivityJson' => $ActivityJson );
    SoftDocsPayment::insert($docPaymentArr);
    Settings::updateRow($company->CompanyNum, array("lockDate" => null,"lockStatus" => 0));

    DB::table('247softnew.log_yaad')->insertGetId([
        'UserId' => 0,
        'Text' => json_encode(["POST" => $_REQUEST], JSON_UNESCAPED_UNICODE),
        'ClientId' => $softClient->id,
        'CompanyNum' => 100,
        'Status' => 0
    ]);

    require_once $_SERVER['DOCUMENT_ROOT']."/office/lockCompany/ProcessPayment.php";
    return;
}
catch (\Throwable $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($company)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($company),JSON_UNESCAPED_UNICODE);
    }

    DB::table("boostapp.update_payment_log")->insertGetId($arr);
}
