<?php
require_once 'ClientActivities.php';
require_once 'ClassStudioAct.php';

class FreezActivities
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $start_freez Date
     */
    private $start_freez;

    /**
     * @var $end_freez Date
     */
    private $end_freez;

    /**
     * @var $activities_count int
     */
    private $activities_count;

    /**
     * @var $memberships json
     */
    private $memberships;

    /**
     * @var $status int
     */
    private $status;

    /**
     * @var $reason string
     */
    private $reason;

    /**
     * @var $update_date DateTime
     */
    private $update_date;

    /**
     * @var $table string
     */
    private $table;

    public function __construct($freez_id = null)
    {
        $this->table = "freez_activities";
        if ($freez_id != null) {
            $this->getFreezById($freez_id);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    public function getFreezById($freez_id) {
        $freez = DB::table($this->table)->where('id', $freez_id)->first();
        if($freez) {
            foreach ($freez as $key => $value){
                $this->__set($key,$value);
            }
        }
    }

    public function getCurrentActiveFreezes($CompanyNum) {
        $freezes = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->where('status', '=', 1)
        ->where('start_freez', '<=', date('Y-m-d'))->where('end_freez', '>', date('Y-m-d'))->where('activities_count', '>', 0)->get();
        $freezArr = array();
        foreach($freezes as $freez) {
            $freezObj = new FreezActivities();
            foreach($freez as $key => $value) {
                $freezObj->__set($key,$value);
            }
            array_push($freezArr, $freezObj);
        }
        return $freezArr;
    }

    public function getMembershipText() {
        $membershipIds = json_decode($this->memberships);
        $memberships = DB::table('membership_type')->select('Type')->where('CompanyNum', '=', $this->CompanyNum)->whereIn('id', $membershipIds)->get();
        $memberArr = array();
        foreach($memberships as $membership) {
            array_push($memberArr, $membership->Type);
        }
        return $memberArr;
    }

    public function updateEndDate($end_freez) {
        $this->end_freez = $end_freez;
        $activitiesArr = $this->getFrozenActivities();
        $count = 0;
        foreach($activitiesArr as $activity) {
            $FreezLog = '';
            $FreezLog .= '{"data": [';
            if (!empty($activity->FreezLog)) {
                $Loops = json_decode($activity->FreezLog, true);
                foreach ($Loops['data'] as $key => $val) {
                    $StartFreezDB = $val['StartFreez'];
                    $EndFreezDB = $val['EndFreez'];
                    $FreezDaysDB = $val['FreezDays'];
                    $DatesDB = $val['Dates'];
                    $UserIdDB = $val['UserId'];
                    $ReasonDB = $val['Reason'];
                    $FreezLog .= '{"StartFreez": "' . $StartFreezDB . '", "EndFreez": "' . $EndFreezDB . '", "FreezDays": "' . $FreezDaysDB . '", "Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Reason":"' . $ReasonDB . '"},';

                }

            }
            $dateDiff = strtotime($this->end_freez) - strtotime($this->start_freez);
            $numberDays = $dateDiff/86400;

            $FreezLog .= '{"StartFreez": "' . $this->start_freez . '", "EndFreez": "' . $this->end_freez . '", "FreezDays": "' . $numberDays . '", "Dates": "' . date('Y-m-d H:i:s') . '", "UserId": "' . Auth::user()->id . '", "Reason":"' . $this->reason . '"}';
            $FreezLog .= ']}';

            DB::table('client_activities')
            ->where('id', '=', $activity->id)
            ->update(array('EndFreez' => $this->end_freez, 'FreezDays' => $numberDays, 'FreezLog' => $FreezLog));

            $getClasses = $this->getActivityClasses($activity->id, $activity->ClientId);
            foreach($getClasses as $class) {

                $UserName = Auth::user()->display_name;
                $CheckNewStatus = DB::table('class_status')->where('id', '=', '19')->first();
                $StatusCount = $CheckNewStatus->StatusCount;
                $StatusJson = '';
                $StatusJson .= '{"data": [';
                if (!empty($class->StatusJson)) {
                    $Loops = json_decode($class->StatusJson, true);
                    foreach ($Loops['data'] as $key => $val) {
                        $DatesDB = $val['Dates'];
                        $UserIdDB = $val['UserId'];
                        $StatusDB = $val['Status'];
                        $StatusTitleDB = $val['StatusTitle'];
                        $UserNameDB = $val['UserName'];
                        $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';
                    }
            
                }
                $StatusJson .= '{"Dates": "' . date('Y-m-d H:i:s') . '", "UserId": "' . Auth::user()->id . '", "Status": "19", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": "' . $UserName . '"}';
                $StatusJson .= ']}';
                if($activity->Department == '2' && in_array($class->Status, array(1,2,6,11,15))) {
                    $trueBalance = $activity->TrueBalanceValue + 1;
                    DB::table('client_activities')->where('id', $activity->id)->where('ClientId', '=', $activity->ClientId)
                    ->update(array('TrueBalanceValue' => $trueBalance));
                }
                (new ClassStudioAct($class->id))->update([
                    'Status' => '19',
                    'StatusJson' => $StatusJson,
                    'StatusCount' => $StatusCount,
                ]);

                //// עדכון שיעור ברשימת משתתפים    
                $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $class->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('StatusCount', '=', '0')->count();
                $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $class->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('StatusCount', '=', '1')->count();
                
                DB::table('classstudio_date')
                    ->where('CompanyNum', '=', $this->CompanyNum)
                    ->where('id', '=', $class->ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList));
            
                DB::table('classlog')->insertGetId(
                    array('CompanyNum' => $this->CompanyNum, 'ClassId' => $class->ClassId, 'ClientId' => $activity->ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => Auth::user()->id, 'numOfClients' => $ClientRegister));
            }
            $count++;
        }
        // update freez_activities
        if($count == 0) {
            $this->status = 0;
        } else {
            $this->status = 1;
        }
        DB::table($this->table)->where('id', $this->id)->update(array('end_freez' => $this->end_freez, 'status' => $this->status, 'activities_count' => $count));
        return $count;
    }

    public function freezOut() {
        $this->end_freez = date('Y-m-d');
        $activitiesArr = $this->getFrozenActivities();
        $count = 0;
        foreach($activitiesArr as $activity) {
        
            if ($this->start_freez > date('Y-m-d')) {
                $FreezDays = 0;
            }   
            else if ($this->end_freez >= date('Y-m-d') && $this->start_freez <= date('Y-m-d')){        
                
                $timeDiff = strtotime($this->end_freez) - strtotime($this->start_freez);
                $FreezDays = $timeDiff / 86400;  
            } else {
                $FreezDays = $activity->FreezDays;
            }
        
            $TrueDate = $activity->TrueDate;
            $ItemsMin = '+' . $FreezDays . ' days';
        
            if(!empty($TrueDate)) {
                $ClassTrueDate = date("Y-m-d", strtotime($ItemsMin, strtotime($TrueDate)));
            } else {
                $ClassTrueDate = null;
            }        
    
            $FreezLog = '';
            $FreezLog .= '{"data": [';

            if (!empty($activity->FreezEndLog)) {
                $Loops = json_decode($activity->FreezEndLog, true);
                foreach ($Loops['data'] as $key => $val) {
                    $FreezDaysDB = $val['FreezDays'];
                    $DatesDB = $val['Dates'];
                    $UserIdDB = $val['UserId'];
                    $FreezLog .= '{"FreezDays": "' . $FreezDaysDB . '", "Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '"},';
                }
        
            }
            $FreezLog .= '{"FreezDays": "' . $FreezDays . '", "Dates": "' . date('Y-m-d H:i:s') . '", "UserId": "' . Auth::user()->id . '"}';
            $FreezLog .= ']}';

            DB::table('client_activities')        
                ->where('ClientId', $activity->ClientId)
                ->where('CompanyNum', $this->CompanyNum)
                ->where('id', $activity->id)
                ->update(array('Freez' => '2', 'TrueDate' => $ClassTrueDate, 'StudioVaildDate' => $ClassTrueDate, 'FreezEndLog' => $FreezLog));

            DB::table('client')
                ->where('CompanyNum', $this->CompanyNum)
                ->where('id', $activity->ClientId)
                ->update(array('FreezStatus' => '0'));

            $getActs = $this->getFreezClassActs($activity->id, $activity->ClientId);
            foreach($getActs as $act) {
        
                $ClassInfo = DB::table('classstudio_date')->where('id', '=', $act->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->first();        
                $ClassCounts = DB::table('classstudio_act')->where('ClassId', '=', $act->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('StatusCount', '=', '0')->count();
                $MaxClient = 0;

                $RegularDates = DB::table('classstudio_dateregular')->where('id', '=', $act->RegularClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('ClientId', '=', $activity->ClientId)->first();

                if (!empty($RegularDates->StatusType)) {
                    $CheckNewStatus = DB::table('class_status')->where('id', '=', $RegularDates->StatusType)->first();
                } else {
                    $CheckNewStatus = DB::table('class_status')->where('id', '=', '12')->first();
                }
    
                if ($ClassCounts >= $ClassInfo->MaxClient) {
                    $CheckNewStatus = DB::table('class_status')->where('id', '=', '9')->first();
                }

                $Status = $CheckNewStatus->id;
                $StatusCount = $CheckNewStatus->StatusCount;

                $StatusJson = '';
                $StatusJson .= '{"data": [';
                if (!empty($act->StatusJson)) {
                    $Loops = json_decode($act->StatusJson, true);
        
                    foreach ($Loops['data'] as $key => $val) {
                        $DatesDB = $val['Dates'];
                        $UserIdDB = $val['UserId'];
                        $StatusDB = $val['Status'];
                        $StatusTitleDB = $val['StatusTitle'];
                        $UserNameDB = $val['UserName'];
                        $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';
                    }
        
                }
                $StatusJson .= '{"Dates": "' . date('Y-m-d H:i:s') . '", "UserId": "' . Auth::user()->id . '", "Status": "19", "StatusTitle": "' . $act->Title . '", "UserName": "' . Auth::user()->display_name . '"}';
                $StatusJson .= ']}';

                (new ClassStudioAct($act->id))->update([
                    'Status' => $Status,
                    'StatusJson' => $StatusJson,
                    'StatusCount' => $StatusCount,
                ]);
        
                $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $act->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('StatusCount', '=', '0')->count();
                $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $act->ClassId)->where('CompanyNum', '=', $this->CompanyNum)->where('StatusCount', '=', '1')->count();

                DB::table('classstudio_date')
                    ->where('CompanyNum', '=', $this->CompanyNum)
                    ->where('id', '=', $act->ClassId)
                    ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList));
        
                DB::table('classlog')->insertGetId(
                    array('CompanyNum' => $this->CompanyNum, 'ClassId' => $act->ClassId, 'ClientId' => $activity->ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => Auth::user()->id, 'numOfClients' => $ClientRegister));
            }
            $count++;
        }

        DB::table($this->table)->where('id', $this->id)->update(array('end_freez' => date('Y-m-d'), 'status' => 0, 'activities_count' => $count));
        return $count;
    }


    public function getFrozenActivities() {
        $memberArr = json_decode($this->memberships);
        $activities = DB::table('client_activities')->where('CompanyNum', '=', $this->CompanyNum)->where('Freez', '=', 1)
        ->where('StartFreez', '<=', date('Y-m-d'))->where('EndFreez', '>', date('Y-m-d'))->where('Status', '=', 0)->whereIn('MemberShip', $memberArr)->get();
        $activitiesArr = array();
        foreach($activities as $activity) {
            $activityObj = new ClientActivities();
            foreach($activity as $key => $value) {
                $activityObj->__set($key,$value);
            }
            array_push($activitiesArr, $activityObj);
        }
        return $activitiesArr;
    }

    public function getActivityClasses($activityId, $clientId) {
        $getClientClasses = DB::table('classstudio_act')->where('CompanyNum', '=', $this->CompanyNum)->where('ClientId', '=', $clientId)->where('ClientActivitiesId', $activityId)->whereIn('Status', array(1,2,6,9,10,11,12,15,16,17,18,21,23))->whereBetween('ClassDate', array($this->start_freez, date('Y-m-d', strtotime('-1 day', strtotime($this->end_freez)))))->get();
        $classesActArr = array();
        foreach($getClientClasses as $class) {
            $classActObj = new ClassStudioAct();
            foreach($class as $key => $value) {
                $classActObj->__set($key,$value);
            }
            array_push($classesActArr, $classActObj);
        }
        return $classesActArr;
    }

    public function getFreezClassActs($activityId, $clientId) {
        $getClientClasses = DB::table('classstudio_act')->where('CompanyNum', '=', $this->CompanyNum)->where('ClientId', '=', $clientId)->where('ClientActivitiesId', $activityId)->where('Status', '=', 19)->where('ClassDate', '>=', date('Y-m-d'))->where('RegularClass', '=', '1')->get();
        $classesActArr = array();
        foreach($getClientClasses as $class) {
            $classActObj = new ClassStudioAct();
            foreach($class as $key => $value) {
                $classActObj->__set($key,$value);
            }
            array_push($classesActArr, $classActObj);
        }
        return $classesActArr;
    }

    
}
