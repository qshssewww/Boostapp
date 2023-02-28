<?php

namespace App\Database;

class BaseConnection extends \Hazzard\Database\Connection
{
    /**
     * Begin a fluent query against a database table.
     *
     * @param  string  $table
     * @return BaseQuery
     */
    public function table($table)
    {
        $query = new BaseQuery($this);

        return $query->from($table);
    }
}