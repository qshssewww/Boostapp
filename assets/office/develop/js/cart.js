import "@/scss/main.scss";

import mainDropdownModal from "@partials/mainDropdownModal";

import CustomDropdown from "@modules/CustomDropdown";
import {setStorageLang} from "@modules/SetStorageLang";
import {
	sendFetch, addClass, removeClass, additionalResizeEvents, showErrorModal,
	getSearchParam, setBodyPreload, readSession
} from "@modules/Helpers";
import {initModal, buildModal, openModal, closeModal} from "@modules/Modal";
import {initCartAside} from "@modules/CartAsideBlock";
import {
	buildItemModal, buildSingleItemByType, sendCartItem, setCartObject, setDebtItems
} from "@modules/cart/CartHelpers";
import {
	init as initNavCategoriesEvents, buildAllCategoriesNav, getItemFromCategory, disabledCategoryItem
} from "@modules/cart/NavCategories";
import {saveClientToCart, checkClient} from "@modules/cart/AsideClient";
import {getItemDetails} from "@modules/cart/AsideSummary";
import {init as initItemGeneralModal} from "@modules/cart/ItemGeneralModal";
import {initSearchModal, recordRecentlyViewed} from "@modules/cart/SearchModal";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
import {openCheckOutOrderPopup} from "@modules/checkout/CheckoutHelpers";
const {
	cartNavCategoriesEl, options, isMobile, globalUrl, bsappDropdownEl, cartControllerUrl,
	cookieCartItemsToSaveName, onlyOneToAddList
} = cartGlobalVariable;

// Private variables
const activeClass = 'active';
const subLevelEl = document.querySelector('.bsapp--tab-content');

async function cartPage() {
	initEvent();
	initModal();
	initNavCategoriesEvents();
	// initialize general item modal
	if (document.getElementById('bsappItemGeneralModal')) {
		initItemGeneralModal();
	}
	// initialize only a customDropdown events
	CustomDropdown({});
	await initialRequest();

	// const cart = readJsCookie(cookieCartItemsToSaveName);
	const cart = readSession(cookieCartItemsToSaveName);
	console.log('[cartPage] cart', cart);
	// if (cart && document.referrer.indexOf('checkout.php') > -1) {
	if (cart) {
		debugger;
		if(cart.clientId) {
			const response1 = await checkClient({
				action: 'saveCartUser',
				id: cart.clientId
			});
		}
		setCartObject(cart);
		if (cart.items.length > 0) {
			cart.items.forEach(el => {
				if (onlyOneToAddList.includes(el.type) && el.type !== 'lesson') {
					disabledCategoryItem(el, true);
				}
			});
		}
		if (cart.clientId) {
			saveClientToCart(cart.clientDetails);
		}
		window.sessionStorage.removeItem(cookieCartItemsToSaveName);
	}

	// update cart drawer after initial page post
	initCartAside();
}

function initEvent() {
	additionalResizeEvents();
	// media query event handler
	if (matchMedia) {
		const mq = window.matchMedia(isMobile.mediaQuery);
		mq.addEventListener('change', widthChange);
		widthChange(mq);
	}

	document.getElementById('openSearchModal').addEventListener('click', e => {
		e.preventDefault();
		const el = e.target.classList.contains('btn') ? e.target : e.target.closest('.btn');
		initSearchModal();
	});

	document.getElementById('openDocsDropdown').addEventListener('click', e => {
		e.preventDefault();
		const el = e.target.classList.contains('btn') ? e.target : e.target.closest('.btn');
		buildModal({
			el: bsappDropdownEl,
			html: mainDropdownModal({
				width: '222',
				top: '13',
				list: [
					{
						textKey: 'cart_all_docs',
						icon: 'fa-light fa-file-magnifying-glass',
						type: 'link',
						href: '/office/CartesetAll.php',
						target: '_blank'
					},
					{
						textKey: 'debit',
						icon: 'fa-light fa-wallet',
						class: 'red js--cart-debit',
						type: 'link',
						href: '/office/Reports/Debt.php',
						target: '_blank'
					}
				]
			})
		}).then(function () {
			openModal({modalEl: 'bsappDropdown', target: el});
		});
	});

	document.addEventListener('click', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches('.js--add-to-cart')) {
				e.preventDefault();
				e.stopImmediatePropagation();
				addItemFromCategoryToCart.call(this, target, e);
				break;
			}
			else if (target.matches('#js-redirect-close-order')) {
				e.preventDefault();
				debugger;
				const openOrderId = target.getAttribute('data-id');
				const openOrderRefund = target.getAttribute('data-is-refund');
				const url = openOrderRefund !== 'true' ? '/office/checkout.php?checkOrderId=' : '/office/refund.php?checkOrderId=';
				window.location.href = url + openOrderId;
			}
			else if (target.matches('#js-redirect-cart')) {
				e.preventDefault();
				debugger;
				window.location.href = `/office/cart.php`;
			}
		}
	}, false);
}

async function initialRequest() {
	// get the initial data
	let data = {
		action : 'getCartData'
	};
	const clientId = getSearchParam('u');
	if (clientId) {
		data.clientId = clientId
	}
	const clientActivity = getSearchParam('debt');
	if (clientActivity) {
		data.debtId = clientActivity
	}
	await sendFetch(
		cartControllerUrl,
		data
	).then(function (response) {
		if (!response.success) {
			showErrorModal({
				error: response.message
			});
			return false;
		}
		if(response.openOrderId !== undefined && response.openOrderId > 0) {
			openCheckOutOrderPopup(response.openOrderId, response.openOrderRefund ?? false);
			return false;
		}
		const {client, vatAmount, coaches, diaries, debts, businessType} = response;
		if (vatAmount || businessType) {
			setCartObject({
				vatAmount: vatAmount ? parseInt(vatAmount) : 0,
				businessType: businessType ? parseInt(businessType) : null
			})
		}
		if (coaches && coaches.length > 0) {
			options.helpData.serviceCoaches = coaches;
		}
		if (diaries && diaries.length > 0) {
			options.helpData.servicePlaces = diaries;
		}
		if (debts && debts.length > 0) {
			setDebtItems(debts);
		}
		saveClientToCart(client);
		buildAllCategoriesNav(response);
	});
}

function widthChange() {
	if (!isMobile.matches) {
		const activeEl = document.querySelector('.js--nav-link.active');
		if (activeEl) {
			const activeType = activeEl.getAttribute('href').replace('#', '');
			addClass(document.querySelector(`.cart--subcategories-desk[data-type="${activeType}"]`), activeClass);
		}
		if (subLevelEl.classList.contains(activeClass)) {
			removeClass(subLevelEl, activeClass);
			removeClass(cartNavCategoriesEl, 'd-none');
			removeClass(subLevelEl, 'slide-in');
			removeClass(subLevelEl, 'slide-out');
		}
	}
}

async function addItemFromCategoryToCart(target, e) {
	setBodyPreload(true);
	const targetModal = target.closest('.bsapp--modal');

	let {item, categoryType} = getItemFromCategory(target);
	if (!item) {
		throw new Error(`Current item not found in categories`);
	}

	if (targetModal && targetModal.getAttribute('id') === 'bsappSearchModal') {
		recordRecentlyViewed({
			id: item.id,
			type: categoryType
		});
	}

	if (categoryType === 'product') {
		const newItem = await getItemDetails({
			id: item.id,
			type: categoryType,
			url: cartControllerUrl
		});
		item = Object.assign({}, newItem, item);
	}

	// console.log('[addItemFromCategoryToCart]', item, categoryType);
	const {id, name, price, variants, options, inventory} = item;

	// open a details popup
	if ((categoryType === 'product' && variants && variants.length > 1 && options && options.length > 0)
		|| categoryType === 'package') {

		const mainData = buildSingleItemByType(item, categoryType);
		setBodyPreload(false);
		buildItemModal(mainData);
		return false;
	}

	let toSendItem = {
		type: categoryType,
		id,
		name,
		price,
		quantity: 1,
		quantityMax: inventory ? inventory : null
	};

	if (categoryType === 'product' && variants && variants.length === 1) {
		toSendItem.options = options;
		toSendItem.variants = variants;
		toSendItem.variantId = variants[0].id;
		toSendItem.quantityMax = variants[0].inventory !== null && variants[0].inventory > 0 ? variants[0].inventory : 0;

	} else if (categoryType === 'service') {
		const mainData = buildSingleItemByType(item, categoryType);
		toSendItem = {...toSendItem, ...mainData};
	}

	// send a post to save item in the cartObject
	sendCartItem(toSendItem);
	setBodyPreload(false);

	// if item was added from the modal, close it
	closeModal(targetModal);
}

setStorageLang().then(function() {
	cartPage();
});