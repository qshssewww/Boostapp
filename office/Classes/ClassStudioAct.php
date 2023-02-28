<?php

require_once __DIR__ . "/AppNotification.php";
require_once __DIR__ . "/AppSettings.php";
require_once __DIR__ . "/ClassCalendar.php";
require_once __DIR__ . "/ClassLog.php";
require_once __DIR__ . "/ClassSettings.php";
require_once __DIR__ . "/ClassStatus.php";
require_once __DIR__ . "/ClassStudioDate.php";
require_once __DIR__ . "/Client.php";
require_once __DIR__ . "/ClientActivities.php";
require_once __DIR__ . "/Company.php";
require_once __DIR__ . "/Item.php";
require_once __DIR__ . "/MeetingGeneralSettings.php";
require_once __DIR__ . "/../services/GoogleCalendarService.php";
require_once __DIR__ . "/../services/LoggerService.php";
require_once __DIR__ . "/StudioBoostappLogin.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $Brands
 * @property $ClientId
 * @property $TrueClientId
 * @property $ClassId
 * @property $ClassNameType
 * @property $ClassName
 * @property $ClassDate
 * @property $ClassStartTime
 * @property $ClassEndTime
 * @property $ClientActivitiesId
 * @property $Department
 * @property $MemberShip
 * @property $ItemText
 * @property $WeekNumber
 * @property $DeviceId
 * @property $Remarks
 * @property $ShowRemarks
 * @property $StatusCount
 * @property $Status
 * @property $UserDate
 * @property $UserDay
 * @property $UserTime
 * @property $Dates
 * @property $UserId
 * @property $MemberShipJson
 * @property $CancelJson
 * @property $MeetingCancellationPolicy
 * @property $StatusJson
 * @property $ReminderJson
 * @property $ReminderStatus
 * @property $ReminderDate
 * @property $ReminderTime
 * @property $WatinglistMin
 * @property $TimeAutoWatinglist
 * @property $TimeAutoWatinglistDate
 * @property $StatusTimeAutoWatinglist
 * @property $SendSMSWeb
 * @property $ClassInfoJson
 * @property $ChangeClassTime
 * @property $ChangeClassDate
 * @property $ChangeClassStatus
 * @property $TestClass
 * @property $TestClassStatus
 * @property $ReClass
 * @property $ForWhichReClass
 * @property $ReClassReason
 * @property $GuideId
 * @property $FloorId
 * @property $KnasOption
 * @property $KnasOptionVule
 * @property $WatingListSort
 * @property $GroupNumber
 * @property $DayNum
 * @property $Day
 * @property $TrueClasess
 * @property $FreeWatingList
 * @property $FirstClass
 * @property $CalendarId
 * @property $ExtraBooking
 * @property $RegularClass
 * @property $RegularClassId
 * @property $Auto
 * @property $SelfStatus
 * @property $WatingStatus
 * @property $FixClientId
 * @property $ActStatus
 * @property $coronaStmt
 *
 * Class ClassStudioAct
 */
class ClassStudioAct extends \Hazzard\Database\Model
{
    protected $table = "boostapp.classstudio_act";

    const STATUS_MEETING_ACTIVE = 1;
    const STATUS_MEETING_CANCELED_BY_STUDIO = 5;
    const STATUS_MEETING_CANCELED = 3;
    const STATUS_MEETING_LATE_CANCEL = 4;
    const STATUS_MEETING_NOT_ARRIVED = 8;

    const STATUS_COUNT_ACTIVE = 0;
    const STATUS_COUNT_INACTIVE = 1;

    public const REMINDER_ACTIVE = 0; // need to send in future
    public const REMINDER_SENT = 1;
    public const REMINDER_NO_SEND = 2;

    /**
     * @param $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_numeric($attributes)) {
            $model = self::find($attributes);
            if ($model) {
                $this->fill($model->toArray());
                $this->exists = true;
            }
            $attributes = [];
        }

        parent::__construct($attributes);
    }

    public static function getClassActById($id)
    {
        return self::where("id", $id)->first();
    }


    /**
     * return active client assignment
     * @param $classId
     * @return ClassStudioAct|null
     */
    public static function getMeetingActByClassId($classId): ?ClassStudioAct
    {
        $ClassStudioActsArray = self::where('ClassId', $classId)
//            ->whereIn('Status', [self::STATUS_MEETING_ACTIVE,11])
            ->orderBy('id', 'desc')
            ->get();

        if(!empty($ClassStudioActsArray)) {
            foreach ($ClassStudioActsArray as $ClassStudioAct) {
                if(in_array($ClassStudioAct->Status, [self::STATUS_MEETING_ACTIVE,11])) {
                    return $ClassStudioAct;
                }
            }
            return $ClassStudioActsArray[0];
        }
        return null;
    }

    public function getClassActByIdAndCompany($id)
    {
        $CompanyNum = Auth::user()->CompanyNum;
        return DB::table($this->table)
            ->where("id", $id)
            ->where("CompanyNum", '=', $CompanyNum)
            ->first();
    }

    /**
     * @param $statusId
     * @param $bySystem
     * @param $updateActStatus
     * @return mixed
     */
    public function changeStatus($statusId, $bySystem = false, $updateActStatus = false)
    {
        /** @var ClassStatus $status */
        $status = ClassStatus::find($statusId);
        $statusCount = $status->StatusCount ?? 0;
        $statusTitle = $status->Title ?? lang('error_admin');

        if (!$status) {
            LoggerService::error(json_encode([
                'classActId' => $this->id,
                'statusId' => $statusId,
            ]), LoggerService::CATEGORY_CLASS_STATUS);

            // fix StatusCount
            if ($statusId == 9) {
                $statusCount = 1;
            } elseif (in_array($statusId, [3, 5, 7, 13, 14, 18, 19, 20])) {
                $statusCount = 2;
            } elseif ($statusId == 4) {
                $statusCount = 3;
            }
        }

        $oldStatuses = json_decode($this->StatusJson)->data ?? [];
        $oldStatuses[] = [
            "Dates" => date("Y-m-d H:i:s"),
            "Status" => $statusId,
            "UserId" => !$bySystem && Auth::check() ? Auth::user()->id : "",
            "UserName" => !$bySystem && Auth::check() ? Auth::user()->display_name : "",
            "StatusTitle" => $statusTitle,
        ];

        $statusJson = json_encode(["data" => $oldStatuses]);
        $updateArr = ["Status" => $statusId, "StatusCount" => $statusCount, "StatusJson" => $statusJson];
        if ($updateActStatus) {
            $updateArr["ActStatus"] = 1;
        }

        $res = self::where('id', $this->id)
            ->update($updateArr);
        GoogleCalendarService::updateCreateIfVisible($this->id);

        ClassLog::insertNewData([
            "CompanyNum" => $this->CompanyNum,
            "ClassId" => $this->ClassId,
            "ClientId" => $this->TrueClientId ?: $this->ClientId,
            "Status" => $statusTitle,
            "UserName" => !$bySystem && Auth::check() ? Auth::user()->id : 0,
            "numOfClients" => self::getClassRegisterCount($this->ClassId, $this->CompanyNum),
        ]);
        return $res;
    }


    public function getClassActsByClientId($clientId, $date, $timeFrame)
    {
        $timeString = "- 1 $timeFrame";
        return DB::table($this->table)
            ->join("boostapp.classstudio_date", "boostapp.classstudio_date.id", "=", "ClassId")
            ->where("ClientId", $clientId)
            ->where("StatusCount", 0)
            ->where("StartDate", ">", date("Y-m-d", strtotime($timeString, strtotime($date))))
            ->where("StartDate", "<=", date("Y-m-d", $date))
            ->get();
    }

    public function getClientsFromActs($id)
    {
        $company = Company::getInstance(false);
        $clientsAct = DB::table($this->table)->where('ClassId', '=', $id)->where('CompanyNum', $company->__get("CompanyNum"))->whereIn('StatusCount', array(0, 1))->get();
        return (new Utils)->convertArrayIntoObjectArray($clientsAct, "ClassStudioAct");
    }

    public function getClientsFromActsAndStatus($id, $statusArr)
    {
        $company = Company::getInstance(false);
        $clientsAct = DB::table($this->table)->where('ClassId', '=', $id)->where('CompanyNum', $company->__get("CompanyNum"))
            ->whereIn('StatusCount', array(0, 1))->whereIn('Status', $statusArr)->get();
        return (new Utils)->convertArrayIntoObjectArray($clientsAct, "ClassStudioAct");
    }

    public function getClientsActByClientId($ClientId, $orderDirection = 'asc')
    {
        $company = Company::getInstance(false);
        $clientsAct = DB::table($this->table)->where('ClientId', '=', $ClientId)->where('CompanyNum', $company->__get("CompanyNum"));
        $clientsAct = !preg_match("/asc/i", $orderDirection) ? $clientsAct->orderBy('Dates', 'desc') : $clientsAct;
        $clientsAct = $clientsAct->first();
        return $clientsAct;
    }

    public function getClientsActsByClientId($ClientId, $orderDirection = 'asc')
    {
        $company = Company::getInstance(false);
        $clientsAct = DB::table($this->table)->where('ClientId', '=', $ClientId)->where('CompanyNum', $company->__get("CompanyNum"));
        $clientsAct = !preg_match("/asc/i", $orderDirection) ? $clientsAct->orderBy('Dates', 'desc') : $clientsAct;
        $clientsAct = $clientsAct->get();
        return $clientsAct;
    }



    public function getClientsActsByClientIdAndClassId($ClientId, $ClassId)
    {
        $company = Company::getInstance(false);
        $clientsAct = DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('ClassId', '=', $ClassId)
            ->where('CompanyNum', $company->__get("CompanyNum"))
            ->first();
        return $clientsAct;
    }

    /**
     * This function return the client companyNum history.
     * the function get client id, companyNum and flg.
     * @param $ClientId
     * Id of the requested client.
     * @param $CompanyNum
     * the requested CompanyNum.
     * @return Array of ClientAct objects;
     */
    public function getClientHistoryActs($ClientId, $CompanyNum)
    {
        $clientHActs = DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('CompanyNum', $CompanyNum)
            ->get();
        return $clientHActs;
    }

    /**
     * This function check if the client is in his first class.
     * @param $ClientId
     * Id of the requested client.
     * @param $CompanyNum
     * the requested CompanyNum.
     * @return bool
     * True in case the client never was in a class before.
     * And false if he was.
     */
    public function getIsClientInFirstClass($ClientId, $CompanyNum): bool
    {
        $clientHActs = DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('CompanyNum', $CompanyNum)
            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23))
            ->where("ClassDate", "<", date('Y-m-d'))
            ->get();
        return !(bool)$clientHActs;
    }

    public static function getAllActsByClassId($ClassId)
    {
        $company = Company::getInstance(false);
        return self::where('ClassId', '=', $ClassId)
            ->where('CompanyNum', $company->__get("CompanyNum"))
            ->get();
    }

    public function getActiveActsByClassId($ClassId, $startDate)
    {
        $company = Company::getInstance(false);
        return DB::table($this->table)
            ->where('ClassId', '=', $ClassId)
            ->where('CompanyNum', $company->__get("CompanyNum"))
            ->whereIn('Status', [1, 2, 5, 6, 7, 8, 10, 11, 12, 15, 16, 17, 21, 22, 23])
            ->get();
    }

    public function getActsByClassIdAndStatus($classId, $statusArr)
    {
        $company = Company::getInstance(false);
        return DB::table($this->table)
            ->where('ClassId', '=', $classId)
            ->where('CompanyNum', $company->__get("CompanyNum"))
            ->whereIn('Status', $statusArr)
            ->get();
    }

    /**
     * @param $ClassId
     * @param $CompanyNum
     * @return mixed
     */
    public static function getWaitingListActsByClassId($ClassId, $CompanyNum)
    {
        return self::where('ClassId', '=', $ClassId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('StatusCount', '=', 1)
            ->where('Status', '=', 9)
            ->orderBy('WatingListSort', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function getSummary($classId)
    {
        $query = DB::table($this->table)
            ->select("boostapp.client.CompanyName", "boostapp.client.ProfileImage", $this->table . ".*")
            ->join("boostapp.client", "boostapp.client.id", "=", $this->table . ".ClientId")
            ->where("ClassId", $classId);
        $result = [];
        $result["Attending"] = $query->where("StatusCount", 0)->get();
        $result["Unattending"] = $query->where("StatusCount", 2)->get();
        $result["LateCancelation"] = $query->where("StatusCount", 3)->get();

        return $result;
    }

    public function getWaitingList($classId, $companyNum)
    {
        return DB::table($this->table)
            ->join("boostapp.client_activities", "ClientActivitiesId", "=", "client_activities.id")
            ->join("boostapp.client", $this->table . ".ClientId", "=", "client.id")
            ->select(
                "boostapp.client_activities.ItemText",
                "boostapp.client.CompanyName",
                "boostapp.client_activities.TrueDate",
                "boostapp.client_activities.BalanceValue",
                "boostapp.client_activities.TrueBalanceValue",
                $this->table . ".*"
            )
            ->where("ClassId", $classId)
            ->where($this->table . ".CompanyNum", $companyNum)
            ->where($this->table . ".Status", 9)
            ->orderBy('watingListSort', 'asc')
            ->get();
    }

    /**
     * @param $ClientId
     * @param null $date
     * @return mixed
     */
    public function getClasses4FullSync($ClientId, $date = null)
    {
        if (!$date || $date < time()) {
            $date = time();
        }

        $date = date('Y-m-d', $date);

        $sectionsTable = "boostapp.sections";
        $brandsTable = "boostapp.brands";
        $classDateTable = "boostapp.classstudio_date";

        return DB::table($this->table)
            ->join($classDateTable, $this->table . ".ClassId", '=', $classDateTable . ".id")
            ->leftJoin($sectionsTable, $classDateTable . ".Floor", '=', $sectionsTable . ".id")
            ->leftJoin($brandsTable, $sectionsTable . ".Brands", '=', $brandsTable . ".id")
            ->where($this->table . '.FixClientId', '=', $ClientId)
            ->where($this->table . '.ClassDate', '>=', $date)
            ->whereNull($classDateTable . '.meetingTemplateId')
            ->select($this->table . ".id",
                $this->table . '.Status',
                $this->table . '.FixClientId as ClientId',
                $classDateTable . ".ClassName",
                $classDateTable . ".start_date",
                $classDateTable . ".end_date",
                $brandsTable . ".BrandName",
                $classDateTable . ".GuideName",
                $classDateTable . ".Remarks",
                $classDateTable . ".RemarksStatus")
            ->orderBy($classDateTable . ".start_date")
            ->get();
    }

    private function createStringFromObjArr($arr)
    {
        $newArr = [];
        foreach ($arr as $idAndOrder) {
            $newArr[] = '(' . DB::getPdo()->quote($idAndOrder["id"]) . ',' . DB::getPdo()->quote($idAndOrder["order"]) . ')';
        }
        $string = join(',', $newArr);
        return $string;
    }

    private function checkIds($arr)
    {
        $ids = [];

        foreach ($arr as $idAndOrder) {
            array_push($ids, $idAndOrder["id"]);
        }

        $rows = DB::table($this->table)
            ->whereIn("id", $ids)
            ->get();

        foreach ($rows as $row) {
            if ($row->CompanyNum != Company::getInstance()->CompanyNum || $row->Status != 9) {
                return false;
            }
        }
        return true;
    }

    /*  NOT update 'classstudio_date' to completed (Status = 0),
        Update every row in 'classstudio_act' except specific statuses - depending on Class Settings
        Return updated acts count */
    public function completeClass($classId)
    {
        $companyNum = Company::getInstance()->CompanyNum;
        $classSettingsObj = new ClassSettings();
        /* @var $studioDate ClassStudioDate */
        $studioDate = ClassStudioDate::find($classId);
        if(!$studioDate) {
            return null;
        }
        $classSettings = $classSettingsObj->GetClassSettingsByCompanyNum($companyNum);

        $acts = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('ClassId', $classId)
            ->whereIn('Status', [1, 2, 4, 6, 8, 11, 12, 15, 16, 21, 22, 23])
            ->get();


        foreach ($acts as $act) {
            $actObj = new self($act->id);
            $oldStatus = $actObj->__get('Status');
            $newStatus = $classSettings->DefaultStatusClass ? 8 : 2;
            if ($oldStatus == 16) {
                $newStatus = $this->getWithoutChargeStatus($newStatus);
            } elseif ($oldStatus == 12) {
                ClientActivities::CancelClassReturnBalance($act, $actObj->CompanyNum, $newStatus);
            }

            if(!in_array($oldStatus, [2,4,8,21,23])) {
                $actObj->changeStatus($newStatus, false, true);
            }

            $clientObj = Client::find($act->FixClientId);
            $clientObj->LastClassDate = $studioDate->StartDate;
        }

        return count($acts);
    }

    public static function cancelClassActs($classId, $isSingleClass = null)
    {
        /** @var ClassStudioAct[] $classActs */
        $classActs = self::where("ClassId", $classId)->get();

        $res = 0;
        foreach ($classActs as $act) {
            if (in_array($act->Status, [9])) {
                $res += $act->changeStatus(3);
            }
            if (in_array($act->Status, [1, 2, 6, 7, 8, 10, 11, 12, 15, 16, 17, 21, 22, 23])) {
                ClientActivities::CancelClassReturnBalance($act, Company::getInstance()->CompanyNum, 5);
                $res += $act->changeStatus(5);

                if (!$act->RegularClass || $isSingleClass)
                    AppNotification::sendClassCanceledByStudio($act->id);
            }
        }

        return $res;
    }

    //updating class acts when canceled class returned to active
    public function changeCanceledToActive($classActs, $returnTrainees)
    {
        $status = $returnTrainees ? 1 : 3; //If returnTrainees true, set status to active, else change to removed
        $clientActivities = new ClientActivities();

        foreach ($classActs as $act) {
            if ($returnTrainees)
                ClientActivities::CancelClassReturnBalance($act, Company::getInstance()->CompanyNum, $status);
            $actObj = new ClassStudioAct($act->id);
            $actObj->changeStatus($status);
        }

    }

    public function updateActiveToWaiting()
    {
        $status = 9;

        if ($this->Status == $status)
            return json_encode(["Message" => "Client is already in waiting list"]);

        ClientActivities::CancelClassReturnBalance(self::getClassActById($this->id), $this->CompanyNum, $status);
        $this->changeStatus($status);
        $this->WatingListSort = $this->getMaxWaitingSortByClassId($this->ClassId) + 1;
        $this->save();
    }

    public function getMaxWaitingSortByClassId($classId)
    {
        $res = DB::table($this->table)->where('ClassId', $classId)->max('WatingListSort');
        return $res;
    }

    public function getLastClassForClient($companyNum, $clientId, $membershipType, $membership = null)
    {
        $query = DB::table($this->table)
            ->where('CompanyNum', $companyNum)
            ->where('FixClientId', $clientId)
            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23));


        if ($membershipType == '0') {
            $query->where('MemberShip', $membership);
        }

        return $query->orderBy('ClassDate', 'DESC')->first();
    }


    public function getClassesByFixClientId($id, $companyNum, $startDate)
    {
        return DB::table($this->table)
            ->where("CompanyNum", $companyNum)
            ->where("FixClientId", $id)
            ->where("ClassDate", ">=", $startDate)
            ->whereIn("Status", array(9, 12))->get();
    }

    /**
     * @param $id
     * @param $startDate
     * @return self[]
     */
    public function getClassesByFixClientIdAfterDate($id, $startDate)
    {
        return self::where("CompanyNum", Auth::user()->CompanyNum)
            ->where("FixClientId", $id)
            ->where("ClassDate", ">=", $startDate)
            ->get();
    }

    public function getClassesByFixClientIdBetween($id, $startDate, $endDate)
    {
        return DB::table($this->table)
            ->where("CompanyNum", Auth::user()->CompanyNum)
            ->where("FixClientId", $id)
            ->whereBetween("ClassDate", [$startDate, $endDate])
            ->whereIn("Status", array(1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 23))->get();
    }

    public function getClassesByFixClientIdSince($id, $startDate)
    {
        return DB::table($this->table)
            ->where("CompanyNum", Auth::user()->CompanyNum)
            ->where("FixClientId", $id)
            ->where("ClassDate", ">=", $startDate)
            ->whereIn("Status", array(1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 23))->get();
    }
    /**
     * @param $id
     */
    public function GetlatestClass($id)
    {
        return self::where("CompanyNum", Auth::user()->CompanyNum)
            ->where("FixClientId", $id)
            ->whereIn('Status', array(1,2,11,15,16,21,23))
            ->orderBy('ClassDate', 'desc')
            ->first();
    }

    public function getClassNotificationReport($dateFrom, $dateTo)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = DB::table($this->table)
            ->where('classstudio_act.CompanyNum', '=', $CompanyNum)->whereIn('classstudio_act.Status', array(14, 20))
            ->whereBetween('classstudio_act.TimeAutoWatinglistDate', array($dateFrom, $dateTo))
            ->join('client', 'classstudio_act.FixClientId', '=', 'client.id')
            ->join('classstudio_date', 'classstudio_act.ClassId', '=', 'classstudio_date.id')
            ->select('classstudio_act.ClassDate', 'classstudio_act.ClassStartTime', 'classstudio_act.TimeAutoWatinglistDate', 'classstudio_act.TimeAutoWatinglist', 'client.CompanyName', 'client.ContactMobile', 'client.id as clientid', 'classstudio_date.ClassName')
            ->orderBy('TimeAutoWatinglistDate', 'ASC')
            ->get();

        $resArr = array('data' => array());
        foreach ($OpenTables as $Task) {
            $tempArr = array();

            $tempArr[0] = '<a href="ClientProfile.php?u=' . $Task->clientid . '"><span class="text-primary">' . $Task->CompanyName . '</span></a>';
            $tempArr[1] = $Task->ContactMobile;
            $tempArr[2] = '<span style="display: none;">' . date('d/m/Y', strtotime($Task->TimeAutoWatinglistDate)) . '</span> <span class="text-danger">' . date('d/m/Y', strtotime($Task->TimeAutoWatinglistDate)) . ' ' . date('H:i:s', strtotime($Task->TimeAutoWatinglist)) . '</span>';
            $tempArr[3] = $Task->ClassName;
            $tempArr[4] = date('d/m/Y', strtotime($Task->ClassDate));
            $tempArr[5] = date('H:i', strtotime($Task->ClassStartTime));

            $resArr['data'][] = $tempArr;
        }
        return $resArr;
    }


    /**
     * @param $CalendarId
     * @param $ClassStatus
     * @param $SendReminder
     * @param $CompanyNum
     * @param $StartDate
     * @param $start
     * @param $ReminderDate
     * @param $TimeReminder
     * @param $Status
     * @return void
     */
    public static function reminderStatusUpdate($CalendarId, $ClassStatus, $SendReminder, $CompanyNum, $StartDate, $start, $ReminderDate, $TimeReminder, $Status)
    {
        $ReminderStatus = 1;
        if ($SendReminder == 1 ||
            $ClassStatus == 1 ||
            $Status == 2 ||
            $StartDate . " " . $start <= date("Y-m-d H:i:s") ||
            $ReminderDate . " " . $TimeReminder <= date("Y-m-d H:i:s")) {

            $actList = self::where('ClassId', $CalendarId)
                ->where('CompanyNum', $CompanyNum)
                ->get();
        } else {
            $ReminderStatus = 0;

            $actList = self::where('ClassId', $CalendarId)
                ->where('CompanyNum', $CompanyNum)
                ->where(function ($q) use ($StartDate, $start, $ReminderDate, $TimeReminder) {
                    $q->where('ClassDate', '!=', $StartDate)
                        ->Orwhere('ClassStartTime', '!=', $start)
                        ->Orwhere('ReminderDate', '!=', $ReminderDate)
                        ->Orwhere('ReminderTime', '!=', $TimeReminder);
                })
                ->get;
        }

        /** @var ClassStudioAct $act */
        foreach ($actList as $act) {
            $act->ReminderStatus = $ReminderStatus;
            $act->save();
        }
    }

    public static function getClassRegisterCount($classId, $CompanyNum)
    {
        return DB::table('classstudio_act')
            ->where('ClassId', '=', $classId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('StatusCount', '=', '0')
            ->count();
    }

    public static function getClassWaitingCount($classId, $CompanyNum)
    {
        return DB::table('classstudio_act')
            ->where('ClassId', '=', $classId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('StatusCount', '=', '1')
            ->count();
    }

    /**
     * @param $data
     * @return bool|int
     */
    public function reorderClassAct($data)
    {
        if ($this->checkIds($data["orderArr"])) {
            foreach ($data["orderArr"] as $act) {
                /** @var ClassStudioAct $classAct */
                $classAct = ClassStudioAct::find($act["id"]);
                $classAct->WatingListSort = $act["order"];
                $classAct->save();
            }
            return true;
        }
        return -1;
    }

    /**
     * @param ClassStudioDate $StudioDate
     * @param $data stdClass|array [clientId, activityId] Optional: deviceId, regularClassId ,status
     * @return array Status, Description and data
     */
    public static function new(ClassStudioDate $StudioDate, $data)
    {
        if (is_array($data)) {
            $data = (object)$data;
        }
        $res = require 'subClasses/assignClientToClass.php';
        return $res;
    }

    public function isFirstLesson($companyNum, $ClassDate, $FixClientId)
    {
        $res = DB::table($this->table)
            ->where('ClassDate', '<', $ClassDate)
            ->where('CompanyNum', $companyNum)
            ->where('FixClientId', $FixClientId)
            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23))
            ->exists();
        return !$res;
    }

    public function getTakenDevices($classId)
    {
        $devices = DB::table($this->table)->where('ClassId', '=', $classId)->where('DeviceId', '!=', '0')
            ->whereIn('Status', [1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 22, 23])->select('DeviceId')->get();
        $resArr = [];
        foreach ($devices as $device) $resArr[] = $device->DeviceId;
        return $resArr;
    }

    public function getEmbbededTraineeData()
    {
        $traineeInfo['StudioActDetails'] = new self($this->id);
        $medicalObj = new ClientMedical();
        $clientCrmObj = new Clientcrm();
        $company = Company::getInstance();

        $ClassClientInfo = new Client($this->TrueClientId != 0 ? $this->TrueClientId : $this->ClientId);
        if (!$ClassClientInfo) {
            return;
        }
        $clientActivityObj = new ClientActivities($this->ClientActivitiesId);
        $traineeInfo['clientInfo'] = $ClassClientInfo;
        $traineeInfo['ClientCrm'] = $clientCrmObj->GetClientcrmByClientId($this->CompanyNum, $this->ClientId);
        $traineeInfo['ClientMedical'] = $medicalObj->GetMdicalByClientId($this->CompanyNum, $this->ClientId);
        $traineeInfo['ClientActivity'] = $clientActivityObj;
        $traineeInfo['iconArr'] = [
            "firstClass" => $this->isFirstLesson($this->CompanyNum, $this->ClassDate, $this->ClientId),
            "regularAssignment" => $this->RegularClass == 1 && $this->RegularClassId != 0,
            "tryMembership" => $clientActivityObj->__get('Department') == 3,
            "hasDebt" => $ClassClientInfo->BalanceAmount > 0,
            "hasBirthday" => $ClassClientInfo->Dob && date('m-d', strtotime($ClassClientInfo->Dob)) == date('m-d', strtotime("now")),
            "greenpass" => ($company->greenPass) ? $ClassClientInfo->getGreenPassIcon() : '',
        ];

        return $traineeInfo;
    }

    public function getClientRegularActs($CompanyNum, $clientId, $regularClassId, $startDate, $endDate = null)
    {
        $q = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('FixClientId', '=', $clientId)
            ->where('ClassDate', '>=', $startDate)
            ->where('RegularClassId', '=', $regularClassId)
            ->whereIn('Status', array(3, 9, 12));

        if ($endDate)
            $q->where('ClassDate', '<=', $endDate);
        $regularActs = $q->get();

        return $regularActs;
    }

    /**
     * @param $regularClassId
     * @return mixed
     */
    public static function getActByRegular($regularClassId)
    {
        return DB::table(self::getTable())
            ->where('RegularClassId', '=', $regularClassId)
            ->first();
    }

    /**
     * @param $id
     * @param $CompanyNum
     * @return mixed
     */
    public function deleteActById($id, $CompanyNum)
    {
        $ClassAct = self::getClassActById($id);

        $delete = DB::table($this->table)
            ->where('id', $id)
            ->where('CompanyNum', '=', $CompanyNum)
            ->delete();

        // todo delete 28.07
        if(!$delete){
            LoggerService::error('not success delete ClassStudioAct - ' . $id, LoggerService::CATEGORY_ACT_MEETING);
        }

        $CalendarId = StudioBoostappLogin::findByClientIdAndCompanyNum($ClassAct->FixClientId, $ClassAct->CompanyNum)->GoogleCalendarId ?? null;
        // required min length 5 - add leading zeroes
        $eventId = str_pad($id, 5, '0', STR_PAD_LEFT);

        // delete from calendar if synced
        GoogleCalendarService::removeFromCalendar($ClassAct->FixClientId, $CalendarId, $eventId);

        return $delete;
    }

    /**
     * @param $status
     * @return void
     */
    public function setKnasOption($status)
    {
        if ($status == 4) {
            $appSettings = new AppSettings(Auth::user()->__get('CompanyNum'));
            if ($appSettings->__get('MemberShipLimitType') != 2) {
                $this->KnasOptionVule = $appSettings->__get('MemberShipLimitMoney');
                $this->KnasOption = 1;
            }
        } elseif ($status == 3) {
            $this->KnasOption = 0;
            $this->KnasOptionVule = 0;
        }
        $this->save();
    }

    function getWithoutChargeStatus($newStatus)
    {
        if ($newStatus == 2) {
            $updateStatus = 23;
        } elseif ($newStatus == 8) {
            $updateStatus = 7;
        } else {
            $updateStatus = 16;
        }
        return $updateStatus;
    }

    /**
     * Returns the information required for the report
     * @param $dateFrom
     * @param $dateTo
     * @param $companyNum
     * @return array
     */
    public static function getAttendanceReportDataBetweenDates($dateFrom, $dateTo, $companyNum): array
    {
        if(empty($companyNum)) {
            $company = Company::getInstance(false);
            $companyNum = $company->__get("CompanyNum");
        }
        return DB::table(self::getTable())
            ->select(
                self::getTable().".FixClientId",
                self::getTable().".ClientId",
                self::getTable().".ClientActivitiesId",
                self::getTable().".ClassName",
                self::getTable().".ClassDate",
                self::getTable().".ClassStartTime",
                self::getTable().".GuideId",
                self::getTable().".Status",
                self::getTable().".StatusCount",
                "cl.CompanyName as DisplayName",
                "cl.ContactMobile as PhoneNumber",
                "cl.Email",
                "cl.Brands as ClientBrand",
                "ca.ItemText as ItemName",
                "ca.Department",
                "ca.TrueDate",
                "ca.TrueBalanceValue",
            )
            ->leftJoin("boostapp.client as cl", self::getTable().".FixClientId", "=", "cl.id")
            ->leftJoin("boostapp.client_activities as ca", self::getTable().".ClientActivitiesId", "=", "ca.id")
            ->where(self::getTable().'.CompanyNum', '=', $companyNum)
            ->whereBetween(self::getTable().'.ClassDate', array($dateFrom, $dateTo))
            ->get();
    }


    public function getTransferStatusJson($statusId, $CardNumber, $bySystem = true, $transferred = true)
    {
        /** @var ClassStatus $status */
        $status = ClassStatus::find($statusId);
        $oldStatuses = json_decode($this->StatusJson)->data ?? [];
        $text = $CardNumber != 0 && $transferred ? ' השיעור עבר ממנוי מספר ' . $CardNumber : '';
        $oldStatuses[] = [
            "Dates" => date("Y-m-d H:i:s"),
            "Status" => $statusId,
            "UserId" => $bySystem ? "" : Auth::user()->id,
            "UserName" => $bySystem ? "" : Auth::user()->display_name,
            "StatusTitle" => $status->Title . $text
        ];

        return json_encode(["data" => $oldStatuses]);

    }

    /**
     * @return mixed
     */
    public static function getOneMinReminderList()
    {
        return self::where('ReminderDate', '=', date('Y-m-d'))
            ->where('ReminderTime', '<=', date('H:i:s'))
            ->where('ReminderStatus', '=', '0')
            ->whereIn('Status', [1, 2, 6, 10, 11, 12, 15, 16, 21, 22, 23])
            ->where('StatusCount', '=', '0')
            ->get();
    }

    /**
     * @param $arr
     * @return mixed
     */
    public function update($arr)
    {
        if (isset($arr['Status'])) {
            $this->changeStatus($arr['Status']);

            unset($arr['Status'], $arr['StatusCount'], $arr['StatusJson']);
        }

        $affected = self::where('id', $this->id)
            ->where('CompanyNum', $this->CompanyNum)
            ->update($arr);

        // sync if needed
        GoogleCalendarService::checkChangedAndSync($this->id, $arr);

        return $affected;
    }

    public static function getLastActiveClass($ClientId, $date) {
        return self::where('FixClientId', $ClientId)
            ->whereBetween('ClassDate', [$date, date('Y-m-d')])
            ->whereIn('Status', [1,2,4,6,10,11,12,15,16,21,22,23])
            ->orderBy('ClassDate', 'desc')
            ->limit(1)
            ->pluck('ClassDate');

    }

    public function getStatusTestClassByClientId($CompanyNum, $ClientId){
        return DB::table($this->table)
            ->select('id', 'Status')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientId)
            ->where('TestClass', '=', '2')
            ->orderBy('TestClassStatus', 'ASC')
            ->orderBy('ClassDate', 'DESC')
            ->first();
    }

    /**
     * @param $ClientId
     * @param $StatusArray
     * @return mixed
     */
    public function getClasses4Sync($ClientId, $StatusArray = [1, 6, 9, 10, 11, 12, 15, 16, 17, 21, 23])
    {
        $sectionsTable = "boostapp.sections";
        $brandsTable = "boostapp.brands";
        $classDateTable = "boostapp.classstudio_date";

        return self::join($classDateTable, $this->table . ".ClassId", '=', $classDateTable . ".id")
            ->leftJoin($sectionsTable, $classDateTable . ".Floor", '=', $sectionsTable . ".id")
            ->leftJoin($brandsTable, $sectionsTable . ".Brands", '=', $brandsTable . ".id")
            ->where($this->table . '.FixClientId', '=', $ClientId)
            ->where($this->table . '.ClassDate', '>=', date('Y-m-d'))
            ->whereIn($this->table . '.Status', $StatusArray)
            ->select($this->table . ".id",
                $classDateTable . ".ClassName",
                $classDateTable . ".start_date",
                $classDateTable . ".end_date",
                $brandsTable . ".BrandName",
                $classDateTable . ".GuideName",
                $classDateTable . ".Remarks",
                $classDateTable . ".RemarksStatus")
            ->orderBy($classDateTable . ".start_date")
            ->get();
    }

    /**
     * @param $ClassActId
     * @return mixed
     */
    public function getSingleClass4Sync($ClassActId)
    {
        $sectionsTable = "boostapp.sections";
        $brandsTable = "boostapp.brands";
        $classDateTable = "boostapp.classstudio_date";

        return DB::table($this->table)
            ->join($classDateTable, $this->table . ".ClassId", '=', $classDateTable . ".id")
            ->leftJoin($sectionsTable, $classDateTable . ".Floor", '=', $sectionsTable . ".id")
            ->leftJoin($brandsTable, $sectionsTable . ".Brands", '=', $brandsTable . ".id")
            ->where($this->table . '.id', $ClassActId)
            ->select($this->table . ".id",
                $this->table . ".Status",
                $this->table . ".FixClientId as ClientId",
                $classDateTable . ".ClassName",
                $classDateTable . ".start_date",
                $classDateTable . ".end_date",
                $brandsTable . ".BrandName",
                $classDateTable . ".GuideName",
                $classDateTable . ".Remarks",
                $classDateTable . ".RemarksStatus")
            ->first();
    }

    /**
     * Returns the number of classes that the client has attended until now
     * @param $ClientId
     * @return int
     */
    public static function countByClient($ClientId) {
        return self::where('FixClientId', $ClientId)
            ->where('StatusCount', 0)
            ->where('ClassDate', '<=', date('Y-m-d'))
            ->count();
    }

    /**
     * Changed client act to count as attended
     * @return void
     */
    public function setNotAttendAndCharged()
    {
        $activity = $this->clientActivity();
        if ($activity) {
            ClientActivities::CancelClassReturnBalance($this, $this->CompanyNum, 8);
        }
    }

    private $_client;
    private $_clientActivity;
    private $_classStudioDate;
    private $_user;
    private $_guide;

    /**
     * @return ClientActivities|\Hazzard\Database\Model|null
     */
    public function clientActivity()
    {
        if (empty($this->_clientActivity)) {
            $this->_clientActivity = ClientActivities::find($this->ClientActivitiesId);
        }
        return $this->_clientActivity;
    }

    /**
     * @param $clientActivity
     * @return void
     */
    public function setClientActivity($clientActivity)
    {
        $this->_clientActivity = $clientActivity;
    }

    /**
     * @return ClassStudioDate|\Hazzard\Database\Model|null
     */
    public function classStudioDate()
    {
        if (empty($this->_classStudioDate)) {
            $this->_classStudioDate = ClassStudioDate::find($this->ClassId);
        }
        return $this->_classStudioDate;
    }

    /**
     * @param ClassStudioDate $classStudioDate
     * @return void
     */
    public function setClassStudioDate(ClassStudioDate $classStudioDate)
    {
        $this->_classStudioDate = $classStudioDate;
    }

    /**
     * @return Client|\Hazzard\Database\Model|null
     */
    public function client()
    {
        if (empty($this->_client)) {
            $this->_client = Client::find($this->ClientId);
        }
        return $this->_client;
    }

    /**
     * @param $client
     * @return void
     */
    public function setClient($client)
    {
        $this->_client = $client;
    }

    /**
     * @return Users|\Hazzard\Database\Model|null
     */
    public function user()
    {
        if (empty($this->_user)) {
            $this->_user = Users::find($this->UserId);
        }
        return $this->_user;
    }

    /**
     * @return Users|\Hazzard\Database\Model|null
     */
    public function guide()
    {
        if (empty($this->_guide)) {
            $this->_guide = Users::find($this->GuideId);
        }
        return $this->_guide;
    }

    /**
     * @param $clientId
     * @param $clientActivityId
     * @return void
     */
    public function changeMeetingClient($clientId, $clientActivityId = 0)
    {
        if ($this->ClientId != $clientId || $this->ClientActivitiesId != $clientActivityId) {
            $this->Status = self::STATUS_MEETING_CANCELED_BY_STUDIO;
            $this->StatusCount = self::STATUS_COUNT_INACTIVE;
            $this->save();
            GoogleCalendarService::updateCreateIfVisible($this->id);

            // handle old client activity
            $clientActivity = $this->clientActivity();
            if ($clientActivity) {
                if ($clientActivity->isPaymentForSingleClass) {
                    $clientActivity->Status = ClientActivities::STATUS_CANCEL;
                    $clientActivity->BalanceMoney = 0;
                    $clientActivity->CancelStatus = 1;
                    $clientActivity->save();

                    $this->client()->updateBalanceAmount();
                } else {
                    ClientActivities::CancelClassReturnBalance(
                        $this,
                        $this->CompanyNum,
                        ClassStudioAct::STATUS_MEETING_CANCELED
                    );
                }
            }

            $meeting = $this->classStudioDate();
            self::createMeetingAct($meeting, $clientId, $clientActivityId);
        }
    }

    /**
     * @param ClassStudioDate $meeting
     * @param $clientId
     * @param $clientActivityId
     * @return int
     */
    public static function createMeetingAct(ClassStudioDate $meeting, $clientId = null, $clientActivityId = 0): int
    {
        if (!is_numeric($clientId)) {
            $clientId = (Client::getRandomClient($meeting->CompanyNum))->id;
        }
        if (empty($clientActivityId)) {
            $res = ClientActivities::assignMembership([
                'clientId' => $clientId,
                'itemId' => Item::getSingleClassItem($meeting->ClassNameType),
                'activityName' => $meeting->getSingleItemName(),
                'itemPrice' => $meeting->purchaseAmount,
                'isForMeeting' => 1,
                'isDisplayed' => 0
            ]);
            $clientActivityId = $res['ClientActivityId'];
        } else {
            $meeting->meetingStatus = MeetingStatus::COMPLETED;
            $meeting->save();
        }
        return ClassStudioAct::new($meeting, [
            'clientId' => $clientId,
            'activityId' => $clientActivityId,
        ])['actId'];
    }

    /**
     * @param $id
     * @return self | null
     */
    public static function getMeetingActByActivityId($id) : ClassStudioAct
    {
        return self::where('ClientActivitiesId', $id)->first();
    }


    /**
     * @param int $id
     * @return int|null
     */
    public static function getClassStudioDateIdMeetingByActivityId(int $id) : ?int
    {
        return self::where('ClientActivitiesId', $id)->pluck('ClassId');
    }

    /**
     * @param $companyNum
     * @param $clientActivityId
     * @param $clientId
     * @return ClassStudioAct[]
     */
    public static function getToDeleteByClientActivityId($companyNum, $clientActivityId, $clientId): array
    {
        return self::where('CompanyNum', $companyNum)
            ->where('ClientActivitiesId', '=', $clientActivityId)
            ->where('ClientId', '=', $clientId)
            ->where('ClassDate', '>', date('Y-m-d'))
            ->get();
    }

    /**
     * @param int $companyNum
     * @param int $clientId
     * @param int $membershipTypeId
     * @return string|null
     */
    public static function getTrueDateFromPrevClassForClient(int $companyNum, int $clientId, int $membershipTypeId = 0): ?string
    {
        $query = self::where('CompanyNum', $companyNum)
            ->where('ClientId', $clientId)
            ->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23));

        if ($membershipTypeId !== 0) {
            $query = $query->where('MemberShip', $membershipTypeId);
        }
        return $query->orderBy('ClassDate', 'DESC')->pluck('TrueDate');
    }

    public static function clientInClass(int $clientId, int $classStudioDateId):bool
    {
        $ClassStudioAct = self::where('FixClientId', $clientId)
            ->where('ClassId', '=', $classStudioDateId)
            ->first();
        return (!empty($ClassStudioAct) && (in_array($ClassStudioAct->Status, [1,2,6,7,8,9,10,11,12,15,16,17,21,22,23])));
    }

    /**
     * @param int $classStudioDateId
     * @return array
     */
    public static function getAllClientIdInClass(int $classStudioDateId):array
    {
        $response = [];
        $classStudioActArray = self::where('ClassId', '=', $classStudioDateId)->get();
        foreach ($classStudioActArray as $ClassStudioAct) {
            if(in_array($ClassStudioAct->Status, [1,2,6,7,8,9,10,11,12,15,16,17,21,22,23])) {
                $response[] = (int)$ClassStudioAct->FixClientId;
            }
        }
        return $response;
    }

    /**
     * @param $CompanyNum
     * @param $ClientId
     * @param $joinDate
     * @return ClassStudioAct | null
     */
    public static function getLastTryOutClass($CompanyNum, $ClientId, $joinDate): ?ClassStudioAct {

        return self::select('ClassId', 'ClassName', 'Status', 'ClassDate')
            ->where('CompanyNum', $CompanyNum)
            ->where('FixClientId', $ClientId)
            ->where('Department', 3)
            ->where('ClassDate', '>=', $joinDate)
            ->orderBy("ClassDate", "desc")
            ->first();
    }

}
