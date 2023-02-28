<?php

/**
 *
 */
class PhoneValidationResponse
{
    public $status = 200;
    public $success = true;
    public $message;
    public $blocked = false;
    public $count;

    /**
     * @return bool
     */
    public function send()
    {
        echo json_encode([
            'status' => $this->status,
            'success' => $this->success,
            'message' => $this->message,
            'blocked' => $this->blocked,
            'count' => $this->count,
        ]);

        return true;
    }
}
