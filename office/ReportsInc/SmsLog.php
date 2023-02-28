<?php require_once '../../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('5')): ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = Auth::user()->ItemId;
?>

<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>

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
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));
?>












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
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'דוח שליחת הודעות SMS', className: 'btn btn-dark',exportOptions: {}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'דוח שליחת הודעות SMS' , className: 'btn btn-dark',exportOptions: {}},
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

<script type="text/javascript" charset="utf-8">     

function myFunction(value)
{

window.location.href = '?Dates='+value+'#SmsLog';

}

</script>



<div class="card spacebottom">
      <div class="card-header text-right" dir="rtl"><i class="fas fa-chart-pie fa-fw"></i><strong> דו״ח שליחת אסאמאסים לחודש <span style="color:#0074A4;"><?php echo $monthNames[$cMonth-1].' '.$cYear; ?></span></strong></div>    
  <div class="card-body">       
                    
    
                      
<div class="row">
<div class="col-md-12 col-sm-12">


<div class="col-md-6 col-sm-12 float-left spacebottom" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
<span style="float:left;"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"  class="btn btn-light">לחודש הקודם</a></span>

<span style="float:left; padding-left:5px;"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"  class="btn btn-light">לחודש הבא</a></span>

                            
<span style="float:left; padding-left:5px;"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', date('m')) . "&year=" . date('Y'); ?>"  class="btn btn-dark">החודש</a></span>
	</div>
<div class="col-md-6 col-sm-12 float-right" style="float:right;padding-right:0px;">

<span style="float:right;padding-right:0px;"><input type="month" class="form-control" id="CDate" value="<?php echo $Dates;?>" onChange="myFunction(this.value);"></span>  

	</div>
</div>
</div>

<hr>
     
<div class="row" dir="rtl" style="padding-left:15px; padding-right:15px;">
<table class="table table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">

<thead>
          <tr class="bg-dark text-white">
            <th style="text-align:right;">#</th>
            <th style="text-align:right;">למספר</th>
            <th style="text-align:right;">לקוח</th>
            <th style="text-align:right;">על ידי</th>
            <th style="text-align:right;">תאריך</th>
            <th style="text-align:right;">שעה</th>
            <th style="text-align:right;">מחיר להודעה</th>
            <th style="text-align:right;">כמות הודעות</th>
            <th style="text-align:right;">עלות</th>
          </tr>

</thead>

<tbody>
<?php
$SmsLogList = DB::table('appnotification')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '1')->where('System', '=', '0')->whereBetween('Date', array($StartDate, $EndDate))->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->get();
$SmsLogListSMSSumPrice = DB::table('appnotification')->where('Type', '=', '1')->where('System', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '2')->whereBetween('Date', array($StartDate, $EndDate))->sum('SMSSumPrice');
	foreach ($SmsLogList as $SmsLog) {
@$UsersDB = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$SmsLog->UserId)->first();
@$ClietsDB = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$SmsLog->ClientId)->first();
$ClientInfoMsgLog = json_decode(@$SmsLog->ClientJson);
?>
<tr>
<td><?php echo $SmsLog->id; ?></td>
<td><?php echo @$ClientInfoMsgLog->data['0']->Mobile; ?></td>
<td dir="rtl"><?php echo @$ClietsDB->CompanyName; ?> :: <?php echo @$ClietsDB->id; ?></td>
<td><?php echo @$UsersDB->display_name; ?> :: <?php echo @$UsersDB->id; ?></td>
<td><?php echo with(new DateTime($SmsLog->Date))->format('d/m/Y'); ?></td>
<td><?php echo with(new DateTime($SmsLog->Time))->format('H:i:s'); ?></td>
<?php if ($SmsLog->Status != '2') { ?>
<td><?php echo $SmsLog->SMSPrice; ?> ₪</td>
<?php } else {echo '<td>נכשל</td>';} ?>
<?php if ($SmsLog->Status != '2') { ?>
<td><?php echo $SmsLog->Count; ?></td>
<?php } else {echo '<td>נכשל</td>';} ?>
<?php if ($SmsLog->Status != '2') { ?>
<td><?php echo $SmsLog->SMSSumPrice; ?> ₪</td>
<?php } else {echo '<td>נכשל</td>';} ?>
</tr>
<?php
	}
	?>
</tbody>

<?php if (@$SmsLogListSMSSumPrice != '') { ?>
<tfoot>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td><strong><?php echo $SmsLogListSMSSumPrice; ?> ₪</strong></td>
</tr>
</tfoot>
<?php } ?>
</table>


</div>

</div></div>
<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage ('הגישה נחסמה', 'סליחה, אין לך הרשאות לגשת לעמוד זה.'); ?>
<?php endif ?>


<?php endif ?>


