<?php
require_once '../app/init.php';
redirect_to('/office/SettingsDashboard.php');
// The page is hidden by request on BP-1844
require_once "Classes/Company.php";

$pageTitle = lang('reports_freeze');
if (!Auth::check())
    redirect_to('../index.php');

require_once '../app/views/headernew.php';
if (Auth::userCan('147')):

    $company = Company::getInstance(false);
    $companyNum = $company->__get("CompanyNum");
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

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<link href="assets/css/fixstyle.css" rel="stylesheet">
<style>
    th { font-size: 14px; }
    td { font-size: 14px; }
</style>
<div class="text-end py-10 d-inline-flex ">
    <label class="mie-7 align-self-center font-weight-bold " for=""><?php echo lang('date_range') ?>:</label>
    <input type="text" name="date" class="form-control width-fit js-date-range">
</div>
<div class="row text-start pie-15">
    <?php include_once "ReportsInc/SideMenu.php"; ?>
    <div class="col-md-10 height-fit py-20 shadow border-radius-10p ">
        <table id="activitiesTable" class="table borderless table-hover dt-responsive text-start display wrap" cellspacing="0" style="width:100%">
            <thead>
                <tr>
                    <th class="text-start"><?php echo lang('client_name') ?></th>
                    <th class="text-start"><?php echo lang('phone') ?></th>
                    <th class="text-start"><?php echo lang('subscribtion_name_membership') ?></th>
                    <th class="text-start"><?php echo lang('start_date') ?></th>
                    <th class="text-start"><?php echo lang('finish_date') ?></th>
                    <th class="text-start"><?php echo lang('freeze_start_date') ?></th>
                    <th class="text-start"><?php echo lang('freeze_end_date') ?></th>
                    <th class="text-start"><?php echo lang('freez_days') ?></th>
                    <th class="text-start"><?php echo lang('freez_reason_single') ?></th>
                    <th class="text-start"><?php echo lang('membership_price') ?></th>
                    <th class="text-start"><?php echo lang('price_per_month') ?></th>
                    <th class="text-start"><?php echo lang('membership_day_price') ?></th>
                    <th class="text-start lastborder"><?php echo lang('membership_freez_cost') ?></th>
                    
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr>
                    <th class="text-start"><?php echo lang('client_name') ?></th>
                    <th class="text-start"><?php echo lang('phone') ?></th>
                    <th class="text-start"><?php echo lang('subscribtion_name_membership') ?></th>
                    <th class="text-start"><?php echo lang('start_date') ?></th>
                    <th class="text-start"><?php echo lang('finish_date') ?></th>
                    <th class="text-start"><?php echo lang('freeze_start_date') ?></th>
                    <th class="text-start"><?php echo lang('freeze_end_date') ?></th>
                    <th class="text-start"><?php echo lang('freez_days') ?></th>
                    <th class="text-start"><?php echo lang('freez_reason_single') ?></th>
                    <th class="text-start"><?php echo lang('membership_price') ?></th>
                    <th class="text-start"><?php echo lang('price_per_month') ?></th>
                    <th class="text-start"><?php echo lang('membership_day_price') ?></th>
                    <th class="text-start lastborder"><?php echo lang('membership_freez_cost') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var data = {
            start: moment().startOf('month'),
            end: moment()
        }
	    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        var dataTable = $('#activitiesTable').dataTable({
            language: BeePOS.options.datatables,
            responsive: true,
            processing: true,
            lengthMenu: [10 ,25, 50, 100 ],
            pageLength: 25,

            dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
            // dom: 'lBfrtip',
            <?php if (Auth::userCan('98')): ?>    
            buttons: [
                {extend: 'excelHtml5',  text: 'Excel <i class="far fa-file-excel aria-hidden="true"></i>', filename: '<?php echo lang('reports_freeze') ?>', className: 'btn btn-sm btn-light text-gray-400 font-weight-bold mie-5'},
                {extend: 'print', text: '<?php echo lang('print') ?> <i class="far fa-print" aria-hidden="true"></i>', className: 'btn btn-sm btn-light text-gray-400 font-weight-bold ', customize: function ( win ) {
                    jQuery(win.document).ready(function(){
                        $(win.document.body).css( 'direction', 'rtl')
                    });                            
                }},
            ],
            <?php endif; ?>
            ajax:{
                url: 'freezReportPost.php',
                data: function(d) {
                    d.start = moment(data.start).format('YYYY-MM-DD'),
                    d.end = moment(data.end).format('YYYY-MM-DD')
                },
                method: 'POST'
            } 
        });

        $('#activitiesTable_filter input').removeClass('form-control').addClass('form-control-custom');
        $('#activitiesTable_length select').removeClass('form-control').addClass('form-control-custom');

        $('.js-date-range').daterangepicker({
            startDate: data.start,
            endDate: data.end,
            // locale: {
            //     format: 'DD/MM/YYYY'
            // }
        }).on('apply.daterangepicker', function(ev, picker){
            data.start = picker.startDate.format('YYYY-MM-DD');
            data.end = picker.endDate.format('YYYY-MM-DD');
            dataTable.DataTable().ajax.reload();
        });
    });
</script>
<?php
    require_once '../app/views/footernew.php';    
endif;
?>