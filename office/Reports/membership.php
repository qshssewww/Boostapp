<?php
require_once '../../app/init.php';
// secure page
if (!Auth::check())
    redirect_to('../../index.php');

$report = new StdClass();
$report->name = lang('report_clients_with_membership');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';
?>
<link
    href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">


<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



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
<?php //echo $DateTitleHeader;  ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
<?php //echo $report->name  ?>
            </div>
        </h3>
    </div>
</div> -->

<div class="row px-0" >
    <div class="col-12 px-0">


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">????????</a>
                </li>
                <li class="breadcrumb-item active">??????????</li>
                <li class="breadcrumb-item active" aria-current="page">
<?php //echo  $report->name  ?>
                </li>
            </ol>
        </nav> -->

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
                                <div class="float-left">
                                    <select data-search="branchIds" placeholder="<?php echo lang('by_branch') ?>" id="branchesFilter" multiple="multiple" style="display: none; min-width: 150px;"></select>
                                </div>
                            </div>
                            <div class="card-body">

                                <!-- <div class="card">
                                    <div class="card-header">
                                        ?????????? ????????
                                    </div>
                                    <div class="card-body">
                                        <input id="exp_start_or_smaller_then_today" type="checkbox" name="exp_start_or_smaller_then_today" value="true">
                                        <label for="exp_start_or_smaller_then_today">???????????? ?????????? ???? ????????</label>
                                        
                                    </div>
                                </div> -->

                                <!-- page content -->
                                <hr>

                                <div class="row px-15"  >

                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th data-name="select" data-bSortable="false"></th>
                                                <th data-name="fullName"><?php echo lang('client_name') ?></th>
                                                <th data-name="phone"><?php echo lang('telephone') ?></th>
                                                <th data-name="department"><?php echo lang('class') ?></th>
                                                <th data-name="membership" data-bSortable="false"><?php echo lang('membership_type_single') ?></th>
                                                <th data-name="item" data-bSortable="false"><?php echo lang('product') ?></th>
                                                <th data-name="exp" data-bSortable="false"><?php echo lang('expires_at') ?></th>
                                                <th data-name="ticketHas" data-bSortable="false"><?php echo lang('punch') ?></th>
                                            </tr>
                                            <tr>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;">
                                                    <input data-search="clientName" type="text" name="clientName" id="clientNameFilter" class="form-control" placeholder="<?php echo lang('search_client') ?>">
                                                </th>
                                                <th style="text-align:start;">
                                                    <input data-search="clientPhone" type="text" name="clientPhone" id="clientPhoneFilter" class="form-control" placeholder="<?php echo lang('search_phone') ?>">
                                                </th>
                                                <th style="text-align:start;">
                                                    <select data-search="departmentIds" multiple="multiple" id="departmentFilter" class="form-control" size="1" placeholder="<?php echo lang('search_department') ?>"></select>
                                                </th>
                                                <th style="text-align:start;">
                                                    <select data-search="membershipIds" multiple="multiple" id="membershipFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_membership') ?>"></select>
                                                </th>
                                                <th style="text-align:start;"><select data-search="productIds" multiple="multiple" id="productsFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_product') ?>"></select></th>
                                                <th style="text-align:start;">
                                                    <input type="test" data-search="exp" data-search-type="dateRange" data-search-start="false" class="form-control" placeholder="<?php echo lang('by_dates') ?>">
                                                </th>
                                                <th style="text-align:start;" class="appendInputs">
                                                    <input type="number"  class="form-control" data-search="ticketHas" data-filter-type="select" placeholder="<?php echo lang('search_single') ?>">
                                                    <select class="form-control" data-filter="ticketHas">
                                                        <option value="equal"><?php echo lang('choose') ?></option>
                                                        <option value="bigger"><?php echo lang('big_or_even') ?></option>
                                                        <option value="smaller"><?php echo lang('small_or_event') ?></option>
                                                        <option value="equal"><?php echo lang('a_event') ?></option>
                                                    </select>
                                                    </div>

                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>






                                </div>

                                <div class="row"  style="padding-left:15px; padding-right:15px;">
                                    <div id="membershipReport" style="width: 100%"></div>
                                </div>

                            </div>
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
        BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        BeePOS.options.datatables.processing = '<i class="fas fa-spinner fa-spin"></i> ' + BeePOS.options.datatables.processing
        var boostAppDataTable = {
            buttons: {
                allowClientPush: true,
                excel: <?php echo Auth::userCan('98') ? 'true' : 'false'; ?>,
                csv: <?php echo Auth::userCan('98') ? 'true' : 'false'; ?>
            }
        };
    </script>
    <script src="./js/membership.js?v1.1.2"></script>

    <!-- popupSendByClientId -->
<?php include_once './popupSendByClientId.php'; ?>

    <?php
    require_once '../../app/views/footernew.php';
    ?>