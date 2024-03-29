<?php
require_once '../app/init.php';
$pageTitle = lang('sales_report');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('128')): ?>
        <?php


        if (!isset($_REQUEST["dateFrom"]))
            $_REQUEST["dateFrom"] = date("Y-m-d");
        if (!isset($_REQUEST["dateTo"]))
            $_REQUEST["dateTo"] = date("Y-m-t");

        $dateFrom = $_REQUEST["dateFrom"];
        $dateTo = $_REQUEST["dateTo"];
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

<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<!--        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
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

                $('#categories tfoot th span').each(function () {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');
                });
                var table = $('#categories');
                var fields = {
                    date: $("input[name='date']"),
                }
                try {
                    fields.date.daterangepicker({
                        <?php if (@$_REQUEST["dateFrom"] != '' && @$_REQUEST["dateTo"] != '') { ?>
                        startDate: moment('<?php echo @$_REQUEST["dateFrom"] ?>').format('DD/MM/YY'),
                        endDate: moment('<?php echo @$_REQUEST["dateTo"] ?>').format('DD/MM/YY'),
                        <?php } else { ?>
                        startDate: moment().startOf('month'),
                        endDate: moment().endOf('month'), //.endOf('month'),
                        <?php } ?>
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "<?php echo lang('approval') ?>",
                            "cancelLabel": "<?php echo lang('cancel') ?>",
                        }
                    }).on('apply.daterangepicker', function () {
                        //                  table.ajax.reload();
                        window.location.href = "Sales.php?dateFrom=" + moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD') + "&dateTo=" + moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                    });
                } catch (e) {
                    console.log(e);
                }


                $.fn.dataTable.moment = function (format, locale) {
                    var types = $.fn.dataTable.ext.type;
                    // Add type detection
                    types.detect.unshift(function (d) {
                        return moment(d, format, locale, true).isValid() ?
                            'moment-' + format :
                            null;
                    });
                    // Add sorting method - use an integer for the sorting
                    types.order['moment-' + format + '-pre'] = function (d) {
                        return moment(d, format, locale, true).unix();
                    };
                };
                $.fn.dataTable.moment('d/m/Y H:i');
                var categoriesDataTable;
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var categoriesDataTable = $('#categories').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    autoWidth: true,
                    "scrollY": true,
                    "scrollCollapse": true,
                    "paging": false,
                    //fixedHeader: {headerOffset: 50},

                    //  bStateSave:true,
                    //		    serverSide: true,
                    //	        pageLength: 5000,
                    dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    //info: true,

                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: '<?php echo lang('sales_report') ?>',
                            className: 'btn btn-dark'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: '<?php echo lang('sales_report') ?>',
                            className: 'btn btn-dark'
                        },
                        {
                            extend: 'print',
                            text: 'הדפסה <i class="fas fa-print" aria-hidden="true"></i>',
                            className: 'btn btn-dark',
                            customize: function (win) {
                                // https://datatables.net/reference/button/print
                                jQuery(win.document).ready(function () {
                                    $(win.document.body)
                                        .css('direction', 'rtl')
                                });
                            }
                        },
                        // 'pdfHtml5'
                        <?php endif ?>

                    ],
                    ajax: {
                        url: 'SalesPost.php',
                        method: 'POST',
                        data: function (d) {
                            d.dateFrom = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                            d.dateTo = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                        },
                        complete: function (data) {
                            $('[name="TotalDownAmount"]').text(data['responseJSON'].totalAmountAllPeriod);
                        }
                    },
                    //        serverSide: true,
                    //		order: [[0, 'ASC']]


                });
                var table = $('#categories').DataTable();
                table.columns().every(function () {
                    var that = this;
                    $('span input', this.footer()).on('keyup change', function () {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                            var total = 0;
                            $('.TotalAmounts').each(function () {
                                var value = parseInt(this.value);
                                if (!isNaN(value)) {
                                    total += value;
                                }
                            });
                            $('#TotalDownAmount').text(parseFloat(total).toFixed(2));
                        }
                    });
                });
                $('#categories .filterHeader').insertAfter($('#categories thead tr'));
                $('#table-filterMembership').on('change', function () {
                    table.column('2').search(this.value).draw();
                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));
                });

                $('body').on('change', '#table-filterMembership_type', function () {
                    table.column('3').search(this.value).draw();
                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));
                });

                $('body').on('change', '#table-filterBrands', function () {
                    table.column('5').search(this.value).draw();
                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));
                });

                $('body').on('change', '#table-filterPaymentSource', function () {
                    table.column('10').search(this.value).draw();
                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));
                });
            });


        </script>

        <?php
        $SumTotal = DB::table('docs2item')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($dateFrom, $dateTo))->orderBy('UserDate', 'ASC')->sum('Amount');


        $CountTotal = DB::table('docs2item')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($dateFrom, $dateTo))->orderBy('UserDate', 'ASC')->count();
        ?>
        <link href="assets/css/fixstyle.css" rel="stylesheet">

        <div class="col-md-12 col-sm-12">
            <div class="row">

                <div class="col-md-2 col-sm-12 pb-10 margin-a">
                    <a href="SalesReports.php" class="btn btn-primary btn-block"><i
                                class="fas fa-chart-line"></i> <?php echo lang('items_concen') ?></a>
                </div>

            </div>

            <div class="row">

                <?php include("ReportsInc/SideMenu.php"); ?>

                <div class="col-md-10 col-sm-12 ">


                    <div class="card spacebottom">
                        <div class="card-header text-right">
                            <i class="fas fa-chart-line"></i> <b><?php echo lang('sales_report') ?></b>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-9 col-sm-12">

                                </div>
                                <div class="col-md-3 col-sm-12">

                                </div>
                            </div>
                            <hr>


                            <table class="table table-hover dt-responsive text-start display wrap" id="categories"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr class="bg-dark text-white">
                                    <th class="text-start"><?php echo lang('date') ?></th>
                                    <th class="text-start"><?php echo lang('item_single') ?></th>
                                    <th class="text-start"><?php echo lang('class') ?></th>
                                    <th class="text-start"><?php echo lang('membership_type_single') ?></th>
                                    <th class="text-start"><?php echo lang('price') ?></th>
                                    <th class="text-start"><?php echo lang('branch') ?></th>
                                    <th class="text-start"><?php echo lang('client_name') ?></th>
                                    <th class="text-start"><?php echo lang('telephone') ?></th>
                                    <th class="text-start"><?php echo lang('payment_single') ?></th>
                                    <th class="text-start"><?php echo lang('document_single') ?></th>
                                    <th class="text-start lastborder"><?php echo lang('table_payment_source') ?></th>
                                </tr>


                                </thead>
                                <tbody>


                                </tbody>

                                <tfoot>
                                <tr class="bg-white text-black filterHeader">
                                    <th><input id="table-filterDate" name="date" type="text" class="form-control"
                                               placeholder="<?php echo lang('search_single') ?>"></th>
                                    <th><span>פריט</span></th>
                                    <th><select id="table-filterMembership" class="form-control">
                                            <option value=""><?php echo lang('all') ?></option>
                                            <?php
                                            $Memberships = DB::table('membership')->where('Status', '=', '0')->get();
                                            foreach ($Memberships as $Membership) {
                                                ?>
                                                <option><?php echo $Membership->MemberShip; ?></option>
                                            <?php } ?>
                                        </select></th>
                                    <th>
                                        <select id="table-filterMembership_type" class="form-control">
                                            <option value=""><?php echo lang('all') ?></option>
                                            <?php
                                            $MembershipTypes = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();
                                            foreach ($MembershipTypes as $MembershipType) {
                                                ?>
                                                <option><?php echo $MembershipType->Type; ?></option>
                                            <?php } ?>
                                            <option><?php echo lang('without_department') ?></option>
                                        </select>
                                    </th>
                                    <th><span><?php echo lang('price') ?></span></th>
                                    <th><select id="table-filterBrands" class="form-control">
                                            <option value=""><?php echo lang('all') ?></option>
                                            <?php
                                            $Brands = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->get();
                                            if (!empty($Brands)) {
                                                foreach ($Brands as $Brand) {
                                                    ?>
                                                    <option><?php echo $Brand->BrandName; ?></option>
                                                <?php }
                                            } else { ?>
                                                <option><?php echo lang('primary_branch') ?></option>
                                            <?php } ?>
                                        </select></th>
                                    <th><span><?php echo lang('client') ?></span></th>
                                    <th><span><?php echo lang('telephone') ?></span></th>
                                    <th><span><?php echo lang('payment_single') ?></span></th>
                                    <th><span><?php echo lang('reports_card_document_number') ?></span></th>
                                    <th><select id="table-filterPaymentSource" class="form-control">
                                            <option value=""><?php echo lang('all') ?></option>
                                            <option><?php echo lang('payment_page_shopping_cart'); ?></option>
                                            <option><?php echo lang('application'); ?></option>
                                            <option><?php echo lang('customer_card_my_profile_app'); ?></option>
                                        </select></th>
                                </tr>

                                <?php
                                if (@$SumTotal >= '0.00') {
                                    $StatusClass = 'text-primary';
                                } else {
                                    $StatusClass = 'text-danger';
                                }
                                ?>


                                </tfoot>

                            </table>

                        </div>
                    </div>

                </div>
            </div>

        </div>


    <?php else: ?>
        <?php redirect_to('../index.php'); ?>
    <?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

    <?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>