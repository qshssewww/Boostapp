<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioAct.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/LoginPushNotifications.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i');
$Date = date('Y-m-d');

$CheckTime = date('H:i');
$Date = date('Y-m-d');
if ($CheckTime > '00:00' && $CheckTime < '05:59') {
    $Time = '06:00:00';
} else {
    $Time = date('H:i');
}


$FirstDate = date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d'))));
$LastDate = date('Y-m-d', strtotime('+7 days', strtotime($FirstDate)));

//////////////////////////////////////////////////////////////// בדיקת מינימום בשיעור ///////////////////////////////////////////////////////
try {

    $GetClassMinimums = DB::table('classstudio_date')
        ->where('Status', '=', 0)
        ->whereBetween('StartDate', array($FirstDate, $LastDate))
        ->where('MinClass', '=', '1')
        ->where('MinClassStatus', '=', '0')
        ->get();

    foreach ($GetClassMinimums as $GetClassMinimum) {


        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {

            $ClassTimeTypeCheck = $GetClassMinimum->ClassTimeTypeCheck;
            $ClassTimeCheck = $GetClassMinimum->ClassTimeCheck;
            $MinClassNum = $GetClassMinimum->MinClassNum;
            $StartDate = $GetClassMinimum->StartDate;


            $ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->first();
            $CancelMinimum = @$ClassSettingsInfo->CancelMinimum;

            if ($CancelMinimum == '') {
                $CancelMinimum = '0';
            }

            $StartTimeTrue = $StartDate . ' ' . $GetClassMinimum->StartTime;

            if ($ClassTimeTypeCheck == '1') { /// דקות
                $ItemsMin = '-' . $ClassTimeCheck . ' minutes';
                $ClassTimeCheck = date("Y-m-d H:i", strtotime($ItemsMin, strtotime($StartTimeTrue)));

            } else if ($ClassTimeTypeCheck == '2') { /// שעות
                $ItemsMin = '-' . $ClassTimeCheck . ' hour';
                $ClassTimeCheck = date("Y-m-d H:i", strtotime($ItemsMin, strtotime($StartTimeTrue)));
            }

            $FixClassTimeCheck = date("H:i", strtotime($ClassTimeCheck));

            $FixClassTimeCheckNew = date("H:i", strtotime('-15 minutes', strtotime(date('H:i:s'))));

            $FixClassTimeDate = date("Y-m-d", strtotime($ClassTimeCheck));

//// בדיקת תאריך השיעור
            if (($FixClassTimeDate == $ThisDate && $FixClassTimeCheck == $ThisTime) || ($FixClassTimeDate == $ThisDate && $FixClassTimeCheck >= $FixClassTimeCheckNew && $FixClassTimeCheck <= $ThisTime)) {

                $CountClient2 = DB::table('classstudio_act')->where('StatusCount', '=', '0')->where('ClassId', '=', $GetClassMinimum->id)->count();
                $CountClient3 = DB::table('classstudio_act')->where('StatusCount', '=', '3')->where('ClassId', '=', $GetClassMinimum->id)->count();

                $CountClient = $CountClient2 + $CountClient3;

                if ($CountClient < $MinClassNum) { /// בטל שיעור


                    $ReClass = '1';
                    $FinalTrueBalanceValue = '0';
                    $KnasOption = '0';
                    $KnasOptionVule = '0.00';
                    $Cards = '';
                    $WatingListSort = '0';

                    $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->first();

                    $MemberShipLimitMoney = $AppSettings->MemberShipLimitMoney;


                    if ($CancelMinimum == '1') {

                        $Item = DB::table('classstudio_date')
                            ->where('id', $GetClassMinimum->id)
                            ->where('CompanyNum', $GetClassMinimum->CompanyNum)
                            ->update(array('MinClassStatus' => '1'));

                    }


                    ////// ביטול שיעור

                    if ($CancelMinimum == '0') {

                        $Item = DB::table('classstudio_date')
                            ->where('id', $GetClassMinimum->id)
                            ->where('CompanyNum', $GetClassMinimum->CompanyNum)
                            ->update(array('Status' => '2', 'MinClassStatus' => '1', 'displayCancel' => '1'));

                        $NewStatus = '5'; /// בוטל באמצעות הסטודיו

                        $Clients = DB::table('classstudio_act')->where('ClassId', '=', $GetClassMinimum->id)->where('CompanyNum', $GetClassMinimum->CompanyNum)->where('StatusCount', '=', '0')->get();
                        foreach ($Clients as $Client) {

                            $ClientBalanceValue = DB::table('client_activities')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->where('id', '=', $Client->ClientActivitiesId)->first();
                            $TrueBalanceValue = $ClientBalanceValue->TrueBalanceValue;
                            $OrigenalBalanceValue = $ClientBalanceValue->BalanceValue;

                            /// בדיקת סטטוס הלקוח
                            $CheckOldStatus = DB::table('class_status')->where('id', '=', $Client->Status)->first();
                            $CheckNewStatus = DB::table('class_status')->where('id', '=', $NewStatus)->first();

                            $StatusCount = $CheckNewStatus->StatusCount;

                            /// מנוי תקופתי
                            if ($Client->Department == '1') {

                                if ($NewStatus == '4' || $NewStatus == '8') {
                                    $KnasOption = '1';
                                    $KnasOptionVule = $MemberShipLimitMoney;
                                }


                            } /// כרטיסיה
                            elseif ($Client->Department == '2' || $Client->Department == '3') {

                                if ($CheckOldStatus->Act == '0' && $CheckNewStatus->Act == '0') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                } elseif ($CheckOldStatus->Act == '0' && $CheckNewStatus->Act == '1') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue + 1; // מחזיר ניקוב
                                } elseif ($CheckOldStatus->Act == '0' && $CheckNewStatus->Act == '2') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue + 1; // מחזיר ניקוב
                                } elseif ($CheckOldStatus->Act == '1' && $CheckNewStatus->Act == '0') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue - 1; // מחסיר ניקוב
                                } elseif ($CheckOldStatus->Act == '1' && $CheckNewStatus->Act == '1') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                } elseif ($CheckOldStatus->Act == '1' && $CheckNewStatus->Act == '2') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                } elseif ($CheckOldStatus->Act == '2' && $CheckNewStatus->Act == '0') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue - 1; // מחסיר ניקוב
                                } elseif ($CheckOldStatus->Act == '2' && $CheckNewStatus->Act == '1') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                } elseif ($CheckOldStatus->Act == '2' && $CheckNewStatus->Act == '2') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                }


                                DB::table('client_activities')
                                    ->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)
                                    ->where('id', '=', $Client->ClientActivitiesId)
                                    ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));

                            }

                            // תיעוד שינוי סטטוס

                            $Dates = date('Y-m-d G:i:s');

                            $StatusJson = '';
                            $StatusJson .= '{"data": [';

                            if ($Client->StatusJson != '') {
                                $Loops = json_decode($Client->StatusJson, true);
                                foreach ($Loops['data'] as $key => $val) {

                                    $DatesDB = $val['Dates'];
                                    $UserIdDB = $val['UserId'];
                                    $StatusDB = $val['Status'];
                                    $StatusTitleDB = $val['StatusTitle'];
                                    $UserNameDB = $val['UserName'];

                                    $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                                }
                            }

                            $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "", "Status": "' . $NewStatus . '", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": ""}';

                            $StatusJson .= ']}';


                            //// השלמת שיעור

                            if ($NewStatus == '10') {
                                $ReClass = '2';
                            }


                            /// עדכון לסטטוס חדש
                            (new ClassStudioAct($Client->id))->update([
                                'Status' => $NewStatus,
                                'StatusJson' => $StatusJson,
                                'StatusCount' => $StatusCount,
                                'ReClass' => $ReClass,
                                'KnasOption' => $KnasOption,
                                'KnasOptionVule' => $KnasOptionVule,
                                'WatingListSort' => $WatingListSort,
                            ]);

                            /// הגדרת התראה
                            $CheckTime = date('H:i');
                            $Date = date('Y-m-d');
                            if ($CheckTime > '00:00' && $CheckTime < '05:59') {
                                $Time = '06:00:00';
                            } else {
                                $Time = date('H:i');
                            }
                            $Dates = date('Y-m-d H:i:s');

                            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->where('Type', '=', '17')->first();

                            if ($Client->TrueClientId == '0') {
                                $TrueClientId = $Client->ClientId;
                            } else {
                                $TrueClientId = $Client->TrueClientId;
                            }


                            $ClientInfo = DB::table('client')->where('id', '=', $TrueClientId)->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->first();


                            $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->first();


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
                            $ClassDate_Not = with(new DateTime($Client->ClassDate))->format('d/m/Y');
                            $ClassTime_Not = with(new DateTime($Client->ClassStartTime))->format('H:i');
                            $ClassName_Not = $Client->ClassName;
                            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName, $Template->Content);
                            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);
                            $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $ClassName_Not ?? '', $Content3);
                            $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], $ClassDate_Not ?? '', $Content4);
                            $Text = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], $ClassTime_Not ?? '', $Content5);

                            $Subject = $Template->Subject;

                            if ($TemplateStatus != '1') {
                                if ($TemplateSendOption != 'BA000') {

                                    $AddNotification = DB::table('appnotification')->insertGetId(
                                        array('CompanyNum' => $GetClassMinimum->CompanyNum, 'ClientId' => $TrueClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                                }
                            }

                            if ($Client->TrueClientId == '0') {
                                $LogTrueClientId = $Client->ClientId;
                            } else {
                                $LogTrueClientId = $Client->TrueClientId;
                            }

                            $ClientRegister = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $GetClassMinimum->id)->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->where('StatusCount', '=', '0')->count();
                            ///// Class Log
                            DB::table('boostapp.classlog')->insertGetId(
                                array('CompanyNum' => $Client->CompanyNum, 'ClassId' => $Client->ClassId, 'ClientId' => $LogTrueClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0', 'numOfClients' => $ClientRegister));


                        }  //// סיום לולאת לקוחות


                    }
                    ////// סיום ביטול שיעור ותחילת שליחת התראה בלבד

                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClassMinimum->CompanyNum)->where('Type', '=', '17')->first();
                    $SendStudioOption = $Template->SendStudioOption;
                    $TemplateStatus = $Template->Status;

                    if ($CancelMinimum == '0') {
                        $Content = lang('the_class_cron') . ' ' . Notificationcontent::REPLACE_ARR["cal_new_class_type_name"] . ' ' . Notificationcontent::REPLACE_ARR["class_date_single"] . ' ' . Notificationcontent::REPLACE_ARR["time_of_a_class"] . ' ' . lang('cancel_due_noshow_cron');
                    } else {
                        $Content = lang('the_class_cron') . ' ' . Notificationcontent::REPLACE_ARR["cal_new_class_type_name"] . ' ' . Notificationcontent::REPLACE_ARR["class_date_single"] . ' ' . Notificationcontent::REPLACE_ARR["time_of_a_class"] . ' ' . lang('notification_min_participants_cron');
                    }

                    /// עדכון תבנית הודעה
                    $ClassDate_Not = with(new DateTime($GetClassMinimum->StartDate))->format('d/m/Y');
                    $ClassTime_Not = with(new DateTime($GetClassMinimum->StartTime))->format('H:i');
                    $ClassName_Not = $GetClassMinimum->ClassName;
                    $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $ClassName_Not ?? '', $Content);
                    $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], $ClassDate_Not ?? '', $Content4);
                    $Text = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], $ClassTime_Not ?? '', $Content5);
//            $ContentTrue = $Content6;

                    $Dates = date('Y-m-d H:i:s');
//            $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                    $Subject = lang('not_reach_min_cron');

                    if ($TemplateStatus != '1') {
                        if ($SendStudioOption != 'BA000') {

                            LoginPushNotifications::sendLoginPushNotification(
                                $GetClassMinimum->CompanyNum,
                                LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_min_participants_not_reached'],
                                $Subject,
                                $Text,
                                $Date,
                                $Time
                            );

                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $GetClassMinimum->CompanyNum, 'Type' => '3', 'ClientId' => '0', 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                        }
                    }

                }


            }


        } else {

            $Item = DB::table('classstudio_date')
                ->where('id', $GetClassMinimum->id)
                ->where('CompanyNum', $GetClassMinimum->CompanyNum)
                ->update(array('MinClassStatus' => '1'));
        }
    }

    $Cron->end();

} catch (\Throwable $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($GetClassMinimum)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClassMinimum), JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


