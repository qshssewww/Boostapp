<?php
require_once "Utils.php";
require_once "calendar.php";
require_once "Users.php";

class ClassTemplate extends Utils
{
    private $id;
    private $CompanyNum;
    private $guideId;
    private $calendar;
    private $classType;
    private $durationTime;
    private $durationType;
    private $notificationTime;
    private $notificationType;
    private $participants;
    private $payment;
    private $price;
    private $cancelationTime;
    private $cancelationType;
    private $frequency;
    private $maxEvents;
    private $minCheckType;
    private $minCheckTime;
    private $minParticipants;
    private $prepTime;
    private $prepType;
    private $image;
    private $content;
    private $zoom_online;
    private $zoomClass;
    private $onlineLink;
    private $redirectLink;
    private $pageLink;
    private $devices;
    private $availability;
    private $status;
    private $date;
    private $table;
    public function __construct()
    {
        $this->table = "classTemplate";
    }

    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

        public function InsertClassTemplateNewData($arrayData){
        $idInsert = DB::table($this->table)
            ->insertGetId($arrayData);
        return $idInsert;
    }

    public function GetClassTemplatesByCoachId($CompanyNum,$guideId){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where("guideId",'=',$guideId)
            ->get();
        return $data;
    }
    public function GetCoachsTemplates($CompanyNum){

        $Coachs = [];

        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->distinct('guideId')->get(['guideId']);
        foreach ($data as $Guide){
            $user = DB::table('users')->where('id','=',$Guide->guideId)->first();
            if(!empty($user)){
                array_push($Coachs,$user);
            }
        }
        return $Coachs;
    }
    public function UpdateTemplate($arrayData)
    {
        $companyNum = $arrayData["CompanyNum"];
        unset($arrayData["CompanyNum"]);
        $id = $arrayData["id"];
        unset($arrayData["id"]);
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('id','=',$id)
            ->update($arrayData);
        return $affact;
    }

    public function UpdateTemplateByGuideId($arrayData)
    {
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $arrayData["CompanyNum"])
            ->where('guideId','=',$arrayData["guideId"])
            ->update($arrayData);
        return $affact;
    }
    public function GetTemplateById($companyNum, $id){
        $calObj = new calendar();
        $userObj = new Users();
        $templates = DB::table($this->table)->where("status","!=",0)->where("id",'=',$id)->where("CompanyNum","=",$companyNum)->get();
        foreach ($templates as $template){
            if($template->calendar != -1) {
                $calendar = explode(",", $template->calendar);
                $template->calendar = $calObj->getCalendarsByIds($calendar,1);
            }
            else{
                $template->calendar = array(0 => "-1");
            }
            if($template->guideId != -1){
                $guides = explode(",", $template->guideId);
                $template->guideId = $userObj->getGuidesByIds($guides,2);
            }
            else{
                $template->guideId = array(0 => "-1");
            }
        }
        return $this->createArrayFromObjArr($templates);
    }
    public function getAllCompanyTemplates($companyNum){
        $calObj = new calendar();
        $userObj = new Users();
        $templates = DB::table($this->table)->where("status","!=",0)->where("CompanyNum","=",$companyNum)->get();
        foreach ($templates as $template){
            if($template->calendar != -1) {
                $calendar = explode(",", $template->calendar);
                $template->calendar = $calObj->getCalendarsByIds($calendar);
            }
            if($template->guideId != -1){
                $guides = explode(",", $template->guideId);
                $template->guideId = $userObj->getGuidesByIds($guides,1);
            }
        }
        return $this->createArrayFromObjArr($templates);
    }
}
