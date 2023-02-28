<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');
$TodayBDay = date('m-d');
$StatusBDay = '0';

try {

//////////////////////////////////////////////////////////////// ימי הולדת היום ///////////////////////////////////////////////////////

    $GetClientDobs = DB::select("SELECT * FROM `client` where `Status` = '" . $StatusBDay . "' AND DATE_FORMAT(Dob, '%m-%d') = '" . $TodayBDay . "' ");

    foreach ($GetClientDobs as $GetClientDob) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', @$GetClientDob->CompanyNum)->where('Status', '=', '0')->first();
        if ($CheckSettings) {


            $Dob = @$GetClientDob->Dob;
            $Date = date('Y-m-d');
            $Time = '12:00:00';
            $Dates = date('Y-m-d H:i:s');

            //// תבנית מזל טוב
            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClientDob->CompanyNum)->where('Type', '=', '1')->first();
            $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientDob->CompanyNum)->first();

            $TemplateStatus = $Template->Status;
            $TemplateSendOption = $Template->SendOption;
            $SendStudioOption = $Template->SendStudioOption;
            $Type = '0';

            if ($TemplateSendOption == 'BA999') {
                $Type = '0';
            } else if ($TemplateSendOption == 'BA000') {
            } else {
                $myArray = explode(',', $TemplateSendOption);
                $Type2 = (in_array('2', $myArray)) ? '2' : '';
                $Type1 = (in_array('1', $myArray)) ? '1' : '';
                $Type0 = (in_array('0', $myArray)) ? '0' : '';

                if ($Type2 != '') {
                    $Type = $Type2;
                }
                if ($Type1 != '') {
                    $Type = $Type1;
                }
                if ($Type0 != '') {
                    $Type = $Type0;
                }

            }

            /// עדכון תבנית הודעה
                
            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName ?? '', $Template->Content);
            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $GetClientDob->CompanyName ?? '' ,$Content1);
            $Text = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $GetClientDob->FirstName ?? '', $Content2);
//            $ContentTrue = $Content3;
    
    
//            $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
            $Subject = $Template->Subject ?? '';

            if ($TemplateStatus != '1') {
                if ($TemplateSendOption != 'BA000') {
                    $AddNotification = DB::table('appnotification')->insertGetId(array(
                            'CompanyNum' => $GetClientDob->CompanyNum,
                            'ClientId' => $GetClientDob->id,
                            'Subject' => $Subject,
                            'Text' => $Text,
                            'Dates' => $Dates,
                            'UserId' => 0,
                            'Type' => $Type,
                            'Date' => $Date,
                            'Time' => $Time
                    ));
                }
            }


        }

    }

    $ThisDate = date('Y-m-d');
    $ThisDay = date('l');
    $ThisTime = date('H:i:s');


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
    if(isset($GetClientDob)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientDob),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
?>
