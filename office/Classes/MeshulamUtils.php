<?php

/**
 * Class Meshulam
 */
class MeshulamUtils extends Utils
{
    /**
     * @var string
     */
    private static $apiKey;
    /**
     * @var string
     */
    private static $creditPageCode;
    /**
     * @var string
     */
    private static $bitPageCode;
    /**
     * @var string
     */
    private static $baseUrl;

    protected const PAYMENT_TYPE_DIRECT = 1;
    protected const PAYMENT_TYPE_REGULAR = 2;
    protected const PAYMENT_TYPE_PAYMENTS = 4;


    /**
     * Meshulam constructor.
     */
    public function __construct()
    {
        $config = app('config')->get('services');
        self::$apiKey = $config["meshulam"]["apiKey"];
        self::$creditPageCode = $config["meshulam"]["creditPageCode"];
//        self::$creditPageCode = "49976aeec0c7"; //todo change in config to 49976aeec0c7
        self::$bitPageCode = $config["meshulam"]["bitPageCode"];
//        self::$baseUrl = $config["meshulam"]["baseUrl"];
        self::$baseUrl = 'https://secure.meshulam.co.il/api/light/server/1.0/'; // todo remove
        //todo remove
        if($_SERVER['HTTP_HOST'] === 'localhost:8000') {
            self::$creditPageCode = "82458e0ceea7"; //todo change only test page
            self::$apiKey = $config["meshulam"]["apiKey"];
            self::$baseUrl = $config["meshulam"]["baseUrl"];
        }
    }

    /**
     * @param $url
     * @param $postData
     * @return mixed
     * @throws Exception
     */
    protected function sendRequest($url, $postData)
    {
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
        $jsonResponse = curl_exec($ch);
        $output = json_decode($jsonResponse, true);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        return $output;
    }

    /**
     * Prepare data - Request transaction info, create payment Data, save new token
     *
     * @param $response
     * @return array
     * @throws Exception
     */
    public function prepareData($response): array
    {
        if (empty($response['transactionId']) || empty($response['transactionToken'])
            || empty($response['customFields']) || empty($response['studioSettings'])) {
            throw new Exception('Data not valid');
        }
        $studioSettings = $response['studioSettings'];

        $transactionInfoResponse = $this->getTransactionInfo($response, $studioSettings->MeshulamAPI);

        //if ($output['status'] == 1 && !empty($output['data']) && $output['data']['status'] === 'שולם' && $output['data']['statusCode'] == 2) {
        // Checks that the transfer information is valid
        if ($transactionInfoResponse['status'] == 1 &&
            !empty($transactionInfoResponse['data']) &&
            $transactionInfoResponse['data']['status'] === 'שולם') {

            // create payment data
            $paymentData = $this->preparePaymentData($transactionInfoResponse['data']);

            // approve transaction
            if ($this->approveTransaction($transactionInfoResponse['data'], $studioSettings->MeshulamAPI)) {
                //Prepare a new token if needed
                if ($paymentData['BrandName'] !== 'BIT') {
                    $tokenData = [
                        'CompanyNum' => $response['customFields']['cField3'],
                        'ClientId' => $response['customFields']['cField1'],
                        'YaadCode' => $paymentData['YaadCode'],
                        'Token' =>  $transactionInfoResponse['data']['cardToken'] ?? '',
                        'Tokef' => $transactionInfoResponse['data']['cardExp'] ?? '',
                        'L4digit' => $paymentData['L4digit'],
                        'YaadNumber' => $studioSettings->YaadNumber,
                    ];
                    $tokenModel = Token::getOrSetToken($tokenData, 1);
                    if (!$tokenModel) {
                        throw new LogicException('Wrong token create');
                    }
                }
                return $paymentData;
            }
        }

        throw new LogicException($response['err']['message']);
    }

    /**
     * @param $response
     * @return mixed
     * @throws Exception
     */
    private function getTransactionInfo($response, $apiKey)
    {
        $url = self::$baseUrl . 'getTransactionInfo';
        $postData = [
            "apiKey" =>$apiKey,
//            "apiKey" =>'210c23bdb9be', //todo remove - test only
            "pageCode" => self::$creditPageCode,
            "transactionId" => $response['transactionId'],
            "transactionToken" => $response['transactionToken'],
        ];

        $transactionInfoResponse = $this->sendRequest($url, $postData);

        //TODO PRINT - transaction info DONE
        $logData = json_encode([
            'file' => 'Meshulam',
            'case' => 'prepareData -> after getTransactionInfo',
            'output' => $transactionInfoResponse,
        ]);
        DB::table('boostapp.fixlog')
            ->insertGetId([
                    'Logdata' => $logData ,
                    'category' => 'Meshulam',
                    'type'=> 'test']
            );
        return $transactionInfoResponse;

    }

    /**
     * @param $data
     * @return array
     */
    private function preparePaymentData($data): array
    {
        $paymentData = array(
            'YaadCode' => $data['transactionId'],
            'CCode' => 0,
            'ACode' => $data['asmachta'],
            'Bank' => 9,
            'Brand' => 0,
            'Issuer' => 0,
            'tashTypeDB' => $data['paymentType']  == self::PAYMENT_TYPE_PAYMENTS ? 2 : 1,
            'Payments' => $data['allPaymentsNum'],
            'PayToken' => $data['transactionToken'],
            'TransactionId'  => $data['transactionId']
        );
        if ((int)$data['transactionTypeId'] === 1) {
            // Payment by card
            $paymentData['L4digit'] = $data['cardSuffix'] ?? '';
            if ($data['cardType'] === 'Local') {
                $local = 'ישראלי';
            } else {
                $local = 'תייר';
            }
            $paymentData['BrandName'] = 'כרטיס ' . $data['cardBrand'] . ' - ' . $local;
        } else {
            // Payment by BIT
            $paymentData['L4digit'] = $data['cardSuffix'] ?? '';
            $paymentData['BrandName'] = 'BIT';
        }
        return $paymentData;

    }

    /**
     * @param $responseData
     * @param $apiKey
     * @return bool
     */
    private function approveTransaction($responseData, $apiKey): bool
    {
        $url = self::$baseUrl . 'approveTransaction';

        $postData = array_merge([
            "pageCode" => self::$creditPageCode,
            "apiKey" => $apiKey,
//            "apiKey" => '210c23bdb9be', //todo remove test only
            "transactionId" => $responseData['transactionId'],
            "transactionToken" => $responseData['transactionToken'],
            "paymentSum" => $responseData['sum']
        ], $responseData);

        try {
            $output= $this->sendRequest($url, $postData);
            // TODO PRINT
            if ($output['status'] == 1) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
        return false;
    }


    /**
     * @param $paymentType
     * @param $totalAmount
     * @param $paymentNum
     * @return array
     */
    private function fixPaymentType($paymentType, $totalAmount, $paymentNum): array
    {
        $paymentDetails = array(
            'sum' => $totalAmount,
            'hokPaymentNum' =>  $paymentNum
        );
        switch ($paymentType) {
            case 1:
                // instead of Direct payment we use Regular type
                $paymentDetails['paymentType'] = self::PAYMENT_TYPE_REGULAR;
                break;
            case 2:
                $paymentDetails['paymentType'] = self::PAYMENT_TYPE_PAYMENTS;
                break;
            case 3:
                // divide to payments logic
                $amount = $totalAmount / $paymentNum;
                $roundedAmount = ceil(round($amount, 2));
                $restOfPayments = $roundedAmount * ($paymentNum - 1);
                $firstPayment = $totalAmount - $restOfPayments;
                $paymentDetails['firstPayment'] = number_format((float)$firstPayment, 2, '.', '');
                $paymentDetails['secondPayment'] = number_format($roundedAmount, 2, '.', '');

                $paymentDetails['hokPaymentNum'] = 1;
                $paymentDetails['bsappPayments'] = 3;   /// if paymentType == 3, payments by boostapp
                $paymentDetails['paymentType'] = self::PAYMENT_TYPE_REGULAR;
                $paymentDetails['sum'] = $paymentDetails['firstPayment'];
                break;
            default:
                throw new InvalidArgumentException('Wrong payment type');
        }
        return $paymentDetails;


    }


    /**
     * return firstname, lastname, email, phone
     *
     * @param $client
     * @return array
     */
    protected function getClientPaymentDetails($client): array
    {
        $clientDetail = array(
            'firstName' => $client->FirstName,
            'lastName' => $client->LastName,
            'email' => $client->Email,
            'phone' => str_replace('+972', '0', $client->ContactMobile),
            'clientId' => $client->id
        );

        if ($parent = $client->parent()) {
            $clientDetail['email'] = $parent->Email;
            $clientDetail['clientId'] = $parent->id;
            $clientDetail['phone'] = str_replace('+972', '0', $parent->ContactMobile);

        }
        return $clientDetail;
    }


    /**
     * @param $data
     * @param $tokenModel
     * @param $paymentType
     * @param $paymentNum
     * @return array
     * @throws Exception
     */
    public function makePaymentWithToken($data ,$tokenModel, $paymentType, $paymentNum): array
    {
        $url = self::$baseUrl . 'createTransactionWithToken';

        $studioSettings = $data['studioSettings'];
        $client = $data['client'];

        $clientDetail = $this->getClientPaymentDetails($client);

        $paymentDetails =$this->fixPaymentType($paymentType, $data['totalAmount'], $paymentNum);

        $postData = [
            "userId" => $studioSettings->MeshulamUserId,
            "apiKey" => $studioSettings->MeshulamAPI,
//            "userId" => '7689a2450290a253', //todo remove -test only
//            "apiKey" => '210c23bdb9be', //todo remove -test only
            "cardToken" => $tokenModel->Token,
            "sum" => $paymentDetails['sum'],
            "paymentType" => $paymentDetails['paymentType'],
            "paymentNum" => $paymentDetails['hokPaymentNum'], // if not type 3 is payment num
            "pageField[fullName]" => $clientDetail['firstName'] . ' ' . $clientDetail['lastName'],
            "pageField[phone]" => $clientDetail['phone'],
            "pageField[email]" => $clientDetail['email'],
            "saveCardToken" => 1,
            "cField1" => $clientDetail['clientId'],
        ];
        // create request to payment with token
        $responsePayment = $this->sendRequest($url, $postData);

        if ($responsePayment['status'] == 1){

            $paymentData = $this->preparePaymentData($responsePayment['data']);
            $paymentData['PaymentType'] = $paymentDetails['bsappPayments'] ?? $paymentType;
            return $paymentData;
        }

        throw new LogicException($responsePayment['err']['message']);
    }


    /**
     * @param $data
     * @param $paymentType
     * @param $paymentNum
     * @param $tempPaymentId
     * @return mixed
     * @throws Exception
     */
    public function makeFirstPayment($data, $paymentType, $paymentNum, $tempPaymentId)
    {
        $url = self::$baseUrl . 'createPaymentProcess';
        $client = $data['client'];
        $studioSettings = $data['studioSettings'];

        if($_SERVER['HTTP_HOST'] === 'localhost:8000') {
            $appBaseUrl = 'http://localhost:8000';
        } else {
            $appBaseUrl = app('config')->get('app')['url'];
        }

        $clientDetail = $this->getClientPaymentDetails($client);

        $paymentDetails =$this->fixPaymentType($paymentType, $data['totalAmount'], $paymentNum);

        $postData = [
            "pageCode" => self::$creditPageCode,
            "userId" => $studioSettings->MeshulamUserId,
            "apiKey" => $studioSettings->MeshulamAPI,
//            "userId" => '7689a2450290a253', //todo remove -test only
//            "apiKey" => '210c23bdb9be', //todo remove -test only
            "sum" => $paymentDetails['sum'],
//            "successUrl" => 'http://localhost:8000/office/payment/Payment.php?action=meshulamSuccess&tempPaymentId=' . $tempPaymentId,
//            "cancelUrl" => 'http://localhost:8000/office/payment/Payment.php?action=meshulamClose&tempPaymentId=' . $tempPaymentId,
            "successUrl" => $appBaseUrl . '/office/payment/Payment.php?action=meshulamSuccess&tempPaymentId=' . $tempPaymentId,
            "cancelUrl" =>$appBaseUrl . '/office/payment/Payment.php?action=meshulamClose&tempPaymentId=' . $tempPaymentId,
            "description" => '', // TODO: make description
            "paymentType" => $paymentDetails['paymentType'],
            "paymentNum" => $paymentDetails['hokPaymentNum'], // if not type 3 is payment num
            "pageField[fullName]" => $clientDetail['firstName'] . ' ' . $clientDetail['lastName'],
            "pageField[phone]" => $clientDetail['phone'],
            "pageField[email]" => $clientDetail['email'],
            "saveCardToken" => 1,
            "cField1" => $clientDetail['clientId'],
            "cField2" => $tempPaymentId,
            "cField3" => $studioSettings->CompanyNum,
        ];

        try {
            $output = $this->sendRequest($url, $postData);
        } catch (\Throwable $e) {
            throw $e;
        }

        if ($output['status'] === 0) {
            throw new Exception($output['err']['id'] . '. ' . $output['err']['message']);
        }

        return $output['data']['url'];
    }

    /**
     * @param $data stdClass mehsulamPaymnets join with client
     */
    public function createPayment($data){
        $phone = substr($data->ContactMobile, 1) == 0 ? $data->ContactMobile : 0 . substr($data->ContactMobile, 4);
        $post_data = [
            "pageCode" => config('extraConf.meshulam.pageCode'),
            "userId" => "376e8f46589f62aa",
            "apiKey" => config('extraConf.meshulam.apiKey'),
            "sum" => $data->payment_sum,
            "pageField[fullName]" => $data->CompanyName,
            "pageField[phone]" => $phone,
            "pageField[email]" => $data->Email,
            "cardToken" => $data->cardToken,
            "paymentType" => 1
        ];
    }


    /**
     * @param $data stdClass mehsulamPaymnets table join with client table
     * @param $userID string masof number
     * @return array|false|mixed|object|string|void
     */
    public function createTransactionWithToken($data, $userID){
        $url = self::$baseUrl . "createTransactionWithToken";
        $phone = substr($data->ContactMobile, 1) == 0 ? $data->ContactMobile : 0 . substr($data->ContactMobile, 4);
        $post_data = [
            "pageCode" => self::$creditPageCode,
            "userId" => $userID,
            "apiKey" =>self::$apiKey,
            "sum" => $data->payment_sum,
            "pageField[fullName]" => $data->CompanyName,
            "pageField[phone]" => $phone,
            "pageField[email]" => $data->Email,
            "cardToken" => $data->card_token,
            "paymentNum" => 1,
            "paymentType" => 2
        ];
        return $this->curl($url,$post_data);
    }

}
