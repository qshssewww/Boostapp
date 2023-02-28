<?php

require_once "Utils.php";

Class ClassLog extends Utils {
    protected $id;
    protected $CompanyNum;
    protected $ClassId;
    protected $ClientId;
    protected $Status;
    protected $Dates;
    protected $UserName;
    protected $numOfClients;

    private static $table = "boostapp.classlog";

    public function __construct($id = null) {
        if ($id != null) {
            $this->setData($id);
        }
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function setData($id) {
        $data = DB::table(self::$table)->where("id", "=", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public static function insertNewData($data) {
        return DB::table(self::$table)->insertGetId($data);
    }

    public static function getLogByClassId($id) {
        return DB::table(self::$table)
            ->where("ClassId", $id)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->get();
    }

    public static function getDescLogByClassId($id) {
        return DB::table(self::$table)
            ->where("ClassId", $id)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->orderBy('Dates', 'DESC')
            ->get();
    }

    public function deleteLogByClientId($CompanyNum, $clientId, $classId) {
        return DB::table(self::$table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $clientId)
            ->where('ClassId', '=', $classId)
            ->delete();
    }
}