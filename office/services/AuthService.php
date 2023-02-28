<?php

require_once __DIR__ . '/../Classes/AuthLog.php';

class AuthService
{
    public const MAX_ATTEMPTS_NUMBER = 5;
    public const FREEZING_DELAY_MINUTES = 15;

    /**
     * @param $phone
     * @return bool
     */
    public static function canAuth($phone): bool
    {
        $lastAuth = AuthLog::getByPhone($phone);

        if (!$lastAuth) {
            $lastAuth = new AuthLog();
            $lastAuth->phone_number = $phone;
            $lastAuth->save();
        }

        $lastAuth->updateIp();

        // check last authorization date
        $lastAttemptTimestamp = strtotime($lastAuth->last_try_date);
        $currentTimestamp = time();

        $moreThan15MinutesFromLastAttempt = ($currentTimestamp - $lastAttemptTimestamp) >= self::FREEZING_DELAY_MINUTES * 60;
        if ($moreThan15MinutesFromLastAttempt) {
            $lastAuth->setAttemptsNumber(0);
        }

        if ($lastAuth->attempts != self::MAX_ATTEMPTS_NUMBER) {
            $lastAuth->setLastAttemptDate();

            // increase number of attempts
            $lastAuth->setAttemptsNumber($lastAuth->attempts + 1);
        }

        // check number of attempts and time from the last try
        if ($lastAuth->attempts > self::MAX_ATTEMPTS_NUMBER && !$moreThan15MinutesFromLastAttempt) {
            return false;
        }

        return true;
    }

    /**
     * @param $phone
     * @return mixed
     */
    public static function getAttemptsNumber($phone)
    {
        $lastAuth = AuthLog::getByPhone($phone);

        if (!$lastAuth) {
            $lastAuth = new AuthLog();
            $lastAuth->phone_number = $phone;
            $lastAuth->save();
        }

        return $lastAuth->attempts;
    }

    /**
     * @param $phone
     * @return void
     */
    public static function resetAttemptsNumber($phone)
    {
        $lastAuth = AuthLog::getByPhone($phone);

        if ($lastAuth) {
            $lastAuth->attempts = 0;
            $lastAuth->save();
        }
    }

    /**
     * @param $phone
     * @return int
     */
    public static function getAttemptsNumberLeft($phone): int
    {
        $lastAuth = AuthLog::getByPhone($phone);

        if (!$lastAuth) {
            return self::MAX_ATTEMPTS_NUMBER;
        }

        $attemptsLeft = self::MAX_ATTEMPTS_NUMBER - $lastAuth->attempts;

        if ($attemptsLeft < 0) {
            return 0;
        }

        return $attemptsLeft;
    }
}
