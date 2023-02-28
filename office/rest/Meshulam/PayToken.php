<?php
require_once '../../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$ClientId = $_REQUEST['ClientId'];
$TokenId = $_REQUEST['TokenId'];

$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$TokenInfo = DB::table('token')->where('id' ,'=', $TokenId)->where('ClientId' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$TypeShva = $SettingsInfo->TypeShva;
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;

if ($LiveMeshulam=='0'){
$meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/doPaymentWithToken';
}
else {
$meshulam_url = 'https://meshulam.co.il/api/server/1.0/doPaymentWithToken';    
}


$post_data = array(
    'api_key' => $MeshulamAPI,
    'user_id' => $MeshulamUserId,
    'card_token_key' => $TokenInfo->Token,
    'full_name' => htmlentities($ClientInfo->CompanyName),
    'phone' => htmlentities($ClientInfo->ContactMobile),
    'sum' => '5.00',
//    'description' => 'עסקת חיוב חודשי',
    'type_id' => '2',
    'payment_num' => '1',
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
        
//        echo $json_response;
        
        
        // get status and payment url
        $responseArr = json_decode($json_response, true);
        
        print_r($json_response);
        
        if(intval($responseArr['status']) == 1){
            $cg_payment_url = $responseArr['status'];
             $cg_payment_url;
            
          $UpdateTransactionDetails = serialize($responseArr);
          $myarray =  unserialize($UpdateTransactionDetails); 
            
        echo $myarray['data'][0]['card_suffix'];     
            
          $InsertTransaction = DB::table('transaction')->insertGetId(
          array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));   
            
            
        }else{
            $cg_payment_url = $responseArr['err']['message'];
             $cg_payment_url;
        }

    }else{
        // the request did not complete as expected.

        //handle error, save api log with error etc.
        echo "Request failed: HTTP status code:  $resultStatus";
     }
}

curl_close($ch);