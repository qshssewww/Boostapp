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

if(isset($dataArray->CompanyNum) && isset($dataArray->csvData)){
    if($dataArray->CompanyNum && $dataArray->csvData && count($dataArray->csvData)){
        $CompanyNum = $dataArray->CompanyNum;
        $csvData = $dataArray->csvData;
        
        foreach ($csvData as $key => $value) {
            $insertArray = json_decode(json_encode($value), true);
            $rejection_array = json_decode(json_encode($value), true);
            // Pre Checks
            
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
            // End Pre Checks
            if(isset($insertArray['LeadsData']) && empty($insertArray['LeadsData']) && $dataArray->fun == "leads"){
                $defaultPipeline = DB::table("pipeline_category")->where("CompanyNum", "=", $CompanyNum)->where("Status","=",0)->first();
                $insertArray["LeadsData"]['Pipeline'] = $defaultPipeline->Title;
            }
            if(isset($insertArray['LeadsData']) && !empty($insertArray['LeadsData'])){
                try {
                    $insert_to_client =  $insertArray;
                    unset($insert_to_client['LeadsData']);
                    $insert_to_client['status'] = 2;        
                    $insert_to_client['CompanyNum'] = $CompanyNum; 
                    if(isset($insertArray['additional_data'])){
                        $add_data_Obj = json_encode($insertArray['additional_data'], JSON_UNESCAPED_UNICODE);
                        $insert_to_client['additional_data'] = $add_data_Obj;        
                    }
                    // *****************pipeline_data
                    $pipeline_data = [
                        'CompanyNum' => $CompanyNum,
                    ];

                    
                    if(isset($insertArray['LeadsData']['Pipeline']) && !empty($insertArray['LeadsData']['Pipeline'])){
                        $Pipeline = $insertArray['LeadsData']['Pipeline'];
                        $MainPipeId = DB::table('pipeline_category')
                        ->where('CompanyNum','=',$CompanyNum)
                        ->where('Title','=',$Pipeline)
                        ->where('Status','=',0)
                        ->pluck('id');
                        if($MainPipeId){
                            $pipeline_data['MainPipeId']= $MainPipeId;
                        }else{
                            $rejection_array['rejection_reason'] = 'Invalid Pipeline data';
                            array_push($not_valid , $rejection_array);
                            continue;
                        }
                    }else{
                        $MainPipeId = DB::table('pipeline_category')
                        ->where('CompanyNum','=',$CompanyNum)
                        ->where('Status','=',0)
                        ->pluck('id');
                        if($MainPipeId){
                            $pipeline_data['MainPipeId']= $MainPipeId;
                        }else{
                            $MainPipeId = 0;
                        }
                    }
                    
                    if(isset($insertArray['LeadsData']['Status']) && !empty($insertArray['LeadsData']['Status'])){
                        $LeadStatus = $insertArray['LeadsData']['Status'];

                        $PipeId = DB::table('leadstatus')
                        ->where('CompanyNum','=',$CompanyNum)
                        ->where('PipeId','=',$MainPipeId)
                        ->where('Title','=',$LeadStatus)
                        ->where('Status','=',0)
                        ->pluck('id');
                        if($PipeId){
                            $pipeline_data['PipeId']= $PipeId;
                        }else{
                            $rejection_array['rejection_reason'] = 'Invalid Lead status';
                            array_push($not_valid , $rejection_array);
                            continue;
                        }

                    }else{
                        $PipeId = DB::table('leadstatus')
                        ->where('CompanyNum','=',$CompanyNum)
                        ->where('Status','=',0)
                        ->pluck('id');

                        if($PipeId){
                            $pipeline_data['PipeId']= $PipeId;
                        }
                    }
                    if(isset($insertArray['LeadsData']['Source']) && !empty($insertArray['LeadsData']['Source'])){
                        $Source = $insertArray['LeadsData']['Source'];
                        $SourceId = DB::table('leadsource')
                        ->where('CompanyNum','=',$CompanyNum)
                        ->where('Title','=',$Source)
                        ->where('Status','=',0)
                        ->pluck('id');
                        if($SourceId){
                            $pipeline_data['SourceId']= $SourceId;
                        }else{
                            $rejection_array['rejection_reason'] = 'Invalid Lead Source';
                            array_push($not_valid , $rejection_array);
                            continue;
                        }
                    }else{
                        $pipeline_data['SourceId']= 0;
                    }
                    // *****************END pipeline_data
                    $lastId = ClientService::addClient($insert_to_client, ClientService::CLIENT_STATUS_LEAD);
                    $lastId = $lastId['Message']['client_id'];
                    $insertedIds[] = $lastId;
                } catch (Exception $e) {
                    $db_success = 0;
                    $rejection_array['rejection_reason'] = 'Invalid data';
                    array_push($not_valid , $rejection_array);
                    array_push($errorsIds,  $insertArray);
                }
            }else{
                $insert_to_client =  $insertArray;
                $insert_to_client['status'] = 2;        
                $insert_to_client['CompanyNum'] = $CompanyNum; 
                try {
                    if(isset($insert_to_client['LeadsData'])){
                        unset($insert_to_client['LeadsData']);
                    }
                    $lastId = ClientService::addClient($insert_to_client, ClientService::CLIENT_STATUS_LEAD);
                    $lastId = $lastId['Message']['client_id'];
                    $insertedIds[] = $lastId;
                } catch (Exception $e) {
                    $db_success = 0;
                    $rejection_array['rejection_reason'] = 'Invalid client data';
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
                'insertedIds' => $insertedIds
            ]));        
        }
    }else{
        exit(json_encode([
            'success' => 0,
            'message' => 'Please send correct json'
        ]));
    }
}
else{
    exit(json_encode([
        'success' => 0,
        'message' => 'Please send correct json'
    ]));
}

