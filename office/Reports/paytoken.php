<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 echo View::make('headernew')->render();

 $report = new StdClass();
 $report->name = 'הוראות קבע';


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

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
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
</style>

<div class="row pb-3">
    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <?php echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
                <?php echo  $report->name ?>
            </div>
        </h3>
    </div>
</div>

<div class="row" dir="rtl" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
    <div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


        <nav aria-label="breadcrumb" dir="rtl">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo  $report->name ?>
                </li>
            </ol>
        </nav>

        <div class="row">

            <?php include("../ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12 order-md-2">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-right">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                                <span class="float-left" id="totalSum"></span>
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div class="row" dir="ltr" style="padding-left:15px; padding-right:15px;">
                                    <table class="table table-hover dt-responsive text-right display wrap" id="dataTable" dir="rtl" cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th class="text-right" data-field="clientId"></th>
                                                <th class="text-right" data-field="paymentLastUnix">תשלום אחרון</th>
                                                <th class="text-right" data-field="paymentNextUnix">תשלום הבא</th>
                                                <th class="text-right" data-field="clientFullName">שם לקוח</th>
                                                <th class="text-right" data-field="clientPhone">נייד</th>
                                                <th class="text-right" data-field="productName">מוצר</th>
                                                <th class="text-right" data-field="paymentAmount">סכום</th>
                                                <th class="text-right" data-field="paymentlastFourDigits">אשראי</th>
                                                <th class="text-right" data-field="branchName">סניף</th>
                                                <th class="text-right" data-field="paymentType">סוג תשלום</th>
                                                <th class="text-right" data-field="paymentNum">תשלומים</th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th class="text-right" data-field="clientId"></th>
                                                <th class="text-right" data-field="paymentLastUnix">
                                                    <input type="text" name="paymentLast" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="paymentNextUnix">
                                                    <input type="text" name="paymentNext" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="clientFullName">
                                                    <input type="text" name="clientFullName" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="clientPhone">
                                                    <input type="text" name="clientPhone" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="productName">
                                                    <input type="text" name="productName" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="paymentAmount">
                                                    <input type="text" name="paymentAmount" class="form-control" placeholder="חפש">
                                                </th>
                                                <th class="text-right" data-field="paymentlastFourDigits"></th>
                                                <th class="text-right" data-field="branchName">
                                                    <select name="branchName" class="form-control">
                                                        <option value="">הכל</option>
                                                    </select>
                                                </th>
                                                <th class="text-right" data-field="paymentType">
                                                    <select name="paymentType" class="form-control">
                                                        <option value="">הכל</option>
                                                        <option value="1">יומי</option>
                                                        <option value="2">שבועי</option>
                                                        <option value="3">חודשי</option>
                                                        <option value="4">שנתי</option>
                                                    </select>
                                                </th>
                                                <th class="text-right" data-field="paymentNum">
                                                    <input type="text" name="paymentNum" class="form-control" placeholder="חפש">
                                                </th>
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
        (function($, BeePOS) {
            $(document).ready(function() {



                var table = $('#dataTable');
                var filter = $('thead tr.filterHeader', table)


                var fields = {
                    paymentLast: jQuery('[name="paymentLast"]', filter),
                    paymentNext: jQuery('[name="paymentNext"]', filter),
                    clientFullName: jQuery('[name="clientFullName"]', filter),
                    clientPhone: jQuery('[name="clientPhone"]', filter),
                    productName: jQuery('[name="productName"]', filter),
                    paymentAmount: jQuery('[name="paymentAmount"]', filter),
                    branchName: jQuery('[name="branchName"]', filter),
                    paymentType: jQuery('[name="paymentType"]', filter),
                    paymentNum: jQuery('[name="paymentNum"]', filter),
                }

                try {
                    fields.paymentLast.daterangepicker({
                        startDate: moment().startOf('month'),
                        endDate: moment(), //.endOf('month'),
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }).on('apply.daterangepicker', function() {
                        fields.paymentNext.val(''); // a hack to allow search by one date range
                        setTimeout(function() {
                            table.DataTable().ajax.reload();
                        }, 0);
                    });

                    fields.paymentNext.daterangepicker({
                        startDate: moment().startOf('month'),
                        endDate: moment(), //.endOf('month'),
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }).on('apply.daterangepicker', function() {
                        fields.paymentLast.val(''); // a hack to allow search by one date range
                        setTimeout(function() {
                            table.DataTable().ajax.reload();
                        }, 0);

                    }).val('');



                } catch (e) {
                    console.log(e);
                }

                // get branches api and inject into select
                $.get('../rest/?type=report&method=branches', function(data) {
                    try {
                        data = JSON.parse(data);
                        var branches = data.items || [];
                        fields.branchName.append('<option value="סניף ראשי">סניף ראשי</option>');
                        for (let i = 0; i < branches.length; i++) {
                            fields.branchName.append(jQuery('<option>', {
                                value: branches[i].branch,
                                text: branches[i].branch
                            }))
                        }

                    } catch (error) {

                    }
                });


                // the magic for the filter
                for (var field in fields) {
                    fields[field].on('keyup change', function(e) {
                        if (e.target.type.indexOf('select') != -1 || e.keyCode == 13) return table.DataTable()
                            .ajax.reload();
                    })
                }

                var modal = $('#SendClientPush');
                var modalsClientIds = $('input[name="clientsIds"]', modal);
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var settings = {
                    orderCellsTop: true, // sorting only on first raw in thead
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    paging: true,
                    scrollY: "450px",
                    scrollCollapse: true,
                    dom: "Blfrtip",
                    buttons: [{
                            text: 'שלח הודעה <i class="fas fa-comments"></i>',
                            className: 'btn btn-dark',
                            action: function(e, dt, node, config) {
                                // rows_selected = table.column(0).checkboxes.selected();
                                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!clientsIds.length) return alert('אנא בחר לקוחות');

                                modalsClientIds.val(clientsIds.join(","));
                                modal.modal('show');

                            }
                        },
                        <?php if (Auth::userCan('98')): ?> {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: 'דו״ח אי הרשמה',
                            className: 'btn btn-dark',
                            exportOptions: {}
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: 'דו״ח אי הרשמה',
                            className: 'btn btn-dark',
                            exportOptions: {}
                        }
                        <?php endif ?>

                    ],
                    ajax: {
                        url: '../rest/',
                        method: 'POST',
                        data: function(d) {
                            d.type = 'report';
                            d.method = 'paytoken';
                            d.filter = {
                                clientFullName: fields.clientFullName.val(),
                                clientPhone: fields.clientPhone.val(),
                                productName: fields.productName.val(),
                                paymentAmount: fields.paymentAmount.val(),
                                branchName: fields.branchName.val(),
                                paymentType: fields.paymentType.val(),
                                paymentNum: fields.paymentNum.val()
                            }

                            // daterange filter by
                            if (fields.paymentLast.val() && fields.paymentLast.val() != '') {
                                d.filter['range'] = 'LastPayment';
                                d.filter['dateFrom'] = moment(fields.paymentLast.data(
                                    'daterangepicker').startDate._d).format('YYYY-MM-DD');
                                d.filter['dateTo'] = moment(fields.paymentLast.data(
                                    'daterangepicker').endDate._d).format('YYYY-MM-DD');
                            }

                            if (fields.paymentNext.val() && fields.paymentNext.val() != '') {
                                d.filter['range'] = 'NextPayment';
                                d.filter['dateFrom'] = moment(fields.paymentNext.data(
                                    'daterangepicker').startDate._d).format('YYYY-MM-DD');
                                d.filter['dateTo'] = moment(fields.paymentNext.data(
                                    'daterangepicker').endDate._d).format('YYYY-MM-DD');
                            }
                        }
                    },
                    serverSide: true,

                    columns: [{
                            "name": "select",
                            bSortable: false
                        },
                        {
                            "name": "paymentLast"
                        },
                        {
                            "name": "paymentNext"
                        },
                        {
                            "name": "clientFullName"
                        },
                        {
                            "name": "clientPhone"
                        },
                        {
                            "name": "productName"
                        },
                        {
                            "name": "paymentAmount"
                        },
                        {
                            "name": "paymentlastFourDigits"
                        },
                        {
                            "name": "branchName"
                        },
                        {
                            "name": "paymentType"
                        },
                        {
                            "name": "paymentPaid"
                        }
                    ],
                    bFilter: false, // hide search field
                    bSort: true,
                    pageLength: 100,
                    lengthChange: true,
                    lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
                    columnDefs: [{
                        'targets': [0],
                        'checkboxes': {
                            'selectRow': true
                        },
                        bSortable: false,
                        orderable: false
                    }, ],
                    select: {
                        style: 'multi'
                    },
                    order: [
                        [1, 'asc']
                    ]

                }

                var totalSum = $('#totalSum');
                // https://stackoverflow.com/a/149099
                Number.prototype.formatMoney = function(c, d, t) {
                    var n = this,
                        c = isNaN(c = Math.abs(c)) ? 2 : c,
                        d = d == undefined ? "." : d,
                        t = t == undefined ? "," : t,
                        s = n < 0 ? "-" : "",
                        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                        j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g,
                        "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };

                table.dataTable(settings).on('xhr.dt', function(e, settings, json, xhr) {
                    totalSum.html('סה"כ: ' + json.totalSum.formatMoney(2, '.', ',') + ' ₪');
                    json.data = json.items.map(function(x) {
                        var data = [];
                        data.push(x.clientId || 0);
                        data.push(moment(parseInt(x.paymentLastUnix)).format("L"));
                        data.push(moment(parseInt(x.paymentNextUnix)).format("L"));

                        data.push('<a href="../ClientProfile.php?u=' + (x.clientId || 0) +
                            '">' + (x.clientFullName || '') + '</a>');

                        if (x.clientPhone && x.clientPhone.indexOf('0') == 0) {
                            data.push('<a href="tel:+' + x.clientPhone.replace('0', '972').replace(
                                /\D/g, '') + '">' + (x.clientPhone || '') + '</a>');
                        } else {
                            data.push(x.clientPhone || '');
                        }

                        data.push(x.productName || 0);
                        data.push(x.paymentAmount || 0);

                        data.push(x.paymentlastFourDigits || 0);
                        data.push(x.branchName || 0);
                        data.push(x.paymentType || 0);
                        data.push(x.paymentPaid + '/' + x.paymentNum || 0);

                        return data
                    });
                    // json.draw = 1;
                    json.recordsTotal = parseInt(json.recordsTotal);
                    json.recordsFiltered = parseInt(json.recordsFiltered);
                });

            })
        })(jQuery, BeePOS)
    </script>


    <!-- popupSendByClientId -->
    <?php include('./popupSendByClientId.php'); ?>



    <?php 
        require_once '../../app/views/footernew.php';
    ?>