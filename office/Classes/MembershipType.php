<?php

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $Type
 * @property $Status
 * @property $CompanyNum
 * @property $Count
 * @property $ClassMemberType
 * @property $ViewClassAct
 * @property $ViewClass
 * @property $ViewClassDayNum
 * @property $OldId
 * @property $disabled
 * @property $order
 * @property $mainMembership
 *
 * Class MembershipType
 *
 */
class MembershipType extends Model
{
    /**
     * @var string
     */
    protected $table = "boostapp.membership_type";

    /**
     * @param $id
     * @return mixed
     */
    public static function getRow($id)
    {
        return self::where("id", $id)->first();
    }

    /**
     * @return array
     */
    public function createArrFromObj()
    {
        return $this->toArray();
    }

    /**
     * @param $id
     * @param $companyNum
     * @param $arr
     * @return void
     */
    public function updateById($id, $companyNum, $arr)
    {
        self::where("id", $id)
            ->where("CompanyNum", $companyNum)
            ->update($arr);
    }

    /**
     * @return MembershipType
     */
    public static function createDefaultMembership()
    {
        $defaultMembership = new self([
            "Type" => lang('club_membership_smart_link'),
            "Status" => 0,
            "CompanyNum" => Auth::user()->CompanyNum,
            "Count" => 0,
            "ClassMemberType" => null,
            "ViewClassAct" => 0,
            "ViewClass" => 3,
            "ViewClassDayNum" => 6,
            "OldId" => 0,
            "disabled" => 0,
            "order" => 1,
            "mainMembership" => 1
        ]);

        $defaultMembership->save();

        return $defaultMembership;
    }

    /**
     * @return mixed
     */
    public static function getDefaultMembership()
    {
        $defaultMembership = self::where('CompanyNum', Auth::user()->CompanyNum)
            ->where('mainMembership', "1")
            ->first();
        if (!$defaultMembership) {
            $defaultMembership = self::createDefaultMembership();
        }
        return $defaultMembership;
    }

    /**
     * @return int|null
     */
    public static function getDefaultMembershipId(): ?int
    {
        try {
            return self::getDefaultMembership()->id;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public static function getActiveMembershipTypes($CompanyNum)
    {
        return self::where('CompanyNum', $CompanyNum)->where('Status', 0)->get();
    }
}
