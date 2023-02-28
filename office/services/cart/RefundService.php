<?php

require_once __DIR__ .  '/CartService.php';


class RefundService extends CartService
{
    /**
     * @param int $docId
     * @param int $checkOrderId
     * @return bool
     */
    public static function getRefundData(int $docId, int $checkOrderId): bool
    {
        $CheckOutDataResponse = new CheckOutDataResponse($docId);
        $companyNum = self::checkAuth($CheckOutDataResponse);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if($docId === 0 && $checkOrderId === 0){
                throw new ErrorException('נתונים לא תקינים');
            }
            if($docId > 0) {
                $invoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($docId);
            } else {
                /** @var CheckoutOrder $CheckoutOrder */
                $CheckoutOrder = CheckoutOrder::find($checkOrderId);
                $invoiceId = $CheckoutOrder->InvoiceId;
            }
            /** @var Docs $InvoiceDoc */
            $InvoiceDoc = Docs::find($invoiceId);
            if($InvoiceDoc === null) {
                throw new ErrorException('docs not valid');
            }
            if($InvoiceDoc->CompanyNum != $companyNum) {
                throw new ErrorException('docs companyNum not valid');
            }
            if(!$InvoiceDoc->isInvoiceDocs()) {
                throw new ErrorException('docs not valid to show - not Invoice');
            }
            if((int)$InvoiceDoc->PayStatus === Docs::PAY_STATUS_CANCELED || (int)$InvoiceDoc->PayStatus === Docs::PAY_STATUS_OPEN) {
                throw new ErrorException('docs not valid to show - canceled or open');
            }
            if($docId > 0 ) {
                $checkOrderId = CheckoutOrder::getOpenOrderIdByClient($InvoiceDoc->ClientId);
                if ($checkOrderId !== 0) {
                    //found open order
                    /** @var CheckoutOrder $CheckoutOrder */
                    $CheckoutOrder = CheckoutOrder::find($checkOrderId);
                    $CheckOutDataResponse->setOpenOrderId($CheckoutOrder->id, ((bool)$CheckoutOrder->IsRefundOrder));
                    return $CheckOutDataResponse->getData();
                }
            }


            $CheckOutDataResponse->setDocId($invoiceId);

            self::addCartToCheckout($InvoiceDoc, $CheckOutDataResponse);
            self::addClientToCheckout($InvoiceDoc, $CheckOutDataResponse);
            if($checkOrderId === 0 && isset($CheckOutDataResponse->client) && !empty($CheckOutDataResponse->client->openOrderId)) {
                $checkOrderId = $CheckOutDataResponse->client->openOrderId;
            }
            self::addReceiptsCheckout($InvoiceDoc, $CheckOutDataResponse);
            self::addRefundCheckout($InvoiceDoc, $CheckOutDataResponse);

            if($checkOrderId) {
                $OrderLoginArray = OrderLogin::getByCheckOutId($checkOrderId);
                foreach ($OrderLoginArray as $OrderLogin) {
                    $CheckOutDataResponse->addTransaction($OrderLogin);
                }
            }

            $CheckOutDataResponse->setTypeShva(Settings::getTypeShvaByCompanyNum($companyNum));

        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $CheckOutDataResponse->returnError($e->getMessage());
        }
        return $CheckOutDataResponse->getData();

    }

    /**
     * @param int $docId
     * @param array $transactions
     * @param int $checkOrderId
     * @param string $remarksText
     * @return bool
     */
    public static function refundDocsByTransactions(int $docId, array $transactions , int $checkOrderId, string $remarksText): bool
    {
        $SaveInDebtResponse = new SaveInDebtResponse();
        $companyNum = self::checkAuth($SaveInDebtResponse);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            $docsInvoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($docId);
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docsInvoiceId);
            if($DocInvoice === null) {
                throw new ErrorException('doc id (Invoice) not valid');
            }
            if((int)$DocInvoice->CompanyNum !== $companyNum) {
                throw new ErrorException('docs companyNum not valid');
            }
            $transactionsArrayData = self::createPaymentDataForDocArray($transactions, 0, true);
            $RefundReceipt = DocsService::createRefundDoc($DocInvoice, $transactionsArrayData, $remarksText);
            if($RefundReceipt === null) {
                throw new ErrorException('error in create refund receipt');
            }
            if($checkOrderId > 0) {
                if(!CheckoutOrder::updateStatusById($checkOrderId, CheckoutOrder::STATUS_AFTER_PAYMENT_CLOSE)) {
                    throw new ErrorException('הוספת המוצרים והפקה תקינה , בעיה בסגרת ההזמנה CheckoutOrderID - ' .  $checkOrderId);
                }
            }
            //ADD TO RESPONSE DATA
            $studioSettings = Settings::getSettings($companyNum);
            $SaveInDebtResponse->setBusiness($studioSettings);
            $Client = self::getOrCreateClient($DocInvoice->CompanyNum, $DocInvoice->ClientId);
            if ($Client) {
                $SaveInDebtResponse->setClient($Client);
            }
//            $SaveInDebtResponse->setInvoice($DocInvoice);
            $SaveInDebtResponse->addReceipt($RefundReceipt);
        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $SaveInDebtResponse->returnError($e->getMessage());
        }
        return $SaveInDebtResponse->getData();

    }

    /**
     * @param int $clientId
     * @param int $tokenId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @return bool
     */
    public static function refundAtTerminalByToken(int $clientId, int $tokenId, float $amount, int $paymentNumber, int $checkOutOrderId, int $invoiceId): bool
    {
        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if($invoiceId === 0) {
                throw new InvalidArgumentException('invoiceId not valid');
            }
            $StudioSettings = (new Settings($companyNum));
            $Client = self::getOrCreateClient($companyNum, $clientId);
            $Token = Token::getById($tokenId);
            if (!$Token || (int)$Token->CompanyNum !== $companyNum || (int)$Token->ClientId !== (int)$Client->id) {
                throw new InvalidArgumentException('Wrong Token ID or Company');
            }
            $PaymentSystem = PaymentService::getPaymentSystemByType($StudioSettings->TypeShva);
            if(!$PaymentSystem->canRefundByToken()) {
                throw new Exception('payment system not support refund by token');
            }
            $CanceledOrder = OrderService::createOrder($Client, $amount, $paymentNumber, OrderLogin::TYPE_PAYMENT_CANCELED, Auth::user()->id);
            $CanceledOrder->TokenId = $tokenId;
            $CanceledOrder->PaymentMethod = $PaymentSystem->getPaymentSystemName();

            $checkOutOrderId = self::createCheckoutOrderAndUpdateLoginOrder($checkOutOrderId, $Client, $invoiceId, $CanceledOrder);
            $Response->setCheckOutOrderId($checkOutOrderId);

            $paymentResult = $PaymentSystem->makeRefundWithToken($CanceledOrder, $Token);
            $CanceledOrder->Status = OrderLogin::STATUS_CANCELLED;
            $transaction = Transaction::saveTransactionByOrderLogin($CanceledOrder, $paymentResult, Auth::user()->id);
            $CanceledOrder->TransactionId = $transaction->id;
            $CanceledOrder->save();
            $CanceledOrder->updateCheckOutOrder();
            $Response->setOrderId($CanceledOrder->id);
        } catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $Response->returnError($e->getMessage());
        }
        return $Response->getData();
    }

    /**
     * @param int $clientId
     * @param int $docPaymentId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @return bool
     */
    public static function refundAtTerminalByDocPayment(int $clientId, int $docPaymentId, float $amount, int $paymentNumber, int $checkOutOrderId, int $invoiceId) : bool
    {
        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if($invoiceId === 0) {
                throw new InvalidArgumentException('invoiceId not valid');
            }
            $StudioSettings = (new Settings($companyNum));
            $Client = self::getOrCreateClient($companyNum, $clientId);
            $PaymentSystem = PaymentService::getPaymentSystemByType($StudioSettings->TypeShva);
            /** @var DocsPayment $DocsPayment */
            $DocsPayment = DocsPayment::find($docPaymentId);
            if (!$DocsPayment || (int)$DocsPayment->CompanyNum !== $companyNum || (int)$DocsPayment->ClientId !== (int)$Client->id) {
                throw new InvalidArgumentException('Wrong DocsPayment ID or Company');
            }
            $CanceledOrder = ReceiptService::refundDocsPayment($Response, $DocsPayment, $PaymentSystem, $Client, $StudioSettings, $amount);
            if($CanceledOrder === null) {
                throw new Exception($Response->getMessage() ?? 'error in refund docs payment');
            }
            $checkOutOrderId = self::createCheckoutOrderAndUpdateLoginOrder($checkOutOrderId, $Client, $invoiceId, $CanceledOrder);
            $Response->setCheckOutOrderId($checkOutOrderId);

            $CanceledOrder->updateCheckOutOrder();
            $Response->setOrderId($CanceledOrder->id);

        } catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $Response->returnError($e->getMessage());
        }
        return $Response->getData();    }

    /**
     * @param int $clientId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @return bool
     */
    public static function refundAtTerminalNewCard(int $clientId, float $amount, int $paymentNumber, int $checkOutOrderId, int $invoiceId): bool
    {
        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            $studioSettings = (new Settings($companyNum));
            $Client = self::getOrCreateClient($companyNum, $clientId);
            $PaymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);
            $CanceledOrder = OrderService::createOrder($Client, $amount, $paymentNumber, OrderLogin::TYPE_REFUND_NEW_CARD, Auth::user()->id);
            $CanceledOrder->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);

            $checkOutOrderId = self::createCheckoutOrderAndUpdateLoginOrder($checkOutOrderId, $Client, $invoiceId, $CanceledOrder);
            $Response->setCheckOutOrderId($checkOutOrderId);

            $iframeUrl = $PaymentSystem->makeCreditPaymentForNewCard($CanceledOrder);
            $Response->setIframeUrl($iframeUrl);
            $Response->setOrderId($CanceledOrder->id);
        } catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $Response->returnError($e->getMessage());
        }
        return $Response->getData();
    }

    /**
     * @param int $checkOutOrderId
     * @param Client $Client
     * @param int $invoiceId
     * @param bool $isRefund
     * @return CheckoutOrder
     */
    private static function getCheckoutOrder(int $checkOutOrderId, Client $Client, int $invoiceId, bool $isRefund = false): CheckoutOrder
    {
        if($checkOutOrderId > 0) {
            /** @var CheckoutOrder $CheckoutOrder */
            $CheckoutOrder = CheckoutOrder::find($checkOutOrderId);
        } else {
            $CheckoutOrder = CheckoutOrder::creatByClient($Client);
            $CheckoutOrder->InvoiceId = $invoiceId;
            if($isRefund) {
                $CheckoutOrder->IsRefundOrder = CheckoutOrder::IS_REFUND_ORDER;
            }
            $CheckoutOrder->save();
            if($CheckoutOrder->id <= 0) {
                throw new InvalidArgumentException('Wrong CheckoutOrder on invoiceId -'.$invoiceId);
            }
        }
        return $CheckoutOrder;
    }

    /**
     * @param int $checkOutOrderId
     * @param Client $Client
     * @param int $invoiceId
     * @param OrderLogin $CanceledOrder
     * @return mixed
     */
    private static function createCheckoutOrderAndUpdateLoginOrder(int $checkOutOrderId, Client $Client, int $invoiceId, OrderLogin $CanceledOrder) {
        $CheckoutOrder = self::getCheckoutOrder($checkOutOrderId, $Client, $invoiceId, true);
        $CanceledOrder->CheckoutOrderId = $CheckoutOrder->id;
        $CanceledOrder->save();
        return  $CheckoutOrder->id;
    }



}
