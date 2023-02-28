<?php require_once '../../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('22')): ?>

<?php echo View::make('headernew')->render() ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;


?>

<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>

<?php CreateLogMovement('fas fa-chart-pie','נכנס לדוח אי הרשמה ','0');


if (@$_REQUEST['StartDate']!=''){

   
$StartDate = $_REQUEST["StartDate"];  
$EndDate = $_REQUEST["EndDate"];  
        
$StartDateWeek = $_REQUEST["StartDateWeek"]; 
$EndDateWeek = $_REQUEST["EndDateWeek"]; 

}

else {

if (!isset($_REQUEST["StartDate"])) $_REQUEST["StartDate"] = date("Y-m-d");
if (!isset($_REQUEST["EndDate"])) $_REQUEST["EndDate"] = date("Y-m-d"); 
if (!isset($_REQUEST["StartDateWeek"])) $_REQUEST["StartDateWeek"] = date("Y-m-d");
if (!isset($_REQUEST["EndDateWeek"])) $_REQUEST["EndDateWeek"] = date( 'Y-m-d', strtotime( '+7 day' ) );     
    
    
$dt = date('Y-m-d',strtotime("-1 Months", strtotime($_REQUEST["StartDate"])));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = $_REQUEST["EndDate"];  
        
$StartDateWeek = $_REQUEST["StartDateWeek"]; 
$EndDateWeek = $_REQUEST["EndDateWeek"];     
    
    
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
   			    url: 'NoneBookingPost.php?StartDateWeek=<?php echo $StartDateWeek; ?>&EndDateWeek=<?php echo $EndDateWeek; ?>',
				type: 'GET',
    		},
	       // autoWidth: true,

            "paging": true,
//            "scrollY": "450px",
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
<i class="fas fa-user-minus"></i> דו״ח אי הרשמה
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
  <li class="breadcrumb-item active" aria-current="page">דו״ח אי הרשמה</li>
  </ol>  
</nav>    

<div class="row">

 <?php include("../ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12 order-md-2">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
        
<div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-right"><i class="fas fa-user-minus"></i><strong> דו״ח אי הרשמה</strong></div>    
  <div class="card-body">       
     
      
 <div class="row">
<div class="col-md-12 col-sm-12">

<div class="col-md-12 col-sm-12">

<form name="ThisForm" method="get">

    <div class="alertb alert-info">
    הגדר טווח תאריכים לבדיקת אי הרשמה בסטודיו
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
    
    
	


	
 <div class="form-group row text-right" align="left">
    <div class="col-sm-12">
      <button type="submit" class="btn btn-dark text-white" id="MakeFile" name="MakeFile" value="MakeFile">הפק דוח אי הרשמה</button>
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
            <th style="text-align:right;">#</th>  
            <th style="text-align:right;">שם לקוח</th>
            <th style="text-align:right;">טלפון</th>
            <th style="text-align:right;">מנוי</th>
            <th style="text-align:right;">שיעור אחרון</th> 
            <th style="text-align:right;">הרשמה עתידית</th>   
          </tr>

</thead>

<tbody>

</tbody>

</table>


</div>

</div></div></div></div></div></div></div>
    
    
 <script type="text/javascript" charset="utf-8">     

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