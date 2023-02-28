<?php

require_once __DIR__.'/../app/initcron.php';
require_once __DIR__.'/../app/helpers/PhoneHelper.php';


$ContactMobile = $_REQUEST['phone'] ?? '';
$CompanyNum = '56149';

$CompanyInfo = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$COMPANY = $CompanyInfo->CompanyName;

if (!empty($ContactMobile)) {
    
$ContactMobile = PhoneHelper::shortPhoneNumber($ContactMobile);

$ClientInfo = DB::table('client')->where('ContactMobile','=',$ContactMobile)->where('CompanyNum', $CompanyNum)->first();
  
if (!empty($ClientInfo->id)) {   
    
$STATUS = 'OK';
$URL = App::url('office/ClientProfile.php?u='.$ClientInfo->id);
$CLIENTNAME = $ClientInfo->CompanyName;
$TOTAL = '1';

$OrderContent = array('STATUS' => $STATUS, 'URL' => $URL, 'CLIENTNAME' => $CLIENTNAME, 'TOTAL' => $TOTAL, 'COMPANY' => $COMPANY);
$OrderContent = json_encode($OrderContent, JSON_UNESCAPED_UNICODE);
echo $OrderContent;      
}
else {
//$OrderContent = array('STATUS' => '', 'URL' => '', 'CLIENTNAME' => '', 'TOTAL' => '0', 'COMPANY' => $COMPANY);
//$OrderContent = json_encode($OrderContent);
//echo $OrderContent;        
}      
}
else {
//$OrderContent = array('STATUS' => '', 'URL' => '', 'CLIENTNAME' => '', 'TOTAL' => '0', 'COMPANY' => $COMPANY);
//$OrderContent = json_encode($OrderContent);
//echo $OrderContent;        
}

$Items = DB::table('logfix')->insertGetId(
array('DataLog' => $ContactMobile) );	


?>
