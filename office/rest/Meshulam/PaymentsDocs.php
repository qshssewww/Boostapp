<?php
require_once '../../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$ClientId = $_REQUEST['ClientId'];
if ($ClientId!='0'){
$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClientsName = htmlentities($ClientInfo->CompanyName);	
$ClientMobile = htmlentities($ClientInfo->ContactMobile);		
}
else {
$ClientsName = htmlentities($_REQUEST['ClientsName']);	
$ClientMobile = htmlentities($_REQUEST['ClientMobile']);	
}


$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$TypeShva = $SettingsInfo->TypeShva;
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;


$Tash = $_REQUEST['Tash'];
$tashType = $_REQUEST['tashType'];
$CreditValue = $_REQUEST['CreditValue'];
$Finalinvoicenum = $_REQUEST['Finalinvoicenum'];
$TrueFinalinvoicenum = $_REQUEST['TrueFinalinvoicenum'];
$TypeDoc = $_REQUEST['TypeDoc'];
$TempId = $_REQUEST['TempId'];

$AmountsMore = DB::table('temp_receipt_payment')->where('CompanyNum' ,'=', $CompanyNum)->where('TempId' ,'=', $TempId)->sum('Amount');

$AmountsMore = number_format((float)$AmountsMore+$CreditValue, 2, '.', '');

if ($TypeDoc=='320'){
if ($AmountsMore>=$Finalinvoicenum){
$ActionM = '1';	
}
else {
$ActionM = '0';		
}
}
else {
$ActionM = '1';	
}



if ($tashType=='0'){
$tashType = '2';     
}
else if ($tashType=='1'){
$tashType = '4';     
}
else if ($tashType=='6'){
$tashType = '1';     
}    
else {
$tashType = '2';   
}

if ($LiveMeshulam=='0'){
$meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/doPayment';
}
else {
$meshulam_url = 'https://meshulam.co.il/api/server/1.0/doPayment';    
}
    
    
$post_data = array(
    'api_key' => $MeshulamAPI,
    'user_id' => $MeshulamUserId,
    'full_name' => $ClientsName,
    'phone' => $ClientMobile,
    'sum' => htmlentities($CreditValue),
    'type_id' => htmlentities($tashType),
    'payment_num' => htmlentities($Tash),
    'return_url' => get_loginboostapp_domain() . '/office/rest/Meshulam/PaymentDocsBack.php?&ActionM='.@$ActionM.'&TempId='.@$TempId.'&TypeDoc='.@$TypeDoc.'&ClientId='.$ClientId.'&Finalinvoicenum='.$Finalinvoicenum.'&parameter=value',
    'update_transaction_url' => get_loginboostapp_domain() . '/office/rest/Meshulam/UpdateTransactionPaymentsDocsDetails.php?CompanyNum='.$CompanyNum.'&ClientId='.$ClientId.'&UserId='.$UserId.'&TypeDoc='.@$TypeDoc.'&TempId='.@$TempId,
    'company_api_extra_details' => 'חיוב לקוח',
);


$defaults = array(
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_URL => $meshulam_url,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FORBID_REUSE => 1,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_POSTFIELDS => http_build_query($post_data),
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
);

$ch = curl_init();

curl_setopt_array($ch, $defaults);
$json_response = curl_exec($ch);

if (curl_errno($ch)) {

    $curl_error = curl_error($ch);
    //handle error, save api log with error etc.
    echo "Couldn't send request, error message: ".$curl_error;
}else{
    // check the HTTP status code of the request
    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($resultStatus == 200) {
        // get status and payment url
        $responseArr = json_decode($json_response, true);
        if(intval($responseArr['status']) == 1){
            $cg_payment_url = $responseArr['data']['url'];
            echo $cg_payment_url;
        }else{
            //handle error
        }

    }else{
        // the request did not complete as expected.

        //handle error, save api log with error etc.
        echo "Request failed: HTTP status code:  $resultStatus";
     }
}

curl_close($ch);