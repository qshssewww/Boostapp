<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 $report = new StdClass();
 $report->name = lang('reports_visitors');
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
            <?php //echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
                <?php //echo  $report->name ?>
            </div>
        </h3>
    </div>
</div> -->

<div class="row px-0 mx-0" >
    <div class="col-12 px-0 mx-0" >


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php //echo  $report->name ?>
                </li>
            </ol>
        </nav> -->

        <div class="row">

            <?php include("../ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active " role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header ">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div class="row px-15" >
                                    <table class="table table-hover dt-responsive  display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th></th>
                                                <th style="text-align:start;"><?php echo lang('client_name') ?></th>
                                                <th style="text-align:start;"><?php echo lang('telephone') ?></th>
                                                <th style="text-align:start;"><?php echo lang('email_table') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_name') ?></th>
                                                <th style="text-align:start;"><?php echo lang('instructor') ?></th>
                                                <th style="text-align:start;"><?php echo lang('date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('hour') ?></th>
                                                <th style="text-align:start;"><?php echo lang('location') ?></th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th></th>
                                                <th style="text-align:start;"><input name="name" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>
                                                <th style="text-align:start;"><input name="phone" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>
                                                <th style="text-align:start;"><input name="email" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>
                                                <th style="text-align:start;">
                                                    <select name="className" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;">
                                                    <select name="instructureName" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;"><input name="dateRange" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>
                                                <th style="text-align:start;">
                                                    <input name="ClassStartTime" type="time" class="form-control" placeholder="<?php echo lang('a_search') ?>">
                                                </th>
                                                <th style="text-align:start;">
                                                    <select name="classLocation" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
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

                var direction = false ;

                if( $("html").attr("dir") == 'rtl' ){
                     direction = true ;
                }

                var table = $('#dataTable');
                var filter = $('thead tr.filterHeader', table)
                var fields = {
                    dateRange: $('input[name="dateRange"]', filter),

                    name: $("input[name='name']", filter),
                    email: $("input[name='email']", filter),
                    phone: $("input[name='phone']", filter),

                    className: $("select[name='className']", filter),
                    instructureName: $("select[name='instructureName']", filter),
                    ClassStartTime: $("input[name='ClassStartTime']", filter),
                    classLocation: $("select[name='classLocation']", filter)  
                }



                   $.get('../rest/?type=report&method=classes', function(data){
                        try {
                            data = JSON.parse(data);
                            var classes = data.items || [];
                        for (let i = 0; i < classes.length; i++) {
                                fields.className.append(jQuery('<option>', {value: classes[i].className, text: classes[i].className, style: 'background: '+classes[i].color+'; color: #fff; font-size: 1.2em'}))  
                        }
                        
                        } catch (error) {
                            console.log(error)
                        }
                    })  

                $.get('../rest/?type=report&method=sections', function(data){
                    try {
                        data = JSON.parse(data);
                        var sections = data.items || [];
                        for (let i = 0; i < sections.length; i++) {
                            fields.classLocation.append(jQuery('<option>', {value: sections[i].room, text: sections[i].room}))  
                        }
                    
                    } catch (error) {
                        console.log(error)
                    }
                })   

                $.get('../rest/?type=report&method=coaches', function(data){
                    try {
                        data = JSON.parse(data);
                        var coaches = data.items || [];
                    for (let i = 0; i < coaches.length; i++) {
                            fields.instructureName.append(jQuery('<option>', {value: coaches[i].coach, text: coaches[i].coach}))  
                    }
                    
                    } catch (error) {
                        console.log(error)
                    }
                })   


                              

                try{
                    fields.dateRange.daterangepicker({
                        startDate: moment(),
                        isRTL: direction,                      
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "<?php echo lang('approval') ?>",
                            "cancelLabel": "<?php echo lang('cancel') ?>",
                        }
                    }).on('apply.daterangepicker', function(){
                        table.DataTable().ajax.reload();
                    });

                }catch(e){
                    console.log(e);
                }            


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
                    paging: true,
                    scrollX: true,
                    scrollY: "450px",
                    scrollCollapse: true,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [
                        {text: lang('send_message_button') + ' <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                            // rows_selected = table.column(0).checkboxes.selected();
                            var clientsIds = dt.column(0).checkboxes.selected().toArray();
                            if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');
      
                            modalsClientIds.val(clientsIds.join(","));
                            modal.modal('show');

                        }},
                        <?php if (Auth::userCan('98')): ?>    
                            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_unregistration') ?>', className: 'btn btn-dark',exportOptions: {}},
			                {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_unregistration') ?>' , className: 'btn btn-dark',exportOptions: {}}
		                 <?php endif ?>
                         
                    ],
                    ajax:{
                        url: '../rest/',
                        method: 'POST',
                        data: function(d){
                            d.type = 'report';
                            d.method = 'registeredVisitors';
                            d.filter = {
                                dateFrom: moment(fields.dateRange.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                dateTo: moment(fields.dateRange.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),
                                name: fields.name.val(),
                                email: fields.email.val(),
                                phone: fields.phone.val(),
                                className: fields.className.val(),
                                instructureName: fields.instructureName.val(),
                                ClassStartTime: fields.ClassStartTime.val(),
                                classLocation: fields.classLocation.val()
                            }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "select", bSortable: false},
                        {"name": "fullName"},
                        {"name": "phone"},
                        {"name": "email"},
                        {"name": "className"},
                        {"name": "instructureName"},
                        {"name": "ClassDate"},
                        {"name": "ClassStartTime"},
                        {"name": "classLocation"}
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
                table.dataTable(settings).on('xhr.dt', function ( e, settings, json, xhr ) {
                    console.log(xhr);

                    json.data = json.items.map(function(x){
                        var data = [];
                        data.push(x.clientId || 0);
                        data.push('<a href="../ClientProfile.php?u='+(x.clientId || 0)+'">'+(x.fullName || '') +'</a>');
                        if(x.phone && x.phone.indexOf('0') == 0){
                            data.push('<a href="tel:+'+x.phone.replace('0', '972').replace(/\D/g,'')+'">'+(x.phone || '') +'</a>');   
                        }else{
                            data.push(x.phone || '');
                        }
                        data.push(x.email || '');
                        data.push(x.className || '');
                        data.push(x.instructureName || '');
                        data.push(x.ClassDate? moment(x.ClassDate).format('D/M/Y'): '');
                        data.push(x.ClassStartTime || '');
                        data.push(x.classLocation || '');
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