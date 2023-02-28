<?php

require_once 'PaymentDB.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $Brands
 * @property $ClientId
 * @property $TokenId
 * @property $TypeKeva
 * @property $NumDate
 * @property $TypePayment
 * @property $Amount
 * @property $NumPayment
 * @property $LastPayment
 * @property $NextPayment
 * @property $CountPayment
 * @property $tashType
 * @property $Tash
 * @property $Status
 * @property $Text
 * @property $ItemId
 * @property $PageId
 * @property $Date
 * @property $UserId
 * @property $TrueDate
 * @property $MultiItems
 * @property $StopInsert
 * *
 * Class PayToken
 **/

class PayToken extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.paytoken';

    /**
     * @param $companyId
     * @param $clientId
     * @return array
     */
    public function isPaytokensByClient($companyId, $clientId)
    {
        return self::where('CompanyNum', '=', $companyId)
            ->where('ClientId', '=', $clientId)
            ->count();
    }

    /**
     * @param $companyId
     * @param $clientId
     * @return array
     */
    public function getPayTokenByClient($companyId, $clientId) {
        return self::where('CompanyNum', '=', $companyId)
            ->where('ClientId', '=', $clientId)
            ->where('Status', '=', '0')
            ->get();
    }

    /**
     * @param $companyId
     * @param $clientId
     * @return array
     */
    public function cancelAllPayTokens($companyId, $clientId)
    {

        $payTokens = self::where('CompanyNum', '=', $companyId)
            ->where('ClientId', '=', $clientId)
            ->where('Status', '=', 0)
            ->get();

        foreach ($payTokens as $payToken){
            (new PaymentDB())->cancelAllPaymentsofPayToken($payToken->id);
        }


        return self::where('CompanyNum', '=', $companyId)
            ->where('ClientId', '=', $clientId)
            ->where('Status', '=', 0)
            ->update(array('Status' => 1));

    }

    public static function isStudioHasPaytokens($company_num) {
        $res = DB::table(self::getTable())
            ->where('CompanyNum', $company_num)
            ->first();

        return !empty($res);
    }

    /**
     * getTypePaymentDescription function
     * @param int $typePaymentId
     * @return string
     */
    public static function getTypePaymentDescription(int $typePaymentId):string{
        switch ($typePaymentId) {
            case 1: return lang('days');
            case 2: return lang('weeks');
            case 3: return lang('months');
            case 4: return lang('years');
            default: return "";
        }
    }

}
