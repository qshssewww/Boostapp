<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/Settings.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $ViewClass
 * @property $ViewClassDates
 * @property $ViewClassDayNum
 * @property $DifrentTime
 * @property $TypeDifrentTime
 * @property $DifrentTimeMin
 * @property $DifrentTimeOption
 * @property $MemberShipLimitType
 * @property $MemberShipLimitLateCancel
 * @property $MemberShipLimitNoneShow
 * @property $MemberShipLimit
 * @property $DaysMemberShipLimit
 * @property $MemberShipLimitDays
 * @property $MemberShipLimitUnBlock
 * @property $MemberShipLimitUnBlockDays
 * @property $MemberShipLimitMoney
 * @property $Watinglist
 * @property $WatinglistMin
 * @property $WatinglistEndMin
 * @property $WatinglistOrder
 * @property $WatinglistOrderTime
 * @property $AppFreez
 * @property $AppRenew
 * @property $AppKeva
 * @property $SendSMS
 * @property $ClassWeek
 * @property $ClassWeekMonth
 * @property $MorningTime
 * @property $EveningTime
 * @property $Takanon
 * @property $Health
 * @property $Sunday
 * @property $Monday
 * @property $Tuesday
 * @property $Wednesday
 * @property $Thursday
 * @property $Friday
 * @property $Saturday
 * @property $SendNotification
 * @property $KevaDays
 * @property $FreeWatingList
 * @property $FreeWatinglistOrderTime
 * @property $SelectDay
 * @property $SelectTimes
 * @property $WatingListNight
 * @property $WatingListStartTime
 * @property $WatingListEndTime
 * @property $MembershipType
 * @property $AppChat
 * @property $GoogleCal
 * @property $KevaTotal
 * @property $ShowTakanon
 * @property $ShowHealth
 * @property $studioMsg
 * @property $logoImg
 * @property $studioCoverImg
 * @property $msgColor
 */
class AppSettings extends Model
{
    protected $table = "appsettings";

    private $_studioSettings;

    /**
     * @param $companyNum
     */
    public function __construct($companyNum = null)
    {
        $attributes = [];
        if ($companyNum !== null) {
            $attributes = [
                'CompanyNum' => $companyNum,
            ];
        }

        parent::__construct($attributes);
    }

    /**
     * @param $companyNum
     * @return void
     */
    public function setData($companyNum)
    {
        $clientAct = DB::table($this->table)->where("CompanyNum", $companyNum)->first();
        if ($clientAct != null) {
            foreach ($clientAct as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * @param $CompanyNum
     * @return AppSettings|null
     */
    public static function getByCompanyNum($CompanyNum)
    {
        return self::where('CompanyNum', $CompanyNum)->first();
    }

    /**
     * @return Settings
     */
    public function studioSettings()
    {
        if (!$this->_studioSettings) {
            $this->_studioSettings = Settings::getSettings($this->CompanyNum);
        }

        return $this->_studioSettings;
    }

    public function getAppSettingsByCompanyNum($companyNum){
        return self::where('CompanyNum', $companyNum)
            ->first();
    }

    public function updateAppSettingsByCompanyNum($companyNum, $viewClass, $day, $times, $ViewClassDayNum){
        return self::where('CompanyNum', $companyNum)
            ->update(array(
                'ViewClass' => $viewClass,
                'SelectDay' => $day,
                'SelectTimes' => $times,
                'ViewClassDayNum' => $ViewClassDayNum
            ));
    }

    /**
     * @param $CompanyNum
     * @param $TsCheck
     * @return false|float|int|mixed|null
     */
    public static function checkSendTimeByCompanyNum($CompanyNum, $TsCheck = null)
    {
        if (!$TsCheck) $TsCheck = time();
        $AppSettings = self::getByCompanyNum($CompanyNum);

        if ($AppSettings->WatingListNight == '1') {
            $Date = date("Y-m-d", $TsCheck);

            $WaitingListStartTime = $AppSettings->WatingListStartTime ?? '01:00:00';
            $WaitingListEndTimeStr = $AppSettings->WatingListEndTime ?? '06:00:00';

            $WaitingListStartTime = strtotime($Date . " " . $WaitingListStartTime);
            $WaitingListEndTime = strtotime($Date . " " . $WaitingListEndTimeStr);

            // current notif late
            $LateNotif = (($WaitingListStartTime > $WaitingListEndTime)
                ? ($WaitingListStartTime <= $TsCheck || $TsCheck < $WaitingListEndTime)
                : ($WaitingListStartTime <= $TsCheck && $TsCheck < $WaitingListEndTime));

            if ($LateNotif) {
                $TsNotif = strtotime($Date . " " . $WaitingListEndTimeStr);
                if ($WaitingListStartTime > $WaitingListEndTime && $WaitingListStartTime <= $TsCheck) {
                    // morning notif && currently evening -> fix date +1 day in seconds
                    $TsNotif += 24 * 60 * 60;
                }

                return $TsNotif;
            }
        }

        return $TsCheck;
    }

    /**
     * @param $CompanyNum
     * @param $TsCheck
     * @return false|float|int|mixed|null
     */
    public static function getNextSendTimeByCompanyNum($CompanyNum, $TsCheck = null)
    {
        if (!$TsCheck) $TsCheck = self::checkSendTimeByCompanyNum($CompanyNum, time());
        $AppSettings = self::getByCompanyNum($CompanyNum);

        return self::checkSendTimeByCompanyNum($CompanyNum, $TsCheck + 60 * $AppSettings->WatinglistMin);
    }
}
