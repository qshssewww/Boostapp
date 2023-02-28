<?php
require_once '../app/init.php';
$pageTitle = lang('report_tasks');
require_once '../app/views/headernew.php';

?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('138')): ?>





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

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.datepicker-dropdown {max-width: 300px;}
.datepicker {float: right}
.datepicker.dropdown-menu {right:auto}

    .add-task-btn{
        border:none;
    }
    .add-task-btn:active{
        border:none
    }
</style>




<script>
    $(document).ready(function () {
        let direction = false;

        if ($("html").attr("dir") == 'rtl') {
            direction = true;
        }

        $('#categories tfoot th span').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');

        });

        const fields = {
            date: $("input[name='date']"),
        };
        try {
            fields.date.daterangepicker({
                <?php if (@$_REQUEST["dateFrom"] != '' && @$_REQUEST["dateTo"] != '') { ?>
                startDate: moment('<?php echo @$_REQUEST["dateFrom"] ?>').format('DD/MM/YY'),
                endDate: moment('<?php echo @$_REQUEST["dateTo"] ?>').format('DD/MM/YY'),
                <?php } else { ?>
                startDate: moment(),
                endDate: moment(), //.endOf('month'),
                <?php } ?>
                isRTL: direction,
                langauge: 'he',
                locale: {
                    format: 'DD/M/YY',
                    "applyLabel": "<?php echo lang('approval') ?>",
                    "cancelLabel": "<?php echo lang('cancel') ?>",
                }
            }).on('apply.daterangepicker', function () {
//                  table.ajax.reload();
                window.location.href = "TaskReport.php?dateFrom=" + moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD') + "&dateTo=" + moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
            });
        } catch (e) {
            console.log(e);
        }

        $.fn.dataTable.moment = function (format, locale) {
            var types = $.fn.dataTable.ext.type;

            // Add type detection
            types.detect.unshift(function (d) {
                return moment(d, format, locale, true).isValid() ?
                    'moment-' + format :
                    null;
            });

            // Add sorting method - use an integer for the sorting
            types.order['moment-' + format + '-pre'] = function (d) {
                return moment(d, format, locale, true).unix();
            };
        };

        $.fn.dataTable.moment('d/m/Y H:i');

        $('#categories').on('click', '.task-popup-btn', (event) => {
            const id = $(event.target).data('id');
            if ($('#js-task-popup').length > 0) {
                handleNewTask(id);
            } else {
                NewCal(id, '')
            }
        });

        $('.add-task-btn').on('click', () => {
            if ($('#js-task-popup').length > 0) {
                handleNewTask();
            } else {
                NewCal();
            }
        });

        BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        $('#categories').dataTable({
            language: BeePOS.options.datatables,
            responsive: true,
            processing: true,
            "paging": false,
            dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
            buttons: [
                <?php if (Auth::userCan('98')): ?>
                {
                    extend: 'excelHtml5',
                    text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                    filename: '<?php echo lang('task_calendar') ?>',
                    className: 'btn btn-dark'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                    filename: '<?php echo lang('task_calendar') ?>',
                    className: 'btn btn-dark'
                },
                {
                    extend: 'print',
                    text: lang('print') + ' <i class="fas fa-print" aria-hidden="true"></i>',
                    className: 'btn btn-dark',
                    customize: function (win) {
                        jQuery(win.document).ready(function () {
                            $(win.document.body)
                                .css('direction', 'rtl')
                        });
                    }
                },
                <?php endif ?>
            ],
            ajax: {
                url: 'TaskPost.php',
                method: 'POST',
                data: function (d) {
                    d.dateFrom = moment(fields.date.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                    d.dateTo = moment(fields.date.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                }
            },
            order: [[3, 'asc']]
        });

    });

    const table = $('#categories').DataTable();
    table.columns().every(function () {
    var that = this;

    $('span input', this.footer()).on('keyup change', function () {
        if (that.search() !== this.value) {
            that
                .search(this.value)
                .draw();
        }
    });
});

$('#categories tfoot tr').insertAfter($('#categories thead tr'));
$('#table-filter').on('change', function () {
    table.column('7').search(this.value).draw();
});


$('#table-filterType').on('change', function () {
    table.column('4').search(this.value).draw();
});

$('#table-filterGroup').on('change', function () {
    table.column('6').search(this.value).draw();
});
</script>


<link href="assets/css/fixstyle.css" rel="stylesheet">
        <div class="col-md-12 col-sm-12">
            <button class="floating-plus-btn d-flex bg-primary add-task-btn"
                    title="<?php echo lang('new_task_button') ?>">
                <i class="fal fa-plus fa-lg margin-a"></i>
            </button>
        </div>

<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">


    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fas fa-calendar-alt"></i> <b><?php echo lang('reports_tasks') ?></b>
 	</div>
  	<div class="card-body">

<div class="row">
<div class="col-md-9 col-sm-12">

</div>
<div class="col-md-3 col-sm-12">

</div>
	</div>
<hr>


<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead >
			<tr class="bg-dark text-white">
				<th class="text-start"><?php echo lang('client') ?></th>
                <th class="text-start"><?php echo lang('telephone') ?></th>
                <th class="text-start"><?php echo lang('task_title') ?></th>
				<th class="text-start"><?php echo lang('date') ?></th>
				<th class="text-start"><?php echo lang('type') ?></th>
                <th class="text-start"><?php echo lang('representative') ?></th>
                <th class="text-start"><?php echo lang('perm_group') ?></th>
                <th class="text-start lastborder"><?php echo lang('status') ?></th>
			</tr>




		</thead>
		<tbody>


        </tbody>

<tfoot>
<tr class="bg-white text-black filterHeader">
                <th><span><?php echo lang('client') ?></span></th>
                <th><span><?php echo lang('telephone') ?></span></th>
                <th><span><?php echo lang('task_title') ?></span></th>
				<th><input id="table-filterDate" name="date" type="text" class="form-control" placeholder="<?php echo lang('search_single') ?>"></th>
				<th><select id="table-filterType" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<?php
$CalTypes = DB::table('caltype')->where('CompanyNum', '=', $CompanyNum)->where('Status', '0')->get();
foreach ($CalTypes as $CalType) {
?>
<option><?= htmlspecialchars($CalType->Type); ?></option>
<?php } ?>
</select></th>
                <th><span><?php echo lang('representative') ?></span></th>
<th>
<select id="table-filterGroup" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<?php
$Roles = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->get();
foreach ($Roles as $Role) {
?>
<option><?php echo $Role->name; ?></option>
<?php } ?>
</select>
</th>
<th class="lastborder"><select id="table-filter" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<option><?php echo lang('open_task') ?></option>
<option><?php echo lang('completed_task') ?></option>
<option><?php echo lang('canceled_task') ?></option>
</select></th>
            </tr>

</tfoot>

        </table>

        </div>
    </div>

	</div>
</div>

</div>






<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>