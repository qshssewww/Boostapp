<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $arr = [
        'CompanyNum' => $rest->CompanyNum,
        'ClientId' => $data['clientId'],
        'Branch' => DB::raw("(select client.Brands from client where client.id=" . $data['clientId'] . " and client.CompanyNum = $rest->CompanyNum)"),
        'Date' => (string) "'".$data['date']."'",
        'Gender' => $data['gender'] == 'male' ? 1 : 2,
        'Age' => $data['age'],
        'Weight' => $data['weight'],
    ];
    $debugInsertSql = true; // this will stop the insert and show the sql
    $rest->answer->sql = DB::table('medical_weight')->insert($arr);
    $debugInsertSql = false;

    // a hack, query builder insert dosn't like me
    $rest->answer->items = DB::statement($rest->answer->sql);

    // unset($rest->answer->sql);

    if($rest->answer->items === FALSE){
        $rest->answer->code = 500;
        $rest->answer->message = 'הנתון לא נקלט';
    }

    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $q = DB::table('medical_weight as m')
        ->select(
            'c.CompanyName as fullName',
            'm.Date as date',
            DB::raw('DATE_FORMAT(m.Date, "%d/%m/%Y") as displayDate'),
            'm.Age as age',
            DB::raw("IF(m.Gender=1,'זכר','נקבה') as gender"),
            'm.Weight as weight'
        )
        ->where('m.CompanyNum', '=', $rest->CompanyNum)
        ->where('m.status', '=', '1')
        ->where('m.ClientId', '=', !empty($_GET['clientId'])?$_GET['clientId']:false)
        ->leftJoin('client as c', function($join){
            $join->on('c.CompanyNum', '=', 'm.CompanyNum')
                 ->on('c.id', '=', 'm.ClientId');
        })
        ->orderBy('m.Date', 'asc')
        ;

        // total without Filter
        $rest->answer->userTotalData = COUNT($q->get());

        // daterange search - recived dates as js date type
        if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])){
            $dateFrom = new DateTime($_GET['dateFrom']);
            $dateTo = new DateTime($_GET['dateTo']);
            $q->whereBetween('m.Date', [date_format($dateFrom, 'Y-m-d'), date_format($dateTo, 'Y-m-d')]);
        }

        // filter by weight
        if(!empty($_GET['weight'])){
            $q->where('m.Weight', '=', (float) $_GET['weight']);
        }

        // total withFilters
        $rest->answer->totalFilltered = COUNT($q->get());

        $limit = ( !empty($_GET['limit']) && (int) $_GET['limit'] && (int) $_GET['limit'] >=1)?(int) $_GET['limit'] : 2;

        $page = ( !empty($_GET['page']) && (int) $_GET['page'] && (int) $_GET['page'] >=1)?(int) $_GET['page'] : 1;

        $q->limit($limit)->offset($limit*($page-1));

        $rest->answer->items = $q->get();
        $rest->answer->count = count($rest->answer->items);
        $rest->answer->pages = count($rest->answer->items) ? ceil( $rest->answer->totalFilltered /count($rest->answer->items) ) : 1;
        $rest->answer->currentPage = $page;
        $rest->answer->sql = $q->toString();


}
