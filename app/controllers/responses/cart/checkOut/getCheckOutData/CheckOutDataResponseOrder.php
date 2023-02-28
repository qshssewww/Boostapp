<?php

require_once __DIR__ . '/../../../BaseResponse.php';
require_once __DIR__ . '/../../CartResponse.php';
require_once __DIR__ . '/../../DiscountResponse.php';
require_once __DIR__ . '/CheckoutOrderItemResponse.php';
require_once __DIR__ . '/TransactionResponse.php';

require_once __DIR__ . '/../../../../../../office/Classes/CheckoutOrder.php';
require_once __DIR__ . '/../../../../../../office/Classes/CheckoutOrderItem.php';
require_once __DIR__ . '/../../../../../../office/Classes/CheckoutOrderItemDetails.php';
require_once __DIR__ . '/../../../../../../office/Classes/Settings.php';
require_once __DIR__ . '/../../../../../../office/Classes/Client.php';


class CheckOutDataResponseOrder extends BaseResponse implements CartResponse
{
    public $checkoutOrderId;
    public $vatAmount;
    public $businessType;

    public $totalPrice;
    /** @var DiscountResponse $discount */
    public $discount;

    public $items;
    public $itemCount; //code from item

//    public $originalPrice;//?
//    public $subtotalPrice;//?

//    public $totalPriceMinusVat;//calc todo remove
//    public $vatPrice; //vcalc todo remove


    public $clientId;//todo-remove
    /** @var ClientBaseResponse $clientDetails */
    public $clientDetails;

    public $transactions = [];

    /**
     * CheckOutDataResponse constructor.
     * @param int $orderId
     */
    public function __construct(int $checkoutOrderId = 0)
    {
        $this->checkoutOrderId = $checkoutOrderId;
    }

    /**
     * @param Client $Client
     */
    public function setClient(Client $Client): void
    {
        $this->clientDetails = new ClientBaseResponse($Client);
        $this->clientId = $Client->id;//todo-remove
    }

    /**
     * @param $Settings
     */
    public function setSettings($Settings): void
    {
        $this->vatAmount = (int)$Settings->CompanyVat === 0 ? $Settings->Vat : 0;
        $this->businessType = $Settings->BusinessType;
    }

    /**
     * @param CheckoutOrder $CheckoutOrder
     */
    public function setCheckoutOrder(CheckoutOrder $CheckoutOrder): void
    {
        $this->totalPrice = $CheckoutOrder->Amount - ($CheckoutOrder->DiscountAmount ?? 0);
        $this->discount = new DiscountResponse($CheckoutOrder->DiscountAmount, $CheckoutOrder->DiscountType, $CheckoutOrder->DiscountValue);
    }

    /**
     * @param CheckoutOrderItem $CheckoutOrderItem
     */
    public function addItem(CheckoutOrderItem $CheckoutOrderItem): void
    {
        if((int)$CheckoutOrderItem->Type === CheckoutOrderItem::TYPE_SERVICE) {
            $CheckoutOrderItemDetails = CheckoutOrderItemDetails::getByCheckoutOrderItemId($CheckoutOrderItem->id);
        } else {
            $CheckoutOrderItemDetails = null;
        }
        $this->items[] = new CheckoutOrderItemResponse($CheckoutOrderItem, $CheckoutOrderItemDetails);
    }


    /**
     * @param OrderLogin $OrderLogin
     */
    public function addTransaction(OrderLogin $OrderLogin): void
    {
        $l4digit = '';
        if($OrderLogin->TokenId) {
            $l4digit = Token::getL4digitById($OrderLogin->TokenId);
        }
        $this->transactions[] = new TransactionResponse($OrderLogin, $l4digit);
    }

    /**
     * @return bool
     */
    public function getData(): bool
    {
        $response = $this->returnData();
        $response['itemCount'] = empty($this->items) ? 0 : count($this->items);
        echo json_encode($response);
        return true;
    }

    /**
     * @param string $message
     * @param int $status
     * @return bool
     */
    public function returnError(string $message = '', int $status = 400): bool
    {
        $this->setError($message, $status);
        return $this->getData();
    }

}
