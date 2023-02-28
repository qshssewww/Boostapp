import "@/scss/main.scss";
import "@/scss/checkout/aside.scss";
import "@/scss/checkout/pdfContent.scss";
import mainDropdownModal from "@partials/mainDropdownModal";
import {createTransaction} from "@modules/checkout/AsidePayButtons";

import {setStorageLang} from "@modules/SetStorageLang";
import {saveClientToCart} from "@modules/cart/AsideClient";
import {sendAllCartItems, setCartObject, isRandomClient} from "@modules/cart/CartHelpers";
import {
	setCheckoutObject, getCheckoutObject, clearAllTransactions, getRestTotalPrice, openCheckOutOrderPopup,
	fetchCancellationPayments, getCheckOrderId, clearAllTransactionsExceptLoginOrderIds, getDocId,
	getRefundTotalOnlyReceipts, getReceiptAmount
} from "@modules/checkout/CheckoutHelpers";
import {
	sendFetch, showErrorModal, additionalResizeEvents, getSearchParam, readSession,
	setBodyPreload, addClass, reduceTotal, closeErrorModal
} from "@modules/Helpers";
import {initModal, buildModal, openModal} from "@modules/Modal";
import {initCartAside} from "@modules/CartAsideBlock";
import {init as initAsideConfirmation} from "@modules/checkout/AsideConfirmation";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {setDisplayNumber} from "@modules/cart/AdditionalEvents";
import {Lang} from "@modules/Lang";
import {isRefundPage} from "./modules/checkout/CheckoutHelpers";
const {
	globalUrl, bsappDropdownEl, checkoutTotalPriceEl, cookieCartItemsToSaveName, cartControllerUrl
} = cartGlobalVariable;
const checkoutBtnOptionsEl = document.getElementById('checkoutBtnOptions');
async function checkoutPage() {
	initEvent();
	initModal();
	let data = {};
	const docId = getSearchParam('docId');
	const checkOrderId = getSearchParam('checkOrderId');
	setCheckoutObject({
		docId: docId ? docId : null,
		checkOrderId: checkOrderId ? checkOrderId : null,
	});
	// const cart = readJsCookie(cookieCartItemsToSaveName);
	const cart = docId || checkOrderId ? null : readSession(cookieCartItemsToSaveName);
	console.log('[checkoutPage] cart', cart);
	if (checkOrderId === null && docId === null && (cart === null || document.referrer.indexOf('cart.php') === -1)){
		location.href = '/office/cart.php';
	}

	if (cart !== null && docId === null && document.referrer.indexOf('cart.php') > -1) {
		setCartObject(cart);
		if (cart.totalPrice !== null && checkoutTotalPriceEl) {
			setCheckoutObject({
				checkoutTotalPrice: cart.totalPrice
			});
		}
		if (cart.clientDetails) {
			saveClientToCart(cart.clientDetails);
		}
	}
	if (docId || checkOrderId) {
		addClass(document.querySelector('.js--return-to-cart-page'), 'visibility-hidden');
		if(docId) {
			data.docId = docId;
		}
		data.checkOrderId = checkOrderId ? checkOrderId : null;
		await sendInitialRequest(data, cart ? cart.clientDetails : {});
	} else {
		setBodyPreload(false);
	}
	// init aside checkout
	initCartAside();
	if(cart !== null && cart.clientId !== null && cart.clientId > 0) {
		addClass(document.getElementById('userSelect'), 'disabled');
	}
}
async function sendInitialRequest(data, clientDetails = {}) {
	if(!data.docId && data.checkOrderId) {
		data.action = 'getCheckoutDataFromOrder';
	}
	if (!data.action) {
		data.action = 'getCheckoutData';
	}
	await sendFetch(
		cartControllerUrl,
		data
	).then(function (response) {
		console.log('[sendInitialRequest] response', response);
		if (!response) {
			setTimeout(function() {
				window.location.href = '/office/cart.php';
			}, 2000);
		} else if (!response.success) {
			showErrorModal({
				error: response.message
			});
			setTimeout(function() {
				window.location.href = '/office/cart.php';
			},3000);
			return false;
		}
		if(response.openOrderId !== undefined && response.openOrderId > 0) {
			openCheckOutOrderPopup(response.openOrderId, response.openOrderRefund ?? false);
			return false;
		}
		if(response.checkoutOrderId !== undefined && response.checkoutOrderId !== null && response.checkoutOrderId > 0) {
			const {
				vatAmount,
				businessType,
				totalPrice,
				items,
				itemCount,
				clientId,
				clientDetails,
				transactions
			} = response;
			if(!transactions) {
				showErrorModal({
					error: 'לא נמצא תשלום שיש לסגו ראותו'
				});
				setTimeout(function() {
					window.location.href = '/office/cart.php';
				},3000);
				return false;
			}
			setCartObject(response);
			if (response.totalPrice !== null && checkoutTotalPriceEl && !isNaN(parseFloat(totalPrice))) {
				setCheckoutObject({
					checkoutTotalPrice: parseFloat(totalPrice)
				});
			}
			if (clientDetails) {
				saveClientToCart(clientDetails);
			}
			transactions.forEach(
				(transaction) => {
					createTransaction('credit',
						{
							loginOrderId: transaction.loginOrderId ?? null,
							typeKey: 'credit',
							creditConfirmationNumber: transaction.creditConfirmationNumber ?? null,
							price: transaction.price ?? 0,
							creditPaymentSettings: '2' ,
							credit4Number: transaction.l4digit ?? null,
							paymentNumber: transaction.numPayment ?? null ,
							creditOriginalChargeDate: transaction.creditOriginalChargeDate ?? null,
						}
					);
				});
		}
		else {
			const {client, cart, docId, receipts, transactions, refunds} = response;
			const haveReceipts = receipts && receipts.length > 0;
			saveClientToCart(Object.assign({}, clientDetails, client));
			if (docId && cart) {
				const totalPrice = parseFloat(cart.totalPrice);
				debugger;//todo: remove
				let checkoutPrice = !isNaN(totalPrice) ? totalPrice : null;
				const receiptTotalPrice = haveReceipts && !isNaN(totalPrice) ? getReceiptAmount(receipts) : 0;
				const refundReceiptTotalPrice = (refunds && refunds.length > 0) ? getRefundTotal(refunds) : 0;
				const totalReceiptBalance = receiptTotalPrice - refundReceiptTotalPrice;
				// change the price after request, if there are already receipts
				if (totalPrice > totalReceiptBalance) {
					checkoutPrice = totalPrice - totalReceiptBalance;
					setDisplayNumber(checkoutTotalPriceEl, checkoutPrice);
				}
				transactions.forEach(
					(transaction) => {
						createTransaction('credit',
							{
								loginOrderId: transaction.loginOrderId ?? null,
								typeKey: 'credit',
								creditConfirmationNumber: transaction.creditConfirmationNumber ?? null,
								price: transaction.price ?? 0,
								creditPaymentSettings: '2' ,
								credit4Number: transaction.l4digit ?? null,
								paymentNumber: transaction.numPayment ?? null ,
								creditOriginalChargeDate: transaction.creditOriginalChargeDate ?? null,
							}
						);
					});
				setCheckoutObject({
					orderId: docId ?? null,
					refundReceipts: refunds ?? [],
					receipts: haveReceipts ? receipts : [],
					checkoutTotalPrice: checkoutPrice
				});
				setCartObject({
					...cart,
					clientDetails: client,
					totalPrice: totalPrice
				});
			}
		}
	});
}

function initEvent() {
	additionalResizeEvents();

	// open a checkout options button
	checkoutBtnOptionsEl.addEventListener('click', e => {
		e.preventDefault();
		buildBottomOptions(e);
	});

	document.addEventListener('click', async function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--go-to-payment-confirmation')) {
				e.preventDefault();
				const docId = getDocId();
				if (docId !== undefined) {
					sendAllCartItems({
						action: 'docIdKeepInDebt'
					});
				} else {
					initAsideConfirmation();
				}
				break;

			} else if (target.matches('#removeTransactions')) {
				e.preventDefault();
				const isRefund = isRefundPage();

				showErrorModal({
					textKey: isRefund ? 'refund_clear_repayments': 'checkout_clear_payments',
					error: isRefund ? '' : Lang('payments_on_credit_without_doc_will_refund'),
					bottomBtns: [
						{
							textKey: isRefund ? 'cart_clear_everything' : 'checkout_clear_payments_btn',
							jsId: 'removeTransactionsWithoutReturn'
						}
					]
				});
				break;
			} else if (target.matches('#removeTransactionsReturn')) {
				e.preventDefault();
				const checkOrderId = getCheckOrderId();
				if(checkOrderId) {
					const notValidOrderIdsArray = await fetchCancellationPayments(checkOrderId);
					if(notValidOrderIdsArray.length > 0){
						showErrorModal({
							error: 'נמצא תשלום שלא ניתן לזיכוי, יש לגשת למסוף לבדוק זאת - פרטים נוספים במערכת התראות'
						});
						clearAllTransactionsExceptLoginOrderIds(notValidOrderIdsArray, true)
						return;
					}
				}
				clearAllTransactions(true);
				break;
			} else if (target.matches('#removeTransactionsWithoutReturn')) {
				e.preventDefault();
				closeErrorModal();
				const checkOrderId = getCheckOrderId();
				if(checkOrderId) {
					const notValidOrderIdsArray = await fetchCancellationPayments(checkOrderId);
					if(notValidOrderIdsArray.length > 0){
						showErrorModal({
							error: 'נמצא תשלום שלא ניתן לזיכוי, יש לגשת למסוף לבדוק זאת - פרטים נוספים במערכת התראות'
						});
						clearAllTransactionsExceptLoginOrderIds(notValidOrderIdsArray)
						return;
					}
				}
				clearAllTransactions(false);
				break;
			} else if (target.matches('.js--remove-transaction-return')) {
				e.preventDefault();
				showErrorModal({
					textKey: 'checkout_remove_transaction_return_text',
					error: Lang('payments_on_credit_without_doc_will_refund'),
					bottomBtns: [
						{
							textKey: 'checkout_remove_transaction_return_btn',
							jsId: 'removeTransactionsReturn'
						}
					]
				});
			}
			else if (target.matches('#js-redirect-close-order')) {
				e.preventDefault();
				const openOrderId = target.getAttribute('data-id');
				const openOrderRefund = target.getAttribute('data-is-refund');
				const url = openOrderRefund !== 'true' ? '/office/checkout.php?checkOrderId=' : '/office/refund.php?checkOrderId=';
				window.location.href = url + openOrderId;
			}
		}
	});
}

function buildBottomOptions(e) {
	const el = e.target.classList.contains('btn') ? e.target : e.target.closest('.btn');
	const checkoutObject = getCheckoutObject();
	const haveTransactions = checkoutObject.transactions.length > 0;
	const hasDocId = checkoutObject.orderId !== null;
	const listItems = [];

	if (!hasDocId && getRestTotalPrice() !== 0 && !isRandomClient()) {
		listItems.push({
			textKey: haveTransactions ? 'checkout_receipt_debt' : 'checkout_debt_invoice',
			class: 'js--cart-keep-in-debt'
		});
	} else if (hasDocId && haveTransactions && !isRandomClient()) {
		listItems.push({
			textKey: 'checkout_receipt_produced',
			class: 'js--docid-keep-in-debt'
		});
	}

	// show for not full paid or have docId
	if (!(haveTransactions || hasDocId)) {
		listItems.push({
			textKey: 'checkout_back_to_cart',
			type: 'link',
			href: '/office/cart.php'
		});
	}

	if (hasDocId) {
		// cancel transaction with request
		listItems.push({
			textKey: 'checkout_cancel_all_transaction',
			class: 'red js--cart-cancel-transaction'
		});
	} else if (haveTransactions) {
		// cancel transaction without request
		listItems.push({
			textKey: 'checkout_cancel_all_transaction',
			class: 'red js--remove-transaction-return'
		});
	}

	buildModal({
		el: bsappDropdownEl,
		html: mainDropdownModal({
			list: listItems
		})
	}).then(function () {
		openModal({modalEl: 'bsappDropdown', target: el});
	});
}

function getRefundTotal(refunds) {
	const key = 'price';
	const keyDocTypeHeader = 'docTypeHeader';
	return refunds.reduce(function (accumulator, item) {
		if(item[key] && item[keyDocTypeHeader] == '400')  {
			return accumulator + parseFloat(item[key]);
		} else {
			return accumulator + (-1 * Math.abs(parseFloat(item[key])));
		}
		return accumulator
	}, 0);
}

setStorageLang().then(function() {
	checkoutPage();
});
