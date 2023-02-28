<?php
require_once '../app/init.php';
require_once __DIR__ . '/Classes/WhatsAppNotifications.php';
require_once __DIR__ . '/Classes/Client.php';
require_once __DIR__ . '/Classes/Notificationcontent.php';

if (Auth::guest()) redirect_to('index.php');

if (Auth::check()):
    if (Auth::userCan('116')) {
        $pageTitle = lang('report_whatsapp_title');
        require_once '../app/views/headernew.php';

        $CompanyNum = Auth::user()->CompanyNum;
        $BusinessSettings = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();

        if (isset($_REQUEST['Dates'])) {
            $Dates = $_REQUEST['Dates'];
            $cMonth = date('m', strtotime($Dates));
            $cYear = date('Y', strtotime($Dates));
        } else {
            $cMonth = $_REQUEST["month"] ?? date("m");
            $cYear = $_REQUEST["year"] ?? date("Y");
            $Dates = $cYear . '-' . $cMonth;
        }

        $prev_year = $cYear;
        $next_year = $cYear;

        $prev_month = $cMonth - 1;
        $next_month = $cMonth + 1;

        if ($prev_month == 0) {
            $prev_month = 12;
            $prev_year = $cYear - 1;
        }

        if ($next_month == 13) {
            $next_month = 1;
            $next_year = $cYear + 1;
        }

        if (!isset($_GET['start']) && !isset($_GET['end'])) {
            $StartDate = $cYear . '/' . $cMonth . '/01';
            $EndDate = $cYear . '/' . $cMonth . '/30';
        } else {
            $StartDate = $_GET["start"];
            $EndDate = $_GET["end"];
        }
        ?>

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
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>
        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
        <script>
            let direction = false;
            $(document).ready(function () {

                if ($("html").attr("dir") == 'rtl') {
                    direction = true;
                }

                $('#categories tfoot th span').each(function () {
                    const title = $(this).text();
                    $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');
                });

                $.fn.dataTable.moment = function (format, locale) {
                    const types = $.fn.dataTable.ext.type;

                    // Add type detection
                    types.detect.unshift(function (d) {
                        return moment(d, format, locale, true).isValid() ? 'moment-' + format : null;
                    });

                    // Add sorting method - use an integer for the sorting
                    types.order['moment-' + format + '-pre'] = function (d) {
                        return moment(d, format, locale, true).unix();
                    };
                };

                $.fn.dataTable.moment('d/m/Y H:i');

                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;

                const categoriesDataTable = $('#categories').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    "scrollY": "450px",
                    "scrollCollapse": true,
                    "paging": true,
                    "info": false,
                    pageLength: 100,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: '<?php echo lang('sms_message_report') ?>',
                            className: 'btn btn-dark',
                            exportOptions: {}
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: '<?php echo lang('sms_message_report') ?>',
                            className: 'btn btn-dark',
                            exportOptions: {}
                        },
                        <?php endif; ?>
                    ],
                });


                const table = $('#categories').DataTable();

                table.columns().every(function () {
                    const that = this;

                    $('input', this.footer()).on('keyup change', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });

                $('#categories tbody').on('click', 'tr', function () {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    } else {
                        categoriesDataTable.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                });
            });
        </script>

        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <script type="text/javascript" charset="utf-8">
            function myFunction(value) {
                window.location.href = 'ReportWhatsApp.php?Dates=' + value;
            }

            function dateRange(start, end) {
                const StartDate = start.format("YYYY/MM/DD");
                const EndDate = end.format("YYYY/MM/DD");
                console.log(StartDate + " " + EndDate);

                window.location.href = 'ReportWhatsApp.php?start=' + StartDate + '&end=' + EndDate;
            }
        </script>

        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
        </h3>
        </div>
        </div>
        <?php CreateLogMovement('fas fa-chart-pie', lang('report_whatsapp_title ') . $monthNames[$cMonth - 1] . ' ' . $cYear, '0'); ?>
        <div class="row px-0 mx-0">
            <div class="col-12 mx-0 px-0">
                <div class="row">
                    <?php include("ReportsInc/SideMenu.php"); ?>
                    <div class="col-md-10 col-sm-12">
                        <div class="tab-content">
                            <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                                <div class="card spacebottom">
                                    <div class="card-header text-start">
                                        <strong> <?php echo lang('report_whatsapp_title_dates') ?>
                                            :
                                            <span style="color:#0074A4;"><?php echo $EndDate . ' - ' . $StartDate ?></span></strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 d-flex justify-content-start flex-wrap spacebottom mx-0 ">
                                        <span class="mie-6 mb-6"> <a
                                                    href="<?php echo $_SERVER["PHP_SELF"] . "?month=" . sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"
                                                    class="btn btn-light"><?php echo lang('to_prev_month') ?></a></span>
                                                <span class="mie-6 mb-6"> <a
                                                            href="<?php echo $_SERVER["PHP_SELF"] . "?month=" . sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"
                                                            class="btn btn-light"><?php echo lang('to_next_month') ?></a></span>
                                                <span class="mie-6 mb-6"> <a
                                                            href="<?php echo $_SERVER["PHP_SELF"] . "?month=" . sprintf('%02d', date('m')) . "&year=" . date('Y'); ?>"
                                                            class="btn btn-dark"><?php echo lang('this_month') ?></a></span>
                                            </div>
                                            <div class="col-md-6 col-sm-12 d-flex justify-content-end ">
                                                <span><input type="text" name="daterange"
                                                             value="<?php echo $StartDate . '-' . $EndDate ?>"/></span>
                                                </span>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <table class="table table-hover dt-responsive text-start display wrap"
                                                   id="categories" cellspacing="0" width="100%">
                                                <thead>
                                                <tr class="bg-dark text-white">
                                                    <th style="text-align:start;">#</th>
                                                    <th style="text-align:start;"><?php echo lang('to_number') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('client') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('subject') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('date') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('hour') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('cost') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $ReportListWA = WhatsAppNotifications::getMessages4Report($CompanyNum, $StartDate, $EndDate);
                                                $ReportListWAPrice = WhatsAppNotifications::getMessages4ReportPriceSum($CompanyNum, $StartDate, $EndDate) ?? '0.00';

                                                /** @var WhatsAppNotifications $MessageWA */
                                                foreach ($ReportListWA as $MessageWA) {
                                                    /** @var Client $client */
                                                    $client = Client::find($MessageWA->ClientId);
                                                    ?>
                                                    <tr>
                                                        <td><?= $MessageWA->id ?></td>
                                                        <td class="text-right" dir="ltr"><?= "+" . $MessageWA->clientPhone ?></td>
                                                        <td><?= $client->CompanyName ?></td>
                                                        <td><?= Notificationcontent::getByTypeAndCompanyNum($CompanyNum, $MessageWA->content_type)->Subject ?></td>
                                                        <td><?= $MessageWA->Date ?></td>
                                                        <td><?= $MessageWA->Time ?></td>
                                                        <td><?= $MessageWA->status == 1 ? $MessageWA->price_unlimited : '0.00' ?>&nbsp;₪</td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>

                                                <?php if (@$ReportListWAPrice != '') { ?>
                                                    <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><strong><?php echo $ReportListWAPrice; ?>&nbsp;₪</strong>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                <?php } ?>
                                            </table>

                                            <script>
                                                $(function () {
                                                    $('input[name="daterange"]').daterangepicker({
                                                        isRTL: direction,
                                                        langauge: 'he',
                                                        opens: 'left',
                                                        startDate: moment('<?php echo $StartDate ?>').format('YYYY/MM/DD'),
                                                        endDate: moment('<?php echo $EndDate ?>').format('YYYY/MM/DD'),
                                                        locale: {
                                                            format: 'YYYY/MM/DD',
                                                            "applyLabel": "<?php echo lang('approval') ?>",
                                                            "cancelLabel": "<?php echo lang('cancel') ?>",
                                                        }
                                                    }, function (start, end, label) {
                                                        dateRange(start, end);
                                                    })
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    } else {
        ErrorPage(lang('permission_blocked'), lang('no_page_persmission'));
    }
endif;

require_once '../app/views/footernew.php';
