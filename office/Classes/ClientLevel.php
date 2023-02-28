<?php
/**
 * @property $id
 * @property $CompanyNum
 * @property $Level
 * *$
 * Class ClientLevel
 */

class ClientLevel extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.clientlevel';

    public static $updateRules =[
        'id' => 'required|integer',
        'Level' => 'required|min:1|max:70',
    ];

    public static $CreateRules =[
        'Level' => 'required|min:1|max:70',
        'CompanyNum' => 'required|integer',
    ];

    public function getLevelById($id) {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->first();
    }

    public function getAllByCompanyNum($companyNum) {
        return DB::table($this->table)
            ->where('CompanyNum', $companyNum)
            ->get();
    }
}