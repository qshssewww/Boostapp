<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/services/GoogleAddressService.php';

class GoogleAddressController extends BaseController{

    /**
     * insertNewGoogleAddress function
     *
     * @param string $place_id
     * @param string $address
     * @param string $lat_lng = ""
     * @return bool
     */
    public function insertNewGoogleAddress(string $place_id, string $address, string $lat_lng = "", string $place_city = ""):bool{
        $DataObject = ['place_id' => $place_id, 'address' => $address, 'lat_lng' => $lat_lng, 'place_city' => $place_city];
        return GoogleAddressService::insertClientGoogleAddress($DataObject);
    }
}