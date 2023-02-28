<?php
require_once '../../app/init.php';

require_once '../Classes/Company.php';
require_once '../Classes/PeriodicPayment.php';
require_once '../Classes/Payments.php';
require_once '../Classes/RegistrationFees.php';
require_once '../Classes/CompanyProductSettings.php';

require_once "../newShop/settingsAjax/CouponController.php";
require_once "../newShop/settingsAjax/OrderController.php";
require_once "../newShop/settingsAjax/ItemsController.php";

header('Content-Type: application/json');
$PeriodicPayment = new PeriodicPayment();
$Payments = new Payments();
$RegistrationFees = new RegistrationFees();
$couponController = new CouponController();
$orderController = new OrderController();
$itemsController = new ItemsController();
$Company = new Company();
$companyNum = Company::getInstance()->__get('CompanyNum');


if (Auth::guest()) exit;

if (Auth::userCan('31'))
{
    /**
     * @param $companyNum
     * @param $name
     * @param $post
     * @return void
     */
    function setCompanySetting($companyNum, $name, $post)
    {
        if (!isset($post[$name])) {
            echo json_encode(array(
                "Message" => "Required parameter \'" . $name . "\' is missing",
                "Status" => "Error"
            ));
        } else {
            $data = (int)$post[$name];
            $data = [$name => $data];

            (new CompanyProductSettings())->updateByCompanyNum($companyNum, $data);
            (new Item())->updateCompanyItems($data);

            LoggerService::info([
                "setting" => $name,
                "value" => $data[$name],
            ], LoggerService::CATEGORY_NOTIFICATION_SETTINGS);

            echo json_encode(["Status" => "Success"], JSON_UNESCAPED_UNICODE);
        }
    }

    if (!empty($_POST["fun"]))
    {
        $fun = $_POST["fun"];
        unset($_POST["fun"]);

        switch ($fun)
        {
            case 'UpdateFamilyMembershipTransferSetting':
                (new CompanyProductSettings())->updateByCompanyNum($companyNum, [
                    'familyMembershipTransfer' => $_POST['familyMembershipTransfer'] ?? 2
                ]);

                echo json_encode([
                    'Status' => 'Success'
                ]);
                break;
            case "InsertPeriodicPaymentNewData":
                $periodicPayment = $PeriodicPayment->GetPeriodicPaymentByCompanyNum($companyNum);
                $_POST["CompanyNum"] = $companyNum;
                if ($periodicPayment) {
                    $id = $periodicPayment->id;
                    $PeriodicPayment->UpdatePeriodicPayment($_POST);
                } else {
                    $id = $PeriodicPayment->InsertPeriodicPaymentNewData($_POST);
                }
                echo json_encode(array(
                    "id" => $id,
                    "Status" => "Success"
                ));

            break;
            case "GetPeriodicPaymentByCompanyNum":
                $CompanyPay = $PeriodicPayment->GetPeriodicPaymentByCompanyNum($companyNum);
                echo json_encode(array(
                    'CompanyPayment' => $CompanyPay,
                    "Status" => "Success"
                ));
            break;
            case "UpdatePeriodicPayment":
                $_POST["CompanyNum"] = $companyNum;
                $affect = $PeriodicPayment->UpdatePeriodicPayment($_POST);
                echo json_encode(array(
                    'affect' => $affect,
                    "Status" => "Success"
                ));
            break;
            case "InsertPaymentsNewData":
                $_POST["CompanyNum"] = $companyNum;
                $data = $_POST;
                $id = $Payments->InsertPaymentsNewData($_POST);
                echo json_encode(array(
                    "id" => $id,
                    "Status" => "Success"
                ));
            break;
            case "GetPaymentsByCompanyNum":
                $CompanyPay = $Payments->GetPaymentsByCompanyNum($companyNum);
                echo json_encode(array(
                    'CompanyPay' => $CompanyPay,
                    "Status" => "Success"
                ));
            break;
            case "GetBitSettings":
                $isBitAvailable = (int)Company::getInstance()->__get('TypeShva') === 1;

                echo json_encode([
                    'Status' => 'Success',
                    'bit' => (int)$isBitAvailable,
                ]);
            break;
            case "getCompanyMemberships":
                $affect = $itemsController->getCompanyMemberships($companyNum);
                echo json_encode($affect);
            break;
            case "UpdatePayments":
                unset($_POST["CompanyNum"]);
                $_POST["LimitPayments"] = json_encode($_POST["LimitPayments"]);
                $_POST["Interest"] = json_encode($_POST["Interest"]);
                $affect = $Payments->UpdatePayments($_POST, $companyNum);
                echo json_encode(array(
                    'affect' => $affect,
                    "Status" => "Success"
                ));
            break;
            case "RegistrationFeeCounter":
                $counter = $RegistrationFees->CompanyRegistretionCounter($companyNum);
                echo json_encode(array(
                    "counter" => $counter,
                    "Status" => "Success"
                ));
                break;
            case "getSingleRegistration":
                if (!isset($_POST['id']))
                {
                    echo json_encode(array(
                        "Message" => "ID required",
                        "Status" => "Error"
                    ));
                }
                else
                {
                    $res = $RegistrationFees->getSingleRegistration($_POST['id']);
                    if ($res->CompanyNum != $companyNum) {
                        echo "error";
                        break;
                    }
                    echo json_encode($res);
                }
                break;
            case "disablePayment":
                if (!isset($_POST['id']))
                {
                    echo json_encode(array(
                        "Message" => "ID required",
                        "Status" => "Error"
                    ));
                }
                else
                {
                    $res = $RegistrationFees->disablePayment($_POST['id'], $_POST['disabled'],$companyNum);
                    echo json_encode(array(
                        "Affect" => $res, "Status" => $res == "error" ? "Error" : "Success"
                    ));
                }
                break;
            case "deletePayment":
                if (!isset($_POST['id']))
                {
                    echo json_encode(array(
                        "Message" => "ID required",
                        "Status" => "Error"
                    ));
                }
                else
                {
                    $res = $RegistrationFees->deletePayment($_POST['id'],$companyNum);
                    echo json_encode(array(
                        "Status" => $res == "error" ? "Error" : "Success"
                    ));
                }
                break;
            case "InsertRegistrationFeesNewData":
                $_POST["CompanyNum"] = $companyNum;
                $id = $RegistrationFees->InsertRegistrationFeesNewData($_POST);
                echo json_encode(array(
                    "id" => $id,
                    "Status" => "Success"
                ));
            break;
            case "UpdateRegistrationFees":
                if (!isset($_POST['id']))
                {
                    echo json_encode(array(
                        "Message" => "id required",
                        "Status" => "Error"
                    ));
                }
                else
                {
                    $_POST["CompanyNum"] = $companyNum;
                    $id = $RegistrationFees->UpdateRegistrationFees($_POST,$_POST["id"]);
                    if ($id == "error") {
                        echo json_encode(["Status" => "Error"]);
                        break;
                    }
                    echo json_encode(array(
                        "id" => $id,
                        "Status" => "Success"
                    ));
                }
                break;
            case "GetRegistrationFeesByCompanyNum":
                $CompanyPay = $RegistrationFees->GetRegistrationFeesByCompanyNum($companyNum);
                echo json_encode(array(
                    'CompanyPay' => $CompanyPay,
                    "Status" => "Success"
                ));
            break;
            case "getCompanyBranches":
                $_POST["compayNum"] = $companyNum;
                $res = $itemsController->getCompanyBranches($_POST);
                echo json_encode($res);
            break;
            case "toggleSpreadPayment":
                $_POST["CompanyNum"] = $companyNum;
                $res = $itemsController->toggleSpreadPayment($_POST);
                echo json_encode(array(
                    "Status" => "Success"
                ));
                break;
            case "toggleBitPayments":
                $_POST["CompanyNum"] = $companyNum;
                $res = $itemsController->toggleBitPayments($_POST);
                echo json_encode(array(
                    "Status" => "Success"
                ));
                break;
            case "getAllItems":
                $noSingleClass = $_POST['noSingleClass'] ?? 0;
                $res = $itemsController->getItems($companyNum, $noSingleClass);
                echo json_encode($res);
                break;
            case "getAllItemsCoupons":
                $res = $itemsController->getItems($companyNum,true);
                echo json_encode($res);
                break;
            case "deleteCoupon":
                $couponController->deleteCoupon($_POST);
            break;
            case "disableCoupon":
                $couponController->disableCoupon($_POST);
            break;
            case "editSingleCoupon":
                $couponController->editSingleCoupon($_POST);
            break;
            case "getCoupons":
                $couponController->getCoupons($_POST);
            break;
            case "getSingleCoupon":
                $couponController->getSingleCoupon($_POST);
            break;
            case "insertNewCoupon":
                $res = $couponController->insertNewCoupon($_POST);
                if($res == "code_exists"){
                    echo json_encode(array(
                        'msg' => $res,
                        "Status" => "Error"
                    ));
                }
            break;
            case "reorderItems":
                $orderController->reorderItems($_POST);
            break;
            case "reorderCategories":
                $orderController->reorderCategories($_POST);
            break;
            case "reorderMembershipTypes":
                $orderController->reorderMembershipTypes($_POST);
            break;
            case "deleteOrMoveCategory":
                $itemsController->deleteOrMoveCategory($_POST);
            break;
            case "deleteOrMoveMembershipType":
                $itemsController->deleteOrMoveMembershipType($_POST);
            break;
            case "disableCategory":
                $itemsController->disableCategory($_POST);
            break;
            case "disableMembershipType":
                $itemsController->disableMembershipType($_POST);
            break;
            case "getCategoriesAndAmounts":
                $itemsController->getCategoriesAndAmounts($_POST);
            break;
            case "getItemsForSelectedCategory":
                $itemsController->getItemsForSelectedCategory($_POST);
            break;
            case "getItemsForSelectedMembership":
                $itemsController->getItemsForSelectedMembership($_POST);
            break;
            case "getMembershipTypeAndAmounts":
                $itemsController->getMembershipTypeAndAmounts($_POST);
            break;
            case "getSingleCompanySettings":
                $itemsController->getSingleCompanySettings($_POST);
            break;
            case "insertNewCategory":
                $itemsController->insertNewCategory($_POST);
            break;
            case "insertNewMembershipType":
                $itemsController->insertNewMembershipType($_POST);
            break;
            case "renameMembershipCategory":
                $itemsController->renameMembershipCategory($_POST);
            break;
            case "renameProductCategory":
                $itemsController->renameProductCategory($_POST);
            break;
            case "toggleManageMemberships":
                $itemsController->toggleManageMemberships($_POST);
            break;
            case "toggleOffsetSetting":
                echo $itemsController->toggleOffsetSetting($_POST);
            break;
            case "toggleEndNotification":
                setCompanySetting($companyNum, "notificationAtEnd", $_POST);
                break;
            case "changeEarlyNotification":
                setCompanySetting($companyNum, "NotificationDays", $_POST);
                $Company->UpdateNotificationDate($_POST['NotificationDays']);
                break;
        }
    }
    else
    {
        echo json_encode(array(
            "Message" => "No Function",
            "Status" => "Error"
        ));

    }
}

