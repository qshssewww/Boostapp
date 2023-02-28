<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/AppNotification.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/EncryptDecrypt.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/Utils.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/ClassOnline.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/Notificationcontent.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/WhatsAppNotifications.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/office/Classes/ClassStudioAct.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/app/enums/NotificationContent/SendOption.php";

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

//require $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';

set_time_limit(0);
ini_set("memory_limit", "-1");

//////////////////////////////////////////////////////////////// שליחת תזכורת ללקוח ///////////////////////////////////////////////////////
try {
    $GetClientReminders = ClassStudioAct::getOneMinReminderList();

    /** @var ClassStudioAct $GetClientReminder */
    foreach ($GetClientReminders as $GetClientReminder) {
        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClientReminder->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {
            $Date = $GetClientReminder->ReminderDate;
            $Time = $GetClientReminder->ReminderTime;

            if ($GetClientReminder->ClassDate == date('Y-m-d') && $Time < date('H:i:s')) {
                $Time = date('H:i:s');
            }

            $Dates = date('Y-m-d H:i:s');

            if ($GetClientReminder->TrueClientId == '0') {
                $TrueClientId = $GetClientReminder->ClientId;
            } else {
                $TrueClientId = $GetClientReminder->TrueClientId;
            }

            $notificationTemplateType = 11;
            //// תבנית מזל טוב
            $Template = DB::table('notificationcontent')
                ->where('CompanyNum', '=', $GetClientReminder->CompanyNum)
                ->where('Type', '=', $notificationTemplateType)
                ->first();

            $ClientInfo = DB::table('client')
                ->where('id', '=', $TrueClientId)
                ->where('CompanyNum', '=', $GetClientReminder->CompanyNum)
                ->first();

            $ClassInfo = DB::table('classstudio_date')->where('id', '=', $GetClientReminder->ClassId)->where('CompanyNum', '=', $GetClientReminder->CompanyNum)->first();
            $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientReminder->CompanyNum)->first();
            if (!empty($ClientInfo) && !empty($ClassInfo)) {
                $TemplateStatus = $Template->Status;
                $TemplateSendOption = $Template->SendOption;
                $SendStudioOption = $Template->SendStudioOption;

                // send Push notification by default
                $Type = AppNotification::TYPE_PUSH;

                if ($TemplateSendOption != 'BA999' && $TemplateSendOption != SendOption::SEND_OPTION_WHATSAPP) {
                    $notificationChannels = explode(',', $TemplateSendOption);

                    if (in_array(AppNotification::TYPE_PUSH, $notificationChannels)) {
                        // Push notification
                        $Type = AppNotification::TYPE_PUSH;
                    } else if (in_array(AppNotification::TYPE_SMS, $notificationChannels)) {
                        // SMS
                        $Type = AppNotification::TYPE_SMS;
                    } else if (in_array(AppNotification::TYPE_EMAIL, $notificationChannels)) {
                        // Email
                        $Type = AppNotification::TYPE_EMAIL;
                    }
                }

                /// עדכון תבנית הודעה
                $start_date = date('Y-m-d H:i:s', strtotime('+' . $ClassInfo->PreparationTimeMinutes . ' minutes', strtotime($ClassInfo->start_date)));
                $ClassDate_Notification = date('d/m/Y', strtotime($start_date));
                $ClassTime_Notification = date('H:i', strtotime($start_date));
                $ClassName_Notification = $ClassInfo->ClassName;

                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName ?? '', $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);

                $Content4 = str_replace([
                    Notificationcontent::REPLACE_ARR["meeting_name"],
                    Notificationcontent::REPLACE_ARR["cal_new_class_type_name"],
                ], $ClassName_Notification ?? '', $Content3);

                $Content5 = str_replace([
                    Notificationcontent::REPLACE_ARR["class_date_single"],
                    Notificationcontent::REPLACE_ARR["date_of_meeting"],
                ], $ClassDate_Notification ?? '', $Content4);

                $ContentTrue = str_replace([
                    Notificationcontent::REPLACE_ARR["time_of_a_class"],
                    Notificationcontent::REPLACE_ARR["time_of_meeting"],
                ], $ClassTime_Notification ?? '', $Content5);

                // Alex approved delete
//                if ($ClassInfo->is_zoom_class == 1) {
//                    $encrypt = new EncryptDecrypt();
//                    $classIdEn = $encrypt->encryption($ClassInfo->id);
//                    $zoomText = lang('hi_corona_cron') . ' ' . $ClientInfo->FirstName . ",<br>";
//                    $zoomText .= lang('class_notification_cron') . ' ' . $ClassInfo->ClassName . ' ' . lang('starts_today_at') . ' ' . date("H:i", strtotime($ClassInfo->StartTime)) . ". ";
//                    $zoomText .= lang('online_class_note_cron');
//                    $zoomTextEmail = $zoomText;
//                    $zoomTextEmail .= lang('view_from_browser_cron') . " " . "https://app.boostapp.co.il/ClassPage.php?classId=" . $classIdEn;
//
//
//                    $zoomSubject = lang('online_class_notification');
//
//                    $notificationTimeZoom = strtotime("-45 minutes", strtotime($ClassInfo->StartTime));
//                    $notificationTimeZoom = date('H:i:s', $notificationTimeZoom);
//
//                    DB::table('appnotification')->insertGetId(
//                        array('CompanyNum' => $GetClientReminder->CompanyNum, 'ClientId' => $TrueClientId,
//                            'Subject' => $zoomSubject, 'Text' => $zoomText, 'Dates' => $Dates, 'UserId' => '0', 'Type' => AppNotification::TYPE_PUSH, 'Date' => $ClassInfo->StartDate, 'Time' => $notificationTimeZoom, "priority" => 1)
//                    );
//                    DB::table('appnotification')->insertGetId(
//                        array('CompanyNum' => $GetClientReminder->CompanyNum, 'ClientId' => $TrueClientId,
//                            'Subject' => $zoomSubject, 'Text' => $zoomTextEmail, 'Dates' => $Dates, 'UserId' => '0', 'Type' => AppNotification::TYPE_EMAIL, 'Date' => $ClassInfo->StartDate, 'Time' => $notificationTimeZoom, "priority" => 1)
//                    );
//                }

                $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                $Subject = @$Template->Subject;

                if ($TemplateStatus != '1') {
                    if ($TemplateSendOption != 'BA000') {
                        if (!empty($ClassInfo->liveClassLink)) {
                            if (!empty($ClassInfo->onlineClassId)) {
                                $classOnline = ClassOnline::find($ClassInfo->onlineClassId);
                                if (!empty($classOnline)) {
                                    $onlineSendType = $classOnline->getAttribute('sendType');
                                    $sendTime = $classOnline->getAttribute('sendTime');
                                    $sendTimeType = $classOnline->getAttribute('sendTimeType');
                                }
                            } else {
                                if (!empty($ClassInfo->onlineSendType)) {
                                    $onlineSendType = $ClassInfo->onlineSendType;
                                } else {
                                    $onlineSendType = '2';
                                }
                            }
                            if (!isset($sendTime)) {
                                $sendTime = 1;
                            }
                            if (!isset($sendTimeType)) {
                                $sendTimeType = 2;
                            }

                            $ContentTrue2 = $ContentTrue . "\r\n" . "כדי להכנס לשיעור יש ללחוץ על הלינק מתחת\r\n";
                            $ContentTrue2 .= '' . $ClassInfo->liveClassLink;
                            $Text2 = $ContentTrue2;

                            $sendTimeTypeText = ClassOnline::getSendTimeType($sendTimeType);
                            $notificationTime = strtotime("-$sendTime $sendTimeTypeText", strtotime($ClassInfo->StartDate.' '.$ClassInfo->StartTime));
                            $notificationTime = date('H:i:s', $notificationTime);
                            $AddNotificationLive = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $GetClientReminder->CompanyNum, 'ClientId' => $TrueClientId, 'Subject' => $Subject, 'Text' => $Text2, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $onlineSendType, 'Date' => $ClassInfo->StartDate, 'Time' => $notificationTime, 'priority' => 1)
                            );
                        }
                        $AddNotification = DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $GetClientReminder->CompanyNum, 'ClientId' => $TrueClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));

                        if ($TemplateSendOption == SendOption::SEND_OPTION_WHATSAPP && $notificationTemplateType == 11) {
                            // add WhatsApp template to DB for send
                            WhatsAppNotifications::sendClassReminder($GetClientReminder, $Date, $Time);
                        }
                    }
                }
            }
        }

        $GetClientReminder->ReminderStatus = 1;
        $GetClientReminder->save();
    }

    $Cron->end();
} catch (\Throwable $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($GetClientReminder)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientReminder), JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
