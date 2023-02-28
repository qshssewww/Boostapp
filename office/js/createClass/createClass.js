const MeetingPopup = {
    parentElem: $('#js-meeting-popup'),
    ajaxOptions: {
        url: 'ajax/SaveStudioDate.php',
        method: 'POST',
    },
    userSearch: {
        init: function () {
            $("#js-user-search-meeting")
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
                    language: $("html").attr("dir") === 'rtl' ? "he" : "en",
                    allowClear: true,
                    theme: "bsapp-dropdown bsapp-no-arrow",
                    minimumInputLength: 2,
                    ajax: {
                        url: '/office/action/getClientsJson.php',
                        data: function (params) {
                            return {
                                query: params.term,
                                type: 'public'
                            }
                            // Query parameters will be ?search=[term]&type=public
                        },
                        processResults: function (data) {
                            let items = $.map($.parseJSON(data).results, user => ({
                                    name: user.name,
                                    id: user.id,
                                    img: user.img,
                                    phone: user.phone,
                                    status: user.status,
                                    gender: user.gender,
                                })
                            )


                            return {
                                results: items
                            };
                        },
                    },
                    templateResult: formatState,
                    templateSelection: function (item) {
                        if (item.id === '') {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>'
                                + lang('search_by_name_or_phone')
                                + '</div><div> </div> </div>');
                        } else if (item.isNew) {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>'
                                + item.text
                                + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">'
                                + lang('create_new_cal') + '</div> </div> </div>');
                        } else {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div><img src="'
                                + item.img
                                + '" class="w-20p h-20p rounded-circle mie-5"  alt=""/><span> '
                                + item.name
                                + ' </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
                        }
                        return $item;
                    }

                })
                .on("select2:selecting", function (e) {
                    $(MeetingPopup.parentElem).find(".js-client-is-new").hide();
                    $(MeetingPopup.parentElem).find(".js-client-phone-valid").hide();
                    MeetingPopup.parentElem.find('[name="IsNew"]').val(0);
                    MeetingPopup.parentElem.find('#js-new-client-button').addClass('d-none');


                    if (!isNaN(e.params.args.data.id))
                        $(MeetingPopup.parentElem).find('input[name="ClientId"]').val(e.params.args.data.id)
                    $(MeetingPopup.parentElem).find('.js-user-data-details input').addClass('custom-required')


                    let selected = e.params.args.data;

                    if (e.params.args.data.isNew) {
                        $('.js-client-is-new').show();
                        MeetingPopup.parentElem.find('[name="IsNew"]').val(1).addClass('changed');
                        if (/^\d+$/.test(selected.text)) { //Check if only digits
                            $(".js-user-data-details #js-user-phone").val(selected.text);
                        } else {
                            $(".js-user-data-details #js-user-name").val(selected.text);
                        }
                        MeetingPopup.parentElem.find('#js-new-client-button').removeClass('d-none');

                    } else {
                        $(".js-user-data-details #js-user-name").val(selected.name).attr("readonly", true)
                        $(".js-user-data-details #js-user-phone").val(selected.phone).attr("readonly", true)

                        fieldEvents.meetingActions.checkCanUseMembershipAndFindMembership(selected.id)
                        fieldEvents.meetingActions.submitClient(this)
                    }

                    MeetingPopup.userSearch.hideSearchField();
                    $(this)
                        .parents(".pointForm")
                        .find(".show-field")
                        .addClass("DisplayOn");
                });
        },
        showSearchField: function () {
            $(".js-user-data-details .form-control").val("").attr("readonly", false)
            $(".js-user-search").val(null).trigger("change")
            $(".js-user-data-details .form-group").hideFlex()
            $("#js-user-search-meeting").parents(".form-group").showFlex()
            $(".pointForm")
                .find(".show-field")
                .removeClass("DisplayOn");

            $(MeetingPopup.parentElem).find('.js-user-data-details input').removeClass('custom-required')
            MeetingPopup.parentElem.find('[name="IsNew"]').val(0)
            fieldEvents.meetingActions.checkCanUseMembershipAndFindMembership()
            MeetingPopup.parentElem.find('#js-new-client-button').addClass('d-none');
        },
        hideSearchField: function () {
            $(".js-user-data-details .form-group").showFlex()
            $("#js-user-search-meeting").parents(".form-group").hideFlex()
        },
        triggerFocus: function () {
            setTimeout(function () {
                $('#js-user-search-meeting').select2('open')
            }, 1000);
        },
    },
    init: function () {
        this.initSelect2();
        this.initSelect2Multi();
        this.initModalTypeSelect2();
        this.initClassSelect2();
        this.initClassTypeSelect2();
        this.initClassColorSelect2();
        this.initSelect2Templates();
        this.initSelect2Schedule();
        this.userSearch.init();
        this.setTagsCategories();

        $(MeetingPopup.parentElem).find(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            dayNamesMin: [
                lang('sunday_short'),
                lang('monday_short'),
                lang('tuesday_short'),
                lang('wednesday_short'),
                lang('thursday_short'),
                lang('friday_short'),
                lang('saturday_short'),
            ],
            monthNames: [
                lang('january'),
                lang('february'),
                lang('march'),
                lang('april'),
                lang('may'),
                lang('june'),
                lang('july'),
                lang('august'),
                lang('september'),
                lang('october'),
                lang('november'),
                lang('december'),
            ],
        });


        fieldEvents.classActions.init()
        fieldEvents.meetingActions.init()

        $('a[data-toggle="pill"]').on('shown.bs.tab', function (event) {
            console.log(event.target) // newly activated tab
            console.log(event.relatedTarget) // previous active tab
        });

        const classId = (new URL(window.location.href))
            .searchParams.get("classId")
        if (classId)
            populateFields.class.init(classId)

        this.ready()
    },
    ready: function () {
        this.initImgPicker()
        $('#RemarksNew').summernote({
            // dialogsInBody: true,
            placeholder: lang('type_here_class_content'),
            tabsize: 2,
            height: 75,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol']]
            ]
        });

        $('body')
            .on('change', '#register-option', function () {
                let target = '';
                switch ($(this).val()) {
                    case 'membership':
                        $('#js-div-free-register').removeClass('d-none')
                        target = '#js-div-cost'
                        break
                    case 'membership-cost':
                        target = '#js-div-cost, #js-div-free-register'
                        break
                    case 'free-register':
                        target = '#js-div-free-register'
                        break
                }
                $(this).data('target', target)
                MeetingPopup.toggleRelatedSelect(this)
            })
            .on('change', '#broadcast-option', function () {
                $('.js-broadcast').addClass('d-none')
                switch ($(this).val()) {
                    case 'zoom':
                        $('#js-div-zoom').removeClass('d-none')
                        break
                    case 'online':
                        $('#js-div-online-class').removeClass('d-none')
                        break
                }
            })
            .on('select2:selecting', '.js-select2-multi', function (e) {
                if ([0, '0', 'BA999'].includes(e.params.args.data.id))
                    $(this).val(e.params.args.data.id).trigger('change')
                else
                    $(this).val(($(this).val()).filter(item => item != "0" && item !== "BA999")).trigger('change')
            })
            .on('select2:unselect', '.js-select2-multi', function () {
                if ($(this).find(':selected').length == 0)
                    $(this).val($(this).find('.js-default-opt').val()).trigger('change')
            })
            .on('click keypress', '.is-invalid', function () {
                $(this).removeClass('is-invalid')
            })
            .on('hidden.bs.modal', '#js-group-edit-modal', function () {
                $(this).find('input[type="radio"]').prop('checked', false)
            })

        $('#js-div-class-content')
            .on('hide.bs.collapse', function () {
                $(`[data-target="#${$(this).attr('id')}"]`).find('i').attr('class', 'fal fa-plus')
            })
            .on('show.bs.collapse', function () {
                $(`[data-target="#${$(this).attr('id')}"]`).find('i').attr('class', 'fal fa-minus')
            })

        $('#js-note-textarea')
            .on('show.bs.collapse', function () {
                $('#js-note-textarea textarea').trigger('focus')
                // $('#js-note-container .js-header-spacing').addClass('flex-grow-1')
                $('#js-note-toggle-symbol').removeClass('fa-plus').addClass('fa-minus')
            })
            .on('hide.bs.collapse', function () {
                // $('#js-note-container .js-header-spacing').removeClass('flex-grow-1')
                $('#js-note-toggle-symbol').removeClass('fa-minus').addClass('fa-plus')

            })

    },
    backToHome: function (elem) {
        $(MeetingPopup.parentElem).find(".js-subpage-tabs").addClass("d-none")
        $(elem).closest('form').find('.js-subpage-home').removeClass("d-none").addClass("animated fadeIn");
    },
    checkChild: function (elem) {
        return $(elem).find('input[type="radio"]').prop('checked', true).addClass('changed').val();
    },
    showSlide: function (elem, callback = null) {
        let js_href = $(elem).attr("data-id");
        $(elem).closest(".js-subpage-home").addClass("d-none");
        $(".js-subpage-tabs .tab-pane").removeClass("show");
        $(".js-subpage-tabs[data-href='" + js_href + "']").removeClass("d-none").addClass("animated slideInLeft");
        if (callback)
            callback();
    },
    initImgPicker: function () {
        let time = function () {
            return '?' + new Date().getTime()
        };
        $('#itemModal').imgPicker({
            url: 'Server/upload_classes.php',
            aspectRatio: 20 / 13,
            setSelect: [350, 200, 0, 0],
            deleteComplete: function () {
                $('#avatar').attr('src', '');
                $('#js-delete-avatar').addClass('d-none')
                this.modal('hide');
            },
            loadComplete: function (image) {
                // Set #avatar image src
                $('#avatar').attr('src', '/office/assets/img/default.png');
                // Set the image for re-crop
                this.setImage(image);
            },
            cropSuccess: function (image) {
                $('#avatar').attr('src', image.versions.pageImg.url + time());
                $('#js-delete-avatar').removeClass('d-none')
                $('#pageImgPath').val(image.versions.pageImg.url);
                this.modal('hide');
                if ($(".edit-avatar").hasClass("classImg")) {
                    imgUpload();
                }
            }
        });
    },
    initClassSelect2: function () {
        $("#js-select2-class")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true
                    };
                },
                dropdownParent: MeetingPopup.parentElem,
                placeholder: "Search by Name or Number",
                allowClear: true,
                theme: "bsapp-dropdown bsapp-no-arrow",
                templateResult: function (state) {
                    if (!state.name) {
                        return state.text;
                    }
                    return $(
                        '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"> <img alt="Image Error" src="'
                        + state.img
                        + '" class="w-40p h-40p rounded-circle mie-8" /><div class="d-flex flex-column"><span> '
                        + state.name + ' </span><span class="bsapp-fs-14">' + state.phone + '</span><div></div></div>'
                    );
                },
                templateSelection: function (item) {
                    let $item;
                    if (item.id == '') {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>Enter Class Name</div><div> </div> </div>');
                    } else if (item.isNew) {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>'
                            + item.text
                            + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">New</div> </div> </div>');
                    } else {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div><!--img src="'
                            + item.img + '" class="w-20p h-20p rounded-circle mie-5" /--><span> ' + item.text
                            + ' </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
                    }
                    return $item;
                }
            })
    },
    initClassTypeSelect2: function () {
        $("#js-select2-class-type")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true
                    };
                },
                allowClear: true,
                placeholder: '',
                dropdownParent: MeetingPopup.parentElem,
                theme: "bsapp-dropdown bsapp-no-arrow",
                templateResult: function (state) {
                    if (state.isNew) {
                        if (!state.loading) {
                            let $state = $(
                                '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center">'
                                + state.text + '</div><div class="badge badge-info badge-pill">'
                                + lang('create_new_cal') + '</div></div>'
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
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>'
                            + lang('exist_new_class_type') + '</div></div>');
                    } else if (item.isNew) {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>'
                            + item.text
                            + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">'
                            + lang('new')
                            + '</div> </div> </div>');
                    } else {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div><!--img src="'
                            + item.img + '" class="w-20p h-20p rounded-circle mie-5" /--><span> '
                            + item.text
                            + ' </span></div><div><span class="js-select2-selection__clear" title="">×</span></div></div>');
                    }
                    return $item;
                }
            })
            .on('select2:open', function () {
                $('.select2-container--open').find('input.select2-search__field').prop('placeholder', lang('type_for_search_or_create'));
            })
    },
    initClassColorSelect2: function () {
        $("#js-select2-class-color")
            .select2({
                theme: "bsapp-dropdown",
                minimumResultsForSearch: -1,
                dropdownParent: MeetingPopup.parentElem,
                templateResult: function (state) {
                    let $option = $(state.element);
                    return $(
                        '<div class="d-flex   align-items-center"><div class="mie-8"><i class="fas fa-circle bsapp-fs-18" style="color:'
                        + $option.attr("data-color-code")
                        + '"></i></div><div> </div></div>'
                    );
                },
                templateSelection: function (item) {
                    let $option = $(item.element);
                    $item = $('<div class="d-flex   align-items-center"><div class="mie-8"><i class="fas fa-circle bsapp-fs-18" style="color:'
                        + $option.attr("data-color-code")
                        + '"></i></div><div></div></div>');
                    return $item;
                }
            })
        ;
    },
    initModalTypeSelect2: function () {
        $(MeetingPopup.parentElem).find(".js-select2-dropdown-arrow").select2({
            theme: "bsapp-dropdown-no-border-arrow w-100 ",
            minimumResultsForSearch: -1,

            templateSelection: function (item) {
                return $('<div class="d-flex justify-content-between font-weight-medium bsapp-fs-18 "><span class="mie-6">'
                    + item.text
                    + '</span> <span><i class="fal fa-angle-down bsapp-fs-26"></i></span></div>');
            }
        }).on("select2:selecting", function (e) {
            MeetingPopup.toggleModalTypeView(e.params.args.data);
        });
    },
    initSelect2: function () {
        $(MeetingPopup.parentElem).find(".js-select2").select2({
            theme: "bsapp-dropdown",
            minimumResultsForSearch: -1,
            dropdownParent: MeetingPopup.parentElem
        });
    },
    initSelect2Templates: function () {
        $(MeetingPopup.parentElem).find(".js-select2-templates").select2({
            theme: "bsapp-dropdown",
            dropdownParent: MeetingPopup.parentElem,
            placeholder: lang('template_selection') ? lang('template_selection') : 'Template selection',
            templateSelection: function (state) {
                if (state.id != "")
                    return $('<span class="js-selected-meeting-temp">' + $(state.element).closest('option').attr('data-label') + '<span>')
                else return lang('template_selection')
            },
            "language": {
                "noResults": function () {
                    return lang('no_templates_found');
                }
            },
        }).on("select2:select", function () {
            const container = $(this).closest('.js-treatment-container')
            const selected = $(this).find(':selected')
            const clientId = $(MeetingPopup.parentElem).find('form#new-meeting [name="ClientId"]').val()

            container.find('.js-template-cost').val(Math.trunc(selected.data('cost'))).trigger('change')
            container.find('.js-template-duration').val(selected.data('minutes'))
                .trigger('change').closest('.js-template-chosen').removeClass('d-none')

            if (clientId && fieldEvents.meetingActions.checkCanShowMembership()) {
                const classTypeId = selected.val()
                fieldEvents.meetingActions.getMatchingMembership(clientId, classTypeId, container)
            }
            fieldEvents.meetingActions.checkTemplateMatch($(this))
            fieldEvents.meetingActions.templateUpdate()

            if (fieldEvents.meetingActions.arrangeTreatCount() < 1)
                $('.js-treat-include-num').find('.fa-trash-alt').closest('a').removeClass('invisible')
        })
    },
    initSelect2Schedule: function () {
        $(MeetingPopup.parentElem).find(".js-select2-schedule").select2({
            theme: "bsapp-dropdown",
            minimumResultsForSearch: -1,
            dropdownParent: MeetingPopup.parentElem,
            placeholder: lang('schedule_selection') ? lang('schedule_selection') : 'Schedule selection',
        }).select2('open').select2('close')
    },
    initSelect2Multi: function () {
        $(MeetingPopup.parentElem).find(".js-select2-multi").select2({
            minimumResultsForSearch: -1,
            dropdownParent: MeetingPopup.parentElem,
            width: '100%',
        });
    },
    toggleModalTypeView: function (selected) {
        if (selected.id == "new-class") {
            fieldEvents.classActions.show()
        } else if (selected.id == "new-meeting") {
            fieldEvents.meetingActions.show()
        }
    },
    getNewClassData: function () {
        let formdata = {fun: "initClassData"};
        $.ajax({
            url: "ajax/CreateNewClass.php",
            data: formdata,
            type: "POST",

            success: function (res) {
                if ($('input[name=CalendarId]').length < 1) {
                    let brand = $('#calendarFilters .js-select-branches').val();
                    let calendar = $($('[data-href=js-items-tab-2-calendar] input[data-brand=' + brand + ']')[0]);

                    calendar.prop('checked', true);
                    MeetingPopup.setTabPreview('js-items-tab-2-calendar', calendar.attr('data-preview'));
                }
                MeetingPopup.setTagsCategories(res);
            }
        });
    },
    initTagsCategories: async function () {
        let result = null;
        await $.ajax({
            ...MeetingPopup.ajaxOptions,
            data: {
                action: 'getTagsCategories'
            },
            success: function (res) {
                result = res;
            },
            error: function (error) {
                console.log('Error while trying to load tags categories: ' + error.data ? error.data : '');
            }
        });

        return result;
    },

    initTags: function(responseFavorite, responseOther) {
        let favorite = $("#favorite-categories");
        let other = $("#other-categories");

        for (const [key, value] of Object.entries(responseFavorite)) {
            let categoryTranslation = lang(`${key}`);
            if (!categoryTranslation) {
                categoryTranslation = 'add Translation';
            }
            favorite.append($(`<div class="oval-form oval-border-favorite favorite" data-tag="${value}" onclick="fieldEvents.showTagChoice(this)"> <p> ${categoryTranslation} </div>`));
        }

        for (const [keyOther, valueOther] of Object.entries(responseOther)) {
            let categoryTranslationOther = lang(`${keyOther}`);
            if (!categoryTranslationOther) {
                categoryTranslationOther = 'add Translation';
            }
            other.append($(`<div class="oval-form oval-border-other other" data-tag="${valueOther}" onclick="fieldEvents.showTagChoice(this)"> <p> ${categoryTranslationOther} </div>`));
        }
    },

    setTagsCategories: function (response) {
        const _this = this;
        if ($("#favorite-categories").children().length < 2) {
            let responseFavorite, responseOther;
            if (response) {

                responseFavorite = typeof response.data !== 'undefined' ? response.data.tags.favorite : (typeof response.Message !== 'undefined' ? response.Message.tags.favorite : {});
                responseOther = typeof response.data !== 'undefined' ? response.data.tags.other : (typeof response.Message !== 'undefined' ? response.Message.tags.other : {});
                if(Object.keys(responseFavorite).length === 0 || Object.keys(responseOther).length === 0) {
                    this.initTagsCategories().then((result) => {
                        responseFavorite = result.data.favorite ? result.data.favorite : {};
                        responseOther = result.data.other ? result.data.other : {};
                        _this.initTags(responseFavorite, responseOther);
                    });
                    return false;

                }
            } else {
                this.initTagsCategories().then((result) => {
                    responseFavorite = result.data.favorite ? result.data.favorite : {};
                    responseOther = result.data.other ? result.data.other : {};
                    _this.initTags(responseFavorite, responseOther);
                });
                return false;

            }


            let favorite = $("#favorite-categories");
            let other = $("#other-categories");

            for (const [key, value] of Object.entries(responseFavorite)) {
                let categoryTranslation = lang(`${key}`);
                if (!categoryTranslation) {
                    categoryTranslation = 'add Translation';
                }
                favorite.append($(`<div class="oval-form oval-border-favorite favorite" data-tag="${value}" onclick="fieldEvents.showTagChoice(this)"> <p> ${categoryTranslation} </div>`));
            }

            for (const [keyOther, valueOther] of Object.entries(responseOther)) {
                let categoryTranslationOther = lang(`${keyOther}`);
                if (!categoryTranslationOther) {
                    categoryTranslationOther = 'add Translation';
                }
                other.append($(`<div class="oval-form oval-border-other other" data-tag="${valueOther}" onclick="fieldEvents.showTagChoice(this)"> <p> ${categoryTranslationOther} </div>`));
            }
        }
    },

    toggleStopCancel: function(elem) {
        if($(elem).val() === '1'){
            $('#js-div-stop-cancel-time-before').addClass('d-none');
        }else $('#js-div-stop-cancel-time-before').removeClass('d-none');
    },
    toggleRelatedSelect: function (elem) {
        elem = $(elem);

        if (elem.attr('name') === 'SendReminder') {
            let reminderElem = $(elem).closest('.js-subpage-tabs')
            let typeReminder = reminderElem.find('select[name="TypeReminder"]')

            if (elem.val() == 0)
                typeReminder.val(1).change()

        }

        const hideValues = elem.data('hideVal').toString().split(',')
        if (!hideValues.includes(elem.val())) {
            $(elem.data('target')).find('select').trigger('change')
            $(elem.data('target')).removeClass('d-none')
        } else {
            if (elem.data('postChange').toString())
                $(elem.data('target')).find('select').val(elem.data('postChange')).trigger('change')
            $(elem.data('target')).addClass('d-none')
        }
    },
    setTabPreview: function (tabName, previewText, subPreviewText = undefined) {
        //Take tab name and set preview text to given value
        const previewDiv = $(`a[data-id="${tabName}"]`).closest('.js-div-tab-preview')

        previewDiv.find('.js-tab-preview').text(previewText)
        if (subPreviewText && !$('#meeting-completed-edit-note').length)
            previewDiv.find('.js-tab-sub-preview').text(subPreviewText).showFlex()
        else
            previewDiv.find('.js-tab-sub-preview').hideFlex()

    },
    toggleTargetAnimation: function (elem) {
        return $($(elem).data('target')).collapse('toggle')
    },
    toggleTarget: function (elem) {
        const target = $($(elem).data('target'))
        if (target.hasClass('d-none')) {
            target.removeClass('d-none')
            $('#js-note-toggle-symbol').removeClass('fa-plus').addClass('fa-minus')
            target.find('[name="Remarks"]').trigger('focus')
        } else {
            if (!target.find('[name="Remarks"]').val().length) {
                target.addClass('d-none')
                $('#js-note-toggle-symbol').removeClass('fa-minus').addClass('fa-plus')
            }
        }
        return target
    },
    checkRemarks: function (elem) {
        if ($(elem).val().length)
            MeetingPopup.parentElem.find('#js-note-clear-container').removeClass('invisible')
        else
            MeetingPopup.parentElem.find('#js-note-clear-container').addClass('invisible')
    },
    setRequiredFields: function (tabName) {
        const tab = $(`div[data-href="${tabName}"]`)
        tab.find('[name]').removeAttr('required')
        const tabInputs = tab.find('[name]:visible')
        for (let tabInput of tabInputs) {
            $(tabInput).prop('required', true)
        }
    },
    disableTabSubmit: function (tabName) {
        $(`[data-href="${tabName}"]`).find('btn-primary').addClass('disabled')
    },
    enableTabSubmit: function (tabName) {
        $(`[data-href="${tabName}"]`).find('btn-primary').removeClass('disabled')
    },
};
let tagsTranslations = [];
const fieldEvents = {
    /** Getting the active form element */
    getActiveForm: function () {
        return MeetingPopup.parentElem.find('form').filter(':not(.d-none)')
    },
    initRegularEndDate: function () {
        const form = this.getActiveForm()
        $(form).find('[name="regularEndDate"]').attr('min', $(form).find('[name="StartDate"]').val()).trigger('change')
    },
    choseStartDate: function (elem) {
        const form = $(elem).closest('form')
        const formId = form.attr('id')
        $(`form#${formId} [name="StartDate"]`).val($(elem).val()).addClass('changed')
        if (!$('#meeting-completed-edit-note').length) {
            this.initRegularEndDate()
        }

        if (formId == 'new-meeting') {
            const guideId = form.find('[name="GuideId"]:checked').val()
            fieldEvents.meetingActions.updateAvailability($(elem).val(), guideId)
        }

        // force single edit on date change for completed status
        if ($('#meeting-completed-edit-note').length) {
            $('#new-meeting select[name="ClassRepeat"]').val('0');
        }
    },
    checkStartTime: function (elem) {
        let hour = $(elem).val().split(':')[0],
            minute = $(elem).val().split(':')[1],
            hour2 = hour
        if (minute) {
            minute = parseInt(minute)
            let diff = minute % 5
            if (diff !== 0) {
                let opt1 = ('0' + (minute - diff)).slice(-2)
                let opt2 = ('0' + (minute + (5 - diff))).slice(-2)
                if (opt2 == '60') {
                    opt2 = '00'
                    hour2 = ('0' + (parseInt(hour2) + 1)).slice(-2)
                }
                $(elem).parent().find('.invalid-feedback')
                    .html(
                        `${lang('try_single')}
                        <a class="cursor-pointer" onclick="fieldEvents.setStartTime(this)"><u>${hour}:${opt1}</u></a> 
                        ${lang('or_single')} 
                        <a class="cursor-pointer" onclick="fieldEvents.setStartTime(this)"><u>${hour2}:${opt2}</u></a>`)
                $(elem).addClass('is-invalid')
            } else {
                $(elem).removeClass('is-invalid')
                $('form#new-class [name="TimeReminder"]').val(moment($(elem).val(), 'HH:mm').subtract(3, 'hours').format('HH:mm'))
            }
        } else
            $(elem).addClass('is-invalid')

        fieldEvents.checkReminderTime($('#TimeReminder'))
    },
    setStartTime: function (elem) {
        $('[name="StartTime"]').val($(elem).text()).removeClass('is-invalid')
    },
    validateForm: function (form, edit = false) {
        let fields = edit ? $(form).find('.changed') : $(form).find(':required, .custom-required')
        let data = {},
            invalidFlag = false;
        
        for (let field of fields) {
            if ($(field).hasClass('custom-invalid') || !field.checkValidity() || $(field).is(':visible') && $(field).val() == "") {
                //Move to tab if in tab and input value is invalid
                const tabName = $(field).closest('div[data-href]').data('href')
                if (tabName) {
                    $(`a[data-id="${tabName}"]`).trigger('click')
                    $(form).find(`[name="${$(field).attr('name')}"]`).addClass('is-invalid').closest('.is-invalid-container').addClass('is-invalid')
                    return
                } else {
                    $('#js-class-tab-link-general').trigger('click')
                    $(field).addClass('is-invalid').closest('.is-invalid-container').addClass('is-invalid')
                    if (field.name !== "GroupEdit") {
                        invalidFlag = true;
                    }
                }
            } else {
                $(field).removeClass('is-invalid').closest('.is-invalid-container').removeClass('is-invalid')
                let fieldName = $(field).attr('name').replace(/\[.*?\]/g, '') //remove squared brackets
                if ($(field).prop('type') == 'radio')
                    data[fieldName] = $(form).find(`[name="${$(field).prop('name')}"]:checked`).val()
                else
                    data[fieldName] = $(field).val()
            }
        }
        if (invalidFlag) {
            return 0;
        }
        if (data['ClassRepeat'] === '0') {
            data['GroupEdit'] = '0';
        }
        if (data['CalendarId'] && !data['GroupEdit']) {
            $('#js-group-edit-modal').modal('show');
            $('#js-group-edit-modal').css('zIndex',1061);
            return 0;
        }
        //check remarks empty html
        const remarksValue = data['Remarks'] ? data['Remarks'] : '';
        if(remarksValue.replace(/<(.|\n)*?>/g, '').trim().length === 0 && !remarksValue.includes("<img"))  {
            data['Remarks'] = '';
        }
        return data
    },
    checkGreaterThan: function (elem, inputName) {
        const elemVal = parseInt($(elem).val())
        const inputVal = parseInt($(`form#new-class [name="${inputName}"]`).val())
        const inputTabName = $(elem).closest('.js-subpage-tabs').data('href')

        if (inputVal > elemVal) {
            MeetingPopup.disableTabSubmit(inputTabName)
            $(elem).addClass('is-invalid custom-invalid')
        } else {
            MeetingPopup.enableTabSubmit(inputTabName)
            $(elem).removeClass('is-invalid custom-invalid')
        }
    },
    checkReminderTime: function (elem) {
        const reminderTime = moment($(elem).val(), 'hh:mm')
        const tabName = 'js-items-tab-4-timing';
        const startTime = $(elem).closest(`[data-href=${tabName}]`).find('[name="StartTime"]:first').val()
        const classTime = moment($('form#new-class [name="StartTime"]').val(), 'hh:mm')
        const thisTab = $(elem).closest('.js-subpage-tabs');

        // check if TypeReminder in the same day and SendReminder = true (send)
        if ($(thisTab).find('select[name="TypeReminder"]').val() == 1 && $(thisTab).find('select[name=SendReminder]').val() == 0) {
            if (reminderTime.isAfter(classTime)) {
                $(elem).addClass('is-invalid custom-invalid')
                return
            }
        }
        $(elem).removeClass('is-invalid custom-invalid')
    },
    removePicture: function (elem) {
        $('.ip-delete').trigger('click')
        $('form#new-class [name="image"]').val('')
    },
    cancelNewClassType: function (elem) {
        $(elem).closest('.modal').modal('hide')
        $('#js-select2-class-type').val('').trigger('change')
    },
    confirmNewClassType: function (elem) {
        const $modal = $(elem).closest('.modal').modal('hide')
        let membershipsArr = $modal.find('input[type="checkbox"]:checked').map(function () {
            return $(this).val()
        }).get()
        $('form#new-class [name="NewClassTypeMemberships"]').val(membershipsArr).addClass('custom-required');
        this.showTagInfo();
    },
    confirmGroupEdit: function (elem) {
        const $modal = $(elem).closest('.modal')

        if ($('[name="group-edit"]:checked').length)
            $('[name="GroupEdit"]').val($('[name="group-edit"]:checked').val())
        else {
            $modal.find('.is-invalid-container').addClass('is-invalid')
            return
        }
        $modal.modal('hide')
        this.getActiveForm().trigger('submit')
    },
    clearRemarks: function (elem) {
        $(elem).closest('form').find('[name="Remarks"]').val('')
            .closest('#js-note-container').find('.js-trigger-note-container').trigger('click')
        MeetingPopup.parentElem.find('#js-note-clear-container').addClass('invisible')
    },
    showGroupEditModal: function () {
        const $form = this.getActiveForm()
        const $modal = $('#js-group-edit-modal')
        let data = {}
        switch ($form.attr('id')) {
            case 'new-class':
                data.warning = lang('group_edit_attention_alert')
                data.opt1 = lang('edit_single_lesson')
                data.opt2 = lang('edit_lesson_series')
                break
            case 'new-meeting':
                data.warning = lang('group_edit_attention_alert_meeting')
                data.opt1 = lang('edit_single_meeting')
                data.opt2 = lang('edit_meeting_series')
                $form.append('<input type="hidden" value="nan" name="GroupEdit" class="changed">')
                break
        }
        for (let key in data) {
            $modal.find(`.js-${key}`).text(data[key])
        }
        $modal.modal('show')
    },
    formErrorCallback: function ($parent) {
        $parent.hideModalLoader()
        $.notify(
            {
                icon: 'fas fa-exclamation-circle',
                message: lang('error_oops_something_went_wrong'),
            }, {
                type: 'danger',
                z_index: '99999999',
            });
    },
    isMeetingTag: function () {
        return typeof meetingTemplate !== 'undefined' && meetingTemplate.mainElem !== null &&
            (meetingTemplate.mainElem.is(':visible') ||
                $('.calendarSettings-meetings-templates-new').is(':visible'));
    },
    showTagInfo: function () {
        let newMeetingTemplate = $('.calendarSettings-meetings-templates-new');
        let activityData;
        let isLesson = !this.isMeetingTag();
        if (isLesson) {
            const typeSelectElement = $('[name="ClassNameType"] :selected');
            activityData = {
                name: $('[name="ClassName"]').val(),
                typeId: typeSelectElement.attr('data-select2-tag') === true ? 0 : typeSelectElement.val(),
                typeName: typeSelectElement[0].text ?? '',
                isCreating:typeof ($('[name="CalendarId"]').val()) == 'undefined',
                isLesson: isLesson
            };
        } else {
            const categorySelectElement = newMeetingTemplate.find('#js-content-new-template #js-select2-template-category');
            activityData = {
                name: newMeetingTemplate.find('#template-name').val(),
                typeId: categorySelectElement.find(':selected').attr('data-select2-tag') === true ? 0 : categorySelectElement.val(),
                typeName: newMeetingTemplate.find('#js-content-new-template #js-select2-template-category :selected').text(),
                isCreating: !$('.save-app-template').hasClass('edit-app-template'),
                isLesson: isLesson
            };
        }
        if (
            typeof(activityData) !== 'undefined'
            && activityData.isCreating == true
            && typeof(activityData.name) !== 'undefined'
            && typeof(activityData.typeId) !== 'undefined'
            && typeof(activityData.typeName) !== 'undefined'
            && activityData.typeId !== null
            && activityData.typeName !== null
            && activityData.name.length !== 0
        ) {
            $.ajax({
                url: 'ajax/CreateNewClass.php',
                method: 'POST',
                data: {
                    fun: 'getDefaultTag',
                    data: activityData,
                },
                success: function (res) {
                    if (res.Message != 'no key') {
                        let tagName = lang(res.Message.key);
                        let id = res.Message.id;
                        fieldEvents.setTag(id,tagName);
                    }
                }
            });
            if (activityData.isLesson){
                $("#myTabContent .tagInfo").removeClass("d-none");
            } else {
                $("#js-content-new-template .tagInfo").removeClass("d-none");
            }
        }
    },
    showTagChoice: function (elem) {
        $(this).prop('disabled', true);
        $(".category-container").addClass('d-none');
        $(".tag-container").removeClass('d-none');

        let tags= elem.getAttribute("data-tag");
        let categoryName = elem.innerText;
        let tagsArray = tags.split(',');
        let tagDiv = $("#availableTags");
        let chosenCategory = $("#chosenCategory");
        const change = lang('edit_two');

        chosenCategory.append($(`    
                            <div className="bsapp-fs-18 tag-favorite-header">קטגוריה</div>
                            <div class="oval-form oval-border-black text-black" data-tag="${tags}"> <p> ${categoryName} </div>
                            <div class="oval-form blue" data-tag="${tags}" onclick="fieldEvents.restartTagsCategory()"> <p> ${change} </div>
                           `));
        tagDiv.append($(`<div className="bsapp-fs-18 tag-favorite-header">בחירת תגית</div>`));
        let i =0;
        while ( i < tagsArray.length - 1) {
            let id = tagsArray[i];
            let tagName = lang(`${tagsArray[i+1]}`);
            tagDiv.append($(`<div class='oval-form oval-border-favorite favorite' data-tagId="${id}" onclick="fieldEvents.setTag('${id}','${tagsArray[i+1]}',true)"> <p> ${tagName} </div>`));
            i = i+2;
        }
        fieldEvents.loadTagsTranslation();
    },
    restartTagsCategory: function () {
        document.getElementById("chosenCategory").textContent = '';
        document.getElementById("availableTags").textContent = '';
        $(".category-container").removeClass('d-none');
        $(".tag-container").addClass('d-none');
    },
    setTag: function (tagId, tagName, isKey = false) {
        if (isKey){
            tagName = lang(tagName)
        }
        $(`[name="tag"]`).val(tagName).attr("data-tagId", tagId);
        $('#js-tag-category-popup').modal('hide');
    },
    showTagRequest: function () {
        $('.tag-search-main-box').removeClass('d-none');
        $('.request-send').removeClass('d-flex').addClass('d-none');
        $('#requestBtn').addClass('btn-primary').removeClass('btn-gray-300').html('שלח בקשה');

        $(this).prop('disabled', true);
        fieldEvents.initTagSelect2();
        $('#js-tag-request-popup').modal('show');

    },
    loadTagsTranslation: function () {
        if (tagsTranslations.length == 0) {
            $.ajax({
                url: 'ajax/CreateNewClass.php',
                method: 'POST',
                data: {
                    fun: 'getAllTags',
                },
                success: function (res) {
                    tagsTranslations = res.Message;
                }
            });
        }
    },
    initTagSelect2: function () {
        $("#js-tag-search")
            .select2(
                {
                    tags: true,
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            isNew: true
                        };
                    },
                    placeholder: {
                        id: "",
                        placeholder: lang('choose_tag')
                    },
                    language: $("html").attr("dir") == 'rtl' ? "he" : "en",
                    allowClear: true,
                    theme: "bsapp-dropdown bsapp-no-arrow",
                    minimumInputLength: 2,
                    data: tagsTranslations,
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
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('choose_tag') + '</div><div> </div> </div>');
                        } else if (item.isNew) {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div> <span class="js-select2-selection__clear" title="">×</span> <div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div> </div> </div>');
                        } else {
                            $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + `</div><div><span class="js-select2-selection__clear" title="">×</span></div></div>`);
                        }
                        return $item;
                    }
                });

        $("#js-tag-search").on("select2:select", function (e) {
            $('#js-tag-search').siblings('.input-group-append, .select2-container').removeClass('border border-danger')
            let data = e.params.data;
            if(!data.isNew) {
                fieldEvents.setTag(data.id, data.text);
                fieldEvents.restartTagsCategory();
                $('#js-tag-request-popup').modal('hide');
                $('#js-tag-category-popup').modal('hide');
            }
        });



    },
    tagRequest: function () {
        if (!$('#js-tag-search').val().length) {
            $('#js-tag-search').siblings('.input-group-append, .select2-container')
                .addClass('border border-danger')
        } else {
            $(this).prop('disabled', true);
            if ($('#requestBtn.btn-primary').length) {
                $('.tag-search-main-box').removeClass('d-flex').addClass('d-none');
                $('.request-send').removeClass('d-none').addClass('d-flex');
                $('#requestBtn').removeClass('btn-primary').addClass('btn-gray-300').html(lang('approval'));

                let tagName = $("#select2-js-tag-search-container").attr('title');
                $.ajax({
                    url: 'ajax/CreateNewClass.php',
                    method: 'POST',
                    data: {
                        fun: 'sendTagRequest',
                        tagName: tagName
                    }
                });
            } else {
                $('#js-tag-request-popup').modal('hide');
            }
        }
    },
    showCategoryChoice: function () {
        $(this).prop('disabled', true);
        $('#js-tag-category-popup').modal('show');
        $('#js-tag-category-popup').css('zIndex',1061);
    },

    classActions: {
        init: function () {
            this.submitGuide()
            this.submitCalendar()
            this.submitTime()
            this.submitDate()
            this.submitExtRegister()
            this.submitRegOption()
            this.submitWaitingList()
            this.submitBroadcastOptions()
            this.submitClassMin()
            this.submitRegisterRist()
            this.submitOpenClose()

            this.choseGuide()
            this.choseClassType($('[name="ClassNameType"]'))
        },
        show: function () {
            $("form#new-meeting").addClass('d-none');
            $("form#new-class").removeClass('d-none');
            $('.js-select-create-new').val('new-class').trigger('change')
        },
        submitGuide: function (elem) {
            const tabName = 'js-items-tab-1-stuff'

            const checkedElem = $('[name="GuideId"]:checked')
            if (checkedElem.hasClass('d-none'))
                $(`[data-id="${tabName}"]`).closest('.js-div-tab-preview').showFlex()

            const coachName = checkedElem.data('preview')
            if (coachName) {
                let coachStr = coachName;
                const extraCoach = $('form#new-class [name="ExtraGuideId"]:checked')
                const extraCoachValue = extraCoach.val()
                const extraCoachName = extraCoach.data('preview')
                if (extraCoachName && extraCoachValue != -1) {
                    $('form#new-class [name="ExtraGuideId"]:checked').prop('required', true)
                    coachStr += ', ' + extraCoachName;
                } else {
                    $('form#new-class [name="ExtraGuideId"]').prop('required', false)
                }

                MeetingPopup.setTabPreview('js-items-tab-1-stuff', coachStr)
            }
            MeetingPopup.backToHome(elem)
        },
        submitCalendar: function (elem) {
            const brand = $('#calendarFilters .js-select-branches').val();
            const tabName = 'js-items-tab-2-calendar';
            let checkedElem = $('form#new-class [name="Floor"]:checked')

            if (!checkedElem.length) {
                checkedElem =
                    $('form#new-class [name="Floor"][data-brand="'+brand+'"]').closest('[onclick]')
                        .not('.d-none').first().trigger('click').find('input');
                if(!checkedElem.length) {
                    checkedElem = $('form#new-class [name="Floor"]').closest('[onclick]')
                        .not('.d-none').first().trigger('click').find('input');
                }
            }
            const calendarName = checkedElem.data('preview');
            $('form#new-class [name="Brands"]').val(checkedElem.data('brand'));

            if (checkedElem.closest('.js-option-div').hasClass('d-none'))
                $(`[data-id="${tabName}"]`).closest('.js-div-tab-preview').showFlex();

            if (calendarName) {
                MeetingPopup.setTabPreview(tabName, calendarName);
            }
            if (elem) {
                MeetingPopup.backToHome(elem);
            }
        },
        submitDate: function (elem) {
            const tabName = 'js-items-tab-3-datenshows'
            const classDate = $('form#new-class [name="StartDate"]').val()
            if (classDate) {
                let dateText = moment(classDate).format('ll')
                const classRepeat = $('form#new-class [name="ClassRepeat"]').val()
                let repeatText = ''
                if (classRepeat == 0)
                    repeatText = lang('single_time_lesson')
                else {
                    if (classRepeat == 1)
                        repeatText = lang('every_week')
                    else
                        repeatText = lang('every') + ` ${classRepeat} ` + lang('weeks')

                    const freqType = $('form#new-class [name="freqType"]').val()
                    if (freqType == 'date') {
                        const regularEndDate = $('form#new-class [name="regularEndDate"]')
                        if (regularEndDate.val().length && regularEndDate.get(0).checkValidity())
                            repeatText += `, ${lang('until')} ${moment(regularEndDate.val()).format('DD/MM/YYYY')}`
                        else {
                            regularEndDate.addClass('is-invalid')
                            return //Must provide date
                        }
                    } else if (freqType == 0)
                        repeatText += ` ${lang('no_time_limit')}`
                    else if (1 < freqType <= 10)
                        repeatText += ` ${lang('for_duration')} ${freqType} ${lang('times')}`
                }
                MeetingPopup.setTabPreview(tabName, dateText, repeatText)

            }
            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }

        },
        submitTime: function (elem) {
            const tabName = 'js-items-tab-4-timing';
            const timingTab = $(`[data-href=${tabName}]`)
            const startTime = $(timingTab).find('[name="StartTime"]').val()

            let timeReminderElem = (timingTab).find('#TimeReminder');

            if (startTime) {
                const duration = $('form#new-class [name="duration"]').find('option:selected').data('text')
                if (duration) {
                    let timeText = `${startTime} ${lang('for_length')} ${duration}`
                    let reminderText = ''
                    if ($('form#new-class [name="SendReminder"]').val() == 0)
                        reminderText = lang('notification_will_sent')
                            + ' ' + lang('on_time')
                            + ' ' + $('form#new-class [name="TimeReminder"]').val()
                            + ' ' + $('form#new-class [name="TypeReminder"]').find('option:selected').text()
                    else
                        reminderText = lang('without_notify')

                    MeetingPopup.setTabPreview(tabName, timeText, reminderText)
                }
            }
            if (elem) {
                fieldEvents.checkReminderTime($(timeReminderElem));
                if (!$(timeReminderElem).hasClass('is-invalid')) {
                    MeetingPopup.setRequiredFields(tabName)
                    MeetingPopup.backToHome(elem)
                }
            }
        },
        submitExtRegister: function (elem) {
            const tabName = 'js-items-tab-5-displayoption'
            const purchaseLocation = $('form#new-class [name="purchaseLocation"]').val()
            let extRegisterText = ''
            let showClientText = ''
            if (purchaseLocation != 0) {
                if (purchaseLocation == '3')
                    extRegisterText = lang('external_register_enabled')
                else if (purchaseLocation == '1')
                    extRegisterText = lang('only_from_app')

                if ($('form#new-class [name="ShowClientNum"]').val() == 0) {
                    showClientText = lang('show_attendence_amount')
                    if ($('form#new-class [name="ShowClientName"]').val() == 0)
                        showClientText += ' ' + lang('and_names')
                } else {

                    showClientText = lang('hide_part_name_count')
                }
            } else {
                showClientText = lang('hide_part_name_count')
                extRegisterText = lang('no_external_register')
            }
            MeetingPopup.setTabPreview(tabName, extRegisterText, showClientText)

            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitRegOption: function (elem) {
            const tabName = 'js-items-tab-6-registrationoption'
            const purchaseOptionSelected = $('form#new-class [name="purchaseOptions"]').find('option:selected')
            let registerOptionText = ''
            switch (purchaseOptionSelected.val()) {
                case 'membership':
                case 'free-register':
                    registerOptionText = purchaseOptionSelected.text()
                    break
                case 'membership-cost':
                    const purchaseAmountVal = $('form#new-class [name="purchaseAmount"]').val()
                    if (purchaseAmountVal)
                        registerOptionText =
                            `${lang('cost_of')} ${purchaseAmountVal} ${lang('currency_symbol')}
                                ${lang('or_with_membership')}`
                    break
            }

            let cancelOptionText = undefined;
            if (purchaseOptionSelected.val() != 'free-register') {
                cancelOptionText = lang('can_cancel_until') + ' '
                switch ($('form#new-class [name="CancelLaw"]').val()) {
                    case "3":
                        const CancelAmountVal = $('form#new-class [name="CancelPeriodAmount"]').val(),
                            CancelTypeVal = $('form#new-class [name="CancelPeriodType"]').val()
                        if (CancelAmountVal) {
                            if (CancelTypeVal == 'hour')
                                cancelOptionText += `${CancelAmountVal} ${lang('hours_before_class')}`
                            else if (CancelTypeVal == 'day')
                                cancelOptionText += `${CancelAmountVal} ${lang('days_before_class')}`
                        }
                        break;
                    case "4":
                    case "5":
                        cancelOptionText = $('form#new-class [name="CancelLaw"]').find('option:selected').text()
                        break;
                }
            }

            MeetingPopup.setTabPreview(tabName, registerOptionText, cancelOptionText)

            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitWaitingList: function (elem) {
            const tabName = 'js-items-tab-21-waitinglist'
            const ClassWating = $('form#new-class [name="ClassWating"]')
            let waitingListText = '';
            if (ClassWating.val() == 1)
                waitingListText = lang('without')
            else {
                const MaxWatingList = $('form#new-class [name="MaxWatingList"]')
                if (MaxWatingList.val() == 1)
                    waitingListText = lang('activated')
                else {
                    const NumMaxWatingList = $('form#new-class [name="NumMaxWatingList"]')
                    if (NumMaxWatingList.val())
                        waitingListText = `${lang('activated')}, ${lang('up_to')} ${NumMaxWatingList.val()} ${lang('waiting')}`
                    else {
                        NumMaxWatingList.addClass('is-invalid').closest('div').addClass('is-invalid')
                        return
                    }
                }
            }

            MeetingPopup.setTabPreview(tabName, waitingListText)

            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitBroadcastOptions: function (elem) {
            const tabName = 'js-items-tab-22-broadcastoptions'
            const LiveClass = $('form#new-class [name="LiveClass"]').val()
            let liveClassText = ''

            switch (LiveClass) {
                case 'without':
                    liveClassText = lang('without')
                    break
                case 'zoom':
                    liveClassText = $('form#new-class [name="meetingNumber"]').val() + `, ${lang('password_single')}: ` + $('form#new-class [name="ZoomPassword"]').val()
                    break;
                case 'online':
                    liveClassText = $('form#new-class [name="liveClassLink"]').val()
                    if (!liveClassText)
                        break
                    let el = liveClassText.match(/(?!http|https|https:\/\/|http:\/\/)([?a-zA-Z0-9-.\+]{2,256}\.[a-z]{2,4}\b)/gm)
                    liveClassText = el[0]
                    break;
            }


            MeetingPopup.setTabPreview(tabName, liveClassText)

            MeetingPopup.setRequiredFields(tabName)
            if (elem) MeetingPopup.backToHome(elem)
        },
        submitClassMin: function (elem) {
            const tabName = 'js-items-tab-23-participantsclass'
            const MinClass = $('form#new-class [name="MinClass"]')
            let minClassText = MinClass.find('option:selected').text()
            let minClassSubText = undefined

            if (MinClass.val() == 1) {
                const MinClassNum = $('form#new-class [name="MinClassNum"]')
                if (MinClassNum.val()) {
                    minClassText += `, ${lang('at_least')} ${MinClassNum.val()} ${lang('attendees')}`
                    minClassSubText = `${lang('will_be_checked')} ${$('form#new-class [name="ClassTimeCheck"]').val()} ${$('form#new-class [name="ClassTimeTypeCheck"]').find('option:selected').text()}`
                } else
                    return
            }

            MeetingPopup.setTabPreview(tabName, minClassText, minClassSubText)

            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitRegisterRist: function (elem) {
            const tabName = 'js-items-tab-24-registrationrestrictions'
            const genderLimit = $('form#new-class [name="GenderLimit"]')
            const memberLimit = $('form#new-class [name="ClassMemberType[]"]')
            const memberLimitVal = $('[name="ClassMemberType[]"]').val() ? $('[name="ClassMemberType[]"]').val() : []
            const levelLimit = $('form#new-class [name="LimitLevel[]"]')
            const levelLimitVal = $('[name="LimitLevel[]"]').val() ? $('[name="LimitLevel[]"]').val() : []
            let registerRistText = ''

            // if (ageLimitType.val() == 3)
            //     fieldEvents.checkGreaterThan($('form#new-class [name="ageLimitNum2"]'), 'ageLimitNum1')

            if (genderLimit.val() == 0 && (memberLimitVal.includes('BA999') ||  !memberLimit.length) && levelLimitVal.includes('0'))  {
                registerRistText = lang('open_to_everyone');
            } else {
                //Gender
                registerRistText = `${lang('gender')}: ${genderLimit.find('option:selected').text()}`;

                //Membership
                if(memberLimit.length > 0) {
                    registerRistText += `, ${lang('membership_type_single')}: `;
                    registerRistText += memberLimitVal.length == 1 ? memberLimit.find('option:selected').text() : `${memberLimitVal.length} ${lang('types')}`;
                }

                //Level
                registerRistText += `, ${lang('levels')}: `;
                registerRistText += levelLimitVal.length == 1 ? levelLimit.find('option:selected').text() : `${levelLimitVal.length} ${lang('types')}`;

            }
            MeetingPopup.setTabPreview(tabName, registerRistText)
            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitOpenClose: function (elem) {
            const tabName = 'js-items-tab-25-opencloseregistration'
            const openOrder = $('form#new-class [name="OpenOrder"]').val()
            const closeOrder = $('form#new-class [name="CloseOrder"]').val()
            let openCloseText = ''

            if (closeOrder == 1 && openOrder == 1)
                openCloseText = lang('without')
            else {
                if (openOrder == 0)
                    openCloseText += `${lang('open_register')}: ${$('form#new-class [name="OpenOrderTime"]').val()} ${$('form#new-class [name="OpenOrderType"]').find('option:selected').text()}`
                if (openOrder == 0 && closeOrder == 0)
                    openCloseText += ', '
                if (closeOrder == 0)
                    openCloseText += `${lang('close_register')}: ${$('form#new-class [name="CloseOrderTime"]').val()} ${$('form#new-class [name="CloseOrderType"]').find('option:selected').text()}`
            }

            MeetingPopup.setTabPreview(tabName, openCloseText)

            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }
        },
        submitForm: function (form, e) {
            e.preventDefault()
            let classData = fieldEvents.validateForm(form)
            if ($(`[name="tag"]`).length) {
                classData.tag = $(`[name="tag"]`).attr("data-tagId");
            }
            if (classData) {
                const $parent = MeetingPopup.parentElem
                $parent.showModalLoader()

                $.ajax({
                    ...MeetingPopup.ajaxOptions,
                    data: {
                        action: 'saveClass',
                        data: classData,
                    },
                    success: function (res) {
                        $parent.hideModalLoader()
                        if (res.status == 1) {
                            let notifyText
                            MeetingPopup.parentElem.modal('hide')
                            if (typeof classData.CalendarId !== 'undefined') {
                                charPopup.reloadPopup(classData.CalendarId)
                                notifyText = lang('lesson_update')
                            } else
                                notifyText = lang('lesson_added')
                            $.notify(
                                {
                                    icon: 'fas fa-check-circle',
                                    message: notifyText,

                                }, {
                                    type: 'success',
                                    z_index: '99999999',
                                });
                            GetCalendarData()
                        } else
                            $.notify({
                                icon: 'fas fa-times-circle',
                                message: res.message,
                            }, {
                                type: 'danger',
                                z_index: '99999999',
                            });

                    },
                    error: function (res) {
                        MeetingPopup.parentElem.modal('hide')
                        $.notify(
                            {
                                icon: 'fas fa-times-circle',
                                message: lang('action_not_done'),
                            }, {
                                type: 'danger',
                                z_index: '99999999',
                            });
                    }
                })
            }

        },

        choseGuide: function (elem) {
            if (elem)
                MeetingPopup.checkChild(elem)
            const chosenGuideId = $(`form#new-class [name="GuideId"]:checked`).val()
            $(`form#new-class [name="ExtraGuideId"]`).closest('.js-coach-container').removeClass('d-none').addClass('d-flex')
            $(`form#new-class [name="ExtraGuideId"][value="${chosenGuideId}"]`).prop('checked', false).closest('.js-coach-container').removeClass('d-flex').addClass('d-none')
        },
        choseClassType: function (elem) {
            const selectedOption = $(elem).find('option:selected')
            const selectedOptionColor = selectedOption.data('color') ? selectedOption.data('color').toString() : "";
            const classNameInput = $(elem).closest('#myTabContent').find('input[name="ClassName"]')

            classNameInput.val(selectedOption.attr('data-text'))

            this.setColor(selectedOptionColor)

            if (!$('form#new-class [name="CalendarId"]').val()) {
                if (selectedOption.data('duration') && selectedOption.data('duration') != '') {
                    $('form#new-class [name="duration"]').val(selectedOption.data('duration')).trigger('change')
                    this.submitTime()
                }
            }

            fieldEvents.showTagInfo();

            if (selectedOption.data('select2Tag')){ //New class type created
                classNameInput.val($('#js-meeting-popup').find('#select2-js-select2-class-type-results > li > div > div.d-flex').text());
                $('form#new-class [name="NewClassType"]').prop('required', true)
                let $modal = $('#js-modal-new-class-type').modal('show');
                $modal.showModalLoader()
                $.ajax({
                    method: 'GET',
                    url: '/office/partials-views/add-meeting/modal-new-class-type.php',
                    success: function (res){
                        $modal.hideModalLoader()
                        $modal.find('.membership-container').html(res)
                    }
                })
            } else {
                $('form#new-class [name="NewClassType"]').prop('required', false);
            }

            $('form#new-class [name="NewClassTypeMemberships"]').removeClass('custom-required')
        },
        setColor: function (color) {
            if (color && color !== "#e2e2e2") {
                if (color.includes('#')) {
                    $('option[value="custom"]').attr('data-color-code', color).prop('disabled', false)
                    $('#js-select2-class-color').val('custom').trigger('change')
                    $('#select2-js-select2-class-color-container').find('i').css('color', color)
                } else {
                    $('#js-select2-class-color').val(color).trigger('change')
                    $('option[value="custom"]').prop('disabled', true)
                }
            }
        },
        choseColor: function (elem) {
            const selectedColor = $(elem).find('option:selected').attr('data-color-code');
            $('#classColor').val(selectedColor)
        },
    },

    meetingActions: {
        init: function () {
            this.submitClient()
            this.submitTherapist()
            this.submitDate()
            this.submitCalendar()

            $('input[type=radio][name=ChargeType]').change(function () {
                if (this.value == 0) {
                    $("#js-proceed-to-payment").show();
                } else {
                    $("#js-proceed-to-payment").hide();
                }
            });
        },
        show: function () {
            $("form#new-class").addClass('d-none');
            $("form#new-meeting").removeClass('d-none');
            $('.js-select-create-new').val('new-meeting').trigger('change')
            $(".js-subpage-class-new .tagInfo").addClass('d-none');
            this.updateAvailability(MeetingPopup.parentElem.find('form#new-meeting').find('[name="StartDate"]').val(),
                MeetingPopup.parentElem.find('form#new-meeting').find('[name="GuideId"]:checked').val())
            fieldEvents.initRegularEndDate()


        },
        submitClient: function (elem) {
            const tabName = 'js-meeting-tab-client';
            let clientName = $('form#new-meeting [name="ClientName"]').val();

            if ($('form#new-meeting [name="IsNew"]').val() === '1') {
                if (!clientName.length) {
                    $('form#new-meeting [name="ClientName"]').addClass('is-invalid');
                    return;
                }

                let regex = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/;
                if (!($('form#new-meeting [name="UserPhone"]').val()).match(regex)) {
                    $('form#new-meeting .js-user-data-details .invalid-feedback').html(lang('phone_format_incorrect_ajax'));
                    $('form#new-meeting [name="UserPhone"]').addClass('is-invalid');
                    return;
                }

                // check if existing phone
                $.ajax({
                    type: 'POST',
                    url: "/office/ajax/ClientsSettings.php",
                    data: {
                        fun: "checkExistingClient",
                        phone: $('form#new-meeting [name="UserPhone"]').val(),
                    },
                    dataType: 'JSON',
                    success: function (result) {
                        if (result.Status === 1) {
                            if (result.response.isDuplicate) {
                                $('form#new-meeting .js-user-data-details .invalid-feedback').html(lang('mobile_exists_ajax'));
                                $('form#new-meeting [name="UserPhone"]').addClass('is-invalid');
                                return;
                            }

                            if (!clientName.length) {
                                clientName = lang('occasional_customer');
                                $('#js-client-tab-reset').addClass('d-none');
                                $('#js-client-tab-link').removeClass('d-none');
                            } else {
                                $('#js-client-tab-reset').removeClass('d-none');
                                $('#js-client-tab-link').addClass('d-none');
                            }
                            MeetingPopup.setTabPreview(tabName, clientName);

                            if (elem)
                                MeetingPopup.backToHome(elem);
                        } else {
                            $.notify({
                                message: result.Message
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.responseText);
                        console.log(thrownError);
                        $.notify({
                            message: lang('error_oops_something_went_wrong')
                        }, {
                            type: 'danger',
                            z_index: 2000,
                        });
                    }
                });
            } else {
                if (!clientName.length) {
                    clientName = lang('occasional_customer');
                    $('#js-client-tab-reset').addClass('d-none');
                    $('#js-client-tab-link').removeClass('d-none');
                } else {
                    $('#js-client-tab-reset').removeClass('d-none');
                    $('#js-client-tab-link').addClass('d-none');
                }
                MeetingPopup.setTabPreview(tabName, clientName);

                if (elem)
                    MeetingPopup.backToHome(elem);
            }
        },
        submitTherapist: function (elem) {
            const coachName = $('form#new-meeting [name="GuideId"]:checked').data('preview')
            if (coachName)
                MeetingPopup.setTabPreview('js-meeting-tab-therapist', coachName)
            MeetingPopup.backToHome(elem)
        },
        submitDate: function (elem) {
            const tabName = 'js-meeting-tab-date'
            const classDate = $('form#new-meeting [name="StartDate"]').val()
            if (classDate) {
                let dateText = moment(classDate).format('ll')
                const $classRepeat = $('form#new-meeting [name="ClassRepeat"]')
                const classRepeat = $classRepeat.val()
                let repeatText = ''
                const clientId = $(MeetingPopup.parentElem).find('form#new-meeting [name="ClientId"]').val()
                fieldEvents.meetingActions.checkCanUseMembershipAndFindMembership(clientId); // show use membership when create single
                if (classRepeat == 0) {
                    repeatText = lang('single_time_meeting')
                } else {
                    repeatText = $classRepeat.find('option:selected').text()

                    const freqType = $('form#new-meeting [name="freqType"]').val()
                    if (freqType == 'date') {
                        const regularEndDate = $('form#new-meeting [name="regularEndDate"]')
                        if (regularEndDate.val().length && regularEndDate.get(0).checkValidity())
                            repeatText += `, ${lang('until')} ${moment(regularEndDate.val()).format('DD/MM/YYYY')}`
                        else {
                            regularEndDate.addClass('is-invalid')
                            return //Must provide date
                        }
                    } else if (freqType == 0)
                        repeatText += ` ${lang('no_time_limit')}`
                    else if (1 < freqType <= 10)
                        repeatText += ` ${lang('for_duration')} ${freqType} ${lang('times')}`
                }
                MeetingPopup.setTabPreview(tabName, dateText, repeatText)

            }
            if (elem) {
                MeetingPopup.setRequiredFields(tabName)
                MeetingPopup.backToHome(elem)
            }

        },
        submitCalendar: function (elem) {
            const brand = $('#calendarFilters .js-select-branches').val();
            const tabName = 'js-meeting-tab-calendar'
            let checkedElem = $('form#new-meeting [name="Floor"]:checked')
            if (!checkedElem.length) {
                checkedElem = $('form#new-meeting [name="Floor"][data-brand="' + brand + '"]').closest('.js-calendar-option')
                    .not('.d-none').first().find('input').prop('checked', true);
                if (!checkedElem.length) {
                    checkedElem = $('form#new-meeting [name="Floor"]').closest('.js-calendar-option')
                        .not('.d-none').first().find('input').prop('checked', true);
                }
            }
            const calendarName = checkedElem.data('preview')
            $('form#new-meeting [name="Brands"]').val(checkedElem.data('brand'))

            if (checkedElem.closest('.js-option-div').hasClass('d-none'))
                $(`[data-id="${tabName}"]`).closest('.js-div-tab-preview').showFlex()

            if (calendarName)
                MeetingPopup.setTabPreview(tabName, calendarName)

            if (elem)
                MeetingPopup.backToHome(elem)
        },
        submit: function (form, e = null, overrideLimitation = null, redirect = false) {
            if (e) e.preventDefault()
            let meetingData = fieldEvents.validateForm(form)
            if (meetingData) {
                if (!fieldEvents.meetingActions.getTreatments().treatments) {
                    //No treatments exception
                    return
                }
                meetingData['treatments'] = fieldEvents.meetingActions.getTreatments().treatments
                if (overrideLimitation) meetingData['overrideLimitation'] = overrideLimitation
                const $parent = MeetingPopup.parentElem
                $parent.showModalLoader()
                $.ajax({
                    ...MeetingPopup.ajaxOptions,
                    data: {
                        action: 'createMeeting',
                        data: meetingData
                    },
                    success: function (res) {
                        switch (res.status) {
                            case 1:
                                $parent.hideModalLoader();
                                MeetingPopup.parentElem.modal('hide');

                                if ($('#js--meeting-popup_details').hasClass('show')) {
                                    $('#js--meeting-popup_details').modal('hide');
                                }

                                // check if restrictions returned
                                if (res.data.restrictions.length > 0) {
                                    currDate = meetingData.StartDate
                                    SetDate()
                                    GetCalendarData()

                                    fieldEvents.meetingActions.openOverLimitationModal(res.data.restrictions, res.data.restrictions[0].checkRes.Message);
                                    break;
                                }

                                $.notify(
                                    {
                                        icon: 'fas fa-check-circle',
                                        message: res.message,

                                    }, {
                                        type: 'success',
                                        z_index: '99999999',
                                    });
                                if (redirect && res.data.clientId != 0) {
                                    if(res.data.isBeta) {
                                        window.location.href = `/office/cart.php?u=${res.data.clientId}&debt=${res.data.clientActivityIds}`;
                                    } else {
                                        window.location.href = `/office/ClientProfile.php?u=${res.data.clientId}&client_activity=${res.data.clientActivityIds}#user-pay`
                                    }
                                }
                                else {
                                    currDate = meetingData.StartDate
                                    SetDate()
                                    GetCalendarData()
                                }
                                break
                            case 2:
                                occupiedPopup.show(res.data, redirect)
                                break;
                            default:
                                $parent.hideModalLoader()
                                $.notify(
                                    {
                                        icon: 'fas fa-exclamation-circle',
                                        message: res.message ? res.message : lang('error_oops_something_went_wrong'),
                                    }, {
                                        type: 'danger',
                                        z_index: '99999999',
                                    });
                                break
                        }
                    },
                    error: function () {
                        fieldEvents.formErrorCallback($parent)
                    }
                })
            }
            console.log(meetingData)
        },
        edit: function (form, e = null, overrideLimitation) {
            if (e) e.preventDefault()

            let data = fieldEvents.validateForm(form, true)
            if(!$(form).find('#meeting-completed-edit-note').length) { // only if this meeting status not completed, can edit group
                if (data.GroupNumber && isNaN(data.GroupEdit)) {
                    fieldEvents.showGroupEditModal()
                    return
                } else if (!data.GroupNumber && data.ClassRepeat) {
                    data.GroupEdit = 1;
                }
            }
            if (data) {
                const $parent = MeetingPopup.parentElem
                $parent.showModalLoader()
                if (overrideLimitation) data['overrideLimitation'] = overrideLimitation
                $.ajax({
                    ...MeetingPopup.ajaxOptions,
                    data: {
                        action: 'updateMeeting',
                        data: data
                    },
                    success: function (res) {
                        switch (res.status) {
                            case 0:
                                $parent.hideModalLoader()
                                MeetingPopup.parentElem.modal('hide')
                                $.notify(
                                    {
                                        icon: 'fas fa-exclamation-circle',
                                        message: res.message ? res.message : lang('error_oops_something_went_wrong'),
                                    }, {
                                        type: 'danger',
                                        z_index: '99999999',
                                    });
                                break
                            case 1:
                                $parent.hideModalLoader()
                                $.notify(
                                    {
                                        icon: 'fas fa-check-circle',
                                        message: res.message,

                                    }, {
                                        type: 'success',
                                        z_index: '99999999',
                                    });
                                $parent.modal('hide')
                                meetingDetailsModule.updateMeetingModal()
                                GetCalendarData()
                                break
                            case 2:
                                occupiedPopup.show(res.data)
                                break;
                            default:
                                $parent.hideModalLoader()
                                $.notify(
                                    {
                                        icon: 'fas fa-exclamation-circle',
                                        message: res.message ? res.message : lang('error_oops_something_went_wrong'),
                                    }, {
                                        type: 'danger',
                                        z_index: '99999999',
                                    });
                                break
                        }

                    },
                    error: function () {
                        fieldEvents.formErrorCallback($parent)
                    }
                })
            }
        },
        initChargeOptions: function (treatmentContainer) {
            const treatCount = $('.js-treat-include-num').length
            $(treatmentContainer)
                .find('.js-charge-type-div input[type="radio"]').attr('name', `chargeType${treatCount}`).end()
                .find('#js-radio-member-1').attr('id', `chargeType${treatCount}-1`).end()
                .find('#js-radio-member-2').attr('id', `chargeType${treatCount}-2`).attr('checked', true).end()
                .find('[for="js-radio-member-1"]').attr('for', `chargeType${treatCount}-1`).end()
                .find('[for="js-radio-member-2"]').attr('for', `chargeType${treatCount}-2`).end()
            return treatmentContainer
        },

        resetClient: function () {
            MeetingPopup.userSearch.showSearchField(this)
            this.submitClient()
        },
        choseTherapist: function (elem) {
            const userId = MeetingPopup.checkChild($(elem).closest('.js-therapist-option'))
            const date = $(elem).closest('form').find('[name="StartDate"]').val()
            fieldEvents.meetingActions.updateAvailability(date, userId)

            this.getActiveTreatmentsContainer().each(function () {
                fieldEvents.meetingActions.checkTemplateMatch($(this).find('.js-template-select'))
            })
            this.submitTherapist(elem)
        },
        choseCalendar: function (elem) {
            const calendarId = MeetingPopup.checkChild($(elem).closest('.js-calendar-option'))
            this.getActiveTreatmentsContainer().each(function () {
                fieldEvents.meetingActions.checkTemplateMatch($(this).find('.js-template-select'))
            })
            this.submitCalendar(elem)
        },

        collapseTreatments: function () {
            const treatmentContainers = $('.js-treatment-container')
            for (let container of treatmentContainers) {
                const treatmentItem = $(container).find('.js-treatment-item').collapse('hide')
                const {startTime, endTime} = this.getTreatmentTime(container)
                let preview = treatmentItem.find('.js-selected-meeting-temp').text()
                preview = preview === '' ? lang('missing_template') : preview + ` (${startTime}-${endTime})`
                const previewContainer = $(container).find('.js-treatment-preview')
                previewContainer.showFlex()
                previewContainer.find('input').val(preview)
            }
        },
        showTreatment: function (elem) {
            this.collapseTreatments()
            $(elem).closest('.js-treatment-preview').hideFlex()
            $(elem).closest('.js-treatment-container').find('.js-treatment-item').collapse('show')
        },
        removeTreatment: function (elem) {
            const container = $(elem).closest('.js-treatment-container').removeClass('pt-10')
            const treatmentItem = container.find('.js-treatment-item')
            treatmentItem.collapse('hide')
            container.closest('.js-treat-include-num').fadeOut(300, function () {
                $(this).hideFlex()
                fieldEvents.meetingActions.arrangeTreatCount()
                fieldEvents.meetingActions.templateUpdate()
                $('.js-treat-include-num:visible').eq(1).removeClass('border-top')
            })
        },
        addTreatment: function (elem) {
            const addBtn = $(MeetingPopup.parentElem).find('.js-add-treat-btn').last().closest('.js-treat-include-num').clone()
            addBtn.find('.js-add-treat-btn').showFlex()
            this.collapseTreatments()
            const container = $(MeetingPopup.parentElem).find('.js-treatment-container').first()
            container.find('.js-select2,.js-select2-templates,.js-select2-schedule').select2('destroy')
            $(elem).closest('div').hideFlex()
            const clone = this.initChargeOptions(container.clone())
            const newElem = $(elem).closest('.w-100').append(clone).closest('.js-treat-include-num').addClass('pt-10')
            MeetingPopup.initSelect2Templates()
            MeetingPopup.initSelect2Schedule()
            MeetingPopup.initSelect2()
            newElem.find('.js-template-cost').val('')
            newElem.find('.js-template-duration').val('60').closest('.js-template-chosen').addClass('d-none')
            newElem.find('.js-charge-type-div').hideFlex();
            $("#js-proceed-to-payment").show();
            newElem.find('.input-group-prepend').trigger('click')
            newElem.find('.js-template-select').removeClass('is-invalid')
            fieldEvents.meetingActions.setNextTreatTime(newElem)

            $(elem).closest('#js-all-treatment-container').append(addBtn.hideFlex())
            this.arrangeTreatCount()
        },
        setNextTreatTime: function (elem) {
            const $prevTreatment = fieldEvents.meetingActions.getActiveTreatmentsContainer().eq('-2'),
                $prevTemplateOptGroup = $prevTreatment.find('.js-template-select :selected').closest('option'),
                prevDuration = $prevTreatment.find('.js-template-duration').val(),
                prevSchedule = $prevTreatment.find('.js-schedule-select').val(),
                prepType = $prevTemplateOptGroup.data('prep-type'),
                prepTime = $prevTemplateOptGroup.data('prep-time-val'),
                prepTimeUnit = $prevTemplateOptGroup.data('prep-time-unit');

            if (prevDuration && prevSchedule) {
                const prepInMinutes = prepTimeUnit == 1 ? prepTime * 60 : prepTime
                let nextTreatTime = moment(prevSchedule, 'HH:mm').add(prevDuration, 'minutes')
                if (prepType == 2)
                    nextTreatTime = moment(nextTreatTime).add(prepInMinutes, 'minutes')
                elem.find('.js-schedule-select').val(nextTreatTime.format('HH:mm')).trigger('change')
            }
        },
        arrangeTreatCount: function () {
            const treatNums = $('.js-treat-num:visible')
            for (let i = 0; i < treatNums.length; i++) {
                $(treatNums[i]).text(i + 1).closest('.js-treat-include-num').attr('data-treat-num', i + 1)
            }
            return treatNums.length
        },
        getActiveTreatmentsContainer: function () {
            return $(MeetingPopup.parentElem).find('.js-treat-include-num:not(.d-none)')
        },
        getTreatments: function () { //return treatments data and conclusion
            const $treatments = this.getActiveTreatmentsContainer()
            let costSum = 0,
                minutesSum = 0,
                $duration,
                $cost,
                $templateSelected,
                $timeSelected,
                $chargeType,
                membershipId,
                treatmentsList = [];

            for (let $treatment of $treatments) {
                $treatment = $($treatment)
                $templateSelected = $treatment.find('.js-template-select option:selected')
                $timeSelected = $treatment.find('.js-schedule-select option:selected')
                $cost = $treatment.find('.js-template-cost')
                $duration = $treatment.find('.js-template-duration')

                if ((!$templateSelected.val() && (treatmentsList.length === 0 || treatmentsList.length < ($treatments.length) - 1))
                    || this.checkFieldEmpty($templateSelected)) {
                    $treatment.find('.js-template-select').closest('.is-invalid-container').addClass('is-invalid');
                    return 0;
                }

                this.checkFieldEmpty($cost)

                $chargeType = $treatment.find('.js-charge-type-div:not(.d-none) [type="radio"]:checked')
                if ($chargeType.length != 0 && $chargeType.hasClass('js-from-membership-input'))
                    membershipId = $chargeType.val()
                else
                    membershipId = null


                if ($templateSelected.length) {
                    treatmentsList.push({
                        classTypeId: $templateSelected.val(),
                        templateId: $templateSelected.closest('option').data('template-id'),
                        duration: $duration.val(),
                        cost: $cost.val(),
                        time: $timeSelected.val(),
                        membershipId: membershipId
                    })

                    costSum += parseInt($cost.val())
                    minutesSum += parseInt($duration.val())
                }
            }
            if (isNaN(costSum) || isNaN(minutesSum))
                return 0

            return {
                treatments: treatmentsList,
                cost: costSum,
                minute: minutesSum
            }
        },
        checkFieldEmpty: function (elem) {
            if (elem.val() == '') {
                elem.closest('.is-invalid-container').addClass('is-invalid')
                return true
            }
            elem.closest('.is-invalid-container').removeClass('is-invalid')
            return false
        },
        isTemplateSelected: function () { //Determine there is selected and handle the related elements
            const isSelected = this.getTreatments()
            if (isSelected) {
                // $('.js-treat-include-num').last().showFlex()
                // this.arrangeTreatCount()
                $('#js-div-tab-conclusion').showFlex()
            } else {
                // $('.js-treat-include-num').last().hideFlex()
                $('#js-div-tab-conclusion').hideFlex()
            }
            $('.js-treat-include-num').last().showFlex()
            this.arrangeTreatCount()
        },
        templateUpdate: function () {
            //Conclusion
            const data = fieldEvents.meetingActions.getTreatments()
            if (data) {
                let durationMoment = moment.duration(data.minute, 'minutes')
                let durationStr = ''
                if (durationMoment.get('hours') > 0) {
                    durationStr = `${durationMoment.get('hours')} ${lang('hours')}` +
                        (durationMoment.get('minutes') > 0 ?
                            ` ${durationMoment.get('minutes')} ${lang('minutes')}` : '')
                } else {
                    durationStr = `${durationMoment.get('minutes')} ${lang('minutes')}`
                }

                $(MeetingPopup.parentElem).find('#js-meeting-cost').text(data.cost)
                $(MeetingPopup.parentElem).find('#js-meeting-length').text(durationStr)
            }
            fieldEvents.meetingActions.isTemplateSelected()
            fieldEvents.meetingActions.checkAllScheduleSelect()
        },
        getMatchingMembership: function (clientId, classTypeId, container) {
            if (classTypeId == "")
                return
            $.ajax({
                ...MeetingPopup.ajaxOptions,
                data: {
                    action: 'getMatchingMembership',
                    clientId: clientId,
                    classTypeId: classTypeId
                },
                success: function (res) {
                    if (res.status) {
                        if (res.data == null || container.find('.js-charge-type-div').hasClass('disabled')) {
                            container.find('.js-charge-type-div').hideFlex();
                            $("#js-proceed-to-payment").show();
                            return
                        }
                        $("#js-radio-member-2").click();
                        const chargeSection = container.find('.js-charge-type-div').showFlex()
                        chargeSection.find('.js-from-membership-input').val(res.data)
                    }
                }
            })
        },
        checkAllTemplatesMembership: function (clientId = null) {
            if (!clientId) {
                $(MeetingPopup.parentElem).find('.js-treat-include-num .js-charge-type-div').hideFlex();
                $("#js-proceed-to-payment").show();
            }
            this.getActiveTreatmentsContainer().each(function () {
                const $selected = $(this).find('.js-template-select option:selected')
                if ($selected.length) {
                    fieldEvents.meetingActions.getMatchingMembership(clientId, $selected.val(), $(this))
                }
            })
        },
        checkCanUseMembershipAndFindMembership: function (clientId = null){
            if(fieldEvents.meetingActions.checkCanShowMembership()){
                fieldEvents.meetingActions.checkAllTemplatesMembership(clientId);
            } else {
                $(MeetingPopup.parentElem).find('.js-treat-include-num .js-charge-type-div').hideFlex();
                $("#js-proceed-to-payment").show();
            }
        },
        checkCanShowMembership: function () {
            // only single meeting can show and use membership
            return ($('#new-meeting select[name="ClassRepeat"]').val() == '0'
                // and don't show on edit
                && !$('form#new-meeting').hasClass('js-edit'));
        },
        updateAvailability: function (date, userId) {
            $.ajax({
                ...MeetingPopup.ajaxOptions,
                data: {
                    action: 'getUserAvailability',
                    date: date,
                    userId: userId
                },
                success: function (res) {
                    const $options = $(MeetingPopup.parentElem).find('.js-select2-schedule option')
                    $options.removeClass('available')
                    if (res.data) {
                        for (const time in res.data) {
                            $(MeetingPopup.parentElem).find(`.js-select2-schedule option[value="${res.data[time]}"]`).addClass('available')
                        }
                    } else
                        $('.js-select2-schedule option').addClass('available')
                }
            })
        },
        checkRemarks: function (elem) {
            if (elem.val().length > 0) {
                $(MeetingPopup.parentElem)
            } else {
                $(MeetingPopup.parentElem).find('.js-remarks-div').hideFlex()
            }
        },

        checkTemplateMatch: function (templateSelect) {
            templateSelect = $(templateSelect)
            templateSelect.removeClass('is-invalid')
            this.checkCertainParam(
                templateSelect,
                'coaches',
                MeetingPopup.parentElem.find('form#new-meeting [name="GuideId"]:checked').val(),
                lang('therapist_template_not_match')
            )
            this.checkCertainParam(
                templateSelect,
                'calendars',
                MeetingPopup.parentElem.find('form#new-meeting [name="Floor"]:checked').val(),
                lang('calendar_template_not_match')
            )

            fieldEvents.showTagInfo();
        },
        checkCertainParam: function (templateSelect, dataAttr, valToCheck, errorMsg) {
            const $optgroup = templateSelect.find(':selected').closest('option')
            const optGroupData = $optgroup.data(dataAttr) ? $optgroup.data(dataAttr).toString() : $optgroup.data(dataAttr);
            if (optGroupData && optGroupData.length) {
                const calendarIdArr = optGroupData.split(',')
                if (!calendarIdArr.includes(valToCheck))
                    templateSelect.addClass('is-invalid')
                        .closest('.form-group').find('.invalid-feedback')
                        .text(errorMsg)
            }
        },
        checkAllScheduleSelect: function () {
            this.getActiveTreatmentsContainer().each(function () {
                fieldEvents.meetingActions.checkScheduleSelection($(this).find('.js-schedule-select'))
            })
        },
        checkScheduleSelection: function (scheduleSelect) {
            scheduleSelect = $(scheduleSelect)
            scheduleSelect.removeClass('is-invalid')
            const $treatment = scheduleSelect.closest('.js-treat-include-num'),
                treatNum = parseInt($treatment.attr('data-treat-num')),
                $prevTreatment = MeetingPopup.parentElem
                    .find(`.js-treat-include-num:not(.d-none)[data-treat-num=${(treatNum - 1)}]`),
                $nextTreatment = MeetingPopup.parentElem
                    .find(`.js-treat-include-num:not(.d-none)[data-treat-num=${(treatNum + 1)}]`);

            const {startTime, endTime} = this.getTreatmentTimeWithPrep($treatment)
            const prevTimeRange = this.getTreatmentTimeWithPrep($prevTreatment)
            const nextTimeRange = this.getTreatmentTimeWithPrep($nextTreatment)
            if (startTime && endTime) {
                if (prevTimeRange.startTime && prevTimeRange.endTime) {
                    if (moment(startTime, 'HH:mm').isBefore(moment(prevTimeRange.endTime, 'HH:mm'))) {
                        scheduleSelect.addClass('is-invalid')
                            .closest('.form-group').find('.invalid-feedback')
                            .text(lang('meeting_before_prev'))
                    }
                }
                if (nextTimeRange.startTime && nextTimeRange.endTime) {
                    if (moment(endTime, 'HH:mm').isAfter(moment(nextTimeRange.startTime, 'HH:mm'))) {
                        scheduleSelect.addClass('is-invalid')
                            .closest('.form-group').find('.invalid-feedback')
                            .text(lang('meeting_after_next'))
                    }
                }
            }
        },
        getTreatmentTime: function (treatmentContainer) {
            treatmentContainer = $(treatmentContainer)
            const startTime = treatmentContainer.find('.js-schedule-select').val(),
                duration = treatmentContainer.find('.js-template-duration').val(),
                endTime = moment(startTime, 'HH:mm').add(duration, 'minutes').format('HH:mm');
            return {startTime, endTime}
        },
        getTreatmentTimeWithPrep: function (treatmentContainer) {
            treatmentContainer = $(treatmentContainer)
            const $selectedOptGroup = treatmentContainer.find('.js-template-select :selected').closest('option'),
                prepType = $selectedOptGroup.data('prep-type'),
                prepTimeVal = $selectedOptGroup.data('prep-time-val'),
                prepTimeUnit = $selectedOptGroup.data('prep-time-unit') == 0 ? 'minutes' : 'hours';

            let {startTime, endTime} = this.getTreatmentTime(treatmentContainer)

            if (startTime && endTime) {
                switch (prepType) {
                    case 1:
                        startTime = moment(startTime, 'HH:mm').subtract(prepTimeVal, prepTimeUnit).format('HH:mm');
                        break
                    case 2:
                        endTime = moment(endTime, 'HH:mm').add(prepTimeVal, prepTimeUnit).format('HH:mm');
                        break
                }
            }

            return {
                startTime: startTime,
                endTime: endTime
            }
        },
        openOverLimitationModal: function (data, Message) {
            $('#js-modal-over-limitation-content').html('');
            $('#js-modal-over-limitation').modal('show');
            $('#js-modal-over-limitation-data').data('formData', JSON.stringify(data));
            $.ajax({
                method: 'GET',
                url: '/office/partials-views/char-popup/modal-over-limitation.php',
                data: {
                    text: Message,
                    isMeeting: true
                },
                success: function (content) {
                    $('#js-modal-over-limitation-content').html(content);
                }
            });
        },
        fixOverLimitationAssignment: function (button) {
            setHtmlToLoader(button);
            let meetingData = JSON.parse($('#js-modal-over-limitation-data').data('formData'));

            $.ajax({
                ...MeetingPopup.ajaxOptions,
                data: {
                    action: 'fixMeetings',
                    data: meetingData
                },
                success: function (res) {
                    $('#js-modal-over-limitation').modal('hide');

                    $.notify(
                        {
                            icon: 'fas fa-check-circle',
                            message: res.message,

                        }, {
                            type: 'success',
                            z_index: '99999999',
                        });

                    // refresh calendar
                    currDate = meetingData.StartDate
                    SetDate()
                    GetCalendarData()
                },
                error: function (res) {
                    $.notify(
                        {
                            icon: 'fas fa-exclamation-circle',
                            message: res.message ? res.message : lang('error_oops_something_went_wrong'),
                        }, {
                            type: 'danger',
                            z_index: '99999999',
                        });
                }
            })
        }
    }
}

const populateFields = {
    class: {
        init: function (classId, duplicate = 0) {
            MeetingPopup.parentElem.showModalLoader()

            $.ajax({
                ...MeetingPopup.ajaxOptions,
                data: {
                    action: 'getClassData',
                    id: classId
                },
                success: populateFields.class.populateCallback,
                error: function () {
                    MeetingPopup.parentElem.modal("hide")
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('no_class_found'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            }).then(function () {
                if (duplicate) {
                    let repeatTypeElem = $('form#new-class [name="ClassRepeat"]');
                    repeatTypeElem.closest('.form-group').removeClass('d-none'); //show frequency
                    repeatTypeElem.prop('disabled', false);
                }

            })
        },
        populateCallback: function (res) {
            MeetingPopup.parentElem.find('.modal-content').find(".js-loader").remove()

            let guideName;
            const data = res.data
            for (let [key, value] of Object.entries(data)) {
                if (['LimitLevel', 'ClassMemberType'].includes(key))
                    key += '[]'
                const elem = $(`form#new-class [name="${key}"]`)

                if (key == 'ClasClassNamesTimeCheck' && value == 0)
                    value = 60;
                if (key == 'StartDate')
                    $('.datepicker').datepicker('setDate', new Date(value));
                if (key == 'StartTime' || key == 'TimeReminder')
                    value = moment(value, 'HH:mm:ss').format('HH:mm')
                if (elem.hasClass('js-select2-multi'))
                    value = value.split(',')

                if (key == 'is_zoom_class' && value == 1) {
                    $('form#new-class [name="LiveClass"]').val('zoom').trigger('change')
                    $('form#new-class [name="meetingNumber"]').val(data.zoomData.meeting_id)
                    $('form#new-class [name="ZoomPassword"]').val(data.zoomData.password)
                }
                if (key == 'onlineClassId' && value || (data.liveClassLink && data.liveClassLink.length > 0)) {
                    $('form#new-class [name="LiveClass"]').val('online').trigger('change')
                }
                if (key == 'CancelLaw' && ['1', '2', '3'].includes(value)) {
                    let cancel_date = data.StartDate
                    let cancel_days_sub = 0
                    if (value == 2) {
                        cancel_days_sub = 1
                    } else if (value == 3) {
                        cancel_days_sub = data.CancelDayMinus
                    }
                    value = '3'
                    let CancelPeriodAmount = parseInt(populateFields.class.calcPeriod(data.start_date, `${data.StartDate} ${data.CancelTillTime}`, cancel_days_sub)),
                        CancelPeriodType = 'hour'

                    if (CancelPeriodAmount % 24 == 0) {
                        CancelPeriodAmount = CancelPeriodAmount / 24
                        CancelPeriodType = 'day'
                    }
                    $('form#new-class [name="CancelPeriodAmount"]').val(CancelPeriodAmount)
                    $('form#new-class [name="CancelPeriodType"]').val(CancelPeriodType).trigger('change')
                }

                if(key == 'StopCancel') $('[name=StopCancel]', 'form#new-class').val(value);
                if(key == 'StopCancelType') $('[name=StopCancelType]', 'form#new-class').val(value);
                if(key == 'StopCancelTime') $('[name=StopCancelTime]', 'form#new-class').val(value);

                if (key == 'ageLimitType') {
                    if (value == 0) {
                        $('form#new-class [name="ageRistriction"]').val(0).trigger('change')
                        value = 1
                    } else
                        $('form#new-class [name="ageRistriction"]').val(1).trigger('change')
                }

                if (key == 'ClassRepeat' && value == 0) {
                    let classRepeatElem = $('form#new-class [name="ClassRepeat"]');
                    if (data.ClassType != 3) {
                        classRepeatElem.val(1).trigger('change');
                        classRepeatElem.prop('disabled', false);
                    } else {
                        classRepeatElem.val(0).trigger('change');
                        classRepeatElem.prop('disabled', true);
                    }
                } else if (key == 'ClassType') {
                    if (value == 2) {
                        //set freqType to date with last class value
                        $('form#new-class [name="freqType"]').val('date').trigger('change').prop('required', true)
                        $('form#new-class [name="regularEndDate"]').val(data.LastClassDate).trigger('change').addClass('changed')
                    } else if (value == 1) {
                        $('form#new-class [name="freqType"]').val(0).trigger('change').prop('required', true)
                    }
                    if (value != 3) // ****** Temp block frequency edit for lessons that not single *******
                        $('form#new-class [name="ClassRepeat"]').closest('.form-group').addClass('d-none')

                } else if (key == 'ClassNameType') {
                    if (!elem.find(`option[value="${value}"]`).length) {
                        elem.append(
                            $(`<option value="${value}" data-color-code="${data.color}" selected>${data.text}</option>`))
                        elem.trigger('change')
                    } else elem.val(value).trigger('change')

                } else if (key == 'image') {
                    $('#avatar').attr('src', value)
                    $('form#new-class [name="image"]').val(value)
                    if (value) $('#js-delete-avatar').removeClass('d-none')
                } else if (['GuideId', 'ExtraGuideId'].includes(key)) {
                    guideName = (key == 'GuideId') ? data.GuideName : data.ExtraGuideName
                    const isExist = $(`form#new-class [name="${key}"][value="${value}"]`)
                    if (!isExist.length && value) {
                        elem.closest('.tab-pane')
                            .append($(`<input checked data-preview="${guideName}" value="${value}" name="${key}" class="d-none" type="radio"/>`))
                    } else
                        $(`form#new-class [name="${key}"][value="${value}"]`).prop('checked', true)
                } else if (key == 'purchaseLocation' && value == 0) {
                    elem.data('postChange', data.ShowClientNum)
                    if (data.ShowApp == 1)
                        elem.val(1).trigger('change')
                    if (data.ShowApp == 2)
                        elem.val(0).trigger('change')
                } else if (key == 'purchaseOptions') {
                    if (value == 0)
                        elem.val('membership').trigger('change')
                    if (value == 1)
                        elem.val('membership-cost').trigger('change')
                    else if (data.FreeClass == 2)
                        elem.val('free-register').trigger('change')
                } else if (elem.attr('type') == 'radio')
                    $(`form#new-class [name="${key}"][value="${value}"]`).prop('checked', true)
                else if (key == 'Remarks' && value) {
                    $('#RemarksNew').summernote("code", value)
                    $('#js-div-class-content').collapse('show')
                }  else if (key == 'tagId') {
                $(`[name="tag"]`).attr("data-tagId", value);
                } else if (elem.is('select')) {
                    if (key == 'TypeReminder' && value == 0) value = 1
                    elem.attr('required', false)
                    elem.addClass('custom-required')
                    elem.val(value).trigger('change')
                } else if (elem.length && value && value !== "0") {
                    elem.attr('required', false)
                    elem.addClass('custom-required')
                    elem.val(value);
                }
            }

            populateFields.class.setDuration(data.start_date, data.end_date);
            MeetingPopup.setTagsCategories(res);
            fieldEvents.classActions.init();
            $('.js-subpage-class-new #myTabContent').find('input[name="ClassName"]').val(data['ClassName'])
            if (data.Status != 1) {  // class not completed and color was not changed
                fieldEvents.classActions.setColor(data.color) //setting real color (override class type color)
            }
            if (data.FreeClass != 0)
                $('.js-cancel-law-field').prop('required', false)

        },
        setDuration: function (start_date, end_date) {
            const format = "YYYY-MM-DD HH:mm:ss"
            const duration = moment(end_date, format).diff(moment(start_date, format), 'minutes')
            $('form#new-class [name="duration"]').val(duration).trigger('change')
        },
        calcPeriod: function (originalDate, cancelDate, daysPeriod) {
            return moment(originalDate).diff(moment(cancelDate).subtract(daysPeriod, 'days'), 'hours')
        },
    },
    meeting: {
        populate: function (data, duplicate = false) {
            OpenClassPopup(null, 0, function () {
                // when edit - don't show this button
                $("#js-proceed-to-payment").addClass('d-none');

                fieldEvents.meetingActions.show()
                const $parent = $('form#new-meeting')
                let newHeader = lang('new_meeting')
                $('.js-add-treat-btn').closest('.js-treat-include-num').remove()

                if (!duplicate) {
                    newHeader = lang('edit_meeting')
                    $parent.attr('onsubmit', 'fieldEvents.meetingActions.edit(this, event)').addClass('js-edit')
                    $parent.append(`<input type="hidden" name="id" value="${data.id}" class="changed"/>`)
                    $parent.find('#js-note-container').remove()
                }

                $parent.find('.js-select-create-new')
                    .closest('div')
                    .empty()
                    .append(
                        `<p class="bsapp-fs-18 font-weight-bold m-0">${newHeader}</p>`
                    )

                if (data.repeat_type != 3)
                    $parent.append(`<input type="hidden" name="GroupNumber" value="${data.GroupNumber}" class="changed"/>`)
                if (data.customer)
                    populateFields.meeting.setClient(data.customer.id, data.customer.phone, data.customer.name)
                if (data.ownerId)
                    $parent.find(`[name="GuideId"][value="${data.ownerId}"]`).prop('checked', true)
                if (data.Floor)
                    $parent.find(`[name="Floor"][value="${data.Floor}"]`).prop('checked', true)
                if (data.ClassTypeId)
                    $parent.find(`.js-template-select`).first().val(data.ClassTypeId).trigger('change')
                if (data.regularEndDate)
                    $parent.find(`[name="regularEndDate"]`).val(data.regularEndDate).trigger('change').addClass('changed')
                if (!duplicate && data.repeat_type && data.period_value && data.period_unit)
                    populateFields.meeting.setFreqType(data.repeat_type, data.period_value, data.period_unit)
                if (data.start) {
                    $parent.find('.js-schedule-select')
                        .first().val(moment(data.start).format('HH:mm'))
                        .trigger('change')
                    $parent.find('.datepicker')
                        .datepicker('setDate', moment(data.start).format('YYYY-MM-DD'))
                        .trigger('change')
                }
                if (data.start && data.end)
                    $parent.find('.js-template-duration')
                        .last().val(moment(data.end).diff(moment(data.start), 'minutes'))
                        .trigger('change')
                if (data.price_total)
                    $parent.find(`.js-template-cost`).val(Math.trunc(data.price_total)).trigger('click')

                fieldEvents.meetingActions.init()
                $parent.find('input, textarea, select').on('change', function () {
                    $(this).addClass('changed')
                })
                fieldEvents.meetingActions.templateUpdate();

                // limitations for completed status
                if (data.status === '4' && !duplicate) {
                    // client change limitations
                    $('#js-client-tab-reset').hide();
                    $('#js-client-tab-link').siblings('div .cursor-pointer').addClass('disabled');

                    // repeat change limitations
                    $('#new-meeting .js-single-edit-effect').hide();
                    $('a[data-id="js-meeting-tab-date"]').closest('.js-div-tab-preview').find('.js-tab-sub-preview').hideFlex();

                    // template limitations
                    $('#new-meeting .js-select2-templates').siblings('span').addClass('disabled');
                    $('#new-meeting .js-template-duration').siblings('span').addClass('disabled');
                    $('#new-meeting .js-template-cost').parent().parent().addClass('disabled');

                    // charge with membership limitations
                    $('#new-meeting .js-treatment-container .js-charge-type-div').addClass('disabled');

                    // note about limited functionality
                    $('#js-div-tab-conclusion').before('<div id="meeting-completed-edit-note" class="d-flex bsapp-fs-14 mx-auto mt-auto px-5 mb-3 bg-gray-200 text-dark rounded">'
                        + lang('meeting_edit_note')
                        + '</div>');
                    $('#js-div-tab-conclusion').removeClass('mt-auto');
                }
            })
        },
        setClient: function (clientId, clientPhone, clientName) {
            $(MeetingPopup.parentElem).find(".js-client-is-new").hide();
            $(MeetingPopup.parentElem).find(".js-client-phone-valid").hide();
            MeetingPopup.parentElem.find('[name="IsNew"]').val(0);
            MeetingPopup.parentElem.find('#js-new-client-button').addClass('d-none');

            $(MeetingPopup.parentElem).find('input[name="ClientId"]').val(clientId).addClass('custom-required');
            $(".js-user-data-details #js-user-name").val(clientName).attr("readonly", true)
            $(".js-user-data-details #js-user-phone").val(clientPhone).attr("readonly", true)

            fieldEvents.meetingActions.checkCanUseMembershipAndFindMembership(clientId)
            MeetingPopup.userSearch.hideSearchField();
            $(this)
                .parents(".pointForm")
                .find(".show-field")
                .addClass("DisplayOn");
        },
        hideFreqFields: function () {
            $('form#new-meeting').find('#js-meeting-div-stopped').removeClass('d-none')
                .end().find('.js-times-option').remove()
                .end().find('[name="ClassRepeat"]').closest('.form-group').addClass('d-none')
        },
        setClassRepeat: function (periodValue, periodUnit) {
            let typeText = ''
            switch (periodUnit) {
                case '1':
                    typeText = 'day'
                    break;
                case '2':
                    typeText = 'week'
                    break;
                case '3':
                    typeText = 'month'
                    break;
            }
            $('form#new-meeting')
                .find('[name="ClassRepeat"]')
                .val(`${periodValue} ${typeText}`)
                .trigger('change')
        },
        setFreqType: function (repeat_type, period_value, period_unit) {
            switch (repeat_type) {
                case "1":
                    $('form#new-meeting [name="freqType"]').val(0).trigger('change')
                    populateFields.meeting.hideFreqFields()
                    populateFields.meeting.setClassRepeat(period_value, period_unit)
                    break;
                case "2":
                    $('form#new-meeting [name="freqType"]').val('date').trigger('change')
                    populateFields.meeting.hideFreqFields()
                    populateFields.meeting.setClassRepeat(period_value, period_unit)
                    break;
            }
        },
    }
}

const occupiedPopup = {
    parentElem: $('#js-occupied-modal'),
    show: function (data, redirect = null) {
        const $occupiedContainer = this.parentElem.find('.js-occupied-container')
        this.parentElem.modal('show')
        $occupiedContainer.html('')

        let li, span, ul, subItem, subItemSpan, div
        for (const item of data) {
            li = document.createElement('li')
            div = document.createElement('div')
            span = document.createElement('span')
            span.innerHTML = `${item.name}:`
            div.appendChild(span)
            for(const warning of item.warnings) {
                span = document.createElement('span')
                span.innerText = `-${warning.message}`
                span.classList.add('mis-10')
                div.classList.add('d-flex', 'flex-column')
                div.appendChild(span)
                if(warning.data) {
                    for (const lesson of warning.data) {
                        ul = document.createElement('ul')
                        subItem = document.createElement('li')
                        subItemSpan = document.createElement('span')
                        subItemSpan.innerHTML = lesson.start_date ? `${moment(lesson.start_date).format('DD/MM/YY HH:mm')}
                        -${moment(lesson.end_date).format('HH:mm')} ${lesson.ClassName}` : `${moment(lesson).format('DD/MM/YY')}`;
                        subItem.appendChild(subItemSpan)
                        ul.appendChild(subItem)
                        ul.classList.add('mb-2')
                        div.appendChild(ul)
                    }
                }
            }
            li.appendChild(div)
            $occupiedContainer.append(li)
        }

        this.parentElem.find('[onclick="occupiedPopup.proceed()"]').data('redirect', redirect ? 1 : 0)

    },
    showBlockEvent: function (warning) {
        const $occupiedContainer = this.parentElem.find('.js-occupied-container')
        this.parentElem.modal('show')
        $occupiedContainer.html('')

        let span, ul, subItem, subItemSpan, div
        div = document.createElement('div')
        span = document.createElement('span')

        span.innerText = `${warning.message}:`
        span.classList.add('mis-10')
        div.classList.add('d-flex', 'flex-column')
        div.appendChild(span)
        if (warning.data) {
            for (const lesson of warning.data) {
                ul = document.createElement('ul')
                subItem = document.createElement('li')
                subItemSpan = document.createElement('span')
                subItemSpan.innerHTML = `${moment(lesson.start_date).format('HH:mm')}-${moment(lesson.end_date).format('HH:mm')} ${lesson.ClassName}`;
                subItem.appendChild(subItemSpan)
                ul.appendChild(subItem)
                ul.classList.add('mb-2')
                div.appendChild(ul)
            }
        }
        $occupiedContainer.append(div)

        // change onclick function
        this.parentElem.find('[onclick="occupiedPopup.proceed()"]').attr("onclick","occupiedPopup.proceedBlockEvent()");
    },
    proceedBlockEvent: function () {
        this.parentElem.modal('hide')
        const $form = $('form#blockEventPopupForm')[0];

        const blockEventData = {};
        blockEventData['overrideLimitation'] = true;
        for (let i = 0; i < $form.length; i++) {
            const field = $form[i];
            const key = field.id;
            if (key == "") continue;
            switch (field.type) {
                case "checkbox":
                    blockEventData[key] = field.checked;
                    break;
                case "search":
                case "button":
                    // skip
                    break;
                default:
                    blockEventData[key] = field.value;
            }
        }

        // convert endTime to duration
        const diff = Math.abs(new Date(blockEventData['StartDate'] + " " + blockEventData['EndTime'])
            - new Date(blockEventData['StartDate'] + " " + blockEventData['StartTime']));
        blockEventData['duration'] = Math.floor((diff / 1000) / 60);

        if ($("#deleteBlockEvent").data('id') !== undefined) {
            blockEventData['CalendarId'] = $("#deleteBlockEvent").data('id');
        }

        // fill other required parameters
        blockEventData['Floor'] = $("#calendarFilters-location input").data("id");      // first space id
        blockEventData['ShowApp'] = 2;                                                  // don't show
        blockEventData['MaxClient'] = 0;
        blockEventData['color'] = '#E3E3E3';
        blockEventData['ClassNameType'] = 0;
        blockEventData['ClassRepeat'] = 0;

        const $parent = $('#js-block-event-popup');
        $parent.showModalLoader();

        $.ajax({
            url: 'ajax/SaveStudioDate.php',
            method: 'POST',
            data: {
                action: 'saveClass',
                data: blockEventData,
            },
            success: function (res) {
                $parent.hideModalLoader();

                if (res.status == 1) {
                    $parent.modal('hide');
                    GetCalendarData();

                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: res.message,
                    }, {
                        type: 'success',
                        z_index: '99999999',
                    });
                } else {
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('action_not_done'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            },
            error: function (res) {
                $parent.hideModalLoader();
                $.notify({
                    icon: 'fas fa-times-circle',
                    message: lang('action_not_done'),
                }, {
                    type: 'danger',
                    z_index: '99999999',
                });
            }
        })
    },
    proceed: function () {
        this.parentElem.modal('hide')
        const $form = $('form#new-meeting')
        if ($form.hasClass('js-edit')) {
            fieldEvents.meetingActions.edit(
                $form,
                null,
                1)
        } else {
            fieldEvents.meetingActions.submit(
                $form,
                null,
                1,
                this.parentElem.find('[onclick="occupiedPopup.proceed()"]').data('redirect'))
        }
    },
    abort: function () {
        this.parentElem.modal('hide')
        MeetingPopup.parentElem.hideModalLoader()
    }
}

$(document).on({
    'show.bs.modal': function () {
        let zIndex = 1040 + (10 * $('.modal.js-modal-no-close:visible').length);
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