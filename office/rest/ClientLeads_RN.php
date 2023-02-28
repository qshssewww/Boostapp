<?php
header('Content-type: application/json');

require_once '../../app/init.php';

$db_success = 1;
$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));
$insertedIds = [];
$errorsIds = [];

function checkIsAValidDate($DateString){
    return (bool)strtotime($DateString);
}

$myDate = '2019/12/05';

if(checkIsAValidDate($myDate)){
    $timestamp = strtotime($myDate);
    $YMD = date("Y-m-d", $timestamp);
	echo $timestamp;
	echo "\n";
	echo $YMD;
}
else{
echo 'es';
}
die('');
$Department = [1, 2, 3];

// $all_item_data = DB::table('items')
//                         ->where('ItemName','=','הדרכת הורים')
//                         ->where('CompanyNum','=',68735)
//                         ->whereIn('Department',$Department)->select('id','Department','MemberShip','ItemPrice','ItemPriceVat','Vat')->get();

$item_data = DB::table('items')
                        ->where('ItemName','=','12 כניסות')
                        ->where('CompanyNum','=',100)
                        ->whereIn('Department',$Department)
                        ->get();

$valid_date = '2018-12-30 02:06:11';                        
$NotificationDays = 60;
$modifiedNotificationDate = date('Y-m-d', strtotime('-'.$NotificationDays.' day', strtotime($valid_date)));
print($modifiedNotificationDate);

die();
exit(json_encode([
            'success' => 1,
            'message' => 'Successful',
            'item_data' => $item_data,
        ]));
print_r($item_data);
print_r($all_item_data);
die('asdf');

if($dataArray && count($dataArray)){
    foreach ($dataArray as $key => $value) {
        $insertArray = json_decode(json_encode($value), true);
        print_r('insertArray');
        if($insertArray['Membership']){
        }
        die('yolo');
        // try {
        //     DB::table('client')->insert($insertArray);
        //     $lastId = DB::getPdo()->lastInsertId();
        //     array_push($insertedIds, $lastId);
        // } catch (Exception $e) {
        //     $db_success = 0;
        //     array_push($errorsIds, $insertArray[$insertArray]);
        // }
    }
    if($errorsIds){
        exit(json_encode([
            'success' => 1,
            'message' => 'Successful',
            'insertedIds' => $insertedIds,
            'errorsIds' => $errorsIds
        ]));
    }
    else{
        exit(json_encode([
            'success' => 1,
            'message' => 'Data inserted',
            'insertedIds' => $insertedIds
        ]));        
    }
}else{
    exit(json_encode([
        'success' => 0,
        'message' => 'Please send correct json'
    ]));
}
