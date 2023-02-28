<?php
require_once "Brand.php";
require_once "MembershipType.php";
require_once "ClassesType.php";
require_once "CompanyProductSettings.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $StudioUrl
 * @property $BrandsMain
 * @property $CompanyName
 * @property $AppName
 * @property $ClientName
 * @property $BusinessType
 * @property $CompanyId
 * @property $Street
 * @property $Number
 * @property $City
 * @property $Zip
 * @property $POBox
 * @property $WebSite
 * @property $Email
 * @property $NikuyMsBamakor
 * @property $NikuyMsBamakorDate
 * @property $ContactMobile
 * @property $ContactPhone
 * @property $ContactFax
 * @property $ResponderCRM
 * @property $PhoneClient
 * @property $PhoneWhatsApp
 * @property $Username019
 * @property $Password019
 * @property $UsernameSendGrid
 * @property $PasswordSendGrid
 * @property $PhoneSMS
 * @property $PhoneSMSTitle
 * @property $EmailCRM
 * @property $TypeShva
 * @property $YaadNumber
 * @property $YaadzPass
 * @property $CreditType
 * @property $LeumiCard
 * @property $Isracrd
 * @property $VisaCal
 * @property $Amkas
 * @property $Diners
 * @property $Shva
 * @property $Vat
 * @property $CompanyVat
 * @property $OutSideLink
 * @property $WeatherCityName
 * @property $CpaEmail
 * @property $CpaEmailCopy
 * @property $PaySiteUrl
 * @property $MassavMosad
 * @property $MassavZikoy
 * @property $MassavSender
 * @property $VoiceCenterToken
 * @property $VoiceCenterNumber
 * @property $ShowBalance
 * @property $DocsBackgroundColor
 * @property $DocsTextColor
 * @property $DocsCompanyLogo
 * @property $DocsCompanyLogoBig
 * @property $FirstHour
 * @property $LastHour
 * @property $SMSLimit
 * @property $SMSPrice
 * @property $EmailPrice
 * @property $GeneralItemId
 * @property $StartYear
 * @property $MaxPayment
 * @property $MainAccounting
 * @property $Memotag
 * @property $Folder
 * @property $Dates
 * @property $Status
 * @property $CountClient
 * @property $FaceBookId
 * @property $API_ACCESS_KEY
 * @property $CpaType
 * @property $GooglePlayLink
 * @property $AppStoreLink
 * @property $MeshulamAPI
 * @property $MeshulamUserId
 * @property $TranzilaTerminal
 * @property $TranzilaPassword
 * @property $TranzilaCreditPass
 * @property $LiveMeshulam
 * @property $intercomId
 * @property $primaryColor
 * @property $coronaStmt
 * @property $greenPass
 * @property $isNew
 * @property $lockStatus
 * @property $lockDate
 * @property $beta
 */
class Company
{
    /**
     * @var $id int
     */
    private $id;
    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $StudioUrl string
     */
    private $StudioUrl;

    /**
     * @var $BrandsMain string
     */
    private $BrandsMain;

    /**
     * @var $CompanyName string
     */
    private $CompanyName;

    /**
     * @var $AppName string
     */
    private $AppName;

    /**
     * @var $ClientName string
     */
    private $ClientName;

    /**
     * @var $BusinessType int
     */
    private $BusinessType;

    /**
     * @var $CompanyId string
     */
    private $CompanyId;

    /**
     * @var $Street string
     */
    private $Street;

    /**
     * @var $Number string
     */
    private $Number;

    /**
     * @var $City int
     */
    private $City;

    /**
     * @var $Zip string
     */
    private $Zip;

    /**
     * @var $POBox string
     */
    private $POBox;

    /**
     * @var $WebSite string
     */
    private $WebSite;

    /**
     * @var $Email string
     */
    private $Email;

    /** @var $NikuyMsBamakor string */
    private $NikuyMsBamakor;

    private $NikuyMsBamakorDate;

    private $ContactMobile;

    private $ContactPhone;

    private $ContactFax;

    private $ResponderCRM;
    private $PhoneClient;
    private $PhoneWhatsApp;
    private $Username019;
    private $Password019;
    private $UsernameSendGrid;
    private $PasswordSendGrid;
    private $PhoneSMS;
    private $PhoneSMSTitle;
    private $EmailCRM;

    /** @var $TypeShva int */
    private $TypeShva;

    private $YaadNumber;
    private $YaadzPass;
    private $CreditType;
    private $LeumiCard;
    private $Isracrd;
    private $VisaCal;
    private $Amkas;
    private $Diners;
    private $Shva;
    private $Vat;
    private $CompanyVat;
    private $OutSideLink;
    private $WeatherCityName;
    private $CpaEmail;
    private $CpaEmailCopy;
    private $PaySiteUrl;
    private $MassavMosad;
    private $MassavZikoy;
    private $MassavSender;
    private $VoiceCenterToken;
    private $VoiceCenterNumber;
    private $ShowBalance;
    private $DocsBackgroundColor;
    private $DocsTextColor;
    private $DocsCompanyLogo;
    private $DocsCompanyLogoBig;
    private $FirstHour;
    private $LastHour;
    private $SMSLimit;
    private $SMSPrice;
    private $EmailPrice;
    private $GeneralItemId;
    private $StartYear;
    private $MaxPayment;
    private $MainAccounting;
    private $Memotag;
    private $Folder;
    private $Dates;
    private $Status;
    private $CountClient;
    private $FaceBookId;
    private $API_ACCESS_KEY;
    private $CpaType;
    private $GooglePlayLink;
    private $AppStoreLink;
    private $MeshulamAPI;
    private $MeshulamUserId;
    private $TranzilaTerminal;
    private $TranzilaPassword;
    private $TranzilaCreditPass;
    private $LiveMeshulam;
    private $intercomId;
    private $MemberShipLimitMoney;
    private $coronaStmt;
    private $greenPass;
    private $lockStatus;
    private $lockDate;
    private $beta;

    /**
     * @var $brands Brand[]
     */
    private $brands;

    /**
     * @var $items stdClass[]
     */
    private $items;
    /**
     * @var $membership_types MembershipType[]
     */
    private $membership_types;

    /**
     * @var $classTypes ClassesType[]
     */
    private $classTypes;
    /**
     * @var $instance Company
     */
    private static $instance;

    /**
     * @param $company
     * @param $data string|bool
     */
    public function __construct($company = null, $data = true)
    {
        if ($company == null) {
            $CompanyNum = Auth::user()->CompanyNum;
        } else {
            $CompanyNum = $company;
        }

        $setting = DB::table("settings")->where("CompanyNum", "=", $CompanyNum)->first();
        foreach ($setting as $key => $val) {
            if (property_exists($this, $key)) {
                $this->__set($key, $val);
            }
        }

        if ($data === "branch") {
            $this->setBrands();
        } elseif ($data == true) {
            $this->setMembership();
            $this->setClassType();
            $this->setBrands();
        }
    }

    /**
     * @param $data
     * @return Company
     */
    public static function getInstance($data = true)
    {
        if (self::$instance === null) {
            self::$instance = new Company(Auth::user()->CompanyNum, $data);
        }
        return self::$instance;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * @return Brand[]
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @return MembershipType[]
     */
    public function getMembershipTypes()
    {
        return $this->membership_types;
    }

    /**
     * @return ClassesType[]
     */
    public function getClassTypes()
    {
        return $this->classTypes;
    }

    /**
     * @return stdClass[]
     */
    public function setGetItems()
    {
        if ($this->items == null) {
            $this->items = DB::table("items")->where("CompanyNum", "=", $this->CompanyNum)->get();
        }
        return $this->items;
    }

    /**
     * @return void
     */
    public function updateCoronaStmt()
    {
        $arr = array(
            "coronaStmt" => $this->coronaStmt
        );
        DB::table("settings")->where("CompanyNum", "=", $this->CompanyNum)->update($arr);
    }

    /**
     * @return void
     */
    public function updateGreenPass()
    {
        $arr = array(
            "greenPass" => $this->greenPass
        );
        DB::table("settings")->where("CompanyNum", "=", $this->CompanyNum)->update($arr);
    }

    /**
     * @return void
     */
    public function setMembership()
    {
        $this->membership_types = array();
        //$companyProductSettings= new CompanyProductSettings();
        //$CompanySettings = $companyProductSettings->getSingleByCompanyNum($this->CompanyNum);
        //   if($CompanySettings->manageMemberships=="1"){
        $memberships = DB::table('membership_type')->where('CompanyNum', '=', $this->CompanyNum)->where("Status", 0)->get();
//        }else{
//            $memberships = DB::table('membership_type')->where('CompanyNum','=', $this->CompanyNum)->where('mainMembership','=',"1")->get();
//        }

        foreach ($memberships as $membership) {
            $mType = new MembershipType();
            foreach ($membership as $key => $value) {
                $mType->__set($key, $value);
            }
            array_push($this->membership_types, $mType);
        }
    }

    /**
     * @return void
     */
    public function setClassType()
    {
        $this->classTypes = array();
        $classes = DB::table('class_type')
            ->where('CompanyNum', '=', $this->CompanyNum)
            ->where("Status", "=", 0)
            ->orderBy('EventType')
            ->get();
        foreach ($classes as $class) {
            $cType = new ClassesType();
            foreach ($class as $key => $value) {
                $cType->__set($key, $value);
            }
            array_push($this->classTypes, $cType);
        }
    }

    /**
     * @return void
     */
    public function setBrands()
    {
        $this->brands = array();
        $CompanyBrands = DB::table('brands')->where('CompanyNum', '=', $this->CompanyNum)->where("Status", "=", 0)->get();
        foreach ($CompanyBrands as $CompanyBrand) {
            $brand = new Brand();
            foreach ($CompanyBrand as $key => $value) {
                $brand->__set($key, $value);
            }
            array_push($this->brands, $brand);
        }
    }

    public function UpdateNotificationDate($days)
    {
        $timeType = 'day';
        $activities = DB::table('client_activities')->where('CompanyNum', '=', $this->CompanyNum)->where('Freez', '!=', '1')->where('Status', '=', '0')
            ->update(['NotificationDays' => DB::raw('TrueDate - INTERVAL '.$days.' DAY')]);
    }

}
