<?php
header('Content-type: application/json');

require_once '../../app/init.php';
require_once '../../office/services/ClientService.php';

$db_success = 1;
$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));
$insertedIds = [];
$errorsIds = [];

if($dataArray->CompanyNum && $dataArray->csvData && count($dataArray->csvData)){
    $CompanyNum = $dataArray->CompanyNum;
    $csvData = $dataArray->csvData;
    
    foreach ($csvData as $key => $value) {
        $insertArray = json_decode(json_encode($value), true);
        if($insertArray['MembershipData']){
            try {
                $Department = [1, 2, 3];
                        
                $item_data = DB::table('items')
                    ->where('id', '=', $insertArray['MembershipData']['MemberShip'])
                ->where('CompanyNum','=',$CompanyNum)
                ->whereIn('Department',$Department)->select('id','Department')->get();
                if($item_data){
                    if($item_data[0]->Department == 1){
                        print_r($item_data[0]->Department);
                        die('firstIf');
                        // $client_activities_data['ItemId'] = $item_data[0]->id;
                    }
                    if($item_data[0]->Department == 2 || $item_data[0]->id == 3){
                        print_r($item_data[0]->Department);
                        die('senondIF');
                        // $client_activities_data['ItemId'] = $item_data[0]->id;
                    }
                    // $client_activities_data['ItemId'] = $item_data[0]->id;
                }

                $insert_to_client =  $insertArray;
                unset($insert_to_client['MembershipData']);
                $insert_to_client['status'] = 1;        
                $insert_to_client['CompanyNum'] = $CompanyNum;
                $lastId = ClientService::addClient($insert_to_client);
                $lastId = $lastId['Message']['client_id'];
                $insertedIds[] = $lastId;
                if($lastId){
                    $client_activities_data = [
                        'CompanyNum' => $CompanyNum,
                        'ClientId' => $lastId,
                    ];
                    if($insertArray['MembershipData']['MemberShip']){
                        $Department = [1, 2, 3];

                        $item_data = DB::table('items')
                            ->where('id', '=', $insertArray['MembershipData']['MemberShip'])
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->whereIn('Department', $Department)
                            ->select('id', "ItemName", 'Department')->get();
                        if($item_data[0]){
                            $client_activities_data['ItemId'] = $item_data[0]->id;
                            $client_activities_data['ItemText'] = $item_data[0]->ItemName;
                        }
                    }
                    if($insertArray['MembershipData']['StartDate']){
                        $client_activities_data['StartDate'] = $insertArray['MembershipData']['StartDate'];
                    }
                    if($insertArray['MembershipData']['VaildDate']){
                        $client_activities_data['VaildDate'] = $insertArray['MembershipData']['VaildDate'];
                    }
                    if($insertArray['MembershipData']['TrueBalanceValue']){
                        $client_activities_data['TrueBalanceValue'] = $insertArray['MembershipData']['TrueBalanceValue'];
                    }
                   
                    try {
                        DB::table('client_activities')->insert($client_activities_data);
                        $last_client_activity = DB::getPdo()->lastInsertId();
                    }catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                $db_success = 0;
                array_push($errorsIds,  $insertArray);
            }
        }else{
            try {
                $lastId = ClientService::addClient((array) $value);
                $lastId = $lastId['Message']['client_id'];
                $insertedIds[] = $lastId;
            } catch (Exception $e) {
                $db_success = 0;
                array_push($errorsIds, $value);
            }
        }
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
