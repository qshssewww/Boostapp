<?php require_once '../app/initcron.php';
require_once __DIR__."/Classes/calendar.php";

if (Auth::userCan('138')):

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;
$RoleId = Auth::user()->role_id;

$RoleInfo = DB::table('roles')->where('id','=',$RoleId)->first();
$SeeAll = ($RoleInfo->permissions=='*' ? '1': '0');


if (!isset($_REQUEST["dateFrom"])) $_REQUEST["dateFrom"] = date("Y-m-d");
if (!isset($_REQUEST["dateTo"])) $_REQUEST["dateTo"] = date("Y-m-d");

$dateFrom = $_REQUEST["dateFrom"];
$dateTo = $_REQUEST["dateTo"];
    $Calendar = new Calendar();
    $result = $Calendar->taskInfo($RoleId, $UserId, $CompanyNum,$dateFrom,$dateTo, $SeeAll);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

endif;











