<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/CoronaHealthCheck.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/ClassCalendar.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/Company.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/EncryptDecrypt.php";

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");


// require_once "../app/init.php";
// require_once "../office/Classes/CoronaHealthCheck.php";
// require_once "../office/Classes/ClassCalendar.php";
// require_once "../office/Classes/Company.php";
// require_once "../office/Classes/EncryptDecrypt.php";



$date = date('Y-m-d H:i:s');
$classCalendar = new ClassCalendar();
$classes = $classCalendar->getAllClassesOnSpecificDate($date,"hours",1,true);
if(!empty($classes)) {
    foreach ($classes as $class) {

        $company = new Company($class->__get("CompanyNum"),false);
        if ($company->__get("coronaStmt") == 0) {
            continue;
        }
        $client = DB::table("classstudio_act")->leftjoin("client", "classstudio_act.ClientId", "=", "client.id")->leftjoin("classstudio_date", "classstudio_date.id", "=", "classstudio_act.ClassId")
            ->where("classstudio_act.ClassId", "=", $class->__get("id"))->where("classstudio_act.coronaStmt", "=", 0)->where('classstudio_date.is_zoom_class', '=', '0')->whereNull('classstudio_date.liveClassLink')->whereIn('classstudio_act.Status', array(1,2,6,10,11,12,15,16,21,22,23))
            ->select("classstudio_act.ClientId As ClientId","classstudio_act.id as ActId","classstudio_act.ClassId as ClassId","classstudio_act.ClassName as ClassName",
                "client.FirstName as FirstName","client.LastName as LastName","client.ContactMobile as phone")->first();
        if($client != null && $client != "") {
            $app = DB::table("appnotification")->where("ClientId","=", $client->ClientId)->where("ClassId","=",$class->__get("id"))->whereIn('Status', array(0,1))->where('Subject', '=', lang('covid_declaration'))->first();
            if($app != null){
                continue;
            }
            $studio = DB::table("boostapplogin.studio")->where("CompanyNum", "=", $class->__get("CompanyNum"))->where("ClientId", "=", $client->ClientId)->first();
            if($studio == null){
                continue;
            }
            $subject = lang('covid_declaration');
            $time = date('H:i:s', strtotime("-1 hours", strtotime($class->__get("StartTime"))));
            $text = lang('hi_corona_cron'). $client->FirstName.",";
            $text .= "<br>";
            $text .= lang('covid_notification_corona') . $client->ClassName . lang('set_today_corona') . date("H:i", strtotime($class->__get("StartTime"))). " ";
            if ($studio->tokenFirebase != null && $studio->tokenFirebase != "") {
                // $link = "https://app.boostapp.co.il/Home.php?GetUrl=" . $studio->StudioUrl;
                $type = 0;
            } else {
                $encrypt = new EncryptDecrypt();
                $userEncrypt = $encrypt->encryption($client->ActId);
                $text .= "<br>";
                $text .= lang('found_link_corona');
                $link = "https://app.boostapp.co.il/corona.php?act=" . $userEncrypt;
                $type = 2;
                $text .= $link;
            }
            $text .= "<br>";
            DB::table('appnotification')->insertGetId(
                array('CompanyNum' => $class->__get("CompanyNum"), 'ClientId' => $client->ClientId,
                    'Subject' => $subject, 'Text' => $text, 'Dates' => $date, 'UserId' => '0', 'Type' => $type, 'Date' => $class->__get("StartDate"), 'Time' => $time,"ClassId" => $class->__get("id") ,"priority" => 1)
            );
        }
    }
}
$Cron->end();

