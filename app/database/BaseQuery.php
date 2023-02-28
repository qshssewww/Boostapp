<?php

namespace App\Database;

class BaseQuery extends \Hazzard\Database\Query
{
    public $indexBy;

    /**
     * Return the column value for the given column name.
     *
     * @param $column
     * @return array
     */
    public function column($column)
    {
        $records = $this->get((array) $column);

        return array_column($records, $column);
    }

    /**
     * @param $column
     * @return $this
     */
    public function indexBy($column)
    {
        $this->indexBy = $column;

        return $this;
    }

    /**
     * Convert results to model instances.
     *
     * @param  array  $results
     * @return array
     */
    public function getModels(array $results)
    {
        $models = [];

        foreach ($results as $result) {
            $model = $this->model->newModel($result);

            if ($this->indexBy) {
                $models[$result->{$this->indexBy}] = $model;
            } else {
                $models[] = $model;
            }
        }

        return $models;
    }
}
