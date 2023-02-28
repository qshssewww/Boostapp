<?php

/**
 * Interface PaymentSystemInterface
 */
interface PaymentSystemInterface {
    /**
     * @return mixed
     */
    public static function getInstance();

    /**
     * @return string|null
     */
    public function getMethod();

    /**
     * @param $response
     * @return mixed
     */
    public function processPayment($response);

    /**
     * @return mixed
     */
    public function getSuccessUrl();

    /**
     * @return mixed
     */
    public function getFailedUrl();

    /**
     * @return mixed
     */
    public function getPaymentUrl();
}
