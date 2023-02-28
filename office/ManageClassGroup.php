<?php 
require_once '../app/init.php'; 
$pageTitle = 'ניהול שיעור';
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('82')): ?>
<?php

$GroupNumber = $_REQUEST['u'];
$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$Clients = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('GroupNumber','=', $GroupNumber)->where('Status','=', '0')->orderBy('StartDate', 'ASC')->get();
$resultcount = count($Clients);

$ClassInfo = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('GroupNumber','=', $GroupNumber)->where('Status','=', '0')->orderBy('StartDate', 'ASC')->first();

CreateLogMovement('נכנס לניהול שיעורים שיעורים', '0');

?>



<link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">



<script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/moment.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script src="https://momentjs.com/downloads/moment.js"></script>

<script>
$(document).ready(function(){
	
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );
	

	
	$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
 
    // Add type detection
    types.detect.unshift( function ( d ) {
        return moment( d, format, locale, true ).isValid() ?
            'moment-'+format :
            null;
    } );
 
    // Add sorting method - use an integer for the sorting
    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
        return moment( d, format, locale, true ).unix();
    };
};
	
	 $.fn.dataTable.moment( 'd/m/Y H:i' );
	
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
	       // autoWidth: true,
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         true,
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 10,
	      dom: "Bfrtip",
		//info: true,
	   
	    buttons: [
        <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'לוג מערכת', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'לוג מערכת' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	   		ajax: { url: 'ManageClassGroupPost.php?u=<?php echo @$_REQUEST['u'] ?>', },

		order: [[0, 'DESC']]

	   	 	   
        } );
		
		    var table = $('#categories').DataTable();
			table.columns().every( function () {
            var that = this;

		 $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );		
				
				
				
    } );
	
	
	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<!-- <div class="col-md-12 col-sm-12">
<div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-university"></i> ניהול שיעורים <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php //if (Auth::userCan('89')): ?>     
<a href="javascript:void(0);" onclick="NewClass()" class="btn btn-primary btn-block"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> הוספת שיעור חדש</a>
<?php //endif ?>   
</div>

</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="ManageClass.php" class="text-dark">ניהול שיעורים</a></li>      
  <li class="breadcrumb-item active"><?php //echo $ClassInfo->ClassName; ?> :: <?php //echo $ClassInfo->Day; ?> :: <?php //echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?></li>
  </ol>  
</nav>     -->

<?php if (Auth::userCan('89')): ?>     
    <a href="javascript:;" class="floating-plus-btn d-flex bg-primary" onclick="NewClass()" title="<?= lang('add_new_class') ?>">
        <i class="fal fa-plus fa-lg margin-a"></i>
    </a>
<?php endif; ?> 

<div class="row">
<div class="col-md-12 col-sm-12">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-university"></i> <b><?php echo $ClassInfo->ClassName; ?> :: <?php echo $ClassInfo->Day; ?> :: <?php echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span></b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
                <th class="text-right">סוג שיעור</th>
				<th class="text-right">כותרת שיעור</th>
                <th class="text-right">תאריך</th>
				<th class="text-right">יום</th>
				<th class="text-right">שעה</th>
                <th class="text-right">מדריך</th>
                <th class="text-right">נרשמים</th>
                <th class="text-right">פנוי</th>
                <th class="text-right">ממתינים</th>
                <th class="text-right">ניצול</th>
                <th class="text-right lastborder">פעולות</th>
			</tr>
		</thead>
		<tbody>
              
        </tbody>
	
	
	<tfoot>
            <tr>
                <th><span>סוג שיעור</span></th>
				<th><span>כותרת שיעור</span></th>
				<th><span>תאריך</span></th>
                <th><span>יום</span></th>
				<th><span>שעה</span></th>
                <th><span>מדריך</span></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="lastborder"></th>
            </tr>
        </tfoot>
	
        </table> 
		</div></div>
    
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



<div class="ip-modal text-right"  role="dialog" id="AddNewTask" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close ip-closePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title">הגדרת שיעור</h4>

				</div>
				<div class="ip-modal-body">

				<form action="AddClassDesk" id="FormAddClassDesk"  class="ajax-form clearfix text-right" autocomplete="off" novalidate>
                

<div class="row">	   
<div class="col-md-12 col-sm-12 order-1">	  
<input type="hidden" id="CalPage" value="1">    
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
	<input type="text" class="form-control" name="MaxClient" id="MaxClient" value="<?php echo $ClassSettingsInfo->MaxClient ?>">  
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
	<input type="number" class="form-control" name="MinClassNum" id="MinClassNum" value="<?php echo $ClassSettingsInfo->MinClient ?>" >  
	</div>  
  </div>
     

 </div>   
    
    <div id="DivMinClassNum2" style="display: none;">
    
   <div class="row">
   <div class="col-md-6">	    
    <div class="form-group">
  <label>זמן בדיקת מינימום משתתפים לפני השיעור</label>
	<input type="text" class="form-control" name="ClassTimeCheck" id="ClassTimeCheck" value="<?php echo $ClassSettingsInfo->CheckMinClient ?>">  
	</div>
    </div>   
     <div class="col-md-6">	   
    <div class="form-group">
    <label>אפשרות</label>
    <select class="form-control text-right" name="ClassTimeTypeCheck" id="ClassTimeTypeCheck" dir="rtl">
    <option value="1" <?php if ($ClassSettingsInfo->CheckMinClientType=='1') { echo 'selected'; } else {} ?> >דקות</option>
    <option value="2" <?php if ($ClassSettingsInfo->CheckMinClientType=='2') { echo 'selected'; } else {} ?> >שעות</option>         
    </select> 
    </div> 
    </div>
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
	 <input name="SetToTime" id="SetToTime" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+".$ClassSettingsInfo->EndClassTime." minutes"))); ?>" class="form-control">  
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
  <input type="text" class="form-control" name="ClassCount" id="ClassCount" value="" min="1" onkeypress='validate(event)'> 
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
     
  <div class="col-md-6">	     
  <div class="form-group">
  <label>להציג כמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientNum" id="ShowClientNum" dir="rtl">
  <option value="0">כן</option>
  <option value="1" selected>לא</option>
  </select>    
  </div> 
  </div>
      
 <div class="col-md-6">	     
    <div class="form-group">
  <label>להציג שמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientName" id="ShowClientName" dir="rtl">
  <option value="0">כן</option>
  <option value="1" selected>לא</option>
  </select>  
	</div> 
  </div>      
       
     
 </div>    

	
	
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
	  
	  
  <div class="col-md-4" id="WatingListDiv" style="display: block;">	     
  <div class="form-group">
  <label>הגבלת רשימת המתנה?</label>
  <select class="form-control text-right" name="MaxWatingList" id="WatingListAct" dir="rtl">
  <option value="0">כן</option>
  <option value="1" selected>לא</option>
  </select>  

  </div>  
  </div>    
	   
  <div id="WatingListNumDiv" class="col-md-4"  style="display: none;">	     
  <div class="form-group">
  <label>מקסימום ממתינים?</label>
  <input type="number" class="form-control" name="NumMaxWatingList" id="WatingListNum" value="" onkeypress='validate(event)'> 
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

<style>

.select2-results__option[aria-selected=true] {
    display: none;
}
</style>

<script>

    
	  function UpdateClass(ClassId, Act) {
		
if (Act=='0'){
$( "#DivViewDeskInfo" ).empty();
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('פרטי השיעור');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassViewDesks.php?Id='+ClassId; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });    
    
}	
    
else if (Act=='1'){
$( "#DivViewDeskInfo" ).empty();	
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('עריכת שיעור');    
    
 modalcode.modal('show'); 
 var url = 'new/ClassEditDesk.php?Id='+ClassId; 
 $('#DivViewDeskInfo').load(url,function(e){    
 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       
 return false;      
 });    
    
}
                   
else if (Act=='2'){
$( "#DivViewDeskInfo" ).empty();	 
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('מתאמנים משובצים');    
    
 modalcode.modal('show'); 
 var url = 'new/ClientList.php?Id='+ClassId; 
// $('#DivViewDeskInfo').load(url); 

  $('#DivViewDeskInfo').load(url,function(e){    
  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  
   return false;     
  });
    
    
}          
          
          
	};

</script>


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
 
	
$("#ClassWating").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  WatingListDiv.style.display = "block";      
  } 
  else {
  WatingListDiv.style.display = "none";
  WatingListNumDiv.style.display = "none";	  
  }  
	
  $('#WatingListAct').val('1').trigger('change');
  $('#WatingListNum').prop('min', '');	
	
});	
	
$("#WatingListAct").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  WatingListNumDiv.style.display = "block"; 
	  
  $('#WatingListNum').prop('min', '1'); 	  
	  
	  
  } 
  else {
  WatingListNumDiv.style.display = "none";      
  }    
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
    
<?php 
    
if ($ClassSettingsInfo->ReminderTimeType=='1'){
$ReminderTimeType = 'minutes';    
}
else {
$ReminderTimeType = 'hours';    
}  
                    
if ($ClassSettingsInfo->CancelTimeType=='1'){
$CancelTimeType = 'minutes';    
}
else {
$CancelTimeType = 'hours';    
}                      
    
?>   
    
    
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
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
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
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }      
    
    
});	     
    
    
    
$(document).ready(function() {
    $(".ip-close").click(function(){
    $('#FormCalendarClient').trigger("reset");
    $('#FormAddClassDesk').trigger("reset");    
    $('#FormAddClassDesk').find('.alert').hide();      
    }); 
});    
     

$('#SetTime').on('change', function() {


/// שנה גלילה לפי שעה	


var SetTime = $('#SetTime').val();
var FixToTime = moment(SetTime,'HH:mm:ss').add(<?php echo @$ClassSettingsInfo->EndClassTime; ?>,'minutes').format('HH:mm:ss') ;   
var FixToTimes = moment(SetTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;
var FixToTimeCancel = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
    
$('#SetToTime').val(FixToTime); 
$('#SetToTime').prop('min', FixToTimes);
$('#CancelTillTime').prop('max', SetTime);   
$('#CancelTillTime').val(FixToTimeCancel);    
  
    
    
 var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }       
    
				   
});	
	
$('#SetToTime').on('change', function() {


var SetTime = $('#SetTime').val();
var SetToTime = $('#SetToTime').val();
    
    
 var TypeReminder = $('#TypeReminder').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminder').prop('max', TimeReminderMax);
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminder').prop('max', '');
  $('#TimeReminder').prop('min', '');      
  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLaw').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTime').prop('max', CancelLawMax);
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTime').prop('max', '');
  $('#CancelTillTime').prop('min', '');      
  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }      
    

	
				   
});	

$( "#ChooseAgentForTask" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );
    

$(function() {
			var time = function(){return'?'+new Date().getTime()};

		$('#AddNewTask').imgPicker({

			});
	
	
});	
</script>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>