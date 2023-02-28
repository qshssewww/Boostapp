<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/Token.php';
require_once __DIR__ . '/TempReceiptPayment.php';
require_once __DIR__ . '/TempReceiptPaymentClient.php';
require_once __DIR__ . '/CheckoutOrder.php';

/**
 * @property $id
 * @property $ClientId
 * @property $CompanyNum
 * @property $Amount
 * @property $Discount
 * @property $Interest
 * @property $TotalAmount
 * @property $CouponId
 * @property $CouponCode
 * @property $PaymentType
 * @property $PaymentMethod
 * @property $NumPayment
 * @property $TransactionId
 * @property $TempReceiptId
 * @property $TokenId
 * @property $UserId
 * @property $Description
 * @property $Type Check TYPE_... constants in OrderLogin class
 * @property $Notified
 * @property $CreatedAt
 * @property $Status
 * @property $CheckoutOrderId
 *
 * Internal orders for working with payment systems
 *
 * @class OrderLogin
 */
class OrderLogin extends \Hazzard\Database\Model
{
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_REFUND = 3;

    const STATUS_NOT_NOTIFIED = 0;
    const STATUS_NOTIFIED = 1;

    const TYPE_NEW_CLIENT = 'newClient'; // create new client
    const TYPE_ADD_NEW_CARD = 'addNewCard'; // add new card with J5
    const TYPE_PAYMENT_NEW_CARD = 'newCardPayment'; // pay with new card
    const TYPE_PAYMENT_NEW_CARD_DOCS = 'newCardPaymentForDocs'; // pay with new card for Docs
    const TYPE_PAYMENT_CARD_READER_DOCS = 'cardReaderPaymentForDocs'; // pay with card reader for Docs
    const TYPE_PAYMENT_CARD_READER = 'cardReaderPayment'; // pay with card reader
    const TYPE_PAYMENT_SAVED_CARD_DOCS = 'savedCardPaymentForDocs'; // pay with saved card for Docs
    const TYPE_PAYMENT_SAVED_CARD = 'savedCardPayment'; // pay with saved card
    const TYPE_PAYMENT_SAVED_CARD_MEETING = 'savedCardPaymentMeeting'; // pay with saved card for meeting
    const TYPE_REFUND = 'refund'; // refund
    const TYPE_REFUND_NEW_CARD = 'refundNewCard'; // refund with new card
    const TYPE_CRON_CREDIT_CARD_KEVA = 'cronCreditCardKeva'; // cron CreditCardKeva.php
    const TYPE_CRON_CREDIT_CARD_KEVA_RETURNS = 'cronCreditCardKevaReturns'; // cron CreditCardKevaReturns.php
    const TYPE_PAYMENT_CANCELED = 'paymentCanceled'; // cron CreditCardKevaReturns.php

    protected $table = 'boostapp.order_login';

    private $_client;
    private $_studioSettings;
    private $_tempReceipt;
    private $_token;

    /**
     * @return Client|null|\Hazzard\Database\Model
     */
    public function client()
    {
        if (empty($this->_client)) {
            $client = (new Client())->getClientByCompanyAndId($this->CompanyNum, $this->ClientId);
            if ($client) {
                $this->_client = (new Client($client->id));
            }
        }
        return $this->_client;
    }

    /**
     * @return Settings|null
     */
    public function studioSettings()
    {
        if (empty($this->_studioSettings)) {
            $this->_studioSettings = new Settings($this->CompanyNum);
        }
        return $this->_studioSettings;
    }

    /**
     * @return \Hazzard\Database\Model|Token|null
     */
    public function token()
    {
        if (empty($this->_token)) {
            $this->_token = Token::find($this->TokenId);
        }
        return $this->_token;
    }

    /**
     * @return TempReceiptPaymentClient|TempReceiptPayment|null
     */
    public function tempReceipt()
    {
        if (empty($this->_tempReceipt)) {
            $orderType = $this->Type;

            if ($orderType === OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS || $orderType === OrderLogin::TYPE_PAYMENT_SAVED_CARD_MEETING) {
                $TempReceipt = new TempReceiptPayment();
            } else {
                $TempReceipt = new TempReceiptPaymentClient();
            }

            $this->_tempReceipt = $TempReceipt::find($this->TempReceiptId);
        }
        return $this->_tempReceipt;
    }

    /**
     * @param int $status
     * @return bool
     */
    public function updateCheckOutOrder($status = CheckoutOrder::STATUS_AFTER_PAYMENT_OPEN):bool
    {
        if(!empty($this->CheckoutOrderId)) {
            return CheckoutOrder::updateStatusById($this->CheckoutOrderId, $status);
        }
        return true;
    }

    /**
     * @return OrderLogin[]
     */
    public static function getByCheckOutId(int $checkOutId): array
    {
        return self::where('CheckoutOrderId',$checkOutId)
            ->whereIn('Status', [self::STATUS_PAID, self::STATUS_REFUND])
            ->get();
    }

    /**
     * @param int $checkOutId
     * @return int
     */
    public static function countPaidByCheckOutId(int $checkOutId): int
    {
        return self::where('CheckoutOrderId', $checkOutId)
            ->where('Status', self::STATUS_PAID)
            ->count();
    }


    /**
     * @return OrderLogin
     */
    public function cloneForCanceled(): OrderLogin
    {
        $newOrderLoginArray = $this->toArray();
        $newOrderLoginArray['Type'] = self::TYPE_PAYMENT_CANCELED;
        $newOrderLoginArray['Status'] = self::STATUS_REFUND;
        unset($newOrderLoginArray['id']);
        $OrderLogin = new OrderLogin($newOrderLoginArray);
        $OrderLogin->save();
        return $OrderLogin;
    }

    /**
     * @return array
     */
    public function getTransactionInfo(): array
    {
        /**  @var $Transaction Transaction*/
        $Transaction = Transaction::find($this->TransactionId);
        if($Transaction === null){
            return [];
        }
        return $Transaction->getUpdateTransactionDetails();

    }

}
