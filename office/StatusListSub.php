<?php 
require_once '../app/init.php';

if (Auth::check()):
if (Auth::userCan('49')):

$CompanyNum = Auth::user()->CompanyNum;
$PipeId = $_REQUEST['Id'];

$Items = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId','=', $PipeId)->orderBy('Sort', 'ASC')->orderBy('Status', 'ASC')->get();
$resultcount = Count($Items);

$PipeInfo = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('id','=', $PipeId)->first();
$pageTitle = $PipeInfo->Title.' :: '.lang('lead_status');
require_once '../app/views/headernew.php';
CreateLogMovement('נכנס לניהול סטטוסי לידים', '0');

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
	 
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'סטטוסי לידים', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'סטטוסי לידים' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
           // 'pdfHtml5'
		
			
        ],

	//	order: [[0, 'DESC']]

	   	 	   
        } );
		
	
	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<!-- <div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-align-right"></i> סטטוסי לידים <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php //if (Auth::userCan('48')): ?>     
<a href="#" data-ip-modal="#StatusPopup" class="btn btn-success btn-block" name="Items"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> סטטוס ליד חדש</a>
<?php //endif; ?>    
</div>


</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item"><a href="StatusList.php" class="text-dark">PIPELINE - <?php //echo $PipeInfo->Title; ?></a></li>      
  <li class="breadcrumb-item active" aria-current="page">סטטוסי לידים</li>
  </ol>  
</nav>     -->
<?php if (Auth::userCan('48')): ?>    
    <a href="javascript:;" data-ip-modal="#StatusPopup" class="floating-plus-btn d-flex bg-primary" title="סטטוס ליד חדש">
		<i class="fal fa-plus fa-lg margin-a"></i>
	</a>
<?php endif; ?> 

<div class="row">
<?php include_once("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-align-right"></i> <b><?php echo lang('lead_status') ?> <span class="text-primary"><?php echo $resultcount; ?> </span></b>
 	</div>
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right"><i class="fas fa-sort"></i></th>
                <th class="text-right">PIPELINE</th>
				<th class="text-right">כותרת הסטטוס</th>
				<th class="text-right">סטטוס</th>
                <th class="text-right">ID</th>
                <th class="text-right lastborder">פעולות</th>
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

?>        
        <tr id="recordsArray_<?php echo $Item->id; ?>">
		<td style="cursor:move;" title="לחץ וגרור כדי לשנות את הסדר" class="text-right"><i class="fas fa-sort" title="לחץ וגרור כדי לשנות את הסדר"></i></td>
        <td><?php echo $PipeInfo->Title; ?></td>    
        <td class="text-right"><?php echo $Item->Title ?></td>
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-dark"><i class="fa fa-eye"></i> מוצג</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> מוסתר</span>'; } ?></td>
        <td class="text-right"><?php echo $Item->id; ?></td>    
        <td class="text-right disable-sort-item">
        <?php if (Auth::userCan('48')): ?>     
		<?php if ($Item->Act=='0') { ?>	
		<a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateStatus("<?php echo $Item->id; ?>");'>ערוך סטטוס</a>
		<?php } ?>	
        <?php endif; ?>    
		</td>
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
	<div class="ip-modal" id="StatusPopup">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo $PipeInfo->Title; ?> :: סטטוס חדש</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="AddStatus"  class="ajax-form clearfix">
    
              <input type="hidden" name="PipeId" value="<?php echo $PipeId; ?>">
   
                <div class="form-group" dir="rtl">
                <label>כותרת הסטטוס</label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="כותרת הסטטוס">
                </div>     

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="StatusEditPopup" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo $PipeInfo->Title; ?> :: עריכת סטטוס</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="EditStatus"  class="ajax-form clearfix">
<input type="hidden" name="ItemId">
<div id="result">


  
</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<script> 
	
	
	
$(document).ready(function(){ 
						   
	$(function() {
		$("#categories tbody").sortable({ opacity: 0.6, cancel: ".disable-sort-item", cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings&PipeId=<?php echo $PipeId; ?>'; 
			$.post("DragDrop/StatusList.php", order, function(theResponse){
			BN('0', 'המיקום עודכן בהצלחה!');
			}); 															 
		}								  
		});
	});

});			
	

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

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>