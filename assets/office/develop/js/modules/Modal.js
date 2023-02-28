import "@/scss/modal.scss";
import {Lang} from "@modules/Lang";
import {addClass, removeClass} from "@modules/Helpers";
import {toggleActiveNav} from "@modules/cart/NavCategories";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {isMobile, bsappModalEl, bsappHalfSidebarEl} = cartGlobalVariable;

// Private general variables
let isInit = false;
const isVisible = "is-visible";
const isOpened = "opened";
const noOverflow = "no-overflow";
const hideScrollbar = "hide-scrollbar";

function initModal() {
	if (isInit) {
		return false;
	}

	const modal = "bsapp--modal";
	const openEls = document.querySelectorAll("[data-open-modal]");

	for (const el of openEls) {
		el.addEventListener("click", function() {
			const modalId = this.dataset.openModal;
			openModal({modalEl: modalId, target: this});

			const modalEl = document.getElementById(modalId);
			if (modalEl !== null) {
				if (modalEl.querySelector('.fc') !== null) {
					setTimeout(function () {
						window.dispatchEvent(new Event('resize'));
					}, 100);

				} else if (modalId === 'bsappItemGeneralModal') {
					const inpEl = modalEl.querySelector('[name="generalItemPrice"]');
					if (inpEl) {
						// reset general price
						inpEl.value = '0.00';
						inpEl.setAttribute('data-price', '0.00');
						inpEl.setAttribute('data-current-operand', '');
						if (inpEl.classList.contains('error')) {
							inpEl.classList.remove('error');
						}
					}
					const nameEl = modalEl.querySelector('[name="itemName"]');
					if (nameEl) {
						nameEl.value = Lang('general');
						if (nameEl.classList.contains('error')) {
							nameEl.classList.remove('error');
						}
					}
				}
			}
		});
	}

	const closeModalClass = ".js--close-modal";
	document.addEventListener('click', function(e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(closeModalClass)) {
				e.preventDefault();
				const el = !target.classList.contains("js--close-modal") ? target.closest(closeModalClass) : target;
				closeModal(el.closest(`.${modal}`));
				break;
			}
		}
	});

	document.addEventListener("mousedown", e => {
		if (e.target.classList.contains(modal) && e.target.classList.contains(isVisible)) {
			closeModal(document.querySelectorAll(`.${modal}.${isVisible}`));
		}
	});

	document.addEventListener("keyup", e => {
		if (e.key == "Escape" && document.querySelector(`.${modal}.${isVisible}`)) {
			closeModal(document.querySelectorAll(`.${modal}.${isVisible}`));
		}
	});

	if (!isInit) {
		isInit = true;
	}
}

async function openModal(props) {
	let {
		modalEl,
		target = null,
		reset = false
	} = props;

	if (typeof modalEl === 'string') {
		modalEl = document.getElementById(modalEl);
	}

	if (modalEl === null || modalEl.classList.contains(isVisible)) {
		return false;
	}

	if (reset) {
		await resetModal(modalEl);
	}

	if (target !== null
		&& modalEl.classList.contains('bsapp--dropdown')) {
		// calculate dropdown position
		calcPositionDropdown({
			modalEl,
			modalElDialog: modalEl.querySelector('.modal-dialog'),
			target
		});
	}

	if (modalEl.querySelector('.btn--full-mob') && !modalEl.classList.contains('footer--big-full-mob')) {
		modalEl.classList.add('footer--big-full-mob');
	}

	modalEl.classList.add(isVisible);
	document.body.classList.add(noOverflow);
	if (document.body.scrollHeight > window.innerHeight) {
		document.body.classList.add(hideScrollbar);
	}

	if (!isMobile.matches
		&& modalEl.getAttribute('id') === 'bsappItemGeneralModal'
		&& document.getElementById('generalItemPrice') !== null) {
		setTimeout(function () {
			document.getElementById('generalItemPrice').focus();
		}, 100);
	}

	setTimeout(function () {
		// console.log('opened');
		modalEl.classList.add(isOpened);
	}, 400);
}

function calcPositionHalfSidebar(target, modalElDialog) {
	const elRect = target.getBoundingClientRect();
	modalElDialog.style.height = (window.innerHeight - elRect.top + 1) + 'px';
	modalElDialog.style.width = elRect.width + 'px';
	modalElDialog.style.top = (elRect.top - 1) + 'px';
}

function calcPositionDropdown(props) {
	const {
		modalEl,
		modalElDialog,
		target
	} = props;

	// if target contains icon add rotate class
	if (target.querySelector('.fa-light, .fa-solid') && target.getAttribute('id')) {
		addClass(target, 'opened');
		modalEl.setAttribute('data-target-id', target.getAttribute('id'));
	}

	const targetParentWidth = target.parentNode.offsetWidth;
	const dropdownContent = modalElDialog.querySelector('.bsapp--dropdown-content');
	let maxWidthDesktop = dropdownContent && dropdownContent.getAttribute('data-width');
	if (maxWidthDesktop) {
		maxWidthDesktop = parseFloat(maxWidthDesktop);
	}

	const elRect = target.getBoundingClientRect();
	const modalElDialogWidth = modalElDialog.offsetWidth;
	const winWidth = window.innerWidth;
	const spaceToBottom = window.innerHeight - elRect.bottom;
	const spaceToLeft = (targetParentWidth > elRect.width && targetParentWidth > elRect.right ? targetParentWidth : winWidth) - elRect.right;
	const width = !isMobile.matches && maxWidthDesktop ? maxWidthDesktop : (elRect.width < modalElDialogWidth ? targetParentWidth : elRect.width);
	const elRectX = spaceToLeft < width && elRect.right > width ? elRect.right - width : elRect.x;
	modalElDialog.style.position = 'fixed';
	modalElDialog.style.width = width + 'px';

	if (modalElDialog.querySelector('.bsapp--dropdown-footer') && isMobile.matches) {
		// mobile view
		modalElDialog.style.removeProperty('top');
		modalElDialog.style.bottom = '1rem';
		modalElDialog.style.left = '1.5rem';
		if (width >= winWidth) {
			modalElDialog.style.width = `calc(${width}px - 3rem)`;
		}
	} else if (spaceToBottom < modalElDialog.offsetHeight) {
		// show the dropdown menu above the button
		modalElDialog.style.removeProperty('top');
		modalElDialog.style.bottom = (spaceToBottom + elRect.height + 12) + 'px';
		modalElDialog.style.left = elRectX + 'px';

	} else {
		// show the dropdown menu below the button
		modalElDialog.style.removeProperty('bottom');
		const topSpace = dropdownContent && dropdownContent.getAttribute('data-top');
		modalElDialog.style.top = (elRect.bottom + (topSpace ? parseInt(topSpace) : 4)) + 'px';
		modalElDialog.style.left = elRectX + 'px';
	}
}

function closeModal(el) {
	if (typeof el === 'string') {
		el = document.getElementById(el);
	}
	if (!el) {
		return;
	}

	const els = (el instanceof NodeList) ? [].slice.call(el) : [el];
	const closeModalsEl = els.length > 1 ? els[els.length - 1] : els[0];

	if (!closeModalsEl.classList.contains('is-visible')) {
		return;
	}

	const modalId = closeModalsEl.getAttribute('id');
	if (!isMobile.matches) {
		if (modalId === 'bsappLessonItemModal' && !bsappModalEl.classList.contains(isVisible)
			|| (modalId === 'bsappModal' && closeModalsEl.querySelector('.js--cart-detail-content[data-item-type="lesson"]') !== null)) {
			const prevCategoryTypeEl = document.getElementById('cartSubcategories').querySelector('.js--sub-level-return');
			const prevCategoryType = prevCategoryTypeEl !== null && prevCategoryTypeEl.getAttribute('data-type');
			if (prevCategoryType !== null) {
				toggleActiveNav(prevCategoryType,true);
			}
		}
	}

	if (closeModalsEl.classList.contains('bsapp--dropdown')) {
		// if target contains icon remove rotate class
		const openeIdEl = closeModalsEl.getAttribute('data-target-id');
		if (openeIdEl && document.getElementById(openeIdEl)) {
			removeClass(document.getElementById(openeIdEl), 'opened');
			closeModalsEl.removeAttribute('data-target-id');
		}
	}

	if (document.querySelectorAll('.bsapp--modal.' + isVisible).length === 1
		&& !(isMobile.matches && document.getElementById('summaryAside') && document.getElementById('summaryAside').classList.contains('show'))) {
		document.body.classList.remove(noOverflow, hideScrollbar);
	}
	closeModalsEl.classList.remove(isVisible, isOpened, 'footer--big-full-mob');
	setTimeout(function () {
		if (modalId !== null
			&& (modalId === 'bsappModal' || modalId === 'bsappErrorModal' || modalId === 'bsappDropdown')) {
			resetModal(closeModalsEl);
		} else {
			preloaderModal(closeModalsEl);
		}
	}, 400);
}

async function resetModal(el) {
	if (typeof el === 'string') {
		el = document.getElementById(el);
	}
	el.innerHTML = '<div class="modal-dialog">' +
		'<div class="modal--preloader js--modal-preloader">' +
		'<div class="spinner-border"><span class="sr-only">' + Lang('loading') + '</span></div>' +
		'</div></div>';
}

async function buildModal(props) {
	let {el, html, className} = props;
	if (typeof el === 'string') {
		el = document.getElementById(el);
	}

	// await resetModal(el);
	const modalDialogEl = el.querySelector('.modal-dialog');
	if (className && !modalDialogEl.classList.contains(className)) {
		modalDialogEl.classList.add(className);
	}
	modalDialogEl.innerHTML = html;
	return true;
}

export function preloaderModal(el, show = false) {
	if (typeof el === 'string') {
		el = document.getElementById(el);
	}

	const loader = el.querySelector('.js--modal-preloader');
	if (loader === null) {
		return false;
	}

	if (show) {
		removeClass(loader, 'd-none');
	} else {
		addClass(loader, 'd-none');
	}
}

export { initModal, openModal, closeModal, buildModal, resetModal };