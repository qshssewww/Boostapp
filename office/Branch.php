<?php
require_once '../app/init.php';
$pageTitle = lang('settings_branch_title');
require_once '../app/views/headernew.php';
require_once __DIR__.'/Classes/Brand.php';
require_once __DIR__.'/Classes/BranchGoogleAddress.php';
require_once __DIR__.'/Classes/247SoftNew/ClientGoogleAddress.php';

if (Auth::check() && Auth::userCan('151')) {


    $APIKey = BranchGoogleAddress::GOOGLE_API_KEY;
    $CompanyNum = Auth::user()->CompanyNum;
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

<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

    <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo $APIKey ?>&libraries=places&language=he"></script>
    <script src="/office/js/settingsDialog/settingsDialog.js"></script>
    <script src="/office/js/dashboard/branch.js"></script>

    <script>
        $(document).ready(function () {

            var dt_dom = '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>';


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
                types.order['moment-' + format + '-pre'] = function (d) {
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
                dom: dt_dom,
                //info: true,

                buttons: [
                    <?php if (Auth::userCan('98')): ?>
                    //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                    {
                        extend: 'excelHtml5',
                        text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                        filename: '<?php echo lang('settings_branch_title') ?>',
                        className: 'btn btn-dark'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                        filename: '<?php echo lang('settings_branch_title') ?>',
                        className: 'btn btn-dark'
                    },
                    // 'pdfHtml5'
                    <?php endif ?>

                ],

                //	order: [[0, 'DESC']]


            });


        });


    </script>


    <link href="assets/css/fixstyle.css" rel="stylesheet">

    <a href="javascript:;" data-ip-modal="#BranchPopup" id="js-newBranch"
       class="floating-plus-btn d-flex bg-primary"
       data-default='<?php
       // default address value from company settings
       $defaultAddress = ClientGoogleAddress::getBusinessAddress($CompanyNum);

       echo json_encode([
           'address' => $defaultAddress,
       ]);
       ?>'
       title="<?php echo lang('new_branch') ?>">
        <i class="fal fa-plus fa-lg margin-a"></i>
    </a>


    <div class="row">
        <?php include_once "SettingsInc/RightCards.php"; ?>

        <div class="col-md-10 col-sm-12 order-md-1">


            <div class="card spacebottom">
                <div class="card-header text-start">
                    <i class="fas fa-code-branch"></i> <b><?php echo lang('settings_branch_title') ?></b>
                </div>
                <div class="card-body text-start">


                    <table class="table table-bordered table-hover dt-responsive text-start display wrap"
                           id="categories" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                        <tr>
                            <th class="text-start">#</th>
                            <th class="text-start"><?php echo lang('branch') ?></th>
                            <th class="text-start"><?php echo lang('status') ?></th>
                            <th class="text-start lastborder"><?php echo lang('path_settings') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        $Items = Brand::getBrandsByCompany($CompanyNum);
                        foreach ($Items as $Item) {
                            $Address = BranchGoogleAddress::getAddressByBranchId($Item->id);
                            ?>
                            <tr>
                                <td class="text-start"><?= $i ?></td>
                                <td class="text-start"> <?= $Item->BrandName ?> </td>
                                <td class="align-middle"><?php if ($Item->Status == '0') {
                                        echo '<span class="text-dark"><i class="fa fa-eye"></i> ' . lang('active') . '</span>';
                                    } else {
                                        echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> ' . lang('hidden') . '</span>';
                                    } ?></td>
                                <td class="text-start"><a class="btn btn-success btn-sm text-white"
                                                          href='javascript:OpenBranchPopup("edit", <?= $i ?>, <?php
                                                          echo str_replace("'", "&#039;", json_encode([
                                                              'address' => $Address ?? $defaultAddress,
                                                              'branch' => $Item,
                                                          ]));
                                                          ?>);
                                    '><?= lang('edit_two') ?></a></td>
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
    <div class="modal fade px-0 px-sm-auto js-modal-no-close text-gray-700 text-start" tabindex="-1"
         id="js-branch-popup" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered bsapp-max-w-420p">
            <div class="modal-content h-100 rounded">
                <div class="modal-body p-0 bsapp-min-h-775p">

                    <!-- new/edit branch modal :: begin -->
                    <form class="modal-body d-flex flex-column justify-content-between p-0 h-100"
                          id="BranchPopupForm" method="post">
                        <div class="js-subpage-home h-100">
                            <!--    header    -->
                            <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
                                <div class="w-200p px-15 py-15">
                                            <span class="bsapp-fs-18 font-weight-bold"
                                                  id="BranchPopupTitle"><?= lang('add_new_branch_title') ?></span>
                                </div>

                                <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold"
                                   data-dismiss="modal">
                                    <i class="fal fa-times"></i>
                                </a>
                            </div>
                            <div class="bsapp-scroll overflow-auto bsapp-newclient-middle-height">
                                <input type="text" class="form-control d-none" name="BranchId" id="BranchId"
                                       value="">
                                <!--    name line    -->
                                <div class="d-flex px-15 mt-15">
                                    <div class="form-group flex-fill mb-10">
                                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('title_for_branch') ?></label>
                                        <div class="is-invalid-container">
                                            <input name="BranchName" id="BranchName"
                                                   placeholder="<?= lang('title_for_branch') ?>"
                                                   class="form-control border-light" type="text" required
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <!--    address line    -->
                                <div class="d-flex px-15">
                                    <div class="form-group flex-fill mb-10">
                                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('address') ?></label>
                                        <div class="is-invalid-container">
                                            <input type="text" class="form-control border-light"
                                                   name="BranchPlaceString" id="BranchPlaceString"
                                                   value="" placeholder="<?= lang('address') ?>">
                                            <input type="text" class="form-control d-none" name="BranchPlaceId"
                                                   id="BranchPlaceId"
                                                   value="" data-string="">
                                            <input type="text" class="form-control d-none"
                                                   name="BranchPlaceLatLng" id="BranchPlaceLatLng"
                                                   value="">
                                            <input type="text" class="form-control d-none"
                                                   name="BranchPlaceCity" id="BranchPlaceCity"
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                                <!--    status line    -->
                                <div class="d-none px-15" id="BranchStatusContainer">
                                    <div class="form-group flex-fill mb-10">
                                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('status_table') ?></label>
                                        <select class="form-control" name="BranchStatus" id="BranchStatus">
                                            <option value="0"><?= lang('active') ?></option>
                                            <option value="1"><?= lang('hidden') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--    footer    -->
                            <div class="d-flex justify-content-end border-top border-light px-15 py-15">
                                <button type="button" class="btn btn-outline-secondary mie-12 px-40"
                                        data-dismiss='modal'><?= lang('cancel') ?></button>
                                <button type="submit" name="submit"
                                        class="btn btn-dark px-40"><?= lang('save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(function () {

            var time = function () {
                return '?' + new Date().getTime()
            };

// Header setup
            $('#BranchPopup').imgPicker({});
// Header setup
            $('#EditBranchPopup').imgPicker({});

        });


    </script>
    <?php
    require_once '../app/views/footernew.php';
} else {
    redirect_to('../index.php');
}


