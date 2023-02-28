<?php

require_once __DIR__ . "/Brand.php";
require_once __DIR__ . "/ClassesType.php";
require_once __DIR__ . "/ClassOnline.php";
require_once __DIR__ . '/Clientcrm.php';
require_once __DIR__ . '/ClassLog.php';
require_once __DIR__ . '/ClientMedical.php';
require_once __DIR__ . '/ClassStudioAct.php';
require_once __DIR__ . '/ClassStudioDateRegular.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/Company.php';
require_once __DIR__ . '/MeetingTemplates.php';
require_once __DIR__ . '/NumbersSub.php';
require_once __DIR__ . '/Section.php';
require_once __DIR__ . '/Users.php';
require_once __DIR__ . '/Utils.php';
require_once __DIR__ . '/MeetingCancelReason.php';
require_once __DIR__ . '/../services/meetings/CreateMeetingService.php';
require_once __DIR__ . '/../services/GoogleCalendarService.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $Brands
 * @property $start_date Y-m-d H:i:s
 * @property $end_date Y-m-d H:i:s
 * @property $text
// * @property $textColor
 * @property $color
 * @property $Floor
 * @property $ClassNameType
 * @property $ShowApp
 * @property $ClassName
 * @property $GuideId
 * @property $GuideName
 * @property $ExtraGuideId
 * @property $ExtraGuideName
 * @property $ClassLevel
 * @property $MaxClient
 * @property $MinClass
 * @property $MinClassNum
 * @property $ClassTime
 * @property $StartDate Y-m-d
 * @property $DayNum
 * @property $Day
 * @property $StartTime
 * @property $EndDate Y-m-d
 * @property $EndTime
 * @property $ClassTypeStatus
 * @property $ClassCount
 * @property $ClassDevice
 * @property $ClassMemberType
 * @property $ClassWating
 * @property $ShowClientNum
 * @property $ShowClientName
 * @property $SendReminder
 * @property $TypeReminder
 * @property $TimeReminder
 * @property $CancelLaw
 * @property $CancelDay
 * @property $CancelDayMinus
 * @property $CancelDayName
 * @property $CancelTillTime
 * @property $Status 0=Active, 1=Completed, 2=Canceled
 * @property $displayCancel
 * @property $UserId
 * @property $GroupNumber
 * @property $Dates
 * @property $ClientRegister
 * @property $WatingList
// * @property $Occupancy
// * @property $OccupancyWating
 * @property $MaxWatingList
 * @property $NumMaxWatingList
 * @property $Change
 * @property $Remarks
 * @property $RemarksStatus
 * @property $ClassLimitTypes
 * @property $LimitLevel
 * @property $GenderLimit
 * @property $FreeClass
 * @property $StopCancel
 * @property $StopCancelTime
 * @property $StopCancelType
 * @property $WatingListOrederShow
 * @property $ClassRepeat
 * @property $ClassRepeatType
 * @property $Auto
 * @property $CheckInStatus
 * @property $Private
 * @property $PrivateId
 * @property $ClientRegisterRegular
 * @property $ClientRegisterRegularWating
 * @property $MinClassStatus
 * @property $OpenOrder
 * @property $OpenOrderTime
 * @property $OpenOrderType
 * @property $CloseOrder
 * @property $CloseOrderTime
 * @property $CloseOrderType
 * @property $liveClassLink
 * @property $onlineClassId
 * @property $registerLimit
 * @property $onlineSendType
 * @property $is_zoom_class
 * @property $image
 * @property $purchaseOptions
 * @property $purchaseAmount
 * @property $purchaseLocation
 * @property $ageLimitNum1
 * @property $ageLimitNum2
 * @property $ageLimitType
 * @property $ClassType
 * @property $meetingTemplateId
 * @property $meetingStatus see MeetingStatus class
 * @property $PreparationTimeMinutes
 * @property $SaveUntilTime
 *
 */
class ClassStudioDate extends \Hazzard\Database\Model
{
    const CLASS_TYPE_PERMANENT = 1;
    const CLASS_TYPE_EXPIRATION = 2;
    const CLASS_TYPE_SINGLE = 3;

    public const SEND_REMINDER_ON = 0;
    public const SEND_REMINDER_OFF = 1;
    public const TYPE_REMINDER_SAME_DAY = 1;
    public const TYPE_REMINDER_DAY_BEFORE = 2;
    public const REMARKS_STATUS_ON = 0;
    public const REMARKS_STATUS_OFF= 1;

    const STATUS_ACTIVE = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELLED = 2;

    protected $table = "boostapp.classstudio_date";

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

    /**
     * This method returns an  array 0f objects of all the data
     * about the class/guide and trainees.
     * @return array
     */
    public function getEmbeddedTrainersData(): array
    {
        $clientCount = $this->updateClientRegisterCount();
        $this->__set('ClientRegister', $clientCount['clientRegistered']);
        $this->__set('WatingList', $clientCount['clientWaiting']);
        $this->__set('ClientRegisterRegular', $clientCount['clientRegisteredRegular']);
        $this->__set('ClientRegisterRegularWating', $clientCount['clientWaitingRegular']);

        $ClassStudioActInfo = new ClassStudioAct();
        $ClassStudioDateRegular = new ClassStudioDateRegular();
        $ClassLog = new ClassLog();
        $clientActivityObj = new ClientActivities();
        $medicalObj = new ClientMedical();
        $clientCrmObj = new Clientcrm();
        $company = Company::getInstance();

        $classInfo = [
            'class' => $this,
            'guide' => Users::find($this->GuideId),
            'extraGuide' => $this->ExtraGuideId != 0 ? Users::find($this->ExtraGuideId) : null,
            'locationStr' => $this->getClassLocation(),
            'activeTrainers' => $ClassStudioActInfo->getActiveActsByClassId($this->id, $this->StartDate),
            'waitingList' => ClassStudioAct::getWaitingListActsByClassId($this->id, $this->CompanyNum),
            'regularTrainers' => ClassStudioDateRegular::getActiveRegularTraineesInDescendingOrder($this->GroupNumber, $this->CompanyNum, $this->StartDate),
            'classLog' => $ClassLog->getDescLogByClassId($this->id),
            'conclusionTab' => $this->getConclusionTabData($this->id),
        ];

        foreach ($classInfo['classLog'] as $key => $row) {
            if ($row->UserName)
                $classInfo['classLog'][$key]->userInfo = Users::find($row->UserName);

            // getting actInfo for link
            $classInfo['classLog'][$key]->actInfo = $ClassStudioActInfo->getClientsActsByClientIdAndClassId($row->ClientId, $row->ClassId);
        }
        $regulars = [];
        foreach ($classInfo['regularTrainers'] as $key => $value) {
            $regulars[] = $value->ClientId;
        }
        //For all the acts found in class get the client info
        foreach ($classInfo['activeTrainers'] as $key => $act) {
            $clientId = $act->TrueClientId != 0 ? $act->TrueClientId : $act->ClientId;
            $ClassClientInfo = new Client($clientId);
            if (empty($ClassClientInfo->__get('id'))) {
                $ClassClientInfo->getNotExistClient();
            }
            $clientActivityObj = new ClientActivities($act->ClientActivitiesId);
            $classInfo['activeTrainers'][$key]->clientInfo = $ClassClientInfo;
            $classInfo['activeTrainers'][$key]->ClientCrm = $clientCrmObj->GetClientcrmByClientId($act->CompanyNum, $clientId);
            $classInfo['activeTrainers'][$key]->ClientMedical = $medicalObj->GetMdicalByClientId($act->CompanyNum, $clientId);
            $classInfo['activeTrainers'][$key]->ClientActivity = $clientActivityObj;
            $classInfo['activeTrainers'][$key]->iconArr = [
                "firstClass" => $ClassStudioActInfo->isFirstLesson($this->CompanyNum, $this->StartDate, $clientId),
                "regularAssignment" => in_array($classInfo['activeTrainers'][$key]->ClientId, $regulars),
                "tryMembership" => $clientActivityObj->__get('Department') == 3 || $classInfo['activeTrainers'][$key]->Status == 11,
                "hasDebt" => $ClassClientInfo->BalanceAmount > 0,
                "hasBirthday" => $ClassClientInfo->Dob && $ClassClientInfo->Dob != '0000-00-00' && date('m-d', strtotime($ClassClientInfo->Dob)) == date('m-d', strtotime("now")),
                "greenpass" => ($company->greenPass) ? $ClassClientInfo->getGreenPassIcon() : '',
            ];


        }

        //Get waitingList Trainees info
        foreach ($classInfo['waitingList'] as $key => $waitingAct) {
            $clientId = $waitingAct->TrueClientId != 0 ? $waitingAct->TrueClientId : $waitingAct->ClientId;
            $ClassClientInfo = new Client($clientId);
            if (empty($ClassClientInfo->__get('id'))) {
                $ClassClientInfo->getNotExistClient();
            }
            $clientActivityObj = new ClientActivities($waitingAct->ClientActivitiesId);
            $classInfo['waitingList'][$key]->clientInfo = $ClassClientInfo;
            $classInfo['waitingList'][$key]->ClientCrm = $clientCrmObj->GetClientcrmByClientId($waitingAct->CompanyNum, $clientId);
            $classInfo['waitingList'][$key]->ClientMedical = $medicalObj->GetMdicalByClientId($waitingAct->CompanyNum, $clientId);
            $classInfo['waitingList'][$key]->ClientActivity = $clientActivityObj;
            $classInfo['waitingList'][$key]->iconArr = [
                "firstClass" => $ClassStudioActInfo->isFirstLesson($this->CompanyNum, $this->StartDate, $clientId) ? true : false,
                "regularAssignment" => in_array($classInfo['activeTrainers'][$key]->ClientId, $regulars),
                "tryMembership" => $clientActivityObj->__get('Department') == 3 || $classInfo['activeTrainers'][$key]->Status == 11,
                "hasDebt" => $ClassClientInfo->BalanceAmount > 0,
                "hasBirthday" => $ClassClientInfo->Dob && date('m-d', strtotime($ClassClientInfo->Dob)) == date('m-d', strtotime("now")),
                "greenpass" => ($company->greenPass) ? $ClassClientInfo->getGreenPassIcon() : '',
            ];
        }
//        }

        //Get regular Trainees info
        foreach ($classInfo['regularTrainers'] as $key => $classRegular) {
            $ClassRegularClientInfo = new Client($classRegular->ClientId);
            if (!$ClassRegularClientInfo) {
                continue;
            }
            $classInfo['regularTrainers'][$key]->clientInfo = $ClassRegularClientInfo;

            // getting actInfo for link
            $classInfo['regularTrainers'][$key]->actInfo = $ClassStudioActInfo->getClientsActsByClientIdAndClassId($classRegular->ClientId, $classInfo["class"]->id);
        }

        //Get the class date in HE string.
        $ClassDate = $this->getClassDateHeFromat();
        $classInfo['dateStr'] = $ClassDate['day'] .
            ', ' .
            $ClassDate['numOfDays'] . ' ' . lang('of_a_month');
        if (lang('of_a_month') == 'Of') {
            $classInfo['dateStr'] .= ' ';
        }
        $classInfo['dateStr'] .= $ClassDate['month'] . '  ' . $ClassDate['startTime'] . '-' . $ClassDate['endTime'];

        return $classInfo;
    }

    /**
     * @param $id
     * @return int
     */
    public static function getFutureClassesCountByClassId($id)
    {
        $classesType = (new ClassesType)->find($id);
        if ($classesType->__get("CompanyNum") != Company::getInstance()->CompanyNum) {
            return -1;
        }
        $res = self::where("ClassNameType", $id)
            ->where(function ($query) {
                $query->where("StartDate", ">", date("Y-m-d"))
                    ->Orwhere("StartDate", date("Y-m-d"))
                    ->where("StartTime", ">=", date("H:i:s"));
            })->count();
        return $res;
    }

    /**
     * @return array[]
     */
    public function getManageClass()
    {
        $CompanyNum = Auth::user()->CompanyNum;
        $OpenTables = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
            ->orderBy('Day', 'DESC')->orderBy('StartTime', 'ASC')->groupBy('GroupNumber')->get();

        $resArr = array('data' => array());
        foreach ($OpenTables as $Class) {
            $tempArr = array();

            $ClassType = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Class->ClassNameType)->first();
            $ClassCounts = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $Class->GroupNumber)->where('Status', '!=', '2')->count();

            $RegisterClient = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $Class->GroupNumber)->where('Status', '!=', '2')->avg('ClientRegister');
            $WatingClient = DB::table($this->table)->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $Class->GroupNumber)->where('Status', '!=', '2')->avg('WatingList');

            $tempArr[0] = $ClassType->Type;
            $tempArr[1] = '<a class="text-success" href="ManageClassGroup.php?u=' . $Class->GroupNumber . '"><strong class="text-success">' . $Class->ClassName . '</strong></a>';
            $tempArr[2] = $Class->Day;
            $tempArr[3] = date('H:i', strtotime($Class->StartTime));
            $tempArr[4] = $Class->GuideName;
            $tempArr[5] = round(($RegisterClient) / ($ClassCounts) * 100) . "%";
            $tempArr[6] = round(($WatingClient) / ($ClassCounts) * 100) . "%";
            $tempArr[7] = '<a class="text-success" href="ManageClassGroup.php?u=' . $Class->GroupNumber . '"><strong class="text-success">' . lang('manage_class') . '</strong></a>';

            array_push($resArr['data'], $tempArr);
        }
        return $resArr;
    }

    /**
     * @param $ClassId
     * @param $GroupNumber
     * @param $Floor
     * @param $StartDate
     * @return array
     */
    public static function updateClassRegistersCount($ClassId, $GroupNumber, $Floor, $StartDate)
    {
        $CompanyNum = Auth::user()->CompanyNum;
        $ClientRegister = ClassStudioAct::getClassRegisterCount($ClassId, $CompanyNum);
        $WatingList = ClassStudioAct::getClassWaitingCount($ClassId, $CompanyNum);

        $ClientRegisterRegular = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('Floor', '=', $Floor)
            ->where('StatusType', '=', '12')
            ->where(function ($q) use ($StartDate) {
                $q->where('RegularClassType', '=', 1)
                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
            })->count();

        $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('GroupNumber', '=', $GroupNumber)
            ->where('Floor', '=', $Floor)
            ->where('StatusType', '=', '9')
            ->where(function ($q) use ($StartDate) {
                $q->where('RegularClassType', '=', 1)
                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
            })->count();


        DB::table('classstudio_date')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('id', '=', $ClassId)
            ->update(array(
                'ClientRegister' => $ClientRegister,
                'WatingList' => $WatingList,
                'ClientRegisterRegular' => $ClientRegisterRegular,
                'ClientRegisterRegularWating' => $ClientRegisterRegularWating
            ));

        return array(
            'ClientRegister' => $ClientRegister,
            'WatingList' => $WatingList,
            'ClientRegisterRegular' => $ClientRegisterRegular,
            'ClientRegisterRegularWating' => $ClientRegisterRegularWating
        );
    }

    /**
     * @param $classId
     * @return array|array[]
     */
    public function getConclusionTabData($classId)
    {
        $clients = ClassStudioAct::getAllActsByClassId($classId);
        $resArr = [
            "present" => [],
            "missing" => [],
            "lateCancellation" => []
        ];
        foreach ($clients as $act) {
            $client = new Client($act->FixClientId);
            if (!$client) {
                continue;
            }
            $arr = [
                "actId" => $act->id,
                "clientId" => $client->__get('id'),
                "trueClientId" => $act->TrueClientId,
                "activityId" => $act->ClientActivitiesId,
                "companyName" => $client->__get('CompanyName'),
                "profileImage" => $client->__get('ProfileImage'),
                "firstName" => $client->__get('FirstName'),
                "lastName" => $client->__get('LastName')
            ];
            if ($act->StatusCount == 0 && in_array($act->Status, [2, 23])) {
                $resArr["present"][] = $arr;
            } else if (in_array($act->Status, [7, 8])) {
                $resArr["missing"][] = $arr;
            } else if ($act->StatusCount == 3 && $act->Status == 4) {
                $resArr["lateCancellation"][] = $arr;
            } else {
                continue;
            }
        }
        return $resArr;
    }

    /**
     * @return array
     */
    public function getClassDateHeFromat()
    {
        $classDay = $this->Day;
        $startDate = $this->StartDate;
        $startTimeBase = $this->StartTime;
        $endTimeBase = $this->EndTime;

        $dayArr = array(
            "ראשון" => lang('hebrew_short_sunday'),
            "Sunday" => lang('hebrew_short_sunday'),
            "שני" => lang('hebrew_short_monday'),
            "Monday" => lang('hebrew_short_monday'),
            "שלישי" => lang('hebrew_short_tuesday'),
            "Tuesday" => lang('hebrew_short_tuesday'),
            "רביעי" => lang('hebrew_short_wednesday'),
            "Wednesday" => lang('hebrew_short_wednesday'),
            "חמישי" => lang('hebrew_short_thursday'),
            "Thursday" => lang('hebrew_short_thursday'),
            "שישי" => lang('hebrew_short_friday'),
            "Friday" => lang('hebrew_short_friday'),
            "שבת" => lang('hebrew_short_saturday'),
            "Saturday" => lang('hebrew_short_saturday'),
        );

        $monthsArr = array(
            "01" => lang('january'),
            "02" => lang('february'),
            "03" => lang('march'),
            "04" => lang('april'),
            "05" => lang('may'),
            "06" => lang('june'),
            "07" => lang('july'),
            "08" => lang('august'),
            "09" => lang('september'),
            "10" => lang('october'),
            "11" => lang('november'),
            "12" => lang('december'));

        list($year, $month, $day) = explode('-', $startDate);

        $startTime = date('H:i', strtotime($startTimeBase));
        $endTime = date('H:i', strtotime($endTimeBase));

        return array(
            'day' => $dayArr[$classDay],
            'numOfDays' => $day,
            'month' => $monthsArr[$month],
            'startTime' => $startTime,
            'endTime' => $endTime);
    }

    /**
     * @return string
     */
    public function getClassLocation()
    {
        $ClassBrand = Brand::where('id', $this->Brands)->first();

        if (!$ClassBrand && $this->meetingTemplateId !== null) {
            return null;
        }

        $ClassBrandsInfo = (new Brand())->getAllByCompanyNum($this->CompanyNum);
        //Check if Floor(Section) is set.
        $flg = 0;
        $tmpBrandName = '';
        if ($this->Brands == 0) {
            if (empty($ClassBrandsInfo)) {
                $flg = 1;
            } else {
                $tmpBrandName = $ClassBrandsInfo[0]->BrandName;
            }
        } else {
            $tmpBrandName = $ClassBrand->BrandName;
        }

        //Check if Floor(Section) is set.
        if (!$this->Floor || $this->Floor == 0) {
            $sectionName = lang('no_room_was_assigned');
        } else {
            $ClassSection = new Section();
            $section = $ClassSection->getSectionById($this->Floor);
            $sectionName = $section->Title;
        }

        //Check if the word סניף is in string if not we add.
        if ((strpos($tmpBrandName, 'סניף') !== false) || (strpos($tmpBrandName, 'Branch') !== false)) {
            $brandName = $tmpBrandName;
        } else {
            if ($flg == 1) {
                $brandName = lang('primary_branch');
            } else {
                if (lang('branch') == 'Branch') {
                    $brandName = $tmpBrandName . ' ' . lang('branch');
                } else {
                    $brandName = lang('branch') . ' ' . $tmpBrandName;
                }
            }
        }

        return $brandName . ' - ' . $sectionName;
    }

    /**
     * @return array
     */
    public function updateClientRegisterCount(): array
    {

        $clientRegistered = ClassStudioAct::getClassRegisterCount($this->id, $this->CompanyNum);
        $clientWaiting = ClassStudioAct::getClassWaitingCount($this->id, $this->CompanyNum);

        $StartDate = $this->StartDate;
        $clientRegisteredRegular = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $this->CompanyNum)
            ->where('GroupNumber', '=', $this->GroupNumber)
            ->where('Floor', '=', $this->Floor)
            ->where('StatusType', '=', '12')
            ->where(function ($q) use ($StartDate) {
                $q->where('RegularClassType', '=', 1)
                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
            })->count();

        $clientWaitingRegular = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $this->CompanyNum)
            ->where('GroupNumber', '=', $this->GroupNumber)
            ->where('Floor', '=', $this->Floor)
            ->where('StatusType', '=', '9')
            ->where(function ($q) use ($StartDate) {
                $q->where('RegularClassType', '=', 1)
                    ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $StartDate);
            })->count();

        //Update classstudio_date only if one counter changed
        if ($this->ClientRegister != $clientRegistered || $this->WatingList != $clientWaiting
            || $this->ClientRegisterRegular != $clientRegisteredRegular || $this->ClientRegisterRegularWating != $clientWaitingRegular) {
            DB::table($this->table)->where('id', '=', $this->id)->update(
                ['ClientRegister' => $clientRegistered, 'WatingList' => $clientWaiting,
                    'ClientRegisterRegular' => $clientRegisteredRegular, 'ClientRegisterRegularWating' => $clientWaitingRegular]);
        }

        return ['clientRegistered' => $clientRegistered, 'clientWaiting' => $clientWaiting,
            'clientRegisteredRegular' => $clientRegisteredRegular, 'clientWaitingRegular' => $clientWaitingRegular];
    }

    /**
     * @return array
     */
    public function getAvailableDevices()
    {
        $numbersSubObj = new NumbersSub();
        $studioActObj = new ClassStudioAct();
        $resArr = [];
        $classDevices = $numbersSubObj->GetNumbersSubByCompanyNum($this->CompanyNum, $this->ClassDevice);
        $takenDevices = $studioActObj->getTakenDevices($this->id);
        foreach ($classDevices as $device) {
            if (!in_array($device->id, $takenDevices))
                $resArr[] = $device;
        }
        return $resArr;
    }

    /**
     * @param $status
     * @return void
     */
    public function changeStatus($status)
    {
        //Add to log
        $statusContent = ($status == 0) ? lang('changed_to_active') : lang('changed_to_completed');
        $content = lang('the_class_cron') . ': ' . $this->ClassName . ', ' . lang('in_date_ajax') . ' ' . date('d/m/Y H:i', strtotime($this->start_date))
            . ' ' . $statusContent;

        CreateLogMovement($content, 0);

        //Update class status
        DB::table($this->table)->where('id', '=', $this->id)->update(['Status' => $status]);
    }

    /**
     * @param $displayCancelTerm
     * @return void
     */
    public function cancelClassDate($displayCancelTerm)
    {
        if ($this->Status == 2)
            return;

        $company = Company::getInstance(false);
        $displayCancel = $displayCancelTerm ? 1 : 0; //If true, display on calendar

        $content = lang('the_class_cron') . ': ' . $this->ClassName . ', ' . lang('in_date_ajax') . ' ' . date('d/m/Y H:i', strtotime($this->start_date))
            . ' ' . lang('changed_to_canceled');

        CreateLogMovement($content, 0);

        return DB::table('classstudio_date')->where('id', $this->id)
            ->where('CompanyNum', $company->__get("CompanyNum"))
            ->update(array('Status' => '2', 'displayCancel' => $displayCancel));

    }

    /**
     * @param $classId
     * @param $CompanyNum
     * @return mixed
     */
    public static function getClassById($classId, $CompanyNum)
    {
        return self::where('id', $classId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
    }

    /**
     * @return mixed
     */
    public function getMaxWaitingSort()
    {
        return DB::table('classstudio_act')->where('ClassId', $this->id)->max('WatingListSort');
    }

    //Compare class cancel law to current time, return false for late cancel

    /**
     * @return bool
     */
    public function checkCancelLaw()
    {
        if ($this->CancelLaw == '1') //Class day, until hour
            $cancelDate = $this->StartDate . ' ' . $this->CancelTillTime;
        else if ($this->CancelLaw == '2') //Day before class, until hour
            $cancelDate = date('Y-m-d', strtotime("-1 day", strtotime($this->StartDate))) . ' ' . $this->CancelTillTime;
        else if ($this->CancelLaw == '3') //Selected day before class (converted to days before class), until hour
            $cancelDate = date('Y-m-d', strtotime("- " . $this->CancelDayMinus . " days", strtotime($this->StartDate))) . ' ' . $this->CancelTillTime;
        else if ($this->CancelLaw == '4') //Cancellation Disabled
            return false;
        else $cancelDate = $this->start_date; //Without limit

        if (date('Y-m-d H:i:s') > $cancelDate)
            return false;
        return true;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function saveClass($data)
    {
        return require 'subClasses/saveClass.php';
    }

    /**
     * @param $blockId
     * @return array
     */
    public static function deleteBlockEvent($blockId)
    {
        /** @var ClassStudioDate $class */
        $class = self::find($blockId);
        if (!$class || $class->MaxClient != 0)
            return ['status' => 0, 'message' => lang('error_oops_something_went_wrong')];

        /** @var Users $Guide */
        $Guide = Users::find($class->GuideId);
        $LogText = 'הסיר את בלוק חסימת יומן - ' . $class->ClassName . ', הגדרות חדשות : תאריך '
            . $class->StartDate . ', איש צוות ' . $Guide->display_name . ', שעה ' . $class->StartTime . ' - ' . $class->EndTime;
        CreateLogMovement($LogText, null);

        $class->delete();
        return ['message' => 'Success', 'status' => 1];
    }

    /**
     * @param array $data
     * @return array
     */
    public static function createMeeting(array $data): array
    {
        return CreateMeetingService::create($data);
    }

    /**
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public static function fixMeetings(array $data): array
    {
        return CreateMeetingService::fixMeetings($data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function updateMeeting(array $data): array
    {
        if (empty($this->id))
            return ['status' => Utils::ERROR_STATUS, 'message' => lang('error_oops_something_went_wrong')];
        return CreateMeetingService::update($data, $this);
    }


    /**
     * @param $classId
     * @param $data
     * @param $edit
     * @return void
     */
    public static function insertIntoClass_zoom($classId, $data, $edit = false)
    {
        $CompanyNum = $data['CompanyNum'] ?? Auth::user()->CompanyNum;
        $zoomArr = array(
            "CompanyNum" => $CompanyNum,
            "class_id" => $classId,
            "meeting_id" => $data['meetingNumber'] ?? '',
            'membership_type' => 10,
            'password' => $data['ZoomPassword'] ?? '',
            'chat' => $data['AllowChat'] ?? 0,
            'share_video' => $data['AllowVideoShare'] ?? 0,
            'audio' => $data['AllowSound'] ?? 0
        );

        if (isset($data['AllowSingleEntry']) && $data['AllowSingleEntry'] == 'on' && isset($data["registerLimitZoom"]) && $data["registerLimitZoom"] == 1) {
            $item = new Item();
            $itemRole = new ItemRoles();
            $role = $itemRole->getItemRoleByClassId($classId);
            if ($role->id != null) {
                $item->getItemById($role->__get("ItemId"));
            }
            if ($edit == true && ($role->id == null || empty($role->id))) {
                $item->createItemFromClasses($data, $classId, $CompanyNum);
            } else if ($edit == false) {
                $item->createItemFromClasses($data, $classId, $CompanyNum);
            } else if ($edit == true) {
                $item->createItemFromClasses($data, $classId, $CompanyNum, true);
            }
            $zoomArr["single_price"] = $data['singleEntryRate'];
            $zoomArr["single_reg"] = 1;
        }
        $zoom = new ZoomClasses();
        $zoom->getZoomByClassId($classId);
        if ($edit == false || $zoom->__get("id") == null) {
            DB::table('class_zoom')->insertGetId($zoomArr);
        } else {
            $zoom = new ZoomClasses();
            $zoom->getZoomByClassId($classId);
            if ($zoom->__get("id") != null) {
                DB::table('class_zoom')->where("class_id", "=", $classId)->update($zoomArr);
            }
        }

    }

    /**
     * @param $GroupNumber
     * @param $CompanyNum
     * @return ClassStudioDate|null
     */
    public static function getLastClass($GroupNumber, $CompanyNum): ?ClassStudioDate
    {
        return self::where('CompanyNum', $CompanyNum)
            ->where('GroupNumber', $GroupNumber)
            ->whereIn('Status', [0, 1])
            ->orderBy('StartDate', 'desc')
            ->first();
    }

    /**
     * Count of events from a group number
     * @param $CompanyNum
     * @param $GroupNumber
     * @param $UntilDate mixed limit to events before this date
     * @return int
     */
    public static function getActiveClassCount($CompanyNum, $GroupNumber, $UntilDate = null)
    {
        $query = self::where('CompanyNum', $CompanyNum)
            ->where('GroupNumber', $GroupNumber)
            ->where('Status', 0);

        if ($UntilDate) {
            $query = $query->where('StartDate', '<', $UntilDate);
        }

        return $query->count();
    }

    /**
     * Get all classes from a group number in a certain date
     * @param $CompanyNum
     * @param $GroupNumber
     * @param $StartDate
     * @param bool $getWithCanceled true - want all classStudioDate (all status: 0, 1, 2)
     * @return ClassStudioDate|null
     */
    public static function getByGroupAndDate($CompanyNum, $GroupNumber, $StartDate, $getWithCanceled = false)
    {
        $query = self::where('CompanyNum', $CompanyNum)
            ->where('GroupNumber', $GroupNumber)
            ->where('StartDate', $StartDate);
        if (!$getWithCanceled) {
            $query = $query->where('Status', '!=', '2');
        }
        return $query->first();
    }

    /**
     * @param $isSingleClass
     * @param $onlineClassData
     * @param $onlineClassId
     * @return mixed
     */
    public static function updateOnlineClass($isSingleClass, $onlineClassData, $onlineClassId)
    {
        if ($onlineClassId) {
            $onlineClass = ClassOnline::find($onlineClassId);
            if ($onlineClassData['sendType'] == $onlineClass->getAttribute('sendType') &&
                $onlineClassData['sendTime'] == $onlineClass->getAttribute('sendTime') &&
                $onlineClassData['sendTimeType'] == $onlineClass->getAttribute('sendTimeType')) {
                return $onlineClassId;
            } else {
                if (!$isSingleClass)
                    $onlineClass = new ClassOnline();
            }
        } else {
            $onlineClass = new ClassOnline();
        }
        if (isset($onlineClassData['sendType'])) $onlineClass->sendType = $onlineClassData['sendType'];
        if (isset($onlineClassData['sendTime'])) $onlineClass->sendTime = $onlineClassData['sendTime'];
        if (isset($onlineClassData['sendTimeType'])) $onlineClass->sendTimeType = $onlineClassData['sendTimeType'];
        $onlineClass->save();

        return $onlineClass->id;
    }

    /**
     * @return bool
     */
    public function isClassFull(): bool
    {
        return ClassStudioAct::getClassRegisterCount($this->id, $this->CompanyNum) >= $this->MaxClient;
    }

    /**
     * @param $companyNum
     * @param $groupNumber
     * @param $status
     * @param $classType
     * @return mixed
     */
    public function getClassesByGroupnumberStatusType($companyNum, $groupNumber, $status, $classType)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('GroupNumber', '=', $groupNumber)
            ->where('Status', '=', $status)
            ->where('ClassType', '=', $classType)
            ->orderBy('ClassCount', 'DESC')
            ->first();
    }

    /**
     * @param $id
     * @param $array
     * @return mixed
     */
    public static function updateById($id, $array)
    {
        $affected = self::where('id', $id)
            ->update($array);

        $idList = self::where('id', $id)
            ->select('id')
            ->get();

        // check and sync data to Google Calendar
        foreach ($idList as $line) {
            // sync if needed
            GoogleCalendarService::checkClassDateChangedAndSync($line->id, $array);
        }

        return $affected;
    }

    /**
     * @param $id
     * @param $CompanyNum
     * @param $status
     * @param $array
     * @return mixed
     */
    public static function updateByIdAndStatus($id, $CompanyNum, $status = [0, 1], $array)
    {
        $affected = self::where('id', $id)
            ->whereIn('Status', $status)
            ->where('CompanyNum', $CompanyNum)
            ->update($array);

        $idList = self::where('id', $id)
            ->whereIn('Status', $status)
            ->where('CompanyNum', $CompanyNum)
            ->select('id')
            ->get();

        // check and sync data to Google Calendar
        foreach ($idList as $line) {
            // sync if needed
            GoogleCalendarService::checkClassDateChangedAndSync($line->id, $array);
        }

        return $affected;
    }

    // TODO check everything below

    /**
     * @param $guideId
     * @param $date
     * @return mixed
     */
    public static function getClassesByDateAndGuide($guideId, $date)
    {
        return self::where('StartDate', $date)
            ->where('GuideId', $guideId)
            ->where('Status', 0)
            ->get();
    }

    private $_classStudioAct;
    private $_user;
    private $_guide;
    private $_brand;
    private $_section;

    /**
     * @return ClassStudioAct|null
     */
    public function classStudioAct()
    {
        if (!$this->_classStudioAct) {
            $this->_classStudioAct = ClassStudioAct::where('ClassId', $this->id)->first();
        }
        return $this->_classStudioAct;
    }

    /**
     * @param $classStudioAct
     * @return void
     */
    public function setClassStudioAct($classStudioAct)
    {
        $this->_classStudioAct = $classStudioAct;
    }

    /**
     * @return Users|\Hazzard\Database\Model|null
     */
    public function user()
    {
        if (!$this->_user) {
            $this->_user = Users::find($this->GuideId);
        }
        return $this->_user;
    }

    /**
     * @return Users|\Hazzard\Database\Model|null
     */
    public function guide()
    {
        if (!$this->_guide) {
            $this->_guide = Users::find($this->GuideId);
        }
        return $this->_guide;
    }

    /**
     * @param $guide
     * @return void
     */
    public function setGuide($guide)
    {
        $this->_guide = $guide;
    }

    /**
     * @return Brand|\Hazzard\Database\Model|null
     */
    public function brand()
    {
        if (!$this->_brand) {
            $this->_brand = Brand::find($this->Brands);
        }
        return $this->_brand;
    }

    /**
     * @param $brand
     * @return void
     */
    public function setBrand($brand)
    {
        $this->_brand = $brand;
    }

    /**
     * @return Section|\Hazzard\Database\Model|null
     */
    public function section()
    {
        if (!$this->_section) {
            $this->_section = Section::find($this->Sections);
        }
        return $this->_section;
    }

    /**
     * @param $section
     * @return void
     */
    public function setSection($section)
    {
        $this->_section = $section;
    }

    /**
     * return name for single event membership name
     * @return string
     */
    public function getSingleItemName(): string
    {
        return $this->ClassName . ': ' . Utils::dateToDayName($this->StartDate)
            . ' ' . date('d/m', strtotime($this->StartDate));
    }

    /**
     * @param $cancelReasonId
     * @return void
     */
    public function setCancelReason($cancelReasonId)
    {
        $meetingCancelReason = MeetingCancelReason::where('classId', $this->id)->first();
        if (empty($meetingCancelReason)) {
            $meetingCancelReason = new MeetingCancelReason();
            $meetingCancelReason->classId = $this->id;
        }
        $meetingCancelReason->reasonId = $cancelReasonId;
        $meetingCancelReason->save();
    }

    public static function setClassesBrandBySection($company_num, $section_id, $new_brand){
        self::where('CompanyNum', $company_num)
            ->where('Floor', $section_id)
            ->where('StartDate', '>=', date('Y-m-d', strtotime('-1 week')))
            ->update(['Brands' => $new_brand]);
    }

    /**
     *
     * @param int $cancelReasonId
     * @param bool $notArrived
     */
    public function setStatusToCanceledMeeting(int $cancelReasonId = 0, $notArrived = true)
    {
        $dateNow = date('Y-m-d H:i:s');
        $this->Status = ClassStudioDate::STATUS_CANCELLED;
        /** todo-BP-3962 not sure */
//        $this->meetingStatus = $this->start_date <= $dateNow ? MeetingStatus::DIDNT_ATTEND : MeetingStatus::CANCELED;
//        $this->displayCancel = $this->start_date <= $dateNow ? 1 : 0;
        $this->meetingStatus = $notArrived ? MeetingStatus::DIDNT_ATTEND : MeetingStatus::CANCELED;
        $this->displayCancel = $notArrived  ? 1 : 0;
        $this->setCancelReason($cancelReasonId);
        $this->save();
    }

    /**
     * @param $classStudioDateId
     * @param string $repeatType
     * @param $repeatVal
     * @return array
     */
    public static function getClassStudioDatesToCancelMeeting($classStudioDateId, string $repeatType, $repeatVal): array
    {
        /** @var ClassStudioDate $ClassStudioDate */
        $ClassStudioDate = self::find($classStudioDateId);
        if(!$ClassStudioDate){
            return [];
        }

        $query = self::where('CompanyNum', $ClassStudioDate->CompanyNum)
            ->where('GroupNumber', $ClassStudioDate->GroupNumber)
            ->whereIn('Status', [self::STATUS_ACTIVE, self::STATUS_COMPLETED])
            ->where('StartDate', '>=', $ClassStudioDate->StartDate);

        switch ($repeatType) {
            case 'single':
                $query = $query->where('id', '=', $classStudioDateId);
                break;
            case 'dates':
                $repeatVal = json_decode($repeatVal);
                $query = $query->where('StartDate', '>=', $repeatVal->since)
                    ->where('EndDate', '<=', $repeatVal->until);
                break;
            case 'quantity':
                if (is_numeric($repeatVal)) {
                    $query = $query->limit((int)$repeatVal);
                } else {
                    return ['status' => 0, 'message' => 'invalid repeat quantity']; //todo
                }
                break;
        }

        return $query->get();
    }

    /**
     * The function return is exist meeting after canceled in series, if $repeatType == dates, check after end_date choose
     * @param $classStudioDateId
     * @param $repeatType
     * @param null $repeatVal
     * @return bool
     */
    public static function getIsExistMeetingAfterCanceled($classStudioDateId, $repeatType, $repeatVal = null): bool
    {
        /** @var ClassStudioDate $ClassStudioDate */
        $ClassStudioDate = self::find($classStudioDateId);
        if(!$ClassStudioDate){
            return 0;
        }

        $query = self::where('CompanyNum', $ClassStudioDate->CompanyNum)
            ->where('GroupNumber', $ClassStudioDate->GroupNumber)
            ->whereIn('Status', [self::STATUS_ACTIVE, self::STATUS_COMPLETED])
            ->where('meetingStatus', '!=', MeetingStatus::COMPLETED)
            ->where('StartDate', '>=', date('Y-m-d'))
            ->where('StartDate', '>=', $ClassStudioDate->StartDate);
        if($repeatType == 'dates') {
            $repeatVal = json_decode($repeatVal);
            $query = $query->where('StartDate', '>', $repeatVal->until);
        }
        return $query->exists();
    }

    public static function isStudioHasClasses($company_num) {
        $res = DB::table(self::getTable())
            ->where('CompanyNum', $company_num)
            ->whereNull('meetingTemplateId')
            ->first();

        return !empty($res);
    }

    /**
     * The if exist number meeting max of this meeting, return array of dates exist max meeting
     * @param array $dateArr
     * @param string $meetingTemplateId
     * @param bool $isUpdateMeeting
     * @return array|null
     */
    public static function isExistMaxOfMeeting(array $dateArr, string $meetingTemplateId, bool $isUpdateMeeting): ?array
    {
        $res = [];
        $SessionsLimitMeeting = MeetingTemplates::getSessionsLimit($meetingTemplateId);
        foreach ($dateArr as $date) {
            // get all availability times in day of meeting ($date)
            /** @var MeetingStaffRuleAvailability[] $timesAvailabilityCoach */
            $numberActiveMeetingsInDate = self::where('meetingTemplateId', $meetingTemplateId)
                ->where('StartDate', $date)
                ->where('Status', '!=', self::STATUS_CANCELLED)//active
                ->whereNotIn('meetingStatus',[MeetingStatus::CANCELED,MeetingStatus::DIDNT_ATTEND])
                ->count();
            if($isUpdateMeeting){ // if is update, less one for this meeting
                --$numberActiveMeetingsInDate;
            }
            if($SessionsLimitMeeting && $SessionsLimitMeeting > 0 && $numberActiveMeetingsInDate >= $SessionsLimitMeeting){
                $res[] = $date;
            }
        }
        return $res;
    }


    /**
     * Returns all classes on a certain day that have not been canceled
     * @param int $CompanyNum
     * @param string $StartDate
     * @return ClassStudioDate[]
     */
    public static function geAllActiveLessonInDate(int $CompanyNum, string $StartDate): array
    {
        return self::where('CompanyNum', $CompanyNum)
            ->where('StartDate', $StartDate)
            ->whereNull('meetingTemplateId')
            ->where('Status', '!=', self::STATUS_CANCELLED)
            ->orderBy('start_date')
            ->get();
    }

    /**
     * @return string
     */
    public function getBrandName(): string
    {
        return Brand::getBranchName($this->CompanyNum, $this->Brands);
    }

    /**
     * @return string
     */
    public function getFloorName(): string
    {
        return Section::getSectionName($this->CompanyNum, $this->Floor);
    }

    /**
     * is frontal Class -> not zoom or online
     * @return bool
     */
    public function isFrontalClass(): bool
    {
        return !($this->is_zoom_class == 1 || $this->onlineClassId);
    }

    public static function isClassSeriesEnded($companyNum, $groupNUmber, $date) {
        $isEndOfSeries = ClassStudioDate::where('CompanyNum', $companyNum)
            ->where('GroupNumber', $groupNUmber)
            ->where('StartDate', '>=', $date)
            ->where('Status', 0)
            ->first();

        return empty($isEndOfSeries);
    }

}
