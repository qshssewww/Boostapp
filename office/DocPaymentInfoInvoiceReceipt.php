
<?php

require_once '../app/initcron.php'; 

			$StatusreditCard = array(
			0 => "בוטל בהצלחה",
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
			111	=> "למסוף אין אישור לעסקה בתשלומים",
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
			902 => "שגיאת תקשורת. התקשר לתמיכה 246SOFT",	
			920 => "לא ניתן לביטול / לא נמצאה העסקה / העסקה בוטלה בעבר",
            997 => "טוקן לא תקין, נא להצפין מחדש את כרטיס האשראי",    
			998 => "עסקה בוטלה - 247SOFT",	
			999 => "שגיאת תקשורת - 247SOFT"	

	        );	


$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$Dates= date('Y-m-d H:i:s');
$UserDate= date('Y-m-d');
$FixTrueYaadNumber = '';
$CheckRefresh = @$_REQUEST['CheckRefresh'];
 
$TempId = $_REQUEST['TempId'];
$TypeDoc = $_REQUEST['TypeDoc'];

$TempInfo = DB::table('temp')->where('CompanyNum' ,'=', $CompanyNum)->where('TypeDoc' ,'=', $TypeDoc)->where('id' ,'=', $TempId)->first();
$Finalinvoicenum = $TempInfo->Amount;
$TrueFinalinvoicenum = $TempInfo->Amount;  

if (@$CheckRefresh=='2'){
$Act = '999'; 
} else {
$Act = $_REQUEST['Act'];
$ClientId = $_REQUEST['ClientId']; 

$GetAmountNow = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->sum('Amount');
$GetAmountExcessNow = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->sum('Excess');

if ($Act=='1'){
    
$CashValue = $_REQUEST['CashValue'];

$TotalCash = $CashValue;     
$Excess = '0';   

DB::table('temp_receipt_payment')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '1', 'Amount' => $TotalCash, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'Excess' => $Excess, 'UserDate' => $UserDate));

}

else if ($Act=='2'){
  
$CheckValue = $_REQUEST['CheckValue'];
$CheckDate = $_REQUEST['CheckDate'];
$CheckSnif = $_REQUEST['CheckSnif'];
$CheckAccount = $_REQUEST['CheckAccount'];
$CheckBank = $_REQUEST['CheckBank'];
$CheckNumber = $_REQUEST['CheckNumber'];    
    
DB::table('temp_receipt_payment')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '2', 'Amount' => $CheckValue, 'CheckBank' => $CheckAccount, 'CheckBankSnif' => $CheckSnif, 'CheckBankCode' => $CheckBank, 'CheckNumber' => $CheckNumber, 'CheckDate' => $CheckDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate));    
    
}

else if ($Act=='3'){

$ClientInfo = DB::table('client')->where('id','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->first();	     
    
if ($ClientInfo->CompanyId!=''){    
$ClinetId = htmlentities($ClientInfo->CompanyId); 
}
else {
$ClinetId = '000000000';  
} 
if ($ClientInfo->CompanyId!=''){      
$ContactMobile = htmlentities($ClientInfo->ContactMobile);     
}
else {
$ContactMobile = '000-0000000';    
}    
   
$CreditType = 'עסקה מגנטית';      
// הקלדה ידנית    
if ($_REQUEST['Credit']=='2'){
$Tokens = DB::table('token')->where('id','=', $_REQUEST['CC3'])->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->first();	 
    
$Token =  @$Tokens->Token;   
$CardTokef = @$Tokens->Tokef;
$CardCvv = @$Tokens->sme;
$FixTrueYaadNumber = @$Tokens->YaadNumber;    
    
$CreditType = 'עסקת טוקן';    
}   
else if ($_REQUEST['Credit']=='3'){       
$ClinetId = $_REQUEST['CCId'];
$CreditType = 'עסקה טלפונית';     
}
else if ($_REQUEST['Credit']=='4'){          
$CDate = $_REQUEST['CDate'];
$OutSidecode = $_REQUEST['CCode']; 
$CreditType = 'עסקה ממסוף אחר';     
} 
    
    
if ($_REQUEST['tashType']=='0' || $_REQUEST['tashType']=='1'){
$tashType = '1';     
}    
else {
$tashType = $_REQUEST['tashType'];   
}  
    
if ($_REQUEST['tashType']=='0'){
$tashTypeDB = '1';     
}    
else if ($_REQUEST['tashType']=='1') {
$tashTypeDB = '2'; 
}   
else if ($_REQUEST['tashType']=='2') {
$tashTypeDB = '4'; 
}       
else if ($_REQUEST['tashType']=='6') {
$tashTypeDB = '3'; 
} 
else {
$tashTypeDB = '5';     
}    
    
   
            $CheckClient = DB::table('client')->where('id','=',$ClientId)->where('CompanyNum', $CompanyNum)->first();    
           
    
if ($_REQUEST['Credit']=='4'){
            $UserDate = $CDate;  
            $Issuer = $_REQUEST['TypeBank'];
            $Bank = $_REQUEST['TypeBank'];    
            $Brand = $_REQUEST['TypeBrand']; 
            $L4digit = $_REQUEST['CC'];
            $Payments = $_REQUEST['Tash'];
            $tashType = $_REQUEST['tashType'];
            $YaadCode =  '0';    
            $ACode =  $OutSidecode;   
            $tashType = $tashType;
            $Payments = $_REQUEST['Tash'];
            $CCode='0';      
			
            $CardType = array(
            0 => "PL",
            1 => "מסטרקארד",    
            2 => "ויזה",
            3 => "Maestro",
            5 => "ישראכרט",
            66 => "דיינרס",
            77 => "אמריקן אקספרס",
            88 => "מסטרקארד",    
            );    
                
                
            if ($Issuer=='1'){
            $BrandName = 'כרטיס ישראכרט מסוג '.@$CardType[$Brand];   
            }   
            else if ($Issuer=='2'){
            $BrandName = 'כרטיס כאל מסוג '.@$CardType[$Brand];    
            }       
            else if ($Issuer=='3'){
            $BrandName = 'כרטיס מסוג דיינרס';    
            }  
            else if ($Issuer=='4'){
            $BrandName = 'כרטיס מסוג אמריקן אקספרס';    
            } 
            else if ($Issuer=='5'){
            $BrandName = 'כרטיס JCB מסוג '.@$CardType[$Brand];    
            } 
            else if ($Issuer=='6'){
            $BrandName = 'כרטיס לאומי קארד מסוג '.@$CardType[$Brand];    
            }  
            else {
            $BrandName = '';    
            } 
                
                
            if ($_REQUEST['Credit']=='4'){
            $UserDate = $CDate; 
            $BrandName = 'כרטיס חויב במסוף אחר';     
            }    
                
                
DB::table('temp_receipt_payment')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '3', 'Amount' => $_REQUEST['CreditValue'], 'L4digit' => $L4digit, 'YaadCode' => $YaadCode, 'CCode' => $CCode, 'ACode' => $ACode, 'Bank' => $Bank, 'Payments' => $Payments, 'Brand' => $Brand, 'BrandName' => $BrandName, 'Issuer' => $Issuer, 'tashType' => $tashTypeDB, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate, 'CreditType' => $CreditType));     
  
$CreditStatus = '1';    

?>      
  
            <script> 
			BN('0','החיוב עבר בהצלחה');
            </script> 
<?php     
    
}
    
    
    
    
    
    
             //// בדיקת מסוף לסניף שונה  
            $YaadNumber = $SettingsInfo->YaadNumber;
            $TrueYaadNumber = $SettingsInfo->YaadNumber;
               
            if ($CheckClient->Brands!='0'){
              
            $BrandCheckYaadNumber = DB::table('brands')->where('id','=',$CheckClient->Brands)->where('CompanyNum', $CompanyNum)->first();     
             
            if (@$BrandCheckYaadNumber->YaadNumber!='0'){
            $YaadNumber = $BrandCheckYaadNumber->YaadNumber;
            $TrueYaadNumber = $BrandCheckYaadNumber->YaadNumber;    
            }    
                
            }    
    
            if ($FixTrueYaadNumber!=''){
            $TrueYaadNumber = $FixTrueYaadNumber;    
            }
    
            $TokenTrueYaad = '0'; 

            //// הגדרת תשלום UPAY
            if ($CompanyNum=='299589'){

            if (@$_REQUEST['tashType']=='1' && $_REQUEST['Tash']>='2' || @$_REQUEST['tashType']=='6' && $_REQUEST['Tash']>='2'){       
            $YaadNumber = '2600075725';
            $TokenTrueYaad = '1';    
            }

            } 
    
if ($_REQUEST['Credit']!='4'){
    
			 $host = 'https://icom.yaad.net/cgi-bin/yaadpay/yaadpay.pl'; // gateway host 
   
    
			 $formdata['action'] = 'soft';
			 $formdata['Masof'] = $YaadNumber;
			 $formdata['PassP'] = 'beepos.co.il';	
             $formdata['Info'] =  htmlentities($SettingsInfo->CompanyName);
             $formdata['UTF8'] = 'True';
             $formdata['UTF8out'] = 'True';
             $formdata['MoreData'] = 'True';

             if ($_REQUEST['Credit']=='44'){
             $formdata['J5'] = 'J2';     
             $formdata['Amount'] = '1'; 
             }
             else {
             $formdata['Amount'] = $_REQUEST['CreditValue']; 
             $formdata['tashType']= $tashType;
             $formdata['Tash']= $_REQUEST['Tash'];     
             }   
    
             $formdata['Fild1'] = $TempId;
    
             if ($_REQUEST['Credit']!='2'){
			 $formdata['CC']= @$_REQUEST['CC'];
             $formdata['CC2']= @$_REQUEST['CC2'];
             $formdata['Tmonth']= @$_REQUEST['Tmonth'];
             $formdata['Tyear']= @$_REQUEST['Tyear'];
             $formdata['cvv']= @$_REQUEST['Cvv'];
             }
             else {
             $formdata['Token'] = 'True';
                             
             if ($TokenTrueYaad=='1'){
             $formdata['tOwner'] = $TrueYaadNumber;    
             }      
                  
             $formdata['CC']= $Token;
             $CardTokef = $CardTokef;
             $Month =  mb_substr($CardTokef, 2);
             $Year = '20'.mb_substr($CardTokef, 0, 2);
             $formdata['Tmonth']= $Month; 
             $formdata['Tyear']= $Year;
             $string = $CardCvv;
             if (@$string!=''){ 
             $search  = array('m-', 's-', 'q-', 'a-', 'o-', 'v-', 'r-', 'x-', 'p-', 't-');
             $replace = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
             $formdata['cvv'] =  str_replace($search, $replace, $string);
             }
             else {
             $formdata['cvv'] =  '';    
             }                
             }

    
             $formdata['UserId'] = $ClinetId;
    
             $formdata['ClientName']  = htmlentities($ClientInfo->FirstName);
             $formdata['ClientLName'] = htmlentities($ClientInfo->LastName);
             $formdata['cell'] = $ContactMobile;
             $formdata['email'] = htmlentities(@$ClientInfo->Email);	

			 $poststring = ''; 

			 //formatting the request string 
			 foreach($formdata AS $key => $val) 
			 { 
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
			 $result = curl_exec( $CR ); 
			 $error = curl_error( $CR ); 

			 $header = curl_getinfo($CR, CURLINFO_HEADER_OUT);

            $TextResults = urldecode($result);
			$UrlSoft = 'https://wwww.247soft.co.il/?'.$result;

             DB::table('log_yaad_return')->insertGetId(
             array('UserId' => $UserId, 'Text' => $TextResults, 'ClientId' => $ClinetId, 'CompanyNum' => $CompanyNum, 'Status' => '0')); 
    
			$parts = parse_url($UrlSoft);
			parse_str($parts['query'], $query);    
    
            $L4digit =  @$query['L4digit'];
            $CCode =  @$query['CCode'];
            $Bank =  @$query['Bank'];
            $Brand =  @$query['Brand'];
            $Issuer = @$query['Issuer'];
    
            if ($_REQUEST['Credit']=='4'){
            $YaadCode =  '0';    
            $ACode =  $OutSidecode;   
            $tashType = $tashType;
            $Payments = $_REQUEST['Tash'];
            }
            else {
            $YaadCode =  @$query['Id'];    
            $ACode =  @$query['ACode'];    
            $tashType = @$query['tashType'];
            $Payments = @$query['Payments'];    
            }
    
            $CompanyId = @$query['UserId'];
    
       	    if ($_REQUEST['Credit']=='4'){
            $UserDate = $CDate;   
            }    
    
			if ($CCode=='0' || $CCode=='700' || $CCode=='600'){
			
            $CardType = array(
            0 => "PL",
            1 => "מסטרקארד",    
            2 => "ויזה",
            3 => "Maestro",
            5 => "ישראכרט",
            66 => "דיינרס",
            77 => "אמריקן אקספרס",
            88 => "מסטרקארד",    
            );    
                
                
            if ($Issuer=='1'){
            $BrandName = 'כרטיס ישראכרט מסוג '.@$CardType[$Brand];   
            }   
            else if ($Issuer=='2'){
            $BrandName = 'כרטיס כאל מסוג '.@$CardType[$Brand];    
            }       
            else if ($Issuer=='3'){
            $BrandName = 'כרטיס מסוג דיינרס';    
            }  
            else if ($Issuer=='4'){
            $BrandName = 'כרטיס מסוג אמריקן אקספרס';    
            } 
            else if ($Issuer=='5'){
            $BrandName = 'כרטיס JCB מסוג '.@$CardType[$Brand];    
            } 
            else if ($Issuer=='6'){
            $BrandName = 'כרטיס לאומי קארד מסוג '.@$CardType[$Brand];    
            }  
            else {
            $BrandName = '';    
            } 
                
                
            if ($_REQUEST['Credit']=='4'){
            $UserDate = $CDate; 
            $BrandName = 'כרטיס חויב במסוף אחר';     
            }    
                
                
DB::table('temp_receipt_payment')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '3', 'Amount' => $_REQUEST['CreditValue'], 'L4digit' => $L4digit, 'YaadCode' => $YaadCode, 'CCode' => $CCode, 'ACode' => $ACode, 'Bank' => $Bank, 'Payments' => $Payments, 'Brand' => $Brand, 'BrandName' => $BrandName, 'Issuer' => $Issuer, 'tashType' => $tashTypeDB, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate, 'CreditType' => $CreditType));   

 DB::table('log_yaad')->insertGetId(
array('UserId' => $UserId, 'Text' => @$TextResults, 'ClientId' => $ClientId, 'CompanyNum' => $CompanyNum, 'Status' => $CCode));     

?>   
            <script> 
			BN('0','החיוב עבר בהצלחה');
            $("#ReceiptBtn").trigger("click");    
            </script>    
            <?php
			}	
			else {

DB::table('log_yaad')->insertGetId(
array('UserId' => $UserId, 'Text' => @$TextResults, 'ClientId' => $ClientId, 'CompanyNum' => $CompanyNum, 'Status' => $CCode));     
                
			$StatusPay = @$StatusreditCard[$CCode];
            ?>   
            <script>   
			BN('1','<?php echo $StatusPay; ?>'); 
            </script>     
            <?php     
			if ($StatusPay==''){	
			$StatusPay = 'מסיבה שאינה ידועה. אנא התקשר לחברת האשראי.';
            ?>
            <script>       
            BN('1','<?php echo $StatusPay; ?>');  
            </script>    
			<?php }		
				
				
			}
    
}
    
    
}

else if ($Act=='4'){
  
$BankValue = $_REQUEST['BankValue'];
$BankDate = $_REQUEST['BankDate'];
$BankNumber = $_REQUEST['BankNumber'];    
    
DB::table('temp_receipt_payment')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '4', 'Amount' => $BankValue, 'CheckDate' => $BankDate, 'BankNumber' => $BankNumber, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate));    
    
}


}





		$TypePayment = array(    
        1 => "מזומן",
		3 => "כרטיס אשראי",
		2 => "המחאה",
		4 => "העברה בנקאית",
		5 => "תו",
		6 => "פתק החלפה",
        7 => "שטר",
        8 => "הוראת קבע",
        9 => "אחר"    
	    );

		$TashType = array(
        1 => "רגיל",
		3 => "תשלומים",
		2 => "קרדיט",
		4 => "חיוב נדחה",
		5 => "אחר"
		);

?>



<table class="table" dir="rtl">

<thead>
    
<tr>
<th style="width: 5%; text-align: right;">#</th>
<th style="width: 10%; text-align: right;">סוג תשלום</th>
<th style="width: 50%; text-align: right;">פירוט</th> 
<th style="width: 15%; text-align: right;">תאריך פרעון</th>    
<th style="width: 10%; text-align: right;">סה"כ</th>
<th style="width: 10%; text-align: right;">פעולות</th>
</tr>
   
</thead>

<tbody>
<?php 

$i = '1';	
$TempsPayments = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->get();

foreach($TempsPayments as $TempsPayment){ 
	
if ($TempsPayment->TypePayment == '1') {$DocPaymentNotes = '';}
elseif ($TempsPayment->TypePayment == '2') {$DocPaymentNotes = 'מספר המחאה '.@$TempsPayment->CheckNumber.' קוד בנק '.@$TempsPayment->CheckBankCode.' מספר חשבון '.@$TempsPayment->CheckBank.' מספר סניף '.@$TempsPayment->CheckBankSnif;}
elseif ($TempsPayment->TypePayment == '3') {$DocPaymentNotes = @$TempsPayment->BrandName.' המסתיים ב-'.@$TempsPayment->L4digit.' ב-'.@$TempsPayment->Payments.' תשלומים '.array_search(@$TempsPayment->tashType, $TashType).', מס׳ אישור: '.@$TempsPayment->ACode;}
elseif ($TempsPayment->TypePayment == '4') {$DocPaymentNotes = 'מספר אסמכתא '.@$TempsPayment->BankNumber;}
elseif ($TempsPayment->TypePayment == '5') {$DocPaymentNotes = '';}
elseif ($TempsPayment->TypePayment == '6') {$DocPaymentNotes = '';}
elseif ($TempsPayment->TypePayment == '7') {$DocPaymentNotes = '';}
elseif ($TempsPayment->TypePayment == '8') {$DocPaymentNotes = '';}
elseif ($TempsPayment->TypePayment == '9') {$DocPaymentNotes = '';}
else {$DocPaymentNotes = 'ללא פירוט';}    
    
    
?>	
	<tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $TypePayment[$TempsPayment->TypePayment]; ?></td>
        <td><?php echo $DocPaymentNotes ; ?></td>
        <td><?php echo with(new DateTime($TempsPayment->CheckDate))->format('d/m/Y'); ?></td>
		<td><?php echo $TempsPayment->Amount+$TempsPayment->Excess; ?> ₪</td>
		<td> <button class="btn btn-outline-danger btn-sm CancelPayments" type="button" name="CancelPayments" data-tempid="<?php echo $TempsPayment->TempId; ?>" data-templistid="<?php echo $TempsPayment->id; ?>">בטל חיוב זה</button></td>
	</tr>
	
<?php ++$i; } ?>	
	
	
</tbody>	
	
	
</table>

<?php 
$GetAmount = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('TypeDoc' ,'=', $TypeDoc)->where('CompanyNum' ,'=', $CompanyNum)->sum('Amount');
$GetExcess = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('TypeDoc' ,'=', $TypeDoc)->where('CompanyNum' ,'=', $CompanyNum)->sum('Excess');
$MoreAmount = $Finalinvoicenum-$GetAmount;
$TrueMoreAmount = $MoreAmount;

$CreditCounts = DB::table('temp_receipt_payment')->where('TempId' ,'=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->where('TypePayment' ,'=', '3')->count();

if (@$CreditCounts=='0' || @$CreditCounts==''){
$CreditCounts = '0';    
}


?>
<div id="info" class="alertb alert-dark" dir="rtl">
 סה"כ לתשלום <span style='font-weight:bold; color:black; padding-right:5px;'><span id='TotalFinal'><?php echo number_format((float)$Finalinvoicenum, 2, '.', ''); ?></span> ₪</span>
</div>

<div id="info" class="alertb alert-dark" dir="rtl">
סכום שהתקבל <span style='font-weight:bold; color:black; padding-right:5px;'><span id='TotalFinalX'><?php echo number_format((float)$GetAmount+$GetExcess, 2, '.', ''); ?></span> ₪</span>
</div>
<?php if ($MoreAmount=='0'){} else { ?>
<div id="infoAmountMore" class="alertb alert-warning" dir="rtl">
יתרה לתשלום <span style='font-weight:bold; color:red; padding-right:5px;' dir="ltr">₪ <span id='TotalFinalX2'><?php echo number_format((float)$MoreAmount, 2, '.', ''); ?></span></span>
</div>
<?php } ?>

<?php if (@$GetExcess=='0' || @$GetExcess==''){} else { ?>
<div id="infoAmountMore" class="alertb alert-info" dir="rtl">
עודף ללקוח <span style='font-weight:bold; color:red; padding-right:5px;' dir="ltr">₪ <span id='TotalFinalX2'>-<?php echo number_format((float)@$GetExcess, 2, '.', ''); ?></span></span>
</div>
<?php } ?>

<script>
  
 <?php if ($CreditCounts>='1'){ ?>    
$("#ReceiptBtn").trigger("click");       
<?php } ?>    
    
$('#TrueFinalinvoicenum').val('<?php echo number_format((float)$TrueMoreAmount, 2, '.', ''); ?>');  
     
$('#CashValue').val(''); 
$('#CheckValue').val('');
$('#BankValue').val(''); 
$('#BankDate').val(''); 
$('#BankNumber').val('');     
$('#CreditValueButton').attr("disabled", true);    
    
<?php if ($Act=='1'){ ?>  
    
$('#CheckDate').val('');
$('#CheckNumber').val(''); 
$('#CheckSnif').val(''); 
$('#CheckAccount').val(''); 
$('#CheckBank').val('');     
    
<?php } else if ($Act=='2'){ 
$CheckDate = date("Y-m-d", strtotime("+1 month", strtotime($CheckDate)));
$CheckNumber = $CheckNumber+1;     
?>    
    
$('#CheckDate').val('<?php echo $CheckDate; ?>');
$('#CheckNumber').val('<?php echo $CheckNumber; ?>');  
    
<?php } ?>    
  
 $('.CancelPayments').click(function(){
            $("#meTest").trigger("click");
            var CancelPayments_TempsListsId = $(this).data("tempid");
            var CancelPayments_TempsListsId_new = $(this).data("templistid");
            $('#CancelPayments_TempsId').val(CancelPayments_TempsListsId);
            $('#CancelPayments_TempsListsId').val(CancelPayments_TempsListsId_new);
            $('#CancelPayments_Finalinvoicenum').val('<?php echo $Finalinvoicenum; ?>');
            $('#CancelPayments_TrueFinalinvoicenum').val('<?php echo $TrueMoreAmount; ?>');
            
     
     
});  
    
  
<?php if ($GetAmount+$GetExcess>'0'){ ?>    
$('#CancelDocButton').attr("disabled", false); 
$('.CloseCheckBoxPayment').attr("disabled", true);    
$('#CancelDocs_TempsId').val('<?php echo $TempId; ?>');    
<?php } else if ($GetAmount+$GetExcess=='0' || $GetAmount+$GetExcess=='0.00') { ?>
$('.CloseCheckBoxPayment').attr("disabled", false);
<?php } ?>        
    
    
$(function() {
			var time = function(){return'?'+new Date().getTime()};
            $('#CancelPaymentsPopup').imgPicker({
   
			});    
    
    
	
});       
    

    
</script>
