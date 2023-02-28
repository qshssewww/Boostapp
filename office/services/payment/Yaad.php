<?php
require_once __DIR__ . '/PaymentSystem.php';
require_once __DIR__ . '/PaymentTypeEnum.php';
require_once __DIR__ . '/../../Classes/OrderLogin.php';
require_once __DIR__ . '/../../Utils/HttpClient.php';
require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../OrderService.php';
require_once __DIR__ . '/../../../app/controllers/responses/BaseResponse.php';


/**
 * Class Yaad
 */
class Yaad extends PaymentSystem
{
    public const METHOD = 'yaad';

    public const PAYMENT_TYPE_REGULAR = 1;
    public const PAYMENT_TYPE_PAYMENTS = 6;

    // Prefixes for Order field that are used in https://login.boostapp.co.il/paymentResolver.php
    // login.boostapp

    public const PREFIX_PROD_CREATE_CLIENT = 'loginCreateClient';
    public const PREFIX_DEV_CREATE_CLIENT = 'devCreateClient';

    public const PREFIX_LOCAL_ADD_NEW_CARD = 'localAddNewCard';
    public const PREFIX_DEV_ADD_NEW_CARD = 'devAddNewCard';
    public const PREFIX_PROD_ADD_NEW_CARD = 'loginAddNewCard';

    public const PREFIX_LOCAL_PAY_WITH_NEW_CARD = 'localPaymentNewCard';
    public const PREFIX_DEV_PAY_WITH_NEW_CARD = 'devPaymentNewCard';
    public const PREFIX_PROD_PAY_WITH_NEW_CARD = 'loginPaymentNewCard';

    public const PREFIX_DEV_PAY_WITH_SAVED_CARD = 'devPaymentSavedCard';
    public const PREFIX_PROD_PAY_WITH_SAVED_CARD = 'loginPaymentSavedCard';

    public const PREFIX_DEV_PAY_WITH_CRON_CARD = 'devPaymentCronCard';
    public const PREFIX_PROD_PAY_WITH_CRON_CARD = 'loginPaymentCronCard';

    public const PREFIX_DEV_PAY_WITH_CRON_CARD_RETURNS = 'devPaymentCronCardReturns';
    public const PREFIX_PROD_PAY_WITH_CRON_CARD_RETURNS = 'loginPaymentCronCardReturns';

    public const PREFIX_DEV_REFUND = 'devRefund';
    public const PREFIX_PROD_REFUND = 'loginRefund';

    /**
     * @return bool
     */
    public function canRefundByToken(): bool {
        return true;
    }

    /**
     * @return string
     */
    public function getPaymentSystemName(): string {
        return PaymentService::PAYMENT_YAAD;
    }

    /**
     * @param false $isTest
     * @return string
     */
    public function getBaseUrl($isTest = false)
    {
        return 'https://icom.yaad.net/p/';
    }

    /**
     *
     */
    public function getYaadKey()
    {
        return Config::get('payment.yaadSarig.key');
    }

    /**
     * @param $response
     * @return array
     * @throws Exception
     * @throws Throwable
     */
    public function prepareData($response): array
    {
        LoggerService::info($response, LoggerService::CATEGORY_YAADSARIG);

        if (!isset($response['CCode'], $response['Order'])) {
            LoggerService::error('missing CCode or Order param', LoggerService::CATEGORY_YAADSARIG);
            throw new Exception('Data not valid');
        }

        if ($response['CCode'] == 0 || $response['CCode'] == 600 || $response['CCode'] == 700) {
            $orderId = (int)$response['Order'];

            /** @var OrderLogin|null $order */
            $order = OrderLogin::find($orderId);
            if (!$order) {
                throw new InvalidArgumentException('Wrong Order ID');
            }

            $tokenData = $this->getTokenFromYaad($response['Id'], $order, $response);
            $tokenModel = Token::getOrSetToken($tokenData, PaymentTypeEnum::TYPE_YAAD);
            if (!$tokenModel) {
                throw new LogicException('Wrong token create');
            }

            LoggerService::debug(['Token' => $tokenModel->toArray()]);

            $order->TokenId = $tokenModel->id;
            $order->save();

            if ($tokenData !== false) {
                $paymentData = $this->preparePaymentData($response, $order);
                return $paymentData;
            }
        }
        throw new LogicException($response['errMsg'] ?? $response['CCode'] . ' Something went wrong. Please, try again');
    }

    /**
     * It's just internal method because we need to get bank card token by order number
     *
     * @param $transactionId
     * @param OrderLogin $order
     * @param $response
     * @return array|false
     * @throws Throwable
     */
    public function getTokenFromYaad($transactionId, OrderLogin $order, $response)
    {
        $client = $order->client();
        $studioSettings = $order->studioSettings();
        $clientId = 0;
        if($client) {
            $clientId = $client->id;
            if ($parent = $client->parent()) {
                $clientId = $parent->id;
            }
        }
        $url = $this->getBaseUrl();
        $postData = [
            'action' => 'getToken',
            'Masof' => $studioSettings->YaadNumber,
            'TransId' => $transactionId,
        ];

        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);

        $tokenResponse = $this->sendRequest($url, $postData, true);

        $this->saveLog($order, $tokenResponse);

        $urlSoft = 'https://wwww.247soft.co.il/?' . $tokenResponse;
        $parts = parse_url($urlSoft);
        parse_str($parts['query'], $tokenResponse);

        if (!in_array($tokenResponse['CCode'], [0, 600, 700])) {
            return false;
        }

        $data = [
            'CompanyNum' => $studioSettings->CompanyNum,
            'ClientId' => $clientId,
            'YaadCode' => $tokenResponse['Id'],
            'Token' => $tokenResponse['Token'],
            'Tokef' => $tokenResponse['Tokef'],
            'L4digit' => $response['L4digit'],
            'YaadNumber' => $studioSettings->YaadNumber,
        ];
        return $data;
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
        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        $postData = [
            "action" => "soft",
            "KEY" => $this->getYaadKey(),
            "Masof" => $order->studioSettings()->YaadNumber,
            "Info" => $order->id, // TODO: change Info field
            "UTF8" => "True",
            "UTF8out" => "True",
            "Amount" => $paymentDetails['sum'],
            "Tash" => $paymentDetails['hokPaymentNum'],
            "J5" => "False",
            "sendemail" => "False",
            "MoreData" => "True",
            "CC" => $tokenModel->Token,
            "Tmonth" => substr($tokenModel->Tokef, 2, 2),
            "Tyear" => substr($tokenModel->Tokef, 0, 2),
            "ClientName" => $clientDetail['firstName'],
            "ClientLName" => $clientDetail['lastName'],
            "cell" => $clientDetail['phone'],
            "email" => $clientDetail['email'],
            "UserId" => "000000000",
            "Token" => "True",
            "Order" => $this->getOrderPrefix($order) . $order->id,
        ];

        if (!empty($tokenModel->sme)) {
            $search = array('m-', 's-', 'q-', 'a-', 'o-', 'v-', 'r-', 'x-', 'p-', 't-');
            $replace = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            $postData["cvv"] = str_replace($search, $replace, $tokenModel->sme);
        }

        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);

        $url = $this->getBaseUrl();
        $output = $this->sendRequest($url, $postData, true);

        $urlSoft = 'https://wwww.247soft.co.il/?' . $output;
        $parts = parse_url($urlSoft);
        parse_str($parts['query'], $response);

        $this->saveLog($order, $output);

        if ($response['CCode'] == 0) {
            $paymentData = $this->preparePaymentData($response, $order);

            LoggerService::info($paymentData, LoggerService::CATEGORY_YAADSARIG);
            return $paymentData;
        }
        throw new LogicException($response['CCode']);
    }

    /**
     * @param OrderLogin $order
     * @param $cardNumberFromReader
     * @param $paymentType
     * @param $paymentNum
     * @return array
     * @throws Throwable
     */
    public function makePaymentWithMagneticStripe(OrderLogin $order, $cardNumberFromReader, $paymentType, $paymentNum): array
    {
        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        $postData = [
            "action" => "soft",
            "KEY" => $this->getYaadKey(),
            "Masof" => $order->studioSettings()->YaadNumber,
            "Info" => $order->id, // TODO: change Info field
            "UTF8" => "True",
            "UTF8out" => "True",
            "Amount" => $paymentDetails['sum'],
            "Tash" => $paymentDetails['hokPaymentNum'],
            "J5" => "False",
            "sendemail" => "False",
            "MoreData" => "True",
            "CC2" => $cardNumberFromReader,
            "ClientName" => $clientDetail['firstName'],
            "ClientLName" => $clientDetail['lastName'],
            "cell" => $clientDetail['phone'],
            "email" => $clientDetail['email'],
            "UserId" => "000000000",
            "Token" => "True",
            "Order" => $this->getOrderPrefix($order) . $order->id,
        ];

        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);

        $url = $this->getBaseUrl();
        $output = $this->sendRequest($url, $postData, true);

        $urlSoft = 'https://wwww.247soft.co.il/?' . $output;
        $parts = parse_url($urlSoft);
        parse_str($parts['query'], $response);

        $this->saveLog($order, $output);

        if ($response['CCode'] == 0) {
            $paymentData = $this->preparePaymentData($response, $order);

            LoggerService::info($paymentData, LoggerService::CATEGORY_YAADSARIG);
            return $paymentData;
        }
        throw new LogicException($response['CCode']);
    }

    /**
     * Create first payment to save card number
     *
     * @param OrderLogin $order
     * @param $paymentType 1 - Direct Debit, 2 - Regular, 4 - Payments
     * @param $paymentNum
     * @return bool
     * @throws Throwable
     */
    public function makeFirstPayment(OrderLogin $order, $paymentType, $paymentNum = 1)
    {
        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);
        $postData = [
            "action" => "APISign",
            "What" => "SIGN",
            "KEY" => $this->getYaadKey(),
            "Masof" => $order->studioSettings()->YaadNumber,
            "Info" => $order->id, // TODO: change Info field
            "UTF8" => "True",
            "UTF8out" => "True",
            "Amount" => $paymentDetails['sum'],
            "ClientName" => $clientDetail['firstName'],
            "ClientLName" => $clientDetail['lastName'],
            "cell" => $clientDetail['phone'],
            "email" => $clientDetail['email'],
            "Tash" => $paymentDetails['hokPaymentNum'],
            "FixTash" => "True",
            "ShowEngTashText" => "False",
            "Coin" => 1, // 1 - ILS, 2 - USD, 3 - EUR, 4 - POUND
            "J5" => "False",
            "Postpone" => "False",
            "Sign" => "True",
            "MoreData" => "True",
            "sendemail" => "False",
            "SendHesh" => "True",
            "PageLang" => "HEB",
            "tmp" => 5,
            "HeshDesc" => "", // TODO: add HeshDesc
            "Order" => $this->getOrderPrefix($order) . $order->id,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);


        $url = $this->getBaseUrl();
        $output = HttpClient::sendRequest('POST', $url, $postData, true);

        $this->saveLog($order, $output);

        return $url . '?action=pay&' . $output;
    }

    /**
     * Makes payment for freezing money to get new token from payment system
     * @param OrderLogin $order
     * @return string
     * @throws Throwable
     */
    public function makeCreditPaymentForNewCard(OrderLogin $order)
    {
        /** @var Client $client */
        $client = $order->client();
        $studioSettings = $order->studioSettings();

        $clientDetail = $this->getClientPaymentDetails($client);

        $postData = [
            "action" => "APISign",
            "What" => "SIGN",
            "KEY" => Config::get('payment.yaadSarig.key'),
            "Masof" => $studioSettings->YaadNumber,
            "UTF8" => "True",
            "UTF8out" => "True",
            "Amount" => 1,
            "ClientName" => $clientDetail['firstName'],
            "ClientLName" => $clientDetail['lastName'],
            "cell" => $clientDetail['phone'],
            "email" => $clientDetail['email'],
            "Tash" => 1,
            "FixTash" => "True",
            "ShowEngTashText" => "False",
            "Coin" => 1, // 1 - ILS, 2 - USD, 3 - EUR, 4 - POUND
            "J5" => "J2",
            "Postpone" => "False",
            "Sign" => "True",
            "MoreData" => "True",
            "sendemail" => "False",
            "SendHesh" => "True",
            "PageLang" => "HEB",
            "tmp" => 7,
            "Info" => 'בדיקת כרטיס ' . $order->id,
            "HeshDesc" => "", // TODO: add HeshDesc
            "Order" => $this->getOrderPrefix($order) . $order->id,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);

        $url = $this->getBaseUrl();

        $iframeUrlParams = $this->sendRequest($url, $postData, true);

        $this->saveLog($order, $iframeUrlParams);

        return $url . '?action=pay&' . $iframeUrlParams;
    }

    /**
     * @param OrderLogin $order
     * @param Token $tokenModel
     * @return array
     * @throws Throwable
     */
    public function makeRefundWithToken(OrderLogin $order, Token $tokenModel)
    {

        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);

        $postData = [
            "action" => "soft",
            "KEY" => $this->getYaadKey(),
            "Masof" => $order->studioSettings()->YaadNumber,
            "zPass" => $order->studioSettings()->YaadzPass,
            "Info" => 'Refund #' . $order->id, // TODO: change Info field
            "UTF8" => "True",
            "UTF8out" => "True",
            "Amount" => $order->TotalAmount,
            "Tash" => $order->NumPayment ?? 1,
            "sendemail" => "False",
            "MoreData" => "True",
            "CC" => $tokenModel->Token,
            "Tmonth" => substr($tokenModel->Tokef, 2, 2),
            "Tyear" => substr($tokenModel->Tokef, 0, 2),
            "ClientName" => $clientDetail['firstName'],
            "ClientLName" => $clientDetail['lastName'],
            "cell" => $clientDetail['phone'],
            "email" => $clientDetail['email'],
            "UserId" => "000000000",
            "Token" => "True",
            "Order" => $this->getOrderPrefix($order) . $order->id,
        ];
        LoggerService::info($postData, LoggerService::CATEGORY_YAADSARIG);

        $url = $this->getBaseUrl();
        $output = $this->sendRequest($url, $postData, true);

        $urlSoft = 'https://wwww.247soft.co.il/?' . $output;
        $parts = parse_url($urlSoft);
        parse_str($parts['query'], $response);

        $this->saveLogReturn($order, $output);

        if ($response['CCode'] == 0) {
            $paymentData = $this->preparePaymentData($response, $order);

            LoggerService::info($paymentData, LoggerService::CATEGORY_YAADSARIG);
            return $paymentData;
        }
        throw new LogicException($response['CCode']);
    }

    /**
     * @param OrderLogin $order
     * @return string
     */
    protected function getOrderPrefix(OrderLogin $order): string
    {
        if ($order->Type) {
            $prefix = '';

            switch ($order->Type) {
                case OrderLogin::TYPE_NEW_CLIENT:
                    $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_CREATE_CLIENT : self::PREFIX_PROD_CREATE_CLIENT;
                    break;
                case OrderLogin::TYPE_ADD_NEW_CARD:
                case OrderLogin::TYPE_REFUND_NEW_CARD:
                    if($_SERVER["HTTP_HOST"] === "localhost:8000") {
                        $prefix = self::PREFIX_LOCAL_ADD_NEW_CARD;
                    } else {
                        $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_ADD_NEW_CARD : self::PREFIX_PROD_ADD_NEW_CARD;
                    }
                    break;
                case OrderLogin::TYPE_PAYMENT_NEW_CARD:
                case OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS:
                    if($_SERVER["HTTP_HOST"] === "localhost:8000") {
                        $prefix = self::PREFIX_LOCAL_PAY_WITH_NEW_CARD;
                    } else {
                        $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_PAY_WITH_NEW_CARD : self::PREFIX_PROD_PAY_WITH_NEW_CARD;
                    }
                    break;
                case OrderLogin::TYPE_PAYMENT_SAVED_CARD:
                case OrderLogin::TYPE_PAYMENT_SAVED_CARD_DOCS:
                case OrderLogin::TYPE_PAYMENT_SAVED_CARD_MEETING:
                    $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_PAY_WITH_SAVED_CARD : self::PREFIX_PROD_PAY_WITH_SAVED_CARD;
                    break;
                case OrderLogin::TYPE_CRON_CREDIT_CARD_KEVA:
                    $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_PAY_WITH_CRON_CARD : self::PREFIX_PROD_PAY_WITH_CRON_CARD;
                    break;
                case OrderLogin::TYPE_CRON_CREDIT_CARD_KEVA_RETURNS:
                    $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_PAY_WITH_CRON_CARD_RETURNS : self::PREFIX_PROD_PAY_WITH_CRON_CARD_RETURNS;
                    break;
                case OrderLogin::TYPE_REFUND:
                case OrderLogin::TYPE_PAYMENT_CANCELED:
                    $prefix = (Config::get('env.env') === 'dev') ? self::PREFIX_DEV_REFUND : self::PREFIX_PROD_REFUND;
                    break;
                default:
                    LoggerService::error('Wrong Order Type for Yaad', LoggerService::CATEGORY_YAADSARIG);
                    return $prefix;
            }

            return $prefix . '-';
        }

        return '';
    }

    /**
     * @param $data
     * @param OrderLogin $order
     * @return array
     *
     * Returns $data = [
     *      'L4digit' => $L4digit,
     *      'YaadCode' => $YaadCode,
     *      'CCode' => $CCode,
     *      'ACode' => $ACode,
     *      'Bank' => $Bank,
     *      'Brand' => $Brand,
     *      'Issuer' => $Issuer,
     *      'BrandName' => $BrandName,
     *      'tashTypeDB' => $tashTypeDB,
     *      'Payments' => $Payments,
     *      'PaymentType' => $PaymentType
     *  ];
     */
    private function preparePaymentData($data, OrderLogin $order): array
    {
        $paymentData = [
            'L4digit' => $data['L4digit'],
            'YaadCode' => $data['Id'],
            'CCode' => $data['CCode'],
            'ACode' => $data['ACode'],
            'Bank' => $data['Bank'],
            'Brand' => $data['Brand'],
            'tashTypeDB' => $data['Payments'] == 1 ? 1 : 2,
            'Payments' => $data['Payments'],
            'PayToken' => '',
            'TransactionId' => $data['Id'],
        ];

        $cardTypes = [
            0 => "אמריקן אקספרס",
            1 => "מסטרקארד",
            2 => "ויזה",
            3 => "דיינרס",
            5 => "ישראכרט"
        ];

        $brand = $data['Brand'];
        $issuer = (int)$data['Issuer'];

        $cardType = $cardTypes[$brand] ?? '';

        if ($issuer == 1) {
            $brandName = 'כרטיס ישראכרט מסוג ' . $cardType;
        } else if ($issuer == 2) {
            $brandName = 'כרטיס כאל מסוג ' . $cardType;
        } else if ($issuer == 3) {
            $brandName = 'כרטיס מסוג דיינרס';
        } else if ($issuer == 4) {
            $brandName = 'כרטיס מסוג אמריקן אקספרס';
        } else if ($issuer == 5) {
            $brandName = 'כרטיס JCB מסוג ' . $cardType;
        } else if ($issuer == 6) {
            $brandName = 'כרטיס לאומי קארד מסוג ' . $cardType;
        } else {
            $brandName = '';
        }
        $paymentData['Issuer'] = $issuer;
        $paymentData['BrandName'] = $brandName;
        $paymentData['PaymentType'] = $order->PaymentType;
        $paymentData['TokenId'] = $order->TokenId;

        return $paymentData;
    }

    /**
     * @param OrderLogin $order
     * @param $urlParams
     */
    private function saveLog(OrderLogin $order, $urlParams)
    {
        try {
            $client = $order->client();

            // Yaad send us URL query params. Let's convert them to array
            $urlSoft = 'https://wwww.247soft.co.il/?' . $urlParams;
            $parts = parse_url($urlSoft);
            parse_str($parts['query'], $response);

            DB::table('log_yaad')->insertGetId([
                'UserId' => Auth::user()->id ?? 0,
                'Text' => $urlParams,
                'ClientId' => $client->id ?? 0,
                'CompanyNum' => Auth::user()->CompanyNum ?? 0,
                'Status' => $response['CCode'] ?? 'CCode is not set',
            ]);

            LoggerService::info($response, LoggerService::CATEGORY_YAADSARIG);
        } catch (\Throwable $e) {
            LoggerService::error($e);
        }
    }

    /**
     * @param OrderLogin $order
     * @param $urlParams
     */
    private function saveLogReturn(OrderLogin $order, $urlParams)
    {
        try {
            $client = $order->client();

            // Yaad send us URL query params. Let's convert them to array
            $urlSoft = 'https://wwww.247soft.co.il/?' . $urlParams;
            $parts = parse_url($urlSoft);
            parse_str($parts['query'], $response);

            DB::table('log_yaad_return')->insertGetId([
                'UserId' => Auth::user()->id ?? 0,
                'Text' => $urlParams,
                'ClientId' => $client->id ?? 0,
                'CompanyNum' => Auth::user()->CompanyNum ?? 0,
                'Status' => $response['CCode'] ?? 'CCode is not set',
            ]);

            LoggerService::info($response, LoggerService::CATEGORY_YAADSARIG);
        } catch (\Throwable $e) {
            LoggerService::error($e);
        }
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
            'hokPaymentNum' => $paymentNum
        );

        switch ($paymentType) {
            case 1:
            case 2:
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
     * @return string[]
     */
    public function getStatusList(): array
    {
        return [
            0 => "עסקה מאושרת",
            1 => "חסום החרם כרטיס",
            2 => "גנוב החרם כרטיס",
            3 => "התקשר לחברת האשראי",
            4 => "סירוב",
            5 => "מזויף החרם כרטיס",
            6 => "ת.ז. או CVV שגויים",
            7 => "חובה להתקשר לחברת האשראי",
            19 => "נסה שנית, העבר כרטיס אשראי",
            33 => "כרטיס לא תקין",
            34 => "כרטיס לא רשאי לבצע במסוף זה או אין אישור לעסקה כזאת",
            35 => "כרטיס לא רשאי לבצע עסקה עם סוג אשראי זה",
            36 => "פג תוקף",
            37 => "שגיאה בתשלומים - סכום העסקה צריך להיות שווה תשלום ראשון + תשלום קבוע כפול מספר התשלומים",
            38 => "לא ניתן לבצע עסקה מעל התקרה לכרטיס לאשרי חיוב מיידי",
            39 => "ספרת ביקורת לא תקינה",
            57 => "לא הוקלד מספר תעודת זהות",
            58 => "לא הוקלד CVV2",
            69 => "אורך הפס המגנטי קצר מידי",
            101 => "אין אישור מחברה אשראי לעבודה",
            106 => "למסוף אין אישור לביצוע שאילתא לאשראי חיוב מיידי",
            107 => "סכום העסקה גדול מידי - חלק למספר עסקאות",
            110 => "למסוף אין אישור לכרטיס חיוב מיידי",
            111 => "למסוף אין אישור לעסקה בתשלומים",
            112 => "למסוף אין אישור לעסקה טלפון/ חתימה בלבד בתשלומים",
            113 => "למסוף אין אישור לעסקה טלפונית",
            114 => "למסוף אין אישור לעסקה חתימה בלבד",
            118 => "למסוף אין אישור לאשראי ישראקרדיט",
            119 => "למסוף אין אישור לאשראי אמקס קרדיט",
            124 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס ישראכרט",
            125 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס אמקס",
            127 => "למסוף אין אישור לעסקת חיוב מיידי פרט לכרטיסי חיוב מיידי",
            129 => "למסוף אין אישור לבצע עסקת זכות מעל תקרה",
            133 => "כרטיס לא תקף על פי רשימת כרטיסים תקפים של ישראכרט",
            138 => "כרטיס לא רשאי לבצע עסקאות בתשלומים על פי רשימת כרטיסים תקפים של ישראכרט",
            146 => "לכרטיס חיוב מיידי אסור לבצע עסקה זכות",
            150 => "אשראי לא מאושר לכרטיסי חיוב מיידי",
            151 => "אשראי לא מאושר לכרטיסי חול",
            156 => "מספר תשלומים לעסקת קרדיט לא תקין",
            160 => "תקרה 0 לסוג כרטיס זה בעסקה טלפונית",
            161 => "תקרה 0 לסוג כרטיס זה בעסקת זכות",
            162 => "תקרה 0 לסוג כרטיס זה בעסקת תשלומים",
            163 => "כרטיס אמריקן אקספרס אשר הנופק בחול לא רשאי לבצע עסקאות תשלומים",
            164 => "כרטיסי JCB רשאי לבצע עסקאות באשראי רגיל",
            169 => "לא ניתן לבצע עסקת זכות עם אשראי שונה מהרגיל",
            171 => "לא ניתן לבצע עסקה מאולצת לכרטיס/אשראי חיוב מיידי",
            172 => "לא ניתן לבטל עסקה קודמת (עסקת זכות או מספר כרטיס אינו זהה)",
            173 => "עסקה כפולה",
            200 => "שגיאה יישומית",
            251 => "נסה שנית, העבר כרטיס אשראי",
            260 => "שגיאה כללית בחברת האשראי. נסה שנית מאוחר יותר.",
            280 => "שגיאה כללית בחברת האשראי, נסה שנית מאוחר יותר.",
            349 => 'אין הרשאה למסוף לאישור J5 ללא חיוב, התקשר לתמיכה.',
            447 => 'מספר כרטיס שגוי',
            901 => "שגיאה במסוף. התקשר לתמיכה BOOSTAPP",
            902 => "שגיאת תקשורת. התקשר לתמיכה BOOSTAPP",
            920 => "לא ניתן לביטול / לא נמצאה העסקה / העסקה בוטלה בעבר",
            997 => "טוקן לא תקין, נא להצפין מחדש את כרטיס האשראי",
            998 => "עסקה בוטלה - BOOSTAPP",
            999 => "שגיאת תקשורת - BOOSTAPP"
        ];
    }

    /**
     * @return int
     */
    public function getTypeShva(): int
    {
        return PaymentSystem::TYPE_YAAD;
    }


    /**
     * @param OrderLogin $OrderLogin
     * @return BaseResponse
     */
    public function refundPayment(OrderLogin $OrderLogin) : BaseResponse
    {
        $Response = new BaseResponse();
        try {
            $refundData = $this->makeRefundWithToken($OrderLogin, $OrderLogin->token());
        } catch (LogicException $e) {
            // payment error
            if (is_numeric($e->getMessage())) {
                $Response->setError(PaymentStatusList::getErrorMessage($e->getMessage()));
            } else {
                $Response->setError($e->getMessage());
            }
            LoggerService::error([
                'message' => 'Error while making refund',
                'response' => $e->getMessage(),
                'OrderLogin' => $OrderLogin,
            ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
        } catch (Throwable $e) {
            // unexpected error
            LoggerService::error($e, LoggerService::CATEGORY_YAADSARIG);
            $Response->setError(lang('unknow_error_meshulam'));
        }
        return $Response;
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
        $Token = $this->getTokenByDocsPayment($DocsPayment , $this->getTypeShva());
        $OrderLogin = parent::createOrderRefund($Client, $DocsPayment, $unionPaymentsTransaction);

        if($Token !== null && empty($Token->Token)) {
            try {
                $tokenData = $this->getTokenFromYaad($DocsPayment->YaadCode, $OrderLogin, ['L4digit' => $DocsPayment->L4digit]);
                $Token->Token = $tokenData['Token'];
                $Token->save();
            } catch (Throwable $e) {
                LoggerService::error($e, LoggerService::CATEGORY_YAADSARIG);
            }
        }
        $OrderLogin->PaymentMethod = PaymentService::getPaymentMethodByType($this->getTypeShva());
        $OrderLogin->TokenId = $Token->id;
        $OrderLogin->save();
        return  $OrderLogin;
    }



}
