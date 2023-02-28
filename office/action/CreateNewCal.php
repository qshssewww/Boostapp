<?php
require_once '../../app/initcron.php';
require_once '../Classes/CalType.php';
$CompanyNum = Auth::user()->CompanyNum;

$ActId = @$_REQUEST['Id'];
$ClientId = @$_REQUEST['ClientId'];
$PipeLineId = @$_REQUEST['PipeId'];

if ($PipeLineId==''){
$PipeLineId = '0';    
}

$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$BrandsMain = $SettingsInfo->BrandsMain;

?>

<script src='new/codebase/dhtmlxscheduler.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_limit.js" type="text/javascript" charset="utf-8"></script>
<script src='new/codebase/locale/locale_he.js' type="text/javascript" charset="utf-8"></script>
<script src='new/codebase/ext/dhtmlxscheduler_readonly.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_collision.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_minical.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_multiselect.js"></script>
<link rel="stylesheet" type="text/css" href="new/common/dhtmlxMenu/skins/dhtmlxmenu_dhx_web.css">
<script src="new/common/dhtmlxMenu/dhtmlxmenu.js"></script>
<script src="new/common/dhtmlxMenu/ext/dhtmlxmenu_ext.js"></script>
<script  src="new/data/dhtmlxdataprocessor.js"></script>
<script src="https://momentjs.com/downloads/moment.js"></script>
<link rel='stylesheet' type='text/css' href='new/codebase/dhtmlxscheduler.css'>

<style type="text/css" media="screen">

	 
	 .dhx_minical_popup{
		 height:260px;
}
	 
.dhx_cal_event.multisection div{
			background-color: inherit;
		}

.dhx_cal_event div.dhx_footer,
		.dhx_cal_event.past_event div.dhx_footer,
		.dhx_cal_event.event_english div.dhx_footer,
		.dhx_cal_event.event_math div.dhx_footer,
		.dhx_cal_event.event_science div.dhx_footer{
			background-color: transparent !important;
		}
		.dhx_cal_event .dhx_body{
			-webkit-transition: opacity 0.1s;
			transition: opacity 0.1s;
			opacity: 0.7;
		}
		.dhx_cal_event .dhx_title{
			line-height: 12px;
		}
		.dhx_cal_event_line:hover,
		.dhx_cal_event:hover .dhx_body,
		.dhx_cal_event.selected .dhx_body,
		.dhx_cal_event.dhx_cal_select_menu .dhx_body{
			opacity: 1;
		}

		.dhx_cal_event.event_2 div, .dhx_cal_event_line.event_2{
			background-color: orange !important;
			height:20px;
		}
		.dhx_cal_event_clear.event_2{
			color:#36BD14 !important;
			height:20px;
		}


	
		
	</style>
<script type="text/javascript" charset="utf-8">
    
    
$(document).ready( function ()
{
	
	init();

});


	
function init() {

    
///// יומן נוסף יומי    
    
        scheduler2 = Scheduler.getSchedulerInstance();
		scheduler2.config.xml_date="%Y-%m-%d %H:%i";
		scheduler2.config.default_date = "%l %j %F, %Y";	
		scheduler2.config.first_hour = 6;
        scheduler2.config.last_hour = 24;
		scheduler2.config.full_day = true;
		scheduler2.config.mark_now = true;	
        scheduler2.config.start_on_monday = false;	
		scheduler2.config.scroll_hour = new Date().getHours();	

						
scheduler2.attachEvent("onBeforeViewChange", function(old_mode,old_date,mode,date){
if(old_mode != mode || +old_date != +date){
		/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler2.getState().date);
var EndDate = new Date(scheduler2.getState().date);	

var SetTime =  $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
		
		scheduler2.deleteMarkedTimespan();
		scheduler2.addMarkedTimespan({
			start_date:new Date(StratDate),
			end_date: new Date(EndDate),
			type:'dhx_time_block'
		});
	}
	return true;
});			
		
			
scheduler2.attachEvent("onTemplatesReady", function(){

scheduler2.templates.event_text = function(start,end,ev){

	
	
	if (ev.Level=='0') {
				var StarIcon = " <i  class='fas fa-star' aria-hidden='true'></i> ";

	} else if (ev.Level=='1') {
				var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
    }	
	else if (ev.Level=='2') {
				var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
			}	
	
	
		
	if (ev.Type=='1') {
				var TypeIcon = " <i class='fas fa-phone' aria-hidden='true'></i> ";

	} else if (ev.Type=='2') {
				var TypeIcon = " <i class='fas fa-users' aria-hidden='true'></i> ";
    }	
	else if (ev.Type=='3') {
				var TypeIcon = " <i class='fas fa-thumbtack' aria-hidden='true'></i> ";
	}	
	else if (ev.Type=='4') {
				var TypeIcon = " <i class='fas fa-flag-checkered' aria-hidden='true'></i> ";
	}	
	else if (ev.Type=='5') {
				var TypeIcon = " <i class='fas fa-comment-alt' aria-hidden='true'></i> ";
	}	
	else if (ev.Type=='6') {
				var TypeIcon = " <i class='fas fa-utensils' aria-hidden='true'></i> ";
	}	
	
	if (ev.FloorName=='') {
				var FloorName = "";

	} else {
				var FloorName = ev.FloorName+' :: '+ev.GuideName;
    }	
    
 return '<div style="margin-top: -4px;"><div><span style="float:right;font-size:12px;font-weight:bold;text-align: right;">' +ev.text+ '</span></div><br><div style="padding-top: 2px;"><span style="float:right;font-size:11px;font-weight:bold;text-align: right;">'+FloorName+' ' + StarIcon + ' ' + TypeIcon + '</span></div></div>';

	
};


}); 
    
       scheduler2.config.readonly = true;

		scheduler2.init("scheduler_here_2",new Date(),"day");
		scheduler2.load("new/data/events.php");
		   

	}
	
  
    
    
$('#ChooseAgentForTask').val('<?php echo Auth::user()->id; ?>').trigger('change');    
$("#SendStudioOption").val(['<?php echo Auth::user()->role_id; ?>']).trigger("change");	    
    
<?php
if ($ActId!='undefined'){
$Act = '1';    
?>
var id = '<?php echo $ActId; ?>'; 
var ClientId = '<?php echo $ClientId; ?>'; 
        
  $('#AddEditTaskCalendarId').val(id);   
            
    $.ajax({
	url:'action/GetCalendarInfo.php?Id='+id+'&ClientId='+ClientId,
    dataType : 'json',
					
    success  : function (response) {
   ClientDiv.style.display = "none";
   $('#ChooseFloorForTask').val(response.Floor).trigger('change');
   $('#CalTaskTitle').val(response.Title);
   $('#AddEditTaskClientId').val(response.ClientId);
   $('#ClientName').val(response.ClientName);  
   $('#ClientPhone').html('<i class="fas fa-phone-square fa-fw"></i> '+response.ClientPhone+' ');     
   $('#AddEditTaskPipeLineId').val(response.PipeLineId);
   
   ClientNameDiv.style.display = "block";       
   $('#CalTypeOption').val(response.Type);
   $('#SetDate').val(response.StartDate);
   $('#SetTime').val(response.StartTime);
   var FixToTimes = moment(response.StartTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;               
   $('#SetToTime').prop('min', FixToTimes);         
   $('#SetToTime').val(response.EndTime).trigger('change');
   $('#CalLevel').val(response.Level);
   $('#ChooseAgentForTask').val(response.AgentId).trigger('change');
   $('#CalRemarks').val(response.Content);
   $('#CalTaskStatus').val(response.Status);
   
    if (response.GroupPermission==null || response.GroupPermission=='' || response.GroupPermission=='(NULL)') {
	$("#SendStudioOption").val(['<?php echo Auth::user()->role_id; ?>']).trigger("change");	
	} else {
	var values = response.GroupPermission;
    var selectedValues = values.split(",");
	$("#SendStudioOption").val(selectedValues).trigger("change");
	}    
        
        
    
       }
}); 
  
<?php 
} else {
$Act= '2';    
} 
?>
    
    
    
    
    
</script> 


<div class="row">	   
<div class="col-md-6 col-sm-12 order-1">	  
<input type="hidden" id="CalPage" value="1">    
<input type="hidden" name="ClientId" id="AddEditTaskClientId" value="<?php echo $ClientId; ?>">
<input type="hidden" name="PipeLineId" id="AddEditTaskPipeLineId" value="<?php echo $PipeLineId; ?>"> 
<input type="hidden" name="CalendarId" id="AddEditTaskCalendarId" value=""> 
<input type="hidden" name="ClientForTask" value="0">     


<div class="form-group" id="ClientNameDiv" style="display: none;">
<?php if (@$ClientId!='0'){ ?>
<label><?php echo lang('client') ?></label>    
<div class="row">
 <div class="col-sm-7">      
  <input type="text" class="form-control" id="ClientName" disabled>
    </div>  
   <div class="col-sm-5">    
  <span class="input-group-text" id="ClientPhone"></span>       
  </div> 
  </div>    
<?php } ?>   
  </div>      
     
<div class="form-group" id="ClientDiv">
<?php if (@$ClientId!='0'){} else { ?>       
  <label><?php echo lang('client') ?></label>
    <select class="form-control select2ClientDesk" id="ChooseClientForTask" name="ClientForTask" data-placeholder="<?php echo lang('choose_client') ?>" style="width: 100%">
	<option value="0"  selected><?php echo lang('without_customer_affiliation_cal') ?></option>	
  </select> 
<?php } ?>    
  </div>            
    
  <div class="form-group" style="display: none;">
  <label><?php echo lang('meeting_room_cal') ?></label>
    <select class="form-control js-example-basic-single text-right select2" id="ChooseFloorForTask" name="FloorId"  data-placeholder="<?php echo lang('select_meeting_room_cal') ?>" style="width: 100%" onChange="UpdateCalView(this.value)">
	<option value="0"  selected><?php echo lang('no_conference_room') ?></option>	
  <?php 
  $SectionInfos = DB::table('sections')->where('CompanyNum', $CompanyNum)->where('Status','=','0')->orderBy('Floor', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Title; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div>  
	 
  <div class="form-group">
  <label><?php echo lang('task_title') ?></label>
	<input type="text" class="form-control" name="TaskTitle" id="CalTaskTitle">  
	</div>

    <div class="form-group">
        <label><?php echo lang('task_type') ?></label>
        <select class="form-control text-right" name="TypeOption" id="CalTypeOption">
            <?php foreach (CalType::getAllActiveByCompanyNum($CompanyNum) as $CalType) { ?>
                <option value="<?php echo $CalType->id; ?>"><?php echo $CalType->Type; ?></option>
            <?php } ?>
        </select>
    </div>

 <div class="form-group">	 
  <div class="form-row"> 
<?php 
function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
   $seconds = strtotime($hour);
   $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
   return date($format, $rounded);
}     
?>
<div class="col">
  <label><?php echo lang('date') ?></label>
  <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
  </div>
    <div class="col">
       <label><?php echo lang('start_hour') ?></label>
  <input name="SetTime" id="SetTime" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
    </div>
	   </div> 
	  </div>  
	
	 <div class="form-group"> 
	  <div class="form-row">  
    <div class="col">
    <label><?php echo lang('end_hour') ?></label>
  <input name="SetToTime" id="SetToTime" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+30 minutes"))); ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
    </div>
	 <div class="col">
    <label><?php echo lang('priority') ?></label>
  <select class="form-control text-right" name="Level" id="CalLevel" >
    <option value="0"><?php echo lang('low_priority_cal') ?></option>
	<option value="1"><?php echo lang('medium_priority_cal') ?></option>
	<option value="2"><?php echo lang('high_priority_cal') ?></option>
	</select>  
    </div>  
	  
 
  </div>
	 </div>  
 <div class="form-group"></div>

    
    
   <div class="form-row">  
     <div class="col">   
  <label><?php echo lang('taking_care_representative') ?></label>
  <select class="form-control js-example-basic-single text-right select2" id="ChooseAgentForTask" name="AgentId"  data-placeholder="<?php echo lang('choose_taking_care_representative') ?>" style="width: 100%">
  <?php 
  if ($BrandsMain=='0'){       
  $UserInfos = DB::table('users')->where('ActiveStatus','=','0')->where('CompanyNum', $CompanyNum)->orderBy('display_name', 'ASC')->get();
  }
  else {
  $UserInfos = DB::table('users')->where('ActiveStatus','=','0')->where('BrandsMain', $BrandsMain)->orderBy('display_name', 'ASC')->get();    
  }      
  foreach ($UserInfos as $UserInfo) {	
  ?>  
  <option value="<?php echo $UserInfo->id; ?>" ><?php echo $UserInfo->display_name; ?></option>	  
  <?php 
		 }
  ?>  
  </select>  
</div> 
       
 <div class="col">   
  <label><?php echo lang('task_auth_group') ?></label>

<select class="form-control js-example-basic-single select2multipleDesk text-right" name="SendStudioOption[]" id="SendStudioOption"   multiple="multiple" data-select2order="true" style="width: 100%;">  
<?php                                                                                 
$SectionInfos = DB::table('roles')->where('CompanyNum','=',$CompanyNum)->get();
foreach ($SectionInfos as $SectionInfo) {	    
?>
<option value="<?php echo $SectionInfo->id; ?>"  ><?php echo $SectionInfo->name; ?></option>
<?php } ?>
      
</select>     
     
     
     
</div>
       
 </div> 	 
 <div class="form-group"></div>	 
  <div class="form-group">
  <label><?php echo lang('contet_single') ?></label>
  <textarea name="Remarks" id="CalRemarks" class="form-control" rows="3" ></textarea>
  </div> 
    <input type="hidden" name="SendMail" value="0">
	

 
	</div>
	
<div class="col-md-6 col-sm-12 order-2" style="margin-top:0px; padding-top: 0px;">

<div class="row" style='width:100%; height:610px; margin: 0px;' >
<style>
    #scheduler_here_2 .dhx_cal_header {
        
        right:50px;
        left: 0px;
    }    
    #scheduler_here_2 .dhx_cal_data dhx_resize_denied dhx_move_denied{
        
         right:0px;
    } 
    #scheduler_here_2 .dhx_scale_holder_now {
         right: 0px;
    }
    #scheduler_here_2 .dhx_scale_holder {
          right: 0px;
    }
    #scheduler_here_2 .dhx_scale_holder{
        float: left;
    }  
</style>	
	
	<div id="scheduler_here_2" class="dhx_cal_container" style="width: 100%; height: 100%; margin-top:0px;">
		<div class="dhx_cal_navline">
			<div class="dhx_cal_date"></div>
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>	

		
	</div>
    
    
	
	</div>	
	
</div>	
	  
	  
</div>	

				<div class="ip-modal-footer px-0 pb-0">
                <div class="ip-actions">
                <button type="submit" name="submit" id="SendCalForm" class="btn btn-primary"><?php _e('main.save_changes') ?></button> 
                                  <div class="form-group" style="padding-left:20px; padding-right:20px; width:200px; float:right;">
                  <select name="TaskStatus" id="CalTaskStatus" class="form-control">
                  <option value="0"><?php echo lang('open_task') ?></option>
                  <option value="1"><?php echo lang('completed_task') ?></option>
                  <option value="2"><?php echo lang('canceled_task') ?></option>
                  </select>
                  </div>      
                </div>  
                <a  class="btn btn-light ip-close" data-dismiss="modal"><?php _e('main.close') ?></a>
				</div>


<script>

 $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl" } );     
$( ".select2ClientDesk" ).select2( {
		theme:"bootstrap", 
		placeholder: "<?php echo lang('search_client') ?>",
		language: "he",
		allowClear: true,
		width: '100%',
     ajax: {
            url: 'SearchClient.php',
            type: 'POST',
            dataType: 'json',
            cache: true
        },
		minimumInputLength: 3,
        dir: "rtl" } );     
    
 $('#SendStudioOption').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl" } );
    }
    else if (selected.indexOf('BA000')>=0){
    $(this).val('BA000').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl" } );         
   }  
  }
    
});     
    
$(document).ready(function() {
    $(".ip-close").click(function(){
    $('#FormCalendarClient').trigger("reset");
    scheduler2.clearAll();	
    scheduler2.setCurrentView(<?php echo date('Y-m-d') ?>);   
    scheduler2.load("new/data/events.php");
    ClientDiv.style.display = "block";
    ClientNameDiv.style.display = "none";     
   //$('#CalTaskStatus').prop('disabled', true);   
    }); 
});    
     
    
    
$('#SetDate').on('change', function() {	
var FloorId = $("#ChooseFloorForTask option:selected").val();   
scheduler2.clearAll();	
scheduler2.setCurrentView(this.value);	

/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler2.getState().date);
var EndDate = new Date(scheduler2.getState().date);	

var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
if (FloorId=='0'){    
scheduler2.load("new/data/events.php"); 
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
}   
else {    
scheduler2.load("new/data/events.php?FloorId="+FloorId);  
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
} 	

	
				   
});
	
$('#SetTime').on('change', function() {
var FloorId = $("#ChooseFloorForTask option:selected").val();	
var SetDate = $('#SetDate').val();	
scheduler2.clearAll();	
scheduler2.setCurrentView(SetDate);	

/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler2.getState().date);
var EndDate = new Date(scheduler2.getState().date);	

var SetTime = $('#SetTime').val();
var FixToTime = moment(SetTime,'HH:mm:ss').add(30,'minutes').format('HH:mm:ss') ;   
var FixToTimes = moment(SetTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;       
$('#SetToTime').val(FixToTime); 
$('#SetToTime').prop('min', FixToTimes);    
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
	
if (FloorId=='0'){    
scheduler2.load("new/data/events.php"); 
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
}   
else {    
scheduler2.load("new/data/events.php?FloorId="+FloorId);  
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
} 
	
				   
});	
	
$('#SetToTime').on('change', function() {
var SetDate = $('#SetDate').val();	
var FloorId = $("#ChooseFloorForTask option:selected").val();    
scheduler2.clearAll();	
scheduler2.setCurrentView(SetDate);	
/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler2.getState().date);
var EndDate = new Date(scheduler2.getState().date);	

var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
	
if (FloorId=='0'){    
scheduler2.load("new/data/events.php"); 
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
}   
else {    
scheduler2.load("new/data/events.php?FloorId="+FloorId);  
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
} 
	
				   
});	

$( "#ChooseAgentForTask" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );
    
function UpdateCalView(FloorId)
{

var SetDate = $('#SetDate').val();	
var StratDate = new Date(scheduler2.getState().date);
var EndDate = new Date(scheduler2.getState().date);
    
var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	    
    
scheduler2.clearAll();	
scheduler2.setCurrentView(SetDate);	
    
if (FloorId=='0'){    
scheduler2.load("new/data/events.php"); 
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
}   
else {    
scheduler2.load("new/data/events.php?FloorId="+FloorId); 
scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"});    
}    

}
	
</script>
