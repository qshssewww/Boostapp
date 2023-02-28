<?php

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $MemberShip
 * @property $Status
 *
 * Class Membership
 */
class Membership extends Model
{

    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;

    protected $table = 'boostapp.membership';

}
