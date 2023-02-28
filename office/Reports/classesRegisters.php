<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 $report = new StdClass();
 $report->name = lang('reports_class_attend');
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

<div class="row px-0 "  >
    <div class="col-12 px-0" >


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

                                <div class="row px-15"  >
                                    <table class="table table-hover text-start display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"><?php echo lang('class_single') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_time') ?></th>
                                                <th style="text-align:start;"><?php echo lang('instructor') ?></th>
                                                <th style="text-align:start;"><?php echo lang('client_name') ?></th>
                                                <th style="text-align:start;"><?php echo lang('branch') ?></th>
                                                <th style="text-align:start;"><?php echo lang('telephone') ?></th>
                                                <!-- <th style="text-align:start;">הנה"ח</th> -->
                                                <th style="text-align:start;"><?php echo lang('membership') ?></th>
                                                <!-- <th style="text-align:start;">תוקף</th> -->
                                                <th style="text-align:start;"><?php echo lang('class_tabe_card') ?></th>
                                                <!-- <th style="text-align:start;">הערה</th> -->
                                                <th style="text-align:start;"><?php echo lang('table_medical_records') ?></th>
                                                <th style="text-align:start;"><?php echo lang('documentation_single') ?></th>
                                                
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th></th>
                                                <th style="text-align:start;"><input name="className" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                <th style="text-align:start;"><input name="classDate" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                <th style="text-align:start;"><input name="classTime" type="time" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>        
                                                <th style="text-align:start;">
                                                    <select name="guideName" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;"><input name="fullName" type="text" class="form-control" placeholder="<?php echo lang('search_by_name') ?>"></th>
                                                <th style="text-align:start;">
                                                    <select name="branch" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;"><input name="phone" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>
                                                <!-- <th style="text-align:start;"><input name="balanceAmount" type="text" class="form-control" placeholder="חפש"></th>      -->
                                                <th style="text-align:start;"><input name="membership" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                <!-- <th style="text-align:start;"><input name="memberExpire" type="text" class="form-control" placeholder="חפש"></th>      -->
                                                <th style="text-align:start;"><input name="ticket" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                <!-- <th style="text-align:start;"><input name="comment" type="text" class="form-control" placeholder="חפש"></th>      -->
                                                <th style="text-align:start;"><input name="medical" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                <th style="text-align:start;"><input name="crm" type="text" class="form-control" placeholder="<?php echo lang('a_search') ?>"></th>     
                                                
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
                    fullName: $("input[name='fullName']", filter),
                    phone: $("input[name='phone']", filter),
                    balanceAmount: $("input[name='balanceAmount']", filter),
                    membership: $("input[name='membership']", filter),    
                    // memberExpire: $("input[name='memberExpire']", filter),  
                    ticket: $("input[name='ticket']", filter),
                    // comment: $("input[name='comment']", filter),
                    medical: $("input[name='medical']", filter),
                    crm: $("input[name='crm']", filter),
                    className: $("input[name='className']", filter),
                    classDate: $("input[name='classDate']", filter),
                    classTime: $("input[name='classTime']", filter),
                    guideName: $("select[name='guideName']", filter),
                    branch: $("select[name='branch']", filter)
                }
                // Prepares the header of branches column
                $.get('../rest/?type=report&method=branches', function(data){
                    try {
                        data = JSON.parse(data);
                        var branches = data.items || [];
                        for (let i = 0; i < branches.length; i++) {
                            fields.branch.append(jQuery('<option>', {value: branches[i].branch, text: branches[i].branch}))
                       }

                    } catch (error) {
                        console.log(error)
                    }
                });

                // Prepares the header of branches coaches
                $.get('../rest/?type=report&method=coaches', function(data){
                    try {
                        data = JSON.parse(data);
                        var coaches = data.items || [];
                       for (let i = 0; i < coaches.length; i++) {
                            fields.guideName.append(jQuery('<option>', {value: coaches[i].coach, text: coaches[i].coach})) 
                       }

                    } catch (error) {
                        console.log(error)
                    }
                });


                // the magic for the filter
                for(var field in fields){
                    fields[field].on('keyup change', function(e){
                        if(e.target.type.indexOf('select') != -1 || e.keyCode == 13) return table.DataTable().ajax.reload();
                    })
                }

                 // convert date to daterange
                try{
                    fields.classDate.daterangepicker({
                        startDate: moment(), //.startOf('month'),
                        endDate: moment(), //.endOf('month'),
                        isRTL: direction,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "<?php echo lang('approval') ?>",
                            "cancelLabel": "<?php echo lang('cancel') ?>",
                        }
                    }).on('apply.daterangepicker', function(){table.DataTable().ajax.reload();});

                    // fields.memberExpire.daterangepicker({
                    //     isRTL: true,
                    //     langauge: 'he',
                    //     locale: {
                    //         format: 'DD/M/YY',
                    //         "applyLabel": "אישור",
                    //         "cancelLabel": "ללא תאריך",
                    //     }
                    // }).on('cancel.daterangepicker', function(){
                    //     fields.memberExpire.val('');
                    //     table.DataTable().ajax.reload();
                    // }).on('apply.daterangepicker', function(){table.DataTable().ajax.reload();});

                    // fields.memberExpire.val('');



                    

                }catch(e){
                    console.log(e);
                }   


                var modal = $('#SendClientPush');
                var modalsClientIds = $('input[name="clientsIds"]', modal);
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var settings = {
                    orderCellsTop: true, // sorting only on first raw in thead
                    language: BeePOS.options.datatables,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    paging: true,
                    // scrollX: true,
//                    scrollY: "450px",
//                    scrollCollapse: true,
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
                            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_class_attend') ?>', className: 'btn btn-dark',exportOptions: {}},
			                {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_class_attend') ?>' , className: 'btn btn-dark',exportOptions: {}}
		                 <?php endif ?>
                         
                    ],
                    ajax:{
                        url: '../rest/',
                        method: 'POST',
                        data: function(d){
                            d.type = 'report';
                            d.method = 'classregister';
                            d.filter = {
                                fullName: fields.fullName.val(),
                                phone: fields.phone.val(),
                                balanceAmount: fields.balanceAmount.val(),
                                 membership: fields.membership.val(),   
                                // memberExpire: fields.memberExpire.val(),
                                ticket: fields.ticket.val(),
                                // comment: fields.comment.val(),
                                medical: fields.medical.val(),
                                crm: fields.crm.val(),
                                className: fields.className.val(),
                                classTime: fields.classTime.val(),
                                guideName: fields.guideName.val(),
                                branch: fields.branch.val(),
                                
                                dateFrom: moment(fields.classDate.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                dateTo: moment(fields.classDate.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),

                            }

                            // if(fields.memberExpire.val()){
                            //     d.filter.memberExpireDateFrom = moment(fields.memberExpire.data('daterangepicker').startDate._d).format('YYYY-MM-DD')
                            //     d.filter.memberExpiredateTo = moment(fields.memberExpire.data('daterangepicker').endDate._d).format('YYYY-MM-DD')
                            // }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "clientId", bSortable: false},
                        {"name": "className"},
                        {"name": "classDate"},
                        {"name": "classTime"},
                        {"name": "guideName"},
                        {"name": "fullName"},
                        {"name": "branch"},
                        {"name": "phone"},
                        // {"name": "balanceAmount"},
                        {"name": "membership"},
                        // {"name": "memberExpire"},
                        {"name": "ticket"},
                        // {"name": "comment"},
                        {"name": "medical"},
                        {"name": "crm"}
                        
                    ],
                    bFilter: false, // hide search field
                    bSort: true,
                    pageLength: 100,
//                    lengthChange: true,
//                    lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
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
                    order: [[2, 'asc']]

                }
                table.dataTable(settings).on('xhr.dt', function ( e, s, json, xhr ) {


                    json.data = json.items.map(function(x){
                        var data = [];
                        for (let index = 0; index < settings.columns.length; index++) {
                            if(settings.columns[index].name === 'fullName'){
                                data.push('<a href="../ClientProfile.php?u='+(x.clientId || 0)+'">'+(x[settings.columns[index].name] || '<?php echo lang('no_data') ?>') +'</a>');
                                continue;
                            }
                            if(settings.columns[index].name === 'phone'){
                                if(x.phone && x.phone.indexOf('0') == 0){
                                    data.push('<a href="tel:+'+x.phone.replace('0', '972').replace(/\D/g,'')+'">'+(x.phone || '') +'</a>');   
                                }else{
                                    data.push(x.phone || '');
                                }
                                continue;
                            }
                            if(settings.columns[index].name === 'branch'){
                                data.push(x.branchName || '');
                                continue;
                            }
                            if(settings.columns[index].name === 'medical'){
                                if(x.medical && x.medical.length){
                                    // var str  = '<ul>';
                                    //     str += x.medical.map(function(x){return '<li>'+x.medical+'</li>'}).join("");
                                    //     str += '</ul>';
                                    data.push( x.medical.map(function(x){return x.medical}).reverse().join(", "));
                                }else{
                                    data.push(x.medical);
                                }
                                continue;     
                            }
                            if(settings.columns[index].name === 'crm'){
                                var comment = (x.comment) ? x.comment : '';
                                var crm = (x.crm && x.crm.length) ? (x.crm.map(function(x){return x.Remarks}).reverse().join(", ")) : (x.crm || '');
                                if(crm != '') comment += ((comment != '') ? '<hr>' : '') +crm;
                                data.push(comment);
                                continue;
                            }

                            if(settings.columns[index].name === 'membership'){
                                // x.membership = (x.membership[x.membership.length -1] === '0') ? x.membership.substr(1).trim() : x.membership;
                                data.push(x.membership ?  x.membership.charAt(x.membership.length - 1) === '0' ? x.membership.slice(0, -1) : x.membership : x.membership);
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