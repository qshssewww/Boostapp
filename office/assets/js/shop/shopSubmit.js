$(document).ready(function() {
    function checkForFiles() {
        let filesInput = $(".filesToUpload");
        let files = [];
        if($(".filesToUploadLabel").is(":visible")){
            filesInput.each(function () {
                if($(this).get(0).files.length > 0) {
                    files.push($(this).prop("files")[0]);
                }
            })
        }
        return files
    }

    function ajaxSubmit(data){
        let fd = new FormData();
        let files = checkForFiles();
        $.each(files,function (ind,val) {
            if(ind === 0) {
                fd.append('image', val);
            }
            else if(ind === 1){
                fd.append('file', val);
            }
        });
        fd.append("data",JSON.stringify(data));
        $.ajax({
            url: "/office/ajax/ajaxShop.php",
            type: "post",
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
    function membershipPage(tabType){
        let data = {};
        data["page"] = tabType;
        let type = $("#newMembershipDDL option:selected").val();
        if(type === "1" || type === "2" || type === "3"){
            data["type"] = type;
        }
        else {
            return "Something Went Wrong";
        }
        let firstBlock = firstBlockCheck();
        if(typeof firstBlock === "string"){
            return firstBlock;
        }
        data["firstBlock"] = firstBlock;

        let secondBlock = secondBlockCheck();
        if(typeof secondBlock === "string"){
            return secondBlock;
        }
        data["secondBlock"] = secondBlock;

        let calcMembershipBlock =  CalcMembershipBlockCheck("1");
        if(typeof calcMembershipBlock === "string"){
            return calcMembershipBlock;
        }
        data["calcMembershipBlock"] = calcMembershipBlock;

        let thirdBlock = thirdBlockCheck();
        if(typeof thirdBlock === "string"){
            return thirdBlock;
        }
        data["thirdBlock"] = thirdBlock;

        let fourthBlock = fourthBlockCheck();
        if(typeof fourthBlock === "string"){
            return fourthBlock;
        }
        data["fourthBlock"] = fourthBlock;

        return data;
    }

    function itemPage(tabType){
        let data = {};
        data["page"] = tabType;
        let mainItemBlock = itemMainBlockCheck();
        if(typeof mainItemBlock === "string"){
            return mainItemBlock;
        }
        data["mainItemBlock"] = mainItemBlock;
        let itemDetails = itemDetailsBlockCheck();
        if(itemDetails !== undefined && itemDetails !== ""){
            data["itemDetails"] = itemDetails;
        }
        let fourthBlock = fourthBlockCheck();
        if(fourthBlock !== undefined && fourthBlock !== ""){
            data["fourthBlock"] = fourthBlock;
        }
        return data;
    }

    function insurancePage(tabType){
        let data = {};
        data["page"] = tabType;
        let firstBlock = firstBlockCheck();
        if(typeof firstBlock === "string"){
            return firstBlock;
        }
        data["firstBlock"] = firstBlock;
        let secondBlock = secondBlockCheck();
        if(typeof secondBlock === "string"){
            return secondBlock;
        }
        return data;
    }

    function paymentPage(tabType){
        let data = {};
        data["page"] = tabType;
        let paymentMainBlock = paymentMainBlockCheck();
        if(typeof paymentMainBlock === "string"){
            return paymentMainBlock;
        }
        data['paymentMainBlock'] = paymentMainBlock;
        let PayItemBlock = PayItemBlockCheck();
        if(typeof PayItemBlock === "string"){
            return PayItemBlock;
        }
        data['PayItemBlock'] = PayItemBlock;
        if(PayItemBlock['saleType'] === "1"){
            let calcMembershipBlock = CalcMembershipBlockCheck(PayItemBlock['department']);
            if(typeof calcMembershipBlock === "string"){
                return calcMembershipBlock;
            }
            data["calcMembershipBlock"] = calcMembershipBlock;
        }
        return data;
    }

    $(".shopSubmit").on("click",function (e) {
        e.preventDefault();
        let errors = $(".inputError");
        if(errors.length > 0){
            errors.each(function () {
                $(this).parent().find(".blockInput").css("border-color","#a1a1a1");
                $(this).remove();
            })
        }
        let data;
        let tabType = $("#tabType").val();
        if(tabType === "1" || tabType === "2" || tabType === "3") {
            data = membershipPage(tabType);
            //To Do Error message
            if(typeof  data === "string") {
                return;
            }
        }
        else if(tabType === "4"){
            data = itemPage(tabType);
            if(typeof  data === "string") {
                return;
            }
        }
        else if(tabType === "5"){
            data = paymentPage(tabType);
            if(typeof  data === "string") {
                return;
            }
        }
        else if(tabType === "6"){
            data = insurancePage(tabType);
            if(typeof  data === "string") {
                return;
            }
        }
        ajaxSubmit(data);
    })
});