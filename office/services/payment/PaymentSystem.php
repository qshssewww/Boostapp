<?php
require_once __DIR__ . '/PaymentSystemInterface.php';
require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../../Classes/Token.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/DocsPayments.php';
require_once __DIR__ . '/../../Classes/OrderLogin.php';
require_once __DIR__ . '/../PaymentService.php';

class PaymentSystem implements PaymentSystemInterface
{
    public const METHOD = null;

    public const TYPE_YAAD = 0;
    public const TYPE_MESHULAM = 1;
    public const TYPE_TRANZILA = 2;

    /**
     * @var static
     */
    protected static $obj;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$obj)) {
            self::$obj = new static();
        }

        return self::$obj;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        self::$obj = null;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return static::METHOD;
    }

    /**
     * @param $url
     * @param $postData
     * @param bool $asString
     * @return array|string
     * @throws Throwable
     */
    protected function sendRequest($url, $postData, bool $asString = false)
    {
        return HttpClient::sendRequest('POST', $url, $postData, $asString);
    }

    /**
     * @return bool
     */
    public function canRefundByToken(): bool {
        return false;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPaymentSystemName(): string {
        throw new Exception('getPaymentSystemName method should be implemented');
    }

    /**
     * Required fields:
     * - CompanyNum
     * - ClientId
     * - Token
     * - Tokef
     * - L4digit
     *
     * Optional:
     * - Cvv
     * - YaadCode
     * - YaadNumber
     *
     * @param $response
     * @return array
     * @throws Exception
     */
    public function prepareData($response): array
    {
        throw new Exception('prepareData method should be implemented');
    }

    /**
     * @param $response
     * @return Order|false
     * @throws Throwable
     */
    public function processPayment($response)
    {
        try {
            [$order, $data] = $this->prepareData($response);

            OrderService::afterPayment($order, $data); //TODO CHANGE

            return $order;
        } catch (\Throwable $e) {
//            LoggerService::error($e, LoggerService::CATEGORY_PAYMENT_PROCESS);
            //TODO PRINT
        }

        return false;
    }

    /**
     * return firstname, lastname, email, phone
     *
     * @param $client
     * @return array
     */
    protected function getClientPaymentDetails($client = null): array
    {
        if ($client) {
            $clientDetail = [
                'firstName' => $client->FirstName,
                'lastName' => !empty($client->LastName) ? $client->LastName : $client->FirstName,
                'email' => $client->Email ?? '',
                'phone' => str_replace('+972', '0', $client->ContactMobile),
                'clientId' => $client->id
            ];

            if ($parent = $client->parent()) {
                $clientDetail['email'] = $parent->Email;
                $clientDetail['clientId'] = $parent->id;
                $clientDetail['phone'] = str_replace('+972', '0', $parent->ContactMobile);
            }
        } else {
            $clientDetail = [
                'firstName' => 'לקוח',
                'lastName' => 'מזדמן',
                'email' => '',
                'phone' => '0500000000',
                'clientId' => 0
            ];
        }

        return $clientDetail;
    }

    /**
     * @param OrderLogin $order
     * @param Token $tokenModel
     * @param $paymentType
     * @param $paymentNum
     * @return array
     * @throws Exception
     */
    public function makePaymentWithToken(OrderLogin $order, Token $tokenModel, $paymentType, $paymentNum) : array
    {
        throw new Exception('makePaymentWithToken method should be implemented');
    }

    /**
     * @return mixed
     */
    public function getSuccessUrl()
    {
        // TODO: Implement getSuccessUrl() method.
    }

    /**
     * @return mixed
     */
    public function getFailedUrl()
    {
        // TODO: Implement getFailedUrl() method.
    }

    /**
     * @return mixed
     */
    public function getPaymentUrl()
    {
        // TODO: Implement getPaymentUrl() method.
    }


    /**
     * @return int
     */
    public function getTypeShva(): int
    {
        // TODO: Implement getTypeShva1() method.
    }


    /**
     * @param Client $Client
     * @param DocsPayment $DocsPayment
     * @param bool $unionPaymentsTransaction - group all payment by yaad code
     * @param int|null $numberPayment
     * @return OrderLogin
     */
    public function createOrderRefund(Client $Client, DocsPayment $DocsPayment, bool $unionPaymentsTransaction = false, ?int $numberPayment = null): OrderLogin
    {
        $numberPayment = empty($numberPayment) ? $DocsPayment->getPaymentsNumber($unionPaymentsTransaction) : $numberPayment;
        return OrderService::createOrder($Client, $DocsPayment->getSum($unionPaymentsTransaction),
            $numberPayment, OrderLogin::TYPE_PAYMENT_CANCELED);
    }


    /**
     * @param DocsPayment $DocsPayment
     * @param int $type
     * @return Token|null
     */
    public function getTokenByDocsPayment(DocsPayment $DocsPayment, int $type = self::TYPE_YAAD ): ?Token
    {
        $Token = Token::getTokenByDocsPayment($DocsPayment, $type);
        if (!$Token) {
            LoggerService::info([
                'message' => 'Token model not found',
                'docsPaymentModel' => $DocsPayment,
            ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
        }
        return $Token;
    }


}
