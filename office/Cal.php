<?php

require_once '../app/init.php';
$pageTitle = lang('task_calendar');
require_once '../app/views/headernew.php';
require_once 'Classes/CalType.php';
if (Auth::check()) {
    if (Auth::userCan('138')) {
        $CompanyNum = Auth::user()->CompanyNum;
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
        $BrandsMain = $SettingsInfo->BrandsMain;

        $RoleId = Auth::user()->role_id;
        $RoleInfo = DB::table('roles')->where('id', '=', $RoleId)->first();
   
   ?>
<link href="assets/css/fixstyle.css?<?php echo date('YmdHis') ?>" rel="stylesheet">
<script type="text/javascript" src="js/settingsDialog/tasksSettings.js?<?php echo filemtime(__DIR__.'/js/settingsDialog/tasksSettings.js') ?>"></script>
<script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime(__DIR__.'/js/settingsDialog/settingsDialog.js') ?>"></script>
<script src='new/codebase/dhtmlxscheduler.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_limit.js" type="text/javascript" charset="utf-8"></script>
<?php if(isset($_SESSION['lang']) && $_SESSION['lang'] == 'he') { ?>
<script src='new/codebase/locale/locale_he.js' type="text/javascript" charset="utf-8"></script>
<?php } ?>
<script src='new/codebase/ext/dhtmlxscheduler_readonly.js' type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_collision.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_minical.js" type="text/javascript" charset="utf-8"></script>
<script src="new/codebase/ext/dhtmlxscheduler_multiselect.js"></script>
<script src="new/codebase/ext/dhtmlxscheduler_grid_view.js" ></script>
<link rel="stylesheet" type="text/css" href="new/common/dhtmlxMenu/skins/dhtmlxmenu_dhx_web.css">
<script src="new/common/dhtmlxMenu/dhtmlxmenu.js"></script>
<script src="new/common/dhtmlxMenu/ext/dhtmlxmenu_ext.js"></script>
<script  src="new/data/dhtmlxdataprocessor.js"></script>
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
   min-height: 30px;
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
           clone.className += " dhx_scale_left_scale";
           zone.appendChild(clone);
           // adjust right scale position depending on scroll
           if(parseInt(zone.style.height) > parseInt(zone.firstChild.style.height)){
               clone.style.left = "17px";
           }else{
               clone.style.left = "2px";
           }
       }
   };
          
       
       
   $(document).ready( function ()
   {
    
    init();
    if(window.location.search !=""){
        var id = getUrlParams('Id');
        var ClientID = getUrlParams('ClientId');
           getTaskByidTaskIdCliend(id,ClientID);
       }
   
   });
   function getUrlParams (name){
       var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
       if (results==null){
           return null;
       }
       else{
           return results[1] || 0;
       }
   }
   function getTaskByidTaskIdCliend(id,ClientID = null){

       var event = {}
       if(ClientID == null){
            event = scheduler.getEvent(id);
       }
       else{
            event.ClientId = ClientID;
       }
   
       var modalcode = $('#AddNewTask');
       modalcode.modal('show');
   
       $('#AddEditTaskCalendarId').val(id);
   
       $.ajax({
           url:'action/GetCalendarInfo.php?Id='+id+'&ClientId='+event.ClientId,
           dataType : 'json',
   
           success  : function (response) {
               ClientDiv.style.display = "none";
               $('#ChooseFloorForTask').val(response.Floor).trigger('change');
               $('#CalTaskTitle').val(response.Title);
               $('#AddEditTaskClientId').val(response.ClientId);
               $('#ClientName').val(response.ClientName);
               if (response.ClientId!='0'){
                   $('#ClientPhone').html('<i class="fas fa-phone-square fa-fw"></i> '+response.ClientPhone+' ');
               }
               else {
                   $('#ClientPhone').html('<i class="fas fa-phone-square fa-fw"></i> ' + '<?php echo lang('without_phone_cal') ?>');
               }
               ClientNameDiv.style.display = "block";
               $('#AddEditTaskPipeLineId').val(response.Floor);
   
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
               $('#CalTaskStatus').prop('disabled', false);
   
               if (response.GroupPermission==null || response.GroupPermission=='' || response.GroupPermission=='(NULL)') {
                   $("#SendStudioOption").val(['<?php echo Auth::user()->role_id; ?>']).trigger("change");
               } else {
                   var values = response.GroupPermission;
                   var selectedValues = values.split(",");
                   $("#SendStudioOption").val(selectedValues).trigger("change");
               }
   
           }
       });
   }
    
   function init() {
   
              
   ///  עדכון לחיצת עכבר לעברית     
   var getColumnIndex = scheduler._get_column_index;
   scheduler._get_column_index = function(){
     var col = getColumnIndex.apply(this, arguments);
     if(scheduler.getState().mode == "month"){
       if(scheduler._cols){
         col = scheduler._cols.length - col;
       }
     }
     else  if(scheduler.getState().mode == "week"){
       if(scheduler._cols){
         col = scheduler._cols.length - col;
       }
     }  
   
     return col;
   };     
        
        
               
   scheduler.templates.week_scale_date = function(date){
       var Hebrewformat = scheduler.date.date_to_str("%Y-%m-%d"); 
       var myVariable = false;
       $.ajax({
           url: 'getHebDate.php',
           type: 'POST',
           data: {Date : Hebrewformat(date)},
           success: function(data) {
           myVariable = data;
           },
           async: false // <- this turns it into synchronous
       });
           
   
           var format = scheduler.date.date_to_str("%l %j ב%F, "+myVariable);
           return format(date);
       
   };
   
        
   scheduler.templates.day_scale_date = function(date){
       
   var Hebrewformat = scheduler.date.date_to_str("%Y-%m-%d"); 
       var myVariable = false;
       $.ajax({
           url: 'getHebDate.php',
           type: 'POST',
           data: {Date : Hebrewformat(date)},
           success: function(data) {
           myVariable = data;
           },
           async: false // <- this turns it into synchronous
       });    
       
   return scheduler.date.date_to_str(scheduler.config.default_date)(date)+', '+myVariable;
   };      
               
   scheduler.templates.month_day = function(date){
       
        var Hebrewformat = scheduler.date.date_to_str("%Y-%m-%d"); 
       var myVariable = false;
       $.ajax({
           url: 'getHebDate.php',
           type: 'POST',
           data: {Date : Hebrewformat(date)},
           success: function(data) {
           myVariable = data;
           },
           async: false // <- this turns it into synchronous
       });
       
       var dateToStr_func = scheduler.date.date_to_str(scheduler.config.month_day);
       return  dateToStr_func(date)+', '+myVariable;
   };
               
      
   scheduler.attachEvent("onTemplatesReady", function(){
   
   scheduler.templates.event_text = function(start,end,ev){
   
    
    
    if (ev.Level=='0') {
          var StarIcon = " <i  class='fas fa-star' aria-hidden='true'></i> ";
   
    } else if (ev.Level=='1') {
          var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
       }  
    else if (ev.Level=='2') {
          var StarIcon = " <i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i><i class='fas fa-star' aria-hidden='true'></i> ";
        } 
    
       
    return '<div style="margin-top: -4px;"><div><span style="font-size:12px;font-weight:bold;text-align: right;">' +ev.text+ '</span></div><br><div style="padding-top: 2px;"><span style="font-size:11px;font-weight:bold;text-align: right;">'+ev.TypeTitle+' ' + StarIcon + '<?php echo lang('representative_cal') ?>' + ev.GuideName + '</span></div></div>';
   
    
   };
   
   
   });
   
   $('#ChooseAgentForTask').val('<?php echo Auth::user()->id; ?>').trigger('change');    
   $("#SendStudioOption").val(['<?php echo Auth::user()->role_id; ?>']).trigger("change");       
       
       
   scheduler.attachEvent("onDblClick", function(id, e) {
   
       getTaskByidTaskIdCliend(id)
   
       
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
       $("#AddEditTaskCalendarId").val('');
       var modalcode = $('#AddNewTask');
       modalcode.modal('show'); 
       
       var formatDate = scheduler.date.date_to_str("%Y-%m-%d\T%H:%i:%s");
       var formatDates = scheduler.date.date_to_str("%Y-%m-%d");
       var formatTime = scheduler.date.date_to_str("%H:%i:%s");
       var formatTimes = scheduler.date.date_to_str("%Y-%m-%d %H:%i:%s");    
       
     $('#SetDate').val(formatDates(date));  
     $('#SetTime').val(formatTime(date));       
     var FixToTimes = moment(formatTime(date),'HH:mm:ss').add(5,'minutes').format('HH:mm:ss'); 
     var EndTime = moment(formatTime(date),'HH:mm:ss').add(30,'minutes').format('HH:mm:ss');
   
     $('#SetToTime').prop('min', FixToTimes);       
     $('#SetToTime').val(EndTime).trigger('change');    
     $('#ChooseAgentForTask').val('<?php echo Auth::user()->id; ?>').trigger('change');    
     
        
       
        
   });
       
       
   // מיון לפי סוג
   var filterType = {
   <?php 
      $rowActivities = DB::table('caltype')->where('CompanyNum','=',$CompanyNum)->orderBy('Type', 'ASC')->get();
      foreach ($rowActivities as $rowActivitie) {
      ?>
          <?php echo $rowActivitie->id; ?>: true,
          <?php  } ?>
        };
   
        var filter_inputs = document.getElementById("filters_Type").getElementsByTagName("input");
        for (var i=0; i<filter_inputs.length; i++) {
          var filter_input = filter_inputs[i];
   
          // set initial input value based on filters settings
          filter_input.checked = filterType[filter_input.name];
   
          // attach event handler to update filters object and refresh view (so filters will be applied)
          filter_input.onchange = function() {
            filterType[this.name] = !!this.checked;
            scheduler.updateView();
          }
        }
    
   // מיון לפי רמות
   var filterslevel = {
          0: true,
          1: true,
          2: true,
        };
    
    
    
    var filter_inputsStar = document.getElementById("filters_level").getElementsByTagName("input");
        for (var i=0; i<filter_inputsStar.length; i++) {
          var filter_inputStar = filter_inputsStar[i];
   
          // set initial input value based on filters settings
          filter_inputStar.checked = filterslevel[filter_inputStar.name];
   
          // attach event handler to update filters object and refresh view (so filters will be applied)
          filter_inputStar.onchange = function() {
            filterslevel[this.name] = !!this.checked;
            scheduler.updateView();
          }
        } 
       
       
   // מיון לפי סטטוס
   var filterstatus = {
          0: true,
          1: true,
          2: false,
        };
    
    
    
    var filter_inputsStatus = document.getElementById("filters_Status").getElementsByTagName("input");
        for (var i=0; i<filter_inputsStatus.length; i++) {
          var filter_inputStatus = filter_inputsStatus[i];
   
          // set initial input value based on filters settings
          filter_inputStatus.checked = filterstatus[filter_inputStatus.name];
   
          // attach event handler to update filters object and refresh view (so filters will be applied)
          filter_inputStatus.onchange = function() {
            filterstatus[this.name] = !!this.checked;
            scheduler.updateView();
          }
        }     
    
   scheduler.filter_unit = scheduler.filter_month = scheduler.filter_day = scheduler.filter_week = function(id, event) {
          // display event only if its type is set to true in filters obj
          // or it was not defined yet - for newly created event
          if (filterstatus[event.Status] && filterslevel[event.Level] && filterType[event.Type] || event.Status==scheduler.undefined && event.Level==scheduler.undefined && event.Type==scheduler.undefined) {
            return true;
          }
   
          // default, do not display event
          return false;
        };        
       
       
          scheduler.locale.labels.grid2_tab = "<?php echo lang('reports_tasks') ?>";
   
   scheduler._click.dhx_cal_tab=function(){
    var name = this.getAttribute("name");
       var date = scheduler.getState().date;
    var mode = name.substring(0, name.search("_tab"));
    if(mode == 'grid2'){
    window.location.href = "TaskReport.php";
       return false;    
    }
       else if (mode == 'week') {
       scheduler.init("scheduler_here",new Date(date),"week");    
       }
       else if (mode == 'month') {
       scheduler.init("scheduler_here",new Date(date),"month");    
       } 
       else if (mode == 'day') {
       scheduler.init("scheduler_here",new Date(date),"day");    
       }     
       return true;
   };    
       
    
          // scheduler.config.readonly = true;
           scheduler.config.dblclick_create = false;
           scheduler.config.drag_create = false;
           scheduler.config.details_on_create = false;
           scheduler.config.details_on_dblclick = false;
      scheduler.config.xml_date="%Y-%m-%d %H:%i";
      scheduler.config.default_date = "%l %j %F, %Y"; 
      scheduler.config.first_hour = 6;
           scheduler.config.last_hour = 24;
      scheduler.config.full_day = true;
      scheduler.config.mark_now = true; 
           scheduler.config.start_on_monday = false;  
      scheduler.config.scroll_hour = new Date().getHours(); 
   //scheduler.config.ltr = true;
      if(screen.width > 768){
         scheduler.init("scheduler_here",new Date(),"week");
      } else {
         scheduler.init("scheduler_here",new Date(),"day");
      }
      scheduler.load("new/data/events.php");
   
          var dp = new dataProcessor("new/data/events.php");
          dp.setTransactionMode("POST", true);
          dp.init(scheduler);
           
       
   scheduler.attachEvent("onSaveError", function(ids, response){
       $.notify(
         {
         icon: 'fas fa-times-circle',
         message: lang('error_oops_something_went_wrong'),
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
         message: lang('action_done'),
                    
         },{
         type: 'success',
                 z_index: '99999999',    
       });
       
       $('#ChooseClientForTask').val('0').trigger('change');
       
       });    
       
       
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
    
       
    return '<div style="margin-top: -4px;"><div><span style="font-size:12px;font-weight:bold;text-align: right;">' +ev.text+ '</span></div><br><div style="padding-top: 2px;"><span style="font-size:11px;font-weight:bold;text-align: right;">'+ev.TypeTitle+' ' + StarIcon + ' ' + ev.GuideName + '</span></div></div>';
   
    
   };
   
   
   }); 
       
          scheduler2.config.readonly = true;
   
      scheduler2.init("scheduler_here_2",new Date(),"day");
      scheduler2.load("new/data/events.php");
         
   
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
<div class="col-md-12 col-sm-12">
   <!-- <div class="row">
      <div class="col-md-5 col-sm-12 order-md-1">
      <h3 class="page-header headertitlemain"  style="height:54px;">
      <?php //echo $DateTitleHeader; ?>
      </h3>
      </div>
      
      <div class="col-md-5 col-sm-12 order-md-3">
      <h3 class="page-header headertitlemain"  style="height:54px;">
      <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; ">
      <i class="fas fa-calendar-alt"></i> יומן משימות </span>
      </div>
      </h3>
      </div> -->
   <div class="col-md-2 col-sm-12 order-md-2 pb-1">
   </div>
</div>
<?php
    require_once '../app/views/tasksSettings.php';
?>
<nav aria-label="breadcrumb" class="d-flex justify-content-between align-items-center bg-light rounded shadow-sm mb-20 px-10" >
   <ol class="breadcrumb  bg-transparent px-0 py-6 mb-0">
      <li class="breadcrumb-item">
        <a class="btn btn-outline-primary" href="TaskReport.php"><?php echo lang('reports_tasks') ?> <i class="fal fa-file-alt"></i></a>
      </li>
    </ol> 
      <?php if (Auth::userCan('139'))  { ?>
      <div   >
         <select class="form-control text-start ChooseAgentForPipeline" id="ChooseAgentForPipeline" name="AgentId"  data-placeholder="<?php echo lang('choose_taking_care_representative') ?>" style="max-width: 200px;">
            <option value="BA999"><?php echo lang('everyone') ?></option>
            <?php 
               $UserInfos = DB::table('users')->where('CompanyNum' ,'=', $CompanyNum)->orderBy('display_name', 'ASC')->get();
               foreach ($UserInfos as $UserInfo) {  
               ?>  
            <option value="<?php echo $UserInfo->id; ?>" <?php if ($UserInfo->id == Auth::user()->id) { echo 'selected'; } else {} ?> ><?php echo $UserInfo->display_name; ?></option>
            <?php 
               }
               ?>  
         </select>
      </div>
      <?php } else { ?>  
      <div   >
         <select class="form-control text-start ChooseAgentForPipeline" id="ChooseAgentForPipeline" name="AgentId"  data-placeholder="<?php echo lang('choose_taking_care_representative') ?>" style="max-width: 200px;">
            <option value="BV999"><?php echo @$RoleInfo->name; ?></option>
            <option value="<?php echo Auth::user()->id; ?>" selected ><?php echo Auth::user()->display_name ?></option>
         </select>
      </div>
      <?php } ?>      
    
</nav>
<div class="row">
   <div class="col-md-12 col-sm-12 order-md-1">
      <div class="card spacebottom">
         <div class="card-header d-flex justify-content-between" >
            <div><i class="fas fa-calendar-alt"></i> <b><?php echo lang('task_calendar') ?>  </b></div>
            <a href="#" data-ip-modal="#activitiesModals"><?php echo lang('filter_tasks_cal') ?>  <i class="fas fa-filter"></i></a>    
         </div>
         <div class="card-body" style="padding: 0px;">
            <div align="center"  style='width:100%; height:870px;'>
               <div id="scheduler_here" class="dhx_cal_container" style="width: 100%; height: 101%;">
                  <div class="dhx_cal_navline">
                     <div class="dhx_cal_prev_button">&nbsp;</div>
                     <div class="dhx_cal_next_button">&nbsp;</div>
                     <div class="dhx_cal_today_button"></div>
                     <div class="dhx_cal_date"></div>
                     <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
                     <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
                     <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
                     <div class="dhx_cal_tab" name="grid2_tab" style="right:76px; width:95px;"></div>
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
</div>
<script type="text/javascript" charset="utf-8">
   $('#CalTypeOption').trigger('click');  
</script> 
<div class="ip-modal text-start" role="dialog" id="AddNewTask" data-backdrop="static" data-keyboard="false" aria-hidden="true">
   <div class="ip-modal-dialog BigDialog">
      <form  action="AddCalendarClient" id="FormCalendarClient"  class="ajax-form clearfix ip-modal-content text-start" autocomplete="off" >
         <div class="ip-modal-header d-flex justify-content-between">
            <h4 class="ip-modal-title"><?php echo lang('task_window_title') ?></h4>
            <a class="ip-close" title="Close"  data-dismiss="modal" aria-label="Close">&times;</a>
         </div>
         <div class="ip-modal-body">
             
               <div class="row">
                  <div class="col-md-6 col-sm-12 order-1">
                     <input type="hidden" id="CalPage" value="1">    
                     <input type="hidden" name="ClientId" id="AddEditTaskClientId" value="0">
                     <input type="hidden" name="PipeLineId" id="AddEditTaskPipeLineId" value="0"> 
                     <input type="hidden" name="CalendarId" id="AddEditTaskCalendarId" value="">     
                     <div class="form-group" id="ClientNameDiv" style="display: none;">
                        <label><?php echo lang('client') ?></label>    
                        <div class="row">
                           <div class="col-sm-7">      
                              <input type="text" class="form-control" id="ClientName" disabled>
                           </div>
                           <div class="col-sm-5">    
                              <span class="input-group-text" id="ClientPhone"></span>       
                           </div>
                        </div>
                     </div>
                     <div class="form-group" id="ClientDiv">
                        <label><?php echo lang('client') ?></label>
                        <select class="form-control select2ClientDesk" id="ChooseClientForTask" name="ClientForTask" data-placeholder="<?php echo lang('choose_client') ?>" style="width: 100%">
                           <option value="0"  selected><?php echo lang('without_customer_affiliation_cal') ?></option>
                        </select>
                     </div>
                     <div class="form-group" style="display: none;">
                        <label><?php echo lang('meeting_room_cal') ?></label>
                        <select class="form-control js-example-basic-single text-start select2" id="ChooseFloorForTask" name="FloorId"  data-placeholder="<?php echo lang('select_meeting_room_cal') ?>" style="width: 100%" onChange="UpdateCalView(this.value)">
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
                          <select class="form-control text-start" name="TypeOption" id="CalTypeOption">
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
                              <select class="form-control text-start" name="Level" id="CalLevel" >
                                 <option value="0"><?php echo lang('low') ?></option>
                                 <option value="1"><?php echo lang('medium') ?></option>
                                 <option value="2"><?php echo lang('high') ?></option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="form-group"></div>
                     <div class="form-row">
                        <div class="col">
                           <label><?php echo lang('taking_care_representative') ?></label>
                           <select class="form-control js-example-basic-single text-start select2" id="ChooseAgentForTask" name="AgentId"  data-placeholder="<?php echo lang('choose_taking_care_representative') ?>" style="width: 100%">
                              <?php 
                                 if ($BrandsMain=='0'){       
                                 $UserInfos = DB::table('users')->where('ActiveStatus','=','0')->where('CompanyNum', $CompanyNum)->orderBy('display_name', 'ASC')->get();
                                 }
                                 else {
                                 $UserInfos = DB::table('users')->where('ActiveStatus','=','0')->where('BrandsMain', $BrandsMain)->orderBy('display_name', 'ASC')->get();    
                                 }      
                                 foreach ($UserInfos as $UserInfo) {  
                                 ?>  
                              <option value="<?php echo $UserInfo->id; ?>"  ><?php echo $UserInfo->display_name; ?></option>
                              <?php 
                                 }
                                 ?>  
                           </select>
                        </div>
                        <div class="col">
                           <label><?php echo lang('task_auth_group') ?></label>
                           <select class="form-control js-example-basic-single select2multipleDesk text-start" name="SendStudioOption[]" id="SendStudioOption"   multiple="multiple" data-select2order="true" style="width: 100%;">
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
                           .dhx_cal_header {
                              left: unset;
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
              
         </div>
         <div class="ip-modal-footer d-flex justify-content-between align-items-start ">
           <div class="ip-actions d-flex">
             <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
             <div class="pis-20" style="width:200px; ">
               <select name="TaskStatus" id="CalTaskStatus" class="form-control" disabled>
               <option value="0"><?php echo lang('open_task') ?></option>
               <option value="1"><?php echo lang('completed_task') ?></option>
               <option value="2"><?php echo lang('canceled_task') ?></option>
               </select>
             </div>    
           </div>
            
           <a href="javascript:;" class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php echo lang('close') ?></a>     
         </div>
      </form>
   </div>
</div>
<div class="ip-modal text-start" tabindex="-1" role="dialog" id="activitiesModals" data-backdrop="static" data-keyboard="false" aria-hidden="true">
   <div class="ip-modal-dialog" <?php  // _e('main.rtl') ?>>
      <div class="ip-modal-content">
         <div class="ip-modal-header d-flex justify-content-between" >
            <h4 class="ip-modal-title"><?php echo lang('filter_tasks_cal') ?></h4>
            <a class="ip-close" title="<?php echo lang('close') ?>" data-dismiss="modal" aria-hidden="true" >&times;</a>
         </div>
         <div class="ip-modal-body" >
            <table class="table table-striped table-bordered table-hover table-dt dt-responsive" id="categories" >
               <thead>
                  <tr>
                     <th class="text-start" ><?php echo lang('type') ?></th>
                     <th class="text-start" ><?php echo lang('priority') ?></th>
                     <th class="text-start" ><?php echo lang('status_table') ?></th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>
                        <div class="filters_Type" id="filters_Type">
                           <?php 
                              $rowGuides = DB::table('caltype')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
                              foreach ($rowGuides as $rowGuide) {
                              
                              ?>
                           <label>
                           <input type="checkbox" name="<?php echo $rowGuide->id; ?>" checked />
                           <?php echo $rowGuide->Type; ?>
                           </label><br>
                           <?php }?>
                        </div>
                     </td>
                     <td>
                        <div class="filters_level" id="filters_level">
                           <label>
                           <input type="checkbox" name="0" checked />
                           <?php echo lang('low_priority_cal') ?>
                           </label><br>
                           <label>
                           <input type="checkbox" name="1" checked />
                           <?php echo lang('medium_priority_cal') ?>
                           </label><br>
                           <label>
                           <input type="checkbox" name="2" checked/>
                           <?php echo lang('high_priority_cal') ?>
                           </label><br>
                        </div>
                     </td>
                     <td>
                        <div class="filters_Status" id="filters_Status">
                           <label>
                           <input type="checkbox" name="0" checked />
                           <?php echo lang('open_task') ?>
                           </label><br>
                           <label>
                           <input type="checkbox" name="1" checked />
                           <?php echo lang('completed_task') ?>
                           </label><br>   
                           <label>
                           <input type="checkbox" name="2"  />
                           <?php echo lang('canceled_task_cal') ?>
                           </label><br>       
                        </div>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="ip-modal-footer  d-flex justify-content-between">
            <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php echo lang('close') ?></a>   
         </div>
      </div>
   </div>
</div>
<script>
   $('#ChooseAgentForPipeline').on('change', function() {  
   scheduler.clearAll();  
   scheduler.setCurrentView(new Date('<?php echo date('Y-m-d') ?>'));
   scheduler2.clearAll(); 
   scheduler2.setCurrentView(new Date('<?php echo date('Y-m-d') ?>'));
   scheduler.load("new/data/events.php?UserId="+this.value); 
   scheduler2.load("new/data/events.php?UserId="+this.value);    
   });

    $(".select2multipleDesk").select2({
        theme:"bootstrap",
        placeholder: lang('select'),
        language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
        dir: $("html").attr("dir")
    });
   $( ".select2ClientDesk" ).select2({
        theme:"bootstrap",
        placeholder: lang('search_client'),
        language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
        allowClear: true,
        width: '100%',
        dir: $("html").attr("dir"),
        ajax: {
               url: 'SearchClient.php',
               type: 'POST',
               dataType: 'json',
               cache: true
           },
        minimumInputLength: 3,
   });

    $('#SendStudioOption').on('select2:select', function (e) {    
   var selected = $(this).val();
   
     if(selected != null)
     {
       if(selected.indexOf('BA999')>=0){
         $(this).val('BA999').select2({
             theme:"bootstrap",
             placeholder: "<?php echo lang('choose') ?>",
             language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
             dir: $("html").attr("dir")
         });
       }
       else if (selected.indexOf('BA000')>=0){
       $(this).val('BA000').select2({
           theme:"bootstrap",
           placeholder: "<?php echo lang('choose') ?>",
           language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
           dir: $("html").attr("dir")
       });
      }  
     }
       
   }); 
       
       
   $(document).ready(function() {
       $(".ip-close").click(function(){
       $('#FormCalendarClient').trigger("reset");
       scheduler2.clearAll(); 
       scheduler2.setCurrentView(<?php echo date('Y-m-d') ?>);   
       scheduler2.load("new/data/events.php");
       $('#CalTaskStatus').prop('disabled', true);  
       ClientDiv.style.display = "block";
       ClientNameDiv.style.display = "none";    
       $('#ChooseClientForTask').val('0').trigger('change');   
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
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   }   
   else {    
   scheduler2.load("new/data/events.php?FloorId="+FloorId);  
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
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
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   }   
   else {    
   scheduler2.load("new/data/events.php?FloorId="+FloorId);  
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
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
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   }   
   else {    
   scheduler2.load("new/data/events.php?FloorId="+FloorId);  
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   } 
    
             
   });  
   
   $( "#ChooseAgentForTask" ).select2({
       theme: "bootstrap",
       placeholder: lang('choose'),
       language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
       dir: $("html").attr("dir")
   });
       
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
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   }   
   else {    
   scheduler2.load("new/data/events.php?FloorId="+FloorId); 
   scheduler2.showEvent({start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_task') ?>"});    
   }    
   
   }
    
   $(function() {
        var time = function(){return'?'+new Date().getTime()};
              
        $('#AddNewLead').imgPicker({
        });
      $('#AddNewTask').imgPicker({
   
        });
      $('#activitiesModals').imgPicker({
   
        });    
    
    
   });  
</script>
<?php
        require_once '../app/views/footernew.php';
    } else {
        redirect_to('../index.php');
    }
 }
?>
