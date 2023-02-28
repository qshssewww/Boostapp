<?php 
require_once '../app/init.php';
redirect_to("/office/DeskPlanNew.php");
$pageTitle = lang('lesson_archive');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>

<?php if (Auth::userCan('82')): ?>

<?php





$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum' ,'=', $CompanyNum)->first();





$Clients = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('Status','!=', '0')->orderBy('StartDate', 'ASC')->get();

$resultcount = count($Clients);



$ClassInfo = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('Status','!=', '0')->orderBy('StartDate', 'ASC')->first();



CreateLogMovement(lang('entered_lessons_archive'), '0');



?>









<!-- <link href="<?php echo App::url('CDN/datatables/bootstrap.css') ?>" rel="stylesheet"> -->

<!-- <link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet"> -->

<!-- <link href="<?php echo App::url('CDN/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet"> -->



<!-- <link href="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet"> -->

<link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">







<script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>



<script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>



<script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>



<!--<script src="--><?php //echo App::url('CDN/datatables/moment.min.js') ?><!--"></script>-->

<script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>



<script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>

<script src="https://momentjs.com/downloads/moment.js"></script>







<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



<script>

$(document).ready(function(){

	

	 $('#categories tfoot th span').each( function () {

        var title = $(this).text();

        if(title === '<?= lang('date') ?>'){

          $(this).html( '<input type="text" id="tblDateRangeFillter" style="width:90%;" class="form-control"  />' );

          setTimeout(function(){

            jQuery('#tblDateRangeFillter').daterangepicker({

              startDate: moment().startOf('month'),

              endDate: moment().endOf('month'),

              locale: {

                  format: 'DD/MM/Y'

              }

            });

          }, 0);

        }else{

          $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

        }

        



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

  

   





   $.fn.dataTable.ext.search.push(

    function( settings, data, dataIndex ) {



        var range = $('#tblDateRangeFillter');

        var startDate = range.data('daterangepicker').startDate;

        var endDate = range.data('daterangepicker').endDate;

        

        var date = moment( data[2] , 'DD/MM/YYYY') || moment(); // use data for the age column

        // console.log(startDate, endDate, data[2], date);



        return ((date.isAfter(startDate) || date.toString() ==  startDate.toString()) && (date.isBefore(endDate) || date.toString() === endDate.toString()));

 

    }

);



	

	var categoriesDataTable;

	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;

   var categoriesDataTable =   $('#categories').dataTable( {

            language: BeePOS.options.datatables,

      			responsive: true,

		        processing: true,

            paging:     true,

  	        pageLength: 100,

	          dom: "Bfrtip",

	          buttons: [

              <?php if (Auth::userCan('98')): ?>    

                {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?= lang('system_log') ?>', className: 'btn btn-dark'},

                {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?= lang('system_log') ?>' , className: 'btn btn-dark'},

              <?php endif ?>

            ],

            ajax: { url: 'ManageClassHistoryPost.php', },

            order: [[2, 'asc'], [ 4, 'asc' ]]	   	 	   

        });

		

        var table = $('#categories').DataTable();

        $('#categories').DataTable().columns().every( function () {

            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {

              if(this.id == 'tblDateRangeFillter'){ 

                if(!jQuery(this).length || !jQuery(this).data('daterangepicker')) return;

                table.search();

                setTimeout(function(){

                  table.draw();

                }, 0);

                return;

              }

              if ( table.search() !== this.value ) table.search( this.value ).draw();

            });	

        });





        table.order();



   

 

	

	

	

});





</script>



<link href="assets/css/fixstyle.css" rel="stylesheet">

<!-- <div class="col-md-12 col-sm-12">

<div class="row">







<div class="col-md-5 col-sm-12 order-md-1">

<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">

<?php //echo $DateTitleHeader; ?>

</h3>

</div>



<div class="col-md-5 col-sm-12 order-md-3">

<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">

<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">

<i class="fas fa-university"></i> <?//= lang('lesson_archive') ?> <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>

</div>

</h3>

</div>



<div class="col-md-2 col-sm-12 order-md-2 pb-1">

<?php //if (Auth::userCan('89')): ?>     

<a href="javascript:void(0);" onclick="NewClass()" class="btn btn-primary btn-block"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> <?//= lang('add_new_class') ?></a>

<?php //endif ?>     

</div>



</div>



<nav aria-label="breadcrumb" dir="rtl">

  <ol class="breadcrumb">	

  <li class="breadcrumb-item"><a href="index.php" class="text-dark"><?//= lang('main') ?></a></li>

  <li class="breadcrumb-item"><a href="ManageClass.php" class="text-dark"><?//= lang('manage_classes') ?></a></li>

  <li class="breadcrumb-item active"><?//= lang('lesson_archive') ?></li>

  </ol>  

</nav>     -->

<?php if (Auth::userCan('89')): ?>     
    <a href="javascript:;" class="floating-plus-btn d-flex bg-primary" onclick="NewClass()" title="<?= lang('add_new_class') ?>">
        <i class="fal fa-plus fa-lg margin-a"></i>
    </a>
<?php endif; ?> 


<div class="row">

<div class="col-md-12 col-sm-12">	



    <div class="card spacebottom">

    <div class="card-header text-right" dir="rtl">

    <i class="fas fa-university"></i> <b><?= lang('archive') ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span></b>

 	</div>    

  	<div class="card-body">       



<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">

		<thead class="thead-dark">

			<tr>

                <th class="text-right"><?= lang('lesson_type') ?></th>

				<th class="text-right"><?= lang('lesson_title') ?></th>

                <th class="text-right"><?= lang('date') ?></th>

				<th class="text-right"><?= lang('day') ?></th>

				<th class="text-right"><?= lang('hour') ?></th>

                <th class="text-right"><?= lang('instructor') ?></th>

                <th class="text-right"><?= lang('registered') ?></th>

                <th class="text-right"><?= lang('available') ?></th>

                <th class="text-right"><?= lang('waiting') ?></th>

                <th class="text-right"><?= lang('usage') ?></th>

                <th class="text-right lastborder"><?= lang('status') ?></th>

			</tr>

		</thead>

		<tbody>

              

        </tbody>

	

	

	<tfoot>

            <tr>

                <th><span><?= lang('lesson_type') ?></span></th>

				<th><span><?= lang('lesson_title') ?></span></th>

				<th><span><?= lang('date') ?></span></th>

                <th><span><?= lang('day') ?></span></th>

				<th><span><?= lang('hour') ?></span></th>

                <th><span><?= lang('instructor') ?></span></th>

                <th></th>

                <th></th>

                <th></th>

                <th></th>

                <th class="lastborder"></th>

            </tr>

        </tfoot>

	

        </table> 

		</div></div>

    

	</div> 

</div>



</div>




<div class="ip-modal text-right"  role="dialog" id="ViewDeskInfo" data-backdrop="static" data-keyboard="false" aria-hidden="true">

		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>

			<div class="ip-modal-content">

				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>

                 <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>

				<h4 class="ip-modal-title"></h4>

                

				</div>

				<div class="ip-modal-body">



	        

<div id="DivViewDeskInfo">

</div>

        

				</div>

			</div>

		</div>

	</div>





<!-- מודל לקוח חדש -->

<div class="ip-modal text-right" role="dialog" id="ClassStatusPopUp" data-backdrop="static" data-keyboard="false" aria-hidden="true">

		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>

			<div class="ip-modal-content">

				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>

                <a class="ip-close ClosePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>

				<h4 class="ip-modal-title"><?= lang('change_lessons_status') ?></h4>

        <div class="alert alert-warning" role="alert">
            <?= lang('recover_canceled_lesson_not_resetting_trainees') ?>
        </div>

				</div>

				<div class="ip-modal-body">

				<form action="EditClassStatusPopUp" id="EditClassStatusPopUp" class="ajax-form needs-validation" novalidate autocomplete="off">

				<div id="ResultClassStatus"><center><i class="fas fa-spinner fa-spin fa-10x p-3"></i></center></div>

				<div class="ip-modal-footer">

                <div class="ip-actions">

                <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>

                </div>

                

				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>

                </form>

				</div>

				</div>

			</div>

		</div>

	</div>

<!-- מודל לקוח חדש -->











<div class="ip-modal text-right"  role="dialog" id="AddNewTask" data-backdrop="static" data-keyboard="false" aria-hidden="true">

		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>

			<div class="ip-modal-content">

				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>

                <a class="ip-close ip-closePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>

				<h4 class="ip-modal-title"><?= lang('lesson_settings') ?></h4>



				</div>

				<div class="ip-modal-body">



				<form action="AddClassDesk" id="FormAddClassDesk"  class="ajax-form clearfix text-right" autocomplete="off" novalidate>

                



<div class="row">	   

<div class="col-md-12 col-sm-12 order-1">	  

<input type="hidden" id="CalPage" value="1">    

<input type="hidden" name="CalendarId" id="AddEditTaskCalendarId" value="">     



 <div class="row">

 <div class="col-md-4">	 

  <div class="form-group">

  <label><?= lang('lesson_location') ?></label>

    <select class="form-control js-example-basic-single text-right" id="ChooseFloorForTask" name="FloorId" dir="rtl" data-placeholder="<?= lang('choose_lesson_location') ?>" style="width: 100%" onChange="UpdateCalView(this.value)">

  <?php 

  $SectionInfos = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Floor', 'ASC')->get();

  foreach ($SectionInfos as $SectionInfo) {	

  ?>  

  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Title; ?></option>	  

  <?php 

		 }

  ?>  

  </select> 

  </div> 

 </div> 

     

 <div class="col-md-4">	 

  <div class="form-group">

  <label><?= lang('lesson_type') ?></label>

    <select class="form-control js-example-basic-single select2Desk text-right" name="ClassNameType" id="ClassNameType" dir="rtl" data-placeholder="<?= lang('choose_lesson_type') ?>">

    <option value=""></option>    

  <?php 

  $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->where('EventType','=','0')->orderBy('Type', 'ASC')->get();

  foreach ($SectionInfos as $SectionInfo) {	

  ?>  

  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Type; ?></option>	  

  <?php 

		 }

  ?>  

  </select> 

  </div> 

 </div>  

     

 <div class="col-md-4">	 

  <div class="form-group">

      <label><?= lang('is_displayed_in_app') ?></label>
      <select class="form-control js-example-basic-single text-right" name="ShowApp" id="ShowApp" dir="rtl">
          <option value="1" selected><?= lang('yes') ?></option>
          <option value="2"><?= lang('no') ?></option>
  </select> 

  </div> 

 </div>       

     

    

 </div>      

	

    

    

 <div class="row">

     <div class="col-md-4">
         <div class="form-group">
             <label><?= lang('lesson_title') ?></label>
             <input type="text" class="form-control" name="ClassName" id="ClassName">
         </div>
     </div>



     <div class="col-md-4">
         <div class="form-group">
             <label><?= lang('instructor') ?></label>
             <select class="form-control js-example-basic-single select2Desk text-right" name="GuideId" id="GuideId" dir="rtl" data-placeholder="<?= lang('choose_lesson_instructor') ?>" >
                 <option value=""></option>
                 <?php
                 $SectionInfos = DB::table('users')->where('CompanyNum','=',$CompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
                 foreach ($SectionInfos as $SectionInfo) {
                     ?>
                     <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->display_name; ?></option>
                     <?php
                 }
                 ?>
             </select>
         </div>
     </div>


     <div class="col-md-4">
         <div class="form-group">
             <label><?= lang('lesson_level') ?></label>
             <select class="form-control text-right" name="ClassLevel" id="ClassLevel" dir="rtl">
                 <option value="0" selected><?= lang('without_class_level') ?></option>
                 <option value="1"><?= lang('beginners_class') ?></option>
                 <option value="2"><?= lang('dynamic_speed_lesson') ?></option>
                 <option value="3"><?= lang('high_level_class') ?></option>
             </select>
         </div>
     </div>

 </div>

     

 

 <div class="row">



     <div class="col-md-4">
         <div class="form-group">
             <label><?= lang('max_participants') ?></label>
             <input type="text" class="form-control" name="MaxClient" id="MaxClient" value="<?php echo $ClassSettingsInfo->MaxClient ?>">
         </div>
     </div>





     <div class="col-md-4">
         <div class="form-group">
             <label><?= lang('define_min_participants') ?></label>
             <select class="form-control text-right" name="MinClass" id="MinClass" dir="rtl">
                 <option value="0" selected><?= lang('no') ?></option>
                 <option value="1"><?= lang('yes') ?></option>
             </select>
         </div>
     </div>



     <div id="DivMinClassNum1" class="col-md-4" style="display: none;">
         <div class="form-group">
             <label><?= lang('min_participants') ?></label>
             <input type="number" class="form-control" name="MinClassNum" id="MinClassNum" value="<?php echo $ClassSettingsInfo->MinClient ?>" >
         </div>
     </div>

 </div>   

    

    <div id="DivMinClassNum2" style="display: none;">

    

   <div class="row">

       <div class="col-md-6">
           <div class="form-group">
               <label><?= lang('min_participants_check_before_class') ?></label>
               <input type="text" class="form-control" name="ClassTimeCheck" id="ClassTimeCheck" value="<?php echo $ClassSettingsInfo->CheckMinClient ?>">
           </div>
       </div>

       <div class="col-md-6">
           <div class="form-group">
               <label><?= lang('option') ?></label>
               <select class="form-control text-right" name="ClassTimeTypeCheck" id="ClassTimeTypeCheck" dir="rtl">
                   <option value="1" <?php if ($ClassSettingsInfo->CheckMinClientType=='1') { echo 'selected'; } else {} ?> ><?= lang('minutes') ?></option>
                   <option value="2" <?php if ($ClassSettingsInfo->CheckMinClientType=='2') { echo 'selected'; } else {} ?> ><?= lang('hours') ?></option>
               </select>
           </div>
       </div>

    </div>



        <div class="alertb alert-warning"><?= lang('attention_auto_lesson_cancel_if_not_min') ?><br>
            <?= lang('notice_will_be_sent_about_cancel') ?></div>
    </div>

    </div>

    

    

    

 <?php 

function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {

   $seconds = strtotime($hour);

   $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);

   return date($format, $rounded);

}     

?>  

    

  <div class="row">

      <div class="col-md-3">
          <div class="form-group">
              <label><?= lang('lesson_start_date') ?></label>
              <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" class="form-control">
          </div>
      </div>



      <div class="col-md-3">
          <div class="form-group">
              <label><?= lang('lesson_day') ?></label>
              <select name="Day" id="Day" data-placeholder="<?= lang('choose_day') ?>" class="form-control" style="width:100%;">
                  <option value=""><?= lang('choose_class') ?></option>

                  <option value="0"><?= lang('sunday') ?></option>
                  <option value="1"><?= lang('monday') ?></option>
                  <option value="2"><?= lang('tuesday') ?></option>
                  <option value="3"><?= lang('wednesday') ?></option>
                  <option value="4"><?= lang('thursday') ?></option>
                  <option value="5"><?= lang('friday') ?></option>
                  <option value="6"><?= lang('saturday') ?></option>

              </select>
          </div>
      </div>

      <div class="col-md-3">
          <div class="form-group">
              <label><?= lang('begin_time') ?></label>
              <input name="SetTime" id="SetTime" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control">
          </div>
      </div>


      <div class="col-md-3">
          <div class="form-group">
              <label><?= lang('finish_time') ?></label>
              <input name="SetToTime" id="SetToTime" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+".$ClassSettingsInfo->EndClassTime." minutes"))); ?>" class="form-control">
          </div>
      </div>
  </div>

    

   <div class="row">

       <div class="col-md-4">
           <div class="form-group">
               <label><?= lang('lesson_prop') ?></label>
               <select class="form-control text-right" name="ClassType" id="ClassType" dir="rtl">
                   <option value="1" selected><?= lang('permanent_lesson') ?></option>
                   <option value="2"><?= lang('limited_lesson') ?></option>
                   <option value="3"><?= lang('single_time_lesson') ?></option>
               </select>
           </div>
       </div>



       <div id="DivClassType" class="col-md-3" style="display: none;">
           <div class="form-group">
               <label><?= lang('rep_times_weeks') ?></label>
               <input type="text" class="form-control" name="ClassCount" id="ClassCount" value="" min="1" onkeypress='validate(event)'>
           </div>
       </div>





       <div class="col-md-5">
           <div class="form-group">
               <label><?= lang('display_gym_equipment') ?></label>
               <select class="form-control js-example-basic-single select2Desk text-right" name="ClassDevice" id="ClassDevice" dir="rtl" data-placeholder="<?= lang('choose_gym_equipment_table') ?>" >
                   <option value=""></option>
                   <?php
                   $SectionInfos = DB::table('numbers')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Name', 'ASC')->get();
                   foreach ($SectionInfos as $SectionInfo) {
                       ?>
                       <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Name; ?></option>
                       <?php
                   }
                   ?>
               </select>
           </div>
       </div>
 </div>       

<hr>

    <div class="form-group">
        <label><?= lang('choose_membership_type_booking_lesson') ?></label>
        <select class="form-control js-example-basic-single select2multipleDesk text-right" name="ClassMemberType[]" id="ClassMemberType" dir="rtl"  multiple="multiple" >
            <option value=""></option>
            <option value="BA999"><?= lang('all_membership_types') ?></option>
            <?php
            $SectionInfos = DB::table('membership_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
            foreach ($SectionInfos as $SectionInfo) {
                ?>
                <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Type; ?></option>
                <?php
            }
            ?>
        </select>
    </div>

<hr>

  <div class="row">

      <div class="col-md-6">
          <div class="form-group">
              <label><?= lang('display_participants_amount') ?></label>
              <select class="form-control text-right" name="ShowClientNum" id="ShowClientNum" dir="rtl">
                  <option value="0"><?= lang('yes') ?></option>
                  <option value="1" selected><?= lang('no') ?></option>
              </select>
          </div>
      </div>
      <div class="col-md-6">
          <div class="form-group">
              <label><?= lang('display_participants_names') ?></label>
              <select class="form-control text-right" name="ShowClientName" id="ShowClientName" dir="rtl">
                  <option value="0"><?= lang('yes') ?></option>
                  <option value="1" selected><?= lang('no') ?></option>
              </select>
          </div>
      </div>

 </div>    



	

  <div class="row">
      <div class="col-md-4">
          <div class="form-group">
              <label><?= lang('allow_wlist') ?></label>
              <select class="form-control text-right" name="ClassWating" id="ClassWating" dir="rtl">
                  <option value="0" selected><?= lang('yes') ?></option>
                  <option value="1"><?= lang('no') ?></option>
              </select>
          </div>
      </div>


      <div class="col-md-4" id="WatingListDiv" style="display: block;">
          <div class="form-group">
              <label><?= lang('limit_wlist') ?></label>
              <select class="form-control text-right" name="MaxWatingList" id="WatingListAct" dir="rtl">
                  <option value="0"><?= lang('yes') ?></option>
                  <option value="1" selected><?= lang('no') ?></option>
              </select>

          </div>
      </div>


      <div id="WatingListNumDiv" class="col-md-4"  style="display: none;">
          <div class="form-group">
              <label><?= lang('max_waiting') ?></label>
              <input type="number" class="form-control" name="NumMaxWatingList" id="WatingListNum" value="" onkeypress='validate(event)'>
          </div>
      </div>
  </div>	

  <hr>	

	

   <div class="row">

       <div class="col-md-4">
           <div class="form-group">
               <label><?= lang('send_reminder_to_client') ?></label>
               <select class="form-control text-right" name="SendReminder" id="SendReminder" dir="rtl">
                   <option value="0" selected><?= lang('yes') ?></option>
                   <option value="1"><?= lang('no') ?></option>
               </select>
           </div>
       </div>



       <div class="col-md-4">
           <div class="form-group">
               <label><?= lang('define_time_to_send_reminder') ?></label>
               <select class="form-control text-right" name="TypeReminder" id="TypeReminder" dir="rtl">
                   <option value="1" selected><?= lang('in_lesson_day') ?></option>
                   <option value="2"><?= lang('day_before_lesson_day') ?></option>
               </select>

           </div>
       </div>



       <div class="col-md-4">
           <div class="form-group">
               <label><?= lang('set_time_sending_reminder') ?></label>
               <input type="time" class="form-control" name="TimeReminder" id="TimeReminder" step="300" value="" max="" min="">
           </div>
       </div>

 </div>  

    

    

   <hr>

   <div class="row">

       <div class="col-md-4">
           <div class="form-group">
               <label><?= lang('choose_cancel_rules') ?></label>
               <select class="form-control text-right" name="CancelLaw" id="CancelLaw" dir="rtl">
                   <option value="1" selected><?= lang('lesson_day_until_one_hour') ?></option>
                   <option value="2"><?= lang('day_before_until_one_hour') ?></option>
                   <option value="3"><?= lang('day_selection_until_hour') ?></option>
                   <option value="4"><?= lang('unable_to_cancel_in_app') ?></option>
                   <option value="5"><?= lang('free_cancel') ?></option>
               </select>
           </div>
       </div>



       <div id="DivCancelLaw3" class="col-md-4" style="display: none;">
           <div class="form-group">
               <label><?= lang('choose_day_before_lesson_day') ?></label>
               <select name="CancelDay" id="CancelDay" data-placeholder="<?= lang('choose_day') ?>" class="form-control" style="width:100%;">
                   <option value=""><?= lang('choose_day') ?></option>
               </select>
           </div>
       </div>



       <div id="DivCancelLaw" class="col-md-4">
           <div class="form-group">
               <label><?= lang('set_time_to_cancel') ?></label>
               <input name="CancelTillTime" id="CancelTillTime" type="time" step="300" min="" value="" class="form-control">
           </div>
       </div>



 </div>




    <div id="DivCancelLaw6" class="alertb alert-warning" style="display: none;"><?= html_entity_decode(nl2br(lang('attention_must_choose_day_before_lesson'))) ?></div>


    <div id="DivCancelLaw4" class="alertb alert-warning" style="display: none;"><?= lang('attention_this_option_wont_appear_to_client') ?></div>

    <div id="DivCancelLaw5" class="alertb alert-warning" style="display: none;"><?= lang('attention_client_cannot_cancel_anytime_free') ?></div>

    


	</div>



	  

</div>	 	



				</div>

            <div class="ip-modal-footer">
                <div class="ip-actions">
                    <button type="submit" name="submit" id="SendCalForm" class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                </div>
                </form>
                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php echo lang('close') ?></a>


            </div>

			</div>

		</div>

	</div>



<style>



.select2-results__option[aria-selected=true] {

    display: none;

}

</style>



<script>



    

	  function UpdateClass(ClassId, Act) {

		

if (Act=='0'){

$( "#DivViewDeskInfo" ).empty();

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('<?= lang('class_details') ?>');

    

 modalcode.modal('show'); 

 var url = 'new/ClassViewDesks.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });    

    

}	

    

else if (Act=='1'){

$( "#DivViewDeskInfo" ).empty();	

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('<?= lang('edit_lesson') ?>');

    

 modalcode.modal('show'); 

 var url = 'new/ClassEditDesk.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });    

    

}

                   

else if (Act=='2'){

$( "#DivViewDeskInfo" ).empty();	 

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('<?= lang('set_trainees') ?>');

    

 modalcode.modal('show'); 

 var url = 'new/ClientList.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url); 



  $('#DivViewDeskInfo').load(url,function(e){    

  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  

   return false;     

  });

    

    

}          

          

          

	};



</script>





<script>

$( ".select2Desk" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl", allowClear:"true" } );
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "<?= lang('choose_membership_type') ?>", 'language':"he", dir: "rtl" } );

$("#Day").change(function() {

    var Id = this.value;
    if (Id=='0') {
/// ראשון
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="6"><?=lang("saturday")?></option><option value="5"><?=lang("friday")?></option><option value="4"><?=lang("thursday")?></option><option value="3"><?=lang("wednesday")?></option><option value="2"><?=lang("tuesday")?></option><option value="1"><?=lang("monday")?></option><option value="0" disabled><?=lang("sunday")?></option>');
    }
    else if (Id=='1') {
/// שני
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="0"><?=lang("sunday")?></option><option value="6"><?=lang("saturday")?></option><option value="5"><?=lang("friday")?></option><option value="4"><?=lang("thursday")?></option><option value="3"><?=lang("wednesday")?></option><option value="2"><?=lang("tuesday")?></option><option value="1" disabled><?=lang("monday")?></option>');
    }
    else if (Id=='2') {
/// שלישי
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="1"><?=lang("monday")?></option><option value="0"><?=lang("sunday")?></option><option value="6"><?=lang("saturday")?></option><option value="5"><?=lang("friday")?></option><option value="4"><?=lang("thursday")?></option><option value="3"><?=lang("wednesday")?></option><option value="2" disabled><?=lang("tuesday")?></option>');
    }
    else if (Id=='3') {
/// רביעי
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="2"><?=lang("tuesday")?></option><option value="1"><?=lang("monday")?></option><option value="0"><?=lang("sunday")?></option><option value="6"><?=lang("saturday")?></option><option value="5"><?=lang("friday")?></option><option value="4"><?=lang("thursday")?></option><option value="3" disabled><?=lang("wednesday")?></option>');
    }
    else if (Id=='4') {
/// חמישי
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="3"><?=lang("wednesday")?></option><option value="2"><?=lang("tuesday")?></option><option value="1"><?=lang("monday")?></option><option value="0"><?=lang("sunday")?></option><option value="6"><?=lang("saturday")?></option><option value="5"><?=lang("friday")?></option><option value="4" disabled><?=lang("thursday")?></option>');
    }
    else if (Id=='5') {
/// שישי
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="4"><?=lang("thursday")?></option><option value="3"><?=lang("wednesday")?></option><option value="2"><?=lang("tuesday")?></option><option value="1"><?=lang("monday")?></option><option value="0"><?=lang("sunday")?></option><option value="6"><?=lang("saturday")?></option><option value="5" disabled><?=lang("friday")?></option>');
    }
    else if (Id=='6') {
/// שבת
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option><option value="5"><?=lang("friday")?></option><option value="4"><?=lang("thursday")?></option><option value="3"><?=lang("wednesday")?></option><option value="2"><?=lang("tuesday")?></option><option value="1"><?=lang("monday")?></option><option value="0"><?=lang("sunday")?></option><option value="6" disabled><?=lang("saturday")?></option>');
    }
    else {
        $('#CancelDay').find('option').remove().end().append('<option value=""><?=lang("choose_day")?></option>');
    }


});    

    

$("#MinClass").change(function() {

  

  var Id = this.value;

  if (Id=='0'){    

  DivMinClassNum1.style.display = "none";

  DivMinClassNum2.style.display = "none";      

  } 

  else {

  DivMinClassNum1.style.display = "block";

  DivMinClassNum2.style.display = "block";  

    

  var MaxClient = $('#MaxClient').val();      

  $('#MinClassNum').prop('max', MaxClient);

  $('#MinClassNum').prop('min', '1');      

      

  }    

});	





$("#ClassWating").change(function() {

  

  var Id = this.value;

  if (Id=='0'){    

  WatingListDiv.style.display = "block";      

  } 

  else {

  WatingListDiv.style.display = "none";

  WatingListNumDiv.style.display = "none";	  

  }  

	

  $('#WatingListAct').val('1').trigger('change');

  $('#WatingListNum').prop('min', '');	

	

});	

	

$("#WatingListAct").change(function() {
  var Id = this.value;
  if (Id=='0'){
  WatingListNumDiv.style.display = "block";
  $('#WatingListNum').prop('min', '1');
  }
  else {
  WatingListNumDiv.style.display = "none";
  }
});		

	

$('#ClassMemberType').on('select2:select', function (e) {    

var selected = $(this).val();
  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){

      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?= lang('choose_membership_type') ?>", 'language':"he", dir: "rtl" } );
    }
  }
});	    


$("#MaxClient").change(function() {
  var MaxClient = $('#MaxClient').val();
  $('#MinClassNum').prop('max', MaxClient);
  $('#MinClassNum').prop('min', '1');
});	  

 $("#ClassNameType").change(function() {
  var ClassName = $('#ClassNameType').select2('data');
  $('#ClassName').val(ClassName[0].text);   

     if ($('#ClassNameType option:selected').length > 0) {
        $('#ClassName').val(ClassName[0].text);
    }
    else {
        $('#ClassName').val('');
    }
});	     

    

<?php 

    

if ($ClassSettingsInfo->ReminderTimeType=='1'){

$ReminderTimeType = 'minutes';    

}

else {

$ReminderTimeType = 'hours';    

}  

                    

if ($ClassSettingsInfo->CancelTimeType=='1'){

$CancelTimeType = 'minutes';    

}

else {

$CancelTimeType = 'hours';    

}                      

    

?>  

    

    

$("#ClassType").change(function() {

  

  var Id = this.value;

  if (Id=='1'){    

  DivClassType.style.display = "none";

  $('#ClassCount').val('999');      

  } 

  else if (Id=='2'){

  DivClassType.style.display = "block";

  $('#ClassCount').val('');   

  }    

  else {

   $('#ClassCount').val('1');

   DivClassType.style.display = "none";      

  }    

});	    

 

$("#TypeReminder").change(function() {

    

var SetTime = $('#SetTime').val();

var SetToTime = $('#SetToTime').val();

    

    

 var TypeReminder = $('#TypeReminder').val();

  if (TypeReminder=='1'){

 

  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');

  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#TimeReminder').prop('max', TimeReminderMax);

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val(TimeReminderVal);  

      

  }    

  else if (TypeReminder=='2') {

   

  $('#TimeReminder').prop('max', '');

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      

        

      

  }  

    

    

  var CancelLaw = $('#CancelLaw').val();

  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){

 

  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');

  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#CancelTillTime').prop('max', CancelLawMax);

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val(CancelLawVal);  

      

  }    

  else if (CancelLaw=='2' || CancelLaw=='3') {

   

  $('#CancelTillTime').prop('max', '');

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      

        

      

  }          

    

 });	   

$("#CancelLaw").change(function() {

  

  var Id = this.value;

  if (Id=='1'){    

  DivCancelLaw.style.display = "block";      

  DivCancelLaw3.style.display = "none";

  DivCancelLaw4.style.display = "none";

  DivCancelLaw5.style.display = "none";

  DivCancelLaw6.style.display = "none";      

  } 

  else if (Id=='2'){

  DivCancelLaw.style.display = "block";      

  DivCancelLaw3.style.display = "none";

  DivCancelLaw4.style.display = "none";

  DivCancelLaw5.style.display = "none";

  DivCancelLaw6.style.display = "none";      

  }  

  else if (Id=='3'){

  DivCancelLaw.style.display = "block";      

  DivCancelLaw3.style.display = "block";

  DivCancelLaw4.style.display = "none";

  DivCancelLaw5.style.display = "none";

  DivCancelLaw6.style.display = "block";      

  }  

  else if (Id=='4'){

  DivCancelLaw.style.display = "none";      

  DivCancelLaw3.style.display = "none";

  DivCancelLaw4.style.display = "block";

  DivCancelLaw5.style.display = "none";

  DivCancelLaw6.style.display = "none";      

  }  

  else if (Id=='5'){

  DivCancelLaw.style.display = "none";      

  DivCancelLaw3.style.display = "none";

  DivCancelLaw4.style.display = "none";

  DivCancelLaw5.style.display = "block";   

  }      

  else {

  DivCancelLaw.style.display = "block";      

  DivCancelLaw3.style.display = "none";

  DivCancelLaw4.style.display = "none";

  DivCancelLaw5.style.display = "none"; 

  DivCancelLaw6.style.display = "none";      

  } 

    

    

var SetTime = $('#SetTime').val();

var SetToTime = $('#SetToTime').val();



    

    

  var CancelLaw = $('#CancelLaw').val();

  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){

 

  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');

  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#CancelTillTime').prop('max', CancelLawMax);

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val(CancelLawVal);  

      

  }    

  else if (CancelLaw=='2' || CancelLaw=='3') {

   

  $('#CancelTillTime').prop('max', '');

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      

        

      

  }      

    

    

});	     

    

    

    

$(document).ready(function() {

    $(".ip-close").click(function(){

    $('#FormCalendarClient').trigger("reset");

    $('#FormAddClassDesk').trigger("reset");    

    $('#FormAddClassDesk').find('.alert').hide();      

    }); 

});    

     



$('#SetTime').on('change', function() {





/// שנה גלילה לפי שעה	





var SetTime = $('#SetTime').val();

var FixToTime = moment(SetTime,'HH:mm:ss').add(<?php echo @$ClassSettingsInfo->EndClassTime; ?>,'minutes').format('HH:mm:ss') ;   

var FixToTimes = moment(SetTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;

var FixToTimeCancel = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');

    

$('#SetToTime').val(FixToTime); 

$('#SetToTime').prop('min', FixToTimes);

$('#CancelTillTime').prop('max', SetTime);   

$('#CancelTillTime').val(FixToTimeCancel);    

  

    

    

 var TypeReminder = $('#TypeReminder').val();

  if (TypeReminder=='1'){

 

  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');

  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#TimeReminder').prop('max', TimeReminderMax);

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val(TimeReminderVal);  

      

  }    

  else if (TypeReminder=='2') {

   

  $('#TimeReminder').prop('max', '');

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      

        

      

  }  

    

    

  var CancelLaw = $('#CancelLaw').val();

  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){

 

  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');

  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#CancelTillTime').prop('max', CancelLawMax);

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val(CancelLawVal);  

      

  }    

  else if (CancelLaw=='2' || CancelLaw=='3') {

   

  $('#CancelTillTime').prop('max', '');

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      

        

      

  }       

			   

});	

	

$('#SetToTime').on('change', function() {





var SetTime = $('#SetTime').val();

var SetToTime = $('#SetToTime').val();

    

    

 var TypeReminder = $('#TypeReminder').val();

  if (TypeReminder=='1'){

 

  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');

  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#TimeReminder').prop('max', TimeReminderMax);

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val(TimeReminderVal);  

      

  }    

  else if (TypeReminder=='2') {

   

  $('#TimeReminder').prop('max', '');

  $('#TimeReminder').prop('min', '');      

  $('#TimeReminder').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      

        

      

  }  

    

    

  var CancelLaw = $('#CancelLaw').val();

  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){

 

  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');

  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');

  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      



  $('#CancelTillTime').prop('max', CancelLawMax);

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val(CancelLawVal);  

      

  }    

  else if (CancelLaw=='2' || CancelLaw=='3') {

   

  $('#CancelTillTime').prop('max', '');

  $('#CancelTillTime').prop('min', '');      

  $('#CancelTillTime').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      

        

      

  }      

    



	

				   

});	



    

$(".changestatus").click( function()

{    

//    

//var Id = $(this).data("id");

//var Status = $(this).data("status");    

    

alert('Id');    

    

});    


    

$( "#ChooseAgentForTask" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );



$(function() {

			var time = function(){return'?'+new Date().getTime()};



		$('#AddNewTask').imgPicker({



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