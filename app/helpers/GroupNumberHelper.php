<?php

/**
 * Generator of GroupNumber value
 */
class GroupNumberHelper
{
    /**
     * @return string
     */
    public static function generate(): string
    {
        return uniqid(uniqid() . strtotime(date('YmdHis')) . 1262055681 . rand(1, 9999999));
    }
}
