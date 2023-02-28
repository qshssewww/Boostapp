<?php

require_once __DIR__ . '/../../../../../../office/Classes/cartClasses/ProductDetails.php';

class ProductVariantsOptionsResponse
{
    public $type;
    public $label;
    public $number;

    /**
     * ProductVariantsOptionsResponse constructor.
     * @param string $type
     * @param string $label
     * @param int $number
     */
    public function __construct(string $type, string $label, int $number)
    {
        $this->type = $type;
        $this->label = $label;
        $this->number = $number;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }




}
