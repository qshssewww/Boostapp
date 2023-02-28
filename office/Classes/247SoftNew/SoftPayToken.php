<?php


class SoftPayToken
{
    private static $table = "247softnew.paytoken";
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
    public static function getRowByClientId($client,$payment = null){
        if($payment != null) {
            return DB::table(self::$table)->where("ClientId", $client)->where("NumPayment", $payment)->first();
        }
        return DB::table(self::$table)->where("ClientId", $client)->first();
    }
    public static function getPayTokenByClientId($clientId) {
        return DB::table(self::$table)
            ->where("ClientId", $clientId)
            ->where("ItemId", 2)
            ->first();
    }
}
