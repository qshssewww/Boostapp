import asideSummary from "@partials/cart/asideSummary";
import barSummary from "@partials/barSummary";
import totalPriceBox from "@partials/item/totalPriceBox";
import discountBox from "@partials/item/discountBox";
import itemEditModal from "@partials/cart/itemEditModal";

import {Lang} from "@modules/Lang";
import {buildModal, closeModal, openModal} from "@modules/Modal";
import {
	addClass, removeClass, getIndex, removeItem, withoutProperty, sendFetch,
	showErrorModal, setBodyPreload, getFilterByValue, setDurationString, getDurationType,
	getSearchParam, writeSession, showClientInLessonModal, getDateTimeMomentSubtractMin
} from "@modules/Helpers";
import {clearClient} from "@modules/cart/AsideClient";
import {showConfirmationStep} from "@modules/checkout/AsideConfirmation";
import {getCheckoutTransactions, getCheckOrderId, getDocId, isRefundPage} from "@modules/checkout/CheckoutHelpers";
import FullCalendar from "@modules/FullCalendar";
import CustomDropdown from "@modules/CustomDropdown";
import {setDefaultDuration} from "@modules/cart/AdditionalEvents";
import {disabledCategoryItem} from "@modules/cart/NavCategories";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	options, cartControllerUrl, bsappBarEl, mainEl, isMobile, bsappModalEl, discountTypePercentId,
	membershipStartCountId, onlyOneToAddList, cookieCartItemsToSaveName, businessTypesWithoutVAT, refundControllerUrl
} = cartGlobalVariable;

// main CART data
let cartObject = {
	vatAmount: 0,
	totalPrice: 0,
	discount: {
		amount: 0,
		type: '1', // '1 '= percent, '2' = shekel
		value: 0
	},
	items: []
};

const colorsSelectId = 'colorsOptionSelect';
const sizesSelectId = 'sizesOptionSelect';

export function getCartObject() {
	return cartObject;
}

export function isRandomClient() {
	return ((cartObject.clientId === undefined || cartObject.clientId === null) && (cartObject.clientDetails === undefined || cartObject.clientDetails === null)) ||
		((cartObject.clientDetails !== undefined || cartObject.clientDetails !== null) && cartObject.clientDetails.isRandomClient !== undefined && cartObject.clientDetails.isRandomClient == 1);
}

export function getCartId() {
	return cartObject.id;
}

export function getCartClientId() {
	return cartObject.clientId;
}

export function getBusinessTypesWithoutVAT() {
	if (!(businessTypesWithoutVAT || cartObject.businessType)) {
		return false;
	}
	return businessTypesWithoutVAT.includes(cartObject.businessType);
}

export function getCartIndex(key, value) {
	if (cartObject.items.length === 0) {
		return -1;
	}
	return getIndex(cartObject.items, value, key);
}

export function getItemFromCart(itemId, key = 'id') {
	return cartObject.items.find(item => item[key] === itemId);
}

export function getOriginalPrice(item) {
	return (item.price * item.quantity) - (item.discount && item.discount.amount ? item.discount.amount : 0);
}

export function getCartOriginalPrice() {
	if (cartObject.items.length === 0) {
		return 0;
	}
	const originalPrice = cartObject.items.reduce(function (accumulator, item) {
		return accumulator + getOriginalPrice(item);
	}, 0);

	return (originalPrice * 100).toFixed() / 100;
}

function getPercentPrice(originalPrice, percentAmount, sumOperation = true) {
	const originalPriceCents = originalPrice * 100;
	// const percentCents = parseFloat(Math.abs(originalPriceCents * (+(percentAmount) * 0.01)).toFixed());
	const percentCents = parseFloat(Math.round(Math.abs(originalPriceCents * (+(percentAmount) * 0.01))).toFixed());
	const priceCents = sumOperation ? originalPriceCents + percentCents : originalPriceCents - percentCents;
	return {
		percentAmount: percentCents / 100,
		price: priceCents / 100
	};
}

export function getDiscount(modalDialog) {
	const itemCurrentCartId = modalDialog.querySelector('.js--cart-detail-content').getAttribute('data-item-current-cart-id');
	const originalPriceEl = modalDialog.querySelector('[data-price-original]');
	let originalPrice = originalPriceEl && originalPriceEl.getAttribute('data-price-original');

	if (originalPrice === null && itemCurrentCartId !== null) {
		const item = getItemFromCart(itemCurrentCartId,'itemCurrentCartId');
		if (!item) {
			return false;
		}
		originalPrice = item.originalPrice;
	}
	const discountValueEl = modalDialog.querySelector('[data-discount-value]');
	let discountValue = discountValueEl && discountValueEl.value !== '' && !isNaN(discountValueEl.value) ? parseFloat(discountValueEl.value) : 0;
	const btnType = modalDialog.querySelector('.js--discount-btn.active').getAttribute('data-type');

	// console.log('getDiscount', btnType, discountValue, originalPrice);

	if (btnType === discountTypePercentId && discountValue > 100) {
		discountValue = 100;
	} else if (btnType !== discountTypePercentId && discountValue > +originalPrice) {
		discountValue = originalPrice;
	}

	return calculateDiscountAmount(parseFloat(originalPrice), btnType, discountValue);
}

export function getDiscountedPrice(originalPrice, amount) {
	if (!amount || amount === 0) {
		return originalPrice;
	}
	const originalPriceCents = originalPrice * 100;
	const priceCents = (Math.round(originalPriceCents - (amount * 100))).toFixed(0);
	return priceCents / 100;
}

function getLastService(currentId, currentDate) {
	const items = cartObject.items.filter(obj => obj.id.toString() === currentId.toString() && obj.date && obj.date === currentDate);

	if (items.length === 0) {
		return null;
	} else if (items.length === 1) {
		return items[0].time ? items[0].time : null;
	}

	items.sort(function(a, b) {
		return new Date('1970/01/01 ' + a.time.replace(/(am|pm)/,' $1')) - new Date('1970/01/01 ' + b.time.replace(/(am|pm)/,' $1'))
	});
	return items[0].time;
}

export function setCartObject(newCartData) {
	cartObject = Object.assign({}, cartObject, newCartData);
}

export function setDiscount(discountObj = null) {
	return {
		amount: discountObj === null ? 0 : discountObj.amount,
		type: discountObj === null ? discountTypePercentId : discountObj.type, // '1' = percent, '2' = shekel
		value: discountObj === null ? 0 : discountObj.value,
	};
}

export function setItemDataModal(modalEl, qtyNew = null, isReturn = false) {
	const detailsEl = modalEl.querySelector('.js--cart-detail-content');
	if (detailsEl === null) {
		return false;
	}

	// const itemId = detailsEl.getAttribute('data-item-id');
	const itemCurrentCartId = detailsEl.getAttribute('data-item-current-cart-id');
	// const itemType = detailsEl.getAttribute('data-item-type');

	const dValueEl = modalEl.querySelector('[data-discount-value]');
	let priceEl = modalEl.querySelector('[data-price-original]') && parseFloat(modalEl.querySelector('[data-price-original]').getAttribute('data-price-original'));
	if (isNaN(priceEl)) {
		priceEl = 0;
	}

	const saveCartItemEl = document.getElementById('saveCartItem');
	let item = getItemFromCart(itemCurrentCartId,'itemCurrentCartId');
	// console.log('[setItemDataModal]', item, saveCartItemEl);

	// if we don't have this item already added to cart
	if (!item && saveCartItemEl) {
		// get main item info from the data attribute
		const primaryJson = saveCartItemEl.getAttribute('data-primary-json');
		item = JSON.parse(primaryJson);
		// get additional item info from the data attribute
		const additionalJson = saveCartItemEl.getAttribute('data-additional-json');
		if (additionalJson) {
			item.additionalData = additionalJson;
		}
	}

	const qty = qtyNew !== null ? qtyNew : item.quantity;
	const price = item.price ? item.price : priceEl;
	const originalPrice = price * qty;

	let discountObj = null;
	const dTypeEl = modalEl.querySelector('.js--discount-btn.active');
	const discountType = dTypeEl && dTypeEl.getAttribute('data-type') !== null ? dTypeEl.getAttribute('data-type') : item.discount.type;
	const discountValue = dTypeEl && dValueEl.value !== null ? parseFloat(dValueEl.value) : item.discount.value;
	if (dTypeEl && discountValue > 0) {
		discountObj = calculateDiscountAmount(originalPrice, discountType, discountValue);
	}

	item.discount = setDiscount(discountObj);
	item.price = price;
	item.quantity = qty;
	item.originalPrice = originalPrice;
	item.totalPrice = discountObj && discountObj.value > 0 ? discountObj.totalPrice : originalPrice;

	// console.log('[setItemDataModal]', item, discountObj);

	if (isReturn) {
		return item;
	}

	buildPricesBox(item);
}

export function setDebtItems(debts) {
	// add debt items to the cartObject
	debts.forEach(obj => {
		const item = buildItem({
			...obj,
			type: 'debt',
			action: 'addItemCart'
		});
		cartObject.items.push(item);
	});

	buildSummaryItems();
}

export function removeDebtItems() {
	if (cartObject.items.length === 0) {
		return false;
	}
	cartObject.items = cartObject.items.filter(function( obj ) {
		return obj.type !== 'debt';
	});

	buildSummaryItems();
}

export function sendAllCartItems(props = {}) {

	let {
		reason = '',
		action = 'saveCartItems',
		url = cartControllerUrl,
		remarksText = '',
		modal = null
	} = props;
	if (modal) {
		closeModal(modal);
	}
	let data = {};

	if (getCheckOrderId()) {
		data.checkOrderId = getCheckOrderId();
	}
	if (remarksText !== '') {
		data.remarksText = remarksText;
	}
	const isCheckoutPage = document.body.classList.contains('bsapp__checkout-page');
	const docId = getDocId();
	if (action === 'clearAllCartItems' && (docId === null || docId === undefined || docId <= 0)) {
		clearCart('cart_deleted_successfully', false);
		return false;
	}
	if(action === 'refundDocs') {
		data.docId = docId;
		url = refundControllerUrl;
	} else if (action === 'docIdKeepInDebt' || action === 'clearAllCartItems') {
		data.clientId = cartObject.clientId;
		data.docId = docId;
		if(reason !== '') {
			data.reason = reason;
		}
	} else {
		data.clientId = cartObject.clientId
		data.items = cartObject.items
		if (cartObject.discount && cartObject.discount.amount !== 0) {
			data.discount = cartObject.discount;
		}
		if (options.helpData.client && options.helpData.client.isNew) {
			delete data.clientId;
		}
	}

	if (action === 'saveCartItems') {
		saveCartToSession(data);
		// got to checkout page
		setBodyPreload(true);
		window.location.href = '/office/checkout.php' + (cartObject.clientId ? '?u=' + cartObject.clientId : '');
		return false;

	} else if (action === 'customerKeepInDebt' || action === 'docIdKeepInDebt' || action === 'refundDocs') {
		const checkoutTransactions = getCheckoutTransactions();
		if (checkoutTransactions !== null) {
			data.transactions = checkoutTransactions.map(el => {
				return withoutProperty(el, ['details', 'typeKey']);
			});
		}
	}

	if (action !== 'docIdKeepInDebt' && action !== 'refundDocs') {
		data.items = cartObject.items.map(getItemDataToSend);
		if (options.helpData.client.isNew) {
			data.clientDetails = options.helpData.client;
		}
	}

	sendFetch(
		url,
		{
			...data,
			...{ action }
	}).then(function(response) {
		if (!response.success) {
			// show error modal
			showErrorModal({
				error: response.message
			});
			return false;
		}
		if (action === 'customerKeepInDebt' || action === 'docIdKeepInDebt' || action === 'refundDocs') {
			// clearCart(isCheckoutPage ? null : 'cart_customer_success_in_debt');
			if (isCheckoutPage) {
				closeModal('bsappDropdown');
				window.sessionStorage.removeItem(cookieCartItemsToSaveName);
				// go to checkout confirmation step
				debugger;//todo
				showConfirmationStep({
					action,
					isRefundPage: isRefundPage(),
					client: response.client,
					business: response.business,
					invoice: response.invoice,
					receipts: response.receipts,
					textKey: action === 'docIdKeepInDebt' ? 'cart_produced_receipt_summary' :
						action === 'refundDocs' ? 'refund_receipt_successfully_created' :
						'cart_customer_success_in_debt'
				});
			} else {
				clearCart('cart_customer_success_in_debt');
			}

		} else if (action === 'clearAllCartItems') {
			clearCart('cart_deleted_successfully');
			if (isCheckoutPage) {
				setBodyPreload(true);
				window.location.href = '/office/cart.php';
			}
		}
	});
}

export function saveCartToSession(data = {}) {
	writeSession(
		cookieCartItemsToSaveName,
		{
			...Object.assign({}, cartObject, data),
			...{
				clientDetails: options.helpData.client ?? null
			}
		}
	);
}

export function clearCart(noteKey = null, clearClientObj = true) {
	closeModal('bsappModal');
	closeModal('bsappDropdown');

	if (clearClientObj) {
		clearClient();
	}
	cartObject.discount = setDiscount();

	if (document.body.classList.contains('bsapp__cart-page')) {
		cartObject.items.forEach(el => {
			if (onlyOneToAddList.includes(el.type) && el.type !== 'lesson') {
				disabledCategoryItem(el);
			}
		});
	}

	cartObject.items = [];
	buildSummaryItems(noteKey);
}

export function buildDiscountBox() {
	const modalDialog = bsappModalEl.querySelector('.modal-dialog');
	const detailsEl = modalDialog.querySelector('.js--cart-detail-content');
	const discountObj = getDiscount(modalDialog);
	const discountHasFocus = modalDialog.querySelector('[data-discount-value]') === document.activeElement;

	buildPricesBox({
		type: detailsEl && detailsEl.getAttribute('data-item-type'),
		discount: discountObj,
		originalPrice: discountObj.originalPrice,
		totalPrice: discountObj.totalPrice
	});
	if (discountHasFocus) {
		modalDialog.querySelector('[data-discount-value]').focus();
	}
}

export function buildPricesBox(data) {
	console.log('[buildPricesBox] data', data);

	const discountObj = data.discount;
	const detailsType = data.type;
	const modalDialog = bsappModalEl.querySelector('.modal-dialog');
	const originalPriceEl = modalDialog.querySelector('[data-price-original]');
	const isGeneralCartDiscount = detailsType === null;
	const originalPrice = discountObj.originalPrice ? discountObj.originalPrice : data.originalPrice;
	const totalPrice = discountObj.totalPrice ? discountObj.totalPrice : data.totalPrice;

	if (modalDialog.querySelector('.js--cart-box-discount') !== null) {
		modalDialog.querySelector('.js--cart-box-discount').innerHTML = discountBox({
			type: detailsType,
			discount: discountObj
		});
	}

	if (originalPrice && originalPriceEl && parseFloat(originalPriceEl.getAttribute('data-price-original')) !== originalPrice) {
		originalPriceEl.setAttribute('data-price-original', originalPrice);
	}
	if (originalPriceEl) {
		originalPriceEl.innerHTML = totalPriceBox({
			text: isGeneralCartDiscount ? Lang('total_discount') : null,
			price: isGeneralCartDiscount ? discountObj.amount : totalPrice
		});
	}
}

export function setCartId(itemId, variantId = null, itemDiscountType = null, itemDiscountValue = null) {
	return `${itemId}${variantId ? '_' + variantId : ''}${itemDiscountType ? '_' + itemDiscountType.toString() : ''}${itemDiscountValue ? '_' + itemDiscountValue.toString() : ''}`;
}

export function buildItem(item, changeCurrentCartId = false) {
	let {
		type, id, variantId, quantity = 1, quantityMax, price, discount, additionalData
	} = item;

	const count = cartObject.items.length;
	const qtyNum = typeof quantity === 'string' ? parseInt(quantity) : quantity;
	const priceNum = parseFloat(price);
	const originalPrice = qtyNum * priceNum;
	const dataDiscount = {
		type: (discount && discount.type) || "1",
		value: (discount && discount.value) || 0,
		amount: (discount && discount.amount) || 0
	};
	const discountObj = calculateDiscountAmount(originalPrice, dataDiscount.type, dataDiscount.value);

	let itemCurrentCartId;
	if (!changeCurrentCartId && item.itemCurrentCartId) {
		itemCurrentCartId = item.itemCurrentCartId;

	} else if (type === 'general') {
		const countGeneral = count > 0 ? cartObject.items.reduce(function (accumulator, el) {
			return el.type === 'general' ? accumulator + 1 : accumulator;
		}, 0) : 0;
		id = setCartId(type, countGeneral.toString());
		itemCurrentCartId = setCartId(type, countGeneral.toString(), discountObj.type, discountObj.value.toString());

	} else if (type === 'debt') {
		itemCurrentCartId = setCartId(id, type, discountObj.type, discountObj.value.toString());

	} else {
		if (type === 'service' && item.date !== null && item.time !== null) {
			variantId = item.date + '_' + item.time;
		}
		itemCurrentCartId = setCartId(id, variantId, discountObj.type, discountObj.value.toString());
	}

	const data = {
		...item,
		id,
		itemCurrentCartId,
		price: priceNum,
		quantity: qtyNum,
		originalPrice: originalPrice,
		totalPrice: discountObj.value > 0 ? discountObj.totalPrice : originalPrice,
		discount: setDiscount(discountObj)
	};

	if (additionalData) {
		data.additionalData = additionalData;
	}
	if (quantityMax) {
		data.quantityMax = quantityMax;
	}

	if (type === 'package' && data.durationNum && data.durationNum !== '0' && data.durationType) {
		if (data.membershipStartCount && parseInt(data.membershipStartCount) !== membershipStartCountId.fromDate) {
			if (data.packageManualStart) {delete data.packageManualStart}
			if (data.packageManualEnd) {delete data.packageManualEnd}
		}
	}

	return data;
}

export function buildCartItem(item) {
	console.log('[buildCartItem]', item);

	if (item.action === 'deleteItemCart') {
		// delete item
		cartObject.items = removeItem(cartObject.items, 'itemCurrentCartId', item.itemCurrentCartId);
		buildSummaryItems();
		return false;
	}

	let noteKey = null;
	let itemToAdd = buildItem(item);

	if (item.action === 'addItemCart' && itemToAdd.type === 'service' && cartObject.items.length > 0) {
		const itemExistsIndex = getCartIndex('id', itemToAdd.id);
		if (itemExistsIndex !== -1) {
			const getLastServiceTime = getLastService(itemToAdd.id, itemToAdd.date);
			if (getLastServiceTime !== null) {
				const itemStartDateMinusDuration = getDateTimeMomentSubtractMin(
					itemToAdd.date,
					getLastServiceTime !== null ? getLastServiceTime : itemToAdd.time,
					itemToAdd.durationMin);
				itemToAdd.time = itemStartDateMinusDuration.time;
				itemToAdd.date = itemStartDateMinusDuration.date;

				itemToAdd = buildItem(itemToAdd, true);
				noteKey = prependItemToCart(itemToAdd);
				buildSummaryItems(noteKey);
				return false;
			}
		}
	}

	// if item exists in the cart depends on itemCurrentCartId ([id]_[variantId]_[discountType]_[discountValue])
	const itemExistsIndex = getCartIndex('itemCurrentCartId', itemToAdd.itemCurrentCartId);
	// console.log('[buildCartItem] itemToAdd', itemExistsIndex, itemToAdd);

	if (itemToAdd.itemCurrentCartId && itemExistsIndex !== -1) {
		const itemInCart = getItemFromCart(itemToAdd.itemCurrentCartId, 'itemCurrentCartId');
		if (itemInCart !== undefined) {
			if (item.action === 'addItemCart') {
				// if current item exists in the cart with discount -> change quantity and rebuild item
				itemToAdd.quantity = itemToAdd.quantity + itemInCart.quantity;
				itemToAdd.totalPrice = null;

		} else if (item.action === 'editItemCart') {
			const newCurrentCartId = setCartId(item.id, item.variantId, item.discount.type, item.discount.value.toString());
			if (newCurrentCartId !== item.itemCurrentCartId) {
				itemToAdd.itemCurrentCartId = null;
			}
		}
		cartObject.items[itemExistsIndex] = buildItem(itemToAdd);

	} else {
		cartObject.items[itemExistsIndex] = itemToAdd;
	}

	} else {
		// item doesn't exist in the cart, so create a new one
		noteKey = prependItemToCart(itemToAdd);
	}

	buildSummaryItems(noteKey);
}

function prependItemToCart(itemToAdd) {
	// append to cart items
	// cartObject.items.push(itemToAdd.action ? withoutProperty(itemToAdd, ['action']) : itemToAdd);
	// cartObject.items.splice(0, 0, itemToAdd.action ? withoutProperty(itemToAdd, ['action']) : itemToAdd);

	// prepend items to the basket, because the client's debt positions may already be in the cart
	cartObject.items.unshift(itemToAdd.action ? withoutProperty(itemToAdd, ['action']) : itemToAdd);
	return isMobile.matches ? 'cart_added_successfully' : null;
}

function setModalTitleByType(type, isAdded) {
	let typeName;
	switch (type) {
		case 'service':
			typeName = Lang('treatment');
			break;
		case 'lesson':
			typeName = Lang(isAdded ? 'class_single' : 'select_class_single');
			break;
		case 'general':
			typeName = Lang('general_item');
			break;
		case 'package':
			typeName = Lang('club_membership_smart_link');
			break;
		default:
			typeName = Lang(type);
	}

	return `${Lang('meeting_options')} ${typeName ? typeName : ''}`;
}

function setServiceData(props) {
	const {list, selectedId, type} = props;
	const newData = {type};
	newData.selected = selectedId ? list.find(v => v.id.toString() === selectedId.toString()) : list[0];
	if (list.length > 1) {
		newData.list = list;
	}
	const additionalData = getServiceStaticData(type);
	return {...newData, ...additionalData};
}

function getServiceStaticData(type = 'coaches') {
	switch (type) {
		case 'places':
			return {
				boxTitleKey: 'cart_diary_selection',
				labelKey: 'the_calendar',
				iconClass: 'fa-light fa-calendar s-24',
				classes: 'cart--service-diary-box'
			};
		default:
			return {
				boxTitleKey: 'cart_choosing_therapist',
				labelKey: 'therapist_single',
				iconClass: 'fa-solid fa-circle-user s-32',
				classes: 'cart--service-coaches-box'
			};
	}
}

export function buildItemModal(item, clickTarget = null) {
	const {
		type, itemCurrentCartId, variantId, variants, options, quantity, quantityMax,
		price, totalPrice, discount
	} = item;

	// show warning quantity text (availability of products in stock is not limited)
	if (quantityMax >= 0 && quantityMax !== '' && quantity > parseInt(quantityMax)) {
		item.quantityErrorText = Lang('cart_note_max_in_stock');
	}

	// build product options
	if (type === 'product' && options && options.length > 0 && variants && variants.length > 0) {
		// create product item custom dropdown (colors, sizes)
		let selectedItemVariant = null;
		if (itemCurrentCartId && variantId) {
			selectedItemVariant = getFilterByValue(variants, 'id', variantId);
			if (selectedItemVariant.length === 0) {
				new Error(`There is no variantId (${variantId}) in the current item variants`);
			}
			selectedItemVariant = selectedItemVariant[0];
		}

		// create customDropdown data for item options
		options.forEach((option, index) => {
			const position = index + 1;
			const selectedOptionInCart = selectedItemVariant && selectedItemVariant[`option${position}`];

			option.position = position;
			option.className = 'bsapp--custom-select_' + option.type;
			option.selectId = option.type === 'colors' ? colorsSelectId : sizesSelectId;
			option.createSelect = true;
			const optionValues = getOptionValues(option.position, variants, options.length, selectedItemVariant);
			option.items = optionValues;

			// console.log(option.type, optionValues);

			if (optionValues.length === 1) {
				option.selectedOptionId = optionValues[0].id;

			} else if (selectedItemVariant) {
				const selectedOption = getFilterByValue(optionValues, 'id', selectedOptionInCart);
				if (selectedOption.length === 0) {
					new Error(`There is no option (${selectedItemVariant[`option${position}`]}) in the ${option.type} option`);
				}
				option.selectedOptionId = selectedOption[0].id;
			}
		});

	} else if (type === 'package' && item.durationNum && item.durationNum !== '0' && item.durationType) {
		const start = item.packageManualStart ? item.packageManualStart : new Date().toISOString().slice(0,10);
		item.packageManualStart = start;
		item.packageManualEnd = item.packageManualEnd ? item.packageManualEnd : setDefaultDuration(start, item.durationNum, item.durationType);

		item.duration = setDurationStr(item.durationNum, item.durationType);
		// create package type custom dropdown
		item.packageType = {
			label: Lang('cart_validity_calculation'),
			className: 'bsapp--custom-select_' + type,
			type: 'packageValidity',
			selectId: 'membershipStartCount',
			createSelect: true,
			selectedOptionId: item.membershipStartCount ? item.membershipStartCount : membershipStartCountId.fromPurchase,
			items: [
				{
					id: membershipStartCountId.fromPurchase,
					text: Lang('from_date_of_purchase')
				},
				{
					id: membershipStartCountId.fromDate,
					text: Lang('cart_manual')
				},
				{
					id: membershipStartCountId.fromLesson,
					text: Lang('cart_first_lesson')
				}
			]
		};

	} else if (type === 'service' && item.additionalData) {
		const additionalData = JSON.parse(item.additionalData);
		if (additionalData.places) {
			item.placesData = setServiceData({
				list: additionalData.places,
				selectedId: item.diaryId,
				type: 'places'
			});
		}
		if (additionalData.coaches) {
			item.coachesData = setServiceData({
				list: additionalData.coaches,
				selectedId: item.coachId,
				type: 'coaches'
			});
		}
	}

	// build data for ../handlebars-partials/cart/itemEditModal.hbs
	const origPrice = price * quantity;
	const dataCompile = {
		item,
		modalTitle: setModalTitleByType(type, itemCurrentCartId),
		itemNameLabel: Lang('item_name'),
		showQuantity: type === 'general' || type === 'product',
		showDiscount: price > 0,
		subtotalPrice: {
			price: totalPrice >= 0 ? totalPrice : (discount && discount.value >= 0 ? discount.totalPrice : origPrice),
			originalPrice: origPrice
		},
		deleteIcon: {
			itemCurrentCartId
		}
	};

	console.log('[buildItemModal] dataCompile', dataCompile);

	buildModal({
		el: bsappModalEl,
		html: itemEditModal(dataCompile)
	}).then(function () {
		setBodyPreload(false);
		openModal({modalEl: 'bsappModal'}).then(function () {
			if (type === 'lesson') {
				closeModal('bsappLessonItemModal');
			}
		});

		if (clickTarget) {
			removeClass(clickTarget, 'disabled');
		}

		// if (type === 'service') {
		// 	createServiceCalendar({
		// 		date: item.date,
		// 		time: item.time,
		// 		durationMin: item.durationMin
		// 	});
		// }
	});
}

function getDataTimeArray(minHours = '00:00') {
	const timeBlocksArr = [];
	const minHourSplit = minHours.split(':');
	const minHour = +(minHourSplit[0]);
	let minMinute = +(minHourSplit[1]);
	minMinute = minMinute < 5 ? minMinute : minMinute / 5;

	// return times options depends on start hour
	for (let i = minHour; i < 24; i++) {
		for (let j = 0; j < 60 / 5; j++) {
			if (minHours !== '00:00' && i === minHour && j <= minMinute) {
				continue;
			}
			const hour = i < 10 ? ('0' + i) : i;
			const minute = (j * 5 < 10 && '0') + j * 5;
			timeBlocksArr.push(`${hour}:${j === 0 ? `00` : minute}`);
		}
	}

	return timeBlocksArr;
}

function getDataDurationArray(minMinute = 5) {
	minMinute = minMinute < 5 ? minMinute : minMinute / 5;
	const arr = [];
	// return times options depends on start hour
	for (let j = 0; j <= 600 / 5; j++) {
		const min = j * 5;
		if (j < minMinute || j > 24 && min % 15 !== 0 || j > 84 && min % 60 !== 0) {
			continue;
		}
		arr.push(min);
	}

	return arr;
}

export function createServiceCalendar(props) {
	const {date, time, durationMin} = props;
	// create FullCalendar for service date selection
	if (date) {
		FullCalendar({
			el: document.getElementById('calendarServiceContent'),
			_setInitialDate: function (date) {
				const saveBtn = document.getElementById('saveServiceDateTime');
				if (saveBtn && date) {
					saveBtn.setAttribute('data-service-date', date.toISOString().split('T')[0]);
				}
			},
			options: {
				initialDate: new Date(date) || new Date(),
				select: null
			}
		});
	}
	// create service start time dropdown
	if (time) {
		const dataArr = [];
		const timesArr = getDataTimeArray();
		timesArr.forEach(el => {
			dataArr.push({
				id: el,
				text: el
			});
		});

		CustomDropdown({
			el: document.getElementById('timeServiceContent'),
			options: {
				labelKey: 'hour',
				type: 'serviceTime',
				createSelect: true,
				selectedOptionId: time,
				items: dataArr
			}
		});
	}
	// create service duration dropdown
	if (durationMin) {
		const dataArr = [];
		const timesArr = getDataDurationArray();
		timesArr.forEach(el => {
			dataArr.push({
				id: el,
				text: setDurationString(el, 'minutes')
			});
		});

		CustomDropdown({
			el: document.getElementById('durationServiceContent'),
			options: {
				labelKey: 'duration',
				type: 'serviceDuration',
				createSelect: true,
				selectedOptionId: durationMin,
				items: dataArr
			}
		});
	}
}

function setDurationStr(num, type) {
	if (!num || !type) {
		return null;
	}

	let translation;
	const numInt = parseInt(num);
	const typeStr = getDurationType(type);
	if ((numInt === 1 && typeStr === 'year') || (numInt === 12 && typeStr === 'month')) {
		translation = Lang('year_js');
	} else if (typeStr === 'month' && numInt > 0 && (numInt % 12) === 0) {
		translation = (numInt / 12) + ' ' + Lang('years');
	} else {
		translation = num + ' ' + Lang(typeStr + (numInt > 1 ? 's' : ''));
	}

	return translation;
}

export function buildSingleItemByType(item, categoryType) {
	const {id, name, quantity, price, variants, inventory} = item;

	// primary item data
	let primaryData = {
		...item,
		type: categoryType,
		quantity: quantity ? quantity : 1,
		discount: calculateDiscountAmount(price)
	};
	const additionalData = {};

	if (categoryType === 'product') {
		const primaryMax = inventory ? inventory : variants[0].inventory;
		primaryData.quantityMax = primaryMax > 0 ? primaryMax : 0;

	} else if (categoryType === 'package') {
		if (item.durationNum && parseInt(item.durationNum) !== 0 && item.durationType) {
			const start = item.packageManualStart ? item.packageManualStart : new Date().toISOString().slice(0,10);
			primaryData.packageManualStart = start;
			primaryData.packageManualEnd = setDefaultDuration(start, item.durationNum, item.durationType);
		}

	} else if (categoryType === 'service') {
		if (options.helpData.servicePlaces) {
			additionalData.places = options.helpData.servicePlaces;
		}
		if (options.helpData.serviceCoaches) {
			additionalData.coaches = options.helpData.serviceCoaches;
		}
	}

	const mainData = {
		...primaryData,
		primaryData: JSON.stringify(primaryData)
	};
	if (Object.keys(additionalData).length > 0) {
		mainData.additionalData = JSON.stringify(additionalData);
	}

	return mainData;
}

function getOptionValues(position, variants, optionsLength, selectedVariant = null) {
	const item = [];
	const otherPosition = optionsLength === 1 ? null : (position === 1 ? 2 : 1);

	variants.forEach(function (el) {
		const name = el[`option${position}`];
		if (item.length > 0 && getIndex(item, name, 'id') > -1) {
			return;
		}

		const splitName = name.split('__');
		const param = {
			id: name,
			text: splitName[0],
			// disabled: el.inventory <= 0
			disabled: false
		};
		if (splitName.length > 1) {
			param.color = splitName[1].toLowerCase();
			if (!param.disabled) {
				param.disabled = selectedVariant !== null && otherPosition !== null && el[`option${otherPosition}`].toString() !== selectedVariant[`option${otherPosition}`].toString();
			}
		}
		item.push(param);
	});

	return item.length > 0 ? item : null;
}

export function deleteCartItem(itemCurrentCartId = null, modalId) {
	const pos = getCartIndex('itemCurrentCartId', itemCurrentCartId);
	if (pos === -1 || itemCurrentCartId === null) {
		return false;
	}

	const itemToDelete = getItemFromCart(itemCurrentCartId, 'itemCurrentCartId');
	itemToDelete.action = 'deleteItemCart';
	sendCartItem(itemToDelete, modalId);
}

export function removeLessonWithClient() {
	const clientId = cartObject.clientId;
	if (!clientId || cartObject.items.length === 0) {
		return false;
	}

	let showModal = false;
	cartObject.items = cartObject.items.filter(el => {
		if (el.type === 'lesson' && el.clientIds && el.clientIds.includes(clientId)) {
			if (!showModal) {
				showModal = true;
			}
			return false;
		}

		return true;
	});

	buildSummaryItems();

	if (showModal) {
		showClientInLessonModal();
	}
}

function cartFixedNote(showNoteDate) {
	let elemDiv = document.createElement('div');
	elemDiv.classList.add('aside--summary-note', 'js--aside-note');
	if (showNoteDate.class) {
		elemDiv.classList.add(showNoteDate.class);
	}
	let elemSpan = document.createElement('span');
	elemSpan.textContent = Lang(showNoteDate.noteTextKey);
	elemDiv.appendChild(elemSpan);
	document.body.appendChild(elemDiv);
	setTimeout(function () {
		elemDiv.classList.add('show');
	}, 200);
}

export function buildSummaryItems(showNoteKey = null) {
	cartObject.itemCount = cartObject.items.length || 0;

	if (cartObject.itemCount > 0) {
		const vatAmount = cartObject.vatAmount || 0;
		const originalPrice = getCartOriginalPrice();
		let subtotalPrice = originalPrice;
		let totalPriceMinusVat = originalPrice;
		let totalPrice = originalPrice;

		const discountValue = cartObject.discount && cartObject.discount.value > 0 ? cartObject.discount.value : 0;
		if (discountValue !== 0) {
			const discountObj = calculateDiscountAmount(originalPrice, cartObject.discount.type, cartObject.discount.value);
			cartObject.discount = setDiscount(discountObj);
			subtotalPrice = getDiscountedPrice(originalPrice, cartObject.discount.amount);
			totalPriceMinusVat = subtotalPrice;
			totalPrice = subtotalPrice;
		}

		if (vatAmount !== 0) {
			// const percentPrices = getPercentPrice(subtotalPrice, vatAmount);

			// const percentPrices = getPercentPrice(subtotalPrice, vatAmount, false);
			// cartObject.vatPrice = percentPrices.percentAmount;
			// totalPriceMinusVat = percentPrices.price;

			const subtotalPriceCent = subtotalPrice * 100;
			const percentCents = parseFloat(Math.round(Math.abs(subtotalPriceCent / ((+(vatAmount) + 100) * 0.01))).toFixed());
			cartObject.vatPrice = (subtotalPriceCent - percentCents) / 100;
			totalPriceMinusVat = percentCents / 100;
			totalPrice = subtotalPrice;
		}

		cartObject.originalPrice = originalPrice;
		cartObject.subtotalPrice = subtotalPrice;
		cartObject.totalPriceMinusVat = totalPriceMinusVat;
		cartObject.totalPrice = totalPrice;

	} else {
		cartObject.discount = setDiscount();
		cartObject.originalPrice = 0;
		cartObject.subtotalPrice = 0;
		cartObject.totalPriceMinusVat = 0;
		cartObject.totalPrice = 0;
		cartObject.vatPrice = 0;
	}

	const cart = cartObject;
	const asideSummaryEl = document.getElementById('asideSummary');

	if (showNoteKey !== null) {
		const showNoteDate = {
			class: showNoteKey == 'cart_customer_success_in_debt' ? 'in-green' : null,
			noteTextKey: showNoteKey,
		}
		if (isMobile.matches) {
			cartFixedNote(showNoteDate);
		} else {
			cart.showNote = showNoteDate;
		}

		// hide the note text after 3 sec and then remove from DOM
		setTimeout(function () {
			const noteElms = document.querySelectorAll('.js--aside-note');
			if (noteElms.length > 0) {
				noteElms.forEach(note => {
					note.classList.remove('show');
					setTimeout(function () {
						note.parentNode.removeChild(note);
					}, 200);
				});
			}
		}, 3000);

	} else if (cart.showNote) {
		cart.showNote = null;
	}

	console.log('[buildSummaryItems] cart', cart, showNoteKey);

	if (asideSummaryEl) {
		asideSummaryEl.innerHTML = asideSummary({
			...cart,
			...{
				openDiscountModal: cart.items.length > 0 && (cart.totalPrice > 0 || (cart.discount && cart.discount.amount > 0)),
				getCartClientIdS: getCartClientId(),
			}
		});
	}

	if (bsappBarEl === null) {
		return false;
	}
	const summaryAsideEl = document.getElementById('summaryAside');
	if (cart.itemCount === 0) {
		if (summaryAsideEl && !summaryAsideEl.classList.contains('show')) {
			removeClass(bsappBarEl, 'not-visible');
		}
		removeClass(bsappBarEl, 'is-visible');
		removeClass(mainEl, 'bar-visible');

	} else {
		if (summaryAsideEl && !summaryAsideEl.classList.contains('show')) {
			buildModal({
				el: bsappBarEl,
				html: barSummary(cart)
			}).then(function () {
				// if (!bsappBarEl.classList.contains('not-visible')) {
					removeClass(bsappBarEl, 'not-visible');
					addClass(bsappBarEl, 'is-visible');
					addClass(mainEl, 'bar-visible');
				// }
			});
		}
	}
}

export function getItemDataToSend(toSendItem) {
	// specify the item only with the required parameter for the request
	const {type, id, name, price, totalPrice, quantity} = toSendItem;
	const newItem = {
		type,
		id,
		name,
		price,
		quantity
	}
	if (toSendItem.discount && toSendItem.discount.amount !== 0) {
		newItem.discount = toSendItem.discount;
	}

	if (type === 'general' && newItem.id) {
		delete newItem.id;

	} else if (type === 'product' && toSendItem.variantId) {
		newItem.variantId = toSendItem.variantId;

	} else if (type === 'lesson' && toSendItem.lessonClientAssign) {
		newItem.lessonClientAssign = toSendItem.lessonClientAssign;

	} else if (type === 'package' && toSendItem.membershipStartCount) {
		newItem.membershipStartCount = toSendItem.membershipStartCount;
		if (toSendItem.packageManualStart && toSendItem.packageManualEnd) {
			newItem.packageManualStart = toSendItem.packageManualStart;
			newItem.packageManualEnd = toSendItem.packageManualEnd;
		}

	} else if (type === 'service') {
		if (toSendItem.diaryId) { newItem.diaryId = toSendItem.diaryId; }
		if (toSendItem.coachId) { newItem.coachId = toSendItem.coachId; }
		if (toSendItem.durationMin) { newItem.durationMin = toSendItem.durationMin; }
		if (toSendItem.date) { newItem.date = toSendItem.date; }
		if (toSendItem.time) { newItem.time = toSendItem.time; }
	}

	return newItem;
}

export function sendCartItem(toSendItem, modalId = null) {
	if (!toSendItem.hasOwnProperty('type')) {
		new Error('[sendCartItem] There is no cart item type');
	}

	if (!toSendItem.action) {
		toSendItem.action = "addItemCart";
	}

	if (modalId !== null) {
		closeModal(modalId);
	}

	// disabled package category item box from page
	if (onlyOneToAddList.includes(toSendItem.type)
		&& (toSendItem.action === "addItemCart" || toSendItem.action === "deleteItemCart")) {
		disabledCategoryItem(toSendItem, toSendItem.action === "addItemCart");
	}

	buildCartItem(toSendItem);
}

export function calculateDiscountAmount(fromPrice = 0, type = discountTypePercentId, value = 0) {
	let price = 0, amount = 0;
	if (type === discountTypePercentId) {
		if (parseInt(value) > 0) {
			const percentPrices = getPercentPrice(fromPrice, value, false);
			amount = percentPrices.percentAmount;
			price = percentPrices.price;
		}
	} else {
		price = value > fromPrice ? 0 : fromPrice - value;
		amount = value > fromPrice ? fromPrice : value;
		value = value > fromPrice ? fromPrice : value;
	}

	return {
		amount,
		type,
		value,
		totalPrice: value > 0 ? price : fromPrice,
		originalPrice: fromPrice,
	};
}