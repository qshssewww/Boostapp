<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$StatusAct = '0';
$ThisDate = date('Y-m-d');
$ThisTime = date('H:i:s');
try {
    $CalendarChecks = DB::table('calendar')->where('StartDate', '<=', $ThisDate)->where('Status', '=', '0')->get();

    foreach ($CalendarChecks as $CalendarCheck) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $CalendarCheck->CompanyNum)->where('Status', '=', '0')->first();
        if ($CheckSettings) {

            $CompanyNum = $CalendarCheck->CompanyNum;
            $ClientId = $CalendarCheck->ClientId;


//// הגדרת סטטוס לפייפליין    

            $CountTaskOpen = DB::table('calendar')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->count();
            $CountTaskOver = DB::table('calendar')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('StartDate', '<', date('Y-m-d'))->where('Status', '=', '0')->count();
            $CountTaskFuture = DB::table('calendar')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('StartDate', '>', date('Y-m-d'))->where('Status', '=', '0')->count();
            $CountTaskToday = DB::table('calendar')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('StartDate', '=', date('Y-m-d'))->where('Status', '=', '0')->count();
            $CountTaskClose = DB::table('calendar')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '1')->count();

            $TaskStatus = '2';
            $StatusColor = '#fff0b3';

            if ($CountTaskFuture >= '1') {
                $TaskStatus = '4';
                $StatusColor = '#40A4C5';
            }

            if ($CountTaskClose >= '1' && $CountTaskOpen == '0') {
                $TaskStatus = '3';
                $StatusColor = '#abb1bf';
            }

            if ($CountTaskClose == '0' && $CountTaskOpen == '0') {
                $TaskStatus = '2';
                $StatusColor = '#fff0b3';
            }

            if ($CountTaskOver >= '1') {
                $TaskStatus = '1';
                $StatusColor = '#ff8080';
            }

            if ($CountTaskToday >= '1') {
                $TaskStatus = '0';
                $StatusColor = '#9ce2a7';
            }

            DB::table('pipeline')
                ->where('CompanyNum', $CompanyNum)
                ->where('ClientId', $ClientId)
                ->update(array('TaskStatus' => $TaskStatus, 'StatusColor' => $StatusColor));


        }

    }

    $Cron->end();
} catch (Exception $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($CalendarCheck)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($CalendarCheck),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


