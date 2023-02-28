<?php require_once '../../app/initcron.php'; 
//if (Auth::userCan('84')):

$Id = $_REQUEST['Id'];
$CompanyNum = Auth::user()->CompanyNum;

$ClassInfo = DB::table('classstudio_date')->where('id','=', $Id)->where('CompanyNum', $CompanyNum)->first();
$Floor = DB::table('sections')->where('id','=', $ClassInfo->Floor)->where('CompanyNum', $CompanyNum)->first();
$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClassInfo->ClassDevice)->where('Status', '=', '0')->first();

$ClassRegularCount = DB::table('classstudio_act')
->where('CompanyNum', '=', $CompanyNum)->where('ClassId', '=', $ClassInfo->id)->where('RegularClass', '=', '1')->whereIn('Status', array(9, 12))
->count();

if ($ClassInfo->ClassMemberType=='BA999'){
$MembershipType = lang('all_membership_types');   
}
else {
$z = '1';
$myArray = explode(',', $ClassInfo->ClassMemberType);	
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


            
 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClassInfo->ClassName ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClassInfo->GuideName ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $Floor->Title ?> 
 </div>   
  <div class="col-md-3">
 <?php if ($ClassInfo->MinClass=='0') { echo lang('without_min_patricipants'); } else { ?>   
  <?php echo lang('min_participants') ?>: <?php echo $ClassInfo->MinClassNum; } ?> 
 </div>  
</div>


 <div class="row">
 <div class="col-md-3">	 
 <?php echo lang('date') ?>: <?php echo with(new DateTime($ClassInfo->StartDate))->format('d/m/Y'); ?> 
 </div> 
  <div class="col-md-3">	 
  <?php echo lang('day') ?>: <?php echo $ClassInfo->Day ?> 
 </div>       
  <div class="col-md-3">	 
  <?php echo lang('class_start') ?> <?php echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?>
 </div>  
  <div class="col-md-3">	 
  <?php echo lang('class_end') ?>: <?php echo with(new DateTime($ClassInfo->EndTime))->format('H:i'); ?> 
 </div>  
</div>

  <hr>            
 <div class="row">
 <div class="col-md-<?php if ($ClassInfo->ClassDevice=='0'){ echo '12'; } else { echo '6'; }?>">	 
 <label><?php echo lang('class_membership_type') ?>:</label>
<?php echo $MembershipType; ?> 
 </div>  
<?php if ($ClassInfo->ClassDevice=='0'){} else {?>     
     <div class="col-md-6">	 
 <label><?php echo lang('class_equipment_type') ?>:</label>
<?php echo @$ClassDeviceName->Name; ?> 
 </div>
<?php } ?>     
</div>
  <hr>            
 <div class="row">
 <div class="col-md-6">	 
 <label><?php echo lang('class_booking_num') ?>:</label>
<span style="font-weight:bold; color:forestgreen"><?php echo $ClassInfo->ClientRegister; ?> <?php echo lang('of_user_manage') ?> <?php echo $ClassInfo->MaxClient; ?> <?php echo lang('registered') ?> (<?php echo $ClassInfo->MaxClient-$ClassInfo->ClientRegister; ?> <?php echo lang('available_spaces') ?>)</span>
 </div>  
     
<div class="col-md-6">	 
<label><?php echo lang('w_list') ?>:</label>
<span style="font-weight:bold; color:orangered"><?php echo $ClassInfo->WatingList; ?><?php echo lang(' class_in_waitlist') ?></span>
 </div>   
     
</div>


<hr>
              
  

    
<div class="alertb alert-danger" id="ClientWatingListText" style="display: none;">	
</div>
   


 <div class="row">
 <div class="col-md-12">	
 <?php echo lang('class_waiting_list') ?>:
     <div class="alertb alert-info">	
     <?php echo lang('sort_waitlist_notice') ?>
</div>   
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
 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:320px; overflow-y:scroll; overflow-x:hidden;'>
<table class="table table-bordered table-sm table-responsive-sm watinglist">

<thead>
<th class="text-start"  >#</th>
<th class="text-start" ><?php echo lang('class_customer_num') ?></th>    
<th class="text-start" ><?php echo lang('class_table_name') ?></th>
<th class="text-start" ><?php echo lang('phone') ?></th>  
</thead>

<tbody id="categories_list" class="ui-sortable" style="cursor: move;">
<?php 
$i= '1';
$Clients = DB::table('classstudio_act')->where('ClassId', '=', $Id)->where('Status', '=', '9')->where('CompanyNum', $CompanyNum)->orderBy('WatingListSort','ASC')->orderBy('id','ASC')->get(); 
foreach ($Clients as $Client) {

if ($Client->TrueClientId!='0'){    
$ClientName = DB::table('client')->where('id', '=', $Client->TrueClientId)->where('CompanyNum', $CompanyNum)->first();
}
else {
$ClientName = DB::table('client')->where('id', '=', $Client->ClientId)->where('CompanyNum', $CompanyNum)->first();    
}    
$StatusInfoColor = DB::table('class_status')->where('id', '=', $Client->Status)->first();    
  
 
?>

<tr <?php echo $StatusInfoColor->Color; ?> id="SortRow_<?php echo $Client->id; ?>">
<td class="align-middle"><?php echo $i; ?></td>    
<td class="align-middle"><?php echo $ClientName->id; ?></td>
<td class="align-middle"><a href="ClientProfile.php?u=<?php echo $ClientName->id; ?>"><span class="text-primary"><?php echo $ClientName->CompanyName; ?></span></a></td>
<td class="align-middle"> <?php echo $ClientName->ContactMobile; ?></td>
   
    
</tr>
    

<?php ++$i;  } ?>
 </tbody>

</table>  

</div>
</div>       


				<div class="ip-modal-footer text-start">
                <div class="ip-actions">
                </div>
                
				<button type="button" class="btn btn-dark text-white ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('close') ?></button>
                </form>
				</div>


<script>
    

$( ".select2General" ).select2( {
		theme:"bootstrap", 
		language: "he",
		allowClear: true,
		width: '100%',
        dir: "rtl" } );       

$(function() {
    
$( "#categories_list" ).sortable( {
    cursor: 'move',
    
    start: function(event, ui) { 
           // console.log('start: ' + ui.item.index())
    },
    
	update: function( event, ui ) {
    
    var order = $("#categories_list").sortable('serialize');   
        
    $(this).children().each(function(index) {
	$(this).find('td').first().html(index + 1)    
    });
           
    
    $.ajax({
          url: "action/UpdateSort.php?ClassId=<?php echo $ClassInfo->id;?>&"+order,
          error: function(data) { 
          BN('1','<?php echo lang('error_oops_something_went_wrong') ?>');
          },
          success: function(data) { 
          
          BN('0','<?php echo lang('action_done') ?>');
          
          }
        });     
        
        
        
  }
}); 
    
});     
    
$( ".watinglist tbody" ).disableSelection();
		   
    
</script>

<?php //endif ?>
