<?php

require_once "Utils.php";
require_once __DIR__ . "/Client.php";
require_once "ClientActivities.php";
require_once "AppNotification.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientName
 * @property $ClientId
 * @property $Dates
 * @property $Status
 * @property $ClassDay
 * @property $ClassTime
 * @property $Floor
 * @property $DayNum
 * @property $ClassName
 * @property $ClassId
 * @property $GroupNumber
 * @property $ClientActivitiesId
 * @property $MembershipType
 * @property $StatusType
 * @property $RegularClassType
 * @property $StartDate
 * @property $EndDate
 * @property $Fix
 * @property $UserId
 *
 * Class ClassStudioDateRegular
 */
class ClassStudioDateRegular extends \Hazzard\Database\Model
{
    protected $table = "classstudio_dateregular";

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

    public function getClientRegularClasses($ClientId, $CompanyNum, $Status)
    {
        return DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', $Status)
            ->get();
    }

    public function GetRegularTraineesByGroupNumber($GroupNumber)
    {
        $Trainees = DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->get();
        return $Trainees;
    }

    public function GetRegularTraineesInDescendingOrder($GroupNumber, $CompanyNum)
    {
        $Trainees = DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('CompanyNum', '=', $CompanyNum)
            ->orderBy('StatusType', 'DESC')
            ->get();
        return $Trainees;
    }


    /**
     * @param $GroupNumber
     * @param $CompanyNum
     * @param $classDate
     * @return ClassStudioDateRegular[]|null
     */
    public static function getActiveRegularTraineesInDescendingOrder($GroupNumber, $CompanyNum, $classDate): ?array
    {
        return self::where('GroupNumber', '=', $GroupNumber)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->where(function ($q) use ($classDate) {
                $q->whereNull('EndDate')->Orwhere('EndDate', '>=', $classDate);
            })
            ->orderBy('StatusType', 'DESC')
            ->get();
    }

    public function getActiveRegularTraineesCount($GroupNumber, $CompanyNum, $classDate)
    {
        return DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('StatusType', '=', 12)
            ->where('Status', '=', 0)
            ->where(function ($q) use ($classDate) {
                $q->whereNull('EndDate')->Orwhere('EndDate', '>=', $classDate);
            })
//            ->where(function ($q) use ($classDate) {
//                $q->whereNull('StartDate')->Orwhere('StartDate', '<=', $classDate);
//            })
            ->count();
    }

    public function GetRegularTraineeByGroupNumberClientID($GroupNumber, $ClientID)
    {
        $Trainee = DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('ClientId', '=', $ClientID)
            ->first();
        return $Trainee;
    }

    public function GetRegularById($regularId, $CompanyNum, $ClientID)
    {
        $Trainee = DB::table($this->table)
            ->where('id', $regularId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientID)
            ->first();
        return $Trainee;
    }

    public function isClientAssignedRegular($GroupNumber, $ClientId)
    {
        return DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('ClientId', '=', $ClientId)
            ->exists();
    }

    public function deleteRegularTraineeByGroupNumberClientID($GroupNumber, $ClientID)
    {
        return DB::table($this->table)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('ClientId', '=', $ClientID)
            ->delete();
    }

    public function deleteRegularAssignmentById($id, $ComapnyNum, $ClientID)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('CompanyNum', '=', $ComapnyNum)
            ->where('ClientId', '=', $ClientID)
            ->delete();
    }

    public static function updateById($id, $arr)
    {
        self::where("id", $id)
            ->update($arr);
    }

    public static function updateByGroupNumber($GroupNumber, $arr)
    {
        self::where("GroupNumber", $GroupNumber)
            ->update($arr);
    }

    public function setData($id)
    {
        $clientAct = DB::table($this->table)
            ->where("id", $id)
            ->where("Status", 0)->first();

        if ($clientAct != null) {
            foreach ($clientAct as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function getRegularReport()
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->orderBy('ClientId', 'ASC')->where('Status', '=', 0)->get();

        $resArr = array('data' => array());
        foreach ($OpenTables as $Task) {
            $tempArr = array();
            $clientActivitiesObj = new ClientActivities($Task->ClientActivitiesId);
            $client = new Client($Task->ClientId);

            $StatusType = $Task->StatusType == 12 ? lang('setting_permanently') : lang('permanent_waitlist');
            $RegularClassType = $Task->RegularClassType == 1 ? lang('permanent_single') : lang('in_date_range');

            $tempArr[0] = '<a href="ClientProfile.php?u=' . $client->__get('id') . '"><span class="text-primary">' . $client->__get('CompanyName') . '</span></a>';
            $tempArr[1] = $client->__get('ContactMobile');
            $tempArr[2] = $clientActivitiesObj->__get('ItemText');
            $tempArr[3] = $Task->ClassName;
            $tempArr[4] = $Task->ClassDay;
            $tempArr[5] = date('H:i', strtotime($Task->ClassTime));
            $tempArr[6] = $RegularClassType;
            $tempArr[7] = ($Task->RegularClassType == 2) ? date('d/m/Y', strtotime($Task->StartDate)) : null;
            $tempArr[8] = ($Task->RegularClassType == 2) ? date('d/m/Y', strtotime($Task->EndDate)) : null;
            $tempArr[9] = $StatusType;

            array_push($resArr['data'], $tempArr);
        }
        return $resArr;
    }


    public function deleteRegularAssignments($GroupNumber)
    {
        $regularActs = DB::table($this->table)
            ->where('GroupNumber', $GroupNumber)
            ->join('client', $this->table . '.ClientId', '=', 'client.id')
            ->select($this->table . '.*', 'client.CompanyName', 'client.FirstName')
            ->get();

        foreach ($regularActs as $regularAct) {
            $log = lang('regular_assignment') . ' ' . $regularAct->ClassName . ' ' . lang('a_day') . ' ' . $regularAct->ClassDay
                . ' ' . lang('in_hour') . ' ' . date('H:i', strtotime($regularAct->ClassTime)) . ' ' . lang('of_the_client') . ' ' . $regularAct->CompanyName . ' ' . lang('removed');

            CreateLogMovement($log, $regularAct->ClientId);
            AppNotification::sendPermanentRegisterCanceled($regularAct);

            DB::table($this->table)->where('id', $regularAct->id)->delete();
        }
    }

    /**
     *  Create new regular assignment and classstudio_act
     *  @param $ClientId mixed client id
     *  @param $ClassId mixed classstudio_date id
     *  @param $ActivityId mixed client_activities id
     *  @param $BetweenDates mixed optional: Set the regular assignment type-
            If empty = permanent,
            If array[StartDate(null=Class->StartDate), EndDate] = By dates
            If number = By count
     * @param $OverrideStatus mixed If one of the 4 first lesson is full (ClientRegister >= MaxClient) Should be provided -
            12 = override waiting list, 9 = assign as waiting
    */
    public function newRegularAssigment($ClientId, $ClassId, $ActivityId, $BetweenDates = null, $OverrideStatus = null): array
    {
        $CompanyNum = Auth::user()->CompanyNum;
        $UserId = Auth::user()->id;
        $clientObj = new Client($ClientId);
        /** @var ClassStudioDate $ClassInfo */
        $ClassInfo = ClassStudioDate::find($ClassId);
        $ActivityInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityId)->first();

        if (is_array($BetweenDates) && $BetweenDates[1] < $ClassInfo->StartDate)
            return (["Message" => "יש לבחור תאריך לאחר מועד השיעור", "Status" => "Error"]);

        /// בדיקה אם כבר קיים שיבוץ לשיעור זה בעבר
        $CheckRegular = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientId)
            ->where('GroupNumber', '=', $ClassInfo->GroupNumber)
            ->where('Status', 0)
            ->where(
                function ($query) {
                    $query->whereNull('EndDate')
                        ->orWhere('EndDate', '>', date('Y-m-d'));
                }
            )
            ->first();

        if ($CheckRegular) {
            return (["Message" => "הלקוח כבר משובץ קבוע לשיעור זה", "Status" => "Error"]);
        }

        //Set regular assignment type
        if ($BetweenDates) {
            if (is_array($BetweenDates)) {//By date
                $BetweenDatesArr = [$BetweenDates[0] ?? $ClassInfo->StartDate, $BetweenDates[1]];
                $RegularDates = ["StartDate" => $BetweenDatesArr[0], "EndDate" => $BetweenDatesArr[1], "RegularClassType" => "2"];
            } else {
                $RegularDates = ["StartDate" => $ClassInfo->StartDate, 'RegularClassType' => '2'];
            }
        }
        else //Permanent
        {
            $RegularDates = ["StartDate" => $ClassInfo->StartDate, 'RegularClassType' => '1'];
        }

        //Check assignment status by first 4 lessons. if one of them is full and $Override status isn't provided,
        // returning message
        if ($this->searchFullClass($ClassId)){
            if ($OverrideStatus) {
                $ClassStatus = $OverrideStatus;
            }
            else {
                return ['Status' => 'full', 'Message' => 'One of of the first lesson is full, provide $OverrideStatus'];
            }
        } else {
            $ClassStatus = 12;
        }

        $ClassRegularData = array('CompanyNum' => $CompanyNum, 'ClientName' => $clientObj->__get('CompanyName'),
            'ClientId' => $ClientId, 'Dates' => date('Y-m-d H:i:s'),
            'ClassDay' => $ClassInfo->Day, 'ClassTime' => $ClassInfo->StartTime,
            'Floor' => $ClassInfo->Floor, 'DayNum' => $ClassInfo->DayNum,
            'ClassName' => $ClassInfo->ClassName, 'ClassId' => $ClassInfo->ClassNameType,
            'GroupNumber' => $ClassInfo->GroupNumber, 'ClientActivitiesId' => $ActivityId,
            'MemberShipType' => $ActivityInfo->MemberShip, 'StatusType' => $ClassStatus, 'UserId' => $UserId);

        $ClassRegularData = array_merge($ClassRegularData, $RegularDates);

        /// שיבוץ מתאמן
        $RegularClassId = DB::table('classstudio_dateregular')->insertGetId($ClassRegularData);

        $ClassesInfo = DB::table('classstudio_date')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('StartDate', '>=', $ClassInfo->StartDate)
            ->where('Status', '=', '0')
            ->where('GroupNumber', '=', $ClassInfo->GroupNumber);

        if (isset($BetweenDatesArr)) {
            $ClassesInfo = $ClassesInfo->whereBetween('StartDate', $BetweenDatesArr);
        }
        else {
            $ClassesInfo = $ClassesInfo->where('StartDate', '>=', $ClassInfo->StartDate);
        }

        $ClassesInfo = $ClassesInfo->orderBy('StartDate', 'ASC')->limit(30)->get();

        $updatedClassIds = [];
        $data = new stdClass();

        $data->clientId = $ClientId;
        $data->clientName = $clientObj->__get('CompanyName');
        $data->status = $ClassStatus;
        $data->chooseMembership = $ActivityId;
        $data->regularClassId = $RegularClassId;

        foreach ($ClassesInfo as $key => $ClassInfo) {
            $data->classId = $ClassInfo->id;
            $studioDateObj = ClassStudioDate::find($ClassInfo->id);
            $newAct = ClassStudioAct::new($studioDateObj, $data);
            if ($newAct['Status'] != 'Success') {
                return ['Status' => 'Error', 'Message' => $ClassInfo->StartDate . ': ' . $newAct['Message']];
            }

            $updatedClassIds[] = ['classId' => $ClassInfo->id, 'clientCount' => $newAct['clientCount']];

            if ($key == 0) {
                $firstLessonAct = $newAct;
            }

            if ($key+1 == $BetweenDates) //If assign by count -> check if reached to count
            {
                break;
            }
        }
        if (is_numeric($BetweenDates)) //Updating 'by-count' permanent assignment end date by last assigned class
            DB::table($this->table)->where('id', $RegularClassId)->update(['EndDate' => $ClassInfo->EndDate]);

        return ['isPermanent' => 1,
            'actInfo' => $firstLessonAct ?? null,
            'updatedClassIds' => $updatedClassIds,
            'Status' => 'Success',
            'Message' => 'המתאמן שובץ בהצלחה'];
    }

    //

    /**
     * Check if one of the first 4 lesson in permanent lesson is full
     * @param $classId
     * @return bool
     */
    public function searchFullClass($classId)
    {
        $studioDateObj = new ClassStudioDate($classId);
        if ($studioDateObj->__get('MaxClient') <= $studioDateObj->__get('ClientRegister'))
            return true;

        $classes = DB::table('classstudio_date')->where('GroupNumber', $studioDateObj->__get('GroupNumber'))->where('ClassCount', '>', $studioDateObj->__get('ClassCount'))->limit(3)->get();
        foreach ($classes as $class) {
            if ($class->MaxClient <= $class->ClientRegister)
                return true;
        }
        return false;
    }


    /**
     * Create assignment for class for every regular assigned trainee
     * using class 'GroupNumber'
     * @param $classId mixed classstudio_date.id
     */
    public static function createActsByRegularAssignment($classId)
    {
        /** @var ClassStudioDate $studioDate */
        $studioDate = ClassStudioDate::find($classId);
        $regularTrainees = self::getActiveRegularTraineesInDescendingOrder(
            $studioDate->GroupNumber,
            $studioDate->CompanyNum,
            $studioDate->StartDate
        );
        foreach ($regularTrainees as $trainee) {
            $data = new stdClass();
            $data->clientId = $trainee->ClientId;
            $data->classId = $classId;
            if ((int)$trainee->ClientActivitiesId === 0 && !empty($trainee->meetingTemplateId)) {
                $res = ClientActivities::assignMembership([
                    'clientId' => $trainee->ClientId,
                    'itemId' => Item::getSingleClassItem($studioDate->ClassNameType),
                    'itemPrice' => $studioDate->purchaseAmount,
                    'activityName' => $studioDate->getSingleItemName(),
                    'isForMeeting' => 1,
                    'isDisplayed' => 0
                ]);
                if ($res['Status'] == 1) {
                    $data->activityId = $res['ClientActivityId'];
                }
            } else {
                $data->activityId = $trainee->ClientActivitiesId;
            }
//            $data->status = 1;
            $data->status = $trainee->StatusType;
            $data->regularClassId = $trainee->id;

            ClassStudioAct::new($studioDate, $data);
        }
    }

    public function deleteRegularClassesByClientId($companyNum, $clientId)
    {
        DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('ClientId', '=', $clientId)
            ->delete();
    }

    /**
     * The function return if exist data in classstudio_dateregular for groupNumber
     * @param $companyNum
     * @param $clientId
     * @param $groupNumber
     * @return mixed
     */
    public static function isExistsRegularForGroupNumberAndClientId($companyNum, $clientId, $groupNumber)
    {
        return self::where('CompanyNum', $companyNum)
            ->where('GroupNumber', $groupNumber)
            ->where('ClientId', $clientId)
            ->exists();
    }
}
