<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/services/cart/CartService.php';
require_once __DIR__ . '/../../office/services/receipt/DocsService.php';

/**
 * @class CartController
 */
class CartController extends BaseController
{
    /****************************** cart function ******************************/

    /**
     * @param int|null $clientId
     * @param string $debtId - string of client activity id of some debt
     * @return bool
     */
    public function getCartData(?int $clientId = null, string $debtId = ''): bool
    {
        return CartService::getCartData($clientId, $debtId);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function saveCartUser(int $id): bool
    {
        return CartService::getClientData($id);
    }
    /**
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function getItemDetails(int $id, string $type ='product'): bool
    {
        switch ($type) {
            case 'product':
                return CartService::getProductData($id, $type);
                break;
            default:
                //todo return error
                return false;
        }
    }
    /**
     * @param string $date
     * @return bool
     */
    public function getClientInLesson(int $id): bool
    {
        return CartService::getClientInLesson($id);
    }
    /**
     * @param string $date
     * @return bool
     */
    public function getLessonsData(string $date): bool
    {
        return CartService::getLessonsData($date);
    }
    /**
     * @param string $phone
     * @return bool
     */
    public function checkNewClientPhone(string $phone): bool
    {
        return CartService::checkNewClientPhone($phone);
    }
    /**
     * @param string $itemCategoryType
     * @param int $itemId
     * @param string $itemType
     * @return bool
     */
    public function itemFavoriteAdd(string $itemCategoryType, int $itemId, string $itemType =''): bool
    {
        return CartService::updateFavorite(true, $itemCategoryType, $itemType, $itemId);
    }
    /**
     * @param string $itemCategoryType
     * @param int $itemId
     * @param string $itemType
     * @return bool
     */
    public function itemFavoriteRemove(string $itemCategoryType, int $itemId, string $itemType = ''): bool
    {
        return CartService::updateFavorite(false, $itemCategoryType, $itemType, $itemId);
    }
    /**
     * get array data for docs (discount)
     * @param array $discountArray keys: amount, type, value
     * @return array keys: DiscountAmount, DiscountType, Discount
     */
    private function createDocDataArrayFromDiscount(array $discountArray): array
    {
        $response = [];
        foreach ($discountArray as $key => $discount) {
            switch($key) {
                case 'amount':
                    $response['DiscountAmount'] = $discount;
                    break;
                case 'type':
                    $response['DiscountType'] = $discount;
                    break;
                case 'value':
                    $response['Discount'] = $discount;
                    break;
            }
        }
        return $response;
    }

    /****************************** checkout & cart function ******************************/

    /**
     * @param array $items
     * @param int $clientId
     * @param array $clientDetails
     * @param array $discount
     * @param array $transactions
     * @param int $checkOrderId
     * @return bool
     */
    public function customerKeepInDebt(array $items, int $clientId = 0, array $clientDetails=[], array $discount = [], array $transactions = [], int $checkOrderId = 0): bool
    {
        $docDataArray = $this->createDocDataArrayFromDiscount($discount);
        $docType = empty($transactions) ? DocsService::DOCUMENT_TYPE_INVOICE : DocsService::DOCUMENT_TYPE_RECEPTION;
        return CartService::postCartItems($items, $clientId, $clientDetails, $docDataArray, $docType, $transactions, $checkOrderId);
    }

    /****************************** checkout function ******************************/

    /**
     * @param int $checkOrderId
     * @return bool
     */
    public function getCheckoutDataFromOrder(int $checkOrderId): bool
    {
        return CartService::getCheckoutDataOrderId($checkOrderId);
    }

    /**
     * @param int $docId
     * @param int|null $checkOrderId
     * @return bool
     */
    public function getCheckoutData(int $docId, ?int $checkOrderId = 0): bool
    {
        return CartService::getCheckoutDataFromDoc($docId, $checkOrderId ?? 0);
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
     * @param int|null $clientId
     * @param float $amount
     * @param int $paymentNumber
     * @param array $clientDetails
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @param array $items
     * @param array $discount
     * @return bool
     */
    public function payWithNewCard(?int $clientId = null, float $amount = 1, int $paymentNumber = 1, array $clientDetails=[], int $checkOutOrderId = 0, int $invoiceId = 0, array $items = [], array $discount = []): bool
    {
        $docDataArray = $this->createDocDataArrayFromDiscount($discount);
        return CartService::payWithNewCard($clientId, $amount, $paymentNumber, $clientDetails, $checkOutOrderId, $invoiceId, $items, $docDataArray);
    }

    /**
     * @param int $clientId
     * @param int $tokenId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @param array $items
     * @param array $discount
     * @return bool
     */
    public function payWithToken(int $clientId, int $tokenId, float $amount, int $paymentNumber = 1, int $checkOutOrderId = 0, int $invoiceId = 0 ,array $items = [], array $discount = []): bool
    {
        $docDataArray = $this->createDocDataArrayFromDiscount($discount);
        return CartService::payWithToken($clientId,$tokenId, $amount, $paymentNumber, $checkOutOrderId, $invoiceId ,$items, $docDataArray);
    }

    /**
     * shareBySMS function
     *
     * @param string $phone
     * @param int $invoiceId
     * @param int $receiptId
     * @return boolean
     */
    public function shareBySMS(string $phone, int $invoiceId = 0, int $receiptId = 0): bool
    {
        return CartService::sendDoc($phone, $invoiceId, $receiptId);
    }

    /**
     * @param int $docId
     * @param array $transactions
     * @param int $clientId
     * @param int $checkOrderId
     * @return bool
     */
    public function docIdKeepInDebt(int $docId, array $transactions = [], int $clientId = 0, int $checkOrderId = 0): bool
    {
        $docType = empty($transactions) ? DocsService::DOCUMENT_TYPE_INVOICE : DocsService::DOCUMENT_TYPE_RECEPTION;
        return CartService::createPartialReceipt($docId, $docType, $transactions, $clientId, $checkOrderId);
    }

    /**
     * @param array $items
     * @param int $clientId
     * @param array $clientDetails
     * @param array $discount
     * @param array $transactions
     * @param int $checkOrderId
     * @return bool
     */
    public function goToPaymentConfirmation(array $items, int $clientId = 0, array $clientDetails=[] , array $discount = [], array $transactions = [], int $checkOrderId = 0): bool
    {
        $docDataArray = $this->createDocDataArrayFromDiscount($discount);
        return CartService::postCheckOutItems($items, $clientId, $clientDetails, $docDataArray, $transactions, $checkOrderId);
    }

    /**
     * @param int $docId
     * @param int $checkOrderId
     * @param string $reason
     * @return bool
     */
    public function clearAllCartItems(int $docId, int $checkOrderId = 0, string $reason =''): bool
    {
        return CartService::cancellationEntireOrder($docId, $checkOrderId, $reason);
    }

    /**
     * @param int $loginOrderId
     * @return bool
     */
    public function cancelPaymentWithOutReceipt(int $loginOrderId): bool
    {
        return CartService::cancelPaymentWithOutReceipt($loginOrderId)->getData();
    }

    /**
     * @param int $checkOrderId
     * @return bool
     */
    public function cancelAllPaymentsWithOutReceipt(int $checkOrderId = 0): bool
    {
        return CartService::cancelAllPaymentsWithOutReceipt($checkOrderId)->getData();
    }


}
