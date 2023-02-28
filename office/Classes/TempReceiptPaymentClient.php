<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/OrderLogin.php';
require_once __DIR__ . '/Token.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $TypeDoc
 * @property $TempId
 * @property $TypePayment
 * @property $Amount
 * @property $L4digit
 * @property $YaadCode
 * @property $CCode
 * @property $ACode
 * @property $Bank
 * @property $Payments
 * @property $Brand
 * @property $BrandName
 * @property $Issuer
 * @property $tashType
 * @property $CheckBank
 * @property $CheckBankSnif
 * @property $CheckBankCode
 * @property $CheckNumber
 * @property $CheckDate
 * @property $BankNumber
 * @property $BankDate
 * @property $Dates
 * @property $UserId
 * @property $Excess
 * @property $UserDate
 * @property $CreditType
 * @property $PayToken
 * @property $TransactionId
 * @property $TokenId
 * @property $PaymentType
 * @property $PaymentConfirmed
 * @property $ClientActivityId
 * @property $OrderId
 * @property $MeshulamPageCode
 *
 * Class TempReceiptPaymentClient
 */
class TempReceiptPaymentClient extends Model
{
    /**
     * @var string
     */
    protected $table = 'boostapp.temp_receipt_payment_client';

    /** @var OrderLogin|null */
    protected $_order;
    /** @var Token|null */
    protected $_token;

    /**
     * @param $tempId
     * @param $typeDoc
     * @param $companyNum
     * @param null $clientActivityId
     * @return mixed
     */
    public static function getReceiptPaymentTempByClientId($tempId, $typeDoc, $companyNum, $clientActivityId = null)
    {
        $query = self::where('TempId', '=', $tempId)
            ->where('PaymentConfirmed', '=', 1)
            ->where('TypeDoc', '=', $typeDoc)
            ->where('CompanyNum', '=', $companyNum);
        if (isset($clientActivityId)) {
            $query->where('ClientActivityId', '=', $clientActivityId);
        }
        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * @param $tempId
     * @param $typeDoc
     * @param $companyNum
     * @param $clientActivityId
     * @param bool $withCard
     * @return mixed
     */
    public static function getReceiptPaymentWithOrWithOutCard($tempId, $typeDoc, $companyNum, $clientActivityId, bool $withCard)
    {
        $markCreditCard = $withCard ? '=' : '<>';

        return self::where('TempId', '=', $tempId)
            ->where('CompanyNum', '=', $companyNum)
            ->where('PaymentConfirmed', '=', 1)
            ->where('TypePayment', $markCreditCard, 3)
            ->where('TypeDoc', '=', $typeDoc)
            ->where('ClientActivityId', '=', $clientActivityId)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param $tempId
     * @param $typeDoc
     * @param $companyNum
     * @param $param
     * @param $clientActivityId
     * @return mixed
     */
    public static function getReceiptPaymentTempSum($tempId, $typeDoc, $companyNum, $param = 'Amount', $clientActivityId = null)
    {
        $query = self::where('TempId', '=', $tempId)
            ->where('PaymentConfirmed', '=', 1)
            ->where('TypeDoc', '=', $typeDoc)
            ->where('CompanyNum', '=', $companyNum);
        if (isset($clientActivityId)) {
            $query->where('ClientActivityId', '=', $clientActivityId);
        }
        return $query->sum($param);
    }

    /**
     * @return OrderLogin|null
     */
    public function order()
    {
        if (!$this->_order) {
            if ($this->OrderId) {
                $this->_order = OrderLogin::where('id', $this->OrderId)->first();
            }
        }
        return $this->_order;
    }
    /**
     * @return Token|null
     */
    public function token()
    {
        if (!$this->_token) {
            if ($this->TokenId) {
                $this->_token = Token::where('id', $this->TokenId)->first();
            }
        }
        return $this->_token;
    }
}
