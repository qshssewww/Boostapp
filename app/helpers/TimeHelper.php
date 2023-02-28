<?php

/**
 * Class TimeHelper
 */
class TimeHelper
{
    const TYPE_SECOND = -1;
    const TYPE_MINUTE = 0;
    const TYPE_HOUR = 1;
    const TYPE_DAY = 2;
    const TYPE_WEEK = 3;
    const TYPE_MONTH = 4;
    const TYPE_YEAR = 5;

    /**
     * @param int $timeValue
     * @param int $timeType
     * @param int $factorTimeType -> if type minute =1 need get factorTimeType=-1
     * @return int
     */
    public static function returnMinuteTime(int $timeValue, int $timeType, int $factorTimeType = 0): int
    {
        switch ($timeType + $factorTimeType) {
            case self::TYPE_SECOND:
                return intdiv($timeValue, 60);
            case self::TYPE_MINUTE:
                return (int)$timeValue;
            case self::TYPE_HOUR:
                return (int)$timeValue * 60;
            case self::TYPE_DAY:
                return (int)$timeValue * 24 * 60;
            case self::TYPE_WEEK:
                return (int)$timeValue * 7 * 24 * 60;
            case self::TYPE_MONTH:
                return (int)$timeValue * 30 * 24 * 60;
            default:
                // invalid time type
                return 0;
        }
    }

    /**
     * @param int $timeValue
     * @param int $timeType
     * @param int $factorTimeType
     * @return string
     */
    public static function convertMinutesToHumanFriendlyString(int $timeValue, int $timeType, int $factorTimeType = 0): string
    {
        $duration = self::returnMinuteTime($timeValue, $timeType, $factorTimeType);
        // get the time string
        $hours = intdiv($duration, 60);
        $minutes = $duration % 60;
        $units = lang($hours === 0 ? "shortening_minute" : "shortening_hours");

        $time = "";
        if ($hours > 0) {
            $time .= $hours . ($minutes > 0 ? ":" : "");}
        if ($minutes > 0) {
            $time .= ($minutes < 10 && $hours > 0 ? "0" : "") . $minutes;}
        $time .=  ' ' . $units;
        return $time;
    }

    /**
     * @param string $start
     * @param string $end
     * @param int $type
     * @return int
     */
    public static function returnDurationFromTimes(string $start, string $end, int $type=self::TYPE_MINUTE):int
    {
        //or get Date difference as total difference
        $d1 = strtotime($start);
        $d2 = strtotime($end);
        $totalSecondsDiff = abs($d1-$d2);
        switch ($type){
            case self::TYPE_SECOND :
                return $totalSecondsDiff;
            case self::TYPE_MINUTE:
                return $totalSecondsDiff/60;
            case self::TYPE_HOUR:
                return $totalSecondsDiff/60/60;
            case self::TYPE_DAY:
                return $totalSecondsDiff/60/60/24;
            case self::TYPE_WEEK:
                return $totalSecondsDiff/60/60/24/30;
            case self::TYPE_MONTH:
                return $totalSecondsDiff/60/60/24/365;
        }
    }

    /**
     * @param int $timeSec
     * @param int $minuteInterval
     * @return int
     */
    public static function roundUpToMinuteInterval(int $timeSec, int $minuteInterval = 15): int
    {
        if($minuteInterval === 0 ) {
            return $timeSec;
        }
        return (int) (ceil($timeSec / (60*$minuteInterval))) * (60*$minuteInterval);
    }

    /**
     * @param int $timeSec
     * @param int $minuteInterval
     * @return int
     */
    public static function roundDownToMinuteInterval(int $timeSec, int $minuteInterval = 15): int
    {
        if($minuteInterval === 0 ) {
            return $timeSec;
        }
        return (int) (floor($timeSec / (60*$minuteInterval))) * (60*$minuteInterval);
    }

    /**
     * @param $start_time1
     * @param $end_time1
     * @param $start_time2
     * @param $end_time2
     * @return bool
     */
    public static function isCoveringTimeRange($start_time1, $end_time1, $start_time2, $end_time2): bool
    {
        return ($start_time1 <= $end_time2 && $start_time2 <= $end_time1);
    }

    /**
     * @param int $timeValue
     * @param int $timeType
     * @param int $factorTimeType
     * @return string
     */
    public static function convertToHumanFriendlyString(int $timeValue, int $timeType, int $factorTimeType = 0): string
    {
        switch ($timeType + $factorTimeType){
            case self::TYPE_SECOND :
                if($timeValue === 1) {
                    return $timeValue . " " .lang('second_lower');
                }
                return $timeValue . " " .lang('seconds_lower');
            case self::TYPE_MINUTE:
                if($timeValue === 1) {
                    return lang('minute_short_cal');
                }
                return $timeValue . " " .lang('minutes_short_cal');
            case self::TYPE_HOUR:
                if($timeValue === 1) {
                   return lang('hour');
                }
                if($timeValue === 2) {
                    return lang('two_hours');
                }
                return $timeValue . " " .lang('cal_template_hours');
            case self::TYPE_DAY:
                if($timeValue === 1) {
                    return lang('day');
                }
                if($timeValue === 2) {
                    return lang('two_days');
                }
                return $timeValue . " " .lang('days');
            case self::TYPE_WEEK:
                if($timeValue === 1) {
                    return lang('week');
                }
                if($timeValue === 2) {
                    return lang('two_weeks');
                }
                return $timeValue . " " .lang('weeks');
            case self::TYPE_MONTH:
                if($timeValue === 1) {
                    return lang('month');
                }
                if($timeValue === 2) {
                    return lang('two_months');
                }
                return $timeValue . " " .lang('months');
            case self::TYPE_YEAR:
                if($timeValue === 1) {
                    return lang('year_signinclock');
                }
                if($timeValue === 2) {
                    return lang('two_years');
                }
                return $timeValue . " " .lang('years');
            default:
                return $timeValue . " " .lang('minutes_short_cal');
        }
    }

    /**
     * @param array $rangeArray
     * @param string $time
     * @return bool
     */
    public static function isInSideArrayRange(array $rangeArray, string $time): bool
    {
        $myTime = strtotime($time);
        $start = strtotime($rangeArray[0]);
        $end = strtotime($rangeArray[1]);
        //If there is an error in DB and the inverse range
        if ($start > $end) {
            return $myTime >= $end && $myTime <= $start;
        }
        if($myTime >= $start && $myTime <= $end) {
            return true;
        }
        return false;
    }

    /**
     * Starts From 0
     * @param int $num
     * @return string
     */
    public static function numberToHebDay(int $num): string
    {
        switch ($num) {
            case 0:
                return 'ראשון';
            case 1:
                return 'שני';
            case 2:
                return 'שלישי';
            case 3:
                return 'רביעי';
            case 4:
                return 'חמישי';
            case 5:
                return 'שישי';
            case 6:
                return 'שבת';
            default:
                return '';
        }
    }

    /**
     * Starts From 0
     * @param string $englishDayName
     * @return string
     */
    public static function getHebrewDayName(string $englishDayName): string {
        switch ($englishDayName) {
            case 'Sun': return "ראשון";
            case 'Mon': return "שני";
            case 'Tue': return "שלישי";
            case 'Wed': return "רביעי";
            case 'Thu': return "חמישי";
            case 'Fri': return "שישי";
            case 'Sat': return "שבת";
            default: return "לא אותר יום";
        }
    }


    /**
     * getHebrewMonthByNumber function
     * @param int $month
     * @return string|null
     */
    public static function getHebrewMonthByNumber(int $month):?string{
        if(!empty($month) && is_numeric($month)){
            switch ($month) {
                case 1: return "ינואר";
                case 2: return "פברואר";
                case 3: return "מרץ";
                case 4: return "אפריל";
                case 5: return "מאי";
                case 6: return "יוני";
                case 7: return "יולי";
                case 8: return "אוגוסט";
                case 9: return "ספטמבר";
                case 10: return "אוקטובר";
                case 11: return "נובמבר";
                case 12: return "דצמבר";
                default: return null;
            }        
        } else return NULL;
    }

    /** GetDayByNum function
     * @param int $day
     * @return string|null
     */
    public static function GetDayByNum(int $day):?string{
        if(!empty($day) && is_numeric($day)){
            switch ($day) {
                case 1: return "Sunday";
                case 2: return "Monday";
                case 3: return "Tuesday";
                case 4: return "Wednesday";
                case 5: return "Thursday";
                case 6: return "Friday";
                case 7: return "Saturday";
                default: return null;
            }
        } else return NULL;
    }
}
