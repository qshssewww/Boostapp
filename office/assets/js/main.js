$(document).ready(function() {
  $.simpleWeather({
    location: 'hertzlia',
    woeid: '',
    unit: 'c',
    success: function(weather) {
      html = '<span><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</span>';
      $("#weather").html(html);
    },
    error: function(error) {
      $("#weather").html('<p>'+error+'</p>');
    }
  });
});

jQuery(function ($) {
	// Render the reCAPTCHA
	$('#reminderModal, #activationModal, #signupModal').on('show.bs.modal', function (e) {
		var captcha = $(e.currentTarget).find('.recaptcha');
		
		if (typeof grecaptcha !== 'undefined') {
			grecaptcha.render(captcha[0], {'sitekey' : Beesoft.options.recaptchaSiteKey});
		}
	}).on('hidden.bs.modal', function (e) {
		$(e.currentTarget).find('.recaptcha').html('');
	});

	// Clear the hash when the reset and activation modals are closing
	$('#resetModal, #activateModal').on('hide.bs.modal', function () {
		window.location.hash = '';
	});

	$('.avatar-container select').on('change', function () {
		$.get(Beesoft.options.ajaxUrl, {action: 'avatarPreview', type: $(this).val()}, function (response) {
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
			$.get(Beesoft.options.ajaxUrl, {action: 'getContacts'}, function(response) {
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

	$('.ajax-form').on('click', '.social-connect a', function(e) {
		Beesoft.alert(Beesoft.trans('connecting') + $(this).text() + '...', 0, $(e.delegateTarget));
	});

	// Open password reset and activation modals if we
	// found a reminder in the hash. Eg: #reset-123456
	var hash = window.location.hash;
	switch ( hash.substr(1, hash.indexOf('-')-1) ) {
		case 'reset':
			var modal = $('#resetModal');
			modal.find('[name="reminder"]').val( hash.substr(hash.indexOf('-')+1, hash.length ) );
			modal.modal('show');
		break;

		case 'activate':
			var modal = $('#activateModal');
			modal.find('[name="reminder"]').val( hash.substr(hash.indexOf('-')+1, hash.length ) );
			modal.modal('show');
			modal.on('shown.bs.modal', function (){
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

Beesoft.ajaxFormCb.login = function (message) {
	if (message.length)
		window.location.href = message;
	else 
		window.location.reload();
};

Beesoft.ajaxFormCb.signup = function (message) {
	var display = $('#signupModal').css('display');

	if (message === true && display !== 'block') {
		window.location.reload();
	} else if (message.redirect !== undefined) {
		if (message.redirect)
			window.location.href = message.redirect;
		else 
			window.location.reload();
	} else if (display === 'block') {
		$('#signupSuccessModal').modal('show');
	}
};

Beesoft.ajaxFormCb.activation = function () {
	if ($('#activationModal').css('display') == 'block')
		$('#activationSuccessModal').modal('show');
	else
		window.location.reload();
};

Beesoft.ajaxFormCb.activate = function () {
	$('#activateSuccessModal').modal('show');
};

Beesoft.ajaxFormCb.reminder = function () {
	if ($('#reminderModal').css('display') == 'block')
		$('#reminderSuccessModal').modal('show');
	else
		window.location.reload();
};

Beesoft.ajaxFormCb.reset = function () {
	if ($('#resetModal').css('display') == 'block')
		$('#resetSuccessModal').modal('show');
	else
		window.location.href = window.location.origin + window.location.pathname;
};

Beesoft.ajaxFormCb.settingsAccount =
Beesoft.ajaxFormCb.settingsProfile = 
Beesoft.ajaxFormCb.settingsMessages = function (m, form) {
	Beesoft.alert(Beesoft.trans('changes_saved'), 1, form);
};

Beesoft.ajaxFormCb.settingsPassword = function (m, form) {
	form.find('input').val('');
	Beesoft.alert(Beesoft.trans('pass_changed'), 1, form);
};

Beesoft.ajaxFormCb.webmasterContact = function (m, form) {
	form.find('[name="message"]').val('');

	Beesoft.alert(Beesoft.trans('message_sent'), 1, form);
};


Beesoft.ajaxFormCb.addganenet = function (m, form) {
	form.find('[name="username"]').val('');
	form.find('[name="email"]').val('');
	form.find('[name="pass1"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
	
};

Beesoft.ajaxFormCb.addcategory = function (m, form) {
	form.find('[name="Name"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
	
};

Beesoft.ajaxFormCb.addlisting = function (m, form) {
	var ed = tinyMCE.get('Contents');
	form.find('[name="Category"]').val('');
	form.find('[name="Title"]').val('');
	form.find('[name="Description"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Listing.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.editlisting = function (m, form) {
	form.find('[name="Category"]').val('');
	form.find('[name="Title"]').val('');
	form.find('[name="Description"]').val('');
	//form.find('[name="Contents"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Listing.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.addclient = function (m, form) {
	form.find('[name="CompanyName"]').val('');
	form.find('[name="BusinessType"]').val('');
	form.find('[name="CompanyId"]').val('');
	form.find('[name="JobsRole1"]').val('');
	form.find('[name="ContactName1"]').val('');
	form.find('[name="ContactMobile1"]').val('');
	form.find('[name="PaymentRole"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Client.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.editclient = function (m, form) {
	form.find('[name="CompanyName"]').val('');
	form.find('[name="BusinessType"]').val('');
	form.find('[name="CompanyId"]').val('');
	form.find('[name="JobsRole1"]').val('');
	form.find('[name="ContactName1"]').val('');
	form.find('[name="ContactMobile1"]').val('');
	form.find('[name="PaymentRole"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Client.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.addsupplier = function (m, form) {
	form.find('[name="CompanyName"]').val('');
	form.find('[name="BusinessType"]').val('');
	form.find('[name="CompanyId"]').val('');
	form.find('[name="JobsRole1"]').val('');
	form.find('[name="ContactName1"]').val('');
	form.find('[name="ContactMobile1"]').val('');
	form.find('[name="PaymentRole"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Client.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.AddItemPOS = function (m, form) {
	form.find('[name="Department"]').val('');
	form.find('[name="ItemName"]').val('');
	form.find('[name="ItemPrice"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "ManagePOSItem.php";
	//location.reload() ;
	
};

Beesoft.ajaxFormCb.EditItemPOS = function (m, form) {
	form.find('[name="Department"]').val('');
	form.find('[name="ItemName"]').val('');
	form.find('[name="ItemPrice"]').val('');
	form.find('[name="ItemId"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "ManagePOSItem.php";
	//location.reload() ;
	
};



Beesoft.ajaxFormCb.AddCourse = function (m, form) {
	form.find('[name="course_name"]').val('');
	form.find('[name="duration"]').val('');
	form.find('[name="dives_day"]').val('');
	form.find('[name="theory_day"]').val('');
	form.find('[name="price"]').val('');
	form.find('[name="dive_star"]').val('');
	form.find('[name="Department"]').val('');
	

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "course.php";
	//location.reload() ;
	
};

Beesoft.ajaxFormCb.EditCourse = function (m, form) {
	form.find('[name="course_name"]').val('');
	form.find('[name="duration"]').val('');
	form.find('[name="dives_day"]').val('');
	form.find('[name="theory_day"]').val('');
	form.find('[name="price"]').val('');
	form.find('[name="dive_star"]').val('');
	form.find('[name="Department"]').val('');
	form.find('[name="ItemId"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "course.php";
	//location.reload() ;
	
};



Beesoft.ajaxFormCb.AddNewActivities = function (m, form) {
	form.find('[name="activitie_code"]').val('');
	form.find('[name="activitie_name"]').val('');
	form.find('[name="time"]').val('');
	form.find('[name="time_type"]').val('');
	form.find('[name="price_guide"]').val('');
	form.find('[name="Department"]').val('');
	form.find('[name="ItemPrice"]').val('');
	

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "activities.php";
	//location.reload() ;
	
};

Beesoft.ajaxFormCb.EditNewActivities = function (m, form) {
	form.find('[name="activitie_code"]').val('');
	form.find('[name="activitie_name"]').val('');
	form.find('[name="time"]').val('');
	form.find('[name="time_type"]').val('');
	form.find('[name="price_guide"]').val('');
	form.find('[name="Department"]').val('');
	form.find('[name="ItemPrice"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "activities.php";
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.editsupplier = function (m, form) {
	form.find('[name="CompanyName"]').val('');
	form.find('[name="BusinessType"]').val('');
	form.find('[name="CompanyId"]').val('');
	form.find('[name="JobsRole1"]').val('');
	form.find('[name="ContactName1"]').val('');
	form.find('[name="ContactMobile1"]').val('');
	form.find('[name="PaymentRole"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "Client.php";
	//location.reload() ;
	
};



	// Update Category
	  function UpdateCategory(categoryId, MainCategory) {
		
		var modal = $('#UpdateCategoryModal');
		
		//modal.find('input[name="Name"]').val(Name);
		
		 var selectval = categoryId;
		
		 $.ajax({
            url: 'CategoryUpdate.php',
            type: 'POST',
            data: {categoryidNew: selectval},
            success: function(data) {
			//alert(data);
              $('#result').html(data);
			  
            }
        });
		
	//	data: {categoryidNew: categoryId};
        modal.find('input[name="category_id"]').val(categoryId);
		modal.find('select[name="MainCategory"]').val(MainCategory);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.UpdateCategory = function () {
           location.reload();
            modal.modal('hide');
        };
	};


	// Delete Category
	  function deleteCategory(categoryId, categoryname) {
		
		var modal = $('#deleteCategoryModal');
		
        modal.find('input[name="category_id"]').val(categoryId);
        modal.find('.category').text(categoryname);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteCategory = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	
	  // Delete Listing
	  function deleteListing(ListingId, listingname) {
		
		var modal = $('#deleteListingModal');
		
        modal.find('input[name="listing_id"]').val(ListingId);
        modal.find('.listing').text(listingname);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteListing = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	
	
/// Add Task


 var dateFormat = function () {
        var    token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
            timezoneClip = /[^-+\dA-Z]/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len) val = "0" + val;
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
            if (isNaN(date)) throw SyntaxError("invalid date");
    
            mask = String(dF.masks[mask] || mask || dF.masks["default"]);
    
            // Allow setting the utc argument via the mask
            if (mask.slice(0, 4) == "UTC:") {
                mask = mask.slice(4);
                utc = true;
            }
    
            var    _ = utc ? "getUTC" : "get",
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
                    d:    d,
                    dd:   pad(d),
                    ddd:  dF.i18n.dayNames[D],
                    dddd: dF.i18n.dayNames[D + 7],
                    m:    m + 1,
                    mm:   pad(m + 1),
                    mmm:  dF.i18n.monthNames[m],
                    mmmm: dF.i18n.monthNames[m + 12],
                    yy:   String(y).slice(2),
                    yyyy: y,
                    h:    H % 12 || 12,
                    hh:   pad(H % 12 || 12),
                    H:    H,
                    HH:   pad(H),
                    M:    M,
                    MM:   pad(M),
                    s:    s,
                    ss:   pad(s),
                    l:    pad(L, 3),
                    L:    pad(L > 99 ? Math.round(L / 10) : L),
                    t:    H < 12 ? "a"  : "p",
                    tt:   H < 12 ? "am" : "pm",
                    T:    H < 12 ? "A"  : "P",
                    TT:   H < 12 ? "AM" : "PM",
                    Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                    o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                    S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                };
    
            return mask.replace(token, function ($0) {
                return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
            });
        };
    }();
    
    // Some common format strings
    dateFormat.masks = {
        "default":      "ddd mmm dd yyyy HH:MM:ss",
        shortDate:      "m/d/yy",
        mediumDate:     "mmm d, yyyy",
        longDate:       "mmmm d, yyyy",
        fullDate:       "dddd, mmmm d, yyyy",
        shortTime:      "h:MM TT",
        mediumTime:     "h:MM:ss TT",
        longTime:       "h:MM:ss TT Z",
        isoDate:        "yyyy-mm-dd",
        isoTime:        "HH:MM:ss",
        isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
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

startTime = new Date( today.getTime() + today.getTimezoneOffset() * 60000  );
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
        Beesoft.ajaxFormCb.addtaskpopup = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	

/// Add Area
	
Beesoft.ajaxFormCb.addarea = function (m, form) {
	form.find('[name="Name"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
	
};

	// Update Category
	  function UpdateArea(areaId, MainArea) {
		
		var modal = $('#UpdateAreaModal');
		
		//modal.find('input[name="Name"]').val(Name);
		
		 var selectval =areaId;
		
		 $.ajax({
            url: 'AreaUpdate.php',
            type: 'POST',
            data: {areaidNew: selectval},
            success: function(data) {
			//alert(data);
              $('#result').html(data);
			  
            }
        });
		
	//	data: {categoryidNew: categoryId};
        modal.find('input[name="area_id"]').val(areaId);
		modal.find('select[name="MainArea"]').val(MainArea);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.UpdateArea = function () {
           location.reload();
           modal.modal('hide');
        };
	};


	// Delete Area
	  function deleteArea(areaId, areaname) {
		
		var modal = $('#deleteAreaModal');
		
        modal.find('input[name="area_id"]').val(areaId);
        modal.find('.area').text(areaname);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteArea = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	
	
/// Add Activity
	
Beesoft.ajaxFormCb.addactivity = function (m, form) {
	form.find('[name="Name"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
	
};

	// Update Activity
	  function UpdateActivity(activityId, MainActivity) {
		
		var modal = $('#UpdateActivityModal');
		
		//modal.find('input[name="Name"]').val(Name);
		
		 var selectval =activityId;
		
		 $.ajax({
            url: 'ActivityUpdate.php',
            type: 'POST',
            data: {activityidNew: selectval},
            success: function(data) {
			//alert(data);
              $('#result').html(data);
			  
            }
        });
		
	//	data: {categoryidNew: categoryId};
        modal.find('input[name="activity_id"]').val(activityId);
		modal.find('select[name="MainActivity"]').val(MainActivity);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.UpdateActivity = function () {
           location.reload();
            modal.modal('hide');
        };
	};


	// Delete Activity
	  function deleteActivity(activityId, activityname) {
		
		var modal = $('#deleteActivityModal');
		
        modal.find('input[name="activity_id"]').val(activityId);
        modal.find('.activity').text(activityname);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteActivity = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	
	    // Compose E-mail
    Beesoft.admin.composeEmail = function (email) {
        var modal = $('#composeModal');

        if (email) modal.find('input[name="to"]').val(email);

        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.sendEmail = function () {
            modal.modal('hide');
        };
    };
	
	
	// Update Quantity
	  function updateQuantity(itemId) {
		
		var modal = $('#updateQuantityModal');
		
		//modal.find('input[name="Name"]').val(Name);
		
		 
		
	//	data: {categoryidNew: categoryId};
        modal.find('input[name="item_id"]').val(itemId);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.updateQuantitys = function () {
           myFunctionP(itemId);
		   $('#QuantityVal').val('0');
           modal.modal('hide');
        };
	};
	
	
		// Update Quantity
	  function updateQuantityItem(itemId) {
		  		
		var modal = $('#updateQuantityItemModal');
		
		//modal.find('input[name="Name"]').val(Name);
		
		 
		
	//	data: {categoryidNew: categoryId};
        modal.find('input[name="item_id"]').val(itemId);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.updateQuantitysItem = function () {
           location.reload();
        };
	};
	
	// Sort Section
    Beesoft.admin.sortSectino = function () {
        var modal = $('#sectinoModal');
		$('#SectionLoad').load( 'action/SectionLoad.php' + '#MySectionLoad' );
        modal.modal('show');     
    };
	
		// Sort Activities
    Beesoft.admin.sortActivities = function () {
        var modal = $('#activitiesModal');
		$('#ActivitiesLoad').load( 'action/ActivitiesLoad.php' + '#MyActivitiesLoad' );
        modal.modal('show');
    };

	// Reminder
    Beesoft.admin.reminder = function () {
        var modal = $('#reminderModal');
		$('#ReminderLoad').load( 'action/ReminderLoad.php' + '#MyReminderLoad' );
        modal.modal('show');
    };
	
	// Notes
    Beesoft.admin.notes = function () {
        var modal = $('#notesModal');
		$('#NotesLoad').load( 'action/NotesLoad.php' + '#MyNotesLoad' );
        modal.modal('show');
    };
	
	// Open DeskPlan
    Beesoft.admin.opendeskplan = function () {
        var modal = $('#openDeskPlanModal');
        modal.modal('show');
    };
	
	// Open Client
    Beesoft.admin.openclient = function () {
        var modal = $('#openClientModal');
        modal.modal('show');
    };
	
	// New Reminder
    Beesoft.admin.openNewReminer = function () {
        var modal = $('#openNewReminerModal');
        modal.modal('show');
    };
	
	Beesoft.ajaxFormCb.NewReminder = function (m, form) {
	form.find('[name="reminder_title"]').val('');
	form.find('[name="reminderN"]').val('');
	form.find('[name="status_reminder"]').val('');
	form.find('[name="start_dateRe"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	var modal = $('#openNewReminerModal');
    modal.modal('hide');
	var modals = $('#reminderModal');
	$('#ReminderLoad').load( 'action/ReminderLoad.php' + '#MyReminderLoad' );
    modals.modal('show');
	
};

	// New Notes
    Beesoft.admin.openNewNotes = function () {
        var modal = $('#openNewNotesModal');
        modal.modal('show');
    };
	
	Beesoft.ajaxFormCb.NewNotes = function (m, form) {
	form.find('[name="Nremarks"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	var modal = $('#openNewNotesModal');
    modal.modal('hide');
	var modals = $('#notesModal');
	$('#NotesLoad').load( 'action/NotesLoad.php' + '#MyNotesLoad' );
    modals.modal('show');
	
};
	
	// Open Activitie
    Beesoft.admin.openActivitie = function () {
        var modal = $('#openActivitieModal');
		$('#DiversLoad').load( 'action/DiversLoad.php' + '#MyDiversLoad' );
        modal.modal('show');
    };
	
	    Beesoft.admin.openCourse = function (Courseid,PaymentFinal,Remarks,StatusDesk,ShuvarNum) {
        var modal = $('#openCourseCModal');
		modal.find('input[name="Courseid"]').val(Courseid);
		modal.find('input[name="PaymentFinal"]').val(PaymentFinal);
		modal.find('input[name="ShuvarNum"]').val(ShuvarNum);
		modal.find('input[name=optionStatus][value=' + StatusDesk + ']').prop('checked',true);
		modal.find('div[id="textremarks"]').html(Remarks);
        modal.modal('show');
    };
	
Beesoft.ajaxFormCb.updateCourseA = function (m, form) {
	form.find('[name="Courseid"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
    modal.modal('hide');
	
};

Beesoft.ajaxFormCb.addMailing = function (m, form) {
	var ed = tinyMCE.get('Contents');
	form.find('[name="Title"]').val('');
	var value = form.find('[name="tab"]').val();

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = value;
	//location.reload() ;
	
};


Beesoft.ajaxFormCb.editMailing = function (m, form) {
	form.find('[name="Title"]').val('');
	var value = form.find('[name="tab"]').val();

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = value;
	//location.reload() ;
	
};

// Delete Listing
	  function deleteMailing(MailingId, Mailingemail) {
		
		var modal = $('#deleteMailingModal');
		
        modal.find('input[name="mailing_id"]').val(MailingId);
        modal.find('.mailing').text(Mailingemail);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteMailing = function () {
           location.reload();
            modal.modal('hide');
        };
	};
	
	
	
	// Open Activitie
    Beesoft.admin.AddActivities = function () {
        var modal = $('#openAddActivitieModal');
		//$('#DiversLoad').load( 'action/DiversLoad.php' + '#MyDiversLoad' );
        modal.modal('show');
    };
	
	
Beesoft.ajaxFormCb.AddActivitie = function (m, form) {
	form.find('[name="Notes"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
    modal.modal('hide');
	
};	


Beesoft.ajaxFormCb.AddRentOpen = function (m, form) {
	form.find('[name="ClientId"]').val('');
	form.find('[name="ActiviteType"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = 'RentDeskPlan.php';
};	

Beesoft.ajaxFormCb.AddItem = function (m, form) {
	form.find('[name="CategoryParent"]').val('');
	form.find('[name="ManufacturerParent"]').val('');
	form.find('[name="ModelParent"]').val('');
	form.find('[name="ItemSize"]').val('');
	form.find('[name="SKUdeep"]').val('');
	form.find('[name="rent"]').val('');

	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	window.location.href = "ManageItem.php";
	//location.reload() ;
	
};

Beesoft.ajaxFormCb.SendClientPush = function (m, form) {


	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
};	


Beesoft.ajaxFormCb.classsettings = function (m, form) {
    form.find('[name="CheckMinClientType"]').val('');
	Beesoft.alert(Beesoft.trans('db_saved'), 1, form);
	location.reload();
};	

