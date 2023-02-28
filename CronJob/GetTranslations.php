<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
//require_once '../app/init.php';

require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Translations.php';
//require_once '../office/Classes/Translations.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');
$TranslationsClass= new Translations();
$languages = $TranslationsClass->getLanguages();
foreach($languages as $lang){
    //$file = dirname(dirname(__FILE__)). '/storage/lang/translations-' . $lang->lang_code  . '.json';
    $file = $_SERVER['DOCUMENT_ROOT'] . '/storage/lang/translations-' . $lang->lang_code  . '.json';
    $dataFromApi = $TranslationsClass->getJsonTranslation($lang->lang_code);
    if(isset($dataFromApi['translation_keys'])) {
        $apiLastUpdate = strtotime($dataFromApi['last_update']);
        $totalTranslationsFromApi = count($dataFromApi['translation_keys']);
        if (file_exists($file)) {
            $localFileJson = file_get_contents($file);
            $localFileJson = json_decode($localFileJson, true);
            $localFileLastUpdate = strtotime($localFileJson['last_update']);
            $totalTranslationsFromLocal = count($localFileJson['translation_keys']);
            if ($localFileLastUpdate < $apiLastUpdate) {
                file_put_contents($file, json_encode($dataFromApi));
            }
        }
        else{
            file_put_contents($file, json_encode($dataFromApi));
        }
    }
}
$Cron->end();
?>
