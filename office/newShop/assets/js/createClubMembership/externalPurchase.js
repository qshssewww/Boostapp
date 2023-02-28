let externalPurchase = {
    mainElem: null,
    /********************* Global function *********************/
    constList : {
        EXTERNAL_PURCHASE_OFF:'0',
        EXTERNAL_PURCHASE_ON :'1',
        START_VALIDITY_DATE_OF_PURCHASE :'1',
        START_VALIDITY_FIRST_CLASS:'3',
        START_VALIDITY_MANUALLY_DATE :'4',
        NOT_ALLOW_LATE_REGISTER :'0',
        ALLOW_LATE_REGISTER :'1',
        OFFSET_LATE_DAY_OFF :'0',
        OFFSET_LATE_DAY_ON :'1',
        AGE_RESTRICTION :"1",
        GENDER_RESTRICTION :"2",
        CLIENT_STATUS_RESTRICTION :"5",
        TAG_RESTRICTIONS :"6",
        SENIORITY_RESTRICTION :"3",
        QUANTITY_LIMIT :"4",
    },
    //Defines constants within the registrationRestrictions object According to constList
    setConst: function () {
        Object.keys(this.constList).forEach(function(constName) {
            Object.defineProperty(externalPurchase, constName,
                {
                    value:	externalPurchase.constList[constName],
                    writable: false,
                    enumerable: true,
                    configurable: true
                }
            );
        });
    },
    init: function (){
        if(!this.mainElem) {
            this.setConst();
            this.mainElem = $(createClubMemberships.mainElem).find('.external-purchase-tab');
        }
        this.reloadMultiSelect2();
    },
    reloadMultiSelect2: function (){
        $( this.mainElem).find("#purchase-restriction-blocks-list .rank-restriction .rank-restriction-input")
            .select2({
                placeholder: lang('select_tags'),
                language: $("html").attr("dir") == 'rtl' ? "he" : "en",
                dropdownParent: $(createClubMemberships.mainElem),
                minimumResultsForSearch: -1
            }).on("select2:unselect", function (evt) {
            if (!evt.params.originalEvent) {
                return;
            }
            evt.params.originalEvent.stopPropagation();
        });

    },

    //Before returning to the main tab, check validation - not valid stay in this tab
    validationBeforeBack: function (elem) {
        console.log('now validation externalPurchase');
        if (createClubMemberships.checkValidation(this.mainElem)) {
            createClubMemberships.setTitleForDisplayInformation(true,false);
            shopMaim.switchTab(elem);
        }
    },
    //Resets all fields in the tab
    resetsTab: function (displayExternalPurchaseOff = true) {
        //remove all registration package blocks

        const blockList = this.mainElem.find('#purchase-restrictions-section #purchase-restriction-blocks-list');
        blockList.find('.remove-button a').each(function () {
            $(this).click()
        });
        this.mainElem.find('#membership-club-information').val('').addClass('js-post-value').trigger('keyup');
        this.mainElem.find('#add-picture-section .remove-picture-btn').click();
        if (displayExternalPurchaseOff) {
            this.mainElem.find('select.external-purchase')
                .val(this.EXTERNAL_PURCHASE_OFF).trigger('change');
        }
        blockList.find(`:input.purchase-restriction-type option`).removeClass('d-none');
        if (createClubMemberships.isTrial) {
            blockList.find(`:input.purchase-restriction-type option[value=${this.CLIENT_STATUS_RESTRICTION}]`).addClass('d-none');
        }
    }
    ,

    //return obj of base item data
    getBaseItemData: function (isEditMode= false){
        let itemExternalData= {};
        //Preparation of the basic information about this item (data from fields in external purchase tab)
        if(createClubMemberships.checkValidation(this.mainElem)) {
            itemExternalData = this.mainElem
                .find(`.app-view-settings > :not(#purchase-restrictions-section) :input.js-post-value${isEditMode?'.changed':''}`)
                .serializeAllArray();
            itemExternalData.Display = this.mainElem.find(`.external-purchase.js-post-value${isEditMode?'.changed':''}`).val();
            if(!itemExternalData.Display ) {
                delete itemExternalData.Display;
            }
            return itemExternalData;
        } else {
            console.log('not valid items'); //todo
            return false;
        }
    },

    //return obj of base item data
    getItemLimitData: function (isEditMode= false){
        let data = this.mainElem.find('#purchase-restrictions-section :input.js-post-value').serializeAllArray();
        if(data.rank) {
            data.rank = JSON.stringify(data.rank);
        }
        return data;
    },

    /********************* purchase app display *********************/
    //return the text for Display Information in the main tab
    getSubTitleForDisplayInformation: function () {
        let subTitleString = '';
        if(this.mainElem.find('#purchase-app-section select.external-purchase').val() === this.EXTERNAL_PURCHASE_ON){
            const blocksSection = this.mainElem.find('#custom-purchase-restriction-blocks');
            if(blocksSection.hasClass('d-none')) {
                subTitleString = lang('vod_set_permi');
            } else {
                const blocks = blocksSection.find('#purchase-restriction-blocks-list .purchase-restriction-block');
                if(blocks.length > 2) {
                    subTitleString = `${lang('are_defined')} ${blocks.length} ${lang('purchase_restrictions_in_app')}`
                } else {
                    subTitleString = lang('limited_purchase_by') + ' ';
                    blocks.find(':input.purchase-restriction-type').each(function (index, item){
                        subTitleString += index > 0 ? lang('and'): '';
                        subTitleString += $(item).find('option:selected').attr('data-short-text') + ' ';
                    })
                    subTitleString.slice(0, -1)
                }
            }
        }
        return subTitleString;
    },

    //When the status is displayed in the application, it changes or does not display the other fields
    displayExternalPurchaseChange: function (elem) {
        let viewSettings = $(elem).closest('#purchase-app-section').find('.app-view-settings');
        const displayInformationSection = createClubMemberships.mainElem.find('#dynamic-blocks-details-section .external-purchase-information-display');
        switch ($(elem).val()) {
            case this.EXTERNAL_PURCHASE_OFF:
                //all inputs now disabled
                viewSettings.addClass('d-none').find(':input')
                    .removeAttr('required').removeClass('js-post-value');
                //change icon
                displayInformationSection.find('.information-display-icon')
                    .addClass('fa-eye-slash').removeClass('fa-eye');
                displayInformationSection.find('.js-preview-title').text(lang('not_display_to_purchase_in_app'));
                displayInformationSection.find('.js-preview-sub-title').text('');
                break;
            case this.EXTERNAL_PURCHASE_ON:
                this.resetsTab(false);
                viewSettings.removeClass('d-none').find('#add-picture-section :input').addClass('js-post-value');
                viewSettings.find('select.type-start-validity')
                    .attr('required', '').val(this.START_VALIDITY_DATE_OF_PURCHASE).addClass('js-post-value').trigger('change');
                displayInformationSection.find('.information-display-icon')
                    .removeClass('fa-eye-slash').addClass('fa-eye') ;
                displayInformationSection.find('.js-preview-title').text(lang('display_to_purchase_in_app'));
                break;
        }
    },
    //Subscription start type changed if manually set shows the relevant fields
    typeStartValidity: function (elem) {
        let moreDetailsStartValidity = $(elem).closest('#validity-calculation-section').find('.more-details');
        switch ($(elem).val()) {
            case this.START_VALIDITY_DATE_OF_PURCHASE:
                $(moreDetailsStartValidity).addClass('d-none').find(':input')
                    .removeAttr('required').removeClass('js-post-value');
                break;
            case this.START_VALIDITY_FIRST_CLASS:
                $(moreDetailsStartValidity).addClass('d-none').find(':input')
                    .removeAttr('required').removeClass('js-post-value');
                break;
            case this.START_VALIDITY_MANUALLY_DATE:
                $(moreDetailsStartValidity).removeClass('d-none');
                $(moreDetailsStartValidity).find(':input.late-register-date')
                    .attr('required', '').val("").addClass('js-post-value').trigger('change')
                $(moreDetailsStartValidity).find('select.allow-late-register')
                    .attr('required', '').addClass('js-post-value').val(this.NOT_ALLOW_LATE_REGISTER).trigger('change')
                break;
        }
    },

    allowLateRegisterChange: function (elem) {
        let offsetDetails = $(elem).closest('.more-details').find('.more-details-offset');
        switch ($(elem).val()) {
            case this.NOT_ALLOW_LATE_REGISTER:
                $(offsetDetails).addClass('d-none').find(':input').removeAttr('required')
                    .removeClass('js-post-value');
                break;
            case this.ALLOW_LATE_REGISTER:
                $(offsetDetails).removeClass('d-none');
                $(offsetDetails).find('.offset-relatively-day-late').attr('required', '')
                    .addClass('js-post-value').val(this.OFFSET_LATE_DAY_OFF).trigger('change');
                break;
        }
    },

    /********************* chose image *********************/
    //Delete Image - Delete a value, and delete a view of the previous image
    removeImage: function (elem) {
        const addPicture = $(elem).closest('#add-picture-section');
        $(addPicture).find('#selected-image img:first').remove();
        shopMaim.showFlex($(addPicture).find('.add-picture-btn'));
        $(addPicture).find('.image-section').addClass('d-none');
        $(addPicture).find('#pageImgPath').val("").trigger('change');
    },

    /********************* purchase restrictions *********************/
    //Default block preparation - adding a first block and hiding "with no purchase restriction"
    createFirstPurchaseRestrictions: function (elem) {
      shopMaim.hideFlex($(elem).closest('#default-purchase-restriction-block'));
      const customRestriction = $(elem).closest('#purchase-restrictions-section')
          .find('#custom-purchase-restriction-blocks:first').removeClass('d-none');
      $(customRestriction).find('.purchase-restriction-type').val(this.AGE_RESTRICTION)
          .attr('data-old-value','-1').trigger('change');

    },

    addPurchaseRestriction: function (elem) {
        const blockList = $(elem).closest('#custom-purchase-restriction-blocks').find('#purchase-restriction-blocks-list:first');
        if(createClubMemberships.checkValidation(blockList)) {
            //clone last block and add it to dom
            const lastBlock = $(blockList).find('.purchase-restriction-row-block:last').clone();
            blockList.append(lastBlock);

            //scroll to new block
            let newBlock = blockList.find('.purchase-restriction-row-block:last');
            $(newBlock)[0].scrollIntoView();

            //change index number of the block
            newBlock.attr('data-id', parseInt(newBlock.attr('data-id')) + 1)
                .find('[data-old-value]').attr('data-old-value','-1');

            //remove select2 old
            newBlock.find('.select2.select2-container').remove();

            //remove from option the new default option
            let typeRestriction =newBlock.find('.purchase-restriction-type');

            const newType= typeRestriction.find('option:not(.d-none):eq(0)').val();
            typeRestriction.val(newType).trigger('change');
            this.reloadMultiSelect2();

            //If there are no more options, do not allow adding more blocks
            if(!typeRestriction.find('option:not(.d-none)').length) {
                $(elem).addClass('bsapp-js-disabled-o');
                blockList.find('select.purchase-restriction-type').addClass('bsapp-js-disabled-o');
            }
        }
    },

    //remove subscription block
    removeBlock: function (elem)  {
        const block = $(elem).attr("disabled", true).closest('.purchase-restriction-row-block');
        const blockList = block.closest('#purchase-restriction-blocks-list');
        const restrictionType = block.find('.purchase-restriction-type').val();
        blockList.closest('#custom-purchase-restriction-blocks')
            .find('#purchase-restrictions-header').removeClass('bsapp-js-disabled-o')
            .find('a:first').removeClass('bsapp-js-disabled-o');
        const restrictionTypeElemns = blockList.find('.purchase-restriction-row-block select.purchase-restriction-type')
            .removeClass('bsapp-js-disabled-o');


        //By the selected membership type displays the relevant fields in the block
        restrictionTypeElemns.find(`option[value=${restrictionType}]`).removeClass('d-none');

        //if removed last than none restriction show
        if($(blockList).find('.purchase-restriction-row-block').length === 1) {
            shopMaim.showFlex($(blockList).closest('#purchase-restrictions-section').find('#default-purchase-restriction-block'));
            $(blockList).closest('#purchase-restrictions-section')
                .find('#custom-purchase-restriction-blocks:first')
                .addClass('d-none').find(':input').removeClass('js-post-value');
            block.attr('data-id',0);
        } else {
            $(block).remove();
        }
    },

    purchaseRestrictionTypeChange: function (elem) {
        let oldValue = $(elem).attr("data-old-value");
        const block = $(elem).closest('.purchase-restriction-block');

        $(block).find('.restriction-more-details').addClass('d-none')
            .find(':input').removeAttr('required').val('').removeClass('js-post-value');

        let selectType = $(block).closest('#purchase-restriction-blocks-list')
            .find('select.purchase-restriction-type');

        //Changes the possible options for selection
        $(selectType).find(`option[value=${oldValue}]`).removeClass('d-none');
        $(selectType).find(`option[value=${elem.value}]`).addClass('d-none');


        //By the selected restriction type displays the relevant fields in the block
        switch (elem.value) {
            case this.AGE_RESTRICTION:
                $(block).find('.restriction-more-details.age-restriction')
                    .removeClass('d-none').find(':input').attr('required', '').addClass('js-post-value');
                break;
            case this.CLIENT_STATUS_RESTRICTION:
                $(block).find('.restriction-more-details.client-status-restriction')
                    .removeClass('d-none').find(':input').attr('required', '').addClass('js-post-value')
                    .val(1).trigger('change');
                break;

            case this.SENIORITY_RESTRICTION:
                $(block).find('.restriction-more-details.seniority-restriction')
                    .removeClass('d-none').find(':input').attr('required', '').addClass('js-post-value')
                    .val(new Date().toISOString().split("T")[0]).trigger('change');
                break;

            case this.QUANTITY_LIMIT:
                $(block).find('.restriction-more-details.quantity-limit-restriction')
                    .removeClass('d-none').find(':input').attr('required', '')
                    .addClass('js-post-value').val(1).trigger('change');
                break;

            case this.GENDER_RESTRICTION:
                $(block).find('.restriction-more-details.gender-restriction')
                    .removeClass('d-none').find(':input').attr('required', '')
                    .addClass('js-post-value').val(2).trigger('change');
                break;

            case this.TAG_RESTRICTIONS:
                $(block).find('.restriction-more-details.rank-restriction')
                    .removeClass('d-none').find('select').attr('required', '')
                    .addClass('js-post-value').val('').trigger('change');
                break;
        }
        //update old value
        $(elem).attr("data-old-value", elem.value);
    },

    //remove subscription block
    ageRestrictionChange: function (elem)  {
        const ageRestrictionSection = $(elem).closest('.age-restriction')
        const thisAge = elem.value;
        if($(elem).hasClass('form-age-restriction')){
            $(ageRestrictionSection).find('.to-age-restriction').attr('min',thisAge);
        } else {
            $(ageRestrictionSection).find('.form-age-restriction:first').attr('max',thisAge);
        }
    },
}


