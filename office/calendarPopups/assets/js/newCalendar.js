function setEmptyMainCalendarPopup() {
    $("#calendarAndApp").click();
    $("#justCalendar").click();
    $("#mainPopupIsEditId").val("");
    $("#mainPopupIsEditGroup").val("");
    $("#calendarPopupClassSelect")
            .val($("#calendarPopupClassSelect option:first").val())
            .trigger("change");
    $("#isNewClass").val("0");
    let color = $("#calendarPopupClassSelect")
            .children("option:selected")
            .attr("data-color");
    $(".colorGridSelect .selectedColor").css("background-color", color);
    $("#selectedColor").val(color);
    $("#calendarPopupLocationSelect")
            .val($("#calendarPopupLocationSelect option:first").val())
            .trigger("change");
    $("#isNewLocation").val("0");
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    today = dd + "/" + mm + "/" + yyyy;
    $("#calendarPopupDateSelect").val(today);
    $("#calendarPopupTimeSelectFrom").val("");
    $("#calendarPopupTimeSelectTo").val("");
    $("#dateTextOutput").text("");

    $("#maxNumberOfAtendees").val("");
    $("#limitStandbyList").val("");
    $("#allowAsStandBy")
            .val("1")
            .trigger("change");
    $("#selectedTrainer1")
            .val($("#selectedTrainer1 option:first").val())
            .trigger("change");

    if ($("#selectedTrainer2").length) {
        $(".shallBeDuplicated")
                .last()
                .hide(0, function () {
                    $(this).remove();
                });
        $("#addTrainer").show(0);
    }
    $("#newClassDurationNumber").val(60);
    $("#newClassDurationUnitType").val(1);
    $(".hiddenReminder")
            .addClass("hidden")
            .removeClass("visible");
    $("#openReminder .plus")
            .addClass("visible")
            .removeClass("hidden");
    $("#prodcastLink").val("");
    $("#broadCastNum").val("60");
    $("#broadCastType").val(1);
    $("#zoomMeetingId").val(null);
    $("#zoomMeetingPassword").val(null);
    $("#broadCastReminderType").val("3");
    $("#prodcastOptions")
            .val(null)
            .trigger("change");
    $(".hiddenSimpleVid")
            .addClass("hidden")
            .removeClass("visible");
    $(".hiddenZoomVid")
            .addClass("hidden")
            .removeClass("visible");
    $("#openSimpleVid .select2-container")
            .addClass("visible")
            .removeClass("hidden");
    $("#contentShow").val("0");
    $("#classContent").summernote('code', "");
    $(".hiddenTextarea")
            .addClass("hidden")
            .removeClass("visible");
    $("#openTextarea .plus")
            .addClass("visible")
            .removeClass("hidden");
    $(".placeClientItem").remove();

    $("#minimumAtendeesAmount").val("60");
    $("#minimumAtendeesCheckAmount").val("3");
    $("#minimumAtendeesCheckType").val("ימים");
    $(".hiddenMinimum")
            .addClass("hidden")
            .removeClass("visible");
    $("#openMinimum .plus")
            .addClass("visible")
            .removeClass("hidden");

    $("#purchaseAmount").val("35");
    $("#purchaseLocation").val("app");
    $(".hiddenPurchaseOptions")
            .addClass("hidden")
            .removeClass("visible");
    $("#openPurchaseOptions .plus")
            .addClass("visible")
            .removeClass("hidden");

    $(".closeLimitationRow").each(function () {
        $(this).trigger("click");
    });

    $("#devicesInput")
            .val(null)
            .trigger("change");
    $(".hiddenDevices")
            .addClass("hidden")
            .removeClass("visible");
    $("#openDevices .plus")
            .addClass("visible")
            .removeClass("hidden");

    $(".closeSignTimingRow").each(function () {
        $(this).trigger("click");
    });

    hideHidden($("#removeImg"), ".hiddenImg");
    $("#pageImgPath").val("");
    $(".ImgName").text("");

    $("#freeRegister")
            .prop("checked", false)
            .trigger("change");

    $("#showMore").attr("data-toggle", "0");
    $("#showMore").html("הצג אפשרויות נוספות");
    $(".showMoreChangeable").addClass("hideShowMoreChangeable");

    $("#checkbox1").prop("checked", false);
    $("#checkbox2").prop("checked", false);
    $("#checkbox3").prop("checked", false);
    $(".HIDEIFEDIT").removeClass("hiddenImportant");
}

function sendData(data, callback) {
    showLoader();
    //debugger;
    data["fun"] = "SetNewCalendarClass";
    $.ajax({
        url: "ajax/CreateClass.php",
        type: "post",
        data: data,
        dataType: "json",
        // contentType: "application/json",
        success: function (response) {
            hideLoader();
            if (response.length) {
                callback(response);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            hideLoader();
            throw errorThrown;
        }
    });
}
function imgUpload() {
    let img = $("#pageImgPath").val();
    let image = img.substr(img.lastIndexOf("/") + 1);
    $(".ImgName").text(image);
    showHidden($("#imagePlus"), ".hiddenImg");
}
function makeid(length) {
    var result = "";
    var characters =
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - (x % 16)) / 16] + hexDigits[x % 16];
}
var hexDigits = new Array(
        "0",
        "1",
        "2",
        "3",
        "4",
        "5",
        "6",
        "7",
        "8",
        "9",
        "a",
        "b",
        "c",
        "d",
        "e",
        "f"
        );

function openPopup(popupId) {
    $(".popupWrapper").removeClass("popupDisplayOn");
    $(`#${popupId}`).addClass("popupDisplayOn");
    $(`#${popupId} .popupContainer`).addClass("scaleUp");
}
function closePopup(popupId) {
    $(`#${popupId}`).removeClass("popupDisplayOn");
    $(`#${popupId} .popupContainer`).removeClass("scaleUp");
}

function duplicateArea(_class, _this) {
    _this.fadeOut();
    var area = $("." + _class)
            .first()
            .clone();
    area.find("select").attr("id", "selectedTrainer2");
    area.append($('<div class="text-danger mis-9" id="removeTrainer"><i class="fas fa-do-not-enter"></i></div>'));
    area
            .insertBefore(_this)
            .hide()
            .toggle("slide");
    $("#removeTrainer").on("click", function () {
        $(".shallBeDuplicated")
                .last()
                .hide("slow", function () {
                    $(this).remove();
                });
        $("#addTrainer").show("slow");
    });
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
            ? _this
            .parent()
            .parent()
            .find(_find)
            : _this
            .parent()
            .parent()
            .find(".plus");
    $(_class).removeClass("visible");
    $(_class).addClass("hidden");
    current.removeClass("hidden");
    current.addClass("visible");
}

$(document).on("change", "#maxNumberOfAtendees", function (event) {
    if ($(this).val() < 1) {
        $(this).val(1);
    }
});

(function (factory) {
    if (typeof define === "function" && define.amd) {
        // AMD. Register as an anonymous module.
        define(["../widgets/datepicker"], factory);
    } else {
        // Browser globals
        factory(jQuery.datepicker);
    }
})(function (datepicker) {
    datepicker.regional.he = {
        closeText: "סגור",
        prevText: "&#x3C;הקודם",
        nextText: "הבא&#x3E;",
        currentText: "היום",
        monthNames: [
            "ינואר",
            "פברואר",
            "מרץ",
            "אפריל",
            "מאי",
            "יוני",
            "יולי",
            "אוגוסט",
            "ספטמבר",
            "אוקטובר",
            "נובמבר",
            "דצמבר"
        ],
        monthNamesShort: [
            "ינו",
            "פבר",
            "מרץ",
            "אפר",
            "מאי",
            "יוני",
            "יולי",
            "אוג",
            "ספט",
            "אוק",
            "נוב",
            "דצמ"
        ],
        dayNames: ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת"],
        dayNamesShort: ["א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת"],
        dayNamesMin: ["א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת"],
        weekHeader: "Wk",
        dateFormat: "dd/mm/yy",
        firstDay: 0,
        isRTL: true,
        showMonthAfterYear: false,
        yearSuffix: ""
    };
    datepicker.setDefaults(datepicker.regional.he);

    return datepicker.regional.he;
});

$(document).ready(function () {
    $('#classContent').summernote({
        height: 120,
        followToolbar: false
    });
    //OPEN CLOSE POPUP FUNCTIONALLITY
    $(".toggleOpenPopup").click(function () {
        let popupId = $(this).attr("data-target");
        openPopup(popupId);
    });
    $(".toggleClosePopup").click(function () {
        let popupId = $(this).attr("data-target");
        let parentPopupId = $(this).attr("data-parent");
        closePopup(popupId);
        if (parentPopupId) {
            openPopup(parentPopupId);
        }
    });

    //add red color on click top buttons
    $(".typeButtons .btn").click(function () {
        $(".typeButtons .btn").removeClass("current-btn");
        $(this).addClass("current-btn");
        //hidden input for send to php reasons
        $("#classFormType").val($(this).attr("data-value"));

        //show or hide content based on class
        if ($(this).attr("data-value") == 1) {
            $("#mainPopup").addClass("justCalendarPopup");
            $("#mainPopup").removeClass("calendarAndAppPopup");
            //change at once to null
            $("#repetitionInput1")
                    .val("once")
                    .trigger("change");
            $("#cancelationInput1")
                    .val("no")
                    .trigger("change");
        } else {
            $("#mainPopup").addClass("calendarAndAppPopup");
            $("#mainPopup").removeClass("justCalendarPopup");
            if ($("#repetitionInput2 option").length > 2) {
                $("#repetitionInput2")
                        .val($("#repetitionInput2 option:eq(1)").val())
                        .trigger("change");
            } else {
                $("#cancelationInput2")
                        .val("no")
                        .trigger("change");
            }
            if ($("#cancelationInput2 option").length > 3) {
                $("#cancelationInput2")
                        .val($("#cancelationInput2 option:eq(2)").val())
                        .trigger("change");
            } else {
                $("#repetitionInput2")
                        .val("once")
                        .trigger("change");
            }
        }
    });

    $("#prodcastOptions").select2({
        minimumResultsForSearch: -1, 
        dropdownParent: $("#mainPopup"),
    });

    $("#prodcastOptions").on("select2:selecting", function (e) {
        hideHidden($("#closeSimpleVid"), ".hiddenSimpleVid ", ".select2");
        hideHidden($("#closeZoomVid"), ".hiddenZoomVid ", ".select2");
        if (e.params.args.data.id == "openZoom") {
            // openPopup("zoomPopup");
            showHidden($("#openSimpleVid"), ".hiddenZoomVid", ".select2");
        }
        if (e.params.args.data.id == "openVideoLink") {
            showHidden($("#openSimpleVid"), ".hiddenSimpleVid", ".select2");
        }
    });
    function checkIfArrayIsUnique(myArray) {
        return myArray.length === new Set(myArray).size;
    }
    //trigger to save to db
    $("#mainPopupButtonSave").click(function () {
        if ($("#classFormType").val() == 2) {
            if (
                    $("#maxNumberOfAtendees").val() == "" ||
                    !$("#maxNumberOfAtendees").val()
                    ) {
                Swal.fire("", "מקסימום נרדמים לשיעור הינו שדה חובה", "error");
                return;
            }
        }
        // if (
        //   $("#calendarPopupDateSelect").val() == "" ||
        //   !$("#calendarPopupDateSelect").val() ||
        //   $("#calendarPopupTimeSelectFrom").val() == "" ||
        //   !$("#calendarPopupTimeSelectFrom").val() ||
        //   $("#calendarPopupTimeSelectTo").val() == "" ||
        //   !$("#calendarPopupTimeSelectTo").val()
        // ) {
        //   Swal.fire("", "שיעור,מיקום,תאריך ושעה הינם שדות חובה", "error");
        //   return;
        // }
        if (
                $("#selectedTrainer2").length &&
                $("#selectedTrainer1").val() == $("#selectedTrainer2").val()
                ) {
            Swal.fire("", "אסור למלא אותו מאמן פעמיים", "error");
            return;
        }

        let traineeIds = [];
        $(".clientName").each(function () {
            traineeIds.push($(this).val());
        });
        if (!checkIfArrayIsUnique(traineeIds)) {
            Swal.fire("", "אסור למלא אותו מתאמן פעמיים", "error");
            return;
        }

        let regexTrainees = false;
        var regex = /^0(5[^7]|[2-4]|[8-9]|7[0-9])[0-9]{7}$/;
        $(".clientCell").each(function () {
            if (
                    !regex.test($(this).val()) &&
                    $(this)
                    .parent()
                    .find(".isUserNew")
                    .val() == "1"
                    ) {
                regexTrainees = true;
            }
        });
        if (regexTrainees) {
            Swal.fire("", "יש למלא מספרי טלפון תקינים", "error");
            return;
        }
        const data = getDataForPostSend();
        console.log(data);
        // return
        sendData(data, function (res) {
            console.log(res);
        });
        console.log(data);
        closePopup("mainPopup");
    });
    function getDataForPostSend() {
        let data = {};
        let RepetitionInput;
        let CancelationInput;
        data.isEdit = $("#mainPopupIsEditId").val();
        if (data.isEdit && data.isEdit != "") {
            data.GroupNumber = $("#mainPopupIsEditGroup").val();
            data.editType = $("input[name='type']:checked").val();
            if (data.editType == "byDays") {
                data.editDays = getSelectedDaysSmallEditPopup();
            }
        }
        data.justCalendar = $("#classFormType").val() == 1;
        data.calendarAndApp = $("#classFormType").val() == 2;
        data.type = $("#classFormType").val();
        if (data.justCalendar) {
            RepetitionInput = $("#repetitionInput1");
            CancelationInput = $("#cancelationInput1");
            let trainees = [];
            $(".placeClientItem").each(function () {
                let trainee = {};
                trainee.isNew = $(this)
                        .find(".isUserNew")
                        .val();
                if (trainee.isNew == "1") {
                    trainee.name = $(this)
                            .find(".clientName")
                            .val();
                    trainee.phone = $(this)
                            .find(".clientCell")
                            .val();
                } else {
                    trainee.id = $(this)
                            .find(".clientCell")
                            .val();
                }
                trainee.dontCharge =
                        $(this)
                        .find(".clientCharge")
                        .val() == "none";
                trainee.chargeAmount = $(this).find(".howMuchToCharge").length
                        ? $(this)
                        .find(".howMuchToCharge")
                        .val()
                        : null;
                trainee.chargeMembership =
                        trainee.dontCharge == false && trainee.chargeAmount == null
                        ? $(this)
                        .find(".clientCharge")
                        .val()
                        : null;
                trainees.push(trainee);
            });
            data.trainees = trainees;
        }
        if (data.calendarAndApp) {
            RepetitionInput = $("#repetitionInput2");
            CancelationInput = $("#cancelationInput2");
            data.maxAtendees = $("#maxNumberOfAtendees").val();
            data.allowWaitingList =
                    $("#allowAsStandBy").val() == 1 || $("#allowAsStandBy").val() == 3;
            data.waitingListAmount =
                    $("#allowAsStandBy").val() == 3 ? $("#limitStandbyList").val() : null;
            data.showAmount = $("#checkbox1").prop("checked");
            data.showImage = $("#checkbox2").prop("checked");
            data.showWaitingListLocation = $("#checkbox3").prop("checked");
            data.minimumAtendees = $(".hiddenMinimum").hasClass("visible");
            if (data.minimumAtendees) {
                data.minimumAtendeesAmount = $("#minimumAtendeesAmount").val();
                data.minimumAtendeesCheckAmount = $(
                        "#minimumAtendeesCheckAmount"
                        ).val();
                data.minimumAtendeesCheckType = $("#minimumAtendeesCheckType").val();
            }
            data.purchaseOptions = $(".hiddenPurchaseOptions").hasClass("visible");
            if (data.purchaseOptions) {
                data.purchaseAmount = $("#purchaseAmount").val();
                data.purchaseLocation = $("#purchaseLocation").val();
            }
            if (
                    $("#pageImgPath").val() != "" &&
                    $("#pageImgPath").val() != undefined
                    ) {
                data.image = $("#pageImgPath").val();
            }
            let registerLimitations = [];
            $(".placeLimitationItem").each(function () {
                if (
                        $(this)
                        .find(".limitTriger")
                        .val() != "0"
                        ) {
                    let limitation = {};
                    limitation.type = $(this)
                            .find(".limitTriger")
                            .val();
                    if (
                            $(this)
                            .find(".limitTriger")
                            .val() == "age"
                            ) {
                        limitation.ageLimitationType = $(this)
                                .find(".ageLimitOperator")
                                .val();
                        limitation.limitationNumber = $(this)
                                .find("#ageLimit1")
                                .val();
                        if (limitation.ageLimitationType == "3") {
                            limitation.limitationSecondaryNumber = $(this)
                                    .find("#ageLimit2")
                                    .val();
                        }
                    }
                    if (
                            $(this)
                            .find(".limitTriger")
                            .val() == "degree"
                            ) {
                        limitation.ranks = $(this)
                                .find(".degreeInput")
                                .val();
                    }
                    if (
                            $(this)
                            .find(".limitTriger")
                            .val() == "type"
                            ) {
                        limitation.memberships = $(this)
                                .find(".mebershipInput")
                                .val();
                    }
                    if (
                            $(this)
                            .find(".limitTriger")
                            .val() == "gender"
                            ) {
                        limitation.gender = $(this)
                                .find(".genderInput")
                                .val();
                    }
                    registerLimitations.push(limitation);
                }
            });
            data.registerLimitations = registerLimitations;
            data.hasDevicesSettings = $(".hiddenDevices").hasClass("visible");
            if (data.hasDevicesSettings) {
                data.deviceSettings = $("#devicesInput").val();
            }
            let timingSettings = [];
            $(".signTimingItem").each(function () {
                let setting = {};
                setting.type = $(this)
                        .find(".limitationType")
                        .val();
                setting.amount = $(this)
                        .find(".timingNumber")
                        .val();
                setting.aountUnits = $(this)
                        .find(".timingUnit")
                        .val();
                timingSettings.push(setting);
            });
            data.timingSettings = timingSettings;
            data.freeRegister = $("#freeRegister").prop("checked");
            //still needs to add image
        }
        data.isNewClass = $("#isNewClass").val() == 1;
        data.isNewLocation = $("#isNewLocation").val() == 1;
        if (data.isNewClass) {
            let memberships = [];
            $(".cards-check input[type=checkbox]:checked")
                    .not("#selectAll")
                    .each(function () {
                        memberships.push($(this).val());
                    });
            data.class = {
                name: $("#calendarPopupClassSelect").val(),
                color: rgb2hex($(".selectedColor").css("background-color")),
                duration: $("#newClassDurationNumber").val(),
                durationType: $("#newClassDurationUnitType").val(),
                memberships: memberships,
                content: $("#classContent").summernote('code')
            };
        } else {
            data.class = $("#calendarPopupClassSelect").val();
        }
        if (data.isNewLocation) {
            data.location = {
                name: $("#calendarPopupLocationSelect").val(),
                brand: $("input[name='brand']:checked").val()
            };
        } else {
            data.location = $("#calendarPopupLocationSelect").val();
        }
        data.date = $("#calendarPopupDateSelect").val();
        data.timeFrom = $("#calendarPopupTimeSelectFrom").val();
        data.timeTo = $("#calendarPopupTimeSelectTo").val();
        data.duration = $("#calendarPopupTimeDurtaion").val();
        data.color = rgb2hex($(".selectedColor").css("background-color"));
        data.trainer1id = $("#selectedTrainer1").val();
        data.trainer1name = $("#selectedTrainer1")
                .find("option:selected")
                .text();
        data.trainer2id = $("#selectedTrainer2").val()
                ? $("#selectedTrainer2").val()
                : null;
        data.trainer2name = data.trainer2id
                ? $("#selectedTrainer2")
                .find("option:selected")
                .text()
                : null;

        if (RepetitionInput.val() == "once") {
            data.frequency = RepetitionInput.val();
        } else {
            let selectedDays = [];
            $(".dayLiSelected, .dayLiLocked").each(function () {
                selectedDays.push($(this).val());
            });
            data.frequency = {
                systematic: $("#datesIsSystematic").val() == "true",
                type: $("#classFormType").val(),
                frequencyNumber: $("#frequencyNumber").val(),
                frequencyType: $("#frequencyTypeOfUnit").val(),
                selectedDays: selectedDays,
                endType: $("input[name='end']:checked").val(),
                endDate: $("#ftime").val(),
                endRepeats: $("#howManyRepeatsNumber").val(),
                name: $("#repetitionTableName").val()
            };
            if (data.frequency.systematic == true) {
                data.frequency.id = $("#repetitionTableId").val()
                        ? $("#repetitionTableId").val()
                        : null;
            }
            data.dates = $("#datesArrayValues")
                    .val()
                    .split(",");
        }
        if (CancelationInput.val() == "no" || CancelationInput.val() == "free") {
            data.cancelation = CancelationInput.val();
        } else {
            data.cancelation = JSON.parse($("#cancelDataInput").val());
        }
        data.reminderBool = $(".hiddenReminder").hasClass("visible");
        if (data.reminderBool) {
            data.reminderType = $("#newClassDurationUnitType").val();
            data.remiderTime = $("#newClassDurationNumber").val();
        }
        data.contentBool = $(".hiddenTextarea").hasClass("visible");
        if (data.contentBool) {
            data.content = $("#classContent").summernote('code');
            data.contentShow = $("#contentShow").val();
        }
        let broadcastType = $("#prodcastOptions").val();
        data.is_zoom_class = 0;
        if (broadcastType == "openZoom") {
            data.is_zoom_class = 1;
            data.zoomMeetingId = $("#zoomMeetingId").val();
            data.zoomPassword = $("#zoomMeetingPassword").val();
            // data.broadcastNumber = $("#broadCastNum").val();
            // data.broadcastType = $("#broadCastType").val();
            // data.broadcastReminderType = $("#broadCastReminderType").val();
        }
        if (broadcastType == "openVideoLink") {
            data.broadcastLink = $("#prodcastLink").val();
            data.broadcastNumber = $("#broadCastNum").val();
            data.broadcastType = $("#broadCastType").val();
            data.broadcastReminderType = $("#broadCastReminderType").val();
        }
        return data;
    }
    $(document).on("change", ".limitTriger", function () {
        deleteMultipleLimits();
    });
    function deleteMultipleLimits() {
        $(".limitTriger option").show();
        $(".limitTriger").each(function () {
            if ($(this).val() != "0") {
                $(".limitTriger")
                        .not($(this))
                        .find(`option[value="${$(this).val()}"]`)
                        .hide();
            }
        });
    }

    $("#selectAll").change(function () {
        let val, text;
        if ($(this).prop("checked")) {
            val = true;
            text = "נקה הכל";
        } else {
            val = false;
            text = "סמן הכל";
        }
        $(".cards-check input[type=checkbox]")
                .not("#selectAll")
                .prop("checked", val);
        $("#selectAllText").html(text);
    });
    //setup select 2s
    $("#calendarPopupLocationSelect, #calendarPopupClassSelect").select2({
        tags: true,
        dropdownParent: $("#mainPopup"),
        createTag: function (tag) {
            return {
                id: tag.term,
                text: tag.term,
                isNew: true
            };
        }
    });
    $("#calendarPopupLocationSelect").on("select2:selecting", function (e) {
        if (e.params.args.data.isNew) {
            $("#calendarPopupLocationSelect")
                    .parents(".selectContainersHalf")
                    .find(".newLabel")
                    .addClass("labelDisplayOn");
            $("#isNewLocation").val("1");
            if ($("#calendarPopupLocationSelect").attr("data-openpopup") == "1") {
                openPopup("createNewCalendar");
            }
        } else {
            $("#calendarPopupLocationSelect")
                    .parents(".selectContainersHalf")
                    .find(".newLabel")
                    .removeClass("labelDisplayOn");
            $("#isNewLocation").val("0");
        }
    });
    $("#calendarPopupClassSelect").on("select2:selecting", function (e) {
        if (e.params.args.data.isNew) {
            $("#calendarPopupClassSelect")
                    .parents(".selectContainersHalf")
                    .find(".newLabel")
                    .addClass("labelDisplayOn");
            $("#isNewClass").val("1");
            openPopup("createNewClassType");
        } else {
            $("#calendarPopupClassSelect")
                    .parents(".selectContainersHalf")
                    .find(".newLabel")
                    .removeClass("labelDisplayOn");
            $("#isNewClass").val("0");
            let color = $("#calendarPopupClassSelect")
                    .children("option:selected")
                    .attr("data-color");
            $(".colorGridSelect .selectedColor").css("background-color", color);
            $("#selectedColor").val(color);
        }
    });
    $(
            "#cancelationInput1, #repetitionInput1 ,#cancelationInput2, #repetitionInput2"
            ).select2({
        minimumResultsForSearch: -1, dropdownParent: $("#mainPopup"),
    });
    $("#cancelationInput1, #cancelationInput2").on("select2:selecting", function (e) {
        //if (e.params.data.id == "advanced") {
        if (e.params.args.data.id == "advanced") {
            openPopup("cancelPopup");
        }
    });
    $("#repetitionInput1, #repetitionInput2").on("select2:selecting", function (e) {
        if (e.params.args.data.id == "advanced") {
            setDefaultFrequencySettings();
            openPopup("frequencySettings");
        }
    });
    function setDefaultFrequencySettings() {
        $("#frequencyNumber").val(1);
        $("#frequencyTypeOfUnit")
                .val(2)
                .trigger("change");
        $(".dayLiSelected").each(function () {
            $(this).trigger("click");
        });
        $("#never").trigger("click");
        $("#ftime").val("");
        $("#howManyRepeatsNumber").val("");
    }
    //setup datepicker
    $("#calendarPopupDateSelect").datepicker($.datepicker.regional["he"]);
    $("#ftime").datepicker($.datepicker.regional["he"]);

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    today = dd + "/" + mm + "/" + yyyy;
    $("#calendarPopupDateSelect").val(today);
    $("#ftime").val(today);

    let iteratedDate = DdMmtoMmDd($("#calendarPopupDateSelect").val());
    let iterateDay = iteratedDate.getDay();
    $(`.dayLi[value="${iterateDay}"]`)
            .addClass("dayLiLocked")
            .attr("data-selected", "1");
    $("#calendarPopupDateSelect").change(function () {
        $(".dayLiLocked").attr("data-selected", "0");
        $(".dayLiLocked").removeClass("dayLiLocked");
        let iteratedDate = DdMmtoMmDd($("#calendarPopupDateSelect").val());
        let iterateDay = iteratedDate.getDay();
        $(`.dayLi[value="${iterateDay}"]`)
                .addClass("dayLiLocked")
                .attr("data-selected", "1");
    });

    //setup timepickers
    $("#calendarPopupTimeSelectFrom, #calendarPopupTimeSelectTo").timepicker({
        timeFormat: "H:i"
    });
    //calculate and show amounts validate from isnt after to
    $("#calendarPopupTimeSelectFrom, #calendarPopupTimeSelectTo").change(
            function () {
                let fromVal = $("#calendarPopupTimeSelectFrom").val();
                let toVal = $("#calendarPopupTimeSelectTo").val();
                let changed = false;
                if (fromVal && fromVal != "") {
                    let hours = fromVal.substring(0, 2);
                    let minutes = fromVal.substring(3, 5);
                    if (parseInt(hours) > 23) {
                        fromVal = fromVal.replace(`${hours}:`, "23:");
                        changed = true;
                    }
                    if (parseInt(minutes) > 59) {
                        fromVal = fromVal.replace(`:${minutes}`, ":59");
                        changed = true;
                    }
                }
                if (toVal && toVal != "") {
                    let hours = toVal.substring(0, 2);
                    let minutes = toVal.substring(3, 5);
                    if (parseInt(hours) > 23) {
                        toVal = toVal.replace(`${hours}:`, "23:");
                        changed = true;
                    }
                    if (parseInt(minutes) > 59) {
                        toVal = toVal.replace(`:${minutes}`, ":59");
                        changed = true;
                    }
                }
                console.log(changed);
                console.log(fromVal);
                console.log(toVal);
                if (changed) {
                    if (fromVal) {
                        $("#calendarPopupTimeSelectFrom").val(fromVal);
                    }
                    if (toVal) {
                        $("#calendarPopupTimeSelectTo").val(toVal);
                    }
                    return;
                }
                if (fromVal) {
                    if (toVal && fromVal > toVal) {
                        $("#calendarPopupTimeSelectTo").val(null);
                        toVal = null;
                        $("#dateTextOutput").html(``);
                        $("#calendarPopupTimeDurtaion").val(null);
                    }
                    $("#calendarPopupTimeSelectTo").timepicker(
                            "option",
                            "minTime",
                            fromVal
                            );
                }
                if (fromVal && !toVal) {
                    $("#calendarPopupTimeSelectTo").timepicker("show");
                }
                if (fromVal && toVal) {
                    var fromArr = fromVal.split(":");
                    var toArr = toVal.split(":");
                    fromArr[0] = fromArr[0] * 60;
                    toArr[0] = toArr[0] * 60;
                    let FromNumber = parseInt(fromArr[0]) + parseInt(fromArr[1]);
                    let ToNumber = parseInt(toArr[0]) + parseInt(toArr[1]);
                    let Duration = ToNumber - FromNumber;
                    $("#dateTextOutput").html(`(${Duration} דק')`);
                    $("#calendarPopupTimeDurtaion").val(Duration);
                }
            }
    );

    //color picker functionality
    $(".colorGrid .colorCube").click(function (evt) {
        evt.stopPropagation();
        let color = $(this).css("background-color");
        $(".colorGridSelect .selectedColor").css("background-color", color);
        $("#selectedColor").val(color);
        $(".colorGridContainer").hide();
    });

    $(".colorGridSelect").click(function () {
        $(".colorGridContainer").toggle();
    });
    let mouse_is_inside;
    $(".colorGridSelect").hover(
            function () {
                mouse_is_inside = true;
            },
            function () {
                mouse_is_inside = false;
            }
    );

    $("#mainPopup").mouseup(function () {
        if (!mouse_is_inside)
            $(".colorGridContainer").hide();
    });

    //set show hide more in calendar and app screen
    $("#showMore").click(function () {
        if ($(this).attr("data-toggle") == "1") {
            $(this).attr("data-toggle", "0");
            $(this).html("הצג אפשרויות נוספות");
            $(".showMoreChangeable").addClass("hideShowMoreChangeable");
        } else {
            $(this).attr("data-toggle", "1");
            $(this).html("הצג פחות");
            $(".showMoreChangeable").removeClass("hideShowMoreChangeable");
        }
    });

    $("#addTrainer").on("click", function () {
        duplicateArea("shallBeDuplicated", $(this));
    });

    $("#openReminder").on("click", function () {
        showHidden($(this), ".hiddenReminder");
    });

    $("#closeReminder").on("click", function (e) {
        hideHidden($(this), ".hiddenReminder");
    });

    $("#openTextarea").on("click", function (e) {
        showHidden($(this), ".hiddenTextarea");
    });

    $("#closeTextarea").on("click", function (e) {
        hideHidden($(this), ".hiddenTextarea");
    });

    // $('#openSimpleVid').on('click', function (e) {
    //   showHidden($(this), '.hiddenSimpleVid');
    // });

    $("#closeSimpleVid").on("click", function (e) {
        hideHidden($("#closeSimpleVid"), ".hiddenSimpleVid ", ".select2");
        $("#prodcastOptions")
                .val("")
                .trigger("change");
        $("#prodcastLink").val("");
        $("#prodcastLink").prop("disabled", false);
    });

    $("#closeZoomVid").on("click", function (e) {
        hideHidden($(this), ".hiddenZoomVid", ".select2");
        $("#prodcastOptions")
                .val("")
                .trigger("change");
        $("#prodcastLink").val("");
        $("#prodcastLink").prop("disabled", false);
    });

    $("#openMinimum").on("click", function (e) {
        showHidden($(this), ".hiddenMinimum");
    });

    $("#closeMinimum").on("click", function (e) {
        hideHidden($(this), ".hiddenMinimum");
    });

    $("#openPurchaseOptions").on("click", function (e) {
        showHidden($(this), ".hiddenPurchaseOptions");
    });

    $("#closePurchaseOptions").on("click", function (e) {
        hideHidden($(this), ".hiddenPurchaseOptions");
    });

    $("#openDevices").on("click", function (e) {
        showHidden($(this), ".hiddenDevices");
    });

    $("#closeDevices").on("click", function (e) {
        hideHidden($(this), ".hiddenDevices");
    });
    $("#removeImg").on("click", function (e) {
        hideHidden($(this), ".hiddenImg");
        $("#pageImgPath").val("");
        $(".ImgName").text("");
    });
    $("#placeSignTiming").on("click", function () {
        var numOfExistingRows = $(".signTimingItem").length;
        if (numOfExistingRows < 2) {
            let selected = numOfExistingRows == 1 ? "selected" : "";
            let markup = `<div class="rowInput showMoreChangeable calendarAndApp signTimingItem transitionAll" data-limitation-number="${numOfExistingRows +
                    1}">
      <div class="rowIconContainer">
        <i class="far fa-eye"></i>
      </div>
      <div class="ml-2">
        השיעור 
      </div>
      <select class="cute-input ml-2 limitationType" name="" id="timingOpenClose${numOfExistingRows}">
          <option value="open">
              יפתח
          </option>
          <option ${selected} value="close">
            יסגר
          </option>
      </select>
      <div class="ml-2">
        להרשמה 
      </div>
      <input class="cute-input ml-2 timingNumber" style='width:50px;' type="number" value="60" name="newClassDurationNumber" id="timingNumber${numOfExistingRows}">
      <select class="cute-input ml-2 timingUnit" id="timingUnit${numOfExistingRows}">
        <option value="דקות">
          דקות
        </option>
        <option value="שעות">
          שעות
        </option>
        <option value="ימים">
          ימים  
        </option>
      </select>
      <div class="ml-2">
      לפני תחילת השיעור 
      </div>
      <div class="text-danger mis-9 closeSignTimingRow"><i class="fas fa-do-not-enter"></i></div>
  </div>`;
            _thisParent = $(this);
            $(markup)
                    .insertBefore($(this))
                    .hide()
                    .toggle("slide");
            $(".closeSignTimingRow").on("click", function () {
                var parentShallBeDoomed = $(this).parent();
                parentShallBeDoomed.hide("slow", function () {
                    $(this).remove();
                    if ($(".signTimingItem").length < 2) {
                        _thisParent.show();
                    }
                });
            });
        }
        if ($(".signTimingItem").length == 2) {
            $(this).hide();

            $("#timingOpenClose0 , #timingOpenClose1").on("change", function () {
                console.log();
                if ($(this).prop("id") === "timingOpenClose0") {
                    if (this.value == "open") {
                        $("#timingOpenClose1").val("close");
                    } else {
                        $("#timingOpenClose1").val("open");
                    }
                } else {
                    if (this.value == "open") {
                        $("#timingOpenClose0").val("close");
                    } else {
                        $("#timingOpenClose0").val("open");
                    }
                }
            });
        }
    });

    $("#placeLimitation").on("click", function () {
        var numOfExistingRows = $(".placeLimitationItem").length;
        if (numOfExistingRows < 4) {
            var markup = `<div class="rowInput showMoreChangeable calendarAndApp placeLimitationItem transitionAll" data-limitation-number="${numOfExistingRows +
                    1}">
      <div class="rowIconContainer">
         <i class="far fa-hand-paper"></i>
      </div>
      <select class="cute-input ml-3 limitTriger" name="" id="limitation${numOfExistingRows}">
          <option value="0">
              בחירת מגבלה
          </option>
          <option value="age">
              לפי גיל
          </option>
          <option value="degree">
              לפי דרגה
          </option>
          <option value="type">
              לפי סוג מנוי
          </option>
          <option value="gender">
         לפי מגדר
        </option>
      </select>
      <div class="text-danger mis-9 closeLimitationRow"><i class="fas fa-do-not-enter"></i></div>
  </div>`;
        }

        _thisParent = $(this);
        $(markup)
                .insertBefore($(this))
                .hide()
                .toggle("slide");
        $(".closeLimitationRow").on("click", function () {
            var parentShallBeDoomed = $(this).parent();
            parentShallBeDoomed.hide("slow", function () {
                $(this).remove();
                deleteMultipleLimits();
                if ($(".placeLimitationItem").length < 4) {
                    _thisParent.show();
                }
            });
        });
        if ($(".placeLimitationItem").length == 4) {
            $(this).hide();
        }

        $(".limitTriger").on("change", function () {
            $(this)
                    .parent()
                    .find(".dynamic-limit")
                    .remove();
            let _val = $(this).val();

            switch (_val) {
                case "age":
                    $(this).after(
                            `<select class="ageLimitOperator cute-input dynamic-limit">
              <option value="1" >גדול מ</option>
              <option value="2" >קטן מ</option>
              <option value="3" >בטווח</option>
            </select>
            <input style="width:50px;" type="number" class="cute-input mr-2 ml-2 dynamic-limit" value="1"  id="ageLimit1">
            <span class="dynamic-limit" id="limit-span" style="display:none;">עד</span>
            <input style="width:50px;" type="hidden" class="cute-input mr-2 ml-2 dynamic-limit" value="1"  id="ageLimit2">`
                            );
                    $(".ageLimitOperator").change(function () {
                        if ($(this).val() == "3") {
                            $("#ageLimit2").attr("type", "number");
                            $("#limit-span").show();
                        } else {
                            $("#ageLimit2").attr("type", "hidden");
                            $("#limit-span").hide();
                        }
                    });
                    break;

                case "degree":
                    let id3 = `${makeid(5)}3`;
                    $(this).after(
                            `<span class="dynamic-limit"><select multiple="multiple" id="${id3}" style="width:300px;" class="cute-input mr-2 ml-2 degreeInput" id="MembershipLevels"><select></span>`
                            );
                    var options4 = $("#hiddenLevelsInput > option").clone();
                    $("#" + id3)
                            .append(options4)
                            .select2();
                    break;
                case "type":
                    let id = `${makeid(5)}2`;
                    $(this).after(
                            `<span class="dynamic-limit"><select  multiple="multiple" id="${id}" style="width:300px;" class="cute-input mr-2 ml-2 mebershipInput" id="MembershipLimits"><select></span>`
                            );
                    var options3 = $("#hiddenMembershipInput > option").clone();
                    $("#" + id)
                            .append(options3)
                            .select2();
                    break;
                case "gender":
                    let id2 = makeid(5);
                    $(
                            this
                            ).after(`<span class="dynamic-limit"><select id="${id2}" style="width:300px;" class="cute-input mr-2 ml-2 genderInput" id="genderLimit">
           <option value="1">זכר</option>
           <option value="2">נקבה</option>
           <option value="0">אחר</option>
           <select>
           </span>`);
                    $("#" + id2).select2({minimumResultsForSearch: -1, dropdownParent: $("#mainPopup"), });
                    break;
            }
        });
        deleteMultipleLimits();
    });
    $("#devicesInput").select2();
    $("#placeClient").on("click", function () {
        let haveEmpty = false;
        $(".clientName, .clientCell").each(function () {
            if ($(this).val() == "" || !$(this).val()) {
                haveEmpty = true;
            }
        });
        if (haveEmpty) {
            Swal.fire("", "יש למלא את המתאמן הקיים לפני שמוסיפים מתאמן חדש", "error");
            return;
        }
        var numOfExistingRows = $(".placeClientItem").length + 1;
        var options1 = $("#hiddenUsersName > option").clone();
        var options2 = $("#hiddenUsersPhone > option").clone();
        var markup = `<div class="rowInput justCalendar placeClientItem" data-client-number="${numOfExistingRows}">
    <div class="rowIconContainer">
        <i class="far fa-user-circle"></i>
    </div>
    <span class="newLabel">חדש</span>
    <input class="isUserNew" id="isUserNew${numOfExistingRows}" value="0" type="hidden"/>
    <select class="clientName cute-input ml-3" id="clientName${numOfExistingRows}">
    <option val=""></option>
    </select>
    <select class="clientCell cute-input ml-3" id="clientCell${numOfExistingRows}">
    <option val=""></option>
    </select>
    <select class="cute-input ml-3 clientCharge" id="clientCharge${numOfExistingRows}">
        <option value="none">
            ללא חיוב
        </option>
        <option value="amount">
            סכום לחיוב
        </option>
    </select>
    <div class="text-danger mis-9 closeClientRow"><i class="fas fa-do-not-enter"></i></div>
</div>`;
        $(markup)
                .insertBefore($(this))
                .hide()
                .toggle("slide");
        $(`#clientName${numOfExistingRows}`)
                .append(options1)
                .select2({
                    dropdownParent: $("#mainPopup"),
                    minimumInputLength: 3,
                    tags: true,
                    placeholder: "שם מלא",
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            isNew: true
                        };
                    }
                });
        $(`#clientCell${numOfExistingRows}`)
                .append(options2)
                .select2({
                    dropdownParent: $("#mainPopup"),
                    minimumInputLength: 3,
                    tags: true,
                    placeholder: "טלפון",
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            isNew: true
                        };
                    }
                });

        $(`#clientName${numOfExistingRows}`).on("select2:selecting", function (e) {
            if (e.params.args.data.isNew) {
                $(this)
                        .parent()
                        .find(".newLabel")
                        .addClass("labelDisplayOn");
                $(this)
                        .parent()
                        .find(".isUserNew")
                        .val("1");
                let otherInputData = $(`#clientCell${numOfExistingRows}`).select2(
                        "data"
                        );
                if (!otherInputData[0].isNew) {
                    $(`#clientCell${numOfExistingRows}`)
                            .val("")
                            .trigger("change");
                }
            } else {
                $(this)
                        .parent()
                        .find(".newLabel")
                        .removeClass("labelDisplayOn");
                $(this)
                        .parent()
                        .find(".isUserNew")
                        .val("0");
                if ($(`#clientCell${numOfExistingRows}`).val() != $(this).val()) {
                    $(`#clientCell${numOfExistingRows}`)
                            .val($(this).val())
                            .trigger("change");
                }
            }
        });
        $(`#clientCell${numOfExistingRows}`).on("select2:selecting", function (e) {
            if (e.params.args.data.isNew) {
                $(this)
                        .parent()
                        .find(".newLabel")
                        .addClass("labelDisplayOn");
                $(this)
                        .parent()
                        .find(".isUserNew")
                        .val("1");
                let otherInputData = $(`#clientName${numOfExistingRows}`).select2(
                        "data"
                        );
                if (!otherInputData[0].isNew) {
                    $(`#clientName${numOfExistingRows}`)
                            .val("")
                            .trigger("change");
                }
            } else {
                $(this)
                        .parent()
                        .find(".newLabel")
                        .removeClass("labelDisplayOn");
                $(this)
                        .parent()
                        .find(".isUserNew")
                        .val("0");
                if ($(`#clientName${numOfExistingRows}`).val() != $(this).val()) {
                    $(`#clientName${numOfExistingRows}`)
                            .val($(this).val())
                            .trigger("change");
                }
            }
        });
        $(`#clientCell${numOfExistingRows}, #clientName${numOfExistingRows}`).on(
                "select2:selecting",
                function (e) {
                    $(`#clientCharge${numOfExistingRows} option.fromServer`).remove();
                    if (!isNaN($(this).val()) && !e.params.args.data.isNew) {
                        showLoader();
                        $.ajax({
                            url: "GetUsersValidMembership.php",
                            type: "post",
                            data: JSON.stringify({clientId: $(this).val()}),
                            dataType: "json",
                            contentType: "application/json",
                            success: function (response) {
                                hideLoader();
                                if (response.length) {
                                    response.forEach(function (activity) {
                                        $(`#clientCharge${numOfExistingRows}`).append(`
                <option class="fromServer" value="${activity.id}">${activity.ItemText}</option>
                `);
                                    });
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                hideLoader();
                                throw errorThrown;
                            }
                        });
                    }
                }
        );
        $(`#clientCharge${numOfExistingRows}`).change(function () {
            if ($(this).val() == "amount") {
                $(
                        `<input type="number" style="max-width: 50px;"class="howMuchToCharge cute-input" id="howMuchToCharge${numOfExistingRows}">`
                        ).insertAfter($(this));
            } else {
                $(`#howMuchToCharge${numOfExistingRows}`).remove();
            }
        });
        $(".closeClientRow").on("click", function () {
            var parentShallBeDoomed = $(this).parent();
            parentShallBeDoomed.hide("slow", function () {
                $(this).remove();
            });
        });
    });

    $("#saveZoom").on("click", function () {
        let inputToSet = $("#prodcastLink");
        let zoomMeetingId = $("#zoomMeetingId").val();
        let zoomMeetingPassword = $("#zoomMeetingPassword").val();

        inputToSet.before(
                $(
                        `<input style="display:none;" type="text" value="${zoomMeetingPassword}" id="zoomPassword">`
                        )
                );
        inputToSet.val(zoomMeetingId);
        inputToSet.prop("disabled", true);

        // closePopup('#zoomPopup')
    });

    $("#allowAsStandBy").on("change", function () {
        if (this.value == 3) {
            $("#limitStandbyList").show();
        } else {
            $("#limitStandbyList").hide();
        }
    });
    $("#freeRegister").change(function () {
        $(".hiddenPurchaseOptions")
                .addClass("hidden")
                .removeClass("visible");
        $("#openPurchaseOptions .plus")
                .addClass("visible")
                .removeClass("hidden");
        if ($(this).prop("checked")) {
            $("#openPurchaseOptions ").hide();
        } else {
            $("#openPurchaseOptions").show();
        }
    });
    $("#openMainPopup").click(function () {
        setEmptyMainCalendarPopup();
        openPopup("mainPopup");
    });
});
