<?php
require_once __DIR__ . '/../app/init.php';
$pageTitle = lang('settings_notifications_title');
require_once __DIR__ . '/../app/views/headernew.php';
require_once __DIR__ . '/../office/Classes/Company.php';
require_once __DIR__ . '/../office/Classes/AppNotification.php';
require_once __DIR__ . '/../office/Classes/Notificationcontent.php';
require_once __DIR__ . '/Classes/Settings.php';
require_once __DIR__ . '/Classes/Item.php';
require_once __DIR__ . '/../app/enums/NotificationContent/SendOption.php';



if (Auth::check()) {
    if (Auth::userCan('17')) {
        $SettingsInfo = Company::getInstance();
        $companySettings = (new Settings())->getSettings(Auth::user()->CompanyNum);
        $Items = Notificationcontent::getContentsByCompanyNum();
        $resultcount = count($Items);


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

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

        <!-- include summernote css/js -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

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

                    //  bStateSave:true,
                    // serverSide: true,
                    pageLength: 100,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    //info: true,

                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        //{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
                        {
                            extend: 'excelHtml5',
                            text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: '<?php echo lang('settings_notifications_template') ?>',
                            className: 'btn btn-dark',
                            exportOptions: {columns: [0, 1, 2, 3]}
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                            filename: '<?php echo lang('settings_notifications_template') ?>',
                            className: 'btn btn-dark',
                            exportOptions: {columns: [0, 1, 2, 3]}
                        }
                        // 'pdfHtml5'
                        <?php endif ?>

                    ],

                    //	order: [[0, 'DESC']]


                });


            });


        </script>

        <link href="assets/css/fixstyle.css" rel="stylesheet">

        <?php
            include_once __DIR__ . '/savedNotificationList/savedNotificationListSettings.php';
            echo '<div class="pb-15 text-end"><a href="javascript:;" class="btn btn-outline-gray-300 text-dark" onclick="savedNotificationList.settingsVisibility(false)"><i class="fal fa-cog"></i></a></div>';
        ?>
        <div class="row">
            <?php include("SettingsInc/RightCards.php"); ?>

            <div class="col-md-10 col-sm-12">


                <div class="card spacebottom">
                    <div class="card-header text-start d-flex justify-content-between ">
                        <div><i class="fas fa-bell"></i> <b><?= lang('settings_notifications_template') ?> <span
                                        class="text-primary"><?= $resultcount; ?> </span></b></div>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-hover dt-responsive text-start display wrap"
                               id="categories" cellspacing="0" width="100%">
                            <thead class="thead-dark">
                            <tr>
                                <th class="text-start">#</th>
                                <th class="text-start"><?= lang('internal_title') ?></th>
                                <th class="text-start"><?= lang('notification_type') ?></th>
                                <th class="text-start"><?= lang('status') ?></th>
                                <th class="text-start d-none"><?= lang('contet_single') ?></th>
                                <?php if (Auth::userCan('18')): ?>
                                    <th class="text-start lastborder"><?= lang('edit') ?></th>
                                <?php endif ?>
                                <?php if (Auth::userCan('18')): ?>
                                    <th class="text-start lastborder"><?= lang('path_settings') ?></th>
                                <?php endif ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $i = 1;

                            $isMemberships = Item::where('CompanyNum', Auth::user()->CompanyNum)
                                ->where('isPaymentForSingleClass', 0)
                                ->whereIn('Department', [1, 2])
                                ->where('Status', 0)
                                ->where('Disabled', 0)
                                ->count();

                            foreach ($Items as $Item) {

                                if($Item->Type == 24 || (in_array($Item->Type, [7, 8, 9, 10 ,19 ,20 ,25]) && $isMemberships == 0)) {
                                    continue;
                                }

                                $alertTypeMail = false;
                                $alertTypeSms = false;
                                $alertTypePush = false;
                                $alertTypeWhatsapp = false;

                                $notificationTypeFilter = 0;

                                $sendOptions = explode(',', $Item->SendOption);

                                foreach ($sendOptions as $sendOption) {
                                    switch ($sendOption) {
                                        case 'BA999':
                                            $alertTypeMail = true;
                                            $alertTypeSms = true;
                                            $alertTypePush = true;

                                            $notificationTypeFilter += 3;
                                            break;
                                        case 'BA000':
                                            break;
                                        case '0':
                                            $alertTypePush = true;
                                            break;
                                        case '1':
                                            $notificationTypeFilter += 1;
                                            $alertTypeSms = true;
                                            break;
                                        case '2':
                                            $notificationTypeFilter += 2;
                                            $alertTypeMail = true;
                                            break;
                                        case '4':
                                            $notificationTypeFilter += 4;
                                            $alertTypePush = true;
                                            $alertTypeWhatsapp = true;
                                            break;
                                    }
                                }

                                $StatusColor = $Item->Status == 0 ? 'success' : 'danger';

                                $notificationContent = Notificationcontent::generateBtnsFromContent($Item->Content);
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $Item->TypeName ?></td>
                                    <td class="text-start text-secondary">
                                        <span class="d-none"><?= $notificationTypeFilter ?></span>
                                        <i style="font-size: 20px" class="px-6 fal fa-envelope <?= $alertTypeMail ? 'text-success' : '' ?>"></i>
                                        <i style="font-size: 20px" class="px-6 fal fa-sms <?= $alertTypeSms ? 'text-success' : '' ?>"></i>
                                        <i style="font-size: 20px" class="px-6 fal fa-bell <?= $alertTypePush ? 'text-success' : '' ?>"></i>
                                        <?php if ($alertTypeWhatsapp) { ?>
                                            <i style="font-size: 20px" class="px-6 fab fa-whatsapp text-success"></i>
                                        <?php } ?>
                                    </td>
                                    <td class="text-start text-<?= $StatusColor ?>"><i class="fas fa-circle"></i><span class="d-none"><?= $StatusColor ?></span></td>
                                    <td class="d-none" style="font-size: 12px;">
                                        <div style="height: 100px;overflow: hidden;width: 200px;"><?= $Item->Subject ?>
                                            <hr style="margin: 0;padding: 0;"><?= $notificationContent ?></div>
                                        <strong><?= lang('display_next') ?></strong></td>
                                    <?php if (Auth::userCan('18')): ?>
                                        <td class="text-start">
                                            <i onclick='UpdateSavedNot("<?= $Item->id; ?>")'
                                               style="cursor:pointer; font-size: 18px" class="fal fa-edit <?= $Item->SendOption == SendOption::SEND_OPTION_WHATSAPP ? 'disabled' : '' ?>">
                                            </i>
                                        </td>
                                    <?php endif ?>
                                    <?php if (Auth::userCan('18')): ?>
                                        <td class="text-start">
                                            <i onclick='UpdateSettingsNotification("<?= $Item->id; ?>")' style="cursor:pointer; font-size: 18px" class="fal fa-cog"></i>
                                        </td>
                                    <?php endif ?>

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


        <!-- Edit DepartmentsPopup -->
        <div class="ip-modal" id="NotEditPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="ip-modal-dialog BigDialog">
                <div class="ip-modal-content text-start d-flex justify-content-between flex-column ">
                    <div class="ip-modal-header d-flex flex-row justify-content-between">
                        <h4 class="ip-modal-title"><?php echo lang('edit_notification_template') ?></h4>
                        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true">&times;</a>

                    </div>
                    <div class="ip-modal-body">


                        <form action="EditSavedNot" class="ajax-form clearfix">
                            <input type="hidden" name="ItemId">
                            <div id="result">


                            </div>

                    </div>
                    <div class="ip-modal-footer  d-flex justify-content-between">
                        <button type="button" class="btn btn-light ip-close"
                                data-dismiss="modal"><?php echo lang('close') ?></button>
                        <div class="ip-actions">
                            <button type="submit" name="submit"
                                    class="btn btn-success"><?php echo lang('save_changes_button') ?></button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Edit DepartmentsPopup -->


        <!-- Edit DepartmentsPopup -->
        <div class="ip-modal" id="UpdateSettingsNotificationPopup" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">
            <div class="ip-modal-dialog BigDialog">
                <div class="ip-modal-content text-start">
                    <div class="ip-modal-header d-flex justify-content-between">
                        <h4 class="ip-modal-title "><?php echo lang('edit_notification_settings') ?></h4>
                        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true">&times;</a>

                    </div>
                    <div class="ip-modal-body">
                        <form id="js--notification-settings-form" class="clearfix">
                            <input type="hidden" name="ItemId">
                            <div id="resultSettingsNotification">


                            </div>

                    </div>
                    <div class="ip-modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-light ip-close"
                                data-dismiss="modal"><?php echo lang('close') ?></button>
                        <div class="ip-actions">
                            <button type="submit" name="submit" class="btn btn-success js--form-submit-btn"><?= lang('save_changes_button') ?>
                                <div class="spinner-border spinner-border-sm text-white d-none" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Edit DepartmentsPopup -->

        <script>

            $(document).ready(function () {

                $('.summernote').summernote({
                    placeholder: '<?php echo lang('type_notification_content') ?>',
                    tabsize: 2,
                    height: 153,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']]
                    ]
                });

                $('#js--notification-settings-form').on('submit', (e) => {
                    e.preventDefault();
                    const $modal = $('#js--whatsapp-popup_helpers');
                    const $submitBtn = $(this).find('.js--form-submit-btn');
                    const $spinner = $submitBtn.find('.spinner-border');
                    $spinner.removeClass('d-none');

                    const sendOption = $('#SendOption').val();
                    if ($modal.length && sendOption.includes('<?= SendOption::SEND_OPTION_WHATSAPP ?>')) {
                        $modal.modal('show');
                        $spinner.addClass('d-none');
                        // return false;
                    } else {
                        submitNotificationForm();
                    }
                });

                $('body').on('click', '.js--confirm-whatsapp', () => {
                    // debugger
                    submitNotificationForm();
                });

                const submitNotificationForm = () => {
                    const $form = $('#js--notification-settings-form');
                    let formData = $form.serialize();
                    const action = 'SettingsNotification';
                    const $modal = $('#js--whatsapp-popup_helpers');
                    const $submitBtn = $('.js--form-submit-btn');
                    const $spinner = $submitBtn.find('.spinner-border');
                    $spinner.removeClass('d-none');
                    const $settingsModal = $('#UpdateSettingsNotificationPopup');

                    $.ajax({
                        url: BeePOS.options.ajaxUrl,
                        type: 'POST',
                        data: formData + '&action=' + action,
                        success: function(response) {
                            if (response.success) {
                                $spinner.addClass('d-none');
                                if($modal.length) $modal.modal('hide');
                                $settingsModal.modal('hide');
                                $.notify(
                                    { icon: 'fas fa-check-circle', message: lang('action_done')},
                                    { type: 'success'}
                                );
                                location.reload();
                            } else {
                                $.notify(
                                    { icon: 'fas fa-times-circle', message: response.message || lang('action_not_done')},
                                    { type: 'danger'}
                                );
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        }
                    })
                }
            });


            $(function () {
                var time = function () {
                    return '?' + new Date().getTime()
                };

                // Header setup
                $('#MsgPopup').imgPicker({});
                // Header setup
                $('#NotEditPopup').imgPicker({});

            });


        </script>
        <script>
            $(function () {

                // Header setup
                $('#AddTechPopup').imgPicker({});


            });
        </script>
    <?php require_once '../app/views/footernew.php'; ?>
    <?php } else { ?>
        <?php redirect_to(__DIR__.'/../office/'); ?>
    <?php } ?>


<?php } ?>




