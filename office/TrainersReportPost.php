<?php

ini_set("max_execution_time", 0);
require_once '../app/initcron.php';
require_once "Classes/ClientActivities.php";
require_once "Classes/ClassStudioAct.php";


header('Content-Type: text/html; charset=utf-8');
if (!Auth::check()) {
    exit;
}

$start = isset($_POST['start']) ? date('Y-m-d', strtotime($_POST['start'])) : date('Y-m-01');
$end = isset($_POST['end']) ? date('Y-m-d', strtotime($_POST['end'])) : date('Y-m-t');
$class_id = '';
$fields = [];
if (isset($_REQUEST['id'])):
    $class_id = base64_decode($_REQUEST['id']);
endif;
if (isset($_REQUEST['fields'])):
    $fields = explode(",", base64_decode($_REQUEST['fields']));
endif;
$CompanyNum = Auth::user()->CompanyNum;


setcookie("freezrepostfields", implode(",", $fields), time() + 60 * 60 * 24 * 30, "/");

$class_studio_act_rows = DB::table('classstudio_act as csr')
    ->select('csr.ClientId', 'csr.ClassDate', 'csr.RegularClassId', 'csr.Status', 'csr.Department', 'clr.CompanyName', 'clr.Dob', 'clr.ContactMobile', 'clr.BalanceAmount')
    ->leftJoin("client as clr", "csr.ClientId", "=", "clr.id")
    ->where('csr.ClassId', '=', $class_id)
    ->whereIn('csr.Status', [1, 2, 6, 7, 8, 10, 11, 12, 15, 16, 21, 22])
    ->get();


$temp_array = [];

function prepareImportantNotes($client_id) {
    $results = DB::table('clientcrm as ccrm')->select('ccrm.Remarks')
        ->where('ccrm.ClientId', '=', $client_id)
        ->where('ccrm.Status', '=', 0)
        ->where('ccrm.StarIcon', '=', 1)
        ->where(function($q) {
            $q->whereNull('ccrm.TillDate')
                ->Orwhere('ccrm.TillDate', '>=', date('Y-m-d'));
        })
        ->get();

    $return = '<ul>';
    foreach ($results as $row):
        $return .= "<li>{$row->Remarks}</li>";
    endforeach;
    $return .= '</ul>';
    return $return;
}

function prepareMedicalInfo($client_id) {
    $results = DB::table('clientmedical as cml')->select('cml.Content')
        ->where('cml.ClientId', '=', $client_id)
        ->where('cml.Status', '=', 0)
        ->where(function($q) {
            $q->whereNull('cml.TillDate')
                ->Orwhere('cml.TillDate', '>=', date('Y-m-d'));
        })
        ->get();


    $return = '<ul>';
    foreach ($results as $row):
        $return .= "<li>{$row->Content}</li>";
    endforeach;
    $return .= '</ul>';
    return $return;
}

$result_array = [];
foreach ($class_studio_act_rows as $key => $row):
    $temp_array = [];
    $temp_array[] = $row->CompanyName;

    if (in_array("customerPhone", $fields)):
        $temp_array[] = $row->ContactMobile;
    endif;
    if (in_array("debtAmount", $fields)):
        $temp_array[] = $row->BalanceAmount;
    endif;
    if (in_array("medicalInfo", $fields)):
        $temp_array[] = prepareMedicalInfo($row->ClientId);
    endif;
    if (in_array("importantNotes", $fields)):
        $temp_array[] = prepareImportantNotes($row->ClientId);
    endif;
    if (in_array("permanentRegister", $fields)):
        $temp_array[] = ($row->RegularClassId != 0) ? lang('yes') : lang('no');
    endif;
    if (in_array("birthday", $fields)):
        $temp_array[] = !empty($row->Dob) && $row->Dob != "0000-00-00" ? date('d/m/Y', strtotime($row->Dob)) : lang('no_birthday_date');
    endif;
    if (in_array("freeClass", $fields)):
        $temp_array[] = ($row->Status == 16) ? lang('without_charge') : lang('with_charge');
    endif;
    if (in_array("firstClass", $fields)):
        $CSA = new ClassStudioAct();
        $isFirstLess = $CSA->isFirstLesson($CompanyNum, $row->ClassDate, $row->ClientId);
        $temp_array[] = ($row->Department == 3) ? lang('under_try_membership') : (( $isFirstLess == true ) ? lang('first_class') : lang('no'));
    endif;

    $result_array["data"][] = $temp_array;
endforeach;


echo json_encode($result_array, JSON_UNESCAPED_UNICODE);