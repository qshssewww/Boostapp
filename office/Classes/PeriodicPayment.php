<?php


require_once "Utils.php";

class PeriodicPayment extends Utils
{
   private $id;
   private $CompanyNum ;
   private $ChargePayment ;
   private $ChargeDay ;
   private $PreventOrders ;
   private $PreventOrderInstantly ;
   private $PreventOrderDays ;
   private $PreventClasses ;
   private $PreventClassesInstantly ;
   private $PreventClassesDays;
   private $table;


    public function __construct()
    {
        $this->table = "PeriodicPayment";
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


    public function InsertPeriodicPaymentNewData($arrayData){
        $idInsert = DB::table($this->table)
            ->insertGetId($arrayData);
        return $idInsert;
    }

    public function GetPeriodicPaymentByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
                ->first();
        return $data;
    }

    public function UpdatePeriodicPayment($arrayData)
    {
        $companyNum = $arrayData["CompanyNum"];
        $dataExist = $this->GetPeriodicPaymentByCompanyNum($companyNum);
        if(empty($dataExist)){
            return $this->InsertPeriodicPaymentNewData($arrayData);
        }
        else{
            $affact = DB::table($this->table)
                ->where('CompanyNum', '=', $companyNum)
                ->update($arrayData);
            return $affact;
        }
    }


}