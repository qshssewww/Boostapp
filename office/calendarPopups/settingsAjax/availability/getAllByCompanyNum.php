<?php
require_once '../../../../app/init.php';
require_once "../../../Classes/TemplateAvailability.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->CompanyNum)){
        $templateAvailability= new TemplateAvailability();
        $res = $templateAvailability->getAllValidTemplateAvailability($data->CompanyNum);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>