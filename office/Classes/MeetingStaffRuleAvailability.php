<?php

require_once __DIR__ . '/MeetingStaffDateAvailability.php';

/**
* @property $id
* @property $UserId
* @property $Status
* @property $RepeatStatus
* @property $Day
* @property $EndPeriodicDate
* @property $StartTime
* @property $EndTime
 *
 * Class MeetingStaffRuleAvailability
 */

class MeetingStaffRuleAvailability extends \Hazzard\Database\Model
{
    public const REPEAT_STATUS_ACTIVE = '1';
    public const NUMBER_OF_REPETITIONS = 30;
    public const REPEAT_STATUS_WEEKLY = '1';
    public const STATUS_ACTIVE = '1';

    private const STAFF_DATE_TABLE = 'boostapp.meeting_staff_date_availability';

    protected $table = 'boostapp.meeting_staff_rule_availability';

    /**
     * @var
     */
    private $_dateAvailability ;
    /**
     * @var mixed
     */

    /**
     * @return array
     */
    public function dateAvailability(): array
    {
        if (empty($this->_dateAvailability)) {
            $this->setDateAvailability();
        }
        return $this->_dateAvailability;
    }

    /**
     *  set ClassType
     */
    public function setDateAvailability($id = null): void
    {
        if($id) {
            $this->_dateAvailability = array(MeetingStaffDateAvailability::find($id));
        } else {
            $this->_dateAvailability = (new MeetingStaffDateAvailability())->getAllByMeetingStaffRuleId($this->id);
        }
    }

    public function getLastDate()
    {
        return (new MeetingStaffDateAvailability())->geLastDateByMeetingStaffRuleId($this->id);
    }

    public function getAmountActive()
    {
        return (new MeetingStaffDateAvailability())->geAmountActiveDateByMeetingStaffRuleId($this->id);
    }

    public function getAllRepeatAvailabilityRule() {
        $query = self::where('Status', '=', self::REPEAT_STATUS_ACTIVE)
            ->where('RepeatStatus', '=', self::REPEAT_STATUS_WEEKLY)
            ->where(function ($query) {
                $query->where('EndPeriodicDate', '>',  date('Y-m-d'))
                    ->orWhereNull('EndPeriodicDate');
            });
        return $query->get();
    }

    //Returns an array of all information about the coach's availability within the date range received
    public function getCoachWeekAvailabilityArray($userId, $startDate, $endDate){
        return DB::table($this->table)
            ->leftJoin('meeting_staff_date_availability', $this->table.'.id', '=','meeting_staff_date_availability.RuleAvailabilityId')
            ->where('UserId', $userId)
            ->where('meeting_staff_date_availability.Status','=','1')
            ->whereBetween('meeting_staff_date_availability.Date', array($startDate, $endDate))
            ->orderBy('meeting_staff_date_availability.Date', 'ASC')
            ->orderBy('StartTime', 'ASC')
            ->select('Day','StartTime','EndTime','meeting_staff_date_availability.id', 'meeting_staff_date_availability.RuleAvailabilityId')
            ->get();
    }

    /**
     *  Returns an array of Obj all information about the coach's availability within the date range received
     * @param $userId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public static function getCoachWeekAvailability($userId, $startDate, $endDate){
        return self::leftJoin('meeting_staff_date_availability', self::getTable().'.id', '=','meeting_staff_date_availability.RuleAvailabilityId')
            ->where('UserId', $userId)
            ->where('meeting_staff_date_availability.Status','=','1')
            ->whereBetween('meeting_staff_date_availability.Date', array($startDate, $endDate))
            ->orderBy('meeting_staff_date_availability.Date', 'ASC')
            ->orderBy('StartTime', 'ASC')
            ->select('Day','StartTime','EndTime','meeting_staff_date_availability.id', 'meeting_staff_date_availability.RuleAvailabilityId')
            ->get();
    }


    //Adds an availability date according to the date received, and according to the timeRepeat if set
    public function addNewStaffDateAvailability($date, $timeRepeat=false ) {
        $dateId = null;
        if ($timeRepeat) {
            $maxLoop = $timeRepeat;
        } elseif($this->RepeatStatus == self::REPEAT_STATUS_ACTIVE) {
            $maxLoop = self::NUMBER_OF_REPETITIONS;
        } else {
            $maxLoop = 1;
        }
        for ($i = 0; $i < $maxLoop; $i++) {
            $newDate = date('Y-m-d', strtotime('+'. $i*7 .' day', strtotime($date)));
            if($this->EndPeriodicDate && $newDate > $this->EndPeriodicDate) {
                break;
            }
            $dateAvailability = new  MeetingStaffDateAvailability([
                'RuleAvailabilityId' => $this->id,
                'Date' => $newDate
            ]);
            $dateAvailability->save();
            if($i===0) {
                $dateId = $dateAvailability->id;
            }
        }
        return $dateId;
    }

    /**
     * return array from this class
     * @return array
     */
    public function returnArray(): array
    {
        $arr = array();
        if (!empty($this->_dateAvailability)) {
            $dateAvailabilityArray = $this->createArrayFromObjArr($this->_dateAvailability);
            $arr['dateAvailability'] = $dateAvailabilityArray;
        }
        $arr['ruleAvailability'] = $this->toArray();
        return $arr;
    }

    /**
     * @param $arr
     * @return array
     * Create from obj array
     */
    private function createArrayFromObjArr($arr): array
    {
        $a = array();
        foreach ($arr as $item){
            $a[] = $item->toArray();
        }
        return $a;
    }

    /**
     * The function check availability coach, return array of dates coach not availability
     * @param array $dateArr
     * @param string $startTime
     * @param string $endTime
     * @param string $coachId
     * @return array
     */
    public static function isAvailabilityCoach(array $dateArr, string $startTime, string $endTime, string $coachId): array //todo fix if next day
    {
        $res = [];
        foreach ($dateArr as $date) {
            // get all availability times in day of meeting ($date)
            /** @var MeetingStaffRuleAvailability[] $timesAvailabilityCoach */
            $timesAvailabilityCoach = self::leftJoin(self::STAFF_DATE_TABLE, self::getTable() . ".id", '=', self::STAFF_DATE_TABLE.".RuleAvailabilityId")
                ->where(self::getTable() . '.UserId', $coachId)
                ->where(self::STAFF_DATE_TABLE.'.Date', $date)
                ->where(self::STAFF_DATE_TABLE . '.Status', self::STATUS_ACTIVE)
                ->where(self::getTable() . '.Status', self::STATUS_ACTIVE)
                ->orderBy(self::getTable() . '.StartTime', 'ASC')
                ->get();
            $isAvailability = self::isAvailabilityInTime($timesAvailabilityCoach, $startTime, $endTime);
            if(!$isAvailability){
                $res[] = $date;
            }
        }
        return $res;
    }

    /**
     * The function check if coach availability in Time, get MeetingStaffRuleAvailability and time, return true / false
     * @param MeetingStaffRuleAvailability[] $timesAvailabilityCoach
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    private static function isAvailabilityInTime(array $timesAvailabilityCoach, string $startTime, string $endTime): bool
    {
        try {
            for($i = 0, $iMax = count($timesAvailabilityCoach); $i < $iMax; $i++){
                // if start time meeting before start time in this availability, return false
                if($timesAvailabilityCoach[$i]->StartTime > $startTime){
                    return false;
                }
                // if end time meeting before end time in this availability, return true
                if($timesAvailabilityCoach[$i]->EndTime >= $endTime){
                    return true;
                }
                // if end time meeting after end time in this availability, check with other availability in a row
                $j = $i + 1;
                while ($j < count($timesAvailabilityCoach)){
                    if($timesAvailabilityCoach[$j - 1]->EndTime < $timesAvailabilityCoach[$j]->StartTime) {
                        break;
                    }
                    if ($timesAvailabilityCoach[$i]->StartTime <= $startTime && $timesAvailabilityCoach[$j]->EndTime >= $endTime) {
                        return true;
                    }
                    $j++;
                }
            }
            return false;
        } catch (Exception $e) {
            //todo-add-log
            return false;
        }
    }


        public static $updateRules =[
            'Status' => 'integer|between:0,1',
            'RepeatStatus' => 'integer|between:0,1',
            'EndPeriodicDate' => 'date',
//            'StartTime' => 'date_format:H:i',
//            'EndTime' => 'date_format:H:i',
        ];

    public static $CreateRules =[
        'id' => 'integer',
        'UserId' => 'required|exists:boostapp.users,id|integer',
        'Status' => 'integer|between:0,1',
        'RepeatStatus' => 'required|integer|between:0,1',
        'Day' => 'required|integer|between:0,6',
        'EndPeriodicDate' => 'date',
        'StartTime' => 'required|date_format:H:i',
        'EndTime' => 'required|date_format:H:i',
    ];

}