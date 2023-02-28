<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;

$TextSaved = DB::table('textsaved')->where('CompanyNum', $CompanyNum)->where('id', $_REQUEST['id'])->first();
$Client = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', @$_REQUEST['cid'])->first();

function get_tiny_url($url)  {  
	return $url;
	$ch = curl_init();
	$timeout = 5;  
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch);  
	return $data;  
}

$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

$EmailContentFilter1 = str_replace("[[מייל]]",@$Client->Email,$TextSaved->EmailContent);
$EmailContentFilter2 = str_replace("[[שם מלא]]",@$Client->CompanyName,$EmailContentFilter1);
$EmailContentFilter3 = str_replace("[[שם פרטי]]",@$Client->FirstName,$EmailContentFilter2);
$EmailContentFilter4 = str_replace("[[שם נציג מלא]]",Auth::user()->display_name,$EmailContentFilter3);
$EmailContentFilter5 = str_replace("[[שם הנציג]]",Auth::user()->display_name,$EmailContentFilter4);
$EmailContentFilter6 = str_replace("[[טלפון]]",@$Client->Phone,$EmailContentFilter5);
$EmailContentFilterTotal = $EmailContentFilter6;

$SmsContentFilter1 = str_replace("[[מייל]]",@$Client->Email,$TextSaved->SmsContent);
$SmsContentFilter2 = str_replace("[[שם מלא]]",@$Client->CompanyName,$SmsContentFilter1);
$SmsContentFilter3 = str_replace("[[שם פרטי]]",@$Client->FirstName,$SmsContentFilter2);
$SmsContentFilter4 = str_replace("[[שם נציג מלא]]",Auth::user()->display_name,$SmsContentFilter3);
$SmsContentFilter5 = str_replace("[[שם הנציג]]",Auth::user()->display_name,$SmsContentFilter4);
$SmsContentFilter6 = str_replace("[[טלפון]]",@$Client->Phone,$SmsContentFilter5);
$SmsContentFilterTotal = $SmsContentFilter6;

//בדיקת תוכן מייל והחלפת לינקים ללינקים מקוצרים
$EmailContent = $EmailContentFilterTotal;

//בדיקת תוכן מייל והחלפת לינקים ללינקים מקוצרים


//בדיקת תוכן אסאמאס והחלפת לינקים ללינקים מקוצרים
$SmsContent = $SmsContentFilterTotal;

//בדיקת תוכן אסאמאס והחלפת לינקים ללינקים מקוצרים


$MessageContent = array('emailtitle' => $TextSaved->EmailTitle, 'emailcontent' => $EmailContent, 'smscontent' => $SmsContent);
$MessageContent = json_encode($MessageContent);
echo $MessageContent;
?>
