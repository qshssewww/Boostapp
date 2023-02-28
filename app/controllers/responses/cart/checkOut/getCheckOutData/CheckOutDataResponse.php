<?php

require_once __DIR__ . '/../../../BaseResponse.php';
require_once __DIR__ . '/../../CartResponse.php';
require_once __DIR__ . '/ClientCheckOutResponse.php';
require_once __DIR__ . '/CartInfoResponse.php';
require_once __DIR__ . '/ReceiptResponse.php';
require_once __DIR__ . '/TransactionResponse.php';

class CheckOutDataResponse extends BaseResponse implements CartResponse
{
    public $docId;
    /** @var ClientCheckOutResponse $client */
    public $client;
    /** @var CartInfoResponse $cart */
    public $cart;
    public $receipts = [];
    public $refunds = [];
    public $transactions = [];
    public $typeShva;
    public $openOrderId;
    public $openOrderRefund = false;



    /**
     * CheckOutDataResponse constructor.
     * @param int $docId
     */
    public function __construct(int $docId = 0)
    {
        $this->docId = $docId;
    }

    /**
     * @param int $docId
     */
    public function setDocId(int $docId = 0)
    {
        $this->docId = $docId;
    }

    /**
     * @param int $openOrderId
     * @param bool $openOrderRefund
     */
    public function setOpenOrderId(int $openOrderId = 0, bool $openOrderRefund = false)
    {
        $this->openOrderId = $openOrderId;
        $this->openOrderRefund = $openOrderRefund;
    }

    /**
     * @param Docs $Doc
     */
    public function setCart(Docs $Doc): void
    {
        $this->cart = new CartInfoResponse($Doc);
    }

    /**
     * @param DocsList $DocsList
     */
    public function addItemToCartInfo(DocsList $DocsList): void
    {
        $this->cart->addItem($DocsList);
    }

    /**
     * @param OrderLogin $OrderLogin
     */
    public function addTransaction(OrderLogin $OrderLogin): void
    {
        $l4digit = '';
        if($OrderLogin->TokenId) {
            $l4digit = Token::getL4digitById($OrderLogin->TokenId);
        }
        $this->transactions[] = new TransactionResponse($OrderLogin, $l4digit);
    }

    /**
     * addReceipt function
     * @param Docs $Doc
     * @return void
     */
    public function addReceipt(Docs $Doc): void
    {
        $this->receipts[] = new ReceiptResponse($Doc);
    }

    /**
     * @param Docs $Doc
     */
    public function addRefund(Docs $Doc): void
    {
        $this->refunds[] = new ReceiptResponse($Doc);
    }

    /**
     * @return bool
     */
    public function getData(): bool
    {
        $response = $this->returnData();
        isset($response['cart']) ? $response['cart'] = $this->cart->getData() : null;
        isset($response['client']) ? $response['client'] = $this->client->getData() : null;
        isset($response['docId']) ? $response['docId'] = $this->docId : null;
        isset($this->typeShva)  ? $response['typeShva'] = $this->typeShva : null;
        isset($this->openOrderId)  ? $response['openOrderId'] = $this->openOrderId : null;
        isset($this->openOrderRefund)  ? $response['openOrderRefund'] = $this->openOrderRefund : null;
        isset($this->receipts) ? $response['receipts'] = $this->receipts : null;
        isset($this->refunds) ? $response['refunds'] = $this->refunds : null;
        isset($this->transactions) ? $response['transactions'] = $this->transactions : null;
        echo json_encode($response);
        return true;
    }

    /**
     * @param int|null $typeShva
     */
    public function setTypeShva(?int $typeShva) {
        if($typeShva !== null) {
            $this->typeShva = $typeShva;
        }
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
     * @param Client $client
     * @param Docs $Doc
     * @param int $openOrderId
     */
    public function setClient(Client $client, Docs $Doc, int $openOrderId = 0): void
    {
        $this->client = new ClientCheckOutResponse($client, $Doc);
        if($openOrderId > 0) {
            $this->client->setOpenOrderId($openOrderId);
        }
    }
}
