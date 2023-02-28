<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/LoginPushNotifications.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$StatusAct = '0';
$ThisDate = date('Y-m-d');
$ThisTime = date('H:i:s');
$Date = date('Y-m-d');
$Time = date('H:i:s');
$Dates = date('Y-m-d H:i:s');
try {
    $CalendarChecks = DB::table('calendar')->where('Status', '=', '0')->where('StartDate', '=', $ThisDate)->get();

    foreach ($CalendarChecks as $CalendarCheck) {

        $CheckSettings = DB::table('settings')->select('id')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $CalendarCheck->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {

            $CompanyNum = $CalendarCheck->CompanyNum;
            $ClientId = $CalendarCheck->ClientId;
            $FixClassTimeCheckNew = date("H:i", strtotime('-30 minutes', strtotime($CalendarCheck->StartTime)));


            ///   בדיקת שעת הפעילות ובדיקת תאריך ישן

            if (($ThisDate == $CalendarCheck->StartDate && $ThisTime >= $CalendarCheck->StartTime) || $ThisDate > $CalendarCheck->StartDate) { /// הקפץ התראה

                $StatusAct = '1';
                /// הגדרת התראה
                $ContentTrue = lang('task_not_completed_cron').' '.with(new DateTime($CalendarCheck->StartDate))->format('d/m/Y').' '.lang('and_in_time_cron').' '.with(new DateTime($CalendarCheck->StartTime))->format('H:i');
                $Subject = lang('notification_not_done_task');


                $CheckNotification = DB::table('appnotification')
                    ->where('CompanyNum', '=', $CalendarCheck->CompanyNum)
                    ->where('CalId', '=', $CalendarCheck->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ((!empty($CheckNotification) && $CheckNotification->CalDate != $CalendarCheck->StartDate) || empty($CheckNotification)) {

                    LoginPushNotifications::sendLoginPushNotification(
                        $CompanyNum,
                        LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_assignment_not_handled'],
                        $Subject,
                        $ContentTrue,
                        $Date,
                        $Time
                    );

                    /// חלון התראות
                    $AddNotification = DB::table('appnotification')->insertGetId(
                        array('CompanyNum' => $CalendarCheck->CompanyNum,
                            'ClientId' => $CalendarCheck->ClientId,
                            'Subject' => $Subject,
                            'Text' => $ContentTrue,
                            'Dates' => $Dates,
                            'UserId' => $CalendarCheck->AgentId,
                            'Type' => '3',
                            'Date' => $Date,
                            'Time' => $Time,
                            'CalId' => $CalendarCheck->id,
                            'CalDate' => $CalendarCheck->StartDate
                        ));

                }
                //// עדכון פייפליין

                if (!empty($CalendarCheck->PipeLineId) && $CalendarCheck->PipeLineId != 0) {
                    DB::table('pipeline')
                        ->where('id', $CalendarCheck->PipeLineId)
                        ->where('CompanyNum', $CalendarCheck->CompanyNum)
                        ->update(array('Status' => 1));
                }


            } else if ($ThisDate == $CalendarCheck->StartDate && $FixClassTimeCheckNew <= $ThisTime && $CalendarCheck->ReminderStatus == '0') {


                $ContentTrue = lang('date_task_notification_today').' '.with(new DateTime($CalendarCheck->StartDate))->format('d/m/Y').' '.lang('and_in_time_cron').' '.with(new DateTime($CalendarCheck->StartTime))->format('H:i');
                $Subject = lang('task_notification_today_cron');


                $CheckNotification = DB::table('appnotification')->where('CompanyNum', '=', $CalendarCheck->CompanyNum)->where('CalId', '=', $CalendarCheck->id)->orderBy('id', 'DESC')->first();

                if (($CheckNotification && $CheckNotification->CalDate && $CheckNotification->CalDate != $CalendarCheck->StartDate) || empty($CheckNotification)) {

                    LoginPushNotifications::sendLoginPushNotification(
                        $CompanyNum,
                        LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_today_assignment_reminder'],
                        $Subject,
                        $ContentTrue,
                        $Date,
                        $Time
                    );

                    /// חלון התראות
                    $AddNotification = DB::table('appnotification')->insertGetId(
                        array('CompanyNum' => $CalendarCheck->CompanyNum,
                            'ClientId' => $CalendarCheck->ClientId,
                            'Subject' => $Subject,
                            'Text' => $ContentTrue,
                            'Dates' => $Dates,
                            'UserId' => $CalendarCheck->AgentId,
                            'Type' => '3',
                            'Date' => $Date,
                            'Time' => $Time,
                            'CalId' => $CalendarCheck->id,
                            'CalDate' => $CalendarCheck->StartDate
                        ));

                }


            }

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


        } else {

            DB::table('pipeline')
                ->where('CompanyNum', $CalendarCheck->CompanyNum)
                ->where('ClientId', $CalendarCheck->ClientId)
                ->update(array('Status' => '1'));

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
