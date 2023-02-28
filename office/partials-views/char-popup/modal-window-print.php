<?php
require_once "../../../app/init.php";

$fields = isset($_COOKIE["freezrepostfields"]) ? $_COOKIE["freezrepostfields"] : "";
$ck_fields = !empty($fields) ? explode(",", $fields) : [];
?>

<div class="d-flex justify-content-between w-100 mb-20">
    <h5 class="bsapp-fs-18 p-15"><i class="fal fa-print mie-10"></i><?php echo lang('print_report'); ?></h5>
    <a class="text-dark p-15" data-dismiss="modal" href="javascript:;"><i class="fal fa-times h4"></i></a>
</div>
<form  class="flex-fill px-15">
    <div class="d-flex flex-column justify-content-between h-100 ">
        <div class="mb-15"><?php echo lang('display_options_report'); ?></div>
        <div class="bsapp-max-h-400p bsapp-scroll overflow-auto mb-10">
            <div class="form-group mb-15 pis-15 js-display-options">
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox" <?php echo (!empty($ck_fields) ? ((in_array("customerPhone", $ck_fields) ? "checked" : "")) : "checked") ?>  class="custom-control-input" id="customerPhone"  >
                    <label class="custom-control-label" for="customerPhone"><?php echo lang('client_phone'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("debtAmount", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="debtAmount">
                    <label class="custom-control-label" for="debtAmount"><?php echo lang('reports_debt_ramain'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("medicalInfo", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="medicalInfo">
                    <label class="custom-control-label" for="medicalInfo"><?php echo lang('medical_information'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("importantNotes", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="importantNotes">
                    <label class="custom-control-label" for="importantNotes"><?php echo lang('imortant_notes'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("permanentRegister", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="permanentRegister">
                    <label class="custom-control-label" for="permanentRegister"><?php echo lang('setting_permanently'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("birthday", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="birthday">
                    <label class="custom-control-label" for="birthday"><?php echo lang('birthday_single'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("freeClass", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="freeClass">
                    <label class="custom-control-label" for="freeClass"><?php echo lang('free_or_paid_class'); ?></label>
                </div>
                <div class="custom-control custom-checkbox mb-10">
                    <input type="checkbox"  <?php echo (!empty($ck_fields) ? ((in_array("firstClass", $ck_fields) ? "checked" : "")) : "checked") ?> class="custom-control-input" id="firstClass">
                    <label class="custom-control-label" for="firstClass"><?php echo lang('first_lesson_or_trial_membership'); ?></label>
                </div>
            </div>
        </div>
        <div class="form-group mt-40 mb-15 d-flex align-items-start">
            <!--button type="button" class="btn btn-light mie-10"  onclick="modalPrintPopup.viewReport(this);"><i class="fal fa-file-pdf mie-10"></i><?php echo lang('download_pdf'); ?></button-->
            <a href="" class="btn btn-light mie-10 disabled js-report-view" target="_blank"><?php echo lang('watch_report'); ?></a>
            <div class="js-div-copy-report-link">
                <button type="button" class="btn btn-light js-report-link-copy disabled"   onclick="modalPrintPopup.copyReportLink(this);"><i class="fal fa-copy"></i> <?php echo lang('copy_link'); ?>
                </button>
                <div class="input-group" style="display:none;">                    
                    <input type="text" class="form-control   js-report-link d-none" >
                    <div class="input-group-append">
                        <div class="input-group-text bg-success border-success text-white rounded" ><i class="fal fa-check mie-10"></i> Copied</div>
                    </div>
                </div>
            </div>
            <!--div class="bsapp-max-w-75">
                <select class="js-select-custom2">
                    <option><?php //echo lang('watch_report'); ?></option>
                    <option><?php //echo lang('download_pdf'); ?></option>
                    <option><?php //echo lang('print'); ?></option>
                </select>
            </div-->
        </div>
        <div class="form-group mb-15">

        </div>
<!--        <div class="d-flex justify-content-end align-items-end h-100p form-group mb-0">-->
<!--            <a class="text-primary bsapp-fs-18 text-decoration-none" href="javascript:;" data-dismiss="modal">--><?php //echo lang('close'); ?><!--</a>-->
<!--        </div>-->
    </div>
</form>

<script>
    modalPrintPopup.init();
</script>