import "@/scss/main.scss";
import "@/scss/checkout/aside.scss";
import "@/scss/checkout/pdfContent.scss";
import mainDropdownModal from "@partials/mainDropdownModal";
import {createTransaction} from "@modules/checkout/AsidePayButtons";

import {setStorageLang} from "@modules/SetStorageLang";
import {saveClientToCart} from "@modules/cart/AsideClient";
import {sendAllCartItems, setCartObject} from "@modules/cart/CartHelpers";
import {
	setCheckoutObject, getCheckoutObject, clearAllTransactions, getRestTotalPrice, isRefundPage, getHasRefundCredit, openCheckOutOrderPopup,
	fetchCancellationPayments, getCheckOrderId, clearAllTransactionsExceptLoginOrderIds, getDocId, setCheckOrderId,
	changeCheckoutTotalPrice, getReceiptAmount, getRefundTotalOnlyReceipts
} from "@modules/checkout/CheckoutHelpers";
import {
	additionalResizeEvents, sendFetch, showErrorModal,
	getSearchParam, readSession,
	setBodyPreload, addClass, reduceTotal, closeErrorModal
} from "@modules/Helpers";
import {initModal, buildModal, openModal} from "@modules/Modal";
import {initCartAside} from "@modules/CartAsideBlock";
import {init as initAsideConfirmation} from "@modules/checkout/AsideConfirmation";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {setDisplayNumber} from "@modules/cart/AdditionalEvents";
import {Lang} from "@modules/Lang";
const {
	globalUrl, bsappDropdownEl, checkoutTotalPriceEl, cartControllerUrl, refundControllerUrl
} = cartGlobalVariable;

const redirectUrl = '/office/cart.php';

const checkoutBtnOptionsEl = document.getElementById('checkoutBtnOptions');

//todo not sure if this one or CheckoutHelpers.getRefundTotalOnlyReceipts
// function getRefundTotalOnlyReceipts(refunds) {
// 	const key = 'price';
// 	return refunds.reduce(function (accumulator, item) {
// 		if(item[key] && parseInt(item[key]) > 0)  {
// 			return accumulator + parseFloat(item[key]);
// 		}
// 		return accumulator
// 	}, 0);
// }

async function sendInitialRequest(data) {
	if (!data.action) {
		data.action = 'getRefundData';
	}
	await sendFetch(
		refundControllerUrl,
		data
	).then(function (response) {
		console.log('[refund - sendInitialRequest] response', response);
		if (!response) {
			setTimeout(function() {
				window.location.href = redirectUrl;
			}, 2000);
		} else if (!response.success) {
			showErrorModal({
				error: response.message
			});
			setTimeout(function() {
				window.location.href = redirectUrl;
			},3000);
		}
		if(response.openOrderId !== undefined && response.openOrderId > 0) {
			openCheckOutOrderPopup(response.openOrderId, response.openOrderRefund ?? false);
			return false;
		}
		let checkoutPrice = 0;
		const {client, cart, docId, receipts, refunds, transactions , typeShva} = response;
		let errorMessage = '';
		if (!receipts || receipts.length <= 0) {
			errorMessage = 'לא נמצאו תקבולים שיש לזכותם';
		} else if (!client) {
			errorMessage = 'התגלתה שגיאה בלקוח יש לנסות שנית או לפנות לתמיכה לבירור';
		} else if (!docId || docId <= 0) {
			errorMessage = 'מספר מסמך לא תקין יש יש לנסות שנית או לפנות לתמיכה לבירור';
		} else {
			if (!cart) {
				errorMessage = 'התגלתה שגיאה נסה שוב או פנה לתמיכה לבירור';
			} else {
				saveClientToCart(client);
				if(client.openOrderId !== undefined && client.openOrderId !== null && client.openOrderId > 0){
					setCheckOrderId(client.openOrderId);
				}

				const invoiceTotal = parseFloat(cart.totalPrice);
				const receiptTotalPrice = getReceiptAmount(receipts);
				const refundReceiptTotalPrice = (refunds && refunds.length > 0) ? getRefundTotalOnlyReceipts(refunds) : 0;
				const totalReceiptBalance = receiptTotalPrice - refundReceiptTotalPrice;
				if (isNaN(totalReceiptBalance) || totalReceiptBalance <= 0 || totalReceiptBalance > invoiceTotal) {
					errorMessage = 'התגלתה שגיאה בסכום האפשרי לזיכוי, נסה שוב או פנה לתמיכה לבירור';
				} else {
					checkoutPrice = totalReceiptBalance;
					setDisplayNumber(checkoutTotalPriceEl, checkoutPrice);
				}
				transactions.forEach((transaction) => {
					createTransaction('credit',
						{
							loginOrderId: transaction.loginOrderId ?? null,
							typeKey: 'credit',
							creditConfirmationNumber: transaction.creditConfirmationNumber ?? null,
							price: transaction.price ?? 0,
							creditPaymentSettings: '2',
							credit4Number: transaction.l4digit ?? null,
							paymentNumber: transaction.numPayment ?? null,
							creditOriginalChargeDate: transaction.creditOriginalChargeDate ?? null,
						}
					);
				});
				setCheckoutObject({
					typeShva: typeShva,
					orderId: docId ?? null,
					receipts: receipts,
					refundReceipts: refunds ?? [],
					checkoutTotalPrice: checkoutPrice
				});
				setCartObject({
					...cart,
					clientDetails: client,
					totalPrice: invoiceTotal
				});
			}
			if (errorMessage !== '') {
				showErrorModal({
					error: errorMessage
				});
				setTimeout(function () {
					window.location.href = redirectUrl;
				}, 3000);
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
				showErrorModal({
					textKey: 'refund_clear_repayments',
					bottomBtns: [
						{
							textKey: 'cart_clear_everything',
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
	const haveRefundCreditTransaction = getHasRefundCredit();
	const listItems = [];

	if (getRestTotalPrice() !== 0 && haveTransactions) {
		listItems.push({
			textKey: 'refund_part_receipt',
			class: 'js-popup-create-refund-receipt',
		});
	}

	// show for not full paid or have docId
	if (!haveRefundCreditTransaction) {
		listItems.push({
			textKey: haveTransactions ? 'exit_without_saving' : 'back_new_add_credit',
			type: 'link',
			href: redirectUrl
		});
	}

	// show for not full paid or have docId
	if (haveRefundCreditTransaction) {
		listItems.push({
			textKey: 'delay_refund_and_return',
			class: 'js-delay-refund-receipt',
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



	async function refundPage() {
		initEvent();
		initModal();
		const docId = getSearchParam('docId');
		const checkOrderId = getSearchParam('checkOrderId');
		const defaultAmount = getSearchParam('amount') ?? undefined;
		//todo -> where?
		if (docId === null && checkOrderId === null) {
			location.href = redirectUrl;
		}
		setCheckoutObject({
			docId: docId ? docId : null,
			checkOrderId: checkOrderId ? checkOrderId : null,
			isRefundPage: true
		});

		// get the initial data
		let data = {
			docId: docId
		};
		data.checkOrderId = checkOrderId ? checkOrderId : 0;
		await sendInitialRequest(data);
		// init aside checkout
		initCartAside();
		changeCheckoutTotalPrice(defaultAmount);

	}

	setStorageLang().then(function () {
		refundPage();
	});
