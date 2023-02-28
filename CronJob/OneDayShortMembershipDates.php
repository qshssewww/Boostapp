<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/CompanyProductSettings.php';

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


			$Vaild_TypeOption = array(
			1 => "day",
			2 => "week",
			3 => "month",
            4 => "year"     
	        );	

//////////////////////////////////////////////////////////////// הפשרת מנוי אוטומטי ///////////////////////////////////////////////////////

try {

    $GetClientBlocks = DB::table('appsettings')->where('MemberShipLimitType', '!=', '2')->get();

    foreach ($GetClientBlocks as $GetClientBlock) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClientBlock->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {

            $CompanyNum = $GetClientBlock->CompanyNum;
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

            $GetClasses = DB::table('classstudio_date')->where('StartDate', '=', $TrueDate)->get();

            foreach ($GetClasses as $GetClasse) {

                $Clients = DB::table('classstudio_act')->where('ClassId', '=', $GetClasse->id)->where('CompanyNum', $GetClasse->CompanyNum)->whereIn('Status', array(4, 8))->get();
                foreach ($Clients as $ClassAct) {

                    $ClientId = $ClassAct->FixClientId;
                    $ClinetId = $ClassAct->FixClientId;
                    $NewStatus = $ClassAct->Status;

                    $ClientBalanceValue = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->ClientActivitiesId)->first();

                    $ItemName = htmlentities(@$ClientBalanceValue->ItemText);

                    if (@$ClientBalanceValue->id != '') {

                        if ($ClientBalanceValue->Department == '1' && $MemberShipLimitType != '2') {

                            if (($NewStatus == '4' && $MemberShipLimitLateCancel == '1') || ($NewStatus == '8' && $MemberShipLimitNoneShow == '1')) {


                                //// ספירת נקודות רעות

                                $Date = date('Y-m-d');
                                $Time = date('H:i:s');
                                $Dates = date('Y-m-d H:i:s');

                                $CheckBedPoint = DB::table('boostapplogin.badpoint')->where('CompanyNum', '=', $CompanyNum)->where('ClinetId', '=', $ClinetId)->where('ClassId', '=', $ClassAct->ClassId)->first();
                                if (@$CheckBedPoint->id == '') {
                                    DB::table('boostapplogin.badpoint')->insertGetId(
                                        array('CompanyNum' => $CompanyNum, 'ClinetId' => $ClientId, 'Dates' => $Date, 'Time' => $Time, 'ClassId' => $ClassAct->ClassId));
                                }

                                $DaysMemberShipLimitFix = '-' . $DaysMemberShipLimit . ' days';
                                $ToDate = date("Y-m-d", strtotime($DaysMemberShipLimitFix, strtotime($Date)));

                                $CountBadPoint = DB::table('boostapplogin.badpoint')->whereBetween('Dates', array($ToDate, $Date))->where('ClinetId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->count();

                                if ($CountBadPoint >= $MemberShipLimit && $MemberShipLimit > '0' && $MemberShipLimitType == '0') {


                                    $BlockDate = date('Y-m-d');

                                    if ($MemberShipLimitUnBlock == '0') {
                                        $ExtraDates = '+' . $MemberShipLimitUnBlockDays . ' day';
                                        $BlockDate = date('Y-m-d', strtotime($ExtraDates, strtotime(date('Y-m-d'))));
                                    } else {
                                        $BlockDate = date('Y-m-d');
                                    }


                                    $StatusBadPoint = '1';
                                    DB::table('boostapplogin.badpoint')
                                        ->where('ClinetId', $ClientId)
                                        ->where('CompanyNum', $CompanyNum)
                                        ->update(array('Status' => '1'));

                                    DB::table('boostapplogin.studio')
                                        ->where('ClientId', $ClientId)
                                        ->where('CompanyNum', $CompanyNum)
                                        ->update(array('CountBadPoint' => $CountBadPoint, 'StatusBadPoint' => $StatusBadPoint, 'BlockDate' => $BlockDate));


                                    //// קליטת לוג מערכת
                                    $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->ClassId)->first();
                                    $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
                                    $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
                                    $LogClassName = $ClassInfo->ClassName;

                                    $LogText = lang('following_class_cron').' '.$LogClassName.' '.lang('in_date_cron').' '.$LogClassDate.' '.lang('and_in_time_cron').' '.$LogClassTime.' '.lang('blocked_from_cancel_cron');

                                    $SubjectBad = lang('blocked_from_app_cron');
                                    $TextNotificationBad = $LogText;

                                    $Date = date('Y-m-d');
                                    $Time = '08:30:00';
                                    $Dates = date('Y-m-d H:i:s');


                                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '12')->first();

                                    $ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

                                    $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

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
                                    $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);
//                                    $ContentTrue = $Content3;


//                                    $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                                    $Subject = $Template->Subject;

                                    if ($TemplateStatus != '1') {
                                        if( $SendStudioOption != 'BA000') {
                                            DB::table('boostapp.appnotification')->insertGetId(
                                                array(
                                                    'CompanyNum' => $CompanyNum,
                                                    'Type' => '3',
                                                    'ClientId' => $ClientId,
                                                    'Subject' => $SubjectBad,
                                                    'Text' => $TextNotificationBad,
                                                    'Dates' => $Dates,
                                                    'UserId' => '0',
                                                    'RoleId' => '3',
                                                    'Date' => $Date,
                                                    'Time' => $Time,
                                                    'SendStudioOption' => 'BA999'
                                                )
                                            );
                                        }
                                        if ($TemplateSendOption != 'BA000') {
                                            $AddNotification = DB::table('appnotification')->insertGetId(
                                                array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                                        }
                                    }


                                } else if ($CountBadPoint >= $MemberShipLimit && $MemberShipLimit > '0' && $MemberShipLimitType == '1') {


                                    $SubjectBad = lang('shorten_subscription_corn');


                                    $StatusBadPoint = '1';
                                    DB::table('boostapplogin.badpoint')
                                        ->where('ClinetId', $ClientId)
                                        ->where('CompanyNum', $CompanyNum)
                                        ->update(array('Status' => '1'));

                                    /// קיצור תוקף מנוי
                                    $ItemsTime = '-' . $MemberShipLimitDays . ' day';
                                    $OldTrueDate = strtotime($ClientBalanceValue->TrueDate);
                                    $NewTrueDate = date("Y-m-d", strtotime($ItemsTime, $OldTrueDate));

                                    $StudioVaildDateLog = '';
                                    $StudioVaildDateLog .= '{"data": [';
                                    $time = date('Y-m-d G:i:s');

                                    if ($ClientBalanceValue->StudioVaildDateLog != '') {
                                        $Loops = json_decode($ClientBalanceValue->StudioVaildDateLog, true);
                                        foreach ($Loops['data'] as $key => $val) {

                                            $StudioVaildDateDB = $val['StudioVaildDate'];
                                            $TrueDateDB = $val['TrueDate'];
                                            $DatesDB = $val['Dates'];
                                            $UserIdDB = $val['UserId'];
                                            $ReasonDB = $val['Reason'];

                                            $StudioVaildDateLog .= '{"StudioVaildDate": "' . $StudioVaildDateDB . '", "TrueDate": "' . $TrueDateDB . '", "Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Reason":"' . $ReasonDB . '"},';

                                        }
                                    }

                                    $StudioVaildDateLog .= '{"StudioVaildDate": "' . $NewTrueDate . '", "TrueDate": "' . $NewTrueDate . '", "Dates": "' . $time . '", "UserId": "0", "Reason":"' . $SubjectBad . '"}';

                                    $StudioVaildDateLog .= ']}';

                                    $ItemsInfo = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClientBalanceValue->ItemId)->first();

                                    $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum)->NotificationDays ?? 0; // התראה לפני סוף מנוי

                                    $ItemsTime = '-' . $NotificationDays . ' day';

                                    $time = strtotime($NewTrueDate);
                                    $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

                                    if ($NotificationDays == '0') {
                                        $NotificationDate = null;
                                    }


                                    DB::table('client_activities')
                                        ->where('id', $ClientBalanceValue->id)
                                        ->where('CompanyNum', $CompanyNum)
                                        ->update(array('TrueDate' => $NewTrueDate, 'StudioVaildDate' => $NewTrueDate, 'StudioVaildDateLog' => $StudioVaildDateLog, 'NotificationDays' => $NotificationDate));


                                    //// קליטת לוג מערכת
                                    $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->ClassId)->first();
                                    $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
                                    $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
                                    $LogClassName = $ClassInfo->ClassName;

                                    $LogText = lang('following_class_cron').' '.$LogClassName.' '.lang('in_date_cron').' '.$LogClassDate.' '.lang('and_in_time_cron').' '.$LogClassTime.' '.lang('subscription_short_due_cancelation');

                                    $SubjectBad = lang('subscription_short_cron');
                                    $TextNotificationBad = $LogText;

                                    $Date = date('Y-m-d');
                                    $Time = '08:30:00';
                                    $Dates = date('Y-m-d H:i:s');

                                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '26')->first();

                                    $ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

                                    $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

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


                                    $FixOldTrueDate = date('d/m/Y', $OldTrueDate);
                                    $FixNewTrueDate = date("d/m/Y", strtotime($NewTrueDate));

                                    /// עדכון תבנית הודעה
                                    $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"] ,$CompanyInfo->AppName ?? '',$Template->Content);
                                    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '',$Content1);
                                    $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '',$Content2);
                                    $Content4 = str_replace(Notificationcontent::REPLACE_ARR["subscription_name"], $ItemName ?? '',$Content3);
                                    $Content5 = str_replace(Notificationcontent::REPLACE_ARR["store_period"], $FixOldTrueDate ?? '',$Content4);
                                    $Content6 = str_replace(Notificationcontent::REPLACE_ARR["new_validity"],$FixNewTrueDate ?? '',$Content5);
                                    $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["days_number"], $MemberShipLimitDays ?? '',$Content6);
//                                    $ContentTrue = $Content7;


//                                    $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                                    $Subject = $Template->Subject;

                                    if ($TemplateStatus != '1') {
                                        if ($TemplateSendOption != 'BA000') {
                                            $AddNotification = DB::table('appnotification')->insertGetId(
                                                array(
                                                    'CompanyNum' => $CompanyNum,
                                                    'ClientId' => $ClientId,
                                                    'Subject' => $Subject,
                                                    'Text' => $TextNotification,
                                                    'Dates' => $Dates,
                                                    'UserId' => '0',
                                                    'Type' => $Type,
                                                    'Date' => $Date,
                                                    'Time' => $Time
                                                )
                                            );
                                        }
                                        if ($SendStudioOption != 'BA000') {
                                            DB::table('boostapp.appnotification')->insertGetId(
                                                array(
                                                    'CompanyNum' => $CompanyNum,
                                                    'Type' => '3',
                                                    'ClientId' => $ClientId,
                                                    'Subject' => $SubjectBad,
                                                    'Text' => $TextNotificationBad,
                                                    'Dates' => $Dates,
                                                    'UserId' => '0',
                                                    'RoleId' => '3',
                                                    'Date' => $Date,
                                                    'Time' => $Time,
                                                    'SendStudioOption' => 'BA999'
                                                )
                                            );
                                        }
                                    }


                                }


                            } else {
                                DB::table('boostapplogin.badpoint')->where('CompanyNum', '=', $CompanyNum)->where('ClinetId', '=', $ClinetId)->where('ClassId', '=', $ClassAct->ClassId)->delete();
                            }


                        }

                    }


                }

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
    if(isset($GetClientBlock)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientBlock),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}