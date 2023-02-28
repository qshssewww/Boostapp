<?php
require_once '../../../app/init.php';
require_once "../../Classes/ItemDetails.php";
require_once "../../Classes/Company.php";
require_once "../../Classes/Item.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $data = $_POST;
//    $item = new Item($data["id"]);
    $item = Item::find($data["id"]);
    $item_detail = new ItemDetails();
    if($data){
        if ($item->__get("CompanyNum") == Company::getInstance()->__get("CompanyNum")) {
            $res = $item_detail->updateItemDetails($data);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }
    else
    {
        echo "error";
    }

}
?>