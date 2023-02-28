<?php
require_once __DIR__ . '/BaseDocResponse.php';

class RelatedDocInformation extends BaseDocResponse
{

    public $TypeNumber;
    public $docHeaderTypeName;
    public $DocDate;
    public $TypeId;

    /**
     * @param Docs $Doc
     */
    public function __construct(Docs $Doc)
    {
        parent::__construct($Doc);
        $this->TypeNumber = $Doc->TypeNumber;
        $this->TypeId = $Doc->TypeDoc;
        //todo - if refund receipt return 'קבלה החזר' else from DB
        if($Doc->isRefundReceiptDocs()){
            $this->docHeaderTypeName = 'קבלה החזר';
        }
        else {
            $this->docHeaderTypeName = $Doc->getTypeDocName();
        }
        $this->DocDate = $Doc->DocDate;
    }

}
