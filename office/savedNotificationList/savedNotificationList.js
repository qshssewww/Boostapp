let savedNotificationList = {
    popUp: '#js-savedNotificationSettings',
    settingsVisibility:function (isVisible){
        if(!isVisible){
            $(this.popUp).fadeIn('fast');
        } else {
            $(this.popUp).fadeOut('fast');
        }
    },
    selectVisibility: function (elem){
        elem = $(elem)[0];
        let selectDiv = (elem.id == 'toggle2') ? $(this.popUp).find('.collapse:first') : $(this.popUp).find('.collapse:last');

        elem.checked ? selectDiv.slideDown(200) : selectDiv.slideUp(200);
    },
    submitSave: function (){
        const popUp = $(this.popUp);
        const btn = popUp.find('button:last');

        btn.prop('disabled', true);
        btn.html('<i class="fal fa-spinner-third fa-spin fa-lg"></i>');

        $.ajax({
            url: 'savedNotificationList/savedNotificationPost.php',
            type: 'POST',
            data: {
                'sendSMS': popUp.find('#toggle1')[0].checked ? 1 : 2,
                'classWeek': popUp.find('#toggle2')[0].checked ? 1 : 2,
                'classWeekMonth': popUp.find('select:first')[0].selectedIndex + 1,
                'waitingListNight': popUp.find('#toggle3')[0].checked ? 1 : 0,
                'fromTime': popUp.find('input[type=time]:first').val(),
                'toTime': popUp.find('input[type=time]:last').val(),
                'sendNotification': popUp.find('#toggle4')[0].checked ? 1 : 0,
            },
            success: function (){
                $.notify(
                    {icon: 'fas fa-check-circle', message: lang('action_done')},
                    {type: 'success'}
                );
                savedNotificationList.settingsVisibility(true);
            },
            error: function (){
                $.notify(
                    {icon: 'fas fa-times-circle', message: lang('error_oops_something_went_wrong')},
                    {type: 'danger'}
                );
            },
            complete: function (){
                btn.prop('disabled', false);
                btn.text('שלח');
            }
        })
    },
}

$(document).ready(function (){
    $(savedNotificationList.popUp).find('select').select2({
        theme: "bsapp-dropdown",
        minimumResultsForSearch: -1,
    });

    $(savedNotificationList.popUp).find('input[type=checkbox]').change();
});

