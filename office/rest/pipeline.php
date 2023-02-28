<?php
if(!empty($_GET['data']) && $_GET['data'] === 'statusList'){
    $rest->answer->items = DB::table('leadstatus')->select('Title as value')->where('leadstatus.CompanyNum', $rest->CompanyNum)->orderby('leadstatus.Title', 'desc')->get();
    exit;
}


$q = DB::table('pipeline')
    ->where('pipeline.CompanyNum', $rest->CompanyNum)
    ->leftJoin('client', 'client.id', '=', 'pipeline.ClientId')
    ->leftJoin('users', 'users.id', '=', 'pipeline.UserId')
    ->leftJoin('leadstatus', function($join){
        $join->on('pipeline.PipeId', '=', 'leadstatus.id')
             ->on('leadstatus.CompanyNum', '=', 'pipeline.CompanyNum');
    })

    ->select(DB::raw('
                                    pipeline.id as id,
                                    DATE_FORMAT(pipeline.Dates, \'%d/%m/%Y\') as date,
                                    CONCAT(client.FirstName, \' \', client.LastName) as fullName,
                                    client.FirstName as firstName,
                                    client.LastName as lastName,
                                    client.id as clientId,
                                    IF(client.ContactMobile, client.ContactMobile, null) as phone,
                                    IF(client.Email IS NOT NULL,client.Email,null ) as email,
                                    pipeline.ClassInfoNames as intrested,
                                    users.display_name as agent,
                                    users.FirstName as agentFirstName,
                                    users.lastName as agentLastName,
                                    pipeline.Source as inputSource,
                                    BrandName as branch,
                                    leadstatus.Title as status
                                    '));

                                    
if (!empty($_GET['filter']['dateFrom']) && $_GET['filter']['dateFrom'] != "") {
    $fromDate = $_GET['filter']['dateFrom'];
    if (!empty($_GET['filter']['dateTo']) && $_GET['filter']['dateTo'] != "") {
        $toDate = $_GET['filter']['dateTo'];
    }
}

if (!empty($fromDate) && !empty($toDate)) {
    $q->whereRaw(DB::raw("DATE_FORMAT(pipeline.Dates, '%Y-%m-%d') between '$fromDate' and '$toDate'"));
} elseif (!empty($fromDate)) {
    $q->whereRaw(DB::raw("DATE_FORMAT(pipeline.Dates, '%Y-%m-%d') >= '$fromDate'"));
} elseif (!empty($fromDate)) {
    $q->whereRaw(DB::raw("DATE_FORMAT(pipeline.Dates, '%Y-%m-%d') <= $fromDate"));
} else {
    $fromDate = 'DATE_FORMAT(CURDATE(), \'%Y-%m-01 00:00:00\')';
    $toDate = 'CURDATE()';
    $q->whereRaw(DB::raw("DATE_FORMAT(pipeline.Dates, '%Y-%m-%d') between $fromDate and $toDate"));
}

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "fullName":$q->orderBy('client.FirstName', $dir);
            break;
        case "email":$q->orderBy('client.Email', $dir);
            break;
        case "phone":$q->orderBy('client.ContactMobile', $dir);
            break;
        case "date":$q->orderBy('pipeline.Dates', $dir);
            break;
    }
}

$filter = false;
$sql = sprintf("select * from (%s) as tbl", $q->toString());
$rest->answer->recordsFiltered = COUNT(DB::select($sql));

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "(tbl.firstName LIKE '" . $_GET['filter']['name'] . "%' OR tbl.lastName LIKE '" . $_GET['filter']['name'] . "%' OR CONCAT(tbl.firstName, ' ', tbl.lastName) LIKE '".$_GET['filter']['name']."%')";
    $filter = true;
}

// search by email
if (!empty($_GET['filter']['email']) && $_GET['filter']['email'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.email LIKE '" . $_GET['filter']['email'] . "%'";
    $filter = true;
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.phone LIKE '" . $_GET['filter']['phone'] . "%'";
    $filter = true;
}

// sarch by branch
if (!empty($_GET['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.branch = '" . $_GET['filter']['branch'] . "'";
    $filter = true;
}

// sarch by source
if (!empty($_GET['filter']['source']) && $_GET['filter']['source'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.inputSource LIKE '" . $_GET['filter']['source'] . "%'";
    $filter = true;
}

// search by intrested
if (!empty($_GET['filter']['intrested']) && $_GET['filter']['intrested'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.intrested LIKE '" . $_GET['filter']['intrested'] . "%'";
    $filter = true;
}

// search by agent
if (!empty($_GET['filter']['agent']) && $_GET['filter']['agent'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "(tbl.agent = '" . $_GET['filter']['agent'] . "')";
    $filter = true;
}
// search by agent
if (!empty($_GET['filter']['status']) && $_GET['filter']['status'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "(tbl.status = '" . $_GET['filter']['status'] . "')";
    $filter = true;
}



$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
$rest->answer->recordsFiltered = isset($filter) && $filter ? COUNT(DB::select($sql)) : $rest->answer->recordsFiltered;
$sql .= " LIMIT $limit OFFSET $start";

$results = DB::select($sql);

$rest->answer->recordsTotal = COUNT($results);
$rest->answer->items = $results;
$rest->answer->query = $sql;
