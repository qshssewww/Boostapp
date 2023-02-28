<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-tasks' aria-hidden='true'></i> ".$LogUserName." נכנס לצפייה בנגיעות אחרונות ";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
?>


<?php

$Reminders = DB::table('log')->where('UserId', '=', $LogUserId)->where(function($query){$query->where('ClientId', '!=', '0')->Orwhere('ClientId', '!=', '');})->orderBy('Dates', 'DESC')->groupBy('ClientId')->limit('60')->get();
$StoreCount = count($Reminders);



?>



<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.css" rel="stylesheet">
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
	   <?php if (Auth::userCan('DownloadDataTable')): ?>
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'נגיעות אחרונות', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'נגיעות אחרונות' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		
			
        ],
	   <?php endif ?>

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
<div class="col-md-12 col-sm-12">
<div class="row">



<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-hand-point-up"></i> נגיעות אחרונות</span>
</div>
</h3>
</div>














</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active">נגיעות אחרונות</li>
  </ol>  
</nav>    


<div class="row">
<div class="col-md-12 col-sm-12">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-hand-point-up fa-fw"></i> <b>נגיעות אחרונות</b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
                <th class="text-right">שם לקוח</th>
				<th class="text-right">תאריך</th>
				<th class="text-right">שעה</th>
                <th class="text-right lastborder">פעולות</th>
			</tr>
		</thead>
		<tbody>
<?php 
			
$t = $StoreCount;			 
foreach ($Reminders as $Reminder) {	
	$Client = DB::table('client')->where('id', '=', $Reminder->ClientId)->first(); 	
	$Leads = DB::table('leads')->where('ClientId', '=', $Reminder->ClientId)->first(); 	

?>        
        <tr>
        <td><?php echo $t; ?></td>
        <td><a href="ClientProfile.php?u=<?php echo @$Client->id; ?>" target="_blank" name="widget2" data-toggle="tooltip" data-placement="top" title="נהל לקוח"><?php echo @$Client->CompanyName; ?></a></td>
		<td><?php echo with(new DateTime($Reminder->Dates))->format('d/m/Y'); ?></td>
		<td><?php echo with(new DateTime($Reminder->Dates))->format('H:i:s'); ?></td>
        <td>
<div class="dropdown">
  <button class="btn btn-info dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    פעולות
  </button>
  <div class="dropdown-menu text-right dropdown-menu-right" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="javascript:ViewCallsLog('<?php echo @$Client->id; ?>');">פתקים</a>
    <a class="dropdown-item" href="javascript:ViewTaskLog('<?php echo @$Client->id; ?>');">פעילויות</a>
    <a class="dropdown-item" href="javascript:ViewInfoLog('<?php echo @$Client->id; ?>');">מידע כללי</a>
    <a class="dropdown-item" href="javascript:ViewLeadLog('<?php echo @$Client->id; ?>');">לוג</a>
  </div>
</div>
		</td>
        </tr>
<?php 

-- $t; } ?>  
        </tbody>
	
	
	<tfoot>
            <tr>
                <th><span>#</span></th>
                <th><span>שם לקוח</span></th>
				<th><span>תאריך</span></th>
				<th><span>שעה</span></th>
                <th class="lastborder"><span>פעולות</span></th>
            </tr>
        </tfoot>
	
        </table> 
		</div></div>
    
	</div> 
</div>

</div>

<?php include('InfoPopUpInc.php'); ?>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>