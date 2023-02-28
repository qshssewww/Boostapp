<?php

class RepetitionSettings
{

    private $table;
    public $id;
    public $name;
    public $repeatNumber;
    public $repeatType;
    public $repeatDays;
    public $endType;
    public $endDate;
    public $endNumber;
    public $CompanyNum;
    public $Type;
    public $date;

    function __construct($id = null)
    {
        $this->table = "boostapp.repetition_settings";
        if ($id != null) {
            $this->getRepititionById($id);
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

    function getRepititionById($id)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('id', '=',  $id)
            ->first();

        if($result != null) {
            foreach ($result as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    function getRepititionByCompanyNum($companyNum)
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

    function getLastThreeRepititionByCompanyNum($companyNum)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('CompanyNum', '=',  $companyNum)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();
        if (empty($result)) {
            return null;
        }
        return $result;
    }
    function getLastThreeRepititionByCompanyNumAndType($companyNum, $Type)
    {
        $result = DB::table($this->table)
            ->select('*')
            ->where('CompanyNum', '=',  $companyNum)
            ->where('Type', '=',  $Type)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();
        if (empty($result)) {
            return null;
        }
        return $result;
    }
    public static function insertNewRepitition($data)
    {
        try {
            $rowId = DB::table('boostapp.repetition_settings')->insertGetId(
                $data
            );
            return $rowId;
        } catch (Exception $e) {
            return $e;
        }
    }
}
