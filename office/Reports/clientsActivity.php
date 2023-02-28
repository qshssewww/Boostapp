<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 echo View::make('headernew')->render();

 $report = new StdClass();
 $report->name = 'דוח מנויים';


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

                                <div class="row" dir="ltr" style="padding-left:15px; padding-right:15px;">
                                    <table class="table table-hover dt-responsive text-right display wrap" id="dataTable" dir="rtl" cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:right;"></th>
                                                <th style="text-align:right;">תאריך רכישה</th>
                                                <th style="text-align:right;">סוג מנוי</th>
                                                <th style="text-align:right;">מחלקה</th>
                                                <th style="text-align:right;">שם לקוח</th>
                                                <th style="text-align:right;">פריט</th>
                                                <!-- <th style="text-align:right;">כרטיסייה</th> -->
                                                <th style="text-align:right;">יתרה</th>
                                                <th style="text-align:right;">תוקף</th>
                                                <th style="text-align:right;">נציג</th>
                                                <th style="text-align:right;">סניף</th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th></th>
                                                <th style="text-align:right;"><input name="date" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;">
                                                    <select name="memberType" class="form-control">
                                                        <option value="">הכול</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;">
                                                    <select name="department" class="form-control">
                                                    <option value="">הכול</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;"><input name="fullName" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;"><input name="product" type="text" class="form-control" placeholder="חפש"></th>
                                                <!-- <th style="text-align:right;"><input name="ticket" type="text" class="form-control" placeholder="חפש"></th> -->
                                                <th style="text-align:right;"><input name="ticketLeft" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;"><input name="ticketExp" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;">
                                                    <select name="agentName" class="form-control">
                                                        <option value="">הכול</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;">
                                                    <select name="brunchName" class="form-control">
                                                        <option value="">הכול</option>
                                                    </select>
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
                var fields = {
                    date: $("input[name='date']", filter),
                    memberType: $("select[name='memberType']", filter),
                    department: $("select[name='department']", filter),
                    fullName: $("input[name='fullName']", filter),    
                    ticketLeft: $("input[name='ticketLeft']", filter),  
                    ticket: $("input[name='ticket']", filter),
                    ticketExp: $("input[name='ticketExp']", filter),
                    agentName: $("select[name='agentName']", filter),
                    brunchName: $("select[name='brunchName']", filter),
                    product: $("input[name='product']", filter)
                }

                // the magic for the filter
                for(var field in fields){
                    fields[field].on('keyup change', function(e){
                        if(e.target.type.indexOf('select') != -1 || e.keyCode == 13) return table.DataTable().ajax.reload();                                   
                    })
                }

                
                 // convert date to daterange
                try{


                    var datepickerOpt = {
                        date: {
                        startDate: moment().startOf('month'),
                        endDate: moment(), //.endOf('month'),
                        autoApply: false,
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }, ticketExp:{
                        autoUpdateInput: false,
                        autoApply: false,
                        isRTL: true,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }
                    }

                    fields.date.daterangepicker(datepickerOpt.date, function(fromDate, toDate){
                        if(!fromDate._isValid || !toDate._isValid) {
                            fields.date.val('');
                            return false;
                        }
                        fields.date.val(fromDate.format('DD/M/YY') +' - '+toDate.format('DD/M/YY'));

                        setTimeout(function(){
                            table.DataTable().ajax.reload();
                            var drp = fields.ticketExp.data('daterangepicker');
                            drp.setStartDate(moment());
                            drp.setEndDate(null);
                            drp.updateCalendars();
                            fields.ticketExp.val('');
                        }, 0)


                    });


                    fields.ticketExp.daterangepicker(datepickerOpt.ticketExp, function(fromDate, toDate){
                        if(!fromDate._isValid || !toDate._isValid) {
                            fields.ticketExp.val('');
                            return false;
                        }
                        fields.ticketExp.val(fromDate.format('DD/M/YY') +' - '+toDate.format('DD/M/YY')).trigger('change');

                        setTimeout(function(){
                            table.DataTable().ajax.reload();
                            var drp = fields.date.data('daterangepicker');
                            drp.setStartDate(moment());
                            drp.setEndDate(null);
                            drp.updateCalendars();
                            fields.date.val('');

                        }, 0);

                    });


                }catch(e){
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
                    paging: true,
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
		                 <?php endif ?>
                         
                    ],
                    ajax:{
                        url: '../rest/',
                        method: 'POST',
                        data: function(d){
                            d.type = 'report';
                            d.method = 'clients';
                            d.filter = {

                                memberType: fields.memberType.val(),
                                department: fields.department.val(),
                                fullName:  fields.fullName.val(),  
                                ticketLeft:  fields.ticketLeft.val(),
                                ticket:  fields.ticket.val(),
                                agentName:  fields.agentName.val(),
                                brunchName:  fields.brunchName.val(),
                                product:  fields.product.val(),

                                
                            }

                            // console.log(`date field: ${fields.date.val()}`)
                            if(fields.date.val() != "" && fields.date.val().indexOf("Invalid") == -1){
                                d.filter.dateFrom =  moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                                d.filter.dateTo =  moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                                d.filter.dateFilter =  'date';
                            }

                            // console.log(`date ticketExp: ${fields.ticketExp.val()}`)
                            if(fields.ticketExp.val() != "" && fields.ticketExp.val().indexOf("Invalid") == -1){
                                d.filter.dateFrom =  moment(fields.ticketExp.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                                d.filter.dateTo =  moment(fields.ticketExp.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                                d.filter.dateFilter =  'ticketExp';            
                            }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "clientId", bSortable: false},
                        {"name": "date"},
                        {"name": "memberType"},
                        {"name": "department"},
                        {"name": "fullName"},
                        {"name": "product"},
                        // {"name": "ticket"}, // merge with ticketLeft
                        {"name": "ticketLeft"},
                        {"name": "ticketExp"},
                        {"name": "agentName"},
                        {"name": "brunchName"}
                    ],
                    bFilter: false, // hide search field
                    bSort: true,
                    pageLength: 100,
                    lengthChange: true,
                    lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
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
                    order: [[1, 'asc']]

                }
                table.dataTable(settings).on('xhr.dt', function ( e, s, json, xhr ) {

                    // auto populate select field logic from api
                    for(field in fields){
                        var type = fields[field].prop('type');
                        if(!type || type.indexOf('select') === -1) continue;
                        var name = fields[field].prop('name') + 's';
                        if(!json[name] || !json[name].length) continue;
                        var oldValue = fields[field].val();

                        fields[field].find('option').not(':first').remove();
                        for(var i=0; i < json[name].length; i++){
                            if(!json[name][i].value) continue;
                            fields[field].append(jQuery('<option>', {value: json[name][i].value, text: json[name][i].value}))    
                        }
                        fields[field].val(oldValue);
                    }


                    json.data = json.items.map(function(x){
                        var data = [];
                        for (let index = 0; index < settings.columns.length; index++) {
                            if(settings.columns[index].name === 'ticketLeft'){
                                data.push((x[settings.columns[index].name] || 0) + '/' +(x['ticket'] || 0));
                                continue;
                            }
                            if(settings.columns[index].name === 'fullName'){
                                data.push('<a href="../ClientProfile.php?u='+(x.clientId || 0)+'">'+(x[settings.columns[index].name] || '') +'</a>');
                                continue;
                            }


                            data.push(x[settings.columns[index].name] || '')     
                        };
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
        <?php include('./popupSendByClientId.php'); ?>



    <?php 
        require_once '../../app/views/footernew.php';
    ?>