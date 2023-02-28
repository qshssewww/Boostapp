<?php
require_once '../app/init.php';
$pageTitle = 'ריכוז פריטים';
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('128')): ?>
        <?php
        CreateLogMovement('נכנס לריכוז פריטים', '0');


        if (!isset($_REQUEST["dateFrom"]))
            $_REQUEST["dateFrom"] = date("Y-m-d");
        if (!isset($_REQUEST["dateTo"]))
            $_REQUEST["dateTo"] = date("Y-m-t");

        $dateFrom = $_REQUEST["dateFrom"];
        $dateTo = $_REQUEST["dateTo"];
        $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
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
                        window.location.href = "SalesReports.php?dateFrom=" + moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD') + "&dateTo=" + moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
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
                    dom: "lBfrtip",
                    //info: true,

                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: 'ריכוז פריטים',
                            className: 'btn btn-dark'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: 'ריכוז פריטים',
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
                        url: 'SalesReportsPost.php',
                        method: 'POST',
                        data: function (d) {
                            d.dateFrom = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                            d.dateTo = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
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


                            var totalsub = 0;
                            $('.TotalBalanceMoney').each(function () {
                                var valuesub = parseInt(this.value);
                                if (!isNaN(valuesub)) {
                                    totalsub += valuesub;
                                }
                            });
                            $('#TotalDownAmountSub').text(parseFloat(totalsub).toFixed(2));


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

                    var totalsub = 0;
                    $('.TotalBalanceMoney').each(function () {
                        var valuesub = parseInt(this.value);
                        if (!isNaN(valuesub)) {
                            totalsub += valuesub;
                        }
                    });
                    $('#TotalDownAmountSub').text(parseFloat(totalsub).toFixed(2));


                });

                $('#table-filterMembership_type').on('change', function () {
                    table.column('3').search(this.value).draw();

                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));

                    var totalsub = 0;
                    $('.TotalBalanceMoney').each(function () {
                        var valuesub = parseInt(this.value);
                        if (!isNaN(valuesub)) {
                            totalsub += valuesub;
                        }
                    });
                    $('#TotalDownAmountSub').text(parseFloat(totalsub).toFixed(2));

                });

                $('#table-filterBrands').on('change', function () {
                    table.column('5').search(this.value).draw();


                    var total = 0;
                    $('.TotalAmounts').each(function () {
                        var value = parseInt(this.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });
                    $('#TotalDownAmount').text(parseFloat(total).toFixed(2));

                    var totalsub = 0;
                    $('.TotalBalanceMoney').each(function () {
                        var valuesub = parseInt(this.value);
                        if (!isNaN(valuesub)) {
                            totalsub += valuesub;
                        }
                    });
                    $('#TotalDownAmountSub').text(parseFloat(totalsub).toFixed(2));

                });


            });


        </script>

        <?php

        $SumTotal = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '2')->whereBetween('Dates', array($dateFrom, $dateTo))->orderBy('Dates', 'ASC')->sum('ItemPrice');

        $SumSubTotal = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '2')->whereBetween('Dates', array($dateFrom, $dateTo))->orderBy('Dates', 'ASC')->where('isDisplayed',  1)->where('isDisplayed',  1)->sum('BalanceMoney');

        $CountTotal = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '2')->whereBetween('Dates', array($dateFrom, $dateTo))->orderBy('Dates', 'ASC')->count();

        ?>
        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <div class="row">

            <?php //echo $DateTitleHeader; ?>


            <div class="col-md-2 col-sm-12 order-md-2 pb-10 m-auto">
                <a href="Sales.php" class="btn btn-primary btn-block" dir="rtl"><i class="fas fa-chart-line"></i> דוח
                    מכירות</a>
            </div>

        </div>

        <div class="row">

            <?php include("ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12 order-md-1">


                <div class="card spacebottom">
                    <div class="card-header text-right" dir="rtl">
                        <i class="fas fa-chart-line"></i> <b>ריכוז פריטים כרטיס לקוח</b>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-9 col-sm-12">

                            </div>
                            <div class="col-md-3 col-sm-12">

                            </div>
                        </div>
<!--                        <hr>-->


                        <table class="table table-hover dt-responsive text-right display wrap" id="categories" dir="rtl"
                               cellspacing="0" width="100%">
                            <thead>
                            <tr class="bg-dark text-white">
                                <th class="text-right">תאריך</th>
                                <th class="text-right">פריט</th>
                                <th class="text-right">מחלקה</th>
                                <th class="text-right">סוג מנוי</th>
                                <th class="text-right">מחיר</th>
                                <th class="text-right">י. לתשלום</th>
                                <th class="text-right">סניף</th>
                                <th class="text-right">שם לקוח</th>
                                <th class="text-right lastborder">טלפון</th>
                            </tr>


                            </thead>
                            <tbody>


                            </tbody>

                            <tfoot>
                            <tr class="bg-white text-black filterHeader">
                                <th><input id="table-filterDate" name="date" type="text" class="form-control"
                                           placeholder="חפש"></th>
                                <th><span>פריט</span></th>
                                <th><select id="table-filterMembership" class="form-control">
                                        <option value="">הכל</option>
                                        <?php
                                        $Memberships = DB::table('membership')->where('Status', '=', '0')->get();
                                        foreach ($Memberships as $Membership) {
                                            ?>
                                            <option><?php echo $Membership->MemberShip; ?></option>
                                        <?php } ?>
                                    </select></th>
                                <th>
                                    <select id="table-filterMembership_type" class="form-control">
                                        <option value="">הכל</option>
                                        <?php
                                        $MembershipTypes = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();
                                        foreach ($MembershipTypes as $MembershipType) {
                                            ?>
                                            <option><?php echo $MembershipType->Type; ?></option>
                                        <?php } ?>
                                        <option>ללא מחלקה</option>
                                    </select>
                                </th>
                                <th><span>מחיר</span></th>
                                <th><span>י.לתשלום</span></th>
                                <th><select id="table-filterBrands" class="form-control">
                                        <option value="">הכל</option>
                                        <?php
                                        $Brands = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->get();
                                        if (!empty($Brands)) {
                                            foreach ($Brands as $Brand) {
                                                ?>
                                                <option><?php echo $Brand->BrandName; ?></option>
                                            <?php }
                                        } else { ?>
                                            <option>סניף ראשי</option>
                                        <?php } ?>
                                    </select></th>
                                <th><span>לקוח</span></th>
                                <th><span>טלפון</span></th>
                            </tr>

                            <?php
                            if (@$SumTotal >= '0.00') {
                                $StatusClass = 'text-primary';
                            } else {
                                $StatusClass = 'text-danger';
                            }

                            if (@$SumSubTotal > '0.00') {
                                $StatusSubClass = 'text-danger';
                            } else {
                                $StatusSubClass = 'text-primary';
                            }

                            ?>

                            <tr class="bg-white text-black active">
                                <td>שורות</td>
                                <td><?php echo $CountTotal; ?></td>
                                <td></td>
                                <td>סה"כ</td>
                                <td class="<?php echo $StatusClass; ?>" dir="ltr"><strong
                                            id="TotalDownAmount"><?php echo @$SumTotal; ?></strong></td>
                                <td class="<?php echo $StatusSubClass; ?>" dir="ltr"><strong
                                            id="TotalDownAmountSub"><?php echo @$SumSubTotal; ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>


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