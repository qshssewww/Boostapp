<?php
require_once __DIR__ . '/../../app/initcron.php';
require_once __DIR__ . '/../Classes/AppNotification.php';
require_once __DIR__ . '/../../app/enums/NotificationContent/SendOption.php';

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();

$ItemId = $_POST['ItemId'];

$Items = DB::table('notificationcontent')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('id', '=', $ItemId)->first();

?>

<h5><?= $Items->TypeName ?></h5>
<!--<hr>-->
<div class="form-group pt-10">
    <label><?= lang('status'); ?>
        <select class="form-control text-start" name="Status" id="StatusDiv">
            <option value="0" <?php if ('0' == $Items->Status) {
                echo 'selected';
            } else {
            } ?>><?= lang('active_notification'); ?></option>
            <option value="1" <?php if ('1' == $Items->Status) {
                echo 'selected';
            } else {
            } ?>><?= lang('disabled_notification'); ?></option>
        </select>
</div>

<div id="DivStatus2" style="display: <?php if ('0' == $Items->Status) {
    echo 'block';
} else {
    echo 'none';
} ?>;">


    <div class="form-group">
        <label><?= lang('settings_notification_options'); ?></label>

        <?php if ($Items->SendOption == 'BA999' || $Items->SendOption == 'BA000' || $Items->SendOption == SendOption::SEND_OPTION_WHATSAPP) : ?>

            <select class="form-control js-example-basic-single select2multipleDesk text-start" name="SendOption[]"
                    id="SendOption" multiple="multiple" data-select2order="true" style="width: 100%;">
                <option value="BA999" <?= ($Items->SendOption == 'BA999') ? 'selected' : '' ?> ><?php echo lang('all'); ?></option>
                <option value="0"><?= lang('push_notification'); ?></option>
                <option value="1"><?= lang('sms_price_template_11'); ?></option>
                <option value="2"><?= lang('email_free'); ?></option>
                <?php
                // TODO remove beta check - BP-735
                if ($SettingsInfo->WhatsAppEnabled == 1 && in_array($Items->Type, [4, 11])) {
                ?>
                <option value="<?= SendOption::SEND_OPTION_WHATSAPP ?>" <?= ($Items->SendOption == SendOption::SEND_OPTION_WHATSAPP) ? 'selected' : '' ?>><?= lang('whatsapp_price_template') ?></option>
                <?php } ?>
                <option value="BA000" <?= ($Items->SendOption == 'BA000') ? 'selected' : '' ?>><?= lang('without_notify'); ?></option>
            </select>

        <?php else :
            $myArray = explode(',', $Items->SendOption);

            ?>

            <select class="form-control js-example-basic-single select2multipleDesk text-start" name="SendOption[]"
                    id="SendOption" multiple="multiple" data-select2order="true" style="width: 100%;">
                <option value="0" <?= $selected = (in_array('0', $myArray)) ? 'selected="selected"' : ''; ?> ><?= lang('push_notification'); ?></option>
                <option value="1" <?= $selected = (in_array('1', $myArray)) ? 'selected="selected"' : ''; ?>><?= lang('sms_price_template_11'); ?></option>
                <option value="2" <?= $selected = (in_array('2', $myArray)) ? 'selected="selected"' : ''; ?>><?= lang('email_free'); ?></option>
                <?php
                // TODO remove beta check - BP-735
                if ($SettingsInfo->WhatsAppEnabled == 1 && in_array($Items->Type, [4, 11])) {
                    ?>
                    <option value="<?= SendOption::SEND_OPTION_WHATSAPP ?>"><?= lang('whatsapp_price_template') ?></option>
                <?php } ?>
                <option value="BA000"><?= lang('without_notify'); ?></option>
            </select>
        <?php endif; ?>
    </div>
    <?php if($Items->Type == 21) { // אפליקציה - פרטי התחברות ?>
        <div class="form-group" >
            <label><?php echo lang('all_status_users'); ?></label>
            <select class="form-control js-example-basic-single js--select2 text-start" name="SendClientsTypeOption" id="ClientsTypeSelect"  data-select2order="true" style="width: 100%;">
                <option value="0" <?php echo $Items->SendClientsTypeOption == 0? 'selected="selected"' : '';?>><?php echo lang('active_clients_plus_interested'); ?></option>
                <option value="1" <?php echo $Items->SendClientsTypeOption == 1? 'selected="selected"' : '';?>><?php echo lang('active_clients'); ?></option>
                <option value="2" <?php echo $Items->SendClientsTypeOption == 2? 'selected="selected"' : '';?>><?php echo lang('interested_clients'); ?></option>
            </select>
        </div>
    <?php } ?>

    <div class="alertb alert-info">
        <label><?= lang('notification_order_notice'); ?></label>
        <br>
    </div>


    <div class="form-group">
        <label><?php echo lang('notification_settings_permissions'); ?></label>

        <?php if ($Items->SendStudioOption == 'BA999' || $Items->SendStudioOption == 'BA000') { ?>

            <select class="form-control js-example-basic-single select2multipleDesk text-start"
                    name="SendStudioOption[]" id="SendStudioOption" multiple="multiple" data-select2order="true"
                    style="width: 100%;">
                <option value="BA999" <?php if ($Items->SendStudioOption == 'BA999') {
                    echo 'selected';
                } else {
                } ?> ><?php echo lang('everyone'); ?></option>

                <?php

                $SectionInfos = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->get();
                foreach ($SectionInfos as $SectionInfo) {

                    ?>
                    <option value="<?php echo $SectionInfo->id; ?>"><?php echo $SectionInfo->name; ?></option>
                <?php } ?>

                <option value="BA000" <?php if ($Items->SendStudioOption == 'BA000') {
                    echo 'selected';
                } else {
                } ?>><?php echo lang('without_notify'); ?></option>
            </select>


        <?php } else { ?>


            <select class="form-control js-example-basic-single select2multipleDesk text-start"
                    name="SendStudioOption[]" id="SendStudioOption" multiple="multiple" data-select2order="true"
                    style="width: 100%;">
                <option value="BA999"><?php echo lang('everyone'); ?></option>

                <?php
                $myArrays = explode(',', $Items->SendStudioOption);
                $SectionInfos = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->get();
                foreach ($SectionInfos as $SectionInfo) {
                    $selecteds = (in_array($SectionInfo->id, $myArrays)) ? ' selected="selected"' : '';
                    ?>
                    <option value="<?php echo $SectionInfo->id; ?>" <?php echo @$selecteds; ?> ><?php echo $SectionInfo->name; ?></option>
                <?php } ?>

                <option value="BA000"><?php echo lang('without_notify'); ?></option>
            </select>


        <?php } ?>

    </div>

</div>

<div id="DivStatus3" style="display: <?php if ('1' == $Items->Status) {
    echo 'block';
} else {
    echo 'none';
} ?>;">

    <div class="alertb alert-warning"><?php echo lang('settings_notification_disabled'); ?></div>

</div>

<!-- Meeting Helpers small modals -->
<div id="js--whatsapp-popup_helpers" class="modal fade bsapp--meeting-popup bsapp--meeting-popup_helpers js--bsapp--meeting-modal bsapp-modal-bg-opacity"
     tabindex="-1"
     aria-labelledby="meetingPopupHelpersLabel"
     aria-hidden="true"
     data-backdrop="none">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close btn--close-modal" aria-label="Close">
                <i class="fal fa-times"></i>
            </button>
            <div class="modal-header">
                <h4 class="modal-title"><?= lang('whatsapp_popup_title') ?></h4>
            </div>
            <div class="modal-body d-flex flex-column justify-content-between" type="">
                <div class="d-flex flex-column ">
                    <div class="text-center">

                        <div class="meeting--helpers-icon">
                            <i class="fab fa-whatsapp" style="color: var(--success)"></i>
                        </div>

                        <p class="meeting--helpers-bold"><?= lang('popup_whatsapp_approve') ?></p>
                    </div>
                </div>

                <div class="bsapp--checkbox-small checkbox--position-bottom pb-5">
                    <input name="whatsapp-confirm-box" id="whatsapp-confirm-box"
                           type="checkbox"
                           class="custom-control-input">
                    <label for="whatsapp-confirm-box"><?= lang('confirm') ?></label>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn--light m-0 w-48 btn--close-modal"><?= lang('cancel') ?></button>
                <button type="button" class="btn btn-success m-0 w-48 js--confirm-whatsapp disabled"><?= lang('confirm') ?>
                    <div class="spinner-border spinner-border-sm text-white d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
            <!-- Meeting Helpers small modals :: end -->
        </div>
    </div>
</div>



<style>

    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>


<script>

    $(".select2multipleDesk").select2({theme: "bootstrap", placeholder: "<?php echo lang('choose'); ?>"});
    $(".js--select2").select2({theme:"bsapp-dropdown", placeholder: "<?php echo lang('choose'); ?>", minimumResultsForSearch: -1});

    $('#SendOption').on('select2:select', function (e) {
        const selected = $(this).val();
        if (selected != null) {
            if (selected.includes('BA999') || selected.includes('BA000') || selected.includes('<?= SendOption::SEND_OPTION_WHATSAPP ?>')) {
                $(this).val(e.params.data.id).select2({
                    theme: "bootstrap",
                    placeholder: "<?php echo lang('choose'); ?>"
                });
            }
        }
    });

    $('body').on('click', '.btn--close-modal', () => {
        const $modal = $('#js--whatsapp-popup_helpers');
        $modal.modal("hide");
    });

    $('body').on('change', '#whatsapp-confirm-box', () => {
        const $modal = $('#js--whatsapp-popup_helpers');
        const $btn = $modal.find('.js--confirm-whatsapp');
        if ($('#whatsapp-confirm-box').is(':checked')) {
            $btn.removeClass('disabled');
        } else {
            $btn.addClass('disabled');
        }
    });

    $('#SendStudioOption').on('select2:select', function (e) {
        var selected = $(this).val();

        if (selected != null) {
            if (selected.indexOf('BA999') >= 0) {
                $(this).val('BA999').select2({theme: "bootstrap", placeholder: "<?php echo lang('choose'); ?>"});
            } else if (selected.indexOf('BA000') >= 0) {
                $(this).val('BA000').select2({theme: "bootstrap", placeholder: "<?php echo lang('choose'); ?>"});
            }
        }

    });


    $("#StatusDiv").change(function () {

        var Id = this.value;
        if (Id == '0') {
            DivStatus2.style.display = "block";
            DivStatus3.style.display = "none";
        } else {
            DivStatus2.style.display = "none";
            DivStatus3.style.display = "block";
        }
    });

</script>

