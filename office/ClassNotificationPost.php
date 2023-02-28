<?php 
require_once '../app/initcron.php';
require_once 'Classes/ClassStudioAct.php';

if (Auth::guest()) exit;

if (Auth::userCan('150')){

  header('Content-Type: text/html; charset=utf-8');

  $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d');
  $dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d'); 

  $classStudioActObj = new ClassStudioAct();
  $result = $classStudioActObj->getClassNotificationReport($dateFrom, $dateTo);

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}











