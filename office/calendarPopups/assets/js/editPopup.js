function getSelectedDaysSmallEditPopup() {
  let selectedDays = [];
  $(".groupDayLiSelected").each(function () {
    selectedDays.push($(this).val());
  });
  return selectedDays;
}

$(document).ready(function () {
  //select weekdays in popup
  $(".groupDayLi").click(function () {
    if ($(this).attr("data-selected") == "0") {
      $(this).addClass("groupDayLiSelected");
      $(this).attr("data-selected", "1");
    } else {
      $(this).removeClass("groupDayLiSelected");
      $(this).attr("data-selected", "0");
    }
  });
  //hide week day selector if not needed
  $("#frequencyTypeOfUnit").change(function () {
    if ($(this).val() == "2") {
      $(".weekDaysContainer").removeClass("hideWeekDaysList");
    } else {
      $(".weekDaysContainer").addClass("hideWeekDaysList");
    }
  });
  //function to get selected days from weekdays list

  $(document).on("change", "#byDays ,#group ,#single", function () {
    let val = $(this).val();
    if (val == "byDays") {
      $(".groupDaysContainer").removeClass("hideGroupDaysContainer");
    } else {
      $(".groupDaysContainer").addClass("hideGroupDaysContainer");
    }
  });

  //close
  $(document).on("click", ".closeEditPopup", function () {
    closePopup("editPopup");
  });
});


//next
function clearEditSmallPopup() {
  $("#single").prop("checked", true).trigger("change");
  $("#toCancelClass").prop("checked", false);
  $(".groupDayLi").removeClass("groupDayLiSelected");
  $(".groupDayLi").attr("data-selected", "0");
}
function setBigPopupWithData(data) {
  setEmptyMainCalendarPopup();
  $(".HIDEIFEDIT").addClass("hiddenImportant");
  $("#mainPopupIsEditId").val(data.id);
  $("#mainPopupIsEditGroup").val(data.GroupNumber);
  if (data.ShowApp == "1") {
    $("#calendarAndApp").click();
    if (data.ClassDevice) {
      showHidden($('#openDevices'), ".hiddenDevices");
      $("#devicesInput").val(data.ClassDevice);
    }
    if (data.MaxClient) {
      $("#maxNumberOfAtendees").val(data.MaxClient);
    }
    if (data.ClassWating) {
      if (data.MaxWatingList) {
        $("#allowAsStandBy").val(3).trigger("change");
        $("#limitStandbyList").val(data.MaxWatingList);
      } else {
        $("#allowAsStandBy").val(1).trigger("change");
      }
    } else {
      $("#allowAsStandBy").val(2).trigger("change");
    }
    $("#showMore").attr("data-toggle", "1");
    $("#showMore").html("הצג פחות");
    $(".showMoreChangeable").removeClass("hideShowMoreChangeable");

    if (data.MinClass) {
      showHidden($("#openMinimum"), ".hiddenMinimum");
      $("#minimumAtendeesAmount").val(data.MinClassNum);
      $("#minimumAtendeesCheckAmount").val(data.ClassTimeCheck);
      if (data.ClassTimeTypeCheck == 1) {
        $("#minimumAtendeesCheckType").val("דקות");
      }
      if (data.ClassTimeTypeCheck == 2) {
        $("#minimumAtendeesCheckType").val("שעות");
      }
      if (data.ClassTimeTypeCheck == 3) {
        $("#minimumAtendeesCheckType").val("ימים");
      }
    }

    if (data.purchaseOptions == "1") {
      showHidden($("#openPurchaseOptions"), ".hiddenPurchaseOptions");
      $("#purchaseAmount").val(data.purchaseAmount);
      if (data.purchaseLocation == 1) {
        $("#purchaseLocation").val("app");
      }
      if (data.purchaseLocation == 2) {
        $("#purchaseLocation").val("link");
      }
      if (data.purchaseLocation == 3) {
        $("#purchaseLocation").val("everywhere");
      }
    }
    if (data.ageLimitType) {
      $('#placeLimitation').trigger('click');
      $('.limitTriger').last().val('age').trigger('change');
      $('.ageLimitOperator').val(data.ageLimitType);
      $('#ageLimit1').val(data.ageLimitNum1)
      if (data.ageLimitType == "3") {
        $('#ageLimit2').val(data.ageLimitNum2)
      }
    }
    if (data.LimitLevel) {
      $('#placeLimitation').trigger('click');
      $('.limitTriger').last().val('degree').trigger('change')
      $('.degreeInput').val(data.LimitLevel.split(',')).trigger('change')
    }
    if (data.ClassLimitTypes == "1") {
      $('#placeLimitation').trigger('click');
      $('.limitTriger').last().val('type').trigger('change')
      let MData = data.membershipIds.MemberShipType;
      $('.mebershipInput').val(MData.split(',')).trigger('change')
    }
    if (data.GenderLimit) {
      $('#placeLimitation').trigger('click');
      $('.limitTriger').last().val('gender').trigger('change')
      $('.genderInput').val(data.GenderLimit)
    }

    let closeUnit = "0";
    let openUnit = "0";
    if (data.OpenOrderType == "1") {
      openUnit = "דקות";
    }
    if (data.OpenOrderType == "2") {
      openUnit = "שעות";
    }
    if (data.OpenOrderType == "3") {
      openUnit = "ימים";
    }
    if (data.CloseOrderType == "1") {
      closeUnit = "דקות";
    }
    if (data.CloseOrderType == "2") {
      closeUnit = "שעות";
    }
    if (data.CloseOrderType == "3") {
      closeUnit = "ימים";
    }
    if (data.OpenOrder == "0" && data.CloseOrder == "0") {
      $("#placeSignTiming").trigger("click");
      $("#placeSignTiming").trigger("click");
      $("#timingOpenClose0").val("open").trigger("change");
      $("#timingOpenClose1").val("close").trigger("change");
      $("#timingNumber0").val(data.OpenOrderTime);
      $("#timingNumber1").val(data.CloseOrderTime);

      $("#timingUnit0").val(openUnit);
      $("#timingUnit1").val(closeUnit);
    } else if (data.OpenOrder == "0") {
      $("#placeSignTiming").trigger("click");
      $("#timingOpenClose0").val("open").trigger("change");
      $("#timingNumber0").val(data.OpenOrderTime);
      $("#timingUnit0").val(openUnit);
    } else if (data.CloseOrder == "0") {
      $("#placeSignTiming").trigger("click");
      $("#timingOpenClose0").val("close").trigger("change");
      $("#timingNumber0").val(data.CloseOrderTime);
      $("#timingUnit0").val(closeUnit);
    }

    //תמונה
    if (data.FreeClass && data.FreeClass != "0") {
      $("#freeRegister").prop("checked", true);
    }
    if (
        data.WatingListOrederShow === "0" ||
        data.WatingListOrederShow === 0
    ) {
      $("#checkbox3").prop("checked", true);
    }
    if (data.ShowClientNum === "0" || data.ShowClientNum === 0) {
      $("#checkbox1").prop("checked", true);
    }
    if (data.ShowClientName === "0" || data.ShowClientName === 0) {
      $("#checkbox2").prop("checked", true);
    }
  }
  $("#calendarPopupClassSelect").val(data.ClassNameType).trigger("change");
  let color = $("#calendarPopupClassSelect")
      .children("option:selected")
      .attr("data-color");
  $(".colorGridSelect .selectedColor").css("background-color", color);
  $("#selectedColor").val(color);

  $("#calendarPopupLocationSelect").val(data.Brands).trigger("change");
  let objDate = data.StartDate.split("-");
  $("#calendarPopupDateSelect").val(
      `${objDate[2]}/${objDate[1]}/${objDate[0]}`
  );
  $("#calendarPopupTimeSelectFrom")
      .val(data.StartTime.slice(0, 5))
      .trigger("change");
  $("#calendarPopupTimeSelectTo")
      .val(data.EndTime.slice(0, 5))
      .trigger("change");
  $("#selectedTrainer1").val(data.GuideId);
  if (data.ExtraGuideId) {
    $("#addTrainer").trigger("click");
    $("#selectedTrainer2").val(data.ExtraGuideId);
  }
  let cancelationInputId;
  if (data.ShowApp != "1") {
    cancelationInputId = "#cancelationInput1";
  } else {
    cancelationInputId = "#cancelationInput2";
  }
  if (data.cancelation) {
    $(cancelationInputId).append(
        `<option data-content='${JSON.stringify(
            data.cancelation
        )}'data-edit="1" value='${data.cancelation.id}'> ${
            data.cancelation.name
        }</option>`
    );

    $(cancelationInputId).val(data.cancelation.id).trigger("change");
    $(cancelationInputId).change(function () {
      $(`${cancelationInputId} option[data-edit='1']`).remove();
    });

    // no and always
  } else {
    if (data.CancelLaw == "4") {
      $(cancelationInputId).val("no").trigger("change");

    }
    if (data.CancelLaw == "5") {
      $(cancelationInputId).val("free").trigger("change");

    }
  }

  //reminder

  if (data.SendReminder == "0") {
    showHidden($('#openReminder'), ".hiddenReminder");
    $("#newClassDurationUnitType").val(data.ReminderUnits);
    $("#newClassDurationNumber").val(data.ReminderNum);
  }

  if (data.liveClassLink) {
    if (data.is_zoom_class) {
      $("#prodcastOptions").val("openZoom").trigger("change");
      showHidden($("#openZoomVid"), ".hiddenZoomVid", ".select2");
      $("#zoomMeetingId").val(data.liveClassLink);
    } else {
      $("#prodcastOptions").val("openVideoLink").trigger("change");
      showHidden($("#openSimpleVid"), ".hiddenSimpleVid", ".select2");
      $("#prodcastLink").val(data.liveClassLink);
      if (data.onlineSendType) {
        $('#broadCastReminderType').val(data.onlineSendType)
      }
      if (data.onlineReminderType && data.onlineReminderType != "0") {
        $('#broadCastType').val(data.onlineReminderType)
      }
    }
    if (data.content) {
      $("#classContent").summernote('code', data.content);
      $(".hiddenTextarea")
          .addClass("visible")
          .removeClass("hidden");
      $("#openTextarea .plus")
          .addClass("hidden")
          .removeClass("visible");
      $("#contentShow").val(data.contentShow);
    }


    if (data.onlineReminderNum && data.onlineReminderNum != "0") {
      $('#broadCastNum').val(data.onlineReminderNum)
    }
  }
  if (data.content) {
    tinymce.get("classContent").setContent(data.content);
    $(".hiddenTextarea").addClass("visible").removeClass("hidden");
    $("#openTextarea .plus").addClass("hidden").removeClass("visible");
    $('#contentShow').val(data.contentShow);
  }

  if (data.trainees) {
    data.trainees.forEach(trainee => {
      $('#placeClient').trigger('click');
      $('.clientName').last().val(trainee.ClientId).trigger('change')
      $('.clientCell').last().val(trainee.ClientId).trigger('change')
      let parent = $('.clientName').last().parent();
      if (trainee.MemberShip == "0") {
        if (trainee.ItemPrice && parseInt(trainee.ItemPrice) > 0) {
          parent.find('.clientCharge').val('amount').trigger('change')
          parent.find('.howMuchToCharge').val(parseInt(trainee.ItemPrice))
        } else {
          parent.find('.clientCharge').val('none').trigger('change')
        }
      }
      $.ajax({
        url: "GetUsersValidMembership.php",
        type: "post",
        data: JSON.stringify({clientId: trainee.ClientId}),
        dataType: "json",
        contentType: "application/json",
        success: function (response) {
          hideLoader();
          if (response.length) {
            response.forEach(function (activity) {
              parent.find('.clientCharge').append(`
                <option class="fromServer" ${activity.id == trainee.ClientActivitiesId ? "selected" : ""} value="${activity.id}">${activity.ItemText}</option>
              `);
            });
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          hideLoader();
          throw errorThrown;
        },
      })
    });
  }
}
function CancelPopupsByData(data){
  $.ajax({
    url: "DisableNewCalendarClass.php",
    type: "post",
    data: JSON.stringify(data),
    dataType: "json",
    contentType: "application/json",
    success: function (response) {
      console.log(response);
      hideLoader();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      hideLoader();
      throw errorThrown;
    },
  });
}


function openEditSmallPopup () {
  clearEditSmallPopup();
  openPopup("editPopup");
};

function editPopupButtonNext (ClassId){
  showLoader();
  $.ajax({
    url: "getSingleCalendarClass.php",
    type: "post",
    data: JSON.stringify({
      id: ClassId, // get from code somehow
    }),
    dataType: "json",
    contentType: "application/json",
    success: function (response) {
      console.log(response);
      if ($("#toCancelClass").is(":checked")) {
        let data = {
          id: response.id,
          GroupNumber: response.GroupNumber,
          editType: $("input[name='type']:checked").val(),
          cancel: true,
        };
        if (data.editType == "byDays") {
          data.editDays = getSelectedDaysSmallEditPopup();
        }
        console.log(data);
        CancelPopupsByData(data);
        closePopup("editPopup");
      } else {
        hideLoader();
        closePopup("editPopup");
        setBigPopupWithData(response);
        openPopup("mainPopup");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      throw errorThrown;
    },
  });
}
