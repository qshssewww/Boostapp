<?php

require_once __DIR__ . '/../../../../../../office/Classes/Docs.php';
require_once __DIR__ . '/../../DiscountResponse.php';
require_once __DIR__ . '/ItemCartResponse.php';

class ReceiptResponse
{
    public $id;
    public $price;
    public $DocType;
    public $DocId;
    public $docHeaderTypeName;
    public $docTypeHeader;

    /**
     * ProductsResponse constructor.
     * @param Docs $Doc
     */
    public function __construct(Docs $Doc)
    {
        $this->id = $Doc->id;
        $this->price = $Doc->Amount;
        $this->DocType = $Doc->TypeDoc;
        $this->DocId = $Doc->TypeNumber;
        $this->docHeaderTypeName = $Doc->getTypeDocName();
        $this->docTypeHeader = $Doc->TypeHeader;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;

    }


}
