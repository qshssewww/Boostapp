<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/CompanyProductSettings.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/ClassStudioAct.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

			$Vaild_TypeOption = array(
			1 => "day",
			2 => "week",
			3 => "month",
            4 => "year"     
	        );	

//////////////////////////////////////////////////////////////// הפשרת מנוי אוטומטי ///////////////////////////////////////////////////////

try {
    $GetClientFreezs = DB::table('client_activities')->where('Status', '=', '0')->where('Freez', '=', '1')->where('EndFreez', '<=', $ThisDate)->get();

    foreach ($GetClientFreezs as $GetClientFreez) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientFreez->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {


            $Date = date('Y-m-d');
            $Time = '09:00:00';
            $Dates = date('Y-m-d H:i:s');


            $EndFreez = $GetClientFreez->EndFreez;
            $StartFreez = $GetClientFreez->StartFreez;

            if ($EndFreez > date('Y-m-d')) {

                $startTimeStamp = strtotime($StartFreez);
                $endTimeStamp = strtotime(date('Y-m-d'));

                $timeDiff = abs($endTimeStamp - $startTimeStamp);

                $numberDays = $timeDiff / 86400;


                $FreezDays = (int)$numberDays;

            } else {
                $FreezDays = $GetClientFreez->FreezDays;
            }
            $TrueDate = $GetClientFreez->TrueDate;

            $ItemsMin = '+' . $FreezDays . ' days';

            if (!empty($TrueDate)) {
                $ClassTrueDate = date("Y-m-d", strtotime($ItemsMin, strtotime($TrueDate)));
            } else {
                $ClassTrueDate = null;
            }

            $time = date('Y-m-d H:i:s');

            $FreezLog = '';
            $FreezLog .= '{"data": [';


            if ($GetClientFreez->FreezEndLog != '') {
                $Loops = json_decode($GetClientFreez->FreezEndLog, true);
                foreach ($Loops['data'] as $key => $val) {

                    $FreezDaysDB = $val['FreezDays'];
                    $DatesDB = $val['Dates'];
                    $UserIdDB = $val['UserId'];

                    $FreezLog .= '{"FreezDays": "' . $FreezDaysDB . '", "Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '"},';

                }
            }

            $FreezLog .= '{"FreezDays": "' . $FreezDays . '", "Dates": "' . $time . '", "UserId": ""}';

            $FreezLog .= ']}';

            $ItemsInfo = DB::table('items')->where('CompanyNum', '=', $GetClientFreez->CompanyNum)->where('id', '=', $GetClientFreez->ItemId)->first();

            $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($GetClientFreez->CompanyNum)->NotificationDays ?? 0;

            $Vaild_TypeOptions = @$Vaild_TypeOption['1'];
            $ItemsTime = '-' . $NotificationDays . ' ' . $Vaild_TypeOptions;

            $time = strtotime($ClassTrueDate);
            $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

            if ($NotificationDays == '0') {
                $NotificationDate = NULL;
            }


            DB::table('client_activities')
                ->where('ClientId', $GetClientFreez->ClientId)
                ->where('CompanyNum', $GetClientFreez->CompanyNum)
                ->where('id', $GetClientFreez->id)
                ->update(array('Freez' => '2', 'TrueDate' => $ClassTrueDate, 'StudioVaildDate' => $ClassTrueDate, 'FreezEndLog' => $FreezLog, 'NotificationDays' => $NotificationDate));

            DB::table('client')
                ->where('CompanyNum', $GetClientFreez->CompanyNum)
                ->where('id', $GetClientFreez->ClientId)
                ->update(array('FreezStatus' => '0'));

            //// הפשרת מנוי אוטומטי
            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $GetClientFreez->CompanyNum)->where('Type', '=', '20')->first();

            $ClientInfo = DB::table('client')->where('id', '=', $GetClientFreez->ClientId)->where('CompanyNum', '=', $GetClientFreez->CompanyNum)->first();
            $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $GetClientFreez->CompanyNum)->first();

            $TemplateStatus = $Template->Status;
            $TemplateSendOption = $Template->SendOption;
            $SendStudioOption = $Template->SendStudioOption;
            $Type = '0';

            if ($TemplateSendOption == 'BA999') {
                $Type = '0';
            } elseif ($TemplateSendOption == 'BA000') {
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
            $Text = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);

            $Subject = $Template->Subject;

            if ($TemplateStatus != '1') {
                if ($TemplateSendOption != 'BA000') {
                    $AddNotification = DB::table('appnotification')->insertGetId(
                        array('CompanyNum' => $GetClientFreez->CompanyNum, 'ClientId' => $GetClientFreez->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                }
                if ($SendStudioOption != 'BA000') {
                    $AddNotification = DB::table('appnotification')->insertGetId(
                        array('CompanyNum' => $GetClientFreez->CompanyNum, 'Type' => '3', 'ClientId' => $GetClientFreez->ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                }
            }


            $CompanyNum = $GetClientFreez->CompanyNum;
            $ClientId = $GetClientFreez->ClientId;


            $GetClientClasses = DB::table('classstudio_act')->where('CompanyNum', '=', $CompanyNum)->where('RegularClass', '=', '1')->where('ClientId', '=', $ClientId)->where('Status', '19')->where('ClassDate', '>=', date('Y-m-d'))->get();

            foreach ($GetClientClasses as $GetClientClass) {

                $ClassInfo = DB::table('classstudio_date')->where('id', '=', $GetClientClass->ClassId)->where('CompanyNum', '=', $CompanyNum)->first();

                $ClassCounts = DB::table('classstudio_act')->where('ClassId', '=', $GetClientClass->ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();

                $Dates = date('Y-m-d G:i:s');
                $UserId = '0';
                $UserName = '';

                if ($GetClientClass->RegularClass == '1') {

                    $RegularDates = DB::table('classstudio_dateregular')->where('id', '=', $GetClientClass->RegularClassId)->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->first();

                    if (@$RegularDates->StatusType != '') {
                        $CheckNewStatus = DB::table('class_status')->where('id', '=', $RegularDates->StatusType)->first();
                    } else {
                        $CheckNewStatus = DB::table('class_status')->where('id', '=', '12')->first();
                    }

                } else {
                    $CheckNewStatus = DB::table('class_status')->where('id', '=', '1')->first();
                }

                if ($ClassCounts >= @$ClassInfo->MaxClient) {
                    $CheckNewStatus = DB::table('class_status')->where('id', '=', '9')->first();
                }

                $Status = $CheckNewStatus->id;
                $StatusCount = $CheckNewStatus->StatusCount;

                $StatusJson = '';
                $StatusJson .= '{"data": [';

                if ($GetClientClass->StatusJson != '') {
                    $Loops = json_decode($GetClientClass->StatusJson, true);
                    foreach ($Loops['data'] as $key => $val) {

                        $DatesDB = $val['Dates'];
                        $UserIdDB = $val['UserId'];
                        $StatusDB = $val['Status'];
                        $StatusTitleDB = $val['StatusTitle'];
                        $UserNameDB = $val['UserName'];

                        $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                    }
                }

                $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "' . $UserId . '", "Status": "19", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": "' . $UserName . '"}';

                $StatusJson .= ']}';

                (new ClassStudioAct($GetClientClass->id))->update([
                    'Status' => $Status,
                    'StatusJson' => $StatusJson,
                    'StatusCount' => $StatusCount,
                ]);

                //// עדכון שיעור ברשימת משתתפים

                $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $GetClientClass->ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
                $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $GetClientClass->ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();


                DB::table('classstudio_date')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $GetClientClass->ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList));

                DB::table('classlog')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'ClassId' => $GetClientClass->ClassId, 'ClientId' => $ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0', 'numOfClients' => $ClientRegister));

            }


        }


    }


//////////////////////////////////////////////////////////////// אי הגעה לסטודיו ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClientFreez)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientFreez),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


