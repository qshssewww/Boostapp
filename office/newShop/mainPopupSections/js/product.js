
var DelInventory = [];
$(document).ready(function () {
    $(".AddInventory").click(function () {
        addInventory()
    })
});
function addInventory() {
    let options = "";
    let colorsOpt = "";
    sizeArr.forEach(function (size) {
        options += "<option value='" + size.id + "'>" + size.name + "</option>";
    });
    colorArr.forEach(function (color) {
        border = color.id == 2 ? 'border' : '';
        colorsOpt += "<div id='" + color.id + "' class='colorCube  "+border+" ' style='background-color:" + color.hex + "'></div>";
    });
    let headerLbl = $('#InventoryList').children().length > 0 ? "" : "<label for='productPrice'>כמות</label>";
    $("#InventoryList").append("<div class='d-flex w-100 Inventory  align-items-end' data-id='0'>"
            + "<div class='col-md-4 px-5 mb-10'>"
            + headerLbl
            + "<div class='d-flex align-items-center'><div class='rowIconContainer'>"
            + '<i class="fal fa-layer-group"></i>'
            + "</div><div>"

            + "<input placeholder='כמות' type='number' name='productPrice' min='0' class='InventoryAmount productPrice form-control bg-light border-light' style='width: 100%' />"

            + "</div></div></div>"
            + "<div class='col-md-4 px-5 mb-10' >"
            + "<div class=''>"
            + "<select class='SizeInventory size js-select2-shop'>"
            + options
            + "</select>"
            + "</div>"
            + "</div>"
            + "<div class='col-md-4 px-5 mb-10 d-flex align-items-center justify-content-between'>"
            // + "<div class='rowIconContainer'>"
            // + "  <span class='newLabel' style='margin-left: 300px;'>חדש</span>"
            // + "</div>"

            + "<div class='' >"
            + "<div class='colorGridSelect'>"
            + "<div class='colorCube selectedColor' data-id='0'></div>"
            + "<span class='downArrow'>"
            + "<i class='fas fa-sort-down'></i>"
            + "</span>"
            + "<input type='hidden' id='selectedColor''>"
            + "<div class='colorGridContainer'>"
            + "<div class='colorGrid'>"
            + "<div data-id='0' class='colorCube'></div>"
            + colorsOpt
            + "</div>"
            + "</div>"
            + "</div>"
            + "</div>"
            // + "<div style='align-self: center;'>"
            // + "<div class='stop mb-2 mr-2 close-inventory'></div>"
            + '<div class="text-danger mis-9 close-inventory cursor-pointer"><i class="fas fa-do-not-enter"></i></div>'
            // + "</div>"
            + "</div>"

            + "</div>"
            );
    $("#InventoryList .Inventory:last-of-type .size").select2({
        tags: true,
        placeholder: "מידה",
        width: "100%",
        allowClear: true,
        theme: "bsapp-dropdown",
        createTag: function (tag) {
            return {
                id: `#${tag.term}`,
                text: tag.term,
                isNew: true,
            };
        },
    }).on('select2:select', function (e) {
        if (e.params.data.isNew) {
            $(e.target)
                    .parents(".Inventory")
                    .find(".newLabel")
                    .addClass("labelDisplayOn");
        } else {
            $(e.target)
                    .parents(".Inventory")
                    .find(".newLabel")
                    .removeClass("labelDisplayOn");
        }
    }).val(null).trigger('change');
}

function addInventoryTableRow() {
    let options = "";
    let colorsOpt = "";
    sizeArr.forEach(function (size) {
        options += "<option value='" + size.id + "'>" + size.name + "</option>";
    });
    colorArr.forEach(function (color) {
        colorsOpt += "<div id='" + color.id + "' class='colorCube' style='background-color:" + color.hex + "'></div>";
    });
    if ($("#InventoryTable tbody").find("#emptyDataInventory").length > 0) {
        $("#InventoryTable tbody").children().remove();
    }
    $("#InventoryTable tbody").append("<tr data-id='0'>" +
            " <td class=''>" +
            "    <div>" +
            "        <input class='InventoryAmount form-control bg-light border-light productPrice mie-10' placeholder='כמות'  type='number' min='0' name='productPrice' >" +
            "    </div>" +
            " </td>" +
            " <td>" +
            "<div class=''>"
            + "<div>"
            + "<select class='SizeInventory size js-select2-shop'>"
            + options
            + "</select>"
            + "</div>"
            + "<div class='rowIconContainer'> "
            + " <span class='newLabel' style='margin-left: 20px;'>חדש</span>"
            + "</div>"
            + "</div>"
            + " </td>" +
            " <td class=''>"

            + "<div class='rowInput'>"
            + "<div class='colorGridSelect'>"
            + "<div class='colorCube selectedColor' data-id='0'></div>"
            + "<span class='downArrow'>"
            + "<i class='fas fa-sort-down'></i>"
            + "</span>"
            + "<input type='hidden' id='selectedColor'/>"
            + "<div class='colorGridContainer'>"
            + "<div class='colorGrid'>"
            + "<div data-id='0' class='colorCube'></div>"
            + colorsOpt
            + "</div>"
            + "</div>"
            + "</div>"
            + "</div>"
            + " </td>" +
            " <td class=''>" +
            // "     <div style='align-self: center;'><div class='stop mb-2 mr-2 close-inventory'></div></div>" +
            '<div class="text-danger mis-9 close-inventory cursor-pointer"><i class="fas fa-do-not-enter"></i></div>' +
            " </td>" +
            "</tr>");
    $("#InventoryTable tr:last-of-type .size").select2({
        tags: true,

        placeholder: "מידה",
        width: "100%",
        allowClear: true,
        theme: "bsapp-dropdown",
        createTag: function (tag) {
            return {
                id: `#${tag.term}`,
                text: tag.term,
                isNew: true
            };
        },
    }).on('select2:select', function (e) {
        if (e.params.data.isNew) {
            $(e.target)
                    .parents("tr")
                    .find(".newLabel")
                    .addClass("labelDisplayOn");
        } else {
            $(e.target)
                    .parents("tr")
                    .find(".newLabel")
                    .removeClass("labelDisplayOn");
        }
    }).val(null).trigger('change');
}
function emptyProductPopup() {
    $("#hiddenIdInput2").val("");
    $("#productName").val("");
    $("#productCategory")
            .val($("#productCategory option:first").val())
            .trigger("change");
    $('#productSuppliers').val(null).trigger('change');
    $('#clear-supplier').parent().hide();
    $("#productBranch").val("-1").trigger("change");
    $("#productPrice").val("").trigger("keyup");
    $("#costPrice").val("");
    $("#productInApp").val(1).trigger("change");
    $("#productContent").summernote('code', '');
    $("#purchaseLimitPopupProductHiddenClose").click();
    $("#stock").val(null);
    $("#supplier").val(null);
    $("#makat").val(null);
    $("#barcode").val(null);
    $("#size").val([]).trigger("change");
    $("#color").val([]).trigger("change");
    $("#openMoreOptions").text("הצג אפשרויות נוספות").removeClass('isOpen');
    $(".extraOption").fadeOut();
    $("#openProductTextarea .plus").removeClass("hidden").addClass("visible");
    $(".hiddenTextarea-product").removeClass("visible").addClass("hidden");
    $(".newLabel").removeClass("labelDisplayOn");
    $("#isProductCategoryNew").val("0");
    $("#InventoryList").children().remove();
    //img
    $("#imgPlus2").html(
            `<div class="rowIconContainer"><i class="far fa-image"></i></div><div class="plus ImgEmpty">  +  תמונה</div><div class="hidden hiddenImg d-flex align-items-center"><div class="ImgName" id="ImgName2"></div></div> `
            );
    let imgPath = $("#pageImgPath2");
    imgPath.val("");
    // imgPath.prev().show();
}

function fillProductPopupWithData(data) {
    emptyProductPopup();
    setUpdateText()
    $("#hiddenIdInput2").val(data.item.id);
    $("#productName").val(data.item.ItemName);
    $("#productCategory").val(data.item.ItemCat).trigger("change");
    $("#productBranch")
            .val(data.item.Brands == "BA999" ? "-1" : data.item.Brands)
            .trigger("change");
    $("#productPrice").val(parseInt(data.item.ItemPrice)).trigger("keyup");
    $("#costPrice").val(parseInt(data.item.CostPrice));
    $('#productInApp').val(data.item.Display == 0 ? 1 : 2).trigger('change');
    if (data.item.Content) {
        $("#productContent").summernote('code', data.item.Content);
        $('#openProductTextarea .plus').removeClass('visible').addClass('hidden');
        $('.hiddenTextarea-product').removeClass('hidden').addClass('visible');
    }

    $("#openMoreOptions").text("הסתר אפשרויות נוספות").addClass('isOpen');
    $(".extraOption").fadeIn();
    $("#stock").val(data.item.inventory);
    $("#makat").val(data.item.sku);
    $("#barcode").val(data.item.barcode);
    if (data.item.colors) {
        $("#color").val(JSON.parse(data.item.colors)).trigger('change');
    }
    if (data.item.sizes) {
        $("#size").val(JSON.parse(data.item.sizes)).trigger('change');
    }
    if (data.item.Image) {
        $("#pageImgPath2").val(data.item.Image)
        let img = $("#pageImgPath2").val();
        $("#ImgName2").append('<img class="shopImage" id="shopImage2" src="' + img + '"/>')
        showHidden($("#imgPlus2"), ".hiddenImg");
        $("#pageImgPath2").prev().show();
    }
    if (data.limit) {
        onSystematicPurchaseLimitAdd(data.limit)
    }
    if (data.details) {

        data.details.forEach(function (item, index) {
            addInventory()
            var elm = $('#InventoryList').children().last()
            addInventoryList(item, elm);
            $("#makat").val(item.sku);
            $("#barcode").val(item.barcode)
            if (index == 0) {
                $("#productSuppliers").val(item.suppliers.id).trigger('change');
                if (item.suppliers.id) {
                    $('#clear-supplier').parent().show();
                }
            }

        })
    }
}

function addInventoryList(data, elm) {
    if(data.colors) {
        $(elm).find('.selectedColor').css("background-color", data.colors.hex || 'white').attr("data-id", data.colors.id);
    }
    $(elm).attr("data-id", data.id);
    $(elm).find(".productPrice").val(data.inventory - data.used);
    // $(elm).find(".productPrice").val(data.inventory);
    if(data.sizes) {
        $(elm).find(".SizeInventory").val(data.sizes.id).trigger('change');
    }
}
$(document).ready(function () {
    $(".AddInventoryTable").click(function () {
        addInventoryTableRow()
    })
    $("#productContent").summernote({
        height: 120,
        width: "100%",
        followToolbar: false,
        dialogsInBody: true,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['ul', 'ol']]
        ],
        callbacks: {
            onPaste: function (e) {
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                document.execCommand('insertText', false, bufferText);
            }
        }
    })
    $("#productInApp").on("change", function () {
        if ($(this).val() == 1) {
            $(".inAppOptions").fadeOut();
        } else {
            $(".inAppOptions").fadeIn();
        }
    });
    $("#openMoreOptions").on("click", function () {
        if ($(this).hasClass("isOpen")) {
            $(this).removeClass("isOpen");
            $(this).text("הצג אפשרויות נוספות");
            $(".extraOption").fadeOut();
        } else {
            $(this).addClass("isOpen");
            $(this).text("הסתר אפשרויות נוספות");
            $(".extraOption").fadeIn();
        }
    });
    $("#productCategory")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true,
                    };
                },
                theme: "bsapp-dropdown"
            })
            .on("select2:select", function (e) {
                if (e.params.data.isNew) {
                    $("#productCategory")
                            .parents(".js-category-containers")
                            .find(".newLabel")
                            .addClass("labelDisplayOn");
                    $("#isProductCategoryNew").val("1");
                } else {
                    $("#productCategory")
                            .parents(".js-category-containers")
                            .find(".newLabel")
                            .removeClass("labelDisplayOn");
                    $("#isProductCategoryNew").val("0");
                }
            });
    $("#productSuppliers")
            .select2({
                tags: true,
                placeholder: 'בחר',
                allowClear: true,
                theme: 'bsapp-dropdown',
                createTag: function (tag) {
                    return {
                        id: `#${tag.term}`,
                        text: tag.term,
                        isNew: true
                    };
                },
            })
            .on("select2:select", function (e) {
                $('#clear-supplier').parent().show();
                if (e.params.data.isNew) {
                    $("#productSuppliers")
                            .parents(".js-supplier-category")
                            .find(".newLabel")
                            .addClass("labelDisplayOn");
                    $("#isProductSupplierNew").val("1");
                } else {
                    $("#productSuppliers")
                            .parents(".js-supplier-category")
                            .find(".newLabel")
                            .removeClass("labelDisplayOn");
                    $("#isProductSupplierNew").val("0");
                }
            });
    $("#color").select2({
        tags: true,
        placeholder: "בחר צבע",
        theme: "bsapp-dropdown",
        createTag: function (tag) {
            return {
                id: `#${tag.term}`,
                text: tag.term,
                isNew: true,
            };
        },
    });
    $('body').on('click', '.close-inventory', function (e) {
        DelInventory.push($(this).parents('.Inventory').attr("data-id"))
        $(this).parents('.Inventory').remove();
    });
    function fillProductInventoryData(data) {
        data.forEach(function (item, index) {
            addInventoryTableRow()
            var elm = $('#InventoryTable tbody').children().last()
            addInventoryTable(item, elm);
        })

    }

    function InventoryDataEmpty() {
        $("#InventoryTable tbody").append("<tr id='emptyDataInventory'>"
                + "<td class='text-danger'>אין</td>"
                + "<td class='text-danger'>מידע</td>"
                + "<td class='text-danger'>להצגה</td>"
                + "</tr>");
    }

    function addInventoryTable(data, elm) {
        if(data.colors) {
            $(elm).find('.selectedColor').css("background-color", data.colors.hex || 'white').attr("data-id", data.colors.id);
        }
        $(elm).attr("data-id", data.id);
        $(elm).find(".InventoryAmount").val(data.inventory - data.used);
        // $(elm).find(".InventoryAmount").val(data.inventory);
        if(data.sizes) {
            $(elm).find(".SizeInventory").val(data.sizes.id).trigger('change');
        }
    }
    $('body').on('click', '#InventoryTable .close-inventory', function (e) {
        DelInventoryTable.push($(this).parents('tr').attr("data-id"))
        $(this).parents('tr').remove();
        if($('#InventoryTable tbody').children().length <= 0) {
            InventoryDataEmpty();
        } 
    });
    $(document).on("click", ".InventoryShow", function () {
        showLoader();
        if ($(this).find("[data-id]").attr("data-id")) {

            $.ajax({
                url: "/office/newShop/ajax.php",
                type: "post",
                data: {
                    "id": $(this).find("[data-id]").attr("data-id"),
                    "fun": "getSingleItem"
                },
                success: function (response) {
                    hideLoader();
                    const itemObj = JSON.parse(response);
                    console.log(itemObj);
                    openPopup("InventoryPopup");
                    $('#InventoryPopup').attr('data-id', itemObj.item.id);
                    if(itemObj.details.length > 0) {
                        fillProductInventoryData(itemObj.details);
                    } else {
                        InventoryDataEmpty();
                    }
                    
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    hideLoader();
                    alert(textStatus);
                    console.log(textStatus, errorThrown);
                },
            });
        } else {
            hideLoader();
            InventoryDataEmpty();
            openPopup("InventoryPopup");
        }
    });
    $(document).on("click", ".editInventoryItem", function () {
        showLoader();
        if ($(this).closest("[data-id]").attr("data-id")) {

            $.ajax({
                url: "/office/newShop/ajax.php",
                type: "post",
                data: {
                    "id": $(this).closest("[data-id]").attr("data-id"),
                    "fun": "getSingleItem"
                },
                success: function (response) {

                    hideLoader();
                    const itemObj = JSON.parse(response);
                    console.log(itemObj);
                    openPopup("InventoryPopup");
                    $('#InventoryPopup').attr('data-id', itemObj.item.id);
                    if(itemObj.details.length > 0) {
                        fillProductInventoryData(itemObj.details);
                    } else {
                        InventoryDataEmpty();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    hideLoader();
                    alert(textStatus);
                    console.log(textStatus, errorThrown);
                },
            });
        } else {
            hideLoader();
            InventoryDataEmpty();
            openPopup("InventoryPopup");
        }
    })
    $("#productPrice").on("keyup", function () {
        if ($(this).val().length > 0) {
            $(".shouldBeHidden").fadeIn();
        } else {
            $(".shouldBeHidden").fadeOut();
        }
    });
    $("#openProductTextarea").on("click", function (e) {
        showHidden($(this), ".hiddenTextarea-product");
    });
    $("#closeProductTextarea").on("click", function (e) {
        hideHidden($(this), ".hiddenTextarea-product");
    });
});
