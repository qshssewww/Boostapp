<?php
require_once __DIR__ . '/BaseResponse.php';

class IdResponse extends BaseResponse
{
    public $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param false $itemIsArray
     * @return bool
     */
    public function getData($itemIsArray = false): bool
    {
        $response = $this->returnData();
        $response['id'] = $this->id ?? 0;
        echo json_encode($response);
        return true;
    }

    /**
     * @param string $message
     * @param int $status
     * @return bool
     */
    public function returnError(string $message = '', int $status = 400): bool
    {
        $this->setError($message, $status);
        return $this->getData();
    }

}
