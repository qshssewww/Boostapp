/**
 * @Version: 1.0
 * Usage:
 *     $('select').bsappMultiSelect();
 *     $('select[multiple]').bsappMultiSelect('open'); // open dropdown
 *     $('select[multiple]').bsappMultiSelect('destroy'); // destroy plugin
 *     $('select[multiple]').bsappMultiSelect('reload'); // reload plugin
 *     $('select[multiple]').bsappMultiSelect('values'); // get all values, but skip others if 'allContainValue' is selected
 *     $('select[multiple]').bsappMultiSelect({
 *     		class: 'my-class',
 *     		searchPlaceholder: 'Searching',
 *     		relatedSelect: $('jQuery selector'),
 *          onCheckboxClick: function(e, select) {
 *				console.log('after checkbox click', e, select);
 *		    }
 *     });
 *
 **/
(function ($) {
	const defaults = {
		class: '', // custom class for the .bsapp--sel element
		label: '', // provide a placeholder label if the select does not have an optgroup
		allContainValue: '_all', // specify the ALL value for the first option to identify that parameter in the current options group
		iconArrow: "", // unicode glyph
		search: true, // include option search input
		searchPlaceholder: typeof lang === 'function' ? lang('search_user_list') : 'Search', // search input placeholder text
		searchShowOptGroups: false, // show option group titles if no options remaining
		container: $('body'), // will display the dropdown above the container if there is not enough space below the container
		relatedSelect: null, // jQuery selectors that related to current select and need to disable an option selected in the current select
		groupSizeLimit: 3,
		/** CALLBACKS **/
		onLoad: function(element) {}, // fires at the end of initialization
		onAfterOpen: function(element) {}, // fires at the start of opening the dropdown
		onAfterClose: function(element) {}, // fires when dropdown is closed
		onCheckboxClick: function(element, option) {}, // fires when a checkbox is clicked
	};

	var bsappCounter = 1;

	function BsappMultiSelect(element, options) {
		const settings = $.extend({}, defaults, options);
		const classes = {
			toTop: 'to-top',
			active: 'active',
			selected: 'selected',
			multi: 'multi',
			multiLabel: 'multi-label',
			disabled: 'disabled',
			selInitial: 'bsapp--sel',
			selBox: 'bsapp--sel__box',
			selBoxOptions: 'bsapp--sel__options',
			selTag: 'bsapp--sel__tag',
			selSearch: 'bsapp--sel__search',
			selSearchDelete: 'bsapp--sel__delete',
			selPlaceholder: 'bsapp--sel__placeholder',
			selOptgroup: 'bsapp--sel__optgroup',
			typeCheckbox: 'input[type="checkbox"]'
		};

		this.element = element;
		this.settings = settings;
		this.classes = classes;
		this.init(options);
	}

	BsappMultiSelect.prototype = {
		init: function (objParameter = {}) {
			const instance = this;

			for (const key in objParameter) {
				instance.settings[key] = objParameter[key];
			}

			/** Make sure this is a select list and not loaded **/
			if (instance.element.nodeName.toLowerCase() !== 'select' || $(instance.element).hasClass(instance.classes.active) || $(instance.element).attr('multiple') === undefined) {
				return true;
			}

			instance._build();
			instance._events();
		},
		/** RESET the DOM **/
		destroy : function() {
			const instance = this;
			$(instance.element).siblings().remove();
			$(instance.element).unwrap();
			$(instance.element).css('display','').removeClass(instance.classes.active);
		},
		/** RELOAD multiselect list **/
		reload: function() {
			// remove existing options
			this.destroy();
			// load element
			this.init();
		},
		/** Open dropdown **/
		open: function() {
			const instance = this;
			const $el = $(instance.element).closest('.' + instance.classes.selInitial);
			instance._open($el);
		},
		/** Get all select values, but skip others if 'allContainValue' is selected **/
		values: function() {
			const instance = this;
			if (instance.element.nodeName.toLowerCase() !== 'select' || !$(instance.element).hasClass(instance.classes.active) || $(instance.element).attr('multiple') === undefined) {
				return true;
			}
			return instance._getValues();
		},
		/** PRIVATE FUNCTIONS **/
		_events: function () {
			const instance = this;
			const settings = instance.settings;
			const selClasses = instance.classes;
			const selClass = '.' + selClasses.selInitial;
			const selBoxClass = '.' + selClasses.selBox;
			const $selInitialEl = $(instance.element).closest(selClass);

			/** Toggling the `.active` state on the `.bsapp--sel`. **/
			$selInitialEl.on('click', '.' + selClasses.selPlaceholder, function(e) {
				e.preventDefault();
				const $el = $(this).closest(selClass);
				instance._open($el);
			});
			/** BIND SELECT ACTION **/
			$selInitialEl.on('change', selClasses.typeCheckbox, function(e){
				e.preventDefault();
				const input = $(this);
				const inputVal = input.val();
				const li = input.closest('.' + selClasses.selBoxOptions);
				if (li.length === 0) {
					return false;
				}

				// toggle clicked option
				const select = $(instance.element);
				const allContainValue = settings.allContainValue;
				if (inputVal.indexOf(allContainValue) > -1) {
					const allList = li.siblings().filter(function(){
						return ($(this).find(selClasses.typeCheckbox).prop('checked') !== input.prop('checked'))
					});
					// mark ALL unchecked checkboxes in this option group
					instance._toggleAllList(allList, input.prop('checked'));
				} else {
					instance._checkMultiCheckbox(li, input);
					instance._checkAllCheckbox(li);
				}

				li.toggleClass(selClasses.selected);
				select.find('option[value="'+ inputVal +'"]').prop('selected', input.prop('checked'));
				select.trigger('change');
				instance._updatePlaceholderText();

				if (settings.relatedSelect !== null && select.find('optgroup').length > 0) {
					instance._changeRelatedEl(select.find('option[value="'+ inputVal +'"]'));
				}

				// USER CALLBACK
				if (typeof settings.onCheckboxClick === 'function') {
					settings.onCheckboxClick(input, select);
				}
			});
			/** Toggling optgroup label **/
			$selInitialEl.on('click', '.' + selClasses.selOptgroup + ' .label', function(e) {
				e.preventDefault();
				$(this).parent().toggleClass(selClasses.active).find('ul').slideToggle('fast');
			});
			/** Searching by option **/
			$selInitialEl.on('keyup', '.' + selClasses.selSearch + ' > input', function(e) {
				e.preventDefault();
				const $input = $(this);
				const searchVal = $input.val();
				const deleteBtn = $input.next('.' + selClasses.selSearchDelete);
				// ignore keystrokes that don't make a difference
				if ($input.attr('data-last-search') == searchVal) {
					return true;
				}
				$input.attr('data-last-search', searchVal);

				if (searchVal.length === 0) {
					deleteBtn.fadeOut('fast');
				} else {
					deleteBtn.fadeIn('fast');
				}

				instance._searchByList($input);
			});
			/** Remove searching text **/
			$selInitialEl.on('click', '.' + selClasses.selSearchDelete, function(e) {
				e.preventDefault();
				const $input = $(this).prev('input');
				$input.val('');
				instance._searchByList($input);
				$(this).fadeOut('fast');
			});
			/** Close dropdown by clicking outside **/
			document.addEventListener("click", e => {
				const trigger = document.querySelector('.' + selClasses.selInitial + '.' + selClasses.active);
				if (trigger !== null && !trigger.contains(e.target)) {
					$('body').find(selClass).removeClass(selClasses.active).find(selBoxClass).css('display', 'none').removeClass(selClasses.toTop);
					return false;
				}
			});
		},
		_open: function ($el) {
			const instance = this;
			const settings = instance.settings;
			const selClasses = instance.classes;
			const selClass = '.' + selClasses.selInitial;
			const selBoxClass = '.' + selClasses.selBox;

			const $elBox = $el.find(selBoxClass);
			if ($(selClass).length > 1) {
				$(selClass).not($el[0]).removeClass(selClasses.active).find(selBoxClass).css('display', 'none');
			}

			const $dropdownParent = settings.container;
			const elPosition = $el.offset();
			const top = elPosition.top + $el.innerHeight();
			if (top + $elBox.height() > $dropdownParent.offset().top + $dropdownParent.innerHeight()) {
				$elBox.addClass(selClasses.toTop);
			}

			$el.toggleClass(selClasses.active);
			if ($el.hasClass(selClasses.active)) {
				// USER CALLBACK
				if (typeof settings.onAfterOpen === 'function') {
					settings.onAfterOpen($el);
				}

				$elBox.slideDown('fast');
			} else {
				$elBox.css('display', 'none').removeClass(selClasses.toTop);

				// USER CALLBACK
				if (typeof settings.onAfterClose === 'function') {
					settings.onAfterClose($el);
				}
			}
		},
		_build: function() {
			const instance = this;
			const settings = instance.settings;
			const selClasses = instance.classes;
			const $select = $(instance.element);

			$select.css('display', 'none').addClass(selClasses.active);
			$select.wrap(function() {
				return '<div class="' + selClasses.selInitial + '"></div>';
			});

			const $selectParent = $select.closest('.' + selClasses.selInitial);
			const selectedIndex = $select.find('option:selected').length > 0 ? $select.find('option:selected').index() : null;
			let placeholderHtml = '';
			if (selectedIndex !== null) {
				const selectedOption = $select.find('option').eq(selectedIndex);
				placeholderHtml = '<li'
					+ (!selectedOption.prop('disabled') ? ' class="' + selClasses.selBoxOptions + '"' : '')
					+ '>' + selectedOption.text() + '</li>';
			}

			$selectParent.prepend($('<div>', {
				class: $selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selBox)
			}));

			if (settings.search && $selectParent.find('.' + selClasses.selSearch).length === 0) {
				$selectParent.find('.' + selClasses.selBox).prepend($('<div>', {
					class: $selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selSearch),
					html: '<input type="text" value="" placeholder="' + settings.searchPlaceholder + '" />' +
						'<button class="' + selClasses.selSearchDelete + '" type="button" aria-label="delete searching"><i class="fal fa-times-circle"></i></button>'
				}));
			}

			$selectParent.prepend($('<ul>', {
				class: $selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selPlaceholder),
				'data-arrow-icon': settings.iconArrow,
				html: placeholderHtml
			}));

			// add options to wrapper
			let options = [];
			$select.children().each(function(){
				if(this.nodeName.toLowerCase() === 'optgroup' ) {
					if ($(this).prop('hidden')) {
						return true;
					}
					const groupOptions = [];
					$(this).children('option').each(function() {
						groupOptions.push(instance._setOptions($(this)));
					});

					options.push({
						label: $(this).attr('label'),
						options: groupOptions
					});

				} else if (this.nodeName.toLowerCase() === 'option') {
					options.push(instance._setOptions($(this)));

				} else {
					return true;
				}
			});

			instance._loadOptions(options, $selectParent);

			// USER CALLBACK
			if (typeof settings.onLoad === 'function') {
				settings.onLoad($selectParent);
			}
		},
		_loadOptions: function(options, $selectParent) {
			const instance = this;
			const selClasses = instance.classes;
			const optionsList = $selectParent.find('.' + selClasses.selBox);
			optionsList.append('<ul></ul>');

			for (let key in options) {
				const thisOption = options[key];
				if (thisOption.hidden) {
					continue;
				}
				const container  = $('<li></li>');

				// optgroup
				if (thisOption.hasOwnProperty('options')) {
					container.addClass($selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selOptgroup));
					container.append('<span class="label">'+ thisOption.label +'</span>');
					container.append('<ul></ul>');

					for (let gKey in thisOption.options) {
						const thisGOption = thisOption.options[gKey];
						if (thisGOption.hidden) {
							continue;
						}
						const gContainer  = $('<li></li>').addClass($selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selBoxOptions));

						instance._addOption(gContainer, thisGOption, key);
						container.find('> ul').append(gContainer);
					}

				} else if (thisOption.hasOwnProperty('value')) {
					container.addClass($selectParent.attr('class').replace(/bsapp--sel/g, selClasses.selBoxOptions))
					instance._addOption(container, thisOption, key);
				}

				optionsList.find('> ul').append(container);
			}

			if (instance.settings.class !== '') {
				$selectParent.addClass(instance.settings.class);
			}
			instance._updatePlaceholderText();
		},
		_toggleAllList: function(lists, prop) {
			const instance = this;
			lists.each(function() {
				const li = $(this);
				const liCheckbox = li.find(instance.classes.typeCheckbox);
				prop ? li.addClass(instance.classes.selected) : li.removeClass(instance.classes.selected);
				liCheckbox.prop('checked', prop);
				$(instance.element).find('option[value="' + liCheckbox.val() + '"]').prop('selected', prop);
			});
		},
		_setOptions: function(el) {
			let newTitle = el.text();
			const isMulti = el.attr('data-multi') !== undefined ? el.attr('data-multi') : false;
			let currMainMultiEl;
			if (!!isMulti) {
				currMainMultiEl = el.parent().find('option[value="' + isMulti + '"]');
				if (newTitle.indexOf(currMainMultiEl.text()) > -1) {
					newTitle = newTitle.replace(currMainMultiEl.text(), '').replace(')', '').replace('(', '');
				}
			}
			return {
				value: el.val(),
				checked: el.prop('selected'),
				hidden: !!isMulti && currMainMultiEl.prop('hidden') ? currMainMultiEl.prop('hidden') : el.prop('hidden'),
				disabled: el.prop('disabled'),
				searchTitle: el.text(),
				title: newTitle,
				isMulti: isMulti,
				isMultiLabel: el.attr('data-multi-label') !== undefined ? el.attr('data-multi-label') : false
			};
		},
		_addOption: function(container, option, key) {
			container.text(option.title);

			if (!!option.isMultiLabel) {
				container.addClass(this.classes.multiLabel).attr('data-multi-count', $(this.element).find('option[data-multi="' + option.value + '"]').length);
			}

			if (option.disabled) {
				container.addClass(this.classes.disabled).attr('data-value', option.value);
				if (option.isMultiLabel === false) { return false; }
			}

			const label = $('<label></label>').attr('for', 'sel__inp-' + bsappCounter + '-' + key + '-' + option.value);
			container.wrapInner(label);
			container.prepend(
				$('<input type="checkbox" value="" title="" />')
					.val(option.value)
					.attr('name', 'checkbox--' + ($(this.element).attr('name') !== undefined && $(this.element).attr('name') !== '' ? $(this.element).attr('name') : bsappCounter))
					.attr('title', option.searchTitle)
					.attr('disabled', option.disabled)
					.attr('id', 'sel__inp-' + bsappCounter + '-' + key + '-' + option.value)
			);

			bsappCounter = bsappCounter + 1;
			container.find(this.classes.typeCheckbox).prop('checked', option.checked);

			if (option.checked) {
				container.addClass('default');
				container.addClass(this.classes.selected);
			}

			if (!!option.isMulti) {
				container.find(this.classes.typeCheckbox).attr('multi-value', option.isMulti);
				container.addClass(this.classes.multi);
			}
		},
		_updatePlaceholderText: function(){
			const instance = this;
			const allContainValue = instance.settings.allContainValue;
			const $select = $(instance.element);
			const $currentSel = $select.closest('.' + instance.classes.selInitial);
			const $currentPlaceholder = $currentSel.find('.' + instance.classes.selPlaceholder);

			// get selected options
			$currentPlaceholder.empty();

			const selOpts = [];
			let groupOpts = 0;
			let ifDiffGroup = [];
			$select.children().each(function() {
				const el = this;
				if (el.nodeName.toLowerCase() === 'optgroup' ) {
					$(el).children('option:selected').each(function() {
						const $opt = $(this);
						if ($opt.prop('hidden')) {
							return true;
						}
						if (instance._checkMultiOption($opt) > 0) {
							return true;
						}
						if (ifDiffGroup.indexOf($(el).attr('label')) === -1) {
							ifDiffGroup.push($(el).attr('label'));
						}

						groupOpts++;
						if ($opt.val().indexOf(allContainValue) > -1) {
							return false;
						}
					});
				} else if (el.nodeName.toLowerCase() === 'option' && $(el).is(':selected')) {
					if (instance._checkMultiOption($(el)) > 0) {
						return true;
					}
					groupOpts++;
					if ($(el).val().indexOf(allContainValue) > -1) {
						return false;
					}
				}
			});

			const isGroupOpts = groupOpts > instance.settings.groupSizeLimit || ifDiffGroup.length > 1;
			let countOpt = 0;
			$select.children().each(function() {
				const el = this;
				if (el.nodeName.toLowerCase() === 'optgroup' ) {
					let countGroup = 0;

					$(el).children('option:selected').each(function() {
						const retGroup = instance._setSelOpts(this, selOpts, isGroupOpts, countGroup);
						if (retGroup === true || retGroup === false) {
							return retGroup;
						}
						countGroup = retGroup;
					});

					if (isGroupOpts && countGroup !== 0) {
						selOpts.push($(el).attr('label') + ": " + countGroup);
					}

				} else if (el.nodeName.toLowerCase() === 'option' && $(el).is(':selected')) {
					const ret = instance._setSelOpts(el, selOpts, isGroupOpts, countOpt);
					if (ret === true || ret === false) {
						return ret;
					}
					countOpt = ret;
				}
			});

			if (isGroupOpts && countOpt !== 0) {
				selOpts.push( lang('were_selected') + instance.settings.label + ": " + countOpt );
			}

			/** UPDATE PLACEHOLDER TEXT WITH OPTIONS SELECTED **/
			for (let i = 0; i < selOpts.length; i++) {
				const li  = $('<li></li>').addClass($currentSel.attr('class').replace(/bsapp--sel/g, instance.classes.selTag));
				li.text(selOpts[i]);
				$currentPlaceholder.append(li);
			}
		},
		_setSelOpts: function(el, selOpts, isGroup, count) {
			const instance = this;
			const allContainValue = instance.settings.allContainValue;
			const elText = $(el).text();
			if ($(el).val().indexOf(allContainValue) > -1) {
				selOpts.push(elText);
				return false;
			}
			if (instance._checkMultiOption($(el)) > 0) {
				return true;
			}
			if (isGroup) {
				count++;
			} else {
				selOpts.push(elText);
			}
			return count;
		},
		_checkMultiOption: function($el) {
			const optMulti = $el.siblings().filter(function(){
				return ($(this).attr('data-multi') === $el.val())
			});
			return optMulti.length;
		},
		_checkMultiCheckbox: function(li, input) {
			const instance = this;
			const isChecked = input.prop('checked');
			const liParent = li.parent();
			const multiList = li.siblings().filter(function () {
				return ($(this).find(instance.classes.typeCheckbox).length > 0 && $(this).find(instance.classes.typeCheckbox).attr('multi-value') === input.val())
			});

			if (multiList.length > 0) {
				instance._toggleAllList(multiList, isChecked);
			} else if (input.attr('multi-value') !== undefined && input.attr('multi-value') !== '') {
				const multiMain = li.siblings().find(instance.classes.typeCheckbox + '[value="' + input.attr('multi-value') + '"]');
				const multiAll = parseInt(multiMain.parent().attr('data-multi-count')) || liParent.find(instance.classes.typeCheckbox + '[multi-value="' + input.attr('multi-value') + '"]').length;
				const currentlyMultiSelected = liParent.find('li').filter(function () {
					const inp = $(this).find(instance.classes.typeCheckbox);
					return (inp.attr('multi-value') !== undefined && inp.attr('multi-value') === input.attr('multi-value') && inp.prop('checked') === isChecked)
				});
				const isMultiMainToggle = isChecked ? currentlyMultiSelected.length === multiAll : currentlyMultiSelected.length <= multiAll;
				if (multiMain.length > 0 && isMultiMainToggle) {
					instance._toggleAllList(multiMain.parent(), isChecked);
				}
			}
		},
		_findAllCheckbox: function(li) {
			const instance = this;
			const allContainValue = instance.settings.allContainValue;
			return li.siblings().filter(function(){
				return ($(this).find(instance.classes.typeCheckbox).length > 0 && $(this).find(instance.classes.typeCheckbox).val().indexOf(allContainValue) > -1)
			});
		},
		_checkAllCheckbox: function(li) {
			const instance = this;
			const liParent = li.parent();
			const allContainValue = instance.settings.allContainValue;
			const firstLi = instance._findAllCheckbox(li);
			const firstLiCheckbox = firstLi.find(instance.classes.typeCheckbox);
			if (firstLiCheckbox.length > 0) {
				if (firstLiCheckbox.prop('checked')) {
					// unmark first ALL checkbox in this option group
					instance._toggleAllList(firstLi, false);
				} else {
					const currentlySelected = liParent.find('li').filter(function () {
						const inp = $(this).find(instance.classes.typeCheckbox);
						return (inp.val() !== undefined && inp.val().indexOf(allContainValue) === -1 && inp.prop('checked') && !$(this).hasClass(instance.classes.multiLabel))
					});
					const liParentDisabled = liParent.find('li.' + instance.classes.disabled + ':not(.' + instance.classes.multiLabel + ')').length || 0;
					const allList = liParent.find('li').length - liParent.find('li.' + instance.classes.multiLabel).length - liParentDisabled - firstLiCheckbox.length;
					if (currentlySelected.length > 0 && allList === currentlySelected.length) {
						// mark the first ALL unchecked checkbox in this option group, if the last checkbox is now checked
						instance._toggleAllList(firstLi, true);
					}
				}
			}
		},
		_searchByList: function($input) {
			const instance = this;
			const settings = instance.settings;
			const selClasses = instance.classes;
			const selBoxClass = '.' + selClasses.selBox;
			const searchVal = $input.val();

			// search non optgroup li's
			$(instance.element).closest('.' + selClasses.selInitial).find(selBoxClass).find('li:not(.' + selClasses.selOptgroup + ')').each(function() {
				const el = $(this);
				const elCheckbox = el.find(selClasses.typeCheckbox);
				const optText = elCheckbox.attr('title') !== undefined && elCheckbox.attr('title') !== '' ? elCheckbox.attr('title') : el.text();

				let multiValueEl = null;
				if (el.hasClass(selClasses.multi)) {
					multiValueEl = el.parent().find(selClasses.typeCheckbox + '[value="' + elCheckbox.attr('multi-value') + '"]').parent();
				}

				// show option if string exists
				if (optText.toLowerCase().indexOf(searchVal.toLowerCase()) > -1) {
					el.show();
					if (multiValueEl !== null && searchVal.length !== 0) {
						multiValueEl.addClass(selClasses.disabled).show().find(selClasses.typeCheckbox).prop('disabled', true);
					}
				} else { // } else if (!el.hasClass(selClasses.selected)) { // don't hide selected items
					el.hide();
				}

				// hide / show optGroups depending on if options within it are visible
				const $liOptgroup = el.closest('li.' + selClasses.selOptgroup);
				if ($liOptgroup.hasClass(selClasses.active) && $liOptgroup.find('> ul:visible').length === 0) {
					$liOptgroup.find('> ul').show();
				}
				if ($liOptgroup.length > 0) {
					$liOptgroup.show().find('.label').hide();
					if ($liOptgroup.find('li:visible').length) {
						$liOptgroup.show();
						if (settings.searchShowOptGroups) {
							$liOptgroup.find('.label').show();
						}
					} else {
						$liOptgroup.hide();
						if (settings.searchShowOptGroups) {
							$liOptgroup.find('.label').hide();
						}
					}
				}

				if (searchVal.length === 0) {
					if (multiValueEl !== null) {
						multiValueEl.removeClass(selClasses.disabled).show().find(selClasses.typeCheckbox).removeAttr('disabled');
					}
					$(selBoxClass).find('.' + selClasses.selOptgroup).find('.label').show();
					$(selBoxClass).find('.' + selClasses.active + '.' + selClasses.selOptgroup).find('> ul').hide();
				}
			});
		},
		_changeRelatedEl: function(currentEl) {
			const instance = this;
			const $relatedEls = instance.settings.relatedSelect.not(instance.element);
			const allContainValue = instance.settings.allContainValue;
			const isDisabled = currentEl.prop('selected');
			const thisOptionValue = currentEl.attr('value');
			const isMulti = currentEl.attr('data-multi') !== undefined && currentEl.attr('data-multi') !== '' ? currentEl.attr('data-multi') : false;

			if ($relatedEls.length === 0) {
				console.error('[bsappMultiSelect] relatedSelect option error: Not found.');
				return false;
			}

			for (let i = 0; i < $relatedEls.length; i++) {
				const $related = $($relatedEls[i]);

				/** Make sure this is a select list and init **/
				if ($relatedEls[i].nodeName.toLowerCase() !== 'select' || !$related.hasClass(instance.classes.active) || $related.attr('multiple') === undefined) {
					$related.find('option[value="' + thisOptionValue + '"]').prop('hidden', isDisabled);
					console.error('[bsappMultiSelect] relatedSelect option error: Make sure that related element is a multiple select and has already been initialized in the plugin.');
					continue;
				}

				const currentOption = $related.find('option[value="' + thisOptionValue + '"]');
				if (currentOption.prop('hidden') !== isDisabled) {
					currentOption.prop('hidden', isDisabled);
				}
				const currentGrOption = currentOption.closest('optgroup');

				if (thisOptionValue.indexOf(allContainValue) > -1) {
					currentGrOption.prop('hidden', isDisabled);
					currentGrOption.children('option').each(function() {
						$(this).prop('hidden', isDisabled);
						if (isDisabled) {
							$(this).prop('selected', false);
						}
					});
				} else {

					if (!!isMulti) {
						const multiMain = currentGrOption.children('option[value="' + isMulti + '"]');
						const multiLength = currentGrOption.children('option[data-multi="' + isMulti + '"]').length;
						const multiList = currentGrOption.children('option[data-multi="' + isMulti + '"]').filter(function () {
							return $(this).prop('selected') !== isDisabled && $(this).val() !== thisOptionValue && $(this).prop('hidden') !== isDisabled;
						});
						const multiHidden = currentGrOption.children('option[data-multi="' + isMulti + '"][hidden]').length;
						if (multiList.length === 0 && multiHidden === multiLength) {
							multiMain.prop('hidden', true).prop('disabled', false);
						} else {
							multiMain.prop('hidden', false);
							if (multiHidden === 0) {
								multiMain.prop('disabled', false);
							} else {
								multiMain.prop('disabled', multiLength !== multiList.length);
							}
						}
					} else if (currentOption.attr('data-multi-label') !== undefined) {
						currentGrOption.children('option[data-multi="' + currentOption.attr('data-multi-label') + '"]').each(function () {
							$(this).prop('hidden', isDisabled);
							if (isDisabled) {
								$(this).prop('selected', false);
							}
						});
					}

					const hiddenOptions = currentGrOption.children('option').filter(function () {
						const opt = $(this);
						return (opt.val() !== '' && opt.val().indexOf(allContainValue) === -1 && opt.prop('hidden') && !$(this).hasClass(instance.classes.multiLabel))
					});
					const firstAllOpt = currentGrOption.children('option').filter(function () {
						return ($(this).val() !== '' && $(this).val().indexOf(allContainValue) > -1)
					});

					const hiddenLength = hiddenOptions.length + (hiddenOptions.length === 0 && firstAllOpt.prop('hidden') ? 0 : firstAllOpt.length);
					if (hiddenLength === currentGrOption.children('option').length) {
						currentGrOption.prop('hidden', isDisabled);
						currentGrOption.children('option').each(function() {
							$(this).prop('hidden', isDisabled);
							if (isDisabled) {
								$(this).prop('selected', false);
							}
						});
					} else {
						currentGrOption.prop('hidden', false);
						if (firstAllOpt.length > 0) {
							firstAllOpt.prop('selected', false).prop('hidden', hiddenLength !== 0);
						}
					}

				}
				if (currentOption.prop('selected') !== false) {
					currentOption.prop('selected', false);
				}
				$related.bsappMultiSelect('reload');
			}
		},
		_getValues: function() {
			const instance = this;
			const allContainValue = instance.settings.allContainValue;
			const values = [];
			$(instance.element).children().each(function() {
				const el = this;
				if (el.nodeName.toLowerCase() === 'optgroup' ) {
					$(el).children('option:selected').each(function() {
						const $el = $(this)
						if ($el.prop('hidden') || $el.attr('data-multi-label') !== undefined) {
							return true;
						}
						if ($el.val().indexOf(allContainValue) > -1) {
							values.push($el.val());
							return false;
						}
						values.push($el.val());
					});
				} else if (el.nodeName.toLowerCase() === 'option' && $(el).is(':selected')) {
					if ($(el).prop('hidden') || $(el).attr('data-multi-label') !== undefined) {
						return true;
					}
					if ($(el).val().indexOf(allContainValue) > -1) {
						values.push($(el).val());
						return false;
					}
					values.push($(el).val());
				}
			});
			return values;
		}
	};

	$.fn.bsappMultiSelect = function(options) {
		const args = arguments;
		let ret;

		if ((options === undefined) || (typeof options === 'object')) {
			return this.each(function() {
				if (!$.data(this, 'bsapp_multiselect')) {
					$.data(this, 'bsapp_multiselect', new BsappMultiSelect(this, options));
				}
			});
		} else if ((typeof options === 'string') && (options[0] !== '_') && (options !== 'init')) {
			this.each(function() {
				const instance = $.data(this, 'bsapp_multiselect');

				if (instance instanceof BsappMultiSelect && typeof instance[options] === 'function') {
					ret = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
				}

				if (options === 'destroy') {
					$.data(this, 'bsapp_multiselect', null);
				}
			});

			return ret;
		}
	};
}(jQuery));
