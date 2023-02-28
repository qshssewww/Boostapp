
$(document).ready(function () {
  //color picker functionality
  $(document).on("click",".colorGrid .colorCube",function(evt) {

    evt.stopPropagation();
    let color = $(this).css("background-color");
    $(this).parent().parent().prev().prev().prev().css("background-color", color);
    $(this).parent().parent().prev().prev().prev().attr("data-id",$(this).attr("id") || $(this).attr("data-id"));
    //$(".colorGridSelect .selectedColor").css("background-color", color);
    $("#selectedColor").val(color || 0);
    $(".colorGridContainer").hide();
  });

  $(document).on("click",".colorGridSelect",function() {
    $(this).find(".colorGridContainer").toggle();
  });
  let mouse_is_inside;
  // $(document).on("click",".colorGridSelect",hover(){
  //     function() {
  //       mouse_is_inside = true;
  //     },
  //     function() {
  //       mouse_is_inside = false;
  //     }
  // });

  $("#mainPopup").mouseup(function() {
    if (!mouse_is_inside) $(".colorGridContainer").hide();
  });
  $('#select1').on("click", function () {
    $('#select-type').val(1).trigger('change');
    $(this).find('input:radio[name="customRadio"]').prop("checked", true);
  })
  $('#select4').on("click", function () {
    $('#select-type').val(4).trigger('change');
    $(this).find('input:radio[name="customRadio"]').prop("checked", true);

  })
  $('#select5').on("click", function () {
    $('#select-type').val(5).trigger('change');
    $(this).find('input:radio[name="customRadio"]').prop("checked", true);
  })
  function emptyAllPopups() {
    emptyMembershipPopup(1);
    emptyProductPopup();
    emptyLinkPopup();
    emptyMembershipPopup(4);
    emptyMembershipPopup(5);
    setInsertText();
    clearMainSelect();
  }
  // $("#select-type").select2({
  //   minimumResultsForSearch: -1,
  // });
  $("#select-type-secondary").select2({
    minimumResultsForSearch: -1,
    theme: "bsapp-dropdown"
  });
  $("#smartLinkHiddenProduct").select2({
    minimumResultsForSearch: -1,
    theme: "bsapp-dropdown"
  });
  $("#add-item").click(function () {
    type = 2;
    closePopup("selectPopup");
    emptyAllPopups();
    openPopup("mainShopPopup");
    $(`.hiddenPopupSection[data-id="2"]`).show();
    $("#select-type-secondary").val("2").trigger("change");
  });
  $("#add-link").click(function () {
    type = 3;
    closePopup("selectPopup");
    emptyAllPopups();
    openPopup("mainShopPopup");
    $(`.hiddenPopupSection[data-id="3"]`).show();
    $("#select-type-secondary").val("3").trigger("change");
  });
  $("#select-type").change(function () {
    if ($(this).val() != "0") {
      closePopup("selectPopup");
      emptyAllPopups();
      openPopup("mainShopPopup");
      $("#select-type-secondary").val($(this).val()).trigger("change");
      $(this).val("0").trigger("change");
    }
  });
  $("#select-type-secondary").change(function () {
    emptyAllPopups();
    type = $(this).val();
    $(".hiddenPopupSection").hide();
    if ($(this).val() == "1" || $(this).val() == "4" || $(this).val() == "5") {
      // $(".rowInput").hide();
      // $(".priceRow").show();
    }
    $(`.hiddenPopupSection[data-id="${$(this).val()}"]`).show();
  });

  function onMainPopupSave() {
    let type = $("#select-type-secondary").val();
    let objData = generateDataObj(type);
    console.log(objData);
    if (objData) {
      if (objData.id) {
        if (type == 1 || type == 4 || type == 5) {
          updateMembership(objData)
        } else if (type == 2) {
          updateProduct(objData)
        } else if (type == 3) {
          updateLink(objData)
        }
      } else {
        if (type == 1 || type == 4 || type == 5) {
          createMembership(objData)
        } else if (type == 2) {
          createProduct(objData)
        } else if (type == 3) {
          createLink(objData)
        }
      }

    }
  }

  function generateDataObj(type) {
    let objData;
    if (type == 1 || type == 4 || type == 5) {
      //מנוי חדש
      objData = generateDataObj145(type);
    } else if (type == 2) {
      //מוצר
      objData = generateDataObj2();
    } else if (type == 3) {
      //לינק חכם
      objData = generateDataObj3();
    }
    return objData;
  }

  function isVAlid(jObj, msg, select = false) {
    if (jObj.val() && jObj.val() != "0" && !select) {
      jObj.css({ "border-bottom": "1px solid black" });
      jObj.siblings('.errorMsg').hide(function () {
        $(this).remove();
      });
      return true;
    }
    else if(select == true && jObj.val() == -1){
      jObj.parent().find('.errorMsg').hide(function () {
        $(this).remove();
      });
      jObj.css({ "border-bottom": "1px solid red" });
      jObj.parent().append(`<div class="errorMsg">${msg}</div>`);
      return false;
    }
    else {
      jObj.parent().find('.errorMsg').hide(function () {
        $(this).remove();
      });
      jObj.css({ "border-bottom": "1px solid red" });
      jObj.parent().append(`<div class="errorMsg">${msg}</div>`);
      return false;
    }

  }
  function isLessThan5(jObj) {
    if (jObj.val() && jObj.val() != "0" && jObj.val() != null && jObj.val() <= 5) {
      jObj.css({ "border-bottom": "1px solid black" });
      jObj.siblings('.errorMsg').hide(function () {
        $(this).remove();
      });
      return true;
    } else {
      jObj.parent().find('.errorMsg').hide(function () {
        $(this).remove();
      });
      jObj.css({ "border-bottom": "1px solid red" });
      jObj.parent().append(`<div class="errorMsg">${"הכנס ערך קטן או שווה ערך ל 5"}</div>`);
      return false;
    }

  }
  function generateDataObj145(type) {
    //Validate
    let valid = true;
    if ($("#membershipName" + type).length && !isVAlid($("#membershipName" + type), 'הכנס שם מנוי'))
      valid = false;
    if ($("#membershipPrice" + type).length && !isVAlid($("#membershipPrice" + type), 'הכנס מחיר'))
      valid = false;
    if ($("#priceSelectOptions" + type).length && !isVAlid($("#priceSelectOptions"), 'הכנס סוג חיוב'))
      valid = false;
    if(type==1){
      if ($("#membershipLength" + type).length && $("#membershipLength" + type).is(":visible") && !isVAlid($("#membershipLength" + type), 'הכנס תקופת מנוי')){
        valid = false;
      }
    }
    if(type==4){
      if ($("#ticketEntries" + type).length && !isVAlid($("#ticketEntries" + type), 'הכנס מספר כניסות'))
      valid = false;
    }
    if(type==5){
      if ($("#ticketEntries" + type).length && !isLessThan5($("#ticketEntries" + type)))
      valid = false;
    }

    if (!valid) return;

    let dataObj = {
      id: $("#hiddenIdInput" + type).val(),
      type: type,
      name: $("#membershipName" + type).val(),
      membershipType: $("#membershipType" + type).val(),
      isMembershipTypeNew: $("#isMembershipTypeNew" + type).val(),
      branch: $("#shopCmpBranch" + type).val(),
      price: $("#membershipPrice" + type).val(),
      hasTax: $("#taxInclude" + type).prop("checked"),
    };
    dataObj.membershipLength = $('#membershipLength' + type).val();
    dataObj.membershipUnits = $('#membershipUnits' + type).val();
    dataObj.alertOnEnd = $('#alertOnEnd' + type).is(":checked");

    dataObj.membershipAlertSettingsNumber = $('#membershipAlertSettingsNumber' + type).closest('.hiddenMembershipAlertSettings').hasClass('hidden') ? 0 : $('#membershipAlertSettingsNumber' + type).val();
    dataObj.membershipAlertSettingsUnitType = $('#membershipAlertSettingsUnitType' + type).val();
    dataObj.classes = [];
    $(`.hiddenPopupSection[data-id="${type}"] .openRegisterLineHiddenInput`).each(function () {
      if ($(this).val()) {
        dataObj.classes.push(JSON.parse($(this).val()));
      }

    });

    dataObj.allowBuyFromApp = $('#allowBuyFromApp' + type).val();
    dataObj.membershipStartSelect = $('#membershipStartSelect' + type).val();
    // Changes the format of the date - from view format to DB format (if not valid pass)
    let newDate =$('#lateRegisterDateInputMembership' + type).val();
    if (newDate && moment(newDate, "DD/MM/YYYY", true).isValid()){
      dataObj.lateRegisterDateInputMembership = moment(newDate,'DD/MM/YYYY').format('YYYY-MM-DD');
    }
    dataObj.allowLateRegisterMembership = $('#allowLateRegisterMembership' + type).is(':checked');
    dataObj.allowRelativeCheckboxMembership = $('#allowRelativeCheckboxMembership' + type).is(':checked');
    dataObj.membershipRelativeDiscount = $('#membershipRelativeDiscount' + type).val();
    dataObj.pageImgPath = $('#pageImgPath' + type).val();
    dataObj.membershipContent = $('#membershipContent' + type).summernote('code');
    if ($('#purchaseLimitPopupMembershipHiddenInput' + type).val()) {
      dataObj.purchaseLimits = JSON.parse($('#purchaseLimitPopupMembershipHiddenInput' + type).val());
    }

    if (type == 1) {
      dataObj.priceOptions = $('#priceSelectOptions').val();
    } else {
      dataObj.numOfEntries = $('#ticketEntries' + type).val();
    }
    console.log(dataObj);
    return dataObj;
  }
  function generateDataObj2() {
    // debugger;
    if (!$("#productName").val() || $("#productName").val() == "" || !$("#productPrice").val() || $("#productPrice").val() == "") {
      if (!$("#productName").val() || $("#productName").val() == "") {
        displayError($("#productName"), "שדה חובה")
      }
      if (!$("#productPrice").val() || $("#productPrice").val() == "") {
        displayError($("#productPrice"), "שדה חובה")
      }
      return null;
    }
    let dataObj = {
      id: $("#hiddenIdInput2").val(),
      type: 2,
      name: $("#productName").val(),
      categoryIsNew: $("#isProductCategoryNew").val() == "1",
      category: $("#productCategory").val(),
      branch: $("#productBranch").val(),
      price: $("#productPrice").val(),
      vat:$("#taxInclude2").is(":checked"),
      costPrice: $("#costPrice").val(),
      allowBuyInApp: $("#productInApp").val() == "2"
    };

    var InventoryList = $(".Inventory");
    var res = validateInventory(InventoryList);
    if(res.color || res.size || res.inventory) {
      if(res.inventory) {
        $('.js-inventory-error').text('נא להקליד כמות עבור מלאי הפריט');
        if(res.amountElm) {
          res.amountElm.addClass('border-danger-error');
        }
      }
      if(res.color) {
        $('.js-inventory-error').text('מלאי לא תקין, נא לבחור צבע עבור הפריט');
        if(res.colorElm) {
          res.colorElm.addClass('border-danger-error');
        }
      } 
       
      if(res.size) {
        $('.js-inventory-error').text('מלאי לא תקין, נא לבחור מידה עבור הפריט');
        if(res.sizeElm) {
          res.sizeElm.addClass('border-danger-error');
        }
      }
      if(res.color && res.size) {
        $('.js-inventory-error').text('מלאי לא תקין, נא לבחור מידה וצבע עבור הפריט');
      }
      $('.js-inventory-error').removeClass('d-none');

      setTimeout(() => {
        $('.js-inventory-error').addClass('d-none');
        $('.js-inventory-error').text('');
        if(res.amountElm) {
          res.amountElm.removeClass('border-danger-error');
        }
        if(res.colorElm) {
          res.colorElm.removeClass('border-danger-error');
        }
        if(res.sizeElm) {
          res.sizeElm.removeClass('border-danger-error');
        }
      }, 4000);

      return;
    }


    var extreaList = [];
    ///todo - need fix all this code
    if(InventoryList.length) {
      InventoryList.each(function () {
        let supplierId = $("#productSuppliers").val();
        let sizeId = $(this).find(".SizeInventory").val();
        var Inventory = {
          id: $(this).attr("data-id"),
          stock: $(this).find(".productPrice").val(),
          supplier:{
            id: supplierId ? (supplierId.startsWith('#') ? 0: supplierId) : -1,
            name:$("#productSuppliers option:selected" ).text()
          },
          sku: $("#makat").val(),
          barcode: $("#barcode").val(),
          size:{
            id: sizeId ? (sizeId.startsWith('#') ? 0 : sizeId) : -1,
            name: $(this).find(".SizeInventory option:selected" ).text()
          },
          color: $(this).find(".selectedColor").attr("data-id"),
        }
        extreaList.push(Inventory)
      })
    } else {
      if($("#openMoreOptions").hasClass('isOpen')) {
        let supplierId = $("#productSuppliers").val();
        var Inventory = {
          supplier:{
            id: supplierId ? (supplierId.startsWith('#') ? 0: supplierId) : -1,
            name:$("#productSuppliers option:selected" ).text()
          },
          sku: $("#makat").val(),
          barcode: $("#barcode").val(),
        }
        extreaList.push(Inventory);
      }
    }
    dataObj.extraOptions = {
      extra:extreaList
    };
    dataObj.DelInventory = DelInventory;
    if (dataObj.allowBuyInApp) {
      dataObj.img = $("#pageImgPath2").val();
      if ($(".hiddenTextarea-product").hasClass("visible")) {
        dataObj.content = $("#productContent").summernote('code');
      }
      if ($("#purchaseLimitPopupProductHidden2").is(":visible")) {
        dataObj.purchaseLimits = JSON.parse(
          $("#purchaseLimitPopupProductHiddenInput2").val()
        );
      }
    }
    return dataObj;
  }


  function generateDataObj3() {

    //Validation 
    let valid = true;
    if ($("#smartLinkTitle").length && $("#smartLinkTitle").is(":visible") && !isVAlid($("#smartLinkTitle"), 'הכנס כותרת לעמוד'))
      valid = false;
    if ($("#smartLinkProductType").length && $("#smartLinkProductType").is(":visible") && !isVAlid($("#smartLinkProductType"), 'הכנס סוג מוצר'))
      valid = false;

    if (($('#smartLinkProductType').val() == '2')) {//club membership

      if ($('#smartLinkChosenMembership').length &&
        $('#smartLinkChosenMembership').is(":visible")) {
        if (!isVAlid($('#smartLinkChosenMembership'), 'בחר מנוי למכירה')) {
          valid = false;
        }
      }

      if ($('input[id^=linkPrice]').length > 0 && $('input[id^=linkPrice]').is(":visible")) {
        $('input[id^=linkPrice]:visible').each(function () {
          if (!isVAlid($(this), 'הכנס מחיר')) {
            valid = false;
          }
        })
      }

    }

    if (($('#smartLinkProductType').val() == '1')) { //general item

      //IF WE CHOSE OPTION 1 BUT NEVER ADDED PRODUCT LINE
      if ($('div[id^=linkProductLine]').length === 0) {
        valid = false;
        $('#addSingleSmartLinkProduct').parent().find('.errorMsg').remove()
        $('#addSingleSmartLinkProduct').parent().append('<div class="errorMsg">הוסף מוצר</div>');
      } else {
        $('#addSingleSmartLinkProduct').parent().find('.errorMsg').remove()
      }

      let rowItemValid = true;
      if ($('select[id^=smartLinkChosenProduct]').length && $('select[id^=smartLinkChosenProduct]').is(":visible")) {
        $('select[id^=smartLinkChosenProduct]').each(function () {
          if (!isVAlid($(this), 'בחר סוג מוצר')) {
            rowItemValid = false;
            valid = false;
          }
        });
        if (!rowItemValid) {
          return
        }
      }

      if ($('input[id^=linkPrice]').length > 0 && $('input[id^=linkPrice]').is(":visible")) {
        $('input[id^=linkPrice]:visible').each(function () {
          if (!isVAlid($(this), 'הכנס מחיר')) {
            rowItemValid = false;
            valid = false;
          }
        })
        if (!rowItemValid) {
          return
        }
      }

    }



    // var expression = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi;
    // var regex = new RegExp(expression);
    // if (!$('#afterLink').val().match(regex)) {
    //   $('#afterLink').parent().find('.errorMsg').remove()
    //   $('#afterLink').parent().append('<div class="errorMsg">הכנס לינק</div>');
    //   $('#openAfterLink').parent().find('.errorMsg').remove()
    //   $('#openAfterLink').parent().append('<div class="errorMsg">הכנס לינק</div>');
    //   return;
    // } else {
    //   $('#afterLink').parent().find('.errorMsg').remove()
    //   $('#openAfterLink').parent().find('.errorMsg').remove()
    // }

    if (!valid) return;

    const annualMembership = [];
    const dateTickets = [];
    const linkProductLine = $('.linkProductLine').length;
    const items = [];
    const dynamicForms = [];
    const medicalForms= [];
    const fees = [];

    if (linkProductLine > 0) {
      $(".linkProductLine").each(function () {
        items.push({
          itemId: $(this).find("select[id^=smartLinkChosenProduct]").val(),
          price: $(this).find("input[id^=linkPrice]").val(),
          taxIncluded: $(this).find("input[id^=linkTaxInclude]").val()
        });
      });
    }

    $('.classSelection').each(function () {
      let check = $(this).siblings('.registerClassDayInput').val();
      let check2 = $(this).val();
      if($(this).siblings('.registerClassDayInput').val() != -1 && $(this).val() == -1){
        valid = isVAlid( $(this), "בחר שיעור", true)
      }
      annualMembership.push({
        day: $(this).siblings('.registerClassDayInput').val(),
        class: $('option:selected', this).attr('data-value')
      })

    });

    $('.classSelectionTickets').each(function () {
      if($(this).siblings('.registerClassDayInput').val() != -1 && $(this).val() == -1){
        valid = isVAlid( $(this), "בחר שיעור", true)
      }
      dateTickets.push({
        date: $(this).siblings('.registerDateInput').val(),
        class: $(this).val()
      })
    });
    if(!valid){
      return;
    }
    $('.registerFormSelect').each(function () {
      if ($(this).val() != -1)
      if($(this).val().startsWith("__M")){
        let parsedVal= $(this).val().replace('__M',"");
        medicalForms.push(parsedVal);
      }else{
        let parsedVal= $(this).val().replace('__D',"");
        dynamicForms.push(parsedVal);
      }
    })

    $('.registerInsuranceSelect').each(function () {
      if ($(this).val() != -1)
        fees.push($(this).val());
    });
    let latedate = $("#smartLinkExpiration").val();
    let choosedate = $('#lateRegisterDateInput').val();
    if(latedate == 4 && choosedate == ""){
      latedate = 1;
    }
    let tax = $("#linkTaxInclude").prop("checked") == true ? "on" : "off";
    let allowRelativeCheckbox = $('#allowRelativeCheckbox').is(':checked')  ? "on" : "off";
    let allowLateRegister = $('#allowLateRegister').is(':checked')  ? "on" : "off";
    let dataObj = {
      id: $("#hiddenIdInput3").val(),
      pageTitle: $("#smartLinkTitle").val(),
      clientToBranch: $("#smartLinkCustomer").val(),
      itemType: {
        generalItem: {
          items: items
        },
        membership: {
          id: $("#smartLinkChosenMembership").val(),
          price: $("#linkPrice").val(),
          taxIncluded: tax,
          date: latedate,
          attachToTrainingSeries: "",
          chooseDate: choosedate,
          allowRelativeReduction: allowRelativeCheckbox,
          allowLateRegitration: allowLateRegister,
          relativeReductionPrice : $('#relativeReductionPrice').val()
        }
      },
      image: $("#pageImgPath3").val(),
      linkAfterPay: $("#afterLink").val(),
      description: $('#linkContent').summernote('code'),
      annualMembership: annualMembership,
      dateTickets: dateTickets,

    }
    if (dynamicForms.length > 0 || medicalForms.length > 0) {
      dataObj.registerForm = dynamicForms;
      dataObj.medicalForm = medicalForms;
    }
    if (fees.length > 0) {
      dataObj.registerInsurance = fees;
    }

    return dataObj;
  }

  //only used to change color

  function reloadAfterInsert(tableId, callback) {
    let dtTable = $(tableId).DataTable();
    dtTable.ajax.reload(callback);
  }

  function createMembership(data) {
    showLoader();
    $.ajax({
      url: "/office/newShop/insertAjax/membership.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopMembershipsTable', initCallbackMembership);
        reloadAfterInsert('#shopLinksTable', initCallbackLinks);
        closePopup("mainShopPopup");
        emptyMembershipPopup();
        updateSelects(response);
        $("#InventoryList").children().remove();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }
  function createProduct(data) {
    showLoader();
    $.ajax({
      url: "/office/newShop/insertAjax/product.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopItemsTable', initCallbackItems);
        reloadAfterInsert('#shopLinksTable', initCallbackLinks);
        closePopup("mainShopPopup");
        updateSelects(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }
  function createLink(data) {
    showLoader();
    $.ajax({
      url: "/office/newShop/insertAjax/smartLink.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopLinksTable', initCallbackLinks);
        closePopup("mainShopPopup");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }

  function updateSelects(response) {
    const data = JSON.parse(response);
    if (data.category) {
      $('select[name=productCategory]').append(data.category);
    }
    if (data.membershipType) {
      $('select[name=location]').append(data.membershipType);
    }
    if (data.newSize && data.newSize.length > 0) {
      sizeArr = [...sizeArr, ...data.newSize];
    }
    if (data.newSupplier) {
      $('#productSuppliers').append(`<option value=${data.newSupplier.id}>${data.newSupplier.name}</option>`).trigger('change');
    }
  }

  function onInventoryPopupSave() {
    const dataObj = {id: $('#InventoryPopup').attr('data-id'), extraOptions: {extra: []}};
    var inventoryList = $('#InventoryTable tbody tr');
    var res = validateInventory(inventoryList);

    if(res.color || res.size || res.inventory) {
      if(res.inventory) {
        $('.js-inventoryTable-error').text('נא להקליד כמות עבור מלאי הפריט');
        if(res.amountElm) {
          res.amountElm.addClass('border-danger-error');
        }
      }
      if(res.color) {
        $('.js-inventoryTable-error').text('מלאי לא תקין, נא לבחור צבע עבור הפריט');
        if(res.colorElm) {
          res.colorElm.addClass('border-danger-error');
        }
      } 
       
      if(res.size) {
        $('.js-inventoryTable-error').text('מלאי לא תקין, נא לבחור מידה עבור הפריט');
        if(res.sizeElm) {
          res.sizeElm.addClass('border-danger-error');
        }
      }
      if(res.color && res.size) {
        $('.js-inventoryTable-error').text('מלאי לא תקין, נא לבחור מידה וצבע עבור הפריט');
      }
      $('.js-inventoryTable-error').removeClass('d-none');
      
      setTimeout(() => {
        $('.js-inventoryTable-error').addClass('d-none');
        $('.js-inventoryTable-error').text('');
        if(res.amountElm) {
          res.amountElm.removeClass('border-danger-error');
        }
        if(res.colorElm) {
          res.colorElm.removeClass('border-danger-error');
        }
        if(res.sizeElm) {
          res.sizeElm.removeClass('border-danger-error');
        }
      }, 4000);

      return;
    }

    $('#InventoryTable tbody tr').each(function () {
      let sizeId = $(this).find('.size').val();
      dataObj.extraOptions.extra.push({
        id: $(this).attr('data-id'),
        stock: $(this).find('.InventoryAmount').val(),
        size: {
          id: !sizeId || sizeId.startsWith('#') ? 0 : sizeId,
          name: $(this).find('.size option:selected').text()
        },
        color: $(this).find('.selectedColor').attr('data-id')
      });
    });
    dataObj.DelInventory = DelInventoryTable;
    showLoader();
    $.ajax({
      url: "/office/newShop/updateAjax/itemDetail.php",
      type: "post",
      data: dataObj,
      success: function (response) {
        reloadAfterInsert('#shopItemsTable', initCallbackItems);;
        closePopup("InventoryPopup");
        updateSelects(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }

  function updateMembership(data) {
    //zohar    
    showLoader();
    $.ajax({
      url: "/office/newShop/updateAjax/membership.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopMembershipsTable', initCallbackMembership);
        closePopup("mainShopPopup");
        emptyMembershipPopup();
        updateSelects(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }
  function updateProduct(data) {
    showLoader();
    $.ajax({
      url: "/office/newShop/updateAjax/product.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopItemsTable', initCallbackItems);
        closePopup("mainShopPopup");
        updateSelects(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }
  function updateLink(data) {
    showLoader();
    $.ajax({
      url: "/office/newShop/updateAjax/smartLink.php",
      type: "post",
      data: data,
      success: function (response) {
        reloadAfterInsert('#shopLinksTable', initCallbackLinks);
        closePopup("mainShopPopup");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      }
    })
  }
  function validateInventory(inventoryList) {
    var hasColor = false;
    var hasSize = false;
    var invalid = {
      size: false,
      color: false,
      inventory: false
    };
    inventoryList.each(function() {
      var colorId = $(this).find(".selectedColor").attr("data-id");
      var size = $(this).find(".SizeInventory option:selected" ).val();
      if (colorId && colorId > 0) {
        hasColor = true;
      }
      if(size) {
        hasSize = true;
      }
    });
    inventoryList.each(function() {
      var colorId = $(this).find(".selectedColor").attr("data-id");
      var size = $(this).find(".SizeInventory option:selected" ).val();
      var amount = $(this).find('.InventoryAmount').val();
      if(!amount || amount <= 0) {
        invalid.inventory = true;
        invalid['amountElm'] = $(this).find('.InventoryAmount').parent();
      }
      if((!colorId || colorId == 0) && hasColor) {
        invalid.color = true;
        invalid['colorElm'] = $(this).find('.colorGridSelect');
      }
      if(!size && (hasSize)) {
        invalid.size = true;
        invalid['sizeElm'] = $(this).find('.SizeInventory').parent();
      }
    });
    return invalid;
  }

  $("#mainShopPopupButtonSave").click(function () {
    onMainPopupSave();
  });

  $("#inventoryPopupButtonSave").click(function () {
    onInventoryPopupSave();
  });

});
