<?php

require_once __DIR__ . '/../services/PaymentService.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $UpdateTransactionDetails
 * @property $Error
 * @property $Status
 * @property $UserId
 * @property $Dates
 * @property $err_message
 * @property $Amount
 * @property $Payments
 * @property $MoneyBack
 * @property $UserDate
 * @property $Type
 * @property $Transaction Payment transaction from payment system
 * @property $MeshulamPageCode
 */
class Transaction Extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.transaction';

    /**
     * Saves the transaction to the database.
     *
     * @param Client $client The client that the transaction belongs to.
     * @param array $paymentResult The payment result from the payment system.
     * @param int $UserId ID of manager who made the transaction.
     * @return Transaction
     */
    public static function saveTransaction(Client $client, array $paymentResult, int $UserId = 0)
    {
        $transaction = new self();
        $transaction->CompanyNum = $client->CompanyNum;
        $transaction->ClientId = $client->id;
        $transaction->UpdateTransactionDetails = serialize($paymentResult);
        $transaction->UserId = $UserId;
        $transaction->Transaction = $paymentResult['YaadCode'] ?? 0;
        $transaction->save();
        return $transaction;
    }


    /**
     * @param OrderLogin $OrderLogin
     * @param array $paymentResult
     * @param int $UserId
     * @return Transaction
     */
    public static function saveTransactionByOrderLogin(OrderLogin $OrderLogin, array $paymentResult, int $UserId = 0): Transaction
    {
        $transaction = new self();
        $transaction->CompanyNum = $OrderLogin->CompanyNum;
        $transaction->ClientId = $OrderLogin->ClientId;
        $transaction->UpdateTransactionDetails = serialize($paymentResult);
        $transaction->UserId = $UserId;
        $transaction->Transaction = $paymentResult['YaadCode'] ?? 0;
        $transaction->save();
        return $transaction;
    }

    public function getUpdateTransactionDetails() {
        return unserialize($this->UpdateTransactionDetails , ['allowed_classes' => false]);
    }
}
