const mediaQuery = "(max-width: 959.98px)";
export default {
	globalUrl: '/assets/office/develop/test-json',
	cartControllerUrl: '/office/ajax/Cart.php',
	refundControllerUrl: '/office/ajax/Refund.php',
	isMobile: {
		mediaQuery,
		matches: matchMedia ? window.matchMedia(mediaQuery) : window.innerWidth < 960
	},
	headerEl: document.getElementById('header'),
	mainEl: document.getElementById('main'),
	cartSubcategoriesEl: document.getElementById('cartSubcategories'),
	cartNavCategoriesEl: document.getElementById('cartNavCategories'),
	summaryAsideEl: document.getElementById('summaryAside'),
	bsappBarEl: document.getElementById('bsappBar'),
	bsappSidebarEl: document.getElementById('bsappSidebar'),
	bsappHalfSidebarEl: document.getElementById('bsappHalfSidebar'),
	bsappModalEl: document.getElementById('bsappModal'),
	bsappErrorModalEl: document.getElementById('bsappErrorModal'),
	bsappDropdownEl: document.getElementById('bsappDropdown'),
	bsappLessonItemModalEl: document.getElementById('bsappLessonItemModal'),
	checkoutTotalPriceEl: document.querySelector('[name="checkoutTotalPrice"]'),
	cookieCartItemsToSaveName: 'bsapp_cartItemsToSave',
	discountTypePercentId: '1', // discount percent value (manually change in the Handlebars partials)
	businessTypesWithoutVAT: [1, 5, 6],
	onlyOneToAddList: ['package', 'lesson'],
	membershipStartCountId: {
		fromPurchase: 1,
		fromLesson: 3,
		fromDate: 4
	},
	options: {
		helpData: {}
	}
}