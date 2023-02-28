<?php

/**
 * @property $id
 * @property $client_id
 * @property $file_name
 * @property $type
 * @property $disply_name
 * @property $description
 * @property $status
 * @property $created_at
 * @property $updated_at
 * 
 * Class Upload
 */

class Upload extends \Hazzard\Database\Model {

    protected $table = "boostapp.uploads";
    protected $id, $client_id, $file_name, $type, $category, $disply_name, $description, $status, $created_at, $updated_at;


    /**
     * __construct function
     * @param $attributes
     */
    public function __construct($attributes = []){
        if (is_numeric($attributes)) {
            $model = self::find($attributes);
            if ($model) {
                $this->fill($model->toArray());
                $this->exists = true;
            }
            $attributes = [];
        }

        parent::__construct($attributes);
    }


    /**
     * getByClientId function
     * @param int $clientId
     * @return Uploads[]|null
     */
    public static function getByClientId(int $clientId, int $status = 1): ?array{
        return self::where('client_id', $clientId)->where('status', $status)->get();
    }

}