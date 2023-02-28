<?php


class InvoiceResponse
{
    public $docId;
    public $docType;
    public $docTypeHeader;
    public $typeNumber;
    public $randomUrl;
    public $clientId;
    public $prefixNewUrl;

    /**
     * InvoiceResponse constructor
     * @param Docs $Invoice
     */
    public function __construct(Docs $Invoice){
        $this->docId = $Invoice->id;
        $this->docType = $Invoice->TypeDoc;
        $this->docTypeHeader = $Invoice->TypeHeader;
        $this->typeNumber = $Invoice->TypeNumber;
        $this->randomUrl = $Invoice->RandomUrl ?? "";
        $this->clientId = $Invoice->ClientId ?? 0;
        $this->prefixNewUrl = get_newboostapp_domain();
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }
    
}
