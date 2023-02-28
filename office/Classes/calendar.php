<?php

require_once "Utils.php";

class Calendar extends Utils
{
    protected $id;
    Protected $start_date;
    Protected $end_date;
    Protected $text;
    Protected $textColor;
    Protected $color;
    Protected $AllDay;
    Protected $Type;
    Protected $TypeTitle;
    Protected $Floor;
    Protected $Level;
    Protected $AgentId;
    Protected $Content;
    Protected $SendMail;
    Protected $ClientId;
    Protected $User;
    Protected $Dates;
    Protected $CompanyNum;
    Protected $Status;
    Protected $GuideName;
    Protected $FloorName;
    Protected $StartDate;
    Protected $StartTime;
    Protected $EndTime;
    Protected $PipeLineId;
    Protected $ItemId;
    Protected $Title;
    Protected $ReminderStatus;
    Protected $ReminderDate;
    Protected $ReminderTime;
    Protected $GroupPermission;
    Protected $Automation;

    private $table;
    public function __construct()
    {
        $this->table = "calendar";
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

    /**
     * @param $companyNum
     * @param $id
     * @return mixed
     */
    public function getTaskById($companyNum, $id)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('id', '=', $id)
            ->first();
    }

    public function getMissionByid($companyNum,$MissionId){
        $mission =  $Missions = DB::table($this->table)
            ->where('CompanyNum','=',$companyNum)
            ->where('id','=',$MissionId)->get();
        return $mission[0];
    }
    public function GetOpenMissionCurrentLateDay($companyNum,$StartDate = null,$EndDate = null){
        $Missions = new stdClass();
        $date = !empty($StartDate) ? $StartDate: date('Y-m-d H:i:s', strtotime("today"));
//        $EndOfDay = !empty($EndDate) ? $EndDate: date('Y-m-d H:i:s', strtotime("tomorrow") -1);
        $LsetSixMonth = date('Y-m-d H:i:s', strtotime("-180 days"));

        $MissionsCurrentDay = DB::table($this->table)
            ->where('CompanyNum','=',$companyNum)
            ->where('StartDate','=', date('Y-m-d'))
            ->where('Status','=',0)
            ->orderBy('StartTime', 'ASC')->get();

        $MissionsLate = DB::table($this->table)
            ->where('CompanyNum','=',$companyNum)
            ->where('start_date','<=',$date)
            ->where('start_date','>=',$LsetSixMonth)
            ->where('Status','=',0)
            ->orderBy('start_date', 'ASC')->get();

        foreach ($MissionsCurrentDay as $key => $mission){
            $userCan = false;
            if($mission->AgentId == Auth::user()->id){
                $userCan = true;
            }
            if($mission->GroupPermission != "" && $userCan == false){
//                $roleId = DB::table("users")->select("role_id")->where("id","=",Auth::user()->id)->first();
                $roleId = Auth::user()->role_id;
//                $role = DB::table('roles')->where('id',"=" ,$roleId)->first();
                $role = Auth::user()->_role;
                if($role->permissions == "*"){
                    $userCan = true;
                }
                $grpPer = explode(",",$mission->GroupPermission);

                if(in_array($roleId,$grpPer)){
                    $userCan = true;
                }
            }
            else{
                $userCan = true;
            }
            if($userCan) {
                $Client = DB::table("client")
                    ->where('id', '=', $mission->ClientId)->first();
                $mission->Client = $Client;
            }
            else{
                unset($MissionsCurrentDay[$key]);
            }
        }
        foreach ($MissionsLate as $key => $mission){
            $userCan = false;
            if($mission->AgentId == Auth::user()->id){
                $userCan = true;
            }
            if($mission->GroupPermission != "" && $userCan == false){
//                $roleId = DB::table("users")->select("role_id")->where("id","=",Auth::user()->id)->first();
                $roleId = Auth::user()->role_id;
//                $role = DB::table('roles')->where('id',"=" ,$roleId)->first();
                $role = Auth::user()->_role;
                if($role->permissions == "*"){
                    $userCan = true;
                }
                $grpPer = explode(",",$mission->GroupPermission);

                if(in_array($roleId,$grpPer)){
                    $userCan = true;
                }
            } else{
                $userCan = true;
            }
            if($userCan) {
                $Client = DB::table("client")
                    ->where("id", '=', $mission->ClientId)->first();
                $mission->Client = $Client;
            } else{
                unset($MissionsLate[$key]);
            }
        }
        $Missions->MissionsCurrentDay = $MissionsCurrentDay;
        $Missions->MissionsLate = $MissionsLate;
        return $Missions;
    }

    public function UpdareMissionStatus($companyNum,$MissionId,$status){
        $row = DB::table($this->table)
            ->where('CompanyNum','=',$companyNum)
            ->where('id','=',$MissionId)
            ->update(['Status' => $status]);
        return $row;
    }
    public function UpdateMission($companyNum,$missiom){
        $MissiomArray = (array) $missiom;
        $row = DB::table($this->table)
            ->where('CompanyNum','=',$companyNum)
            ->where('id','=',$missiom->id)
            ->update($MissiomArray);
        return $row;
    }
    public function getCalendarsByIds($ids, $template = 0){
        if($template == 1){
            $calendars =  DB::table($this->table)->select("id")->whereIn("id",$ids)->get();
            $arr = array();
            foreach ($calendars as $calendar){
                $arr[] = $calendar->id;
            }
            return $arr;
        }
        return DB::table($this->table)->whereIn("id",$ids)->get();
    }

    public function taskInfo ($RoleId, $UserId, int $CompanyNum, $dateFrom,$dateTo, int $SeeAll){

        $OpenTables = $SeeAll=='1' ?
            DB::table('calendar')->where('CompanyNum', '=', $CompanyNum)->whereBetween('StartDate', array($dateFrom, $dateTo))->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get() :
            DB::select('select * from `calendar` where (CompanyNum = "'.$CompanyNum.'" AND `StartDate` BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" AND FIND_IN_SET("'.$RoleId.'",GroupPermission) > 0 )
            OR (CompanyNum = "'.$CompanyNum.'" AND AgentId = "'.$UserId.'" AND `StartDate` BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'") Order By `StartDate` ASC, `StartTime` ASC ');

        $resArr = array("data" => array());
        foreach($OpenTables as $Task){
            $reportArray = array();
            $UserNameLog = $Task->AgentId == 0 ? lang ('customer_card_gender') :
                (DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', $Task->AgentId)->first())->display_name;

            $ClientMobile="";
            if ($Task->ClientId == '0'){
                //at final table doesn't show exactly 'private_task_taskpost' key, it works if change it to another.
                $ClientUserNameLog = lang('private_task_taskpost');
            }
            else {

                $ClientUserNameLogs =  DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', $Task->ClientId)->first();
                $ClientUserNameLog =  $ClientUserNameLogs->CompanyName;
                $ClientMobile = $ClientUserNameLogs->ContactMobile;
            }

            $CalTypes = DB::table('caltype')->where('CompanyNum', '=', $CompanyNum)->where('id', @$Task->Type)->first();
            $StatusText = ($Task->Status=='0' ? lang('open_task') : ($Task->Status=='1' ? lang('completed_task') : lang('canceled_task')));

            if ($Task->GroupPermission!=''){
                $z = '1';
                $myArray = explode(',', $Task->GroupPermission);
                $SoftNames = '';
                $SoftInfos = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->whereIn('id', $myArray)->get();
                $SoftCount = count($SoftInfos);

                foreach ($SoftInfos as $SoftInfo){
                    $SoftNames .= $SoftInfo->name;
                    if($SoftCount!=$z) $SoftNames .= ', ';
                    ++$z;
                }
                $TaskGroup = $SoftNames;
            }
            else {
                $TaskGroup = '';
            }
            $reportArray[0] = $Task->ClientId != 0 ? '<a href="/office/ClientProfile.php?u='.$Task->ClientId.'">'.$ClientUserNameLog.'</a>' : $ClientUserNameLog;
            $reportArray[1] = $ClientMobile;
            $reportArray[2] = '<a class="task-popup-btn" style="color: #00c736; text-decoration: none; background-color: transparent; cursor:pointer;}" data-id=' . $Task->id . ' > ' . $Task->Title . ' </a>';
            $reportArray[3] = '<span style="display: none" > '.(new DateTime($Task->StartDate))->format('d/m/Y') .'</span> '.(new DateTime($Task->StartDate))->format('d/m/Y') . " ".(new DateTime($Task->StartTime))->format('H:i').'';
            $reportArray[4] = '<span style="color: '.$CalTypes->Color.'">'. htmlspecialchars($CalTypes->Type) .'</span>';
            $reportArray[5] = $UserNameLog;
            $reportArray[6] = $TaskGroup;
            $reportArray[7] = $StatusText;
            array_push($resArr["data"], $reportArray);
        }
        return $resArr;
    }
}


