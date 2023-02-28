const LeadsData = {
    GetDataManageLeads: function (pipeId, agentId) {
        if (!pipeId) pipeId = $('.js-manage-leads-body #ChoosePipeline option:selected').val();
        LeadsData.showShimmingLoader();
        const apiProps = {
            fun: "GetDataManageLeadsPage",
            PipeId: pipeId,
            AgentId: agentId
        }
        postApi('ManageLeadsView', apiProps, 'LeadsData.LoadManageLeadsData', true)
    },
    closePopupAfterChanged: function (){
        const pipeId = $('.js-manage-leads-body #ChoosePipeline option:selected').val();
        this.GetDataManageLeads(pipeId);
        $('.ip-modal').filter(function () {
            return $(this).css('display') != 'none';
        }).find('.ip-close:first').trigger('click');
    },
    btnPopoverClick: function (elem, event) {
        event.stopPropagation();
        $(elem).popover({
            html: true,
            trigger: 'manual',
            placement: popover_placement
        }).popover('toggle');
        $('.btnPopover').not(elem).popover('hide');
    },
    showShimmingLoader: function () {
        $(".js-manage-leads-body .js-loading-leads-shimmer:first").show();
        $('.js-manage-leads-body .js-table-lead-statuses div:not(#js-error-get-data-lead-statuses)').remove();
        $('.js-manage-leads-body .js-table-lead-statuses #js-error-get-data-lead-statuses:first').addClass('d-none');
        $('.js-manage-leads-body #ChooseAgentForPipeline:first').attr('disabled', true);
        $('.js-manage-leads-body #ChoosePipeline:first').attr('disabled', true);
    },
    hideShimmingLoader: function () {
        $(".js-manage-leads-body .js-loading-leads-shimmer:first").hide();
        $('.js-manage-leads-body #ChooseAgentForPipeline:first').attr('disabled', false);
        $('.js-manage-leads-body #ChoosePipeline:first').attr('disabled', false);
    },
    errorChecking: function (response) {
        if (response.Status) {
            return true
        } else {
            $.notify({
                message: lang('error_oops_something_went_wrong')
            }, {
                type: 'danger',
                z_index: 2000,
            });
        }
    },
    showMorePipeLine: function (elem) {
        var ID = $(elem).data('ajax');
        var count = $('#DashPipe' + ID + ' li').length - 1;
        $('#loadmore' + ID).hide();
        $('#loading' + ID).show();
        const PipeId = $(".js-manage-leads-body #ChoosePipeline option:selected").val();
        const AgentId = $('.js-manage-leads-body #ChooseAgentForPipeline option:selected').val();
        let data = {
            id: ID,
            count,
            PipeId,
        }
        if (AgentId)
            data.AgentId = AgentId;
        else
            data.All = 'True';

        $.ajax({
            type: 'POST',
            url: 'MoreLeades.php',
            data,
            success: function (html) {
                $('#loading' + ID).hide();
                $('#DashPipe' + ID).html("");
                $('#DashPipe' + ID).append('<li class="item list-group-item text-start text-dark padding-0 cursorcursor lidiv" style="display: none;"></li>' + html);
                $('.sortable').trigger('sortupdate');
                if (localStorage.getItem("limitflag") == "0") {
                    $('#loadmore' + ID).show();
                }
                localStorage.removeItem('limitflag');
                var count = $(`#DashPipe${ID} li`).length - 1;
                if (count == '') {
                    count = '1';
                }
                $(window).scrollTop($('#DashPipe' + ID + ' li:nth-last-child(' + count + ')').offset().top);
            }
        });
    },
    updateSort: function () {
        let elements = $('.js-manage-leads-body .js-table-lead-statuses ul[id]');
        let PipeTitlesIds = [];
        elements.each(function () {
            PipeTitlesIds.push($(this).attr('id'))
        });
        PipeTitlesIds.forEach((PipeTitle) => {
                $(`#DashPipe${PipeTitle}`).each(function () {
                    $(this).html($(this).children('li').sort(function (a, b) {
                        if (($(a).data('sort')) == ($(b).data('sort'))) {
                            // score is the same, sort by endgame
                            if (($(a).data('id')) > ($(b).data('id'))) return 1;
                        }
                        // sort the higher score first:
                        return ($(a).data('sort')) > ($(b).data('sort')) ? 1 : -1;
                    }));
                });
            }
        )
    },
    LoadManageLeadsData: function (data) {
        if (!this.errorChecking(data)) {
            this.hideShimmingLoader();
            $('.js-manage-leads-body .js-table-lead-statuses #js-error-get-data-lead-statuses:first').removeClass('d-none');
            return
        }
        const {GetSuccess, GetFails, GetNoneFails, VoiceCenterToken, CanAddLead, CanEditAndAddPipeLine} = data.response;
        const leadStatuses = data.response.LeadStatuses.reverse();
        const dataRender = leadStatuses.map((data, index) => this.renderData(data, VoiceCenterToken, CanAddLead, CanEditAndAddPipeLine, index));
        dataRender.forEach(a => $('.js-table-lead-statuses:first').append(a));
        $('.wonlosediv:first').empty();
        $(".wonlosediv:first").append(LeadsData.renderActLeads(GetSuccess, GetFails, GetNoneFails));
        $('[data-toggle="tooltip"]').tooltip();
        $(".sortable").sortable({
            items: "li:not(.unsortable)",
            dropOnEmpty: true,
            opacity: 0.5,
            zIndex: 999,
            scroll: false,
            cancel: '.disablesortable',
            connectWith: ".sortable",
            start: function (event, ui) {
                StartId = this.id;
                ui.item.addClass("ui-draggable-dragging");
                $(".wonlosediv").show();
                $('.sortable').sortable('refresh');
            },
            receive: function (event, ui) {
                if (this.id == `${$companyNo}100`) {
                    ui.sender.sortable("cancel");
                } else if (this.id == `${GetSuccess}` || this.id == `${GetFails}` || this.id == `${GetNoneFails}`) {
                    ui.item.remove();
                }
            },
            over: function (event, ui) {
                if (this.id == `${$companyNo}100` || this.id == `${GetSuccess}` || this.id == `${GetFails}` || this.id == `${GetNoneFails}`) {
                    $('.uldiv').removeClass('sortable');
                    $('.lidiv').removeClass('item');
                    $('.uldiv').addClass('SmallDiv');
                    $(".sortable").sortable('disable');
                    //     $('.uldiv').css('z-index', '-1');
                } else {
                    $('.uldiv').addClass('sortable');
                    $('.lidiv').addClass('item');
                    $('.uldiv').removeClass('SmallDiv');
                    $(".sortable").sortable('enable');
                }
                $('.uldiv').removeClass("bg-light");
                $('.getbackground' + this.id).addClass("bg-light");
                $(`.wonlosedivbg${GetSuccess}`).removeClass("hover");
                $(`.wonlosedivbg${GetFails}`).removeClass("hover");
                $(`.wonlosedivbg${GetNoneFails}`).removeClass("hover");
                $('.wonlosedivbg' + this.id).addClass("hover");
            },
            update:
                function (event, ui) {
                    $('.popover').popover('hide'); /// הסתר פרטים
                    $('.uldiv').addClass('sortable');
                    $('.lidiv').addClass('item');
                    $('.uldiv').removeClass('SmallDiv');
                    $(".sortable").sortable('enable');
                    LeadsData.updateSort();

                    const AgentId = $('#ChooseAgentForPipeline option:selected').val();
                    if (this.id != StartId) {
                        switch (this.id) {
                            case GetFails:
                                archivePopupVars.newStatus = 1;
                                break;
                            case GetSuccess:
                                archivePopupVars.newStatus = 0;
                                break;
                            default:
                                archivePopupVars.newStatus = 2;
                                break;
                        }

                        const submitBtn = archivePopupVars.popUp.find('#submitReason');
                        if (archivePopupVars.newStatus == 1) {
                            if (!submitBtn.length){
                                archivePopupVars.requestType = 0;
                                archivePopupVars.pipeId = this.id;
                                archivePopupVars.leadId = ui.item[0].id;

                                CreateFailReasonPopupButtons(true);
                            }
                            archivePopupVars.popUp.modal('show');
                        } else {
                            $.ajax({
                                url: "action/UpdatePipeNew.php",
                                data: {
                                    PipeId: this.id,
                                    LeadId: ui.item[0].id,
                                    All: AgentId ? 'False' : 'True'
                                },
                                error: function (data) {
                                    alert(lang('error_oops_something_went_wrong'));
                                },
                                success: function (data) {
                                    $('#DashPipeCount' + data.PipeOldId).text(data.PipeOldCount); // update old pipe count
                                    $('#DashPipeCount' + data.PipeNewId).text(data.PipeNewCount); // update new pipe count
                                },
                                complete: function() {
                                }
                            });
                        }
                    }
                    if (this.id == `${GetSuccess}`) {
                        $.notify({
                            icon: 'fas fa-trophy',
                            message: `${lang('well_done_keep_up_the_good_work')}`,
                        }, {
                            type: 'success',
                        });

                        $("#SuccessConfetti").show();
                        setTimeout(function () {
                            $("#SuccessConfetti").fadeOut("slow");
                        }, 2000);
                    }
                    if (this.id == `${GetFails}`) {
                        $.notify({
                            icon: 'fas fa-times',
                            message: `${lang('too_bad_moving_to_new_lead')}`,
                        }, {
                            type: 'danger',
                            z_index: 999999,
                        });
                        var modalcode = $('#PipeReasonsPopup');
                        modalcode.modal('show');
                        $('#ReasonsItemId').val(ui.item[0].id);
                    }
                    if (this.id == `${GetNoneFails}`) {
                        $.notify({
                            icon: 'fas fa-trash-alt',
                            message: `${lang('lead_moved_to_trash')}`,
                        }, {
                            type: 'secondary',
                        });
                    }
                },
            stop: function (event, ui) {
                $(function () {
                    ui.item.removeClass("ui-draggable-dragging");
                    $('.uldiv').removeClass("bg-light");
                    $('.uldiv').removeClass('SmallDiv');
                    $(".wonlosediv").hide();
                });
                $('.sortable').sortable('refresh');
            },
        }).disableSelection();
        $(".sortable").disableSelection();
        $('.sortable').on('sortupdate', function () {
            LeadsData.updateSort();
        });

        LeadsData.hideShimmingLoader();
    },
    renderData: function (PipeTitle, voiceCenterToken, CanAddLead, CanEditAndAddPipeLine, index) {
        let dataOfPipeLine = PipeTitle.pipeLines.map(data => LeadsData.foreachPipeLine(data, voiceCenterToken, CanAddLead, CanEditAndAddPipeLine));
        let htmlLeadStatus = `
            <div class="col-md col-sm-12" style="padding: 0px; border-bottom: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5; ${index == 0 && "border-left: 1px solid #e5e5e5"};">
                <ul class="list-group list-special sortable uldiv  getbackground${PipeTitle.id}"
                    id="${PipeTitle.id}" style="min-height: 300px;height: 100%;">
                    <a data-toggle="collapse" href="#DashPipe${PipeTitle.id}" aria-expanded="true"
                       aria-controls="DashPipe${PipeTitle.id}" class="text-dark" data-placement="bottom"
                       style="text-decoration: none;">
                        <li class="unsortable d-flex justify-content-between  list-group-item text-start text-dark padding-0 bg-light"
                            style="padding:15px; border-bottom: 1px solid #e5e5e5;border-top: 1px solid #e5e5e5;">
                            <div>
                                <strong>${PipeTitle.Title}</strong>
                            </div>
                            <span class="text-secondary" style="font-size: 15px;">
                         <strong>
                         <span id="DashPipeCount${PipeTitle.id}" class="mie-5">${PipeTitle.count}</span>
                           <i class="fas fa-angle-double-right fa-lg " style="color: lightgray;"></i>
                         </strong>
                      </span>
                        </li>
                    </a>
                    <div class="collapse show" id="DashPipe${PipeTitle.id}">
                        <li class="item list-group-item text-start text-dark padding-0 cursorcursor lidiv"
                            style="display: none;"></li>`

        dataOfPipeLine.forEach(pipeLine => htmlLeadStatus += pipeLine);

        htmlLeadStatus += `</div>
                            <span id="loadmore${PipeTitle.id}" data-ajax="${PipeTitle.id}" onclick="LeadsData.showMorePipeLine(this)" class="show_more" ${(PipeTitle.count <= dataOfPipeLine.length || dataOfPipeLine.length == 0) && "style='display: none;'"} title="${lang('load_more')}..."> ${lang('load_more')} <i class="fas fa-caret-down"></i></span>
                            <span id="loading${PipeTitle.id}" class="loading" style="display: none;"><span class="loading_txt" > ${lang('loading')} <i class="fas fa-spinner fa-pulse"></i></span></span>
                            </ul>
                        </div>`;

        return htmlLeadStatus;
    },

    getDataPopUpHtml: function (PipeLine) {
        if (!PipeLine.Tasks || PipeLine.Tasks == '')
            return '';

        let DataPopUp = '', ColorRed, textColor;
        const Loops = JSON.parse(PipeLine.Tasks);
        Loops['data'].forEach(val => {
            if (val.Date < moment().format('YYYY-MM-DD') || (val.Date == moment().format('YYYY-MM-DD') && val.Time < moment().format('HH:mm:ss'))) {
                ColorRed = '#ff8080';
                textColor = 'text-danger';
            } else {
                textColor = 'text-success';
            }
            DataPopUp += `<div class='row ${textColor}'>
                            <a class='col-12 js-new-task' data-task='${val.Id}' data-client='${PipeLine.ClientId}' data-pipe-id='${PipeLine.id}' role='button'><i class='${val.Icon} fa-xs'></i> ${val.Title}<br>
                                <i class='fas fa-calendar-alt fa-xs'></i> <span style='font-size: 11px;'>${moment(val.Time, 'HH:mm').format('HH:mm')} ${moment(val.Date, 'YYYY-mm-DD').format("DD/mm/YYYY")}</span>
                            </a>
                      </div><hr>`;
        })
        return DataPopUp;
    },

    getActClassData: function (Status) {
        let ClassActColor = 'text-secondary', ClassActText = lang('trial_lesson');
        if (Status == '2') {
            ClassActColor = 'text-primary';
            ClassActText = lang('arrived_to_lesson');
        } else if (Status == '7' || Status == '8') {
            ClassActColor = 'text-danger';
            ClassActText = lang('not_arrived_to_lesson');
        } else if (Status == '3' || Status == '4' || Status == '5') {
            ClassActColor = 'text-danger';
            ClassActText = lang('canceled_lesson');
        }

        return {ClassActColor, ClassActText}
    },

    getNewAge: function (Dob) {
        let NewAge, from, to;
        if (!Dob || Dob == '' || Dob == '0000-00-00')
            return '';
        from = new Date(Dob);
        to = new Date();
        var diffM = to.getMonth() - from.getMonth();
        var diffY = to.getFullYear() - from.getFullYear();
        if (diffM == 0 && to.getDate() < from.getDate())
            NewAge = (diffY - 1) + '.' + (diffM + 11);
        else if (diffM < 0)
            NewAge = (diffY - 1) + '.' + (diffM + 12);
        else
            NewAge = diffY + '.' + diffM;
        return NewAge;
    },

    getGenderInfoHtml: function (Gender) {
        if (Gender == '1')
            return `<i class="fas fa-mars" data-toggle="tooltip" title="${lang('male')}"></i>`;
        if (Gender == '2')
            return `<i class="fas fa-venus" data-toggle="tooltip" title="${lang('female')}"></i>`;
        return '';
    },

    getConcatInfo: function (PipeLine) {
        if (PipeLine.ContactMobile != '')
            return PipeLine.ContactMobile;
        if (PipeLine.ContactMobile == '' && PipeLine.Email != '')
            return PipeLine.Email;
        return PipeLine.ContactInfo
    },

    foreachPipeLine: function (PipeLine, voiceCenterToken, CanAddLead, CanEditAndAddPipeLine) {
        const regexDigits = /^\d+$/;
        const DataPopUp = this.getDataPopUpHtml(PipeLine);
        const {ClassActColor, ClassActText} = this.getActClassData(PipeLine.CheckClassStatus);
        const NewAge = this.getNewAge(PipeLine.Dob);
        const GenderIcon = this.getGenderInfoHtml(PipeLine.Gender);
        const ContactInfo = this.getConcatInfo(PipeLine);
        const NewTask = '0';

        let htmlPipeLine =
            `<li class="item list-group-item text-start text-dark padding-0 cursorcursor ${!CanAddLead && !CanEditAndAddPipeLine ? 'disablesortable' : ''} pb-0"
            style="padding:5px; border-bottom: 1px solid #e5e5e5; pointer-events: stroke;"
            id="${PipeLine.id}"
            data-sort="${PipeLine.TaskStatus},${PipeLine.NoteDates}"
            data-id="${PipeLine.id}" data-clientid="${PipeLine.ClientId}">
            <span class="text-secondary"> ${GenderIcon} ${NewAge}</span>
            <a href="ClientProfile.php?u=${PipeLine.ClientId}" style="font-size: 15px;"
               style="text-decoration: none;" class="text-dark disablesortable">
                ${PipeLine.CompanyName}
            </a>
            <div class="d-flex justify-content-between" style="color: #AEAEAE;">
             <span style="font-size: 13px;" class="disablesortable unicode-plaintext">`

        if (!regexDigits.test(ContactInfo))
            htmlPipeLine += ContactInfo
        else {
            if (voiceCenterToken)
                htmlPipeLine += `<a href="javascript:void(0);" OnClick="CallToClient(${PipeLine.ClientId},${PipeLine.id})" style="color: #AEAEAE;">`
            else
                htmlPipeLine += `<a href="javascript:void(0);" style="color: #AEAEAE;">`

            htmlPipeLine += `${ContactInfo}
                     <span class="CallClientdivb${PipeLine.id}" style="display: none;">
                             <i class="fas fa-spinner fa-spin text-warning"></i>
                     </span>
                     <span class="CallClientdiv${PipeLine.id}" style="display: none;">
                         <span class="fa-layers fa-fw text-success">
                             <i class="fas fa-circle"></i>
                             <i class="fa-inverse fas fa-phone" data-fa-transform="shrink-6"></i>
                         </span>
                     </span>
                </a>`
        }

        htmlPipeLine += ` </span>
                        <span class="text-secondary disablesortable"> <a href='javascript:PipeMore(${PipeLine.id},${PipeLine.MainPipeId},${PipeLine.PipeId},${PipeLine.ClientId});'
                        class="text-secondary text-start disablesortable">  <i
                            class="fas fa-info-circle disablesortable" data-toggle="tooltip"
                            title="${lang('more_details')}"></i>
                        </a>`

        if (PipeLine.CheckNotes >= '1')
            htmlPipeLine += `<a href='javascript:PipeAddNote(${PipeLine.id},${PipeLine.ClientId});' class='text-secondary'><i class="fas fa-sticky-note disablesortable" data-toggle="tooltip" title="${lang('notes')}"></i> </a>`;
        if (PipeLine.CheckClassId != '')
            htmlPipeLine += `<a href='javascript:PipeAddCalss(${PipeLine.id},${PipeLine.ClientId});' class='${ClassActColor}' ><i class="fas fa-calendar-check disablesortable ${ClassActColor}" data-toggle="tooltip" title="${ClassActText}"></i></a>`;

        htmlPipeLine += `</span></div>
                <div class="d-flex justify-content-between" style="color: #AEAEAE;">
                     <span style="font-size: 13px;" class="disablesortable">
                         <a href="javascript:PipeAction('${PipeLine.id}','${PipeLine.MainPipeId}','${PipeLine.PipeId}','${PipeLine.ClientId}')"
                            class="text-start disablesortable">
                             <span class="text-secondary"><i class="fas fa-bars"></i>  ${lang('actions')}
                             </span>
                         </a>
                     </span>
                                
                    <a style="cursor:pointer" class="btnPopover text-start disablesortable" onclick="LeadsData.btnPopoverClick(this, event)"
                        rel="popover" data-toggle="popover" data-html="true" data-content="<div  style='width: 250px; padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>
                         <div class='DivScroll text-dark' style='max-height:220px; overflow-y:scroll; overflow-x:hidden;margin: 0px; padding: 0px; '>
                         ${DataPopUp}
                         </div>
                         <div style='text-align: center;' align='center'>`

        if (CanEditAndAddPipeLine) htmlPipeLine += `<a  data-task=${NewTask} data-client='${PipeLine.ClientId}'  data-pipe-id='${PipeLine.id}' class='text-dark js-new-task' role='button'>${lang('new_task')}</a>`

        htmlPipeLine += `</div>
                         </div>">
                         <style type="text/css">
                            .bsapp-corner-badge.badge-color-${PipeLine.id}:before {
                                border-color: transparent   ${PipeLine.StatusColor} transparent transparent;
                            }
        
                            [dir="rtl"] .bsapp-corner-badge.badge-color-${PipeLine.id}:before {
                                border-color: transparent transparent  ${PipeLine.StatusColor} transparent;
                            }
                        </style>
                        <div class="bsapp-corner-badge badge-color-${PipeLine.id}">`

        if (PipeLine.TaskStatus == '2') htmlPipeLine += `<i class="fas fa-exclamation bsapp-fs-10" style="color: #AEAEAE;"></i>`

        htmlPipeLine += ` </div></a> </div> </li>`;

        return htmlPipeLine;
    },

    renderActLeads: function (GetSuccess, GetFails, GetNoneFails) {
        return `<center>
              <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg${GetNoneFails}" id="${GetNoneFails}">
                 <ul style="list-style-type: none;" class="list-group list-special">
                    <li class="unsortable bg-secondary" style="padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-trash-alt fa-fw"></i> ${lang('not_relevant')}</li>
                 </ul>
              </div>
              <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg${GetFails}" id="${GetFails}">
                 <ul style="list-style-type: none;" class="list-group list-special">
                    <li class="unsortable bg-danger" style="padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-times fa-fw"></i> ${lang('failure')}</li>
                 </ul>
              </div>
              <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg${GetSuccess}" id="${GetSuccess}">
                 <ul style="list-style-type: none;" class="list-group list-special">
                    <li class="unsortable bg-success" style=" padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-trophy fa-fw"></i> ${lang('success')}</li>
                 </ul>
              </div>
           </center>`
    },
}







