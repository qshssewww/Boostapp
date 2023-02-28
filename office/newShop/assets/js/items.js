function initCallbackItems() {
    let folderRow = $("#shopItemsTable .shopRowSeparator");
    folderRow.parents("tr").css("background-color", "#F4F4F4");

    folderRow.each(function () {
        let folderId = $(this).attr("data-folder-id");
        $(this).parents("tr").attr("data-folder-id", folderId);
        $(this).parents("tr").addClass("folderRow");
    });

    let itemRow = $("#shopItemsTable .shopRowItem");
    //itemRow.parents("tr").css("display", "none");
    itemRow.each(function () {
        let folderId = $(this).attr("data-folder-id");
        let itemId = $(this).attr("data-folder-id");
        $(this).parents("tr").attr("data-folder-id", folderId);
        $(this).parents("tr").attr("data-folder-id", itemId);
        $(this).parents("tr").addClass("itemRow");
    });
    let disabledRow = $(".disabledRow");
    disabledRow.each(function () {
        $(this).parents("tr").addClass("disabledTrRow");
    });
}

$(document).ready(function () {
    var shopItemsDataTable = $("#shopItemsTable").dataTable({
        language: {
            emptyTable: lang('new_item_membership'),
            loadingRecords: lang('loading_datatables'),
        },
        responsive: false,
        ajax: {url: "ItemsPostNew.php?data=2", type: "POST"},
        processing: true,
        // autoWidth: true,
        scrollY: "90%",
        scrollCollapse: true,
        paging: false,
        bInfo: false,
        bSort: false,

        ////  blocked until inventory calc is ready
        // columnDefs: [
        //   { className: "InventoryShow", targets: 1 }
        // ],

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
                filename: "מוצרים",
                className: "excel-btn btn",
                exportOptions: {columns: [0, 1, 2, 3]},
            },
        ],
        //"aaSorting": [[0,'asc']]
        //"aaSorting": [],
        drawCallback: initCallbackItems,
        initComplete: initCallbackItems,
    });
    let dtTable = $("#shopItemsTable").DataTable();

    $("#shopItemsTable").on("click", ".folderRow", function () {
        let icon = $(this).find($(".folderIcon"));
        let folderId = $(this).attr("data-folder-id");
        let items = $("#shopItemsTable").find(
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
    $("#search-shop2").on("keyup , change", function () {
        dtTable.search($(this).val()).draw();
    });

    //show popup on click
    $("#shopItemsTable").on("click", ".shop-dots-btn", function () {
        let item = $(this).children(".rowBox");
        item.addClass("rowBox-active");
    });
    //remove popup on hover
    $("#shopItemsTable").on("mouseleave", ".shop-dots-btn", function () {
        let item = $(this).children(".rowBox");
        item.removeClass("rowBox-active");
    });

    //butons functionality just now only alert
    $("#shopItemsTable").on("click", ".rowBox-item", function () {
        let item = $(this);
        let row_id = item.parents(".shop-dots-btn").attr("data-id");
        console.log(row_id);
        if (item.attr("id") === "rowBoxEdit") {
            openEditRow(row_id);
        } else if (item.attr("id") === "rowBoxPause") {
            pauseStartRow(row_id, true);
        } else if (item.attr("id") === "rowBoxDel") {
            warningConfirm(lang('warning_remove_item'), function () {
                deleteRow(row_id);
            });
        } else if (item.attr("id") === "rowBoxStart") {
            pauseStartRow(row_id, false);
        }
    });

    $("#shopItemsTable").on("change", ".sliderCheckbox", function () {
        let item = $(this);
        let row_id = item.attr("data-id");
        let checked = item.is(":checked");
        toggleDisplay(row_id, checked);
    });

    function openEditRow(id) {
        showLoader();
        $.ajax({
            url: "/office/newShop/ajax.php",
            type: "post",
            data: {
                "id": id,
                "fun": "getSingleItem"
            },
            success: function (response) {
                hideLoader();
                const itemObj = JSON.parse(response);
                console.log(itemObj);
                $("#select-type-secondary").val(2).trigger("change");
                fillProductPopupWithData(itemObj);
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
            url: "/office/newShop/tableAjax/deleteMembershipOrItem.php",
            type: "post",
            data: {id: id},
            success: function (response) {
                hideLoader();
                dtTable.ajax.reload(initCallbackItems);
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
            data: {id: id, disabled: isPause == true ? "1" : "0"},
            success: function (response) {
                hideLoader();
                dtTable.ajax.reload(initCallbackItems);

                // if (isPause) { //
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`)
                //             .find("#rowBoxPause")
                //             .hide();
                //     if ($(`#shopItemsTable .sliderCheckbox[data-id="${response}"]`).prop("checked") == true) {
                //         $(`#shopItemsTable .sliderCheckbox[data-id="${response}"]`).trigger("click");
                //     }
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`)
                //             .find("#rowBoxStart")
                //             .show().addClass("disabledRow");
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`).parents("tr").addClass("disabledTrRow");
                // } else {
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`).parents("tr").removeClass("disabledTrRow");
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`)
                //             .find("#rowBoxPause")
                //             .show();
                //     $(`#shopItemsTable .shop-dots-btn[data-id="${response}"]`)
                //             .find("#rowBoxStart")
                //             .hide().removeClass("disabledRow");
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
            data: {id: id, display: display == true ? "1" : "0"},
            success: function (response) {
                if (display == true) {
                    $(`#shopItemsTable .shop-dots-btn[data-id="${id}"]`)
                            .find("#rowBoxPause")
                            .show();
                    $(`#shopItemsTable .shop-dots-btn[data-id="${id}"]`)
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
function reloadAfterInsert(tableId, callback) {
    let dtTable = $(tableId).DataTable();
    dtTable.ajax.reload(callback);
}

// $(document).ajaxSuccess(function (event, jqxhr, settings) {
//   // const data = settings.data.split('&');
//   console.log(settings);
// });
