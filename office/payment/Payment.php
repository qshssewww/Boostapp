<?php
require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/OrderLogin.php';
require_once __DIR__ . '/../Classes/Settings.php';
require_once __DIR__ . '/../Classes/Token.php';
require_once __DIR__ . '/../Classes/TempReceiptPayment.php';
require_once __DIR__ . '/../Classes/TempReceiptPaymentClient.php';
require_once __DIR__ . '/../Classes/TransactionResponseDetails.php';
require_once __DIR__ . '/../Classes/247SoftNew/FixEventLog.php';
require_once __DIR__ . '/../services/payment/Meshulam.php';
require_once __DIR__ . '/../services/payment/Yaad.php';
require_once __DIR__ . '/../services/LoggerService.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../services/receipt/ReceiptService.php';

if (!empty($_REQUEST) && isset($_REQUEST['action'])) {
    $data = $_REQUEST;
    switch ($_REQUEST['action']) {

        //After successful payment in Yaad, get all payment details and call ProcessPayment
        case 'yaadSarigSuccess':
            return (new Payment())->yaadSarigSuccess($data);

        case 'yaadSarigError':
            return (new Payment())->yaadSarigError($data);

        case 'meshulamSuccess':
            return (new Payment())->meshulamSuccess($data);

        case 'meshulamClose':
            return (new Payment())->meshulamClose($data);

        //This request comes from out of the system (from meshulam) after payment
        case 'meshulamHandle':
        case 'mehulamHandle':
            return (new Payment())->meshulamHandle($data);

        case 'addNewCard':
            return (new Payment())->addNewCard($data);
        case 'payWithNewCard':
            return (new Payment())->payWithNewCard($data);

        case 'yaadSuccessUniversal':
            return (new Payment())->yaadSuccessUniversal($data);

        case 'yaadErrorUniversal':
            return (new Payment())->yaadErrorUniversal($data);

        case 'refund':
            return (new Payment())->refund($data);

        case 'cleanTempPayment':
            return (new Payment())->cleanTempPayment($data);
        case 'tranzilaSuccess':
            return (new Payment())->tranzilaSuccess($data);
        case 'tranzilaError':
            return (new Payment())->tranzilaError($data);
        case 'tranzilaHandle':
            return (new Payment())->tranzilaHandle($data);
        case 'getLastToken':
            return (new Payment())->getLastToken($data);
    }
}

/**
 *
 */
class Payment
{
    /**
     * @param $data
     * @throws Exception
     */
    public function yaadSarigSuccess($data)
    {
        $status = 'pay';
        $errorMessage = null;
        if (empty($data)) {
            $errorMessage = "לא נשלח פרמטר נדרש";
        } elseif (empty($data['Order'])) {
            $errorMessage = "מזהה הקבלה נדרש";
        } else {
            $orderId = $data['Order'];

            /** @var OrderLogin|null $order */
            $order = OrderLogin::find($orderId);

            /** @var TempReceiptPaymentClient $tempPaymentInfo */
            $tempPaymentInfo = TempReceiptPaymentClient::find($order->TempReceiptId);

            $companyNum = $order->CompanyNum;
            $client = $order->client();
            $settingsInfo = new Settings($companyNum);

            $paymentSystem = PaymentService::getPaymentSystemByMethod(PaymentService::PAYMENT_YAAD);
            try {
                $paymentData = $paymentSystem->prepareData($data);

                $tempPaymentInfo->L4digit = $paymentData['L4digit'];
                $tempPaymentInfo->YaadCode = $paymentData['YaadCode'];
                $tempPaymentInfo->CCode = $paymentData['CCode'];
                $tempPaymentInfo->ACode = $paymentData['ACode'];
                $tempPaymentInfo->Bank = $paymentData['Bank'];
                $tempPaymentInfo->Brand = $paymentData['Brand'];
                $tempPaymentInfo->Issuer = $paymentData['Issuer'];
                $tempPaymentInfo->BrandName = $paymentData['BrandName'];
                $tempPaymentInfo->TokenId = $paymentData['TokenId'];
                $tempPaymentInfo->PaymentConfirmed = 1;

                $tempPaymentInfo->save();

                $order->TempReceiptId = $tempPaymentInfo->id;
                $order->Status = OrderLogin::STATUS_PAID;
                $order->save();

                ReceiptService::saveReceiptAfterPayWithCard($order);
            } catch (\Throwable $e) {
                $errorMessage = $e->getMessage();
                LoggerService::error($e, LoggerService::CATEGORY_YAADSARIG);
            }
        }
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function yaadSarigError($data)
    {
        $status = 'pay';
        $errorMessage = 'שגיאה בתשלום';
        LoggerService::error($data, LoggerService::CATEGORY_YAADSARIG);
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     * @throws Throwable
     */
    public function addNewCard($data)
    {
        try {
            if (!isset($data['ClientId']) || !is_numeric($data['ClientId'])) {
                throw new LogicException('Wrong Client ID');
            }

            $CompanyNum = Auth::user()->CompanyNum;
            $studioSettings = (new Settings($CompanyNum));
            $client = (new Client($data['ClientId']));

            if (!$client->id || $client->CompanyNum != $CompanyNum) {
                throw new InvalidArgumentException('Wrong Client ID or Company');
            }

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

            $amount = 1;
            if (isset($data['amount']) && $data['amount'] != 0) {
                $amount = $data['amount'];
            }

            $orderType = $data['orderType'];
            if (!in_array($orderType, [OrderLogin::TYPE_ADD_NEW_CARD, OrderLogin::TYPE_REFUND_NEW_CARD])) {
                throw new InvalidArgumentException('Wrong Order Type');
            }

            $order = OrderService::createOrder($client, $amount, 1, $orderType);

            $order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
            $order->save();

            $iframeUrl = $paymentSystem->makeCreditPaymentForNewCard($order);

            $result = [
                'status' => 'success',
                'url' => $iframeUrl,
            ];
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        echo json_encode($result);
    }

    /**
     * @param $data
     * @throws Throwable
     */
    public function payWithNewCard($data)
    {
        try {
            if (!isset($data['ClientId']) || !is_numeric($data['ClientId'])) {
                throw new LogicException('Wrong Client ID');
            }

            $CompanyNum = Auth::user()->CompanyNum;
            $studioSettings = (new Settings($CompanyNum));
            /** @var Client|null $client */
            $client = DB::table('client')->where('id', '=', $data['ClientId'])->where('CompanyNum', '=', $CompanyNum)->first();

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

            $amount = $data['amount'];
            $paymentNumber = $data['paymentNumber'];
            $orderType = $data['orderType'];
            if (!in_array($orderType, [OrderLogin::TYPE_PAYMENT_NEW_CARD, OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS])) {
                throw new InvalidArgumentException('Wrong Order Type');
            }

            $order = OrderService::createOrder($client, $amount, $paymentNumber, $orderType);

            $order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
            $order->save();

            if ($orderType === OrderLogin::TYPE_PAYMENT_NEW_CARD) {
                $TempReceipt = new TempReceiptPaymentClient();
                $TempReceipt->TempId = $client->id;
            } elseif ($orderType === OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS) {
                $TempReceipt = new TempReceiptPayment();
                $TempReceipt->TempId = $data['TempId'];
            } else {
                throw new InvalidArgumentException('Wrong Order Type');
            }

            $savedTempReceipt = $TempReceipt::where('TempId', $TempReceipt->TempId)
                ->where('CompanyNum', $CompanyNum)
                ->where('TypePayment', 3)
                ->first();

            if ($savedTempReceipt) {
                $savedTempReceipt->delete();
            }

            $TempReceipt->CompanyNum = $CompanyNum;
            $TempReceipt->TypeDoc = $data['TypeDoc'];
            $TempReceipt->Dates = date('Y-m-d H:i:s');
            $TempReceipt->UserDate = date('Y-m-d');
            $TempReceipt->UserId = Auth::user()->id;
            $TempReceipt->CheckDate = $TempReceipt->UserDate;
            $TempReceipt->TypePayment = 3; // payment with credit card
            $TempReceipt->CreditType = lang('phone_meshulam'); // payment with NEW credit card
            $TempReceipt->Amount = $amount;
            $TempReceipt->Payments = $paymentNumber;
            $TempReceipt->OrderId = $order->id;
            $TempReceipt->tashType = $paymentNumber > 1 ? 2 : 1;

            $paymentType = $paymentNumber > 1 ? PaymentService::CARD_SYSTEM_PAYMENT_TYPE_PAYMENTS
                : PaymentService::CARD_SYSTEM_PAYMENT_TYPE_REGULAR;

            $iframeUrl = $paymentSystem->makeFirstPayment($order, $paymentType, $paymentNumber);

            $TempReceipt->save();

            $order->TempReceiptId = $TempReceipt->id;
            $order->save();

            $result = [
                'status' => 'success',
                'url' => $iframeUrl,
                'orderId' => $order->id,
            ];
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        echo json_encode($result);
    }

    /**
     * @param $data
     * @throws Throwable
     */
    public function refund($data)
    {
        try {
            if (!isset($data['ClientId'], $data['amount']) || !is_numeric($data['ClientId']) || !is_numeric($data['amount'])) {
                throw new InvalidArgumentException('Wrong Client ID');
            }

            $CompanyNum = Auth::user()->CompanyNum;
            $studioSettings = (new Settings($CompanyNum));
            $client = (new Client($data['ClientId']));

            if (!$client->id || $client->CompanyNum != $CompanyNum) {
                throw new InvalidArgumentException('Wrong Client ID or Company');
            }

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

            $order = OrderService::createOrder($client, $data['amount'], 1, OrderLogin::TYPE_REFUND);

            $order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
            $order->save();

            if (!isset($data['tokenId']) || !($tokenModel = Token::getById($data['tokenId']))) {
                throw new InvalidArgumentException('Wrong token field');
            }

            $paymentResult = $paymentSystem->makeRefundWithToken($order, $tokenModel);

            $transaction = new Transaction();
            $transaction->CompanyNum = $CompanyNum;
            $transaction->ClientId = $client->id;
            $transaction->UpdateTransactionDetails = serialize($paymentResult);
            $transaction->UserId = Auth::user()->id;
            $transaction->save();

            $order->TransactionId = $transaction->id;
            $order->save();

            $result = [
                'status' => 'success',
                'url' => null,
            ];
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        echo json_encode($result);
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function yaadSuccessUniversal($data)
    {
        $response = $data;

        LoggerService::info([
            'action' => 'yaadSuccessUniversal',
            'data' => $data,
            '_GET' => $_GET,
            '_POST' => $_POST,
        ]);
        try {
            if ($data['CCode'] != 600 && $data['CCode'] != 700) {
                if (TransactionResponseDetails::isPaymentExist(TransactionResponseDetails::YAAD, (string)$data['Id'], (string)$data['ACode'])) {
                    $fixEventLog = new FixEventLog();

                    $fixEventLog->project = 'LOGIN';
                    $fixEventLog->message = 'duplicate payment';
                    $fixEventLog->data = json_encode($data, JSON_PRETTY_PRINT);

                    $fixEventLog->save();

                    exit;
                } else {
                    $duplicatePayment = new TransactionResponseDetails();

                    $duplicatePayment->shva = TransactionResponseDetails::YAAD;
                    $duplicatePayment->id_code = $data['Id'];
                    $duplicatePayment->a_code = $data['ACode'];

                    $duplicatePayment->save();
                }
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
        }


        try {
            $order = PaymentService::processPayment($response['Order'], $response, PaymentService::PAYMENT_YAAD);

            $status = 'success';
            $type = $order->Type;
            $tokenId = $order->TokenId;
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $status = 'error';
        }

        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function yaadErrorUniversal($data)
    {
        if(isset($data['Order'])) {
            $del = TempReceiptPaymentClient::where('OrderId', $data['Order'])->delete();
            if(!$del) {
                TempReceiptPayment::where('OrderId', $data['Order'])->delete();
            }
        }
        $status = 'pay';
        $errorMessage = 'שגיאה בתשלום';
        LoggerService::error($data, LoggerService::CATEGORY_YAADSARIG);
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function meshulamSuccess($data)
    {
        LoggerService::info($data, LoggerService::CATEGORY_MESHULAM);
        $status = 'meshulamSuccess';
        $errorMessage = "סוגר תשלום";
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function meshulamClose($data)
    {
        if(isset($data['orderId'])) {
            $del = TempReceiptPaymentClient::where('OrderId', $data['orderId'])->delete();
            if(!$del) {
                TempReceiptPayment::where('OrderId', $data['orderId'])->delete();
            }
        }
        LoggerService::info($data, LoggerService::CATEGORY_MESHULAM);
        $status = 'close';
        $errorMessage = "סוגר תשלום";
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function meshulamHandle($data)
    {
        LoggerService::info([
            'data' => $data,
            '$_POST' => $_POST,
            '$_GET' => $_GET,
        ], LoggerService::CATEGORY_MESHULAM);

        try {
            // fix meshulam wrong field name
            if (isset($data['data']['customField'])) {
                $data['data']['customFields'] = $data['data']['customField'];
                unset($data['data']['customField']);
            }

            $dataParam = $data['data'];

            if (!isset($dataParam['customFields']['cField1'])) {
                throw new InvalidArgumentException('Order ID is empty');
            }
            if (!isset($dataParam['customFields']['cField2'])) {
                throw new InvalidArgumentException('Wrong Client ID');
            }
            if (!isset($dataParam['customFields']['cField3'])) {
                throw new InvalidArgumentException('Wrong Company Num');
            }

            PaymentService::processPayment((int)$dataParam['customFields']['cField1'], $data, PaymentService::PAYMENT_MESHULAM);

            echo 'OK';
        } catch (\Throwable $e) {
            LoggerService::error($e, LoggerService::CATEGORY_MESHULAM);

            echo 'Error';
        }
    }

    /**
     * @param $data
     * @return void
     */
    public function tranzilaError($data)
    {
        $errorMessage = 'שגיאה בתשלום';
        LoggerService::info($data, LoggerService::CATEGORY_TRANZILA);
        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     * @return void
     */
    public function tranzilaSuccess($data)
    {
        $status = 'success';
        $errorMessage = null;

        require_once __DIR__ . '/ProcessPayment.php';
    }

    /**
     * @param $data
     */
    public function tranzilaHandle($data)
    {
        LoggerService::info([
            'data' => $data,
            '$_POST' => $_POST,
            '$_GET' => $_GET,
        ], LoggerService::CATEGORY_TRANZILA);

        try {
            PaymentService::processPayment((int)$data['order'], $data, PaymentService::PAYMENT_TRANZILA);
            echo 'OK';
        } catch (\Throwable $e) {
            LoggerService::error($e, LoggerService::CATEGORY_TRANZILA);

            echo 'Error';
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function cleanTempPayment($data)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $orderType = $data['orderType'];
        if (!in_array($orderType, [OrderLogin::TYPE_PAYMENT_NEW_CARD, OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS])) {
            throw new InvalidArgumentException('Wrong Order Type');
        }

        if ($orderType === OrderLogin::TYPE_PAYMENT_NEW_CARD) {
            $TempReceiptInstance = new TempReceiptPaymentClient();
            $tempId = (int)$data['ClientId'];
        } elseif ($orderType === OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS) {
            $TempReceiptInstance = new TempReceiptPayment();
            $tempId = (int)$data['TempId'];
        } else {
            throw new InvalidArgumentException('Wrong Order Type');
        }

        /** @var TempReceiptPayment[]|TempReceiptPaymentClient[] $tempReceiptsList */
        $tempReceiptsList = $TempReceiptInstance::where('TempId', $tempId)->where('CompanyNum', $CompanyNum)->where('TypePayment', 3)->get();

        foreach ($tempReceiptsList as $tempReceipt) {
            $order = $tempReceipt->order();
            if ($order && $order->Status == OrderLogin::STATUS_UNPAID) {
                $tempReceipt->delete();
            }
        }

        echo json_encode([
            'status' => 'success',
        ]);
    }


    /**
     * @param $data
     */
    public function getLastToken($data): void
    {
        try {
            $orderId = $data['orderId'] ?? 0;
            $previousTokenId = $data['previousTokenId'] ?? null;

            $order = OrderLogin::find($orderId);
            if (!$order) {
                throw new InvalidArgumentException('Wrong Order ID #' . $orderId);
            }

            $client = $order->client();

            $lastTokenQuery = Token::where('CompanyNum', $client->CompanyNum)
                ->where('ClientId', $client->ClientId)
                ->where('Private', 0)
                ->where('Status', Token::STATUS_ACTIVE)
                ->orderBy('TokenId', 'DESC');

            if ($previousTokenId) {
                $lastTokenQuery->where('id', '>', $previousTokenId);
            }

            $lastToken = $lastTokenQuery->first();

            if ($lastToken) {
                $previousTokenId = $lastToken->id;
            } else {
                $previousTokenId = null;
            }

            echo json_encode([
                'status' => 'success',
                'tokenId' => $previousTokenId,
            ]);
        } catch (\Throwable $e) {
            LoggerService::error($e);

            echo json_encode([
                'status' => 'error',
                'tokenId' => null,
            ]);
        }
    }
}
