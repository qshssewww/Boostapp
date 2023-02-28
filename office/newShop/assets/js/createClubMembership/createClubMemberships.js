
//todo inside
(function ($) {
    $.fn.serializeAllArray = function () {
        let obj = {};
        $(this).each(function () {
            if(this.type === 'checkbox') {
                if(!$(this).prop('checked')) {
                    // console.log('passs');
                } else if(obj[this.name]) {
                    obj[this.name].push(this.value);
                } else {
                    obj[this.name] = [this.value];
                }
            } else if($(this).is("select")) {
                obj[this.name] = $(this).find('option:selected').val();
            }else {
                obj[this.name] = $(this).val();
            }
        });
        return obj;
    }
})(jQuery);

//Do not allow enter in form outside of textarea
jQuery(function($) { // DOM ready
    $('form.add-club-memberships').on('keydown', function(ev) {
        if (ev.key === "Enter" && !$(ev.target).is('textarea')) {
            ev.preventDefault();
            console.log("ENTER-KEY PREVENTED ON NON-TEXTAREA ELEMENTS");
        }
    });

});



let createClubMemberships = {
    mainElem: null,
    isTrial: false,
    constList : {
        MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP :  '1',
        MEMBERSHIP_TYPE_CARD : '2',
        MEMBERSHIP_TRIAL : '3',
        MEMBERSHIP_TYPE_STANDING_ORDER : '5',
        DEPARTMENT_TYPE_DEFAULT : '0',
        EXPIRE_CYCLE_DEFAULT : '1-3',
        EXPIRE_CARD_DEFAULT : '0-1',
        RESTRICTION_ENTRIES_DEFAULT : 10,
    },
    //Defines constants within the registrationRestrictions object According to constList
    /********************* helper function *********************/
    //add constants to this object
    setConst: function () {
        Object.keys(this.constList).forEach(function(constName) {
            Object.defineProperty(createClubMemberships, constName,
                {
                    value:	createClubMemberships.constList[constName],
                    writable: false,
                    enumerable: true,
                    configurable: true
                }
            );
        });
    },
    //change the value to this format- X.yy
    setTwoNumberDecimal: function (elem) {
        elem.value = parseFloat(elem.value).toFixed(2);
    },
    //Checks all inputs nested inside `elem` if valid or not
    checkValidation: function (elem){
        let valid = true;
        let inputs =$(elem).find(`:input.js-post-value`);
        $(inputs).each(function () {
            if(!this.checkValidity()){
                this.scrollIntoView();
                $(this).addClass('is-invalid').parent('.is-invalid-container').addClass('is-invalid');
                //on change the invalid input remove style
                $(this).one('change', function (e)
                {
                    $(e.target).removeClass('is-invalid')
                        .parent('.is-invalid-container').removeClass('is-invalid');
                });
                valid = false
                return false;
            }
        })
        if(valid) {
            $(elem).find('is-invalid').removeClass('is-invalid');
        }
        return valid;
    },
    /********************* Global function *********************/
    init: function (){
        if(!this.mainElem) {
            this.setConst();
            this.mainElem = $('.popupWrapper#create-club-memberships-popup:first');
            externalPurchase.init();
            registrationRestrictions.init();
        }
        shopMaim.select2MembershipType($(this.mainElem).find('.js-select2-shop-new.membership'));

        const basicSection = $(this.mainElem).find('#basic-details-section');
        basicSection.find('.membership-name').val('');
        const blocksList = $(this.mainElem).find('#registration-packages-section #registration-packages-list');
        blocksList.find('.package-row-block:not(:first)').remove();
        blocksList.find('.package-row-block select option').removeClass('d-none').prop('disabled', false);
        blocksList.find('.package-row-block .remove-button').removeClass('d-flex').addClass('d-none').prop('disabled', true);
        blocksList.find('.package-row-block .department-type')
            .val(this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP).trigger('change');

        //set the first class-type-limits-input to all classes
        registrationRestrictions.mainElem.find('#tab-content .registration-restrictions-option-section select#class-type-limits-input-1')
                .parent().find('[type="checkbox"][value="_all_class"]').prop('checked', true).change();
        type = ''; //todo
    },
    //show the popup
    openPopup: function(isTrial=false) {
        this.isTrial = isTrial;
        this.setEmptyForm();
        closePopup("selectPopup");
        openPopup("create-club-memberships-popup");
    },
    //Requests the data of the club members from db and puts them in the popup
    showEditClubMembershipsData: function(elem) {
        const id =  $(elem).attr('data-id');
        showLoader();
        $.ajax({
            url: "/office/newShop/ajax/club-memberships.php",
            type: "post",
            data: {
                fun: 'GetClubMembershipsData',
                id: id
            },
            success: function (response) {
                hideLoader();
                try {
                    if (!shopMaim.errorChecking(response.Status) || response.data.items.length === 0) {
                        return;
                    }
                    //Check if an experimental subscriber is editing
                    if(response.data.items[0].Department === createClubMemberships.MEMBERSHIP_TRIAL){
                        createClubMemberships.isTrial = true;
                    }
                } catch (e){
                    return;
                }
                try {
                    createClubMemberships.isTrial = response.data.items[0].Department === '3';
                } catch (e) {
                    createClubMemberships.isTrial = false;
                }
                createClubMemberships.setEmptyForm();
                createClubMemberships.setDataToForm(response.data);
                openPopup("create-club-memberships-popup");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoader();
                alert(textStatus);
                console.log(textStatus, errorThrown);
            },
        });
    },
    //Reset of the form Adding Club Memberships
    setEmptyForm: function () {
        const form = this.mainElem.find('form.add-club-memberships:first');

        /**** main tab ***/
        const titleText = this.isTrial ? lang('new_trial_membership_title') : lang('new_membership_title');
        form.find('.form-title:first').text(titleText);
        const mainTab = form.find('.sub-tab-body-main:first');
        mainTab.find('input.club-memberships-id').val('');
        mainTab.find(':input.membership-name').val('').trigger('change'); //name = empty

        const defaultDepartmentValue = this.isTrial ? this.MEMBERSHIP_TYPE_CARD : this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP;
        const listPackages = mainTab.find('#registration-packages-section #registration-packages-list');
        const restrictionByEntriesElem = listPackages.find(`select.restriction-by-entries`);

        //remove all package blocks only first one stay with default value
        listPackages.find('.package-row-block .remove-button a')
            .each(function(index) {
                if (index > 0) {
                    $(this).click()
                } else {
                    $(this).closest('.package-row-block').attr('data-id',0)
                        .find(':input.department-type')
                        .val(defaultDepartmentValue).trigger('change');
                    $(this).closest('.package-row-block').find(':input.item-id').val(-1);
                }
            });
        mainTab.find('.item-price.js-post-value').val('').trigger('change');

        //trial
        if(this.isTrial) {
            //only card type visible
            listPackages.find(`select.department-type option:not([value=${this.MEMBERSHIP_TYPE_CARD}])`).addClass('d-none').prop('disabled', true);
            restrictionByEntriesElem.val(1).trigger('change')
                .find(`option`).each(function (){
                this.value > 5 ? $(this).addClass('d-none').prop('disabled', true) : '';
            })
            listPackages.find('select.department-type').addClass('bsapp-js-disabled-o');

        } else {
            const departmentTypeSelect = listPackages.find('select.department-type').removeClass('bsapp-js-disabled-o');
            departmentTypeSelect.find(`option:not([value=${this.MEMBERSHIP_TYPE_CARD}])`).removeClass('d-none').prop('disabled', false);
            restrictionByEntriesElem.find(`option`).removeClass('d-none').prop('disabled', false);
        }

        /**** external purchase tab ***/
        externalPurchase.resetsTab();
        registrationRestrictions.resetsTab();
        this.setTitleForDisplayInformation();
    },
    //add the data into the form
    setDataToForm: function (data) {
        //change title to edit
        const titleText = this.isTrial ? lang('edit_trial_membership_title') : lang('edit_membership_title');
        this.mainElem.find('form.add-club-memberships:first .form-title:first').text(titleText);

        /***** clubMemberships ****/
        const clubMembershipsSection = this.mainElem.find('.js-tab-home:first #basic-details-section');
        const clubMemberships = data.clubMemberships;

        if(clubMemberships.BrandsId === null) {
            clubMemberships.BrandsId = 'BA999';
        }
        //Puts in the basic details (clubMemberships)
        for (const [key, value] of Object.entries(clubMemberships)) {
            clubMembershipsSection.find(`:input[name=${key}]`).val(value).trigger('change');
        }
        clubMembershipsSection.find(':input').removeClass('changed')
            .change(function (){$(this).addClass('changed');});

        /***** items ****/
        const items = data.items;
        const itemsSection = $(this.mainElem).find('.js-tab-home:first #registration-packages-section');
        //Puts in the blocks item details (price , Vaild, BalanceClass)
        if(items.length > 0) {
            for (let [index, item] of items.entries()) {
                //If a standing order is defined defines the department as such
                item['Department'] = item['Payment']  === '2' ? this.MEMBERSHIP_TYPE_STANDING_ORDER : item['Department'];
                delete item.Payment;
                //if is trial
                item['Department'] = item['Department']  === this.MEMBERSHIP_TRIAL ? this.MEMBERSHIP_TYPE_CARD : item['Department'];
                //Defining validity by format in front
                item['Vaild_Type'] = item['Vaild'] === '0' ? '1' : item['Vaild_Type'];
                item['validityMembership'] = item['Vaild'] + '-' + item['Vaild_Type'];
                if(index > 0) {
                    createClubMemberships.addPackage(itemsSection.find('#registration-packages-header:first'));
                }
                this.setItemsDataBlock(itemsSection.find(`.package-row-block:last`), item);
            }
        }
        itemsSection.find(`:input`).removeClass('changed')
            .change(function (){$(this).addClass('changed');});


        /***** item limits ****/
        const itemsLimitSection = $(this.mainElem).find('.external-purchase-tab:first');
        itemsLimitSection.find(':input.external-purchase').removeClass('changed')
            .change(function (){$(this).addClass('changed');});

        const itemsLimit = data.itemsLimit;
        const itemsLimitBlocksData = itemsLimit.blocksData;
        delete itemsLimit.blocksData;

        //Puts in the item details that similar to all item in the membership club (image, comment, start time..)
        for (const [key, value] of Object.entries(itemsLimit)) {
            itemsLimitSection.find(`:input[name=${key}]`).val(value).trigger('change');
        }
        itemsLimitSection.find('#membership-club-information').trigger('keyup');
        if(itemsLimit.Image) {
            itemsLimitSection.find('.image-section:first').removeClass('d-none').prop('disabled', false)
                .find('#selected-image').html(
                    '<img class="w-100 h-100" style="object-fit:cover;object-position:center;border-radius:8px;" id="shopImage" src="' + itemsLimit.Image + '"/>'
                );
            shopMaim.hideFlex(itemsLimitSection.find('.add-picture-btn'));
            $(itemsLimitSection).find('.image-section').removeClass('d-none').prop('disabled', false);
        }

        const itemLimitIdElem = itemsLimitSection.find('#purchase-restrictions-section:first input.itemLimitId');
        if(itemsLimitBlocksData['id']) {
            itemLimitIdElem.addClass('js-post-value').val(itemsLimitBlocksData['id']);
        } else {
            itemLimitIdElem.removeClass('js-post-value').val(-1);
        }
        delete itemsLimitBlocksData.id;

        itemsLimitSection.find(`.app-view-settings > :not(#purchase-restrictions-section) :input`)
            .removeClass('changed')
            .change(function (){$(this).addClass('changed');});

        let isFirst = true;
        //Puts in the blocks the item limits details
        for (const [key, value] of Object.entries(itemsLimitBlocksData)) {
            if(value == null)
                continue;
            switch (key) {
                case 'maxPurchase':
                    createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, value, externalPurchase.QUANTITY_LIMIT,isFirst);
                    const selectElem = itemsLimitSection.find(`.purchase-restriction-row-block:last :input[name=${key}]`);
                    createClubMemberships.checkOptionAddSetSelected(selectElem,value, value + ` ${lang('for_each_client')}`)

                    break;
                case 'gender':
                    createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, value, externalPurchase.GENDER_RESTRICTION,isFirst);
                    break;
                case 'rank':
                    createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, JSON.parse(value) , externalPurchase.TAG_RESTRICTIONS,isFirst);
                    break;
                case 'startAge':
                    if(itemsLimitBlocksData['endAge']) {
                        createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, value, externalPurchase.AGE_RESTRICTION,isFirst);
                        itemsLimitSection.find('.purchase-restriction-row-block:last :input[name=endAge]')
                            .val(itemsLimitBlocksData['endAge']).trigger('change');
                    }
                    break;
                case 'seniority':
                    createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, value, externalPurchase.SENIORITY_RESTRICTION,isFirst);
                    break;
                case 'customerStatus':
                    createClubMemberships.addNewLimitBlockWithData(itemsLimitSection, key, value, externalPurchase.CLIENT_STATUS_RESTRICTION,isFirst);
                    break;
                default:
                    continue;
            }
            isFirst = false;
        }

        /***** item roles ****/
        const rolesOptionTabs = $(registrationRestrictions.mainElem).find('#registration-option-tabs-section');
        const rolesTabContent = $(registrationRestrictions.mainElem).find('#tab-content:first');
        const itemsRoles = data.itemsRoles;

        if(itemsRoles && itemsRoles.length > 0) {
            registrationRestrictions.resetAllClassTypeSelect();
            let newOption = true, optionNum = 0, lastClass = '';
            itemsRoles.forEach( function (itemsRole)  {
                //if class change need open new option
                newOption = lastClass != itemsRole.Class;
                lastClass = itemsRole.Class;
                if (newOption) {
                    optionNum++;
                }
                rolesOptionTabs.find(`#registration-option-tabs-list li[data-id=${optionNum}]:first a:first`).click();
                const tab = rolesTabContent.find(`.tab-body-option[data-option=${optionNum}]`);
                if (itemsRole.Group === 'Class') {
                    const classArray = registrationRestrictions.fromStringOfClassToArrayFrontFormat(itemsRole.Class);
                    classArray.forEach(function (value) {
                        $(tab).find('.bsappMultiSelect.class-type-limits').closest('.bsapp--sel')
                            .find(`.bsapp--sel__box input[type=checkbox][value=${value}]:first`).click();
                    });
                } else if (itemsRole.Group === 'Item') {
                    const valueObj = registrationRestrictions.fromDbRowToFrontDataObj(itemsRole);
                    registrationRestrictions.addLimitBasedAvailabilityFromObject(valueObj);
                } else {
                    //add block
                    const noLimitInput = tab.find('#default-membership-club-limit-block .no-limit-input');
                    if (noLimitInput.val() == 1) {
                        registrationRestrictions.createFirstLimitBlock(noLimitInput);
                    } else {
                        registrationRestrictions.addLimit(tab.find('#membership-club-limit-section #membership-club-limit-header a:first'));
                    }
                    //set the data inside the block
                    const lastBlock = tab.find('#membership-club-limit-section #membership-club-limit-blocks-list .membership-club-limit-row-block:last')
                    const valueObj = registrationRestrictions.fromDbRowToFrontDataObj(itemsRole);
                    for (const [key, value] of Object.entries(valueObj)) {
                        const selectElem = lastBlock.find(`:input.${key}`);
                        if(key === 'max-number-entries') {
                            createClubMemberships.checkOptionAddSetSelected(selectElem, value, `${lang('up_to')} ` + value);
                        } else {
                            selectElem.val(value).trigger('change');
                        }
                    }
                }
            });
            rolesOptionTabs.find(`#registration-option-tabs-list li[data-id=${1}]:first a:first`).click()
        }
        this.setTitleForDisplayInformation();
    },
    //Enter the item data into the blocks
    setItemsDataBlock: function(block, data) {
        if(data.id) {
            block.find(`:input[name=id]`).val(data.id).trigger('change');
        }
        if(data.Department) {
            block.find(`:input[name=Department]`).val(data.Department).trigger('change');
        }
        if(data.validityMembership) {
            let selectElem = block.find(`.expire-elem:not(.d-none) :input[name=validityMembership]`);
            this.checkOptionAddSetSelected(selectElem, data.validityMembership,
                this.validityMembershipToText(data.validityMembership));
        }
        if(data.BalanceClass) {
            let selectElem = block.find(`:input[name=BalanceClass]`);
            this.checkOptionAddSetSelected(selectElem, data.BalanceClass, data.BalanceClass)
        }
        if(data.ItemPrice) {
            block.find(`:input[name=ItemPrice]`).val(data.ItemPrice).trigger('change');
        }
    },
    //Submits the form (editing or creating a new one)
    submitForm:function (formElem, e){
        e.preventDefault();
        const mainTab = this.mainElem.find('.sub-tab-body-main:first');
        const clubMembershipsId = mainTab.find('input.club-memberships-id').val();
        if(clubMembershipsId) {
            this.addNewClubMembership(true);
        } else {
            this.addNewClubMembership();
        }
    },

    validityMembershipToText: function (validityMembership, factor = 0) {
        try {
            const words = validityMembership.split('-');
            let text = words[0];
            switch (parseInt(words[1]) + factor){
                case -1 :
                    return text + ` ${lang('minutes')}`;
                case 0:
                    return text + ` ${lang('hours')}`;
                case 1:
                    return text + ` ${lang('days')}`;
                case 2:
                    return text + ` ${lang('weeks')}`;
                case 3:
                    return text + ` ${lang('months')}`;
                default:
                    return '';
            }
        } catch (e) {
            return '';
        }

    },

    checkOptionAddSetSelected: function (elem, value ,text) {
        if (elem.find(`option[value=${value}]`).length === 0) {
            this.addOptionToSelect(elem, value ,text)
        }
        elem.val(value).trigger('change');
    },

    addOptionToSelect: function (elem, value ,text) {
        $(elem).append($('<option>', {
            value: value,
            text: text
        }));
    },

    //return obj of base club membership data or false if not valid
    getBaseClubMembershipData: function (isEditMode= false){
        const clubMembershipsSection = this.mainElem.find('.js-tab-home:first #basic-details-section');
        if(this.checkValidation(clubMembershipsSection)) {
            return clubMembershipsSection.find(`:input.js-post-value${isEditMode?'.changed':''}`)
                .serializeAllArray();
        }
        return false;
    },

    getItemData: function (elem, getAllBlocks = true){
        let newBlockData = elem.find(`:input.js-post-value`).serializeAllArray();
        // create item name
        let departmentText = this.isTrial ? lang('a_trial') :elem.find(':input.department-type option:selected').text();
        let balanceCountText = newBlockData.BalanceClass ? `(${newBlockData.BalanceClass})` : '';
        let validText =''
        if(newBlockData.Department !== this.MEMBERSHIP_TYPE_STANDING_ORDER &&
            newBlockData.validityMembership !== "0-1"){
            validText = ` - ${elem.find('.expire-elem :input:visible option:selected').attr('data-text')}`
        }
        let clubMemberShipName = this.mainElem.find('#basic-details-section :input.membership-name').val();
        newBlockData.ItemName = `${clubMemberShipName} - ${departmentText}${balanceCountText}${validText}`;
        //If department is standing order add attribute Payment
        if(newBlockData.Department ===  this.MEMBERSHIP_TYPE_STANDING_ORDER) {
            newBlockData.Department = this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP;
            newBlockData.Payment = 2;
            newBlockData.Vaild = 1;
            newBlockData.Vaild_Type = 3;
        }
        if(this.isTrial){
            newBlockData.Department = this.MEMBERSHIP_TRIAL;
        }
        //fix data format
        if(newBlockData.validityMembership) {
            const words = newBlockData.validityMembership.split('-');
            newBlockData.Vaild = words[0];
            newBlockData.Vaild_Type = words[1];
            delete newBlockData.validityMembership;
        }
        if(!getAllBlocks) {
            const changeInput = elem.find(`:input.js-post-value${!getAllBlocks?'.changed':''}`);
            if(!changeInput.hasClass('validity-membership')) {
                delete newBlockData.Vaild;
                delete newBlockData.Vaild_Type;
            }
            if(!changeInput.hasClass('restriction-by-entries')) {
                delete newBlockData.BalanceClass;
            }
            if(!changeInput.hasClass('item-price')) {
                delete newBlockData.ItemPrice;
            } else {
                if(changeInput.length === 1) {
                }
                delete newBlockData.ItemName;
            }
            delete newBlockData.Department
        }
        return newBlockData;

    },
    getAllItemData: function (itemExternalData, getAllBlocks=true){
        const itemsListSection = this.mainElem.find('.js-tab-home:first #registration-packages-section #registration-packages-list');
        let itemList = [];
        if(this.checkValidation(itemsListSection)) {
            let allBlocks;
            if(getAllBlocks){
               allBlocks = itemsListSection.find('.package-row-block');
            } else {
                allBlocks = itemsListSection.find('.package-row-block :input.js-post-value.changed').closest('.package-row-block')
            }
            //A loop on all the blocks and inserting their data into the array
            allBlocks.each(function () {
                let newBlockData = createClubMemberships.getItemData($(this),getAllBlocks)
                newBlockData = Object.assign(newBlockData, itemExternalData);
                itemList.push(newBlockData);
            });
            try {
                let valueArr = itemList.map(function(item){ return `${item.BalanceClass}-${item.Vaild}-${item.Vaild_Type}`});
                let indexNotValid = -1;
                let isDuplicate = valueArr.some(function(item, idx){
                    if(!item.includes("undefined") && valueArr.indexOf(item) != idx) {
                        indexNotValid = idx;
                    }
                    return !item.includes("undefined") && valueArr.indexOf(item) != idx;
                });
                if(isDuplicate) {
                    $(allBlocks[indexNotValid]).find('.restriction-by-entries').val('');
                    $(allBlocks[indexNotValid]).find('.validity-membership').val('');
                    this.checkValidation(itemsListSection)
                    console.log('not valid items..'); //todo
                    return false;
                }
            } catch (e) {
                return itemList;
            }
            return itemList;
        } else {
            console.log('not valid items..'); //todo
            return false;
        }
    },


    isEmptyClassTypeSelect: function () {
        let roleClassLimitEmpty = true;
        $('#registration-restrictions-section .bsappMultiSelect.class-type-limits').each(function() {
            if($(this).val().length > 0) {
                roleClassLimitEmpty = false;
            }
        });
        return roleClassLimitEmpty;
    },

    addNewClubMembership: function (isEditMode=false){
        if(this.isEmptyClassTypeSelect()) {
            shopMaim.switchTab(this.mainElem.find('#dynamic-blocks-details-section .item-roles-information-display a.stretched-link:first'));
            return;
        }
        let data = {};
        /***** clubMemberships ****/
        data.clubMemberships = this.getBaseClubMembershipData(isEditMode);
        if(!data.clubMemberships) {
            console.log('not valid clubMemberships'); //todo
            return;
        }
        /***** items ****/
        //Preparation of the basic information about this item (data from fields in external purchase tab)
        let itemExternalData = externalPurchase.getBaseItemData(isEditMode);
        if(!itemExternalData) {
            console.log('not valid item'); //todo
            return;
        }
        if(isEditMode) {
            data.clubMembershipId = this.mainElem.find('#basic-details-section .club-memberships-id').val();
            if(data.clubMemberships.BrandsId) {
                itemExternalData.Brands = data.clubMemberships.BrandsId;
            }
            if(data.clubMemberships.MemberShipTypeId) {
                itemExternalData.MemberShip = data.clubMemberships.MemberShipTypeId;
            }
            if(data.clubMemberships.ClubMemberShipName) {
                itemExternalData.ItemPrefixName = data.clubMemberships.ClubMemberShipName;
            }
            data.itemsGeneralData = itemExternalData; //all items = basic data for all items in the club membership
            itemExternalData = {};
        }

        const itemsListSection = this.mainElem.find('.js-tab-home:first #registration-packages-section #registration-packages-list');
        const isCriticalChange = !isEditMode  || !!itemsListSection.find(`.package-row-block :input.js-post-value.department-type.changed`).length
        data.items = this.getAllItemData(itemExternalData, isCriticalChange);
        if(isCriticalChange) {
            data.updateAllItems = true;
        }
        if(!data.items) {
            console.log('not valid items..'); //todo
            return;
        }

        /***** items limit****/
        data.itemsLimit = externalPurchase.getItemLimitData();
        /***** items roles****/
        data.itemsRoles = registrationRestrictions.geItemRoleData()
        this.postClubMembership(data, isEditMode)
    },
    //post club membership data to the db
    postClubMembership: function (data, isEditMode){
        const fun = isEditMode ? 'EditClubMemberships' : 'AddClubMemberships'
        showLoader();
        $.ajax({
            url: "/office/newShop/ajax/club-memberships.php",
            type: "post",
            data: {
                fun: fun,
                data: data
            },
            success: function (response) {
                hideLoader();
                if (!shopMaim.errorChecking(response.Status)) {
                    return;
                }
                const text = lang('action_done');
                $.notify(
                    {
                        icon: 'fas fa-check-circle',
                        message: text,
                    },{
                        type: 'success',
                        z_index: '99999999',
                    });
                loadClubMemberships();
                reloadAfterInsert('#shopLinksTable', initCallbackLinks);
                closePopup("create-club-memberships-popup");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoader();
                alert(textStatus);
                console.log(textStatus, errorThrown);
            },
        });
    },
    //create a block of item limit with the required data
    addNewLimitBlockWithData: function (itemsLimitSection, key, value, status, isFirst=false) {
        if(isFirst) {
            //create first block
            externalPurchase.createFirstPurchaseRestrictions(itemsLimitSection.find('#default-purchase-restriction-block:first a:first'));
        } else {
            // add block
            externalPurchase.addPurchaseRestriction(itemsLimitSection.find('#custom-purchase-restriction-blocks:first #purchase-restrictions-header'));
        }
        const lastBlock =  itemsLimitSection.find('.purchase-restriction-row-block:last');
        lastBlock.find(':input.purchase-restriction-type').val(status).trigger('change');
        lastBlock.find(`:input[name=${key}]`).val(value).trigger('change');
    },
    //Created the title of the sub-tabs
    setTitleForDisplayInformation(setItemLimit = true, setItemRoles=true) {
        if(setItemLimit) {
            const subTextExternalPurchase = this.mainElem.find('#dynamic-blocks-details-section .external-purchase-information-display .js-preview-sub-title');
            subTextExternalPurchase.text(externalPurchase.getSubTitleForDisplayInformation());
        }
        if(setItemRoles) {
            const infoItemRoleSection = this.mainElem.find('#dynamic-blocks-details-section .item-roles-information-display');
            const subTextItemRoles = infoItemRoleSection.find('.js-preview-sub-title');
            subTextItemRoles.text(registrationRestrictions.getSubTitleForDisplayInformation());
            const titleTextItemRoles = infoItemRoleSection.find('.js-preview-title');
            titleTextItemRoles.text(registrationRestrictions.getTitleForDisplayInformation());
        }
    },

    /********************* Registration Package *********************/
    // Changing the subscription type changes the block view and subscription restriction options
    departmentTypeChange: function (elem) {
        const block = $(elem).closest('.package-block');
        //show all input
        const expireElem = block.find('.expire-elem').removeClass('d-none').prop('disabled', false);
        $(expireElem).find('select').prop('required', true).addClass('js-post-value');
        const entriesElem = block.find('.entries-item').removeClass('d-none').prop('disabled', false);
        $(entriesElem).find('select').prop('required', true).addClass('js-post-value');

        // show standing order option if not selected yet
        let membershipList = (block).closest('#registration-packages-list')
            .find(`.package-block .department-type`);
        if(!this.isTrial && membershipList.find(`option[value=${this.MEMBERSHIP_TYPE_STANDING_ORDER}]:selected`).length === 0) {
            membershipList.find(`option[value=${this.MEMBERSHIP_TYPE_STANDING_ORDER}]`).removeClass('d-none').prop('disabled', false);
        } else {
            membershipList.find(`option[value=${this.MEMBERSHIP_TYPE_STANDING_ORDER}]`).addClass('d-none').prop('disabled', true);
        }

        //By the selected subscription type displays the relevant fields in the block
        switch (elem.value) {
            case this.MEMBERSHIP_TYPE_CARD:
                block.find('.expire-elem-cycle').addClass('d-none').prop('disabled', true)
                    .find('select').prop('required', false).removeClass('js-post-value').val('').trigger('change');
                const expireCardElem = block.find('.expire-elem-card select');
                if(expireCardElem.find(`option.d-none[value=${this.EXPIRE_CARD_DEFAULT}]`).length) {
                    expireCardElem.addClass('js-post-value')
                        .val(expireCardElem.find('option:not(.d-none):eq(0)').val()).trigger('change')
                } else {
                    expireCardElem.addClass('js-post-value')
                        .val(this.EXPIRE_CARD_DEFAULT).trigger('change')
                }
                break
            case this.MEMBERSHIP_TYPE_STANDING_ORDER:
                $(expireElem).addClass('d-none').prop('disabled', true)
                    .each(function (){
                        $(this).find('select').prop('required', false).removeClass('js-post-value').val('').trigger('change');
                    });
                $(elem).find(`option[value=${this.MEMBERSHIP_TYPE_STANDING_ORDER}]`).addClass('d-none').prop('disabled', true);
                $(entriesElem).addClass('d-none').prop('disabled', true)
                    .find('select').prop('required', false).val('').trigger('change');
                break;
            case this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP:
                $(entriesElem).addClass('d-none').prop('disabled', true).find('select').prop('required', false).removeClass('js-post-value').val('').trigger('change');
                block.find('.expire-elem-card').addClass('d-none').prop('disabled', true)
                    .find('select').prop('required', false).removeClass('js-post-value').val('').trigger('change');
                //chose the first option that still available
                let cycleMember = block.find('.expire-elem-cycle select').addClass('js-post-value');
                if($(cycleMember).find(`option.d-none[value=${this.EXPIRE_CYCLE_DEFAULT}]`).length) {
                    $(cycleMember).val($(cycleMember).find('option:not(.d-none):eq(0)').val()).trigger('change');
                } else {
                    $(cycleMember).val(this.EXPIRE_CYCLE_DEFAULT).trigger('change');
                }
        }
    },
    // When the validity of a cycle subscription changes
    validityMembershipCycleChange: function (elem) {
        let oldValue = $(elem).attr("data-old-value");
        let newValue = $(elem).val();
        const expireCycleElem = $(elem).closest('#registration-packages-list').find('.expire-elem-cycle');
        // add to option old selected
        $(expireCycleElem).find(`select.validity-membership option[value=${oldValue}]`).removeClass('d-none').prop('disabled', false);
        // remove the new selected option
        $(expireCycleElem).find(`select.validity-membership option[value=${newValue}]`).addClass('d-none').prop('disabled', true);
        //update old value
        $(elem).attr("data-old-value", newValue);
    },
    // When the validity of a card subscription changes
    validityMembershipCardChange: function (elem) {
        let newValue = $(elem).val();
        let oldValue = $(elem).attr("data-old-value");
        let entries = $(elem).closest('.row').find('select.restriction-by-entries option:selected').val();
        if(newValue) {
            const list = $(elem).closest('#registration-packages-list');
            list.find(`.expire-elem-card select option[value=${oldValue}]`).each(function (){
                // remove the new selected option
                $(this).removeClass('d-none').prop('disabled', false)
                    .each(function () {
                            $(this).closest('.row').find(`select.restriction-by-entries option[value=${entries}]`)
                                .removeClass('d-none').prop('disabled', false);
                        });
            });
            let entriesSelect = $(elem).closest('.row').find(`select.restriction-by-entries`);
            if(!this.isTrial) {
                entriesSelect.val('').find('option.d-none').removeClass('d-none').prop('disabled', false);
            } else {
                entriesSelect.val('').find('option.d-none').each(function () {
                    this.value < 6 ? $(this).removeClass('d-none').prop('disabled', false) : '';
                });
            }
            list.find(`.expire-elem-card select option[value=${newValue}]:selected`)
                .each(function () {
                    let entries = $(this).closest('.row').find('select.restriction-by-entries option:selected').val();
                    entriesSelect.find(`option[value=${entries}]`).addClass('d-none').prop('disabled', true);
                });
            if(entriesSelect.find('option:not(.d-none)').length > 0){
                if (this.isTrial || entriesSelect.find(`option.d-none[value=${this.RESTRICTION_ENTRIES_DEFAULT}]`).length) {
                    entriesSelect.val(entriesSelect.find('option:not(.d-none):eq(0)').val()).trigger('change');
                } else {
                    entriesSelect.val(this.RESTRICTION_ENTRIES_DEFAULT).trigger('change');
                }
            } else {
                $(elem).val($(elem).find('option:not(.d-none):eq(0)').val()).trigger('change');
            }
            if(entriesSelect.find('option:not(.d-none)').length === 0) {
                list.find(`.expire-elem-card select`).each(function () {
                    // add to option old selected
                    $(this).find(`option[value=${newValue}]`).addClass('d-none').prop('disabled', true);
                });
            }

            $(elem).attr("data-old-value", newValue);
        }
    },
    //When the amount of entries changes, check all the blocks with the same validity and change their options
    restrictionByEntriesChange: function (elem) {
        let oldValue = $(elem).attr("data-old-value");
        let newValue = $(elem).find('option:selected').val();
        if(newValue) {
            const selectedList = $(elem).closest('#registration-packages-list');
            const expireVal = $(elem).closest('.row').find('.expire-elem-card select option:selected').val();
            selectedList.find(`.expire-elem-card select option[value=${expireVal}]:selected`)
                .each(function () {
                    let entriesSelect = $(this).closest('.row').find('select.restriction-by-entries');
                    // remove the new selected option
                    entriesSelect.find(`option[value=${oldValue}].d-none`).removeClass('d-none').prop('disabled', false);
                    // add to option old selected
                    entriesSelect.find(`option[value=${newValue}]`).addClass('d-none').prop('disabled', true);
                });
            //update old value
            $(elem).attr("data-old-value", newValue);
        }
    },
    // Add a subscription type block
    addPackage: function (elem) {
        let valid = true;
        //Validation checks before adding package
        const packagesList = $(elem).closest('#registration-packages-section')
            .find('#registration-packages-list:first')
        let inputs = packagesList.find(':input');
        inputs.each(function () {
            if(!this.checkValidity()){
                this.scrollIntoView();
                $(this).addClass('is-invalid');
                //listener to remove is-invalid
                this.addEventListener("input",
                        e => e.target.classList.remove("is-invalid"),
                    { once: true });
                valid = false
                return false;
            }
        })
        if(valid) {
            packagesList.find(':input.is-invalid').removeClass('is-invalid');
            //clone last block and add it to dom
            const lastBlock = packagesList.find('.package-row-block:last').clone();
            packagesList.append(lastBlock);
            //show remove button if hidden
            packagesList.find('.remove-button.d-none').removeClass('d-none').addClass('d-flex');
            //scroll to new block
            let newBlock = $(packagesList).find('.package-row-block:last');
            newBlock[0].scrollIntoView();

            //change index number of the block
            newBlock.attr('data-id', parseInt(newBlock.attr('data-id')) + 1).find(':input.item-id').val(-1);
            newBlock.find('[data-old-value]').attr('data-old-value','-1');
            newBlock.find('.price-elem .item-price').val('');
            //checking type of membership to show

            if(!this.isTrial) {
                if($(packagesList).find(`.package-row-block .department-type option:selected[value=${this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP}]`).length  === 1 ) {
                    newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP).trigger('change');
                } else if($(packagesList).find(`.package-row-block .department-type option:selected[value=${this.MEMBERSHIP_TYPE_CARD}]`).length === 0) {
                    newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_CARD).trigger('change');
                }  else if($(packagesList).find(`.package-row-block .department-type option:selected[value=${this.MEMBERSHIP_TYPE_STANDING_ORDER}]`).length === 0) {
                    newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_STANDING_ORDER).trigger('change');
                } else {
                    newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP).trigger('change');
                    if(newBlock.find('.expire-elem-cycle .validity-membership option:not(.d-none)').length === 0) {
                        newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_CARD).trigger('change');
                    }
                }
            } else {
                newBlock.find('.department-type').val(this.MEMBERSHIP_TYPE_CARD).trigger('change');
            }
            newBlock.find('.department-type').addClass('changed');
        }
    },
    //remove subscription block
    removePackage: function (elem)  {
        const block = $(elem).attr("disabled", true).closest('.package-row-block');
        const blockList = block.closest('#registration-packages-list');
        const membershipType = block.find('.department-type option:selected').val();
        blockList.find(`:input.department-type`).addClass('changed');
        //By the selected membership type displays the relevant fields in the block
        switch (membershipType) {
            case this.MEMBERSHIP_TYPE_CYCLE_MEMBERSHIP:
                //show the deleted option (expire cycle)
                const expireCycle = block.find('.expire-elem-cycle select option:selected').val();
                blockList.find(`.package-row-block .expire-elem-cycle select option[value=${expireCycle}]`)
                    .removeClass('d-none');
                break;
            case this.MEMBERSHIP_TYPE_CARD:
                //show the deleted option (expire cycle)
                const expireCard = block.find('.expire-elem-card select option:selected').val();
                const entries = block.find('.restriction-by-entries option:selected').val();
                blockList.find(`.expire-elem-card select`).each(function (){
                    $(this).find(`option[value=${expireCard}].d-none`).removeClass('d-none').prop('disabled', false);
                });
                blockList.find(`.package-row-block .expire-elem-card select option:selected[value=${expireCard}]`)
                    .each(function () {
                        let entriesSelect = $(this).closest('.row').find('select.restriction-by-entries');
                        // add to option old selected
                        entriesSelect.find(`option[value=${entries}]`).removeClass('d-none').prop('disabled', false);
                    });
                break
            case this.MEMBERSHIP_TYPE_STANDING_ORDER:
                //show the deleted option (department-type)
                blockList.find(`.department-type option[value=${membershipType}]`).removeClass('d-none').prop('disabled', false);
                break;
        }

        block.remove();
        //If one block remains, it hides the option to delete the blocks
        if(blockList.find('.package-row-block').length === 1) {
            blockList.find('.remove-button:first').removeClass('d-flex').addClass('d-none');
        }

    },
}






