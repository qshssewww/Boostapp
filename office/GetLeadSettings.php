<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('ManageStatus')): ?>
<?php

$Items = DB::table('leadstatus')->orderBy('Status', 'ASC')->get();
$resultcount = count($Items);


//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-filter' aria-hidden='true'></i> ".$LogUserName." נכנס ל<a href='StatusList.php' target='_blank'>משיכת לידים</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	

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
	        "scrollY":        "450px",
            "scrollCollapse": true,
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
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'סטטוסי לידים', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'סטטוסי לידים' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
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



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-sync"></i> תנאים למשיכת ליד אוטומטי <span style="color:#0074A4;"><?php echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<a href="#" data-ip-modal="#GetLeadTermsPopup" class="btn btn-info btn-block" name="Items"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> תנאי חדש</a>
</div>


</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active">הגדרות</li>
  <li class="breadcrumb-item active">תנאים למשיכת ליד אוטומטי</li>
  </ol>  
</nav>    


<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-hand-rock"></i> <b>תנאים למשיכת ליד אוטומטי</b>
 	</div>    
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
				<th class="text-right">כותרת התנאי</th>
				<th class="text-right">הגדרות התנאי</th>
				<th class="text-right">סטטוס</th>
                <th class="text-right lastborder">פעולות</th>
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

?>        
        <tr>
        <td class="text-right"><?php echo $i?></td>
        <td class="text-right"><?php echo $Item->Status ?></td>
        <td class="text-right"><?php echo $Item->Status ?></td>
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-info"><i class="fa fa-eye"></i> פעיל</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> מוסתר</span>'; } ?></td>
        <td class="text-right"><a class="btn btn-info btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateGetLeadTerms("<?php echo $Item->id; ?>");'>ערוך תנאי</a></td>
        </tr>
        
        
        
 <?php
    
  ++ $i; } ?>       
        

        </tbody>
	
	
        </table> 
    
        </div>
    </div>

	</div> 
</div>

</div>




<!-- DepartmentsPopup -->
	<div class="ip-modal" id="GetLeadTermsPopup">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">תנאי חדש</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="AddGetLeadTerms"  class="ajax-form clearfix">
                <div class="form-group" dir="rtl">
                <label>כותרת התנאי</label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="כותרת התנאי">
                </div>     
                <div class="form-group" dir="rtl">
                <label>תנאי 1</label>
                <select class="form-control" name="Where" id="Where">
                	<option value="Agents">נציג</option>
                	<option value="Status">סטטוס ליד</option>
                	<option value="Source">מקור הליד</option>
                	<option value="Date">תאריך הליד</option>
                	<option value="Phone">טלפון</option>
                	<option value="Email">דואר אלקטרוני</option>
                </select>
                <select class="form-control" name="Condition" id="Condition">
                	<option value="=">שווה</option>
                	<option value="!=">שונה</option>
                	<option value="!=">קטן מ</option>
                	<option value="<=">קטן שווה מ</option>
                	<option value="!=">גדול מ</option>
                	<option value=">=">גדול שווה מ</option>
                	<option value="like">מכיל</option>
                </select>
                <input type="text" class="form-control" name="Filter2" id="Filter2" style="display:none;">
                <select class="form-control" name="Filter" id="Filter">
                <optgroup label="Agents" style="display: none;">
                	<option value="">יוסי קליין</option>
                	<option value="">אפי רביבו</option>
                </optgroup>
                <optgroup label="Status" style="display: none;">
                	<option value="">ליד חדש</option>
                	<option value="">ליד חם</option>
                	<option value="">ביטל</option>
                	<option value="">רכש</option>
                </optgroup>
                <optgroup label="Source" style="display: none;">
                	<option value="">פייסבוק</option>
                	<option value="">וובינר</option>
                </optgroup>
                <optgroup label="Date" style="display: none;">
                	<option value="">תאריך של היום</option>
                	<option value="">תאריך של לפני שבוע</option>
                	<option value="">תאריך של עוד שבוע</option>
                	<option value="">תאריך של לפני חודש</option>
                	<option value="">תאריך של עוד חודש</option>
                </optgroup>
                </select>
                </div>     

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end DepartmentsPopup -->

<script>
$("#Where").on("change", function() {
    $states = $("#Filter");
	$fdg = $('#Where').val()
	if ($fdg == 'Phone' || $fdg == 'Email') {
		$("#Filter").hide();
		$("#Filter2").show();
		console.log ('הוחלף לאינפוט');
	}
	else {
		$("#Filter2").hide();
		$("#Filter").show();
    $states.find("optgroup").hide().children().hide();
    $states.find("optgroup[label='" + this.value + "']").show().children().show();
    $states.find("optgroup[label='" + this.value + "'] option").eq(0).prop("selected", true);
				console.log ('הוחלף לסלקט');

	}
});
</script>


<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="GetLeadTermsEditPopup" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">עריכת סטטוס</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="GetLeadTermsStatus"  class="ajax-form clearfix">
<input type="hidden" name="ItemId">
<div id="result">


  
</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<script> 

$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#GetLeadTermsPopup').imgPicker({
			});
			// Header setup
			$('#GetLeadTermsEditPopup').imgPicker({
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

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>