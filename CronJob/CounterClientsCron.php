<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/ActiveClients.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/CheckClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] ."/office/Classes/Client.php";

// require_once "../app/init.php";
//require_once "../office/Classes/ActiveClients.php";
//require_once "../office/Classes/CheckClient.php";
//require_once "../office/Classes/Client.php";


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

try {

    $Companies = DB::table('settings')->get();
    $ActiveClients = new ActiveClients();
    $CheckClient = new CheckClient();
    $clients = new Client();

    foreach ($Companies as $company) {
        echo("Company_start_" . $company->CompanyNum . " " . date('H:i:s') . "\n");
        $clientMembership = $clients->getActiveCheck($company->CompanyNum);
        $ActiveClients->setData($clientMembership["clientsActiveCounter"], $company->CompanyNum);
        $CheckClient->setData($clientMembership["clientCheckCounter"], $company->CompanyNum);
        echo("Company_end_" . $company->CompanyNum . " " . date('H:i:s') . "\n");
    }
    echo("Cron_end " . date('H:i:s') . "\n");
    $Cron->end();

} catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($company)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($company),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
