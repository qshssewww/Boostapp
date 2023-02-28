<?php

interface CartResponse
{
    public function returnError(string $message = '', int $status = 400);
    public function getData();
}