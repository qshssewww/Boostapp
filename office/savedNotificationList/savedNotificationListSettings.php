<!-- CSS import -->
<link href="savedNotificationList/savedNotificationList.css" rel="stylesheet">

<?php
require_once __DIR__ . '/../Classes/AppSettings.php';

$companyAppSettings = AppSettings::getByCompanyNum(Auth::user()->CompanyNum);
?>

<!-- Main popup  -->
<div id="js-savedNotificationSettings" class="position-relative bsapp-drop-menu animated bsapp-z-999" <?= $_SESSION['lang'] == 'he' ? 'dir="ltr"' : 'dir="rtl"' ?> style="display: none">

    <!-- Popup body -->
    <div class="shadow position-absolute p-16 overflow-hidden bg-white rounded w-100 text-start js-dropdown-inner bsapp-drop-menu-inner" dir="<?= $_COOKIE['boostapp_dir'] ?>">
        <div class="bsapp-settings-panel main-settings-panel d-flex flex-column position-absolute h-100 w-100 bg-white p-15 overflow-y-auto">
            <h5 class="row">
                <div class="col-10 font-weight-bold">
                    <?= lang('settings_notifications_title_popup'); ?>
                </div>
                <div class="col-2 text-end">
                    <i onclick="savedNotificationList.settingsVisibility(true);" class="fal fa-times pointer"></i>
                </div>
            </h5>

            <hr class="border-bottom"> <!-- Setting 1 -->

            <div class="overflow-y-auto container-fluid h-100">
                <div class="row">
                    <div class="col-10 font-weight-bold">
                        <?= lang('settings_sms_notification'); ?>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-switch">
                            <input <?= $companyAppSettings->SendSMS == 1 ? 'checked' : '' ?> type="checkbox" class="custom-control-input" id="toggle1">
                            <label class="custom-control-label pointer" for="toggle1"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-11 function-description">
                        <?= lang('description_settings_sms'); ?>
                    </div>
                </div>

                <hr class="border-bottom"> <!-- Setting 2 -->

                <div class="row">
                    <div class="col-10 font-weight-bold">
                        <?= lang('one_week_notification'); ?>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-switch">
                            <input <?= $companyAppSettings->ClassWeek == 1 ? 'checked' : '' ?> type="checkbox" onchange="savedNotificationList.selectVisibility(this)" class="custom-control-input" id="toggle2">
                            <label class="custom-control-label pointer" for="toggle2"></label>
                        </div>
                    </div>
                </div>
                <div class="collapse">
                    <br>
                    <span class="font-weight-bold"><?= lang('last_month_notification'); ?></span>
                    <select class="form-control">
                        <?php
                        for ($i = 1; $i<=12 ; $i++){
                            echo '<option '.($companyAppSettings->ClassWeekMonth == $i ? 'selected' : '').'>'.$i.'</option>';
                        }
                        ?>
                    </select>
                    <span class="function-description">
                        <?= lang('notification_settings_months_description'); ?>
                    </span>
                </div>

                <hr class="border-bottom"> <!-- Setting 3 -->

                <div class="row">
                    <div class="col-10 font-weight-bold">
                        <?= lang('disable_notifications_night'); ?>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-switch">
                            <input <?= $companyAppSettings->WatingListNight == 1 ? 'checked' : '' ?> type="checkbox" class="custom-control-input" onchange="savedNotificationList.selectVisibility(this)" id="toggle3">
                            <label class="custom-control-label pointer" for="toggle3"></label>
                        </div>
                    </div>
                </div>
                <div class="row collapse">
                    <div class="d-flex">
                        <div class="col-6">
                            <div class="row">
                                <div class="col">
                                    <?= lang('start_hour'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input value="<?= $companyAppSettings->WatingListStartTime ?>" type="time" class="form-control bg-light border-light">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col">
                                    <?= lang('end_hour'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input value="<?= $companyAppSettings->WatingListEndTime ?>" type="time" class="form-control bg-light border-light">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-bottom"> <!-- Setting 4 -->

                <div class="row">
                    <div class="col-10 font-weight-bold">
                        <?= lang('send_all_actions_email'); ?>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-switch">
                            <input <?= $companyAppSettings->SendNotification == 1 ? 'checked' : '' ?> type="checkbox" class="custom-control-input" id="toggle4">
                            <label class="custom-control-label pointer" for="toggle4"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-11 function-description">
                        <?= lang('app_actions_description'); ?>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button onclick="savedNotificationList.submitSave()" class="btn btn-success mt-5">
                <?= lang('save'); ?>
            </button>
        </div>
    </div>
</div>

<script src="savedNotificationList/savedNotificationList.js"></script>

