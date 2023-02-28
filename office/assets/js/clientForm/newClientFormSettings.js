$(document).ready(function () {
  //build formOnLoad
  function RefreshForm() {
    showLoader();
    $.ajax({
      url: "/office/ajax/clientForm/getFormData.php",
      type: "post",
      data: JSON.stringify({ type: $("#form_type").val() }),
      success: function (response) {
        BuildFormCallback(response);
        console.log(response);
        hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
        hideLoader();
      },
    });
  }
  RefreshForm();

  //add drag and drop sorting
  $("tbody").sortable();

  //change type of form based on dropbox on top
  $("#form_type").on("change", function () {
    showLoader();
    $.ajax({
      url: "/office/ajax/clientForm/getFormData.php",
      type: "POST",
      data: JSON.stringify({ type: $("#form_type").val() }),
      success: function (response) {
        BuildFormCallback(response);
        console.log(response);
        hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
        hideLoader();
      },
    });
  });

  function BuildFormCallback(response) {
    $("#company_num").val(response.form.company_num);
    $("#form_id").val(response.form.form_id);
    $("#tbodyFormSettings").html("");
    let type = response.form.type;

    response.fields.forEach((field) => {
      let optionString = "";
      if (field.options) {
        const options = JSON.parse(field.options);
        options.forEach(
          (option) =>
            (optionString += `<div class="flexRow" ><input class="optionValue" value="${option}"><span class="deleteButton"><i class="far fa-trash-alt" aria-hidden="true"></i></span></div>`)
        );
      }
      let default_field =
        type == "client"
          ? field.customer_default_field
          : field.lead_default_field;
      $("#tbodyFormSettings").append(`
            <tr data-id="${field.field_id}" class="FieldRow">
            <td style="width: 60%">
                <i class="fas fa-grip-vertical"></i>
                <div class="flexRow"><input class="fieldName" ${
                  default_field == 1 ? "disabled" : ""
                } value="${field.name}"> ${
        default_field == 1
          ? ""
          : '<span class="deleteButtonRow"><i class="far fa-trash-alt"></i></span>'
      }</div>
            </td>
            <td style="width: 10%">
                <label class="replace">
                    <input type="checkbox" class="show_checkbox fieldCheckbox" id="show_${
                      field.field_id
                    }" name="show_${field.field_id}" ${
        field.show == 1 ? "checked" : ""
      } ><br>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td style="width: 10%">
                <label class="replace">
                    <input type="checkbox" class="require_checkbox fieldCheckbox" name="mandatory_${
                      field.field_id
                    }" ${field.mandatory == 1 ? "checked" : ""}><br>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td style="width: 20%">
                <select class="fieldType" ${
                  default_field == 1 ? "disabled" : ""
                } >
                    <option value="string" ${
                      field.type == "string" ? "selected" : ""
                    } >טקסט</option>
                    <option value="number"  ${
                      field.type == "number" ? "selected" : ""
                    } >מספר</option>
                    <option value="list"  ${
                      field.type == "list" ? "selected" : ""
                    } >רשימה</option>
                    <option value="date" ${
                      field.type == "date" ? "selected" : ""
                    } >תאריך</option>
                    <option value="radio" ${
                      field.type == "radio" ? "selected" : ""
                    } >כפתור בחירה</option>
                    <option value="checkbox" ${
                      field.type == "checkbox" ? "selected" : ""
                    } >צ'ק בוקס</option>
                </select>
                ${
                  field.type == "radio" || field.type == "list"
                    ? `<div class="optionDiv"> 
                ${field.options ? optionString : ""}
                <button class="addOption" type="button">הוסף אופציה</button> 
                </div>`
                    : ""
                }
            </td>
        </tr> 
    `);
      setupDeleteButtons();
    });
  }

  //add options in radiobutton and list types
  $("#fields-table").on("change", ".fieldType", function () {
    let type = $(this).val();
    let trDiv = $(this).parent().parent();
    let tdDiv = $(this).parent();
    if (type == "radio" || type == "list") {
      let hasOptions = tdDiv.find(".optionDiv");
      if (!hasOptions.length) {
        let input =
          '<div class="optionDiv"><div class="flexRow"><input class="optionValue" value=""><span class="deleteButton"><i class="far fa-trash-alt" aria-hidden="true"></i></span></div>' +
          '<button class="addOption" type="button">הוסף אופציה</button>' +
          "</div>";
        tdDiv.append(input);
        setupDeleteButtons();
      }
    } else {
      tdDiv.find(".optionDiv").remove();
    }
  });

  //add row when click הוסף אופציה
  $("#fields-table").on("click", ".addOption", function () {
    let input =
      '<div class="flexRow"><input class="optionValue" value=""><span class="deleteButton"><i class="far fa-trash-alt" aria-hidden="true"></i></span></div>';
    $(this).before(input);
    setupDeleteButtons();
  });

  //make checkboxes functional
  $("#fields-table").on("click", ".fieldCheckbox", function () {
    let checked = $(this);
    if (
      typeof checked.attr("checked") !== typeof undefined &&
      checked.attr("checked") !== false
    ) {
      checked.removeAttr("checked");
    } else {
      checked.attr("checked", true);
    }
  });

  //add new field row button
  $("#add_new_field").click(function () {
    let tbody = $(".clientSettingForm");
    tbody.append(
      '<tr data-id="" class="FieldRow">' +
        '<td style="width: 60%">' +
        '<i class="fas fa-grip-vertical"></i>' +
        '<div class="flexRow"><input class="fieldName" value=""><span class="deleteButtonRow"><i class="far fa-trash-alt"></i></span></div>' +
        "</td>" +
        '<td style="width: 10%">' +
        '<label class="replace">' +
        '<input type="checkbox" class="show_checkbox fieldCheckbox" id="" name=""><br>' +
        '<span class="checkmark"></span>' +
        "</label>" +
        "</td>" +
        '<td style="width: 10%">' +
        '<label class="replace">' +
        '<input type="checkbox" class="require_checkbox fieldCheckbox" id="" name=""><br>' +
        '<span class="checkmark"></span>' +
        "</label>" +
        "</td>" +
        "<td>" +
        '<select class="fieldType">' +
        '<option value="string">טקסט</option>' +
        '<option value="number">מספר</option>' +
        '<option value="list">רשימה</option>' +
        '<option value="date">תאריך</option>' +
        '<option value="radio">כפתור בחירה</option>' +
        '<option value="checkbox">צ\'ק בוקס</option>' +
        "</select>" +
        "</td>" +
        "</tr>"
    );
    setupDeleteButtons();
  });

  //save button functionality with ajax
  $(".saveSettings").click(function () {
    showLoader();
    let fields = $(".FieldRow");
    let data = {};
    let form_id = $("#form_id").val();
    fields.each(function (key, value) {
      let elem = $(this);
      let rowData = {};
      rowData["name"] = elem.find(".fieldName").val();
      if (rowData["name"] == "") {
        return;
      }
      rowData["id"] = elem.attr("data-id");
      rowData["form_id"] = form_id;
      rowData["order"] = key + 1;
      rowData["display"] = elem.find(".show_checkbox").val();
      if (
        elem.find(".show_checkbox").attr("checked") !== false &&
        elem.find(".show_checkbox").attr("checked") !== undefined
      ) {
        rowData["display"] = "1";
      } else {
        rowData["display"] = "0";
      }
      if (
        elem.find(".require_checkbox").attr("checked") !== false &&
        elem.find(".require_checkbox").attr("checked") !== undefined
      ) {
        rowData["require"] = "1";
      } else {
        rowData["require"] = "0";
      }
      rowData["type"] = elem.find(".fieldType").val();
      if (rowData["type"] === "list" || rowData["type"] === "radio") {
        let option = elem.find(".optionValue");
        let optionData = [];
        option.each(function (key, value) {
          optionData[key] = $(this).val();
        });
        if (optionData.length > 0) {
          rowData["options"] = optionData;
        }
      }
      data[key] = rowData;
    });
    updateForm(data);
  });

  //save ajax request
  function updateForm(data) {
    data["form"] = "1";
    $.ajax({
      url: "/office/ajax/clientForm/updateForm.php",
      type: "post",
      data: data,
      success: function (response) {
        console.log(response);
        RefreshForm();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
        RefreshForm();
      },
    });
  }
  function setupDeleteButtons() {
    $(".deleteButton").unbind("click");
    $(".deleteButton").click(function () {
      $(this).parent().remove();
    });
    $(".deleteButtonRow").unbind("click");
    $(".deleteButtonRow").click(function () {
      const _this = $(this);
      showLoader();
      const id = _this.parents("tr").attr("data-id");
      if (id && id != "") {
        $.ajax({
          url: "/office/ajax/clientForm/deleteRow.php",
          type: "post",
          data: JSON.stringify({ id: id, form_id: $("#form_id").val() }),
          success: function (response) {
            console.log(response);
            _this.parents("tr").remove();
            hideLoader();
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            hideLoader();
          },
        });
      } else {
        _this.parents("tr").remove();
        hideLoader();
      }
    });
  }
});
