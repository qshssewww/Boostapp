<?php

class City {
    
    private $table;

    public function __construct() {
        $this->table = 'boostapp.cities';
    }

    public function getAllCities() {
        $data = DB::table($this->table)->get();
        return $data;
    }

    public function getFirstCityByName($name) {
        $data = DB::table($this->table)->where('City', $name)->first();
        return $data;
    }

    /**
     * @param $cityId
     * @return string
     */
    public static function getNameByCityId($cityId): string
    {
        return DB::table('boostapp.cities')
            ->where('CityId',$cityId)
            ->pluck('City') ?? '';
    }

    public function getCityIdByName($name) {
        $city = strtoupper(trim($name));
        $obj = DB::table($this->table)
            ->select('CityId')
            ->where('City', 'like', $city)
            ->orWhere('CityEn', 'like', $city)
            ->orWhere('City', 'like', $city.'%')
            ->first();

        return $obj->CityId ?? null;
    }
}