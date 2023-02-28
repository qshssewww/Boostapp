<?php
    require_once 'Utils.php';

    class PipelineCategory extends Utils {
        private static $table = "boostapp.pipeline_category";

        protected $id;
        protected $CompanyNum;
        protected $Title;
        protected $Status;
        protected $Act;
        protected $PipeAgentNew;
        protected $MaxRecord;

        public static $updateRules =[
            'id' => 'required|integer',
            'Title' => 'min:1|max:70|required_if:Status,',
            'Status' => 'integer|between:0,1|required_if:Title,'
        ];

        public static $CreateRules =[
            'CompanyNum' => 'required|integer',
            'Title' => 'required|min:1|max:70',
            'Status' => 'integer|between:0,1'
        ];
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

        public static function get_main_category($companyNum) {
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->where("Act", 1)->first();
        }

        public static function getPipelineCategories($companyNum) {
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->where("Status","=",0)->get();
        }
        public static function check_pipeline_category_exists($companyNum,$id){
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->where("id","=" ,$id)->where("Status","=",0)->get();
        }
        public static function getAllPipelineCategories($companyNum) {
            return DB::table(self::$table)->where("CompanyNum", $companyNum)->orderBy('id', 'ASC')->orderBy('Status', 'ASC')->get();
        }
        public static function getPipeLineById($companyNum, $id) {
            return DB::table(self::$table)->where('CompanyNum','=', $companyNum)->where('id','=', $id)->first();
        }

        public static function create_main_category($companyNum) {
            return DB::table(self::$table)->insertGetId([
                "CompanyNum" => $companyNum,
                "Title" => 'ראשי',
                "Status" => 0,
                "Act" => 1,
                "PipeAgentView" => 0,
                "MaxRecord" => 30
            ]);
        }

        public static function getAllCategories($companyNum){
            return DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->first();
        }

        public static function getMainPipelineCategory($companyNum, $mainPipeId){
            return DB::table(self::$table)->where('CompanyNum', '=', $companyNum)->where('id', '=', $mainPipeId)->first();
        }

        public function update() {
            $pipeLineCategoryArr = $this->createArrayFromObj($this);
            return DB::table(self::$table)->where("id", $this->id)->update($pipeLineCategoryArr);
        }
        public function delete() {
            return DB::table(self::$table)->where("id", $this->id)->delete();
        }
        public static function getPipelineCategoriesWithLeadStatus($companyNum) {
            return DB::table(self::$table.' as pc')
                ->leftJoin('leadstatus as ls', 'ls.PipeId', '=', 'pc.id')
                ->select('pc.id as pcId', 'pc.Title as pcName', 'pc.Act as pcDefault', 'pc.Status as pcStatus', 'ls.id as lsId', 'ls.Title as lsName', 'ls.Status as lsStatus', 'ls.Sort as Sort')
                ->where("pc.CompanyNum", $companyNum)
                ->orderBy('pcId', 'ASC')
                ->orderBy('Sort', 'ASC')
                ->get();
        }
    }
?>
