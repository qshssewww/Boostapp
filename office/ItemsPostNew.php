<?php
require_once '../app/init.php';
require_once "Classes/Company.php";
require_once "Classes/ShopPost.php";
header('Content-Type: text/html; charset=utf-8');
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $data=$_GET['data'];
    if($data){
        $company = Company::getInstance();
        $shopPost = new ShopPost();
        if($data=="1"){
            $OpenTables = $shopPost->getItemAndMembership();
            echo $shopPost->dtMembership($OpenTables);
        }else if($data=="2"){
            $OpenTables = $shopPost->getPhysicalItems();
            echo $shopPost->dtItems($OpenTables);
        }else if($data=="3"){
            $OpenTables = $shopPost->getPayment();
            echo $shopPost->dtPayment($OpenTables);
        }
    
    }else{
        echo "error";
    }

}
