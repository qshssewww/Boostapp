import payTypesModal from "@partials/checkout/payTypesModal.hbs";
import pdfDocBox from "@partials/checkout/pdfDocBox.hbs";
// import partialTransactions from "@partials/checkout/partialTransactions.hbs";
import iframeContentBox from "@partials/item/iframeContentBox.hbs";
import customDropdown from "@partials/item/customDropdown.hbs";

import {Lang} from "@modules/Lang";
import CustomDropdown from "@modules/CustomDropdown";
import {
	validateOnlyNumber, validateOnlyNumberLetter, addClass, removeClass, toggleClass, sendFetch, showErrorModal,
	getObjectSize, reduceTotal, getIndex, setDate, slideDown, slideUp
} from "@modules/Helpers";
import {buildModal, openModal, closeModal} from "@modules/Modal";
import {getCartObject, getCartClientId, getItemDataToSend, isRandomClient} from "@modules/cart/CartHelpers";
import {
	getCheckoutObject, setCheckoutObject, setNewTransaction, deleteTransaction as deleteCheckoutTransaction,
	buildTransactions, toggleAttributeWithDropdown, closeHalfSidebar, toggleVisibilityHalfSidebar, setHasRefundCredit,
	getCheckOrderId, setCheckOrderId ,fetchCancellationPayment, isRefundPage, getTypeShva, getDocId
} from "@modules/checkout/CheckoutHelpers";
import {setDisplayNumber, updateDisplayNumber, setError} from "@modules/cart/AdditionalEvents";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	options, globalUrl, cartControllerUrl, isMobile, bsappHalfSidebarEl, checkoutTotalPriceEl,  refundControllerUrl, cookieCartItemsToSaveName
} = cartGlobalVariable;


let isInit = false;
let checkPaymentStatusTimer;
const valuePaymentSettingsSaved = '2';
const valuePaymentSettingsIframe = '3';
const valuePaymentSettingsOtherMasof = '4';
const valuePaymentSettingsPayWithToke = '8';

// checkout default parameters for payment methods
const checkoutDefaultParameters = {
	paymentMethod: {
		labelKey: 'payment_method',
		type: 'paymentMethod',
		items: [
			{
				id: '0',
				textKey: 'regular'
			},
			{
				id: '1',
				textKey: 'payments'
			}
		]
	},
	paymentNumber: {
		labelKey: 'payments_num',
		type: 'paymentNumber',
		items: [
			{id: '2', text: '2'},
			{id: '3', text: '3'},
			{id: '4', text: '4'},
			{id: '5', text: '5'},
			{id: '6', text: '6'},
			{id: '7', text: '7'},
			{id: '8', text: '8'},
			{id: '9', text: '9'},
			{id: '10', text: '10'},
			{id: '11', text: '11'},
			{id: '12', text: '12'}
		]
	},
	creditPaymentSettings: {
		labelKey: 'payment_settings',
		type: 'creditPaymentSettings',
		items: [
			// {
			// 	id: '1',
			// 	textKey: 'credit_card_scanner'
			// },
			{
				id: '3',
				textKey: 'manual_type'
			},
			{
				id: '2',
				textKey: 'checkout_credit_saved_card'
			},
			{
				id: '4',
				textKey: 'checkout_credit_terminal_transaction'
			}
		]
	},
	creditTypeCard: {
		labelKey: 'choose_credit_card_type',
		type: 'creditTypeCard',
		items: [
			{
				id: '88',
				textKey: 'mastercard'
			},
			{
				id: '2',
				textKey: 'visa'
			},
			{
				id: '5',
				textKey: 'isracard'
			},
			{
				id: '66',
				textKey: 'diners'
			},
			{
				id: '77',
				textKey: 'american_express'
			}
		]
	},
	creditTypeBank: {
		labelKey: 'choose_company_to_be_paid_off',
		type: 'creditTypeBank',
		items: [
			{
				id: '2',
				textKey: 'visa_cal'
			},
			{
				id: '1',
				textKey: 'isracard'
			},
			{
				id: '6',
				textKey: 'leumi_card'
			}
		]
	},
	bankTransferType: {
		labelKey: 'checkout_transfer_type',
		type: 'bankTransferType',
		items: [
			{
				id: '1',
				textKey: 'bank_transfer'
			},
			{
				id: '2',
				text: 'Bit'
			},
			{
				id: '3',
				text: 'Pepper'
			},
			{
				id: '4',
				text: 'Paybox'
			},
			{
				id: '5',
				text: 'Paypal'
			}
		]
	}
}

const payCashBtn = document.getElementById('payCash');
const checkoutDocsPreviewEl = document.getElementById('checkoutDocsPreview');
const openHalfSidebarBtn = document.querySelectorAll('.js--open-half-sidebar');

function updateCreditPaymentSettingsByTypeShva(typeShva) {
	checkoutDefaultParameters.paymentMethod.labelKey = 'refund_method';
	switch (typeShva) {
		case 0:
			checkoutDefaultParameters.creditPaymentSettings.items = [
				{
					id: '3',
					textKey: 'manual_type'
				},
				{
					id: '2',
					textKey: 'checkout_credit_saved_card'
				},
				{
					id: '4',
					textKey: 'checkout_credit_terminal_transaction'
				}
			];
			break;
		case 1:
		case 2:
			checkoutDefaultParameters.creditPaymentSettings.items = [
				{
					id: '2',
					textKey: 'checkout_credit_saved_card'
				},
				{
					id: '4',
					textKey: 'checkout_credit_terminal_transaction'
				}
			];
			break;
	}


}

export function init() {
	initEvents();
	// initialize only a customDropdown events
	CustomDropdown({});
	// build bottom transactions content if we have receipts in response
	if (getCheckoutObject().receipts) {
		buildTransactions();
	}
	// build PDF preview
	buildPdfDoc();
}

function initEvents() {

	if (isInit) {
		return false;

	}
	openHalfSidebarBtn.forEach(button => {
		button.addEventListener('click', (e) => {
			e.preventDefault();
			if (payCashBtn.classList.contains('active')) {
				removeClass(payCashBtn, 'active');
			}
			buildHalfSidebar(e.currentTarget);
		});
	});

	payCashBtn.addEventListener('click', (e) => {
		e.preventDefault();
		const el = e.currentTarget;
		toggleClass(el, 'active');
		createTransaction(el.getAttribute('data-pay-type'));
	});

	document.addEventListener('click', async function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--return-to-aside')) {
				checkoutTotalPriceEl.removeAttribute('data-docs-payment-price-max');
				e.preventDefault();
				clearInterval(checkPaymentStatusTimer);
				closeHalfSidebar();
				break;

			} else if (target.matches('.js--create-transaction')) {
				e.preventDefault();
				prepareToCreateTransaction.call(this, target, e);
				break;

			} else if (target.matches('.js--delete-transaction')) {
				e.preventDefault();
				const targetId = target.getAttribute('data-id');
				if (!targetId) return false;
				const type = target.getAttribute('data-type');
				if (type === 'credit' ) {
					const paymentSetting = target.getAttribute('data-payment-setting');
					if (paymentSetting !== valuePaymentSettingsOtherMasof) {
						if(isRefundPage()) {
							showErrorModal({
								textKey: 'cant_cancel_refund',
								error: Lang('cant_cancel_refund_error_details')
							});
							return false;
						}
						const loginOrderId = target.getAttribute('login-order-id');
						showErrorModal({
							textKey: 'checkout_sure_delete_credit_receipt',
							bottomBtns: [{
								textKey: 'checkout_make_cancellation',
								jsId: 'checkoutCancelCredit',
								loginOrderId: loginOrderId,
								dataType: type,
								dataId: targetId
							}]
						});
						return false;
					}
				}
				deleteTransaction(targetId, type);
				break;

			} else if (target.matches('#checkoutCancelCredit')) {
				e.preventDefault();
				const loginOrderId = target.getAttribute('login-order-id');
				if(loginOrderId) {
					const successes = await fetchCancellationPayment(loginOrderId);
					if(!successes){
						return;
					}
				}

				deleteTransaction(target.getAttribute('data-id'), target.getAttribute('data-type'));
				closeModal('bsappErrorModal');
				break;

			} else if (target.matches('.js--try-again-get-iframe')) {
				e.preventDefault();
				const creditPaymentSettingsEl = bsappHalfSidebarEl.querySelector('[name="creditPaymentSettings"]');
				if (creditPaymentSettingsEl) {
					getDataRequest(creditPaymentSettingsEl.value, target.closest('.aside--half-sidebar-pay'));
				}
				break;

			}
		}
	});

	document.addEventListener('keyup', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('[validate-only-number]')) {
				// const attrPrice = target.getAttribute('data-price') && parseFloat(target.getAttribute('data-price'));
				const attrPrice = target.value && parseFloat(target.value);
				const attrMax = target.getAttribute('data-price-max');

				if (attrMax && attrPrice > parseFloat(attrMax)) {
					// target.setAttribute('data-current-operand', attrMax);
					// updateDisplayNumber(target);
					target.value = attrMax;
					debugger
					addClass(target, 'error');
					// setError(target, Lang('checkout_can_not_type_more'), true);
				} else {
					setError(target);
				}
			}
		}
	});

	document.addEventListener('keydown', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('[validate-only-number]')) {
				if (!validateOnlyNumber(e)) {
					e.preventDefault();
					return false;
				}
				break;

			} else if (target.matches('[validate-only-number-letter]')) {
				if (!validateOnlyNumberLetter(e)) {
					e.preventDefault();
					return false;
				}
				break;
			}
		}
	});

	document.addEventListener('change', function (e) {

		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.bsapp--custom-select_hidden')) {
				changeCustomDropdown.call(this, target, e);
				break;

			} else if (target.matches('[required]')) {
				const payTypeEl = target.closest('.aside--half-sidebar-pay');
				if (payTypeEl && payTypeEl.getAttribute('data-type') && target.getAttribute('name')) {
					setCheckoutObject({
						[target.getAttribute('name')]: target.value
					});
				}
				break;

			}
		}
		if ((e.target.name==='checkPaymentDate' || e.target.name==='bankTransferDepositDate' ) && e.target.value){
			e.target.classList.remove('error')
		}
	});

	document.addEventListener('input', (e)=>{
		if (e.target.name==='bankTransferRefNumber') {
			e.target.classList.remove('error')
		}
	})


	const mq = window.matchMedia(isMobile.mediaQuery);
	mq.addEventListener('change', function() {
		// if (!isMobile.matches && checkoutDocsPreviewEl.childNodes.length === 0) {
		if (!isMobile.matches) {
			buildPdfDoc();
		}
	});

	if (!isInit) {
		isInit = true;
	}
}

function changeCustomDropdown(target, e) {
	const payTypeEl = target.closest('.aside--half-sidebar-pay');
	const paymentNumberEl = payTypeEl.querySelector('[name="paymentNumber"]');
	const paymentMethodEl = payTypeEl.querySelector('[name="paymentMethod"]');
	const name = target.getAttribute('name');
	const value = target.value;
	setCheckoutObject({
		[name]: value
	});

	if (name === 'paymentMethod') {
		// toggle disabled to payment number if choose type 'regular'
		toggleAttributeWithDropdown(paymentNumberEl, value === '0');
	} else if (name === 'creditSavedCard') {
		if (isRefundPage()) {
			debugger;//todo
			updateDocsPaymentPriceMax(target);
		}
	} else if (name === 'creditPaymentSettings') {
		// change content depends on credit payment settings
		payTypeEl.setAttribute('data-type-open', value);
		debugger;//todo
		clearInterval(checkPaymentStatusTimer);
		if (isRefundPage()) {
			hiddenPaymentMethod((value === valuePaymentSettingsOtherMasof));
			if(value !== valuePaymentSettingsSaved){
				checkoutTotalPriceEl.removeAttribute('data-docs-payment-price-max');
			} else {
				const saveTokenEl = document.getElementById('creditSavedCardContent').querySelector('[name="creditSavedCard"]');
				if (saveTokenEl !== null) {
					updateDocsPaymentPriceMax(saveTokenEl);
				}
			}
		}
		halfSidebarToNormal([checkoutTotalPriceEl, paymentMethodEl])
		toggleAttributeWithDropdown(paymentNumberEl, paymentMethodEl && paymentMethodEl.value === '0');

		if(getDocId() === null) {
			removeClass(document.getElementById('userSelect'), 'disabled');
		}
		if (value === valuePaymentSettingsSaved && checkoutDefaultParameters.creditSavedCardForUser !== getCartClientId()) {
			// send request to get already saved credit cards of current customer
			getDataRequest(value, target.closest('.aside--half-sidebar-pay'));

		} else if (value === valuePaymentSettingsIframe && bsappHalfSidebarEl.querySelector('.js--create-transaction')) {
			// change text of the bottom button, to continue with request to get iframe url for the payment
			bsappHalfSidebarEl.querySelector('.js--create-transaction').textContent = Lang('continue_main');
		}
	}
}

function getCheckOrderParamToData(data, paymentMethodEl, paymentNumberEl) {
	const checkOrderId = getCheckOrderId();

	// data.amount = checkoutTotalPriceEl && parseFloat(checkoutTotalPriceEl.getAttribute('data-price'));
	data.amount = checkoutTotalPriceEl && parseFloat(checkoutTotalPriceEl.value);
	data.paymentNumber = paymentMethodEl && paymentMethodEl.value === "1" && paymentNumberEl
		? paymentNumberEl.value : "1";
	if (checkOrderId === undefined || checkOrderId <= 0) {
		const cartObject = getCartObject();
		if(cartObject.id) {
			data.invoiceId = cartObject.id;
		}
		data.items = cartObject.items.map(getItemDataToSend);
		if (cartObject.discount && cartObject.discount.amount !== 0) {
			data.discount = cartObject.discount;
		}
	} else {
		data.checkOutOrderId = checkOrderId;
	}
}

function halfSidebarToNormal(elementArray) {
	// remove iframe url
	document.getElementById('iframeForPayment').innerHTML = '';
	elementArray.forEach(element =>{
		toggleAttributeWithDropdown(element, false);
	});
	toggleVisibilityFooter(false);
	// removeClass(document.getElementById('userSelect'), 'disabled');
}

function getDataRequest(value, el, callback, token = undefined) {
	const paymentMethodEl = el.querySelector('[name="paymentMethod"]');
	const paymentNumberEl = el.querySelector('[name="paymentNumber"]');
	const loginOrderIdEl = el.querySelector('[name="loginOrderId"]');
	const clientId = getCartClientId();
	let data = {};
	if(isRefundPage()) {
		data.invoiceId = getDocId();
	}
	switch (value) {
		case valuePaymentSettingsIframe:
			data.action = isRefundPage() ? 'refundWithNewCard' : 'payWithNewCard'
			//add client to data on payment
			if(options !== undefined && options.helpData !== undefined && options.helpData.client !== undefined
				&& options.helpData.client.isNew !== undefined && options.helpData.client.isNew) {
				data.clientDetails = options.helpData.client;
				data.clientId = 0;
			} else {
				data.clientId = clientId;
			}
			getCheckOrderParamToData(data, paymentMethodEl, paymentNumberEl);
			break;
		case valuePaymentSettingsPayWithToke:
			if(clientId === null || token === undefined) {
				return false;
			}
			data.clientId = clientId
			if(isRefundPage()) {
				data.action = 'refundWithToken';
				(getTypeShva() !== 0) ? data.docPaymentId  = token : data.tokenId = token;
			} else {
				data.action = 'payWithToken';
				data.tokenId = token;
			}
			getCheckOrderParamToData(data, paymentMethodEl, paymentNumberEl);
			break;
		case valuePaymentSettingsSaved: //get tokes
			debugger;//todo
			if(clientId === null || isRandomClient()) {
				return false;
			}
			data = {
				action: 'getSavedCardTokens',
				clientId: clientId
			};
			if(isRefundPage() && getTypeShva() !== 0) {
				data.invoiceId = getDocId();
			}
			break;
	}

	// add loader to current block
	sendFetch(
		isRefundPage() ? refundControllerUrl : cartControllerUrl,
		data
	).then(function(response) {
		if (!response.success) {
			showErrorModal({
				error: response.message
			});
			return false;
		}
		if(response.checkOutOrderId) {
			setCheckOrderId(response.checkOutOrderId)
		}
		if(response.orderId) {
			loginOrderIdEl.value = response.orderId;
		}
		if (data.action === 'payWithToken' || data.action === 'refundWithToken') {
			console.log(document.getElementById('creditSavedCardId'));
			callback();
			toggleAttributeWithDropdown(checkoutTotalPriceEl, false)
		} else if (data.action === 'payWithNewCard' || data.action === 'refundWithNewCard') {
			if (!response.iframeUrl) {
				throw new Error('[getDataRequest] expected an iframe url');
			}
			toggleAttributeWithDropdown(checkoutTotalPriceEl, true);
			toggleAttributeWithDropdown(paymentMethodEl, true);
			toggleAttributeWithDropdown(paymentNumberEl, true);
			toggleVisibilityFooter(true);
			addClass(document.getElementById('userSelect'), 'disabled');
			document.getElementById('iframeForPayment').innerHTML = iframeContentBox({
				iframeUrl: response.iframeUrl
			});
			window.paymentStatus = 'waiting';
			checkPaymentStatusTimer = setInterval(function() {
				console.log('[checkPaymentStatusTimer tick]');
				if (window.paymentStatus !== 'waiting') {
					clearInterval(checkPaymentStatusTimer);
					document.getElementById('iframeForPayment').innerHTML = '';
					if (window.paymentStatus === 'error') {
						// TODO: return button and unblock the fields
						showErrorModal({
							// TODO: change message text
							textKey: 'processing_error_meshulam'
						});
						return;
					}
					if (window.paymentStatus === 'success' || window.paymentStatus === 'success_meshulam') {
						console.log(document.getElementById('creditSavedCardId'));
						if( data.action === 'refundWithNewCard') {
							const tokenId = window.paymentTokenId;
							callback(tokenId);
							toggleAttributeWithDropdown(checkoutTotalPriceEl, false)
						} else {
							callback();
							toggleAttributeWithDropdown(checkoutTotalPriceEl, false)
						}
					} else {
						halfSidebarToNormal([checkoutTotalPriceEl, paymentMethodEl])
						toggleAttributeWithDropdown(paymentNumberEl, paymentMethodEl && paymentMethodEl.value === '0');
					}
				}
			}, 1500);
		}
		else if(data.action === 'getSavedCardTokens') {
			const savedTokenData = {
				labelKey: 'checkout_credit_card_saved',
				placeholderKey: response.creditSavedCard ? 'select_cc' : 'table_no_data',
				createSelect: true,
				required: true,
				bigClass: true,
				selectId: 'creditSavedCard',
				type: 'clientCard',
				selectedOptionId: getCheckoutObject().creditSavedCard,
				items: response.creditSavedCard
			};
			console.log('[savedTokenData]', savedTokenData);
			const savedTokenEl = document.getElementById('creditSavedCardContent')

			savedTokenEl.innerHTML = customDropdown(savedTokenData);
			if (response.creditSavedCard) {
				checkoutDefaultParameters.creditSavedCardForUser = getCartClientId();
				checkoutDefaultParameters.creditSavedCard = savedTokenData;
				savedTokenEl.querySelector('[name="creditSavedCard"]').addEventListener('change', function (e) {
					debugger;
					console.log('[savedTokenData] change update!!!');
					if(isRefundPage()) {
						updateDocsPaymentPriceMax(e.target)
					}
				});

			} else {
				toggleVisibilityFooter(true);
			}
		}
	});
}

function updateDocsPaymentPriceMax(el) {
	const maxValue = el.options[el.selectedIndex].getAttribute("data-max-value");
	if(maxValue === null || maxValue === undefined) {
		return false;
	}
	const maxTotalPrice = checkoutTotalPriceEl.getAttribute('data-price-max');
	const maxPrice = Math.min(maxValue, maxTotalPrice);
	checkoutTotalPriceEl.setAttribute('data-docs-payment-price-max', maxPrice);
	if(parseFloat(checkoutTotalPriceEl.value) > parseFloat(maxPrice)) {
		checkoutTotalPriceEl.value = maxPrice;
	}
}

function prepareCustomDropdown(props) {
	const {
		newKey,
		createSelect = true,
		required = true,
		disabled = false,
		bigClass = true,
		globals = checkoutDefaultParameters[newKey]
	} = props;

	// change item.text with translation if item.textKey is present
	globals.items.forEach(item => {
		item.text = !item.text && item.textKey ? Lang(item.textKey) : item.text;
	});
	return {
		...globals, ...{
			createSelect,
			required,
			disabled,
			bigClass,
			selectId: newKey,
			selectedOptionId: getCheckoutObject()[newKey]
		}
	};
}

function createContent(type) {
	const currentTypeData = {
		type,
		modalFooter: true,
		modalHeader: {
			title: getPaymentTypeTitle(type),
			iconClass: `fa-${type}`
		}
	};
	switch (type) {
		case 'credit':
			currentTypeData.modalHeader.iconClass = 'fa-credit-card';
			let globalsPaymentSettings = checkoutDefaultParameters.creditPaymentSettings;
			globalsPaymentSettings.items.forEach(item => {
				item.disabled = (!getCartClientId() || isRandomClient()) && item.id === valuePaymentSettingsSaved;
			});
			currentTypeData.creditPaymentSettings = prepareCustomDropdown({
				newKey: 'creditPaymentSettings',
				globals: globalsPaymentSettings
			});
			currentTypeData.paymentMethod = prepareCustomDropdown({
				newKey: 'paymentMethod'
			});
			currentTypeData.paymentNumber = prepareCustomDropdown({
				newKey: 'paymentNumber',
				disabled: getCheckoutObject().paymentMethod === '0'
			});
			currentTypeData.creditTypeCard = prepareCustomDropdown({
				newKey: 'creditTypeCard'
			});
			currentTypeData.creditTypeBank = prepareCustomDropdown({
				newKey: 'creditTypeBank'
			});

			// if we already have a customer saved cards token
			if (checkoutDefaultParameters.creditSavedCard && checkoutDefaultParameters.creditSavedCardForUser === getCartClientId()) {
				currentTypeData.creditSavedCard = prepareCustomDropdown({
					newKey: 'creditSavedCard'
				});
			}
			break;

		case 'check':
			currentTypeData.modalHeader.iconClass = 'fa-money-check-pen';
			break;

		case 'bankTransfer':
			currentTypeData.modalHeader.iconClass = 'fa-building-columns';
			currentTypeData.bankTransferType = prepareCustomDropdown({
				newKey: 'bankTransferType'
			});
			break;

		default:
			currentTypeData.modalFooter = false;
	}

	return currentTypeData;
}

function getPaymentTypeTitle(type) {
	switch (type) {
		case 'credit':
			return Lang('credit_card');
		case 'bankTransfer':
			return Lang('checkout_transfer');
		default:
			return Lang(type);
	}
}

function buildHalfSidebar(button) {
	const type = button.getAttribute('data-pay-type');
	if(isRefundPage()) {
		updateCreditPaymentSettingsByTypeShva(getTypeShva());
	}
	const currentTypeData = createContent(type);
	if (currentTypeData.creditPaymentSettings) {
		currentTypeData.typeOpen = currentTypeData.creditPaymentSettings.selectedOptionId;
		if (currentTypeData.creditPaymentSettings.selectedOptionId === valuePaymentSettingsIframe && currentTypeData.modalFooter) {
			currentTypeData.modalFooter = {
				textKey: 'continue_main'
			};
		}
	}
	buildModal({
		el: bsappHalfSidebarEl,
		html: payTypesModal({...getCheckoutObject(), ...currentTypeData})
	}).then(function () {
		if (isRefundPage()) {
			hiddenPaymentMethod();
			bsappHalfSidebarEl.querySelector('.js--create-transaction').textContent = Lang('documents_refund');
			if(document.getElementById('creditSavedCardContent') !== null) {
				const saveTokenEl = document.getElementById('creditSavedCardContent').querySelector('[name="creditSavedCard"]');
				if (saveTokenEl !== null) {
					updateDocsPaymentPriceMax(saveTokenEl);
				}
			}
		}
		toggleVisibilityHalfSidebar(false);
		if (currentTypeData.creditPaymentSettings
			&& currentTypeData.creditPaymentSettings.selectedOptionId === valuePaymentSettingsSaved
			&& checkoutDefaultParameters.creditSavedCardForUser !== getCartClientId()) {
			// send request to get already saved credit cards of current customer
			getDataRequest(valuePaymentSettingsSaved, bsappHalfSidebarEl.querySelector('.aside--half-sidebar-pay'));
		}
	});
}

function hiddenPaymentMethod(showPaymentMethod = false) {
	const paymentMethod = document.getElementById('js-payment-method');
	if(showPaymentMethod) {
		removeClass(paymentMethod, 'hidden')
	} else {
		getTypeShva() !== 0 ? addClass(paymentMethod, 'hidden') : removeClass(paymentMethod, 'hidden');
	}
}

function toggleVisibilityFooter(hide) {
	const modalFooterEl = bsappHalfSidebarEl.querySelector('.modal-footer');
	const modalBodyEl = bsappHalfSidebarEl.querySelector('.modal-body');
	if (hide) {
		addClass(modalFooterEl, 'hidden');
		addClass(modalBodyEl, 'without-footer');
	} else {
		removeClass(modalFooterEl, 'hidden');
		removeClass(modalBodyEl, 'without-footer');
		if (bsappHalfSidebarEl.querySelector('.js--create-transaction')) {
			const textBtn = isRefundPage() ? Lang('documents_refund') : Lang('checkout_pay')
			bsappHalfSidebarEl.querySelector('.js--create-transaction').textContent = textBtn;
		}
	}
}

function deleteTransaction(targetId, type) {
	const checkoutTransactions = getCheckoutObject().transactions;
	const pos = getIndex(checkoutTransactions, targetId);
	if (pos === -1) {
		throw new Error('Invalid transaction ID in the delete checkout transactions.');
	}

	closeModal('bsappModal');
	deleteCheckoutTransaction(pos);
	buildTransactions();
}

function prepareToCreateTransaction(target, e) {
	// validate inputs
	const type = target.getAttribute('data-type');
	const modalDialog = target.closest('.modal-dialog');
	const creditPaymentSettingsEl = modalDialog.querySelector('[name="creditPaymentSettings"]');
	if (type == 'credit' && creditPaymentSettingsEl
		&& creditPaymentSettingsEl.value === valuePaymentSettingsIframe) {
		getDataRequest(valuePaymentSettingsIframe, modalDialog.querySelector('.aside--half-sidebar-pay'), function (tokenId= 0) {
			if(tokenId > 0) {
				getDataRequest(valuePaymentSettingsPayWithToke, modalDialog.querySelector('.aside--half-sidebar-pay'),
					function () {
						const requiredValid = checkRequiredFields(modalDialog.querySelector('.aside--half-sidebar-pay'));
						createTransaction(type, requiredValid);
						}, tokenId);
				return false;
			}
			const requiredValid = checkRequiredFields(modalDialog.querySelector('.aside--half-sidebar-pay'));
			console.log('[type, requiredValid]', type, requiredValid);
			createTransaction(type, requiredValid);
		});
		return false;
	}
	// before send post, check whether all required fields are filled
	const requiredValid = checkRequiredFields(modalDialog.querySelector('.aside--half-sidebar-pay'));
	if (!requiredValid) {
		return false;
	}
	if (type == 'credit' && creditPaymentSettingsEl && requiredValid.creditSavedCard
		&& creditPaymentSettingsEl.value === valuePaymentSettingsSaved) {
		getDataRequest(valuePaymentSettingsPayWithToke, modalDialog.querySelector('.aside--half-sidebar-pay'),
			function () {
				const requiredValid = checkRequiredFields(modalDialog.querySelector('.aside--half-sidebar-pay'));
				createTransaction(type, requiredValid);
			}
			, requiredValid.creditSavedCard);
			return false;
	}
	createTransaction(type, requiredValid);
}

export function createTransaction(type, requiredValid = {}) {
	const newTotalPrice = requiredValid.price ? requiredValid.price :checkoutTotalPriceEl.value;
	const newPriceInt = parseFloat(newTotalPrice);
	const maxTotalPrice = checkoutTotalPriceEl.getAttribute('data-price-max');
	const maxTotalPriceExists = maxTotalPrice !== null;
	const maxPriceInt = parseFloat(maxTotalPrice);

	if(type == 'credit'  && requiredValid.creditPaymentSettings != valuePaymentSettingsOtherMasof) {
		window.sessionStorage.removeItem(cookieCartItemsToSaveName);
		if(isRefundPage()) {
			setHasRefundCredit(true);
		}
	}

	if (isNaN(newPriceInt) || (newPriceInt === 0 && maxTotalPriceExists && maxPriceInt !== 0)) {
		addClass(checkoutTotalPriceEl, 'error');
		return false;

	} else if (maxTotalPriceExists && maxPriceInt > newPriceInt) {
		const newMax = parseFloat(maxTotalPrice) - newPriceInt;
		setDisplayNumber(checkoutTotalPriceEl, (newMax * 100).toFixed() / 100);
	}

	console.log('[transactionInfo]', {
		...requiredValid,
		...{
			type,
			typeKey: getPaymentTypeTitle(type),
			price: newPriceInt,
			details: getTransactionDetailsText(type, requiredValid),
			dateCreated: getDateISO(type, requiredValid),
			id: `pay_${type}_${getCheckoutObject().transactions.length + 1}`
		}
	});

	// build a partial payment step (aside bottom part)
	setNewTransaction({
		...requiredValid,
		...{
			type,
			typeKey: getPaymentTypeTitle(type),
			price: newPriceInt,
			details: getTransactionDetailsText(type, requiredValid),
			dateCreated: getDateISO(type, requiredValid),
			id: `pay_${type}_${getCheckoutObject().transactions.length + 1}`
		}
	});
	buildTransactions();
}

function getDateISO(type, requiredValid) {

	let date;
	switch (type) {
		case 'credit':
			date = requiredValid.creditOriginalChargeDate ? requiredValid.creditOriginalChargeDate : null;
			break;
		case 'bankTransfer':
			date = requiredValid.bankTransferDepositDate ? requiredValid.bankTransferDepositDate : null;
			break;
		case 'check':
			date = requiredValid.checkPaymentDate ? requiredValid.checkPaymentDate : null;
			break;
		default:
			date = null;
	}

	return date === null ? (new Date()).toISOString() : (new Date(date)).toISOString();
}

function getTransactionDetailsText(type, requiredValid) {
	let text = '';
	const paymentOrRefund = isRefundPage() ? 'זיכוי' : 'חיוב';
	switch (type) {
		case 'credit':
			if(requiredValid.creditPaymentSettings === valuePaymentSettingsIframe) {
				text = paymentOrRefund + ' כרטיס חדש';
			} else if(requiredValid.creditPaymentSettings === valuePaymentSettingsSaved) {
				text = paymentOrRefund + ' בכרטיס';
			} else {
				text = paymentOrRefund + ' במסוף אחר ';
			}
			if (requiredValid.credit4Number || requiredValid.creditSavedCard) {
				if (requiredValid.creditSavedCard) {
					const numbersText = document.querySelector('[name="creditSavedCard"]').querySelector(`[value="${requiredValid.creditSavedCard}"]`).textContent;
					text += ` המסתיים ב-${numbersText.replaceAll("*", "")}`;
				} else {
					text += ` המסתיים ב-${requiredValid.credit4Number}`;
				}
			}
			if (requiredValid.paymentNumber) {
				text += requiredValid.paymentNumber === '1' ? ' בתשלום רגיל' : ` ב-${requiredValid.paymentNumber} תשלומים`;
			}
			if (requiredValid.creditConfirmationNumber) {
				text += `, מס׳ אישור: ${requiredValid.creditConfirmationNumber}`;
			}
			return text;

		case 'bankTransfer':
			return `מספר אסמכתא: ${requiredValid.bankTransferRefNumber}`;

		case 'check':
			text = `מספר המחאה: ${requiredValid.checkNumber}`;
			if (requiredValid.checkBankNumber) {
				text += `, קוד בנק: ${requiredValid.checkBankNumber}`;
			}
			if (requiredValid.checkBranchNumber) {
				text += `, סניף: ${requiredValid.checkBranchNumber}`;
			}
			if (requiredValid.checkAccountNumber) {
				text += `, מספר חשבון: ${requiredValid.checkAccountNumber}`;
			}

			return text;
		default:
			return text;
	}
}

function checkRequiredFields(modalEl) {
	const type = modalEl.getAttribute('data-type');
	let getInputFrom = modalEl;
	if (type === 'credit') {
		getInputFrom = modalEl.querySelector(`[data-type-block="${modalEl.getAttribute('data-type-open')}"]`);
	}

	let requiredFields = [...getInputFrom.querySelectorAll('[required]')];
	if (type === 'credit') {
		requiredFields.push(modalEl.querySelector('[name="creditPaymentSettings"]'));
		requiredFields.push(modalEl.querySelector('[name="paymentNumber"]'));
		requiredFields.push(modalEl.querySelector('[name="creditSavedCardId"]'));
		requiredFields.push(modalEl.querySelector('[name="loginOrderId"]'));
	}

	console.log('[requiredFields]', requiredFields);
	const data = {};
	for (let i = 0; i < requiredFields.length; i++) {
		const el = requiredFields[i];
		const name = el.getAttribute('name');
		const nodeName = el.nodeName.toLowerCase();

		if(name === 'creditSavedCard') {
			if(parseFloat(el.options[el.selectedIndex].getAttribute("data-max-value")) < parseFloat(checkoutTotalPriceEl.value)) {
				updateDocsPaymentPriceMax(el);
				return false;
			}
		}
		if (nodeName === 'input') {
			const elValueLength = el.value.length;
			const attrMax = el.getAttribute('maxlength');
			const attrMin = el.getAttribute('minlength');
			if (elValueLength === 0
				|| (attrMax && elValueLength > parseInt(attrMax))
				|| (attrMin && elValueLength < parseInt(attrMin))) {
				debugger
				addClass(el, 'error');
				continue;
			}
		} else if (nodeName === 'select') {
			debugger
			if (el.querySelector('[selected="selected"]') === null) {
				if (el.classList.contains('bsapp--custom-select_hidden')) {
					debugger
					addClass(el.closest('.bsapp--custom-select'), 'error');
				}
				debugger
				addClass(el, 'error');
				continue;
			}
		}

		if (name === 'paymentNumber') {
			const paymentMethodEl = modalEl.querySelector('[name="paymentMethod"]');
			data[name] = paymentMethodEl && paymentMethodEl.value === "1" ? el.value : "1";
		} else {
			data[name] = el.value;
		}
	}

	// console.log('[checkRequiredFields]', getObjectSize(data), '===', requiredFields.length, data);
	return getObjectSize(data) === requiredFields.length ? data : false;
}

export function buildPdfDoc() {
	const checkoutObject = getCheckoutObject();
	const cartObject = getCartObject();
	if (!isMobile.matches) {
		const studioInfoTableEl = document.getElementById('studioInfoTable');
		checkoutDocsPreviewEl.innerHTML = pdfDocBox({
			isRefund: checkoutObject.isRefundPage ?? false,
			studioBg: studioInfoTableEl && studioInfoTableEl.getAttribute('data-studio-bg') || '#0D7DFF',
			studioColor: studioInfoTableEl && studioInfoTableEl.getAttribute('data-studio-color') || '#FFF',
			dateStr: setDate(new Date(), false, '/'),
			client: options.helpData.client,
			cart: cartObject,
			hasOrder: checkoutObject.orderId !== null,
			cartSubtotalPrice: reduceTotal(cartObject.items, 'totalPrice'),
			transactions: checkoutObject.transactions,
			transactionsTotalPrice: reduceTotal(checkoutObject.transactions)
		});
	}

	// update a main price input value
	if (checkoutTotalPriceEl !== null) {
		// console.log('buildPdfDoc', checkoutObject.checkoutTotalPrice, checkoutObject.restTotalPrice, cartObject.totalPrice);
		setDisplayNumber(checkoutTotalPriceEl, checkoutObject.restTotalPrice !== null ? checkoutObject.restTotalPrice : checkoutObject.checkoutTotalPrice);
		if (checkoutTotalPriceEl.classList.contains('error')) {
			debugger
			removeClass(checkoutTotalPriceEl, 'error');
			setError(checkoutTotalPriceEl);
		}
	}
}