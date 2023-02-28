<?php
require_once '../../Classes/Pipeline.php';
require_once '../../Classes/Client.php';
require_once "../../Classes/Company.php";
require_once '../../../app/init.php';
require_once "../../services/ClientService.php";

header("Content-Type: application/json", true);
if (Auth::guest()) exit;
if (Auth::check() && Auth::userCan('31')) {
    $CompanyNum = Auth::user()->CompanyNum;
    $mainPipeId = DB::table('boostapp.pipeline_category')->where("CompanyNum", "=", $CompanyNum)->where("Act", "=", 1)->first();
    $postdata = file_get_contents("php://input");
    $obj = json_decode($postdata);

    $client = ClientService::addClient([
        "CompanyNum" => $CompanyNum,
        "Email" => $obj->pemail,
        "ContactMobile" => $obj->pphone,
        "FirstName" => $obj->fname,
        "LastName" => $obj->lname,

        // Pipeline part
        'MainPipeId' => $mainPipeId->id,
        'PipeId' => $obj->leadStatus,
        'Status' => 0,
        'ClassInfo' => implode(",", $obj->interestsIds),
        'ClassInfoNames' => implode(",", $obj->interestsName),
        'additional_data' => json_encode($obj->additional_data, JSON_UNESCAPED_UNICODE)
    ], ClientService::CLIENT_STATUS_LEAD);

    echo json_encode([$client['Message']['pipeline_id']], JSON_UNESCAPED_UNICODE);
}
