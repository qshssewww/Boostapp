<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $Title
 * @property $SmsContent
 * @property $EmailTitle
 * @property $EmailContent
 * @property $Status
 *
 * Class TextSaved
 */


class TextSaved extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.textsaved';

    /**
     * @param $CompanyNum
     * @return int
     */
    public static function getCompanyTemplates($CompanyNum) :int {
        return self::where('CompanyNum', $CompanyNum)->count();
    }
}