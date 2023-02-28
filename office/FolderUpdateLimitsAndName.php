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
        $postdata = file_get_contents("php://input"); 
        $obj = json_decode($postdata); 
        $results=[];
        if(!isset($obj->membership) || $obj->membership == null){
            $obj->showForAll = true;
        }
        ////////
        if($obj->showForAll && $obj->showForAll==true){
            $results[]=$folderObJ->updateFolderNameAndLimit($obj);
        }else{
            $results[]=$folderObJ->updateFolderNameAndLimit($obj);
            if(isset($obj->membership)){
                $results[]=$folderLimitObJ->addOrUpdateLimit($obj);
            }
        }
        echo json_encode($results,JSON_UNESCAPED_UNICODE);
    }
}
