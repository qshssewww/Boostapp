<?php
require_once '../app/initcron.php'; 
require_once 'Classes/ClassStudioDateRegular.php';

if (Auth::guest()) exit;

if (Auth::userCan('144')){

  header('Content-Type: text/html; charset=utf-8');

  $classStudioDateRegularObj = new ClassStudioDateRegular();
  $result = $classStudioDateRegularObj->getRegularReport();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}











