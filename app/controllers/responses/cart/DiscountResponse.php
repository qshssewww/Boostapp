<?php

require_once __DIR__ . '/../../../../office/Classes/Client.php';
require_once __DIR__ . '/../../../helpers/ImageHelper.php';

class DiscountResponse
{
    public $amount;
    public $type;
    public $value;

    /**
     * DiscountResponse constructor.
     * @param float $amount
     * @param int $type
     * @param float $value
     */
    public function __construct(float $amount, int $type,float $value)
    {
        $this->amount = $amount;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }
}
