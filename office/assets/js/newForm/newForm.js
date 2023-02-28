/* Hebrew initialisation for the UI Datepicker extension. */
/* Written by Amir Hardon (ahardon at gmail dot com). */
(function (factory) {
  if (typeof define === "function" && define.amd) {
    // AMD. Register as an anonymous module.
    define(["../widgets/datepicker"], factory);
  } else {
    // Browser globals
    factory(jQuery.datepicker);
  }
})(function (datepicker) {
  datepicker.regional.he = {
    closeText: "סגור",
    prevText: "&#x3C;הקודם",
    nextText: "הבא&#x3E;",
    currentText: "היום",
    monthNames: [
      "ינואר",
      "פברואר",
      "מרץ",
      "אפריל",
      "מאי",
      "יוני",
      "יולי",
      "אוגוסט",
      "ספטמבר",
      "אוקטובר",
      "נובמבר",
      "דצמבר",
    ],
    monthNamesShort: [
      "ינו",
      "פבר",
      "מרץ",
      "אפר",
      "מאי",
      "יוני",
      "יולי",
      "אוג",
      "ספט",
      "אוק",
      "נוב",
      "דצמ",
    ],
    dayNames: ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת"],
    dayNamesShort: ["א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת"],
    dayNamesMin: ["א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת"],
    weekHeader: "Wk",
    dateFormat: "dd/mm/yy",
    firstDay: 0,
    isRTL: true,
    showMonthAfterYear: false,
    yearSuffix: "",
  };
  datepicker.setDefaults(datepicker.regional.he);

  return datepicker.regional.he;
});


function getIcon(name) {
  switch (name) {
    case "שם פרטי":
    case "שם משפחה":
    case "אנשי קשר":
    case "פרטי ליד":
      return '<i class="fas fa-user"></i>';
    case "מספר טלפון":
      return '<i class="fas fa-phone"></i>';
    case "אימייל":
      return '<i class="fas fa-envelope"></i>';
    case "תיעוד לקוח":
      return '<i class="fas fa-file-signature"></i>';
    case "כתובת":
      return '<i class="fas fa-map-marked"></i>';
  }
  return "";
}
//
function getBlock(field) {
  var name = field.name;
  switch (name) {
    case "פרטי ליד":
      return `<div class="fields-wrapper row mt-5 mb-5 grey">
            <div class="col-md-12 m-0">
                <div class="title">
              ${getIcon(name)}${name}
                </div>
            </div>
            <div class="col-md-6">
                <label for="leadStatus">סטאטוס</label>
                <select data-mandatory="${
        field.mandatory
        }"  id="leadStatus">
            </select>
            </div>
            <div class="col-md-6">
                <label for="leadSrc">מקור הגעה</label>
                <input value="ללא"  disabled data-mandatory="${
        field.mandatory
        }" type="text" id="leadSrc">
            </div>
            <div class="col-md-12">
                <label for="interestedIn">מתעניין ב</label>
                <input data-mandatory="${
        field.mandatory
        }" type="text" id="interestedIn"
                  class='some_class_name'
                  name='input' 
                  placeholder='' 
                  value='' 
                  data-blacklist=''
                >
            </div>
         </div>      `;

    case "תיעוד לקוח":
      return `<div class="fields-wrapper row mt-5 mb-5 grey">
                <div class="col-md-12 m-0">
                    <div class="title">
                      ${getIcon(name)}${name}
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="userDucomentation">תוכן התיעוד</label>
                    <textarea data-mandatory="${
        field.mandatory
        }" id="userDucomentation"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="replace" for="addComment">
                        <input data-mandatory="${
        field.mandatory
        }" type="checkbox" value="" id="addComment">
                        <span class="checkmark"></span>
                        <div class="check-label">הוסף הערה כאייקון קבוע לשם הלקוח</div>
                    </label>
                </div>
            </div>`;
    case "אנשי קשר":
      return `<div class="fields-wrapper row mt-5 mb-5 grey">
                <div id="contactsWrapper" class="row m-0">
                    <div class="col-md-12 m-0">
                      <div class="title">
                        ${getIcon(name)}${name}
                      </div>
                    </div>
                     <div class="repeat row m-0" >
                        <div class="col-md-6">
                            <label for="contactName1">שם מלא</label>
                            <input data-mandatory="${
        field.mandatory
        }" type="text" class="contactName" id="contactName1">
                        </div>
                        <div class="col-md-6">
                            <label for="contactRelative1">קרבה</label>
                            <input data-mandatory="${
        field.mandatory
        }" type="text" class="contactRelative" id="contactRelative1">
                        </div>
                        <div class="col-md-6">
                            <label for="contactPhone1">מספר נייד</label>
                            <div class="phone-container d-flex">
                                <input data-mandatory="${
        field.mandatory
        }" class="ml-3 contactPhone" type="text" id="contactPhone1" style="flex:8;">
                                <select class="contactPhoneZone" id="contactPhoneZone1" style="flex:2;">
                                    <option selected value='+972'>+972</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="contactEmail1">אימייל</label>
                            <input data-mandatory="${
        field.mandatory
        }" type="email" class="contactEmail" id="contactEmail1">
                        </div>
                    </div>    
                </div>
                <div class="row m-0" >
                    <div class="col-md-12 m-0" >
                        <div id="addContact" class="btn-addrow">
                        + איש קשר נוסף  
                        </div>
                    </div>
                </div>
            </div>`;

    case "כתובת":
      return `<div class="fields-wrapper row mt-5 mb-5 grey">
            <div class="col-md-12 m-0">
                <div class="title">
                  ${getIcon(name)}${name}
                </div>
            </div>
            <div class="ui-widget inputcontainer col-md-6">
                <label for="city">עיר</label>
                <input data-mandatory="${
        field.mandatory
        }" type="text" id="city">
                <div class="icon-container">
                    <i class="loader"></i>
                </div>
            </div>
            <div class="ui-widget inputcontainer col-md-6">
                <label for="street">רחוב</label>
                <input data-mandatory="${
        field.mandatory
        }" type="text" id="street">
                <div class="icon-container">
                    <i class="loader"></i>
                </div>
            </div>
            <div class="col-md-3">
                <label for="apartment">דירה</label>
                <input data-mandatory="${
        field.mandatory
        }" type="number" id="apartment">
            </div>
            <div class="col-md-3">
                <label for="number">מספר</label>
                <input data-mandatory="${
        field.mandatory
        }" type="number" id="number">
            </div>
            <div class="col-md-3">
                <label for="mailbox">ת.ד</label>
                <input data-mandatory="${
        field.mandatory
        }" type="number" id="mailbox">
            </div>
            <div class="col-md-3">
                <label for="zip">מיקוד</label>
                <input data-mandatory="${
        field.mandatory
        }" type="number" id="zip">
            </div>
        </div>`;

    case "שם פרטי":
      return `<div class="col-md-6">
                <label for="fname">${name}</label>
                <input data-mandatory="${
        field.mandatory
        }" type="text" id="fname">
                <div class="icon-container visible-always">
                  ${getIcon(name)}
                </div>
            </div>`;

    case "שם משפחה":
      return ` <div class="col-md-6">
                <label for="lname">${name}</label>
                <input data-mandatory="${
        field.mandatory
        }" type="text" id="lname">
                <div class="icon-container visible-always">
                  ${getIcon(name)}
                 </div>
            </div>`;

    case "מספר טלפון":
      return ` <div class="col-md-6">
                <label for="pphone">${name}</label>
                <div class="phone-container  d-flex">
                    <input data-mandatory="${
        field.mandatory
        }" class="ml-3" type="text" id="pphone" style="flex:8;">
                    <select data-mandatory="" id="phoneZone" style="flex:2;">
                        <option selected value='+972'>+972</option>
                    </select>
                </div>
                <div class="icon-container">
                ${getIcon(name)}
              </div>
            </div>`;

    case "אימייל":
      return `    <div class="col-md-6">
                <label for="pemail">${name}</label>
                <input data-mandatory="${
        field.mandatory
        }" type="email" id="pemail">
            <div class="icon-container visible-always">
              ${getIcon(name)}
            </div>
            </div>`;

    case "תאריך לידה":
      return `<div class="col-md-6">
            <label for="birthday">${name}</label>
            <input data-mandatory="${field.mandatory}" type="text" id="birthday">
        </div>`;

    case "מין":
      return `<div id="gender" data-mandatory="${field.mandatory}" class="d-flex justify-content-around col-md-6 col-12 radios align-items-end">
                <label for="">${name}</label>
                <label for="male">
                    <input  type="radio" name="gender" id="male">
                    זכר
                </label>
                <label for="female">
                    <input type="radio" name="gender" id="female">
                    נקבה 
                </label>
                <label for="else">
                    <input type="radio" name="gender" id="else">
                    אחר
                </label>
            </div>`;

    case "תעודת זהות":
      return `<div class="col-md-6">
                <label for="id">תעודת זהות</label>
                <input data-mandatory="${field.mandatory}" type="number" id="id">
            </div>`;
    case 'דיוור':
      return `<div class="col-md-6 col-md-6 mt-4">
            <label class="replace" for="smsMailing">
                <input data-mandatory="${field.mandatory}" type="checkbox" value="" id="smsMailing">
                <span class="checkmark"></span>
                <div class="check-label">קבלת דיוור במסרון</div>
            </label>
        </div>
        <div class="col-md-6 col-md-6 mt-4">
            <label class="replace" for="mailMailing">
                <input data-mandatory="${field.mandatory}" type="checkbox" value="" id="mailMailing">
                <span class="checkmark"></span>
                <div class="check-label">קבלת דיוור במייל</div>
            </label>
        </div>`
  }
}

function getRepeatBlock(field_name, repeatNum) {
  switch (field_name) {
    case "אנשי קשר":
      return `<hr><div class="repeat row m-0" >
            <div class="col-md-6">
                <label for="contactName${repeatNum}">שם מלא</label>
                <input data-mandatory="" type="text" class="contactName" id="contactName${repeatNum}">
            </div>
            <div class="col-md-6">
                <label for="contactRelative${repeatNum}">קרבה</label>
                <input data-mandatory="" type="text" class="contactRelative" id="contactRelative${repeatNum}">
            </div>
            <div class="col-md-6">
                <label for="contactPhone${repeatNum}">מספר נייד</label>
                <div class="phone-container d-flex">
                    <input data-mandatory="" class="ml-3 contactPhone" type="text" id="contactPhone${repeatNum}" style="flex:8;">
                    <select data-mandatory="" class="contactPhoneZone" id="contactPhoneZone${repeatNum}" style="flex:2;">
                        <option selected value='+972'>+972</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label for="contactEmail${repeatNum}">אימייל</label>
                <input data-mandatory="" type="email" class="contactEmail" id="contactEmail${repeatNum}">
            </div>
        </div>    `;
  }
}

function getBlockByType(field) {
  switch (field.type) {
    case "string":
      $("#fields .dynamic-wrapper").append(
        `  <div class="col-md-6">
              <label for="${field.field_id}">
                ${field.name} 
              </label>
              <input data-custom=1 data-name="${field.name}" data-mandatory="${field.mandatory}" type="text"  id="${field.field_id}">
            </div>`
      );
      break;

    case "number":
      $("#fields .dynamic-wrapper").append(
        `  <div class="col-md-6">
            <label for="${field.field_id}">
            ${field.name}
            </label>
              <input  data-custom=1 data-name="${field.name}" data-mandatory="${field.mandatory}" type="number"  id="${field.field_id}">
          </div>`
      );
      break;

    case "list":
      $("#fields .dynamic-wrapper").append(
        $(
          `<div class="col-md-6" >
            <label for="${field.field_id}">
              ${field.name}
            </label>
            <select  data-custom=1 data-name="${field.name}" data-mandatory="${field.mandatory}" id="${field.field_id}">
            </select>
          </div>`
        )
      );
      if (field.options && field.options.length > 0) {
        $.each(field.options, function (key, value) {
          $(`#${field.field_id}`).append(
            $("<option></option>").attr("value", value).text(value)
          );
        });
      }
      break;

    case "date":
      $("#fields .dynamic-wrapper").append(
        `<div class="col-md-6 col-12" >
            <label for="${field.field_id}">
              ${field.name}
            </label>  
          <input data-custom=1 data-name="${field.name}" data-mandatory="${field.mandatory}" type="text" id="${field.field_id}">
        </div>`
      );
      break;

    case "radio":
      $("#fields .dynamic-wrapper").append(
        `<div data-mandatory="${field.mandatory}" data-custom=1 data-radio=1 class="d-flex justify-content-around col-md-6 col-12 radios align-items-end" id="${field.field_id}">
        <label for="">
            ${field.name} 
          </label>
        </div>`
      );
      if (field.options && field.options.length > 0) {
        $.each(field.options, function (key, value) {
          var id = field.field_id + "-" + key;
          $(`#${field.field_id}`).append(
            `<label for="${id}">
              <input data-name="${field.name}"  type="radio" id="${id}" name="${field.name}" value="${value}">
              ${value} 
            </label>`
          );
        });
      }
      break;

    case "checkbox":
      $("#fields .dynamic-wrapper").append(
        `<div class="d-flex col-md-6 col-12">
          <label class="replace" for="${field.field_id}">
            <input data-custom=1 data-check=1 data-name="${field.name}" data-mandatory="${field.mandatory}" type="checkbox" value="${field.name}" id="${field.field_id}">
            <span class="checkmark"></span>
            <div class="check-label">${field.name}</div>
          </label>
        </div>`
      );
      break;
  }
}

function getHiddenInputBlock(field) {
  return (
    `<input data-mandatory="${field.mandatory}" type="hidden" id="${field.field_id}">`
  );
}

function addContact(repeatNum) {
  $("#contactsWrapper").append(getRepeatBlock("אנשי קשר", repeatNum));
}

/**
 *
 * @param {*} data {type:'', city:'', street:''}
 * @param {*} callback fn
 */
function autoComplete(input, data, callback) {
  input.siblings(".icon-container").show();
  switch (data.type) {
    case "city":
      $.ajax({
        url: "/office/GetCities.php",
        type: "post",
        dataType: "json",
        contentType: "application/json",
        success: function (res) {
          input.siblings(".icon-container").hide();
          if (res) callback(res);
          else throw new Error("empty data");
        },
        error: function (err) {
          input.siblings(".icon-container").hide();

          console.log(err);
        },
        data: JSON.stringify({
          city: data.city,
        }),
      });
      break;

    case "street":
      $.ajax({
        url: "/office/GetStreets.php",
        type: "post",
        dataType: "json",
        contentType: "application/json",
        success: function (res) {
          input.siblings(".icon-container").hide();
          if (res) callback(res);
          else throw new Error("empty data");
        },
        error: function (err) {
          input.siblings(".icon-container").hide();
          console.log(err);
        },
        data: JSON.stringify({
          city: data.city,
          street: data.street,
        }),
      });
      break;
  }
}

function getLeadStatusLst(callback) {
  showLoader();
  $.ajax({
    url: "/office/GetLeadStatus.php",
    type: "post",
    dataType: "json",
    contentType: "application/json",
    success: function (res) {
      hideLoader();
      callback(res);
    },
    error: function (err) {
      hideLoader();
      console.log(err);
    },
    data: JSON.stringify({}),
  });
}

function getFormByType(type, callback) {
  showLoader();
  $.ajax({
    url: "/office/GetFormByType.php",
    type: "post",
    dataType: "json",
    contentType: "application/json",
    success: function (res) {
      hideLoader();
      callback(res);
    },
    error: function (err) {
      hideLoader();
      console.log(err);
    },
    data: JSON.stringify({ type: type }),
  });
}

function buildForm(res, formType) {
  res.form.forEach(function (field) {
    if (field.options) {
      field.options = JSON.parse(field.options);
    }

    if (field.show === "0") {
      $("#fields .dynamic-wrapper").append(getHiddenInputBlock(field))
    }
    var isDefault;
    if (formType === "client") {
      isDefault = field.customer_default_field == "1";
    } else {
      isDefault = field.lead_default_field == "1";
    }
    if (isDefault) {
      if (
        field.name === "שם פרטי" ||
        field.name === "שם משפחה" ||
        field.name === "מספר טלפון" ||
        field.name === "אימייל" ||
        field.name === "תאריך לידה" ||
        field.name === "מין" ||
        field.name === "תעודת זהות" ||
        field.name === "דיוור"
      ) {
        $("#fields .dynamic-wrapper").append(getBlock(field));
      } else {
        $("#fields").append(getBlock(field));
      }
    } else {
      getBlockByType(field);
    }
  });

  $("#fields").append(
    `<input id="fType" type="hidden" value="` + formType + `">`
  );
}

function setForm(formType) {
  $("#fields").html("");
  $("#fields").append(`<div class="row dynamic-wrapper mt-5 mb-5"></div>`);
  getFormByType(formType, function (res) {
    if (res && res.form) {
      buildForm(res, formType);
      $("#city").on("keyup change", function () {
        if ($(this).val().length >= 2) {
          autoComplete(
            $(this),
            {
              type: "city",
              city: $(this).val(),
              street: "",
            },
            function (cities) {
              $("#city").autocomplete({
                source: cities,
                change: function (event, ui) {
                  $(this).val((ui.item ? ui.item.value : ""));
                }
              });
            }
          );
        }
      });
      $("#street").on("keyup change", function () {
        if ($(this).val().length >= 1) {
          autoComplete(
            $(this),
            {
              type: "street",
              city: $("#city").val(),
              street: $(this).val(),
            },
            function (streets) {
              $("#street").autocomplete({
                source: streets,
                change: function (event, ui) {
                  $(this).val((ui.item ? ui.item.value : ""));
                }
              });
            }
          );
        }
      });
      $("#birthday").datepicker($.datepicker.regional["he"]);

      if ($('#leadStatus')) {
        getLeadStatusLst(function (leadStatusList) {
          leadStatusList.forEach(function (leadStatus) {
            $('#leadStatus').append(`<option value=${leadStatus.id}>${leadStatus.Title}</option>`);
          });
        })
      }

      var contactRepeat = 2;
      $("#addContact").on("click", function () {
        addContact(contactRepeat);
        contactRepeat++;
      });
      tagifyCheck();
    }
  });
}

$(document).ready(function () {
  setForm("client");
  $(".client-btn").on("click", function () {
    setForm("client");
    $(this).addClass("current-btn");
    $(".lead-btn").removeClass("current-btn");
  });
  $(".lead-btn").on("click", function () {
    setForm("lead");
    $(this).addClass("current-btn");
    $(".client-btn").removeClass("current-btn");
  });
});
