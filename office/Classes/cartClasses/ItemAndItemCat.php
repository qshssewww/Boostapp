<?php
require_once __DIR__ . '/../Item.php';

/**
 * @property $itemCat_id
 * @property $itemCat_name
 * @property $itemCat_favorite
 * @property $companyNum
 * @property $item_id
 * @property $item_department
 * @property $item_name
 * @property $item_price
 * @property $item_vaild
 * @property $item_vaildType
 * @property $item_favorite
 * @property $clubMemberShip_name
 * @property $item_payment //Payment
 * @property $item_balanceClass
// * @property $item_membershipStartCount //membershipStartCount
// * @property $item_membershipStartDate //membershipStartDate
// * @property $clubMemberships_id
// * @property $clubMemberShip_name
 *
 * Class StudioMeetingData
 */
class ItemAndItemCat extends \Hazzard\Database\Model
{
    private const ITEMS_TABLE_CAT = 'boostapp.item_cat';
    private const CLUB_MEMBERSHIPS_TABLE = 'boostapp.club_memberships';
    protected $table = "boostapp.items";

    /**
     * @param int $companyNum
     * @return ItemAndItemCat[]
     */
    public static function getProductsToCart(int $companyNum): array
    {
        return self::select(
            self::getTable() . ".id as item_id",
            self::getTable() . ".Department as item_department",
            self::getTable() . ".ItemName as item_name",
            self::getTable() . ".ItemPrice as item_price",
            self::getTable() . ".CompanyNum as companyNum",
            self::getTable() . ".Favorite as item_favorite",
            self::ITEMS_TABLE_CAT . ".id as itemCat_id",
            self::ITEMS_TABLE_CAT . ".Favorite as itemCat_favorite",
            self::ITEMS_TABLE_CAT . ".Name as itemCat_name")
            ->leftJoin(self::ITEMS_TABLE_CAT ,self::ITEMS_TABLE_CAT . ".id", '=', self::getTable().".ItemCat")
            ->where(self::getTable() . ".CompanyNum","=",$companyNum)
            ->where(self::getTable() . ".Status","=",Item::STATUS_ACTIVE)
            ->where(self::getTable() . ".Department","=",Item::DEPARTMENT_PRODUCT)
            ->orderBy(self::getTable() . ".ClubMembershipsId")
            ->orderBy(self::getTable() . ".ItemPrice")
            ->get();
    }

    /**
     * @param int $companyNum
     * @return ItemAndItemCat[]
     */
    public static function getPackagesToCart(int $companyNum): array
    {
        return  self::select(
            self::getTable() . ".CompanyNum as companyNum",
            self::getTable() . ".id as item_id",
            self::getTable() . ".Favorite as item_favorite",
            self::getTable() . ".Department as item_department",
            self::getTable() . ".ItemName as item_name",
            self::getTable() . ".ItemPrice as item_price",
            self::getTable() . ".Vaild as item_vaild",
            self::getTable() . ".Vaild_Type as item_vaildType",
            self::getTable() . ".BalanceClass as item_balanceClass",
            self::getTable() . ".Favorite as item_favorite",
            self::getTable() . ".Payment as item_payment",
            self::CLUB_MEMBERSHIPS_TABLE . ".id as clubMemberships_id",
            self::CLUB_MEMBERSHIPS_TABLE . ".ClubMemberShipName as clubMemberShip_name")
            ->leftJoin(self::CLUB_MEMBERSHIPS_TABLE , self::getTable().".ClubMembershipsId", '=',self::CLUB_MEMBERSHIPS_TABLE . ".id",  )
            ->where(self::getTable() . ".CompanyNum","=",$companyNum)
            ->where(self::getTable() . ".Status", Item::STATUS_ACTIVE)
            ->where(self::getTable() . ".Department","<",Item::DEPARTMENT_PRODUCT)
            ->where(self::getTable() . ".isPaymentForSingleClass", 0)
            ->orderBy(self::getTable() . ".ClubMembershipsId")
            ->orderBy(self::getTable() . ".ItemPrice")
            ->get();
    }


    /**
     * todo if need change display name
     * @return string
     */
    public function getItemName(): string
    {
        return $this->item_name ?? '';
        //If a new item (clubMemberships) and not a general item
//        if(!empty($this->clubMemberships_id) && isset($this->item_department) && $this->item_department !== 4) {
//            $name = $this->clubMemberShip_name ?? '';
//            $name .=
//            return
//        }
    }

}
