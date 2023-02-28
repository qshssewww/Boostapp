import "@/scss/cart/item-lesson-modal.scss";

import {Lang} from "@modules/Lang";
import FullCalendar from "@modules/FullCalendar";
import CustomDropdown from "@modules/CustomDropdown";
import {closeModal, openModal} from "@modules/Modal";
import {
	getLocaleTime, getPriceWithoutZero, sendFetch, showErrorModal,
	setDate, setTime, setDuration, withoutProperty
} from "@modules/Helpers";
import {
	calculateDiscountAmount, buildItemModal, setDiscount, getCartIndex
} from "@modules/cart/CartHelpers";

import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	options, globalUrl, bsappModalEl, bsappLessonItemModalEl, cartControllerUrl, onlyOneToAddList
} = cartGlobalVariable;

let isInit = false;

export function openLessonModal() {
	// send a post for lessons data
	getLessonsData();
	initEvents();
}

function initEvents() {
	if (isInit) {
		return false;
	}

	const backToCalLesson = '.js--back-to-calendar-lesson';
	document.addEventListener('click', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(backToCalLesson)) {
				e.preventDefault();
				closeModal(bsappModalEl);
				openModal({modalEl: bsappLessonItemModalEl});
				break;

			}
		}
	}, false);

	if (!isInit) {
		isInit = true;
	}
}

function getLessonsData() {
	const date = options.helpData.lessonInitialDate || new Date();
	// get all lessons for currently selected day
	sendFetch(cartControllerUrl, {
		action: 'getLessonsData',
		date: date.toISOString().slice(0,10)
	}).then(function(response) {
		if (!response.success) {
			// show error modal
			showErrorModal({
				error: response.message
			});
			return false;
		}

		// open a modal if close
		if (bsappLessonItemModalEl && !bsappLessonItemModalEl.classList.contains('is-visible')) {
			openModal({modalEl: bsappLessonItemModalEl});
		}

		const {items} = response;
		if (!items) {
			throw new Error('[getLessonsData] Invalid of received items data');
		}
		createDropdown(items);
		if (!options.helpData.lessonInitialDate) {
			createCalendar();
		}
	});
}

function createDropdown(lessons) {
	const items = [];
	const lessonCount = lessons.length;
	if (lessonCount > 0) {
		lessons.forEach(el => {
			// don't show a lesson if it already exists in the cart
			if (onlyOneToAddList.includes('lesson') && getCartIndex('id', el.id) > -1) {
				return;
			}

			items.push({
				id: el.id,
				text: el.title + (el.owner ? ' | ' + el.owner : '') + ' | ' + getLocaleTime(el.start),
				dataJson: JSON.stringify(el)
			});
		});
	}

	CustomDropdown({
		el: document.getElementById('lessonsContent'),
		options: {
			labelKey: 'class_single',
			type: 'lessons',
			search: true,
			disabled: lessonCount === 0 || items.length === 0,
			placeholderKey: lessonCount > 0 && items.length > 0 ? 'choose_class' : 'cart_no_lessons_found',
			items: items
		},
		_handleClick: function(target) {
			const itemJson = target.getAttribute('data-json');
			if (itemJson) {
				const item = buildSingleLesson(JSON.parse(itemJson));
				setTimeout(() => {
					buildItemModal(item);
				}, 200);
			}
		}
	});
}

export function buildSingleLesson(item) {
	const {
		itemCurrentCartId, originalPrice, discount, id, title, start, end,
		location, backgroundColor, owner, live, price_total, repeat_type, calendar_name, participants, participantsMax
	} = item;

	const priceFromResponse = itemCurrentCartId && originalPrice ? originalPrice : price_total;
	const itemPrice = priceFromResponse !== null ? parseFloat(priceFromResponse) : 0;
	const itemDiscount = discount ? discount : setDiscount();
	const discountObj = calculateDiscountAmount(itemPrice, itemDiscount.type, itemDiscount.value);

	// primary item data
	const primaryData = {
		id,
		type: 'lesson',
		quantity: 1,
		name: title,
		price: itemPrice,
		discount: discountObj,
		itemCurrentCartId
	};

	// compile data for modal
	const additionalData = {
		price_total,
		repeat_type,
		backgroundColor,
		owner,
		calendar_name,
		participants,
		participantsMax,
		live,
		dateStr: setDate(start, true),
		timeStr: setTime(start),
		durationStr: setDuration(start, end),
		branchName: location.indexOf('סניף') > -1 ? location.replace('סניף', '').trim() : location
	};

	const itemData = {...primaryData, ...additionalData};
	const additionalDataWithoutProperties = withoutProperty(item, ['itemCurrentCartId', 'discount', 'originalPrice', 'itemCurrentCartId']);
	itemData.additionalData = JSON.stringify(additionalDataWithoutProperties);
	itemData.primaryData = JSON.stringify(primaryData);

	if (priceFromResponse === null) {
		itemData.priceData = {
			labelKey: 'cart_entrance_fee',
			priceStr: priceFromResponse,
			price: getPriceWithoutZero(itemData.price)
		};
	}

	// console.log('[buildSingleLesson]', itemData);
	return itemData;
}

function createCalendar() {
	FullCalendar({
		el: document.getElementById('calendarContent'),
		_setInitialDate: function (date) {
			options.helpData.lessonInitialDate = date;
			getLessonsData();
		},
		options: {
			initialDate: options.helpData.lessonInitialDate || new Date(),
			select: null
		}
	});
}