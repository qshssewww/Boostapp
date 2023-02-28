<?php
//require_once '../../app/initcron.php';
//require_once '../Classes/Client.php';
//require_once "../Classes/Company.php";
//require_once "../Classes/Yaad.php";
//
////todo remove using ajax/client
//
//$errorMessage = null;
//
//
//if (!isset($_POST["trueFinalInvoiceNum"])) {
//    $errorMessage = "trueFinalInvoiceNum required";
//} elseif (!isset($_POST['tempId'])) {
//    $errorMessage = "tempId required";
//} elseif (!isset($_POST['typeDoc'])) {
//    $errorMessage = "typeDoc required";
//} elseif (!isset($_POST['act'])) {
//    $errorMessage ="act required";
//} elseif (!is_numeric($_POST["act"])) {
//   $errorMessage = "act must be numeric";
//} else {
//    $statusreditCard = array(
//        0 => "עסקה מאושרת",
//        1 => "חסום החרם כרטיס",
//        2 => "גנוב החרם כרטיס",
//        3 => "התקשר לחברת האשראי",
//        4 => "סירוב",
//        5 => "מזויף החרם כרטיס",
//        6 => "ת.ז. או CVV שגויים",
//        7 => "חובה להתקשר לחברת האשראי",
//        19 => "נסה שנית, העבר כרטיס אשראי",
//        33 => "כרטיס לא תקין",
//        34 => "כרטיס לא רשאי לבצע במסוף זה או אין אישור לעסקה כזאת",
//        35 => "כרטיס לא רשאי לבצע עסקה עם סוג אשראי זה",
//        36 => "פג תוקף",
//        37 => "שגיאה בתשלומים - סכום העסקה צריך להיות שווה תשלום ראשון + תשלום קבוע כפול מספר התשלומים",
//        38 => "לא ניתן לבצע עסקה מעל התקרה לכרטיס לאשרי חיוב מיידי",
//        39 => "ספרת ביקורת לא תקינה",
//        57 => "לא הוקלד מספר תעודת זהות",
//        58 => "לא הוקלד CVV2",
//        69 => "אורך הפס המגנטי קצר מידי",
//        101 => "אין אישור מחברה אשראי לעבודה",
//        106 => "למסוף אין אישור לביצוע שאילתא לאשראי חיוב מיידי",
//        107 => "סכום העסקה גדול מידי - חלק למספר עסקאות",
//        110 => "למסוף אין אישור לכרטיס חיוב מיידי",
//        111	=> "למסוף אין אישור לעסקה בתשלומים",
//        112 => "למסוף אין אישור לעסקה טלפון/ חתימה בלבד בתשלומים",
//        113 => "למסוף אין אישור לעסקה טלפונית",
//        114 => "למסוף אין אישור לעסקה חתימה בלבד",
//        118 => "למסוף אין אישור לאשראי ישראקרדיט",
//        119 => "למסוף אין אישור לאשראי אמקס קרדיט",
//        124 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס ישראכרט",
//        125 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס אמקס",
//        127 => "למסוף אין אישור לעסקת חיוב מיידי פרט לכרטיסי חיוב מיידי",
//        129 => "למסוף אין אישור לבצע עסקת זכות מעל תקרה",
//        133 => "כרטיס לא תקף על פי רשימת כרטיסים תקפים של ישראכרט",
//        138 => "כרטיס לא רשאי לבצע עסקאות בתשלומים על פי רשימת כרטיסים תקפים של ישראכרט",
//        146 => "לכרטיס חיוב מיידי אסור לבצע עסקה זכות",
//        150 => "אשראי לא מאושר לכרטיסי חיוב מיידי",
//        151 => "אשראי לא מאושר לכרטיסי חול",
//        156 => "מספר תשלומים לעסקת קרדיט לא תקין",
//        160 => "תקרה 0 לסוג כרטיס זה בעסקה טלפונית",
//        161 => "תקרה 0 לסוג כרטיס זה בעסקת זכות",
//        162 => "תקרה 0 לסוג כרטיס זה בעסקת תשלומים",
//        163 => "כרטיס אמריקן אקספרס אשר הנופק בחול לא רשאי לבצע עסקאות תשלומים",
//        164 => "כרטיסי JCB רשאי לבצע עסקאות באשראי רגיל",
//        169 => "לא ניתן לבצע עסקת זכות עם אשראי שונה מהרגיל",
//        171 => "לא ניתן לבצע עסקה מאולצת לכרטיס/אשראי חיוב מיידי",
//        172 => "לא ניתן לבטל עסקה קודמת (עסקת זכות או מספר כרטיס אינו זהה)",
//        173 => "עסקה כפולה",
//        200 => "שגיאה יישומית",
//        251 => "נסה שנית, העבר כרטיס אשראי",
//        260 => "שגיאה כללית בחברת האשראי. נסה שנית מאוחר יותר.",
//        280 => "שגיאה כללית בחברת האשראי, נסה שנית מאוחר יותר.",
//        349 => 'אין הרשאה למסוף לאישור J5 ללא חיוב, התקשר לתמיכה.',
//        447 => 'מספר כרטיס שגוי',
//        901 => "שגיאה במסוף. התקשר לתמיכה BOOSTAPP",
//        902 => "שגיאת תקשורת. התקשר לתמיכה BOOSTAPP",
//        920 => "לא ניתן לביטול / לא נמצאה העסקה / העסקה בוטלה בעבר",
//        997 => "טוקן לא תקין, נא להצפין מחדש את כרטיס האשראי",
//        998 => "עסקה בוטלה - BOOSTAPP",
//        999 => "שגיאת תקשורת - BOOSTAPP"
//    );
//
//    $userId = Auth::user()->id;
//    $companyNum = Auth::user()->CompanyNum;
//    $settingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $companyNum)->first();
//    $dates= date('Y-m-d H:i:s');
//    $userDate= date('Y-m-d');
//    $checkRefresh = !empty($_POST["checkRefresh"]) ? $_POST["checkRefresh"] : 0;
//    $creditStatus = '0';
//    $fixTrueYaadNumber = '';
//    $tempId = $_POST['tempId'];
//    $typeDoc = $_POST['typeDoc'];
//
//    if ($checkRefresh=='2'){
//        $trueFinalInvoiceNum = $_POST['trueFinalInvoiceNum'];
//        $act = '999';
//
//    }else {
//        $act = $_POST['act'];
//        $trueFinalInvoiceNum = $_POST['trueFinalInvoiceNum'];
//
//        switch ($act) {
//            case 1:
//                $cashValue = $_POST['paymentValue'];
//                DB::table('temp_receipt_payment_client')->insertGetId(
//                    array('CompanyNum' => $companyNum,
//                        'TypeDoc' => $typeDoc,
//                        'TempId' => $tempId,
//                        'TypePayment' => '1',
//                        'Amount' => $cashValue,
//                        'CheckDate' => $userDate,
//                        'Dates' => $dates,
//                        'UserId' => $userId,
//                        'Excess' => '0',
//                        'UserDate' => $userDate));
//                break;
//            case 2:
//                if (empty($_POST["checkDate"])) {
//                    $errorMessage = "תאריך פרעון נדרש";
//                } elseif (empty($_POST["checkSnif"])) {
//                    $errorMessage = "מספר סניף נדרש";}
//                elseif (empty($_POST["checkNumber"])) {
//                    $errorMessage = "מספר צ'ק נדרש";
//                } elseif (!is_numeric($_POST["checkNumber"])) {
//                    $errorMessage = "מספר צ'ק חייב להיות מספר";
//                } elseif (empty($_POST["checkBank"])) {
//                    $errorMessage = "בנק נדרש";
//                } elseif (empty($_POST["checkAccount"])) {
//                    $errorMessage = "שם הבנק נדרש";
//                } else {
//                    $checkValue = $_POST['paymentValue'];
//                    $checkDate = $_POST['checkDate'];
//                    $checkSnif = $_POST['checkSnif'];
//                    $checkAccount = $_POST['checkAccount'];
//                    $checkBank = $_POST['checkBank'];
//                    $checkNumber = $_POST['checkNumber'];
//                    DB::table('temp_receipt_payment_client')->insertGetId(
//                        array('CompanyNum' => $companyNum,
//                            'TypeDoc' => $typeDoc,
//                            'TempId' => $tempId,
//                            'TypePayment' => '2',
//                            'Amount' => $checkValue,
//                            'CheckBank' => $checkAccount,
//                            'CheckBankSnif' => $checkSnif,
//                            'CheckBankCode' => $checkBank,
//                            'CheckNumber' => $checkNumber,
//                            'CheckDate' => $checkDate,
//                            'Dates' => $dates,
//                            'UserId' => $userId,
//                            'UserDate' => $userDate));
//                }
//                break;
//            case 3:
//                if (empty($_POST["paymentType"])) {
//                    $errorMessage = "סוג התשלום נדרש";
//                } elseif (!is_numeric($_POST["paymentType"])) {
//                    $errorMessage = "סוג התשלום חייב להיות מספר";
//                } elseif (!isset($_POST['paymentNumber'])) {
//                    $errorMessage = "מספר תשלומים נדש";
//                } elseif (!is_numeric($_POST["paymentNumber"])) {
//                    $errorMessage = "מספר תשלומים חייב להיות מספרי";
//                } elseif (!isset($_POST['clientId'])) {
//                    $errorMessage = "מספר משתמש נדרש";
//                } elseif (!isset($_POST['membershipId'])) {
//                    $errorMessage = "מספר משתמש נדרש";
//                }else {
//                    $client = new Client($_POST['clientId']);
//                    $token = isset($_POST['token']) ? $_POST['token'] : null;
//                    $method = $settingsInfo->TypeShva;
//                    $paymentNumber = isset($_POST['paymentNumber']) ? (int)$_POST['paymentNumber'] : 1;
//                    $paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : 0;
//                    $paymentNumber  = $paymentType === 1 ? 1: $paymentNumber;
//                    $paymentValue = $_POST['paymentValue'];
//                    $membershipId = $_POST['membershipId'];
//
//                    if ($paymentValue == 0) {
//                        //todo add  this case
//                    }
//
//                    $tokenModel = null;
//                    $L4Digits = $YaadCode = '';
//
//                    if (!empty($token)) {
//                        $tokenModel = DB::table('boostapp.token')->where("Token", "=", $token)->first();
//                        if (!$tokenModel) {
//                            $errorMessage = "Card is invalid." ;
//                            break;
//                        }
//                        $L4Digits = $tokenModel->L4digit;
//                        $YaadCode = $tokenModel->YaadCode;
//                    }
//
//                    switch ($method){
//                        case '0':
//                            $paymentSystem = new Yaad();
//                            break;
//                        case '1':
//                            $paymentSystem = new Yaad(); //todo change
//                            break;
//                        default:
//                            $errorMessage = "שם מערכת התשלום שנבחרה לא תואמת את האפשרויות הנתמכות";
//                            break;
//                    }
//
//                    $dataPayment= array(
//                        'client' => $client,
//                        "studioSettings" => $settingsInfo,
//                        'totalAmount' => $paymentValue,
//                        'info' => " הזמנת מנוי מס' " . $membershipId,
//
//                    );
//
//                    try {
//                        $data = $paymentSystem->makePaymentWithToken($dataPayment, $tokenModel, $paymentType, $paymentNumber);
//                        DB::table('temp_receipt_payment_client')->insertGetId(
//                            array('CompanyNum' => $companyNum,
//                                'TypeDoc' => $typeDoc,
//                                'TempId' => $tempId,
//                                'TypePayment' => '3',
//                                'Amount' => $paymentValue,
//                                'L4digit' => $data['L4digit'],
//                                'YaadCode' => $data['YaadCode'],
//                                'CCode' => $data['CCode'],
//                                'ACode' => $data['ACode'],
//                                'Bank' => $data['Bank'],
//                                'Brand' => $data['Brand'],
//                                'Issuer' => $data['Issuer'],
//                                'BrandName' => $data['BrandName'],
//                                'Dates' => $dates,
//                                'UserId' => $userId,
//                                'UserDate' => $userDate));
//                    } catch (\Throwable $e) {
//                        $errorMessage = $e->getMessage();
//                        header('HTTP/1.0 506 error');
//                        die(json_encode(["Message" => $errorMessage, "Status" => "Error"]));
//                    }
//                }
//                break;
//            case 4:
//                if (empty($_POST["bankDate"])) {
//                   $errorMessage = "תאריך העברה בנקאית נדרש";
//                } elseif (empty($_POST["bankNumber"])) {
//                   $errorMessage = "מספר הסמכתה נדרש";
//                } elseif (!is_numeric($_POST["bankNumber"])) {
//                   $errorMessage = "מספר הסמכתה חייב להיות מספר";
//                } else {
//                    $bankValue = $_POST['paymentValue'];
//                    $bankDate = $_POST['bankDate'];
//                    $bankNumber = $_POST['bankNumber'];
//                    DB::table('temp_receipt_payment_client')->insertGetId(
//                        array('CompanyNum' => $companyNum, 'TypeDoc' => $typeDoc, 'TempId' => $tempId, 'TypePayment' => '4', 'Amount' => $bankValue, 'CheckDate' => $bankDate, 'BankNumber' => $bankNumber, 'Dates' => $dates, 'UserId' => $userId, 'UserDate' => $userDate));
//
//
//
//                }
//                break;
//            // remove payment
//            case 99:
//
//        }
//    }
//
//    $typePayment = array(
//        1 => "מזומן",
//        3 => "כרטיס אשראי",
//        2 => "המחאה",
//        4 => "העברה בנקאית",
//        5 => "תו",
//        6 => "פתק החלפה",
//        7 => "שטר",
//        8 => "הוראת קבע",
//        9 => "אחר"
//    );
//
//    $tashType = array(
//        1 => "רגיל",
//        3 => "תשלומים",
//        2 => "קרדיט",
//        4 => "חיוב נדחה",
//        5 => "אחר"
//    );
//
//    if (isset($errorMessage)) {
//        //todo change status number + and add log
//        header('HTTP/1.0 506 error');
//        die(json_encode(["Message" => $errorMessage, "Status" => "Error"]));
//
//    } else {
//        $tempsPayments = DB::table('temp_receipt_payment_client')->where('TempId', '=', $tempId)
//            ->where('TypeDoc', '=', $typeDoc)
//            ->where('CompanyNum', '=', $companyNum)
//            ->orderBy('id','desc')
//            ->get(); ?>
<!---->
<!--        <div id="step-3-summary">-->
<!--        <div class="list-group list-group-flush" id="list-payment">-->
<!--            --><?php //foreach ($tempsPayments as $tempsPayment){
//                if ($tempsPayment->TypePayment == '1') {
//                    $docPaymentNotes = '';
//                } elseif ($tempsPayment->TypePayment == '2') {
//                    $docPaymentNotes = 'מספר המחאה ' . @$tempsPayment->CheckNumber . ' קוד בנק ' . @$tempsPayment->CheckBankCode . ' מספר חשבון ' . @$tempsPayment->CheckBank . ' מספר סניף ' . @$tempsPayment->CheckBankSnif;
//                } elseif ($tempsPayment->TypePayment == '3') {
//                    $docPaymentNotes = @$tempsPayment->BrandName . ' המסתיים ב-' . @$tempsPayment->L4digit . ' ב-' . @$tempsPayment->Payments . ' תשלומים ' . array_search(@$tempsPayment->tashType, $tashType) . ', מס׳ אישור: ' . @$tempsPayment->ACode;
//                } elseif ($tempsPayment->TypePayment == '4') {
//                    $docPaymentNotes = 'מספר אסמכתא ' . @$tempsPayment->BankNumber;
//                } elseif ($tempsPayment->TypePayment == '5') {
//                    $docPaymentNotes = '';
//                } elseif ($tempsPayment->TypePayment == '6') {
//                    $docPaymentNotes = '';
//                } elseif ($tempsPayment->TypePayment == '7') {
//                    $docPaymentNotes = '';
//                } elseif ($tempsPayment->TypePayment == '8') {
//                    $docPaymentNotes = '';
//                } elseif ($tempsPayment->TypePayment == '9') {
//                    $docPaymentNotes = '';
//                } else {
//                    $docPaymentNotes = 'ללא פירוט';
//                } ?>
<!--                <div class="list-group-item d-flex justify-content-between">-->
<!--                <span>-->
<!--                    <i class="fas fa-minus-circle text-danger"  onclick="ClientForm.removePaymentRow(this)"></i>-->
<!--                    --><?php //echo $typePayment[$tempsPayment->TypePayment];?>
<!--                </span>-->
<!--                    <h6>₪<span>--><?php //echo $tempsPayment->Amount+$tempsPayment->Excess;?><!--</span></h6>-->
<!--                    <input type="hidden" class="js-payment-row" payment-value="--><?php //echo $tempsPayment->Amount+$tempsPayment->Excess;?><!--" type-payment="--><?php //echo $_POST['act'];?><!--" value="--><?php //echo $typePayment[$tempsPayment->TypePayment];?><!--" id-payment="--><?php //echo $tempsPayment->id; ?><!--" />-->
<!--                </div>-->
<!--                --><?php
//            }
//            $getAmount = DB::table('temp_receipt_payment_client')->where('TempId' ,'=', $tempId)
//                ->where('TypeDoc' ,'=', $typeDoc)
//                ->where('CompanyNum' ,'=', $companyNum)
//                ->sum('Amount');
//            $getExcess = DB::table('temp_receipt_payment_client')->where('TempId' ,'=', $tempId)
//                ->where('TypeDoc' ,'=', $typeDoc)
//                ->where('CompanyNum' ,'=', $companyNum)
//                ->sum('Excess');
//            $moreAmount = $trueFinalInvoiceNum-$getAmount;
//            $creditCounts = DB::table('temp_receipt_payment_client')->where('TempId' ,'=', $tempId)
//                ->where('TypeDoc' ,'=', $typeDoc)
//                ->where('CompanyNum' ,'=', $companyNum)
//                ->where('TypePayment' ,'=', '3')
//                ->count();
//            if (@$creditCounts=='0' || @$creditCounts==''){
//                $creditCounts = '0';
//            }
//            ?>
<!--            <div class="list-group-item d-flex justify-content-between">-->
<!--                <h6><strong>--><?php //echo lang('total_revenue')?><!--</strong></h6>-->
<!--                <h6>₪<strong id="total-revenue-amount">--><?php //echo number_format((float)$getAmount+$getExcess, 2, '.', ''); ?><!--</strong></h6>-->
<!--            </div>-->
<!--            <div class="list-group-item d-flex justify-content-between">-->
<!--                <h6><strong>--><?php //echo lang('remainder_of_payment')?><!--</strong></h6>-->
<!--                <h6>₪<strong id="remainder-payment-amount">--><?php //echo number_format((float)$moreAmount, 2, '.', ''); ?><!--</strong></h6>-->
<!--            </div>-->
<!--        </div>-->
<?php //}
//}?>