<?php

require_once "Utils.php";
require_once "Settings.php";
require_once "PaymentPage.php";
require_once "Item.php";
require_once __DIR__ . '/Client.php';
//require_once "Docs.php";
//require_once "PayToken.php";
//require_once "DocsPayments.php";
//require_once "docs2item.php";



class Functions extends Utils
{


//    public function docsAfterPayment($companyNum,$client,$payment,$item){
//
//        $SettingsInfo = Settings::getSettings($companyNum);
//        $TypeHeader = 400;
//        if ($SettingsInfo->BrandsMain!='0' && $SettingsInfo->MainAccounting=='1'){
//            $TrueCompanyNum = $SettingsInfo->BrandsMain;
//        }
//        else{
//            $TrueCompanyNum = $companyNum;
//        }
//
//        $BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $client->City)->first();
//        $BusinessSettingsStreet = DB::table('street')->where('id', '=', $client->Street)->first();
//        $GetDocs = DB::table('docstable')->where('TypeHeader','=', $TypeHeader)->where('TrueCompanyNum' ,'=', $TrueCompanyNum)->first();
//        $DocsCountGets = DB::table('docs')->where('TrueCompanyNum' ,'=', $TrueCompanyNum)->where('TypeHeader','=', $TypeHeader)->orderBy('id','DESC')->first();
//        if (isset($DocsCountGets) && $DocsCountGets->TypeNumber == ""){
//            $TypeNumber = $GetDocs->TypeNumber;
//        }
//        else{
//            $TypeNumber = $GetDocs->TypeNumber + 1;
//        }
//        $ActivityJson = '';
//        $ActivityJson .= '{"data": [';
//        $ActivityJson .= '{"ItemText": "' . htmlentities($item->ItemName) . '", "ItemId": "' . $item->id . '", "OldBalanceMoney": "' . $payment->payment_sum . '", "NewAmount": "0"}';
//        $ActivityJson .= ']}';
//        $docsData = array(
//            'CompanyNum' => $companyNum,
//            'Brands' => $client->Brands,
//            'TrueCompanyNum' => $TrueCompanyNum,
//            'TypeDoc' => $GetDocs->id,
//            'TypeHeader' => $GetDocs->TypeHeader,
//            'TypeNumber' => $TypeNumber,
//            'ClientId' => $client->id,
//            'UserDate' => date('Y-m-d'),
//            'Dates' => date('Y-m-d H:i:s'),
//            'Amount' => $payment->payment_sum,
//            'Vat' => '0',
//            'VatAmount' => '0',
//            'DiscountType' => '1',
//            'Discount' => '0',
//            'DiscountAmount' => '0',
//            'PaymentRole' => '1',
//            'Company' => htmlentities($client->Company),
//            'CompanyId' => $client->CompanyId,
//            'ContactName' => htmlentities($client->CompanyName),
//            'Mobile' => $client->ContactMobile,
//            'Phone' => $client->ContactPhone,
//            'Fax' => $client->ContactFax,
//            'Email' => $client->Email,
//            'UserId' => '0',
//            'ManualInvoice' => 0,
//            'DocConvert' => 0,
//            'PaymentTime' => date('Y-m-d'),
//            'BalanceAmount' => '0',
//            'Street' => $BusinessSettingsStreet,
//            'Number' => $client->Number,
//            'PostCode' => $client->PostCode,
//            'City' => $BusinessSettingsCity,
//            'Accounts' => $GetDocs->Accounts,
//            'DocDate' => date('Y-m-d'),
//            'DocMonth' => date("m",),
//            'DocYear' => date("Y"),
//            'DocTime' => date('H:i:s'),
//            'RandomUrl' => $this->createRandomNumber(),
//            'ActivityJson' => $ActivityJson,
//            'Status' => '1',
//            'AutoPayment' => '1',
//            'AutoPaymentId' => $payment->payToken,
//            'TypeShva' => $payment->type_shva,
//            'Remarks' => !empty($GetDocs->DocsRemarks) ? $GetDocs->DocsRemarks : ''
//        );
//        $docsData["id"] = Docs::insert($docsData);
//        return $docsData;
//    }
//
//    public function docsPayments($docData,$response,$payment,$transaction){
//        $SettingsInfo = Settings::getSettings($payment->company_num);
//        $dpObj = new DocsPayment();
//        $CreditType = "עסקת תשלום מחזורי";
//        if($payment->total_payments < 0){
//            $CreditType =  'עסקת הוראת קבע בכרטיס אשראי';
//        }
//        $docsList = array(
//            'CompanyNum' => $payment->company_num,
//            'Brands' => $docData["Brands"],
//            'TrueCompanyNum' => $docData["TrueCompanyNum"],
//            'TypeDoc' => $docData["TypeDoc"],
//            'TypeHeader' => $docData["TypeHeader"],
//            'TypeNumber' => $docData["TypeNumber"],
//            'DocsId' =>  $docData["id"],
//            'ClientId' => $docData["ClientId"],
//            'TypePayment' => '3',
//            'Amount' => $docData["Amount"],
//            'L4digit' => ($payment->type_shva == 1) ? $response["data"]["cardSuffix"] : $response["L4digit"],
//            'YaadCode' => ($payment->type_shva == 1) ?  $response["data"]["transactionId"] : $response["Id"],
//            'CCode' => ($payment->type_shva == 1) ? 0 :  $response["CCode"],
//            'ACode' => ($payment->type_shva == 1) ?  $response["data"]["asmachta"] : $response["ACode"],
//            'Bank' => ($payment->type_shva == 1) ? 9 : $response["Bank"],
//            'Payments' => 1,
//            'Brand' => ($payment->type_shva == 1) ? 0 : $response["Brand"],
//            'BrandName' => ($payment->type_shva == 1) ? $this->getCreditCardName( $response["data"]["cardTypeCode"],1, $response["data"]["cardBrand"]) : $this->getCreditCardName($response["Issuer"],1,$response["Brand"]),
//            'Issuer' =>  ($payment->type_shva == 1) ? 0 : $response["Issuer"],
//            'tashType' => $this->getTashTypeFromPayToken($payment->payToken,$payment->type_shva),
//            'CheckDate' => $docData["Dates"],
//            'Dates' => $docData["Dates"],
//            'UserId' => '0',
//            'UserDate' => $docData["UserDate"],
//            'DocDate' => $docData["DocDate"],
//            'DocMonth' => $docData["DocMonth"],
//            'DocYear' => $docData["DocYear"],
//            'DocTime' => $docData["DocTime"],
//            'CreditType' => $CreditType,
//            'ActivityJson' => $docData["ActivityJson"],
//            'PayToken' => $payment->payToken,
//            'TransactionId' => $transaction
//        );
//        if($SettingsInfo->CpaType == 1 && $payment->total_payments > 0 ){
//            $docsList["Amount"] = $payment->total_amount;
//            $dpObj->insert($docsList);
//        }
//        else{
//            for($i = 1; $i < $payment->total_payments;$i++){
//                $docsList["Payments"] = $i;
//                $add = $i - 1;
//                $AddDate = '+'.$add.' month';
//                $docsList["CreditDate"] = date('Y-m-d', strtotime($AddDate, strtotime($docData["UserDate"])));
//                $dpObj->insert($docsList);
//            }
//        }
//
//    }
//    public function docsToItems($docData,$payment,$item){
//        $docObj = new docs2item();
//        $doc = array(
//            'CompanyNum' => $payment->company_num,
//            'TrueCompanyNum' => $docData["TrueCompanyNum"],
//            'Brands' => $docData["Brands"],
//            'ClientId' => $docData["ClientId"],
//            'ItemId' => $item->id,
//            'DocsId' => $docData["id"],
//            'Amount' => $payment->payment_sum,
//            'Department' => $item->Department,
//            'MemberShip' => $item->MemberShip,
//            'ItemName' => htmlentities($item->ItemName),
//            'UserDate' => $docData["UserDate"]
//        );
//        $docObj->insert($doc);
//    }

//
//    /**
//     * @param $type int Credit Card Type
//     * @param $slika int TypeShva - 1 = Meshulam, 0 = Yaad
//     * @param null $brand
//     * @return string
//     */
//    public function getCreditCardName($type,$slika,$brand = null){
//        if($slika == 0){
//            $CardType = array(
//                0 => "אמריקן אקספרס",
//                1 => "מסטרקארד",
//                2 => "ויזה",
//                3 => "דיינרס",
//                5 => "ישראכרט"
//            );
//            if ($type == '1') {
//                $BrandName = 'כרטיס ישראכרט מסוג ' . $CardType[$brand];
//            } else if ($type == '2') {
//                $BrandName = 'כרטיס כאל מסוג ' . $CardType[$brand];
//            } else if ($type == '3') {
//                $BrandName = 'כרטיס מסוג דיינרס';
//            } else if ($type == '4') {
//                $BrandName = 'כרטיס מסוג אמריקן אקספרס';
//            } else if ($type == '5') {
//                $BrandName = 'כרטיס JCB מסוג ' . $CardType[$brand];
//            } else if ($type == '6') {
//                $BrandName = 'כרטיס לאומי קארד מסוג ' . $CardType[$brand];
//            } else {
//                $BrandName = '';
//            }
//            return $BrandName;
//        }
//        else if($slika == 1){
//            if ($type == 1) {
//                $Local = 'ישראלי';
//            } else {
//                $Local = 'תייר';
//            }
//            $BrandName = 'כרטיס ' .$brand . ' - ' . $Local;
//            return $BrandName;
//        }
//        return "";
//    }
//
//    /**
//     * @param $ptId
//     * @param $slika
//     * @return int|string
//     */
//    public function getTashTypeFromPayToken($ptId, $slika){
//        /**
//         * @param $payToken stdClass
//         */
//        $paytokenObj = new PayToken();
//        $payToken = $paytokenObj->getRow($ptId);
//        $tashTypeDB = 1;
//        if($slika == 0){
//            if ($payToken->tashType == '0') {
//                $tashTypeDB = '1';
//            } else if ($payToken->tashType == '1') {
//                $tashTypeDB = '2';
//            } else if ($payToken->tashType == '2') {
//                $tashTypeDB = '4';
//            } else if ($payToken->tashType == '6') {
//                $tashTypeDB = '3';
//            } else {
//                $tashTypeDB = '5';
//            }
//        }
//        else if($slika == 1){
//            if ($payToken->tashType == '0') {
//                $tashTypeDB = '1';
//            } else if ($payToken->tashType == '1') {
//                $tashTypeDB = '2';
//            } else if ($payToken->tashType == '6') {
//                $tashTypeDB = '3';
//            } else {
//                $tashTypeDB = '5';
//            }
//
//        }
//        return $tashTypeDB;
//    }
//
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function checkMobile($phone){
        $mobileRegex = Client::mobileRegex;
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace("-","",$phone);
        if(preg_match($mobileRegex, $phone)) {
            $mobile = substr($phone, 0, 4) == '+972' ? substr($phone, 4, strlen($phone)) : $phone;
            $mobile = substr($mobile, 0, 1) == '0' ? substr($mobile, 1, strlen($mobile)) : $mobile;
            // israeli phone number with country code
            $mobile = '+972'.$mobile;
            return $mobile;
        }
        return false;
    }
    public function checkEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return $email;
    }
    public function getClientBy($check,$company){
        $clientObj = new Client();
        $phone = $this->checkMobile($check);
        $email = $this->checkEmail($check);
        $id = is_numeric($check);
        if(!$phone && $id){
            $client = $clientObj->getClientBy("id",$id,$company);
        }
        elseif($phone){
            $client = $clientObj->getClientBy("ContactMobile",$phone,$company);
            if(!$client){
                $check = str_replace("-","",$check);
                $client = $clientObj->getClientBy("ContactMobile",$check,$company);
            }
        }
        elseif ($email){
            $client = $clientObj->getClientBy("Email",$email,$company);
        }
        else{
            return false;
        }
        if($client){
            return $client;
        }
        return false;

    }
    public static function getValidType($Type)
    {
        $Valid_Type = '';
        if ($Type == '1') {
            $Valid_Type = 'days';
        } else if ($Type == '2') {
            $Valid_Type = 'weeks';
        } else if ($Type == '3') {
            $Valid_Type = 'months';
        }
        return $Valid_Type;
    }

    public function getDayName($num){
        if($num == 0){
            return lang("sunday");
        }
        elseif ($num == 1){
            return lang("monday");
        }
        elseif ($num == 2){
            return lang("tuesday");
        }
        elseif ($num == 3){
            return lang("wednesday");
        }
        elseif ($num == 4){
            return lang("thursday");
        }
        elseif ($num == 5){
            return lang("friday");
        }
        elseif ($num == 6){
            return lang("saturday");
        }
    }
    public function getSlikaBrandName ($Issuer, $Brand, $TypeShva) {
        if($TypeShva == 0) {
            $CardTypes = array(
                0 => "אמריקן אקספרס",
                1 => "מסטרקארד",
                2 => "ויזה",
                3 => "דיינרס",
                5 => "ישראכרט"
            );
            $CardType = $CardTypes[$Brand] ?? '';
            if ($Issuer == '1') {
                $BrandName = 'כרטיס ישראכרט מסוג ' . $CardType;
            } else if ($Issuer == '2') {
                $BrandName = 'כרטיס כאל מסוג ' . $CardType;
            } else if ($Issuer == '3') {
                $BrandName = 'כרטיס מסוג דיינרס';
            } else if ($Issuer == '4') {
                $BrandName = 'כרטיס מסוג אמריקן אקספרס';
            } else if ($Issuer == '5') {
                $BrandName = 'כרטיס JCB מסוג ' . $CardType;
            } else if ($Issuer == '6') {
                $BrandName = 'כרטיס לאומי קארד מסוג ' . $CardType;
            } else {
                $BrandName = '';
            }
        } else {
            $CardBrand = [
                1 => "דיינרס",
                2 => "מסטרקארד",
                3 => "ויזה",
                4 => "מאסטרו",
                5 => "ישראכרט"
            ];
            $CardType = [
                1 => 'מקומי',
                2 => 'זר',
                3 => 'דלק',
                4 => 'דביט',
                5 => "מתנה / נטען"
            ];
            $cardBrandStr = $CardBrand[$Brand] ?? 'לא ידוע';
            $cardTypeStr = $CardType[$Issuer] ?? 'לא ידוע';
            $BrandName = 'כרטיס ' . $cardTypeStr . ' של ' . $cardBrandStr;
        }
        return $BrandName;
    }
    public function createRandomNumber(){
        $GroupNumber = rand(1262055681,1262055681);
        $GroupNumber = uniqid().''.strtotime(date('YmdHis')).''.$GroupNumber.''.rand(1,9999999);
        $RandomNumber = uniqid($GroupNumber);
        return $RandomNumber;
    }
}


