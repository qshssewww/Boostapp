function initCallbackLinks() {
  let shopRow = $("#shopLinksTable .shopRowSeparator");
  shopRow.parents("tr").css("background-color", "#F4F4F4");
}

$(document).ready(function () {
  var shopLinksDataTable = $("#shopLinksTable").dataTable({
    language: {
      emptyTable: lang('new_item_membership'),
      loadingRecords: lang('loading_datatables'),
    },
    responsive: false,
    ajax: {
      url: "ItemsPostNew.php?data=3",
      type: "POST",
    },
    createdRow: function(row, data, dataIndex) {
      if (!data[1].includes('checked')) {
        $(row).addClass('disabledTrRow');
      }
    },
    processing: true,
    // autoWidth: true,
    scrollY: "90%",
    scrollCollapse: true,
    paging: false,
    bInfo: false,
    bSort: false,
    // fixedHeader: {
    //     headerOffset: 50
    // },
    //bStateSave:true,
    //serverSide: true,
    pageLength: 100,
    dom: "Bfrtip",
    //info: true,
    buttons: [
      {
        extend: "excelHtml5",
        text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
        filename: "קישורים חכמים",
        className: "excel-btn btn",
        exportOptions: { columns: [0, 1, 2, 3] },
      },
    ],
    //"aaSorting": [[0,'asc']]
    //"aaSorting": [],
    initComplete: initCallbackLinks,
  });
  let dtTable = $("#shopLinksTable").DataTable();

  //only used to change color

  //searchLogic
  $("#search-shop3").on("keyup , change", function () {
    dtTable.search($(this).val()).draw();
  });

  //show popup on click
  $("#shopLinksTable").on("click", ".shop-dots-btn", function () {
    let item = $(this).children(".rowBox, .rowBox1");
    item.addClass("rowBox-active");
  });
  //remove popup on hover
  $("#shopLinksTable").on("mouseleave", ".shop-dots-btn", function () {
    let item = $(this).children(".rowBox, .rowBox1");
    item.removeClass("rowBox-active");
  });

  //butons functionality just now only alert
  $("#shopLinksTable").on("click", ".rowBox-item", function () {
    let item = $(this);
    let row_id = item.parents(".shop-dots-btn").attr("data-id");
    console.log(row_id);
    if (item.attr("id") === "rowBoxEdit") {
      openEditRow(row_id);
    } else if (item.attr("id") === "rowBoxPause") {
      pauseStartRow(row_id, true);
    } else if (item.attr("id") === "rowBoxDel") {
      warningConfirm("האם אתה בטוח?, פעולה זו בלתי הפיכה", function () {
        deleteRow(row_id);
      });
    } else if (item.attr("id") === "rowBoxStart") {
      pauseStartRow(row_id, false);
    }
  });

  $("#shopLinksTable").on("change", ".sliderCheckbox", function () {
    let item = $(this);
    let row_id = item.attr("data-id");
    let checked = item.is(":checked");
    pauseStartRow(row_id, !checked);
  });

  function openEditRow(id) {
    showLoader();
    $.ajax({
      url: "/office/newShop/tableAjax/getSingleSmartLink.php",
      type: "post",
      data: { id: id },
      success: function (response) {
        
        hideLoader();
        const itemObj = JSON.parse(response);
        console.log(itemObj);
        $("#select-type-secondary").val(3).trigger("change");
        fillLinkPopupWithData(itemObj);
        openPopup("mainShopPopup");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      },
    });
  }

  function deleteRow(id) {
    showLoader();
    $.ajax({
      url: "/office/newShop/tableAjax/deleteLink.php",
      type: "post",
      data: { id: id },
      success: function (response) {
        dtTable.ajax.reload(initCallbackLinks);
        //hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      },
    });
  }

  function pauseStartRow(id, isPause) {
    showLoader();
    $.ajax({
      url: "/office/newShop/tableAjax/toggleSuspendLink.php",
      type: "post",
      data: { id: id, disabled: isPause == true ? "1" : "0" },
      success: function (response) {
        hideLoader();
        if (isPause) {
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`)
            .find("#rowBoxPause")
            .hide();
          if( $(`#shopLinksTable .sliderCheckbox[data-id="${response}"]`).prop("checked") == true) {
            $(`#shopLinksTable .sliderCheckbox[data-id="${response}"]`).trigger("click");
          }
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`)
            .find("#rowBoxStart")
            .show().addClass("disabledRow");
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`).parents("tr").addClass("disabledTrRow");
        } else {
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`)
            .find("#rowBoxPause")
            .show();
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`).parents("tr").removeClass("disabledTrRow");
          $(`#shopLinksTable .shop-dots-btn[data-id="${response}"]`)
            .find("#rowBoxStart")
            .hide().removeClass("disabledRow");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      },
    });
  }

  function toggleDisplay(id, display) {
    showLoader();
    $.ajax({
      url: "/office/newShop/tableAjax/toggleAppDisplayLink.php",
      type: "post",
      data: { id: id, display: display == true ? "0" : "1" },
      success: function (response) {
        if(display == true){
          $(`#shopLinksTable .shop-dots-btn[data-id="${id}"]`)
              .find("#rowBoxPause")
              .show();
          $(`#shopLinksTable .shop-dots-btn[data-id="${id}"]`)
              .find("#rowBoxStart")
              .hide();
        }
        hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        alert(textStatus);
        console.log(textStatus, errorThrown);
      },
    });
  }
});
