<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ItemRoles.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

$Ststus = '999';
$CheckStatus = '0';
$BrackStatus = '0';

$FirstDate = date('Y-m-d', strtotime((date('Y-m-d'))));
$LastDate = date('Y-m-d', strtotime('+7 days', strtotime(date('Y-m-d'))));

//////////////////////////////////////////////////////////////// עדכון מנוי בשיבוץ קבוע ///////////////////////////////////////////////////////
try {


    $GetClientRegulars = DB::table('classstudio_act')->whereBetween('ClassDate', array($FirstDate, $LastDate))->whereIn('Status', array('12', '9'))->groupBy('ClientId')->select('id', 'CompanyNum', 'MemberShip', 'ClientId', 'ClientActivitiesId', 'ClassId', 'TrueClientId', 'RegularClassId')->get();

//echo count($GetClientRegulars);

    foreach ($GetClientRegulars as $GetClientRegular) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientRegular->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {

            $Ststus = '999';
            $CheckStatus = '0';
            $ClientActivityId = $GetClientRegular->ClientActivitiesId;
            $ClientId = $GetClientRegular->ClientId;
            $TrueClientId = $GetClientRegular->TrueClientId;
            $CompanyNum = $GetClientRegular->CompanyNum;
            $MemberShip = $GetClientRegular->MemberShip;
            $ClassId = $GetClientRegular->ClassId;


            $ClientActivityInfo = DB::table('client_activities')->where('id', '=', $ClientActivityId)->where('CompanyNum', '=', $CompanyNum)->first();


            if (@$ClientActivityInfo->id != '') {

                if ($ClientActivityInfo->Department == '1' && $ClientActivityInfo->TrueDate <= $ThisDate) {
                    $CheckStatus = '1';
                }
                if ($ClientActivityInfo->Department == '2' && $ClientActivityInfo->TrueBalanceValue <= '0') {
                    $CheckStatus = '1';
                }
                if ($ClientActivityInfo->Department == '3' && $ClientActivityInfo->TrueBalanceValue <= '0') {
                    $CheckStatus = '1';
                }
                if ($ClientActivityInfo->Department == '2' && $ClientActivityInfo->TrueDate != '' && $ClientActivityInfo->TrueDate <= $ThisDate) {
                    $CheckStatus = '1';
                }
                if ($ClientActivityInfo->Department == '3' && $ClientActivityInfo->TrueDate != '' && $ClientActivityInfo->TrueDate <= $ThisDate) {
                    $CheckStatus = '1';
                }
                if ($ClientActivityInfo->Status == '2' || $ClientActivityInfo->Status == '3') {
                    $CheckStatus = '1';
                }

            }

            if ($CheckStatus == '1') {


                //// בדיקת מנוי משפחתי

                $MemberShipInfos = DB::select('select * from boostapp.client_activities where (CompanyNum = "' . $CompanyNum . '" AND Freez != "1" AND Department != "4" AND Status = "0" AND FIND_IN_SET("' . $ClientId . '",TrueClientId) > 0 ) OR (CompanyNum = "' . $CompanyNum . '" AND ClientId = "' . $ClientId . '" AND Freez != "1" AND Department != "4" AND Status = "0") ');


/// פרטי השיעור    
                $ClassInfo = DB::table('classstudio_date')->where('id', '=', $ClassId)->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->first();
                $TrueClasess = '';

/// בחירת מנוי מכלל המנויים הפעילים ללקוח 
                if (!empty($MemberShipInfos)) {
                    foreach ($MemberShipInfos as $MemberShipInfo) {

                        $TrueClasessFinal = '';
                        $MemberInfo = DB::table('boostapp.items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipInfo->ItemId)->first();
                        $MemberShip = $MemberInfo->MemberShip; // סוג מנוי


                        $ActivityId = $MemberShipInfo->id; // ID
                        $Department = $MemberShipInfo->Department; // כרטיסיה/מנוי תקופתי

                        $ItemId = $MemberShipInfo->ItemId; // סוג פריט
                        $CheckItemsRole = $ClassInfo ? ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $ItemId, $ClassInfo->ClassNameType) : null;
                        if ($CheckItemsRole) {
                            $GroupId = $CheckItemsRole->GroupId;
                            $TrueClasessFinal = $CheckItemsRole->GroupId;
                            $TrueClasess = $CheckItemsRole->Class;
                        }

/// נמצאה התאמה    
                        if (@$TrueClasess != '') {

/// בדיקת סוג מנוי פלוס תוקף ו/או יתרת כרטיסיה    

                        if ($Department == '1' && $MemberShipInfo->TrueDate < date('Y-m-d')) {
                            $Ststus = '1';
                            $Text = lang('renew_membership_cron');

                            }

                        if ($Department == '1' && $MemberShipInfo->StartDate > date('Y-m-d')) {
                            $Ststus = '1';
                            $Text = lang('membership_start_date_cron').' '.with(new DateTime(@$MemberShipInfo->StartDate))->format('d/m/Y').' '.lang('contact_studio_cron');

                            }


                        if (($Department == '2' && $MemberShipInfo->TrueBalanceValue <= '0') || ($Department == '3' && $MemberShipInfo->TrueBalanceValue <= '0')) {
                            $Ststus = '1';
                            $Text = lang('punch_card_ended_cron');

                            }


                        if (($Department == '2' && $MemberShipInfo->TrueDate != '' && $MemberShipInfo->TrueDate < date('Y-m-d')) || ($Department == '3' && $MemberShipInfo->TrueDate != '' && $MemberShipInfo->TrueDate < date('Y-m-d'))) {
                            $Ststus = '1';
                            $Text = lang('punch_card_date_ended_cron');

                            }

//// הצג שגיאה    
                            if ($Ststus == '1') {
                            } /// שמור הזמנה בבסיס נתונים
                            else {

                                /// נתוני מנוי פנימי
                                $MemberInfo = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipInfo->ItemId)->first();

                                $StartTime = $MemberInfo->StartTime;
                                $EndTime = $MemberInfo->EndTime;
                                $CancelLImit = $MemberInfo->CancelLImit;
                                $ClassSameDay = $MemberInfo->ClassSameDay;
                                $BalanceClass = $MemberInfo->BalanceClass;

                                $LimitClassMorning = $MemberInfo->LimitClassMorning;
                                $LimitClassEvening = $MemberInfo->LimitClassEvening;
                                $LimitClassMonth = $MemberInfo->LimitClassMonth;

                                $TrueBalanceClass = $MemberShipInfo->TrueBalanceValue;
                                $LimitClass = $MemberShipInfo->LimitClass;

                                if ($TrueClasessFinal != '') {
                                    DB::table('classstudio_act')
                                        ->where('ClientActivitiesId', '=', $ClientActivityId)
                                        ->where('ClientId', '=', $ClientId)
                                        ->where('CompanyNum', '=', $CompanyNum)
                                        ->where('ClassDate', '>=', date('Y-m-d'))
                                        ->whereIn('Status', array('12', '9'))
                                        ->update(array('ClientActivitiesId' => $MemberShipInfo->id, 'MemberShip' => $MemberShipInfo->MemberShip, 'Department' => $MemberShipInfo->Department, 'TrueClasess' => $TrueClasessFinal));
                                }

                                if ($TrueClientId != '') {
                                    $FixTrueClientId = $TrueClientId;
                                } else {
                                    $FixTrueClientId = $ClientId;
                                }

                                if ($TrueClasessFinal != '') {
                                    DB::table('classstudio_dateregular')
                                        ->where('id', '=', $GetClientRegular->RegularClassId)
                                        ->where('CompanyNum', '=', $CompanyNum)
                                        ->where('ClientId', '=', $FixTrueClientId)
                                        ->update(array('ClientActivitiesId' => $MemberShipInfo->id, 'MemberShipType' => $MemberShipInfo->MemberShip));


                                    if ($ClientActivityInfo->Department == '1') {
                                        DB::table('client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->where('MemberShip', '=', $ClientActivityInfo->MemberShip)
                                            ->where('Department', '=', '1')
                                            ->where('Status', '=', '0')
                                            ->where('TrueDate', '<=', date('Y-m-d'))
                                            ->update(array('Status' => '3'));
                                    } else if ($ClientActivityInfo->Department == '2') {

                                        DB::table('client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->where('MemberShip', '=', $ClientActivityInfo->MemberShip)
                                            ->where('Department', '=', '2')
                                            ->where('Status', '=', '0')
                                            ->where('TrueBalanceValue', '<=', '0')
                                            ->update(array('Status' => '3'));

                                        DB::table('client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->where('MemberShip', '=', $ClientActivityInfo->MemberShip)
                                            ->where('Department', '=', '2')
                                            ->where('Status', '=', '0')
                                            ->where('TrueDate', '<=', date('Y-m-d'))
                                            ->update(array('Status' => '3'));

                                    } else if ($ClientActivityInfo->Department == '3') {
                                        ///// סגירת מנוי היכרות/התנסות

                                        DB::table('client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->where('Department', '=', '3')
                                            ->where('Status', '=', '0')
                                            ->where('TrueBalanceValue', '<=', '0')
                                            ->update(array('Status' => '3'));

                                        DB::table('client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->where('Department', '=', '3')
                                            ->where('Status', '=', '0')
                                            ->where('TrueDate', '<=', date('Y-m-d'))
                                            ->update(array('Status' => '3'));
                                    }


                                }

                                $Ststus = '0';
                                $BrackStatus = '1';
                            }


                            if ($BrackStatus == '1') {
                                $Ststus = '0';
                            } else {
                                $Ststus = '999';
                            }

                        } /// סיום בדיקת התאמה


                        if ($BrackStatus == '1') {
                            break;
                        } else {
                            $BrackStatus = '0';
                        }


                    } /// סיום לולאה
                } /// סיום בדיקת נתונים


            }


        }

    }

//////////////////////////////////////////////////////////////// סיום עדכון מנוי בשיבוץ קבוע ///////////////////////////////////////////////////////


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
    if(isset($GetClientRegular)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientRegular),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
