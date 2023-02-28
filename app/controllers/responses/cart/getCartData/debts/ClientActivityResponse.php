<?php

require_once __DIR__ . '/../../../../../../office/Classes/ClientActivities.php';
require_once __DIR__ . '/../../../../../helpers/ImageHelper.php';

class ClientActivityResponse
{
    public $id;
    public $name;
    public $price;


    /**
     * ClientBaseResponse constructor.
     * @param Client $Client
     */
    public function __construct(ClientActivities $ClientActivity)
    {
        $this->id = (int)$ClientActivity->id;
        $this->name = $ClientActivity->ItemText;
        $this->price = $ClientActivity->BalanceMoney;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }

}
