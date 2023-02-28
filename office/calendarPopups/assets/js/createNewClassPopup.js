$(document).ready(function() {
  $("#summernote").summernote();
  $(document).on("click", ".classPopupButtonClose", function() {
    $("#calendarPopupClassSelect")
      .val($("#calendarPopupClassSelect option:first").val())
      .trigger("change");

    $("#calendarPopupClassSelect")
      .parents(".selectContainersHalf")
      .find(".newLabel")
      .removeClass("labelDisplayOn");
    $("#isNewClass").val("0");
    let color = $("#calendarPopupClassSelect")
      .children("option:selected")
      .attr("data-color");
    $(".colorGridSelect .selectedColor").css("background-color", color);
    $("#selectedColor").val(color);

    closePopup("createNewCalendar");
    openPopup("mainPopup");
  });

  // $('#createNewClassType #newClassDurationUnitType').change(function(){
  //   if($(this).val()=="דקות"){
  //     $('#createNewClassType #newClassDurationNumber').val(30).attr('max',59)
  //   }else{
  //     $('#createNewClassType #newClassDurationNumber').val(12).attr('max',23)
  //   }
  // })
  // $('#createNewClassType #newClassDurationNumber').on('input',function(){
  //   var value = $(this).val();
  //   var max = $(this).attr('max');
  //   if ((value !== '') && (value.indexOf('.') === -1)) {
  //       $(this).val(Math.max(Math.min(value, max),0));
  //   }
  // })

  $(document).on("click", "#classPopupButtonSave", function() {
    $("#isNewClass").val("1");
    closePopup("createNewCalendar");
    openPopup("mainPopup");
  });
  
  $("#customCheckboxInline-20").click(function(){
      alert("yoo");
  });
});
