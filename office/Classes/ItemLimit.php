<?php


class ItemLimit
{

    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $ClubMembershipsId int
     */
    private $ClubMembershipsId;

    /**
     * @var $itemId int
     */
    private $itemId;

    /**
     * @var $userId int
     */
    private $userId;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $maxPurchase int
     */
    private $maxPurchase;

    /**
     * @var $gender string
     */
    private $gender;

    /**
     * @var $rank string
     */
    private $rank;

    /**
     * @var $startAge int
     */
    private $startAge;

    /**
     * @var $endAge int
     */
    private $endAge;

    /**
     * @var $seniority string
     */
    private $seniority;

    /**
     * @var $membership int
     */
    private $membership;

    /**
     * @var $Date DateTime
     */
    private $Date;
    /**
     * @var $GeneratedString string
     */
    private $GeneratedString;
    /**
     * @var $table string
     */
    private $table;

        /**
     * @var $customerStatus string
     */
    private $customerStatus;


    public function __construct($companyNum = null,$itemId = null){
        $this->table = "items_limit";
        if($companyNum != null && $itemId != null) {
            $limits = DB::table($this->table)->where("CompanyNum", "=", $companyNum)->andWhere("itemId", "=", $itemId)->get();
            foreach ($limits as $limit) {
                foreach ($limit as $key => $value) {
                    $this->__set($key, $value);
                }
            }
        }
    }

    public function createObjFromShop($data,$userId,$companyNum,$itemId){

        $this->userId = $userId;
        $this->CompanyNum = $companyNum;
        $this->itemId = $itemId;
        $this->maxPurchase = ($data["purchaseAmount"]!="false") ? $data["purchaseAmount"] : null;
        $this->startAge = ($data["age"]!="false") ? $data["age"]["fromAge"] : null;
        $this->endAge = ($data["age"]!="false") ? $data["age"]["toAge"] : null;
        $this->gender = ($data["gender"]!="false") ? $data["gender"] : null;
        $this->rank = ($data["rank"]!="false") ? json_encode($data["rank"]) : null;
        $this->seniority = ($data["seniority"]!="false") ? $data["seniority"]["date"] : null;
        $this->membership = ($data["memberships"]!="false") ? json_encode($data["memberships"]) : null;
        $this->Date = date('Y-m-d H:i:s');
        $this->GeneratedString = $data['string'];
        $this->customerStatus= ($data["status"]!="false") ? $data["status"] : null;
    }


    /**
     * @param $limits ItemLimit
     */
    public function insertItemLimit($limits = null){
        if($limits != null) {
            DB::table($this->table)->insert(
                array(
                    "itemId" => $limits->__get("itemId"),
                    "userId" => $limits->__get("userId"),
                    "CompanyNum" => $limits->__get("CompanyNum"),
                    "maxPurchase" => $limits->__get("maxPurchase"),
                    "gender" => $limits->__get("gender"),
                    "rank" => $limits->__get("rank"),
                    "startAge" => $limits->__get("startAge"),
                    "endAge" => $limits->__get("endAge"),
                    "seniority" => $limits->__get("seniority"),
                    "membership" => $limits->__get("membership"),
                    "Date" => $limits->__get("Date"),
                    "GeneratedString" => $limits->__get("GeneratedString"),
                    "customerStatus"=> $limits->__get("customerStatus")
                )
            );
        }
        elseif($this->itemId != null){
            DB::table($this->table)->insert(
                array(
                    "itemId" => $this->__get("itemId"),
                    "userId" => $this->__get("userId"),
                    "CompanyNum" => $this->__get("CompanyNum"),
                    "maxPurchase" => $this->__get("maxPurchase"),
                    "gender" => $this->__get("gender"),
                    "rank" => $this->__get("rank"),
                    "startAge" => $this->__get("startAge"),
                    "endAge" => $this->__get("endAge"),
                    "seniority" => $this->__get("seniority"),
                    "membership" => $this->__get("membership"),
                    "Date" => $this->__get("Date"),
                    "GeneratedString" => $this->__get("GeneratedString"),
                    "customerStatus"=> $this->__get("customerStatus")
                )
            );
        }
    }
    public function deleteLimits($id){
        DB::table($this->table)->where('itemId', '=', $id)->delete();
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

    public function getItemLimitByClubMemberships($ClubMembershipsId){
        return DB::table($this->table)
            ->select('id','maxPurchase', 'gender', 'rank', 'startAge',
                'endAge','seniority', 'customerStatus' ,'membership')
            ->where('ClubMembershipsId','=',$ClubMembershipsId)
            ->first();
    }

    public function updateById($id, $data){
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }

    public function getByItemId($itemId){
         return DB::table($this->table)
            ->select('id')
            ->where('itemId','=',$itemId)
            ->first();


     }

    public function deleteByClubMemberships($ClubMembershipsId){
        return DB::table($this->table)
            ->where('ClubMembershipsId','=',$ClubMembershipsId)
            ->delete();
    }





    public function createNewItemLimit($itemLimit) {
        return DB::table($this->table)->insertGetId(
            $itemLimit
        );
    }
    public static $createRules =[
        'id' => 'integer',
        'ClubMembershipsId' => 'required|exists:boostapp.club_memberships,id',
        'itemId' => 'required|exists:boostapp.items,id',
        'UserId' => 'exists:boostapp.users,id',
        'CompanyNum' => 'required|integer',
        'maxPurchase' => 'integer',
        'gender' => 'integer|between:0,2',
        'rank' => 'max:255',
        'startAge' => 'integer|between:0,120',
        'endAge' => 'integer|between:0,120|required_if:startAge,between:0,120',
        'seniority' => 'date_format:Y-m-d|before:tomorrow',
        'customerStatus' => 'integer|between:0,2',
    ];

    public static $updateRules =[
        'maxPurchase' => 'integer',
        'gender' => 'integer|between:0,2',
        'rank' => 'max:255',
        'startAge' => 'integer|between:0,120',
        'endAge' => 'integer|between:0,120|required_if:startAge,between:0,120',
        'seniority' => 'date_format:Y-m-d|before:tomorrow',
        'customerStatus' => 'integer|between:0,2',
    ];


}