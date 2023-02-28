<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('ManageAgents')):


$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

?>

<script src='new/codebase/dhtmlxschedulerLTR.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_limit.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_units.js"></script>
<script src='new/codebase/locale/locale_he.js' type="text/javascript" charset="utf-8"></script>
<script src='new/codebase/ext/dhtmlxscheduler_readonly.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_collision.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_minical.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_multiselect.js"></script>

<link rel="stylesheet" type="text/css" href="new/common/dhtmlxMenu/skins/dhtmlxmenu_dhx_web.css">
<script src="new/common/dhtmlxCombo/dhtmlxcombo.js"></script>
<script src="new/common/dhtmlxMenu/dhtmlxmenu.js"></script>
<script src="new/common/dhtmlxMenu/ext/dhtmlxmenu_ext.js"></script>

<script  src="new/data/dhtmlxdataprocessor.js"></script>
<script src="https://momentjs.com/downloads/moment.js"></script>
<link rel='stylesheet' type='text/css' href='new/codebase/dhtmlxscheduler_flat.css'>
<link href="assets/css/fixstyle.css" rel="stylesheet">


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
		
#BeeOffice_form {
			position: absolute;
			z-index: 10001;
			display: none;
			padding: 10px;
		}



	/* Important !!! */
	.dhx_scale_hour{ 
		line-height:normal;
	}


.select2-dropdown {
  z-index: 20001;
}

.dhx_scale_bar {
font-size:14px;
font-weight:bold;

}


.dhx_now {
	background-color:#e35623;}

		
	.red_section {
		
		    position: relative;
			background-color: rgba(255,0,0,0.1); 
		}

		.NewClass {
			
		
			position: absolute; top: 0; left: 0; display: block;
			
			
		}		
		
	</style>
    
    <script type="text/javascript" charset="utf-8">

$(document).ready( function ()
{
	
	init();

});	
	
var oldScale =scheduler._reset_scale;
var oldScrollWidth = scheduler.xy.scroll_width;
scheduler._reset_scale = function(){
    if(this._mode != "month"){
        scheduler.xy.scroll_width = scheduler.xy.scale_width + oldScrollWidth + 1;
    }else{
        scheduler.xy.scroll_width = oldScrollWidth;
    }
    oldScale.apply(scheduler, arguments);

    var zone = scheduler._els["dhx_cal_data"][0];

    var scale = zone.lastChild;
    if(scale && scale.className.indexOf("dhx_scale_holder") == 0){
        var clone = scale.cloneNode(true);
        clone.className += " dhx_scale_right_scale";
        zone.appendChild(clone);
        // adjust right scale position depending on scroll
        if(parseInt(zone.style.height) > parseInt(zone.firstChild.style.height)){
            clone.style.right = "17px";
        }else{
            clone.style.right = "2px";
        }
    }
};
		
function init() {
		

    
    
		var sections = scheduler.serverList("sections", sections);
        var sectionslists = scheduler.serverList("sectionslists", sectionslists);	

			scheduler.locale.labels.section_custom="סניף";
			scheduler.locale.labels.section_activitie_id="סוג השיעור";
			
			scheduler.locale.labels.section_userid="מדריך";
			scheduler.locale.labels.section_addDate="תאריך הוספה";
			//scheduler.config.drag_lightbox = true;
			

	
	
///  דילוג שעות כל 15 דקות

        var step = 15;
		var format = scheduler.date.date_to_str("%H:%i");
		
		scheduler.config.hour_size_px=(60/step)*22;
		scheduler.templates.hour_scale = function(date){
			html="";
			for (var i=0; i<60/step; i++){
				html+="<div style='height:22px;line-height:22px;'>"+format(date)+"</div>";
				date = scheduler.date.add(date,step,"minute");
			}
			return html;
		}
		

scheduler.config.time_step = 15;

scheduler.attachEvent("onCellClick", function (x_ind, y_ind, x_val, y_val, e){
//alert(sectionid);
if (+scheduler.getActionData(e).section == 2 ){
scheduler.config.collision_limit = 1;
} else {
scheduler.config.collision_limit = 1;
};   
return true;
});		

		
    scheduler.templates.tooltip_text = function(start,end,ev){
	 var formatFunc = scheduler.date.date_to_str("%H:%i");	
	 
	 
	 	
    return "<div dir='rtl'></div>";
};

    
    
    
scheduler.attachEvent("onDblClick", function(id, e) {
var event = scheduler.getEvent(id);    
  var modalcode = $('#AddNewTask');
  modalcode.modal('show');
    
  $('#AddEditTaskCalendarId').val(id);   
           
    $.ajax({
	url:'action/GetCalendarInfo.php?Id='+id+'&ClientId='+event.ClientId,
    dataType : 'json',
					
    success  : function (response) {

   $('#ChooseFloorForTask').val(response.Floor).trigger('change');
   $('#CalTaskTitle').val(response.Title);
   $('#AddEditTaskClientId').val(response.ClientId);
   $('#AddEditTaskPipeLineId').val(response.Floor);
      
   $('#CalTypeOption').val(response.Type);
   $('#SetDate').val(response.StartDate);
   $('#SetTime').val(response.StartTime);  
   var FixToTimes = moment(response.StartTime,'hh:mm:ss').add(5,'minutes').format('hh:mm:ss') ;               
   $('#SetToTime').prop('min', FixToTimes);         
   $('#SetToTime').val(response.EndTime).trigger('change');
   $('#CalLevel').val(response.Level);
   $('#ChooseAgentForTask').val(response.User);
   $('#CalRemarks').val(response.Content);
   $('#CalTaskStatus').val(response.Status);
   $('#CalTaskStatus').prop('disabled', false);       
        
    if (response.Disable=='0'){
    $('#SendCalForm').prop('disabled', false);     
    }
    else {
    $('#SendCalForm').prop('disabled', true);     
    }
        
        
        
       }
}); 
    

    
return false;
  
});    
    
   
    
    
scheduler.attachEvent("onBeforeDrag", function(id, e) {
var event = scheduler.getEvent(id);    
if (event.AgentId != "1") {
return false;
}
return true;
});
    
    
scheduler.attachEvent("onClick", function (id, e){
 return false;
});

    
  setTimeout(function(){
   if(scheduler._dblClickFired){
       scheduler._dblClickFired = false;
       //if it's double-click, just drop the flag and return
   }else{
      //real handler
   }
}, 100)
    
    
scheduler.attachEvent("onEmptyClick", function (date, e){
    var action_data = scheduler.getActionData(e);
    var clickedColumn = action_data.section;
    var modalcode = $('#AddNewTask');
    modalcode.modal('show'); 
    
    var formatDate = scheduler.date.date_to_str("%Y-%m-%d\T%H:%i:%s");
    var formatDates = scheduler.date.date_to_str("%Y-%m-%d");
    var formatTime = scheduler.date.date_to_str("%H:%i:%s");
    var formatTimes = scheduler.date.date_to_str("%Y-%m-%d %H:%i:%s");    
    
    
  $('#ChooseFloorForTask').val(clickedColumn).trigger('change');    
  $('#SetDate').val(formatDates(date));  
  $('#SetTime').val(formatTime(date));       
  var FixToTimes = moment(formatTime(date),'hh:mm:ss').add(5,'minutes').format('hh:mm:ss'); 
  var EndTime = moment(formatTime(date),'hh:mm:ss').add(30,'minutes').format('hh:mm:ss');  
  $('#SetToTime').prop('min', FixToTimes);       
  $('#SetToTime').val(EndTime).trigger('change');    
  $('#SendCalForm').prop('disabled', false);    
  $('#AddEditTaskClientId').val('0'); 
  $('#AddEditTaskPipeLineId').val(''); 
  $('#AddEditTaskCalendarId').val('');     
    
  var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(formatTime(date),'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var TimeReminderMax = moment(formatTime(date),'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var TimeReminderMin = moment(formatTime(date),'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(formatTime(date),'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var CancelLawMax = moment(formatTime(date),'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var CancelLawMin = moment(formatTime(date),'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('17:00');      
        
      
  }   
     
});      
    

     

    
    
            scheduler.config.dblclick_create = false;
            scheduler.config.drag_create = false;
            scheduler.config.details_on_create = false;
            scheduler.config.details_on_dblclick = false;
	    	scheduler.locale.labels.week_unit_tab = "7 ימים";
            scheduler.locale.labels.unit_tab = "יומי";
            scheduler.config.readonly = true;
			scheduler.config.details_on_create=false;
			scheduler.config.details_on_dblclick=false;
			scheduler.config.xml_date="%Y-%m-%d %H:%i";
			scheduler.config.first_hour = 6;
			scheduler.config.last_hour = 24;
			scheduler.config.show_loading = true;
			scheduler.config.default_date = "%l %j %F, %Y";
			scheduler.config.quick_info_detached = false;
		
	       scheduler.config.full_day = false;
	
			scheduler.createUnitsView({
				name:"unit",
				property:"Floor",
				list: scheduler.serverList("sections", sections),
				size:20,                                     
				step:1
		    });

			scheduler.createUnitsView({
				name:"week_unit",
				property:"Floor",
				list: scheduler.serverList("sectionslists", sectionslists),
				days:7,
				size:20,                                     
				step:1
			});


//var mode = scheduler.getState().mode;

scheduler.attachEvent("onTemplatesReady", function(){

scheduler.templates.event_text = function(start,end,ev){

	
	if (ev.ClassLevel=='0') {
				var StarIcon = "";

	}
	else if (ev.ClassLevel=='1') {
				var StarIcon = " <i  class='fas fa-star' aria-hidden='true'></i> ";

	} else if (ev.ClassLevel=='2') {
				var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
    }	
	else if (ev.ClassLevel=='3') {
				var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
			}	
	

    // ShowApp	
	if (ev.ShowApp=='1'){
	var ShowAppIcon = " <i class='fas fa-mobile-alt' aria-hidden='true'></i> ";	
	}else {
	var ShowAppIcon = "";
	}
    
    // MinClass	
	if (ev.MinClass=='0'){
	var MinClassIcon = "";	
	}else {
	var MinClassIcon = " <i class='fas fa-info-circle' aria-hidden='true'></i> ";
	}
    
    
	var VIcon = " <i class='fas fa-check' aria-hidden='true'></i> ";
	
	var ClientRegisters = ev.MaxClient - ev.ClientRegister;
	if (ClientRegisters>0){
	var VIcon = " <i class='fas fa-check' aria-hidden='true'></i> ";	
	}else {
		
	var VIcon = " <i class='fas fa-pause' aria-hidden='true'></i> ";
		
	}
	
    if (ev.ClassWating=='0'){
	var WatingListShow = ev.WatingList+' ברשימת המתנה';
    }
    else {
    var WatingListShow = 'ללא רשימת המתנה';    
    }    

    return '<div><div><span style="float:right;font-size:12px;font-weight:bold;">' +ev.ClassName+ '</span><span style="float:left;font-size:12px;">' +StarIcon+ '</span></div><br><div style="padding-top:5px;" ><span style="float:right;font-size:11px;font-weight:bold;">' + ev.GuideName + '</span><span style="float:left;font-size:14px;font-weight:bold;">' +ShowAppIcon+ ' ' +MinClassIcon+ ' ' +VIcon+ '</span></div><br><div style="padding-top:5px;" ><span style="float:right;font-size:11px;font-weight:bold;">רשומים '+ev.ClientRegister+' מתוך '+ev.MaxClient+'</span><span style="float:left;font-size:10px;font-weight:bold; text-left">'+WatingListShow+'</span></div></div>';

	
};


}); 

    
   

////  הסתרת רשומות שנמחקו
var filterstatus = {
				0: true,
				1: true,
				2: false,
			};
	
	
	
// מיון לפי רמות
var filtersstar = {
				0: true,
				1: true,
				2: true,
                3: true,
			};
	
	
	
	var filter_inputsStar = document.getElementById("filters_star").getElementsByTagName("input");
			for (var i=0; i<filter_inputsStar.length; i++) {
				var filter_inputStar = filter_inputsStar[i];

				// set initial input value based on filters settings
				filter_inputStar.checked = filtersstar[filter_inputStar.name];

				// attach event handler to update filters object and refresh view (so filters will be applied)
				filter_inputStar.onchange = function() {
					filtersstar[this.name] = !!this.checked;
					scheduler.updateView();
				}
			}
	
	
	
// מיון לפי מיקום
			var filterRoom = {
			<?php 
	
$rowActivities = DB::table('sections')->where('CompanyNum', $CompanyNum)->orderBy('id', 'ASC')->get();
foreach ($rowActivities as $rowActivitie) {
?>
				<?php echo $rowActivitie->id; ?>: true,
				<?php  } ?>
			};

			var filter_inputs = document.getElementById("filters_room").getElementsByTagName("input");
			for (var i=0; i<filter_inputs.length; i++) {
				var filter_input = filter_inputs[i];

				// set initial input value based on filters settings
				filter_input.checked = filterRoom[filter_input.name];

				// attach event handler to update filters object and refresh view (so filters will be applied)
				filter_input.onchange = function() {
					filterRoom[this.name] = !!this.checked;
					scheduler.updateView();
				}
			}
	
	
	
scheduler.filter_unit = function(id, event) {
				// display event only if its type is set to true in filters obj
				// or it was not defined yet - for newly created event
				if (filterstatus[event.Status] && filterRoom[event.Floor] && filtersstar[event.ClassLevel] || event.Status==scheduler.undefined && event.Floor==scheduler.undefined && event.ClassLevel==scheduler.undefined) {
					return true;
				}

				// default, do not display event
				return false;
			};
	
	
<?php

$TMonth = date('Y-m-d', strtotime("-2 Months"));
	
?>

scheduler.init('scheduler_here', new Date(), "unit");

            var formatDate = scheduler.date.date_to_str("%Y-%m-%d");
            var view_start =   formatDate(new Date()); // 
	        var view_from =   formatDate(new Date('<?php echo $TMonth; ?>')); // 
	        var view_to =   formatDate(new Date('2050-01-01')); // 
            scheduler.load("new/data/deskplan.php?date="+view_start+"&from="+view_from+"&to="+view_to);
			var dp = new dataProcessor("new/data/deskplan.php?date="+view_start+"&from="+view_from+"&to="+view_to);
			dp.setTransactionMode("POST", true);
			//dp.live_updates("http://62.90.195.35:8008/sync");
	    	dp.init(scheduler);



           scheduler.config.mark_now = true;
           scheduler.config.start_on_monday = false;
		   scheduler.config.multisection = true; 

scheduler.attachEvent("onSaveError", function(ids, response){
    $.notify(
			 {
			 icon: 'fas fa-times-circle',
			 message: 'אופס... התגלתה שגיאה!',
			 },{
			 type: 'danger',
             z_index: '99999999',     
		 });
})

    
dp.attachEvent("onAfterUpdate", function(id, action, tid, response){

       $.ajax({
           
           url: "action/onEventChanged.php?TaskId="+id,
           success: function(dataN)
           {}
         });
    
    
       $.notify(
			 {
			 icon: 'fas fa-check-circle',
			 message: 'הפעולה בוצעה בהצלחה!',
                 
			 },{
			 type: 'success',
              z_index: '99999999',    
		 });
    });    
   

    
    
    
var event_id = null;

/// תפריט  קיצור דרך מקש ימני
var menu = new dhtmlXMenuObject();
			menu.setSkin("dhx_web");
            menu.setIconset("awesome");
		  //  menu.setIconsPath("new/common/imgs/");
			menu.renderAsContextMenu();
            menu.loadStruct("new/common/dhxmenu.xml?e=" + new Date().getTime());
			//menu.loadXML("new/common/dhxmenu.xml?e=" + new Date().getTime());

			scheduler.attachEvent("onContextMenu", function(event_id, native_event_object) {
				if (event_id) {
					var posx = 0;
					var posy = 0;
					if (native_event_object.pageX || native_event_object.pageY) {
						posx = native_event_object.pageX;
						posy = native_event_object.pageY;
					} else if (native_event_object.clientX || native_event_object.clientY) {
						posx = native_event_object.clientX + document.body.scrollRight + document.documentElement.scrollRight;
						posy = native_event_object.clientY + document.body.scrollTop + document.documentElement.scrollTop;
					}
					menu.showContextMenu(posx, posy);

					return false; // prevent default action and propagation

				}
								
				return true;
			});


menu.attachEvent("onClick", function(id) {
   var menuname = menu.getItemText(id);
   
   
if (id=='ClientList'){
$( "#DivViewDeskInfo" ).empty();	 
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('מתאמנים משובצים');    
    
 modalcode.modal('show'); 
 var url = 'new/ClientList.php?Id='+event_id; 
// $('#DivViewDeskInfo').load(url); 

  $('#DivViewDeskInfo').load(url,function(e){    
  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  
   return false;     
  });
    
    
}
    
if (id=='ClientWatingList'){
$( "#DivViewDeskInfo" ).empty();	 
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('סידור רשימת המתנה');    
    
 modalcode.modal('show'); 
 var url = 'new/ClientWatingList.php?Id='+event_id; 
// $('#DivViewDeskInfo').load(url); 

  $('#DivViewDeskInfo').load(url,function(e){    
  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
   return false;      
  });
    
    
}    
   

else if (id=='ClassClose'){
$( "#DivViewDeskInfo" ).empty();
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('שיעור הושלם');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassClose.php?Id='+event_id; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });    
    
}

  
else if (id=='ClassCancel'){
$( "#DivViewDeskInfo" ).empty();
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('ביטול שיעור');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassCancel.php?Id='+event_id; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });     
	
}
    
else if (id=='SendNofitication'){
$( "#DivViewDeskInfo" ).empty();
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('שליחת הודעה לרשומים');    
    
 modalcode.modal('show'); 
 var url = 'new/SendNofitication.php?Id='+event_id; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });     
	
}    
		
else if (id=='ClassViewDesks'){
$( "#DivViewDeskInfo" ).empty();
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('פרטי השיעור');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassViewDesks.php?Id='+event_id; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });    
    
}	
    
else if (id=='ClassEditDesk'){
$( "#DivViewDeskInfo" ).empty();	
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('עריכת שיעור');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassEditDesk.php?Id='+event_id; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });    
    
}	    
	
	
	
	
	
	
});


 scheduler.attachEvent("onContextMenu", function(event_id_loc, native_event_object) {
      event_id = event_id_loc;
   });    
    
    
    
////  סוף היומן

    
}
		
		
/// לוח שנה קטן
function show_minical(){
		if (scheduler.isCalendarVisible())
			scheduler.destroyCalendar();
		else
			scheduler.renderCalendar({
				position:"dhx_minical_icon",
				date:scheduler._date,
				navigation:true,
				handler:function(date,calendar){
					scheduler.setCurrentView(date);
					scheduler.destroyCalendar()
				}
			});	
			
						
	}

	
	</script>

<script type="text/javascript" src="https://urbanpilates.beeoffice.co.il/assets/js/date_time.js"></script>
 <script type="text/javascript" src="new/scripts/date.js"></script>



<!--- Start DeskPlan --->

<div class="row">
    
    
<?php include("CalInc/RightCards.php"); ?>
    
<div class="col-md-10 col-sm-12 order-md-1">	
  
    
    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-calendar-check"></i> <b>יומן שיעורים</b> 
 	</div>    
  	<div class="card-body" style="padding: 0px;">       



	<div align="center" style='width:100%; height:1000px;' >
	
	   <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_today_button"></div>
			<div class="dhx_cal_date"></div>
			
			<div class="dhx_cal_tab" name="week_unit_tab" style="right:280px;"></div>
			<div class="dhx_cal_tab" name="unit_tab" style="right:280px;"></div>

    <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="show_minical()">&nbsp;</div>
			
			
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>		
	</div>
	</div>	   
        
        
        
	</div>
    </div>
    
	</div> 
</div>

               	

<!--- End DeskPlan ---> 




	<div class="modal draggable fade" id="activitiesModals" role="dialog" aria-hidden="true" dir="rtl">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">מיון שיעורים</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">

		
	<table class="table table-striped table-bordered table-hover table-dt dt-responsive" id="categories" dir="rtl">
		<thead>
			<tr>
				<th style="text-align: right;">מדריכים</th>
                <th style="text-align: right;">מיקום</th>
                <th style="text-align: right;">דרגת שיעורים</th>
			</tr>
		</thead>
		<tbody>
		
		<tr>
		<td>
			<div class="filters_Guide" id="filters_Guide">
<?php 

$rowGuides = DB::table('Guide')->where('Status','=','0')->orderBy('GuideName', 'ASC')->get();
foreach ($rowGuides as $rowGuide) {

?>
		<label>
			<input type="checkbox" name="<?php echo $rowGuide->id; ?>" checked />
			<?php echo $rowGuide->GuideName; ?>
		</label><br>
        <?php }?>
</div>	
			</td>
			
			<td>
			<div class="filters_room" id="filters_room">
<?php 

$rowActivities = DB::table('sections')->orderBy('id', 'ASC')->get();
foreach ($rowActivities as $rowActivitie) {

?>
		<label>
			<input type="checkbox" name="<?php echo $rowActivitie->id; ?>" checked />
			<?php echo $rowActivitie->Title; ?>
		</label><br>
        <?php }?>
</div>	
			</td>
			
			
			<td><div class="filters_star" id="filters_star">

		<label>
			<input type="checkbox" name="0" checked />
			שיעור מתחילים
		</label><br>
		<label>
			<input type="checkbox" name="1" checked />
			שיעור בקצב דינאמי
		</label><br>
		<label>
			<input type="checkbox" name="2" checked/>
			שיעור ברמה מתקדמת
		</label><br>

</div></td>
	
	
		</tr>
			
	</tbody>
	</table>  	

				</div>
				<div class="ip-modal-footer">
    
                <button type="button" class="btn btn-default ip-close" data-dismiss='modal'><?php _e('main.close') ?></button> 
             
 
				</div>
			</div>
		</div>
	</div>



<script type="text/javascript" charset="utf-8">
$('#CalTypeOption').trigger('click');	
</script> 

<div class="ip-modal text-right"  role="dialog" id="AddNewTask" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close ip-closePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title">הגדרת שיעור</h4>

				</div>
				<div class="ip-modal-body">

				<form action="AddClassDesk" id="FormAddClassDesk"  class="ajax-form clearfix text-right" autocomplete="off">
                

<div class="row">	   
<div class="col-md-12 col-sm-12 order-1">	  
<input type="hidden" id="CalPage" value="1"> 
<input type="hidden" id="CalPageR" value="2">       
<input type="hidden" name="CalendarId" id="AddEditTaskCalendarId" value="">     

 <div class="row">
 <div class="col-md-4">	 
  <div class="form-group">
  <label>מיקום שיעור</label>
    <select class="form-control js-example-basic-single text-right" id="ChooseFloorForTask" name="FloorId" dir="rtl" data-placeholder="בחר מיקום לשיעור" style="width: 100%" onChange="UpdateCalView(this.value)">
  <?php 
  $SectionInfos = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Floor', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Title; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div> 
 </div> 
     
 <div class="col-md-4">	 
  <div class="form-group">
  <label>סוג שיעור</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="ClassNameType" id="ClassNameType" dir="rtl" data-placeholder="בחר סוג שיעור">
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->where('EventType','=','0')->orderBy('Type', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Type; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div> 
 </div>  
     
 <div class="col-md-4">	 
  <div class="form-group">
  <label>מוצג באפליקציה?</label>
    <select class="form-control js-example-basic-single text-right" name="ShowApp" id="ShowApp" dir="rtl">
    <option value="1" selected>כן</option> 
    <option value="2">לא</option>     
  </select> 
  </div> 
 </div>       
     
    
 </div>      
	
    
    
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>כותרת השיעור</label>
	<input type="text" class="form-control" name="ClassName" id="ClassName">  
	</div>  
  </div>
     
 <div class="col-md-4">	     
  <div class="form-group">
  <label>מדריך</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="GuideId" id="GuideId" dir="rtl" data-placeholder="בחר מדריך לשיעור" >
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$CompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->display_name; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
	</div>  
  </div>
     
     
 <div class="col-md-4">	     
  <div class="form-group">
    <label>דרגת השיעור</label>
  <select class="form-control text-right" name="ClassLevel" id="ClassLevel" dir="rtl">
    <option value="0" selected>ללא דרגת שיעור</option>
    <option value="1">שיעור למתחילים</option>
	<option value="2">שיעור בקצב דינאמי</option>
	<option value="3">שיעור ברמה מתקדמת</option>
	</select>  
	</div>  
  </div>     
     
 </div>
     
 
 <div class="row">
     
 <div class="col-md-4">	     
    <div class="form-group">
  <label>מקסימום משתתפים</label>
	<input type="text" class="form-control" name="MaxClient" id="MaxClient" value="6">  
	</div> 
  </div>     
     
     
 <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר מינימום בשיעור?</label>
  <select class="form-control text-right" name="MinClass" id="MinClass" dir="rtl">
    <option value="0" selected>לא</option>
    <option value="1">כן</option>
	</select>  
	</div>  
  </div>
     
  <div id="DivMinClassNum1" class="col-md-4" style="display: none;">	     
  <div class="form-group">
  <label>מינימום משתתפים</label>
	<input type="number" class="form-control" name="MinClassNum" id="MinClassNum">  
	</div>  
  </div>
     

 </div>   
    
    <div id="DivMinClassNum2" style="display: none;">
    
    <div class="form-group">
  <label>זמן בדיקת מינימום משתתפים לפני השיעור (בדקות)</label>
	<input type="text" class="form-control" name="ClassTimeCheck" id="ClassTimeCheck" value="10">  
	</div> 
    
    <div class="alertb alert-warning">שים לב! השיעור יבוטל אוטומטית במידה והשיעור לא הגיע למינימום משתתפים.<br>
התראה תשלח למשתתפים הרשומים על ביטול השיעור.</div>
    </div>
    
    
    
 <?php 
function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
   $seconds = strtotime($hour);
   $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
   return date($format, $rounded);
}     
?>  
    
  <div class="row">
 <div class="col-md-3">	     
  <div class="form-group">
  <label>תאריך תחילת השיעור</label>
  <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" class="form-control">
	</div>  
  </div>
     
  <div class="col-md-3">	     
  <div class="form-group">
  <label>יום השיעור</label>
 <select name="Day" id="Day" data-placeholder="בחר יום" class="form-control" style="width:100%;">
<option value="">בחר יום</option>  

     <option value="0">ראשון</option>
     <option value="1">שני</option>
     <option value="2">שלישי</option>
     <option value="3">רביעי</option>
     <option value="4">חמישי</option>
     <option value="5">שישי</option>
     <option value="6">שבת</option>

          </select>

	</div>  
  </div>
     
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת התחלה</label>
	  <input name="SetTime" id="SetTime" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control">  
	</div> 
  </div>
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת סיום</label>
	 <input name="SetToTime" id="SetToTime" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+50 minutes"))); ?>" class="form-control">  
	</div> 
  </div>      
       
     
 </div>     
    
   <div class="row">
 <div class="col-md-4">	        
  <div class="form-group">
  <label>אופי השיעור</label>
    <select class="form-control text-right" name="ClassType" id="ClassType" dir="rtl">
  <option value="1" selected>שיעור קבוע</option>
  <option value="2">שיעור מוגבל בחזרות</option>
  <option value="3">שיעור חד פעמי</option>         
          
  </select>  
  </div>
 </div>
       
 <div id="DivClassType" class="col-md-3" style="display: none;">	        
  <div class="form-group">
  <label>מספר חזרות (בשבועות)</label>
  <input type="text" class="form-control" name="ClassCount" id="ClassCount" value="" min="1"> 
  </div>
 </div>       
 
       
 <div class="col-md-5">	        
  <div class="form-group">
  <label>הצג בחירת מכשירים</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="ClassDevice" id="ClassDevice" dir="rtl" data-placeholder="בחר טבלת מכשירים" >
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('numbers')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Name', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Name; ?></option>	  
  <?php 
		 }
  ?>  
  </select>
  </div>
 </div>       
       
 </div>       

<hr>
    
  <div class="form-group">
  <label>בחר סוג מנוי להזמנת שיעור זה</label>
    <select class="form-control js-example-basic-single select2multipleDesk text-right" name="ClassMemberType[]" id="ClassMemberType" dir="rtl"  multiple="multiple" >
    <option value=""></option>
    <option value="BA999">כל סוגי המנויים</option>    
  <?php 
  $SectionInfos = DB::table('membership_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Type; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
	</div>      

<hr>
  <div class="row">

  <div class="col-md-4">	     
  <div class="form-group">
  <label>לאפשר רשימת המתנה?</label>
  <select class="form-control text-right" name="ClassWating" id="ClassWating" dir="rtl">
  <option value="0" selected>כן</option>
  <option value="1">לא</option>
  </select>  

  </div>  
  </div>
     
  <div class="col-md-4">	     
  <div class="form-group">
  <label>להציג כמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientNum" id="ShowClientNum" dir="rtl">
  <option value="0">כן</option>
  <option value="1" selected>לא</option>
  </select>    
  </div> 
  </div>
      
 <div class="col-md-4">	     
    <div class="form-group">
  <label>להציג שמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientName" id="ShowClientName" dir="rtl">
  <option value="0">כן</option>
  <option value="1" selected>לא</option>
  </select>  
	</div> 
  </div>      
       
     
 </div>    
    <hr>
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>שלח תזכורת ללקוח?</label>
  <select class="form-control text-right" name="SendReminder" id="SendReminder" dir="rtl">
  <option value="0" selected>כן</option>
  <option value="1">לא</option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר זמן לשליחת התזכורת</label>
  <select class="form-control text-right" name="TypeReminder" id="TypeReminder" dir="rtl">
  <option value="1" selected>ביום השיעור</option>
  <option value="2">יום לפני השיעור</option>
  </select>  

  </div>  
  </div>
     
  <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר שעת שליחת התזכורת</label>
  <input type="time" class="form-control" name="TimeReminder" id="TimeReminder" step="300" value="" max="" min="">     
  </div> 
  </div>
        

 </div>  
    
    
   <hr>
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>בחר חוק ביטולים</label>
  <select class="form-control text-right" name="CancelLaw" id="CancelLaw" dir="rtl">
  <option value="1" selected>ביום השיעור עד שעה</option>
  <option value="2">ביום לפני השיעור עד שעה</option>
  <option value="3">ביום לבחירה עד שעה</option>
  <option value="4">לא ניתן לביטול באפליקציה</option>       
  <option value="5">ביטול חופשי</option>       
  </select>  
	</div>  
  </div>
     
   <div id="DivCancelLaw3" class="col-md-4" style="display: none;">	     
  <div class="form-group">
  <label>בחר יום לפני יום השיעור</label>
  <select name="CancelDay" id="CancelDay" data-placeholder="בחר יום" class="form-control" style="width:100%;">
  <option value="">בחר יום</option>  


  </select>
  </div>  
  </div>   
       
     
  <div id="DivCancelLaw" class="col-md-4">	     
  <div class="form-group">
  <label>הגדר עד שעה לביטול</label>
  <input name="CancelTillTime" id="CancelTillTime" type="time" step="300" min="" value="" class="form-control">           
  </div> 
  </div>

 </div>  
    
    
       
    <div id="DivCancelLaw6" class="alertb alert-warning" style="display: none;">שים לב! יש לבחור <u>יום</u> לפני יום השיעור שנקבע.<br>
לדוגמא: שיעורי יום ראשון בשעה 09:00 בבוקר ניתן לבטל עד שישי בשעה 12:00.</div> 
    
    
    <div id="DivCancelLaw4" class="alertb alert-warning" style="display: none;">שים לב! באפשרות זו, ללקוח לא יופיע כפתור ביטול באפליקציה לאחר הזמנת שיעור זה.</div>   
       
    <div id="DivCancelLaw5" class="alertb alert-warning" style="display: none;">שים לב! הלקוח יוכל לבטל את השיעור בכל שלב וללא חיוב.</div>      
    

	</div>
	

	  
	  
</div>	 	
					
					
					
					
					
					
					
				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" id="SendCalForm" class="btn btn-primary"><?php _e('main.save_changes') ?></button> 
                </div>
				</form>    
                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php _e('main.close') ?></a>     
				
                
				</div>
			</div>
		</div>
	</div>



<div class="ip-modal text-right"  role="dialog" id="ViewDeskInfo" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                 <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title"></h4>
                
				</div>
				<div class="ip-modal-body">

	        
<div id="DivViewDeskInfo">
</div>
        
				</div>
			</div>
		</div>
	</div>




<script>

$( ".select2Desk" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl", allowClear:"true" } );  
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl" } );      

     
    

$("#Day").change(function() {

var Id = this.value; 
if (Id=='0') {    
/// ראשון    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0" disabled>ראשון</option>'); 
}
else if (Id=='1') {     
/// שני    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1" disabled>שני</option>');
}
else if (Id=='2') {      
/// שלישי    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2" disabled>שלישי</option>'); 
}
else if (Id=='3') {      
/// רביעי    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3" disabled>רביעי</option>'); 
}
else if (Id=='4') {      
/// חמישי    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4" disabled>חמישי</option>');
}
else if (Id=='5') {      
/// שישי    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5" disabled>שישי</option>'); 
}
else if (Id=='6') {      
/// שבת    
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6" disabled>שבת</option>');
}
else {
$('#CancelDay').find('option').remove().end().append('<option value="">בחר יום</option>');    
}    
    
   
    
//.val('whatever')    
    
});    
    
$("#MinClass").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  DivMinClassNum1.style.display = "none";
  DivMinClassNum2.style.display = "none";      
  } 
  else {
  DivMinClassNum1.style.display = "block";
  DivMinClassNum2.style.display = "block";  
    
  var MaxClient = $('#MaxClient').val();      
  $('#MinClassNum').prop('max', MaxClient);
  $('#MinClassNum').prop('min', '1');      
      
  }    
});	

    
$('#ClassMemberType').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl" } );
    }
  }
    
});	    
    
    
 
    
$("#MaxClient").change(function() {

  var MaxClient = $('#MaxClient').val();      
  $('#MinClassNum').prop('max', MaxClient);
  $('#MinClassNum').prop('min', '1');      
      
});	  
    
    
 $("#ClassNameType").change(function() {

  var ClassName = $('#ClassNameType').select2('data');
       
     
     
  $('#ClassName').val(ClassName[0].text);   
     
  if ($('#ClassNameType option:selected').length > 0) {
   $('#ClassName').val(ClassName[0].text);    
  }
else {
    $('#ClassName').val('');  
}     
     
     
      
});	     
    
    
    
$("#ClassType").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivClassType.style.display = "none";
  $('#ClassCount').val('999');      
  } 
  else if (Id=='2'){
  DivClassType.style.display = "block";
  $('#ClassCount').val('');   
  }    
  else {
   $('#ClassCount').val('1');
   DivClassType.style.display = "none";      
  }    
});	    
 
$("#TypeReminder").change(function() {
    
var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();
    
    
 var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('17:00');      
        
      
  }          
    
 });	   
$("#CancelLaw").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivCancelLaw.style.display = "block";      
  DivCancelLaw3.style.display = "none";
  DivCancelLaw4.style.display = "none";
  DivCancelLaw5.style.display = "none";
  DivCancelLaw6.style.display = "none";      
  } 
  else if (Id=='2'){
  DivCancelLaw.style.display = "block";      
  DivCancelLaw3.style.display = "none";
  DivCancelLaw4.style.display = "none";
  DivCancelLaw5.style.display = "none";
  DivCancelLaw6.style.display = "none";      
  }  
  else if (Id=='3'){
  DivCancelLaw.style.display = "block";      
  DivCancelLaw3.style.display = "block";
  DivCancelLaw4.style.display = "none";
  DivCancelLaw5.style.display = "none";
  DivCancelLaw6.style.display = "block";      
  }  
  else if (Id=='4'){
  DivCancelLaw.style.display = "none";      
  DivCancelLaw3.style.display = "none";
  DivCancelLaw4.style.display = "block";
  DivCancelLaw5.style.display = "none";
  DivCancelLaw6.style.display = "none";      
  }  
  else if (Id=='5'){
  DivCancelLaw.style.display = "none";      
  DivCancelLaw3.style.display = "none";
  DivCancelLaw4.style.display = "none";
  DivCancelLaw5.style.display = "block";   
  }      
  else {
  DivCancelLaw.style.display = "block";      
  DivCancelLaw3.style.display = "none";
  DivCancelLaw4.style.display = "none";
  DivCancelLaw5.style.display = "none"; 
  DivCancelLaw6.style.display = "none";      
  } 
    
    
var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();
        
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('17:00');      
        
      
  }      
    
    
});	     
    
    
    
$(document).ready(function() {
    $(".ip-close").click(function(){
    $('#FormCalendarClient').trigger("reset");
    $('#FormAddClassDesk').trigger("reset"); 
    $('.select2multipleDesk').val('').trigger('change');
    $('.select2Desk').val('').trigger('change');       
    $('#FormAddClassDesk').find('.alert').hide();      
    }); 
});    
     
    
    
$('#SetDate').on('change', function() {	
var FloorId = $("#ChooseFloorForTask option:selected").val();   
scheduler.clearAll();	
scheduler.setCurrentView(this.value);	

/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler.getState().date);
var EndDate = new Date(scheduler.getState().date);	

var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
if (FloorId=='0'){    
scheduler.load("new/data/deskplan.php"); 
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
}   
else {    
scheduler.load("new/data/deskplan.php?FloorId="+FloorId);  
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
} 	

	
				   
});
	
$('#SetTime').on('change', function() {
var FloorId = $("#ChooseFloorForTask option:selected").val();	
var SetDate = $('#SetDate').val();	
scheduler.clearAll();	
scheduler.setCurrentView(SetDate);	

/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler.getState().date);
var EndDate = new Date(scheduler.getState().date);	

var SetTime = $('#SetTime').val();
var FixToTime = moment(SetTime,'HH:mm:ss').add(50,'minutes').format('HH:mm:ss') ;   
var FixToTimes = moment(SetTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;
var FixToTimeCancel = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
    
$('#SetToTime').val(FixToTime); 
$('#SetToTime').prop('min', FixToTimes);
$('#CancelTillTime').prop('max', SetTime);   
$('#CancelTillTime').val(FixToTimeCancel);    
  
    
    
 var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('17:00');      
        
      
  }       
    
    
    
    
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
	
if (FloorId=='0'){    
scheduler.load("new/data/deskplan.php"); 
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
}   
else {    
scheduler.load("new/data/deskplan.php?FloorId="+FloorId);  
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
} 
	
				   
});	
	
$('#SetToTime').on('change', function() {
var SetDate = $('#SetDate').val();	
var FloorId = $("#ChooseFloorForTask option:selected").val();    
scheduler.clearAll();	
scheduler.setCurrentView(SetDate);	
/// שנה גלילה לפי שעה	
var StratDate = new Date(scheduler.getState().date);
var EndDate = new Date(scheduler.getState().date);	

var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();
    
    
 var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('17:00');      
        
      
  }      
    
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	
	
if (FloorId=='0'){    
scheduler.load("new/data/deskplan.php"); 
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
}   
else {    
scheduler.load("new/data/deskplan.php?FloorId="+FloorId);  
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
} 
	
				   
});	

$( "#ChooseAgentForTask" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );
    
function UpdateCalView(FloorId)
{

var SetDate = $('#SetDate').val();	
var StratDate = new Date(scheduler.getState().date);
var EndDate = new Date(scheduler.getState().date);
    
var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();	
	
var SetTime_H = SetTime.split(':');	
var SetToTime_H = SetToTime.split(':');		

StratDate.setHours(SetTime_H[0]);
StratDate.setMinutes(SetTime_H[1]);
	
EndDate.setHours(SetToTime_H[0]);
EndDate.setMinutes(SetToTime_H[1]);	    
    
scheduler.clearAll();	
scheduler.setCurrentView(SetDate);	
    
if (FloorId=='0'){    
scheduler.load("new/data/deskplan.php"); 
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
}   
else {    
scheduler.load("new/data/deskplan.php?FloorId="+FloorId); 
scheduler.showEvent({start_date:StratDate, end_date:EndDate, text:"פעילות חדשה"});    
}    

}
	
$(function() {
			var time = function(){return'?'+new Date().getTime()};
						
			$('#AddNewLead').imgPicker({
			});
		$('#AddNewTask').imgPicker({

			});
	
	
});	
</script>

<?php else: ?>
<?php redirect_to('logout.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>
<?php redirect_to('index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>