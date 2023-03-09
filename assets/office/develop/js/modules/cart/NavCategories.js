import "@/scss/cart/nav-categories.scss";

import navItem from "@partials/cart/navItem";
import subcategoriesBox from "@partials/cart/subcategoriesBox";
import {
	addClass, removeClass, toggleClass, copy, getFilterByValue, getCategoryTranslations,
	getIndex, sendFetch, showErrorModal, isEmptyObject, setFilterByValue
} from "@modules/Helpers";
import {openLessonModal} from "@modules/cart/ItemLessonModal";
import {closeModal} from "@modules/Modal";
import {recordRecentlyViewed} from "@modules/cart/SearchModal";
import cartGlobalVariable from "@modules/cart/GlobalVariables";
const {
	isMobile, globalUrl, cartNavCategoriesEl, cartSubcategoriesEl, cartControllerUrl
} = cartGlobalVariable;

// Public variables to get from functions
let dataFavorites = {};
let dataProducts = {};
let dataServices = {};
let dataPackages = {};

// Private variables
const activeClass = 'active';
const subLevelEl = document.querySelector('.bsapp--tab-content');

export function init() {
	const navLinkClass = '.js--nav-link';
	const articleContentClass = '.js--article-content';
	document.addEventListener('click', function (e) {
		for (let target = e.target; target && target != this; target = target.parentNode) {
			if (target.matches(navLinkClass)) {
				e.preventDefault();
				const hrefAttr = target.getAttribute('href');
				if (!hrefAttr || hrefAttr.indexOf('#') === -1) {
					return false;
				}
				openNavLink(hrefAttr.replace('#', ''));
				break;

			} else if (target.matches(articleContentClass)) {
				e.preventDefault();
				openSubcategoryContent.call(this, target, e);
				break;

			} else if (target.matches('.js--choose-subcategory')) {
				e.preventDefault();
				const typeId = target.getAttribute('data-id');
				const type = target.getAttribute('data-category-type');
				if (type) {
					const bsappSearchModalEl = document.getElementById('bsappSearchModal');
					const fromSearch = bsappSearchModalEl && bsappSearchModalEl.classList.contains('is-visible') && typeId !== null;
					if (fromSearch) {
						closeModal(bsappSearchModalEl);
						recordRecentlyViewed({
							id: +typeId,
							type,
							isSubcategory: true
						});
					}

					openNavLink(type);

					setTimeout(function () {
						const subcategoryTarget = document.querySelector(`${articleContentClass}[data-id="${typeId}"]`);
						if (subcategoryTarget) {
							openSubcategoryContent(subcategoryTarget);
						}
					}, 100);

					if (fromSearch && isMobile.matches) {
						setTimeout(function () {
							const subcategoryEl = document.querySelector(`.js--back-to-subcategory[data-id="${typeId}"]`);
							if (subcategoryEl) {
								subcategoryEl.scrollIntoView({behavior: "smooth"});
							}
						}, 500);
					}
				}

				break;

			} else if (target.matches('.js--back-to-subcategory')) {
				e.preventDefault();
				const targetParent = target.closest('.js--sub-level-return');
				const targetType = targetParent.getAttribute('data-type');
				if (!targetType) {
					return false;
				}

				if (isMobile.matches && target.classList.contains('return-to-subcategories')) {
					removeClass(subLevelEl, activeClass);
					removeClass(cartNavCategoriesEl, 'd-none');
					removeClass(subLevelEl, 'slide-in');
					addClass(subLevelEl, 'slide-out');

				} else if (!isMobile.matches) {
					removeClass(targetParent, activeClass);
					addClass(document.querySelector(`.bsapp--tab-panel[data-type="${targetType}"]`), activeClass);
				}
				break;

			} else if (target.matches('.js--favorite--star')) {
				e.preventDefault();
				toggleFavoriteItem.call(this, target, e);
				break;

			}
		}
	}, false);
}

export function getCategoryByType(type) {
	switch (type) {
		case 'favorite':
			return dataFavorites;
		case 'product':
			return dataProducts;
		case 'service':
			return dataServices;
		case 'package':
			return dataPackages;
		default:
			return null;
	}
}

export function getItemFromCategory(target) {
	const {categoryType, itemId, itemType, subcategoryId} = getAllItemInfo(target);
	// get current item from category object
	const {item} = getCurrentItemFromCategory(itemId, itemType, categoryType, subcategoryId);
	// return item for build modal
	return {item, categoryType};
}

function getCurrentItemFromCategory(itemId, itemType, categoryType, subcategoryId) {
	const data = getCategoryByType(categoryType);
	const mainObjKey = itemType === 'subcategory' || itemType === 'subItem' ? 'subcategories' : 'items';

	let mainIndex;
	let currentArray = data[mainObjKey];
	if (!currentArray) {
		throw new Error(`Current array not found in the ${itemType} object`);
	}

	if (itemType === 'subItem' && subcategoryId) {
		mainIndex = getIndex(currentArray, subcategoryId);
		// When specific item is not found
		if (mainIndex === -1) {
			return;
		}
		currentArray = currentArray[mainIndex].items;
	}

	const itemIndex = getIndex(currentArray, itemId);
	if (itemIndex === -1) {
		throw new Error(`Current ${itemId} ID not found in the ${itemType} object`);
	}

	return {
		mainIndex,
		mainObjKey,
		itemIndex,
		item: currentArray[itemIndex]
	};
}

function setFavoritesItem(props) {
	const {currentCategoryType, categoryType, subcategoryId, itemType, itemId, isFavorite} = props;
	// get current item from category object
	const {
		mainIndex, mainObjKey, item, itemIndex
	} = getCurrentItemFromCategory(itemId, itemType, categoryType, subcategoryId);
	// change value for isFavorite key for current item
	item.isFavorite = isFavorite;

	setItems({
		categoryType, mainObjKey, mainIndex, itemIndex, item, hasSubItem: itemType === 'subItem'
	});

	// rebuild favorites
	buildFavorites();
	if (currentCategoryType === 'favorite' && isFavorite === false && !ifFavoritesExists()) {
		buildCategory('favorite');
	}
}

function setItems(props) {
	const {categoryType, mainObjKey, mainIndex, itemIndex, item, hasSubItem} = props;

	if (categoryType === 'product') {
		if (hasSubItem) {
			dataProducts[mainObjKey][mainIndex].items[itemIndex] = item;
		} else {
			dataProducts[mainObjKey][itemIndex] = item;
		}
	} else if (categoryType === 'service') {
		if (hasSubItem) {
			dataServices[mainObjKey][mainIndex].items[itemIndex] = item;
		} else {
			dataServices[mainObjKey][itemIndex] = item;
		}
	} else if (categoryType === 'package') {
		if (hasSubItem) {
			dataPackages[mainObjKey][mainIndex].items[itemIndex] = item;
		} else {
			dataPackages[mainObjKey][itemIndex] = item;
		}
	}
}

export function buildAllCategoriesNav(props) {
	const {products, packages, services, lessons} = props;
	const nav = [];
	// products items
	if (products && products.length > 0) {
		dataProducts = buildSubcategory('product', products);
		if (dataProducts && !isEmptyObject(dataProducts)) {
			nav.push(buildNav(dataProducts.type, dataProducts.categoryTitle));
		}
	}
	// packages items
	if (packages && packages.length > 0) {
		dataPackages = buildSubcategory('package', packages);
		if (dataPackages && !isEmptyObject(dataPackages)) {
			nav.push(buildNav(dataPackages.type, dataPackages.categoryTitle));
		}
	}
	// services items
	if (services && services.length > 0) {
		dataServices = buildSubcategory('service', services);
		if (dataServices && !isEmptyObject(dataServices)) {
			nav.push(buildNav(dataServices.type, dataServices.categoryTitle));
		}
	}

	// build favorites after build all others categories
	buildFavorites();
	// add lesson item to navigation
	if (lessons === true) {
		nav.push(buildNav('lesson', getCategoryTranslations('lesson')));
	}

	// add favorite item to navigation
	nav.push(buildNav(dataFavorites.type, dataFavorites.categoryTitle));
	// compile navigation
	console.log(nav)
	cartNavCategoriesEl.innerHTML = navItem(nav);
	const haveFavorites = ifFavoritesExists();
	navOrderPosition(haveFavorites);
	buildCategory(haveFavorites ? 'favorite' : nav[0].type);
}

export function userAccountNav(){
	let nav = []
	nav.push({type: '#user-account', categoryTitle: '123'});
	nav.push({type: '#user-account', categoryTitle: '456'});
	cartNavCategoriesEl.innerHTML = navItem(nav);
}
export function getAllItemInfo(target) {
	const articleEl = target.closest('.article');
	const articleContentEl = target.closest('.js--article-content');
	const id = articleContentEl.getAttribute('data-id');
	if (!id) {
		throw new Error('[toggleFavoriteItem] Invalid item ID');
	}

	const categoryType = articleContentEl.getAttribute('data-category-type');
	const itemCategoryType = articleEl.getAttribute('data-type');
	const isInFavoriteNow = itemCategoryType === 'favorite';
	const mainAttrEl = isInFavoriteNow ? articleContentEl : articleEl;
	const itemType = mainAttrEl.getAttribute('data-current-type');

	return {
		categoryType: isInFavoriteNow && categoryType ? categoryType : itemCategoryType,
		currentCategoryType: itemCategoryType,
		itemId: id,
		itemType,
		subcategoryId: itemType === 'subItem' ? mainAttrEl.getAttribute('data-subcategory-id') : null
	};
}

function buildNav(type, categoryTitle, link) {
	return {
		type,
		categoryTitle,
		link
	};
}

export function buildCategory(type = null) {
	const currentData = getCategoryByType(type);
	if (!currentData) {
		return false;
	}
	// compile category
	cartSubcategoriesEl.innerHTML = subcategoriesBox(currentData);
	toggleActiveNav(type);
}

export function toggleActiveNav(type, onlyNavChange = false) {
	removeClass(document.querySelectorAll('.nav-link'), activeClass);
	const target = document.querySelector(`.js--nav-link[href="#${type}"]`);
	if (target) {
		addClass(target, activeClass);
	}
	if (onlyNavChange) {
		return false;
	}
	const showEl = isMobile.matches ? '.cart--subcategories-mob' : '.cart--subcategories-desk';
	const el = document.querySelector(`${showEl}[data-type="${type}"]`);
	if (el) {
		addClass(el, activeClass);
	}
}

function buildSubcategory(type, array) {
	const arrayKey = array.find(el => el.hasOwnProperty('price')) ? 'items' : 'subcategories';
	return {
		type,
		categoryTitle: getCategoryTranslations(type),
		[arrayKey]: array
	};
}

function buildFavorites() {
	// set favorites
	dataFavorites = {
		type: 'favorite',
		categoryTitle: getCategoryTranslations('favorite'),
		items: []
	};

	if (dataProducts && !isEmptyObject(dataProducts)) {
		buildFavoritesByType(dataProducts);
	}
	if (dataPackages && !isEmptyObject(dataPackages)) {
		buildFavoritesByType(dataPackages);
	}
	if (dataServices && !isEmptyObject(dataServices)) {
		buildFavoritesByType(dataServices);
	}

	const haveFavorites = ifFavoritesExists();
	if (!haveFavorites) {
		dataFavorites.title = getCategoryTranslations('noFavorites');
		dataFavorites.text = getCategoryTranslations('favoritesText');
	}
	navOrderPosition(haveFavorites);
}

function buildFavoritesByType(category) {
	const type = category.type;
	const arrayKey = category.hasOwnProperty('subcategories') ? 'subcategories' : 'items';
	// copy category data to avoid duplicate keys
	const newArray = copy(category[arrayKey]);
	dataFavorites.items = buildFavoritesItem({
		type,
		array: newArray,
		parentType: category.hasOwnProperty('subcategories') ? 'subcategory' : 'item'
	});
	newArray.forEach(el => {
		if (!el.hasOwnProperty('items')) {
			return;
		}
		if (el.items && el.items.length > 0) {
			dataFavorites.items = buildFavoritesItem({
				type,
				array: el.items,
				parentType: 'subItem',
				parentTitle: el.name,
				subcategoryId: el.id
			});
		}
	});
}

function buildFavoritesItem(props) {
	const {
		type,
		array,
		parentTitle,
		parentType,
		subcategoryId
	} = props;

	const favoritesItems = getFilterByValue(array, 'isFavorite', true);
	if (favoritesItems && favoritesItems.length > 0) {
		favoritesItems.forEach(el => {
			el.showChooseBtn = !el.price;
			el.categoryType = type;
			el.currentType = parentType;
			el.parentTitle = parentTitle ? parentTitle : getCategoryTranslations(type);
			if (subcategoryId) {
				el.subcategoryId = subcategoryId;
			}
		});
		return dataFavorites.items.concat(favoritesItems);
	}
	return dataFavorites.items;
}

function ifFavoritesExists() {
	return dataFavorites && dataFavorites.items.length > 0;
}

function toggleFavoriteItem(target, e) {
	const articleEl = target.closest('.article');
	const {
		categoryType, currentCategoryType, itemId, itemType, subcategoryId
	} = getAllItemInfo(target);

	const isAdded = target.classList.contains(activeClass);
	// create post to add/remove from favorite items
	sendFetch(
		cartControllerUrl,
		{
			action: isAdded ? 'itemFavoriteRemove' : 'itemFavoriteAdd',
			itemCategoryType: categoryType,
			itemType,
			itemId
		}
	).then(function (response) {
		if (!response.success) {
			showErrorModal({
				error: response.message
			});
			return false;
		}

		// then rebuild favorite items
		setFavoritesItem({
			currentCategoryType,
			categoryType,
			subcategoryId,
			itemType,
			itemId,
			isFavorite: !isAdded
		});

		// toggle class for the start icon
		toggleClass(target, activeClass);
		const starEl = target.querySelector('.fa-star-icon');
		starEl.setAttribute('data-prefix', !isAdded ? 'fas' : 'fal');
		if (!isAdded) {
			starEl.classList.remove('fa-light');
			starEl.classList.add('fa-solid');
		} else {
			starEl.classList.add('fa-light');
			starEl.classList.remove('fa-solid');
		}

		if (currentCategoryType === 'favorite' && isAdded) {
			// remove current article from favorites DOM
			articleEl.remove();
		}
	});
}

function navOrderPosition(toFirst = false) {
	const el = cartNavCategoriesEl.querySelector('.nav-item[data-type="favorite"]');
	if (!el) {
		return false;
	}
	if (toFirst) {
		addClass(el, 'to-first');
	} else {
		removeClass(el, 'to-first');
	}
}

function openNavLink(type) {
	// if lesson, open a Calendar popup with request
	if (type === 'lesson') {
		if (!isMobile.matches) {
			toggleActiveNav(type);
		}
		openLessonModal();
		return false;
	}

	buildCategory(type);

	if (isMobile.matches) {
		removeClass(subLevelEl, 'slide-out');
		addClass(subLevelEl, 'slide-in');
		setTimeout(function () {
			addClass(subLevelEl, activeClass);
			addClass(cartNavCategoriesEl, 'd-none');
		}, 500);
	}
}

function openSubcategoryContent(target, e) {
	const targetParent = target.closest('.article');
	const id = targetParent.getAttribute('data-id');
	if (!id) {
		return false;
	}
	addClass(document.getElementById(id), activeClass);
	removeClass(targetParent.parentNode, activeClass);
}

export function disabledCategoryItem(toSendItem, isDisabled = false) {
	const {id, type} = toSendItem;
	let data = getCategoryByType(type);
	if (!data || isEmptyObject(data)) {
		return false;
	}

	let {
		subIndex,
		itemIndex,
		dataItems
	} = getDisabledItem(data, id);

	if (dataItems && dataItems.length > 0 && itemIndex > -1) {
		const newData = setFilterByValue(dataItems, 'id', id, 'disabled', isDisabled);
		const subcategoryExists = data.subcategories && data.subcategories.length > 0;
		setItems({
			categoryType: type,
			mainObjKey: subcategoryExists ? 'subcategories' : 'items',
			mainIndex: subIndex,
			itemIndex,
			item: newData[itemIndex],
			hasSubItem: subcategoryExists
		});
	}

	const articleElms = document.querySelectorAll(`.article--item-${id}`);
	if (articleElms.length > 0) {
		articleElms.forEach(el => {
			if (isDisabled) {
				el.setAttribute('disabled', 'disabled');
			} else {
				el.removeAttribute('disabled');
			}
		});

		const favoriteData = getCategoryByType('favorite');
		if (!isEmptyObject(favoriteData) && favoriteData.items.length > 0) {
			if (getIndex(favoriteData.items, id) > -1) {
				// rebuild favorite items
				buildFavorites();
			}
		}
	}
}

function getDisabledItem(data, mainId) {
	let dataItems = data.items;
	let subCategory;
	if (data.subcategories && data.subcategories.length > 0) {
		subCategory = data.subcategories.find((sub, index) => {
			return getIndex(sub.items, mainId) > -1;
		});
		dataItems = subCategory && subCategory.items ? subCategory.items : null;
	}

	return {
		subIndex: data.subcategories && data.subcategories.length > 0 && subCategory ? getIndex(data.subcategories, subCategory.id) : null,
		itemIndex: getIndex(dataItems, mainId),
		dataItems
	};
}