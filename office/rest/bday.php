<?php

$month = str_pad((int) (!empty($_REQUEST['filter']['dob']) ? $_REQUEST['filter']['dob'] : date('m', time())), 2, 0, STR_PAD_LEFT);

$q = DB::table('client')
    ->where('client.CompanyNum', '=', $rest->CompanyNum)
    ->where('client.Status', '=', '0')
    ->whereBetween(DB::raw("DATE_FORMAT(client.Dob, '%m-%d')"), array("'$month-01'", "'$month-31'"))
// ->orderBy(DB::raw("DATE_FORMAT(Dob,'%M %d')"), 'asc')
    ->select(
        'client.id as clientId',
        'client.CompanyName as fullName',
        'client.ContactMobile as phone',
        'client.Email as email',
        DB::Raw('DATE_FORMAT(client.Dob, "%d/%m/%Y") as dob'),
        DB::raw("IF(client.Gender = 0, '".lang('other')."', IF(client.Gender = 1, '".lang('male')."', '".lang('female')."')) as gender"),
        DB::raw('IF(client.Brands=0, "'.lang('primary_branch').'", brands.BrandName) as branch')
    )
    ->leftJoin('brands', function ($join) use ($rest) {
        $join
            ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('brands.id', '=', 'client.Brands');
    });

// search by gender
if (!empty($_GET['filter']['gender']) && $_GET['filter']['gender'] != "") {
    $q->where('client.Gender', '=', (int) $_GET['filter']['gender']);
}

// search by branch
if (!empty($_GET['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $q->where('brands.BrandName', '=', (int) $_GET['filter']['branch']);
}

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {
    $q
        ->whereRaw("(client.FirstName LIKE '".$_GET['filter']['name']."%' OR client.LastName LIKE '".$_GET['filter']['name']."%')");
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $q->where('client.ContactMobile', 'like', "'".$_GET['filter']['phone'] . "%'");
}

// search by email
if (!empty($_GET['filter']['email']) && $_GET['filter']['email'] != "") {
    $q->where('client.Email', 'like', "'".$_GET['filter']['email'] . "%'");
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
        case "gender":$q->orderBy('client.Gender', $dir);
            break;
        case "dob":$q->orderBy('client.Dob', $dir);
            break;
    }
} else {
    $q->orderBy('client.FirstName', 'asc');
}

// total amount without filter
$rest->answer->recordsFiltered = COUNT(DB::select($q->toString()));

$q->offset((!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0)
    ->limit((!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100);

$results = DB::select($q->toString());
// total items display
$rest->answer->recordsTotal = COUNT($results);

$rest->answer->items = $results;

$rest->answer->query = $q->toString();
