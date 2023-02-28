<?php

/**
 * @property $id
 * @property $username
 * @property $CompanyLogin
 * @property $email
 * @property $password
 * @property $display_name
 * @property $CompanyNum
 * @property $BrandsMain
 * @property $ItemId
 * @property $Brands
 * @property $joined
 * @property $status
 * @property $role_id
 * @property $last_session
 * @property $reminder
 * @property $remember
 * @property $FirstName
 * @property $LastName
 * @property $ContactMobile
 * @property $LastActivity
 * @property $ActiveStatus
 * @property $AgentNumber
 * @property $AgentEXT
 * @property $EmailSend
 * @property $MobileSend
 * @property $Coach
 * @property $CompanyId
 * @property $Salary
 * @property $Dob
 * @property $FixPrice
 * @property $About
 * @property $UploadImage
 * @property $Gender
 * @property $JumpBrands
 * @property $JumpBrandsId
 * @property $multiUserId
 * @property $tokenFirebase
 */
class Users extends \Hazzard\Database\Model
{
    public const IS_COACH_STATUS = 1;
    public const STATUS_ACTIVE = 1;
    public const STATUS_OFF = 0;

    protected $table = 'boostapp.users';

    /**
     * @param $email
     * @return bool
     */
    public function isEmailExists($email)
    {
        $user = DB::table($this->table)->where("email", $email)->first();
        return (bool)$user;
    }

    /**
     * @param null $CompanyNum
     * @return mixed
     */
    public function getCoachers($CompanyNum = null)
    {
        $Company = empty($CompanyNum) ? $this->CompanyNum : $CompanyNum;
        return
            DB::table($this->table)
                ->where("CompanyNum", "=", $Company)
                ->where("Coach", "=", "1")
                ->where('ActiveStatus', '=', '0')
                ->get();
    }

    /**
     * get all user that Coaches first the active
     * @param int $companyNum
     * @return Users[]
     */
    public static function getAllCoaches (int $companyNum): array
    {
           return self::where("CompanyNum", "=", $companyNum)
               ->where("Coach", "=", self::IS_COACH_STATUS)
               ->where("Status", "=", self::STATUS_ACTIVE)
               ->orderBy('ActiveStatus', 'desc')
               ->get();
    }

    public static function countActiveCoaches ($CompanyNum) {
        return
           self::where("Coach", "=", "1")
                ->where('ActiveStatus', '=', '0')
                ->where("CompanyNum", "=", $CompanyNum)
                ->count();
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function GetCoachersByCompanyNum($CompanyNum)
    {
        return DB::table($this->table)
            ->where($this->table . ".Coach", "=", "1")
            ->where($this->table . ".CompanyNum", "=", $CompanyNum)
            ->where($this->table . ".status", "=", 0)
            ->leftJoin('boostapp.userScheduleSettings', 'boostapp.userScheduleSettings.userId', '=', $this->table . '.id')
            ->select('boostapp.userScheduleSettings.*', 'boostapp.userScheduleSettings.status as userScheduleStatus', $this->table . '.*')
            ->get();
    }

    /**
     * @param $CoachesIdArr
     * @return mixed
     */
    public function GetCoachesByArr($CoachesIdArr)
    {
        return DB::table($this->table)->whereIn('id', $CoachesIdArr)->get();
    }

    /**
     * @param $ids
     * @param int $template
     * @return array
     */
    public function getGuidesByIds($ids, $template = 0)
    {
        if ($template == 1) {
            return DB::table($this->table)->select("id", "FirstName", "LastName")->where("Coach", "=", "1")->whereIn("id", $ids)->get();
        } elseif ($template == 2) {
            $guides = DB::table($this->table)->select("id")->where("Coach", "=", "1")->whereIn("id", $ids)->get();
            $arr = array();
            foreach ($guides as $guide) {
                $arr[] = $guide->id;
            }
            return $arr;
        }
        return DB::table($this->table)->where("Coach", "=", "1")->whereIn("id", $ids)->get();
    }

    /**
     * @param $id
     * @param $companyNum
     * @return mixed
     */
    public function getGuide($id, $companyNum)
    {
        return DB::table($this->table)->where("Coach", "=", "1")->where("CompanyNum", "=", $companyNum)->where("id", "=", $id)->get();
    }

    /**
     * @param $dateArr
     * @param $StartTime
     * @param $EndTime
     * @param $GroupNumber
     * @return mixed
     */
    public function isOccupied($guide_id, $company_num, $dateArr, $StartTime, $EndTime, $GroupNumber = null){
        $query = DB::table('classstudio_date')
            ->where('GuideId', $guide_id)
            ->where('CompanyNum', '=', $company_num)
            ->whereIn('StartDate', $dateArr)
            ->where('Status', '!=', '2')
            ->where(function ($q) use ($StartTime, $EndTime) {
                $q->where('StartTime', '>=', $StartTime)->where('EndTime', '<=', $EndTime)
                    ->Orwhere('StartTime', '<', $EndTime)->where('EndTime', '>', $StartTime)->where('EndTime', '!=', '00:00:00');
            });

        if ($GroupNumber)
            $query = $query->where('GroupNumber', '!=', $GroupNumber);

        return $query->first();
    }

    /**
     * @param $dateArr
     * @param $StartTime
     * @param $EndTime
     * @param $GroupNumber
     * @return mixed
     */
    public function getOccupied($dateArr, $StartTime, $EndTime, $GroupNumber = null){
        $res = [];
        foreach ($dateArr as $date) {
            $query = ClassStudioDate::where('GuideId', $this->id)
                ->where('CompanyNum', '=', $this->CompanyNum)
                ->where('StartDate', '=', $date)
                ->where('Status', '!=', '2')
                ->where('EndTime', '!=', '00:00:00')
                ->where(function ($q) use ($StartTime, $EndTime) {
                    $q->where('StartTime', '>=', $StartTime)->where('EndTime', '<=', $EndTime)
                        ->Orwhere('StartTime', '<', $EndTime)->where('EndTime', '>', $StartTime);
                });

            if ($GroupNumber)
                $query = $query->where('GroupNumber', '!=', $GroupNumber);

            $ClassStudioDates = $query->orderBy('StartTime')->get();
            foreach ($ClassStudioDates as $ClassStudioDate) {
                $res[] = $ClassStudioDate->toArray();
            }
        }
        return $res;
    }

    /**
     * @param $id
     * @param $companyNum
     * @return mixed
     */
    public function getGuideFromAll($id,$companyNum)
    {
        return DB::table($this->table)->where("CompanyNum", "=", $companyNum)->where("id", "=", $id)->first();
    }

    /**
     * @param $companyNum
     * @return self[]|null
     */
    public static function getAllCoachesByCompanyNum($companyNum): array
    {
        return self::where("CompanyNum", "=", $companyNum)->get();
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public function getAllUsersWithoutSupportTeam($companyNum){
        return DB::table($this->table)->where('CompanyNum', '=', $companyNum)->where('role_id', '!=', '1')->where('status', '=', '1')->get();
    }

    //get all coaches and Meeting details
    public function getAllCoachesAndMeetingByCompanyNum($companyNum, $meetingTemplateId=null ){
        $query = DB::table($this->table);
        if(empty($meetingTemplateId)) {
            $query->select('users.id', 'users.Brands', 'users.display_name' , 'users.UploadImage');
        } else {
            $query->select('users.id', 'users.Brands', 'users.display_name' , 'users.UploadImage',
                'meeting_template_coaches.Status', 'meeting_template_coaches.MeetingTemplateId')
                ->leftJoin('boostapp.meeting_template_coaches', function($q) use ($meetingTemplateId){
                $q->on('users.id','=', 'meeting_template_coaches.CoachId');
                $q->where('meeting_template_coaches.MeetingTemplateId', '=' , $meetingTemplateId);
            });
        }
        $query->where('CompanyNum','=',$companyNum)
            ->where('Coach','=',1)
            ->where('ActiveStatus','=',0);
        return $query->get();
    }

    public function getCoachesLimitData($CompanyNum = null){
        $Company = empty($CompanyNum) ? $this->CompanyNum : $CompanyNum;
        return DB::table($this->table)
            ->select('users.id', 'users.Brands', 'users.display_name' , 'users.UploadImage','AvailabilityStatus')
            ->where("Coach", "=", "1")
            ->where('ActiveStatus', '=', '0')
            ->where("CompanyNum" , "=",$Company)
            ->get();
    }

    public function updateAvailabilityStatus($id , $status){
        DB::table($this->table)->where('id', '=', $id)->update(['AvailabilityStatus' => $status]);
    }

    public function getAgent($CompanyNum)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', '1')
            ->where('role_id', '!=', '1')
            ->orderBy('display_name', 'ASC')
            ->get();
    }

    public function isPhoneExist($companyNum, $phone){
        $prefix = ['0','972'];

        foreach ($prefix as $pf){
            $phone = substr($phone, 0, strlen($pf)+1) == '+'.$pf ? substr($phone, strlen($pf)+1, strlen($phone)) : $phone;
            $phone = substr($phone, 0, strlen($pf)) == $pf ? substr($phone, strlen($pf), strlen($phone)) : $phone;
        }


        $req = self::where('CompanyNum', '=', $companyNum)
            ->where('ContactMobile', 'LIKE', '%'.$phone.'%')
            ->first();

        return !empty($req);
    }

    public static function isStudioHasUsers($company_num, $user_id = null) {
        $q = self::where('CompanyNum', $company_num);

        if (!empty($user_id)) {
            $q->where('id', '!=', $user_id);
        }

        return $q->count();
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public static function getFirstCoach($companyNum)
    {
        return self::where('CompanyNum', $companyNum)
            ->where("Coach", "=", self::IS_COACH_STATUS)
            ->where("Status", "=", self::STATUS_ACTIVE)
            ->orderBy('ActiveStatus', 'desc')
            ->pluck('id');
    }
}


