<?php

require_once __DIR__ . '/MeetingTemplatesCoaches.php';
require_once __DIR__ . '/MeetingTemplatesCalendars.php';
require_once __DIR__ . '/ClassesType.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $Status
 * @property $TemplateName
 * @property $CategoryId
 * @property $ColorId
 * @property $ExternalRegistration
 * @property $RegistrationLimitedTo
 * @property $SessionsLimit
 * @property $MeetingType
 * @property $OnlineSendType
 * @property $LiveClassLink
 * @property $OnlineReminderType
 * @property $OnlineReminderValue
 * @property $ZoomMeetingNumber
 * @property $ZoomMeetingPassword
 * @property $PreparationTimeStatus
 * @property $PreparationTimeType
 * @property $PreparationTimeValue
 * @property $MoreInfoText
 * @property $PageLink
 * @property $Devices
 * @property $AllCoaches
 * @property $AllCalendars
 * @property $CreatedDate
 * @property $EditDate
 *
 * Class MeetingTemplates
 */
class MeetingTemplates extends \Hazzard\Database\Model
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_OFF = 0;

    const MAX_WORD_LENGTH = 100;
    const MAX_LONG_TEXT = 6500;
//    const URL_REGEX = "((https?|ftp)\:\/\/)?" . "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"
//    . "([a-z0-9-.]*)\.([a-z]{2,3})" . "(\:[0-9]{2,5})?" . "(\/([a-z0-9+\$_-]\.?)+)*\/?"
//    ."(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?" ."(#[a-z_.-][a-z0-9+\$_.-]*)?";
    const MAX_ZOOM_PASSWORD = 45;
    const MEETING_TYPE_ONLINE = '1', MEETING_TYPE_ZOOM = '2';

    const PreparationTimeType = [
        '0' => 'minutes',
        '1' => 'hours',
    ];

    /**
     * @var string
     */
    protected $table = 'boostapp.meeting_templates';
    /**
     * @var
     */
    private $_classesType;
    /**
     * @var
     */
    private $_coaches;
    /**
     * @var
     */
    private $_calendars;

    /**
     * @param $companyNum
     * @return array
     */
    public function getAllTemplatesByCompany($companyNum)
    {
        $templates = self::where('CompanyNum', '=', $companyNum)
            ->where('Status', '<>', '0')
            ->orderBy('Status', 'ASC')
            ->get();
        return $templates;
    }

    public function getActiveTemplatesByCompany($companyNum)
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where('Status', 1)
            ->get();
    }

    /**
     * @param $categoryId
     * @return array
     */
    public function getAllTemplateByCategory($categoryId)
    {
        return DB::table($this->table)
            ->select('id')
            ->where('CategoryId', '=', $categoryId)
            ->where('Status', '<>', '0')
            ->get();
    }

    /**
     * @return ClassesType[]
     */
    public function classesType(): array
    {
        if (empty($this->_classesType)) {
            $this->setClassesType();
        }
        return $this->_classesType;
    }

    /**
     * @return array
     */
    public function coaches(): array
    {
        if (empty($this->_coaches)) {
            $this->setCoaches();
        }
        return $this->_coaches;
    }

    /**
     * @return array
     */
    public function calendars(): array
    {
        if (empty($this->_calendars)) {
            $this->setCalendars();
        }
        return $this->_calendars;
    }

    /**
     *  set ClassType
     */
    public function setClassesType(): void
    {
        $this->_classesType = ClassesType::getAllByMeetingTemplateId($this->id);
    }

    /**
     *  set Coaches
     */
    public function setCoaches($coachIdArray = null): void
    {
        if (!$coachIdArray) {
            $this->_coaches = MeetingTemplatesCoaches::getAllByMeetingTemplateId($this->id);
        }
    }

    /**
     *  set Calendars
     */
    public function setCalendars($calendarIdArray = null): void
    {
        if (!$calendarIdArray) {
            $this->_calendars = (new MeetingTemplatesCalendars())->getAllByMeetingTemplateId($this->id);
        }
    }

    /**
     *  GET CalendarsOnlyId
     */
    public function getCalendarsOnlyId()
    {
        if ($this->AllCalendars != 1) {
            $calendars = (new MeetingTemplatesCalendars())->getAllByMeetingTemplateId($this->id);
            $a = array();
            foreach ($calendars as $item) {
                $a[] = $item->CalendarId;
            }
            return $a;
        }
        return [];
    }

    public function removeCoachfromDb()
    {
        if (is_array($this->coaches())) {
            foreach ($this->coaches() as $coach) {
                $coach->delete();
            }
        }
    }//todo-remove

    public function removeCalendarfromDb()
    {
        if (is_array($this->calendars())) {
            foreach ($this->calendars() as $calendar) {
                $calendar->delete();
            }
        }
    }//todo-remove

    /**
     * return array from this class
     * @return array
     */
    public function returnArray(): array
    {
        $a = array();
        if (!empty($this->_classesType)) {
            $classesTypeArray = $this->createArrayFromObjArr($this->_classesType);
            $a['durationPrice'] = $classesTypeArray;
        }
        if (!empty($this->_coaches)) {
            $coachesArray = $this->createArrayFromObjArr($this->_coaches);
            $a['coaches'] = $coachesArray;
        }
        if (!empty($this->_calendars)) {
            $calendarsArray = $this->createArrayFromObjArr($this->_calendars);
            $a['calendars'] = $calendarsArray;
        }
        $a['templateData'] = $this->toArray();
        return $a;
    }

    /**
     * return array from this class
     * @return array
     */
    public function returnTemplateDisplayArray(): array
    {
        $a = $this->toArray();
        $durationArray = array();
        if (!empty($this->_classesType)) {
            foreach ($this->_classesType as $classType) {
                $factor = $classType->durationType == '1' ? 60 : 1;
                $durationArray[] = $classType->duration * $factor;
            }
        }
        $a['duration'] = $durationArray;
        return $a;
    }

    /**
     * Return array from data ideal to new template
     * @return array
     */
    public function newMeetingTemplateDisplay(): array
    {
        $a = $this->toArray();
        $durationArray = array();
        if (!empty($this->_classesType)) {
            foreach ($this->_classesType as $classType) {
                $factor = $classType->durationType == '1' ? 60 : 1;
                $volumesArray[] = [
                    'duration' => $classType->duration * $factor,
                    'price' => $classType->Price,
                    'toString' => $classType->Type,
                    'id' => $classType->id
                ];
            }
        }
        $a['volumes'] = $volumesArray ?? null;
        if (!empty($this->_coaches)) {
            foreach ($this->_coaches as $coach) {
                $coachesArray[] = $coach->CoachId;
            }
            $a['coachIds'] = $coachesArray;
        }
        if (!empty($this->_calendars)) {
            foreach ($this->_calendars as $calendar) {
                $calendarsArray[] = $calendar->CalendarId;
            }
            $a['calendarIds'] = $calendarsArray;
        }
        return $a;
    }

    /**
     * @param $arr
     * @return array
     * Create from obj array
     */
    private function createArrayFromObjArr($arr): array
    {
        $a = array();
        foreach ($arr as $item) {
            $a[] = $item->toArray();
        }
        return $a;
    }

    /**
     * @throws Exception
     */
    public function addTemplateCalendarToDb($calendar): void
    {
        $meetingTemplateCalendar = new MeetingTemplatesCalendars($calendar);
        $validator = Validator::make($meetingTemplateCalendar->getAttributes(), $meetingTemplateCalendar::$createRules);
        if ($validator->passes()) {
            $meetingTemplateCalendar->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @throws Exception
     */
    public function addTemplateClassesTypeToDb($classTypeData): void
    {
        $classType = new classesType($classTypeData);
        $validator = Validator::make($classType->getAttributes(), $classType::$createRules);
        if ($validator->passes()) {
            $fullDateNow = date('Y-m-d H:i:s');
            $classType->CreatedDate = $fullDateNow;
            $classType->EditDate = $fullDateNow;
            $classType->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @throws Exception
     */
    public function addTemplateCoachToDb($coach): void
    {
        $meetingTemplateCoach = new MeetingTemplatesCoaches($coach);
        $validator = Validator::make($meetingTemplateCoach->getAttributes(), $meetingTemplateCoach::$createRules);
        if ($validator->passes()) {
            $meetingTemplateCoach->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @throws Exception
     */
    public function createNewTemplate($data): void
    {
        foreach ($data as $key => $value) {
            if ($value !== '') {
                $this->$key = $value;
            }
        }
        $validator = Validator::make($this->getAttributes(), self::$createRules);
        if ($validator->passes()) {
            $date = date('Y-m-d H:i:s');
            $this->CreatedDate = $date;
            $this->EditDate = $date;
            $this->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    public static $createRules = [
        'id' => 'integer',
        'CompanyNum' => 'required|integer',
        'Status' => 'integer|between:0,2',
        'TemplateName' => 'required|min:1|max:' . self::MAX_WORD_LENGTH,
        'CategoryId' => 'required|integer',
        'ColorId' => 'required|integer',
        'ExternalRegistration' => 'required|integer|between:0,1',
        'RegistrationLimitedTo' => 'required_if:ExternalRegistration,1|integer|between:0,2',
        'SessionsLimit' => 'required|integer|between:0,100',
        'MeetingType' => 'required|integer|between:0,2',
        'OnlineSendType' => 'required_if:MeetingType,' . self::MEETING_TYPE_ONLINE . '|integer|between:1,3',
        'LiveClassLink' => 'required_if:MeetingType,' . self::MEETING_TYPE_ONLINE,
        'OnlineReminderType' => 'required_if:MeetingType,' . self::MEETING_TYPE_ONLINE . '|integer|between:0,2',
        'OnlineReminderValue' => 'required_if:MeetingType,' . self::MEETING_TYPE_ONLINE . '|numeric|between:0,999999',
        'ZoomMeetingNumber' => 'required_if:MeetingType,' . self::MEETING_TYPE_ZOOM . '|integer|digits_between:1,100',
        'ZoomMeetingPassword' => 'required_if:MeetingType,' . self::MEETING_TYPE_ZOOM . 'min:1|max:' . self::MAX_ZOOM_PASSWORD,
        'PreparationTimeStatus' => 'required|integer|between:0,2',
        'PreparationTimeType' => 'required_if:PreparationTimeStatus,in:[1,2]|integer|between:0,1',
        'PreparationTimeValue' => 'required_if:PreparationTimeStatus,in:[1,2]|numeric|between:0,999999',
        'MoreInfoText' => 'max:' . self::MAX_LONG_TEXT,
        'AllCoaches' => 'required|integer|between:0,1',
        'AllCalendars' => 'required|integer|between:0,1',
        'TagsId' => 'required|integer',
    ];

    public static function getTagByPreviousTemplateName($templateName, $companyNum)
    {
        $words = explode(' ', $templateName);

        foreach ($words as $word) {

            if (empty($word))
                continue;

            $tag = self::select('TagsId')
                ->where('CompanyNum', $companyNum)
                ->where('TemplateName', 'LIKE', '%' . $word . '%')
                ->first();

            if (!empty($tag->TagsId))
                return $tag->TagsId;
        }

        return null;
    }

    public static function getTagByPreviousTemplateType($templateTypeId, $companyNum)
    {
        $tag = self::select('TagsId')
            ->where('CompanyNum', $companyNum)
            ->where('CategoryId', $templateTypeId)
            ->first();

        return !empty($tag->TagsId) ? $tag->TagsId : null;
    }

    public static function getSessionsLimit($id)
    {
        return self::where('id', $id)
            ->pluck('SessionsLimit');
    }
}