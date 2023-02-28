/***  constant: global **/
const TYPE_MINUTE = 0 , TYPE_HOURE = 1 , TYPE_DAY = 2;

/***  constant: meeting- general settings **/
const CANCEL_LAW_NEVER = '4', CANCEL_LAW_ALWAYS = '5', CANCEL_LAW_MANUAL = '6';

/***  constant: meeting- templates **/
const EXTERNAL_REGISTRATION_DISABLED = '0', EXTERNAL_REGISTRATION_ACTIVE ='1';
const NO_SESSIONS_LIMIT = '0', WITH_SESSIONS_LIMIT = '1'
const MEETING_TYPE_PHYSICAL = '0', MEETING_TYPE_ONLINE = '1', MEETING_TYPE_ZOOM = '2';
const NONE_PREPARATION = '0', AFTER_PREPARATION = '1', BEFORE_PREPARATION = '2';

/***  constant: meeting- cancellation policy **/
const GROUP_CUSTOMER_TAG = '0', GROUP_CUSTOMER_ENTRY = '1', GROUP_CUSTOMER_STATUS = '2';
const TYPE_GROUP_CUSTOMERS_LEVEL = '0' , TYPE_GROUP_CUSTOMERS_ENTRIES = '1' , TYPE_GROUP_CUSTOMERS_STATUS = '2'
    ,TYPE_GROUP_CUSTOMERS_DEFAULT = '3';
const NO_CHARGE = '0' , MANUAL_CHARGE = '1' , FULL_CHARGE = '2';
const CLIENT_STATUS_ACTIVE = '0', CLIENT_STATUS_ARCHIVE = '1', CLIENT_STATUS_INTERESTED = '2';
const ACTION_DISABLED = true, ACTION_ACTIVE = false;

/***  constant: meeting- staff **/
const REPEAT_STATUS_OFF = '0', REPEAT_STATUS_WEEK = '1';
const REPEAT_FOREVER = '0', REPEAT_END_DATE = '1';
const EDIT_MODE_ONLY_THIS = '0', EDIT_MODE_AS_SEQUENCE = '1';
const EDIT_MODE_END_BY_DATE = '0', EDIT_MODE_END_BY_AMOUNT = '1', EDIT_MODE_END_INFINITE ='2';


var levelMap = {};
var colorMap = {};
var categoryMap = {};


var calendar = [];
var coaches = [];
var optionsCalendar = [];
var optionsCoachs = [];
var autocompleteCoachs = '';
var autocompleteCalendar = '';
var firstday = ''
var lastday = '';
// var currDate = '';
var coachId = '';
var hidenDate = '';
var Calendarid = '';
var prevDays = 0;
var isLoading = '';
var jsTimeFrom, jsTimeTo;


$('#calendarSettings > .dropdown-toggle').one('click', function () {
    const apiProps = {
        fun: 'GetClassSettingsByCompanyNum',
        CompanyNum: $companyNo
    };
    postApi('calendarSettings', apiProps, 'renderCalendarSettings');
    getOutdoor();
    getSpaceType()
    getBranchesCalendar();
    // populateClassSelect();// todo remove
    populateCalendarSelect();
    // populateCoachesSelect(); todo remove
    populateDevicesSelect();

    globalCalendarSettings.getLevels();
    globalCalendarSettings.getColors();
    globalCalendarSettings.getMeetingCategories();
    globalCalendarSettings.getTagsCategories();

    calendarsAndClasses.init(this);
    fieldEvents.loadTagsTranslation();



});


let globalCalendarSettings = {
    mainElem: $('#bsapp-calendar #calendarSettings .dropdown-menu'),
    loader:
        `<div class="form-static d-flex align-items-center justify-content-center bg-light rounded
                text-start m-0 py-15 px-10 bsapp-fs-14" id="delete-loader">
            <div class="spinner-border spinner-border-sm text-success" role="status">
                <span class="sr-only">${lang("loading")}</span>
            </div>
        </div>`,

    /**************** init maps  ****************/
    //Receipt from Data Base - colors
    getColors: function () {
        const apiProps = {
            fun: 'GetColors'
        };
        postApi('calendarSettings', apiProps, 'globalCalendarSettings.createColorsMap', true);
    },
    //Receipt from Data Base - clientLevels
    getLevels: function () {
        const apiProps = {
            fun: 'GetLevels',
            CompanyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'globalCalendarSettings.createLevelMap', true)
    },
    getCategory: function () {
        const apiProps = {
            fun: 'GetMeetingCategories',
            CompanyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'meetingCategories.createCategoryMap', true)
    },
    //Receipt from Data Base - classType
    getClassType: function () {
        const apiProps = {
            CompanyNum: $companyNo,
            fun: 'GetAllClassTypes'
        };
        postApi('calendarSettings', apiProps, 'globalCalendarSettings.select2ClassType')
    },
    //Receipt from Data Base - classType
    getMeetingCategories: function () {
        const apiProps = {
            fun: 'GetMeetingCategories',
            CompanyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'meetingCategories.createCategoryMap', true)
    },

    //Tags Categories: favorite and others
    getTagsCategories: function () {
        const apiProps = {
            fun: 'getTagsCategories',
            CompanyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'MeetingPopup.setTagsCategories', true)
    },
    //Preparing a static map and Request to update select2
    createLevelMap: function (data) {
        if(globalCalendarSettings.errorChecking(data)) {
            $.each(data.Levels, function (key) {
                levelMap[data.Levels[key].id] = data.Levels[key].Level;
            });
            this.select2Level();
        }
    },
    //Preparing a static map and Request to update select2
    createColorsMap: function (data) {
        if(globalCalendarSettings.errorChecking(data)) {
            $.each(data.Colors, function (key) {
                colorMap[data.Colors[key].id] = data.Colors[key].hex;
            });
            this.select2Color();
        }
    },

    /**************** select2 function  ****************/
    // Update select2 according to the data on the map
    select2Level: function () {
        var levels = [];
        $.each(Object.entries(levelMap), function (key, value) {
            levels.push({'id': value[0], 'name': value[1]})
        });
        var levelOptions = levels.map(lev => {
            var class_option = {
                'id': lev.id,
                'html': `<span data-id="${lev.id}">${lev.name}</span>`,
                'text': lev.name,
                'title': lev.name
            };
            return class_option
        });
        $(".calendarSettings-meetings-cancellation-policy-add-option .level-customers").select2({
            data: levelOptions,
            placeholder: lang('choose_class_type'),
            language: $("html").attr("dir") == 'rtl' ? "he" : "en",
            dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
            theme: 'bsapp-dropdown'
        })
    },
    // Update select2 according to the data on the map
    select2Color: function () {
        let colorCircles = [];
        for (const [key, value] of Object.entries(colorMap)) {
            let colors = {
                'id': key,
                'html': `<i class="fa fa-circle" style="color:${value}"></i>`,
                'text': `<i class="fa fa-circle" style="color:${value}"></i>`,
                'title': 'Class Color'
            };
            colorCircles.push(colors);
        }
        ;
        $(this.mainElem).find(".select2--colors, select.template-colors").select2({
            data: colorCircles,
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
            theme: 'bsapp-dropdown'
        });
    },
    // Update select2 according to the data on the map
    select2Category: function () {
        let categoryCircles = [];
        for (const [key, value] of Object.entries(categoryMap)) {
            let categories = {
                'id': key,
                'html': `<span data-id="${key}">${value}</span>`,
                'text': value,
                'title': value
            };
            categoryCircles.push(categories);
        }
        let selectElem = $(this.mainElem).find('.calendarSettings-meetings-templates-new #js-select2-template-category,' +
            '.calendarSettings-meetings-category-remove #js-select2-template-category');
        $(selectElem).find('option').remove();
        this.select2CategoryParam(selectElem, categoryCircles);

    },

    select2CategoryParam: function (selectElem, data=null) {
        let param = {
            tags: true,
            createTag: function (tag) {
                return {
                    id: tag.term,
                    text: tag.term,
                    isNew: true
                };
            },
            allowClear: true,
            escapeMarkup: function (markup) {
                return markup
            },
            templateResult: function (state) {
                if (state.isNew) {
                    if (!state.loading) {
                        let $state = $(
                            '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center">' + state.text + '</div><div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div></div>'
                        );
                        return $state;
                    } else {
                        return state.text;
                    }
                }
                return state.text;
            },
            templateSelection: function (item) {
                if (item.id == '') {
                    $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('choose_or_create_category') + '</div><div> </div> </div>');
                } else if (item.isNew) {
                    $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">' + lang('new') + '</div> </div> </div>');
                } else {
                    $item = $(`<div class="d-flex justify-content-between align-items-center"><div><span>  ${item.text} </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>`);
                }
                return $item;
            },
            minimumResultsForSearch: 0,
            placeholder: {
                id: "",
                placeholder: lang('choose_or_create_category')
            },
            language: $("html").attr("dir") == 'rtl' ? "he" : "en",
            theme: 'bsapp-dropdown bsapp-no-arrow'
        }
        if (data != null) {
            param['data'] = data;
        }
        $(selectElem).select2(param).on('select2:open', function() {
            $('.select2-search__field').attr('maxlength', 60);
        });

    },
    //todo change!!!
    select2ClassType: function (result) {
        var data = result.ClassTypes,
            classes = [];
        $.each(data, function (key) {
            classes.push({
                'id': data[key].id,
                'name': data[key].Type,
                'color': data[key].Color,
                'duration': data[key].duration
            })
        });
        var classesOptions = classes.map(class_option => {
            var class_option = {
                'id': class_option.id,
                'color': class_option.color,
                'duration': class_option.duration,
                'html': `<span data-id="${class_option.id}">${class_option.name}</span>`,
                'text': class_option.name,
                'title': class_option.name
            };
            return class_option
        });
        $(".bsapp-settings-dialog .select2--class-type").select2({
            data: classesOptions,
            escapeMarkup: function (markup) {
                return markup
            },
            templateResult: function (data) {
                return data.html
            },
            templateSelection: function (data) {
                return data.text
            },
            minimumResultsForSearch: 0,
            placeholder: lang('choose_class_type'),
            language: $("html").attr("dir") == 'rtl' ? "he" : "en",
            dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu'),
            theme: 'bsapp-dropdown'
        })
        $(".bsapp-settings-dialog #js-select2-template-class-type")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true
                    };
                },
                data: classesOptions,
                allowClear: true,
                templateResult: function (state) {
                    if (state.isNew) {
                        if (!state.loading) {
                            var $state = $(
                                '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center">' + state.text + '' +
                                '</div><div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div></div>'
                            );
                            return $state;
                        } else {
                            return state.text;
                        }
                    }
                    return state.text;
                },
                templateSelection: function (item) {
                    if (item.id == '') {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('exist_new_class_type') + '</div><div> </div> </div>');
                    } else if (item.isNew) {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">' + lang('new') + '</div> </div> </div>');
                    } else {
                        $item = $(`<div class="d-flex justify-content-between align-items-center data-duration='${item.duration}' data-color='${item.color}'"><div><span>  ${item.text} </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>`);
                    }
                    return $item;

                },
                minimumResultsForSearch: 0,
                placeholder: lang('choose_class_type'),
                language: $("html").attr("dir") == 'rtl' ? "he" : "en",
                theme: 'bsapp-dropdown bsapp-no-arrow'
            })
            .on("select2:selecting", function (e) {
                const selectData = e.params.args.data;
                if (!selectData)
                    return;
                // change color in calendar field if class type have valid color
                let colorId = selectData.color ? selectData.color : '1';
                if (colorId.includes('#')) {
                    colorId = globalCalendarSettings.getKeyByValue(colorMap, colorId) ?? '1';
                }
                $('.calendarSettings-meetings-templates-new select.template-colors').val(colorId).trigger('change');
                $('.calendarSettings-meetings-templates-new #js-content-new-template [name="NewClassType"]').val(0);
                if (selectData.isNew) { //New class type created
                    let modal = $('#js-modal-new-class-type-meeting').appendTo("body").modal('show');

                    //get all memberships and create options
                    $.ajax({
                        method: 'GET',
                        url: '/office/partials-views/add-meeting/modal-new-class-type.php',
                        success: function (res) {
                            $(modal).find('.data-content .membership-container').html(res);
                            $(modal).find('.js-loader').removeClass('d-flex').addClass('d-none');
                            $(modal).find('.data-content').removeClass('d-none');
                            fieldEvents.showTagInfo();
                        }
                    })
                }
            });
    },


    /**************** helper function  ****************/
    changeDirection: function (elem) {
        const rtl_rx = /^\s*([$&+,:;=?@#|'<>.^*()%!-])*([0-9])*([a-zA-Z]+)/;
        elem.style.direction = rtl_rx.test(elem.value) ? 'ltr' : 'rtl';
    },
    fromMinutesToFormatTime: function (minute) {
        const hours = Math.trunc(minute / 60);
        const minutes = minute % 60;
        const timeType = hours > 0 ? lang('shortening_hours') : lang('shortening_minute');
        let timeText;
        if (minutes === 0) {
            timeText = hours + " " + timeType;
        } else if (hours === 0) {
            timeText = minutes + " " + timeType;
        } else {
            timeText = hours + ":" + minutes + timeType;
        }
        return timeText;
    },
    getDurationText: function (durationArray) {
        const min = Math.min(...durationArray);
        let durationText = globalCalendarSettings.fromMinutesToFormatTime(min);
        if (durationArray.length > 1) {
            let max = Math.max(...durationArray);
            durationText += ' - ' + globalCalendarSettings.fromMinutesToFormatTime(max);
        }
        return durationText;
    },
    setTwoNumberDecimal: function (elem) {
        elem.value = parseFloat(elem.value).toFixed(2);
    },
    typeAndTimeToMinute: function (type, value) {
        value = parseInt(value);
        type = parseInt(type);
        switch (type) {
            case TYPE_MINUTE :
                return value;
            case TYPE_HOURE:
                return value * 60;
            case TYPE_DAY:
                return value * 60 * 24
        }
    },
    setRequiredAndVisibility: function (elem, toShow) {
        if (toShow) {
            $(elem).show();
            $(elem).prop('required', true);
        } else {
            $(elem).hide();
            $(elem).prop('required', false);
        }
    },

    //Receiving the level according to the id
    findLevelById: function (id) {
        let levelName = levelMap[id] ? levelMap[id] : 'לא נמצא';
        return levelName;
    },
    //Receiving the color according to the id
    findColorHexById: function (id) {
        return colorMap[id] ? colorMap[id] : '#ad53ff'; //todo change default
    },
    //Receiving the key of object according the value
    getKeyByValue: function (object, value) {
        return Object.keys(object).find(key =>
            object[key] === value);
    },


    //Error checking, if detected shows the cause and return false
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

    //hide or show delete element and display loader if necessary
    toggleDeleteLoader: function (deleteElem, toDelete) {
        if (toDelete) {
            $(deleteElem).children().first().addClass('d-none').removeClass('d-flex');
            $(deleteElem).append(this.loader);
        } else {
            $(deleteElem).find('#delete-loader').remove();
            $(deleteElem).children().first().addClass('d-flex').removeClass('d-none');
        }
    },
    disabledButton: function (elem) {
        $(elem).addClass('disabled');
        setTimeout(function () {
            // enable click after 1 second
            $(elem).removeClass('disabled');
        }, 500);
    },

    classesDisplayOptions: function (elem) {
        const closestLi = $(elem).closest('li');
        const firstCollapsedDiv = $(closestLi.find('.collapse')[0]);
        const secondCollapsedDiv = $(closestLi.find('.collapse')[1]);

        if(elem.selectedIndex == 1) {
            firstCollapsedDiv.slideUp(200);
        } else {
            firstCollapsedDiv.slideDown(200);
            closestLi.closest('.scrollable').animate({
                scrollTop: firstCollapsedDiv.offset().top
            },200);
        }

        if(elem.selectedIndex == 0) {
            secondCollapsedDiv.slideUp(200);
        } else {
            secondCollapsedDiv.slideDown(200);
            closestLi.closest('.scrollable').animate({
                scrollTop: secondCollapsedDiv.offset().top
            },200);
        }
    }
}


/***** meetings  *****/
let meetingGeneralSettings = {
    mainElem: null,
    //Performed once, receiving general settings and updating select2
    init: function (elem){
        if(!this.mainElem) {
            this.mainElem = $(elem).parents('.dropdown-menu').find('.calendarSettings-meetings-general-settings');
            $(elem).parents('#calendarSettings').find(".js-select2-dropdown-arrow-template").select2({
                theme: "bsapp-dropdown",
                minimumResultsForSearch: -1,
            });
            this.getGeneralSettings();
        }
    },
    // get General Settings from db
    getGeneralSettings: function() {
        const apiProps = {
            fun: 'GetGeneralSettings',
            CompanyNum: $companyNo
        }
        postApi('calendarSettings', apiProps, 'meetingGeneralSettings.renderGeneralSettingsData', true);
    },
    // render general settings from data
    renderGeneralSettingsData: function(data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            //if was error close the settings
            this.mainElem = null;
            $("#calendarSettings .js-close-calendar-settings").click();
            return;
        }

        let generalSettings =  data.response;
        $(this.mainElem).attr('data-id',generalSettings.id);
        //loop over setting data and set input fields
        Object.entries(generalSettings).forEach(entry => {
            const [key, value] = entry;
            $(this.mainElem).find(`.list-of-settings :input[name="${key}"]`).val(value).trigger('change');
        });
        $(this.mainElem).find(`.list-of-settings :input[name="ExternalOrderingRange"] option[time-type="$ExternalOrderingRangeType"][value=${generalSettings.ExternalOrderingRangeType}]`).val(generalSettings.ExternalOrderingRange).trigger('change');

        //hide loading and show setting
        $(this.mainElem).find('ul.list-of-loading').addClass('d-none');
        $(this.mainElem).find('ul.list-of-settings').removeClass('d-none');
        if(generalSettings.PreOrderType === '1' && generalSettings.PreOrderTime !== '0') {
            $(this.mainElem).find('select.pre-order')
                .val('d-' + generalSettings.PreOrderTime).trigger('change'); //fix PreOrderType day format
        }
        // After changing settings, a save button is displayed
        $(this.mainElem).find('.list-of-settings :input').change(function (){
            let saveElem = $(this).parents('.calendarSettings-meetings-general-settings').find('.js-save-meeting-settings');
            saveElem.removeClass('d-none');
            saveElem.find('.save-meeting-settings').removeClass('disabled').text(lang('save_changes_button'));
            $(this).addClass('changed');
        });
    },
    // update the db after validation
    updateGeneralSettings:function (elem) {
        $(elem).addClass("disabled").text(lang('loading_datatables'));
        let apiProps = {};
        apiProps['fun'] = 'UpdateGeneralSettings';
        apiProps['id'] = $(this.mainElem).attr('data-id');
        let changedFields = $(this.mainElem).find('ul.list-of-settings :input.changed');
        $(changedFields).each(function() {
            apiProps[$(this).attr("name")] = $(this).val();
        });
        if(apiProps.PreOrderTime) {
            apiProps.PreOrderTime = apiProps.PreOrderTime.replace(/^(d-)/,""); //fix PreOrderType day format
        }
        postApi('calendarSettings', apiProps,'meetingGeneralSettings.afterSave', true);

    },
    // reset changed flag hide save button and back to meeting navigation
    afterSave: function(data) {
        this.mainElem.find('.save-meeting-settings')
            .removeClass('disabled')
            .text(lang('save_changes_button'));
        if (!globalCalendarSettings.errorChecking(data)) {
            return;
        } else {
            //reset the changed flag
            $(this.mainElem).find('ul.list-of-settings :input').removeClass('changed');
            let saveElem = $(this.mainElem).find('.js-save-meeting-settings');
            saveElem.addClass('d-none');
            switchSettingsPanel($(saveElem), 'Meetings-navigation');
        }
    },
    /*********************** Send reminder ***********************/
    // When "Send Reminder status" changes -  hide/show "schedule send reminder"
    sendReminderStatusChanged: function (elem) {
        let status = $(elem).find(':selected').val();
        switch (status) {
            case '0':
                $(elem).parent().find('.more-details').addClass('d-none');
                break;
            case '1':
                $(elem).parent().find('.more-details').removeClass('d-none');
                break;
        }
    },
    /*********************** Preorder ***********************/
    // When "pre order" changes -  set value in hidden input"
    preOrderChanged: function (elem) {
        let selectedElem = $(elem).find(':selected');
        let status =$(elem).parent().find('#pre-order-status');
        if(selectedElem.hasClass('possible')) {
            status.val(0).trigger('change');
        } else if(selectedElem.hasClass('hour')) {
            if(status.val() === '0'){
                status.val(1).trigger('change')
            }
            $(elem).parent().find('#pre-order-type').val(0).trigger('change');
        } else if(selectedElem.hasClass('day')){
            if(status.val() === '0'){
                status.val(1).trigger('change')
            }
            $(elem).parent().find('#pre-order-type').val(1).trigger('change');
        }
    },

    /*********************** External Ordering Range ***********************/
    // When "Send Reminder status" changes -  hide/show "schedule send reminder"
    externalOrderingRangeChanged: function (elem) {
        let typeInputElem = $(elem).closest('.external-ordering-range-section').find('#js-external-ordering-range-type');
        const type = $(elem).find(':selected').attr('time-type');
        switch (type){
            case '1':
                typeInputElem.val(1).trigger('change');
                break;
            case '2':
                typeInputElem.val(2).trigger('change');
                break;
            default:
                typeInputElem.val(2).trigger('change');
                break;
        }
    }
}
let meetingTemplate = {
    mainElem: null,
    addTemplateElem: null,
    addTemplateAdvancedElem: null,

    /*********************** GeT templates ***********************/
    //get all Templates of this company
    getAllTemplates: function (elem) {
        globalCalendarSettings.disabledButton(elem);
        this.mainElem = $(elem).closest('.dropdown-menu').find('.all-templates:first');
        this.addTemplateElem = $(elem).closest('.dropdown-menu')
            .find('.calendarSettings-meetings-templates-new:first');
        this.addTemplateAdvancedElem = $(elem).closest('.dropdown-menu')
            .find('.calendarSettings-meetings-templates-advanced-settings:first');
        //remove template front and show loader
        $(this.mainElem).find('.templates-list').children().not(".item-loading").remove();
        $(this.mainElem).find('.templates-list').children('.item-loading').show();
        this.getCalendarsOptions();
        const apiProps = {
            fun: 'GetAllTemplates',
            CompanyNum: $companyNo
        }
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderAllTemplates', true);
    },
    //render template option
    renderAllTemplates: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.mainElem).find('.templates-list'), 'Meetings-navigation');
            return;
        }
        let result = data.templates;
        if (result.length) {
            $.each(result, function (key) {
                result[key]['durationText'] = globalCalendarSettings.getDurationText(result[key]['duration'])
                $(meetingTemplate.mainElem).find('.templates-list').append(meetingTemplate.templatesRow(result[key]));
            })
        } else {
            if ($(this.mainElem).find('#templates-not-found').length === 0)
                $(this.mainElem).find('.templates-list').append(`<li class="mb-10 animated fadeInUp"><div id="templates-not-found" class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_templates_found')}</div></li>`)
        }
        //hide loader
        $(this.mainElem).find('.templates-list').children('.item-loading').hide()
    },

    /***********************  List template action ***********************/
    //check how much link item has to this template
    checkBeforeDeleteRowTemplate: function (elem) {
        let template = $(elem).closest('.js-template-row'),
            template_id = template.attr('data-id');
        //display loader
        globalCalendarSettings.toggleDeleteLoader(template, true);
        const apiProps = {
            fun: 'CountItemLinkToTemplate',
            id:template_id
        };
        postApi('calendarSettings', apiProps, 'meetingTemplate.warningDeleteTemplate', true);
    },
    //show warning page with the number of item link to this template
    warningDeleteTemplate: function (data) {
        let listTemplate = $(this.mainElem).find(".templates-list");
        if (!globalCalendarSettings.errorChecking(data)) {
            let elem = $(listTemplate).find('#delete-loader').parent();
            globalCalendarSettings.toggleDeleteLoader(elem, false)
            return;
        }
        //if find some item link to template show warning
        let removeClassTypeMainElem = $(globalCalendarSettings.mainElem)
            .find('.meetings-template-class-type-remove');
        if(data.count > 0) {
            let removeClassTypeElem = $(removeClassTypeMainElem).find('.class-type-confirm-deleted-section');
            //change button to template remove
            $(removeClassTypeMainElem).find('[data-target]').attr('data-target','all-templates');
            $(removeClassTypeMainElem).find('.js-remove-class-type-button').attr('onclick',`meetingTemplate.deleteRowTemplate(${data.id}, this)`);
            //change text in remove Class Type page
            $(removeClassTypeElem).find('.class-type-name').text(data.name);
            $(removeClassTypeElem).find('.payment-option-text').text("");
            $(removeClassTypeElem).find('.link-item-text').text(lang('this_template_existing'));
            $(removeClassTypeElem).find('.item-link-count').text(data.count);
            $(removeClassTypeElem).find('.delete-explanation').text(lang('confirm_delete_template_message'));
            switchSettingsPanel($(listTemplate), 'meetings-template-class-type-remove');
        } else {
            this.deleteRowTemplate(data.id ,$(removeClassTypeMainElem).find('.js-remove-class-type-button') );
        }
    },
    //delete from db template and class type
    deleteRowTemplate: function (id, elem) {
        globalCalendarSettings.disabledButton(elem);
        let classTypeElem =$(elem).closest('.meetings-template-class-type-remove').find('.class-type-confirm-deleted-section');
        globalCalendarSettings.toggleDeleteLoader($(classTypeElem).parent(), true);
        const apiProps = {
            fun: 'ChangeStatusToTemplateId',
            id: id,
            Status: 0
        }
        postApi('calendarSettings', apiProps, 'meetingTemplate.removeRowFront', true);
    },
    //hide/show  row of template - call 'ChangeStatusToTemplateId' to change status to 1 or 2
    hideRowTemplate: function (elem) {
        let template = $(elem).closest('.js-template-row'),
            template_id = template.attr('data-id'),
            template_status = (template.hasClass('disabled') || template.hasClass('disabled-style')) ? 1 : 2;
        const apiProps = {
            fun: 'ChangeStatusToTemplateId',
            id: template_id,
            Status: template_status
        }
        postApi('calendarSettings', apiProps)
    },
    //remove the row of template in front
    removeRowFront: function (res) {
        let removeClassTypeElem = $(globalCalendarSettings.mainElem)
            .find('.meetings-template-class-type-remove .class-type-confirm-deleted-section')
        globalCalendarSettings.toggleDeleteLoader($(removeClassTypeElem.parent()), false);
        $(removeClassTypeElem).removeClass('d-flex');
        if (!globalCalendarSettings.errorChecking(res)) {
            return;
        }
        switchSettingsPanel($(removeClassTypeElem), 'all-templates');
        let listTemplate = $(this.mainElem).find(".templates-list");
        if (!globalCalendarSettings.errorChecking(res)) {
            let elem = $(listTemplate).find('#delete-loader').parent();
            globalCalendarSettings.toggleDeleteLoader(elem, false);
            return;
        }
        $(listTemplate).find(`.js-template-row[data-id='${res.id}']`).remove();
        if ($(listTemplate).find('.js-template-row').length === 0) {
            if ($(listTemplate).find('#templates-not-found').length === 0) {
                $(listTemplate).append(`<li class="mb-10 animated fadeInUp"><div id="templates-not-found" class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_templates_found')}</div></li>`)
            } else {
                $(listTemplate).find("#templates-not-found").parent('li').removeClass('d-none');
            }
        }

    },
    //edit template
    editTemplate: function (elem) {

        let template_id = $(elem).parents('.js-template-row').attr('data-id');
        $(this.addTemplateElem).find('#list-template-basics-list').click();
        //change path title
        $(this.addTemplateElem).find('.path-title').text(lang('path_edit_appointments'));
        $(this.addTemplateElem).find('.save-app-template').text(lang('save_changes_button'))
            .addClass('edit-app-template').removeClass('disabled').attr('data-id', template_id);
        //show loader
        this.showNewTemplateLoader(true);
        const apiProps = {
            fun: 'GetTemplateById',
            id: template_id
        };
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderTemplatesData', true);
    },
    //render template data into basics tab and into advanced tab
    renderTemplatesData: function (result) {
        if (!globalCalendarSettings.errorChecking(result)) {
            switchSettingsPanel($(this.addTemplateElem).find('.save-app-template'), 'all-templates');
            return;
        }
        let templateData = result.Template.templateData;
        let durationPriceArray = result.Template.durationPrice;
        let coaches = result.Template.coaches;
        let calendars = result.Template.calendars;
        /**  Basics Tab **/

        let basicsTab = $(this.addTemplateElem).find('#list-template-basics');
        //change template name
        $(basicsTab).find('#template-name').val(templateData.TemplateName);
        //change class type
        $(basicsTab).find('#js-select2-template-category').val(templateData.CategoryId).trigger('change');
        //change color id
        $(basicsTab).find('select.template-colors').val(templateData.ColorId).trigger('change');
        $("#js-entrance-price-block .tagInfo").removeClass("d-none"); //show tag field
        let tagId = templateData.TagsId;
        let tagName = tagsTranslations.find((o) => {
            return o["id"] === tagId && o["lang"] === lag
        });
        if (typeof tagName === 'undefined') {
            tagName = tagsTranslations.find((o) => {
                return o["id"] === tagId
            });
            if (typeof tagName === 'undefined') {
                tagName =  tagsTranslations.find((o) => {return o["id"] === '41'});
                tagId = 41;
            }
        }
        fieldEvents.setTag(tagId, tagName.text);

        /** edit Dynamic payment option **/
        let addButton = $(basicsTab).find('.js-add-payment-option');
        this.removeAllPaymentOption();
        // loop over duration amd price data and create blocks
        if (durationPriceArray?.length > 0) {
            durationPriceArray.forEach(function (element, index) {
                let durationMinute = globalCalendarSettings.typeAndTimeToMinute(element.durationType, element.duration);
                meetingTemplate.addPaymentOption(addButton, durationMinute, element.Price, element.id);
                $(basicsTab).find(`.price-duration-block[data-id='${element.id}'] .template-duration`).val(durationMinute).trigger('change');

            });
        }
        this.updateRemoveAndAddButtons();

        /**  Advanced Tab **/
        let advancedPage = $(this.addTemplateAdvancedElem);
        //change External registration
        $(advancedPage).find('#external-registration-section .registration-limited-to').val(templateData.RegistrationLimitedTo).trigger('change');
        $(advancedPage).find('#external-registration-section .external-registration-status').val(templateData.ExternalRegistration).trigger('change');
        //Sessions limit
        if (templateData.SessionsLimit != 0) {
            $(advancedPage).find('#sessions-limit-section .sessions-limit-status').val(1).trigger('change');
            $(advancedPage).find('#sessions-limit-section .sessions-limit-number').val(templateData.SessionsLimit).trigger('change');
        } else {
            $(advancedPage).find('#sessions-limit-section .sessions-limit-status').val(0).trigger('change');
        }
        // Online options
        //online
        if (templateData.MeetingType == 1) {
            $(advancedPage).find('#online-options-section .online-reminder-value').val(templateData.OnlineReminderValue).trigger('change');
            $(advancedPage).find('#online-options-section .live-class-link').val(templateData.LiveClassLink).trigger('change');
            $(advancedPage).find('#online-options-section .online-reminder-type').val(templateData.OnlineReminderType).trigger('change');
            $(advancedPage).find('#online-options-section .online-send-type').val(templateData.OnlineSendType).trigger('change');
            //zoom
        } else if (templateData.MeetingType == 2) {
            $(advancedPage).find('#online-options-section .zoom-meeting-number').val(templateData.ZoomMeetingNumber).trigger('change');
            $(advancedPage).find('#online-options-section .zoom-meeting-password').val(templateData.ZoomMeetingPassword).trigger('change');
        }
        $(advancedPage).find('#online-options-section .online-options-type').val(templateData.MeetingType).trigger('change');
        //Preparation time
        if (templateData.PreparationTimeStatus != 0) {
            $(advancedPage).find('#preparation-time-section .preparation-time-value').val(templateData.PreparationTimeValue).trigger('change');
            $(advancedPage).find('#preparation-time-section .preparation-time-type').val(templateData.PreparationTimeType).trigger('change');
        }
        $(advancedPage).find('#preparation-time-section .preparation-time-status').val(templateData.PreparationTimeStatus).trigger('change');
        //coaches
        let coachesList = $(advancedPage).find('#coaches-limit-section .coaches-list');
        // remove list of coaches
        coachesList.children().remove()
        $(advancedPage).find('#coaches-limit-section #js-all-coaches-selected').prop('checked', false)
        if (coaches?.length > 0) {
            coaches.forEach(coach => {
                $(coachesList).append(this.coachRow(coach, templateData.id))
            })
            this.coachSelectedChanged();
            if (templateData.AllCoaches == 1) {
                $(advancedPage).find('#coaches-limit-section #js-all-coaches-selected').prop('checked', true).trigger('change');
            }
        } else {
            $(coachesList).append('<li class="mb-10 animated fadeInUp"><div id="coach-not-found" class="form-static d-flex align-items-center justify-content-center text-danger rounded text-start m-0 py-8 px-10 bsapp-fs-18">' + lang("no_coaches_found") + '</div></li>')
        }

        //calendars
        //If the calendars are set manually
        if (templateData.AllCalendars == 0) {
            $(advancedPage).find('#calendars-limit-section #js-all-calendars-selected').prop('checked', false).trigger('change');
            $(advancedPage).find('#calendars-limit-section .calendar-list input[name="js-calendar-id"]').map(function () {
                if (calendars.includes($(this).val())) {
                    $(this).prop('checked', true).trigger('change');
                } else {
                    $(this).prop('checked', false).trigger('change');
                }
            });
        } else {
            $(advancedPage).find('#calendars-limit-section #js-all-calendars-selected').prop('checked', true).trigger('change');
        }

        //more info
        $(advancedPage).find('#more-info-section #more-info-text').text(templateData.MoreInfoText);

        // reset changed flag in all inputs
        $(advancedPage).find(':input').removeClass('changed');
        $(basicsTab).find(':input').removeClass('changed');
        // After changing settings, a save button is displayed
        $(advancedPage).find(':input').change(function (){
            $(this).addClass('changed');
        });
        $(basicsTab).find(':input').change(function (){
            $(this).addClass('changed');
        });

        this.showNewTemplateLoader(false);
    },
    //render empty form of templates
    renderEmptyNewTemplateForm: function () {
        $('#js-content-new-template input[name=tag]').val(null);
        let apiProps = {
            fun: 'GetCoachesByCompanyNum',
            companyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderDefaultCoaches', true);
        // show basics list tab
        $(this.addTemplateElem).find('#list-template-basics-list').click();
        this.showNewTemplateLoader(true);
        //change path title
        $(this.addTemplateElem).find('path-title').text(lang('path_new_appointments'));
        $(this.addTemplateElem).find('.save-app-template').text(lang('save'))
            .removeClass(['edit-app-template', 'disabled']).attr('data-id', -1);

        /** Basics Tab **/
        let basicsTab = $(this.addTemplateElem).find('#list-template-basics');
        //change template name
        $(basicsTab).find('#template-name').val("");
        //change class type
        $(basicsTab).find('#js-select2-template-category').val('').trigger('change');
        //change color id
        $(basicsTab).find('select.template-colors').val(1).trigger('change');
        this.removeAllPaymentOption();

        $(basicsTab).find('.border-danger').removeClass('border-danger');
        let addButton = $(basicsTab).find('.js-add-payment-option');
        meetingTemplate.addPaymentOption(addButton, 60, 0, 0);
        let defaultBlock = $(basicsTab).find('.payment-options-section .price-duration-block:first');
        $(defaultBlock).find('.template-duration').parent('div').removeClass('disabled');
        $(defaultBlock).find('.remove-button').removeClass('d-flex').addClass('d-none');

        /**  Advanced Tab **/
        let advancedPage = $(this.addTemplateAdvancedElem);

        //change External registration
        $(advancedPage).find('#external-registration-section .external-registration-status').val(1).trigger('change');
        $(advancedPage).find('#external-registration-section .registration-limited-to').val(0).trigger('change');
        //Sessions limit
        $(advancedPage).find('#sessions-limit-section .sessions-limit-status').val(0).trigger('change');
        // Online options
        $(advancedPage).find('#online-options-section .online-options-type').val(0).trigger('change');
        //Preparation time
        $(advancedPage).find('#preparation-time-section .preparation-time-status').val(0).trigger('change');
        //more info
        $(advancedPage).find('#more-info-section #more-info-text').text("");
        $(advancedPage).find('#more-info-section #more-info-text').attr("placeholder", lang('free_string'))
        //calendars - all checked
        $(advancedPage).find('#calendars-limit-section #js-all-calendars-selected').prop('checked', true).trigger('change');

        let onlineSection  = $(advancedPage).find('#online-options-section');
        $(onlineSection).find('.not-physical-meeting input').val('');

        //show loader
        this.showNewTemplateLoader(false);
        $("#js-content-new-template .tagInfo").addClass('d-none');
    },
    // hide or show loader
    showNewTemplateLoader: function (toShow = true) {
        if (toShow) {
            $(this.addTemplateElem).find('#js-content-new-template').addClass('d-none');
            $(this.addTemplateElem).find('#js-loader-new-template').removeClass('d-none');
        } else {
            $(this.addTemplateElem).find('#js-content-new-template').removeClass('d-none');
            $(this.addTemplateElem).find('#js-loader-new-template').addClass('d-none');
        }
    },
    //add new template or edit existing template
    saveTemplate: function (elem) {
        $(elem).addClass("disabled");
        let isNew = true;
        let templateId = -1;
        if ($(elem).hasClass('edit-app-template')) {
            templateId = $(elem).attr('data-id');
            isNew = false;
        }
        let fields = $(elem).parents('.bsapp-settings-panel').find("[required]");
        if (validateTemplateFields(fields)) {
            $(elem).text(lang('loading_datatables'));
            this.fetchTemplateData(isNew, elem, templateId);
        } else {
            $(elem).removeClass("disabled");
        }
    },
    //function post to server template data
    fetchTemplateData: function (isNewTemplate, elem, templateId = -1) {
        // check invalid fields
        if ($(this.addTemplateElem).find('#list-template-advanced .js-coaches-limit .title-block-value').hasClass('text-danger')
            || $(this.addTemplateElem).find('#list-template-advanced .js-calendars-limit .title-block-value').hasClass('text-danger')) {
            const saveButton = $(this.addTemplateElem).find('.save-app-template');
            const buttonText = $(saveButton).hasClass('edit-app-template') ? lang('save_changes_button') : lang('save');
            saveButton.removeClass('disabled').text(buttonText);
            $('#list-template-advanced-list').click();

            return;
        }

        let apiProps= {};
        let advancedElem = $(this.addTemplateAdvancedElem);
        let baseFields = $(elem).parents('.calendarSettings-meetings-templates-new')
            .find('#js-content-new-template :input:not(.price-block)');
        let durationAndPriceBlocks = $(elem).parents('.calendarSettings-meetings-templates-new')
            .find('#js-content-new-template #list-template-basics .payment-options-section');

        let advancedPageFields = $(advancedElem).find(":input:not([type='checkbox'])");
        apiProps['TagsId'] = $(`[name="tag"]`).attr("data-tagId");
        if(isNewTemplate == 1) {
            apiProps['fun'] = 'CreateNewTemplate';
            apiProps['CompanyNum'] = $companyNo;

        } else {
            apiProps['fun'] = 'UpdateTemplate';
            apiProps['id'] = templateId;

            //get only the change fields
            baseFields = $(baseFields).map(function() {
                if($(this).hasClass('changed'))
                    return this;
            }).get();

            //get only the change fields
            advancedPageFields = $(advancedPageFields).map(function() {
                if($(this).hasClass('changed'))
                    return this;
            }).get();
        }
        //get all base inputs
        $(baseFields).each(function () {
            apiProps[$(this).attr("name")] = $(this).val();
        });

        // check if new category
        apiProps['NewCategory'] = $(this.addTemplateElem)
            .find('#js-select2-template-category :selected[data-select2-tag]').length ? 1 : 0;

        //get all prise and durations
        let classesTypeArray= [];
        //check valid duration - only one time each duration
        let durationArray = []
        $(durationAndPriceBlocks).find('.price-duration-block').each(function () {
            durationArray.push($(this).find('.template-duration').val());
            if(isNewTemplate == 1 || $(this).find('.js-template-price').hasClass('changed')) {
                let durationAndPrice = {};
                let duration = $(this).find('.template-duration:input:first').val();
                durationAndPrice['id'] = $(this).attr('data-id');
                durationAndPrice['price'] = parseFloat($(this).find('.js-template-price:input:first').val());
                durationAndPrice['duration'] = duration;
                durationAndPrice['durationText'] =  globalCalendarSettings.fromMinutesToFormatTime(duration);
                classesTypeArray.push(durationAndPrice);
            }
        })
        durationArray = durationArray.filter(function (e) {return e != null;});
        //find duplicate function
        let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) != index)
        let notValidDuration = findDuplicates(durationArray);
        //if not valid
        if (notValidDuration.length > 0) {
            $(elem).parents('.calendarSettings-meetings-templates-new:first')
                .find('.bsapp-tabs-navigation #list-template-basics-list').click();
            $(elem).parents('.calendarSettings-meetings-templates-new:first')
                .find('#js-content-new-template #list-template-basics .payment-options-section .template-duration:input')
                .each(function () {
                    if ($(this).val() == notValidDuration[0]) {
                        $(this).siblings('.input-group-append, .select2-container')
                            .addClass('border border-danger')
                    }
                });
            const saveButton = $(this.addTemplateElem).find('.save-app-template');
            const buttonText = $(saveButton).hasClass('edit-app-template') ? lang('save_changes_button') : lang('save');
            saveButton.removeClass('disabled').text(buttonText);
            return;
        }
        apiProps['classesTypeArray'] = classesTypeArray;

        //get all advanced inputs with out checkbox
        $(advancedPageFields).each(function () {
            apiProps[$(this).attr("name")] = $(this).val();
        });

        let allCoachesElem = $(advancedElem).find('#coaches-limit-section #js-all-coaches-selected[type=checkbox]');
        if (isNewTemplate == 1 || $(allCoachesElem).hasClass('changed')) {
            //get array of coaches id
            if ($(allCoachesElem).prop('checked')) {
                apiProps['AllCoaches'] = 1;
            } else {
                apiProps['AllCoaches'] = 0;
                apiProps['CoachId[]'] = $(advancedElem)
                    .find('#coaches-limit-section .coaches-list input[type=checkbox]:checked')
                    .map(function (_, el) {
                        return $(el).val();
                    })
                    .get();
            }
        }

        //get array of calendars  id
        let allCalendarsElem = $(advancedElem).find('#calendars-limit-section #js-all-calendars-selected[type=checkbox]')
        if (isNewTemplate == 1 || $(allCalendarsElem).hasClass('changed')) {
            //get array of coaches id
            if ($(allCalendarsElem).prop('checked')) {
                apiProps['AllCalendars'] = 1;
            } else {
                apiProps['AllCalendars'] = 0;
                apiProps['CalendarId[]'] = $(advancedElem)
                    .find('#calendars-limit-section .calendar-list input[type=checkbox]:checked')
                    .map(function (_, el) {
                        return $(el).val();
                    })
                    .get();
            }
        }
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderNewTemplateInList', true);

    },
    //render new template row on the list
    renderNewTemplateInList: function(data) {
        globalCalendarSettings.getCategory();
        if (!globalCalendarSettings.errorChecking(data)) {
            const saveButton = $(this.addTemplateElem).find('.save-app-template');
            const buttonText = $(saveButton).hasClass('edit-app-template') ? lang('save_changes_button') : lang('save');
            saveButton.removeClass('disabled').text(buttonText);
            $(this.addTemplateElem).find('#js-select2-template-category').val('').trigger('change');
            return;
        }
        const templateData = data.response;
        let templateList = $('.dropdown-menu .all-templates .templates-list');
        templateData['durationText'] = globalCalendarSettings.getDurationText(templateData.duration);
        //if is new one
        if (templateData.isNew == '1') {
            //remove not found block
            $(templateList).find('#templates-not-found').parent('li').addClass('d-none');
            $(templateList).append(this.templatesRow(templateData));
        } else {
            let targetRow = $(templateList).find($('.js-template-row[data-id="' + templateData.id + '"]'));
            targetRow.find('.item-label').text(templateData.TemplateName);
            targetRow.find('.item-duration').text(templateData.durationText);
            targetRow.find('.fa-circle').css('color', globalCalendarSettings.findColorHexById(templateData.ColorId));
        }
        // $(elem).removeClass('edit-app-template').removeAttr('data-id');
        switchSettingsPanel($(this.addTemplateElem).find('#js-content-new-template'), 'all-templates');
    },
    //create html row base on input
    templatesRow: function(data) {
        // Defining variables to format the template view
        let checked='', disabled='', hide_btn;
        if(data.Status == 1) {
            checked = 'checked';
            hide_btn = dropdown_hide
        } else {
            disabled = 'disabled-style';
            hide_btn = dropdown_unhide
        }
        const color = globalCalendarSettings.findColorHexById(data.ColorId);
        const markup = `<li class="mb-10 js-template-row ${disabled}" data-id="${data.id}">
        <div class="form-static d-flex align-items-center border rounded pt-7 pb-6 px-0">
            <div class="col-6 d-flex align-items-center font-weight-bold text-start pis-10 pie-0"> <i
                    class="fa fa-circle mie-7 bsapp-fs-14" style="color:${color}"></i> <span
                    class="item-label" style="overflow: hidden;text-overflow: ellipsis;
                    display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;">${data.TemplateName}</span>
            </div>
            <div class="col-3  ustify-content-between text-gray-700 px-0">
                <span class="item-duration bsapp-fs-14 text-gray-700 mie-2">${data.durationText}</span>
            </div>
    
            <div class="col-3 d-flex align-items-center justify-content-between text-gray-700 pie-10">
                <div class="custom-control custom-switch"> <input type="checkbox" onclick="meetingTemplate.hideRowTemplate(this)"
                        class="hide-toggle custom-control-input" id="template-${data.id}"
                        ${checked}/> <label class="custom-control-label" for="template-${data.id}"
                        role="button"></label>
                </div>  
                <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i
                        class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"></i>
                    <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow">
                        <ul class="list-unstyled m-0 p-0">
                            <li class="mb-6"> <a role="button"
                                    class="edit-appointment-template d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"
                                    onclick="meetingTemplate.editTemplate(this)" data-target="calendarSettings-meetings-templates-new"><i
                                        class="fal fa-edit fa-fw mx-5"></i> <span>${lang('edit')}</span></a> </li>
                            <li class="mb-6"> <a role="button" onclick="meetingTemplate.hideRowTemplate(this)" 
                                class="hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a>
                            </li>
                            <li class="mb-6"> <a role="button" onclick="meetingTemplate.checkBeforeDeleteRowTemplate(this)"
                                    class="js-delete-row-template d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                                <i class="fal fa-minus-circle fa-fw mx-5"></i>
                                <span>${lang('delete')}</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </li>`;
        return markup;
    },
    /***********************  meeting Template Basics Settings ***********************/
    /**
     * Add a payment option
     * check previous lines for validity
     * check less than 5 lines
     * update button if necessary
     *
     * @param  el The element ws click
     * @param  {Number} duration The duration
     * @param  {Number} price The price
     */
    addPaymentOption: function (el, duration, price, durationPriceId) {
        //Validation checks before adding a payment option
        let block = $(el).parent()
            .find(`.payment-options-section .price-duration-block`)
        let field = $(block).find(':input[required]');
        let valid = validateTemplateFields(field);
        let amountPaymentOptionsRow = block.length;
        //Checks if there are 5 rows already
        if (amountPaymentOptionsRow > 5 || !valid) {
            return;
        } else if (amountPaymentOptionsRow === 4) {
            //If there are 4 rows does not allow an add button
            $(el).addClass('disabled');
        } else if (amountPaymentOptionsRow === 1) {
            // if only 1 row, enable delete button for first
            $(block).find('.remove-button').removeClass("d-none").addClass("d-flex");
        }
        let priceInput = (price) ? price : '',
            durationInput = (duration) ? duration : '',
            durationPriceIdInput = (durationPriceId) ? durationPriceId : 0,
            indexInput = amountPaymentOptionsRow;
        const html = `<div class="row m-5 mb-10  price-duration-block" data-id="${durationPriceIdInput}">
        <div class="col-11 shadow rounded-lg p-10">
            <div class="row">
                <div class="col-6">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        ${lang('duration')}
                    </h6>
                </div>
                <div class="col-6">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        ${lang('price')}
                    </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-6 ${durationPriceIdInput == '0' ? '':'disabled'}">
                    <select class="js-select2-dropdown-arrow-template template-duration price-block" name="template-duration-${indexInput}" required>
                        <option data-text="5 דקות" value="5">
                        5 דקות </option>
                        <option data-text="10 דקות" value="10">
                            10 דקות </option>
                        <option data-text="15 דקות" value="15">
                            15 דקות </option>
                        <option data-text="20 דקות" value="20">
                            20 דקות </option>
                        <option data-text="25 דקות" value="25">
                            25 דקות </option>
                        <option data-text="30 דקות" value="30">
                            30 דקות </option>
                        <option data-text="35 דקות" value="35">
                            35 דקות </option>
                        <option data-text="40 דקות" value="40">
                            40 דקות </option>
                        <option data-text="45 דקות" value="45">
                            45 דקות </option>
                        <option data-text="50 דקות" value="50">
                            50 דקות </option>
                        <option data-text="55 דקות" value="55">
                            55 דקות </option>
                        <option selected="" data-text="1 שעות" value="60">
                            1 שעות </option>
                        <option data-text="1 שעות ו5 דקות" value="65">
                            1 שעות ו5 דקות </option>
                        <option data-text="1 שעות ו10 דקות" value="70">
                            1 שעות ו10 דקות </option>
                        <option data-text="1 שעות ו15 דקות" value="75">
                            1 שעות ו15 דקות </option>
                        <option data-text="1 שעות ו20 דקות" value="80">
                            1 שעות ו20 דקות </option>
                        <option data-text="1 שעות ו25 דקות" value="85">
                            1 שעות ו25 דקות </option>
                        <option data-text="1 שעות ו30 דקות" value="90">
                            1 שעות ו30 דקות </option>
                        <option data-text="1 שעות ו35 דקות" value="95">
                            1 שעות ו35 דקות </option>
                        <option data-text="1 שעות ו40 דקות" value="100">
                            1 שעות ו40 דקות </option>
                        <option data-text="1 שעות ו45 דקות" value="105">
                            1 שעות ו45 דקות </option>
                        <option data-text="1 שעות ו50 דקות" value="110">
                            1 שעות ו50 דקות </option>
                        <option data-text="1 שעות ו55 דקות" value="115">
                            1 שעות ו55 דקות </option>
                        <option data-text="2 שעות" value="120">
                            2 שעות </option>
                        <option data-text="2 שעות ו15 דקות" value="135">
                            2 שעות ו15 דקות </option>
                        <option data-text="2 שעות ו30 דקות" value="150">
                            2 שעות ו30 דקות </option>
                        <option data-text="2 שעות ו45 דקות" value="165">
                            2 שעות ו45 דקות </option>
                        <option data-text="3 שעות" value="180">
                            3 שעות </option>
                        <option data-text="3 שעות ו15 דקות" value="195">
                            3 שעות ו15 דקות </option>
                        <option data-text="3 שעות ו30 דקות" value="210">
                            3 שעות ו30 דקות </option>
                        <option data-text="3 שעות ו45 דקות" value="225">
                            3 שעות ו45 דקות </option>
                        <option data-text="4 שעות" value="240">
                            4 שעות </option>
                        <option data-text="4 שעות ו30 דקות" value="270">
                            4 שעות ו30 דקות </option>
                        <option data-text="5 שעות" value="300">
                            5 שעות </option>
                        <option data-text="5 שעות ו30 דקות" value="330">
                            5 שעות ו30 דקות </option>
                        <option data-text="6 שעות" value="360">
                            6 שעות </option>
                        <option data-text="6 שעות ו30 דקות" value="390">
                            6 שעות ו30 דקות </option>
                        <option data-text="7 שעות" value="420">
                            7 שעות </option>
                        <option data-text="8 שעות" value="480">
                            8 שעות </option>
                        <option data-text="9 שעות" value="540">
                            9 שעות </option>
                        <option data-text="10 שעות" value="600">
                            10 שעות </option>  
                    </select>
                </div>
                <div class="col-6 ">
                    <div class="position-relative">
                        <input  inputmode="decimal" type="number" onchange="globalCalendarSettings.setTwoNumberDecimal(this)" min="0" step="0.25" aria-label="Template price" name="template-price-${indexInput}" required
                               onKeyPress="if(this.value.length==7) return false;"  class="${durationPriceIdInput?'':'changed'} form-control bg-light border rounded js-template-price shadow-none m-0 py-2 px-15 price-block"
                               placeholder="${lang('add_membership_price_js')}" value="${priceInput}">
                        <span class="position-absolute" style="top:6px;right:0;">₪</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1 pl-0 d-flex align-items-center remove-button">
            <a role="button" onClick="meetingTemplate.removePaymentOption(this)">
                <i class="p-0 fal fa-trash-alt bsapp-fs-24"></i>
        </div>
    </div>`;
        $(el).parents().find('.payment-options-section').append(html);
        $(globalCalendarSettings.mainElem).find(".js-select2-dropdown-arrow-template").select2({
            minimumResultsForSearch: -1,
            theme: "bsapp-dropdown"
        });
        this.updateRemoveAndAddButtons();
    },
    /**
     * Remove a payment option
     * check less than 5 now and allow a new addition
     * update button if necessary
     * @param  elem The element ws click
     */
    removePaymentOption: function (elem) {
        let paymentRow = $(elem).closest('.price-duration-block');
        const blockId = $(paymentRow).attr('data-id');
        if(blockId === '0') {
            $(paymentRow).remove();
            this.updateRemoveAndAddButtons()
        } else {
            //show loader and disabled screen
            $(this.addTemplateElem).addClass('bsapp-js-disabled-o');
            globalCalendarSettings.toggleDeleteLoader(elem, true);
            const apiProps = {
                fun: 'getSingleClassTypeWithMemberships',
                id:blockId
            };
            postApi('calendarSettings', apiProps, 'meetingTemplate.renderWarningRemovePayment',true)
        }
    },
    //render Warning RemovePayment
    renderWarningRemovePayment: function (data) {
        let removeButton = $(this.addTemplateElem)
            .find('#list-template-basics .payment-options-section .remove-button #delete-loader').parent();
        if (!globalCalendarSettings.errorChecking(data)) {
            $(this.addTemplateElem).removeClass('bsapp-js-disabled-o');
            globalCalendarSettings.toggleDeleteLoader(removeButton, false);
            return;
        }
        let classType = data.ClassType;

        let removeClassTypeMainElem = $(globalCalendarSettings.mainElem)
            .find('.meetings-template-class-type-remove');
        let removeClassTypeElem = $(removeClassTypeMainElem)
            .find('.class-type-confirm-deleted-section').attr('data-id',classType.id);

        $(this.addTemplateElem).removeClass('bsapp-js-disabled-o');
        if(classType.memberships.length === 0) {
            this.removeClassType($(removeClassTypeMainElem).find('.js-remove-class-type-button'));
            return;
        }

        this.updateRemoveAndAddButtons();

        //change button to template remove
        $(removeClassTypeMainElem).find('[data-target]').attr('data-target','calendarSettings-meetings-templates-new');
        $(removeClassTypeMainElem).find('.js-remove-class-type-button').attr('onclick','meetingTemplate.removeClassType(this)');
        //change text in remove Class Type page
        $(removeClassTypeElem).find('.payment-option-text').text(lang('payment_option'));
        $(removeClassTypeElem).find('.link-item-text').text(lang('this_payment_option_existing'));
        $(removeClassTypeElem).find('.delete-explanation').text(lang('confirm_delete_class-type_message'));
        $(removeClassTypeElem).find('.class-type-name').text(classType.Type);
        $(removeClassTypeElem).find('.item-link-count').text(classType.memberships.length);
        switchSettingsPanel($(removeButton), 'meetings-template-class-type-remove');
    },
    //show loader and sends a command to DB to delete the class type
    removeClassType: function (elem){
        globalCalendarSettings.disabledButton(elem);
        let classTypeElem =$(elem).closest('.meetings-template-class-type-remove').find('.class-type-confirm-deleted-section');
        const classTypeId =$(classTypeElem).attr('data-id');
        globalCalendarSettings.toggleDeleteLoader($(classTypeElem).parent(), true);
        const apiProps = {
            fun: 'RemoveClassType',
            id:classTypeId
        };
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderAfterRemoveClassType',true);
    },
    //remove from the front after remove and fix list template
    renderAfterRemoveClassType: function (data){
        let removeClassTypeElem = $(globalCalendarSettings.mainElem)
            .find('.meetings-template-class-type-remove .class-type-confirm-deleted-section')
        globalCalendarSettings.toggleDeleteLoader($(removeClassTypeElem.parent()), false);
        $(removeClassTypeElem).removeClass('d-flex');
        if (!globalCalendarSettings.errorChecking(data)) {
            return;
        }
        $(this.addTemplateElem).find('.payment-options-section .price-duration-block #delete-loader')
            .closest('.price-duration-block').remove();
        this.updateRemoveAndAddButtons();
        //update list duration text
        let durationList = $(this.addTemplateElem).find('.price-duration-block .template-duration')
            .get().map(item => item.value);
        let templateList = $(this.mainElem).find('.templates-list');
        const templateId = $(this.addTemplateElem).find('.edit-app-template').attr('data-id');
        let targetRow = $(templateList).find($('.js-template-row[data-id="' + templateId + '"]'));
        targetRow.find('.item-duration').text(globalCalendarSettings.getDurationText(durationList));
        switchSettingsPanel($(removeClassTypeElem), 'calendarSettings-meetings-templates-new');
    },
    //helper function - disabled or  not add new block and show or not remove button
    updateRemoveAndAddButtons: function () {
        const allBlocks = $(this.addTemplateElem).find('.payment-options-section .price-duration-block');
        if(allBlocks.length === 1) {
            $(this.addTemplateElem).find(`#list-template-basics .payment-options-section .remove-button`)
                .removeClass('d-flex').addClass('d-none');
        } else if(allBlocks.length <5) {
            $(this.addTemplateElem).find('#list-template-basics #js-add-payment-option').removeClass('disabled');
        }
        if($(allBlocks).filter("[data-id!='0']").length === 1) {
            $(this.addTemplateElem).find(`#list-template-basics .payment-options-section .price-duration-block[data-id!='0'] .remove-button`)
                .removeClass('d-flex').addClass('d-none');
        }
    },

    removeDeleteLoaders: function (elem) {
        let item;
        if ($(elem).attr('data-target') === 'all-templates' ){
            item = $(this.mainElem).find(`#delete-loader`).parent();
        } else {
            item = $(this.addTemplateElem).find(`.payment-options-section .price-duration-block #delete-loader`).parent()
        }
        globalCalendarSettings.toggleDeleteLoader(item, false);
        // $(item).find('.js-add-category').removeClass('d-flex');
    },

    // remove app payment block except for the default
    removeAllPaymentOption: function () {
        let paymentSection = $(this.addTemplateElem).find('#list-template-basics .payment-options-section');
        $(paymentSection).find('.price-duration-block').remove();
        $(paymentSection).find('.template-duration').val(60).trigger('change');
        $(paymentSection).find('.js-template-price').val('');
        $(paymentSection).parent().find('#js-add-payment-option').removeClass('disabled');
    },

    /***********************  meeting Template Advanced Settings ***********************/
    // before back to menu check validation
    backToAdvancedSettings: function (elem, target) {
        let activeElem = $(elem).parents('.calendarSettings-meetings-templates-advanced-settings').find('.scrollable .active');
        let field = $(activeElem).find('[required]');
        if (activeElem.hasClass('leastOneChoice')) {
            if ($(field).filter(':checked').length === 0) {
                $(activeElem).find('.is-invalid').removeClass('d-none');
                return
            } else {
                $(activeElem).find('.is-invalid').addClass('d-none');
            }
        }
        if (validateTemplateFields(field)) {
            $('#calendarSettings .js-close-calendar-settings').show()
            switchSettingsPanel($(elem), target);
        }
    },

    /******** External registration ********/
    // When "external registration status" changes - changes on the setting page and hide/show "registrationLimitedTo"
    externalRegistrationStatusChanged: function (elem) {
        const externalRegistrationStatus = $(elem).find(':selected').val();
        let titleBlockValue = "";
        let externalLimitToElem = $(this.addTemplateAdvancedElem)
            .find('#external-registration-section .js-registration-limited-to');
        let externalIconElem = $(this.addTemplateElem).find('.js-external-registration .icon-block');
        switch (externalRegistrationStatus) {
            case  EXTERNAL_REGISTRATION_DISABLED:
                $(elem).parents('#external-registration-section')
                    .find('.js-registration-limited-to :input').prop('required', false);
                $(externalLimitToElem).hide();
                titleBlockValue = $(elem).find(':selected').attr('data-text');
                $(externalIconElem).addClass('fa-eye-slash').removeClass('fa-eye');
                break;
            case EXTERNAL_REGISTRATION_ACTIVE:
                $(elem).parents('#external-registration-section')
                    .find('.js-registration-limited-to :input').prop('required', true);
                $(externalLimitToElem).show();
                titleBlockValue = $('.registration-limited-to :selected').attr('data-text');
                $(externalIconElem).removeClass('fa-eye-slash').addClass('fa-eye');
                break;
        }
        $(this.addTemplateElem).find('#list-template-advanced .js-external-registration .title-block-value')
            .text(titleBlockValue);
    },
    //When "registration Limited To Changed" changes - changes on the setting page
    registrationLimitedToChanged: function (elem) {
        const titleBlockValue = $(elem).find(':selected').attr('data-text');
        $(this.addTemplateElem).find('#list-template-advanced .js-external-registration .title-block-value')
            .text(titleBlockValue);
    },

    /******** Coaches limit  ********/
    // When "all coaches" changes - changes coaches to not-check /checked and update titleBlockValue
    allCoachesSelectedChanged: function (elem) {
        $(elem).addClass('changed')
        let coachesLimitSection = $(this.addTemplateAdvancedElem).find('#coaches-limit-section');
        const textElem = $(this.addTemplateElem).find('#list-template-advanced .js-coaches-limit .title-block-value');
        if ($(elem).is(':checked')) {
            $(coachesLimitSection).find('.coaches-list [name="js-coach-id"]').prop('checked', true);
            $(coachesLimitSection).find('.is-invalid').addClass('d-none');
            textElem.text(lang('all_coaches')).removeClass('text-danger');
        } else {
            $(coachesLimitSection).find('.coaches-list [name="js-coach-id"]').prop('checked', false);
            $(coachesLimitSection).find('.is-invalid').removeClass('d-none');
            textElem.text(lang('warning_no_coach_selected')).addClass('text-danger');
        }
    },
    // When "coach Selected" changes - changes all-coaches to not checked and update titleBlockValue
    coachSelectedChanged: function (elem) {
        let coachesLimitSection = $(this.addTemplateAdvancedElem).find('#coaches-limit-section');
        let allCoaches = $(coachesLimitSection).find('#js-all-coaches-selected').addClass('changed');
        let titleBlockValue = "";
        $(coachesLimitSection).find('#js-all-coaches-selected').prop('checked', false);
        let selectedInput = $('#coaches-limit-section .coaches-list [name="js-coach-id"]:checkbox:checked');
        const textElem = $(this.addTemplateElem).find('#list-template-advanced .js-coaches-limit .title-block-value');
        let numberOfCoachesSelect = selectedInput.length
        $(coachesLimitSection).find('.is-invalid').addClass('d-none');
        textElem.removeClass('text-danger');
        if (numberOfCoachesSelect === 0) {
            $(coachesLimitSection).find('.is-invalid').removeClass('d-none');
            titleBlockValue = lang('warning_no_coach_selected');
            textElem.addClass('text-danger');
        } else if (numberOfCoachesSelect === $(coachesLimitSection).find('.coaches-list [name="js-coach-id"]').length) {
            titleBlockValue = lang('all_coaches');
            $(allCoaches).prop('checked', true);
        } else if (numberOfCoachesSelect === 1) {
            titleBlockValue = lang('one_coach_selected');
        } else {
            titleBlockValue = lang('were_selected') + " " + numberOfCoachesSelect + " " + lang('different_coaches');
        }
        textElem.text(titleBlockValue);
    },
    //create coach row by the data
    coachRow: function (coach, meetingTemplateId = null) {
        let uploadImage;
        let checked = '';
        if (coach.Status == 1 && coach.MeetingTemplateId == meetingTemplateId)
            checked = 'checked';
        if (uploadImage) {
            uploadImage = '/camera/uploads/large/' + coach.UploadImage;
        } else {
            uploadImage = 'https://ui-avatars.com/api/?length=1&name=' + coach.display_name + '&background=f3f3f4&color=000&font-size=0.5';
        }
        const markup =
            `<div class=" py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative">
                <div class="d-flex ">
                    <img src="${uploadImage}" class="w-40p h-40p mie-10 rounded-circle" />
                 <div class="bsapp-fs-18 coach-name text-right">${coach.display_name}</div>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" id="js-coach-id-${coach.id}" name="js-coach-id" required ${checked}
                    onchange="meetingTemplate.coachSelectedChanged(this)" value=${coach.id} class="custom-control-input">
                    <label class="custom-control-label" for="js-coach-id-${coach.id}"></label>
                 </div>
            </div>`;
        return markup;
    },
    //make coaches list all checked
    renderDefaultCoaches: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.addTemplateElem).find('.save-app-template'), 'all-templates');
            return;
        }
        const coaches = data.coachesList;
        let advancedPage = $(this.addTemplateAdvancedElem);
        let coachesList = $(advancedPage).find('#coaches-limit-section .coaches-list');
        // remove list of coaches
        coachesList.children().remove()
        if (coaches?.length > 0) {
            coaches.forEach(coach => {
                $(coachesList).append(meetingTemplate.coachRow(coach))
            })
            $(advancedPage).find('#coaches-limit-section #js-all-coaches-selected').prop('checked', true).trigger('change');
        } else {
            $(coachesList).append('<li class="mb-10 animated fadeInUp"><div id="coach-not-found" class="form-static d-flex align-items-center justify-content-center rounded text-start m-0 py-8 px-10 bsapp-fs-18">' + lang("no_coaches_found") + '</div></li>')
        }
    },

    /******** Calendar limit ********/
    // When "all calendars" changes - changes calendars to not-check /checked and update titleBlockValue
    allCalendarsSelectedChanged: function (elem) {
        let calendarsLimitSection = $(this.addTemplateAdvancedElem).find('#calendars-limit-section');
        const textElem = $(this.addTemplateElem).find('#list-template-advanced .js-calendars-limit .title-block-value');
        if ($(elem).is(':checked')) {
            $(calendarsLimitSection).find('.calendar-list [name="js-calendar-id"]').prop('checked', true);
            $(calendarsLimitSection).find('.is-invalid').addClass('d-none');
            textElem.text(lang('all_calendar')).removeClass('text-danger');
        } else {
            $(calendarsLimitSection).find('.calendar-list [name="js-calendar-id"]').prop('checked', false);
            $(calendarsLimitSection).find('.is-invalid').removeClass('d-none');
            textElem.text(lang('warning_no_calendars_selected')).addClass('text-danger');
        }
    },
    // When "calendar Selected" changes - changes all-calendar to not checked and update titleBlockValue
    calendarsSelectedChanged: function (elem) {
        let titleBlockValue;
        let calendarsLimitSection = $(this.addTemplateAdvancedElem).find('#calendars-limit-section');
        $(calendarsLimitSection).find('#js-all-calendars-selected').prop('checked', false).addClass('changed');
        let selectedInput = $(calendarsLimitSection).find('.calendar-list [name="js-calendar-id"]:checkbox:checked');
        const textElem = $(this.addTemplateElem).find('#list-template-advanced .js-calendars-limit .title-block-value');
        const numberOfCalendarsSelect = selectedInput.length;
        $(calendarsLimitSection).find('.is-invalid').addClass('d-none');
        textElem.removeClass('text-danger');
        if (numberOfCalendarsSelect === 0) {
            $(calendarsLimitSection).find('.is-invalid').removeClass('d-none');
            titleBlockValue = lang('warning_no_calendars_selected');
            textElem.addClass('text-danger');
        } else if (numberOfCalendarsSelect === $(calendarsLimitSection).find('.calendar-list [name="js-calendar-id"]').length) {
            titleBlockValue = lang('all_calendar');
            $(calendarsLimitSection).find('#js-all-calendars-selected').prop('checked', true);
        } else if (numberOfCalendarsSelect === 1) {
            titleBlockValue = lang('one_calendar_selected');
        } else {
            titleBlockValue = lang('were_selected') + " " + numberOfCalendarsSelect + " " + lang('different_calendars');
        }
        textElem.text(titleBlockValue);
    },
    //get from the db all calendars options
    getCalendarsOptions: function () {
        const apiProps = {
            fun: 'GetBrandAndCalendarsOptions',
            CompanyNum: $companyNo
        }
        postApi('calendarSettings', apiProps, 'meetingTemplate.renderCalendarsOptions', true);

    },
    // show the calendars option on front
    renderCalendarsOptions: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.mainElem).find('.templates-list'), 'Meetings-navigation');
            return;
        }
        let calenderOptions = data.calenderOptions;
        let advancedPage = $(this.addTemplateAdvancedElem);
        let calendarsList = $(advancedPage).find('#calendars-limit-section .calendar-list');
        calendarsList.children().remove()
        if (Object.keys(calenderOptions)?.length > 0) {
            Object.values(calenderOptions).forEach(calendar => {
                $(calendarsList).append(meetingTemplate.createCalendarBrandEmptyRow(calendar))
            })
        } else {
            $(calendarsList).append('<li class="mb-10 animated fadeInUp"><div id="calendar-not-found" class="form-static d-flex align-items-center justify-content-center rounded text-start m-0 py-8 px-10 bsapp-fs-18">' + lang("no_calendars_found") + '</div></li>')
        }
    },
    createCalendarBrandEmptyRow: function (calendars) {
        let markup = '';
        if (calendars?.length === 1 && calendars[0].sectionsId === null) {
            markup =
                `<div>
                    <div class="d-flex justify-content-center border-bottom brand-name" data-id=${calendars[0].id}>
                        <span class="font-weight-bold py-5">${calendars[0].BrandName}</span>
                    </div>
                    <div class=" py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative">
                        <div class="d-flex ">
                          <div class="bsapp-fs-18 calendar-name-empty">לא קיימים יומנים לסניף זה</div>
                        </div>
                    </div>
                </div>`;
        } else {
            markup =
                `<div>
                    <div class="d-flex justify-content-center border-bottom brand-name" data-id=${calendars[0].id}>
                        <span class="font-weight-bold py-5">${calendars[0].BrandName}</span>
                    </div>`;
            calendars.forEach(calendar => {
                let calendarItem = (this.createCalendarEmptyRow(calendar));
                markup += calendarItem;
            })
            markup += `</div>`
        }
        return markup;
    },
    createCalendarEmptyRow: function (calendar) {
        return `<div class="py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative">
                <div class="d-flex ">
                    <div class="bsapp-fs-18 calendar-name">${calendar.Title}</div>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" id="js-calendar-id-${calendar.sectionsId}" checked
                           onchange="meetingTemplate.calendarsSelectedChanged(this)"
                           name="js-calendar-id" required value=${calendar.sectionsId} class="custom-control-input">
                        <label class="custom-control-label" for="js-calendar-id-${calendar.sectionsId}"></label>
                </div>
            </div>`;
    },

    /******** Sessions limit ********/
    // When "sessions limit status" changes - changes on the setting page and hide/show "sessions limit number"
    sessionsLimitStatusChanged: function (elem) {
        let sessionsLimitNumber = $(this.addTemplateAdvancedElem).find('#sessions-limit-section .js-sessions-limit-number');
        const sessionsLimitStatus = $(elem).find(':selected').val();
        let titleBlockValue = "";
        switch (sessionsLimitStatus) {
            case NO_SESSIONS_LIMIT:
                $(sessionsLimitNumber).hide();
                titleBlockValue = $(elem).find(':selected').attr('data-text');
                break;
            case WITH_SESSIONS_LIMIT:
                $(sessionsLimitNumber).show();
                titleBlockValue = $(sessionsLimitNumber).find('.sessions-limit-number :selected').attr('data-text')
                break;
        }
        $(this.addTemplateElem).find('#list-template-advanced .js-sessions-limit .title-block-value').text(titleBlockValue);
    },
    //When "sessions limit number" changes - changes on the setting page
    sessionsLimitNumberChanged: function (elem) {
        const titleBlockValue = $(elem).find(':selected').attr('data-text');
        $(this.addTemplateElem).find('#list-template-advanced .js-sessions-limit .title-block-value').text(titleBlockValue);
    },

    /******** Online options ********/
    // When "online type" changes - changes on the setting page and hide/show "online Send" or "zoom details"
    onlineTypeChanged: function (elem) {
        let onlineType = $(elem).find(':selected').val();
        let titleBlockValue = $(elem).find(':selected').attr('data-text');

        let onlineSection  = $(this.addTemplateAdvancedElem).find('#online-options-section');
        let onlineIconElem = $(this.addTemplateElem).find('.js-online-options .icon-block');

        $(onlineSection).find('.not-physical-meeting :input').prop('required', false);
        switch (onlineType) {
            case MEETING_TYPE_PHYSICAL:
                $(onlineSection).find('.not-physical-meeting').addClass('d-none');
                $(onlineIconElem).removeClass('fa-video').addClass('fa-video-slash');
                break;
            case MEETING_TYPE_ONLINE:
                if($(onlineSection).find('.js-online-send-info :input.online-reminder-value').val() == ''){
                    $(onlineSection).find('.js-online-send-info :input.online-reminder-value').val('2').trigger('change');
                }
                $(onlineSection).find('.js-online-send-info :input').prop('required', true);
                $(onlineSection).find('.js-zoom-details').addClass('d-none');
                $(onlineSection).find('.js-online-send-info').removeClass('d-none');
                titleBlockValue = this.onlineSendBlockTitleHelper(titleBlockValue);
                $(onlineIconElem).addClass('fa-video').removeClass('fa-video-slash');
                break;
            case MEETING_TYPE_ZOOM:
                $(onlineSection).find('.js-zoom-details :input').prop('required', true);
                $(onlineSection).find('.js-online-send-info').addClass('d-none');
                $(onlineSection).find('.js-zoom-details').removeClass('d-none');
                titleBlockValue = this.zoomBlockTitleHelper(titleBlockValue);
                break;
                $(onlineIconElem).addClass('fa-video').removeClass('fa-video-slash');
        }
        $(this.addTemplateElem).find('#list-template-advanced .js-online-options .title-block-value').text(titleBlockValue);
    },
    //When "online Send Type" changes - changes on the setting page
    onlineReminderValueChanged: function (elem) {
        const onlineOptionsType = $(this.addTemplateAdvancedElem)
            .find('#online-options-section .online-options-type :selected').attr('data-text');
        let titleBlockValue = this.onlineSendBlockTitleHelper(onlineOptionsType);
        $(this.addTemplateElem).find('#list-template-advanced .js-online-options .title-block-value').text(titleBlockValue);
    },
    //When "zoom Details" changes - changes on the setting page
    zoomDetailsChanged: function (elem = null) {
        //remove space in meeting id
        if (elem) {
            elem.value = (elem.value).replace(/ /g, "");
        }
        const onlineOptionsType = $(this.addTemplateAdvancedElem)
            .find('#online-options-section .online-options-type :selected').attr('data-text');
        let titleBlockValue = this.zoomBlockTitleHelper(onlineOptionsType);
        $(this.addTemplateElem).find('#list-template-advanced .js-online-options .title-block-value').text(titleBlockValue);
    },

    /******** Preparation time ********/
    // When "preparation status" changes - changes on the setting page and hide/show "preparation-info"
    preparationStatusChanged: function (elem) {
        let preparationInfo  = $(this.addTemplateAdvancedElem).find('#preparation-time-section .js-preparation-info');
        let preparationStatusChanged = $(elem).find(':selected').val();
        let titleBlockValue = $(elem).find(':selected').attr('data-text');
        switch (preparationStatusChanged) {
            case NONE_PREPARATION:
                $(preparationInfo).addClass('d-none');
                $(preparationInfo).find('input.preparation-time-value').prop('required', false);
                break;
            case AFTER_PREPARATION:
            case BEFORE_PREPARATION:
                $(preparationInfo).find('input.preparation-time-value').prop('required', true);
                $(preparationInfo).removeClass('d-none');
                titleBlockValue = this.preparationBlockTitleHelper(titleBlockValue);
                break;
        }
        $(this.addTemplateElem).find('#list-template-advanced .js-preparation .title-block-value').text(titleBlockValue);
    },
    //When "who can register" changes - changes on the setting page
    preparationInfoChanged: function (elem) {
        let preparationStatus = $(this.addTemplateAdvancedElem)
            .find('#preparation-time-section .preparation-time-status :selected').attr('data-text');
        let titleBlockValue = this.preparationBlockTitleHelper(preparationStatus);
        $(this.addTemplateElem).find('#list-template-advanced .js-preparation .title-block-value').text(titleBlockValue);
    },

    /******** Helper title ********/
    onlineSendBlockTitleHelper: function (onlineOptionsType) {
        const sendInfo =(this.addTemplateAdvancedElem).find('#online-options-section .js-online-send-info');
        const schedulingType = $(sendInfo).find('.online-reminder-type :selected ').attr('data-text');
        const schedulingTimeValue = $(sendInfo).find('.online-reminder-value').val();
        return onlineOptionsType + ", " + " " + lang('link_will_send') + ' ' + schedulingTimeValue + ' ' + schedulingType;
    },
    zoomBlockTitleHelper: function (onlineOptionsType) {
        let zoomMeetingNumber = $(this.addTemplateAdvancedElem)
            .find('#online-options-section .js-zoom-details #zoom-meeting-number').val();
        if (zoomMeetingNumber != '')
            return onlineOptionsType + ": MI " + zoomMeetingNumber.replace(/(.{10})..+/, "$1…");
        return onlineOptionsType;
    },
    preparationBlockTitleHelper: function (preparationStatus) {
        let preparationInfo  = $(this.addTemplateAdvancedElem).find('#preparation-time-section .js-preparation-info');

        const preparationTimeType = $(preparationInfo).find(':selected').attr('data-text');
        const preparationTimeValue = $(preparationInfo).find('.preparation-time-value').val()
        return preparationTimeValue + ' ' + preparationTimeType + ' ' + preparationStatus;
    },
}
let meetingCancellationPolicy = {
    mainElem: null,
    addPolicyElem: null,

    /*********************** Get Cancellation Policy ***********************/
    //get all cancellation policy from db
    getAllMeetingCancellationPolicy: function(elem) {
        const generalMeetingSettingId = $(meetingGeneralSettings.mainElem).attr('data-id');
        meetingCancellationPolicy.mainElem = $(elem).parents('.dropdown-menu').find('.calendarSettings-meetings-cancellation-policy');
        //show loading and show setting;
        $(this.mainElem).find('ul.list-of-loading').removeClass('d-none');
        $(this.mainElem).find('ul.list-of-cancellation-blocks').addClass('d-none');
        const apiProps = {
            fun: 'GetAllMeetingCancellationPolicy',
            generalMeetingSettingId: generalMeetingSettingId
        }
        postApi('calendarSettings', apiProps, 'meetingCancellationPolicy.renderMeetingsCancellationPolicy', true);
    },
    //show all pre-payments
    renderMeetingsCancellationPolicy: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.mainElem).find('.js-add-policy-option'), 'Meetings-navigation');
            return;
        }
        this.addPolicyElem = $(globalCalendarSettings.mainElem).find('.calendarSettings-meetings-cancellation-policy-add-option');
        let meetingsCancellationPolicy = data.response;
        let policyList = $(this.mainElem).find('ul.list-of-cancellation-blocks');
        //remove the old blocks
        $(policyList).find('li.cancellation-dynamic-block').remove();
        //loop over meetings cancellation Policy array
        meetingsCancellationPolicy.forEach(policy => {
            //if default than only change the fields value
            if (policy.TypeGroupCustomers == TYPE_GROUP_CUSTOMERS_DEFAULT) {
                $(policyList).prepend(this.addBlockPolicy(policy));
            } else {
                $(policyList).append(this.addBlockPolicy(policy));
            }
        })
        //hide loading and show setting
        $(this.mainElem).find('ul.list-of-loading').addClass('d-none');
        $(policyList).removeClass('d-none');
        //add changed flag to all input that changed
        $(policyList).find('.cancellation-default-block :input').change(function () {
            $(this).addClass('changed');
        });
    },
    //add dynamic block of policy depending on the type of group
    addBlockPolicy: function (policy) {
        let displayData = this.getDisplayData(policy);
        this.changeOption(policy.TypeGroupCustomers, displayData.groupCustomers, ACTION_DISABLED);
        const isDefaultGroup = policy.TypeGroupCustomers === TYPE_GROUP_CUSTOMERS_DEFAULT
        const editButton = isDefaultGroup ?
            ` <div class="align-self-center">
            <i class="fal fa-angle-right bsapp-fs-28 mr-10"></i>
                <a class="stretched-link" onclick="meetingCancellationPolicy.editPolicyOption(this)"
                 data-target="calendarSettings-meetings-cancellation-policy-add-option"></a>
         </div>` :
            `<div class="align-self-center js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21 mr-10" role="button">
                      <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow">
                            <ul class="list-unstyled m-0 p-0">
                                <li class="mb-6"> <a role="button"
                                    class="edit-appointment-template d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"
                                    onclick="meetingCancellationPolicy.editPolicyOption(this)" data-target="calendarSettings-meetings-cancellation-policy-add-option"><i
                                        class="fal fa-edit fa-fw mx-5"></i> <span>${lang('edit')}</span></a> </li>
                            <li class="mb-6"> <a role="button" onclick="meetingCancellationPolicy.removeCancellationPolicy(this)"
                                    class="js-delete-row-template d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                                <i class="fal fa-minus-circle fa-fw mx-5"></i>
                                <span>${lang('delete')}</span></a></li>
                        </ul>
                        </div>
                        </div>`

        const markup = `
            <li  class='cancellation-dynamic-block' data-id="${policy.id}">
            <div>
               <input type="hidden" name="IsPercentage" value="${policy.IsPercentage}" />
               <input type="hidden" name="TypeGroupCustomers" value="${policy.TypeGroupCustomers}" />
               <input type="hidden" name="GroupCustomers" value="${displayData.groupCustomers}" />
               
               <input class="loopItems" type="hidden" name="AfterPurchaseChargeStatus" value="${policy.AfterPurchaseChargeStatus}" />
               <input class="loopItems" type="hidden" name="AfterPurchaseChargeAmount"
                value="${meetingCancellationPolicy.fromChargeStatusToValue(policy.AfterPurchaseChargeStatus, policy.AfterPurchaseChargeAmount)}" />
               
               <input class="loopItems" type="hidden" name="ManualChargeStatus" value="${policy.ManualChargeStatus}" />
               <input class="loopItems" type="hidden" name="ManualChargeAmount"
                value="${meetingCancellationPolicy.fromChargeStatusToValue(policy.ManualChargeStatus, policy.ManualChargeAmount)}" />
                <input class="loopItems" type="hidden" name="ManualChargeTime" value="${policy.ManualChargeTime ?? '6'}" />
               <input class="loopItems" type="hidden" name="ManualChargeTimeType" value="${policy.ManualChargeTimeType ?? '1'}" />
               
               <input class="loopItems" type="hidden" name="NotArriveChargeStatus" value="${policy.NotArriveChargeStatus}" />
               <input class="loopItems" type="hidden" name="NotArriveChargeAmount"
                value="${meetingCancellationPolicy.fromChargeStatusToValue(policy.NotArriveChargeStatus, policy.NotArriveChargeAmount)}" />
               
               <div class="d-flex align-items-center px-8 py-8 border-bottom ${isDefaultGroup ? 'border-top text-muted bsapp-min-h-70p' : 'border-light' }">
                  <div class="d-flex w-100 justify-content-between position-relative">
                     <div>
                         <div class="d-flex">
                             <i class="fal ${displayData.iconClass} icon-block bsapp-fs-20 ml-8"></i>
                            <h6 class="text-gray-700 text-start font-weight-bolder mb-2 bsapp-fs-14 block-title">
                               ${displayData.title}
                            </h6>
                        </div>
                        <div class="d-flex align-items-center">
                           <div class="bsapp-fs-14 text-right block-sub-title">
                             ${displayData.subTitle}
                           </div>
                        </div>
                     </div>
                     ${editButton}
                  </div>
               </div>
              </div>
           </li>`;
        return markup;
    },
    //Function for get the display data of the dynamic block
    getDisplayData: function (policy) {
        let response = {}
        //icon, sub-title and groupCustomers
        switch (policy.TypeGroupCustomers) {
            case TYPE_GROUP_CUSTOMERS_LEVEL:
                response.title = lang('only_for_trainees_from') + globalCalendarSettings.findLevelById(policy.LevelId);
                response.groupCustomers = policy.LevelId;
                response.iconClass = 'fa-tag';
                break;
            case TYPE_GROUP_CUSTOMERS_ENTRIES:
                response.title = lang('everyone_who_comes_to')+ policy.MinMeetingAmount + " "  + lang('meetings_or_more');
                response.groupCustomers = policy.MinMeetingAmount;
                response.iconClass = 'fa-check-circle';
                break;
            case TYPE_GROUP_CUSTOMERS_STATUS:
                let statusText = policy.ClientStatus == CLIENT_STATUS_ACTIVE ? lang('active'):
                    policy.ClientStatus == CLIENT_STATUS_ARCHIVE ? lang('archive') : lang('interested_single')
                response.title = lang('all_status_users') + ' - ' + statusText;
                response.groupCustomers = policy.ClientStatus;
                response.iconClass = 'fa-user-check';
                break;
            case TYPE_GROUP_CUSTOMERS_DEFAULT:
                response.title = lang('default_cancellation_policy');
                response.groupCustomers = '0';
                response.iconClass = 'fa-exclamation';
                break;
            default:
                return;
        }
        let symbol = "";
        if (policy.IsPercentage == '1') {
            symbol = '%</b>' +  ' ' + lang('total_transaction');
        } else if(policy.IsPercentage == '0') {
            symbol = '₪</b>' + ' '  + lang('regardless_of_cost_appointment');
        }
        let subTitleHtml = '<span> ';

        function statusToPrice(status, symbol, value, time, subTitle , text) {
            switch ('' + status) {
                case NO_CHARGE:
                    time === 0 ?  subTitle += lang('free_cancel') +" " : '';
                    break;
                case MANUAL_CHARGE:
                    subTitle += text + '<b>' + parseFloat(value) + symbol;
                    break;
                case FULL_CHARGE:
                    subTitle += text + '<b>' + lang('full_amount_sum') +'</b>' + ' ' + lang('the_deal');
                    break;
            }
            return subTitle
        }
        // return text prefix text of manual charge
        function setManualText(type, amount, prevStatus) {
            let text = '';
            try {
                let typeText;
                switch (type) {
                    case '0':
                        typeText = " " +  lang('minutes') + " ";
                        break;
                    case '1':
                        typeText = " " +lang('hours') + " ";
                        break;
                    case '2':
                        typeText = " " + lang('days') + " ";
                        break;
                }
                text = prevStatus == '0' ? lang('to_user_manage') : ',<br>' + lang('cancellation_within_range_of');
                text += ' <b>' + parseFloat(amount) + typeText + '</b>';
                text += prevStatus == '0' ?lang('before_start_queue')  + ',<br>' + lang('then_charge_of') +' '
                    : " " + lang('before_queue_incur_charge_of') + ' ' ;
            } catch (e) {
                return '';
            }
            return text;
        }

        subTitleHtml = statusToPrice(policy.AfterPurchaseChargeStatus, symbol, policy.AfterPurchaseChargeAmount, 0, subTitleHtml,  lang('canceling_queue_incur_charge_of') + ' ');
        if(policy.AfterPurchaseChargeStatus !='2') {
            let manualText = setManualText(policy.ManualChargeTimeType, policy.ManualChargeTime, policy.AfterPurchaseChargeStatus)
            subTitleHtml= statusToPrice(policy.ManualChargeStatus, symbol, policy.ManualChargeAmount, 1, subTitleHtml,manualText);
            if(policy.ManualChargeStatus != '2')
                subTitleHtml= statusToPrice(policy.NotArriveChargeStatus, symbol, policy.NotArriveChargeAmount, 2, subTitleHtml, '<br>' + lang('not_arrive will_incur_charge_of') + ' ');
        }
        response.subTitle = subTitleHtml + '</span>';
        return response;
    },

    getChargeStatusFromPrevPolicy: function (status, prevStatus = 0) {
        if(prevStatus > status) {
            return prevStatus;
        }
    },

    fromChargeStatusToValue: function (status, chargeValue, prevValue = 0) {
        if(prevValue > chargeValue) {
            return chargeValue;
        }
        switch (parseInt(status)) {
            case 0:
                return 0;
            case 1:
                return chargeValue;
            case 2:
                return 100;
        }
    },

    /*********************** Dynamic blocks ***********************/
    // When "payment value" changes corrects the value:
    paymentValueChange: function (elem) {
        const moreDetails = $(elem).closest('.more-details');
        const policyItemElem = $(moreDetails).closest('.policy-item'); //section item (order)
        let elemValue = $(elem).val();
        switch (parseInt(elemValue)) {
            case 0 :
                policyItemElem.find('.cancellation-policy-status').val(NO_CHARGE).trigger('change');
                break;
            case 100 :
                policyItemElem.find('.cancellation-policy-status').val(FULL_CHARGE).trigger('change');
                break;
            default:
                if(elemValue < 0) {
                    $(elem).val(0).trigger('change');
                } else if(elemValue > 100) {
                    $(elem).val(100).trigger('change');
                } else {
                    policyItemElem.find('.cancellation-policy-status').val(MANUAL_CHARGE).trigger('change');
                }
        }
    },
    // When "payment in advance status" changes -  hide/show "more details"
    cancellationPolicyStatusChange: function (elem) {
        const policyItemElem = $(elem).closest('.policy-item'); //section item (order)
        const policyDetails = policyItemElem.closest('.policy-details'); //section items
        const moreDetailsItem = policyItemElem.find('.more-details .more-details-item:first');
        let status = $(elem).find(':selected').val();
        const order = policyItemElem.attr('order');
        switch (status) {
            case FULL_CHARGE:
                policyDetails.find(`.policy-item`).each(function () {
                    if ($(this).attr('order') >= order) {
                        let policyInputElem = $(this).removeClass('d-none')
                            .find('.more-details .more-details-item').addClass('d-none')
                            .find('.policy-amount-input').val(100)
                        if($(this).find('.cancellation-policy-status').val() !== FULL_CHARGE ) {
                            policyInputElem.trigger('change');
                        }
                        if($(this).attr('order') == 1 ) {
                            policyItemElem.find('.manual-charge-timing').removeClass('d-none');
                        }
                    }
                });
                //show order -1
                policyDetails.find(`.policy-item[order=${order-1}]`).removeClass('d-none');
                // moreDetailsItem.addClass('d-none').find('.policy-amount-input').val(100);
                break;
            case MANUAL_CHARGE:
                let elemValue = moreDetailsItem.find('.policy-amount-input').val();
                moreDetailsItem.removeClass('d-none');
                policyDetails.find(`.policy-item[order=${order-1}]`).removeClass('d-none');
                policyDetails.find(`.policy-item`).each(function () {
                    if ($(this).attr('order') > order) {
                        let nextValue =  $(this).find('.more-details .more-details-item:first .policy-amount-input').val()
                        let policyInputElem =$(this).removeClass('d-none')
                            .find('.more-details .more-details-item').removeClass('d-none')
                            .find('.policy-amount-input').val(Math.max(elemValue,nextValue));
                        if($(this).find('.cancellation-policy-status').val() !== MANUAL_CHARGE ) {
                            policyInputElem.trigger('change');
                        }
                    }
                    if ($(this).attr('order') < order) {
                        let prevValue =  $(this).find('.more-details .more-details-item:first .policy-amount-input').val()
                        if(parseInt(prevValue) > parseInt(elemValue)) {
                            $(this).removeClass('d-none')
                                .find('.more-details .more-details-item').removeClass('d-none')
                                .find('.policy-amount-input').val(Math.min(elemValue,prevValue)).trigger('change');
                        }
                    }
                    if($(this).attr('order') == 1 ) {
                        policyItemElem.find('.manual-charge-timing').removeClass('d-none');
                    }
                });
                break;
            case NO_CHARGE:
                moreDetailsItem.addClass('d-none').find('.policy-amount-input').val(0);
                //show hide -1
                policyDetails.find(`.policy-item`).each(function () {
                    if ($(this).attr('order') < order) {
                        let policyInputElem = $(this).addClass('d-none')
                            .find('.more-details .more-details-item').addClass('d-none')
                            .find('.policy-amount-input').val(0);
                        if($(this).find('.cancellation-policy-status').val() !== NO_CHARGE ) {
                            policyInputElem.trigger('change');
                        }
                    }
                    if($(this).attr('order') == 1 ) {
                        policyItemElem.find('.manual-charge-timing').addClass('d-none');
                    }
                });
                break;
        }
    },
    findDefaultValue: function (orderNumber){
        const orderNumberInt = parseInt(orderNumber);
        const policyDetails = this.addPolicyElem.find('.cancellation-policy-fields .policy-details')
        const maxValue = orderNumberInt === 2 ? 100 :
            policyDetails.find(`.policy-item[order=${orderNumberInt+1}] .more-details-item:first .policy-amount-input`).val();
        const minValue = orderNumberInt === 0 ? 0 :
            policyDetails.find(`.policy-item[order=${orderNumberInt-1}] .more-details-item:first .policy-amount-input`).val();
        const elemValue = policyDetails.find(`.policy-item[order=${orderNumberInt}] .more-details-item:first .policy-amount-input`).val();
        if(orderNumberInt === 0) {
            return
        }
        return Math.max(elemValue,maxValue,minValue);
    },
    // When "is percentage" changes - corrects the value:
    isPercentageChange: function (elem) {
        const isPercentage = $(elem).is('.payment-value-in-percentage');
        let amountElem = $(elem).closest('.partial-payment-details').find('#policy-amount-input:first');
        if (amountElem.value > 100 && isPercentage)
            amountElem.value = 100;
        else if (amountElem.value < 0)
            amountElem.value = 0;
        else
            amountElem.value = parseFloat(amountElem.value).toFixed(2);
    },
    //groupType change displays the desired elements
    typeGroupCustomersChange: function (elem) {
        const groupType = $(elem).val();
        let tagsElement = $(elem).parent().find('.more-details .more-details-tags');
        let entriesElement = $(elem).parent().find('.more-details .more-details-entries');
        let clientStatusElement = $(elem).parent().find('.more-details .more-details-status');
        $(elem).parents('.group-customer:first').removeClass('d-none');

        //By selecting groupType Displays the desired select
        switch (groupType) {
            case GROUP_CUSTOMER_TAG:
                $(tagsElement).removeClass('d-none').find('select.select2-hidden-options').prop('required',true);
                $(entriesElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                $(clientStatusElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                break;
            case GROUP_CUSTOMER_ENTRY:
                $(tagsElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                $(entriesElement).removeClass('d-none').find('select.select2-hidden-options').prop('required',true);
                $(clientStatusElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                break;
            case GROUP_CUSTOMER_STATUS:
                $(tagsElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                $(entriesElement).addClass('d-none').find('select.select2-hidden-options').prop('required',false);
                $(clientStatusElement).removeClass('d-none').find('select.select2-hidden-options').prop('required',true);
                break;
            default:
                $(elem).parents('.group-customer:first').addClass('d-none');
                break;
        }

    },
    //when click on remove block - remove from db
    removeCancellationPolicy: function (elem) {
        let policyBlock = $(elem).parents('li.cancellation-dynamic-block'),
            payment_id = policyBlock.attr('data-id')
        //display loader
        globalCalendarSettings.toggleDeleteLoader(policyBlock, true);
        const apiProps = {
            fun: 'ChangeStatusToCancellationPolicy',
            id: payment_id,
            Status: 0
        }
        postApi('calendarSettings', apiProps, 'meetingCancellationPolicy.removeRowFront',true);
    },
    // if Success remove from front
    removeRowFront: function (res) {
        let policyList = $(this.mainElem).find('ul.list-of-cancellation-blocks');
        if (!globalCalendarSettings.errorChecking(res)) {
            let elem = $(policyList).find('#delete-loader').parent();
            globalCalendarSettings.toggleDeleteLoader(elem, false);
            $(elem).children().first().removeClass('d-flex');
            return;
        }
        let policyBlock = $(policyList).find(`.cancellation-dynamic-block[data-id='${res.id}']`);
        let groupType = $(policyBlock).find(":input[name='TypeGroupCustomers']").val();
        let value = $(policyBlock).find(":input[name='GroupCustomers']").val();
        this.changeOption(groupType, value, ACTION_ACTIVE);
        $(policyBlock).remove();

    },
    //show all data in edit page
    editPolicyOption: function (elem) {
        let policyBlock = $(elem).parents('li.cancellation-dynamic-block');
        let policyId = $(policyBlock).attr('data-id');
        //change path title
        $(this.addPolicyElem).find('.path-title').text(lang('path_cal_cancellation_policy_edit_option'));
        //change button save to edit
        $(this.addPolicyElem).find('.add-new-meetings-policy')
            .text(lang('save_changes_button')).removeClass('disabled')
            .addClass('edit-policy').attr('data-id', policyId);
        let policyFields = $(this.addPolicyElem).find('.cancellation-policy-fields');
        // get the data from front in dynamic block
        let isPercentage = $(policyBlock).find(":input[name='IsPercentage']").val();
        $(policyBlock).find(":input.loopItems").each(function () {
            $(policyFields).find(`.policy-details :input[name=${this.name}]`).val(this.value).trigger('change');
        })
        let typeGroupCustomers = $(policyBlock).find(":input[name='TypeGroupCustomers']").val();
        let groupCustomers = $(policyBlock).find(":input[name='GroupCustomers']").val();
        $(policyFields).find('.group-customer').removeClass('d-none');
        //change the group select
        switch (typeGroupCustomers) {
            case TYPE_GROUP_CUSTOMERS_LEVEL:
                $(policyFields).find('.level-customers').val(groupCustomers).trigger('change');
                break;
            case TYPE_GROUP_CUSTOMERS_ENTRIES:
                $(policyFields).find('.min-meeting-amount').val(groupCustomers).trigger('change');
                break;
            case TYPE_GROUP_CUSTOMERS_STATUS:
                $(policyFields).find('.client-status').val(groupCustomers).trigger('change');
                break;
            case TYPE_GROUP_CUSTOMERS_DEFAULT:
                $(policyFields).find('.group-customer :input').prop('required',false).val('');
                $(policyFields).find('.group-customer').addClass('d-none');
                break;
        }
        $(policyFields).find('.type-group-customers').val(typeGroupCustomers).trigger('change');
        // change the percentage check box
        if (isPercentage == '1') {
            $(policyFields).find('.policy-details .payment-value-in-percentage').click();
        } else {
            $(policyFields).find('.policy-details .payment-value-not-percentage').click();
        }
        this.renderSelect2GroupDetails();
        let inputs =  $(policyFields).find(':input');
        $(inputs).removeClass('changed');
        $(inputs).change(function () {
            $(this).addClass('changed');
        });

    },
    //new prepayment - display page
    newPolicytOption: function (elem) {
        this.renderSelect2GroupDetails();
        //change path title
        $(this.addPolicyElem).find('.path-title').text(lang('path_cal_cancellation_policy_add_option'));
        //change button save to edit
        $(this.addPolicyElem).find('.add-new-meetings-policy').text(lang('save'))
            .removeClass(['disabled','edit-policy']);
        let policyFields = $(this.addPolicyElem).find('.cancellation-policy-fields');
        $(policyFields).find('.type-group-customers').val(TYPE_GROUP_CUSTOMERS_LEVEL).trigger('change');
        $(policyFields).find('.more-details .select2-hidden-options').val('').trigger('change');
        $(policyFields).find('.cancellation-policy-status').val(NO_CHARGE).trigger('change');
        //show next page
        switchSettingsPanel($(elem), 'calendarSettings-meetings-cancellation-policy-add-option');
    },
    //edit or create new dynamic payment
    saveDynamicPayment: function (elem) {
        $(elem).addClass("disabled");
        let apiProps = {};
        let isNew = true;
        if($('.policy-item[order=1] .cancellation-policy-status').val() != NO_CHARGE) {
            $('.policy-item[order=1] .manual-charge-timing :input').prop('required',true);
        } else {
            $('.policy-item[order=1] .manual-charge-timing :input').prop('required',false);
        }
        let fields = $(elem).parents('.bsapp-settings-panel').find("[required]");
        if (validateTemplateFields(fields)) {
            let changedFields = null
            if ($(elem).hasClass('edit-policy')) {
                apiProps['id'] = $(elem).attr('data-id');
                isNew = false;
                changedFields = $(meetingCancellationPolicy.addPolicyElem).find('.cancellation-policy-fields .changed:input');
            } else {
                apiProps['GeneralMeetingSettingId'] = $(meetingGeneralSettings.mainElem).attr('data-id');
                changedFields = $(meetingCancellationPolicy.addPolicyElem).find('.cancellation-policy-fields :input');
            }
            //noting to change
            if(changedFields.length < 1) {
                let saveElem = $(this.addPolicyElem).find('.add-new-meetings-policy');
                $(saveElem).removeClass('disabled');
                switchSettingsPanel($(saveElem), 'calendarSettings-meetings-cancellation-policy');
                return;
            }
            $(elem).text(lang('loading_datatables'));
            apiProps['fun'] = isNew ? 'CreateNewCancellationPolicy' : 'UpdateCancellationPolicy';
            //get fields that need to update
            $(changedFields).each(function () {
                if ($(this).attr('type') !== 'radio') {
                    apiProps[$(this).attr("name")] = $(this).val() ?? $(this).find(':selected').val();
                } else {
                    if ($(this).prop('checked') && this.name != '') {
                        apiProps[$(this).attr("name")] = $(this).val();
                    }
                }
            });
            switch (apiProps.TypeGroupCustomers) {
                // remove level and entries
                case TYPE_GROUP_CUSTOMERS_STATUS:
                    apiProps.LevelId = null;
                    apiProps.MinMeetingAmount = null;
                    break
                //remove entries
                case TYPE_GROUP_CUSTOMERS_LEVEL:
                    apiProps.MinMeetingAmount = null;
                    apiProps.ClientStatus = null;
                    break;
                case TYPE_GROUP_CUSTOMERS_ENTRIES:
                    apiProps.LevelId = null;
                    apiProps.ClientStatus = null;
                    break;
                case TYPE_GROUP_CUSTOMERS_DEFAULT:
                    apiProps.LevelId = null;
                    apiProps.ClientStatus = null;
                    apiProps.ClientStatus = null;
                    break;
            }
            postApi('calendarSettings', apiProps, 'meetingCancellationPolicy.afterSaveDynamicPayment',true);
        } else {
            $(elem).removeClass("disabled");
        }
    },
    //change front after save dynamic payment
    afterSaveDynamicPayment: function (data) {
        let saveElem = $(this.addPolicyElem).find('.add-new-meetings-policy');
        if (!globalCalendarSettings.errorChecking(data)) {
            const buttonText = $(saveElem).hasClass('edit-policy') ? lang('save_changes_button'): lang('save');
            saveElem.removeClass('disabled').text(buttonText);
            return;
        }
        let policy = data.response;
        //edit block..
        if (!data.isNew) {
            let block = $(this.mainElem).find(`.list-of-cancellation-blocks .cancellation-dynamic-block[data-id='${data.oldId}']`).attr('data-id',policy.id);
            let displayData = this.getDisplayData(policy);
            let typeGroupCustomersElem  = $(block).find("input[name='TypeGroupCustomers']");
            let groupCustomersElem  = $(block).find("input[name='GroupCustomers']");
            //update select2 option
            this.changeOption($(typeGroupCustomersElem).val(), $(groupCustomersElem).val(), ACTION_ACTIVE);
            this.changeOption(policy.TypeGroupCustomers, displayData.groupCustomers, ACTION_DISABLED);
            // set in all hidden input the new data
            $(block).find("input.loopItems").each(function () {
                $(this).val(policy[this.name]);
            });
            $(block).find("input[name='IsPercentage']").val(policy.IsPercentage);
            $(typeGroupCustomersElem).val(policy.TypeGroupCustomers);
            $(groupCustomersElem).val(displayData.groupCustomers);

            //change the icon and display titles
            $(block).find(".block-title").text(displayData.title);
            //remove icon class
            $(block).find(".icon-block").removeClass(function (index, css) {
                return (css.match(/(^|\s)fa-\S+/g) || []).join(' ');
            });
            $(block).find(".icon-block").addClass(displayData.iconClass);
            $(block).find(".block-sub-title").html(displayData.subTitle);
        } else {
            $(this.mainElem).find('.list-of-cancellation-blocks').append(this.addBlockPolicy(policy));
        }

        //return to back page
        switchSettingsPanel($(saveElem), 'calendarSettings-meetings-cancellation-policy');
    },
    //remove or add option from select2 (disabled or active)
    changeOption: function (groupType, value, action) {
        let classGroup;
        switch (groupType) {
            case TYPE_GROUP_CUSTOMERS_LEVEL:
                classGroup = 'level-customers';
                break;
            case TYPE_GROUP_CUSTOMERS_ENTRIES:
                classGroup = 'min-meeting-amount';
                break;
            case TYPE_GROUP_CUSTOMERS_STATUS:
                classGroup = 'client-status';
                break;
            default:
                return;
        }
        let option = $(this.addPolicyElem).find(`.${classGroup} option[value='${value}']`);
        if(action) {
            $(option).attr('disabled', 'disabled');
        } else {
            $(option).removeAttr('disabled');
        }
    },
    //update select2- group details
    renderSelect2GroupDetails: function () {
        let selectElem = $(this.addPolicyElem).find('.select2-hidden-options');
        $(selectElem).select2({
            language: $("html").attr("dir") == 'rtl' ? "he" : "en",
            theme: 'bsapp-dropdown',
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: lang('choose_option'),
        });
    }
}
let meetingCategories = {
    mainElem: null,
    confirmDeleteElem: null,

    //get all meeting categories
    getAllMeetingCategories: function (elem) {
        globalCalendarSettings.disabledButton(elem);
        if (this.mainElem == null) {
            this.mainElem = $(elem).parents('.dropdown-menu').find('.calendarSettings-meetings-category');
            this.confirmDeleteElem = $(elem).parents('.dropdown-menu').find('.calendarSettings-meetings-category-remove');

        }
        $(this.mainElem).find('.form-static.d-none').toggleClass('d-flex d-none');
        $(this.mainElem).find('.form-inline').remove();

        //remove template front and show loader
        let categoriesList = $(this.mainElem).find('.meetings-category-list');
        $(categoriesList).children().not(".item-loading").remove();
        $(categoriesList).children('.item-loading').show();

        const apiProps = {
            fun: 'GetMeetingCategories',
            CompanyNum: $companyNo
        };
        postApi('calendarSettings', apiProps, 'meetingCategories.renderMeetingCategories', true)

    },
    //Preparing a static map and Request to update select2
    createCategoryMap: function (data = null) {
        if (globalCalendarSettings.errorChecking(data)) {
            categoryMap = {};
            $.each(data.Categories, function (key) {
                categoryMap[data.Categories[key].id] = data.Categories[key].CategoryName;
            });
            globalCalendarSettings.select2Category();
        }
    },
    //render category data into the page
    renderMeetingCategories: function (data) {
        let categoriesList = $(this.mainElem).find('.meetings-category-list');
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(categoriesList), 'Meetings-navigation');
            return;
        }
        this.createCategoryMap(data);
        if (Object.keys(categoryMap).length > 0) {
            for (const [key, value] of Object.entries(categoryMap)) {
                $(categoriesList).append(meetingCategories.categoryRow(key, value));
            }
        } else {
            $(categoriesList).append(`<li class="mb-10 animated fadeInUp"><div id="categories-not-found" class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_categories_found')}</div></li>`)
        }
        //hide loader
        $(categoriesList).children('.item-loading').hide()

    },
    //create one item of category
    categoryRow: function (id, name) {
        const markup = `<li class="mb-10 category-row" data-id="${id}">
            <div class="form-toggle js-add-category">
              <div class="form-static d-flex align-items-center justify-content-between border rounded font-weight-bolder text-gray-700 text-start m-0 py-7 px-10">
                <div class="row w-100">
                  <div class="text-truncate px-10"> <span class="category-name">${name}</span> 
                  </div>
                </div>
                <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button">
                 <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                  <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow">
                    <ul class="list-unstyled m-0 p-0">
                      <li class="mb-6"> <a role="button" onclick="meetingCategories.toggleButtonAddCategory(this)"
                       class="d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                        <i class="fal fa-edit fa-fw mx-5"></i> ${lang('edit')} </a> </li>
                      <li> <a onclick="meetingCategories.getConfirmDeleteCategory(this)"
                      class="d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16" role="button">
                       <i class="fal fa-minus-circle fa-fw mx-5"></i> ${lang('delete')} </a> </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            </li>`;
        return markup;


    },
    //get all the template that related to the category - show loaders and set some data
    getConfirmDeleteCategory: function (elem) {
        //show loader
        $(this.confirmDeleteElem).find('.loading-confirm-deleted-section').removeClass('d-none');
        $(this.confirmDeleteElem).find('.js-remove-button').addClass('d-none');
        let dataElem = $(this.confirmDeleteElem).find('.category-confirm-deleted-section').addClass('d-none');

        let categoryRow = $(elem).closest('.category-row');
        let categoryId = $(categoryRow).attr('data-id');
        let categoryName = $(categoryRow).find('.category-name:first').text();

        const apiProps = {
            fun: 'GetAllTemplateByCategory',
            CategoryId: categoryId
        };
        postApi('calendarSettings', apiProps, 'meetingCategories.renderConfirmDeleteCategory', true)
        //update page data
        $(dataElem).find('.category-name').text(categoryName);
        $(dataElem).attr('data-id', categoryId);
        let selectElem = $(dataElem).find('#js-select2-template-category').val('').trigger('change');
        let option =$(dataElem).find(`#js-select2-template-category option[value='${categoryId}']`);
        $(option).attr('disabled', 'disabled');
        globalCalendarSettings.select2CategoryParam(selectElem);


    },
    // add count related template to the page and hide loaders, if none related delete category
    renderConfirmDeleteCategory: function (data) {
        let dataElem = $(this.confirmDeleteElem).find('.category-confirm-deleted-section');
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(dataElem), 'calendarSettings-meetings-category');
            return;
        }
        let templates = data.templates;
        let categoryId = $(dataElem).attr('data-id');

        let elem = $(meetingCategories.mainElem).find(`.meetings-category-list .category-row[data-id=${categoryId}]`);
        //There are no templates with the selected category - only remove
        if(templates.length < 1) {
            this.deleteCategory(elem);
            return;
        }
        switchSettingsPanel($('.meetings-category-list:first'), 'calendarSettings-meetings-category-remove');



        let divButton = $(this.confirmDeleteElem).find('.js-remove-button');
        $(divButton).find('.js-replace-category-button:first').off("click");
        $(divButton).find('.js-replace-category-button:first')
            .on("click", {oldId: categoryId, templates:templates } ,meetingCategories.replaceCategoryInTemplates);

        //add loader
        let item = $(elem).closest('.category-row');
        globalCalendarSettings.toggleDeleteLoader(item, true);

        $(dataElem).find('.template-count').text(templates.length);
        //hide loader
        $(this.confirmDeleteElem).find('.loading-confirm-deleted-section').addClass('d-none');
        $(divButton).removeClass('d-none');
        $(dataElem).removeClass('d-none');
    },
    //replace category id in template and delete old category
    replaceCategoryInTemplates: function (e) {
        $(this).addClass("disabled");
        let fields = $(this).closest('.calendarSettings-meetings-category-remove').find(":input[required]");
        if (validateTemplateFields(fields)) {
            $(this).text(lang('loading_datatables'));
            let newCategory = $(this).closest('.calendarSettings-meetings-category-remove')
                .find('.category-confirm-deleted-section #js-select2-template-category');
            const newCategoryId =  $(newCategory).val();
            const oldCategoryId = e.data.oldId;
            //from array of object to array
            let templates = e.data.templates.map(function (obj) {
                return obj.id;
            });
            const apiProps = {
                fun: 'ReplaceCategoryInTemplates',
                newCategoryId: newCategoryId,
                oldCategoryId: oldCategoryId,
                template: templates,
                isNewCategory: $(newCategory).find(':selected[data-select2-tag]').length ? 1 : 0
            }
            postApi('calendarSettings', apiProps, 'meetingCategories.updateFrontCategory', true)
        } else {
            $(this).removeClass("disabled");
        }
    },
    //delete category form backend
    deleteCategory: function (elem) {
        //add loader
        let item = $(elem).closest('.category-row');
        globalCalendarSettings.toggleDeleteLoader(item, true);
        let item_id = item.data('id');
        const apiProps = {
            fun: 'RemoveMeetingCategory',
            id: item_id
        };
        postApi('calendarSettings', apiProps, 'meetingCategories.updateFrontCategory', true)
    },
    //show and hide editing area
    toggleButtonAddCategory: function (elem) {
        let old_val = '',
            form = $(elem).parents('.js-add-category');
        if (form.children('.form-static').hasClass('d-none')) {
            form.children('.form-static')
                .toggleClass('d-flex d-none');
            form.children('.form-inline')
                .remove()
        } else {
            $(this.mainElem).find('.form-static.d-none').toggleClass('d-flex d-none');
            $(this.mainElem).find('.form-inline').remove()

            if (form.find('.category-name').length)
                old_val = form.find('.category-name').text();
            form.children('.form-static').toggleClass('d-flex d-none');
            form.append(`
                <div class="form-inline d-flex"> <label class="sr-only" for="inlineFormInputGroup"></label> 
                <div class="input-group d-flex flex-row w-100 border bsapp-border-primary rounded">
                 <input autocomplete="off" type="text" class="w-75 flex-grow-1 outline-none border-0 shadow-none py-5 pie-10"
                  id="inlineFormInputGroup" placeholder="${lang("enter_name")}" value="${old_val}" maxlength="60">
                   <div class="w-25 input-group-apend border-0 rounded-right">
                        <div class="btn text-primary py-3 px-7 bsapp-fs-24" onclick="meetingCategories.saveCategory(this)">
                         <i class="fal fa-check-circle"></i>
                        </div>
                      <div class="btn text-gray-700 py-3 px-7 bsapp-fs-24" onclick="meetingCategories.toggleButtonAddCategory(this)">
                       <i class="fal fa-minus-circle"></i>
                        </div></div></div></div>`)
        }
    },
    //save after change name
    saveCategory: function (elem) {
        let form = $(elem).closest('.js-add-category'),
            newName = form.find('input#inlineFormInputGroup').val(),
            oldNameElem = form.find('.category-name:first');

        if (newName.length  < 1 || newName.length  >  65) {
            form.find('input#inlineFormInputGroup').addClass(['border','border-danger', 'in-valid-field'])
                .removeClass('border-0 ');
            return;
        } else {
            form.find('input#inlineFormInputGroup').removeClass(['border','border-danger', 'in-valid-field'])
                .addClass('border-0');
        }
        let item = $(elem).closest('.category-row');
        globalCalendarSettings.toggleDeleteLoader(item, true);
        const item_id = item.data('id');
        if (item_id != undefined) { // If exists = edited
            oldNameElem.text(newName);
            const apiProps = {
                id: item_id,
                CategoryName: newName,
                fun: 'EditMeetingCategory'
            };
            postApi('calendarSettings', apiProps, 'meetingCategories.updateFrontCategory', true);
        } else { // if new
            if (newName != '') {
                const apiProps = {
                    CompanyNum: $companyNo,
                    CategoryName: newName,
                    fun: 'CreateMeetingCategory'
                }
                postApi('calendarSettings', apiProps, 'meetingCategories.updateFrontCategory', true);
            }
        }
    },
    //hide the loader of delete element
    removeDeleteLoaders: function () {
        let selectElem = $(meetingCategories.confirmDeleteElem)
            .find(`#js-select2-template-category option:disabled`).removeAttr('disabled');
        globalCalendarSettings.select2CategoryParam(selectElem);

        let item = $(this.mainElem).find(`.category-row #delete-loader`).closest('.category-row');
        globalCalendarSettings.toggleDeleteLoader(item, false);
        $(item).find('.js-add-category').removeClass('d-flex');

        $(this.confirmDeleteElem).find('.js-remove-button .js-replace-category-button')
            .removeClass("disabled").text(lang('change_and_delete'));
    },
    //Updates the view according to the action performed
    updateFrontCategory: function (data) {
        if (globalCalendarSettings.errorChecking(data)) {
            let item = $(this.mainElem).find(`.category-row #delete-loader`).closest('.category-row');
            switch (data.action) {
                case 'new':
                    this.getAllMeetingCategories();
                    $(item).find('.form-inline:first').remove();
                    break;
                case 'edit':
                    globalCalendarSettings.getMeetingCategories();
                    item = $(this.mainElem).find(`.meetings-category-list .category-row[data-id='${data.response.id}']`);
                    $(item).find('.form-static:first').toggleClass('d-flex d-none');
                    $(item).find('.form-inline:first').remove();
                    break;
                case 'replace':
                    let dataElem = $(this.confirmDeleteElem).find('.category-confirm-deleted-section');
                    switchSettingsPanel($(dataElem), 'calendarSettings-meetings-category');
                    meetingCategories.getAllMeetingCategories();
                    break;
                case 'remove':
                    let categoriesList = $(item).closest('.meetings-category-list');
                    if ($(categoriesList).find('.category-row').length === 1) {
                        $(categoriesList).append(`<li class="mb-10 animated fadeInUp"><div id="categories-not-found" class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_categories_found')}</div></li>`)
                    }
                    item.remove();
                    globalCalendarSettings.getMeetingCategories();
                    switchSettingsPanel($('.category-confirm-deleted-section:first'), 'calendarSettings-meetings-category');
                    break
            }
        }
        this.removeDeleteLoaders();


    }
}
let meetingStaff = {
    mainElem: null,
    coachAvailability: null,
    addAvailability: null,
    deleteAvailability: null,
    /*********************** Get staff ***********************/
    //get all Templates of this company
    getAllCoaches: function (elem) {
        if (this.mainElem == null) {
            this.mainElem = $(elem).closest('.dropdown-menu').find('.calendarSettings-meetings-staff-availability:first');
            this.coachAvailability = $(elem).closest('.dropdown-menu').find('.meetings-coach-weekly-availability:first');
            this.addAvailability = $(elem).closest('.dropdown-menu').find('.meetings-add-coach-availability:first');
            this.deleteAvailability = $(elem).closest('.dropdown-menu').find('.delete-coach-availability:first');

            let startDate;
            let endDate;
            $('#week-picker').datepicker( {
                dateFormat: "yy-mm-dd",
                maxDate: "+5M",
                minDate: 0,
                showOn: '',
                onSelect: function() {
                    const id = $(meetingStaff.coachAvailability).find(".days-time-section .list-days-time").attr('user-id');
                    let date = $(this).datepicker('getDate');
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
                    meetingStaff.loadAvailabilityCoach(id, date.format('yyyy-mm-dd'))
                },
                beforeShowDay: function(date) {
                    var cssClass = '';
                    if(date >= startDate && date <= endDate) {
                        cssClass = 'ui-datepicker-current-day';
                    }
                    return [true, cssClass];
                },
            });
            $("#alt-date").click(function(){
                $("#week-picker").datepicker('show');
            })

            jQuery(function($){
                const selectedLanguage = $.cookie('boostapp_lang') ?? 'he';
                switch (selectedLanguage) {
                    case 'he':
                        $.datepicker.regional['he'] = {
                            closeText: 'סגור',
                            prevText: '',
                            nextText: '',
                            currentText: 'היום',
                            monthNames: ['ינואר','פברואר','מרץ','אפריל','מאי','יוני',
                                'יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],
                            monthNamesShort: ['ינו','פבר','מרץ','אפר','מאי','יוני',
                                'יולי','אוג','ספט','אוק','נוב','דצמ'],
                            dayNames: ['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],
                            dayNamesShort: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
                            dayNamesMin: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
                            weekHeader: 'Wk',
                            dateFormat: 'dd/mm/yy',
                            firstDay: 0,
                            isRTL: true,
                            showMonthAfterYear: false,
                            yearSuffix: ''};
                        $.datepicker.setDefaults($.datepicker.regional['he']);
                        break;
                    case 'en':
                        break;
                }



            });
            // $('#ui-datepicker-div .ui-datepicker-calendar tr').on('mousemove', function() { $(this).closest("tr").find('td a').addClass('ui-state-hover'); });
            // $('#ui-datepicker-div .ui-datepicker-calendar tr').on('mouseleave', function() { $(this).closest("tr").find('td a').removeClass('ui-state-hover'); });
        }
        //remove template front and show loader
        $(this.mainElem).find('.coaches-list').children().not(".item-loading").remove();
        $(this.mainElem).find('.coaches-list').children('.item-loading').show();
        const apiProps = {
            fun: 'GetStaffByCompanyNum',
            CompanyNum: $companyNo
        }
        postApi('calendarSettings', apiProps, 'meetingStaff.renderCoaches', true);
    },
    //render Coaches option
    renderCoaches: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.mainElem).find('.coaches-list'), 'Meetings-navigation');
            return;
        }
        let result = data.CoachList;
        if (result.length) {
            $.each(result, function (key) {
                let image = ((result[key].UploadImage != null) && (result[key].UploadImage != '')) ? '../camera/uploads/small/' + result[key].UploadImage : 'assets/img/default-avatar.png';
                $('.coaches-list').append(meetingStaff.coachRow(result[key].AvailabilityStatus, result[key].id, image, result[key].display_name))
            })
        } else {
            if ($(this.mainElem).find('#coaches-not-found').length === 0)
                $(this.mainElem).find('.coaches-list')
                    .append(`<li class="mb-10 animated fadeInUp"><div id="coaches-not-found" class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang('no_coaches_found')}</div></li>`)
        }
        //hide loader
        $(this.mainElem).find('.coaches-list').children('.item-loading').hide()
    },
    // create coach row from data
    coachRow: function (status, id, image, name) {
        let checked = '',
            disabled = '';
        if (status == 1)
            checked = 'checked'
        else
            disabled = 'disabled-style';
        return `<li class="mb-10 js-coach-row ${disabled}" data-id="${id}">
                    <div class="form-static d-flex align-items-center border rounded pt-7 pb-6 px-0">
                     <div class="col-8 d-flex align-items-center font-weight-bold text-start pis-10 pie-0">
                      <img class="bsapp-appointments-img mie-7" src="${image}" alt="${name}"/>
                       <span class="coach-name">${name}</span> 
                     </div>
                     <div class="col-4 d-flex align-items-center justify-content-between text-gray-700 pie-10">
                      <a tabindex="0" role="button" class="disabled bsapp-fs-21" title="${name + " " + lang('copy_link')}"
                       data-toggle="tooltip" data-code="url-to-coach" style="visibility: hidden;" >
                       <i class="fal fa-link mie-7"></i>
                      </a>
                       <div class="custom-control custom-switch">
                       <input type="checkbox" onclick="meetingStaff.changeCoachAvailabilityStatus(this)" class="hide-coacher hide-toggle custom-control-input" id="coach-${id}" ${checked}>
                        <label class="custom-control-label" for="coach-${id}" role="button"></label>
                         </div><a class="loadAvilabilatylink" role="button" data-target="meetings-coach-weekly-availability" 
                         onclick="meetingStaff.loadAvailabilityCoach(${id}, false,'${name.replace(/['"]+/g, '')}')">
                          <i class="fal fa-chevron-right bsapp-fs-16"></i>
                   </a> </div></div></li>`
    },
    /*********************** Main page staff function ***********************/
    //update Availability Status of the user
    changeCoachAvailabilityStatus: function (elem) {
        let coachElem = $(elem).closest('.js-coach-row'),
            coachId = coachElem.attr('data-id'),
            availabilityStatus = (coachElem.hasClass('disabled-style')) ? 1 : 0;
        const apiProps = {
            fun: 'ChangeAvailabilityStatus',
            id: coachId,
            Status: availabilityStatus
        }
        postApi('calendarSettings', apiProps)
    },
    /*********************** Coach Availability ***********************/
    //load availability coach times
    loadAvailabilityCoach: function (id, day = false, name = null) {
        //show loading and show setting;
        $(this.coachAvailability).find('.days-time-section ul.list-of-loading').removeClass('d-none');
        $(this.coachAvailability).find('.days-time-section ul.list-days-time').addClass('d-none');
        // show name of the coach in the page
        if (name) {
            $(this.coachAvailability).find('.path-coach-name:first').text(name);
        }
        //set default day (today) if false
        let currDate;
        if (!day) {
            currDate = new Date();
        } else {
            currDate = new Date(day);
        }
        this.renderDaysAndTimePicker(currDate);
        //get the first day of this week
        let first = new Date(currDate.setDate(currDate.getDate() - currDate.getDay()));
        const apiProps = {
            "fun": "GetCoachWeekAvailability",
            "userId": id,
            "date": first.format("yyyy-mm-dd")
        };
        postApi('calendarSettings', apiProps, 'meetingStaff.renderCoachAvailabilityData', true);
    },
    // change week-picker and days title on day list
    renderDaysAndTimePicker: function (day) {
        $(this.coachAvailability).find('#week-picker')
            .val(day.format('yyyy-mm-dd')).trigger('change');
        const first = day.getDate() - day.getDay();
        let firstDay = new Date(day.setDate(first));
        //add range in edit coachAvailability
        $(this.coachAvailability).find('.days-time-section .list-days-time .days')
            .each(function (index) {
                let date = new Date(firstDay);
                date.setDate(date.getDate() + index)
                $(this).find(".date-of-day").text(date.format("dd/mm/yy"));
                $(this).find(".add-new-time").attr("data-time", date.format("yyyy-mm-dd"));
            })
    },
    renderCoachAvailabilityData: function (data) {
        let listDayElem = $(this.coachAvailability).find(".days-time-section .list-days-time")
            .attr('user-id', data.userId);
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(listDayElem), 'calendarSettings-meetings-staff-availability');
            return;
        }
        //remove all old data inside days section
        $(listDayElem).find(".times-list").children().remove();
        let res = data.weekAvailability
        //add data to day list
        res.forEach(function (elm, index) {
            meetingStaff.addAvailabilityDataToDay(elm)
        });
        this.testLimitAddAvailability();

        //hide loading and show list of day;
        $(this.coachAvailability).find('.days-time-section ul.list-of-loading').addClass('d-none');
        $(this.coachAvailability).find('.days-time-section ul.list-days-time').removeClass('d-none');
    },
    // Adds blocks of times for the relevant day
    addAvailabilityDataToDay: function (item) {
        $(this.coachAvailability).find(`.days-time-section [data-day=${item.Day}] .times-list`)
            .append(
                `<div class="position-relative time-box d-inline-block" date-id="${item.id}"  rule-id="${item.RuleAvailabilityId}">
                    <input type="text" disabled class="bsapp-startTime w-100 bg-light border shadow-none  
                    outline-none rounded py-6 pis-10 pie-0" value="${item.StartTime.slice(0, 5)} - ${item.EndTime.slice(0, 5)}">
                        <a tabindex="0" class="EditLink" role="button" onclick="meetingStaff.editAvailabilityTime(this)" data-target="meetings-add-coach-availability">
                            <i class="fa fa-edit edit grey position-absolute"></i>
                        </a></div>`);
    },
    // test if in some day has more than 2 records if soo, disabled add option
    testLimitAddAvailability: function () {
        $(this.coachAvailability).find('.list-days-time li.days').each(function () {
            if ($(this).find('.times-list .time-box').length > 2) {
                $(this).find('.add-new-time').addClass('disabled');
            } else {
                $(this).find('.add-new-time').removeClass('disabled');
            }
        })
    },
    //show new availability page after seting default data into the fields
    renderNewAvailabilityPage: function (elem) {
        //show loader
        $(this.addAvailability).find('.list-of-loading').removeClass('d-none');
        let detailsSection = $(this.addAvailability).find('.availability-details-section').addClass('d-none');
        detailsSection.find('.periodic-edit-fields').addClass('d-none').find(':input').prop('required',false);


        let availabilityFields = detailsSection.find('.availability-fields');
        $(availabilityFields).find('.availability-details-part-1').addClass('d-flex').removeClass('d-none')


        // set sub-title date and time
        const removeBlock = $(this.addAvailability).find('.remove-block');
        $(removeBlock).find('.title-remove').addClass('d-none');
        let dateSub = $(removeBlock).find('.date-sub-title');
        const dateTimeSub = $(elem).closest('.days').find('.title-day').text();
        $(dateSub).find('.title-date').text(dateTimeSub);
        $(dateSub).find('.title-date-time').addClass('d-none').text('');


        const userId = $(elem).closest('.list-days-time').attr('user-id');
        const dateTime = $(elem).attr('data-time');
        const day = $(elem).closest('.days').attr('data-day');
        const name = $(this.coachAvailability).find('.path-coach-name:first').text();

        $(availabilityFields).find('input[name="UserId"]').val(userId);
        $(availabilityFields).find('input[name="Date"]').val(dateTime);
        $(availabilityFields).find('input[name="Day"]').val(day);
        $(availabilityFields).removeAttr('was-repeat-status');
        $(this.addAvailability).find('.path-coach-name:first').text(name);
        $(availabilityFields).find('.end-periodic-date-section:first #end-periodic-date')
            .attr('min',dateTime).val("").trigger('change');

        $(availabilityFields).find('.repeat-status').val('1').trigger('change');
        $(availabilityFields).find('.end-periodic-date-status').val('0').trigger('change');

        $(availabilityFields).find('.pick-time-section .time-input').val('').trigger('change');
        $(this.addAvailability).find('.js-save-time-availability a')
            .removeClass('edit-time-availability').addClass('save-time-availability').attr('data-id', '');
        // hide loader
        $(this.addAvailability).find('.list-of-loading').addClass('d-none');
        detailsSection.removeClass('d-none');

    },
    //add input value to add availability page
    editAvailabilityTime: function (elem) {
        //show loader
        $(this.addAvailability).find('.list-of-loading').removeClass('d-none');
        $(this.addAvailability).find('.availability-details-section').addClass('d-none');
        const timeBox = $(elem).closest('.time-box');

        const removeBlock = $(this.addAvailability).find('.remove-block');
        const dateTime = $(timeBox).closest('.days').find('.title-day').text();
        const timeTitle = $(timeBox).find('.bsapp-startTime').val();
        const name = $(this.coachAvailability).find('.path-coach-name:first').text();

        // set sub-title date and time
        $(removeBlock).find('.title-remove').removeClass('d-none');
        let dateSub = $(removeBlock).find('.date-sub-title')
        $(dateSub).find('.title-date').text(dateTime);
        $(dateSub).find('.title-date-time').removeClass('d-none').text(timeTitle);
        $(this.addAvailability).find('.path-coach-name:first').text(name);

        //set delete page values
        const deleteWarning = $(this.deleteAvailability).find('.delete-section .delete-warning');
        $(deleteWarning).find('.title-coach-name').text(name);
        $(deleteWarning).find('.title-date').text(dateTime);
        $(deleteWarning).find('.title-date-time').text(timeTitle);

        const apiProps = {
            "fun": "GetAvailabilityTime",
            "dateId": $(timeBox).attr('date-id'),
            "ruleId": $(timeBox).attr('rule-id')
        };
        postApi('calendarSettings', apiProps, 'meetingStaff.renderEditAvailabilityPage', true);
    },
    renderEditAvailabilityPage: function (data) {
        let detailsSection = $(this.addAvailability).find('.availability-details-section');
        let availabilityFields = detailsSection.find('.availability-fields');

        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel(detailsSection, 'meetings-coach-weekly-availability');
            return;
        }
        const dateAvailability = data.availability.dateAvailability[0];
        const ruleAvailability = data.availability.ruleAvailability;
        availabilityFields.find('input[name="Date"]').val(dateAvailability.Date);
        availabilityFields.find('input[name="UserId"]').val(ruleAvailability.UserId);
        availabilityFields.find('input[name="Day"]').val(ruleAvailability.Day);
        availabilityFields.find('.end-periodic-date-section:first #end-periodic-date')
            .attr('min',dateAvailability.Date).val(ruleAvailability.EndPeriodicDate ?? '').trigger('change');
        availabilityFields.find('.end-periodic-date-status').val(ruleAvailability.EndPeriodicDate ? '1' : '0' ).trigger('change');
        detailsSection.find('.periodic-edit-fields .end-periodic-date :input').attr('min',dateAvailability.Date);

        if (ruleAvailability.RepeatStatus === '0' ) {
            availabilityFields.removeAttr('was-repeat-status');
            availabilityFields.find('.availability-details-part-1').addClass('d-flex').removeClass('d-none');
            availabilityFields.find('.repeat-status').val(ruleAvailability.RepeatStatus).trigger('change');
            detailsSection.find('.periodic-edit-fields').addClass('d-none').find(':input').prop('required',false);
            $(this.deleteAvailability).find('.delete-section .periodic-edit-fields').addClass('d-none').find(':input').prop('required',false);
        } else {
            availabilityFields.find('.repeat-status').val(ruleAvailability.RepeatStatus)
            availabilityFields.attr('was-repeat-status',true);
            availabilityFields.find('.availability-details-part-1').removeClass('d-flex').addClass('d-none');
            detailsSection.find('.periodic-edit-fields .end-periodic').val(2).trigger('change');
            detailsSection.find('.periodic-edit-fields .edit-mode').val(0).trigger('change');
            detailsSection.find('.periodic-edit-fields').removeClass('d-none').find('.edit-mode').prop('required',true);
            detailsSection.find('.periodic-edit-fields .start-periodic option.date-option').attr('value',dateAvailability.Date);

            let deleteWarrning = $(this.deleteAvailability).find('.delete-section .periodic-edit-fields').removeClass('d-none');
            deleteWarrning.find('.end-periodic').val(2).trigger('change');
            deleteWarrning.find('.edit-mode').val(0).trigger('change').prop('required',true);
            deleteWarrning.find('.start-periodic option.date-option').attr('value',dateAvailability.Date);
        }
        availabilityFields.find('.pick-time-section .time-input[name="StartTime"]').val(ruleAvailability.StartTime);
        availabilityFields.find('.pick-time-section .time-input[name="EndTime"]').val(ruleAvailability.EndTime).trigger('change');
        $(this.addAvailability).find('.js-save-time-availability a')
            .removeClass('save-time-availability').addClass('edit-time-availability')
            .attr('date-id',dateAvailability.id).attr('rule-id',ruleAvailability.id);
        $(this.addAvailability).find('.list-of-loading').addClass('d-none');
        detailsSection.removeClass('d-none');

        let inputs =  availabilityFields.find(':input');
        $(inputs).removeClass('changed');
        $(inputs).change(function () {
            $(this).addClass('changed');
        });

    },
    pickDateChange: function (elem) {
        const date  =new Date($(elem).val());
        const first = date.getDate() - date.getDay();
        const last = first + 6;
        let firstDay = new Date(date.setDate(first));
        let lastDay = new Date(date.setDate(last));
        //add range in edit coachAvailability
        let timeSection = $(this.coachAvailability).find('.time-selection-section')
        $(timeSection).find('#alt-date-input').val(firstDay.format("dd") + "-" + lastDay.format("dd/mm/yy"));
        // disabled button next and prev week if need to
        const today = new Date();
        $(timeSection).find('.next-week').removeClass('disabled');
        $(timeSection).find('.prev-week').removeClass('disabled');
        if(firstDay <= today) {
            $(timeSection).find('.prev-week').addClass('disabled');
        } else if (lastDay >=  new Date(today.setMonth(today.getMonth()+5))) {
            $(timeSection).find('.next-week').addClass('disabled');
        }
    },
    changeDate: function (elem, add=false) {
        const id = $(this.coachAvailability).find(".days-time-section .list-days-time").attr('user-id');
        let newDate = null;
        if(add) {
            newDate = new Date($(elem).parents('.time-selection-section').find('#week-picker').val());
            newDate = new Date(newDate.setDate(newDate.getDate() + add)).format('yyyy-mm-dd');
        }
        this.loadAvailabilityCoach(id, newDate)
    },
    /*********************** add Availability time ***********************/
    // When time change test if valid - start time <  end time
    timeChange: function (elem) {
        let times = $(elem).closest('.pick-time-section').find('.time-input');
        if( (times[0].value >= times[1].value)  && (times[0].value && times[1].value)) {
            $(elem).addClass('border-danger').attr('not-valid',true)
                .parent().find('.validation-warning:first').removeClass('d-none').text('ערך לא תקין')
        } else {
            $(times).each(function() {
                $(this).removeClass('border-danger').removeAttr('not-valid')
                    .parent().find('.validation-warning:first').addClass('d-none').text('');
            })
        }

    },
    // When repeat status change hide/ show end-repeated-section
    repeatStatusChange: function (elem) {
        const repeatStatus = $(elem).find(':selected').val();
        let endRepeated = $(elem).closest('.availability-fields').find('.end-repeated-section:first');
        switch (repeatStatus) {
            case REPEAT_STATUS_OFF:
                $(endRepeated).addClass('d-none').removeClass('d-flex')
                    .find(':input').prop('required',false);
                break;
            case REPEAT_STATUS_WEEK:
                $(endRepeated).removeClass('d-none').addClass('d-flex')
                    .find(':input.end-periodic-date-status').prop('required',true);
                if(endRepeated.value === '0') {
                    $(endRepeated).find('.end-periodic-date-section :input').prop('required', false);
                }
                break;
        }
    },
    // When end repeat status change hide/ show end repeated date section
    endRepeatedStatusChange: function (elem) {
        const endRepeatedStatus = $(elem).find(':selected').val();
        let endRepeatedDate = $(elem).closest('.end-repeated-section').find('.end-periodic-date-section:first');
        switch (endRepeatedStatus) {
            case REPEAT_FOREVER:
                $(endRepeatedDate).addClass('d-none')
                    .find(':input').prop('required',false);
                break;
            case REPEAT_END_DATE:
                $(endRepeatedDate).removeClass('d-none')
                    .find(':input').prop('required',true);
                break;
        }
    },
    // When end repeat date change show in dd/m/yyyy format or "enter date"
    endRepeatedDateChange: function (elem) {
        if($(elem).val() == "" ) {
            elem.setAttribute(
                "data-date", lang('select_date'));
        } else {
            elem.setAttribute(
                "data-date",
                moment(elem.value, "YYYY-MM-DD")
                    .format( elem.getAttribute("data-date-format") )
            )
        }
    },
    // When end repeat status change hide/ show end repeated date section
    editModeChange: function (elem) {
        const editMode = $(elem).find(':selected').val();
        let periodicDetails = $(elem).closest('.edit-mode-section').find('.periodic-details-mode');
        switch (editMode) {
            case EDIT_MODE_ONLY_THIS:
                $(periodicDetails).addClass('d-none').removeClass('d-flex')
                    .find(':input').prop('required',false);
                break;
            case EDIT_MODE_AS_SEQUENCE:
                $(periodicDetails).removeClass('d-none').addClass('d-flex')
                    .find(':input.edit-mode-1').prop('required',true);
                break;
        }
    },
    // When end repeat status change hide/ show end repeated date section
    endPeriodicChange: function (elem) {
        const endPeriodicType = $(elem).find(':selected').val();
        let endPeriodicDetails = $(elem).closest('.end-periodic-details')
        $(endPeriodicDetails).find('.end-periodic-details').addClass('d-none')
            .find(':input').prop('required',false);
        switch (endPeriodicType) {
            case EDIT_MODE_END_BY_DATE:
                $(endPeriodicDetails).find('.end-periodic-date:first').removeClass('d-none')
                    .find(':input').prop('required',true);
                break;
            case EDIT_MODE_END_BY_AMOUNT:
                $(endPeriodicDetails).find('.end-periodic-amount:first').removeClass('d-none')
                    .find(':input').prop('required',true);
                break;
            case EDIT_MODE_END_INFINITE:
                break;
        }
    },
    //save (edit or create new availability) validation and send to db
    saveAvailability: function(elem) {
        $(elem).addClass("disabled");
        let apiProps = {};
        let isNew = true;
        let fields = $(elem).parents('.bsapp-settings-panel').find("[required]");
        if (validateTemplateFields(fields)) {
            let changedFields = null
            if ($(elem).hasClass('edit-time-availability')) {
                apiProps['dateId'] = $(elem).attr('date-id');
                apiProps['ruleId'] = $(elem).attr('rule-id');
                isNew = false;
                changedFields = $(this.addAvailability).find('.availability-fields .changed:input,' +
                    '.periodic-edit-fields :input[required]');
            } else {
                changedFields = $(this.addAvailability).find('.availability-fields :input');
            }
            //noting to change
            if (changedFields.length < 1) {
                $(elem).removeClass('disabled');
                switchSettingsPanel($(elem), 'meetings-coach-weekly-availability');
                return;
            }
            $(elem).text(lang('loading_datatables'));
            $(changedFields).each(function () {
                apiProps[$(this).attr("name")] = $(this).val();
            });
            apiProps['fun'] = isNew ? 'CreateNewAvailability' : 'UpdateAvailability';
            if($(this.addAvailability).find('.availability-fields').attr('was-repeat-status')) {
                apiProps['WasRepeatStatus'] = '1';
            } else {
                apiProps['WasRepeatStatus'] = '0';

            }
            postApi('calendarSettings', apiProps, 'meetingStaff.renderAfterSaveAvailability', true);
        } else {
            $(elem).removeClass('disabled');
        }

    },
    //after edit or create, remove old and add to front new time block
    renderAfterSaveAvailability: function (data) {
        let saveElem = $(this.addAvailability).find('.js-save-time-availability a');
        const buttonText = $(saveElem).hasClass('edit-time-availability') ? lang('save_changes_button'): lang('save');
        saveElem.removeClass('disabled').text(buttonText);
        if (!globalCalendarSettings.errorChecking(data)) {
            return;
        }
        let ruleAvailability = data.response;
        ruleAvailability['RuleAvailabilityId'] = ruleAvailability.id;
        ruleAvailability['id'] = data.dateId;
        //edit block..
        if (!data.isNew) {
            $(this.coachAvailability)
                .find(`.days-time-section [data-day=${ruleAvailability.Day}] .times-list [date-id=${ruleAvailability.id}]`)
                .remove();
            this.addAvailabilityDataToDay(ruleAvailability)
        } else {
            this.addAvailabilityDataToDay(ruleAvailability)
        }
        //return to back page
        switchSettingsPanel($(saveElem), 'meetings-coach-weekly-availability');
    },
    /*********************** remove Availability time ***********************/
    //add date and rule id to delete button
    renderDeleteAvailability: function(elem) {
        const saveInput = $(elem).closest('.meetings-add-coach-availability').find('.save-section:first .edit-time-availability');
        const dateId = $(saveInput).attr('date-id');
        const ruleId = $(saveInput).attr('rule-id');
        $(this.deleteAvailability).find('.save-delete-section:first .delete-availability')
            .attr('date-id', dateId)
            .attr('rule-id', ruleId);

    },
    //delete availability from db
    removeAvailability: function(elem) {
        $(elem).addClass("disabled").text(lang('loading_datatables'));
        let apiProps = {
            'fun': 'DeleteAvailability',
            'dateId': $(elem).attr('date-id'),
            'ruleId': $(elem).attr('rule-id')
        };

        let changedFields = $(this.deleteAvailability).find('.periodic-edit-fields :input[required]');

        $(changedFields).each(function () {
            apiProps[$(this).attr("name")] = $(this).val();
        });

        postApi('calendarSettings', apiProps, 'meetingStaff.renderAfterDeleteAvailability', true);
    },
    //if succeeded return to coach-weekly-availability
    renderAfterDeleteAvailability: function (data) {
        let deleteElem = $(this.deleteAvailability).find('.save-delete-section:first .delete-availability');
        deleteElem.removeClass('disabled').text(lang('delete'));
        if (!globalCalendarSettings.errorChecking(data)) {
            return;
        }
        $(this.coachAvailability)
            .find(`.days-time-section [data-day=${data.Day}] .times-list [date-id=${data.id}]`)
            .remove();
        switchSettingsPanel($(deleteElem), 'meetings-coach-weekly-availability');


    },
}



/***** calendars And Classes *****/
let calendarsAndClasses = {
    mainElem: null,
    addElem: null,
    //save main element
    init: function (elem){
        this.mainElem = $(elem).parents('#calendarSettings').find('.calendarSettings-calendars-and-classes_classes');
        this.addElem = $(elem).parents('#calendarSettings').find('.calendarSettings-classes--new');
        $(this.addElem).find('.js-select2-dropdown-arrow-classes').select2({
            minimumResultsForSearch: -1,
            theme: "bsapp-dropdown",
            allowClear: true,
            placeholder: lang('select_default_class_duration'),
        })
    },
    //get all classes and  show loader for waiting time
    getClasses: function () {
        //add loader
        $(this.mainElem).find('.classes-list li:not(.item-loading)').remove();
        $(this.mainElem).find('.classes-list .item-loading').show();
        // get all class type
        const apiProps = {
            CompanyNum: $companyNo,
            fun: 'GetAllClassTypes'};
        postApi('calendarSettings', apiProps, 'calendarsAndClasses.renderClasses', true);
        this.getClassesMemberships()
    },
    // create list of classes-type if not empty
    renderClasses: function (data) {
        if (!globalCalendarSettings.errorChecking(data)) {
            switchSettingsPanel($(this.mainElem).find('.classes-list'), 'calendarSettings-calendars-and-classes');
            return;
        }
        let result = data.ClassTypes;
        $(this.mainElem).find('.classes-list .item-loading').hide();
        $(this.mainElem).find('.classes-list li:not(.item-loading)').remove();
        if (result.length) {
            let classesList =  $(this.mainElem).find('.classes-list');
            $.each(result, function (key) {
                $(classesList).append(
                    calendarsAndClasses.classesRow(result[key].id,
                        globalCalendarSettings.findColorHexById(result[key].Color), result[key].Type, result[key].duration))
            })
        } else
            $(this.mainElem).find('.classes-list').append('' +
                '<li class="mb-10 animated fadeInUp">' +
                '<div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">' +
                lang("classes_not_found_app_booking") + '</div></li>'
            );
        globalCalendarSettings.select2ClassType(data)
    },
    // create front row
    classesRow: function (id, color, name, duration) {
        let durationText = globalCalendarSettings.fromMinutesToFormatTime(duration);
        let markup = `<li class="mb-10 bsapp-class-row" data-id="${id}">
                      <div class="form-static d-flex align-items-center border rounded py-7 px-0">
                        <div class="d-flex align-items-center col-7 font-weight-bold text-start pis-12 pie-0">
                         <i class="fa fa-circle mie-7 bsapp-fs-14" style="color:${color}"></i>
                          <span class="class-name" style="overflow: hidden;text-overflow: ellipsis; 
                            display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;">${name}</span>
                           </div><div class="col-4 text-end text-gray-700 px-0">`
            + (duration ? `${durationText}` : `${lang('without')}`) + `</div><div class="col-1 text-end pie-10"> 
                           <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button">
                            <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                             <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow">
                              <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> 
                              <a role="button" class="edit-class d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                               <i class="fal fa-edit fa-fw mx-5"></i> `+ lang('edit') +` </a> </li><li>
                                <a role="button" class="delete-class d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">
                                 <i class="fal fa-minus-circle fa-fw mx-5"></i> `+ lang('a_remove_single') +` </a> </li></ul> </div></div></div></div></li>`;
        return markup
    },

    getClassesMemberships: function () {
        $.ajax({
            method: 'GET',
            url: '/office/partials-views/add-meeting/modal-new-class-type.php',
            success: function (res) {
                $('.newClass--memberships').append(res);
            }
        })
    },

}

//validation required fields
function validateTemplateFields(fields) {

    let validated = true;

    if(fields?.length < 1) {
        return validated
    }
    fields.each(function () {
        let value = $(this).val();
        if(value == null) {
            value = $(this).find(':selected').val();
        }
        let validType = true
        if($(this).attr('type') == 'url') {
            validType = value.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
        } else if($(this).attr('not-valid')){
            validType = false;
        }

        if (value !== '' && value !== null && value !== undefined && validType) {
            $(this).removeClass(['border-danger', 'in-valid-field'])
                .siblings('.input-group-append, .select2-container')
                .removeClass('border border-danger');
        } else {
            //If not validated in the basic tab show this tab
            if($(this).parents('#list-template-basics').length)
                $(this).parents('.calendarSettings-meetings-templates-new:first').find('.bsapp-tabs-navigation #list-template-basics-list').click();
            //change the field border to red
            $(this).addClass(['border-danger', 'in-valid-field'])
                .siblings('.input-group-append, .select2-container')
                .addClass('border border-danger');
            $(this).on('change', function () {
                validateTemplateFields($(this))
            });
            validated = false;
        }
    }); // change to true for development
    return validated
}






//todo move to advanced-setting?
//When entering advanced setting: displays the name and hide the exit button
$('#js-content-new-template .advanced-setting-item').click(function () {
    $('#calendarSettings .js-close-calendar-settings').hide();
    let text = $(this).attr('data-text');
    $('.calendarSettings-meetings-templates-advanced-settings .back-button-text').text(text);
    $(".calendarSettings-meetings-templates-advanced-settings .js-subpage-tabs").removeClass("show active").addClass('d-none');
    let sectionIdToShow = $(this).attr('data-target-id');
    $('#' + sectionIdToShow).addClass("show active").removeClass('d-none');
});



function renderCalendarSettings(result) {
    // Display Options
    var data = result.ClassSettings;
    var filters = result.Filters;
    var appsettings = result.AppSettings;

    $('.calendarSettings-general-settings, .calendarSettings-display-options, .calendarSettings-permanent-registration').attr('data-id', data.id);
    if (filters.TypeOfView == 1)
        $('#calendar-type-view').click()
    else if (filters.TypeOfView == 2)
        $('#agenda-type-view').click();

    // AppSettings
    const liAppSettings = $('#js-appDisplayTimeSettingsSelect').closest('li');
    let selectAppSettings = liAppSettings.find('select');
    let numOfDays = appsettings.ViewClassDayNum;

    if (appsettings.ViewClass == 4){
        selectAppSettings[0].selectedIndex = 0;
        selectAppSettings[1].selectedIndex = numOfDays <= 7 ? numOfDays - 1 : (numOfDays > 7 && numOfDays <= 28 ? (numOfDays / 7) + 5 : (numOfDays / 30) + 8);
        $(selectAppSettings[1]).change();
    } else {
        selectAppSettings[0].selectedIndex = 1;
        selectAppSettings[2].selectedIndex = numOfDays - 1;
        $(selectAppSettings[2]).change();
    }

    liAppSettings.find('input[type=time]').first().val(appsettings.SelectTimes);
    $(selectAppSettings[0]).change();
    // -----------

    $('#split-view').val(filters.SplitView).trigger('change');
    // Permanent Registration
    $('.select2--reserve-when-expired').val(data.PermanentRegistration).trigger('change');
    $('.select2--set-registrations-limit').val(data.RegistrationExpiredMembers).trigger('change');
    $('#cancel-permanent-after').val(data.CancelPermanentRegistration);
    // General Settings
    $('.select2--class-default-status').val(data.DefaultStatusClass).trigger('change');
    $('#allow-busy-scheduling').prop('checked', (data.GuideCheck == 0) ? true : false);
    $('#send-class-available-alert').val(data.WatingListPOPUP).trigger('change');
    $('#cancel-minumum').val(data.CancelMinimum).trigger('change');
    $('.btn-save-calendar-settings').addClass('d-none').removeClass('d-block')
}

// General Settings :: Change
$('[data-setting="general"]').on('change keyup', function () {
    $(this).parents('.bsapp-settings-panel')
        .find('.btn-save-calendar-settings')
        .addClass('d-block')
        .removeClass('d-none')
});
function updateCalendarSettings($this) {
    var screen = $this.parents('.bsapp-settings-panel');
    let apiProps = {
        fun: 'UpdateClassSettings',
        CompanyNum: $companyNo,
        id: screen.attr('data-id')
    }

    jsTimeFrom = $("#js-time-from").val();
    jsTimeTo = $("#js-time-to").val();
    if (screen.hasClass('calendarSettings-general-settings')) { // If General Settings
        apiProps.DefaultStatusClass = $('.select2--class-default-status').val();
        apiProps.GuideCheck = $('#allow-busy-scheduling').is(':checked') ? 0 : 1;
        apiProps.WatingListPOPUP = $('#send-class-available-alert').val();
        apiProps.CancelMinimum = $('#cancel-minumum').val();

        const appSettings = $('#js-appDisplayTimeSettingsSelect').closest('li');
        const appSelects = appSettings.find('select');
        let dayNumSelectedIndex = appSelects[1].selectedIndex;
        if (appSelects[0].selectedIndex == 0){
            apiProps.ViewClass = 4;
            apiProps.ViewClassDayNum = dayNumSelectedIndex < 7 ? dayNumSelectedIndex + 1 : (dayNumSelectedIndex < 10 ? (dayNumSelectedIndex - 5) * 7 : ((dayNumSelectedIndex - 8) * 30));
        } else {
            apiProps.ViewClass = 2;
            apiProps.ViewClassDayNum = appSelects[2].selectedIndex + 1;
        }
        apiProps.SelectTimes = appSettings.find('input[type=time]').val();
    }
    if (screen.hasClass('calendarSettings-display-options')) { // If Display Options
        apiProps.TypeOfView = $('#calendar-type-view').is(':checked') ? 1 : 2;
        apiProps.SplitView = $('#split-view').val();
        jsSplitView = apiProps.SplitView;
        jsTypeOfView = apiProps.TypeOfView;
        jsMobileTypeOfView = apiProps.TypeOfView;
        GetCalendarData();
        $this.addClass('d-none').removeClass('d-block');
        if($(window).width() < 767){
            $("#calendarSettings .js-close-calendar-settings").click();
        }
        return true;
    }
    if (screen.hasClass('calendarSettings-permanent-registration')) { // If Permanent Registration
        apiProps.PermanentRegistration = $('.select2--reserve-when-expired').val();
        apiProps.RegistrationExpiredMembers = $('.select2--set-registrations-limit').val();
        apiProps.CancelPermanentRegistration = $('#cancel-permanent-after').val()
    }
    if (apiProps.TypeOfView == 2) {
        $("#calendar-main").addClass("bsapp-js-agenda-view");
    } else {
        $("#calendar-main").removeClass("bsapp-js-agenda-view");
    }


    postApi('calendarSettings', apiProps, 'GetCalendarData');
    $this.addClass('d-none').removeClass('d-block');

}
function getBranchesCalendar() {
    const apiProps = {
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
    // branches.push({'id': 'BA999', 'name': lang('all_branch')});
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
        theme:"bsapp-dropdown",
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

function populateCalendarSelect() {
    const apiProps = {
        CompanyNum: $companyNo,
        fun: 'GetAllCalendars'};
    postApi('calendarSettings', apiProps, 'updateCalendarsSelect')
}
function updateCalendarsSelect(result) {
    var data = result.CalendarList,
        calendars = [];
    $.each(data, function (key) {
        calendars.push({'id': data[key].id, 'name': data[key].Title})
    });
    var calendarOptions = calendars.map(calendar => {
        var calendar = {
            'id': calendar.id,
            'html': `<span data-id="${calendar.id}">${calendar.name}</span>`,
            'text': calendar.name,
            'title': calendar.name};
        return calendar
    });
    $(".bsapp-settings-dialog #appTemplate-calendar").select2({
        data: calendarOptions,
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
        placeholder: 'Select Calendar',
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu')})
}

function populateDevicesSelect() {
    const apiProps = {
        CompanyNum: $companyNo,
        fun: 'GetNumbersByCompanyNum'};
    postApi('calendarSettings', apiProps, 'updateDevicesSelect')
}
function updateDevicesSelect(result) {
    var data = result.NumbersList,
        devices = [];
    $.each(data, function (key) {
        devices.push({'id': data[key].id, 'name': data[key].Name})
    });
    var devicesOptions = devices.map(device => {
        var device = {
            'id': device.id,
            'html': `<span data-id="${device.id}">${device.name}</span>`,
            'text': device.name,
            'title': device.name};
        return device
    });
    $(".bsapp-settings-dialog #appTemplate-devices").select2({
        data: devicesOptions,
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
        placeholder: 'Select Devices',
        dropdownParent: $('.bsapp-settings-dialog > .dropdown-menu')})
}



$('[data-templates]').click(function () {
    var target = $(this).attr('data-templates');
    $('.calendarSettings-meetings-templates-new > a').attr('data-back-target', target);
});

$(".js-close-calendar-settings").click(function () {
    $('.tagInfo').addClass('d-none')
});

// Appointments Templates New :: Tabs Change
$('.bsapp-tabs-navigation a').click(function (e) {
    e.preventDefault();
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    var target = $(this).attr('href'),
        tabs = $(this).parents('.bsapp-settings-panel').find('.tab-pane');
    tabs.removeClass('active show');
    $(target).addClass('active');
    setTimeout(function () {
        $(target).addClass('show');
    }, 100)
});
// Appointments Templates New :: Summernote Initialization
$(document).ready(function () {

    $(".js-select2").select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown bsapp-no-arrow"
    });

    if ($(window).width() < 767 && $('#calendar-main').length && $('#calendarMainAboutScroll').length) {
        let times,
            cookNameDay = 'boostapp_mob_hand_anim_day',
            cookName = 'boostapp_mob_hand_anim',
            calendarMainAboutScroll = $('#calendarMainAboutScroll');
        if ($.cookie(cookName) != '0') {
            times = $.cookie(cookName) != undefined ? '0' : '1';
            if ($.cookie(cookNameDay) == null) {
                calendarMainAboutScroll.modal('show');
                $.cookie(cookNameDay, '1', { expires: 1, path: '/' });
            }
            calendarMainAboutScroll.on('shown.bs.modal', function () {
                $.cookie(cookName, times, {expires: 365, path: '/'});
                if (times == '0') {
                    $.removeCookie(cookNameDay, { path: '/' });
                }
            });
        }
    }

    $('#js-appDisplayTimeSettingsSelect').change();
});
// Appointments Templates Set Availability :: Toggle Day Off/On
$('.bsapp-settings-panel').on('click', 'label .toggle-manage', function () {
    $(this).parent().toggleClass('text-gray-700 text-gray-500');
    $(this).parent().siblings('.bsapp-availability-times').children('.form-toggle').children('.toggle-content').removeClass('d-flex').addClass('d-none')
});


// Appointments Templates Set Availability :: Add Colon to times automatically
$('.bsapp-settings-panel').on('change keyup', '.bsapp-startTime, .bsapp-endTime', timeKeyHandler);
function timeKeyHandler(e) {
    var element = this,
        key = e.keyCode || e.which;
    if (element.value > 23)
        element.value = 23;
    insertTimeColon(element, key)
}
function insertTimeColon(element, key) {
    if (element.value.trim().length == 2 && key !== 8)
        element.value = element.value + ':'
}





// Device Selection Management :: Get Devices
function getDevices() {
    const apiProps = {
        fun: 'GetNumbersByCompanyNum',
        CompanyNum: $companyNo};
    postApi('calendarSettings', apiProps, 'renderDevices')
}
function renderDevices(data) {
    var result = data.NumbersList;
    $('.devices-list .item-loading').hide();
    $('.devices-list li:not(.item-loading)').remove();
    if (result.length) {
        $.each(result, function (key) {
            var hidden = '';
            if (result[key].Status == 1)
                hidden = 'disabled';
            $('.devices-list').append(devicesRow(hidden, result[key].id, result[key].Name))
        })
        //updateDevicesSelect() for use on other panels
    } else
        $('.devices-list').append(`<li class="mb-10 animated fadeInUp"><div class="js-no-devices form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang("no_devices_found")}</div></li>`);
}
function devicesRow(hidden, id, name) {
    var hide_btn = (hidden == '') ? dropdown_hide : dropdown_unhide,
        toggle = (hidden == '') ? 'checked' : '',
        markup = `<li class="mb-10 bsapp-devices-row ${hidden}" data-id="${id}"> <div class="form-static d-flex align-items-center border rounded py-7 px-0"> <div class="devices-category-name d-flex align-items-center col-9 font-weight-bold text-start pis-12 pie-0 bsapp-fs-16 bsapp-lh-16"> ${name} </div><div class="col-3 d-flex justify-content-between text-end text-gray-700 pie-10"> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-devices hide-toggle custom-control-input" id="device-management-${id}" ${toggle}> <label class="custom-control-label" for="device-management-${id}" role="button"></label> </div><div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-devices d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-edit fa-fw mx-5"></i> `+ lang('edit') +` </a> </li><li class="mb-6"> <a role="button" class="hide-devices hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a> </li><li> <a role="button" class="delete-devices-group d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-minus-circle fa-fw mx-5"></i> `+ lang('a_remove_single') +` </a> </li></ul> </div></div></div></div></li>`;
    return markup
}

// Device Selection Management :: Add New Devices Group
$('.new-device-selection').click(function () {
    $('.device-category-list .item-loading').hide();
    $('.device-category-list li:not(.item-loading)').remove();
    $('#deviceSelection-name').val('');
    var form = $('.calendarSettings-device-selection-management--new');
    form.removeAttr('data-id');
    form.find('h3 span').text('create new')
});

// Device Selection Management :: Delete Devices Group
$('.bsapp-settings-panel').on('click', '.delete-devices-group', function () {
    var category = $(this).parents('.bsapp-devices-row'),
        category_id = category.attr('data-id');
    const apiProps = {
        fun: 'DeleteNumbers',
        CompanyNum: $companyNo,
        id: category_id};
    category.remove();
    const devicesList = $('.calendarSettings-device-selection-management .devices-list');
    if(devicesList.find('.bsapp-devices-row').length === 0 && devicesList.find('.js-no-devices').length === 0) {
        devicesList.append(`<li class="mb-10 animated fadeInUp"><div class="js-no-devices form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">${lang("no_devices_found")}</div></li>`);
    }


    postApi('calendarSettings', apiProps)
});

// Device Selection Management :: Hide Devices Group
$('.bsapp-settings-panel').on('click', '.hide-devices', function () {
    var category = $(this).parents('.bsapp-devices-row'),
        category_id = category.attr('data-id'),
        status = (category.hasClass('disabled')) ? 1 : 0;
    const apiProps = {
        fun: 'UpdateNumbers',
        CompanyNum: $companyNo,
        id: category_id,
        Status: status};
    postApi('calendarSettings', apiProps)
});

// Device Selection Management :: Edit Devices Group
$('.bsapp-settings-panel').on('click', '.edit-devices', function () {
    var category = $(this).parents('.bsapp-devices-row'),
        category_id = category.attr('data-id'),
        category_name = category.find('.devices-category-name').text(),
        form = $('.calendarSettings-device-selection-management--new');
    const apiProps = {
        fun: 'GetNumberSubsByCompanyNum',
        CompanyNum: $companyNo,
        NumbersId: category_id}
    postApi('calendarSettings', apiProps, 'renderCategoryDevices');
    $('#deviceSelection-name').val(category_name);
    form.find('h3 span').text('edit selection');
    form.attr('data-id', category_id);
    $('.device-category-list .item-loading').show();
    $('.device-category-list li:not(.item-loading)').remove();
    switchSettingsPanel($(this), 'calendarSettings-device-selection-management--new')
});
function renderCategoryDevices(data) {
    var result = data.NumbersSubList;
    $('.device-category-list .item-loading').hide();
    $('.device-category-list li:not(.item-loading)').remove();
    if (result.length)
        $.each(result, function (key) {
            $('.device-category-list').append(devicesCategoryRow(result[key].Name, result[key].id))
        })
}
function devicesCategoryRow(name, id) {
    var cat_id = (id != undefined) ? `data-id="${id}"` : '',
        markup = `<li class="bsapp-device-category-row form-toggle mb-10" ${cat_id}> <div class="form-static d-flex align-items-center border rounded py-7 px-0"> <div class="d-flex align-items-center col-11 font-weight-bold text-start pis-12 pie-0"> <span class="item-label">${name}</span> </div><div class="col-1 text-end text-gray-500 pie-10"> <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-device-selection d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-edit fa-fw mx-5"></i> Edit </a> </li><li> <a role="button" class="delete-device-sub d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"> <i class="fal fa-minus-circle fa-fw mx-5"></i> Delete </a> </li></ul> </div></div></div></div></li>`;
    return markup
}

// Device Selection Management :: Delete Numbers Sub
$("body").on("click", ".delete-device-sub", function () {
    var category = $(this).closest('.bsapp-device-category-row'),
        category_id = category.attr('data-id');
    const apiProps = {
        fun: 'DeleteNumbersSub',
        id: category_id
    };
    category.remove();
    postApi('calendarSettings', apiProps)
});

// Device Selection Management :: Inline Form Edit
$("body").on("click", ".edit-device-selection", function () {
    var old_val = '',
        form = $(this).parents('.form-toggle');
    if (form.children('.form-static').hasClass('d-none')) {
        form.children('.form-static').toggleClass('d-flex d-none');
        form.children('.form-inline').remove();
    } else {
        if (form.find('.item-label').length)
            old_val = form.find('.item-label').text();
        form.children('.form-static').toggleClass('d-flex d-none');
        form.append(`<form class="form-inline d-flex"> <label class="sr-only" for="inlineFormInputGroup">Enter Name</label> <div class="input-group d-flex flex-row w-100 border border-primary rounded"> <input autocomplete="off" type="text" class="w-75 flex-grow-1 outline-none border-0 shadow-none py-5 pie-10" id="inlineFormInputGroup" placeholder="Enter Name" value="${old_val}"> <div class="w-25 input-group-apend border-0 rounded-right"> <div class="save-device-selection btn text-primary py-3 px-7 bsapp-fs-24"> <i class="fal fa-check-circle"></i> </div><div class="edit-device-selection btn text-gray-700 py-3 px-7 bsapp-fs-24"> <i class="fal fa-minus-circle"></i> </div></div></div></form>`)
    }
});

// Device Selection Management :: Edit / Add Row
$("body").on("click", ".save-device-selection", function () { // tbc
    $.ajax({
        url: '',
        method: 'POST',
        data: {

        }
    });
    var form = $(this).parents('.form-toggle'),
        new_val = form.find('input').val();
    if (form.find('.item-label').length) {
        form.find('.item-label').text(new_val)
    } else {
        if (new_val !== '')
            $('.device-category-list').append(devicesCategoryRow(new_val))
    }
    form.children('.form-static').toggleClass('d-flex d-none');
    form.children('.form-inline').remove()
});

// Device Selection Management :: Delete Device Selection - Update to reflect in DB
$("body").on("click", ".delete-device-selection", function () {
    $(this).parents('.form-toggle').remove()
});

// Device Selection Management :: Save Device Group
$('.save-device-group').click(function () {
    var fields = $(this).parents(".bsapp-settings-panel").find("[required]");
    if (validateSettingsFields(fields)) {
        $(this).children('span').hide();
        $(this).children('.spinner-border').removeClass('d-none').addClass('d-block')
        updateDeviceGroup()
    }
});
function updateDeviceGroup() {
    var form = $('.calendarSettings-device-selection-management--new'),
        name = form.find('#deviceSelection-name').val();
    let edited = (form.attr('data-id') != undefined) ? true : false;
    let apiProps = {
        fun: (edited) ? "UpdateNumbers" : "InsertNumbersNewData",
        CompanyNum: $companyNo,
        Name: name
    }
    if (edited) {
        apiProps.id = form.attr('data-id');
        postApi('calendarSettings', apiProps);
        updateDeviceCategories({id: form.attr('data-id')});
        $('.devices-list').find('[data-id="' + form.attr('data-id') + '"]').html(devicesRow('', form.attr('data-id'), name));
    } else {
        apiProps.recordListingId = 0;
        apiProps.Unique = 0;
        postApi('calendarSettings', apiProps, 'updateDeviceCategories');
        $('.devices-list').append(devicesRow('', '#', name));
    }
    const devicesList = $('.calendarSettings-device-selection-management .devices-list');
    if(devicesList.find('.bsapp-devices-row').length) {
        devicesList.find('.js-no-devices').remove(); // remove no found
    }


}
function updateDeviceCategories(data) {
    $('.devices-list').find('[data-id="#"]').attr('data-id', data.id);
    $.each($('.device-category-list .bsapp-device-category-row'), function () {
        var edited_cat = ($(this).attr('data-id') != undefined) ? true : false,
            apiProps = {
                fun: (edited_cat) ? "UpdateNumbersSub" : "InsertNumbersSubNewData",
                CompanyNum: $companyNo,
                NumbersId: data.id,
                Name: $(this).find('.item-label').text()}
        if (!edited_cat) {
            apiProps.recordListingId = 0;
            apiProps.Status = 0;
        } else {
            apiProps.id = $(this).attr('data-id');
        }
        postApi('calendarSettings', apiProps);
    });
    switchSettingsPanel($('.save-device-group'), 'calendarSettings-device-selection-management');
    $('.save-device-group span').show();
    $('.save-device-group').find('.spinner-border').addClass('d-none').removeClass('d-block');
    populateDevicesSelect();
}



// Display Options :: Label Toggle
$(".label-boxes label").click(function () {
    $('.label-boxes label').removeClass('active');
    $(this).addClass('active')
});



// Permanent Registration :: Expired Select
$('.select2--reserve-when-expired').on('change', function (e) {
    var selected = $(this).find(':selected'),
        target = selected.data('target');
    if (target == "show-registrations-limit") {
        if ($('.select2--set-registrations-limit:selected').data('target') == 'show-cancel-permanent') {
            $(this).parents('li').siblings().addClass('d-block').removeClass('d-none');
            if ($('#cancel-permanent-after').val() == 0)
                $('#cancel-permanent-after').val(1);
        } else {
            $(this).parents('li').next().addClass('d-block').removeClass('d-none');
            $('.select2--set-registrations-limit').trigger('change')
        }
    } else
        $(this).parents('li').siblings().addClass('d-none').removeClass('d-block')
});

// Permanent Registration :: Limit
$('.select2--set-registrations-limit').on('change', function (e) {
    var selected = $(this).find(':selected'),
        target = selected.data('target');
    if (target == "show-cancel-permanent")
        $(this).parents('li').next().addClass('d-block').removeClass('d-none')
    else
        $(this).parents('li').next().addClass('d-none').removeClass('d-block')
});



// Calendars :: Get Calendars
function getCalendars() {
    if (tagsTranslations == []) {
        fieldEvents.loadTagsTranslation();
    }
    $('.calendars-list li:not(.item-loading)').remove();
    $('.calendars-list .item-loading').show();
    const apiProps = {
        CompanyNum: $companyNo,
        fun: 'GetAllCalendars'};
    postApi('calendarSettings', apiProps, 'renderCalendars')
}

// Calendars :: Render Calendar Items
function renderCalendars(data) {
    var result = data.CalendarList;
    $('.calendars-list .item-loading').hide();
    $('.calendars-list li:not(.item-loading)').remove();
    if (result.length) {
        $.each(result, function (key) {
            var hidden = '';
            if (result[key].Status == 1) {
                hidden = 'disabled-style';
            }
            let price = 0;
            let tagId = 41;

            if (typeof(result[key].Price) !== 'undefined') {
                price = result[key].Price
            }

            if (typeof(result[key].tagId) !== 'undefined') {
                tagId = result[key].tagId
            }

            $('.calendars-list').append(calendarRow(hidden, result[key].id, result[key].Title, result[key].Brands, result[key].outdoor, result[key].SpaceType, price, tagId))
        })
        //updateCalendarsSelect() for use on other panels
    } else
        $('.calendars-list').append('<li class="mb-10 animated fadeInUp"><div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-8 px-10 bsapp-fs-14">No Calendars Found</div></li>')
}

function calendarRow(hidden, id, name, branch, outdoor, spaceType, price, tagId) {
    var hide_btn = (hidden == '') ? dropdown_hide : dropdown_unhide,
        toggle = (hidden == '') ? 'checked' : '',
        spaceTypeText = (spaceType==0) ? lang('lessons_meetings') : lang('space_entrances');
    if(false) { //todo: remove false when space ready
        markup = `<li class="mb-10 bsapp-calendar-row ${hidden}" data-id="${id}"> <div class="form-static d-flex align-items-center border rounded py-7 px-0"> <div class="d-flex align-items-center col-7 text-start pis-12 pie-0"> <span class="calendar-name font-weight-bold bsapp-fs-16 bsapp-lh-16">${name}&nbsp</span><span class="bsapp-16 bsapp-lh-16">(${spaceTypeText})</span> </div><div class="col-4 d-flex text-start text-gray-700"> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-calendar hide-toggle custom-control-input" id="calendar-id-${id}" ${toggle}> <label class="custom-control-label" for="calendar-id-${id}" role="button"></label> </div><span class="calendar-branch" style="display: none;">${branch}</span> <span class="calendar-outdoor" style="display: none;">${outdoor}</span><span class="calendar-spaceType" style="display: none;">${spaceType}</span><span class="calendar-price" style="display: none;">${price}</span><span class="calendar-tagId" style="display: none;">${tagId}</span></div><div class="col-1 text-end pie-10"> <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" style="pointer-events: auto;" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-calendar d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"><i class="fal fa-edit fa-fw mx-5"></i> ` + lang('edit') + ` </a> </li><li class="mb-6"> <a role="button" class="hide-calendar hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a> </li></ul> </div></div></div></div></li>`;
    } else {
        markup = `<li class="mb-10 bsapp-calendar-row ${hidden}" data-id="${id}"> <div class="form-static d-flex align-items-center border rounded py-7 px-0"> <div class="d-flex align-items-center col-7 text-start pis-12 pie-0"> <span class="calendar-name font-weight-bold bsapp-fs-16 bsapp-lh-16">${name}&nbsp</span></div><div class="col-4 d-flex text-start text-gray-700"> <div class="custom-control custom-switch"> <input type="checkbox" class="hide-calendar hide-toggle custom-control-input" id="calendar-id-${id}" ${toggle}> <label class="custom-control-label" for="calendar-id-${id}" role="button"></label> </div><span class="calendar-branch" style="display: none;">${branch}</span> <span class="calendar-outdoor" style="display: none;">${outdoor}</span><span class="calendar-spaceType" style="display: none;">${spaceType}</span><span class="calendar-price" style="display: none;">${price}</span><span class="calendar-tagId" style="display: none;">${tagId}</span></div><div class="col-1 text-end pie-10"> <div class="js-dropdown dropdown position-relative text-gray-500 bsapp-fs-21" style="pointer-events: auto;" role="button"> <i class="dropdown-toggle fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i> <div class="dropdown-menu dropdown-menu-right text-start font-weight-normal border-0 shadow"> <ul class="list-unstyled m-0 p-0"> <li class="mb-6"> <a role="button" class="edit-calendar d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16"><i class="fal fa-edit fa-fw mx-5"></i> `+ lang('edit')+` </a> </li><li class="mb-6"> <a role="button" class="hide-calendar hide-toggle d-flex align-items-center w-100 text-gray-700 py-0 px-10 bsapp-fs-16">${hide_btn}</a> </li></ul> </div></div></div></div></li>`;
    }
    return markup
}

var activeCal_name = '',
    activeCal_branch = '',
    activeCal_outdoor = '',
    activeCal_hidden = '',
    activeCal_spaceType = '',
    activeCal_price = '',
    activeCal_tagId = '';


// Calendars :: Edit Calendar
$('.bsapp-settings-panel').on('click', '.edit-calendar', function () {
    var cal = $(this).parents('.bsapp-calendar-row'),
        cal_id = cal.data('id'),
        cal_name = cal.find('.calendar-name').text().trim(),
        cal_branch = cal.find('.calendar-branch').text(),
        cal_outdoor = cal.find('.calendar-outdoor').text(),
        cal_spaceType = cal.find('.calendar-spaceType').text(),
        cal_price = cal.find('.calendar-price').text(),
        cal_tagId = cal.find('.calendar-tagId').text();
    if (cal.hasClass('disabled'))
        activeCal_hidden = 'disabled';
    updateCalendarForm(cal_id, cal_name, cal_branch, cal_outdoor, cal_spaceType, cal_price, cal_tagId);
    switchSettingsPanel($(this), 'calendarSettings-calendars--new')
});

function clearCalendarForm() {
    $('.calendarSettings-calendars--new').attr('data-id', '');
    $('.bsapp-save-calendar').text('Save');
    $('.calendarSettings-calendars--new input').val('');
    $('.calendarSettings-calendars--new select').val('').trigger('change');
    const branchSelect = $('.select2--branches');
    if (!calendar_data.branches.length) {
        branchSelect.data('noBranch', '1').addClass('d-none').attr('required', false)
            .closest('.branch-space-section').addClass('d-none');
        branchSelect.closest('.d-flex').find('i').addClass('d-none');
    } else {
        branchSelect.trigger('change').removeClass('d-none').attr('required', true)
            .closest('.branch-space-section').removeClass('d-none');
        branchSelect.closest('.d-flex').find('i').removeClass('d-none');
    }


}

function updateCalendarForm(id, name, branch, outdoor, spaceType, price, tagId) {
    var screen = $('.calendarSettings-calendars--new');
    screen.attr('data-id', id);
    $('#newCalendar-name').val(name);
    const branchSelect = $('.select2--branches');
    if (!calendar_data.branches.length) {
        branchSelect.data('noBranch', '1').attr('required', false ).closest('.branch-space-section').addClass('d-none');
        branchSelect.closest('.d-flex').find('i').addClass('d-none');
    } else {
        branchSelect.val(branch).trigger('change').removeClass('d-none').attr('required', true)
            .closest('.branch-space-section').removeClass('d-none');
        branchSelect.closest('.d-flex').find('i').removeClass('d-none');
    }
    const outdoorSelect = $('.select2--outdoor');
    outdoorSelect.val(outdoor).trigger('change').removeClass('d-none').attr('required', true)
    outdoorSelect.closest('.d-flex').find('i').removeClass('d-none');
    const spaceTypeSelect = $('.select2--spaceType');
    spaceTypeSelect.val(spaceType).trigger('change').removeClass('d-none').attr('required', true)
    spaceTypeSelect.closest('.d-flex').find('i').removeClass('d-none');

    if (price == 'null') {
        price = 0;
    }
    $('[name="entrance-price"]').val(price);
    let tagName = tagsTranslations.find((o) => {
        return o["id"] === tagId && o["lang"] === lag
    });
    if (typeof tagName === 'undefined') {
        tagName = tagsTranslations.find((o) => {
            return o["id"] === tagId
        });
        if (typeof tagName === 'undefined') {
            fieldEvents.setTag(41, tagsTranslations.find((o) => {
                return o["id"] === '41'
            }).text);
        } else {
            fieldEvents.setTag(tagId, tagName.text);
        }
    } else {
        fieldEvents.setTag(tagId, tagName.text);
    }

}

// Calendars :: Save Calendar
$('.bsapp-settings-panel').on('click', '.save-calendar', function () {
    const fields = $(this).parents(".bsapp-settings-panel").find("[required]");
    if (validateSettingsFields(fields)) {
        $(this).html('<i class="fal fa-spinner-third fast-spin fa-lg"></i>');
        const screen = $('.calendarSettings-calendars--new');
        activeCal_name = $('#newCalendar-name').val();
        activeCal_branch = $('.select2--branches').val();
        activeCal_outdoor = $('.select2--outdoor').val();
        activeCal_spaceType = $('.select2--spaceType').val();
        activeCal_price = $('[name="entrance-price"]').val();
        activeCal_tagId = $(`[name="tag"]`).attr("data-tagId");

        if ($('.select2--branches').hasClass('d-none') || $('.select2--branches option').length == 0) //set branches to 0 if no branches
            activeCal_branch = 0;

        let apiProps = {
            Title: activeCal_name,
            Brands: activeCal_branch,
            SpaceType: activeCal_spaceType,
            Outdoor: activeCal_outdoor,
            Price: activeCal_price,
            tagId: activeCal_tagId,
        };

        if (screen.attr('data-id') != '') {
            apiProps.fun = "EditCalendar";
            apiProps.id = screen.attr('data-id');
        } else {
            apiProps.fun = "InsertNewCalendar";
            apiProps.CompanyNum = $companyNo;
        }
        postApi('calendarSettings', apiProps, 'updateCalendars', true)
    }
});
function updateCalendars(data) {
    $('.calendarSettings-calendars--new .save-calendar').html(lang('save'));
    if (!globalCalendarSettings.errorChecking(data)) {
        return;
    }
    var id = (data.InsertedId) ? data.InsertedId : $('.calendarSettings-calendars--new').attr('data-id'),
        markup = calendarRow(activeCal_hidden, id, activeCal_name, activeCal_branch, activeCal_outdoor, activeCal_spaceType, activeCal_price, activeCal_tagId);
    if (data.InsertedId) {
        $('.calendars-list').append(markup);
        locations += ',' + data.InsertedId;
    } else
        $('.calendars-list').find('[data-id="' + id + '"]').html(markup);
    activeCal_name = '';
    activeCal_branch = '';
    activeCal_outdoor = '';
    activeCal_hidden = '';
    activeCal_spaceType= '';
    activeCal_price= '';
    activeCal_tagId = '';
    $('.calendarSettings-calendars--new').attr('data-id', '');
    switchSettingsPanel($('.save-calendar'), 'calendarSettings-calendars-and-classes_calendars');
    populateCalendarSelect()
    GetCalendarData();
}

// Calendars :: New Calendar
$('.bsapp-settings-panel').on('click', '.new-calendar', function () {
    var screen = $('.calendarSettings-calendars--new');
    screen.attr('data-id', '');
    clearCalendarForm();
    switchSettingsPanel($(this), 'calendarSettings-calendars--new');
    $(".select2--outdoor").val('0').trigger("change");
    $(".select2--spaceType").val('0').trigger("change");
    $('[name="entrance-price"]').val('0');




});

// Calendars :: Hide Toggle Calendar
$('.bsapp-settings-panel').on('click', '.hide-calendar', function () {
    cal = $(this).parents('.bsapp-calendar-row');
    cal_id = cal.data('id');
    let hidden = $(this).closest('.bsapp-calendar-row').find('input').is(':checked') ? 0 : 1;
    const apiProps = {
        fun: "ToggleHideCalendar",
        id: cal_id,
        display: hidden};
    postApi('calendarSettings', apiProps, 'toggleCalendar')

});

function toggleCalendar(data) {
    let calsArr = locations.split(',');
    if (calsArr.includes(data.id)) {
        calsArr = calsArr.filter(item => item != data.id);
        locations = calsArr.join();
    } else
        locations += ',' + data.id;


    // GetCalendarData();
}








//todo move to calendarsAndClasses
// Classes :: Edit Class
var cal_class,
    cal_class_id
updatedClass = {};
//todo move to calendarsAndClasses
$('.bsapp-settings-panel').on('click', '.edit-class', function () {
    clearClassForm();
    cal_class = $(this).parents('.bsapp-class-row');
    cal_class_id = cal_class.data('id');
    const apiProps = {
        fun: "getSingleClassTypeWithMemberships",
        id: cal_class_id};
    postApi('calendarSettings', apiProps, 'editClass')
});
//todo move to calendarsAndClasses
function editClass(result) {
    var data = result.ClassType;
    $('.save-class').addClass('save-edited-class')
        .attr('data-id', cal_class_id);
    $('#newClass--name').val(data.Type);
    $('.select2--colors').val(data.Color).trigger('change');
    let durationData = (data.duration != false && $(`#newClass--duration option[value=${data.duration}]`).length > 0) ? data.duration : "";
    $(`#newClass--duration`).val(durationData).trigger('change');
    $('.newClass--memberships input').prop('checked', false);
    if (data.memberships != null)
        $.each(data.memberships, function (key) {
            $(calendarsAndClasses.addElem)
                .find(`.newClass--memberships input[value="${data.memberships[key].ItemId}"]`).prop('checked', true);
        });
    switchSettingsPanel($('.bsapp-save-class'), 'calendarSettings-classes--new')
}
//todo move to calendarsAndClasses
// Classes :: Save Class
$('.bsapp-settings-panel').on('click', '.save-class', function () {
    if ( $('#newClass--name').val() == '') {
        $('#newClass--name').removeClass('border-0').css('border-color', 'red');
        setTimeout(function(){ $('#newClass--name').addClass('border-0'); }, 3000);
        return;
    }

    if (!$('#newClass--duration').val() || !$.isNumeric($('#newClass--duration').val()) ){
        $('#newClass--duration').addClass('border-danger')
            .siblings('.input-group-append, .select2-container')
            .addClass('border border-danger');
        setTimeout(function(){ $('#newClass--duration')
            .removeClass('border-danger')
            .siblings('.input-group-append, .select2-container')
            .removeClass('border border-danger'); }, 3000);
        return;
    }
    var edited = false;
    if ($(this).hasClass('save-edited-class'))
        edited = true;
    $(this).removeClass('save-edited-class');
    let apiProps = {
        Type: $('#newClass--name').val(),
        Color: $('.select2--colors').val(),
        duration: $('#newClass--duration').val()}
    if (edited) {
        apiProps.fun = 'EditClassType';
        apiProps.id = $(this).attr('data-id');
    } else {
        apiProps.fun = 'InsertSingleClassType';
        apiProps.CompanyNum = $companyNo;
    }
    var selected_memberships = $('.newClass--memberships').find('input:checked'),
        memberships = [];
    selected_memberships.each(function () {
        memberships.push($(this).val())
    });
    apiProps.memberships = memberships;
    updatedClass = apiProps;
    postApi('calendarSettings', apiProps, 'updateClasses');
    switchSettingsPanel($(this), 'calendarSettings-calendars-and-classes_classes')
});
function updateClasses(data) {
    var edited = false;
    if (!data.insertedId)
        edited = true;
    else {
        updatedClass.id = data.insertedId;
        title += ',' + data.insertedId;
    }
    data = updatedClass;
    var updatedMarkup = calendarsAndClasses.classesRow(data.id, globalCalendarSettings.findColorHexById(data.Color), data.Type, data.duration);
    if (edited)
        $('.classes-list').find('.bsapp-class-row[data-id=' + data.id + ']').html(updatedMarkup)
    else
        $('.classes-list').append(updatedMarkup);
    globalCalendarSettings.getClassType();
    GetCalendarData();
}


//todo move to calendarsAndClasses
// Classes :: Clear Class Form
function clearClassForm() {
    cal_class = '',
        cal_class_id = '';
    updatedClass = {};
    $('#newClass--name, #newClass--duration').val('').trigger('change');
    $('.select2--colors').val(1).trigger('change');
    $('.newClass--memberships input').prop('checked', false)
}
//todo move to calendarsAndClasses
// Classes :: New Class
$('.bsapp-settings-panel').on('click', '.new-class', function () {
    clearClassForm()
});
//todo move to calendarsAndClasses
// Classes :: Delete Class Initiate
var class_type_id;
$('.bsapp-settings-panel').on('click', '.delete-class', function () {
    $id = $(this).parents('.bsapp-class-row').data('id');
    const apiProps = {
        fun: "GetFutureEventCount",
        ClassTypeId: parseInt($id)}
    class_type_id = $id;
    postApi('calendarSettings', apiProps, 'checkEventCount')
});
//todo move to calendarsAndClasses
// Classes :: Check Event Count on Delete
function checkEventCount(data) {
    remove_screen = $('.calendarSettings-remove-class');
    if (data.Message > 0) {
        remove_screen.find('.item-count').text(data.Message);
        remove_screen.find('.bsapp-delete-or-move-class').attr('data-id', class_type_id);
        switchSettingsPanel($('.new-class'), 'calendarSettings-remove-class')
    } else {
        deleteMoveClassType(class_type_id);
        $('.bsapp-class-row[data-id=' + class_type_id + ']').remove()
    }
}
//todo move to calendarsAndClasses
// Classes :: Delete / Move Class Initiate
$('.bsapp-settings-panel').on('click', '.bsapp-delete-or-move-class', function () {
    var $id = $(this).attr('data-id'),
        $otherId = undefined;
    if ($('#move-class-type').is(':checked'))
        $otherId = $('#move-to-class').val();
    deleteMoveClassType($id, $otherId)
});
//todo move to calendarsAndClasses
// Classes :: Delete or Move Class Type
function deleteMoveClassType($id, $otherId) {
    let apiProps = {
        fun: "DeleteMoveClassType",
        id: $id};
    if ($otherId != undefined)
        apiProps.otherId = $otherId;
    postApi('calendarSettings', apiProps);
    $('.classes-list li[data-id="' + $id + '"]').remove();
    globalCalendarSettings.getClassType();
    switchSettingsPanel($('.bsapp-delete-or-move-class'), 'calendarSettings-calendars-and-classes_classes')
}


