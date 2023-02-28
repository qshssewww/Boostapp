<?php
$companyProductSettings = $companyProductSettings ?? (new CompanyProductSettings)->getSingleByCompanyNum(Auth::user()->CompanyNum);
$CompanySettingsDash = $CompanySettingsDash ?? DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
?>
<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-manage-settings d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart"
     data-depth="1">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       data-target="main-settings-panel">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('back_to_store') ?>
        </h5>
    </a>

    <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
        <i class="fal fa-layer-group mie-6 text-gray-500 bsapp-fs-19"></i>
        <?php echo lang('general_settings_admin') ?>
    </h3>

    <div class="scrollable stable">
        <div class="pb-50">

            <ul class="list-unstyled p-0">

                <?php if ($companyProductSettings->manageMemberships == 1) { ?>
                    <li class="mb-20">
                        <div class="form-toggle">
                            <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-10">
                                <span class="flex-grow-1"><?php echo lang('mange_by_membership_type') ?></span>
                                <div class="custom-control custom-switch mie-5 d-none">
                                    <input type="checkbox" class="toggle-manage custom-control-input" disabled
                                           id="manage-memberships-switch" checked>
                                    <label class="custom-control-label" for="manage-memberships-switch" role="button"
                                           onclick="toggleShopSettings($(this))"></label>
                                </div>
                            </h6>
                            <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?php echo lang('manage_membership_description') ?></p>
                            <a class="toggle-content d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                               role="button" onclick="getItems('memberships')" data-target="storeSettings-manage-items"
                               role="button">
                                <?php echo lang('manage_subscription_type') ?>
                                <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                            </a>
                        </div>
                    </li>
                <?php } ?>

                <li class="mb-20">
                    <div class="form-toggle">
                        <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-15">
                            <span class="flex-grow-1"><?= lang('family_subscription_settings_title') ?></span>
                        </h6>
                        <p class="text-gray-500 mb-10 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                            <?= lang('family_subscription_settings_description') ?>
                        </p>
                        <div class="form-toggle flex-fill mb-10 w-100">
                            <select class="form-control" id="family_membership_transfer_setting" onchange="familyMembershipTransferSetting(this)">
                                <option <?= $companyProductSettings->familyMembershipTransfer == 0 ? 'selected' : '' ?> value="0"><?= lang('no') ?></option>
                                <option <?= $companyProductSettings->familyMembershipTransfer == 2 ? 'selected' : '' ?> value="2"><?= lang('family_subscription_settings_yes')?></option>
                                <?php if ($companyProductSettings->manageMemberships == 1) { ?>
                                    <option <?= $companyProductSettings->familyMembershipTransfer == 1 ? 'selected' : '' ?> value="1"><?= lang('family_subscription_settings_type') ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </li>

                <li class="mb-20">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('product_cat_manage') ?></h6>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?php echo lang('product_cat_description') ?></p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="storeSettings-manage-items" role="button" onclick="getItems('categories')">
                        <?php echo lang('manage_category') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>

                <!--    Notification settings   -->
                <li class="mb-20">
                    <div class="form-toggle">
                        <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-15">
                            <span class="flex-grow-1"><?php echo lang('note_send_notification_membership') ?></span>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="toggle-manage custom-control-input"
                                       id="membership_notification_switch"
                                    <?php echo($companyProductSettings->notificationAtEnd == 1 ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="membership_notification_switch"
                                       role="button"></label>
                            </div>
                        </h6>
                    </div>
                    <div class="form-toggle">
                        <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-15">
                            <span class="flex-grow-1"><?php echo lang('send_notification_before_membership') ?></span>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="toggle-manage custom-control-input"
                                       id="membership_notification_early_switch"
                                    <?php echo($companyProductSettings->NotificationDays > 0 ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="membership_notification_early_switch"
                                       role="button"></label>
                            </div>
                        </h6>
                        <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                            <?php echo lang('settings_send_reminder_sub') ?></p>
                    </div>
                    <div class="<?php echo($companyProductSettings->NotificationDays > 0 ? 'd-flex' : 'd-none') ?> mt-15 align-items-end"
                         id="membership_notification_options">
                        <div class="form-toggle flex-fill mb-10 w-85p mie-15">
                            <select class="form-control" id="membership_notification_value" rollback="<?php
                            echo $companyProductSettings->NotificationDays == 0 ? 1 : $companyProductSettings->NotificationDays ?>">
                                <?php for ($iter = 1; $iter <= 30; $iter++) { ?>
                                    <option value="<?php echo $iter ?>" <?php echo($companyProductSettings->NotificationDays == $iter ? 'selected' : '') ?>>
                                        <?php echo $iter ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-toggle flex-fill mb-10 w-100">
                            <select class="form-control" id="membership_notification_type" disabled>
                                <option value="days"><?php echo lang('days') . ' ' . lang('before_membership_ends') ?></option>
                                <option value="weeks"><?php echo lang('weeks') . ' ' . lang('before_membership_ends') ?></option>
                            </select>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    function switchHandler(data, name) {
        // error handler
        if (data.Status !== "Success") {
            $.notify({
                message: lang('error_oops_something_went_wrong')
            }, {
                type: 'danger',
                z_index: 2000,
            });

            //  return toggle
            const checkBoxes = $(name);
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
            if (name === '#membership_notification_early_switch') {
                $("#membership_notification_options").toggleClass("d-none d-flex");
            }
        }

        $(name).attr("disabled", false);
        if (name === '#membership_notification_early_switch') {
            $("#membership_notification_value").attr("disabled", false);
        }
    }

    function notificationSwitchHandler(data) {
        switchHandler(data, '#membership_notification_switch');
    }

    function notificationEarlySwitchHandler(data) {
        switchHandler(data, '#membership_notification_early_switch');
    }

    function notificationEarlyValueHandler(data) {
        // error handler
        if (data.Status !== "Success") {
            $.notify({
                message: lang('error_oops_something_went_wrong')
            }, {
                type: 'danger',
                z_index: 2000,
            });

            //  rollback value
            const rollback = $('#membership_notification_value')[0].getAttribute('rollback');
            $("#membership_notification_value")[0].value = rollback;
            $('#membership_notification_value').trigger('change.select2');
        }

        $('#membership_notification_value')[0].setAttribute('rollback', $("#membership_notification_value")[0].value);
        $('#membership_notification_early_switch').attr("disabled", false);
        $("#membership_notification_value").attr("disabled", false);
    }

    function familyMembershipTransferSetting(elem) {
        if (<?= $companyProductSettings->familyMembershipTransfer ?? 2 ?> != $(elem).val()) {
            postApi('storeSettings', {
                fun: 'UpdateFamilyMembershipTransferSetting',
                familyMembershipTransfer: ($(elem).val()),
            });
        }
    }

    $(document).ready(function () {
        $("select#membership_notification_type").select2({
            minimumResultsForSearch: Infinity,
            theme: "bsapp-dropdown bsapp-no-arrow",
            width: '100%',
        });

        $("select#membership_notification_value").select2({
            minimumResultsForSearch: Infinity,
            theme: "bsapp-dropdown",
            width: '100%',
        });

        $('select#family_membership_transfer_setting').select2({
            minimumResultsForSearch: Infinity,
            theme: 'bsapp-dropdown',
            width: '100%'
        });

        // end notifications checkbox toggle
        $('#membership_notification_switch').on('change', function () {
            $('#membership_notification_switch').attr("disabled", true);
            postApi('storeSettings', {
                fun: "toggleEndNotification",
                notificationAtEnd: ($('#membership_notification_switch')[0].checked ? '1' : '0'),
            }, 'notificationSwitchHandler', true);
        });

        // early notifications checkbox toggle
        $('#membership_notification_early_switch').on('change', function () {
            $('#membership_notification_early_switch').attr("disabled", true);
            $('#membership_notification_value').attr("disabled", true);

            $("#membership_notification_options").toggleClass("d-none d-flex");

            postApi('storeSettings', {
                fun: "changeEarlyNotification",
                NotificationDays: ($('#membership_notification_early_switch')[0].checked ? $('#membership_notification_value')[0].value : 0),
            }, 'notificationEarlySwitchHandler', true);
        });

        // end notifications value
        $('#membership_notification_value').on('change', function () {
            $('#membership_notification_early_switch').attr("disabled", true);
            $('#membership_notification_value').attr("disabled", true);

            postApi('storeSettings', {
                fun: "changeEarlyNotification",
                NotificationDays: $('#membership_notification_value')[0].value,
            }, 'notificationEarlyValueHandler', true);
        });
    });
</script>
