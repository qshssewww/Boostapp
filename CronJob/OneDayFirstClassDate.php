<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/CompanyProductSettings.php';
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


//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////
try {

    $Clients = DB::table('client_activities')->where('Status', '=', '0')->where('FirstDate', '=', '1')->where('FirstDateStatus', '=', '1')->get();

    foreach ($Clients as $Client) {

        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $Client->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '') {


            $CheckClasees = DB::table('classstudio_act')->where('CompanyNum', '=', $Client->CompanyNum)->where('ClientId', '=', $Client->ClientId)->where('ClientActivitiesId', '=', $Client->id)->whereIn('Status', array(1, 2, 4, 6, 8, 11, 15, 21))->orderBy('ClassDate', 'ASC')->first();

            if (@$CheckClasees->id != '') {

                $StartDate = $CheckClasees->ClassDate;

                $ItemsInfo = DB::table('items')->where('CompanyNum', '=', $Client->CompanyNum)->where('id', '=', $Client->ItemId)->first();

                $ItemPrice = $ItemsInfo->ItemPrice;
                $ItemPriceVat = $ItemsInfo->ItemPriceVat;
                $ItemText = htmlentities($ItemsInfo->ItemName);

                $Department = $ItemsInfo->Department; // חוק מנוי
                $MemberShip = $ItemsInfo->MemberShip; // סוג מנוי

                $Vaild = $ItemsInfo->Vaild; // חישוב תוקף
                $Vaild_Type = $ItemsInfo->Vaild_Type; // סוג חישוב
                $LimitClass = $ItemsInfo->LimitClass; // הגבלת שיעורים

                $CompanyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($Client->CompanyNum);
                $NotificationDays = $CompanyProductSettings->NotificationDays ?? 0; // התראה לפני סוף מנוי

                $BalanceClass = $ItemsInfo->BalanceClass; // כמות שיעורים
                $MinusCards = $CompanyProductSettings->offsetMemberships ?? 1; // קיזוז מכרטיסיה קודמת
                $StartTime = $ItemsInfo->StartTime; // הגבלת הזמנת שיעורים
                $EndTime = $ItemsInfo->EndTime; // הגבלת הזמנת שיעורים
                $CancelLImit = $ItemsInfo->CancelLImit; // ביטול הגבלה
                $ClassSameDay = $ItemsInfo->ClassSameDay; // הזמנת שיעור באותו היום
                $FreezMemberShip = $ItemsInfo->FreezMemberShip; // ניתן להקפאה?
                $FreezMemberShipDays = $ItemsInfo->FreezMemberShipDays; // מספר ימים מקסימלי להקפאה
                $FreezMemberShipCount = $ItemsInfo->FreezMemberShipCount; // מספר פעמים שניתן להקפיא מנוי


                $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                $time = strtotime($StartDate);
                $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));


                $Vaild_TypeOptions = @$Vaild_TypeOption['1'];
                $ItemsTime = '-' . $NotificationDays . ' ' . $Vaild_TypeOptions;

                $time = strtotime($ClassDate);
                $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

                if ($NotificationDays == '0' || $NotificationDays == '') {
                    $NotificationDate = NULL;
                }


                DB::table('client_activities')
                    ->where('id', $Client->id)
                    ->where('CompanyNum', $Client->CompanyNum)
                    ->update(array('StartDate' => $StartDate, 'VaildDate' => $ClassDate, 'TrueDate' => $ClassDate, 'NotificationDays' => $NotificationDate, 'FirstDate' => '1', 'FirstDateStatus' => '0'));

                $Date = date('Y-m-d');
                $Dates = date('Y-m-d H:i:s');
                $Time = '09:00:00';

                $TextNotification = lang('subscription_validity_cron').' '.$ItemText.' '.lang('after_complete_day');
                $Subject = lang('count_valid_first_day');


                DB::table('boostapp.appnotification')->insertGetId(
                    array('CompanyNum' => $Client->CompanyNum, 'Type' => '3', 'ClientId' => $Client->ClientId, 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'RoleId' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => 'BA999'));

            }


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
    if(isset($Client)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($Client),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}

?>
