$(document).ready(function () {
  $.fn.dataTable.moment = function (format, locale) {
    var types = $.fn.dataTable.ext.type;

    // Add type detection
    types.detect.unshift(function (d) {
      return moment(d, format, locale, true).isValid()
        ? "moment-" + format
        : null;
    });

    // Add sorting method - use an integer for the sorting
    types.order["moment-" + format + "-pre"] = function (d) {
      return moment(d, format, locale, true).unix();
    };
  };
  $.fn.dataTable.moment("d/m/Y H:i");
  BeePOS.options.datatables = JSON.stringify("datatables");

  //foldersEdit table
  var foldersEdit = $("#foldersEdit").DataTable({
    "language": {
      "emptyTable": lang('no_videos_vod_library')
    },
    responsive: true,
    ajax: { url: "FolderPost.php", type: "POST" },
    processing: true,
    rowReorder: {
      selector: ".LibraryFolderRow td:first-child ",
      dataSrc: 0,
      update: false,
    },
    columns: [
      { data: "col1" },
      { data: "col2" },
      { data: "col3" },
      { data: "col4" },
      { data: "col5" },
    ],
    searching: false,
    // autoWidth: true,
    scrollY: "450px",
    scrollCollapse: true,
    paging: false,
    bInfo: false,
    // fixedHeader: {
    //     headerOffset: 50
    // },
    //bStateSave:true,
    //serverSide: true,
    bSort: false,
    pageLength: 100,
    dom: "Bfrtip",
    //info: true,
    buttons: [],
    initComplete: initCallback,
    //"aaSorting": [[0,'asc']]
    //"aaSorting": []
  });

  function initCallback() {
    let folderRow = $("#foldersEdit .folderRow");
    folderRow.addClass("deleteInput");
    folderRow.each(function () {
      let folderId = $(this).attr("data-value");
      $(this).parents("tr").attr("data-value", folderId);
      $(this).parents("tr").addClass("LibraryFolderRow");
    });
    addJqueryListeners();
    setMultipleChildRows();
  }

  //reorder callback for db
  foldersEdit.on("row-reorder", function (e, diff, edit) {
    try {
      const changes = [];
      for (let i = 0; i < diff.length; i++) {
        const id = $(diff[i].node).attr("data-value");
        const position = diff[i].newPosition;
        changes.push({ id: parseInt(id), position: parseInt(position) });
      }
      FolderPositionUpdate(changes);
    } catch (err) {
      console.log(err);
      foldersEdit.ajax.reload(initCallback);
    }
    setMultipleChildRows();
  });

  foldersEdit.on("pre-row-reorder", function (e, node, index) {
    deleteAllChildRows();
  });

  function FolderDisplayUpdate(id, display = 0) {
    $.ajax({
      url: "FolderDisplayUpdate.php",
      type: "post",
      data: JSON.stringify({ id: id, display: display }),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        throw errorThrown;
      },
    });
  }
  function FolderPositionUpdate(changes) {
    $.ajax({
      url: "FolderPositionUpdate.php",
      type: "post",
      data: JSON.stringify(changes),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        throw errorThrown;
      },
    });
  }

  function FolderUpdateLimitsAndName(data) {
    $("#foldersEdit .folderRow").addClass("deleteInput");
    $.ajax({
      url: "FolderUpdateLimitsAndName.php",
      type: "post",
      data: JSON.stringify(data),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        throw errorThrown;
      },
    });
  }
  function FolderAddNew(data) {
    $.ajax({
      url: "FolderAddNew.php",
      type: "post",
      data: JSON.stringify(data),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        console.log(response);
        foldersEdit.ajax.reload(initCallback);
        hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        throw errorThrown;
        hideLoader();
      },
    });
  }
  function FolderDelete(id) {
    $.ajax({
      url: "FolderDelete.php",
      type: "post",
      data: JSON.stringify({ id: id }),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        throw errorThrown;
      },
    });
  }
  function setSingleChildRow(tr) {
    let checkbox = tr.find(".showForAll");
    var row = foldersEdit.row(tr);
    if (checkbox.prop("checked") == false) {
      row.child(formatChildRow(row.data())).show();
      $("#foldersEdit tr")
        .not(".LibraryFolderRow")
        .find("td")
        .first()
        .css("border", "none");
      InitSelect2(tr.next("tr"));
      tr.addClass("shown");
      tr.next().find(".select2-search__field").focus();
    } else {
      row.child.hide();
      tr.removeClass("shown");
    }
    $("#foldersEdit tr")
      .not(".LibraryFolderRow")
      .find("td")
      .css("border", "none");
  }
  function setMultipleChildRows() {
    $("#foldersEdit tr").each(function () {
      let checkbox = $(this).find(".showForAll");
      var row = foldersEdit.row($(this));
      if (checkbox.prop("checked") == false) {
        row.child(formatChildRow(row.data())).show();
        $("#foldersEdit tr")
          .not(".LibraryFolderRow")
          .find("td")
          .css("border", "none");
        InitSelect2Multi();
        $(this).addClass("shown");
      } else {
        row.child.hide();
        $(this).removeClass("shown");
      }
    });
  }
  function deleteAllChildRows() {
    $("#foldersEdit tr").each(function () {
      var row = foldersEdit.row($(this));
      if ($(this).hasClass("shown")) {
        row.child.hide();
        $(this).removeClass("shown");
      }
    });
  }
  function formatChildRow(d) {
    return (
      '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;width:100%;">' +
      "<tr class='selectContainer'>" +
      "<td style='width:2%'> " +
      "</td>" +
      "<td style='width:29%'> " +
      ` <select class="folderEditSingleLimits" name="singleLimits" style="width:95%">
      <option value="1">`+ lang('vod_for_membership')+`</option>
        </select>` +
      "</td>" +
      "<td style='width:69%'>" +
      `<select class="folderEditMultipleLimits js-example-placeholder-multiple js-states form-control" data-placeholder="`+lang('select_membership')+`"  values="${
        d["videoLimits"] ? d["videoLimits"]["membership"] : null
      }" name="multLimits[]" multiple="multiple" style="width:95%">
      ${d["membershipTypes"].map(function (item) {
        return `<option value="${item.id}">${item.Type}</option>`;
      })}
       </select>` +
      "</td>" +
      "</table>"
    );
  }
  function InitSelect2(jQueryObj) {
    //onChangeSave
    jQueryObj
      .find(".folderEditMultipleLimits")
      .select2({
          minimumResultsForSearch: -1,
          placeholder: function(){
            $(this).data('placeholder');
          }
        }
      )
      .on("select2:open", function (e) {
        const evt = "scroll.select2";
        $(".dataTables_scrollBody").off(evt);
      });
    jQueryObj
      .find(".folderEditSingleLimits")
      .select2()
      .on("select2:open", function (e) {
        const evt = "scroll.select2";
        $(".dataTables_scrollBody").off(evt);
      });
    const Values = jQueryObj.find(".folderEditMultipleLimits").attr("values");
    if (Values) {
      jQueryObj.find(".folderEditMultipleLimits").val(Values.split(","));
      jQueryObj.find(".folderEditMultipleLimits").trigger("change");
    }
    jQueryObj.find(".folderEditMultipleLimits").change(function () {
      let _this = $(this);
      console.log('change')
      const id = _this.parents("tr").prev("tr").attr("data-value");
      const name = _this.parents("tr").prev("tr").find(".folderRow").val();
      let singleSelect = null;
      let multSelect = null;
      const showForAll = _this
        .parents("tr")
        .prev("tr")
        .find(".showForAll")
        .prop("checked");
      if (!showForAll) {
        singleSelect = _this
          .parents("tr")
          .find(".folderEditSingleLimits")
          .val();
        multSelect = _this
          .parents("tr")
          .find(".folderEditMultipleLimits")
          .val();
      }
        FolderUpdateLimitsAndName({
        name: name,
        id: id,
        showForAll: showForAll,
        membership: multSelect,
      });
    });
  }

  function InitSelect2Multi() {
    $('.folderEditMultipleLimits').off('change');
    $("#foldersEdit .selectContainer").each(function () {
      InitSelect2($(this));
    });
  }
  function addEmptyRow() {
    showLoader();
    FolderAddNew({
      name: "תיקייה חדשה",
      showForAll: true,
      membership: null,
      display: 1,
    });
  }
  $(".add-folder").on("click", addEmptyRow);

  function addJqueryListeners() {
    //onChangeSave

    $("#foldersEdit input")
      .not(".toggleDisplay")
      .change(function () {
        let _this=$(this);
        const id = _this.parents("tr").attr("data-value");
        const name = _this.parents("tr").find(".folderRow").val();
        let singleSelect = null;
        let multSelect = null;
        const showForAll = _this
          .parents("tr")
          .find(".showForAll")
          .prop("checked");
        if (!showForAll) {
          singleSelect = _this
            .parents("tr")
            .next()
            .find(".folderEditSingleLimits")
            .val();
          multSelect = _this
            .parents("tr")
            .next()
            .find(".folderEditMultipleLimits")
            .val();
        }
        FolderUpdateLimitsAndName({
          name: name,
          id: id,
          showForAll: showForAll,
          membership: multSelect,
        });
      });

    $("#foldersEdit .toggleDisplay").change(function () {
      const id = $(this).parents("tr").attr("data-value");
      const display = $(this).prop("checked");
      FolderDisplayUpdate(id, display ? 1 : 0);
    });
    $("#foldersEdit .showForAll").change(function () {
      const tr = $(this).parents("tr");
      setSingleChildRow(tr);
    });
    $("#foldersEdit .editName").click(function () {
      let input = $(this).parents("tr").find(".folderRow");
      if (input.hasClass("deleteInput")) {
        input.removeClass("deleteInput").prop("disabled", false);
      } else {
        input.addClass("deleteInput").prop("disabled", true);
      }
    });
    $("#foldersEdit .deleteFolder").click(function () {
      var _this = $(this);
      warningConfirm(
        lang('remove_videos_notice_vod'),
        function () {
          const id = _this.parents("tr").attr("data-value");
          FolderDelete(id);
          foldersEdit.row(_this.parents("tr")).remove().draw();
        }
      );
    });
  }
});
