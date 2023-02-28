<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 echo View::make('headernew')->render();

 $report = new StdClass();
 $report->name = 'שיעורים שדורשים PUSH';


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
                                                <th style="text-align:right;">תאריך</th>
                                                <th style="text-align:right;">סניף</th>
                                                <th style="text-align:right;">מיקום</th>
                                                <th style="text-align:right;">שעה</th>
                                                <th style="text-align:right;">שיעור</th>
                                                <th style="text-align:right;">מדריך</th>
                                                <th style="text-align:right;">מקומות פנויים</th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th style="text-align:right;"><input name="date" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;">
                                                    <select name="branch" class="form-control">
                                                        <option value="">הכל</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;"><input name="classLocation" type="text" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;"><input name="classTime" type="time" class="form-control" placeholder="חפש"></th>
                                                <th style="text-align:right;">
                                                    <select name="className" class="form-control">
                                                        <option value="">הכל</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;">
                                                    <select name="guideName" class="form-control">
                                                        <option value="">הכל</option>
                                                    </select>
                                                </th>
                                                <th style="text-align:right;"></th>
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
                    branch: $("select[name='branch']", filter),
                    classLocation: $("input[name='classLocation']", filter),
                    classTime: $("input[name='classTime']", filter),
                    className: $("select[name='className']", filter),
                    guideName: $("select[name='guideName']", filter)

                }

    try{
        fields.date.daterangepicker({
            startDate: moment(),
            endDate: moment().add(1, 'day'), //.endOf('month'),
            isRTL: true,
            langauge: 'he',
            locale: {
                format: 'DD/M/YY',
                "applyLabel": "אישור",
                "cancelLabel": "ביטול",
            }
        }).on('apply.daterangepicker', function(){table.DataTable().ajax.reload();});

    }catch(e){
        console.log(e);
    }            
                // get branches name list
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
                });

                
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
                            d.method = 'push';
                            d.filter = {
                                dateFrom: moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                dateTo: moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),
                                branch: fields.branch.val(),
                                classTime: fields.classTime.val(),
                                className: fields.className.val(),
                                classLocation: fields.classLocation.val(),
                                guideName: fields.guideName.val()
                            }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "date"},
                        {"name": "branch"},
                        {"name": "classLocation"},
                        {"name": "classTime"},
                        {"name": "className"},
                        {"name": "guideName"},
                        {"name": "spacesLeft"}
                    ],
                    bFilter: false, // hide search field
                    bSort: true,
                    pageLength: 100,
                    lengthChange: true,
                    lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
                    select: {
                        style: 'multi'
                    },
                    order: [[1, 'asc']]

                }
                table.dataTable(settings).on('xhr.dt', function ( e, settings, json, xhr ) {


                    json.data = json.items.map(function(x){
                        var data = [];
                       
                        data.push(x.displayDate || '');
                        data.push(x.branchName || '');
                        data.push(x.classLocation || '');
                        data.push(x.classTime || '');
                        data.push(x.className || '');
                        data.push(x.guideName || '0');
                        data.push(x.spacesLeft || '0');
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