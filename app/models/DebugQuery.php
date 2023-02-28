<?php

namespace App\Models;

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $key
 * @property $type
 * @property $query
 * @property $trace
 * @property $time
 *
 * Class DebugQuery
 */
class DebugQuery extends Model
{
    protected $table = 'boostapp.debug_queries';
}
