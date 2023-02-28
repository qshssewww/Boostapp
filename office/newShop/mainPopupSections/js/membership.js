function emptyMembershipPopup(type) {
    $(".addMembershipLengthFlex").show();
    $(".membershipLengthFlex").addClass("membershipLenghtFLexHide");
    $("#membershipLength1")
            .parent()
            .removeClass("membershipLenghtFLexHide");
    $(`#isMembershipTypeNew${type}`).val("0");
    $(`#isMembershipTypeNew${type}`)
            .parents(".selectContainers")
            .find(".newLabel")
            .removeClass("labelDisplayOn");
    $("#hiddenIdInput" + type).val("");
    $("#membershipName" + type).val("");
    $("#membershipType" + type)
            .val($(`#membershipType${type} option:first`).val())
            .trigger("change");
    $("#shopCmpBranch" + type)
            .val($(`#shopCmpBranch${type} option:first`).val())
            .trigger("change");
    $("#membershipPrice" + type)
            .val("")
            .trigger("keyup");
    $("#taxInclude" + type).prop("checked", true);
    if (type == 1) {
        // $("#priceSelectOptions")
        //         .val("0")
        //         .trigger("change");
    } else {
        $("#ticketEntries" + type)
                .val("")
                .trigger("change");
    }
    $("#purchaseLimitPopupMembershipHiddenInput" + type).val('');
    $("#purchaseLimitPopupMembershipHiddenText" + type).html('');
    $("#membershipLength" + type).val("");
    // $("#membershipUnits" + type).val("3");
    $("#alertOnEnd" + type).prop("checked", false);
    $("#membershipAlertSettingsNumber" + type).val(3).trigger('change');
    $("#membershipAlertSettingsUnitType" + type).val(1).trigger('change');
    $("#membershipContent" + type).summernote('code', '');

    $(`#openMembershipAlertSettings${type} .plus`)
            .removeClass("hidden")
            .addClass("visible");
    $(".hiddenMembershipAlertSettings")
            .removeClass("visible")
            .addClass("hidden");

    $(`.hiddenPopupSection[data-id="${type}"] .openRegisterLine`).remove();
    $("#membershipStartSelect" + type)
            .val("1")
            .trigger("change");
    $("#lateRegisterDateInputMembership" + type)
            .val("")
            .trigger("change");
    $("#allowLateRegisterMembership" + type).prop("checked", false);
    $("#allowRelativeCheckboxMembership" + type).prop("checked", false);
    $("#membershipRelativeDiscount" + type).val("");
    $("#imgPlus" + type).html(
            `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName${type}"></div></div> `
            );
    let imgPath = $("#pageImgPath" + type);
    imgPath.val("");
    $('.removeImg').hide();
    // imgPath.prev().show();
    // if (tinymce.get("membershipContent" + type)) {
    //   tinymce.get("membershipContent" + type).setContent("");
    // }

    $(`#openTextarea${type} .plus`)
            .removeClass("hidden")
            .addClass("visible");
    $(".hiddenTextarea")
            .removeClass("visible")
            .addClass("hidden");
    if ($("#purchaseLimitPopupMembershipHidden" + type).is(":visible")) {
        console.log("visible");
        $("#purchaseLimitPopupMembershipHiddenClose" + type).click();
    }
    $("#allowBuyFromApp" + type)
            .val("0")
            .trigger("change");
    $(".membershipLengthDependent").hide();
}

$(document).on("click", ".alertOnEndLabel", function (e) {
    e.preventDefault();
    setAlertOnEndTrue($(this));
});

$(document).on("click", "#closeAlertOnEndFlex", function (e) {
    e.preventDefault();
    setAlertOnEndFalse($(this));
});

function setAlertOnEndTrue(triger) {
    let checkBox = triger
            .parents(".alertOnEndFlex")
            .find("input[id^=alertOnEnd]");
    checkBox.prop("checked", true);
    triger.siblings(".alertOnEndOpend").removeClass("closed");
    triger.hide();
}

function setAlertOnEndFalse(triger) {
    let checkBox = triger
            .parents(".alertOnEndFlex")
            .find("input[id^=alertOnEnd]");
    checkBox.prop("checked", false);
    triger
            .parents(".alertOnEndFlex")
            .find(".alertOnEndLabel")
            .show();
    triger.parents(".alertOnEndOpend").addClass("closed");
}

function fillMembershipPopupWithData(data) {
    emptyMembershipPopup();
    setUpdateText();
    $("#hiddenIdInput" + type).val(data.id);
    $("#membershipName" + type).val(data.ItemName);
    $("#membershipType" + type)
            .val(data.MemberShip)
            .trigger("change");
    $("#shopCmpBranch" + type)
            .val(data.Brands == "BA999" ? "-1" : data.Brands)
            .trigger("change");
    $("#membershipPrice" + type)
            .val(parseInt(data.ItemPrice))
            .trigger("keyup");
    $("#taxInclude" + type).prop("checked", data.Vat != "0");
    if (type == 1) {
        $("#priceSelectOptions")
                .val(data.Payment ? data.Payment : "1")
                .trigger("change");
    } else {
        $("#ticketEntries" + type)
                .val(data.BalanceClass)
                .trigger("change");
    }

    if (data.Vaild && data.Vaild != "0") {
        $(".membershipLengthDependent").show();
        $(".addMembershipLengthFlex").hide();
        $(".membershipLengthFlex").removeClass("membershipLenghtFLexHide");
        $("#membershipLength" + type).val(data.Vaild);
        $("#membershipUnits" + type).val(data.Vaild_Type).trigger("change");
        $("#alertOnEnd" + type).prop("checked", data.notificationAtEnd == "1");
        console.log(data.notificationAtEnd == "1");
        if ($("#alertOnEnd" + type).is(":checked")) {
            let triger = $("#alertOnEnd" + type)
                    .parents(".alertOnEndFlex")
                    .find(".alertOnEndLabel");
            setAlertOnEndTrue(triger);
        } else {
            let triger = $("#alertOnEnd" + type)
                    .parents(".alertOnEndFlex")
                    .find("#closeAlertOnEndFlex");
            setAlertOnEndFalse(triger);
        }
        if (data.NotificationDays > '0') {
            $(`#openMembershipAlertSettings${type} .plus`)
                    .removeClass("visible")
                    .addClass("hidden");
            $(".hiddenMembershipAlertSettings")
                    .removeClass("hidden")
                    .addClass("visible");
            let daysofNotification = data.NotificationDays;
            let alertUnit;
            let alertNumber;

            if (!(daysofNotification % 30)) {
                alertUnit = "3";
                alertNumber = daysofNotification / 30;
            } else if (!(daysofNotification % 7)) {
                alertUnit = "2";
                alertNumber = daysofNotification / 7;
            } else {
                alertUnit = "1";
                alertNumber = daysofNotification;
            }
            $("#membershipAlertSettingsNumber" + type).val(alertNumber).trigger('change');
            $("#membershipAlertSettingsUnitType" + type).val(alertUnit).trigger('change');
        }
    }
    if (data.registerLimits) {
        onSystematicRegisterLimitAdd(data.registerLimits);
    }

    $("#allowBuyFromApp" + type)
            .val(data.Display)
            .trigger("change");
    if (data.Display == "1") {
        $("#membershipStartSelect" + type)
                .val(data.membershipStartCount)
                .trigger("change");
        if (data.membershipStartCount == 4 && data.membershipStartDate) {
            try {
                $("#lateRegisterDateInputMembership" + type)
                    .val(new Date(data.membershipStartDate).format('dd/mm/yyyy'))
                    .trigger("change");
            } catch (e) {
                //format not valid
            }
        }
        $("#allowLateRegisterMembership" + type)
                .prop("checked", data.membershipAllowLateReg == "1")
                .trigger("change");
        $("#allowRelativeCheckboxMembership" + type)
                .prop("checked", data.membershipAllowRelativeDiscount == "1")
                .trigger("change");
        if (data.membershipAllowRelativeDiscount == 1)
            $("#membershipRelativeDiscount" + type).val(data.membershipRelativeDiscount)

        if (data.Image) {
            $("#ImgName" + type).append(
                    '<img class="shopImage" id="shopImage' +
                    type +
                    '" src="' +
                    data.Image +
                    '"/>'
                    );
            showHidden($("#imgPlus" + type), ".hiddenImg");
            $("#pageImgPath" + type).val(data.Image);
            $("#pageImgPath" + type)
                    .prev()
                    .show();

            // $("#imgPlus" + type).attr('data-item-id', data.id);
            // var time = function () {
            //     return'?' + new Date().getTime()
            // };

            // $('#itemModal').imgPicker({
            //     url: 'Server/upload_item.php',
            //     aspectRatio: 20 / 13,
            //     setSelect: [350, 200, 0, 0],
            //     deleteComplete: function () {
            //         $('#avatar').attr('src', '/office/assets/img/default.png');
            //         this.modal('hide');
            //     },
            //     loadComplete: function (image) {
            //         // Set #avatar image src
            //         // $('#avatar').attr('src', '/office/assets/img/default.png');
            //         // Set the image for re-crop
            //         this.setImage(image);
            //     },
            //     cropSuccess: function (image) {
            //         $('#avatar').attr('src', image.versions.pageImg.url + time());
            //         $('#pageImgPath' + type).val(image.versions.pageImg.url);
            //         this.modal('hide');
            //         if ($(".edit-avatar").hasClass("classImg")) {
            //             imgUpload();
            //         }
            //         $("#editImg, #deleteImp").hide();
            //     }
            // });
        }

        if (data.Content) {
            $(`#openTextarea${type} .plus`)
                    .removeClass("visible")
                    .addClass("hidden");
            $(".hiddenTextarea")
                    .removeClass("hidden")
                    .addClass("visible");
            $("#membershipContent" + type).summernote('code', data.Content);
        }

        if (data.purchaseLimits) {
            onSystematicPurchaseLimitAdd(data.purchaseLimits);
        }
    }
}

$(document).ready(function () {
    $(".membershipContent").summernote({
        height: 120,
        width: "100%",
        followingToolbar: false,
        dialogsInBody: true,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['ul', 'ol']]
        ],
        callbacks: {
            onPaste: function (e) {
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                document.execCommand('insertText', false, bufferText);
            }
        }
    });

    $(".addMembershipLengthFlex").click(function () {
        $(this).hide();
        $(".membershipLengthFlex").removeClass("membershipLenghtFLexHide");
    });
    $(".closeMembershipLengthFlex").click(function () {
        $(".addMembershipLengthFlex").show();
        $(".membershipLengthFlex").addClass("membershipLenghtFLexHide");
        $(this)
                .parent()
                .find(".membershipLength")
                .val(null)
                .trigger("change");
    });
    $("#membershipType1, #membershipType4, #membershipType5")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true
                    };
                },
                theme: "bsapp-dropdown"
            })
            .on("select2:select", function (e) {
                if (e.params.data.isNew) {
                    $(this)
                            .parents(".selectContainers")
                            .find(".newLabel")
                            .addClass("labelDisplayOn");
                    $(this)
                            .parents(".selectContainers")
                            .find("input[id^='isMembershipTypeNew']")
                            .val("1");
                } else {
                    $(this)
                            .parents(".selectContainers")
                            .find(".newLabel")
                            .removeClass("labelDisplayOn");
                    $(this)
                            .parents(".selectContainers")
                            .find("input[id^='isMembershipTypeNew']")
                            .val("0");
                }
            });

    $(document).on("keyup", ".membershipPrice", function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($("#select-type-secondary").val() == "1") {
            if ($(this).val() && $(this).val() != "") {
                parent.find(".priceSelect").show();
                $("#priceSelectOptions")
                        .val("1")
                        .trigger("change");
            } else {
                parent.find(".priceSelect").hide();
                // $("#priceSelectOptions")
                //         .val("0")
                //         .trigger("change");
            }
        } else {
            if ($(this).val() && $(this).val() != "") {
                parent.find(".tickets").show();
            } else {
                parent.find(".tickets").hide();
                //$("#priceSelectOptions").val("0").trigger("change");
            }
            if ($("#select-type-secondary").val() == "5") {
                $("#ticketEntries" + type).attr("max", 5);
            } else {
                $("#ticketEntries" + type).removeAttr("max");
            }
        }
    });
    function isLessThan5(jObj) {
        if (
                jObj.val() &&
                jObj.val() != "0" &&
                jObj.val() != null &&
                jObj.val() <= 5
                ) {
            jObj.css({"border-bottom": "1px solid black"});
            jObj.siblings(".errorMsg").hide(function () {
                $(this).remove();
            });
            return true;
        } else {
            jObj.val(null).trigger("change");
            jObj
                    .parent()
                    .find(".errorMsg")
                    .hide(function () {
                        $(this).remove();
                    });
            jObj.css({"border-bottom": "1px solid red"});
            jObj
                    .parent()
                    .append(
                            `<div class="errorMsg">${"הכנס ערך קטן או שווה ערך ל 5"}</div>`
                            );
            return false;
        }
    }
    $(".priceSelectOptions, .ticketEntries").change(function () {
        let elem = $(this).attr("id");
        let parent = $(this).closest(".hiddenPopupSection");

        if (
                $("#select-type-secondary").val() == "5" &&
                parseInt($(this).val()) > 5
                ) {
            isLessThan5($("#ticketEntries5"));
        }
        if (elem == "priceSelectOptions") {
            if ($(this).val() && $(this).val() != "") {
                if ($(this).val() == "0") {
                    parent.find(".priceSelectDependent").hide();
                    parent.find(".priceSelect1Dependent").hide();
                    $("#allowBuyFromApp" + type)
                            .val("0")
                            .trigger("change");
                    $("#membershipLength" + type)
                            .val(null)
                            .trigger("change");
                } else {
                    parent.find(".priceSelectDependent").show();
                    if ($(this).val() == "1" && elem == "priceSelectOptions") {
                        parent.find(".priceSelect1Dependent").show();
                    } else {
                        $("#membershipLength" + type)
                                .val(null)
                                .trigger("change");
                        parent.find(".priceSelect1Dependent").hide();
                    }
                }
            }
        } else {
            if ($(this).val() && $(this).val() != "") {
                $("#allowBuyFromApp" + type)
                        .val("0")
                        .trigger("change");
                parent.find(".priceSelectDependent ").show();
                parent.find(".priceSelect1Dependent").show();
                $("#membershipLength" + type)
                        .val(null)
                        .trigger("change");
            } else {
                $("#allowBuyFromApp" + type)
                        .val("0")
                        .trigger("change");
                parent.find(".priceSelectDependent ").hide();
                parent.find(".priceSelect1Dependent ").hide();
                $("#membershipLength" + type)
                        .val(null)
                        .trigger("change");
            }
        }
    });

    $(document).on("change", ".allowBuyFromApp", function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).val() == "0") {
            parent.find(".allowBuyDependent").hide();
            parent.find(".purchaseLimitPopupMembershipHidden").hide();
        } else {
            parent.find(".allowBuyDependent").show();
            if (
                    $("#membershipLength" + type).val() == "" ||
                    $("#membershipLength" + type).val() == undefined
                    ) {
                $("#membershipStartSelect" + type)
                        .parent()
                        .hide();
            }
            if ($("#purchaseLimitPopupMembershipHiddenText" + type).text() != "") {
                // console.log("show");
                parent.find(".purchaseLimitPopupMembershipHidden").show();
                parent.find("#purchaseLimitPopupMembership" + type).hide();
            }
        }
    });

    $(document).on("change", `.membershipLength, .allowBuyFromApp`, function () {
        if (
                $(`#membershipLength${type}`).val() &&
                $(`#membershipLength${type}`).val() != "" &&
                $(`#allowBuyFromApp${type}`).val() == "1"
                ) {
            $("#membershipStartSelect" + type)
                    .parent()
                    .show();
        } else {
            $("#membershipStartSelect" + type)
                    .parent()
                    .hide();
        }
    });

    $(document).on("change", ".membershipLength", function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).val() && $(this).val() != "") {
            $('.alertOnEndLabel').trigger('click');
            parent.find(".membershipLengthDependent").show();
        } else {
            parent.find(".membershipLengthDependent").hide();
        }
    });

    $(document).on("click", ".openMembershipAlertSettings", function (e) {
        e.stopPropagation();
        showHidden($(this), ".hiddenMembershipAlertSettings");
    });

    $(document).on("click", ".closeMembershipAlertSettings", function (e) {
        e.stopPropagation();
        hideHidden($(this), ".hiddenMembershipAlertSettings");
    });

    $(document).on("click", ".openTextarea", function (e) {
        e.stopPropagation();
        showHidden($(this), ".hiddenTextarea");
    });

    $(document).on("click", ".closeTextarea", function (e) {
        e.stopPropagation();
        hideHidden($(this), ".hiddenTextarea");
    });


    const selectedLanguage= $.cookie('boostapp_lang') ?? 'he';

    $("#lateRegisterDateInputMembership1").datepicker($.datepicker.regional[selectedLanguage])
    $("#lateRegisterDateInputMembership4").datepicker($.datepicker.regional[selectedLanguage])
    $("#lateRegisterDateInputMembership5").datepicker($.datepicker.regional[selectedLanguage])

    $(".membershipStartSelect").change(function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).val() == "4") {
            parent.find(".dateExpirationDependentMembership").show();
        } else {
            parent.find(".dateExpirationDependentMembership").hide();
            parent.find(".allowLateRegisterDependentMembership").hide();
            parent.find(".dateSelectedDependendMembership").hide();
            parent.find(".relativeDiscount").hide();
            $("#allowLateRegisterMembership" + type).prop("checked", false);
            $("#allowRelativeCheckboxMembership" + type).prop("checked", false);
            $("#lateRegisterDateInputMembership" + type).val("");
            $("#membershipRelativeDiscount" + type).val("");
        }
    });

    $(".lateRegisterDateInputMembership").change(function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).val() && $(this).val() != "") {
            parent.find(".dateSelectedDependendMembership").show();
        } else {
            parent.find(".dateSelectedDependendMembership").hide();
            $("#allowLateRegisterMembership" + type).prop("checked", false);
        }
    });

    $(".allowLateRegisterMembership").change(function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).prop("checked")) {
            parent.find(".allowLateRegisterDependentMembership").show();
        } else {
            parent.find(".allowLateRegisterDependentMembership").hide();
            $("#allowRelativeCheckboxMembership" + type).prop("checked", false);
            parent.find(".relativeDiscount").hide();
        }
    });
    $(".allowRelativeCheckboxMembership").change(function () {
        let parent = $(this).closest(".hiddenPopupSection");
        if ($(this).prop("checked")) {
            parent.find(".relativeDiscount").show();
        } else {
            parent.find(".relativeDiscount").hide();
        }
    });
});
