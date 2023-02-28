<?php
require_once __DIR__ . '/cart/CartResponse.php';

class BaseResponse implements CartResponse
{
    public $status = 200;
    public $success = true;
    public $message = '';

    /**
     * @return array
     */
    protected function returnData(): array
    {
        return json_decode(json_encode($this), true);
    }

    /**
     * @param string $message
     * @param int $status
     */
    public function setError($message='', int $status = 400):void {
        $this->setStatus($status);
        $this->setSuccess(false);
        $this->setMessage($message);
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

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success)
    {
        $this->success = $success;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
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

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
}
