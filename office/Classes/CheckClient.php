<?php



require_once "Utils.php";

class CheckClient extends Utils{
    protected $id;

    protected $CompanyNum;

    protected $count;

    protected $dayNotChanged;

    protected $date;

    private  $table;

    public function __construct()
    {
        $this->table = "CheckClient";
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


    public function GetCompanyByLastDate ($companyNum){
        return DB::table($this->table)
            ->where("CompanyNum",'=',$companyNum)
            ->orderBy('date', 'desc')
            ->first();
    }
    private function UpdateData($company){
        $CompanyUpdate =["dayNotChanged" => $company->dayNotChanged + 1];
        return DB::table($this->table)
            ->where('id', $company->id)
            ->update($CompanyUpdate);
    }
    private  function InsertData($ActiveNum,$CompanyNum){
        $CompanyData= ["CompanyNum"=>$CompanyNum,"count"=>$ActiveNum,"dayNotChanged"=>0];
        return DB::table($this->table)->insertGetId($CompanyData);
    }
    public function setData($ActiveNum,$CompanyNum){
        $company = $this->GetCompanyByLastDate($CompanyNum);
        if(!$company || $ActiveNum != $company->count) {
            $this->InsertData($ActiveNum,$CompanyNum);
        } else {
            $this->UpdateData($company);
        }

    }

    /**
     * @param $companyNum
     */
    public function GetCounterOfLastThirtyDays($companyNum){
        $date = date('Y-m-d H:i:s',strtotime("-30 days"));
        $CheckCounter = [];
        $companies = DB::table($this->table)->where('CompanyNum','=',$companyNum)->where("date",'>=',$date)->get();
        foreach ($companies as $company){
            for ($i=0; $i <= $company->dayNotChanged ; $i++ ){
                $CheckCounter[] = $company->count;
            }
        }
        if(count($CheckCounter) < 30 )
        {
            $len = count($CheckCounter);
            for($i = $len ; $i < 30 ; $i++){
                $CheckCounter[] = 0;
            }
        }
        return $CheckCounter;
    }
}
