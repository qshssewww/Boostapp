<?php
require_once '../../app/init.php';
require_once '../../app/views/headernew.php';

if (Auth::check()):
    if (Auth::userCan('149')):

        $report = new StdClass();
        $report->name = lang('time_clock');
        $pageTitle = lang('time_clock_report');
        require_once '../../app/views/headernew.php';

        $CompanyNum = Auth::user()->CompanyNum;
        CreateLogMovement('fas fa-chart-pie', lang('time_clock_report'), '0');
        $Dates = $_REQUEST["InputDate"] ?? date('Y-m');
        $Guide = $_REQUEST["Guide"][0] ?? DB::table('users')->where('CompanyNum', '=', $CompanyNum)->orderBy('display_name', 'ASC')->pluck('id');
        ?>


        <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>
        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

        <script>
            $(document).ready(function () {

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


                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var categoriesDataTable = $('#categories').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    "ordering": false,
                    ajax: {
                        url: 'signInClockPost.php?Guide=<?php echo $Guide; ?>&InputDate=<?php echo $Dates; ?>',
                        type: 'POST',
                    },
                    "paging": true,
                    pageLength: 100,
                    autoWidth: false, //step 1
                    columnDefs: [
                        {width: '20%', targets: [0, 3]},
                        {width: '30%', targets: [1, 2]}
                    ],
                    dom: '<<"d-flex justify-content-between w-100 mb-10 align-items-end" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: '<?php echo lang('reports_time_clock') ?>',
                            className: 'btn btn-light',
                            exportOptions: {}
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: '<?php echo lang('reports_time_clock') ?>',
                            className: 'btn btn-light',
                            exportOptions: {}
                        },
                        <?php endif ?>
                    ]
                });

                $('#categories').on('click', '.js-string-time', function () {
                    //console.log('should open save btn', $(this).parent().parent().parent().find('.fal.fa-save.fa-sm.text-success.d-none'));
                    $(this).parent().find('.d-none').removeClass('d-none').addClass('d-block');
                    $(this).removeClass('d-block').addClass('d-none');
                    $(this).parent().parent().parent().find('.js-savebtn').removeClass('d-none').text(lang('save_action'));

                });

                //add new entrance
                $('#categories').on('click', '.fal.fa-plus-circle.fa-xs.text-success', function () {
                    if ($(this).parent().parent().find('span.js-string-time').hasClass('js-studiouser-exit')) {
                        var inputTime = '<div><span class="d-none js-string-time js-studiouser-exit"> </span><span class = "js-input-time d-block"><input type="time" name="appt-time" value=""> <i class="fal fa-minus-circle fa-xs text-danger" role="button"></i> <i class="fal fa-plus-circle fa-xs text-success" role="button"></i></span></div>';
                        $(this).parent().parent().parent().prepend(inputTime);
                    } else {
                        var inputTime = '<div><span class="d-none js-string-time js-studiouser-entry"> </span><span class = "js-input-time d-block"><input type="time" name="appt-time" value=""> <i class="fal fa-minus-circle fa-xs text-danger" role="button"></i> <i class="fal fa-plus-circle fa-xs text-success" role="button"></i></span></div>';
                        $(this).parent().parent().parent().prepend(inputTime);
                    }
                });

                //delete entrance
                $('#categories').on('click', '.fal.fa-minus-circle.fa-xs.text-danger', function () {
                    if ($(this).parents('td').find('span.d-block').length == 1) {
                        $(this).parent().find('input').val('');
                        $(this).parent().parent().children().first('span.js-string-time').text('0').removeClass('d-none').addClass('d-block');
                        $(this).parent().removeClass('d-block').addClass('d-none');
                    } else {
                        $(this).parent().find('input').val('');
                        $(this).parent().removeClass('d-block').addClass('d-none');
                    }
                });

                //save
                $(document).on('click', '.js-savebtn', function () {
                    var currentRow = $(this);
                    var date = currentRow.parents('tr').find('td').eq('0').find('div').attr("data-value");
                    var guide = currentRow.parents('tr').find('td').eq('0').find('div').attr("data-guide");
                    var totalDayPointer = currentRow.parents('tr').find('td').eq('3');
                    var totalDay = totalDayPointer.text();
                    var totalMonth = $('#total').text();
                    var companyNum = currentRow.parents('tr').find('td').eq('0').find('div').attr("data-company");

                    var entriesArray = currentRow.parents('tr').find('td').eq('1').find('span.d-block').parent().find('input').map(function () {
                        if ($(this).val() != '') {
                            return $(this).val();
                        }
                    }).get();

                    var exitArray = currentRow.parents('tr').find('td').eq('2').find('span.d-block').parent().find('input').map(function () {
                        if ($(this).val() != '') {
                            return $(this).val();
                        }
                    }).get();
                    entriesArray.sort();
                    exitArray.sort();
                    $.post("../../ajax.php", {
                        date: date, guide: guide, companyNum: companyNum,
                        entries: entriesArray, exit: exitArray, totalDay: totalDay, totalMonth: totalMonth,
                        action: "signInReport"
                    }, function (data) {
                        totalDayPointer.text(data.message.totalDay);
                        $('#total').text(data.message.totalMonth);
                        currentRow.addClass('d-none');

                        //close each input with d-block, update the value of string from input
                        var inputList = currentRow.parents('tr').find('.d-block.js-input-time input');
                        if (inputList.length > 1) {
                            $(inputList).each(function (index, value) {
                                var time = $(this).val();
                                var stringTime = $(this).parent().siblings('span.js-string-time');
                                stringTime.text(time);
                                stringTime.removeClass('d-none').addClass('d-block');
                                $(this).parent().removeClass('d-block').addClass('d-none');
                            });
                        } else if (inputList.length == 1) {
                            // console.log(inputList[0].val());
                            var time = inputList[0].value == '' ? '0' : inputList[0].value;
                            var stringTime = inputList.parent().siblings('span.js-string-time');
                            stringTime.text(time);
                            stringTime.removeClass('d-none').addClass('d-block');
                            inputList.parent().removeClass('d-block').addClass('d-none');
                        }
                        var shine = currentRow.parents('tr').find('span.js-string-time');
                        shine.addClass('myGlower');
                        setTimeout(function () {
                            $('.myGlower').removeClass('myGlower');
                        }, 2000);
                    }, "json");
                });
            });


        </script>

        <link href="../assets/css/fixstyle.css" rel="stylesheet">

        <style>
            tbody tr.selected {
                color: white;
            }

            .myGlower {
                border-color: limegreen;
                -webkit-box-shadow: 0 0 5px limegreen;
                -moz-box-shadow: 0 0 5px limegreen;
                box-shadow: 0 0 5px limegreen;
            }

            .dataTables_info {
                display: none;
            }

        </style>

        <div class="row px-0 mx-0">
            <div class="col-12 px-0 mx-0">
                <div class="row">
                    <?php include_once "../ReportsInc/SideMenu.php"; ?>
                    <div class="col-md-10 col-sm-12">
                        <div class="tab-content">
                            <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                                <div class="card spacebottom">
                                    <div class="card-header text-start d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-user-tag"></i><strong> <?php echo lang('time_clock_report') ?></strong>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <form name="ThisForm" method="get">
                                                    <div class="row">

                                                        <div class="col-md-4 col-sm-12">
                                                            <div class="form-group row">
                                                                <label class="pis-15 align-self-end"><?php echo lang('instructor') ?></label>
                                                                <div class="col-sm-10">
                                                                    <select name="Guide[]" id="Guide"
                                                                            class="form-control selectAddItem"
                                                                            style="width:100%;"
                                                                            data-placeholder="<?php echo lang('select_coach') ?>">
                                                                        <option value=""></option>
                                                                        <?php
                                                                        $Activities = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->orderBy('display_name', 'ASC')->get();
                                                                        foreach ($Activities as $Activitie) {
                                                                            $selected = ($Activitie->id == $Guide) ? ' selected="selected"' : '';
                                                                            ?>
                                                                            <option value="<?php echo $Activitie->id ?>" <?php echo $selected; ?> ><?php echo $Activitie->display_name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group px-0">
                                                            <div class="col-sm-10 px-0">
                                                                <input type="month" class="form-control" id="InputDate"
                                                                       name="InputDate" value="<?php echo $Dates; ?>"
                                                                       placeholder="<?php echo lang('select_date') ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row text-start">
                                                        <div class="col-sm-12">
                                                            <button type="submit" class="btn btn-dark" id="MakeFile"
                                                                    name="MakeFile"
                                                                    value="MakeFile"><?php echo lang('extract_report_payroll') ?></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                                    <hr>-->
                                    <div class="row px-15">
                                        <table class="table table-hover dt-responsive text-start display wrap"
                                               id="categories" cellspacing="0" width="100%">

                                            <thead>
                                            <tr class="">
                                                <th><?php echo lang('date') ?></th>
                                                <th><?php echo lang('entrance') ?></th>
                                                <th><?php echo lang('exit_action') ?></th>
                                                <th><?php echo lang('total') ?></th>

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


        <style>

            .select2-results__option[aria-selected=true] {
                display: none;
            }
        </style>


        <script type="text/javascript" charset="utf-8">

            $(".selectAddItem").select2({
                theme: "bootstrap",
                placeholder: "Select a State",
                'language': "he",
                dir: "rtl"
            });

            $('#Class').on('select2:select', function (e) {
                var selected = $(this).val();

                if (selected != null) {
                    if (selected.indexOf('BA999') >= 0) {
                        $(this).val('BA999').select2({
                            theme: "bootstrap",
                            placeholder: "<?php echo lang('choose_class') ?>",
                            'language': "he",
                            dir: "rtl"
                        });
                    }
                }

            });

            $('#Guide').on('select2:select', function (e) {
                var selected = $(this).val();

                if (selected != null) {
                    if (selected.indexOf('BA999') >= 0) {
                        $(this).val('BA999').select2({
                            theme: "bootstrap",
                            placeholder: "<?php echo lang('select_coach') ?>",
                            'language': "he",
                            dir: "rtl"
                        });
                    }
                }

            });

            $('#InputDate').change(function () {
                $('#InputDate').val(this.value);
            });


        </script>


    <?php else: ?>
        <?php ErrorPage(lang('permission_blocked'), lang('no_page_persmission')); ?>
    <?php endif ?>

    <?php require_once '../../app/views/footernew.php'; ?>

<?php endif ?>


