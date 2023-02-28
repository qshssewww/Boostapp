<?php
require_once __DIR__ . '/../../BaseResponse.php';
require_once __DIR__ . '/../CartResponse.php';
require_once __DIR__ . '/../BusinessResponse.php';
require_once __DIR__ . '/../ClientBaseResponse.php';
require_once __DIR__ . '/InvoiceResponse.php';

class SaveInDebtResponse extends BaseResponse implements CartResponse
{
    public $client;
    public $business;
    public $invoice;
    public $receipts;

    /**
     * setClient function
     * @param Client $Client
     * @return void
     */
    public function setClient(Client $Client): void{
        $this->client = new ClientBaseResponse($Client);
    }

    /**
     * setBusiness function
     * @param $Settings
     * @return void
     */
    public function setBusiness($Settings): void{
        $this->business = new BusinessResponse($Settings);
    }

    /**
     * setInvoice function
     * @param Docs $Invoice
     * @return void
     */
    public function setInvoice(Docs $Invoice): void{
        $this->invoice = new InvoiceResponse($Invoice);
    }

    /**
     * addReceipt function
     * @param Docs $Receipt
     * @return void
     */
    public function addReceipt(Docs $Receipt): void{
        $this->receipts[] = new InvoiceResponse($Receipt);
    }

    /**
     * @return bool
     */
    public function getData(): bool
    {
        $response = $this->returnData();
        if ($this->invoice) {
            $response['invoice'] = $this->invoice->getData() ?? [];
        } else {
            $response['invoice'] = null;
        }

        !empty($this->client) ? $response['client'] = $this->client : null;
        !empty($this->business) ? $response['business'] = $this->business : null;
        !empty($this->receipts) ? $response['receipts'] = $this->receipts : null;
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
