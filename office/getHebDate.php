<?php
$pieces = explode("-", $_REQUEST['Date']);
$day = $pieces['2'];
$month = $pieces['1'];
$year = $pieces['0'];
$JewishDate = iconv ('WINDOWS-1255', 'UTF-8',jdtojewish(gregoriantojd( date($month), date($day), date($year)), true, CAL_JEWISH_ADD_GERESHAYIM));
echo str_replace('"', "",$JewishDate);
?>