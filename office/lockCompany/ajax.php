<?php

require_once dirname(__FILE__, 3) . '/app/init.php';
require_once dirname(__FILE__, 2) . '/Classes/247SoftNew/SoftPayment.php';
require_once dirname(__FILE__, 2) . '/Classes/247SoftNew/SoftClient.php';
require_once dirname(__FILE__, 2) . '/Classes/Company.php';
require_once dirname(__FILE__, 2) . '/Classes/YaadUtils.php';
require_once dirname(__FILE__, 2) . '/Classes/YaadNumbers.php';


if (Auth::check()) {
    try {
        $company = Company::getInstance(false);
        $softClient = SoftClient::getRow($company->__get("CompanyNum"), "FixCompanyNum");
        if ($softClient) {
            $softPayment = SoftPayment::getDeclinedPayment($softClient->id);
        }
        if (isset($softPayment)) {
            $yaadNumber = array(
                "CompanyNum" => $softClient->FixCompanyNum,
                "Amount" => $softPayment->Amount,
                "source" => "Lock Company",
                "PaymentPageId" => 0,
                "ClientId" => $softClient->id
            );
            $orderId = YaadNumbers::insert($yaadNumber);
            if($orderId > 0) {
                $yaadObj = new YaadUtils();
                $masof = YaadUtils::SOFT_YAAD_NUMBER;
//                $masof = "0010158521";
                $data = array(
                    "FirstName" => $softClient->CompanyName,
                    "LastName" => $softClient->ContactName,
                    "cell" => $softClient->ContactMobile,
                    "tash" => 1,
                    "Info" => "עדכון כרטיס",
                    "payment_sum" => $softPayment->Amount,
                    "clientId" => $softClient->id,
                    "fild2" => isDevEnviroment() ? "devloginpay" : "loginpay",
                    "order" => isDevEnviroment() ? "devloginpay-" . $orderId : "loginpay-" . $orderId
                );
                $url = $yaadObj->apiGetUrl($data, $masof);
            }
            else{
                throw new Exception("Databse Insert Failed",500);
            }
        }
        $res = array(
            "res" => $url,
            "code" => 200
        );
        echo json_encode($res);
    }
    catch (Exception $e){
        $res = array(
            "res" => $e->getMessage(),
            "code" => $e->getCode(),
            "line" => $e->getLine(),
            "file_path" => $e->getFile(),
            "trace" => $e->getTraceAsString()
        );
        echo json_encode($res);
    }
}
