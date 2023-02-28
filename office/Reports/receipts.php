<?php
require_once '../../app/init.php';
// secure page
if (!Auth::check())
    redirect_to('../../index.php');

$report = new StdClass();
$report->name = lang('reports_receipt');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';
?>

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

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script src="../js/datatable/dataTables.checkboxes.min.js"></script>

<link href="../assets/css/fixstyle.css" rel="stylesheet">
<style>
    .bg-gray {background-color: #e9ecef;}
    .dataTables_scrollHead table{margin-bottom: 0px;}
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

<div class="row px-0 mx-0" >
    <div class="col-12" >


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
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
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div class="row" >
                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:start;"><?php echo lang('date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('branch') ?></th>
                                                <th style="text-align:start;"><?php echo lang('cash') ?></th>
                                                <th style="text-align:start;"><?php echo lang('check') ?></th>
                                                <th style="text-align:start;"><?php echo lang('credit_card') ?></th>
                                                <th style="text-align:start;"><?php echo lang('bank_transfer') ?></th>
                                                <th style="text-align:start;"><?php echo lang('total') ?></th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">

                                                <th style="text-align:start;"><input name="date" type="text" class="form-control" placeholder="<?php echo lang('search_single') ?>"></th>
                                                <th style="text-align:start;">
                                                    <select name="branch" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
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
        .datepicker-dropdown {max-width: 300px;}
        .datepicker {float: right}
        .datepicker.dropdown-menu {right:auto}
    </style>
    <script>
        (function($, BeePOS){
        $(document).ready(function(){

        var fields = {
        date: $("input[name='date']", filter),
                branch: $("select[name='branch']", filter)
        }
        // get branches api and inject into select
        $.get('../rest/?type=report&method=branches', function(data){
        try {
        data = JSON.parse(data);
        var branches = data.items || [];
        for (let i = 0; i < branches.length; i++) {
        fields.branch.append(jQuery('<option>', {value: branches[i].branch, text: branches[i].branch}))
        }

        } catch (error) {

        }
        })


                var table = $('#dataTable');
        var filter = $('thead tr.filterHeader', table)




                // the magic for the filter
                for (var field in fields){
        fields[field].on('keyup change', function(e){
        if (e.target.type.indexOf('select') != - 1 || e.keyCode == 13) return table.DataTable().ajax.reload();
        })
        }


        try{
        if (window.location.search != '') {
        var startDate = getUrlParams('startDate').split('-');
        var endDate = getUrlParams('endDate').split('-');
        startDate = new Date(startDate[0], (parseInt(startDate[1]) - 1), startDate[2])
                endDate = new Date(endDate[0], (parseInt(endDate[1]) - 1), endDate[2])
        }
        else {
        var startDate = moment()
                var endDate = moment()
        }

        direction = false;
        if ($("html").attr("dir") == "rtl"){
        direction = true;
        }

        fields.date.daterangepicker({
        startDate: startDate,
                endDate: endDate, //.endOf('month'),
                isRTL: direction,
                langauge: 'he',
                locale: {
                format: 'DD/M/YY',
                        "applyLabel": "<?php echo lang('approval') ?>",
                        "cancelLabel": "<?php echo lang('cancel') ?>",
                }
        }).on('apply.daterangepicker', function(){
        table.DataTable().ajax.reload();
        });
        } catch (e){
        console.log(e);
        }

        var modal = $('#SendClientPush');
        var modalsClientIds = $('input[name="clientsIds"]', modal);
        BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        var settings = {
        orderCellsTop: true, // sorting only on first raw in thead
                language: BeePOS.options.datatables,
                responsive: true,
                processing: true,
                paging: false,
                // scrollY: "450px",
                scrollCollapse: true,
                dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                buttons: [
                        // {text: 'שלח הודעה <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                        //     // rows_selected = table.column(0).checkboxes.selected();
                        //     var clientsIds = dt.column(0).checkboxes.selected().toArray();
                        //     if(!clientsIds.length) return alert('אנא בחר לקוחות');

                        //     modalsClientIds.val(clientsIds.join(","));
                        //     modal.modal('show');

                        // }},
<?php if (Auth::userCan('98')): ?>
                    {extend: 'excelHtml5', text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_receipt') ?>', className: 'btn btn-dark', exportOptions: {}},
                    {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_receipt') ?>', className: 'btn btn-dark', exportOptions: {}}
<?php endif ?>

                ],
                ajax:{
                url: '../rest/',
                        method: 'POST',
                        data: function(d){
                        d.type = 'report';
                        d.method = 'receipts';
                        d.filter = {
                        branch: fields.branch.val(),
                                dateFrom: moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                dateTo: moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD')
                        }
                        }
                },
                serverSide: true,
                columns: [
                {"name": "date"},
                {"name": "branch"},
                {"name": "cash"},
                {"name": "checks"},
                {"name": "creditCards"},
                {"name": "bank"},
                {"name": "total"}
                ],
                bFilter: false, // hide search field
                bSort: true,
                // pageLength: 100,
                // lengthChange: true,
                // lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
                select: {
                style: 'multi'
                },
                order: [[1, 'asc']],
                rowCallback: function(row, data, index){
                if (data[0] == '<?php echo lang('total') ?>'){
                $(row).addClass('bg-dark text-white')
                }
                }

        }
        table.dataTable(settings).on('xhr.dt', function (e, settings, json, xhr) {

        // no reson to show empty total
        // will output no data found
        if (json.items.length == 1) {
        json.recordsTotal = 0;
        json.recordsFiltered = 0; // show filter x/x
        return json.data = [];
        }
        var template = '<span style="direction: ltr" >%s</span>'
                json.data = json.items.map(function(x){
                var data = [];
                data.push(x.date || 0);
                data.push(x.branch || '');
                data.push(template.replace("%s", (x.cash || '')));
                data.push(template.replace("%s", (x.checks || '')));
                data.push(template.replace("%s", (x.creditCards || '')));
                data.push(template.replace("%s", (x.bank || '')));
                data.push(template.replace("%s", (x.total || '')));
                return data
                });
        // json.draw = 1;
        json.recordsTotal = parseInt(json.recordsTotal) - 1;
        json.recordsFiltered = parseInt(json.recordsFiltered) - 1;
        });
        })
                function getUrlParams (name){
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                if (results == null){
                return null;
                }
                else{
                return results[1] || 0;
                }
                }
        })(jQuery, BeePOS)

    </script>


    <!-- popupSendByClientId -->
<?php include('./popupSendByClientId.php'); ?>



    <?php
    require_once '../../app/views/footernew.php';
    ?>