<?php
require_once "Utils.php";
require_once "calendar.php";
require_once "Users.php";

class userScheduleSettings extends Utils
{
    private $id;
    private $userId;
    private $link;
    private $status;
    private $date;

    private  $table;

    public function __construct()
    {
        $this->table = "userScheduleSettings";
    }

    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }


    public function InsertOrUpdate($data){
        if (DB::table($this->table)->where("userId","=",$data["userId"])->exists()) {
            $id =  DB::table($this->table)
                ->where("userId","=",$data["userId"])
                ->update($data);
        }
        else{
            $id =   DB::table($this->table)->insertGetId($data);
        }
        return $id;
    }
}


