


// reCAPTCHA helper
jQuery.fn.extend({
    showModalLoader: function (){
        $(this).find('.modal-content').prepend(
            $(
                '    <div class="bsapp-overlay-loader js-loader d-flex">' +
                '        <div class="spinner-border text-primary" role="status">' +
                '            <span class="sr-only">'+ lang('loading_datatables') +'?></span>' +
                '        </div>' +
                '    </div>'
            )
        )
    },
    hideModalLoader: function (){
        $(this).find('.modal-content').find(".js-loader").remove()
    }
})

BeePOS.recaptcha = {
    html: function () {
        var template = $('#recaptchaTemplate');

        return template.length ? template.html() : '';
    },

    get: function () {
        if (BeePOS.recaptcha.html() == '')
            return;

        var protocol = window.location.protocol == 'https:' ? 'https' : 'http';
        var public_key = $('#recaptcha_public_key').val();

        $.getScript(protocol + '://www.google.com/recaptcha/api/js/recaptcha_ajax.js', function () {
            Recaptcha.create(public_key, 'recaptcha_widget', {
                theme: 'custom',
                custom_theme_widget: 'recaptcha_widget'
            });
        });
    }
};

function validate(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault)
            theEvent.preventDefault();
    }
}

function validateMobile() {
    var mobile = document.getElementById("contactMobile").value;
    var mobileRegex = /^\s*0?\s*(5\s*[0|1|2|3|4|5|8|9])(\s*\-\s*)?[1-9]\s*\d{1}\s*\d{1}\s*\d{1}\s*\d{1}\s*\d{1}\s*\d{1}\s*$/;

}


jQuery(function ($) {
    // Render the reCAPTCHA input and image
    $('#reminderModal, #activationModal, #signupModal').on('show.bs.modal', function (e) {
        $('.modal .recaptcha').html('');
        $(e.currentTarget).find('.recaptcha').html(BeePOS.recaptcha.html());
        BeePOS.recaptcha.get();
    });

    // Clear the hash when the reset and activation modals are closing
    $('#resetModal, #activateModal').on('hide.bs.modal', function () {
        window.location.hash = '';
    });

    $('.avatar-container select').on('change', function () {
        $.get(BeePOS.options.ajaxUrl, {action: 'avatarPreview', type: $(this).val()}, function (response) {
            if (response.success)
                $('.avatar-image').attr('src', response.message);
        }, 'json');
    });

    $('#settingsModal a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var modal = $('#settingsModal');
        var action = $(e.target).attr('href').replace('#', '');

        modal.find('form').attr('action', action != 'connectTab' ? action : '');

        modal.find('.alert').hide();

        if (action == 'settingsMessages') {
            $.get(BeePOS.options.ajaxUrl, {action: 'getContacts'}, function (response) {
                if (response.success) {
                    var list = modal.find('.contact-list');
                    list.html('');

                    for (var i = 0; i < response.message.length; i++) {
                        list.append(tmpl('contactItemTemplate', response.message[i]));
                    }

                }
            }, 'json');
        }
    });

    $('.ajax-form').on('click', '.social-connect a', function (e) {
        BeePOS.alert(BeePOS.trans('connecting') + $(this).text() + '...', 0, $(e.delegateTarget));
    });

    // Open password reset and activation modals if we
    // found a reminder in the hash. Eg: #reset-123456
    var hash = window.location.hash;
    switch (hash.substr(1, hash.indexOf('-') - 1)) {
        case 'reset':
            var modal = $('#resetModal');
            modal.find('[name="reminder"]').val(hash.substr(hash.indexOf('-') + 1, hash.length));
            modal.modal('show');
            break;

        case 'activate':
            var modal = $('#activateModal');
            modal.find('[name="reminder"]').val(hash.substr(hash.indexOf('-') + 1, hash.length));
            modal.modal('show');
            modal.on('shown.bs.modal', function () {
                modal.find('form').trigger('submit');
            });
            break;

        case 'settings':
            var modal = $('#settingsModal');
            modal.modal('show');
            modal.find('a[href="#connectTab"]').tab('show');

            window.location.hash = '';
            break;
    }
});

// Register ajaxForm callbacks

BeePOS.ajaxFormCb.login = function (message) {
    if (message.length)
        window.location.href = message;
    else
        window.location.reload();
};

BeePOS.ajaxFormCb.signup = function (message) {
    if (message != null) {
        if (message.redirect.length)
            window.location.href = message.redirect;
        else
            window.location.reload();
    } else if ($('#signupModal').css('display') == 'block') {
        $('#signupSuccessModal').modal('show');
    }
};

BeePOS.ajaxFormCb.activation = function () {
    if ($('#activationModal').css('display') == 'block')
        $('#activationSuccessModal').modal('show');
    else
        window.location.reload();
};

BeePOS.ajaxFormCb.activate = function () {
    $('#activateSuccessModal').modal('show');
};

BeePOS.ajaxFormCb.reminder = function () {
    if ($('#reminderModal').css('display') == 'block')
        $('#reminderSuccessModal').modal('show');
    else
        window.location.reload();
};

BeePOS.ajaxFormCb.reset = function () {
    if ($('#resetModal').css('display') == 'block')
        $('#resetSuccessModal').modal('show');
    else
        window.location.href = window.location.origin + window.location.pathname;
};

BeePOS.ajaxFormCb.settingsAccount =
        BeePOS.ajaxFormCb.settingsProfile =
        BeePOS.ajaxFormCb.settingsMessages = function (m, form) {
            BeePOS.alert(BeePOS.trans('changes_saved'), 1, form);
        };

BeePOS.ajaxFormCb.settingsPassword = function (m, form) {
    form.find('input').val('');
    BeePOS.alert(BeePOS.trans('pass_changed'), 1, form);
};

BeePOS.ajaxFormCb.webmasterContact = function (m, form) {
    form.find('[name="message"]').val('');

    BeePOS.alert(BeePOS.trans('message_sent'), 1, form);
};

BeePOS.ajaxFormCb.POSCancel = function (m, form) {
    form.find('[name="TempsId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $('#GetItems').load('Clean.php' + '#MeItem');
    parent.location.href = "index.php";


};


BeePOS.ajaxFormCb.AddDepartments = function (m, form) {
    form.find('[name="DepartmentName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddMotag = function (m, form) {
    form.find('[name="MotagName"]').val('');
    form.find('[name="MotagSite"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AddSupplierType = function (m, form) {
    form.find('[name="SupplierTypeName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddCity = function (m, form) {
    form.find('[name="City"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AddItemPopUp = function (m, form) {
    form.find('[name="Client"]').val('');
    form.find('[name="ItemPrice"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.AddItemPopUp = function (message, form) {
    form.find('input:text, input:password, input:file, select, textarea').val('');
    form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    form.find('[name="Client"]').select2("val", "");

};



BeePOS.ajaxFormCb.AddItem = function (m, form) {
    form.find('[name="Department"]').val('');
    form.find('[name="ItemName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = 'ManagePOSItem.php';
};

BeePOS.ajaxFormCb.EditItem = function (m, form) {
    form.find('[name="Department"]').val('');
    form.find('[name="ItemName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = 'ManagePOSItem.php';

};

BeePOS.ajaxFormCb.AddDepartmentSub = function (m, form) {
    form.find('[name="DepartmentName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddDepartmentSubs = function (m, form) {
    form.find('[name="DepartmentName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


// Update Department
function UpdateDepartments(DepartmentId) {

    var modal = $('#DepartmentsEditPopup');

    var selectval = DepartmentId;

    $.ajax({
        url: 'Action/updateDepatment.php',
        type: 'POST',
        data: {DepartmentId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="DepartmentId"]').val(DepartmentId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditDepartments = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Sub Department
function UpdateDepartmentSub(DepartmentId) {

    var modal = $('#DepartmentsEditSubPopup');

    var selectval = DepartmentId;

    $.ajax({
        url: 'Action/updateDepatmentSub.php',
        type: 'POST',
        data: {DepartmentId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="DepartmentId"]').val(DepartmentId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.SubDepartmentSub = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

// Update Sub Sub Department
function UpdateDepartmentSubs(DepartmentId) {

    var modal = $('#DepartmentsEditSubsPopup');

    var selectval = DepartmentId;

    $.ajax({
        url: 'Action/updateDepatmentSubs.php',
        type: 'POST',
        data: {DepartmentId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="DepartmentId"]').val(DepartmentId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditDepartmentSubs = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddSize = function (m, form) {
    form.find('[name="SizeName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

// Update Sub Sub Department
function UpdateSize(SizeId) {

    var modal = $('#SizeEditPopup');

    var selectval = SizeId;

    $.ajax({
        url: 'Action/updateSize.php',
        type: 'POST',
        data: {SizeId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="SizeId"]').val(SizeId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSize = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddSizeSub = function (m, form) {
    form.find('[name="SizeName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

// Update Sub Sub Department
function UpdateSizeSub(SizeId) {

    var modal = $('#SizeSubEditPopup');

    var selectval = SizeId;

    $.ajax({
        url: 'Action/updateSizeSub.php',
        type: 'POST',
        data: {SizeId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="SizeId"]').val(SizeId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSizeSub = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddColor = function (m, form) {
    form.find('[name="ColorName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

// Update Sub Sub Department
function UpdateColor(ColorId) {

    var modal = $('#ColorEditPopup');

    var selectval = ColorId;

    $.ajax({
        url: 'Action/updateColor.php',
        type: 'POST',
        data: {ColorId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ColorId"]').val(ColorId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditColor = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddColorSub = function (m, form) {
    form.find('[name="ColorName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

// Update Sub Sub Department
function UpdateColorSub(ColorId) {

    var modal = $('#ColorSubEditPopup');

    var selectval = ColorId;

    $.ajax({
        url: 'Action/updateColorSub.php',
        type: 'POST',
        data: {ColorId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ColorId"]').val(ColorId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditColorSub = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Motag
function UpdateMotag(MotagId) {

    var modal = $('#MotagEditPopup');

    var selectval = MotagId;

    $.ajax({
        url: 'action/updateMotag.php',
        type: 'POST',
        data: {MotagId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="MotagId"]').val(MotagId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditMotag = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update SupplierType
function UpdateSupplierType(TypeId) {

    var modal = $('#SupplierTypeEditPopup');

    var selectval = TypeId;

    $.ajax({
        url: 'Action/updateSupplierType.php',
        type: 'POST',
        data: {TypeId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="TypeId"]').val(TypeId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSupplierType = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update City
function UpdateCity(CityId) {

    var modal = $('#CityEditPopup');

    var selectval = CityId;

    $.ajax({
        url: 'Action/updateCity.php',
        type: 'POST',
        data: {CityId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="CityId"]').val(CityId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditCity = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

/// Add Task


var dateFormat = function () {
    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
            timezoneClip = /[^-+\dA-Z]/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len)
                    val = "0" + val;
                return val;
            };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date;
        if (isNaN(date))
            throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
                d = date[_ + "Date"](),
                D = date[_ + "Day"](),
                m = date[_ + "Month"](),
                y = date[_ + "FullYear"](),
                H = date[_ + "Hours"](),
                M = date[_ + "Minutes"](),
                s = date[_ + "Seconds"](),
                L = date[_ + "Milliseconds"](),
                o = utc ? 0 : date.getTimezoneOffset(),
                flags = {
                    d: d,
                    dd: pad(d),
                    ddd: dF.i18n.dayNames[D],
                    dddd: dF.i18n.dayNames[D + 7],
                    m: m + 1,
                    mm: pad(m + 1),
                    mmm: dF.i18n.monthNames[m],
                    mmmm: dF.i18n.monthNames[m + 12],
                    yy: String(y).slice(2),
                    yyyy: y,
                    h: H % 12 || 12,
                    hh: pad(H % 12 || 12),
                    H: H,
                    HH: pad(H),
                    M: M,
                    MM: pad(M),
                    s: s,
                    ss: pad(s),
                    l: pad(L, 3),
                    L: pad(L > 99 ? Math.round(L / 10) : L),
                    t: H < 12 ? "a" : "p",
                    tt: H < 12 ? "am" : "pm",
                    T: H < 12 ? "A" : "P",
                    TT: H < 12 ? "AM" : "PM",
                    Z: utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                    o: (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                    S: ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();

// Some common format strings
dateFormat.masks = {
    "default": "ddd mmm dd yyyy HH:MM:ss",
    shortDate: "m/d/yy",
    mediumDate: "mmm d, yyyy",
    longDate: "mmmm d, yyyy",
    fullDate: "dddd, mmmm d, yyyy",
    shortTime: "h:MM TT",
    mediumTime: "h:MM:ss TT",
    longTime: "h:MM:ss TT Z",
    isoDate: "yyyy-mm-dd",
    isoTime: "HH:MM:ss",
    isoDateTime: "yyyy-mm-dd'T'HH:MM:ss",
    isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
    dayNames: [
        "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
        "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
        "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
    return dateFormat(this, mask, utc);
};



function AddTaskModaljs(dates) {


    var modal = $('#AddTaskModal');


    today = new Date(dates);

    startTime = new Date(today.getTime() + today.getTimezoneOffset() * 60000);
    var dateString = today.format("yyyy-mm-dd");
    var timeString = startTime.format("HH:mm");

    var myDate = startTime.toLocaleTimeString();

    //alert(myDate);

    modal.find('input[name="StartDate"]').val(dateString);
    modal.find('input[name="EndDate"]').val(dateString);
    modal.find('input[name="StartTime"]').val(myDate);

    modal.find('input[name="EndTime"]').val(myDate);
    // modal.find('.listing').text(listingname);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.addtaskpopup = function () {
        location.reload();
        modal.modal('hide');
    };
}
;



function AddDeskModaljs(dates, resourceObj) {


    var modal = $('#AddTaskModal');


    today = new Date(dates);

    startTime = new Date(today.getTime() + today.getTimezoneOffset() * 60000);
    var dateString = today.format("yyyy-mm-dd");
    var timeString = startTime.format("HH:mm");

    var myDate = startTime.toLocaleTimeString();

    startTimes = new Date(today.getTime());

    var myDates = startTimes.toLocaleTimeString();

    //alert(myDate);

    modal.find('input[name="StartDate"]').val(dateString);
    modal.find('input[name="EndDate"]').val(dateString);
    modal.find('input[name="StartTime"]').val(myDate);
    modal.find('input[name="resourceId"]').val(resourceObj);

    //	 modal.find('input[name="EndTime"]').val(myDates);
    // modal.find('.listing').text(listingname);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.addDeskpopup = function () {
        window.location.href = "DeskPlanNew.php";
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.AddSupplier = function (m, form) {
    form.find('[name="CompanyName"]').val('');
    form.find('[name="BusinessType"]').val('');
    form.find('[name="CompanyId"]').val('');
    form.find('[name="JobsRole1"]').val('');
    form.find('[name="ContactName1"]').val('');
    form.find('[name="ContactMobile1"]').val('');
    form.find('[name="PaymentRole"]').val('');
    form.find('[name="SupplierType"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = "Supplier.php";
    //location.reload() ;

};


BeePOS.ajaxFormCb.EditSupplier = function (m, form) {
    form.find('[name="BusinessType"]').val('');
    form.find('[name="PaymentRole"]').val('');
    form.find('[name="SupplierType"]').val('');

    var value = form.find('[name="Tab"]').val();

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //parent.location.href = value;
    location.reload();

};

BeePOS.ajaxFormCb.AddSupplierContact = function (m, form) {
    form.find('[name="JobsRole"]').val('');
    form.find('[name="ContactName"]').val('');
    form.find('[name="ContactMobile"]').val('');

    var value = form.find('[name="Tab"]').val();

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //window.location.href = value;
    location.reload();

};

// Del Supplier Contact
function DelSupplierContacts(ContactId) {

    var modal = $('#DelSupplierContactPopup');

    modal.find('input[name="ContactId"]').val(ContactId);
    modal.modal('show');



    BeePOS.ajaxFormCb.DelSupplierContact = function () {

        //window.location.href = value;
        location.reload();
        modal.modal('hide');
    };
}
;

// Edit Supplier Contact
function UpdateSupplierContacts(ContactId) {

    var modal = $('#EditSupplierContactPopup');

    var selectval = ContactId;

    $.ajax({
        url: 'Action/updateSupplierContact.php',
        type: 'POST',
        data: {ContactId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ContactIds"]').val(ContactId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSupplierContact = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


/// Clients

BeePOS.ajaxFormCb.AddClient = function (m, form) {
    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="ContactEmail"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddClient = function (message, form) {
    window.location.href = message.redirect;

};


BeePOS.ajaxFormCb.EditClient = function (m, form) {
    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="ContactEmail"]').val('');

    var value = form.find('[name="Tab"]').val();

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //parent.location.href = value;
    location.reload();

};

BeePOS.ajaxFormCb.AddNewContactClient = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ContactName"]').val('');
    form.find('[name="ContactMobile"]').val('');

    var value = form.find('[name="Tab"]').val();

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //window.location.href = value;
    location.reload();

};

// Del Supplier Contact
function DelClientContacts(ContactId) {

    var modal = $('#DelClientContactPopup');

    modal.find('input[name="ContactId"]').val(ContactId);
    modal.modal('show');



    BeePOS.ajaxFormCb.DelClientContact = function () {

        //window.location.href = value;
        location.reload();
        modal.modal('hide');
    };
}
;

// Edit Supplier Contact
function UpdateClientContacts(ContactId) {

    var modal = $('#EditClientContactPopup');

    var selectval = ContactId;

    $.ajax({
        url: 'action/updateClientContact.php',
        type: 'POST',
        data: {ContactId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultUpdateClientContacts').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ContactIds"]').val(ContactId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditClientContact = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

// Compose E-mail
BeePOS.admin.composeEmail = function (email) {
    var modal = $('#composeModal');

    if (email)
        modal.find('input[name="to"]').val(email);

    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.sendEmail = function () {
        modal.modal('hide');
    };
};

BeePOS.ajaxFormCb.EditSettings = function (m, form) {
    form.find('[name="ContactEmail"]').val('');
    form.find('[name="ContactMobile"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.addtech = function (m, form) {
    form.find('[name="username"]').val('');
    form.find('[name="email"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.edittech = function (m, form) {
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


// Update Item
function UpdateItemsPopup(ItemId, Type) {

    var modal = $('#EditItemsPopup');

    var selectval = ItemId;
    var selectType = Type;

    $.ajax({
        url: 'Action/UpdateItemsPopup.php',
        type: 'POST',
        data: {ItemId: selectval, Type: selectType},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditItemPopUp = function () {
        modal.modal('hide');
    };
}
;

// Del Item 
function DelItemsPopup(ItemId, Type) {

    var modal = $('#DelItemsPopup');

    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');



    BeePOS.ajaxFormCb.DelItemPopUp = function () {
        modal.modal('hide');
    };
}
;



// Del Item 
function CheckSavePopups() {

    var modal = $('#CheckSavePopup');

    modal.modal('show');

    BeePOS.ajaxFormCb.CheckSave = function () {
        modal.modal('hide');
        //window.onbeforeunload = null;
    };
}
;


/// חשבוניות ספק	

BeePOS.ajaxFormCb.SupplierInvoice = function (m, form) {
    form.find('[name="Supplier"]').val('');
    form.find('[name="SupplierInvoiceId"]').val('');
    form.find('[name="SupplierDates"]').val('');
    form.find('[name="Details1"]').val('');
    form.find('[name="Price1"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.SupplierInvoice = function (message, form) {
    window.location.href = message.redirect;

};

BeePOS.ajaxFormCb.EditSupplierInvoice = function (m, form) {
    form.find('[name="SupplierInvoiceId"]').val('');
    form.find('[name="SupplierDates"]').val('');
    form.find('[name="Details1"]').val('');
    form.find('[name="Price1"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.EditSupplierInvoice = function (message, form) {
    window.location.href = message.redirect;

};


//// 


BeePOS.ajaxFormCb.AddDiscountDoc = function (m, form) {
    form.find('[name="TempsIdDiscount"]').val('');
    var TempId = document.getElementById('TempId').value;
    var TypeDoc = document.getElementById('TypeDoc').value;
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $("#TempsIdDiscount").val(TempId);
    var url = "UpdatesItems.php?ActTemp=" + TempId + "&TypeDoc=" + TypeDoc;
    $("#GetItems").load(url + '#MeItem');
    $('#Discounts').val('0');


    $("#DiscountPopup").fadeOut("fast");

};

BeePOS.ajaxFormCb.AddVatDoc = function (m, form) {
    form.find('[name="TempsIdVat"]').val('');
    var TempId = document.getElementById('TempId').value;
    var TypeDoc = document.getElementById('TypeDoc').value;
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $("#TempsIdVat").val(TempId);
    var url = "UpdatesItems.php?ActTemp=" + TempId + "&TypeDoc=" + TypeDoc + "&TempId=" + TempId;
    $("#GetItems").load(url + '#MeItem');
    $("#VatPopup").fadeOut("fast");

};


var spinnerVisible = false;
function showProgress() {
    if (!spinnerVisible) {
        $("div#spinner").fadeIn("fast");
        spinnerVisible = true;
    }
}
;
function hideProgress() {
    if (spinnerVisible) {
        var spinner = $("div#spinner");
        spinner.stop();
        spinner.fadeOut("fast");
        spinnerVisible = false;
    }
}
;

/// הוספת מסמכים במערכת

BeePOS.ajaxFormCb.AddDocs = function (m, form) {
    form.find('[name="Client"]').val('');
    form.find('[name="Dates"]').val('');
    form.find('[name="TypeDoc"]').val('');
    form.find('[name="DocTempId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddDocs = function (message, form) {
    window.location.href = message.redirect;

};

// Update Task
function UpdateDesk(TaskId, DateUrl) {

    var modal = $('#EditTaskModal');

    var modalold = $('#DetailsPopup');
    modalold.modal('hide');
    var selectval = TaskId;

    $.ajax({
        url: 'action/updateDesk.php',
        type: 'POST',
        data: {TaskId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="TaskId"]').val(TaskId);
    modal.modal('show');

    var DateUrl = DateUrl;

    // Register ajax form callback
    BeePOS.ajaxFormCb.editDeskpopup = function () {


        window.location.href = "DeskPlanNew.php";
        // location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.AddPaymentPage = function (m, form) {
    form.find('[name="Title"]').val('');
    form.find('[name="TitleUrl"]').val('');
    form.find('[name="ItemId"]').val('');
    form.find('[name="Amount"]').val('');
    form.find('[name="MaxPaymentRegular"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = "ManagePayment.php";
    //location.reload() ;

};

BeePOS.ajaxFormCb.EditPaymentPage = function (m, form) {
    form.find('[name="Title"]').val('');
    form.find('[name="TitleUrl"]').val('');
    form.find('[name="ItemId"]').val('');
    form.find('[name="Amount"]').val('');
    form.find('[name="MaxPaymentRegular"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = "ManagePayment.php";
    //location.reload() ;

};


BeePOS.ajaxFormCb.AddItems = function (m, form) {
    form.find('[name="Membership"]').val('');
    form.find('[name="membership_type"]').val('');
    form.find('[name="ItemName"]').val('');
    form.find('[name="ItemPrice"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "Items.php";
    location.reload();

};

BeePOS.ajaxFormCb.AddCoupon = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddStatus = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

BeePOS.ajaxFormCb.AddPipelineReasons = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};


BeePOS.ajaxFormCb.AddSource = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};


BeePOS.ajaxFormCb.AddSavedMsg = function (m, form) {

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};



BeePOS.ajaxFormCb.EditMyProfile = function (m, form) {
    form.find('[name="AffId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};



BeePOS.ajaxFormCb.EditMyBank = function (m, form) {
    form.find('[name="AffId"]').val('');
    form.find('[name="FullName"]').val('');
    form.find('[name="AffPassport"]').val('');
    form.find('[name="BankId"]').val('');
    form.find('[name="BranchId"]').val('');
    form.find('[name="BankNumber"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};






// Update Department
function UpdateItems(ItemId) {

    var modal = $('#ItemsEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'Action/updateItems.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditItems = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

// Update Department
function UpdateStatus(ItemId) {

    var modal = $('#StatusEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateStatus.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditStatus = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Department
function UpdatePipeline(ItemId) {

    var modal = $('#PipelineEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updatePipeline.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditPipeline = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Department
function UpdatePipelineReasons(ItemId) {

    var modal = $('#ReasonsEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updatePipelineReasons.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditPipelineReasons = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Department
function UpdateSource(ItemId) {

    var modal = $('#SourceEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateSource.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSource = function () {

        location.reload();
        modal.modal('hide');
    };
}
;







function UpdateSavedMsg(ItemId) {

    var modal = $('#MsgEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateSavedMsg.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSavedMsg = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


function UpdateSavedNot(ItemId) {

    var modal = $('#NotEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateSavedNot.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSavedNot = function () {

        location.reload();
        modal.modal('hide');
    };
}
;




BeePOS.ajaxFormCb.AddSteps = function (m, form) {
    form.find('[name="NumPayment"]').val('');
    form.find('[name="Amount"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "Items.php";
    location.reload();

};

// Update Department
function UpdateSteps(ItemId) {

    var modal = $('#StepsEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateSteps.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSteps = function () {

        location.reload();
        modal.modal('hide');
    };
}
;



// Update Coupon (Yosi)
function UpdateCoupon(ItemId) {

    var modal = $('#CouponEditPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateCoupon.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditCoupon = function () {

        location.reload();
        modal.modal('hide');
    };
}
;






BeePOS.ajaxFormCb.AddCRM = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="Remarks"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();
};

BeePOS.ajaxFormCb.AddReminder = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="Remarks"]').val('');
    form.find('[name="Reminder"]').val('');



    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


// Update Reminder
function UpdateReminder(ReminderId) {

    var modal = $('#ReminderEditPopup');

    var selectval = ReminderId;

    $.ajax({
        url: 'Action/updateReminder.php',
        type: 'POST',
        data: {ReminderId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultTask').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ReminderId"]').val(ReminderId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditReminder = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Token
function UpdateToken(TokenId) {

    var modal = $('#TokenEditPopup');

    var selectval = TokenId;



    $.ajax({
        url: 'action/updateToken.php',
        type: 'POST',
        data: {TokenId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultToken').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="TokenId"]').val(TokenId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditToken = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


// Update Pay Token
function UpdatePayToken(TokenId, newApi = false) {

    var url;
    if (newApi == false) {
        url = 'action/updatePayToken.php';
    } else {
        url = 'action/updatePaymentNewApi.php';
    }
    var modal = $('#PayTokenEditPopup');

    modal.on('hide.bs.modal', function (){
        $('.popover').popover('dispose');
    });

    $('#ShowSaveKeva').hide();
    var selectval = TokenId;

    $.ajax({
        url: url,
        type: 'POST',
        data: {TokenId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultPayToken').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="PayTokenId"]').val(TokenId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditPayToken = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.EditAff = function (m, form) {
    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');
    form.find('[name="ContactMobile"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


// Update Seller
function UpdateSaller(Id) {

    var modal = $('#SallerEditPopup');

    var selectval = Id;

    $.ajax({
        url: 'action/updateSaller.php',
        type: 'POST',
        data: {Id: selectval},
        success: function (data) {
            //alert(data);
            $('#resultSaller').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="SallerId"]').val(Id);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSaller = function () {

        modal.modal('hide');
    };
}
;




// ViewCallsLog
function ViewCallsLog(Id) {

    var modal = $('#ViewCallsLogPOPUP');

    var selectval = Id;

    $.ajax({
        url: 'Action/ViewCallsLog.php',

        type: 'POST',
        data: {Id: selectval},
        success: function (data) {
            $('#resultViewCallsLog').html(data);
        }
    });

    modal.find('input[name="ClientId"]').val(Id);
    modal.modal('show');
    // Register ajax form callback
    BeePOS.ajaxFormCb.AddCRMPP = function () {
        modal.modal('hide');
    };
}
;


// ViewTaskLog
function ViewTaskLog(Id) {

    var modal = $('#ViewTaskLogPOPUP');

    var selectval = Id;

    $.ajax({
        url: 'Action/ViewTaskLog.php',
        type: 'POST',
        data: {Id: selectval},
        success: function (data) {
            //alert(data);
            $('#resultViewTaskLog').html(data);

        }
    });
    modal.find('input[name="ClientId"]').val(Id);
    modal.find('[name="Remarks"]').val('');
    modal.find('[name="Reminder"]').val('');
    modal.modal('show');



    BeePOS.ajaxFormCb.AddReminderPP = function () {
        modal.modal('hide');
    };


}
;

// ViewTaskLog
function ViewInfoLog(Id) {

    var modal = $('#ViewInfoLogPOPUP');

    var selectval = Id;

    $.ajax({
        url: 'Action/ViewInfoLog.php',
        type: 'POST',
        data: {Id: selectval},
        success: function (data) {
            //alert(data);
            $('#resultViewInfoLog').html(data);

        }
    });

    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSaller = function () {

        modal.modal('hide');
    };
}
;





BeePOS.ajaxFormCb.sendEmail = function (message, form) {
    $('#emailmessage').summernote("code", '');
    $('#emailsubject').val('');
};


BeePOS.ajaxFormCb.AddPayToken = function (m, form) {
    form.find('[name="Amount"]').val('');
    form.find('[name="NumPayment"]').val('');
    form.find('[name="NextPayment"]').val('');
    form.find('[name="TokenId"]').val('');
    form.find('[name="ClientId"]').val('');
    form.find('[name="PageId"]').val('');




    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.POSCancelPayments = function (m, form) {

    form.find('[name="ClientId"]').val('');
    form.find('[name="TempListsId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.POSCancelPayments = function (message, form) {

    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;
    var TypeDoc = message.TypeDoc;
    var Finalinvoicenum = message.Finalinvoicenum;
    var TrueFinalinvoicenum = message.TrueFinalinvoicenum;
    if (StatusNew == '1') {
        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TempId=" + TempIdNew + "&CheckRefresh=2&TypeDoc=" + TypeDoc + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
        $(".CancelPaymentsClose").trigger("click");

        let paymentsTable = $('#DocsPayments').find('table').find('tbody');
        if(paymentsTable.find('tr').length > 1) {
            $('#ReceiptBtn').attr("disabled", false);
        } else {
            $('#ReceiptBtn').attr("disabled", true);
        }

    } else {
        BN('1', Status);
    }


};


BeePOS.ajaxFormCb.AddCalendarClient = function (message, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="SetDate"]').val('');
    form.find('[name="SetTime"]').val('');
    form.find('[name="SetToTime"]').val('');
    form.find('[name="TypeOption"]').val('');


    BeePOS.alert(message, 1, form);

    var CalPage = $('#CalPage').val();
    var modal = $('#AddNewTask');
    if (CalPage == '0') {
        location.reload();
    } else {
        modal.modal('hide');
        $('#FormCalendarClient').trigger("reset");
        form.find('.alert').hide();
        if ($('#FormCalendarClient').hasClass('dashboardTasks')) {
            $('#ChooseClientForTask').val('0').trigger('change');

        } else {
            scheduler.load("new/data/events.php");
            $('#ChooseClientForTask').val('0').trigger('change');
        }
    }
};


BeePOS.ajaxFormCb.GeneralSettingsPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.ContactInfoPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');
    form.find('[name="Email"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="Cities"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.DocsRemakrsPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AccountManagerPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.DesignDocumentLog = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.VoiceCenterPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.DocsNumPage = function (m, form) {
    form.find('[name="CompanyNum"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.POSCancelDocs = function (m, form) {
    form.find('[name="TempIdPOSCancelDocs"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
};

BeePOS.ajaxFormCb.POSCancelDocs = function (message, form) {
    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;

    if (StatusNew == '1') {
        location.reload();
    } else {
        $("#POSCancelDocsError").css("display", "block");
        $('#POSCancelDocsErrorText').html(Status);
        $("#CancelDocs_TempsId").val(TempIdNew);
    }


};






//Chat Send
BeePOS.ajaxFormCb.ChatSend = function (m, form) {
    form.find('[name="Content"]').val('');
    var UserId = form.find('[name="UserId"]').val();

    $(".loading").css("display", "block");
    //location.reload();

    var url = 'action/ChatBox.php?U=' + UserId;
    $('#ChatBoxAjax').load(url, function (e) {
        $('#ChatBoxAjax .ajax-form').on('submit', BeePOS.ajaxForm);
        $('#SendTrueChat').focus();
        return false;
    });


    $.ajax({
        url: 'action/ChatUsers.php?U=' + UserId,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ChatUsers').html(data);
            myFunction()
        }
    });

};
//Chat Send




//QuickUpdate :: ClientProfile :: Email
BeePOS.ajaxFormCb.EditClientQuickemail = function (m, form) {
    $("#EditClientQuick-email").hide();
    $("#email1").val($('#email1').val());
    $("#email").text($('#email1').val());
    $("#email").show();
    $("#ContactEmail").val($('#email1').val());
    FixSmsEmailForm();
    $(".profileimage").attr('src', 'https://pikmail.herokuapp.com/' + $('#email1').val() + '?size=85');
    $(".profileimage").attr('onerror', "this.src='../assets/img/21122016224223511960489675402.png'");
    $('#profileimageiframe').attr('onClick', "window.parent.TINY.box.show({image:'https://pikmail.herokuapp.com/" + $('#email1').val() + "?size=500', fixed:true,boxid:'frameless',animate:true})");
    $('.profileimage').on("error", function () {
        $('#profileimageiframe').attr('onClick', "window.parent.TINY.box.show({image:'../assets/img/21122016224223511960489675402.png', fixed:true,boxid:'frameless',animate:true})");
    });
};
//END QuickUpdate :: ClientProfile :: Email


//QuickUpdate :: ClientProfile :: Phone
BeePOS.ajaxFormCb.EditClientQuickphone = function (m, form) {
    $("#EditClientQuick-phone").hide();
    $("#phone1").val($('#phone1').val());
    $("#phone").text($('#phone1').val());
    $("#phone").show();
    $("#ContactMobile").val($('#phone1').val());
    FixSmsEmailForm();
};
//END QuickUpdate :: ClientProfile :: Phone


//QuickUpdate :: ClientProfile :: CompanyId
BeePOS.ajaxFormCb.EditClientQuickcompanyid = function (m, form) {
    $("#EditClientQuick-companyid").hide();
    $("#companyid1").val($('#companyid1').val());
    $("#companyid").text($('#companyid1').val());
    $("#companyid").show();
    $("#CompanyId").val($('#companyid1').val());
};
//END QuickUpdate :: ClientProfile :: CompanyId


//QuickUpdate :: ClientProfile :: CompanyName
BeePOS.ajaxFormCb.EditClientQuickcompanyname = function (m, form) {
    $("#EditClientQuick-companyname").hide();
    $("#companyname1").val($('#companyname1').val());
    $("#companyname").text($('#companyname1').val());
    $(".CompanyNameChange").text($('#companyname1').val());
    $("#companyname").show();

    var ret = $('#companyname1').val().split(" ");
    var firstName = ret[0];
    var lastName = $('#companyname1').val().split(' ').slice(1).join(' ');

    $('#FirstName').val(firstName);
    $('#LastName').val(lastName);
};
//END QuickUpdate :: ClientProfile :: CompanyName


BeePOS.ajaxFormCb.EditClientQuickclasslevel = function (m, form) {
    $("#EditClientQuick-classlevel").hide();
    var ClassLevelQuick = $("#ClassLevelQuick option:selected").text();
    $("#classlevel").text('דרגה: ' + ClassLevelQuick);
    $("#ClassLevel").val($('#ClassLevelQuick').val());
    $("#classlevel").show();
};



//QuickUpdate :: ClientProfile :: Dob
BeePOS.ajaxFormCb.EditClientQuickdob = function (m, form) {
    $("#EditClientQuick-dob").hide();
    $("dob1").val($('#dob1').val());
    $("#dob").show();
    $("#Dob").val($('#dob1').val());

    var date = $('#dob1').val();
    $("#dob").text(date.split('-').reverse().join('/'));

    function getAge(dateString) {
        var today = new Date();
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }
    if ((new Date(date)) <= (new Date())) {
        $("#dobage").text(getAge($('#dob1').val()));
        $(".agetr").show();
    } else {
        $("#dob").text('אין תאריך לידה');
        $(".agetr").hide();
    }
    location.reload();
};
//END QuickUpdate :: ClientProfile :: Dob


//QuickUpdate :: ClientProfile :: Gender
BeePOS.ajaxFormCb.EditClientQuickgender = function (m, form) {
    $("#EditClientQuick-gender").hide();
    var GetGender = $("#EditClientQuick-gender input[type='radio']:checked").val();
    if (GetGender == '1') {
        $("#gender1").html('<i class="fas fa-male"></i> זכר');
    } else if (GetGender = '2') {
        $("#gender1").html('<i class="fas fa-female"></i> נקבה');
    }
    $("#gender").show();
    $("#Gender").val(GetGender);
};
//END QuickUpdate :: ClientProfile :: Gender


//QuickUpdate :: ClientProfile :: Status
BeePOS.ajaxFormCb.EditClientQuickstatus = function (m, form) {
    $("#EditClientQuick-status").hide();
    var GetStatus = $("#EditClientQuick-status input[type='radio']:checked").val();
    if (GetStatus == '0') {
        $("#status1").html('<span style="color: #48AD42;">פעיל</span>');
    } else if (GetStatus = '1') {
        $("#status1").html('<span style="color: #da2846;">ארכיון</span>');
    }
    $("#status").show();
    $("#Status").val(GetStatus);
    location.reload();
};
//END QuickUpdate :: ClientProfile :: Status


BeePOS.ajaxFormCb.AddNewLead = function (m, form) {

    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="Email"]').val('');


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddNewLead = function (message, form) {
    window.location.href = message.redirect;

};

BeePOS.ajaxFormCb.UpdateLeadInfo = function (m, form) {

    form.find('[name="FirstName"]').val('');
    form.find('[name="LastName"]').val('');
    form.find('[name="ContactMobile"]').val('');
    form.find('[name="Email"]').val('');


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();
};


// AddEditTask
function AddEditTask(PipeLineId, ClientId, CalendarId) {

    $('#AddEditTaskPipeLineId').val(PipeLineId);
    $('#AddEditTaskClientId').val(ClientId);
    $('#AddEditTaskCalendarId').val(CalendarId);

    if (CalendarId != null) {

        $.ajax({
            url: 'action/GetCalendarInfo.php?Id=' + CalendarId + '&ClientId=' + ClientId,
            dataType: 'json',

            success: function (response) {

                $('#ChooseFloorForTask').val(response.Floor).trigger('change');
                $('#CalTaskTitle').val(response.Title);
                $('#CalTypeOption').val(response.Type);
                $('#SetDate').val(response.StartDate);
                $('#SetTime').val(response.StartTime);
                var FixToTimes = moment(response.StartTime, 'hh:mm:ss').add(5, 'minutes').format('H:mm:ss');
                $('#SetToTime').prop('min', FixToTimes);
                $('#SetToTime').val(response.EndTime).trigger('change');
                $('#CalLevel').val(response.Level);
                $('#ChooseAgentForTask').val(response.AgentId);
                $('#CalRemarks').val(response.Content);
                $('#CalTaskStatus').val(response.Status);
                $('#CalTaskStatus').prop('disabled', false);



            }
        });

    }


    var modal = $('#AddNewTask');

    modal.modal('show');
    init();
    scheduler.update_view();

}
;

//הודעת שגיאה
function BN(kind, title) {
    if (kind == '2') {
        var BNwarning = $.notify({
            icon: 'fas fa-spinner fa-pulse',
            message: title,
        }, {
            type: 'warning',
            z_index: '99999999',
        });
    } else if (kind == '1') {
        $.notify({
            icon: 'fas fa-times-circle',
            message: title,
        }, {
            type: 'danger',
            z_index: '99999999',
        });
    } else if (kind == '0') {
        $.notify({
            icon: 'fas fa-check-circle',
            message: title,
        }, {
            type: 'success',
            z_index: '99999999',
        });
    }
}
//הודעת שגיאה


function clickAndDisable(el) {
    el.onclick = function(event) {
        event.preventDefault();
    }
    $(el).addClass('disabled');
}

// פופאפ שליחת חשבונית
function SendDocumentModal(TypeId, DocId, Way) {

    var modal = $('#SendDocumentModal');

    var TypeId = TypeId;
    var DocId = DocId;
    var Way = Way;

    $.ajax({
        url: 'action/SendDocumentModal.php',
        type: 'POST',
        data: {TypeId: TypeId, DocId: DocId, Way: Way},
        success: function (data) {
            $('#ResultSendDocumentModal').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

    BeePOS.ajaxFormCb.ActSendDocumentModal = function () {
        modal.modal('hide');
    };
}
;


function OpenDocumentModal(TypeId, DocId) {

    var modal = $('#OpenDocumentModal');

    var TypeId = TypeId;
    var DocId = DocId;

    $.ajax({
        url: 'action/OpenDocumentModal.php',
        type: 'POST',
        data: {TypeId: TypeId, DocId: DocId},
        success: function (data) {
            $('#ResultOpenDocumentModal').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback
}
;

// סיום פופאפ שליחת חשבונית

BeePOS.ajaxFormCb.AddMemberShip = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateMemberShip(ItemId) {

    var modal = $('#EditMemberShipPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateMemberShip.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            $('#result').html(data);
        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditMemberShip = function () {

        location.reload();
        modal.modal('hide');
    };
}
;



BeePOS.ajaxFormCb.AddActivity = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="Items1"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();

};

function LogActivity(ItemId) {

    var modal = $('#LogActivityPopup');

    var selectval = ItemId;
    var url = 'action/LogActivityPopup.php?ItemId=' + selectval;

    $('#resultLogActivity').load(url, function () {
        $('#resultLogActivity .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;
    });


    modal.modal('show');


}
;


function LogActivityRegular(ItemId) {

    var modal = $('#LogActivityRegularPopup');

    var selectval = ItemId;
    var url = 'action/LogActivityRegularPopup.php?ItemId=' + selectval;

    $('#resultLogActivityRegular').load(url, function () {
        $('#resultLogActivityRegular .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;
    });


    modal.modal('show');


}
;


function OptionActivity(ItemId, reg = false) {

    var modal = $('#OptionsActivityPopup');

    var selectval = ItemId;
    var url = "";
    if (reg == false) {
        url = 'action/OptionsActivityPopup.php?ItemId=' + selectval;
    } else {
        url = 'action/regFeeActivity.php?reg=' + selectval;
    }

    $('#resultOptionsActivity').load(url, function () {
        $('#resultOptionsActivity .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;
    });

    modal.modal('show');

}
;




BeePOS.ajaxFormCb.AddDiscountActivity = function (m, form) {
    form.find('[name="ActivityId"]').val('');
    form.find('[name="ClientId"]').val('');


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.EditMenegmentMemberShip = function (m, form) {
    form.find('[name="ActivityId"]').val('');
    form.find('[name="ClientId"]').val('');


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddFreez = function (m, form) {
    form.find('[name="ClassDate"]').val('');
    form.find('[name="ClassDateEnd"]').val('');
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="Reason"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.FreezAllClients = function () {

    location.reload();
}

BeePOS.ajaxFormCb.FreezOutAllActivities = function (response, form) {
    BeePOS.alert(response, 1, form);
    location.reload();
}

BeePOS.ajaxFormCb.AddDateCalss = function (m, form) {
    form.find('[name="ClassDate"]').val('');
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="Reason"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AddStartDateCalss = function (m, form) {
    form.find('[name="ClassDate"]').val('');
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="Reason"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddCalss = function (m, form) {
    form.find('[name="ClassNumber"]').val('');
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="Reason"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.CancelNewActivity = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="Reason"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


BeePOS.ajaxFormCb.AddMultiClients = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};


function CancelFreez(Id, ClientId) {

    var modal = $('#FreezOutActivityPopup');

    modal.find('input[name="ClientId"]').val(ClientId);
    modal.find('input[name="ActivityId"]').val(Id);
    modal.modal('show');



    BeePOS.ajaxFormCb.FreezOutActivity = function () {

        //window.location.href = value;
        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddClassType = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateClassType(ItemId) {
    $('#result').empty();
    var modal = $('#EditClassTypePopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateClassType.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditClassType = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddClassDesk = function (message, form) {
    form.find('[name="SetDate"]').val('');
    form.find('[name="Day"]').val('');
    form.find('[name="SetTime"]').val('');
    form.find('[name="SetToTime"]').val('');
    form.find('[name="FloorId"]').val('');

    BeePOS.alert(message, 1, form);

    var CalPage = $('#CalPageA').val();
    var CalPageR = $('#CalPageR').val();

    var modal = $('#AddNewTask');
    if (CalPage == '0') {
        location.reload();
    } else {
        modal.modal('hide');
        $('#FormAddClassDesk').trigger("reset");
        $('#FormAddClassDesks').empty();
        $(".ip-close").trigger("click");
        form.find('.alert').hide();
        if (CalPageR == '1') {
            location.reload();
        } else {
            location.reload();
            // scheduler.load("new/data/deskplan.php"); 
        }

    }
};




BeePOS.ajaxFormCb.AddClientRemarksPopUp = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddClientRemarksPopUp = function (message, form) {
    var ActivityId = message.ActivityId;
    var Remarks = message.Remarks;
    $('#ClientTRDiv_Remarks' + ActivityId).html(Remarks);
    $('.HideDiv').hide();

};


BeePOS.ajaxFormCb.AddClientDevicePopUp = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddClientDevicePopUp = function (message, form) {
    var ActivityId = message.ActivityId;
    var DeviceTitle = message.DeviceTitle;
    $('#ClientTRDiv_DeviceTitle' + ActivityId).html(DeviceTitle);
    $('.HideDiv').hide();

};

BeePOS.ajaxFormCb.AddClientClassInsetedPopUp = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.AddClientClassInsetedPopUp = function (message, form) {
    var ActivityId = message.ActivityId;
    var ClassTitle = message.ClassTitle;
    $('#ClientTRDiv_ClassTitle' + ActivityId).html(ClassTitle);
    $('#ClientTRDivs' + ActivityId).show();
    $('.HideDiv').hide();

};

BeePOS.ajaxFormCb.AddClientAddClass = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();

};

BeePOS.ajaxFormCb.ClientRemoveRegularClass = function (m, form) {
    form.find('[name="RemoveClassId"]').val('');
    form.find('[name="RemoveClassClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.POSClientCancelDocs = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
};

BeePOS.ajaxFormCb.POSClientCancelDocs = function (message, form) {
    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;

    if (StatusNew == '1') {
        location.reload();
    } else {
        $("#POSCancelDocsError").css("display", "block");
        $('#POSCancelDocsErrorText').html(Status);
        $("#CancelDocs_TempsId").val(TempIdNew);
    }


};


BeePOS.ajaxFormCb.POSClientCancelDocsRefound = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
};

BeePOS.ajaxFormCb.POSClientCancelDocsRefound = function (message, form) {
    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;

    if (StatusNew == '1') {
        location.reload();
    } else {
        $("#POSCancelDocsErrorRefound").css("display", "block");
        $('#POSCancelDocsErrorTextRefound').html(Status);
        $("#CancelDocs_TempsIdRefound").val(TempIdNew);
    }


};


BeePOS.ajaxFormCb.POSClientCancelPayments = function (m, form) {

    form.find('[name="ClientId"]').val('');
    form.find('[name="TempListsId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.POSClientCancelPayments = function (message, form) {

    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;
    var GroupNumber = message.GroupNumber;
    var Finalinvoicenum = message.Finalinvoicenum;
    if (StatusNew == '1') {
        $("#DocsPayments").load("DocPaymentInfoClient.php?TempId=" + TempIdNew + "&CheckRefresh=2&TypeDoc=" + GroupNumber + "&TrueFinalinvoicenum=" + Finalinvoicenum);
        $(".CancelPaymentsClose").trigger("click");
        let paymentsTable = $('#DocsPayments').find('table').find('tbody');
        if(paymentsTable.find('tr').length > 1) {
            $('#ReceiptBtn').attr("disabled", false);
        } else {
            $('#ReceiptBtn').attr("disabled", true);
        }

    } else {
        BN('1', Status);
    }


};


BeePOS.ajaxFormCb.POSClientCancelPaymentsRefound = function (m, form) {

    form.find('[name="ClientId"]').val('');
    form.find('[name="TempListsId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.POSClientCancelPaymentsRefound = function (message, form) {

    var Status = message.Status;
    var TempIdNew = message.TempIdNew;
    var StatusNew = message.StatusNew;
    var GroupNumber = message.GroupNumber;
    var Finalinvoicenum = message.Finalinvoicenum;
    if (StatusNew == '1') {
        $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId=" + TempIdNew + "&CheckRefresh=2&TypeDoc=" + GroupNumber + "&TrueFinalinvoicenum=" + Finalinvoicenum);
        $(".CancelPaymentsClose").trigger("click");
        let paymentsTable = $('#DocsPaymentsRefound').find('table').find('tbody');
        if(paymentsTable.find('tr').length > 1) {
            $('#ReceiptRefoundBtn').attr("disabled", false);
        } else {
            $('#ReceiptRefoundBtn').attr("disabled", true);
        }
    } else {
        BN('1', Status);
    }


};



BeePOS.ajaxFormCb.AddSections = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateSections(ItemId) {

    var modal = $('#EditMemberShipPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/UpdateSections.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSections = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.AddDevice = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function updateDeviceNumbers(ItemId) {

    var modal = $('#EditMemberShipPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateDeviceNumbers.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditDevice = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.AddDeviceSub = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function updateDeviceNumbersSub(ItemId) {

    var modal = $('#EditMemberShipPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateDeviceNumbersSub.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditDeviceSub = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.ViewClass = function (m, form) {
    form.find('[name="ViewClass"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AppGeneral = function (m, form) {
    form.find('[name="AppRenew"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AppContent = function (m, form) {
    form.find('[name="AppRenew"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AppCancel = function (m, form) {

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AppWatingList = function (m, form) {
    form.find('[name="Watinglist"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AppNotification = function (m, form) {
    form.find('[name="SendSMS"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.CloseEventPopUp = function (m, form) {
    form.find('[name="ClassIdCloseEvent"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $(".ip-close").trigger("click");
    //   scheduler.load("New/data/deskplan.php");     

};

BeePOS.ajaxFormCb.CancelEventPopUp = function (m, form) {
    form.find('[name="ClassIdCloseEvent"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $(".ip-close").trigger("click");
    //   scheduler.load("New/data/deskplan.php");     

};

BeePOS.ajaxFormCb.SendNotificationEventPopUp = function (m, form) {
    form.find('[name="ClassIdCloseEvent"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    $(".ip-close").trigger("click");
};

BeePOS.ajaxFormCb.SendNotificationClient = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();
};

BeePOS.ajaxFormCb.SendPipeFormClient = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();
};

BeePOS.ajaxFormCb.SendPipeFormMedicalClient = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    const noReload = form.hasClass('js-no-reload');
    if(noReload) LeadsData.closePopupAfterChanged();
    else location.reload();
};




BeePOS.ajaxFormCb.SendNotificationWorker = function (m, form) {
    form.find('[name="ClientId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};

// new client/lead popup
function NewClient(opt = 'client') {
    const $popup = $("#js-client-popup");
    $popup.modal("show").showModalLoader();

    $.ajax({
        url: 'new-client-popup.php',
        method: 'GET',
        data: {
            option: opt,
        },
        success: function (res) {
            $popup.find('.modal-content').html(res);
        }
    });
}
// end of new client/lead popup

// new task popup
function handleNewTask(id = null, clientId = null) {
    const $popup = $("#js-task-popup");
    $popup.modal("show").showModalLoader();

    $.ajax({
        url: 'task-form-popup.php',
        method: 'GET',
        data: {
            id: id,
            clientId: clientId,
        },
        success: function (res) {
            $popup.find('.modal-content').html(res);
        }
    });
}
// end of new task popup

// פופאפ לקוח חדש
function NewCal(Id, ClientId, PipeId, PipeCategoryId = null) {

    $('#ResultAddNewCal').empty();
    $('#ResultAddNewCal').html('<center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center>');
    $('#AddNewCal').find('.alert').hide();
    var modal = $('#AddNewCal');

    $.ajax({
        url: 'action/CreateNewCal.php?Id=' + Id + '&ClientId=' + ClientId + '&PipeId=' + PipeId,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddNewCal').html(data).fadeIn('fast');
        }
    });

    modal.modal('show');
    // Register ajax form callback

    BeePOS.ajaxFormCb.AddCalendarClient = function () {
        modal.modal('hide');
        if(PipeCategoryId) // if PipeCategoryId not need reload page
            LeadsData.GetDataManageLeads(PipeCategoryId);
        else
            location.reload();
    };
}
;

// סיום פופאפ לקוח חדש

BeePOS.ajaxFormCb.AppHealth = function (m, form) {
    form.find('[name="Content"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};

BeePOS.ajaxFormCb.AppTakanon = function (m, form) {
    form.find('[name="Content"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.AddClientMedical = function (m, form) {
    form.find('[name="Content"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


function LogClass(ItemId) {

    $("#DivViewDeskInfo").empty();
    var modalcode = $('#ViewDeskInfo');
    $('#ViewDeskInfo .ip-modal-title').html('מתאמנים משובצים');

    modalcode.modal('show');
    var url = 'new/ClientList.php?Id=' + ItemId;
    // $('#DivViewDeskInfo').load(url); 

    $('#DivViewDeskInfo').load(url, function (e) {
        $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;
    });


}
;

BeePOS.ajaxFormCb.SaveReceipt = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="GroupNumber"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.SaveReceiptRefound = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="GroupNumber"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.SupportChangeCompanyNum = function () {
    location.reload();
};



window.ConfettiGenerator = function (e) {
    function t(e, t) {
        e || (e = 1);
        var i = Math.random() * e;
        return t ? Math.floor(i) : i
    }
    function i() {
        return {prop: a.props[t(a.props.length, !0)], x: t(a.width), y: t(a.height), radius: t(4) + 1, line: Math.floor(t(65) - 30), angles: [t(10, !0) + 2, t(10, !0) + 2, t(10, !0) + 2, t(10, !0) + 2], color: a.colors[t(a.colors.length, !0)], rotation: t(360, !0) * Math.PI / 180, speed: t(a.clock / 7) + a.clock / 30}
    }
    function r(e) {
        var t = e.radius <= 3 ? .4 : .8;
        switch (n.fillStyle = n.strokeStyle = "rgba(" + e.color + ", " + t + ")", n.beginPath(), e.prop) {
            case "circle":
                n.moveTo(e.x, e.y), n.arc(e.x, e.y, e.radius * a.size, 0, 2 * Math.PI, !0), n.fill();
                break;
            case "triangle":
                n.moveTo(e.x, e.y), n.lineTo(e.x + e.angles[0] * a.size, e.y + e.angles[1] * a.size), n.lineTo(e.x + e.angles[2] * a.size, e.y + e.angles[3] * a.size), n.closePath(), n.fill();
                break;
            case "line":
                n.moveTo(e.x, e.y), n.lineTo(e.x + e.line * a.size, e.y + 5 * e.radius), n.lineWidth = 2 * a.size, n.stroke();
                break;
            case "square":
                n.save(), n.translate(e.x + 15, e.y + 5), n.rotate(e.rotation), n.fillRect(-15 * a.size, -5 * a.size, 15 * a.size, 5 * a.size), n.restore()
        }
    }
    var a = {target: "confetti-holder", max: 80, size: 1, animate: !0, props: ["circle", "square", "triangle", "line"], colors: [[165, 104, 246], [230, 61, 135], [0, 199, 228], [253, 214, 126]], clock: 25, interval: null, width: window.innerWidth, height: window.innerHeight};
    e && (e.target && (a.target = e.target), e.max && (a.max = e.max), e.size && (a.size = e.size), void 0 !== e.animate && null !== e.animate && (a.animate = e.animate), e.props && (a.props = e.props), e.colors && (a.colors = e.colors), e.clock && (a.clock = e.clock), e.width && (a.width = e.width), e.height && (a.height = e.height));
    var o = document.getElementById(a.target), n = o.getContext("2d"), l = [];
    return {render: function () {
            function e() {
                n.clearRect(0, 0, a.width, a.height);
                for (var e in l)
                    r(l[e]);
                s()
            }
            function s() {
                for (var e = 0; e < a.max; e++) {
                    var i = l[e];
                    a.animate && (i.y += i.speed), i.y > a.height && (l[e] = i, l[e].x = t(a.width, !0), l[e].y = -10)
                }
            }
            o.width = a.width, o.height = a.height, l = [];
            for (var c = 0; c < a.max; c++)
                l.push(i());
            return a.animate ? a.interval = setInterval(e, 20) : e()
        }, clear: function () {
            n.clearRect(0, 0, o.width, o.height);
            var e = o.width;
            o.width = 1, o.width = e, clearInterval(a.interval)
        }}
};


BeePOS.ajaxFormCb.SendTokens = function (m, form) {
    form.find('[name="ClientId"]').val('');
    $('#CreditSubmitToken').attr("disabled", true);
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
};

BeePOS.ajaxFormCb.SendTokens = function (message, form) {
    var Status = message.Status;
    var StatusNew = message.StatusNew;


    if (StatusNew == '1') {
        $("#IframeOpenCreditSuccessToken").css("display", "block");
        location.reload();
    } else {
        $("#IframeOpenCreditDangerToken").css("display", "block");
        $('#CreditErrorToken').html(Status);
        $('#CreditSubmitToken').attr("disabled", false);
    }


};


BeePOS.ajaxFormCb.AddNewClientPopUp = function (m, form) {
    form.find('[name="ClientId"]').val('');
    form.find('[name="ActivityId"]').val('');
    form.find('[name="ClassId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.AddNewClientPopUp = function (message, form) {
    var ClassId = message.ClassId;

    var url = 'new/ClientList.php?Id=' + ClassId;
    $('#DivViewDeskInfo').load(url, function (e) {
        $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);

        $.ajax({
            url: '/office/action/ClientsClass.php',
            type: 'POST',
            contentType: "application/json; charset=utf-8",
            success: function (data) {
                var classes = data.ClassesAct;
                $(".ClassParticipants").each(function () {
                    for (var i = 0; i < classes.length; i++) {
                        if (classes[i].id == $(this).attr("data-id")) {
                            var CurClass = classes[i];
                            break;
                        }
                    }
                    $(this).text(CurClass.ClassParticipants.length.toString() + "/" + CurClass.MaxClient.toString())
                })

            },
            error: function (data) {
                alert(data.Messege);

            }


        });
        return false;
    });

};

BeePOS.ajaxFormCb.SendClientForm = function (m, form) {
    form.find('[name="Email"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};

BeePOS.ajaxFormCb.SendClientPush = function (m, form) {


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.SendClientPushReport = function (m, form) {


    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.AgentClientPushReport = function (m, form) {

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.classsettings = function (m, form) {
    form.find('[name="CheckMinClientType"]').val('');
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


BeePOS.ajaxFormCb.AddFAQ = function (m, form) {
    form.find('[name="Question"]').val('');
    form.find('[name="Answer"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateFAQ(ItemId) {

    var modal = $('#EditFAQPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateFAQ.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultFAQ').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditFAQ = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddRoles = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateRoles(ItemId) {

    var modal = $('#EditRolespPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateRoles.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditRoles = function () {

        location.reload();
        modal.modal('hide');
    };
}
;





BeePOS.ajaxFormCb.AddClassRemarksPopUp = function (m, form) {
    form.find('[name="RemarksStatus"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};


BeePOS.ajaxFormCb.AddSalary = function (m, form) {
    form.find('[name="CoachId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateSalary(ItemId) {

    var modal = $('#EditSalaryPopup');
    $('#result').html("");
    var selectval = ItemId;

    $.ajax({
        url: 'action/updateSalary.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditSalary = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

// פופאפ שיעור חדש
function NewClass() {

    $('#ResultAddNewClass').html('');
    $('#AddNewClass').find('.alert').hide();
    var modal = $('#AddNewClass');

    $.ajax({
        url: '/office/action/CreateNewClass.php',
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;


function NewEditClass(Id) {
    $('#ResultEditNewClass').html('');

    $('#EditNewClass').find('.alert').hide();
    var modal = $('#EditNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/EditNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultEditNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;


function NewDuplicateClass(Id) {

    $('#ResultDuplicateNewClass').html('');
    $('#DuplicateNewClass').find('.alert').hide();
    var modal = $('#DuplicateNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/DuplicateNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultDuplicateNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;


function NewViewClass(Id) {

    $('#ResultViewNewClass').html('');
    var modal = $('#ViewNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/ViewNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultViewNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;

// סיום פופאפ שיעור חדש


BeePOS.ajaxFormCb.AddClassNewPopUp = function (message, form) {
    form.find('[name="SetDate"]').val('');
    form.find('[name="Day"]').val('');
    form.find('[name="SetTime"]').val('');
    form.find('[name="SetToTime"]').val('');
    form.find('[name="FloorId"]').val('');

    BeePOS.alert(message, 1, form);
    //location.hash = "";
    $('#ResultAddNewClass').html('');
    $('#ResultEditNewClass').html('');
    if (window.location.href.indexOf("DeskPlanNew.php") == -1) {
        location.reload();
    } else {
        $("#AddNewClass").modal("hide");
        $("#EditNewClass").modal("hide");
        $("#js-char-popup").modal("hide");
        location.hash = "";
        GetCalendarData();
    }
};





// פופאפ פריט חדש
function NewItems() {

    $('#ResultAddNewItems').html('');

    $('#AddNewItems').find('.alert').hide();
    var modal = $('#AddNewItems');

    $.ajax({
        url: '/office/action/CreateNewItems.php',
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddNewItems').html(data);
        }
    });

    modal.modal('show');

}
;

function NewItemsEdit(id) {

    $('#ResultEditNewItems').html('');

    $('#EditNewItems').find('.alert').hide();
    var modal = $('#EditNewItems');

    $.ajax({
        url: '/office/action/EditNewItems.php?Id=' + id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultEditNewItems').html(data);
        }
    });

    modal.modal('show');

}
;



BeePOS.ajaxFormCb.AddItemNewPopUp = function (m, form) {
    form.find('[name="membership_type"]').val('');
    form.find('[name="Membership"]').val('');
    form.find('[name="ItemName"]').val('');
    form.find('[name="ItemPrice"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = 'Items.php';
};

BeePOS.ajaxFormCb.EditItemNewPopUp = function (m, form) {
    form.find('[name="membership_type"]').val('');
    form.find('[name="Membership"]').val('');
    form.find('[name="ItemName"]').val('');
    form.find('[name="ItemPrice"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    window.location.href = 'Items.php';
};


BeePOS.ajaxFormCb.AddUsersClock = function (m, form) {
    form.find('[name="UserId"]').val('');
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();
};


// Update RegularClass
function UpdateRegularClass(ClassId) {

    var modal = $('#RegularClassPopup');
    $('#ShowSaveRegularClass').hide();
    var selectval = ClassId;

    $.ajax({
        url: 'action/UpdateRegularClass.php',
        type: 'POST',
        data: {ClassId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultRegularClass').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditRegularClass = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddClientLevel = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};

function UpdateClientLevel(ItemId) {

    var modal = $('#EditClientLevelPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/updateClientLevel.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultClientLevel').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditClientLevel = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

BeePOS.ajaxFormCb.LogCallRecord = function (m, form) {

    form.find('[name="ClientId"]').val('');
    form.find('[name="CallStartDate"]').val('');
    form.find('[name="CallEndDate"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};


BeePOS.ajaxFormCb.LogCallRecord = function (message, form) {

    $('#CallLogTable').html('');
    var json = message.json;

    $.ajax({
        url: 'action/CallLogTable.php',
        type: 'POST',
        data: {Json: json},
        success: function (data) {
            //alert(data);
            $('#CallLogTable').html(data);

        }
    });
};

function UpdateSettingsNotification(ItemId) {

    var modal = $('#UpdateSettingsNotificationPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/SettingsNotification.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultSettingsNotification').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.SettingsNotification = function () {

        location.reload();
        modal.modal('hide');
    };
};

// פופאפ שיעור חדש
function NewPrivateClass() {
    $('#ResultAddNewPrivateClass').html('');
    $('#AddNewPrivateClass').find('.alert').hide();
    var modal = $('#AddNewPrivateClass');

    $.ajax({
        url: '/office/action/CreateNewPrivateClass.php',
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddNewPrivateClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;


function NewPrivateEditClass(Id) {

    $('#ResultEditNewPrivateClass').html('');
    $('#EditNewPrivateClass').find('.alert').hide();
    var modal = $('#EditNewPrivateClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/EditNewPrivateClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultEditNewPrivateClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;

BeePOS.ajaxFormCb.AddClassNewPrivatePopUp = function (message, form) {
    form.find('[name="FloorId"]').val('');

    BeePOS.alert(message, 1, form);
    location.hash = "";
    $('#ResultEditNewPrivateClass').html('');
    location.reload();

};


// פופאפ שיעור חדש
function NewsClass() {

    $('#ResultAddsNewClass').html('');
    $('#AddsNewClass').find('.alert').hide();
    var modal = $('#AddsNewClass');

    $.ajax({
        url: '/office/action/CreatesNewClass.php',
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddsNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;

// new block event popup
function blockEvent() {
    $("#js-action-modal").modal("hide");

    const $popup = $("#js-block-event-popup");
    $popup.modal("show").showModalLoader();

    $.ajax({
        url: 'new-block-event.php',
        method: 'GET',
        success: function (res) {
            $popup.find('.modal-content').html(res);
        }
    });
}
// end of new block event popup

BeePOS.ajaxFormCb.AddsClassNewPopUp = function (message, form) {
    form.find('[name="SetDate"]').val('');
    form.find('[name="Day"]').val('');
    form.find('[name="SetTime"]').val('');
    form.find('[name="SetToTime"]').val('');
    form.find('[name="Template"]').val('');

    BeePOS.alert(message, 1, form);
    location.hash = "";
    $('#ResultAddNewClass').html('');
    location.reload();

};

BeePOS.ajaxFormCb.MembershipTypes = function (m, form) {
    form.find('[name="MembershipType"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

BeePOS.ajaxFormCb.AddCalType = function (m, form) {
    form.find('[name="Type"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

function UpdateCalType(ItemId) {

    var modal = $('#EditCalTypePopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/UpdateCalType.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditCalType = function () {

        location.reload();
        modal.modal('hide');
    };
}
;


BeePOS.ajaxFormCb.AddPipeline = function (m, form) {
    form.find('[name="Title"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    //	window.location.href = "StatusList.php";
    location.reload();

};


BeePOS.ajaxFormCb.AddPipeReasons = function (m, form) {
    form.find('[name="ReasonId"]').val('');
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    var modal = $('#PipeReasonsPopup');
    modal.modal('hide');

};



BeePOS.ajaxFormCb.MoveLeadProfile = function (m, form) {
    form.find('[name="ItemId"]').val('');
    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);

};

BeePOS.ajaxFormCb.MoveLeadProfile = function (message, form) {
    window.location.href = message.redirect;

};


function UpdateClassStatus(ClassId, Status) {

    var modal = $('#ClassStatusPopUp');

    var selectval = ClassId;
    var selectType = Status;

    $.ajax({
        url: 'action/UpdateClassStatus.php',
        type: 'POST',
        data: {ClassId: selectval, Status: selectType},
        success: function (data) {
            //alert(data);
            $('#ResultClassStatus').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditClassStatusPopUp = function () {
        modal.modal('hide');
        location.reload();
    };
}
;


function ChangeActivity(ClassId, ActivityId, ClassYear, ClassMonth, ClientId) {

    var modal = $('#ChangeActivityPopup');

    var selectval = ClassId;
    var ActivityId = ActivityId;
    var ClassYear = ClassYear;
    var ClassMonth = ClassMonth;
    var ClientId = ClientId;

    var url = 'action/ChangeActivityPopup.php?ClassId=' + selectval + '&ActivityId=' + ActivityId + '&ClassYear=' + ClassYear + '&ClassMonth=' + ClassMonth + '&ClientId=' + ClientId;

    $('#resultChangeActivity').load(url, function () {
        $('#resultChangeActivity .ajax-form').on('submit', BeePOS.ajaxForm);
        return false;
    });

    modal.modal('show');


}
;


BeePOS.ajaxFormCb.ChangeActivityClass = function (m, form) {
    form.find('[name="OldActivityId"]').val('');
    form.find('[name="ClassId"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    var modal = $('#ChangeActivityPopup');
    modal.modal('hide');

    var ClassYear = $('#ClassYear1').val();
    var ClassMonth = $('#ClassMonth1').val();
    var ClientId = $('#FixClientId1').val();
    $('#HistoryMonth').val(ClassMonth);
    var url = 'action/ClassHistory.php?ClassYear=' + ClassYear + '&ClassMonth=' + ClassMonth + '&ClientId=' + ClientId;
    $('#DivClassHistory').empty();
    $('#DivClassHistory').load(url, function () { });



};


BeePOS.ajaxFormCb.AddAutomation = function (m, form) {
    form.find('[name="Category"]').val('');
    form.find('[name="Type"]').val('');
    form.find('[name="Value"]').val('');

    BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
    location.reload();

};

function UpdateAutomation(ItemId) {
    $('#result').empty();
    var modal = $('#EditAutomationPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/EditAutomation.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#result').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditAutomation = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

function UpdateCRMClient(ItemId) {
    $('#resultCRMClient').empty();
    var modal = $('#UpdateCRMClientPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/UpdateCRMClient.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultCRMClient').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.UpdateCRMClient = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

function UpdateMedicalClient(ItemId, ClientId) {
    $('#resultMedicalClient').empty();
    var modal = $('#UpdateMedicalClientPopup');
    var selectval = ItemId;

    $.ajax({
        url: 'action/UpdateMedicalClient.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultMedicalClient').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.find('input[name="ClientId"]').val(ClientId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.UpdateMedicalClient = function () {

        location.reload();
        modal.modal('hide');
    };
}
function OpenClassPopup(classId = null, duplicate = 0, callback = null ) {
    if (!$('#isOldClassModal').length) {
        const $popup = $("#js-meeting-popup")
        $popup.modal("show").showModalLoader()

        $.ajax({
            url: 'new-meeting-popup.php',
            method: 'GET',
            data: {
                classId: classId,
                duplicate: duplicate
            },
            success: function (res) {
                $popup.find('.modal-content').html(res);

                MeetingPopup.init()
                if (classId) {
                    populateFields.class.init(classId, duplicate)
                    $popup.find('#myTabContent .tagInfo').removeClass('d-none');
                }
            }
        }).then(function () {
            if (callback) callback()
        })
    } else {
        if (classId){
            if (duplicate)
                NewDuplicateClass(classId)
            else NewEditClass(classId)
        } else NewClass()
    }
}


// פופאפ שיעור חדש
function NewClass() {
    $('#ResultAddNewClass').html('');
    $('#AddNewClass').find('.alert').hide();
    var modal = $('#AddNewClass');

    $.ajax({
        url: '/office/action/CreateNewClass.php',
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultAddNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}
;


function removeClasses() {
    $("#RemoveClassesPopUp").show();
}


function NewEditClass(Id) {
    $('#ResultEditNewClass').html('');

    $('#EditNewClass').find('.alert').hide();
    var modal = $('#EditNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/EditNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultEditNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

}


function NewDuplicateClass(Id) {

    $('#ResultDuplicateNewClass').html('');
    $('#DuplicateNewClass').find('.alert').hide();
    var modal = $('#DuplicateNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/DuplicateNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultDuplicateNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback
}
;


function NewViewClass(Id) {

    $('#ResultViewNewClass').html('');
    var modal = $('#ViewNewClass');
    var Id = Id;
    $.ajax({
        url: '/office/action/ViewNewClass.php?Id=' + Id,
        type: 'POST',
        data: '',
        success: function (data) {
            $('#ResultViewNewClass').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback
}



function CancelDocumentModal(TypeId, DocId, TypeHeader) {

    var TypeId = TypeId;
    var DocId = DocId;
    var TypeHeader = TypeHeader;

    $.ajax({
        url: 'action/CancelDocumentModal.php',
        type: 'POST',
        dataType: 'json',
        data: {TypeId: TypeId, DocId: DocId, TypeHeader: TypeHeader},
        success: function (data) {
            var url = 'Docs.php?Types=' + data.TrueTypeHeader + '&DocAction=' + data.DocAction + '&ClientId=' + data.EditClientId + '&EditTempId=' + data.EditTempId;
            window.location.href = url;
        }
    });

};

function ConvertDocumentModal(TypeId, DocId, TypeHeader, Action) {

    var TypeId = TypeId;
    var DocId = DocId;
    var TypeHeader = TypeHeader;
    var Action = Action;

    $.ajax({
        url: 'action/ConvertDocumentModal.php',
        type: 'POST',
        dataType: 'json',
        data: {TypeId: TypeId, DocId: DocId, TypeHeader: TypeHeader, Action: Action},
        success: function (data) {
            var url = 'Docs.php?Types=' + data.TrueTypeHeader + '&DocAction=' + data.DocAction + '&ClientId=' + data.EditClientId + '&EditTempId=' + data.EditTempId;
            window.location.href = url;
        }
    });

}
;


function CancelDocumentModalRefound(TypeId, DocId, TypeHeader) {
    $('#resultCancelDocumentModalRefound').empty();
    var modal = $('#UpdateCancelDocumentModalRefoundPopup');

    var TypeId = TypeId;
    var DocId = DocId;
    var TypeHeader = TypeHeader;

    $.ajax({
        url: 'action/CancelDocumentModalRefound.php',
        type: 'POST',
        data: {TypeId: TypeId, DocId: DocId, TypeHeader: TypeHeader},
        success: function (data) {
            //alert(data);
            $('#resultCancelDocumentModalRefound').html(data);

        }
    });

    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.UpdateCancelDocumentModalRefound = function () {
    };

    BeePOS.ajaxFormCb.UpdateCancelDocumentModalRefound = function (message, form) {
        var Status = message.Status;
        var StatusNew = message.StatusNew;

        if (StatusNew == '1') {
            location.reload();
            modal.modal('hide');
        } else {
            $("#RPOSCancelDocsError").css("display", "block");
            $('#RPOSCancelDocsErrorText').html(Status);
        }


    };
}
;


BeePOS.ajaxFormCb.UpdateCancelDocumentModalRefound = function (message, form) {
    var Status = message.Status;
    var StatusNew = message.StatusNew;

    if (StatusNew == '1') {
        location.reload();
        modal.modal('hide');
    } else {
        $("#RPOSCancelDocsError").css("display", "block");
        $('#RPOSCancelDocsErrorText').html(Status);
    }
};

// פופאפ שליחת איפוס סיסמה
function SendAppPassModal(ClientId, Way) {

    var modal = $('#SendAppPassModal');

    var ClientId = ClientId;
    var Way = Way;

    $.ajax({
        url: 'action/SendAppPassModal.php',
        type: 'POST',
        data: {ClientId: ClientId, Way: Way},
        success: function (data) {
            $('#ResultSendAppPassModal').html(data);
        }
    });

    modal.modal('show');
    // Register ajax form callback

    BeePOS.ajaxFormCb.SendAppUsers = function () {
        modal.modal('hide');
    };
}
;


function UpdateForms(FormsId) {

    var modal = $('#UpdateFormsPopup');

    var selectval = FormsId;

    $.ajax({
        url: 'action/UpdateFormsPopup.php',
        type: 'POST',
        data: {FormsId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultUpdateFormsPopup').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="FormsId"]').val(FormsId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.UpdateForms = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

function UpdateRegistrationFees(ItemId) {
    $('#resultRegistrationFeesPopup').html('');
    var modal = $('#RegistrationFeesPopup');

    var selectval = ItemId;

    $.ajax({
        url: 'action/UpdateRegistrationFees.php',
        type: 'POST',
        data: {ItemId: selectval},
        success: function (data) {
            //alert(data);
            $('#resultRegistrationFeesPopup').html(data);

        }
    });

    //	data: {categoryidNew: categoryId};
    modal.find('input[name="ItemId"]').val(ItemId);
    modal.modal('show');

    // Register ajax form callback
    BeePOS.ajaxFormCb.EditRegistrationFees = function () {

        location.reload();
        modal.modal('hide');
    };
}
;

function getUrlParams(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    } else {
        return results[1] || 0;
    }
};

function setTwoNumberDecimal(elem) {
    elem.value = parseFloat(elem.value).toFixed(2);
};

function maxLengthCheck(object)
{
    if (parseFloat(object.value) > parseFloat(object.max)) {
        object.value = object.max;
    }
    if (parseFloat(object.value) < 0) {
        object.value = 0;
    }
}


function OffsetDebtReception(docsId, invoiceAmount, balanceAmount) {
    let modal = $('#offset-debt-reception-popup');
    const formElem = modal.find('#offset-debt-reception-form');
    formElem.find('#docs-id').val(docsId);
    modal.modal("show").showModalLoader();//add loader
    //check param
    $.ajax({
        url: "/office/ajax/Doc.php",
        type: "post",
        data: {
            docId: docsId,
            action: 'getInvoiceData',
        },
        success: function (response) {
            const responseObject = JSON.parse(response);
            if (responseObject.success) {
                invoiceAmount = responseObject.Amount - responseObject.refundAmount;
                balanceAmount = responseObject.BalanceAmount;
                // formElem.find('#offset-amount').val(balanceAmount).attr("max",invoiceAmount); //להוסיף אם רוצים אפשרות להעברה להחזרת כסף וךא רק לקזז
                formElem.find('#offset-amount').val(balanceAmount).attr("max",balanceAmount);
                modal.find('#balance-amount-text').text(balanceAmount);
                formElem.find('#balance-amount').val(balanceAmount);
                //remove loader
                modal.hideModalLoader();
            } else {
                $.notify({
                    icon: 'fas fa-times-circle',
                    message: lang('action_failed_footernew'),
                }, {
                    type: 'danger',
                    z_index: '99999999',
                });
                modal.modal('hide').hideModalLoader();

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            modal.modal('hide').hideModalLoader();
            $.notify({
                icon: 'fas fa-times-circle',
                message: lang('action_failed_footernew'),
            }, {
                type: 'danger',
                z_index: '99999999',
            });
            console.log(textStatus, errorThrown);
        },
    });
}

function OffsetDebtReceptionPost(e) {
    e.preventDefault();
    if (!Boolean(parseFloat($('#offset-amount').val()))){
        $.notify({
            icon: 'fas fa-times-circle',
            message:  lang('checkout_can_not_type_more'),
        }, {
            type: 'danger',
            z_index: '99999999',
        });
        return
    }
    let modal = $('#offset-debt-reception-popup');
    const formElem = modal.find('#offset-debt-reception-form');
    const submitButton = formElem.find('.submit-button').addClass("disabled");
    const offsetAmount = parseFloat(formElem.find('#offset-amount').val());
    const balanceAmount = parseFloat(formElem.find('#balance-amount').val());
    const docId = formElem.find('#docs-id').val();
    if(offsetAmount > balanceAmount) {
        //todo change...
        let prefix = location.origin;
        if(prefix === null || prefix === undefined) {
            prefix = 'https://login.boostapp.co.il'
        }
        window.open(`${prefix}/office/refund.php?docId=${docId}&amount=${offsetAmount-balanceAmount}`, '_blank');
        submitButton.removeClass("disabled");
        modal.modal('hide');
    } else {
        modal.showModalLoader();
        $.ajax({
            url: "/office/ajax/Doc.php",
            type: "post",
            data: {
                docId: docId,
                action: 'offsettingInvoiceDebt',
                offsetAmount: offsetAmount,
            },
            success: function (response) {
                submitButton.removeClass("disabled");
                modal.modal('hide').hideModalLoader();
                const responseObject = JSON.parse(response);
                let responseMessage = responseObject.message;
                if (responseObject.success) {
                    modal.modal('hide').hideModalLoader();
                    responseMessage = (responseMessage === undefined || responseMessage === null || responseMessage === "")
                        ? lang('action_done_beepos') : responseMessage;
                    $.notify(
                        {
                            icon: 'fas fa-check-circle',
                            message: responseMessage,
                        }, {
                            type: 'success',
                            z_index: '99999999',
                        });
                    location.reload();
                } else {
                    responseMessage = (responseMessage === undefined || responseMessage === null || responseMessage === "") ?
                        lang('action_failed_footernew') : responseMessage;
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: responseMessage,
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                    window.setTimeout(function(){location.reload(true)},3000)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                submitButton.removeClass("disabled");
                modal.modal('hide').hideModalLoader();
                $.notify({
                    icon: 'fas fa-times-circle',
                    message: lang('action_failed_footernew'),
                }, {
                    type: 'danger',
                    z_index: '99999999',
                });
                console.log(textStatus, errorThrown);
            },
        });
    }

}

function cancelDocumentsByInvoice(docId) {
    $("#spinners.payment_loader").show();
    $.ajax({
        url: "/office/ajax/Doc.php",
        type: "post",
        data:{
            docId: docId,
            action: 'cancelDocumentsByInvoice',
        },
        success: function (response) {
            const responseObject = JSON.parse(response);
            let responseMessage = responseObject.message;
            if(responseObject.success) {
                $("#spinners.payment_loader").hide();
                responseMessage = (responseMessage === undefined || responseMessage === null || responseMessage === "" )
                    ? lang('action_done_beepos') : responseMessage;
                $.notify(
                    {
                        icon: 'fas fa-check-circle',
                        message: responseMessage,
                    },{
                        type: 'success',
                        z_index: '99999999',
                    });
                location.reload();
            } else {
                $("#spinners.payment_loader").hide();
                responseMessage = (responseMessage === undefined || responseMessage === null || responseMessage === "" ) ?
                    lang('action_failed_footernew') : responseMessage;
                $.notify({
                    icon: 'fas fa-times-circle',
                    message: responseMessage,
                }, {
                    type: 'danger',
                    z_index: '99999999',
                });
                window.setTimeout(function(){location.reload(true)},3000)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#spinners.payment_loader").hide();
            $.notify({
                icon: 'fas fa-times-circle',
                message: lang('action_failed_footernew'),
            }, {
                type: 'danger',
                z_index: '99999999',
            });
            console.log(textStatus, errorThrown);
        },
    });
}

// popup related documents

 async function fetchData(url = '', data = {}) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            mode: "same-origin",
            credentials: "same-origin",
            headers: {
                "Accept":'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error('Something went wrong');
        }
        return await response.json(); // parse JSON response
    } catch (error) {
        console.error('[fetchData] error:', error);
        // errorCallback(error.message);
    }
};

function handleOpenRelDocsPopup(docId, docType,TypeId){
    $('#docs-related-loader').removeClass('d-none')
    $('#js-related-documents').modal('show');
    $('.docs-navigator').text(docType)
    $('.docs-navigator').attr('data-id', docType);
    $('.docs-navigator').attr('data-type', TypeId)
    fetchData("/office/ajax/Doc.php", {
        action: 'getLinkDocs',
        docId:docId
    }).then((res)=>{
       if (res.success){
           const {linkDocs,invoice}=res
           const balance=invoice?.BalanceAmount ?? '0'
          const subTitle=lang('detail') + " " + invoice?.docHeaderTypeName.replaceAll('-',' ');
           const documents=linkDocs.map((d)=>{
               return {
                   ...d,
                   DocDate:d.DocDate.replaceAll('-', '/').substring(2,10).split('/').reverse().join('/'),
                   isAmountUnderZero:Boolean(parseInt(d.Amount)<0),
                   isBalanceBiggerThanZero:Boolean(parseInt(balance)>0)
               }
           })


           const relatedDocsSource = $('#related-docs-template').html();
           const template = Handlebars.compile(relatedDocsSource);
           const relatedDocsContainer = document.getElementById('related-docs-wrapper');
           relatedDocsContainer.scroll({top: 0})
           $('.docs-navigator').text(invoice.TypeNumber);
           $('.docs-navigator').attr('data-id', invoice.TypeNumber);
           $('.docs-navigator').attr('data-type', invoice.TypeId)
           relatedDocsContainer.innerHTML = template({documents});
           $("#related-docs-title").text(subTitle);
           $('#related-docs-balance').text(balance);
           const btn= document.getElementById('related-docs-btn')
           btn.setAttribute('data-id', docId)
           if (parseInt(balance)>0){
              btn.textContent=lang("move_to_payment");
              btn.isNavigator=true;
           }else {
               btn.textContent=lang("close");
               btn.isNavigator=false;
           }

       }else {
           $.notify({
               icon: 'fas fa-times-circle',
               message: res.message || lang('action_not_done'),
           }, {
               type: 'danger',
               z_index: '99999999',
           });
           handleCloseRelDocsPopup()
       }
    }).catch((e)=>{
        $.notify({
            icon: 'fas fa-times-circle',
            message: lang('action_not_done'),
        }, {
            type: 'danger',
            z_index: '99999999',
        });
        handleCloseRelDocsPopup()

    }).finally(()=>{
        $('#docs-related-loader').addClass('d-none')
    })


}

function handleCloseRelDocsPopup(){
    $('#js-related-documents').modal('hide');
    $('body').find('.tbox').css('z-index', 900);
    $('body').find('.tmask').css('z-index', 900);

}




