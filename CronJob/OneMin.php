<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/ClassStudioAct.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// עדכון עריכת שיעורים ///////////////////////////////////////////////////////
try {

    $GetClasses = DB::table('classstudio_date')->where('Change', '=', '1')->get();

    foreach ($GetClasses as $GetClasse) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {


            $ClassId = $GetClasse->id;
            $GroupNumber = $GetClasse->GroupNumber;
            $StartDate = $GetClasse->StartDate;
            $CompanyNum = $GetClasse->CompanyNum;

            $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
            $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();

            $ClientRegisterRegular = DB::table('classstudio_dateregular')
                ->where('CompanyNum', '=', $GetClasse->CompanyNum)
                ->where('GroupNumber', '=', $GetClasse->GroupNumber)
                ->where('Floor', '=', $GetClasse->Floor)
                ->where('StatusType', '=', '12')
                ->where(function ($q) use ($GetClasse) {
                    $q->where('RegularClassType', '=', 1)
                        ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $GetClasse->StartDate);
                })->count();

            $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
                ->where('CompanyNum', '=', $GetClasse->CompanyNum)
                ->where('GroupNumber', '=', $GetClasse->GroupNumber)
                ->where('Floor', '=', $GetClasse->Floor)
                ->where('StatusType', '=', '9')
                ->where(function ($q) use ($GetClasse) {
                    $q->where('RegularClassType', '=', 1)
                        ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $GetClasse->StartDate);
                })->count();


            if ($GetClasse->Status == '1') {
                DB::table('classstudio_date')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList, 'Change' => '0', 'color' => '#e2e2e2', 'ClientRegisterRegular' => $ClientRegisterRegular, 'ClientRegisterRegularWating' => $ClientRegisterRegularWating));
            } else {
                DB::table('classstudio_date')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList, 'Change' => '0', 'ClientRegisterRegular' => $ClientRegisterRegular, 'ClientRegisterRegularWating' => $ClientRegisterRegularWating));
            }

            DB::table('classstudio_dateregular')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('DayNum', '=', $GetClasse->DayNum)
                ->where('GroupNumber', '=', $GetClasse->GroupNumber)
                ->where('Floor', '=', $GetClasse->Floor)
                ->update(array('ClassTime' => $GetClasse->StartTime));


            if ($ClientRegister > '0') {

                //// עדכון לקוחות משובצים

                $ClassInfo = DB::table('classstudio_date')->where('id', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->first();
                $FloorInfo = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassInfo->Floor)->first();
                $GuideNames = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassInfo->GuideId)->first();

                $TypeReminder = $ClassInfo->TypeReminder;
                $TimeReminder = $ClassInfo->TimeReminder;
                $CancelLaw = $ClassInfo->CancelLaw;
                $CancelDay = $ClassInfo->CancelDay;
                $CancelDayMinus = $ClassInfo->CancelDayMinus;
                $CancelDayName = $ClassInfo->CancelDayName;
                $CancelTillTime = $ClassInfo->CancelTillTime;
                $ClassName = $ClassInfo->ClassName;
                $ClassNameType = $ClassInfo->ClassNameType;
                $StartDate = $ClassInfo->StartDate;
                $StartTime = $ClassInfo->StartTime;
                $EndTime = $ClassInfo->EndTime;
                $GuideId = $ClassInfo->GuideId;
                $FloorId = $ClassInfo->Floor;


                if ($CancelLaw == '1') {
                    $CancelDate = $StartDate;
                    $CancelDay = '';
                    $CancelTime = $CancelTillTime;
                } else if ($CancelLaw == '2') {
                    $CancelDate = date("Y-m-d", strtotime('-1 day', strtotime($StartDate)));
                    $CancelDay = '';
                    $CancelTime = $CancelTillTime;
                } else if ($CancelLaw == '3') {
                    $CancelDayNum = '-' . $CancelDayMinus . ' day';
                    $CancelDate = date("Y-m-d", strtotime($CancelDayNum, strtotime($StartDate)));
                    $CancelDay = $CancelDayName;
                    $CancelTime = $CancelTillTime;


                } else if ($CancelLaw == '4') {
                    $CancelDate = '';
                    $CancelDay = '';
                    $CancelTime = '';
                } else if ($CancelLaw == '5') {
                    $CancelDate = '';
                    $CancelDay = '';
                    $CancelTime = '';
                }

                $CancelJson = '';
                $CancelJson .= '{"data": [';
                $CancelJson .= '{"CancelDate": "' . $CancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
                $CancelJson .= ']}';

                if ($TypeReminder == '1') {
                    $ReminderDate = $StartDate;
                } else {
                    $ReminderDate = date("Y-m-d", strtotime('-1 day', strtotime($StartDate)));
                }


                $AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
                $DifrentTime = $AppSettings->DifrentTime;
                $DifrentTimeMin = $AppSettings->DifrentTimeMin;
                $ChangeClassDate = null;

                if ($DifrentTime == '1') {
                    $ClassDate = $StartDate . ' ' . $StartTime;
                    $CancelDayNum = '-' . $DifrentTimeMin . ' minutes';
                    $ChangeClassDate = date("Y-m-d H:i:s", strtotime($CancelDayNum, strtotime($ClassDate)));

                }

                $actList = ClassStudioAct::where('ClassId', $ClassId)
                    ->where('CompanyNum', $CompanyNum)
                    ->get();

                /** @var ClassStudioAct $act */
                foreach ($actList as $act) {
                    $act->update([
                        'ClassNameType' => $ClassNameType,
                        'ClassName' => $ClassName,
                        'ClassDate' => $StartDate,
                        'ClassStartTime' => $StartTime,
                        'ClassEndTime' => $EndTime,
                        'CancelJson' => $CancelJson,
                        'GuideId' => $GuideId,
                        'FloorId' => $FloorId,
                        'ReminderDate' => $ReminderDate,
                        'ReminderTime' => $TimeReminder,
                        'ChangeClassDate' => $ChangeClassDate,
                    ]);
                }
            }


        } else {
            DB::table('classstudio_date')
                ->where('CompanyNum', '=', $GetClasse->CompanyNum)
                ->where('id', '=', $GetClasse->id)
                ->update(array('Change' => '0', 'color' => '#e2e2e2'));
        }

    }


//////////////////////////////////////////////////////////////// סיום עדכון עריכת שיעורים ///////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClasse)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClasse),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
