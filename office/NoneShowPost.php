<?php 
require_once '../app/initcron.php';
require_once 'Classes/Client.php'; 

if (Auth::guest()) exit;

if (Auth::userCan('22')){ 

  header('Content-Type: text/html; charset=utf-8');

  $dateFrom = isset($_POST["dateFrom"]) ? $_POST["dateFrom"] : date("Y-m-d");
  $dateTo = isset($_POST["dateTo"]) ? $_POST["dateTo"] : date("Y-m-d");

  $client = new Client();
  $result = $client->getNoneShow($dateFrom, $dateTo);

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}