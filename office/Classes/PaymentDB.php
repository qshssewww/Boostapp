<?php

/**
 * @property $id
 * @property $CompanyNym
 * @property $ClientId
 * @property $TypeKeva
 * @property $Amount
 * @property $Date
 * @property $Status
 * @property $Error
 * @property $NumTry
 * @property $L4digit
 * @property $YaadCode
 * @property $CCode
 * @property $ACode
 * @property $Bank
 * @property $Payments
 * @property $Brand
 * @property $Issuer
 * @property $BrandName
 * @property $tashType
 * @property $TryDate
 * @property $LastDate
 * @property $KevaId
 * @property $RandomUrl
 * @property $NumPayment
 * @property $TrueDayNum
 * @property $ActStatus
 * @property $newApi
 * @property $workerStatus
 * *
 * Class PaymentDB
 **/

class PaymentDB extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.payment';

    public function cancelAllPaymentsofPayToken($payToken){
        return self::where('KevaId', '=', $payToken)
            ->where('Status', '=', 0)
            ->update(array('ActStatus' => 1));
    }
}