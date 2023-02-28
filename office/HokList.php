<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

$pageTitle = lang('hok_title');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('21')): 

// CreateLogMovement('נכנס לניהול הוראות קבע', '0');

?>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">


<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>




<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>-->
<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<script src="./js/datatable/dataTables.checkboxes.min.js"></script>
<script src="/office/assets/js/onlineLibrary/confirm.js"></script>

<!--	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />




<div class="d-flex">
    <a href="javascript:;"  data-toggle="modal" data-target="#manageHoksModal" class="margin-a btn btn-outline-primary js-manageHoks"><?php echo lang('hok_title') ?></a>
</div>

<hr>
     
                 
<div class="row px-15"   id="pageContent">

    <?php include("ReportsInc/SideMenu.php"); ?>

    <div class="col-md-10 col-sm-12">


        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="charges-tab" data-toggle="tab" href="#charges" role="tab" aria-controls="charges" aria-selected="true"><?php echo lang('system_charges') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="status-tab" data-toggle="tab" href="#status" role="tab" aria-controls="status" aria-selected="true"><?php echo lang('direct_debit_status') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="withoutHok-tab" data-toggle="tab" href="#withoutHok" role="tab" aria-controls="withoutHok" aria-selected="false"><?php echo lang('non_direct_debit_clients') ?></a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="charges" role="tabpanel" aria-labelledby="charges-tab">
            <table class="table table-hover dt-responsive  display wrap text-start" id="dataTableCharges"  cellspacing="0" width="100%">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th></th>
                            <th><?php echo lang('date') ?></th>
                            <th><?php echo lang('client') ?></th>
                            <th><?php echo lang('product') ?></th>
                            <th><?php echo lang('summary') ?></th>
                            <th><?php echo lang('status') ?></th>
                            <th><?php echo lang('notes_two') ?></th>    
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <input type="text" placeholer="" name="dates" class="form-control">
                            </th>
                            <th>
                                <input type="text" placeholer="<?php echo lang('search_client') ?>" name="clientName" class="form-control">
                            </th>
                            <th>
                                <select name="itemIds" class="form-control" multiple="true"  size="1" style="width: 160px"></select>
                            </th>
                            <th>
                                <input type="number" placeholer="<?php echo lang('search_amount') ?>" name="amount" class="form-control">
                            </th>
                            <th>
                                <select name="statusIds" class="form-control" multiple="true"  size="1" style="width: 160px">
                                    <option value="" selected><?php echo lang('all') ?></option>
                                    <option value="0"><?php echo lang('future_single') ?></option>
                                    <option value="1"><?php echo lang('done_single') ?></option>
                                    <option value="2"><?php echo lang('failed_single') ?></option>
                                    <option value="4"><?php echo lang('lost_single') ?></option>
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    <thead>
                    <tbody></tbody>
                    <tfoot>
                    <tr class="">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>    
                        </tr>
                    </tfoot>
                    </table>     
            </div>

            <div class="tab-pane fade show" id="status" role="tabpanel" aria-labelledby="status-tab">
                <table class="table table-hover dt-responsive  display wrap text-start" id="dataTable"  cellspacing="0" width="100%">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th></th>
                            <th><?php echo lang('client') ?></th>
                            <th><?php echo lang('clearing_page') ?></th>
                            <th><?php echo lang('monthly_charge') ?></th>
                            <th><?php echo lang('payments') ?></th>
                            <th><?php echo lang('payments_balance') ?></th>
                            <th><?php echo lang('total') ?></th>
                            <th><?php echo lang('table_last_payment') ?></th>
                            <th><?php echo lang('table_next_payment') ?></th>
                            <th><?php echo lang('status') ?></th>
                            <!-- <th>ת. הוספה</th> -->
                            <th><?php echo lang('tools') ?></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <input type="text" name="clientName" placeholder="<?php echo lang('type_name') ?>" class="form-control">
                            </th>
                            <th>
                                <select class="form-control" name="paymentPage" multiple="true" size="1" style="width: 160px" placeholder="<?php echo lang('click_for_selection') ?>"></select>
                            </th>
                            <th>
                                <input type="number" min="0" name="monthlyPayment" placeholder="<?php echo lang('search_by_event_amount') ?>" class="form-control">
                            </th>
                            <th>
                                <select class="form-control" name="payments">
                                    <option value="" selected><?php echo lang('all') ?></option>
                                    <option value="permanent"><?php echo lang('permanent_single') ?></option>
                                    <option value="payment"><?php echo lang('payments') ?></option>
                                </select>
                            </th>
                            <th></th>
                            <th></th>
                            <th>
                            <input type="text" class="form-control" name="lastChargeDates">
                            </th>
                            <th>
                                <input type="text" class="form-control" name="nextChargeDates">
                            </th>
                            <th>
                                <select class="form-control" name="status">
                                    <option value="-1"><?php echo lang('all') ?></option>
                                    <option value="1" selected><?php echo lang('active') ?></option>
                                    <option value="0"><?php echo lang('canceled_two') ?></option>
                                </select>
                            </th>
                            <!-- <th></th> -->
                            <th></th>               
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <!-- <th></th> -->
                            <th></th>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <div class="tab-pane fade" id="withoutHok" role="tabpanel" aria-labelledby="withoutHok-tab">
                <table class="table table-hover dt-responsive  display wrap text-start" id="dataTableNoKeva"  cellspacing="0" width="100%">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th></th>
                            <th><?php echo lang('client') ?></th>
                            <th><?php echo lang('telephone') ?></th>
                            <th><?php echo lang('membership') ?></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <input type="text" placeholder="<?php echo lang('search_by_client') ?>" name="clientName" class="form-control">
                            </th>
                            <th>
                            <input type="text" placeholder="<?php echo lang('search_by_phone') ?>" name="clientPhone" class="form-control">
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
    <?php include_once 'Reports/popupSendByClientId.php'; ?>
</div>

<script>
(function($, BeePOS) {
    var dataTableCharges = jQuery('#dataTableCharges');
    var dataTableNoKeva = jQuery('#dataTableNoKeva');

    var filtersCharges = {
        clientName: jQuery('input[name="clientName"]', dataTableCharges),
        amount: jQuery('input[name="amount"]', dataTableCharges),
        itemIds: jQuery('select[name="itemIds"]', dataTableCharges),
        statusIds: jQuery('select[name="statusIds"]', dataTableCharges)
    }  
    
    filtersCharges.statusIds.select2({tags: true, placeholder: filtersCharges.statusIds.attr('placeholder'), width: 'resolve', dropdownAutoWidth : true});
    
    var filtersNoKeva = {
        clientName: jQuery('input[name="clientName"]', dataTableNoKeva),
        clientPhone: jQuery('input[name="clientPhone"]', dataTableNoKeva)
    }   

    var direction = false;

    if( $("html").attr("dir") == 'rtl' ){
        direction = true ;
    }


        $('[name="dates"]', dataTableCharges).on('keyup', function(e){
            if(e.which == 13) dataTableCharges.DataTable().ajax.reload();
        }).daterangepicker({
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            isRTL: direction,
            langauge: 'he',
            locale: {
                format: 'DD/M/YY',
                "applyLabel": "<?php echo lang('approval') ?>",
                "cancelLabel": "<?php echo lang('cancel') ?>",
            }
        }).on('apply.daterangepicker', function() {
            dataTableCharges.DataTable().ajax.reload();
        });

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };



    
    for(field in filtersCharges){
        switch(filtersCharges[field].get(0).tagName.toLowerCase()){
            case "select": 
            filtersCharges[field].on('change', function(){
                dataTableCharges.DataTable().ajax.reload();
                });
            break;
            case "input":
            filtersCharges[field].on('keyup', debounce(function(){
                dataTableCharges.DataTable().ajax.reload();
                }, 500));
            break;
        }
    }   

    for(field in filtersNoKeva){
        switch(filtersNoKeva[field].get(0).tagName.toLowerCase()){
            case "select": 
                filtersNoKeva[field].on('change', function(){
                    dataTableNoKeva.DataTable().ajax.reload();
                });
            break;
            case "input":
                filtersNoKeva[field].on('keyup', debounce(function(){
                    dataTableNoKeva.DataTable().ajax.reload();
                }, 500));
            break;
        }
    }

    var dateRangeCharges = $('[name="dates"]', dataTableCharges);

    var settingsCharges = {
        bInfo: false,
        orderCellsTop: true, // sorting only on first raw in thead
        language: BeePOS.options.datatables,
        responsive: true,
        processing: true,
        paging: false,
        // scrollY: "450px",
        scrollCollapse: false,
        dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
        buttons: [
            {text: lang('send_message_button') + ' <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                // rows_selected = table.column(0).checkboxes.selected();
                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');

                $('input[name="clientsIds"]', $('#SendClientPush')).val(clientsIds.join(","));
                    $('#SendClientPush').modal('show');

            }},
            <?php if (Auth::userCan('2')): ?>
            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>', className: 'btn btn-dark',exportOptions: {}},
            {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>', className: 'btn btn-dark',exportOptions: {}}
            <?php endif; ?>
        ],
        ajax: {
            url: '<?php echo get_loginboostapp_domain() ?>/api/client/bank/standingOrders',
            headers: {
                'x-cookie': document.cookie
            },
            method: 'GET',
            data: function(d) {
                var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                for(key in d) delete d[key];
                d.sort = sortKey;
                d.dir = sortDir;
                d.charges = 'true'
                d.filter = {};


                if(filtersCharges.clientName.val() != '') d.filter.clientName = filtersCharges.clientName.val()
                if(filtersCharges.amount.val() != '') d.filter.amount = filtersCharges.amount.val()
                if(filtersCharges.itemIds.val() != '') d.filter.itemIds = filtersCharges.itemIds.val()
                if(filtersCharges.statusIds.val() != '') d.filter.statusIds = filtersCharges.statusIds.val()
                if(dateRangeCharges.val() != ''){
                    d.filter.endDate = dateRangeCharges.data('daterangepicker').startDate._d;
                    d.filter.startDate = dateRangeCharges.data('daterangepicker').endDate._d;
                }
            }
        },
        serverSide: true,
        columns: [{
                "name": "select",
                bSortable: false
            },
            {
                "name": "date"
            },
            {
                "name": "clientName"
            },
            {
                "name": "product"
            },
            {
                "name": "amount"
            },
            {
                "name": "status"
            },
            {
                "name": "comments"
            }
        ],
        bFilter: false, // hide search field
        // bSort: true,
        // pageLength: 100,
        lengthChange: false,
        // lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
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

    dataTableCharges.dataTable(settingsCharges).on('xhr.dt', function(e, settings, json, xhr) {

    var api = dataTableCharges.dataTable().api();
    

    // var total = (([0].concat(json.items.filter(function(x){return x.status === 'בוצע'}).map(function(x){return x.amount}).filter(function(x){return x}))).reduce(function(total, sum){ return total+parseInt(sum)}));
    var total = (([0].concat(json.items.map(function(x){return x.amount}).filter(function(x){return x}))).reduce(function(total, sum){ return total+parseInt(sum)}));

    jQuery( api.column( 0 ).footer() ).html('סה"כ');
    jQuery( api.column( 1 ).footer() ).html(json.items.length);


    if(total){
        
        jQuery( api.column( 4 ).footer() ).html(

        '<div class="text-success">'+formatMoney(total, 2, ".", ",")+'</div>'
        // '<div class="text-danger">'+formatMoney((([0].concat(json.items.filter(function(x){return !x.status}).map(function(x){return x.monthlyPayment}).filter(function(x){return x}))).reduce(function(total, sum){ return total+parseInt(sum)})), 2, ".", ",")+'</div>'

        );
    }else{

        jQuery( api.column( 4 ).footer() ).html('');
    }

        json.data = json.items.map(function(x) {
            var data = [];

            data.push(x.clientId)
            data.push(moment(new Date(x.date)).format('DD/MM/YYYY'))
            data.push('<a href="./ClientProfile.php?u=' + (x.clientId || 0) +
                '">' + (x.fullName || '') + '</a>');

            data.push(x.product);
            data.push(x.amount);
            data.push(x.status);
            data.push(x.paymentStatus)

            return data
        });
        // json.draw = 1;
        json.recordsTotal = parseInt(json.items.length);
        json.recordsFiltered = parseInt(json.items.length);
    });

    var settingsNoKeva = {
        bInfo: false,
        orderCellsTop: true, // sorting only on first raw in thead
        language: BeePOS.options.datatables,
        responsive: true,
        processing: true,
        paging: false,
        // scrollY: "450px",
        scrollCollapse: false,
        dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
        buttons: [
            {text: lang('send_message_button')+' <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                // rows_selected = table.column(0).checkboxes.selected();
                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');

                $('input[name="clientsIds"]', $('#SendClientPush')).val(clientsIds.join(","));
                    $('#SendClientPush').modal('show');

            }},
            <?php if (Auth::userCan('2')): ?>
            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>', className: 'btn btn-dark',exportOptions: {}},
            {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>' , className: 'btn btn-dark',exportOptions: {}}
            <?php endif; ?>
        ],
        ajax: {
            url: '<?php echo get_loginboostapp_domain() ?>/api/client/bank/standingOrders',
            headers: {
                'x-cookie': document.cookie
            },
            method: 'GET',
            data: function(d) {
                var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                for(key in d) delete d[key];
                d.sort = sortKey;
                d.dir = sortDir;
                d.keva = 'false'
                d.filter = {};
                if(filtersNoKeva && filtersNoKeva.clientName && filtersNoKeva.clientName.val() != '') d.filter['clientName'] = filtersNoKeva.clientName.val();
                if(filtersNoKeva && filtersNoKeva.clientPhone && filtersNoKeva.clientPhone.val() != '') d.filter['clientPhone'] = filtersNoKeva.clientPhone.val();
              
                
                if(nextChargeDates && nextChargeDates.val() != ''){
                    d.filter['nextPaymentFrom'] = nextChargeDates.data('daterangepicker').startDate._d;
                    d.filter['nextPaymentTo'] = nextChargeDates.data('daterangepicker').endDate._d;
                }
            }
        },
        serverSide: true,
        columns: [{
                "name": "select",
                bSortable: false
            },
            {
                "name": "clientName"
            },
            {
                "name": "clientPhone"
            },
            {
                "name": "membership"
            }
        ],
        bFilter: false, // hide search field
        // bSort: true,
        // pageLength: 100,
        lengthChange: false,
        // lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
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

dataTableNoKeva.dataTable(settingsNoKeva).on('xhr.dt', function(e, settings, json, xhr) {

    json.data = json.items.map(function(x) {
        var data = [];

        data.push(x.clientId)
        data.push('<a href="./ClientProfile.php?u=' + (x.clientId || 0) +
            '">' + (x.clientName || '') + '</a>');

        data.push(x.clientPhone);
        data.push(x.meberships.map(function(x){return x.name + ' ('+x.mebershipType+')'}).join("<br>"))

        return data
    });
// json.draw = 1;
json.recordsTotal = parseInt(json.items.length);
json.recordsFiltered = parseInt(json.items.length);
});


    // with kva
     var table = $('#dataTable');
    var modal = $('#SendClientPush');
    var modalsClientIds = $('input[name="clientsIds"]', modal);
    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;

    var filters = {
        clientName: jQuery('input[name="clientName"]', table),
        paymentPage: jQuery('select[name="paymentPage"]', table),
        status: jQuery('select[name="status"]', table),
        monthlyPayment: jQuery('input[name="monthlyPayment"]', table),
        payments: jQuery('select[name="payments"]', table)      
    }

    var lastChargeDates = jQuery('input[name="lastChargeDates"]', table);
    var nextChargeDates = jQuery('input[name="nextChargeDates"]', table);

    var direction = false;

    if( $("html").attr("dir") == 'rtl' ){
        direction = true ;
    }

    try {
        lastChargeDates.on('keyup', function(e){
            if(e.which == 13) table.DataTable().ajax.reload();
        }).daterangepicker({
            startDate: moment().subtract(60, 'd'),
            endDate: moment(), //.endOf('month'),
            isRTL: direction,
            langauge: 'he',
            maxDate: moment(),
            locale: {
                format: 'DD/M/YY',
                "applyLabel": "<?php echo lang('approval') ?>",
                "cancelLabel": "<?php echo lang('cancel') ?>",
            }
        }).on('apply.daterangepicker', function() {
            table.DataTable().ajax.reload();
        }).val('');

        
    var direction = false;

    if( $("html").attr("dir") == 'rtl' ){
        direction = true ;
    }


        nextChargeDates.on('keyup', function(e){
            if(e.which == 13) table.DataTable().ajax.reload();
        }).daterangepicker({
            startDate: moment(),
            endDate: moment().add(30, 'd'), //.endOf('month'),
            isRTL: direction,
            langauge: 'he',
            minDate: moment(),
            locale: {
                format: 'DD/M/YY',
                "applyLabel": "<?php echo lang('approval') ?>",
                "cancelLabel": "<?php echo lang('cancel') ?>",
            }
        }).on('apply.daterangepicker', function() {
            table.DataTable().ajax.reload();
        }).val('');

    } catch (e) {
        console.log(e);
        throw e;
    }


        $.ajax({
            url: '<?php echo get_loginboostapp_domain() ?>/api/company/products',
            headers: {'x-cookie': document.cookie},
            method: 'GET',  
        }).done(function( data, textStatus, jqXHR ) {
            $(document).ready(function() {
                var select = filters.paymentPage;
                var charges = filtersCharges.itemIds;

                for (let index = 0; index < data.items.length; index++) {
                    select.append($('<option>', {value:data.items[index].id, text:data.items[index].name || '<?php echo lang('product_without_name ') ?>' + data.items[index].id}));     
                    charges.append($('<option>', {value:data.items[index].id, text:data.items[index].name || '<?php echo lang('product_without_name ') ?>'+data.items[index].id}));     
                }
                select.select2({tags: true, placeholder: select.attr('placeholder'), width: 'resolve', dropdownAutoWidth : true});
                charges.select2({tags: true, placeholder: select.attr('placeholder'), width: 'resolve', dropdownAutoWidth : true});

                
            })
        });

   


    for(field in filters){
        switch(filters[field].get(0).tagName.toLowerCase()){
            case "select": 
                filters[field].on('change', function(){
                    table.DataTable().ajax.reload();
                });
            break;
            case "input":
                filters[field].on('keyup', debounce(function(){
                    table.DataTable().ajax.reload();
                }, 500));
            break;
        }

    }



    var settings = {
        bInfo: false,
        orderCellsTop: true, // sorting only on first raw in thead
        language: BeePOS.options.datatables,
        responsive: true,
        processing: true,
        paging: false,
        // scrollY: "450px",
        scrollCollapse: false,
        dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
        buttons: [
            {text: lang('send_message_button')+' <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                // rows_selected = table.column(0).checkboxes.selected();
                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');

                $('input[name="clientsIds"]', $('#SendClientPush')).val(clientsIds.join(","));
                    $('#SendClientPush').modal('show');

            }},
            <?php if (Auth::userCan('2')): ?>
            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>', className: 'btn btn-dark',exportOptions: {}},
            {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('hok_report') ?>', className: 'btn btn-dark',exportOptions: {}}
            <?php endif; ?>
        ],
        ajax: {
            url: '<?php echo get_loginboostapp_domain() ?>/api/client/bank/standingOrders',
            headers: {
                'x-cookie': document.cookie
            },
            method: 'GET',
            data: function(d) {
                var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                for(key in d) delete d[key];
                d.sort = sortKey;
                d.dir = sortDir;

                d.filter = {}

                if(nextChargeDates.val() != ''){
                    d.filter['nextPaymentFrom'] = nextChargeDates.data('daterangepicker').startDate._d;
                    d.filter['nextPaymentTo'] = nextChargeDates.data('daterangepicker').endDate._d;
                }

                if(lastChargeDates.val() != ''){
                    d.filter['lastPaymentFrom'] = lastChargeDates.data('daterangepicker').startDate._d;
                    d.filter['lastPaymentTo'] = lastChargeDates.data('daterangepicker').endDate._d;
                }

                if(filters && filters.clientName && filters.clientName.val() != '') d.filter['clientName'] = filters.clientName.val();
                if(filters && filters.monthlyPayment && filters.monthlyPayment.val() != '') d.filter['monthlyPayment'] = filters.monthlyPayment.val();
                if(filters && filters.payments && filters.payments.val() != '') d.filter['payments'] = filters.payments.val();
                
                
                if(filters && filters.paymentPage && filters.paymentPage.val().length) d.filter['paymentPageIds'] = filters.paymentPage.val();
                if(filters && filters.status) d.filter['status'] = filters.status.val();

                
                
                
            }
        },
        serverSide: true,
        columns: [{
                "name": "select",
                bSortable: false
            },
            {
                "name": "clientName"
            },
            {
                "name": "paymentPage"
            },
            {
                "name": "monthlyPayment"
            },
            {
                "name": "payments"
            },
            {
                "name": "paymentPaids"
            },
            {
                "name": "grandTotal"
            },
            {
                "name": "lastPayment"
            },
            {
                "name": "nextPayment"
            },
            {
                "name": "status"
            },
            {
                "name": "entrenceDate"
            }
        ],
        bFilter: false, // hide search field
        // bSort: true,
        // pageLength: 100,
        lengthChange: false,
        // lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
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

function formatMoney(n, c, d, t) {
  var c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
    j = (j = i.length) > 3 ? j % 3 : 0;

  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

            table.dataTable(settings).on('xhr.dt', function(e, settings, json, xhr) {

                    var api = table.dataTable().api();
                    
                    

                    var total = (([0].concat(json.items.filter(function(x){return x.status}).map(function(x){return x.monthlyPayment}).filter(function(x){return x}))).reduce(function(total, sum){ return total+parseInt(sum)}));
                    if(total){
                        jQuery( api.column( 0 ).footer() ).html('<?php echo lang('total') ?>');
                        jQuery( api.column( 3 ).footer() ).html(

                        '<div class="text-success">'+formatMoney(total, 2, ".", ",")+'</div>'
                        // '<div class="text-danger">'+formatMoney((([0].concat(json.items.filter(function(x){return !x.status}).map(function(x){return x.monthlyPayment}).filter(function(x){return x}))).reduce(function(total, sum){ return total+parseInt(sum)})), 2, ".", ",")+'</div>'

                        );
                    }else{
                        jQuery( api.column( 0 ).footer() ).html('');
                        jQuery( api.column( 3 ).footer() ).html('');
                    }

                   
                    


                    json.data = json.items.map(function(x) {
                        var data = [];

                        data.push(x.clientId)
                        data.push('<a href="./ClientProfile.php?u=' + (x.clientId || 0) +
                            '">' + (x.fullName || '') + '</a>');

                        data.push(x.paymentPage);
                        data.push(x.monthlyPayment?formatMoney(x.monthlyPayment, 2, ".", ","):'-');
                        data.push(x.payments);
                        data.push(!x.paymentPaids || isNaN(x.paymentPaids)?'-':parseInt(x.payments)-parseInt(x.paymentPaids));
                        data.push(x.grandTotal?formatMoney(x.grandTotal, 2, ".", ","):'-');
                        data.push(x.lastPayment?moment(x.lastPayment).format('L'):'-');
                        data.push(x.nextPayment?moment(x.nextPayment).format('L'):'-');
                        data.push(x.status?'<span class="text-success">פעיל</span>':'<span class="text-danger">בוטל</span>');
                        // data.push(x.entrenceDate?moment(x.entrenceDate).format('L'):'-');
                        data.push('<a href=\'javascript:UpdatePayToken("'+x.id+'");\' title=\'עריכת הוראת קבע\'><i class="far fa-edit"></i></a>');
                        return data
                    });
                    // json.draw = 1;
                    json.recordsTotal = parseInt(json.items.length);
                    json.recordsFiltered = parseInt(json.items.length);
                });

})(jQuery, BeePOS)

</script>

 <!-- Edit DepartmentsPopup -->
	<div class="ip-modal " id="PayTokenEditPopup" tabindex="-1">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('recurring_payment_edit') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true">&times;</a>
                
				</div>
				<div class="ip-modal-body" >
                    <form action="EditPayToken"  class="ajax-form clearfix" autocomplete="off">
                    <input type="hidden" name="PayTokenId">

                    <div id="resultPayToken"></div>

                </div>
                <div class="ip-modal-footer">
                    <div class="ip-actions" id="ShowSaveKeva" style="display: none;">
                        <button type="submit" name="submit" class="btn btn-dark text-white"><?php echo lang('save_changes_button') ?></button>
                    </div>
                    
                        <button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                    </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->
    <div class="ip-modal text-right" id="manageHoksModal">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo lang('hok_title') ?></h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
                <p><?php echo lang('select_actions') ?></p>
                <form id="manageHoks" class="" autocomplete="off">
                <div class="row">
                    <div class="form-group px-20">
                        <input type="radio" name="hoksAction" value="change" checked>
                        <label for="changeHoksDate"><?php echo lang('postpone_recurring_payment') ?></label>
                    </div>
                    <div class="form-group px-20">
                        <input type="radio" name="hoksAction" value="cancel">
                        <label for="cancelHoks"><?php echo lang('cancel_recurring_payment') ?></label>
                    </div>

                </div>
                <div id="changeHoksDates">
                    <div class="alert alert-info" role="alert">
                    <?php echo lang('postpont_notice') ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">   
                            <div class="form-group">
                                <label><?php echo lang('number_single') ?></label>
                                <input type="number" min="1" name="reScheduleAmount" id="reScheduleAmount" class="form-control" required>   
                            </div> 
                        </div>     
                        <div class="col-md-4">	     
                            <div class="form-group">
                                <label><?php echo lang('search_by') ?></label>
                                <select name="timeType" id="timeType" class="form-control">
                                    <option value="1"><?php echo lang('days') ?></option>  
                                    <option value="2"><?php echo lang('months') ?></option>
                                </select>
                            </div>  
                        </div>
                    </div>
                </div>
                <div id="cancelHoks" style="display:none">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('select_cancel_action') ?></label>
                                <select name="cancelType" id="cancelType" class="form-control">
                                    <option value="1"> <?php echo lang('between_dates') ?></option>  
                                    <option value="2"><?php echo lang('permanent_cancellation') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="betweenDates">
                        <div class="col-md-3">	     
                            <div class="form-group">
                                <label><?php echo lang('from_date') ?></label>
                                <input name="fromDate" id="fromDate" type="date" min="<?php echo date('Y-m-d') ?>" value="<?php echo date('Y-m-d') ?>" class="form-control" required>     
                            </div>  
                        </div>
                        <div class="col-md-3">	     
                            <div class="form-group">
                                <label><?php echo lang('until_date') ?></label>
                                <input name="toDate" id="toDate" type="date" min="<?php echo date('Y-m-d') ?>"  value="<?php echo date('Y-m-d') ?>" class="form-control" required>   
                            </div>  
                        </div>
                    </div>
                    <div id="cancelAll" style="display: none">
                        <div class="alert alert-info" role="alert">
                        <?php echo lang('cancellation_notice') ?>
                        </div>
                    </div>
                </div>
                <div class="row px-15 form-group">
                    <!-- <div class="form-group px-15">
                        <input type="checkbox" name="typeKeva" id="typeKeva" value="1">
                        <label for="typeKeva"><?php //echo lang('include_recurring_notice') ?></label>
                    </div> -->
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="typeKeva" name="typeKeva" value="1">
                        <label class="custom-control-label" for="typeKeva"><?php echo lang('include_recurring_notice') ?></label>
                    </div>
                </div>
                <div class="row flex-d-col" id="successMsg-div" style="display: none">
                    <p class="margin-a text-primary"><?php echo lang('action_done') ?></p>
                    <p id="successMsg" class="margin-a text-primary"></p>
                </div>
                <div class="row flex-d-col" id="errorMsg-div" style="display: none">
                    <p class="margin-a text-danger"><?php echo lang('action_not_done') ?></p>
                    <p id="errorMsg" class="margin-a text-danger"></p>
                </div>
                <div class="ip-modal-footer py-10">
                    <div class="ip-actions">
                        <button type="submit" name="submit" id="loading-btn" class="btn btn-primary text-white"><?php echo lang('save_changes_button') ?> </button>
                        <!-- <button class="buttonload">
                        <i class="fad fa-circle-notch fa-spin"></i>Loading
                        </button> -->
                    </div>
                        <button type="button" class="btn btn-outline-dark ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                </div>
                </form>

			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->  
    
    <script>
        $(document).ready(function() {
            $("input[name='hoksAction']").on('change', function() {
                var input = $(this).val();
                if(input == 'cancel') {
                    $("#changeHoksDates").fadeOut("fast");
                    $("#cancelHoks").fadeIn("fast");
                    $('#reScheduleAmount').prop('required',false);
                } else {
                    $("#cancelHoks").fadeOut("fast");
                    $("#changeHoksDates").fadeIn("fast");
                    $('#reScheduleAmount').prop('required',true);
                }
            });

            $("select[name='cancelType']").on('change', function() {
                var input = $(this).val();
                if(input == '1') {
                    $("#cancelAll").fadeOut("fast");
                    $("#betweenDates").fadeIn("fast");
                    $('#fromDate').prop('required',true);
                    $('#toDate').prop('required',true);
                } else {
                    $("#betweenDates").fadeOut("fast");
                    $("#cancelAll").fadeIn("fast");
                    $('#fromDate').prop('required',false);
                    $('#toDate').prop('required',false);
                }
            });

            $("#manageHoks").on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                if (confirm("<?php echo lang('q_action_notice') ?>")) {
                    $("#loading-btn").addClass('disabled');
                    $("#loading-btn").append('<i class="fad fa-circle-notch fast-spin">');
                    $.ajax({
                        url: './ajax/updateHoks.php',
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            var data = JSON.parse(response);
                            $("#loading-btn").removeClass('disabled');
                            $("#loading-btn i").remove();
                            if(data.err == false) {
                                $('#successMsg').text(data.message);
                                $('#successMsg-div').show();
                                location.reload();
                            } else {
                                $('#errorMsg').text(data.message);
                                $('#errorMsg-div').show();
                                return;
                            }
                        },
                        error: function(response) {
                            $("#loading-btn").removeClass('disabled');
                            $("#loading-btn i").remove();
                            $('#errorMsg').text(data.message);
                            $('#errorMsg-div').show();
                            return;
                        }
                    });                    
                }
            });

        });
    </script>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>