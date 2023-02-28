<?php

require_once __DIR__ . '/BaseDocResponse.php';


class InvoiceBaseResponse extends BaseDocResponse
{

    public $BalanceAmount;
    public $TypeNumber;
    public $docHeaderTypeName;

    /**
     * @param Docs $Doc
     */
    public function __construct(Docs $Doc)
    {
        parent::__construct($Doc);
        $this->BalanceAmount = $Doc->BalanceAmount;
        $this->TypeId = $Doc->TypeDoc;
        $this->TypeNumber = $Doc->TypeNumber;
        $this->docHeaderTypeName = $Doc->getTypeDocName();
    }

}
