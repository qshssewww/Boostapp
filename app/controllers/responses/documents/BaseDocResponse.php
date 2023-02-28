<?php

require_once __DIR__ . '/../BaseResponse.php';
require_once __DIR__ . '/../../../../office/Classes/Docs.php';


class BaseDocResponse extends BaseResponse
{
    public $id;
    public $Amount;

    /**
     * BaseDocResponse constructor.
     * @param $id
     * @param $Amount
     */
    public function __construct(Docs $Doc)
    {
        $this->id = $Doc->id;
        $this->Amount = $Doc->Amount;
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
