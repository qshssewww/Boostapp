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





//////////////////////////////////////////////////////////////// כרטיסיה ניקוב אחרון ///////////////////////////////////////////////////////


try {


    $Clients = DB::table('client_activities')->where('Status', '=', '0')->whereIn('Department', array(2))->where('ActBalanceValue', '=', '1')->where('BalanceValue', '>', '1')->where('CardStatus', '=', '0')->where('ClientStatus', '=', '0')->where('Freez', '!=', '1')->get();

    $LogDateTime = date('Y-m-d G:i:s');
    foreach ($Clients as $Client) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $Client->CompanyNum)->where('Status', '=', '0')->first();

        if (@$CheckSettings->id != '') {


            /// ניקוב משיבוץ קבוע להגיע/מומש


            $CompanyNum = $Client->CompanyNum;


            $Date = date('Y-m-d');

            $Dates = date('Y-m-d H:i:s');

            $Time = '09:00:00';


            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '8')->first();


            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $Client->ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

            $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $CompanyNum)->first();

            $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();


            $MembershipType = @$AppSettings->MembershipType;

            $CheckCountMembership = '0';


                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Status', '=', '0')->where('MemberShip', '=', $Client->MemberShip)->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();


                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('MemberShip', '=', $Client->MemberShip)->where('ActBalanceValue', '>=', '1')->count();




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

            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName ,$Template->Content);

            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '',$Content1);

            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);

            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["subscription_name"], $Client->ItemText ?? '',$Content3);

//            $ContentTrue = $Content4;


//            $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים

            $Subject = $Template->Subject;


            if (($TemplateStatus != '1' && $CheckCountMembership <= '0' && $MembershipType == '1') || ($TemplateStatus != '1' && $MembershipType == '0' && $CheckCountMembership <= '0')) {

                if ($TemplateSendOption != 'BA000') {

                    DB::table('boostapp.appnotification')->insertGetId(

                        array('CompanyNum' => $CompanyNum, 'ClientId' => $Client->ClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));

                }

                if ($SendStudioOption != 'BA000') {

                    DB::table('boostapp.appnotification')->insertGetId(

                        array('CompanyNum' => $CompanyNum, 'Type' => '3', 'ClientId' => $Client->ClientId, 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));

                }

            }


            DB::table('client_activities')
                ->where('id', $Client->id)
                ->where('CompanyNum', $Client->CompanyNum)
                ->update(array('CardStatus' => '1'));


        }


    }


    $ThisDate = date('Y-m-d');

    $ThisDay = date('l');

    $ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// כרטיסיה הסתיימה ///////////////////////////////////////////////////////


    $Clients = DB::table('client_activities')->where('Status', '=', '0')->whereIn('Department', array(2))->where('ActBalanceValue', '<=', '0')->where('CardStatus', '=', '1')->where('ClientStatus', '=', '0')->where('Freez', '!=', '1')->get();


    foreach ($Clients as $Client) {


        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $Client->CompanyNum)->where('Status', '=', '0')->first();

        if (@$CheckSettings->id != '') {


            /// ניקוב משיבוץ קבוע להגיע/מומש


            $CompanyNum = $Client->CompanyNum;


            $Date = date('Y-m-d');

            $Dates = date('Y-m-d H:i:s');

            $Time = '09:00:00';


            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '9')->first();


            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $Client->ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

            $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $CompanyNum)->first();


            $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();


            $MembershipType = @$AppSettings->MembershipType;

            $CheckCountMembership = '0';


            if ($MembershipType == '0') {

                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Status', '=', '0')->where('MemberShip', '=', $Client->MemberShip)->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();

                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('MemberShip', '=', $Client->MemberShip)->where('ActBalanceValue', '>=', '1')->count();

            } else {

                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Status', '=', '0')->where('TrueDate', '!=', '')->where('TrueDate', '>', $ThisDate)->count();

                $CheckCountMembership += DB::table('client_activities')->where('id', '!=', $Client->id)->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('Department', '=', '2')->where('Status', '=', '0')->where('ActBalanceValue', '>=', '1')->count();

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

            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName,$Template->Content);

            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '',$Content1);

            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);

            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["subscription_name"], $Client->ItemText ?? '',$Content3);

//            $ContentTrue = $Content4;
//            $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים

            $Subject = $Template->Subject;


            if (($TemplateStatus != '1' && $CheckCountMembership <= '0' && $MembershipType == '1') || ($TemplateStatus != '1' && $MembershipType == '0' && $CheckCountMembership <= '0')) {

                if ($TemplateSendOption != 'BA000') {

                    DB::table('boostapp.appnotification')->insertGetId(

                        array('CompanyNum' => $CompanyNum, 'ClientId' => $Client->ClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));

                }

                if ($SendStudioOption != 'BA000') {

                    DB::table('boostapp.appnotification')->insertGetId(

                        array('CompanyNum' => $CompanyNum, 'Type' => '3', 'ClientId' => $Client->ClientId, 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));

                }

            }


            DB::table('client_activities')
                ->where('id', $Client->id)
                ->where('CompanyNum', $Client->CompanyNum)
                ->update(array('CardStatus' => '2'));


        }


    }


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
    if(isset($Client)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($Client),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


?>

