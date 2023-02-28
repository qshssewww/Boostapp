<?php

require_once "Utils.php";
require_once "Token.php";
// require_once "Client.php";

class YaadUtils extends Utils
{
    public const SOFT_YAAD_NUMBER = '4500355328';

    public function getBaseUrl($isTest = false)
    {
        return 'https://icom.yaad.net/p/';
    }

    public function apiSignAndGetUrl($data, $masof) {
        $postData = [
            "action" => "APISign",
//            "KEY" => "b6589fc6ab0dc82cf12099d1c2d40ab994e8410c",  test api key
            "KEY" => Config::get('payment.yaadSarig.key'),
            "What" => "SIGN",
            "Masof" => $masof,
            "UTF8" => "True",
            "UTF8out" => "True",
            "Info" => $data["Info"],
            "Amount" => $data["payment_sum"],
            "J5" => "J2",
//            "PassP" => 'yaad', //config('extraConf.yaad.passP'),
            "Tash" => 1,
            "Sign" => "True",
            "sendemail" => "False",
            "MoreData" => "True",
            "ClientName" => $data['FirstName'],
            "ClientLName" => $data['LastName'],
            "cell" => $data['cell'],
            "UserId" => $data["UserId"],
            "PageLang" => "HEB"
        ];

        return $this->curl("https://icom.yaad.net/p/", $postData);
    }
    public function apiGetUrl($data, $masof) {
        $postData = [
            "action" => "APISign",
//            "KEY" => "b6589fc6ab0dc82cf12099d1c2d40ab994e8410c",  //test api key
            "KEY" => Config::get('payment.yaadSarig.key'),
            "What" => "SIGN",
            "Masof" => $masof,
            "UTF8" => "True",
            "UTF8out" => "True",
            "Info" => $data["Info"],
            "Amount" => $data["payment_sum"],
            //"PassP" => 'yaad', //config('extraConf.yaad.passP'),
            "Tash" => $data["tash"],
            "Sign" => "True",
            "sendemail" => "False",
            "MoreData" => "True",
            "SendHesh" => "True",
            "ShowEngTashText" => "False",
            "FixTash" => "True",
            "Coin" => 1,
            "J5" => "False",
            "Postpone" => "False",
            "ClientName" => $data['FirstName'] ?? '',
            "ClientLName" => $data['LastName'] ?? '',
            "cell" => $data['cell'],
            "phone" => $data['cell'],
            "email" => " ",
            "Fild2" => $data["fild2"] ?? '',
            "Order" => $data["order"],
            "tmp" => 5,
            "PageLang" => "HEB"
        ];
        return $this->curl("https://icom.yaad.net/p/", $postData);
    }

    public function getToken($transactionId, $Masof){
        $postData = array(
            "action" => "getToken",
            "Masof" => $Masof,
            "TransId" => $transactionId
        );
        $url = $this->getBaseUrl();

        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        );
        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $output = curl_exec($ch);
        curl_close($ch);

        $UrlSoft = 'https://wwww.247soft.co.il/?' . $output;
        $parts = parse_url($UrlSoft);
        parse_str($parts['query'], $output);

        $Token = $output['Token'] ?? '';
        $Tokef = $output['Tokef'] ?? '';

        if(empty($Token)) {
            $arr = array(
                "message" => "could not get token",
                "file_path" => "Yaad.php",
            );
            $arr["data"] = json_encode(["data" => $output, "masof" => $Masof], JSON_PRETTY_PRINT);
            DB::table("boostapp.update_payment_log")->insertGetId($arr);
            return [];
        }

         return [
             'Token' => $Token,
             'Tokef' => $Tokef,
        ];
    }

    public function createTransactionWithToken($data, $masof) {
        $Info = "תשלום " . ($data->last_payment_num + 1) . " ";
        if ($data->total_payments != -1) {
            $Info .= "מתוך " . $data->total_payments;
        }
        $token = Token::getById($data->token_id);
        if (!$token) {
            return false;
        }
        $client = new Client($data->client_id);
        $s = substr($client->__get('ContactMobile'), 0, 1);
        if ($s == '0') {
            $cell = $client->__get('ContactMobile');
        } elseif ($s == '+') {
            $cell = '0' . substr($client->__get('ContactMobile'), 4);
        } else {
            $cell = '0' . substr($client->__get('ContactMobile'), 3);
        }
        $postData = [
            "action" => "soft",
            "Masof" => $masof,
            "UTF8" => "True",
            "UTF8out" => "True",
            "Info" => $Info,
            "Amount" => $data->payment_sum,
            "J5" => "False",
//            "PassP" => 'yaad',
            "Tash" => 1,
            "sendemail" => "False",
            "MoreData" => "True",
            "CC" => $data->card_token,
            "Tmonth" => substr($token->Tokef, 2),
            "Tyear" => substr($token->Tokef, 0, 2),
            "ClientName" => $client->__get('FirstName'),
            "ClientLName" => $client->__get('LastName'),
            "cell" => $cell,
            "UserId" => $client->__get('CompanyId'),
            "Token" => "True"
        ];
        $test = $this->curl('https://icom.yaad.net/p/', $postData);
        return $test;
    }

   public function curl($url, $query, $method = "post"){
       $post = 1;
       if ($method == "get" || $method == "GET"){
           $post = 0;
       }
       $defaults = array(
           CURLOPT_POST => $post,
           CURLOPT_HEADER => 0,
           CURLOPT_URL => $url,
           CURLOPT_FRESH_CONNECT => 1,
           CURLOPT_FOLLOWLOCATION => TRUE,
           CURLOPT_SSL_VERIFYPEER => false,
           CURLOPT_RETURNTRANSFER => 1,
           CURLOPT_FORBID_REUSE => 1,
           CURLOPT_TIMEOUT => 60,
           CURLOPT_POSTFIELDS => http_build_query($query),
           CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
       );
       $ch = curl_init();
       curl_setopt_array($ch, $defaults);
       $response = curl_exec($ch);
       if (curl_errno($ch)) {
           $curl_error = curl_error($ch);
           //handle error, save api log with error etc.
           curl_close($ch);
           return json_encode(['error' => $curl_error], 400);
       } else {
           $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
           if ($resultStatus == 200) {
               curl_close($ch);
            //    $parts = parse_url($response);
            //    parse_str($parts['query'], $query);
               return 'https://icom.yaad.net/p/?action=pay&' . $response;
           }
       }
       curl_close($ch);
       return json_encode(['error' => $response["err"]["message"]], 400);
   }
}
