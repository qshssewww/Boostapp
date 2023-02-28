<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('144')): ?>

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

 var table = $('#categories');    

    
	
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
            "paging":         false,
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
//		    serverSide: true,
//	        pageLength: 5000,
	      dom: "Bfrtip",
		//info: true,
	   
	    buttons: [
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'שיבוץ קבוע', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'שיבוץ קבוע' , className: 'btn btn-dark'},
            {extend: 'print', text: 'הדפסה <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
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
           url: 'FormsReportPost.php',
           method: 'POST',
           },
       
//        serverSide: true,
		order: [[0, 'DESC']]

	   	 	   
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

 $('#table-filterCity').on('change', function(){
       table.column('2').search(this.value).draw();   
}); 
	
 $('#table-filterCityW').on('change', function(){
       table.column('4').search(this.value).draw();   
}); 	
	
	
 $('#table-filterArrived').on('change', function(){
       table.column('6').search(this.value).draw();   
}); 
	
$('#table-filterWorkout').on('change', function(){
       table.column('7').search(this.value).draw();   
}); 	
    
});
    
  


</script>


<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-calendar-alt"></i> שאלון מגורים </span>
</div>
</h3>
</div>


<div class="col-md-2 col-sm-12 order-md-2 pb-1">  
סתם בא לי לקשקש פה	
</div>       

</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">דוחות</li>
  <li class="breadcrumb-item active">שאלון מגורים</li>
  </ol>  
</nav>    


<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-calendar-alt"></i> <b>שאלון מגורים</b>
 	</div>    
  	<div class="card-body">       
                    
<div class="row">
<div class="col-md-9 col-sm-12">

</div>
<div class="col-md-3 col-sm-12">

</div>
	</div>
<hr>


<table class="table table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead >
			<tr class="bg-dark text-white">
				<th class="text-right">לקוח</th>
                <th class="text-right">טלפון</th>
                <th class="text-right">עיר מגורים</th>
                <th class="text-right">כתובת מגורים</th>
                <th class="text-right">עיר עבודה</th>
				<th class="text-right">כתובת עבודה</th>
                <th class="text-right">הגעה</th>
				<th class="text-right">העדפה</th>
                <th class="text-right lastborder">ת.יצירה</th>
			</tr>
            
          
            
            
		</thead>
		<tbody>
  

        </tbody>

<tfoot>
<tr class="bg-white text-black filterHeader">
                <th><span>לקוח</span></th>
                <th><span>טלפון</span></th>
                <th><select id="table-filterCity" class="form-control selectCity2" style="width:100%;">
<option value="">הכל</option>
<?php 
$CalTypes = DB::table('cities')->get();
foreach ($CalTypes as $CalType) {                    
?>
<option><?php echo $CalType->City; ?></option>                    
<?php } ?>                   
</select></th>    
                <th><span>כתובת מגורים</span></th>
<th>
<select id="table-filterCityW" class="form-control selectCity2" style="width:100%;" >
<option value="">הכל</option>
<?php 
$CalTypes = DB::table('cities')->get();
foreach ($CalTypes as $CalType) {                    
?>
<option><?php echo $CalType->City; ?></option>                    
<?php } ?>                   
</select>	
</th>
                <th><span>כתובת עבודה</span></th>
                <th><select id="table-filterArrived" class="form-control">
<option value="">הכל</option>
<option>רכב פרטי</option>
<option>תחבורה ציבורית</option>	
<option>קורקינט/אופניים</option>	
<option>ברגל</option>							
</select></th>
<th><select id="table-filterWorkout" class="form-control">
<option value="">הכל</option>
<option>קרוב הביתה</option>
<option>קרוב למקום העבודה</option>					
</select></th>
                <th><span>ת.יצירה</span></th>
            </tr>      
    
</tfoot>
	
        </table> 
    
        </div>
    </div>

	</div> 
</div>

</div>


<script>
//$( ".selectCity2" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl" } );
</script>



<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>