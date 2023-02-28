<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 echo View::make('headernew')->render();

 $report = new StdClass();
 $report->name = 'דוח אי נוכחות';


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

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js">
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
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>



                                <div class="row mb-2" dir="rtl" style="padding-right: 15px; padding-left:15px">
                                    <div class="col-md-6">
                                        <label>הגדר טווח תאריכים לבדיקת אי נוכחות בסטודיו</label>
                                        <input name="lastClass" type="text" class="form-control" placeholder="חפש לפי שיעור">
                                    </div>
                                </div>

                                <div class="row mb-2" dir="rtl" style="padding-right: 15px; padding-left:15px">
                                    <div class="col-md-6">
                                    <input type="checkbox" onclick="this.checked?jQuery('[name=\'dateInStudioOnce\']').show().val(''):jQuery('[name=\'dateInStudioOnce\']').hide().val('')"> <label>הגדר טווח תאריכים שהלקוח נכח בהם לפחות פעם אחת</label>
                                        <input name="dateInStudioOnce" type="text" class="form-control" placeholder="חפש" style="display: none;">
                                    </div>
                                </div>


                                <div class="row" dir="ltr" style="padding-left:15px; padding-right:15px;">

                                    <table class="table table-hover dt-responsive text-right display wrap" id="dataTable" dir="rtl" cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:right;"></th>
                                                <th style="text-align:right;">שם לקוח</th>
                                                <th style="text-align:right;">טלפון</th>
                                                <th style="text-align:right;">מנוי</th>
                                                <th style="text-align:right;">שיעור אחרון</th>
                                                <th style="text-align:right;">הרשמה עתידית</th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th></th>
                                                <th style="text-align:right;"><input name="name" type="text" class="form-control"
                                                        placeholder="חפש"></th>
                                                <th style="text-align:right;"><input name="phone" type="text" class="form-control"
                                                        placeholder="חפש"></th>
                                                <th style="text-align:right;">
                                                    <select multiple name="member" size="1" class="form-control" style="width: 100%" placeholder="חפש"></select>
                                                </th>
                                                <th style="text-align:right;"></th>
                                                <th style="text-align:right;">
                                                    <select name="futureClasses" class="form-control">
                                                        <option value="">הכל</option>
                                                        <option value="true">יש</option>
                                                        <option value="false" selected>אין</option>
                                                    </select>
                                                </th>
                                            </tr>

                                        </thead>

                                        <tbody></tbody>
                                        <tfoot>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tfoot>

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

            return false;

            $.ajax({
                url: BeePOS.options.api + 'company/products',
                headers: { 'x-cookie': document.cookie },
                method: 'GET',
            }).done(function (data, textStatus, jqXHR) {
                $(document).ready(function () {
                    var selects = $("select[name='member']");
                    selects.each(function (i, select) {
                        select = jQuery(select);
                        select.append($('<option>', { value: 'NULL', text: 'ללא מנוי' }));
                        for (let index = 0; index < data.items.length; index++) {
                            select.append($('<option>', { value: data.items[index].id, text: data.items[index].name }));
                        }
                        select.select2({ tags: true, placeholder: select.attr('placeholder') });
                    })
                })
            });


            $(document).ready(function() {


                var table = $('#dataTable');
                var filter = $('thead tr.filterHeader', table)
                var fields = {
                    name: $("input[name='name']", filter),
                    date: $("input[name='dateInStudioOnce']"),
                    phone: $("input[name='phone']", filter),
                    email: $("input[name='member']", filter),
                    member: $("select[name='member']", filter),
                    lastClass: $("input[name='lastClass']"),
                    futureClasses: $('select[name="futureClasses"]', filter)
                }

                function debounce(func, wait, immediate) {
                    var timeout;
                    return function () {
                        var context = this, args = arguments;
                        var later = function () {
                            timeout = null;
                            if (!immediate) func.apply(context, args);
                        };
                        var callNow = immediate && !timeout;
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                        if (callNow) func.apply(context, args);
                    };
                };
                // the magic for the filter
                for (var field in fields) {
                    var el = fields[field][0];
                    if(!el) continue;
                    switch (el.tagName.toLowerCase()) {
                    case "select":
                            jQuery(el).on('change', function () {
                                table.DataTable().ajax.reload();
                            });
                            break;
                     case "input":
                            jQuery(el).on('keyup', debounce(function () {
                                table.DataTable().ajax.reload();
                            }, 500));
                            break;
                    }
                }


                // convert date to daterange
                try {
                    fields.date.daterangepicker({
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
                        table.DataTable().ajax.reload();
                    }).val('');

                    fields.lastClass.daterangepicker({
                        startDate: moment().startOf('week'),
                        endDate: moment(), //.endOf('month'),
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }).on('apply.daterangepicker', function() {
                        table.DataTable().ajax.reload();
                    });

                } catch (e) {
                    console.log(e);
                    throw e;
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
                        url: BeePOS.options.api + 'client/nonattendance',
                        headers: {
                            'x-cookie': document.cookie
                        },
                        data: function(d) {
                            var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                            var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                            var limit = JSON.parse(JSON.stringify(d.length));
                            var page = JSON.parse(JSON.stringify((d.start + d.length) / d.length));
                            for (key in d) delete d[key];
                            d.sort = sortKey;
                            d.dir = sortDir;
                            d.page = page;
                            d.limit = limit;
                            d.filter = {
                                date_start: moment(fields.lastClass.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                date_end: moment(fields.lastClass.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),

                                // nonattendance_start: moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                // nonattendance_end: moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),


                            }

                            if(fields.date.val() != ''){
                                d.filter.nonattendance_start = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                                d.filter.nonattendance_end = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                            }

                            if(fields.name.val()) d.filter.name = fields.name.val();
                            if(fields.email.val()) d.filter.email = fields.email.val();
                            if(fields.phone.val()) d.filter.phone = fields.phone.val();
                            if(fields.member.val()) d.filter.member = fields.member.val();
                            if(fields.futureClasses.val()) d.filter.futureClasses = fields.futureClasses.val();
                        }
                    },
                    serverSide: true,
                    columns: [{
                            "name": "select",
                            bSortable: false
                        },
                        {
                            "name": "fullName"
                        },
                        {
                            "name": "phone"
                        },
                        {
                            "name": "member"
                        },
                        {
                            "name": "lastClass"
                        },
                        {
                            "name": "futureClasses"
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

                var dTable = table.dataTable(settings);
                
                dTable.on('xhr.dt', function(e, settings, json, xhr) {


                    json.data = json.items.map(function(x) {
                        var data = [];
                        data.push(x.clientId || 0);
                        data.push('<a href="../ClientProfile.php?u=' + (x.clientId || 0) +
                            '">' + (x.clientName || '') + '</a>');

                        data.push('<a href="tel:' + x.clientPhone + '">' + (x.clientPhone || '').replace('+972', '0') + '</a>');

                        data.push(x.clientMembership || '');
                        data.push(x.clientLastClass ? moment(x.clientLastClass).format('DD/MM/YYYY'): '');
                        data.push(x.clientFutureClassCount == '0' ?
                            '<div class="text-center"><i class="fa fa-times-circle text-danger"></i> <span hidden>אין</span></div>' :
                            '<div class="text-center"><i class="fa fa-user-check text-success" title="' +
                            x.clientFutureClassCount + '"></i> <span hidden>יש</span></div>'
                        );

                        return data
                    });
                    // json.draw = 1;
                    json.recordsTotal = parseInt(json.meta.rows); // Total records, before filtering
                    json.recordsFiltered = parseInt(json.meta.rows); // Total records, after filtering
                });

            })
        })(jQuery, BeePOS)
    </script>


    <!-- popupSendByClientId -->
    <?php include('./popupSendByClientId.php'); ?>



    <?php 
        require_once '../../app/views/footernew.php';
    ?>