<?php

require_once "UserBoostappLogin.php";

/**
 * @property $id
 * @property $StudioUrl
 * @property $StudioName
 * @property $CompanyNum
 * @property $UserId
 * @property $BrandsMain
 * @property $Status
 * @property $ClientId
 * @property $Takanon
 * @property $Medical
 * @property $CountBadPoint
 * @property $StatusBadPoint
 * @property $BlockDate
 * @property $tokenFirebase
 * @property $OS
 * @property $LastDate
 * @property $LastTime
 * @property $Memotag
 * @property $Folder
 * @property $GoogleCal
 * @property $GoogleCalType
 * @property $GoogleCalTime
 * @property $GoogleCalendarId
 *
 * Class StudioBoostappLogin
 */
class StudioBoostappLogin extends \Hazzard\Database\Model
{
    protected $table = "boostapplogin.studio";

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
     * @param $userId
     * @param $companyNum
     * @return mixed
     */
    public static function findByUserIdAndCompanyNum($userId, $companyNum)
    {
        return self::where("UserId", $userId)->where("CompanyNum", $companyNum)->first();
    }

    /**
     * @param $clientId
     * @param $companyNum
     * @return mixed
     */
    public static function findByClientIdAndCompanyNum($clientId, $companyNum)
    {
        return self::where("ClientId", $clientId)->where("CompanyNum", $companyNum)->first();
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public static function findByClientId($clientId)
    {
        return self::where("ClientId", $clientId)->first();
    }

    /**
     * @return mixed
     */
    public function update()
    {
        $userArr = $this->createArrayFromObj($this);
        return self::where("id", $this->id)->update($userArr);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function insert_into_table($data)
    {
        return self::insertGetId($data);
    }

    /**
     * @param $studioId
     * @param $data
     * @return mixed
     */
    public static function updateStudioById($studioId, $data)
    {
        return self::where("id", $studioId)->update($data);
    }

    /**
     * @param $clientId
     * @param $companyId
     * @param $data
     * @return mixed
     */
    public static function updatebyClientAndCompany($clientId, $companyId, $data)
    {
        return self::where('ClientId', '=', $clientId)
            ->where('CompanyNum', '=', $companyId)
            ->update($data);
    }

    /**
     * @param $ClientId
     * @param $CalendarId
     * @return void
     */
    public static function updateGoogleCalendarId($ClientId, $CalendarId)
    {
        self::where('ClientId', '=', $ClientId)
            ->update(['GoogleCalendarId' => $CalendarId]);
    }

    /**
     * returns user's avatar (profile picture)
     * @param $clientId
     * @param $companyNum
     * @return string|null
     */
    public static function getAvatar($clientId, $companyNum)
    {
        $studioUser = self::findByClientIdAndCompanyNum($clientId, $companyNum);
        if (!empty($studioUser)) {
            return UserBoostappLogin::getAvatar($studioUser->UserId);
        }
        return null;
    }
}
