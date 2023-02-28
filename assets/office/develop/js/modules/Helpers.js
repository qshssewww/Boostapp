import moment from "moment";
moment.locale(document.documentElement.lang || 'he');
import errorModal from "@partials/errorModal";
import iframeContentBox from "@partials/item/iframeContentBox.hbs";
import {getHasRefundCredit} from "@modules/checkout/CheckoutHelpers";
import {Lang} from "@modules/Lang";
import {buildModal, closeModal, openModal} from "@modules/Modal";
import jsCookie from "@modules/Cookie";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {getCartOriginalPrice} from "@modules/cart/CartHelpers";
const {
	globalUrl, isMobile, bsappModalEl, bsappErrorModalEl
} = cartGlobalVariable;

const classError = 'error';

export function fireEvent(element,event) {
	if (document.createEventObject){
		// dispatch for IE
		var evt = document.createEventObject();
		return element.fireEvent('on'+event,evt)
	}
	else{
		// dispatch for firefox + others
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent(event, true, true ); // event type,bubbling,cancelable
		return !element.dispatchEvent(evt);
	}
}

export function additionalResizeEvents() {
	// media query event handler
	if (matchMedia) {
		const mq = window.matchMedia(isMobile.mediaQuery);
		// mq.addEventListener('change', widthChange);
		window.addEventListener('resize', e => {
			widthChange(mq);
		});
		widthChange(mq);
	}

	const goToPrevPageBtn = document.querySelectorAll('.js--open-confirm-exit');
	goToPrevPageBtn.forEach(button => {
		button.addEventListener('click', (e) => {
			const errorMassage = getHasRefundCredit() ? Lang('exit_refund_process_error') : '';
			e.preventDefault();
				showErrorModal({
					classNameModal: 'modal--smaller',
					textKey: 'cart_exit_sure_text',
					notM: true,
					error: errorMassage,
					linkBtn: {
						textKey: 'yes_sure_app_booking',
						class: 'theme--link js--to-previous-page'
					}
				});
		});
	});

	document.addEventListener('click', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--to-previous-page')) {
				e.preventDefault();
				// let toHistory = -1;
				// const referrerPrev = document.referrer.split('/');
				// if (document.body.classList.contains('bsapp__checkout-page')
				// 	&& referrerPrev[referrerPrev.length - 1] === 'cart.php') {
				// 	toHistory = -2;
				// }
				// setBodyPreload(true);
				// window.history.go(toHistory);
				window.location.href = '/office';
				break;

			} else if (target.matches('.js--open-pdf-modal')) {
				const elHref = target.getAttribute('href');
				if (!bsappModalEl) return false;
				e.preventDefault();
				buildModal({
					el: bsappModalEl,
					className: 'modal-dialog--pdf',
					html: iframeContentBox({
						showLikeModal: false,
						showWithoutHeader:true,
						iframeUrl: elHref,
						iframeHeight: 446
					})
				}).then(function () {
					openModal({modalEl: bsappModalEl});
				});

				break;
			}
		}
	});

	window.addEventListener("resize", function() {
		if (document.activeElement.tagName == "INPUT") {
			document.activeElement.scrollIntoView({behavior: "smooth", block: "start"});
		}
	})
}

function widthChange(mq) {
	isMobile.matches = mq.matches;
	// console.log('isMobile', isMobile.matches);

	// First we get the viewport height and we multiple it by 1% to get a value for a vh unit
	const vh = window.innerHeight;
	// Then we set the value in the --vh custom property to the root of the document
	document.documentElement.style.setProperty('--vh', `${vh}`);

	closeModal('bsappDropdown');

	const generalItemPriceElement = document.getElementById('generalItemPrice');
	if (generalItemPriceElement && document.body.classList.contains('bsapp__cart-page')) {
		if (isMobile.matches || window.matchMedia("(pointer: coarse)").matches) {
			generalItemPriceElement.setAttribute('data-readonly', '');
			generalItemPriceElement.setAttribute('disabled', 'disabled');
		} else {
			generalItemPriceElement.removeAttribute('data-readonly');
			generalItemPriceElement.removeAttribute('disabled');
		}

	} else if (document.body.classList.contains('bsapp__checkout-page') && !isMobile.matches) {
		closeModal('bsappHalfSidebar');
	}
}

export function writeJsCookie(name, data, dayCount = null) {
	let date = null;
	if (dayCount) {
		date = new Date();
		date.setTime(date.getTime() + (dayCount * 24 * 60 * 60 * 1000));
	}
	jsCookie.set(name,  JSON.stringify(data), date);
}

export function readJsCookie(name) {
	const cookieValue = jsCookie.get(name);
	if (cookieValue !== null) {
		return JSON.parse(cookieValue);
	}
	return null;
}

export function writeSession(name, data) {
	window.sessionStorage.setItem(name,  JSON.stringify(data));
}

export function readSession(name) {
	const value = window.sessionStorage.getItem(name);
	if (value !== null) {
		return JSON.parse(value);
	}
	return null;
}

async function createFetch(opts) {
	try {
		const response = await fetch(opts.url, opts);
		if (!response.ok) {
			throw Error(response.statusText);

		} else {
			if (!document.getElementById('preloader').classList.contains('hide-preloader')) {
				setBodyPreload(false);
			}
			return response.json();
		}

	} catch (error) {
		console.error('[createFetch] There has been a problem with your fetch operation: ', error);
		if (!document.getElementById('preloader').classList.contains('hide-preloader')) {
			setBodyPreload(false);
		}
		showErrorModal({error: error});
	}
}

export async function sendFetch(url = '', data = null, bodyPreload = true) {
	if (bodyPreload) {
		setBodyPreload(true);
	}

	const opts = {
		url,
		method: data !== null ? 'POST' : 'GET'
	};
	if (data !== null) {
		const isNotFormData = !(data instanceof FormData);
		if (isNotFormData) {
			opts.headers = {
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			};
		}
		opts.body = isNotFormData ? JSON.stringify(data) : data;
	}

	return await createFetch(opts);
}

export function getOffset(el) {
	const rect = el.getBoundingClientRect();
	return {
		left: rect.left + window.scrollX,
		top: rect.top + window.scrollY
	};
}

export function getObjectSize(obj) {
	let size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
}

export function getIndex(array, value, key = 'id') {
	if (!value) {
		return false;
	}
	return array.findIndex(obj => obj[key].toString() === value.toString());
}

export function getElementHeight(el) {
	const el_style = window.getComputedStyle(el),
		el_display = el_style.display,
		el_position = el_style.position,
		el_visibility = el_style.visibility,
		el_max_height = el_style.maxHeight.replace('px', '').replace('%', '');

	let wanted_height = 0;

	// if its not hidden we just return normal height
	if (el_display !== 'none' && el_max_height !== '0') {
		return parseFloat(el_max_height);
	}

	// the element is hidden so:
	// making the el block, so we can meassure its height but still be hidden
	setStylesOnElement(el, {
		position: 'absolute',
		visibility: 'hidden',
		display: 'block'
	});

	wanted_height = el.offsetHeight;

	// reverting to the original values
	setStylesOnElement(el, {
		position: el_position,
		visibility: el_visibility,
		display: el_display
	});
	return wanted_height;
}

export function getLocaleTime(myDate) {
	return new Date(myDate).toLocaleTimeString(
		'en',
		{ timeStyle: 'short', hour12: false });
}

export function getPriceWithoutZero(price) {
	return (+price).toFixed(2).replace(/\.0+$/,'')
}

function getMonthName(num) {
	const month = [Lang('january'), Lang('february'), Lang('march'), Lang('april'), Lang('may'), Lang('june'), Lang('july'), Lang('august'), Lang('september'), Lang('october'), Lang('november'), Lang('december')];
	return month[num];
}

export function getDayOfWeekName(num) {
	const month = [Lang('sunday'), Lang('monday'), Lang('tuesday'), Lang('wednesday'), Lang('thursday'), Lang('friday'), Lang('saturday')];
	return month[num];
}

export function getFilterByValue(array, key, value) {
	return array.filter(obj => obj[key] && obj[key].toString() === value.toString());
}

export function getDurationType(type = 1) {
	switch (type.toString()) {
		case '2':
			return 'week';
		case '3':
			return 'month';
		case '4':
			return 'year';
		default:
			return 'day';
	}
}

export function getSearchParam(param) {
	const query = new URLSearchParams(window.location.search);
	return query.get(param) !== null ? query.get(param) : null;
}

export function getDateTimeMomentSubtractMin(date, time, duration, durationType = 'minutes') {
	const newDate =  moment(`${date} ${time}`).subtract(duration, durationType);
	return {
		date: moment(newDate).format("YYYY-MM-DD"),
		time: moment(newDate).format("HH:mm")
	};
}

export function setSearchParam(param, value) {
	const query = new URLSearchParams(window.location.search);
	query.set(param, value);
	window.history.pushState({}, document.title, location.pathname + '?' + query.toString());
}

export function deleteSearchParam(param) {
	const query = new URLSearchParams(window.location.search);
	if (!query.has(param)) {
		return false;
	}
	query.delete(param);
	window.history.pushState({}, document.title, location.pathname + (query.toString() ? '?' + query.toString() : ''));
}

export function setFilterByValue(array, key, value, key2, newValue) {
	return array.map(obj => {
		if (obj[key] && obj[key].toString() === value.toString()) {
			return {...obj, [key2]: newValue};
		}

		return obj;
	});
}

export function setDate(date, toString = false, divider = '.') {
	const dateObj = typeof date === 'string' ? new Date(date) : date;
	if (isNaN(dateObj)) {
		throw new Error(`Invalid date: ${date}`);
	}
	let response = '';
	const day = dateObj.getDate();
	const month = dateObj.getMonth();
	if (toString) {
		const monthStr = getMonthName(month);
		const dayOfWeek = getDayOfWeekName(dateObj.getDay());
		response = Lang('day') + ' ' + dayOfWeek + ', ' + day + ' ' + monthStr;
	} else {
		response = ("0" + day).slice(-2) + divider + ("0"+(month+1)).slice(-2) + divider + dateObj.getFullYear().toString();
	}
	return response;
}

export function setTime(start) {
	let startD = new Date(start);
	let startMins = startD.getMinutes();
	return startD.getHours() + ':' + (startMins.toString().length == 1 ? '0' + startMins : startMins);
}

export function setDuration(start, end = undefined) {
	let startD = new Date(start);
	if (end != undefined) {
		let endD = new Date(end);
		const diffMs = endD.getTime() - startD.getTime();
		if (diffMs < 0) {
			return '';
		}
		return setDurationString(Math.abs(diffMs) / 60000);
		// return durationStr + (minutes !== null ? Lang('cal_class_type_minutes') + ' ' + (minutes.toString().length == 1 ? '0' + minutes : minutes) : '');
	}
	return null;
}

export function setDurationString(diffMins, keyMinutes = 'shortening_minute') {
	const hours = diffMins > 60 ? convertHM(diffMins)[0] : null;
	const minutes = diffMins > 0 ? (diffMins <= 60 ? diffMins : convertHM(diffMins)[1]) : null;

	let durationStr = hours !== null ? hours : '';
	if (hours !== null && minutes !== null) {
		durationStr += ' ' + Lang('and') + ' ';
	}
	if (minutes !== null) {
		durationStr += minutes + ' ' + Lang(keyMinutes);
	}
	return durationStr;
}

export function setDateTimeMomentString(date, time) {
	return moment(`${date} ${time}`).format("DD MMMM YYYY | HH:mm");
}

export function setBodyPreload(show) {
	const bodyPreload = document.getElementById('preloader');
	if (show) {
		removeClass(bodyPreload, 'hide-preloader');
	} else {
		addClass(bodyPreload, 'hide-preloader');
	}
}

export function setStylesOnElement(element, styles) {
	Object.assign(element.style, styles);
}

export function setSelectedSelect(select, selectedId) {
	select.value = selectedId;
	const options = Array.from(select.options);
	options.forEach((option, i) => {
		option.removeAttribute('selected');
		if (option.value === selectedId) {
			select.selectedIndex = i;
			option.setAttribute('selected', 'selected');
		}
	});
	// firing the event properly
	fireEvent(select,'change');
}

export function setPhoneWithCountryCode(customerTel) {
	if (!customerTel) {return ''}
	return !customerTel.startsWith('+972') ? '+972' + (customerTel[0] === '0' ? customerTel.substring(1) : customerTel) : customerTel;
}
export function setPhoneWithCountryCodeWithoutPlus(customerTel) {
	if (!customerTel) {return ''}
	return customerTel.startsWith('+') ? customerTel.substring(1) : customerTel;
}

export function substrPhone(phone) {
	if (!phone) {return ''}
	const chunkAfter = phone.startsWith('972') ? 2 : 3;
	const substrPhone = phone.substring(0, chunkAfter) + '-' + phone.substring(chunkAfter);
	return phone.startsWith('972') ? '+' + substrPhone : substrPhone;
}

export function triggerErrorClass(target, addError = false) {
	if (addError) {
		addClass(target, classError);
	} else {
		removeClass(target, classError);
	}
}

function convertHM(num) {
	const hours = (num / 60);
	let nHours = Math.floor(hours);
	const minutes = (hours - nHours) * 60;
	const nMinutes = Math.round(minutes);

	switch (nHours) {
		case 1:
			nHours = Lang('hour');break;
		case 2:
			nHours = Lang('two_hours');break;
		default:
			nHours = nHours + ' ' + Lang('hours');
	}

	return [nHours, nMinutes != 0 ? nMinutes : null];
}

export function copy(aObject) {
	// Prevent undefined objects
	if (!aObject) return aObject;
	let bObject = Array.isArray(aObject) ? [] : {};

	let value;
	for (const key in aObject) {
		// Prevent self-references to parent object
		// if (Object.is(aObject[key], aObject)) continue;

		value = aObject[key];
		bObject[key] = (typeof value === "object") ? copy(value) : value;
	}
	return bObject;
}

export function removeItem(array, key, value) {
	const index = getIndex(array, value, key);
	return index >= 0 ? [
		...array.slice(0, index),
		...array.slice(index + 1)
	] : array;
}

export function changeQty(operation , itemQty, qtyMax = null, qtyMin = 1) {
	let qty = validateQty(itemQty);
	const max = typeof qtyMax === 'number' ? qtyMax : parseInt(qtyMax);
	const min = typeof qtyMin === 'number' ? qtyMin : parseInt(qtyMin);
	if (operation === 'plus') {
		qty += 1;
		// if (!isNaN(max) && qty > max) {qty = max}
	} else {
		qty -= 1;
		if (!isNaN(min) && qty < min) {qty = min}
	}
	return qty;
}

export function validateQty(qty) {
	if((parseFloat(qty) == parseInt(qty)) && !isNaN(qty)) {
		return parseInt(qty); // We have a valid number!
	} else {
		return 1; // Not a number. Default to 1.
	}
}

export function validateOnlyNumber(e, isPrice = false) {
	const el = e.target;
	triggerErrorClass(el);

	const val = el.value;
	const max = el.getAttribute('maxlength');
	if (max && parseInt(max) <= val.length) {
		return true;
	}
	const charCode = (e.which) ? e.which : e.keyCode;
	// console.log(val, val.includes('.'), 'validateOnlyNumber', charCode, !(charCode > 31 && (charCode < 48 || charCode > 57) && (charCode > 105 || charCode < 96)));

	if (el.getAttribute('type') === 'number' && (charCode === 38 || charCode === 40)) {
		return true;
	} else if (charCode === 46) {
		return true;
	} else if (el.getAttribute('pattern') && el.getAttribute('pattern').indexOf('+') > -1 && charCode === 107) {
		return true;
	} else if (el.getAttribute('pattern') && el.getAttribute('pattern').indexOf('.') > -1 && val.indexOf('.') === -1 && (charCode === 110 || charCode === 190)) {
		return true;
	}
	return !(charCode > 31 && (charCode < 48 || charCode > 57) && (charCode > 105 || charCode < 96));
}

export function validateOnlyNumberLetter(e) {
	const regex = new RegExp("^[a-zA-Z0-9]+$");
	return regex.test(e.key);
}

export function validateByRegExp(input) {
	let isValid = false;
	const pattern = input.getAttribute('pattern') ? new RegExp(input.getAttribute('pattern')) : null;
	if (input.value.length === 0
		|| (input.value.length > 0 && pattern !== null && !pattern.test(input.value))
		|| (input.getAttribute('maxlength') && input.value.length > parseInt(input.getAttribute('maxlength')))) {
		input.focus();
		triggerErrorClass(input, true);

	} else {
		triggerErrorClass(input);
		isValid = true;
	}

	// console.log('[validateByRegExp]', isValid, input);
	return isValid;
}

export function toggleAttribute(el, isSet, attrName = 'disabled') {
	// console.log('toggleAttribute', el, isSet, attrName);
	if (el === null) {
		return false;
	}
	if (isSet) {
		el.setAttribute(attrName, attrName);
	} else {
		el.removeAttribute(attrName);
	}
}

export function showErrorModal(data) {
	// console.log(!data.classIcon, 'showErrorModal', data);

	if (!data.langTitleKey) {
		data.langTitleKey = 'pay_attention';
	}
	if (!data.textKey) {
		data.textKey = 'action_cancled';
	}
	if (!data.classIcon) {
		data.classIcon = 'fa-light fa-triangle-exclamation';
	}

	buildModal({
		el: bsappErrorModalEl,
		className: data.classNameModal ? data.classNameModal : 'modal--small',
		html: errorModal(data)
	}).then(function() {
		openModal({modalEl: bsappErrorModalEl});
	});
}

export function closeErrorModal() {
	closeModal(bsappErrorModalEl);
}

export function showClientInLessonModal() {
	showErrorModal({
		textKey: 'cart_client_in_lesson_text',
		bottomBtns: [
			{
				textKey: 'approval',
				jsId: 'removeClientFromLesson'
			}
		]
	});
}

export function getPdfUrl(props) {
	const {el, type, id} = props;
	if (el && el.getAttribute('href') !== null && (el.getAttribute('href') !== '' || el.getAttribute('href') !== '#')) {
		return el.getAttribute('href');
	}
	const origin = window.location.origin;
	const newOrigin = origin.indexOf('localhost') > - 1 ? 'https://devlogin.boostapp.co.il' : origin;
	return `${newOrigin}/office/PDF/Docs.php?DocType=${type}&DocId=${id}`;
}

export function reduceTotal(items, key = 'price') {
	return items.reduce(function (accumulator, item) {
		return accumulator + parseFloat(item[key]);
	}, 0);
}

export function slideUp(element, duration = 400) {
	return new Promise(function (resolve, reject) {
		element.style.height = element.offsetHeight + 'px';
		element.style.transitionProperty = `height, margin, padding`;
		element.style.transitionDuration = duration + 'ms';
		element.offsetHeight;
		element.style.overflow = 'hidden';
		element.style.height = 0;
		element.style.paddingTop = 0;
		element.style.paddingBottom = 0;
		element.style.marginTop = 0;
		element.style.marginBottom = 0;
		window.setTimeout(function () {
			element.style.display = 'none';
			element.style.removeProperty('height');
			element.style.removeProperty('padding-top');
			element.style.removeProperty('padding-bottom');
			element.style.removeProperty('margin-top');
			element.style.removeProperty('margin-bottom');
			element.style.removeProperty('overflow');
			element.style.removeProperty('transition-duration');
			element.style.removeProperty('transition-property');
			resolve(false);
		}, duration)
	})
}

export function slideDown (element, duration = 400) {
	return new Promise(function (resolve, reject) {
		element.style.removeProperty('display');
		let display = window.getComputedStyle(element).display;

		if (display === 'none') {
			display = 'block';
		}
		element.style.display = display;

		let height = element.offsetHeight;
		element.style.overflow = 'hidden';
		element.style.height = 0;
		element.style.paddingTop = 0;
		element.style.paddingBottom = 0;
		element.style.marginTop = 0;
		element.style.marginBottom = 0;
		element.offsetHeight;
		element.style.transitionProperty = `height, margin, padding`;
		element.style.transitionDuration = duration + 'ms';
		element.style.height = height + 'px';
		element.style.removeProperty('padding-top');
		element.style.removeProperty('padding-bottom');
		element.style.removeProperty('margin-top');
		element.style.removeProperty('margin-bottom');
		window.setTimeout(function () {
			element.style.removeProperty('height');
			element.style.removeProperty('overflow');
			element.style.removeProperty('transition-duration');
			element.style.removeProperty('transition-property');
			resolve(false);
		}, duration)
	})
}

export function slideToggle (element, duration = 400) {
	if (window.getComputedStyle(element).display === 'none') {
		return slideDown(element, duration);
	} else {
		return slideUp(element, duration);
	}
}

export function addClass(el, className) {
	if (typeof el === 'string') el = document.querySelectorAll(el);
	const els = (el instanceof NodeList) ? [].slice.call(el) : [el];

	els.forEach(e => {
		if (e === null || hasClass(e, className)) { return; }

		if (e.classList) {
			e.classList.add(className);
		} else {
			e.className += ' ' + className;
		}
	});
}

export function removeClass(el, className) {
	if (typeof el === 'string') el = document.querySelectorAll(el);
	const els = (el instanceof NodeList) ? [].slice.call(el) : [el];

	els.forEach(e => {
		if (e === null) { return; }
		if (hasClass(e, className)) {
			if (e.classList) {
				e.classList.remove(className);
			} else {
				e.className = e.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
			}
		}
	});
}

function hasClass(el, className) {
	if (typeof el === 'string') el = document.querySelector(el);
	if (!el) {
		return false;
	}
	if (el.classList) {
		return el.classList.contains(className);
	}
	return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
}

export function toggleClass(el, className) {
	if (typeof el === 'string') el = document.querySelector(el);
	const flag = hasClass(el, className);
	if (flag) {
		removeClass(el, className);
	} else {
		addClass(el, className);
	}
	return flag;
}

function isInArray(value, array) {
	return array.indexOf(value) > -1;
}

export function withoutProperty(obj, properties) {
	// Returns the given `obj` without the `property` key name from array
	return {
		...Object.keys(obj)
			.filter(item => !isInArray(item, properties))
			.reduce((newObj, item) => {
				return {
					...newObj, [item]: obj[item]
				}
			}, {})
	}
}

export function isEmptyObject(obj) {
	return obj && Object.keys(obj).length === 0 && Object.getPrototypeOf(obj) === Object.prototype;
}

export function objChangeValueByKey(obj, key, value) {
	for (const objKey in obj) {
		if (objKey === key) {
			obj[key] = value;
		}
	}
	return obj;
}

export function getCategoryTranslations(key) {
	const transTitles = {
		noFavorites: Lang('no_favorites_history'),
		favoritesText: Lang('favorites_add_remove_text'),
		favorite: Lang('favorites'),
		product: Lang('products'),
		package: Lang('packages'),
		lesson: Lang('classes'),
		service: Lang('services_admin')
	};
	return transTitles[key];
}

export function trimStr(str) {
	if(!str) return str;
	return str.replace(/^\s+|\s+$/g, '');
}

export function tinyUrl(url){
	if(typeof url === 'string'){
		return new Promise((reslove, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open('GET', `https://tinyurl.com/api-create.php?url=${url}`, false);
			xhr.onreadystatechange = function () {
				if(xhr.readyState == 4 && xhr.status == 200){
					reslove((/tinyurl/gi).test(xhr.responseText) ? xhr.responseText : null);
				}else{
					reslove(null);
				}
			}
			xhr.send();
		});
	}else return null;
}