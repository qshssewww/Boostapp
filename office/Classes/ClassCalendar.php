<?php

require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/RepetitionSettings.php";
require_once __DIR__ . "/CancelationSettings.php";
require_once __DIR__ . "/ZoomClasses.php";
require_once __DIR__ . "/ClassSettings.php";
require_once __DIR__ . "/ClientActivities.php";
require_once __DIR__ . "/ClassStudioAct.php";
require_once __DIR__ . "/ClassStudioDate.php";
require_once __DIR__ . "/Client.php";
require_once __DIR__ . "/Company.php";
require_once __DIR__ . "/Users.php";
require_once __DIR__ . "/Section.php";
require_once __DIR__ . "/Brand.php";
require_once __DIR__ . "/ClassesType.php";
require_once __DIR__ . "/MeetingStaffRuleAvailability.php";
require_once __DIR__ . "/CompanyProductSettings.php";
require_once __DIR__ . "/AppNotification.php";
require_once __DIR__ . "/Numbers.php";
require_once __DIR__ . "/../services/TagsService.php";
require_once __DIR__ . "/../services/GoogleCalendarService.php";
require_once __DIR__ . "/../services/meetings/MeetingService.php";
require_once __DIR__ . "/MeetingGeneralSettings.php";
require_once __DIR__ . '/../../app/enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . "/TaskStatus.php";
require_once __DIR__ . "/Settings.php";

class ClassCalendar extends Utils
{
    public const TYPE_MEETING = '1';    // string type check in front
    public const TYPE_TASK = 2;

    public const STATUS_OPEN = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_CANCELED = 2;

    protected $id;
    protected $CompanyNum;
    protected $Brands;
    protected $start_date;
    protected $end_date;
    protected $text;
//    protected $textColor;
    protected $color;
    protected $Floor;
    protected $ClassNameType;
    protected $ShowApp;
    protected $ClassName;
    protected $GuideId;
    protected $GuideName;
    protected $ExtraGuideId;
    protected $ExtraGuideName;
    protected $ClassLevel;
    protected $MaxClient;
    protected $MinClass;
    protected $MinClassNum;
    protected $ClassTimeCheck;
    protected $ClassTimeTypeCheck;
    protected $StartDate;
    protected $DayNum;
    protected $Day;
    protected $StartTime;
    protected $EndDate;
    protected $EndTime;
    protected $ClassType;
    protected $ClassTypeStatus;
    protected $ClassCount;
    protected $ClassDevice;
    protected $ClassMemberType;
    protected $ClassWating;
    protected $ShowClientNum;
    protected $ShowClientName;
    protected $SendReminder;
    protected $TypeReminder;
    protected $TimeReminder;
    protected $CancelLaw;
    protected $CancelDay;
    protected $CancelDayMinus;
    protected $CancelDayName;
    protected $CancelTillTime;
    protected $Status;
    protected $displayCancel;
    protected $UserId;
    protected $GroupNumber;
    protected $Dates;
    protected $ClientRegister;
    protected $WatingList;
//    protected $Occupancy;
//    protected $OccupancyWating;
    protected $MaxWatingList;
    protected $NumMaxWatingList;
    protected $Change;
    protected $Remarks;
    protected $RemarksStatus;
    protected $ClassLimitTypes;
    protected $LimitLevel;
    protected $GenderLimit;
    protected $FreeClass;
    protected $StopCancel;
    protected $StopCancelTime;
    protected $StopCancelType;
    protected $WatingListOrederShow;
    protected $ClassRepeat;
    protected $ClassRepeatType;
    protected $Auto;
    protected $CheckInStatus;
    protected $Private;
    protected $PrivateId;
    protected $ClientRegisterRegular;
    protected $ClientRegisterRegularWating;
    protected $MinClassStatus;
    protected $OpenOrder;
    protected $OpenOrderTime;
    protected $OpenOrderType;
    protected $CloseOrder;
    protected $CloseOrderTime;
    protected $CloseOrderType;
    protected $liveClassLink;
    protected $registerLimit;
    protected $onlineSendType;
    protected $is_zoom_class;
    protected $image;
//    protected $content;
//    protected $frequencyId;
//    protected $cancelationId;
    protected $purchaseOptions;
    protected $purchaseAmount;
    protected $purchaseLocation;
//    protected $contentShow;
//    protected $onlineReminderType;
//    protected $onlineReminderNum;
    protected $ageLimitNum1;
    protected $ageLimitNum2;
    protected $ageLimitType;
//    protected $ReminderUnits;
//    protected $ReminderNum;
    protected $meetingTemplateId;
    protected $meetingStatus;
    protected $SaveUntilTime;
    protected $PreparationTimeMinutes;

//    /**
//     * @var $repetition RepetitionSettings
//     */
//    protected $repetition;
//
//    /**
//     * @var $cancelation CancelationSettings
//     */
//    protected $cancelation;

    /**
     * @var $table string
     */
    protected $table;

    /**
     * @param $id
     */
    public function __construct($id = null)
    {
        $this->table = "boostapp.classstudio_date";
        if ($id != null) {
            $this->setClassCalendarObjectById($id);
        }
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * @param $CompanyNum
     * @param $ClassId
     * @return mixed
     */
    public function GetClassById($CompanyNum, $ClassId)
    {
        $Class = DB::table($this->table)
            ->where('id', '=', $ClassId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $Class;
    }

    /**
     * @param $ClassNameType
     * @param $CompanyNum
     * @return array
     */
    public function getClassCalendar($ClassNameType, $CompanyNum)
    {
        $classes = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->where("ClassNameType", "=", $ClassNameType)->get();
        $classArr = array();
        foreach ($classes as $class) {
            $classObj = new ClassCalendar();
            foreach ($class as $key => $value) {
                $classObj->__set($key, $value);
            }
            array_push($classArr, $classObj);
        }
        return $classArr;
    }

    /**
     * @param $classId
     * @return void
     */
    public function setClassCalendarObjectById($classId)
    {
        $class = DB::table($this->table)->where("id", "=", $classId)->first();
        if ($class != null) {
            foreach ($class as $key => $value) {
                $this->__set($key, $value);
            }
        }
//        $this->setCancelation();
//        $this->setRepetition();
        if ($this->__get('ClassLimitTypes') == "1") {
            $this->membershipIds = DB::table('boostapp.classstudio_date_roles')->where("ClassId", "=", $classId)->first();
        }
        $trainees = DB::table('boostapp.classstudio_act')->where("ClassId", "=", $classId)->where('CompanyNum', $this->__get("CompanyNum"))->whereIn('StatusCount', array(0, 1))->get();
        foreach ($trainees as $trainee) {
            if ($trainee->MemberShip == "0") {
                $classActivity = DB::table('boostapp.client_activities')->where("id", "=", $trainee->ClientActivitiesId)->where('ClientId', $trainee->ClientId)->first();
                $trainee->ItemPrice = $classActivity ? $classActivity->ItemPrice : null;
            }
        }
        $this->trainees = $trainees;
    }

    /**
     * @param $date
     * @param null|string $measurement
     * @param null|int $timeDiff
     * @param boolean $add
     * @param int|null $companyNum
     * @return array
     */
    public function getAllClassesOnSpecificDate($date, $measurement = null, $timeDiff = null, $add = true, $companyNum = null)
    {
        if ($measurement == NULL || ($measurement != "days" && $measurement != "hours" && $measurement != "minutes")) {
            $measurement = "hours";
        }
        $timeRange = "+1 " . $measurement;
        if ($timeDiff != null) {
            if ($add) {
                $timeRange = "+" . $timeDiff . " " . $measurement;
            } else {
                $timeRange = "-" . $timeDiff . " " . $measurement;
            }
        }
        $dateNow = date('Y-m-d');
        $classes = array();
        if ($companyNum == null) {
            $todayClasses = DB::table($this->table)->where("StartDate", "=", $dateNow)->where('Status', '=', '0')->get();
//            $classes = DB::table($this->table)->whereBetween("start_date", array(date('Y-m-d H:i:s', strtotime($date)), date('Y-m-d H:i:s', strtotime($timeRange, strtotime($date)))))->get();
        } else {
            $todayClasses = DB::table($this->table)->where("StartDate", "=", $dateNow)->where("CompanyNum", "=", $companyNum)->where('Status', '=', '0')->get();
//            $classes = DB::table($this->table)->whereBetween("start_date", array(date('Y-m-d H:i:s', strtotime($date)), date('Y-m-d H:i:s', strtotime($timeRange, strtotime($date)))))
//                ->where("CompanyNum","=", $companyNum)->get();
        }
        $range = date('Y-m-d H:i:s', strtotime($timeRange, strtotime($date)));
        foreach ($todayClasses as $classNow) {
            if ($classNow->start_date >= date('Y-m-d H:i:s', strtotime($date)) && $classNow->start_date <= $range) {
                array_push($classes, $classNow);
            }
        }
        $classArr = array();
        foreach ($classes as $class) {
            $classObj = new ClassCalendar();
            foreach ($class as $key => $value) {
                $classObj->__set($key, $value);
            }
            array_push($classArr, $classObj);
        }
        return $classArr;
    }

    /**
     * @param $date
     * @param $CompanyNum
     * @return array|null
     */
    public function getClassesByDate($date, $CompanyNum)
    {
        
        $date = date("Y-m-d", strtotime($date));
        $classes = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->where("StartDate", "=", $date)->where('status', '=', 0)->whereNull('meetingTemplateId')->get();
        if ($classes) {
            $classArr = array();
            foreach ($classes as $class) {
                $classObj = new ClassCalendar();
                foreach ($class as $key => $value) {
                    $classObj->__set($key, $value);
                }
                array_push($classArr, $classObj);
            }
            return $classArr;
        }
        return null;
    }

    /**
     * @param $groupNumber
     * @param $startDate
     * @param $companyNum
     * @param $quantity
     * @return mixed
     */
    public function getClassesByGroupNumber($groupNumber, $startDate, $companyNum, $quantity = null)
    {
        $query = DB::table($this->table)
            ->where("GroupNumber", $groupNumber)
            ->where("CompanyNum", $companyNum)
            ->where("Status", 0)
            ->where("start_date", ">=", $startDate);

        if ($quantity) {
            $query = $query->limit($quantity);
        }
        return $query->get();
    }

    /**
     * @param $day
     * @param $CompanyNum
     * @param $duration
     * @return array|null
     */
    public function getGroupClassesByDay($day, $CompanyNum, $duration)
    {
        $today = date("Y-m-d");
        $length = "+" . $duration;
        $endDate = date('Y-m-d', strtotime($length, strtotime($today)));
        if ($duration == "per") {
            $classes = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->where("DayNum", "=", $day)->where("StartDate", ">=", $today)->groupBy("GroupNumber")->havingRaw("Count(GroupNumber) > 1")->get();
        } else {
            $classes = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->where("DayNum", "=", $day)->where("StartDate", ">=", $today)->where("EndDate", "<=", $endDate)->
            groupBy("GroupNumber")->havingRaw("Count(GroupNumber) > 1")->get();
        }
        if ($classes) {
            $classArr = array();
            foreach ($classes as $class) {
                $classObj = new ClassCalendar();
                foreach ($class as $key => $value) {
                    $classObj->__set($key, $value);
                }
                array_push($classArr, $classObj);
            }
            return $classArr;
        }
        return null;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function insertNewClass($data)
    {
        return DB::table('boostapp.classstudio_date')->insertGetId($data);
    }

    /**
     * @param $data
     * @param $id
     * @return mixed
     */
    public static function updateClass($data, $id)
    {
        return ClassStudioDate::updateById($id, $data);
    }

    /**
     * @param $data
     * @param $groupid
     * @return mixed
     */
    public static function updateClassViaGroup($data, $groupid)
    {
        $affected = DB::table('boostapp.classstudio_date')
            ->where('GroupNumber', "=", $groupid)
            ->update($data);

        $idList = DB::table('boostapp.classstudio_date')
            ->where('GroupNumber', "=", $groupid)
            ->select('id')
            ->get();

        // check and sync data to Google Calendar
        foreach ($idList as $line) {
            // sync if needed
            GoogleCalendarService::checkClassDateChangedAndSync($line->id, $data);
        }

        return $affected;
    }

    /**
     * @param $groupNumber
     * @param $startDate
     * @param $dataArr
     * @return mixed
     */
    public static function updateClassesByGroupAndDate($groupNumber, $startDate, $dataArr)
    {
        $affected = DB::table('boostapp.classstudio_date')
            ->where('GroupNumber', $groupNumber)
            ->where('StartDate', '>=', $startDate)
            ->update($dataArr);

        $idList = DB::table('boostapp.classstudio_date')
            ->where('GroupNumber', $groupNumber)
            ->where('StartDate', '>=', $startDate)
            ->select('id')
            ->get();

        // check and sync data to Google Calendar
        foreach ($idList as $line) {
            // sync if needed
            GoogleCalendarService::checkClassDateChangedAndSync($line->id, $dataArr);
        }

        return $affected;
    }

    /**
     * @param $startDate
     * @param $CompanyNum
     * @param $dataArr
     * @return mixed
     */
    public static function updateClassesByStartDate($startDate, $CompanyNum, $dataArr)
    {
        $affected = DB::table('boostapp.classstudio_date')
            ->where('CompanyNum', $CompanyNum)
            ->where('StartDate', '=', $startDate)
            ->update($dataArr);

        $idList = DB::table('boostapp.classstudio_date')
            ->where('CompanyNum', $CompanyNum)
            ->where('StartDate', '=', $startDate)
            ->select('id')
            ->get();

        // check and sync data to Google Calendar
        foreach ($idList as $line) {
            // sync if needed
            GoogleCalendarService::checkClassDateChangedAndSync($line->id, $dataArr);
        }

        return $affected;
    }

    /**
     * @param $data
     * @param $groupid
     * @param $days
     * @return int
     */
    public static function updateClassViaGroupAndDays($data, $groupid, $days)
    {
        $result = 0;
        foreach ($days as $day) {
            $count = DB::table('boostapp.classstudio_date')
                ->where('GroupNumber', "=", $groupid)
                ->where('DayNum', "=", $day)
                ->update($data);
            $result += $count;

            $idList = DB::table('boostapp.classstudio_date')
                ->where('GroupNumber', "=", $groupid)
                ->where('DayNum', "=", $day)
                ->select('id')
                ->get();

            // check and sync data to Google Calendar
            foreach ($idList as $line) {
                // sync if needed
                GoogleCalendarService::checkClassDateChangedAndSync($line->id, $data);
            }
        }
        return $result;
    }

    /**
     * @param $studioDateObjArr
     * @param $displayCancel
     * @return void
     */
    public static function cancelClass($studioDateObjArr, $displayCancel = null)
    {
        $isSingleClass = count($studioDateObjArr) == 1 ? 1 : 0;
        foreach ($studioDateObjArr as $studioDateObj) {
            $studioDateObj = new ClassStudioDate($studioDateObj->id);
            $studioDateObj->cancelClassDate($displayCancel);

            $studioActObj = new ClassStudioAct();
            $studioActObj->cancelClassActs($studioDateObj->__get('id'), $isSingleClass);

            $studioDateObj->updateClientRegisterCount();
        }
    }

    /**
     * @param $startDate
     * @param $groupNumber
     * @return mixed
     */
    public function isActiveClassRemaining($startDate, $groupNumber)
    {
        return DB::table($this->table)->where('GroupNumber', $groupNumber)
            ->where('start_date', '>=', $startDate)->where('Status', '!=', '2')->exists();
    }

    /**
     * @param $start_date
     * @param $groupNumber
     * @return void
     */
    public function deleteRegularAssignmentsIfAllCanceled($start_date, $groupNumber)
    {
        if (!$this->isActiveClassRemaining($start_date, $groupNumber)) {
            $studioDateRegular = new ClassStudioDateRegular();
            $studioDateRegular->deleteRegularAssignments($_POST["groupNumber"]);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function cancelClassById($id)
    {
        $company = Company::getInstance(false);
        return DB::table('classstudio_date')->where('id', $id)->where('CompanyNum', $company->__get("CompanyNum"))->update(array('Status' => '2', 'displayCancel' => '1'));
    }

    /**
     * @param $id
     * @return void
     */
    public function changeCanceledToActive($id)
    {
        DB::table($this->table)
            ->where("id", $id)
            ->update(["Status" => 0]);
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteClassAndUnassignClients($id)
    {
        $this->cancelClassById($id);
        $classStudio = new ClassStudioAct();
        $clients = $classStudio->getClientsFromActs($id);
        $clientActivities = new ClientActivities();
        $clientActivities->refundCanceledClass($clients);
    }

    /**
     * @param $id
     * @return void
     */
    public static function cancelClassesForSingleClass($id)
    {
        $classStudio = new ClassStudioAct();
        $clients = $classStudio->getClientsFromActs($id);
        $clientActivities = new ClientActivities();
        $clientActivities->refundCanceledClass($clients, 5, false);
    }

    /**
     * @param $groupNumber
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getGroupClassesInRange($groupNumber, $startDate, $endDate)
    {
        return DB::table($this->table)
            ->where("CompanyNum", Company::getInstance()->CompanyNum)
            ->where("StartDate", ">=", $startDate)
            ->where("EndDate", "<=", $endDate)
            ->where("GroupNumber", $groupNumber)
            ->get();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $types
     * @return int
     */
    public function getAllClassInRange($startDate, $endDate, $types = null)
    {
        $company = Company::getInstance(false);
        $startDate = strtotime($startDate);
        $start = date('Y-m-d', $startDate);
        $endDate = strtotime($endDate);
        $end = date('Y-m-d', $endDate);
        if ($types == null || ($types["regular"] == 1 && $types["zoom"] == 1 && $types["online"] == 1)) {
            $ids = DB::table($this->table)->select("id")
                ->where("CompanyNum", "=", $company->__get("CompanyNum"))
                ->where("Status", "=", 0)
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)
                ->get();
        } else if ($types["regular"] == 1 && $types["zoom"] == 0 && $types["online"] == 1) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->where("is_zoom_class", "!=", 1)
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        } else if ($types["regular"] == 1 && $types["zoom"] == 0 && $types["online"] == 0) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->where("is_zoom_class", "!=", 1)->whereNull("liveClassLink")
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        } else if ($types["regular"] == 1 && $types["zoom"] == 1 && $types["online"] == 0) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->whereNull("liveClassLink")
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        } else if ($types["regular"] == 0 && $types["zoom"] == 1 && $types["online"] == 1) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->where(function ($query) {
                $query->whereNotNull("liveClassLink")->orWhere('is_zoom_class', "=", '1');
            })->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        } else if ($types["regular"] == 0 && $types["zoom"] == 0 && $types["online"] == 1) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->whereNotNull("liveClassLink")
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        } else if ($types["regular"] == 0 && $types["zoom"] == 1 && $types["online"] == 0) {
            $ids = DB::table($this->table)->select("id")->where("CompanyNum", "=", $company->__get("CompanyNum"))->where("Status", "=", 0)->where("is_zoom_class", "=", 1)
                ->whereNull("meetingTemplateId")
                ->where("StartDate", ">=", $start)->where("EndDate", "<=", $end)->get();
        }
        if (isset($ids)) {
            $res = 0;
            foreach ($ids as $id) {
                $this->deleteClassAndUnassignClients($id->id);
                $res = 1;
            }
            return $res;
        } else {
            return 0;
        }
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public function getClassesCurrentDate($companyNum)
    {
        $beginOfDay = date('Y-m-d H:i:s', strtotime("today"));
        $EndOfDay = date('Y-m-d H:i:s', strtotime("tomorrow") - 1);

        return DB::table($this->table)
            ->where('StartDate', '=', date('Y-m-d'))
            ->where('status', '=', 0)
            ->where('CompanyNum', '=', $companyNum)
            ->orderBy('StartTime', 'ASC')
            ->get();
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public function getClassesAct($companyNum)
    {
        $Classes = $this->getClassesCurrentDate($companyNum);
        foreach ($Classes as $class) {
            $act = DB::table('classstudio_act')
                ->where('classstudio_act.ClassId', '=', $class->id)->whereIn("classstudio_act.Status", [1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 22])
                ->get();

            $class->ClassParticipants = $act;
            $User = Users::find($class->GuideId);
            $class->UploadImage = !empty($User->UploadImage) ? 'https://login.boostapp.co.il/camera/uploads/large/' . $User->UploadImage : 'https://login.boostapp.co.il/assets/img/21122016224223511960489675402.png';
        }

        return $Classes;
    }

    /**
     * @param $CompanyNum
     * @param $Floor
     * @return mixed
     */
    public function GetClassesByStudioAndFloor($CompanyNum, $Floor)
    {
        $ClassDates = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '!=', '2')->where('Floor', '=', $Floor)->orderBy('StartTime', 'ASC')->get();
        return $ClassDates;
    }

    /**
     * @param $CompanyNum
     * @param $filters
     * @param $branch
     * @return mixed
     */
    public function GetClassesByStudioByDate($CompanyNum, $filters, $branch = 0)
    {
        $start = date('Y-m-d', strtotime($filters['StartDate']));
        $end = date('Y-m-d', strtotime($filters['EndDate']));
        $sectionsArr = explode(",", $filters['Locations']);
        $classesTypeArr = explode(",", $filters['Classes']);
        $coachesArr = explode(",", $filters['Coaches']);
        $mainBranchId = (new Brand())->getMainBranchId($CompanyNum);
        $branch = $branch == 0 && $mainBranchId ? $mainBranchId : $branch;

        $query = DB::table($this->table)
            ->join("boostapp.sections", "boostapp.sections.id", '=', $this->table . ".Floor")
            ->select($this->table . '.*', 'boostapp.sections.Title')
            ->where($this->table . '.CompanyNum', '=', $CompanyNum)
            ->whereBetween($this->table . ".StartDate", [$start,$end])
            ->where(function ($q) {
                return $q->where($this->table . '.Status', '!=', 2)
                    ->Orwhere($this->table . '.Status', '=', 2)->where($this->table . '.displayCancel', '=', '1');
            })->where('boostapp.sections.Brands', '=', $branch);

        /// todo: missing spaceType where condition, return it after spaces feature will be done


        if ($sectionsArr[0] != "") {
            $query = $query->whereNotIn($this->table . ".Floor", $sectionsArr);
        }
        if ($classesTypeArr[0] != "") {
            $query = $query->whereNotIn($this->table . ".ClassNameType", $classesTypeArr);
        }

        return $query->orderBy($this->table . '.StartDate', 'ASC')->get();
    }

    /**
     * @param $CompanyNum
     * @param $filters
     * @param bool $applyFilter
     * @return mixed
     */
    public function GetTasksByStudioByDate($CompanyNum, $filters, bool $applyFilter = false)
    {
        $roleId = Auth::user()->role_id;
        $start = date('Y-m-d', strtotime($filters['StartDate']));
        $end = date('Y-m-d', strtotime($filters['EndDate']));

        $query = DB::table('boostapp.calendar')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '<>', self::STATUS_CANCELED)
            ->whereBetween("StartDate", [$start, $end]);

        if ($applyFilter) {
            if ($filters['Tasks'] == 0)
                return [];

            if (!empty($filters['Coaches'])) {
                $coachesArr = explode(",", $filters['Coaches']);
                $query = $query->whereNotIn("AgentId", $coachesArr);
            }
        }

        $query = $query->get();

        // filter user view access - check role_id
        if ($roleId != 1) {
            $res = [];
            foreach ($query as $record) {
                $roles = explode(",", $record->GroupPermission);

                if (empty($roles) || (count($roles) == 1 && $roles[0] == "") || in_array($roleId, $roles)) {
                    $res[] = $record;
                }
            }
            return $res;
        }

        return $query;
    }

    /**
     * @param $filtersArr
     * @return false|float|int|mixed|Services_JSON_Error|string|void
     */
    public function getCalendarData($filtersArr = array())
    {

        $company = Company::getInstance("branch");
        $CompanyNum = $company->__get("CompanyNum");
        $StudioUrl = $company->__get("StudioUrl");
        $Section = new Section();
        $Users = new Users();
        $ClassType = new ClassesType();
        $Brand = new Brand();
        $ClassSettings = new ClassSettings();
        $CompanyClassSettings = $ClassSettings->GetClassSettingsByCompanyNum($CompanyNum);
        /** @var Settings $CompanySettings */
        $CompanySettings = Settings::getByCompanyNum($CompanyNum);

//        $branchId = $filtersArr["branchId"] ?? $Brand->getMainBranchId($CompanyNum);

        $ts = strtotime("now");
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);


        $lastUserFilters = $this->GetLastFilterForUser($CompanyNum, Auth::user()->id);
        if ($lastUserFilters) {
            $FilterData['Locations'] = $filtersArr['Locations'] ?? $lastUserFilters->Locations;
            $FilterData['Coaches'] = $filtersArr['Coaches'] ?? $lastUserFilters->Coaches;
            $FilterData['Classes'] = $filtersArr['Classes'] ?? $lastUserFilters->Classes;
            $FilterData['Tasks'] = $filtersArr['Tasks'] ?? $lastUserFilters->Tasks;
            $FilterData['ViewState'] = $filtersArr['ViewState'] ?? $lastUserFilters->ViewState;
            $FilterData['TypeOfView'] = $filtersArr['TypeOfView'] ?? $lastUserFilters->TypeOfView;
            $FilterData['SplitView'] = $filtersArr['SplitView'] ?? $lastUserFilters->SplitView;
            $FilterData['MobileView'] = $filtersArr['MobileView'] ?? $lastUserFilters->MobileView;
            $FilterData['MobileSplitView'] = $filtersArr['MobileSplitView'] ?? $lastUserFilters->MobileSplitView;
            $FilterData['MobileTypeOfView'] = $filtersArr['MobileTypeOfView'] ?? $lastUserFilters->MobileTypeOfView;
            $FilterData['BranchId'] = $filtersArr['branchId'] ?? $lastUserFilters->BranchId;
            $branchId = $FilterData['BranchId'];

            $screen_width = !empty($filtersArr['ScreenWidth']) ? $filtersArr['ScreenWidth'] : $_COOKIE['screen_width'];
            if (!isset($filtersArr['StartDate']) || !isset($filtersArr['EndDate'])) {
                if ($screen_width > 767) {
                    if ($FilterData['ViewState'] == "dayGridMonth") {
                        $FilterData["StartDate"] = date('Y-m-01', $start);
                        $FilterData["EndDate"] = date('Y-m-t', strtotime('next saturday', $start));
                    } else if ($FilterData['ViewState'] == "timeGridWeek") {
                        $FilterData["StartDate"] = date('Y-m-d', $start);
                        $FilterData["EndDate"] = date('Y-m-d', strtotime('next saturday', $start));
                    } else if ($FilterData['ViewState'] == "timeGridThreeDay") {
                        $FilterData["StartDate"] = date('Y-m-d');
                        $FilterData["EndDate"] = date('Y-m-d', strtotime('+2 days'));
                    } else {    // daily
                        $FilterData["StartDate"] = date('Y-m-d');
                        $FilterData["EndDate"] = date('Y-m-d');
                    }
                } else {
                    if ($FilterData['MobileView'] == "dayGridMonth") {
                        $FilterData["StartDate"] = date('Y-m-01', $start);
                        $FilterData["EndDate"] = date('Y-m-t', strtotime('next saturday', $start));
                    } else if ($FilterData['MobileView'] == "timeGridWeek") {
                        $FilterData["StartDate"] = date('Y-m-d', $start);
                        $FilterData["EndDate"] = date('Y-m-d', strtotime('next saturday', $start));
                    } else if ($FilterData['MobileView'] == "timeGridThreeDay") {
                        $FilterData["StartDate"] = date('Y-m-d');
                        $FilterData["EndDate"] = date('Y-m-d', strtotime('+2 days'));
                    } else {    // daily
                        $FilterData["StartDate"] = date('Y-m-d');
                        $FilterData["EndDate"] = date('Y-m-d');
                    }
                }


                $FilterData['ViewDate'] = date('Y-m-d');
            } else {
                $FilterData['StartDate'] = $filtersArr['StartDate'];
                $FilterData['EndDate'] = $filtersArr['EndDate'];
                $FilterData['ViewDate'] = $filtersArr['ViewDate'];
            }
            if ($FilterData['ViewState'] == "dayGridMonth") {
                $FilterData['TypeOfView'] = $lastUserFilters->TypeOfView;
            }
            if ($FilterData['MobileView'] == "dayGridMonth") {
                $FilterData["MobileTypeOfView"] = $lastUserFilters->MobileTypeOfView;
            }
        } else {
            $branchId = $filtersArr["branchId"] ?? $Brand->getMainBranchId($CompanyNum);
            $FilterData['BranchId'] = $branchId;
            $FilterData['Locations'] = $filtersArr['Locations'] ?? "";
            $FilterData['Coaches'] = $filtersArr['Coaches'] ?? "";
            $FilterData['Classes'] = $filtersArr['Classes'] ?? "";
            $FilterData['Tasks'] = $filtersArr['Tasks'] ?? 1;
            $FilterData['ViewState'] = $filtersArr['ViewState'] ?? 'timeGridWeek';
            $FilterData['StartDate'] = $filtersArr['StartDate'] ?? date('Y-m-d', $start);
            $FilterData['EndDate'] = $filtersArr['EndDate'] ?? date('Y-m-d', strtotime('next saturday', $start));
            $FilterData['ViewDate'] = $filtersArr['ViewDate'] ?? date('Y-m-d');

            $FilterData['TypeOfView'] = $filtersArr['TypeOfView'] ?? 2;
            $FilterData['SplitView'] = $filtersArr['SplitView'] ?? 1;
            $FilterData['MobileView'] = $filtersArr['MobileView'] ?? 'timeGridDay';
            $FilterData['MobileSplitView'] = $filtersArr['MobileSplitView'] ?? 1;
            $FilterData['MobileTypeOfView'] = $filtersArr['MobileTypeOfView'] ?? 2;
        }


        $FilterData['CompanyNum'] = $CompanyNum;
        $FilterData['UserId'] = Auth::user()->id;
//        if (Auth::userCan('161')) {
//            $this->SaveFilterState($FilterData);
//        }
        $this->SaveFilterState($FilterData);

        $userViewSetting['TypeOfView'] = $FilterData['TypeOfView'];
        $userViewSetting['SplitView'] = $FilterData['SplitView'];

        $mobileSettingsArr['view'] = $FilterData['MobileView'];
        $mobileSettingsArr['SplitView'] = $FilterData['MobileSplitView'];
        $mobileSettingsArr['TypeOfView'] = $FilterData['MobileTypeOfView'];

//        if ($screen_width > 767) {
//            $userViewSetting['TypeOfView'] = $FilterData['TypeOfView'];
//            $userViewSetting['SplitView'] = $FilterData['SplitView'];
//        } else {
//            $userViewSetting['TypeOfView'] = $FilterData['MobileTypeOfView'];
//            $userViewSetting['SplitView'] = $FilterData['MobileSplitView'];
//        }

        $settingsArr = array();
        if (isset($filtersArr['TimeFrom']) || isset($filtersArr['TimeTo'])) {
            /* if (isset($filtersArr['TypeOfView'])) {
              $settingsArr['TypeOfView'] = $filtersArr['TypeOfView'];
              }
              if (isset($filtersArr['SplitView'])) {
              $settingsArr['SplitView'] = $filtersArr['SplitView'];
              } */
            if (isset($filtersArr['TimeFrom'])) {
                $settingsArr['DisplayTimeFrom'] = $filtersArr['TimeFrom'];
            }
            if (isset($filtersArr['TimeTo'])) {
                $settingsArr['DisplayTimeTo'] = $filtersArr['TimeTo'];
            }
            $ClassSettings->UpdateClassSettings($settingsArr, $FilterData['CompanyNum']);
        } else {
            $filtersArr['TimeFrom'] = ($CompanyClassSettings->DisplayTimeFrom && $CompanyClassSettings->DisplayTimeFrom != "00:00:00") ? $CompanyClassSettings->DisplayTimeFrom : '06:00:00';
            $filtersArr['TimeTo'] = ($CompanyClassSettings->DisplayTimeTo && $CompanyClassSettings->DisplayTimeTo != "00:00:00") ? $CompanyClassSettings->DisplayTimeTo : '23:59:00';
        }

        $resources = array();
        $ClassesRes = [];
        $branchArr = array();
        if (count($company->__get("brands")) > 0) {
            $flag = 0;
            foreach ($company->__get("brands") as $branch) {
                $selected = (isset($FilterData['BranchId']) && $FilterData['BranchId'] == $branch->id) ? 1 : 0;
                if ($selected == 1) {
                    $flag = 1;
                }
                $bArr = array(
                    "id" => $branch->id,
                    "name" => $branch->BrandName,
                    "selected" => $selected
                );
                array_push($branchArr, $bArr);
            }
            if ($flag == 0) {
                $branchArr[0]['selected'] = 1;
            }
        } else {
            $branchId = 0;
        }


        //split = 1 no split
        //split = 0 coaches
        //split = 2 calendar
        //$splitView = $ClassSettings->SplitView($CompanyNum);
        $Classes = $this->GetClassesByStudioByDate($CompanyNum, $FilterData, $branchId);
        $arrCheck = array();
        $CountClasses = 0;
        $CountActs = 0;

        foreach ($Classes as $class) {
            $TempClass = new stdClass();
            $resArr = array();
            //not show unsave classStudioDate
            if($class->SaveUntilTime !== null && $class->SaveUntilTime < date('Y-m-d H:i:s')) {
                continue;
            }
            if (Auth::userCan('161')) {
                $coachFlag = true;
                $coachArr = explode(',', $FilterData['Coaches']);
                if ($coachArr[0] != "") {
                    foreach ($coachArr as $coachId) {
                        $diffArr = array_diff($coachArr, [$coachId]);
                        if ($class->GuideId == $coachId && (in_array($class->ExtraGuideId, $diffArr) || !$class->ExtraGuideId || $class->ExtraGuideId == $coachId)) {
                            $coachFlag = false;
                            break;
                        }
                    }
                }
            } else {
                $coachFlag = true;
                $coachId = Auth::user()->id;
                if ($class->GuideId != $coachId && $class->ExtraGuideId != $coachId) {
                    $coachFlag = false;
                    continue;
                }
            }


            if (isset($screen_width) && $screen_width <= 767) {     // mobile
                if ($mobileSettingsArr['SplitView'] == 2) {
                    $TempClass->resourceId = $class->Floor;
                    if (!in_array($class->Floor, $arrCheck)) {
                        array_push($arrCheck, $class->Floor);
                        $resArr = array(
                            "id" => $class->Floor,
                            "title" => $class->Title,
                            "type" => "Floor"
                        );
                    }
                } else if ($mobileSettingsArr['SplitView'] == 0) {
                    $TempClass->resourceId = $class->GuideId;
                    if (!in_array($class->GuideId, $arrCheck)) {
                        array_push($arrCheck, $class->GuideId);
                        $businessHoursArray = $this->createBusinessHoursArray($class->GuideId, $FilterData['StartDate'], $FilterData['EndDate']);
                        $resArr = array(
                            "id" => $class->GuideId,
                            "title" => $class->GuideName,
                            "type" => "GuideId",
                            "businessHours" => $businessHoursArray
                        );
                    }
                }
            } else {
                if ($userViewSetting['SplitView'] == 2) {
                    $TempClass->resourceId = $class->Floor;
                    if (!in_array($class->Floor, $arrCheck)) {
                        array_push($arrCheck, $class->Floor);
                        $resArr = array(
                            "id" => $class->Floor,
                            "title" => $class->Title,
                            "type" => "Floor"
                        );
                    }
                } else if ($userViewSetting['SplitView'] == 0) {
                    $TempClass->resourceId = $class->GuideId;
                    if (!in_array($class->GuideId, $arrCheck)) {
                        array_push($arrCheck, $class->GuideId);
                        $businessHoursArray = $this->createBusinessHoursArray($class->GuideId, $FilterData['StartDate'], $FilterData['EndDate']);
                        $resArr = array(
                            "id" => $class->GuideId,
                            "title" => $class->GuideName,
                            "type" => "GuideId",
                            "businessHours" => $businessHoursArray
                        );
                    }
                }
            }

            $studioDateObj = new ClassStudioDate($class->id);
            $clientCount = $studioDateObj->updateClientRegisterCount();

            $studioActObj = new ClassStudioAct();
            $actsNames = [];
            if ($class->MaxClient == 1) {
                $acts = $studioActObj->getActiveActsByClassId($class->id, $class->StartDate);
                foreach ($acts as $act) {
                    $clientObj = new Client($act->TrueClientId != 0 ? $act->TrueClientId : $act->ClientId);
                    $actsNames[] = $clientObj->__get('CompanyName');
                }
            }

            $user = Users::find($class->GuideId);

            $TempClass->id = $class->id;
            $TempClass->groupNumber = $class->GroupNumber;
            $TempClass->status = $class->Status;
            $TempClass->title = $class->ClassName;
            $TempClass->branch = $class->Brands;
            $TempClass->titleId = $class->ClassNameType;
            $TempClass->start = $class->start_date;
            $TempClass->end = $class->end_date;
            $TempClass->owner = $user->display_name ?? '';
            $TempClass->ownerId = $class->GuideId;
            $TempClass->ExtraGuideId = $class->ExtraGuideId;
            $TempClass->location = $class->Title;
            $TempClass->locationId = $class->Floor;
            $TempClass->backgroundColor = $class->color;
            $TempClass->members = $clientCount['clientRegistered'];
            $TempClass->maxMembers = $class->MaxClient;
            $TempClass->minMembers = $class->MinClass;
            $TempClass->waitingCount = $clientCount['clientWaiting'];
            $TempClass->regularMembers = $class->ClientRegisterRegular;
            $TempClass->regularMembersCount = (new ClassStudioDateRegular())->getActiveRegularTraineesCount($class->GroupNumber, $class->CompanyNum, $class->StartDate);
            $TempClass->isHidden = $class->ShowApp == 2;
            $TempClass->isAlarm = $class->MinClass == 1;
            $TempClass->is_zoom_class = $class->is_zoom_class == 1;
            $TempClass->liveClass = !empty($class->liveClassLink);
            $TempClass->membersNames = $actsNames;

            //Meeting
            if ($class->meetingTemplateId) {
                $TempClass->type = self::TYPE_MEETING;
                $TempClass->customer = self::getMeetingCustomer($class->id);
                $TempClass->status = $class->meetingStatus;
                $TempClass->price_total = $class->purchaseAmount;

                MeetingService::getCustomerIcons($class->id, $TempClass->customer);

                if (isset($TempClass->customer['ClientActivitiesId'])) {

                    /** @var ClientActivities $clientActivity */
                    $clientActivity = ClientActivities::find($TempClass->customer['ClientActivitiesId']);
                    if($clientActivity) {
                        $TempClass->price_total = $clientActivity->ItemPrice;
                        $TempClass->hasActiveSubscription = $clientActivity->isPaymentForSingleClass === '0';
                        if ($TempClass->status == MeetingStatus::COMPLETED || $TempClass->status == MeetingStatus::DIDNT_ATTEND) {
                            //todo-bp-909 (cart) can remove only beta check
                            if(in_array($CompanySettings->beta, [1]) && $clientActivity->InvoiceId) {
                                $debtAmount = $clientActivity->getInvoiceInDebt();
                                $TempClass->has_debt = $debtAmount > 0;
                                if($TempClass->has_debt) {
                                    $TempClass->has_debtAmount = $debtAmount;
                                }
                            } else {
                                $hasDebtAmount = ClientActivities::getDebt($TempClass->customer['ClientActivitiesId']);
                                $TempClass->has_debt = $hasDebtAmount > 0;
                                if (isset($hasDebtAmount) && $TempClass->has_debt) {
                                    $TempClass->has_debtAmount = $hasDebtAmount;
                                }
                            }


                        }
                        if (!$clientActivity->isPaymentForSingleClass) {
                            $TempClass->price_total = ClassesType::find($class->ClassNameType)->Price ?? 0;
                            $TempClass->has_debt = false;
                        }
                    }
                }
            }


            if ($class->Status == 2 && $class->displayCancel == 1) {
                $TempClass->isCancelled = true;
//                $TempClass->isAlarm = true;
            } else {
                $TempClass->isCancelled = false;
                $CountClasses++;
                $CountActs += $class->ClientRegister;
            }


            if ($coachFlag)
                array_push($ClassesRes, $TempClass);

            if (($mobileSettingsArr['SplitView'] != 1 || $userViewSetting['SplitView'] != 1) && !empty($resArr)) {
                array_push($resources, $resArr);
            }
        }

        // TODO remove beta check after beta - BS-1823
        if (in_array($CompanySettings->beta, [1, 2]) && Auth::userCan('138')) {
            // add tasks to response
            $Tasks = [];
            // if not view by spaces
            if ((isset($screen_width) && $screen_width <= 767 && $mobileSettingsArr['SplitView'] != 2)
                || $userViewSetting['SplitView'] != 2) {
                $Tasks = $this->GetTasksByStudioByDate($CompanyNum, $FilterData, true);
            }

            foreach ($Tasks as $task) {
                // prepare record
                $TempTask = new stdClass();

                $TempTask->id = $task->id;
                $TempTask->title = $task->Title;
                $TempTask->status = $task->Status;

                if ($task->Status == self::STATUS_CANCELED) continue;

                switch ($task->Status) {
                    case self::STATUS_OPEN:
                        $TempTask->statusName = lang('open_task');
                        $TempTask->statusColor = '#FBBB45';
                        break;
                    case self::STATUS_COMPLETED:
                        $TempTask->statusName = lang('completed_task');
                        $TempTask->statusColor = '#47CA00';
                        break;
                    default:
                        /** @var TaskStatus $taskStatus */
                        $taskStatus = TaskStatus::find($task->Status);
                        $TempTask->statusName = $taskStatus->Name;
                        $TempTask->statusColor = '#006FFF';
                }

                if ((isset($screen_width) && $screen_width <= 767 && $mobileSettingsArr['SplitView'] == 0)
                    || ($userViewSetting['SplitView'] == 0)) {
                    $TempTask->resourceId = $task->AgentId;

                    if (!in_array($task->AgentId, $arrCheck)) {
                        $arrCheck[] = $task->AgentId;
                        $businessHoursArray = $this->createBusinessHoursArray($task->AgentId, $FilterData['StartDate'], $FilterData['EndDate']);
                        $resources[] = [
                            "id" => $task->AgentId,
                            "title" => $task->GuideName,
                            "type" => "GuideId",
                            "businessHours" => $businessHoursArray
                        ];
                    }
                }

                $TempTask->start = $task->start_date;
                $TempTask->end = $task->end_date;
                $TempTask->backgroundColor = '#FFF3A1';
                $TempTask->owner = $task->GuideName;
                $TempTask->ownerId = $task->AgentId;
                $TempTask->maxMembers = 1;
                $TempTask->members = 0;
                $TempTask->membersNames = [];
                if ($task->ClientId) {
                    /** @var Client $client */
                    $client = Client::find($task->ClientId);
                    if ($client) {
                        $TempTask->members = 1;
                        $TempTask->membersNames[] = $client->CompanyName;
                    }
                }

                $TempTask->priority = $task->Level;

                $TempTask->ExtraGuideId = 0;
                $TempTask->isCancelled = false;
                $TempTask->type = self::TYPE_TASK;

                // add to list
                $ClassesRes[] = $TempTask;
            }
        }

        $filters = $this->getCompanyFilters($CompanyNum, $FilterData, $branchId);
        return json_encode(array(
            'MeetingStatuses' => MeetingStatus::toList(),
            'MeetingSettings' => (MeetingGeneralSettings::getByCompanyNum($CompanyNum)) ? MeetingGeneralSettings::getByCompanyNum($CompanyNum)->toArray() : [],
            'Classes' => $ClassesRes,
            'Stats' => ['TotalClasses' => $CountClasses, 'TotalTrainers' => $CountActs],
            "FilterState" => $FilterData,
            "viewType" => $userViewSetting,
            "resources" => $resources,
            "branches" => $branchArr,
            "filters" => $filters,
            "Status" => "Success",
            "MobileView" => $mobileSettingsArr,
            "TimeFrom" => $filtersArr['TimeFrom'],
            "TimeTo" => $filtersArr['TimeTo'],
            "waitingPopUp" => $ClassSettings->getWatingPopUp($CompanyNum),
            "StudioUrl" => $StudioUrl
        ));
    }

    /**
     * @param $classId
     * @return array
     */
    private static function getMeetingCustomer($classId)
    {
        /** @var ClassStudioAct $act */
        $act = ClassStudioAct::getMeetingActByClassId($classId);

        if (empty($act))
            return [];

        /** @var Client $client */
        $client = Client::find($act->FixClientId);
        return [
            'id' => $act->FixClientId,
            'classActInfo' => $act->id,
            'ClientActivitiesId' => $act->ClientActivitiesId,
            'name' => $client->CompanyName,
            'phone' => $client->ContactMobile,
            'avatar' => Client::getAvatar($client->id, $client->CompanyNum),
        ];
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function SplitView($CompanyNum)
    {
        $SplitView = DB::table($this->table)
            ->select('SplitView')
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $SplitView;
    }

    //ClassCalendar

    /**
     * @param $FilterData
     * @return mixed
     */
    public function GetClassesByfilter($FilterData)
    {
        $res = DB::table($this->table)
            ->where('CompanyNum', '=', $FilterData['CompanyNum'])
            ->where('Status', '=', 0)
            ->where('StartDate', '>=', $FilterData['StartDate'])
            ->where('EndDate', '<=', $FilterData['EndDate'])
            ->where('Brands', '=', $FilterData['Brands'])
            ->whereIn('GuideId', $FilterData['Coaches'])
            ->whereIn('Floor', $FilterData['Rooms'])
            ->whereIn('ClassType', $FilterData['ClassType'])
            ->get();
        return $res;
    }

    /**
     * @param $FilterData
     * @return void
     */
    public function SaveFilterState($FilterData)
    {

        if (DB::table('FilterState')->where('UserId', '=', $FilterData['UserId'])->exists()) {
            DB::table('FilterState')->where('UserId', '=', $FilterData['UserId'])
                ->update($FilterData);
        } else {

            $id = DB::table('FilterState')
                ->insertGetId($FilterData);
        }
    }

    /**
     * @param $CompanyNum
     * @param $UserId
     * @return mixed
     */
    public function GetLastFilterForUser($CompanyNum, $UserId)
    {
        $FilterState = DB::table('FilterState')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('UserId', '=', $UserId)->first();
        return $FilterState;
    }

//    public function getFutureClassesCountByClassId($id) {
//        $classesType = new ClassesType($id);
//        if ($classesType->__get("CompanyNum") != Company::getInstance()->CompanyNum) {
//            return -1;
//        }
//        $res = DB::table($this->table)->where("ClassNameType", $id)
//            ->where("start_time", '>', Date('Y-m-d H:i:s'))->count();
//        return $res;
//    }
//    public function setClassToActive($clasId) {
//
//    }
//    public function setZoomData($canId){
//        if($this->is_zoom_class == "1"){
//            $this->zoom = new ZoomClasses($canId);
//            $this->zoom->getZoomByClassId($this->id);
//        }
//    }

    /**
     * @param $id
     * @return int
     */
    public function getFutureClassesCountByClassId($id)
    {
        $classesType = (new ClassesType)->find($id);
        if ($classesType->__get("CompanyNum") != Company::getInstance()->CompanyNum) {
            return -1;
        }
        $res = DB::table($this->table)->where("ClassNameType", $id)
            ->where(function ($query) {
                $query->where("StartDate", ">", date("Y-m-d"))
                    ->Orwhere("StartDate", date("Y-m-d"))
                    ->where("StartTime", ">=", date("H:i:s"));
            })->count();
        return $res;
    }

    /**
     * @param $companyNum
     * @param $filterData
     * @param $branchId
     * @return array
     */
    public function getCompanyFilters($companyNum, $filterData, $branchId)
    {
        $filterData['Locations'] = $filterData['Classes'] = $filterData['Coaches'] = "";
        $classes = $this->GetClassesByStudioByDate($companyNum, $filterData, $branchId);

        $usersObj = new Users();
        $sectionObj = new Section();
        $classTypeObj = new ClassesType();

        $classTypeArr = $locationArr = $coacherArr = [];
        $coachId = (string)Auth::user()->id;
        foreach ($classes as $class) {
            //not show unsave classStudioDate
            if($class->SaveUntilTime !== null && $class->SaveUntilTime < date('Y-m-d H:i:s')) {
                continue;
            }
            if (!in_array($class->ClassNameType, $classTypeArr)) {
                if (Auth::userCan('161')) {
                    $classTypeArr[] = $class->ClassNameType;
                } elseif ($class->GuideId == $coachId || $class->ExtraGuideId == $coachId) {
                    $classTypeArr[] = $class->ClassNameType;
                    continue;
                }
            }
            if (!in_array($class->GuideId, $coacherArr))
                $coacherArr[] = $class->GuideId;
            if (!in_array($class->ExtraGuideId, $coacherArr))
                $coacherArr[] = $class->ExtraGuideId;
            if (!in_array($class->Floor, $locationArr))
                $locationArr[] = $class->Floor;
        }

        $tasksArr = [];
        if (Auth::userCan('138')) {
            // if user have permissions to view tasks
            $tasks = $this->GetTasksByStudioByDate($companyNum, $filterData);

            // update $coacherArr
            foreach ($tasks as $task) {
                if (!in_array($task->AgentId, $coacherArr))
                    $coacherArr[] = $task->AgentId;
            }

            if (count($tasks) > 0) {
                $tasksArr[] = count($tasks);
            }
        }

        $users = $usersObj->GetCoachesByArr($coacherArr);
        $sections = $sectionObj->getSectionByIds($locationArr);
        $classesTypes = $classTypeObj->getClassTypesWithFullNameByIds($classTypeArr);

        $sectionsArr = [];
        foreach ($sections as $section) {
            $sectionsArr[] = ["id" => $section->id, "title" => $section->Title, "brandId" => $section->Brands];
        }

        return [
            "coaches" => $users,
            "classesTypes" => $classesTypes,
            "locations" => $sectionsArr,
            "tasks" => $tasksArr,
        ];
    }

    /**
     * @param $classId
     * @param $CompanyNum
     * @return array|stdClass
     */
    public function setClassResponseData($classId, $CompanyNum)
    {
        $class = $this->GetClassById($CompanyNum, $classId);
        if ($class) {
            return [];
        } else {
            $TempClass = new stdClass();
//        $splitView = (new ClassSettings())->SplitView($CompanyNum);
//            $arrCheck = array();
//            $resArr = array();
//        if ($splitView->SplitView == 2 || $mobileSettingsArr['SplitView'] == 2) {
//            $TempClass->resourceId = $class->Floor;
//            if (!in_array($class->Floor, $arrCheck)) {
//                array_push($arrCheck, $class->Floor);
//                $resArr = array(
//                    "id" => $class->Floor,
//                    "title" => $class->Title
//                );
//            }
//        } else if ($splitView->SplitView == 0 || $mobileSettingsArr['SplitView'] == 0) {
//            $TempClass->resourceId = $class->GuideId;
//            if (!in_array($class->GuideId, $arrCheck)) {
//                array_push($arrCheck, $class->GuideId);
//                $resArr = array(
//                    "id" => $class->GuideId,
//                    "title" => $class->GuideName
//                );
//            }
//        }
//        $studioDateObj = new ClassStudioDate($class->id);
//        $clientCount = $studioDateObj->updateClientRegisterCount();

            $TempClass->id = $class->id;
            $TempClass->groupNumber = $class->GroupNumber;
            $TempClass->status = $class->Status;
            $TempClass->title = $class->ClassName;
            $TempClass->branch = $class->Brands;
            $TempClass->titleId = $class->ClassNameType;
            $TempClass->start = $class->start_date;
            $TempClass->end = $class->end_date;
            $TempClass->owner = $class->GuideName;
            $TempClass->ownerId = $class->GuideId;
            $TempClass->location = $class->Title;
            $TempClass->locationId = $class->Floor;
            $TempClass->backgroundColor = $class->color;
            $TempClass->members = $class->ClientRegister;
            $TempClass->maxMembers = $class->MaxClient;
            $TempClass->minMembers = $class->MinClass;
            $TempClass->waitingCount = $class->WatingList;
            $TempClass->regularMembers = $class->ClientRegisterRegular;
            $TempClass->isHidden = $class->ShowApp == 2;
            $TempClass->isAlarm = $class->MinClass == 1;

            if ($class->Status == 2 && $class->displayCancel == 1) {
                $TempClass->isCancelled = true;
//                $TempClass->isAlarm = true;
            } else {
                $TempClass->isCancelled = false;
//            $CountClasses++;
//            $CountActs += $class->ClientRegister;
            }

            if ($class->ClientRegister == $class->MaxClient && $class->WatingList == 1) {
                $TempClass->isAlarm = true;
            }
            if ($class->MinClass == 1) {
                $TempClass->isAlarm = true;
            }
            if ($class->ShowApp == 2) {
                $TempClass->isHidden = true;
            }

            return $TempClass;
        }
    }

    /**
     * @return array
     */
    public function initClassData()
    {
        $user = Auth::user();
        $CompanyNum = $user->CompanyNum;
        return [
            "user" => $user,
            "classTypes" => (new ClassesType())->getAllClassTypes(array("CompanyNum" => $CompanyNum)),
            "coaches" => (new Users())->getCoachers($CompanyNum),
            "colors" => (new ClassesType())->getColors(),
            "brands" => (new Brand())->getSectionsSortedByBranch($CompanyNum),
            "calendarCount" => Section::countActive($CompanyNum),
            "deviceTypes" => (new Numbers())->GetActiveNumbersByCompanyNum($CompanyNum),
            "clientLevel" => (new ClientLevel())->getAllByCompanyNum($CompanyNum),
            "membershipTypes" => MembershipType::getActiveMembershipTypes($CompanyNum),
            "productSettings" => (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum),
            "tags" => TagsService::getFavoriteAndOtherCategoriesTags($CompanyNum)
        ];
    }


    /**
     * @param $guideId
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private function createBusinessHoursArray($guideId, $startDate, $endDate): array
    {
        $businessHoursArray = [];
        $meetingStaffRulesAndDatesAvailability = MeetingStaffRuleAvailability::getCoachWeekAvailability($guideId, $startDate, $endDate);
        if (!empty($meetingStaffRulesAndDatesAvailability)) {
            foreach ($meetingStaffRulesAndDatesAvailability as $rulesAndDatesAvailability) {
                $businessHoursArray[] = [
                    "daysOfWeek" => [$rulesAndDatesAvailability->Day],
                    "startTime" => $rulesAndDatesAvailability->StartTime,
                    "endTime" => $rulesAndDatesAvailability->EndTime
                ];
            }
        }
        return $businessHoursArray;
    }

}
