<?php
require_once __DIR__ . '/../../app/init.php';

// secure page
if (!Auth::check()) {
    redirect_to(__DIR__ . '/../../index.php');
}

$CompanyNum = Auth::user()->CompanyNum;
$Coaches = Users::getAllCoachesByCompanyNum($CompanyNum);
$MeetingStatuses = MeetingStatus::filterNames();

$filter = $_REQUEST["filter"] ?? 'all';
if ($filter != 'all') {
    $filter = MeetingStatus::name($filter);
}

$reportName = lang('meeting_report_title');
$pageTitle = $reportName;

$tableTypes = [
    0 => 'meetingReport-ordered',
    1 => 'meetingReport-timed'
];

include __DIR__ . '/../../app/views/headernew.php';
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
<script src="../js/datatable/dataTables.checkboxes.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<link href="../assets/css/fixstyle.css?<?= filemtime("../assets/css/fixstyle.css") ?>" rel="stylesheet">

<style>
    /* not active */
    .pill-1:not(.active) {
        /*    background-color: rgba(255, 0, 0, 0.5);*/
        color: #838383 !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: none !important;
    }

    /* active (faded) */
    .pill-1 {
        color: #1d2124 !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom-width: medium !important;
        border-bottom-color: #1d2124 !important;
    }

    #meetingReport-tab-content .d-flex {
        width: 25%;
    }

    @media screen  and (max-width: 1024px) {
        #meetingReport-tab-content .d-flex {
            width: 100%;
        }
    }
</style>
<div class="text-end py-10 d-inline-flex ">
    <label class="mie-7 align-self-center font-weight-bold " for=""><?= lang('date_range') ?>:</label>
    <input type="text" name="date" class="form-control width-fit js-date-range">
</div>
<div class="row px-0">
    <div class="col-12 px-0">
        <div class="row m-0">
            <div class="col-md-12 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active " role="tabpanel" id="user-overview">

                        <!-- page content -->

                        <nav class="nav nav-tabs nav-fill pb-15" id="meetingReport-nav-tabs" role="tablist">
                            <a class="nav-item nav-link pill-1 cursor-pointer active" id="meetingReport-ordered-tab" data-toggle="tab"
                               href="#meetingReport-ordered" role="tab" aria-controls="meetingReport-ordered" aria-selected="true">
                                <strong><?= lang('by_meeting_time') ?></strong>
                            </a>
                            <a class="nav-item nav-link pill-1 cursor-pointer" id="meetingReport-timed-tab" data-toggle="tab"
                               href="#meetingReport-timed" role="tab" aria-controls="meetingReport-timed" aria-selected="false">
                                <strong><?= lang('by_order_time') ?></strong>
                            </a>
                        </nav>

                        <div class="tab-content" id="meetingReport-tab-content">
                            <?php foreach ($tableTypes as $k => $v) { ?>
                                <div class="tab-pane fade show <?= $k == 0 ? 'active' : '' ?>" id="<?= $v ?>" role="tabpanel" aria-labelledby="<?= $v ?>-tab">
                                    <div>
                                        <div class="d-flex">
                                            <div class="container-fluid text-start font-weight-bold">
                                                <?= lang('search_by_status') ?>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="statusFilter<?= $k ?> container-fluid text-start">
                                                <select class="form-control" multiple="multiple">
                                                    <optgroup label="<?= lang('choose_status') ?>">
                                                        <?php foreach ($MeetingStatuses as $MeetingStatus) { ?>
                                                            <option value="<?= $MeetingStatus ?>" <?= $MeetingStatus == $filter ? 'selected' : '' ?>><?= $MeetingStatus ?></option>
                                                        <?php } ?>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <table class="table table-hover dt-responsive display wrap text-start"
                                               id="attendanceTable<?= $k ?>"
                                               cellspacing="0" width="100%">
                                            <thead>
                                            <tr class="">
                                                <th class="text-start"></th>
                                                <th class="text-start"><?= lang('client_name') ?></th>
                                                <th class="text-start"><?= lang('meeting_report_meeting_name') ?></th>
                                                <th class="text-start"><?= lang('date'). ' '.lang('docs_order') ?></th>
                                                <th class="text-start"><?= lang('date'). ' '.lang('meeting_single') ?></th>
                                                <th class="text-start"><?= lang('meetings_report_duration') ?></th>
                                                <th class="text-start"><?= lang('price') ?></th>
                                                <th class="text-start"><?= lang('status') ?></th>
                                                <th class="text-start"><?= lang('instructor') ?></th>
                                                <th class="text-start lastborder"><?= lang('remainder_of_payment') ?></th>
                                            </tr>
                                            <tr class="searchBar<?= $k ?>">
                                                <th class="text-start"></th>
                                                <th class="text-start"><?= lang('client_name') ?></th>
                                                <th class="text-start"><?= lang('meeting_report_meeting_name') ?></th>
                                                <th class="text-start"><?= lang('date'). ' '.lang('docs_order') ?></th>
                                                <th class="text-start"><?= lang('date'). ' '.lang('meeting_single') ?></th>
                                                <th class="text-start"><?= lang('meetings_report_duration') ?></th>
                                                <th class="text-start"><?= lang('price') ?></th>
                                                <th class="text-start">

                                                </th>
                                                <th class="text-start guideFilter<?= $k ?>">
                                                    <select class="form-control">
                                                        <option value=""><?= lang('all') ?></option>
                                                        <?php foreach ($Coaches as $Coach) { ?>
                                                            <option value="<?= $Coach->display_name; ?>"><?= $Coach->display_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </th>
                                                <th class="text-start"><?= lang('remainder_of_payment') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <?php for($i = 0; $i < 10; $i++) { ?>
                                                    <th class="text-start"></th>
                                                <?php } ?>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        $(document).ready(function () {

            const data = {
                start: moment().subtract(1, 'months'),
                end: moment()
            }

            BeePOS.options.datatables = <?= json_encode(trans('datatables')); ?>;
            <?php foreach ($tableTypes as $k => $v) { ?>

            $('#attendanceTable<?= $k ?> .searchBar<?= $k ?> th').each(function (index) {
                if (!$(this).hasClass("statusFilter<?= $k ?>") &&
                    !$(this).hasClass("guideFilter<?= $k ?>") && index > 0) {
                    $(this).html('<input type="text" placeholder="' + $(this).text() + '" style="width:90%;" class="form-control" ' + (index == 7 || index == 8 ? 'disabled' : '') + ' />');
                }
            });

            const dataTable<?= $k ?> = $('#attendanceTable<?= $k ?>').dataTable({
                language: BeePOS.options.datatables,
                responsive: true,
                processing: true,
                paging: true,
                scrollX: true,
                scrollY: "450px",
                scrollCollapse: false,
                lengthMenu: [[10, 25, 50, 75, 100, 150, 200, 250, 300, 500, -1], [10, 25, 50, 75, 100, 150, 200, 250, 300, 500, "הכל"]],
                pageLength: 50,
                order: [[3, 'asc']],
                columnDefs: [
                    {
                        'targets': [0],
                        'checkboxes': {
                            'selectRow': true
                        },
                        bSortable: false,
                        orderable: false
                    },
                ],
                select: {
                    style: 'multi'
                },
                drawCallback: () => {
                    $('.dt-checkboxes-cell').removeClass('dt-checkboxes-cell')
                },
                dom: '<<"d-flex justify-content-between w-100 mb-10" <rl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

                <?php if (Auth::userCan('98')): ?>
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel <i class="far fa-file-excel aria-hidden="true"></i>',
                        filename: '<?php echo lang('reports_presence') ?>',
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
                    url: 'MeetingsReportPost.php',
                    data: function (d) {
                        d.start = moment(data.start).format('YYYY-MM-DD'),
                            d.end = moment(data.end).format('YYYY-MM-DD'),
                            d.companyNum = <?= $CompanyNum ?>,
                            d.filter = <?= $k ?>
                    },
                    method: 'POST'
                },
                fnInitComplete: function () {
                    // apply filter after load
                    $('#attendanceTable<?= $k ?>').DataTable().column('7').search($('.statusFilter<?= $k ?> select').val()).draw();
                }
            });

            const table<?= $k ?> = $('#attendanceTable<?= $k ?>').DataTable();
            table<?= $k ?>.columns().every(function () {
                const that = this;

                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });

            $('.guideFilter<?= $k ?> select').on('change', function () {
                table<?= $k ?>.column('8').search(this.value).draw();
            });

            $('.statusFilter<?= $k ?> select').on('change', function () {
                let searchText = null;
                let option = null;

                for (let i = 0; i < this.options.length; i++) {
                    option = this.options[i];
                    if (option.selected == true) {
                        searchText = i != 0 ? searchText + '|' + option.value : option.value;
                    }
                }

                table<?= $k ?>.column('7').search(searchText ?? '', true, false).draw();
            });

            $('.statusFilter<?= $k ?> select').bsappMultiSelect({
                searchPlaceholder: 'חיפוש לפי סטטוס פגישה'
            });

            <?php } ?>

            // Correct dataTable header to body
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            $('.js-date-range').daterangepicker({
                startDate: data.start,
                endDate: data.end,
                language: 'he',
                locale: {
                    format: 'DD/MM/YY',
                    "applyLabel": "<?php echo lang('approval') ?>",
                    "cancelLabel": "<?php echo lang('cancel') ?>",
                }
            }).on('apply.daterangepicker', function (ev, picker) {
                data.start = picker.startDate.format('YYYY-MM-DD');
                data.end = picker.endDate.format('YYYY-MM-DD');
                dataTable0.DataTable().ajax.reload();
                dataTable1.DataTable().ajax.reload();
            });


            $('.dt-checkboxes-cell').removeClass('dt-checkboxes-cell')
        });
    </script>

    <!-- popupSendByClientId -->
    <?php include __DIR__ . '/popupSendByClientId.php'; ?>

    <?php include __DIR__ . '/../../app/views/footernew.php'; ?>
