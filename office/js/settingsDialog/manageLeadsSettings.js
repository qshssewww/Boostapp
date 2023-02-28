/***  constant: type-data **/
const TYPE_LEAD_STATUS = 'lead-status', TYPE_LEAD_SOURCE = 'lead-source', TYPE_PIPE_LINE = 'pipe-line';
/***  constant: status mode **/
const STATUS_ON = 0, STATUS_OFF = 1;
/***  constant: disabled mode **/
const NO_DISABLED = 0, DISABLED = 1;
/***  constant: length title **/
const MIN_LENGTH_TITLE = 0, MAX_LENGTH_TITLE = 71;

// const dropdown_hide = '<i class="fal fa-low-vision fa-fw mx-5"></i> <span>'+lang('hide')+'</span>',
//     dropdown_unhide = '<i class="fal fa-eye fa-fw mx-5"></i> <span>'+ lang('show_client_profile') +'</span>';

const LeadsSettings = {
    initSelect2: function () {
        $(".js-select2:not(.select2-hidden-accessible)").select2({
            theme: 'bsapp-dropdown'
        });
    },
    initSortable: function () {
        Sortable.create(js_sortable_container, {
            animation: 100,
            group: 'list-1',
            draggable: '.js-sortable-item',
            handle: '.js-grip-handle',
            sort: true,
            filter: '.sortable-disabled',
            chosenClass: 'active',
            onUpdate: function (evt) {
                const leadStatusIdsArray = [];
                const startSort = Math.min(evt.oldDraggableIndex,evt.newDraggableIndex);
                const endSort = Math.max(evt.oldDraggableIndex,evt.newDraggableIndex);
                let currElem;
                for(let i=startSort; i <= endSort; i++)
                {
                    currElem = $(`.lead-status-list li.js-fields:eq(${i})`);
                    leadStatusIdsArray.push(currElem.attr("data-id"));
                    currElem.find(".js-item-key:first").text(i);
                }
                const PipeId = currElem.closest(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id")
                if(!PipeId) return

                const apiProps = {
                    fun: "UpdateSortLeadStatus",
                    sortStart: startSort - 1,
                    leadStatusIdsArray,
                    PipeId
                };
                postApi('manageLeadsSettings', apiProps, 'leadStatusFunction.afterUpdateSort', true);
            },
        });
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
    openSettings: function () {
        $(".js-drop-menu:first").addClass("bsapp-js-show");
        $(".js-dropdown-inner:first").removeClass("d-none").addClass("d-block");
    },
    closeSettings: function (elem) {
        var $elem = $(elem);
        var $parent = $elem.closest("[data-page-id]");
        $parent.removeClass("d-flex").addClass("d-none");
        $("[data-page-id='js-tabs-home']").removeClass("d-none").addClass("d-flex");
        $(".js-dropdown-inner:first").removeClass("d-block").addClass("d-none");
        $(".js-drop-menu:first").removeClass("bsapp-js-show");
    },

    goTo: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.closest("[data-page-id]");
        var $target = $('[data-page-id="' + $elem.attr("data-next") + '"]');
        //$parent.removeClass("d-flex").addClass("d-none")
        // $target.removeClass("d-none").addClass("d-flex animated slideInStart animated");


        curr_depth = $parent.data('depth'),
            target_depth = $target.data('depth');

        if (target_depth < curr_depth) { // backwards
            $target.removeClass('d-none slideInStart animated')
                .addClass('d-flex');
            $parent.removeClass('slideInStart animated')
                .addClass('slideOutStart animated');
            setTimeout(function () {
                +
                    $parent.removeClass('d-flex slideOutStart animated')
                        .addClass('d-none slideInStart animated');
            }, 300)
        } else { // forward
            $target.addClass('slideInStart animated d-flex')
                .removeClass('d-none');
            setTimeout(function () {
                $parent.removeClass('d-flex slideInStart animated')
                    .addClass('d-none');
            }, 300);
        }
    },
    closePageAndSaveTitle: function (elem, event, isBack) {
        if(isBack) // go back
            this.goTo(elem, event);
        else // close settings
            this.closeSettings(elem);

        if($('.js-name-pipe-line:first').hasClass('changed')) {
            const PipeId = $(elem).closest(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id")
            let title = $('.js-name-pipe-line.changed:first').val();
            if (PipeId && title.length  > MIN_LENGTH_TITLE && title.length  <  MAX_LENGTH_TITLE) {
                const apiProps = {
                    fun: "UpdatePipeLineCategory",
                    id: PipeId,
                    Title: title,
                };
                postApi('manageLeadsSettings', apiProps, 'pipeLineCategoryFunction.afterUpdateName', true);
            }
        }
        $(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id", "");
        if($('.js-name-pipe-line:first').hasClass('border-danger'))
            $('.js-name-pipe-line:first').removeClass('border-danger').addClass('border-light');
    },
    addClassChanged: function (elem) {
        $(elem).addClass('changed');
    },
    removeClassChanged: function (selector) {
        $(selector).removeClass('changed');
    },
    renderItem: function(elem, id, name, status, key = null, isDisabled = NO_DISABLED){
        const switchId = Math.random();
        const hide_btn = status == STATUS_ON ? dropdown_hide : dropdown_unhide;
        elem.removeClass("d-none item-example").attr("data-id", id);
        elem.find(".js-item-name:first").text(name);
        elem.find(".js-item-id:first").text(id);
        elem.find(".js-item-status input:first").attr("id", `js-switch-id-${name}-${switchId}`).prop("checked", status == STATUS_ON);
        elem.find(".js-item-status label:first").attr("for", `js-switch-id-${name}-${switchId}`);
        elem.find('.js-item-text-status').prepend(hide_btn);
        if(key) {
            elem.find(".js-item-key:first").text(key);
        }
        if(isDisabled == DISABLED) {
            elem.find(".js-item-status input:first").prop('disabled', true);
            elem.find('.js-item-text-status').prop('disabled', true);
        }
        return elem;
    },
    showAddItem: function (elem) {
        elem.removeClass("d-none item-example");
        elem.find(".js-part-view").removeClass("d-flex").addClass("d-none");
        elem.find(".js-part-edit").removeClass("d-none").addClass("d-flex");
        return elem;
    },
    saveEdit: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.parents(".js-editable-item");
        $parent.find(".js-text-div").html($parent.find(".js-input-div").val());
        $parent.find(".js-part-edit").removeClass("d-flex").addClass("d-none");
        $parent.find(".js-part-view").removeClass("d-none").addClass("d-flex");
    },
    cancelEdit: function (elem, event) {
        const parent = $(elem).closest(".js-editable-item");
        parent.find(".js-part-edit:first").removeClass("d-flex").addClass("d-none");
        if($(elem).closest(".js-fields[data-id]").attr("data-id"))
            parent.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        else {
            $(elem).closest(".js-fields[data-id='']").remove();
        }
        if(parent.find(".js-part-edit:first").hasClass('border-danger')){
            parent.find(".js-part-edit:first").removeClass('border-danger');
        }
    },
    cancelStepEdit: function (elem, event) {
        const parent = $(elem).closest(".js-editable-item");
        parent.find(".js-part-edit:first").removeClass("d-flex").addClass("d-none");
        parent.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");

        if(parent.find(".js-part-edit:first").hasClass('border-danger')){
            parent.find(".js-part-edit:first").removeClass('border-danger');
        }
    },
    showEdit: function (elem, event) {
        const parent = $(elem).closest(".js-editable-item");
        parent.find(".js-input-div:first").val(parent.find(".js-text-div").html());
        parent.find(".js-part-edit:first").removeClass("d-none").addClass("d-flex");
        parent.find(".js-part-view:first").removeClass("d-flex").addClass("d-none");
    },
    deleteItem: function (elem, event) {
        const parent = $(elem).parents(".js-editable-item");
        parent.remove();
    },
    changeStatus: function (elem, event) {
        if ($(elem).prop('disabled')) return;
        let statusElement;
        let textStatusElement;
        if ($(elem).is('input')) { // click on input status
            statusElement = $(elem);
            textStatusElement = $(elem).closest('.js-part-view').find('.js-item-text-status:first');
        } else { // click on text status
            textStatusElement = $(elem);
            statusElement = $(elem).closest(".js-fields[data-id]").find(".js-item-status input:first");

            statusElement.prop("checked", !$(statusElement).is(':checked')) // update input
        }

        // update text to change status
        textStatusElement.empty()
        const hide_btn = statusElement.is(':checked') ? dropdown_hide : dropdown_unhide;
        textStatusElement.prepend(hide_btn)

        const dataType = $(statusElement).closest(".js-fields[data-id]").attr("data-type");


        if(dataType == TYPE_LEAD_STATUS) {
            // if no other lead status active, not can hide and reset input status
            let canHide = false;
            $(statusElement).closest(".lead-status-list").find('li:not(.item-example):not(.item-loading) .js-item-status input').each(function () {
                if ($(this).is(":checked")) {
                    canHide = true;
                    return false;
                }
            })
            if (!canHide) {
                LeadsSettings.resetStatus(statusElement);
                return;
            }
        }

        const dataId = $(statusElement).closest(".js-fields[data-id]").attr("data-id");
        if(!dataId) return;

        statusElement.addClass("editing-status");
        statusElement.prop('disabled', true);
        textStatusElement.prop('disabled', true);
        const apiProps = {
            id: dataId,
            Status: $(statusElement).is(':checked') ? STATUS_ON : STATUS_OFF
        };

        switch (dataType) {

            case TYPE_LEAD_STATUS:
                apiProps.fun = "UpdateLeadStatus";
                apiProps.PipeId = $(elem).closest(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id");
                postApi('manageLeadsSettings', apiProps, 'leadStatusFunction.afterUpdateStatus', true);
                break;

            case TYPE_LEAD_SOURCE:
                apiProps.fun = "UpdateLeadSource";
                postApi('manageLeadsSettings', apiProps, 'leadSourceFunction.afterUpdateStatus', true);
                break;

            case TYPE_PIPE_LINE:
                apiProps.fun = "UpdatePipeLineCategory";
                postApi('manageLeadsSettings', apiProps, 'pipeLineCategoryFunction.afterUpdateStatus', true);
                break;

            default:
                $.notify({
                    message: lang('error_oops_something_went_wrong')
                }, {
                    type: 'danger',
                    z_index: 2000,
                });
                break;
        }
    },
    resetStatus: function (elem) {
        const oldValue = elem.is(":checked");
        elem.removeClass("editing-status").prop("checked", !oldValue).prop('disabled', false);
        const titleStatus = elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);
        titleStatus.empty()
        const hide_btn = !oldValue ? dropdown_hide : dropdown_unhide;
        titleStatus.prepend(hide_btn);
    },
    goToLeadSources: function (elem, event) {
        $('.lead-sources-list li.item-loading').removeClass("d-none");
        $('.lead-sources-list li:not(.item-loading):not(.item-example)').remove();
        this.goTo(elem, event);
        leadSourceFunction.getLeadSources();
    },
    goToPipeLines: function (elem, event) {
        $('.lead-scale-process-list li.item-loading').removeClass("d-none");
        $('.lead-scale-process-list li:not(.item-loading):not(.item-example)').remove();
        this.goTo(elem, event);
        pipeLineCategoryFunction.getPipeLines();
    },
    goToPipeLinePage: function (elem, event, isAdd) {
        $('.lead-status-list li:not(.item-loading):not(.item-example)').remove();
        $('.static-lead-status-list li:not(.item-loading):not(.item-example)').remove();
        $('.js-name-pipe-line').val("");
        $('#js-button-add-pipe-line-category').addClass("d-none");
        $('.js-name-pipe-line:first').attr("placeholder", isAdd ? lang("enter_name") : '');
        $('.js-title-pipe-line-page:first').text(isAdd ? lang('add_new_pipeline_settings') : lang('edit_pipeline_settings'));

        if(isAdd){ // go to add pipeline
            $('.lead-status-list li.item-loading').addClass("d-none");
            $('#js-button-add-pipe-line-category').removeClass("d-none");
            pipeLineCategoryFunction.showTemplatesLeadStatus();
        } else { // go to edit pipeline
            $('.lead-status-list li.item-loading').removeClass("d-none");
            $('#js-button-add-pipe-line-category').addClass("d-none");
            const dataId = $(elem).parents("[data-id]").attr("data-id");
            $('.js-name-pipe-line:first').attr("disabled", true);
            pipeLineCategoryFunction.getDataOnePipeLine(dataId);
        }

        this.goTo(elem,event);
    },
    saveLeadStatus: function (elem, event) {
        const parent = $(elem).closest(".js-fields[data-id]");
        const titleInput = parent.find(".js-input-div:first");
        if (!titleInput.hasClass('changed')) {
            this.cancelStepEdit(titleInput);
            return;
        }
        const title = titleInput.val();
        const PipeId = $(elem).closest(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id")

        if (title.length  < MIN_LENGTH_TITLE || title.length  >  MAX_LENGTH_TITLE) {
            parent.find(".js-part-edit:first").addClass('border-danger');
            return;
        } else {
            parent.find(".js-part-edit:first").removeClass('border-danger');
        }
        $(elem).closest(".js-part-edit").removeClass("d-flex").addClass("d-none");
        this.removeClassChanged(titleInput);

        if(!PipeId) {
            parent.find(".js-part-view:first").find(".js-item-name:first").html(title);
            parent.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
            return
        }

        parent.find('.js-part-loading:first').removeClass('d-none');

        if (parent.attr("data-id")) { // edit
            const apiProps = {
                fun: "UpdateLeadStatus",
                id: parent.attr("data-id"),
                Title: title,
                PipeId
            };
            postApi('manageLeadsSettings', apiProps, 'leadStatusFunction.afterUpdateName', true);
        } else { // add new
            const apiProps = {
                fun: "AddLeadStatus",
                Title: title,
                PipeId
            };
            postApi('manageLeadsSettings', apiProps, 'leadStatusFunction.showNewLeadStatus', true);
        }

    },
    saveLeadSource: function (elem, event) {
        let parent = $(elem).closest(".js-fields[data-id]");
        const titleInput = parent.find(".js-input-div:first");
        if (!titleInput.hasClass('changed')) {
            this.cancelEdit(titleInput);
            return;
        }
        const title = titleInput.val();
        if (title.length  < MIN_LENGTH_TITLE || title.length  >  MAX_LENGTH_TITLE) {
            parent.find(".js-part-edit:first").addClass('border-danger');
            return;
        } else {
            parent.find(".js-part-edit:first").removeClass('border-danger');
        }

        const loading = $(elem).closest(".lead-sources-list").find(".item-loading:first").clone();
        $(elem).closest(".js-editable-item").append(loading);
        loading.removeClass("d-none animated");
        $(elem).closest(".js-part-edit").removeClass("d-flex").addClass("d-none");
        this.removeClassChanged(titleInput);

        if (parent.attr("data-id")) { // edit
            const apiProps = {
                fun: "UpdateLeadSource",
                id: parent.attr("data-id"),
                Title: title
            };
            postApi('manageLeadsSettings', apiProps, 'leadSourceFunction.afterUpdateName', true);
        } else { // add new
            const apiProps = {
                fun: "AddLeadSource",
                Title: title
            };
            postApi('manageLeadsSettings', apiProps, 'leadSourceFunction.showNewLeadSource', true);
        }
    },
    addPipeLine: function (elem, event) {
        let titleElem = $('.js-name-pipe-line:first');
        let title = titleElem.val();
        if (title.length  < MIN_LENGTH_TITLE || title.length  >  MAX_LENGTH_TITLE) {
            titleElem.removeClass("border-light").addClass('border-danger');
            return
        } else {
            titleElem.removeClass('border-danger').addClass("border-light");
        }

        $(".js-pipe-line-settings-page .js-loader:first").addClass('d-flex');
        $(elem).text(lang("saving_imgpicker"));

        const leadStatusArray = [];
        let currElem, titleLeadStatus, Status;
        const length = $(`.lead-status-list li.js-fields:not(.item-example)`).length
        for(let i=0; i < length; i++)
        {
            currElem = $(`.lead-status-list li.js-fields:not(.item-example):eq(${i})`);
            titleLeadStatus = currElem.find(".js-item-name:first").text()
            Status = currElem.find(".js-item-status input:first").is(':checked') ? STATUS_ON : STATUS_OFF;
            leadStatusArray.push({Title: titleLeadStatus, Status});
        }

        const apiProps = {
            fun: "AddPipeLineCategory",
            leadStatusArray,
            Title: title
        };
        postApi('manageLeadsSettings', apiProps, 'pipeLineCategoryFunction.afterAddPipeLineCategory', true);
    },
    addLeadStatus: function () {
        const elementClone = $('.lead-status-list .item-example').clone();
        $('.lead-status-list').append(this.showAddItem(elementClone));
    },
    addLeadSource: function () {
        const elem = $('.lead-sources-list .item-example').clone();
        $('.lead-sources-list').append(this.showAddItem(elem));
    },
    goToFacebook: function(elem){
        LeadsSettings.goTo(elem);
        FBFunction.showLoading();
        const apiProps = {
            fun: "getDataForFacebook",
        }
        postApi('manageLeadsSettings', apiProps, 'FBFunction.afterGetDataFromBoostapp', true);
    },
};
const pipeLineCategoryFunction = {
    getPipeLines: function () {
        const apiProps = {
            fun: "GetPipeLineCategories"
        };
        postApi('manageLeadsSettings', apiProps, 'pipeLineCategoryFunction.showPipeLines');
    },
    showPipeLines: function (data) {
        if(LeadsSettings.errorChecking(data)) {
            const result = data.response;
            $('.lead-scale-process-list .item-loading').addClass("d-none");
            $('.lead-scale-process-list li:not(.item-loading):not(.item-example)').remove();
            if (result.length) {
                $.each(result, function (key) {
                    const newElem = $('.lead-scale-process-list .item-example').clone();
                    $('.lead-scale-process-list').append(LeadsSettings.renderItem(newElem, result[key].id, result[key].Title, result[key].Status, null, result[key].Act))
                })
            }
        } else {
            const elem = $("[data-page-id='js-tabs-pipe-categories'] [data-next='js-tabs-home']:first");
            LeadsSettings.goTo(elem); // go home page
        }
    },
    getDataOnePipeLine: function (PipeId) {
        const apiProps = {
            fun: "GetDataPipeLine",
            PipeId
        };
        postApi('manageLeadsSettings', apiProps, 'pipeLineCategoryFunction.showPagePipeLine');
    },
    showPagePipeLine: function (data) {
        if (LeadsSettings.errorChecking(data)) {
            $('.lead-status-list .item-loading').addClass("d-none");
            $('.js-name-pipe-line:first').val(data.response.PipeLineCategoryTitle);
            $('.js-name-pipe-line:first').attr("disabled", false);
            $("[data-pipe-id='']").attr("data-pipe-id", data.response.PipeLineCategoryId);
            leadStatusFunction.showLeadStatus(data);
        } else {
            const elem = $('.js-go-to-pipe-line-categories:first');
            LeadsSettings.goTo(elem);
        }
    },
    showTemplatesLeadStatus: function () {
        const titleTemplates = [
            lang('a_new_lead'),
            lang('hot_lead_ajax'),
            lang('first_contact_ajax'),
            lang('trial_lesson'),
            lang('lead_close_ajax'),
        ]
        titleTemplates.forEach((title, index) => {
            const newElem = $('.lead-status-list .item-example').clone();
            $('.lead-status-list').append(LeadsSettings.renderItem(newElem, "", title, STATUS_ON, index + 1));
        })
        $('.static-lead-status-list').append(leadStatusFunction.renderLeadStatusStaticRow("", lang('success')))
        $('.static-lead-status-list').append(leadStatusFunction.renderLeadStatusStaticRow("", lang('failure')))
        $('.static-lead-status-list').append(leadStatusFunction.renderLeadStatusStaticRow("", lang('not_relevant')))
    },
    afterUpdateName: function (data) {
        LeadsSettings.removeClassChanged('.js-name-pipe-line.changed:first');
        if (LeadsSettings.errorChecking(data)) {
            $(".lead-scale-process-list  .js-part-view[data-id='" + data.response.id + "'] .js-text-div:first").text(data.response.Title);
            // update form add lead
            $(`#AddNewLead #PipeLineSelect`).append(`<option value="${data.response.id}">${data.response.Title}</option>`);
            // update manageLeads page
            $(`.js-manage-leads-body #ChoosePipeline option[value=${data.response.id}]`).text(`${lang('uppercase_pipeline')} :: ${data.response.Title}`);
        }
    },
    afterUpdateStatus: function (data) {
        const elem = $(`.lead-scale-process-list  .js-fields[data-type=${TYPE_PIPE_LINE}] input.editing-status:first`);
        if (LeadsSettings.errorChecking(data)) {
            const {id, Status, Title } = data.response;
            elem.removeClass("editing-status").prop("checked", Status == STATUS_ON).prop('disabled', false);
            elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);

            // update form add lead
            if (Status == STATUS_ON) {
                $(`#AddNewLead #PipeLineSelect`).append(`<option value="${id}">${Title}</option>`);
            } else {
                $(`#AddNewLead #PipeLineSelect option[value=${id}]`).remove();
            }

            // update manageLeads page
            if (Status == STATUS_ON){
                $(`.js-manage-leads-body #ChoosePipeline`).append(`<option value=${id}>${lang('uppercase_pipeline')} :: ${Title}</option>`);
            } else {
                if ($(`.js-manage-leads-body #ChoosePipeline option[value=${id}]`).is(':selected')) {
                    let newPipeId = $(`.js-manage-leads-body #ChoosePipeline option[value=${id}]`).next().val();
                    if (!newPipeId) newPipeId = $(`.js-manage-leads-body #ChoosePipeline option:eq(0)`).val();
                    $(`.js-manage-leads-body #ChoosePipeline`).val(newPipeId);
                    $(`.js-manage-leads-body #ChoosePipeline`).trigger('change');
                }
                $(`.js-manage-leads-body #ChoosePipeline option[value=${id}]`).remove();
            }


        } else
            LeadsSettings.resetStatus(elem);
    },
    afterAddPipeLineCategory: function (data) {
        const elem = $('#js-button-add-pipe-line-category');
        if (LeadsSettings.errorChecking(data)) {
            const newElem = $('.lead-scale-process-list .item-example').clone();
            $('.lead-scale-process-list').append(LeadsSettings.renderItem(newElem,data.response.id, data.response.Title, data.response.Status, null, data.response.Act))
            if ($('.js-name-pipe-line:first').hasClass('changed'))
                LeadsSettings.removeClassChanged('.js-name-pipe-line.changed:first');
            LeadsSettings.goTo(elem);
            // update form add lead
            $(`#AddNewLead #PipeLineSelect`).append(`<option value="${data.response.id}">${data.response.Title}</option>`);
            // update manageLeads page
            if (data.response.Status == STATUS_ON)
                $(`.js-manage-leads-body #ChoosePipeline`).append(`<option value=${data.response.id}>${lang('uppercase_pipeline')} :: ${data.response.Title}</option>`);
        }
        elem.find("a:first").text(lang("save"));
        $(".js-pipe-line-settings-page .js-loader:first").removeClass('d-flex').addClass('d-none');
    }
}
const leadStatusFunction = {
    showLeadStatus: function (data){
        const result = data.response.LeadStatus;
        $('.static-lead-status-list li:not(.item-loading):not(.item-example)').remove();
        $('.lead-status-list li:not(.item-loading):not(.item-example)').remove();
        let index = 1;
        if (result.length) {
            $.each(result, function (key) {
                if (result[key].Act == 0) {
                    const newElem = $('.lead-status-list .item-example').clone();
                    $('.lead-status-list').append(LeadsSettings.renderItem(newElem, result[key].id, result[key].Title, result[key].Status, index))
                    index++;
                } else
                    $('.static-lead-status-list').append(leadStatusFunction.renderLeadStatusStaticRow(result[key].id, result[key].Title))
            })
        }
    },
    renderLeadStatusStaticRow: function (id, name) {
        const elem = $('.static-lead-status-list .item-example').clone();
        elem.removeClass("d-none item-example").addClass("d-flex").attr("data-id", id);
        elem.find(".js-item-name:first").text(name);
        elem.find(".js-item-id:first").text(id);
        return elem;
    },
    showNewLeadStatus: function (data) {
        const elem = $(".lead-status-list  .js-fields[data-id='']:not(.item-example):first");
        if (LeadsSettings.errorChecking(data)) {
            LeadsSettings.renderItem(elem, data.response.id, data.response.Title, STATUS_ON, elem.index() - 3);
            elem.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
            elem.find('.js-part-loading:first').addClass('d-none');
            const { id, PipeId, Title } = data.response;

            // update form add lead
            $(`#AddNewLead #StatusSelect`).append(`<option value="${id}" data-ajax="${PipeId}">${Title}</option>`)

            // update manageLeads page
            if ($(".js-manage-leads-body #ChoosePipeline option:selected").val() == PipeId)
                LeadsData.GetDataManageLeads(PipeId);
        } else {
            elem.remove();
        }
    },
    afterUpdateName: function (data) {
        if (LeadsSettings.errorChecking(data)) {
            $(".lead-status-list  .js-fields[data-id='" + data.response.id + "'] .js-editable-item .js-text-div:first").text(data.response.Title);
            // update form add lead
            $(`#AddNewLead #StatusSelect option[value=${data.response.id}]`).text(data.response.Title);
            // update manageLeads page
            if($(".js-manage-leads-body #ChoosePipeline option:selected").val() == data.response.PipeId) {
                $(`.js-manage-leads-body ul[id=${data.response.id}] [aria-controls=DashPipe${data.response.id}] strong:first`).text(data.response.Title);
            }
        }
        const loading = $(".lead-status-list .js-fields .js-editable-item .js-part-loading:not(.d-none):first").addClass("d-none");
        loading.closest(".js-editable-item").find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        loading.addClass("d-none");
    },
    afterUpdateStatus: function (data) {
        const elem = $(`.lead-status-list  .js-fields[data-type=${TYPE_LEAD_STATUS}] input.editing-status:first`);
        if (LeadsSettings.errorChecking(data)) {
            const { id, PipeId, Status, Title } = data.response;
            elem.removeClass("editing-status").prop("checked", Status == STATUS_ON).prop('disabled', false);
            elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);

            // update form add lead
            if (Status == STATUS_ON)
                $(`#AddNewLead #StatusSelect`).append(`<option value="${id}" data-ajax="${PipeId}">${Title}</option>`)
            else
                $(`#AddNewLead #StatusSelect option[value=${id}]`).remove();
            // update manageLeads page
            if ($(".js-manage-leads-body #ChoosePipeline option:selected").val() == PipeId)
                if (Status == STATUS_ON) {
                    LeadsData.GetDataManageLeads(PipeId);
                } else {
                    $(`.js-manage-leads-body ul[id=${id}]`).parent('div').remove();
                }
            } else
                LeadsSettings.resetStatus(elem);
    },
    afterUpdateSort: function (data) {
        const PipeId = $(".js-pipe-line-settings-page[data-pipe-id]").attr("data-pipe-id");
        if(LeadsSettings.errorChecking(data)){
            if($(".js-manage-leads-body #ChoosePipeline option:selected").val() == PipeId)
                LeadsData.GetDataManageLeads(PipeId);
        } else {
            if(!PipeId) return;

            const apiProps = {
                fun: "getLeadStatuses",
                PipeId
            }
            postApi('manageLeadsSettings', apiProps, 'leadStatusFunction.showLeadStatus', true);
        }
    }
}
const leadSourceFunction = {
    getLeadSources: function () {
        const apiProps = {
            fun: "GetLeadSources"
        };
        postApi('manageLeadsSettings', apiProps, 'leadSourceFunction.showLeadSourcePage', true);
    },
    showLeadSourcePage: function (data) {
        if (LeadsSettings.errorChecking(data)) {
            const result = data.LeadSources;
            $('.lead-sources-list .item-loading').addClass("d-none");
            $('.lead-sources-list li:not(.item-loading):not(.item-example)').remove();
            if (result.length) {
                $.each(result, function (key) {
                    const newElem = $('.lead-sources-list .item-example').clone();
                    $('.lead-sources-list').append(LeadsSettings.renderItem(newElem, result[key].id, result[key].Title, result[key].Status));
                })
            } else
                $('.lead-sources-list').append(`<li class="mb-10 animated fadeInUp"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('not_found')}</div></li>`)
        } else {
            const elem = $("[data-page-id='js-tabs-lead-source'] [data-next='js-tabs-home']:first");
            LeadsSettings.goTo(elem); // go home page
        }
    },
    showNewLeadSource: function (data) {
        const elem = $(".lead-sources-list  .js-fields[data-id='']:not(.item-example):first");
        if (LeadsSettings.errorChecking(data)) {
            LeadsSettings.renderItem(elem, data.response.id, data.response.Title, STATUS_ON);
            elem.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        } else {
            elem.remove();
       }
        $(".lead-sources-list .item-loading:last").addClass("d-none");

    },
    afterUpdateName: function (data) {
        if (LeadsSettings.errorChecking(data)) {
            const elem = $(".lead-sources-list  .js-fields[data-id='" + data.response.id + "'] .js-editable-item:first");
            elem.find(".js-text-div:first").text(data.response.Title);
        }
        const loading = $(".lead-sources-list .item-loading:not(.d-none):first");
        loading.closest(".js-editable-item").find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        loading.addClass("d-none");
    },
    afterUpdateStatus: function (data) {
        const elem = $(`.lead-sources-list .js-fields[data-type=${TYPE_LEAD_SOURCE}] input.editing-status:first`);
        if (LeadsSettings.errorChecking(data)) {
            const {id, Status, Title} = data.response;
            elem.removeClass("editing-status").prop("checked", Status == STATUS_ON).prop('disabled', false);
            elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);
            // update form add lead
            if (Status == STATUS_ON)
                $(`#AddNewLead #SourceSelect`).append(`<option value="${id}">${Title}</option>`)
            else
                $(`#AddNewLead #SourceSelect option[value=${id}]`).remove();
        } else
            LeadsSettings.resetStatus(elem);
    }
}
function get_boostapplogin_domain(){
    var queryString = 'devlogin.boostapp.co.il';
    var url = window.location.href;
    if(url.indexOf(queryString) != -1){
        return 'https://devlogin.boostapp.co.il';
    }
    return 'https://login.boostapp.co.il'
}


