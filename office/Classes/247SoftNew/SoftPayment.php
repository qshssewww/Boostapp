<?php


class SoftPayment
{
    protected static $table = "247softnew.payment";

    public static function getRow($value,$key = null){
        if($key != null) {
            return DB::table(self::$table)->where($key, $value)->first();
        }
        return DB::table(self::$table)->where("id", $value)->first();
    }
    public static function update($id,$data){
        return DB::table(self::$table)->where("id",$id)->update($data);
    }
    public static function insert($id,$data){
        return DB::table(self::$table)->insertGetId($data);
    }

    public static function getDeclinedPayment($clientId){
        return DB::table(self::$table)
            ->where("clientId", $clientId)
            ->where("Status", "=", 2)
            ->orderBy("id", "Desc")
            ->first();
    }
}
