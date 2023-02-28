<?php

/**
 * @property $id
 * @property $phone_number
 * @property $attempts
 * @property $last_try_date
 * @property $last_ip
 */
class AuthLog extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.auth_log';

    public function __construct($attributes = [])
    {
        $this->attempts = 0;

        parent::__construct($attributes);
    }

    /**
     * @param $phone
     * @return AuthLog|null
     */
    public static function getByPhone($phone)
    {
        $phone = PhoneHelper::processPhone($phone);
        return self::where('phone_number', $phone)->first();
    }

    /**
     * @return void
     */
    public function setAttemptsNumber($number)
    {
        $this->attempts = $number;
        $this->save();
    }

    /**
     * @return void
     */
    public function setLastAttemptDate()
    {
        $this->last_try_date = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * @return void
     */
    public function updateIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->last_ip = $ip;
        $this->save();
    }
}
