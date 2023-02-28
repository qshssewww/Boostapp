/***  constant: status mode **/
const STATUS_ON = 0, STATUS_OFF = 1;
/***  constant: length title **/
const MIN_LENGTH_TITLE = 1, MAX_LENGTH_TITLE = 70;

$(document).ready(function () {
    ClientsSettings.initSelect2();
    ClientsSettings.initSortable();
});

const ClientsSettings = {
    initSelect2: function () {
        $(".js-select2:not(.select2-hidden-accessible)").select2({
            theme: 'bsapp-dropdown'
        });
    },
    initSortable: function () {
        Sortable.create(js_sortable_container_1, {
            animation: 100,
            group: 'list-1',
            draggable: '.js-sortable-item',
            handle: '.js-grip-handle',
            sort: true,
            filter: '.sortable-disabled',
            chosenClass: 'active'
        });
        Sortable.create(js_sortable_container, {
            animation: 100,
            group: 'list-1',
            draggable: '.js-sortable-item',
            handle: '.js-grip-handle',
            sort: true,
            filter: '.sortable-disabled',
            chosenClass: 'active'
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
        if ($(elem).closest(".js-fields[data-id]").attr("data-id")) {
            parent.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        } else {
            $(elem).closest(".js-fields[data-id='']").remove();
        }
        if (parent.find(".js-part-edit:first").hasClass('border-danger')) {
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
    goToTags: function (elem) {
        $('.js-tags-clients-list li.item-loading').removeClass("d-none");
        $('.js-tags-clients-list li:not(.item-loading):not(.item-example)').remove();
        this.goTo(elem);
        TagsFunction.getClientsTags();
    },
    addTag: function () {
        if(!$(".js-tags-clients-list .item-loading:first").hasClass('d-none')) return; // if show loading, no add item
        const newElem = $('.js-tags-clients-list .item-example').clone();
        newElem.find(".js-part-edit:first").removeClass("d-none").addClass("d-flex");
        newElem.find(".js-part-view:first").removeClass("d-flex").addClass("d-none");
        newElem.removeClass("d-none item-example");

        $('.js-tags-clients-list').append(newElem);
    },
    addClassChanged: function (elem) {
        $(elem).addClass('changed');
    },
    removeClassChanged: function (selector) {
        $(selector).removeClass('changed');
    },
    saveTag: function (elem) {
        const parent = $(elem).closest(".js-fields[data-id]");
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

        const loading = $(elem).closest(".js-tags-clients-list").find(".item-loading:first").clone();
        $(elem).closest(".js-editable-item").append(loading);
        loading.removeClass("d-none animated");
        $(elem).closest(".js-part-edit").removeClass("d-flex").addClass("d-none");
        this.removeClassChanged(titleInput);

        if (parent.attr("data-id")) { // edit
            const apiProps = {
                fun: "UpdateClientsTag",
                id: parent.attr("data-id"),
                Title: title
            };
            postApi('clientsSettings', apiProps, 'TagsFunction.afterUpdateTag', true);
        } else { // add new
            const apiProps = {
                fun: "AddClientsTag",
                Title: title
            };
            postApi('clientsSettings', apiProps, 'TagsFunction.showNewTag', true);
        }

    },
    goToReasonsLeave: function (elem) {
        $('.js-reasons-leave-list li.item-loading').removeClass("d-none");
        $('.js-reasons-leave-list li:not(.item-loading):not(.item-example)').remove();
        this.goTo(elem);
        ReasonsLeaveFunction.getReasonsLeave();
    },
    addReasonsLeave: function () {
        if(!$(".js-reasons-leave-list .item-loading:first").hasClass('d-none')) return; // if show loading, no add item
        const newElem = $('.js-reasons-leave-list .item-example').clone();
        newElem.find(".js-part-edit:first").removeClass("d-none").addClass("d-flex");
        newElem.find(".js-part-view:first").removeClass("d-flex").addClass("d-none");
        newElem.removeClass("d-none item-example");
        $('.js-reasons-leave-list').append(newElem);
    },
    saveReasonLeave: function (elem) {
        const parent = $(elem).closest(".js-fields[data-id]");
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

        const loading = $(elem).closest(".js-reasons-leave-list").find(".item-loading:first").clone();
        $(elem).closest(".js-editable-item").append(loading);
        loading.removeClass("d-none animated");
        $(elem).closest(".js-part-edit").removeClass("d-flex").addClass("d-none");
        this.removeClassChanged(titleInput);

        if (parent.attr("data-id")) { // edit
            const apiProps = {
                fun: "UpdateReasonLeave",
                id: parent.attr("data-id"),
                Title: title
            };
            postApi('clientsSettings', apiProps, 'ReasonsLeaveFunction.afterUpdateReasonLeave', true);
        } else { // add new
            const apiProps = {
                fun: "AddReasonLeave",
                Title: title
            };
            postApi('clientsSettings', apiProps, 'ReasonsLeaveFunction.showNewReasonLeave', true);
        }

    },
    changeStatus: function (elem) {
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

        const dataId = $(statusElement).closest(".js-fields[data-id]").attr("data-id");
        if (!dataId) return;

        statusElement.addClass("editing-status");
        statusElement.prop('disabled', true);
        textStatusElement.prop('disabled', true);

        const apiProps = {
            fun: "UpdateReasonLeave",
            id: dataId,
            Status: $(statusElement).is(':checked') ? STATUS_ON : STATUS_OFF
        };
        postApi('clientsSettings', apiProps, 'ReasonsLeaveFunction.afterUpdateStatus', true);

    },
};

const TagsFunction = {
    getClientsTags: function () {
        const apiProps = {
            fun: "GetClientsTags"
        };
        postApi('clientsSettings', apiProps, 'TagsFunction.showClientsTags', true);
    },
    showClientsTags: function (data) {
        if (ClientsSettings.errorChecking(data)) {
            const result = data.response;
            $('.js-tags-clients-list .item-loading').addClass("d-none");
            $('.js-tags-clients-list li:not(.item-loading):not(.item-example)').remove();
            if (result.length) {
                $.each(result, function (key) {
                    const newElem = $('.js-tags-clients-list .item-example').clone();
                    $('.js-tags-clients-list').append(TagsFunction.renderTag(newElem, result[key].id, result[key].Level, result[key].count))
                })
            }
        } else {
            ClientsSettings.goTo($('.js-from-tag-go-home:first'));
        }
    },
    renderTag: function (elem, id, name, count) {
        elem.removeClass("d-none item-example").attr("data-id", id);
        elem.find(".js-item-name:first").text(name);
        elem.find(".js-item-count:first").text(count);
        return elem;
    },
    showNewTag: function (data) {
        const elem = $(".js-tags-clients-list  .js-fields[data-id='']:not(.item-example):first");
        if (ClientsSettings.errorChecking(data)) {
            TagsFunction.renderTag(elem, data.response.id, data.response.Level, 0);
            elem.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        } else {
            elem.remove();
        }
        $(".js-tags-clients-list .item-loading:last").addClass("d-none");
    },
    afterUpdateTag: function (data) {
        if (ClientsSettings.errorChecking(data)) {
            $(".js-tags-clients-list  .js-fields[data-id='" + data.response.id + "'] .js-editable-item .js-text-div:first").text(data.response.Level);
        }
        const loading = $(".js-tags-clients-list .item-loading:not(.d-none):first");
        loading.closest(".js-editable-item").find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        loading.addClass("d-none");
    },
}
const ReasonsLeaveFunction = {
    getReasonsLeave: function () {
        const apiProps = {
            fun: "GetReasonsLeave"
        };
        postApi('clientsSettings', apiProps, 'ReasonsLeaveFunction.showReasonsLeave', true);
    },
    showReasonsLeave: function (data) {
        if (ClientsSettings.errorChecking(data)) {
            const result = data.response;
            $('.js-reasons-leave-list .item-loading').addClass("d-none");
            $('.js-reasons-leave-list li:not(.item-loading):not(.item-example)').remove();
            if (result.length) {
                $.each(result, function (key) {
                    const newElem = $('.js-reasons-leave-list .item-example').clone();
                    $('.js-reasons-leave-list').append(ReasonsLeaveFunction.renderReason(newElem, result[key].id, result[key].Title, result[key].Status))
                })
            }
        } else {
            ClientsSettings.goTo($('.js-from-reasons-leave-go-home:first'));
        }
    },
    renderReason: function (elem, id, name, status) {
        const switchId = Math.random();
        const hide_btn = status == STATUS_ON ? dropdown_hide : dropdown_unhide;
        elem.removeClass("d-none item-example").attr("data-id", id);
        elem.find(".js-item-name:first").text(name);
        elem.find(".js-item-id:first").text(id);
        elem.find(".js-item-status input:first").attr("id", `js-switch-id-${name}-${switchId}`).prop("checked", status == STATUS_ON);
        elem.find(".js-item-status label:first").attr("for", `js-switch-id-${name}-${switchId}`);
        elem.find('.js-item-text-status').prepend(hide_btn);
        return elem;
    },
    showNewReasonLeave: function (data) {
        const elem = $(".js-reasons-leave-list  .js-fields[data-id='']:not(.item-example):first");
        if (ClientsSettings.errorChecking(data)) {
            ReasonsLeaveFunction.renderReason(elem, data.response.id, data.response.Title, STATUS_ON);
            elem.find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        } else {
            elem.remove();
        }
        $(".js-reasons-leave-list .item-loading:last").addClass("d-none");
    },
    afterUpdateReasonLeave: function (data) {
        if (ClientsSettings.errorChecking(data)) {
            $(".js-reasons-leave-list  .js-fields[data-id='" + data.response.id + "'] .js-editable-item .js-text-div:first").text(data.response.Title);
        }
        const loading = $(".js-reasons-leave-list .item-loading:not(.d-none):first");
        loading.closest(".js-editable-item").find(".js-part-view:first").removeClass("d-none").addClass("d-flex");
        loading.addClass("d-none");
    },
    afterUpdateStatus: function (data) {
        const elem = $(`.js-reasons-leave-list .js-fields input.editing-status:first`);
        if (ClientsSettings.errorChecking(data)) {
            elem.removeClass("editing-status").prop("checked", data.response.Status == STATUS_ON).prop('disabled', false);
            elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false);
        } else {
            const oldValue = elem.is(":checked");
            elem.removeClass("editing-status").prop("checked", !oldValue).prop('disabled', false);
            const titleStatus = elem.closest('.js-part-view').find('.js-item-text-status:first').prop('disabled', false)
            titleStatus.empty()
            const hide_btn = !oldValue ? dropdown_hide : dropdown_unhide;
            titleStatus.prepend(hide_btn);
        }
    }
}