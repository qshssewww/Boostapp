<?php


require_once '../app/init.php';
require_once './Classes/LeadSource.php';
require_once './Classes/Automation.php';

$CompanyNum = Auth::user()->CompanyNum;
$Brands = new Brand();
$LeadSource = new LeadSource();
$Users = new Users();

$Brands = $Brands->getAllByCompanyNum($CompanyNum);
$LeadSources = $LeadSource->getLeadSources($CompanyNum);

$pageTitle = (isset($_GET['open']) && $_GET['open'] == 1) ? lang('manage_open_leads') : lang('manage_interested');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('47')): ?>
        <?php
        $CompanyNum = Auth::user()->CompanyNum;
        $Category2 = (new Automation())->getAutomationAmount($CompanyNum, '2');
        ?>


        <link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
        <link href="<?php echo App::url('CDN/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet">

        <link href="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet">
        <link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">
        <link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">


        <script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>

        <script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>
        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
        <script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>
        <script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>

<!--        <script src="--><?php //echo App::url('CDN/datatables/moment.min.js') ?><!--"></script>-->
        <script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>

        <script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>
        <script src="<?php echo App::url('office/js/datatable/dataTables.checkboxes.min.js') ?>"></script>

<!--        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
         <!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <link href="assets/css/leadsJoinReports.css" rel="stylesheet">

        <?php
        $datesFilter = "lastmonth";
        $datepickerValue = null;
        if(array_key_exists('today', $_GET) || array_key_exists('lastmonth', $_GET)){
            if(!empty($_GET['lastmonth'])) {
                $datesFilter = "lastmonth";
            }
            elseif(!empty($_GET['today'])) {
                $datesFilter = "today";
                $datepickerValue = date('d-m-Y')." - ".date('d-m-Y');
            }
        }
        ?>

        <div class="container-fluid">
            <nav class="nav nav-tabs nav-fill" id="leadsReport-nav-tabs" role="tablist">
                <a class="nav-item nav-link pill-1 cursor-pointer active" id="leadsReport-inProcess-tab" data-toggle="tab"
                    href="#leadsReport-inProcess" role="tab" aria-controls="leadsReport-inProcess" aria-selected="true">
                    <strong><?php echo lang('leads').' '; echo lang('meeting_pending')?>  (<span id="pending-count"><i class="fas fa-spinner fa-pulse"></i></span>)</strong>
                </a>
                <a class="nav-item nav-link pill-1 cursor-pointer" id="leadsReport-succeeded-tab" data-toggle="tab"
                    href="#leadsReport-succeeded" role="tab" aria-controls="profile" aria-selected="false">
                    <strong><?php echo lang('success')?>  (<span id="succeeded-count"><i class="fas fa-spinner fa-pulse"></i></span>)</strong>
                </a>
                <a class="nav-item nav-link pill-1 cursor-pointer" id="leadsReport-failure-tab" data-toggle="tab"
                    href="#leadsReport-failure" role="tab" aria-controls="contact" aria-selected="false">
                    <strong><?php echo lang('failure')?>  (<span id="faliure-count"><i class="fas fa-spinner fa-pulse"></i></span>)</strong>
                </a>
            </nav>
            <div class="tab-content" id="leadsReport-tab-content">
                <div class="tab-pane fade show active" id="leadsReport-inProcess" role="tabpanel" aria-labelledby="leadsReport-inProcess-tab">
                    <div class="tableFiltersWrap d-flex flex-wrap col-12 col-lg-6">
                        <div class="dateFilterSelectWrap d-flex py-10 px-10 d-flex">
                            <label for="dateFilterInProccess" class="mie-7 align-self-center font-weight-bold"><?php echo lang('filter_by_date')?>:</label>
                            <select class="dateFilterSelect form-control" name="dateFilterInProccess" data-table="inProcess">
                                <option value="lastWeek"><?php echo lang('pink_dash_last_week'); ?></option>
                                <option value="currMonth" <?php echo $datesFilter === 'lastmonth' ? 'selected' : '';?>><?php echo lang('dash_pink_last_month'); ?></option>
                                <option value="prevMonth"><?php echo lang('previous_month'); ?></option>
                                <option value="datesRange" <?php echo $datesFilter === 'today' ? 'selected' : '';?>><?php echo lang('date_range'); ?></option>
                                <option value="all" <?php echo $datesFilter === 'all' ? 'selected' : '';?> ><?php echo lang('all_the_time'); ?></option>
                            </select>
                        </div>                
                        <div class="datepicker-wrap text-end py-10 px-10 <?php echo $datepickerValue ? 'active' : '' ?>">
                            <label class="mie-7 align-self-center font-weight-bold "
                                for=""><?php echo lang('date_range') ?>:</label>
                            <input type="text" name="date" class="form-control width-fit js-date-range" <?php echo $datepickerValue ? 'value="'.$datepickerValue.'"' : '';?>>
                        </div>
                    </div>                    
                    <table id="leadsReportTable-inProcess" class="table table-hover dt-responsive  wrap text-start" cellspacing="0">
                        <thead class="thead-dark">
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start"><?php echo lang('branch') ?></th>
                                <th class="text-start"><?php echo lang('facebook_pipeline') ?></th>
                                <th class="text-start"><?php echo lang('stage_sale') ?></th>
                                <th class="text-start"><?php echo lang('incoming_source') ?></th>
                                <th class="text-start creation_date"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('task_single') ?></th>
                                <th class="text-start"><?php echo lang('trial_lesson') ?></th>
                                <th class="text-start"><?php echo lang('lesson_arrival_status') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start branchFilter">
                                    <select class="form-control">
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($Brands as $Brand): ?> <option><?php echo $Brand->BrandName; ?><option> <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start"><?php echo lang('facebook_pipeline') ?></th>
                                <th class="text-start"><?php echo lang('stage_sale') ?></th>
                                <th class="text-start incomingSourceFilter"> 
                                    <select class="form-control"> 
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($LeadSources as $LeadSource): ?> <option><?php echo $LeadSource->Title; ?><option> <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start creation_date"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('task_single') ?></th>
                                <th class="text-start"><?php echo lang('last_class') ?></th>
                                <th class="text-start"><?php echo lang('status_table') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tab-pane fade" id="leadsReport-succeeded" role="tabpanel" aria-labelledby="leadsReport-succeeded-tab"> 
                    <div class="tableFiltersWrap d-flex flex-wrap col-12 col-lg-6">
                        <div class="dateFilterSelectWrap d-flex py-10 px-10 d-flex">
                            <!-- <label for="dateFilterSucceeded" class="mie-7 align-self-center font-weight-bold"><?php echo lang('filter_by_date')?>:</label> -->
                            <label for="dateFilterSucceeded" class="mie-7 align-self-center font-weight-bold">סינון לפי תאריך:</label>
                            <select class="dateFilterSelect form-control" name="dateFilterInSucceeded" data-table="success">
                                <option value="lastWeek"><?php echo lang('pink_dash_last_week'); ?></option>
                                <option value="currMonth" <?php echo $datesFilter === 'lastmonth' ? 'selected' : '';?>><?php echo lang('dash_pink_last_month'); ?></option>
                                <option value="prevMonth"><?php echo lang('previous_month'); ?></option>
                                <option value="datesRange" <?php echo $datesFilter === 'today' ? 'selected' : '';?>><?php echo lang('date_range'); ?></option>
                                <option value="all" <?php echo !$datesFilter ? 'selected' : '';?>><?php echo lang('all_the_time'); ?></option>
                            </select>
                        </div>                
                        <div class="datepicker-wrap text-end py-10 px-10 <?php echo $datepickerValue ? 'active' : '' ?>">
                            <label class="mie-7 align-self-center font-weight-bold "
                                for=""><?php echo lang('date_range') ?>:</label>
                            <input type="text" name="date" class="form-control width-fit js-date-range" <?php echo $datepickerValue ? 'value="'.$datepickerValue.'"' : '';?>>
                        </div>
                    </div>                    
                    <table id="leadsReportTable-succeeded" class="table table-hover dt-responsive  display wrap text-start" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start"><?php echo lang('branch') ?></th>
                                <th class="text-start"><?php echo lang('incoming_source') ?></th>
                                <th class="text-start creation_date"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('conversion_date') ?></th>
                                <th class="text-start"><?php echo lang('task_single') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start branchFilter">
                                    <select class="form-control">
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($Brands as $Brand): ?>
                                        <option><?php echo $Brand->BrandName; ?></option> <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start incomingSourceFilter"> 
                                    <select class="form-control"> 
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($LeadSources as $LeadSource): ?>
                                        <option><?php echo $LeadSource->Title; ?><option> 
                                        <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('conversion_date') ?></th>
                                <th class="text-start"><?php echo lang('task_single') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tab-pane fade" id="leadsReport-failure" role="tabpanel" aria-labelledby="leadsReport-failure-tab">                             
                    <div class="tableFiltersWrap d-flex flex-wrap col-12 col-lg-6">
                        <div class="dateFilterSelectWrap d-flex py-10 px-10 d-flex">
                            <!-- <label for="dateFilterFailure" class="mie-7 align-self-center font-weight-bold"><?php echo lang('filter_by_date')?>:</label> -->
                            <label for="dateFilterFailure" class="mie-7 align-self-center font-weight-bold">סינון לפי תאריך:</label>
                            <select class="dateFilterSelect form-control" name="dateFilterFailure" data-table="failure">
                                <option value="lastWeek"><?php echo lang('pink_dash_last_week'); ?></option>
                                <option value="currMonth" <?php echo $datesFilter === 'lastmonth' ? 'selected' : '';?>><?php echo lang('dash_pink_last_month'); ?></option>
                                <option value="prevMonth"><?php echo lang('previous_month'); ?></option>
                                <option value="datesRange" <?php echo $datesFilter === 'today' ? 'selected' : '';?>><?php echo lang('date_range'); ?></option>
                                <option value="all" <?php echo !$datesFilter ? 'selected' : '';?>><?php echo lang('all_the_time'); ?></option>
                            </select>
                        </div>                
                        <div class="datepicker-wrap text-end py-10 px-10 <?php echo $datepickerValue ? 'active' : '' ?>">
                            <label class="mie-7 align-self-center font-weight-bold "
                                for=""><?php echo lang('date_range') ?>:</label>
                            <input type="text" name="date" class="form-control width-fit js-date-range" <?php echo $datepickerValue ? 'value="'.$datepickerValue.'"' : '';?>>
                        </div>
                    </div>                    
                    <table id="leadsReportTable-failure" class="table table-hover dt-responsive  display wrap text-start" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start"><?php echo lang('branch') ?></th>
                                <th class="text-start"><?php echo lang('incoming_source') ?></th>
                                <th class="text-start creation_date"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('conversion_date') ?></th>
                                <th class="text-start"><?php echo lang('trial_lesson') ?></th>
                                <th class="text-start"><?php echo lang('coach_single') ?></th>
                                <th class="text-start"><?php echo lang('reason_leave_not_join') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="">
                                <th class="text-start"></th>
                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                <th class="text-start"><?php echo lang('email_table') ?></th>
                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                <th class="text-start branchFilter">
                                    <select class="form-control">
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($Brands as $Brand): ?> <option><?php echo $Brand->BrandName; ?><option> <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start incomingSourceFilter"> 
                                    <select class="form-control"> 
                                        <option value=""><?php echo lang('all') ?></option>
                                        <?php foreach($LeadSources as $LeadSource): ?>
                                        <option><?php echo $LeadSource->Title; ?><option> <?php endforeach ?>
                                    </select>
                                </th>
                                <th class="text-start"><?php echo lang('creation_date') ?></th>
                                <th class="text-start"><?php echo lang('conversion_date') ?></th>
                                <th class="text-start"><?php echo lang('trial_lesson') ?></th>
                                <th class="text-start"><?php echo lang('coach_single') ?></th>
                                <th class="text-start"><?php echo lang('reason_leave_not_join') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>

            $(document).ready(function () {

                var myDefaultWhiteList = $.fn.tooltip.Constructor.Default.whiteList;
                myDefaultWhiteList.a = ['data-client', 'data-pipe-id', 'data-task'];


                $('#leadsReportTable-inProcess tfoot th').each(function (index) {
                    let title = $(this).text();

                    if (!$(this).hasClass("branchFilter") &&
                        !$(this).hasClass("statusFilter") &&
                        !$(this).hasClass("incomingSourceFilter") && index > 0) {
                        $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');
                    }
                });

                $('#leadsReportTable-succeeded tfoot th').each(function (index) {
                    let title = $(this).text();

                    if (!$(this).hasClass("branchFilter") &&
                        !$(this).hasClass("representativeFilter") &&
                        !$(this).hasClass("incomingSourceFilter") && index > 0) {
                        $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');
                    }
                });

                $('#leadsReportTable-failure tfoot th').each(function (index) {
                    let title = $(this).text();

                    if (!$(this).hasClass("branchFilter") &&
                        !$(this).hasClass("incomingSourceFilter") && index > 0) {
                        $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');
                    }
                });


                $('a#leadsReport-inProcess-tab').on('click', function(event){
                    setTimeout(() => $('#leadsReportTable-inProcess').DataTable().columns.adjust().draw() , 200);     
                });

                $('a#leadsReport-succeeded-tab').on('click', function(event){
                    setTimeout(() => $('#leadsReportTable-succeeded').DataTable().columns.adjust().draw(), 200);
                });
                $('a#leadsReport-failure-tab').on('click', function(event){ 
                    setTimeout(() => $('#leadsReportTable-failure').DataTable().columns.adjust().draw(), 200);
                });


                // new task button
                $("body").on("click", ".js-new-task", function () {
                    var new_task = $(this).attr("data-task");
                    var client_id = $(this).attr("data-client");
                    var pipe_id = $(this).attr("data-pipe-id");
                    NewCal(new_task, client_id, pipe_id);
                });


                const ajaxData = {
                    inProcess: {
                        start: moment().startOf('month').format('YYYY-MM-DD'),
                        end: moment().endOf('month').format('YYYY-MM-DD')
                    },
                    success: {
                        start: moment().startOf('month').format('YYYY-MM-DD'),
                        end: moment().endOf('month').format('YYYY-MM-DD')
                    },
                    failure: {
                        start: moment().startOf('month').format('YYYY-MM-DD'),
                        end: moment().endOf('month').format('YYYY-MM-DD')
                    }
                }

                $('select.dateFilterSelect').on('change', function(event){
                    if(/range/gi.test(this.value)){ 
                        $('.datepicker-wrap', $(this).parents('.tableFiltersWrap')).addClass('active');
                        $('.datepicker-wrap input', $(this).parents('.tableFiltersWrap')).focus().click();
                    }
                    else if($('.datepicker-wrap', $(this).parents('.tableFiltersWrap')).hasClass('active'))
                        $('.datepicker-wrap', $(this).parents('.tableFiltersWrap')).removeClass('active');
                });

                $('#leadsReport-tab-content select.dateFilterSelect').on('change', function(event){
                    let table;
                    switch ($(this).attr('data-table')) {
                        case 'failure': table = 'failure'; break;
                        case 'success': table = 'success'; break;
                        default: table = 'inProcess'; break;
                    }

                    if($(this).val() !== 'datesRange'){
                        switch (this.value) {
                        case 'lastWeek': 
                            ajaxData[table].start = moment().subtract(7, 'days').format('YYYY-MM-DD');
                            ajaxData[table].end = moment().format('YYYY-MM-DD');
                             break;
                        case 'currMonth':
                            ajaxData[table].start = moment().startOf('month').format('YYYY-MM-DD');
                            ajaxData[table].end  = moment().endOf('month').format('YYYY-MM-DD');
                            break;
                        case 'prevMonth': 
                            ajaxData[table].start = moment(moment().subtract(1, 'month')).startOf('month').format('YYYY-MM-DD');
                            ajaxData[table].end = moment(moment().subtract(1, 'month')).endOf('month').format('YYYY-MM-DD');
                            break;
                        default: // all
                            ajaxData[table].start = "1970-01-01";
                            ajaxData[table].end = moment().add(1, 'd').format('YYYY-MM-DD');
                            break;
                        } 
                    
                        switch (table) {
                            case 'inProcess': inProcessDataTableConfig.DataTable().ajax.reload(); break;
                            case 'success': successDataTableConfig.DataTable().ajax.reload(); break;
                            case 'failure': faliureDataTableConfig.DataTable().ajax.reload(); break;
                        }
                    }
                });

                // 
                if (window.location.search != "") {
                    if(getUrlParams('lastmonth')){
                        for (const [table] of Object.entries(ajaxData)){
                            ajaxData[table].start = moment().startOf('month').format('YYYY-MM-DD');
                            ajaxData[table].end = moment().endOf('month').format('YYYY-MM-DD');
                        }
                    }
                    else if(getUrlParams('today')){
                        for (const [table] of Object.entries(ajaxData)){
                            ajaxData[table].start = moment().format('YYYY-MM-DD');
                            ajaxData[table].end = moment().format('YYYY-MM-DD');
                        }
                    }
                }else{
                    for (const [table] of Object.entries(ajaxData)){
                        ajaxData[table].start = moment().startOf('month').format('YYYY-MM-DD');
                        ajaxData[table].end = moment().endOf('month').format('YYYY-MM-DD');
                    }
                }

                $('#activitiesTable_filter input').removeClass('form-control').addClass('form-control-custom');
                $('#activitiesTable_length select').removeClass('form-control').addClass('form-control-custom');


                const modal = $('#SendClientPush');
                const modalsClientIds = $('input[name*="clientsIds"]', modal);

                const agentModal = $('#AgentClientPush');
                const agentModalClientIds = $('input[name="newclientsIds"]', agentModal);

                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;



                // TABLE - IN PROCESS LEADS //
                const inProcessDataTableConfig = $('#leadsReportTable-inProcess').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    paging: true,
                    // scrollX: false,
                    // scrollY: "750px",
                    // scrollCollapse: true,
                    lengthMenu: [[10, 25, 50, 75, 100, 150, 200, 250, 300, 500, -1], [10, 25, 50, 75, 100, 150, 200, 250, 300, 500, "<?php echo lang('all') ?>"]],
                    pageLength: 50,
                    order: [[6, 'asc']],
                    columnDefs: [{'targets': [0], 'checkboxes': {'selectRow': true}, bSortable: false, orderable: false},],
                    select: {style: 'multi'},
                    dom: '<<"checkbox-buttons-container d-flex justify-content-between w-100" <rl><B>> t <"mt-10 d-flex justify-content-between  w-100" <p><i> >>',

                    <?php if (Auth::userCan('98')): ?>
                        
                    buttons: [ // ABOVE DATATABLE BUTTONs
                        {
                            text: lang('send_message_button') + ' <i class="far fa-paper-plane"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableInProcess.column(0).checkboxes.selected();
                                // var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                modalsClientIds.val(rows_selected.join(","));
                                modal.modal('show');
                            }
                        },
                        {
                            text: 'שייך לנציג <i class="fas fa-link"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableInProcess.column(0).checkboxes.selected();
                                // var newclientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                agentModalClientIds.val(rows_selected.join(","));
                                agentModal.modal('show');
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                    ],
                    <?php endif; ?>
                    ajax: {
                        url: 'LeadsJoinReportPost.php',
                        data: function (d) {
                            d.startDate = ajaxData.inProcess.start;
                            d.endDate = ajaxData.inProcess.end;
                            d.leadStatus = 0;
                        },
                        method: 'POST',
                    }
                });


                $('#leadsReport-inProcess .js-date-range').daterangepicker({
                    langauge: 'he',
                    locale: {
                        format: 'DD/MM/YY',
                        "applyLabel": "<?php echo lang('approval') ?>",
                        "cancelLabel": "<?php echo lang('cancel') ?>",
                    }
                }).on('apply.daterangepicker', function (ev, picker) {
                    ajaxData.inProcess.start = picker.startDate.format('YYYY-MM-DD');
                    ajaxData.inProcess.end = picker.endDate.format('YYYY-MM-DD');
                    inProcessDataTableConfig.DataTable().ajax.reload();
                });

                var tableInProcess = $('#leadsReportTable-inProcess').DataTable();
                tableInProcess.columns().every(function () {
                    var that = this;
                    $('input', this.footer()).on('keyup change', function () {
                        if (that.search() !== this.value) that.search(this.value).draw();
                    });
                });

                $('.branchFilter select').on('change', function () {
                    tableInProcess.column('4').search(this.value).draw();
                });

                $('.statusFilter select').on('change', function () {
                    tableInProcess.column('5').search(this.value).draw();
                });

                $('.incomingSourceFilter select').on('change', function () {
                    tableInProcess.column('8').search(this.value).draw();
                });

                // $('.representativeFilter select').on('change', function () {
                //     table.column('9').search(this.value).draw();
                // });

                const inProcess_clientsIds = [];
                inProcessDataTableConfig.on('xhr.dt', function (e, settings, json, xhr) {
                    $('#pending-count').text(json.data.length);
                    json.data = json.data.map(function (x) {
                        const data = [];
                        inProcess_clientsIds[x.clientId] = x.dataContent;
                        data.push(x.clientId);
                        data.push(x.clientFullName);
                        data.push(x.clientEmail);
                        data.push(x.clientPhone);
                        data.push(x.brandName);
                        data.push(x.pipeInfoTitle);
                        data.push(x.pipeStatusInfoTitle);
                        data.push(x.leadSource);
                        data.push(x.Dates);
                        data.push('<a href="javascript:void(0);" class="btnPopover" data-client-id="' + x.clientId + '" rel="popover" data-toggle="popover" data-html="true" data-placement="left">' + x.leadTasks);
                        data.push(x.ClientAct);
                        data.push(x.ClassStatus);
                        
                        return data;
                    });
                });

                tableInProcess.on('click', '.btnPopover', function (e) {
                    const clientIdKey = $(this).attr('data-client-id');
                    e.stopPropagation();
                    $(this).popover({
                        html: true,
                        trigger: 'manual',
                        content: inProcess_clientsIds[clientIdKey]
                    }).popover('toggle');
                    $('.btnPopover').not(this).popover('hide');
                });


                // TABLE - SUCCESS LEADS//
                const successDataTableConfig = $('#leadsReportTable-succeeded').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    paging: true,
                    // scrollX: true,
                    // scrollY: "750px",
                    // scrollCollapse: true,
                    lengthMenu: [[10, 25, 50, 75, 100, 150, 200, 250, 300, 500, -1], [10, 25, 50, 75, 100, 150, 200, 250, 300, 500, "<?php echo lang('all') ?>"]],
                    pageLength: 50,
                    order: [[6, 'asc']],
                    columnDefs: [{'targets': [0], 'checkboxes': {'selectRow': true}, bSortable: false, orderable: false},],
                    select: {style: 'multi'},
                    dom: '<<"checkbox-buttons-container d-flex justify-content-between w-100" <rl><B>> t <"mt-10 d-flex justify-content-between  w-100" <p><i> >>',

                    <?php if (Auth::userCan('98')): ?>
                        
                    buttons: [ // ABOVE DATATABLE BUTTONs
                        {
                            text: lang('send_message_button') + ' <i class="far fa-paper-plane"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableSuccess.column(0).checkboxes.selected();
                                // var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                modalsClientIds.val(rows_selected.join(","));
                                modal.modal('show');
                            }
                        },
                        {
                            text: 'שייך לנציג <i class="fas fa-link"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableSuccess.column(0).checkboxes.selected();
                                // var newclientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                agentModalClientIds.val(rows_selected.join(","));
                                agentModal.modal('show');
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                    ],
                    <?php endif; ?>
                    ajax: {
                        url: 'LeadsJoinReportPost.php',
                        data: function (d) {
                            d.startDate = ajaxData.success.start;
                            d.endDate = ajaxData.success.end;
                            d.leadStatus = 1;
                        },
                        method: 'POST',
                    }
                });


                
                $('#leadsReport-succeeded .js-date-range').daterangepicker({
                    langauge: 'he',
                    locale: {
                        format: 'DD/MM/YY',
                        "applyLabel": "<?php echo lang('approval') ?>",
                        "cancelLabel": "<?php echo lang('cancel') ?>",
                    }
                }).on('apply.daterangepicker', function (ev, picker) {
                    ajaxData.success.start = picker.startDate.format('YYYY-MM-DD');
                    ajaxData.success.end = picker.endDate.format('YYYY-MM-DD');
                    successDataTableConfig.DataTable().ajax.reload();
                });

                var tableSuccess = $('#leadsReportTable-succeeded').DataTable();
                tableSuccess.columns().every(function () {
                    var that = this;
                    $('input', this.footer()).on('keyup change', function () {
                        if (that.search() !== this.value) that.search(this.value).draw();
                    });
                    
                });

                $('.branchFilter select', '#leadsReportTable-succeeded').on('change', function () {
                    tableSuccess.column('4').search(this.value).draw();
                });

                $('.incomingSourceFilter select', '#leadsReportTable-succeeded').on('change', function () {
                    tableSuccess.column('8').search(this.value).draw();
                });

                tableSuccess.columns.adjust().draw();

                const success_clientsIds = [];
                successDataTableConfig.on('xhr.dt', function (e, settings, json, xhr) {
                    $('#succeeded-count').text(json.data.length);

                    json.data = json.data.map(function (x) {
                        const data = [];
                        success_clientsIds[x.clientId] = x.dataContent;
                        data.push(x.clientId);
                        data.push(x.clientFullName);
                        data.push(x.clientEmail);
                        data.push(x.clientPhone);
                        data.push(x.brandName);
                        data.push(x.leadSource);
                        data.push(x.Dates);
                        data.push(x.convertDates);
                        data.push('<a href="javascript:void(0);" class="btnPopover" data-client-id="' + x.clientId + '" rel="popover" data-toggle="popover" data-html="true" data-placement="left">' + x.leadTasks);
                        return data;
                    });
                });

                tableSuccess.on('click', '.btnPopover', function (e) {
                    const clientIdKey = $(this).attr('data-client-id');
                    e.stopPropagation();
                    $(this).popover({
                        html: true,
                        trigger: 'manual',
                        content: success_clientsIds[clientIdKey]
                    }).popover('toggle');
                    $('.btnPopover').not(this).popover('hide');
                });


            // TABLE - FALIURE LEADS //
            const faliureDataTableConfig = $('#leadsReportTable-failure').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    paging: true,
                    // scrollX: true,
                    // scrollY: "750px",
                    // scrollCollapse: true,
                    lengthMenu: [[10, 25, 50, 75, 100, 150, 200, 250, 300, 500, -1], [10, 25, 50, 75, 100, 150, 200, 250, 300, 500, "<?php echo lang('all') ?>"]],
                    pageLength: 50,
                    order: [[6, 'asc']],
                    columnDefs: [{'targets': [0], 'checkboxes': {'selectRow': true}, bSortable: false, orderable: false},],
                    select: {style: 'multi'},
                    dom: '<<"checkbox-buttons-container d-flex justify-content-between w-100" <rl><B>> t <"mt-10 d-flex justify-content-between  w-100" <p><i> >>',

                    <?php if (Auth::userCan('98')): ?>
                        
                    buttons: [ // ABOVE DATATABLE BUTTONs
                        {
                            text: lang('send_message_button') + ' <i class="far fa-paper-plane"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableFaliure.column(0).checkboxes.selected();
                                // var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                modalsClientIds.val(rows_selected.join(","));
                                modal.modal('show');
                            }
                        },
                        {
                            text: 'שייך לנציג <i class="fas fa-link"></i>',
                            className: 'dt-button btn btn-dark',
                            action: function (e, dt, node, config) {
                                rows_selected = tableFaliure.column(0).checkboxes.selected();
                                // var newclientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!rows_selected.length) return alert('<?php echo lang('select_customers') ?>');
                                agentModalClientIds.val(rows_selected.join(","));
                                agentModal.modal('show');
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code aria-hidden="true"></i>',
                            filename: '<?php echo $pageTitle?>',
                            className: 'dt-button btn btn-dark'
                        },
                    ],
                    <?php endif; ?>
                    ajax: {
                        url: 'LeadsJoinReportPost.php',
                        data: function (d) {
                            d.startDate = ajaxData.failure.start;
                            d.endDate = ajaxData.failure.end;
                            d.leadStatus = 2;
                        },
                        method: 'POST',
                    }
                });


                
                $('#leadsReport-failure .js-date-range').daterangepicker({
                    langauge: 'he',
                    locale: {
                        format: 'DD/MM/YY',
                        "applyLabel": "<?php echo lang('approval') ?>",
                        "cancelLabel": "<?php echo lang('cancel') ?>",
                    }
                }).on('apply.daterangepicker', function (ev, picker) {
                    ajaxData.failure.start = picker.startDate.format('YYYY-MM-DD');
                    ajaxData.failure.end = picker.endDate.format('YYYY-MM-DD');
                    faliureDataTableConfig.DataTable().ajax.reload();
                });

                var tableFaliure = $('#leadsReportTable-failure').DataTable();
                tableFaliure.columns().every(function () {
                    var that = this;
                    $('input', this.footer()).on('keyup change', function () {
                        if (that.search() !== this.value) that.search(this.value).draw();
                    });
                });

                $('.branchFilter select').on('change', function () {
                    tableFaliure.column('4').search(this.value).draw();
                });

                $('.incomingSourceFilter select').on('change', function () {
                    tableFaliure.column('8').search(this.value).draw();
                });


                const faliure_clientsIds = [];
                faliureDataTableConfig.on('xhr.dt', function (e, settings, json, xhr) {
                    $('#faliure-count').text(json.data.length);

                    json.data = json.data.map(function (x) {
                        const data = [];
                        faliure_clientsIds[x.clientId] = x.dataContent;
                        data.push(x.clientId);
                        data.push(x.clientFullName);
                        data.push(x.clientEmail);
                        data.push(x.clientPhone);
                        data.push(x.brandName);
                        data.push(x.leadSource);
                        data.push(x.Dates);
                        data.push(x.convertDates);
                        data.push(x.className);
                        data.push(x.classGuide);
                        data.push(x.failureReason);
                        return data;
                    });
                });

                tableFaliure.on('click', '.btnPopover', function (e) {
                    const clientIdKey = $(this).attr('data-client-id');
                    e.stopPropagation();
                    $(this).popover({
                        html: true,
                        trigger: 'manual',
                        content: faliure_clientsIds[clientIdKey]
                    }).popover('toggle');
                    $('.btnPopover').not(this).popover('hide');
                });
            }); 

            $('option', ".branchFilter, .incomingSourceFilter").each(function () {
                if(/^\s+$/.test(this.innerText)) $(this).remove();
            });
            // END OF DOCUMENT-READY

            $('body').on('click', function (e) {
                //did not click a popover toggle or popover
                if ($(e.target).data('toggle') !== 'popover'
                    && $(e.target).parents('.popover.in').length === 0) {
                    $('[data-toggle="popover"]').popover('hide');
                }
            });

        </script>

        <?php include('Reports/popupSendByClientId.php'); ?>
        <?php include('LeadsList/popupAgentByClientId.php'); ?>

    <?php else: ?>
        <?php redirect_to('../index.php'); ?>
    <?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

    <?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>