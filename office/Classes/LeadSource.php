<?php
/**
 * @property $id
 * @property $CompanyNum
 * @property $Title
 * @property $Status
 * *$
 * Class LeadSource
 */

class LeadSource extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.leadsource';

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

    public function getLeadSources($companyNum) {
        return DB::table($this->table)->where("CompanyNum", $companyNum)
            ->where("Status", 0)
            ->get();
    }

    public function getAllLeadSources($companyNum){
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->orderBy('Status', 'ASC')->get();
    }

}

