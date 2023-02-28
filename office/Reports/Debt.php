<?php require_once '../../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php if (Auth::check()):?>
<?php 
if (Auth::userCan('22')): 

$pageTitle = lang('reports_debt_title');
require_once '../../app/views/headernew.php';
?>



<div class="row pb-3">

<!-- <div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-industry"></i> דו״ח חייבים
</div>
</h3>
</div> -->


</div>
<div class="row mx-0 px-0 "   >
<div class="col-12 mx-0 px-0" >
    
    <div class="row">

    <?php include("../ReportsInc/SideMenu.php"); ?>

        <div class="col-md-10 col-sm-12">	
            <div class="tab-content">
                <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                    <div class="card spacebottom">
                        <div class="card-header text-start"><i class="fas fa-industry"></i><strong> <?php echo lang('reports_debt_title') ?></strong></div>    
                        <div class="card-body">
                        <!-- Content goes here -->
                        <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
                        <script src="//cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
                        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
                        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<!--                        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>-->
<!--                        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
                        <script src="../js/datatable/dataTables.checkboxes.min.js"></script>
                        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>



                        <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js">
                        <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js">
                        <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
                        <link rel="stylesheet" href="/office/assets/css/fixstyle.css">

                        <!-- <style>
                            .dt-buttons{text-align: left;}
                        </style> -->


                        <script>
                            (function($, BeePOS){
                                $(document).ready(function(){
                                    var settings = {
                                        language: {"emptyTable":"\u05dc\u05d0 \u05e0\u05de\u05e6\u05d0\u05d5 \u05e0\u05ea\u05d5\u05e0\u05d9\u05dd \u05d1\u05de\u05d0\u05d2\u05e8.","info":"\u05de\u05e6\u05d9\u05d2 _START_ \u05e2\u05d3 _END_ \u05de\u05ea\u05d5\u05da _TOTAL_ \u05e8\u05e9\u05d5\u05de\u05d5\u05ea","infoEmpty":"","infoFiltered":"","infoPostFix":"","thousands":",","lengthMenu":"\u05de\u05e6\u05d9\u05d2 _MENU_ \u05e8\u05e9\u05d5\u05de\u05d5\u05ea","loadingRecords":"\u05d8\u05d5\u05e2\u05df...","processing":"\u05de\u05e2\u05d1\u05d3 \u05e0\u05ea\u05d5\u05e0\u05d9\u05dd...","search":"\u05d7\u05d9\u05e4\u05d5\u05e9: ","zeroRecords":"\u05dc\u05d0 \u05e0\u05de\u05e6\u05d0\u05d5 \u05e0\u05ea\u05d5\u05e0\u05d9\u05dd \u05d1\u05de\u05d0\u05d2\u05e8.","paginate":{"first":"\u05e8\u05d0\u05e9\u05d5\u05df","last":"\u05d0\u05d7\u05e8\u05d5\u05df","next":"&rsaquo;","previous":"&lsaquo;"},"aria":{"sortAscending":": activate to sort column ascending","sortDescending":": activate to sort column descending"}},
                                        bPaginate: false,
                                        bInfo: false,
                                        responsive: true,
                                        processing: true,
                                        scrollX: true,
                                        scrollY: "650px",
                                        buttons: [
                                            {text: '<?php echo lang('send_message_button') ?> <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                                                // rows_selected = table.column(0).checkboxes.selected();
                                                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                                if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');

                                                $('input[name="clientsIds"]', $('#SendClientPush')).val(clientsIds.join(","));
                                                    $('#SendClientPush').modal('show');

                                            }},
                                            {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_debt_title') ?>', className: 'btn btn-dark',exportOptions: {}},
                                            {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_debt_title') ?>', className: 'btn btn-dark',exportOptions: {}},
                                            {extend: 'print', text: '<?php echo lang('print') ?> <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
                                                // https://datatables.net/reference/button/print
                                                jQuery(win.document).ready(function(){
                                                    $(win.document.body)
                                                    .css( 'direction', 'rtl' )
                                                });                            
                                            }},
                                        ],
                                        dom: 'fBrtip',


                                        ajax: {
                                            url: '<?php echo get_loginboostapp_domain() ?>/api/client/activity/debth',
                                            headers: {
                                                'x-cookie': document.cookie
                                            },
                                            method: 'GET',
                                            data: function(d){
                                                var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                                                var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                                                for(key in d) delete d[key];
                                                d.sort = sortKey;
                                                d.dir = sortDir;
                                            }
                                        },
                                        serverSide: true,
                                        columns: [{
                                                "name": "select",
                                                "sortable": false
                                            },
                                            {
                                                "name": "clientFullName",
                                                "sortable": true
                                            },
                                            {
                                                "name": "clientPhone",
                                                "sortable": true
                                            },
                                            {
                                                "name": "clientEmail",
                                                "sortable": true
                                            },
                                            {
                                                "name": "clientGender",
                                                "sortable": false
                                            },
                                            {
                                                "name": "clientJoined",
                                                "sortable": false
                                            },
                                            {
                                                "name": "clientDebt",
                                                "sortable": false
                                            },
                                            {
                                                "name": "clientStatus",
                                                "sortable": false
                                            }
                                        ],
                                        bFilter: false, // hide search field
                                        lengthChange: false,
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

                                    $('#categories').dataTable(settings).on('xhr.dt', function(e, settings, json, xhr){
                                        if(!json || !json.items) {
                                            json = [];
                                            json.items = [];
                                        }
                                        json.recordsTotal = parseInt(json.items.length);
                                        json.recordsFiltered = parseInt(json.items.length);

                                        var api = $('#categories').dataTable().api();
                                        jQuery( api.column( 0 ).footer() ).html('<?php echo lang('total') ?>');
                                        jQuery( api.column( 1 ).footer() ).html(json.items.length);
                                        if (json.items.length > 0) {
                                            jQuery(api.column(6).footer()).html((json.items.map(function (x) {
                                                return x.totalDebth
                                            }).reduce(function (total, num) {
                                                return parseFloat(total) + parseFloat(num)
                                            })).toFixed(2) + ' ₪');
                                        }


                                        json.data = json.items.map(function(x) {
                                            var data = [];

                                            data.push(x.clientId)
                                            data.push('<a href="../ClientProfile.php?u=' + (x.clientId || 0) +
                                                '">' + (x.fullName || '') + '</a>');

                                            data.push(x.phone);
                                            data.push(x.email);
                                            // data.push(x.bday?moment(x.bday).format('L'):'');
                                            data.push(x.gender);
                                            data.push(moment(x.joined).format('L'));
                                            data.push('<div class="text-danger" data-debth="'+encodeURIComponent(JSON.stringify(x))+'"><i class="fas fa-info-circle text-secondary"></i> '+(x.totalDebth || 0).toFixed(2)+' ₪</div>');
                                            data.push(x.status);

                                            return data
                                        });
                                    });
                                });

                                jQuery(document).on('click', '[data-debth]', function(){
                                    var data = JSON.parse(decodeURIComponent(jQuery(this).attr('data-debth')));
                                    var popupTemplate =
                                    '<div class="modal fade">' +
                                    '  <div class="modal-dialog modal-lg">' +
                                    '    <div class="modal-content"  style="text-align: right;">' +
                                    '      <div class="modal-header">' +
                                    '        <h4 class="modal-title"> '+ '<?php echo lang('reports_debt_ramain') ?>' +data.fullName+'</h4>' +
                                    '      </div>' +
                                    '      <div class="modal-body">' +
                                    '           <table class="table table-hover">' +
                                    '             <thead class="thead-dark">' +
                                    '               <tr>' +
                                    '                 <th>'+ '<?php echo lang('item_name') ?>' +'</th>' +
                                    '                 <th>' + '<?php echo lang('price') ?>' + '</th>' +
                                    '                 <th>' + '<?php echo lang('reports_debt_ramain') ?>' +  '</th>' +
                                    '                 <th>' + '<?php echo lang('payment_date') ?>' + '</th>' +
                                    '                 <th>' + '<?php echo lang('start_date') ?>' + '</th>' +
                                    '                 <th>' + '<?php echo lang('end_date') ?>' + '</th>' +
                                    '               </tr>' +
                                    '             </thead>' +
                                    '             <tbody>';
                                    for(var i=0; i < data.debth.length; i++){
                                        popupTemplate += '<tr>'+
                                    '                     <td>'+data.debth[i].name+'</td>' +
                                    '                     <td>'+data.debth[i].price+'</td>' +
                                    '                     <td>'+data.debth[i].debth+'</td>' +
                                    '                     <td>'+(data.debth[i].purchaseDate? moment(data.debth[i].purchaseDate).format('L') : '')+'</td>' +
                                    '                     <td>'+(data.debth[i].startDate? moment(data.debth[i].startDate).format('L') : '')+'</td>' +
                                    '                     <td>'+(data.debth[i].endDate? moment(data.debth[i].endDate).format('L') : '')+'</td>' +
                                    '                    </tr>';
                                    }


                                    popupTemplate += '             </tbody>' +
                                    '           </table>' +
                                    '      </div>' +
                                    '      <div class="modal-footer">' +
                                    '        <button type="button" class="btn btn-link" data-dismiss="modal">סגור</button>' +
                                    '      </div>' +
                                    '    </div>' +
                                    '  </div>' +
                                    '</div>'; 
                                    
                                    $(popupTemplate).modal().on('hidden.bs.modal',function(e){jQuery(this).remove();})
                                })
                            })(jQuery, BeePOS)
                        </script>



                        <table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
                            <thead>
                                <tr class="bg-dark text-white">
                                    <th></th>
                                    <th style="text-align:right;"><?php echo lang('client_name') ?></th>
                                    <th style="text-align:right;"><?php echo lang('telephone') ?></th>
                                    <th style="text-align:right;"><?php echo lang('email_table') ?></th>
                                    <th style="text-align:right;"><?php echo lang('gender') ?></th>  
                                    <th style="text-align:right;"><?php echo lang('join_date_table') ?></th>    
                                    <th style="text-align:right;"><?php echo lang('reports_debt_ramain') ?></th> 
                                    <th style="text-align:right;"><?php echo lang('status') ?></th>   
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                                <th></th>                     
                            </tfoot>
                        </table>
                        <!-- END Content goes here -->
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<?php include('./popupSendByClientId.php'); ?>

    
    
<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage (lang('permission_blocked'), lang('no_page_persmission')); ?>
<?php endif ?>


<?php endif ?>


<?php require_once '../../app/views/footernew.php'; ?>