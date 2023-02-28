<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $Status
 * @property $Amount
 * @property $DiscountType
 * @property $DiscountValue
 * @property $DiscountAmount
 * @property $InvoiceId
 * @property $IsRefundOrder
 */
class CheckoutOrder extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.checkout_order';

    const STATUS_BEFOR_PAYMENT = 0;
    const STATUS_AFTER_PAYMENT_OPEN = 1; //with out docs
    const STATUS_AFTER_PAYMENT_CLOSE = 2; //done with this order

    const IS_NOT_REFUND_ORDER = 0;
    const IS_REFUND_ORDER = 1;

    /**
     * @param array $discount
     */
    public function setDiscount(array $discount = []): void
    {
        if(!empty($discount)) {
            $this->DiscountType = $discount['DiscountType'] ?? $this->DiscountType;
            $this->DiscountValue = $discount['Discount'] ?? $this->DiscountValue;
            $this->DiscountAmount = $discount['DiscountAmount'] ?? $this->DiscountAmount;
        }
    }

    /**
     * @param Client $Client
     * @param array $discount
     * @param int $status
     * @return CheckoutOrder
     */
    public static function creatByClient(Client $Client, array $discount = [], int $status = self::STATUS_BEFOR_PAYMENT): CheckoutOrder {
        $CheckoutOrder = new CheckoutOrder();
        $CheckoutOrder->CompanyNum = $Client->CompanyNum;
        $CheckoutOrder->ClientId = $Client->id;
        $CheckoutOrder->Status = $status;
        $CheckoutOrder->Amount = 0;
        $CheckoutOrder->setDiscount($discount);
        $CheckoutOrder->save();
        return $CheckoutOrder;
    }

    /**
     * @param int $id
     * @param int $status
     * @return bool
     */
    public static function updateStatusById(int $id, int $status): bool
    {
        /** @var CheckoutOrder $CheckoutOrder */
        $CheckoutOrder = self::find($id);
        if($CheckoutOrder === null) {
            return false;
        }
        $prevStatus = $CheckoutOrder->Status;
        if($prevStatus !== $status) {
            $CheckoutOrder->Status = $status;
            if($CheckoutOrder->save()) {
                LoggerService::info('update status of CheckoutOrder.id = '. $id .' from ' .$prevStatus . ' to '  .$CheckoutOrder->Status, LoggerService::CATEGORY_CHECKOUT_ORDER);
            } else{
                return false;
            }
        }
        return true;
    }

    /**
     * @param $clientId
     * @return int
     */
    public static function getOpenOrderIdByClient($clientId): int
    {
        return self::where('ClientId', $clientId)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->pluck('id') ?? 0;
    }

    /**
     * @param $clientId
     * @return CheckoutOrder|null
     */
    public static function getOpenOrderByClient($clientId): ?CheckoutOrder
    {
        return self::where('ClientId', $clientId)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->first();
    }

    /**
     * @param int $docId
     * @return bool
     */
    public static function isInvoiceOpen(int $docId): bool
    {
        return self::where('InvoiceId', $docId)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->first() !== null;
    }

    /**
     * @param int $companyNum
     * @return CheckoutOrder[]
     */
    public static function getAllOpenOrdersInCompany(int $companyNum): array
    {
        return self::where('CompanyNum', $companyNum)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->get();
    }

    /**
     * @param int $companyNum
     * @return bool
     */
    public static function hasOpenOrdersInCompany(int $companyNum): bool
    {
        return (bool)self::where('CompanyNum', $companyNum)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->first();
    }

    /**
     * @param int $companyNum
     * @return CheckoutOrder|null
     */
    public static function getOpenOrdersInCompany(int $companyNum): ?CheckoutOrder
    {
        return self::where('CompanyNum', $companyNum)->where('Status', self::STATUS_AFTER_PAYMENT_OPEN)->first();
    }

    /**
     * @param int $companyNum
     * @return CheckoutOrder|null
     */
    public static function getOpenOrderRandomClient(int $companyNum): ?CheckoutOrder
    {
        $clientId = Client::getRandomClientIdByCompanyNum($companyNum);
        if($clientId > 0) {
            return self::getOpenOrderByClient($clientId);
        }
        return null;
    }
}
