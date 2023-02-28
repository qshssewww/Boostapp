<?php

/**
 * @property $sendType 1-SMS 2-Email (default: 2)
 * @property $sendTime Link sending time units (default: 1)
 * @property $sendTimeType Link sending time type: 1-Minutes 2-Hours (default: 2)
 */
class ClassOnline extends \Hazzard\Database\Model
{
    const sendTimeType = [1 => 'minutes', 2 => 'hours'];

    protected $table = 'class_online';

    /**
     * @param $value
     * @return string
     */
    public static function getSendTimeType($value): string
    {
        return self::sendTimeType[$value];
    }
}
