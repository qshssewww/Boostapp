<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
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

$timenew = strtotime($ThisDate);
$AddDate = '-1 day';
$final = date("Y-m-d", strtotime($AddDate, $timenew));
$TrueDate = $final;	

//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////
try {

    $GetClasses = DB::table('classstudio_date')->where('CompanyNum', '!=', '569121')->where('CheckInStatus', '=', '1')->where('StartDate', '=', $TrueDate)->get();

    foreach ($GetClasses as $GetClasse) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {

            $CompanyNum = $GetClasse->CompanyNum;

            $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();

            $MemberShipLimitMoney = $AppSettings->MemberShipLimitMoney;
            $MemberShipLimit = $AppSettings->MemberShipLimit;
            $MemberShipLimitType = $AppSettings->MemberShipLimitType;
            $MemberShipLimitLateCancel = $AppSettings->MemberShipLimitLateCancel;
            $MemberShipLimitNoneShow = $AppSettings->MemberShipLimitNoneShow;
            $MemberShipLimitDays = $AppSettings->MemberShipLimitDays;
            $MemberShipLimitUnBlockDays = $AppSettings->MemberShipLimitUnBlockDays;
            $MemberShipLimitUnBlock = $AppSettings->MemberShipLimitUnBlock;
            $DaysMemberShipLimit = $AppSettings->DaysMemberShipLimit;

            $CheckInSettings = DB::table('boostapp.checkinsettings')->where('CompanyNum', '=', $GetClasse->CompanyNum)->first();
            $StatusClose = $CheckInSettings->StatusClose;

/// עדכן שיעור הושלם    
            DB::table('classstudio_date')
                ->where('id', $GetClasse->id)
                ->update(array('CheckInStatus' => '2'));

            $Clients = DB::table('classstudio_act')->where('ClassId', '=', $GetClasse->id)->where('CompanyNum', $GetClasse->CompanyNum)->whereIn('Status', array(1, 6, 10, 11, 12, 15, 21))->get();
            foreach ($Clients as $ClassAct) {


                $ClientId = $ClassAct->FixClientId;

                $ClientBalanceValue = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->ClientActivitiesId)->first();

                $EventId = $ClassAct->id;
                $NewStatus = $StatusClose;

                $ReClass = '1';
                $FinalTrueBalanceValue = '0';
                $KnasOption = '0';
                $KnasOptionVule = '0.00';
                $Cards = '';
                $WatingListSort = '0';
                $TestClass = '1';


                $ClientBalanceValue = DB::table('boostapp.client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->ClientActivitiesId)->first();
                $TrueBalanceValue = $ClientBalanceValue->TrueBalanceValue;
                $OrigenalBalanceValue = $ClientBalanceValue->BalanceValue;

                $CheckOldStatus = DB::table('boostapp.class_status')->where('id', '=', $ClassAct->Status)->first();
                $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();

                $StatusCount = $CheckNewStatus->StatusCount;

/// מנוי תקופתי
                if ($ClientBalanceValue->Department == '1') {

                    if ($NewStatus == '4' || $NewStatus == '8') {
                        $KnasOption = '1';
                        $KnasOptionVule = $MemberShipLimitMoney;
                    }


                } /// כרטיסיה
                elseif ($ClientBalanceValue->Department == '2' || $ClientBalanceValue->Department == '3') {

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


                    DB::table('boostapp.client_activities')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('id', '=', $ClientBalanceValue->id)
                        ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));

                    $Cards = $FinalTrueBalanceValue . ' / ' . $OrigenalBalanceValue;


                }

/// תיעוד שינוי סטטוס

                $Dates = date('Y-m-d G:i:s');
                $UserId = '0';

                $StatusJson = '';
                $StatusJson .= '{"data": [';

                if ($ClassAct->StatusJson != '') {
                    $Loops = json_decode($ClassAct->StatusJson, true);
                    foreach ($Loops['data'] as $key => $val) {

                        $DatesDB = $val['Dates'];
                        $UserIdDB = $val['UserId'];
                        $StatusDB = $val['Status'];
                        $StatusTitleDB = $val['StatusTitle'];
                        $UserNameDB = $val['UserName'];

                        $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                    }
                }

                $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "' . $UserId . '", "Status": "' . $NewStatus . '", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": ""}';

                $StatusJson .= ']}';


//// השלמת שיעור

                if ($NewStatus == '10') {
                    $ReClass = '2';
                }

/// שיעור נסיון
                if ($NewStatus == '11') {
                    $TestClass = '2';
                }


/// עדכון לסטטוס חדש
                (new ClassStudioAct($EventId))->update([
                    'Status' => $NewStatus,
                    'StatusJson' => $StatusJson,
                    'StatusCount' => $StatusCount,
                    'ReClass' => $ReClass,
                    'KnasOption' => $KnasOption,
                    'KnasOptionVule' => $KnasOptionVule,
                    'WatingListSort' => $WatingListSort,
                    'TestClass' => $TestClass,
                ]);

//// עדכון שיעור ברשימת משתתפים

                $ClientRegister = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassAct->ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
                $WatingList = DB::table('boostapp.classstudio_act')->where('ClassId', '=', $ClassAct->ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();


                DB::table('boostapp.classstudio_date')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $ClassAct->ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList));

                if ($WatingList > '0' && $CheckNewStatus->StatusCount != '0') {
                    $True = 'True';

                } else {
                    $True = 'False';
                }


                if ($NewStatus == '10') {
                    $TrueReClass = 'True';
                } else {
                    $TrueReClass = 'False';
                }

///// Class Log
DB::table('boostapp.classlog')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassAct->ClassId, 'ClientId' => $ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => $UserId, 'numOfClients' => $ClientRegister));
/////////////////////////////////////////   


            }


        } else {
            DB::table('classstudio_date')
                ->where('id', $GetClasse->id)
                ->update(array('CheckInStatus' => '2'));
        }


    }


    $ThisDate = date('Y-m-d');
    $ThisDay = date('l');
    $ThisTime = date('H:i:s');


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
?>
