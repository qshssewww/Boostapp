<?php require_once '../../app/initcron.php'; 



$CompanyNum = Auth::user()->CompanyNum;

$CheckPipe = DB::table('pipeline_category')->where('CompanyNum', '=', $CompanyNum)->orderBy('id','ASC')->first();



if (!isset($_REQUEST["startDate"])) $_REQUEST["startDate"] = date( 'Y-m-01' ,strtotime ( '-1 year' , strtotime(date('Y-m-d')) ) );

if (!isset($_REQUEST["endDate"])) $_REQUEST["endDate"] = date("Y-m-t");



if(!empty($_REQUEST['startDate'])) $StartDate = $_REQUEST['startDate'];

if(!empty($_REQUEST['endDate'])) $EndDate = $_REQUEST['endDate'];



$EndDate = date('Y-m-d', strtotime('+1 day', strtotime($EndDate)));

$NewTask = '0';

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

        <div class="card-header d-flex justify-content-between " >
        <div class="align-self-center">
            <span>     
                <i class="fas fa-align-right fa-fw"></i> <b> ניהול מתעניינים - פתוחים</b>
            </span>
        </div>    
        <div class="d-flex flex-row">
            <div class="pl-10">
                <a class="btn btn-primary" href="manage-leads.php" class="text-dark"><i class="fal fa-users-class"></i> Pipeline</a>
            </div>
            <div>
                <span>
                    <input id="dateRange" type="text" placeholder="חפש לפי טווח תאריכים" class="dateRange form-control" autocomplete="off">
                </span> 
            </div>
 	    
        </div> 

 	</div>    

  	<div class="card-body">       



        

        

        

        

        

        

        

<table class="table table-hover dt-responsive  display wrap" id="LeadsOpens"  cellspacing="0" width="100%">

		<thead>

			<tr class="bg-dark text-white">

				<th class="" width="10px;">#</th>

                <th class="">שם לקוח</th>

				<th class="">טלפון</th>

                <th class="">דוא"ל</th>

                <th class=" ">סניף</th>

                <th class="">PIPELINE</th>

                <th class="">סטטוס</th>

                <th class="">מקור הגעה</th>

                <th class="">נציג</th>

                <th class="">ת.יצירה</th>

                <th class="" lastborder>משימות</th>

			</tr>

		</thead>

		<tbody>

           

<?php 

$LeadsOpens = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->where('StatusFilter', '=', '0')->whereBetween('Dates', array($StartDate, $EndDate))->orderBy('Dates', 'DESC')->get();
//$LeadsOpens = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->where('StatusFilter', '=', '0')->get();
$LeadsOpenShow = [];
foreach ($LeadsOpens as $open){
    $ClientLeadStatus = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('id','=',$open->PipeId)->first();
    if(!empty($ClientLeadStatus) && $ClientLeadStatus->Act == 0){
        array_push($LeadsOpenShow,$open);
    }
}


foreach ($LeadsOpenShow as $LeadsOpen) {

 $DataPopUp = '';   

$ClientInfo = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->ClientId)->first(); 

if (!empty($ClientInfo->id)) {
    
    $PipeInfo = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->MainPipeId)->first(); 
    $PipeStatusInfo = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->PipeId)->first(); 
        
    $UserInfo = DB::table('users')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $LeadsOpen->AgentId)->first();   
    $PipeLineColor = $LeadsOpen->StatusColor;
     
        if (empty($LeadsOpen->Tasks)){
            
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
                <a class='col-12 js-new-task' data-task='".$val['Id']."' data-client='".$LeadsOpen->ClientId."' data-pipe-id='".$LeadsOpen->id."' style='cursor: pointer;'><i class='".$val['Icon']." fa-xs'></i> ".$val['Title']."<br>
                <i class='fas fa-calendar-alt fa-xs'></i> <span style='font-size: 11px;'>".date('H:i', strtotime($val['Time']))." ".date('d/m/Y', strtotime($val['Date']))."</span>
                </a>
                </div>
                <hr>";    
            
            
            
        }
        
        
        $PipeLineColor = $PipeLineColor;	
        $LeadTasks = '<i class="fas fa-tasks" style="color:'.$PipeLineColor.'" data-toggle="tooltip" title="משימות"></i>'; 	
        }    
        
        
       
        
    ?>            
                
                
            <tr>
    
            <td><?php echo $ClientInfo->id; ?></td>
            <td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><span class="text-primary"><?php echo $ClientInfo->CompanyName; ?></span></a></td>
            <td><?php echo $ClientInfo->ContactMobile; ?></td>
            <td><?php echo $ClientInfo->Email; ?></td>     
            <td><?php echo $ClientInfo->BrandName; ?></td>
            <td><?php echo @$PipeInfo->Title; ?></td>
            <td><?php echo @$PipeStatusInfo->Title; ?></td>
            <td><?php echo $LeadsOpen->Source; ?></td>
            <td><?php echo @$UserInfo->display_name; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($LeadsOpen->Dates));?></td>
            <td><a href="javascript:void(0);"  class="btnPopover " rel="popover" data-toggle="popover" data-html="true" data-placement="left" data-content="<div  style='width: 250px; padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>
         <div class='DivScroll text-dark' style='max-height:220px; overflow-y:scroll; overflow-x:hidden;margin: 0px; padding: 0px; '>
     <?php echo $DataPopUp; ?>
     </div>
     
    <div style='text-align: center;' align='center'>
    <?php if (Auth::userCan('48')): ?>
        <a data-task='<?php echo $NewTask; ?>' data-client='<?php echo $LeadsOpen->ClientId; ?>'  data-pipe-id='<?php echo $LeadsOpen->id; ?>' class='text-dark js-new-task'><?= lang('new_task') ?></a>
    <?php endif; ?>
     </div> 
      </div>"><?php echo $LeadTasks; ?></a></td>    
            </tr>
    <?php }

    ?>   

<?php



	$DataPopUp = '';

	$ColorRed = '';	



} ?>            

            

        </tbody>

	

	

	<tfoot>

            <tr>

                <th></th>

                <th><span>שם לקוח</span></th>

				<th><span>טלפון</span></th>

                <th><span>דוא"ל</span></th>    

                <th><select id="table-filterBrands-Opens" class="form-control">

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

<th><select id="table-filterPipeLine-Opens" class="form-control">

<option value="">הכל</option>

<?php 

$Pipes = DB::table('pipeline_category')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();

foreach ($Pipes as $Pipe) {                    

?>

<option><?php echo $Pipe->Title; ?></option>                                        

<?php } ?>                    

</select>  </th>

<th><select id="table-filterPipeLineStatus-Opens" class="form-control">

<option value="">הכל</option>

<?php 

$Pipes = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->GroupBy('Title')->get();

foreach ($Pipes as $Pipe) {                    

?>

<option><?php echo $Pipe->Title; ?></option>                                        

<?php } ?>                    

</select>  </th>

                

<th><select id="table-filterPipeLineSource-Opens" class="form-control">

<option value="">הכל</option>

<?php 

$Pipes = DB::table('leadsource')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();

foreach ($Pipes as $Pipe) {                    

?>

<option><?php echo $Pipe->Title; ?></option>                                        

<?php } ?>                    

</select>  </th>

<th><select id="table-filterPipeLineAgent-Opens" class="form-control">

<option value="">הכל</option>

<?php 

$Pipes = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->where('status', '=', '1')->get();

foreach ($Pipes as $Pipe) {                    

?>

<option><?php echo $Pipe->display_name; ?></option>                                        

<?php } ?>                    

</select>  </th>

                <th><span>ת.יצירה</span></th>

                <th class="lastborder"></th>

            </tr>

        </tfoot>

	

        </table> 



        

        

        

        

        

    </div>

    </div>  



			<script>
        var direction = false ;  
				(function($){

					$(document).ready(function(){

            if( $("html").attr("dir") == 'rtl' ){
                direction = true ;
            }

						var dateRange = jQuery('#dateRange');

						dateRange.daterangepicker({

                            startDate: moment('<?php echo $StartDate; ?>', 'YYYY-MM-DD'),

                            endDate:  moment('<?php echo $EndDate; ?>', 'YYYY-MM-DD'),    

                            isRTL: direction ,

                           

                            locale: {

                                format: 'DD/M/YY',

                                "applyLabel": "אישור",

                                "cancelLabel": "ביטול",

                            }

                        }).on('apply.daterangepicker', function(e, d){

                            

                            $.get('LeadsList/Opens.php?startDate='+moment(d.startDate).format('YYYY-MM-DD')+'&endDate='+moment(d.endDate).format('YYYY-MM-DD'), function(data){

                            $('#nav-open').html(data);

                            });

                            

                            

//							window.location.href = 'cartesetcredit.php?startDate='+moment(d.startDate).format('YYYY-MM-DD')+'&endDate='+moment(d.endDate).format('YYYY-MM-DD');

                            

                            

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

    var myDefaultWhiteList = $.fn.tooltip.Constructor.Default.whiteList;
    myDefaultWhiteList.a = ['data-client','data-pipe-id','data-task']

	$('#LeadsOpens tfoot th span').each( function () {

        var title = $(this).text();

        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );

    $("body").on("click",".js-new-task",function(){
        var new_task = $(this).attr("data-task");
        var client_id = $(this).attr("data-client");
        var pipe_id = $(this).attr("data-pipe-id");
        NewCal(new_task , client_id , pipe_id);
    });

	var modal = $('#SendClientPush');

    var modalsClientIds = $('input[name="clientsIds"]', modal);

    

    var newmodal = $('#AgentClientPush');

    var newmodalsClientIds = $('input[name="newclientsIds"]', newmodal);

    

	var categoriesDataTable;

	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;

   var categoriesDataTable =   $('#LeadsOpens').dataTable( {

            language: BeePOS.options.datatables,

            retrieve: true,

			responsive: true,

		    processing: false,

	       // autoWidth: true,

	        //"scrollY":        "450px",

            //"scrollCollapse": true,

            "paging": false,

            pageLength: 500,

	         //fixedHeader: {headerOffset: 50},



	     //  bStateSave:true,

//		    serverSide: true,

//	        pageLength: 5000,

	      dom:  '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

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

			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'מתעניינים פתוחים', className: 'btn btn-dark'},

			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'מתעניינים פתוחים' , className: 'btn btn-dark'},

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

          <?php if (Auth::userCan('141')): ?> 

         {text: 'שייך לנציג <i class="fas fa-link"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {

         // rows_selected = table.column(0).checkboxes.selected();

         var newclientsIds = dt.column(0).checkboxes.selected().toArray();

         if(!newclientsIds.length) return alert('אנא בחר לקוחות');

         newmodalsClientIds.val(newclientsIds.join(","));

         newmodal.modal('show');



          }},     

        <?php endif ?>    

			

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

		

		    var table = $('#LeadsOpens').DataTable();  

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

	

$('#LeadsOpens tfoot tr').insertAfter($('#LeadsOpens thead tr'));   

        

$('#table-filterBrands-Opens').on('change', function(){

table.column('4').search("^" + this.value + "$", true, false, true).draw();   

});    

    

$('#table-filterPipeLine-Opens').on('change', function(){

table.column('5').search(this.value).draw();   

});   

    

$('#table-filterPipeLineStatus-Opens').on('change', function(){

table.column('6').search(this.value).draw();   

});       

 

$('#table-filterPipeLineSource-Opens').on('change', function(){

table.column('7').search(this.value).draw();   

});   

    

$('#table-filterPipeLineAgent-Opens').on('change', function(){

table.column('8').search(this.value).draw();   

});       

    

    

    

    

    

});

    

</script>



