<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $BrandName
 * @property $Status
 * @property $FinalCompanynum
 * @property $YaadNumber
 * @property $ShowBrand
 *
 * Class Brand
 */
class Brand extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.brands';

//    public function __construct($id = null)
//    {
//        if($id != null){
//            $this->setData($id);
//        }
//    }

    public function setData($id){
        $brand = DB::table($this->table)->where("id", $id)
            ->where("CompanyNum", Auth::user()->CompanyNum)
            ->where("Status", 0)
            ->first();

        if($brand != null) {
            foreach ($brand as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function getBrandById($id){
        return DB::table($this->table)->where('id',"=",$id)->first();
    }

    public function getAllByCompanyNum($CompanyNum){
        return DB::table($this->table)
            ->where('CompanyNum',"=",$CompanyNum)
            ->where("Status","=",0)
            ->get();
    }

    /**
     * @param $CompanyNum
     * @return self[]|null
     */
    public static function getBrandsByCompany($CompanyNum): ?array
    {
        return DB::table(self::getTable())
            ->where('CompanyNum',"=",$CompanyNum)
            ->get();
    }

    /** return number of active brands
     * @param $CompanyNum
     * @return mixed
     */
    public static function countActive($CompanyNum){
        return self::where('CompanyNum' ,$CompanyNum)
                ->where('Status' ,0)
                ->count();
    }
    public function getMainBranchId($CompanyNum) {
        $branch = DB::table($this->table)
            ->select('id')
            ->where('CompanyNum',"=",$CompanyNum)
            ->where("Status","=",0)
            ->orderBy('id', 'ASC')
            ->first();
         return $branch->id ?? 0;
    }
    public function isMainBranch($CompanyNum ,$branchId) {
        $branch = DB::table($this->table)
            ->select('id')
            ->where('CompanyNum',"=",$CompanyNum)
            ->where("Status","=",0)
            ->orderBy('id', 'ASC')
            ->first();
        if($branch) {
            return $branch->id == $branchId;
        }
        return false;
    }
    public function getBrand($CompanyNum,$id){
        return DB::table($this->table)
            ->where('CompanyNum',"=",$CompanyNum)
            ->where('Status','=',0)
            ->where('id','=',$id)
            ->get();
    }

    public function getActiveBrand($CompanyNum){
        return DB::table($this->table)
            ->where('CompanyNum',"=",$CompanyNum)
            ->where('Status','=',0)
            ->get();
    }

    public function getSectionsSortedByBranch($CompanyNum){
        $Branches = $this->getActiveBrand($CompanyNum);
        if (!count($Branches))
            $Branches[] = $this->getMainBranchObject($CompanyNum);

        $sectionObj = new Section();
        foreach ($Branches as $Branch){
            $Branch->Sections = $sectionObj->GetAllRoomsByBranch($CompanyNum, $Branch->id);
            foreach ($Branch->Sections as $section)
                if ($section->Status == 0)
                    $Branch->hasActiveSection = 1;
        }

        return $Branches;
    }

    public function getMainBranchObject($CompanyNum = 0){
        $res = new stdClass();
        $res->id = 0;
        $res->BrandName = lang('primary_branch');
        $res->CompanyNum = $CompanyNum;
        return $res;
    }

    public function getBrandByCompanyNumAndId($CompanyNum, $brandId){
        return DB::table($this->table)
            ->where('CompanyNum',"=",$CompanyNum)
            ->where('id', "=" , $brandId)
            ->first();
    }

    public function getNameBranches($CompanyNum) {
        return DB::table($this->table)
            ->select('id', 'BrandName as name', 'Status as status')
            ->where('CompanyNum', "=", $CompanyNum)
            ->get();
    }

    /**
     * @param $companyNum
     * @param int $brandId
     * @return string
     */
    public static function getBranchName($companyNum, int $brandId): string
    {
        if ($brandId === 1 ) {
            return lang('main');
        }
        $branchName =self::where('CompanyNum',"=",$companyNum)
            ->where('id', "=" , $brandId)
            ->Pluck('BrandName');
        return $branchName ?? lang('main');
    }


}
