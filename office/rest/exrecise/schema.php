<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if(!empty($_GET['action']) && $_GET['action'] == 'delete'){
        if(empty($data['id'])){
            $rest->answer->err = true;
            $rest->answer->code = 400;
            $rest->answer->message = 'ID is required';
            exit;
        }
        $sql = sprintf("UPDATE `exrecise` SET `Status` = IF(`Status`=1, 0, 1) WHERE id=%d AND CompanyNum=%d", $data['id'], $rest->CompanyNum);
        DB::select($sql);
        $rest->answer->message = "התרגיל עודכן בהצלחה";
        exit;
    }



    $arr = [
        'CompanyNum' => $rest->CompanyNum,
        'Title' => $data['name'],
        'Description' => $data['description']
    ];

    // $debugInsertSql = true; // this will stop the insert and show the sql
    // $insert = DB::table('exrecise')->insert($arr);
    // $debugInsertSql = false;

    DB::table('exrecise')->insert($arr);
    $lastId = DB::getPdo()->lastInsertId();

    $sql = '';

    if(!empty($data['group']) && is_array($data['group'])){
        $sql .= "INSERT INTO `exrecisetags` (`Tag`, `CompanyNum`, `ExreciserId`) VALUES ";
        foreach($data['group'] as $tag){
            $sql .= "('".$tag['text']."', $rest->CompanyNum, $lastId)";
            if (next($data['group'])==true) $sql .= ",";
        }
        $sql .= " ON DUPLICATE KEY UPDATE `Status`=". (!empty($tag['status'])?(int) $tag['status']:1) .";";
        $rest->answer->insertTags = $sql;
    }

    if(!empty($data['steps']) && is_array($data['steps'])){
        $insert = '';
        // prepare statement
        $insertSql = "INSERT INTO `exrecisesteps`(`CompanyNum`, `ExreciserId`, `Position`, `Status`, `Name`, `ExreciseRepeat`, `ExreciseSets`, `Time`, `TimeUnit`, `Weight`, `WeightUnit`, `Distance`, `DistanceUnit`, `Break`, `BreakUnit`) VALUES ";
        $index = 0;
        foreach($data['steps'] as $step){
            // check if new or just updating
            if(empty($step['id'])){
                $insert .= "($rest->CompanyNum, $lastId, $index, 1,";
                $insert .= "'".$step['name']."',";
                $insert .= (!empty($step['repeat']) ? (int) $step['repeat'] : 'NULL') . ",";
                $insert .= (!empty($step['sets']) ? (int) $step['sets'] : 'NULL') . ",";

                foreach(array('time', 'weight', 'distance', 'break') as $el){
                    $insert .= ((isset($step[$el]) && isset($step[$el]['amount'])) ? (float) $step[$el]['amount'] : 'NULL'). ",";
                    $insert .= ((isset($step[$el]) && isset($step[$el]['unit']) && !empty($step[$el]['unit']['name'])) ? "'".(string) $step[$el]['unit']['name']. "'" : 'NULL'). ",";
                }
                $insert = rtrim($insert, ",");
                $insert .= "),";

            }else{
                $update = "UPDATE `exrecisesteps` SET `Position`=".$index.", `Status`=".(int) $step['status'] .", ";
                $update = "`Name`= '".$step['name']."',";
                $update = "`ExreciseRepeat`= ".(!empty($step['repeat']) ? (int) $step['repeat'] : 'NULL').",";
                $update = "`ExreciseSets`= ".(!empty($step['sets']) ? (int) $step['repeat'] : 'NULL').",";
                foreach(array('time', 'weight', 'distance', 'break') as $el){
                    $update = "`".ucfirst($el) ."`=". ((isset($step[$el]) && isset($step[$el]['amount'])) ? (float) $step[$el]['amount'] : 'NULL'). ",";
                    $update = "`".ucfirst($el) ."Unit`=". ((isset($step[$el]) && isset($step[$el]['unit']) && !empty($step[$el]['unit']['name'])) ? "'".(string) $step[$el]['unit']['name']. "'" : 'NULL'). ",";
                }
                $update = rtrim($update, ",").";";
                $sql .= $update;
                unset($update);
            }
            $index++;
        }
        if($insert != '') $sql .= $insertSql.rtrim($insert, ",").";";
        unset($insert, $insertSql);
    }

    $rest->answer->exeSql = DB::select($sql);
    $rest->answer->id =  $lastId;
    $rest->answer->exrecise =  $rest->getExrecise($lastId)[0];


    

    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $rest->answer->items = $rest->getExrecise((int) !empty($_GET['id'])?$_GET['id']:'0', (!empty($_GET['tags'])?array_map('trim', explode(",", $_GET['tags'])):array()) );
    $rest->answer->tags = (!empty($_GET['tags'])?array_map('trim', explode(",", $_GET['tags'])):array());

}
