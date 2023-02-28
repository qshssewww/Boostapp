import jsCookie from '@modules/Cookie.js';
import {jsModal} from '@modules/LoginModal.js';

function LoginSection() {
	function login() {
		this.options = {
			cookieBlockedPhone: theme.prefix + '_blockedSendPhone', // blocking cookie name
			classLoading: 'btn--spinner',
			classInputCode: 'input-code',
			classMessageBox: 'js--form-group_message',
			classMessageBoxSuccess: 'form-group_message--success',
			classMessageBoxError: 'form-group_message--error',
			classError: 'error',
			nameInputEmail: '[name="username"]'
		};
		this.selectors = {
			step: document.querySelectorAll('.js--step'),
			linkTo: document.querySelectorAll('.js--link-to'),
			errorBox: document.querySelector('.' + this.options.classMessageBox),
			putPhone: document.querySelector('.js--put-phone'),
			formPhone: document.querySelector('[name="loginWithPhone"]'),
			inputPhone: document.querySelector('.js--input-phone'),
			sendPhoneAgain: document.getElementById('send-phone-again'),
			formCode: document.querySelector('[name="loginWithCode"]'),
			inputsCode: document.querySelectorAll('.js--input-code'),
			inputCode: document.querySelector('[name="otp"]'),
			inputEmail: document.querySelector('[name="username"]'),
			formUsername: document.querySelector('[name="loginWithUsername"]'),
			formReminder: document.querySelector('[name="loginWithReminder"]')
		};

		this.setTimer();

		if (document.querySelector('.' + theme.prefix + '__modal') !== null) {
			jsModal();
		}

		for (let i = 0; i < this.selectors.linkTo.length; i++) {
			this.selectors.linkTo[i].addEventListener('click', this.linkToClick.bind(this), false);
		}

		document.querySelector(this.options.nameInputEmail).addEventListener('input', function(e) {
			this.validateEmail(e.currentTarget);
		}.bind(this), false);

		this.selectors.inputPhone.addEventListener("keypress", function(e) {
			this.isNumberKey(e);
		}.bind(this), false);

		for (let i = 0; i < this.selectors.inputsCode.length; i++) {
			this.selectors.inputsCode[i].addEventListener('keydown', this.inputsCodeKeypress.bind(this), false);
			this.selectors.inputsCode[i].addEventListener('keyup', this.moveToNextInputDigit.bind(this), false);
		}

		this.selectors.inputCode.addEventListener('blur input', function(e) {
			this.setCode($(e.currentTarget).val());
		}.bind(this), false);

		this.selectors.sendPhoneAgain.addEventListener('click', function(e) {
			e.preventDefault();
			const el = e.currentTarget;
			if (jsCookie.get(this.options.cookieBlockedPhone) !== null) {
				this.goTo(el.getAttribute('data-to'));
				return false;
			}

			// send phone number if there isn't a blocking cookie
			this.clearCode();
			this.submitFormPhone(this.selectors.formPhone);
		}.bind(this), false);

		this.selectors.formPhone.addEventListener('submit', function(e) {
			e.preventDefault();
			const form = e.currentTarget;
			this.submitFormPhone(form);
		}.bind(this), false);

		this.selectors.formCode.addEventListener('submit', function(e) {
			e.preventDefault();
			const form = e.currentTarget;
			const getCode = this.getCode();
			if (getCode == '') {
				return false;
			}
			this.focusFirst(form);

			this.selectors.inputCode.value = getCode;
			this.submitForm(form, function(response) {
				this.focusFirst(form);

				// the [response] variable is a pseudo response from the server
				// var response = {
				// 	status: 200,
				// 	success: false, // true if the user approved the phone
				// 	blocked: false,
				// 	message: "Mobile number verification failed",
				// 	count: 1
				// };

				if ((response.status === 200 || response.status === 300) && response.success) {
					const formBtn = form.querySelector('[type="submit"]');
					formBtn.classList.add(this.options.classLoading);

					window.location.reload();
				} else {
					this.showError(response, form, response.message);
				}
			}.bind(this));
		}.bind(this), false);

		this.selectors.formUsername.addEventListener('submit', function(e) {
			e.preventDefault();
			const form = e.currentTarget;
			// const emailValid = this.validateEmail(form.querySelector(this.options.nameInputEmail));
			const emailValid = true;
			if (!emailValid) {
				return false;
			}
			this.submitForm(form, function(response) {
				if (response.status === 200 && response.success) {
					const formBtn = form.querySelector('[type="submit"]');
					formBtn.classList.add(this.options.classLoading);

					window.location.reload();
				} else if (!response.success && response.message === "The username or password is incorrect") {
					this.showError(response, form, theme.translation.loginUsernameError);
				} else {
					this.showError(response, form, response.message);
				}
			}.bind(this));
		}.bind(this), false);

		this.selectors.formReminder.addEventListener('submit', function(e) {
			e.preventDefault();
			const form = e.currentTarget;
			const emailValid = this.validateEmail(form.querySelector(this.options.nameInputEmail));
			if (!emailValid) {
				return false;
			}
			this.submitForm(form, function(response) {

				if (response.status === 200 && response.success) {
					this.showSuccess(response, form, response.message);
					console.log('Send reminder email');
				} else {
					this.showError(response, form, response.message);
				}
			}.bind(this));
		}.bind(this), false);
	}

	login.prototype = Object.assign({}, login.prototype, {
		submitFormPhone: function(form) {
			this.hideError(this.selectors.formCode);
			this.clearCode();
			this.submitForm(form, function(response) {
				let phone = this.selectors.inputPhone.value;
				if (phone[0] !== '0') {phone = '0' + phone;}
				const textEl = this.selectors.putPhone;
				const text = theme.translation.loginCodeSent;
				const newText = text.replace('{phone}', phone.slice(0,3) + "-" + phone.slice(3));
				textEl.textContent = newText;

				window.console.log(response);

				// the [response] variable is a pseudo response from the server
				// var response = {
				// 	status: 200,
				// 	success: true,
				// 	blocked: false,
				// 	message: "Error"
				// };

				if (response.status === 200 && response.success) {
					this.setCookie(this.options.cookieBlockedPhone, 30); // alive for 30 seconds
					this.setTimer();

					console.log('Phone send', this);
				} else {
					this.showError(response, form);
				}
			}.bind(this));
		},
		linkToClick: function(e) {
			const el = e.currentTarget;
			const nextStep = el.getAttribute('data-to');
			if (nextStep === "step--phone") {
				this.clearCode();
			}
			const elParent = el.parentNode;
			const form = elParent.querySelector('form');
			if (form !== null && form.querySelector('form input').classList.contains(this.options.classError)) {
				this.hideError(form);
			}
			this.goTo(nextStep);
		},
		inputsCodeKeypress: function(e) {
			const el = e.currentTarget;
			if (this.index(el) === 0 && el.classList.contains(this.options.classError) && el.value != '') {
				this.clearCode();
				const elForm = el.closest('form');
				this.hideError(elForm);
			}
			this.isNumberKey(e);
		},
		focusFirst: function(el) {
			const inputFirst = el.querySelector('input');
			if (inputFirst.value === '') {
				inputFirst.focus();
			}
		},
		clearCode: function() {
			for (let el of this.selectors.inputsCode) {
				el.value = '';
				el.classList.remove(this.options.classError);
			}
			this.selectors.inputCode.value = '';
		},
		isNumberKey: function(e) {
			const charCode = (e.which) ? e.which : e.keyCode;
			if (charCode === 8) {
				this.moveToPrevInputDigit(e);
			} else if (charCode === 46) {
				e.currentTarget.value = '';
			} else if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode > 105 || charCode < 96)) {
				e.preventDefault();
				return false;
			}
			return true;
		},
		getCurrentDigit: function(e) {
			const el = e.currentTarget;
			return parseInt(el.getAttribute('id').replace("digit",""));
		},
		moveToPrevInputDigit: function(e){
			let currentDigit = this.getCurrentDigit(e);
			currentDigit--;
			if (e.currentTarget.value.length === 0 && currentDigit > 0)  {
				const prevEl = document.getElementById('digit' + currentDigit);
				prevEl.value = '';
				prevEl.focus();
			}
		},
		moveToNextInputDigit: function(e){
			const el = e.currentTarget;
			const charCode = (e.which) ? e.which : e.keyCode;
			if (charCode === 9 || charCode === 16) {
				return true;
			}

			let currentDigit = this.getCurrentDigit(e);
			currentDigit++;
			if (el.value.length === 1 && currentDigit <= 6)  {
				const nextEl = document.getElementById('digit' + currentDigit);
				nextEl.blur();
				nextEl.focus();
				nextEl.select();
			}
		},
		validateEmail: function(input) {
			let pattern;
			if (input.value.includes('@')) {
				pattern = /^[\w\._+]+@([\w-]+\.)+[\w-]{2,4}$/;
			} else {
				pattern = /^[\w\d-]+$/;
			}

			if (input.value !== '' && !pattern.test(input.value)) {
				input.classList.add(this.options.classError);
				input.focus();
				return false;
			} else {
				input.classList.remove(this.options.classError);
				return true;
			}
		},
		setCode: function(code) {
			const codeArr = code.split('');
			for (let i = 0; i < this.selectors.inputsCode.length; i++) {
				this.selectors.inputsCode[i].value = codeArr[i];
			}
		},
		getCode: function() {
			let code = "";
			for (let i = 0; i < this.selectors.inputsCode.length; i++) {
				code += this.selectors.inputsCode[i].value;
			}
			return code;
		},
		serialize: function (data) {
			let obj = {};
			for (let [key, value] of data) {
				if (obj[key] !== undefined) {
					if (!Array.isArray(obj[key])) {
						obj[key] = [obj[key]];
					}
					obj[key].push(value);
				} else {
					obj[key] = value;
				}
			}
			return obj;
		},
		submitForm: async function(form, callback = undefined) {
			if (form === undefined || form.getAttribute('action') == '') {
				return false;
			}
			this.hideError(form);
			const formBtn = form.querySelector('[type="submit"]');
			formBtn.classList.add(this.options.classLoading);

			try {
				const data = new FormData(form);
				const formObj = this.serialize(data);

				// Remove 'https://jsonplaceholder.typicode.com/posts' after real request!

				const response = await fetch(form.getAttribute('action'), {
					method: form.getAttribute('method'),
					credentials: "same-origin",
					headers: {
						"Content-Type": "application/json",
						"X-Requested-With": "XMLHttpRequest"
					},
					body: JSON.stringify(formObj)
				});

				// check for error response
				if (!response.ok) {
					throw new Error('Status Code Error: ' + response.status);
				}
				const contentType = response.headers.get('content-type');
				if (!contentType || !contentType.includes('application/json')) {
					throw new TypeError("The response is not JSON");
				}

				const json = await response.json(); // returns a Promise that resolves with the result of parsing as JSON.

				formBtn.classList.remove(this.options.classLoading);
				const nextStep = formBtn.getAttribute('data-to');
				if (nextStep != undefined) {
					this.goTo(nextStep);
				}
				if (callback !== undefined) {
					callback(json);
				}

			} catch (error) {
				console.error('A problem with fetch: ', error.message);
				this.showError(error, form, error.message);
			}
		},
		hideError: function(form) {
			form.querySelector('.' + this.options.classMessageBox).textContent = '';
			form.querySelector('.' + this.options.classMessageBox).classList.remove(this.options.classMessageBoxSuccess);
			form.querySelector('.' + this.options.classMessageBox).classList.remove(this.options.classMessageBoxError);
			for (let item of form.querySelectorAll('input')) {
				item.classList.remove(this.options.classError);
			}
		},
		showError: function(errors, form, errorText = undefined) {
			const maxTimes = 5;
			const message = form.querySelector('.' + this.options.classMessageBox);
			if (message == null) {
				return false;
			}

			// TODO: Replace {times} in error message
			// TODO: קוד שגוי, נותרו לך {times} נסיונות נוספים
			if (errors.blocked) {
				this.goTo('step--lock');
			} else if (errorText != undefined && errors.message == "Mobile number verification failed") {
				const timesLeft = maxTimes - (errors.count != undefined ? errors.count : 1);
				const errorTextNew = errorText.replace('{times}', timesLeft);
				message.classList.remove(this.options.classMessageBoxSuccess);
				message.classList.add(this.options.classMessageBoxError);

				message.textContent = errorTextNew;
			} else if (errorText != undefined && errors.message != "") {
				message.classList.remove(this.options.classMessageBoxSuccess);
				message.classList.add(this.options.classMessageBoxError);

				message.textContent = errorText;
			}

			for (let item of form.querySelectorAll('input')) {
				item.classList.add(this.options.classError);
			}
		},
		showSuccess: function(success, form, messageText) {
			window.console.log(success, form, messageText);

			const message = form.querySelector('.' + this.options.classMessageBox);
			if (message == null) {
				return false;
			}

			if (messageText != undefined) {
				message.classList.remove(this.options.classMessageBoxError);
				message.classList.add(this.options.classMessageBoxSuccess);

				message.textContent = messageText;
			}

			for (let item of form.querySelectorAll('input')) {
				item.classList.remove(this.options.classError);
			}
		},
		goTo: function(stepClass) {
			const toEl = document.getElementById(stepClass);
			if (toEl.length == 0 || (toEl.offsetWidth > 0 && toEl.offsetHeight > 0)) {
				return false;
			}

			for (let item of this.selectors.step) {
				item.style.display = "none";
			}
			toEl.style.display = "block";
			this.focusFirst(toEl);
		},
		setCookie: function(name, seconds, value = undefined) {
			let date = new Date();
			let miliSec = date.getTime() + (seconds * 1000);
			date.setTime(date.getTime() + (seconds * 1000));
			jsCookie.set(name, (value === undefined ? miliSec : value), date);
		},
		insertTimer: function(timer, ready) {
			let sec = parseInt((ready - Date.parse(new Date())) / 1000);
			timer.textContent = '00:' + (sec < 10 ? '0' + sec : sec);
			return sec;
		},
		setTimer: function() {
			const cookie = jsCookie.get(this.options.cookieBlockedPhone);
			if (cookie !== null) {
				const form = this.selectors.inputPhone.closest('form');
				const btn = form.querySelector('[type="submit"]');
				const btnBlockedText = btn.querySelector('.btn--text-blocked');
				const ready = parseInt(cookie);
				const timer = btnBlockedText.querySelector('b');
				this.insertTimer(timer, ready);
				btn.classList.add('btn--blocked');

				const newInterval = setInterval(function() {
					let sec = this.insertTimer(timer, ready);
					if (sec <= 1) {
						btn.classList.remove('btn--blocked');
						clearInterval(newInterval);
					}
				}.bind(this), 1000);
				return false;
			}
			return true;
		},
		index: function (el) {
			if (!el) return -1;
			let i = 0;
			while (el = el.previousElementSibling) {i++;}
			return i;
		}
	});

	return new login();
}

document.addEventListener("DOMContentLoaded", function (event) {
	LoginSection();
});