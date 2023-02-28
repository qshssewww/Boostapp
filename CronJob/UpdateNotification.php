<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/CompanyProductSettings.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

$Vaild_TypeOption = array(
    1 => "day",
    2 => "week",
    3 => "month",
    4 => "year"     
);	

//////////////////////////////////////////////////////////////// עדכון עריכת שיעורים ///////////////////////////////////////////////////////

try {

$GetClasses = DB::table('client_activities')->where('Status','=','0')->where('ChangeStatus','=','1')->select('id', 'CompanyNum', 'TrueDate', 'ItemId', 'NotificationDays')->limit(300)->get();

    foreach ($GetClasses as $GetClasse) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings) || !empty($GetClasse->TrueDate)) {


            $ItemsInfo = DB::table('items')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('id', '=', $GetClasse->ItemId)->first();
            if (!empty($ItemsInfo)) {
                $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($GetClasse->CompanyNum)->NotificationDays ?? 0; // התראה לפני סוף מנוי

                $Department = $ItemsInfo->Department;
                $TrueDate = $GetClasse->TrueDate;
                $OldNotificationDays = $GetClasse->NotificationDays;

                $Vaild_TypeOptions = $Vaild_TypeOption['1'];
                $ItemsTime = '-' . $NotificationDays . ' ' . $Vaild_TypeOptions;

                $time = strtotime($TrueDate);
                $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

                if ($NotificationDate <= date('Y-m-d')) {
                    $NotificationDate = date("Y-m-d", strtotime('+1 day', strtotime(date('Y-m-d'))));
                }

                if ($OldNotificationDays <= date('Y-m-d') || $TrueDate <= $NotificationDate) {
                    $NotificationDate = $OldNotificationDays;
                }

                if ($NotificationDays == '0' || $Department == '4' || empty($TrueDate)) {
                    $NotificationDate = NULL;
                }

                DB::table('client_activities')
                    ->where('id', $GetClasse->id)
                    ->where('CompanyNum', $GetClasse->CompanyNum)
                    ->update(array('NotificationDays' => $NotificationDate, 'ChangeStatus' => '0'));
            }


        } else {
            DB::table('client_activities')
                ->where('CompanyNum', '=', $GetClasse->CompanyNum)
                ->where('id', '=', $GetClasse->id)
                ->update(array('ChangeStatus' => '0'));
        }

    }

//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClasse)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClasse),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
?>
