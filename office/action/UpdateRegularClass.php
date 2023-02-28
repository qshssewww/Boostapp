<?php require_once '../../app/initcron.php'; ?>

<?php

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$ClassId = $_REQUEST['ClassId'];

$ClassRegularInfo = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $ClassId)->first();

$GroupNumber = $ClassRegularInfo->GroupNumber;
$ClientId = $ClassRegularInfo->ClientId;

$ClassInfo = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->ClassId)->first(); 
$FloorInfo = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->Floor)->first(); 


$ItemspInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->ClientActivitiesId)->first();
if (!isset($ItemspInfo->id)) {
    $ItemspInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->ClientActivitiesId)->first();
}
$MemberShipInfo = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ItemspInfo->MemberShip)->first();

if ($ClassRegularInfo->StatusType=='12'){
$RegularStatus = '';    
}    
else {
$RegularStatus = lang('waiting_card');
}  


if ($ClassRegularInfo->RegularClassType=='1'){
$TypeToken = lang('permanent_single').' '.$RegularStatus;
}
else {
$TypeToken = lang('date_range').' '.$RegularStatus;
} 

if ($ItemspInfo->Department=='1') {
$FixDepartment = lang('cycle_membership');
}
else if ($ItemspInfo->Department=='2'){
$FixDepartment = lang('class_tabe_card');
}
else if ($ItemspInfo->Department=='3'){
$FixDepartment = lang('a_trial');
}

if (($ItemspInfo->Department == '2' && $ItemspInfo->TrueBalanceValue <= '0') || ($ItemspInfo->Department == '3' && $ItemspInfo->TrueBalanceValue <= '0')){
$BalanceClassColor = 'text-danger';
}
else {
$BalanceClassColor = '';    
}


if (($ItemspInfo->Department == '1' && $ItemspInfo->TrueDate <= date('Y-m-d')) || ($ItemspInfo->Department == '2' && $ItemspInfo->TrueDate <= date('Y-m-d')) ||
    ($ItemspInfo->Department == '3' && $ItemspInfo->TrueDate <= date('Y-m-d'))) {
$DateClassColor = 'text-danger';
}
else {
$DateClassColor = '';    
}

?> 
       <style>
       
           .DivScroll::-webkit-scrollbar {
             width: 5px;
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

<div class="row small">

<div class="col-md-3"><?php echo lang('class_single') ?>: <?php echo @$ClassInfo->Type; ?></div>
<div class="col-md-3"><?php echo lang('location') ?>: <?php echo @$FloorInfo->Title; ?></div>
<div class="col-md-3"><?php echo lang('membership') ?>: <?php echo @$MemberShipInfo->Type; ?></div>
<div class="col-md-3"><?php echo lang('customer_card_schedule_type') ?>: <?php echo @$TypeToken; ?></div>

</div>
<hr>
<div class="row small">
<div class="col-md-3"><?php echo lang('item_single') ?>: <?php echo $ItemspInfo->ItemText; ?></div>
<div class="col-md-3"><?php echo lang('class') ?>: <?php echo $FixDepartment; ?></div>
                 
<?php if ($ItemspInfo->Department=='2' || $ItemspInfo->Department=='3'){ ?>    
<div class="col-md-3 <?php echo $BalanceClassColor; ?>"><?php echo lang('punch_card_balance') ?>: <?php echo $ItemspInfo->TrueBalanceValue; ?>/<?php echo $ItemspInfo->BalanceValue; ?></div> <?php } else { ?>
<div class="col-md-3"></div>     
<?php } ?>
<?php if ($ItemspInfo->Department=='1' || ($ItemspInfo->Department == '2' && $ItemspInfo->TrueDate != '') || ($ItemspInfo->Department == '3' && $ItemspInfo->TrueDate != '')){ ?>
<div class="col-md-3 <?php echo $DateClassColor; ?>"><?php echo lang('expires_at') ?>: <?php echo with(new DateTime(@$ItemspInfo->TrueDate))->format('d/m/Y'); ?></div>
 <?php } else { ?>
<div class="col-md-3"></div>     
<?php } ?>    
</div>

<hr>

<?php if ($ClassRegularInfo->RegularClassType=='2') { ?>
<div class="row small">  
<div class="col-md-3"><?php echo lang('from_date') ?>: <?php echo with(new DateTime(@$ClassRegularInfo->StartDate))->format('d/m/Y'); ?></div>
<div class="col-md-3"><?php echo lang('until_date') ?>: <?php echo with(new DateTime(@$ClassRegularInfo->EndDate))->format('d/m/Y'); ?></div>
<div class="col-md-3"></div>    
<div class="col-md-3"></div>    
</div>
<hr>
<?php } ?>


 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:420px; overflow-y:scroll; overflow-x:hidden;'> 
<table class="table table-hover text-right" style="font-size:12px; font-weight:bold;" dir="rtl" id="Token">
  <thead >
          <tr style="background-color:#bce8f1;">
            <th align="right" style="text-align:right;" width="10%">#</th>
            <th align="right" style="text-align:right;"><?php echo lang('class_date_single') ?></th>
            <th align="right" style="text-align:right;"><?php echo lang('lesson_day') ?></th>
            <th align="right" style="text-align:right;"><?php echo lang('time_of_a_class') ?></th>
            <th align="right" style="text-align:right;"><?php echo lang('instructor') ?></th>
            <th align="right" style="text-align:right;"><?php echo lang('status_table') ?></th>
            <th align="right" style="text-align:right;"><?php echo lang('actions') ?></th>
          </tr>
        </thead>
<tbody>
  
<?php

$GetRegularInfos = DB::table('classstudio_act')
    ->where('CompanyNum', '=', $CompanyNum)->where('ClassDate', '>=', date('Y-m-d'))->where('RegularClassId', '=' , $ClassId)->orderBy('ClassDate', 'ASC')->get();
$i = '1';
foreach ($GetRegularInfos as $GetRegularInfo) {
    
$UsersName = DB::table('users')->where('id', '=', @$GetRegularInfo->GuideId)->first();
$StatusInfo = DB::table('class_status')->where('id', '=', @$GetRegularInfo->Status)->first();
$ClassDate = DB::table('classstudio_date')->select('start_date', 'Day')->where('id' , '=' ,$GetRegularInfo->ClassId)->first();
    ?>
    
    <tr>
    <td><?php echo $i; ?></td>    
    <td><?php echo with(new DateTime(@$ClassDate->start_date))->format('d/m/Y'); ?></td>
    <td><?php echo transDbVal(trim($ClassDate->Day)) ?></td>
    <td><?php echo with(new DateTime(@$ClassDate->start_date))->format('H:i'); ?></td>
    <td><?php echo @$UsersName->display_name; ?></td>
    <td><?php echo transDbVal(trim(@$StatusInfo->Title)); ?></td>
    <td><a href="javascript:void(0);" class="EditRegularClass" data-rowid="<?php echo $GetRegularInfo->id; ?>" data-classid="<?php echo $ClassId; ?>" ><?php echo lang('edit_booking_update') ?></a></td>
    
    </tr>
    
 <?php ++$i; } ?>   
    
    </tbody>
    
    </table>
</div>

<script>
       
$(".EditRegularClass").click(function(){

    var ClassId = $(this).data("classid");
    var RowId =  $(this).data("rowid");
        
   document.getElementById("resultRegularClass").innerHTML=" ";
   $('#resultRegularClass').load('/office/action/updateRegularClassEdit.php?ClassId='+ClassId+'&RowId='+RowId);
   $('#ShowSaveRegularClass').show();    
    
 });  
    
</script>


