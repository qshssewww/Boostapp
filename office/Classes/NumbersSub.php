<?php

require_once "Utils.php";
require_once "Company.php";

class NumbersSub extends Utils
{
    private $id;
    private $Name;
    private $NumbersId;
    private $recordListingID;
    private $Status;
    private $CompanyNum;

    private $table;
    public function __construct($id = null)
    {
        $this->table = "numberssub";
        if($id != null){
            $this->setData($id);
        }
    }

    public function setData($id){
        $data = DB::table($this->table)->where("id", "=", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
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

    public function InsertNumbersSubNewData($arrayData){
        $idInsert = DB::table($this->table)
            ->insertGetId($arrayData);
        return $idInsert;
    }

    public function GetNumbersSubByCompanyNum($CompanyNum,$NumbersId){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('NumbersId','=',$NumbersId)
            ->where('Status', 0)
            ->get();
        return $data;
    }

    public function UpdateNumbersSub($arrayData)
    {
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $arrayData['CompanyNum'])
            ->where('NumbersId','=',$arrayData['NumbersId'])
            ->update($arrayData);
        return $affact;
    }

    public function UpdateNumbersSubById($arrayData)
    {
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $arrayData['CompanyNum'])
            ->where('id','=',$arrayData['id'])
            ->update($arrayData);
        return $affact;
    }

    public function deleteNumberSub($id) {
        return DB::table($this->table)->where("id", $id)->where("CompanyNum", Company::getInstance()->CompanyNum)->update(["Status" => 1]);
    }

    public function deleteNumberSubsByNumberId($id) {
        return DB::table($this->table)->where("NumbersId", $id)->where("CompanyNum", Company::getInstance()->CompanyNum)->update(["Status" => 1]);
    }
}