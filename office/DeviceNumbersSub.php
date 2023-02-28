<?php 
require_once '../app/init.php';
$pageTitle = 'ניהול מספור מכשירים';
require_once '../app/views/headernew.php';

if(Auth::check()) :
if (Auth::userCan('9')):

$CompanyNum = Auth::user()->CompanyNum;
$NumbersId = $_REQUEST['Id'];

$Items = DB::table('numberssub')->where('CompanyNum','=', $CompanyNum)->where('NumbersId','=', $NumbersId)->orderBy('Status', 'ASC')->get();
$resultcount = count($Items);

$NumbersInfo = DB::table('numbers')->where('CompanyNum','=', $CompanyNum)->where('id','=', $NumbersId)->first();

CreateLogMovement('נכנס לניהול מספור מכשירים', '0');

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
        <?php if (Auth::userCan('98')): ?>
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'מספור מכשירים', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'מספור מכשירים' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		

	
	
	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">


<?php if (Auth::userCan('10')) { ?>    
	<a href="javascript:;" data-ip-modal="#MemberShipPopup" class="floating-plus-btn d-flex bg-primary" title="הוספת מכשיר חדש">
    	<i class="fal fa-plus fa-lg margin-a"></i>
	</a>
<?php } ?>

<div class="row">
<?php include_once "SettingsInc/RightCards.php"; ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-sync"></i> <b>מספור מכשירים <span class="text-primary"><?php echo $resultcount; ?></span></b>
 	</div>    
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
				<th class="text-right">סוג מכשיר</th>
                <th class="text-right">מספור מכשיר</th>
				<th class="text-right">סטטוס</th>
                <?php if (Auth::userCan('10')): ?>  
                <th class="text-right lastborder">פעולות</th>
                <?php endif ?>
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

?>        
        <tr>
        <td class="text-right"><?php echo $i?></td>
        <td class="text-right"><?php echo $NumbersInfo->Name; ?></td>    
        <td class="text-right"><?php echo $Item->Name ?></td>
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-dark"><i class="fa fa-eye"></i> פעיל</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> מוסתר</span>'; } ?></td>
        <?php if (Auth::userCan('10')): ?>      
        <td class="text-right"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:updateDeviceNumbersSub("<?php echo $Item->id; ?>");'>ערוך מכשיר</a></td>
        <?php endif ?>    
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
	<div class="ip-modal" id="MemberShipPopup">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo $NumbersInfo->Name; ?> :: הוסף מכשיר חדש</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="AddDeviceSub"  class="ajax-form clearfix" autocomplete="off">
    
    <input type="hidden" name="NumbersId" value="<?php echo $NumbersId; ?>">

                <div class="form-group" dir="rtl">
                <label>כותרת המכשיר</label>
                <input type="text" name="Type" id="Type" class="form-control">
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
	<div class="ip-modal" id="EditMemberShipPopup" tabindex="-1">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo $NumbersInfo->Name; ?> :: עריכת מכשיר קיים</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="EditDeviceSub"  class="ajax-form clearfix" autocomplete="off">
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

$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#MemberShipPopup').imgPicker({
			});
			// Header setup
			$('#EditMemberShipPopup').imgPicker({
			});
	
});


</script>


<?php
require_once '../app/views/footernew.php';

else: 
 redirect_to('../index.php'); 
endif;

endif;

?>