<?php
require_once '../../app/initcron.php';
require_once '../Classes/ClassStudioDate.php';
?>


 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:320px; overflow-y:scroll; overflow-x:hidden;'>

<table class="table table-bordered table-sm table-responsive-sm">

<thead>
<th style="text-align:right;">#</th>
<th style="text-align:right;"><?= lang('location') ?></th>
<th style="text-align:right;"><?= lang('table_title') ?></th>
<th style="text-align:right;"><?= lang('day') ?></th>
<th style="text-align:right;"><?= lang('date') ?></th>
<th style="text-align:right;"><?= lang('hour') ?></th>
<th style="text-align:right;"><?= lang('instructor') ?></th>
<th style="text-align:right;"><?= lang('status_table') ?></th>
</thead>

<tbody>

<?php 
$ItemId = $_REQUEST['ItemId'];
$Act = $_REQUEST['Act'];    
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ItemId)->first();
$i = '1';  
 
if ($Act=='0'){    
$ClassLogs = DB::table('classstudio_act')->where('ClientActivitiesId', '=', $ItemId)->where('CompanyNum', $CompanyNum)->where('Status', '!=', '12')->orderBy('ClassDate','DESC')->get(); 
}
else {
$ClassLogs = DB::table('classstudio_act')->where('ClientActivitiesId', '=', $ItemId)->where('CompanyNum', $CompanyNum)->whereIn('Status', array(1, 2, 4,6,8,11,15,21))->orderBy('ClassDate','DESC')->get();     
}    
    
foreach ($ClassLogs as $ClassLog) {

    $classStudioDateInfo = ClassStudioDate::find($ClassLog->ClassId);

$StatusInfoColor = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();  
    
$FloorInfo = DB::table('sections')->where('id', '=', $ClassLog->FloorId)->where('CompanyNum', '=', $CompanyNum)->first();  
$GuideInfo = DB::table('users')->where('id', '=', $ClassLog->GuideId)->first();
$FloorName = $FloorInfo->Title ?? '';
$GuideName = $GuideInfo->display_name ?? '';
$class_day = $classStudioDateInfo->Day ? transDbVal(trim($classStudioDateInfo->Day)) : transDbVal(trim($ClassLog->Day));
$class_name = $classStudioDateInfo->ClassName ?? $ClassLog->ClassName;
$start_date = $classStudioDateInfo->StartDate ?? $ClassLog->ClassDate;
$start_time = $classStudioDateInfo->StartTime ?? $ClassLog->ClassStartTime;
//$show = false;
    
?>   
    
    
<tr <?php echo $StatusInfoColor->Color; ?> >
    <td class="align-middle"><?php echo $i; ?></td>
    <td class="align-middle"><?php echo $FloorName; ?></td>
    <td class="align-middle"><?php echo $class_name; ?></td>
    <td class="align-middle"><?php echo $class_day; ?></td>
    <td class="align-middle"><?php echo with(new DateTime($start_date))->format('d/m/Y'); ?></td>
    <td class="align-middle"><?php echo with(new DateTime($start_time))->format('H:i'); ?></td>
    <td class="align-middle"><?php echo $GuideName; ?></td>
    <td class="align-middle">
        <?php if (Auth::userCan('62') && $classStudioDateInfo->meetingTemplateId == null): ?>
            <select name="StatusEvent" id="StatusEvent<?php echo $ClassLog->id ?>" data-placeholder="<?= lang('choose_status') ?>" class="form-control" style="width:100%;" >
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
            if (!$classStudioDateInfo->meetingTemplateId || in_array($ClassLog->Status,[8,3])) {
                $ClassStatusInfo = DB::table('class_status')->where('id', '=', $ClassLog->Status)->first();
                echo transDbVal(trim($ClassStatusInfo->Title));
            } else {
                echo MeetingStatus::name($classStudioDateInfo->meetingStatus);
            }
            ?>

        <?php endif ?>
    </td>
</tr> 
    
<?php if ($ClassLog->ReClass=='2'){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u><?= lang('complete_class') ?>:</u> <?php echo $ClassLog->ReClassReason; ?></td>
</tr>    
<?php } ?>    
    
    
<?php if ($ClassLog->Remarks!=''){?>    
<tr style="font-size: 13px;">
<td colspan="8"><u><?= lang('class_notice') ?>:</u> <?php echo $ClassLog->Remarks; ?></td>
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
<td colspan="8"><u><?= lang('status_log') ?>:</u> <?php echo $StatusLog; ?></td>
</tr>    
<?php } ?>    
  
<?php if ($ClassLog->TrueClientId!='0') { 
$TrueClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassLog->TrueClientId)->first();         
?>  
<tr style="font-size: 13px;">
<td colspan="8"><u><?= lang('family_membersip').' '.lang('booked_by_class_log') ?>:</u> <a href="ClientProfile.php?u=<?php echo @$TrueClientInfo->id; ?>"><?php echo @$TrueClientInfo->CompanyName; ?></a> </td>
</tr>      
<?php } ?>    
    
<tr style="font-size: 13px; display:none;" id="ClientTRClassInsetedDiv<?php echo $ClassLog->id ?>" class="HideDiv">   
<td colspan="8">
   
<form action="AddClientClassInsetedPopUp" class="ajax-form clearfix text-right popup-ajax" autocomplete="off"> 
<div class="form-group">     
<input type="hidden" name="ActivityId" value="<?php echo $ClassLog->id; ?>">    
<input type="hidden" name="ClientId" value="<?php echo $ClassLog->ClientId; ?>">
<?= lang('select_a_class_to_complete') ?>
</div>     
<div class="form-group">     

<select name="ForWhichReClass" data-placeholder="<?= lang('choose_class') ?>" class="form-control select2General" style="width:100%;" >
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
<label><?= lang('add_reason_class_completion') ?></label>
<textarea name="ClientReClassReason" class="form-control" rows="2" ><?php echo $ClassLog->ReClassReason ?></textarea>
</div>     
<div class="form-group">
<button type="submit" name="submit" class="btn btn-dark btn-sm text-white"><?= lang('save_changes_button') ?></button>
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
 
BN('0', lang('action_done_beepos'));
    

}
});
		 
		 
		 
		 
    });      
</script>    
    
    
<?php ++$i; } ?> 
</tbody>
    
    </table> 
     
</div>
 