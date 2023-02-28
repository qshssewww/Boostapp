<?php 
require_once '../app/init.php'; 
$pageTitle = lang('settings_users');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>

<?php if (Auth::userCan('3')): ?>

<?php

$CompanyNum = Auth::user()->CompanyNum;

$Clients = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->orderBy('display_name', 'ASC')->get();

$resultcount = count($Clients);



CreateLogMovement(lang('settings_user_log'), '0');



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

	   var dt_dom = '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>' ; 

     

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

	      dom: dt_dom ,

		//info: true,

       

	    buttons: [

        <?php if (Auth::userCan('98')): ?>    

		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},

			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('title_user_manage') ?>', className: 'btn btn-dark'},

			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('title_user_manage') ?>', className: 'btn btn-dark'},

           // 'pdfHtml5'

		 <?php endif ?>	

        ],

      

		ajax: { url: 'AgentPost.php', },

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

<?php if (Auth::userCan('4') && $CompanyNum != 100): ?>
<a href="javascript:;" data-ip-modal="#AddTechPopup" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('button_user_manage') ?>">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>
<?php endif; ?>    


<div class="row">

<?php include("SettingsInc/RightCards.php"); ?>



<div class="col-md-10 col-sm-12 order-md-1">	





    <div class="card spacebottom">

    <div class="card-header text-start d-flex justify-content-between" >

    <div>  
    <i class="fas fa-users"></i> <b><?php echo lang('title_user_manage') ?> <span style="color:#48AD42;"><?php echo $resultcount; ?></span></b>
     </div>
 	</div>    

  	<div class="card-body">       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">

		<thead class="thead-dark">

			<tr>

				<th class="text-start"><?php echo lang('user_table') ?></th>

                <th class="text-start"><?php echo lang('name_table') ?></th>

				<th class="text-start"><?php echo lang('cell_table') ?></th>

				<th class="text-start"><?php echo lang('email_table') ?></th>

				<th class="text-start"><?php echo lang('last_activity_table') ?></th>

                <th class="text-start"><?php echo lang('permission_table') ?></th>

                <th class="text-start lastborder"><?php echo lang('status') ?></th>

			</tr>

		</thead>

		<tbody>

              

        </tbody>

	

	

	<tfoot>

            <tr>

				<th class="text-start"><span><?php echo lang('user_table') ?></span></th>

                <th class="text-start"><span><?php echo lang('name_table') ?></span></th>

				<th class="text-start"><span><?php echo lang('cell_table') ?></span></th>

				<th class="text-start"><span><?php echo lang('email_table') ?></span></th>

				<th class="text-start"><span><?php echo lang('last_activity_table') ?></span></th>

                <th class="text-start"><span><?php echo lang('permission_table') ?></span></th>

                <th class="text-start lastborder"><span><?php echo lang('status') ?></span></th>

            </tr>

        </tfoot>

	

        </table> 

		</div></div>

    

	</div> 

</div>



</div>

<!-- Add Tech -->

	<div class="ip-modal" id="AddTechPopup">

		<div class="ip-modal-dialog BigDialog">

			<div class="ip-modal-content text-start">

				<div class="ip-modal-header d-flex justify-content-between" >

				<h4 class="ip-modal-title"><?php echo lang('add_new_user') ?></h4>
                <a class="ip-close" title="Close"  >&times;</a> 

				</div>

				<div class="ip-modal-body">

                <form action="addtech"  class="ajax-form clearfix" autocomplete="off">

                                 

                <div class="form-group">

                <?php if (Config::get('auth.require_username')): ?>

                <label><?php echo lang('username_single') ?></label>

                <input type="text" class="form-control focus-me" name="username" id="username">

                <?php endif ?>

                </div>    

                <div class="form-group">

                <label><?php echo lang('email') ?></label>

                <input type="text" class="form-control" name="email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$" id="email">

                </div>

                <div class="form-group">

                <label><?php echo lang('permission_table') ?></label>

                <select name="role_id" class="form-control">

                <?php

                $AgentRules = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->orderBy('Title', 'ASC')->get();

				foreach($AgentRules as $AgentRule) {

					echo '<option value="'.$AgentRule->id.'">'.$AgentRule->Title.'</option>';

				}

				?>

                </select>

                

                

                

                </div>

                <hr>

                    

                <div class="row">

               	<div class="col-md-3">

                <div class="form-group">

                <label><?php echo lang('first_name') ?></label>

                <input type="text" class="form-control" required name="FirstName" >

                </div>

               	</div>

               	<div class="col-md-3">

                <div class="form-group">

                <label><?php echo lang('last_name') ?></label>

                <input type="text" class="form-control" required name="LastName" >

                </div>

               	</div>

               	<div class="col-md-3">

                <div class="form-group">

                <label><?php echo lang('cellular') ?></label>

                <input type="text" class="form-control" name="ContactMobile" required pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" onkeypress='validate(event)'>

                </div>

               	</div>

               	<div class="col-md-3">

                <div class="form-group">

                <label><?php echo lang('id') ?></label>

                <input type="text" class="form-control" name="CompanyId"  onkeypress='validate(event)'>

                </div> 

               	</div>

               	</div>

               	 <hr>    

                    

               	<div class="row">

               	<div class="col-md-3">

                <div class="form-group">

             <label><?php echo lang('date_birthday') ?></label>

              <input name="Dob" type="date" class="form-control">

                 </div>

					</div>

               		<div class="col-md-3">

              <div class="form-group">

                <label><?php echo lang('gender') ?></label>

                <select name="Gender" class="form-control">

                <option value="1"><?php echo lang('male') ?></option>

                <option value="2"><?php echo lang('female') ?></option>

                </select>

              </div>

              </div>

              <div class="col-md-3">

              <div class="form-group">

                 <label><?php echo lang('is_trainer') ?></label>

                <select name="Coach" class="form-control">

                <option value="1" selected><?php echo lang('yes') ?></option>

                <option value="0"><?php echo lang('no') ?></option>

                </select>

                </div>

				</div>



				</div>

               	 <hr>

                    

                    

               <div class="row">



               <div class="col-md-3">

              <div class="form-group">

              <label><?php echo lang('user_phone_center') ?></label>

              <input name="AgentNumber" type="text" class="form-control" onkeypress='validate(event)'>

              </div>

              </div>

              <div class="col-md-3">

              <div class="form-group">

              <label><?php echo lang('extension_number') ?></label>

              <input name="AgentEXT" type="text" class="form-control"  onkeypress='validate(event)'>

              </div>

				</div>

				</div>

               	 <hr>    

                    

               <div class="form-group">

                 <label><?php echo lang('user_send_login') ?></label>

                <select name="SendInfo" class="form-control">

                <option value="0" selected><?php echo lang('yes') ?></option>

                <option value="1"><?php echo lang('no') ?></option>

                </select>

                </div>

                    

                    

				</div>

				<div class="ip-modal-footer d-flex justify-content-between">

                <div class="ip-actions">

                <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('save_changes_button') ?></button>

                </div>

				<button type="button" class="btn btn-dark ip-close"><?php echo lang('close') ?></button>

                </form>

				</div>



			</div>

		</div>

	</div>

	<!-- end Add Tech -->



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