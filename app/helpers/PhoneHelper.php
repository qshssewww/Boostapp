<?php

class PhoneHelper
{
    /**
     * @param $phone
     * @return string|null
     */
    public static function processPhone($phone)
    {
        // delete all non-digit symbols
        $phone = preg_replace('/\D/', '', $phone);
        // delete country code
        $phone = preg_replace('/^([+972]+)/', '', $phone);

        // if phone number doesn't start from 972 -> delete 0 in the start and add code 972
        if (substr($phone, 0, 3) !== '972') {
            $phone = ltrim($phone, '0');
            $phone = '972' . $phone;
        }

        // add + to the start
        $phone = '+' . $phone;

        if (!self::validatePhone($phone)) {
            return null;
        }

        return $phone;
    }

    /**
     * @param string $phone
     * @return string|null
     */
    public static function shortPhoneNumber(string $phone): ?string
    {
        if (strpos($phone, '+972') === 0) {
            $phone = substr($phone, 4);
        }
        if (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }
        if(preg_match("/^5/", $phone) && strlen($phone) >= 9){
            return $phone;
        }
        if (strlen($phone) < 9) {
            return null;
        }
        return $phone;
    }

    /**
     * @param $phone
     * @return bool
     */
    public static function validatePhone($phone): bool
    {
        if (strlen($phone) != 13) {
            return false;
        }

        if (substr($phone, 0, 5) !== '+9725') {
            return false;
        }

        return true;
    }

    /**
     * getFullPhoneNumber function
     * @param string $phoneNumber
     * @param string $areaCode
     * @return string|null
     */
    public static function getFullPhoneNumber(string $phoneNumber, string $areaCode = "+972"): ?string{
        $shortPhoneNum = self::shortPhoneNumber($phoneNumber);
        return $shortPhoneNum ? $areaCode . $phoneNumber : null ;
    }
}
