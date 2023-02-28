<?php
/**
 * @property $id
 * @property $CompanyNum
 * @property $Type
 * @property $Icon
 * @property $Color
 * @property $Status
 * *$
 *
 * Class CalType
 */

class CalType extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.caltype';

    public static $updateRules =[
        'id' => 'required|integer',
        'Type' => 'min:1|max:70|required_if:Status,',
        'Status' => 'integer|between:0,1|required_if:Type,'
    ];

    public static $CreateRules =[
        'Type' => 'required|min:1|max:70',
        'CompanyNum' => 'required|integer',
        'Status' => 'integer|between:0,1',
    ];

    public function getAllCalType($companyNum){
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->orderBy('Status', 'ASC')
            ->get();
    }

    public static function getAllActiveByCompanyNum($companyNum){
        return self::where('CompanyNum', $companyNum)
            ->where('Status', 0)
            ->get();
    }

}

