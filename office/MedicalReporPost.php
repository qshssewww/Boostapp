<?php

require_once '../app/initcron.php';
require_once __DIR__."/Classes/Client.php";

if (Auth::userCan('138')):

    header('Content-Type: application/json; charset=utf-8');
    if(!Auth::check()) exit;
    $CompanyNum = Auth::user()->CompanyNum;
    $Client = new Client();
    $result = $Client->getMedicalInfo($CompanyNum);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

endif;

