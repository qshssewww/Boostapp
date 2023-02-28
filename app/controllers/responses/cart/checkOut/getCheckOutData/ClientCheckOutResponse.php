<?php

require_once __DIR__ . '/../../../../../../office/Classes/Item.php';
require_once __DIR__ . '/../../ClientBaseResponse.php';

class ClientCheckOutResponse extends ClientBaseResponse
{
    public $balanceAmount;
    public $idNumber;
    public $email;
    public $url;
    public $openOrderId = 0;

    /**
     * ClientCheckOutResponse constructor.
     */
    public function __construct(Client $Client, Docs $Doc)
    {
        //todo-check-name
        parent::__construct($Client);
        $this->balanceAmount = $Doc->BalanceAmount; //todo-ask
        $this->idNumber = $Doc->CompanyId;
        $this->email = $Doc->Email;
        $this->url = '/office/ClientProfile.php?u=' . $Client->id;
    }

    /**
     * @param int $orderId
     */
    public function setOpenOrderId(int $orderId): void
    {
        $this->openOrderId = $orderId;
    }

}
