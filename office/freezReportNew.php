<?php

require_once '../app/init.php';
require_once "Classes/Company.php";
require_once  "Classes/ClassStudioDate.php";
//require_once "Classes/Client.php";
error_reporting(E_ALL);
ini_set("display_errors", true);

$pageTitle = lang('trainers_report_for_class');
if (!Auth::check())
    redirect_to('../index.php');

$class_id = '';
$fields = [];
if (isset($_GET['id'])):
    $class_id = base64_decode($_GET['id']);
endif;
if (isset($_GET['fields'])):
    $fields = explode(",", base64_decode($_GET['fields']));
endif;


$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");
$class = new ClassStudioDate($class_id);
if ($class) {
    $pageTitle .= ' '.$class->__get('ClassName').' '.lang('in_date_ajax').' '.date('d/m/Y' ,strtotime($class->__get('StartDate')));
}
require_once '../app/views/headernew.php';

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
    <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>


    <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<!--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<!--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <!--link href="assets/css/fixstyle.css" rel="stylesheet"-->
    <style>
        th {
            font-size: 14px;
        }

        td {
            font-size: 14px;
        }
    </style>
    <!--div class="text-end py-10 d-inline-flex ">
        <label class="mie-7 align-self-center font-weight-bold " for=""><?php echo lang('date_range') ?>:</label>
        <input type="text" name="date" class="form-control width-fit js-date-range">
    </div-->
    <div class="row text-start px-15">
        <?php //include_once "ReportsInc/SideMenu.php";      ?>
        <div class="col-md-12 py-20 shadow rounded bsapp-datatable-container">
            <table id="activitiesTable"
                   class="table borderless table-hover dt-responsive text-start display wrap bsapp-datatable"
                   cellspacing="0" style="width:100%">
                <thead>
                <tr>
                    <th class="text-start"><?php echo lang('client_name') ?></th>
                    <?php if (in_array("customerPhone", $fields)): ?>
                        <th class="text-start"><?php echo lang('client_phone') ?></th><?php endif; ?>
                    <?php if (in_array("debtAmount", $fields)): ?>
                        <th class="text-start"><?php echo lang('reports_debt_ramain') ?></th><?php endif; ?>
                    <?php if (in_array("medicalInfo", $fields)): ?>
                        <th class="text-start"><?php echo lang('medical_information') ?></th><?php endif; ?>
                    <?php if (in_array("importantNotes", $fields)): ?>
                        <th class="text-start"><?php echo lang('imortant_notes') ?></th><?php endif; ?>
                    <?php if (in_array("permanentRegister", $fields)): ?>
                        <th class="text-start"><?php echo lang('setting_permanently') ?></th><?php endif; ?>
                    <?php if (in_array("birthday", $fields)): ?>
                        <th class="text-start"><?php echo lang('birthday_single') ?></th><?php endif; ?>
                    <?php if (in_array("freeClass", $fields)): ?>
                        <th class="text-start"><?php echo lang('free_or_paid_class') ?></th><?php endif; ?>
                    <?php if (in_array("firstClass", $fields)): ?>
                        <th class="text-start"><?php echo lang('first_lesson_or_trial_membership') ?></th><?php endif; ?>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <!--                <tfoot>-->
                <!--                    <tr>-->
                <!--                        <th class="text-start">-->
                <?php //echo lang('client_name') ?><!--</th>                        -->
                <?php //if (in_array("customerPhone", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('client_phone') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("debtAmount", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('reports_debt_ramain') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("medicalInfo", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('medical_information') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("importantNotes", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('imortant_notes') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("permanentRegister", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('setting_permanently') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("birthday", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('birthday_single') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("freeClass", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('free_or_paid_class') ?><!--</th>--><?php //endif; ?>
                <!--                        -->
                <?php //if (in_array("firstClass", $fields)): ?><!--<th class="text-start">-->
                <?php //echo lang('first_lesson_or_trial_membership') ?><!--</th>--><?php //endif; ?>
                <!---->
                <!--                    </tr>-->
                <!--                </tfoot>-->
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var data = {
                start: moment().startOf('month'),
                end: moment()
            }
            BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
            var dataTable = $('#activitiesTable').dataTable({
                language: BeePOS.options.datatables,
                responsive: true,
                processing: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 25,
                dom: '<<"d-flex justify-content-between w-100 mb-10" <"d-flex" r<"mie-15" f>l><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                // dom: 'lBfrtip',
                <?php if (Auth::userCan('98')): ?>
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel <i class="far fa-file-excel aria-hidden="true"></i>',
                        filename: '<?php echo $pageTitle; ?>',
                        className: 'btn btn-sm btn-light text-gray-400 font-weight-bold mie-5'
                    },
                    {
                        extend: 'print',
                        text: 'הדפסה <i class="far fa-print" aria-hidden="true"></i>',
                        className: 'btn btn-sm btn-light text-gray-400 font-weight-bold ',
                        customize: function (win) {
                            jQuery(win.document).ready(function () {
                                $(win.document.body).css('direction', 'rtl')
                            });
                        }
                    },
                ],
                <?php endif; ?>
                ajax: {
                    url: 'TrainersReportPost.php',
                    data: function (d) {
                        d.start = moment(data.start).format('YYYY-MM-DD'),
                            d.end = moment(data.end).format('YYYY-MM-DD'),
                            d.fields = '<?php echo $_GET['fields']; ?>',
                            d.id = '<?php echo $_GET['id']; ?>'
                    },
                    method: 'POST'
                },
                "initComplete": function (settings, json) {
                    $(".dataTables_length select").select2({
                        theme: "bsapp-dropdown",
                        minimumResultsForSearch: -1
                    });
                }
            });
            $('#activitiesTable_filter input').removeClass('form-control').addClass('form-control-custom');
            $('#activitiesTable_length select').removeClass('form-control').addClass('form-control-custom');
        });
    </script>
<?php
require_once '../app/views/footernew.php';