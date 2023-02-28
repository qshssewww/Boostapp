<?php
require_once '../../../app/init.php';
if(Auth::guest()) {
    exit;
}
require_once __DIR__.'/../../services/LoggerService.php';


$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$ClientId = $_REQUEST['ClientId'];
$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
if($ClientInfo->parentClientId != 0) {
    $parentClient = DB::table('client')->where('id' ,'=', $ClientInfo->parentClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();    
    if(!empty($parentClient)) {
        $ClientId = $parentClient->id;
        $ClientInfo = $parentClient;
    }
}

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$TypeShva = $SettingsInfo->TypeShva;
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;
    
if ($LiveMeshulam=='0'){
$meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/createToken';
}
else {
$meshulam_url = 'https://meshulam.co.il/api/server/1.0/createToken';
}

$post_data = array(
    'api_key' => $MeshulamAPI,
    'user_id' => $MeshulamUserId,
    'full_name' => htmlentities($ClientInfo->CompanyName),
    'phone' => htmlentities(str_replace("+972", '',$ClientInfo->ContactMobile)),
    'return_url' => get_loginboostapp_domain() . '/office/rest/Meshulam/TokenBack.php?parameter=value',
    'update_details_url' => get_loginboostapp_domain() . '/office/rest/Meshulam/UpdateTransactionDetails.php?CompanyNum='.$CompanyNum.'&ClientId='.$ClientId.'&UserId='.$UserId,
    'company_api_extra_details' => 'יצירת טוקן',
);
LoggerService::info($post_data, LoggerService::CATEGORY_MESHULAM);
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
} else {
    // check the HTTP status code of the request
    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($resultStatus == 200) {
        // get status and payment url
        $responseArr = json_decode($json_response, true);
        LoggerService::info($responseArr, LoggerService::CATEGORY_MESHULAM);
        if((int)$responseArr['status'] == 1){
            $cg_payment_url = $responseArr['data']['url'];
            echo $cg_payment_url;
        } else {
            //handle error
        }

    } else {
        // the request did not complete as expected.

        //handle error, save api log with error etc.
        echo "Request failed: HTTP status code:  $resultStatus";
     }
}

curl_close($ch);

