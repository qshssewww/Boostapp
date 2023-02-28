<?php
require_once '../../../app/init.php';
require_once "../../Classes/ShopPost.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $data = $_POST;
    $shop_post=new ShopPost();
    if($data){
        $res = $shop_post->toggleSuspendItem($data["id"],$data['disabled']);
        echo $data["id"];
    }
    else
    {
        echo "error";
    }

}
?>