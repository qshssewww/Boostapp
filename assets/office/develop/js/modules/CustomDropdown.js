import "@/scss/custom-dropdown.scss";
import customDropdown from "@partials/item/customDropdown";

import {
	slideToggle, toggleClass, removeClass, addClass, getElementHeight,
	setStylesOnElement, setSelectedSelect
} from "@modules/Helpers";
import {additionalEvents} from "@modules/cart/AdditionalEvents";

const openedClass = 'opened';
const activeClass = 'active';
const hideClass = 'd-none';
const coverClass = 'bsapp--custom-select-cover';
const bsappCustomClass = '.bsapp--custom-select';
const dropdownClass = '.js--group-dropdown';
const iconRemoveClass = '.js--group-icon--remove';
const iconArrowClass = '.js--group-icon--arrow';
const inputClass = '.js--group-input';
const dropdownItemClass = '.js--dropdown-item';

let isInit = false;
let customDropdownCallback = null;

export default function CustomDropdown(props) {
	const {el, options, _handleClick} = props;

	if (_handleClick) {
		customDropdownCallback = _handleClick;
	}

	if (el) {
		const param = {
			labelKey: 'choose_option',
			className: null,
			required: false,
			disabled: false,
			bigClass: false,
			type: 'general',
			search: false,
			createSelect: false,
			selectId: null,
			placeholderKey: 'choose_option',
			items: []
		};
		const settings = Object.assign({}, param, options);
		el.innerHTML = customDropdown(settings);
	}

	initEvents();
}

function initEvents() {
	if (isInit) {
		return false;
	}

	document.addEventListener('click', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(inputClass)) {
				e.preventDefault();
				openDropdown.call(this, target, e);
				break;

			} else if (target.matches(iconArrowClass)) {
				e.preventDefault();
				if (target.parentNode.querySelector(inputClass)
					&& target.parentNode.querySelector(inputClass).getAttribute('disabled') === 'disabled') {
					return false;
				}
				// toggleDropdown(target.closest(bsappCustomClass));
				openDropdown.call(this, target, e);
				break;

			} else if (target.matches(dropdownItemClass)) {
				e.preventDefault();
				chooseItem.call(this, target, e);
				break;

			} else if (target.matches(iconRemoveClass)) {
				e.preventDefault();
				const prevEl = target.parentNode.querySelector(inputClass);
				if (prevEl) {
					searchItem(prevEl);
				}
				break;

			} else if (target.matches('.' + coverClass)) {
				e.preventDefault();
				toggleDropdown(document.querySelector(`${bsappCustomClass}.${openedClass}`));
				break;

			}
		}
	}, false);

	document.addEventListener('keyup', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(inputClass)) {
				e.preventDefault();
				if (e.key === "Tab") {
					return false;
				}
				searchItem.call(this, target, e);
				break;
			}
		}
	}, false);

	document.addEventListener('keypress', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(dropdownItemClass)) {
				if (e.key === "Enter") {
					chooseItem.call(this, target, e);
				}
				break;
			}
		}
	}, false);

	additionalEvents();

	if (!isInit) {
		isInit = true;
	}
}

function chooseItem(target, e) {
	const totalItems = [...target.parentNode.querySelectorAll(dropdownItemClass)];
	totalItems.forEach((el, index) => {
		el.setAttribute('aria-selected', el == target);
	});

	const targetId = target.getAttribute('data-id');
	const bsappCustomEl = target.closest(bsappCustomClass);
	if (bsappCustomEl.classList.contains('error')) {
		removeClass(bsappCustomEl, 'error');
	}

	bsappCustomEl.querySelector(inputClass).value = target.getAttribute('aria-label');
	const selectEl = bsappCustomEl.querySelector('select');
	if (selectEl) {
		// change select value
		setSelectedSelect(selectEl, targetId);
		if (selectEl.classList.contains('error')) {
			removeClass(selectEl, 'error');
		}
	}
	toggleDropdown(bsappCustomEl);

	if (customDropdownCallback !== null) {
		customDropdownCallback(target);
	}
}

export function searchItem(target, e) {
	if (target.classList.contains(' js--group-input--search')) {
		return false;
	}

	const searchVal = target.value;
	const deleteBtn = target.parentNode.querySelector(iconRemoveClass);
	// ignore keystrokes that don't make a difference
	if (target.getAttribute('data-last-search') == searchVal) {
		return true;
	}
	target.setAttribute('data-last-search', searchVal);
	if (searchVal.length === 0) {
		removeClass(deleteBtn, activeClass);
	} else {
		addClass(deleteBtn, activeClass);
	}

	searchByList(target);
}

function searchByList(input) {
	const searchVal = input.value;
	const bsappCustomEl = input.closest(bsappCustomClass);
	const dropdownEl = bsappCustomEl.querySelector(dropdownClass);
	const totalItems = [...dropdownEl.querySelectorAll(dropdownItemClass)];
	const totalItemsLength = totalItems.length;

	totalItems.forEach((el, index) => {
		// search through aria-label attribute
		const optText = el.getAttribute('aria-label');
		// show option if string exists
		const isContains = optText.toLowerCase().indexOf(searchVal.toLowerCase()) > -1;
		if (isContains) {
			removeClass(el, hideClass);
		} else {
			addClass(el, hideClass);
		}

		el.setAttribute('aria-disabled', !isContains);
	});

	if (totalItemsLength === [...dropdownEl.querySelectorAll(`${dropdownItemClass}.${hideClass}`)].length) {
		addClass(dropdownEl, 'no-items');
	} else {
		removeClass(dropdownEl, 'no-items');
	}
}

function openDropdown(target, e) {
	const bsappCustomEl = target.closest(bsappCustomClass);
	const input = target.classList.contains(inputClass) ? target : bsappCustomEl.querySelector(inputClass);
	if (input.getAttribute('disabled') === 'disabled') {
		return false;
	}
	if (input.getAttribute('readonly') !== null) {
		beforeOpenDropdown(bsappCustomEl);
		toggleDropdown(bsappCustomEl);

	} else if (bsappCustomEl.classList.contains(openedClass)) {
		input.focus();
	}
}

function beforeOpenDropdown(bsappCustomEl) {
	const dropdown = bsappCustomEl.querySelector(dropdownClass);
	if (!dropdown) {
		return false;
	}
	const container = bsappCustomEl.querySelector(inputClass),
		offset = container.getBoundingClientRect(),
		height = container.offsetHeight,
		dropdownStyle = window.getComputedStyle(dropdown),
		dropdownMinWidth = parseInt(dropdownStyle.minWidth),
		width = bsappCustomEl.offsetWidth,
		dropHeight = getElementHeight(dropdown.querySelector('ul')),
		modal = bsappCustomEl.closest('.modal-dialog') || document.body,
		windowBounding = modal.getBoundingClientRect(),
		viewPortRight = windowBounding.right,
		viewportBottom = windowBounding.bottom,
		modalStyle = window.getComputedStyle(modal),
		isModalStyleFixed = modalStyle.position === 'fixed';

	let dropLeft = offset.left,
		dropTop = offset.top + height,
		dropTopWithoutHeight = offset.top - dropHeight,
		viewPortTop = windowBounding.top;

	let enoughRoomBelow = dropTop + dropHeight <= viewportBottom,
		enoughRoomAbove = isModalStyleFixed && !enoughRoomBelow ? offset.top > dropTopWithoutHeight : dropTopWithoutHeight >= viewPortTop,
		dropWidth = dropdown.offsetWidth;

	const enoughRoomOnRight = function() {
			return dropLeft + dropWidth <= viewPortRight;
		},
		enoughRoomOnLeft = function() {
			return offset.left + viewPortRight + container.offsetWidth  > dropWidth;
		},
		above = !enoughRoomBelow && enoughRoomAbove;

	// console.log("dropdownMinWidth:", width, dropdownMinWidth);
	// console.log("offset:", offset);
	// console.log("below / droptop:", dropTop, "dropHeight", dropHeight, "sum", (dropTop+dropHeight)+" viewport bottom", viewportBottom, "enough?", enoughRoomBelow);
	// console.log("above / offset.top", offset.top, "dropHeight", dropHeight, "top", (offset.top-dropHeight), "scrollTop", modal.top, "enough?", enoughRoomAbove);

	if (!enoughRoomOnRight() && enoughRoomOnLeft()) {
		dropLeft = offset.left + container.offsetWidth - dropWidth;
	}

	const zIndex = parseInt(dropdownStyle.zIndex) + 2;
	if (container.classList.contains('js--group-input--search')) {
		setStylesOnElement(container.closest('.form--group-items'), {zIndex});
	}
	const leftPos = isModalStyleFixed && document.dir === 'ltr' ? dropLeft - (window.innerWidth - parseInt(modalStyle.width)) : dropLeft;
	const css =  {
		position: 'fixed',
		left: (dropdownMinWidth > 0 ? leftPos - (dropdownMinWidth - width) : leftPos) + 'px',
		width: (dropdownMinWidth > 0 ? dropdownMinWidth : width) + 'px',
		zIndex
	};
	const space = bsappCustomEl.classList.contains('bsapp--custom-select_colors') || bsappCustomEl.classList.contains('bsapp--custom-select_sizes') ? 0.25 : 0.5;

	if (above) {
		css.bottom = `calc(${window.innerHeight - offset.top - 2}px + ${space}rem)`;
		css.top = 'auto';
	} else {
		if (isModalStyleFixed) {
			dropTop = dropTop - viewPortTop;
		}
		css.top = `calc(${dropTop}px + ${space}rem)`;
		css.bottom = 'auto';
	}

	setStylesOnElement(dropdown, css);
}

function toggleDropdown(bsappCustomEl) {
	const dropdownEl = bsappCustomEl.querySelector(dropdownClass);
	const input = bsappCustomEl.querySelector(inputClass);
	if (!dropdownEl || input.getAttribute('disabled') === 'disabled') {
		return false;
	}
	const iconSearch = bsappCustomEl.querySelector(iconRemoveClass);

	// toggle opened class
	toggleClass(bsappCustomEl, openedClass);
	const modalDialog = bsappCustomEl.closest('.modal-dialog');
	if (modalDialog) {
		toggleClass(modalDialog, openedClass);
	}

	const isDropdownOpen = bsappCustomEl.classList.contains(openedClass);
	if (isDropdownOpen) {
		createCover(bsappCustomEl);
		if (window.getComputedStyle(modalDialog).position === 'fixed') {
			createCover();
		}
	}

	const haveSearchHandle = iconSearch !== null && isDropdownOpen;
	if (iconSearch !== null) {
		if (isDropdownOpen) {
			if (input.value !== '') {
				input.value = '';
			}
			searchItem(input);
		} else {
			const selectedItem = bsappCustomEl.querySelector(dropdownItemClass + '[aria-selected="true"]');
			const selectedItemLabel = selectedItem && selectedItem.getAttribute('aria-label');
			if (selectedItemLabel) {
				input.value = selectedItemLabel;
			}
		}
	}
	// toggle slide down dropdown
	slideToggle(dropdownEl, 200).then(function() {
		if (isDropdownOpen) {
			// scrolling to selected item
			const liSelected = dropdownEl.querySelector('[aria-selected="true"]');
			if (liSelected) {
				liSelected.scrollIntoView();
			}
		} else {
			removeStyles(dropdownEl);
			const cover = document.querySelectorAll(`.${coverClass}.d-block`);
			for (let i = 0; i < cover.length; i++) {
				cover[i].parentNode.removeChild(cover[i]);
			}
		}

		if (haveSearchHandle) {
			input.removeAttribute('readonly');
			input.blur();
		} else if (input.getAttribute('readonly') === null) {
			input.setAttribute('readonly', 'readonly');
		}
	});
}

function removeStyles(el) {
	el.style.removeProperty('width');
	el.style.removeProperty('left');
	el.style.removeProperty('top');
	el.style.removeProperty('bottom');
	el.style.removeProperty('z-index');
	el.style.removeProperty('position');
}

function getModalIndex(el) {
	if (!el.classList.contains('bsapp--custom-select')) {
		el = document.querySelector(`${bsappCustomClass}.${openedClass}`);
	}
	const modal = el.closest('.modal-dialog');
	let zIndex = window.getComputedStyle(modal).zIndex;
	return zIndex === 'auto' ? 1 : parseInt(zIndex);
}

function createCover(el = document.body) {
	const dropdownCover = document.createElement('div');
	dropdownCover.className = coverClass + ' d-block';
	el.appendChild(dropdownCover);
	if (el === document.body) {
		dropdownCover.style.zIndex = getModalIndex(el) - 1;
	}
}