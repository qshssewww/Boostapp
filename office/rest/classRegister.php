<?php
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('classstudio_act')
    ->where('classstudio_act.CompanyNum', '=', $rest->CompanyNum)
    ->whereBetween('classstudio_act.ClassDate', array($dateFrom, $dateTo))
    ->where('classstudio_act.StatusCount', '=', '0')
    ->select(
        'classstudio_act.ClassId as dbClassId',
        'classstudio_act.ClientId as dbClientId',
        'classstudio_act.FixClientId as FixClientId',
        'classstudio_act.TrueClientId as dbTrueClientId',

        'client.id as clientId',
        'client.CompanyName as fullName', // שם לקוח
        'client.ContactMobile as phone', // טלפון
        'client.BalanceAmount as balanceAmount', // הנה"ח
        DB::raw("CONCAT(JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].ItemText')), ' ', DATE_FORMAT(JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].TrueDate')), '%d/%m/%Y'), ' ', JSON_UNQUOTE(JSON_EXTRACT(client.MemberShipText, '$.data[0].TrueBalanceValue'))) AS membership"), // מנוי
        DB::raw('DATE_FORMAT(client_activities.TrueDate, "%d/%m/%Y") as memberExpire'), // תוקף
        DB::raw("IF((client_activities.Department IN (2,3)), CONCAT(client_activities.TrueBalanceValue, '/', client_activities.BalanceValue),  NULL) as ticket"), // כרטיסייה
        DB::raw('IF(classstudio_act.Remarks, classstudio_act.Remarks, null) as comment'), //הערה
        'clientmedical.Content as medical', // ממצאים רפואים
        'classstudio_act.ClassName as className', // שיעור
        DB::raw('DATE_FORMAT(classstudio_act.ClassDate, "%d/%m/%Y") as classDate'), // תאריך שיעור
        DB::raw("CONCAT(JSON_UNQUOTE(DATE_FORMAT(classstudio_act.ClassStartTime, '%H:%i')), '-', JSON_UNQUOTE(DATE_FORMAT(classstudio_act.ClassEndTime, '%H:%i'))) as classTime"), // שעת שיעור
        'guide.display_name as guideName', // שם מדריך
        DB::raw('IF(client.Brands=0, "סניף ראשי", brands.BrandName) as branchName')

    )
//    ->leftJoin('client', 'client.id', '=', DB::raw('IF(classstudio_act.TrueClientId=0, classstudio_act.ClientId, classstudio_act.TrueClientId)'))
    ->leftJoin('client', 'client.id', '=', 'classstudio_act.FixClientId')
    // ->leftJoin('client_activities', 'client.id', '=', 'client_activities.ClientId')
    ->leftJoin('client_activities', function($join) use($rest){
        $join
            ->on('client_activities.CompanyNum', '=', DB::raw($rest->CompanyNum))
//            ->on('client_activities.ClientId', '=', 'client.id')     
            ->on('client_activities.id', '=', 'classstudio_act.ClientActivitiesId');        
        
    })
    ->leftJoin('users as guide', 'guide.id', '=', 'classstudio_act.GuideId')
    ->leftJoin('clientmedical', DB::raw('clientmedical.ClientId = client.id AND clientmedical.Status = 0 '), 'and', DB::raw('(clientmedical.TillDate >= CURDATE() OR clientmedical.TillDate IS NULL) '))
    ->leftJoin('clientcrm', DB::raw('clientcrm.ClientId = client.id'), 'and', DB::raw('(clientcrm.TillDate >= CURDATE() OR clientcrm.TillDate IS NULL) AND clientcrm.StarIcon = 1 AND clientcrm.Status = 0'))
    ->leftJoin('brands', function ($join) use ($rest) {
        $join
            ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('brands.id', '=', 'client.Brands');
    });

/*

membership: fields.membership.val(),
ticket: fields.ticket.val(),

 */

// search by name
if (!empty($_REQUEST['filter']['fullName']) && $_GET['filter']['fullName'] != "") {
    $q->whereRaw("(client.FirstName LIKE '" . $_GET['filter']['fullName'] . "%' OR client.LastName LIKE '" . $_GET['filter']['fullName'] . "%' OR client.CompanyName LIKE '" . $_GET['filter']['fullName'] . "%')");
}

// search by branch
if (!empty($_REQUEST['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $q->where('brands.BrandName', 'like', '"' . $_GET['filter']['branch'] . '"');
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $q->where('client.ContactMobile', 'like', "'" . $_GET['filter']['phone'] . "%'");
}
// search by balanceAmount
if (!empty($_GET['filter']['balanceAmount']) && $_GET['filter']['balanceAmount'] != "") {
    $q->where('client.BalanceAmount', 'like', "'" . $_GET['filter']['balanceAmount'] . "%'");
}

// search by memberExpire
if (!empty($_GET['filter']['memberExpireDateFrom']) && $_GET['filter']['memberExpireDateFrom'] != "") {
    $q->whereBetween('client_activities.TrueDate', array("'".$_GET['filter']['memberExpireDateFrom']."'", "'".$_GET['filter']['memberExpiredateTo']."'"));
}

if (!empty($_GET['filter']['membership']) && $_GET['filter']['membership'] != "") {
    $q->where('client_activities.ItemText',  'like', "'%" . $_GET['filter']['membership'] . "%'");
}


// search by comment
if (!empty($_GET['filter']['comment']) && $_GET['filter']['comment'] != "") {
    $q->where('classstudio_act.Remarks', 'like', "'" . $_GET['filter']['comment'] . "%'");
}

// search by medical
if (!empty($_GET['filter']['medical']) && $_GET['filter']['medical'] != "") {
    $q->where('clientmedical.Content', 'like', "'" . $_GET['filter']['medical'] . "%'");
}

// search by crm
if (!empty($_GET['filter']['crm']) && $_GET['filter']['crm'] != "") {
    $q->where('clientcrm.Remarks', 'like', "'" . $_GET['filter']['crm'] . "%'");
}

// search by className
if (!empty($_GET['filter']['className']) && $_GET['filter']['className'] != "") {
    $q->where('classstudio_act.ClassName', 'like', "'%" . $_GET['filter']['className'] . "%'");
}

// search by classTime
if (!empty($_GET['filter']['classTime']) && $_GET['filter']['classTime'] != "") {
    $q->where('classstudio_act.ClassStartTime', '=', "'" . $_GET['filter']['classTime'] . "'");
}

// search by guideName
if (!empty($_GET['filter']['guideName']) && $_GET['filter']['guideName'] != "") {
    $q->where('guide.display_name', 'like', '"' . $_GET['filter']['guideName'] .'"' );
}

// // sort by column
// if (!empty($_GET['order'][0]['column'])) {
//     $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
//     $dir = $_GET['order'][0]['dir'];

//     switch ($sortName) {
//         case "fullName":$q->orderBy('client.FirstName', $dir);
//             break;
//         case "phone":$q->orderBy('client.ContactMobile', $dir);
//             break;
//         case "balanceAmount":$q->orderBy('client.BalanceAmount', $dir);
//             break;
//         case "memberExpire":$q->orderBy('client_activities.TrueDate', $dir);
//             break;
//         case "comment":$q->orderBy('classstudio_act.Remarks', $dir);
//             break;
//         case "medical":$q->orderBy('clientmedical.Content', $dir);
//             break;
//         case "className":$q->orderBy('classstudio_act.ClassName', $dir);
//             break;
//         case "classTime":$q->orderBy('classstudio_act.ClassStartTime', $dir);
//             break;
//         case "guideName":$q->orderBy('guide.display_name', $dir);
//             break;
//         case "crm":$q->orderBy('clientcrm.Remarks', $dir);
//             break;
//     }
// } else {
//     $q
//         ->orderBy('classstudio_act.ClassStartTime', 'ASC')
//         ->orderBy('classstudio_act.ClassDate', 'ASC')
//         ->orderBy('classstudio_act.GuideId', 'ASC');
// }

$q->groupBy('client.id');
$q->groupBy('classstudio_act.ClassNameType');
$q->groupBy('classstudio_act.ClassDate');
$q->groupBy('classstudio_act.ClassStartTime');

$query = sprintf("SELECT * FROM (%s) as tbl", $q->toString());
$query .= sprintf(" GROUP BY tbl.dbClassId, tbl.FixClientId");

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "fullName": $query .= sprintf(" ORDER BY tbl.fullName $dir");
            break;
        case "phone":$query .= sprintf(" ORDER BY tbl.phone $dir");
            break;
        case "balanceAmount":$query .= sprintf(" ORDER BY tbl.balanceAmount $dir");
            break;
        case "memberExpire":$query .= sprintf(" ORDER BY tbl.memberExpire $dir");
            break;
        case "comment":$query .=  sprintf(" ORDER BY tbl.comment $dir");
            break;
        case "medical":$query .= sprintf(" ORDER BY tbl.medical $dir");
         break;
        case "classDate":$query .= sprintf(" ORDER BY tbl.classDate $dir");    
            break;
        case "className":$query .= sprintf(" ORDER BY tbl.className $dir");
            break;
        case "classTime":$query .= sprintf(" ORDER BY tbl.classTime $dir");
            break;
        case "guideName":$query .= sprintf(" ORDER BY tbl.guideName $dir");
            break;
        case "crm":$query .= sprintf(" ORDER BY tbl.crm $dir");
            break;
    }
} else {
    $query .= sprintf(" ORDER BY tbl.classTime ASC, tbl.classDate ASC, tbl.guideName ASC");
}

$filter = false;
//if (!empty($_REQUEST['filter']['membership']) && $_GET['filter']['membership'] != "") {
//    $query .= ($filter?" AND ": " WHERE")." tbl.membership LIKE '".$_GET['filter']['membership']."%'";
//}
if (!empty($_REQUEST['filter']['ticket']) && $_GET['filter']['ticket'] != "") {
    $query .= ($filter?" AND ": " WHERE")." tbl.ticket LIKE '".$_GET['filter']['ticket']."%'";
}
if (!empty($_GET['order'][0]['column'])) {
    switch($sortName){
        case "membership": $query .= " ORDER BY tbl.membership $dir "; break;
        case "ticket": $query .= " ORDER BY tbl.ticket $dir "; break;
    }
}


// total amount without filter
$rest->answer->recordsFiltered = COUNT(DB::select($query));

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;

$query .= " LIMIT $limit OFFSET $start";

$q = DB::select($query);

$rest->answer->sql = $query;

$subSql = [];
// search for medical records, save machine time only rows that is not null
foreach ($q as &$row) {
    // 'clientmedical.Content as medical', // ממצאים רפואים
    // ->leftJoin('clientmedical', DB::raw('clientmedical.ClientId = client.id'), 'and', DB::raw('(clientmedical.TillDate <= CURDATE() OR clientmedical.TillDate IS NULL) '))
    if($row->medical){
        $query = DB::table('clientmedical')
        ->select('clientmedical.Content as medical')
        ->where('clientmedical.CompanyNum', '=',  DB::raw($rest->CompanyNum))
        ->where('clientmedical.ClientId', '=',  DB::raw($row->clientId))
        ->where('clientmedical.Status', '=', '0')
        ->whereRaw("(clientmedical.TillDate >= CURDATE() OR clientmedical.TillDate IS NULL)");
        $subSql[] = $query->toString();
        $row->medical = $query->get();
    }

    $query = DB::table('clientcrm')
    ->select('clientcrm.*')
    ->where('clientcrm.CompanyNum', '=',  DB::raw($rest->CompanyNum))
    ->where('clientcrm.ClientId', '=',  DB::raw($row->clientId))
    ->where('clientcrm.StarIcon', '=', DB::raw(1))
    ->where('clientcrm.Status', '=', '0')
    ->whereRaw("(clientcrm.TillDate >= CURDATE() OR clientcrm.TillDate IS NULL)");
    $subSql[] = $query->toString();
    $row->crm = $query->get();

    
}

$rest->answer->recordsTotal = COUNT($q);

$rest->answer->sqlMedical= $subSql;
$rest->answer->items = $q;
