<?php
if (!isset($ClientId))
    return;

require_once __DIR__.'/../../Classes/Pipereasons.php';
?>
<div class="modal fade text-start" role="dialog" id="js-PipeFailReasonsPopup">
    <div class="modal-dialog" style="width: 420px;">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h4 class="modal-title"><?= lang('type_reason') ?></h4>
            </div>
            <div class="modal-body">
                <?php if (!empty((new ClassStudioDateRegular())->getClientRegularClasses($ClientId, Auth::user()->CompanyNum, 0))) { ?>
                    <div class="form-group alert alert-danger">
                        <span><?= lang('clinet_archive_note'); ?></span>
                    </div>
                <?php } ?>
                <div autocomplete="off">
                    <input type="hidden" name="ItemId" id="ReasonsItemId" value="">
                    <div class="form-group" >
                        <label><?= lang('reason_leave_not_join') ?></label>
                        <select class="form-control" id="js-ReasonId" name="ReasonId">
                            <?php
                            $PipeSources = (new Pipereasons())->getActiveReasonByCompany(Auth::user()->CompanyNum);
                            foreach ($PipeSources as $PipeSource) { ?>
                                <option value="<?= $PipeSource->id; ?>"><?= $PipeSource->Title ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= lang('free_text') ?></label>
                        <textarea name="FailRemarks" id="js-FailRemarks" class="form-control" rows="15" maxlength="250" style="resize: none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-between">
                <!-- footerBtns -->
            </div>
        </div>
    </div>
</div>
<style>
    .btn-outline-dark:hover{
        background: none;
    }
</style>
<script>
    let archivePopupVars = {
        requestType: null,
        newStatus: null,
        oldStatus: null,
        pipeId: null,
        leadId: null,
        popUp:$('#js-PipeFailReasonsPopup')
    };

    function CreateFailReasonPopupButtons(isDragDrop) {
        const footer = archivePopupVars.popUp.find('.modal-footer');

        let footerBtns;

        const pipeLineBtn = '<button type="submit" name="submitReason" id="submitReason" onclick="UpdateInProfilePipeline()" class="btn btn-dark">';
        const regularBtn = '<button type="submit" name="submitReason" id="submitReason" onclick="UpdateClientStatus()" class="btn btn-dark">';

        if (archivePopupVars.requestType === 2) {
            if(archivePopupVars.oldStatus == 2){
                footerBtns = pipeLineBtn;
            } else {
                footerBtns = regularBtn;
            }
        } else if (archivePopupVars.requestType === 0) {
            footerBtns = pipeLineBtn;
        } else {
            footerBtns = regularBtn;
        }


        footerBtns += '<?= lang("save_changes_button") ?></button>';
        footer.append(footerBtns);

        if (!isDragDrop) {
            if (archivePopupVars.requestType === 2) {
                if (archivePopupVars.newStatus === 1) {
                    footer.append('<a class="btn btn-outline-dark" onclick="changeUserSettingsStatusSelect()" data-dismiss="modal"><?= lang("close") ?></a>');
                } else {
                    footer.append('<a class="btn btn-outline-dark" data-dismiss="modal"><?= lang("close") ?></a>');
                }
            } else {
                footer.append('<a class="btn btn-outline-dark" data-dismiss="modal"><?= lang("close") ?></a>');
            }
        }

        if (archivePopupVars.newStatus === 0 && archivePopupVars.requestType != 2) {
            archivePopupVars.popUp.find('#submitReason').click();
        }
    }

    function UpdateClientStatus(){

        const submitBtn = archivePopupVars.popUp.find('#submitReason');
        const reasonId = archivePopupVars.popUp.find('select#js-ReasonId').find(':selected').val();
        const reasonText = archivePopupVars.popUp.find('textarea#js-FailRemarks').val();

        submitBtn.prop('disabled', true);

        const bufferingMessage = $.notify(
            {
                icon: 'fas fa-spinner fa-spin',
                message: '<?php echo lang('updating_lead_status') ?>',
            }
            ,{
                type: 'warning',
            }
        );

        $.ajax({
            url: 'action/UpdateClientStatus.php',
            type: 'POST',
            data:{
                'ClientId': '<?= $ClientId ?>',
                'CompanyNum': '<?= Auth::user()->CompanyNum ?>',
                'newValue': archivePopupVars.newStatus,
                'ReasonId' : reasonId,
                'ReasonText': reasonText
            },
            success: function(){
                $.notify(
                    {icon: 'fas fa-check-circle',message: lang('action_done')},
                    {type: 'success'},
                );
            },
            error: function(){
                $.notify(
                    {icon: 'fas fa-times-circle', message: lang('error_oops_something_went_wrong')},
                    {type: 'danger'},
                );
            },
            complete: function (){
                bufferingMessage.close();
                location.reload();
                submitBtn.prop('disabled', false);
            },
        });
    }

    function UpdateInProfilePipeline() {
        const reasonId = archivePopupVars.popUp.find('select#js-ReasonId').find(':selected').val();
        const reasonText = archivePopupVars.popUp.find('textarea#js-FailRemarks').val();
        const submitBtn = archivePopupVars.popUp.find('#submitReason');

        submitBtn.prop('disabled', true);

        const bufferingMsg = $.notify(
            {
                icon: 'fas fa-spinner fa-spin',
                message: '<?php echo lang('updating_lead_status') ?>',
            }
            , {
                type: 'warning',
            }
        );

        $.ajax({
                url: 'action/UpdatePipeNew.php',
                type: 'POST',
                data: {
                    'LeadId': archivePopupVars.leadId,
                    'PipeId': archivePopupVars.pipeId,
                    'NewStatus': archivePopupVars.newStatus,
                    'ReasonId': reasonId,
                    'ReasonText': reasonText,
                },
                success: function (res) {
                    if (res.length) {
                        $.notify(
                            {
                                icon: 'fas fa-times-circle',
                                message: res,
                            }
                            , {
                                type: 'danger',
                            }
                        );
                        return;
                    }

                    bufferingMsg.close();

                    $.notify(
                        {
                            icon: 'fas fa-check-circle',
                            message: '<?php echo lang('lead_status_updated') ?>',
                        }
                        , {
                            type: 'success',
                        }
                    );
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    location.reload();
                }
            }
        );
    }

    function changeUserSettingsStatusSelect() {
        const selectStatus = $('#js-UserSettings-EditClient').find('select[name=Status]');

        if (archivePopupVars.newStatus === 1) {
            selectStatus.val(0);
        } else {
            selectStatus.val(1);
        }
    }

</script>