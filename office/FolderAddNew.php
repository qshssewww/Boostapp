<?php
require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/VideoLimit.php";
require_once "Classes/Video.php";
require_once "Classes/Company.php";
require_once "Classes/Users.php";
header("Content-Type: application/json", true);
if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        $folderObJ = new VideoFolder();
        $folderLimitObJ = new VideoLimit();
        $company = Company::getInstance();
        $postdata = file_get_contents("php://input"); 
        $obj = json_decode($postdata); 
        $obj->CompanyNum=$company->__get('CompanyNum');
        $results=[];
        ////////
        if($obj->showForAll && $obj->showForAll==true){
            $id=$folderObJ->addVideoFolder($obj);
        }else{
            $id=$folderObJ->addVideoFolder($obj);
            $obj->id=$id;
            $folderLimitObJ->addOrUpdateLimit($obj);
        }
        echo json_encode($id,JSON_UNESCAPED_UNICODE);
    }
}