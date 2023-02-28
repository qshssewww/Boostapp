<?php

require_once "Utils.php";
require_once "Users.php";
require_once "UserSchedule.php";

class UserScheduleUpdate extends Utils
{
    protected $id;

    protected $CompanyNum;

    protected $userScheduleId;

    protected $userId;

    protected $scheduleDate;

    protected $startHour;

    protected $endHour;

    protected $date;

    protected $status;

    private $table;

    public function __construct()
    {
        $this->table = "userScheduleUpdate";
    }

    public function getChangedUserScheduleByUserId($userId){
        $schedule = DB::table($this->table)->where("userId","=",$userId)->where("status","!=",0)->get();
        return $this->createArrayFromObjArr($schedule);
    }
    public function getScheduleById($scheduleId,$date = null){
        if($date == null) {
            return DB::table($this->table)->where("userScheduleId", "=", $scheduleId)->where("status", "!=", 0)->orderBy("id", "Desc")->get();
        }
        else{
            $date  = date("Y-m-d H:i", strtotime($date));
            return DB::table($this->table)->where("userScheduleId", "=", $scheduleId)->where("scheduleDate", "=", $date)->where("status", "!=", 0)->orderBy("id", "Desc")->first();
        }
    }
    public function insertChangedUserSchedule($data,$scheduleId){
        $schedule = $this->getScheduleById($scheduleId,$data["scheduleDate"]);
        $id = DB::table($this->table)->insertGetId($data);
        if(!empty($schedule)) {
            $oldDate = date("d-m-Y", strtotime($schedule->scheduleDate));
            $newDate = date("d-m-Y", strtotime($data["scheduleDate"]));
            if (($id != null || $id != 0) && $oldDate == $newDate) {
                $this->deleteSchedule($schedule->id);
            }
        }
        return $id;
    }
    public function deleteSchedule($id){
        DB::table($this->table)->where("id","=",$id)->update(array("status" => 0));
    }

}
