import discountModal from "@partials/cart/discountModal";
import mainDropdownModal from "@partials/mainDropdownModal";

import {Lang} from "@modules/Lang";
import {openModal, closeModal, buildModal} from "@modules/Modal";
import {showErrorModal, sendFetch, setBodyPreload, addClass} from "@modules/Helpers";
import {
	buildSummaryItems, calculateDiscountAmount, buildItemModal, getDiscount, getCartOriginalPrice,
	setDiscount, getItemFromCart, setCartObject, getCartObject
} from "@modules/cart/CartHelpers";
import {buildSingleLesson} from "@modules/cart/ItemLessonModal";

import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	bsappModalEl, bsappDropdownEl
} = cartGlobalVariable;

function init() {
	buildSummaryItems();
	initEvents();
}

function initEvents() {
	document.addEventListener('click', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--summary-total--discount')) {
				e.preventDefault();
				// to show general discount modal
				buildGeneralDiscountModal();
				break;

			} else if (target.matches('.js--cart-item-options')) {
				e.preventDefault();
				// to open an edit item modal depends on item type
				openOptionBtnModal.call(this, target, e);
				break;

			} else if (target.matches('.js--cart-btn-options')) {
				e.preventDefault();
				// to show dropdown when clicking on the 3-dots icon near checkout button
				buildModal({
					el: bsappDropdownEl,
					html: mainDropdownModal({
						list: [
							{
								jsId: "checkoutBtnModal",
								textKey: 'cart_keep_in_debt',
							},
							{
								textKey: 'cart_clear_all',
								class: 'red js--cart-clear-all'
							}
						]
					})
				}).then(function () {
					openModal({modalEl: 'bsappDropdown', target});
				});
				break;
			} else if (target.matches('.js--delete-general-discount')) {
				e.preventDefault();
				// to delete general cart discount
				setCartObject({discount: setDiscount()});
				buildSummaryItems();
				closeModal('bsappModal');
				break;

			} else if (target.matches('#saveGeneralDiscount')) {
				e.preventDefault();
				// to save general cart discount
				const parEl = target.closest('.modal-dialog');
				const discountObj = getDiscount(parEl);
				const cartObject = getCartObject();
				if (cartObject.discount.amount !== discountObj.amount || cartObject.discount.type !== discountObj.type) {
					setCartObject({discount: setDiscount(discountObj)});
					buildSummaryItems();
				}
				closeModal('bsappModal');
				break;

			}
		}
	}, false);
}

function openOptionBtnModal(target, e) {
	const btn = target.querySelector('.cart--options-btn');
	if (!btn) {
		return false;
	}

	const targetId = btn.getAttribute('data-id');
	const targetCartId = btn.getAttribute('data-item-current-cart-id');
	if (targetId === null || targetCartId === null) {
		return false;
	}

	addClass(target, 'disabled');
	setBodyPreload(true);
	let item = getItemFromCart(targetCartId, 'itemCurrentCartId');
	const targetType = btn.getAttribute('data-type');

	if (targetType === 'lesson') {
		// get updated data from the response
		// const responseItem = await getItemDetails({
		// 	id: item.id,
		// 	type: item.type,
		// 	url: globalUrl + '/getLessonData.json'
		// });

		// get additional lesson data from old item
		const oldAdditionalData = item.additionalData ? JSON.parse(item.additionalData) : {};

		// merged additional old data with new one, which is received from the request
		// const newAdditionalData = Object.assign({}, oldAdditionalData, responseItem);

		// prepare lesson with parameters to build cart drawer
		const newItem = buildSingleLesson({...item, ...oldAdditionalData});
		// update item for Edit modal
		item = Object.assign({}, item, newItem);

	// } else if (targetType === 'service') {
		// get updated data from the response
		// const responseItem = await getItemDetails({
		// 	id: item.id,
		// 	type: item.type,
		// 	url: globalUrl + '/getServiceData.json'
		// });
		// const newItem = Object.assign({}, responseItem, item);
		// // update item for Edit modal
		// item = buildSingleItemByType(newItem, targetType);
	}

	// build Edit modal
	buildItemModal(item, target);
}

export async function getItemDetails(props) {
	const {id, type, url} = props;
	// update item data before open an edit modal
	return await sendFetch(url, {
		action: 'getItemDetails',
		id,
		type
	}).then(function(response) {
		if (!response.success) {
			// show error modal
			showErrorModal({
				error: response.message
			});
			return false;
		}

		const {item} = response;
		// returns a response anyway if the type is product (since a product may not have variants)
		if (type !== 'product' && !item) {
			throw new Error('[getItemDetails] Invalid of received items data, expected items array');
		}

		return item;
	});
}

function buildGeneralDiscountModal() {
	// if (!bsappModalEl.classList.contains('footer--big-full-mob')) {
	// 	bsappModalEl.classList.add('footer--big-full-mob');
	// }

	const cartObject = getCartObject();
	const originalPrice = getCartOriginalPrice();
	const discount = calculateDiscountAmount(originalPrice, cartObject.discount.type, cartObject.discount.value);
	const data = {
		discount,
		originalPrice: {
			text: Lang('subtotal').replace(':', ''),
			price: originalPrice
		},
		subtotalPrice: {
			text: Lang('total_discount'),
			price: discount.amount || 0,
			originalPrice
		},
		deleteIcon: {
			// itemCurrentCartId: cartObject.discount && cartObject.discount.amount !== 0,
			itemCurrentCartId: cartObject.itemCurrentCartId,
			className: 'js--delete-general-discount'
		}
	};

	buildModal({
		el: bsappModalEl,
		html: discountModal(data)
	}).then(function() {
		openModal({modalEl: 'bsappModal'});
	});
}

export { init, bsappDropdownEl };