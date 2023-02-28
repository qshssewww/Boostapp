<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioAct.php';

//require_once '../app/init.php';
//require_once '../office/Classes/ClassStudioAct.php';
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}
echo "-----------------     Start Script at ".date("H:i:s")."   ----------------------------------\n";
$count = $countRegular = 0;
$regularFlag = false;
try {
    $regulars = DB::table('boostapp.classstudio_dateregular')
        ->leftJoin('boostapp.settings', 'settings.CompanyNum', '=', 'classstudio_dateregular.CompanyNum')
        ->leftJoin('boostapp.client', 'client.id', '=', 'classstudio_dateregular.ClientId')
        ->select('classstudio_dateregular.*')
        ->where('classstudio_dateregular.Status', 0)
        ->where(function ($q) {
            $q->whereNull('classstudio_dateregular.EndDate')->orWhere('classstudio_dateregular.EndDate', '>=', date('Y-m-d'));
        })
//        ->whereIn('classstudio_dateregular.CompanyNum', [536847])
        ->where('settings.Status', 0)
        ->whereIn('client.Status', [0,2])
        ->get();

    echo "\n-----------------      got ".count($regulars)."  regulars records   ----------------------------------\n";

    foreach ($regulars as $key => $regular) {
        $classes = DB::table('boostapp.classstudio_date')
            ->where('CompanyNum', $regular->CompanyNum)
            ->where('Status', 0)
            ->where('StartDate', '>=', date('Y-m-d'))
            ->where('GroupNumber', '=', $regular->GroupNumber)
            ->whereIn('ClassType', [1,2])
            ->get();
        if(count($classes) > 30) {
            echo "\n TOO MANY CLASSES - regular id: ".$regular->id. " client: ".$regular->ClientId." and group number: ".$regular->GroupNumber."\n";
            continue;
        }
        $regularFlag = false;
        foreach ($classes as $class) {
            if((!$class->StartDate && !$class->EndDate) ||
                (($class->StartDate && $class->StartDate >= $regular->StartDate) && (!$regular->EndDate || $class->StartDate <= $regular->EndDate))) {

                $act = DB::table('boostapp.classstudio_act')
                    ->where('ClassId', $class->id)
                    ->where('FixClientId', $regular->ClientId)
                    ->first();
                if(!$act) {
                    $WeekNumber = date("Wo", strtotime("+1 day",strtotime($class->StartDate)));
                    $statusTitle = $regular->StatusType == 12 ? 'שיבוץ קבוע' : 'ממתין';
                    $statusArr[] = [
                        "Dates" => date("Y-m-d H:i:s"),
                        "Status" => $regular->StatusType,
                        "UserId" => "",
                        "UserName" => "",
                        "StatusTitle" => $statusTitle
                    ];
                    $statusJson = json_encode(["data" => $statusArr]);

                    $CancelLaw = $class->CancelLaw;
                    if ($CancelLaw == 1) {
                        $CancelDate = $class->StartDate;
                        $CancelDay = '';
                        $CancelTime = $class->CancelTillTime;
                    } elseif ($CancelLaw == 2 || $CancelLaw == 3) {
                        $CancelDate = date("Y-m-d", strtotime('-1 day', strtotime($class->StartDate)));
                        $CancelDay = '';
                        $CancelTime = $class->CancelTillTime;
                    } else {
                        $CancelDate = '';
                        $CancelDay = '';
                        $CancelTime = '';
                    }
                    $CancelJson = '';
                    $CancelJson .= '{"data": [';
                    $CancelJson .= '{"CancelDate": "' . $CancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
                    $CancelJson .= ']}';

                    $CompanyNum = $class->CompanyNum;
                    $ClientRegister = ClassStudioAct::getClassRegisterCount($class->id, $CompanyNum);
                    $WatingList = ClassStudioAct::getClassWaitingCount($class->id, $CompanyNum);
                    if($ClientRegister >= $class->MaxClient && $regular->StatusType != 9) {
                        // class is full
                        echo "CLASS IS FULL!!! - found missing act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$class->StartDate." \n";
                        continue;
                    }

                    $oldAct = DB::table('boostapp.classstudio_act')
                        ->where('FixClientId', $regular->ClientId)
                        ->where('RegularClassId', $regular->id)
                        ->where('ClassDate', '>=', date('Y-m-d'))
                        ->where('ClassDate', '<', $class->StartDate)
                        ->orderBy('ClassDate', 'desc')
                        ->first();
                    if($oldAct) {
                        echo "added missing act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$class->StartDate." \n";
                        $count++;
                        $regularFlag = true;

//                        $AddClassDesk = DB::table('classstudio_act')->insertGetId(array(
//                            'CompanyNum' => $regular->CompanyNum,
//                            'ClientId' => $oldAct->ClientId,
//                            'TrueClientId' => $oldAct->TrueClientId,
//                            'ClassId' => $class->id,
//                            'ClassNameType' => $oldAct->ClassNameType,
//                            'ClassName' => $class->ClassName,
//                            'ClassDate' => $class->StartDate,
//                            'ClassStartTime' => $class->StartTime,
//                            'ClassEndTime' => $class->EndTime,
//                            'ClientActivitiesId' => $regular->ClientActivitiesId,
//                            'Department' => $oldAct->Department,
//                            'MemberShip' => $oldAct->MemberShip,
//                            'ItemText' => $oldAct->ItemText,
//                            'WeekNumber' => $WeekNumber,
//                            'DeviceId' => $oldAct->DeviceId,
//                            'Remarks' => $oldAct->Remarks,
//                            'StatusCount' => $regular->StatusType == 12 ? 0 : 1,
//                            'Status' => $regular->StatusType,
//                            'StatusJson' => $statusJson,
//                            'CancelJson' => $CancelJson,
//                            'Dates' => date('Y-m-d H:i:s'),
//                            'UserId' => $oldAct->UserId,
//                            'ReminderStatus' => 0,
//                            'ReminderDate' => date("Y-m-d", strtotime("-1 days", strtotime($class->StartDate))),
//                            'ReminderTime' => $oldAct->ReminderTime,
//                            'WatinglistMin' => $oldAct->WatinglistMin,
//                            'TimeAutoWatinglist' => $oldAct->TimeAutoWatinglist,
//                            'SendSMSWeb' => 1,
//                            'GuideId' => $class->GuideId,
//                            'FloorId' => $class->Floor,
//                            'WatingListSort' => 0,
//                            'GroupNumber' => $regular->GroupNumber,
//                            'TestClass' => $oldAct->TestClass,
//                            'DayNum' => $class->DayNum,
//                            'Day' => $class->Day,
//                            'TrueClasess' => $oldAct->TrueClasess,
//                            'ChangeClassDate' => $oldAct->ChangeClassDate,
//                            'FixClientId' => $oldAct->FixClientId,
//                            'RegularClass' => 1,
//                            'RegularClassId' => $regular->id,
//                        ));
//                        if($AddClassDesk) {
//                            echo "added missing act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$class->StartDate." \n";
//                            $count++;
//                            $regularFlag = true;
//
//                            $ClientRegisterRegular = DB::table('classstudio_dateregular')
//                                ->where('CompanyNum', '=', $CompanyNum)
//                                ->where('GroupNumber', '=', $regular->GroupNumber)
//                                ->where('Floor', '=', $class->Floor)
//                                ->where('StatusType', '=', 12)
//                                ->where(function ($q) use ($class) {
//                                    $q->where('RegularClassType', '=', 1)
//                                        ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $class->StartDate);
//                                })->count();
//
//                            $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
//                                ->where('CompanyNum', '=', $CompanyNum)
//                                ->where('GroupNumber', '=', $regular->GroupNumber)
//                                ->where('Floor', '=', $class->Floor)
//                                ->where('StatusType', '=', '9')
//                                ->where(function ($q) use ($class) {
//                                    $q->where('RegularClassType', '=', 1)
//                                        ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $class->StartDate);
//                                })->count();
//
//
//                            DB::table('classstudio_date')
//                                ->where('CompanyNum', '=', $CompanyNum)
//                                ->where('id', '=', $class->id)
//                                ->update(array(
//                                    'ClientRegister' => $ClientRegister,
//                                    'WatingList' => $WatingList,
//                                    'ClientRegisterRegular' => $ClientRegisterRegular,
//                                    'ClientRegisterRegularWating' => $ClientRegisterRegularWating
//                                ));
//                        }
                    }

                } elseif($regular->id != $act->RegularClassId) {

//                    echo "needs update act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$class->StartDate." \n";
//                    $count++;
//                    $regularFlag = true;

//                    $update = DB::table('classstudio_act')
//                        ->where('id', $act->id)
//                        ->update([
//                            'RegularClass' => 1,
//                            'RegularClassId' => $regular->id,
//                            'GroupNumber' => $regular->GroupNumber
//                        ]);
//
//                    if($update > 0) {
//                        echo "updated act for company: ".$regular->CompanyNum." client: ".$regular->ClientId.", regular id: ".$regular->id." at date: ".$class->StartDate." \n";
//                        $count++;
//                        $regularFlag = true;
//                    }
                }
            }
        }
        if($regularFlag) {
            $countRegular++;
            echo "\n-----------------      iteration: ".$key."   ----------------------------------\n";
        }
    }

    echo "-----------------     total act count = ".$count."\n";
    echo "-----------------     total regular count = ".$countRegular."\n";
    echo "-----------------     End Script at ".date("H:i:s")."   ----------------------------------\n";
} catch(\Throwable $e) {
    echo $e->getMessage();
}


