<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
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

//////////////////////////////////////////////////////////////// הפשרת מנוי אוטומטי ///////////////////////////////////////////////////////
try {

    $GetClientBlocks = DB::table('appsettings')->where('MemberShipLimitType', '=', '0')->where('MemberShipLimitUnBlock', '=', '0')->get();

    foreach ($GetClientBlocks as $GetClientBlock) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientBlock->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {


            $Date = date('Y-m-d');
            $Time = date('H:i:s');
            $Dates = date('Y-m-d H:i:s');
            $CompanyNum = $GetClientBlock->CompanyNum;

            $GetClientAppBlocks = DB::table('boostapplogin.studio')->where('CompanyNum', '=', $CompanyNum)->where('StatusBadPoint', '=', '1')->where('Status', '=', '0')->where('BlockDate', '<=', $Date)->get();

            foreach ($GetClientAppBlocks as $GetClientAppBlock) {

                $ClientInfo = DB::table('client')->where('id', '=', $GetClientAppBlock->ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

                if (!empty($ClientInfo) && $ClientInfo->Status != '1') {


                    $BlockDatex = null;

                    DB::table('boostapplogin.studio')
                        ->where('id', $GetClientAppBlock->id)
                        ->where('CompanyNum', $CompanyNum)
                        ->update(array('StatusBadPoint' => '0', 'BlockDate' => $BlockDatex));


                    $SubjectBad = lang('release_from_block');
                    $TextNotificationBad = lang('message_sent_to_customer');


                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '25')->first();

                    $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

                    $TemplateStatus = $Template->Status;
                    $TemplateSendOption = $Template->SendOption;
                    $SendStudioOption = $Template->SendStudioOption;
                    $Type = '0';

                    if ($TemplateStatus != '1' && $SendStudioOption != 'BA000') {
                        DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $CompanyNum, 'Type' => '3', 'ClientId' => $GetClientAppBlock->ClientId,
                                'Subject' => $SubjectBad, 'Text' => $TextNotificationBad, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3',
                                'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => 'BA999'));
                    }

                    if ($TemplateSendOption != 'BA999' && $TemplateSendOption != 'BA000') {
                        $myArray = explode(',', $TemplateSendOption);
                        $Type2 = (in_array('2', $myArray)) ? '2' : '';
                        $Type1 = (in_array('1', $myArray)) ? '1' : '';
                        $Type0 = (in_array('0', $myArray)) ? '0' : '';

                        if (@$Type2 != '') {
                            $Type = $Type2;
                        }
                        if (@$Type1 != '') {
                            $Type = $Type1;
                        }
                        if (@$Type0 != '') {
                            $Type = $Type0;
                        }
                    }


                    /// עדכון תבנית הודעה
                    $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName,$Template->Content);
                    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '',$Content1);
                    $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);
//                    $ContentTrue = $Content3;


//                    $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                    $Subject = $Template->Subject;

                    if ($TemplateStatus != '1') {
                        if ($TemplateSendOption != 'BA000') {
                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $GetClientAppBlock->ClientId, 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                        }
                    }

                }

            }


        }


    }


//////////////////////////////////////////////////////////////// אי הגעה לסטודיו ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClientBlock)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientBlock),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}