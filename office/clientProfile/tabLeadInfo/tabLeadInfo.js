let clientProfile_tabLeadInfo = {
    reLead: function (elem, leadId, pipeId) {
        elem = $(elem);

        let btnText = elem[0].innerText;

        elem.html('<i class="fal fa-spinner-third fa-spin fa-lg container-fluid"></i>');
        elem.prop('disabled', true);
        $.ajax({
            url: 'action/UpdatePipeNew.php',
            type: 'POST',
            data: {
                'LeadId': leadId,
                'PipeId': pipeId,
            },
            complete: function (){
                elem.html(btnText);
                elem.prop('disabled', false);
                window.location.reload();
            }
        });
    }
}
