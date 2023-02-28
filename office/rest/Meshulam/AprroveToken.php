<?php
require_once '../../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$ClientId = $_REQUEST['ClientId'];
$TokenId = '8';

$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$TokenInfo = DB::table('transaction')->where('id' ,'=', $TokenId)->where('CompanyNum' ,'=', $CompanyNum)->first();

$myarray =  unserialize($TokenInfo->UpdateTransactionDetails);

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$TypeShva = $SettingsInfo->TypeShva;
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;

if ($LiveMeshulam=='0'){
$meshulam_url = 'https://meshulam.co.il/api/server/1.0/approveCreateToken';
}
else {
$meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/approveCreateToken';    
}


$post_data = array(
    'api_key' => $MeshulamAPI,
    'secure_token' => $myarray['data']['secure_token'],
    'request_id' => $myarray['data']['request_id'],
    'create_date' => $myarray['data']['create_date'],
    'business_title' => $myarray['data']['business_title'],
    'full_name' => $myarray['data']['full_name'],
    'payer_phone' => $myarray['data']['payer_phone'],
    'tz' => $myarray['data']['tz'],
    'card_suffix' => $myarray['data']['card_suffix'],
    'card_exp' => $myarray['data']['card_exp'],
    'card_type' => $myarray['data']['card_type'],
    'card_type_code' => $myarray['data']['card_type_code'],
    'card_brand' => $myarray['data']['card_brand'],
    'card_brand_code' => $myarray['data']['card_brand_code'],
    'card_token_key' => $myarray['data']['card_token_key'],

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
        if((int)$responseArr['status'] == 1){
            $cg_payment_url = $responseArr['status'];
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