import "print-js/dist/print.css";
import printJS from 'print-js';
import confirmAsideContent from "@partials/checkout/confirmAsideContent.hbs";
import {Lang} from "@modules/Lang";
import {getItemDataToSend, getCartObject} from "@modules/cart/CartHelpers";
import {
	addClass, removeClass, sendFetch, setBodyPreload, setPhoneWithCountryCode, setPhoneWithCountryCodeWithoutPlus,
	showErrorModal, validateByRegExp, getPdfUrl, withoutProperty, tinyUrl
} from "@modules/Helpers";
import {getCheckoutObject, getCheckoutReceipts, getCheckOrderId, isRefundPage, setHasRefundCredit} from "@modules/checkout/CheckoutHelpers";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	options, headerEl, summaryAsideEl, cookieCartItemsToSaveName, cartControllerUrl
} = cartGlobalVariable;

let isInit = false;

// Private variables
const hideClass = 'd-none';
const confirmationAsideContentEl = document.getElementById('confirmationAsideContent');
const shareWhatsAppEl = document.getElementById('shareWhatsApp');
const printInvoicePdfEl = document.getElementById('printInvoicePdf');
const shareSMSEl = document.getElementById('shareSMS');
const inputPhoneEl = document.querySelector('[name="phone"]');
const ClientInfo = {id: null, fname: null, lname: null, phone: null, img: null, isRandom: null};
const BusinessInfo = {num: null, name: null, type: null, app: null, city: null, street: null};
const InvoiceInfo = {id: null, idTable: null, type: null, typeNumber: null};


export function init() {
	initEvents();
	goToPaymentConfirmation();
}

function initEvents() {
	if (isInit) {
		return false;
	}
	inputPhoneEl.addEventListener("change", function(e) {
		const el = e.currentTarget;
		console.log("change", !validateByRegExp(el), el.value === '');
		if (!validateByRegExp(el) || el.value === '') {
			return false;
		}
		// // set phonenumber for whatsapp - onchange
		if(shareWhatsAppEl){
			shareWhatsAppEl.setAttribute('phone', el.value);
		}
	});

	// set phonenumber for whatsapp
	inputPhoneEl.addEventListener('input', function(e){
		shareWhatsAppEl.setAttribute('phone', e.target.value);
	});
	
	// print PDF
	printInvoicePdfEl.addEventListener('click', e => {
		debugger
		let checkoutPrintDocEl = document.getElementById('checkoutInvoiceNumber');
		if (checkoutPrintDocEl === null) {
			document.querySelector('.js--receipt-number-link');
			checkoutPrintDocEl = document.getElementsByClassName('js-checkout-receipt-number')[0];
			if (checkoutPrintDocEl === null) {
				return false;
			}
		}
		const url = getPdfUrl({
			el: checkoutPrintDocEl
		});
		// open print modal dialog
		printJS({
			printable: url,
			type: 'pdf',
			onLoadingStart: () => {
				setBodyPreload(true);
			},
			onLoadingEnd: () => {
				setBodyPreload(false);
			}
		});
	});

	// share by SMS request
	shareSMSEl.addEventListener('click', e => {
		debugger;//todo
		e.preventDefault();
		const checkoutInvoiceNumberEl = document.getElementById('checkoutInvoiceNumber');
		const checkoutReceiptNumberEl = document.querySelector('.js--receipt-number-link');
		if (!validateByRegExp(inputPhoneEl)) {
			return false;
		}
		sendFetch(cartControllerUrl, {
			action: 'shareBySMS',
			phone:  inputPhoneEl.value,
			invoiceId: checkoutInvoiceNumberEl ? checkoutInvoiceNumberEl.getAttribute('doc-id'): 0,
			receiptId: checkoutReceiptNumberEl ? checkoutReceiptNumberEl.getAttribute('doc-id') : 0
		}).then(function (response) {
			if (!response.success) {
				showErrorModal({
					error: response.message
				});
				return false;
			}
			showErrorModal({
				textKey: 'message_sent',
				classIcon: 'fa-light fa-circle-check'
			});
		});
	});

	if(shareWhatsAppEl && !shareWhatsAppEl.getAttribute('unbubbling')){
		shareWhatsAppEl.setAttribute('unbubbling', true);
		shareWhatsAppEl.addEventListener('click', e => {
			if (!validateByRegExp(inputPhoneEl)) {
				e.preventDefault();
				return false;
			}
			shareByWhatsAppUrl(shareWhatsAppEl.getAttribute('phone'));
		});
	}

	if (!isInit) {
		isInit = true;
	}
}

function getUrlForShare(el) {
	let pdfUrl;
	if (el && el.getAttribute('copy-href') !== null && (el.getAttribute('copy-href') !== '' || el.getAttribute('copy-href') !== '#')) {
		pdfUrl = el.getAttribute('copy-href');
	} else {
		pdfUrl = getPdfUrl({
			el
		});
	}
	if (!(pdfUrl.startsWith('http://') || pdfUrl.startsWith('https://'))) {
		pdfUrl = window.location.origin + pdfUrl;
	}
	return pdfUrl;
}

function shareByWhatsAppUrl(phoneValue) {
	const checkoutInvoiceNumberEl = document.getElementById('checkoutInvoiceNumber');
	const checkoutReceiptNumberEl = document.querySelector('.js--receipt-number-link');
	const phoneNumber = setPhoneWithCountryCode(phoneValue);
	let paymentUrlMessage = getSharingMessageTemplate(InvoiceInfo.type, checkoutReceiptNumberEl);
	new Promise((resolve, reject) => {
		if (checkoutInvoiceNumberEl !== null)  {
			const invoiceHref = getUrlForShare(checkoutInvoiceNumberEl, true);
			if (invoiceHref) {
				tinyUrl(invoiceHref).then(tinyedUrl => {
					if(tinyedUrl){
						paymentUrlMessage = paymentUrlMessage.replace(/INVOICELINK/, encodeURIComponent(tinyedUrl));
						resolve();	
					}else{
						reject(Lang('action_not_done'));
					}
				});
			}
		} else {
			resolve();
		}
	}).then(()=>{
		if (checkoutReceiptNumberEl !== null && checkoutInvoiceNumberEl !== undefined) {
			const receiptHref = getUrlForShare(checkoutReceiptNumberEl, true);
			if (receiptHref) {
				tinyUrl(receiptHref).then(tinyedUrl => {
					if(tinyedUrl){
						paymentUrlMessage = paymentUrlMessage.replace(/RECEIPTLINK/, encodeURIComponent(tinyedUrl));
					} else{
						reject(Lang('action_not_done'));
					}
				});
			}
		}
	}).then(()=>{
		if (paymentUrlMessage !== '') {
			window.open(`https://wa.me/${setPhoneWithCountryCodeWithoutPlus(phoneNumber)}?text=${paymentUrlMessage}`, "_blank");
		} else{
			showErrorModal({error: Lang('action_not_done')});
		}
	}).catch((reason) => {
		showErrorModal({error: reason});
	});
}

function getSharingMessageTemplate(invoiceType, receipt){
	const isRefund = isRefundPage();
	const clientName = ClientInfo.isRandom ? Lang('dear_client') : ClientInfo.fname;
	let message = `${Lang('hi_corona_cron')} ${clientName}, ${Lang('attached_hereto')} `;
	let forPurchaseAtAndWatch = ` ${isRefund ? Lang('for_refund_in') : Lang('for_purchase_in')} ${BusinessInfo.app}, ${Lang('to_watch')}`;
	if(invoiceType == 320) message += `${Lang('doc_invoice_receipt')} ${forPurchaseAtAndWatch}: INVOICELINK `;
	else if(!receipt) message += `${Lang('invoice_single')} ${forPurchaseAtAndWatch}: INVOICELINK `;
	else if(isRefund) {
		message += `${Lang('receipt')} ${forPurchaseAtAndWatch}: RECEIPTLINK `;
	}
	else{
		message += `${Lang('docs')} ${forPurchaseAtAndWatch} ${Lang('in_invoice')}: INVOICELINK, ${Lang('to_watch_receipt')}: RECEIPTLINK`;
	} 
	return message;
}

function goToPaymentConfirmation() {
	const cartObject = getCartObject();
	const checkoutObject = getCheckoutObject();
	const data = {
		action: 'goToPaymentConfirmation',
		clientId: cartObject.clientId,
		items: cartObject.items.map(getItemDataToSend),
		transactions: checkoutObject.transactions.map(el => {
			return withoutProperty(el, ['id', 'dateCreated', 'details', 'typeKey']);
		})
	};
	if (checkoutObject.orderId) {
		data.orderId = checkoutObject.orderId;
	}
	if (checkoutObject.checkOrderId) {
		data.checkOrderId = checkoutObject.checkOrderId;
	}
	if (cartObject.discount && cartObject.discount.amount !== 0) {
		data.discount = cartObject.discount;
	}
	if (options.helpData.client && options.helpData.client.isNew) {
		data.clientDetails = options.helpData.client;
		delete data.clientId;
	}

	// click to generate a tax / receipt invoice button
	sendFetch(
		cartControllerUrl,
		data
	).then(function (response) {
		if (!response.success) {
			showErrorModal({
				error: response.message
			});
			return false;
		}

		window.sessionStorage.removeItem(cookieCartItemsToSaveName);
		setClient(response.client.id, response.client.fname, response.client.lname, response.client.name, response.client.phone, response.client.img, response.client.isRandomClient);
		setBusiness(response.business.companyNum, response.business.companyName, response.business.businessType, response.business.appName);
		setInvoice(response.invoice.docId, response.invoice.docTypeHeader);
		showConfirmationStep({
			client: response.client, 
			invoice: response.invoice,
			receipts: response.receipts
		});
	});
}

export function showConfirmationStep(props = {}) {
	initEvents();
	let {
		client = null,
		business = null,
		invoice = null,
		// receipts = getCheckoutReceipts(),
		receipts = null,
		textKey = '',
		isRefundPage = false,
		action = '',
	} = props;
	setHasRefundCredit(false);

	const confirmationAsideEl = document.getElementById('confirmationAside');
	const checkoutBtnOptionsEl = document.getElementById('checkoutBtnOptions');
	// const checkoutTransactionsPreviewEls = document.querySelectorAll('[data-type="transaction"]');
	const headerBarTitle = headerEl.querySelector('.header--bar-mob-title');

	// insert invoice number and create a link for docs preview
	// change a main title if the client is in debt, depends on translation key
	confirmationAsideContentEl.innerHTML = confirmAsideContent({
		clientId: client.id ?? false,
		isRefund: isRefundPage,
		invoice,
		receipts,
		titleKey: textKey !== '' ? textKey : 'checkout_doc_generated'
	});

	if(client) {
		setClient(client.id, client.fname, client.lname, client.name, client.phone, client.img, client.isRandomClient);
	}
	if(business) {
		setBusiness(business.companyNum, business.companyName, business.businessType, business.appName);
	}

	if (checkoutBtnOptionsEl !== null) {
		addClass(checkoutBtnOptionsEl, 'hidden');
	}

	// insert user phone number to phone fields
	if (inputPhoneEl && client.phone && !client.isRandomClient) {
		inputPhoneEl.value = client.phone;

		// set phonenumber for whatsapp
		if(shareWhatsAppEl && client.phone){
			shareWhatsAppEl.setAttribute('phone', client.phone)
		}
		
		// oninput phone field set phonenumber for whatsapp
		inputPhoneEl.addEventListener('input', function(e){
			shareWhatsAppEl.setAttribute('phone', e.target.value);
		});

			
		// set whatsapp share link/s onclick whatsapp btn
		if(shareWhatsAppEl && !shareWhatsAppEl.getAttribute('unbubbling')){
			shareWhatsAppEl.setAttribute('unbubbling', true);
			shareWhatsAppEl.addEventListener('click', function() {
				shareByWhatsAppUrl(shareWhatsAppEl.getAttribute('phone'));
			});	
		}
	}
	// change a main aside top title to summary translations
	if (headerBarTitle) {
		headerBarTitle.textContent = Lang('summary_2');
	}
	// hide transaction preview blocks if the client is in debt
	// if (action === 'customerKeepInDebt' && checkoutTransactionsPreviewEls.length > 0) {
	// 	addClass(checkoutTransactionsPreviewEls, hideClass);
	// }

	addClass(headerEl.querySelector('.bsapp--to-cart-page'), hideClass);
	addClass(summaryAsideEl, hideClass);
	debugger
	removeClass(confirmationAsideEl, hideClass);
}

function setClient(id, fname, lname, fullname, phone, img, isRandom){
	ClientInfo.id = id ?? null;
	ClientInfo.fname = fname ? fname : fullname;
	ClientInfo.lname = lname ?? null;
	ClientInfo.phone = phone ?? null;
	ClientInfo.img = img ?? null;
	ClientInfo.isRandom = isRandom ?? 0;
}

function setBusiness(num, name, type, app, city, street){
	BusinessInfo.num = num ?? null;
	BusinessInfo.name = name ?? null;
	BusinessInfo.type = type ?? null;
	BusinessInfo.app = app ?? null;
	BusinessInfo.city = city ?? null;
	BusinessInfo.street = street ?? null;
}

function setInvoice(id, type){
	InvoiceInfo.id = id;
	InvoiceInfo.type = type;
}
