<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->id)){
        $res = DB::table('boostapp.class_type')->where("id", "=", $data->id)->first();
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>