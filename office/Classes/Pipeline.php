<?php

require_once "Utils.php";

class Pipeline extends Utils {
    /**
     * @var $id int
     */
    protected $id;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;

    /**
     * @var $Brands int
     */
    protected $Brands;

    /**
     * @var $MainPipeId int
     */
    protected $MainPipeId;
    /**
     * @var $PipeId int
     */
    protected $PipeId;

    /**
     * @var $ClientId int
     */
    protected $ClientId;

    /**
     * @var $FirstName string
     */
    protected $FirstName;

    /**
     * @var $LastName string
     */
    protected $LastName;

    /**
     * @var $CompanyName string
     */
    protected $CompanyName;

    /**
     * @var $Email string
     */
    protected $Email;

    /**
     * @var $ContactInfo string
     */
    protected $ContactInfo;

    /**
     * @var $UserId int
     */
    protected $UserId;

    /**
     * @var $ItemId int
     */
    protected $ItemId;

    /**
     * @var $Tasks string
     */
    protected $Tasks;

    /**
     * @var $Status int
     */
    protected $Status;

    /**
     * @var $TaskStatus int
     */
    protected $TaskStatus;

    /**
     * @var $StatusColor string
     */
    protected $StatusColor;

    /**
     * @var $Info string
     */
    protected $Info;

    /**
     * @var $Source string
     */
    protected $Source;

    /**
     * @var $SourceId int
     */
    protected $SourceId;

    /**
     * @var $ImportSource int
     */
    protected $ImportSource;

    /**
     * @var $Note string
     */
    protected $Note;

    /**
     * @var $ClassTest string
     */
    protected $ClassTest;

    /**
     * @var $MemberShip string
     */
    protected $MemberShip;

    /**
     * @var $ClassInfo string
     */
    protected $ClassInfo;

    /**
     * @var $ClassInfoNames string
     */
    protected $ClassInfoNames;

    /**
     * @var $BrandsNames string
     */
    protected $BrandsNames;

    /**
     * @var $Dates DateTime
     */
    protected $Dates;

    /**
     * @var $NoteDates DateTime
     */
    protected $NoteDates;

    /**
     * @var $AgentId int
     */
    protected $AgentId;

    /**
     * @var $StatusTimeLine string
     */
    protected $StatusTimeLine;

    /**
     * @var $ReasonsId int
     */
    protected $ReasonsId;

    /**
     * @var $FreeText string
     */
    protected $FreeText;

    /**
     * @var $StatusFilter int
     */
    protected $StatusFilter;

    /**
     * @var $ConvertDate DateTime
     */
    protected $ConvertDate;

    /**
     * @var $additional_data string
     */
    protected $additional_data;

    private $table;

    public function __construct($id = null){
        $this->table = "boostapp.pipeline";
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

    public function __set($name, $value){
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name){
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert_into_table($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function getByClientId($clientId) {
        $id = DB::table($this->table)->where("ClientId", $clientId)->first()->id;
        if ($id) {
            $this->setData($id);
            return 1;
        }
        return 0;
    }

    public function getRow($id){
        $returnedObj = DB::table($this->table)->where("id", "=", $id)->first();
        return $returnedObj;
    }
    public function getAllRows(){
        $dataArray = DB::table($this->table)->get();
        return $dataArray;
    }

    public function update() {
        $clientArr = $this->createArrayFromObj($this);
        $res = DB::table($this->table)->where("id", $this->id)->update($clientArr);
        return $res;
    }

    public function GetOpenLeads($CompanyNum)
    {
        $LeadsOpens = DB::table("pipeline")->select("client.id", "pipeline.PipeId")
            ->leftJoin("client","client.id","=","pipeline.ClientId")
            ->leftJoin("leadstatus","leadstatus.id","=","pipeline.PipeId")
            ->where('client.CompanyNum','=', $CompanyNum)
            ->where('pipeline.CompanyNum','=', $CompanyNum)
            ->where('pipeline.StatusFilter', '=', '0')
            ->where("leadstatus.Act","=",0)
//            ->orderBy("client.id")
            ->get();

        return $LeadsOpens;
    }

    /**
     * GetOpenLeadsByDates function
     * @param int $CompanyNum
     * @param string $startDate
     * @param string $endDate
     * @return stdClass[]|null
     */
    public function GetOpenLeadsByDates(int $CompanyNum, string $startDate, string $endDate): ?array{
        return DB::table($this->table)->select("$this->table.*")->leftJoin("client","client.id","=","pipeline.ClientId")->leftJoin("leadstatus","leadstatus.id","=","pipeline.PipeId")
        ->where('client.CompanyNum','=', $CompanyNum)
        ->where("$this->table.CompanyNum",'=', $CompanyNum)
        ->where("$this->table.StatusFilter", '=', '0')
        ->where("leadstatus.Act","=", 0)
        ->whereBetween('pipeline.Dates', [$startDate." 00:00:00" , $endDate." 23:59:59"])
        ->orderBy("client.id")
        ->get();
    }

    public function GetCurrenDayLeads($CompanyNum){
            $beginOfDay = date('Y-m-d H:i:s', strtotime("today"));
        $EndOfDay = date('Y-m-d H:i:s', strtotime("tomorrow") -1);
        $CurrenDayLeads = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Dates','>=',$beginOfDay)
            ->where('Dates','<=',$EndOfDay)->get();

        return $CurrenDayLeads;
    }
    public function GetLastMonthLeads($CompanyNum){
        $StartMonth = date("Y-m-d", strtotime("first day of previous  month"));
        $EndMonth =date("Y-m-d", strtotime("last day of previous  month"));
        $LastMonthLeads = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Dates','>=',$StartMonth)
            ->where('Dates','<=',$EndMonth)
            ->where('StatusFilter','=',0)
            ->where('ConvertDate','=',null)->get();
        return $LastMonthLeads;
    }
    public function GetConvertLeads($CompanyNum){
        $ConvertLeads = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('StatusFilter','=',0)
            ->whereNotNull('ConvertDate')->get();
        return $ConvertLeads;
    }
    public function GetConvertLeadsCount($CompanyNum){
        $ConvertLeadsCount = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('StatusFilter','=',0)

            ->whereNotNull('ConvertDate')->count();
        return $ConvertLeadsCount;
    }
    public function GetLeadsAtLeatThirtyDays($CompanyNum){
        $date = date('Y-m-d',strtotime("-30 days"));
        $OpenLeadsCount = 0;
        $ConvertLeadsCount = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Dates','>=',$date)
            ->where('StatusFilter','=',1)
            ->whereNotNull('ConvertDate')->count();
        $LeadsOpens = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Dates','>=',$date)->get();

        $OpenLeadsCount = Count($LeadsOpens);
        if($OpenLeadsCount != 0) {

            $PercentConvertLeads = ($ConvertLeadsCount) / ($OpenLeadsCount);
            $PercentConvertLeads =number_format( $PercentConvertLeads * 100, 2 );
            $percentOpenLeads = 100 - $PercentConvertLeads;
//            $percentOpenLeads =number_format( $percentOpenLeads * 100, 2 );
        }
        else{
            $PercentConvertLeads = 0;
            $percentOpenLeads = 0;
        }
        $Leads = array(
            'AllLeads' => $OpenLeadsCount,
            'ConvertLeads' => $ConvertLeadsCount,
//            'OpenLeads' => $OpenLeadsCount,
            'PercentConvertLeads'=>round($PercentConvertLeads,0),
            'percentOpenLeads'=>round($percentOpenLeads,0)
        );
        return $Leads;

    }
    public function getPipeLineByPipeId($companyNum, $pipeId, $AgentId = null, $limit = 30){
        if(!$AgentId)
            $PipeLines = DB::table($this->table)
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum', '=', $companyNum)
                ->where('pipeline.PipeId', '=', $pipeId)
                ->where('client.Status', 2)
                ->orderBy('pipeline.TaskStatus', 'ASC')->orderBy('pipeline.NoteDates', 'ASC')->orderBy('pipeline.Dates', 'ASC')
                ->limit($limit)
                ->get();
        else
            $PipeLines = DB::table($this->table)
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum', '=', $companyNum)
                ->where('pipeline.PipeId', '=', $pipeId)
                ->where('pipeline.AgentId', '=', $AgentId)
                ->where('client.Status', 2)
                ->orderBy('pipeline.TaskStatus', 'ASC')->orderBy('pipeline.NoteDates', 'ASC')->orderBy('pipeline.Dates', 'ASC')
                ->limit($limit)
                ->get();

        return $PipeLines;
    }
    public function getCountPipeLineByPipeId($companyNum, $pipeId, $AgentId = null){
        if(!$AgentId)
            $CountPipeLines = DB::table($this->table)
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum', '=', $companyNum)
                ->where('pipeline.PipeId', '=', $pipeId)
                ->where('client.Status', 2)
                ->count();
        else
            $CountPipeLines = DB::table($this->table)
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum', '=', $companyNum)
                ->where('pipeline.PipeId', '=', $pipeId)
                ->where('pipeline.AgentId', '=', $AgentId)
                ->where('client.Status', 2)
                ->count();

        return $CountPipeLines;
    }

    public function updatePipeline($id,$data){
        return DB::table($this->table)->where("id","=", $id)->update($data);
    }

    public function checkPipeId($id,$companyNum){
        return DB::table($this->table)->where("ClientId","=", $id)->where("CompanyNum","=",$companyNum)->first();
    }

    public function updatePipelineByClientId($id,$data){
        return DB::table($this->table)->where("ClientId","=", $id)->update($data);
    }

    public function getAllLeadsBetweenDates($startDate, $endDate , $companyNum){
        return DB::table($this->table)->where('CompanyNum', '=', $companyNum)->whereBetween('Dates', array($startDate , $endDate))->orderBy('Dates', 'DESC')->get();
    }

        public function getLeadsByStatusFilter($startDate, $endDate, $companyNum, $statusFilter = 0){
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where("StatusFilter", "=", $statusFilter)
            ->whereBetween('Dates', [$startDate." 00:00:00" , $endDate." 23:59:59"])
            ->orderBy('Dates', 'DESC')
            ->get();
    }
}
