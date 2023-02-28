<?php


class TranslationDbValues
{
    private static $table = 'boostapp.translation_db_values';

    protected static $instance = null;
    private $translationsArr = [];

    public function __construct() {
        $translations = DB::table(self::$table)->get();
        foreach ($translations as $trans) {
            $this->translationsArr[$trans->key] = $trans->value;
        }
    }

    /**
     * @return TranslationDbValues | null
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return static::$instance;
    }

    public function getKeyFromValue($val) {
        return array_search($val, $this->translationsArr) ?? '';
    }

}