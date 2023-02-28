<?php

class PasswordHelper
{
    public const PASSWORD_USER_REGEX = "(?=.*?[0-9a-zA-Z])(?=.*?[#?!@$%^&*-]).{8,}";

    /**
     * Generates random password by length
     * @param $length
     * @return string
     */
    public static function generate($length): string
    {
        return self::random($length);
    }
    /**
     * Hashes password
     * @param $password
     * @return string
     */
    public static function hash($password): string
    {
        return Hash::make($password);
    }

    /**
     * Makes random string of defined length
     * @param $length
     * @return string
     */
    private static function random($length): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException('Wrong password length');
        }

        $chars = "abcdefghijklmnopqrstuvwxyz123456789";
        return substr(str_shuffle($chars), 0, $length);
    }


    public static function test(string $password): bool{
        return (bool)preg_match('/'.self::PASSWORD_USER_REGEX.'/i', $password);
    }

}
