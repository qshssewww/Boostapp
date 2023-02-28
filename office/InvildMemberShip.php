<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('22')): ?>
<?php



if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $next_year.'-'.$next_month.'-01';

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-filter' aria-hidden='true'></i> ".$LogUserName." נכנס ל<a href='LogList.php' target='_blank'>מנויים לא בתוקף</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	


$resultcount = DB::table('client_activities')
    ->where('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
    ->Orwhere('TrueBalanceValue','<=', '0')->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=','2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=','2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
    ->count();


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

<script type="text/javascript" charset="utf-8">     

function myFunction(value)
{

window.location.href = 'LogList.php?Dates='+value;

}

</script>



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
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'מנויים לא בתוקף', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'מנויים לא בתוקף' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	   		ajax: { url: 'InvildMemberShipPost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },

		order: [[0, 'DESC']],
        columnDefs: [ {
            targets: 6,
            type: 'iso-date'
        }]
	   	 	   
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

_isoDateSort = function(a, b) {
	var a = moment(a, 'DD/MM/YYYY').unix();
    var b = moment(b, 'DD/MM/YYYY').unix();
    
	return ((a < b) ? -1 : ((a > b) ? 1 : 0));    
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
	"iso-date-asc": function (a, b) {
		return _isoDateSort(a, b);
	},
	"iso-date-desc": function (a, b) {
		return _isoDateSort(a, b) * -1;
	}
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
<i class="fas fa-filter"></i> מנויים לא בתוקף <span style="color:#48AD42;"><?php echo $resultcount; ?> </span>
</div>
</h3>
</div>


</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="Client.php" class="text-dark">לקוחות</a></li>      
  <li class="breadcrumb-item active">מנויים</li>
  <li class="breadcrumb-item active">מנויים לא בתוקף</li>
  </ol>  
</nav>    


<div class="row">

<div class="col-md-12 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-filter"></i> <b>מנויים לא בתוקף</b>
 	</div>    
  	<div class="card-body">       
                    

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
				<th class="text-right">שם לקוח</th>
				<th class="text-right">ת.ז</th>
				<th class="text-right">טלפון</th>
                <th class="text-right">סוג</th>
				<th class="text-right">מנוי</th>
                <th class="text-right">תוקף</th>
                <th class="text-right">שיעורים</th>
                <th class="text-right">פעולות</th>
			</tr>
		</thead>
		<tbody>
  

        </tbody>
		<tfoot>
            <tr>
                <th><span>#</span></th>
                <th><span>שם לקוח</span></th>
				<th><span>ת.ז</span></th>
				<th><span>טלפון</span></th>
                <th><span>סוג</span></th>
				<th><span>מנוי</span></th>
                <th><span>תוקף</span></th>
                <th><span>שיעורים</span></th>
                <th></th>
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