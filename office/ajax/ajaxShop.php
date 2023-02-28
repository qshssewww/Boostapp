<?php

require_once '../../app/init.php';

require_once '../Classes/Company.php';
require_once '../Classes/NewShopItem.php';
require_once '../Classes/ShopPost.php';
require_once '../Classes/FilesUpload.php';

if (Auth::check()) {

    $data = $_POST["data"];
    $data = json_decode($data, true);
    $newItem = new NewShopItem();
    $shopPost = new ShopPost();
    $message = "Success";
    switch ($data["page"]) {

        //Membership
        case 1:
        case 2:
        case 3:
            $dataContent = $newItem->newMembershipItem($data);
            if (gettype($dataContent) == "string") {
                $message = $dataContent;
            }
            $shopPost->newMembership($dataContent);
            break;

        //Items
        case 4:
            $dataContent = $newItem->newItem($data);
            if (gettype($dataContent) == "string") {
                $message = $dataContent;
            }
            $res = $shopPost->newItem($dataContent);
            if ($res === true) {
                echo "success";
            } else {
                echo "fails";
            }
            break;

        //Payments Pages
        case 5:
            $dataContent = $newItem->newPaymentPage($data);
            if (gettype($dataContent) == "string") {
                $message = $dataContent;
            }
            break;

        //Insurance And Documents
        case 6:
            $dataContent = $newItem->newInsurance($data);
            if (gettype($dataContent) == "string") {
                $message = $dataContent;
            }
            break;
        default:
            $message = "Wrong Type";
    }
    echo $message;
}
