<?php
$id = "";
$m_date = "";
$date = "";
$type = 'crm';
$edit_d_none = true;
$source = 'js';
if (isset($medical)):
    $id = $medical->id;
    $m_date = date("d/m/Y H:i", strtotime($medical->Dates));
    if (isset($medical->TillDate)):
        $date = date("d/m/Y", strtotime($medical->TillDate));
    endif;
    $type = 'medical';
    $edit_d_none = false;
    $source = 'back';
endif;
if (isset($crm)):
    $id = $crm->id;
    $m_date = date("d/m/Y H:i", strtotime($crm->Dates));
    if (isset($crm->TillDate)):
        $date = date("d/m/Y", strtotime($crm->TillDate));
    endif;
    $type = 'crm';
    $edit_d_none = false;
    $source = 'back';
endif;

$icon = ($type == "medical") ? "notes-medical text-danger" : "clipboard-check text-warning";
if (isset($medical) || isset($crm)):
    $content = ($type == "medical") ? $medical->Content : $crm->Remarks;
    $till_date_label = isset($$type->TillDate, $date) ? lang('until_date') . ': ' . $date : "";
    $till_date = isset($$type->TillDate) ? $date : "";
else:
    $content = "";
    $till_date_label = "";
    $till_date = "";
endif;
?>

<div class="js-textarea-newly-added mt-20 "  data-type='<?php echo $type; ?>' data-id="<?php echo $id; ?>" data-origin='<?php echo $source; ?>'>
    <div class="<?php echo ($edit_d_none == false ) ? 'd-none' : 'd-flex'; ?> flex-column w-100 js-textarea-edit-mode px-15 py-20 border-bottom border-top border-light">
        <div class="mb-10">
            <textarea class="form-control js-form-control-textarea"><?php echo $content; ?></textarea>
        </div>
        <div class="d-flex justify-content-between align-items-start">
            <a class="btn btn-light mie-8 js-textarea-delete" href="javascript:;" onclick="modalUserPopup.hideAddedTextArea(this);"><i class="fal fa-times"></i></a>
            <div class="d-flex justify-content-end">
                <div onclick="modalUserPopup.showCal(this)" class="position-absolute w-50" style="left:90px; cursor: pointer;">
                <i class="fal fa-calendar position-absolute start-top-10p"></i>
                <input class="form-control bg-light border border-light js-datepicker mie-10" name="till_date" placeholder="<?= lang('until_date') ?>">
                </div>
                <a class="btn btn-info js-textarea-add-content" href="javascript:;" onclick="modalUserPopup.updateTextContent(this);"><span class="js-loader-spin" style='display:none;'><i class="fas fa-spinner fa-spin"></i></span> <?php echo lang('add_single') ?></a>
            </div>
        </div>
    </div>
    <div class="js-textarea-crm-div flex-column px-15 <?php echo ($edit_d_none == false ) ? 'd-flex' : 'd-none'; ?> ">            
        <div class="w-100 shadow p-15 rounded-lg js-textarea-read-mode js-<?php echo $type; ?>-div" >
            <div class="d-flex justify-content-between">
                <span class="text-gray-400 bsapp-fs-14 font-weight-bold"><i class=" fal fa-<?php echo $icon; ?> mie-5"></i><?php echo ($type == "medical") ? lang('customer_card_medical_records') : lang('important_client_notes'); ?></span>
                <span class="bsapp-fs-14 text-gray-400" >
                    <span class="js-till-date" formatedate="<?php echo $till_date; ?>"> <?php echo $till_date_label; ?></span>
                    <?php if (Auth::userCan('172') || Auth::userCan('170')): ?>
                        <a class="js-edit-content text-gray-400" href="javascript:;"  onclick="modalUserPopup.editModeOn(this);"><i class="fas fa-pencil bsapp-fs-14 edit-icon"></i></a>
                    <?php endif;?>
                </span>
            </div>
            <h6 class="my-8 js-content-remarks pb-20"><?php echo $content; ?></h6>
            <div class="d-flex justify-content-between w-100 text-gray-400 bsapp-fs-14">
                <?php if (Auth::userCan('172') || Auth::userCan('170')): ?>
                    <a data-client-id="<?php echo isset($clientData) ? $clientData["clientInfo"]->id : ""; ?>" href="javascript:;"  onclick='modalUserPopup.remove(this);' class="js-remove-<?php echo $type; ?> text-gray-400"><i class="fal fa-minus-circle"></i> <?php echo lang('a_remove_single') ?></a>
                <?php endif;?>
                <div><?php echo lang('updated_in_date') . ' ' . $m_date; ?></div>
            </div>
        </div>   
    </div>
</div>
