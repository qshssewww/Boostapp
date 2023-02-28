<?php

require_once '../../../app/init.php';
require_once __DIR__."/../../Classes/Company.php";
require_once __DIR__."/../../Classes/InsurancePopupEntries.php";
require_once __DIR__."/../../services/EmailService.php";

const RECIPIENT_EMAILS = [
    'amir@biran-insurance.com',
    'alex@boostapp.co.il',
    'migdalleads@migdal.co.il',
    'migdal-sales@migdal.co.il',
];

if (Auth::check()) {
    $CompanyNum = Auth::user()->CompanyNum;
    $timestamp = (new DateTime)->getTimestamp();
    $ByUserId = Auth::user()->id;
    $step = $_POST['form_step'];
    $email_sent = 0;
    $form_completed = 0;
    $step = $_POST['form_step'];


    if (isset($_POST['load_view'])) {
        $Supplier = Company::getInstance();
        $business_name = htmlentities($Supplier->CompanyName);
        $business_number = $Supplier->CompanyId;
        $email_id = Auth::user()->email;
        $phone_number = Auth::user()->ContactMobile;

        $insuranceObj = new InsurancePopupEntries([
            'by_user_id' => $ByUserId,
            'name' => $business_name,
            'calculation' => '',
            'phone' => '',
            'company_num' => $CompanyNum,
            'email' => '',
            'business_number' => $business_number,
            'email_sent' => $email_sent,
            'form_completed' => 0,
            'form_step' => $step
        ]);
        $insuranceObj->save();
        $insurance_insert_id = $insuranceObj->id;

        require '../../partials-views/insurance/modal-insurance-form.php';
        exit;
    }

    $insurance_insert_id = $_POST['insurance']['id'];
    $insurance_formula = InsurancePopupEntries::FORMULA_ARR;

    $type = isset($_POST['coverage']['crossfit_training']) ? (($_POST['coverage']['crossfit_training'] == "yes") ? "crossfit" : "regular") : "";
    $limit = $_POST['coverage']['warranty_limit'] ?? "";
    $coaches = $_POST['coverage']['no_of_coaches'];
    $amount = null;
    if (isset($insurance_formula[$type][$limit])) {
        $amount = $insurance_formula[$type][$limit][$coaches];
    }
    $insurance = $_POST['insurance'];
    $calculation = $_POST['coverage'];
    $calculation['amount'] = $amount;
    $calc = json_encode($calculation);
    $phone = "";
    $email = "";
    if (!isset($_POST['form_submit'])) {
        echo json_encode(['status' => 1, "data" => $amount ?? 0]);
    }
    if (isset($_POST['form_submit'])) {
        $email = $insurance['email'];
        $phone = $insurance['phone'];
        $if_entry = InsurancePopupEntries::isPhoneExist($phone);
        if ($if_entry) {
            $phone = "";
            echo json_encode(["status" => 0, "msg" => lang('mobile_validation_notice_insurance'), "mobile" => 1]);
            exit();
        } else {
            $form_completed = 1;
            $is_crossfit = $calculation["crossfit_training"] == "yes" ? lang("yes") : lang("no");
            $no_of_coaches = $calculation["no_of_coaches"] ?? 1;
            $warranty_limit = $calculation["warranty_limit"] ?? 500000;

            $email_text = '<div style="font-weight: bold">
                <p>'.lang('client_details_class').': </p>
                <p>'.lang('studio_name').': '.$insurance['business_name'] .' </p>
                <p>'.lang('reports_card_name').': '. Auth::user()->display_name .'</p>
                <p>'.lang('phone').': '. $phone .'</p>
                <p>'.lang('email').': '. $email .'</p>
                <p>גבולות אחריות: '. $warranty_limit .'</p>
                <p>תוספת בגין אימוני קרוספיט: '. $is_crossfit .'</p>
                <p>מספר מאמנים: '. $no_of_coaches .'</p>
                <p>'.lang('date').': '. date('d/m/Y') .'</p>
                <p>'.lang('summary').': '.$amount.'₪</p>
            </div>';
            $subject = "פרטי התקשרות למתעניין בביטוח למאמנים - Boostapp";

            try {
                foreach (RECIPIENT_EMAILS as $recipientEmail){
                    $res = EmailService::send($recipientEmail, $subject, $email_text);
                }
            } catch (\Throwable $e) {
                $email_sent = 0;
                echo json_encode(["status" => 0, "msg" => $e->getMessage()]);
                exit;
            }
            if(isset($res['status']) && $res['status'] == 1) {
                $email_sent = $res['status'];
                echo json_encode(["status" => 1, "msg" => "COMPLETED"]);
            } else {
                $email_sent = $res['status'];
                echo json_encode(["status" => 0, "msg" => $res["message"] ?? ""]);
            }
        }

        $step = 3;
        $phone = $insurance['phone'];
    }

    $updateArr = [
        'name' => $insurance['business_name'],
        'calculation' => $calc,
        'phone' => $phone,
        'email' => $email,
        'business_number' => $insurance['business_number'],
        'email_sent' => $email_sent,
        'form_completed' => $form_completed,
        'form_step' => $step,
    ];
    InsurancePopupEntries::update($insurance_insert_id, $updateArr);

}