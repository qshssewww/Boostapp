
let registrationRestrictions = {
    mainElem: null,

    constList : {
        MAX_ENTRANCES_LIMIT: '1',
        DAYS_LIMIT : '2',
        HOURS_LIMIT : '3',
        DEFAULT_MAX_NUMBER_ENTRIES: '3',
        DEFAULT_TYPE_PERIOD_TIME: '2',
        DEFAULTS_LIMIT_BASED_AVAILABILITY_DATA: {
            'basedAvailabilityAmount-popup': 1,
            'basedAvailabilityPeriodType-popup': 2,
            'basedAvailabilityTimeBefore-popup': 12,
            'basedAvailabilityTypeTimeBefore-popup': 2,
        },
        MAX_ENTRANCES_LIMIT_TYPE_YEAR: 4,
        MAX_ENTRANCES_LIMIT_TYPE_MONTH: 3,
        MAX_ENTRANCES_LIMIT_TYPE_WEEK: 2,
        MAX_ENTRANCES_LIMIT_TYPE_DAY: 1



    },

    //Defines constants within the registrationRestrictions object According to constList
    setConst: function () {
        Object.keys(this.constList).forEach(function(constName) {
            Object.defineProperty(registrationRestrictions, constName,
                {
                    value:	registrationRestrictions.constList[constName],
                    writable: false,
                    enumerable: true,
                    configurable: true
                }
            );
        });
    },

    /********************* Global function *********************/
    init: function (){
        if(!this.mainElem) {
            this.setConst();
            this.mainElem = $(createClubMemberships.mainElem).find('.registration-restrictions-tab');
            this.initBsappMultiSelect();
        }
        this.mainElem.find('#registration-restrictions-section #tab-content .tab-body-option.active' +
            ' .registration-restrictions-option-section select.class-type-limits').attr('required', '');
    },
    //Initializes the Select of class type limits
    initBsappMultiSelect: function () {
        this.mainElem.find('select.bsappMultiSelect.class-type-limits')
            .bsappMultiSelect({
            class: 'my-class validation-check',
            relatedSelect: this.mainElem.find('select.bsappMultiSelect.class-type-limits'),
            container: this.mainElem,
            onAfterClose: function(e) {
            },
            onLoad: function(e) {
            },
            onCheckboxClick: function(e) {
                const container = $(e).closest('.class-type-limit-container');
                const selectLimits = container.find('select.class-type-limits:first');
                //if valid remove is-invalid class
                if($(selectLimits).val().length || !$(selectLimits).attr('required')){
                    container.find('.is-invalid').removeClass('is-invalid');
                }

                const mainSection = $(container).closest('#registration-restrictions-section');
                const nextNavTab = $(mainSection).find('#registration-option-tabs-list:first .registration-option-tab a.add-option:first')
                    .closest('.registration-option-tab').removeClass('bsapp-js-disabled-o');

                //hide add option button if empty options
                const nextAddTabNum = $(nextNavTab).attr('data-id');
                const nextClassTypeSelect = $(mainSection).find(`#tab-content:first .tab-body-option[data-option=${nextAddTabNum}] .limit-membership-club-to:first select.class-type-limits:first`);
                if(registrationRestrictions.checkSelectEmpty(nextClassTypeSelect)) {
                    $(nextNavTab).addClass('bsapp-js-disabled-o');
                }
            }
        });
    },
    //return number of tab
    findTabOptionNum: function (elem){
        return $(elem).closest('.tab-body-option').attr('data-option');
    },
    //if valid back to main page
    backToMainTab: function (elem){
        this.removeEmptyOrFullDaysLimit();
        const thisTab = $(elem).closest('.registration-restrictions-tab');
        if(this.allSelectClassTypAreEmpty()) {
            $(thisTab).find('select.class-type-limits').addClass('is-invalid')
                .parent('.validation-check').addClass('is-invalid');
        } else if (this.isEmptyTab(thisTab) || createClubMemberships.checkValidation(thisTab)){
            createClubMemberships.setTitleForDisplayInformation(false,true);
            this.mainElem.find('#registration-option-tabs-section')
                .find(`#registration-option-tabs-list li[data-id=${1}]:first a:first`).click()
            shopMaim.switchTab(elem);
        } else {
            $(thisTab).find(':input.is-invalid.class-type-limits')
                .parent('.validation-check').addClass('is-invalid');
        }
    },


    //Receives string input of all class type,
    // And returns an array of each class type in a format that the select expects to receive them
    fromStringOfClassToArrayFrontFormat: function (input) {
        let array=  input.split(",");
        array.forEach(function(value,index,self) {
            if(value === 'BA999') {
                self[index] = '_all_class';
            } else if(value === 'BA888') {
                self[index] = '_all_meet';
            } else if(value === 'BA777') {
                self[index] = '_all_space';
            }
        });
        return  array;
    },
    //Receives string input of all class type in front format,
    // And returns an array of each class type in a db format
    fromStringOfClassToArrayFrontDb: function (input) {
        input.forEach(function(value,index,self) {
            if(value === '_all_class') {
                self[index] = 'BA999';
            } else if(value === '_all_meet') {
                self[index] = 'BA888';
            } else if(value === '_all_space') {
                self[index] = 'BA777';
            }
        });
        return input.join();
    },
    //Receives the input returned by DB and converts it to an object that contains the class name and the value to be entered
    fromDbRowToFrontDataObj: function (row) {
        let responseObj = {}
        switch (row.Group) {
            case 'Max':
                responseObj['membership-club-limit-type'] = this.MAX_ENTRANCES_LIMIT;
                let type = '';
                if (row.Item === 'Day') {
                    type = this.MAX_ENTRANCES_LIMIT_TYPE_DAY;
                } else if (row.Item === 'Week') {
                    type = this.MAX_ENTRANCES_LIMIT_TYPE_WEEK;
                } else if (row.Item === 'Month') {
                    type = this.MAX_ENTRANCES_LIMIT_TYPE_MONTH;
                } else if (row.Item === 'Year') {
                    type = this.MAX_ENTRANCES_LIMIT_TYPE_YEAR;
                }
                responseObj['type-period-time'] = type;
                responseObj['max-number-entries'] = row.Value;
                break;
            case 'Day':
                responseObj['membership-club-limit-type'] = this.DAYS_LIMIT;
                let daysArray = row.Value.split(",");
                daysArray.forEach(function (value, index, self) {
                    if (value === 'ראשון') {
                        self[index] = 0;
                    } else if (value === 'שני') {
                        self[index] = 1;
                    } else if (value === 'שלישי') {
                        self[index] = 2;
                    } else if (value === 'רביעי') {
                        self[index] = 3;
                    } else if (value === 'חמישי') {
                        self[index] = 4;
                    } else if (value === 'שישי') {
                        self[index] = 5;
                    } else if (value === 'שבת') {
                        self[index] = 6;
                    } else {
                        self[index] = '';
                    }
                });
                responseObj['limit-day'] = daysArray;
                break;
            case 'Time':
                responseObj['membership-club-limit-type'] = this.HOURS_LIMIT;
                let parsedTimeData = JSON.parse(row.Value);
                responseObj['hours-limit-from'] = parsedTimeData.data[0].FromTime;
                responseObj['hours-limit-until'] = parsedTimeData.data[0].ToTime;
                break;
            case 'Item' :
                let parsedStandByData = JSON.parse(row.Value);
                if(Array.isArray(parsedStandByData.data)) {
                    responseObj['basedAvailabilityAmount'] = parsedStandByData.data[0].StandByCount;
                    responseObj['basedAvailabilityPeriodType'] = parsedStandByData.data[0].StandByVaild_Type;
                    responseObj['basedAvailabilityTimeBefore'] = parsedStandByData.data[0].StandByTime;
                    responseObj['basedAvailabilityTypeTimeBefore'] = parsedStandByData.data[0].StandByTimeVaild_Type;
                }
                else{
                    responseObj['basedAvailabilityAmount'] = parsedStandByData.data.StandByCount;
                    responseObj['basedAvailabilityPeriodType'] = parsedStandByData.data.StandByVaild_Type;
                    responseObj['basedAvailabilityTimeBefore'] = parsedStandByData.data.StandByTime;
                    responseObj['basedAvailabilityTypeTimeBefore'] = parsedStandByData.data[0].StandByTimeVaild_Type;
                }
                break;
        }
        return responseObj;
    },
    //Receives the input returned by DB and converts it to an object that contains the class name and the value to be entered
    fromFrontDataToDbObj: function (data) {
        let responseObj = {}
        if(data.class && data.class !== '') {
            responseObj.Class = registrationRestrictions.fromStringOfClassToArrayFrontDb(data.class);
            responseObj.optionNumber = data.optionNumber ?? 1;
        }
        if(data.membershipClubLimitType) {
            switch (data.membershipClubLimitType) {
                case registrationRestrictions.MAX_ENTRANCES_LIMIT:
                    responseObj.Group = 'Max';
                    let type;
                    if (data.typePeriodTime == this.MAX_ENTRANCES_LIMIT_TYPE_DAY) {
                        type = 'Day';
                    } else if (data.typePeriodTime == this.MAX_ENTRANCES_LIMIT_TYPE_WEEK) {
                        type = 'Week';
                    } else if (data.typePeriodTime == this.MAX_ENTRANCES_LIMIT_TYPE_MONTH) {
                        type = 'Month';
                    } else if (data.typePeriodTime == this.MAX_ENTRANCES_LIMIT_TYPE_YEAR) {
                        type = 'Year';
                    }
                    responseObj.Item = type;
                    responseObj.Value = data.maxNumberEntries ?? '';
                    break;
                case registrationRestrictions.DAYS_LIMIT:
                    if(data.limitDay && data.limitDay.length > 0) {
                        let daysArray = data.limitDay;
                        daysArray.forEach(function (value, index, self) {
                            if (value == 0) {
                                self[index] = 'ראשון';
                            } else if (value == 1) {
                                self[index] = 'שני';
                            } else if (value == 2) {
                                self[index] = 'שלישי';
                            } else if (value == 3) {
                                self[index] = 'רביעי';
                            } else if (value == 4) {
                                self[index] = 'חמישי';
                            } else if (value == 5) {
                                self[index] = 'שישי';
                            } else if (value == 6) {
                                self[index] = 'שבת';
                            }
                        });
                        responseObj.Group = 'Day';
                        responseObj.Item = 'Days';
                        responseObj.Value = daysArray.join();
                    }
                    break;
                case registrationRestrictions.HOURS_LIMIT:
                    const formTime = data.hoursLimitFrom ?? '';
                    const toTime = data.hoursLimitUntil ?? '';
                    if (formTime && toTime) {
                        responseObj.Value = `{"data":[{"FromTime": "${formTime}" , "ToTime": "${toTime}"}]}`;
                        responseObj.Group = 'Time';
                        responseObj.Item = 'Time';
                    }
                    break;
                default:
                    break;
            }
        }
        else if(data.groupClass) {
            responseObj.Group = 'Class';
            responseObj.Item = 'Class';
        } else if(data.basedAvailabilityAmount && data.basedAvailabilityPeriodType && data.basedAvailabilityTimeBefore){
            responseObj.Group = 'Item';
            responseObj.Item = 'StandBy';
            responseObj.Value = `{"data": [{"StandByCount":"${data.basedAvailabilityAmount}","StandByVaild_Type":"${data.basedAvailabilityPeriodType}","StandByTime":"${data.basedAvailabilityTimeBefore}","StandByTimeVaild_Type":${data.basedAvailabilityTypeTimeBefore},"StandByOption":1}]}`;
        } else{
            return {};
        }
        return responseObj;
    },

    /********************* tabs *********************/
    //When a tab is selected checks which tab to move to - first empty tab or one that is pressed
    selectedTab: function (elem,e) {
        e.preventDefault();
        this.removeEmptyOrFullDaysLimit();
        if($(elem).hasClass('active')) {
            return;
        }
        const oldLinkTab = $(elem).closest('#registration-option-tabs-list').find('li.active');
        const oldTab = $(elem).closest('#registration-restrictions-section')
            .find(`#tab-content .tab-body-option[data-option=${$(oldLinkTab).attr('data-id')}]`);
        $(oldTab).find('.registration-restrictions-option-section select.class-type-limits')
            .attr('required', '').addClass('js-post-value');
        //if try to move tab and empty -> 'remove' (only show in next valid adding and not required any more)
        if($(elem).hasClass('link-option') && this.isEmptyTab(oldTab))  {
            $(oldTab).find('.registration-restrictions-option-section select.class-type-limits')
                .removeAttr('required').removeClass('js-post-value');
            this.showTab(elem)
        } else if (createClubMemberships.checkValidation(oldTab)) {
            //if valid and move tab than move to the tab
            if($(elem).hasClass('link-option') ) {
                registrationRestrictions.showTab(elem);
            } else {
                //if valid and add tab than move to first empty tab
                $(elem).closest('#registration-restrictions-section')
                    .find(`#tab-content .tab-body-option:not(.active)`).each(function () {
                    if(registrationRestrictions.isEmptyTab(this)) {
                        let firstEmptyTab = $(elem).closest('#registration-option-tabs-list')
                            .find(`.registration-option-tab[data-id=${$(this).attr('data-option')}] a`)
                        registrationRestrictions.showTab(firstEmptyTab);
                        return false;
                    } else {
                    }
                })
            }
        } else {
            $(oldTab).find(':input.is-invalid.class-type-limits')
                .parent('.validation-check').addClass('is-invalid');
        }
    },
    // Displays the active tabs in large and the rest in small and markings according to their function
    showTab: function (elem) {
        $(elem).tab('show');
        //Shrinks all tabs option
        $(elem).closest('#registration-option-tabs-list').find('.link-option').each(function () {
            const num = $(this).closest('li')
                .removeClass('col-6 active').addClass('col-2').attr('data-id')
            $(this).text(num);
        });
        //Shapes and enlarges the pressed tab
        const optionNum =$(elem).closest('li.registration-option-tab').addClass('col-6 active').attr('data-id');
        $(elem).text(lang('option') + " " + optionNum);
        // add new tab and change style
        if($(elem).hasClass('add-option')) {
            $(elem).removeClass('add-option').addClass('link-option');
            $(elem).closest('#registration-option-tabs-list').find(`li[data-id=${parseInt(optionNum)+ 1}]`)
                .removeClass('d-none').find('a')
                .removeClass('disabled-option').addClass('add-option').text('+');
        }

        const selectClassLimit = $(elem).closest('#registration-restrictions-section')
            .find(`#tab-content:first .tab-body-option[data-option=${optionNum}] .limit-membership-club-to:first select.class-type-limits:first`);
        if(selectClassLimit.val().length === 0) {
            setTimeout(function () {
                selectClassLimit.bsappMultiSelect('open');
            }, 200);
        }
    },
    // Returns true if the tab is empty
    isEmptyTab: function (tab) {
        return (!$(tab).find('select.class-type-limits').bsappMultiSelect('values').length &&
            $(tab).find('#default-membership-club-limit-block input.no-limit-input').val() == 1)
    },
    // Returns true all tab is empty
    allSelectClassTypAreEmpty: function () {
        return (!this.mainElem.find('select.class-type-limits option:selected').length);
    },

    resetAllClassTypeSelect:function () {
        const classTypeSelect = this.mainElem.find('.bsappMultiSelect.class-type-limits').removeClass('js-post-value is-invalid');
        classTypeSelect.find('option, optgroup').prop('selected', false).attr('hidden',false);
        classTypeSelect.bsappMultiSelect('reload');
    },
    //Resets all fields in the tab
    resetsTab: function () {
        // //remove all registration package blocks based-availability-limit
        this.mainElem.find('#custom-membership-club-limit-blocks .based-availability-limit-row-block .remove-button a').click();
        //remove all registration package blocks
        this.mainElem.find('#custom-membership-club-limit-blocks .membership-club-limit-row-block .remove-button a')
            .each(function() { $(this).click() });
        this.resetAllClassTypeSelect();
        this.mainElem.find('#class-type-limits-input-1').addClass('js-post-value').closest('.bsapp--sel')
            .find('.bsapp--sel__box input[type=checkbox][value=_all_class]:first').click();

        $('#registration-option-tabs-section ul').remove();
        $('#registration-option-tabs-section').append(
            `
            <!-- Nav tabs -->
                <ul id="registration-option-tabs-list" class="nav nav-tabs p-0 m-0 pt-10 " role="tablist">
                    <li class="registration-option-tab active col-6 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 100;" data-id=1>
                        <a class="text-dark bold link-option text-decoration-none" href="#option1" role="tab"
                           onclick="registrationRestrictions.selectedTab(this,event)">
                            ${lang('option')} 1</a>
                    </li>
                    <li class="registration-option-tab col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 99;"data-id=2>
                        <a class="text-dark bold add-option text-decoration-none" href="#option2" role="tab"
                           onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                    </li>
                    <li class="registration-option-tab d-none col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 98;" data-id=3>
                        <a class="text-dark disabled-option bold text-decoration-none" href="#option3" role="tab"
                           onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                    </li>
                    <li class="registration-option-tab d-none col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 97" data-id="4">
                        <a class="text-dark bold disabled-option text-decoration-none" href="#option4" role="tab"
                           onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                    </li>
                </ul>
            `
        )
    },
    //return the title text for Display Information in the main tab
    getTitleForDisplayInformation: function () {
        let titleString = '';
        const blocksSection = this.mainElem.find('#custom-membership-club-limit-blocks:not(.d-none)');
        const allBlocks = blocksSection.find('#membership-club-limit-blocks-list .membership-club-limit-block');
        const limitTypes =  allBlocks.find('select.membership-club-limit-type');
        if(limitTypes.length === 1) {
            titleString = lang('limit_purchase') + ' ';
            switch (limitTypes.val()) {
                case this.MAX_ENTRANCES_LIMIT:
                    const numberOfEntriesElem = allBlocks.find(':input.max-number-entries option:selected');
                    const typePeriod =allBlocks.find(':input.type-period-time option:selected').text();
                    if(blocksSection.find('.based-availability-limit-row-block').hasClass('d-none')){
                        titleString += numberOfEntriesElem.val() === '0' ? ` ${lang('without_ordering_capacity')} ` :
                            `${numberOfEntriesElem.text()} ${lang('entries')} ${typePeriod}`;
                    } else {
                        titleString += numberOfEntriesElem.val() === '0'  ?  ` ${lang('based_on_availability_only')} ` :
                            `${numberOfEntriesElem.text()} ${lang('entries')} ${typePeriod}`;
                    }
                    break
                case this.DAYS_LIMIT:
                    let dayValString= '';
                    allBlocks.find('input[name=limitDay]:checked').each(function (index, item){
                        dayValString += $(item).attr('data-short-text') + ',';
                    });
                    titleString += `${lang('in_days_popup')} ${dayValString.slice(0, -1)}`;
                    break
                case this.HOURS_LIMIT:
                    const fromTime = allBlocks.find(':input.hours-limit-from option:selected').text();
                    const untilTime = allBlocks.find(':input.hours-limit-until option:selected').text();
                    titleString += `${lang('start_hour')} ${fromTime} ${lang('end_hour')} ${untilTime}`;
                    break
            }
        } else if(limitTypes.length === 0){
            titleString = lang('no_limits_purchase');
        } else {
            let hasMaxLimit = false ,hasDayLimit = false ,hasHoursLimit = false;
            limitTypes.each(function (){
                switch (this.value) {
                    case registrationRestrictions.MAX_ENTRANCES_LIMIT:
                        hasMaxLimit = true;
                        break;
                    case registrationRestrictions.DAYS_LIMIT:
                        hasDayLimit = true;
                        break;
                    case registrationRestrictions.HOURS_LIMIT:
                        hasHoursLimit = true;
                        break;
                }
            })
            titleString = `${lang('limits_purchase_by')} ${hasMaxLimit ? lang('entries') + ',' : ''}${hasHoursLimit ? lang('hours') + ',': ''}${hasDayLimit ? lang('days') + ',' : ''}`

        }
        return titleString.replace(/,$/, '');
    },
    //return the subtitle text for Display Information in the main tab
    getSubTitleForDisplayInformation: function () {
        let subTitleString = '';
        const selectClassTypeElem = this.mainElem.find('.bsappMultiSelect.class-type-limits');
        const classesSelectedNumber = selectClassTypeElem.find('optgroup.optgroup-class option:selected:not([value=_all_class])').length;
        const meetingSelectedNumber = selectClassTypeElem.find('optgroup.optgroup-meeting option:selected[data-multi]').length;
        const spaceSelectedNumber = selectClassTypeElem.find('optgroup.optgroup-space option:selected:not([value=_all_space])').length;
        const allClassesNumber = selectClassTypeElem.eq(0).find('optgroup.optgroup-class option:not([value=_all_class])').length;
        const allMeetingsNumber = selectClassTypeElem.eq(0).find('optgroup.optgroup-meeting option[data-multi]').length;
        const allSpacesNumber = selectClassTypeElem.eq(0).find('optgroup.optgroup-space option:not([value=_all_space])').length ;

        if(allClassesNumber === 0  || classesSelectedNumber === 0 ) {
            subTitleString += "";
        } else if(allClassesNumber === classesSelectedNumber) {
            subTitleString += lang('all_classes') + ',';
        } else {
            subTitleString += classesSelectedNumber +  " " + lang('classes') + ',';
        }
        if(allMeetingsNumber === 0 || meetingSelectedNumber === 0) {
            subTitleString += "";
        } else if(allMeetingsNumber === meetingSelectedNumber ) {
            subTitleString += lang('meeting_all') + ',';
        } else {
            subTitleString += meetingSelectedNumber +  " " + lang('cal_appointments') + ',';
        }
        if(allSpacesNumber === 0 || spaceSelectedNumber === 0) {
            subTitleString += "";
        } else if(spaceSelectedNumber === allSpacesNumber) {
            subTitleString += lang('all_spaces')
        } else {
            subTitleString += spaceSelectedNumber +  " " + lang('spaces')
        }
        return subTitleString.replace(/,$/, '');
    },

    //return obj of base item data
    geItemRoleData: function (isEditMode= false){
        let itemsRolesList = [];
        const registrationRestrictionSection = this.mainElem
            .find('#registration-restrictions-section:first .tab-body-option');
        let count = 0;
        registrationRestrictionSection.each(function () {
            count++;
            const classType = $(this).find(':input.bsappMultiSelect.class-type-limits')
                .bsappMultiSelect('values');
            if(classType.length > 0) {
                const classData = {'class': classType, 'groupClass': true, 'optionNumber': count};
                const dataInDbFormat = registrationRestrictions.fromFrontDataToDbObj(classData);
                itemsRolesList.push(dataInDbFormat);
                $(this).find('#membership-club-limit-blocks-list .membership-club-limit-block, #membership-club-limit-blocks-list .based-availability-limit-block')
                    .each(function () {
                        let newBlockData = $(this).find(':input.js-post-value').serializeAllArray();
                        newBlockData.class = classType;
                        newBlockData.optionNumber = count;
                        const dataInDbFormat = registrationRestrictions.fromFrontDataToDbObj(newBlockData);
                        if (Object.keys(dataInDbFormat).length > 0) {
                            itemsRolesList.push(dataInDbFormat);
                        }
                    });
            }
        })
        return itemsRolesList;
    },


    /********************* purchase limits *********************/
    //Default block limit - adding a first block and hiding "with no purchase limits"
    createFirstLimitBlock: function (elem) {
        const defaultBlock = $(elem).closest('#default-membership-club-limit-block');
        $(defaultBlock).find('input.no-limit-input').val(0).removeClass('js-post-value');
        shopMaim.hideFlex(defaultBlock);
        const customRestriction = $(elem).closest('#membership-club-limit-section')
            .find('#custom-membership-club-limit-blocks:first').removeClass('d-none');
        $(customRestriction).find('.membership-club-limit-type').val(1)
            .attr('data-old-value','-1').trigger('change');
        shopMaim.showFlex($(elem).closest('.registration-restrictions-option-section')
            .find('.limit-based-availability-section'));
    },
    //add block of limit
    addLimit: function (elem) {
        let valid = true;
        //Validation checks before adding block
        const blockList = $(elem).closest('#custom-membership-club-limit-blocks')
            .find('#membership-club-limit-blocks-list:first');

        let inputs = blockList.find(':input.js-post-value');
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
            blockList.find(':input.is-invalid').removeClass('is-invalid');
            //clone last block and add it to dom
            const lastBlock = blockList.find('.membership-club-limit-row-block:last').clone();
            blockList.append(lastBlock);

            //scroll to new block
            let newBlock = blockList.find('.membership-club-limit-row-block:last');
            newBlock[0].scrollIntoView();
            //change index number of the block
            newBlock.attr('data-id', parseInt(newBlock.attr('data-id')) + 1)
                .find('[data-old-value]').attr('data-old-value','-1');
            //remove select2 old
            newBlock.find('.select2.select2-container').remove();
            //remove from option the new default option
            let limitType = newBlock.find('.membership-club-limit-type');
            const newType= limitType.find('option:not(.d-none):eq(0)').val();
            limitType.val(newType).trigger('change');

            //If there are no more options, do not allow adding more blocks
            if(!limitType.find('option:not(.d-none)').length) {
                $(elem).addClass('bsapp-js-disabled-o');
                blockList.find('select.membership-club-limit-type').addClass('bsapp-js-disabled-o');
            }
        }
    },

    removeEmptyOrFullDaysLimit: function (){
        this.mainElem.find('#custom-membership-club-limit-blocks:not(.d-none) #membership-club-limit-blocks-list')
            .find(`.membership-club-limit-row-block select.membership-club-limit-type option:selected[value=${registrationRestrictions.DAYS_LIMIT}]`)
            .each(function (){
                const limitDaySection = $(this).closest('.membership-club-limit-block')
                    .find('.days-limits:first .limit-day-selector');
                if(limitDaySection.find('input[name=limitDay]:checked').length === 0 || limitDaySection.find('input[name=limitDay]:checked').length === 7) {
                    $(this).closest('.membership-club-limit-row-block ').find('.remove-button:first a').click();
                }
            });
    },

    //When the type of limits changes - Displays the appropriate fields and changes the available options
    membershipClubLimitTypeChange:function (elem) {
        $(elem).addClass('js-post-value');
        const block = $(elem).closest('.membership-club-limit-block');
        let oldValue = $(elem).attr("data-old-value");
        const listOfBlocks = block.closest('#membership-club-limit-blocks-list');
        const limitTypeHoursOption = listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option[value=${this.HOURS_LIMIT}]`);
        block.find('.limit-more-details').addClass('d-none')
            .find(':input').removeAttr('required').val('').removeClass('js-post-value');
        //Changes the options available in the limit type according to the old value'
        switch (oldValue) {
            case this.MAX_ENTRANCES_LIMIT:
                const oldEntries = block.find('.max-entrances-limit.limit-more-details .type-period-time').attr('data-old-value');
                listOfBlocks.find(`.membership-club-limit-row-block .max-entrances-limit .type-period-time option[value=${oldEntries}]`)
                    .removeClass('d-none');
                listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option[value=${this.MAX_ENTRANCES_LIMIT}]`).removeClass('d-none');
                break;
            case this.DAYS_LIMIT:
                listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option[value=${this.DAYS_LIMIT}]`).removeClass('d-none');
                break;
            case this.HOURS_LIMIT:
                $(limitTypeHoursOption).removeClass('d-none');
                break;
        }
        //By the selected restriction type displays the relevant fields in the block
        switch (elem.value) {
            case this.MAX_ENTRANCES_LIMIT:
                const nextTypePeriod = block.find(`.max-entrances-limit .type-period-time option.d-none[value=${this.DEFAULT_TYPE_PERIOD_TIME}]`).length ?
                block.find('.max-entrances-limit .type-period-time option:not(.d-none):eq(0)').val() : this.DEFAULT_TYPE_PERIOD_TIME;
                block.find('.limit-more-details.max-entrances-limit')
                    .removeClass('d-none').find(':input').attr('required', '').addClass('js-post-value').each(function () {
                        if($(this).hasClass('type-period-time')) {
                            $(this).val(nextTypePeriod).trigger('change');
                        } else {
                            $(this).val(registrationRestrictions.DEFAULT_MAX_NUMBER_ENTRIES).trigger('change');
                        }
                    });
                block.find('.limit-more-details.days-limits .limit-day-selector > div').remove();
                break;
            case this.DAYS_LIMIT:
                block.find('.limit-more-details.days-limits').removeClass('d-none')
                    .find('.limit-day-selector').append(this.renderDays(this.findTabOptionNum(block)));
                block.find('.limit-more-details.days-limits .limit-day-selector :input').addClass('js-post-value')
                //remove option day limit from membershipClubLimitType
                listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option[value=${this.DAYS_LIMIT}]`).addClass('d-none');
                break;
            case this.HOURS_LIMIT:
                const hoursLimitBlockInputs =block.find('.limit-more-details.hours-limits')
                    .removeClass('d-none').find(':input').attr('required', '').addClass('js-post-value');
                const hoursLimitsBlocks = listOfBlocks.find('.hours-limits.limit-more-details:not(.d-none)');
                //If this is the second block of the hours limit, restrict the selection of the appropriate hours only
                if(hoursLimitsBlocks.length > 1) {
                    $(limitTypeHoursOption).addClass('d-none');
                    const times = $(hoursLimitsBlocks).find('select option:selected').map(function (){
                        return parseInt($(this).attr('data-index'));
                    }).get();
                    const maxTime = Math.max.apply(Math, times);
                    const minTime = Math.min.apply(Math, times);
                    $(hoursLimitBlockInputs).find('option').each(function () {
                        this.index > maxTime || this.index < minTime ?
                            $(this).removeClass('d-none') : $(this).addClass('d-none');
                    });
                } else{
                    $(hoursLimitBlockInputs).find('option').removeClass('d-none');
                }
                block.find('.limit-more-details.days-limits .limit-day-selector > div').remove();
                break;

        }
        //update old value
        $(elem).attr("data-old-value", elem.value);
        //Does not allow clicking the add block button when no options available
        if(listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option:not('d-none')`)) {
            listOfBlocks.closest('#membership-club-limit-section').find('#membership-club-limit-header .add-block')
                .prop('disabled', 'disabled');
        } else {
            listOfBlocks.closest('#membership-club-limit-section').find('#membership-club-limit-header .add-block')
                .prop('disabled', false);
        }
        //hide based-availability sections if max limitation not exist
        let basedAvailabilityHide = listOfBlocks.closest('.based-availability-limit-row-block').hasClass('d-none');
        const basedAvailabilityLimitBlock = listOfBlocks.closest('#custom-membership-club-limit-blocks')
            .find('.based-availability-limit-row-block');
        if(!listOfBlocks.find(`.membership-club-limit-row-block .membership-club-limit-type option:selected[value=${this.MAX_ENTRANCES_LIMIT}]`).length) {
            this.removeBlockBasedAvailability(basedAvailabilityLimitBlock.find('a:first'))
            //hide footer
            shopMaim.hideFlex(listOfBlocks.closest('.registration-restrictions-option-section')
                .find('.limit-based-availability-section:first'));
        } else {
            if(basedAvailabilityLimitBlock.hasClass('d-none') || basedAvailabilityHide) {
                shopMaim.showFlex(listOfBlocks.closest('.registration-restrictions-option-section')
                    .find('.limit-based-availability-section:first'));
            } else {
                shopMaim.hideFlex(listOfBlocks.closest('.registration-restrictions-option-section')
                    .find('.limit-based-availability-section:first'));
            }
        }
        // disabled all empty select (none valid option)
        listOfBlocks.find('.membership-club-limit-row-block select').each(function (){
            $(this).find('option:not(.d-none)').length === 0 ?
                $(this).addClass('bsapp-js-disabled-o') : $(this).removeClass('bsapp-js-disabled-o')
        })
    },
    //When the typePeriodTime changes - change attribute old and changes the available options
    typePeriodTimeChange:function (elem) {
        let oldValue = elem.getAttribute('data-old-value');
        let newValue = elem.value ? elem.value : '-1'
        $(elem).closest('#membership-club-limit-blocks-list').find('.max-entrances-limit select.type-period-time')
            .each(function () {
                // add to option old selected
                $(this).find(`option[value=${oldValue}]`).removeClass('d-none');
                // remove the new selected option
                $(this).find(`option[value=${newValue}]`).addClass('d-none');
            });
        //update old value
        $(elem).attr("data-old-value", newValue);
        let maxEntranceOption = $(elem).closest('#membership-club-limit-blocks-list')
            .find(`.membership-club-limit-row-block .membership-club-limit-type option[value=${this.MAX_ENTRANCES_LIMIT}]`)
        $(elem).find('option:not(.d-none)').length === 0 ? $(maxEntranceOption).addClass('d-none')
            : $(maxEntranceOption).removeClass('d-none');

    },
    //todo fix seconds
    hoursLimitFromChange:function (elem) {
        const formTime = $(elem).find('option:selected').attr('data-index');
        let untilSelect = $(elem).closest('.hours-limits.limit-more-details').find('select.hours-limit-until');
        $(untilSelect).find('option').each(function () {
           this.index <= formTime ? $(this).addClass('d-none') : $(this).removeClass('d-none');
        });
    },
    //todo fix seconds
    hoursLimitUntilChange:function (elem) {
        const untilTime =  $(elem).find('option:selected').attr('data-index');
        let fromSelect = $(elem).closest('.hours-limits.limit-more-details').find('select.hours-limit-from');
        // $(elem).closest('#membership-club-limit-blocks-list').find('.hours-limits.limit-more-details select.hours-limit-from')
        $(fromSelect).find('option').each(function () {
            this.index >= untilTime ? $(this).addClass('d-none') : $(this).removeClass('d-none');
        });
    },

    dayLimitClicked:function (elem) {
    },

    //Returns the day limit selection buttons according to tab option number
    renderDays: function (optionNum) {
        return `<div>
            <input type="checkbox" data-short-text="${lang('sunday_short')}" name="limitDay" id="limit-day-sun-${optionNum}" class="limit-day js-post-value" onchange="registrationRestrictions.dayLimitClicked(this)" value="0" />
            <label for="limit-day-sun-${optionNum}">${lang('sunday_short')}</label>
            <input type="checkbox" data-short-text="${lang('monday_short')}" name="limitDay" id="limit-day-mon-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="1" />
            <label for="limit-day-mon-${optionNum}">${lang('monday_short')}</label>
            <input type="checkbox" data-short-text="${lang('tuesday_short')}" name="limitDay" id="limit-day-tue-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="2" />
            <label for="limit-day-tue-${optionNum}">${lang('tuesday_short')}</label>
            <input type="checkbox" data-short-text="${lang('wednesday_short')}" name="limitDay" id="limit-day-wed-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="3" />
            <label for="limit-day-wed-${optionNum}">${lang('wednesday_short')}</label>
            <input type="checkbox" data-short-text="${lang('thursday_short')}" name="limitDay" id="limit-day-thu-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="4" />
            <label for="limit-day-thu-${optionNum}">${lang('thursday_short')}</label>
            <input type="checkbox" data-short-text="${lang('friday_short')}" name="limitDay" id="limit-day-fri-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="5" />
            <label for="limit-day-fri-${optionNum}">${lang('friday_short')}</label>
            <input type="checkbox" data-short-text="${lang('saturday_short')}" name="limitDay" id="limit-day-sat-${optionNum}" class="limit-day js-post-value"
                   onchange="registrationRestrictions.dayLimitClicked(this)" value="6" />
            <label for="limit-day-sat-${optionNum}">${lang('saturday_short')}</label>
    </div>`;

    },

    //remove subscription block
    removeBlock: function (elem)  {
        const block = $(elem).attr("disabled", true).closest('.membership-club-limit-row-block');
        const blockList = block.closest('#membership-club-limit-blocks-list');

        blockList.closest('#membership-club-limit-section')
            .find('#membership-club-limit-header a:first').removeClass('bsapp-js-disabled-o');
        blockList.find('.membership-club-limit-row-block select.membership-club-limit-type')
            .removeClass('bsapp-js-disabled-o');

        block.find(':input').val('').trigger('change');
        //if removed last than none restriction show
        if(blockList.find('.membership-club-limit-row-block').length === 1) {
            const defaultBlock = blockList.closest('#membership-club-limit-section')
                .find('#default-membership-club-limit-block');
            $(defaultBlock).find('input.no-limit-input').val(1).trigger('change').addClass('js-post-value');
            shopMaim.showFlex(defaultBlock);
            blockList.closest('#custom-membership-club-limit-blocks').addClass('d-none');
            shopMaim.hideFlex($(elem).closest('.registration-restrictions-option-section')
                .find('.limit-based-availability-section'));
            block.attr('data-id',0);
        } else {
            block.remove();
        }
    },
    //Checks if the closest Initialized bsappMultiSelect is empty
    checkSelectEmpty: function (elem) {
        return $(elem).closest('.bsapp--sel').find('.bsapp--sel__box input[type=checkbox]:not(:disabled)').length === 0;
    },


    /********************* limit based availability *********************/
    //Displays the pop-up and the rest of the html make disabled
    openLimitBasedAvailabilityPopup: function (editElem= null) {
        this.mainElem.find('#registration-restrictions-page:first').addClass('bsapp-js-disabled-o');
        this.mainElem.find('#limit-based-availability-background').removeClass('d-none')
            .find('#limit-based-availability-modal').addClass('popupDisplayOn');
        //if param of this function null -> no data yet use default
        const data = editElem ? this.createDataToLimitBasedAvailability(editElem) : this.DEFAULTS_LIMIT_BASED_AVAILABILITY_DATA;
        this.setDataToLimitBasedAvailabilityPopup(data);
    },
    //Searches for the values of the block and from them create the data
    createDataToLimitBasedAvailability: function (elem) {
        let data = {}
        $(elem).closest('.based-availability-limit-block').find('.based-availability-limit-inputs :input')
            .each(function () {
                data[this.name + '-popup'] = this.value;
            });
        return data;
    },

    //set the data in popup fields according to data object
    setDataToLimitBasedAvailabilityPopup:function (data) {
        this.mainElem.find('#limit-based-availability-modal:first .limit-based-availability-inputs-section .limit-based')
            .each(function () {
                if($(this).attr('name') === 'basedAvailabilityAmount-popup') {
                    createClubMemberships.checkOptionAddSetSelected($(this), data[this.name], data[this.name]);
                } else if($(this).attr('name') === 'basedAvailabilityTimeBefore-popup') {
                    const timeType = `${data[this.name]}-${data['basedAvailabilityTypeTimeBefore-popup']}`;
                    createClubMemberships.checkOptionAddSetSelected($(this),
                        `${timeType}`,
                        `${createClubMemberships.validityMembershipToText(timeType, -2)}` );
                } else {
                    $(this).val(data[this.name]).trigger('change');
                }
            });
    },

    ////hide the pop-up and the rest of the html make clickable
    closeLimitBasedAvailabilityPopup: function () {
        this.mainElem.find('#registration-restrictions-page:first').removeClass('bsapp-js-disabled-o');
        this.mainElem.find('#limit-based-availability-background').addClass('d-none')
            .find('#limit-based-availability-modal').removeClass('popupDisplayOn');
    },

    //Add a block of limit based availability - get data form popup
    addLimitBasedAvailability: function (elem) {
        const sectionOfInputs = $(elem).closest('#limit-based-availability-modal')
            .find('.limit-based-availability-inputs-section');
        const data  = this.createObjectOfAvailabilityLimitData(sectionOfInputs);
        this.addLimitBasedAvailabilityFromObject(data);
    },

    //Add a block of limit based availability - get object data
    addLimitBasedAvailabilityFromObject: function (data) {
        const activeLimitSection = this.mainElem
            .find(`#registration-restrictions-section .tab-body-option.active:first #membership-club-limit-section`);
        const availabilityLimitRow = $(activeLimitSection).find('.based-availability-limit-row-block');

        if(data['basedAvailabilityTypeTimeBefore']) {
            data['basedAvailabilityTimeBefore'] +=  `-${data['basedAvailabilityTypeTimeBefore']}`;
            delete data["basedAvailabilityTypeTimeBefore"];
        }

        //set in the active tab inputs of availability limit
        for (const [key, value] of Object.entries(data)) {
            if(key === 'basedAvailabilityTimeBefore') {
                const words = value.split('-');
                if(words.length > 1) {
                    availabilityLimitRow.find(`.based-availability-limit-inputs input[name=basedAvailabilityTypeTimeBefore]`)
                        .addClass('js-post-value').val(words[1]);
                }
                availabilityLimitRow.find(`.based-availability-limit-inputs input[name=${key}]`)
                    .addClass('js-post-value').val(words[0]);
            }else {
                availabilityLimitRow.find(`.based-availability-limit-inputs input[name=${key}]`)
                    .addClass('js-post-value').val(value);
            }

        }

        //hide footer
        shopMaim.hideFlex($(activeLimitSection).closest('.registration-restrictions-option-section')
            .find('.limit-based-availability-section:first'));
        $(activeLimitSection).addClass('no-footer');

        let newLimitText = this.getBlockAvailabilityTextFromMap(data);

        //show block
        $(availabilityLimitRow).removeClass('d-none')
            .find('.based-availability-limit-block:first .block-limit-text:first').text(newLimitText);

        this.closeLimitBasedAvailabilityPopup();
    },


    //Deletes - hides the block and updates all inputs to empty
    removeBlockBasedAvailability: function (elem) {
        const block = $(elem).attr("disabled", true).closest('.based-availability-limit-row-block');
        //set all inputs empty and hide the block
        block.addClass('d-none').find(':input').removeClass('js-post-value').val('').trigger('change');
        shopMaim.showFlex(block.closest('.registration-restrictions-option-section')
            .find('.limit-based-availability-section:first'));
        block.closest('#membership-club-limit-section').removeClass('no-footer');

    },

    //Receives the parent element of the inputs and returns a map with the names and values of the inputs
    createObjectOfAvailabilityLimitData: function (elem) {
        const data  = {}
        //Inserts to data map the values and names of inputs form the popup
        $(elem).find(':input').each(function () {
            data[this.name.replace('-popup','')]= this.value
        });
        return data;
    },

    //create from map the text that display in a block
    getBlockAvailabilityTextFromMap: function(data) {
        let text = `${data.basedAvailabilityAmount} ${lang('entries')} ${lang('and_allow_only')} `;
        if(data.basedAvailabilityTypeTimeBefore) {
            text += `${data.basedAvailabilityTimeBefore} ${this.basedAvailabilityTypeTimeBeforeToText(data.basedAvailabilityTypeTimeBefore)}`
        }else {
            text += `${this.basedAvailabilityTypeTimeBeforeToText(data.basedAvailabilityTimeBefore)}`
        }
        return text;
    },

    basedAvailabilityTypeTimeBeforeToText: function (valueAndType){
        try {
            const words = valueAndType.split('-');
            let type = valueAndType , text = '';
            if(words.length > 1) {
                text = words[0];
                type = words[1];
            }
            switch (type) {
                case '1':
                    return text + ' ' + lang('min_before_class');
                case '2':
                    return text + ' ' + lang('hours_before_start_class');
                case '3':
                    return text + ' ' + lang('days_before_class');
                default:
                    return text + ' ' +  lang('hours_before_start_class');
            }
        } catch (e) {
            return '';
        }
    }
}





