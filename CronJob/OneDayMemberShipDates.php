<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

//////////////////////////////////////////////////////////////// בדיקת התראת תוקף מנוי ///////////////////////////////////////////////////////

try {
    $GetClientActivitys = DB::table('client_activities')
        ->where('Status', '=', '0')
        ->whereIn('Department', array(1, 2))
        ->where('TrueDate', '!=', '')
        ->where('NotificationDays', '=', $ThisDate)
        ->where('KevaAction', '=', '0')
        ->where('Freez', '!=', '1')
        ->where('ClientStatus', '=', '0')
        ->whereIn('CardStatus', array(0, 1))
        ->get();

    foreach ($GetClientActivitys as $GetClientActivity) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {

            $CheckCountMembership = '0';
            $Date = date('Y-m-d');
            $Time = '09:30:00';
            $Dates = date('Y-m-d H:i:s');

            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('Type', '=', '7')->first();

            $ClientInfo = DB::table('client')->where('id', '=', $GetClientActivity->ClientId)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();

            if (!empty($ClientInfo) && $ClientInfo->Status != '1') {

                $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();

                $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();

                $MembershipType = @$AppSettings->MembershipType;

                $CheckCountMembership = '0';


                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Status', '=', '0')->where('MemberShip', '=', $GetClientActivity->MemberShip)->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();
                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('MemberShip', '=', $GetClientActivity->MemberShip)->where('ActBalanceValue', '>=', '1')->count();


                if ($CheckCountMembership == '') {
                    $CheckCountMembership = '0';
                }

                $TemplateStatus = $Template->Status;
                $TemplateSendOption = $Template->SendOption;
                $SendStudioOption = $Template->SendStudioOption;
                $Type = '0';

                if ($TemplateSendOption == 'BA999') {
                    $Type = '0';
                } else if ($TemplateSendOption == 'BA000') {
                } else {
                    $myArray = explode(',', $TemplateSendOption);
                    $Type2 = (in_array('2', $myArray)) ? '2' : '';
                    $Type1 = (in_array('1', $myArray)) ? '1' : '';
                    $Type0 = (in_array('0', $myArray)) ? '0' : '';

                    if ($Type2 != '') {
                        $Type = $Type2;
                    }
                    if ($Type1 != '') {
                        $Type = $Type1;
                    }
                    if ($Type0 != '') {
                        $Type = $Type0;
                    }

                }

                /// עדכון תבנית הודעה
                $Tokef = with(new DateTime($GetClientActivity->TrueDate))->format('d/m/Y') . ' ' . htmlentities($GetClientActivity->ItemText);
                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName, $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$ClientInfo->CompanyName, $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ClientInfo->FirstName, $Content2);
                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["membership_expire_date"], @$Tokef, $Content3);
                $ContentTrue = $Content4;

                $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                $Subject = $Template->Subject;

                if (($TemplateStatus != '1' && $CheckCountMembership <= '0' && $MembershipType == '1') || ($TemplateStatus != '1' && $MembershipType == '0' && $CheckCountMembership <= '0')) {
                    if ($TemplateSendOption != 'BA000') {
                        $AddNotification = DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $GetClientActivity->CompanyNum, 'ClientId' => $GetClientActivity->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                    }
                    if ($SendStudioOption != 'BA000') {
                        $AddNotification = DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $GetClientActivity->CompanyNum, 'Type' => '3', 'ClientId' => $GetClientActivity->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                    }
                }


                $CheckCountMembership = '0';

            } else {

                DB::table('client_activities')
                    ->where('id', $GetClientActivity->id)
                    ->where('CompanyNum', $GetClientActivity->CompanyNum)
                    ->update(array('Status' => '2'));


            }


        }


    }

    $ThisDate = date('Y-m-d');
    $ThisDay = date('l');
    $ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// בדיקת תוקף מנוי ///////////////////////////////////////////////////////

    $GetClientActivitys = DB::table('client_activities')->where('Status', '=', '0')->whereIn('Department', array(1, 2))->where('TrueDate', '!=', '')->where('TrueDate', '=', $ThisDate)->where('KevaAction', '=', '0')->where('Freez', '!=', '1')->where('ClientStatus', '=', '0')->whereIn('CardStatus', array(0, 1))->get();

    foreach ($GetClientActivitys as $GetClientActivity) {


        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('Status', '=', '0')->first();
        if (!empty($CheckSettings)) {

            $Date = date('Y-m-d');
            $Time = '09:30:00';
            $Dates = date('Y-m-d H:i:s');
            $ClientInfo = DB::table('client')->where('id', '=', $GetClientActivity->ClientId)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();

            if ((in_array($GetClientActivity->Department, [2, 3]) && isset($GetClientActivity->ActBalnaceValue) && $GetClientActivity->ActBalnaceValue <= 0) || ($ClientInfo && $ClientInfo->Status == 1)) {

            } else {


                $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('Type', '=', '10')->first();
                $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();
                $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->first();

                $MembershipType = @$AppSettings->MembershipType;
                $CheckCountMembership = '0';

                if ($MembershipType == '0') {
                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Status', '=', '0')->where('MemberShip', '=', $GetClientActivity->MemberShip)->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();
                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('MemberShip', '=', $GetClientActivity->MemberShip)->where('ActBalanceValue', '>=', '1')->count();
                } else {
                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Status', '=', '0')->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();
                    $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $GetClientActivity->id)->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('ClientId', '=', $GetClientActivity->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('ActBalanceValue', '>=', '1')->count();
                }

                if ($CheckCountMembership == '') {
                    $CheckCountMembership = '0';
                }


                $TemplateStatus = $Template->Status;
                $TemplateSendOption = $Template->SendOption;
                $SendStudioOption = $Template->SendStudioOption;
                $Type = '0';

                if ($TemplateSendOption == 'BA999') {
                    $Type = '0';
                } else if ($TemplateSendOption == 'BA000') {
                } else {
                    $myArray = explode(',', $TemplateSendOption);
                    $Type2 = (in_array('2', $myArray)) ? '2' : '';
                    $Type1 = (in_array('1', $myArray)) ? '1' : '';
                    $Type0 = (in_array('0', $myArray)) ? '0' : '';

                    if (@$Type2 != '') {
                        $Type = $Type2;
                    }
                    if (@$Type1 != '') {
                        $Type = $Type1;
                    }
                    if (@$Type0 != '') {
                        $Type = $Type0;
                    }

                }

                /// עדכון תבנית הודעה
                $Tokef = with(new DateTime($GetClientActivity->TrueDate))->format('d/m/Y') . ' ' . htmlentities($GetClientActivity->ItemText);
                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName, $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$ClientInfo->CompanyName, $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ClientInfo->FirstName, $Content2);
                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["membership_expire_date"], @$Tokef, $Content3);
                $ContentTrue = $Content4;


                $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                $Subject = $Template->Subject;

                if (($TemplateStatus != '1' && $CheckCountMembership == '0' && $MembershipType == '1') || ($TemplateStatus != '1' && $MembershipType == '0' && $CheckCountMembership == '0')) {
                    if ($TemplateSendOption != 'BA000') {
                        $AddNotification = DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $GetClientActivity->CompanyNum, 'ClientId' => $GetClientActivity->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                    }
                    if ($SendStudioOption != 'BA000') {
                        $AddNotification = DB::table('appnotification')->insertGetId(
                            array('CompanyNum' => $GetClientActivity->CompanyNum, 'Type' => '3', 'ClientId' => $GetClientActivity->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                    }
                }


                if ($MembershipType == '0' && $CheckCountMembership >= '1') {

//              //// סגירת מנוי קודם
//               
                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('MemberShip', '=', $GetClientActivity->MemberShip)
                        ->where('Department', '=', '1')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('MemberShip', '=', $GetClientActivity->MemberShip)
                        ->where('Department', '=', '2')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('ActBalanceValue', '<=', '0')
                        ->update(array('Status' => '3'));

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('MemberShip', '=', $GetClientActivity->MemberShip)
                        ->where('Department', '=', '2')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                    ///// סגירת מנוי היכרות/התנסות

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('MemberShip', '=', $GetClientActivity->MemberShip)
                        ->where('Department', '=', '3')
                        ->where('Status', '=', '0')
                        ->where('ActBalanceValue', '<=', '0')
                        ->update(array('Status' => '3'));

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('MemberShip', '=', $GetClientActivity->MemberShip)
                        ->where('Department', '=', '3')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                } else if ($MembershipType == '1' && $CheckCountMembership >= '1') {

                    //// סגירת מנוי קודם

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('Department', '=', '1')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('Department', '=', '2')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('ActBalanceValue', '<=', '0')
                        ->update(array('Status' => '3'));

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('Department', '=', '2')
                        ->where('Freez', '!=', '1')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                    ///// סגירת מנוי היכרות/התנסות

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('Department', '=', '3')
                        ->where('Status', '=', '0')
                        ->where('ActBalanceValue', '<=', '0')
                        ->update(array('Status' => '3'));

                    DB::table('client_activities')
                        ->where('ClientId', $GetClientActivity->ClientId)
                        ->where('CompanyNum', $GetClientActivity->CompanyNum)
                        ->where('Department', '=', '3')
                        ->where('Status', '=', '0')
                        ->where('TrueDate', '<=', date('Y-m-d'))
                        ->update(array('Status' => '3'));


                }


            }


            ///// ספירת סוג מנוי

            $MemberShip = $GetClientActivity->MemberShip;
            $CompanyNum = $GetClientActivity->CompanyNum;
            $ClientId = $GetClientActivity->ClientId;

            $MemberShipCounts = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->get();

            $MemberShipCount = count($MemberShipCounts);

            DB::table('membership_type')
                ->where('id', $MemberShip)
                ->where('CompanyNum', $CompanyNum)
                ->update(array('Count' => $MemberShipCount));


            /////// עדכון כרטיס לקוח

            $MemberShipText = '';
            $MemberShipText .= '{"data": [';
            $Taski = '1';
            $GetTasks = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->orderBy('CardNumber', 'ASC')->get();
            $TaskCount = count($GetTasks);

            foreach ($GetTasks as $GetTask) {

                if ($Taski < $TaskCount) {
                    $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"},';
                } else {
                    $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"}';
                }


                ++$Taski;
            }
            $MemberShipText .= ']}';

            DB::table('client')
                ->where('id', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->update(array('MemberShipText' => $MemberShipText));


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
    if(isset($GetClientActivity)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientActivity),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
