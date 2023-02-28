<?php

require_once __DIR__ . '/../../ItemBaseResponse.php';
require_once __DIR__ . '/../../../../../../office/Classes/DocsList.php';

class ItemCartResponse extends ItemBaseResponse
{
    public $type;
    public $totalPrice;
    public $quantity;
    public $discount;

    /**
     * ItemCartResponse constructor.
     */
    public function __construct(DocsList $DocsList)
    {
        parent::__construct($DocsList->id, $DocsList->ItemPrice, $DocsList->ItemName);
        $this->type = 'general';//todo-check
        $this->totalPrice = $DocsList->Itemtotal;
        $this->quantity = $DocsList->ItemQuantity;
        if(isset($DocsList->ItemDiscount) && $DocsList->ItemDiscount > 0) {
            $this->discount = new DiscountResponse($DocsList->ItemDiscountAmount, $DocsList->ItemDiscountType, $DocsList->ItemDiscount);
        }
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
