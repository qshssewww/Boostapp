<?php
require_once "../Classes/Coupons.php";

class CouponController
{
    /**
     * @param $rawData
     */
    public function deleteCoupon($rawData)
    {
        $data = (object)$rawData;
        if ($data && isset($data->id)) {
            $coupons = new Coupons();
            $coupon = $coupons->getSingleById($data->id);
            if ($coupon->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            $res = $coupons->deleteById($data->id);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }

    /**
     * @param $rawData
     */
    public function disableCoupon($rawData)
    {
        $data = (object)$rawData;
        if ($data && isset($data->id) && isset($data->disabled)) {
            $coupons = new Coupons();
            $coupon = $coupons->getSingleById($data->id);
            if ($coupon->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            $res = $coupons->updateById($data->id, array('disabled' => $data->disabled));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }

    /**
     * @param $rawData
     */
    public function editSingleCoupon($rawData)
    {
        $data = (object)$rawData;
        if ($data && isset($data->id) && isset($data->Title) && isset($data->Amount)) {
            $coupons = new Coupons();

            $coupon = $coupons->getSingleById($data->id);

            if ($coupon->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }

            $updateData = array(
                "Title" => $data->Title,
                "Amount" => (float)$data->Amount,
                "Code" => $data->Code,
                "EndDate" => isset($data->EndDate) && $data->EndDate != "" ? $data->EndDate : null,
                "StartDate" => isset($data->StartDate) && $data->StartDate != "" ? $data->StartDate : date('Y-m-d'),
                "timeLimit" => isset($data->timeLimit) ? $data->timeLimit : 0,
                "Limit" => isset($data->Limit) ? $data->Limit : "-1",
                "isPercentage" => isset($data->isPercentage) ? $data->isPercentage : "0",
                "limitForProducts" => isset($data->limitForProducts) ? $data->limitForProducts : null,
            );
            $res = $coupons->updateById($data->id, $updateData);
            $res->edited = 1;
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }

    /**
     * @param $rawData
     */
    public function getCoupons($rawData)
    {
        $data = (object)$rawData;
        $coupons = new Coupons();
        $res = $coupons->getMultipleByCompanyNum(Company::getInstance()->CompanyNum);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $rawData
     */
    public function getSingleCoupon($rawData)
    {
        $data = (object)$rawData;
        if ($data && isset($data->id)) {
            $coupons = new Coupons();
            $res = $coupons->getSingleById($data->id);
            if ($res->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }

    /**
     * @param $rawData
     * @return string|void
     */
    public function insertNewCoupon($rawData)
    {
        $data = (object)$rawData;
        if ($data && isset($data->Title) && isset($data->Code) && isset($data->Amount)) {
            $coupons = new Coupons();
            if ($coupons->testByCompanyNumAndCodeExists($data->Code, Company::getInstance()->CompanyNum)) {
                return "code_exists";
            }

            $insertData = array(
                "CompanyNum" => Company::getInstance()->CompanyNum,
                "Title" => $data->Title,
                "Code" => $data->Code,
                "timeLimit" => isset($data->timeLimit) ? $data->timeLimit : 0,
                "Amount" => (float)$data->Amount,
                "StartDate" => isset($data->StartDate) && $data->StartDate != "" ? $data->StartDate : date('Y-m-d'),
                "EndDate" => isset($data->EndDate) && $data->EndDate != "" ? $data->EndDate : null,
                "Limit" => isset($data->Limit) ? $data->Limit : "-1",
                "CountLimit" => '0',
                "isPercentage" => isset($data->isPercentage) ? $data->isPercentage : "0",
                "limitForProducts" => isset($data->limitForProducts) ? $data->limitForProducts : null,
            );
            $res = $coupons->insertNew($insertData);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }
}
