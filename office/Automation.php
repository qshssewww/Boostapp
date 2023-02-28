<?php 
require_once '../app/init.php';
$pageTitle = lang('automation_manage');
require_once '../app/views/headernew.php';

if (Auth::check()) { 
    if (Auth::userCan('143')) { 

        $CompanyNum = Auth::user()->CompanyNum;
        $Items = DB::table('automation')->where('CompanyNum', '=', $CompanyNum)->orderBy('Category', 'ASC')->orderBy('Status', 'ASC')->get();
        $resultcount = count($Items);


        $Category1 = DB::table('automation')->where('CompanyNum', '=', $CompanyNum)->where('Category', '=', '1')->where('Type', '=', '1')->count();
        $Category2 = DB::table('automation')->where('CompanyNum', '=', $CompanyNum)->where('Category', '=', '2')->where('Type', '=', '1')->count();
        $TotalCategory = $Category1 + $Category2;

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

        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>


        <script>
            $(document).ready(function() {

                $('#categories tfoot th span').each(function() {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');

                });



                $.fn.dataTable.moment = function(format, locale) {
                    var types = $.fn.dataTable.ext.type;

                    // Add type detection
                    types.detect.unshift(function(d) {
                        return moment(d, format, locale, true).isValid() ?
                            'moment-' + format :
                            null;
                    });

                    // Add sorting method - use an integer for the sorting
                    types.order['moment-' + format + '-pre'] = function(d) {
                        return moment(d, format, locale, true).unix();
                    };
                };

                $.fn.dataTable.moment('d/m/Y H:i');


                var categoriesDataTable;
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                var categoriesDataTable = $('#categories').dataTable({
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    // autoWidth: true,
                    //	        "scrollY":        "450px",
                    //            "scrollCollapse": true,
                    "paging": false,
                    fixedHeader: {
                        headerOffset: 50
                    },

                    //  bStateSave:true,
                    // serverSide: true,
                    pageLength: 100,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>' ,
                    //info: true,
                    <?php if (Auth::userCan('98')) { ?>
                    buttons: [
                            //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                            {
                                extend: 'excelHtml5',
                                text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                                filename: '<?php echo lang('automation_settings') ?>',
                                className: 'btn btn-dark',
                                exportOptions: {
                                    columns: [0, 1, 2]
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                                filename: '<?php echo lang('automation_settings') ?>',
                                className: 'btn btn-dark',
                                exportOptions: {
                                    columns: [0, 1, 2]
                                }
                            },
                            // 'pdfHtml5'

                    ],
                    <?php } ?>
                    //	order: [[0, 'DESC']]

                });



            });
        </script>



        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <!-- <div class="col-md-12 col-sm-12">
            <div class="row">



                <div class="col-md-5 col-sm-12 order-md-1">
                    <h3 class="page-header headertitlemain"  style="height:54px;">
                        <?php //echo $DateTitleHeader; ?>
                    </h3>
                </div>

                <div class="col-md-5 col-sm-12 order-md-3">
                    <h3 class="page-header headertitlemain"  style="height:54px;">
                        <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                            <i class="fas fa-magic"></i> הגדרות אוטומציה</span>
                        </div>
                    </h3>
                </div>


                <div class="col-md-2 col-sm-12 order-md-2 pb-1">
                    <?php //if ($TotalCategory != '2') { ?>
                        <a href="#" data-ip-modal="#AutomationPopup" class="btn btn-success btn-block" name="Items" ><i class="fas fa-plus-circle fa-fw"></i> הוסף אוטומציה חדשה</a>
                    <?php //} ?>
                </div>


            </div>

            <nav aria-label="breadcrumb" >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
                    <li class="breadcrumb-item active">הגדרות אוטומציה</li>
                </ol>
            </nav> -->

            <?php if ($TotalCategory != '2') { ?>
                <a href="javascript:;" data-ip-modal="#AutomationPopup" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('new_automation') ?>">
                    <i class="fal fa-plus fa-lg margin-a"></i>
                </a>
            <?php } ?>


            <div class="row">
                <?php include("SettingsInc/RightCards.php"); ?>

                <div class="col-md-10 col-sm-12">


                    <div class="card spacebottom">
                        <div class="card-header text-start" >
                            <i class="fas fa-magic"></i> <b><?php echo lang('automation_settings') ?></b>
                        </div>
                        <div class="card-body text-start" >


                            <table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-start">#</th>
                                        <th class="text-start"><?php echo lang('category_single') ?></th>
                                        <th class="text-start"><?php echo lang('action_type') ?></th>
                                        <th class="text-start"><?php echo lang('a_order') ?></th>
                                        <th class="text-start"><?php echo lang('status') ?></th>
                                        <th class="text-start lastborder"><?php echo lang('path_settings') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                            $i = 1;

                                            foreach ($Items as $Item) {

                                                if ($Item->Category == '1') {
                                                    $Category = lang('new_client');
                                                } else if ($Item->Category == '2') {
                                                    $Category = lang('a_new_lead');
                                                }

                                                if ($Item->Type == '1') {
                                                    $Type = lang('auto_add_membership');
                                                }

                                                $Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Item->Value)->first();
                                                ?>
                                        <tr>
                                            <td class="text-start"><?php echo $i ?></td>
                                            <td class="text-start"><?php echo $Category ?></td>
                                            <td class="text-start"><?php echo $Type ?></td>
                                            <td class="text-start"><?php echo $Activities->ItemName; ?></td>
                                            <td class="align-middle"><?php if ($Item->Status == '0') {
                                                                                        echo '<span class="text-dark"><i class="fa fa-eye"></i> '.lang('active').'</span>';
                                                                                    } else {
                                                                                        echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> '.lang('hidden').'</span>';
                                                                                    } ?></td>
                                            <td class="text-start"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateAutomation("<?php echo $Item->id; ?>");'><?php echo lang('edit_two') ?></a></td>
                                        </tr>



                                    <?php

                                                ++$i;
                                            } ?>


                                </tbody>


                            </table>


                        </div>
                    </div>

                </div>
            </div>

        </div>



        <!-- DepartmentsPopup -->
        <div class="ip-modal" id="AutomationPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="ip-modal-dialog BigDialog">
                <div class="ip-modal-content text-start">
                    <div class="ip-modal-header d-flex justify-content-between" >
                        <h4 class="ip-modal-title"><?php echo lang('set_new_automation') ?></h4>
                        <a class="ip-close" title="Close" >&times;</a>

                    </div>
                    <div class="ip-modal-body" >
                        <form action="AddAutomation" class="ajax-form clearfix">

                            <div class="form-group">
                                <label><?php echo lang('category_single') ?></label>
                                <select class="form-control" name="Category" id="Category" required>
                                    <option value=""><?php echo lang('choose') ?></option>
                                    <?php if ($Category1 == '0') { ?>
                                        <option value="1"><?php echo lang('new_client') ?></option>
                                    <?php }
                                            if ($Category2 == '0') {
                                                ?>
                                        <option value="2"><?php echo lang('a_new_lead') ?></option>
                                    <?php } ?>
                                </select>

                            </div>

                            <div class="form-group">
                                <label>סוג</label>
                                <select class="form-control" name="Type" required>
                                    <!-- <option value="">בחר</option> -->
                                    <option value="1"><?php echo lang('auto_add_membership') ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?php echo lang('select_membership') ?></label>
                                <select class="form-control" name="Value" required>
                                    <option value=""><?php echo lang('choose') ?></option>
                                    <?php
                                        $Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->whereIn('Department', array(1, 2, 3))->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();
                                        if (isset($_POST['value']) ) {
                                            echo "<script type='text/javascript'>alert('hereeee');</script>";
                                            if ($_POST['value'] == '2') {
                                                $message = "wrong answer";
                                                echo "<script type='text/javascript'>alert('$message');</script>";
                                                //var_dump($_POST['value']);
                                                $Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Department', '=', "3")->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();        
                                            }
                                        }
                                        foreach ($Activities as $Activitie) {
                                            $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Activitie->MemberShip)->first();
                                            if (!$membership_type) {
                                                continue;
                                            }

                                            if ($Activitie->MemberShip == 'BA999') {
                                                $Type = lang('no_membership_type');
                                            } else {
                                                $Type = $membership_type->Type;
                                            }


                                            if ($Activitie->Department == '1') {
                                                $department = lang('cycle_membership');
                                            } elseif ($Activitie->Department == '2') {
                                                $department = lang('class_tabe_card');
                                            } elseif ($Activitie->Department == '3') {
                                                $department = lang('a_trial');
                                            } elseif ($Activitie->Department == '4') {
                                                $department = lang('general_item');
                                            }


                                    ?>
                                    <option value="<?php echo $Activitie->id ?>"><?php echo $department ?> :: <?php echo $Type; ?> :: <?php echo $Activitie->ItemName; ?> - ₪<?php echo $Activitie->ItemPrice; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group" >
                                <label><?php echo lang('customer_card_validity_count') ?></label>
                                <select name="VaildType" id="VaildType" class="form-control" style="width:100%;" data-placeholder="בחר">
                                    <option value="0" selected><?php echo lang('customer_card_date_buy') ?></option>
                                    <option value="2"><?php echo lang('cusomer_card_validity_2') ?></option>
                                    <option value="5"><?php echo lang('cusomer_card_validity_4') ?></option>
                                </select>
                            </div>



                            <div class="form-group">
                                <label><?php echo lang('status') ?></label>
                                <select class="form-control" name="Status">
                                    <option value="0" selected><?php echo lang('active') ?></option>
                                    <option value="1"><?php echo lang('not_active') ?></option>
                                </select>
                            </div>

                    </div>
                    <div class="ip-modal-footer">
                        <div class="ip-actions">
                            <button type="submit" name="submit" class="btn btn-success"><?php echo lang('save_changes_button') ?></button>
                        </div>

                        <button type="button" class="btn btn-dark ip-close"><?php echo lang('close') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end DepartmentsPopup -->
        <script>
            $('#Category').on('change', function() {

                var selected = $("#Category option:selected").val();
                $.ajax({
                    url: 'Automation.php',
                    type: 'post',
                    data: {"value": selected },
                    success: function(response) {
                        //console.log(value);
                    }
                });
            });
        </script>

        <!-- Edit DepartmentsPopup -->
        <div class="ip-modal" id="EditAutomationPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="ip-modal-dialog BigDialog">
                <div class="ip-modal-content text-start">
                    <div class="ip-modal-header d-flex justify-content-between" >
                        <h4 class="ip-modal-title">עריכת אוטומציה</h4>
                        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>

                    </div>
                    <div class="ip-modal-body" >
                        <form action="EditAutomation" class="ajax-form clearfix">
                            <input type="hidden" name="ItemId">
                            <div id="result">

                            </div>

                    </div>
                    <div class="ip-modal-footer d-flex justify-content-between">
                        <div class="ip-actions">
                            <button type="submit" name="submit" class="btn btn-success"><?php echo lang('save_changes_button') ?></button>
                        </div>

                        <button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Edit DepartmentsPopup -->

        <?php require_once '../app/views/footernew.php'; ?>

        <script>
            $(function() {
                var time = function() {
                    return '?' + new Date().getTime()
                };

                // Header setup
                $('#AutomationPopup').imgPicker({});
                // Header setup
                $('#EditAutomationPopup').imgPicker({});

            });
        </script>

    <?php } else { 
        redirect_to('../index.php');  
    } 
}
?>


