<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 $report = new StdClass();
 $report->name = lang('birthdays');
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

<div class="row"  style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
    <div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo  $report->name ?>
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

                                <div class="row"  style="padding-left:15px; padding-right:15px;">
                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"><?php echo lang('client_name') ?></th>
                                                <th style="text-align:start;"><?php echo lang('branch') ?></th>
                                                <th style="text-align:start;"><?php echo lang('telephone') ?></th>
                                                <th style="text-align:start;"><?php echo lang('email_table') ?></th>
                                                <th style="text-align:start;"><?php echo lang('date_birthday') ?></th>
                                                <th style="text-align:start;"><?php echo lang('table_gender') ?></th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th></th>
            
                                                <th style="text-align:start;"><input name="name" type="text" class="form-control" placeholder="<?php echo lang('search_by_name') ?>"></th>
                                                <th style="text-align:start;">
                                                    <select name="branch" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;"><input name="phone" type="text" class="form-control" placeholder="<?php echo lang('search_by_phone') ?>"></th>
                                                <th style="text-align:start;"><input name="email" type="text" class="form-control" placeholder="<?php echo lang('search_by_email') ?>"></th>
                                                <th style="text-align:start;">
                                                    <select name="dob" class="form-control">
                                                        <?php 
                                                        $months = [
                                                            lang('january'),
                                                            lang('february'),
                                                            lang('march'),
                                                            lang('april'),
                                                            lang('may'),
                                                            lang('june'),
                                                            lang('july'),
                                                            lang('august'),
                                                            lang('september'),
                                                            lang('october'),
                                                            lang('november'),
                                                            lang('december')
                                                        ];
                                                        for ($i=0; $i < 12; $i++) { 
                                                            printf('<option value="%d" %s>%s</option>', $i+1, (((int) date("n") == $i+1)?"selected":""), $months[$i] );
                                                        }?>
                                                    </select>
                                                </th>
                                                <th style="text-align:start;">
                                                    <select name="gender" class="form-control">
                                                        <option value=""><?php echo lang('choose_gender') ?></option>
                                                        <!-- <option value="0">לא מוגדר</option> -->
                                                        <option value="1"><?php echo lang('male') ?></option>
                                                        <option value="2"><?php echo lang('female') ?></option>
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
                    name: $("input[name='name']", filter),
                    branch: $("select[name='branch']", filter),
                    email: $("input[name='email']", filter),
                    phone: $("input[name='phone']", filter),
                    gender: $("select[name='gender']", filter),
                    dob: $("select[name='dob']", filter)
                }

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
                    scrollY: "450px",
                    scrollCollapse: true,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [
                        {text: lang('send_message_button') + ' <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                            // rows_selected = table.column(0).checkboxes.selected();
                            var clientsIds = dt.column(0).checkboxes.selected().toArray();
                            if(!clientsIds.length) return alert(lang('select_customers'));
      
                            modalsClientIds.val(clientsIds.join(","));
                            modal.modal('show');

                        }},
                        <?php if (Auth::userCan('98')): ?>    
                            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('birthday_report') ?>', className: 'btn btn-dark',exportOptions: {}},
			                {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('birthday_report') ?>', className: 'btn btn-dark',exportOptions: {}}
		                 <?php endif ?>
                         
                    ],




                    ajax:{
                        url: '../rest/',
                        method: 'POST',
                        data: function(d){
                            d.type = 'report';
                            d.method = 'bday';
                            d.filter = {
                                name: fields.name.val(),
                                branch: fields.branch.val(),
                                email: fields.email.val(),
                                phone: fields.phone.val(),
                                gender: fields.gender.val(),
                                dob: fields.dob.val()
                            }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "select", bSortable: false},
                        {"name": "fullName"},
                        {"name": "branch"},
                        {"name": "phone"},
                        {"name": "email"},
                        {"name": "dob"},
                        {"name": "gender"}
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


                    json.data = json.items.map(function(x){
                        var data = [];
                        data.push(x.clientId || 0);
                        data.push('<a href="../ClientProfile.php?u='+(x.clientId || 0)+'">'+(x.fullName || '') +'</a>');
                        data.push(x.branch || '');
                        if(x.phone && x.phone.indexOf('0') == 0){
                            data.push('<a href="tel:+'+x.phone.replace('0', '972').replace(/\D/g,'')+'">'+(x.phone || '') +'</a>');
                        }else{
                            data.push(x.phone || '');
                        }

                        data.push(x.email || '');
                        data.push(x.dob || '');
                        data.push(x.gender || '');
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