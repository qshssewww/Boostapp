import "@/scss/_inputs.scss";
import moment from 'moment';

import quantityBox from "@partials/item/quantityBox";


import {Lang} from "@modules/Lang";
import {closeModal} from "@modules/Modal";
import {
	changeQty, addClass, removeClass, triggerErrorClass, setDateTimeMomentString, setSelectedSelect,
	slideUp, slideDown, showErrorModal, toggleClass, getDurationType, sendFetch, showClientInLessonModal,
	validateOnlyNumber
} from "@modules/Helpers";
import {
	buildDiscountBox, deleteCartItem, sendCartItem, setItemDataModal, getCartIndex,
	createServiceCalendar, getCartClientId
} from "@modules/cart/CartHelpers";
import {buildSearch} from "@modules/cart/SearchModal";

import {isRefundPage} from "@modules/checkout/CheckoutHelpers";


import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	globalUrl, bsappModalEl, discountTypePercentId, membershipStartCountId, cartControllerUrl
} = cartGlobalVariable;

let isInit = false;
const packageTypeName = 'membershipStartCount';
const manualStartName = 'packageManualStart';
const manualEndName = 'packageManualEnd';
const itemPriceName = 'itemPrice';
const serviceTimeAttrName = 'data-service-time';
const serviceDateAttrName = 'data-service-date';
const serviceDurationAttrName = 'data-service-duration-min';

export function additionalEvents() {
	if (isInit) {
		return false;
	}

	const inputClassEl = '.js--group-input--remove';
	const inputRemoveClassEl = '.js--group-icon--remove';
	const itemValidatePriceEl = '[data-validate-price]';
	const colorsSelectId = 'colorsOptionSelect';
	const sizesSelectId = 'sizesOptionSelect';

	document.addEventListener('click', async function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(inputRemoveClassEl)) {
				e.preventDefault();
				const prevEl = target.parentNode.querySelector(inputClassEl);
				if (prevEl) {
					target.classList.remove('active');
					prevEl.value = prevEl.getAttribute('type') === 'tel' && prevEl.getAttribute('name') !== 'phone' ? '0' : '';
					prevEl.focus();

					changeValueWithRemoveIcon(prevEl, inputRemoveClassEl);
					if (prevEl.getAttribute('data-discount-value') !== null) {
						buildDiscountBox();
					}
				}
				break;

			} else if (target.matches('.js--discount-btn')) {
				e.preventDefault();
				changeDiscountType.call(this, target, e);
				break;

			} else if (target.matches('[data-id="lessonClientAssign"]')) {
				e.preventDefault();
				saveCartItemEditModal(document.getElementById('saveCartItem'), target.getAttribute('data-type'));
				break;

			} else if (target.matches('#saveCartItem')) {
				e.preventDefault();
				const toContinue = await checkCartLesson(target);
				if (!toContinue) {
					return false;
				}

				saveCartItemEditModal.call(this, target);
				break;

			} else if (target.matches('#removeClientFromLesson')) {
				e.preventDefault();
				closeModal('bsappErrorModal');
				closeModal('bsappModal');
				break;

			} else if (target.matches('.js--delete-item')) {
				e.preventDefault();
				deleteCartItem(target.getAttribute('data-item-current-cart-id'), bsappModalEl);
				closeModal('bsappModal');
				break;

			} else if (target.matches('.js--qty-btn')) {
				e.preventDefault();
				changeItemQty.call(this, target, e);
				break;

			} else if (target.matches('.js--toggle-service-box')) {
				e.preventDefault();
				const elName = '.cart--service-box-slide';
				const isHeader = target.classList.contains('modal-header');
				const el = isHeader ? target.closest(elName) : target.parentNode.querySelector(elName);
				toggleServiceSlideBox(el, !isHeader ? target.parentNode : null);
				break;

			} else if (target.matches('#saveServiceDateTime')) {
				e.preventDefault();
				saveServiceDateTime.call(this, target, e);
				break;

			}
		}
	}, false);

	document.addEventListener('input', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(inputClassEl)) {
				e.preventDefault();
				changeValueWithRemoveIcon(target, inputRemoveClassEl);
				break;
			}
		}
	}, false);

	document.addEventListener('focus',function(e){
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(inputClassEl)) {
				e.preventDefault();
				if (target.value.length !== 0) {
					changeValueWithRemoveIcon(target, inputRemoveClassEl);
				}
				break;
			}
		}
	}, true);

	document.addEventListener('change', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(`[name="${itemPriceName}"]`)) {
				e.preventDefault();
				changeItemPrice.call(this, target, e);
				// validatePrice.call(this, target, e, itemValidatePriceEl);
				break;

			} else if (target.matches('[data-discount-value]')) {
				e.preventDefault();
				changeDiscountValue.call(this, target, e);
				break;

			} else if (target.matches(`[name="${sizesSelectId}"]`)) {
				e.preventDefault();
				changeOptionVariants.call(this, target, e);
				break;

			} else if (target.matches(`[name="${colorsSelectId}"]`)) {
				e.preventDefault();
				changeOptionVariants.call(this, target, e);
				break;

			} else if (target.matches(`[name="${manualStartName}"]`)) {
				e.preventDefault();
				changePackageStartDate.call(this, target, e);
				break;

			} else if (target.matches(`[name="${manualEndName}"]`)) {
				e.preventDefault();
				changePackageEndDate.call(this, target, e);
				break;

			} else if (target.matches('.js--service-change-data')) {
				e.preventDefault();
				serviceChangeData.call(this, target, e);
				break;

			} else if (target.matches(`[name="${packageTypeName}"]`)) {
				e.preventDefault();
				const selectedOption = target.querySelector('[selected="selected"]');
				if (parseInt(selectedOption.value) === membershipStartCountId.fromDate) {
					slideDown(document.querySelector('[data-show-for="cart_manual"]'));
				} else {
					slideUp(document.querySelector('[data-show-for="cart_manual"]'));
				}
				break;
			}
		}
	}, false);

	document.addEventListener('keydown', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(itemValidatePriceEl)) {
				if (!validateOnlyNumber(e, true)) {
					e.preventDefault();
					return false;
				}
				break;
			}
		}
	}, false);

	document.addEventListener('keyup', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(itemValidatePriceEl)) {
				let val = target.value;
				const maxTotalPrice = target.getAttribute('data-price-max');
				const maxDocsPaymentPrice = target.getAttribute('data-docs-payment-price-max');
				const charCode = (e.which) ? e.which : e.keyCode;

				// console.log('keyup', e.type, charCode, e);
				// console.log('keyup maxTotalPrice', val, maxTotalPrice, parseFloat(val) > parseFloat(maxTotalPrice));

				if (isNaN(val) || parseFloat(val) === 0) {
					e.preventDefault();
					addClass(target, 'error');
					return false;

				} else if (maxTotalPrice !== null && parseFloat(val) > parseFloat(maxTotalPrice)) {
					if(maxDocsPaymentPrice !== null) {
						target.value = (+Math.min(maxDocsPaymentPrice,maxTotalPrice)).toFixed(2);
					} else {
						setDisplayNumber(target, maxTotalPrice);
					}
					addClass(target, 'error');
					const errorText = isRefundPage() ? Lang('refund_can_not_type_more') : Lang('checkout_can_not_type_more');
					setError(target, errorText, true);
					return false;

				} else if (maxDocsPaymentPrice !== null && parseFloat(val) > parseFloat(maxDocsPaymentPrice)) {
					target.value = (+maxDocsPaymentPrice).toFixed(2);
					addClass(target, 'error');
					setError(target, Lang('can_refund_up_to_transaction_amount'), true);
					return false;

				} else if (!(charCode > 31 && (charCode < 48 || charCode > 57) && (charCode > 105 || charCode < 96))
					&& val.includes('.')) {
					const decimalDigits = val.split('.')[1];
					if (decimalDigits && decimalDigits.length > 2) {
						target.value = target.value.substring(0, target.value.length - 1);
					}
				} else {
					removeClass(target, 'error');
					setError(target);
				}

				changeItemPrice.call(this, target, e);
				break;
			}
		}
	}, false);

	if (!isInit) {
		isInit = true;
	}
}

export function validatePrice(target, e, itemValidatePriceEl) {
	// console.log('validatePrice', e.type, e.key, (e.which) ? e.which : e.keyCode);

	const key = e.key;
	const keyCode = (e.which) ? e.which : e.keyCode;
	if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) { //[0-9]
		e.preventDefault();
		appendNumber(key, target);
	} else if (keyCode === 110 || key === 190) { // '.'
		e.preventDefault();
		if (target.getAttribute('data-type') !== discountTypePercentId) {
			appendNumber(key, target);
		} else {
			return false;
		}
	} else if (keyCode === 13) { // Enter
		e.preventDefault();
		target.blur();
		updateDisplayNumber(target);
	} else if (keyCode === 8 || key === "Backspace") { // Backspace
		e.preventDefault();
		deleteNumber(target);
		updateDisplayNumber(target);
	} else if (keyCode === 46) { // Delete
		e.preventDefault();
		clearNumber(target);
		updateDisplayNumber(target);
	} else {
		e.preventDefault();
		return false;
	}
}

export function setError(target, errorText = '', add = false) {
	const errorEl = target.closest('.form--group-items').querySelector('.form--group-error');
	if (!errorEl) {
		return false;
	}
	if (add) {
		errorEl.innerHTML = errorText === '' ? errorText : `<span>${errorText}</span>`;
	} else {
		errorEl.innerHTML = '';
	}
}

function serviceChangeData(target, e) {
	const type = target.getAttribute('data-type');
	const id = target.getAttribute('data-id');
	const name = target.getAttribute('data-name');
	const typeEl = target.closest(`[data-service-type="${type}"]`);
	const titleEl = typeEl.querySelector('.cart--service-selected');
	if (typeEl && id) {
		typeEl.setAttribute('data-selected-id', id);
		typeEl.querySelector('.service-name').textContent = name;

		// close and return to the main service modal
		toggleServiceSlideBox(target.closest('.cart--service-box-slide'));

		if (type !== 'coaches') {
			return false;
		}
		// change the avatar to the image of the selected coach
		const image = target.getAttribute('data-image');
		const iconEl = titleEl.querySelector('.bsapp--icon');
		if (image) {
			if (iconEl.querySelector('img')) {
				iconEl.querySelector('img').setAttribute('src', image);
			} else {
				addClass(iconEl, 'bsapp--customer-avatar');
				iconEl.innerHTML = `<img src="${image}" alt="${name}">`;
			}
		} else {
			iconEl.innerHTML = '<i class="fa-solid fa-circle-user s-24"></i>';
		}
	}
}

function toggleServiceSlideBox(el, target = null) {
	const className = 'is-visible';
	const flag = toggleClass(el, className);
	const modalBody = el.closest('.modal-body');
	if (modalBody) {
		if (flag) {
			setTimeout(function() {
				modalBody.style.position = "relative";
			}, 500);
		} else {
			// open
			modalBody.style.position = "static";

			const calendarEl = el.querySelector('.bsapp--calendar--content');
			if (target && target.getAttribute('data-service-type') === 'dates' && calendarEl && calendarEl.childNodes.length === 0) {
				createServiceCalendar({
					date: target.getAttribute('data-service-date'),
					time: target.getAttribute('data-service-time'),
					durationMin: +target.getAttribute('data-service-duration-min')
				});
			}
		}
	}
}

function saveServiceDateTime(target, e) {
	const modalSlideBox = target.closest('[data-service-type="dates"]');
	if (!modalSlideBox) {
		return false;
	}

	const durationCustomSelect = modalSlideBox.querySelector('[name="serviceDurationCustomSelect"]');
	const durationCustomSelectOption = durationCustomSelect && durationCustomSelect.querySelector('[selected=selected]');
	if (durationCustomSelectOption && durationCustomSelectOption.value && durationCustomSelectOption.value !== modalSlideBox.getAttribute(serviceDurationAttrName)) {
		modalSlideBox.setAttribute(serviceDurationAttrName, durationCustomSelectOption.value);
	}

	const timeCustomSelect = modalSlideBox.querySelector('[name="serviceTimeCustomSelect"]');
	const timeCustomSelectOption = timeCustomSelect && timeCustomSelect.querySelector('[selected=selected]');
	if (timeCustomSelectOption && timeCustomSelectOption.value && timeCustomSelectOption.value !== modalSlideBox.getAttribute(serviceTimeAttrName)) {
		modalSlideBox.setAttribute(serviceTimeAttrName, timeCustomSelectOption.value);
	}
	const dateStr = modalSlideBox.getAttribute(serviceDateAttrName);
	const dateSaveBtn = target.getAttribute(serviceDateAttrName);
	if (dateSaveBtn && dateStr !== dateSaveBtn) {
		modalSlideBox.setAttribute(serviceDateAttrName, dateSaveBtn);
	}

	// change date and time content of the main modal
	if (timeCustomSelectOption && timeCustomSelectOption.value && modalSlideBox.querySelector('.service-name')) {
		modalSlideBox.querySelector('.service-name').textContent = setDateTimeMomentString(dateSaveBtn ? dateSaveBtn : dateStr, timeCustomSelectOption.value);
	}

	// close
	toggleServiceSlideBox(target.closest('.cart--service-box-slide'));
}

async function checkCartLesson(target) {
	const modalDialog = target.closest('.modal-dialog');
	const modalDetails = modalDialog.querySelector('.js--cart-detail-content');
	const itemCurrentCartId = modalDetails.getAttribute('data-item-current-cart-id');
	const itemId = modalDetails.getAttribute('data-item-id');

	if (modalDetails &&
		modalDetails.getAttribute('data-item-type') === 'lesson'
		&& itemCurrentCartId === null) {

		if (itemId !== null) {
			// checking client in the lesson before add to cart
			const afterRequestIds = await sendFetch(cartControllerUrl, {
				action: 'getClientInLesson',
				id: itemId
			}).then(function (response) {
				if (!response.success) {
					// show error modal
					showErrorModal({
						error: response.message
					});
					return false;
				}


				const {ids} = response;
				if (!ids) {
					throw new Error('[getClientInLesson] Invalid of received ids data, expected array');
				}
				// append ids to lesson additional data
				const saveCartItemEl = document.getElementById('saveCartItem');
				const primaryJson = saveCartItemEl.getAttribute('data-primary-json');
				if (primaryJson) {
					const addJsonParse = JSON.parse(primaryJson);
					addJsonParse.clientIds = ids;
					saveCartItemEl.setAttribute('data-primary-json', JSON.stringify(addJsonParse));
				}

				return ids;
			});

			const clientId = getCartClientId();
			if (afterRequestIds.includes(clientId)) {
				showClientInLessonModal();
				return false;
			}
		}

		const additionInfo = target.getAttribute('data-additional-json') && JSON.parse(target.getAttribute('data-additional-json'));
		if (additionInfo.participants && additionInfo.participantsMax
			&& parseInt(additionInfo.participants) >= parseInt(additionInfo.participantsMax)) {
			// show a modal if the lesson is full to add a client over the maximum or to a waiting list
			showErrorModal({
				textKey: 'max_exceeds_assign',
				error: Lang('desk_select_action'),
				bottomBtns: [
					{
						textKey: 'book_and_exceed_desk',
						dataType: 'overMax',
						dataId: 'lessonClientAssign'
					},
					{
						textKey: 'desk_book_as_waiting',
						dataType: 'waitingList',
						dataId: 'lessonClientAssign'
					}
				]
			});
			return false;
		}
	}

	return true;
}

function saveCartItemEditModal(target, lessonClientAssign = null) {
	const modalDialog = target.closest('.modal-dialog');
	let qty = modalDialog.querySelector('[data-item-quantity]') && modalDialog.querySelector('[data-item-quantity]').getAttribute('data-item-quantity');
	qty = qty ? parseInt(qty) : 1;

	const itemPriceEl = modalDialog.querySelector(`[name="${itemPriceName}"]`);
	if (itemPriceEl && parseFloat(itemPriceEl.value) === 0) {
		addClass(itemPriceEl, 'error');
		return false;
	}

	const itemVariantsSelectEl = modalDialog.querySelector('[name="itemVariantsSelect"]');
	if (itemVariantsSelectEl && itemVariantsSelectEl.querySelector('[selected="selected"]').value === '') {
		const optionSelects = itemVariantsSelectEl.parentNode.querySelectorAll('.bsapp--custom-select_hidden');
		for (let i = 0; i < optionSelects.length; i++) {
			const selectedOpt = optionSelects[i].querySelector('[selected="selected"]');
			if (selectedOpt === null) {
				addClass(optionSelects[i].parentNode, 'error');
			}
		}
		return false;
	}

	const toSendItem = setItemDataModal(modalDialog, qty, true);
	if (toSendItem === false) {
		throw new Error('There is no data');
	}
	if (itemVariantsSelectEl) {
		toSendItem.variantId = +itemVariantsSelectEl.querySelector('[selected="selected"]').value;
	}

	const itemPackageTypeEl = modalDialog.querySelector(`[name="${packageTypeName}"]`);
	if (toSendItem.type === 'package'
		&& itemPackageTypeEl
		&& itemPackageTypeEl.querySelector('[selected="selected"]')
		&& itemPackageTypeEl.querySelector('[selected="selected"]').value !== '') {
		toSendItem.membershipStartCount = parseInt(itemPackageTypeEl.querySelector('[selected="selected"]').value);

		if (parseInt(toSendItem.membershipStartCount) === membershipStartCountId.fromDate) {
			const itemManualStartEl = modalDialog.querySelector(`[name="${manualStartName}"]`);
			const itemManualEndEl = modalDialog.querySelector(`[name="${manualEndName}"]`);

			if (itemManualStartEl && itemManualStartEl.value !== null && itemManualStartEl.value !== '') {
				toSendItem[manualStartName] = itemManualStartEl.value;
			} else {
				addClass(itemManualStartEl, 'error');
				return false;
			}

			if (itemManualEndEl &&  itemManualEndEl.value !== null && itemManualEndEl.value !== '') {
				toSendItem[manualEndName] = itemManualEndEl.value;
			} else {
				addClass(itemManualEndEl, 'error');
				return false;
			}
		}
	}

	if (toSendItem.type === 'lesson' && lessonClientAssign && lessonClientAssign !== '') {
		toSendItem.lessonClientAssign = lessonClientAssign;
	}

	if (toSendItem.type === 'service') {
		const placesEl = modalDialog.querySelector('[data-service-type="places"]');
		if (placesEl) {
			toSendItem.diaryId = +placesEl.getAttribute('data-selected-id');
		}
		const coachesEl = modalDialog.querySelector('[data-service-type="coaches"]');
		if (coachesEl) {
			toSendItem.coachId = +coachesEl.getAttribute('data-selected-id');
		}
		const datesEl = modalDialog.querySelector('[data-service-type="dates"]');
		if (datesEl) {
			toSendItem.time = datesEl.getAttribute(serviceTimeAttrName);
			toSendItem.date = datesEl.getAttribute(serviceDateAttrName);
			toSendItem.durationMin = +datesEl.getAttribute(serviceDurationAttrName);
		}
	}

	// check if item already in the cart array
	if (toSendItem.itemCurrentCartId) {
		const pos = getCartIndex('itemCurrentCartId', toSendItem.itemCurrentCartId);
		if (pos > -1) {
			toSendItem.action = "editItemCart";
		}
	}

	closeModal('bsappSearchModal');
	closeModal('bsappErrorModal');
	sendCartItem(toSendItem, 'bsappModal');
}

function changePackageEndDate(target, e) {
	if (target.classList.contains('error')) {
		removeClass(target, 'error');
	}

	const modalDialog = target.closest('.modal-dialog');
	const manualStartEl = modalDialog.querySelector(`[name="${manualStartName}"]`);
	const manualStartValue = manualStartEl.value;
	if (!manualStartEl || !manualStartValue) {
		return false;
	}

	const durationNum = manualStartEl.getAttribute('data-duration-num');
	const durationType = manualStartEl.getAttribute('data-duration-type');
	if (!durationNum || !durationType) {
		return false;
	}

	const realDiff = moment(target.value).diff(moment(manualStartValue), getMomentType(durationType), true);
	const errorData = {
		classNameModal: 'modal--smaller'
	};

	if (realDiff === parseInt(durationNum)) {
		return false;
	} else if (realDiff <= 0) {
		target.value = setDefaultDuration(manualStartValue, durationNum, durationType);
		return false;
	} else if (realDiff < parseInt(durationNum)) {
		errorData['textKey'] = 'cart_period_shortened';
	} else if (realDiff > parseInt(durationNum)) {
		errorData['textKey'] = 'cart_period_extended';
	}

	target.blur();
	showErrorModal(errorData);
}

function changePackageStartDate(target, e) {
	const durationNum = target.getAttribute('data-duration-num');
	const durationType = target.getAttribute('data-duration-type');
	if (!durationNum || !durationType) {
		return false;
	}

	const startValue = target.value;
	const startValueMin = target.getAttribute('min');
	if (startValueMin !== null && moment(startValue).diff(moment(startValueMin), 'days') <= 0) {
		target.value = startValueMin;
		return false;
	}

	const modalDialog = target.closest('.modal-dialog');
	const manualEndEl = modalDialog.querySelector(`[name="${manualEndName}"]`);
	if (manualEndEl) {
		manualEndEl.value = setDefaultDuration(target.value, durationNum, durationType);
	}
}

export function setDefaultDuration(start, num, type) {
	return moment(start).add(parseInt(num), getMomentType(type)).format("YYYY-MM-DD");
}

function getMomentType(type = 1) {
	const typeStr = getDurationType(type);
	switch (typeStr) {
		case 'week':
			return 'w';
		case 'month':
			return 'M';
		case 'year':
			return 'y';
		default:
			return 'd';
	}
}

function changeValueWithRemoveIcon(target, inputRemoveClassEl) {
	const targetValue = target.value;
	const targetName = target.getAttribute('name');
	if (target.getAttribute('required') !== null && targetValue.length === 0) {
		triggerErrorClass(target, true);
	} else {
		triggerErrorClass(target);
	}

	const nextEl = target.parentNode.querySelector(inputRemoveClassEl);
	if (target.classList.contains('js--group-input--remove') && nextEl && nextEl.getAttribute('type') === 'button') {
		if (targetValue.length === 0
			|| (target.getAttribute('type') === 'tel' && parseFloat(targetValue) === 0)) {
			nextEl.classList.remove('active');
		} else {
			nextEl.classList.add('active');
		}
	}

	if (targetName === 'searchInput') {
		buildSearch(target);

	} else if (target.getAttribute('data-discount-value') !== null) {
		checkDiscountValue(target);
	}
}

function changeDiscountValue(target, e) {
	checkDiscountValue(target);

	if (target.getAttribute('data-type') === discountTypePercentId && parseInt(target.value) > 100) {
		triggerErrorClass(target, true);
		return false;
	}

	buildDiscountBox();
}

function changeDiscountType(target, e) {
	if (target.classList.contains('active')) {
		return false;
	}

	removeClass(target.parentNode.querySelectorAll('.btn'), 'active');
	target.classList.add('active');

	if (target.closest('.js--cart-box-discount')) {
		checkDiscountValue(target.closest('.js--cart-box-discount').querySelector('[data-discount-value]'));
	}
	buildDiscountBox();
}

function checkDiscountValue(target) {
	if (!target) {
		return false;
	}

	const targetValue = parseInt(target.value);
	if (target.getAttribute('data-type') === discountTypePercentId) {
		if (target.value.indexOf('.') > -1) {
			target.value = '0';
		} else if (targetValue > 100) {
			target.value = '100';
		}

	} else {
		const modalDialog = target.closest('.modal-dialog');
		const price = modalDialog && modalDialog.querySelector('[data-price-original]') && modalDialog.querySelector('[data-price-original]').getAttribute('data-price-original');
		if (price && targetValue > +price) {
			target.value = price;
		}
	}
}

function changeItemPrice(target, e) {
	// const targetValue = target.getAttribute('data-price') ? target.getAttribute('data-price') : target.value;
	const targetValue = target.value;
	const modalDialog = target.closest('.modal-dialog');

	if (!modalDialog) {
		return false;
	}
	if (modalDialog.querySelector('[data-price-original]')) {
		modalDialog.querySelector('[data-price-original]').setAttribute('data-price-original', targetValue);
		setItemDataModal(modalDialog);
	}
}

function changeItemQty(target, e) {
	let elOperation = target.getAttribute('data-type');
	if (!elOperation) {
		elOperation = 'plus';
	}

	const modalDialog = target.closest('.modal-dialog');
	const detailQtyEl = modalDialog.querySelector('.cart--quantity-detail');
	const itemQtyEl = modalDialog.querySelector('[data-item-quantity]');
	const itemQty = itemQtyEl && itemQtyEl.getAttribute('data-item-quantity');
	if (itemQty === null) {
		return false;
	}

	// control of the number of product (availability of products in stock is not limited, only shows warning text)
	const qtyMaxEl = modalDialog.querySelector('[data-item-max-quantity]');
	const qtyMax = qtyMaxEl && qtyMaxEl.getAttribute('data-item-max-quantity');
	const qty = changeQty(elOperation, itemQty, qtyMax);
	if (parseInt(itemQtyEl.getAttribute('data-item-quantity')) === qty) {
		return false;
	}

	itemQtyEl.setAttribute('data-item-quantity', qty);
	if (detailQtyEl) {
		detailQtyEl.innerHTML = quantityBox({
			quantity: qty,
			quantityErrorText: qtyMax !== '' && qty > parseInt(qtyMax) ? Lang('cart_note_max_in_stock') : null
		});
	}

	setItemDataModal(modalDialog, qty);
}

function changeOptionVariants(target, e) {
	const id = target.value;
	if (!id || id === '') {
		return false;
	}

	const parent = target.parentNode;
	const selectedOption = target.querySelector('[selected="selected"]');
	const title = selectedOption.textContent;

	// change color circle preview
	const colorValue = selectedOption.getAttribute('data-color');
	const mainColorCircle = parent.querySelector('.form--group-rel').querySelector('.form--group-icon--color');
	if (colorValue && mainColorCircle) {
		mainColorCircle.style.backgroundColor = colorValue;
		mainColorCircle.setAttribute('data-color', colorValue);
		mainColorCircle.setAttribute('title', title);
	}

	// change other option select (if exists)
	const modalEl = target.closest('.modal-dialog');
	const itemVariantsSelectEl = modalEl.querySelector('[name="itemVariantsSelect"]');
	const currentPosition = +target.getAttribute('data-position');
	const otherPosition = currentPosition === 1 ? 2 : 1;
	const otherSelect = modalEl.querySelector(`.bsapp--custom-select_hidden[data-position="${otherPosition}"]`);
	const otherSelecteParent = otherSelect && otherSelect.parentNode;

	// hide all options in other dropdown that not exist
	if (otherSelect !== null) {
		const allAvailableOtherOption = [...itemVariantsSelectEl.querySelectorAll('option')].filter(opt => {
			return opt.getAttribute(`data-option${currentPosition}`) === id.toString();
		});

		if (otherSelecteParent && otherSelecteParent.classList.contains('bsapp--custom-select_colors')) {
			for (let i = 0; i < otherSelecteParent.querySelectorAll('.js--dropdown-item').length; i++) {
				otherSelecteParent.querySelectorAll('.js--dropdown-item')[i].setAttribute('disabled', 'disabled');
			}
			allAvailableOtherOption.forEach(el => {
				const currentId = el.getAttribute(`data-option${otherPosition}`);
				const currentEl = otherSelecteParent.querySelector(`.js--dropdown-item[data-id="${currentId}"]`);
				if (currentEl) {
					currentEl.removeAttribute('disabled');
				}
			});
		}
	}

	// change main variants select
	const currentMainOption = itemVariantsSelectEl.querySelector('[selected="selected"]');
	if (currentMainOption) {
		setSelectedSelect(itemVariantsSelectEl, itemVariantsSelectEl.querySelector('[value=""]').value);
	}

	const otherSelectedOption = otherSelect && otherSelect.querySelector('[selected="selected"]');
	const allDropdowns = itemVariantsSelectEl.parentNode.querySelectorAll('.bsapp--custom-select_hidden');

	let selectedMainOption;
	if (allDropdowns.length === 1) {
		selectedMainOption = itemVariantsSelectEl.querySelector(`[data-option${currentPosition}="${id}"]`);
	} else if (otherSelectedOption && allDropdowns.length === 2) {
		selectedMainOption = itemVariantsSelectEl.querySelector(`[data-option${currentPosition}="${id}"][data-option${otherPosition}="${otherSelectedOption.value}"]`);
	}

	// if variant exists
	if (selectedMainOption) {
		setSelectedSelect(itemVariantsSelectEl, selectedMainOption.value);

		// change inventory
		const inventory = selectedMainOption.getAttribute('data-inventory');
		const maxQtyEl = modalEl.querySelector('[data-item-max-quantity]');
		if (inventory && maxQtyEl && maxQtyEl.getAttribute('data-item-max-quantity') !== inventory) {
			modalEl.querySelector('.inventory[data-inventory]').textContent = 'X' + inventory;
			maxQtyEl.setAttribute('data-item-max-quantity', inventory);
			if (maxQtyEl.querySelector('.error') && parseInt(maxQtyEl.getAttribute('data-item-quantity')) <= parseInt(inventory)) {
				maxQtyEl.querySelector('.error').remove();
				removeClass(maxQtyEl.querySelector('.cart--quantity-box'), 'p-b-4');
			}
		}

	} else if (otherSelect) {
		otherSelecteParent.querySelector('input').value = '';
		setSelectedSelect(otherSelect, '');

		// reset color dropdown
		const colorCircleEl = otherSelecteParent.querySelector('.form--group-icon--color');
		if (otherSelect.getAttribute('name') === 'colorsOptionSelect' && colorCircleEl) {
			colorCircleEl.setAttribute('data-color', '');
			colorCircleEl.setAttribute('title', '');
			colorCircleEl.style.backgroundColor = '';
		}
	}
}

export function appendNumber(number, target) {
	let currentOperand = target.getAttribute('data-current-operand');
	if (parseFloat(currentOperand) !== 0) {
		triggerErrorClass(target, false);
	}

	if (number === '.' && (currentOperand.includes('.') || currentOperand === '')) {
		return false;
	}

	if (currentOperand.includes('.')) {
		const decimalDigits = currentOperand.split('.')[1];
		if (decimalDigits && decimalDigits.length === 2) {
			currentOperand = (parseFloat(currentOperand) * 10).toFixed(1);
		}
	}

	target.setAttribute('data-current-operand', currentOperand.toString() + number.toString());
	updateDisplayNumber(target);
}

function getDisplayNumber(number, withZero = false) {
	const stringNumber = number.toString();
	const integerDigits = parseFloat(stringNumber.split('.')[0]);
	const decimalDigits = stringNumber.split('.')[1];
	let integerDisplay;

	if (isNaN(integerDigits)) {
		integerDisplay = '';
	} else {
		integerDisplay = integerDigits.toLocaleString('en', {
			maximumFractionDigits: 2
		});
	}

	if (decimalDigits != null) {
		const decimalDigitsCents = withZero && decimalDigits.length === 1 ? decimalDigits + '0' : decimalDigits;
		return `${integerDisplay}.${decimalDigitsCents}`;
	} else {
		return integerDisplay;
	}
}

export function updateDisplayNumber(target, withZero = false) {
	const currentOperand = target.getAttribute('data-current-operand');
	let number = parseFloat(currentOperand);
	if (isNaN(number)) {
		number = 0;
	}

	target.setAttribute('data-price', number.toFixed(2));
	const currentPriceValue = number === 0 ? 0 : getDisplayNumber(currentOperand, withZero);
	target.value = currentPriceValue;

	if (target.getAttribute('name') === itemPriceName) {
		changeItemPrice(target);
	}

	if (target.matches('.js--group-input--remove'))  {
		changeValueWithRemoveIcon(target, '.js--group-icon--remove');
	}
}

export function setDisplayNumber(target, newValue) {
	// console.log('setDisplayNumber', newValue, target.value);
	target.setAttribute('data-price-max', newValue);
	target.value = (+newValue).toFixed(2);
}

export function deleteNumber(target) {
	const currentOperand = target.getAttribute('data-current-operand');
	target.setAttribute('data-current-operand', currentOperand.toString().slice(0, -1));
}

function clearNumber(target) {
	target.setAttribute('data-current-operand', '');
}