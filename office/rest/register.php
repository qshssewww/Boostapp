<?php

$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL(1-DAYOFWEEK(CURDATE())) DAY)');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('client')
    ->where('client.CompanyNum', '=', $rest->CompanyNum)
    ->where('client.Status', '=', '0')
    ->whereBetween(DB::raw('DATE_FORMAT(client.Dates, "%Y-%m-%d")'), array($dateFrom, $dateTo))
    ->select(
        'client.id as clientId',
        'client.CompanyName as fullName',
        'client.ContactMobile as phone',
        'client.Email as email',
        'client.ConvertDate as dateConvert',
        'client.JoinDate as dateJoin',
        DB::Raw('DATE_FORMAT(client.Dob, "%d/%m/%Y") as dob'),
        DB::raw("IF(client.Gender = 0, 'לא ידוע', IF(client.Gender = 1, 'זכר', 'נקבה')) as gender"),
        DB::raw("CONCAT(JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].ItemText')), ' ', DATE_FORMAT(JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].TrueDate')), '%d/%m/%Y'), ' ', JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].TrueBalanceValue'))) AS membership"),
        DB::Raw('DATE_FORMAT(client.Dates, "%d/%m/%Y") as joinDate'),
        DB::Raw('DATE_FORMAT(client.LastClassDate, "%d/%m/%Y") as lastClassDate')

        // DB::raw('STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, "$.data[0].TrueDate")), "%Y-%m-%d") AS trueDate'),
        // DB::raw('JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, "$.data[0].TrueDate")) AS trueDate'),
        // DB::raw('JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, "$.data[0].ItemText")) AS ItemText'),
        // DB::raw('JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, "$.data[0].TrueBalanceValue")) AS TrueBalanceValue'),
    );

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {
    $q
        ->whereRaw("(client.FirstName LIKE '".$_GET['filter']['name']."%' OR client.LastName LIKE '".$_GET['filter']['name']."%' OR client.CompanyName LIKE '".$_GET['filter']['name']."%') ");
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $q->where('client.ContactMobile', 'like', "'".$_GET['filter']['phone'] . "%'");
}

// search by email
if (!empty($_GET['filter']['email']) && $_GET['filter']['email'] != "") {
    $q->where('client.Email', 'like', "'".$_GET['filter']['email'] . "%'");
}

// search by gender
if (isset($_GET['filter']['gender']) && $_GET['filter']['gender'] != "") {
    $q->where('client.Gender', '=', (int) $_GET['filter']['gender']);
}

// search by bday range
if (!empty($_GET['filter']['bdayStart']) && $_GET['filter']['bdayStart'] != "") {
    $q->whereBetween(DB::raw('client.Dob'), array("'".$_GET['filter']['bdayStart']."'", "'".$_GET['filter']['bdayEnd']."'"));
}

// search by lastClass
if (!empty($_GET['filter']['lastClassStart']) && $_GET['filter']['lastClassStart'] != "") {
    $q->whereBetween(DB::raw('client.LastClassDate'), array("'".$_GET['filter']['lastClassStart']."'", "'".$_GET['filter']['lastClassEnd']."'"));
}

// search by convertRange
if (!empty($_GET['filter']['convertRangeStart']) && $_GET['filter']['convertRangeStart'] != "") {
    $q->whereBetween(DB::raw('client.ConvertDate'), array("'".$_GET['filter']['convertRangeStart']." 00:00:00'", "'".$_GET['filter']['convertRangeEnd']." 23:59:59'"));
}


// search by joinRange
if (!empty($_GET['filter']['joinRangeStart']) && $_GET['filter']['joinRangeStart'] != "") {
    $q->whereBetween(DB::raw('client.JoinDate'), array("'".$_GET['filter']['joinRangeStart']."'", "'".$_GET['filter']['joinRangeEnd']."'"));
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
        case "date":$q->orderBy('client.Dates', $dir);
            break;
        case "gender":$q->orderBy('client.Gender', $dir);
            break;
        case "dob":$q->orderBy('client.Dob', $dir);
            break;
        case "member":$q->orderBy('client.MemberShipText', $dir);
            break;
        case "lastClass":$q->orderBy('client.LastClassDate', $dir);
            break;
        case "dateConvert":$q->orderBy('client.ConvertDate', $dir);
            break;
        case "dateJoin":$q->orderBy('client.JoinDate', $dir);
            break;
    }
} else {
    $q->orderBy('client.Dates', 'asc');
}

$query = sprintf("SELECT * FROM (%s) as tbl", $q->toString());
// search by member
if (!empty($_GET['filter']['member']) && $_GET['filter']['member'] != "") {
    $query .= " WHERE membership LIKE '".$_GET['filter']['member']."%'";
}


// total amount without filter
$rest->answer->recordsFiltered = COUNT(DB::select($query));

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;

$query .= " LIMIT $limit OFFSET $start";

$results = DB::select($query);
// total items display
$rest->answer->recordsTotal = COUNT($results);


$rest->answer->items = $results;
$rest->answer->query = $query;
