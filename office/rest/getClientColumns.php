<?php
header('Access-Control-Allow-Origin', '*');
header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS,DELETE,PUT');
header('Content-type: application/json');

require_once '../../app/init.php';

$db_success = 1;
try {
    $query = 'SHOW COLUMNS FROM client';
    $column_name = 'Field';
    $reverse = false;
    
    foreach(DB::select($query) as $column)
    {
        $columns[] = $column->$column_name;
    }

    if($reverse)
    {
        $columns = array_reverse($columns);
    }
} catch (Exception $e) {
    $db_success = 0;
}

if($db_success){
    exit(json_encode([
        'success' => 1,
        'column_list' => $columns,
        'column_count' => count($columns)
    ]));    
}else{
    exit(json_encode([
        'success' => 0,
        'db_success' => $db_success,
        'message' => 'columns not found'
    ]));
}
