<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 $report = new StdClass();
 $report->name = lang('capacity_percentage_in_classes');
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

<div class="row px- mx-0"  >
    <div class="col-12 px-0 mx-0" >


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">????????</a>
                </li>
                <li class="breadcrumb-item active">??????????</li>
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

                                                                                            <style>
                                                            #daysrangeFilter {}
                                                            #daysrangeFilter input+label{background: lightgray; color: #fff; font-weight: bold}
                                                            #daysrangeFilter input:checked+label{background: #48AD42}
                                                        </style>
                                                        <div id="daysrangeFilter" class="row" >
                                                        <div class="col">
                                                        <div>
                                                        <?php echo lang('reports_lesson_occupancy_days') ?></div>
                                                        <input type="checkbox" id="dateRangeSun" checked class="d-none" name="daysInRange[]" value="7">
                                                        <label for="dateRangeSun" class="btn btn-sm"><?php echo lang('word_alef_first') ?></label>

                                                         <input type="checkbox" id="dateRangeMon" class="d-none" checked name="daysInRange[]" value="1">
                                                        <label for="dateRangeMon" class="btn btn-sm"><?php echo lang('word_bet_second') ?></label>

                                                        <input type="checkbox" id="dateRangeTus" class="d-none" checked name="daysInRange[]" value="2">
                                                        <label for="dateRangeTus" class="btn btn-sm"><?php echo lang('word_gimel_third') ?></label>

                                                        <input type="checkbox" id="dateRangeWed" class="d-none" checked name="daysInRange[]" value="3">
                                                        <label for="dateRangeWed" class="btn btn-sm"><?php echo lang('word_dalet_fourth') ?></label>

                                                        <input type="checkbox" id="dateRangeThu" class="d-none" checked name="daysInRange[]" value="4">
                                                        <label for="dateRangeThu" class="btn btn-sm"><?php echo lang('word_hei_fifth') ?></label>

                                                        <input type="checkbox" id="dateRangeFri" class="d-none" checked name="daysInRange[]" value="5">
                                                        <label for="dateRangeFri" class="btn btn-sm"><?php echo lang('word_vav_sixth') ?></label>

                                                        <input type="checkbox" id="dateRangeSat" class="d-none" checked name="daysInRange[]" value="6">
                                                        <label for="dateRangeSat" class="btn btn-sm"><?php echo lang('word_shin_seventh') ?></label>
                                                        </div>
                                                        </div>
                                <div class="row px-15" >

                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">

                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th style="text-align:start;"><?php echo lang('date') ?></th>
                                                <th style="text-align:start;"><?php echo lang('hour') ?></th>
                                                <th style="text-align:start;"><?php echo lang('class_name') ?></th>
                                                <!-- <th style="text-align:start;">?????????? ??????????</th> -->
                                                <th style="text-align:start;"><?php echo lang('table_ocupancy_percentage') ?></th>
                                                <th style="text-align:start;"><?php echo lang('table_ocupancy_entry') ?></th>
                                                <th style="text-align:start;"><?php echo lang('signed') ?></th>
                                                
                                                <th style="text-align:start;"><?php echo lang('arrivals') ?></th>
                                                <th style="text-align:start;"><?php echo lang('not_arrived') ?></th>
                                                <th style="text-align:start;"><?php echo lang('late_cancellation') ?></th>
                                                <th style="text-align:start;"><?php echo lang('available_spaces') ?></th>
                                                <th style="text-align:start;"><?php echo lang('total_spaces') ?></th>
                                                <th style="text-align:start;"><?php echo lang('table_waitlist') ?></th>
                                            </tr>
                                            <tr class="bg-white text-black filterHeader">
                                                <th style="text-align:start;">
                                                        <input name="date" type="text" class="form-control" placeholder="<?php echo lang('search_single') ?>">

                                                </th>
                                                <th style="text-align:start;">
                                                        <input type="time" name="time" placeholder="<?php echo lang('select_hour') ?>">
                                                </th>
                                                <th style="text-align:start;">
                                                    <select name="className" class="form-control">
                                                        <option value=""><?php echo lang('all') ?></option>
                                                    </select>
                                                    </th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <th style="text-align:start;"></th>
                                                <!-- <th style="text-align:start;"></th> -->
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
                    date: $('input[name="date"]', filter),
                    daysRange: jQuery('[name="daysInRange[]"]', jQuery('#daysrangeFilter')),
                    className: $("select[name='className']", filter),
                    time: $("input[name='time']", filter)
                    // spacesTaken: $("input[name='spacesTaken']", filter),
                    // absent: $("input[name='absent']", filter),
                    // lateCancelation: $("input[name='lateCancelation']", filter),
                    // spacesAvailable: $("input[name='spacesAvailable']", filter),
                    // spaces: $("input[name='spaces']", filter),
                    // waitingList: $("select[name='waitingList']", filter)
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

                try{
                    fields.date.daterangepicker({
                        startDate: moment().subtract(7,'d'),
                        endDate: moment(), //.endOf('month'),
                        isRTL: direction,
                        
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "<?php echo lang('approval') ?>",
                            "cancelLabel": "<?php echo lang('cancel') ?>",
                        }
                    }).on('apply.daterangepicker', function(){table.DataTable().ajax.reload();});

                }catch(e){
                    console.log(e);
                }            

                // the magic for the filter
                for(var field in fields){
                    fields[field].on('keyup change', function(e){
                        if(e.target.type.indexOf('select') != -1 || e.target.type.indexOf('checkbox') != -1 || e.keyCode == 13) return table.DataTable().ajax.reload();                                   
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
                    scrollX: true,
                    scrollY: "750px",
                    scrollCollapse: true,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [

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
                            d.method = 'occupancy';
                            d.display = 'details';
                            d.filter = {
                                dateFrom: moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD'),
                                dateTo: moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD'),
                                className: fields.className.val(),
                                time: fields.time.val(),
                                daysRange: fields.daysRange.filter(':checked').map(function(){return jQuery(this).val()}).get()
                                // spacesTaken: fields.spacesTaken.val(),
                                // absent: fields.absent.val(),
                                // lateCancelation: fields.lateCancelation.val(),
                                // lateCancelation: fields.spacesAvailable.val(),
                                // lateCancelation: fields.waitingList.val()
                            }
                        }
                    },
                    serverSide: true,
                    columns: [
                        {"name": "date"},
                        {"name": "time"},
                        {"name": "className"},
                        {"name": "percentArrived"},
                        // {"name": "percentNoArrived"},
                        {"name": "spacesTakenPercent"},
                        {"name": "spacesTaken"},
                        {"name": "taken"},
                        {"name": "absent"},
                        {"name": "lateCancelation"},
                        {"name": "spacesAvailable"},
                        {"name": "spaces"},
                        {"name": "waitingList"}
                    ],
                    bFilter: false, // hide search field
                    bSort: true,
                    // pageLength: 100,
                    lengthChange: false,
                    // lengthMenu: [ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500 ],
                    select: {
                        style: 'multi'
                    }

                }
                table.dataTable(settings).on('xhr.dt', function ( e, settings, json, xhr ) {


                    json.data = json.items.map(function(x){
                        var data = [];
                        data.push(x.date || 0); // ??????????
                        data.push(x.classStartTime || 0); // ??????????
                        data.push(x.className || 0); // ???? ??????????
                        

                        var percent = '0';
                        try {
                            percent = (( (parseInt(x.MaxClient) - parseInt(x.ClientRegister)) / parseInt(x.MaxClient)) *100)
                        } catch (error) {
                            
                        }

                        data.push((100-percent).toFixed(2) + '%'); // ?????????? ????????
                        // data.push((percent).toFixed(2) + '%'); // ?????????? ???? ????????

                        var taken = Math.round(
                          ( ( parseFloat(x.MaxClient) - parseFloat(x.ClientRegister) ) / ( parseFloat(x.ClientRegister) / parseFloat(x.MaxClient))) *100
                        );

                        data.push(((((parseInt(x.ClientRegister) - parseInt(x.absent)) / parseInt(x.MaxClient)) *100).toFixed() +'%'));  // ?????????? ??????????
                        data.push(x.ClientRegister);  // ??????????
                        
                        data.push( parseInt(x.ClientRegister) - parseInt(x.absent) );  // ??????????
                        data.push(x.absent ); // ???? ??????????
                        data.push(x.lateCancelation );
                        data.push(parseInt(x.MaxClient) - parseInt(x.ClientRegister));
                        data.push(parseInt(x.MaxClient));
                        data.push(x.waitingList );
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