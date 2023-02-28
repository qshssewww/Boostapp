<?php
header('Content-type: application/json');

require_once '../../app/init.php';
require_once '../../office/services/ClientService.php';
require_once '../Classes/CompanyProductSettings.php';

$db_success = 1;
$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));
$insertedIds = [];
$errorsIds = [];
$not_valid = [];

if($dataArray->CompanyNum && $dataArray->csvData && count($dataArray->csvData)){
    $CompanyNum = $dataArray->CompanyNum;
    $csvData = $dataArray->csvData;
    
    foreach ($csvData as $key => $value) {
        $insertArray = json_decode(json_encode($value), true);
        $rejection_array = json_decode(json_encode($value), true);
        //START
        // if(!isset($insertArray['Email']) && !isset($insertArray['ContactPhone'])){
        //     $rejection_array['rejection_reason'] = 'Email or Phonenumber is required';
        //     array_push($not_valid , $rejection_array);
        //     continue;
        // }

        if((!isset($insertArray['Email']) || !empty($insertArray['Email'])) && (!isset($insertArray['ContactPhone']) || empty($insertArray['ContactPhone']))){
            $rejection_array['rejection_reason'] = 'Email and Phonenumber cannot be empty';
            array_push($not_valid , $rejection_array);
            continue;
        }

        if(isset($insertArray['Email'])){
            $check_email = check_email($insertArray['Email']);
            if($check_email){
                $if_email_exists = DB::table('client')
                ->where('Email','=',$insertArray['Email'])
                ->where('CompanyNum','=',$CompanyNum)
                ->pluck('id');
                if($if_email_exists){
                    $rejection_array['rejection_reason'] = 'Email already Exists';
                    array_push($not_valid , $rejection_array);
                    continue;
                }

            }else{
                $rejection_array['rejection_reason'] = 'Invalid Email Address';
                array_push($not_valid , $rejection_array);
                continue;                
            }
        }

        if(isset($insertArray['ContactPhone']) && !empty($insertArray['ContactPhone'])){
            $insertArray['ContactPhone'] = substr($insertArray['ContactPhone'], 0, 3) == '972' ? '+'.$insertArray['ContactPhone'] : $insertArray['ContactPhone'];
            $check_phone = check_phone($insertArray['ContactPhone']);
            if($check_phone){
                $if_phone_exists = DB::table('client')
                ->where('ContactMobile','=',$insertArray['ContactPhone'])
                ->where('CompanyNum','=',$CompanyNum)
                ->pluck('id');
                if($if_phone_exists){
                    $rejection_array['rejection_reason'] = 'Phone/Mobile number already Exists';
                    array_push($not_valid , $rejection_array);
                    continue;
                }else{
                    $insertArray['ContactMobile'] = $insertArray['ContactPhone'];
                }

            }else{
                $rejection_array['rejection_reason'] = 'Invalid Phone Number';
                array_push($not_valid , $rejection_array);
                continue;  
            }
            
        }

        if(isset($insertArray['Dob']) && !empty($insertArray['Dob'])){
            $Dob = $insertArray['Dob'];
            if(checkIsAValidDate($Dob)){
                $timestamp = strtotime($Dob);
                $insertArray['Dob'] = date("Y-m-d", $timestamp);
            }
            else{
                $rejection_array['rejection_reason'] = 'Invalid date format for DOB Expected format: YYYY-MM-DD';
                array_push($not_valid , $rejection_array);
                continue;
            }
        }
        $FirstName = '';
        $LastName = '';
        if(!isset($insertArray['FirstName']) || !isset($insertArray['LastName'])){
            $rejection_array['rejection_reason'] = 'FirstName or LastName is required';
            array_push($not_valid , $rejection_array);
            continue;   
        }
        if(isset($insertArray['FirstName']) && !empty($insertArray['FirstName'])){
            if($insertArray['FirstName']){
                $FirstName = $insertArray['FirstName'];
            }
        }
        
        if(isset($insertArray['LastName']) && !empty($insertArray['LastName'])){
           if($insertArray['LastName']){
                $LastName = $insertArray['LastName'];
            }
        }
        
        $insertArray['CompanyName'] = $FirstName.' '.$LastName;
        $insertArray['Vat'] = 0;
        
        $max_memberId = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->max('MemberId');
        if($max_memberId >= 0){
            $new_memberId = (int)$max_memberId + 1;
            $insertArray['MemberId'] = $new_memberId;
        }
        // END
        
        if(isset($insertArray['MembershipData']) || !empty($insertArray['MembershipData'])){
            $client_activities_data = [
                        'CompanyNum' => $CompanyNum
                    ];
            try {
                if(isset($insertArray['MembershipData']['StartDate']) && !empty($insertArray['MembershipData']['StartDate'])){
                    $getStartDate = $insertArray['MembershipData']['StartDate'];
                    if(checkIsAValidDate($getStartDate)){
                        $timestamp = strtotime($getStartDate);
                        $StartDate = date("Y-m-d", $timestamp);
                        $insertArray['MembershipData']['StartDate'] = $StartDate;
                    }
                    else{
                        $rejection_array['rejection_reason'] = 'Invalid date format for Start Date Expected format: YYYY-MM-DD';
                        array_push($not_valid , $rejection_array);
                        continue;
                    }

                }else{
                    $insertArray['MembershipData']['StartDate'] = '';
                }
                
                if(isset($insertArray['MembershipData']['VaildDate']) && !empty($insertArray['MembershipData']['VaildDate'])){
                    $getVaildDate = $insertArray['MembershipData']['VaildDate'];
                    if(checkIsAValidDate($getVaildDate)){
                        $timestamp = strtotime($getVaildDate);
                        $VaildDate = date("Y-m-d", $timestamp);
                        $insertArray['MembershipData']['VaildDate'] = $VaildDate;
                    }
                    else{
                        $rejection_array['rejection_reason'] = 'Invalid date format for End Date Expected format: YYYY-MM-DD';
                        array_push($not_valid , $rejection_array);
                        continue;
                    }
                }
                else{
                    $insertArray['MembershipData']['VaildDate'] = '';
                }


                $insert_to_client =  $insertArray;
                unset($insert_to_client['MembershipData']);
                $insert_to_client['status'] = 0;        
                $insert_to_client['CompanyNum'] = $CompanyNum;
                if(isset($insertArray['additional_data'])){
                    $add_data_Obj = json_encode($insertArray['additional_data'], JSON_UNESCAPED_UNICODE);
                    $insert_to_client['additional_data'] = $add_data_Obj;        
                }           
                
                // Department Check 
                $Department = [1, 2, 3];
                
                if(isset($insertArray['MembershipData']['MemberShip']) && !empty($insertArray['MembershipData']['MemberShip'])){
                    $MemberShip_id = $insertArray['MembershipData']['MemberShip'];

                    $all_item_data = DB::table('items')
                        ->where('id', '=', $MemberShip_id)
                    ->where('CompanyNum','=',$CompanyNum)
                    ->first(['id','Department']);
                    
                    if(!$all_item_data){
                        $rejection_array['rejection_reason'] = 'Membership not found';
                        array_push($not_valid , $rejection_array);
                        continue;
                    }else{
                        if($all_item_data->Department == 1){
                            if( (
                                !isset($insertArray['MembershipData']['StartDate'])
                                || empty($insertArray['MembershipData']['StartDate'])
                            ) 
                            || (
                                !isset($insertArray['MembershipData']['VaildDate'])
                                || empty($insertArray['MembershipData']['VaildDate'])
                            )
                            ){
                                $rejection_array['rejection_reason'] = 'Invalid Membership Start or End Date';
                                array_push($not_valid , $rejection_array);
                                continue;
                            }
                            else{
                                $client_activities_data['StartDate'] = $insertArray['MembershipData']['StartDate'];
                            }
                        }else{
                            
                            if(!isset($insertArray['MembershipData']['TrueBalanceValue']) || empty($insertArray['MembershipData']['TrueBalanceValue'])){
                                $rejection_array['rejection_reason'] = 'Invalid remaining visits value';
                                array_push($not_valid , $rejection_array);
                                continue;
                            }
                            if($insertArray['MembershipData']['StartDate']){
                                $client_activities_data['StartDate'] = $insertArray['MembershipData']['StartDate'];
                            }else{
                                $client_activities_data['StartDate'] = date('Y-m-d');
                            }
                        }
                        $lastId = ClientService::addClient($insert_to_client);
                        $lastId = $lastId['Message']['client_id'];
                        $insertedIds[] = $lastId;
                    }
                }else{
                    $lastId = ClientService::addClient($insert_to_client);
                    $lastId = $lastId['Message']['client_id'];
                    $insertedIds[] = $lastId;
                    continue;
                }

                //END Department Check 
                
                if($lastId){
                    
                    $client_activities_data['ClientId'] = $lastId;

                    if (!empty($MemberShip_id)) {
                        $Department = [1, 2, 3];
                        
                        $item_data = DB::table('items')
                            ->where('id', '=', $MemberShip_id)
                        ->where('CompanyNum','=',$CompanyNum)
                        ->whereIn('Department',$Department)->get();
                        if(!empty($item_data[0])){
                            $client_activities_data['ItemId'] = $item_data[0]->id;
                            $client_activities_data['ItemText'] = $item_data[0]->ItemName;
                            
                            $client_activities_data['Department'] = $item_data[0]->Department;
                            $client_activities_data['MemberShip'] = $item_data[0]->MemberShip;
                            $client_activities_data['ItemPrice'] = $item_data[0]->ItemPrice;
                            $client_activities_data['ItemPriceVat'] = $item_data[0]->ItemPriceVat;
                            $client_activities_data['Vat'] = $item_data[0]->Vat;
                            $client_activities_data['LimitClass'] = $item_data[0]->LimitClass;
                            $client_activities_data['BalanceValue'] = $item_data[0]->BalanceClass;

                            $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum)->NotificationDays ?? 0;
                            $client_activities_data['MemberShipRule'] = '{"data": [{
                                    "EndTime": "",
                                    "StartTime": "",
                                    "LimitClass": '. $item_data[0]->LimitClass .',
                                    "CancelLImit": '. $item_data[0]->CancelLImit .',
                                    "ClassSameDay": '. $item_data[0]->ClassSameDay .',
                                    "FreezMemberShip": '. $item_data[0]->FreezMemberShip .',
                                    "LimitClassMonth": '. $item_data[0]->LimitClassMonth .',
                                    "NotificationDays": '. $NotificationDays .',
                                    "LimitClassEvening": '. $item_data[0]->LimitClassEvening .',
                                    "LimitClassMorning": '. $item_data[0]->LimitClassMorning .',
                                    "FreezMemberShipDays": '. $item_data[0]->FreezMemberShipDays .',
                                    "FreezMemberShipCount": '. $item_data[0]->FreezMemberShipCount .'
                                    }
                                ]}';
                            // $client_activities_data['MemberShipRule'] = json_decode($MemberShipRule);
                            // Departmarnt Checks
                            if($item_data[0]->Department == 1){
                                if( (
                                    !isset($insertArray['MembershipData']['StartDate'])
                                    || empty($insertArray['MembershipData']['StartDate'])
                                ) 
                                || (
                                    !isset($insertArray['MembershipData']['VaildDate'])
                                    || empty($insertArray['MembershipData']['VaildDate'])
                                )
                                ){
                                    $rejection_array['rejection_reason'] = 'Invalid Membership Start or End Date';
                                    array_push($not_valid , $rejection_array);
                                    continue;
                                }
                                else{
                                    $client_activities_data['StartDate'] = $insertArray['MembershipData']['StartDate'];
                                }
                            }else{
                                
                                if(!isset($insertArray['MembershipData']['TrueBalanceValue']) || empty($insertArray['MembershipData']['TrueBalanceValue'])){
                                    $rejection_array['rejection_reason'] = 'Invalid remaining visits value';
                                    array_push($not_valid , $rejection_array);
                                    continue;
                                }
                                if($insertArray['MembershipData']['StartDate']){
                                    $client_activities_data['StartDate'] = $insertArray['MembershipData']['StartDate'];
                                }else{
                                    $client_activities_data['StartDate'] = date('Y-m-d');
                                }
                                
                            }
                        }
                    }
                    if(isset($insertArray['MembershipData']['VaildDate'])){
                        $VaildDate = $insertArray['MembershipData']['VaildDate'];
                        
                        $client_activities_data['VaildDate'] = $client_activities_data['TrueDate'] = $insertArray['MembershipData']['VaildDate'];
                        if(isset($NotificationDays)){
                            $modifiedNotificationDate = date('Y-m-d', strtotime('-'.$NotificationDays.' day', strtotime($VaildDate)));
                            $client_activities_data['NotificationDays'] = $modifiedNotificationDate;
                        }
                    }
                    
                    if(isset($insertArray['MembershipData']['TrueBalanceValue'])){
                        $client_activities_data['TrueBalanceValue'] = $insertArray['MembershipData']['TrueBalanceValue'];
                        $client_activities_data['ActBalanceValue'] = $insertArray['MembershipData']['TrueBalanceValue'];
                    }else{
                        $client_activities_data['TrueBalanceValue'] = 0;
                        $client_activities_data['ActBalanceValue'] = 0;
                    }
                   
                    try {
                        // 
                        $client_activities_data['CardNumber'] = 1;
                        $client_activities_data['UserId'] = 0;
                        $client_activities_data['ItemPriceVatDiscount'] = 0;
                        $client_activities_data['VatAmount'] = 0;
                        $client_activities_data['FirstDate'] = 0;
                        $client_activities_data['FirstDateStatus'] = 0;
                        
                        // 
                        DB::table('client_activities')->insert($client_activities_data);
                        $last_client_activity = DB::getPdo()->lastInsertId();
                        if($last_client_activity){
                            $updateClientsArray = array(
                                'ActiveMembership'    => 1,
                                'MemberShipText' => '{"data": [
                                            {
                                                "Id": "'.$last_client_activity.'",
                                                "ItemText": "' . $client_activities_data['ItemText'] . '",
                                                "TrueDate": "'. $insertArray['MembershipData']['VaildDate'] .'",
                                                "LimitClass": "'. $client_activities_data['LimitClass'] .'",
                                                "TrueBalanceValue": "'. $client_activities_data['TrueBalanceValue'] .'"
                                            }
                                        ]}'
                                    );
                            $updateClientTable = DB::table('client')->where('id', $lastId)->update($updateClientsArray);
                            
                        }
                    }catch (Exception $e) {
                        $rejection_array['rejection_reason'] = 'Invalid membership data client_activities';
                        array_push($not_valid , $rejection_array);
                    }
                }
            } catch (Exception $e) {
                $db_success = 0;
                $rejection_array['rejection_reason'] = 'Invalid data';
                array_push($not_valid , $rejection_array);
                array_push($errorsIds,  $insertArray);
            }
        }else{
            try {
                if(isset($insert_to_client['MembershipData'])){
                    unset($insert_to_client['MembershipData']);
                }
                $insert_to_client =  $insertArray;
                $insert_to_client['status'] = 0;        
                $insert_to_client['CompanyNum'] = $CompanyNum;

                $lastId = ClientService::addClient($insert_to_client);
                $lastId = $lastId['Message']['client_id'];
                $insertedIds[] = $lastId;
            } catch (Exception $e) {
                $db_success = 0;
                $rejection_array['rejection_reason'] = 'Invalid Client data';
                array_push($not_valid , $rejection_array);
                
                array_push($errorsIds, $value);
            }
        }
    }
    if($not_valid){
        exit(json_encode([
            'success' => 1,
            'message' => 'Successful',
            'insertedIds' => $insertedIds,
            'errorsIds' => $errorsIds,
            'not_valid' => $not_valid
        ]));
    }
    else{
        exit(json_encode([
            'success' => 1,
            'message' => 'Data inserted',
            'insertedIds' => $insertedIds,
            'not_valid' => $not_valid
        ]));        
    }
}else{
    exit(json_encode([
        'success' => 0,
        'message' => 'Please send correct json'
    ]));
}
