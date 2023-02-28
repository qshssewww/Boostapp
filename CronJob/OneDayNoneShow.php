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



//////////////////////////////////////////////////////////////// אי הגעה לסטודיו ///////////////////////////////////////////////////////
try {

 if ($ThisDay == 'Sunday') {

  $GetAppSettingss = DB::table('appsettings')->where('ClassWeek', '=', '1')->get();

  foreach ($GetAppSettingss as $GetAppSettings) {

   $CheckSettings = DB::table('settings')
       ->where('CompanyNum', '=', $GetAppSettings->CompanyNum)
       ->where('Status', '=', '0')
       ->first();
   if ($CheckSettings) {


    $ClassWeekMonth = $GetAppSettings->ClassWeekMonth;
    $ItemsMin = '-' . $ClassWeekMonth . ' month';
    $MonthDate = date("Y-m-d", strtotime($ItemsMin, strtotime($ThisDate)));

    $StartDateWeek = date('Y-m-d', strtotime('sunday this week -1 week'));
    $EndDateWeek = date('Y-m-d', strtotime('saturday this week'));

    $GetClientLastClasses = DB::table('client')
        ->where('CompanyNum', '=', $GetAppSettings->CompanyNum)
        ->where('Status', '=', '0')
        ->where('FreezStatus', '=', '0')
        ->whereBetween('LastClassDate', array($MonthDate, $ThisDate))
        ->get();

    foreach ($GetClientLastClasses as $GetClientLastClass) {

     //// ספירת הלקוח
     $ClientNoneShowMonthCount = DB::table('classstudio_act')
         ->where('FixClientId', '=', $GetClientLastClass->id)
         ->where('CompanyNum', '=', $GetClientLastClass->CompanyNum)
         ->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))
         ->whereIn('Status', array(1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 23))
         ->select('id')
         ->count();

     if ($ClientNoneShowMonthCount == '0') { /// לקוח לא היה בסטודיו

      $Date = date('Y-m-d');
      $Time = '13:00:00';
      $Dates = date('Y-m-d H:i:s');

      $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClientLastClass->CompanyNum)->where('Type', '=', '2')->first();
      $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientLastClass->CompanyNum)->first();

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

      $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName, $Template->Content);
      $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$GetClientLastClass->CompanyName, $Content1);
      $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$GetClientLastClass->FirstName, $Content2);
      $ContentTrue = $Content3;


      $Text = $ContentTrue;
      $Subject = $Template->Subject;

      if ($TemplateStatus != '1') {
       if ($TemplateSendOption != 'BA000') {
        $AddNotification = DB::table('appnotification')->insertGetId(
            array('CompanyNum' => $GetClientLastClass->CompanyNum, 'ClientId' => $GetClientLastClass->id, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
       }
      }


     }


    }   //// סיום לולאת לקוחות


   } /// סיום לולאה הגדרות אפליקציה

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
 if(isset($GetClientLastClass)){
  $util = new Utils();
  $arr["data"] = json_encode($util->createArrayFromObj($GetClientLastClass),JSON_UNESCAPED_UNICODE);
 }
 $Cron->cronLog($arr);
}
?>
