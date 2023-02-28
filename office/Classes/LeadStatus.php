<?php
    require_once 'Utils.php';

    class LeadStatus extends Utils {
        private static $table = "boostapp.leadstatus";

        protected $id;
        protected $CompanyNum;
        protected $PipeId;
        protected $Title;
        protected $Sort;
        protected $Status;
        protected $Act;

        public function __construct($id = null) {
            if ($id) {
                $this->setData($id);
            }
        }

        public function setData($id) {
            $data = DB::table(self::$table)->where("id", $id)->first();
            if ($data) {
                foreach ($data as $key => $value) {
                    $this->__set($key, $value);
                }
            }
        }

        public function __set($name, $value) {
            if(property_exists($this,$name)){
                $this->$name = $value;
            }
        }
    
        public function __get($name) {
            if(property_exists($this, $name)){
                return $this->$name;
            }
            return null;
        }

        public static function insert_into_table($data) {
            $id =  DB::table(self::$table)->insertGetId($data);
            return $id;
        }

        public static function getLeadStatuses($companyNum, $pipeId, $act) {
            return DB::table(self::$table)->where("CompanyNum", $companyNum)
                ->where("Act", $act)
                ->where("PipeId", $pipeId)
                ->orderBy("Sort")
                ->get();
        }

        public static function check_lead_status($companyNum,$pipeCategory,$id){
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->where("PipeId", $pipeCategory)->where("id", $id)->first();
        }

        public static function getNewLeadStatus($companyNum){
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->orderBy('id', 'asc')->first();
        }

        public static function create_default_lead_statuses($companyNum, $pipeId) {
            $lead_statuses = DB::table(self::$table)->where("CompanyNum", 999)->get();
            $id = null;
            foreach ($lead_statuses as $status) {
                $tempId = DB::table(self::$table)->insertGetId([
                    "CompanyNum" => $companyNum,
                    "PipeId" => $pipeId,
                    "Title" => $status->Title,
                    "Sort" => $status->Sort,
                    "Status" => $status->Status,
                    "Act" => $status->Act
                ]);
                if ($status->Sort == 1) {
                    $id = $tempId;
                }
            }
            return $id;
        }

        public static function getLeadStatus($companyNum, $leadId){
           return DB::table(self::$table)->where('id', '=', $leadId)->where('CompanyNum', '=', $companyNum)->first();
        }

        public static function getLeadStatusByActAndStatus($companyNum, $act , $status){
            return DB::table(self::$table)
                ->where('CompanyNum', '=', $companyNum)
                ->where('Act', '=', $act)
                ->where('Status', '=', $status)
                ->orderBy('Sort', 'ASC')
                ->get();
        }

        public static function getAllLeadStatusesByPipeId($companyNum, $pipeId){
            return DB::table(self::$table)
                ->where('CompanyNum' ,'=', $companyNum)
                ->where('PipeId','=', $pipeId)
                ->orderBy('Sort', 'ASC')
                ->orderBy('Status', 'ASC')
                ->get();
        }

        /**
         * @param $companyNum
         * @param $pipeId
         * @return array leadStatuses by pipeId
         * @throws Exception if failed to find leadStatuses, at least one.
         */
        public static function getActiveLeadStatusesByPipeId($companyNum, $pipeId): array
        {
            $res =  DB::table(self::$table)
                ->where('CompanyNum' ,'=', $companyNum)
                ->where('PipeId','=', $pipeId)
                ->where('Status', '=', '0')
                ->orderBy('Sort', 'ASC')
                ->get();
            if(!$res || count($res) == 0)
                throw new Exception('failed to find lead statuses, PipeId= ' . $pipeId);

            return $res;
        }

        /**
         * @param $companyNum
         * @param $pipeId
         * @param $act
         * @return string id of leadStatus by pipeId and Act
         * @throws Exception if failed to find leadStatus
         */
        public static function getLeadStatusByPipeIdByAct($companyNum, $pipeId, $act): string
        {
            $res = DB::table(self::$table)
                ->select('id')
                ->where('CompanyNum' ,'=', $companyNum)
                ->where('PipeId','=', $pipeId)
                ->where('Act', '=', $act)
                ->first();
            if(!$res) {
                throw new Exception('failed to find lead status, PipeId= ' . $pipeId . ', Act= ' . $act);
            }

            return $res->id;
        }
        public function getLastSortNum($companyNum, $pipeId){
            $res = DB::table(self::$table)
                ->select("Sort")
                ->where('CompanyNum' ,'=', $companyNum)
                ->where('PipeId','=', $pipeId)
                ->orderBy('Sort', 'DESC')
                ->first();
            return $res->Sort;
        }
        public function getCountActiveLeadStatusByPipeId($companyNum, $pipeId){
            return DB::table(self::$table)
                ->where('CompanyNum' ,'=', $companyNum)
                ->where('PipeId','=', $pipeId)
                ->where('Status', '0')
                ->count();
        }

        public function update() {
            $leadStatusArr = $this->createArrayFromObj($this);
            $res = DB::table(self::$table)->where("id", $this->id)->update($leadStatusArr);
            return $res;
        }
        public function deleteAllLeadStatusByPipeId($PipeId){
            return DB::table(self::$table)->where("PipeId", $PipeId)->delete();
        }

        public function getPipeTitles($companyNum, $pipeId){
            return DB::table(self::$table)
                ->where('CompanyNum', $companyNum)
                ->where('PipeId', $pipeId)
                ->where('Status', 0)
                ->where('Act', 0)
                ->orderBy('Sort', 'ASC')
                ->get();
        }

        public static $updateRules =[
            'id' => 'required|integer',
            'Title' => 'min:1|max:70|required_if:Status,',
            'Status' => 'integer|between:0,1|required_if:Title,'
        ];

        public static $CreateRules =[
            'CompanyNum' => 'required|integer',
            'PipeId' => 'required|integer',
            'Title' => 'required|min:1|max:70',
            'Sort'=> 'required|integer',
            'Status' => 'integer|between:0,1'
        ];
    }
?>
