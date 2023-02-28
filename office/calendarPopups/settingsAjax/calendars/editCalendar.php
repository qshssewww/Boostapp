<?php
require_once '../../../../app/init.php';
require_once "../../../Classes/Brand.php";
require_once "../../../Classes/Section.php";
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object)$rawData;
    if ($data && isset($data->id) && isset($data->Brands) && isset($data->Title)) {
        $data->Title = trim($data->Title);

        $section = new Section();
        $res = $section->editSection($data->id, array('Brands' => $data->Brands, 'Title' => $data->Title));
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    } else {
        echo "error";
    }
}
