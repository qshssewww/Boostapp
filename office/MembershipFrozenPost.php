<?php require_once '../app/initcron.php';
require_once __DIR__."/Classes/ClientActivities.php";

if (Auth::userCan('147')):

    header('Content-Type: text/html; charset=utf-8');

    if (Auth::guest()) exit;

    $CompanyNum = Auth::user()->CompanyNum;
    $OpenTables = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('Freez', '=', '1')->orderBy('ItemText', 'ASC')->get();

    $clientAct = new ClientActivities();
    $result = $clientAct->membershipFrozenInfo($CompanyNum);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

endif;










