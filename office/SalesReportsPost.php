<?php

require_once '../app/initcron.php';
require_once "Classes/ClientActivities.php";

if (Auth::guest()) exit;

if (Auth::userCan('128')){

    header('Content-Type: text/html; charset=utf-8');

    $dateFrom = $_POST["dateFrom"] ?? date("Y-m-t"); // get date from
    $dateTo = $_POST["dateTo"] ?? date("Y-m-t"); // get date to

    $clientActivities = new ClientActivities(); // create instance of ClientActivities object
    $result = $clientActivities->getSalesReports($dateFrom, $dateTo); // return array of result

    echo json_encode($result, JSON_UNESCAPED_UNICODE);

}