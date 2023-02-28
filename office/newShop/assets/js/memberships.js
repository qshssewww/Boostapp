function initCallbackMembership() {
  let folderRow = $("#shopMembershipsTable .shopRowSeparator");
  folderRow.parents("tr").css("background-color", "#F4F4F4");

  folderRow.each(function () {
    let folderId = $(this).attr("data-folder-id");
    $(this).parents("tr").attr("data-folder-id", folderId);
    $(this).parents("tr").addClass("folderRow");
  });
  let itemRow = $("#shopMembershipsTable .shopRowItem");
  //itemRow.parents("tr").css("display", "none");
  itemRow.each(function () {
    let folderId = $(this).attr("data-folder-id");
    let itemId = $(this).attr("data-folder-id");
    $(this).parents("tr").attr("data-folder-id", folderId);
    $(this).parents("tr").attr("data-folder-id", itemId);
    $(this).parents("tr").addClass("itemRow");
  });

  let disabledRow = $(".disabledRow");
  disabledRow.each(function(){
    $(this).parents("tr").addClass("disabledTrRow");
  });

  hideLoader();
}
console.log(BeePOS.options.datatables);
$(document).ready(function () {
  console.log($('.hidden-option'));
  var shopMembershipsDataTable = $("#shopMembershipsTable").dataTable({
    language: {
      emptyTable: lang('new_item_membership'),
      loadingRecords: lang('loading_datatables'),
    },
    columnDefs: [
      { visible:  !$('.hidden-option').attr('hidden'), targets: 1}
    ],
    responsive: false,
    ajax: { url: "ItemsPostNew.php?data=1", type: "POST" },
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
        filename: lang('subscriptions'),
        className: "excel-btn btn",
        exportOptions: { columns: [0, 1, 2, 3] },
      },
    ],
    //"aaSorting": [[0,'asc']]
    //"aaSorting": [],
    initComplete: initCallbackMembership,
    drawCallback: initCallbackMembership
  });
  let dtTable = $("#shopMembershipsTable").DataTable();

  //only used to change color

  $("#shopMembershipsTable").on("click", ".folderRow", function () {
    let icon = $(this).find($(".folderIcon"));
    let folderId = $(this).attr("data-folder-id");
    let items = $("#shopMembershipsTable").find(
      ".itemRow[data-folder-id='" + folderId + "']"
    );
    if (icon.hasClass("fa-chevron-up")) {
      $(this).addClass("folderRowSelected");
      icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
      items.each(function () {
        $(this).show();
      });
    } else if (icon.hasClass("fa-chevron-down")) {
      $(this).removeClass("folderRowSelected");
      icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
      items.each(function () {
        $(this).hide();
      });
    }
  });

  //searchLogic
  $("#search-shop1").on("keyup , change", function () {
    dtTable.search($(this).val()).draw();
  });

  //show popup on click
  $("#shopMembershipsTable").on("click", ".shop-dots-btn", function () {
    let item = $(this).children(".rowBox");
    item.addClass("rowBox-active");
  });
  //remove popup on hover
  $("#shopMembershipsTable").on("mouseleave", ".shop-dots-btn", function () {
    let item = $(this).children(".rowBox");
    item.removeClass("rowBox-active");
  });

  //butons functionality just now only alert
  $("#shopMembershipsTable").on("click", ".rowBox-item", function () {
    let item = $(this);
    let row_id = item.parents(".shop-dots-btn").attr("data-id");
    console.log(row_id);
    if (item.attr("id") === "rowBoxEdit") {
      openEditRow(row_id);
    } else if (item.attr("id") === "rowBoxPause") {
      pauseStartRow(row_id, true);
    } else if (item.attr("id") === "rowBoxDel") {
      warningConfirm(lang('undone_action_membership'), function () {
        deleteRow(row_id);
      });
    } else if (item.attr("id") === "rowBoxStart") {
      pauseStartRow(row_id, false);
    }
  });

  $("#shopMembershipsTable").on("change", ".sliderCheckbox", function () {
    let item = $(this);
    let row_id = item.attr("data-id");
    let checked = item.is(":checked");
    toggleDisplay(row_id, checked);
  });

  function openEditRow(id) {
    showLoader();
    $.ajax({
      url: "/office/newShop/tableAjax/getSingleMembership.php",
      type: "post",
      data: { id: id },
      success: function (response) {
        hideLoader();
        const itemObj = JSON.parse(response);
        console.log(itemObj);
        let typeForMembership;
        if (itemObj.Department == 1) {
          typeForMembership = 1;
        } else if (itemObj.Department == 2) {
          typeForMembership = 4;
        } else if (itemObj.Department == 3) {
          typeForMembership = 5;
        }
        $("#select-type-secondary").val(typeForMembership).trigger("change");
        fillMembershipPopupWithData(itemObj);
        openPopup("mainShopPopup");
        if ($("#purchaseLimitPopupMembershipHiddenText" + typeForMembership).text() != "") {
          $("#purchaseLimitPopupMembershipHidden" + typeForMembership).show();
          $("#purchaseLimitPopupMembership" + typeForMembership).hide();
        }
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
      url: "/office/newShop/tableAjax/deleteMembershipOrItem.php",
      type: "post",
      data: { id: id },
      success: function (response) {
        hideLoader();
        dtTable.ajax.reload(initCallbackMembership);
        reloadAfterInsert('#shopLinksTable', initCallbackLinks);
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
      url: "/office/newShop/tableAjax/toggleSuspendMembershipOrItem.php",
      type: "post",
      data: { id: id, disabled: isPause == true ? "1" : "0" },
      success: function (response) {
        hideLoader();
        dtTable.ajax.reload(initCallbackMembership);

        // if (isPause) {
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`)
        //     .find("#rowBoxPause")
        //     .hide();
        //   if( $(`#shopMembershipsTable .sliderCheckbox[data-id="${response}"]`).prop("checked") == true) {
        //     $(`#shopMembershipsTable .sliderCheckbox[data-id="${response}"]`).trigger("click");
        //   }
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`)
        //     .find("#rowBoxStart")
        //     .show().addClass("disabledRow");
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`).parents("tr").addClass("disabledTrRow");
        // } else {
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`)
        //     .find("#rowBoxPause")
        //     .show();
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`).parents("tr").removeClass("disabledTrRow");
        //   $(`#shopMembershipsTable .shop-dots-btn[data-id="${response}"]`)
        //     .find("#rowBoxStart").removeClass("disabledRow")
        //     .hide();
        // }
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
      url: "/office/newShop/tableAjax/toggleAppDisplayMembershipOrItem.php",
      type: "post",
      data: { id: id, display: display == true ? "1" : "0" },
      success: function (response) {
        if(display == true){
          $(`#shopMembershipsTable .shop-dots-btn[data-id="${id}"]`)
              .find("#rowBoxPause")
              .show();
          $(`#shopMembershipsTable .shop-dots-btn[data-id="${id}"]`)
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

const showMembershipType = (show) => {
  if (show) {
    $('.membershipField').show();
    $('.hidden-option').removeAttr('hidden');
    $('.tableNumber1').find('table').DataTable().column(1).visible(true);
  } else {
    $('.membershipField').hide();
    $('.hidden-option').attr('hidden', 'hidden');
    $('.tableNumber1').find('table').DataTable().column(1).visible(false);
  }
}

$(document).on('change', '#manage-memberships-switch', function () {
  showMembershipType($('#manage-memberships-switch').is(':checked'));
});

$(document).ajaxSend(function (event, xhr, settings) {
  if (settings.data) {
    const data = decodeUri(settings.data);
    if (data.fun == 'toggleManageMemberships'
      || data.fun == 'disableMembershipType'
      || data.fun == 'disableCategory'
      || data.fun == 'renameProductCategory'
      || data.fun == 'deleteOrMoveCategory'
      || data.fun == 'reorderItems'
      || data.fun == 'reorderCategories') {
      showLoader();
    }
  }
})

function decodeUri (search) {
  return JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"').replace('+', ' ') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) });
}

$(document).ajaxSuccess(function (event, xhr, settings) {
  if (settings.data) {
    const data = decodeUri(settings.data);
    if (data.fun == 'toggleManageMemberships'
      || data.fun == 'disableMembershipType'
      || data.fun == 'deleteOrMoveMembershipType') {
      $('.tableNumber1').find('table').DataTable().ajax.reload();
      if (data.fun == 'deleteOrMoveMembershipType') {
        $('select[name=location]').find(`option[value=${data.id}]`).remove();
        if ($('#membershipType1').find('option').length <= 1) {
          showMembershipType(false);
        }
      }
    } else if (data.fun == 'disableCategory'
      || data.fun == 'renameProductCategory'
      || data.fun == 'deleteOrMoveCategory'
      || data.fun == 'reorderCategories') {
      $('.tableNumber2').find('table').DataTable().ajax.reload();
      if (data.fun == 'deleteOrMoveCategory') {
        $('select[name=productCategory]').find(`option[value=${data.id}]`).remove();
        getItems("categories");
      }
    } else if (data.fun == 'insertNewMembershipType') {
      if ($('#membershipField').is(':hidden')) {
        showMembershipType(true);
      }
      $('select[name=location]').append(`<option value="${xhr.responseJSON}">${data.name}</option>`);
    } else if (data.fun == 'insertNewCategory') {
      $('select[name=productCategory]').append(`<option value="${xhr.responseJSON}">${data.name}</option>`);
    } else if (data.fun == 'reorderItems') {
      $('.tableNumber1').find('table').DataTable().ajax.reload();
      $('.tableNumber2').find('table').DataTable().ajax.reload();
    } else {
      hideLoader();
    }
  } else {
    hideLoader();
  }
});

$(document).ajaxError(function () {
  hideLoader();
})
function reloadAfterInsert(tableId, callback) {
  let dtTable = $(tableId).DataTable();
  dtTable.ajax.reload(callback);
}
