function jsClosest(el, selector) {
	let matchesFn;
	['matches','webkitMatchesSelector','mozMatchesSelector','msMatchesSelector','oMatchesSelector'].some(function(fn) {
		if (typeof document.body[fn] == 'function') {
			matchesFn = fn;
			return true;
		}
		return false;
	})
	let parent;
	while (el) {
		parent = el.parentElement;
		if (parent && parent[matchesFn](selector)) {
			return parent;
		}
		el = parent;
	}
	return null;
}

export function jsModal() {
	function modal() {
		this.options = {
			'classModal': theme.prefix + '__modal',
			'classBackdrop': theme.prefix + '__modal-backdrop',
			'classOpen': 'modal-open',
			'classShow': 'show'
		};
		this.selectors = {
			openEl: document.querySelectorAll('[data-open="modal"]'),
			closeEl: document.querySelectorAll('[data-dismiss="modal"]')
		};

		for (let i = 0; i < this.selectors.openEl.length; i++) {
			this.selectors.openEl[i].addEventListener('click', this.show.bind(this), false);
		}

		for (let i = 0; i < this.selectors.closeEl.length; i++) {
			this.selectors.closeEl[i].addEventListener('click', this.hide.bind(this), false);
		}
	}

	modal.prototype = Object.assign({}, modal.prototype, {
		showBackdrop: function() {
			const el = document.querySelector('.' + this.options.classBackdrop);
			if (el === null) {
				let elemDiv = document.createElement('div')
				elemDiv.classList.add(this.options.classBackdrop, this.options.classShow);
				elemDiv.style.display = "block";
				document.body.appendChild(elemDiv);
			} else {
				el.style.display = "block";
				el.classList.add(this.options.classShow);
			}
		},
		hideBackdrop: function() {
			const el = document.querySelector('.' + this.options.classBackdrop);
			el.classList.remove(this.options.classShow);
			setTimeout(function () {
				el.style.display = "none";
			}.bind(this), 200);
		},
		show: function(e) {
			e.preventDefault();
			const el = e.currentTarget;
			const modal = el.getAttribute('href');
			const modalEl = document.getElementById(modal.replace('#', ''));
			document.body.classList.add(this.options.classOpen);
			modalEl.style.display = "block";
			modalEl.setAttribute('aria-modal', 'true');
			this.showBackdrop();
			setTimeout(function () {
				modalEl.classList.add(this.options.classShow);
			}.bind(this), 200);
		},
		hide: function(e) {
			e.preventDefault();
			const el = e.currentTarget;
			const modalEl = jsClosest(el, '.'+this.options.classModal);
			document.body.classList.remove(this.options.classOpen);
			modalEl.classList.remove(this.options.classShow);
			setTimeout(function () {
				modalEl.style.display = "none";
				modalEl.removeAttribute('aria-modal');
				this.hideBackdrop();
			}.bind(this), 200);
		},
	});

	return new modal();
}