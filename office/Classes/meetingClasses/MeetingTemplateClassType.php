<?php
require_once __DIR__ . '/../ClassesType.php';
require_once __DIR__ . '/../MeetingTemplates.php';
require_once __DIR__ . '/../MeetingCategories.php';
require_once __DIR__ . '/../MeetingTemplatesCoaches.php';
require_once __DIR__ . '/../Users.php';

/**
 * @property $meetingTemplate_id
 * @property $meetingTemplate_name //TemplateName
 * @property $meetingTemplate_type //MeetingType - online zoom or normal
 * @property $companyNum
 * @property $meetingTemplate_allCoaches
 * @property $coachId
 * @property $meetingTemplate_allCalendars
 * @property $calendar
 * @property $classType_id
 * @property $classType_name
 * @property $classType_duration
 * @property $classType_durationType
 * @property $classType_price
 * @property $classType_favorite
 * @property $meetingCategory_name
 * @property $meetingCategory_id
 * @property $meetingCategory_favorite
 *
 * Class MeetingTemplateClassType
 */
class MeetingTemplateClassType extends \Hazzard\Database\Model
{
    private const CLASSES_TYPE_TABLE = 'boostapp.class_type';
    private const MEETING_CALENDARS_TABLE = 'boostapp.meeting_template_calendars';
    private const MEETING_COACHES_TABLE = 'boostapp.meeting_template_coaches';
    private const MEETING_CATEGORY_TABLE = 'boostapp.meeting_category';
    protected $table = "boostapp.meeting_templates";

    /**
     * @param int $companyNum
     * @return MeetingTemplateClassType[]
     */
    public static function getMeetingToCart(int $companyNum): array
    {
        return self::select(
            self::getTable() . ".id as meetingTemplate_id",
            self::getTable() . ".TemplateName as meetingTemplate_name",
            self::getTable() . ".MeetingType as meetingTemplate_type",
            self::getTable() . ".CompanyNum as companyNum",
            self::getTable() . ".AllCoaches as meetingTemplate_allCoaches",
            self::getTable() . ".AllCalendars as meetingTemplate_allCalendars",
            self::CLASSES_TYPE_TABLE . ".id as classType_id",
            self::CLASSES_TYPE_TABLE . ".Type as classType_name",
            self::CLASSES_TYPE_TABLE . ".duration as classType_duration",
            self::CLASSES_TYPE_TABLE . ".durationType as classType_durationType",
            self::CLASSES_TYPE_TABLE . ".price as classType_price",
            self::CLASSES_TYPE_TABLE . ".Favorite as classType_favorite",
            self::MEETING_CATEGORY_TABLE . ".id as meetingCategory_id",
            self::MEETING_CATEGORY_TABLE . ".Favorite as meetingCategory_favorite",
            self::MEETING_CATEGORY_TABLE . ".CategoryName as meetingCategory_name")
            ->leftJoin(self::MEETING_CATEGORY_TABLE ,self::MEETING_CATEGORY_TABLE . ".id", '=', self::getTable().".CategoryId")
            ->leftJoin(self::CLASSES_TYPE_TABLE ,self::CLASSES_TYPE_TABLE . ".MeetingTemplateId", '=', self::getTable().".id")
            ->where(self::getTable() . ".CompanyNum","=",$companyNum)
            ->where(self::getTable() . ".Status","=",MeetingTemplates::STATUS_ACTIVE)
            ->where(self::CLASSES_TYPE_TABLE . ".Status","=", ClassesType::STATUS_ACTIVE)
            ->get();
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->meetingTemplate_name . ' | ' .
            TimeHelper::convertMinutesToHumanFriendlyString($this->classType_duration, $this->classType_durationType)  ;
    }

    /**
     * First choice priority is the user who invites (if he is a coach and suitable for the meeting)
     * If not choose another coach that is suitable for the meeting
     * return userId OR 0 if No matter which coach
     * @param array $coachIdsArray
     * @return int
     */
    public function getDefaultCoachId(array $coachIdsArray = []): int
    {
        $userId = Auth::user()->id ?? 0;
        $userIsCoach = isset(Auth::user()->Coach) && (int)Auth::user()->Coach === 1;
        if((int)$this->meetingTemplate_allCoaches === 1) {
            $coachId = $userIsCoach ? Auth::user()->id : 0;
            if(empty($coachIdsArray) || in_array($coachId, $coachIdsArray,true)){
                return $coachId;
            }
        }
        $MeetingTemplatesCoach = MeetingTemplatesCoaches::getByTemplateIdAndUserId($this->meetingTemplate_id, $userId);
        if($MeetingTemplatesCoach) {
            $coachId = $userId;
        } else {
            $coachId = MeetingTemplatesCoaches::getFirstCoachToMeeting($this->meetingTemplate_id) ?? 0 ;
        }
        if(empty($coachIdsArray) || in_array($coachId, $coachIdsArray,true)){
            return $coachId;
        }
        return $coachIdsArray[0];
    }

    /**
     * @param array $diariesIdsArray
     * @return int
     */
    public function getDefaultCalendar(array $diariesIdsArray=[]): int
    {

        $calendarId = (int)$this->meetingTemplate_allCalendars === 1 ? 0 :
            MeetingTemplatesCalendars::getFirstCalendarToMeeting($this->meetingTemplate_id) ?? 0;
        if(empty($diariesIdsArray) || in_array($calendarId, $diariesIdsArray,true)){
            return $calendarId;
        }
        return $diariesIdsArray[0];

    }
}
