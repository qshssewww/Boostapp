<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/services/cart/CartService.php';
require_once __DIR__ . '/../../office/services/cart/RefundService.php';

/**
 * @class RefundController
 */
class RefundController extends BaseController
{
    /**
     * @param ?int $docId
     * @param ?int $checkOrderId
     * @return bool
     */
    public function getRefundData(?int $docId = 0, ?int $checkOrderId = 0): bool
    {
        $docId = isset($docId) ? $docId : 0;
        $checkOrderId = isset($checkOrderId) ? $checkOrderId : 0;
        return RefundService::getRefundData($docId, $checkOrderId);
    }

    /**
     * @param null $clientId
     * @param int $invoiceId
     * @return bool
     * @throws Throwable
     */
    public function getSavedCardTokens($clientId = null, int $invoiceId = 0): bool
    {
        return CartService::getSavedCardTokens($clientId, $invoiceId);
    }

    /**
     * @param int $docId
     * @param string $remarksText
     * @param array $transactions
     * @param int $checkOrderId
     * @return bool
     */
    public function refundDocs(int $docId, string $remarksText, array $transactions = [], int $checkOrderId = 0): bool
    {
        return RefundService::refundDocsByTransactions($docId, $transactions, $checkOrderId, $remarksText);
    }

    /**
     * @param int $clientId
     * @param float $amount
     * @param int $tokenId
     * @param int $docPaymentId
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @return bool
     */
    public function refundWithToken(int $clientId, float $amount, int $tokenId = 0, int $docPaymentId = 0, int $paymentNumber = 1, int $checkOutOrderId = 0, int $invoiceId = 0): bool
    {
        if($docPaymentId === 0){
            return RefundService::refundAtTerminalByToken($clientId, $tokenId, $amount, $paymentNumber, $checkOutOrderId, $invoiceId);
        }
        return RefundService::refundAtTerminalByDocPayment($clientId, $docPaymentId, $amount, $paymentNumber, $checkOutOrderId, $invoiceId);
    }

    /**
     * @param int|null $clientId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @return bool
     */
    public function refundWithNewCard(?int $clientId = null, float $amount = 1, int $paymentNumber = 1, int $checkOutOrderId = 0, int $invoiceId = 0): bool
    {
        return RefundService::refundAtTerminalNewCard($clientId, $amount, $paymentNumber, $checkOutOrderId, $invoiceId);
    }


//    /**
//     * @param int $clientId
//     * @param int $tokenId
//     * @param float $amount
//     * @param int $paymentNumber
//     * @param int $checkOutOrderId
//     * @param int $invoiceId
//     * @param array $items
//     * @param array $discount
//     * @return bool
//     */
//    public function payWithToken(): bool
//    {
//        $docDataArray = $this->createDocDataArrayFromDiscount($discount);
//        return CartService::payWithToken($clientId,$tokenId, $amount, $paymentNumber, $checkOutOrderId, $invoiceId ,$items, $docDataArray);
//    }
}
