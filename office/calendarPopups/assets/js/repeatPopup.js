function DdMmtoMmDd(dateString) {
    var dateParts = dateString.split("/");
    return new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
  }
  function get05YearFromNow() {
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth();
    var day = d.getDate();
    return new Date(year, month+6, day);
  }

$(document).ready(function () {
  //select weekdays in popup
    let iteratedDate = DdMmtoMmDd($("#calendarPopupDateSelect").val());
    let iterateDay=iteratedDate.getDay();
    $(`.dayLi[value="${iterateDay}"]`).click();

  $(".dayLi").click(function () {
    if ($(this).attr("data-selected") == "0") {
      $(this).addClass("dayLiSelected");
      $(this).attr("data-selected", "1");
    } else {
      $(this).removeClass("dayLiSelected");
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
  function getSelectedDays() {
    let selectedDays = [];
    $(".dayLiSelected, .dayLiLocked").each(function () {
      selectedDays.push($(this).val());
    });
    return selectedDays;
  }
  function translateWeekDays(selectedDays) {
    selectedDays = selectedDays.sort().map((day) => {
      switch (day) {
        case 0:
          return "א";
        case 1:
          return "ב";
        case 2:
          return "ג";
        case 3:
          return "ד";
        case 4:
          return "ה";
        case 5:
          return "ו";
        case 6:
          return "ש";
      }
    });
    return selectedDays;
  }
  function generateFirstHalfString() {
    let ftype = $("#frequencyTypeOfUnit").val();
    let fnumber = $("#frequencyNumber").val();
    if (ftype == "1") {
      if (fnumber == 1) {
        return "כל יום";
      } else {
        return `כל ${fnumber} ימים`;
      }
    } else if (ftype == "2") {
      let dateArray = translateWeekDays(getSelectedDays());
      if (fnumber == 1) {
        if (dateArray.length) {
          if (dateArray.length == 1) {
            return `כל שבוע ביום ${dateArray[0]}`;
          } else {
            return `כל שבוע בימים ${dateArray.join()}`;
          }
        } else {
          return `כל שבוע`;
        }
      } else {
        if (dateArray.length) {
          if (dateArray.length == 1) {
            return `כל ${fnumber} שבועות ביום ${dateArray[0]}`;
          } else {
            return `כל ${fnumber} שבועות ביום ${dateArray.join()}`;
          }
        } else {
          return `כל ${fnumber} שבועות`;
        }
      }
    }
  }
  function generateSecondHalfString() {
    let radioVal = $("input[name='end']:checked").val();
    if (radioVal == 1) {
      return "";
    } else if (radioVal == 2) {
      return `עד לתאריך ${$("#ftime").val()}`;
    } else if (radioVal == 3) {
      return `למשך ${$("#howManyRepeatsNumber").val()} שיעורים`;
    } else {
      return "";
    }
  }
  function generateString() {
    return `${generateFirstHalfString()} ,${generateSecondHalfString()}`;
  }

  function generateDateArrays() {
    let radioVal = $("input[name='end']:checked").val();
    let newDateArray = [];
    let iteratedDate = DdMmtoMmDd($("#calendarPopupDateSelect").val());
    let whileIndex = 1;
    let selectedDays = getSelectedDays();
    if (radioVal == 1) {
      //1 year

      while (iteratedDate <= get05YearFromNow()) {
        if (CheckDateViaFilters(iteratedDate, selectedDays, whileIndex)) {
          newDateArray.push(moment(iteratedDate).format("DD/MM/YYYY"));
        }
        if (
          $("#frequencyTypeOfUnit").val() == "2" &&
          whileIndex % 7 == 0
        ) {
          iteratedDate = iteratedDate.addDays(
            ($("#frequencyNumber").val() - 1) * 7
          );
          iteratedDate = iteratedDate.addDays(1);
        } else {
          iteratedDate = iteratedDate.addDays(1);
        }
        whileIndex++;
      }
    } else if (radioVal == 2) {
      //some date
      while (iteratedDate <= DdMmtoMmDd($("#ftime").val())) {
        if (CheckDateViaFilters(iteratedDate, selectedDays, whileIndex)) {
          newDateArray.push(moment(iteratedDate).format("DD/MM/YYYY"));
        }
        if (
          $("#frequencyTypeOfUnit").val() == "2" &&
          whileIndex % 7 == 0
        ) {
          iteratedDate = iteratedDate.addDays(
            ($("#frequencyNumber").val() - 1) * 7
          );
          iteratedDate = iteratedDate.addDays(1);
        } else {
          iteratedDate = iteratedDate.addDays(1);
        }
        whileIndex++;
      }
    } else if (radioVal == 3) {
      // run untill done
      let RepeatsCount = 0;
      let targetRepeats = $("#howManyRepeatsNumber").val();
      while (RepeatsCount < targetRepeats) {
        if (CheckDateViaFilters(iteratedDate, selectedDays, whileIndex)) {
          newDateArray.push(moment(iteratedDate).format("DD/MM/YYYY"));
          RepeatsCount++;
        }
        if (
          $("#frequencyTypeOfUnit").val() == "2" &&
          whileIndex % 7 == 0
        ) {
          iteratedDate = iteratedDate.addDays(
            ($("#frequencyNumber").val() - 1) * 7
          );
          iteratedDate = iteratedDate.addDays(1);
        } else {
          iteratedDate = iteratedDate.addDays(1);
        }
        whileIndex++;
      }
    }
    return newDateArray;
  }

  Date.prototype.addDays = function (days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
  };

  function CheckDateViaFilters(date, selectedDaysArray, index) {
    if ($("#frequencyTypeOfUnit").val() == "2") {
      return selectedDaysArray.includes(date.getDay());
    } else if ($("#frequencyTypeOfUnit").val() == "1") {
      if (index % $("#frequencyNumber").val() == 0) {
        return true;
      } else {
        return false;
      }
    }
  }

  function onPopupSave() {
    let string = generateString();
    let arrays = generateDateArrays();
    return { string: string, dateArray: arrays, systematic: false ,type: $('#classFormType').val()};
  }
  function setInputsSystematicaly(data){
        $('#frequencyNumber').val(data.repeatNumber)
        $('#frequencyTypeOfUnit').val(data.repeatType).trigger('change')
        if(data.repeatDays){
            let repeatDaysArray=data.repeatDays.split(",");
            repeatDaysArray.forEach(function(day){
                $(`.dayLi[value="${day}"]`).click();
            })
        }
        if(data.endType==1){
            $("#never").prop("checked", true);
        }else if(data.endType==2){
            $("#given_date").prop("checked", true);
            $('#ftime').val(data.endDate)

        }else{
            $("#repeat_num").prop("checked", true);
            $('#howManyRepeatsNumber').val(data.endNumber)
        }
  }

  function onSystematicSave(data) {
    setInputsSystematicaly(data);
    let string = generateString();
    let arrays = generateDateArrays();
    return { string: string, dateArray: arrays, systematic: true ,type: data.Type,id:data.id};
  }

  $("#repetitionInput1, #repetitionInput2").change(function () {
    if ($(this).children("option:selected").attr("data-content")) {
      let inputData = JSON.parse(
        $(this).children("option:selected").attr("data-content")
      );
      let systematicSaveData=onSystematicSave(inputData);
      $("#datesArrayValues").val(systematicSaveData.dateArray);
      $("#datesIsSystematic").val(systematicSaveData.systematic);
      $("#repetitionTableId").val(systematicSaveData.id);
      $("#repetitionTableName").val(systematicSaveData.string);
    }
  });

  $(document).on("click", "#repeatPopupButtonSave", function () {
    let saveData = onPopupSave();
    let selectorNumber=$('#classFormType').val()
    $("#datesArrayValues").val(saveData.dateArray);
    $("#datesIsSystematic").val(saveData.systematic);
    $("#repetitionTableName").val(saveData.string);
    var newOption = new Option(saveData.string, saveData.string, false, true);
    $(newOption).insertBefore(`#repetitionInput${selectorNumber} option:last`);
    $(`#repetitionInput${selectorNumber}`).trigger("change");
    function deleteValue() {
      $(`#repetitionInput${selectorNumber} option[value='${saveData.string}']`).remove();
      $(`#repetitionInput${selectorNumber}`).unbind("change", deleteValue);
    }
    $(`#repetitionInput${selectorNumber}`).bind("change", deleteValue);

    // $("#repetitionInput2").append(newOption).val(saveData.string).trigger("change");
    // function deleteValue2() {
    //   $(`#repetitionInput2 option[value='${saveData.string}']`).remove();
    //   $("#repetitionInput2").unbind("change", deleteValue2);
    // }
    // $("#repetitionInput2").bind("change", deleteValue2);


    closePopup("frequencySettings");
    openPopup("mainPopup");
  });
  $('#calendarPopupDateSelect').change(function(){
      $('#repetitionInput1, #repetitionInput2').val('once').trigger('change')
  })
  $(document).on("click", ".closeFrequencyPopup", function () {
    $("#repetitionInput1 , #repetitionInput2").val("once").trigger("change");
    closePopup("frequencySettings");
    openPopup("mainPopup");
  });
});
