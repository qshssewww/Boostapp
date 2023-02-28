<?php

session_start();

require_once '../../app/init.php';
require_once '../Classes/YaadUtils.php';


$yaad = new YaadUtils();

if (isset($_REQUEST['function']) && $_REQUEST["function"] == 'getIframeUrl') {

    if (!isset($_REQUEST['Name'])){
        echo json_encode(["Message" => "CardName is required", "Status" => "Error"]);
    } else {
        $_SESSION["Request"] = json_encode($_REQUEST);
        $res = json_encode(['url' => $yaad->apiSignAndGetUrl([
            "Info" => "אימות נתונים",
            "payment_sum" => 1,
            "FirstName" => $_REQUEST['Name'],
            "LastName" => '',
            "cell" => '000-0000000',
            "UserId" => '000000000'
        ], "0010158521")]);
        echo $res;
    }
} else {
    $StatusreditCard = array(
        0 => "עסקה מאושרת",
        1 => "חסום החרם כרטיס",
        2 => "גנוב החרם כרטיס",
        3 => "התקשר לחברת האשראי",
        4 => "סירוב",
        5 => "מזויף החרם כרטיס",
        6 => "ת.ז. או CVV שגויים",
        7 => "חובה להתקשר לחברת האשראי",
        19 => "נסה שנית, העבר כרטיס אשראי",	
        33 => "כרטיס לא תקין",
        34 => "כרטיס לא רשאי לבצע במסוף זה או אין אישור לעסקה כזאת",
        35 => "כרטיס לא רשאי לבצע עסקה עם סוג אשראי זה",
        36 => "פג תוקף",
        37 => "שגיאה בתשלומים - סכום העסקה צריך להיות שווה תשלום ראשון + תשלום קבוע כפול מספר התשלומים",
        38 => "לא ניתן לבצע עסקה מעל התקרה לכרטיס לאשרי חיוב מיידי",
        39 => "ספרת ביקורת לא תקינה",
        57 => "לא הוקלד מספר תעודת זהות",
        58 => "לא הוקלד CVV2",
        69 => "אורך הפס המגנטי קצר מידי",
        101 => "אין אישור מחברה אשראי לעבודה",	
        106 => "למסוף אין אישור לביצוע שאילתא לאשראי חיוב מיידי",
        107 => "סכום העסקה גדול מידי - חלק למספר עסקאות",	
        110 => "למסוף אין אישור לכרטיס חיוב מיידי",	
        111 => "למסוף אין אישור לעסקה בתשלומים",
        112 => "למסוף אין אישור לעסקה טלפון/ חתימה בלבד בתשלומים",	
        113 => "למסוף אין אישור לעסקה טלפונית",	
        114 => "למסוף אין אישור לעסקה חתימה בלבד",	
        118 => "למסוף אין אישור לאשראי ישראקרדיט",
        119 => "למסוף אין אישור לאשראי אמקס קרדיט",	
        124 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס ישראכרט",
        125 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס אמקס",	
        127 => "למסוף אין אישור לעסקת חיוב מיידי פרט לכרטיסי חיוב מיידי",
        129 => "למסוף אין אישור לבצע עסקת זכות מעל תקרה",	
        133 => "כרטיס לא תקף על פי רשימת כרטיסים תקפים של ישראכרט",	
        138 => "כרטיס לא רשאי לבצע עסקאות בתשלומים על פי רשימת כרטיסים תקפים של ישראכרט",	
        146 => "לכרטיס חיוב מיידי אסור לבצע עסקה זכות",	
        150 => "אשראי לא מאושר לכרטיסי חיוב מיידי",	
        151 => "אשראי לא מאושר לכרטיסי חול",
        156 => "מספר תשלומים לעסקת קרדיט לא תקין",	
        160 => "תקרה 0 לסוג כרטיס זה בעסקה טלפונית",	
        161 => "תקרה 0 לסוג כרטיס זה בעסקת זכות",	
        162 => "תקרה 0 לסוג כרטיס זה בעסקת תשלומים",	
        163 => "כרטיס אמריקן אקספרס אשר הנופק בחול לא רשאי לבצע עסקאות תשלומים",	
        164 => "כרטיסי JCB רשאי לבצע עסקאות באשראי רגיל",
        169 => "לא ניתן לבצע עסקת זכות עם אשראי שונה מהרגיל",
        171 => "לא ניתן לבצע עסקה מאולצת לכרטיס/אשראי חיוב מיידי",
        172 => "לא ניתן לבטל עסקה קודמת (עסקת זכות או מספר כרטיס אינו זהה)",	
        173 => "עסקה כפולה",	
        200 => "שגיאה יישומית",
        251 => "נסה שנית, העבר כרטיס אשראי",	
        260 => "שגיאה כללית בחברת האשראי. נסה שנית מאוחר יותר.",
        280 => "שגיאה כללית בחברת האשראי, נסה שנית מאוחר יותר.",
        349 => 'אין הרשאה למסוף לאישור J5 ללא חיוב, התקשר לתמיכה.', 
        447 => 'מספר כרטיס שגוי',     
        901 => "שגיאה במסוף. התקשר לתמיכה BOOSTAPP",
        902 => "שגיאת תקשורת. התקשר לתמיכה BOOSTAPP",	
        920 => "לא ניתן לביטול / לא נמצאה העסקה / העסקה בוטלה בעבר",
        998 => "עסקה בוטלה - BOOSTAPP",	
        999 => "שגיאת תקשורת - BOOSTAPP"	
    );	

    $Vaild_TypeOption = array(
        1 => "day",
        2 => "week",
        3 => "month",
        4 => "year"    
    );

    function rand_string($length) {

        $chars = "abcdefghijklmnopqrstuvwxyz123456789";
        return substr(str_shuffle($chars),0,$length);

    }

    function NewCompanyNum($rand): bool
    {
        $isExist = DB::table('247softnew.client')->where('FixCompanyNum', '=', $rand)->count();
        if($isExist > 0) {
            return false;
        }
        return true;
    }

    $request = json_decode($_SESSION["Request"]);

    $Id = $request['CreateCode'];

    $PageId = DB::table('247softnew.tempclient')->where('CreateCode','=',$Id)->first(); 
    $SettingsInfo = DB::table('247softnew.settings')->where('CompanyNum', '=', $PageId->CompanyNum)->first();
    $CompanyNum = $PageId->CompanyNum;
    $TrueType = $PageId->Type;
    $OnTimePrice = $PageId->OnTimePrice;
    $DocRemarks = $PageId->Remarks;
    $CCode = 'התגלתה שגיאה, אנא פנה לצוות התמיכה לעזרה.';
    $AgentId = $PageId->AgentId;
    $OnTimeDays = $PageId->OnTimeDays;
    $FixPrice = $PageId->FixPrice;
    $Masof = $PageId->Masof;

    if ($OnTimeDays=='0') {
        $OnTimeDays = '14';    
    }

    $FullName = htmlentities($request['FullName']);
    $CompanyName = htmlentities(trim($request['CompanyName'])); 
    $AppCompanyName = htmlentities(trim($request['AppCompanyName']));
    $BusinessType = $request['BusinessType'];
    $CompanyId = trim($request['CompanyId']);
    $City = $request['City'];
    $StreetH = htmlentities(trim($request['Street']));
    $Number = trim($request['Number']);
    $POBox = trim($request['POBox']);
    $PostCode = trim($request['PostCode']);
    $ContactName = htmlentities($request['ContactName']);
    $JobsRole = $request['JobsRole'];
    $ContactPhone = trim($request['ContactPhone']);
    $ContactMobile = trim($request['ContactMobile']);
    $Email = trim($request['Email']);
    $ContactFax = trim($request['ContactFax']);
    $Isracrd = trim(@$request['Isracart']);
    $VisaCal = trim(@$request['Cal']);
    $LeumiCard = trim(@$request['Leumi']);
    $Diners = trim(@$request['Diners']);
    $Amkas = trim(@$request['Amkas']);
    $CreditType = '0';
    $SendMeCreditCard = '0';



    $Terms = $request['Terms'];
    $FullName2 = htmlentities( $request['FullName2']);
    $CardName = htmlentities($request['CardName']);
    // $CardId = $_REQUEST['CardId'];
    // $CardNumber = $_REQUEST['CardNumber'];
    // $CardMonth = $_REQUEST['CardMonth'];
    // $CardYear = $_REQUEST['CardYear'];
    // $CardCvv = $_REQUEST['CardCvv'];

    //            if (isset($_POST['BankPayment'])) {
    //			$BankPayment = '1';
    //			} else {
    $BankPayment = '0';
    //			}

    $FullName3 = htmlentities(@$request['FullName3']);
    $SignatureJSON = @$request['signatureJSON'];
    $OnTimePaymentNum = @$request['OnTimePaymentNum'];

    $Dates = date('Y-m-d H:i:s');


    $FormAgreement = '';    
    $FormAgreement .= '{"data": [';    
    $FormAgreement .= '{"FullName": "'.$FullName.'","CompanyName": "'.$CompanyName.'","AppCompanyName": "'.$AppCompanyName.'","BusinessType": "'.$BusinessType.'","CompanyId": "'.$CompanyId.'","City": "'.$City.'","StreetH": "'.$StreetH.'","Number": "'.$Number.'","POBox": "'.$POBox.'","PostCode": "'.$PostCode.'","ContactName": "'.$ContactName.'","JobsRole": "'.$JobsRole.'","ContactPhone": "'.$ContactPhone.'","ContactMobile": "'.$ContactMobile.'","Email": "'.$Email.'","ContactFax": "'.$ContactFax.'","Isracrd": "'.$Isracrd.'","VisaCal": "'.$VisaCal.'","LeumiCard": "'.$LeumiCard.'","Diners": "'.$Diners.'","Amkas": "'.$Amkas.'","CreditType": "'.$CreditType.'","SendMeCreditCard": "'.$SendMeCreditCard.'","Terms": "'.$Terms.'","FullName2": "'.$FullName2.'","CardName": "'.$CardName.'","BankPayment": "'.$BankPayment.'","FullName3": "'.$FullName3.'","Dates": "'.$Dates.'", "OnTimePaymentNum": "'.$OnTimePaymentNum.'"}';      
    $FormAgreement .= ']}';

    $Log = $SignatureJSON.' :: '.$FormAgreement; 

    ///// בדיקת משתמש קיים במערכת
    $ClientCheckCompanyId = DB::table('247softnew.client')->where('CompanyNum', '=', $CompanyNum)->where('CompanyId', '=', $CompanyId)->first();
    $ClientCheckEmail = DB::table('247softnew.client')->where('CompanyNum', '=', $CompanyNum)->where('Email', '=', $Email)->first();

    //            if (@$ClientCheckCompanyId->id!=''){
    //            $CCode = 'עוסק מורשה קיים במערכת, נא לפנות לתמיכה';
    //            echo 'Close=0&ErrorText='.$CCode.'&Status=0';         
    //            }

    //            if (@$ClientCheckEmail->id!=''){
    //            $CCode = 'דואר אלקטרוני קיים במערכת, נא לפנות לתמיכה';
    //            echo 'Close=0&ErrorText='.$CCode.'&Status=0';        
    //            }



    $AppPassword = rand_string(6);
    $StudioUrl = uniqid();

    $pos = strpos(@$ContactName, ' ');
    $FirstName = substr(@$ContactName, 0, @$pos);
    $LastName = substr(@$ContactName, @$pos+1);
    $ClientCheckEmailid = '0';
    //            if (@$ClientCheckEmail->id=='' && @$ClientCheckCompanyId->id=='') {
    if (@$ClientCheckEmailid == '0') {

        ///// חיוב עסקה לאישור

        $host = 'https://icom.yaad.net/p/'; // gateway host 

        $formdata['action'] = 'soft';
        $formdata['Masof'] = $SettingsInfo->YaadNumber;
        $formdata['PassP'] = '1234';
        $formdata['Info'] = htmlentities($SettingsInfo->CompanyName);
        $formdata['UTF8'] = 'True';
        $formdata['UTF8out'] = 'True';
        $formdata['MoreData'] = 'True';
        $formdata['Amount'] = '1';
        $formdata['J5'] = 'J2';

        $formdata['CC'] = $CardNumber;
        $formdata['Tmonth'] = $CardMonth;
        $formdata['Tyear'] = $CardYear;
        $formdata['cvv'] = $CardCvv;

        $formdata['CC2'] = '';

        $formdata['UserId'] = '000000000';

        $formdata['ClientName'] = htmlentities(@$CardName);
        $formdata['ClientLName'] = '';
        $formdata['cell'] = '000-0000000';

        $poststring = '';

        //formatting the request string 
        foreach ($formdata AS $key => $val) {
            $poststring .= $key . "=" . $val . "&";
        }

        // strip off trailing ampersand 
        $poststring = substr($poststring, 0, -1);

        // init curl connection 
        // $CR = curl_init();
        // curl_setopt($CR, CURLOPT_URL, $host);
        // curl_setopt($CR, CURLOPT_POST, true);
        // curl_setopt($CR, CURLOPT_FAILONERROR, 1);
        // curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
        // curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($CR, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($CR, CURLOPT_AUTOREFERER, TRUE);
        // curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($CR, CURLINFO_HEADER_OUT, true);

        // actual curl execution perfom 
        // $result = curl_exec($CR);
        // $error = curl_error($CR);

        // $header = curl_getinfo($CR, CURLINFO_HEADER_OUT);

        // $TextResults = urldecode($result);
        // $UrlSoft = 'https://wwww.247soft.co.il/?' . $result;
        // $parts = parse_url($UrlSoft);
        // parse_str($parts['query'], $query);

        $L4digit = isset($_REQUEST['L4digit']) ? $_REQUEST['L4digit'] : '';
        $YaadCode = isset($_REQUEST['Id']) ? $_REQUEST['Id'] : '';
        $CCode = isset($_REQUEST['CCode']) ? $_REQUEST['CCode'] : '';
        $ACode = isset($_REQUEST['ACode']) ? $_REQUEST['ACode'] : '';

        $omerTest = true;
        ////// יצירת טוקן ללקוח   

        if ($CCode == '0' || $CCode == '700' || $CCode == '600' || $omerTest) {

            $host = 'https://icom.yaad.net/cgi-bin/yaadpay/yaadpay.pl'; // gateway host 

            $formdata['action'] = 'getToken';
            $formdata['Masof'] = $SettingsInfo->YaadNumber;

            $formdata['TransId'] = $YaadCode;

            $poststring = '';

            //formatting the request string 
            foreach ($formdata AS $key => $val) {
                $poststring .= $key . "=" . $val . "&";
            }

            // strip off trailing ampersand 
            $poststring = substr($poststring, 0, -1);

            // init curl connection 
            $CR = curl_init();
            curl_setopt($CR, CURLOPT_URL, $host);
            curl_setopt($CR, CURLOPT_POST, true);
            curl_setopt($CR, CURLOPT_FAILONERROR, 1);
            curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
            curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($CR, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($CR, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($CR, CURLINFO_HEADER_OUT, true);

            // actual curl execution perfom 
            $result = curl_exec($CR);
            $error = curl_error($CR);

            $header = curl_getinfo($CR, CURLINFO_HEADER_OUT);

            $TextResults = urldecode($result);
            $UrlSoft = 'https://wwww.247soft.co.il/?' . $result;

            $parts = parse_url($UrlSoft);
            parse_str($parts['query'], $query);

            $YaadCode = isset($query['Id']) ? $query['Id'] : '';
            $Token = isset($query['Token']) ? $query['Token'] : '';
            $Tokef = isset($query['Tokef']) ? $query['Tokef'] : '';

            $time = date('Y-m-d H:i:s');

            // $string = @$CardCvv;
            // if ($string != '') {
            //     $search = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
            //     $replace = array('m-', 's-', 'q-', 'a-', 'o-', 'v-', 'r-', 'x-', 'p-', 't-');
            //     $Cvv = str_replace($search, $replace, $string);
            // } else {
            //     $Cvv = '';
            // }

            if (@$CreditType == '') {
                $CreditType = '0';
            }

            if (@$City == '') {
                $City = '5000';
            }

            $FixCompanyNum = mt_rand(100000, 999999);
            while (!NewCompanyNum($FixCompanyNum)) {
                $FixCompanyNum = mt_rand(100000, 999999);
            }

            $AddClient = DB::table('247softnew.client')->insertGetId(
                array('CompanyNum' => '100', 'FixCompanyNum' => $FixCompanyNum, 'CompanyName' => $CompanyName, 'BusinessType' => $BusinessType, 'CompanyId' => $CompanyId, 'Email' => $Email, 'Dates' => $Dates, 'Status' => '2', 'ContactMobile' => $ContactMobile, 'ContactName' => $ContactName, 'City' => $City, 'StreetH' => @$StreetH, 'Number' => @$Number, 'PostCode' => @$PostCode, 'POBox' => @$POBox, 'ContactPhone' => $ContactPhone, 'ContactFax' => $ContactFax, 'StudioUrl' => $StudioUrl, 'AppName' => $AppCompanyName, 'CreditType' => $CreditType, 'LeumiCard' => @$LeumiCard, 'Isracrd' => @$Isracrd, 'VisaCal' => @$VisaCal, 'Amkas' => @$Amkas, 'Diners' => @$Diners, 'Memotag' => @$TrueType, 'SignatureJSON' => @$SignatureJSON, 'FormAgreement' => @$FormAgreement, 'PaymentOnTime' => '0', 'OnTimePrice' => @$OnTimePrice, 'OnTimePaymentNum' => @$OnTimePaymentNum, 'DocRemarks' => @$DocRemarks, 'AgentId' => $AgentId, 'OnTimeDays' => $OnTimeDays, 'Masof' => @$Masof, 'FixPrice' => @$FixPrice)
            );


            $InsertToken = DB::table('247softnew.token')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'ClientId' => $AddClient, 'Token' => $Token, 'Tokef' => $Tokef, 'YaadCode' => $YaadCode, 'Dates' => $Dates, 'UserId' => '0', 'sme' => $Cvv, 'L4digit' => $L4digit)
            );


            $PriceLists = DB::table('247softnew.pricelist')->where('Type', '=', $TrueType)->where('Status', '=', '0')->where('Act', '=', '0')->orderBy('id', 'ASC')->get();
            foreach ($PriceLists as $PriceList) {

                if ($TrueType == '4') {
                    $NewAmounts = $FixPrice;
                } else {
                    $NewAmounts = $PriceList->Amount;
                }

                $InsertClientPrice = DB::table('247softnew.cleint_pricelist')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'ClientId' => $AddClient, 'Text' => $PriceList->Text, 'NumClient' => $PriceList->NumClient, 'Amount' => $NewAmounts, 'StartDate' => $PriceList->StartDate, 'Type' => $PriceList->Type)
                );

            }


            //// הקמת חיובים

            $FixNextPayment = date("Y-m", strtotime('+14 day', strtotime(date('Y-m-d'))));
            $CheckNextPayment = date("d", strtotime('+14 day', strtotime(date('Y-m-d'))));

            $NextPayment = date("Y-m-d", strtotime('+60 day', strtotime(date('Y-m-d'))));


            $PayTokenId = DB::table('247softnew.paytoken')->insertGetId(
            array('CompanyNum' => '100', 'ClientId' => $AddClient, 'TokenId' => $InsertToken, 'NumDate' => '1', 'TypePayment' => '3', 'Amount' => '0', 'NumPayment' => '999', 'NextPayment' => $NextPayment, 'CountPayment' => '999', 'tashType' => '0', 'Tash' => '1', 'Text' => 'מסלול לפי חבילה', 'ItemId' => '2', 'PageId' => '0', 'UserId' => '0'));

            if ($OnTimePrice > '0') {

                //            $OneNextPayment = date("Y-m-d", strtotime('+1 day', strtotime(date('Y-m-d')))); 
                $OneNextPayment = date("Y-m-d");

                $PayTokenId = DB::table('247softnew.paytoken')->insertGetId(
                    array('CompanyNum' => '100', 'ClientId' => $AddClient, 'TokenId' => $InsertToken, 'NumDate' => '1', 'TypePayment' => '3', 'Amount' => $OnTimePrice, 'NumPayment' => '1', 'NextPayment' => $OneNextPayment, 'CountPayment' => '1', 'tashType' => '0', 'Tash' => $OnTimePaymentNum, 'Text' => 'הקמה חד פעמית', 'ItemId' => '4', 'PageId' => '0', 'UserId' => '0')
                );

            }

            //// סיום הקמת חיובים    

            $StatusNew = '1';
            $StatusPay = 'הכרטיס נשמר בהצלחה';

            ///////////  שליחת פרטי התחברות ראשוניים

            $Date = date('Y-m-d');
            $Time = date('H:i:s');
            $Dates = date('Y-m-d H:i:s');

            $Template = DB::table('247softnew.notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '1')->first();

            $ClientInfo = DB::table('247softnew.client')->where('id', '=', $AddClient)->where('CompanyNum', '=', $CompanyNum)->first();
            $CompanyInfo = DB::table('247softnew.settings')->where('CompanyNum', '=', $CompanyNum)->first();
            $AgentInfo = DB::table('247softnew.users')->where('id', '=', $AgentId)->first();

            $AgentName = htmlentities(@$AgentInfo->display_name);

            /// עדכון תבנית הודעה

            $Content1 = str_replace("[[שם חברה]]", @$CompanyName, $Template->Content);
            $ContentTrue = $Content1;


            $Text = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
            $Subject = $Template->Subject . ' - ' . @$CompanyName;

            $AddNotification = DB::table('247softnew.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $AddClient, 'Type' => '2', 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'CheckCopy' => '1'));

            if (@$AgentInfo->display_name != '') {
                $TextStudio = 'בוצעה הרשמה חדשה באמצעות טופס הקמת לקוח אונליין - נציג מכירות ' . $AgentName;
            } else {
                $TextStudio = 'בוצעה הרשמה חדשה באמצעות טופס הקמת לקוח אונליין';
            }


            $AddNotification = DB::table('247softnew.appnotification')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'ClientId' => $AddClient, 'Type' => '3', 'Subject' => 'לקוח חדש הצטרף למערכת', 'Text' => $TextStudio, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'CheckCopy' => '1')
            );

            $CCode = 'הפרטים נקלטו בהצלחה';


            echo 'Close=0&ErrorText=' . $CCode . '&Status=1';

            /// עדכון סיום הצטרפות
            DB::table('247softnew.tempclient')
                ->where('id', $PageId->id)
                ->update(array('Status' => '1', 'Log' => $Log));

        } else {

            $StatusNew = '0';
            $StatusPay = @$StatusreditCard[$CCode];
            echo 'Close=0&ErrorText=' . $StatusPay . '&Status=0';

        }
    } else {
        $CCode = 'התגלתה שגיאה בטופס, אנא נסה שנית.';
        echo 'Close=0&ErrorText=' . $CCode . '&Status=0';
    }
}
