<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioDate.php';
echo "-----------------     Start Script    ----------------------------------<br>";
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}

function fillMissingActs($regular, $prev, $next, $act) {
    $intervals = abs(strtotime($next) - strtotime($prev)) / 60 / 60 / 24 / 7;
    $res = false;
    if(!is_int($intervals) || $intervals <= 1) {
        return $res;
    }
    $addWeek = $prev;
    for ($i =0; $i < $intervals -1; $i++) {
        $addWeek = date("Y-m-d", strtotime("+7 days", strtotime($addWeek)));
        $getClass = DB::table('classstudio_date')
            ->where('CompanyNum', '=', $regular->CompanyNum)
            ->where('GroupNumber', '=', $regular->GroupNumber)
            ->where('StartDate', '=', $addWeek)
            ->where('StartTime', '=', $regular->ClassTime)
            ->where('Floor', '=', $regular->Floor)
            ->where('Status', '=', 0)
            ->first();
        if ($getClass) {
            $res = true;
            $hasAct = DB::table('classstudio_act')
                ->where('CompanyNum', '=', $regular->CompanyNum)
                ->where('ClientId', '=', $regular->ClientId)
                ->where('ClassDate', '=', $addWeek)
                ->where('FloorId', '=', $regular->Floor)
                ->where('ClassId', '=', $getClass->id)
                ->first();
            if($hasAct) {
                /// edit
                echo "found exist act (need to edit) for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$getClass->StartDate." \n";
//                $updateAct = DB::table('classstudio_act')
//                    ->where('id', $hasAct->id)
//                    ->where('CompanyNum', '=', $hasAct->CompanyNum)
//                    ->update([''])
            } else {
                echo "found missing act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$getClass->StartDate." \n";

//                $WeekNumber = date("Wo", strtotime("+1 day",strtotime($getClass->StartDate)));
//                /// CancelJson is missing
//                $AddClassDesk = DB::table('classstudio_act')->insertGetId(array(
//                    'CompanyNum' => $regular->CompanyNum,
//                    'ClientId' => $act->ClientId,
//                    'TrueClientId' => $act->TrueClientId,
//                    'ClassId' => $getClass->id,
//                    'ClassNameType' => $act->ClassNameType,
//                    'ClassName' => $getClass->ClassName,
//                    'ClassDate' => $addWeek,
//                    'ClassStartTime' => $getClass->StartTime,
//                    'ClassEndTime' => $getClass->EndTime,
//                    'ClientActivitiesId' => $regular->ClientActivitiesId,
//                    'Department' => $act->Department,
//                    'MemberShip' => $act->MemberShip,
//                    'ItemText' => $act->ItemText,
//                    'WeekNumber' => $WeekNumber,
//                    'DeviceId' => $act->DeviceId,
//                    'Remarks' => $act->Remarks,
//                    'StatusCount' => $act->StatusCount,
//                    'Status' => $regular->StatusType,
//                    'Dates' => date('Y-m-d H:i:s'),
//                    'UserId' => $act->UserId,
//                    'ReminderStatus' => 0,
//                    'ReminderDate' => date("Y-m-d", strtotime("-1 days", strtotime($getClass->StartDate))),
//                    'ReminderTime' => $act->ReminderTime,
//                    'WatinglistMin' => $act->WatinglistMin,
//                    'TimeAutoWatinglist' => $act->TimeAutoWatinglist,
//                    'SendSMSWeb' => 1,
//                    'GuideId' => $getClass->GuideId,
//                    'FloorId' => $getClass->Floor,
//                    'WatingListSort' => 0,
//                    'GroupNumber' => $regular->GroupNumber,
//                    'TestClass' => $act->TestClass,
//                    'DayNum' => $getClass->DayNum,
//                    'Day' => $getClass->Day,
//                    'TrueClasess' => $act->TrueClasess,
//                    'ChangeClassDate' => $act->ChangeClassDate,
//                    'FixClientId' => $act->ClientId,
//                    'RegularClass' => $act->RegularClass,
//                    'RegularClassId' => $act->RegularClassId
//                ));

//                $update = ClassStudioDate::updateClassRegistersCount($getClass->id, $regular->GroupNumber, $getClass->Floor, $getClass->StartDate);

//                $CompanyNum = Auth::user()->CompanyNum;
//                $ClientRegister = ClassStudioAct::getClassRegisterCount($ClassId, $CompanyNum);
//                $WatingList = ClassStudioAct::getClassWaitingCount($ClassId, $CompanyNum);
//
//                $ClientRegisterRegular = DB::table('classstudio_dateregular')
//                    ->where('CompanyNum', '=', $CompanyNum)
//                    ->where('GroupNumber', '=', $GroupNumber)
//                    ->where('Floor', '=', $Floor)
//                    ->where('StatusType', '=', '12')
//                    ->where(function ($q) use ($StartDate) {
//                        $q->where('RegularClassType', '=', 1)
//                            ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
//                    })->count();
//
//                $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
//                    ->where('CompanyNum', '=', $CompanyNum)
//                    ->where('GroupNumber', '=', $GroupNumber)
//                    ->where('Floor', '=', $Floor)
//                    ->where('StatusType', '=', '9')
//                    ->where(function ($q) use ($StartDate) {
//                        $q->where('RegularClassType', '=', 1)
//                            ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
//                    })->count();
//
//
//                return DB::table('classstudio_date')
//                    ->where('CompanyNum', '=', $CompanyNum)
//                    ->where('id', '=', $ClassId)
//                    ->update(array(
//                        'ClientRegister' => $ClientRegister,
//                        'WatingList' => $WatingList,
//                        'ClientRegisterRegular' => $ClientRegisterRegular,
//                        'ClientRegisterRegularWating' => $ClientRegisterRegularWating
//                    ));
            }

        }
    }
    return $res;

}

$regulars = DB::table('classstudio_dateregular')
    ->leftJoin('classstudio_act', 'classstudio_dateregular.id', '=', 'classstudio_act.RegularClassId')
    ->leftJoin('client', 'classstudio_dateregular.ClientId', '=', 'client.id')
    ->leftJoin('settings', 'classstudio_dateregular.CompanyNum', '=', 'settings.CompanyNum')
    ->select('classstudio_dateregular.*')
//    ->whereIn('classstudio_dateregular.CompanyNum', [786572,153533,510814])
    ->where('classstudio_dateregular.Status', '=', 0)
    ->where('classstudio_act.ClassDate', '>=', date('Y-m-d'))
    ->where('settings.Status', '=', 0)
    ->whereRaw('classstudio_dateregular.Floor = classstudio_act.FloorId')
    ->whereRaw('classstudio_dateregular.ClientId = classstudio_act.ClientId')
//    ->where('classstudio_dateregular.ClassDay', '=', 'classstudio_act.Day')->where('classstudio_dateregular.ClassTime', '=', 'classstudio_act.ClassStartTime')->where('classstudio_act.Status', '=', 12)
    ->where('classstudio_act.RegularClass', '=', 1)->where('classstudio_act.RegularClassId', '!=', 0)
    ->where('classstudio_dateregular.RegularClassType', '=', 1)
    ->where('client.Status', '=', 0)
    ->groupBy('classstudio_dateregular.id')
    ->havingRaw("COUNT(classstudio_act.RegularClassId) <= 29")
    ->get();
    $count = 0;
foreach ($regulars as $regular) {
    $acts = DB::table('classstudio_act')
        ->where('RegularClassId', '=', $regular->id)->where('ClassDate', '>=', date('Y-m-d'))->orderBy('ClassDate', 'ASC')->get();
    foreach ($acts as $key => $act) {
        if($key > 0) {
            $prev = $acts[$key - 1]->ClassDate;
            $curr = $acts[$key]->ClassDate;

            $addWeek = date("Y-m-d", strtotime("+7 days", strtotime($prev)));
            if (strtotime($curr) > strtotime($addWeek)) {
                /// found invalid regular assignment
                $res = fillMissingActs($regular, $acts[$key - 1]->ClassDate, $curr, $act);
                if($res) {
                    $count++;
                }
            }
        }

    }
}

echo "-----------------     Total count: ".$count."    ----------------------------------<br>";
echo "-----------------     End Script    ----------------------------------<br>";

//    SELECT cd.*, COUNT(ca.GroupNumber) FROM `classstudio_dateregular` as cd LEFT JOIN classstudio_act as ca ON cd.id = ca.RegularClassId LEFT JOIN client ON cd.ClientId = client.id
//    WHERE cd.Status = 0 AND cd.ClientId = ca.ClientId AND ca.ClassDate >= "2021-03-22" AND cd.ClassDay = ca.Day AND cd.ClassTime = ca.ClassStartTime AND ca.Status = 12 AND ca.RegularClass = 1
//    AND cd.RegularClassType = 1 AND ca.RegularClassId != 0 AND cd.GroupNumber = ca.GroupNumber AND client.Status = 0 GROUP BY cd.id HAVING COUNT(ca.GroupNumber) < 28 ORDER BY `cd`.`Dates` DESC
