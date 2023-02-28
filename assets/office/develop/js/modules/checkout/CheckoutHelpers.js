// main CHECKOUT data
import partialTransactions from "@partials/checkout/partialTransactions";
import {getCartObject, getDiscountedPrice} from "@modules/cart/CartHelpers";
import {
	addClass, getPdfUrl, reduceTotal, removeClass, setBodyPreload, toggleAttribute, sendFetch, showErrorModal, withoutProperty
} from "@modules/Helpers";
import {buildPdfDoc} from "@modules/checkout/AsidePayButtons";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {Lang} from "@modules/Lang";

const {
	options, isMobile, checkoutTotalPriceEl, bsappHalfSidebarEl, cartControllerUrl, cookieCartItemsToSaveName, refundControllerUrl
} = cartGlobalVariable;


let checkoutObject = {
	transactions: [],
	isRefundPage: false,
	hasRefundCredit: false,
	restTotalPrice: null,
	checkoutTotalPrice: null,
	orderId: null,
	checkOrderId: null,
	bankTransferType: '1', // default value is Bank Transfer
	creditPaymentSettings: '3', // default value is Manual credit
	paymentMethod: '0', // default value is Regular
	paymentNumber: '2' // default value is 2 times
};

export function getRefundTotalOnlyReceipts(refunds) {
	const key = 'price';
	return refunds.reduce(function (accumulator, item) {
		if(item[key] && parseFloat(item[key]) > 0)  {
			return accumulator + parseFloat(item[key]);
		}
		return accumulator
	}, 0);
}

export function getReceiptAmount(receipts) {
	const key = 'price';
	return receipts.reduce(function (accumulator, item) {
		if(item[key]){
			return accumulator + Math.abs(parseFloat(item[key]));
		}
		return accumulator
	}, 0);
}

export function setHasRefundCredit(hasRefundCredit)
{
	checkoutObject.hasRefundCredit = hasRefundCredit;
}

export function getHasRefundCredit()
{
	return checkoutObject.hasRefundCredit;
}

export function getCheckoutObject() {
	return checkoutObject;
}

export function getCheckoutTransactions() {
	if (checkoutObject.transactions.length === 0) {
		return null;
	}
	return checkoutObject.transactions;
}

export function getCheckoutReceipts() {
	if (!checkoutObject.receipts || checkoutObject.receipts.length === 0) {
		return null;
	}
	return checkoutObject.receipts;
}

export function getCheckoutRefundReceipts() {
	if (!checkoutObject.refundReceipts || checkoutObject.refundReceipts.length === 0) {
		return null;
	}
	return checkoutObject.refundReceipts;
}

export function hasRefundReceipts(onlyRefundReceipts = false) {
	if (!checkoutObject.refundReceipts || checkoutObject.refundReceipts.length === 0) {
		return false;
	}
	if (onlyRefundReceipts) {
		return checkoutObject.refundReceipts.some(receipt => receipt.price > 0)
	}
	return true;

}



export function getRestTotalPrice() {
	return checkoutObject.restTotalPrice;
}

export function getCheckOrderId() {
	return checkoutObject.checkOrderId ?? undefined;
}

export function getDocId() {
	return checkoutObject.orderId ?? undefined;
}

export function setCheckOrderId(checkOrderId) {
	checkoutObject.checkOrderId = checkOrderId;
}

export function setCheckoutObject(newCheckoutData) {
	checkoutObject = Object.assign({}, checkoutObject, newCheckoutData);
}

export function setNewTransaction(newData) {
	checkoutObject.transactions.push(newData);
}

export function deleteTransaction(pos) {
	checkoutObject.transactions.splice(pos, 1);
}

export function clearAllTransactionsExceptLoginOrderIds(loginOrderIds, returnToCart = false) {
	if(loginOrderIds.length === 0) {
		clearAllTransactions();
		return;
	}
	const newTransactions = [];
	checkoutObject.transactions.forEach((transaction) => {
		if (loginOrderIds.includes(isNaN(parseInt(transaction.loginOrderId)) ? 0 : parseInt(transaction.loginOrderId))) {
			newTransactions.push(transaction);
		}
	});
	checkoutObject.transactions = newTransactions;
	buildTransactions();
	if(returnToCart) {
		setBodyPreload(true);
		window.location.href = '/office/cart.php';
	}
}

export function clearAllTransactions(returnToCart = false) {
	checkoutObject.transactions = [];
	buildTransactions();
	if (returnToCart) {
		setBodyPreload(true);
		window.location.href = '/office/cart.php';
	}
}

export function preventChangeClient() {
	// to prevent changing user on the checkout page
	const userSelectEl = document.getElementById('userSelect');
	// const checkoutObject = getCheckoutObject();

	// console.log('preventChangeClient', options.helpData.client !== undefined && (checkoutObject.transactions.length > 0 || checkoutObject.orderId !== null), checkoutObject.transactions.length, checkoutObject);
	if (options.helpData.client !== undefined && (checkoutObject.transactions.length > 0 || checkoutObject.orderId !== null)) {
		addClass(userSelectEl, 'disabled');
	} else {
		removeClass(userSelectEl, 'disabled');
	}
}

export function buildTransactions() {
	// const totalPrice = getCartObject().totalPrice;
	const totalPrice = checkoutObject.checkoutTotalPrice;
	const partialPrice = reduceTotal(checkoutObject.transactions);
	let newPrice = 0;
	if (totalPrice > partialPrice) {
		// calculate the remaining amount to pay for this order
		newPrice = getDiscountedPrice(totalPrice, partialPrice);
	}
	checkoutObject.restTotalPrice = newPrice;

	// close pay sidebar
	if (bsappHalfSidebarEl && !bsappHalfSidebarEl.classList.contains('hidden')) {
		toggleVisibilityHalfSidebar();
	}

	// console.log('[buildTransactions] checkoutObject', checkoutObject.restTotalPrice, partialPrice, checkoutObject);

	const receipts = checkoutObject.receipts;
	if (receipts instanceof Array && receipts.length > 0) {
		for (let i = 0; i < receipts.length; i++) {
			const el = receipts[i];
			if (el.DocType && el.DocId) {
				// create url for the PDF document
				el.pdfUrl = getPdfUrl({
					type: el.DocType,
					id: el.DocId
				});
			}
		}
	}

	const refundReceipts = checkoutObject.refundReceipts ?? [];
	if (refundReceipts instanceof Array && refundReceipts.length > 0) {
		for (let i = 0; i < refundReceipts.length; i++) {
			const el = refundReceipts[i];
			if (el.DocType && el.DocId) {
				// create url for the PDF document
				el.pdfUrl = getPdfUrl({
					type: el.DocType,
					id: el.DocId
				});
			}
		}
	}

	const checkoutBottomTransactionEl = document.getElementById('checkoutBottomTransaction');

	const showConfirmedBtn = checkoutObject.restTotalPrice === 0 && checkoutObject.transactions.length > 0;
	const clearingReceiptsBtn = checkoutObject.isRefundPage && checkoutObject.hasRefundCredit ?  false : showConfirmedBtn

	checkoutBottomTransactionEl.innerHTML = partialTransactions({
		receipts,
		refundReceipts,
		isRefund: checkoutObject.isRefundPage,
		transactions: checkoutObject.transactions,
		subTotalPrice: checkoutObject.transactions.length > 1 ? partialPrice : null,
		showConfirmedBtn: showConfirmedBtn,
        clearingReceiptsBtn: clearingReceiptsBtn,
		restTotalPrice: totalPrice === 0 && checkoutObject.restTotalPrice === 0 ? null : checkoutObject.restTotalPrice
	});

	const summaryAsideEl = document.getElementById('summaryAside');
	if (checkoutObject.transactions.length === 0 || checkoutObject.restTotalPrice !== 0) {
		removeClass(summaryAsideEl, 'no-rest-pay');
	} else {
		addClass(summaryAsideEl, 'no-rest-pay');
	}

	// hide opportunity to change client
	preventChangeClient();

	// update PDF document preview and total price value
	buildPdfDoc();
}

export function toggleAttributeWithDropdown(el, isSet, attrName = 'disabled') {
	toggleAttribute(el, isSet, attrName);

	const elCustomDropdown = el.closest('.bsapp--custom-select');
	if (elCustomDropdown && elCustomDropdown.querySelector('input')) {
		if (isSet) {
			elCustomDropdown.querySelector('input').setAttribute(attrName, attrName);
		} else {
			elCustomDropdown.querySelector('input').removeAttribute(attrName);
		}
	}
}

export function closeHalfSidebar() {
	preventChangeClient();
	toggleAttributeWithDropdown(checkoutTotalPriceEl, false);
	toggleVisibilityHalfSidebar();
}

export function toggleVisibilityHalfSidebar(hide = true) {
	if (hide) {
		removeClass(document.getElementById('bsappHalfSidebarOverlay'), 'is-visible');
		removeClass(document.getElementById('asideCheckout'), 'hidden');
		removeClass(document.getElementById('checkoutBottomTransaction'), 'hidden');
		removeClass(document.getElementById('checkoutBottomOptions'), 'hidden');
		addClass(bsappHalfSidebarEl, 'hidden');
	} else {
		removeClass(bsappHalfSidebarEl, 'hidden');
		addClass(document.getElementById('bsappHalfSidebarOverlay'), 'is-visible');
		addClass(document.getElementById('asideCheckout'), 'hidden');
		addClass(document.getElementById('checkoutBottomTransaction'), 'hidden');
		addClass(document.getElementById('checkoutBottomOptions'), 'hidden');
	}
}

export async function fetchCancellationPayment(loginOrderId) {
	console.log(`fetchCancellationPayment of order - ${loginOrderId}`);
	const data = {
		action: 'cancelPaymentWithOutReceipt',
		loginOrderId: loginOrderId
	};
	return await sendFetch(
		cartControllerUrl,
		data
	).then(function (response) {
		if (!response.success) {
			showErrorModal({
				error: response.message
			});
			return false;
		}
		return true;
	});
}

export async function fetchCancellationPayments(checkOutOrderId) {
	console.log(`fetchCancellationPayments of checkOutOrderId - ${checkOutOrderId}`);
	const data = {action: 'cancelAllPaymentsWithOutReceipt',
		checkOrderId : checkOutOrderId
	};
	return await sendFetch(
		cartControllerUrl,
		data
	).then(function(response) {
		if (!response.success) {
			return response.ids ?? [];
		}
		return [];
	});
}

export async function openCheckOutOrderPopup(checkOutOrderId, isRefundPage = false) {
	window.sessionStorage.removeItem(cookieCartItemsToSaveName);
	showErrorModal({
		textKey: 'open_checkout_popup_title',
		error: Lang('open_checkout_popup_error'),
		jsCloseId: 'js-redirect-cart',
		bottomBtns: [
			{
				textKey: 'open_checkout_popup_btn',
				jsId: 'js-redirect-close-order',
				dataIdParam: checkOutOrderId,
				dataIsRefund: isRefundPage,
			}
		]
	});
}

export function isRefundPage() {
	return checkoutObject.isRefundPage;
}

export function getTypeShva(){
	return checkoutObject.typeShva;
}

export function changeCheckoutTotalPrice(amount) {
	if(amount !== undefined && amount !== null && amount > 0 ) {
		checkoutTotalPriceEl.value = amount;
		checkoutTotalPriceEl.fireEvent("onchange");
	}
}