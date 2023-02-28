<?php

require_once 'SmsService.php';

class OTPService
{
    protected const MAX_ATTEMPTS_NUMBER = 5;

    /**
     * @return string
     */
    public static function generateOtp(): string
    {
        return (string)rand(100000, 999999);
    }

    /**
     * Sending random password in sms and saving it for verification
     * @param string $phone Phone number including country code
     *
     * @return bool
     */
    public static function sendOtp(string $phone): bool
    {
        $otp = self::generateOtp();

        $_SESSION['phone'] = $phone;
        $_SESSION['serverOtp'] = $otp;

        $message = "קוד האימות שלך ל - Boostapp הוא: " . $otp;

        if (SmsService::send($phone, $message)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $otp user provided One Time Password
     * @return bool
     */
    public static function compareOtp(string $otp): bool
    {
        return $otp === (string)$_SESSION['serverOtp'];
    }
}
