<?php



require_once "Utils.php";

//TODO: Should the name of the class be change to ClientMedical ??
class clientmedical extends Utils{
    private $id;
    private $CompanyNum;
    private $ClientId;
    private $Content;
    private $TillDate;
    private $Dates;
    private $UserId;
    private $Status;
    private $table;

    public function __construct()
    {
        $this->table = "clientmedical";
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

    public function GetMdicalByClientId($CompanyNum,$ClientId){
        $Mediacl = DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientId)->whereNull('TillDate')->where('Status', '=', '0')
            ->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientId)->where('TillDate', '>=', date('Y-m-d'))->where('Status', '=', '0')
            ->orderBy('Dates','DESC')
            ->first();
        return $Mediacl;

    }
    public function getAllMedicalByClientId($CompanyNum,$ClientId){
        $Mediacl = DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientId)->whereNull('TillDate')->where('Status', '=', '0')
            ->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientId)->where('TillDate', '>=', date('Y-m-d'))->where('Status', '=', '0')
            ->orderBy('Dates','DESC')
            ->get();
        return $Mediacl;

    }
    public function editMedicalStatus($clientId, $medicalId, $status) {
        return DB::table($this->table)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->where('ClientId', $clientId)
            ->where('id', $medicalId)
            ->update(array("Status" => $status, 'Dates' => date('Y-m-d H:i:s')));
    }

    public function editClientMedicalContent($content, $clientId, $medicalId, $tillDate = null){
        $update = DB::table($this->table)
            ->where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->where('id', $medicalId)
            ->where('ClientId', $clientId)
            ->update(array("Content" => $content, 'Dates' => date('Y-m-d H:i:s'), 'TillDate' => $tillDate));
        return $update ? DB::table($this->table)->where('id', $medicalId)->first() : 0;
    }

    public function addClientMedicalRecord($companyNum, $clientId,$content, $tillDate, $date, $userId){
        $res = DB::table($this->table)->insertGetId([
                'CompanyNum' => $companyNum,
                'ClientId'=>$clientId,
                'Content' => $content,
                'UserId'=>$userId,
                'TillDate'=>$tillDate,
                'Dates'=>$date,
                'Status'=>0
            ]);
        return $res;
    }




}