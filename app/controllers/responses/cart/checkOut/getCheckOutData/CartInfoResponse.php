<?php

require_once __DIR__ . '/../../../../../../office/Classes/Docs.php';
require_once __DIR__ . '/../../DiscountResponse.php';
require_once __DIR__ . '/ItemCartResponse.php';

class CartInfoResponse
{
    public $id;
    public $vatAmount;
    public $vatPrice;
    public $totalPrice;
    public $amountAfterRefund;
    /** @var DiscountResponse $discount */
    public $discount;
    /** @var ItemCartResponse[] $items */
    public $items;

    /**
     * ProductsResponse constructor.
     * @param Docs $Doc
     */
    public function __construct(Docs $Doc)
    {
        $this->id = $Doc->id;
        $this->vatAmount = $Doc->Vat;
        $this->vatPrice = $Doc->VatAmount;
        $this->totalPrice = $Doc->Amount;
        $this->balanceAmount = $Doc->BalanceAmount;
        $this->discount = new DiscountResponse($Doc->DiscountAmount, $Doc->DiscountType, $Doc->Discount);
    }

    /**
     * @param DocsList $DocsList
     */
    public function addItem(DocsList $DocsList): void
    {
        $this->items[] = new ItemCartResponse($DocsList);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return[
            'id' => $this->id,
            'vatAmount' => $this->vatAmount,
            'vatPrice' => $this->vatPrice,
            'totalPrice' => $this->totalPrice,
            'balanceAmount' => $this->balanceAmount,
            'discount' => $this->discount->getData(),
            'items' => $this->items
        ];
    }


}
