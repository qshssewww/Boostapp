$(document).ready(function () {
  $(document).on("click", ".calendarPopupButtonClose", function () {
    $("#calendarPopupLocationSelect")
      .val($("#calendarPopupLocationSelect option:first").val())
      .trigger("change");

    $("#calendarPopupLocationSelect")
      .parents(".selectContainersHalf")
      .find(".newLabel")
      .removeClass("labelDisplayOn");
    $("#isNewLocation").val("0");

    closePopup("createNewCalendar");
    openPopup("mainPopup");
  });

  $(document).on("click", "#calendarPopupButtonSave", function () {
    $("#isNewLocation").val("1");
    closePopup("createNewCalendar");
    openPopup("mainPopup");
  });
});
