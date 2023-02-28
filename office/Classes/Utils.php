<?php


class Utils
{
    const ERROR_STATUS = 0;
    const SUCCESS_STATUS = 1;
    const WARNING_STATUS = 2;

    /**
     * @var string[] Array of country codes available in the system
     */
    public static $systemPrefix = ['972', '1', '91', '44'];

    public function createArrayFromObj($item = null ,$null = false){
        if ($item == null){
            $item = $this;
        }
        $itemArr = array();
        foreach ($item as $key => $value){
            if($null == false) {
                if ($value !== null && $key != "table") {
                    if (is_object($value)) {
                        $arr = array();
                        foreach ($value as $ind => $val) {
                            $arr[$ind] = $val;
                        }
                        $itemArr[$key] = $arr;
                    } else {
                        $itemArr[$key] = $value;
                    }
                }
            }
            else{
                if ($key != "table") {
                    if (is_object($value)) {
                        $arr = array();
                        foreach ($value as $ind => $val) {
                            $arr[$ind] = $val;
                        }
                        $itemArr[$key] = $arr;
                    } else {
                        $itemArr[$key] = $value;
                    }
                }
            }
        }
        return $itemArr;
    }
    public function stdClassToObj($std,$type){
        if ($std == null){
            return null;
        }
        $class = new $type;
        foreach ($std[0] as $key => $value) {
            $class->__set($key, $value);
        }
        return $class;
    }
    public function arrayIntoObject($arr,$type){
        if ($arr == null){
           return null;
        }
        $class = new $type;
        foreach ($arr as $key => $value) {
            $class->__set($key, $value);
        }
        return $class;
    }

    public function convertMembershipDurationToTime($valid,$validType, $payment = null){
        //חיוב קבוע
        if($payment == 2){
            return "per";
        }
        if($valid == 0){
            return 0;
        }
        if ($validType == 1) {
            return $valid . " days";
        } else if ($validType == 2) {
            return $valid . " weeks";
        } else if ($validType == 3) {
            return $valid . " months";
        }
        return null;
    }

    public function createArrayFromObjArr($arr,$null = false){
        $a = array();
        foreach ($arr as $item){
            array_push($a,$this->createArrayFromObj($item,$null));
        }
        return $a;
    }
    public function convertArrayIntoObjectArray($obj,$type){
        $a = array();
        foreach ($obj as $item){
            array_push($a,$this->arrayIntoObject($item,$type));
        }
        return $a;
    }

    public function curl($url,$query, $method = "post"){
        $post = 1;
        if ($method == "get" || $method == "GET"){
            $post = 0;
        }
        $defaults = array(
            CURLOPT_POST => $post,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POSTFIELDS => http_build_query($query),
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        );
        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $json_response = curl_exec($ch);
        $responseArr = json_decode($json_response, true);
        if (curl_errno($ch)) {
            $curl_error = curl_error($ch);
            //handle error, save api log with error etc.
            curl_close($ch);
            return json_encode(['error' => $curl_error], 400);
        }else{
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200) {
                curl_close($ch);
                return $responseArr;
            }
        }
        curl_close($ch);
        return json_encode(['error' => $responseArr["err"]["message"]], 400);
    }


    public function nextAndPreviousMonthChecking ($cMonth,$cYear ){
        $prev_year= $cYear;
        $next_year = $cYear;
        $prev_month = $cMonth-1;
        $next_month = $cMonth+1;

        if ($prev_month == 0 ) {
            $prev_month = 12;
            $prev_year = $cYear - 1;
        }
        if ($next_month == 13 ) {
            $next_month = 1;
            $next_year = $cYear + 1;
        }

        $StartDate = $cYear.'-'.$cMonth.'-01';
        $EndDate = $next_year.'-'.$next_month.'-01';
        return [$StartDate, $EndDate, $prev_year ];
    }



    public static function getTimeLeftInMembershipTxt($trueDate) {
        if ($trueDate < date('Y-m-d')){
            return lang('subscription_expired');
        }
        $timeLeft = date_diff(date_create($trueDate), date_create(date('Y-m-d',time())));
        if ($timeLeft->y >= 1) {
            if ($timeLeft->m == 0 && $timeLeft->d == 0) {
                return lang('year_signinclock');
            } else {
                return lang('year_signinclock'). ' +';
            }
        } elseif ($timeLeft->m >= 1) {
            if ($timeLeft->d == 0) {
                if ($timeLeft->m > 1) {
                    return "$timeLeft->m ". lang('months');
                } else {
                    return lang('month');
                }
            } else {
                if ($timeLeft->m > 1) {
                    return "$timeLeft->m ". lang('months'). ' +';
                } else {
                    return lang('month'). ' +';
                }
            }
        } elseif ($timeLeft->d >= 1) {
            return "$timeLeft->d ". lang('days');
        } else {
            return '';
        }
    }

    public static function getClientIconInfo($classInfo, $key, $clientSetArray, $ClassStudioActInfo, $id, $CompanyNum) {
        $classInfo['activeTrainers'][$key]['clientActivityInfo'] = $clientSetArray['ClientActivity']->getActiveClientActivityByClientId($id, $CompanyNum);
        //Get client medical info.
        $classInfo['activeTrainers'][$key]['clientMedicalInfo'] = $clientSetArray['ClientMedical']->GetMdicalByClientId($CompanyNum, $id);
        //Get client notice info.
        $classInfo['activeTrainers'][$key]['clientNoticeInfo'] = $clientSetArray['ClientNotice']->GetClientcrmByClientId($CompanyNum, $id);
        //Get client class act history.
        $classInfo['activeTrainers'][$key]['clientFirstClass'] = $ClassStudioActInfo->getIsClientInFirstClass($CompanyNum, $id);
        //Get the client time the left in his membership.
        $classInfo['activeTrainers'][$key]['clientMembershipTimeLeft'] = Utils::getTimeLeftInMembershipTxt($classInfo['activeTrainers'][$key]['clientActivityInfo']->TrueDate);
    }

    public static function hexcode($str) {
        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);
        return $code;
    }

    //time format hh:mm
    public function secToTime ($sec){
        $h = (int)($sec / 3600);
        $totalSec = (int)($sec - ($h * 3600));
        $m = (int)($totalSec / 60);
       return sprintf('%02d:%02d', $h, $m);
    }

    public function timeToSec($time){
        $arr= explode(':', $time);
        return $arr[0] * 3600 + $arr[1] * 60;
    }

    //time format hh:mm
    public function timeCalc($time1,$time2, $operator){
        $time1 = $this->timeToSec($time1);
        $time2 = $this->timeToSec($time2);
        if ($operator == "+"){
            return $this->secToTime($time1+$time2);
        }
        return $this->secToTime($time1-$time2);
    }

    public static function getCurrentAnnualQuarter($currentDate){
        //Get the month number of the date
        $month = date("n", strtotime($currentDate));
        //Divide that month number by 3 and round up
        return ceil($month / 3);
    }

    //Starts From 0
    public static function numberToDay($num){
        $daysArr = [
            lang('sunday'),
            lang('monday'),
            lang('tuesday'),
            lang('wednesday'),
            lang('thursday'),
            lang('friday'),
            lang('saturday')
        ];

        return $daysArr[$num];
    }

    public static function dateToDayName($date){
        return self::numberToDay(date("w", strtotime($date)));
    }

    /**
     * @param $StartDate string Start of date range (Y-m-d)
     * @param $EndDate string End of date range (Y-m-d)
     * @param $interval int|string How many weeks between dates
     * @return array All the dates according to dates and interval
     */
    public static function createDateRangeWeek(string $StartDate, string $EndDate, $interval) :array {
        $dateArr = [];
        $tempDate = $StartDate;
        while ($tempDate < $EndDate) {
            $dateArr[] = $tempDate;
            $tempDate = date('Y-m-d', strtotime("+$interval week", strtotime($tempDate)));
        }
        return $dateArr;
    }

    /**
     * @param $StartDate string Start of date range (Y-m-d)
     * @param $EndDate string End of date range (Y-m-d)
     * @param $interval string Interval between dates (relative datetime php format)
     * @see https://www.php.net/manual/en/datetime.formats.relative.php
     * @return array All the dates according to dates and interval
     */
    public static function createDateRange(string $StartDate, string $EndDate, string $interval): array{
        $dateArr = [];
        $tempDate = $StartDate;
        while ($tempDate <= $EndDate) {
            $dateArr[] = $tempDate;
            $tempDate = date('Y-m-d', strtotime($interval, strtotime($tempDate)));
        }
        return $dateArr;
    }

    /**
     * @param string $StartTime
     * @param string $EndTime
     * @param string $interval
     * @return array
     */
    public static function createTimeRange(string $StartTime, string $EndTime, string $interval): array{
        $timeArr = [];
        $tempTime = date('H:i', strtotime($StartTime));
        while ($tempTime < $EndTime) {
            $timeArr[] = $tempTime;
            $tempTime = date('H:i', strtotime($interval, strtotime($tempTime)));
        }
        return $timeArr;
    }

    /**
     * Adding interval to date string (According to PHP accepted formats)
     * @see http://php.net/manual/en/datetime.formats.date.php
     * @param $date string Date to add interval to
     * @param $format string Format of date to return
     * @param $interval string Interval to add to date (e.g. +1 week, +1 month, +1 year)
     * @return false|string Date with interval added
     */
    public static function addInterval(string $date, string $interval, string $format = 'Y-m-d'){
        return date($format, strtotime($interval, strtotime($date)));
    }

    /**
     * Calculate the number of minutes between two times
     * @param $startTime string
     * @param $endTime string
     * @return int
     */
    public static function calcMinutesDiff(string $startTime, string $endTime){
        $interval = date_diff(date_create($startTime), date_create($endTime));
        return ($interval->days * 24 * 60) + ($interval->h * 60) + ($interval->i);
    }

    public static function safeText($text) {
        $text = trim(strip_tags(htmlspecialchars($text)));
        return str_replace(["\t", "\n", "\r", "\\"], '', $text);
    }
}
