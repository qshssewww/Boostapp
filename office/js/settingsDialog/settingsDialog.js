// General :: Select2
$('.bsapp-settings-dialog .select2').select2({
    minimumResultsForSearch: -1,
    dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu')});
$('.bsapp-settings-dialog .select2-multi').select2({
    tags: true,
    minimumResultsForSearch: -1,
    dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu')});
$('.bsapp-settings-dialog .select2-input').select2({
    tags: true,
    dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
    theme: 'select2-container--hide-arrow'});


// General :: API handler
var apiProps = {},
        apiRoute;
function postApi(route, props, callback, handleError=false) {
    if (route == "storeSettings")
        apiRoute = "/office/ajax/storeSettings.php";
    else if (route == "calendarSettings")
        apiRoute = "/office/ajax/CalendarSettings.php";
    else if (route == "CalendarView")
        apiRoute = "/office/ajax/CalendarView.php";
    else if(route == "manageLeadsSettings")
        apiRoute = "/office/ajax/ManageLeadsSettings.php";
    else if(route == "ManageLeadsView")
        apiRoute = "/office/ajax/ManageLeadsView.php";
    else if (route == "ClubMemberships")
        apiRoute = "/office/newShop/ajax/club-memberships.php";
    else if(route == "clientsSettings")
        apiRoute = "/office/ajax/ClientsSettings.php";
    else if(route == "tasksSettings")
        apiRoute = "/office/ajax/TasksSettings.php";
    else if(route == "branchSettings")
        apiRoute = "/office/ajax/BranchSettings.php";
    else
        return;
    $.ajax({
        type: 'POST',
        url: apiRoute,
        data: props,
        dataType: 'JSON',
        success: function (result) {
            if (callback != undefined) {
                eval(callback + '(' + JSON.stringify(result) + ')');
            }
            return result
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
            let result = {
                'Status': 0
            }
            if (callback != undefined && handleError) {
                eval(callback + '(' + JSON.stringify(result) + ')');
            } else {
                $.notify({
                    message: lang('error_oops_something_went_wrong')
                }, {
                    type: 'danger',
                    z_index: 2000,
                });
            }
            return false
        }})
}

// General :: Custom Open for Settings Module
$('.bsapp-settings-dialog .btn.dropdown-toggle').click(function () {
    var menu = $('.bsapp-settings-dialog > .dropdown-menu');
    if (!menu.hasClass('show')) {
        menu.removeClass('fadeOut')
                .addClass('show fadeIn')
    } else {
        menu.removeClass('fadeIn')
                .addClass('fadeOut');
        setTimeout(function () {
            menu.removeClass('show');
        }, 300)
    }
    $('.bsapp-settings-panel').removeClass('d-flex')
            .addClass('d-none');
    $('.main-settings-panel').addClass('d-flex')
            .removeClass('d-none')
});

// General :: Change Settings Panels
$("body").on("click", ".bsapp-settings-panel a", function (e) {
    let target = $(this).attr("data-target");
    if (target != undefined) {
        switchSettingsPanel($(this), target);
        e.preventDefault();
    }
});
function switchSettingsPanel($this, target) {
    var current = $this.parents('.bsapp-settings-panel'),
            curr_depth = current.data('depth'),
            target_depth = $('.' + target).data('depth');
    if (target_depth < curr_depth) { // backwards
        $('.' + target).removeClass('d-none slideInStart')
                .addClass('d-flex');
        current.removeClass('slideInStart')
                .addClass('slideOutStart');
        setTimeout(function () {
            current.removeClass('d-flex slideOutStart')
                    .addClass('d-none slideInStart');
        }, 300)
    } else { // forward
        $('.' + target).addClass('slideInStart d-flex')
                .removeClass('d-none');
        $('.' + target).find('.border-danger').removeClass('border-danger');
        setTimeout(function () {
            current.removeClass('d-flex slideInStart')
                    .addClass('d-none');
        }, 300);
    }
}

// General :: Toggle "Manage" and Contents
$('.bsapp-settings-panel').on('click', '.toggle-manage', function () {
    $(this).closest('.form-toggle')
            .children('.toggle-content')
            .toggleClass('d-none d-flex')
});

// General :: Validation
function validateSettingsFields(fields) {
    var validated = true;
    fields.each(function () {
        var field = ($(this).parent('.input-group').length) ? $(this).parent() : $(this);
        if ($(this).val() != '' && $(this).val()) {
            field.removeClass('border-danger').addClass('border-0');
            $(this).closest('.row')
                    .find('.bsapp-validation-msg')
                    .addClass('d-none')
                    .removeClass('d-block');
        } else if (field.hasClass('input-group') || field.hasClass('price-block') && $('.select2--spaceType :selected').val()==0){}
        else {
            if(field.hasClass('select2--branches')) {
                field.next().addClass("border-danger").removeClass('border-0');
            } else {
                field.addClass('border-danger').removeClass('border-0');
            }

            validated = false;
        }
    });
    return validated;
}



// Store Settings :: Load Fee Counter
$('#storeSettings > .dropdown-toggle').click(function () {
    apiProps = {
        fun: "RegistrationFeeCounter",
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'renderPaymentsCounter');
    getShopSettings()
});
function renderPaymentsCounter(count) {
    if (count.counter > 0)
        $('.bsapp-fees-counter').addClass('d-inline-block')
                .removeClass('d-none')
                .text(count.counter)
    else
        $('.bsapp-fees-counter').addClass('d-none').removeClass('d-inline-block').text('')
}

// Manage Items :: Global Scope
var item,
        item_id,
        newItemName,
        item_type,
        items = [];

// Manage Items :: Inline Form toggle
$("body").on("click", ".bsapp-edit-item, .bsapp-new-item", function () {
    var old_val = '',
            form = $(this).parents('.form-toggle');
    form.find('.dropdown-menu').removeClass('show');
    if (form.children('.form-static').hasClass('d-none')) {
        form.children('.form-static')
                .toggleClass('d-flex d-none');
        form.children('.form-inline')
                .remove()
    } else {
        if (form.find('.item-label').length)
            old_val = form.find('.item-label').text();
        form.children('.form-static').toggleClass('d-flex d-none');
        form.append(`<div class="form-inline d-flex"> <label class="sr-only" for="inlineFormInputGroup">'+lang('enter_name')+'</label> <div class="input-group d-flex flex-row w-100 border bsapp-border-primary rounded pis-10 pie-6"> <input autocomplete="off" type="text" class="w-75 flex-grow-1 outline-none border-0 shadow-none py-5 pie-10" id="inlineFormInputGroup" placeholder="${lang("enter_name")}" value="${old_val}"> <div class="w-25 input-group-apend border-0 rounded-right"> <div class="bsapp-save-item btn text-primary py-3 px-7 bsapp-fs-24"> <i class="fal fa-check-circle"></i> </div><div class="bsapp-edit-item btn text-gray-700 py-3 px-7 bsapp-fs-24"> <i class="fal fa-minus-circle"></i> </div></div></div></div>`)
    }
});

function clearItemInput() {
    $('.bsapp-new-item').click()
}

// Manage Items :: Inline Form Edit / Add New
$("body").on("click", ".bsapp-save-item", function () {
    var form = $(this).parents('.form-toggle'),
            new_val = form.find('input').val(),
            old_val = form.find('.item-label');
    item = $(this).parents('.bsapp-item-row');
    item_id = item.data('id');
    if (item_id != undefined) { // If exists = edited
        old_val.text(new_val);
        var props = {
            id: item_id,
            name: new_val};
        if (item_type == "memberships")
            props.fun = "renameMembershipCategory"
        else if (item_type == "categories")
            props.fun = "renameProductCategory";
        postApi('storeSettings', props);
        form.children('.form-static').toggleClass('d-flex d-none');
        form.children('.form-inline').remove()
    } else { // if new
        if (new_val != '') {
            apiProps = {
                CompanyNum: $companyNo,
                name: new_val}
            if (item_type == "memberships")
                apiProps.fun = "insertNewMembershipType"
            else if (item_type == "categories")
                apiProps.fun = "insertNewCategory";
            postApi('storeSettings', apiProps, 'renderItem');
            newItemName = new_val;
            form.children('.form-static').toggleClass('d-flex d-none');
            form.children('.form-inline').remove()
        }
    }
});

// Manage Items :: Render New Item
function renderItem(id, count) {
    if (count == undefined)
        count = 0;
    var hide_btn = dropdown_hide;
    itemRow('', id, newItemName, count, hide_btn)
}

// Manage Items :: Load All Items
function getItems(type) {
    $('.items-list li:not(.item-loading)').remove();
    $('.item-loading').show();
    apiProps = {
        CompanyNum: $companyNo};
    if (type == "memberships") {
        $('.storeSettings-manage-items h3').text(lang('manage_subscription_type'));
        $('.bsapp-new-item').text(lang('add_new_membership_type'));
        apiProps.fun = "getMembershipTypeAndAmounts";
        item_type = "memberships";
    } else if (type == "categories") {
        $('.storeSettings-manage-items h3').text(lang('product_cat_manage'));
        $('.bsapp-new-item').text(lang('new_category'));
        apiProps.fun = "getCategoriesAndAmounts"
        item_type = "categories";
    }
    postApi('storeSettings', apiProps, 'renderItems')
}

// Manage Items :: Render Items
function renderItems(result) {
    $('.item-loading').hide();
    items = [];
    if (result.length) {
        for (var i = 0; i < result.length; i++) {
            if (item_type == "memberships")
                var name = result[i].Type
            else if (item_type == "categories")
                var name = result[i].Name;
            var itemCount = result[i].countOfItems,
                    hidden = '',
                    hide_btn = dropdown_hide;
            if (result[i].disabled == 1) {
                hidden = 'disabled-style';
                hide_btn = dropdown_unhide
            }
            itemRow(hidden, result[i].id, name, itemCount, hide_btn);
            items.push({'name': name, 'id': result[i].id})
        }
        updateItemsSelect()
    } else
        $('.items-list').append('<li class="item-loading mb-10 animated"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">No Entries Found</div></li>')
}

// Manage Items :: Item Row Template
function itemRow(hidden, id, name, itemCount, hide_btn) {
    var markup = `<li class="mb-10 bsapp-item-row ${hidden}" data-id="${id}" > <div class="form-toggle"> <div class="form-static d-flex align-items-center justify-content-between border rounded font-weight-bolder text-gray-700 text-start m-0 py-7 px-10"> <div class="d-flex w-100"> <div class="text-truncate"> <span class="item-label">${name}</span> </div><span class="font-weight-normal pis-5 bsapp-fs-14"> (<span class="item-count">${itemCount}</span> `+lang('items')+`)</span> </div><div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow" disabled="false"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="bsapp-edit-item d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-edit fa-fw mx-5"></i> ${lang('edit')} </a> </li><li class="mb-6"> <a role="button" class="bsapp-hide-item hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16" >${hide_btn}</a> </li><li> <a class="bsapp-delete-item d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16" role="button"> <i class="fal fa-minus-circle fa-fw mx-5"></i> ${lang('delete')} </a> </li></ul> </div></div></div></div></li>`;

    $('.items-list').append(markup)
}

// Manage Items :: Hide Item
$('.bsapp-settings-panel').on('click', '.bsapp-hide-item', function () {
    item = $(this).parents('.bsapp-item-row');
    item_id = item.data('id');
    var hidden = (item.hasClass('disabled-style')) ? 0 : 1;
    apiProps = {
        id: item_id,
        disabled: hidden,
        CompanyNum: $companyNo
    };
    if (item_type == "memberships")
        apiProps.fun = "disableMembershipType"
    else if (item_type == "categories")
        apiProps.fun = "disableCategory";
    postApi('storeSettings', apiProps)
});

// Manage Items :: Update Remove Item Screen
function updateRemoveForm(name, count) {
    const parent = $('.storeSettings-remove-item');
    if (item_type == "memberships")
        parent.find('.item-type').text(lang('club_membership_smart_link'))
    else if (item_type == "categories")
        parent.find('.item-type').text(lang('category_single'));
    parent.find('.item-label').text(name);
    parent.find('.item-count').text(count)
}

// Manage Items :: Delete items call
var delete_item_id = '',
        createNewItem;
$('.bsapp-settings-panel').on('click', '.bsapp-delete-item', function () {
    var parent = $(this).parents('.bsapp-item-row'),
            item_name = parent.find('.item-label').text(),
            item_count = parent.find('.item-count').text();
    delete_item_id = parseInt(parent.attr('data-id'));

    if (item_count > 0) {
        updateRemoveForm(item_name, item_count);
        switchSettingsPanel($(this), 'storeSettings-remove-item');
        // disable delete item from select
        if(delete_item_id) {
            let $selectElem = $('.bsapp-settings-panel').find('#move-item');
            $selectElem.find('option').removeAttr('disabled');
            $selectElem.find('option[value="'+delete_item_id +'"]').prop('disabled', true);
            $selectElem.select2();
            let opt = $selectElem.find('option:not([disabled]):first').val();
            if(opt) {
                $selectElem.val(opt).trigger('change');
            }
        }
    } else {
        deleteItem();
        parent.addClass('opacity-50');
    }
});

// Manage Items :: Delete Item
function deleteItem(moveTo) {
    apiProps = {
        id: delete_item_id};
    if (moveTo != undefined)
        apiProps.otherId = moveTo;
    if (item_type == "memberships")
        apiProps.fun = "deleteOrMoveMembershipType"
    else if (item_type == "categories")
        apiProps.fun = "deleteOrMoveCategory";
    postApi('storeSettings', apiProps, 'removeItemRow')
}

// Manage Items :: Move Item
function moveItem() {
    apiProps = {};
    newItemName = $('#move-item').val();
    if (createNewItem) { // If Moving to New Category
        apiProps.CompanyNum = $companyNo;
        apiProps.name = newItemName;
        if (item_type == "memberships")
            apiProps.fun = "insertNewMembershipType"
        else if (item_type == "categories")
            apiProps.fun = "insertNewCategory";
        createNewItem = false;
        postApi('storeSettings', apiProps, 'moveItemsToNew')
    } else {
        deleteItem(newItemName)
    }
}

// Manage Items :: Move to New callback
function moveItemsToNew(moveToId) {
    var count = $('.items-list').find('.bsapp-item-row[data-id=' + delete_item_id + '] .item-count').text();
    renderItem(moveToId, count);
    deleteItem(moveToId)
}

// Manage Items :: Remove Item Row
function removeItemRow() {
    $('.items-list').find('[data-id="' + delete_item_id + '"]').remove();
    delete_item_id = '';
    const manage_item_modal = $('.storeSettings-remove-item');
    if(manage_item_modal.is(':visible')) {
        switchSettingsPanel(manage_item_modal.find('.bsapp-delete-or-move'), 'storeSettings-manage-items');
    }
}

// Remove Item :: Update Items Select Options
function updateItemsSelect() {
    var itemOptions = items.map(item => {
        var item = {
            'id': item.id,
            'html': `<span data-id="${item.id}">${item.name}</span>`,
            'text': item.name,
            'title': item.name
        };
        return item
    });
    $(".bsapp-settings-dialog .select2--items").select2({
        placeholder: '',
        tags: true,
        data: itemOptions,
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            return data.text
        },
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term == '')
                return null;
            return {
                id: term,
                text: term,
                newTag: true
            }
        }
    }).on('select2:select', function (e) {
        $('.select2--items option[value="' + e.params.data.id + '"').attr('selected', 'selected');
        if (e.params.data.newTag) {
            createNewItem = true;
            $('.bsapp-new-tag').removeClass('d-none').addClass('d-block');
        } else {
            createNewItem = false;
            $('.bsapp-new-tag').removeClass('d-block').addClass('d-none');
        }
    })
}

// Manage Items :: Delete Or Move Button
$('.bsapp-delete-or-move').click(function () {
    if ($('#move-items').is(":checked"))
        moveItem();
    else
        deleteItem()
});

// Order Products :: Tab Change (Bug when returning back from items - to fix)
$('.bsapp-settings-panel a[role="tab"]').on('click', function () {
    $(this).removeClass('active');
    var target = $(this).attr('href');
    $(this).parents('.scrollable')
            .find('.tab-pane')
            .removeClass('show active');
    $(target).addClass('show active')
});

// Payment & Billing :: Get Both Settings
function GetPaymentsSettings() {
    getPaymentSettings('bit')
    getPaymentSettings('spread');
    getPaymentSettings('debit')
}
function getPaymentSettings(type) {
    $('.btn-save-spread, .btn-save-debit').removeClass('d-block').addClass('d-none');
    apiProps = {
        CompanyNum: $companyNo};
    if (type == "spread") {
        apiProps.fun = "GetPaymentsByCompanyNum";
        var callback = "renderSpread";
    } else if (type == "debit") {
        apiProps.fun = "GetPeriodicPaymentByCompanyNum";
        var callback = "renderDebit"
    } else if (type === "bit") {
        apiProps.fun = "GetBitSettings";
        var callback = "renderBit"
    }
    postApi('storeSettings', apiProps, callback)
}

// Payment & Billing :: Spread Toggle
$("#spread-payments-switch").click(function () {
    var toggle = ($(this).is(':checked')) ? 1 : 0;
    apiProps = {
        CompanyNum: $companyNo,
        fun: "toggleSpreadPayment",
        id: $('.storeSettings-spread-payments').attr('data-id'),
        payment: toggle
    };
    postApi('storeSettings', apiProps);
    if (toggle == 1)
        insertPaymentsNewData()
});

// Payment & Billing :: Spread Toggle
$("#bit-payments-switch").click(function () {
    var toggle = ($(this).is(':checked')) ? 1 : 0;
    apiProps = {
        CompanyNum: $companyNo,
        fun: "toggleBitPayments",
        id: $('.storeSettings-spread-payments').attr('data-id'),
        payment: toggle
    };
    postApi('storeSettings', apiProps);
});

// Payment & Billing :: Insert New Spreat
function insertPaymentsNewData() {
    apiProps = {
        CompanyNum: $companyNo,
        fun: "InsertPeriodicPaymentNewData"};
    postApi('storeSettings', apiProps)
}

// Payment & Billing :: Render Spread Settings
function renderSpread(result) {

    var screen = $('.storeSettings-spread-payments'),
            settings = result.CompanyPay,
            periodic_payments = (settings.PeriodicPayments == 1) ? 1 : 0,
            maxPaymentByValid = (settings.MaxPaymentsNumberByValid == 1) ? 1 : 0;
    max_payment = (settings.MaxDistribution != null) ? settings.MaxDistribution : '';

    screen.attr('data-id', settings.id);
    screen.find('[data-prop="Interest"]').remove();
    screen.find('[data-prop="LimitPayments"]').remove();

    if (periodic_payments == 1) {
        $('#spread-periodic').prop('checked', true);
    } else {
        $('#spread-periodic').prop('checked', false);
    }

    if (maxPaymentByValid == 1) {
        $('#max-payment-numbers-by-valid').prop('checked', true);
    } else {
        $('#max-payment-numbers-by-valid').prop('checked', false);
    }

    $('#spread-distribution').val(max_payment);
    var limitPayments = JSON.parse(settings.LimitPayments),
            interest = JSON.parse(settings.Interest);
    if(limitPayments !== null && limitPayments.length) {
        for (var i = 0; i < limitPayments.length; i++)
            addPayoutLimit($('.add-payout-limit'), limitPayments[i].LimitPayments, limitPayments[i].MaximumAmount);
    }
    if(interest !== null && interest.length){
        for (var i = 0; i < interest.length; i++)
            setInterestRate($('.add-interest-rate'), interest[i].LimitPayments, interest[i].MaximumAmount);
    }

    $('.btn-save-spread').removeClass('d-block').addClass('d-none')
}

// Payment & Billing :: Render Debit Settings
function renderDebit(data) {
    var settings = data.CompanyPayment,
            charge_payment = (settings.ChargePayment == 1) ? true : false,
            prevent_orders = (settings.PreventOrders == 1) ? true : false,
            prevent_classes = (settings.PreventClasses == 1) ? true : false;
    toggleSettingsSwitch('#managing-periodic-switch', charge_payment);
    toggleSettingsSwitch('#prevent-booking-switch', prevent_orders);
    toggleSettingsSwitch('#cancel-subsription-switch', prevent_classes);
    if (charge_payment)
        $('#periodic-charge-day').val(settings.ChargeDay);
    if (prevent_orders) {
        if (settings.PreventOrdersInstantly == 1)
            $('#prevent-booking-instantly').click()
        else if (settings.PreventOrdersInstantly == 0) {
            $('#prevent-booking-after').click();
            $('#prevent-booking-days').val(settings.PreventOrderDays)
        }
    }
    if (prevent_classes) {
        if (settings.PreventClassesInstantly == 1)
            $('#prevent-classes-instantly').click()
        else if (settings.PreventClassesInstantly == 0) {
            $('#prevent-classes-after').click();
            $('#prevent-classes-days').val(settings.PreventClassesDays)
        }
    }
    $('.btn-save-debit').removeClass('d-block').addClass('d-none')
}

function renderBit(data) {
    if (data.bit == 0) {
        $('#bit-payments-switch').parents('li').remove();
    } else {
        $('#bit-payments-switch').parents('li').removeClass('d-none');
    }
}

// Payment & Billing :: Toggle Switch - Can turn global
function toggleSettingsSwitch(elem, checked) {
    if (checked == true) {
        $(elem).prop('checked', true);
        $(elem).closest('.form-toggle')
                .children('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $(elem).prop('checked', false);
        $(elem).closest('.form-toggle')
                .children('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')
    }
}

var cat_id;
// Main :: Get Shop Settings
function getShopSettings() {
    apiProps = {
        fun: "getSingleCompanySettings",
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'renderShopSettings')
}

// Main :: Render Shop Settings
function renderShopSettings(settings) {
    $("#negative-balance-switch, #manage-memberships-switch").attr('data-id', settings.id);
    if (settings.offsetMemberships == 1)
        $('#negative-balance-switch').prop('checked', true)
    else
        $('#negative-balance-switch').prop('checked', false);
    if (settings.manageMemberships == 1) {
        $('#manage-memberships-switch').prop('checked', true)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $('#manage-memberships-switch').prop('checked', false)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')

        $('#manage-memberships-switch').parent().removeClass('d-none');
    }
    if (settings.spreadPayments == 1) {
        $('#spread-payments-switch').prop('checked', true)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $('#spread-payments-switch').prop('checked', false)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')
    }
    if (settings.bitPayments == 1) {
        $('#bit-payments-switch').prop('checked', true)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $('#bit-payments-switch').prop('checked', false)
                .parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')
    }
}

// General :: Toggle Shop Settings
function toggleShopSettings($this) {
    if ($this.siblings('input').is(':checked'))
        var toggle_val = 0
    else {
        var toggle_val = 1
    }
    var shopSettings_id = $this.siblings('input').data('id');
    apiProps = {
        id: shopSettings_id};
    if ($this.siblings('input#manage-memberships-switch').length) {

        $('#js-go-to-support').modal();
        return;
        // apiProps.toggle = toggle_val;
        // apiProps.fun = "toggleManageMemberships";
    } else if ($this.siblings("input#negative-balance-switch").length) {
        apiProps.offset = toggle_val;
        apiProps.fun = "toggleOffsetSetting"
    }
    // Can add spread payments toggle here
    postApi('storeSettings', apiProps)
}

// General :: Toggle Row Hide
var dropdown_hide = '<i class="fal fa-low-vision fa-fw mx-5"></i> <span>'+lang('hide')+'</span>',
        dropdown_unhide = '<i class="fal fa-eye fa-fw mx-5"></i> <span>'+ lang('show_client_profile') +'</span>';
function toggleHideRow($this) {
    const item = $this.parents('li[data-id]'),
        hideSwitch = item.find('.custom-control-input.hide-toggle'),
        hideBtn = item.find('a.hide-toggle');
    item.toggleClass('disabled-style');
    let chkd = (hideBtn.html() != dropdown_hide);
    if (hideBtn.length == 0) {
        chkd = !item.hasClass('disabled-style');
    } else {
        hideBtn.html((hideBtn.html() == dropdown_hide) ? dropdown_unhide : dropdown_hide)
    }
    hideSwitch.prop('checked', chkd);
}
$('.bsapp-settings-panel').on('click', '.hide-toggle', function () {
    toggleHideRow($(this))
});


// Spread Payments :: Toggle Spread
function toggleSpread(result) {
    if (result.CompanyPay == null) {
        $("#spread-payments-switch").prop('checked', false);
        $("#spread-payments-switch").closest('.form-toggle')
                .children('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')
    } else {
        $("#spread-payments-switch").prop('checked', true);
        $("#spread-payments-switch").closest('.form-toggle')
                .children('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    }
}

// Order Product Display :: Get Orders
function getDisplayOrders($type) {
    $('.order-' + $type + '-list li:not(.item-placeholder)').remove();
    $('.item-loading').show();
    apiProps = {
        CompanyNum: $companyNo}
    var list = $('.order-' + $type + '-list'),
            callback = '';
    if ($type == "memberships") {
        apiProps.fun = "getCompanyMemberships";
        callback = "renderMembershipOrder"
    } else if ($type == "categories") {
        apiProps.fun = "getCategoriesAndAmounts";
        callback = "renderCategoriesOrder"
    } else if ($type == "items") {
        apiProps.fun = "getItemsForSelectedCategory";
        apiProps.id = cat_id;
        callback = "renderItemsOrder"
    } else
        return;
    postApi('storeSettings', apiProps, callback);

    list.sortable({
        items: "> li:not(.item-placeholder)",
        handle: ".fa-grip-vertical",
        stop: function (event, ui) {
            var items = $(this).find("li:not(.item-placeholder)");
            apiProps = {
                orderArr: []};
            if ($(this).hasClass('order-memberships-list'))
                apiProps.fun = "reorderItems"
            else if ($(this).hasClass('order-categories-list'))
                apiProps.fun = "reorderCategories"
            else if ($(this).hasClass('order-items-list'))
                apiProps.fun = "reorderItems"
            items.each(function (i) {
                var item_id = parseInt($(this).attr('data-id')),
                        item_order = i + 1;
                $(this).children('.item-row-count').text(item_order);
                apiProps.orderArr.push({id: item_id, order: item_order})
            });
            postApi('storeSettings', apiProps)
        }
    }).disableSelection()
}

// Order Products Display :: Render Initial Membership Order
function renderMembershipOrder(memberships) {
    $('.item-loading').hide();
    $('.order-membership-list li:not(.item-placeholder)').remove();
    var data = {};
    data = memberships.sort(function (a, b) {
        return a.order - b.order
    });

    if (data.length) {
        for (var i = 0; i < data.length; i++) {
            var period = data[i].Vaild,
                    rowCount = i + 1;
            if (data[i].Vaild_Type == 1)
                period = period + ' d';
            else if (data[i].Vaild_Type == 2)
                period = period + ' w';
            else if (data[i].Vaild_Type == 3)
                period = period + ' m';

            var markup = `<li class="d-flex align-items-center mb-10" data-id="${data[i].id}"> <span class="item-row-count font-weight-bolder py-7 pis-0 pie-10">${rowCount}</span> <div class="flex-grow-1 border bg-white rounded py-7 px-0"> <div class="d-flex"> <span class="d-flex align-items-center col-6 text-start px-5"> <i class="fas fa-grip-vertical pie-3 text-gray-500 mie-7 pis-6 bsapp-fs-16" role="button"></i> <span class="bsapp-lh-16">${data[i].ItemName}</span> </span> <span class="col-2 text-center px-5">${period}</span> <span class="col-2 text-center px-5">${data[i].BalanceClass}</span> <span class="col-2 text-end pis-5 pie-10">${parseInt(data[i].ItemPrice)}₪</span> </div></div></li>`;
            $('.order-memberships-list').append(markup)
        }
    } else
        $('.order-membership-list').append('<li class="item-loading mb-10 animated"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">No Memberships Found</div></li>')
}

// Order Products Display :: Render Initial Category Order
function renderCategoriesOrder(data) {
    $('.item-loading').hide();
    $('.order-categories-list li:not(.item-placeholder)').remove();

    if (data.length) {
        for (var i = 0; i < data.length; i++) {
            var rowCount = i + 1,
                    markup = `<li class="d-flex align-items-center mb-10" data-id="${data[i].id}"> <span class="item-row-count font-weight-bolder py-7 pis-0 pie-10">${rowCount}</span> <div class="col-auto flex-grow-1 bg-white border rounded py-7 px-0"> <div class="d-flex"> <span class="col-auto flex-grow-1 text-gray-700 text-start px-5 bsapp-fs-14"> <i class="fas fa-grip-vertical text-gray-500 mie-7 pis-6 bsapp-fs-16" role="button"></i> <span>${data[i].Name}</span> </span> <div class="bsapp-order-items-btn col-2 text-gray-700 text-end pis-5 pie-5 bsapp-fs-21" role="button" onclick="showItems($(this))"> <i class="fal fa-angle-right pie-10"></i> </div> </div></div></li>`; // Move to template component later
            $('.order-categories-list').append(markup)
        }
    } else
        $('.order-categories-list').append('<li class="item-loading mb-10 animated"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">No Categories Found</div></li>')
}

// Order Products Display :: Render Initial Items Order
function renderItemsOrder(data) {
    $('.item-loading').hide();
    $('.order-items-list li:not(.item-placeholder)').remove();

    if (data.length) {
        for (var i = 0; i < data.length; i++) {
            var rowCount = i + 1,
                    markup = `<li class="d-flex align-items-center mb-10" data-id="${data[i].id}"> <span class="item-row-count font-weight-bolder py-7 pis-0 pie-10">${rowCount}</span> <div class="col-auto flex-grow-1 bg-white border rounded py-7 px-0"> <div class="d-flex"> <span class="col-9 text-gray-700 text-start px-5"> <i class="fas fa-grip-vertical text-gray-500 mie-7 pis-6 bsapp-fs-16" role="button"></i> <span>${data[i].ItemName}</span> </span> <span class="col-3 text-end pis-5 pie-10 bsapp-fs-14">${parseInt(data[i].ItemPrice)}₪</span> </div></div></li>`;
            $('.order-items-list').append(markup)
        }
    } else
        $('.order-items-list').append(`<li class="item-loading mb-10 animated"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_items_found')}</div></li>`)
}

// Order Products Display :: Get Items for selected category
function showItems($this) {
    cat_id = $this.parents('li').attr('data-id');
    $('.storeSettings-order-products .tab-pane').removeClass('show active');
    $('#list-items').addClass('show active');
    getDisplayOrders('items')
}

// Fixed Payments :: Get Registration
function getRegistrationFees() {
    apiProps = {
        fun: "GetRegistrationFeesByCompanyNum",
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'renderRegistrationFees');
    getMembershipItems()
}

// Fixed Payments :: Get Memberships & Update Select
function getMembershipItems() {
    apiProps = {
        fun: "getAllItems",
        noSingleClass: '1',
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'updateMembershipsSelect')
}
function updateMembershipsSelect(membershipTypes) {
    var memberships = membershipTypes.map(membership => {
        var membership = {
            'id': membership.id,
            'html': `<span data-id="${membership.id}">${membership.ItemName}</span>`,
            'text': membership.ItemName,
            'title': membership.ItemName};
        return membership
    });
    $(".bsapp-settings-dialog .select2--memberships").select2({
        // theme: "bootstrap",
        placeholder: lang('select_item'),
        language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
        dir: $("html").attr("dir"),
        data: memberships,
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            return data.text
        },
        minimumResultsForSearch: -1,
        dropdownParent: $('.bsapp-settings-dialog')})
}

// Fixed Payments :: Render Registration Fees
function renderRegistrationFees(data) {
    $('.registration-fees-list li:not(.fees-placeholder)').remove();
    $('.fees-loading').hide();
    var result = data.CompanyPay;
    if (result.length) {
        for (var i = 0; i < result.length; i++) {
            var toggle = 'checked',
                    hidden = '',
                    hide_btn = dropdown_hide;
            if (result[i].disabled == "1") {
                hidden = 'disabled-style';
                hide_btn = dropdown_unhide,
                        toggle = ''
            }
            var markup = `<li class="mb-10 bsapp-payment-row ${hidden}" data-id="${result[i].id}"> <div class="form-static d-flex align-items-center border rounded py-8 px-0"> <span class="d-flex align-items-center col-7 font-weight-bold text-start pis-12 pie-0"> <span class="bsapp-lh-16">${result[i].ItemName}</span> </span> <span class="col-2 text-center"> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-payment hide-toggle custom-control-input" id="payment-id-${result[i].id}" ${toggle}> <label class="custom-control-label" for="payment-id-${result[i].id}" role="button"></label> </div></span> <span class="col-2 text-center text-gray-500 font-weight-bold px-0">${parseInt(result[i].ItemPrice)}₪</span> <div class="col-1 text-end pie-10"> <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-payment d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-edit fa-fw mx-5"></i> `+lang('edit')+` </a> </li><li class="mb-6"> <a role="button" class="hide-payment hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a> </li><li> <a role="button" class="delete-payment d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-minus-circle fa-fw mx-5"></i> `+lang('a_remove_single')+` </a> </li></ul> </div></div></div></div></li>`;
            $('.registration-fees-list').append(markup)
        }
    } else
        $('.registration-fees-list').append(`<li class="no-fees-found item-loading mb-10 animated"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_fees_found')}</div></li>`);
    getBranches()
}

// Fixed Payments :: Global Vars
var fee,
        fee_id,
        branches = [],
        edit_fee = false;

// Fixed Payments New :: Update Branches Select
function getBranches() {
    apiProps = {
        fun: 'getCompanyBranches',
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'updateBranchesSelect')
}
function updateBranchesSelect(data) {
    if (!data.length) {
        $('.bsapp-branches-label').hide();
        $('#newPayment-branch').attr('required', false).hide();
        return false;
    }
    branches = [];
    branches.push({'id': 'BA999', 'name': lang('all_branch')});
    for (var i = 0; i < data.length; i++)
        branches.push({'id': data[i].id, 'name': data[i].BrandName});
    var branchOptions = branches.map(branch => {
        var branch = {
            'id': branch.id,
            'html': `<span data-id="${branch.id}">${branch.name}</span>`,
            'text': branch.name,
            'title': branch.name};
        return branch
    });
    $(".bsapp-settings-dialog .select2--branches").select2({
        data: branchOptions,
        theme:"bsapp-dropdown bsapp-no-arrow",
        placeholder:  lang("choose_branch"),
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            return data.text
        },
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
    })
}

function getOutdoor() {
    outdoors= [{
            'status': 0,
            'name': lang("indoor")},
        {
            'status': 1,
            'name': lang("outdoor")
        }];

    var outdoorsOption = outdoors.map(row => {
        var outdoor = {
            'id': row.status,
            'html': `<span data-id="${row.status}">${row.name}</span>`,
            'text': row.name,
            'title': row.name};
        return outdoor
    });
    $(".bsapp-settings-dialog .select2--outdoor").select2({
        data: outdoorsOption,
        theme:"bsapp-dropdown",
        minimumResultsForSearch: -1,
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            return data.text
        },
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
    })

}

function getSpaceType() {
    spaceType = [{
        'status': 0,
        'name': lang("lesson_meeting_registration")
    },
        {
            'status': 1,
            'name': lang("entrances_wo_registration")
        }];

    let spaceOption = spaceType.map(row => {
        let spaceType = {
            'id': row.status,
            'html': `<span data-id="${row.status}">${row.name}</span>`,
            'text': row.name,
            'title': row.name,

        };
        // if(spaceType.id == 0){
        //     $('#js-entrance-price-block').addClass('d-none')
        // } else {
        //     $('#js-entrance-price-block').removeClass('d-none');
        //     $('.js-entrance-price').focus();
        // }
        return spaceType
    });
    $(".bsapp-settings-dialog .select2--spaceType").select2({
        data: spaceOption,
        theme: "bsapp-dropdown",
        minimumResultsForSearch: -1,
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            tooglePriceBySpaceType(data.id)
            return data.text
        },
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
    })

}

function tooglePriceBySpaceType(id) {
    if(id==1){
        $('#js-entrance-price-block').removeClass('d-none');
        $('.js-entrance-price').focus();
        $('[name="entrance-price"]').trigger('change').attr('required', true);
        if ($('[name="entrance-price"]').val() == '') {
            $('[name="entrance-price"]').val(0);
        }
        if ($(`[name="tag"]`).val() == '') {
            fieldEvents.setTag(40,  tagsTranslations.find((o) => { return o["id"] === '40'}).text);
        }
        $('#js-entrance-price-block .tagInfo').removeClass('d-none');
        $('#js-entrance-price-block .bsapp-fs-12').css('line-height', 'normal');
    } else {
        $('[name="entrance-price"]').attr('required', false);
        $('#js-entrance-price-block').addClass('d-none');
    }
}

// Fixed Payments :: Create New Fee
$('.bsapp-new-registration-fee').click(function () {
    $('.storeSettings-fixed-payments-new h3').text(lang('fixed_payment_path'));
    $('.bsapp-save-payment').removeClass('bsapp-edit-payment');
    clearPaymentForm();
    switchSettingsPanel($(this), 'storeSettings-fixed-payments-new')
});
function clearPaymentForm() {
    $('.bsapp-save-payment').text(lang('save'));
    $('.storeSettings-fixed-payments-new input').val('');
    $('.select2--payment-form, .select2--periodic-type').val('1').trigger('change');
    $('.select2--memberships, .select2--branches, .select2--outdoor, .select2--spaceType').val('[]').trigger('change');
    $('#newPayment-required').prop('checked', true)
            .parents('.form-toggle')
            .find('.toggle-content')
            .addClass('d-none')
            .removeClass('d-flex')
}

// Fixed Payments :: Delete Fee
$('.bsapp-settings-panel').on('click', '.delete-payment', function () {
    fee = $(this).parents('.bsapp-payment-row');
    fee_id = fee.data('id');
    fee.children('.form-static').addClass('opacity-50');
    apiProps = {
        fun: "deletePayment",
        id: fee_id};
    postApi('storeSettings', apiProps, 'deleteRegistration')
});
function deleteRegistration(result) {
    if (result.Status == "Success")
        fee.remove()
}

// Fixed Payments :: Edit Fee
$('.bsapp-settings-panel').on('click', '.edit-payment', function () {
    var target = $(this).parents('.bsapp-payment-row');
    fee_id = target.data('id');
    edit_fee = true;
    target.children('.form-static').addClass('border-success');
    apiProps = {
        fun: "getSingleRegistration",
        id: fee_id};
    clearPaymentForm();
    postApi('storeSettings', apiProps, 'editRegistration')
});
function editRegistration(data) {
    $('.bsapp-save-payment').addClass('bsapp-edit-payment');
    $('.bsapp-edit-payment').attr("data-payment-id", data.id);
    $('.registration-fees-list').find(("[data-id='" + data.id + "']"))
            .children('.form-static')
            .removeClass('border-success');
    $('#newPayment-title').val(data.ItemName);
    $('#newPayment-price').val(parseInt(data.ItemPrice));
    if (data.VatAmount > 0)
        $('#newPayment-vat').prop('checked', true);
    if (data.Brand)
        $('#newPayment-branch').val(data.Brand).trigger('change');
    if (data.AllMemberships == 1) {
        $('#newPayment-required').prop('checked', true);
        $('#newPayment-required').parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-none')
                .removeClass('d-flex')
    } else {
        $('#newPayment-required').prop('checked', false);
        $('#newPayment-required').parents('.form-toggle')
                .find('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    }
    if ((data.MembershipList != null) && (data.MembershipList != undefined)) {
        var memberships = JSON.parse(data.MembershipList),
                selectedMemberships = [];
        for (var i = 0; i < memberships.length; i++)
            selectedMemberships.push(memberships[i].item);
        $('.select2--memberships').val(selectedMemberships).trigger('change')
    }

    $('.select2--payment-form').val(data.Type).trigger('change');
    if (data.Vaild_Type)
        $('.select2--periodic-type').val(data.Vaild_Type).trigger('change');
    if (data.Vaild)
        $('#newPayment-periodic-val').val(data.Vaild);

    $('.storeSettings-fixed-payments-new h3').text(lang('fixed_payment_path_edit'));
    switchSettingsPanel($(this), 'storeSettings-fixed-payments-new')
}

// Fixed Payments :: Hide Fee
$('.bsapp-settings-panel').on('click', '.hide-payment', function () {
    fee = $(this).parents('.bsapp-payment-row');
    fee_id = fee.data('id');
    apiProps = {
        fun: "disablePayment",
        id: fee_id,
        disabled: (fee.hasClass('disabled-style') ? 1 : 0)
    };
    postApi('storeSettings', apiProps)
});

// Fixed Payments New :: Save Payment
$('.bsapp-save-payment').click(function () {
    var fields = $(this).parents(".bsapp-settings-panel").find("[required]"),
            edited = false;
    if ($(this).hasClass('bsapp-edit-payment'))
        edited = true;
    if (validateSettingsFields(fields)) {
        fetchPaymentData(edited);
        clearPaymentForm();
        if(!edited) {
            $('.registration-fees-list .item-loading.no-fees-found').remove();
        }
        switchSettingsPanel($(this), 'storeSettings-fixed-payments')
    }
});

// Fixed Payments New :: Trigger Validation
$('.storeSettings-fixed-payments-new input[required]').on('change keyup', function () {
    validateSettingsFields($(this))
});

// Fixed Payments New :: Fetch Data
function fetchPaymentData(edited) {
    $('.bsapp-save-payment').text(lang('saving_imgpicker'));
    var title = $('#newPayment-title').val(),
            amount = $('#newPayment-price').val(),
            vat = ($('#newPayment-vat').is(':checked')) ? true : false,
            branches = $('#newPayment-branch').val(),
            all_memberships = ($('#newPayment-required').is(':checked')) ? 1 : 0,
            memberships = $('#newPayment-memberships').val(),
            payment_type = $('.select2--payment-form').val();
    fee = {
        CompanyNum: $companyNo,
        vat: vat,
        AllMemberships: all_memberships,
        Type: payment_type,
        Brand: 'BA999'
    };
    if (title != '') {
        fee.ItemName = title;
    }
    if (amount != '') {
        fee.ItemPrice = amount;
    }
    if (branches !== '' && branches !== null) {
        fee.Brand = branches;
    }
    if (payment_type == 3) {
        fee.Vaild = $('#newPayment-periodic-val').val();
        fee.Vaild_Type = $('#newPayment-periodic-type').val()
    }
    if (all_memberships == 0) {
        var selectedMemberships = [];
        for (var i = 0; i < memberships.length; i++) {
            var member = {
                item: memberships[i]};
            selectedMemberships.push(member)
        }
        fee.MembershipList = JSON.stringify(selectedMemberships)
    }
    fee.fun = (edit_fee) ? "UpdateRegistrationFees" : "InsertRegistrationFeesNewData";
    if (edit_fee) {
        fee.id = $('.bsapp-edit-payment').attr("data-payment-id");
    }
    postApi('storeSettings', fee, 'updateFees')
}

// Fixed Payments New :: Save Data
function updateFees(data) {
    var $id = (edit_fee) ? fee.id : data.id,
            markup = `<li class="mb-10 bsapp-payment-row" data-id="${$id}"> <div class="form-static d-flex align-items-center border rounded py-8 px-0" data-edit="storeSettings-fixed-payments-new"> <span class="d-flex align-items-center col-7 font-weight-bold text-start pis-12 pie-0"> <span class="bsapp-lh-16">${fee.ItemName}</span> </span> <span class="col-2 text-center"> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-payment hide-toggle custom-control-input" id="payment-status-id-1" checked> <label class="custom-control-label" for="payment-status-id-1" role="button"></label> </div></span> <span class="col-2 text-start text-gray-500 font-weight-bold">${fee.ItemPrice}₪</span> <div class="col-1 text-end pie-10"> <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-payment d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-edit fa-fw mx-5"></i> `+lang('edit')+` </a> </li><li class="mb-6"> <a role="button" class="hide-payment hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${dropdown_hide}</a> </li><li> <a role="button" class="delete-payment d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-minus-circle fa-fw mx-5"></i> `+lang('a_remove_single')+` </a> </li></ul> </div></div></div></div></li>`;
    if (!edit_fee)
        $('.registration-fees-list').append(markup)
    else
        $('.registration-fees-list').find(("[data-id='" + fee.id + "']")).replaceWith(markup);
    edit_fee = false;
    fee = {}
}

// Fixed Payments New :: Change Fee Type
$('.select2--payment-form').on('change', function (e) {
    var selected = $(this).find(':selected'),
            target = selected.val(),
            form = $(this).parents('.bsapp-payment-form'),
            periodic = form.siblings('.bsapp-payment-periodic');
    description = $('.bsapp-payment-description');
    if (target == 3) {
        periodic.addClass('d-flex')
                .removeClass('d-none')
                .children('input')
                .prop('required', true)
    } else {
        periodic.removeClass('d-flex')
                .addClass('d-none')
                .children('input')
                .prop('required', false)
    }
    /*description.children('span').removeClass('d-block');
     description.children('span:nth-child('+target+')').addClass('d-block');*/ });

// Direct Debit :: Validation Start
$('.storeSettings-direct-debit input').on('change keyup', function () {
    $('.btn-save-debit').removeClass('d-none').addClass('d-block');
});

// Direct Debit :: Update On Save
function updatePeriodicPayments($this) {
    apiProps = {
        fun: "UpdatePeriodicPayment",
        CompanyNum: $companyNo};

    if ($("#managing-periodic-switch").is(':checked')) {
        apiProps.ChargePayment = 1;
        apiProps.ChargeDay = $('#periodic-charge-day').val();
    } else {
        apiProps.ChargePayment = 0;
        apiProps.ChargeDay = ''
    }

    if ($("#prevent-booking-switch").is(':checked')) {
        apiProps.PreventOrders = 1;
        if ($("#prevent-booking-instantly").is(':checked')) {
            apiProps.PreventOrdersInstantly = 1;
            apiProps.PreventOrderDays = '';
        } else {
            apiProps.PreventOrdersInstantly = 0;
            apiProps.PreventOrderDays = $('#prevent-booking-days').val()
        }
    } else {
        apiProps.PreventOrders,
                apiProps.PreventOrdersInstantly = 0;
        apiProps.PreventOrderDays = ''
    }

    if ($("#cancel-subsription-switch").is(':checked')) {
        apiProps.PreventClasses = 1;
        if ($("#cancel-subscrption-instantly").is(':checked')) {
            apiProps.PreventClassesInstantly = 1;
            apiProps.PreventClassesDays = '';
        } else {
            apiProps.PreventClassesInstantly = 0;
            apiProps.PreventClassesDays = $('#prevent-classes-days').val()
        }
    } else {
        apiProps.PreventClasses,
                apiProps.PreventClassesInstantly = 0;
        apiProps.PreventClassesDays = ''
    }

    postApi('storeSettings', apiProps);
    $this.removeClass('d-block').addClass('d-none')
}


$(document).on('change', '#newCalendar-name', function () {
        $('#newCalendar-name').removeClass('border-danger').addClass('border-0')
});
$('.select2--branches').on('select2:select', function () {
    $('.select2--branches').next().removeClass("border-danger").addClass('border-0')
});

// Spread Payments :: Validation
$(document).on('change keyup', '.storeSettings-spread-payments input', function () {
    if (validateSettingsFields($(this))) {
        validateSpreadFields($(this));
    } else
        $('.btn-save-spread').removeClass('d-block').addClass('d-none')
});

function validateSpreadFields(el) {
    var target = el.attr('data-pair'),
            val = el.val(),
            fields = el.parents('.bsapp-spread-group').siblings();
    if (fields.length) {
        fields.each(function () {
            var other_el = $(this).find('[data-pair="' + target + '"]'),
                    field = (el.parent('.input-group').length) ? el.parent() : el;
            if (val == other_el.val()) {
                field.addClass('border-danger').removeClass('border-0');
                $('.btn-save-spread').removeClass('d-block').addClass('d-none');
                return false
            } else {
                field.removeClass('border-danger').addClass('border-0');
                $('.btn-save-spread').removeClass('d-none').addClass('d-block')
            }
        });
    } else
        $('.btn-save-spread').removeClass('d-none').addClass('d-block');
}

// Spread Payments :: Add payout limit
function addPayoutLimit(el, no, amount) {
    var payments = (no) ? no : '',
            from = (amount) ? amount : '',
            html = `<div class="bsapp-spread-group d-flex align-items-center justify-content-between text-gray-700 mb-15 bsapp-fs-16" data-prop="LimitPayments"> <span>${lang('short_from')}</span> <div class="col-2 input-group d-flex align-items-center bg-light rounded border mx-7 py-2 px-5"> <div class="input-group-prepend">₪</div><input type="number" class="form-control border-0 bg-transparent shadow-none text-center p-0 pis-5" max="9999" aria-label="Spread payments" data-pair="MaximumAmount" value="${from}" required/> </div><span class="col-auto px-5">${lang('coupon_limit')}</span> <div class="col-2 px-0 mx-7"> <input type="number" class="form-control shadow-none bg-light rounded text-center border-0 py-2 px-10" min="0" max="99" aria-label="Spread paments" data-pair="LimitPayments" value="${payments}" required/> </div><span>${lang('payments')}</span> <a class="mis-5 text-danger" role="button" onClick="removeSpreadRow($(this))"> <i class="fas fa-minus-circle"></i> </a></div>`;
    el.before(html);
    $('.btn-save-spread').removeClass('d-none').addClass('d-block')
}

// Spread Payments :: Add Interest rate
function setInterestRate(el, no, amount) {
    var payments = (amount) ? amount : '',
            rate = (no) ? no : '',
            html = `<div class="bsapp-spread-group d-flex align-items-center justify-content-between text-gray-700 mb-15 bsapp-fs-16" data-prop="Interest"> <span>${lang('short_from')}</span> <div class="col-2 mx-10 px-0"> <input type="number" class="form-control text-center border-0 bg-light rounded shadow-none px-10" min="0" aria-label="Set Interest Rate for Payments" data-pair="MaximumAmount"  value="${payments}" required/></div><span class="col-auto px-5">${lang('payments_add')}</span> <div class="input-group align-items-center bg-light border rounded mx-10 py-2 px-10"> <input type="number" class="form-control border-0 bg-transparent shadow-none p-0 pie-5" aria-label="Spread payments" placeholder="0.0" data-pair="LimitPayments" value="${rate}" required/> <div class="input-group-apend">%</div></div><a class="mis-10 text-danger" role="button" onClick="removeSpreadRow($(this))"> <i class="fas fa-minus-circle"></i> </a></div>`;
    el.before(html);
    $('.btn-save-spread').removeClass('d-none').addClass('d-block')
}

// Spread Payments :: Remove Interest/Payout Row
function removeSpreadRow(elem) {
    elem.parent().remove();
    $('.btn-save-spread').removeClass('d-none').addClass('d-block')
}

// Spread Payments :: Update Payment Settings
function updateSpreadPayments($this) {
    var settings = $this.parents('.bsapp-settings-panel'),
            props = settings.find('[data-prop]'),
            prop_arr = [],
            active_prop_name,
            fields = settings.find("[required]")

    if (validateSettingsFields(fields)) {
        apiProps = {
            fun: 'UpdatePayments',
            CompanyNum: $companyNo,
            LimitPayments: '',
            Interest: ''};

        props.each(function () {
            var prop_name = $(this).attr('data-prop'),
                    prop_val,
                    prop_childs = $(this).find('[data-pair]'),
                    curr_row = {};
            if (prop_name == "PeriodicPayments") {
                prop_val = ($(this).is(':checked')) ? 1 : 0;
                apiProps[prop_name] = prop_val;
                apiProps.RegularPayment = !prop_val;
            } else if (prop_name == "MaxPaymentsNumberByValid") {
                prop_val = ($(this).is(':checked')) ? 1 : 0;
                apiProps[prop_name] = prop_val;
            } else if ($(this).is('[type="number"]')) {
                prop_val = $(this).val();
                apiProps[prop_name] = prop_val
            } else if (prop_childs.length) {
                if (prop_name != active_prop_name) {
                    prop_arr = [];
                    active_prop_name = prop_name
                }
                prop_childs.each(function (i) {
                    var child_prop = $(this).attr('data-pair'),
                            child_val = $(this).val();
                    curr_row[child_prop] = child_val;
                    if (i == 1)
                        prop_arr.push(curr_row)
                });
                apiProps[prop_name] = prop_arr
            }
        });

        postApi('storeSettings', apiProps);
        $this.removeClass('d-block').addClass('d-none');
        switchSettingsPanel($this, 'storeSettings-payment-and-billing')
    }
}

// Coupons :: Copy code to clipboard & trigger tooltip
$("body").on("click", ".bsapp-settings-panel a[data-code]", function () {
    var code = $(this).attr('data-code'),
            temp = $("<input>");
    $("body").append(temp);
    temp.val(code).select();
    document.execCommand("copy");
    temp.remove();
    $(this).focus();

    if (!$(this).hasClass('afterClick')) {
        $(this).toggleClass('afterClick');
        $(this).html($('<i/>',{class:'fa fa-check text-success'}));

        setTimeout(function(){
            $(".afterClick").html($('<i/>',{class:'fal fa-copy'}));
            $(".afterClick").toggleClass('afterClick');
        }, 3000);
    }
});

$('.bsapp-settings-panel a[data-code]').tooltip({
    trigger: 'focus',
    placement: 'top'});

// Coupons :: Get coupon list
function getCoupons() {
    apiProps = {
        fun: "getCoupons",
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'renderCoupons')
}

function renderCoupons(data) {
    $('.coupon-loading').hide();
    $('.coupon-list').html('');
    var result = data;
    for (var i = 0; i < result.length; i++) {
        var usedCoupons = result[i].CountLimit,
                toggle = 'checked',
                hidden = '',
                hide_btn = dropdown_hide;

        if (result[i].disabled == 1) {
            toggle = '';
            hidden = 'disabled-style';
            hide_btn = dropdown_unhide;
        }

        if (result[i].Limit != -1) {
            usedCoupons = result[i].CountLimit + '/' + result[i].Limit;
        }

        amount = (result[i].isPercentage == 1) ? amount = parseInt(result[i].Amount) + '%' : parseInt(result[i].Amount) + '₪';
        var markup = `<li class="mb-10 bsapp-coupon-row ${hidden}" data-id="${result[i].id}"> <div class="form-static d-flex align-items-center justify-content-between border rounded text-start m-0 py-7 px-10 bsapp-fs-14"> <span class="item-label d-flex align-items-center font-weight-bolder text-gray-700 bsapp-lh-16">${result[i].Title}</span> <div class="d-flex justify-content-between align-items-center flex-grow-1 pis-20"> <span class="item-count font-weight-normal text-gray-500 bsapp-fs-13 col-3 px-0">${usedCoupons}</span> <a tabindex="0" role="button" class="text-gray-500 bsapp-fs-21" title="Copy Coupon" data-toggle="tooltip" data-code="${result[i].Code}"> <i class="fal fa-copy"></i> </a> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-coupon hide-toggle custom-control-input" id="coupon-id-${result[i].id}" ${toggle}> <label class="custom-control-label" for="coupon-id-${result[i].id}" role="button"></label> </div><span class="item-value font-weight-bolder text-gray-500 bsapp-fs-13">${amount}</span> <div class="dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <!-- edit coupon was here --><li class="mb-6"> <a role="button" class="hide-coupon hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a> </li><li> <a role="button" class="delete-coupon d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-minus-circle fa-fw mx-5"></i> ${lang('delete')} </a> </li></ul> </div></div></div></div></li>`;
        $('.coupon-list').append(markup);
    }
    getAllItems(true)
}

// Coupons :: Global Vars
var coupon,
        coupon_id;

// Coupons :: Delete coupon
$('.bsapp-settings-panel').on('click', '.delete-coupon', function () {
    coupon = $(this).parents('.bsapp-coupon-row');
    coupon.children('.form-static').addClass('opacity-50');
    coupon_id = coupon.data('id');
    apiProps = {
        fun: "deleteCoupon",
        id: coupon_id};
    postApi('storeSettings', apiProps, 'deleteCoupon')
});

function deleteCoupon(result) {
    if (result == "1")
        coupon.remove()
}

// Coupons :: Edit coupon
$('.bsapp-settings-panel').on('click', '.edit-coupon', function () {
    coupon = $(this).parents('.bsapp-coupon-row');
    coupon_id = coupon.data('id');
    apiProps = {
        fun: "getSingleCoupon",
        id: coupon_id};
    clearCouponForm();
    postApi('storeSettings', apiProps, 'editCoupon')
});

function editCoupon(data) {
    $('.bsapp-save-coupon').addClass('bsapp-edit-coupon')
            .attr("data-coupon-id", data.id);
    $('#newCoupon-code').val(data.Code);
    $('#newCoupon-name').val(data.Title);
    $('#newCoupon-amount').val(parseInt(data.Amount));

    if (data.timeLimit == 1 && ((data.StartDate) || (data.EndDate))) {
        $('#newCoupon-time-limit').prop('checked', true);
        $('#newCoupon-start-date').val(data.StartDate);
        $('#newCoupon-end-date').val(data.EndDate);
        $('#newCoupon-start-date').parents('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $('#newCoupon-time-limit').prop('checked', false);
        $('#newCoupon-start-date, #newCoupon-end-date').val('');
        $('#newCoupon-start-date').parents('.toggle-content')
                .removeClass('d-flex')
                .addClass('d-none')
    }

    if ((data.Limit != -1) && (data.Limit != undefined)) {
        $('#newCoupon-quantity-limit').prop('checked', true);
        $('#newCoupon-quantity').val(data.Limit)
                .prop('required', true);
        $('#newCoupon-quantity').parents('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
    } else {
        $('#newCoupon-quantity-limit').prop('checked', false);
        $('#newCoupon-quantity').val('')
                .prop('required', false);
        $('#newCoupon-quantity').parents('.toggle-content')
                .removeClass('d-flex')
                .addClass('d-none')
    }

    if ((data.limitForProducts != null) && (data.limitForProducts != undefined)) {
        $('#newCoupon-products-limit').prop('checked', true);
        $('#newCoupon-products').val(data.limitForProducts)
                .prop('required', true);
        $('#newCoupon-products').parents('.toggle-content')
                .addClass('d-flex')
                .removeClass('d-none')
        $('#newCoupon-products').val(data.limitForProducts.split(',')).trigger('change')
    } else {
        $('#newCoupon-products-limit').prop('checked', false);
        $('#newCoupon-products').val('')
                .prop('required', false);
        $('#newCoupon-products').parents('.toggle-content')
                .removeClass('d-flex')
                .addClass('d-none')
    }

    if (data.isPercentage == 1)
        $('#newCoupon-percentage').click()
    else
        $('#newCoupon-currency').click();

    $('.storeSettings-coupons-new h3').text(lang('edit_coupon_path'));
    switchSettingsPanel($(this), 'storeSettings-coupons-new')
}

// Coupons :: Hide coupon
$('.bsapp-settings-panel').on('click', '.hide-coupon', function () {
    coupon = $(this).parents('.bsapp-coupon-row');
    coupon_id = coupon.data('id');

    var hidden = ($(this).closest('.bsapp-coupon-row').find('input').is(':checked')) ? 0 : 1;

    apiProps = {
        fun: "disableCoupon",
        id: coupon_id,
        disabled: hidden
    };

    postApi('storeSettings', apiProps);
});

// Coupons :: Create New Coupon
$('.bsapp-new-coupon').click(function () {
    $('.storeSettings-coupons-new h3').text(lang('new_coupon_path'));
    $('.bsapp-save-coupon').removeClass('bsapp-edit-coupon');
    clearCouponForm();
    switchSettingsPanel($(this), 'storeSettings-coupons-new')
});

// Coupons :: Update "Limit For Products" Options
function getAllItems(coupon = false) {
    let fun ='getAllItems';
    if(coupon == true){
        fun ='getAllItemsCoupons';
    }
    apiProps = {
        fun: fun,
        CompanyNum: $companyNo};
    postApi('storeSettings', apiProps, 'updateProductsSelect')
}

function updateProductsSelect(data) {
    var products = [];
    for (var i = 0; i < data.length; i++)
        products.push({'id': data[i].id, 'name': data[i].ItemName});
    var productOptions = products.map(product => {
        var product = {
            'id': product.id,
            'html': `<span data-id="${product.id}">${product.name}</span>`,
            'text': product.name,
            'title': product.name};
        return product
    });
    $(".select2--limit-products").select2({
        data: productOptions,
        theme:"bsapp-dropdown",
        escapeMarkup: function (markup) {
            return markup
        },
        templateResult: function (data) {
            return data.html
        },
        templateSelection: function (data) {
            return data.text
        },
        minimumResultsForSearch: -1,
        placeholder: lang('products'),
        // dropdownParent: $('.bsapp-settings-dialog')
    })
}

// Coupons New :: Generate Random Code
function randomCoupon(elem, length) {
    var result = '',
            characters = 'abcdefghijklmnopqrstuvwxyz0123456789',
            charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength)).toUpperCase();
    }
    elem.parent().siblings().find('input').val(result)
}

$("#newCoupon-code").keypress(function(e) {
    // allow only english characters and digits
    var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

    return allowed.indexOf(String.fromCharCode(e.keyCode)) !=-1;
});

// Coupons New :: Trigger Save
$('.bsapp-save-coupon').click(function () {
    var fields = $(this).parents('.bsapp-settings-panel').find("[required]"),
        edited = false;
    if ($(this).hasClass('bsapp-edit-coupon')) {
        edited = true;
    }
    if (validateSettingsFields(fields)) {
        fetchCouponData(edited);
    }
});

// Coupons New :: Validation Trigger
$('.storeSettings-coupons-new input[required]').on('change keyup', function () {
    $(this).closest('.row')
            .find('.bsapp-validation-msg')
            .addClass('d-none')
            .removeClass('d-block');
    validateSettingsFields($(this))
});

// Coupons New :: Global Vars
var editedCoupon = {};

// Coupons New :: Fetch Coupon Data
function fetchCouponData(edited) {
    $('.bsapp-save-coupon').text('Saving...');
    var code = $('#newCoupon-code').val(),
            title = $('#newCoupon-name').val(),
            amount = $('#newCoupon-amount').val(),
            coupon = {
                CompanyNum: $companyNo,
                Code: code,
                Title: title,
                Amount: amount};
    coupon.fun = (edited) ? "editSingleCoupon" : "insertNewCoupon";

    if (edited) {
        coupon.id = $('.bsapp-edit-coupon').attr('data-coupon-id');
        var couponCount = $('.coupon-list').find("[data-id='" + coupon.id + "']")
                .find('.item-count')
                .text()
                .split('/');
        coupon.CountLimit = couponCount[0]
    } else
        coupon.CountLimit = 0;
    if ($('#newCoupon-time-limit').is(':checked')) {
        var endDate = $('#newCoupon-end-date').val(),
            startDate = $('#newCoupon-start-date').val();
        coupon.EndDate = (endDate == '') ? null : endDate;
        coupon.StartDate = (startDate == '') ? null : startDate;
        coupon.timeLimit = 1;
    }

    if ($('#newCoupon-percentage').is(':checked'))
        coupon.isPercentage = $('#newCoupon-percentage').is(':checked') ? 1 : 0;
    if ($('#newCoupon-quantity-limit').is(':checked'))
        coupon.Limit = $('#newCoupon-quantity').val();
    if ($('#newCoupon-products-limit').is(':checked')) {
        coupon.limitForProducts =  $('#newCoupon-products').val()
    }

    postApi('storeSettings', coupon, 'updateCoupons');
    if (edited)
        editedCoupon = coupon
}

// Coupons New :: Clear Coupon Form
function clearCouponForm() {
    $('.bsapp-save-coupon').text(lang('save'));
    $('.storeSettings-coupons-new input, .storeSettings-coupons-new select').val('')
    $('.storeSettings-coupons-new select').val(null).trigger("change");
    if ($('#newCoupon-time-limit').is(':checked')) {
        $('#newCoupon-time-limit').trigger('click');
    }
    if ($('#newCoupon-quantity-limit').is(':checked')) {
        $('#newCoupon-quantity-limit').trigger('click');
    }
    if ($('#newCoupon-products-limit').is(':checked')) {
        $('#newCoupon-products-limit').trigger('click');
    }
}

// Coupons :: Update Coupons List
function updateCoupons(data) {
    var edited = false;
    if (data.edited) {
        edited = true;
        data = editedCoupon
    } else if (data.msg == 'code_exists') {
        $('#newCoupon-code').addClass('border-danger')
            .removeClass('border-0')
            .closest('.row')
            .find('.bsapp-validation-msg')
            .addClass('d-block')
            .removeClass('d-none');
        $('.bsapp-save-coupon').text(lang('save'))
        return;
    }
    var usedCoupons = data.CountLimit,
            amount = parseInt(data.Amount) + '₪';
    if (data.Limit && data.Limit != -1)
        usedCoupons = data.CountLimit + '/' + data.Limit;
    else
        usedCoupons = data.CountLimit;
    if (data.isPercentage == 1)
        amount = parseInt(data.Amount) + '%';
/* EDIT COUPON CODE
                                            <li class="mb-6">
                                                <a role="button" class="edit-coupon d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                                                    <i class="fal fa-edit fa-fw mx-5"></i> `+lang('edit')+`
                                                </a>
                                            </li>
*/
    var markup = `<li class="mb-10 bsapp-coupon-row" data-id="${data.id}"> 
                        <div class="form-static d-flex align-items-center justify-content-between border rounded text-start m-0 py-7 px-10 bsapp-fs-14"> 
                            <span class="item-label d-flex align-items-center font-weight-bolder text-gray-700 bsapp-lh-16">${data.Title}</span> 
                            <div class="d-flex justify-content-between align-items-center flex-grow-1 pis-20"> 
                                <span class="item-count font-weight-normal text-gray-500 bsapp-fs-13">${usedCoupons}</span> 
                                <a tabindex="0" role="button" class="text-gray-500 bsapp-fs-21" title="Copy Coupon" data-toggle="tooltip" data-code="${data.Code}"> 
                                    <i class="fal fa-copy"></i> 
                                </a> 
                                <div class="custom-control custom-switch"> 
                                    <input type="checkbox" class="hide-coupon hide-toggle custom-control-input" id="coupon-id-${data.id}" checked> 
                                    <label class="custom-control-label" for="coupon-id-${data.id}" role="button"></label> 
                                </div>
                                <span class="item-value font-weight-bolder text-gray-500 bsapp-fs-13">${amount}</span> 
                                <div class="dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> 
                                    <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> 
                                    <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> 
                                        <ul class="list-unstyled m-0 p-0"> 
                                        <!-- edit coupon was here -->
                                            <li class="mb-6"> 
                                                <a role="button" class="hide-coupon hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                                                    <i class="fal fa-eye-slash fa-fw mx-5"></i> <span>`+lang('hide')+`</span>
                                                </a> 
                                            </li>
                                            <li> 
                                                <a role="button" class="delete-coupon d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> 
                                                <i class="fal fa-minus-circle fa-fw mx-5"></i> `+lang('a_remove_single')+` </a> 
                                            </li>
                                        </ul> 
                                    </div>
                                </div>
                            </div>
                        </div>
                  </li>`;

    if (!edited)
        $('.coupon-list').append(markup)
    else
        $('.coupon-list').find(("[data-id='" + data.id + "']")).html(markup);
    editedCoupon = {}
    clearCouponForm();
    switchSettingsPanel($('.bsapp-save-coupon'), 'storeSettings-coupons')

}

// Coupons New :: Time Limit
$('#newCoupon-time-limit').click(function () {
    var required = ($(this).is(':checked')) ? true : false;
    $('#newCoupon-start-date, #newCoupon-end-date').prop('required', required)
});

// Coupons New :: Quantity Limit
$('#newCoupon-quantity-limit').click(function () {
    var required = ($(this).is(':checked')) ? true : false;
    $('#newCoupon-quantity').prop('required', required)
});

// Coupons New :: Limit For Products
$('#newCoupon-products-limit').click(function () {
    var required = ($(this).is(':checked')) ? true : false;
    $('#newCoupon-products').prop('required', required)
});
