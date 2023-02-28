<?php 
require_once __DIR__ . '/../../app/controllers/responses/BaseResponse.php';
require_once '../Classes/247SoftNew/ClientGoogleAddress.php';
require_once '../Classes/247SoftNew/SoftClient.php';
require_once '../Classes/City.php';
require_once __DIR__ . '/LoggerService.php';

class GoogleAddressService {

    /**
     * insertClientGoogleAddress function
     *
     * @param array $data
     * @return bool
     */
    public static function insertClientGoogleAddress(array $data) :bool{

        $Response = new BaseResponse();

        try {
            if(Auth::guest()){
                throw new Exception(lang('action_failed_footernew'));
            }
    
            if(Auth::UserCan('1')){
                $GoogleAddressObject = new ClientGoogleAddress();
                $CompanyNum = Auth::user()->CompanyNum ?? 0;
                $Client = SoftClient::getRow($CompanyNum, 'FixCompanyNum');
                $GoogleAddressObject->client_id = $Client->id;
                $data['city_id'] = (new City())->getCityIdByName($data['place_city']);
                if(empty($data['city_id'])) unset($data['city_id']);
                unset($data['place_city']);
        
                foreach($data as $key => $value){
                    $GoogleAddressObject->$key = $value;
                }
        
                $validator = Validator::make($GoogleAddressObject->getAttributes(), ClientGoogleAddress::$createRules);
        
                if($validator->passes()){
                    if(!$GoogleAddressObject->save()){
                        $Response->setError(lang('action_cancled'));
                    } 
                } else $Response->setError(lang('action_cancled'));
                
                return $Response->getData();
    
            } else throw new Exception(lang('action_cancled'));
            
        } catch (\Throwable $err) {
            LoggerService::error($err, LoggerService::CATEGORY_CLIENT_GOOGLE_ADDRESS);
            $Response->setError(lang('action_cancled'));
            return $Response->getData();
        }
    }
}
?>