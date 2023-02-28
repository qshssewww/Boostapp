<?php
require_once '../../app/initcron.php';
require_once __DIR__.'/../Classes/ClientActivities.php';


$CompanyNum = Auth::user()->CompanyNum;
$ClientId = $_REQUEST['ClientId'];
$ClassId = $_REQUEST['ClassId'];
$ClientAddClassType = @$_REQUEST['ClientAddClassType'];
$ClassStatus = @$_REQUEST['ClassStatus'];
$ExtraClient = '0';

if ($ClassStatus==''){
$ClassStatus = '0';   
}

$ClientActivity = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->first();
$Activity = $ClientActivity->ClassMemberType;
$getFirstClass = DB::table('classstudio_act')->where('CompanyNum', '=', $CompanyNum)->where('ClassId', '=', $ClassId)->where('StatusCount', '=', '0')->count();
$showMsg = false;

$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClientId)->first();

            $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=' , $ClientActivity->id)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '0')->count();
            $WatingList = DB::table('classstudio_act')->where('ClassId', '=' , $ClientActivity->id)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '1')->count();
               
               
            $ClientRegisterRegular1 = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClientActivity->GroupNumber)->where('DayNum', '=', $ClientActivity->DayNum)->where('ClassTime', '=', $ClientActivity->StartTime)->where('Floor', '=', $ClientActivity->Floor)->where('RegularClassType', '=','1')->where('StatusType', '=','12')
            ->count();
            $ClientRegisterRegularWating1 = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClientActivity->GroupNumber)->where('DayNum', '=', $ClientActivity->DayNum)->where('ClassTime', '=', $ClientActivity->StartTime)->where('Floor', '=', $ClientActivity->Floor)->where('RegularClassType', '=','1')->where('StatusType', '=','9')
            ->count();   
    
    
            $ClientRegisterRegular2 = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClientActivity->GroupNumber)->where('DayNum', '=', $ClientActivity->DayNum)->where('ClassTime', '=', $ClientActivity->StartTime)->where('Floor', '=', $ClientActivity->Floor)->where('RegularClassType', '=','2')->where('EndDate', '>=',$ClientActivity->StartDate)->where('StatusType', '=','12')
            ->count();
            $ClientRegisterRegularWating2 = DB::table('classstudio_dateregular')
            ->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClientActivity->GroupNumber)->where('DayNum', '=', $ClientActivity->DayNum)->where('ClassTime', '=', $ClientActivity->StartTime)->where('Floor', '=', $ClientActivity->Floor)->where('RegularClassType', '=','2')->where('EndDate', '>=',$ClientActivity->StartDate)->where('StatusType', '=','9')
            ->count();   
    
             $ClientRegisterRegular = $ClientRegisterRegular1+$ClientRegisterRegular2;
             $ClientRegisterRegularWating = $ClientRegisterRegularWating1+$ClientRegisterRegularWating2;


if ($ClientRegisterRegular>=$ClientActivity->MaxClient){
$ExtraClient = '1';
$showMsg = true;    
} else if ($getFirstClass >= $ClientActivity->MaxClient) {
  $ExtraClient = '1';
  $showMsg = false;
}




//$MemberShipClients = DB::select('select * from boostapp.client_activities where (CompanyNum = "'.$CompanyNum.'"  AND Department != "4" AND Status = "0" AND FIND_IN_SET("'.$ClientId.'",TrueClientId) > 0 )
// OR (CompanyNum = "'.$CompanyNum.'" AND ClientId = "'.$ClientId.'" AND Department != "4" AND Status = "0")
// Order By `CardNumber` DESC ');
if($ClientAddClassType == '3')
    $MemberShipClients = (new ClientActivities())->getActivitiesForRegularClassAssignment($CompanyNum, $ClientId);
else
    $MemberShipClients = (new ClientActivities())->getActivitiesForRegularAssignment($CompanyNum, $ClientId);

 
$Disabled = '';   
$i = '1';
if (!empty($MemberShipClients)){
    
$Floor = DB::table('sections')->where('id','=', $ClientActivity->Floor)->where('CompanyNum', $CompanyNum)->first();
$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClientActivity->ClassDevice)->where('Status', '=', '0')->first();

if ($ClientActivity->ClassMemberType=='BA999'){
$MembershipType = 'כל סוגי המנויים';   
}
else {
$z = '1';
$myArray = explode(',', $ClientActivity->ClassMemberType);	
$MembershipType = '';	
$SoftInfos = DB::table('membership_type')->where('CompanyNum', $CompanyNum)->whereIn('id', $myArray)->get();
$SoftCount = count($SoftInfos);
	
foreach ($SoftInfos as $SoftInfo){

$MembershipType .= $SoftInfo->Type;

if($SoftCount==$z){}else {	
$MembershipType .= ', ';	
}
	
++$z; 	
}	

$MembershipType = $MembershipType;
}    
    
    
?>    

<hr>

<?php    
foreach ($MemberShipClients as $MemberShipClient) {
$Disabled = '';
    $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipClient->MemberShip)->first();  
    if ($MemberShipClient->MemberShip=='BA999'){
    $Type = 'ללא סוג מנוי';    
    } 
    else {
    $Type = $membership_type->Type;     
    }                                                    

    if ($MemberShipClient->Department=='1' && $MemberShipClient->FirstDateStatus=='0') {
    $TokefText = 'בתוקף עד: '. with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');
    $BalnaceText = '';
        
    if ($MemberShipClient->TrueDate>=date('Y-m-d')){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }    
        
        
    }
    else if ($MemberShipClient->Department=='2') {
    $BalnaceText = '<span dir="ltr">'.$MemberShipClient->TrueBalanceValue.'</span> <span dir="rtl">יתרת שיעורים:</span>'; 
    $TokefText = ''; 
        
    if ($MemberShipClient->TrueBalanceValue>='1'){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }      
        
        
    if ($MemberShipClient->TrueDate!='' && $MemberShipClient->FirstDateStatus=='0'){
    $TokefText = 'בתוקף עד: '. with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');  
        
    if ($MemberShipClient->TrueDate>=date('Y-m-d')){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';    
    }      
        
    }
        
    }
    
    else if ($MemberShipClient->Department=='3') {
    $BalnaceText = '<span dir="ltr">'.$MemberShipClient->TrueBalanceValue.'</span> <span dir="rtl">יתרת שיעורים:</span>'; 
    $TokefText = ''; 
        
    if ($MemberShipClient->TrueBalanceValue>='1'){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }      
    
        
    } 
    else {
    $BalnaceText = 'תוקף יחושב מתאריך שיעור ראשון'; 
    $TokefText = '';     
    $CheckBoxColor = 'success';     
    }
    
    if ($MemberShipClient->TrueClientId=='0'){
    $TrueClientText = '';    
    }
    else {
    $TrueClientText = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i> מנוי משפחתי';    
    }
    
?>

<div class="checkbox">
<label style="padding-right:25px;">
<input name="ActivityId" id="ActivityId_<?php echo $i; ?> " type="radio" value="<?php echo $MemberShipClient->id; ?>" class="pull-right" <?php echo $Disabled; ?>><span class="text-<?php echo $CheckBoxColor; ?>"> <?php echo $Type; ?>, #<?php echo $MemberShipClient->CardNumber; ?> - <?php echo $MemberShipClient->ItemText; ?> // </span><span dir="ltr" class="text-<?php echo $CheckBoxColor; ?>"><?php echo $BalnaceText; ?></span><span class="text-<?php echo $CheckBoxColor; ?>"> <?php echo $TokefText; ?></span> <?php echo $TrueClientText;?> </label><br>
</div>



<?php ++ $i; } ?> 


<div class="alertb alert-warning"> 
 * בחר מנוי אחד מהרשימה הנ"ל.<br>   
 *  לא ניתן לשבץ לקוח שאין לו יתרה במנוי או מנוי לא בתוקף!<br>
 *  ניתן לשבץ לקוח לפי סוג המנוי שהוגדר בשיעור זה בלבד.
</div> 

<?php if ($ClientInfo->LastClassDate!=''){ ?>    
<div class="alertb alert-info"> 
תאריך שיעור אחרון: <?php echo with(new DateTime($ClientInfo->LastClassDate))->format('d/m/Y'); ?>
</div> 
<?php } ?>


<?php
if ($ClientActivity->ClassDevice=='0'){
?>    
<input type="hidden" name="DeviceId" value="0">
<?php     
} else {    
$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClientActivity->ClassDevice)->where('Status', '=', '0')->first();   
echo @$ClassDeviceName->Name;     
?>
</div>     
<div class="form-group">     
<select name="DeviceId" data-placeholder="בחר <?php echo @$ClassDeviceName->Name; ?>" class="form-control select2General" style="width:100%;" >
<option value=""></option>    
<?php 
$ClassDeviceInfos = DB::table('numberssub')->where('NumbersId', $ClientActivity->ClassDevice)->where('CompanyNum', $CompanyNum)->where('Status', '=', '0')->orderBy('Name', 'ASC')->get();  
foreach ($ClassDeviceInfos as $ClassDeviceInfo) {   
    
$CheckDevice = DB::table('classstudio_act')->where('CompanyNum', $CompanyNum)->where('ClassId', '=', $ClassId)->where('DeviceId', '=', $ClassDeviceInfo->id)->where('StatusCount', '=', '0')->count(); 
   
    
  if (@$CheckDevice!='0'){} else {   
    
?>    
<option value="<?php echo $ClassDeviceInfo->id ?>" ><?php echo $ClassDeviceInfo->Name ?></option>
<?php } } ?>
    
    
 <?php if ($ClientActivity->ClientRegister>=$ClientActivity->MaxClient) { ?>      
 <option value="0">בחר המתנה</option>      
 <?php } ?>       
    
    
</select>    
    
</div>  
<?php } ?>

<div class="form-group">   
<label>הערה לשיעור</label>    
<textarea name="ClientReClassReason" class="form-control" rows="2" dir="rtl"></textarea>    
</div>    
<div class="form-group">   
<select name="ShowRemarks" class="form-control" style="width:100%;" >
<option value="1">הערה מוצגת ללקוח</option>
<option value="0" selected>הערה מוסתרת מהלקוח</option>    
</select>     
</div>     

<hr>

 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClientActivity->ClassName ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClientActivity->GuideName ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $Floor->Title ?> 
 </div>   
  <div class="col-md-3">
 <?php if ($ClientActivity->MinClass=='0') { echo 'ללא מינימום משתתפים'; } else { ?>   
 מינמום משתתפים: <?php echo $ClientActivity->MinClassNum; } ?> 
 </div>  
</div>


 <div class="row">
 <div class="col-md-3">	 
 תאריך: <?php echo with(new DateTime($ClientActivity->StartDate))->format('d/m/Y'); ?> 
 </div> 
  <div class="col-md-3">	 
 יום: <?php echo $ClientActivity->Day ?> 
 </div>       
  <div class="col-md-3">	 
 התחלה: <?php echo with(new DateTime($ClientActivity->StartTime))->format('H:i'); ?> 
 </div>  
  <div class="col-md-3">	 
 סיום: <?php echo with(new DateTime($ClientActivity->EndTime))->format('H:i'); ?> 
 </div>  
</div>

  <hr>            

 <div class="row">
 <div class="col-md-6">	 
 <label>מספר רשומים:</label>
<span style="font-weight:bold; color:forestgreen"><?php echo $ClientActivity->ClientRegister; ?> מתוך <?php echo $ClientActivity->MaxClient; ?> רשומים (<?php echo $ClientActivity->MaxClient-$ClientActivity->ClientRegister; ?> מקומות פנויים)</span>
 </div>  
     
<div class="col-md-6">	 
<label>רשימת המתנה:</label>
<span style="font-weight:bold; color:orangered"><?php echo $ClientActivity->WatingList; ?> ברשימת המתנה</span>
 </div>   
     
</div>


 <div class="row">
 <div class="col-md-6">	 
 <label>מספר רשומים בשיבוץ קבוע:</label>
<span style="font-weight:bold; color:dodgerblue"><?php echo @$ClientRegisterRegular; ?></span>
 </div>  
     
 <div class="col-md-6">	 
 <label>מספר רשומים בממתינים קבוע:</label>
<span style="font-weight:bold; color:orangered"><?php echo @$ClientRegisterRegularWating; ?></span>
 </div>       
     
     
</div>


<hr>


<?php if ($ClientAddClassType=='2' && $ExtraClient=='1' && $ClassStatus=='12' || $ClientAddClassType=='3' && $ExtraClient=='1' && $ClassStatus=='12') { ?>
<?php if ($showMsg) { ?>
<div class="alertb alert-danger">
שים לב! בעת שיבוץ לקוח זה תחרוג מהכמות המקסימלית של מתאמנים קבועים בשיעור זה<br>
האם להמשיך בכל זאת?
</div>
<?php } else { ?>
<div class="alertb alert-danger">
שים לב! בעת שיבוץ לקוח זה, תחרוג בשיעור הקרוב בתאריך <strong><?php echo with(new DateTime($ClientActivity->StartDate))->format('d/m/Y'); ?> ביום <?php echo $ClientActivity->Day ?></strong> מכמות המשתתפים המקסימלית<br>
האם להמשיך בכל זאת?
</div>
<?php } ?>

 <div class="row">
 <div class="col-md-3">	 
<div class="form-group" dir="rtl">
<button type="submit" name="submit" id="SaveClassClient" class="btn btn-danger text-white">כן, המשך ושמור שינויים</button> 
</div>
</div>
 <div class="col-md-3">	      
<div class="form-group" dir="rtl">
<button type="button" onClick="window.location.reload()" class="btn btn-dark text-white">לא, אל תשבץ לקוח לשיעור זה</button> 
</div>
</div>
     
<div class="col-md-3"></div> 
<div class="col-md-3"></div>      
     
</div>     

<hr>

<?php } else { ?>

<div class="form-group" dir="rtl">
<button type="submit" name="submit" id="SaveClassClient" class="btn btn-dark text-white">שמור שינויים</button>    
</div>

<?php } ?>



<?php } else { ?> 

<?php if (!empty($ClientId)){ ?>
<div class="alertb alert-warning">             
* לא נמצא מנוי ללקוח זה.<br>
יש להקים מנוי בכרטיס הלקוח ורק לאחר מכן לשבץ לשיעור!
</div> 


<?php } } ?>

<script>
$( ".select2General" ).select2( {
		theme:"bootstrap", 
		language: "he",
		allowClear: true,
		width: '100%',
        dir: "rtl" } );  
</script>