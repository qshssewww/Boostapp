<?php

include_once "Utils.php";

class CancelationSettings extends Utils
{
    private $table;
    public $id;
    public $name;
    public $cacelationNumber;
    public $cacelationType;
    public $buttonBlockNumber;
    public $buttonBlockType;
    public $allowCancel;
    public $allowButtonBlock;
    public $CompanyNum;
    public $Type;
    public $date;

    function __construct($id = null)
    {
        $this->table = "boostapp.cancelation_settings";
        if ($id != null) {
            $this->getCancelationById($id);
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

    function getCancelationById($id)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('id', '=',  $id)
            ->first();

        if (empty($result)) {
            return null;
        }
        if($result != null) {
            foreach ($result as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    function getCancelationByCompanyNum($companyNum)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('CompanyNum', '=',  $companyNum)
            ->get();

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    function getLastThreeCancelationByCompanyNum($companyNum)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('CompanyNum', '=',$companyNum)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();
        if (empty($result)) {
            return null;
        }
        return $result;
    }
    function getLastThreeCancelationByCompanyNumAndType($companyNum,$Type)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('CompanyNum', '=',$companyNum)
            ->where('Type', '=',$Type)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();
        if (empty($result)) {
            return null;
        }
        return $result;
    }
    public static function insertNewCancelation($data)
    {
        try {
            $rowId = DB::table('boostapp.cancelation_settings')->insertGetId(
                $data
            );
            return $rowId;
        } catch (Exception $e) {
            return $e;
        }
    }
}
