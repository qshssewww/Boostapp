

function digitsTest(val){
    let pattern = /^[0-9]\d*(\.\d+)?$/;
    return pattern.test(val);
}
function isEmpty(obj) {
    for(let key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
}
function displayError(elem,msg) {
    elem.css("border-color","red");
    elem.parent().append('<label style="color: red" class="inputError">' + msg +'</label>');
    $('html, body').animate({
        scrollTop: elem.offset().top - 100
    }, 1000);
}
function firstBlockCheck(){
    let display = true;
    let selectBrand = $("#shopCmpBranch");
    let selectMemberShip = $("#membershipType");
    let price = $("#shopItemPrice");
    let name = $("#membershipName");
    let params = {};
    if(selectBrand.length > 0 && (selectBrand.val() === undefined || selectBrand.val() === "")){
        displayError(selectBrand, lang('select_brand'));
        return lang('select_brand');
    }
    if(selectBrand.length > 0) {
        params["brands"] = selectBrand.val();
    }
    if(selectMemberShip.length > 0 && (selectMemberShip.val() === undefined || selectMemberShip.val() === "" || selectMemberShip.val() === "-1")){
        displayError(selectMemberShip, lang('please_select_membership'));
        return lang('please_select_membership');
    }
    if(selectMemberShip.length > 0) {
        params["membership"] = selectMemberShip.val();
    }
    if(price.val() === undefined || price.val() === ""){
        displayError(price,lang('shop_valid_js'));
        return  lang('shop_valid_js');
    }
    else if(price.val() < 0 || !digitsTest(price.val())){
        displayError(price,lang('price_fix_js'));
        return lang('price_fix_js');
    }
    params["itemPrice"] = price.val();
    if(name.val() === undefined || name.val() === ""){
        displayError(name,lang('add_name_js'));
        return lang('add_name_js');
    }
    params["itemName"] = name.val();
    params["taxInclude"] = 0;
    if($("#taxInclude").is(":checked")){
        params["taxInclude"] = 1;
    }
    // if(display === true){
    //     $(".fourthItemBlock").show("slow", "linear");
    //     $(".thirdItemBlock").show("slow", "linear");
    //     $(".SecondItemBlock").show("slow", "linear");
    //     $(".itemBlockLabelDiv").show("slow", "linear");
    //     $("#limitsBtn").css("display","block");
    // }
    return params;
}

function secondBlockCheck(){
    let param = {};
    if($("#time-Validation-checkbox").prop("checked") === false && $(".SecondItemBlock").is(":visible")){
        let timePeriod = $("#timePeriod");
        if(timePeriod.val() === "" || timePeriod.val() === undefined){
            displayError(timePeriod,lang('add_validity_js'));
            return  lang('add_validity_js');
        }
        else if(timePeriod.val() < 0 || !digitsTest(timePeriod.val())){
            displayError(timePeriod,lang('period_error_js'));
            return lang('period_error_js');
        }
        param["timePeriod"] = Math.floor(timePeriod.val()) + " " + $("#timePeriodDdl option:selected").val();
        param["renew-membership"] = 0;
        if($("#renew-membership").is(":checked")){
            param["renew-membership"] = 1;
        }
        if($("#timePeriodRenewCheck").prop("checked") === true){
            let timePeriodRenew = $("#timePeriodRenew");
            if(timePeriodRenew.val() === "" || timePeriodRenew.val() === undefined){
                displayError(timePeriodRenew.val(),lang('add_notification_time_js'));
                return lang('add_notification_time_js');
            }
            param["timePeriodRenew"] = Math.floor(timePeriodRenew.val()) + " " + $("#timePeriodRenewDdl option:selected").val();
        }
    }
    return param;
}

function thirdBlockCheck(){
    let params = {};
    let thirdBlocks = $(".thirdItemBlock");
    let message = "success";
    if($("#limits-checkbox").prop("checked") === false && thirdBlocks.is(":visible")){
        let classes = [];
        thirdBlocks.each(function () {
            let classesDet = {};
            let classesIds = $(this).find("#selectClassType").val();
            if(classesIds.length === 0){
                message = lang('add_class_js');
                displayError($(this).find("#selectClassType"),message);
                return false;
            }
            classesDet["classes"] = classesIds;
            let multiLimit = $(this).find(".MultiLimit");
            if(multiLimit.length >0) {
                let limits = {};
                multiLimit.each(function () {
                    let multiLim = {};
                    let inputLimit = $(this).find(".MultiLimitBlock");
                    if(inputLimit.val() === "-1"){
                            message = lang('add_purchase_limit_js');
                            displayError(inputLimit,message);
                            return false;
                    }
                    else if(inputLimit.val() === "1"){
                        let maxLimit = $("#MaxLimit");
                        if (maxLimit.val() === "" || maxLimit.val() === undefined) {
                            message = lang('add_max_limit_js');
                            displayError(maxLimit,message);
                            return false;
                        }
                        else if(maxLimit.val() < 0 || !digitsTest(maxLimit.val())){
                            displayError(maxLimit,lang('incorret_quantity_js'));
                            return false;
                        }
                        multiLim["limitType"] = inputLimit.val();
                        multiLim["maxLimit"] = maxLimit.val();
                        let maxSelect = $("#MaxSelectLimit");
                        multiLim["maxSelect"] = maxSelect.val();
                    }
                    else if(inputLimit.val() === "2"){
                        let daySelect = $("#DaysSelectLimit");
                        multiLim["limitType"] = inputLimit.val();
                        multiLim["daySelect"] = daySelect.val();
                    }
                    else if(inputLimit.val() === "3"){
                        let startTime = $("#startTime");
                        if(startTime.val() === "" || startTime.val() === undefined){
                            displayError(startTime,lang('add_start_hour_js'));
                            return false;
                        }
                        let endTime = $("#endTime");
                        if(endTime.val() === "" || endTime.val() === undefined){
                            displayError(startTime,lang('add_end_hour_js'));
                            return false;
                        }
                        if(endTime.val() < startTime.val()){
                            displayError(endTime,lang('end_time_late_start_js'));
                            return false;
                        }
                        multiLim["limitType"] = inputLimit.val();
                        multiLim["startTime"] = startTime.val();
                        multiLim["endTime"] = endTime.val();
                    }
                    limits[inputLimit.val()] = multiLim;
                    // else if(inputLimit < 0 || !digitsTest(inputLimit)){
                    //     message = "מגבלה לא תקינה";
                    //     displayError($(this).find("#LimitedItem"),message);
                    //     return false;
                    // }
                    // limit.push(Math.floor(inputLimit) + " " + $(this).find(".LimitCount option:selected").val());
                });
                classesDet["limits"] = limits;
            }
            classesDet["freeReg"] = 0;
            if($(this).find("#freeReg").is(":checked")){
                classesDet["freeReg"] = 1;
                let regNumber = $("#regNumber");
                if (regNumber.val() === "" || regNumber.val() === undefined) {
                    message = lang('add_max_class_limit_js');
                    displayError(regNumber,message);
                    return false;
                }
                else if(regNumber.val() < 0 || !digitsTest(regNumber.val())){
                    displayError(regNumber,lang('incorret_quantity_js'));
                    return false;
                }
                classesDet["regNumber"] = regNumber.val();
                let regTime = $("#regTime");
                classesDet["regTime"] = regTime.val();
                let regBeforeClass = $("#regBeforeClass");
                if (regBeforeClass.val() === "" || regBeforeClass.val() === undefined) {
                    message = lang('add_booking_before_class_js');
                    displayError(regBeforeClass,message);
                    return false;
                }
                else if(regBeforeClass.val() < 0 || !digitsTest(regBeforeClass.val())){
                    displayError(regBeforeClass,lang('incorrect_time_js'));
                    return false;
                }
                classesDet["regBefore"] = regBeforeClass.val();
                let regTimeBefore = $("#regBeforeClassTime");
                classesDet["regTimeBefore"] = regTimeBefore.val();

            }
            classes.push(classesDet);
        });
        params["classes"] = classes;
    }
    if(message === "success"){
        return params;
    }
    return message;
}

function fourthBlockCheck(){
    let params = {};
    let appCheck = $("#application-checkbox");
    params["appCheck"] = appCheck.prop("checked");
    if(appCheck.prop("checked") === true && $(".fourthItemBlock").is(":visible")){
        let appName = $("#ApplicationName");
        let appDesc = $("#shopItemAppDesc");
        let appPrice = $("#shopItemAppPrice");
        let message = "success";
        if(appName.val() === "" || appName.val() === undefined){
            displayError(appName,lang('add_product_name_app_js'));
            return lang('add_product_name_app_js');
        }
        params["appName"] = appName.val();
        if(appPrice.val() === "" || appPrice.val() === undefined){
            displayError(appPrice,lang('add_product_price_app_js'));
            return lang('add_product_price_app_js');
        }
        else if(appPrice.val() < 0 || !digitsTest(appPrice.val())){
            displayError(appPrice,lang('price_fix_js'));
            return lang('price_fix_js');
        }
        params["appPrice"] = appPrice.val();
        if(appDesc.val() === "" || appDesc.val() === undefined){
            displayError(appDesc,lang('add_product_description_app_js'));
            return lang('add_product_description_app_js');
        }
        params["dealType"] = $("#dealType").attr("data-id");
        params["appDesc"] = appDesc.val();
        if($("#shopPayment").is(":visible")){
            let paymentNum = $("#PaymentNum");
            if(paymentNum.val() === "" || paymentNum.val() === undefined){
                message =  lang('add_purschase_count_limit_js');
                displayError(paymentNum,message);
                return false;
            }
            else if(paymentNum.val() <= 0 || !digitsTest(paymentNum.val())){
                message = lang('incorrect_count_js');
                displayError(paymentNum,message);
                return false;
            }
            params["paymentNum"] = Math.floor(paymentNum.val());
            if(Number(paymentNum.val()) > 1){
                params["credit-payment"] = 0;
                if($("#credit-payment").is(":checked")){
                    params["credit-payment"] = 1;
                }
                params["paymentFrame"] = 0;
                if($("#paymentFrame").is(":checked")){
                    params["paymentFrame"] = 1;
                }
            }
        }
        let limitBlock = $(".limitAppBlock");

        let limits = [];
        limitBlock.each(function () {
            let limit = {};
            let selected  = $(this).find(".blockSelectLimit option:selected").val();
            if(selected !== "-1") {
                limit["selected"] = selected;
            }
            if(selected === "-1"){
                return true;
            }
            else if (selected === "1"){
                let itemLimitId = $("#itemLimitId");
                if(itemLimitId.val() === "" || itemLimitId.val() === undefined){
                    message =  lang('add_purschase_count_limit_js');
                    displayError(itemLimitId,message);
                    return false;
                }
                else if(itemLimitId.val() <= 0 || !digitsTest(itemLimitId.val())){
                    message = lang('incorrect_count_js');
                    displayError(itemLimitId,message);
                    return false;
                }
                limit["itemLimitId"] = Math.floor(itemLimitId.val());
            }
            else if (selected === "2"){
                limit["gender"] = $("input[name='gender']:checked").val();
            }
            else if (selected === "3"){
                let startAge = $("#itemStartAge");
                let endAge = $("#itemEndAge");
                if(startAge.val() === "" || startAge.val() === undefined || endAge.val() === "" || endAge.val() === undefined ){
                    message = lang('add_age_js');
                    displayError(startAge,message);
                    return false;
                }
                else if(startAge.val() < 0 || !digitsTest(startAge.val()) || endAge.val() < 0 || !digitsTest(endAge.val())){
                    message = lang('incorrect_age_js');
                    displayError(startAge,message);
                    return false;
                }
                limit["startAge"] = Math.floor(startAge.val());
                limit["endAge"] = Math.floor(endAge.val());
            }
            else if (selected === "4"){
                let rank = $("#rankSelectLimit option:selected");
                if(rank.val() === "-1"){
                    displayError(rank,message);
                    message = lang('select_rank_js');
                    return false;
                }
                limit["rank"] = rank.val();
            }
            else if (selected === "5"){
                let seniority = $("#DateApp");
                if(seniority.val() === ""){
                    message = lang('select_date');
                    displayError(seniority,message);
                    return false;
                }
                limit["seniority"] = seniority.val();
            }
            else if (selected === "6"){
                limit["limitClassType"] = $("#TypeSelectLimit option:selected").val();
            }
            else{
                message = "Something Went Wrong";
            }
            limits.push(limit);
        });
        if(message === "success"){
            if(limits.length > 0) {
                params["limits"] = limits;
            }
            return params;
        }
    }
    return params;
}

function itemMainBlockCheck() {
    let params = {};
    let itemPrice = $("#addItemPrice");
    let selectDep = $("#departmentDdl");
    let name = $("#additemName");
    let priceSale = $("#shopItemPrice");
    if(itemPrice.val() === undefined || itemPrice.val() === ""){
        displayError(itemPrice,lang('shop_valid_js'));
        return  lang('shop_valid_js');
    }
    else if(itemPrice.val() < 0 || !digitsTest(itemPrice.val())){
        displayError(itemPrice,lang('price_fix_js'));
        return lang('price_fix_js');
    }
    params["itemPrice"] = itemPrice.val();

    if(selectDep.length > 0 && (selectDep.val() === undefined || selectDep.val() === "" || selectDep.val() === "-1")){
        displayError(selectDep,lang('select_category_js'));
        return lang('select_category_js');
    }
    params["category"] = selectDep.val();
    if(name.val() === undefined || name.val() === ""){
        displayError(name,lang('add_name_js'));
        return lang('add_name_js');
    }
    params["itemName"] = name.val();
    if(priceSale.val() === undefined || priceSale.val() === ""){
        displayError(priceSale, lang('shop_valid_js'));
        return  lang('shop_valid_js');
    }
    else if(priceSale.val() < 0 || !digitsTest(priceSale.val())){
        displayError(priceSale, lang('price_fix_js'));
        return lang('price_fix_js');
    }
    params["priceSale"] = priceSale.val();
    params["taxInclude"] = 0;
    if($("#taxInclude").is(":checked")){
        params["taxInclude"] = 1;
    }
    return params;

}

function itemDetailsBlockCheck() {
    let params = {};
    let itemBarcode = $("#itemBarcode");
    let itemSku = $("#itemSku");
    let itemSize = $("#itemSize");
    let itemColor = $("#itemColor");
    let itemStock = $("#itemStock");
    let itemSupplier = $("#itemSupplier");
    if(itemBarcode.val() !== undefined && itemBarcode.val() !== ""){
        params["itemBarcode"] = itemBarcode.val();
    }

    if(itemSku.val() !== undefined && itemSku.val() !== ""){
        params["itemSku"] = itemSku.val();
    }
    if(itemSize.val() !== undefined && itemSize.val() !== ""){
        params["itemSize"] = itemSize.val();
    }
    if(itemColor.val() !== undefined && itemColor.val() !== ""){
        params["itemColor"] = itemColor.val();
    }
    if(itemStock.val() !== undefined && itemStock.val() !== ""){
        params["itemStock"] = itemStock.val();
    }
    if(itemSupplier.val() !== undefined && itemSupplier.val() !== ""){
        params["itemSupplier"] = itemSupplier.val();
    }
    if(!isEmpty(params)){
        return params;
    }
}

function paymentMainBlockCheck() {
    let params = {};
    let name = $("#linkHeadline");
    if(name.val() === undefined || name.val() === ""){
        displayError(name, lang('add_url_title_js'));
        return lang('add_url_title_js');
    }
    params["linkName"] = name.val();
    let selectBrand = $("#shopCmpBranch");
    if(selectBrand.length > 0 && (selectBrand.val() === undefined || selectBrand.val() === "")){
        displayError(selectBrand, lang('select_brand'));
        return lang('select_brand');
    }
    if(selectBrand.length > 0) {
        params["brands"] = selectBrand.val();
    }
    let linkContent = $("#linkContent");
    if(linkContent.val() === undefined || linkContent.val() === ""){
        displayError(linkContent, lang('add_url_content_js'));
        return lang('add_url_content_js');
    }
    params["linkContent"] = linkContent.val();
    let linkRedirect = $("#linkRedirect");
    if(linkRedirect.val() === undefined || linkRedirect.val() === ""){
        displayError(linkRedirect, lang('add_redirect_js'));
        return lang('add_redirect_js');
    }
    params["linkRedirect"] = linkRedirect.val();
    return params;
}

function PayItemBlockCheck(){
    let params = {};
    let saleType = $("#saleType option:selected").val();
    if(saleType === "1"){
        params["saleType"] = saleType;
        let membershipType = $("#membershipNameDDl option:selected");
        if(membershipType.val() === "" || membershipType.val() === undefined){
            displayError(membershipType, lang('select_subscription_type_js'));
            return lang('select_subscription_type_js');
        }
        params["membershipType"] = membershipType.val();

        let membershipPrice = $("#membershipPrice");
        if(membershipPrice.val() === undefined || membershipPrice.val() === ""){
            displayError(membershipPrice, lang('add_membership_price_js'));
            return  lang('add_membership_price_js');
        }
        else if(membershipPrice.val() < 0 || !digitsTest(membershipPrice.val())){
            displayError(membershipPrice, lang('price_fix_js'));
            return lang('price_fix_js');
        }
        params["membershipPrice"] = membershipPrice.val();

        let department = membershipType.attr("data-dep");
        if(department !== "1" && department !== "2" && department !== "3"){
            return "Something Went Wrong";
        }
        params["department"] = department;
    }
    else if (saleType === "2"){
        params["saleType"] = saleType;
    }
    return params;
}

function CalcMembershipBlockCheck(department) {
    let params = {};
    if(department === "1" || department === "2"){
        let startCollect = $("input[name='memCalc']:checked").val();
        if(startCollect === "1"){
            params["startCollect"] = startCollect;
            params["regularStart"] = $("input[name='memCalcRegular']:checked").val();
        }
        else if(startCollect === "2"){
            params["startCollect"] = startCollect;
            let startDate = $("#startDateMem");
            let endDate = $("#endDateMem");
            if(endDate.val() === undefined || endDate.val() === "" || startDate.val() === undefined || startDate.val() === "" ){
                if(endDate.val() === undefined || endDate.val() === ""){
                    displayError(endDate.val(),  lang('add_dates_js'));
                }
                else{
                    displayError(startDate.val(),  lang('add_dates_js'));
                }
                return lang('add_dates_js');
            }
            if(new Date(endDate.val()) < new Date(startDate.val()))
            {
                displayError(startDate,  lang('start_date_late_end_js'));
                return lang('start_date_late_end_js');
            }
            params["startDate"] = startDate.val();
            params["endDate"] = endDate.val();
            params["latePurchase"] = 0;
            if($("#latePurchase").is(":checked")){
                params["latePurchase"] = 1;
                let untilDate = $("#untilDateMem");
                if(untilDate.val() === undefined || untilDate.val() === ""){
                    displayError(untilDate,  lang('add_date_js'));
                    return lang('add_date_js');
                }
                params["untilDate"] = untilDate.val();
                params["priceCut"] = 0;
                if($("#priceCut").is(":checked")) {
                    params["priceCut"] = 1;
                    params["priceCutDDl"] = $("#priceCutDDl option:selected").val();
                }
            }
        }

    }
    else if(department === "3"){
        params["ticketRadio"] = $("input[name='ticketRadio']:checked").val();
        params["relativeTicketPrice"] = 0;
        if($("#relativeTicketPrice").is(":checked")){
            params["relativeTicketPrice"] = 1;
        }
    }
    if(!isEmpty(params)){
        return params;
    }
}