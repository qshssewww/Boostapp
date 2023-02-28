let popover_placement = 'right';

$(document).ready(function(){
    const PipeCategoryId = $('.js-manage-leads-body #ChoosePipeline option:selected').val();
    LeadsData.GetDataManageLeads(PipeCategoryId);

    $(".ip-close").click(function(){
        $('#FormCalendarClient').trigger("reset");
        // scheduler.clearAll();
        //  scheduler.setCurrentView(<?php //echo date('Y-m-d') ?>//);
        //  scheduler.load("new/data/events.php");
        $('#CalTaskStatus').prop('disabled', true);
    });

    $('#minor_checkbox').on('click', function() {
        if ($(this).is(":checked")) {
            $("#minor-lead-div").show();
            $('#minor-lead-div').height(200);
            $("#minor_firstName").prop('required', true);
            $("#minor_lastName").prop('required', true);
            $("#minor_lastName").val($('#lead_LastName').val());

        } else {
            $("#minor_firstName").prop('required', false);
            $("#minor_lastName").prop('required', false);
            $("#minor_lastName").val();
            $('#minor-lead-div').height(0);
            setTimeout(() => {
                $("#minor-lead-div").hide();
            }, 200);
        }
    });

    var myDefaultWhiteList = $.fn.tooltip.Constructor.Default.whiteList;
    myDefaultWhiteList.a = ['data-client','data-pipe-id','data-task']

    if($("html").attr("dir") == 'rtl'){
        popover_placement = 'left';
    }
    $("body").on("click", ".js-new-task", function () {
        const new_task = $(this).attr("data-task");
        const client_id = $(this).attr("data-client");
        const pipe_id = $(this).attr("data-pipe-id");
        const PipeCategoryId = $('.js-manage-leads-body #ChoosePipeline option:selected').val()
        if ($('#js-task-popup').length > 0) {
            handleNewTask(new_task, client_id);
        } else {
            NewCal(new_task, client_id, pipe_id, PipeCategoryId);
        }
    });

    $('#PipeLineSelect').trigger('change');
});

$('#ChooseAgentForPipeline').on('change', function() {
    const pipeId = $(".js-manage-leads-body #ChoosePipeline option:selected").val()
    const Id = this.value;
    if (Id=='BA999'){
        LeadsData.GetDataManageLeads(pipeId);
    }
    else {
        LeadsData.GetDataManageLeads(pipeId, Id);
    }
});

$('#ChoosePipeline').on('change', function() {
    const Id = this.value;
    const AgentId = $('#ChooseAgentForPipeline option:selected').val();
    if(AgentId)
        $(`.js-manage-leads-body #ChooseAgentForPipeline`).val('BA999');
    LeadsData.GetDataManageLeads(Id);
    $(`#PipeLineSelect`).val(Id);
    $(`#PipeLineSelect`).trigger('change');
});

$('#PipeLineSelect').on('change', function() {
    var Id = this.value;

    $('#StatusSelect option')
        .hide() // hide all
        .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
        .show(); // and show them

    $('#StatusSelect').val('');
});

function PipeAction(PipeLineId,MainPipeId,PipeId,ClientId) {


    var PipeLineId =  PipeLineId;
    var MainPipeId =  MainPipeId;
    var PipeId =  PipeId;
    var ClientId = ClientId;


    $( "#ResultPipeline" ).empty();
    var modalcode = $('#PipeActionPopup');
    $('#PipeActionPopupTitle').html(lang('actions'));
    modalcode.modal('show');

    var url = 'new/PipeLine_Action.php?Id='+PipeLineId+'&ClientId='+ClientId+'&noReload=true';

    $('#ResultPipeline').load(url,function(e){
        $('#ResultPipeline .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });


}

function PipeMore(PipeLineId,MainPipeId,PipeId,ClientId) {


    var PipeLineId =  PipeLineId;
    var MainPipeId =  MainPipeId;
    var PipeId =  PipeId;
    var ClientId = ClientId;


    $( "#ResultPipeline" ).empty();
    var modalcode = $('#PipeActionPopup');
    $('#PipeActionPopupTitle').html(lang('more_details'));
    modalcode.modal('show');
    var url = 'new/PipeLine_More.php?Id='+PipeLineId+'&ClientId='+ClientId;

    $('#ResultPipeline').load(url,function(e){
        $('#ResultPipeline .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeAddNote(PipeLineId,ClientId) {


    var PipeId =  PipeLineId;
    var ClientId = ClientId;


    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('manage_notes'));

    modalcode.modal('show');
    var url = 'new/PipeLine_AddNote.php?Id='+PipeId+'&ClientId='+ClientId+'&noReload=true';


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeSendSMS(PipeLineId,ClientId) {


    var PipeId =  PipeLineId;
    var ClientId = ClientId;


    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('send_message'));

    modalcode.modal('show');
    var url = 'new/PipeLine_SendMessage.php?Id='+PipeId+'&ClientId='+ClientId;


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeAddMemberShip(PipeLineId,ClientId) {


    var PipeId =  PipeLineId;
    var ClientId = ClientId;


    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('define_trial_membership'));

    modalcode.modal('show');
    var url = 'new/PipeLine_AddMemberShip.php?Id='+PipeId+'&ClientId='+ClientId;


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeAddCalss(PipeLineId,ClientId) {


    var PipeId =  PipeLineId;
    var ClientId = ClientId;

    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('set_trial_lesson'));

    modalcode.modal('show');
    var url = 'new/PipeLine_AddClass.php?Id='+PipeId+'&ClientId='+ClientId+'&noReload=true';


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeSendForm(PipeLineId,ClientId) {

    var PipeId =  PipeLineId;
    var ClientId = ClientId;

    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('send_joining_form'));

    modalcode.modal('show');
    var url = 'new/PipeLine_SendForm.php?Id='+PipeId+'&ClientId='+ClientId;


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

function PipeSendMedicalForm(PipeLineId,ClientId) {

    var PipeId =  PipeLineId;
    var ClientId = ClientId;

    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html(lang('send_health_declaration_form'));

    modalcode.modal('show');
    var url = 'new/PipeLine_SendMedicalForm.php?Id='+PipeId+'&ClientId='+ClientId;


    $('#DivPipLinePopUp').load(url,function(e){
        $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;

    });

}

$(".select2multipleDeskClass").select2({
    theme: "bootstrap",
    placeholder: lang('select'),
    language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
    dir: $("html").attr("dir"),
    width: "100%"
});

$(".ChangeLeadAgentp").select2({
    theme: "bootstrap",
    placeholder: lang('choose_representative'),
    language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
    dir: $("html").attr("dir")
});

$('#ClassTypeClass').on('select2:select', function (e) {
    var selected = $(this).val();

    if(selected != null)
    {
        if(selected.indexOf('BA999')>=0){
            $(this).val('BA999').select2( {theme:"bootstrap", placeholder: lang('choose_class_type')} );
        }
    }

});

$('#BrandsTypeClass').on('select2:select', function (e) {
    var selected = $(this).val();

    if(selected != null)
    {
        if(selected.indexOf('BA999')>=0){
            $(this).val('BA999').select2( {theme:"bootstrap", placeholder: lang('choose_branch')} );
        }
    }

});

$('#TypeOption_0').trigger('click');

var confettiSettings = { target: 'SuccessConfetti', max: '500' };
var confetti = new ConfettiGenerator(confettiSettings);
confetti.render();

//CallButton
function CallToClient( ClientID, PipelineId )
{
    $(".CallClientdivb"+PipelineId).show();
    var callspinner = $.notify(
        {
            icon: 'fas fa-spinner fa-spin',
            message: lang('try_calling_client_few_moments'),
        },{
            type: 'warning',
        });

    $.ajax({
        type: "POST",
        url: "POS3/CallClient.php?u="+ClientID,
        success: function(dataN)
        {
            callspinner.close();
            $.notify(
                {
                    icon: 'fas fa-phone',
                    message: lang('call_is_being_made_pleasant_call'),
                },{
                    type: 'success',
                });
            $(".CallClientdivb"+PipelineId).hide();
            $(".CallClientdiv"+PipelineId).show();
            setTimeout(function() {$(".CallClientdiv"+PipelineId).hide( "bounce", { times: 3 }, "slow" )}, 10000);
        }
    });
}
//END CallButton

$('body').on('click', function (e) {
    //did not click a popover toggle or popover
    if ($(e.target).data('toggle') !== 'popover'
        && $(e.target).parents('.popover.in').length === 0) {
        $('[data-toggle="popover"]').popover('hide');
    }
});

$(function() {
    var time = function(){return'?'+new Date().getTime()};

    $('#AddNewLead').imgPicker({
    });
    $('#AddNewClient').imgPicker({
    });
    $('#AddNewTask').imgPicker({
    });
});

