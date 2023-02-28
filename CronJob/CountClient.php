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


    $GetClients = DB::table('settings')->where('CountClient', '=', '0')->where('Status', '=', '0')->get();

    $TotalPayments = '0';


    foreach ($GetClients as $GetClient) {


///// ספירת סוג מנוי

        $CompanyNum = $GetClient->CompanyNum;


        $CountClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->count();


        $GetClientId = DB::table('247softnew.client')->where('FixCompanyNum', '=', $CompanyNum)->first();


        if (@$GetClientId->id != '') {

            $GetClientCreditKeva = DB::table('247softnew.paytoken')->where('ClientId', '=', $GetClientId->id)->where('Status', '=', '0')->where('ItemId', '=', '2')->where('CountPayment', '>=', '1')->first();


            if (@$GetClientCreditKeva->id != '') {

                $DayPayments = $GetClientCreditKeva->NextPayment;

                $DayPaymentTrue = with(new DateTime(@$DayPayments))->format('d/m/Y');


                $DateCheck = date("Y-m-d", strtotime('-1 day', strtotime($DayPayments)));

                if ($GetClientCreditKeva->LastPayment != '') {

                    if ($GetClientCreditKeva->LastPayment == $ThisDate) {

                        $DateFromCheck = date("Y-m-d", strtotime('-1 day', strtotime($GetClientCreditKeva->LastPayment)));

                    } else {

                        $DateFromCheck = $GetClientCreditKeva->LastPayment;
                    }
                } else {

                    $DateFromCheck = date("Y-m-d", strtotime('-1 month', strtotime($DayPayments)));

                }


                $ClientCounts = DB::table('boostapp.client_count')->where('CompanyNum', '=', $CompanyNum)->whereBetween('Date', array($DateFromCheck, $DateCheck))->max('CountClient');


                if ($ClientCounts == '') {

                    $ClientCounts = '0';

                }


                ///// בדיקת חבילת תמחור


                $ClientDeals = DB::table('247softnew.cleint_pricelist')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->whereBetween('NumClient', array('0', $ClientCounts))->orderBy('NumClient', 'DESC')->first();


                $StepPayments = @$ClientDeals->Text;

                $StepPaymentPrice = @$ClientDeals->Amount;


                /////// בדיקת SMS
                $ClientSMS = DB::table('boostapp.appnotification')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('Type', '=', '1')
                    ->where('Status', '=', '1')
                    ->where('System', '=', '0')
                    ->whereBetween('Date', array($DateFromCheck, $DateCheck))
                    ->sum('SMSSumPrice');

                if ($ClientSMS == '') {

                    $ClientSMS = '0';

                }


                ////// בדיקת תוספות


                $ClientExtra = DB::table('247softnew.cleint_pricelist_add')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->sum('Amount');


                if ($ClientExtra == '') {

                    $ClientExtra = '0';

                }


                $TotalPayments = @$ClientDeals->Amount + $ClientExtra + $ClientSMS;


            } else {

                $TotalPayments = '0';

            }


        } /// סוף חישוב צפי


//echo $TotalPayments;

//echo '<br>';    


        DB::table('client_count')->insertGetId(

            array('CompanyNum' => $CompanyNum, 'CountClient' => $CountClient, 'Date' => $ThisDate, 'Amount' => $TotalPayments));


        $TotalPayments = '0';


    }


//////////////////////////////////////////////////////////////// סיום טבלת לקוחות + ספירת מנויים ///////////////////////////////////////////////////////


    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClient)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClient),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}


?>

