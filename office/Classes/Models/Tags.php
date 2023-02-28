<?php

use Hazzard\Database\Model;


/**
 * @property $id
 * @property $translation_id
 * @property $created_at
 * @property $updated_at
 * @property $category_id
 *
 * Class Tags
 */
class Tags extends Model
{
    public const PERSONAL_TRAINING = 70;

    protected $table = 'boostapp.tags';

    public static function getIdByTranslationId($translationId)
    {
        return self::where('translation_id', $translationId)->id;
    }

    public function getCategory()
    {
        return $this->category_id;
    }

    public function getTranslationId()
    {
        return $this->translation_id;
    }

    /**
     * @param $categoriesArray
     * @return array
     */
    public static function getSortedTagsWithKey($categoriesArray)
    {
        $objectsArray = DB::table('boostapp.tags as tags')
            ->select('tags.id', 'tags.category_id', 'keys.key')
            ->leftjoin('boostappcms_dev.translation_keys as keys', 'tags.translation_id', '=', 'keys.id')
            ->get();

        $resultArray = [];
        foreach ($objectsArray as $object) {
            $resultArray [$categoriesArray[$object->category_id]][] = [$object->id, $object->key];
        }
        return $resultArray;
    }

    /**
     * @return mixed
     */
    public static function getAllTagsWithTranslation($classNameArray) : Array
    {
        return DB::table('boostapp.tags as tags')
            ->select('tags.id', 'values.value')
            ->leftjoin('boostappcms_dev.translation_values as values', 'tags.translation_id', '=', 'values.translation_key_id')
            ->where(function ($q) use($classNameArray) {
                foreach ($classNameArray as $word){
                    $q->orWhere('values.value', 'like', '%'.$word.'%');
                }
            })->get();
    }

    /**
     * @param $arrayOfObjects
     * @return array
     */
    public static function sortTagsWithTranslation($arrayOfObjects)
    {
        $resultArray = [];
        foreach ($arrayOfObjects as $object){
            $resultArray[strtolower($object->value)] = $object->id;
        }
        return $resultArray;
    }

    /**
     * @param $appointmentName
     * @return mixed|null
     */
    public static function getTagIdByAppointmentNameAndTranslations($appointmentName)
    {
        $strReplaceArr = ['\'', '"', '`', '\\', '/', '*', '!', '?', '@', '#', '$', '%', '^', '&', '(', ')', '[', ']' , '{', '}', '|' ,'<', '>', '-', '+', '.', ',', ':', '_'];

        $appointmentName = strtolower($appointmentName);
        $appointmentName = str_replace($strReplaceArr, ' ', $appointmentName);
        $appointmentName = explode(' ', $appointmentName);

        $appointmentExplodedCount = array_map('mb_strlen', $appointmentName);

        if (max($appointmentExplodedCount) < 3){
            return null;
        }
        $tagId = self::getAllTagsWithTranslation($appointmentName);
        //$tagsTranslationsArray = self::sortTagsWithTranslation($tagId);
        //$tagId = $tagsTranslationsArray[$appointmentName] ?? null;

        $wordsArray = [];

        $existTags = [];

        $bestMatch = null;
        $lastLev = 99999999;
        $newArray = [];
        foreach ($tagId as $req){ // all tags from DB

            $translationValue = strtolower($req->value);
            //$translationValue = str_replace($strReplaceArr, '', $translationValue);

            $explodedReq = explode(' ', $translationValue);

            foreach ($appointmentName as $word){ // all words from lesson name
                foreach ($explodedReq as $item) { // tags from DB broke into words
                    if (strtolower($item) == $word) {
                        if (!str_contains($word, $existTags))
                            $existTags[] = $word;

                        $wordsArray[] = $translationValue.'::'.$req->id;
                        $expoTag = explode(' ', $translationValue);

                        foreach ($existTags as $eTag){ //
                            if (in_array($eTag, $expoTag) && !in_array($eTag, $newArray)){
                                $newArray[array_search($eTag, $expoTag)] = $eTag;
                            }
                        }
                    }
                }
            }
        }
        $newArray = array_unique($newArray);
        ksort($newArray);

        foreach ($wordsArray as $tag){
            $implodedNewArray = implode(' ', $newArray);
            $explodedForId = explode('::', $tag);

            $newLev = levenshtein($tag, $implodedNewArray);

            if ($newLev < $lastLev) {
                $lastLev = $newLev;
                $bestMatch = $explodedForId;
            }
        }

        return $bestMatch[1] ?? null;

        /* LITTLE NEWER
         $bestTag = null;

        foreach ($tagId as $tag){
            $bestTag = $tag->value == $appointmentName ? $tag->id : $bestTag;
        }

        if (!empty($bestTag))
            return $bestTag;

        if (!empty($tagId))
            return $tagId[0]->id;

        return null;*/


        /*OLD
         $nameWordsArray = explode(' ', $appointmentName);
        foreach ($nameWordsArray as $word) {
            $tagId = $tagsTranslationsArray[$word] ?? null;
            if ($tagId) {
                return $tagId;
            }
        }*/
    }

}
