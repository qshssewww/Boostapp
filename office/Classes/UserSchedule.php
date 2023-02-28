<?php

require_once "Utils.php";
require_once "Users.php";

class UserSchedule extends Utils
{
    protected $id;

    protected $CompanyNum;

    protected $userId;

    protected $startDate;

    protected $startHour;

    protected $endHour;

    protected $repeat;

    protected $endRepeat;

    protected $endDate;

    protected $status;

    private $table;

    public function __construct()
    {
        $this->table = "boostapp.userSchedule";
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

    public function getUserScheduleByTimeId($id){
        $schedule = DB::table($this->table)->where("id","=",$id)->first();
        return $schedule;
    }
    public function getUserSchedule($userId){
        $schedule = DB::table($this->table)->where("userId","=",$userId)->where("status","=", 1)->get();
        return $this->createArrayFromObjArr($schedule);
    }
    public function getUsersScheduleByUsersId($ids){
        $schedule = DB::table($this->table)->whereIn("userId",$ids)->order("userId","ASC")->get();
        return $this->createArrayFromObjArr($schedule);
    }
    public function insertUserSchedule($data){
        $id = DB::table($this->table)->insertGetId($data);
        return $id;
    }
    public function updateUserSchedule($data){
        return DB::table($this->table)->where("id","=",$data["id"])->update($data);
    }
    public function deleteUserSchedule($id){
        return DB::table($this->table)->where("id","=",$id)->update(array("status" => 0));
    }
    public function userFullSchedule($schedule, $changedSchedule){
        foreach ($schedule as $key => $sch){
            $newSchedule = array();
            foreach ($changedSchedule as $change){
                if($change["userScheduleId"] == $sch["id"]){
                    array_push($newSchedule,$change);
                }
            }
            if (!empty($newSchedule)){
                $schedule[$key]["newSchedule"] = $newSchedule;
            }
        }
        return $schedule;
    }

    public function getUserWeeklySchedule($schedule, $changedSchedule,$startDate){
        $fullSchedule = $this->userFullSchedule($schedule,$changedSchedule);
        $dayNum = date('w', strtotime($startDate));
        if($dayNum != 0){
            $startDate = date('Y-m-d', strtotime('-'.$dayNum.' days',strtotime($startDate)));
        }
        $weekly = array();
        $end = strtotime($startDate);
        $end = date("Y-m-d", strtotime("+6 day", $end));
        foreach ($fullSchedule as $sch){
            $sched = array();
            $deleted = false;
            if(isset($sch["newSchedule"])){
                foreach ($sch["newSchedule"] as $newSche){
                    $scheduleDate = date("Y-m-d", strtotime($newSche["scheduleDate"]));
                    if($scheduleDate >= $startDate && $scheduleDate <= $end){
                        if($newSche["status"] == 2){
                            $deleted = true;
                            continue;
                        }
                        else{
                            $day = date('w', strtotime($newSche["scheduleDate"]));
                            $sched = array(
                                "day" => date('D', strtotime($newSche["scheduleDate"])),
                                "date" => date('Y-m-d', strtotime('+'.$day.' days',strtotime($startDate))),
                                "startHour" => $newSche["startHour"],
                                "endHour" => $newSche["endHour"],
                                "scheduleId" => $sch["id"],
                                "changedScheduleId" => $newSche["id"]
                            );
                        }
                    }
                }
                if(empty($sched) && !$deleted){
                    $sched = $this->arrangeSchedule($sch,$startDate,$end);
                }
            }

            else{
                $sched = $this->arrangeSchedule($sch,$startDate,$end);
            }
            if(!empty($sched)) {
                array_push($weekly, $sched);
            }
        }
        return $weekly;
    }

    public function arrangeSchedule($sch,$startDate,$end){
        $schedule = array();
        $valid = false;
        $lastWeek = date("Y-m-d", strtotime("-6 days", strtotime($sch["startDate"])));
        if($sch["repeat"] == 0){
            if($sch["startDate"] >= $startDate && $sch["startDate"] <= $end){
                $valid = true;
            }
        }
        elseif($sch["repeat"] == 1 && $sch["endRepeat"] == 1) {
            $lastWeek = date("Y-m-d", strtotime("-6 days", strtotime($sch["startDate"])));
            if ($lastWeek <= $startDate && $sch["endDate"] >= $end) {
                $valid = true;
            }
        }
        elseif($sch["repeat"] == 1 && $sch["endRepeat"] == 0) {
            if ($lastWeek <= $startDate) {
                $valid = true;
            }
        }

        if($valid == true){
            $day = date('w', strtotime($sch["startDate"]));
            $schedule = array(
                "day" => date('D', strtotime($sch["startDate"])),
                "date" => date('Y-m-d', strtotime('+'.$day.' days',strtotime($startDate))),
                "startHour" => $sch["startHour"],
                "endHour" => $sch["endHour"],
                "scheduleId" => $sch["id"]
            );
        }
        return $schedule;
    }
}
