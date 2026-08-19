/**
 * Desktop nav overflow. WordPress menus often have 7–10 long Persian
 * labels; the HTML prototype only had five short ones. If the row would
 * wrap, extra items move into a "بیشتر" dropdown so names stay on one line.
 */
(function () {
	'use strict';

	var MORE_LABEL = 'بیشتر';

	function listEl() {
		return document.querySelector('.main-nav > ul.nav-links, .main-nav > .nav-links');
	}

	function isDesktop() {
		return window.matchMedia('(min-width: 1051px)').matches;
	}

	function restore(list) {
		var more = list.querySelector(':scope > li.nav-more');
		if (!more) {
			return;
		}
		var sub = more.querySelector(':scope > ul');
		if (sub) {
			while (sub.firstElementChild) {
				list.insertBefore(sub.firstElementChild, more);
			}
		}
		more.remove();
	}

	function ensureMore(list) {
		var more = list.querySelector(':scope > li.nav-more');
		if (more) {
			return more;
		}
		more = document.createElement('li');
		more.className = 'menu-item menu-item-has-children nav-more';
		more.innerHTML =
			'<a href="#">' +
			MORE_LABEL +
			' <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>' +
			'<ul class="sub-menu nav-dropdown"></ul>';
		list.appendChild(more);
		return more;
	}

	function compact() {
		var list = listEl();
		if (!list) {
			return;
		}
		restore(list);
		if (!isDesktop()) {
			return;
		}

		var safety = 0;
		while (list.scrollWidth > list.clientWidth + 2 && list.children.length > 4 && safety < 16) {
			safety += 1;
			var items = [].filter.call(list.children, function (li) {
				return !li.classList.contains('nav-more');
			});
			if (items.length < 3) {
				break;
			}
			var more = ensureMore(list);
			var sub = more.querySelector(':scope > ul');
			var last = items[items.length - 1];
			if (!last || !sub) {
				break;
			}
			sub.insertBefore(last, sub.firstChild);
		}
	}

	var frame = 0;
	function schedule() {
		if (frame) {
			cancelAnimationFrame(frame);
		}
		frame = requestAnimationFrame(compact);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', schedule);
	} else {
		schedule();
	}
	window.addEventListener('resize', schedule);
	window.addEventListener('load', schedule);
})();
