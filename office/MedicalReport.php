<?php 
require_once '../app/init.php'; 
$pageTitle = lang('health_declaration_title');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('138')): ?>
<?php
CreateLogMovement(lang('health_declaration_log'), '0');
?>





<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
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
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.datepicker-dropdown {max-width: 300px;}
.datepicker {float: right}
.datepicker.dropdown-menu {right:auto}
</style>




<script>
$(document).ready(function(){
	
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );


	
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
	       // autoWidth: true,
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         false,
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
//		    serverSide: true,
//	        pageLength: 5000,
	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
		//info: true,
	   
	    buttons: [
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('health_declaration_title') ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('health_declaration_title') ?>' , className: 'btn btn-dark'},
            {extend: 'print', text: '<?php echo lang('print') ?> <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
              // https://datatables.net/reference/button/print
             jQuery(win.document).ready(function(){
             $(win.document.body)
             .css( 'direction', 'rtl' )
             });                            
             }},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
       ajax:{
           url: 'MedicalReporPost.php',
           method: 'POST',
           data: function(d){
//           d.dateFrom = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
//           d.dateTo = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
           }
           },
       
//        serverSide: true,
		order: [[2, 'DESC']]

	   	 	   
        } );
		
		    var table = $('#categories').DataTable();  
			table.columns().every( function () {
            var that = this;

		 $( 'span input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );		
				
		
				
    } );
	
 $('#categories tfoot tr').insertAfter($('#categories thead tr'));    
$('#table-filter').on('change', function(){
       table.column('3').search(this.value).draw();   
});  
$('#table-filterstatus').on('change', function(){
       table.column('2').search(this.value).draw();   
});      
    
    
 
    
});
    
  


</script>


<link href="assets/css/fixstyle.css" rel="stylesheet">
<!-- <div class="col-md-12 col-sm-12"> -->
<!-- <div class="row">



<div class="col-md-4 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-4 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-briefcase-medical"></i> דוח הצהרת בריאות</span>
</div>
</h3>
</div> -->


<!-- <div class="col-md-2 col-sm-12 order-md-2 pb-1">  

</div>    
    
<div class="col-md-2 col-sm-12 order-md-3">

</div>     

</div> -->

<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">הגדרות</li>
  <li class="breadcrumb-item active">דוח הצהרת בריאות</li>
  </ol>  
</nav>     -->


<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fas fa-briefcase-medical"></i> <b><?php echo lang('health_declaration_title') ?></b>
 	</div>    
  	<div class="card-body">       
                    
<div class="row">
<div class="col-md-9 col-sm-12">

</div>
<div class="col-md-3 col-sm-12">

</div>
	</div>
<hr>


<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead >
			<tr class="bg-dark text-white">
				<th class="text-start"><?php echo lang('client') ?></th>
                <th class="text-start"><?php echo lang('telephone') ?></th>
                <th class="text-start"><?php echo lang('client_status') ?></th>
                <th class="text-start lastborder"><?php echo lang('health_declaration') ?></th>
			</tr>
            
          
            
            
		</thead>
		<tbody>
  

        </tbody>

<tfoot>
<tr class="bg-white text-black filterHeader">
                <th><span><?php echo lang('client') ?></span></th>
                <th><span><?php echo lang('telephone') ?></span></th>
<th class="lastborder"><select id="table-filterstatus" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<option><?php echo lang('active') ?></option>
<option><?php echo lang('interested_single') ?></option>    
</select></th>    
<th class="lastborder"><select id="table-filter" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<option><?php echo lang('there_is') ?></option>
<option><?php echo lang('there_is_not') ?></option>
</select></th>
            </tr>      
    
</tfoot>
	
        </table> 
    
        </div>
    </div>

	</div> 
</div>

</div>






<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>