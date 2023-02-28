<?php require_once '../../../app/initcron.php';
require_once __DIR__ . '/../../../office/Classes/Client.php';
require_once __DIR__ . '/../../../office/Classes/ItemRoles.php';
require_once __DIR__ . '/../../../office/Classes/CompanyProductSettings.php';
require_once __DIR__ . '/../../../office/Classes/LoginPushNotifications.php';
require_once __DIR__ . '/../../../office/services/ClientService.php';

            $StudioUrl = trim(@$_REQUEST['StudioUrl']);
            $FullName =  htmlentities(trim(@$_REQUEST['FullName']));
            $FirstName = trim(@$_REQUEST['FirstName']);
            $LastName = trim(@$_REQUEST['LastName']);
            $Gender = trim(@$_REQUEST['Gender']);
            $GetSMS = trim(@$_REQUEST['GetSMS']);
            $GetEmail = trim(@$_REQUEST['GetEmail']);
            $ContactMobile = isset($_REQUEST['ContactMobile']) ? trim(addslashes($_REQUEST['ContactMobile'])) : '';
			$Email = trim(@$_REQUEST['Email']);
            $MainPipeId = @$_REQUEST['MainPipeId']; // 
			$PipeId = @$_REQUEST['PipeId']; // סטטוס ליד
            $Brands = @$_REQUEST['Brands'];/// סניף
            $ClassType = @$_REQUEST['ClassType']; ///סוגי שיעורים 
            $Source = @$_REQUEST['Source']; /// מקור הגעה
            $Info = trim(@$_REQUEST['Info']); /// json - נתונים כללים
            $TrueFix='1';
            $CCode = '';
            $Status = '1';
            $mobileRegex = Client::mobileRegex;
            $ContactMobile = substr($ContactMobile, 0,1) != '+' && substr($ContactMobile, 0,3) == "972" ? '+'.$ContactMobile : $ContactMobile;

            if ($Gender==''){
            $Gender = '0';    
            }

            if ($Info==''){
            $Info = null;   
            }

            if ($ContactMobile==''){
                $TrueFix='0'; 
                $CCode = 'טלפון סלולרי שדה חובה';    
            } else if (!(preg_match($mobileRegex, $ContactMobile)) || strlen($ContactMobile) > 15) {
                $TrueFix='0'; 
                $CCode = 'פורמט מספר נייד אינו תקין';
            }

            $mobile = substr($ContactMobile, 0, 4) == '+972' ? substr($ContactMobile, 4, strlen($ContactMobile)) : $ContactMobile;

            $mobile = substr($mobile, 0, 1) == '0' ? substr($mobile, 1, strlen($mobile)) : $mobile;

            // israeli phone number with country code
            $ContactMobile = '+972'.$mobile;

            if ($PipeId==''){
            $TrueFix='0';     
            $CCode = 'סטטוס ליד שדה חובה';    
            }

            if ($StudioUrl==''){
            $TrueFix='0';     
            $CCode = 'קוד חברה שדה חובה';    
            }

            if ($StudioUrl!='' && $TrueFix=='1' && $Status!='') {

            $SettingsInfo = DB::table('settings')->where('StudioUrl', '=', $StudioUrl)->first();               
            $CompanyNum = $SettingsInfo->CompanyNum;   

            if (@$MainPipeId==''){    
            $PipeInfo = DB::table('leadstatus')->where('CompanyNum','=',$CompanyNum)->where('id','=',$PipeId)->first(); 
            $MainPipeId =  @$PipeInfo->PipeId;   
            }
                
                
            if ($FullName!='') {
                
            $FirstName = $FullName;    
                
            }    
                
            if (@$GetEmail == '') {$GetEmail = '0';}
			if (@$GetSMS == '') {$GetSMS = '0';}   
                
            if ($ClassType=='' || $ClassType=='BA999'){
            $ClassType = 'BA999'; 
            $ClassNames = 'כל השיעורים';    
            }   

            if (@$ClassType!='BA999'){    
            //// סיום בדיקת שדות חובה
            $JsonMemberType = '';
            foreach ($ClassType as $value)
            {
            $JsonMemberType .= $value . ",";
            } 								
            $JsonMemberType = substr($JsonMemberType,0,-1);      
               
               
            $z = '1';
            $myArray = explode(',', $JsonMemberType);	
            $ClassNames = '';	
            $SoftInfos = DB::table('class_type')->whereIn('id', $myArray)->where('CompanyNum', $CompanyNum)->get();
            $SoftCount = count($SoftInfos);

            foreach ($SoftInfos as $SoftInfo){

            $ClassNames .= $SoftInfo->Type; 

            if($SoftCount==$z){}else {	
            $ClassNames .= ', ';	
            }

            ++$z; 	
            }	

            $ClassNames = $ClassNames; 
            }
            else {
            $ClassNames = 'כל השיעורים'; 
            $JsonMemberType = 'BA999';    
            }   
               
               
               
			if ($FirstName==''){
			$FirstName = 'ללא';	
			}  
			
			if ($LastName==''){
			$LastName = 'ליד חדש';	
			}   
			   
			$CompanyName = $FirstName.' '.$LastName;     
			   
			
               

            $BrandName = 'סניף ראשי';
            $Brands = '0'; 
            $BrandsInfo = DB::table('brands')->where('CompanyNum','=',$CompanyNum)->orderBy('id','ASC')->first(); 
            if (@$BrandsInfo->BrandName!='') {
            $BrandName = @$BrandsInfo->BrandName;
            $Brands = @$BrandsInfo->id;    
            }
            else {
            $BrandName = 'סניף ראשי';
            $Brands = '0';    
            }  
			 

            if ($Source==''){
            $Source = '0';     
            $SourceName = 'ללא';    
            } 
            else {    
            $SourceInfo = DB::table('leadsource')->where('CompanyNum','=',$CompanyNum)->where('id','=',$Source)->first(); 
            if (@$SourceInfo->Title!='') {
            $SourceName = @$SourceInfo->Title;    
            }
            else {
            $SourceName = 'ללא';   
            }     
            }

            $mobileRegex = "^[\+972|\+91|\+1|\+44]*0?".$mobile."$";
            $isMobileExist = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereRaw("ContactMobile regexp '".$mobileRegex."'")->first();
            if (!empty($isMobileExist->id)) {

                json_message('מספר נייד קיים במערכת', false);

                exit;

            }
                
			//// בדיקת לקוח קיים במערכת
			if ($ContactMobile!='' && $Email==''){   
			$CheckClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereRaw("ContactMobile regexp '".$mobileRegex."'")->first();   
			}
			else if ($ContactMobile!='' && $Email!=''){
			$CheckClient = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereRaw("ContactMobile regexp '".$mobileRegex."'")->Orwhere('Email', '=', $Email)->where('CompanyNum', '=', $CompanyNum)->first();	
			}   
			else {
			$CheckClient = DB::table('client')->where('Email', '=', $Email)->where('CompanyNum', '=', $CompanyNum)->first();  	
			}   
			if (@$CheckClient->id!='') {
			//// בדיקת ליד קיים על נציג אחר	
				
			$CheckLead = DB::table('pipeline')->where('ClientId', '=', $CheckClient->id)->where('CompanyNum', '=', $CompanyNum)->first(); 	
			
			if (@$CheckLead->id!=''){
			/// נמצא ליד קיים	
				
			$CCode = 'ליד קיים במערכת';	
			$Status = '0';	
				
			}	
			else {	
			/// לא נמצא ליד במערכת - הוספה כליד חדש
	
			if (@$CheckClient->ContactMobile!=''){
			$ContactInfo = $CheckClient->ContactMobile;	
			}
			else {
			$ContactInfo = $CheckClient->Email;	
			}	
				 
		    $GetPipeId = DB::table('pipeline')->insertGetId(
            array('MainPipeId' => $MainPipeId, 'PipeId' => $PipeId, 'Brands' => $Brands, 'ClientId' => $CheckClient->id, 'CompanyName' => $CheckClient->CompanyName, 'ContactInfo' => $ContactInfo, 'UserId' => '0', 'ItemId' => '0', 'CompanyNum' => $CompanyNum, 'ClassInfo' => $JsonMemberType, 'ClassInfoNames' => $ClassNames, 'BrandsNames' => $BrandName, 'Source' => $SourceName, 'SourceId' => $Source, 'Info' => $Info) );   
                
            if ($GetPipeId!='0'){  
                
             $LogDateTime = date('Y-m-d H:i:s');
	         $LogContent = 'נוצר כרטיס ליד אוטומטי לכרטיס לקוח קיים';
	         DB::table('log')->insert(array('UserId' => '', 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $CheckClient->id, 'CompanyNum' => $CompanyNum));    
			
            $FixSubject = 'התקבל ליד חדש במערכת';
            $TextStudio = 'התקבל ליד חדש במערכת '.$CheckClient->CompanyName;   
            $Date = date('Y-m-d');    
            $Time = date('H:i:s');
            $Dates = date('Y-m-d H:i:s');


                /*LoginPushNotifications::sendLoginPushNotification(
                    $CompanyNum,
                    LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_horaat_keva_failed'], // CHANGE TO LEAD PERMISSION
                    $FixSubject,
                    $TextStudio
                );*/

            $AddNotification = DB::table('appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $CheckClient->id, 'Type' => '3', 'Subject' => $FixSubject, 'Text' => $TextStudio, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time) );     
             
            }
                
            //// אוטומציה
                
            $AutomationInfo = DB::table('boostapp.automation')->where('CompanyNum','=', $CompanyNum)->where('Category','=', '2')->where('Type','=', '1')->where('Status','=', '0')->first();
                
            if (@$AutomationInfo->id!=''){
            RunAutomationAddMemberShip($CheckClient->id,$AutomationInfo->Value,$CompanyNum,$AutomationInfo->VaildType);       
            }        
                
                
                
			}

            } else {
                ////  הוספת לקוח + ליד חדש למערכת

                // ביטול של קידומית ומניעת כפילות כי תתבצע כבר ב ClientService
                $ContactMobile = preg_match("/^[+972]/", $ContactMobile) ? str_replace("+972","0",$ContactMobile) : $ContactMobile;

                $AddClient = ClientService::addClient([
                    'CompanyNum' => $CompanyNum,
                    'Brands' => $Brands,
                    'Email' => $Email,
                    'ContactMobile' => $ContactMobile,
                    'FirstName' => $FirstName,
                    'LastName' => $LastName,
                    'UserId' => '0',
                    // Pipeline part
                    'MainPipeId' => $MainPipeId,
                    'PipeId' => $PipeId,
                    'ItemId' => '0',
                    'ClassInfo' => $JsonMemberType,
                    'ClassInfoNames' => $ClassNames,
                    'Source' => $SourceName,
                    'SourceId' => $Source,
                    'Info' => $Info
                ], ClientService::CLIENT_STATUS_LEAD);
                $GetPipeId = $AddClient['Message']['pipeline_id'];
                $AddClient = $AddClient['Message']['client_id'];

                if ($GetPipeId != '0') {
                    $LogDateTime = date('Y-m-d H:i:s');
                    $LogContent = 'נוצר כרטיס ליד אוטומטי';
                    DB::table('log')->insert(array(
                        'UserId' => '',
                        'Text' => $LogContent,
                        'Dates' => $LogDateTime,
                        'ClientId' => $AddClient,
                        'CompanyNum' => $CompanyNum,
                    ));

                    $FixSubject = 'התקבל ליד חדש במערכת';
                    $TextStudio = 'התקבל ליד חדש במערכת ' . $CompanyName;
                    $Date = date('Y-m-d');
                    $Time = date('H:i:s');
                    $Dates = date('Y-m-d H:i:s');

                    /*LoginPushNotifications::sendLoginPushNotification(
                        $CompanyNum,
                        LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_horaat_keva_failed'], // CHANGE TO LEAD PERMISSION
                        $FixSubject,
                        $TextStudio
                    );*/

                    $AddNotification = DB::table('appnotification')->insertGetId(array(
                        'CompanyNum' => $CompanyNum,
                        'ClientId' => $AddClient,
                        'Type' => '3',
                        'Subject' => $FixSubject,
                        'Text' => $TextStudio,
                        'Dates' => $Dates,
                        'UserId' => '0',
                        'Date' => $Date,
                        'Time' => $Time
                    ));
                }

                //// אוטומציה
                $AutomationInfo = DB::table('boostapp.automation')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('Category', '=', '2')
                    ->where('Type', '=', '1')
                    ->where('Status', '=', '0')
                    ->first();
                if (@$AutomationInfo->id != '') {
                    RunAutomationAddMemberShip($AddClient, $AutomationInfo->Value, $CompanyNum, $AutomationInfo->VaildType);
                }
            }

                
echo 'Close=1&ErrorText='.$CCode.'&Status='.$Status;                
            
}

else {
     
echo 'Close=1&ErrorText='.$CCode.'&Status=0';
    
    
}


function RunAutomationAddMemberShip($ClientId, $ValueId,$CompanyNum,$VaildType) {

    		$Vaild_TypeOption = array(
			1 => "day",
			2 => "week",
			3 => "month",
            4 => "year"    
	        );	
    
    
            $CompanyNum = $CompanyNum;
            $SettingsInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $CompanyNum)->first();
            $AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
            $BrandsMain = $SettingsInfo->BrandsMain;
            $MembershipType = @$AppSettings->MembershipType;
            $Vat = $SettingsInfo->Vat;   
			$ClientId = $ClientId;
			$Items = $ValueId;
			$StartDates = @$_POST['ClassDate'];
			$Vaild_LastCalss = @$VaildType;
		    $FirstDate = '0'; 
            $FirstDateStatus = '0';

            $ItemNamep = trim(@$_POST['ItemNamep']);
            $ItemPricep = trim(@$_POST['ItemPricep']);
            $ClassDateEnd = @$_POST['ClassDateEnd'];   
               
            if (@$Items==''){
            json_message('יש לבחור מנוי', false);
            exit;    
			}   
               
			if (@$StartDates==''){
			$Today = date('Y-m-d');  
			$StartDate = date('Y-m-d');
			}
			else {
			$Today = $StartDates;
			$StartDate = $StartDates;   
			}
               
			/// קליטת פרטי פעילות
			   
			$ItemsInfo = DB::table('boostapp.items')->where('CompanyNum','=',$CompanyNum)->where('id','=',$Items)->first();
		
              
            $ItemText = $ItemsInfo->ItemName;
            if ($ItemNamep!=''){ 
            $ItemText = $ItemNamep;
            }
               
               
			$ItemPrice = $ItemsInfo->ItemPrice;
            $ItemPriceVat = $ItemsInfo->ItemPriceVat; 
               
               
            $Department = $ItemsInfo->Department; // חוק מנוי
            $MemberShip = $ItemsInfo->MemberShip; // סוג מנוי
               
            $Vaild = $ItemsInfo->Vaild; // חישוב תוקף
            $Vaild_Type = $ItemsInfo->Vaild_Type; // סוג חישוב
            $LimitClass = $ItemsInfo->LimitClass; // הגבלת שיעורים
            $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum)->NotificationDays ?? 0; // התראה לפני סוף מנוי

            $BalanceClass = $ItemsInfo->BalanceClass; // כמות שיעורים
            $MinusCards = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum)->offsetMemberships ?? 1; // קיזוז מכרטיסיה קודמת
            $StartTime = $ItemsInfo->StartTime; // הגבלת הזמנת שיעורים
            $EndTime = $ItemsInfo->EndTime; // הגבלת הזמנת שיעורים
            $CancelLImit = $ItemsInfo->CancelLImit; // ביטול הגבלה
            $ClassSameDay = $ItemsInfo->ClassSameDay; // הזמנת שיעור באותו היום
            $FreezMemberShip = $ItemsInfo->FreezMemberShip; // ניתן להקפאה?
            $FreezMemberShipDays = $ItemsInfo->FreezMemberShipDays; // מספר ימים מקסימלי להקפאה
            $FreezMemberShipCount = $ItemsInfo->FreezMemberShipCount; // מספר פעמים שניתן להקפיא מנוי  

               
            $LimitClassMorning = $ItemsInfo->LimitClassMorning;
            $LimitClassEvening = $ItemsInfo->LimitClassEvening;
            $LimitClassMonth =  $ItemsInfo->LimitClassMonth;    
               
            $TrueBalanceClass = $BalanceClass;    
            $BalanceValueLog = NULL;     
               
               
            $MemberShipRule = '';    
            $MemberShipRule .= '{"data": [';    
            $MemberShipRule .= '{"LimitClass": "'.$LimitClass.'", "NotificationDays": "'.$NotificationDays.'", "StartTime": "'.$StartTime.'", "EndTime": "'.$EndTime.'", "CancelLImit": "'.$CancelLImit.'", "ClassSameDay": "'.$ClassSameDay.'", "FreezMemberShip": "'.$FreezMemberShip.'", "FreezMemberShipDays": "'.$FreezMemberShipDays.'", "FreezMemberShipCount": "'.$FreezMemberShipCount.'", "LimitClassMorning": "'.$LimitClassMorning.'", "LimitClassEvening": "'.$LimitClassEvening.'", "LimitClassMonth": "'.$LimitClassMonth.'"}';      
            $MemberShipRule .= ']}';     
               
 
            // מנוי תקופתי   
            if ($Department=='1') {
              
            /// חישוב תוקף מהשיעור האחרון במידה וקיים    
            if ($Vaild_LastCalss=='2'){
             /// חישוב תוקף מהמנוי האחרון במידה וקיים  
            if ($MembershipType=='0'){    
            $LastClass = DB::table('boostapp.client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)->orderBy('id', 'DESC')->first();
            }
            else {
            $LastClass = DB::table('boostapp.client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->orderBy('id', 'DESC')->first();    
            }    
                
            if (@$LastClass->TrueDate!=''){
            $StartDate = $LastClass->TrueDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }    
            else if ($Vaild_LastCalss=='3'){
             /// חישוב תוקף מהשיעור האחרון במידה וקיים 
            if ($MembershipType=='0'){     
            $LastClass = DB::table('boostapp.classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')->where('MemberShip','=',$MemberShip)
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)   
            ->orderBy('ClassDate','DESC')->first();
            }
            else {
            $LastClass = DB::table('boostapp.classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)   
            ->orderBy('ClassDate','DESC')->first();    
            }    
                
            if (@$LastClass->ClassDate!=''){
            $StartDate = $LastClass->ClassDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }   
            else if ($Vaild_LastCalss=='5') {
            $StartDate = $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            }

            $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];    
            $ItemsTime = '+'.$Vaild.' '.$Vaild_TypeOptions;    
                
            $time = strtotime($StartDate);
            $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time)); 

                            
            if ($Vaild_LastCalss=='5') {
            $StartDate =  $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            } 

            }  
            // כרטיסיה   
            else if ($Department=='2') {
               
            $ClassDate = NULL;
                
            /// חישוב תוקף    
            if ($Vaild!='0'){
            
                
            /// חישוב תוקף מהשיעור האחרון במידה וקיים    
            if ($Vaild_LastCalss=='2'){
             /// חישוב תוקף מהמנוי האחרון במידה וקיים  
            if ($MembershipType=='0'){    
            $LastClass = DB::table('boostapp.client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)->orderBy('id', 'DESC')->first();
            }
            else {
            $LastClass = DB::table('boostapp.client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->orderBy('id', 'DESC')->first();    
            }    
                
            if (@$LastClass->TrueDate!=''){
            $StartDate = $LastClass->TrueDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }    
            else if ($Vaild_LastCalss=='3'){
             /// חישוב תוקף מהשיעור האחרון במידה וקיים 
            if ($MembershipType=='0'){     
            $LastClass = DB::table('boostapp.classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')->where('MemberShip','=',$MemberShip)
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)   

            ->orderBy('ClassDate','DESC')->first();
            }
            else {
            $LastClass = DB::table('boostapp.classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)   
            ->orderBy('ClassDate','DESC')->first();    
            }    
                
            if (@$LastClass->ClassDate!=''){
            $StartDate = $LastClass->ClassDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }   
            else if ($Vaild_LastCalss=='5') {
            $StartDate = $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            }   
                
                
                
            $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];    
            $ItemsTime = '+'.$Vaild.' '.$Vaild_TypeOptions;    
                
            $time = strtotime($StartDate);
            $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));      
  
            if ($Vaild_LastCalss=='5') {
            $StartDate =  $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            }      
  
            }    

            }    
            // התנסות     
            else if ($Department=='3') {
            
             $ClassDate = NULL;
                
            /// חישוב תוקף    
            if ($Vaild!='0'){
            
                
            /// חישוב תוקף מהשיעור האחרון במידה וקיים    
            if ($Vaild_LastCalss=='2'){
             /// חישוב תוקף מהמנוי האחרון במידה וקיים  
            if ($MembershipType=='0'){    
            $LastClass = DB::table('client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)->orderBy('id', 'DESC')->first();
            }
            else {
            $LastClass = DB::table('client_activities')
            ->where('Status','=','0')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->orderBy('id', 'DESC')->first();    
            }    
                
            if (@$LastClass->TrueDate!=''){
            $StartDate = $LastClass->TrueDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }    
            else if ($Vaild_LastCalss=='3'){
             /// חישוב תוקף מהשיעור האחרון במידה וקיים 
            if ($MembershipType=='0'){     
            $LastClass = DB::table('classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')->where('MemberShip','=',$MemberShip)
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)   

            ->orderBy('ClassDate','DESC')->first();
            }
            else {
            $LastClass = DB::table('classstudio_act')
            ->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('TrueClientId','=','0')
            ->Orwhere('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('CompanyNum','=',$CompanyNum)->where('TrueClientId','=',$ClientId)   
            ->orderBy('ClassDate','DESC')->first();    
            }    
                
            if (@$LastClass->ClassDate!=''){
            $StartDate = $LastClass->ClassDate;     
            }   
            else {    
            $StartDate = $StartDate;
            }    
                
            }   
            else if ($Vaild_LastCalss=='5') {
            $StartDate = $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            }   
                
                
                
            $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];    
            $ItemsTime = '+'.$Vaild.' '.$Vaild_TypeOptions;    
                
            $time = strtotime($StartDate);
            $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));      
  
            if ($Vaild_LastCalss=='5') {
            $StartDate =  $StartDate;
            $FirstDate = '1';
            $FirstDateStatus = '1';     
            }          
                
                
            }        
                
                
                
            $MemberShipRule = NULL;
            $LimitClass = '999'; 
  
                
            } 
            // פריט כללי   
            else if ($Department=='4') {
            $ClassDate = NULL;
            $MemberShipRule = NULL;
            $LimitClass = '0'; 
            $BalanceClass = '0';    
            }        
            
            // מספור מספר המנויים שהלקוח רכש   
            $CardNum = DB::table('boostapp.client_activities')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->count();  
			$CardNumber = $CardNum+1;
               
            if ($ClassDateEnd!='' && $Department=='1' || $ClassDateEnd!='' && $Department=='2'){
            $ClassDate = $ClassDateEnd;    
            }
            /// הכנסת נתונים ועדכון טבלאות   
               
            $UserId = '0';
			$Dates = date('Y-m-d G:i:s');
            
               
            $Vaild_TypeOptions = @$Vaild_TypeOption['1'];    
            $ItemsTime = '-'.$NotificationDays.' '.$Vaild_TypeOptions;    
                
            $time = strtotime($ClassDate);
            $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));  
               
            if ($NotificationDays=='0' || $NotificationDays=='' || $Department=='4' || $Department=='3' || $Vaild_LastCalss=='5'){
            $NotificationDate = NULL;    
            } 
               
             
            if ($ItemPricep!=''){
            
            $ItemPrice = $ItemPricep;    
            $CompanyVat = $SettingsInfo->CompanyVat;  
               
            $Vat = $_POST['Vat']; 
               
            if ($CompanyVat=='0'){
            
            if ($Vat=='0'){
            
            $Vat = $SettingsInfo->Vat;    
            $Vats = '1.'.$Vat;
            $Vat = $Vat;
        
            $TotalVatItemPrice = $ItemPrice/$Vats;
            $TotalVatItemPrice = $TotalVatItemPrice;    
            $TotalVatItemPrice = round($ItemPrice-$TotalVatItemPrice,2);  

            $ItemPriceVat = round($ItemPrice-$TotalVatItemPrice,2);       
            $ItemPriceVat = $ItemPriceVat;     
            $ItemPrice = $ItemPrice; 
                
            }    
            else {
            
            $ItemPrice = $ItemPrice; 
            $ItemPriceVat = $ItemPrice;     
            $Vat = $SettingsInfo->Vat;      
            $Vat = $Vat;    
            $TotalVatItemPrice = $ItemPrice*$Vat/100;
            $TotalVatItemPrice = round($TotalVatItemPrice,2);  
            $ItemPrice = $ItemPrice+$TotalVatItemPrice;     
                
            }   
                
              
            }   
            else {
            $ItemPrice = $ItemPrice;    
            $ItemPriceVat = $ItemPrice;     
            }    
                
                
            }   
               
            $VatAmount = $ItemPrice-$ItemPriceVat;   
               
               
			$AddClientActivity = DB::table('boostapp.client_activities')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'CardNumber' => $CardNumber, 'ClientId' => $ClientId, 'Department' => $Department, 'MemberShip' => $MemberShip, 'ItemId' => $Items, 'ItemText' => $ItemText, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $ItemPriceVat,  'Vat' => $Vat, 'VatAmount' => $VatAmount, 'StartDate' => $StartDate, 'VaildDate' => $ClassDate, 'TrueDate' => $ClassDate, 'BalanceValue' => $BalanceClass, 'TrueBalanceValue' => $TrueBalanceClass, 'ActBalanceValue' => $TrueBalanceClass, 'LimitClass' => $LimitClass, 'Dates' => $Dates, 'UserId' => $UserId, 'BalanceMoney' => $ItemPrice, 'MemberShipRule' => $MemberShipRule, 'NotificationDays' => $NotificationDate, 'BalanceValueLog' => $BalanceValueLog, 'FirstDate' => $FirstDate, 'FirstDateStatus' => $FirstDateStatus) );
               
            ///// מעבר ניקובים+שיעורים ממנוי ישן לחדש
            
            $MembershipType = @$AppSettings->MembershipType;  
            $CheckItemsRoleTwo = DB::table('boostapp.items_roles')->where('CompanyNum', '=' , $CompanyNum)->where('ItemId', '=' , $Items)->first();
            $TrueClasessFinal = @$CheckItemsRoleTwo->GroupId;
               
            if ($Department=='1'){
    
            if ($MembershipType=='0'){    
            $ClientMinusKevas = DB::table('boostapp.client_activities')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)->where('Department','=','1')->where('KevaAction','=','1')->where('TrueBalanceValueStatus','=','0')->orderBy('id','DESC')->get(); 
            }
            else {
            $ClientMinusKevas = DB::table('boostapp.client_activities')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('Department','=','1')->where('KevaAction','=','1')->where('TrueBalanceValueStatus','=','0')->orderBy('id','DESC')->get();      
            }    
            foreach ($ClientMinusKevas as $ClientMinusKeva){    

            $GetClientClassKevas = DB::table('boostapp.classstudio_act')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('ClientActivitiesId','=',$ClientMinusKeva->id)->where('ClassDate','>=', $StartDate)->orderBy('id','DESC')->get();   
                
            foreach ($GetClientClassKevas as $GetClientClassKeva){

            DB::table('boostapp.classstudio_act')
		   ->where('id', $GetClientClassKeva->id)
           ->where('CompanyNum', $CompanyNum)    
           ->update(array('ClientActivitiesId' => $AddClientActivity, 'TrueClasess' => $TrueClasessFinal, 'MemberShip' => $MemberShip));      
                
            }    
                
            /// עדכון מימוש      
            DB::table('boostapp.client_activities')
		   ->where('id', $ClientMinusKeva->id)
           ->where('CompanyNum', $CompanyNum)    
           ->update(array('TrueBalanceValueStatus' => '1'));    
                
            }    
                
            }   
            else if ($Department=='2') {
              
            /// קיזוז מינוס מכרטיסיה קודמת
  
            if ($MinusCards==1){
             
            if ($MembershipType=='0'){     
            $ClientMinusCard = DB::table('boostapp.client_activities')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('MemberShip','=',$MemberShip)->where('Department','=','2')->where('TrueBalanceValue','<','0')->where('TrueBalanceValueStatus','=','0')->orderBy('id','DESC')->get();  
            }
            else {
            $ClientMinusCard = DB::table('boostapp.client_activities')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->where('Department','=','2')->where('TrueBalanceValue','<','0')->where('TrueBalanceValueStatus','=','0')->orderBy('id','DESC')->get();      
            }   
                
            foreach ($ClientMinusCard as $ClientMinusCards){
                
            $TrueBalanceClass = $BalanceClass+$ClientMinusCards->TrueBalanceValue;
            $LimitBalance = str_replace('-','',$ClientMinusCards->TrueBalanceValue);
                
            $GetClientClassKevas = DB::table('boostapp.classstudio_act')->where('CompanyNum','=',$CompanyNum)->where('ClientId','=',$ClientId)->whereIn('StatusCount', array(0,3))->where('ClientActivitiesId','=',$ClientMinusCards->id)->where('ClassDate','>=', $StartDate)->orderBy('id','DESC')->limit($LimitBalance)->get();   
                
            foreach ($GetClientClassKevas as $GetClientClassKeva){

            DB::table('boostapp.classstudio_act')
		   ->where('id', $GetClientClassKeva->id)
           ->where('CompanyNum', $CompanyNum)    
           ->update(array('ClientActivitiesId' => $AddClientActivity, 'TrueClasess' => $TrueClasessFinal, 'MemberShip' => $MemberShip));      
                
            }    
                
            /// עדכון מימוש      
            DB::table('boostapp.client_activities')
		   ->where('id', $ClientMinusCards->id)
           ->where('CompanyNum', $CompanyNum)    
           ->update(array('TrueBalanceValueStatus' => '1', 'TrueBalanceValue' => '0')); 
                
             /// עדכון מימוש      
            DB::table('boostapp.client_activities')
		   ->where('id', $AddClientActivity)
           ->where('CompanyNum', $CompanyNum)    
           ->update(array('TrueBalanceValue' => $TrueBalanceClass, 'ActBalanceValue' => $TrueBalanceClass));     
                
                
            } 

                
            }    
                
                
                
            }   
               

            //// עדכון חוב ללקוח

            $MemberShipText = '';    
            $MemberShipText .= '{"data": [';    
            $Taski = '1';
            $GetTasks = DB::table('boostapp.client_activities')
            ->where('TrueDate','>=', date('Y-m-d'))->where('Department','=', '1')->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->whereNull('TrueDate')->where('Department','=', '2')->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->where('TrueDate','>=', date('Y-m-d'))->where('Department','=', '2')->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->whereNull('TrueDate')->where('Department','=', '3')->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->where('TrueDate','>=', date('Y-m-d'))->where('Department','=', '3')->where('ClientId','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')    
            ->orderBy('CardNumber','ASC')->get();
            $TaskCount = count($GetTasks);  
                
            foreach ($GetTasks as $GetTask){   
                
            if ($Taski<$TaskCount){
            $MemberShipText .= '{"ItemText": "'.$GetTask->ItemText.'", "TrueDate": "'.$GetTask->TrueDate.'", "TrueBalanceValue": "'.$GetTask->TrueBalanceValue.'", "Id": "'.$GetTask->id.'", "LimitClass": "'.$GetTask->LimitClass.'"},';      
            }  
            else {    
            $MemberShipText .= '{"ItemText": "'.$GetTask->ItemText.'", "TrueDate": "'.$GetTask->TrueDate.'", "TrueBalanceValue": "'.$GetTask->TrueBalanceValue.'", "Id": "'.$GetTask->id.'", "LimitClass": "'.$GetTask->LimitClass.'"}';      
            }
              
            
            ++ $Taski; }
            $MemberShipText .= ']}';       
            

         //// בדיקת כרטיסית אב
               
            $CheckCleintPayment = DB::table('boostapp.client')->where('id','=',$ClientId)->where('CompanyNum', $CompanyNum)->first();   
            $BalanceAmount = '0.00';
               
               
            if (@$CheckCleintPayment->PayClientId!='0') {
            $PayClientId = $CheckCleintPayment->PayClientId; 

            $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId','=',$PayClientId)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->sum('BalanceMoney');     
            
             DB::table('boostapp.client')
             ->where('id', $ClientId)
             ->where('CompanyNum', $CompanyNum)       
             ->update(array('BalanceAmount' => '0.00'));   
                
            DB::table('boostapp.client_activities')
             ->where('ClientId', $ClientId)
             ->where('CompanyNum', $CompanyNum)       
             ->update(array('PayClientId' => $PayClientId));      
                
            }
            else {
            $PayClientId = $ClientId;  
                
            $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId','=',$ClientId)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->sum('BalanceMoney');     
            
             DB::table('boostapp.client_activities')
             ->where('ClientId', $ClientId)
             ->where('CompanyNum', $CompanyNum)       
             ->update(array('PayClientId' => '0'));      
                
            }   
 
            $CheckClientInfoer = DB::table('boostapp.client')->where('CompanyNum', $CompanyNum)->where('PayClientId', $PayClientId)->get(); 
            if (!empty($CheckClientInfoer)){     
            foreach ($CheckClientInfoer as $CheckClientInfo){
            if (@$CheckClientInfo->id != '') {     
            $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId','=',$CheckClientInfo->id)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->sum('BalanceMoney');  
            }
            }
            }
 
               
            DB::table('boostapp.client')
               ->where('id', $PayClientId)
               ->where('CompanyNum', $CompanyNum)       
               ->update(array('BalanceAmount' => $BalanceAmount, 'MemberShipText' => $MemberShipText));   
               

            //// סגירת מנוי קודם
               
               DB::table('boostapp.client_activities')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum) 
               ->where('Department','=','1')       
               ->where('Status','=','0')
               ->where('TrueDate','<=', date('Y-m-d'))       
               ->update(array('Status' => '3'));    

                
                DB::table('boostapp.client_activities')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum) 
               ->where('Department','=','2')        
               ->where('Status','=','0')
               ->where('TrueBalanceValue','<=', '0')       
               ->update(array('Status' => '3'));  
                
               DB::table('boostapp.client_activities')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum) 
               ->where('Department','=','2')       
               ->where('Status','=','0')
               ->where('TrueDate','<=', date('Y-m-d'))         
               ->update(array('Status' => '3'));  

               
            ///// סגירת מנוי היכרות/התנסות
                
                DB::table('boostapp.client_activities')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum) 
               ->where('Department','=','3')        
               ->where('Status','=','0')
               ->where('TrueBalanceValue','<=', '0')      
               ->update(array('Status' => '3'));  
                
               DB::table('boostapp.client_activities')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum) 
               ->where('Department','=','3')           
               ->where('Status','=','0')
               ->where('TrueDate','<=', date('Y-m-d'))         
               ->update(array('Status' => '3'));  
              
               
 
               
               
   
              if ($Department=='1' && $Vaild_LastCalss!='5' || $Department=='2' && $Vaild_LastCalss!='5') {
              
              $GetClasess = DB::table('boostapp.classstudio_act')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->where('ClassDate', '>=', $StartDate)->whereIn('Status', array(12, 9))->get();
               foreach ($GetClasess as $GetClases){
               
                   
                $TrueClasess = ''; 
                $TrueClasessFinal = '';


                $ClassInfo = DB::table('boostapp.classstudio_date')->where('id', '=', $GetClases->ClassId)->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->first();
                $CheckItemsRole = $ClassInfo ? ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $Items, $ClassInfo->ClassNameType) : null;

                if ($CheckItemsRole){
                    $GroupId = $CheckItemsRole->GroupId;
                    $TrueClasessFinal = $CheckItemsRole->GroupId;
                    $TrueClasess = $CheckItemsRole->Class;
                } else {
               $CheckItemsRoleTwo = DB::table('boostapp.items_roles')->where('CompanyNum', '=' , $CompanyNum)->where('ItemId', '=' , $Items)->first();
               $TrueClasessFinal = @$CheckItemsRoleTwo->GroupId;        
                }   
                   
               if ($TrueClasessFinal!=''){     
                DB::table('boostapp.classstudio_act')
               ->where('id', $GetClases->id)
               ->where('CompanyNum', $CompanyNum)   
               ->update(array('ClientActivitiesId' => $AddClientActivity, 'TrueClasess' => $TrueClasessFinal, 'MemberShip' => $MemberShip)); 
                   
                   
              //// עדכון מנוי שיבוץ קבוע   
               
            $CheckClientRegular = DB::table('boostapp.classstudio_dateregular')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->first();     
            
            if (@$CheckClientRegular->id!='') {
             
            $ClientActivitiesId = $CheckClientRegular->ClientActivitiesId; 
            $CheckClientActivites = DB::table('boostapp.client_activities')->where('CompanyNum', $CompanyNum)->where('id', $ClientActivitiesId)->first();    
                   
            if (@$CheckClientActivites->Status!='0') {
              
             DB::table('boostapp.classstudio_dateregular')
               ->where('ClientId', $ClientId)
               ->where('CompanyNum', $CompanyNum)        
               ->update(array('ClientActivitiesId' => $AddClientActivity));  
 
            }       
                   
                   
                   
                   
                   
               }
                   
                   
               }      

              }
               

                
            }   
               
               
               
            /// עדכון ספירה לסוג המנוי
            if ($Department=='1' || $Department=='2' || $Department=='3'){   
                
                
            if ($Department=='1'){
             
            $GetActivityCount = DB::table('boostapp.client_activities')->where('TrueDate','>=', date('Y-m-d'))->where('MemberShip','=', $MemberShip)->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->count();    
                
            }    
            else if ($Department=='2'){
            
            $GetActivityCount = DB::table('boostapp.client_activities')->where('TrueBalanceValue','>=', '1')->whereNull('TrueDate')->where('Department','=', '2')
            ->where('MemberShip','=', $MemberShip)->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->Orwhere('TrueBalanceValue','>=', '1')
            ->where('TrueDate','>=', date('Y-m-d'))->where('Department','=', '2')->where('MemberShip','=', $MemberShip)->where('CompanyNum','=', $CompanyNum)
            ->where('Status','=', '0')->count();    
                
            }    
            else if ($Department=='3'){
             
            $GetActivityCount = DB::table('boostapp.client_activities')->where('CompanyNum',$CompanyNum)->where('Department','3')->where('MemberShip', $MemberShip)->where('TrueBalanceValue', '>=', '1')->where('Status','=', '0')->count();     
                
            }                    
                

                
			  DB::table('boostapp.membership_type')
               ->where('id', $MemberShip)
               ->where('CompanyNum', $CompanyNum)       
               ->update(array('Count' => $GetActivityCount));
                
            }


}



?>