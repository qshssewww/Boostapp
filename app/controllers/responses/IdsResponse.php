<?php
require_once __DIR__ . '/BaseResponse.php';

class IdsResponse extends BaseResponse implements CartResponse
{
    public $ids;

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     */
    public function setIds(array $ids): void
    {
        $this->ids = $ids;
    }

    /**
     * @param int $id
     */
    public function addId(int $id): void
    {
        if(isset($this->ids)) {
            $this->ids[] = $id;
        } else {
            $this->ids = [$id];
        }
    }

    /**
     * @param false $itemIsArray
     * @return bool
     */
    public function getData($itemIsArray = false): bool
    {
        $response = $this->returnData();
        $response['ids'] = $this->ids;
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
