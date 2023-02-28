<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->CompanyNum)){
        $res = DB::table('items_roles')
        ->join('items', 'items.id', '=', 'items_roles.ItemId')
        ->where("items_roles.Item", "=", "Class")->where('items_roles.CompanyNum',"=",$data->CompanyNum)
        ->select('items_roles.ItemId','items_roles.Class', 'items.ItemName')
        ->get();
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>