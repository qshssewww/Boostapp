<?php

require_once __DIR__ . '/../../BaseResponse.php';

class CheckoutCreditsResponse extends BaseResponse
{
    public $creditSavedCard;
    public $iframeUrl;
    public $orderId;
    public $checkOutOrderId;
    public $creditCard2 = [];

    /**
     * @param array $creditCards
     * @param $iframeUrl
     */
    public function __construct(array $creditCards = [], $iframeUrl = null, $orderId = null)
    {
        $this->setCreditCards($creditCards);
        $this->setIframeUrl($iframeUrl);
        $this->setOrderId($orderId);
    }

    /**
     * @param DocsPayment $DocsPayment
     */
    public function addCreditCard(DocsPayment $DocsPayment)
    {
        $this->creditSavedCard[] =[
            'id' => $DocsPayment->id,
            'docPaymentId' => $DocsPayment->id,
            'maxValue' => $DocsPayment->getSum(true),
            'text' => lang('card_meshulam') . '- ' . $DocsPayment->L4digit .'** ' .lang('confirmation_num_meshulam') . ' '. $DocsPayment->ACode .' סכום :' . $DocsPayment->Amount,
        ];
    }

    /**
     * @param Token[] $creditCards
     * @return void
     */
    public function setCreditCards($creditCards)
    {
        foreach ($creditCards as $card) {
            $this->creditSavedCard[] = [
                'id' => $card->id,
                'text' => '****' . $card->L4digit,
            ];
        }
    }


    /**
     * @param Token[] $creditCards
     * @return void
     */
    public function setIframeUrl($iframeUrl)
    {
        $this->iframeUrl = $iframeUrl;
    }

    /**
     * @param $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param int $checkOutOrderId
     * @return void
     */
    public function setCheckOutOrderId(int $checkOutOrderId)
    {
        $this->checkOutOrderId = $checkOutOrderId;
    }

}