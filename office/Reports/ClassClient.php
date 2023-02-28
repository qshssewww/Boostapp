<?php require_once '../../app/init.php'; ?>



<?php if (Auth::guest()): redirect_to('index.php'); endif ?>





<?php if (Auth::check()):?>

<?php if (Auth::userCan('149')): 

$pageTitle = lang('reports_payroll_customers');
require_once '../../app/views/headernew.php';

$CompanyNum = Auth::user()->CompanyNum;




$BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();



CreateLogMovement('fas fa-chart-pie',lang('reports_payroll_coach_log '),'0');





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

            "ordering": false,

            ajax: {

   			url: 'ClassClientPost.php?StartDateWeek=<?php echo $StartDateWeek; ?>&EndDateWeek=<?php echo $EndDateWeek; ?>&Class=<?php echo $Class; ?>&Guide=<?php echo $Guide; ?>',

		    type: 'POST',

    		},

	       // autoWidth: true,

          //  "order": [[ 0, "ASC" ]],

            "paging": true,

            "scrollY": "450px",

            "scrollCollapse": true,

	     //  bStateSave:true,

		   // serverSide: true,

	        pageLength: 100,

	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

		//info: true,

         buttons: [

        <?php if (Auth::userCan('98')): ?>    

		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},

			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_unregistration') ?>', className: 'btn btn-dark',exportOptions: {}},

			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_unregistration') ?>' , className: 'btn btn-dark',exportOptions: {}},

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





<!-- <div class="row pb-3">



<div class="col-md-6 col-sm-12 order-md-1">

<h3 class="page-header headertitlemain"  style="height:54px;">

<?php //echo $DateTitleHeader; ?>

</h3>

</div>



<div class="col-md-6 col-sm-12 order-md-4">

<h3 class="page-header headertitlemain"  style="height:54px;">

<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">

<i class="fas fa-user-tag"></i> דו״ח מתאמנים לפי מדריך

</div>

</h3>

</div>





</div> -->

<div class="row mx-0 px-0"   >

<div class="col-12 mx-0 px-0">





<!-- <nav aria-label="breadcrumb" >

  <ol class="breadcrumb">	

  <li class="breadcrumb-item"><a href="/index.php" class="text-info">ראשי</a></li>

  <li class="breadcrumb-item active">דוחות</li>

  <li class="breadcrumb-item active" aria-current="page">דו״ח מתאמנים לפי מדריך</li>

  </ol>  

</nav>     -->



<div class="row">



<?php include("../ReportsInc/SideMenu.php"); ?>



<div class="col-md-10 col-sm-12">	

    <div class="tab-content"> 
        

<div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">

<div class="card spacebottom">

      <div class="card-header text-start"><i class="fas fa-user-tag"></i><strong> <?php echo lang('reports_payroll_customers') ?></strong></div>    

  <div class="card-body">       

     

      

 <div class="row">

<div class="col-md-12 col-sm-12">



<div class="col-md-12 col-sm-12">



<form name="ThisForm" method="get">



    <div class="alertb alert-info">

    <?php echo lang('payroll_set_date') ?>

    </div>

    

    

    <div class="row">

    <div class="col-md-6 col-sm-12">

    <div class="form-group row">

    <label for="InputStartDateWeek" class="col-sm-2 col-form-label"><?php echo lang('from_date') ?></label>

    <div class="col-sm-10">

      <input type="date" class="form-control" id="InputStartDateWeek" name="StartDateWeek" value="<?php echo $StartDateWeek; ?>" placeholder="<?php echo lang('select_date') ?>">

    </div>

  </div>     

    </div>  

        

    <div class="col-md-6 col-sm-12">

    <div class="form-group row">

    <label for="InputEndDateWeek" class="col-sm-2 col-form-label"><?php echo lang('until_date') ?></label>

    <div class="col-sm-10">

          <input type="date" class="form-control" id="InputEndDateWeek" name="EndDateWeek" value="<?php echo $EndDateWeek; ?>" min="<?php echo $StartDateWeek; ?>" placeholder="<?php echo lang('select_date') ?>">

    </div>

  </div>     

    </div>      

    

    </div>

    

     <div class="row">



    <div class="col-md-6 col-sm-12">

    <div class="form-group row">

    <label for="InputEndDateWeek" class="col-sm-2 col-form-label"><?php echo lang('instructor') ?></label>

    <div class="col-sm-10">

    <select name="Guide[]"  id="Guide" class="form-control selectAddItem" style="width:100%;"  data-placeholder="<?php echo lang('select_coach') ?>"  >

    <option value=""></option>         

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

	





	

 <div class="form-group row text-start">

    <div class="col-sm-12">

      <button type="submit" class="btn btn-dark text-white" id="MakeFile" name="MakeFile" value="MakeFile"><?php echo lang('extract_report_clients') ?></button>

    </div>

  </div>	

</form>

    

    

	</div>

</div>

</div>



<hr>     

      

      

      

      

      

      

      

      

      

      

<div class="row px-0 mx-0" >

<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">



<thead>

          <tr class="bg-dark text-white">

            <th style="text-align:start;"><?php echo lang('date') ?></th>

            <th style="text-align:start;"><?php echo lang('day') ?></th>

            <th style="text-align:start;"><?php echo lang('class_time') ?></th>   

            <th style="text-align:start;"><?php echo lang('class_booking_num') ?></th>

            <th style="text-align:start;"><?php echo lang('total_to_pay') ?></th>  

            <th style="text-align:start;"><?php echo lang('class_name') ?></th>

            <th style="text-align:start;"><?php echo lang('instructor') ?></th>  

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



$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "Select a State" } );      

     

 $('#Class').on('select2:select', function (e) {    

var selected = $(this).val();



  if(selected != null)

  {

    if(selected.indexOf('BA999')>=0){

      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_class') ?>" } );

    }

  }

    

});

     

 $('#Guide').on('select2:select', function (e) {    

var selected = $(this).val();



  if(selected != null)

  {

    if(selected.indexOf('BA999')>=0){

      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?php echo lang('select_coach') ?>" } );

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

<?php ErrorPage (lang('permission_blocked'), lang('no_page_persmission')); ?>

<?php endif ?>





<?php endif ?>





<?php require_once '../../app/views/footernew.php'; ?>