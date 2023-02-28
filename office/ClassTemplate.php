<?php
require_once '../app/init.php';
$pageTitle = lang('title_classtemplate');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('136')): ?>
        <?php
        $CompanyNum = Auth::user()->CompanyNum;
        $Items = DB::table('classstudio_date_template')->where('CompanyNum', '=', $CompanyNum)->orderBy('ClassName', 'ASC')->get();
        $resultcount = count($Items);


        CreateLogMovement(lang('classtemplate_log'), '0');
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
            $(document).ready(function () {

                $('#categories tfoot th span').each(function () {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="' + title + '" style="width:90%;" class="form-control"  />');

                });



                $.fn.dataTable.moment = function (format, locale) {
                    var types = $.fn.dataTable.ext.type;

                    // Add type detection
                    types.detect.unshift(function (d) {
                        return moment(d, format, locale, true).isValid() ?
                                'moment-' + format :
                                null;
                    });

                    // Add sorting method - use an integer for the sorting
                    types.order[ 'moment-' + format + '-pre' ] = function (d) {
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
                    "scrollY": "450px",
                    "scrollCollapse": true,
                    "paging": true,
                    fixedHeader: {
                        headerOffset: 50
                    },

                    //  bStateSave:true,
                    // serverSide: true,
                    pageLength: 100,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    //info: true,

                    buttons: [
        <?php if (Auth::userCan('98')): ?>
                            //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                            {extend: 'excelHtml5', text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: lang('settings_personal_training'), className: 'btn btn-dark', exportOptions: {columns: [0, 1, 2]}},
                            {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: lang('settings_personal_training'), className: 'btn btn-dark', exportOptions: {columns: [0, 1, 2]}},
                            // 'pdfHtml5'
        <?php endif ?>

                    ],

                    //	order: [[0, 'DESC']]


                });





            });


        </script>

        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <!-- <div class="col-md-12 col-sm-12">
        <div class="row">



        <div class="col-md-5 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain"  style="height:54px;">
        <?php //echo $DateTitleHeader;  ?>
        </h3>
        </div>

        <div class="col-md-5 col-sm-12 order-md-3">
        <h3 class="page-header headertitlemain"  style="height:54px;">
        <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
        <i class="fas fa-user-edit fa-fw"></i> אימונים אישיים <span style="color:#48AD42;"><?php //echo $resultcount;     ?> </span>
        </div>
        </h3>
        </div>

        <div class="col-md-2 col-sm-12 order-md-2 pb-1">
        <a href="javascript:void(0);" onclick="NewPrivateClass()" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> הוסף תבנית חדשה חדש</a>
        </div>


        </div>

        <nav aria-label="breadcrumb" >
          <ol class="breadcrumb">	
          <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
          <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
          <li class="breadcrumb-item active" aria-current="page">אימונים אישיים</li>
          </ol>  
        </nav>     -->

        <a href="javascript:;" onclick="NewPrivateClass()" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('new_template') ?>">
            <i class="fal fa-plus fa-lg margin-a"></i>
        </a>

        <div class="row">
            <?php include("SettingsInc/RightCards.php"); ?>

            <div class="col-md-10 col-sm-12">	


                <div class="card spacebottom">
                    <div class="card-header text-start d-flex justify-content-between" >
                        <div>
                            <i class="fas fa-user-edit"></i> <b><?php echo lang('settings_personal_training') ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span></b>
                        </div>
                    </div>    
                    <div class="card-body">       






                        <table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-start">#</th>
                                    <th class="text-start"><?php echo lang('template_title_class_template') ?></th>
                                    <th class="text-start"><?php echo lang('room_class_template') ?></th>
                                    <th class="text-start"><?php echo lang('instructor') ?></th>
                                    <th class="text-start"><?php echo lang('status_table') ?></th>
                                    <th class="text-start lastborder"><?php echo lang('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;

                                foreach ($Items as $Item) {
                                    $SectionInfos = '';
                                    $GuideInfos = '';
                                    if (property_exists($Item, 'Floor')):
                                        $SectionInfos = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Item->Floor)->first();
                                    endif;
                                    if (property_exists($Item, 'GuideId')):
                                        $GuideInfos = DB::table('users')->where('id', '=', $Item->GuideId)->first();
                                    endif;
                                    ?>        
                                    <tr>
                                        <td class="text-start"><?php echo $i ?></td>
                                        <td class="text-start"><?php echo property_exists($Item, 'ClassName') ? $Item->ClassName : ""; ?></td> 
                                        <td class="text-start"><?php echo property_exists($SectionInfos, 'Title') ? $SectionInfos->Title : ""; ?></td>   
                                        <td class="text-start"><?php echo property_exists($GuideInfos, 'display_name') ? $GuideInfos->display_name : ""; ?></td>       
                                        <td class="align-middle"><?php
                                            if ($Item->Status == '0') {
                                                echo '<span class="text-dark"><i class="fa fa-eye"></i> ' . lang('active') . '</span>';
                                            } else {
                                                echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> ' . lang('hidden') . '</span>';
                                            }
                                            ?></td>
                                        <td class="text-start"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:NewPrivateEditClass("<?php echo $Item->id; ?>");'><?php echo lang('edit_template') ?></a></td>
                                    </tr>



                                    <?php
                                    ++$i;
                                }
                                ?>       


                            </tbody>


                        </table> 

                    </div>
                </div>

            </div> 
        </div>

        </div>


        <!-- מודל שיעור חדש -->
        <div class="ip-modal text-start" role="dialog" id="AddNewPrivateClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl')     ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header d-flex justify-content-between "  <?php //_e('main.rtl')     ?>>
                        <h4 class="ip-modal-title">הוספת תבנית שיעור</h4>
                        <a class="ip-close ClassClosePopUp" title="Close"  data-dismiss="modal" aria-label="Close">&times;</a>

                    </div>
                    <div class="ip-modal-body">
                        <form action="AddClassNewPrivatePopUp" id="AddClassNewPrivatePop" class="ajax-form needs-validation" novalidate autocomplete="off">
                            <div id="ResultAddNewPrivateClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- מודל שיעור חדש -->


        <!-- מודל שיעור חדש -->
        <div class="ip-modal text-start" role="dialog" id="EditNewPrivateClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl')     ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header d-flex justify-content-between"  <?php //_e('main.rtl')     ?>>
                        <h4 class="ip-modal-title"><?php echo lang('add_class_template') ?></h4>
                        <a class="ip-close ClassClosePopUp" title="Close"  data-dismiss="modal" aria-label="Close">&times;</a>

                    </div>
                    <div class="ip-modal-body">
                        <form action="AddClassNewPrivatePopUp" id="EditClassNewPrivatePop" class="ajax-form needs-validation" novalidate autocomplete="off">
                            <div id="ResultEditNewPrivateClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- מודל שיעור חדש -->



    <?php else: ?>
        <?php redirect_to('../index.php'); ?>
    <?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

    <?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>