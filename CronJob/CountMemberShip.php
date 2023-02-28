<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// עדכון טבלת לקוחות + ספירת מנויים ///////////////////////////////////////////////////////
try {


    $GetClientActivitys = DB::table('membership_type')->where('CompanyNum', '!=', '569121')->where('Status', '=', '0')->get();

    foreach ($GetClientActivitys as $GetClientActivity) {

        ///// ספירת סוג מנוי
        $CompanyNum = $GetClientActivity->CompanyNum;
        $MemberShip = $GetClientActivity->id;

        $MemberShipCount = DB::table('client_activities')
            ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
            ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
            ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
            ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
            ->count();

        DB::table('membership_type')
            ->where('id', $MemberShip)
            ->where('CompanyNum', $CompanyNum)
            ->update(array('Count' => $MemberShipCount));


    }


//////////////////////////////////////////////////////////////// סיום טבלת לקוחות + ספירת מנויים ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClientActivity)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientActivity),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
?>
