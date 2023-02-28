<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $FixCompanyNum
 * @property $CompanyName
 * @property $Company
 * @property $BusinessType
 * @property $CompanyId
 * @property $Email
 * @property $Dates
 * @property $Status
 * @property $ContactMobile
 * @property $ChangeDate
 * @property $ContactName
 * @property $UserId
 * @property $Vat
 * @property $PaymentRole
 * @property $BalanceAmount
 * @property $City
 * @property $Street
 * @property $StreetH
 * @property $Number
 * @property $PostCode
 * @property $POBox
 * @property $ContactPhone
 * @property $ContactFax
 * @property $WebSite
 * @property $Remarks
 * @property $Flat
 * @property $Floor
 * @property $Entry
 * @property $GetSMS
 * @property $GetEmail
 * @property $StudioUrl
 * @property $BrandsMain
 * @property $AppName
 * @property $YaadNumber
 * @property $CreditType
 * @property $LeumiCard
 * @property $Isracrd
 * @property $VisaCal
 * @property $Amkas
 * @property $Diners
 * @property $Shva
 * @property $VatNumber
 * @property $CompanyVat
 * @property $OutSideLink
 * @property $WeatherCityName
 * @property $MassavMosad
 * @property $MassavZikoy
 * @property $MassavSender
 * @property $SMSLimit
 * @property $SMSPrice
 * @property $EmailPrice
 * @property $Memotag
 * @property $Folder
 * @property $SignatureJSON
 * @property $FormAgreement
 * @property $PaymentOnTime
 * @property $OnTimePrice
 * @property $ArchiveDate
 * @property $NextDate
 * @property $LastDate
 * @property $CountClient
 * @property $BoostApp
 * @property $SendClient
 * @property $OnTimePaymentNum
 * @property $DocRemarks
 * @property $AgentId
 * @property $KevaBank
 * @property $DateBank
 * @property $OnTimeDays
 * @property $Masof
 * @property $FixPrice
 * @property $Area
 * @property $OldSystem
 * @property $Special
 * @property $CreateDates
 * @property $TrueFile
 * @property $MasofType
 *
 * Class SoftClient
 */
class SoftClient extends \Hazzard\Database\Model
{
    protected $table = "247softnew.client";

    /**
     * @param $value
     * @param $key
     * @return mixed
     */
    public static function getRow($value, $key = null)
    {
        if ($key != null) {
            return self::where($key, $value)->first();
        }
        return self::where("id", $value)->first();
    }
}
