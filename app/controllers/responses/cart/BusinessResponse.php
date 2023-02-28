<?php

require_once __DIR__ . '/../../../../office/Classes/Settings.php';
require_once __DIR__ . '/../BaseResponse.php';

class BusinessResponse extends BaseResponse{
    public $companyNum;
    public $companyName;
    public $businessType;
    public $appName;
    public $city;
    public $street;


    /**
     * BusinessResponse constructor.
     * @param $Settings
     */
    public function __construct($Settings){
        $this->companyNum = $Settings->CompanyNum;
        $this->companyName = $Settings->CompanyName;
        $this->businessType = $Settings->BusinessType;
        $this->appName = $Settings->AppName;
        $this->city = $Settings->City;
        $this->street = $Settings->Street;
        return $this;
    }

    public function getCompanyNum(){
        return $this->companyNum;
    }

    public function setCompanyNum($companyNum): void{
        $this->companyNum = $companyNum;
    }

    public function getCompanyName(){
        return $this->companyName;
    }

    public function setCompanyName($companyName): void{
        $this->companyName = $companyName;
    }

    public function getBusinessType(){
        return $this->businessType;
    }

    public function setBusinessType($businessType): void{
        $this->businessType = $businessType;
    }

    public function getAppName(){
        return $this->appName;
    }

    public function setAppName($appName): void{
        $this->appName = $appName;
    }

    public function getCity(){
        return $this->city;
    }

    public function setCity($city): void{
        $this->city = $city;
    }

    public function getStreet(){
        return $this->street;
    }

    public function setStreet($street): void{
        $this->street = $street;
    }
}
