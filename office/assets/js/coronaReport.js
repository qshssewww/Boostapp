$(document).ready(function(){

    $(".js-green-pass-date-reset").on('click', function() {
        $(this).append(' <i class="fas fa-spinner-third fast-spin">');
        let data = {"fun": "resetGreenPassDate"};
        const popup = $('#js-reset-green-pass-modal');
        $.ajax({
            url: "ajax/ajaxCorona.php",
            type: "POST",
            data: data,
            success: function (response) {
                $(".js-green-pass-date-reset i").remove();
                console.log(response);
                $.notify(
                    { icon: 'fas fa-check-circle', message: lang('action_done') },
                    { type: 'success'}
                );
                popup.modal('hide');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".js-green-pass-date-reset i").remove();
                console.log(textStatus, errorThrown);
                $.notify(
                    { icon: 'fas fa-times-circle', message: lang('error_oops_something_went_wrong') },
                    { type: 'danger'}
                );
                popup.modal('hide');
            }
        })
    })

    $(".js-green-pass").change(function () {
        let settingValue = $(this).val();
        let data = {"corona" : settingValue};
        $.ajax({
            url: "/office/ajax/ajaxCorona.php",
            type: "post",
            data: data,
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

});