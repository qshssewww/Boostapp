<?php

require_once __DIR__ . '/../BaseResponse.php';
require_once __DIR__ . '/InvoiceBaseResponse.php';
require_once __DIR__ . '/RelatedDocInformation.php';
require_once __DIR__ . '/../../../../office/Classes/Docs.php';


class LinkDocsResponse extends BaseResponse
{

    public $invoice; //InvoiceBaseResponse
    public $linkDocs = [];

    /**
     * BaseDocResponse constructor.
     * @param $id
     * @param $Amount
     */
    public function __construct(Docs $Invoice)
    {
        $this->invoice = new InvoiceBaseResponse($Invoice);
    }

    /**
     * @param Docs $Doc
     */
    public function addLinkDoc(Docs $Doc)
    {
        $this->linkDocs[] = new RelatedDocInformation($Doc);
    }

    /**
     * @return bool
     */
    public function getData(): bool
    {
        $response = $this->returnData();
        echo json_encode($response);
        return true;
    }

}
