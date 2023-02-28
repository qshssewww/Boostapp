import asideClientBox from "@partials/asideClientBox";
import asideClientModal from "@partials/asideClientModal";
import select2UserItem from "@partials/select2UserItem";

import Select2 from "@modules/Select2";
import {Lang} from "@modules/Lang";
import {openModal, closeModal, resetModal, buildModal} from "@modules/Modal";
import {
	sendFetch, substrPhone, deleteSearchParam, setSearchParam
} from "@modules/Helpers";
import {
	setCartObject, getCartClientId, removeLessonWithClient, setDebtItems, removeDebtItems, saveCartToSession
} from "@modules/cart/CartHelpers";

import {buildPdfDoc} from "@modules/checkout/AsidePayButtons";
import {
	preventChangeClient, setCheckoutObject, openCheckOutOrderPopup, toggleVisibilityHalfSidebar
} from "@modules/checkout/CheckoutHelpers";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	options, globalUrl, bsappSidebarEl, cartControllerUrl
} = cartGlobalVariable;


// Private variables
let isInit = false;
let oldClientId = null;
let sidebarClient = null;
const classError = 'error';
const userSelect2El = 'userSearchSelect';
const userSelectEl = document.getElementById('userSelect');
const isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/

function init() {
	initEvents();
	buildAsideSelected();
}

function initEvents() {
	if (isInit) {return false;}

	document.addEventListener('click', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--open-user-sidebar')) {
				e.preventDefault();
				openSidebar.call(this, target, e);
				break;

			} else if (target.matches('.js--aside-append-user')) {
				e.preventDefault();
				appendAsideClient.call(this, target, e);
				break;

			} else if (target.matches('.js--clear-select2')) {
				e.preventDefault();
				clearClient();
				break;
			}
		}
	}, false);

	document.addEventListener('input', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('[name="client-name"]')) {
				e.preventDefault();
				if (document.querySelector('[name="is-new"]') && document.querySelector('[name="is-new"]').value === 'true') {
					const sidebarBtn = document.querySelector('.js--aside-append-user');

					if (target.value.length > 0) {
						target.classList.remove(classError);
						const phoneEl = document.querySelector('[name="client-phone"]');
						if (validateInput(phoneEl) && phoneEl.value.length > 0) {
							sidebarBtn.removeAttribute('disabled');
						}
					} else {
						target.classList.add(classError);
						sidebarBtn.setAttribute('disabled', 'disabled');
						return false;
					}
					sidebarClient.name = target.value;
				}
				break;

			} else if (target.matches('[name="client-phone"]')) {
				e.preventDefault();
				if (document.querySelector('[name="is-new"]')
					&& document.querySelector('[name="is-new"]').value === 'true') {
					const sidebarBtn = document.querySelector('.js--aside-append-user');

					if (target.value.length > 0 && validateInput(target)) {
						sidebarBtn.removeAttribute('disabled');
					} else {
						sidebarBtn.setAttribute('disabled', 'disabled');
						return false;
					}

					sidebarClient.phone = target.value;
				}
				break;

			}
		}
	}, false);

	if (!isInit) {
		isInit = true;
	}
}

function validateInput(input) {
	const errorEl = document.querySelector(".js--aside-user-error");
	if (input.value !== '' && !isValidMobileRegx.test(input.value)) {
		input.classList.add(classError);
		if (input.getAttribute('name') === 'client-phone') {
			errorEl.textContent = Lang('phone_format_incorrect_ajax');
		}
		input.focus();
		return false;

	} else {
		input.classList.remove(classError);
		errorEl.textContent = '';
		return true;
	}
}

function openSidebar(target, e) {
	if (target.classList.contains('disabled')) {
		return false;
	}

	closeModal('bsappErrorModal');
	closeModal('bsappModal');
	closeModal('bsappDropdown');
	closeModal('bsappHalfSidebar');

	const helpDataClient = options.helpData.client;
	if (oldClientId !== null && helpDataClient && oldClientId === helpDataClient.id) {
		const errorPhoneEl = bsappSidebarEl.querySelector(".js--aside-user-error");
		if (errorPhoneEl && errorPhoneEl.textContent !== '') {
			errorPhoneEl.textContent = '';
		} else {
			openModal({modalEl: 'bsappSidebar'});
			return false;
		}
	}

	buildModal({
		el: bsappSidebarEl,
		html: buildSidebar(helpDataClient, true)
	}).then(function () {
		openModal({modalEl: 'bsappSidebar'});
	});

	if (!(helpDataClient && helpDataClient.id)) {
		buildSelect2();
	}
}

function buildSelect2() {
	Select2({
		elId: userSelect2El,
		isOpen: true,
		templateHandlebarsName: select2UserItem,
		options: {
			placeholder: Lang('search_by_name_or_phone'),
			dropdownParent: '.js--aside--user-field',
			ajax: {
				url: '/office/action/getClientsJson.php',
				data: function (params) {
					// Query parameters will be ?search=[term]&type=public
					return {
						query: params.term,
						type: 'public'
					};
				},
				processResults: function (data) {
					const res = JSON.parse(data).results;
					const items = res.map(user => ({
							name: user.name,
							id: +user.id,
							img: user.img,
							url: user.url,
							email: user.email,
							phone: user.phone,
							status: user.status
						})
					);
					return {
						results: items
					};
				},
			}
		},
		_onSelection: function(e) {
			console.log("select2:selecting", e.params.args.data);
			const selectedUser = e.params.args.data;
			const id = selectedUser.id;
			const isNew = selectedUser.isNew ? selectedUser.isNew : false;
			let text = selectedUser.text;
			let phone = selectedUser.phone;

			if (isNew) {
				if (text.match(isValidMobileRegx)) {
					phone = text;
					text = '';
				}
			}

			const select2client = {
				id,
				isNew,
				phone,
				name: selectedUser.name ? selectedUser.name : text,
				img: selectedUser.img,
				url: selectedUser.url,
				email: selectedUser.email,
				readonly: !isNew,
			};
			sidebarClient = select2client;
			buildSidebar(select2client);
		}
	});
}

function buildAsideSelected() {
	// const clientObj = sidebarClient ?? options.helpData.client;
	const clientObj = options.helpData.client;
	userSelectEl.innerHTML = asideClientBox(clientObj);
	const isCheckoutPage = document.body.classList.contains('bsapp__checkout-page');
	if (!isCheckoutPage) {
		return false;
	}

	// show/hide user in PDF preview on checkout page
	buildPdfDoc();
	preventChangeClient();

	const creditBox = document.querySelector('.aside--half-sidebar-pay[data-type="credit"]');
	if (creditBox !== null) {
		if (creditBox.getAttribute('data-type-open') === '2') {
			setCheckoutObject({creditPaymentSettings: '1'});
		}
		toggleVisibilityHalfSidebar();
	}
}

function buildSidebar(clientObj = null, toReturn = false) {
	const html = asideClientModal({
		userAppended: !!getCartClientId(),
		user: clientObj
	});
	if (toReturn) {
		return html;
	}

	resetModal(bsappSidebarEl);
	bsappSidebarEl.innerHTML = html;
}

export function saveClientToCart(client) {
	if (!(client && client.id && client.id !== '')) {
		return false;
	}
	createClient({clientObj: client});
}

async function appendAsideClient(target, e) {
	let clientObj = sidebarClient ?? options.helpData.client;
	const clientId = clientObj && clientObj.id;
	if (!clientId) {
		return false;
	}
	let data;
	if (clientObj.isNew) {
		const nameEl = bsappSidebarEl.querySelector('[name="client-name"]');
		const phoneEl = bsappSidebarEl.querySelector('[name="client-phone"]');
		if (nameEl.value.length === 0 || phoneEl.value.length === 0 || !(phoneEl.value.length > 0 && validateInput(phoneEl))) {
			return false;
		}
		// check by phone if a new client already exists in system
		data= {
			action: 'checkNewClientPhone',
			phone: phoneEl.value
		};
	} else {
		// check if client has already a debt items
		data = {
			action: 'saveCartUser',
			id: clientId
		};
	}

	const response = await checkClient(data);
	const clientInSystem = response && response.client;
	createClient({
		target,
		clientObj: clientInSystem ?? clientObj,
		itemsInDebt: response && response.debts
	});
}

export async function checkClient(data) {
	return sendFetch(
		cartControllerUrl,
		data
	).then(function(response) {
		if (!response.success) {
			const errorPhoneEl = bsappSidebarEl.querySelector(".js--aside-user-error");
			if (errorPhoneEl) {
				errorPhoneEl.textContent = response.message;
			}
			return false;
		}
		if(response.openOrderId !== undefined && response.openOrderId > 0) {
			openCheckOutOrderPopup(response.openOrderId, response.openOrderRefund ?? false);
			return false;
		}
		return response;
	});
}


function createClient(props) {
	let {
		clientObj,
		itemsInDebt = null,
		target = null
	} = props;

	if (target) {
		target.setAttribute('disabled', 'disabled');
	}

	let {id, isNew = false, phone} = clientObj;

	// save client in helpData
	options.helpData.client = {...clientObj, readonly: !isNew};
	if (phone) {
		options.helpData.client.substrPhone = substrPhone(phone);
	}
	if (isNew) {
		options.helpData.client.id = id;
	}

	// build the client's initial items are in debt
	if (itemsInDebt && itemsInDebt.length > 0 && window.location.href.indexOf("checkout.php")  < 0) {
		setDebtItems(itemsInDebt);
	}

	saveAsideClient(clientObj);

	if (document.body.classList.contains('bsapp__checkout-page')) {
		saveCartToSession();
	}
}

function saveAsideClient(clientObj) {
	if (!clientObj.isNew) {
		// change url only if client exists in the system
		setSearchParam('u', clientObj.id);
	} else {
		deleteSearchParam('u');
	}
	if(clientObj.isRandomClient === 1) {
		setCartObject({clientId: null});
	} else {
		setCartObject({clientId: clientObj.id});
	}
	// delete lessons if the client is already assigned to those lessons, that already added to the cart
	removeLessonWithClient();
	buildAsideSelected();

	closeModal('bsappSidebar');
}

export function clearClient() {
	// if (getCartClientId()) {
		clearClientItemsInDebt();
		setCartObject({clientId: null});
		sidebarClient = {};
		delete options.helpData.client;
	// }

	const asideUserEl = document.querySelector('.js--aside-append-user');
	if (asideUserEl && asideUserEl.getAttribute('disabled') !== null) {
		asideUserEl.removeAttribute('disabled');
	}

	buildAsideSelected();
	buildSidebar();
	buildSelect2();
	oldClientId = null;
}

console.log(sidebarClient)
function clearClientItemsInDebt() {
	deleteSearchParam('u');
	removeDebtItems();
}

export { init };