<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/AppNotification.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ItemRoles.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/ClassStudioAct.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/AppSettings.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

$classLogIds = [];

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

//////////////////////////////////////////////////////////////// המתנה לתגובת רשימת המתנה ///////////////////////////////////////////////////////
try {


    $GetClientWatings = DB::table('classstudio_act')
        ->where('Status', 17)
        ->where('TimeAutoWatinglistDate', '<=', $ThisDate)
        ->where('TimeAutoWatinglist', '<=', $ThisTime)
        ->where('StatusTimeAutoWatinglist', 1)
        ->get();
    $BrackStatus = '0';
    foreach ($GetClientWatings as $GetClientWating) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientWating->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {
            $Dates = date('Y-m-d H:i:s');
            $NewStatus = '14';
            $CheckNewStatus = DB::table('class_status')->where('id', '=', '14')->first();
            $CompanyNum = $GetClientWating->CompanyNum;

            $CheckDeviceId = $GetClientWating->DeviceId;

            if ($CheckDeviceId != '0') {
                $CancelDeviceInfo = DB::table('boostapp.numberssub')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $CheckDeviceId)->first();
                $OldDeviceTitle = @$CancelDeviceInfo->Name;
            }

            $StatusJson = '{"data": [';

            if ($GetClientWating->StatusJson != '') {
                $Loops = json_decode($GetClientWating->StatusJson, true);
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

            (new ClassStudioAct($GetClientWating->id))->update([
                'Status' => '14',
                'StatusCount' => '2',
                'StatusTimeAutoWatinglist' => '2',
                'StatusJson' => $StatusJson,
            ]);

            if ($GetClientWating->TrueClientId == '0') {
                $TrueClientId = $GetClientWating->ClientId;
            } else {
                $TrueClientId = $GetClientWating->TrueClientId;
            }


            DB::table('appnotification')
                ->where('ClassId', '=', $GetClientWating->id)
                ->where('ClientId', '=', $GetClientWating->FixClientId)
                ->where('CompanyNum', '=', $GetClientWating->CompanyNum)
                ->update(array('ClassIdStatus' => '1'));


            //  שליחת התראת פיפסוס
            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=', $GetClientWating->CompanyNum)->where('Type', '=', '27')->first();

            if ($Template->Status != '1' && $Template->SendOption != 'BA000') {
                $Type = AppNotification::getTypeByTemplateSendOption($Template->SendOption);

                $SendTime = AppSettings::checkSendTimeByCompanyNum($GetClientWating->CompanyNum);
                $Time = date("H:i:s", $SendTime);
                $Date = date("Y-m-d", $SendTime);
                $Dates = $Date . " " . $Time;

                $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetClientWating->FixClientId)->where('CompanyNum', '=', $GetClientWating->CompanyNum)->first();

                $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $GetClientWating->CompanyNum)->first();

                /// עדכון תבנית הודעה
                $ClassDate_Not = with(new DateTime($GetClientWating->ClassDate))->format('d/m/Y');
                $ClassTime_Not = with(new DateTime($GetClientWating->ClassStartTime))->format('H:i');
                $ClassName_Not = $GetClientWating->ClassName;
                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName ?? '', $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);
                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $ClassName_Not ?? '', $Content3);
                $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], $ClassDate_Not ?? '', $Content4);
                $Text = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], $ClassTime_Not ?? '', $Content5);

                AppNotification::insertGetId([
                    'CompanyNum' => $GetClientWating->CompanyNum,
                    'ClientId' => $GetClientWating->FixClientId,
                    'Subject' => $Template->Subject,
                    'Text' => $Text,
                    'Dates' => $Dates,
                    'UserId' => '0',
                    'Type' => $Type,
                    'Date' => $Date,
                    'Time' => $Time,
                    'ClassId' => $GetClientWating->ClassId,
                    'priority' => '1',
                ]);
            }
            ///// Class Log
            $classLogIds[] = DB::table('boostapp.classlog')->insertGetId(
                array('CompanyNum' => $GetClientWating->CompanyNum, 'ClassId' => $GetClientWating->ClassId, 'ClientId' => $GetClientWating->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0'));
            /////////////////////////////////////////       
            ///// שלח התראה ללקוח הבא ברשימת ההמתנה
            $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $GetClientWating->CompanyNum)->first();

            $MorningTime = $AppSettings->MorningTime ?? '12:00:00'; // שיעורי בוקר
            $EveningTime = $AppSettings->EveningTime ?? '16:00:00'; // שיעורי ערב

            $ClassId = $GetClientWating->ClassId;

            //////////////////////////  בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////
            $FreeWatingList = $AppSettings->FreeWatingList;
            $Watinglist = $AppSettings->Watinglist; // בדיקת שיבוץ אוטומטי
            $WatinglistEndMin = $AppSettings->WatinglistEndMin; // זמן ביטחון לפני תחילת שיעור

            $WatingListAct = '0';
            $WatingListActTrue = '0';
            $ChooseClass = '0';

            //// בדיקת מצב שיעור
            $ClassInfo = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->first();
            $MaxClient = $ClassInfo->MaxClient;
            $CountMaxClient = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();

            if ($CountMaxClient >= $MaxClient || $ClassInfo->StartDate < date('Y-m-d') || ($ClassInfo->StartDate == date('Y-m-d') && $ClassInfo->StartTime <= date('H:i:s'))) {
            } else {

                $GetWatingLists = ClassStudioAct::getWaitingListActsByClassId($ClassId, $CompanyNum);
                if (!empty($GetWatingLists)) {
                    /** @var ClassStudioAct $GetWatingList */
                    foreach ($GetWatingLists as $GetWatingList) {
                        $StatusFreeWatingList = '0';
                        $ClinetIdWatingList = $GetWatingList->ClientId;
                        $TrueClinetIdWatingList = $GetWatingList->TrueClientId;
                        $WatingListActDevice = '1';
                        $Day = '0';
                        $Week = '0';
                        $Month = '0';
                        $Morning = '0';
                        $Evening = '0';

                        if ($CheckDeviceId != '0') {
                            $NewDeviceInfo = DB::table('boostapp.numberssub')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $GetWatingList->DeviceId)->first();
                            if($NewDeviceInfo) {
                                $DeviceInfoUnique = DB::table('boostapp.numbers')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $NewDeviceInfo->NumbersId)->first();
                                $NewDeviceTitle = $NewDeviceInfo->Name ?? "";

                                if ($OldDeviceTitle != $NewDeviceTitle && $DeviceInfoUnique && $DeviceInfoUnique->Unique == 1) { /// מידה לא תואמת
                                    $WatingListActDevice = '0';
                                }
                            }
                        }


                        $ActivityInfo = DB::table('boostapp.client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $GetWatingList->ClientActivitiesId)->first();
                        $LimitMultiActivity = @$ActivityInfo->LimitMultiActivity;

                        $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;
                        if ($ActivityInfo->KevaAction == 1 && $ActivityInfo->TrueDate != '') {
                            $MemberShipInfoTrueDate = date('Y-m-d', strtotime($MemberShipInfoTrueDate . ' + ' . $AppSettings->KevaDays . ' days'));
                        }
                        if ($ActivityInfo->FirstDateStatus == 1) {
                            $MemberShipInfoTrueDate = '2040-01-01';
                        }


                        if ($ActivityInfo->Department == 1 && $MemberShipInfoTrueDate > date('Y-m-d')) { /// שבץ לקוח
                            $WatingListActTrue = '1';
                            $WatingListAct = '1';
                        }

                        if (($ActivityInfo->Department == 2 && $ActivityInfo->TrueBalanceValue >= 1) || ($ActivityInfo->Department == 3 && $ActivityInfo->TrueBalanceValue >= 1)) { /// שבץ לקוח
                            $WatingListActTrue = '1';
                            $WatingListAct = '1';
                        }
                        //// בדיקת מגבלה יומית
                        $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType, ItemRoles::GROUP_VALUE_MAX, 'Day');
                        if (!empty($CheckItemsRoles)) {
                            foreach ($CheckItemsRoles as $CheckItemsRole) {
                                $ClassValue = $CheckItemsRole->Value;
                                $TrueClasess = $CheckItemsRole->Class;
                                $GroupId = $CheckItemsRole->GroupId;
                            }
                        } else {
                            $ClassValue = '999';
                        }

                        $MemberShip = $ActivityInfo->MemberShip;

                        $WeekNumber = date("Wo", strtotime("+1 day", strtotime($ClassInfo->StartDate)));

                        //// בדיקת מגבלות רשימת המתנה חופשית

                        if ($FreeWatingList == '1') {
                            $MemberInfo = DB::table('boostapp.items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityInfo->ItemId)->first();

                            $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType);
                            if (!empty($CheckItemsRoles)) {
                                foreach ($CheckItemsRoles as $CheckItemsRole) {
                                    $FoundMatch = '1';
                                    $Class = $CheckItemsRole->Class;
                                    $TrueClasessFinal = $CheckItemsRole->GroupId;
                                    $TrueClasess = $CheckItemsRole->Class;
                                    $Group = $CheckItemsRole->Group;
                                    $Item = $CheckItemsRole->Item;
                                    $Value = $CheckItemsRole->Value;
                                    $GroupId = $CheckItemsRole->GroupId;

                                    if ($Group == 'Max') {
                                            if ($Item == 'Day') {
                                                ////// בדיקת שיבוץ שיעור באותו היום פלוס שעה פלוס בדיקת זמן בטחון
                                                if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                                    $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                } else {
                                                    $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                }
                                                if ($CountLimitClassDay >= $Value) {
                                                    $Day = '1';
                                                }
                                            } else if ($Item == 'Week') {
                                                if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                                    $CountLimitClassWeek = DB::table('boostapp.classstudio_act')
                                                        ->where('ClientId', '=', $ClinetIdWatingList)
                                                        ->where('TrueClientId', '=', '0')
                                                        ->where('WeekNumber', '=', $WeekNumber)
                                                        ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                                        ->where('WatingStatus', '=', '0')
                                                        ->where('CompanyNum', '=', $CompanyNum)
                                                        ->where('MemberShip', '=', $MemberShip)
                                                        ->where('TrueClasess', $GroupId)
                                                        ->count();
                                                } else {
                                                    $CountLimitClassWeek = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                }

                                                /// בדיקת מגבלה שבועית
                                                if ($CountLimitClassWeek >= $Value) {
                                                    $Week = '1';
                                                }
                                            } else if ($Item == 'Month') {
                                                if ($MemberInfo->LimitType == '0' || $ActivityInfo->TrueDate == '') {
                                                    $ClassDateStart = date('Y-m-01', strtotime($ClassInfo->StartDate));
                                                    $ClassDateEnd = date('Y-m-t', strtotime($ClassInfo->StartDate));
                                                } else { //// תחילת חישוב תוקף מנוי לבדיקת מגבלה חודשית
                                                    $LimitType_StartDate = $ActivityInfo->StartDate;
                                                    $LimitType_EndDate = $MemberShipInfoTrueDate;
                                                    $LimitType_TodayDate = date('Y-m', strtotime($ClassInfo->StartDate));
                                                    $LimitType_ThisMonth = date('m', strtotime($ClassInfo->StartDate));
                                                    $LimitType_ThisYear = date('Y', strtotime($ClassInfo->StartDate));
                                                    $LimitType_ThisDate = $ClassInfo->StartDate;
                                                    $LimitType_StartMonth = date('m', strtotime($LimitType_StartDate));
                                                    $LimitType_EndMonth = date('m', strtotime($LimitType_EndDate));
                                                    $LimitType_ThisDateStart = date('d', strtotime($LimitType_StartDate));
                                                    $LimitType_ThisDateEnd = date('d', strtotime($LimitType_EndDate));
                                                    $LimitType_ThisYearStart = date('Y', strtotime($LimitType_StartDate));
                                                    $LimitType_ThisYearEnd = date('Y', strtotime($LimitType_EndDate));


                                                    if ($LimitType_ThisMonth == $LimitType_StartMonth) {
                                                        $ClassDateStart = $LimitType_StartDate;
                                                        $Limit_CheckDate = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))) . '-' . $LimitType_ThisDateStart;
                                                        if ($Limit_CheckDate >= $LimitType_EndDate) {
                                                            $ClassDateEnd = $LimitType_EndDate;
                                                        } else {
                                                            $ClassDateEnd = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))) . '-' . $LimitType_ThisDateStart;
                                                        }
                                                    } else if ($LimitType_ThisMonth > $LimitType_StartMonth && $LimitType_ThisMonth < $LimitType_EndMonth) {

                                                        $ThisDateStartFix = date('d', strtotime('+1 day', strtotime($LimitType_StartDate)));
                                                        $ClassDateStart = $LimitType_ThisYear . '-' . $LimitType_ThisMonth . '-' . $ThisDateStartFix;
                                                        if ($ClassDateStart > $LimitType_ThisDate) {
                                                            $ClassDateStart = $LimitType_StartDate;
                                                        }
                                                        $Limit_CheckDate = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))) . '-' . $LimitType_ThisDateStart;
                                                        if ($Limit_CheckDate >= $LimitType_EndDate) {
                                                            $ClassDateEnd = $LimitType_EndDate;
                                                        } else {
                                                            $ClassDateEnd = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))) . '-' . $LimitType_ThisDateStart;
                                                        }
                                                    } else if ($LimitType_ThisMonth == $LimitType_EndMonth) {
                                                        $ThisDateStartFix = date('d', strtotime('+1 day', strtotime($LimitType_StartDate)));
                                                        $ClassDateStart = $LimitType_ThisYear . '-' . $LimitType_ThisMonth . '-' . $ThisDateStartFix;
                                                        if ($ClassDateStart > $LimitType_ThisDate) {
                                                            $ClassDateStart = $LimitType_StartDate;
                                                        }
                                                        $ClassDateEnd = $LimitType_ThisYearEnd . '-' . $LimitType_EndMonth . '-' . $LimitType_ThisDateEnd;
                                                    } else {
                                                        $ClassDateStart = $LimitType_StartDate;
                                                        $ClassDateEnd = $LimitType_EndDate;
                                                    }

                                                } //// סיום חישוב תוקף מנוי לבדיקת מגבלה חודשית


                                                if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                                    $CountLimitClassMonth = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                } else {
                                                    $CountLimitClassMonth = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                }

                                                /// בדיקת מגבלה שבועית
                                                if ($CountLimitClassMonth >= $Value) {
                                                    $Month = '1';
                                                }
                                            } elseif ($Item == 'Morning') {
                                                if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                                    $CountLimitClassMorning = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '<=', $MorningTime)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                } else {
                                                    $CountLimitClassMorning = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '<=', $MorningTime)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                }

                                                /// בדיקת מגבלה שבועית
                                                if ($CountLimitClassMorning >= $Value) {
                                                    $Morning = '1';
                                                }
                                            } elseif ($Item == 'Evening') {
                                                if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                                    $CountLimitClassEvening = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '>=', $EveningTime)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                } else {
                                                    $CountLimitClassEvening = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '>=', $EveningTime)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                                }

                                                /// בדיקת מגבלה שבועית
                                                if ($CountLimitClassEvening >= $Value) {
                                                    $Evening = '1';
                                                }
                                            }
                                    }/// סיום סוויטש
                                } /// סיום לולאה
                            } /// לולאה ריקה
                        } ////// סיום בדיקת מגבלות רשימת המתנה חופשית

                        $TrueWatingLimit = $Day . ',' . $Week . ',' . $Month . ',' . $Morning . ',' . $Evening;
                        if ($Day == '1' || $Week == '1' || $Month == '1' || $Morning == '1' || $Evening == '1') {
                            $StatusFreeWatingList = '1';
                            $ChooseClass = 1;
                        }


                        $GroupId = '';
                        $TrueClasess = '';
                        //// סיום בדיקת מגבלה יומית

                        $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType, ItemRoles::GROUP_VALUE_CLASS);
                        if (!empty($CheckItemsRoles)) {
                            foreach ($CheckItemsRoles as $CheckItemsRole) {
                                $TrueClasess = $CheckItemsRole->Class;
                                $GroupId = $CheckItemsRole->GroupId;
                            }
                        }


                        $WatinglistOrder = $AppSettings->WatinglistOrder; /// אפשר להרשם להמתנה פלוס שיעור לאותו היום

                        //// בדיקה אם הלקוח כבר משובץ לשיעור אחר לאותו יום השיעור
                        if ($GetWatingList->TrueClientId == '0') {
                            /// בדיקת הזמנה כפולה לאותו יום השיעור
                            $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                            $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                        } else {
                            $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClinetIdWatingList)->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                            $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClinetIdWatingList)->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                        }


                        $Total_CheckClientRegister = $CheckClientRegister + $CountLimitClassWatingToday;

                        if ($WatinglistOrder == '1' && $CheckClientRegister >= $ClassValue) { /// לבחור שיעור
                            $WatingListAct = '1';
                            $WatingListActTrue = '1';
                            $ChooseClass = '1';
                        }

                        if (($ActivityInfo->Department == '2' && $ActivityInfo->TrueDate != '' && $MemberShipInfoTrueDate < date('Y-m-d')) || ($ActivityInfo->Department == '3' && $ActivityInfo->TrueDate != '' && $MemberShipInfoTrueDate < date('Y-m-d'))) { /// לא משבץ לקוח
                            $WatingListActTrue = '0';
                            $WatingListAct = '0';
                        }


                        //// שיבוץ לקוח מתאים מרשימת המתנה
                        $ThisTimeDate = date('Y-m-d H:i:s');
                        $ClassFixDate = $GetClientWating->ClassDate . ' ' . $GetClientWating->ClassStartTime;
                        $ClassFixDateNew = date("Y-m-d H:i:s", strtotime('-' . $WatinglistEndMin . ' minutes', strtotime($ClassFixDate)));

                        if (($WatingListAct == '1' && $Watinglist == '1' && $ChooseClass == '0' && $WatingListActDevice == '1' && $StatusFreeWatingList != '1' && $WatingListActTrue == '1' && $ThisTimeDate < $ClassFixDateNew) || ($WatingListAct == '1' && $Watinglist == '1' && $ChooseClass == '0' && $ThisTimeDate < $ClassFixDateNew && $WatingListActDevice == '1' && $StatusFreeWatingList != '1' && $WatingListActTrue == '1')) { /// שיבוץ אוטומטי

                            //// עדכון סטטוס
                            $NewStatus = '15'; // מומש מרשימת המתנה

                            /// בדיקת סטטוס הלקוח
                            $CheckOldStatus = DB::table('class_status')->where('id', '=', $GetWatingList->Status)->first();
                            $CheckNewStatus = DB::table('class_status')->where('id', '=', $NewStatus)->first();

                            $StatusCount = $CheckNewStatus->StatusCount;

                            // תיעוד שינוי סטטוס

                            $Dates = date('Y-m-d H:i:s');

                            $StatusJson = '{"data": [';

                            if ($GetWatingList->StatusJson != '') {
                                $Loops = json_decode($GetWatingList->StatusJson, true);
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

                            $GetWatingList->update([
                                'Status' => $NewStatus,
                                'StatusJson' => $StatusJson,
                                'StatusCount' => $StatusCount,
                                'DeviceId' => $CheckDeviceId,
                            ]);

                            $TrueBalanceValue = $ActivityInfo->TrueBalanceValue;

                            if ($ActivityInfo->Department == '2' || $ActivityInfo->Department == '3') {
                                ////  ניקוב כרטיסיה
                                if ($CheckOldStatus->Act == '0' && $CheckNewStatus->Act != '0') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue + 1; // מחזיר ניקוב
                                } elseif ($CheckOldStatus->Act != '0' && $CheckNewStatus->Act == '0') {
                                    $FinalTrueBalanceValue = $TrueBalanceValue - 1; // מחסיר ניקוב
                                } else {
                                    $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                                }


                                DB::table('client_activities')
                                    ->where('CompanyNum', '=', $CompanyNum)
                                    ->where('id', '=', $GetWatingList->ClientActivitiesId)
                                    ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));
                            }

                            // שליחת התראה ללקוח
                            $SendTime = $SendTime ?? AppSettings::checkSendTimeByCompanyNum($GetWatingList->CompanyNum);
                            $Time = date("H:i:s", $SendTime);
                            $Date = date("Y-m-d", $SendTime);
                            $Dates = $Date . " " . $Time;

                            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=', $GetWatingList->CompanyNum)->where('Type', '=', '3')->first();

                            if ($Template->Status != '1' && $Template->SendOption != 'BA000') {
                                $Type = AppNotification::getTypeByTemplateSendOption($Template->SendOption);

                                if ($GetWatingList->TrueClientId == '0') {
                                    $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->ClientId)->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();
                                } else {
                                    $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->TrueClientId)->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();
                                }

                                $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();

                                /// עדכון תבנית הודעה
                                $ClassDate_Not = with(new DateTime($GetWatingList->ClassDate))->format('d/m/Y');
                                $ClassTime_Not = with(new DateTime($GetWatingList->ClassStartTime))->format('H:i');
                                $ClassName_Not = $GetWatingList->ClassName;
                                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName, $Template->Content);
                                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);
                                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $ClassName_Not ?? '', $Content3);
                                $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], $ClassDate_Not ?? '', $Content4);
                                $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], $ClassTime_Not ?? '', $Content5);

                                AppNotification::insertGetId([
                                    'CompanyNum' => $CompanyNum,
                                    'ClientId' => $GetWatingList->TrueClientId == '0' ? $GetWatingList->ClientId : $GetWatingList->TrueClientId,
                                    'TrueClientId' => '0',
                                    'Subject' => $Template->Subject,
                                    'Text' => $TextNotification,
                                    'Dates' => $Dates,
                                    'UserId' => '0',
                                    'Type' => $Type,
                                    'Date' => $Date,
                                    'Time' => $Time,
                                    'ChooseClass' => '2',
                                    'ClassId' => $GetWatingList->id,
                                    'priority' => 1,
                                ]);
                            }

                            //// קליטת לוג מערכת
                            $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->first();
                            $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');

                            $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
                            $LogClassName = $ClassInfo->ClassName;

                            $LogText = lang('booked_from_waitlist_cron').' '.$LogClassName.' '.lang('in_date_cron').' '.$LogClassDate.' '.lang('and_in_time_cron').' '.$LogClassTime;

                            $InsertLog = DB::table('log')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText));

                            ///// Class Log
                            $classLogIds[] = DB::table('boostapp.classlog')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $GetWatingList->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0'));
                            /////////////////////////////////////////


                            //// סיום קליטת לוג מערכת

                            $BrackStatus = '1';
                        } elseif (($WatingListAct == '1' && $Watinglist == '2' && $WatingListActDevice == '1' && $WatingListActTrue == '1') ||
                            ($WatingListAct == '1' && $ChooseClass == '1' && $WatingListActDevice == '1' && $WatingListActTrue == '1') ||
                            ($WatingListAct == '1' && $Watinglist == '1' && $WatingListActDevice == '1' && $WatingListActTrue == '1')) { /// שליחת התראת שיבוץ והמתנה לתגובת הלקוח


                            if ($GetWatingList->TrueClientId == '0') {
                                $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->ClientId)->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();
                            } else {
                                $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->TrueClientId)->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();
                            }

                            $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();


                            $DisplayName = htmlentities(@$ClientInfo->CompanyName);


                            //// עדכון סטטוס
                            $NewStatus = '17'; // מומש מרשימת המתנה

                            /// בדיקת סטטוס הלקוח
                            $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();

                            $StatusCount = $CheckNewStatus->StatusCount;

                            // תיעוד שינוי סטטוס

                            $Dates = date('Y-m-d H:i:s');

                            $StatusJson = '{"data": [';

                            if ($GetWatingList->StatusJson != '') {
                                $Loops = json_decode($GetWatingList->StatusJson, true);
                                foreach ($Loops['data'] as $key => $val) {

                                    $DatesDB = $val['Dates'];
                                    $UserIdDB = $val['UserId'];
                                    $StatusDB = $val['Status'];
                                    $StatusTitleDB = $val['StatusTitle'];
                                    $UserNameDB = htmlentities($val['UserName']);

                                    $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                                }
                            }

                            $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "", "Status": "' . $NewStatus . '", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": ""}';

                            $StatusJson .= ']}';

                            // שליחת התראה ללקוח
                            $NextSendTime = AppNotification::sendWaitingListFree($GetWatingList, $ChooseClass, $StatusFreeWatingList, $TrueWatingLimit, $SendTime ?? null);

                            $GetWatingList->update([
                                'Status' => $NewStatus,
                                'StatusJson' => $StatusJson,
                                'StatusCount' => $StatusCount,
                                'TimeAutoWatinglist' => date("H:i:s", $NextSendTime),
                                'TimeAutoWatinglistDate' => date("Y-m-d", $NextSendTime),
                                'StatusTimeAutoWatinglist' => '1',
                                'DeviceId' => $CheckDeviceId,
                                'FreeWatingList' => $FreeWatingList,
                            ]);

            //// קליטת לוג מערכת
            $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ClassId)->first();
            $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
            $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
            $LogClassName = $ClassInfo->ClassName;

            $LogText = lang('waitlist_notification_sent_cron').' '.$LogClassName.' '.lang('in_date_cron').' '.$LogClassDate.' '.lang('and_in_time_cron').' '.$LogClassTime;

            $InsertLog = DB::table('log')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText) );   
               
                            // שליחת התראה לסטודיו בדואר אלקטרוני
                            if ($AppSettings->SendNotification == '1') {
                                $TextNotification = lang('waitlist_notification_sent_cron') . ' ' . $LogClassName . ' ' . lang('in_date_cron') . ' ' . $LogClassDate . ' ' . lang('and_in_time_cron') . ' ' . $LogClassTime;
                                $Subject = @$DisplayName . ' ' . lang('class_waiting_cron');
                                $Date = date('Y-m-d');
                                $Time = date('H:i:s');
                                $Dates = date('Y-m-d H:i:s');
                                if ($GetWatingList->TrueClientId == '0') {
                                    DB::table('boostapp.appnotification')->insertGetId(
                                        array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'TrueClientId' => '0', 'Type' => '4', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'ClassId' => '0'));
                                } else {
                                    DB::table('boostapp.appnotification')->insertGetId(
                                        array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->TrueClientId, 'TrueClientId' => '0', 'Type' => '4', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'ClassId' => '0'));
                                }
                            }

                            ///// Class Log
                            $classLogIds[] = DB::table('boostapp.classlog')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $GetWatingList->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0'));
                            /////////////////////////////////////////

                            //// סיום קליטת לוג מערכת
                            $BrackStatus = '1';
                        } else {

                            //// עדכון סטטוס
                            $NewStatus = '20'; // בוטל אוטומטית מרשימת המתנה

                            /// בדיקת סטטוס הלקוח
                            $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();

                            $StatusCount = $CheckNewStatus->StatusCount;

                            // תיעוד שינוי סטטוס

                            $Dates = date('Y-m-d H:i:s');

                            $StatusJson = '{"data": [';

                            if ($GetWatingList->StatusJson != '') {
                                $Loops = json_decode($GetWatingList->StatusJson, true);
                                foreach ($Loops['data'] as $key => $val) {

                                    $DatesDB = $val['Dates'];
                                    $UserIdDB = $val['UserId'];
                                    $StatusDB = $val['Status'];
                                    $StatusTitleDB = $val['StatusTitle'];
                                    $UserNameDB = htmlentities($val['UserName']);

                                    $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                                }
                            }

                            $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "", "Status": "' . $NewStatus . '", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": ""}';

                            $StatusJson .= ']}';

                            $GetWatingList->update([
                                'Status' => $NewStatus,
                                'StatusJson' => $StatusJson,
                                'StatusCount' => $StatusCount,
                            ]);

                            ///// Class Log
                            $classLogIds[] = DB::table('boostapp.classlog')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $GetWatingList->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0'));
                            /////////////////////////////////////////

                            $BrackStatus = '0';

                        }

                        if ($BrackStatus == '1') {
                            break;
                        }
                    } ////  סיום לולאה רשימת המתנה
                }
            }

            //// עדכון שיעור ברשימת משתתפים
            $ClientRegister = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
            $WatingListNum = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();

            foreach ($classLogIds as $classLogId){
                DB::table('boostapp.classlog')->where('id', $classLogId)->update(['numOfClients' => $ClientRegister]);
            }

            DB::table('boostapp.classstudio_date')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('id', '=', $ClassId)
                ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingListNum));

            //////////////////////////  סיום בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////


        } else {
            (new ClassStudioAct($GetClientWating->id))->update([
                'Status' => '14',
                'StatusCount' => '2',
                'StatusTimeAutoWatinglist' => '2',
            ]);

            DB::table('appnotification')
                ->where('ClassId', '=', $GetClientWating->id)
                ->where('ClientId', '=', $GetClientWating->ClientId)
                ->where('CompanyNum', '=', $GetClientWating->CompanyNum)
                ->update(array('ClassIdStatus' => '1'));

        }


    }

    DB::table('boostapp.watinglistlog')->insertGetId(
        array('Dates' => date('Y-m-d H:i:s')));

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClientWating)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientWating),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
