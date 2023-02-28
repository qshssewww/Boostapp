<?php
require_once '../../app/init.php';
require_once '../Classes/Brand.php';
require_once '../Classes/Users.php';

// secure page
if (!Auth::check())
    redirect_to('../../index.php');

$CompanyNum = Auth::user()->CompanyNum;
$ClassesType = new ClassesType();
$Brands = Brand::getBrandsByCompany($CompanyNum);
$ClassesType = $ClassesType->GetClassesTypeByCompanyNum($CompanyNum);
$Coaches = Users::getAllCoachesByCompanyNum($CompanyNum);
$ClassStatuses = ClassStatus::getAllStatusesInSystem();
$report = new StdClass();
$report->name = lang('reports_presence');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';


?>

    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
    <script src="../js/datatable/dataTables.checkboxes.min.js"></script>

<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

    <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<!--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link href="../assets/css/fixstyle.css?<?= filemtime("../assets/css/fixstyle.css") ?>" rel="stylesheet">
    <style>
        .bg-gray {
            background-color: #e9ecef;
        }

        .dataTables_scrollHead table {
            margin-bottom: 0px;
        }
    </style>
    <div class="text-end py-10 d-inline-flex ">
        <label class="mie-7 align-self-center font-weight-bold " for=""><?php echo lang('date_range') ?>:</label>
        <input type="text" name="date" class="form-control width-fit js-date-range">
    </div>
    <div class="row px-0">
        <div class="col-12 px-0">

            <div class="row m-0">

                <?php include("../ReportsInc/SideMenu.php"); ?>

                <div class="col-md-10 col-sm-12">
                    <div class="tab-content">
                        <div class="tab-pane fade show active " role="tabpanel" id="user-overview">
                            <div class="card spacebottom">
                                <div class="card-header text-start">
                                    <i class="fas fa-user-plus"></i>
                                    <strong>
                                        <?php echo $report->name ?>
                                    </strong>
                                </div>
                                <div class="card-body">

                                    <!-- page content -->

                                    <div >
                                        <table class="table table-hover dt-responsive  display wrap text-start" id="attendanceTable"
                                               cellspacing="0" width="100%">

                                            <thead>
                                                <tr class="">
                                                    <th class="text-start" ></th>
                                                    <th class="text-start" ><?php echo lang('client_name') ?></th>
                                                    <th class="text-start" ><?php echo lang('branch') ?></th>
                                                    <th class="text-start" ><?php echo lang('telephone') ?></th>
<!--                                                    <th class="text-start" >--><?php //echo lang('email_table_search') ?><!--</th>-->
                                                    <th class="text-start" ><?php echo lang('membership') ?></th>
                                                    <th class="" ><?php echo lang('class_single') ?></th>
                                                    <th class="text-start" ><?php echo lang('class_date') ?></th>

                                                    <th class="text-start" ><?php echo lang('class_time') ?></th>
                                                    <th class="text-start" ><?php echo lang('status') ?></th>
                                                    <th class="text-start lastborder" ><?php echo lang('instructor') ?></th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                                <tr class="">
                                                    <th class="text-start" ></th>
                                                    <th class="text-start" ><?php echo lang('client_name') ?></th>
                                                    <th class="text-start branchFilter" >
                                                        <select class="form-control">
                                                            <option value=""><?php echo lang('all') ?></option>
                                                            <?php
                                                            foreach ($Brands as $Brand) {
                                                                ?>
                                                                <option><?php echo $Brand->BrandName; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </th>
                                                    <th class="text-start" ><?php echo lang('telephone') ?></th>
<!--                                                    <th class="text-start" >--><?php //echo lang('email_table_search') ?><!--</th>-->
                                                    <th class="text-start" ><?php echo lang('membership') ?></th>
                                                    <th class="text-start classTypeFilter" >
                                                        <select class="form-control">
                                                            <option value=""><?php echo lang('all') ?></option>
                                                            <?php
                                                            foreach ($ClassesType as $ClassType) {
                                                                ?>
                                                                <option><?php echo $ClassType->Type; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </th>
                                                    <th class="text-start" ><?php echo lang('class_date') ?></th>

                                                    <th class="text-start" ><?php echo lang('class_time') ?></th>
                                                    <th class="text-start statusFilter" >
                                                        <select class="form-control">
                                                            <option value=""><?php echo lang('all') ?></option>
                                                            <?php
                                                            foreach ($ClassStatuses as $ClassStatusInfo) {
                                                                ?>
                                                                <option><?php echo $ClassStatusInfo->Title; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </th>
                                                    <th class="text-start lastborder guideFilter">
                                                        <select class="form-control">
                                                            <option value=""><?php echo lang('all') ?></option>
                                                            <?php
                                                            foreach ($Coaches as $Coach) {
                                                                ?>
                                                                <option><?php echo $Coach->display_name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </tfoot>

                                        </table>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<!--        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>-->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<!--        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>-->
        <style>
            .datepicker-dropdown {
                max-width: 300px;
            }

            .datepicker {
                float: right
            }

            .datepicker.dropdown-menu {
                right: auto
            }
        </style>
        <script>

            $(document).ready(function() {

                $('#attendanceTable tfoot th').each( function (index) {
                    var title = $(this).text();
                    // const arr = ["statusFilter", "classTypeFilter", "branchFilter"];
                    if(!$(this).hasClass("statusFilter") &&
                        !$(this).hasClass("branchFilter") &&
                        !$(this).hasClass("classTypeFilter") &&
                        !$(this).hasClass("guideFilter") && index > 0) {
                        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );
                    }

                } );

                var data = {
                    start: moment().subtract(7, 'd'),
                    end: moment()
                }

                var modal = $('#SendClientPush');
                var modalsClientIds = $('input[name="clientsIds"]', modal);
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var dataTable = $('#attendanceTable').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    paging: true,
                    scrollX: true,
                    scrollY: "450px",
                    scrollCollapse: true,
                    lengthMenu:  [[ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500, -1 ],[ 10, 25, 50, 75, 100, 150, 200, 250, 300, 500, "הכל" ]],
                    pageLength: 50,
                    order: [[6, 'asc']],
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

                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

                    <?php if (Auth::userCan('98')): ?>
                    buttons: [
                        {text: lang('send_message_button') + ' <i class="far fa-paper-plane"></i>', className: 'btn btn-sm btn-light text-gray-400 font-weight-bold ', action: function ( e, dt, node, config ) {
                                // rows_selected = table.column(0).checkboxes.selected();
                                var clientsIds = dt.column(0).checkboxes.selected().toArray();
                                if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');

                                modalsClientIds.val(clientsIds.join(","));
                                modal.modal('show');

                            }},
                        {extend: 'excelHtml5',  text: 'Excel <i class="far fa-file-excel aria-hidden="true"></i>', filename: '<?php echo lang('reports_presence') ?>', className: 'btn btn-sm btn-light text-gray-400 font-weight-bold mie-5'},
                        {extend: 'print', text: 'הדפסה <i class="far fa-print" aria-hidden="true"></i>', className: 'btn btn-sm btn-light text-gray-400 font-weight-bold ', customize: function ( win ) {
                                jQuery(win.document).ready(function(){
                                    $(win.document.body).css( 'direction', 'rtl')
                                });
                            }},
                    ],
                    <?php endif; ?>
                    ajax:{
                        url: 'attendancePost.php',
                        data: function(d) {
                            d.start = moment(data.start).format('YYYY-MM-DD'),
                                d.end = moment(data.end).format('YYYY-MM-DD'),
                                d.companyNum =  <?= $CompanyNum ?>
                        },
                        method: 'POST'
                    }
                });

                $('#activitiesTable_filter input').removeClass('form-control').addClass('form-control-custom');
                $('#activitiesTable_length select').removeClass('form-control').addClass('form-control-custom');

                $('.js-date-range').daterangepicker({
                    startDate: data.start,
                    endDate: data.end,
                    langauge: 'he',
                    locale: {
                        format: 'DD/MM/YY',
                        "applyLabel": "<?php echo lang('approval') ?>",
                        "cancelLabel": "<?php echo lang('cancel') ?>",
                    }
                }).on('apply.daterangepicker', function(ev, picker){
                    data.start = picker.startDate.format('YYYY-MM-DD');
                    data.end = picker.endDate.format('YYYY-MM-DD');
                    dataTable.DataTable().ajax.reload();
                });

                var table = $('#attendanceTable').DataTable();
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
                $('.statusFilter select').on('change', function(){
                    table.column('8').search(this.value).draw();
                });
                $('.classTypeFilter select').on('change', function(){
                    table.column('5').search(this.value).draw();
                });
                $('.branchFilter select').on('change', function(){
                    table.column('1').search(this.value).draw();
                });
                $('.guideFilter select').on('change', function(){
                    table.column('9').search(this.value).draw();
                });
            });
        </script>


        <!-- popupSendByClientId -->
        <?php include('./popupSendByClientId.php'); ?>


<?php
require_once '../../app/views/footernew.php';
?>