<?php

namespace App\Models;

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $key
 * @property $url
 * @property $date
 *
 * Class DebugKeys
 */
class DebugKeys extends Model
{
    protected $table = 'boostapp.debug_keys';
}
