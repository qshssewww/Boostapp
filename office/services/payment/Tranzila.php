<?php

use Hazzard\Support\Facades\App;

require_once __DIR__ . '/PaymentSystem.php';
require_once __DIR__ . '/PaymentTypeEnum.php';
require_once __DIR__ . '/errors/TranzilaErrors.php';
require_once __DIR__ . '/../../Classes/OrderLogin.php';
require_once __DIR__ . '/../../Classes/AppNotification.php';
require_once __DIR__ . '/../../Utils/HttpClient.php';

/**
 * Class Tranzila
 */
class Tranzila extends PaymentSystem
{
    public const METHOD = 'tranzila';

    protected const PAYMENT_TYPE_REGULAR = 1; // normal transaction
    protected const PAYMENT_TYPE_PAYMENTS = 8; // payments transaction

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return 'https://secure5.tranzila.com/cgi-bin';
    }

    /**
     * @return string
     */
    public function getPaymentSystemName(): string {
        return PaymentService::PAYMENT_TRANZILA;
    }

    /**
     * @return string
     */
    protected function getAppUrl()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            if ($_SERVER['HTTP_HOST'] === 'localhost:8000') {
                $appBaseUrl = 'http://localhost:8000';
            } else {
                $appBaseUrl = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            }
        } else {
            $appBaseUrl = App::url();
        }
        return $appBaseUrl;
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
        // Checks that the transfer information is valid
        if ($response['Response'] === '000') {
            $orderId = (int)$response['order'];
            /** @var OrderLogin|null $order */
            $order = OrderLogin::find($orderId);

            // create payment data
            $paymentData = $this->preparePaymentData($response, $order);
            LoggerService::debug($paymentData, LoggerService::CATEGORY_TRANZILA);

            try {
                $transactionInfo = $this->getTransactionInfo($response, $order);
                LoggerService::info($transactionInfo);
            } catch (\Throwable $e) {
                LoggerService::error($e, LoggerService::CATEGORY_TRANZILA);
            }

            $paymentData['tokenId'] = null;

            //Prepare a new token if needed
            $client = $order->client();

            $clientId = 0;
            if ($client) {
                $clientId = $client->id;
                if ($parent = $client->parent()) {
                    $clientId = $parent->id;
                }
            }

            $tokenData = [
                'CompanyNum' => $order->CompanyNum,
                'ClientId' => $clientId,
                'YaadCode' => $paymentData['YaadCode'],
                'Token' =>  $response['TranzilaTK'],
                'Tokef' => $response['expmonth'] . $response['expyear'],
                'L4digit' => $paymentData['L4digit'],
                'YaadNumber' => 0,
            ];
            $tokenModel = Token::getOrSetToken($tokenData, PaymentTypeEnum::TYPE_TRANZILA);
            if (!$tokenModel) {
                throw new LogicException('Wrong token create');
            }

            $order->TokenId = $tokenModel->id;
            $order->save();

            $paymentData['TokenId'] = $tokenModel->id;

            return $paymentData;
        }
        throw new LogicException(TranzilaErrors::getError($response['Response']));
    }

    /**
     * @param OrderLogin $order
     * @param $responseData
     * @return bool
     */
    private function approveTransaction(OrderLogin $order, $responseData): bool
    {
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
        $url = 'https://direct.tranzila.com/' . $order->studioSettings()->TranzilaTerminal . '/iframenew.php';

        if (Auth::check()) {
            switch (Auth::user()->language) {
                case 'he':
                    $tranzilaLang = 'il';
                    break;
                case 'ru':
                    $tranzilaLang = 'ru';
                    break;
                case 'eng':
                default:
                    $tranzilaLang = 'us';
                    break;
            }
        } else {
            $tranzilaLang = 'il';
        }

        $appBaseUrl = $this->getAppUrl();

        $postData = [
            'sum' => $order->TotalAmount,
            'currency' => 1, // 1 for NIS, 2 for USD, 978 for EUR, 826 for GBP
            'tranmode' => 'VK',
            'hidesum' => 1,
            'nologo' => 1,
            'lang' => $tranzilaLang,
            'buttonLabel' => lang('confirmation'),
            'app' => 'login',
            'order' => $order->id,
            'success_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaSuccess'),
            'fail_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaError'),
            'notify_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaHandle'),
        ];
        
        LoggerService::info($postData, LoggerService::CATEGORY_TRANZILA);

        $url .= '?';
        foreach ($postData as $name => $value) {
            $url .= $name . '=' . $value . '&';
        }

        return $url;
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
        $url = $this->getBaseUrl() . '/tranzila71u.cgi';
        $client = $order->client();
        $clientDetail = $this->getClientPaymentDetails($client);
        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        $appBaseUrl = $this->getAppUrl();

        $postData = [
            'supplier' => $order->studioSettings()->TranzilaTerminal, // 'TERMINAL_NAME' should be replaced by actual terminal name
            'sum' => $paymentDetails['sum'], //Transaction sum
            'expdate' => $tokenModel->Tokef, // Card expiry date: mmyy
            'currency' => '1', //Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
            'TranzilaPW' => $order->studioSettings()->TranzilaPassword, //Token password if required
            'TranzilaTK' => $tokenModel->Token, //Token for the card number
            'cred_type' => $paymentDetails['paymentType'], // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
            'tranmode' => 'A',
            'success_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaSuccess'),
            'fail_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaError'),
            'notify_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaHandle'),
        ];

        if ($paymentDetails['paymentType'] === self::PAYMENT_TYPE_PAYMENTS) {
            $postData = array_merge($postData, [
                'fpay' => $paymentDetails['firstPayment'], //First payment
                'spay' => $paymentDetails['secondPayment'], //Following payments
                'npay' => $paymentDetails['hokPaymentNum'], //for payments transaction npay=number of payments-1, for credit transaction npay=number of payments
            ]);
        }

        LoggerService::info($postData, LoggerService::CATEGORY_TRANZILA);

        // create request to payment with token
        $responsePaymentString = $this->sendRequest($url, $postData, true);

        LoggerService::info($responsePaymentString, LoggerService::CATEGORY_TRANZILA);

        $tempUrl = $this->getBaseUrl() . '/?' . $responsePaymentString;
        $parts = parse_url($tempUrl);
        parse_str($parts['query'], $responsePayment);

        if (isset($responsePayment['Response']) && $responsePayment['Response'] == '000') {
            $paymentData = $this->preparePaymentData($responsePayment, $order);
            $paymentData['PaymentType'] = $paymentDetails['bsappPayments'] ?? $paymentType;
            return $paymentData;
        }
        throw new LogicException(TranzilaErrors::getError($responsePayment['Response']));
    }

    /**
     * @param OrderLogin $order
     * @param $paymentType
     * @param $paymentNum
     * @return mixed
     * @throws Throwable
     */
    public function makeFirstPayment(OrderLogin $order, $paymentType, $paymentNum = 1)
    {
        LoggerService::info([
            'order' => $order,
            'paymentType' => $paymentType,
            'paymentNum' => $paymentNum,
        ]);

        $url = 'https://direct.tranzila.com/' . $order->studioSettings()->TranzilaTerminal . '/iframenew.php';

        if (Auth::check()) {
            switch (Auth::user()->language) {
                case 'he':
                    $tranzilaLang = 'il';
                    break;
                case 'ru':
                    $tranzilaLang = 'ru';
                    break;
                case 'eng':
                default:
                    $tranzilaLang = 'us';
                    break;
            }
        } else {
            $tranzilaLang = 'il';
        }

        $appBaseUrl = $this->getAppUrl();

        $paymentDetails = $this->fixPaymentType($paymentType, $order->TotalAmount, $paymentNum);

        LoggerService::info([
            '$paymentDetails' => $paymentDetails,
        ]);

        $postData = [
            'sum' => $order->TotalAmount,
            'currency' => 1, // 1 for NIS, 2 for USD, 978 for EUR, 826 for GBP
            'tranmode' => 'AK',
            'nologo' => 1,
            'lang' => $tranzilaLang,
            'buttonLabel' => lang('checkout_pay'),
            'app' => 'login',
            'order' => $order->id,
            'success_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaSuccess'),
            'fail_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaError'),
            'notify_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaHandle'),
            'cred_type' => $paymentDetails['paymentType'],
        ];

        if ($paymentDetails['paymentType'] === self::PAYMENT_TYPE_PAYMENTS) {
            $postData = array_merge($postData, [
                'fpay' => $paymentDetails['firstPayment'], //First payment
                'spay' => $paymentDetails['secondPayment'], //Following payments
                'npay' => $paymentDetails['hokPaymentNum'], //for payments transaction npay=number of payments-1, for credit transaction npay=number of payments
            ]);
        }

        LoggerService::info($postData, LoggerService::CATEGORY_TRANZILA);

        $url .= '?';
        foreach ($postData as $name => $value) {
            $url .= $name . '=' . $value . '&';
        }

        return $url;
    }

    /**
     * @param OrderLogin $order
     * @param Token $tokenModel
     * @param int $previousTransactionId
     * @param $confirmationCode
     * @return array
     * @throws Throwable
     */
    public function makeRefundWithToken(OrderLogin $order, Token $tokenModel, int $previousTransactionId, $confirmationCode)
    {
        $appBaseUrl = $this->getAppUrl();

        $postData = [
            'supplier' => $order->studioSettings()->TranzilaTerminal, // 'TERMINAL_NAME' should be replaced by actual terminal name
            'sum' => $order->Amount, // Transaction sum
            'expdate' => $tokenModel->Tokef, // Card expiry date: mmyy
            'currency' => 1, // Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
            'TranzilaPW' => $order->studioSettings()->TranzilaPassword, // Token password if required
            'TranzilaTK' => $tokenModel->Token, // Token for the card number
            'cred_type' => 1, // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
            'tranmode' => 'C' . $previousTransactionId, // This field specifies the transaction type, the letter D should be sent, followed by the index number of the original transaction to be credited. For example, for transaction number 123 send: D123
            'CreditPass' => $order->studioSettings()->TranzilaCreditPass, // Crediting/Cancellations password
            'authnr' => $confirmationCode, // This field indicates the confirmation number received from the credit companies
            'success_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaSuccess'),
            'fail_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaError'),
            'notify_url_address' => urlencode($appBaseUrl . '/office/payment/Payment.php?action=tranzilaHandle'),
        ];

        LoggerService::info($postData, LoggerService::CATEGORY_TRANZILA);

        $url = $this->getBaseUrl() . '/tranzila71u.cgi';
        $output = $this->sendRequest($url, $postData, true);

        $urlSoft = 'https://wwww.247soft.co.il/?' . $output;
        $parts = parse_url($urlSoft);
        parse_str($parts['query'], $response);

        LoggerService::info($response, LoggerService::CATEGORY_TRANZILA);

        if ($response['Response'] === '200' || $response['Response'] === '000') {
            return $response;
        }
        throw new LogicException(TranzilaErrors::getError($response['Response']));
    }

    /**
     * @param $response
     * @param OrderLogin $order
     * @return mixed
     * @throws Throwable
     */
    private function getTransactionInfo($response, OrderLogin $order)
    {
        $url = 'https://secure5.tranzila.com/cgi-bin/billing/tranzila_dates.cgi';

        $postData = [
            'terminal' => $order->studioSettings()->TranzilaTerminal,
            'passw' => $order->studioSettings()->TranzilaPassword,
            'index' => $response['index'],
        ];

        $transactionInfoResponse = $this->sendRequest($url, $postData, true);

        LoggerService::info($transactionInfoResponse, LoggerService::CATEGORY_TRANZILA);

        return $transactionInfoResponse;
    }

    /**
     * @param $data
     * @param OrderLogin $order
     * @return array
     */
    private function preparePaymentData($data, OrderLogin $order): array
    {
        $paymentData = [
            'L4digit' => !empty($data['ccno']) ? $data['ccno'] : substr($data['TranzilaTK'], -4),
            'YaadCode' => $data['index'],
            'CCode' => 0,
            'ACode' => $data['ConfirmationCode'],
            'Bank' => 9,
            'Brand' => 0,
            'tashTypeDB' => $order->NumPayment == 1 ? 1 : 2,
            'Payments' => $order->NumPayment,
            'PayToken' => '',
            'TransactionId' => $data['index'],
        ];

        $cardTypes = [
            0 => 'Issuer Private Card',
            1 => lang('mastercard'),
            2 => lang('visa'),
            3 => 'Maestro',
        ];

        $brand = $data['cardtype'];

        $cardType = $cardTypes[$brand] ?? '';

        $issuer = (int)$data['cardissuer'];
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
     * @param $paymentType
     * @param $totalAmount
     * @param $paymentNum
     * @return array
     */
    private function fixPaymentType($paymentType, $totalAmount, $paymentNum): array
    {
        $paymentDetails = array(
            'sum' => $totalAmount,
            'hokPaymentNum' => $paymentNum, // for payments transaction npay=number of payments-1, for credit transaction npay=number of payments
        );

        switch ((int)$paymentType) {
            case 1:
                if ($paymentNum == 1) {
                    $paymentDetails['paymentType'] = self::PAYMENT_TYPE_REGULAR;
                    break;
                }
            case 2:
                // divide to payments logic
                $amount = $totalAmount / $paymentNum;
                $roundedAmount = ceil(round($amount, 2));
                $restOfPayments = $roundedAmount * ($paymentNum - 1);
                $firstPayment = $totalAmount - $restOfPayments;

                $paymentDetails['firstPayment'] = number_format((float)$firstPayment, 2, '.', '');
                $paymentDetails['secondPayment'] = number_format($roundedAmount, 2, '.', '');

                $paymentDetails['hokPaymentNum'] = $paymentNum - 1; // for payments transaction npay=number of payments-1, for credit transaction npay=number of payments
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

    public function getTypeShva(): int
    {
        return PaymentSystem::TYPE_TRANZILA;
    }

    /**
     * @param DocsPayment $DocsPayment
     * @param OrderLogin $OrderLogin
     * @return BaseResponse
     */
    public function refundPayment(DocsPayment $DocsPayment, OrderLogin $OrderLogin) : BaseResponse
    {
        $Response = new BaseResponse();
        try {
            $refundData = $this->makeRefundWithToken($OrderLogin, $OrderLogin->token(), $DocsPayment->YaadCode, $DocsPayment->ACode);

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
        } catch (LogicException $e) {
            $Response->setError($e->getMessage());
            LoggerService::error([
                'message' => 'Error while making refund',
                'response' => $e->getMessage(),
                'OrderLogin' => $OrderLogin,
            ], LoggerService::CATEGORY_PAYMENT_CANCEL_DOCS);
        } catch (Throwable $e) {
            // unexpected error
            LoggerService::error($e, LoggerService::CATEGORY_TRANZILA);
            $Response->setError('שגיאה לא מזוהה טרנזילה');
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
    public function createOrderRefund(Client $Client, DocsPayment $DocsPayment, bool $unionPaymentsTransaction = false, ?int $numberPayment = null): OrderLogin
    {

        $OrderLogin = parent::createOrderRefund($Client, $DocsPayment, $unionPaymentsTransaction, 1);
        try {
            $previousTransaction = Transaction::where('Transaction', $DocsPayment->TransactionId)->where('ClientId', $DocsPayment->ClientId)->first();
            $transactionDetails = unserialize($previousTransaction->UpdateTransactionDetails, ['allowed_class' => [stdClass::class]]);
            $OrderLogin->TokenId = $transactionDetails['TokenId'] ?? 0;
            $OrderLogin->PaymentMethod = PaymentService::getPaymentMethodByType($this->getTypeShva());
            $OrderLogin->save();
        } catch (Throwable $e) {
            $OrderLogin->TokenId = 0;
            return $OrderLogin;
        }
        return  $OrderLogin;
    }
}
