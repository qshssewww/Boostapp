<?php
require_once __DIR__.'/TranslationKeys.php';

use Hazzard\Database\Model;

class TranslationValues extends Model
{
    protected $table = 'boostappcms_dev.translation_values';

    /**
     * @param array $idArray
     * @return array
     */
    public static function getTranslationsByKeys(array $idArray): array
    {
        $arrayOfObjects = self::select('translation_key_id', 'value', 'lang')->whereIn('translation_key_id', array_keys($idArray))->get();
        $result = [];
        if (isset($arrayOfObjects)) {
            foreach ($arrayOfObjects as $object) {
                $translationTagId = [];
                if ($object->value) {
                    $translationTagId['text'] = $object->value;
                    $translationTagId['id'] = $idArray[$object->translation_key_id];
                    $translationTagId['lang'] = $object->lang;
                    $result[] = $translationTagId;
                }
            }
        }
        return $result;
    }


    /**
     * @param string $langKey
     * @param string $lang
     * @return string|null
     */
    public static function getTranslationValueByLangKeyPair(string $langKey, string $lang = "he"): ?string {
        $keyId = TranslationKeys::getKeyIdByKeyName($langKey);
        return $keyId !== null ? self::where('translation_key_id', '=', $keyId)->where('lang', '=', $lang)->pluck('value')
            : null;
    }
}
