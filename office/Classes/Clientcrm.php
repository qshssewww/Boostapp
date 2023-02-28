<?php

require_once "Utils.php";
require_once "Company.php";

class Clientcrm extends \Hazzard\Database\Model {

    private $id;
    private $CompanyNum;
    private $ClientId;
    private $Remarks;
    private $Dates;
    private $User;
    private $LeadId;
    private $TillDate;
    private $StarIcon;
    private $Status;
    protected $table = "boostapp.clientcrm";

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

    public function GetClientcrmByClientId($CompanyNum,$ClientId){
        $CRM = DB::table($this->table)

            ->where('CompanyNum', $CompanyNum)->where('ClientId', '=',$ClientId)->where('StarIcon', '=', '1')->where('Status', '=', '0')->whereNull('TillDate')

            ->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=',$ClientId)->where('StarIcon', '=', '1')->where('Status', '=', '0')->where('TillDate', '>=', date('Y-m-d'))

            ->orderBy('Dates','DESC')

            ->first();
        return $CRM;
    }

    public function getAllClientcrmByClientId($CompanyNum,$ClientId){
        $CRM = DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)->where('ClientId', '=',$ClientId)->where('StarIcon', '=', '1')->where('Status', '=', '0')->whereNull('TillDate')
            ->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=',$ClientId)->where('StarIcon', '=', '1')->where('Status', '=', '0')->where('TillDate', '>=', date('Y-m-d'))
            ->orderBy('Dates','DESC')
            ->get();
        return $CRM;
    }

    public function isNotice($id)
    {
       return DB::table('clientcrm')
            ->where('ClientId', '=', $id)
            ->where('Status', '=', 0)
            ->where(
                function ($query) {
                    $query->where('TillDate', '>', date("Y-m-d"))
                        ->orWhere('TillDate', null);
                }
            )
            ->exists();
    }

    public function editClientCrm($crmId, $clientId, $status) {
        return DB::table($this->table)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->where('id', $crmId)
            ->where('ClientId', $clientId)
            ->update(array("Status" => $status, 'Dates' => date('Y-m-d H:i:s')));
    }

    public function addClientCrm($clientId,$userId,$remarks, $tillDate = null){
        if ($tillDate == '') {
            $tillDate = null;
        }

        $res = DB::table($this->table)->insertGetId([
            'CompanyNum' => Auth::user()->CompanyNum,
            'ClientId' => $clientId,
            'Remarks' => $remarks,
            'User' => $userId,
            'LeadId' => 0,
            'TillDate' => $tillDate,
            'StarIcon' => 1,
            'Status' => 0
        ]);
        
        $time = date('Y-m-d H:i:s');
        $pipeRes= DB::table('pipeline')
            ->where('CompanyNum', Auth::user()->CompanyNum)
            ->where('ClientId', $clientId)
            ->update(array('NoteDates' => $time));

        return DB::table($this->table)
            ->where('id', '=', $res)
            ->get();
    }

    public function editClientCrmRemark($remarks, $clientId, $crmId, $tillDate = null){
        if ($tillDate == "")
            $tillDate = null;

        $update = DB::table($this->table)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->where('id', $crmId)
            ->where('ClientId', $clientId)
            ->update(array("Remarks" => $remarks, 'Dates' => date('Y-m-d H:i:s'), 'TillDate' => $tillDate));

        return $update ? DB::table('clientcrm')->where('id', $crmId)->first() : 0;
    }
    public function countClientCrm($companyNum, $ClientId){
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('ClientId', '=', $ClientId)
            ->count();
    }
}