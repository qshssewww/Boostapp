<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $Status
 * @property $CategoryName
 * @property $Favorite
 * Class MeetingCategories
 */

class MeetingCategories extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_category';

    /**
     * @param $companyNum
     * @return array
     */
    public function getAllByCompanyNum($companyNum){
        return DB::table($this->table)
            ->select('id','CategoryName')
            ->where('CompanyNum', $companyNum)
            ->where('Status','=','1')
            ->get();
    }

    /**
     * @param int $id
     * @param int $status
     * @return int
     */
    public static function changeFavorite(int $id, int $status) :int
    {
        return self::where('id', '=', $id)
            ->update(['Favorite' => $status]);
    }

    public static $updateRules =[
        'id' => 'required|integer',
        'CategoryName' => 'min:1|max:100',
        'CompanyNum' =>'required|integer',
        'Status' => 'integer|between:0,1'
        ];

    public static $CreateRules =[
        'CategoryName' => 'min:1|max:100',
        'CompanyNum' => 'required|integer'
    ];

}