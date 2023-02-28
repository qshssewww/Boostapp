<?php

/**
 * @property $id
 * @property $hex
 * @property $calendar
 * @property $date
 */
class Color extends \Hazzard\Database\Model
{
    protected $table = "colors";

    public function getCalendarColors(): array
    {
        $colors = DB::table($this->table)->where("calendar", "=", 1)->get();
        $colorsArr = array();
        foreach ($colors as $color){
            $mType = new ItemColor();
            foreach ($color as $key => $value){
                $mType->__set($key,$value);
            }
            array_push($colorsArr,$mType);
        }
        return $colorsArr;
    }
}