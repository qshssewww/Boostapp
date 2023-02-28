<?php


class Translations
{
    public function getJsonTranslation($lang)
    {
        $translationArr = $this->getTranslationsByLangFromDB($lang);
        $translationArr['last_update'] = date('Y-m-d H:i:s');
        return $translationArr;
    }

    public function getLanguages(){
        return DB::table("boostappcms_dev.languages")->where("status","=",1)->orderBy("order","ASC")->get();
    }

    private function getTranslationsByLangFromDB($lang)
    {
        $translations = DB::table('boostappcms_dev.translation_keys')
            ->join('boostappcms_dev.translation_values',"boostappcms_dev.translation_keys.id", '=', "boostappcms_dev.translation_values.translation_key_id")
            ->where('boostappcms_dev.translation_values.lang',"=",$lang)
            ->select('boostappcms_dev.translation_values.*','boostappcms_dev.translation_keys.*')
            ->orderBy('boostappcms_dev.translation_keys.updated_at', 'asc')
            ->get();
        if(!sizeof($translations))
        {
            return [];
        }
        $translationsArr=[];
        foreach($translations as $translation){
            $translationsArr[$translation->key] = $translation->value;
        }

        $Translations = [
            'translation_keys' => $translationsArr,
        ];

        return $Translations;
    }
}
