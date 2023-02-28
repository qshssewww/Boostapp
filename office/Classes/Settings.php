<?php

require_once "Utils.php";

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
 * @property $WhatsAppPrice
 * @property $WhatsAppEnabled
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
class Settings extends Utils
{
    protected $id;
    protected $CompanyNum;
    protected $StudioUrl;
    protected $BrandsMain;
    protected $CompanyName;
    protected $AppName;
    protected $ClientName;
    protected $BusinessType;
    protected $CompanyId;
    protected $Street;
    protected $Number;
    protected $City;
    protected $Zip;
    protected $POBox;
    protected $WebSite;
    protected $Email;
    protected $NikuyMsBamakor;
    protected $NikuyMsBamakorDate;
    protected $ContactMobile;
    protected $ContactPhone;
    protected $ContactFax;
    protected $ResponderCRM;
    protected $PhoneClient;
    protected $PhoneWhatsApp;
    protected $Username019;
    protected $Password019;
    protected $UsernameSendGrid;
    protected $PasswordSendGrid;
    protected $PhoneSMS;
    protected $PhoneSMSTitle;
    protected $EmailCRM;
    protected $TypeShva;
    protected $YaadNumber;
    protected $YaadPass;
    protected $YaadzPass;
    protected $CreditType;
    protected $LeumiCard;
    protected $Isracrd;
    protected $VisaCal;
    protected $Amkas;
    protected $Diners;
    protected $Shva;
    protected $Vat;
    protected $CompanyVat;
    protected $OutSideLink;
    protected $WeatherCityName;
    protected $CpaEmail;
    protected $CpaEmailCopy;
    protected $PaySiteUrl;
    protected $MassavMosad;
    protected $MassavZikoy;
    protected $MassavSender;
    protected $VoiceCenterToken;
    protected $VoiceCenterNumber;
    protected $ShowBalance;
    protected $DocsBackgroundColor;
    protected $DocsTextColor;
    protected $DocsCompanyLogo;
    protected $DocsCompanyLogoBig;
    protected $FirstHour;
    protected $LastHour;
    protected $SMSLimit;
    protected $SMSPrice;
    protected $EmailPrice;
    protected $GeneralItemId;
    protected $StartYear;
    protected $MaxPayment;
    protected $MainAccounting;
    protected $Memotag;
    protected $Folder;
    protected $Dates;
    protected $Status;
    protected $CountClient;
    protected $FaceBookId;
    protected $API_ACCESS_KEY;
    protected $CpaType;
    protected $GooglePlayLink;
    protected $AppStoreLink;
    protected $MeshulamAPI;
    protected $MeshulamUserId;
    protected $TranzilaTerminal;
    protected $TranzilaPassword;
    protected $TranzilaCreditPass;
    protected $LiveMeshulam;
    protected $intercomId;
    protected $primaryColor;
    protected $coronaStmt;
    protected $greenPass;
    protected $isNew;
    protected $lockStatus;
    protected $lockDate;
    protected $beta;

    private static $table = "settings";

    /**
     * @param null $companyNum
     */
    public function __construct($companyNum = null)
    {
        if ($companyNum != null) {
            $this->setData($companyNum);
        }
    }

    /**
     * @param $name
     * @param $value
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
     * @param $companyNum
     */
    public function setData($companyNum)
    {
        $clientAct = DB::table(self::$table)->where("CompanyNum", $companyNum)->first();
        if ($clientAct != null) {
            foreach ($clientAct as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * @param $companyNum
     * @return Settings|null
     */
    public static function getSettings($companyNum)
    {
        return DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->first();
    }

    /**
     * @param $company
     * @param $data
     * @return mixed
     */
    public static function updateRow($company, $data)
    {
        return DB::table(self::$table)->where("CompanyNum", $company)->update($data);
    }

    /**
     * @param $company
     * @return mixed
     */
    public static function getVat($company)
    {
        return DB::table(self::$table)->where("CompanyNum", $company)->pluck('Vat');
    }

    /**
     * @return mixed
     */
    public function update()
    {
        $settingsArr = $this->createArrayFromObj($this);
        $res = DB::table(self::$table)->where("id", $this->id)->update($settingsArr);
        return $res;
    }
    public static function getCompanyNumByStudioUrl($StudioUrl) {
        $settings = DB::table(self::$table)
            ->select('CompanyNum')
            ->where('StudioUrl', $StudioUrl)
            ->where('Status', 0)
            ->first();
        return $settings->CompanyNum ?? null;
    }

    public static function getCompanyNameByNum($companyNum) {
        $settings = DB::table(self::$table)
            ->where('CompanyNum', $companyNum)
            ->where('Status', 0)
            ->first();
        return $settings->CompanyName ?? null;
    }

    /**
     * @return bool return true is can use credit cards
     */
    public function hasCreditTerminal(): bool
    {
        return (int)$this->TypeShva === 0 ? !empty($this->YaadNumber) : !empty($this->MeshulamUserId);
    }

    /**
     * @param int $companyNum
     * @return mixed
     */
    public static function getByCompanyNum(int $companyNum)
    {
        return DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->first();
    }

    /**
     * If the studio has branches, returns the company number of the main one, if not then returns the company number
     * @return int (CompanyNum)
     */
    public static function getCompanyNumFromMainBrand($Settings): int
    {
        if ((int)$Settings->BrandsMain !== 0 && (int)$Settings->MainAccounting === 1) {
            return $Settings->BrandsMain;
        }
        return $Settings->CompanyNum;
    }

    /**
     * @param $Settings
     * @return int
     */
    public static function getVatByBusinessType($Settings): int
    {

        if(in_array((int)$Settings->BusinessType, BusinessType::BUSINESS_TYPE_WITH_OUT_VAT)) {
            return 0;
        }
        if(isset($Settings->CompanyVat) && (int)$Settings->CompanyVat === 1) {
            return 0;
        }
        return $Settings->Vat;
    }

    /**
     * @param int $companyNum
     * @return int
     */
    public static function getBusinessType(int $companyNum): int
    {
        $businessType = DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->pluck('BusinessType') ?? 0;
        return (int)$businessType;
    }


    /**
     * @param int $companyNum
     * @return int|null
     */
    public static function getTypeShvaByCompanyNum(int $companyNum): ?int
    {
        return (int)DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->pluck('TypeShva');
    }


    public static function getBetaCode($companyNum) {
        return (int)DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->pluck('beta');
    }


}
