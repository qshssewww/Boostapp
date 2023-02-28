<?php require_once '../../app/initcron.php'; ?>

<?php

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$ClassId = $_REQUEST['ClassId'];
$RowId = $_REQUEST['RowId'];

$ClassRegularInfo = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $ClassId)->first();

$RegularClassRowId = DB::table('classstudio_act')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $RowId)->first();
$UsersName = DB::table('users')->where('id', '=', @$RegularClassRowId->GuideId)->first(); 

$GroupNumber = $ClassRegularInfo->GroupNumber;
$ClientId = $ClassRegularInfo->ClientId;

$ClassInfo = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->ClassId)->first(); 
$FloorInfo = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassRegularInfo->Floor)->first(); 


$ItemspInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$RegularClassRowId->ClientActivitiesId)->first();
$MemberShipInfo = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ItemspInfo->MemberShip)->first();
    
if ($ClassRegularInfo->StatusType=='12'){
$RegularStatus = '';    
}    
else {
$RegularStatus = lang('waiting_card');
}  

if ($ClassRegularInfo->RegularClassType=='1'){
$TypeToken = lang('permanent_single').$RegularStatus;
}
else { 
$TypeToken = lang('date_range').$RegularStatus;
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

if ($ItemspInfo->Department=='2' && $ItemspInfo->TrueBalanceValue<='0' || $ItemspInfo->Department=='3' && $ItemspInfo->TrueBalanceValue<='0'){
$BalanceClassColor = 'text-danger';
}
else {
$BalanceClassColor = '';    
}


if ($ItemspInfo->Department=='1' && $ItemspInfo->TrueDate<=date('Y-m-d') || $ItemspInfo->Department=='2' && $ItemspInfo->TrueDate<=date('Y-m-d') || $ItemspInfo->Department=='3' && $ItemspInfo->TrueDate<=date('Y-m-d')){
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
<div class="col-md-3 <?php echo $BalanceClassColor; ?>">יתרה: <?php echo $ItemspInfo->TrueBalanceValue; ?>/<?php echo $ItemspInfo->BalanceValue; ?></div> <?php } else { ?>
<div class="col-md-3"></div>     
<?php } ?>
<?php if ($ItemspInfo->Department=='1' || $ItemspInfo->Department=='2' && $ItemspInfo->TrueDate!='' || $ItemspInfo->Department=='3' && $ItemspInfo->TrueDate!=''){ ?>     
<div class="col-md-3 <?php echo $DateClassColor; ?>">תוקף: <?php echo with(new DateTime(@$ItemspInfo->TrueDate))->format('d/m/Y'); ?></div> 
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
<?php } ?>

<div class="row">  
<div class="col-md-12"> 
<span style="float:left; padding-top:5px; padding-left:15px;"><a href="javascript:void(0);" id="RegularClassBack" class="btn btn-sm btn-dark text-white">חזור</a></span>         
</div>    
</div>    

<hr>

 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:420px; overflow-y:scroll; overflow-x:hidden;'> 

     
                <input type="hidden" name="ClientId" value="<?php echo $ClassRegularInfo->ClientId; ?>">
                <input type="hidden" name="ClassId" value="<?php echo $ClassId; ?>">
                <input type="hidden" name="RowId" value="<?php echo $RowId; ?>">
                <input type="hidden" name="ClientActivitiesId" value="<?php echo $RegularClassRowId->ClientActivitiesId; ?>"> 
                <input type="hidden" name="OldStatus" value="<?php echo $RegularClassRowId->Status; ?>"> 
     
     
                <div class="row small">
                <div class="col-md-3">יום: <?php echo transDbVal(trim($RegularClassRowId->Day)) ?></div>
                <div class="col-md-3">תאריך: <?php echo with(new DateTime(@$RegularClassRowId->ClassDate))->format('d/m/Y'); ?></div>
                <div class="col-md-3">שעה: <?php echo with(new DateTime(@$RegularClassRowId->ClassStartTime))->format('H:i'); ?></div>    
                <div class="col-md-3">מדריך: <?php echo @$UsersName->display_name; ?></div>    
                </div>

                <hr>
     
               <div class="form-group">
               <label>מנוי לקוח </label>
               <select name="RegularClassMemberShip" class="form-control">
              <?php 
              $MemberShipClients = DB::select('select * from boostapp.client_activities where (CompanyNum = "'.$CompanyNum.'"  AND Department != "4" AND Status = "0" AND FIND_IN_SET("'.$ClassRegularInfo->ClientId.'",TrueClientId) > 0 ) OR (CompanyNum = "'.$CompanyNum.'" AND ClientId = "'.$ClassRegularInfo->ClientId.'" AND Department != "4" AND Status = "0") Order By `CardNumber` DESC ');
              if (!empty($MemberShipClients)){       
              foreach ($MemberShipClients as $MemberShipClient) {   
              if ($MemberShipClient->TrueClientId=='0'){
                $TrueClientText = '';    
                }
                else {
                $TrueClientText = lang('familiy_double');
                }
                  
                $TokefText = '';
                $BalnaceText = ''; 
                  
                if ($MemberShipClient->TrueDate!=''){
                $TokefText = lang('until_double'). with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');
                }      
                
                if ($MemberShipClient->Department=='2' || $MemberShipClient->Department=='3') {
                $BalnaceText = lang('balance_double').$MemberShipClient->TrueBalanceValue;
                }
                  
              ?>    
              <option value="<?php echo $MemberShipClient->id ?>" <?php if ($RegularClassRowId->ClientActivitiesId==$MemberShipClient->id){ echo 'selected'; } else {} ?>><?php echo $MemberShipClient->ItemText; ?> <?php echo $TrueClientText; ?> <?php echo $TokefText; ?> <?php echo $BalnaceText; ?></option>
              <?php } } else { 
              $MemberShipClientSingle = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $RegularClassRowId->ClientActivitiesId)->first(); 
                  
                if ($MemberShipClientSingle->TrueClientId=='0'){
                $TrueClientText = '';    
                }
                else {
                $TrueClientText = lang('familiy_double');
                }  
                  
                $TokefText = '';
                $BalnaceText = ''; 
                  
                if (@$MemberShipClientSingle->TrueDate!=''){
                $TokefText = lang('until_double'). with(new DateTime($MemberShipClientSingle->TrueDate))->format('d/m/Y');
                }      
                
                if (@$MemberShipClientSingle->Department=='2' || @$MemberShipClientSingle->Department=='3') {
                $BalnaceText = lang('balance_double').$MemberShipClientSingle->TrueBalanceValue;
                }   
                  
              ?>	
              <option value="<?php echo $MemberShipClientSingle->id ?>" <?php if ($RegularClassRowId->ClientActivitiesId==$MemberShipClientSingle->id){ echo 'selected'; } else {} ?>><?php echo $MemberShipClientSingle->ItemText; ?> <?php echo $TrueClientText; ?> <?php echo $TokefText; ?> <?php echo $BalnaceText; ?></option>      
              <?php } ?>     
              </select>  
              </div> 
     
     
              <div class="form-group">
              <label><?php echo lang('status_table') ?> </label>
              <select name="RegularClassStatus" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose_status') ?>"  >
              <?php 
              $ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->whereIn('id', array(9,3,12))->orderBy('id', 'ASC')->get();  
              foreach ($ClassStatusInfos as $ClassStatusInfo) {    
              ?>    
              <option value="<?php echo $ClassStatusInfo->id ?>" <?php if ($RegularClassRowId->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?>><?php echo transDbVal(trim($ClassStatusInfo->Title)) ?></option>
             <?php } ?>	      
                  
              </select>  
              </div>
     
              <div class="form-group">
              <label><?php echo lang('q_edit_as_group') ?> </label>
              <select name="EditRegularClassAll" id="EditRegularClassAll" class="form-control">
               <option value="0" selected><?php echo lang('edit_only_one_class') ?></option>
               <option value="1"><?php echo lang('edit_all_classes_onwards') ?></option>
               </select>  
               </div>     
     
               <div id="DivEditRegularClass" style="display: none;">
                   
                <div class="form-group" dir="rtl">
                <label><?php echo lang('edit_date_not_req') ?></label>
                <input type="date" name="TillDate" class="form-control" value="">
                </div>
                </div>     
     
               </div>
     
</div>


<div class="alertb alert-warning">
    <div><?= lang('reg_booking_notice_1') ?></div>
    <div><?= lang('reg_booking_notice_2') ?></div>
</div>


<script>
       
$("#RegularClassBack").click(function(){
        
   document.getElementById("resultRegularClass").innerHTML=" ";
   $('#resultRegularClass').load('/office/action/updateRegularClass.php?ClassId=<?php echo $ClassId; ?>'); 
   $('#ShowSaveRegularClass').hide(); 
   var modal = $('#RegularClassPopup');    
   modal.find('.alert').hide();    
 });    
 
    
$("#EditRegularClassAll").change( function()
{

var Id = $(this).val();

if (Id=='0') {
$('#DivEditRegularClass').hide();   
}    
else {
$('#DivEditRegularClass').show();    
}    
    
}
);      
    
</script>


