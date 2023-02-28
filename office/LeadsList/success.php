<?php require_once '../../app/initcron.php'; 

$CompanyNum = Auth::user()->CompanyNum;


if (!isset($_REQUEST["startDate"])) $_REQUEST["startDate"] = date( 'Y-m-01' ,strtotime ( '-1 year' , strtotime(date('Y-m-d')) ) );
if (!isset($_REQUEST["endDate"])) $_REQUEST["endDate"] = date("Y-m-t");

if(!empty($_REQUEST['startDate'])) $StartDate = $_REQUEST['startDate'];
if(!empty($_REQUEST['endDate'])) $EndDate = $_REQUEST['endDate'];

$EndDate = date('Y-m-d', strtotime('+1 day', strtotime($EndDate)));
$NewTask = '0';

$TypeRanges = @$_REQUEST['TypeRanges'];
if ($TypeRanges==''){
$TypeRanges = '0';    
}

?>    
       <style>
       
           .DivScroll::-webkit-scrollbar {
             width: 2px;
             padding-left: 0px;
             margin-left: 0px;
           } 
           
             .DivScroll::-webkit-scrollbar-thumb {
             background-color: darkgrey;
             outline: 1px solid slategray;
            padding-left: 0px;
             margin-left: 0px;     
           }    
           
       
       </style>  

    <div class="card spacebottom">
    <div class="card-header text-start d-flex justify-content-between" >
    <span >    
    <i class="fas fa-trophy fa-fw"></i> <b>ניהול מתעניינים - הצלחות</b>
    </span>    
    <span >
	<input id="dateRanges" type="text" placeholder="חפש לפי טווח תאריכים" class="dateRanges form-control" autocomplete="off">		
	</span> 
    
    <span >
	<select id="TypeRanges" name="TypeRanges" class="form-control">
    <option value="0" <?php if ($TypeRanges=='0') { echo 'selected'; } else {} ?> >ת.יצירה</option> 
    <option value="1" <?php if ($TypeRanges=='1') { echo 'selected'; } else {} ?> >ת.המרה</option>     
    </select>		
    </span>     
    
        
 	</div>    
  	<div class="card-body">       

        
        
        
        
        
        
        
<table class="table table-hover dt-responsive text-start display wrap" id="LeadsSuccess"  cellspacing="0" width="100%">
		<thead>
			<tr class="bg-dark text-white">
				<th class="text-start" width="10px;">#</th>
                <th class="text-start">שם לקוח</th>
				<th class="text-start">טלפון</th>
                <th class="text-start">דוא"ל</th>
                <th class="text-start ">סניף</th>
                <th class="text-start">PIPELINE</th>
                <th class="text-start">מקור הגעה</th>
                <th class="text-start">נציג</th>
                <th class="text-start">ת.יצירה</th>
                <th class="text-start" lastborder>ת.המרה</th>
			</tr>
		</thead>
		<tbody>
           
<?php 
if ($TypeRanges=='0'){
    $LeadsSuccess = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->where('StatusFilter', '=', '1')->whereBetween('Dates', array($StartDate, $EndDate))->orderBy('Dates', 'DESC')->get();
}
else {
    $LeadsSuccess = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->where('StatusFilter', '=', '1')->whereBetween('ConvertDate', array($StartDate, $EndDate))->orderBy('ConvertDate', 'DESC')->get();
}

//$LeadsSuccess = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->where('StatusFilter', '=', '1')->whereBetween('Dates', array($StartDate, $EndDate))->orderBy('Dates', 'DESC')->get();
$LeadsSuccessShow = [];
foreach ($LeadsSuccess as $success){
    $ClientLeadStatus = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('id','=',$success->PipeId)->first();
    if(!empty($ClientLeadStatus) && $ClientLeadStatus->Act == 1){
        array_push($LeadsSuccessShow,$success);
    }
}



foreach ($LeadsSuccessShow as $LeadsOpen) {
    
$ClientInfo = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->ClientId)->first(); 
    
$PipeInfo = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->MainPipeId)->first(); 
$PipeStatusInfo = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->PipeId)->first(); 
    
$UserInfo = DB::table('users')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->AgentId)->first();   $DataPopUp = '';  
$PipeLineColor = $LeadsOpen->StatusColor;
 
    if ($LeadsOpen->Tasks==''){
		
	$DataPopUp = '';
    $LeadTasks = '<i class="fas fa-exclamation" style="color:'.$PipeLineColor.'" data-toggle="tooltip" title="לא הוגדרו משימות כלל"></i>';     
		
	} else { 
		
	$Loops =  json_decode($LeadsOpen->Tasks,true);	
    foreach($Loops['data'] as $key=>$val){ 
		
    if ($val['Date'] < date('Y-m-d') || $val['Date'] == date('Y-m-d') && $val['Time'] < date('H:i:s')){
	$ColorRed = '#ff8080';
	$textColor = 'text-danger';	
	}	
	
	else {
	$textColor = 'text-success';		
	}	

		
	$DataPopUp .= "<div class='row ".$textColor."'>
				  <div class='col-12' onclick='javascript:NewCal(".$val['Id'].",".$LeadsOpen->ClientId.",".$LeadsOpen->id.");' style='cursor: pointer;'><i class='".$val['Icon']." fa-xs'></i> ".$val['Title']."<br>
				  <i class='fas fa-calendar-alt fa-xs'></i> <span style='font-size: 11px;'>".date('H:i', strtotime($val['Time']))." ".date('d/m/Y', strtotime($val['Date']))."</span>
				  </div>
				  </div>
				  <hr>";		
		
		
		
	}
	
	
    $PipeLineColor = $PipeLineColor;	
	$LeadTasks = '<i class="fas fa-tasks" style="color:'.$PipeLineColor.'" data-toggle="tooltip" title="ישנם משימות מוגדרות"></i>'; 	
	}    
    
    
   
    
?>            
            
            
        <tr>
        <td><?php echo $ClientInfo->id; ?></td>
        <td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><span class="text-primary"><?php echo $ClientInfo->CompanyName; ?></span></a></td>
        <td><?php echo $ClientInfo->ContactMobile; ?></td>
        <td><?php echo $ClientInfo->Email; ?></td>     
        <td><?php echo $ClientInfo->BrandName; ?></td>
        <td><?php echo @$PipeInfo->Title; ?></td>
        <td><?php echo $LeadsOpen->Source; ?></td>
        <td><?php echo @$UserInfo->display_name; ?></td>
        <td><?php echo with(new DateTime(@$LeadsOpen->Dates))->format('d/m/Y H:i'); ?></td>
        <td><?php echo isset($LeadsOpen->ConvertDate) ? with(new DateTime($LeadsOpen->ConvertDate))->format('d/m/Y H:i') : ""; ?></td>
        </tr>    
            
<?php } ?>            
            
        </tbody>
	
	
	<tfoot>
            <tr>
                <th></th>
                <th><span>שם לקוח</span></th>
				<th><span>טלפון</span></th>
                <th><span>דוא"ל</span></th>
                <th><select id="table-filterBrands-Success" class="form-control">
<option value="">הכל</option>
<?php 
$Brands = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->get();
if (!empty($Brands)){                     
foreach ($Brands as $Brand) {                    
?>
<option><?php echo $Brand->BrandName; ?></option>                    
<?php } } else { ?> 
<option>סניף ראשי</option>                      
<?php } ?>                    
</select>  </th>
<th><select id="table-filterPipeLine-Success" class="form-control">
<option value="">הכל</option>
<?php 
$Pipes = DB::table('pipeline_category')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();       foreach ($Pipes as $Pipe) {                    
?>
<option><?php echo $Pipe->Title; ?></option>                                        
<?php } ?>                    
</select>  </th>

                
<th><select id="table-filterPipeLineSource-Success" class="form-control">
<option value="">הכל</option>
<?php 
$Pipes = DB::table('leadsource')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();
foreach ($Pipes as $Pipe) {                    
?>
<option><?php echo $Pipe->Title; ?></option>                                        
<?php } ?>                    
</select>  </th>
<th><select id="table-filterPipeLineAgent-Success" class="form-control">
<option value="">הכל</option>
<?php 
$Pipes = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->where('status', '=', '1')->get();
foreach ($Pipes as $Pipe) {                    
?>
<option><?php echo $Pipe->display_name; ?></option>                                        
<?php } ?>                    
</select>  </th>
                <th><span>ת.יצירה</span></th>
                <th class="lastborder"><span>ת.המרה</span></th>

            </tr>
        </tfoot>
	
        </table> 

        
        
        
        
        
    </div>
    </div>  

			<script>
        var direction = false ;


				(function($){
					$(document).ready(function(){
				    
            if( $("html").attr("dir") == 'rtl'){
              direction = true ;
            }

        		var dateRange = jQuery('#dateRanges');
                        
						dateRange.daterangepicker({
                            startDate: moment('<?php echo $StartDate; ?>', 'YYYY-MM-DD'),
                            endDate:  moment('<?php echo $EndDate; ?>', 'YYYY-MM-DD'),    
                            isRTL: direction,
                            langauge: 'he',
                            locale: {
                                format: 'DD/M/YY',
                                "applyLabel": "אישור",
                                "cancelLabel": "ביטול",
                            }
                        }).on('apply.daterangepicker', function(e, d){
                            var TypeRanges = jQuery('#TypeRanges').val();
                            $.get('LeadsList/success.php?TypeRanges='+TypeRanges+'&startDate='+moment(d.startDate).format('YYYY-MM-DD')+'&endDate='+moment(d.endDate).format('YYYY-MM-DD'), function(data){
                            $('#nav-success').html(data);
                            });

                        });
					})
				})(jQuery);
                
                
    		$(function() {
    $('.btnPopover').click(function(e){  
	e.stopPropagation();	
    $(this).popover({
        html: true,
        trigger: 'manual'
    }).popover('toggle');
        $('.btnPopover').not(this).popover('hide');
    }); 

}); 	            
  
	 $('body').on('click', function (e) {
    //did not click a popover toggle or popover
    if ($(e.target).data('toggle') !== 'popover'
        && $(e.target).parents('.popover.in').length === 0) { 
        $('[data-toggle="popover"]').popover('hide');
    }
});                 
                
$(function() {
			var time = function(){return'?'+new Date().getTime()};
						
			$('#AddNewLead').imgPicker({
			});
			$('#AddNewClient').imgPicker({
			});
		$('#AddNewTask').imgPicker({

			});
	
	
});	
                
$(function() {
  $('[data-toggle="tooltip"]').tooltip(); 
});
                
			</script>
<script>
$(document).ready(function(){
	
	 $('#LeadsSuccess tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );


	
	var modal = $('#SendClientPush');
    var modalsClientIds = $('input[name="clientsIds"]', modal);
    
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#LeadsSuccess').dataTable( {
            language: BeePOS.options.datatables,
       retrieve: true,
			responsive: true,
		    processing: false,
	       // autoWidth: true,
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         false,
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
//		    serverSide: true,
//	        pageLength: 5000,
	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
       'columnDefs': [
         {
            'targets': 0,
            'checkboxes': {
               'selectRow': true
            }
         }
      ],
      'select': {
         'style': 'multi'
      },
		//info: true,
	   
	    buttons: [
          
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'מתעניינים הצלחות', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'מתעניינים הצלחות' , className: 'btn btn-dark'},
            {extend: 'print', text: 'הדפסה <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
              // https://datatables.net/reference/button/print
             jQuery(win.document).ready(function(){
             $(win.document.body)
             .css( 'direction', 'rtl' )
             });                            
             }},
           // 'pdfHtml5'
		<?php endif ?>
            
        {text: 'שלח הודעה <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                            // rows_selected = table.column(0).checkboxes.selected();
                            var clientsIds = dt.column(0).checkboxes.selected().toArray();
                            if(!clientsIds.length) return alert('אנא בחר לקוחות');
      
                            modalsClientIds.val(clientsIds.join(","));
                            modal.modal('show');

          }}, 
            
			
        ],
	   
//       ajax:{
//           url: 'MedicalReporPost.php',
//           method: 'POST',
//           data: function(d){
////           d.dateFrom = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
////           d.dateTo = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
//           }
//           },
       
//        serverSide: true,
//		order: [[2, 'DESC']]

	   	 	   
        } );
		
		    var table = $('#LeadsSuccess').DataTable();  
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
	
$('#LeadsSuccess tfoot tr').insertAfter($('#LeadsSuccess thead tr'));   
        
$('#table-filterBrands-Success').on('change', function(){
table.column('4').search("^" + this.value + "$", true, false, true).draw();   
});    
    
$('#table-filterPipeLine-Success').on('change', function(){
table.column('5').search(this.value).draw();   
});   
    
$('#table-filterPipeLineSource-Success').on('change', function(){
table.column('6').search(this.value).draw();   
});   
    
$('#table-filterPipeLineAgent-Success').on('change', function(){
table.column('7').search(this.value).draw();   
});       
    
    
    
    
    
});
    
</script>

