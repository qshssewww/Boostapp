<?php
header('Content-type: application/json');

require_once '../../app/init.php';
require_once '../../office/services/ClientService.php';

$db_success = 1;
$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));
$insertedIds = [];
$errorsIds = [];
$not_valid = [];


if(isset($dataArray->CompanyNum) && isset($dataArray->csvData) && !empty($dataArray->csvData)){
    $CompanyNum = $dataArray->CompanyNum;
    $csvData = $dataArray->csvData;
    foreach ($csvData as $key => $insertArray) {
        $insertArray = json_decode(json_encode($insertArray), true);
        $rejection_array = json_decode(json_encode($insertArray), true);
        
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
        
        try {
            $max_memberId = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->max('MemberId');
            if($max_memberId >= 0){
                $new_memberId = (int)$max_memberId + 1;
                $insertArray['MemberId'] = $new_memberId;
            }

            $insertArray['status'] = 1;
            $insertArray['CompanyNum'] = $CompanyNum;
            $insertArray['CompanyNum'] = $CompanyNum;

            $lastId = ClientService::addClient($insertArray, ClientService::CLIENT_STATUS_ARCHIVE);
            $lastId = $lastId['Message']['client_id'];
            $insertedIds[] = $lastId;
        } catch (Exception $e) {
            $db_success = 0;
            $rejection_array['rejection_reason'] = 'Validation Error';            
            array_push($not_valid, $rejection_array);
        }
    }

    if($not_valid){
        exit(json_encode([
            'success' => 1,
            'message' => 'Successful',
            'insertedIds' => $insertedIds,
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
