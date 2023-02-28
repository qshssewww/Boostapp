<?php

class Street {

    private $table;

    public function __construct() {
        $this->table = 'boostapp.street';
    }

    public function getAllStreets() {
        $data = DB::table($this->table)->get();
        return $data;
    }

    public function getFirstStreetByName($name, $cityId) {
        $data = DB::table($this->table)
            ->where('Street', $name)
            ->where('CityId', $cityId)
            ->first();
        return $data;
    }

    /**
     * @param $id
     * @return string
     */
    public static function getNameById($id): string
    {
        return DB::table('boostapp.street')
                ->where('id',$id)
                ->pluck('Street') ?? '';
    }
}
