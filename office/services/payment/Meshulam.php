<?php

require_once __DIR__ . '/PaymentSystem.php';
require_once __DIR__ . '/../OrderService.php';
require_once __DIR__ . '/PaymentTypeEnum.php';
require_once __DIR__ . '/../../Classes/OrderLogin.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/AppNotification.php';
require_once __DIR__ . '/../../Classes/TransactionError.php';
require_once __DIR__ . '/../../Utils/HttpClient.php';

/**
 * Class Meshulam
 */
class Meshulam extends PaymentSystem
{
    public const METHOD = 'meshulam';

    protected $isTest = false;

    protected const PAYMENT_TYPE_DIRECT = 1;
    protected const PAYMENT_TYPE_REGULAR = 2;
    protected const PAYMENT_TYPE_PAYMENTS = 4;

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        if ($this->getIsTest()) {
            return 'https://sandbox.meshulam.co.il/api/light/server/1.0/';
        } else {
            return 'https://secure.meshulam.co.il/api/light/server/1.0/';
        }
    }

    /**
     * @return string
     */
    public function getPaymentSystemName(): string {
        return PaymentService::PAYMENT_SYSTEM_MESHULAM;
    }
    /**
     * @return string
     */
    public function getPageCode()
    {
        return Config::get('payment.meshulamLogin.creditPageCode');
    }

    /**
     * @return bool
     */
    protected function getIsTest()
    {
        return Config::get('payment.meshulamLogin.isTest') ?? $this->isTest;
    }


    /**
     * Prepare data - Request transaction info, create payment Data, save new token
     *
     * @param $response
     * @return array
     * @throws Exception|LogicException|Throwable
     */
    public function prepareData($response): array
    {
        if (empty($response['data']['transactionId']) || empty($response['data']['transactionToken'])
            || empty($response['data']['customFields'])) {
            throw new Exception('Data not valid');
        }
        $transactionInfoResponse = $this->getTransactionInfo($response);

        // fix meshulam wrong field name
        if (isset($transactionInfoResponse['data']['customField'])) {
            $transactionInfoResponse['data']['customFields'] = $transactionInfoResponse['data']['customField'];
            unset($transactionInfoResponse['data']['customField']);
        }

        // Checks that the transfer information is valid
        if ($transactionInfoResponse['status'] == 1 && !empty($transactionInfoResponse['data']) && in_array($transactionInfoResponse['data']['status'], ['שולם', 'עסקה מושהית'])) {
            $orderId = (int)$response['data']['customFields']['cField1'];
            /** @var OrderLogin|null $order */
            $order = OrderLogin::find($orderId);

            // create payment data
            $paymentData = $this->preparePaymentData($transactionInfoResponse['data']);
            // approve transaction
            if ($this->approveTransaction($order, $transactionInfoResponse['data'])) {
                LoggerService::debug($transactionInfoResponse, LoggerService::CATEGORY_MESHULAM);

                $paymentData['tokenId'] = null;

                //Prepare a new token if needed
                if ($paymentData['BrandName'] !== 'BIT') {
                    $client = $order->client();

                    $clientId = 0;
                    if ($client) {
                        $clientId = $client->id;
                        if ($parent = $client->parent()) {
                            $clientId = $parent->id;
                        }
                    }

                    $tokenData = [
                        'CompanyNum' => $response['data']['customFields']['cField3'],
                        'ClientId' => $clientId,
                        'YaadCode' => $paymentData['YaadCode'],
                        'Token' =>  $transactionInfoResponse['data']['cardToken'] ?? '',
                        'Tokef' => $transactionInfoResponse['data']['cardExp'] ?? '',
                        'L4digit' => $paymentData['L4digit'],
                        'YaadNumber' => 0,
                    ];
                    $tokenModel = Token::getOrSetToken($tokenData, PaymentTypeEnum::TYPE_MESHULAM);
                    if (!$tokenModel) {
                        throw new LogicException('Wrong token create');
                    }

                    $order->TokenId = $tokenModel->id;
                    $order->save();

                    $paymentData['TokenId'] = $tokenModel->id;
                }
                return $paymentData;
            }
        }
        throw new LogicException($response['err']['message'] ?? 'Error with Meshulam payment');
    }

    /**
     * @param OrderLogin $order
     * @param $responseData
     * @return bool
     */
    private function approveTransaction(OrderLogin $order, $responseData): bool
    {
        $url = $this->getBaseUrl() . 'approveTransaction';
        $postData = array_merge([
            "pageCode" => $this->getPageCode(),
            "apiKey" => $order->studioSettings()->MeshulamAPI,
            "transactionId" => $responseData['transactionId'],
            "transactionToken" => $responseData['transactionToken'],
        ], $responseData);

        if ($responseData['statusCode'] == 11) {
            return true;
        }

        try {
            $output = $this->sendRequest($url, $postData);

            if ($output['status'] == 1) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
        return false;
    }

    /**
     * Makes payment for freezing money to get new token from payment system
     *
     * @param OrderLogin $order
     * @return mixed
     * @throws Throwable
     */
    public function makeCreditPaymentForNewCard(OrderLogin $order)
    {
        $url = $this->getBaseUrl() . 'createPaymentProcess';
        $client = $order->client();
        $studioSettings = $order->studioSettings();

        if ($_SERVER['HTTP_HOST'] === 'localhost:8000') {
            $appBaseUrl = 'http://localhost:8000';
            $serverBaseUrl = 'https://devlogin.boostapp.co.il';

        } else {
            $appBaseUrl = App::url();
//            $appBaseUrl = 'https://devbiz2.pinkapp.co.il';
            $serverBaseUrl = $appBaseUrl;
        }


        $clientDetail = $this->getClientPaymentDetails($client);
        $postData = [
            "pageCode" => $this->getPageCode(),
            "userId" => $studioSettings->MeshulamUserId,
            "apiKey" => $studioSettings->MeshulamAPI,
            "sum" => $order->TotalAmount,
            "successUrl" => $appBaseUrl . '/office/payment/Payment.php?action=meshulamSuccess&orderId=' . $order->id,
            "cancelUrl" => $appBaseUrl . '/office/payment/Payment.php?action=meshulamClose&orderId=' . $order->id,
            "notifyUrl" => $serverBaseUrl . '/office/payment/Payment.php?action=meshulamHandle',
            "description" => '', // TODO: make description
            "paymentType" => self::PAYMENT_TYPE_REGULAR,
            "paymentNum" => 1,
            "pageField[fullName]" => $clientDetail['firstName'] . ' ' . $clientDetail['lastName'],
            "pageField[phone]" => $clientDetail['phone'],
            "pageField[email]" => $clientDetail['email'],
            "saveCardToken" => 1,
            "cField1" => $order->id,
            "cField2" => $client->id,
            "cField3" => $order->CompanyNum,
            "chargeType" => 3,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_MESHULAM);
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
     * @param OrderLogin $order
     * @param Token $tokenModel
     * @param $paymentType
     * @param $paymentNum
     * @return array
     * @throws Throwable
     */
    public function makePaymentWithToken(OrderLogin $order, Token $tokenModel, $paymentType, $paymentNum): array
    {
        $url = $this->getBaseUrl() . 'createTransactionWithToken';
        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        $transactionUniqueIdentifier = $order->id;

        $postData = [
            "userId" => $order->studioSettings()->MeshulamUserId,
            "apiKey" => $order->studioSettings()->MeshulamAPI,
            "cardToken" => $tokenModel->Token,
            "sum" => $paymentDetails['sum'],
            "paymentType" => $paymentDetails['paymentType'],
            "paymentNum" => $paymentDetails['hokPaymentNum'], // if not type 3 is payment num
            "pageField[fullName]" => $clientDetail['firstName'] . ' ' . $clientDetail['lastName'],
            "pageField[phone]" => $clientDetail['phone'],
            "pageField[email]" => $clientDetail['email'],
            "saveCardToken" => 1,
            "cField1" => $client->id,
            "cField2" => $order->id,
            "transactionUniqueIdentifier" => $transactionUniqueIdentifier,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_MESHULAM);

        // create request to payment with token
        try {
            // send regular request
            $responsePayment = $this->sendRequest($url, $postData);
            if (empty($responsePayment)) {
                throw new LogicException('Empty response from payment system');
            }
        } catch (\LogicException $e) {
            LoggerService::error($e, LoggerService::CATEGORY_MESHULAM);
            // if we got an error when get response from Meshulam
            // we will try to get transaction info by transaction unique identifier
            $url = $this->getBaseUrl() . 'getTokenTransactionsByExternalIdentifiers';

            $checkPaymentData = [
                "userId" => $order->studioSettings()->MeshulamUserId,
                "cardToken" => $tokenModel->Token,
                "transactionUniqueIdentifier" => $transactionUniqueIdentifier,
            ];
            LoggerService::info($checkPaymentData, LoggerService::CATEGORY_MESHULAM);

            $responsePayment = $this->sendRequest($url, $postData);
            LoggerService::info($responsePayment, LoggerService::CATEGORY_MESHULAM);
        } catch (\Throwable $e) {
            throw $e;
        }

        if (empty($responsePayment)) {
            /// notify to Moshe client when meshulam response is empty
            $subject = 'empty response (login) from meshulam, order_login = '.$order->id;
            $text = 'date: '.date('Y-m-d H:i:s').'<br> order_login id: '.$order->id.'<br> company: '.$order->CompanyNum.' <br> client: '.$order->ClientId;
            $notification = new AppNotification([
                'CompanyNum' => 100,
                'ClientId' => 251931,
                'Type' => AppNotification::TYPE_EMAIL,
                'Subject' => $subject ?? '',
                'Text' => $text ?? '',
                'Dates' => date('Y-m-d H:i:s'),
                'UserId' => 0,
                'Date' => date('Y-m-d'),
                'Time' => date('H:i:s'),
                'priority' => 1
            ]);
            $notification->save();
        }

        LoggerService::info($responsePayment, LoggerService::CATEGORY_MESHULAM);

        if ($responsePayment['status'] == 1) {
            $paymentData = $this->preparePaymentData($responsePayment['data']);
            $paymentData['PaymentType'] = $paymentDetails['bsappPayments'] ?? $paymentType;
            return $paymentData;
        }
        throw new LogicException($responsePayment['err']['message']);
    }

    /**
     * @param $responsePayment
     * @return array
     */
    public function makePaymentWithTokenForFix($responsePayment): array
   {
        LoggerService::info($responsePayment, LoggerService::CATEGORY_MESHULAM);

        if ($responsePayment['status'] == 1) {
            return $this->preparePaymentData($responsePayment);
        }
        throw new LogicException($responsePayment);
    }

    /**
     * @param OrderLogin $order
     * @param $paymentType
     * @param $paymentNum
     * @return mixed
     * @throws Throwable
     */
    public function makeFirstPayment(OrderLogin $order, $paymentType, $paymentNum = 0)
    {
        if($paymentNum <= 0) {
            $paymentNum = $order->NumPayment ?? 1;
        }


        $url = $this->getBaseUrl() . 'createPaymentProcess';
        $client = $order->client();
        $studioSettings = $order->studioSettings();

        if($_SERVER['HTTP_HOST'] === 'localhost:8000') {
            $appBaseUrl = 'http://localhost:8000';
            $serverBaseUrl = 'https://devlogin.boostapp.co.il';
        } else {
            $appBaseUrl = app('config')->get('app')['url'];
            $serverBaseUrl = $appBaseUrl;
        }

        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        // Fix for empty cField2. Try to send -1 instead of 0 because Meshulam doesn't return us zero-field
        if ($clientDetail['clientId'] === 0) {
            $clientDetail['clientId'] = -1;
        }

        $postData = [
            "pageCode" => $this->getPageCode(),
            "userId" => $studioSettings->MeshulamUserId,
            "apiKey" => $studioSettings->MeshulamAPI,
            "sum" => $paymentDetails['sum'],
            "successUrl" => $appBaseUrl . '/office/payment/Payment.php?action=meshulamSuccess&orderId=' . $order->id,
            "cancelUrl" => $appBaseUrl . '/office/payment/Payment.php?action=meshulamClose&orderId=' . $order->id,
            "notifyUrl" => $serverBaseUrl . '/office/payment/Payment.php?action=meshulamHandle',
            "description" => '', // TODO: make description
            "paymentType" => $paymentDetails['paymentType'],
            "paymentNum" => $paymentDetails['hokPaymentNum'], // if not type 3 is payment num
            "pageField[fullName]" => $clientDetail['firstName'] . ' ' . $clientDetail['lastName'],
            "pageField[phone]" => $clientDetail['phone'],
            "pageField[email]" => $clientDetail['email'],
            "saveCardToken" => 1,
            "cField1" => $order->id,
            "cField2" => $clientDetail['clientId'],
            "cField3" => $order->CompanyNum,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_MESHULAM);

        try {
            $output = $this->sendRequest($url, $postData);

            LoggerService::info($output, LoggerService::CATEGORY_MESHULAM);
        } catch (\Throwable $e) {
            throw $e;
        }
        if ($output['status'] === 0) {
            throw new Exception($output['err']['id'] . '. ' . $output['err']['message']);
        }
        return $output['data']['url'];
    }

    /**
     * @param $meshulamAPIKey
     * @param $meshulamUserId
     * @param $transactionId
     * @param $transactionToken
     * @param $amount
     * @return array
     * @throws Throwable
     */
    public function makeRefund($meshulamAPIKey, $meshulamUserId, $transactionId, $transactionToken, $amount, $pageCode = null)
    {
        $url = $this->getBaseUrl() . 'refundTransaction';

        $postData = [
            "userId" => $meshulamUserId,
            "apiKey" => $meshulamAPIKey,
//            "pageCode" => $pageCode ?? $this->getPageCode(),
            "transactionId" => $transactionId,
            "transactionToken" => $transactionToken,
            "refundSum" => $amount,
        ];

        LoggerService::info($postData, LoggerService::CATEGORY_MESHULAM);

        // create request to payment with token
        $responsePayment = $this->sendRequest($url, $postData);

        LoggerService::info($responsePayment, LoggerService::CATEGORY_MESHULAM);

        if ($responsePayment['status'] == 1) {
            return $responsePayment;
        }
        throw new LogicException($responsePayment['err']['message']);
    }

    /**
     * @param $meshulamAPIKey
     * @param $meshulamUserId
     * @param $transactionId
     * @param $transactionToken
     * @param $amount
     * @param bool $fullRefund
     * @return array
     */
    public function makeRefundOldAPI($meshulamAPIKey, $meshulamUserId, $transactionId, $transactionToken, $amount, $fullRefund = true)
    {
        //// חיוב אשראי שמור משולם API
        if ($this->getIsTest()) {
            $meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/refundPayment';
        } else {
            $meshulam_url = 'https://meshulam.co.il/api/server/1.0/refundPayment';
        }

        $responseArr = [];

        $post_data = array(
            'api_key' => $meshulamAPIKey,
            'secure_token' => $transactionToken,
            'payment_id' => $transactionId,
            'refund_sum' => $amount,
            'refund_type' => $fullRefund ? 1 : 2,
            'refund_last_dd' => '0',
            'stop_dd' => '0'
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
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $curl_error = curl_error($ch);
            //handle error, save api log with error etc.
            echo "Couldn't send request, error message: " . $curl_error;

        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LoggerService::info($response, LoggerService::CATEGORY_MESHULAM);

            if ($resultStatus == 200) {
                $responseArr = json_decode($response, true);
                LoggerService::info($responseArr, LoggerService::CATEGORY_MESHULAM);

                // get status and payment url
                if ((int)$responseArr['status'] == 1) {
                    // success
                } else {
                    throw new LogicException($responseArr['err']['message']);
                }
            } else {
                curl_close($ch);

                throw new LogicException('התגלתה שגיאה, אנא פנה לתמיכה.');
            }
        }

        curl_close($ch);

        return $responseArr;
    }

    /**
     * @param $response
     * @return mixed
     * @throws Exception
     * @throws Throwable
     */
    private function getTransactionInfo($response)
    {
        $url = $this->getBaseUrl() . 'getTransactionInfo';

        $postData = [
            "apiKey" => Config::get('payment.meshulamLogin.apiKey'),
            "pageCode" => $this->getPageCode(),
            "transactionId" => $response['data']['transactionId'],
            "transactionToken" => $response['data']['transactionToken'],
        ];

        LoggerService::debug([$url, $postData], LoggerService::CATEGORY_MESHULAM);

        $transactionInfoResponse = $this->sendRequest($url, $postData);

        LoggerService::info($transactionInfoResponse, LoggerService::CATEGORY_MESHULAM);

        return $transactionInfoResponse;
    }

    /**
     * @param $data
     * @return array
     */
    public function preparePaymentData($data): array
    {
        $paymentData = array(
            'YaadCode' => $data['transactionId'],
            'CCode' => "0",
            'ACode' => $data['asmachta'] ?? '',
            'Bank' => "9",
            'Brand' => "0",
            'Issuer' => 0,
            'tashTypeDB' => $data['paymentType']  == self::PAYMENT_TYPE_PAYMENTS ? 2 : 1,
            'Payments' => $data['allPaymentsNum'],
            'PayToken' => $data['transactionToken'],
            'TransactionId'  => $data['transactionId'],
            'MeshulamPageCode' => $this->getPageCode(),
        );

        if ((int)$data['transactionTypeId'] === 1) {
            // Payment by card
            $paymentData['L4digit'] = $data['cardSuffix'] ?? '';
            if (isset($data['cardType'])) {
                if ($data['cardType'] === 'Local') {
                    $local = 'ישראלי';
                } else {
                    $local = 'תייר';
                }
                $paymentData['BrandName'] = 'כרטיס ' . $data['cardBrand'] . ' - ' . $local;
            } else {
                $paymentData['BrandName'] = '';
            }
        } else {
            // Payment by BIT
            $paymentData['L4digit'] = $data['cardSuffix'] ?? '';
            $paymentData['BrandName'] = 'BIT';
        }
        return $paymentData;
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
     * @param DocsPayment $DocsPayment
     * @param $SettingsInfo
     * @param OrderLogin $OrderLogin
     * @return BaseResponse
     */
    public function refundPayment(DocsPayment $DocsPayment, $SettingsInfo, OrderLogin $OrderLogin) : BaseResponse
    {
        $Response = new BaseResponse();
        try {
            //$refundData = $paymentSystem->makeRefund($SettingsInfo->MeshulamAPI, $SettingsInfo->MeshulamUserId, $SettingsInfo->MeshulamAPIKey, $TempPaymentInfo->TransactionId, $TempPaymentInfo->PayToken, $TempPaymentInfo->Amount);
            $isFullRefund = !(isset($OrderLogin->TotalAmount) && $OrderLogin->Amount < $OrderLogin->TotalAmount);
            $refundData = $this->makeRefundOldAPI($SettingsInfo->MeshulamAPI, $SettingsInfo->MeshulamUserId, $DocsPayment->YaadCode, $DocsPayment->PayToken, $OrderLogin->Amount, $isFullRefund);
            $Transaction = new Transaction();
            $Transaction->CompanyNum = $DocsPayment->CompanyNum;
            $Transaction->ClientId = $DocsPayment->ClientId;
            $docsPaymentRefundData = $DocsPayment->getArrayRefundTransactionDetails();
            if(!empty($docsPaymentRefundData)) {
                $refundData = array_merge($refundData, $docsPaymentRefundData);
            }
            $Transaction->UpdateTransactionDetails = serialize($refundData);
            $Transaction->UserId = Auth::user()->id ?? 0;
            $Transaction->save();

            $OrderLogin->TransactionId = $Transaction->id;
            $OrderLogin->save();

        } catch (Throwable $e) {
            $TransactionError = new TransactionError();
            $TransactionError->CompanyNum = $DocsPayment->CompanyNum;
            $TransactionError->ClientId = $DocsPayment->ClientId;
            $TransactionError->UpdateTransactionDetails = $e->getMessage();
            $TransactionError->UserId = Auth::user()->id ?? 0;
            $TransactionError->save();

            // unexpected error
            LoggerService::error($e, LoggerService::CATEGORY_MESHULAM);
            $Response->setError($e->getMessage());
        }
        return $Response;
    }

    /**
     * @return int
     */
    public function getTypeShva(): int
    {
        return PaymentSystem::TYPE_MESHULAM;
    }

    /**
     * @param Client $Client
     * @param DocsPayment $DocsPayment
     * @param bool $unionPaymentsTransaction - group all payment by yaad code
     * @param int|null $numberPayment
     * @return OrderLogin
     */
    public function createOrderRefund(Client $Client, DocsPayment $DocsPayment, bool $unionPaymentsTransaction = false,  ?int $numberPayment = null): OrderLogin
    {
        return parent::createOrderRefund($Client, $DocsPayment, $unionPaymentsTransaction, 1);
    }

}



