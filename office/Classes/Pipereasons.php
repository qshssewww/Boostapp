<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $Title
 * @property $Status
 * @property $Act
 *
 * Class Pipereasons
 *
 */
class Pipereasons extends \Hazzard\Database\Model
{
    protected $table = "boostapp.pipereasons";

    public static $updateRules =[
        'id' => 'required|integer',
        'Title' => 'min:1|max:70|required_if:Status,',
        'Status' => 'integer|between:0,1|required_if:Title,'
    ];

    public static $CreateRules =[
        'Title' => 'required|min:1|max:70',
        'CompanyNum' => 'required|integer',
        'Status' => 'integer|between:0,1'
    ];

    public function getActiveReasonByCompany($companyNum)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('Status', '=', '0')
            ->orderBy('Title', 'ASC')->get();
    }

    public static function getZapierReasonId($companyNum)
    {
        return self::select('id')
            ->where('CompanyNum', '=', $companyNum)
            ->where('Title', '=', 'Zapier')
            ->first();
    }

    public function getAllReasonsByCompany($companyNum)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->orderBy('Status', 'ASC')
            ->get();
    }
}

