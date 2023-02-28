var selectedUser = "";
var charPopup = {
    class_info: {},
    init: function () {
        $("#js-modal-add-user").on("shown.bs.modal", function () {
            $(".js-user-search").select2("open");
        })
    },
    showError: function ($elem, title, description, showReloadButton = false) {
        $elem.html($(".js-char-error-html").html());
        $elem.find(".js-error-title").html(title);
        $elem.find(".js-error-description").html(description);
        if (showReloadButton) {
            $elem.find(".js-error-btn-reload").show();
        } else {
            $elem.find(".js-error-btn-reload").hide();
        }
    },
    showClientInfo: function (clientId, activityId, actId) {
        $("#js-modal-user-content").html($(".js-char-shimming-loader").html())
        $("#js-modal-user").modal("show");

        if (!clientId) {
            charPopup.showError($("#js-modal-user-content"), lang('error_oops_something_went_wrong'), lang('something_wrong_cal'), false);

            /** error show :: end **/
            return;
        }
        if (!actId) {
            charPopup.showError($("#js-modal-user-content"), lang('error_oops_something_went_wrong'), lang('something_wrong_cal'), false);

            /** error show :: end **/
            return;
        }
        const data = {
            clientId: clientId,
            activityId: activityId,
            actId: actId
        };

        $.ajax({
            url: '/office/partials-views/char-popup/modal-client-info.php',
            type: 'GET',
            data: data,
            success: function (response) {
                $("#js-modal-user-content").html(response);
            },
            error: function () {
                charPopup.showError($("#js-modal-user-content"), lang('error_oops_something_went_wrong'), lang('something_wrong_cal'), false);
            }
        });

    },
    showDevicesPopup: function (actId) {
        if (!actId)
            return 'Must have actId';
        $("#js-modal-device-add-content").load('/office/partials-views/char-popup/modal-select-device.php', {actId: actId});
    },
    getRowFormatHtml: function (client, isLog = false) {
        if (client) {
            if (!client.companyName)
                return 'companyName Missing';
            else if (!client.firstName)
                return 'firstName Missing';
            else if (!client.hexCode)
                return 'hexCode Missing';
            else if (!client.clientId)
                return 'clientId Missing';
            else {
                const clientTr = $(`tr[data-clientid="${client.clientId}"]`);

                if (!client.lastName)
                    client.lastName = '';
                let pic;
                if (client.profileImage)
                    pic = $(`<img alt="${client.clientId}" src="/camera/uploads/large/${client.profileImage}" alt="not found">`);
                else
                    pic = $(`<img alt="${client.clientId}" src="https://ui-avatars.com/api/?length=1&name=${client.firstName}&background=f3f3f4&color=000&font-size=0.5">`)
                pic.addClass("w-40p h-40p rounded-circle mie-8");
                let div = $('<div class="d-flex align-items-center position-relative"></div>');
                div.append(pic)
                        .append(`<div>${client.companyName}</div>`)
                        .append(`<a class="stretched-link js-modal-user" data-client-id="${client.clientId}" data-act-id="${clientTr.data('actid')}"></a>`);
                let addId = isLog ? '' : `id="conclusion-${client.clientId}"`;
                return $(`<tr ${addId} data-client-id="${client.clientId}" data-act-id="${clientTr.data('actid')}"></tr>`).append(
                        $('<td></td>').append(
                        $('<div class="button-container d-flex"></div>').append(div)
                        )
                        );
            }
    }
    },
    removeClientFromConclusion: function (clientId) {
        const tr = $(`#conclusion-${clientId}`);
        if ($(tr).text()) {
            const tabName = (tr.closest('div').attr('id')).replace('section-', '');
            $(`#${tabName}`).children('span').text(function () {
                return parseInt($(this).text()) - 1;
            });
            tr.remove();
        }
    },
    addClientToConclusion: function (status, data) {
        charPopup.removeClientFromConclusion(data.clientId);
        if (status == '8') {
            $('#missing-clients').children('tbody').append(charPopup.getRowFormatHtml(data));
            $('#js-participants-tab-2').children('span').text(function () {
                return parseInt($(this).text()) + 1;
            })
        } else if (status == '2') {
            $('#present-clients').children('tbody').append(charPopup.getRowFormatHtml(data));
            $('#js-participants-tab-1').children('span').text(function () {
                return parseInt($(this).text()) + 1;
            })
        } else if (status == '4') {
            const lateCancelRow = charPopup.getRowFormatHtml(data);
            $('#late-cancel-clients').children('tbody').append(charPopup.setLateCancelButton(lateCancelRow));
            $('#js-participants-tab-3').children('span').text(function () {
                return parseInt($(this).text()) + 1;
            })
        } else if (status == '12') {
            $('#regular-assignment').children('tbody').append(charPopup.getRowFormatHtml(data));
            $('#js-participants-tab-4').children('span').text(function () {
                return parseInt($(this).text()) + 1;
            })
        }
    },
    setLateCancelButton: function (tr) {
        tr.find('.button-container')
                .addClass('justify-content-between')
                .append('<div class="d-flex align-items-center">' +
                        '<button class="btn btn-outline-secondary btn-sm rounded-pill" data-status="3" onclick="charPopup.removeClientLateCancel(this)">' +
                        lang('cancel_charge') +
                        '</button>' +
                        '</div>');
        return tr;
    },
    addEventToLog: function (logData, clientData) {
        let tr = charPopup.getRowFormatHtml(clientData, true);
        if (typeof tr === 'string')
            return;
        let cellClass = "p-10";
        let registeredClients = $('#client-reg').text();
        let maxClients = $('#client-max').text();
        let statusCell = $('<td></td>').append($('<div></div>').html(`<div class="${cellClass}">${logData.status}</div>`));
        let userNameCell = $('<td></td>').append($('<div></div>').html(`<div class="${cellClass}">${logData.userName}</div>`))
        let timeCell = $('<td></td>').append($('<div></div>').html(`<div class="${cellClass}">${logData.date}</div>`));
        let clientRegister = $('<td></td>').append($('<div></div>').html(`<div class="js-log-reg-client ${cellClass}">${registeredClients}/${maxClients}</div>`));
        tr.append(statusCell).append(userNameCell).append(timeCell).append(clientRegister);
        $('#log-table').prepend(tr);
    },
    clearSelection: function () {
        for (let i = 1; i < 3; i++) {
            var $parent = $(`table[data-bottom-bar="js-participant-tab-${i}"]`)
            //sequence of events :: begin
            //reverted the selection sequence
            $parent.removeClass("bsapp-disabled-actions");
            $parent.find(".js-img-to-check").removeClass("bsapp-check-shown");
            $parent.find(".js-img-to-check img").removeClass("d-none");
            $parent.find(".js-img-to-check .form-check").addClass("d-none");
            $parent.find(".js-tbl-select-all").addClass("d-none");
            $parent.find(".js-fa-user-friends").show();
            $(".js-bottom-action-bar").addClass("d-flex").removeClass("d-none");
            $('[data-context="' + $parent.attr("data-bottom-bar") + '"').removeClass("d-flex").addClass("d-none");
            $parent.find(".js-check-select-all").prop("checked", false);
            $parent.find('input[id="customSelectSample"]').prop("checked", false);
        }
    },
    reloadPopup: function (classId) {
        $("#js-char-popup-content").html($(".js-char-shimming-loader").html());
        $.ajax({
            url: '/office/characteristics-popup.php?id=' + classId,
            type: 'GET',
            success: function (response) {
                var jsonObj = JSON.parse(response);
                $("#js-char-popup-content").html(jsonObj.js_char_popup_content);
                $("#js-modal-device-add .modal-body").html(jsonObj.js_modal_device_add);
                $(`#client-reg-card${classId}`).text($('#client-reg').text());
            },
            error: function () {
                charPopup.showError($("#js-modal-user-content"), lang('error_oops_something_went_wrong'), lang('something_wrong_cal'), false);
            }
        });
    },
    updateWaitingCount(clientWaiting, classId) {
        if (clientWaiting == 0)
            $(".js-datatable-draggable").prepend($('#js-no-clients-waiting').html());

        $('#client-waiting').text(clientWaiting);
        $(`#client-waiting-card${classId}`).html(clientWaiting);
    },
    showMaxClientPopup(actIdArr, classId, overClients, isSingle) {
        $("#js-modal-over-max-client-content").load('/office/partials-views/char-popup/modal-over-max-client.php', {
            actIdArr: actIdArr,
            classId: classId,
            overClients: overClients,
            isSingle: isSingle
        });
    },
    addClientToActiveTrainee(actId) {
        $(`tr[data-actid="${actId}"]`).remove();
        const tbl = $('table[data-bottom-bar="js-participant-tab-1"]');
        $.get('/office/partials-views/char-popup/create-active-trainee.php',
                {
                    actId: actId,
                    index: $('#company-details').find('tr[data-actid]').length
                },
                function (response) {
                    $(".no-clients").addClass('d-none');
                    tbl.children('tbody').append(response);
                    $('[data-toggle="tooltip"]').tooltip();
                });
    },
    addClientToWaitingList(actId, clientWaiting, classId) {
        $(`tr[data-actid="${actId}"]`).remove();
        const tbl = $('table[data-bottom-bar="js-participant-tab-2"]');
        $.get('/office/partials-views/char-popup/create-waiting-trainee.php', {actId: actId, clientWaiting: clientWaiting}, function (response) {
            $(".no-clients").addClass('d-none');
            tbl.children('tbody').append(response);

            $('#client-waiting').text(clientWaiting);
            $(`#client-waiting-card${classId}`).html(clientWaiting);

            if (clientWaiting == 1) {
                let counter = $(`#client-waiting-card${classId}`);
                let counterDiv = counter.closest('div')
                var eIconPause = $('<i class="far fa-pause-circle bsapp-fs-16 mie-4"></i>');
                counterDiv.attr('class', 'bsapp-event-pause  bsapp-min-w-50p d-flex align-items-center text-white');
                counter.html(clientWaiting);
                counterDiv.prepend(eIconPause);
            }
            $('[data-bottom-bar="js-participant-tab-2"]').find('.dataTables_empty').remove();
            $('[data-toggle="tooltip"]').tooltip();
        });
    },
    showNoWaitingClient(clientWaiting) {
        if (clientWaiting == 0)
            $(".js-datatable-draggable").prepend($('#js-no-clients-waiting').html());
    },
    showCanceledToActivePopup(traineesCount) {
        $('#js-modal-canceled-to-active-content').load('/office/partials-views/char-popup/modal-canceled-to-active.php', {
            traineesCount: traineesCount
        });
    },
    initSecondTabDataTable: function () {
        $(".js-datatable-draggable").dataTable({
            "dom": "t",
            sStripeEven: '',
            sStripeOdd: '',
            paging: false,
            rowReorder: {
                selector: ".js-row-draggable"
            },
            "language": {
                "emptyTable": $('#js-no-clients-waiting').find('div').text()
            },
            "orderable": false,
            columnDefs: [
                {"targets": [0, 1, 2, 3, 4], "sortable": false},
            ],
            "initComplete": function (settings, json) {

                $('.js-datatable-draggable').on('row-reorder.dt', function (e, details, edit) {
                    var send_data = new Array();
                    $(".js-datatable-draggable tbody tr").each(function (x) {
                        send_data.push({id: $(this).attr("data-actid"), order: x})
                    });

                    if (send_data.length > 1) {
                        var formdata = {fun: 'orderWaitingList', orderArr: send_data};
                        $.ajax({
                            url: '/office/ajax/Trainees.php',
                            data: formdata,
                            type: "POST",
                            // processData: false,
                            // contentType: false,
                            success: function (response) {
                                if (response.Status == 'Success') {
                                    $.notify({
                                        // options
                                        message: (response.Message) ? response.Message : "Updated"
                                    }, {
                                        // settings
                                        type: 'success',
                                        z_index: 2000,
                                    });
                                } else {
                                    $.notify({
                                        // options
                                        message: (response.Message) ? response.Message : "Error"
                                    }, {
                                        // settings
                                        type: 'success',
                                        z_index: 2000,
                                    });
                                }

                            },
                            error: function () {
                                $.notify({
                                    // options
                                    message: 'Error'
                                }, {
                                    // settings
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        });
                    }
                });

            }
        });
    },
    close: function (elem, force = false) {
        var $elem = $(elem);
        if (force == true) {
            $("#js-char-popup").modal("hide");
            $elem.parents(".modal").modal("hide");
            return false;
        }
        // var wl = $elem.attr("data-waiting-list");
        var wl = parseInt($('#client-waiting').text());
        var total = parseInt($elem.attr("data-total"));
        // var regns = $elem.attr("data-registrations");
        var regns = parseInt($("#client-reg").text());

        if (!jQuery.isEmptyObject(charPopup.class_info) && charPopup.class_info.classStatus == 0 && moment().isBefore(charPopup.class_info.classDate)) {
            if (wl > 0 && regns < total) {
                // $elem.parents(".modal").modal("hide");
                if (typeof calendar_data !== "undefined" && parseInt(calendar_data.waitingPopUp) == 0) {
                    charPopup.runWaitingList();
                    // todo: show loader on auto action
                } else {
                    $("#js-charpopup-confirmation-modal").modal("show");
                    return false;
                }
            }
        }
        $elem.parents(".modal").modal("hide");
    },
    runWaitingList: function (elem) {
        const $parent = $('#js-charpopup-confirmation-modal');
        $parent.showModalLoader();
        var class_id = $('#js-class-data').attr('data-classid');
        if (class_id) {
            $.ajax({
                url: '/office/new/RunWaitingListNew.php',
                data: {"ClassId": class_id},
                type: 'POST',
                success: function (res) {
                    var result = JSON.parse(res);
                    if (result && result.Status == "Success") {

                        $("#js-charpopup-confirmation-modal").modal("hide");
                        charPopup.reloadPopup(class_id);
                        GetCalendarData();
                        var message = (result.Message && result.Message != "") ? result.Message : lang('action_done_beepos');
                        $.notify({
                            // options
                            message: message
                        }, {
                            // settings
                            type: 'success',
                            z_index: 2000,
                        });
                    } else {
                        $.notify({
                            message: (result.Message && result.Message != "") ? result.Message : lang('error_detected_cal')
                        }, {
                            type: 'danger',
                            z_index: 2000,
                        });

                        $("#js-charpopup-confirmation-modal").modal("hide");
                    }
                },
                error: function () {
                    // $parent.find(".js-loader").remove();
                    $.notify({
                        message: lang('error_detected_cal')
                    }, {
                        type: 'danger',
                        z_index: 2000,
                    });
                    $("#js-charpopup-confirmation-modal").modal("hide");
                },
                complete: function () {
                    $parent.find('.modal-content').find(".js-loader").remove();
                }
            });
        }

    },
    updateClassTrainersCount: function (classId, data) {
        // active count
        $('#client-reg').text(data.registered);
        $('#client-reg-card' + classId).text(data.registered);
        // waiting list count
        $('#client-waiting').text(data.waiting);
        if ($('#client-waiting-card' + classId).length) {
            $('#client-waiting-card' + classId).text(data.waiting);
        }
        // update conclusion tab count & remove
        $('#js-participants-tab-4').children('span').text(data.regularRegistered);
    },
    clientAddToWaitingList: function (elem) {
        var $tr = $(elem).parents("tr");
        var actId = $tr.attr("data-actid");
        var formdata = {fun: 'moveToWaitingList', actId: actId};
        $(".js-tabs-n-tables").html($(".js-window-loader-stripe-3").html() + $(".js-window-loader-stripe-3").html());
        $.ajax({
            url: "ajax/Trainees.php",
            data: formdata,
            type: "POST",
            success: function (response) {
                $(".js-tabs-n-tables").html(response.html);
                $("#js-pill-tab-2").trigger("click");
                GetCalendarData();
            }
        });
    },
    setMiniDropForConclusionTab: function () {
        $(".js-tab-select").on("change", function () {
            var selected = $(this).val();
            $("#" + selected).click();
        });
        /*   $(".js-tab-select:not(.select2-hidden-accessible)").select2({
         dropdownParent: $("#js-char-popup"),
         theme: "bsapp-dropdown",
         minimumResultsForSearch: -1
         }).on("select2:selecting", function (e) {
         var selected = e.params.args.data.id;
         $("#" + selected).click();
         }); */
    },
    removeClientLateCancel: function (elem) {
        const modal = $('#js-modal-late-cancel');
        let actId;
        if (modal.data('bs.modal')?._isShown) {
            actId = modal.data('actId');
        } else {
            actId = $(elem).closest('tr').data('actId');
        }
        const tr = $('tr[data-actid="' + actId + '"]');
        const status = $(elem).data('status');
        const classId = charPopup.class_info.classid;

        $.ajax({
            method: 'POST',
            url: '/office/ajax/Trainees.php',
            data: {
                fun: 'removeClientFromClass',
                actId: [actId],
                classId: classId,
                status: status
            },
            success: function (result) {
                charPopup.removeClientFromConclusion(tr.data('clientid'));
                charPopup.addClientToConclusion(status, result.data[0])
                $('#client-reg').html(result.Message.clientRegistered);
                charPopup.addEventToLog(result.logData, result.data[0]);
                $(`#client-reg-card${classId}`).html(result.Message.clientRegistered);
                let total_trainers = parseInt($('#total_trainers').text());
                $('#total_trainers').html(total_trainers - 1);
                if (result.Message.clientRegistered == 0)
                    $('#js-no-clients').removeClass('d-none');

                tr.hide('slow', function () {
                    tr.remove();
                });

                modal.modal('hide');
            }
        })
    },
    showLateCancelModal: function (actId, classId) {
        $('#js-modal-late-cancel').modal('show');
        $('#js-modal-late-cancel').data('actId', actId).data('classId', classId);
    },
    submitEditClassContent: function (ClassId, elem) {
        setHtmlToLoader(elem);
        const RemarksContent = $('#Remarks').summernote('code');
        const RemarksStatus = $('#RemarksStatus').val();
        const SaveOptions = $('#js-save-options').find('input[type="checkbox"]:checked').map(function () {
            return this.value;
        });

        $.ajax({
            method: 'POST',
            url: '/office/ajax/Trainees.php',
            data: {
                fun: 'EditClassRemarks',
                ClassId: ClassId,
                Remarks: RemarksContent,
                RemarksStatus: RemarksStatus,
                SaveOptions: JSON.stringify(SaveOptions)
            },
            success: function () {
                $('#js-modal-class-content').modal('hide');
                $('#update-class-content').trigger('reset');
                $('#Remarks').summernote('code', '');
                $('#js-class-remarks').html(RemarksContent);
                $(elem).text(lang('save'));
            }
        });
    },
    reloadSingleMember(classId){
        if ($(`#client-max`).text() == 1)
            GetCalendarData();
    }
}
if ($.fn.dataTable) {
    $.extend($.fn.dataTable.ext.classes, {
        sStripeEven: '', sStripeOdd: ''
    });
}

charPopup.init();

var modalUserPopup = {
    showCal: function (elem){
        elem = $(elem).find('input');
        $(elem).datepicker({
            dateFormat: 'dd/mm/yy',
        });
        $(elem).datepicker('show');
    },
    addNewTextarea: function () {
        var js_new_textarea = $(".js-html-textarea").html();
        if ($('.js-textarea-edit-mode.d-flex').length <= 1) {
            $(".js-textarea-newly-added:last-child")[0].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
            $("#js-modal-user-content .js-scroll-height").append(js_new_textarea);
        }
    },
    editModeOn: function (elem) {
        var $parent = $(elem).parents(".js-textarea-newly-added");
        var $edit = $parent.find(".js-textarea-edit-mode");
        var $view = $parent.find(".js-textarea-crm-div");
        var date_span = $view.find(".js-till-date").attr("formatedate");
        var date = date_span ? date_span : null;
        $edit.removeClass("d-none").addClass("d-flex");
        $view.removeClass("d-flex").addClass("d-none");
        $edit.find("textarea").text($view.find(".js-content-remarks").text());
        if (date) {
            $edit.find(".js-datepicker").val(date);
        }
    },
    hideAddedTextArea: function (elem) {
        var $parent = $(elem).parents(".js-textarea-newly-added");
        var $edit = $parent.find(".js-textarea-edit-mode");
        var $view = $parent.find(".js-textarea-crm-div");
        if ($parent.attr("data-origin") == "js") {
            $parent.remove();
            return;
        }
        $view.removeClass("d-none").addClass("d-flex");
        $edit.removeClass("d-flex").addClass("d-none");
    },
    updateTextContent: function (elem) {
        var $parent = $(elem).parents(".js-textarea-newly-added");
        $parent.prepend($(".js-window-loader-3").html());
        var $edit = $parent.find(".js-textarea-edit-mode");
        var $view = $parent.find(".js-textarea-crm-div");
        var $js_datepicker = $edit.find(".js-datepicker");
        var $textarea = $edit.find("textarea");

        var id = $parent.attr("data-id");
        var till_date = $js_datepicker.val() ? $js_datepicker.val() : null;
        var remarkToUpdate = $textarea.val();
        var clientIdInput = $('body').find('.clientIdInput').val();

        $textarea.removeClass("border-danger");
        $js_datepicker.removeClass("border border-danger").addClass("border border-light")

        if (remarkToUpdate.trim() == "") {
            $textarea.addClass("border-danger")
            $parent.find(".js-loader").remove();
            return false;
        }

        /*if (till_date.trim() == "") {
         $js_datepicker.removeClass("border border-light").addClass("border border-danger")
         $parent.find(".js-loader").remove();
         return false;
         }*/


        till_date = till_date ? moment(till_date, 'DD/MM/YYYY').format('YYYY-MM-DD') : null;

        if ($parent.attr("data-origin") == "back") {
            if ($parent.attr("data-type") == "medical") {
                var data = {
                    fun: "editMedicalContent",
                    ClientId: clientIdInput,
                    MedicalId: id,
                    TillDate: till_date,
                    Content: remarkToUpdate.trim(),
                };
            } else {
                var data = {
                    fun: "editCrmNoticeRemark",
                    ClientId: clientIdInput,
                    CrmId: id,
                    TillDate: till_date,
                    Remarks: remarkToUpdate.trim(),
                };
            }
        } else {
            var data = {
                fun: "AddCrmNotice",
                ClientId: clientIdInput,
                TillDate: till_date,
                Remarks: remarkToUpdate.trim()
            }
        }

        $.ajax({
            url: '/office/ajax/Trainees.php',
            data: data,
            type: 'POST',
            success: function (res) {
                $parent.find(".js-loader").remove();
                if (res && res.Status == "Success") {
                    $parent.replaceWith(res.html);
                    var $tr = $('tr[data-clientid="' + clientIdInput + '"]');
                    if (!$tr.find('.js-client-crm-icon').length) {
                        $tr.find('.js-client-icons-div').append('<a href="#" class="mie-5 js-client-crm-icon bsapp-fs-14"' +
                                ' data-toggle="tooltip" data-placement="top"><i class="fal fa-clipboard text-warning"></i></a>');
                    }
                } else {
                    $.notify({
                        message: 'Error'
                    }, {
                        type: 'danger',
                        z_index: 2000,
                    });
                }
            },
            error: function () {
                $parent.find(".js-loader").remove();
                $.notify({
                    message: 'Error'
                }, {
                    type: 'danger',
                    z_index: 2000,
                });
            }
        });
    },
    confirmDelete: function (elem) {
        const removeOption = $('[name="removeOption"]:checked');
        if (!removeOption.val())
            return notInputBorderDanger($('#removeOptionContainer'));

        var id = $(".js-remove-regular-assignment").attr("data-regular-id");
        var client_id = $("input.clientIdInput").val();
        var class_id = $('#js-class-data').attr('data-classid');


        let data = {"fun": "removeRegularAssignment", "regularClassId": id, "client_id": client_id, "class_id": class_id};

        let removeOptionInput = null;
        switch (removeOption.val()){
            case 'all':
                console.log('all')
                break;
            case 'by-date':
                removeOptionInput = removeOption.siblings('label').children('input')
                if (!removeOptionInput.val())
                    return inputBorderDanger(removeOptionInput);
                else
                    data.endDate = removeOptionInput.val();
                break;
            case 'by-quantity':
                removeOptionInput = removeOption.siblings('label').children('input')
                if (!removeOptionInput.val())
                    return inputBorderDanger(removeOptionInput);
                else
                    data.quantity = removeOptionInput.val();
                break;
        }

        $(elem).addClass("disabled");
        $(elem).find(".js-loader-spin").show();


        $.ajax({
            url: 'ajax/Trainees.php',
            type: "POST",
            data: data,
            success: function (res) {
                $(elem).removeClass("disabled");
                $(elem).find(".js-loader-spin").hide();
                $(elem).parents(".modal").modal("hide");
                if (res.Status == "Success") {
                    if (res.data.isRegularRemoved)
                        $(".js-regular-assignment-div").remove();
                    $("tr[data-clientid='" + client_id + "']").remove();
                    if (res.data) {
                        charPopup.updateClassTrainersCount(class_id, res.data);
                        $('#regular-client-' + client_id).remove();
                    }
                    charPopup.reloadPopup(class_id);
                    var message = (res.Message) ? res.Message : 'Deleted successfully';
                    $.notify({
                        // options
                        message: message
                    }, {
                        // settings
                        type: 'success',
                        z_index: 2000,
                    });
                } else {
                    var message = res.Message ? res.Message : "Error";
                    $.notify({
                        // options
                        message: message
                    }, {
                        // settings
                        type: 'danger',
                        z_index: 2000,
                    });

                }
            },
            error: function () {
                $(elem).removeClass("disabled");
                $(elem).find(".js-loader-spin").hide();
                $.notify({
                    // options
                    message: "Error"
                }, {
                    // settings
                    type: 'danger',
                    z_index: 2000,
                });
            }
        });
    },
    remove: function (elem) {
        var $parent = $(elem).parents(".js-textarea-newly-added");
        $parent.prepend($(".js-window-loader-3").html());
        var $edit = $parent.find(".js-textarea-edit-mode");
        var $view = $parent.find(".js-textarea-crm-div");
        var id = $parent.attr("data-id");
        var clientId = $('body').find('.clientIdInput').val();

        if ($parent.attr("data-type") == "medical") {
            var data = {
                fun: "removeClientMedical",
                clientId: clientId,
                medicalId: id
            };

        } else {
            var data = {
                fun: "removeClientCrm",
                clientId: clientId,
                crmId: id
            };

        }
        $.ajax({
            url: '/office/ajax/Trainees.php',
            data: data,
            type: 'POST',
            success: function (response) {
                if (response && response.Status == "Success") {
                    var $tr = $('tr[data-clientid="' + clientId + '"]');
                    $parent.fadeOut();
                    if ($parent.attr("data-type") == "medical") {
                        var $medical_elems = $('#js-modal-user-content .js-textarea-newly-added[data-type="medical"]:visible');
                        if ($medical_elems.length <= 1) {
                            $tr.find('.js-client-medical-icon').remove();
                        }
                    } else { /// crm notice removed
                        var $crm_elems = $('#js-modal-user-content .js-textarea-newly-added[data-type="crm"]:visible');
                        if ($crm_elems.length <= 1) {
                            $tr.find('.js-client-crm-icon').remove();
                        }
                    }

                } else {
                    var message = res.Message ? res.Message : "Error";
                    $.notify({
                        // options
                        message: message
                    }, {
                        // settings
                        type: 'danger',
                        z_index: 2000,
                    });
                }

            },
            error: function () {
                var message = res.Message ? res.Message : "Error";
                $.notify({
                    // options
                    message: message
                }, {
                    // settings
                    type: 'danger',
                    z_index: 2000,
                });
            }
        });
    },
    showMaxClientPopup(actIdArr, classId, overClients, isSingle) {
        $("#js-modal-over-max-client-content").load('/office/partials-views/char-popup/modal-over-max-client.php', {
            actIdArr: actIdArr,
            classId: classId,
            overClients: overClients,
            isSingle: isSingle
        });
    },
    showCanceledToActivePopup(traineesCount) {
        $('#js-modal-canceled-to-active-content').load('/office/partials-views/char-popup/modal-canceled-to-active.php', {
            traineesCount: traineesCount
        });
    },
    //get array of objects {classId: classId, classCount: {clientRegister, clientWaiting}), updating class cards (on calendar)
    updateClassCardRegularAssignment(lessonArr) {
        for (let lesson of lessonArr) {
            $(`#client-reg-card${lesson.classId}`).html(lesson.clientCount.clientRegistered);
            $(`#client-waiting-card${lesson.classId}`).html(lesson.clientCount.clientWaiting);
        }
    },
}

var modalOverMaxRegular = {
    openModal: function (data) {
        $('#js-modal-over-max-regular').modal('show');
        $('#js-modal-over-max-regular-data').data('formData', data);
    },
    forceAssignment: function (button, status) {

        setHtmlToLoader(button);
        let data = JSON.parse($('#js-modal-over-max-regular-data').data('formData'));
        Object.assign(data, {overrideStatus: status});

        $.ajax({
            method: 'POST',
            url: 'ajax/Trainees.php',
            data: {
                fun: 'HandleClassAssignment',
                data: JSON.stringify(data)
            },
            success: function (res) {
                $('#js-modal-over-max-regular').modal('hide');
                modalOverLimitation.assignIfSucceed(res, data.classId);
            }
        }).then(function () {
            button.html(button.data('buttonText'));
        });
    }
}

var modalAssignOverMaxOrWaitingList= {
    openModal: function (data) {
        $('#js-modal-over-max-or-waiting-list').modal('show');
        $('#js-modal-over-max-or-waiting-list-data').data('formData', data);
    },
    forceAssignment: function (button, status) {
        setHtmlToLoader(button);
        let data = JSON.parse($('#js-modal-over-max-or-waiting-list-data').data('formData'));
        Object.assign(data, {overrideStatus: status});

        $.ajax({
            method: 'POST',
            url: 'ajax/Trainees.php',
            data: {
                fun: 'HandleClassAssignment',
                data: JSON.stringify(data)
            },
            success: function (res) {
                $('#js-modal-over-max-or-waiting-list').modal('hide');
                modalOverLimitation.assignIfSucceed(res, data.classId);
            }
        }).then(function () {
            button.html(button.data('buttonText'));
        });
    }
}




var modalOverLimitation = {
    openModal: function (data, assignmentRes) {
        $('#js-modal-over-limitation').modal('show');
        $('#js-modal-over-limitation-data').data('formData', data);
        $.ajax({
            method: 'GET',
            url: '/office/partials-views/char-popup/modal-over-limitation.php',
            data: {
                text: assignmentRes.Message
            },
            success: function (content) {
                $('#js-modal-over-limitation-content').html(content);
            }
        });
    },
    forceAssignment: function (button) {
        setHtmlToLoader(button);
        let data = JSON.parse($('#js-modal-over-limitation-data').data('formData'));
        Object.assign(data, {override: true});

        $.ajax({
            method: 'POST',
            url: 'ajax/Trainees.php',
            data: {
                fun: 'HandleClassAssignment',
                data: JSON.stringify(data)
            },
            success: function (res) {
                $('#js-modal-over-limitation').modal('hide');
                if(res.Status == 'overLimit'){
                    // There is no place in the lesson - popup assign or waiting list
                    data.popup = "assign";
                    data= JSON.stringify(data);
                    modalAssignOverMaxOrWaitingList.openModal(data);
                } else{
                    modalOverLimitation.assignIfSucceed(res, data.classId);
                }
            }

        }).then(function () {
            button.html(button.data('buttonText'));
        });
    },
    assignIfSucceed: function (res, classId) {
        const data = res.data;
        if (res.Status == 'Success') {
            if (data.logData.statusId == '9') {
                charPopup.addClientToWaitingList(data.actId, data.clientCount.clientWaiting, classId);
            } else {
                charPopup.addClientToActiveTrainee(data.actId);
                $('#client-reg').html(data.clientCount.clientRegistered);
                $(`#client-reg-card${classId}`).html(data.clientCount.clientRegistered);
                let total_trainers = parseInt($('#total_trainers').text());
                $('#total_trainers').html(total_trainers + 1);
            }
            charPopup.addEventToLog(data.logData, data.clientInfo);
            if (data.isPermanent == "1") {
                modalUserPopup.updateClassCardRegularAssignment(data.updatedClassIds);
                charPopup.addClientToConclusion(12, data.clientInfo);//Always 12 because it should also show waiting regular assignment
                charPopup.reloadPopup(classId);
            }
            $('#js-modal-add-user').modal('hide');
            $.notify({
                message: lang('traine_booked_cal')
            }, {
                type: 'success',
                z_index: 2000,
            });

            charPopup.reloadSingleMember(classId);
        } else {
            $.notify({
                message: res.Message ? res.Message : lang('error_detected_cal')
            }, {
                type: 'danger',
                z_index: 2000,
            });
            $('#js-modal-over-limitation').modal('hide');
        }
    }
}

var modalClassPopup = {
    initUserSelect2: function () {

        //$("#js-user-search.select2-hidden-accessible").select2("destroy")
        $("#js-user-search")
                .select2({
                    tags: true,
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            isNew: true
                        };
                    },
                    placeholder: lang('search_by_name_or_phone'),
                    language: $("html").attr("dir") == 'rtl' ? "he" : "en",
                    allowClear: true,
                    theme: "bsapp-dropdown bsapp-no-arrow",
                    minimumInputLength: 2,
                    ajax: {
                        url: '/office/action/getClientsJson.php',
                        data: function (params) {
                            var query = {
                                query: params.term,
                                type: 'public'
                            }

                            // Query parameters will be ?search=[term]&type=public
                            return query;
                        },
                        processResults: function (data) {
                            var items = $.map($.parseJSON(data).results, user => ({
                                    name: user.name,
                                    id: user.id,
                                    img: user.img,
                                    phone: user.phone,
                                    status: user.status
                                    // concatArchive: user.status == "bg-danger" ? '(' + lang("archive") + ')': "",

                                })
                            )



                            return {
                                results: items
                            };
                        },
                    },
                    templateResult: formatState,
                    templateSelection: function (item) {

                        if (item.id == '') {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('search_by_name_or_phone') + '</div><div> </div> </div>');
                        } else if (item.isNew) {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div> </div> </div>');

                            //$item = $('<div class="d-flex justify-content-between align-items-center"><div>'+item.text+'</div><span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">' +item.text+ '</div> </div>');
                        } else {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
                        }
                        return $item;
                    }
                    //templateSelection: formatSelectionNew

                })
                .on("select2:selecting", function (e) {

                    $(".js-client-is-new").hide();
                    $(".js-client-phone-valid").hide();
                    $('input[name="client-id"]').val(e.params.args.data.id);
                    $('input[name="is-new"]').val(e.params.args.data.isNew);
                    $('input[name="client-isLead"]').val(e.params.args.data.status);

                    if (e.params.args.data.isNew) {
                        $('.js-client-is-new').show();
                        $('#client-activities').html("");
                        $('#js-charge-options-new').val('without-charge').trigger('change');

                        $('#js-charge-options-new').next(".select2-container").removeClass('d-none');
                        $('#js-charge-options-exist').next(".select2-container").addClass('d-none');
                        $('#alert-created-as-lead').removeClass('d-none');

                        $(this)
                                .parents(".userselectContainers")
                                .find(".newLabel")
                                .addClass("labelDisplayOn");
                        var userVal = $(".userType").val();

                        $(".newLabel").text('New');
                        $(this)
                                .parents(".userselectContainers")
                                .find("input[id^='isMembershipTypeNew']")
                                .val("1");

                        var selected = e.params.args.data;
                        var isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/

                        if (selected.text.match(isValidMobileRegx)) {
                            $(".js-client-phone-valid").show();
                            $(".js-user-data-details #js-user-phone").val(selected.text);
                        } else {
                            $(".js-user-data-details #js-user-name").val(selected.text);
                        }

                    } else {
                        var selected = e.params.args.data;

                        $('#js-charge-options-new').next(".select2-container").addClass('d-none');
                        $('#js-charge-options-exist').next(".select2-container").removeClass('d-none');
                        $('#alert-created-as-lead').addClass('d-none');

                        setHtmlToLoader('#client-activities');
                        $.ajax({
                            method: 'POST',
                            url: '/office/partials-views/char-popup/show-client-activities.php',
                            data: {
                                clientId: selected.id,
                                classDate: $('#js-class-data').data('classDate')
                            },
                            success: function (res) {
                                $('#client-activities').html(res);
                                $('#js-charge-options-exist').trigger('change');
                            }
                        });


                        $(".js-user-data-details #js-user-name").val(selected.name).attr("readonly", true)
                        $(".js-user-data-details #js-user-phone").val(selected.phone).attr("readonly", true)

                        $(this)
                                .parents(".userselectContainers")
                                .find(".newLabel")
                                .removeClass("labelDisplayOn");
                        $(this)
                                .parents(".userselectContainers")
                                .find("input[id^='isMembershipTypeNew']")
                                .val("0");

                    }

                    modalClassPopup.hideSearchField();
                    $(this)
                            .parents(".pointForm")
                            .find(".show-field")
                            .addClass("DisplayOn");
                });
    },
    showSearchField: function (elem) {
        $('#alert-created-as-lead').addClass('d-none');
        $(".js-user-data-details .form-control").val("").attr("readonly", false)
        $(".js-user-search").val(null).trigger("change");
        $(".js-user-data-details .form-group").removeClass("d-flex").addClass("d-none");
        $("#js-user-search").parents(".form-group").removeClass("d-none").addClass("d-flex");
        $(".pointForm")
                .find(".show-field")
                .removeClass("DisplayOn");
    },
    hideSearchField: function () {
        $(".js-user-data-details .form-group").removeClass("d-none").addClass("d-flex");
        $("#js-user-search").parents(".form-group").removeClass("d-flex").addClass("d-none");
    },
    resetAssignClientModal: function () {
        modalClassPopup.showSearchField();
        $('#assign-client-form').trigger('reset');
        $('#alert-created-as-lead').addClass('d-none')
        $('select[name="charge-option-exist"]').val('choose-membership').trigger('change');
        $('select[name="assign-type"]').val(1).trigger('change');
        $('select[name="charge-option-new"]').val('without-charge').trigger('change');

    }
};
function formatState(state) {
    if (!state.name) {
        if (!state.loading) {
            var $state = $(
                    '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"><div class="mie-8 w-40p h-40p rounded-circle border  d-flex align-items-center justify-content-center bsapp-plus-icon"><i class="fal fa-plus bsapp-fs-20" ></i></div>' + state.text + '</div><div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div></div>'
                    );
            return $state;
        } else {
            return state.text;
        }
    }

    var $state = $(
            '<div class="d-flex justify-content-between align-items-center "><div class="d-flex align-items-center"> <div class="position-relative mie-8"><img src="' + state.img + '" class="w-40p h-40p rounded-circle " /> <div class="position-relative bsapp-status-icon mt_-12p '+ state.status+ '"></div> </div> <div class="d-flex flex-column"><div class="d-flex"><span> ' + state.name + ' </span></div><span class="bsapp-fs-14" dir="ltr">' + state.phone + '</span><div></div></div>'
            );


    return $state;
}
function formatStatePhone(state) {

    if (!state.phone) {
        return state.text;
    }


    var $state = $(
            '<div class="d-flex justify-content-between align-items-center"><div><img src="' + state.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + state.phone + ' </span></div></div>'
            );
    return $state;
}

function setHtmlToLoader(selector) {
    $(selector).html('<i class="fal fa-spinner-third fast-spin fa-lg"></i>');
}

function formatSelection(item) {

    //return  $( '<div class="bg-light px-8 py-4 rounded d-flex justify-content-between align-items-center mie-10 mb-10"><div><img src="' +  item.owner.avatar_url  + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div><a class="text-danger js-user-remove-selected  mis-15" href="javascript:;"><i class="fas fa-minus-circle"></i> </a></div>')
    return $('<div class="bg-light border border-gray-300 px-5 pt-2 pb-3  rounded d-flex justify-content-between align-items-center bsapp-fs-14"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div><a class="text-danger js-user-remove-selected mis-10 " href="javascript:;"><i class="fal fa-minus-circle"></i> </a></div>')
}

function formatSelectionNew(item) {

    //return  $( '<div class="bg-light px-8 py-4 rounded d-flex justify-content-between align-items-center mie-10 mb-10"><div><img src="' +  item.owner.avatar_url  + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div><a class="text-danger js-user-remove-selected  mis-15" href="javascript:;"><i class="fas fa-minus-circle"></i> </a></div>')
    return $('<div class="px-5 pt-2 pb-3  rounded d-flex justify-content-between align-items-center bsapp-fs-14"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div></div>')
}

var modalPrintPopup = {
    init: function () {

        if ($(".js-display-options .custom-control-input:checked").length > 0) {
            var keys = $(".js-display-options .custom-control-input:checked").map(function () {
                return this.id;
            }).get().toString();
            var js_report_link = js_app_url + '/office/TrainersReport.php?id=' + btoa($("#js-class-data").attr("data-classid")) + '&fields=' + btoa(keys);
            // var js_report_link = 'http://localhost:8000/office/TrainersReport.php?id=' + btoa($("#js-class-data").attr("data-classid")) + '&fields=' + btoa(keys);

            $(".js-report-view").attr("href", js_report_link).removeClass("disabled");
            $(".js-report-link-copy").removeClass("disabled");

        } else {
            $(".js-report-view").addClass("disabled");
            $(".js-report-link-copy").addClass("disabled");
        }
        $("body").on("click", ".js-display-options .custom-control-input", function () {
            if ($(".js-display-options .custom-control-input:checked").length > 0) {
                var keys = $(".js-display-options .custom-control-input:checked").map(function () {
                    return this.id;
                }).get().toString();
                var js_report_link = js_app_url + '/office/TrainersReport.php?id=' + btoa($("#js-class-data").attr("data-classid")) + '&fields=' + btoa(keys);
                // var js_report_link = 'http://localhost:8000/office/TrainersReport.php?id=' + btoa($("#js-class-data").attr("data-classid")) + '&fields=' + btoa(keys);

                $(".js-report-view").attr("href", js_report_link).removeClass("disabled");
                $(".js-report-link-copy").removeClass("disabled");

            } else {
                $(".js-report-view").addClass("disabled");
                $(".js-report-link-copy").addClass("disabled");
            }

        });
    },
    viewReport: function (elem) {

    },
    copyReportLink: function (elem) {
        clearTimeout();
        clipboard.copy($(".js-report-view").attr("href"));
        $(elem).attr("readonly", false)
        $input_group = $(elem).parents(".js-div-copy-report-link").find(".input-group");
        $("input.js-report-link").val($(".js-report-view").attr("href")).attr("readonly", true);
        $input_group.show();
        $(elem).hide();
        setTimeout(function () {
            $(elem).show();
            $input_group.hide();
        }, 3000);
    }
};



function inputBorderDanger(inputElement) {
    inputElement.removeClass('border-light').addClass('border-danger');
    window.setTimeout(function () {
        inputElement.removeClass('border-danger').addClass('border-light');
    }, 4000);

    return 1;
}

function notInputBorderDanger(elem) {
    elem.addClass('border border-danger');
    window.setTimeout(function () {
        elem.removeClass('border border-danger')
    }, 4000);

    return 1;
}

function dashToCamelCase(myStr) {
    return myStr.replace(/-([a-z])/g, function (g) {
        return g[1].toUpperCase();
    });
}


var js_last_active_tab = 1;
jQuery(function () {
    $("#js-char-popup").on("hidden.bs.modal", function (e) {
        charPopup.clearSelection();
        $("#js-char-popup-content").html("");
    });

    $('body').on('contextmenu', 'tr[data-clientid]', function (e) {
        e.preventDefault();
        $(this).find('.dropdown-toggle').dropdown('toggle');
    })

    $('body').on('hidden.bs.modal', '#js-modal-over-max-regular', function () {
        $("a[data-button-text]").each(function () {
            $(this).html($(this).data('buttonText'));
        });
    });

    $('body').on('hidden.bs.modal', '#js-remove-regular-assignment-modal', function () {
        $('#removeOptionContainer').trigger('reset');
    });

    ////// Print Class ///////
    $('body').on('click', 'a[data-target="#js-modal-window-print"]', function () {
        $("#js-modal-window-print-content").html($(".js-modal-print-shimming-loader ").html());
        $('#js-modal-window-print-content').load('/office/partials-views/char-popup/modal-window-print.php');
    });


    ////// Copy Link //////
    $('body').on('click', '.js-copy-link-button', function () {
        const linkInput = $(".js-link-to-copy");
        const defButton = $(this);
        const succeedButton = $('.js-copy-link-succeed');

        linkInput.trigger('select');
        document.execCommand('copy');
        linkInput.trigger('deselect');

        defButton.addClass('d-none');
        succeedButton.removeClass('d-none');

        window.setTimeout(function () {
            succeedButton.addClass('d-none');
            defButton.removeClass('d-none');
        }, 2000);
    });

    //////// Class Functionality ///////

    ///// Assign To Class /////
    $('body').on('hidden.bs.modal', '#js-modal-add-user', () => modalClassPopup.resetAssignClientModal())

    $('body').on('change', '#js-items-container', function () {
        const selected = $(this).find(':selected');
        $('input[name="new-membership-amount"]').val(selected.data('price'));
        $('#assign-type').val('1').trigger('change').next(".select2-container");
    });
    $('body').on('click','input[name=membership]:checked', function() {
        $('#assign-type').val('1').trigger('change').next(".select2-container");
    });


    $('body').on('change', '.js-charge-options', function () {
        const selected = $(this).find(':selected');
        $('.js-charge-option').addClass('d-none');
        $(`#${selected.val()}`).removeClass('d-none');

        switch (selected.val()) {
            case 'choose-membership':
                $('#assign-type').next(".select2-container").removeClass('d-none');
                $('#assign-type-label').removeClass('d-none');
                $('#assign-type').val('1').trigger('change').next(".select2-container");
            case 'new-membership':
                $('#assign-type').next(".select2-container").removeClass('d-none');
                $('#assign-type-label').removeClass('d-none');
                $('#assign-type').val('1').trigger('change').next(".select2-container");

                $('#js-items-container').html('');
                $.ajax({
                    method: 'POST',
                    url: '/office/partials-views/char-popup/show-items-for-class.php',
                    data: {classTypeId: charPopup.class_info.classTypeId},
                    success: function (res) {
                        const select = $('#js-items-container');
                        select.html(res);
                        select.trigger('change');
                    }
                })
                break;
            case 'without-charge':
            case 'single-payment':
                $('#assign-type').val('1').trigger('change').next(".select2-container").addClass('d-none');
                $('#assign-type-label').addClass('d-none');
                break;

        }
    });

    $('body').on('click', '.js-submit-client-assignment', function (e) {
        e.preventDefault();
        let result = {};
        //Import form to json
        let formJson = {};
        $.map($('#assign-client-form').serializeArray(), function (n, i) {
            formJson[n['name']] = n['value'];
        });

        Object.assign(result, {'classTypeId': charPopup.class_info.classTypeId});
        Object.assign(result, {'classId': charPopup.class_info.classid});
        Object.assign(result, {'clientId': formJson['client-id']});
        Object.assign(result, {'isNew': formJson['is-new']});

        Object.assign(result, setRequiredByInputName('client-name'));

        // if ($('[name="client-name"]').val() == 'boostapp_please_dance'){
        //     $('#js-modal-add-user').modal('hide');
        //     $('tr[data-clientid]').addClass('dance');
        //     window.setTimeout(function () {
        //         $('tr[data-clientid]').removeClass('dance')
        //     }, 5000);
        // }

        //Check phone format
        const phone = $('[name="js-user-phone"]');
        if (formJson['is-new']) {
            Object.assign(result, setRequiredByInputName('js-user-phone'));
            if (!result.error && (!phone.val().match(/^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/))) {
                inputBorderDanger(phone);
                Object.assign(result, {error: lang('phone_format_incorrect_ajax')});
            }
        }

        //Check if there is selection
        const chargeOption = formJson['is-new'] ? 'charge-option-new' : 'charge-option-exist';
        Object.assign(result, setRequiredByInputName(chargeOption));
        const chargeOptionElem = $(`[name="${chargeOption}"]`);
        switch (chargeOptionElem.val()) {
            case "choose-membership":
                const checked = $('#client-activities').find('input[name="membership"]:checked');
                if (!checked.val()) {
                    notInputBorderDanger($('#client-activities'));
                    Object.assign(result, {error: 'error'});
                } else
                    Object.assign(result, {['chooseMembership']: checked.val()})
                break;
            case "without-charge":
                break;
            case "single-payment":
                Object.assign(result, setRequiredByInputName('single-payment-amount'));
                break;
            case "new-membership":
                Object.assign(result, setRequiredByInputName('new-membership-amount'));
                Object.assign(result, setRequiredByInputName('new-membership-select'));
                break;
        }

        Object.assign(result, setRequiredByInputName('assign-type'));

        if (formJson['assign-type'] == 2) { //Registered for group of lessons
            const checked = $('[name="multi-assign"]:checked');
            if (!checked.val()) {
                notInputBorderDanger($('#multi-assign-div'));
                Object.assign(result, {error: 'error'});
            } else {
                Object.assign(result, {['multiAssign']: checked.val()});
                if (checked.val() == 'by-date') {
                    let dateInput = $(`[name="assign-until-date"]`);
                    if (moment(dateInput.val()).isBefore(charPopup.class_info.startDate)) {
                        inputBorderDanger(dateInput);
                        Object.assign(result, {error: lang('error_future_date_lesson')});
                    }
                    Object.assign(result, setRequiredByInputName('assign-until-date'));
                } else if (checked.val() == 'by-count') {
                    let byCountInput = $('[name="assign-by-count"]');
                    if (parseInt(byCountInput.val()) > 30) {
                        inputBorderDanger(byCountInput);
                        Object.assign(result, {error: lang('max_30_times')});
                    }
                    Object.assign(result, setRequiredByInputName('assign-by-count'));
                }
            }
        }
        // const checkError = Object.values(result).find(elem => elem == 'error');
        if (result.error) {
            if (result.error == 'error')
                return;
            const errorDiv = $('#add-client-errors')
            errorDiv.css('opacity', '0').removeClass('d-none').text(result.error);
            errorDiv.animate({opacity: 1}, 200);

            $("html, .bsapp-max-h-400p.bsapp-scroll").animate({
                scrollTop: 0,
            }, 1000);

            window.setTimeout(function () {
                errorDiv.animate({opacity: 0}, 200, function () {
                    errorDiv.addClass('d-none');
                });
            }, 4000);
            return;
        } else {
            $('.js-submit-client-assignment').prop('disabled', true);
            const $parent = $('#js-modal-add-user');
            $parent.showModalLoader();
            Object.assign(result, {'popup': 0});
            let data = JSON.stringify(result);
            $.ajax({
                method: 'POST',
                url: 'ajax/Trainees.php',
                data: {
                    fun: 'HandleClassAssignment',
                    data: data
                },
                success: function (res) {
                    $parent.find('.modal-content').find(".js-loader").remove();
                    if (res.Status == 'full') {
                        modalOverMaxRegular.openModal(data);
                    } else if (res.Status == 'limitation') {
                        //פופ אפ שיבוץ בחריגה
                        modalOverLimitation.openModal(data, res);
                    } else if(res.Status == 'overLimit') {
                        // There is no place in the lesson - popup assign or waiting list
                        data = JSON.parse(data);
                        data.popup = "assign";
                        data = JSON.stringify(data);
                        modalAssignOverMaxOrWaitingList.openModal(data);
                    }else if(res.Status == 'Error'){
                        $parent.find('.modal-content').find(".js-loader").remove();
                        $.notify({
                            message: res.Message
                        }, {
                            type: 'danger',
                            z_index: 2000,
                        });
                    } else {
                        modalOverLimitation.assignIfSucceed(res, result.classId);
                    }

                },
                error: function () {
                    $parent.find('.modal-content').find(".js-loader").remove();
                    $.notify({
                        message: 'Error'
                    }, {
                        type: 'danger',
                        z_index: 2000,
                    });
                }
            });
            $('.js-submit-client-assignment').prop('disabled', false);
        }
    });

    function setRequiredByInputName(fieldName) {
        const elem = $(`[name="${fieldName}"]`);
        if (!elem.val()) {
            if (elem.prop('tagName') == 'INPUT')
                inputBorderDanger(elem);
            else
                (elem.prop('tagName') == 'SELECT')
            notInputBorderDanger(elem.next());
            return {['error']: 'error'};
        } else
            return {[dashToCamelCase(fieldName)]: elem.val()};
    }

    ///// Cancel Class //////
    $('body').on('click', '.js-mark-class-canceled', function () {
        $('#js-modal-cancel-action-content').load('/office/partials-views/char-popup/modal-cancel-class.php');
    });

    $('body').on('click', '#js-submit-cancel-class', function () {
        const classId = $('#js-class-data').data('classid');
        const freq = $('input[name="cancel_action_radio"]:checked').data('freq');

        if (freq == 'single') {
            setHtmlToLoader(this);
            const showInCal = $('#show-in-cal-switch').is(':checked') ? 1 : 0;
            $.post('ajax/Trainees.php', {fun: 'cancelClassOneTime', classId: classId, displayCancel: showInCal}, () => finishedCancelationSingle(showInCal));
        } else if (freq == 'multi') {
            setHtmlToLoader(this);
            const amount = $('#js-series-dropdown :selected').data('amount');
            handleMultiCancelation(amount);
        }
    });

    function finishedCancelationSingle(showInCal) {
        let classId = charPopup.class_info.classid;
        $('#js-modal-cancel-action').modal('hide');
        charPopup.reloadPopup(classId);
        GetCalendarData();
    }

    function handleMultiCancelation(amount) {
        const groupNumber = charPopup.class_info.groupNumber;
        const startDate = charPopup.class_info.classDate;
        switch (amount) {
            case 'all':
                $.post('ajax/Trainees.php', {fun: 'cancelAllClasses', groupNumber: groupNumber, startDate: startDate}, finishedCancelationMulti);
                break;
            case 'dates':
                const since = $('#js-cancel-date-since');
                const until = $('#js-cancel-date-until');

                if (since.val() == "")
                    return inputBorderDanger(since);
                if (until.val() == "")
                    return inputBorderDanger(until);

                $.post('ajax/Trainees.php', {fun: 'cancelClassesByDates', groupNumber: groupNumber, startDate: since.val(), endDate: until.val()}, finishedCancelationMulti);
                break;
            case 'quantity':
                const quantity = $('#js-cancel-quantity');
                if (isNaN(quantity.val()) || quantity.val().length < 1)
                    return inputBorderDanger(quantity);

                $.post('ajax/Trainees.php', {fun: 'cancelClassesByQuantity', groupNumber: groupNumber, quantity: quantity.val(), startDate: startDate}, finishedCancelationMulti);
                break;
        }
    }

    let finishedCancelationMulti = function () {
        const classId = charPopup.class_info.classid;
        $('#js-modal-cancel-action').modal('hide');
        charPopup.reloadPopup(classId);
        GetCalendarData();
    }

    ///// Change Class Status //////
    $('body').on('click', '.js-mark-class-completed', function () {
        let classData = $('#js-class-data');
        let classId = classData.data('classid');
        $("#js-char-popup-content").html($(".js-char-shimming-loader").html());
        $.post('ajax/Trainees.php', {fun: 'changeClassStatus', classId: classId, status: 1}, function (res) {
            GetCalendarData();
            charPopup.reloadPopup(classData.data('classid'));
        });
    });

    $('body').on('click', '.js-force-cancel-to-active', function () {
        setHtmlToLoader(this);
        const classId = charPopup.class_info.classid;
        const returnTrainees = $(this).data('returnTrainees');
        $.post('ajax/Trainees.php', {fun: 'changeClassStatus', classId: classId, status: 0, returnTrainees: returnTrainees}, function (res) {
            $('#js-modal-canceled-to-active').modal('hide');
            charPopup.reloadPopup(classId);
            GetCalendarData();
        });
    });

    $('body').on('click', '.js-mark-class-active', function () {
        let classId = charPopup.class_info.classid;
        // $("#js-char-popup-content").html($(".js-char-shimming-loader").html());
        $.post('ajax/Trainees.php', {fun: 'changeClassStatus', classId: classId, status: 0}, function (res) {
            if (res.Status == 'Missing') {
                $('#js-modal-canceled-to-active').modal('show');
                charPopup.showCanceledToActivePopup(res.data);
                return;
            }
            GetCalendarData();
            charPopup.reloadPopup(classId);
        })
    });

    ////// END Class Functionality //////

    /////// Waiting List Functionality /////////

    ///////// Assign Waiting //////////

    $('body').on('click', '.js-agree-max-client', function () {
        setHtmlToLoader(this);
        const popupData = $('#js-max-popup-data');
        const actIdArr = popupData.data('actIdArr');
        const classId = popupData.data('classId');

        $.post('ajax/Trainees.php', {fun: 'assignWaitingToClass', actIdArr: actIdArr, classId: classId, overridePopup: 1}, function (result) {
            if (popupData.data('isSingle') == 'one')
                handleSingleAssignment(result, classId);
            else if (popupData.data('isSingle') == 'many')
                handleManyAssignment(result, classId);
            $('#js-modal-over-max-client').modal('hide');
            charPopup.reloadSingleMember(classId);
        });
    });

    $('body').on('click', '.js-assign-waiting', function () {
        let prevButton = $(this);
        let prevText = prevButton.html();
        $('body').on('click', '.js-cancel-assignment', function () {
            prevButton.html(prevText);
            $('#' + $(this).closest('div[role="dialog"]').attr('id')).modal('hide');
        });
        /////

        setHtmlToLoader(this);
        let tr = $(this).closest('tr');
        let classId = $('#js-class-data').data('classid');
        let actIdArr = [tr.data('actid')];
        let override = 0;
        if (tr.hasClass('js-waiting-pending')) {
            override = 1;
        }
        $.post('ajax/Trainees.php', {fun: 'assignWaitingToClass', actIdArr: actIdArr, classId: classId, overridePopup: override}, function (result) {

            if (result.Status == 'over') {
                $('#js-modal-over-max-client').modal('show');
                charPopup.showMaxClientPopup(actIdArr, classId, result.data.overClients, 'one');
                return;
            }
            handleSingleAssignment(result, classId);
        });
    });

    //Get api response for assignWaitingToClass and handeling front end
    function handleSingleAssignment(result, classId) {
        $('#client-reg').html(result.Message.clientRegistered);
        $('#client-waiting').html(result.Message.clientWaiting);
        charPopup.addEventToLog(result.logData, result.data[0]);
        $(`#client-reg-card${classId}`).html(result.Message.clientRegistered);
        $(`#client-waiting-card${classId}`).html(result.Message.clientWaiting);
        let total_trainers = parseInt($('#total_trainers').text());
        $('#total_trainers').html(total_trainers + 1);

        let tr = $(`tr[data-clientid="${result.data[0].clientId}`);
        charPopup.addClientToActiveTrainee(tr.data('actid'));
        tr.hide('slow', function () {
            tr.remove();
        });
        charPopup.updateWaitingCount(result.Message.clientWaiting, classId);
    }

    $('body').on('click', '.js-assign-many-waiting', function () {
        const actIdArr = $('.js-client-checkbox:checked').closest('tr').toArray().map(el => $(el).data('actid'));
        const classId = $('#js-class-data').data('classid');

        $.post('ajax/Trainees.php', {fun: 'assignWaitingToClass', actIdArr: actIdArr, classId: classId, overridePopup: 0}, function (result) {
            if (result.Status == 'over') {
                $('#js-modal-over-max-client').modal('show');
                charPopup.showMaxClientPopup(actIdArr, classId, result.data.overClients, 'many');
                return;
            }
            handleManyAssignment(result, classId);
        });
    });

    //Get api response for assignWaitingToClass and handeling front end
    function handleManyAssignment(result, classId) {
        let clientRegistered = parseInt($('#client-reg').text());

        for (res of result.data) {
            const tr = $(`tr[data-clientid="${res.clientId}"]`);
            tr.hide('slow', function () {
                tr.remove();
            });
            clientRegistered++;
            $('#client-reg').html(clientRegistered);
            charPopup.addEventToLog(result.logData, res);
            charPopup.addClientToActiveTrainee(tr.data('actid'));
        }
        charPopup.clearSelection();
        $('#client-waiting').html(result.Message.clientWaiting);
        $(`#client-reg-card${classId}`).html(result.Message.clientRegistered);
        $(`#client-waiting-card${classId}`).html(result.Message.clientWaiting);
        let total_trainers = parseInt($('#total_trainers').text());
        $('#total_trainers').html(total_trainers + parseInt(result.data.length));
        charPopup.updateWaitingCount(result.Message.clientWaiting, classId);
    }

    ///////////////////////

    ////////// END Waiting List Functionality ///////////

    /////// Active Trainees Functionality ///////

    /////// Trainees Functionality ///////

    /////// Green Pass Popup ///////

    $('body').on("click", ".js-green-pass-icon", function () {
        let clientId = $(this).attr("data-id");

        $.ajax({
            url: "/office/ajax/covidGreenPass.php",
            type: "post",
            data: {
                client_id: clientId,
                fun: "modal"
            },
            success: function (response) {
                $("#greenPassModalClientList").html(response);
                $("#green_pass_modal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus);

            },
        });
    })

    ////////////////////////////////

    /////// Log Customizing ////////
    $('body').on('click', '#js-participants-tab-5', function () {
        let clientRegisterdLog = $('.js-log-reg-client').get().reverse();
        let prevTd;
        for (td of clientRegisterdLog) {
            if ((!prevTd && $(td).text() == 1) || parseInt($(td).text()) > parseInt($(prevTd).text())) {
                if (!$(td).children('i').length)
                    $(td).append('<i class="fas fa-caret-up mr-5"></i>').addClass("text-success");
            } else if (!prevTd || $(td).text() == $(prevTd).text()) {
                $(td).addClass("text-secondary");
            } else {
                if (!$(td).children('i').length)
                    $(td).append('<i class="fas fa-caret-down mr-5"></i>').addClass("text-danger");
            }
            prevTd = td;
        }
    })
    ///////////////

    ///// Paid\Free class /////

    $('body').on('click', '.js-mark-without-charge', function () {
        const tr = $(this).closest('tr');
        const button = $(this);

        $.post('ajax/Trainees.php', {fun: 'MarkClassWithoutCharge', actId: tr.data('actid')}, function (res) {
            tr.find('.js-badge-without-charge').text(tr.find('.js-badge-without-charge-content').text()).removeClass("d-none");
            charPopup.addEventToLog(res.logData, res.data);
            button.hide();
            tr.find('.js-mark-with-charge').first().show();
            // tr.find('div[data-status="2"] input').prop('checked', false);
            // tr.find('div[data-status="8"] input').prop('checked', false);
        })
    });

    $('body').on('click', '.js-mark-with-charge', function () {
        const tr = $(this).closest('tr');
        const button = $(this);
        $.post('ajax/Trainees.php', {fun: 'MarkClassWithCharge', actId: tr.data('actid')}, function (res) {
            tr.find('.js-badge-without-charge').text("").addClass("d-none");
            charPopup.addEventToLog(res.logData, res.data);
            button.hide();
            tr.find('.js-mark-without-charge').first().show();
        });
    });

    //////////////

    ////// Attendency ///////

    $('body').on('click', '.js-mark-many-attended', function () {
        let currentModal = $(this).closest('div[role="dialog"]');
        let actIds = $('.js-client-checkbox:checked').closest('tr').toArray().map(el => $(el).data('actid'));
        let status = $(this).data('status');
        let prevHtml = $(this).html();
        const button = $(this);
        setHtmlToLoader(this);
        $.post('ajax/Trainees.php', {fun: 'ChangeArrivalStatus', actId: actIds, status: status}, function (results) {
            for (res of results.data) {
                const tr = $(`tr[data-clientid="${res.clientId}"]`);
                tr.find('input[type="checkbox"').prop('checked', false);
                const radioButton = tr.find(`div[data-status='${status}']`);
                radioButton.children('input').prop("checked", true);
                charPopup.addClientToConclusion(status, res);
                charPopup.addEventToLog(results.logData, res);
            }
            currentModal.modal('hide');
            button.html(prevHtml)
            charPopup.clearSelection();
        })
    })

    $('body').on('click', '.js-mark-attended input', function (e) {
        const tr = $(this).closest('tr');
        let status = $(this).closest('div').data('status');

        if (!$(this).is(':checked'))
            status = 1;
        else {
            if (status == 2)
                tr.find('div[data-status="8"] input').prop('checked', false);
            else if (status == 8)
                tr.find('div[data-status="2"] input').prop('checked', false);
        }
        $.post('ajax/Trainees.php', {fun: 'ChangeArrivalStatus', actId: [tr.data('actid')], status: status}, function (res) {

            if (res.data[0]) {
                charPopup.addClientToConclusion(status, res.data[0]);
                charPopup.addEventToLog(res.logData, res.data[0]);
            }
        })
    })

    /////////////////////

    //// Remove from class //////

    $('body').on('click', '.js-remove-many-clients', function () {
        setHtmlToLoader(this);
        const btnElem = $(this);
        const actIds = $('.js-client-checkbox:checked').closest('tr').toArray().map(el => $(el).data('actid'));
        const classId = $('#js-class-data').data('classid');
        const tabName = $('[data-toggle="pill"].active').data('order');
        let clientRegistered = parseInt($('#client-reg').text());

        $.post('ajax/Trainees.php', {fun: 'removeClientFromClass', actId: actIds, classId: classId, override_late: true}, function (result) {
            for (res of result.data) {
                const tr = $(`tr[data-clientid="${res.clientId}"]`);
                tr.hide('slow', function () {
                    tr.remove();
                });
                if (tabName == 1) {
                    clientRegistered--;
                    $('#client-reg').html(clientRegistered);
                }
                charPopup.addEventToLog(result.logData, res);
                charPopup.removeClientFromConclusion(tr.data('clientid'));
            }
            charPopup.clearSelection();

            if (tabName == 1) {
                $(`#client-reg-card${classId}`).html(result.Message.clientRegistered);
                let total_trainers = parseInt($('#total_trainers').text());
                $('#total_trainers').html(total_trainers - parseInt(result.data.length));
                if (result.Message.clientRegistered == 0)
                    $('#js-no-clients').removeClass('d-none');
            } else if (tabName == 2) {
                charPopup.updateWaitingCount(result.Message.clientWaiting, classId);
            }
            $('#js-remove-clients-modal').modal('hide');
            btnElem.html(lang('yes'));
            charPopup.reloadSingleMember(classId);
        })
    })

    $(".js-remove-client-from-class").off('click');
    $('body').on('click', '.js-remove-client-from-class', function () {
        const tr = $(this).closest('tr');
        const classId = tr.data('classid');
        const tabName = tr.closest('table').data('tab');
        const override_late = tabName == 'waiting' ? 1 : 0;

        $.post('ajax/Trainees.php', {fun: 'removeClientFromClass', actId: [tr.data('actid')], classId: classId, override_late: override_late}, function (result) {
            if (result.Status == 'Late' && tabName == 'active') {
                charPopup.showLateCancelModal(tr.data('actid'), classId);
                return;
            }

            charPopup.removeClientFromConclusion(tr.data('clientid'));
            $('#client-reg').html(result.Message.clientRegistered);
            charPopup.addEventToLog(result.logData, result.data[0]);

            if (tabName == 'waiting') {
                charPopup.updateWaitingCount(result.Message.clientWaiting, classId);
            } else if (tabName == 'active') {
                $(`#client-reg-card${classId}`).html(result.Message.clientRegistered);
                let total_trainers = parseInt($('#total_trainers').text());
                $('#total_trainers').html(total_trainers - 1);
                if (result.Message.clientRegistered == 0)
                    $('#js-no-clients').removeClass('d-none');
            }
            tr.hide('slow', function () {
                tr.remove();
            });
            charPopup.reloadSingleMember(classId);
        })
    });

    $(".js-remove-charge").off('click');
    $('body').on('click', '.js-remove-charge', function () {
        const actId = $(this).data('actid');
        $.post('ajax/Trainees.php', {fun: 'MarkClassWithCharge', actId: actId}, function (result) {
            if (result.Status) {
                var tr = $('body').find('#pills-tabContent').find("table").find("tr[data-actid='" + actId + "']");
                tr.find("a.js-badge-without-charge").text("");
                $('.js-charge-div').remove();
                tr.find('.js-mark-with-charge').hide();
                tr.find('.js-mark-without-charge').first().show();
                charPopup.addEventToLog(result.logData, result.data);
            }
        })
    })

    /////////////////////

    ////// Device //////

    $('body').on('click', '.js-add-device-button', function () {
        $("#js-modal-device-add").modal("show");
        const actId = $(this).closest('tr').data('actid');
        charPopup.showDevicesPopup(actId);
    })

    $(".js-save-device").off('click');
    $('body').on('click', '.js-save-device', function () {
        const deviceId = $('input[name="deviceRadios"]:checked').val();
        const actId = $('#act-id').data('actid');
        if (deviceId) {
            setHtmlToLoader(this);
            $.post('ajax/Trainees.php', {fun: 'setDeviceForAct', deviceId: deviceId, actId: actId}, function (data) {
                $('#js-modal-device-add').modal('hide');
                $(`#device-column${actId}`).html(`<div class="card bg-light py-2 my-5 small">${data.Message}</div> <a data-actid="${actId}" class="js-remove-device cursor-pointer"><i class="fas fa-do-not-enter"></i></a>`);
            })
        } else {

        }
    })

    $('body').on('click', '.js-remove-device', function () {
        const actId = $(this).data('actid');
        $.post('ajax/Trainees.php', {fun: 'setDeviceForAct', deviceId: 0, actId: actId}, function () {
            $(`#device-column${actId}`).html($('.js-no-device-exist').html());
        })
    })


    function formatDate(date) {
        var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
    /*
     $('body').on('click', '.js-edit-content', function () {
     
     var elementCrm = $(this).closest('.js-crm-div');
     var eleMedical = $(this).parents('.js-medical-div');
     var js_new_textarea_update = $(".js-html-textarea-update").html();
     if (elementCrm.length > 0) {
     elementCrm.after(js_new_textarea_update);
     var editArea = $(this).closest('.js-textarea-parent-div').find('.js-textarea-newly-added-update').find('.js-textarea-edit-mode-update');
     
     var crmId = elementCrm.attr('data-crm-id');
     var crmRemark = elementCrm.find('h6').text().trim();
     var crmDate = elementCrm.find('.tillDate').attr("formateDate");
     if (crmDate !== null) {
     const d = formatDate(crmDate);
     editArea.find('.js-datepicker').val(d);
     }
     elementCrm.hide();
     console.log("crmidid after click on edit", crmId);
     editArea.attr('data-crm-id', crmId);
     editArea.find('.js-form-control-textarea-update').val(crmRemark);
     
     }
     
     if (eleMedical.length > 0) {
     eleMedical.after(js_new_textarea_update);
     var editArea = $(this).closest('.js-textarea-parent-div').find('.js-textarea-newly-added-update').find('.js-textarea-edit-mode-update');
     var medicalId = eleMedical.attr('data-medical-id');
     var mediRemark = eleMedical.find('h6').text().trim();
     eleMedical.hide();
     console.log("medicalId after click on edit", medicalId);
     var js_new_textarea_update = $(".js-html-textarea-update").html();
     editArea.attr('data-medical-id', medicalId);
     editArea.find('.js-form-control-textarea-update').val(mediRemark);
     
     }
     
     })*/
    ////////////////

    ////////END Trainees Functionality////////

    $('body').on('click', '.js-cancel-selection', function () {
        charPopup.clearSelection();
    })

    $('body').on('click', '.close-modal', function () {
        $('#' + $(this).closest('div[role="dialog"]').attr('id')).modal('hide');
    })

    // const editor = new EditorJS({
    //   holderId: 'js-editorjs',
    //});

    $.fn.showFlex = function () {
        return this.removeClass("d-none").addClass("d-flex");
    }
    $.fn.hideFlex = function () {
        return this.removeClass("d-flex").addClass("d-none");
    }

    $('body').on('click', '.js-modal-user', function () {
        var activity_id = $(this).attr("data-activity-id") ? $(this).attr("data-activity-id") : 0;
        var client_id = $(this).attr("data-client-id");

        var act_id = $(this).attr("data-act-id");
        charPopup.showClientInfo(client_id, activity_id, act_id);

    });
    $('body').on('shown.bs.tab', '.js-tabs-animated-panes a[data-toggle="pill"]', function (e) {
        $tab = $(e.target).attr("href");
        $tab_order = $(e.target).attr("data-order");
        if ($tab_order == 3) {
            charPopup.setMiniDropForConclusionTab();
        }
        //var total_checked =$($tab).find(".js-img-to-check input[type='checkbox']:checked").length ;
        $($tab).find(".js-img-to-check input[type='checkbox']:checked").prop("checked", false);
        $(".js-bottom-user-action-bar").removeClass("d-flex").addClass("d-none");
        $(".js-bottom-action-bar").removeClass("d-none").addClass("d-flex");
        $table = $($tab).find("table");

        $table.removeClass("bsapp-disabled-actions");
        $table.find(".js-img-to-check").removeClass("bsapp-check-shown");
        $table.find(".js-img-to-check img").removeClass("d-none");
        $table.find(".js-img-to-check .form-check").addClass("d-none");
        $table.find(".js-tbl-select-all").addClass("d-none");
        $table.find(".js-fa-user-friends").show();
        $('[data-context="' + $table.attr("data-bottom-bar") + '"').removeClass("d-flex").addClass("d-none");
        /* if( total_checked > 0 ){
         
         $('[data-context="'+$($tab).find("table").attr("data-bottom-bar")+'"]').removeClass("d-none").addClass("d-flex");
         $(".js-bottom-action-bar").removeClass("d-flex").addClass("d-none");
         } */

        // $("#pills-tabContent > .tab-pane").removeClass("animated slideInLeft slideInRight");
        if ($tab_order == 1) {
            // if ($("html").attr("dir") == "rtl") {
            //     $($tab).addClass("animated slideInRight faster");
            // } else {
            //     $($tab).addClass("animated slideInLeft faster");
            // }
        } else if ($tab_order == 3) {
            // if ($("html").attr("dir") == "rtl") {
            //     $($tab).addClass("animated slideInLeft faster");
            // } else {
            //     $($tab).addClass("animated slideInRight faster");
            // }
        } else if ($tab_order == 2) {
            // if (js_last_active_tab == 1) {
            //     if ($("html").attr("dir") == "rtl") {
            //         $($tab).addClass("animated slideInLeft faster");
            //     } else {
            //         $($tab).addClass("animated slideInRight faster");
            //     }
            // } else if (js_last_active_tab == 3) {
            //     if ($("html").attr("dir") == "rtl") {
            //         $($tab).addClass("animated slideInRight faster");
            //     } else {
            //         $($tab).addClass("animated slideInLeft faster");
            //     }
            // }
            if ($.fn.dataTable.isDataTable(".js-datatable-draggable") == false) {
                charPopup.initSecondTabDataTable();
            }

        }
        js_last_active_tab = $tab_order;
    })



    $("body").on("click", ".js-user-remove-selected", function () {
        const tagContent = $(this).parents("li").find("span.tagContent").text();
        $('.js-user-select2 option').each(function () {
            if ($.trim($(this).val()) == $.trim(tagContent)) {
                $(this).remove();
            }
        });
        $(this).parents("li").find(".select2-selection__choice__remove").click();
    })

    const messageSelect = $(".js-user-select2").select2({
        allowClear: true,
        dropdownParent: $("#js-modal-window-message"),
        ajax: {
            url: '/office/action/getClientsJson.php',
            data: function (params) {
                var query = {
                    query: params.term,
                    type: 'public'
                }

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {

                var items = $.map($.parseJSON(data).results, user => ({
                        name: user.name,
                        id: user.id,
                        img: user.img,
                        clientId: user.id,
                        phone: user.phone,
                        status: user.status
                    })

                )


                return {
                    results: items
                };
            }
        },
        multiple: true,
        placeholder: lang('start_type_cal'),
        minimumInputLength: 2,
        theme: "bsapp-dropdown-user-select",
        templateResult: formatState,
        templateSelection: function (item) {
            if ('params' in item.element.dataset) {

                var param = JSON.parse(item.element.dataset.params);

                $item = $('<div clientId="' + param.id + '" class="custom-user-tags bg-light border border-gray-300 px-5 pt-2 pb-3  rounded d-flex justify-content-between align-items-center bsapp-fs-14"><div><img src="' + param.img + '" class="w-20p h-20p rounded-circle mie-5" /><span class="tagName"> ' + param.name + ' </span><span style="display:none" class="tagContent"> ' + param.email + ' </span></div><a class="text-danger js-user-remove-selected mis-10 " href="javascript:;"><i class="fal fa-minus"></i> </a></div>');
            } else {
                if (item.id == '') {
                    $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('enter_name_cal') + '</div><div> </div> </div>');
                } else {
                    $item = $('<div clientId="' + item.clientId + '" class="custom-user-tags bg-light border border-gray-300 px-5 pt-2 pb-3  rounded d-flex justify-content-between align-items-center bsapp-fs-14"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span><span style="display:none" class="tagContent"> ' + item.id + ' </span></div><a class="text-danger js-user-remove-selected mis-10 " href="javascript:;"><i class="fal fa-minus"></i> </a></div>');
                }
            }
            return $item;
        }
    });

    // $(".js-user-select2").on("select2:select", function (e) {
    //     if (e.params.args.data) {
    //         $(this)
    //                 .parents(".window-message-user")
    //                 .addClass("custom-user-design");
    //         $(this)
    //                 .parents(".window-message-user")
    //                 .find("input.select2-search__field")
    //                 .attr("placeholder", "Type here to add more");
    //     }
    // });

    //https://select2.org/programmatic-control/add-select-clear-items

    $(".js-user-select2").on("select2:selecting", function (e) {
        $("#sendMsgForm").find(".selectError").remove();
        $(this)
                .parents(".window-message-user")
                .addClass("custom-user-design");

        $(this)
                .parents(".window-message-user")
                .find("input.select2-search__field")
                .attr("placeholder", lang('type_here_to_add_cal'));

    });


    $('body').on('click', '.select-item', function () {
        var userSelect = $('.js-user-select2');
        var userCompany = [];
        userSelect.val(null).trigger("change");
        userCompany.push($(this).closest("tr").find(".js-modal-user").attr('data-client-id'));
        getCompany(userCompany, userSelect);
    });

    $('body').on('click', '#sendMessage, .js-send-checked-messages', function () {
        var userCompany = [];
        var userSelect = $('.js-user-select2');
        userSelect.val(null).trigger("change");

        $(this).parents(".modal-body").find(".tab-pane.active table.bsapp-company-details > tbody > tr").not('#js-no-clients').each(function (index, tr) {
            if ($(tr).find("input.js-client-checkbox").is(":checked")) {
                var text = $(tr).find("span.company-name").text();
                if (text != '') {
                    userCompany.push($.trim(text));
                }
            }
        });
        getCompany(userCompany, userSelect);
    });

    $('body').on('click', '.js-send-message-to-all', function () {
        var userCompany = [];
        var userSelect = $('.js-user-select2');
        userSelect.val(null).trigger("change");
        $(this).parents(".modal-body").find("table#company-details > tbody > tr").not('#js-no-clients').each(function (index, tr) {
            var text = $(tr).find("span.company-name").text();
            userCompany.push($.trim(text));
        });
        getCompany(userCompany, userSelect);
    });

    $(".cancelBtn").on("click", function (e) {
        $(".close-btn").click();
        $("#js-modal-window-message").modal('hide');
    })

    $("#emailOnly").on("keyup", function () {
        $(this).next(".inputError").remove();
    });

    $(".close-btn").on("click", function (e) {
        e.preventDefault();
        let jsUsersElmnt = $("#jsUsers");
        let selectedMsgElmnt = $("#selectedMsgTo");
        let emailOnly = $("#emailOnly");
        let messageContentElmnt = $("#clientemailmessage");

        formReset(jsUsersElmnt, selectedMsgElmnt, emailOnly, messageContentElmnt);
        $("#sendMsgForm .response").html("");
    })

    function getCompany(userCompany, userSelect) {
        $("#js-modal-window-message").modal('show');
        $(".js-window-message-shimming-loader").removeClass("d-none").addClass("d-block");
        $.ajax({
            url: "/office/action/getClientBulkJson.php",
            type: "POST",
            data: {companyName: userCompany},
            success: function (response) {
                var items = $.parseJSON(response).results;

                $(items).each(function (i, item) {

                    if (userSelect.find("option[value='" + item.id + "']").length) {
                        userSelect.val(item.id).trigger('change');
                        userSelect.trigger('select2:selecting');
                    } else {
                        var newOption = new Option(item.name, item.id, false, true);
                        newOption.dataset.params = JSON.stringify(item);
                        userSelect.append(newOption);
                        userSelect.trigger('change');
                        userSelect.trigger('select2:selecting');
                    }
                });
                $(".js-window-message-shimming-loader").removeClass("d-block").addClass("d-none");

            },
            error: function (jqXHR, textStatus, errorThrown) {

                $(".js-window-message-shimming-loader").removeClass("d-block").addClass("d-none");
            }
        });
    }
    /*
     $(".js-user-name-select2").select2({
     dropdownParent : $("#js-modal-add-user"),
     theme : "bsapp-dropdown bsapp-no-arrow" ,
     tags: true ,
     allowClear : true,
     placeholder: "Select A User.." ,
     ajax: {
     //url: 'https://api.github.com/orgs/select2/repos',
     url: 'http://localhost/boostapp/demo3.json',
     data: function (params) {
     var query = {
     search: params.term,
     type: 'public'
     }
     
     // Query parameters will be ?search=[term]&type=public
     return query;
     },
     processResults: function (data) {
     // Transforms the top-level key of the response object from 'items' to 'results'
     return {
     results: data.items
     };
     }
     },
     templateResult : function( state ){
     return state.text;
     },
     templateSelection : function( item ){
     
     if( $(item.element).attr("data-select2-tag") == 'true' ){
     $item = $('<div class="d-flex justify-content-between align-items-center"><div>'+ item.text +'</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill"> New Item </div> </div> </div>');
     }else{
     $item = $('<div class="d-flex justify-content-between align-items-center"><div>'+ item.text +'</div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
     }
     return $item ;
     }
     });
     */

    $(".js-user-name-select2").select2({
        dropdownParent: $("#js-modal-add-user"),
        theme: "bsapp-dropdown bsapp-no-arrow",
        tags: true,
        allowClear: true,
        placeholder: lang('select_user_cal'),
        ajax: {
            //url: 'http://localhost:8000/office/action/getClientsJson.php?query=',
            //url: '<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/assets/demo/demo3.json'; ?>',
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'name'
                }

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data.items
                };
            }
        },
        templateSelection: function (item) {

            if ($item == null) {
                $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('create_new') + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill"> ' + lang('new_item') + ' </div> </div> </div>');
            } else {
                $item = $('<div class="d-flex justify-content-between align-items-center"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
            }
            return $item;
        }
    });


    // $(".js-user-number-select2").select2({
    //     dropdownParent: $("#js-modal-add-user"),
    //     theme: "bsapp-dropdown bsapp-no-arrow",
    //     tags: true,
    //     createTag: function (tag) {
    //                     return {
    //                         id: tag.term,
    //                         text: tag.term,
    //                         isNew: true
    //                     };
    //                 },
    //     placeholder: "Enter Phone Number..",
    //     ajax: {
    //         //url: 'https://api.github.com/orgs/select2/repos',
    //         // url: '<?php echo "//" . $_SERVER['HTTP_HOST'] . '/office/assets/demo/demo3.json'; ?>',
    //         data: function (params) {
    //             var query = {
    //                 search: params.term,
    //                 type: 'public'
    //             }

    //             // Query parameters will be ?search=[term]&type=public
    //             return query;
    //         },
    //         processResults: function (data) {
    //             // Transforms the top-level key of the response object from 'items' to 'results'
    //             return {
    //                 results: data.items
    //             };
    //         }
    //     },
    //     templateSelection: function (item) {

    //         $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div><span class="" title=""><i class="fal fa-check text-info"></i></span></div></div>');
    //         return $item;
    //     }
    // });
    $("body").on("click", ".js-select2-selection__clear", function (e) {
        e.preventDefault();
        $(this).parents(".select2-selection__rendered").find(".select2-selection__clear").trigger("mousedown");
    })




    //checkbox selection :: begin

    $("body").on("click", ".js-img-to-check img", function () {
        var $parent = $(this).parents("table");
        $(this).siblings(".form-check").find("input[type='checkbox']").prop("checked", true);
        var total_checks = $parent.find(".js-img-to-check input[type='checkbox']").length;
        var total_checked = $parent.find(".js-img-to-check input[type='checkbox']:checked").length;
        $parent.addClass("bsapp-disabled-actions");
        // sequence of events :: begin
        // this sequence is reverted in case of unselected
        $parent.find(".js-img-to-check").addClass("bsapp-check-shown");
        $parent.find(".js-img-to-check img").addClass("d-none");
        $parent.find(".js-img-to-check .form-check").removeClass("d-none");
        $parent.find(".js-tbl-select-all").removeClass("d-none");
        $parent.find(".js-fa-user-friends").hide();
        $(".js-bottom-action-bar").removeClass("d-flex").addClass("d-none");

        $('[data-context="' + $parent.attr("data-bottom-bar") + '"').removeClass("d-none").addClass("d-flex");
        //sequence of events :: end
    })

    $("body").on("click", ".js-img-to-check input[type='checkbox']", function () {
        var $parent = $(this).parents("table");
        var total_checks = $parent.find(".js-img-to-check input[type='checkbox']").length;
        var total_checked = $parent.find(".js-img-to-check input[type='checkbox']:checked").length;
        if ($(this).prop("checked") == true) {

            if (total_checked == total_checks) {
                $parent.find(".js-check-select-all").prop("checked", true);
            }

        } else {

            if (total_checked == 0) {
                //sequence of events :: begin
                //reverted the selection sequence
                $parent.removeClass("bsapp-disabled-actions");
                $parent.find(".js-img-to-check").removeClass("bsapp-check-shown");
                $parent.find(".js-img-to-check img").removeClass("d-none");
                $parent.find(".js-img-to-check .form-check").addClass("d-none");
                $parent.find(".js-tbl-select-all").addClass("d-none");
                $parent.find(".js-fa-user-friends").show();
                $(".js-bottom-action-bar").addClass("d-flex").removeClass("d-none");
                $('[data-context="' + $parent.attr("data-bottom-bar") + '"').removeClass("d-flex").addClass("d-none");
                //sequence of events :: end
            }

            $parent.find(".js-check-select-all").prop("checked", false);
        }

    })


    $("body").on("click", ".js-check-select-all", function () {
        var $parent = $(this).parents('table');
        if ($(this).prop("checked") == true) {
            $parent.find(".js-img-to-check input[type='checkbox']").prop("checked", true)
        } else {
            $parent.find(".js-img-to-check input[type='checkbox']").prop("checked", false)
        }

    })

    //checkbox selection :: end
    // $(".js-datatable").dataTable({
    //     "dom": "t",
    //     "columnDefs": [
    //         {"width": "100px", "targets": [0]},
    //         {"width": "50px", "targets": [1]},
    //         ($('#device-column').html() ? {"width": "50px", "targets": [2]} : {}),
    //         ($('#device-column').html() ? {"width": "30px", "targets": [3]} : {"width": "30px", "targets": [2]}),
    //         ($('#device-column').html() ? {"width": "10px", "targets": [4]} : {"width": "10px", "targets": [3]}),
    //     ],
    //     "aoColumns": [
    //         { "bSortable": false },
    //         { "bSortable": false },
    //         ($('#device-column').html() && { "bSortable": false }),
    //         { "bSortable": false },
    //         { "bSortable": false },

    //     ],
    // })




    //$(".js-datepicker").datepicker();

    $(".js-select2").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown",
        // dropdownParent: $("#js-modal-add-user")
    })

    $("#js-modal-add-user .js-select-custom2").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown",
        dropdownParent: $("#js-modal-add-user"),
        templateResult: function (option) {
            var isLead = $('input[name="client-isLead"]').val() != 'bg-success';
            var chooseMembership = $("#js-charge-options-exist").val() == 'choose-membership';
            var checked = $('#client-activities').find('input[name="membership"]:checked');
            if(checked.data() != undefined) {
                var membershipDepartment = checked.data().department == '3';
            }
            else membershipDepartment = false;
            var selectedDepartment = $("#js-items-container").find(':selected').data().department == '3' ?? 'false';

            if ((option.id == '2' && chooseMembership && isLead) ||
                (option.id == '2' && chooseMembership && membershipDepartment) ||
                (option.id == '2' && selectedDepartment)) {
                return;
            }
            return option.text;
        }
    });

    $("#js-modal-edit-class-popup .js-select2-edit-class").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown",
        dropdownParent: $("#js-modal-edit-class-popup")
    });

    $("#js-modal-window-message .js-select-message2").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown",
        dropdownParent: $("#js-modal-window-message")
    });

    $(".js-select-message2").change(function () {
        if ($(this).val() == "2") {
            $(".subject-form").addClass("d-block");
            $(".subject-form").removeClass("d-none");
        } else {
            $(".subject-form").removeClass("d-block");
            $(".subject-form").addClass("d-none");
        }
    });

    $(".chooseOption").change(function () {
        if ($(this).val() == "2") {
            $(".showOption").addClass("d-block");
            $(".showOption").removeClass("d-none");
        } else {
            $(".showOption").removeClass("d-block");
            $(".showOption").addClass("d-none");
        }
    });

    // $(".chooseOption").on("select2:selecting", function (e) {
    //     console.log(e.params.args.data.val);
    //     if (e.params.args.data.val = 2 ) {
    //          $(".showOption").addClass("d-block");
    //          $(".showOption").removeClass("d-none");
    //     } else {
    //         $(".showOption").removeClass("d-block");
    //         $(".showOption").addClass("d-none");
    //     }
    // });

    $(".js-select2-only-dropdown").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown",
        dropdownCssClass: "w-100p",
        templateSelection: function (item) {
            $item = $('<div></div>');
            return $item;
        }
    })



    //modal inner functionality code :: begin
    // js-cancel-action-modal :: begin
    $("body").on("click", "[name='cancel_action_radio']", function () {
        var id = $(this).attr("id");
        $('[data-context="' + id + '"]').showFlex();
        if (id == "js-ca-radio-1") {
            $('[data-context="js-ca-radio-2"]').hideFlex();
        }
        if (id == "js-ca-radio-2") {
            $('[data-context="js-ca-radio-1"]').hideFlex();
        }
    })

    $("#js-series-dropdown").on("select2:selecting", function (e) {
        var val = e.params.args.data.id;

        $('[data-context="' + val + '"]').showFlex();
        if (val == "js-series-option-1") {
            $('[data-context="js-series-option-3"]').hideFlex();
            $('[data-context="js-series-option-2"]').hideFlex();
        }
        if (val == "js-series-option-2") {
            $('[data-context="js-series-option-3"]').hideFlex();
        }
        if (val == "js-series-option-3") {
            $('[data-context="js-series-option-2"]').hideFlex();
        }
    })
    // js-cancel-action-modal :: end

    // js-edit-action-modal :: begin
    $("body").on("click", "[name='edit_action_radio']", function () {
        var id = $(this).attr("id");
        $('[data-context="' + id + '"]').showFlex();
        if (id == "js-ed-radio-1") {
            $('[data-context="js-ed-radio-2"]').hideFlex();
        }
        if (id == "js-ed-radio-2") {
            $('[data-context="js-ed-radio-1"]').hideFlex();
        }
    })
    $("#js-ed-series-dropdown").on("select2:selecting", function (e) {
        var val = e.params.args.data.id;

        $('[data-context="' + val + '"]').showFlex();
        if (val == "js-ed-series-option-1") {
            $('[data-context="js-ed-series-option-3"]').hideFlex();
            $('[data-context="js-ed-series-option-2"]').hideFlex();
        }
        if (val == "js-ed-series-option-2") {
            $('[data-context="js-ed-series-option-3"]').hideFlex();
        }
        if (val == "js-ed-series-option-3") {
            $('[data-context="js-ed-series-option-2"]').hideFlex();
        }
    })
    $("#openMainPopup").click(function () {
        setEmptyMainCalendarPopup();
        openPopup("mainPopup");
        //$("#js-modal-edit-class-popup").modal('hide');
    });
    $("#openMainPopupEdit").click(function () {
        setEmptyMainCalendarPopup();
        openPopup("mainPopup");
        //$("#js-modal-edit-class-popup").modal('hide');
    });
    // js-edit-action-modal :: end

    // js-modal-add-user ::  begin
    $(".js-user-number-select2").on("select2:selecting", function () {
        $('[data-context="js-user-number-select2"]').showFlex();
    })
    // js-modal-add-user ::  end


    //modal inner functionality code :: end

    // $('#clientemailmessage').summernote('code');
    $('#clientemailmessage').summernote({
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

    $('#Remarks').summernote({
        followingToolbar: false,
        placeholder: lang('type_here_class_content'),
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['ul', 'ol']]
        ]
    });

    $('body').on('shown.bs.modal', '#js-modal-class-content', function () {
        $('#Remarks').summernote('code', $('#js-class-remarks').html());
    })


    $(".showCopyLinkBtn button").click(function () {
        $(this).fadeOut();
        $(".copylink-box").fadeIn();
    });

    modalClassPopup.initUserSelect2();
});

$("#clientemailmessage").on("summernote.change", function (e) {

    $('#sendMsgForm').find(".contentError").remove();
});
$('body').on('submit', '#sendMsgForm', function (e) {
    e.preventDefault();
    let jsUsersElmnt = $("#jsUsers");
    let messageContentElmnt = $("#clientemailmessage");
    let selectedMsgElmnt = $("#selectedMsgTo");
    let emailOnly = $("#emailOnly");
    let isFormValid = true;


    /*For search*/
    var clientIds = [];
    if (jsUsersElmnt.find("option").length == 0) {
        displayError(jsUsersElmnt, "Please select client");
        return "Client is required";
        isFormValid = false;
    } else {
        $(".custom-user-tags").each(function (i, row) {
            clientIds.push($(row).attr("clientId"));
        })
        jsUsersElmnt.parent().find(".selectError").remove();
    }

    /*For dropdown*/
    var sendType = selectedMsgElmnt.children("option:selected").val();

    if (sendType == "2") {

        if (emailOnly.val() === undefined || emailOnly.val() === "") {
            displayError(emailOnly, "Email Subject is required");
            return "Email is required";
            isFormValid = false;
        } else {
            emailOnly.parent().find(".inputError").remove();
        }
    } else {
        emailOnly.parent().find(".inputError").remove();
    }

    /*For content*/
    var content = messageContentElmnt.val();
    if (content === undefined || content === "" || content === "<p><br></p>") {
        displayError(messageContentElmnt, "Content is required");
        return "Content is required";
        isFormValid = false;
    } else {
        messageContentElmnt.parent().find(".inputError").remove();
    }

    if (isFormValid) {

        $(".js-window-message-shimming-loader").removeClass("d-none").addClass("d-block");
        var data = {
            fun: "sendMessage",
            clientIds: clientIds,
            sendType: sendType,
            content: content,
            subject: emailOnly.val()
        }

        $.ajax({
            url: '/office/ajax/Trainees.php',
            data: data,
            type: 'POST',
            success: function (response) {
                $(".js-window-message-shimming-loader").removeClass("d-block").addClass("d-none");
                if (response.Status == "Success") {
                    formReset(jsUsersElmnt, selectedMsgElmnt, emailOnly, messageContentElmnt);
                    // let message = '<div class="alert alert-success" role="alert">'+ lang('action_done') +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    // $("#sendMsgForm .response").html(message);
                    $.notify({
                        // options
                        message: lang('action_done')
                    }, {
                        // settings
                        type: 'success',
                        z_index: 2000,
                    });
                    $('#js-modal-window-message').modal('hide');
                } else {
                    // let error = '<div class="alert alert-danger" role="alert">' + response.Message + '</div>';
                    // $("#sendMsgForm .response").html(error);
                    $.notify({
                        // options
                        message: lang('error_oops_something_went_wrong')
                    }, {
                        // settings
                        type: 'success',
                        z_index: 2000,
                    });
                }

            }
        });
    }
});

function formReset(jsUsersElmnt, selectedMsgElmnt, emailOnly, messageContentElmnt) {
    jsUsersElmnt.val(null).trigger("change");
    selectedMsgElmnt.val(0).trigger("change");
    $("#jsUsers option").each(function () {
        $(this).remove();
    });
    emailOnly.val('');
    messageContentElmnt.summernote('code', '<p><br></p>');

}
/*Ocean*/
/*$('body').on("click", ".js-add-waiting-list", function (e) {
 e.preventDefault();
 let tr = $(this).closest('tr');
 let classId = $('#js-class-data').data('classid');
 let actIdArr = [tr.data('actid')];
 
 // $.post('ajax/Trainees.php', {fun: 'assignWaitingToClass', actIdArr: actIdArr, classId: classId, overridePopup: 0}, function (result) {
 //         console.log("result", result);
 //     if (result.Status == 'over') {
 //         $('#js-modal-over-max-client').modal('show');
 //         charPopup.showMaxClientPopup(actIdArr, classId, result.data.overClients, 'one');
 //         return;
 //     }
 //     handleSingleAssignment(result, classId);
 // });
 $("#js-pill-tab-2").click();
 
 })*/
/*VALIDATION FUNCTION HERE*/
function digitsTest(val) {
    let pattern = /^[0-9]\d*(\.\d+)?$/;
    return pattern.test(val);
}
function isEmpty(obj) {
    for (let key in obj) {
        if (obj.hasOwnProperty(key))
            return false;
    }
    return true;
}
function displayError(elem, msg) {


    elem.css("border-color", "red");
    if (elem.parent().find(".inputError").length > 0) {
        elem.parent().find(".inputError").html('<label style="color: red" class="inputError">' + msg + '</label>');
    } else {
        elem.parent().append('<label style="color: red" class="inputError">' + msg + '</label>');
        elem.parent().addClass('error-div');
    }

    $('html, body').animate({
        scrollTop: elem.offset().top - 100
    }, 1000);
}


$(document).on({
    'show.bs.modal': function () {
        var zIndex = 1040 + (10 * $('.modal.js-modal-no-close:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    },
    'hidden.bs.modal': function () {
        if ($('.modal.js-modal-no-close:visible').length > 0) {
            // restore the modal-open class to the body element, so that scrolling works
            // properly after de-stacking a modal.
            setTimeout(function () {
                $(document.body).addClass('modal-open');
            }, 0);
        }
    }
}, '.modal');