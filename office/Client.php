<?php

require_once '../app/init.php';
$pageTitle = lang('client_management');
require_once '../app/views/headernew.php';

if (Auth::check()):

if ((Auth::userCan('45') && @$_REQUEST['Act'] == '0') || (Auth::userCan('46') && @$_REQUEST['Act'] == '1') || (Auth::userCan('47') && @$_REQUEST['Act'] == '2')):

$CompanyNum = Auth::user()->CompanyNum;
$user_ID = Auth::user()->id;
$StatusAct = @$_REQUEST['Act'];

if ($StatusAct==''){

$StatusAct = '0';    

}

$Clients = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('Status', '=', $StatusAct)->orderBy('CompanyName', 'ASC')->get();

$resultcount = count($Clients);




$Category2 = DB::table('automation')->where('CompanyNum','=', $CompanyNum)->where('Category','=', '2')->where('Type','=', '1')->where('Status','=', '0')->count();

?>

<script type="text/javascript" src="js/settingsDialog/clientsSettings.js?<?php echo filemtime('js/settingsDialog/clientsSettings.js') ?>"></script>
<script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime('js/settingsDialog/settingsDialog.js') ?>"></script>

<link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet">



<link href="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">







<script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>



<script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>

<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

<script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>



<!--<script src="--><?php //echo App::url('CDN/datatables/moment.min.js') ?><!--"></script>-->

<script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>


<script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>

<script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/absolute.js"></script>



<script>

$(document).ready(function(){

if( $("html").attr("dir") == "rtl"){
 // var dir_dom  = '<"row d-flex justify-content-between align-items-end" <"col-md-6 d-flex flex-column" l <"d-flex justify-content-between" B >> <"col-md-6" fr > <"col-md-12" t > <"col-md-6" i> <"col-md-6" p> > ';
  var dir_dom  =  'lBfrtip';
}else{
  var dir_dom  = '<"row d-flex justify-content-between align-items-end" <"col-md-6 col-md-6 d-flex justify-content-start" rf > <"col-md-6 d-flex flex-column align-items-end" l <"d-flex justify-content-between" B >> <"col-md-12" t > <"col-md-6" > <"col-md-6 d-flex flex-column align-items-end" ip>  > ';
}
  
 

	 $('#categories tfoot th span').each( function () {

        var title = $(this).text();

        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );



    } );

	

	    function profileUrl (id, name) { 

        <?php if (Auth::userCan('118')): ?>    

        return '<a class="text-success" href="ClientProfile.php?u='+id+'"><strong class="text-success">'+name+'</strong></a>';

        <?php else: ?>

        return '<strong class="text-success">'+name+'</strong>';    

        <?php endif ?>    

        }

    

	

 function ItemStatus (id) { 



	 if (id=='0'){

	     return  '<span class="text-success"><i class="fas fa-eye"></i> <?= lang('active') ?></span>';

		

	 } else if (id=='1') {

		 return '<span class="text-danger"><i class="fas fa-eye"></i> <?= lang('completed') ?></span>';

		

        }

     

     else  {

		 return '<span class="text-info"> <?= lang('interested_single') ?></span>';

		

        }

		

		}



$.date = function(dateObject) {

    if (dateObject==''){

    var date = '';    

    }

    else {

    var d = new Date(dateObject);

    var day = d.getDate();

    var month = d.getMonth() + 1;

    var year = d.getFullYear();

    if (day < 10) {

        day = "0" + day;

    }

    if (month < 10) {

        month = "0" + month;

    }

    var date = day + "/" + month + "/" + year;

    }

    

    if (d<new Date()){

    date = '<span class="text-danger">'+date+'</span>';    

    }

    

    

    return date;

};    

    

 function MemberShipType (id) {



    var ItemText = ''; 

   if (jQuery.isEmptyObject(id)){

         return ItemText;   

   }   

else {     

   var jsonConvertedData = JSON.parse(id);  

     var return_data = '';

      for(var i=0;i< jsonConvertedData.data.length; i++){

        if (jsonConvertedData.data[i].TrueBalanceValue<=0){

        return_data += jsonConvertedData.data[i].ItemText+' '+$.date(jsonConvertedData.data[i].TrueDate)+', ';     

        }

        else {  

        return_data += jsonConvertedData.data[i].ItemText+' '+$.date(jsonConvertedData.data[i].TrueDate)+' <?= lang('classes_2') ?> '+jsonConvertedData.data[i].TrueBalanceValue+', ';

        }

      }

     

    return_data = return_data.replace(/,(?=[^,]*$)/, ' ')

    return return_data;

    }



 }   

    

	$.fn.dataTable.moment = function ( format, locale ) {

    var types = $.fn.dataTable.ext.type;



    // Add type detection

    types.detect.unshift( function ( d ) {

        // Null and empty values are acceptable
        if ( d === '' || d === null ) {
            return 'moment-'+format;
        }

        return moment( d, format, locale, true ).isValid() ?

            'moment-'+format :

            null;

    } );



    // Add sorting method - use an integer for the sorting

    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {

        return moment( d, format, locale, true ).unix();

    };

};

	

	 $.fn.dataTable.moment( 'D/M/YYYY' );

	 var nameType = $.fn.dataTable.absoluteOrder( 'Unknown' );

	


	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;

   var categoriesDataTable =   $('#categories').dataTable( {

            language: BeePOS.options.datatables,
/*
        "oLanguage": {
          "sSearch": "_INPUT_ <span>YOUR SEARCH TITLE HERE:</span> " //search
        },*/

       responsive: true,


         //   serverSide: true,

            //sAjaxSource: "ClientPost.php",

           	ajax: {
   			    url: 'ClientPostNew.php?Act=<?php echo $StatusAct; ?>',
				type: 'POST',
    		},


		    processing: true,

            "paging": true,

	    pageLength: 100,

        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "<?= lang('all') ?>"]],

	    //dom: "lBfrtip",
       dom  : dir_dom,

		//info: true,

	    buttons: [

        <?php if (Auth::userCan('98')): ?>

		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},

			{extend: 'excelHtml5',  text: '<?= lang('excel') ?> <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?= lang('client_management') ?>', className: 'btn btn-dark' },

			{extend: 'csvHtml5', text: '<?= lang('csv') ?> <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?= lang('client_management') ?>' , className: 'btn btn-dark'},

            {extend: 'print', text: '<?= lang('print') ?> <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {

              // https://datatables.net/reference/button/print

             jQuery(win.document).ready(function(){

             $(win.document.body)

             .css( 'direction', $("html").attr("dir") )

             });

             }},

           // 'pdfHtml5'

		<?php endif ?>



        ],



		//order: [[1, 'ASC']]





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

<link href="assets/css/fixstyle.css?<?= filemtime('assets/css/fixstyle.css') ?>" rel="stylesheet">
<?php

if (@$StatusAct=='0'){

$PageTitleClient = lang('client_management');

} 

else if (@$StatusAct=='1'){

$PageTitleClient = lang('archived_clients');

}  

else if (@$StatusAct=='2'){

$PageTitleClient = lang('manage_interested');

}  

else {

$PageTitleClient = lang('client_management');

}                

?>  

<div class="col-md-12 col-sm-12">

<?php
    require_once '../app/views/clientsSettings.php';
?>
<a class="floating-plus-btn header_red_btn_media" id="openCheckOutOrder" target="_blank" href="<?=$linkCart?>">
    <i class="fal fa-cash-register header-red-icon"></i>
</a>
<?php if ((Auth::userCan('50') && $_REQUEST['Act'] == '0') || (Auth::userCan('50') && $_REQUEST['Act'] == '1')): ?>
<a href="javascript:void(0);" class="floating-plus-btn d-flex bg-primary" onclick="NewClient()">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>
<?php endif; ?>




<div class="row">

<div class="col-md-12 col-sm-12">	



    <div class="card spacebottom">

        <div class="card-header text-start d-flex justify-content-between py-8" >
            <div class="align-self-center">
                <i class="fas fa-th"></i> <b><?php echo $PageTitleClient; ?></b>
            </div>
            <div>
                <?php if(isset($_GET['Act']) && $_GET['Act'] == 0) { ?>
                    <a class="btn btn-outline-danger mie-7" href="/office/Client.php?Act=1"><?php echo lang('archived_clients') ?> <i class="fal fa-users"></i></a>
                <?php } elseif(isset($_GET['Act']) && $_GET['Act'] == 1) {?>
                    <a class="btn-calendar btn-outline-success mie-7" href="/office/Client.php?Act=0"><?php echo lang('active_clients') ?><i class="fal fa-users"></i></a>
                <?php } ?>
                <?php if(Auth::user()->role_id == 1){ ?>
                    <a href="./import_client/fileupload?cNum=<?php echo $CompanyNum; ?>&uid=<?php echo $user_ID ?>" target="_blank" class="btn btn-outline-dark mie-7" ><i class="fas fa-users"></i> <?php echo lang('import_client_list') ?> </a>
                    <a href="javascript:void(0);" class="btn btn-dark" data-ip-modal="#FreezAllClients" ><i class="far fa-snowflake"></i>  <?php echo lang('freeze_all_clients') ?></a>
                <?php } ?>
            </div>

 	    </div>

  	<div class="card-body">       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">

		<thead>

			<tr class="bg-dark text-white">

				<th class="text-start" width="6%">#</th>

                <th class="text-start"><?= lang('client_name') ?></th>

				<th class="text-start"><?= lang('telephone') ?></th>

				<th class="text-start"><?= lang('email_table') ?></th>

                <?php if ($StatusAct == 0) { ?>
                    <th class="text-start"><?= lang('join_date') ?></th>
                    <th class="text-start" width="20%"><?= lang('membership') ?></th>
                    <th class="text-start" width="20%"><?= lang('last_class') ?></th>

                <?php } else { ?>
                    <th class="text-start" style="min-width: 10em;" ><?= lang('leave_date') ?></th>
                    <th class="text-start" style="min-width: 10em;" ><?= lang('fail_reason') ?></th>
                <?php } ?>


                <th class="text-start" lastborder><?= lang('branch') ?></th>

			</tr>

		</thead>

		<tbody>

        </tbody>

	<tfoot>
            <tr>
                <th><span><?= '#' ?></span></th>
                <th><span><?= lang('client_name') ?></span></th>
				<th><span><?= lang('telephone') ?></span></th>
				<th><span><?= lang('email_table') ?></span></th>
                <?php if ($StatusAct == 0) { ?>
                <th><span><?= lang('join_date') ?></span></th>
                <th><span><?= lang('membership') ?></span></th>
                <th><span><?= lang('last_class') ?></span></th>
                <?php } else { ?>
                    <th><span><?= lang('leave_date') ?></span></th>
                    <th><span><?= lang('fail_reason') ?></span></th>
                <?php } ?>
                <th class="lastborder"><span><?= lang('branch') ?></span></th>


            </tr>

        </tfoot>



        </table>

		</div></div>

    

	</div> 

</div>



</div>





	<div class="ip-modal text-start" id="AddNewLead">

		<div class="ip-modal-dialog">

			<div class="ip-modal-content">

				<div class="ip-modal-header d-flex justify-content-between"> 
				  <h4 class="ip-modal-title"><?= lang('add_new_lead') ?></h4> 
          <a class="ip-close" title="Close" style="" data-dismiss="modal">&times;</a> 
				</div>

				<div class="ip-modal-body">



				<form action="AddNewLead"  class="ajax-form clearfix" autocomplete="off">

                

                <div class="form-group" >

                <label><?= lang('choose_pipeline') ?></label>

                <select class="form-control text-start" name="PipeLine" id="PipeLineSelect"  required>

                <option value=""><?= lang('choose') ?></option>

				<?php

                $b = '1';    

				$ClassTypes = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   

                if (!empty($ClassTypes)){     

				foreach ($ClassTypes as $ClassType) { ?> 	

				<option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Title ?></option>

				<?php ++$b; } } else { ?>     

                <?php } ?>    

				</select>

                </div>	       

                    

                <div class="row">    

                <div class="col-md-6 col-sm-12 order-md-1">    

				<div class="form-group" >

                <label><?= lang('first_name') ?> <em class="text-danger font-rubik">*</em></label>

                <input type="text" name="FirstName" class="form-control" required>

                </div>

				</div>	

                <div class="col-md-6 col-sm-12 order-md-2">     

				<div class="form-group" >

                <label><?= lang('last_name') ?> <em class="text-danger font-rubik">*</em></label>

                <input type="text" name="LastName" class="form-control" required>

                </div>	

                </div>    

                    

                </div> 

                    

                    

				<div class="form-group">
                    <label><?= lang('cellular') ?> <em class="text-danger font-rubik">*</em></label>
                    <input type="tel" name="ContactMobile" id="ContactMobile" class="form-control" required pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="<?php echo lang('incorrect_mobile') ?>">
                </div>

                <div class="form-group">
                    <label><?= lang('email_table') ?></label>
                    <input type="email" name="Email" class="form-control" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$" title="<?php echo lang('woring_email') ?>">
                </div>

                <!-- minor section -->
                <div class="py-10">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="minor_checkbox" name="minor_checkbox">
                        <label class="custom-control-label" for="minor_checkbox"><?php echo lang('fill_for_minor') ?></label>
                    </div>
                </div>
                <div id="minor-lead-div" class="form-group" style="display: none">
                    <div class="mt-11 font-weight-bold">
                        <label><?php echo lang('minor_client_details') ?></label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label><?php echo lang('first_name') ?> <span class="text-primary">(<?php echo lang('minor') ?>) </span><em class="text-danger font-rubik">*</em></label>
                            <input type="text" name="minor_firstName" id="minor_firstName" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label><?php echo lang('last_name') ?> <span class="text-primary">(<?php echo lang('minor') ?>) </span><em class="text-danger font-rubik">*</em></label>
                            <input type="text" name="minor_lastName" id="minor_lastName" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group" dir="rtl">
                                <label><?= lang('cellular') ?> <span class="text-primary">(<?php echo lang('minor') ?>) </span></label>
                                <input type="tel" name="minor_ContactMobile" id="minor_ContactMobile" class="form-control" pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="<?php echo lang('incorrect_mobile') ?>">
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label><?php echo lang('minor_relationship') ?></label>
                            <select name="relationship" id="minor_relationship" class="form-control">
                                <option value="1"><?php echo lang('father') ?></option>	
                                <option value="2"><?php echo lang('mother') ?></option>
                                <option value="3"><?php echo lang('brother_or_sister') ?></option>
                                <option value="4"><?php echo lang('relative') ?></option>
                                <option value="5"><?php echo lang('other') ?></option>
                            </select>
                        </div>
                    </div>
                </div>	
                <!-- end minor section -->	

				<div class="form-group" >

                <label><?= lang('email_table') ?></label>

                <input type="email" name="Email" class="form-control">

                </div>	

				 

                <div class="form-group" >

                <label><?= lang('interested_in_class') ?></label>

                <select class="form-control js-example-basic-single select2multipleDesk text-start" name="ClassType[]" id="ClassType"   multiple="multiple" >

                <option value="BA999"><?= lang('all_classes') ?></option>

				<?php

				$ClassTypes = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Type', 'ASC')->get();    

				foreach ($ClassTypes as $ClassType) { ?> 	

				<option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Type ?></option>

				<?php } ?>	

				</select>

                </div>	    

                    

                <div class="form-group" >

                <label><?= lang('branch') ?></label>

                <select class="form-control text-start" name="Brands" id="BrandsTypeClass" >

				<?php

                $b = '1';    

				$ClassTypes = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   

                if (!empty($ClassTypes)){     

				foreach ($ClassTypes as $ClassType) { ?> 	

				<option value="<?php echo $ClassType->id; ?>" <?php if ($b=='1'){ echo 'selected';} else {} ?>><?php echo $ClassType->BrandName ?></option>

				<?php ++$b; } } else { ?>

                <option value="0"><?= lang('primary_branch') ?></option>

                <?php } ?>    

				</select>

                </div>





                    <div class="form-group" >

                <label><?= lang('incoming_source') ?></label>

                <select class="form-control" name="Source">

				<option value="0" selected><?= lang('without') ?></option>

				<?php

				$PipeSources = DB::table('leadsource')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Title', 'ASC')->get();    

				foreach ($PipeSources as $PipeSource) { ?> 	

				<option value="<?php echo $PipeSource->id; ?>"><?php echo $PipeSource->Title ?></option>

				<?php } ?>	

				</select>

                </div>		

					

					

                    

				<div class="form-group" >

                <label><?= lang('status') ?></label>

                <select class="form-control" name="Status" id="StatusSelect" required>

                <option value=""><?= lang('choose') ?></option>

				<?php

				$PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('Act','=', '0')->where('Status','=', '0')->orderBy('Sort', 'ASC')->get();    

				foreach ($PipeTitles as $PipeTitle) { ?> 	

				<option value="<?php echo $PipeTitle->id; ?>" data-ajax="<?php echo $PipeTitle->PipeId; ?>" ><?php echo $PipeTitle->Title ?></option>

				<?php } ?>	

				</select>

                </div>	

					

                <?php

				if (Auth::userCan('141')) {

				?>

                    <div class="form-group" >

                    <label><?= lang('choose_representative') ?></label>

                    <select name="Agents" class="form-control text-start ChangeLeadAgentp"  style="width: 100%" data-placeholder="<?= lang('choose_representative') ?>">

                    <option value="0"><?= lang('without_representative') ?></option>

                    <?php

					$AgentLoops = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('ActiveStatus', '=', '0')->get();

					foreach ($AgentLoops as $AgentLoop) {

			        echo '<option value="'.$AgentLoop->id.'" >'.$AgentLoop->display_name.'</option>';

                    }

					?>

                    </select>

                    </div>	    

				<?php

				}else { ?>

				<input type="hidden" name="Agents" value="0">

				<?php } ?>       

                    

                    

                <?php if ($Category2=='1'){ ?>

                <div class="form-group">

                <label><?= lang('set_automation') ?></label>

                <select class="form-control" name="Automation">   

                <option value="0" selected><?= lang('activated') ?></option>

                <option value="1"><?= lang('turned_off') ?></option>

                </select>

                </div>   

                <?php } else { ?>

                <input type="hidden" name="Automation" value="1">

                 <?php } ?>       

                    

                    

				</div>

				<div class="ip-modal-footer d-flex justify-content-between">

                <div class="ip-actions">

                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>

                </div>

				</form>    

                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php _e('main.close') ?></a>     

				

                

				</div>

			</div>

		</div>

	</div>

    <div class="ip-modal text-start" id="FreezAllClients">

<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>

    <div class="ip-modal-content">

        <div class="ip-modal-header  d-flex justify-content-between"  <?php //_e('main.rtl') ?>>

        <h4 class="ip-modal-title"><?php echo lang('freeze_all_clients') ?></h4>
        <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>




        </div>

        <div class="ip-modal-body">



        <form action="FreezAllClients"  class="ajax-form clearfix" autocomplete="off">

        <div class="form-group">
            <div class="alert alert-warning" role="alert" style="display:block">
            <?php echo lang('freeze_clients_notice') ?>
            </div>
        </div>

        <div class="form-group">
        <label><?php echo lang('freeze_start_date') ?> <em><?php echo lang('req_field') ?></em></label>
        <input type="date" class="form-control focus-me" id="ClassDate" min="<?php echo date('Y-m-d'); ?>" name="ClassDate" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
        <label><?php echo lang('freeze_end_date') ?> <em><?php echo lang('req_field') ?></em></label>
        <input type="date" min="<?php echo date('Y-m-d'); ?>" class="form-control" id="ClassDateEnd" name="ClassDateEnd" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <label><?php echo lang('select_subscribtion_freeze') ?> </label>
        <div class="form-group" style="max-height: 250px; overflow-y:scroll">
            <div class="grid-memberships">
            <?php $classTypes = DB::table('membership_type')->where('CompanyNum','=', $CompanyNum)->where('Status', '=', 0)->get();
            foreach($classTypes as $key => $classType) {  ?>
                <!-- <div class="d-flex flex-d-col">
                    <input id="rm-regular" type="checkbox" name="membership_type[]" checked value="<?php //echo $classType->id; ?>"><span><?php //echo $classType->Type ?></span>
                </div> -->
                <div class="custom-control custom-checkbox d-flex flex-d-col">
                    <input type="checkbox" class="custom-control-input" checked id="rm-regular<?php echo $key ?>" name="membership_type[]" value="<?php echo $classType->id; ?>">
                    <label class="custom-control-label" for="rm-regular<?php echo $key ?>"><?php echo $classType->Type ?></label>
                </div>
            <?php } ?>
            </div>
        </div>
    
        <div class="form-group">
            <label><?php echo lang('freeze_reason') ?></label>
                <textarea class="form-control" name="Reason" rows="2" required></textarea>
        </div>     

        <div class="ip-modal-footer d-flex justify-content-between px-0">

        <a class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php _e('main.close') ?></a>

        <div class="ip-actions">

        <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>

        </div>

        </form>    
     

        

        

        </div>

    </div>

</div>

</div>



<script type="text/javascript" src="<?php echo asset_url('js/jquery.scannerdetection.js') ?>"></script>

<script>



$(document).scannerDetection({

	timeBeforeScanTest: 200, // wait for the next character for upto 200ms

	endChar: [13], // be sure the scan is complete if key 13 (enter) is detected

	avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms

	ignoreIfFocusOn: 'input', // turn off scanner detection if an input has focus

	preventDefault: false,

    onComplete: function(barcode){  

	

    var urls= 'RFID.php?C='+barcode;	

    window.location.href = urls;



	}, // main callback function

	scanButtonKeyCode: 116, // the hardware scan button acts as key 116 (F5)

	scanButtonLongPressThreshold: 5, // assume a long press if 5 or more events come in sequence

//	onScanButtonLongPressed: showKeyPad, // callback for long pressing the scan button

	//onError: function(string){alert('Error ' + string);}

});





</script>  





<script>

 

$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "<?= lang('choose_class_type') ?>", 'language':"he", dir: "rtl" } );

   

$( ".ChangeLeadAgentp" ).select2( {theme:"bootstrap", placeholder: "<?= lang('choose_representative') ?>", 'language':"he", dir: "rtl" } );

    

$('#ClassType').on('select2:select', function (e) {    

var selected = $(this).val();



  if(selected != null)

  {

    if(selected.indexOf('BA999')>=0){

      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?= lang('choose_class_type') ?>"  } );

    }

  }

    

});	      

    

$(function() {

			var time = function(){return'?'+new Date().getTime()};

						

			$('#AddNewLead').imgPicker({

			});



	

	

});

$(function() {

var time = function(){return'?'+new Date().getTime()};

            

$('#FreezAllClients').imgPicker({

});







});	



    

$('#PipeLineSelect').on('change', function() {	 

var Id = this.value;



 $('#StatusSelect option')

        .hide() // hide all

        .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value

        .show(); // and show them    

    

 $('#StatusSelect').val('');     

});      

$(document).ready(function() {
    $('#minor_checkbox').on('click', function() {
        if ($(this).is(":checked")) {
            $("#minor-lead-div").show();
            $('#minor-lead-div').height(200);
            $("#minor_firstName").prop('required', true);
            $("#minor_lastName").prop('required', true);
            
        } else {
            $("#minor_firstName").prop('required', false);
            $("#minor_lastName").prop('required', false);
            $('#minor-lead-div').height(0);
            setTimeout(() => {
                $("#minor-lead-div").hide();    
            }, 200);
        }
    });
});    

</script>





<?php include('InfoPopUpInc.php'); ?>





<?php else: ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>





<?php endif ?>



<?php if (Auth::guest()): ?>



<?php redirect_to('../index.php'); ?>



<?php endif ?>



<?php require_once '../app/views/footernew.php'; ?>