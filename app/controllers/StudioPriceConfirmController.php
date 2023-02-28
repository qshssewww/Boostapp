<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/Classes/StudioPriceConfirm.php';

class StudioPriceConfirmController extends BaseController {


    /**
     * insertPriceConfirm function
     * @param int $status
     * @return bool
     */
    public function insertPriceConfirm(int $status){
        $response = StudioPriceConfirm::insertNew($status);
        return $this->json(['success' => (bool)$response]);
    }


    public function getByCompanyNum(int $companyNum){
        return StudioPriceConfirm::getByCompanyNum($companyNum);
    }

}


