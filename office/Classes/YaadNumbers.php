<?php


class YaadNumbers
{
    private static $table = "boostapp.yaad_sarig_order_numbers";

    public static function get($id){
        return DB::table(self::$table)->where("id",$id)->first();
    }
    public static function insert($data){
        return DB::table(self::$table)->insertGetId($data);
    }

    public static function update($id,$data){
        return DB::table(self::$table)->where("id",$id)->update($data);
    }

}
