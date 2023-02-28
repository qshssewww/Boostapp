<?php require_once '../../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('22')): ?>

<?php echo View::make('headernew')->render() ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;


?>

<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>

<?php CreateLogMovement('fas fa-chart-pie','נכנס לדוח רישום לשיעור ','0');


if (@$_REQUEST['StartDate']!=''){

   
$StartDate = $_REQUEST["StartDate"];  
$EndDate = $_REQUEST["EndDate"];  
        
$StartDateWeek = $_REQUEST["StartDateWeek"]; 
$EndDateWeek = $_REQUEST["EndDateWeek"]; 
    
    
$Class = '';
foreach ($_POST['Class'] as $value)
{
$Class .= $value . ",";
} 								
$Class = substr($Class,0,-1);  
    
    
$Guide = '';
foreach ($_POST['Guide'] as $value)
{
$Guide .= $value . ",";
} 								
$Guide = substr($Guide,0,-1);      
 
$myArrayClass = explode(',', $Class);
$myArrayGuide = explode(',', $Guide);    
    
    
}

else {

if (!isset($_REQUEST["StartDate"])) $_REQUEST["StartDate"] = date("Y-m-d");
if (!isset($_REQUEST["EndDate"])) $_REQUEST["EndDate"] = date("Y-m-d"); 
if (!isset($_REQUEST["StartDateWeek"])) $_REQUEST["StartDateWeek"] = date("Y-m-d");
if (!isset($_REQUEST["EndDateWeek"])) $_REQUEST["EndDateWeek"] = date("Y-m-d");
    
if (!isset($_REQUEST["Class"])) $_REQUEST["Class"] = 'BA999';
if (!isset($_REQUEST["Guide"])) $_REQUEST["Guide"] = 'BA999';    
    
    
$dt = date('Y-m-d',strtotime("-1 Months", strtotime($_REQUEST["StartDate"])));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = $_REQUEST["EndDate"];  
        
$StartDateWeek = $_REQUEST["StartDateWeek"]; 
$EndDateWeek = $_REQUEST["EndDateWeek"];
    
$Class = $_REQUEST["Class"];
$Guide = $_REQUEST["Guide"];    
  
if (@$_REQUEST['Class']!='BA999') {    
$Class = '';
foreach ($_REQUEST['Class'] as $value)
{
$Class .= $value . ",";
} 								
$Class = substr($Class,0,-1);  
}
    
if (@$_REQUEST['Guide']!='BA999') {     
$Guide = '';
foreach ($_REQUEST['Guide'] as $value)
{
$Guide .= $value . ",";
} 								
$Guide = substr($Guide,0,-1);      
}
    
$myArrayClass = explode(',', $Class);
$myArrayGuide = explode(',', $Guide);     
    
    
    
}






?>



<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>




<script>
$(document).ready(function(){
	

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
            ajax: {
   			url: 'ClassRegisterPost.php?StartDateWeek=<?php echo $StartDateWeek; ?>&EndDateWeek=<?php echo $EndDateWeek; ?>&Class=<?php echo $Class; ?>&Guide=<?php echo $Guide; ?>',
		    type: 'POST',
    		},
	       // autoWidth: true,
            "order": [[ 9, "ASC" ]],
            "paging": true,
            "scrollY": "450px",
            "scrollCollapse": true,
	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: "Bfrtip",
		//info: true,
        buttons: [
        <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'דו״ח אי הרשמה', className: 'btn btn-dark',exportOptions: {}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'דו״ח אי הרשמה' , className: 'btn btn-dark',exportOptions: {}},
           // 'pdfHtml5'
		  <?php endif ?>
			
        ],
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		


	
	
	
});

	

 

</script>

<link href="../assets/css/fixstyle.css" rel="stylesheet">

<style>
tbody tr.selected {
  color: white;
  background-color: #eeeeee;  /* Not working */
}
</style>


<div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-user-tag"></i> דו״ח רישום לשיעור
</div>
</h3>
</div>


</div>
<div class="row" dir="rtl" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
<div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="/index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active">דוחות</li>
  <li class="breadcrumb-item active" aria-current="page">דו״ח רישום לשיעור</li>
  </ol>  
</nav>    

<div class="row">

<?php include("../ReportsInc/SideMenuTrue.php"); ?>

<div class="col-md-10 col-sm-12 order-md-2">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
        
<div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-right"><i class="fas fa-user-tag"></i><strong> דו״ח רישום לשיעור</strong></div>    
  <div class="card-body">       
     
      
 <div class="row">
<div class="col-md-12 col-sm-12">

<div class="col-md-12 col-sm-12">

<form name="ThisForm" method="get">

    <div class="alertb alert-info">
    הגדר טווח תאריכים לבדיקת רישום לשיעור
    </div>
    
    
    <div class="row">
    <div class="col-md-6 col-sm-12">
    <div class="form-group row">
    <label for="InputStartDateWeek" class="col-sm-2 col-form-label">מתאריך</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="InputStartDateWeek" name="StartDateWeek" value="<?php echo $StartDateWeek; ?>" placeholder="בחר תאריך">
    </div>
  </div>     
    </div>  
        
    <div class="col-md-6 col-sm-12">
    <div class="form-group row">
    <label for="InputEndDateWeek" class="col-sm-2 col-form-label">עד תאריך</label>
    <div class="col-sm-10">
          <input type="date" class="form-control" id="InputEndDateWeek" name="EndDateWeek" value="<?php echo $EndDateWeek; ?>" min="<?php echo $StartDateWeek; ?>" placeholder="בחר תאריך">
    </div>
  </div>     
    </div>      
    
    </div>
    
     <div class="row">
    <div class="col-md-6 col-sm-12">
    <div class="form-group row">
    <label for="InputStartDateWeek" class="col-sm-2 col-form-label">שיעור</label>
    <div class="col-sm-10">
    <select name="Class[]" id="Class"  class="form-control selectAddItem" style="width:100%;"  data-placeholder="בחר שיעור" multiple="multiple" >
    <option value=""></option>  
    <option value="BA999" <?php if ($Class=='BA999'){ echo 'selected'; } else {} ?> >כל השיעורים</option>         
    <?php
    $Activities = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Type', 'ASC')->get();
    foreach ($Activities as $Activitie) {
    $selected = (in_array($Activitie->id, $myArrayClass)) ? ' selected="selected"' : '';     
	?>
   <option value="<?php echo $Activitie->id ?>" <?php echo @$selected; ?> ><?php echo $Activitie->Type; ?></option>
   <?php } ?>
   </select>  
    </div>
  </div>     
    </div>  
        
    <div class="col-md-6 col-sm-12">
    <div class="form-group row">
    <label for="InputEndDateWeek" class="col-sm-2 col-form-label">מדריך</label>
    <div class="col-sm-10">
    <select name="Guide[]"  id="Guide" class="form-control selectAddItem" style="width:100%;"  data-placeholder="בחר מדריך" multiple="multiple" >
    <option value=""></option>  
    <option value="BA999" <?php if ($Guide=='BA999'){ echo 'selected'; } else {} ?> >כל המדריכים</option>         
    <?php
    $Activities = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->orderBy('display_name', 'ASC')->get();
    foreach ($Activities as $Activitie) {
    $selected = (in_array($Activitie->id, $myArrayGuide)) ? ' selected="selected"' : '';     
	?>
   <option value="<?php echo $Activitie->id ?>" <?php echo @$selected; ?> ><?php echo $Activitie->display_name; ?></option>
   <?php } ?>
   </select>  
    </div>
  </div>     
    </div>      
    
    </div> 
	


	
 <div class="form-group row text-right" align="left">
    <div class="col-sm-12">
      <button type="submit" class="btn btn-dark text-white" id="MakeFile" name="MakeFile" value="MakeFile">הפק דוח רישום לשיעור</button>
    </div>
  </div>	
</form>
    
    
	</div>
</div>
</div>

<hr>     
      
      
      
      
      
      
      
      
      
      
<div class="row" dir="ltr" style="padding-left:15px; padding-right:15px;">
<table class="table table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">

<thead>
          <tr class="bg-dark text-white">
            <th style="text-align:right;">שם לקוח</th>
            <th style="text-align:right;">טלפון</th>
            <th style="text-align:right;">הנה"ח</th>   
            <th style="text-align:right;">מנוי</th>
            <th style="text-align:right;">תוקף</th>
            <th style="text-align:right;">כרטיסיה</th>
            <th style="text-align:right;">הערה</th> 
            <th style="text-align:right;">ממצאים רפואיים</th>  
            <th style="text-align:right;">שיעור</th>  
            <th style="text-align:right;">תאריך שיעור</th> 
            <th style="text-align:right;">שעת שיעור</th>
            <th style="text-align:right;">מדריך</th>  
          </tr>

</thead>

<tbody>

</tbody>

</table>


</div>

</div></div></div></div></div></div></div>
    
 <style>

.select2-results__option[aria-selected=true] {
    display: none;
}
</style>
    
    
 <script type="text/javascript" charset="utf-8">     

$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );      
     
 $('#Class').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר שיעור", 'language':"he", dir: "rtl" } );
    }
  }
    
});
     
 $('#Guide').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר מדריך", 'language':"he", dir: "rtl" } );
    }
  }
    
});
     
     
     
$('#InputStartDate').change(function() { 

 $("#InputEndDate").attr({
       "min" : this.value
    });	
	
$('#InputEndDate').val(this.value);	
	
});

     
     
$('#InputStartDateWeek').change(function() { 

 $("#InputEndDateWeek").attr({
       "min" : this.value
    });	
	
$('#InputEndDateWeek').val(this.value);	
	
});
     

     

</script>	 
    
    
    
<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage ('הגישה נחסמה', 'סליחה, אין לך הרשאות לגשת לעמוד זה.'); ?>
<?php endif ?>


<?php endif ?>


<?php require_once '../../app/views/footernew.php'; ?>