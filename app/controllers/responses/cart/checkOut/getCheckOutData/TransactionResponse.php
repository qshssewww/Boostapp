<?php

require_once __DIR__ . '/../../../../../../office/Classes/OrderLogin.php';

class TransactionResponse
{
    public $loginOrderId;
    public $price;
    public $numPayment;
    public $l4digit;
    public $creditConfirmationNumber;
    public $creditOriginalChargeDate;

    /**
     * ItemCartResponse constructor.
     * @param OrderLogin $OrderLogin
     * @param string|null $l4digit
     */
    public function __construct(OrderLogin $OrderLogin, string $l4digit = '') {
        $this->loginOrderId = $OrderLogin->id;
        $this->price = $OrderLogin->Amount;
        $this->numPayment = $OrderLogin->NumPayment;
        $this->creditConfirmationNumber = $OrderLogin->ACode ?? null;
        $this->creditOriginalChargeDate = date('Y-m-d', strtotime($OrderLogin->CreatedAt));
        !empty($l4digit) ? $this->l4digit = $l4digit : null;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
