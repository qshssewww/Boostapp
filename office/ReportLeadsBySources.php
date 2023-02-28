<?php require_once '../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('117')): ?>

<?php echo View::make('headernew')->render() ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;


?>

<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>

<?php CreateLogMovement('נכנס לדוח לידים לפי מקורות ','0'); ?>


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
	        "scrollY":        "450px",
            "scrollCollapse": true,
            "paging":         true,
	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: "Bfrtip",
		//info: true,
	   
	    buttons: [
        <?php if (Auth::userCan('98')): ?>
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'דו״ח לידים לפי מקור', className: 'btn btn-dark',exportOptions: {}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'דו״ח לידים לפי מקור' , className: 'btn btn-dark',exportOptions: {}},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	//	order: [[0, 'DESC']]

	   	 	   
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
    $('#categories tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            categoriesDataTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
	
	
	
});

	

 

</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">

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
<i class="fas fa-chart-pie fa-fw"></i> דו״ח לידים לפי מקור
</div>
</h3>
</div>


</div>
<div class="row" dir="rtl" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
<div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active">דוחות</li>
  <li class="breadcrumb-item active" aria-current="page">דו״ח לידים לפי מקור</li>
  </ol>  
</nav>    

<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12 order-md-2">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
<div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-right" dir="rtl"><i class="fas fa-chart-pie fa-fw"></i><strong> דו״ח לידים לפי מקור</strong></div>    
  <div class="card-body">       
                    <div class="alert alert-info" role="alert">
  <strong>לידיעתך:</strong> בטבלה מוצגים גם מקורות אוטומטיים וגם מקורות ידניים. ליד מסויים יכול להיות משוייך ל-2 סוגי מקורות ולכן יכול להופיע פעמיים.
</div>
<br>
<div class="row" dir="rtl" style="padding-left:15px; padding-right:15px;">



<table class="table table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">

<thead>
          <tr class="bg-dark text-white">
            <th style="text-align:right;">שם המקור</th>
            <?php 
			$LeadsStstuss = DB::table('leadstatus')->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->orderBy('Sort', 'ASC')->get();
			foreach ($LeadsStstuss as $LeadsStstus) {
            echo '<th style="text-align:right;">'.$LeadsStstus->Title.'</th>';
            }
			?>
            <th style="text-align:right;">הצלחה</th>
            <th style="text-align:right;">כישלון</th>
            <th style="text-align:right;">סה״כ</th>
          </tr>

</thead>

<tbody>
<?php
$Sources = DB::table('pipeline')->where('CompanyNum', '=', $CompanyNum)->groupBy('Source','Source2')->get();
	foreach ($Sources as $Source) {
$PipeLineCountTotal = DB::table('pipeline')->where('Source', '=', @$Source->Source)->where('CompanyNum', '=', $CompanyNum)->Orwhere('Source2', '=', @$Source->Source2)->where('CompanyNum', '=', $CompanyNum)->groupBy('Source','Source2')->count();
$PipeLineCountSuccess = DB::table('pipeline')->where('Source', '=', @$Source->Source)->where('PipeId', '=', $CompanyNum.'98')->where('CompanyNum', '=', $CompanyNum)->Orwhere('Source2', '=', @$Source->Source2)->where('PipeId', '=', $CompanyNum.'98')->where('CompanyNum', '=', $CompanyNum)->groupBy('Source','Source2')->count();
$PipeLineCountFaild = DB::table('pipeline')->where('Source', '=', @$Source->Source)->where('PipeId', '=', $CompanyNum.'99')->where('CompanyNum', '=', $CompanyNum)->Orwhere('Source2', '=', @$Source->Source2)->where('PipeId', '=', $CompanyNum.'99')->where('CompanyNum', '=', $CompanyNum)->groupBy('Source','Source2')->count();
?>
<tr>

<td><?php echo @$Source->Source; ?><?php echo @$Source->Source2; ?><?php if (@$Source->Source == '' && @$Source->Source2 == '') {echo 'ללא שם';} ?></td>
            <?php 
			$LeadsStstuss = DB::table('leadstatus')->where('Status', '=', '0')->orderBy('Sort', 'ASC')->get();
			foreach ($LeadsStstuss as $LeadsStstus) {
				$PipeLineCount = DB::table('pipeline')->where('PipeId', '=', @$LeadsStstus->id)->where('Source', '=', @$Source->Source)->where('CompanyNum', '=', $CompanyNum)->groupBy('Source')->count();
            echo '<td style="text-align:right;">'.@$PipeLineCount.'</td>';
            }
			?>
		
<td><?php echo @$PipeLineCountSuccess; ?></td>
<td><?php echo @$PipeLineCountFaild; ?></td>
<td><?php echo @$PipeLineCountTotal; ?></td>
</tr>
<?php
	}
	?>
</tbody>

</table>


</div>

</div></div></div></div></div></div></div>
    
    
    
    
<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage ('הגישה נחסמה', 'סליחה, אין לך הרשאות לגשת לעמוד זה.'); ?>
<?php endif ?>


<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>