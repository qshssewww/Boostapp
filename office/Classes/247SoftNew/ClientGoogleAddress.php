<?php

/**
 * @property $id
 * @property $client_id
 * @property $place_id
 * @property $address
 * @property $lat_lng
 * @property $created_at
 * @property $updated_at
 *
 * Class ClientGoogleAddress
 */
class ClientGoogleAddress extends \Hazzard\Database\Model
{
    protected $table = '247softnew.client_google_address';
    public const GOOGLE_API_KEY = 'AIzaSyAfA8N-khOrkjYh2Z6pVjlJRymV2bIM8jM';

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public static function getBusinessAddress($CompanyNum)
    {
        return DB::table(self::getTable())
            ->join('247softnew.client', self::getTable() . '.client_id', '=', '247softnew.client.id')
            ->where('247softnew.client.FixCompanyNum', '=', $CompanyNum)
            ->select(self::getTable() . '.*')
            ->first();
    }

    public static $createRules = [
            "id" => "integer",
            "client_id" => 'required|integer|exists:247softnew.client,id',
            "place_id" => "required",
            "address" => "max:255",
            "lat_lng" => "max:255",
            "city_id" => "integer"
        ];
    
}