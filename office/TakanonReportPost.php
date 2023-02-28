<?php 
require_once '../app/initcron.php';
require_once 'Classes/Client.php';

if (Auth::guest()) exit;

if (Auth::userCan('138')){ 

  header('Content-Type: text/html; charset=utf-8');

  

  $client = new Client();
  $result = $client->getTakanonReport();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}