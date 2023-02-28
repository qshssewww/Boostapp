<?php


class SoftItem
{
    private static $table = "247softnew.items";
    public Static Function insert($data){
        return DB::table(self::$table)->insertGetId($data);
    }
    public static function getRow($value,$key = null){
        if($key != null) {
            return DB::table(self::$table)->where($key, $value)->first();
        }
        return DB::table(self::$table)->where("id", $value)->first();
    }
    public static function update($id,$data){
        return DB::table(self::$table)->where("id",$id)->update($data);
    }
}
