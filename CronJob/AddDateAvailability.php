<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/MeetingStaffRuleAvailability.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");



try {
    $MeetingStaffRuleAvailability = new MeetingStaffRuleAvailability();
    $allRepeatAvailabilityRule = $MeetingStaffRuleAvailability->getAllRepeatAvailabilityRule();

    foreach ($allRepeatAvailabilityRule as $availabilityRule) {
        $lastDate = $availabilityRule->getLastDate();
        $nextNewDate = strtotime($lastDate) + 622080;
        if ($availabilityRule->EndPeriodicDate) {
            //if last + week > EndPeriodicDate: Availability rule status (end repeated)
            if( $nextNewDate > strtotime($availabilityRule->EndPeriodicDate) ) {
                $availabilityRule->Status = 0;
                $availabilityRule->save();
                continue;
            }
        }
        //Check out some activists date
        $amount = $availabilityRule->getAmountActive();
        $maxTimeRepeat = 30;
        $amountAdd = max($maxTimeRepeat - $amount, 0);
        $availabilityRule->addNewStaffDateAvailability(date('Y-m-d',$nextNewDate), $amountAdd);

    }

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
