<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Models/TagsStudio.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/MeetingGeneralSettings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Item.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ItemRoles.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassesCanceledSeries.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/GoogleCalendarService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');
$Dates = date('Y-m-d H:i:s');

function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
{
    $dateArr = array();

    do {
        if (date("w", strtotime($startDate)) != $weekdayNumber) {
            $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate))); // add 1 day
        }
    } while (date("w", strtotime($startDate)) != $weekdayNumber);

    while ($startDate <= $endDate) {
        $dateArr[] = date('Y-m-d', strtotime($startDate));
        $startDate = date('Y-m-d', strtotime('+7 day', strtotime($startDate))); // add 7 days

    }
    return ($dateArr);
}

try {
//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////

    $GetSettings = DB::table('settings')->select('id', 'CompanyNum')->whereNotIn('CompanyNum', array(100))->where('Status', '=', '0')->get();

    $limit_iteration = 40;
    $iterations = ceil(count($GetSettings) / $limit_iteration);

    for ($j = 0; $j < $iterations; $j++) {

        $GetSettings_splice = DB::table('settings')->select('id', 'CompanyNum')->whereNotIn('CompanyNum', array(100))->where('Status', '=', '0')->offset($j * $limit_iteration)->limit($limit_iteration)->get();


        foreach ($GetSettings_splice as $GetSetting) {

            $GetClasses = DB::table('classstudio_date')->where('CompanyNum', '=', $GetSetting->CompanyNum)->where('Status', '=', '0')->where('StartDate', '=', $ThisDate)->where('ClassType', '=', '1')->whereNull('meetingTemplateId')->get();

            foreach ($GetClasses as $GetClasse) {

                $checkCanceledSeries = ClassesCanceledSeries::where('companyNum', $GetSetting->CompanyNum)
                    ->where('groupNumber', $GetClasse->GroupNumber)
                    ->first();

                if (!empty($checkCanceledSeries)) {
                    continue;
                }

                $ClassInfo = DB::table('classstudio_date')->where('CompanyNum', '=', $GetSetting->CompanyNum)->where('Status', '=', '0')->where('GroupNumber', '=', $GetClasse->GroupNumber)->where('ClassType', '=', '1')->whereNull('meetingTemplateId')->orderBy('ClassCount', 'DESC')->first();
                $ClassCountsNum = DB::table('classstudio_date')->where('CompanyNum', '=', $GetSetting->CompanyNum)->where('Status', '=', '0')->where('GroupNumber', '=', $GetClasse->GroupNumber)->where('ClassType', '=', '1')->whereNull('meetingTemplateId')->count();


                //// מציאת תאריכים לימים שנבחרו
                $StartDate = $ClassInfo->StartDate;
                $StartTime = $ClassInfo->StartTime;
                $EndTime = $ClassInfo->EndTime;
                $CompanyNum = $GetSetting->CompanyNum;
                $FloorId = $ClassInfo->Floor;
                $DayNums = $ClassInfo->DayNum;

                $TreeCount = 30;
                $CountMinus = $TreeCount - $ClassCountsNum;
                if ($CountMinus < 0) {
                    $CountMinus = 0;
                }

                $ClassRepeat = $ClassInfo->ClassRepeat == 0 ? 1 : $ClassInfo->ClassRepeat;
                $ClassRepeatTypeText = 'week';
                if (!empty($ClassInfo->ClassRepeatType)) {
                    $ClassRepeatType= $ClassInfo->ClassRepeatType;
                    if ($ClassRepeatType == '1') {
                        $ClassRepeatType = 'day';
                    } elseif ($ClassRepeatType == '2') {
                        $ClassRepeatType = 'week';
                    } elseif ($ClassRepeatType == '3') {
                        $ClassRepeatType = 'month';
                    }
                }
                $ItemsDay = '+'.($CountMinus * $ClassRepeat).' '.$ClassRepeatTypeText;
                $Today = date('Y-m-d', strtotime($StartDate));
                $EndDates = date('Y-m-d', strtotime($Today . $ItemsDay));

                $dateArr = Utils::createDateRange($Today, $EndDates, '+'.$ClassRepeat.' '.$ClassRepeatTypeText);

                // מספר חזרות
                $i = $ClassInfo->ClassCount + 1;
                foreach ($dateArr as $key => $value) {

                    $start_date = date($value . ' ' . $StartTime);
                    $end_date = date($value . ' ' . $EndTime);
                    $dayNum = date('w', strtotime($value));

                    //// בדיקת שיעור קיים באותו יום,שעה,אולם
                    $CheckFloor = DB::table('classstudio_date')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('Floor', '=', $FloorId)
                        ->where('Status', '!=', 2)
                        ->where('StartDate', '=', $value)
                        ->where(function ($q) use ($StartTime, $EndTime) {
                            $q->where('StartTime', '>=', $StartTime)->where('EndTime', '<=', $EndTime)
                                ->orWhere('StartTime', '<', $EndTime)->where('EndTime', '>', $StartTime);
                        })->first();

                    if (!$CheckFloor) {

                        $AddClassDesk = DB::table('classstudio_date')->insertGetId(array(
                            'CompanyNum' => $CompanyNum,
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'text' => $ClassInfo->text,
                            'color' => $ClassInfo->color,
                            'Floor' => $FloorId,
                            'ClassNameType' => $ClassInfo->ClassNameType,
                            'ShowApp' => $ClassInfo->ShowApp,
                            'ClassName' => $ClassInfo->ClassName,
                            'GuideId' => $ClassInfo->GuideId,
                            'GuideName' => $ClassInfo->GuideName,
                            'ExtraGuideId' => $ClassInfo->ExtraGuideId,
                            'ExtraGuideName' => $ClassInfo->ExtraGuideName,
                            'MaxClient' => $ClassInfo->MaxClient,
                            'MinClass' => $ClassInfo->MinClass,
                            'MinClassNum' => $ClassInfo->MinClassNum,
                            'ClassTimeCheck' => $ClassInfo->ClassTimeCheck,
                            'StartDate' => $value,
                            'DayNum' => $dayNum,
                            'Day' => Utils::numberToDay($dayNum),
                            'StartTime' => $StartTime,
                            'EndDate' => $value,
                            'EndTime' => $EndTime,
                            'ClassType' => $ClassInfo->ClassType,
                            'ClassCount' => $i,
                            'ClassDevice' => $ClassInfo->ClassDevice,
                            'ClassMemberType' => $ClassInfo->ClassMemberType,
                            'ClassWating' => $ClassInfo->ClassWating,
                            'ShowClientNum' => $ClassInfo->ShowClientNum,
                            'ShowClientName' => $ClassInfo->ShowClientName,
                            'SendReminder' => $ClassInfo->SendReminder,
                            'TypeReminder' => $ClassInfo->TypeReminder,
                            'TimeReminder' => $ClassInfo->TimeReminder,
                            'CancelLaw' => $ClassInfo->CancelLaw,
                            'CancelDay' => $ClassInfo->CancelDay,
                            'CancelDayMinus' => $ClassInfo->CancelDayMinus,
                            'CancelDayName' => $ClassInfo->CancelDayName,
                            'CancelTillTime' => $ClassInfo->CancelTillTime,
                            'UserId' => $ClassInfo->UserId,
                            'GroupNumber' => $ClassInfo->GroupNumber,
                            'Dates' => $Dates,
                            'MaxWatingList' => $ClassInfo->MaxWatingList,
                            'NumMaxWatingList' => $ClassInfo->NumMaxWatingList,
                            'ClassTimeTypeCheck' => $ClassInfo->ClassTimeTypeCheck,
                            'ClassLimitTypes' => $ClassInfo->ClassLimitTypes,
                            'LimitLevel' => $ClassInfo->LimitLevel,
                            'GenderLimit' => $ClassInfo->GenderLimit,
                            'FreeClass' => $ClassInfo->FreeClass,
                            'StopCancel' => $ClassInfo->StopCancel,
                            'StopCancelTime' => $ClassInfo->StopCancelTime,
                            'StopCancelType' => $ClassInfo->StopCancelType,
                            'WatingListOrederShow' => $ClassInfo->WatingListOrederShow,
                            'ClassRepeat' => $ClassInfo->ClassRepeat,
                            'ClassRepeatType' => $ClassInfo->ClassRepeatType,
                            'Auto' => 2,
                            'meetingTemplateId' => $ClassInfo->meetingTemplateId,
                            'onlineClassId' => $ClassInfo->onlineClassId,
                            'Brands' => $ClassInfo->Brands,
                        ));

                        TagsStudio::cronCreating($ClassInfo->id, $AddClassDesk, $CompanyNum);

                        if ($ClassInfo->ClassLimitTypes == '1') {
                            $GetLimits = DB::table('classstudio_date_roles')->where('CompanyNum', '=', $GetSetting->CompanyNum)->where('ClassId', '=', $ClassInfo->id)->get();

                            foreach ($GetLimits as $GetLimit) {

                                $AddClassLimit = DB::table('classstudio_date_roles')->insertGetId(
                                    array('CompanyNum' => $CompanyNum, 'ClassId' => $AddClassDesk, 'MemberShipType' => $GetLimit->MemberShipType, 'Value' => $GetLimit->Value, 'Auto' => '1'));

                            }
                        }

                        /////// שיבוץ קבוע
                        $GetClients = DB::table('classstudio_dateregular')
                            ->where('CompanyNum', '=', $GetSetting->CompanyNum)
                            ->where('GroupNumber', '=', $ClassInfo->GroupNumber)
                            ->where('Status', 0)
                            ->where(function ($q) use ($value) {
                                $q->whereNull('EndDate')->Orwhere('EndDate', '>=', $value);
                            })
                            ->get();

                        foreach ($GetClients as $GetClient) {

                            $CheckClient = DB::table('client')
                                ->where('id', $GetClient->ClientId)
                                ->where('CompanyNum', $GetSetting->CompanyNum)
                                ->first();

                            if ($CheckClient && in_array($CheckClient->Status, [0,2])) {

                                $actId = 0;
                                $ActDates = '0';

                                $GetClassClientInfos = DB::table('classstudio_act')
                                    ->where('CompanyNum', '=', $GetSetting->CompanyNum)
                                    ->where('FixClientId', '=', $GetClient->ClientId)
                                    ->where('RegularClassId', '=', $GetClient->id)
                                    ->orderBy('ClassDate', 'DESC')
                                    ->first();

                                $RegularClassType = $GetClient->RegularClassType;
                                $RegularStartDate = $GetClient->StartDate;
                                $RegularEndDate = $GetClient->EndDate ?? null;

                                if ($RegularClassType == '2') {

                                    $GetClassDate = $value;

                                    if ($GetClassDate >= $RegularStartDate && (!$RegularEndDate || $GetClassDate <= $RegularEndDate)) {
                                        $ActDates = '0';
                                    } else {
                                        $ActDates = '1';
                                        DB::table('classstudio_dateregular')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('id', '=', $GetClient->id)
                                            ->update(['Status' => 1]);
                                    }

                                }


                                if ($RegularClassType == '1' || ($RegularClassType == '2' && $ActDates == '0')) {

                                    $WeekNumber = date("Wo", strtotime("+1 day", strtotime($value)));

                                    if ($ClassInfo->TypeReminder == '1') {
                                        $ReminderDate = $value;
                                    } else {
                                        $ReminderDate = date("Y-m-d", strtotime('-1 day', strtotime($value)));
                                    }


                                    $CancelDay = $ClassInfo->CancelDay;
                                    $CancelTime = $ClassInfo->CancelTillTime;
                                    $CancelLaw = $ClassInfo->CancelLaw;
                                    $cancelDate = $value;
                                    if ($CancelLaw == '1') {
                                        $cancelDate = $value;
                                    } elseif ($CancelLaw == '2') {
                                        $cancelDate = date("Y-m-d", strtotime('-1 day', strtotime($value)));
                                    } elseif ($CancelLaw == '3') {
                                        $cancelDate = date("Y-m-d", strtotime('-' . $ClassInfo->CancelDayMinus . ' days', strtotime($value)));
                                    } else {
                                        $cancelDate = '';
                                    }

                                    $CancelJson = '';
                                    $CancelJson .= '{"data": [';
                                    $CancelJson .= '{"CancelDate": "' . $cancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
                                    $CancelJson .= ']}';

                                    if ($GetClient->StatusType == '9') {
                                        $StatusCount = '1';
                                        $StatusTitle = lang('waiting_addclasses');
                                    } else {
                                        $StatusCount = '0';
                                        $StatusTitle = lang('setting_permanently');
                                    }

                                    $StatusJson = '';
                                    $StatusJson .= '{"data": [';

                                    $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "", "Status": "' . $GetClient->StatusType . '", "StatusTitle": "' . $StatusTitle . '", "UserName": ""}';

                                    $StatusJson .= ']}';

                                    $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
                                    $DifrentTime = $AppSettings->DifrentTime; /// לאפשר החלפת שיעור באותו היום? 1 כן
                                    $DifrentTimeMin = $AppSettings->DifrentTimeMin; /// זמן בדקות
                                    $Watinglist = $AppSettings->Watinglist; /// בדיקת שיבוץ אוטומטי 2 לא
                                    $WatinglistMin = $AppSettings->WatinglistMin; // זמן תגובה ללא שיבוץ אוטומטי
                                    $SendSMSWeb = $AppSettings->SendSMS;

                                    if ($Watinglist == '2') {
                                        $TimeAutoWatinglist = null;
                                        $StatusTimeAutoWatinglist = '1';
                                    } else {
                                        $TimeAutoWatinglist = null;
                                        $StatusTimeAutoWatinglist = '0';
                                    }

                                    $ChangeClassDate = null;
                                    $ChangeClassStatus = '1';
                                    if ($DifrentTime == '1') {
                                        $ClassDateDifrent = $value . ' ' . $StartTime;
                                        $CancelDayNum = '-' . $DifrentTimeMin . ' minutes';
                                        $ChangeClassDate = date("Y-m-d H:i:s", strtotime($CancelDayNum, strtotime($ClassDateDifrent)));
                                        $ChangeClassStatus = '0';
                                    }


                                    $TrueClasessFinal = '';
                                    if ($GetClient->ClientActivitiesId != 0) {
                                        $ActivityInfo = DB::table('client_activities')
                                        ->where('id', '=', $GetClient->ClientActivitiesId)
                                        ->where('CompanyNum', '=', $CompanyNum)
                                        ->first();
                                    } else {
                                        $itemId = Item::getSingleClassItemByCron($GetClasse->ClassNameType, $CompanyNum);
                                        $ActivityInfo = DB::table('client_activities')
                                            ->where('CompanyNum', '=', $CompanyNum)
                                            ->where('ItemId', '=', $itemId)
                                            ->where('ClientId', $GetClient->ClientId)
                                            ->orderBy('id', 'desc')
                                            ->first();
                                    }

                                    if ($ActivityInfo) {
                                        $TrueClientId = $ActivityInfo->TrueClientId;
                                        $FixClientId = $ActivityInfo->ClientId;
                                        $ItemId = $ActivityInfo->ItemId;

                                        $CheckItemsRole = ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $ItemId, $ClassInfo->ClassNameType);
                                        if ($CheckItemsRole) {
                                            $TrueClasessFinal = $CheckItemsRole->GroupId ?? '';
                                        } else {
                                            $CheckItemsRoleTwo = DB::table('items_roles')->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $ItemId)->first();
                                            $TrueClasessFinal = $CheckItemsRoleTwo->GroupId ?? '';
                                        }


                                        if ($FixClientId == $GetClient->ClientId) {
                                            $FixTrueClientId = '0';
                                            $ClientId = $GetClient->ClientId;
                                        } else {
                                            $FixTrueClientId = $GetClient->ClientId;
                                            $ClientId = $FixClientId;
                                        }

                                        $ReminderStatus = $ClassInfo->SendReminder;
                                        if ($ReminderStatus == '1') {
                                            $ReminderStatus = '2';
                                        }

                                        $TimeReminder = $ClassInfo->TimeReminder;
                                        $FreeWatingList = $AppSettings->FreeWatingList;

                                        $ClientActivitiesId = $GetClient->ClientActivitiesId;

                                        if (isset($MeetingSettings)) {
                                            if ($MeetingSettings->SendReminder) $ReminderStatus = 0;
                                            $interval = $MeetingSettings->getReminderInterval();
                                            $ReminderDate = Utils::addInterval("$value $StartTime", $interval);
                                            $TimeReminder = Utils::addInterval("$value $StartTime", $interval, 'H:i:s');

                                            if ($ClientActivitiesId == 0) {
                                                $assignRes = ClientActivities::assignMembership([
                                                    "clientId" => $GetClient->ClientId,
                                                    "itemId" => $itemId,
                                                    "itemPrice" => $ActivityInfo->ItemPrice, //get updated price from last purchase
                                                ]);
                                                $ClientActivitiesId = $assignRes['ClientActivityId'];
                                            }
                                        }

                                        if ($GetClassClientInfos) {
                                            $actId = DB::table('classstudio_act')->insertGetId(array(
                                                'CompanyNum' => $CompanyNum,
                                                'ClientId' => $ClientId,
                                                'TrueClientId' => $FixTrueClientId,
                                                'ClassId' => $AddClassDesk,
                                                'ClassNameType' => $ClassInfo->ClassNameType,
                                                'ClassName' => htmlentities($ClassInfo->ClassName),
                                                'ClassDate' => $value,
                                                'ClassStartTime' => $StartTime,
                                                'ClassEndTime' => $EndTime,
                                                'ClientActivitiesId' => $ClientActivitiesId,
                                                'Department' => $ActivityInfo->Department,
                                                'MemberShip' => $ActivityInfo->MemberShip,
                                                'ItemText' => htmlentities($ActivityInfo->ItemText),
                                                'WeekNumber' => $WeekNumber,
                                                'DeviceId' => 0,
                                                'StatusCount' => $StatusCount,
                                                'Status' => $GetClient->StatusType,
                                                'Dates' => date('Y-m-d H:i:s'),
                                                'UserId' => 0,
                                                'CancelJson' => $CancelJson,
                                                'StatusJson' => $StatusJson,
                                                'ReminderStatus' => $ReminderStatus,
                                                'ReminderDate' => $ReminderDate,
                                                'ReminderTime' => $TimeReminder,
                                                'WatinglistMin' => $WatinglistMin,
                                                'TimeAutoWatinglist' => $TimeAutoWatinglist,
                                                'StatusTimeAutoWatinglist' => $StatusTimeAutoWatinglist,
                                                'SendSMSWeb' => $SendSMSWeb,
                                                'ChangeClassDate' => $ChangeClassDate,
                                                'ChangeClassStatus' => $ChangeClassStatus,
                                                'GuideId' => $ClassInfo->GuideId,
                                                'FloorId' => $ClassInfo->Floor,
                                                'WatingListSort' => 0,
                                                'GroupNumber' => $ClassInfo->GroupNumber,
                                                'TestClass' => 0,
                                                'DayNum' => $ClassInfo->DayNum,
                                                'Day' => $ClassInfo->Day,
                                                'TrueClasess' => $TrueClasessFinal,
                                                'FreeWatingList' => $FreeWatingList,
                                                'RegularClass' => 1,
                                                'RegularClassId' => $GetClient->id,
                                                'Auto' => 2,
                                                'FixClientId' => $GetClient->ClientId
                                            ));
                                            GoogleCalendarService::checkChangedAndSync($actId, [], true);
                                        }
                                    }

                                    if(!$actId && $AddClassDesk) {
                                        // failed to add act
                                        LoggerService::debug([
                                            'message' => 'failed to add act',
                                            'classId' => $AddClassDesk,
                                            'oldAct' => $GetClassClientInfos ?? '',
                                            'client' => $CheckClient,
                                            'activity' => $ActivityInfo ?? '',
                                            'regular' => $GetClient ?? ''
                                        ], LoggerService::CATEGORY_CRON_ADD_CLASSES);
                                    }

                                    if ($GetClassClientInfos && $GetClassClientInfos->TrueClientId == 0) {
                                        $LogTrueClientId = $GetClassClientInfos->ClientId;
                                    } else {
                                        $LogTrueClientId = $GetClassClientInfos->TrueClientId ?? 0;
                                    }

                                    ///// Class Log
                                    $logId = DB::table('boostapp.classlog')->insertGetId(
                                        array('CompanyNum' => $CompanyNum, 'ClassId' => $AddClassDesk, 'ClientId' => $LogTrueClientId, 'Status' => $StatusTitle, 'UserName' => '0'));

                                }
                            }
                        }
                        //// עדכון שיעור ברשימת משתתפים

                        $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $AddClassDesk)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
                        $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $AddClassDesk)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();

                        if (isset($logId)) { //Update 'numOfClients' on class log
                            DB::table('boostapp.classlog')
                                ->where('id', '=', $logId)
                                ->update(array('numOfClients' => $ClientRegister));
                        }


                        $ClientRegisterRegular = DB::table('classstudio_dateregular')
                            ->where('CompanyNum', '=', $ClassInfo->CompanyNum)
                            ->where('GroupNumber', '=', $ClassInfo->GroupNumber)
                            ->where('Floor', '=', $ClassInfo->Floor)
                            ->where('StatusType', '=', '12')
                            ->where(function ($q) use ($ClassInfo) {
                                $q->where('RegularClassType', '=', 1)
                                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $ClassInfo->StartDate);
                            })->count();

                        $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
                            ->where('CompanyNum', '=', $ClassInfo->CompanyNum)
                            ->where('GroupNumber', '=', $ClassInfo->GroupNumber)
                            ->where('Floor', '=', $ClassInfo->Floor)
                            ->where('StatusType', '=', '9')
                            ->where(function ($q) use ($ClassInfo) {
                                $q->where('RegularClassType', '=', 1)
                                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $ClassInfo->StartDate);
                            })->count();


                        $ClientRegisterRegular = DB::table('classstudio_dateregular')
                            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->whereIn('RegularClassType', array(1, 2))->where('StatusType', '=', '12')
                            ->count();
                        $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
                            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->whereIn('RegularClassType', array(1, 2))->where('StatusType', '=', '9')
                            ->count();

                        DB::table('classstudio_date')
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->where('id', '=', $AddClassDesk)
                            ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList, 'ClientRegisterRegular' => $ClientRegisterRegular, 'ClientRegisterRegularWating' => $ClientRegisterRegularWating));
                    }

                    ++$i;
                }

            }

        }

        sleep(5);
    }

    $Cron->end();
} catch (Exception $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($GetClasse)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClasse), JSON_UNESCAPED_UNICODE);
    }

    $Cron->cronLog($arr);
}
