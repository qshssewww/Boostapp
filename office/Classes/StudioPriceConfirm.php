<?php 

/**
 * @property $id
 * @property $company_num
 * @property $user_id
 * @property $status
 * @property $created_at
 * @property $updated_at
 */

class StudioPriceConfirm extends \Hazzard\Database\Model {

    protected $table = "boostapp.studio_price_confirm";

    /**
     * getByCompanyNum function
     * @param int $companyNum
     * @return StudioPriceConfirm|null
     */
    public static function getByCompanyNum(int $companyNum) :?StudioPriceConfirm {
        return self::where('company_num', $companyNum)->first();
    }


    /**
     * @param int $status
     * @return bool
     */
    public static function insertNew(int $status) : bool
    {
        $studioPriceConfirm = new self([
            'user_id' => Auth::user()->id,
            'company_num' => (int)Auth::user()->CompanyNum,
            'status' => $status ? 1 : 0
        ]);
        return $studioPriceConfirm->save();
    }
    

}

