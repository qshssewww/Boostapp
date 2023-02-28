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

  //folders table
  folders = $("#folders").DataTable({
    // "columns": [
    //   { "width": "5%" },
    //   { "width": "5%" },
    //   { "width": "20%" },
    //   { "width": "20%" },
    //   { "width": "20%" },
    //   { "width": "20%" },
    //   { "width": "5%" },
    //   { "width": "5%" },

    // ],
    // language: BeePOS.options.datatables,
    "language": {
      "emptyTable": lang('no_videos_vod_library')
    },
    responsive: true,
    ajax: { url: "VideoPost.php", type: "POST" },
    processing: true,
    rowReorder: {
      selector: ".LibraryVideoRow td:first-child ",
      dataSrc: 0,
      update: false,
    },
    autoWidth: false,
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
    let folderRow = $("#folders .folderRow");
    folderRow.parents("tr").css("background-color", "#F4F4F4");
    folderRow.each(function () {
      let folderId = $(this).attr("data-value");
      $(this).parents("tr").attr("data-value", folderId);
      $(this).parents("tr").addClass("LibraryFolderRow");
    });
    let videoRow = $("#folders .videoRow");
    // videoRow.parents('tr').css("display", "none");
    videoRow.each(function () {
      let folderId = $(this).attr("data-folder");
      let videoId = $(this).attr("data-video");
      $(this).parents("tr").attr("data-folder", folderId);
      $(this).parents("tr").attr("data-video", videoId);
      $(this).parents("tr").addClass("LibraryVideoRow");
    });
    $("#folders .toggleDisplay").change(function () {
      const id = $(this).parents("tr").attr("data-video");
      const display = $(this).prop("checked");
      VideoDisplayUpdate(id, display ? 1 : 0);
    });
  }

  //search
  let dtTable = $("#folders").DataTable();
  $("#search-library").keyup(function () {
    dtTable.search($(this).val()).draw();
  });

  //dots for delete edit and pause
  let table = $("#folders");
  $(table).on("mouseenter", ".library-dots-btn", function () {
    let item = $(this).children(".rowBox");
    item.toggleClass("rowBox-active");
  });
  $(table).on("mouseleave", ".library-dots-btn", function () {
    let item = $(this).children(".rowBox");
    item.toggleClass("rowBox-active");
  });
  $(table).on("click", ".rowBox-item", function () {
    let item = $(this);
    let row_id = item.parents(".library-dots-btn").attr("data-id");
    if (item.attr("id") === "rowBoxEdit") {
      //Open video editing popup
      getVideoEditData(row_id, function (data) {
        $(".current").show();
        $(".new").hide();
        hideLoader();
        buildForm(data);
      });
    } else if (item.attr("id") === "rowBoxPause") {
      const Toggle = $(this).parents(".LibraryVideoRow").find(".toggleDisplay");
      Toggle.click();
      //   VideoDisplayUpdate(row_id);
    } else if (item.attr("id") === "rowBoxDel") {
      var _this = $(this);
      warningConfirm(
        "האם אתה בטוח שברצונך למחוק את הסרטון? פעולה זו אינה הפיכה",
        function () {
          VideoDelete(row_id);
          folders.row(_this.parents("tr")).remove().draw();
        }
      );
    }
  });

  //close and open
  $("#folders").on("click", ".LibraryFolderRow", function () {
    let icon = $(this).find($(".folderIcon"));
    let folderId = $(this).attr("data-value");
    let videos = $("#folders").find(
      ".LibraryVideoRow[data-folder='" + folderId + "']"
    );
    if (icon.hasClass("fa-chevron-down")) {
      $(this).addClass("LibraryFolderRowSelected");
      icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
      videos.each(function () {
        $(this).show();
      });
    } else if (icon.hasClass("fa-chevron-up")) {
      $(this).removeClass("LibraryFolderRowSelected");
      icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
      videos.each(function () {
        $(this).hide();
      });
    }
  });

  //reorder callback for db
  folders.on("row-reorder", function (e, diff, edit) {
    try {
      const changes = [];
      for (let i = 0; i < diff.length; i++) {
        const type = $(diff[i].node).hasClass("LibraryFolderRow")
          ? "folder"
          : "video";
        if (type == "folder") {
          alert("cant move away from folder");
          throw new Error("cant move away from folder");
        } else {
          const id = $(diff[i].node).attr("data-video");
          const position = diff[i].newPosition;
          changes.push({ id: parseInt(id), position: parseInt(position) });
        }
      }
      console.log(changes);
      $.ajax({
        url: "VideoPositionUpdate.php",
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
    } catch (err) {
      console.log(err);
      folders.ajax.reload(initCallback);
    }
  });

  //ajax functions
  function VideoDisplayUpdate(id, display = 0) {
    $.ajax({
      url: "VideoDisplayUpdate.php",
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
  function VideoDelete(id) {
    $.ajax({
      url: "VideoDelete.php",
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

  function getVideoEditData(id, callback) {
    showLoader();
    $.ajax({
      url: "/office/GetVideoEditData.php",
      type: "post",
      dataType: "json",
      contentType: "application/json",
      success: function (data) {
        callback(data);
      },
      data: JSON.stringify({ id: id }),
    });
  }


  function buildForm(data, isNew = false) {
    if (data.video) {
      $("#videoId").val(data.video.id).trigger("change");
      $("#videoName").val(data.video.name).trigger("change");
      $("#videoLink").val(data.video.externalLink).trigger("change");
      $("#videoDesc").val(data.video.description).trigger("change");

      if (data.video.time) {
        $("#h").val(getDuration(data.video.time)[0]).trigger("change");
        $("#m").val(getDuration(data.video.time)[1]).trigger("change");
        $("#s").val(getDuration(data.video.time)[2]).trigger("change");
      } else {
        $("#h").val('').trigger("change");
        $("#m").val('').trigger("change");
        $("#s").val('').trigger("change");
      }

      if (isNew && data.videoFolders.length > 0 && data.coachers) {
        data.video.folderId = data.videoFolders[0].id;
        data.video.guide = data.coachers[0].id;
      }
    }
    if (data.videoFolders) {
      $("#videoFolder").children().remove().end();
      $.each(data.videoFolders, function (key, value) {
        $("#videoFolder").append(
          $("<option></option>").attr("value", value.id).text(value.name)
        );
      });
      $("#videoFolder option[value=" + data.video.folderId + "]")
        .prop("selected", "selected")
        .trigger("change");
      if (!$('#videoFolder').hasClass('select2-hidden-accessible')) {
        $('#videoFolder').select2({
          tags: true
        });
      }


      // let videoFolders = $.map(data.videoFolders, function (value, key) {
      //   return {
      //     label: value.name,
      //     value: value.id
      //   }
      // });
      // $('#videoFolder').autocomplete({
      //   source: videoFolders,
      //   change: function (event, ui) {
      //     $(this).data('data-id', ui.item.value);
      //   }
      // });

    }
    if (data.coachers) {
      $("#videoCoacher").children().remove().end();
      $.each(data.coachers, function (key, value) {
        $("#videoCoacher").append(
          $("<option></option>")
            .attr("value", value.id)
            .text(value.display_name)
        );
      });
      $("#videoCoacher option[value=" + data.video.guide + "]")
        .prop("selected", "selected")
        .trigger("change");

      if (!$('#videoCoacher').hasClass('select2-hidden-accessible')) {
        $('#videoCoacher').select2();
      }

      // let videoCoachers = $.map(data.coachers, function (value, key) {
      //   return {
      //     label: value.display_name,
      //     value: value.id
      //   }
      // });
      // $('#videoCoacher').autocomplete({
      //   source: videoCoachers,
      //   change: function (event, ui) {
      //     $(this).val((ui.item ? ui.item.label : ""));
      //     $(this).data('data-id', ui.item.value);
      //   }
      // });
    }
    $("#popup-wrapper").addClass("visible");
  }

  function addVideo() {
    getVideoEditData("", function (data) {
      $(".current").hide();
      $(".new").show();
      hideLoader();
      buildForm(data, true);
      $("#popup-wrapper").addClass("visible");
    });
  }
  $(".add-video").on("click", addVideo);

  /////editVideo.js
  function Video(video = null) {
    this.id = video ? video.id : null;
    this.name = video ? video.name : null;
    this.folderId = video ? video.folderId : null;
    this.guide = video ? video.guide : null;
    this.externalLink = video ? video.externalLink : null;
    this.description = video ? video.description : null;
    this.duration = video ? video.duration : null;
  }

  Video.prototype.saveVideo = function (callback) {
    if (!this.name || !this.externalLink || !this.description) {
      $(".message").show();
      return;
    } else {
      $(".message").hide();
    }
    showLoader();
    $.ajax({
      url: "/office/saveVideo.php",
      type: "post",
      dataType: "json",
      contentType: "application/json",
      success: function (data) {
        hideLoader();
        callback(data);
      },
      error: function () {
        hideLoader();

      },
      data: JSON.stringify(this),
    });
  };

  var video = new Video();

  $("#h").on("change paste keyup", function () {
    video.duration = setDuration($(this).val(), $("#m").val(), $("#s").val());
    console.log(getDuration(video.duration))
  });
  $("#m").on("change paste keyup", function () {
    video.duration = setDuration($('#h').val(), $(this).val(), $("#s").val());
    console.log(getDuration(video.duration))
  });
  $("#s").on("change paste keyup", function () {
    video.duration = setDuration($('#h').val(), $("#m").val(), $(this).val());
    console.log(getDuration(video.duration))
  });

  video.name = $("#videoName").val();
  $("#videoName").on("change paste keyup", function () {
    video.name = $(this).val();
    setIsValid($(this), $(this).val() != "");
  });

  video.externalLink = $("#videoLink").val();
  $("#videoLink").on("change paste keyup", function () {
    video.externalLink = $(this).val();
    var regex = new RegExp(
      "^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?"
    );
    setIsValid($(this), regex.test(video.externalLink));
  });

  video.description = $("#videoDesc").val();
  $("#videoDesc").on("change paste keyup", function () {
    video.description = $(this).val();
    setIsValid($(this), $(this).val() != "");
  });

  video.guide = $("#videoCoacher").val();
  $("#videoCoacher").on("change", function () {
    video.guide = this.value;
  });

  video.folderId = $("#videoFolder").val();
  $("#videoFolder").on("change", function () {
    video.folderId = this.value;
  });

  video.id = $("#videoId").val();
  $("#videoId").on("change", function () {
    video.id = this.value;
  });

  $("#confirmBtn").on("click", function () {
    video.saveVideo(function (data) {
      if (data && data.operation) {
        if (data.operation === "update") {
          if (data.result == 1) {
            folders.ajax.reload(initCallback);
            $("#popup-wrapper").removeClass("visible");
          }
        } else if (data.operation === "insert") {
          if (data.result !== "failed") {
            folders.ajax.reload(initCallback);
            $("#popup-wrapper").removeClass("visible");
          }
        }
      }
    });
  });

  $("#closePopup , #cancelBtn").on("click", function () {
    $("#popup-wrapper").removeClass("visible");
  });

  function setIsValid(input, isValid) {
    if (isValid) {
      input.removeClass("not-valid");
    } else {
      input.addClass("not-valid");
    }
  }

  function setDuration(h, m, s) {

    if (h === '') {
      h = '00';
    }
    else if (h < 10 && h >= 0) {
      h = '0' + h;
    }

    if (m === '') {
      m = '00';
    }
    else if (m < 10 && m >= 0) {
      m = '0' + m;
    }

    if (s === '') {
      s = '00';
    }
    else if (s < 10 && s >= 0) {
      s = '0' + s;
    }

    return h + ':' + m + ':' + s;
  }

  function getDuration(duration) {
    //2 = h
    //1 = m
    //0 = s
    var arr = duration.split(':');
    for (var i = 0; i < arr.length; i++) {
      arr[i] = parseInt(arr[i]);
    }
    return arr;
  }

  //Edit video folder Popup

  $("#openVideoFolderEdit").on("click", function () {
    $("#popup-wrapper-2").addClass("visible");
  });
  $("#closePopup2").on("click", function () {
    $("#popup-wrapper-2").removeClass("visible");
  });

  $('#durationBtn').on('click',function(){
    $('#duration').toggle("slow")
  })

  $("#videoLink").focusout(function () {
      let video =   $("#videoLink");
      let link = video.val();
      if(link != "" && !link.includes("youtube") && !link.includes("vimeo") && !link.includes("youtu.be")){
        customAlert(
            " Youtube-ו Vimeo ניתן לשתף רק סרטוני",
            function () {
              removeVal(video);
            }
        );
      }
  });
  function removeVal(video) {
    video.val("");
  }
});
