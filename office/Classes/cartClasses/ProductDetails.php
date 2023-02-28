<?php
require_once __DIR__ . '/../ItemDetails.php';


/**
 * @property $itemDetailsId
 * @property $itemId
 * @property $companyNum
 * @property $inventory
 * @property $used
 * @property $colorsId
 * @property $sizesId
 * @property $sizesName
 * @property $colorsName
 * @property $colorsHex
 * Class ProductDetails
 */
class ProductDetails extends \Hazzard\Database\Model
{
    private const ITEM_COLORS = 'boostapp.item_colors';
    private const ITEM_SIZES = 'boostapp.item_sizes';
    protected $table = "boostapp.item_details";

    /**
     * @param int $itemId
     * @param int $companyNum
     * @return ItemDetails[]
     */
    public static function getItemDetails(int $itemId, int $companyNum): array
    {
        return self::select(
            self::getTable() . ".itemId as item_id",
            self::getTable() . ".id as itemDetailsId",
            self::getTable() . ".used",
            self::getTable() . ".colors as colorsId",
            self::getTable() . ".sizes as sizesId",
            self::getTable() . ".inventory as inventory",
            self::ITEM_SIZES . ".name as sizesName",
            self::ITEM_COLORS . ".name as colorsName",
            self::ITEM_COLORS . ".hex as colorsHex")
            ->leftJoin(self::ITEM_COLORS , self::getTable().".colors", '=', self::ITEM_COLORS.".id")
            ->leftJoin(self::ITEM_SIZES , self::getTable().".sizes", '=', self::ITEM_SIZES.".id")
            ->where(self::getTable() . ".CompanyNum","=",$companyNum)
            ->where(self::getTable() . ".itemId","=",$itemId)
            ->where(self::getTable() . ".Status", "=", ItemDetails::STATUS_ACTIVE)
            ->get();
    }

    /**
     * @return string
     */
    public function getColorNameAndHex(): string
    {
        if(is_numeric($this->colorsId) && $this->colorsId > 0) {
            return $this->colorsName . '__' . $this->colorsHex;
        }
        return '';
    }

    /**
     * @return int
     */
    public function getInventoryNow(): int
    {
        if(is_numeric($this->inventory) && $this->inventory > 0) {
            if(is_numeric($this->used)) {
                return $this->inventory - $this->used;
            }
            return $this->inventory;
        }
        return 0;
    }

}
