<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->CompanyNum)){
        $res = DB::table('boostapp.class_type')->where("Status", "=", "0")->where('CompanyNum',"=",$data->CompanyNum)->get();
        if($res){
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "[]";
            return;
        }
    }else{
        echo "error";
    }
}
?>