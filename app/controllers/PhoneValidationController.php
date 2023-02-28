<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/responses/PhoneValidationResponse.php';
require_once __DIR__ . '/../helpers/PhoneHelper.php';
require_once __DIR__ . '/../../office/services/OTPService.php';
require_once __DIR__ . '/../../office/services/AuthService.php';

/**
 *
 */
class PhoneValidationController extends BaseController
{
    /**
     * @param $phone
     * @return bool
     */
    public function sendOtp($phone): bool
    {
        $phone = PhoneHelper::processPhone($phone);
        $this->asJson();

        $response = new PhoneValidationResponse();

        if (!AuthService::canAuth($phone)) {
            $response->success = false;
            $response->blocked = true;
            $response->message = 'Blocked';
            return $response->send();
        }

        if (OTPService::sendOtp($phone)) {
            $response->message = 'Verification code was sent';
        } else {
            throw new LogicException('Error while sending sms');
        }

        return $response->send();
    }

    /**
     * @param $otp
     * @return void
     * @throws Exception
     */
    public function validateOtp($otp)
    {
        $validationSucceed = OTPService::compareOtp($otp);

        $this->asJson();

        if ($validationSucceed) {
            //OTP matches
            /** @var User $user */
            $user = Auth::user();
            if ($user) {
                $user->setMultiUser($_SESSION['phone']);
            }

            echo json_encode([
                'status' => 1,
                'message' => 'success',
                'data' => ['phone' => PhoneHelper::processPhone($_SESSION['phone'])]
            ]);
            return;
        }

        echo json_encode([
            'status' => 0,
            'message' => 'otp mismatch',
        ]);
    }
}
