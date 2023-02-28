<?php
require_once '../app/init.php';
require_once 'Classes/ClassStudioDate.php';

header('Content-Type: application/json; charset=utf-8');

if (Auth::guest()) exit;

if (Auth::userCan('82')){

  $classStudioDateObj = new ClassStudioDate();
  $result = $classStudioDateObj->getManageClass();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}









