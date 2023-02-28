<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $Name
 * @property $Status
 *
 * Class TaskStatus
 */
class TaskStatus extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.task_status';

    public static $updateRules = [
        'id' => 'required|integer',
        'Name' => 'min:1|max:70|required_if:Status,',
        'Status' => 'integer|between:0,1|required_if:Name,'
    ];

    public static $CreateRules = [
        'Name' => 'required|min:1|max:70',
        'CompanyNum' => 'required|integer',
        'Status' => 'integer|between:0,1',
    ];

    /**
     * @param $companyNum
     * @return mixed
     */
    public static function getAllActiveByCompanyNum($companyNum)
    {
        return self::where('CompanyNum', $companyNum)
            ->where('Status', 0)
            ->get();
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public static function getAllByCompanyNum($companyNum)
    {
        return DB::table(self::getTable())
            ->where('CompanyNum', '=', $companyNum)
            ->orderBy('Status', 'ASC')
            ->get();
    }
}
