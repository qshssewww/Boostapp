<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/services/EmailService.php';
require_once __DIR__ . '/../../office/services/LoggerService.php';


class EmailController extends BaseController
{
    /**
     * @param $email
     */
    public function sendThanksForJoin($email, $password, $name)
    {
        if (isset($email)) {
            EmailService::sendGetRegistrationSuccess($email, $password, $name);
        }
    }

    /**
     * @param $companyName
     */
    public function sendJoinEmailToSupport($companyName) {
        try {
            $mail = 'info@boostapp.co.il';
            $Template = DB::table('247softnew.notificationcontent')
                ->where('CompanyNum', '=', 100)
                ->where('Type', '=', 1)
                ->first();

            if($Template) {
                $content = str_replace("[[שם חברה]]", $companyName, $Template->Content);
                $subject = $Template->Subject . ' - ' . $companyName. ' (via get)';
                EmailService::send($mail, $subject, $content);
            }
        } catch(\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_EXCEPTION);
        }
    }



}
