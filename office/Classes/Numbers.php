<?php

require_once "Utils.php";
require_once "NumbersSub.php";
require_once "Company.php";
require_once "ClassStudioAct.php";
require_once "ClassStudioDate.php";

class Numbers extends Utils
{
    private $id;
    private $Name;
    private $recordListingID;
    private $Status;
    private $CompanyNum;
    private $Unique;
    private $table;
    public function __construct()
    {
        $this->table = "numbers";
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

    public function InsertNumbersNewData($arrayData){
        $idInsert = DB::table($this->table)
            ->insertGetId($arrayData);
        return $idInsert;
    }

    public function GetNumbersByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->get();
        return $data;
    }

    public function GetActiveNumbersByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum' ,$CompanyNum)
            ->where('Status', 0)
            ->get();
        return $data;
    }



    public function UpdateNumbers($arrayData)
    {
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $arrayData['CompanyNum'])
            ->where('id','=',$arrayData['id'])
            ->update($arrayData);
        return $affact;
    }
    public function GetNumbersById($CompanyNum,$id){
        $ClassDevice = DB::table($this->table)->where('CompanyNum', $CompanyNum)->where('id', '=', $id)->where('Status', '=', '0')->first();
        return $ClassDevice;

    }
    public function deleteNumbers($id) {
        $affect = DB::table($this->table)->where("id", $id)->where("CompanyNum", Company::getInstance()->CompanyNum)->update(["Status" => 1]);
        $numbersSub = new NumbersSub();
        $numbersSub->deleteNumberSubsByNumberId($id);
        return $affect;
    }
    public function getDevicePopupInfo($actId){
        $studioActObj = new ClassStudioAct($actId);
        $studioDateObj = new ClassStudioDate($studioActObj->__get('ClassId'));
        return [
            'availableDevices' => $studioDateObj->getAvailableDevices(),
        ];

    }
}
