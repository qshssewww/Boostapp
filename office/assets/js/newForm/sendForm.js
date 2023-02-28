$(document).ready(function () {

  function handleResponse(formType, data) {

    if (formType === 'client') {

    }

    if (formType === 'lead') {

    }

  }

  function is_valid(value, type) {
    switch (type) {
      case "text":
        return value.length > 0 && typeof value !== 'null' && typeof value !== 'undefined';
      case "email": {
        let regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return regex.test(value);
      }
      case "id": {
        return value.toString().length === 9;
      }
      case "phone": {
        let regex = /^05\d{7}$/;
        return regex.test(value);
      }
    }

  }

  $("#submitForm").click(function () {
    //change to real id
    let cf = checkCustomInputs();
    console.log(cf)
    if ($("#fType").val() == "client") {
      valid = validateClientFields();
      if (valid && cf) {
        sendClient(function (res) {
          if (res && res.alreadyExists) {
            let html = `<div class="container mt-3 mb-3">`;
            if (res.data.length > 0) {
              res.data.forEach(obj => {
                html += `<div class="row mb-2">
                  <div class="col-6 d-flex justify-content-center">
                    ${obj.CompanyName}
                  </div>
                  <div class="col-6 d-flex justify-content-center">
                    <a target="_blank" href="/office/ClientProfile.php?u=${obj.id}">
                      <div class="btn btn-primary">
                         לינק לעמוד משתמש
                      </div>
                    </a>
                  </div>
                </div>`
              });
              html += `</div>`;
              Swal.fire({
                title: 'רשימת משתמשים',
                icon: 'info',
                html: html,
                showCloseButton: true,
                showConfirmButton: true,
                confirmButtonText: 'בסדר'
              }).then(res => {
                $('.client-btn').click()
              })
            }
            //res.data
            //user name: res.data.CompanyName
            //user id; res.data.id 


          } else {
            Swal.fire(
              'המידע נשמר',
              '',
              'success'
            ).then(res => {
              $('.client-btn').click()
            })
          }
        });
        //clear form or exit popup is inside this function
      }
    } else {
      valid = validateLeadFields();
      if (valid && cf) {
        sendLead(function (res) {
          Swal.fire(
            'המידע נשמר',
            '',
            'success'
          ).then(res => {
            $('.lead-btn').click()
          })
        });
        //clear form or exit popup is inside this function
      }
    }
  });

  function showMessage(element, message) {
    if (element.attr('data-radio') == 1 || element.attr('id') === 'gender') {
      element = element.children(":first");
    }
    if (element.prev().attr('class') === 'valid-msg')
      return
    element.parent().css({
      position: 'relative'
    })
    element.css({
      'border-color': 'red'
    });
    element.before(`<div class="valid-msg">${message}</div>`);
  }

  function setValid(element) {
    if (element.attr('data-radio') == 1 || element.attr('id') === 'gender') {
      element = element.children(":first");
    }
    if (element.prev().attr('class') === 'valid-msg') {
      element.css({
        'border-color': 'inherit'
      });
      element.prev().remove();
    }
  }

  function validateClientFields() {
    let valid = true;
    let message = '';

    if ($("#gender")) {
      let is_mandatory = $("#gender").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !checkRadio($('#gender'))) {
        valid = false;
        message = 'בחר אופציה';
        showMessage($("#gender"), message)
      } else {
        setValid($("#gender"))
      }
    }

    if ($("#addComment")) {
      let is_mandatory = $("#addComment").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !$("#addComment").is(':checked')) {
        valid = false;
        message = 'הכנס תוכן תיעוד';
        showMessage($("#addComment"), message)
      } else {
        setValid($("#addComment"))
      }
    }

    if ($("#apartment")) {
      let is_mandatory = $("#apartment").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#apartment").val(), 'text')) {
        valid = false;
        message = 'הכנס מס דירה';
        showMessage($("#apartment"), message)
      } else {
        setValid($("#apartment"))
      }
    }

    if ($("#birthday")) {
      let is_mandatory = $("#birthday").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#birthday").val(), 'text')) {
        valid = false;
        message = 'הכנס תאריך לידה';
        showMessage($("#birthday"), message)
      } else {
        setValid($("#birthday"))
      }
    }

    if ($("#city")) {
      let is_mandatory = $("#city").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#city").val(), 'text')) {
        valid = false;
        message = 'הכנס עיר';
        showMessage($("#city"), message)
      } else {
        setValid($("#city"))
      }
    }

    if ($("#fname")) {
      let is_mandatory = $("#fname").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#fname").val(), 'text')) {
        valid = false;
        message = 'הכנס שם';
        showMessage($("#fname"), message)
      } else {
        setValid($("#fname"))
      }
    }

    if ($("#id")) {
      let is_mandatory = $("#id").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#id").val(), 'id')) {
        valid = false;
        message = 'הכנס תעודת זהות';
        showMessage($("#id"), message)
      } else {
        setValid($("#id"))
      }
    }

    if ($("#lname")) {
      let is_mandatory = $("#lname").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#lname").val(), 'text')) {
        valid = false;
        message = 'הכנס שם משפחה';
        showMessage($("#lname"), message)
      } else {
        setValid($("#lname"))
      }
    }


    if ($("#mailMailing")) {
      let is_mandatory = $("#mailMailing").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !$("#mailMailing").is(':checked')) {
        valid = false;
        message = 'אשר קבלת מסרים בסמס';
        showMessage($("#mailMailing"), message)
      } else {
        setValid($("#mailMailing"))
      }
    }

    if ($("#mailbox")) {
      let is_mandatory = $("#mailbox").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#mailbox").val(), 'text')) {
        valid = false;
        message = 'הכנס תיבת דואר';
        showMessage($("#mailbox"), message)
      } else {
        setValid($("#mailbox"))
      }
    }

    if ($("#number")) {
      let is_mandatory = $("#number").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#number").val(), 'text')) {
        valid = false;
        message = 'הכנס מספר דירה';
        showMessage($("#number"), message)
      } else {
        setValid($("#number"))
      }
    }

    if ($("#pemail")) {
      let is_mandatory = $("#pemail").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#pemail").val(), 'email')) {
        valid = false;
        message = 'הכנס אימייל';
        showMessage($("#pemail"), message)
      } else {
        setValid($("#pemail"))
      }
    }

    if ($("#pphone")) {
      let is_mandatory = $("#pphone").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#pphone").val(), 'phone')) {
        valid = false;
        message = 'הכנס פלאפון';
        showMessage($("#pphone"), message)
      } else {
        setValid($("#pphone"))
      }
    }

    if ($("#smsMailing")) {
      let is_mandatory = $("#smsMailing").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !$("#smsMailing").is(':checked')) {
        valid = false;
        message = 'אשר קבלת מסרים בסמס';
        showMessage($("#smsMailing"), message)
      } else {
        setValid($("#smsMailing"))
      }
    }

    if ($("#street")) {
      let is_mandatory = $("#street").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#street").val(), 'text')) {
        valid = false;
        message = 'הכנס רחוב';
        showMessage($("#street"), message)
      } else {
        setValid($("#street"))
      }
    }

    if ($("#userDucomentation")) {
      let is_mandatory = $("#userDucomentation").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#userDucomentation").val(), 'text')) {
        valid = false;
        message = 'הכנס תוכן תיעוד';
        showMessage($("#userDucomentation"), message)
      } else {
        setValid($("#userDucomentation"))
      }
    }

    if ($("#zip")) {
      let is_mandatory = $("#zip").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#zip").val(), 'text')) {
        valid = false;
        message = 'הכנס מיקוד';
        showMessage($("#zip"), message)
      } else {
        setValid($("#zip"))
      }
    }
    return valid;
  }

  function validateLeadFields() {

    let valid = true;

    if ($("#fname")) {
      let is_mandatory = $("#fname").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#fname").val(), 'text')) {
        valid = false;
        message = 'הכנס שם';
        showMessage($("#fname"), message)
      } else {
        setValid($("#fname"))
      }
    }

    if ($("#lname")) {
      let is_mandatory = $("#lname").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#lname").val(), 'text')) {
        valid = false;
        message = 'הכנס שם משפחה';
        showMessage($("#lname"), message)
      } else {
        setValid($("#lname"))
      }
    }

    if ($("#pphone")) {
      let is_mandatory = $("#pphone").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#pphone").val(), 'phone')) {
        valid = false;
        message = 'הכנס פלאפון';
        showMessage($("#pphone"), message)
      } else {
        setValid($("#pphone"))
      }
    }

    if ($("#pemail")) {
      let is_mandatory = $("#pemail").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#pemail").val(), 'email')) {
        valid = false;
        message = 'הכנס אימייל';
        showMessage($("#pemail"), message)
      } else {
        setValid($("#pemail"))
      }
    }

    if ($("#leadStatus")) {
      let is_mandatory = $("#leadStatus").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#leadStatus").val(), 'text')) {
        valid = false;
        message = 'הכנס ליד סטאטוס';
        showMessage($("#leadStatus"), message)
      } else {
        setValid($("#leadStatus"))
      }
    }

    if ($("#leadSrc")) {
      let is_mandatory = $("#leadSrc").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#leadSrc").val(), 'text')) {
        valid = false;
        message = 'הכנס מקור ליד';
        showMessage($("#leadSrc"), message)
      } else {
        setValid($("#leadSrc"))
      }
    }

    if ($("#interestedIn")) {
      let is_mandatory = $("#interestedIn").attr('data-mandatory') == '1' ? true : false;
      if (is_mandatory && !is_valid($("#interestedIn").val(), 'text')) {
        valid = false;
        message = 'הכנס קורס';
        showMessage($("#interestedIn"), message)
      } else {
        setValid($("#interestedIn"))
      }
    }

    return valid;
  }

  function checkRadio(element) {
    console.log(element.find("input:checked").length);
    return element.find("input:checked").length === 1;
  }

  function checkCustomInputs() {

    let valid = true;

    $("#fields input[data-custom] , #fields select[data-custom] , #fields div[data-radio]").each(function () {
      if ($(this).attr('data-radio') == 1 && $(this).attr('data-mandatory') == 1) {
        let checked = checkRadio($(this));
        if (!checked) {
          valid = false;
          message = 'שדה זה חובה';
          showMessage($(this), message)
        } else {
          setValid($($(this)))
        }
      } else {
        let is_mandatory = $(this).attr('data-mandatory') == '1' ? true : false;
        if ($(this).attr('data-check') == 1) {
          if (is_mandatory && !$(this).is(':checked')) {
            valid = false;
            message = 'שדה זה חובה';
            showMessage($(this), message)
          } else {
            setValid($($(this)))
          }
        } else {
          if (is_mandatory && !is_valid($(this).val(), 'text')) {
            valid = false;
            message = 'שדה זה חובה';
            showMessage($(this), message)
          } else {
            setValid($($(this)))
          }
        }
      }
    });
    return valid;
  }

  function sendClient(callback) {
    showLoader();
    $.ajax({
      url: "ajax/clientForm/sendNewClientForm.php",
      type: "post",
      data: JSON.stringify(generateDataForClientSend()),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        hideLoader();
        console.log(response);
        callback(response)
        //exit popup or show message
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        //exit popup or show message
      },
    });
  }
  function sendLead(callback) {
    showLoader();
    $.ajax({
      url: "ajax/clientForm/sendNewLeadForm.php",
      type: "post",
      data: JSON.stringify(generateDataForLeadSend()),
      dataType: "json",
      contentType: "application/json",
      success: function (response) {
        hideLoader();
        console.log(response);
        callback(response)
        //exit popup or show message
      },
      error: function (jqXHR, textStatus, errorThrown) {
        hideLoader();
        //exit popup or show message
      },
    });
  }

  function generateDataForClientSend() {
    let data = { additional_data: {}, additional_contacts: [] };
    $("#fields input,#fields textarea,#fields select")
      .not("#contactsWrapper input,#contactsWrapper select")
      .not(":radio")
      .each(function () {
        if (
          $(this).attr("id") == "fType" ||
          $(this).attr("id") == "submitForm"
        ) {
          return;
        }
        if (
          $(this).attr("id") == "addComment" ||
          $(this).attr("id") == "apartment" ||
          $(this).attr("id") == "birthday" ||
          $(this).attr("id") == "city" ||
          $(this).attr("id") == "fname" ||
          $(this).attr("id") == "id" ||
          $(this).attr("id") == "lname" ||
          $(this).attr("id") == "mailMailing" ||
          $(this).attr("id") == "mailbox" ||
          $(this).attr("id") == "number" ||
          $(this).attr("id") == "pemail" ||
          $(this).attr("id") == "phoneZone" ||
          $(this).attr("id") == "pphone" ||
          $(this).attr("id") == "smsMailing" ||
          $(this).attr("id") == "street" ||
          $(this).attr("id") == "userDucomentation" ||
          $(this).attr("id") == "zip"
        ) {
          if ($(this).attr("type") == "checkbox") {
            data[$(this).attr("id")] = $(this).prop("checked") ? 1 : 0;
          } else {
            data[$(this).attr("id")] = $(this).val();
          }
        } else {
          if ($(this).attr("type") == "checkbox") {
            data.additional_data[$(this).attr("data-name")] = $(this).prop(
              "checked"
            )
              ? 1
              : 0;
          } else {
            data.additional_data[$(this).attr("data-name")] = $(this).val();
          }
        }
      });
    $("#fields input[type='radio']:checked").each(function () {
      if ($(this).attr("id") == "male") {
        data["gender"] = 0;
        return;
      }
      if ($(this).attr("id") == "female") {
        data["gender"] = 1;
        return;
      }
      if ($(this).attr("id") == "else") {
        data["gender"] = 2;
        return;
      }
      data.additional_data[$(this).attr("name")] = $(this).val();
    });
    $("#fields #contactsWrapper .repeat").each(function () {
      let ContactData = {};
      $(this)
        .find("input, select")
        .each(function () {
          if ($(this).hasClass("contactName")) {
            ContactData.name = $(this).val();
            return;
          }
          if ($(this).hasClass("contactRelative")) {
            ContactData.relative = $(this).val();
            return;
          }
          if ($(this).hasClass("contactPhone")) {
            ContactData.phone = $(this).val();
            return;
          }
          if ($(this).hasClass("contactPhoneZone")) {
            ContactData.phoneZone = $(this).val();
            return;
          }
          if ($(this).hasClass("contactEmail")) {
            ContactData.email = $(this).val();
            return;
          }
        });
      data.additional_contacts.push(ContactData);
    });
    return data;
  }

  function generateDataForLeadSend() {
    let data = { additional_data: {}, interestsName: [], interestsIds: [] };
    $("#fields input,#fields textarea,#fields select")
      .not(":radio")
      .each(function () {
        if (
          $(this).attr("id") == "fType" ||
          $(this).attr("id") == "submitForm"
        ) {
          return;
        }
        if (
          $(this).attr("id") == "fname" ||
          $(this).attr("id") == "lname" ||
          $(this).attr("id") == "pphone" ||
          $(this).attr("id") == "phoneZone" ||
          $(this).attr("id") == "pemail" ||
          $(this).attr("id") == "leadStatus" ||
          $(this).attr("id") == "leadSrc" ||
          $(this).attr("id") == "interestedIn"
        ) {
          if ($(this).attr("type") == "checkbox") {
            data[$(this).attr("id")] = $(this).prop("checked") ? 1 : 0;
          } else {
            if ($(this).attr("id") == "interestedIn") {
              if ($(this).val() != "") {
                const parsedData = JSON.parse($(this).val());
                parsedData.forEach((interestData) => {
                  data.interestsName.push(interestData.value);
                  data.interestsIds.push(interestData.id);
                });
              } else {
                data.interestsName.push("כל השיעורים");
                data.interestsIds.push("BA999");
              }
            } else {
              data[$(this).attr("id")] = $(this).val();
            }
          }
        } else {
          if ($(this).attr("type") == "checkbox") {
            data.additional_data[$(this).attr("data-name")] = $(this).prop(
              "checked"
            )
              ? 1
              : 0;
          } else {
            data.additional_data[$(this).attr("data-name")] = $(this).val();
          }
        }
      });
    $("#fields input[type='radio']:checked").each(function () {
      data.additional_data[$(this).attr("name")] = $(this).val();
    });
    return data;
  }
});
