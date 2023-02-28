<?php

/**
 * @property $id
 * @property $CheckoutOrderId
 * @property $Type
 * @property $Name
 * @property $Price
 * @property $Quantity
 * @property $DiscountType
 * @property $DiscountValue
 * @property $DiscountAmount
 * @property $ItemId
 * @property $ClassStudioDateId
 * @property $ClassTypeId
 * @property $ClientActivityId
 * @property $MembershipStartCount
 * @property $VariantId
 * @property $PackageManualStart
 * @property $PackageManualEnd
 */
class CheckoutOrderItem extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.checkout_order_item';

    public const TYPE_NOT_VALID_TYPE = 0;
    public const TYPE_PRODUCT = 1;
    public const TYPE_PACKAGE = 2;
    public const TYPE_LESSON = 3;
    public const TYPE_SERVICE = 4;
    public const TYPE_DEBT = 5;
    public const TYPE_GENERAL = 6;

    /**
     * @param $checkoutOrderId
     * @return CheckoutOrderItem[]
     */
    public static function getByCheckoutOrderId($checkoutOrderId): array
    {
        return self::where('CheckoutOrderId', $checkoutOrderId)->get();
    }

    /**
     * @return string
     */
    public function typeToString(): string
    {
        switch ((int)$this->Type) {
            case self::TYPE_PRODUCT :
                return 'product';
            case self::TYPE_PACKAGE :
                return 'package';
            case self::TYPE_LESSON :
                return 'lesson';
            case self::TYPE_SERVICE :
                return 'service';
            case self::TYPE_DEBT :
                return 'debt';
            case self::TYPE_GENERAL :
                return 'general';
            default:
                return '';
        }
    }

    /**
     * @param string $type
     * @return int
     */
    public static function StringToType(string $type): int
    {
        switch ($type) {
            case 'product':
                return self::TYPE_PRODUCT;
            case 'general':
                return self::TYPE_GENERAL;
            case 'package':
                return self::TYPE_PACKAGE;
            case 'lesson':
                return self::TYPE_LESSON;
            case 'service':
                return self::TYPE_SERVICE;
            case 'debt':
                return self::TYPE_DEBT;
            default:
                return 0;
        }
    }

    /**
     * @param int $checkoutOrderId
     * @param array $item
     * @param int $companyNum
     * @return CheckoutOrderItem
     */
    public static function creatByCartItem(int $checkoutOrderId, array &$item, int $companyNum): CheckoutOrderItem {
        $CheckoutOrderItem = new CheckoutOrderItem();
        $CheckoutOrderItem->CheckoutOrderId = $checkoutOrderId;
        if(isset($item['type'])) {
            $CheckoutOrderItem->Type = self::StringToType($item['type']);
            unset($item['type']);
        }
        if(isset($item['name'])) {
            $CheckoutOrderItem->Name = $item['name'];
            unset($item['name']);
        }
        if(isset($item['price'])) {
            $CheckoutOrderItem->Price = $item['price'];
            unset($item['price']);
        }
        if(isset($item['quantity'])) {
            $CheckoutOrderItem->Quantity = $item['quantity'] ?? 1;
            unset($item['quantity']);
        }
        $item['id'] = $item['id'] ?? 0;
        switch ($CheckoutOrderItem->Type) {
            case self::TYPE_PRODUCT :
            case self::TYPE_PACKAGE :
                $CheckoutOrderItem->ItemId = $item['id'];
                break;
            case self::TYPE_GENERAL :
                $CheckoutOrderItem->ItemId = ItemService::getGeneralItem($companyNum);
                break;
            case self::TYPE_LESSON :
                $CheckoutOrderItem->ClassStudioDateId = $item['id'];
                break;
            case self::TYPE_SERVICE :
                $CheckoutOrderItem->ClassTypeId = $item['id'];
                break;
            case self::TYPE_DEBT :
                $CheckoutOrderItem->ClientActivityId = $item['id'];
                break;
        }
        unset($item['id']);

        if(isset($item['membershipStartCount'])) {
            $CheckoutOrderItem->MembershipStartCount = $item['membershipStartCount'];
            unset($item['membershipStartCount']);
        }
        if(isset($item['variantId'])) {
            $CheckoutOrderItem->VariantId = $item['variantId'];
            unset($item['variantId']);
        }

        if(isset($item['packageManualStart'])) {
            $CheckoutOrderItem->PackageManualStart = $item['packageManualStart'];
            unset($item['packageManualStart']);
        }

        if(isset($item['packageManualEnd'])) {
            $CheckoutOrderItem->PackageManualEnd = $item['packageManualEnd'];
            unset($item['packageManualEnd']);
        }

        ///discount
        if(isset($item['discount'])) {
            $CheckoutOrderItem->DiscountType = $item['discount']['type'] ?? 1;
            $CheckoutOrderItem->DiscountValue = $item['discount']['value'] ?? 0;
            $CheckoutOrderItem->DiscountAmount = $item['discount']['amount'] ?? 0;
            unset($item['discount']);
        }
        $CheckoutOrderItem->save();
        return $CheckoutOrderItem;
    }

    public function getItemId(){
        switch ((int)$this->Type) {
            case self::TYPE_GENERAL:
            case self::TYPE_PACKAGE:
            case self::TYPE_PRODUCT :
                return $this->ItemId;
            case self::TYPE_LESSON :
                return $this->ClassStudioDateId;
            case self::TYPE_SERVICE :
                return $this->ClassTypeId;
            case self::TYPE_DEBT :
                return $this->ClientActivityId;
            default:
                return '';
        }
    }

}
