<?php
/**
 * @property $id
 * @property $branch_id
 * @property $place_id
 * @property $address
 * @property $lat_lng
 * @property $created_at
 * @property $updated_at
 *
 * Class BranchGoogleAddress
 */

class BranchGoogleAddress extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.branch_google_address';
    public const GOOGLE_API_KEY = 'AIzaSyAfA8N-khOrkjYh2Z6pVjlJRymV2bIM8jM';

    public static function getAddressByBranchId($branchId) {
        $address = self::where('branch_id', $branchId)->first();
        return $address ? $address->toArray() : null;
    }
}