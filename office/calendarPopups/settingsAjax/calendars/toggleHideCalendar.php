<?php
require_once '../../../../app/init.php';
require_once "../../../Classes/Brand.php";
require_once "../../../Classes/Section.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->id) && isset($data->Status)){
        $section= new Section();
        $res = $section->editSection($data->id,array('Status'=>$data->Status));
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>