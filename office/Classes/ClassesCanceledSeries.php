<?php

/**
 * @property $id
 * @property $companyNum
 * @property $groupNumber
 * @property $createdAt
 */

class ClassesCanceledSeries extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.classes_canceled_series';

    public static function insertCanceledSeries($companyNum, $groupNumber) {
        $isCanceled = self::where('companyNum', $companyNum)
            ->where('groupNumber', $groupNumber)
            ->first();

        if (empty($isCanceled)) {
            self::insert([
                'companyNum' => $companyNum,
                'groupNumber' => $groupNumber,
            ]);
        }
    }
}
