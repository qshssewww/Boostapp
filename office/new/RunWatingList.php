<?php
require_once '../../app/initcron.php';
require_once '../Classes/ClassStudioAct.php';
require_once '../Classes/ItemRoles.php';
require_once '../Classes/Notificationcontent.php';

$ClassId = $_POST['ClassId'];
$CompanyNum = Auth::user()->CompanyNum;

$classLogId = [];

/// הגדרות אפליקציה    
$AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
$SendNotification = $AppSettings->SendNotification;
$KevaDays = $AppSettings->KevaDays;
$FreeWatingList = $AppSettings->FreeWatingList; 
$FreeWatinglistOrderTime = $AppSettings->FreeWatinglistOrderTime;    
    
$MorningTime = $AppSettings->MorningTime; // שיעורי בוקר  
$EveningTime = $AppSettings->EveningTime; // שיעורי ערב 

$WatingListNight = $AppSettings->WatingListNight;
$WatingListStartTime = $AppSettings->WatingListStartTime;
$WatingListEndTime = $AppSettings->WatingListEndTime;

if ($MorningTime==''){
$MorningTime = '12:00:00';    
}  
    
if ($EveningTime==''){
$EveningTime = '16:00:00';    
}      
    
$CheckDeviceId = '0';    
$MemberShipLimit = $AppSettings->MemberShipLimit;
$DaysMemberShipLimit = $AppSettings->DaysMemberShipLimit;    
$MemberShipLimitMoney = $AppSettings->MemberShipLimitMoney; 


            //////////////////////////  בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////
    
           $Watinglist = $AppSettings->Watinglist; // בדיקת שיבוץ אוטומטי
           $WatinglistEndMin = $AppSettings->WatinglistEndMin; // זמן ביטחון לפני תחילת שיעור
           $WatinglistMin = $AppSettings->WatinglistMin; // זמן המתנה
           $WatingListAct = '0';
           $ChooseClass = '0'; 
    
    
           //// בדיקת מצב שיעור
           $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=', $ClassId)->first();

            $WeekNumber = date("Wo", strtotime("+1 day",strtotime($ClassInfo->StartDate))); 

    
           $MaxClient = $ClassInfo->MaxClient;
           $CountMaxClient = DB::table('boostapp.classstudio_act')->where('ClassId', '=' , $ClassId)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '0')->count();
    
           if ($CountMaxClient>=$MaxClient || ($ClassInfo->StartDate == date('Y-m-d') && $ClassInfo->StartTime <= date('H:i:s')) || $ClassInfo->StartDate<date('Y-m-d')) {}
           else { 
    
           $GetWatingLists = DB::table('boostapp.classstudio_act')->where('CompanyNum' ,'=', $CompanyNum)->where('ClassId' ,'=', $ClassId)->where('Status' ,'=', '9')->orderBy('WatingListSort', 'ASC')->orderBy('id', 'ASC')->get();
           if (!empty($GetWatingLists)){  
           foreach ($GetWatingLists as $GetWatingList) { 
               
               
           $WatingListAct = '0';
           $WatingListActTrue = '0';
           $StatusFreeWatingList = '0';       
           $ChooseClass = '0';
           $ClinetIdWatingList = $GetWatingList->ClientId;
           $TrueClientId = $GetWatingList->TrueClientId;       
           $ActivityIdWatingList = $GetWatingList->ClientActivitiesId;
           $NewCheckDeviceId = $GetWatingList->DeviceId;    
           $WatingListActDevice = '1';
		   $Day = '0';	
		   $Week = '0';
		   $Month = '0';
		   $Morning = '0';
		   $Evening = '0';		   
               
           if (@$CheckDeviceId!='0'){       
           $NewDeviceInfo = DB::table('boostapp.numberssub')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $NewCheckDeviceId)->first();
           $DeviceInfoUnique = DB::table('boostapp.numbers')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $NewDeviceInfo->NumbersId)->first();       
           $NewDeviceTitle = @$NewDeviceInfo->Name;     
                    
           if (@$OldDeviceTitle!=$NewDeviceTitle && $DeviceInfoUnique->Unique=='1') { /// מידה לא תואמת
           $WatingListActDevice = '0';    
           }    
           }       
           
           $ActivityInfo = DB::table('boostapp.client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ActivityIdWatingList)->first();     
           $LimitMultiActivity = @$ActivityInfo->LimitMultiActivity;  
            
            if ($ActivityInfo->KevaAction=='1' && $ActivityInfo->TrueDate!=''){
            $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;    
            $MemberShipInfoTrueDate = date('Y-m-d', strtotime($MemberShipInfoTrueDate. ' + '.$KevaDays.' days'));    
            }
            else {
            $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;    
            }        
               
           if ($ActivityInfo->FirstDateStatus=='1'){
           $MemberShipInfoTrueDate = $ActivityInfo->TrueDate;
           }    
               
           if ($ActivityInfo->Department=='1' && $MemberShipInfoTrueDate>date('Y-m-d')){ /// שבץ לקוח
           $WatingListActTrue = '1';
           $WatingListAct = '1';       
           }
           
           if ($ActivityInfo->Department=='2' && $ActivityInfo->TrueBalanceValue>='1' || $ActivityInfo->Department=='3' && $ActivityInfo->TrueBalanceValue>='1'){ /// שבץ לקוח
           $WatingListActTrue = '1';
           $WatingListAct = '1';       
           } 

           if ($ActivityInfo->Department=='2' && $ActivityInfo->TrueDate!='' && $MemberShipInfoTrueDate<date('Y-m-d') || $ActivityInfo->Department=='3' && $ActivityInfo->TrueDate!='' && $MemberShipInfoTrueDate<date('Y-m-d') ){ /// לא משבץ לקוח 
           $WatingListActTrue = '0';
           $WatingListAct = '0';       
           }        
               
           
           $MemberShip = $ActivityInfo->MemberShip;       
               
          //// בדיקת מגבלות רשימת המתנה חופשית    
               
            if ($FreeWatingList=='1') {

            $LimitType = '0';
            $MemberInfo = DB::table('boostapp.items')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ActivityInfo->ItemId)->first();    
            $LimitType = $MemberInfo->LimitType;
             $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType);
            if (!empty($CheckItemsRoles)){      
            foreach ($CheckItemsRoles as $CheckItemsRole){
            $FoundMatch = '1';  
            $Class = $CheckItemsRole->Class;
            $TrueClasessFinal = $CheckItemsRole->GroupId;    
            $TrueClasess = $CheckItemsRole->Class;    
            $Group = $CheckItemsRole->Group;
            $Item = $CheckItemsRole->Item;    
            $Value = $CheckItemsRole->Value;
            $GroupId = $CheckItemsRole->GroupId;          

            switch ($Group) {
                
            case 'Max': /// מגבלת מקסימום

            if ($Item=='Day'){  
                
            ////// בדיקת שיבוץ שיעור באותו היום פלוס שעה פלוס בדיקת זמן בטחון   
                    
            if ($ClassInfo->StartDate==date('Y-m-d')){  
   
            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=',  $ClassInfo->StartDate)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }      
             
            if ($CountLimitClassDay>=$Value) {
            $StatusFreeWatingList = '1';
            $Day = '1';
            } 
            else {
	        $StatusFreeWatingList = '0';	
            }     
 
            }     
            else {    

            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate', '=',  $ClassInfo->StartDate)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassDay = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate', '=', $ClassInfo->StartDate)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }      
             
            if ($CountLimitClassDay>=$Value) {
            $StatusFreeWatingList = '1';
			$Day = '1';	
            }  
            else {
            $StatusFreeWatingList = '0';	
            }    
                
            }
            }
            else if ($Item=='Week'){
            
            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassWeek = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('WeekNumber', '=', $WeekNumber)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassWeek = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }    

            /// בדיקת מגבלה שבועית 
            if ($CountLimitClassWeek>=$Value){
            $StatusFreeWatingList = '1'; 
			$Week = '1';	
            } 
            else {
            $StatusFreeWatingList = '0'; 	
            }      
                
                
                
            } 
            else if ($Item=='Month'){
         
            if ($LimitType=='0' || $ActivityInfo->TrueDate==''){
            $ClassDateStart = date('Y-m-01', strtotime($ClassInfo->StartDate)); 
            $ClassDateEnd = date('Y-m-t', strtotime($ClassInfo->StartDate));   
            }
            else { //// תחילת חישוב תוקף מנוי לבדיקת מגבלה חודשית  
            $LimitType_StartDate = $ActivityInfo->StartDate;
            $LimitType_EndDate = $MemberShipInfoTrueDate;
            $LimitType_TodayDate = date('Y-m', strtotime($ClassInfo->StartDate));
            $LimitType_ThisMonth = date('m', strtotime($ClassInfo->StartDate));
            $LimitType_ThisYear = date('Y', strtotime($ClassInfo->StartDate));
            $LimitType_ThisDate = $ClassInfo->StartDate;
            $LimitType_StartMonth = date('m', strtotime($LimitType_StartDate));
            $LimitType_EndMonth = date('m', strtotime($LimitType_EndDate));
            $LimitType_ThisDateStart = date('d', strtotime($LimitType_StartDate));
            $LimitType_ThisDateEnd = date('d', strtotime($LimitType_EndDate));
            $LimitType_ThisYearStart = date('Y', strtotime($LimitType_StartDate));
            $LimitType_ThisYearEnd = date('Y', strtotime($LimitType_EndDate));    


            if ($LimitType_ThisMonth==$LimitType_StartMonth){  
            $ClassDateStart = $LimitType_StartDate; 
            $Limit_CheckDate = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))).'-'.$LimitType_ThisDateStart; 
            if ($Limit_CheckDate>=$LimitType_EndDate){
            $ClassDateEnd = $LimitType_EndDate;     
            }   
            else {    
            $ClassDateEnd = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))).'-'.$LimitType_ThisDateStart; 
            }
            }
            else if ($LimitType_ThisMonth>$LimitType_StartMonth && $LimitType_ThisMonth<$LimitType_EndMonth){

            $ThisDateStartFix = date('d', strtotime('+1 day', strtotime($LimitType_StartDate)));       
            $ClassDateStart = $LimitType_ThisYear.'-'.$LimitType_ThisMonth.'-'.$ThisDateStartFix; 
            if ($ClassDateStart>$LimitType_ThisDate){
            $ClassDateStart = $LimitType_StartDate;   
            }         
            $Limit_CheckDate = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))).'-'.$LimitType_ThisDateStart;    
            if ($Limit_CheckDate>=$LimitType_EndDate){
            $ClassDateEnd = $LimitType_EndDate;     
            }
            else {    
            $ClassDateEnd = date('Y-m', strtotime('+1 month', strtotime($LimitType_TodayDate))).'-'.$LimitType_ThisDateStart;  
            }
            }
            else if ($LimitType_ThisMonth==$LimitType_EndMonth){  
            $ThisDateStartFix = date('d', strtotime('+1 day', strtotime($LimitType_StartDate)));     
            $ClassDateStart = $LimitType_ThisYear.'-'.$LimitType_ThisMonth.'-'.$ThisDateStartFix;
            if ($ClassDateStart>$LimitType_ThisDate){
            $ClassDateStart = $LimitType_StartDate;   
            }         
            $ClassDateEnd = $LimitType_ThisYearEnd.'-'.$LimitType_EndMonth.'-'.$LimitType_ThisDateEnd;    
            }
            else {
            $ClassDateStart = $LimitType_StartDate;
            $ClassDateEnd = $LimitType_EndDate;       
            }    

            } //// סיום חישוב תוקף מנוי לבדיקת מגבלה חודשית   
       

            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassMonth = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassMonth = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }    

            /// בדיקת מגבלה שבועית 
            if ($CountLimitClassMonth>=$Value){
            $StatusFreeWatingList = '1';
			$Month = '1';	
            }
            else {
            $StatusFreeWatingList = '0';
            }     
                
                
            }  
            else if ($Item=='Year'){
        
                
            } 
            else if ($Item=='Morning'){
             
            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassMorning = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '<=', $MorningTime)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassMorning = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '<=', $MorningTime)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }    

            /// בדיקת מגבלה שבועית 
            if ($CountLimitClassMorning>=$Value){
            $StatusFreeWatingList = '1'; 
			$Morning = '1';	
            }      
            else {
            $StatusFreeWatingList = '0'; 
            }      
                
                
            }  
            else if ($Item=='Evening'){ 
                
            if ($TrueClientId=='0' || $LimitMultiActivity=='1'){    
            $CountLimitClassEvening = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '>=', $EveningTime)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();        
            }
            else {
            $CountLimitClassEvening = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('WeekNumber', '=', $WeekNumber)->where('ClassStartTime', '>=', $EveningTime)->whereIn('Status', array(1,2,4,6,8,10,11,12,15,16,21))->where('WatingStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();    
            }    

            /// בדיקת מגבלה שבועית 
            if ($CountLimitClassEvening>=$Value){
            $StatusFreeWatingList = '1'; 
			$Evening = '1';	
            } 
            else {
	        $StatusFreeWatingList = '0'; 
            }      
                
            }                  
                      
            break 1;        
                   
                
            }/// סיום סוויטש  
            } /// סיום לולאה
            } /// לולאה ריקה
            } ////// סיום בדיקת מגבלות רשימת המתנה חופשית     
            
           $TrueWatingLimit = $Day.','.$Week.','.$Month.','.$Morning.','.$Evening;
			if($Day == '1' || $Week == '1' || $Month == '1' || $Morning == '1' || $Evening == '1') {
                $StatusFreeWatingList = '1';
            }   
           //// בדיקת מגבלה יומית
            $CheckItemsRoles = ItemRoles::getAllByItemIdAndClassType($CompanyNum, $ActivityInfo->ItemId, $ClassInfo->ClassNameType, ItemRoles::GROUP_VALUE_MAX, 'Day');
           if (!empty($CheckItemsRoles)){
           foreach ($CheckItemsRoles as $CheckItemsRole){
                  
           $ClassValue = $CheckItemsRole->Value;
           $TrueClasess = $CheckItemsRole->Class;
           $GroupId = $CheckItemsRole->GroupId; 

           $WatinglistOrder = $AppSettings->WatinglistOrder; /// אפשר להרשם להמתנה פלוס שיעור לאותו היום       
         
            //// בדיקה אם הלקוח כבר משובץ לשיעור אחר לאותו יום השיעור
            if ($TrueClientId=='0' || $ClinetIdWatingList==$ActivityInfo->ClientId){    
            /// בדיקת הזמנה כפולה לאותו יום השיעור 
            $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate','=',$ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
            $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('ClientId', '=', $ClinetIdWatingList)->where('TrueClientId', '=', '0')->where('ClassDate','=',$ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();     
            }
            else {
            $CheckClientRegister = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate','=',$ClassInfo->StartDate)->where('StatusCount', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();
            $CountLimitClassWatingToday = DB::table('boostapp.classstudio_act')->where('TrueClientId', '=', $TrueClientId)->where('ClassDate','=',$ClassInfo->StartDate)->where('StatusCount', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('MemberShip', '=', $MemberShip)->where('TrueClasess', $GroupId)->count();     
            }     
               
               
            $Total_CheckClientRegister = $CheckClientRegister+$CountLimitClassWatingToday;   
               
            if ($WatinglistOrder=='1' && $CheckClientRegister >= $ClassValue){ /// לבחור שיעור
            $WatingListAct = '1';
            $ChooseClass = '1';       
            }         

           }
           }
           else {
           $WatinglistOrder = $AppSettings->WatinglistOrder; /// אפשר להרשם להמתנה פלוס שיעור לאותו היום            
           $ClassValue = '999'; 
           $WatingListAct = '1';
           $ChooseClass = '0';         
           }       
            
               
           //// שיבוץ לקוח מתאים מרשימת המתנה     
               
           $ThisTime = date('H:i:s');
		   $ThisTimeDate = date('Y-m-d H:i:s');	   
		   $ClassFixDate = $ClassInfo->StartDate.' '.$ClassInfo->StartTime;	    
           $ClassFixDateNew = date("Y-m-d H:i:s", strtotime('-'.$WatinglistEndMin.' minutes', strtotime($ClassFixDate))); 
		   $ClassTime = date("Y-m-d", strtotime('-'.$WatinglistEndMin.' minutes', strtotime($ClassInfo->StartTime))); 	   
            

           if ($WatingListAct=='1' && $Watinglist=='1' && $ChooseClass=='0' && $WatingListActDevice=='1' && $StatusFreeWatingList!='1' && $WatingListActTrue=='1' && $ThisTimeDate<$ClassFixDateNew || $WatingListAct=='1' && $Watinglist=='1' && $ChooseClass=='0' && $ThisTimeDate<$ClassFixDateNew && $WatingListActDevice=='1' && $StatusFreeWatingList!='1' && $WatingListActTrue=='1' ){ /// שיבוץ אוטומטי
           
            //// עדכון סטטוס   
            $NewStatus = '15'; // מומש מרשימת המתנה
             
            /// בדיקת סטטוס הלקוח
            $CheckOldStatus = DB::table('boostapp.class_status')->where('id', '=', $GetWatingList->Status)->first();
            $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();    
            
            $StatusCount = $CheckNewStatus->StatusCount;     
               
            // תיעוד שינוי סטטוס
 
            $Dates = date('Y-m-d G:i:s');   
            $UserId = Auth::user()->id;

            $StatusJson = '';    
            $StatusJson .= '{"data": [';  

            if ($GetWatingList->StatusJson!=''){                  
            $Loops =  json_decode($GetWatingList->StatusJson,true);	
            foreach($Loops['data'] as $key=>$val){ 

            $DatesDB = $val['Dates'];
            $UserIdDB = $val['UserId'];
            $StatusDB = $val['Status']; 
            $StatusTitleDB = $val['StatusTitle']; 
            $UserNameDB = $val['UserName'];     

            $StatusJson .= '{"Dates": "'.$DatesDB.'", "UserId": "'.$UserIdDB.'", "Status": "'.$StatusDB.'", "StatusTitle": "'.$StatusTitleDB.'", "UserName": "'.$UserNameDB.'"},';    

            }  
            }

            $StatusJson .= '{"Dates": "'.$Dates.'", "UserId": "", "Status": "'.$NewStatus.'", "StatusTitle": "'.$CheckNewStatus->Title.'", "UserName": ""}';

            $StatusJson .= ']}';

            (new ClassStudioAct($GetWatingList->id))->update([
                'Status' => $NewStatus,
                'StatusJson' => $StatusJson,
                'StatusCount' => $StatusCount,
                'DeviceId' => $CheckDeviceId,
            ]);
  
            $TrueBalanceValue = $ActivityInfo->TrueBalanceValue;
    
            if ($ActivityInfo->Department=='2' || $ActivityInfo->Department=='3'){ 
                
            ////  ניקוב כרטיסיה    
             
            if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='0'){
            $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
            }   
            else if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='1'){
            $FinalTrueBalanceValue = $TrueBalanceValue+1; // מחזיר ניקוב
            } 
            else if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='2'){
            $FinalTrueBalanceValue = $TrueBalanceValue+1; // מחזיר ניקוב
            }  
            else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='0'){
            $FinalTrueBalanceValue = $TrueBalanceValue-1; // מחסיר ניקוב
            }  
            else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='1'){
            $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
            }   
            else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='2'){
            $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
            } 
            else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='0'){
            $FinalTrueBalanceValue = $TrueBalanceValue-1; // מחסיר ניקוב
            }  
            else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='1'){
            $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
            }   
            else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='2'){
            $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
            }       


            DB::table('boostapp.client_activities')
            ->where('CompanyNum', '=' , $CompanyNum)
            ->where('id', '=' , $GetWatingList->ClientActivitiesId)
            ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));

            }      
               
            ///// שליחת התראה ללקוח    
              
            $Date = date('Y-m-d'); 
               
            $Time = date('H:i:s'); 
        
            if ($WatingListNight=='1'){
              
            if ($WatingListStartTime<=date('H:i:s') && $WatingListEndTime>=date('H:i:s')) {
            $Time = $WatingListEndTime;    
            }    
            else {
            $Time = date('H:i:s');    
            }    
                
                
            }   
               
               
            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->where('Type', '=' , '3')->first();
    
            $TemplateStatus = $Template->Status;   
            $TemplateSendOption = $Template->SendOption;  
            $SendStudioOption = $Template->SendStudioOption;      
            $Type = '0';    
             
            if ($TemplateSendOption=='BA999'){
            $Type = '0';    
            }
            else if ($TemplateSendOption=='BA000'){}    
            else {
            $myArray = explode(',', $TemplateSendOption); 
            $Type2 = (in_array('2', $myArray)) ? '2' : '';
            $Type1 = (in_array('1', $myArray)) ? '1' : '';
            $Type0 = (in_array('0', $myArray)) ? '0' : '';    
                
            if (@$Type2!=''){
            $Type = $Type2;     
            }
            if (@$Type1!=''){
            $Type = $Type1;     
            }
            if (@$Type0!=''){
            $Type = $Type0;    
            }     
                
            }     
               
            if ($GetWatingList->TrueClientId=='0'){   
            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->ClientId)->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();
            } else {
            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->TrueClientId)->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();    
            }
               
            $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();
     
            /// עדכון תבנית הודעה
            $ClassDate_Not = with(new DateTime($GetWatingList->ClassDate))->format('d/m/Y'); 
            $ClassTime_Not = with(new DateTime($GetWatingList->ClassStartTime))->format('H:i'); 
            $ClassName_Not = $GetWatingList->ClassName;       
            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName,$Template->Content);
            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$ClientInfo->CompanyName,$Content1);
            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ClientInfo->FirstName,$Content2);
            $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], @$ClassName_Not,$Content3);
            $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], @$ClassDate_Not,$Content4);
            $Content6 = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], @$ClassTime_Not,$Content5);
            $ContentTrue = $Content6;   
    
    
            $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
            $Subject = $Template->Subject;
                
            if ($TemplateStatus!='1'){
            if ($TemplateSendOption!='BA000'){    
            if ($GetWatingList->TrueClientId=='0'){    
            $AddNotification = DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time, 'ChooseClass' => '2', 'ClassId' => $GetWatingList->id, 'priority' => 1));   
            }
            else {
            $AddNotification = DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->TrueClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time, 'ChooseClass' => '2', 'ClassId' => $GetWatingList->id, 'priority' => 1));                   
            } 
            }
            }
               
            //// קליטת לוג מערכת
            $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ClassId)->first();
            $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
            $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
            $LogClassName = $ClassInfo->ClassName;
                
            $LogText = 'שובץ מרשימת המתנה לשיעור '.$LogClassName.' בתאריך '.$LogClassDate.' ובשעה '.$LogClassTime;

            $InsertLog = DB::table('log')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText) );     

            if ($GetWatingList->TrueClientId=='0'){
            $FixClientId = $GetWatingList->ClientId;    
            }
            else {
            $FixClientId = $GetWatingList->TrueClientId;
            }  
               
            ///// Class Log
            $classLogId[] = DB::table('boostapp.classlog')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0')); 
            /////////////////////////////////////////   
               
               
               
            //// סיום קליטת לוג מערכת     
               
           $BrackStatus = '1';     
           }
           else if (($WatingListAct == '1' && $Watinglist == '2' && $WatingListActDevice == '1' && $WatingListActTrue == '1') ||
               ($WatingListAct == '1' && $ChooseClass == '1' && $WatingListActDevice == '1' && $WatingListActTrue == '1') ||
               ($WatingListAct == '1' && $Watinglist == '1' && $WatingListActDevice == '1' && $WatingListActTrue == '1')) { /// שליחת התראת שיבוץ והמתנה לתגובת הלקוח

            //// עדכון סטטוס  
            $NewStatus = '17'; // מומש מרשימת המתנה

            /// בדיקת סטטוס הלקוח
            $CheckOldStatus = DB::table('boostapp.class_status')->where('id', '=', $GetWatingList->Status)->first();
            $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();    
            
            $StatusCount = $CheckNewStatus->StatusCount;     
               
            // תיעוד שינוי סטטוס
 
            $Dates = date('Y-m-d H:i:s');   
            $UserId = Auth::user()->id;

            $StatusJson = '';    
            $StatusJson .= '{"data": [';  

            if ($GetWatingList->StatusJson!=''){                  
            $Loops =  json_decode($GetWatingList->StatusJson,true);	
            foreach($Loops['data'] as $key=>$val){ 

            $DatesDB = $val['Dates'];
            $UserIdDB = $val['UserId'];
            $StatusDB = $val['Status']; 
            $StatusTitleDB = $val['StatusTitle']; 
            $UserNameDB = $val['UserName'];     

            $StatusJson .= '{"Dates": "'.$DatesDB.'", "UserId": "'.$UserIdDB.'", "Status": "'.$StatusDB.'", "StatusTitle": "'.$StatusTitleDB.'", "UserName": "'.$UserNameDB.'"},';    

            }  
            }

            $StatusJson .= '{"Dates": "'.$Dates.'", "UserId": "", "Status": "'.$NewStatus.'", "StatusTitle": "'.$CheckNewStatus->Title.'", "UserName": ""}';

            $StatusJson .= ']}';     
            

            $TimeAutoWatinglists =  date("Y-m-d H:i:s", strtotime('+'.$WatinglistMin.' minutes', strtotime(date('Y-m-d H:i:s')))); 
            $TimeAutoWatinglist =  date("H:i:s", strtotime($TimeAutoWatinglists)); 
            $TimeAutoWatinglistDate =  date("Y-m-d", strtotime($TimeAutoWatinglists));    
               
            if ($WatingListNight=='1'){
            if ($WatingListStartTime<=date('H:i:s') && $WatingListEndTime>=date('H:i:s')) {    
            $TimeAutoWatinglists =  date("Y-m-d H:i:s", strtotime('+'.$WatinglistMin.' minutes', strtotime(date('Y-m-d '.$WatingListEndTime)))); 
            $TimeAutoWatinglist =  date("H:i:s", strtotime($TimeAutoWatinglists)); 
            $TimeAutoWatinglistDate =  date("Y-m-d", strtotime($TimeAutoWatinglists)); 
            }
            }

            (new ClassStudioAct($GetWatingList->id))->update([
                'Status' => $NewStatus,
                'StatusJson' => $StatusJson,
                'StatusCount' => $StatusCount,
                'TimeAutoWatinglist' => $TimeAutoWatinglist,
                'TimeAutoWatinglistDate' => $TimeAutoWatinglistDate,
                'StatusTimeAutoWatinglist' => '1',
                'DeviceId' => $CheckDeviceId,
                'FreeWatingList' => $FreeWatingList,
            ]);
               
            ///// שליחת התראה ללקוח    
              
            $Date = date('Y-m-d');    
            $Time = date('H:i:s'); 
               
            if ($WatingListNight=='1'){
              
            if ($WatingListStartTime<=date('H:i:s') && $WatingListEndTime>=date('H:i:s')) {
            $Time = $WatingListEndTime;    
            }    
            else {
            $Time = date('H:i:s');    
            }    
                
                
            }      
               
           
            $Template = DB::table('boostapp.notificationcontent')->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->where('Type', '=' , '4')->first();
    
            $TemplateStatus = $Template->Status;   
            $TemplateSendOption = $Template->SendOption;  
            $SendStudioOption = $Template->SendStudioOption;      
            $Type = '0';    
             
            if ($TemplateSendOption=='BA999'){
            $Type = '0';    
            }
            else if ($TemplateSendOption=='BA000'){}    
            else {
            $myArray = explode(',', $TemplateSendOption); 
            $Type2 = (in_array('2', $myArray)) ? '2' : '';
            $Type1 = (in_array('1', $myArray)) ? '1' : '';
            $Type0 = (in_array('0', $myArray)) ? '0' : '';    
                
            if (@$Type2!=''){
            $Type = $Type2;     
            }
            if (@$Type1!=''){
            $Type = $Type1;     
            }
            if (@$Type0!=''){
            $Type = $Type0;    
            }     
                
            }     
               
            if ($GetWatingList->TrueClientId=='0'){   
            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->ClientId)->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();
            } else {
            $ClientInfo = DB::table('boostapp.client')->where('id', '=', $GetWatingList->TrueClientId)->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();    
            }  
               
            $CompanyInfo = DB::table('boostapp.settings')->where('CompanyNum', '=' , $GetWatingList->CompanyNum)->first();
     
            /// עדכון תבנית הודעה
            $ClassDate_Not = with(new DateTime($GetWatingList->ClassDate))->format('d/m/Y'); 
            $ClassTime_Not = with(new DateTime($GetWatingList->ClassStartTime))->format('H:i'); 
            $ClassName_Not = $GetWatingList->ClassName;
            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], @$CompanyInfo->AppName,$Template->Content);
            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], @$ClientInfo->CompanyName,$Content1);
            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], @$ClientInfo->FirstName,$Content2);
            $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], @$ClassName_Not,$Content3);
            $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], @$ClassDate_Not,$Content4);
            $Content6 = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], @$ClassTime_Not,$Content5);
            $ContentTrue = $Content6;

    
    
            $TextNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
            $Subject = $Template->Subject;
               
            if ($TemplateStatus!='1'){
            if ($TemplateSendOption!='BA000'){    
            if ($GetWatingList->TrueClientId=='0'){   
            $AddNotification = DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time, 'ChooseClass' => $ChooseClass, 'ClassId' => $GetWatingList->id, 'FreeWatingList' => $FreeWatingList, 'StatusFreeWatingList' => $StatusFreeWatingList, 'TrueWatingLimit' => $TrueWatingLimit, 'priority' => 1)); 
            }
            else {
            $AddNotification = DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->TrueClientId, 'TrueClientId' => '0', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time, 'ChooseClass' => $ChooseClass, 'ClassId' => $GetWatingList->id, 'FreeWatingList' => $FreeWatingList, 'StatusFreeWatingList' => $StatusFreeWatingList, 'TrueWatingLimit' => $TrueWatingLimit, 'priority' => 1));                
            }   
            }
            }
                
            //// קליטת לוג מערכת
            $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ClassId)->first();
            $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
            $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
            $LogClassName = $ClassInfo->ClassName;
                
            $LogText = 'נשלחה התראת תגובה מרשימת המתנה לשיעור '.$LogClassName.' בתאריך '.$LogClassDate.' ובשעה '.$LogClassTime;

            $InsertLog = DB::table('log')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'UserId' => '0', 'Text' => $LogText) );   
               
            /// שליחת התראה לסטודיו בדואר אלקטרוני    
            if ($SendNotification=='1'){
             
            $TextNotification = 'נשלחה התראת תגובה מרשימת המתנה לשיעור '.$LogClassName.' בתאריך '.$LogClassDate.' ובשעה '.$LogClassTime;  
            $Subject = $DisplayName.' המתנה לשיעור';
            $Date = date('Y-m-d');    
            $Time = date('H:i:s'); 
            $Dates = date('Y-m-d H:i:s');  
            if ($GetWatingList->TrueClientId=='0'){     
            DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->ClientId, 'TrueClientId' => '0', 'Type' => '4', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'ClassId' => '0', 'priority' => 1) ); 
            }
            else {
            DB::table('boostapp.appnotification')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClientId' => $GetWatingList->TrueClientId, 'TrueClientId' => '0', 'Type' => '4', 'Subject' => $Subject, 'Text' => $TextNotification, 'Dates' => $Dates, 'UserId' => '0', 'Date' => $Date, 'Time' => $Time, 'ClassId' => '0', 'priority' => 1) );     
            }    
                 
            }     
            
            if ($GetWatingList->TrueClientId=='0'){
            $FixClientId = $GetWatingList->ClientId;    
            }
            else {
            $FixClientId = $GetWatingList->TrueClientId;
            }  
               
            ///// Class Log
            $classLogId[] = DB::table('boostapp.classlog')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0')); 
            /////////////////////////////////////////      

            //// סיום קליטת לוג מערכת  
               
               
            $BrackStatus = '1';   
               
            } 
            else {
              
            //// עדכון סטטוס  
            $NewStatus = '20'; // בוטל אוטומטית מרשימת המתנה

            /// בדיקת סטטוס הלקוח
            $CheckOldStatus = DB::table('boostapp.class_status')->where('id', '=', $GetWatingList->Status)->first();
            $CheckNewStatus = DB::table('boostapp.class_status')->where('id', '=', $NewStatus)->first();    
            
            $StatusCount = $CheckNewStatus->StatusCount;     
               
            // תיעוד שינוי סטטוס
 
            $Dates = date('Y-m-d H:i:s');   
            $UserId = Auth::user()->id;

            $StatusJson = '';    
            $StatusJson .= '{"data": [';  

            if ($GetWatingList->StatusJson!=''){                  
            $Loops =  json_decode($GetWatingList->StatusJson,true);	
            foreach($Loops['data'] as $key=>$val){ 

            $DatesDB = $val['Dates'];
            $UserIdDB = $val['UserId'];
            $StatusDB = $val['Status']; 
            $StatusTitleDB = $val['StatusTitle']; 
            $UserNameDB = $val['UserName'];     

            $StatusJson .= '{"Dates": "'.$DatesDB.'", "UserId": "'.$UserIdDB.'", "Status": "'.$StatusDB.'", "StatusTitle": "'.$StatusTitleDB.'", "UserName": "'.$UserNameDB.'"},';    

            }  
            }

            $StatusJson .= '{"Dates": "'.$Dates.'", "UserId": "", "Status": "'.$NewStatus.'", "StatusTitle": "'.$CheckNewStatus->Title.'", "UserName": ""}';

            $StatusJson .= ']}';

             (new ClassStudioAct($GetWatingList->id))->update([
                 'Status' => $NewStatus,
                 'StatusJson' => $StatusJson,
                 'StatusCount' => $StatusCount,
             ]);
                
            if ($GetWatingList->TrueClientId=='0'){
            $FixClientId = $GetWatingList->ClientId;    
            }
            else {
            $FixClientId = $GetWatingList->TrueClientId;
            }  
               
            ///// Class Log
            $classLogId[] = DB::table('boostapp.classlog')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0')); 
            /////////////////////////////////////////          
                
                
                
            }   
               
               
            if ($BrackStatus=='1'){
            break;   
            }  
            else {
            $BrackStatus = '0';    
            } 
                    
               
            } ////  סיום לולאה רשימת המתנה
            } 
            }



            //// עדכון שיעור ברשימת משתתפים
            
            $ClientRegister = DB::table('boostapp.classstudio_act')->where('ClassId', '=' , $ClassId)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '0')->count();
            $WatingListNum = DB::table('boostapp.classstudio_act')->where('ClassId', '=' , $ClassId)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '1')->count();
               
               
             DB::table('boostapp.classstudio_date')
		    ->where('CompanyNum', '=' , $CompanyNum)
            ->where('id', '=' , $ClassId)
            ->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingListNum));

            foreach ($classLogId as $logId)
                DB::table('boostapp.classlog')->where('id', '=', $logId)->update(array('numOfClients' => $ClientRegister));

            $ClassInfo = DB::table('boostapp.classstudio_date')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ClassId)->first();
            $LogClassDate = with(new DateTime($ClassInfo->StartDate))->format('d/m/Y');
            $LogClassTime = with(new DateTime($ClassInfo->StartTime))->format('H:i');
            $LogClassName = $ClassInfo->ClassName;

             $LogWatingList = 'הריץ רשימת המתנה ידנית לשיעור '.$LogClassName.' בתאריך:'.$LogClassDate.' בשעה:'.$LogClassTime;
             CreateLogMovement($LogWatingList, '0');
    
            //////////////////////////  סיום בדיקת רשימות המתנה ושיבוצם בהתאם ///////////////////////////////////


?>
