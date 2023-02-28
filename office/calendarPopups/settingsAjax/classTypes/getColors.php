<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $colors = DB::table('boostapp.colors')->where("calendar", "=", 1)->get();
    if($colors){
        echo json_encode($colors, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>