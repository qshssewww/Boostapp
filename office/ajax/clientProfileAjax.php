<?php

require_once '../../app/init.php';
require_once '../Classes/ClientRegistrationFees.php';
require_once '../Classes/Company.php';
require_once '../Classes/StudioBoostappLogin.php';

if (Auth::check()) {
    if (!empty($_POST['fun']) && $_POST['fun'] == 'UserAppAccess') {
        $client =
            StudioBoostappLogin::where('ClientId', $_POST['clientId'])
                ->where('CompanyNum', $_POST['companyNum'])
                ->first();
        $client->StatusBadPoint = $_POST['newValue'];
        $client->save();
        echo json_encode(["msg" => "Success"]);
        exit;
    }
}

if (Auth::userCan('55')){
    $clientRegObj = new ClientRegistrationFees();
    $company = Company::getInstance(false);
    switch ($_POST["regType"]){
        case 1:
            $check = $clientRegObj->getReg($_POST["regId"],$company->__get("CompanyNum"),$_POST["clientIdReg"]);
            if(empty($check)){
                echo json_encode(["msg" => "Failed"]);
                break;
            }
            $res = $clientRegObj->update($_POST["regId"],["purchase_time" => $_POST["regDate"]]);
            if($res == null || $res == "" || $res == 0){
                echo json_encode(["msg" => "Failed"]);
                break;
            }
            echo json_encode(["msg" => "Success"]);
            break;
        case 2:
        case 3:
            $check = $clientRegObj->getReg($_POST["regId"],$company->__get("CompanyNum"),$_POST["clientIdReg"]);
            if(empty($check)){
                echo json_encode(["msg" => "Failed"]);
                break;
            }
            $res = $clientRegObj->update($_POST["regId"],["status" => $_POST["status"]]);
            echo json_encode(["msg" => "Success"]);
            break;

        default:
            echo json_encode(["msg" => "wrong type"]);
    }
}
