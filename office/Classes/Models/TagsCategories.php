<?php

use Hazzard\Database\Model;

class TagsCategories extends Model
{
    protected $table = 'boostapp.tags_categories';

    /**
     * @return array
     */
    public static function getCategoriesWithKey()
    {
        $objectsArray = DB::table('boostapp.tags_categories as categories')
            ->select('categories.id', 'keys.key')
            ->leftjoin('boostappcms_dev.translation_keys as keys', 'categories.translation_id', '=', 'keys.id')
            ->get();
        $resultArray = [];
        foreach ($objectsArray as $object) {
            $resultArray[$object->id] = $object->key;
        }
        return $resultArray;
    }
}