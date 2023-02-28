<?php
require_once '../../app/init.php';
if(Auth::check()) {
    if (isset($_POST['hoksAction'])) {
        $action = $_POST['hoksAction'];
        $companyNum = Auth::user()->CompanyNum;
        $LogUserId = Auth::user()->id;
        $LogUserName = Auth::user()->display_name;
        $err = false;
        $typeKevaArr = array();
        array_push($typeKevaArr, '0');
        isset($_POST['typeKeva']) ? array_push($typeKevaArr, $_POST['typeKeva']) : false;
        $message = '';
        switch ($action) {
            case 'change':
                // postpone regular payments 
                if (isset($_POST['timeType']) && isset($_POST['reScheduleAmount'])) {
                    $timeType = $_POST['timeType'] == '1' ? 'day' : 'month';
                    $amount = $_POST['reScheduleAmount'];

                    $updateDateCnt = DB::table('payment')->leftJoin('paytoken', 'payment.KevaId', '=', 'paytoken.id')
                        ->where('payment.CompanyNum', '=', $companyNum)->where('payment.Status', '=', '0')->where('payment.ActStatus','=','0')
                        ->where('payment.Date','>=', date('Y-m-d'))->where('paytoken.Status', '=', '0')->whereIn('payment.TypeKeva', $typeKevaArr)
                        ->update(array('payment.Date' => DB::raw('DATE_ADD(payment.Date, INTERVAL '.$amount.' '.$timeType.')')));
                    
                    $updatePaytoken = DB::table('paytoken')->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')->whereIn('TypeKeva', $typeKevaArr)
                        ->update(array('NextPayment' => DB::raw('DATE_ADD(NextPayment, INTERVAL '.$amount.' '.$timeType.')'))); 


                    if($_POST['timeType'] == '1') {
                        $timeType = 'ימים';
                    } else {
                        $timeType = 'חודשים';
                    }
                    if($updateDateCnt) {
                        $LogContent = "<i class='fa fa-credit-card-alt' aria-hidden='true'></i> " . $LogUserName . ' דחה את כל הוראות הקבע הפעילות ב - '.$amount. ' '.$timeType.'.';
                        $isInserted = DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => date('Y-m-d H:i:s'), 'CompanyNum' => $companyNum));
                        $message = 'הוראות הקבע הפעילות נדחו ב- '.$amount. ' '.$timeType;
                    } else {
                        $message = 'לא נמצאו הוראות קבע פעילות.';
                    }

                    

                } else {
                    $err = true;
                    $message = 'לא נקלט מספר';
                }
                break;
            case 'cancel':
                if (isset($_POST['cancelType'])) {
                    $cancelType = $_POST['cancelType'];
                    if ($cancelType == 1) {
                        // cancel between dates
                        if(isset($_POST['fromDate']) && isset($_POST['toDate'])) {
                            $fromDate = $_POST['fromDate'];
                            $toDate = $_POST['toDate'];
                            
                            $cancelBetweenDates = DB::table('payment')->leftJoin('paytoken', 'payment.KevaId', '=', 'paytoken.id')
                            ->where('payment.CompanyNum', '=', $companyNum)->where('payment.Status', '=', '0')->where('payment.ActStatus','=','0')
                            ->whereBetween('payment.Date', array($fromDate, $toDate))->where('paytoken.Status', '=', '0')->whereIn('payment.TypeKeva', $typeKevaArr)
                            ->update(array('payment.ActStatus' => 1));

                            if($cancelBetweenDates) {
                                $LogContent = "<i class='fa fa-credit-card-alt' aria-hidden='true'></i> " . $LogUserName . ' ביטל/ה את כל הוראות הקבע הפעילות בין התאריכים '.$fromDate.' - '.$toDate.'.';
                                DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => date('Y-m-d H:i:s'), 'CompanyNum' => $companyNum));
                                $message = 'הוראות הקבע הפעילות שבין התאריכים '.$fromDate. ' ל- '.$toDate.' בוטלו.';
                            } else {
                                $message = 'לא נמצאו הוראות קבע בין התאריכים '.$fromDate. ' ל- '.$toDate.'.';
                            }
                        }

                    } else if($cancelType == 2) {
                        // cancel All
                        $cancelAllCnt = DB::table('payment')->leftJoin('paytoken', 'payment.KevaId', '=', 'paytoken.id')
                        ->where('payment.CompanyNum', '=', $companyNum)->where('payment.Status', '=', '0')
                        ->where('payment.Date','>=', date('Y-m-d'))->where('paytoken.Status', '=', '0')->whereIn('payment.TypeKeva', $typeKevaArr)
                        ->update(array('payment.ActStatus' => 1, 'paytoken.Status' => 1));

                        if($cancelAllCnt) {
                            $LogContent = "<i class='fa fa-credit-card-alt' aria-hidden='true'></i> " . $LogUserName . ' ביטל/ה את כל הוראות הקבע הפעילות.';
                            DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => date('Y-m-d H:i:s'), 'CompanyNum' => $companyNum));
                            $message = 'כל הוראות הקבע הפעילות בוטלו.';
                        } else {
                            $message = 'לא נמצאו הוראות קבע פעילות.';
                        }

                    }
                }
                break;
        }
        $data = array("err" => $err, "message" => $message);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);


    } 
    
}

?>