<?php require_once '../../app/initcron.php'; 


$Id = $_GET['Id'];
$ClientId = $_GET['ClientId'];

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$PipeInfo = DB::table('pipeline')->where('id' ,'=', $Id)->where('CompanyNum' ,'=', $CompanyNum)->first();


?>


            
 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClientInfo->CompanyName; ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClientInfo->ContactMobile ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $ClientInfo->Email ?> 
 </div>   
  <div class="col-md-3">
      <?php echo lang('intrested_in_pipeline') ?> <?php echo $PipeInfo->ClassInfoNames; ?>
 </div>  
</div>


<hr>

  <div class="alertb alert-light">      
  <div class="row" style="padding-right: 15px;padding-left: 15px;" dir="rtl">
      
      
   <div class="col-md-2 col-sm-12 order-md-3">      
<label><?php echo lang('customer_card_class_title') ?></label>
   </div>
   <div class="col-md col-sm-12 order-md-4">      
<label><?php echo lang('date') ?></label>
   </div>

   <div class="col-md col-sm-12 order-md-5">      
<label><?php echo lang('day') ?></label>
   </div>      
  
    <div class="col-md col-sm-12 order-md-6">      
<label><?php echo lang('') ?></label>
   </div>
      
   <div class="col-md col-sm-12 order-md-7">      
<label><?php echo lang('instructor') ?></label>
   </div>       
 <div class="col-md-3 col-sm-12 order-md-8">  
 <label><?php echo lang('status_table') ?></label>
</div> 
</div>      
</div> 


<?php 
 

$ClassHistorys =  DB::table('classstudio_act')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Department', '=', '3')->orderBy('ClassDate','ASC')->orderBy('Status','ASC')->get();     
foreach ($ClassHistorys as $ClassHistory) { 
    
$StatusInfoColor = DB::table('class_status')->where('id', '=', $ClassHistory->Status)->first();     
    
$FloorInfo = DB::table('sections')->where('id', '=', $ClassHistory->FloorId)->where('CompanyNum', '=', $CompanyNum)->first();  
$GuideInfo = DB::table('users')->where('id', '=', $ClassHistory->GuideId)->where('CompanyNum', '=', $CompanyNum)->first();        
$ClassDay = transDbVal(trim($ClassHistory->Day));
$FloorName = @$FloorInfo->Title;
$GuideName = @$GuideInfo->display_name;       
        
?>         
<div class="alertb alert-light">
  <div class="row" style="padding-right: 15px;padding-left: 15px;" dir="rtl">
      
   <div class="col-md-2 col-sm-12 order-md-3">      
<p><?php echo $ClassHistory->ClassName; ?></p>       
   </div>
   <div class="col-md col-sm-12 order-md-4">      
<p><?php echo with(new DateTime($ClassHistory->ClassDate))->format('d/m/Y'); ?></p>       
   </div>

   <div class="col-md col-sm-12 order-md-5">      
<p><?php echo $ClassDay; ?></p>       
   </div>      
  
    <div class="col-md col-sm-12 order-md-6">      
<p><?php echo with(new DateTime($ClassHistory->ClassStartTime))->format('H:i'); ?></p>        
   </div>
      
   <div class="col-md col-sm-12 order-md-7">      
<p><?php echo $GuideName; ?></p>       
   </div>       
 <div class="col-md-3 col-sm-12 order-md-8">  
     
<?php //if (Auth::userCan('120')): ?><!--     -->
    
<select name="StatusEvent" id="StatusEvent<?php echo $ClassHistory->id ?>" data-placeholder="<?php echo lang('choose_status') ?>" class="form-control" style="width:100%;" >
<?php 
$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '0')->orderBy('id', 'ASC')->get();  
foreach ($ClassStatusInfos as $ClassStatusInfo) {    
?>    
	
<option value="<?php echo $ClassHistory->id ?>:<?php echo $ClassHistory->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassHistory->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?> <?php if ($ClassHistory->WatingListSort=='1' && $ClassHistory->Status=='9'){ echo lang('first'); } else {} ?></option>
	
<?php } ?>
	
<?php 
$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '1')->where('id', '=', $ClassHistory->Status)->orderBy('id', 'ASC')->get();  
foreach ($ClassStatusInfos as $ClassStatusInfo) {    
?>    
	
<option value="<?php echo $ClassHistory->id ?>:<?php echo $ClassHistory->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($ClassHistory->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?> <?php if ($ClassHistory->WatingListSort=='1' && $ClassHistory->Status=='9'){ echo lang('first'); } else {} ?></option>
	
<?php } ?>	
	
	
	
</select>

<?php //else: ?><!--      -->
<!--     -->
<!--<p --><?php //echo $StatusInfoColor->Color ?><!-- --><?php //echo $StatusInfoColor->Title ?><!--</p>   -->
<!--     -->
<?php //endif; ?><!--    -->

</div> 
      
</div>    
</div> 
        
<hr> 


<script>
$("#StatusEvent<?php echo $ClassHistory->id ?>").change(function () {
var Acts = this.value;    
$.ajax({
type: 'POST',  
data:'Act='+ Acts,
dataType: 'json',    
url:'new/StatusChange.php',     
success: function(data){}
});
		 
		 
		 
		 
    });      
    
</script>    



<?php } ?> 

            
 <form action="AddClientAddClass" class="ajax-form text-right <?php if(isset($_GET['noReload']) && $_GET['noReload']) echo "js-no-reload" ?>" dir="rtl" autocomplete="off">
<input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
<input type="hidden" name="TrueClientId" value="0">     
    
     
 <div class="form-group">
<label><?php echo lang('customer_card_schedule_type') ?></label>
  <select class="form-control" name="ClientAddClassType" id="ClientAddClassType">
    <option value="1"><?php echo lang('schedule_single') ?></option>
  </select>
</div> 
     
     
 <div class="form-group">
<label><?php echo lang('customer_card_choose_class') ?></label>
<select name="ClientAddClassId" data-placeholder="<?php echo lang('customer_card_choose_class') ?>" class="form-control ClientAddClassId" style="width:100%;" >
<option value=""></option>    

</select>
</div>       

<div class="alertb alert-info" id="DivClientAddClassType1_1"><?php echo lang('customer_card_embed_notice') ?><br>
    <?php echo lang('date_format') ?>
</div>       
     
     
<div id="ClientAddClassActivites">
    
</div>       
     
     
<div class="ip-modal-footer text-left">
<button type="button" class="btn btn-danger text-white ip-close" data-dismiss='modal'><?php echo lang('close') ?></button>
</form>
</div>

<script>
    
$(document).ready(function(){	
    
var ClassId = $('.ClientAddClassId').children('option:selected').val();

$( ".ClientAddClassId" ).select2( {
        
		theme:"bootstrap", 
		placeholder: "<?php echo lang('search_class') ?>",
		language: "he",
		allowClear: false,
		width: '100%',
        ajax: {
                url: 'SearchClass.php',
                dataType: 'json',
                type: 'GET',
                cache: true,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        ClassId: $('#ClientAddClassType').children('option:selected').val(),
                    };
                },
        },
		minimumInputLength: 3,
        dir: "rtl" } );    
    

});

 $('.ClientAddClassId').on('change',function(){

  var ClassId = $(this).children('option:selected').val();  
  var ClientId = '<?php echo $ClientId; ?>';
    
  if ($('.ClientAddClassId option:selected').length > 0 ||  ClassId!=null) {
  var urls= 'action/ClientActivityMemberShip.php?ClientId='+ClientId+'&ClassId='+ClassId;
  $('#ClientAddClassActivites').load(urls,function(){     
  return false;    
  });
}
else {
 $( "#ClientAddClassActivites" ).empty();    
}                               

}); 

</script>  