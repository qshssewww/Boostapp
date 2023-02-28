import $ from "jquery";
import "select2/dist/css/select2.min.css";
import "select2/dist/js/select2.min";
import en from "select2/src/js/select2/i18n/en";
import he from "select2/src/js/select2/i18n/he";
import "@/scss/select2.scss";

import {Lang} from "@modules/Lang";

export default function Select2(props) {
	const {
		elId,
		options,
		isOpen,
		templateHandlebarsName,
		_onSelection
	} = props;

	const $sel2El = $('#' + elId);
	if ($sel2El.length === 0 || $sel2El.hasClass('select2-hidden-accessible')) {
		return false;
	}

	const param = {
		placeholder: Lang('search'),
		language: document.dir === "rtl" ? he : en,
		dir: document.dir,
		allowClear: true,
		theme: "without-arrow bsapp--select2-dropdown without-arrow",
		minimumInputLength: 2,
		tags: true,
		createTag: function (params) {
			const term = $.trim(params.term);
			if (term === '') {
				return null;
			}
			return {
				id: term,
				text: term,
				isNew: true // add additional parameters
			};
		},
		templateResult: function (item) {
			//console.log('templateResult', item);
			if (templateHandlebarsName) {
				return $(templateHandlebarsName(item));
			} else {
				return $(`<div>${item.text}</div>`);
			}
		},
		templateSelection: function (item) {
			//console.log('templateSelection', item);
			if (templateHandlebarsName) {
				return $(templateHandlebarsName(item));
			} else {
				return $(`<div>${item.text}</div>`);
			}
		}
	};

	const settings = Object.assign({}, param, options);
	$sel2El.select2(settings).on("select2:selecting", function (e) {
		// Fired when a choice is being selected in the dropdown, but before any modification has been made to the selection
		if (_onSelection) {
			_onSelection(e);
		}

	}).on("select2:open", function (e) {
		const el = e.currentTarget;
		setTimeout(function() {
			el.parentNode.querySelector('.select2-search__field[type="search"]').focus();
		}, 100);
	});

	if (isOpen) {
		$sel2El.select2('open');
	}
}