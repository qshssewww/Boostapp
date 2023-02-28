<?php

require_once __DIR__ . '/../../app/initcron.php';
require_once __DIR__ . '/../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../Classes/ClientActivities.php';
require_once __DIR__ . '/../Classes/ClassesType.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/ItemRoles.php';
require_once __DIR__ . '/../Classes/AppNotification.php';
require_once __DIR__ . '/../Classes/AppSettings.php';
require_once __DIR__.'/../Classes/Notificationcontent.php';

$ClassId = $_POST['ClassId'];
$CompanyNum = Auth::user()->CompanyNum;
$BrackStatus = 0;

/// הגדרות אפליקציה    
$AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();

$MorningTime = $AppSettings->MorningTime ?? '12:00:00'; // שיעורי בוקר
$EveningTime = $AppSettings->EveningTime ?? '16:00:00'; // שיעורי ערב

$CheckDeviceId = '0';

//////////////////////////  בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////

$Watinglist = $AppSettings->Watinglist; // בדיקת שיבוץ אוטומטי
$WatinglistEndMin = $AppSettings->WatinglistEndMin; // זמן ביטחון לפני תחילת שיעור
$WatingListAct = '0';
$ChooseClass = '0';

// בדיקת מצב שיעור
/** @var ClassStudioDate $ClassInfo */
$ClassInfo = ClassStudioDate::find($ClassId);
$LogClassDate = date('d/m/Y', strtotime($ClassInfo->StartDate));
$LogClassTime = date('H:i', strtotime($ClassInfo->StartTime));
$LogClassName = $ClassInfo->ClassName;

$WeekNumber = date("Wo", strtotime("+1 day", strtotime($ClassInfo->StartDate)));

$CountMaxClient = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
$message = '';

if ($CountMaxClient >= $ClassInfo->MaxClient) {
    $message = lang('class_has_no_space');
} else if(($ClassInfo->StartDate == date('Y-m-d') && $ClassInfo->StartTime <= date('H:i:s')) || $ClassInfo->StartDate < date('Y-m-d')) {
    $message = lang('cant_run_waiting_list_on_old_class');
} else {
    $GetWatingLists = ClassStudioAct::getWaitingListActsByClassId($ClassId, $CompanyNum);
        /** @var ClassStudioAct $GetWatingList */
        foreach ($GetWatingLists as $GetWatingList) {
            $WatingListAct = '0';
            $WatingListActTrue = '0';
            $StatusFreeWatingList = '0';
            $ChooseClass = '0';
            $ClinetIdWatingList = $GetWatingList->ClientId;
            $TrueClientId = $GetWatingList->TrueClientId;
            $WatingListActDevice = '1';
            $Day = '0';
            $Week = '0';
            $Month = '0';
            $Morning = '0';
            $Evening = '0';

            if ($CheckDeviceId != '0') {
                $NewDeviceInfo = DB::table('boostapp.numberssub')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $GetWatingList->DeviceId)->first();
                $DeviceInfoUnique = DB::table('boostapp.numbers')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $NewDeviceInfo->NumbersId)->first();
                $NewDeviceTitle = @$NewDeviceInfo->Name;

                if ($DeviceInfoUnique->Unique == '1') { /// מידה לא תואמת
                    $WatingListActDevice = '0';
                }
            }

            /** @var Client $ClientInfo */
            $ClientInfo = Client::find($GetWatingList->TrueClientId == '0' ? $GetWatingList->ClientId : $GetWatingList->TrueClientId);
            /* @var ClientActivities $ActivityInfo */
            $ActivityInfo = DB::table('boostapp.client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $GetWatingList->ClientActivitiesId)->first();
            $LimitMultiActivity = @$ActivityInfo->LimitMultiActivity;

            $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;
            if ($ActivityInfo->KevaAction == '1' && $ActivityInfo->TrueDate != '') {
                $MemberShipInfoTrueDate = date('Y-m-d', strtotime($MemberShipInfoTrueDate . ' + ' . $AppSettings->KevaDays . ' days'));
            }

            if ($ActivityInfo->FirstDateStatus == '1') {
                $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;
            }

            if ($ActivityInfo->Department == '1' && $MemberShipInfoTrueDate > date('Y-m-d')) { /// שבץ לקוח
                $WatingListActTrue = '1';
                $WatingListAct = '1';
            }

            if (($ActivityInfo->Department == '2' && $ActivityInfo->TrueBalanceValue >= '1') || ($ActivityInfo->Department == '3' && $ActivityInfo->TrueBalanceValue >= '1')) { /// שבץ לקוח
                $WatingListActTrue = '1';
                $WatingListAct = '1';
            }

            if (($ActivityInfo->Department == '2' && $ActivityInfo->TrueDate != '' && $MemberShipInfoTrueDate < date('Y-m-d')) || ($ActivityInfo->Department == '3' && $ActivityInfo->TrueDate != '' && $MemberShipInfoTrueDate < date('Y-m-d'))) { /// לא משבץ לקוח
                $WatingListActTrue = '0';
                $WatingListAct = '0';
            }


            $MemberShip = $ActivityInfo->MemberShip;

            //// בדיקת מגבלות רשימת המתנה חופשית    

            if ($AppSettings->FreeWatingList == 1) {
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
                            $classArr = explode(',', $CheckItemsRole->Class);
                            $eventCode = ClassesType::getEventTypeCode($ClassInfo->ClassNameType);
                            if (in_array($eventCode, $classArr)) {
                                $classArr = [];
                                $classTypes = DB::table('boostapp.class_type')->select('id')->where('CompanyNum', $CompanyNum)->get();
                                foreach ($classTypes as $type) {
                                    $classArr[] = $type->id;
                                }
                            }
                                if ($Item == 'Day') {
                                    ////// בדיקת שיבוץ שיעור באותו היום פלוס שעה פלוס בדיקת זמן בטחון
                                    if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                        $CountLimitClassDay = DB::table('boostapp.classstudio_act')
                                            ->where('ClientId', '=', $ClinetIdWatingList)
                                            ->where('TrueClientId', '=', '0')
                                            ->where('ClassDate', '=', $ClassInfo->StartDate)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    } else {
                                        $CountLimitClassDay = DB::table('boostapp.classstudio_act')
                                            ->where('TrueClientId', '=', $TrueClientId)
                                            ->where('ClassDate', '=', $ClassInfo->StartDate)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
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
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    } else {
                                        $CountLimitClassWeek = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                                    }

                                    /// בדיקת מגבלה שבועית
                                    if ($CountLimitClassWeek >= $Value) {
                                        $Week = '1';
                                    }
                                } else if ($Item == 'Month') {


                                    if (empty($ActivityInfo->TrueDate)) {
                                        $fromDateLimit = date('Y-m-01', strtotime($ClassInfo->StartDate));
                                        $toDateLimit = date('Y-m-t', strtotime($ClassInfo->StartDate));
                                    } else {

                                        $membershipDay = date('d', strtotime($ActivityInfo->StartDate));
                                        $classDate = date('Y-m-d', strtotime($ClassInfo->StartDate));
                                        $getClassDay = date('d', strtotime($ClassInfo->StartDate));

                                        if ($membershipDay <= $getClassDay) {
                                            $fromDateLimit = date('Y-m', strtotime($classDate)) . '-' . $membershipDay;
                                            /// todo: notice this fix - can cause problems with dates - see source on AddBooking.php
                                            $toDateLimit = date('Y-m', strtotime('+1 month', strtotime(date('Y-m', strtotime($classDate))))) . '-' . $membershipDay;
                                        } else {
                                            $fromDateLimit = date('Y-m', strtotime('-1 month', strtotime($classDate))) . '-' . $membershipDay;
                                            $toDateLimit = date('Y-m', strtotime(date('Y-m', strtotime($classDate)))) . '-' . $membershipDay;
                                        }
                                        $toDateLimit = date('Y-m-d', strtotime('-1 day', strtotime($toDateLimit)));

                                    } //// סיום חישוב תוקף מנוי לבדיקת מגבלה חודשית

                                    if (!empty($ActivityInfo->StudioVaildDate) && $ActivityInfo->StudioVaildDate < $ActivityInfo->VaildDate && strtotime("+1 month", strtotime($ActivityInfo->StartDate)) > strtotime($ActivityInfo->StudioVaildDate)) {
                                        $fromDateLimit = $ActivityInfo->StartDate;
                                        $toDateLimit = date('Y-m-d', strtotime('-1 day', strtotime($ActivityInfo->StudioVaildDate)));
                                    }

                                    if (!empty($ActivityInfo->VaildDate) && !empty($ActivityInfo->StudioVaildDate) && strtotime("+1 month", strtotime($ActivityInfo->StartDate)) == strtotime($ActivityInfo->VaildDate) && $ActivityInfo->StudioVaildDate > $ActivityInfo->VaildDate && strtotime("+2 month", strtotime($ActivityInfo->StartDate)) > strtotime($ActivityInfo->StudioVaildDate)){
                                        $fromDateLimit = $ActivityInfo->StartDate;
                                        $toDateLimit = date('Y-m-d', strtotime('-1 day', strtotime($ActivityInfo->StudioVaildDate)));
                                    }
                                    // todo: add grace days edition

//                                        if ($ActivityInfo->KevaAction == '1' && !empty($ActivityInfo->TrueDate) && $ClassInfo->StartDate > $ActivityInfo->TrueDate && !$hasKevaActive) {
//                                            $fromDateLimit = $ActivityInfo->TrueDate;
//                                            $toDateLimit = date('Y-m-d', strtotime($ActivityInfo->TrueDate . ' + ' . $KevaDays . ' days'));
//                                        }


                                    if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                        $CountLimitClassMonth = DB::table('boostapp.classstudio_act')
                                            ->where('ClientId', '=', $ClinetIdWatingList)
                                            ->where('TrueClientId', '=', '0')
                                            ->whereBetween('ClassDate', array($fromDateLimit, $toDateLimit))
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    } else {
                                        $CountLimitClassMonth = DB::table('boostapp.classstudio_act')
                                            ->where('TrueClientId', '=', $TrueClientId)
                                            ->whereBetween('ClassDate', array($fromDateLimit, $toDateLimit))
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    }

                                    /// בדיקת מגבלה שבועית
                                    if ($CountLimitClassMonth >= $Value) {
                                        $Month = '1';
                                    }
                                } else if ($Item == 'Morning') {

                                    if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                        $CountLimitClassMorning = DB::table('boostapp.classstudio_act')
                                            ->where('ClientId', '=', $ClinetIdWatingList)
                                            ->where('TrueClientId', '=', '0')
                                            ->where('WeekNumber', '=', $WeekNumber)
                                            ->where('ClassStartTime', '<=', $MorningTime)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    } else {
                                        $CountLimitClassMorning = DB::table('boostapp.classstudio_act')
                                            ->where('TrueClientId', '=', $TrueClientId)
                                            ->where('WeekNumber', '=', $WeekNumber)
                                            ->where('ClassStartTime', '<=', $MorningTime)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    }

                                    /// בדיקת מגבלה שבועית
                                    if ($CountLimitClassMorning >= $Value) {
                                        $Morning = '1';
                                    }
                                } else if ($Item == 'Evening') {

                                    if ($TrueClientId == '0' || $LimitMultiActivity == '1') {
                                        $CountLimitClassEvening = DB::table('boostapp.classstudio_act')
                                            ->where('ClientId', '=', $ClinetIdWatingList)
                                            ->where('TrueClientId', '=', '0')
                                            ->where('WeekNumber', '=', $WeekNumber)
                                            ->where('ClassStartTime', '>=', $EveningTime)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    } else {
                                        $CountLimitClassEvening = DB::table('boostapp.classstudio_act')
                                            ->where('TrueClientId', '=', $TrueClientId)
                                            ->where('WeekNumber', '=', $WeekNumber)
                                            ->where('ClassStartTime', '>=', $EveningTime)
                                            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 17, 21, 23))
                                            ->where('WatingStatus', '=', '0')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ClientActivitiesId', '=', $ActivityInfo->id)
                                            ->whereIn('ClassNameType', $classArr)
                                            ->count();
                                    }

                                    /// בדיקת מגבלה שבועית
                                    if ($CountLimitClassEvening >= $Value) {
                                        $Evening = '1';
                                    }
                                }
                        }
                    }
                }
            }

            $TrueWatingLimit = $Day . ',' . $Week . ',' . $Month . ',' . $Morning . ',' . $Evening;
            if ($Day == '1' || $Week == '1' || $Month == '1' || $Morning == '1' || $Evening == '1') {
                $StatusFreeWatingList = 1;
                $ChooseClass = 1;
            }
            //// בדיקת מגבלה יומית
            $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType, ItemRoles::GROUP_VALUE_MAX, 'Day');
            if (!empty($CheckItemsRoles)) {
                foreach ($CheckItemsRoles as $CheckItemsRole) {

                    $ClassValue = $CheckItemsRole->Value;
                    $TrueClasess = $CheckItemsRole->Class;
                    $GroupId = $CheckItemsRole->GroupId;

                    $WatinglistOrder = $AppSettings->WatinglistOrder; /// אפשר להרשם להמתנה פלוס שיעור לאותו היום       
                    //// בדיקה אם הלקוח כבר משובץ לשיעור אחר לאותו יום השיעור
                    if ($TrueClientId == '0' || $ClinetIdWatingList == $ActivityInfo->ClientId) {
                        /// בדיקת הזמנה כפולה לאותו יום השיעור 
                        $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                        $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                    } else {
                        $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                        $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
                    }


                    $Total_CheckClientRegister = $CheckClientRegister + $CountLimitClassWatingToday;

                    if ($WatinglistOrder == '1' && $CheckClientRegister >= $ClassValue) { /// לבחור שיעור
                        $WatingListAct = '1';
                        $ChooseClass = '1';
                    }
                }
            } else {
                $WatinglistOrder = $AppSettings->WatinglistOrder; /// אפשר להרשם להמתנה פלוס שיעור לאותו היום            
                $ClassValue = '999';
                $WatingListAct = '1';
                $ChooseClass = '0';
            }


            //// שיבוץ לקוח מתאים מרשימת המתנה     

            $ThisTime = date('H:i:s');
            $ThisTimeDate = date('Y-m-d H:i:s');
            $ClassFixDate = $ClassInfo->StartDate . ' ' . $ClassInfo->StartTime;
            $ClassFixDateNew = date("Y-m-d H:i:s", strtotime('-' . $WatinglistEndMin . ' minutes', strtotime($ClassFixDate)));
            $ClassTime = date("Y-m-d", strtotime('-' . $WatinglistEndMin . ' minutes', strtotime($ClassInfo->StartTime)));


            if (($WatingListAct == '1' && $Watinglist == '1' && $ChooseClass == '0' && $WatingListActDevice == '1' && $StatusFreeWatingList != '1' && $WatingListActTrue == '1' && $ThisTimeDate < $ClassFixDateNew) ||
                ($WatingListAct == '1' && $Watinglist == '1' && $ChooseClass == '0' && $ThisTimeDate < $ClassFixDateNew && $WatingListActDevice == '1' && $StatusFreeWatingList != '1' && $WatingListActTrue == '1')) { /// שיבוץ אוטומטי
                //// עדכון סטטוס   
                $NewStatus = '15'; // מומש מרשימת המתנה
                /// בדיקת סטטוס הלקוח
                $CheckOldStatus = DB::table('boostapp.class_status')->where('id', '=', $GetWatingList->Status)->first();
                $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();

                $StatusCount = $CheckNewStatus->StatusCount;

                // תיעוד שינוי סטטוס

                $Dates = date('Y-m-d G:i:s');
                $UserId = Auth::user()->id;

                $StatusJson = '';
                $StatusJson .= '{"data": [';

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

                    DB::table('boostapp.client_activities')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('id', '=', $GetWatingList->ClientActivitiesId)
                        ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));
                }

                // שליחת התראה ללקוח
                $SendTime = AppSettings::checkSendTimeByCompanyNum($GetWatingList->CompanyNum);
                $Time = date("H:i:s", $SendTime);
                $Date = date("Y-m-d", $SendTime);
                $Dates = $Date . " " . $Time;

                $Template = Notificationcontent::getByTypeAndCompanyNum($GetWatingList->CompanyNum, 3);

                if ($Template->Status != '1' && $Template->SendOption != 'BA000') {
                    $Type = AppNotification::getTypeByTemplateSendOption($Template->SendOption);

                    $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $GetWatingList->CompanyNum)->first();

                    /// עדכון תבנית הודעה
                    $Content1 = str_replace(Notificationcontent::REPLACE_ARR['studio_name'], @$CompanyInfo->AppName, $Template->Content);
                    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName, $Content1);
                    $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName, $Content2);
                    $Content4 = str_replace(Notificationcontent::REPLACE_ARR["meeting_name"], $LogClassName, $Content3);
                    $Content5 = str_replace(Notificationcontent::REPLACE_ARR["date_of_meeting"], $LogClassDate, $Content4);
                    $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["time_of_meeting"], $LogClassTime, $Content5);


                    AppNotification::insertGetId([
                        'CompanyNum' => $CompanyNum,
                        'ClientId' => $ClientInfo->id,
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
                $LogText = 'שובץ מרשימת המתנה לשיעור ' . $LogClassName . ' בתאריך ' . $LogClassDate . ' ובשעה ' . $LogClassTime;

                $InsertLog = DB::table('log')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText));

                ///// Class Log
                DB::table('boostapp.classlog')->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClassId' => $ClassId,
                    'ClientId' => $ClientInfo->id,
                    'Status' => $CheckNewStatus->Title,
                    'UserName' => '0',
                ]);
                /////////////////////////////////////////   
                //// סיום קליטת לוג מערכת     

                $BrackStatus = '1';
            } else if ($WatingListAct == '1' && $WatingListActDevice == '1' && $WatingListActTrue == '1'
                && ($Watinglist == '1' || $Watinglist == '2' || $ChooseClass == '1')) { /// שליחת התראת שיבוץ והמתנה לתגובת הלקוח
                //// עדכון סטטוס  
                $NewStatus = '17'; // מומש מרשימת המתנה
                /// בדיקת סטטוס הלקוח
                $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();

                $StatusCount = $CheckNewStatus->StatusCount;

                // תיעוד שינוי סטטוס

                $Dates = date('Y-m-d H:i:s');
                $UserId = Auth::user()->id;

                $StatusJson = '';
                $StatusJson .= '{"data": [';

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
                    'FreeWatingList' => $AppSettings->FreeWatingList,
                ]);

                //// קליטת לוג מערכת
                $LogText = 'נשלחה התראת תגובה מרשימת המתנה לשיעור ' . $LogClassName . ' בתאריך ' . $LogClassDate . ' ובשעה ' . $LogClassTime;

                $InsertLog = DB::table('log')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText));

                /// שליחת התראה לסטודיו בדואר אלקטרוני    
                if ($AppSettings->SendNotification == '1') {

                    $TextNotification = 'נשלחה התראת תגובה מרשימת המתנה לשיעור ' . $LogClassName . ' בתאריך ' . $LogClassDate . ' ובשעה ' . $LogClassTime;
                    $Subject = $ClientInfo->CompanyName . ' המתנה לשיעור';
                    $Date = date('Y-m-d');
                    $Time = date('H:i:s');
                    $Dates = date('Y-m-d H:i:s');

                    DB::table('boostapp.appnotification')->insertGetId([
                        'CompanyNum' => $CompanyNum,
                        'ClientId' => $ClientInfo->id,
                        'TrueClientId' => '0',
                        'Type' => '4',
                        'Subject' => $Subject,
                        'Text' => $TextNotification,
                        'Dates' => $Dates,
                        'UserId' => '0',
                        'Date' => $Date,
                        'Time' => $Time,
                        'ClassId' => '0',
                        'priority' => 1,
                    ]);
                }

                ///// Class Log
                DB::table('boostapp.classlog')->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClassId' => $ClassId,
                    'ClientId' => $ClientInfo->id,
                    'Status' => $CheckNewStatus->Title,
                    'UserName' => '0',
                ]);
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
                $UserId = Auth::user()->id;

                $StatusJson = '';
                $StatusJson .= '{"data": [';

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
                ]);

                ///// Class Log
                DB::table('boostapp.classlog')->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClassId' => $ClassId,
                    'ClientId' => $ClientInfo->id,
                    'Status' => $CheckNewStatus->Title,
                    'UserName' => '0',
                ]);
                /////////////////////////////////////////

                $BrackStatus = '0';
            }

            if ($BrackStatus == '1') {
                break;
            }
        } ////  סיום לולאה רשימת המתנה
}


//// עדכון שיעור ברשימת משתתפים

$ClientRegister = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
$WatingListNum = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();


DB::table('boostapp.classstudio_date')
    ->where('CompanyNum', '=', $CompanyNum)
    ->where('id', '=', $ClassId)
    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingListNum));

if($BrackStatus == 1) {
    $LogWatingList = 'הריץ רשימת המתנה ידנית לשיעור ' . $LogClassName . ' בתאריך:' . $LogClassDate . ' בשעה:' . $LogClassTime;
    CreateLogMovement($LogWatingList, '0');
}

//////////////////////////  סיום בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////
echo json_encode(array("Status" => $BrackStatus == 1 ? "Success" : "Error", "Message" => $message));
