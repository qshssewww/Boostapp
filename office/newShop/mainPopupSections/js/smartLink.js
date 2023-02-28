var itemRolesSmart = {};

function convertDaysStringArrayToNumArray(days) {
    let dayArray= [];
    $.each(days,function (ind,day) {
        if(day == "sunday" || day == "ראשון"){
            dayArray.push(0);
        }
        else if(day == "monday" || day == "שני"){
            dayArray.push(1);
        }
        else if(day == "tuesday" || day == "שלישי"){
            dayArray.push(2);
        }
        else if(day == "wednesday" || day == "רביעי"){
            dayArray.push(3);
        }
        else if(day == "thursday" || day == "חמישי"){
            dayArray.push(4);
        }
        else if(day == "friday" || day == "שישי"){
            dayArray.push(5);
        }
        else if(day == "saturday" || day == "שבת"){
            dayArray.push(6);
        }
    })
    return dayArray;
}



function checkItemRoles(type, data) {
    if(itemRolesSmart.length < 1){
        return data;
    }
    data.statusWeek = -2;
    $.each(itemRolesSmart, function (ind,role) {
        let e = role;
        if(role.Group == "Day"){
            if(type == "membership") {
                let select = $('<div/>').html(data.select).contents();
                let days = role.Value.split(",");
                let options = select.find('.registerClassDayInput option');
                options.each(function (key,opt) {
                    let text = opt.text;
                    if($.inArray(text,days) <= -1 && key != 0){
                        select.find('option:contains("' + text + '")').remove();
                    }
                });
                data.select = select;
            }
            else if(type == "tickets"){
                let days = role.Value.split(",");
                days = convertDaysStringArrayToNumArray(days);
                let chosenDay = new Date(data.date).getDay();
                if($.inArray(chosenDay,days) <= -1){
                    data.statusDay = false;
                }
            }
        }
        else if(role.Group == "Max"){
            if(type == "membership") {
                if(role.Item == "Month"){
                    data.statusMaxMonth = false;
                }
                else if(role.Item == "Week"){
                    data.statusWeek = role.Value;
                }
                else if(role.Item == "Day"){
                    let dayArr = [];
                    $(".registerClassDayInput").each(function (ind,sel) {
                        let val = dayArr[sel.value];
                        if(val == undefined){
                            dayArr[sel.value] = 1;
                        }
                        else{
                            dayArr[sel.value] = val + 1;
                        }
                    })
                    let select = $('<div/>').html(data.select).contents();
                    $.each(dayArr,function (day,num) {
                        if(num == role.Value){
                            select.find('option[value="' + day + '"]').remove();
                        }
                    });
                    data.select = select;
                }
            }
            else if(type == "tickets"){
                let checkDate = new Date(data.date);
                let datesElement = $(".registerDateInput");
                if(role.Item == "Month"){
                    let month = checkDate.getMonth();
                    let year = checkDate.getFullYear();
                    let count = -1;
                    $.each(datesElement,function (ind,date) {
                        let elemDate = new Date(date.value);
                        let elemYear = elemDate.getFullYear();
                        let elemMonth = elemDate.getMonth();
                        if(elemYear == year && elemMonth == month){
                            count++;
                        }
                        if (count == role.Value){
                            data.statusMaxMonth = false;
                        }
                    })
                }
                else if(role.Item == "Week"){
                    let dateDay = checkDate.getDay();
                    let firstDayOfWeek = new Date();
                    firstDayOfWeek = new Date(firstDayOfWeek.setDate(checkDate.getDate() - dateDay));
                    let lastDayOfWeek = new Date(firstDayOfWeek);
                    lastDayOfWeek.setDate(lastDayOfWeek.getDate() + 6);
                    let count = -1;
                    $.each(datesElement,function (ind,date) {
                        let elemDate = new Date(date.value);
                        if(elemDate >= firstDayOfWeek && elemDate <= lastDayOfWeek) {
                            count++;
                        }
                        if (count == role.Value){
                            data.statusMaxWeek = false;
                        }
                    })
                }
                else if(role.Item == "Day"){
                    let count = -1;
                    $.each(datesElement,function (ind,date) {
                        let elemDate = new Date(date.value);
                        if(elemDate.getDate() == checkDate.getDate()){
                            count++;
                        }
                        if (count == role.Value){
                            data.statusMaxDay = false;
                        }
                    });
                }
            }
        }
    });
    return data;
}

function emptyLinkPopup() {

    $('#openRegisterForm .rowInput')
            .each(function (index) {
                if (index === 0) {
                    $(this).find('select').val('-1');
                } else {
                    if ($(this).find(".plus").length != 1) {
                        $(this).remove();
                    }
                }
            });

    $('#openRegisterInsurance .rowInput')
            .each(function (index) {
                if (index === 0) {
                    $(this).find('select').val('-1');
                } else {
                    if ($(this).find(".plus").length != 1) {
                        $(this).remove();
                    }
                }
            });

    $("#hiddenIdInput3").val("");
    $('#smartLinkTitle').val("");
    $("#smartLinkCustomer")
            .val($(`#smartLinkCustomer option:first`).val())
            .trigger("change");
    $('#smartLinkProductType').val(0).trigger('change');
    $('#linkPrice').val("");
    $('#linkTaxInclude').prop("checked", true);
    $(".smartLinkExpiration").hide();
    $('#smartLinkExpiration').val(1).trigger('change');
    $('#lateRegisterDateInput').val("").trigger('change');
    $('#allowLateRegister').prop('checked', false).trigger('change');

    $('#allowRelativeCheckbox').prop('checked', false);

    $("#openRegisterForm .plus").removeClass("hidden").addClass("visible");
    $("#openRegisterForm .js-select-container").removeClass("visible").addClass("hidden");

    $("#openRegisterInsurance .plus").removeClass("hidden").addClass("visible");
    $("#openRegisterInsurance .js-select-container").removeClass("visible").addClass("hidden");
    if ($("#openRegisterInsurance").hasClass("editInsuranceJs")){
        $("#openRegisterInsurance").removeClass("editInsuranceJs");
    }
    $("#registerFormSelect")
            .val($(`#registerFormSelect option:first`).val())
            .trigger("change");
    $("#registerInsuranceSelect")
            .val($(`#registerInsuranceSelect option:first`).val())
            .trigger("change");
    $("#imgPlus3").html(
            `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName3"></div></div> `
            );
    let imgPath = $("#pageImgPath3");
    imgPath.val("");
    // imgPath.prev().show();

    $("#openLink .plus").removeClass("hidden").addClass("visible");
    $(".hiddenLink").removeClass("visible").addClass("hidden");
    $('#afterLink').val("")
    $("#linkContent").summernote('code', "");
    $("#openTextareaLink .plus").removeClass("hidden").addClass("visible");
    $(".hiddenTextareaLink").removeClass("visible").addClass("hidden");
}
function fillLinkPopupWithData(data) {
    emptyLinkPopup();
    setUpdateText();
    $("#hiddenIdInput3").val(data.id);
    $('#smartLinkTitle').val(data.Title);
    $('#smartLinkCustomer').val(data.Brands).trigger('change');
    if (data.type == 'item') {
        $('#smartLinkProductType').val("1").trigger('change');
        $('#addSingleSmartLinkProduct').click();
        $('.smartLinkChosenProduct').val(data.ItemId).trigger('change');
        //price should be automatic
    } else {
        $('#smartLinkProductType').val("2").trigger('change');
        $('#smartLinkChosenMembership').val(data.ItemId).trigger('change');
        $('#linkTaxInclude').prop('checked', data.Vat == "0");
        if(data.lateRegistration == "3" || data.lateRegistration == "1") {
            $('#smartLinkExpiration').val(data.lateRegistration).trigger('change');
        }
        else{
            let lateReg = JSON.parse(data.lateRegistration);
            $('#smartLinkExpiration').val(4).trigger('change');
            $('#lateRegisterDateInput').val(lateReg.date).trigger('change');
            if(lateReg.allowLateRegitration == 1) {
                $('#allowLateRegister').prop("checked",true).trigger('change');
            }
            if(lateReg.allowRelativeReduction == 1){
                $('#allowRelativeCheckbox').prop("checked",true).trigger('change');
                $('#relativeReductionPrice').val(lateReg.relativeReductionPrice);
            }
        }
        $('#linkPrice').val(data.Amount);
        //שיבץ לסדרת שיעורים
        if (data.tickets != "") {

            let elem = $('.ticketsClasses');
            data.tickets.forEach(singleTicket => {
                let randId = new Date().valueOf();
                let date = new Date(singleTicket.classDate);
                let div = `<div class="col-12 ticketsClassesDiv" id="ticketsClassesDiv${randId}">
      <div class="rowInput">
      <div class="rowIconContainer">
      <i class="far fa-calendar-alt"></i>
      </div>
      בתאריך
      <input class="registerDateInput" value="${date.getUTCMonth() + 1}/${date.getUTCDate()}/${date.getUTCFullYear()}"class="cute-input"/>
      <div class="text-danger mis-9 ticketsClassesStop ticketsClassesStop${randId}" id="ticketsClassesDiv${type}"><i class="fas fa-do-not-enter"></i></div>
      </div>`;

                $(`ticketsClassesStop${randId}`).on('click', function () {
                    $(this).closest('.ticketsClassesDiv').hide(function () {
                        $(this).remove();
                    });
                });

                if (elem.hasClass("newClassReg")) {
                    elem.remove();
                }

                $(elem).after(div);
                $(`#ticketsClassesDiv${randId}`).find('.registerDateInput').datepicker($.datepicker.regional["he"]);


                let elem2 = $(`#ticketsClassesDiv${randId}`).find('.registerDateInput');

                if (elem2.next().is(".classSelection")) {
                    elem2.next().remove();
                }

                let classes = singleTicket.options;
                let select = `<select class="cute-input classSelectionTickets classSelection p-6">
      <option value="-1">בחר שיעור</option>`;

                classes.forEach(singleClass => {
                    var letters = /^[a-z][a-z\s]*$/;

                    let time = "(" + singleClass.EndTime + " - " + singleClass.StartTime + ")";
                    let fullname =  time + ' ' + singleClass.ClassName;
                    if (singleClass.ClassName.match(letters)) {
                        fullname = singleClass.ClassName + ' ' + time;
                    }
                    let option = `<option value="${singleClass.id}" ${singleClass.id == singleTicket.classId ? "selected" : ""} data-group="${singleClass.GroupNumber}">${fullname}</option>`;
                    select += option
                })
                select += "</select>";
                elem2.after(select);


            });

            $(elem).hide(500);

            let newReg = `<div class="col-12 newClassReg ticketsClassesDiv">
        <div class="rowInput">
        <div class="rowIconContainer">
        <i class="far fa-calendar-alt"></i>
        </div>
        שבץ למועד חדש
        </div>
        </div>`;


            $(".ticketsClassesDiv:last").after(newReg);



        }
        if (data.membership != "") {
            let elem = $('.membershipClasses');
            data.membership.forEach(singleMembership => {
                let randId = new Date().valueOf();
                let div = `<div class="col-12 membershipClassesDiv" id="membershipClassesDiv${randId}">
      <div class="rowInput">
      <div class="rowIconContainer">
      <i class="far fa-calendar-alt"></i>
      </div>
      <select class="registerClassDayInput cute-input mr-2 p-6">
      <option value="-1">בחר יום</option>
      <option ${singleMembership.day == "0" ? "selected" : ""} value="0">יום ראשון</option>
      <option  ${singleMembership.day == "1" ? "selected" : ""} value="1">יום שני</option>
      <option  ${singleMembership.day == "2" ? "selected" : ""} value="2">יום שלישי</option>
      <option  ${singleMembership.day == "3" ? "selected" : ""} value="3">יום רביעי</option>
      <option  ${singleMembership.day == "4" ? "selected" : ""} value="4">יום חמישי</option>
      <option  ${singleMembership.day == "5" ? "selected" : ""} value="5">יום שישי</option>
      <option  ${singleMembership.day == "6" ? "selected" : ""} value="6">יום שבת</option>
      </select>
      <div class="text-danger mis-9 membershipClassesStop classesStop${randId}" id="membershipClassesDiv${type}"><i class="fas fa-do-not-enter"></i></div>
      </div>`;

                $(elem).after(div);

                $(`.classesStop${randId}`).on('click', function (e) {
                    $(this).closest('.membershipClassesDiv').hide(function () {
                        $(this).remove();
                    });
                    $('.membershipClasses').show();
                });

                let elem2 = $(`#membershipClassesDiv${randId}`).find('.registerClassDayInput');
                if (elem2.next().is(".classSelection")) {
                    elem2.next().remove();
                }

                let classes = singleMembership.options;
                let select = `<select class="cute-input classSelection p-6">
      <option value="-1">בחר שיעור</option>`;

                classes.forEach(singleClass => {
                    var letters = /^[a-z][a-z\s]*$/;

                    let time = "(" + singleClass.EndTime + " - " + singleClass.StartTime + ")";
                    let fullname =  time + ' ' + singleClass.ClassName;
                    if (singleClass.ClassName.match(letters)) {
                        fullname = singleClass.ClassName + ' ' + time;
                    }
                    let option = `<option value="${singleClass.id}" data-value="${singleClass.GroupNumber}" ${singleClass.GroupNumber == singleMembership.classGroup ? "selected" : ""}>
          ${fullname}
        </option>`
                    select += option;
                })
                select += "</select>";

                elem2.after(select);
            });
            $(elem).hide(500);
            let newReg = `<div class="col-12 membershipClassesDiv newMembershipClass">
        <div class="rowInput">
        <div class="rowIconContainer">
        <i class="far fa-calendar-alt"></i>
        </div>
        שבץ למועד חדש
        </div>
        </div>`;
            $(".membershipClassesDiv:last").after(newReg);
        }

    }

    //ZOHAR
    if (data.dynamicForm) {
        let df = data.dynamicForm.split(',');
        //showHidden($('#openRegisterForm'), ".hiddenRegisterForm");
        df.forEach((val, index) => {
            $('#openRegisterForm .rowInput .plus').trigger('click')
            $('#openRegisterForm select').last().val(`__D${val}`).trigger('change');
        });
        $('#openRegisterForm').addClass("editInsuranceJs");
        //$('#openRegisterForm .rowInput .plus').trigger('click')
    }
    if (data.medicalForm) {
        let mf = data.medicalForm.split(',');
        //showHidden($('#openRegisterForm'), ".hiddenRegisterForm");
        mf.forEach((val, index) => {
            $('#openRegisterForm .rowInput .plus').trigger('click')
            $('#openRegisterForm select').last().val(`__M${val}`).trigger('change');
        });
        $('#openRegisterForm').addClass("editInsuranceJs");
        //$('#openRegisterForm .rowInput .plus').trigger('click')
    }
    if (data.extraFees) {
        let ef = data.extraFees.split(',');
        showHidden($('#openRegisterInsurance'), ".hiddenRegisterInsurance");
        ef.forEach((val, index) => {
            $('.registerInsuranceSelect').last().val(val).trigger('change');
        });
        $('#openRegisterInsurance').addClass("editInsuranceJs");
        $('#openRegisterInsurance .rowInput  .plus').trigger('click');
    }
    if (data.ImageLink) {
        $("#pageImgPath3").val(data.ImageLink)
        $("#ImgName3").append('<img class="shopImage" id="shopImage3" src="' + data.ImageLink + '"/>')
        showHidden($("#imgPlus3"), ".hiddenImg");
        $("#pageImgPath3").prev().show();
    }

    // if(data.Image){
    //   $("#ImgName" + type).append('<img class="shopImage" id="shopImage' + type + '" src="' + data.Image + '"/>')
    //   showHidden($("#imgPlus" + type), ".hiddenImg");
    //   $("#pageImgPath" + type).val(data.Image);
    //   $("#pageImgPath" + type).prev().show();
    // }

    if (data.ThankYouPage) {
        showHidden($('#openLink'), ".hiddenLink");
        $('#afterLink').val(data.ThankYouPage);
    }
    if (data.Content) {
        $("#linkContent").summernote('code', data.Content);
        $("#openTextareaLink .plus").removeClass("visible").addClass("hidden");
        $(".hiddenTextareaLink").removeClass("hidden").addClass("visible");
    }


}

var membershipBalance = 0;
var valid = 0;
var MembershipObj = {};
$(document).ready(function () {
    $('#linkContent').summernote({

        height: 120,
        width: "100%",
        followToolbar: false,
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
    //שיוך לקוח משלם לסניף
    // $("#smartLinkCustomer").select2({})
    $("#smartLinkProductType").change(function () {
        $("#imgPlus3").html(
                `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName3"></div></div> `
                );
        let thisValue = $(this).val();
        if (thisValue == "0") {
            $(".productTypeDependent1").hide(0);
            $(".productTypeDependent2").hide(0);
            $(".linkProductLine").hide(0, function () {
                $(this).remove();
                $(".selectItemDependent").hide(0);
            });
            $("#smartLinkChosenMembership").val(0).trigger("change");
        } else {
            if (thisValue == "1") {
                $(".productTypeDependent1").show();
                $(".productTypeDependent2").hide(500);
                $("#smartLinkChosenMembership").val(0).trigger("change");
            } else {
                $(".productTypeDependent2").show();
                $(".productTypeDependent1").hide(500);
                $(".linkProductLine").hide(0, function () {
                    $(this).remove();
                    $(".selectItemDependent").hide(0);
                });
            }
        }
    });

    $("#smartLinkChosenMembership").select2({
        theme: "bsapp-dropdown"
    });
    $("#smartLinkChosenMembership").change(function () {
        if ($(this).val() == 0) {
            $(".selectItemDependent").hide();
        } else {
            $(".selectItemDependent").show();
        }
        var price = $(this).find('option:selected').attr('data-price');
        var image = $(this).find('option:selected').attr('data-image');
        if (image) {
            $("#pageImgPath3").val(image)
            $("#ImgName3").append('<img class="shopImage" id="shopImage3" src="' + image + '"/>')
            showHidden($("#imgPlus3"), ".hiddenImg");
            $("#pageImgPath3").prev().show();
        } else {
            $("#imgPlus3").html(
                    `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName3"></div></div> `
                    );
        }
        if (price) {
            //$('#linkPrice').val(price).trigger('change');
            $('#linkPrice').val(price).trigger('keyup');
        } else {
            //$('#linkPrice').val("").trigger('change')
            $('#linkPrice').val("").trigger('keyup');
        }

        //reset all tickets and membership special options

        $('#addSingleSmartLinkProduct').show();

        $('.ticketsClassesDiv').hide(function () {
            $(this).remove();
        });

        $('.membershipClassesDiv').hide(function () {
            $(this).remove();
        });

        if ($(this).val() == 0) {
            $(".productSelectDependent2").hide();
            $("#generatedMembershipString").html("");
        } else {
            let MembershipData = JSON.parse(
                $("#smartLinkChosenMembership option:selected").attr("data-content")
            );
            MembershipObj = MembershipData;
            $(".productSelectDependent2").show();
            valid = MembershipData.valid;
            if($(".smartLinkExpiration").is(":visible")){
                MembershipData.StartMembership = $("#smartLinkExpiration").val();
            }
            if((MembershipData.Department == 2 && MembershipData.StartMembership != 4 && MembershipData.valid != 0)
                || (MembershipData.Department == 1 && MembershipData.StartMembership != 4)
                || (MembershipData.Department == 3 && MembershipData.valid != 0))
            {
                $(".membershipClasses").hide();
                //$(".smartLinkExpiration").show();
                $(".ticketsClasses").hide();
                membershipBalance = MembershipData.Balance;
            }
            else if ((MembershipData.Department != 1 && MembershipData.valid == 0) || MembershipData.Department == 3 && MembershipData.valid == 0) {
                $(".ticketsClasses").show();
                $(".membershipClasses").hide();
                //$(".smartLinkExpiration").hide();
                membershipBalance = MembershipData.Balance;
            }
            else {
                $(".ticketsClasses").hide();
                $(".membershipClasses").show();
                membershipBalance = 0;
            }
            if(MembershipData.valid == 0){
                $(".smartLinkExpiration").hide();
            }
            else{
                $(".smartLinkExpiration").show();
            }
            if (MembershipData.Payment == "2") {
                $(".ticketsClasses").hide();
                $(".membershipClasses").show();
                membershipBalance = 0;
                $(".smartLinkExpiration").hide();
            }
            // let string = `${
            //   MembershipData.Payment ? MembershipData.Payment : "חיוב חד פעמי רגיל "
            //   } :: תוקף ${MembershipData.valid} ${getValidType(
            //     MembershipData.validType
            //   )}.`;
            let string = generateSmartLinkString(MembershipData);
            $("#generatedMembershipString").html(string);
        }

        let id = $(this).val();
        if(id != 0) {
            let data = {
                "fun": "getItemRoles",
                "itemId": id
            };

            $.ajax({
                url: "/office/newShop/ajax.php",
                type: "post",
                data: data,
                success: function (response) {
                    console.log(response);
                    if(response != "Not Found"){
                        itemRolesSmart = JSON.parse(response);
                    }
                    else{
                        itemRolesSmart = {};
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })
        }
    });

    $("#addSingleSmartLinkProduct").click(function () {

        if ($('.linkProductLine').length > 0) {
            return
        }
        var options1 = $("#smartLinkHiddenProduct > option").clone();
        let randomId = Date.now();
        var markup = ` <div class="rowInput linkProductLine" id="linkProductLine${randomId}" style="display:none">
                        <div class="rowIconContainer">
                            <i class="fas fa-store"></i>
                        </div>
                    <select id="smartLinkChosenProduct${randomId}" class="cute-input smartLinkChosenProduct" style="width:33%; min-width: 350px;">
                    </select>
                    <input id="linkPrice${randomId}" class="cute-input linkPrice mis-7 mie-7" placeholder="מחיר" type="number"/>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="linkTaxInclude${randomId}" name="taxInclude" checked="checked">
                        <label class="custom-control-label p-0" for="linkTaxInclude${randomId}">כולל מע"מ</label>
                    </div>
                    <div class="text-danger mis-9" id="closeLineProductLine${randomId}"><i class="fas fa-do-not-enter"></i></div>
                </div>`;
        $("#chosenProductsContainer").prepend(markup);
        $(`#linkProductLine${randomId}`).show();
        $(`#smartLinkChosenProduct${randomId}`).append(options1).select2({
            theme: "bsapp-dropdown w-100"
        });
        $(`#closeLineProductLine${randomId}`).click(function () {
            $('#addSingleSmartLinkProduct').show();
            $(`#linkProductLine${randomId}`).hide(500, function () {
                $(this).remove();
            });
        });
        $(`#smartLinkChosenProduct${randomId}`).change(function () {

            var price = $('option:selected', this).attr('data-price');
            var image = $('option:selected', this).attr('data-image');
            if (image) {
                $("#pageImgPath3").val(image)
                $("#ImgName3").append('<img class="shopImage" id="shopImage3" src="' + image + '"/>')
                showHidden($("#imgPlus3"), ".hiddenImg");
                $("#pageImgPath3").prev().show();
            } else {
                $("#imgPlus3").html(
                        `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName3"></div></div> `
                        );
            }
            $(this).siblings('input[id^=linkPrice]').val(price);
            var oneSelected = false;
            $(".smartLinkChosenProduct").each(function (k, v) {
                // this assumes that zero is an unselected index
                if ($(v).children("option:selected").val() != 0) {
                    oneSelected = true;
                }
            });
            if (oneSelected) {
                $(".selectItemDependent").show();
            } else {
                $(".selectItemDependent").hide();
            }
        });

        $('#addSingleSmartLinkProduct').hide();
    });




    function generateSmartLinkString(contentData) {
        var res = '';
        var payment = contentData.Payment == 2 ? 'חיוב קבוע' : 'חיוב חד פעמי רגיל';
        var typeOfExperiment = contentData.valid + ' ' + getValidType(contentData.validType);
        if (contentData.Balance > 0) {
            var numberOfEnterances = contentData.Balance + ' ' + 'כניסות';
        }

        // if(contentData.Department != 1 &&  contentData.Balance != 0){
        //   res = payment + ' :: ' + numberOfEnterances;
        // }
        // else if (contentData.valid != 0) {
        //   res = payment + ' :: ' + typeOfExperiment;
        // }
        // else {
        //   res = payment;
        // }
        res = payment + ' :: ' + typeOfExperiment;
        if (contentData.Balance > 0) {
            res += ' :: ' + numberOfEnterances;
        }
        return res;
    }

    function getValidType(Type) {
        Valid_Type = "";
        if (Type == "1") {
            Valid_Type = "ימים";
        } else if (Type == "2") {
            Valid_Type = "שבועות";
        } else if (Type == "3") {
            Valid_Type = "חודשים";
        }
        return Valid_Type;
    }


    $("#openTextareaLink").on("click", function (e) {
        if (e.target.classList.contains('plus'))
            showHidden($(this), ".hiddenTextareaLink");
    });

    $("#closeTextareaLink").on("click", function (e) {
        hideHidden($(this), ".hiddenTextareaLink");
    });
    $("body").on("click","#openRegisterForm .rowInput .plus", function (e) {

        // if ($("#openRegisterForm .js-select-container.hidden").length == 1 && !($("#openRegisterForm").hasClass("editInsuranceJs"))){
        //     $("#openRegisterForm .js-select-container").removeClass("hidden");
        // } else {
            var options1 = $("#registerFormSelect option").clone();
            $.each(options1, function (ind, opt) {
                let optVal = $(this).val();
                $.each($(".registerFormSelect"), function () {
                    if (optVal == $(this).val()) {
                        console.log(options1[ind])
                        $(options1[ind]).attr("disabled", true);
                        console.log($(options1[ind]).attr("disabled", true));
                    }
                })
            });
            let randomId = Date.now();
            var markup = ` <div class="rowInput fitContent linkDynamicForm" id="linkDynamicForm${randomId}" style="display:none">
                        <div class="rowIconContainer">
                            <i class="fas fa-paperclip"></i>
                        </div>
                        <div style="width:100%;" class="hiddenRegisterForm d-flex align-items-center w-250p">
                    <select id="registerFormSelect${randomId}" class="cute-input registerFormSelect w-100">
                    </select>
                    <div class="text-danger mis-9 cursor-pointer" id="closeRegisterForm${randomId}"><i class="fas fa-do-not-enter"></i></div>
                    </div>
                </div>`;
            $("#openRegisterForm .js-add-btn-container").before(markup);
            $(`#linkDynamicForm${randomId}`).find("select").select2({
                theme: "bsapp-dropdown"
            });

            $(`#linkDynamicForm${randomId}`).show();
            $(`#registerFormSelect${randomId}`).append(options1);
            $(`#closeRegisterForm${randomId}`).click(function () {
                $(`#linkDynamicForm${randomId}`).hide(500, function () {
                    $(this).remove();
                });
            });
        //}
        $("#openRegisterForm .js-add-btn-container").addClass("hidden");
    });

    $("body").on("change", ".registerFormSelect:last", function () {
        $("#openRegisterForm .js-add-btn-container").removeClass("hidden");
    });

    $("body").on("change", ".registerInsuranceSelect:last", function () {
        $("#openRegisterInsurance .js-add-btn-container").removeClass("hidden");
    });
    
     /* $("body").on("change", ".registerFormSelect", function () {
         console.log($(this));
         console.log($(".registerFormSelect:last"));
        if( $(this) != $(".registerFormSelect:last")){
              
            var $remaining = $(this).nextAll(".registerFormSelect");
            console.log( $remaining  )
            var this_val = $(this).val();
            $( $remaining ).each(function(){
                if( $(this).val() == this_val ) {
                    console.log("yesss")
                    console.log($(this).val())
                    console.log(this_val)
                    $(this).remove()
                }
            });
        }
    });*/

    

    $("#closeRegisterForm").on("click", function (e) {
        if ($('.linkDynamicForm').length == 0) {
            $("#openRegisterForm .js-select-container").addClass("hidden");
        }
    });

    $("body").on("click","#openRegisterInsurance .rowInput  .plus", function (e) {
        if ($("#openRegisterInsurance .js-select-container.hidden").length == 1 && !($("#openRegisterInsurance").hasClass("editInsuranceJs"))) {
            $("#openRegisterInsurance .js-select-container").removeClass("hidden");
        } else {
            $("#openRegisterInsurance .js-select-container").removeClass("hidden");
            var options1 = $("#registerInsuranceSelect > option").clone();
            let num = 0;
            $.each(options1, function (ind, opt) {
                let optVal = $(this).val();
                $.each($(".registerInsuranceSelect"), function (index, value) {
                    var isLastElement = index == $(".registerInsuranceSelect").length - 1;
                    if (optVal == $(this).val()) {
                        $(options1[ind]).attr("disabled", true);
                    } else {
                        if (isLastElement) {
                            num += 1;
                        }
                    }
                })
            });
            if (num == 1) {
                return;
            }
            let randomId = Date.now();
            var markup = ` <div class="rowInput fitContent linkInsurance" id="linkInsurance${randomId}" style="display:none">
                        <div class="rowIconContainer">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div style="width:100%;" class="hiddenRegisterInsurance d-flex align-items-center w-250p">
                    <select id="registerInsuranceSelect${randomId}" class="cute-input registerInsuranceSelect w-100">
                    </select>
                    <div class="text-danger mis-9 cursor-pointer " id="closeRegisterInsurance${randomId}"><i class="fas fa-do-not-enter"></i></div>
                    </div>
                </div>`;
            $("#openRegisterInsurance .js-add-btn-container").before(markup);

            $(`#linkInsurance${randomId}`).show();
            $(`#registerInsuranceSelect${randomId}`).append(options1);
            $(`#registerInsuranceSelect${randomId}`).select2({
                theme: "bsapp-dropdown"
            });

            $(`#closeRegisterInsurance${randomId}`).click(function () {
                $(`#linkInsurance${randomId}`).hide(500, function () {
                    $(this).remove();
                });
            });
            $(`#registerInsuranceSelect${randomId}`).change(function () {
                var oneSelected = false;
                $(".registerInsuranceSelect").each(function (k, v) {
                    // this assumes that zero is an unselected index
                    if ($(v).children("option:selected").val() != 0) {
                        oneSelected = true;
                    }
                });
                if (oneSelected) {
                    $(".selectItemDependent").show();
                } else {
                    $(".selectItemDependent").hide();
                }
            });

       }
        $("#openRegisterInsurance .js-add-btn-container").addClass("hidden");
    });

    $("#closeRegisterInsurance").on("click", function (e) {
        if ($('.linkInsurance').length == 0) {
            $("#openRegisterInsurance .js-select-container").addClass("hidden");
            $("#openRegisterInsurance .js-add-btn-container").removeClass("hidden");
        }
    });


    $("#openLink").on("click", function (e) {
        if (e.target.classList.contains('plus'))
            showHidden($(this), ".hiddenLink");
    });

    $("#closeLink").on("click", function (e) {
        hideHidden($(this), ".hiddenLink");
    });

    $(document).on("click", ".ticketsClassesStop", function (e) {
        let div = $(this).closest(".ticketsClassesDiv");
        removeDiv($(this), div);
        if ($(".ticketsClassesDiv").length < 1) {
            $(".ticketsClasses").show();
            $(".newClassReg").hide(500, function () {
                $(".newClassReg").remove();
            });
        }
    });

    $(document).on("click", ".membershipClassesStop", function (e) {
        let div = $(this).closest(".membershipClassesDiv");
        removeDiv($(this), div);
        if ($(".membershipClassesDiv").length < 1) {
            $(".membershipClasses").show();
            $(".newMembershipClass").hide(500, function () {
                $(".newMembershipClass").remove();
            });
        }
    });




    $('#lateRegisterDateInput').datepicker($.datepicker.regional["he"]);

    $('#smartLinkExpiration').change(function () {
        if ($(this).val() == "4") {
            $('.dateExpirationDependent').show()
            $('#smartLinkChosenMembership').trigger("change");
        } else {
            $('.dateExpirationDependent').hide()
            $('.allowLateRegisterDependent').hide()
            $('.dateSelectedDependend').hide()
            $('#allowLateRegister').prop('checked', false)
            $('#lateRegisterDateInput').val("");
            $('.allowRelativeDependent').hide()
            $('#relativeReductionPrice').val("");
            $('#smartLinkChosenMembership').trigger("change");

        }
    })

    $(document).on("keyup", "#linkPrice", function () {
        if ($(this).val() != "" && MembershipObj.valid != "0" && MembershipObj.payment != "2") {
            $(".smartLinkExpiration").show();
        } else {
            $(".smartLinkExpiration").hide();
        }
    })

    $('#lateRegisterDateInput').change(function () {
        if ($(this).val() && $(this).val() != "") {
            $('.dateSelectedDependend').show()
        } else {
            $('.dateSelectedDependend').hide()
            $('#allowLateRegister').prop('checked', false)
        }
    })

    $('#allowRelativeCheckbox').change(function () {
        if ($(this).prop('checked')) {
            $('.allowRelativeDependent').show()
        } else {
            $('.allowRelativeDependent').hide()
        }
    });
    $('#allowLateRegister').change(function () {
        if ($(this).prop('checked')) {
            $('.allowLateRegisterDependent').show()
            $('#allowRelativeCheckbox').prop('checked', false)
        } else {
            $('.allowLateRegisterDependent').hide()
            $('#allowRelativeCheckbox').prop('checked', false)
            $('.allowRelativeDependent').hide()
            $('#relativeReductionPrice').val("");
        }
    });
    $(document).on("click", ".membershipClasses, .newMembershipClass", function (e) {
        if (!e.target.classList.contains('plus'))
            return;
        let div = '<div class="col-12 membershipClassesDiv">' +
                '<div class="rowInput">' +
                '<div class="rowIconContainer">' +
                '<i class="far fa-calendar-alt"></i>' +
                '</div>' +
                '<select class="registerClassDayInput cute-input p-6">' +
                '<option value="-1">בחר יום</option>' +
                '<option value="0">ראשון</option>' +
                '<option value="1">שני</option>' +
                '<option value="2">שלישי</option>' +
                '<option value="3">רביעי</option>' +
                '<option value="4">חמישי</option>' +
                '<option value="5">שישי</option>' +
                '<option value="6">שבת</option>' +
                '</select>' +
                // '<div class="close stop mr-2 membershipClassesStop" id="membershipClassesDiv' + type + '"></div>' +
                '<div class="text-danger mis-9 membershipClassesStop" id="membershipClassesDiv' + type + '"><i class="fas fa-do-not-enter"></i></div>' +
                '</div>' +
                '</div>';
        let elem = $(this);
        let dataForRoles = {
            "select" : div
        };
        let rolesData = checkItemRoles("membership",dataForRoles);
        if(rolesData.statusMaxMonth == false){
            Swal.fire({
                title: 'לא ניתן לשבץ לשיעורים מראש כאשר יש הגבלה חודשית על המנוי',
                confirmButtonText: `סגור`,
            });
            return;
        }
        let divs = $(".membershipClassesDiv").length - 1;
        if(divs == parseInt(rolesData.statusWeek)){
            Swal.fire({
                title: 'לא ניתן לשבץ ליותר סדרות שיעורים מההגבלה השבועית',
                confirmButtonText: `סגור`,
            });
            return;
        }
        $(elem).after(rolesData.select);
        $(elem).hide();
        if (elem.hasClass("newMembershipClass")) {
            elem.remove();
        }
    });
    $(document).on("change", ".registerClassDayInput", function () {
        let elem = $(this);
        let day = elem.val();
        let itemId = $('#smartLinkChosenMembership').val();
        if (day != "-1") {
            let data = {"fun": "classesMembership", "day": day, "duration": MembershipObj.valid, "type": MembershipObj.validType, "payment": MembershipObj.Payment,"itemId": itemId};
            showLoader()
            $.ajax({
                url: "/office/newShop/ajax.php",
                type: "post",
                data: data,
                success: function (response) {
                    hideLoader();
                    if (elem.next().is(".classSelection")) {
                        elem.next().remove();
                    }
                    let selArr = [];
                    $.each($(".classSelection"), function () {
                        selArr.push($(this).val())
                    });
                    let classes = "";
                    if (response != "classes not found") {
                        classes = JSON.parse(response);
                    }

                    let select = '<select class="cute-input classSelection p-6">' +
                            '<option value="-1">בחר שיעור</option>';
                    $.each(classes, function (key, value) {
                        let inArr = $.inArray(value.id, selArr);
                        if (inArr != "-1") {
                            return;
                        }
                        var letters = /^[a-z][a-z\s]*$/;

                        let time = "(" + value.EndTime + " - " + value.StartTime + ")";
                        if (value.name.match(letters)) {
                            select += '<option value="' + value.id + '" data-value="' + value.group + '">' + value.name + ' ' + time + '</option>';
                        } else {
                            select += '<option value="' + value.id + '" data-value="' + value.group + '">' + time + ' ' + value.name + '</option>';
                        }
                    });
                    select += '</select>';
                    elem.after(select);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    hideLoader();

                    console.log(textStatus, errorThrown);
                }
            })
        }
    });
    $(document).on("click", ".ticketsClasses, .newClassReg", function (e) {
        if (!e.target.classList.contains('plus'))
            return
        let div = '<div class="col-12 ticketsClassesDiv">' +
                '<div class="rowInput">' +
                '<div class="rowIconContainer">' +
                '<i class="far fa-calendar-alt"></i>' +
                '</div>' +
                '+ בתאריך' +
                '<input class="registerDateInput" class="cute-input mr-2"/>' +
                // '<div class="close stop mr-2 ticketsClassesStop" id="ticketsClassesDiv' + type + '"></div>' +
                '<div class="text-danger mis-9 ticketsClassesStop" id="ticketsClassesDiv' + type + '"><i class="fas fa-do-not-enter"></i></div>' +
                '</div>' +
                '</div>';
        let elem = $(this);
        $(elem).after(div);
        $(elem).hide();
        if (elem.hasClass("newClassReg")) {
            elem.remove();
        }
        $(".registerDateInput").datepicker($.datepicker.regional["he"]);
    });
    $(document).on("change", ".registerDateInput", function () {
        let elem = $(this);
        let date = elem.val();
        let checkData = {
            "date" : date
        };
        checkData = checkItemRoles("tickets",checkData);
        if (checkData.statusDay == false || checkData.statusMaxDay == false || checkData.statusMaxMonth == false || checkData.statusMaxWeek == false){
            elem.val("");
            Swal.fire({
                title: 'התאריך אינו תואם למגבלות המנוי',
                confirmButtonText: `סגור`,
            });
            return;
        }
        if (date != "") {
            date = date.replaceAll('/','-');
            let itemId = $('#smartLinkChosenMembership').val();
            let data = {"fun": "classesTickets", "date": date, "itemId": itemId};
            showLoader();
            $.ajax({
                url: "/office/newShop/ajax.php",
                type: "post",
                data: data,
                success: function (response) {
                    if (elem.next().is(".classSelection")) {
                        elem.next().remove();
                    }
                    let classes = "";
                    if (response != "classes not found") {
                        classes = JSON.parse(response);
                    }
                    let selArr = [];
                    $.each($(".classSelection"), function () {
                        selArr.push($(this).val())
                    });
                    let select = '<select class="cute-input classSelectionTickets classSelection p-6">' +
                            '<option value="-1">בחר שיעור</option>';
                    $.each(classes, function (key, value) {
                        let inArr = $.inArray(value.id, selArr);
                        if (inArr != "-1") {
                            return;
                        }
                        var letters = /^[a-z][a-z\s]*$/;

                        let time = "(" + value.EndTime + " - " + value.StartTime + ")";
                        if (value.name.match(letters)) {
                            select += '<option value="' + value.id + '">' + value.name + ' ' + time + '</option>';
                        } else {
                            select += '<option value="' + value.id + '">' + time + ' ' + value.name + '</option>';
                        }
                    });
                    select += '</select>';
                    elem.after(select);
                    hideLoader()
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    hideLoader()
                    console.log(textStatus, errorThrown);
                }
            })
        }
    });

    $(document).on("change", ".classSelection", function () {
        let classes = $(".classSelection");
        if ($(this).val() != "-1" && classes.length < membershipBalance && MembershipObj.Payment != "2") {
            let newReg = '<div class="col-12 newClassReg ticketsClassesDiv">' +
                    '<div class="rowInput">' +
                    '<div class="rowIconContainer ">' +
                    '<i class="far fa-calendar-alt"></i>' +
                    '</div>' +
                    '<div class="plus">' +
                    '+ שבץ למועד חדש' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            if ($(this).closest(".ticketsClassesDiv").next().is(".ticketsClassesDiv") || $(this).closest(".ticketsClassesDiv").next().is(".newClassReg")) {
                console.log("check");
            } else {
                $(".ticketsClassesDiv:last").after(newReg);
            }
        } else if ($(this).val() != "-1" && (valid != 0 || MembershipObj.Payment == "2")) {// מנוי
            let newReg = '<div class="col-12 membershipClassesDiv newMembershipClass">' +
                    '<div class="rowInput">' +
                    '<div class="rowIconContainer">' +
                    '<i class="far fa-calendar-alt"></i>' +
                    '</div>' +
                    '<div class="plus">' +
                    '+ שבץ למועד חדש' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            if ($(this).closest(".membershipClassesDiv").next().is(".membershipClassesDiv") || $(this).closest(".membershipClassesDiv").next().is(".newMembershipClass")) {
                console.log("check");
            } else {
                $(".membershipClassesDiv:last").after(newReg);
            }
        }
    });

    // tinymce.init({
    //   selector: "#linkContent",
    //   directionality: "rtl",
    //   menubar: false,
    //   statusbar: false,
    //   toolbar:
    //     "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | forecolor backcolor",
    //   plugins: "textcolor",
    // });

    // tinyMCE.get('linkContent').getContent();
});
