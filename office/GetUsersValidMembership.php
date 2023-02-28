<?php

require_once '../app/init.php';

if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {
            $postdata = file_get_contents('php://input');
            $clientId = json_decode($postdata)->clientId;
            //For postman check
            // $clientId = $_POST['clientId'];            
            if ($clientId) {
                $userMemberships = DB::table('boostapp.client_activities')->where('ClientId', '=', $clientId)->where('isPaymentForSingleClass',"=","0")->get();
                $validMemberships=[];
                foreach($userMemberships as $membership){
                        if($membership->Department==1){
                            if($membership->TrueDate){
                                if($membership->TrueDate>=date("Y-m-d")){
                                    $validMemberships[]=$membership;
                                }
                            }
                        }else{
                            if($membership->TrueBalanceValue>0){
                                if($membership->TrueDate==null || $membership->TrueDate>=date("Y-m-d") ){
                                    $validMemberships[]=$membership;
                                }
                            }
                        }      
                 }
                echo json_encode($validMemberships, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                throw new Exception('problem...');
            }
        } catch (Exception $e) {
            echo json_encode($e, JSON_UNESCAPED_UNICODE);
        }
    }
}
// SELECT * FROM boostapp.client_activities
// WHERE TrueDate >= NOW()
// AND ClientId = 785
// AND (Department = 1 OR (Department != 1 AND TrueBalanceValue > 0))


