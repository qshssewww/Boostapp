<?php

require_once __DIR__ . '/BaseDocResponse.php';


class OffsetDocResponse extends BaseDocResponse
{

    public $BalanceAmount;
    public $PayStatus;
    public $refundAmount;
    public $Refound;

    /**
     * @param Docs $Doc
     */
    public function __construct(Docs $Doc)
    {
        parent::__construct($Doc);
        $this->BalanceAmount = $Doc->BalanceAmount;
        $this->PayStatus = $Doc->PayStatus;
        $this->refundAmount = $Doc->refundAmount;
        $this->Refound = $Doc->Refound;
    }

}
