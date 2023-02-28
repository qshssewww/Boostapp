// reCAPTCHA helper
BeePOS.recaptcha = {
	html: function () {
    	var template = $('#recaptchaTemplate');
		
		return template.length ? template.html() : '';
	},

	get: function () {
		if (BeePOS.recaptcha.html() == '') return;

		var protocol = window.location.protocol == 'https:' ? 'https' : 'http';
		var public_key = $('#recaptcha_public_key').val();

		$.getScript(protocol+'://www.google.com/recaptcha/api/js/recaptcha_ajax.js', function () {
			Recaptcha.create(public_key, 'recaptcha_widget', {
				theme : 'custom',
				custom_theme_widget: 'recaptcha_widget'
			});
		});
	}
};




jQuery(function ($) {
	// Render the reCAPTCHA input and image
	$('#reminderModal, #activationModal, #signupModal').on('show.bs.modal', function (e) {
		$('.modal .recaptcha').html('');	
		$(e.currentTarget).find('.recaptcha').html( BeePOS.recaptcha.html() );
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
			$.get(BeePOS.options.ajaxUrl, {action: 'getContacts'}, function(response) {
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
		BeePOS.alert(BeePOS.trans('connecting') + $(this).text() + '...', 0, $(e.delegateTarget));
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


BeePOS.ajaxFormCb.POSPause = function (m, form) {
	form.find('[name="TempsIds"]').val('');

	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$('#GetItems').load('Clean.php' + '#MeItem');
		
};

BeePOS.ajaxFormCb.AddRemarks = function (m, form) {
	form.find('[name="TempsIdRemarks"]').val('');
    var TempId = document.getElementById('TempId').value;
	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$("#TempsIdRemarks").val(TempId);
	$("#RemarksPopup").fadeOut("fast");
		
};

BeePOS.ajaxFormCb.AddDiscount = function (m, form) {
	form.find('[name="TempsIdDiscount"]').val('');
    var TempId = document.getElementById('TempId').value;
	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$("#TempsIdDiscount").val(TempId);
	$('#GetItems').load('UpdatesItems.php?TempId='+TempId+'#MeItem');
	$("#DiscountPopup").fadeOut("fast");
		
};


BeePOS.ajaxFormCb.AddOpening = function (m, form) {
	form.find('[name="OpeningAmount"]').val('');
	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$('#IframeOpening').attr('src', 'POSPrint/PrintOpening.php');
	window.setTimeout(function(){
    parent.location.href = "index.php";
    }, 1000);	
		
};

BeePOS.ajaxFormCb.AddDiscountItem = function (m, form) {
	form.find('[name="DiscountTempId"]').val('');
    var TempId = document.getElementById('DiscountTempId').value;
	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$("#DiscountTempId").val(TempId);
	$('#GetItems').load('UpdatesItems.php?TempId='+TempId+'#MeItem');
		
};

BeePOS.ajaxFormCb.AddVat = function (m, form) {
	form.find('[name="TempsIdVat"]').val('');
    var TempId = document.getElementById('TempId').value;
	BeePOS.alert(BeePOS.trans('db_saved'), 1, form);
	$("#TempsIdVat").val(TempId);
	$('#GetItems').load('UpdatesItems.php?TempId='+TempId+'#MeItem');
	$("#VatPopup").fadeOut("fast");
		
};



   
 var spinnerVisible = false;
    function showProgress() {
        if (!spinnerVisible) {
            $("div#spinner").fadeIn("fast");
            spinnerVisible = true;
        }
    };
    function hideProgress() {
        if (spinnerVisible) {
            var spinner = $("div#spinner");
            spinner.stop();
            spinner.fadeOut("fast");
            spinnerVisible = false;
        }
    };  