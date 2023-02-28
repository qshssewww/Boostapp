import "@/scss/cart/item-general-modal.scss";

import {triggerErrorClass} from "@modules/Helpers";
import {sendCartItem} from "@modules/cart/CartHelpers";
import {
	additionalEvents, updateDisplayNumber, appendNumber, deleteNumber, validatePrice
} from "@modules/cart/AdditionalEvents";

const numberButtons = document.querySelectorAll('[data-number]');
const deleteButton = document.querySelector('[data-delete]');
const currentOperandTextElement = document.querySelector('[data-current-operand]');
const addGeneralItemBtn = document.getElementById('addGeneralItem');

function init() {
	additionalEvents();
	const generalItemNameInp = document.getElementById('generalItemName');
	const generalItemPriceInp = document.getElementById('generalItemPrice');

	addGeneralItemBtn.addEventListener('click', (e) => {
		e.preventDefault();
		const itemPrice = parseFloat(generalItemPriceInp.getAttribute('data-price'));
		if (generalItemNameInp.value === '' || isNaN(itemPrice) || itemPrice === 0) {
			if (generalItemNameInp.value === '') {
				generalItemNameInp.classList.add('error');
			} else {
				generalItemPriceInp.classList.add('error');
			}
			return false;
		}

		generalItemNameInp.classList.remove('error');
		generalItemPriceInp.classList.remove('error');

		// save a general item in cartObject
		sendCartItem({
			type: "general",
			name: generalItemNameInp.value,
			quantity: 1,
			price: itemPrice,
		}, 'bsappItemGeneralModal');
	});

	numberButtons.forEach(button => {
		button.addEventListener('click', () => {
			appendNumber(button.innerText, currentOperandTextElement);
		})
	});

	deleteButton.addEventListener('click', button => {
		deleteNumber(currentOperandTextElement);
		updateDisplayNumber(currentOperandTextElement);
	});

	currentOperandTextElement.addEventListener('change', function (event) {
		triggerErrorClass(event.target);
	});

	currentOperandTextElement.addEventListener('keydown', function (e) {
		validatePrice(e.target, e);
	});
}

export {init};