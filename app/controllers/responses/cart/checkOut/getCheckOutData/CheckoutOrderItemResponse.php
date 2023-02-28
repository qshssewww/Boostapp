<?php

//require_once __DIR__ . '/../../../../../../office/Classes/Docs.php';
//require_once __DIR__ . '/../../DiscountResponse.php';
require_once __DIR__ . '/ItemCartResponse.php';

class CheckoutOrderItemResponse extends ItemBaseResponse
{
    public $type;
    public $totalPrice;
    public $quantity;
    public $discount;
    public $membershipStartCount;
    public $originalPrice;
    public $packageManualStart;
    public $packageManualEnd;

//    public $quantityMax;
//    public $eachMonth;
//    public $entry;
//    public $durationNum;

    public $date;
    public $time;
    public $durationMin;
    public $diaryId;
    public $coachId;
    public $variantId;


    /**
     * ItemCartResponse constructor.
     */
    public function __construct(CheckoutOrderItem $CheckoutOrderItem, CheckoutOrderItemDetails $CheckoutOrderItemDetails = null)
    {
        parent::__construct($CheckoutOrderItem->getItemId(), $CheckoutOrderItem->Price, $CheckoutOrderItem->Name);
        $this->type = $CheckoutOrderItem->typeToString();
        $this->totalPrice = $CheckoutOrderItem->Price - $CheckoutOrderItem->DiscountAmount;
        $this->quantity = $CheckoutOrderItem->Quantity ?? 1;
        if (isset($CheckoutOrderItem->DiscountAmount) && $CheckoutOrderItem->DiscountAmount > 0) {
            $this->discount = new DiscountResponse($CheckoutOrderItem->DiscountAmount, $CheckoutOrderItem->DiscountType, $CheckoutOrderItem->DiscountValue);
        }
        $this->membershipStartCount = $CheckoutOrderItem->MembershipStartCount;
        $this->variantId = $CheckoutOrderItem->VariantId;
        $this->originalPrice = $CheckoutOrderItem->Price;
        $CheckoutOrderItem->PackageManualStart !== null ? $this->packageManualStart = $CheckoutOrderItem->PackageManualStart : null;
        $CheckoutOrderItem->PackageManualEnd !== null ? $this->packageManualEnd = $CheckoutOrderItem->PackageManualEnd : null;

        if($CheckoutOrderItemDetails !== null) {
            $this->date = $CheckoutOrderItemDetails->Date;
            $this->time = $CheckoutOrderItemDetails->Time;
            $this->durationMin = $CheckoutOrderItemDetails->DurationMin;
            $this->diaryId = $CheckoutOrderItemDetails->DiaryId;
            $this->coachId = $CheckoutOrderItemDetails->CoachId;
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
