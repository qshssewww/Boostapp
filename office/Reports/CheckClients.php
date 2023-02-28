<?php
require_once '../../app/init.php';
require_once "../Classes/Company.php";
// secure page
if (!Auth::check())
    redirect_to('../../index.php');

$report = new StdClass();
$report->name = lang('reports_expired_memberships');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';
$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");

$membershipType = DB::table('appsettings')->select('membershipType')->where("CompanyNum",'=',$companyNum)->first();

?>
    <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
    </script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js">
    </script>

    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js">
    </script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">
    </script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js">
    </script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
    </script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js">
    </script>

<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js">-->
    </script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
    </script>

    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js">
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js">
    </script>

    <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
    </script>
    <script src="../js/datatable/dataTables.checkboxes.min.js">
    </script>
    <link href="../assets/css/fixstyle.css" rel="stylesheet">
    <link href="../assets/css/check-clients.css" rel="stylesheet">


            <div class="row">
                <?php include("../ReportsInc/SideMenu.php"); ?>
                <div class="col-md-10 col-sm-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link pill-1 active cursor-pointer" data-target="#nav-exp-membership" id="nav-exp-membership-tab" data-toggle="tabajax" role="tooltip" aria-controls="nav-exp-membership" aria-selected="true">
                                <i class="far fa-users"></i>
                                <strong>
                                    <?php echo lang('reports_expired_membership_title') ?> (<span id="invalid-count"></span>)
                                </strong>
                            </a>

                            <a class="nav-item nav-link pill-2 cursor-pointer" data-target="#nav-exp-balance" id="nav-exp-balance-tab" data-toggle="tabajax" role="tooltip" aria-controls="nav-exp-balance" aria-selected="false">
                                <i class="far fa-users"></i>
                                <strong>
                                    <?php echo lang('balance_expire_checkclients') ?> (<span id="exp-balance-count"></span>)
                                </strong>
                            </a>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-exp-membership" role="tabpanel" aria-labelledby="nav-exp-membership-tab">
                            <div class="card spacebottom text-start">
                                <div class="card-body">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="ShowHideSubs" checked>
                                        <label class="custom-control-label" for="ShowHideSubs">
                                            <?php echo lang('show_hide_subs_checkclients') ?> <!--(<span id="subs-count"></span>)-->
                                        </label>
                                    </div>
<!--                                     <input type="checkbox" class="btn btn-dark text-white" id="ShowHideSubs" checked/><span>  אל תציג בדוח כניסות בודדת שנגמרו וטרם חודשו</span>-->

                                    <!-- page content -->
                                    <hr>
                                    <div class="row"  style="padding-left:15px; padding-right:15px;">
                                        <table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
                                            <thead class="thead-dark">

                                            <tr>
                                                <th class="text-start">#</th>
                                                <th class="text-start"><?php echo lang('client_name') ?></th>

                                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                                <th class="text-start"><?php echo lang('type') ?></th>
                                                <th class="text-start"><?php echo lang('membership') ?></th>
                                                <th class="text-start"><?php echo lang('expires_at') ?></th>
                                                <th class="text-start"><?php echo lang('classes') ?></th>
                                                <th class="text-start"><?php echo lang('actions') ?></th>
                                            </tr>
                                            </thead>

                                            <tbody >
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th><span>#</span></th>
                                                <th><span><?php echo lang('client_name') ?></span></th>

                                                <th><span><?php echo lang('telephone') ?></span></th>
                                                <th><span><?php echo lang('type') ?></span></th>
                                                <th><span><?php echo lang('membership') ?></span></th>
                                                <th><span><?php echo lang('expires_at') ?></span></th>
                                                <th><span><?php echo lang('classes') ?></span></th>
                                                <th></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="nav-exp-balance" role="tabpanel" aria-labelledby="nav-exp-balance-tab">
                            <div class="card spacebottom text-start">
                                <div class="card-body">
                                    <!-- page content -->
                                    <hr>

                                    <div class="row"  style="padding-left:15px; padding-right:15px;">

                                        <table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categoriesBalanceValidity"  cellspacing="0" width="100%">
                                            <thead class="thead-dark">
                                            <tr>
                                                <th class="text-start">#</th>
                                                <th class="text-start"><?php echo lang('client_name') ?></th>
                                                <th class="text-start"><?php echo lang('telephone') ?></th>
                                                <th class="text-start"><?php echo lang('type') ?></th>
                                                <th class="text-start"><?php echo lang('membership') ?></th>
                                                <th class="text-start"><?php echo lang('expires_at') ?></th>
                                                <th class="text-start"><?php echo lang('classes') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th><span>#</span></th>
                                                <th><span><?php echo lang('client_name') ?></span></th>
                                                <th><span><?php echo lang('telephone') ?></span></th>
                                                <th><span><?php echo lang('type') ?></span></th>
                                                <th><span><?php echo lang('membership') ?></span></th>
                                                <th><span><?php echo lang('expires_at') ?></span></th>
                                                <th><span><?php echo lang('classes') ?></span></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
<!--                <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
                <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--                <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
                <script src="../js/datatable/dataTables.checkboxes.min.js"></script>
                <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
                <script>
                    //var HideShow = false;
                    function InitInvildMemberShipPost(settings, json){
                        const SubsType =  $("#categories tbody .SubsType")

                        $(SubsType).each(function () {

                            let lineDisplay = false;
                            const child = $(this).children();
                            for( let i = 0; i < child.length ; i++) {
                                if ($(child[i]).hasClass('d-block'))
                                    lineDisplay = true;
                                    break;
                            }
                            if(!lineDisplay){
                                $(this).parent().addClass('d-none');
                            }

                        })
                        if(json!= undefined) {
                            $('#invalid-count').text(json.data.length);

                            // // subs count for full array
                            // let subs = 0;
                            // for (let line of json.data) {
                            //     let shouldCount = 1;
                            //     for (let field of line){
                            //         if (!field) continue;
                            //         if (field.includes('d-block')){
                            //             shouldCount = 0;
                            //             break;
                            //         }
                            //     }
                            //     subs += shouldCount;
                            // }
                            // $('#subs-count').text(subs);
                            // // end of subs count
                        } else {
                            $('#invalid-count').text(settings.data.length);
                        }
                    }

                    $(document).ready(function(){


                        $(document).on("click",".status.btn",function() {
                            var modalcode = $('#ViewDeskInfo');
                            modalcode.modal('show');
                            var ClassId = $(this).attr("data-user-id");
                            var url = '/office/action/GetSubscriptionLIstClient.php?Id='+ClassId;
                            $.ajax({
                                url:url,
                                type: 'POST',
                                success: function (data) {
                                    $("#ViewDeskInfo .ip-modal-body").html(data)
                                },
                                error: function(data){

                                }
                            });

                            $('#DivViewDeskInfo').load(url,function(e){

                                return false;
                            });

                        });
                        $('#ClosePOPUP').click(function () {
                            $("#ViewDeskInfo .ip-modal-body").html('');
                        })
                        $("#ShowHideSubs").on('click' ,function () {
                            // if(!HideShow){
                            //     $(this).text('הסתר מנויים');
                            //     HideShow= true;
                            // }
                            // else{
                            //     $(this).text('הצג מנויים');
                            //     HideShow= false;
                            // }

                            $("#categories .d-none").toggleClass('d-table-row');
                        });

                        $('#categories tfoot th span,#categoriesBalanceValidity tfoot th span,#categoriesClientwithoutSubscription tfoot th span').each( function () {
                            var title = $(this).text();
                            $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );
                        });
                        $.fn.dataTable.moment = function ( format, locale ) {
                            var types = $.fn.dataTable.ext.type;
                            // Add type detection
                            types.detect.unshift( function ( d ) {
                                return moment( d, format, locale, true ).isValid() ?
                                    'moment-'+format :
                                    null;
                            });
                            // Add sorting method - use an integer for the sorting
                            types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
                                return moment( d, format, locale, true ).unix();
                            };
                        };
                        $.fn.dataTable.moment( 'd/m/Y H:i' );
                        var buttons = []
                        var modal = $('#SendClientPush');
                        var modalsClientIds = $('input[name="clientsIds"]', modal);
                        if (<?php echo Auth::userCan('98') ? "true" : "false"; ?>) buttons.push({
                            text: lang('send_message_button') + ' <i class="fas fa-comments"></i>',
                            className: 'btn btn-dark',
                            action: function (e, dt, node, config) {
                                // rows_selected = table.column(0).checkboxes.selected();
                                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if (!clientsIds.length) return alert(lang('select_customers'));
                                modalsClientIds.val(clientsIds.join(","));
                                modal.modal('show');
                            }
                        })
                        if (<?php echo Auth::userCan('98')? "true" : "false"; ?>) buttons.push({
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: lang('reports_unregistration'),
                            className: 'btn btn-dark',
                            exportOptions: {}
                        })
                        if (<?php echo Auth::userCan('98')? "true" : "false"; ?>) buttons.push({
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: lang('reports_unregistration'),
                            className: 'btn btn-dark',
                            exportOptions: {}
                        })
                        var categoriesDataTable;
                        BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;                        
                        var categoriesDataTable = $('#categories').dataTable({
                            language: BeePOS.options.datatables,
                            responsive: true,
                            processing: true,
                            // autoWidth: true,
                            //"scrollY":        "450px",
                            //"scrollCollapse": true,
                            "paging": true,
                            //fixedHeader: {headerOffset: 50},
                            //  bStateSave:true,
                            // serverSide: true,
                            pageLength: 100,
                            lengthChange: true,
                            lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
                            dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                            //info: true,
                            buttons: buttons,
                            ajax: { url: '../InvildMemberShipPost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },
                            order: [[1, 'asc']],
                            columnDefs: [ {
                                targets: 5,
                                type: 'iso-date'
                            },{
                                targets: 0,
                                width: '26px',
                                checkboxes: {
                                    selectRow: true
                                },

                                bSortable: false,
                                ordering: false

                            }, {className: "SubsType", targets: 3}],

                            initComplete: InitInvildMemberShipPost
                        } );

                        var table = $('#categories').DataTable();


                        table.columns().every( function () {

                            var that = this;

                            $( 'input', this.footer() ).on( 'keyup change', function () {

                                if ( that.search() !== this.value ) {
                                    that
                                        .search( this.value )
                                        .draw();
                                }
                            });
                        });
                        var categoriesBalanceValidityDataTable =   $('#categoriesBalanceValidity').dataTable({
                            language: BeePOS.options.datatables,
                            responsive: true,
                            processing: true,
                            // autoWidth: true,
                            //"scrollY":        "450px",
                            //"scrollCollapse": true,
                            "paging":         true,
                            //fixedHeader: {headerOffset: 50},
                            // bStateSave:true,
                            // serverSide: true,
                            pageLength: 100,
                            lengthChange: true,
                            lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
                            dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                            //info: true,
                            buttons: buttons,
                            ajax: { url: '../expBalancePost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },
                            order: [[1, 'asc']],
                            columnDefs: [ {
                                targets: 6,
                                type: 'iso-date'
                            },{
                                targets: 0,
                                width: '10%',
                                checkboxes: {
                                    selectRow: true
                                },
                                bSortable: false,
                                ordering: false
                            }],
                            initComplete: function (settings, json) {
                                $('#exp-balance-count').text(json.data.length);
                            }
                        });
                        var table = $('#categoriesBalanceValidity').DataTable();
                        table.columns().every( function () {
                            var that = this;
                            $( 'input', this.footer() ).on( 'keyup change', function () {
                                if ( that.search() !== this.value ) {
                                    that
                                        .search( this.value )
                                        .draw();
                                }
                            });
                        });
                        //here
                        var categoriesBalanceValidityDataTable = $('#categoriesClientwithoutSubscription').dataTable({
                            language: BeePOS.options.datatables,
                            responsive: true,
                            processing: true,
                            // autoWidth: true,
                            //"scrollY":        "450px",
                            //"scrollCollapse": true,
                            "paging":         true,
                            //fixedHeader: {headerOffset: 50},

                             bStateSave:true,
                            // serverSide: true,
                            pageLength: 100,
                            lengthChange: true,
                            lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
                            dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                            //info: true,

                            buttons: buttons,

                            ajax: { url: '../InvalidSubscriptionsPost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },

                            order: [[1, 'asc']],
                            columnDefs: [ {
                                targets: 6,
                                type: 'iso-date'
                            },{
                                'targets': [0],
                                'checkboxes': {
                                    'selectRow': true
                                },
                                bSortable: false,
                                ordering: false
                            }]
                        });

                        var table = $('#categoriesClientwithoutSubscription').DataTable();

                        table.columns().every( function () {
                            var that = this;

                            $( 'input', this.footer() ).on( 'keyup change', function () {
                                if ( that.search() !== this.value ) {
                                    that
                                        .search( this.value )
                                        .draw();
                                }
                            } );



                        } );

                    });

                    _isoDateSort = function(a, b) {
                        var a = moment(a, 'DD/MM/YYYY').unix();
                        var b = moment(b, 'DD/MM/YYYY').unix();

                        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                    }

                    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                        "iso-date-asc": function (a, b) {
                            return _isoDateSort(a, b);
                        },
                        "iso-date-desc": function (a, b) {
                            return _isoDateSort(a, b) * -1;
                        }
                    });

                </script>

<!--                Tabs behaviour control                  -->
                <script>
                    $('[data-toggle="tabajax"]').click(function(e) {
                        const $this = $(this),
                            targ = $this.attr('data-target');

                        $this.tab('show');
                        window.location.hash = targ;
                        $('html,body').scrollTop(0);
                        return false;
                    });
                </script>
<!--                End of tabs behaviour control            -->

                <script>
                    var BeePOS = BeePOS || {};
                    BeePOS.options = BeePOS.options || {};
                    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                    BeePOS.options.datatables.processing = '<i class="fas fa-spinner fa-spin"></i> ' + BeePOS.options.datatables.processing
                    var boostAppDataTable = {
                        allowDebug: <?php echo Auth::user()->role_id == '1' ? 'true' : 'false'; ?>,
                        buttons: {
                            allowClientPush: true,
                            excel: <?php echo Auth::userCan('98')?"true":"false"; ?>,
                            csv: <?php echo Auth::userCan('98')?"true":"false"; ?>
                        }
                    };
                </script>
                <script src="./js/inactive.js"></script>


                <!-- popupSendByClientId -->
                <?php include('./popupSendByClientId.php'); ?>
            </div>
            <div class="modal text-start"  role="dialog" id="ViewDeskInfo" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="ip-modal-dialog BigDialog">
                    <div class="ip-modal-content">
                        <div class="ip-modal-header d-flex justify-content-between">
                            <h4 class="ip-modal-title"><?php echo lang('customer_subscription_checkclients') ?> </h4>
                            <a class="ip-close" title="Close" style="" data-dismiss="modal" aria-label="Close" id="ClosePOPUP">&times;</a>
                        </div>
                        <div class="ip-modal-body">

                        </div>
                    </div>
                </div>
            </div>
<?php
require_once '../../app/views/footernew.php';
?>