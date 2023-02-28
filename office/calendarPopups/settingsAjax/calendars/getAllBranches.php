<?php
require_once '../../../../app/init.php';
require_once "../../../Classes/Brand.php";
require_once "../../../Classes/Section.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->CompanyNum)){
        $brand= new Brand();
        $res = $brand->getAllByCompanyNum($data->CompanyNum);
        echo json_encode($res?$res:[], JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>