$(document).ready(function () {
  function generateString() {
    let unit;
    if($("#lateCancelUnitType").val()==1){
      unit="ימים";
    }
    if($("#lateCancelUnitType").val()==2){
      unit="דקות";
    }
    if($("#lateCancelUnitType").val()==2){
      unit="שעות";
    }
    
    return `ביטול מאוחר - ${$(
      "#lateCancelNumber"
    ).val()} ${unit} לפני השיעור`;
  }

  function onPopupSave() {
    let string = generateString();
    let cancelData = {
      allowLateCancel: true,
      allowDisableButton: $("#disableCancelButton").prop("checked"),
      lateNumber: $("#lateCancelNumber").val(),
      lateUnits: $("#lateCancelUnitType").val(),
      disableNumber: $("#disableCancelButtonNumber").val(),
      disableUnits: $("#disableCancelButtonUnitType").val(),
      systematic: false,
      type:$('#classFormType').val(),
      name:string
    };
    return { string: string, cancelData: cancelData };
  }
  function onSystematicSave(data) {
    let string = data.name;
    let cancelData = {
      allowLateCancel: data.allowCancel == "1" ? true : false,
      allowDisableButton: data.allowButtonBlock == "1" ? true : false,
      lateNumber: data.cancelationNumber,
      lateUnits: data.cancelationType,
      disableNumber: data.buttonBlockNumber,
      disableUnits: data.buttonBlockType,
      systematic: true,
      type:data.Type,
      name:string,
      id:data.id
    };
    return { string: string, cancelData: cancelData };
  }
    $("#cancelationInput1 , #cancelationInput2").change(function () {
      if (
        $(this).children("option:selected").attr("data-content")
      ) {
        let inputData = JSON.parse(
          $(this).children("option:selected").attr("data-content")
        );
        let systematicSaveData= onSystematicSave(inputData);
        $("#cancelDataInput").val(JSON.stringify(systematicSaveData.cancelData));
      }
    });

  $(document).on("click", "#cancelPopupButtonSave", function () {
    let saveData = onPopupSave();
    let selectorNumber=$('#classFormType').val()
    $("#cancelDataInput").val(JSON.stringify(saveData.cancelData));
    var newOption = new Option(saveData.string, saveData.string, false, false);

    $(`#cancelationInput${selectorNumber}`).append(newOption).val(saveData.string).trigger("change");
    function deleteValue() {
      $(`#cancelationInput${selectorNumber} option[value='${saveData.string}']`).remove();
      $(`#cancelationInput${selectorNumber}`).unbind("change", deleteValue);
    }
    $(`#cancelationInput${selectorNumber}`).bind("change", deleteValue);

    // $("#cancelationInput2").append(newOption).val(saveData.string).trigger("change");
    // function deleteValue2() {
    //   $(`#cancelationInput2 option[value='${saveData.string}']`).remove();
    //   $("#cancelationInput2").unbind("change", deleteValue2);
    // }
    // $("#cancelationInput2").bind("change", deleteValue2);

    closePopup("cancelPopup");
    openPopup("mainPopup");
  });
  $(document).on("click", ".closeCancelPopup", function () {
    $("#cancelationInput1 , #cancelationInput2").val("no").trigger("change");
    closePopup("cancelPopup");
    openPopup("mainPopup");
  });
});
