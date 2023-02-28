<?php

require_once __DIR__.'/../app/init.php';
require_once __DIR__."/Classes/docs2item.php";

if (Auth::guest()) exit;

if (Auth::userCan('128')){

//  header('Content-Type: text/html; charset=utf-8');

  $dateFrom = $_POST["dateFrom"] ?? date("Y-m-01");
  
  $dateTo = $_POST["dateTo"] ?? date("Y-m-t");

  $docs2item = new docs2item();
  $result = $docs2item->GetSales($dateFrom, $dateTo);

  echo json_encode($result, JSON_UNESCAPED_UNICODE);

}