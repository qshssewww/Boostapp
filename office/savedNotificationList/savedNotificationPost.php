<?php
require_once '../../app/init.php';
require_once '../Classes/AppSettings.php';

if (!Auth::check()) return;

$companyNum       = Auth::user()->CompanyNum;
$classAppSettings = new AppSettings();
$companySettings  = $classAppSettings::getByCompanyNum($companyNum);

$sendSMS           = $_POST['sendSMS']          ?? $classAppSettings->SendSMS;             // 1 - Y or 2 - N
$classWeek         = $_POST['classWeek']        ?? $classAppSettings->ClassWeek;           // 1 - Y or 2 - N
$classWeekMonth    = $_POST['classWeekMonth']   ?? $classAppSettings->ClassWeekMonth;      // 1 - 12
$sendNotification  = $_POST['sendNotification'] ?? $classAppSettings->SendNotification;    // 0 - N / 1 - Y
$waitingListNight  = $_POST['waitingListNight'] ?? $classAppSettings->WatingListNight;     // 0 - N / 1 - Y
$fromTime          = $_POST['fromTime']         ?? $classAppSettings->WatingListStartTime; // hh:mm:ss
$toTime            = $_POST['toTime']           ?? $classAppSettings->WatingListEndTime;   // hh:mm:ss

$companySettings->SendSMS             = $sendSMS;
$companySettings->ClassWeek           = $classWeek;
$companySettings->ClassWeekMonth      = $classWeekMonth;
$companySettings->SendNotification    = $sendNotification;
$companySettings->WatingListNight     = $waitingListNight;
$companySettings->WatingListStartTime = $fromTime;
$companySettings->WatingListEndTime   = $toTime;

$companySettings->save();

