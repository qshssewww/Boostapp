<?php

$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('classstudio_act')
    ->select(
        'client.id as clientId',
        'client.CompanyName as fullName',
        DB::raw('IF(client.Brands=0, "סניף ראשי", brands.BrandName) as brunchName'),
        DB::raw("IF(client.Gender = 0, 'לא ידוע', IF(client.Gender = 1, 'זכר', 'נקבה')) as gender"),
        'client.ContactMobile as phone',
        'client.Email as email',
        DB::raw("CONCAT(classstudio_act.ItemText , ' ', DATE_FORMAT(client_activities.TrueDate, '%d/%m/%Y'), ' ', client_activities.TrueBalanceValue) AS membership"),
        'classstudio_act.ClassName',
        DB::raw("DATE_FORMAT(classstudio_act.ClassDate, '%d/%m/%Y') as classDate"),
        'classstudio_act.ClassStartTime as classTime',
        'users.display_name as guideName',
        'class_status.Title as status'

    )
    ->where('classstudio_act.CompanyNum','=', $rest->CompanyNum)
    ->whereBetween('classstudio_act.ClassDate', array($dateFrom, $dateTo))

    ->leftJoin('client', DB::raw("IF(classstudio_act.TrueClientId=0, classstudio_act.ClientId, classstudio_act.TrueClientId)"), '=', 'client.id')
    ->leftJoin('users', 'users.id', '=', 'classstudio_act.GuideId')
    ->leftJoin('client_activities', 'client_activities.id', '=', 'classstudio_act.ClientActivitiesId')
    ->leftJoin('class_status', function ($join) use($rest){
        $join
            // ->on('class_status.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('class_status.id', '=', 'classstudio_act.Status');
    })
    ->leftJoin('brands', function ($join) use ($rest) {
        $join
            ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('brands.id', '=', 'client.Brands');
    });


if((empty ($_REQUEST['status']) )){
    $q->where('classstudio_act.StatusCount','=','0');
}

$fields = [
    'fullName'=>'client.CompanyName',
    'phone'=>'client.ContactMobile',
    'email'=>'client.Email',
    'membership'=>'classstudio_act.MemberShipText',
    'className'=>'classstudio_act.ClassName',
    'classDate'=>'classstudio_act.ClassDate',
    'classTime'=>'classstudio_act.ClassStartTime',
    'status'=>'class_status.Title',
    'guideName'=>'users.display_name',
    'brunchName'=>'brands.BrandName'
];

if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];
    $sortKeyPos = array_search($sortName, array_keys($fields));
    if($sortKeyPos !== FALSE){
        $q->orderBy(array_values($fields)[$sortKeyPos], $dir);
    }else{
        $q->orderBy('client.CompanyName', 'asc');
    }

} else {
    $q->orderBy('client.CompanyName', 'asc');
}

// total amount without filter
$rest->answer->recordsFiltered = COUNT(DB::select($q->toString()));

$rest->answer->statuss = DB::select(sprintf("select status as value from (%s) as tbl GROUP BY status", $q->toString()));
$rest->answer->classNames = DB::select(sprintf("select className as value from (%s) as tbl GROUP BY className", $q->toString()));


// search filter
$filter = false;
$sql = sprintf("select * from (%s) as tbl", $q->toString());

foreach ($fields as $field=>$tblField) {

    if($field === 'classDate'){
        if (!empty($_GET['filter'][$field]) && $_GET['filter'][$field] != "") {
            $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.".$field." = DATE_FORMAT('".$_GET['filter'][$field]."', '%d/%m/%Y')";
            $filter = true;
            continue;
        }
    }

    if (!empty($_GET['filter'][$field]) && $_GET['filter'][$field] != "") {
        $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.".$field." LIKE '" . $_GET['filter'][$field] . "%'";
        $filter = true;
    }

}


$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
$rest->answer->recordsFiltered = isset($filter) && $filter ? COUNT(DB::select($sql)) : $rest->answer->recordsFiltered;
$sql .= " LIMIT $limit OFFSET $start";

$results = DB::select($sql);

$rest->answer->recordsTotal = COUNT($results);
$rest->answer->items = $results;
$rest->answer->sql = $sql;
// $rest->answer->filter = $_GET['filter'];

