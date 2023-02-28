<?php
require_once '../../app/initcron.php';
require_once '../Classes/ClassStudioDate.php';
require_once '../Classes/ClassStudioAct.php';
require_once '../Classes/Client.php';

if (Auth::userCan('124')): 
$ItemId = $_REQUEST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ItemId)->first();

$ClientInfo = Client::where('id', $Items->ClientId)
    ->where('CompanyNum', $CompanyNum)
    ->first();
// update last class date, if it is not right
$lastClassDate = isset($ClientInfo->Dates) ? ClassStudioAct::getLastActiveClass($ClientInfo->id, date('Y-m-d', strtotime($ClientInfo->Dates))) : null;
if ($lastClassDate && $lastClassDate != $ClientInfo->LastClassDate) {
    $ClientInfo->LastClassDate = $lastClassDate;
    $ClientInfo->save();
}

if ($Items->TrueClientId=='0'){
$TrueClientIcon = '';    
}   
else {
$TrueClientIcon = '<i class="fas fa-user-friends" title="'.lang('family_membersip').'"></i>';
}   


 $GetClientClassCounts = DB::table('classstudio_act')->where('ClientId' ,'=', $Items->ClientId)->where('CompanyNum' ,'=', $CompanyNum)->where('StatusCount' ,'=', '0')->where('Status' ,'!=', '12')->where('ClassDate' ,'>', date('Y-m-d'))->count();  

?>

 <div class="row">
 <div class="col-md-12">	 
<span class="text-center font-weight-bold"><?php echo $ClientInfo->CompanyName; ?></span>
 </div> 
<div class="col-md-6">	 
<span class="text-center font-weight-bold"><?php echo $TrueClientIcon; ?> <?php echo $Items->ItemText; ?> // <?php echo lang('membership_number_activity') ?> <?php echo $Items->CardNumber; ?></span>
 </div> 
<div class="col-md-6">	 
<span class="text-center font-weight-bold"><?php echo lang('future_classes_membership_app') ?> <?php echo @$GetClientClassCounts; ?></span>
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
 <label><?php echo lang('expires_at') ?>:</label>
<?php echo with(new DateTime($Items->TrueDate))->format('d/m/Y'); ?>
 </div> 
<?php } if ($Items->Department=='2'){ ?>   
<div class="col-md-<?php if ($Items->TrueDate!=''){ echo '6'; } else { echo '12'; }?>">
<label><?php echo lang('classes') ?>:</label>
<span id="ClientTRDiv_Card" dir="ltr"><?php echo $Items->TrueBalanceValue; ?> / <?php echo $Items->BalanceValue; ?></span>
</div>  
<?php } ?>
     
</div>

<div class="row">

    
<div class="col-md-9">
</div>    
<div class="col-md-3 text-left">	 
<select id="ShowStatusBalance" class="form-control form-control-sm">
  <option value="All"><?php echo lang('display_all') ?>
  <option value="Nikov" selected><?php echo lang('show_only_card_punch') ?>
</select>
 </div>     
</div>
<hr>

 <div class="row" id="mainBody">
 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:320px; overflow-y:scroll; overflow-x:hidden;'>

<table class="table table-bordered table-sm table-responsive-sm">

<thead>
<th style="text-align:right;">#</th>
<th style="text-align:right;"><?php echo lang('location') ?></th>
<th style="text-align:right;"><?php echo lang('task_title') ?></th>
<th style="text-align:right;"><?php echo lang('day') ?></th>
<th style="text-align:right;"><?php echo lang('date') ?></th>
<th style="text-align:right;"><?php echo lang('hour') ?></th>
<th style="text-align:right;"><?php echo lang('instructor') ?></th>
<th style="text-align:right;"><?php echo lang('status_table_search') ?></th>
</thead>

<tbody>

<?php 
$i = '1';    
$ClassLogs = DB::table('classstudio_act')
    ->where('ClientActivitiesId', '=', $ItemId)
    ->where('CompanyNum', $CompanyNum)
    ->where('Status', '!=', '12')
    ->orderBy('ClassDate','DESC')
    ->get();

foreach ($ClassLogs as $ClassLog) {

    $classStudioDateInfo = ClassStudioDate::find($ClassLog->ClassId);

$StatusInfoColor = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();  
    
$FloorInfo = DB::table('sections')->where('id', '=', $ClassLog->FloorId)->where('CompanyNum', '=', $CompanyNum)->first();  
$GuideInfo = DB::table('users')->where('id', '=', $ClassLog->GuideId)->first();
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
    <td class="align-middle"><?php echo @$GuideName; ?></td>
    <td class="align-middle">
        <?php if (Auth::userCan('62')): ?>
            <select name="StatusEvent" id="StatusEvent<?php echo $ClassLog->id ?>" data-placeholder="<?php echo lang('choose_status') ?>" class="form-control" style="width:100%;" >
                <?php
                $ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '0')->whereIn('id', [1,2,3,4,7,8,23])->orderBy('id', 'ASC')->get();
                foreach ($ClassStatusInfos as $ClassStatusInfo) {
                    ?>
                    <option value="<?php echo $ClassLog->id ?>:<?php echo $ClassLog->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassLog->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo transDbVal(trim($ClassStatusInfo->Title)) ?></option>
                <?php } ?>

                <?php
                $ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('id', '=', $ClassLog->Status)->whereNotIn('id', [1,2,3,4,7,8,23])->orderBy('id', 'ASC')->get();
                foreach ($ClassStatusInfos as $ClassStatusInfo) {
                    ?>
                    <option value="<?php echo $ClassLog->id ?>:<?php echo $ClassLog->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassLog->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo transDbVal(trim($ClassStatusInfo->Title)) ?></option>
                <?php } ?>



            </select>
        <?php else:
            $ClassStatusInfo = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();
            echo transDbVal(trim($ClassStatusInfo->Title));
            ?>

        <?php endif ?>
    </td>
</tr> 
    
<?php if ($ClassLog->ReClass=='2'){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u><?php echo lang('complete_class') ?>:</u> <?php echo $ClassLog->ReClassReason; ?></td>
</tr>    
<?php } ?>    
    
    
<?php if ($ClassLog->Remarks!=''){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u><?php echo lang('class_notice') ?>:</u> <?php echo $ClassLog->Remarks; ?></td>
</tr>    
<?php } ?>
    
<?php if ($ClassLog->StatusJson!=''){
$StatusLog = '';
$Loops =  json_decode($ClassLog->StatusJson,true);	
foreach($Loops['data'] as $key=>$val){ 

$DatesDB = $val['Dates'];
$UserIdDB = $val['UserId'];
$StatusDB = $val['Status']; 
$StatusTitleDB = transDbVal(trim($val['StatusTitle']));
$UserNameDB = $val['UserName'];     

$StatusLog .= with(new DateTime($DatesDB))->format('d/m/Y H:i').' <u>'.$StatusTitleDB.'</u> '.$UserNameDB.', ';     
  
}      
  
$StatusLog = rtrim($StatusLog, ', ');    
    
?>    
<tr style="font-size: 13px;">
<td colspan="8"><u><?php echo lang('status_log') ?>:</u> <?php echo $StatusLog; ?></td>
</tr>    
<?php } ?>    
  
<?php if ($ClassLog->TrueClientId!='0') { 
$TrueClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassLog->TrueClientId)->first();         
?>  
<tr style="font-size: 13px;">
<td colspan="8"><u><?php echo lang('booked_by_family') ?></u> <a href="ClientProfile.php?u=<?php echo @$TrueClientInfo->id; ?>"><?php echo @$TrueClientInfo->CompanyName; ?></a> </td>
</tr>      
<?php } ?>    
    
<tr style="font-size: 13px; display:none;" id="ClientTRClassInsetedDiv<?php echo $ClassLog->id ?>" class="HideDiv">   
<td colspan="8">
   
<form action="AddClientClassInsetedPopUp" class="ajax-form clearfix text-right popup-ajax" autocomplete="off"> 
<div class="form-group">     
<input type="hidden" name="ActivityId" value="<?php echo $ClassLog->id; ?>">    
<input type="hidden" name="ClientId" value="<?php echo $ClassLog->ClientId; ?>">
    <?php echo lang('select_a_class_to_complete') ?>
</div>     
<div class="form-group">     

<select name="ForWhichReClass" data-placeholder="<?php echo lang('choose_class') ?>" class="form-control select2General" style="width:100%;" >
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
<label><?php echo lang('class_fullfill_reason') ?></label>
<textarea name="ClientReClassReason" class="form-control" rows="2" dir="rtl"><?php echo $ClassLog->ReClassReason ?></textarea>    
</div>     
<div class="form-group">
<button type="submit" name="submit" class="btn btn-dark btn-sm text-white"><?php echo lang('save_changes_button') ?></button>
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
 
BN('0',lang('action_done_beepos'));
    

}
});
		 
		 
		 
		 
    });      
</script>    
    
    
<?php ++$i; } ?> 

</table> 
     
</div>
</div>     


<?php if ($Items->Status=='2'){ ?>
<span class="text-center font-weight-bold"><?php echo lang('subscription_cancel_reason') ?></span><br>

<?php echo $Items->Reason; ?> <?php if ($Items->CancelDate!=''){ echo with(new DateTime($Items->CancelDate))->format('d/m/Y H:i'); }  ?>
<br>
<?php } ?>


<?php if (($Items->Department=='2' || $Items->Department=='3') && $Items->BalanceValueLog!=''){ ?>
<span class="text-center font-weight-bold">לוג פעולות</span>
<?php

        $Loops =  json_decode($Items->BalanceValueLog,true);
        //var_dump($Items->BalanceValueLog);
        foreach($Loops['data'] as $key=>$val){
        $UserName = DB::table('users')->where('id', '=' , $val['UserId'])->first();

        if(empty(@$UserName->display_name)) {
                $BalanceText = lang('were_offset');
                $val['ClassNumber'] = abs($val['ClassNumber']);
                $operation = lang('auto_action_double');
        }
        else {
                $operation = lang('manual_action_double');
                if ($val['ClassNumber']>'0'){
                        $BalanceText = lang('add_single');
                }
                else {
                        $BalanceText = lang('deduction_single');

                }
        }

            

            
        echo $ClassNumber = '<div class="alertb alert-light">'.$operation.' '.$BalanceText.' <span dir="ltr">'.$val['ClassNumber'].'</span> '.lang('classes').'. <br>'.$val['Reason'].'<br>
        <span style="font-size: 13px;"> '.lang('updated_by_action').' '.@$UserName->display_name.' בתאריך: '.with(new DateTime($val['Dates']))->format('d/m/Y H:i').'<span></div><hr>';
        }                                                                  
?>                                                                  


<?php } ?>

<?php
        if (in_array($Items->Department, [1, 2, 3]) && $Items->StudioStartDateLog != '') {
        $flag = true;
        if ($Items->Department == 2) {
            $flag = $Items->TrueDate != '' ? true : false;
        }
        if ($flag) {
    ?>
<span class="text-center font-weight-bold"><?php echo lang('log_start_membership') ?></span>

<?php
        $Loops =  json_decode($Items->StudioStartDateLog,true);	
        foreach($Loops['data'] as $key=>$val){ 
                   
        $UserName = DB::table('users')->where('id', '=' , $val['UserId'])->first();
            
        echo $ClassNumber = '<div class="alertb alert-light">'.lang('date_start_chage_log').' <span dir="ltr">'.with(new DateTime($val['TrueDate']))->format('d/m/Y').' <br>במקום '.with(new DateTime($val['StudioStartDate']))->format('d/m/Y').'</span> <br>'.$val['Reason'].'<br>
        <span style="font-size: 13px;"> '.lang('updated_by_action').' '.@$UserName->display_name.' '.lang('in_date_ajax').' '.with(new DateTime($val['Dates']))->format('d/m/Y H:i').'<span></div><hr>';
        }
        }
 } ?>

<?php
    if (in_array($Items->Department, [1, 2, 3]) && $Items->StudioVaildDateLog != '') {
        $flag = true;
        if ($Items->Department == 2) {
            $flag = $Items->TrueDate != '' ? true : false;
        }
        if ($flag) {
    ?>
<span class="text-center font-weight-bold"><?php echo lang('end_date_log_subscription') ?></span>

<?php
            $Loops = json_decode($Items->StudioVaildDateLog, true);
            foreach ($Loops['data'] as $key => $val) {
                $UserName = DB::table('users')->where('id', '=', $val['UserId'])->first();

                echo $ClassNumber = '<div class="alertb alert-light">'.lang('change_valid_date_client').' <span dir="ltr">' . with(
                        new DateTime($val['TrueDate'])
                    )->format('d/m/Y') . '</span> <br>' . $val['Reason'] . '<br>
        <span style="font-size: 13px;"> '.lang('updated_by_action').' ' . @$UserName->display_name . ' '.lang('in_date_ajax').' ' . with(
                        new DateTime($val['Dates'])
                    )->format('d/m/Y H:i') . '<span></div><hr>';
            }
        }
}
        if (in_array($Items->Department, [1, 2, 3]) && $Items->FreezLog!='') {
        $flag = true;
        if ($Items->Department == 2) {
            $flag = $Items->TrueDate != '' ? true : false;
        }
        if ($flag) {
    ?>
<span class="text-center font-weight-bold"><?php echo lang('freeze_log_membership') ?></span>

<?php
            $Loops = json_decode($Items->FreezLog, true);
            foreach ($Loops['data'] as $key => $val) {
                $UserName = DB::table('users')->where('id', '=', $val['UserId'])->first();

                echo $ClassNumber = '<div class="alertb alert-light">'.lang('start_date_freeze').' <span dir="ltr">' . with(
                        new DateTime($val['StartFreez'])
                    )->format('d/m/Y') . '</span> '.lang('until_date').' ' . with(new DateTime($val['EndFreez']))->format(
                        'd/m/Y'
                    ) . ' '.lang('for_duration').' ' . $val['FreezDays'] . ' '.lang('days').'.<br>' . $val['Reason'] . '<br>

        <span style="font-size: 13px;"> '.lang('updated_by_action').' ' . $UserName->display_name . ' '.lang('in_date_ajax').' ' . with(
                        new DateTime($val['Dates'])
                    )->format('d/m/Y H:i') . '<span></div><hr>';
            }
        }
 } ?>

<script>
$( ".select2General" ).select2( {
		theme:"bootstrap", 
		language: "he",
		allowClear: true,
		width: '100%',
        dir: "rtl" } );  
    
$(document).ready(function(){   
document.getElementById("mainBody").innerHTML=" ";    
$('#mainBody').load('/office/action/nikovim.php?ItemId=<?php echo $ItemId; ?>&Act=1');
});   
    
$("#ShowStatusBalance").change(function () {
var Acts = this.value; 
if(Acts=="Nikov"){
document.getElementById("mainBody").innerHTML=" ";
$('#mainBody').load('/office/action/nikovim.php?ItemId=<?php echo $ItemId; ?>&Act=1');
}
else
{
document.getElementById("mainBody").innerHTML=" ";
$('#mainBody').load('/office/action/nikovim.php?ItemId=<?php echo $ItemId; ?>&Act=0');
}
});    
    
</script>

<?php endif ?>
