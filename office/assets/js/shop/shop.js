$(document).ready(function() {
    //DataTable
    $("#categories_wrapper > .dt-buttons").appendTo(".shop-top-bar");

    $("#selectClassType" ).select2({
        theme:"bootstrap",
        'language':"he",
        dir: "rtl"
    });

    $("#itemSize" ).select2({
        theme:"bootstrap",
        'language':"he",
        dir: "rtl",
        tags: true,
        selectOnClose: true
    });

    $("#itemColor" ).select2({
        theme:"bootstrap",
        'language':"he",
        dir: "rtl",
        tags: true,
        selectOnClose: true
    });

    $("#departmentDdl" ).select2({
        theme:"bootstrap",
        'language':"he",
        dir: "rtl",
        tags: true,
        selectOnClose: true
    });





    let dtTable = $('#categories').DataTable();
    $('#search-shop').keyup(function () {
        dtTable.search($(this).val()).draw();
    });
    let table = $("#categories");
    $(table).on("click",".shop-dots-btn",function () {
        let item = $(this).children(".rowBox");
        item.addClass("rowBox-active")
    });
    $(table).on("mouseleave",".shop-dots-btn",function () {
        let item = $(this).children(".rowBox");
        item.removeClass("rowBox-active")
    });
    $(table).on("click",".rowBox-item",function(){
        let item = $(this);
        let row_id = item.parents('.shop-dots-btn').attr('data-id');
        if(item.attr('id') === "rowBoxEdit"){
            alert("rowBoxEdit_" + row_id)
        }
        else if(item.attr('id') === "rowBoxPause"){
            alert("rowBoxPause_" + row_id)
        }
        else if(item.attr('id') === "rowBoxDel"){
            alert("rowBoxDel_" + row_id)
        }
    });
    let page_content = $("#page-content-wrapper");
    $(page_content).on("click",".shop-float, #shop-back-btn",function () {
        let item = $(this);
        let action = item.attr('data-id');
        let shopNewItems = $(".shopNewItems");
        let newShopPage = $(".newShopPage");
        shopNewItems.slideToggle("slow","linear");
        newShopPage.slideToggle("slow","linear");
        if(action === "1"){
            console.log("membership");
        }
        else if(action === "2"){
            console.log("tickets");
        }
        else if(action === "3"){
            console.log("trails");
        }
        else if(action === "4"){
            console.log("items");
        }
    });


    //Add Items


    // let blockInput = $(".blockInput");
    // $(blockInput).focus(function () {
    //     let label = $("label[for='" + $(this).attr('id') + "']");
    //     label.css("color","#30C93D");
    // });
    // $(blockInput).blur(function () {
    //     let label = $("label[for='" + $(this).attr('id') + "']");
    //     label.css("color","#A1A1A1");
    // });
    $("#time-Validation-checkbox").click(function () {
        $(".SecondItemBlock").slideToggle("slow","linear");
        let deal = $("#dealType");
        if($(this).prop("checked") === true){
            deal.text(lang('regular_one_time_shop_render'));
            deal.attr("data-id", 1);
            $("#shopPayment").show();
        }
        else{
            deal.text(lang('recurring_billing_membership'));
            deal.attr("data-id", 2);
            $("#shopPayment").hide();
        }
    });
    $("#PaymentNum").bind('keyup mouseup', function () {
        if($(this).val() > 1){
            $("#paymentFrame").removeAttr("disabled");
            $("#credit-payment").removeAttr("disabled");
        }
        else{
            $("#paymentFrame").attr("disabled", true);
            $("#credit-payment").attr("disabled", true);
            // $("#credit-payment").attr("checked",true);
            // $("#paymentFrame").attr("checked",true);
        }
    });
    $("#limits-checkbox").click(function () {
        $(".thirdItemBlock").slideToggle("slow","linear");
        $("#limitsBtn").slideToggle("slow","linear");
    });
    $("#application-checkbox").click(function () {
        $(".fourthItemBlock").slideToggle("slow","linear");
    });
    $(page_content).on("click","#itemLimitsBtn",function (e) {
        e.preventDefault();
        // $(this).before('<div class="itemBlockDiv-row MultiLimit limitBlock">' +
        //     '<input type="number" id="LimitedItem" class="blockInput blockInputLimit" />' +
        //     '<select class="blockInput LimitCount">' +
        //     '<option value="days">ביום</option>' +
        //     '<option value="weeks">בשבוע</option>' +
        //     '<option value="months">בחודש</option>' +
        //     '<option value="years">בשנה</option>' +
        //     '</select>' +
        //     '<i class="fal fa-times limitRemove"></i>' +
        //     '</div>')
        $(this).before('<div class="itemBlockDiv no-padding-row itemBlockDiv-row limitBlock MultiLimit" style="width: 100%">' +
            '<i class="fal fa-times limitRemove"></i>'+
            '<select class="blockInput MultiLimitBlock" >' +
            '<option value="-1">'+lang('select_restriction_js')+'</option>' +
            '<option value="1">'+lang('max_js')+'</option>' +
            '<option value="2">'+lang('days')+'</option>' +
            '<option value="3">'+lang('hours')+'</option>' +
            '</select>' +
            '</div>');
    });
    $(page_content).on("change",".MultiLimitBlock",function () {
        let selectVal = $(this).val();
        if($(this).siblings(".LimitDivSelect").length > 0){
            $(this).siblings(".LimitDivSelect").remove();
        }
        if(selectVal === "1"){
            $(this).after('<div class="LimitDivSelect"><label class="shopLabel shopLabelLimit">'+lang('select_max_restrictions_js')+'</label>' +
                '<input type="number" id="MaxLimit" class="blockInput blockInputLimit" />' +
                '<select id="MaxSelectLimit" class="blockInput blockInputLimit">' +
                '<option value="day">'+lang('day')+'</option>'+
                '<option value="week">'+lang('week')+'</option>'+
                '<option value="month">'+lang('month')+'</option>'+
                '<option value="year">'+lang('year_js')+'</option>'+
                '<option value="morning">'+lang('morning_js')+'</option>'+
                '<option value="evening">'+lang('evening_js')+'</option>'+
                '</select>'+
                '</div>')
        }
        else if(selectVal === "2"){
            $(this).after('<div class="LimitDivSelect MultiSelectDiv"><label class="shopLabel shopLabelLimit">'+lang('select_days_restriction_js')+'</label>' +
                '<select class="js-example-basic-single select2multipleDesk text-right" name="Days[]" id="DaysSelectLimit" dir="rtl"   multiple="multiple" data-select2order="true" style="width: 30%;">' +
                '<option value="1">'+lang('sunday')+'</option>'+
                '<option value="2">'+lang('monday')+'</option>'+
                '<option value="3">'+lang('thursday')+'</option>'+
                '<option value="4">'+lang('wednesday')+'</option>'+
                '<option value="5">'+lang('thursday')+'</option>'+
                '<option value="6">'+lang('friday')+'</option>'+
                '<option value="7">'+lang('saturday')+'</option>'+
                '</select>'+
                '</div>');
                $("#DaysSelectLimit" ).select2({
                    theme:"bootstrap",
                    'language':"he",
                    dir: "rtl"
                });

        }
        else if(selectVal === "3"){
            $(this).after('<div class="LimitDivSelect"><label class="shopLabel shopLabelLimit">'+lang('start_hour')+'</label>' +
                '<input type="time" id="startTime" class="blockInput InputLimit" />' +
                '<label class="shopLabel">'+lang('end_hour')+'</label>' +
                '<input type="time" id="endTime" class="blockInput InputLimit" />' +
                '</div>')
        }
    });
    $(page_content).on("focus",".shopSelect",function () {
        $(this).find("option").each(function () {
            let val = $(this).val();
            if(val === ''){
                $(this).hide();
            }
        })
    });
    $(page_content).on("click","#freeReg",function () {
        if($(this).is(":checked")){
            $(this).parent().parent().parent().find(".freeSpaceReg").show();
        }
        else{
            $(this).parent().parent().parent().find(".freeSpaceReg").hide();
        }
    });
    $("#addItemBtn").click(function (e) {
        e.preventDefault();
        let itemBtn = $(this);
        let count = $(".newItemBlock").last().attr("data-id");
        if(count === undefined){
            count = Number(1);
        }
        else{
            count = Number(count) + Number(1);
        }
        let options = '<option class="itemOption" data-id="-1">'+lang('select_item')+'</option>';
        $.each(items,function (ind,value) {
            options += '<option class="itemOption" data-id="'+ind+'">'+ value[0] +'</option>'
        });
        $(itemBtn).parent().before('<div class="itemBlockDiv-row newItemBlock" data-id="'+count+'">' +
        '<div class="itemBlockDiv no-padding-row" style="width: 25%">' +
            '<select id="ItemSelectLimit" class="blockInput blockInputLimit">' + options +
            '</select>'+
        '</div>' +
        '<div class="itemBlockDiv no-padding-row" style="width: 25%">' +
        '<input id="itemPrice" class="blockInput"></div>' +
        '<div class="itemBlockDiv no-padding-row newItemBlockRemove">' +
        '<i class="fal fa-times newItemRemove"></i></div></div>');
    });
    $(page_content).on("change","#ItemSelectLimit",function () {
        let itemId = $(this).find("option:selected").attr("data-id");
        let item = items[itemId];
        $(this).parent().parent().find("#itemPrice").val(item[1]);
    });
    $(page_content).on("click",".newItemRemove",function () {
        $(this).parent().parent().remove();
    });

    $("#itemLimitsAppBtn").click(function (e) {
        e.preventDefault();
        let itemlimit = $(this);
        let limitblock = $(".limitBlock");
        let selectValues = [];
        limitblock.each(function () {
            selectValues.push($(this).find(".blockSelectLimit").val());
        });
        $(itemlimit).before('<div class="itemBlockDiv no-padding-row itemBlockDiv-row limitBlock limitAppBlock" style="width: 100%">' +
            '<i class="fal fa-times limitRemove"></i>'+
            '<select class="blockInput blockSelectLimit" >' +
            '<option value="-1">'+lang('select_restriction_js')+'</option>' +
            '<option value="1">'+lang('add_client_restriction_number_js')+'</option>' +
            '<option value="2">'+lang('restriction_by_age_js')+'</option>' +
            '<option value="3">'+lang('restrict_age_range_js')+'</option>' +
            '<option value="4">'+lang('restrict_by_rank_js')+'</option>' +
            '<option value="5">'+lang('restrict_by_seniority_js')+'</option>' +
            '<option value="6">'+lang('restrict_by_subscription_type_js')+'</option>' +
            '</select>' +
            '</div>');
        let lastLimit = itemlimit.parent().find(".limitBlock").last();
        lastLimit.find("option").each(function () {
            if(selectValues.includes($(this).val()) && $(this).val() !== "-1"){
                $(this).remove();
            }
        })

    });
    $("#saleType").change(function () {
        let selectVal = $(this).val();
        if(selectVal === "1"){
            $(".membershipSelection").show();
            $(".CalcMembershipBlock").show();
            $(".CalcMembershipBlockDiv").show();
            $(".itemsSelection").hide();
        }
        else if(selectVal === "2"){
            $(".membershipSelection").hide();
            $(".CalcMembershipBlock").hide();
            $(".CalcMembershipBlockDiv").hide();
            $(".itemsSelection").show();
        }
    });
    $("#membershipNameDDl").change(function () {
        let price = $(this).find(":selected").attr("data-price");
        let department = $(this).find(":selected").attr("data-dep");
        $("#membershipPrice").val(price);
        if(department === "2"){
           $(".calcRadioDiv").hide();
           $(".calcTicketDiv").show();
           $(".PayClassesBlock").show();
           $(".PayClassesBlockDiv").show();
        }
        else{
            $(".calcRadioDiv").show();
            $(".calcTicketDiv").hide();
            $(".PayClassesBlock").hide();
            $(".PayClassesBlockDiv").hide();
        }
    });

    $('input:radio[name=memCalc]').change(function () {
        if($(this).val() === "1"){
            $("#regularMemCalc").show();
            $("#definedMemCalc").hide();
            $(".PayClassesBlock").hide();
        }
        if($(this).val() === "2"){
            $("#regularMemCalc").hide();
            $("#definedMemCalc").show();
            $(".PayClassesBlock").show();
        }
    });
    $("#latePurchase").click(function () {
        if($(this).prop("checked") === true){
            $(".untilDiv").show();
        }
        else{
            $(".untilDiv").hide();
        }
    });
    $(".fourthItemBlock").on("change",".blockSelectLimit", function () {
        let selectVal = $(this).val();
        if($(this).siblings(".selectSib").length > 0){
            $(this).siblings(".selectSib").remove();
        }
        if(selectVal === "1"){
            $(this).after('<div class="selectSib"><label class="shopLabel shopLabelLimit">'+lang('coupon_limit')+'</label>' +
                 '<input type="number" id="itemLimitId" class="blockInput blockInputLimit" />' +
                 '<label class="shopLabel">'+lang('purchase_for_customer')+'</label>' +
                 '</div>')
        }
        else if(selectVal === "2"){
            $(this).after('<div class="selectSib">' +
                '<label>'+
                '<input type="radio" class="rdo-input" checked name="gender" value="male">' +
                '<span class="rdo"></span><span>'+lang('male')+'</span>'+
                '</label>'+
                '<label>' +
                '<input type="radio" class="rdo-input" name="gender" value="female">'+
                '<span class="rdo"></span><span>'+lang('female')+'</span>'+
                '</label>' +
                '</div>');
        }
        else if(selectVal === "3"){
            $(this).after('<div class="selectSib"><label class="shopLabel shopLabelLimit">'+lang('from_age_js')+'</label>' +
                '<input type="number" id="itemStartAge" class="blockInput blockInputLimit" />' +
                '<label class="shopLabel">'+lang('to_age')+'</label>' +
                '<input type="number" id="itemEndAge" class="blockInput blockInputLimit" />' +
                '</div>')
        }
        else if(selectVal === "4"){
            $(this).after('<div class="selectSib"><label class="shopLabel shopLabelLimit">'+lang('select_rank_js')+'</label>' +
                '<select id="rankSelectLimit" class="blockInput blockInputLimit">' +
                '<option value="-1">'+lang('select_rank_js')+'</option>'+
                '<option value="beginner">'+lang('start_js')+'</option>'+
                '<option value="advanced">'+lang('advanced_js')+'</option>'+
                '</select>'+
                '</div>')
        }
        else if(selectVal === "5"){
            $(this).after('<div class="selectSib itemBlockDiv-row"><label class="shopLabel shopLabelLimit">'+lang('late_join_date_js')+'</label>' +
                '<input placeholder="" id="DateApp" required class="blockInput" type="date">' +
                '</div>')
        }
        else if(selectVal === "6"){
            let options = ""
            $.each(memberType,function (ind,value) {
                options += '<option class="TypeOption" value="'+ind+'">'+ value +'</option>'
            });
            $(this).after('<div class="selectSib"><label class="shopLabel shopLabelLimit">'+lang('select_subscription_type_js')+'</label>' +
                '<select id="TypeSelectLimit" class="blockInput blockInputLimit">' + options +
                '</select>'+
                '</div>')
        }
    });
    $(page_content).on("click",".limitRemove", function () {
        $(this).parent().remove();
    });


    $("#limitsBtn").on("click",function (e) {
        e.preventDefault();
        let thirdBlocks = $(".thirdItemBlock");
        let thirdElem = thirdBlocks.first().clone();
        thirdElem.prepend('<i class="fal fa-times limitRemove limitRemoveBlock"></i>');
        thirdElem.css("display","block");
        thirdElem.find(".freeSpaceReg").css("display","none");
        thirdElem.find(".select2block").find("span").remove();
        let select = thirdElem.find("#selectClassType");

        //removes selected options
        let selectValues = [];
        thirdBlocks.each(function () {
            let values = $(this).find("#selectClassType").val();
            $.each(values,function (ind,val) {
                selectValues.push(val);
            })
        });
        select.find("option").each(function (ind, val) {
            let s = val;
            if(selectValues.includes(val.value)){
                $(this).remove();
            }
        });
        select.select2( {theme:"bootstrap", 'language':"he", dir: "rtl" } );
        thirdElem.find(".CheckboxLabel input").prop("checked", false);
        thirdElem.find(".MultiLimit").remove();
        $(this).before(thirdElem)
    })

    $(".filesToUpload").change(function () {
        let label  = $(this).prev('label');
        let fileName = $('.filesToUpload')[0].files[0].name;
        label.text(fileName);
        label.removeAttr("hidden");
    })


    //MOBILE

    $("#pageNavSelect").change(function () {
        let selectVal = $(this).val();
        window.location.href = "/office/itemsnew.php?kind=" + selectVal;
    })
});