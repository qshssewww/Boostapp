<?php

/**
 * @property $id
 * @property $Title
 * @property $Act
 * @property $Status
 * @property $Color
 * @property $PopupStatus
 * @property $StatusCount
 *
 * Class ClassStatus
 */
class ClassStatus extends \Hazzard\Database\Model {

    protected $table = "boostapp.class_status";

    public static function GetStatusById($id)
    {
        return self::where('id', '=', $id)
            ->first();
    }

    public function getAllStatuses(){
        return DB::table($this->table)
                ->whereIn('id', [1,2,8,12,15,16,17,22,23])
                ->get();
    }

    /**
     * @return self[]|null
     */
    public static function getAllStatusesInSystem(): ?array{
        return self::get();
    }

}