<?php
require_once '../../app/init.php';
// secure page
if (!Auth::check()) {
    redirect_to('../../index.php');
}

if(!isset($_GET['classId']) OR (int) $_GET['classId'] == 0){
    redirect_to('../../index.php');
}

echo View::make('headernew')->render();

$report = new StdClass();
$report->name = 'שיעורים עם רשימת המתנה';

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
                <?php echo $report->name ?>
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
                    <?php echo $report->name ?>
                </li>
            </ol>
        </nav>

        <div class="row">

            <?php include "../ReportsInc/SideMenu.php";?>

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

                                <div class="row" id="classDetails" style="padding-left:15px; padding-right:15px;"></div>
                                <hr>

                                <div class="row" dir="ltr" style="padding-left:15px; padding-right:15px;">
                                    <table class="table table-hover dt-responsive text-right display wrap" id="dataTable" dir="rtl" cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th></th>
                                                <th class="text-right">שם</th>
                                                <th class="text-right">גיל</th>
                                                <th class="text-right">מגדר</th>
                                                <th class="text-right">טלפון</th>
                                                <th class="text-right">מייל</th>
                                            </tr>
                                            <!-- <tr class="bg-gray text-black filterHeader">
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                            </tr> -->

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

<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
.datepicker-dropdown {max-width: 300px;}
.datepicker {float: right}
.datepicker.dropdown-menu {right:auto}
</style>
    <script>
        (function($, BeePOS){

            $(document).ready(function(){


                var table = $('#dataTable');
                var filter = $('thead tr.filterHeader', table)
                var fields = {}

                // the magic for the filter
                for(var field in fields){
                    fields[field].on('keyup change', function(e){
                        if(e.target.type.indexOf('select') != -1 || e.keyCode == 13) return table.DataTable().ajax.reload();
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
                    paging: false,
                    scrollY: "450px",
                    scrollCollapse: true,
                    dom: "Blfrtip",
                    buttons: [ 
                        {text: 'שלח הודעה <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                            // rows_selected = table.column(0).checkboxes.selected();
                            var clientsIds = dt.column(0).checkboxes.selected().toArray();
                            if(!clientsIds.length) return alert('אנא בחר לקוחות');
      
                            modalsClientIds.val(clientsIds.join(","));
                            modal.modal('show');

                        }},
                        <?php if (Auth::userCan('98')): ?>
                            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'דו״ח אי הרשמה', className: 'btn btn-dark',exportOptions: {}},
			                {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'דו״ח אי הרשמה' , className: 'btn btn-dark',exportOptions: {}}
		                 <?php endif?>

                    ],
                    ajax:{
                        url: '../rest/',
                        method: 'POST',
                        data: function(d){
                            d.type = 'report';
                            d.method = 'overbooked';
                            d.details = <?php echo isset($_GET['classId']) ? (int) $_GET['classId'] : '0'; ?>;
                            d.filter = {}
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "clientId"},
                        {"name": "name"},
                        {"name": "age"},
                        {"name": "gender"},
                        {"name": "phone"},
                        {"name": "email"},
                    ],
                    bFilter: false, // hide search field
                    bSort: false,
                    pageLength: 100,
                    lengthChange: true,
                    lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
                    select: {
                        style: 'multi'
                    },
                    columnDefs: [
                        {
                            'targets': [0],
                            'checkboxes': {
                            'selectRow': true
                            },
                            bSortable: false,
                            orderable: false
                        },
                    ]
                    // order: [[1, 'asc']]

                }
                var classDetails = $('#classDetails');
                table.dataTable(settings).on('xhr.dt', function ( e, settings, json, xhr ) {

                    // populate class details outside table
                    if(json && json.class){
                        var html = '';
                         html += '<div class="col"><label>שם שיעור:</label> '+json.class.className+'</div>';
                         html += '<div class="col"><label>מיקום שיעור:</label> '+json.class.classRoomName+'</div>';
                         html += '<div class="col"><label>תאריך:</label> '+json.class.displayDate +' ' +json.class.classTime+'</div>';
                         html += '<div class="col"><label>שם מדריך:</label> '+json.class.guideName+'</div>';
                        classDetails.html(html);
                    }
                    console.log(json);
                    json.data = json.items.map(function(x){
                        var data = [];
                        data.push(x.clientId || '');
                        data.push('<a href="../ClientProfile.php?u='+(x.clientId || 0)+'">'+(x.fullName || '') +'</a>');
                        data.push(x.age || '');
                        data.push(x.gender || '');
                        data.push(x.phone ? '<a href="tel:+'+x.phone.replace('0', '972').replace(/\D/g,'')+'">'+x.phone+'</a>': '');
                        data.push(x.email || '');
                        return data
                    });
                    // json.draw = 1;
                    json.recordsTotal = parseInt(json.recordsTotal);
                    json.recordsFiltered = parseInt(json.recordsFiltered);
                } );

            })
        })(jQuery, BeePOS)
    </script>


        <!-- popupSendByClientId -->
        <?php include './popupSendByClientId.php';?>



    <?php
require_once '../../app/views/footernew.php';
?>