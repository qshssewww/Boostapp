
<?php require_once '../app/init.php'; ?>

<?php

$SaleId = @$_REQUEST['SaleId'];
$Status = @$_REQUEST['Status'];
$Act = @$_REQUEST['Act'];

if ($Act=='All'){
$Leads = DB::table('leads')->where('Seller', '=', $SaleId)->orderBy('id', 'DESC')->groupBy('Phone')->get();
}
elseif ($Status != ''){
$Leads = DB::table('leads')->where('Seller', '=', $SaleId)->where('Status', '=', $Status)->orderBy('id', 'DESC')->groupBy('Phone')->get();
}


$LeadsCount = count($Leads);





//LOG
$SellerNameForLog = DB::table('users')->where('id', '=', $SaleId)->first();
if ($Act=='All') {
	$ActLog = 'טבלת לידים כללית';
}
elseif ($Status != '') {
	$StatusesForLog = DB::table('leadstatus')->where('id', '=', $Status)->first();
	$ActLog = 'טבלת לידים בסטטוס '.$StatusesForLog->Status;
}
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-list-alt' aria-hidden='true'></i> ".$LogUserName." נכנס לצפות ב".@$ActLog." של הנציג <a href='SalesProfile.php?u=".$SaleId."' target='_blank'>".$SellerNameForLog->display_name."</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//LOG





?>


<script>
$(document).ready(function(){
	

	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'"  />' );

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
		   // processing: true,
	         fixedHeader: {
        headerOffset: 50
    },
	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: "Bfrtip",
		info: true,
	    buttons: [
			{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fa fa-file-excel-o" aria-hidden="true"></i>', filename: 'לידים נגועים', className: 'btn btn-warning'},
			{extend: 'csvHtml5', text: 'CSV <i class="fa fa-file-code-o" aria-hidden="true"></i>', filename: 'לידים נגועים' , className: 'btn btn-danger'},
           // 'pdfHtml5'
		
			
        ],
	   
		ajax: { url: 'SalesLeadPost.php?Status=<?php echo $Status; ?>&SaleId=<?php echo $SaleId; ?>&Act=<?php echo $Act; ?>', },
	   
	//	order: [[0, 'DESC']]
	   
	   initComplete: function () {
            this.api().columns([5,6]).every( function () {
                var column = this;
                var select = $('<select class="form-control"><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
	if (d==''){} else {				
    if(column.search() === '^'+d+'$'){
        select.append( '<option value="'+d+'" selected="selected">'+d+'</option>' )
    } else {
        select.append( '<option value="'+d+'">'+d+'</option>' )
    }
	}
} );
            } );
        },
	   	   
	   
        } );
		
		    var table = $('#categories').DataTable();
			table.columns().every( function () {
            var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
			
			
			
            if ( that.search() !== this.value ) {
                that
				   // .columns( [0,5] )
                    table.search( this.value )
                    .draw();				
            }
        } );
    } );
	
	
	
});


</script>

<style>
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>


<div class="row" style="padding: 20px;">



<h4><i class="fa fa-paper-plane" aria-hidden="true"></i> לידים
<span style="color:#0074A4;"><?php echo $LeadsCount; ?></span>
</h4>


<div dir="ltr">
	 <div id="updatestatustext" class="alert alert-warning" dir="rtl" style="margin-top:20px; display:none;">
 אנא המתן בזמן עיבוד הנתונים...
</div>

<table class="table table-striped table-bordered table-hover table-dt display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>מספר לקוח</th>
                <th>שם לקוח</th>

				<th>טלפון</th>
				<th>דוא"ל</th>
                <th>ת.הצטרפות</th>

                <th>נציג מכירות</th>
                
               <th>סטטוס</th>
			</tr>
		</thead>
		<tbody>
              
        </tbody>
	
	
	<tfoot>
            <tr>
                <th><span>מספר לקוח</span></th>
                <th><span>שם לקוח</span></th>
			
				<th><span>טלפון</span></th>
				<th><span>דואל</span></th>
                <th><span>ת.הצטרפות</span></th>
        
                <th><span>נציג מכירות</span></th>
                <th><span>סטטוס</span></th>

            </tr>
        </tfoot>
	
        </table> 
	
	</div>	
</div>








<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewCallsLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">תיעוד שיחות</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">

					
					
<form action="AddCRMPP"  class="ajax-form clearfix">
 <input type="hidden" name="ClientId">
<div id="resultViewCallsLog">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
					
					
					
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->





<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewTaskLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">משימות מתוזמנות ללקוח</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="AddReminderPP"  class="ajax-form clearfix">
 <input type="hidden" name="ClientId">
<div id="resultViewTaskLog">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewInfoLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">מידע כללי</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<div id="resultViewInfoLog">


  
</div>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewLeadLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">לוג לקוח</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<div id="resultViewLeadLog">


  
</div>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<script>
$( ".select4" ).select2( { placeholder: "Select a State", maximumSelectionSize: 6,language: "he" } );
    
function func(selectedValue)
 {
    //make the ajax call
    $.ajax({
        url: 'action/SaveStatus.php',
        type: 'POST',
        data: {option : selectedValue},
        success: function() {
            console.log("Data sent!");
			$("#updatestatustext").show();
			setTimeout(function() {$('#updatestatustext').fadeOut('fast');}, 1000); 
        }
    });
}    
    
</script>


