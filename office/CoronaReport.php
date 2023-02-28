<?php
require_once '../app/init.php';
redirect_to('/office/SettingsDashboard.php');
// The page is hidden by the request on BP-1844

require_once 'Classes/CoronaHealthCheck.php';
$reportName = lang('health_declaration_covid');
$pageTitle = $reportName;
require_once '../app/views/headernew.php';


if (Auth::check()){
    if (Auth::userCan('138')){


        
        ?>
        <link href="assets/css/fixstyle.css" rel="stylesheet">

        <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

        <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
        <link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-IIED/eyOkM6ihtOiQsX2zizxFBphgnv1zbe1bKA+njdFzkr6cDNy16jfIKWu4FNH" crossorigin="anonymous">

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
        <script src="js/datatable/dataTables.checkboxes.min.js"></script>

    <script>
        $(document).ready(function() {
            BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
            var categoriesDataTable = $('#report').dataTable({
                language: BeePOS.options.datatables,
                responsive: true,
                processing: true,
                // "paging": false,
                dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                // lengthChange: true,
                pageLength: 50,
                lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "<?php echo lang('all') ?>"]],

                buttons: [
                    <?php if (Auth::userCan('98')){?>
                    //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                    {
                        extend: 'excelHtml5',
                        text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                        filename: '<?php echo lang('covid_report') ?>',
                        className: 'btn btn-dark'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                        filename: '<?php echo lang('covid_report') ?>',
                        className: 'btn btn-dark'
                    },
                    // {
                    //     extend: 'print',
                    //     text: 'הדפסה <i class="fas fa-print" aria-hidden="true"></i>',
                    //     className: 'btn btn-dark',
                    //     customize: function (win) {
                    //         // https://datatables.net/reference/button/print
                    //         jQuery(win.document).ready(function () {
                    //             $(win.document.body)
                    //                 .css('direction', 'rtl')
                    //         });
                    //     }
                    // },
                    // 'pdfHtml5'
                    <?php } ?>

                ],

                ajax: {
                    url: 'CoronaReportPost.php',
                    method: 'POST',
                    data: function (d) {
                    }
                },

                order: [[2, 'DESC']]


            });
        });

    </script>
        <link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="row px-0 mx-0" >
        <div class="col-12 px-0 mx-0" >

            <!-- <nav aria-label="breadcrumb" >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/index.php" class="text-info">ראשי</a>
                    </li>
                    <li class="breadcrumb-item active">דוחות</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php //echo  $reportName ?>
                    </li>
                </ol>
            </nav> -->

            <div class="row">
                <?php include("ReportsInc/SideMenu.php"); ?>
                <div class="col-md-10 col-sm-12">
                    <div class="tab-content">
                        <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                            <div class="card spacebottom">
                                <div class="card-header text-start">
                                    <i class="fas fa-user-plus"></i>
                                    <strong>
                                        <?php echo $reportName ?>
                                    </strong>
                                </div>
                                <div class="card-body">

                                    <!-- page content -->
                                    <hr>

                                    <div class="row px-15"  >
                                        <table class="table table-hover dt-responsive text-start display wrap" id="report"  cellspacing="0" width="100%">
                                            <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"><?php echo lang('client_name') ?></th>
                                                <th style="text-align:start;"><?php echo lang('branch') ?></th>
                                                <th style="text-align:start;"><?php echo lang('telephone') ?></th>
                                                <th style="text-align:start;"><?php echo lang('email_table') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_single') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_time') ?></th>
                                                <th style="text-align:start;"><?php echo lang('update_date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('covid_declaration') ?></th>
                                            </tr>

                                            </thead>

                                            <tbody>

                                            </tbody>

                                        </table>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<style type="text/css">
    .dataTables_wrapper ul.pagination{
        padding-inline-start : unset !important; 
    }
</style>
<?php    }
require_once '../app/views/footernew.php';
}
?>
