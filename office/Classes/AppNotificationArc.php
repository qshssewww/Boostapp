<?php



class AppNotificationArc
{
    private static $table = "boostapp.appnotification_arc";

    public static function insert($data){
        DB::table(self::$table)->insertGetId($data);
    }

    public static function insertBulk($dataArr){
        DB::table(self::$table)->insert($dataArr);
    }
}
