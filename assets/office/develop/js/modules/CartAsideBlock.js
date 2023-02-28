import "@/scss/cart/aside.scss";

import {Lang} from "@modules/Lang";
import {additionalEvents} from "@modules/cart/AdditionalEvents";
import {init as initAsideCustomer, oldClientId} from "@modules/cart/AsideClient";
import {bsappDropdownEl, init as initAsideSummary} from "@modules/cart/AsideSummary";
import {init as initAsidePayButtons} from "@modules/checkout/AsidePayButtons";
import {addClass, getSearchParam, readSession, removeClass, setBodyPreload, showErrorModal} from "@modules/Helpers";
import {
	sendAllCartItems, getCartObject, getCartClientId, getBusinessTypesWithoutVAT, clearCart, isRandomClient
} from "@modules/cart/CartHelpers";

import {getCheckoutReceipts, refundDocs, hasRefundReceipts, getDocId} from "@modules/checkout/CheckoutHelpers";

import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {buildModal, openModal} from "@modules/Modal";
import mainDropdownModal from "@partials/mainDropdownModal";
import getClientId, {clientId} from "@/js/checkout";
import checkoutPage from "@/js/checkout";
const {
	summaryAsideEl, isMobile, bsappBarEl, cartControllerUrl, cookieCartItemsToSaveName
} = cartGlobalVariable;

let isInitAside = false;

export function initCartAside() {
	if (document.getElementById('userSelect')) {
		// cart/checkouts pages - build user box, that can be added to order
		initAsideCustomer();
	}
	if (document.getElementById('asideSummary')) {
		// cart page - build items that were added to cart drawer
		initAsideSummary();
	}
	if (document.getElementById('asideCheckout')) {
		// checkout page - build buttons with different types of payment
		initAsidePayButtons();
	}

	initEvents();
}
function initEvents() {
	if (isInitAside) {
		return false;
	}

	additionalEvents();

	const showClass = "show";
	const noOverflow = "no-overflow";
	const isVisible = "is-visible";
	const returnToCartItems = document.querySelector('.js--to-cart-items');

	if (returnToCartItems) {
		returnToCartItems.addEventListener('click', (e) => {
			e.preventDefault();
			document.body.classList.remove(noOverflow);
			removeClass(summaryAsideEl, showClass);
			if (getCartObject().itemCount > 0) {
				removeClass(bsappBarEl, 'not-visible');
				addClass(bsappBarEl, isVisible);
			}
		});
	}

	document.addEventListener('click', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('#toCartAsideBtn')) {
				e.preventDefault();

				if (!isMobile.matches || summaryAsideEl === null) {
					return false;
				}
				document.body.classList.add(noOverflow);
				addClass(summaryAsideEl, showClass);
				addClass(bsappBarEl, 'not-visible');
				removeClass(bsappBarEl, isVisible);
				break;

			} else if (target.matches('.js--cart-cancel-transaction')) {
				debugger;//todo: remove
				e.preventDefault();
				const hasReceipts = getCheckoutReceipts() !== null;
				const needReason = hasReceipts || (getDocId() !== null && getDocId() > 0);

				const errorText = !needReason ?Lang('cart_refund_invoice_will_generated') :
					hasReceipts ? Lang('checkout_refund_receipt') : Lang('checkout_refund_only_invoice_text');

				const errorModalData = {};
				if(hasRefundReceipts(true)) {
					errorModalData.textKey = 'cant_cancel_refund';
					errorModalData.error = Lang('cant_cancel_refund_error_details');
				} else {
					errorModalData.textKey = 'checkout_transaction_sure_text';
					errorModalData.error = errorText;
					needReason ? errorModalData.textAreaInput = 'רשום פה את סיבת הזיכוי' : null;
					errorModalData.bottomBtns = [{
					textKey: 'checkout_remove_transaction_return_btn',
					jsId: 'cartClearAll'
					}];
				}
				// to show confirmation modal to clear cart items at the checkout
				showErrorModal(errorModalData);
				break;

			} else if (target.matches('.js--cart-clear-all')) {
				e.preventDefault();
				// to show confirmation modal to clear cart
				showErrorModal({
					textKey: 'cart_clear_sure_text',
					bottomBtns: [{
						textKey: 'cart_clear_everything',
						jsId: 'cartClearAll'
					}]
				});
				break;

			} else if (target.matches('.js--cart-clear-all-without-modal')) {
				e.preventDefault();
				// to delete user, discount and all items in the checkout
				const isCheckoutPage = document.body.classList.contains('bsapp__checkout-page');
				if (isCheckoutPage) {
					setBodyPreload(true);
					window.location.href = '/office/cart.php';
				}
				break;

			} else if (target.matches('.js--checkout-btn-options')) {
				e.preventDefault();
				const clientId = getSearchParam('u');
				if (clientId !== null){
					buildModal({
						el: bsappDropdownEl,
						html: mainDropdownModal({
							list: [
								{
									type: 'link',
									href: 'ClientProfile.php?u='+clientId,
									textKey: 'cart_summary_option_customer_card',
								},
							]
						})
					}).then(function () {
						openModal({modalEl: 'bsappDropdown', target});
					});
					break;
				} else {
					buildModal({
						el: bsappDropdownEl,
						html: mainDropdownModal({
							list: [
								{
									type: 'link',
									href: 'ClientProfile.php?u='+315128,
									textKey: 'cart_summary_option_customer_card',
								},
							]
						})
					}).then(function () {
						openModal({modalEl: 'bsappDropdown', target});
					});
					break;
				}
			} else if (target.matches('#cartClearAll')) {
				e.preventDefault();
				let reason = null;
				let data = {
					action: 'clearAllCartItems',
					modal: target.closest('.bsapp--modal')
				}
				const modal = target.closest('.bsapp--modal');
				const reasonInputEl = modal.querySelector('#js-error-input-message');
				if(reasonInputEl) {
					reason = reasonInputEl.value;
					if(reason === undefined || reason === null || reason === '') {
						return false;
					}
					data.reason = reason;
				}
				sendAllCartItems(data);
				break;

			} else if (target.matches('.js--cart-keep-in-debt')) {
				e.preventDefault();
				if (isRandomClient()) {
					showErrorModal({
						classNameModal: 'modal--smaller',
						textKey: 'cart_choose_customer_on_debt',
						linkBtn: {
							textKey: 'choose_client',
							class: 'theme--link js--open-user-sidebar'
						}
					});
					return false;
				}
				showErrorModal({
					textKey: 'cart_keep_debt_sure_text',
					error: getBusinessTypesWithoutVAT() ? Lang('cart_invoice_will_generated') : Lang('tax_will_generated'),
					bottomBtns: [{
						textKey: 'cart_keep_in_debt_btn',
						jsId: 'cartKeepInDebt'
					}]
				});
				break;

			} else if (target.matches('#cartKeepInDebt')) {
				e.preventDefault();
				sendAllCartItems({
					action: 'customerKeepInDebt',
					modal: target.closest('.bsapp--modal')
				});
				break;

			} else if (target.matches('#delayRefundAndReturn')) {
				e.preventDefault();
				window.location.href = '/office';

				break;

			} else if (target.matches('.js--docid-keep-in-debt')) {
				e.preventDefault();
				sendAllCartItems({
					action: 'docIdKeepInDebt'
				});
				break;

			} else if (target.matches('#js--create-refund-receipt')) {
				e.preventDefault();
				const modal = target.closest('.bsapp--modal');
				const reasonInputEl = modal.querySelector('#js-error-input-message');
				const reason = reasonInputEl.value;
				if(reason === undefined || reason === null || reason === '') {
					return false;
				}
				sendAllCartItems({
					action: 'refundDocs',
					remarksText: reason,
					modal: modal
				});
				break;

			} else if (target.matches('.js-popup-create-refund-receipt')) {
				e.preventDefault();
				showErrorModal({
					textKey: 'what_is_refund_reason_title',
					classIcon: 'fa-light fa-location-question',
					textAreaInput: 'רשום פה את סיבת הזיכוי',
					bottomBtns: [{
						textKey: 'checkout_generate_refund_receipt',
						jsId: 'js--create-refund-receipt'
					}]
				});
				break;
			}


			else if (target.matches('.js-delay-refund-receipt')) {
				e.preventDefault();
				showErrorModal({
					textKey: 'exit_refund_process_title',
					error: Lang('exit_refund_process_error'),
					bottomBtns: [{
						textKey: 'delay_refund_and_return',
						jsId: 'delayRefundAndReturn'
					}]
				});
					break;


			} else if (target.matches('#checkoutBtnModal')) {
				e.preventDefault();
				console.log('click to #checkoutBtnModal');
				if(!getCartClientId()){
					showErrorModal({
						classNameModal: 'modal--smaller-big',
						textKey: 'cart_select_client_popup_description',
						borderN: 'border: none;',
						classText: 'mb-2',
						bottomBtns:[
							{
								textKey: 'yes',
								jsId: 'checkoutBtn',
								class1: 'btn--errorModal btn--primary-revert'
							},
							{
								textKey: 'search_client_modal',
								class1: 'btn--errorModal btn-black js--open-user-sidebar'
							},
						],
					});
					break;
				}
			} else if(target.matches('#checkoutBtn')){
				e.preventDefault();
				console.log('click to #checkoutBtn');
				sendAllCartItems();
				break;
			}
		}
	}, false);

	isInitAside = true;
}