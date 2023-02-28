<?php
$time_start = microtime(true); 
function totalTime(){
    global $time_start;
    return (microtime(true) - $time_start)/60;
}

$range = (!empty($_GET['range']) && in_array($_GET['range'], ["3months", "month", "week"]))?$_GET['range']:"week";

switch($range):
    case "week": $range = '7 Day'; break;
    case "month": $range = '1 Month'; break;
    case "3months": $range = '3 Month'; break;
endswitch;



        // $rest->CompanyNum =569121;
        // $rest->CompanyNum =68735;
        // $rest->CompanyNum =100;
        $dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('(CURDATE() - INTERVAL '.$range.')');
        $dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

        if(!empty($_REQUEST['filter']['daysRange']) && is_array($_REQUEST['filter']['daysRange'])){
            $days = array();
            for ($date = strtotime($_REQUEST['filter']['dateFrom']); $date <= strtotime($_REQUEST['filter']['dateTo']); $date += 60 * 60 * 24) {
                if(in_array(date('N', $date), $_REQUEST['filter']['daysRange'])) $days[] = "'".date('Y-m-d', $date)."'";
            }
            $rest->answer->start = strtotime($_REQUEST['filter']['dateFrom']);
            $rest->answer->end = strtotime($_REQUEST['filter']['dateTo']);
        }


        /*$sql = "
            SELECT
                `classstudio_date`.`id` AS `classId`,
                `classstudio_date`.`ClassName` AS `className`,
                `classstudio_date`.`StartDate` AS `rawDate`,
                `classstudio_date`.`MaxClient` AS `MaxClient`,
                `classstudio_date`.`ClientRegister` AS `ClientRegister`,
                DATE_FORMAT(
                    classstudio_date.StartDate,
                    '%d/%m/%Y'
                ) AS date,
                `WatingList` AS `waitingList`
            FROM
                `classstudio_date`
            WHERE
                `CompanyNum` = $rest->CompanyNum AND `Status` = 1 AND (DATE_FORMAT(
                    classstudio_date.StartDate,
                    '%Y-%m-%d'
                ) BETWEEN $dateFrom AND $dateTo ) ";

            if(!empty($_REQUEST['filter']['className']) && $_REQUEST['filter']['className'] != ""){
                $sql .= " AND `classstudio_date`.`ClassName` = '".$_REQUEST['filter']['className']."' ";
            }

            $sql .= "GROUP BY
                `classstudio_date`.`ClassNameType`, `classstudio_date`.`StartDate`
            ORDER BY
                `classstudio_date`.`StartDate` ASC, `classstudio_date`.`ClassName`
            DESC        
        ";*/

        $sql = "
        SELECT
            `classstudio_date`.`id` AS `classId`,
            `classstudio_date`.`ClassName` AS `className`,
            `classstudio_date`.`StartDate` AS `rawDate`,
            `classstudio_date`.`MaxClient` AS `MaxClient`,
            `classstudio_date`.`ClientRegister` AS `ClientRegister`,
            DATE_FORMAT(
                classstudio_date.StartDate,
                '%d/%m/%Y'
            ) AS date,
            DATE_FORMAT(classstudio_date.StartTime, '%H:%i') as classStartTime,
            `WatingList` AS `waitingList`,
            (
                SELECT 
                    count(*) as count 
                FROM `classstudio_act` 
                WHERE 
                    Status = 4 and 
                    CompanyNum = classstudio_date.CompanyNum and 
                    classstudio_act.ClassId = `classstudio_date`.`id`
            ) as lateCancelation,
            (
                SELECT 
                    count(*) as count 
                FROM `classstudio_act` 
                WHERE 
                    Status IN(7,8) and 
                    CompanyNum = classstudio_date.CompanyNum and 
                    classstudio_act.ClassId = `classstudio_date`.`id`
            ) as absent,
            (
                SELECT 
                    count(*) as count 
                FROM `classstudio_act` 
                WHERE 
                    Status IN(3,19,5) and 
                    CompanyNum = classstudio_date.CompanyNum and 
                    classstudio_act.ClassId = `classstudio_date`.`id`                
            ) as cancelation

        FROM
            `classstudio_date`
        WHERE
            `CompanyNum` = $rest->CompanyNum AND `Status` = 1 AND ";
            
            if(!empty($_REQUEST['filter']['daysRange']) && is_array($_REQUEST['filter']['daysRange'])){
                $sql .= " (DATE_FORMAT(
                    classstudio_date.StartDate,
                    '%Y-%m-%d'
                ) IN (".implode(",", $days).")) ";
            }else{
                $sql .= " (DATE_FORMAT(
                    classstudio_date.StartDate,
                    '%Y-%m-%d'
                ) BETWEEN $dateFrom AND $dateTo ) ";
            }
            
            if(!empty($_REQUEST['filter']['time'])){
                $sql .= " AND DATE_FORMAT(classstudio_date.StartTime, '%H:%i') = '".$_REQUEST['filter']['time']."'";
            }
            

        if(!empty($_REQUEST['filter']['className']) && $_REQUEST['filter']['className'] != ""){
            $getTypeId = DB::table('class_type')->where('CompanyNum','=',$rest->CompanyNum)->where('Type', '=', $_REQUEST['filter']['className'])->first();
            // $sql .= " AND `classstudio_date`.`ClassName` = '".$_REQUEST['filter']['ClassName']."' ";
            $sql .= " AND `classstudio_date`.`ClassNameType` = '".$getTypeId->id."' ";
        }

        $sql .= "GROUP BY
            `classstudio_date`.`ClassNameType`, `classstudio_date`.`StartDate`, `classstudio_date`.`StartTime`

        ORDER BY
            `classstudio_date`.`StartDate` ASC, `classstudio_date`.`StartTime` ASC, `classstudio_date`.`ClassName`
        DESC          
        ";


    $rest->answer->debug = new stdClass();
    $rest->answer->debug->query = $sql;

    $items = DB::select($sql);

    
    $rest->answer->debug->time = totalTime();
    
/*
    foreach($items as &$item){
        
        $item->lateCancelation = DB::select("
        SELECT 
                count(*) as count 
            FROM `classstudio_act` 
            WHERE 
                Status = 4 and 
                CompanyNum = $rest->CompanyNum and 
                classstudio_act.ClassId = $item->classId
        ")[0]->count;

        $item->cancelation = DB::select("
        SELECT 
                count(*) as count 
            FROM `classstudio_act` 
            WHERE 
                Status IN(3,19,5) and 
                CompanyNum = $rest->CompanyNum and 
                classstudio_act.ClassId = $item->classId
        ")[0]->count;

        $item->absent = DB::select("
        SELECT 
                count(*) as count 
            FROM `classstudio_act` 
            WHERE 
                Status IN(7,8) and 
                CompanyNum = $rest->CompanyNum and 
                classstudio_act.ClassId = $item->classId
        ")[0]->count;



        // unset($item->rawDate);
        // unset($item->classId);
    }
    $rest->answer->debug->loopItems = totalTime();
    */

if(!empty($_GET['display']) && $_GET['display'] == 'details'){
    $rest->answer->items =  $items ;
    $rest->answer->recordsTotal =  count($items) ;
    $rest->answer->recordsFiltered =  count($items) ;

    // $rest->answer->sql =    str_replace(array("\n", "\r"), '', $sql);

}else{
    // for dashboard
    $rest->answer->items = new StdClass();
    $rest->answer->items->spacesTaken = 0; // הגעות
    $rest->answer->items->absent = 0; // לא היגיעו
    $rest->answer->items->lateCancelation = 0; // ביטול מאוחר
    $rest->answer->items->spacesAvailable = 0; // מקומות פנויים
    $rest->answer->items->waitingList = 0; // רשימת המתנה
    $rest->answer->items->MaxClient = 0; // רשימת המתנה

    $rest->answer->items->rawData = new stdClass();
    $rest->answer->items->rawData->spacesTaken = 0;
    $rest->answer->items->rawData->absent = 0;
    $rest->answer->items->rawData->lateCancelation = 0;
    $rest->answer->items->rawData->spacesAvailable = 0;
    $rest->answer->items->rawData->waitingList = 0;
    $rest->answer->items->rawData->MaxClient = 0;


    foreach($items as $item){
        $rest->answer->items->spacesTaken += (int) $item->ClientRegister;
        $rest->answer->items->rawData->spacesTaken += (int) $item->ClientRegister;


        $rest->answer->items->absent += (int) $item->absent;
        $rest->answer->items->rawData->absent += (int) $item->absent;


        $rest->answer->items->lateCancelation += (int) $item->lateCancelation;
        $rest->answer->items->rawData->lateCancelation += (int) $item->lateCancelation;


        $rest->answer->items->spacesAvailable += ((int) $item->MaxClient - (int) $item->ClientRegister);
        $rest->answer->items->rawData->spacesAvailable += ((int) $item->MaxClient - (int) $item->ClientRegister);


        $rest->answer->items->waitingList += (int) $item->waitingList;
        $rest->answer->items->rawData->waitingList += (int) $item->waitingList;


        $rest->answer->items->MaxClient += (int) $item->MaxClient;
        $rest->answer->items->rawData->MaxClient += (int) $item->MaxClient;
    }

    // calculate percentage
    if( $rest->answer->items->waitingList) $rest->answer->items->waitingList = number_format(($rest->answer->items->waitingList/$rest->answer->items->MaxClient)*100, 2);
    if( $rest->answer->items->spacesTaken) $rest->answer->items->spacesTaken = number_format(($rest->answer->items->spacesTaken/$rest->answer->items->MaxClient)*100, 2);
    if( $rest->answer->items->absent) $rest->answer->items->absent = number_format(($rest->answer->items->absent/$rest->answer->items->MaxClient)*100, 2);
    if( $rest->answer->items->lateCancelation) $rest->answer->items->lateCancelation = number_format(($rest->answer->items->lateCancelation/$rest->answer->items->MaxClient)*100, 2);
    if( $rest->answer->items->spacesAvailable) $rest->answer->items->spacesAvailable = number_format(($rest->answer->items->spacesAvailable/$rest->answer->items->MaxClient)*100, 2);
    if( $rest->answer->items->waitingList) $rest->answer->items->waitingList = number_format(($rest->answer->items->waitingList/$rest->answer->items->MaxClient)*100, 2);
    unset($rest->answer->items->MaxClient);
    $rest->answer->debug->totalSum = totalTime();

    unset($rest->answer->debug);
    

}