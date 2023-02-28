<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

$report = new StdClass();
$report->name = lang('trial_lesson_title');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';



?>
<link
    href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">


<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js">
</script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js">
</script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js">
</script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js">-->
</script>
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
</script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js">
</script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
</script>
<script src="../js/datatable/dataTables.checkboxes.min.js">
</script>

<link href="../assets/css/fixstyle.css" rel="stylesheet">
<style>
    .bg-gray {
        background-color: #e9ecef;
    }

    .dataTables_scrollHead table {
        margin-bottom: 0px;
    }
    div.dataTables_wrapper div.dataTables_processing {
        position: fixed;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    z-index: 999;
    background: #38ab4b;
    color: #fff;
}
th.appendInputs {white-space: nowrap;}
th.appendInputs input{display: inline-block; max-width: 60px}
th.appendInputs select{display: inline-block; max-width: 20px}
.select2-container .select2-search__field {
    width: 100% !important;
}
</style>

<!-- <div class="row pb-3">
    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <?php //echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
                <?php //echo  $report->name ?>
            </div>
        </h3>
    </div>
</div> -->

<div class="row px-0 mx-0" >
    <div class="col-12 px-0 mx-0" >

        <div class="row">

            <?php include("../ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div class="row px-0 mx-0">

                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th data-name="select" data-bSortable="false"></th>
                                                <th data-name="classDate"><?php echo lang('date') ?></th>
                                                <th data-name="clientFullName"><?php echo lang('client_name') ?></th>
                                                <!-- <th data-name="clientPhone">טלפון</th> -->
                                                <!-- <th data-name="clientGender">מין</th> -->
                                                <th data-name="clientMembership"><?php echo lang('membership_type_single') ?></th>
                                                <th data-name="clientStatus"><?php echo lang('status') ?></th>
                                                <th data-name="clientIsActive"><?php echo lang('sale_convertion') ?></th>
                                                <th data-name="className"><?php echo lang('class_name') ?></th>
                                                
                                                <th data-name="classInstructor"><?php echo lang('instructor') ?></th>
                                                <th data-name="classStatus"><?php echo lang('registration_status') ?></th>
                                                <th data-name="classLocation"><?php echo lang('location') ?></th>
                                                <th data-name="classBranch"><?php echo lang('branch') ?></th>
                                                <th data-name="classComment"><?php echo lang('reports_class_notes') ?></th>
                                                <th data-name="clientMedical"><?php echo lang('medical_information') ?></th>
                                            </tr>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="classDate">
                                                    <input type="text" class="form-control" data-search="date" data-search-type="dateRange" data-date-start="now" data-date-end="7">
                                                </th>
                                                <th data-name="clientFullName">
                                                    <input type="text" placeholder="<?php echo lang('search_by_name') ?>" data-search="name" class="form-control">
                                                 </th>
                                                <!-- <th data-name="clientPhone">
                                                    <input type="text" placeholder="חפש לפי שם" data-search="phone" class="form-control">
                                                </th>
                                                <th data-name="clientGender">
                                                    <select data-search="genderIds" multiple placeholder="חפש לפי מגדר" class="form-control" style="width: 100%">
                                                        <option value="0">לא ידוע</option>
                                                        <option value="1">זכר</option>
                                                        <option value="2">נקבה</option>
                                                    </select>
                                                </th>  -->
                                                <th data-name="clientMembership">
                                                    <select data-search="membershipIds" multiple placeholder="<?php echo lang('search_by_membership_type') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="clientStatus">
                                                    <select data-search="statusIds" multiple placeholder="<?php echo lang('search_by_status') ?>" class="form-control" style="width: 100%">
                                                        <option value="0"><?php echo lang('active') ?></option>
                                                        <option value="1"><?php echo lang('archive') ?></option>    
                                                        <option value="2"><?php echo lang('interested_single') ?></option>    
                                                    </select>
                                                </th>
                                                <th data-name="clientIsActive">
                                                    <select data-search="clientIsActive" multiple placeholder="<?php echo lang('search_by_gender') ?>" class="form-control" style="width: 100%">
                                                        <option value="true"><?php echo lang('yes') ?></option>
                                                        <option value="false"><?php echo lang('no') ?></option>    
                                                    </select>
                                                </th>
                                                <th data-name="className">
                                                    <select data-search="classIds" multiple placeholder="<?php echo lang('search_by_class') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="classInstructor">
                                                    <select data-search="instructorIds" multiple placeholder="<?php echo lang('search_by_coach') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="classStatus">
                                                    <select data-search="classStatusIds" multiple placeholder="<?php echo lang('search_by_status') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="classLocation">
                                                    <select data-search="classLocationIds" multiple placeholder="<?php echo lang('search_by_location_try') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="classBranch">
                                                    <select data-search="classBranchIds" multiple placeholder="<?php echo lang('search_by_branch') ?>" class="form-control" style="width: 100%"></select>
                                                </th>
                                                <th data-name="classComment"></th>
                                                <th data-name="clientMedical"></th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="classDate"></th>
                                                <th data-name="clientFullName"></th>
                                                <!-- <th data-name="clientPhone"></th>
                                                <th data-name="clientGender"></th> -->
                                                <th data-name="clientMembership"></th>
                                                <th data-name="clientStatus"></th>
                                                <th data-name="clientIsActive"></th>
                                                <th data-name="className"></th>
                                                <th data-name="classInstructor"></th>
                                                <th data-name="classStatus"></th>
                                                <th data-name="classLocation"></th>
                                                <th data-name="classBranch"></th>
                                                <th data-name="classComment"></th>
                                                <th data-name="clientMedical"></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    

                                    


                                </div>


                            </div>
                        </div>
                    </div>
                </div>

    </div>

<!--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .datepicker-dropdown {
            max-width: 300px;
        }

        .datepicker {
            float: right
        }

        .datepicker.dropdown-menu {
            right: auto
        }
    </style>
    <script>
        var BeePOS = BeePOS || {};
            BeePOS.options = BeePOS.options || {};
            BeePOS.options.api = '<?php echo App::url('api/') ?>';
            BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
            BeePOS.options.datatables.processing = '<i class="fas fa-spinner fa-spin"></i> ' + BeePOS.options.datatables.processing
        var boostAppDataTable = {
            allowDebug: <?php echo Auth::user()->role_id == '1' ? 'true' : 'false'; ?>,
            buttons: {
                allowClientPush: true,
                excel: <?php echo Auth::userCan('98')?true:false; ?>,
                csv: <?php echo Auth::userCan('98')?true:false; ?>
            }
        };
    </script>
    <script src="./js/tryOut.js"></script>


    <!-- popupSendByClientId -->
    <?php include('./popupSendByClientId.php'); ?>



    <?php 
        require_once '../../app/views/footernew.php';
    ?>