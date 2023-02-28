<?php

/* Angular save clock via application/json */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    $_PUT = file_get_contents('php://input');
    try{
        $_PUT = json_decode($_PUT); 
    } catch (Exception $e) {
        $rest->answer->err = true;
        $rest->answer->message = $e->getMessage();
        exit;
    }
    

    $sql = '';

    $userId = $_PUT->userId;

    foreach ($_PUT->dates as $el) {
        
        $date = strtotime($el->jsDate);
        $Times = date('Y-m-d H:i:s', $date);
        $Dates = date('Y-m-d', $date);
        if(isset($el->id) && (int) $el->id){
            $sql .= "UPDATE timekeeper SET Times='$Times', Status='$el->Status', UserAction=$rest->UserId WHERE id=$el->id AND CompanyNum=$rest->CompanyNum;";
        }else{
            $sql .="INSERT INTO `timekeeper`(`CompanyNum`, `UserId`, `Dates`, `Times`, `Act`, `TimeAction`, `UserAction`, `Status`) VALUES ($rest->CompanyNum, $userId, '$Dates', '$Times', '$el->Act', CURRENT_TIMESTAMP, $rest->UserId, $el->Status);";
        }
    }

    try{
        DB::select($sql);
    } catch (Exception $e) {
        $rest->answer->err = true;
        $rest->answer->message = $e->getMessage();
        exit;       
    }

    $rest->answer->err = false;
    // $rest->answer->items = $_PUT;
    // $rest->answer->sql = $sql;

    exit;
}



$month = !empty($_GET['month']) ?(int)  $_GET['month'] : false;
$year = !empty($_GET['year']) ? (int) $_GET['year'] : false;
$userId = !empty($_GET['userId']) ? (int) $_GET['userId'] : false;

if(!$month || !$year || !$userId){
    $rest->answer->err = true;
    $rest->answer->message = 'חסר פרמטרים לדוח זה';
    $rest->answer->code = 500;
    exit;
}

$q = DB::table('timekeeper')
        ->where('timekeeper.CompanyNum', '=', $rest->CompanyNum)
        ->where('timekeeper.UserId', '=', $userId)
        ->where(DB::raw('DATE_FORMAT(Dates, "%Y-%m")'), '=', sprintf("%s-%s", $year, str_pad($month, 2, '0', STR_PAD_LEFT)))
        ->select('*', DB::raw('UNIX_TIMESTAMP(Times) * 1000 as jsDate'))
        ->orderBy('Times');

$items =  $q->get();

$byDate = new StdClass();

for ($i=0; $i < count($items); $i++) { 
    $data = new StdClass();
    $data->id = $items[$i]->id;
    // $data->TimeAction = $items[$i]->TimeAction;
    $data->Act = $items[$i]->Act;
    $data->Status = $items[$i]->Status;
    // $data->UserAction = $items[$i]->UserAction;
    $data->jsDate = (float) $items[$i]->jsDate;
    $byDate->{$items[$i]->Dates}[] = $data;
    unset($data);
}


// $rest->answer->items = array_values((array) $byDate);
$rest->answer->items = ((array) $byDate);
// $rest->answer->sql = $q->toString();