<?php
require_once '../../app/initcron.php';
require_once '../Classes/ClassStudioDate.php';

if (Auth::userCan('124')): 
$ItemId = $_REQUEST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ItemId)->first();
$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Items->ClientId)->first(); 


if ($Items->TrueClientId=='0'){
$TrueClientIcon = '';    
}   
else {
$TrueClientIcon = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i>';    
}   

?>

 <div class="row">
 <div class="col-md-12">	 
<span class="text-center font-weight-bold"><?php echo $ClientInfo->CompanyName; ?></span>
 </div> 
<div class="col-md-12">	 
<span class="text-center font-weight-bold"><?php echo $TrueClientIcon; ?> <?php echo $Items->ItemText; ?> // מנוי מספר <?php echo $Items->CardNumber; ?></span>
 </div> 
 </div>      



       <style>
       
           .DivScroll::-webkit-scrollbar {
             width: 2px;
             padding-left: 0px;
             margin-left: 0px;
           } 
           
             .DivScroll::-webkit-scrollbar-thumb {
             background-color: darkgrey;
             outline: 1px solid slategray;
            padding-left: 0px;
             margin-left: 0px;     
           }    
           
       
       </style>   
 

 <div class="row">
<?php if ($Items->TrueDate!='' && $Items->FirstDateStatus=='0'){ ?> 
 <div class="col-md-<?php if ($Items->Department=='1'){ echo '12'; } else if ($Items->Department=='2' && $Items->TrueDate!='') { echo '6'; }?>">	 
 <label>תוקף:</label>
<?php echo with(new DateTime($Items->TrueDate))->format('d/m/Y'); ?>
 </div> 
<?php } if ($Items->Department=='2'){ ?>   
<div class="col-md-<?php if ($Items->TrueDate!=''){ echo '6'; } else { echo '12'; }?>">
<label>שיעורים:</label>
<span id="ClientTRDiv_Card" dir="ltr"><?php echo $Items->TrueBalanceValue; ?> / <?php echo $Items->BalanceValue; ?></span>
</div>  
<?php } ?>     
</div>

 <div class="row">
 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:320px; overflow-y:scroll; overflow-x:hidden;'>

<table class="table table-bordered table-sm table-responsive-sm">

<thead>
<th style="text-align:right;">#</th>
<th style="text-align:right;">מיקום</th>
<th style="text-align:right;">כותרת</th>
<th style="text-align:right;">יום</th>
<th style="text-align:right;">תאריך</th>
<th style="text-align:right;">שעה</th>    
<th style="text-align:right;">מדריך</th>
<th style="text-align:right;">סטטוס</th>   
</thead>

<tbody>
<?php 
$i = '1';    
$ClassLogs = DB::table('classstudio_act')->where('ClientActivitiesId', '=', $ItemId)->where('CompanyNum', $CompanyNum)->where('RegularClass', '=', '1')->orderBy('ClassDate','DESC')->get(); 
foreach ($ClassLogs as $ClassLog) {

    $classStudioDateInfo = ClassStudioDate::find($ClassLog->ClassId);;

$StatusInfoColor = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();     
    
$FloorInfo = DB::table('sections')->where('id', '=', $ClassLog->FloorId)->where('CompanyNum', '=', $CompanyNum)->first();  
$GuideInfo = DB::table('users')->where('id', '=', $ClassLog->GuideId)->where('CompanyNum', '=', $CompanyNum)->first();
$FloorName = $FloorInfo->Title ?? '';
$GuideName = $GuideInfo->display_name ?? '';
$class_day = $classStudioDateInfo->Day ?? $ClassLog->ClassName;
$class_name = $classStudioDateInfo->ClassName ?? $ClassLog->ClassName;
$start_date = $classStudioDateInfo->StartDate ?? $ClassLog->ClassDate;
$start_time = $classStudioDateInfo->StartTime ?? $ClassLog->ClassStartTime;
    
?>   
    
    
<tr <?php echo $StatusInfoColor->Color; ?> >
<td class="align-middle"><?php echo $i; ?></td>
<td class="align-middle"><?php echo $FloorName; ?></td>
<td class="align-middle"><?php echo $class_name; ?></td>
<td class="align-middle"><?php echo $class_day; ?></td>
<td class="align-middle"><?php echo with(new DateTime($start_date))->format('d/m/Y'); ?></td>
<td class="align-middle"><?php echo with(new DateTime($start_time))->format('H:i'); ?></td>
<td class="align-middle"><?php echo $GuideName ?? ''; ?></td>
<td class="align-middle">
 <?php if (Auth::userCan('62')): ?> 
<select name="StatusEvent" id="StatusEvent<?php echo $ClassLog->id ?>" data-placeholder="בחר סטטוס" class="form-control" style="width:100%;" >
<?php 
$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '0')->orderBy('id', 'ASC')->get();  
foreach ($ClassStatusInfos as $ClassStatusInfo) {    
?>    
<option value="<?php echo $ClassLog->id ?>:<?php echo $ClassLog->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassLog->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?></option>
<?php } ?>
	
<?php 
$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '1')->where('id', '=', $ClassLog->Status)->orderBy('id', 'ASC')->get();  
foreach ($ClassStatusInfos as $ClassStatusInfo) {    
?>    
<option value="<?php echo $ClassLog->id ?>:<?php echo $ClassLog->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassLog->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?></option>
<?php } ?>	
	
	
	
</select>    
 <?php else: 
$ClassStatusInfo = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();     
echo $ClassStatusInfo->Title;       
?>
    
<?php endif ?>     
</td>    
</tr> 
    
<?php if ($ClassLog->ReClass=='2'){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u>השלמת שיעור:</u> <?php echo $ClassLog->ReClassReason; ?></td>
</tr>    
<?php } ?>    
    
    
<?php if ($ClassLog->Remarks!=''){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u>הערה לשיעור:</u> <?php echo $ClassLog->Remarks; ?></td>
</tr>    
<?php } ?>
    
<?php if ($ClassLog->StatusJson!=''){
$StatusLog = '';
$Loops =  json_decode($ClassLog->StatusJson,true);	
foreach($Loops['data'] as $key=>$val){ 

$DatesDB = $val['Dates'];
$UserIdDB = $val['UserId'];
$StatusDB = $val['Status']; 
$StatusTitleDB = $val['StatusTitle']; 
$UserNameDB = $val['UserName'];     

$StatusLog .= with(new DateTime($DatesDB))->format('d/m/Y H:i').' <u>'.$StatusTitleDB.'</u> '.$UserNameDB.', ';     
  
}      
  
$StatusLog = rtrim($StatusLog, ', ');    
    
?>    
<tr style="font-size: 13px;">
<td colspan="8"><u>לוג סטטוס:</u> <?php echo $StatusLog; ?></td>
</tr>    
<?php } ?>    
  
<?php if ($ClassLog->TrueClientId!='0') { 
$TrueClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassLog->TrueClientId)->first();         
?>  
<tr style="font-size: 13px;">
<td colspan="8"><u>מנוי משפחתי: הוזמן ע"י</u> <a href="ClientProfile.php?u=<?php echo @$TrueClientInfo->id; ?>"><?php echo @$TrueClientInfo->CompanyName; ?></a> </td>
</tr>      
<?php } ?>    
    
<tr style="font-size: 13px; display:none;" id="ClientTRClassInsetedDiv<?php echo $ClassLog->id ?>" class="HideDiv">   
<td colspan="8">
   
<form action="AddClientClassInsetedPopUp" class="ajax-form clearfix text-right popup-ajax" autocomplete="off"> 
<div class="form-group">     
<input type="hidden" name="ActivityId" value="<?php echo $ClassLog->id; ?>">    
<input type="hidden" name="ClientId" value="<?php echo $ClassLog->ClientId; ?>">
בחר שיעור עבורו הלקוח משלים שיעור    
</div>     
<div class="form-group">     

<select name="ForWhichReClass" data-placeholder="בחר שיעור" class="form-control select2General" style="width:100%;" >
<option value=""></option>    
<?php 
$ReplaceClassInfos = DB::table('classstudio_act')->where('ClientId', $ClassLog->ClientId)->where('CompanyNum', $CompanyNum)->where('ClassDate', '<', $ClassLog->ClassDate)->orderBy('ClassDate', 'ASC')->get();  
foreach ($ReplaceClassInfos as $ReplaceClassInfo) {   
    
?>    
<option value="<?php echo $ReplaceClassInfo->id ?>" <?php if (@$ClassLog->ForWhichReClass==$ReplaceClassInfo->id){ echo 'selected'; } else {} ?>><?php echo $ReplaceClassInfo->ClassName; ?></option>
<?php } ?>
</select>     
</div>   
<div class="form-group">   
<label>כתוב סיבה להשלמת השיעור</label>    
<textarea name="ClientReClassReason" class="form-control" rows="2" dir="rtl"><?php echo $ClassLog->ReClassReason ?></textarea>    
</div>     
<div class="form-group">
<button type="submit" name="submit" class="btn btn-dark btn-sm text-white">שמור שינויים</button>    
</div>
</form>    
</td>    
</tr>      
    
    
<script>
$("#StatusEvent<?php echo $ClassLog->id ?>").change(function () {
var Acts = this.value;    
$.ajax({
type: 'POST',  
data:'Act='+ Acts,
dataType: 'json',    
url:'new/StatusChange.php',     
success: function(data){
$('#ClientTRDiv_Card').html(data.Cards); 
$('#ClientProfileTRDiv_Card<?php echo $Items->id ?>').html(data.Cards);  
    
if (data.ReClass=='True'){    
$('#ClientTRClassInsetedDiv<?php echo $ClassLog->id ?>').show(); 
}
else {
$('#ClientTRClassInsetedDiv<?php echo $ClassLog->id ?>').hide();   
}      
 
BN('0','הפעולה בוצעה בהצלחה!');    
    

}
});
		 
		 
		 
		 
    });      
</script>    
    
    
<?php ++$i; } ?> 
</tbody>
</table> 
     
</div>
</div>     

   

<?php if ($Items->Department=='2' && $Items->BalanceValueLog!=''){ ?>
<span class="text-center font-weight-bold">לוג קליטה ידנית</span>

<?php
        $Loops =  json_decode($Items->BalanceValueLog,true);	
        foreach($Loops['data'] as $key=>$val){ 
        if ($val['ClassNumber']>'0'){
        $BalanceText = 'הוספה';    
        }   
        else {
        $BalanceText = 'החסרה';    
        }        
            
        $UserName = DB::table('users')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $val['UserId'])->first();
            
        echo $ClassNumber = '<div class="alertb alert-light">'.$BalanceText.' <span dir="ltr">'.$val['ClassNumber'].'</span> שיעורים. <br>'.$val['Reason'].'<br>
        <span style="font-size: 13px;"> עודכן ע"י '.@$UserName->display_name.' בתאריך: '.with(new DateTime($val['Dates']))->format('d/m/Y H:i').'<span></div><hr>'; 
        }                                                                  
?>                                                                  


<?php } ?>
<?php if ($Items->Department=='1' && $Items->StudioVaildDateLog!='' || $Items->Department=='2' && $Items->TrueDate!='' && $Items->StudioVaildDateLog!=''){ ?>
<span class="text-center font-weight-bold">לוג הארכת תוקף</span>

<?php
        $Loops =  json_decode($Items->StudioVaildDateLog,true);	
        foreach($Loops['data'] as $key=>$val){ 
                   
        $UserName = DB::table('users')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $val['UserId'])->first();
            
        echo $ClassNumber = '<div class="alertb alert-light">הארכת תוקף למנוי <span dir="ltr">'.with(new DateTime($val['TrueDate']))->format('d/m/Y').'</span> <br>'.$val['Reason'].'<br>
        <span style="font-size: 13px;"> עודכן ע"י '.@$UserName->display_name.' בתאריך: '.with(new DateTime($val['Dates']))->format('d/m/Y H:i').'<span></div><hr>'; 
        }                                                                  
?>  

<?php } ?>
<?php if ($Items->Department=='1' && $Items->FreezLog!='' || $Items->Department=='2' && $Items->TrueDate!='' && $Items->FreezLog!=''){ ?>
<span class="text-center font-weight-bold">לוג הקפאת מנוי</span>

<?php
        $Loops =  json_decode($Items->FreezLog,true);	
        foreach($Loops['data'] as $key=>$val){ 
                   
        $UserName = DB::table('users')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $val['UserId'])->first();
            
        echo $ClassNumber = '<div class="alertb alert-light">הקפאת מנוי מתאריך <span dir="ltr">'.with(new DateTime($val['StartFreez']))->format('d/m/Y').'</span> עד תאריך '.with(new DateTime($val['EndFreez']))->format('d/m/Y').' למשך '.$val['FreezDays'].' ימים.<br>'.$val['Reason'].'<br>

        <span style="font-size: 13px;"> עודכן ע"י '.$UserName->display_name.' בתאריך: '.with(new DateTime($val['Dates']))->format('d/m/Y H:i').'<span></div><hr>'; 
        }                                                                  
?>  

<?php } ?>

<script>
$( ".select2General" ).select2( {
		theme:"bootstrap", 
		language: "he",
		allowClear: true,
		width: '100%',
        dir: "rtl" } );    
</script>

<?php endif ?>