<?php

require_once '../../app/initcron.php';
require_once __DIR__.'/../Classes/Notificationcontent.php';
require_once __DIR__.'/../Classes/LoginPushNotifications.php';

$CompanyNum = Auth::user()->CompanyNum;

$CheckTime = date('H:i');
$Date = date('Y-m-d');
if ($CheckTime > '00:00' && $CheckTime < '06:59') {
    $Time = '07:00:00';
} else {
    $Time = date('H:i');
}
$Dates = date('Y-m-d H:i:s');


$DocGets = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '2')->groupBy('ClientId')->orderBy('Date', 'ASC')->get();

foreach ($DocGets as $DocGet) {

    $ClientId = $DocGet->ClientId;

    /// מחולל מספר מסמך
    $RandomTokenNumber = $DocGet->RandomUrl;

    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '15')->first();

    $ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->first();
    if(!$ClientInfo) {
        continue;
    }
    $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
    $ErrorText = $DocGet->Error;
    $KevaLink = '<a href="' . get_paymentboostapp_domain() . '/UpdatePayment.php?CUrl=' . $RandomTokenNumber . '">לחץ כאן</a>';

    /// עדכון תבנית הודעה
    $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName, $Template->Content);
    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$ClientInfo->CompanyName, $Content1);
    $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ClientInfo->FirstName, $Content2);
    $Content4 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ErrorText, $Content3);
    $Content5 = str_replace(Notificationcontent::REPLACE_ARR["click_here"], @$KevaLink, $Content4);
    $ContentTrue = $Content5;


    $Text = $ContentTrue;
    $Subject = $Template->Subject;
    $TextBoostapp = '<strong class="text-danger">' . 'הוראת קבע של ' . $ClientInfo->CompanyName . ' נכשלה. סיבת ההחזרה: ' . $ErrorText . ' נשלח קישור לעדכון פרטי אשראי ללקוח.' . '</strong>';

    LoginPushNotifications::sendLoginPushNotification(
        $CompanyNum,
        LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_horaat_keva_failed'],
        $Subject,
        $Text,
        $Date,
        $Time
    );

    $AddNotification = DB::table('appnotification')->insertGetId(array(
        'CompanyNum' => $CompanyNum,
        'ClientId' => $ClientId,
        'Subject' => $Subject,
        'Text' => $Text,
        'Type' => '2',
        'Dates' => $Dates,
        'UserId' => '0',
        'Date' => $Date,
        'Time' => $Time,
        'RandomUrl' => $RandomTokenNumber
    ));

    DB::table('appnotification')->insertGetId(array(
        'CompanyNum' => $CompanyNum,
        'ClientId' => $ClientId,
        'Subject' => $Subject,
        'Text' => $TextBoostapp,
        'Dates' => $Dates,
        'UserId' => '0',
        'Type' => '3',
        'Date' => $Date,
        'Time' => $Time
    ));


}

echo 'הפקודה נשלחה בהצלחה';
