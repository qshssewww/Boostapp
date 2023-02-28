<?php

use Hazzard\Database\Model;

class Automation extends Model
{
    protected $table = 'automation';

    /**
     * @param $companyNum
     * @param $category
     * @return mixed
     */
    public static function getAutomationAmount($companyNum, $category)
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where('Category', '=', $category)
            ->where('Type', '=', 1)
            ->where('Status', '=', 0)
            ->count();
    }

    /**
     * @param $companyNum
     * @param $category
     * @return mixed
     */
    public static function getAutomation($companyNum, $category = 2)
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where('Category', '=', $category)
            ->where('Type', '=', 1)
            ->where('Status', '=', 0)
            ->first();
    }

}
