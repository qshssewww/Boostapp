import "@/scss/cart/search-modal.scss";

import searchModal from "@partials/cart/searchModal.hbs";

import jsCookie from '@modules/Cookie';
import {Lang} from "@modules/Lang";
import {openModal, closeModal} from "@modules/Modal";
import {
	addClass, isEmptyObject, removeClass, getCategoryTranslations, trimStr, getIndex,
	readJsCookie, writeJsCookie
} from "@modules/Helpers";
import {buildCategory, getCategoryByType} from "@modules/cart/NavCategories";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	isMobile
} = cartGlobalVariable;

// Private variables
const config = {
	howManyToShow: 3,
	howManyToStoreInMemory: 6
};
let globalSearchItems = [];

let isInit = false;
const cookieRecentSearchesName = 'bsapp_cartRecentSearches';
const bsappSearchModalEl = document.getElementById('bsappSearchModal');
const searchResultsContentEl = document.getElementById('searchResultsContent');
const searchInputEl = document.querySelector('[name="searchInput"]');
const closeIconInputEls = document.querySelectorAll('.js--group-icon--close-modal');

export function initSearchModal() {
	initEvents();
	getAllCategoriesData();
	openSearchModal(true);
}

function initEvents() {
	if (isInit) {
		return false;
	}

	closeIconInputEls.forEach(el => {
		el.addEventListener('click', function (e) {
			const typeEl = document.getElementById('cartSubcategories').querySelector('.js--sub-level-return');
			if (typeEl && typeEl.getAttribute('data-type')) {
				buildCategory(typeEl.getAttribute('data-type'));
			}
			closeModal(bsappSearchModalEl);
			searchInputEl.blur();
		});
	});

	searchInputEl.addEventListener('input', function (e) {
		buildSearch(e.target, e);
	});

	if (!isInit) {
		isInit = true;
	}

	document.getElementById('searchForm').addEventListener('submit', e => {
		e.preventDefault();
	});
}

function setItemData(item, categoryType, subcategoryId = null, addCategoryName = false) {
	return {
		...item,
		...{
			isFavorite: item.isFavorite ? item.isFavorite : false,
			categoryType,
			parentTitle: addCategoryName ? getCategoryTranslations(categoryType) : null,
			subcategoryId,
			currentType: subcategoryId ? 'subItem' : 'item'
		}
	};
}

function setSubcategoryData(categoryType, sub) {
	return {
		...sub,
		...{
			isFavorite: sub.isFavorite ? sub.isFavorite : false,
			categoryType: categoryType,
			showChooseBtn: true,
			currentType: 'subcategory'
		}
	};
}

function getEmptyContent(key) {
	return {
		classFA: 'fa-light fa-magnifying-glass',
		title: Lang(key)
	};
}

function getAllCategoriesData() {
	globalSearchItems = [];
	const dataProducts = getCategoryByType('product');
	if (!isEmptyObject(dataProducts)) {
		globalSearchItems.push(dataProducts);
	}
	const dataPackages = getCategoryByType('package');
	if (!isEmptyObject(dataPackages)) {
		globalSearchItems.push(dataPackages);
	}
	const dataServices = getCategoryByType('service');
	if (!isEmptyObject(dataServices)) {
		globalSearchItems.push(dataServices);
	}
}

function isEqual(arrLength, howManyToShow, recently, id, categoryType) {
	return arrLength < howManyToShow
		&& id === recently.id
		&& categoryType === recently.type;
}

function isContains(obj, searchVal) {
	return obj.name && obj.name.toLowerCase().indexOf(trimStr(searchVal)) > -1;
	// return obj.name && obj.name.toLowerCase().split(' ').includes(searchVal);
}

function recordByType(data, props) {
	const position = getIndex(data, props.id);
	if (position === -1) {
		data.unshift(props);
		if (data.length > config.howManyToStoreInMemory) {
			data.splice(-1);
		}
	} else {
		data.splice(position, 1);
		data.unshift(props);
	}

	// console.log('[recordByType] data', data);

	return data;
}

export function recordRecentlyViewed(props) {
	let {
		id, type, isSubcategory = false
	} = props;

	let recentlyViewed = readJsCookie(cookieRecentSearchesName);
	let recentlyViewedSub = recentlyViewed && recentlyViewed.subcategories ? recentlyViewed.subcategories : [];
	let recentlyViewedItems = recentlyViewed && recentlyViewed.items ? recentlyViewed.items : [];

	if (isSubcategory) {
		recentlyViewedSub = recordByType(recentlyViewedSub, {id, type});
	} else {
		recentlyViewedItems = recordByType(recentlyViewedItems, {id, type});
	}

	writeJsCookie(cookieRecentSearchesName,
		{
			subcategories: recentlyViewedSub,
			items: recentlyViewedItems
		},
		365);
}

function openSearchModal(open = false) {
	if (searchInputEl.value.length > 0) {
		searchInputEl.value = '';
	}

	let recentData = buildRecentSearch();
	if (isMobile.matches && recentData.length > 0) {
		removeClass(searchResultsContentEl, 'with-bg');
	} else {
		addClass(searchResultsContentEl, 'with-bg');
	}

	buildSearchModal(recentData.length > 0 ? {
		results: recentData
	} :
		getEmptyContent('cart_no_search_history')
	);

	if (open) {
		openModal({modalEl: bsappSearchModalEl}).then(function () {
			setTimeout(() => {
				searchInputEl.focus();
			}, 400);
		});
	}
}

function buildSearchModal(data) {
	// console.log('[buildSearchModal]', data);
	searchResultsContentEl.innerHTML = searchModal(data);
}

export function buildSearch(target) {
	const searchVal = target.value.toLowerCase();
	if (searchVal.length === 0) {
		openSearchModal();
	}

	// ignore keystrokes that don't make a difference
	if (target.getAttribute('data-last-search') === searchVal || searchVal.length < 2) {
		return true;
	}
	target.setAttribute('data-last-search', searchVal);
	searchData(searchVal);
}

function buildSearchData(categoryType, items, title) {
	return {
		title: title ? title : getCategoryTranslations(categoryType),
		items
	};
}

function searchData(searchVal) {
	// search through available data
	if (globalSearchItems.length === 0) {
		return false;
	}
	const results = [];
	const subcategoriesRes = [];
	const productRes = [];
	const packageRes = [];
	const serviceRes = [];

	globalSearchItems.forEach((el, index) => {
		const categoryType = el.type;
		let data;
		if (categoryType === 'product') {
			data = productRes;
		} else if (categoryType === 'package') {
			data = packageRes;
		} else if (categoryType === 'service') {
			data = serviceRes;
		}

		const categoryTitle = el.categoryTitle;
		if (el.subcategories && el.subcategories.length > 0) {
			el.subcategories.forEach((sub, index) => {
				if (sub.items && sub.items.length > 0) {
					sub.items.forEach(item => {
						if (isContains(item, searchVal) && !item.disabled) {
							data.push(setItemData(item, categoryType, sub.id));
						}
					});
				}

				if (isContains(sub, searchVal)) {
					subcategoriesRes.push(setSubcategoryData(categoryType, sub));
				}
			});
		} else if (el.items && el.items.length > 0) {
			el.items.forEach(item => {
				if (isContains(item, searchVal) && !item.disabled) {
					data.push(setItemData(item, categoryType));
				}
			});
		}
	});

	if (subcategoriesRes.length > 0) {
		results.push({
			// type: 'subcategory',
			title: Lang('cart_categories'),
			items: subcategoriesRes
		});
	}

	if (productRes.length > 0) {
		results.push(buildSearchData('product', productRes));
	}
	if (packageRes.length > 0) {
		results.push(buildSearchData('package', packageRes));
	}
	if (serviceRes.length > 0) {
		results.push(buildSearchData('service', serviceRes));
	}

	buildSearchModal(results.length > 0 ? {
		results
	} :
		getEmptyContent('not_found')
	);
}

function buildRecentSearch() {
	const recentlyViewed = readJsCookie(cookieRecentSearchesName);
	if (!recentlyViewed || isEmptyObject(recentlyViewed)) {
		return [];
	}

	const results = [];
	const subcategoriesRes = [];
	const itemsRes = [];
	const recentlySub = recentlyViewed.subcategories;
	const recentlyItems = recentlyViewed.items;

	if (recentlySub.length > 0) {
		recentlySub.forEach(recently => {
			globalSearchItems.forEach(el => {
				const categoryType = el.type;
				if (el.subcategories && el.subcategories.length > 0) {
					el.subcategories.forEach(sub => {
						if (isEqual(subcategoriesRes.length, config.howManyToShow - 1, recently, parseInt(sub.id), categoryType)) {
							subcategoriesRes.push(setSubcategoryData(categoryType, sub));
						}
					});
				}
			});
		});
	}
	if (recentlyItems.length > 0) {
		recentlyItems.forEach(recently => {
			globalSearchItems.forEach(el => {
				const categoryType = el.type;
				if (el.subcategories && el.subcategories.length > 0) {
					el.subcategories.forEach(sub => {
						if (sub.items && sub.items.length > 0) {
							sub.items.forEach(item => {
								// if (!item.disabled && isEqual(itemsRes.length, config.howManyToShow, recently, parseInt(item.id), categoryType)) {
								if (isEqual(itemsRes.length, config.howManyToShow, recently, parseInt(item.id), categoryType)) {
									itemsRes.push(setItemData(item, categoryType, sub.id, true));
								}
							});
						}
					});
				} else if (el.items && el.items.length > 0) {
					el.items.forEach(item => {
						// if (!item.disabled && isEqual(itemsRes.length, config.howManyToShow, recently, parseInt(item.id), categoryType)) {
						if (isEqual(itemsRes.length, config.howManyToShow, recently, parseInt(item.id), categoryType)) {
							itemsRes.push(setItemData(item, categoryType, null, true));
						}
					});
				}
			});
		});
	}

	if (subcategoriesRes.length > 0) {
		results.push({
			type: 'subcategory',
			title: Lang('cart_recent_categories'),
			items: subcategoriesRes
		});
	}

	if (itemsRes.length > 0) {
		results.push(buildSearchData(
			'cart_recent_searches',
			itemsRes,
			Lang('cart_recent_searches')
		));
	}

	console.log('[buildRecentSearch] results', results);

	return results;
}