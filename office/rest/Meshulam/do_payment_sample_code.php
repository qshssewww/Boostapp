<?php

$meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/doPayment';

$post_data = array(
    'api_key' => 'ce0961e665a3',
    'user_id' => '11100',
    'full_name' => 'תשלום בדיקה',
    'phone' => '0535885668',
    'email' => 'efi@boostapp.co.il',
    'sum' => '5',
    'description' => 'תיאור לעסקה',
    'type_id' => '2',
    'payment_num' => '1',
    'return_url' => get_boostapp_domain() . '?parameter=value',
    'update_transaction_url' => get_boostapp_domain() . '/api/UpdateTransactionDetails',
    'company_api_extra_details' => 'your date',
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