$(document).ready(function () {
    $('.confim_popup').click(function () {
        $('#js-confirmation-modal .confim-btn').attr('data-id-task',$(this).attr('data-id-task'));
    })

    $('#js-confirmation-modal .confim-btn').click(function () {
        $(this).html('<i class="fal fa-circle-notch fast-spin">');
        var data = {
            id: $(this).attr('data-id-task'),
            fun : "TaskCompleted"
        }
        $.ajax({
            type: 'POST',
            url:'/office/ajax/Dashboard/DashboardAjax.php',
            data: data,
            success: function(res){
                // remove task
                $("#js-confirmation-modal .confim-btn i").remove();
                $("#js-confirmation-modal .confim-btn").text('כן');
                let activeCounter =  $("#js-tabs-dashboard .active span");
                $("#js-tabs-dashboard .active span").text(activeCounter.text() - 1);
                $(".js-task-action[data-id-task="+data.id+"]").addClass("d-none").removeClass('d-flex');
                $('#js-confirmation-modal').modal('hide');
            },
            error: function(xhr, ajaxOptions, thrownError){

            }
        });
    })
    $('.delete_popup').click(function () {
        $('#js-confirmation-modal-2 .confim-btn').attr('data-id-task',$(this).attr('data-id-task'));
    })
    $('#js-confirmation-modal-2 .confim-btn').click(function () {
        $(this).html('<i class="fal fa-circle-notch fast-spin">');
        var data = {
            id: $(this).attr('data-id-task'),
            fun : "TaskCanceled"
        }
        $.ajax({
            type: 'POST',
            url:'/office/ajax/Dashboard/DashboardAjax.php',
            data: data,
            success: function(res){
                // remove task
                $("#js-confirmation-modal-2 .confim-btn i").remove();
                $("#js-confirmation-modal-2 .confim-btn").text('כן');
                let activeCounter =  $("#js-tabs-dashboard .active span");
                $("#js-tabs-dashboard .active span").text(activeCounter.text() - 1);
                $(".js-task-action[data-id-task="+data.id+"]").addClass("d-none").removeClass('d-flex');
                $('#js-confirmation-modal-2').modal('hide');
            },
            error: function(xhr, ajaxOptions, thrownError){

            }
        });
    })
    $(".select2multipleDesk").select2({
        theme:"bootstrap",
        placeholder: lang('select'),
        language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
        dir: $("html").attr("dir")
    });
    $( ".select2ClientDesk" ).select2( {
        theme:"bsapp-dropdown",
        placeholder: lang('search_client'),
        language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
        allowClear: true,
        width: '100%',
        ajax: {
            url: 'SearchClient.php',
            type: 'POST',
            dataType: 'json',
            cache: true
        },
        minimumInputLength: 3,
        dir: $("html").attr("dir")
    });

    $('#SendStudioOption').on('select2:select', function (e) {
        var selected = $(this).val();

        if(selected != null)
        {
            if(selected.indexOf('BA999')>=0){
                $(this).val('BA999').select2({
                    theme:"bootstrap",
                    placeholder: lang('choose'),
                    language: $("html").attr("dir") == "rtl" ? "he" : "en",
                    dir: $("html").attr("dir")
                });
            }
            else if (selected.indexOf('BA000')>=0){
                $(this).val('BA000').select2({
                    theme:"bootstrap",
                    placeholder: lang('choose'),
                    language: $("html").attr("dir") == "rtl" ? "he" : "en",
                    dir: $("html").attr("dir")
                });
            }
        }

    });

    $(".ip-close").click(function(){
        $("#AddNewTask").hide();
    });

    $("body").on("click", "#js-link-edit-class", function () {
        var ClassId = $("#js-class-data").attr("data-classid")
        OpenClassPopup(ClassId)
    })

    $(".ShowClass").click( function()
    {
        var ClassId =  ($(this).attr('data-classid'));
        // var ClassAct = ($(this).attr('data-classact'));
        // var role = ($(this).attr('data-role'));
        //alert(ClassAct);
        //var_dump(ClassAct);

        $("#js-char-popup").modal("show");
        $("#js-char-popup-content").html($(".js-char-shimming-loader").html());
        $.ajax({
            url: '/office/characteristics-popup.php?id=' + ClassId,
            type: 'GET',
            success: function (response) {
                var jsonObj = $.parseJSON(response);
                $("#js-char-popup-content").html(jsonObj.js_char_popup_content);
                $("#js-modal-device-add .modal-body").html(jsonObj.js_modal_device_add);
            },
        });

        // if (role == 1){
        //     $("#js-char-popup").modal("show");
        //     $("#js-char-popup-content").html($(".js-char-shimming-loader").html());
        //
        //     $.ajax({
        //         url: '/office/characteristics-popup.php?id=' + ClassId,
        //         type: 'GET',
        //         success: function (response) {
        //             var jsonObj = $.parseJSON(response);
        //             $("#js-char-popup-content").html(jsonObj.js_char_popup_content);
        //             $("#js-modal-device-add .modal-body").html(jsonObj.js_modal_device_add);
        //         },
        //     });
        // } else if (ClassAct=='ClientList'){
        //     $( "#DivViewDeskInfo" ).empty();
        //     var modalcode = $('#ViewDeskInfo');
        //
        //     modalcode.modal('show');
        //
        //
        //     $('#ClosePOPUP').addClass("ClientCloseNew");
        //
        //     var url = 'new/ClientList.php?Id='+ClassId;
        //     // $('#DivViewDeskInfo').load(url);
        //
        //     $('#DivViewDeskInfo').load(url,function(e){
        //         $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);
        //         return false;
        //     });
        // }



    });

    $("#js-newClient").on("click", function() {
        $("#js-action-modal").modal("hide");
        NewClient();
    });

    $("#js-newLead").on("click", function () {
        $("#js-action-modal").modal("hide");
        NewClient('lead');
    });
    
    $("#js-applicationForm").on("click", function() {
        $("#js-action-modal").modal("hide");
        $("#SendClientForm").modal("show");
    });

    $("#js-newTask").on("click", function () {
        if ($('#js-task-popup').length > 0) {
            handleNewTask();
        } else {
            $("#js-action-modal").modal("hide");
            $("#AddNewTask").modal("show");
        }
    });
    
    $("#js-distributionList").on("click", function() {
        $("#js-action-modal").modal("hide");
        $("#SendClientPush").modal("show");
    });

    $('#js-membership-redirect').on('click', function() {
        location.href = '/office/Reports/membership.php';
    });

    $('#js-client-redirect').on('click', function() {
        location.href = '/office/Client.php?Act=0';
    });

    // count letters only for SMS option
    $("#letterCntLbl").hide();

    $("#sendPlatform").change(function() {
        var val = $("#sendPlatform").val();
        if (val == 1 || val == 0) {
            $("#letterCntLbl").hide();
            $("#subjectDiv").hide();
            $("#subject").val('');
            $("#subject").removeAttr('required');
            $("#subject").hide();
            $('.summernote').summernote('code', '');
            $(".summernote").summernote('destroy');
            $('.summernote').hide();
            $("#Message").prop('required',true);
            if(!$("#Message").is(":visible")) {
                $("#Message").show();
                $('#Message').val('');
            }
            if (val == 1) {
                $("#Message").trigger('keyup');
                $("#letterCntLbl").show();
            }
        }
        else {
            $("#letterCntLbl").hide();
            $("#subjectDiv").show();
            $("#subject").show();
            $("#subject").prop('required',true);
            $("#Message").removeAttr('required');
            $("#Message").hide();
            $(".summernote").show();
            $('.summernote').summernote({
                followingToolbar: false,
                placeholder: lang('type_messge_content_js'),
                tabsize: 2,
                height: 200,
                toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol']]
                ]
            });

        }
    });

    $("#Message").on('keyup', function(){
        let sms_limit = $("#sms-limit").val();
        let LengthM = $(this).val().length;
        let LengthT = Math.ceil(($(this).val().length)/sms_limit);
        $("#count").text(LengthM + ' ' +lang('sms_js') + ' ' + LengthT + ' ' + lang('messages'));

    });

    $('#minor_checkbox').on('click', function() {
        if ($(this).is(":checked")) {
            $("#minor-lead-div").show();
            $('#minor-lead-div').height(200);
            $("#lead_minor_firstName").prop('required', true);
            $("#lead_minor_lastName").prop('required', true);
            $("#lead_minor_lastName").val($('#lead_LastName').val());
            
        } else {
            $("#lead_minor_firstName").prop('required', false);
            $("#lead_minor_lastName").prop('required', false);
            $("#lead_minor_lastName").val();
            $('#minor-lead-div').height(0);
            setTimeout(() => {
                $("#minor-lead-div").hide();    
            }, 200);
        }
    });

    $('#PipeLineSelect').on('change', function() {
        var Id = this.value;
    
        $('#StatusSelect option')
            .hide() // hide all
            .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
            .show(); // and show them    
        
        $('#StatusSelect').val('');     
    });
    
    $("#AddNewLead").on('show.bs.modal', function(){
        $('#PipeLineSelect').trigger('change');        
    });
});


function TaskPopup(id,ClientID) {
    $("#AddNewTask").show();
    $('#AddEditTaskCalendarId').val(id);
    $.ajax({
        url:'action/GetCalendarInfo.php?Id='+id+'&ClientId='+ClientID,
        dataType : 'json',

        success  : function (response) {
            ClientDiv.style.display = "none";
            $('#ChooseFloorForTask').val(response.Floor)
            $('#CalTaskTitle').val(response.Title);
            $('#AddEditTaskClientId').val(response.ClientId);
            $('#ClientName').val(response.ClientName);
            if (response.ClientId!='0'){
                $('#ClientPhone').html('<i class="fas fa-phone-square fa-fw"></i> '+response.ClientPhone+' ');
            }
            else {
                $('#ClientPhone').html('<i class="fas fa-phone-square fa-fw"></i>'+lang('without_phone_cal'));
            }
            ClientNameDiv.style.display = "block";
            $('#AddEditTaskPipeLineId').val(response.Floor);

            $('#CalTypeOption').val(response.Type);
            $('#SetDate').val(response.StartDate);
            $('#SetTime').val(response.StartTime);
            var FixToTimes = moment(response.StartTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;
            $('#SetToTime').prop('min', FixToTimes);
            $('#SetToTime').val(response.EndTime).trigger('change');
            $('#CalLevel').val(response.Level);
            $('#ChooseAgentForTask').val(response.AgentId).trigger('change');
            $('#CalRemarks').val(response.Content);
            $('#CalTaskStatus').val(response.Status);
            $('#CalTaskStatus').prop('disabled', false);

            if (response.GroupPermission==null || response.GroupPermission=='' || response.GroupPermission=='(NULL)') {
                $("#SendStudioOption").val(['<?php echo Auth::user()->role_id; ?>']).trigger("change");
            } else {
                var values = response.GroupPermission;
                var selectedValues = values.split(",");
                $("#SendStudioOption").val(selectedValues).trigger("change");
            }

        }
    });
}