
let shopMaim = {
    select2MembershipType: function (selectElem) {
        let param = {
            tags: true,
            createTag: function (tag) {
                return {
                    id: tag.term,
                    text: tag.term,
                    isNew: true
                };
            },
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
                    $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('choose_membership_type') + '</div><div> </div> </div>');
                } else if (item.isNew) {
                    $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div> <div class="mx-2 badge badge-info badge-pill">' + lang('new') +'</div></div>');
                } else {
                    $item = $(`<div class="d-flex justify-content-between align-items-center"><div><span>  ${item.text} </span></div></div>`);
                }
                return $item;
            },
            minimumResultsForSearch: 0,
            language: $("html").attr("dir") == 'rtl' ? "he" : "en",
            theme: 'bsapp-dropdown bsapp-outline-gray-300'
        }
        $(selectElem).select2(param)
            .on('select2:open', function() {
            $('.select2-search__field').attr('maxlength', 60)
        })
    },

    changeDirection: function (elem) {
        const rtl_rx = /^\s*([$&+,:;=?@#|'<>.^*()%!-])*([0-9])*([a-zA-Z]+)/;
        elem.style.direction = rtl_rx.test(elem.value) ? 'ltr' : 'rtl';
        let characterCount = $(elem).val().length,
            current = $(elem).closest('#membership-club-information-section')
                .find('#current-character-amount')
        current.text(characterCount);
    },

    hideFlex: function (elem){
        $(elem).addClass('d-none').removeClass('d-flex');
    },

    showFlex: function (elem){
        $(elem).removeClass('d-none').addClass('d-flex');
    },
    
    switchTab: function (elem) {
        const tabName = $(elem).attr("data-target");
        //close this tab
        let currTab = $(elem).closest(".js-tab");
        const newOpenTab = $(`.js-tab[data-herf=${tabName}]`);
        if ($(newOpenTab).attr('data-depth') < $(currTab).attr('data-depth')) {
            $(currTab).removeClass('slideInStart').addClass('slideOutStart');
            setTimeout(function () {
                $(newOpenTab).removeClass('d-none slideInStart').addClass('d-flex');
                $(currTab).removeClass('d-flex slideOutStart')
                    .addClass('d-none slideInStart');
            }, 200);
        } else {
            $(newOpenTab).addClass('slideInStart d-flex').removeClass('d-none');
            $(currTab).removeClass('d-flex slideInStart').addClass('d-none');
            setTimeout(function () {
                $(currTab).removeClass('d-flex slideInStart').addClass('d-none');
            }, 200);
        }
    },

    //Error checking, if detected shows the cause and return false
    errorChecking: function (responseStatus) {
        if (responseStatus) {
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

    //todo after fix variable "type" need fix this function
    imgUpload: function (){
        let imgPath = $("#pageImgPath" + type);
        let img = imgPath.val();
        imgPath.prev().show();
        if(type) {
            $("#ImgName" + type).html('<img class="shopImage" id="shopImage' + type + '" src="' + img + '"/>')
            showHidden($("#imgPlus" + type), ".hiddenImg");
        } else {
            const addPicture = $('.app-view-settings #add-picture-section');
            $(addPicture).find('#selected-image').html(
                '<img class="w-100 h-100" style="object-fit:cover;object-position:center;border-radius:8px;" id="shopImage" src="' + img + '"/>'
            );
            this.hideFlex($(addPicture).find('.add-picture-btn'));
            $(addPicture).find('.image-section').removeClass('d-none');
        }
    },

}
//popup logic
var type = 1;
function checkIfArrayIsUnique(myArray) {
    return myArray.length === new Set(myArray).size;
}

function setUpdateText() {
    $('#generalPopupTitleMaim').text('עריכת');
    $('#mainShopPopupButtonSave').text("עדכן");
}
function setInsertText() {
    $('#generalPopupTitleMaim').text('הקמת');
    $('#mainShopPopupButtonSave').text("שמור");
}
function openPopup(popupId) {
    $(".popupWrapper").removeClass("popupDisplayOn");
    $(`#${popupId}`).addClass("popupDisplayOn");
    $('body').addClass("overflow-hidden");
    $(`#${popupId} .popupContainer`).addClass("scaleUp");
    if (popupId == "mainShopPopup") {
        $(`.hiddenPopupSection[data-id="${$('#select-type-secondary').val()}"]`).show();
    }
}
//popup logic
function closePopup(popupId) {
    $(`#${popupId}`).removeClass("popupDisplayOn");
    $(`#${popupId} .popupContainer`).removeClass("scaleUp");
    $('body').removeClass("overflow-hidden");
    if (popupId == "mainShopPopup") {
        $(".hiddenPopupSection").hide();
    }
    if (popupId == "InventoryPopup") {
        $("#InventoryTable tbody").children().remove()
    }
}
function showHidden(_this, _class, _find = false) {
    var current = _find ? _this.find(_find) : _this.find(".plus");
    $(_class).addClass("visible");
    $(_class).removeClass("hidden");
    current.addClass("hidden");
}
function hideHidden(_this, _class, _find = false) {
    event.stopPropagation();
    var current = _find
            ? _this.parent().parent().find(_find)
            : _this.parent().parent().find(".plus");
    $(_class).removeClass("visible");
    $(_class).addClass("hidden");
    current.removeClass("hidden");
    current.addClass("visible");
}
function hideHiddenImg(_this, _class, _find = false) {
    event.stopPropagation();
    var current = _find
            ? _this.parent().find(_find)
            : _this.parent().find(".plus");
    $(_class).removeClass("visible");
    $(_class).addClass("hidden");
    current.removeClass("hidden");
    current.addClass("visible");
}
function removeDiv(_this, _class, _find = false) {
    event.stopPropagation();
    $(_class).remove();
}
function displayError(elem, msg) {
    if (!elem.parent().find('.inputError').length) {
        elem.addClass("redBorder");
        elem.parent().append('<label style="color: red" class="inputError">' + msg + '</label>');
        $('html, body').animate({
            scrollTop: elem.offset().top - 100
        }, 1000);
        elem.change(function () {
            $(this).removeClass("redBorder");
            $(this).parent().find('.inputError').remove();
        })
    }
}

$(document).ready(function () {
    //Translation of the datepicker
    $.datepicker.regional['he'] = {
        closeText: 'סגור',
        prevText: '',
        nextText: '',
        currentText: 'היום',
        monthNames: ['ינואר','פברואר','מרץ','אפריל','מאי','יוני',
            'יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],
        monthNamesShort: ['1','2','3','4','5','6',
            '7','8','9','10','11','12'],
        dayNames: ['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],
        dayNamesShort: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
        dayNamesMin: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
        weekHeader: 'Wk',
        dateFormat: 'dd/mm/yy',
        firstDay: 0,
        isRTL: true,
        showMonthAfterYear: false,
        yearSuffix: ''};

    //popup logic
    $(".toggleOpenPopup").click(function () {
        let popupId = $(this).attr("data-target");
        openPopup(popupId);
    });
    //popup logic
    // $(".toggleClosePopup").click(function () {
    //   let popupId = $(this).attr("data-target");
    //   let parentPopupId = $(this).attr("data-parent");
    //   closePopup(popupId);
    //   if (parentPopupId) {
    //     openPopup(parentPopupId);
    //   }
    // });
    $(document).on('click', '.toggleClosePopup', function () {

        let popupId = $(this).attr("data-target");
        let parentPopupId = $(this).attr("data-parent");
        closePopup(popupId);
        if (parentPopupId) {
            openPopup(parentPopupId);
        }
    })
    $(".typeButtons .btn").click(function () {
        $(".typeButtons .btn.current-btn").removeClass("current-btn");
        $(this).addClass("current-btn");
        $(".shop-page").addClass("hiddenTable");
        $(`.tableNumber${$(this).attr("data-value")}`).removeClass("hiddenTable");
        //fix weird scroll bug that shrinks thead
        $(`.tableNumber${$(this).attr("data-value")}`)
                .find("table")
                .DataTable()
                .columns.adjust()
                .draw();
    });
    // $(".removeImg").on("click", function (e) {
    //   
    //   hideHidden($(this), ".hiddenImg");
    //   $("#pageImgPath" + type).val("");
    //   $("#shopImage" + type).remove();
    // });

    $(document).on("click", ".removeImg", function (e) {
        hideHiddenImg($(this), ".hiddenImg");
        $(this).hide();
        $("#pageImgPath" + type).val("");
        $("#shopImage" + type).remove();
    });
    // openPopup('purchaseLimitPopup')
    // openPopup('registerLimitPopup')

    $(document).on('click', '.copyToClipboard', function (e) {
        try {
            let link = $(this).attr('data-link');
            let input = document.createElement('textarea');
            input.classList.add('js-remove-copy-link-elm');
            input.value = link;
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            // document.body.removeChild(input);
            // $('.alertush').addClass('alertush-active');
            // setTimeout(() => {
            //     $('.alertush').removeClass('alertush-active');
            // }, 1000)
            $(this).attr('title', 'Copied!');
            $(this).tooltip('show');
            $(this).removeClass("fa-paste").addClass("fa-check text-success");
            $('.js-remove-copy-link-elm').remove();
            setTimeout(() => {
                $(this).removeClass("fa-check text-success").addClass("fa-paste");
                $(this).tooltip('dispose');
                $(this).attr('title', '');
            }, 2000)

        } catch (err) {

        }
    });

    $("body").on("click", ".js-select2-selection__clear", function (e) {
        e.preventDefault();
        $(this).parents(".select2-selection__rendered").find(".select2-selection__clear").trigger("mousedown");
    })
});
