<?php

/**
 * @property $id
 * @property $CheckoutOrderItemId
 * @property $CoachId
 * @property $DiaryId
 * @property $DurationMin
 * @property $Date
 * @property $Time
 */
class CheckoutOrderItemDetails extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.checkout_order_item_details';

    /**
     * @param $checkoutOrderItemId
     * @return CheckoutOrderItemDetails
     */
    public static function getByCheckoutOrderItemId($checkoutOrderItemId): CheckoutOrderItemDetails
    {
        return self::where('CheckoutOrderItemId', $checkoutOrderItemId)->first();
    }

    /**
     * @param int $checkoutOrderItemId
     * @param array $item
     * @return CheckoutOrderItemDetails
     */
    public static function creatByCartItem(int $checkoutOrderItemId, array &$item): CheckoutOrderItemDetails {
        $CheckoutOrderItemDetails = new CheckoutOrderItemDetails();
        $CheckoutOrderItemDetails->CheckoutOrderItemId = $checkoutOrderItemId;

        if(isset($item['coachId'])) {
            $CheckoutOrderItemDetails->CoachId = $item['coachId'];
            unset($item['coachId']);
        }
        if(isset($item['diaryId'])) {
            $CheckoutOrderItemDetails->DiaryId = $item['diaryId'];
            unset($item['diaryId']);
        }
        if(isset($item['durationMin'])) {
            $CheckoutOrderItemDetails->DurationMin = $item['durationMin'];
            unset($item['durationMin']);
        }
        if(isset($item['date'])) {
            $CheckoutOrderItemDetails->Date = $item['date'];
            unset($item['date']);
        }
        if(isset($item['time'])) {
            $CheckoutOrderItemDetails->Time = $item['time'];
            unset($item['time']);
        }

        $CheckoutOrderItemDetails->save();
        return $CheckoutOrderItemDetails;
    }

}
