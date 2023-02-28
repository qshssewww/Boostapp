<?php

require_once __DIR__ . '/../../ItemBaseResponse.php';
require_once __DIR__ . '/../../../../../../office/Classes/Item.php';

class PackageResponse extends ItemBaseResponse
{
    public $eachMonth = false; //PAYMENT_REGULAR
    public $entry; //BalanceClass
    public $durationNum; //Vaild
    public $durationType; //Vaild_Type

    /**
     * PackageResponse constructor.
     */
    public function __construct(ItemAndItemCat $ItemAndItemCat)
    {
        parent::__construct($ItemAndItemCat->item_id, $ItemAndItemCat->item_price,
            $ItemAndItemCat->getItemName(), $ItemAndItemCat->clubMemberShip_name, $ItemAndItemCat->item_favorite);
        if(isset($ItemAndItemCat->item_payment) && (int)$ItemAndItemCat->item_payment === Item::PAYMENT_STANDING_ORDER){
            $this->eachMonth = true;
        }
        switch ($ItemAndItemCat->item_department) {
            case Item::DEPARTMENT_TICKET:
            case Item::DEPARTMENT_TRIAL:
                $this->entry = $ItemAndItemCat->item_balanceClass ?? 1;
            case Item::DEPARTMENT_PERIODIC:
                if(!$this->eachMonth && !empty($ItemAndItemCat->item_vaildType) && !empty($ItemAndItemCat->item_vaild)) {
                    $this->durationNum = $ItemAndItemCat->item_vaild ?? 1;
                    $this->durationType = $ItemAndItemCat->item_vaildType ?? 3;
                }
                break;
        }
    }

    /**
     * @return bool
     */
    public function isEachMonth(): bool
    {
        return $this->eachMonth;
    }

    /**
     * @param bool $eachMonth
     */
    public function setEachMonth(bool $eachMonth): void
    {
        $this->eachMonth = $eachMonth;
    }

    /**
     * @return int
     */
    public function getEntry(): int
    {
        return $this->entry;
    }

    /**
     * @param int $entry
     */
    public function setEntry(int $entry): void
    {
        $this->entry = $entry;
    }

    /**
     * @return int
     */
    public function getDurationNum(): int
    {
        return $this->durationNum;
    }

    /**
     * @param int $durationNum
     */
    public function setDurationNum(int $durationNum): void
    {
        $this->durationNum = $durationNum;
    }

    /**
     * @return int
     */
    public function getDurationType(): int
    {
        return $this->durationType;
    }

    /**
     * @param int $durationType
     */
    public function setDurationType(int $durationType): void
    {
        $this->durationType = $durationType;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
