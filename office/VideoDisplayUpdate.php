<?php
require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/Video.php";
require_once "Classes/Company.php";
require_once "Classes/Users.php";
header("Content-Type: application/json", true);
if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        $videoObj = new Video();
        $postdata = file_get_contents("php://input"); 
        $obj = json_decode($postdata); 
        $results=$videoObj->updateVideoDisplay($obj);
        echo json_encode($results,JSON_UNESCAPED_UNICODE);
    }
}