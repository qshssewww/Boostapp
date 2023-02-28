<?php

require_once '../../app/initcron.php';
$SettingsInfo2 = DB::table('settings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();

$CompanyNameSend = Auth::user()->MobileSend;

//$Cost = $_REQUEST['Amount'];
$ClientPhone = $_REQUEST['Phone'];
$ClientName = $_REQUEST['ClientName'];
$Messages = $_REQUEST['Message'];
$ClientId = $_REQUEST['ClientId'];

$GroupNumber = rand(1,9999999);
$GroupNumber;

$url = "https://019sms.co.il/api";
$xml ='
<?xml version="1.0" encoding="UTF-8"?>
<sms>
<user>
<username>mashtap3</username>
<password>43567</password>
</user>
<source>'.$CompanyNameSend.'</source>
<destinations>
<phone id="'.$GroupNumber.'">'.$ClientPhone.'</phone>
</destinations>
<message>
'.$Messages.'
</message>
</sms>';

$CR = curl_init();


curl_setopt($CR, CURLOPT_URL, $url); 
curl_setopt($CR, CURLOPT_POST, 1); 
curl_setopt($CR, CURLOPT_FAILONERROR, true); 
curl_setopt($CR, CURLOPT_POSTFIELDS, $xml); 
curl_setopt($CR, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($CR, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($CR,CURLOPT_HTTPHEADER,array("charset=utf-8"));

$result = curl_exec($CR);
curl_close( $CR ); 
 
if (mb_strlen($Messages) <= '100') {
	$CountTotalLetters = '1';
}
else {
	$CountTotalLetters = ceil(mb_strlen($Messages)/200);
}
$SMSPrice = $SettingsInfo2->SMSPrice;
$SMSSumPrice = $CountTotalLetters*$SettingsInfo2->SMSPrice;


$Dates = date('Y-m-d');
$time = date('Y-m-d G:i:s');

            $UserId = Auth::user()->id;
            $CompanyNum = Auth::user()->CompanyNum;

$CompanyNum = DB::table('smslog')->insertGetId(
            array('ClientId' => $ClientId, 'Receiver' => $ClientPhone, 'Text' => $Messages,'Date' => $time,'Dates' => $Dates,'UserId' => $UserId,'CompanyNum' => $CompanyNum,'Count' => $CountTotalLetters,'SMSPrice' => $SMSPrice,'SMSSumPrice' => $SMSSumPrice) );

?>