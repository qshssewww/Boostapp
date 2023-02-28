<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// עדכון טבלת לקוחות + ספירת מנויים ///////////////////////////////////////////////////////
try {


    $GetClientActivitys = DB::table('client_activities')->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('Department', '!=', '4')->groupBy('ClientId')->select('id', 'CompanyNum', 'MemberShip', 'ClientId')->get();

    foreach ($GetClientActivitys as $GetClientActivity) {
        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientActivity->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {


            ///// ספירת סוג מנוי
            $CompanyNum = $GetClientActivity->CompanyNum;
            $MemberShip = $GetClientActivity->MemberShip;
            $ClientId = $GetClientActivity->ClientId;


            /////// עדכון כרטיס לקוח

            $MemberShipText = '';
            $MemberShipText .= '{"data": [';
            $Taski = '1';
            $ActiveMembership = '0';
            $GetTasks = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->orderBy('CardNumber', 'ASC')
                ->select('id', 'ItemText', 'TrueDate', 'LimitClass', 'TrueBalanceValue', 'LimitClass', 'TrueClientId')
                ->get();
            $TaskCount = count($GetTasks);
            if ($TaskCount >= '1') {
                foreach ($GetTasks as $GetTask) {

                    $ItemText = str_replace("'", "`", $GetTask->ItemText);


                    if ($Taski < $TaskCount) {
                        $MemberShipText .= '{"ItemText": "' . htmlentities($ItemText) . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"},';
                    } else {
                        $MemberShipText .= '{"ItemText": "' . htmlentities($ItemText) . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"}';
                    }

                    $ActiveMembership = '1';

                    if ($GetTask->TrueClientId != '0') {

                        $myArray = explode(',', $GetTask->TrueClientId);

                        foreach ($myArray as $value) {
                            $UpdateClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $value)->first();
                            if($UpdateClient) {
                                DB::table('client')
                                    ->where('id', $UpdateClient->id)
                                    ->where('CompanyNum', $CompanyNum)
                                    ->update(array('ChangeDate' => $UpdateClient->ChangeDate, 'ActivityClientId' => $ClientId));
                            }

                        }

                    }


                    ++$Taski;

                }


            } else {


                $UpdateClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClientId)->first();
                $ActivityClientId = @$UpdateClient->ActivityClientId;


                $GetTasks2 = DB::table('client_activities')
                    ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ActivityClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                    ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ActivityClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                    ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ActivityClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                    ->Orwhere('TrueBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ActivityClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                    ->orderBy('CardNumber', 'ASC')
                    ->select('id', 'ItemText', 'TrueDate', 'LimitClass', 'TrueBalanceValue', 'LimitClass', 'TrueClientId')
                    ->get();
                foreach ($GetTasks2 as $GetTask) {


                    $ActiveMembership = '1';

                    if ($GetTask->TrueClientId != '0') {

                        $myArray = explode(',', $GetTask->TrueClientId);

                        foreach ($myArray as $value) {
                            $UpdateClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $value)->first();
                            DB::table('client')
                                ->where('id', $UpdateClient->id)
                                ->where('CompanyNum', $CompanyNum)
                                ->update(array('ChangeDate' => $UpdateClient->ChangeDate, 'ActiveMembership' => $ActiveMembership));

                        }

                    }


                }


            }

            $MemberShipText .= ']}';

            $ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->select('id', 'ChangeDate')->first();

            if (@$ClientInfo->id != '') {
                DB::table('client')
                    ->where('id', $ClientId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('MemberShipText' => $MemberShipText, 'ChangeDate' => $ClientInfo->ChangeDate, 'ActiveMembership' => $ActiveMembership));
            }


        } else {

            DB::table('client_activities')
                ->where('id', $GetClientActivity->id)
                ->where('CompanyNum', $GetClientActivity->CompanyNum)
                ->update(array('Status' => '2'));


        }


    }


//////////////////////////////////////////////////////////////// סיום טבלת לקוחות + ספירת מנויים ///////////////////////////////////////////////////////


    $ThisDate = date('Y-m-d');
    $ThisDay = date('l');
    $ThisTime = date('H:i:s');

//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////
    $Cron->end();
}

/////////////////////////////////////////////////////// שמירת לוגים במקרה של נפילה ///////////////////////////////////////////////////////

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

?>
