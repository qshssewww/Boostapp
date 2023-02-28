/***  constant: status mode **/
const STATUS_ON = 0, STATUS_OFF = 1;
/***  constant: length title **/
const MIN_LENGTH_TITLE = 1, MAX_LENGTH_TITLE = 70;

$(document).ready(function () {
    TasksSettings.initSelect2();
});

const TasksSettings = {
    initSelect2: function () {
        $(".js-select2:not(.select2-hidden-accessible)").select2({
            theme: 'bsapp-dropdown'
        });
    },
    openSettings: function () {
        $(".js-drop-menu").addClass("bsapp-js-show");
        $(".js-dropdown-inner").removeClass("d-none").addClass("d-block");
    },
    closeSettings: function (elem) {
        var $elem = $(elem);
        var $parent = $elem.parents("[data-page-id]");
        $parent.removeClass("d-flex").addClass("d-none");
        $("[data-page-id='js-tabs-home']").removeClass("d-none").addClass("d-flex");
        $(".js-dropdown-inner").removeClass("d-block").addClass("d-none");
        $(".js-drop-menu").removeClass("bsapp-js-show");
    },
    goTo: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.parents("[data-page-id]");
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
    addNewItem: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.parents("[data-page-id]");
        var js_copy = $('.' + $elem.attr("data-copy-item")).html();
        $parent.find($elem.attr("data-copy-container")).append(js_copy);
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
    showEdit: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.parents(".js-editable-item");
        $parent.find(".js-input-div").val($parent.find(".js-text-div").html());
        $parent.find(".js-part-edit").removeClass("d-none").addClass("d-flex");
        $parent.find(".js-part-view").removeClass("d-flex").addClass("d-none");
    },
    deleteItem: function (elem, event) {
        var $elem = $(elem);
        var $parent = $elem.parents(".js-editable-item");
        $parent.remove();
    },
    addClassChanged: function (elem) {
        $(elem).addClass('changed');
    },
    removeClassChanged: function (selector) {
        $(selector).removeClass('changed');
    },
    goToTaskTypes: function (elem) {
        $('.js-task-types-list li.item-loading').removeClass("d-none");
        $('.js-task-types-list li:not(.item-loading):not(.item-example)').remove();
        this.goTo(elem);
        TaskTypesFunction.getTaskTypes();
    },
    saveTaskObj: function (elem, listClass, funcType, callbackClass) {
        let parent = $(elem).closest(".js-fields[data-id]");
        const titleInput = parent.find(".js-input-div:first");
        if (!titleInput.hasClass('changed')) {
            this.cancelEdit(titleInput);
            return;
        }
        const title = titleInput.val();
        if (title.length < MIN_LENGTH_TITLE || title.length > MAX_LENGTH_TITLE) {
            parent.find(".js-part-edit:first").addClass('border-danger');
            return;
        } else {
            parent.find(".js-part-edit:first").removeClass('border-danger');
        }

        const loading = $(elem).closest(listClass).find(".item-loading:first").clone();
        $(elem).closest(".js-editable-item").append(loading);
        loading.removeClass("d-none animated");
        $(elem).closest(".js-part-edit").removeClass("d-flex").addClass("d-none");
        this.removeClassChanged(titleInput);

        if (parent.attr("data-id")) { // edit
            const apiProps = {
                fun: "UpdateTask" + funcType,
                id: parent.attr("data-id"),
                Name: title,
            };
            postApi('tasksSettings', apiProps, callbackClass + '.afterTaskParam', true);
        } else { // add new
            const apiProps = {
                fun: "AddTask" + funcType,
                Name: title,
            };
            postApi('tasksSettings', apiProps, callbackClass + '.showNewTaskParam', true);
        }
    },
    saveTaskType: function (elem) {
        TasksSettings.saveTaskObj(elem, '.js-task-types-list', 'Type', 'TaskTypesFunction');
    },
    saveTaskStatus: function (elem) {
        TasksSettings.saveTaskObj(elem, '.js-task-statuses-list', 'Status', 'TaskStatusFunction');
    },
    changeObjStatus: function (elem, funcType, callbackClass) {
        if ($(elem).prop('disabled')) return
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

        const dataId = $(statusElement).closest(".js-fields[data-id]").attr("data-id");
        if (!dataId) return;

        statusElement.addClass("editing-status");
        statusElement.prop('disabled', true);
        textStatusElement.prop('disabled', true);

        const apiProps = {
            fun: "UpdateTask" + funcType,
            id: dataId,
            Status: statusElement.is(':checked') ? STATUS_ON : STATUS_OFF
        };
        postApi('tasksSettings', apiProps, callbackClass + '.afterUpdateParam', true);
    },
    changeStatus: function (elem, event) {
        TasksSettings.changeObjStatus(elem, 'Type', 'TaskTypesFunction');
    },
    changeTaskStatus: function (elem, event) {
        TasksSettings.changeObjStatus(elem, 'Status', 'TaskStatusFunction');
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
    addTaskObj: function (listClass) {
        if (!$(listClass + " .item-loading:first").hasClass('d-none')) return; // if show loading, no add item
        const elem = $(listClass + ' .item-example').clone();
        elem.removeClass("d-none item-example");
        elem.find(".js-part-view").removeClass("d-flex").addClass("d-none");
        elem.find(".js-part-edit").removeClass("d-none").addClass("d-flex");
        $(listClass).append(elem);
    },
    addTaskType: function () {
        TasksSettings.addTaskObj('.js-task-types-list');
    },
    addTaskStatus: function () {
        TasksSettings.addTaskObj('.js-task-statuses-list');
    },
    getTaskObj: function (objType, callback) {
        const apiProps = {
            fun: "GetTask" + objType
        };
        postApi('tasksSettings', apiProps, callback);
    },
    showTaskObjects: function (data, listClass) {
        if (TasksSettings.errorChecking(data)) {
            const result = data.response;
            $(listClass + ' .item-loading').addClass("d-none");
            $(listClass + ' li:not(.item-loading):not(.item-example):not(.item-default)').remove();
            if (result.length) {
                $.each(result, function (key) {
                    const newElem = $(listClass + ' .item-example').clone();
                    $(listClass).append(TasksSettings.renderItem(newElem, result[key].id,
                        (listClass === '.js-task-types-list' ? result[key].Type : result[key].Name), result[key].Status));
                })
            }
        } else {
            TasksSettings.goTo($('.js-from-task-types-go-home:first'));
        }
    },
    renderItem: function (elem, id, name, status) {
        const switchId = Math.random();
        const hide_btn = status == STATUS_ON ? dropdown_hide : dropdown_unhide;
        elem.removeClass("d-none item-example").attr("data-id", id);
        elem.find(".js-item-name:first").text(name);
        elem.find(".js-item-status input:first").attr("id", `js-switch-id-${name}-${switchId}`).prop("checked", status == STATUS_ON);
        elem.find(".js-item-status label:first").attr("for", `js-switch-id-${name}-${switchId}`);
        elem.find('.js-item-text-status').prepend(hide_btn);
        return elem;
    },
    showNewTaskObj: function (data, listClass) {
        const elem = $(listClass + "  .js-fields[data-id='']:not(.item-example):first");
        if (TasksSettings.errorChecking(data)) {
            TasksSettings.renderItem(elem, data.response.id,
                (listClass === '.js-task-types-list' ? data.response.Type : data.response.Name), STATUS_ON);
            elem.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        } else {
            elem.remove();
        }
        $(listClass + " .item-loading:last").addClass("d-none");
    },
    afterTaskObj: function (data, listClass) {
        if (TasksSettings.errorChecking(data)) {
            const elem = $(listClass + "  .js-fields[data-id='" + data.response.id + "'] .js-editable-item:first");
            elem.find(".js-text-div:first").text((listClass === '.js-task-types-list' ? data.response.Type : data.response.Name));
        }
        const loading = $(listClass + " .item-loading:not(.d-none):first");
        loading.closest(".js-editable-item").find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        loading.addClass("d-none");
    },
    afterUpdateObj: function (data, listClass) {
        const elem = $(listClass + ` .js-fields input.editing-status:first`);
        if (TasksSettings.errorChecking(data)) {
            elem.removeClass("editing-status").prop("checked", data.response.Status == STATUS_ON).prop('disabled', false);
            elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);
        } else {
            const oldValue = elem.is(":checked");
            elem.removeClass("editing-status").prop("checked", !oldValue).prop('disabled', false);
            const titleStatus = elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);
            titleStatus.empty()
            const hide_btn = !oldValue ? dropdown_hide : dropdown_unhide;
            titleStatus.prepend(hide_btn);
        }
    },
};
const TaskTypesFunction = {
    getTaskTypes: function () {
        TasksSettings.getTaskObj('Types', 'TaskTypesFunction.showTaskTypes');
    },
    showTaskTypes: function (data) {
        TasksSettings.showTaskObjects(data, '.js-task-types-list');
    },
    showNewTaskParam: function (data) {
        TasksSettings.showNewTaskObj(data, '.js-task-types-list');
    },
    afterTaskParam: function (data) {
        TasksSettings.afterTaskObj(data, '.js-task-types-list');
    },
    afterUpdateParam: function (data) {
        TasksSettings.afterUpdateObj(data, '.js-task-types-list');
    },
}

const TaskStatusFunction = {
    getTaskStatuses: function () {
        TasksSettings.getTaskObj('Statuses', 'TaskStatusFunction.showTaskStatuses');
    },
    showTaskStatuses: function (data) {
        TasksSettings.showTaskObjects(data, '.js-task-statuses-list');
    },
    showNewTaskParam: function (data) {
        TasksSettings.showNewTaskObj(data, '.js-task-statuses-list');
    },
    afterTaskParam: function (data) {
        TasksSettings.afterTaskObj(data, '.js-task-statuses-list');
    },
    afterUpdateParam: function (data) {
        TasksSettings.afterUpdateObj(data, '.js-task-statuses-list');
    },
}