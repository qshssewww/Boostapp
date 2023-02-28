function onSystematicRegisterLimitAdd(limitsData) {
  limitsData.forEach((singleLimit) => {
    let dataObj = generateDataFromSysDataRegisterLimit(singleLimit);
    $("#registerPopupContainer" + type).prepend(`
    <div class="openRegisterLine" id="openRegisterLine${dataObj.id}">
        <div class="rowIconContainer">
            <i class="far fa-hand-paper"></i>
        </div>
        <input type="hidden" class="openRegisterLineHiddenInput">
        <div class="text">${dataObj.string}</div>
        <div class="mr-2 editRegisterLine"><i class="fas fa-pencil-alt"></i></div>      
        <div class="text-danger deleteOpenRegisterLine mis-9"><i class="fas fa-do-not-enter"></i></div>      
    </div>`);
    $("#openRegisterLine" + dataObj.id).find('.openRegisterLineHiddenInput').val(JSON.stringify(dataObj));

  });
  fillMainSelectWithData();
}
function checkHours() {
  let string = "";
  let hours = $(".hiddenHoursLimit").hasClass("visible");
  if (hours) {
    if (
      parseInt($("#limitFromHour").val().replace(":", "")) >
      parseInt($("#limitToHour").val().replace(":", ""))
    ) {
      // alert("זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה")
      Swal.fire(
        "",
        "זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה",
        "error"
      );
      return null;
    }
    hours = {
      from: $("#limitFromHour").val(),
      to: $("#limitToHour").val(),
    };
    string += `בין השעות ${$("#limitToHour").val()}-${$(
      "#limitFromHour"
    ).val()}, `;
  }
}
function checkExtraHour() {
  let string = "";
  let extraHours = $(".extraHoursLimitContainer").is(":visible");
  if (extraHours) {
    if (
      parseInt($("#limitFromHour2").val().replace(":", "")) >
      parseInt($("#limitToHour2").val().replace(":", ""))
    ) {
      // alert("זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה")
      Swal.fire(
        "",
        "זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה",
        "error"
      );
      return null;
    }
    let time1 = parseInt($("#limitFromHour").val().replace(":", ""));
    let time2 = parseInt($("#limitToHour").val().replace(":", ""));
    let time3 = parseInt($("#limitFromHour2").val().replace(":", ""));
    let time4 = parseInt($("#limitToHour2").val().replace(":", ""));
    if (
      (time1 > time3 && time1 < time4) ||
      (time3 > time1 && time3 < time2) ||
      time1 == time3
    ) {
      // alert("שעות המגבלות אינן יכולות לחפוף")
      Swal.fire("", "שעות המגבלות אינן יכולות לחפוף", "error");
      return null;
    }

    extraHours = {
      from: $("#limitFromHour2").val(),
      to: $("#limitToHour2").val(),
    };
    string += `ובין השעות ${$("#limitToHour2").val()}-${$(
      "#limitFromHour2"
    ).val()}, `;
  }
}
function generateDataFromSysDataRegisterLimit(sysData) {
  let data = {
    classes: null,
    maximum: null,
    days: null,
    hours: null,
    extraHours: null,
    register: null,
    string: null,
    id: Date.now(),
  };
  sysData.forEach((singleLimit) => {
    if (singleLimit.Group == "Class") {
      if (singleLimit.Item == "Class") {
        //Supported only in class (ba999)! not supported meetings and complexes
        if ((singleLimit.Class).includes('BA999')) {
          data.classes = "all";
        } else {
          data.classes = singleLimit.Class.split(",");
        }
      }
    }
    if (singleLimit.Group == "String") {
      if (singleLimit.Item == "String") {
        data.string = singleLimit.Value;
      }
    }

    if (singleLimit.Group == "Day") {
      if (singleLimit.Item == "Days") {
        data.days = stringToDaysArray(singleLimit.Value);
      }
    }
    if (singleLimit.Group == "Time") {
      if (singleLimit.Item == "Time") {
        let parsedTimeData = JSON.parse(singleLimit.Value);
        if (data.hours) {
          data.extraHours = {
            from: parsedTimeData.data[0].FromTime,
            to: parsedTimeData.data[0].ToTime,
          };
        } else {
          data.hours = {
            from: parsedTimeData.data[0].FromTime,
            to: parsedTimeData.data[0].ToTime,
          };
        }
      }
    }
    if (singleLimit.Group == "Item") {
      if (singleLimit.Item == "StandBy") {
        let parsedStandByData = JSON.parse(singleLimit.Value);
        if(Array.isArray(parsedStandByData.data)) {
          data.register = {
            number: parsedStandByData.data[0].StandByCount,
            type: parsedStandByData.data[0].StandByVaild_Type,
            timingNumber: parsedStandByData.data[0].StandByTime,
            timingType: parsedStandByData.data[0].StandByTimeVaild_Type,
          };
        }
        else{
          data.register = {
            number: parsedStandByData.data.StandByCount,
            type: parsedStandByData.data.StandByVaild_Type,
            timingNumber: parsedStandByData.data.StandByTime,
            timingType: parsedStandByData.data.StandByTimeVaild_Type,
          };
        }
      }
    }
    if (singleLimit.Group == "Max") {
      if (data.maximum === null) {
        data.maximum = [];
      }
      if (singleLimit.Item == "Day") {
        data.maximum.push({
          type: 1,
          number: singleLimit.Value,
        });
      }
      if (singleLimit.Item == "Week") {
        data.maximum.push({
          type: 2,
          number: singleLimit.Value,
        });
      }
      if (singleLimit.Item == "Month") {
        data.maximum.push({
          type: 3,
          number: singleLimit.Value,
        });
      }
      if (singleLimit.Item == "Year") {
        data.maximum.push({
          type: 4,
          number: singleLimit.Value,
        });
      }
    }
  });

  let register = $(".hiddenRegisterLimits").hasClass("visible");
  if (register) {
    register = {
      number: $("#registerLimitNumber").val(),
      type: $("#registerLimitType").val(),
      timingNumber: $("#registerTimingInput").val(),
      timingType: $("#registerLimitTimingType").val(),
    };
  }
  return data;
}
function stringToDaysArray(days) {
  let newArr = days.split(",");
  newArr = newArr
    .filter((day) => day != "")
    .map((day) => {
      switch (day) {
        case "ראשון":
          return 0;
        case "שני":
          return 1;
        case "שלישי":
          return 2;
        case "רביעי":
          return 3;
        case "חמישי":
          return 4;
        case "שישי":
          return 5;
        case "שבת":
          return 6;
      }
    });
  return newArr;
}
function clearMainSelect() {
  $("#registerPopupClassSelect").select2("destroy");
  $("#registerPopupClassSelect option").each(function () {
    $(this).prop("disabled", false);
  });
  $('.openRegisterLimitPopup').removeClass('DISPLAYNONEIMPORTANT')
  initialSetSelect2();
}

function fillMainSelectWithData(openedID=null) {
  $("#registerPopupClassSelect").select2("destroy");

  let selectedValues = [];
  $('.openRegisterLineHiddenInput').each(function(){
    let value= JSON.parse($(this).val());
    if(!(openedID && openedID==value.id)){
      if(value.classes=="all"){
        selectedValues.push("all");
      }else{
        value.classes.forEach(singleClass=>{
          selectedValues.push(singleClass);
        })
      }
    }
  })
  let hideButton = false;
  if (selectedValues.length > 0) {
    $('#registerPopupClassSelect option[value="all"]').prop("disabled", true);
  }else{
    $('#registerPopupClassSelect option[value="all"]').prop("disabled", false);
  }
  if (selectedValues.includes("all")) {
    // $("#registerPopupClassSelect option").each(function () {
    //   $(this).prop("disabled", true);
    // });
    hideButton=true;
  } else {
    hideButton=true;
    $("#registerPopupClassSelect option").each(function () {
      if ($(this).val() != "all") {
        if (selectedValues.includes($(this).val())) {
          $(this).prop("disabled", true);
        } else {
          $(this).prop("disabled", false);
          hideButton=false;
        }
      }
    });
  }
  initialSetSelect2();
  if(hideButton){
    $('.openRegisterLimitPopup').addClass('DISPLAYNONEIMPORTANT')
  }else{
    $('.openRegisterLimitPopup').removeClass('DISPLAYNONEIMPORTANT')

  }
}

function initialSetSelect2() {
  //select2
  $("#registerPopupClassSelect")
    .select2({
      minimumResultsForSearch: -1,
    })
    .on("change.select2", function () {
      //make sure only all is selected
      if ($(this).val() && $(this).val().length) {
        if ($(this).val().includes("all")) {
          if ($(this).val().length != 1) {
            $(this).val(["all"]).trigger("change");
          }
        }
        $(".classSelectDependent").show();
        if ($(".maxLimitLine").length > 1) {
          $(".oneMaxDependent").show();
        }
      } else {
        $("#registerLimitationChoose").val("0").trigger("change");
        $(".classSelectDependent").hide();
        $(".oneMaxDependent").hide();
      }

      //disable annoying dropdown on delete
      $("#registerPopupClassSelect").on("select2:unselect", function (evt) {
        if (!evt.params.originalEvent) {
          return;
        }

        evt.params.originalEvent.stopPropagation();
      });
    });
}

$(document).ready(function () {


  function disableInputs() {
    let valArray = [];
    $(".maxLimitLineType option").each(function () {
      $(this).attr("disabled", false);
      $(this).css("display", "block");
    });
    $(".maxLimitLineType").each(function () {
      valArray.push($(this).val());
    });
    $(".maxLimitLineType").each(function () {
      let This = $(this);
      $(this)
        .find("option")
        .each(function () {
          if (This.val() != $(this).val() && valArray.includes($(this).val())) {
            $(this).attr("disabled", true);
            $(this).css("display", "none");
          }
        });
    });
  }

  $(document).on("change", "#limitToHour2, #limitFromHour2", function () {
    console.log(3);
    checkExtraHour();
  });

  $(document).on("change", "#limitToHour, #limitFromHour", function () {
    console.log(4);
    checkHours();
  });
  $(document).on("change", ".maxLimitLineType", function () {
    disableInputs();
  });

  $("#registerLimitPopupOpenSelect").change(function () {
    if ($(this).val() == "1") {
      addEmptyRow();
      $("#openRegisterLimits").show();
      $("#openRegisterLimits .plus").show();
    }
    if ($(this).val() == "2") {
      openDaysLimit();
    }
    if ($(this).val() == "3") {
      if ($(".hiddenHoursLimit").hasClass("hidden")) {
        openHoursLimit();
      } else {
        openExtraHoursLimit();
      }
    }
    if ($(this).val() == "4") {
      openRegisterLimits();
    }
    disableSelect();
  });

  function disableSelect() {
    if ($(".maxLimitLine").length == 4) {
      $("#registerLimitPopupOpenSelect option[value='1']").addClass(
        "DISPLAYNONEIMPORTANT"
      );
    } else {
      $("#registerLimitPopupOpenSelect option[value='1']").removeClass(
        "DISPLAYNONEIMPORTANT"
      );
    }

    if ($(".hiddenDaysLimit").hasClass("visible")) {
      $("#registerLimitPopupOpenSelect option[value='2']").addClass(
        "DISPLAYNONEIMPORTANT"
      );
    } else {
      $("#registerLimitPopupOpenSelect option[value='2']").removeClass(
        "DISPLAYNONEIMPORTANT"
      );
    }

    if ($(".extraHoursLimitContainer").hasClass("extraHoursIsOpen")) {
      $("#registerLimitPopupOpenSelect option[value='3']").addClass(
        "DISPLAYNONEIMPORTANT"
      );
    } else {
      $("#registerLimitPopupOpenSelect option[value='3']").removeClass(
        "DISPLAYNONEIMPORTANT"
      );
    }

    if ($(".maxLimitLine").length == 0 || $(".hiddenRegisterLimits").hasClass("visible")) {
      $("#registerLimitPopupOpenSelect option[value='4']").addClass(
        "DISPLAYNONEIMPORTANT"
      );
    } else {
      $("#registerLimitPopupOpenSelect option[value='4']").removeClass(
        "DISPLAYNONEIMPORTANT"
      );
    }

    let num = $("#registerLimitPopupOpenSelect option").not(
      ".DISPLAYNONEIMPORTANT"
    ).length;
    if (num == 1) {
      $("#registerLimitPopupOpenSelect").addClass("DISPLAYNONEIMPORTANT");
    } else {
      $("#registerLimitPopupOpenSelect").removeClass("DISPLAYNONEIMPORTANT");
    }
    if ($("#registerLimitPopupOpenSelect").val() != 0) {
      $("#registerLimitPopupOpenSelect").val(0).trigger("change");
    }
  }

  initialSetSelect2();

  function openDaysLimit() {
    showHidden($("#openDaysLimit"), ".hiddenDaysLimit");
  }

  function closeDaysLimit() {
    hideHidden($("#closeDaysLimit"), ".hiddenDaysLimit");
    $(".hiddenDaysLimit")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openDaysLimit").on("click", function (e) {
    openDaysLimit();
  });

  $("#closeDaysLimit").on("click", function (e) {
    closeDaysLimit();
    disableSelect();
  });

  function selectLimitDay(jqueryObj) {
    jqueryObj.addClass("limitDayLiSelected");
    jqueryObj.attr("data-selected", "1");
  }
  function deselectLimitDay(jqueryObj) {
    jqueryObj.removeClass("limitDayLiSelected");
    jqueryObj.attr("data-selected", "0");
  }

  function selectLimitDaysViaArray(array) {
    deselectAllLimitDays();
    $(".limitDayLi").each(function () {
      if (array.includes($(this).val())) {
        selectLimitDay($(this));
      }
    });
  }
  function deselectAllLimitDays() {
    $(".limitDayLi").each(function () {
      deselectLimitDay($(this));
    });
  }

  $(".limitDayLi").click(function () {
    if ($(this).attr("data-selected") == "0") {
      selectLimitDay($(this));
    } else {
      deselectLimitDay($(this));
    }
  });

  //function to get selected days from weekdays list
  function getSelectedDays() {
    let selectedDays = [];
    $(".limitDayLiSelected").each(function () {
      selectedDays.push($(this).val());
    });
    return selectedDays;
  }

  function openHoursLimit() {
    showHidden($("#openHoursLimit"), ".hiddenHoursLimit");
  }

  function closeHoursLimit() {
    hideHidden($("#closeHoursLimit"), ".hiddenHoursLimit");
    $(".hiddenHoursLimit")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openHoursLimit").on("click", function (e) {
    openHoursLimit();
  });

  $("#closeHoursLimit").on("click", function (e) {
    closeHoursLimit();
    disableSelect();
  });

  $("#limitFromHour, #limitToHour,#limitFromHour2, #limitToHour2").timepicker({
    timeFormat: "H:i",
  });
  function openExtraHoursLimit() {
    $("#addAnotherHourLimit").hide();
    $("#closeHoursLimit").hide();
    $(".extraHoursLimitContainer").show();
    $(".extraHoursLimitContainer").addClass("extraHoursIsOpen");
  }
  function closeExtraHoursLimit() {
    $("#addAnotherHourLimit").show();
    $("#closeHoursLimit").show();
    $(".extraHoursLimitContainer").hide();
    $(".extraHoursLimitContainer").removeClass("extraHoursIsOpen");
  }
  $("#addAnotherHourLimit").click(function () {
    openExtraHoursLimit();
  });
  $("#closeExtraHoursLimit").click(function () {
    closeExtraHoursLimit();
    disableSelect();
  });

  function addEmptyRow() {
    let valArray = [];
    let arrayOfOptions = ["1", "2", "3", "4"];
    $(".maxLimitLineType").each(function () {
      valArray.push($(this).val());
    });
    arrayOfOptions = arrayOfOptions.filter((option) => {
      if (valArray.includes(option)) {
        return false;
      } else {
        return true;
      }
    });
    if ($(".maxLimitLine").length < 4) {
      $(".maxLimitContainer").prepend(`
      <div class="maxLimitLine" style="display:none" >
          <input class="cute-input ml-2 maxLimitLineNumber" type="number" style='width:50px;'>
          <div class="ml-2" style="
          font-size: 1em;  overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;"
          >הרשמות לשיעורים</div> 
          <select class="cute-input maxLimitLineType">
            <option ${
              arrayOfOptions[0] == 1 ? "selected" : ""
            } value="1">ביום</option>
            <option ${
              arrayOfOptions[0] == 2 ? "selected" : ""
            } value="2">בשבוע</option>
            <option ${
              arrayOfOptions[0] == 3 ? "selected" : ""
            } value="3">בחודש</option>
            <option ${
              arrayOfOptions[0] == 4 ? "selected" : ""
            } value="4">בשנה</option>
          </select>
          <div class="text-danger closeMaxLimitLine mis-9"><i class="fas fa-do-not-enter"></i></div>
      </div>
      `);
      $(".maxLimitLine").show();
      $(".oneMaxDependent").show();
    }
    if ($(".maxLimitLine").length == 4) {
      $("#openMaxLimit").hide();
    }
    $("#hiddenMaxLimitHeader").show();
    disableInputs();
  }

  function addRowWithValue(number, type) {
    if ($(".maxLimitLine").length < 4) {
      $(".maxLimitContainer").prepend(`
      <div class="maxLimitLine" style="display:none" >
          <input class="cute-input ml-2 maxLimitLineNumber" type="number" value="${number}" style='width:50px;'>
          <div class="ml-2" style="
          font-size: 1em;  overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;"
          >הרשמות לשיעורים</div> 
          <select class="cute-input maxLimitLineType">
            <option ${type == 1 ? "selected" : ""} value="1">ביום</option>
            <option ${type == 2 ? "selected" : ""} value="2">בשבוע</option>
            <option ${type == 3 ? "selected" : ""} value="3">בחודש</option>
            <option ${type == 4 ? "selected" : ""} value="4">בשנה</option>
          </select>
          <div class="text-danger closeMaxLimitLine mis-9"><i class="fas fa-do-not-enter"></i></div>
      </div>
      `);
      $(".maxLimitLine").show();
      $(".oneMaxDependent").show();
    }
    if ($(".maxLimitLine").length == 4) {
      $("#openMaxLimit").hide();
    }
    $("#hiddenMaxLimitHeader").show();
    disableInputs;
  }

  $("#openMaxLimit").click(function () {
    addEmptyRow();
  });

  function closeMaxLimit(jqueryObj) {
    $("#openMaxLimit").show();
    if ($(".maxLimitLine").length === 1) {
      $("#hiddenMaxLimitHeader").hide();
      $(".oneMaxDependent").hide();
    }
    jqueryObj.parents(".maxLimitLine").hide(200, function () {
      $(this).remove();
      disableInputs();
      disableSelect();
    });
  }
  function CloseAllMaxLimit() {
    $(".closeMaxLimitLine").each(function () {
      closeMaxLimit($(this));
    });
  }

  $(document).on("click", ".closeMaxLimitLine", function () {
    closeMaxLimit($(this));
  });

  function openRegisterLimits() {
    showHidden($("#openRegisterLimits"), ".hiddenRegisterLimits");
  }

  function closeRegisterLimits() {
    hideHidden($("#closeRegisterLimits"), ".hiddenRegisterLimits");
    $(".hiddenRegisterLimits")
      .parents(".rowInput")
      .find(".plus")
      .removeClass("hidden")
      .addClass("visible");
  }

  $("#openRegisterLimits").on("click", function (e) {
    openRegisterLimits();
  });

  $("#closeRegisterLimits").on("click", function (e) {
    closeRegisterLimits();
    disableSelect();
  });

  function setEmptyPopupValues() {
    $("#registerLimitLineId").val("");
    $("#registerLimitNumber").val(null);
    $("#registerLimitType").val(2).trigger("change");
    $("#registerTimingInput").val("");
    $("#registerLimitTimingType").val(2).trigger("change");
    closeRegisterLimits();
    CloseAllMaxLimit();
    deselectAllLimitDays();
    closeDaysLimit();
    $("#limitFromHour2").val(null);
    $("#limitToHour2").val(null);
    closeExtraHoursLimit();
    $("#limitFromHour").val(null);
    $("#limitToHour").val(null);
    closeHoursLimit();
    $("#registerPopupClassSelect").val(null).trigger("change");
    disableSelect();
  }

  function setUnemptyPopupValues(data) {
    setEmptyPopupValues();
    $("#registerLimitLineId").val(data.id);
    if (data.days) {
      openDaysLimit();
      selectLimitDaysViaArray(data.days);
    }
    if (data.hours) {
      openHoursLimit();
      $("#limitFromHour").val(data.hours.from);
      $("#limitToHour").val(data.hours.to);
      if (data.extraHours) {
        openExtraHoursLimit();
        $("#limitFromHour2").val(data.extraHours.from);
        $("#limitToHour2").val(data.extraHours.to);
      }
    }
    if (data.maximum) {
      data.maximum.forEach((item) => {
        addRowWithValue(item.number, item.type);
      });
      if (data.register) {
        openRegisterLimits();
        $("#registerLimitNumber").val(data.register.number);
        $("#registerLimitType").val(data.register.type).trigger("change");
        $("#registerTimingInput").val(data.register.timingNumber);
        $("#registerLimitTimingType")
          .val(data.register.timingType)
          .trigger("change");
      }
    }

    if (data.classes == "any") {
      $("#registerPopupClassSelect").val(["any"]).trigger("change");
    } else {
      $("#registerPopupClassSelect").val(data.classes).trigger("change");
    }
    disableSelect();
  }

  function generateObjFromData() {
    let string = "";
    let id = $("#registerLimitLineId").val();
    if (!id || id == "") {
      id = Date.now();
    }
    let classes = $("#registerPopupClassSelect").val();
    if (!classes || classes.length == 0) {
      // alert("יש להכניס שיעורים")

      Swal.fire("", "יש להכניס שיעורים", "error");
      return null;
    }
    if (classes.includes("all")) {
      classes = "all";
      string += "כל השיעורים, ";
    } else {
      $("#registerLimitPopup .select2-selection__choice").each(function () {
        string += `${$(this).attr("title")}, `;
      });
    }

    let maximum = $(".maxLimitLine").length ? true : false;
    if (maximum) {
      let types = [];
      $(".maxLimitLineType").each(function () {
        types.push($(this).val());
      });
      if (!checkIfArrayIsUnique(types)) {
        // alert("סוגי מגבלות מקסימום צריכים להיות ייחודיים")
        Swal.fire("", "סוגי מגבלות מקסימום צריכים להיות ייחודיים", "error");
        return null;
      }
      let emptyMax = false;
      $(".maxLimitLineNumber").each(function () {
        if (!$(this).val() || $(this).val == "") {
          emptyMax = true;
        }
      });
      if (emptyMax) {
        // alert("סוגי מגבלות מקסימום צריכים להיות ייחודיים")
        Swal.fire("", "יש להזין מספר הרשמות לשיעורים", "error");
        return null;
      }
      maximum = [];
      $(".maxLimitLine").each(function () {
        maximum.push({
          type: $(this).find(".maxLimitLineType").val(),
          number: $(this).find(".maxLimitLineNumber").val(),
        });
        string += `עד ${$(this).find(".maxLimitLineNumber").val()} הרשמות ${$(
          this
        )
          .find(".maxLimitLineType option:selected")
          .text()}, `;
      });
    }

    let days = $(".hiddenDaysLimit").hasClass("visible");
    if (days) {
      days = getSelectedDays();
      if (days.length == 0) {
        // alert("סוגי מגבלות מקסימום צריכים להיות ייחודיים")
        Swal.fire("", "יש לבחור ימים למגבלה", "error");
        return null;
      }
      string += `בימים `;
      $("#registerLimitPopup .limitDayLiSelected").each(function () {
        string += `${$(this).html()}, `;
      });
    }

    let hours = $(".hiddenHoursLimit").hasClass("visible");
    if (hours) {
      if (
        parseInt($("#limitFromHour").val().replace(":", "")) >
        parseInt($("#limitToHour").val().replace(":", ""))
      ) {
        // alert("זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה")
        Swal.fire(
          "",
          "זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה",
          "error"
        );
        return null;
      }
      hours = {
        from: $("#limitFromHour").val(),
        to: $("#limitToHour").val(),
      };
      string += `בין השעות ${$("#limitToHour").val()}-${$(
        "#limitFromHour"
      ).val()}, `;
    }
    let extraHours = $(".extraHoursLimitContainer").is(":visible");
    if (extraHours) {
      if (
        parseInt($("#limitFromHour2").val().replace(":", "")) >
        parseInt($("#limitToHour2").val().replace(":", ""))
      ) {
        // alert("זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה")
        Swal.fire(
          "",
          "זמן תחילת המגבלה אינו יכול להיות גדול מזמן סוף המגבלה",
          "error"
        );
        return null;
      }
      let time1 = parseInt($("#limitFromHour").val().replace(":", ""));
      let time2 = parseInt($("#limitToHour").val().replace(":", ""));
      let time3 = parseInt($("#limitFromHour2").val().replace(":", ""));
      let time4 = parseInt($("#limitToHour2").val().replace(":", ""));
      if (
        (time1 > time3 && time1 < time4) ||
        (time3 > time1 && time3 < time2) ||
        time1 == time3
      ) {
        // alert("שעות המגבלות אינן יכולות לחפוף")
        Swal.fire("", "שעות המגבלות אינן יכולות לחפוף", "error");
        return null;
      }

      extraHours = {
        from: $("#limitFromHour2").val(),
        to: $("#limitToHour2").val(),
      };
      string += `ובין השעות ${$("#limitToHour2").val()}-${$(
        "#limitFromHour2"
      ).val()}, `;
    }
    let register = $(".hiddenRegisterLimits").hasClass("visible");
    if (register) {
      if (
        !$("#registerLimitNumber").val() ||
        $("#registerLimitNumber").val() == "" ||
        !$("#registerTimingInput").val() ||
        $("#registerTimingInput").val() == ""
      ) {
        // alert("שעות המגבלות אינן יכולות לחפוף")
        Swal.fire("", "יש להזין מגבלת הרשמה לשיעורים", "error");
        return null;
      }
      register = {
        number: $("#registerLimitNumber").val(),
        type: $("#registerLimitType").val(),
        timingNumber: $("#registerTimingInput").val(),
        timingType: $("#registerLimitTimingType").val(),
      };
      string += "הרשמות על בסיס פנוי, ";
      if (register.number) {
        string += `${$("#registerLimitNumber").val()} הרשמות ${$(
          "#registerLimitType option:selected"
        ).text()} `;
      }
      if (register.timingNumber) {
        string += `כ-${$("#registerTimingInput").val()} ${$(
          "#registerLimitTimingType option:selected"
        ).text()} לפני תחילת השיעור, `;
      }
    }
    string = string.slice(0, string.length - 2);
    string += ".";
    let data = {
      classes,
      maximum,
      days,
      hours,
      extraHours,
      register,
      string,
      id: id,
    };
    return data;
  }
  $(document).on("click", ".editRegisterLine", function () {
    let Data = $(this)
      .parents(".openRegisterLine")
      .find(".openRegisterLineHiddenInput")
      .val();
    console.log(Data);
    let ParsedDATA=JSON.parse(Data)
    fillMainSelectWithData(ParsedDATA.id);
    setUnemptyPopupValues(ParsedDATA);
    closePopup("mainShopPopup");
    openPopup("registerLimitPopup");
  });

  function onPopupSave() {
    let dataObj = generateObjFromData();
    console.log(dataObj);
    if (dataObj) {
      if (
        $("#registerLimitLineId").val() &&
        $("#registerLimitLineId").val() != ""
      ) {
        //has id so just update
        $(`#openRegisterLine${dataObj.id}`)
          .find(".openRegisterLineHiddenInput")
          .val(JSON.stringify(dataObj));
        $(`#openRegisterLine${dataObj.id}`).find(".text").html(dataObj.string);
      } else {
        // no id so create new
        $("#registerPopupContainer" + type).prepend(`
        <div class="openRegisterLine" id="openRegisterLine${dataObj.id}">
            <div class="rowIconContainer">
                <i class="far fa-hand-paper"></i>
            </div>
            <input type="hidden" class="openRegisterLineHiddenInput">
            <div class="text">${dataObj.string}</div>
            <div class="mr-2 editRegisterLine"><i class="fas fa-pencil-alt"></i></div>      
            <div class="text-danger deleteOpenRegisterLine mis-9"><i class="fas fa-do-not-enter"></i></div>      
        </div>
        `);
        $("#openRegisterLine" + dataObj.id).find('.openRegisterLineHiddenInput').val(JSON.stringify(dataObj));

      }
    }
    return dataObj;
  }
  $(document).on("click", ".deleteOpenRegisterLine", function () {
    $(this)
      .parents(".openRegisterLine")
      .hide(500, function () {
        $(this).remove();
        fillMainSelectWithData()
      });
  });

  $(document).on("click", ".openRegisterLimitPopup", function () {
    fillMainSelectWithData();
    setEmptyPopupValues();
    closePopup("mainShopPopup");
    openPopup("registerLimitPopup");
  });

  $(document).on("click", "#registerLimitPopupButtonSave", function () {
    let dataObj = onPopupSave();
    if (dataObj) {
      fillMainSelectWithData();
      closePopup("registerLimitPopup");
      setEmptyPopupValues();
      openPopup("mainShopPopup");
    }
  });
  $(document).on("click", ".closeRegisterLimitPopup", function () {
    closePopup("registerLimitPopup");
    fillMainSelectWithData();
    setEmptyPopupValues();
    openPopup("mainShopPopup");
  });
});
