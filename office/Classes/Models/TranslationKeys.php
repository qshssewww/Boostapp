<?php

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $key
 * @property $created_at
 * @property $updated_at
 * @property $notes
 * @property $images
 *
 * Class TranslationKeys
 */
class TranslationKeys extends Model
{
    protected $table = 'boostappcms_dev.translation_keys';


    /**
     * getKeyIdByKeyName function
     *
     * @param string $keyName
     * @return int|null
     */
    public static function getKeyIdByKeyName(string $keyName):?int
    {
        $keyId = self::where('key', '=', $keyName)->pluck('id');
        return !empty($keyId) ? (int)$keyId : null;
    }

}