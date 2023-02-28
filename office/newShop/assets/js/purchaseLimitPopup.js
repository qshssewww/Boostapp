function onSystematicPurchaseLimitAdd(sysData) {
  let data = generateDataFromSysDataPurchaseLimit(sysData);
  data.type=type;
  if (type == "1" || type == "4" || type == "5") {
    $("#purchaseLimitPopupMembershipHidden" + type).show();
    $("#purchaseLimitPopupMembership" + type).hide();
    $("#purchaseLimitPopupMembershipHiddenInput" + type).val(
      JSON.stringify(data)
    );
    $("#purchaseLimitPopupMembershipHiddenText" + type).html(data.string);
  } else {
    $("#purchaseLimitPopupProductHidden" + type).show();
    $("#purchaseLimitPopupProduct" + type).hide();
    $("#purchaseLimitPopupProductHiddenInput" + type).val(JSON.stringify(data));
    $("#purchaseLimitPopupProductHiddenText" + type).html(data.string);
  }
}
function generateDataFromSysDataPurchaseLimit(sysData) {
  let newData = { string: sysData.GeneratedString };
  if (sysData.startAge && sysData.endAge) {
    newData.age = { fromAge: sysData.startAge, toAge: sysData.endAge };
  } else {
    newData.age = false;
  }
  if (sysData.gender) {
    newData.gender = sysData.gender
  } else {
    newData.gender = false;
  }
  if (sysData.membership) {
    newData.memberships = JSON.parse(sysData.membership);
  } else {
    newData.memberships = false;
  }
  if (sysData.maxPurchase) {
    newData.purchaseAmount = parseInt(sysData.maxPurchase);
  } else {
    newData.purchaseAmount = false;
  }
  if (sysData.rank) {
    newData.rank = JSON.parse(sysData.rank);
  } else {
    newData.rank = false;
  }
  if (sysData.seniority) {
    newData.seniority = { date: sysData.seniority };
  } else {
    newData.seniority = false;
  }
  if (sysData.customerStatus) {
    newData.status = sysData.customerStatus;
  } else {
    newData.status = false;
  }
  return newData;
}
$(document).ready(function () {
  $("#openHiddenRowInputsSelect").change(function () {
    if ($(this).val() == "1") {
      openAgeLimit();
    }
    if ($(this).val() == "2") {
      openGenderLimit();
    }
    if ($(this).val() == "3") {
      openSeniorityLimit();
    }
    if ($(this).val() == "4") {
      openPurchaseAmountLimit();
    }
    if ($(this).val() == "5") {
      openStatusLimit();
    }
    if ($(this).val() == "6") {
      openRankLimit();
    }
    if ($(this).val() == "7") {
      openMembershipLimit();
    }
    if ($(this).val() != "0") {
      $(this).val(0).trigger("change");
    }
    disableSelect();
  });
  function disableSelect() {
    if ($(".hiddenAgeLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='1']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='1']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenGenderLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='2']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='2']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenSeniorityLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='3']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='3']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenPurchaseAmountLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='4']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='4']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenStatusLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='5']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='5']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenRankLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='6']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='6']").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($(".hiddenMembershipLimitP").hasClass("visible")) {
      $("#openHiddenRowInputsSelect option[value='7']").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#openHiddenRowInputsSelect option[value='7']").removeClass("DISPLAYNONEIMPORTANT");
    }
    let num =  $("#openHiddenRowInputsSelect option").not('.DISPLAYNONEIMPORTANT').length;
    if(num == 1){
      $("#openHiddenRowInputsSelect").addClass("DISPLAYNONEIMPORTANT");
    }
    else{
      $("#openHiddenRowInputsSelect").removeClass("DISPLAYNONEIMPORTANT");
    }
    if($('#openHiddenRowInputsSelect').val()!=0){
      $('#openHiddenRowInputsSelect').val(0).trigger('change')
    }
  }
  function openAgeLimit() {
    showHidden($("#openAgeLimitP"), ".hiddenAgeLimitP");
  }

  function closeAgeLimit() {
    hideHidden($("#closeAgeLimitP"), ".hiddenAgeLimitP");
    $(".hiddenAgeLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");

  }

  $("#openAgeLimitP").on("click", function (e) {
    openAgeLimit();
  });

  $("#closeAgeLimitP").on("click", function (e) {
    closeAgeLimit();
    disableSelect();

  });

  function openGenderLimit() {
    showHidden($("#openGenderLimitP"), ".hiddenGenderLimitP");
  }

  function closeGenderLimit() {
    hideHidden($("#closeGenderLimitP"), ".hiddenGenderLimitP");
    $(".hiddenGenderLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openGenderLimitP").on("click", function (e) {
    openGenderLimit();
  });

  $("#closeGenderLimitP").on("click", function (e) {
    closeGenderLimit();
    disableSelect();

  });

  const selectedLanguage= $.cookie('boostapp_lang') ?? 'he';
  $('#seniorityAge').datepicker( {
    dateFormat: 'dd/mm/yy',
    maxDate: '0',
  })
  $.datepicker.setDefaults($.datepicker.regional[selectedLanguage]);


  function openSeniorityLimit() {
    showHidden($("#openSeniorityLimitP"), ".hiddenSeniorityLimitP");
  }

  function closeSeniorityLimit() {
    hideHidden($("#closeSeniorityLimitP"), ".hiddenSeniorityLimitP");
    $(".hiddenSeniorityLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openSeniorityLimitP").on("click", function (e) {
    openSeniorityLimit();
  });

  $("#closeSeniorityLimitP").on("click", function (e) {
    closeSeniorityLimit();
    disableSelect();

  });

  function openPurchaseAmountLimit() {
    showHidden($("#openPurchaseAmountLimitP"), ".hiddenPurchaseAmountLimitP");
  }

  function closePurchaseAmountLimit() {
    hideHidden($("#closePurchaseAmountLimitP"), ".hiddenPurchaseAmountLimitP");
    $(".hiddenPurchaseAmountLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openPurchaseAmountLimitP").on("click", function (e) {
    openPurchaseAmountLimit();
  });

  $("#closePurchaseAmountLimitP").on("click", function (e) {
    closePurchaseAmountLimit();
    disableSelect();

  });

  function openStatusLimit() {
    showHidden($("#openStatusLimitP"), ".hiddenStatusLimitP");
  }

  function closeStatusLimit() {
    hideHidden($("#closeStatusLimitP"), ".hiddenStatusLimitP");
    $(".hiddenStatusLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openStatusLimitP").on("click", function (e) {
    openStatusLimit();
  });

  $("#closeStatusLimitP").on("click", function (e) {
    closeStatusLimit();
    disableSelect();

  });

  $("#rankMultiSelectP")
    .select2({ minimumResultsForSearch: -1 })
    .on("select2:unselect", function (evt) {
      if (!evt.params.originalEvent) {
        return;
      }

      evt.params.originalEvent.stopPropagation();
    });
  function openRankLimit() {
    showHidden($("#openRankLimitP"), ".hiddenRankLimitP");
  }

  function closeRankLimit() {
    hideHidden($("#closeRankLimitP"), ".hiddenRankLimitP");
    $(".hiddenRankLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openRankLimitP").on("click", function (e) {
    openRankLimit();
  });

  $("#closeRankLimitP").on("click", function (e) {
    closeRankLimit();
    disableSelect();

  });

  $("#membershipMultiSelectP")
    .select2({ minimumResultsForSearch: -1 })
    .on("select2:unselect", function (evt) {
      if (!evt.params.originalEvent) {
        return;
      }

      evt.params.originalEvent.stopPropagation();
    });

  function openMembershipLimit() {
    showHidden($("#openMembershipLimitP"), ".hiddenMembershipLimitP");
  }

  function closeMembershipLimit() {
    hideHidden($("#closeMembershipLimitP"), ".hiddenMembershipLimitP");
    $(".hiddenMembershipLimitP")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openMembershipLimitP").on("click", function (e) {
    openMembershipLimit();
  });

  $("#closeMembershipLimitP").on("click", function (e) {
    closeMembershipLimit();
    disableSelect();

  });
  function setEmptyPopupValues() {
    $("#fromAgeP").val("");
    $("#toAgeP").val("");
    closeAgeLimit();
    $("#maleP").prop("checked", false);
    $("#femaleP").prop("checked", false);
    $("#otherP").prop("checked", false);
    closeGenderLimit();
    $("#seniorityAge").val("");
    closeSeniorityLimit();
    $("#puchaseAmountP").val("");
    closePurchaseAmountLimit();
    $("input:radio[name=cols]").val(1);
    closeStatusLimit();
    $("#rankMultiSelectP").val([]).trigger("change");
    closeRankLimit();
    $("#membershipMultiSelectP").val([]).trigger("change");
    closeMembershipLimit();
    disableSelect();

  }

  function setUnEmptyPopupValues(data) {
    setEmptyPopupValues();
    $("#purchaseLimitPopupSrc").val(data.type);
    if (data.age) {
      openAgeLimit();
      $("#fromAgeP").val(data.age.fromAge);
      $("#toAgeP").val(data.age.toAge);
    }
    $('.hiddenGenderLimitP input[name="gender"]').prop( "checked", false );
    if (data.gender) {
      openGenderLimit();
      if (data.gender == 1) {
        $("#maleP").prop("checked", true);
      }
      if (data.gender  == 2) {
        $("#femaleP").prop("checked", true);
      }
      if (data.gender  == 0) {
        $("#otherP").prop("checked", true);
      }
    }
    if (data.seniority) {
      try {
        openSeniorityLimit();
        $("#seniorityAge").val(new Date(data.seniority.date).format('dd/mm/yyyy'));
      } catch (e) {
        //format not valid
      }
    }
    if (data.purchaseAmount) {
      openPurchaseAmountLimit();
      $("#puchaseAmountP").val(data.purchaseAmount);
    }
    if (data.status) {
      openStatusLimit();
      $(`input[name=status][value='${data.status}']`).prop("checked", true);
    }
    if (data.rank) {
      openRankLimit();
      $("#rankMultiSelectP").val(data.rank).trigger("change");
    }
    if (data.memberships) {
      openMembershipLimit();
      $("#membershipMultiSelectP").val(data.memberships).trigger("change");
    }
    disableSelect();
  }
  $(document).on("focusout","#fromAgeP, #toAgeP",function () {

    let from = $("#fromAgeP").val();
    let to = $("#toAgeP").val();
    if(to != "" && from != "" && parseFloat(from) > parseFloat(to)){
      Swal.fire(
          "",
          "טווח גילאים אינו חוקי",
          "error"
      );
      $("#fromAgeP").val("");
      $("#toAgeP").val("");
    }
  })
  function onPopupSave() {
    let string = "";
    let age = $(".hiddenAgeLimitP").hasClass("visible");
    if (age) {
      age = {};
      age.fromAge = $("#fromAgeP").val();
      age.toAge = $("#toAgeP").val();
      string += `החל מגיל ${$("#fromAgeP").val()} ועד גיל ${$(
        "#toAgeP"
      ).val()} , `;

    }
    let gender = $(".hiddenGenderLimitP").hasClass("visible") && $('.hiddenGenderLimitP input[name="gender"]:checked').length === 1;
    if (gender) {
      gender = $('.hiddenGenderLimitP input[name="gender"]:checked').val();
      string += `מוגבל למין ${$("#maleP").prop("checked") ? "זכר" : ""} ${
        $("#femaleP").prop("checked") ? "נקבה" : ""
      } ${$("#otherP").prop("checked") ? "אחר" : ""}, `;
    }
    let seniority = $(".hiddenSeniorityLimitP").hasClass("visible");
    if (seniority) {
      seniority = {};
      if (moment($("#seniorityAge").val(), "DD/MM/YYYY", true).isValid()){
        seniority.date = moment($("#seniorityAge").val(),'DD/MM/YYYY').format('YYYY-MM-DD');
      }
      string += `מוגבל למשתמשים רשומים עד לתאריך ${$(
        "#seniorityAge"
      ).val()}, `;
    }
    let purchaseAmount = $(".hiddenPurchaseAmountLimitP").hasClass("visible");
    if (purchaseAmount) {
      purchaseAmount = $("#puchaseAmountP").val();
      string += `מוגבל לרכישה עד ${$(
        "#puchaseAmountP"
      ).val()} פעמים ללקוח, `;
    }
    let status = $(".hiddenStatusLimitP").hasClass("visible");
    if (status) {
      status = $("input[name='status']:checked").val();
      string += `למשתמשים בסטטוס ${
        $("input[name='status']:checked").val() == 1 ? "פעיל" : "מתעניין"
      }, `;
    }
    let rank = $(".hiddenRankLimitP").hasClass("visible");
    if (rank) {
      rank = $("#rankMultiSelectP").val();
      string += `למשתמשים עם תגית: `;
      $(".hiddenRankLimitP .select2-selection__choice").each(function () {
        string += `${$(this).attr("title")}, `;
      });
    }
    let memberships = $(".hiddenMembershipLimitP").hasClass("visible");
    if (memberships) {
      memberships = $("#membershipMultiSelectP").val();
      string += `למשתמשים עם סוג מנוי: `;
      $(".hiddenMembershipLimitP .select2-selection__choice").each(function () {
        string += `${$(this).attr("title")}, `;
      });
    }
    string = string.slice(0, string.length - 2);
    string += ".";
    let typeSrc = $("#purchaseLimitPopupSrc").val();
    let data = {
      age,
      gender,
      seniority,
      purchaseAmount,
      status,
      rank,
      memberships,
      typeSrc,
      string,
      type:typeSrc
    };
    if (typeSrc == "1" || typeSrc == "4" || typeSrc == "5") {
      $("#purchaseLimitPopupMembershipHidden" + type).show();
      $("#purchaseLimitPopupMembership" + type).hide();
      $("#purchaseLimitPopupMembershipHiddenInput" + type).val(
        JSON.stringify(data)
      );
      $("#purchaseLimitPopupMembershipHiddenText" + type).html(data.string);
    } else {
      $("#purchaseLimitPopupProductHidden" + type).show();
      $("#purchaseLimitPopupProduct" + type).hide();
      $("#purchaseLimitPopupProductHiddenInput" + type).val(
        JSON.stringify(data)
      );
      $("#purchaseLimitPopupProductHiddenText" + type).html(data.string);
    }
  }

  $(document).on("click", "#purchaseLimitPopupProductHiddenClose", function () {
    $("#purchaseLimitPopupProductHidden" + type).hide();
    $("#purchaseLimitPopupProduct" + type).show();
    $("#purchaseLimitPopupProductHiddenInput" + type).val("");
    $("#purchaseLimitPopupProductHiddenText" + type).html("");
  });

  $(document).on(
    "click",
    "#purchaseLimitPopupMembershipHiddenClose" + 1,
    function () {
      $("#purchaseLimitPopupMembershipHidden" + 1).hide();
      $("#purchaseLimitPopupMembership" + 1).show();
      $("#purchaseLimitPopupMembershipHiddenInput" + 1).val("");
      $("#purchaseLimitPopupMembershipHiddenText" + 1).html("");
    }
  );
  $(document).on(
    "click",
    "#purchaseLimitPopupMembershipHiddenClose" + 4,
    function () {
      $("#purchaseLimitPopupMembershipHidden" + 4).hide();
      $("#purchaseLimitPopupMembership" + 4).show();
      $("#purchaseLimitPopupMembershipHiddenInput" + 4).val("");
      $("#purchaseLimitPopupMembershipHiddenText" + 4).html("");
    }
  );
  $(document).on(
    "click",
    "#purchaseLimitPopupMembershipHiddenClose" + 5,
    function () {
      $("#purchaseLimitPopupMembershipHidden" + 5).hide();
      $("#purchaseLimitPopupMembership" + 5).show();
      $("#purchaseLimitPopupMembershipHiddenInput" + 5).val("");
      $("#purchaseLimitPopupMembershipHiddenText" + 5).html("");
    }
  );

  $(document).on("click", ".openPurchaseLimitPopup", function () {
    $("#purchaseLimitPopupSrc").val($(this).attr("data-type"));
    setEmptyPopupValues();
    closePopup("mainShopPopup");
    openPopup("purchaseLimitPopup");
  });

  $(document).on("click", ".editPurchaseLine", function () {
    let data = $(this).parents(".rowInput").find(".hiddenPurchaseInput").val();
    setUnEmptyPopupValues(JSON.parse(data));
    closePopup("mainShopPopup");
    openPopup("purchaseLimitPopup");
  });

  $(document).on("click", "#purchaseLimitPopupButtonSave", function () {
    onPopupSave();
    closePopup("purchaseLimitPopup");
    setEmptyPopupValues();
    openPopup("mainShopPopup");
  });

  $(document).on("click", ".closePurchaseLimitPopup", function () {
    closePopup("purchaseLimitPopup");
    setEmptyPopupValues();
    openPopup("mainShopPopup");
  });
});
