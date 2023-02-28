<?php

namespace Hazzard\Translation;

class TranslationManager
{

    private $translationsArr = [];

    protected static $instance = null;

    public function __construct()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/storage/lang/translations-' . $_SESSION['lang']  . '.json';
        if(file_exists($file)) {
            $this->translationsArr = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/storage/lang/translations-' . $_SESSION['lang'] . '.json'), true);
        }
        else{
            $this->translationsArr = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/storage/lang/translations-he.json'), true);
        }
    }

    /**
     * @return TranslationManager | null
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param $key
     * @param array $params
     * @return array|string|string[]
     */
    public function get($key, array $params = [])
    {
        if (empty($this->translationsArr['translation_keys'][$key])) {
            return '';
        }

        if (!empty($this->translationsArr['translation_keys'][$key])) {
            $translation = stripslashes($this->translationsArr['translation_keys'][$key]);
            if (!empty($params)) {
                foreach ($params as $k => $value) {
                    $translation = str_replace('{' . $k . '}', $value, $translation);
                }
            }

            return $translation;
        } else {
            return ('');
        }
    }

}
