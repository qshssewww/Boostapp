<?php 
require_once '../app/init.php';

if (Auth::check()):
if (Auth::userCan('PaymentPageBuyers')): 

$CompanyNum = Auth::user()->CompanyNum;
if (empty($_GET['u'])) redirect_to(App::url());
$PageInfo = DB::table('payment_pages')->where('CompanyNum', $CompanyNum)->where('id', $_GET['u'])->first();
$pageTitle = 'רוכשים בדף הסליקה :: '.$PageInfo->Title;
require_once '../app/views/headernew.php'; 

$Pages = DB::table('docs')->where('CompanyNum', $CompanyNum)->where('TypeHeader', '400')->where('PageId', $_GET['u'])->orderBy('UserDate', 'ASC')->get();
$resultcount = count($Pages);
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
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         true,
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: "Bfrtip",
		//info: true,
	           "columnDefs": [
            {
                "targets": [ 11 ],
                "visible": false,
            },
 	],
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'רוכשים בדף הסליקה <?php echo $PageInfo->Title; ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 11 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'רוכשים בדף הסליקה <?php echo $PageInfo->Title; ?>' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 11 ]}},
           // 'pdfHtml5'
		
			
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
	
	
	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">


<div class="row">
<?php include("ItemsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-th"></i> <b>רוכשים בדף הסליקה: <?php echo $PageInfo->Title; ?> <span class="test-primary"><?php echo $resultcount; ?> </span></b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right" width="20px" style="text-align: right;">#</th>
                <th class="text-right" width="80px;" style="text-align: right;">שם מלא</th>
                <th class="text-right" width="80px;" style="text-align: right;">תעודת זהות</th>
                <th class="text-right" width="80px;" style="text-align: right;">טלפון</th>
				<th class="text-right" width="20px;" style="text-align: right;">דואר אלקטרוני</th>
                <th class="text-right" width="20px;" style="text-align: right;">סכום ששולם</th>
                <th class="text-right" width="20px;" style="text-align: right;">תאריך רכישה</th>
			</tr>
		</thead>
		<tbody>
        
        <?php 
		
$i = '1';

foreach ($Pages as $Page) {

?>
        <tr>
        <td><?php echo $i; ?></td>      
        <td><a href="ClientProfile.php?u=<?php echo $Page->ClientId; ?>"><?php echo $Page->ContactName; ?></a></td>    
        <td><?php echo $Page->CompanyId; ?></td>    
        <td><?php echo $Page->Mobile; ?></td>    
        <td><?php echo $Page->Email; ?></td>    
        <td><?php echo number_format(str_replace('-','', $Page->Amount), 2); ?> ₪</td>       
        <td><?php echo with(new DateTime(@$Page->UserDate))->format('d/m/Y'); ?></td>    
        </tr>
        
      
    <?php ++ $i;  } ?>
        

        </tbody>
	
	
        </table> 
		</div></div>
    
	</div> 
</div>

</div>





<script> 

	function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}
	
	
$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#StatusPopup').imgPicker({
			});
			// Header setup
			$('#StatusEditPopup').imgPicker({
			});
	
});


</script>
<script>
$(function() {
			
			// Header setup
			$('#AddTechPopup').imgPicker({
			});

	
});
</script>

<?php
require_once '../app/views/footernew.php';

else:
    redirect_to('../index.php');
endif;

endif;

if (Auth::guest()):

    redirect_to('../index.php'); 

endif;

?>