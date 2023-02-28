<?php
include_once 'checkMembershipLimitInclude.php';

$CompanyNum = Auth::user()->CompanyNum;
$MemberShipInfo = $this;
if (!isset($ClassId))
    return ["Status" => 2, "Message" => "ClassId is missing"];
$ClassInfo = ClassStudioDate::find($ClassId);

$AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
$KevaDays = $AppSettings->KevaDays;
$FreeWatingList = $AppSettings->FreeWatingList;
$WatinglistOrder = $AppSettings->WatinglistOrder;

$TimeChecked = '0';
$StatusTimes = '1';

$LimitMultiActivity = $MemberShipInfo->LimitMultiActivity;

if ($MemberShipInfo->ClientId == $ClientId)
    $TrueClientId = '0';
else
    $TrueClientId = $MemberShipInfo->ClientId;

//  בדיקת מנוי מוקפא
if ($MemberShipInfo->Freez == '1' && ($MemberShipInfo->StartFreez <= $ClassInfo->StartDate && $MemberShipInfo->EndFreez >= $ClassInfo->StartDate))
    return ["Status" => 0, "Message" => lang('client_membership_frozen')];

// free online registration
if ((!empty($ClassInfo->liveClassLink) || $ClassInfo->is_zoom_class == "1") && $ClassInfo->registerLimit == 2)
    return ["Status" => 1, "Message" => lang('class_sched_success')];

$Department = $MemberShipInfo->Department; // כרטיסיה/מנוי תקופתי
$ItemId = $MemberShipInfo->ItemId; // סוג פריט

$MemberInfo = Item::find($ItemId);
$MemberShip = $MemberInfo->MemberShip; // סוג מנוי
$LimitType = $MemberInfo->LimitType; /// 0=calendary, 1=activity dates.

/// פרטי הפריט התאמה לחוקי מנוי
// free class    
if ((!empty($ClassInfo->liveClassLink) && $ClassInfo->registerLimit == 2) || ($ClassInfo->is_zoom_class == "1") || ($ClassInfo->is_zoom_class == "1" && $ClassInfo->registerLimit == 2)) {
    $CheckItemsRoles = DB::select('select * from boostapp.items_roles where CompanyNum = "' . $CompanyNum . '" AND ItemId = "' . $ItemId . '"');
} else {
    $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ItemId, $ClassInfo->ClassNameType);
}
if (empty($CheckItemsRoles))
    return ["Status" => "0", "Message" => lang('cannot_order_class')];

/// בדיקת הזמנה כפולה לאותו השיעור
$CountDoubleClassToday = DB::table('boostapp.classstudio_act')
    ->where('CompanyNum', '=', $CompanyNum)
    ->where('FixClientId', '=', $ClientId)
    ->where('ClassId', '=', $ClassId)
    ->whereIn('StatusCount', [0, 1])
    ->count();

if ($CountDoubleClassToday >= '1' && !$isMeeting)
    return ['Status' => 2, 'Message' => lang('client_already_assigned')];

/// בדיקת סוג מנוי פלוס תוקף ו/או יתרת כרטיסיה
if ($MemberShipInfo->KevaAction == '1' && !empty($MemberShipInfo->TrueDate) && $ClassInfo->StartDate > $MemberShipInfo->TrueDate) {
    $MemberShipInfoTrueDate = date('Y-m-d', strtotime($MemberShipInfo->TrueDate . ' + ' . $KevaDays . ' days'));
} else {
    $MemberShipInfoTrueDate = $MemberShipInfo->TrueDate;
}
if ($MemberShipInfo->FirstDateStatus == '1') {
    $MemberShipInfoTrueDate = '2040-01-01';
}

if ($Department == '1' && $MemberShipInfoTrueDate < $ClassInfo->StartDate)
    return ["Status" => "0", "Message" => lang('subscription_expired')];

if ($Department == '1' && $MemberShipInfo->StartDate > $ClassInfo->StartDate && $MemberShipInfo->FirstDateStatus == '0')
    return ["Status" => "0", "Message" => lang('member_will_start_in') . date('d/m/Y', strtotime($MemberShipInfo->StartDate))];

if (($Department == '2' || $Department == '3') && $MemberShipInfo->TrueBalanceValue <= '0')
    return ["Status" => "0", "Message" => lang('card_balance_over')];

if (($Department == '2' || $Department == '3') && $MemberShipInfo->TrueDate != '' && $MemberShipInfoTrueDate < $ClassInfo->StartDate)
    return ["Status" => "0", "Message" => lang('punch_card_expired')];

if (($Department == '2' || $Department == '3') && $MemberShipInfo->StartDate > $ClassInfo->StartDate && $MemberShipInfo->FirstDateStatus == '0')
    return ["Status" => "0", "Message" => lang('card_will_start_in') . date('d/m/Y', strtotime($MemberShipInfo->StartDate))];

if ($MemberShipInfo->TrueDate != '' && $MemberShipInfoTrueDate < $ClassInfo->StartDate)
    return ["Status" => "0", "Message" => lang('membership_expired_before_class')];

$hasKevaActive = false;
if ($MemberShipInfo->KevaAction == '1' && !empty($MemberShipInfo->TrueDate) && $ClassInfo->StartDate > $MemberShipInfo->TrueDate) {
    $hasKevaActive = true;
}

foreach ($CheckItemsRoles as $CheckItemsRole) {
    $TrueClasessFinal = $CheckItemsRole->GroupId;
    $Item = $CheckItemsRole->Item;
    $Value = $CheckItemsRole->Value;
    $classArr = explode(',', $CheckItemsRole->Class);
    $eventCode = ClassesType::getEventTypeCode($ClassInfo->ClassNameType);
    if (in_array($eventCode, $classArr, true)) {
        $classArr = (new ClassesType())->getAllClassTypesArr($CompanyNum);
    }

    // בדיקות מגבלות מנוי
    switch ($CheckItemsRole->Group) {
        case 'Day': /// מגבלת לפי ימים
            $myArrayDays = explode(',', $Value);
            if (!in_array($ClassInfo->Day, $myArrayDays, true))
                return ["Status" => "0", "Message" => lang('cant_order_day')];
            break;
        case 'Time': /// מגבלת לפי ימים
            if ($StatusTimes == '0')
                break;
            $TimeChecked = '1';
            $Loops = json_decode($Value, true);
            foreach ($Loops['data'] as $val) {
                $StartTime = $val['FromTime']; // הגבלת שעות הזמנת שיעור
                $EndTime = $val['ToTime']; // הגבלת שעות הזמנת שיעור

                if ($ClassInfo->StartTime >= $StartTime && $ClassInfo->StartTime <= $EndTime) {
                    $StatusTimes = '0';
                    break;
                }
            }
            break;
        case 'Max': /// מגבלת מקסימום
            if ($Item == 'Day' && (!$isForWaitingList || $FreeWatingList == '0' || $isMeeting)) {
                $CountLimitClassDay = DB::table('boostapp.classstudio_act')
                    ->where('ClassDate', '=', $ClassInfo->StartDate)
                    ->where('ClassId', '!=', $ClassInfo->id)
                    ->where('StatusCount', '!=', '2')
                    ->whereIn('Status', array(1, 2, 4, 6, 8, 9, 11, 12, 15, 17))
                    ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                    ->where('WatingStatus', '=', '0')
                    ->where('ExtraBooking', '=', '0')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('MemberShip', '=', $MemberShip)
                    ->whereIn('ClassNameType', $classArr);
                $CountLimitClassDay = addClientCheckAndCount($CountLimitClassDay, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                if ($WatinglistOrder == '1') {
                    $CountLimitClassToday = $CountLimitClassDay;

                    $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')
                        ->where('ClassDate', '=', $ClassInfo->StartDate)
                        ->where('ClassId', '!=', $ClassInfo->id)
                        ->where('StatusCount', '=', '1')
                        ->where('Status', '=', 9)
                        ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                        ->where('WatingStatus', '=', '0')
                        ->where('ExtraBooking', '=', '0')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('MemberShip', '=', $MemberShip)
                        ->whereIn('ClassNameType', $classArr);
                    $CountLimitClassWatingToday = addClientCheckAndCount($CountLimitClassWatingToday, $ClientId, $MemberShipInfo, $LimitMultiActivity);

/// בדיקת מגבלה שבועית 
                    if ($CountLimitClassDay >= $Value && $CountLimitClassWatingToday != '1')
                        return ["Status" => "0", "Message" => lang('order_more_than') . $Value . ' ' . lang('lesson_in_day')];

                    $ClassCount = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
                    $CountClassCancelLate = DB::table('boostapp.classstudio_act')->where('FixClientId', '=', $ClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->where('ClassId', '!=', $ClassInfo->id)->where('StatusCount', '=', '3')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('ClientActivitiesId', '=', $MemberShipInfo->id)->whereIn('ClassNameType', $classArr)->count();
                    $countActiveToday = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClientId)->where('TrueClientId', '=', '0')->where('ClassDate', '=', $ClassInfo->StartDate)->where('ClassId', '!=', $ClassInfo->id)->where('StatusCount', '=', '0')->where('WatingStatus', '=', '0')->where('ExtraBooking', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('ClientActivitiesId', '=', $MemberShipInfo->id)->whereIn('ClassNameType', $classArr)->count();

                    if (!(($CountLimitClassToday >= '1' && $CountLimitClassWatingToday == '0' && $CountClassCancelLate == '0') || $CountLimitClassToday == '0' || ($CountLimitClassWatingToday == '1' && $countActiveToday == '0' && $CountClassCancelLate == '0'))) {
                        if ($CountClassCancelLate >= '1')
                            return ["Status" => "0", "Message" => lang('client_made_late_cancel')];
                        else
                            return ["Status" => "0", "Message" => lang('cannot_order_more_than1')];
                    }

                } else {
/// בדיקת מגבלה שבועית
                    if ($CountLimitClassDay >= $Value)
                        return ["Status" => "0", "Message" => lang('order_more_than') . $Value . ' ' . lang('lesson_in_day')];
                }
            } elseif ($Item == 'Week' && (!$isForWaitingList || $FreeWatingList == '0' || $isMeeting)) {
                $WeekNumber = date("Wo", strtotime("+1 day", strtotime($ClassInfo->StartDate)));

                $CountLimitClassWeek = DB::table('boostapp.classstudio_act')
                    ->where('WeekNumber', '=', $WeekNumber)
                    ->where('ClassId', '!=', $ClassInfo->id)
                    ->where('StatusCount', '!=', '2')
                    ->whereIn('Status', array(1, 2, 4, 6, 8, 9, 11, 12, 15, 17))
                    ->where('WatingStatus', '=', '0')
                    ->where('ExtraBooking', '=', '0')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('MemberShip', '=', $MemberShip)
                    ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                    ->whereIn('ClassNameType', $classArr);
                $CountLimitClassWeek = addClientCheckAndCount($CountLimitClassWeek, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                if ($FreeWatingList == '1') {
                    $CountLimitClassWeekWating = DB::table('boostapp.classstudio_act')
                        ->where('WeekNumber', '=', $WeekNumber)
                        ->where('ClassId', '!=', $ClassInfo->id)
                        ->where('Status', '=', '9')
                        ->where('WatingStatus', '=', '0')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('MemberShip', '=', $MemberShip)
                        ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                        ->whereIn('ClassNameType', $classArr);
                    $CountLimitClassWeekWating = addClientCheckAndCount($CountLimitClassWeekWating, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                    $CountLimitClassWeek -= $CountLimitClassWeekWating;
                }

/// בדיקת מגבלה שבועית 
                if ($CountLimitClassWeek >= $Value)
                    return ["Status" => "0", "Message" => lang('order_more_than') . $Value . ' ' . lang('lesson_in_week')];

            } elseif ($Item == 'Month' && (!$isForWaitingList || $FreeWatingList == '0' || $isMeeting)) {
                if ($LimitType == '0' || $MemberShipInfo->TrueDate == '') {
                    $fromDateLimit = date('Y-m-01', strtotime($ClassInfo->StartDate));
                    $toDateLimit = date('Y-m-t', strtotime($ClassInfo->StartDate));
                } else { //// תחילת חישוב תוקף מנוי לבדיקת מגבלה חודשית
                    $membershipDay = date('d', strtotime($MemberShipInfo->StartDate));
                    $classDate = $ClassInfo->StartDate;
                    $getClassDay = date('d', strtotime($ClassInfo->StartDate));

                    if ($membershipDay <= $getClassDay) {
                        $fromDateLimit = date('Y-m', strtotime($classDate)) . '-' . $membershipDay;
                        $toDateLimit = date('Y-m', strtotime('+1 month', strtotime($classDate))) . '-' . $membershipDay;
                    } else {
                        $fromDateLimit = date('Y-m', strtotime('-1 month', strtotime($classDate))) . '-' . $membershipDay;
                        $toDateLimit = date('Y-m', strtotime($classDate)) . '-' . $membershipDay;
                    }
                    $toDateLimit = date('Y-m-d', strtotime('-1 day', strtotime($toDateLimit)));

                } //// סיום חישוב תוקף מנוי לבדיקת מגבלה חודשית

                if (!empty($MemberShipInfo->VaildDate) && !empty($MemberShipInfo->StudioVaildDate) && strtotime("+1 month", strtotime($MemberShipInfo->StartDate)) == strtotime($MemberShipInfo->VaildDate) && $MemberShipInfo->StudioVaildDate > $MemberShipInfo->VaildDate && strtotime("+2 month", strtotime($MemberShipInfo->StartDate)) > strtotime($MemberShipInfo->StudioVaildDate)) {
                    $fromDateLimit = $MemberShipInfo->StartDate;
                    $toDateLimit = date('Y-m-d', strtotime('-1 day', strtotime($MemberShipInfo->StudioVaildDate)));
                }

// grace days edition
                if ($MemberShipInfo->KevaAction == '1' && $MemberShipInfo->TrueDate != '' && $ClassInfo->StartDate > $MemberShipInfo->TrueDate) {
                    $fromDateLimit = $MemberShipInfo->TrueDate;
                    $toDateLimit = date('Y-m-d', strtotime($MemberShipInfo->TrueDate . ' + ' . $KevaDays . ' days'));
                }

                $CountLimitClassMonth = DB::table('boostapp.classstudio_act')
                    ->whereBetween('ClassDate', array($fromDateLimit, $toDateLimit))
                    ->where('ClassId', '!=', $ClassInfo->id)
                    ->where('StatusCount', '!=', '2')
                    ->where('WatingStatus', '=', '0')
                    ->whereIn('Status', array(1, 2, 4, 6, 8, 9, 11, 12, 15, 17))
                    ->where('ExtraBooking', '=', '0')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('MemberShip', '=', $MemberShip)
                    ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                    ->whereIn('ClassNameType', $classArr);
                $CountLimitClassMonth = addClientCheckAndCount($CountLimitClassMonth, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                if ($FreeWatingList == '1') {
                    $CountLimitClassMonthWating = DB::table('boostapp.classstudio_act')
                        ->whereBetween('ClassDate', array($fromDateLimit, $toDateLimit))
                        ->where('ClassId', '!=', $ClassInfo->id)
                        ->where('Status', '=', '9')
                        ->where('WatingStatus', '=', '0')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('MemberShip', '=', $MemberShip)
                        ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                        ->whereIn('ClassNameType', $classArr);
                    $CountLimitClassMonthWating = addClientCheckAndCount($CountLimitClassMonthWating, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                    $CountLimitClassMonth -= $CountLimitClassMonthWating;
                }
/// בדיקת מגבלה חודשית
                if ($CountLimitClassMonth >= $Value)
                    return ["Status" => "0", "Message" => lang('order_more_than') . $Value . ' ' . lang('lesson_in_month')];

            } elseif ($Item == 'Year' && (!$isForWaitingList || $FreeWatingList == '0' || $isMeeting)) {
// תיקון מגבלת שנה
                $ClassDateStart = $MemberShipInfo->StartDate;
                $ClassDateEnd = date('Y-m-d', strtotime('+1 year', strtotime($MemberShipInfo->StartDate)));

                if ($ClassInfo->StartDate > $ClassDateEnd && $ClassInfo->StartDate <= $MemberShipInfo->TrueDate) {
                    $ClassDateStart = date('Y-m-d', strtotime('+1 day', strtotime($ClassDateEnd)));
                    $ClassDateEnd = date('Y-m-d', strtotime('+1 year', strtotime($ClassDateStart)));
                    $ClassDateEnd = date('Y-m-d', strtotime('-1 day', strtotime($ClassDateEnd)));
                }

                $CountLimitClassYear = DB::table('boostapp.classstudio_act')
                    ->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))
                    ->where('ClassId', '!=', $ClassInfo->id)
                    ->where('StatusCount', '!=', '2')
                    ->whereIn('Status', array(1, 2, 4, 6, 8, 9, 11, 12, 15, 17))
                    ->where('WatingStatus', '=', '0')
                    ->where('ExtraBooking', '=', '0')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('MemberShip', '=', $MemberShip)
                    ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                    ->whereIn('ClassNameType', $classArr);
                $CountLimitClassYear = addClientCheckAndCount($CountLimitClassYear, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                if ($FreeWatingList == '1') {
                    $CountLimitClassYearWating = DB::table('boostapp.classstudio_act')
                        ->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))
                        ->where('ClassId', '!=', $ClassInfo->id)
                        ->where('Status', '=', '9')
                        ->where('WatingStatus', '=', '0')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('MemberShip', '=', $MemberShip)
                        ->where('ClientActivitiesId', '=', $MemberShipInfo->id)
                        ->whereIn('ClassNameType', $classArr);
                    $CountLimitClassYearWating = addClientCheckAndCount($CountLimitClassYearWating, $ClientId, $MemberShipInfo, $LimitMultiActivity);

                    $CountLimitClassYear -= $CountLimitClassYearWating;
                }
/// בדיקת מגבלה שנתית
                if ($CountLimitClassYear >= $Value)
                    return ["Status" => "0", "Message" => lang('order_more_than') . $Value . ' ' . lang('lesson_in_year')];

            } elseif ($FreeWatingList == '1' && !$isForWaitingList && $Value <= 0) {
                return ["Status" => "0", "Message" => lang('cannot_order_class')];
            }

            break;

//// בדיקת ע"ב מקום פנוי/סטנד ביי    

    }/// סיום סוויטש
}

if ($StatusTimes == '1' && $TimeChecked == '1')
    return ["Status" => "0", "Message" => lang('cant_book_hour')];

$ClassCount = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
if ($ClassCount >= $ClassInfo->MaxClient) {
/// בדיקת הזמנה כפולה לאותו השיעור 
    $CheckOrderSameClassWatingStatus = DB::table('boostapp.classstudio_act')->where('FixClientId', '=', $ClientId)->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->whereIn('Status', array(4, 8))->first();

    if (!empty($CheckOrderSameClassWatingStatus))
        return ["Status" => "0", "Message" => lang('lesson_late_cancled')];
}

return ["Status" => "1", "Message" => lang('class_sched_success'), "TrueClasessFinal" => $TrueClasessFinal ?? null];
