<?php
require_once '../app/initcron.php';
require_once "Classes/ClientActivities.php";

if (Auth::userCan('147')):

    header('Content-Type: text/html; charset=utf-8');
    if(!Auth::check())
        exit;

    $start = isset($_POST['start']) ? date('Y-m-d', strtotime($_POST['start'])) : date('Y-m-01');
    $end = isset($_POST['end']) ? date('Y-m-d', strtotime($_POST['end'])) : date('Y-m-t');

    $CompanyNum = Auth::user()->CompanyNum;
    $ClientActivities = new ClientActivities();
    $result = $ClientActivities->getFreezesByDates($start, $end);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

endif;