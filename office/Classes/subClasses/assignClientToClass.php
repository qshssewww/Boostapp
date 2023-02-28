<?php
require_once __DIR__ . "/../../services/GoogleCalendarService.php";
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/MeetingCancellationPolicy.php';

$AuthUser = Auth::user();
$UserId = $AuthUser->__get('id');
$UserName = $AuthUser->__get('display_name');
$CompanyNum = $AuthUser->__get('CompanyNum');

$data = $data ?? "Error";

if ($data == "Error") {
    return ["Status" => "Error", "Message" => "data is missing"];
}
$StudioDate = $StudioDate ?? ClassStudioDate::find($data->classId);

$ClientId = $data->clientId;
$ClientName = (new Client($ClientId))->__get('CompanyName');
$ActivityId = $data->chooseMembership ?? $data->activityId;
$DeviceId = $data->deviceId ?? 0;
$MemberShipInfo = new ClientActivities($ActivityId);
$StudioActObj = new ClassStudioAct();
$TrueClasessFinal = $MemberShipInfo->__get('CompanyNum').$MemberShipInfo->__get('ItemId').'-1';


$FloorInfo = DB::table('boostapp.sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $StudioDate->Floor)->first();

$TrueClientId = $MemberShipInfo->__get('ClientId') == $ClientId ? '0' : $MemberShipInfo->__get('ClientId');

$ReminderStatus = $StudioDate->SendReminder;
$TypeReminder = $StudioDate->TypeReminder;
$TimeReminder = $StudioDate->TimeReminder;
$CancelLaw = $StudioDate->CancelLaw;
$CancelDay = $StudioDate->CancelDay;
$CancelDayMinus = $StudioDate->CancelDayMinus;
$CancelDayName = $StudioDate->CancelDayName;
$CancelTillTime = $StudioDate->CancelTillTime;
$ClassName = $StudioDate->ClassName;
$ClassNameType = $StudioDate->ClassNameType;
$ClassDate = $StudioDate->StartDate;
$ClassStartTime = $StudioDate->StartTime;
$ClassEndTime = $StudioDate->EndTime;


if ($ReminderStatus == '1') {
    $ReminderStatus = '2';
} elseif ($StudioDate->TypeReminder == 1 && $StudioDate->StartDate == date('Y-m-d') && $StudioDate->StartTime <= date('H:i:s')) {
        $ReminderStatus = 1;
}

$CancelDate = '';
$CancelDay = '';
$CancelTime = '';
if ($CancelLaw == '1') {
    $CancelDate = $ClassDate;
    $CancelTime = $CancelTillTime;
} else if ($CancelLaw == '2') {
    $CancelDate = date("Y-m-d", strtotime('-1 day', strtotime($ClassDate)));
    $CancelTime = $CancelTillTime;
} else if ($CancelLaw == '3') {
    $CancelDayNum = '-' . $CancelDayMinus . ' day';
    $CancelDate = date("Y-m-d", strtotime($CancelDayNum, strtotime($ClassDate)));
    $CancelDay = $CancelDayName;
    $CancelTime = $CancelTillTime;
}

$CancelJson = '{"data": [';
$CancelJson .= '{"CancelDate": "' . $CancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
$CancelJson .= ']}';

if ($StudioDate->meetingTemplateId) {
    $Client = Client::find($ClientId);
    if($Client) {
        $cancellationPolicyId = MeetingCancellationPolicy::getPolicyIdForClient($Client);
    }
    if ($StudioDate->SendReminder == ClassStudioDate::SEND_REMINDER_OFF) {
        $ReminderStatus = ClassStudioAct::REMINDER_NO_SEND;
    } elseif ($TypeReminder == ClassStudioDate::TYPE_REMINDER_SAME_DAY && $ClassDate == date('Y-m-d') && $ClassStartTime <= date('H:i:s')) {
        $ReminderStatus = ClassStudioAct::REMINDER_SENT;
    } else {
        $ReminderStatus = $StudioDate->SendReminder;  // ClassStudioAct::REMINDER_ACTIVE
    }
}

if ($TypeReminder == '1') {
    $ReminderDate = $ClassDate;
} elseif ($TypeReminder == '2' && $ClassDate == date('Y-m-d')) {
    $ReminderDate = $ClassDate;
    $TimeReminder = date("H:i:s");
} else {
    $ReminderDate = date("Y-m-d", strtotime('-1 day', strtotime($ClassDate)));
}


/// בדיקת הגדרות אפליקציה
$AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();

$DifrentTime = $AppSettings->DifrentTime; /// לאפשר החלפת שיעור באותו היום? 1 כן
$DifrentTimeMin = $AppSettings->DifrentTimeMin; /// זמן בדקות
$Watinglist = $AppSettings->Watinglist; /// בדיקת שיבוץ אוטומטי 2 לא
$WatinglistMin = $AppSettings->WatinglistMin; // זמן תגובה ללא שיבוץ אוטומטי
$SendSMSWeb = $AppSettings->SendSMS;
$FreeWatingList = $AppSettings->FreeWatingList;
$SendNotification = $AppSettings->SendNotification;

$TimeAutoWatinglist = null;
if ($Watinglist == '2') {
    $StatusTimeAutoWatinglist = '1';
} else {
    $StatusTimeAutoWatinglist = '0';
}


if ($DifrentTime == '1') {
    $ItemsMin = '-' . $DifrentTimeMin . ' minutes';
    $time = strtotime($ClassStartTime);
    $ChangeClassTime = date("H:i", strtotime($ItemsMin, $time));
    $ChangeClassStatus = '0';
} else {
    $ChangeClassTime = '';
    $ChangeClassStatus = '1';
}

$Department = $MemberShipInfo->__get('Department');
$MemberShip = $MemberShipInfo->__get('MemberShip');
$ItemText = $MemberShipInfo->__get('ItemText');

/// נתוני מנוי פנימי
$MemberInfo = DB::table('boostapp.items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipInfo->__get('ItemId'))->first();

$StartTime = $MemberInfo->StartTime;
$EndTime = $MemberInfo->EndTime;

$TrueBalanceClass = $MemberShipInfo->__get('TrueBalanceValue');
$WatingListSort = '0';

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//// בדיקה במידה והשיעור הוזמן בעבר


    /// בדיקת הזמנה כפולה לאותו השיעור

$CheckOrderSameClass = DB::table('boostapp.classstudio_act')
    ->where('CompanyNum', '=', $CompanyNum)
    ->where('FixClientId', $ClientId)
    ->where('ClassId', '=', $StudioDate->id)
    ->first();


if (!empty($CheckOrderSameClass)) {
    if (in_array($CheckOrderSameClass->Status, [1,2,6,7,8,9,10,11,12,15,16,17,21,22,23]) && !isset($data->status) ) //if already assigned with active/waiting status, unless got status
    {
        return ["Message" => lang('client_already_assigned'), "Status" => "Error"];
    }

    $TextStatus = '';
    if ($ActivityId != $CheckOrderSameClass->ClientActivitiesId) {

        $ActivityInfoChange = DB::table('boostapp.client_activities')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('id', $CheckOrderSameClass->ClientActivitiesId)
            ->first();
        $TextStatus = 'השיעור עבר ממנוי מספר ' . $ActivityInfoChange->CardNumber;
        $tempUpdateArr = [];
        if($CheckOrderSameClass->Status == 4) {     /// late cancellation
            /// return balance to old membership and change status to 3 for punch new membership
            ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($CheckOrderSameClass->id), $CompanyNum, 3);
            $tempUpdateArr = [
                "Status" => 3
            ];
        }
        $tempUpdateArr['ClientActivitiesId'] = $ActivityId;
        $updateNewActivity = (new ClassStudioAct($CheckOrderSameClass->id))->update($tempUpdateArr);

    }


    /// בדיקת מצב שיעור וקביעת סטטוס ראשוני
    $WatingListSort = '0';
    $ClassCount = DB::table('boostapp.classstudio_act')
        ->where('ClassId', '=', $StudioDate->id)
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('StatusCount', '=', '0')
        ->count();
    

    if ($ClassCount >= $StudioDate->MaxClient && !isset($data->status)) {
        // if full and before popup return massage
        if($data->popup === 0) {
            return ['Status' => 'overLimit'];
        }
        // after popup and click on assign
        if($data->overrideStatus === 1) {
            $Status = 1;
        } else {
            // after popup or not need popup and wait list
            $Status = 9;    /// ממתין ברשימת המתנה
            $StatusCount = 1;
            $WatingListSort = $StudioActObj->getMaxWaitingSortByClassId($StudioDate->id) + 1;
        }

    } elseif ((!empty($StudioDate->liveClassLink) || $StudioDate->is_zoom_class == "1") && $StudioDate->registerLimit == 2) {
        $Status = 16;  //שיעור ללא חיוב
    } elseif (isset($data->status)) {
        $Status = $data->status;
    } else {
        $Status = 1;    /// שובץ פעיל/מומש
        if($Department == 3 || $data->isNew && $data->chargeOption == 'without-charge'){
            $Status = 11;
            $TestClass = 2;
        }
    }
    
    //Update balance to client who was signed up to this lesson on the past
    $CheckNewStatus = ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($CheckOrderSameClass->id), $CompanyNum, $Status);
    $StatusCount = $CheckNewStatus->StatusCount;

    $Dates = date('Y-m-d H:i:s');

    $StatusJson = '{"data": [';

    if ($CheckOrderSameClass->StatusJson != '') {
        $Loops = json_decode($CheckOrderSameClass->StatusJson, true);
        foreach ($Loops['data'] as $key => $val) {

            $DatesDB = $val['Dates'];
            $UserIdDB = $val['UserId'];
            $StatusDB = $val['Status'];
            $StatusTitleDB = $val['StatusTitle'];
            $UserNameDB = $val['UserName'];

            $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

        }
    }

    $StatusTitle = $CheckNewStatus->Title . ' ' . $TextStatus;
    $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "' . $UserId . '", "Status": "' . $Status . '", "StatusTitle": "' . $StatusTitle . '", "UserName": "'. $UserName .'"}';
    $StatusJson .= ']}';

    $ChangeClassDate = null;

    if ($DifrentTime == '1') {
        $ClassDateDifrent = $ClassDate . ' ' . $ClassStartTime;
        $CancelDayNum = '-' . $DifrentTimeMin . ' minutes';
        $ChangeClassDate = date("Y-m-d H:i:s", strtotime($CancelDayNum, strtotime($ClassDateDifrent)));

    }

    $updateArr = array(
        'StatusJson' => $StatusJson,
        'DeviceId' => $DeviceId,
        'MemberShip' => $MemberShip,
        'StatusCount' => $StatusCount,
        'Department' => $Department,
        'ItemText' => $MemberShipInfo->__get('ItemText'),
        'Status' => $Status,
        'ExtraBooking' => 0,
        'WatingListSort' => $WatingListSort,
        'StatusTimeAutoWatinglist' => '1',
        'TimeAutoWatinglist' => null,
        'TimeAutoWatinglistDate' => null,
        'ClientActivitiesId' => $ActivityId,
        'TrueClasess' => $TrueClasessFinal,
        'ChangeClassDate' => $ChangeClassDate,
        'MeetingCancellationPolicy' => $cancellationPolicyId ?? null,
    );


    if (isset($data->regularClassId)) {
        $updateArr = array_merge($updateArr, ['RegularClass' => 1, 'RegularClassId' => $data->regularClassId]);
    }

    $updateAct = (new ClassStudioAct($CheckOrderSameClass->id))->update($updateArr);

    $AddClassDesk = $CheckOrderSameClass->id;

    //מספר לקוח עבור לקוח עם שיבוץ קיים
    $ClientIdLog = $CheckOrderSameClass->FixClientId;

    //// קליטת לוג מערכת
    $LogClassDate = date('d/m/Y', strtotime($StudioDate->StartDate));
    $LogClassTime = date('H:i', strtotime($StudioDate->StartTime));
    $LogClassName = $StudioDate->ClassName;

    $isOverrideText = isset($data->override) && $data->override == true ? ' שובץ בחריגה במנוי' : '';

    $LogText = 'נרשם לשיעור כ-' . $CheckNewStatus->Title . ' עבור שיעור ' . $LogClassName . ' בתאריך ' . $LogClassDate . ' ובשעה ' . $LogClassTime . $isOverrideText;

    DB::table('log')->insertGetId(
        array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UserId' => $UserId, 'Text' => $LogText));

    DB::table('boostapplogin.badpoint')
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('ClinetId', '=', $CheckOrderSameClass->FixClientId)
        ->where('ClassId', '=', $CheckOrderSameClass->ClassId)
        ->delete();

} else {

    /// בדיקת מצב שיעור וקביעת סטטוס ראשוני

    $WatingListSort = '0';
    $ClassCount = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $StudioDate->id)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();

    if ($ClassCount >= $StudioDate->MaxClient && !isset($data->status)) {
        // if full and before popup return massage
        if($data->popup === 0) {
            return ['Status' => 'overLimit'];
        }
        // after popup and click on assign
        if($data->overrideStatus === 1) {
            $Status = 1;
            if ($Department == '2' || $Department == '3') {
                ////  ניקוב כרטיסיה
                if($Department == 3 || $data->isNew && $data->chargeOption == 'without-charge'){
                    $Status = 11;
                    $TestClass = 2;
                }
                $TrueBalanceValue = $MemberShipInfo->__get('TrueBalanceValue') - 1;

                DB::table('boostapp.client_activities')
                    ->where('id', $MemberShipInfo->__get('id'))
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('TrueBalanceValue' => $TrueBalanceValue));
            }
        } else{
            // after popup or not need popup and wait list
            $Status = 9;    /// ממתין ברשימת המתנה
            $StatusCount = 1;
            $WatingListSort = $StudioActObj->getMaxWaitingSortByClassId($StudioDate->id) + 1;
        }


    } elseif ((!empty($StudioDate->liveClassLink) || $StudioDate->is_zoom_class == "1") && $StudioDate->registerLimit == 2) {
        $Status = 16;  //שיעור ללא חיוב
    } elseif (isset($data->status)) {
        $Status = $data->status;
    } else {
        $Status = 1;    /// שובץ פעיל/מומש

        if ($Department == '2' || $Department == '3') {
            ////  ניקוב כרטיסיה
            if($Department == 3 || $data->isNew && $data->chargeOption == 'without-charge') {
                $Status = 11;
                $TestClass = 2;
            }
            $TrueBalanceValue = $MemberShipInfo->__get('TrueBalanceValue') - 1;

            DB::table('boostapp.client_activities')
                ->where('id', $MemberShipInfo->__get('id'))
                ->where('CompanyNum', $CompanyNum)
                ->update(array('TrueBalanceValue' => $TrueBalanceValue));


        }

    }

    $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $Status)->first();
    $StatusCount = $CheckNewStatus->StatusCount;
    $StatusTitle = $CheckNewStatus->Title;
    $Dates = date('Y-m-d H:i:s');


    $StatusJson = '{"data": [';
    $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": ' . $UserId . ', "Status": "' . $Status . '", "StatusTitle": "' . $StatusTitle . '", "UserName": "'. $UserName .'"}';
    $StatusJson .= ']}';

    // תיקון חישוב שבוע בשנה
    $WeekNumber = date("Wo", strtotime("+1 day", strtotime($ClassDate)));


    $ChangeClassDate = null;

    if ($DifrentTime == '1') {
        $ClassDateDifrent = $ClassDate . ' ' . $ClassStartTime;
        $CancelDayNum = '-' . $DifrentTimeMin . ' minutes';
        $ChangeClassDate = date("Y-m-d H:i:s", strtotime($CancelDayNum, strtotime($ClassDateDifrent)));

    }

    $insertArr = [
        'CompanyNum' => $CompanyNum,
        'ClassId' => $StudioDate->id,
        'ClassNameType' => $ClassNameType,
        'ClassName' => $ClassName,
        'ClassDate' => $ClassDate,
        'ClassStartTime' => $ClassStartTime,
        'ClassEndTime' => $ClassEndTime,
        'ClientActivitiesId' => $ActivityId,
        'Department' => $Department,
        'MemberShip' => $MemberShip,
        'ItemText' => $ItemText,
        'WeekNumber' => $WeekNumber,
        'DeviceId' => $DeviceId,
        'StatusCount' => $StatusCount,
        'Status' => $Status,
        'Dates' => $Dates,
        'UserId' => '0',
        'CancelJson' => $CancelJson,
        'StatusJson' => $StatusJson,
        'ReminderStatus' => $ReminderStatus,
        'ReminderDate' => $ReminderDate,
        'ReminderTime' => $TimeReminder,
        'WatinglistMin' => $WatinglistMin,
        'TimeAutoWatinglist' => $TimeAutoWatinglist,
        'StatusTimeAutoWatinglist' => $StatusTimeAutoWatinglist,
        'SendSMSWeb' => $SendSMSWeb,
        'ChangeClassStatus' => $ChangeClassStatus,
        'GuideId' => $StudioDate->GuideId,
        'FloorId' => $StudioDate->Floor,
        'WatingListSort' => $WatingListSort,
        'GroupNumber' => $StudioDate->GroupNumber,
        'DayNum' => $StudioDate->DayNum,
        'Day' => $StudioDate->Day,
        'TrueClasess' => $TrueClasessFinal,
        'FreeWatingList' => $FreeWatingList,
        'ExtraBooking' => 0,
        'ChangeClassDate' => $ChangeClassDate,
        'FixClientId' => $ClientId,
        'TestClass' => $TestClass ?? 1,
        'MeetingCancellationPolicy' => $cancellationPolicyId ?? null,
    ];

    if (isset($data->regularClassId)) {
        $insertArr = array_merge($insertArr, ['RegularClass' => 1, 'RegularClassId' => $data->regularClassId]);
    }

    //// שמירת נתונים בטבלה
    if ($TrueClientId == '0') {
        $insertArr = array_merge($insertArr, ['ClientId' => $ClientId, 'TrueClientId' => '0']);
    }
    else {
        $insertArr = array_merge($insertArr, ['ClientId' => $TrueClientId, 'TrueClientId' => $ClientId]);
    }

    $AddClassDesk = DB::table('boostapp.classstudio_act')->insertGetId($insertArr);
    GoogleCalendarService::checkChangedAndSync($AddClassDesk, [], true);
    //מספר לקוח עבור לקוח ללא שיבוץ קיים לשיעור



} //// סיום בדיקת הזמנה קודמת

//// עדכון שיעור ברשימת משתתפים

$ClientRegister = $StudioDate->updateClientRegisterCount();

///// Class Log

$classLogId = DB::table('boostapp.classlog')->insertGetId(
    array('CompanyNum' => $CompanyNum, 'ClassId' => $StudioDate->id, 'ClientId' => $ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => $UserId, 'numOfClients' => $ClientRegister['clientRegistered']));

//// הדפסת סיום פעולה
if ($Status == '9') {
    //// קליטת לוג מערכת
    $LogClassDate = date('d/m/Y', strtotime($StudioDate->StartDate));
    $LogClassTime = date('H:i', strtotime($StudioDate->StartTime));
    $LogClassName = $StudioDate->ClassName;

    $LogText = ''. lang('waiting_for_class') .' ' . $LogClassName . ' '. lang('in_date_cron').' ' . $LogClassDate . ' '.lang('and_in_time_cron').' ' . $LogClassTime;

} else {
    //// קליטת לוג מערכת
    $LogClassDate = date('d/m/Y', strtotime($StudioDate->StartDate));
    $LogClassTime = date('H:i', strtotime($StudioDate->StartTime));
    $LogClassName = $StudioDate->ClassName;

    $LogText = ''.lang('assigned_to_class').' ' . $LogClassName . ' '.lang('in_date_cron').' ' . $LogClassDate . ' '.lang('and_in_time_cron').' ' . $LogClassTime;

    $InsertLog = DB::table('log')->insertGetId(
        array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UserId' => $UserId, 'Text' => $LogText));

    //// סיום קליטת לוג מערכת


}

return ['Status' => 'Success', 'actId' => $AddClassDesk, 'newStatus' => $Status, 'clientCount' => $ClientRegister];