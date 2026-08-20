/**
 * Teznevise UI kit — 1.6.9
 * Vanilla JS (WordPress-safe). Magnetic CTAs, ink cursor, counters, FAQ.
 */
(function () {
	'use strict';
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var fine = window.matchMedia('(pointer: fine)').matches && window.innerWidth > 900;

	/* ----- FAQ (both class names) ----- */
	document.querySelectorAll('.faq-q, .faq-question').forEach(function (btn) {
		if (btn.dataset.tzFaq) return;
		btn.dataset.tzFaq = '1';
		btn.addEventListener('click', function () {
			var item = btn.closest('.faq-item');
			if (!item) return;
			var group = item.parentElement;
			var open = item.classList.contains('open');
			if (group) {
				group.querySelectorAll('.faq-item.open').forEach(function (el) {
					if (el !== item) {
						el.classList.remove('open');
						var q = el.querySelector('.faq-q, .faq-question');
						if (q) q.setAttribute('aria-expanded', 'false');
					}
				});
			}
			item.classList.toggle('open', !open);
			btn.setAttribute('aria-expanded', String(!open));
		});
	});

	/* ----- Persian counters ----- */
	var FA = '۰۱۲۳۴۵۶۷۸۹';
	function toFa(n) {
		return String(n).replace(/\d/g, function (d) { return FA[d]; });
	}
	if ('IntersectionObserver' in window) {
		var cObs = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				cObs.unobserve(entry.target);
				var el = entry.target;
				var target = parseInt(el.getAttribute('data-counter'), 10);
				if (isNaN(target)) return;
				var suffix = el.getAttribute('data-suffix') || '';
				if (reduced) {
					el.textContent = toFa(target) + suffix;
					return;
				}
				var start = performance.now();
				var dur = 900;
				function tick(now) {
					var t = Math.min(1, (now - start) / dur);
					var eased = 1 - Math.pow(1 - t, 3);
					el.textContent = toFa(Math.round(target * eased)) + suffix;
					if (t < 1) requestAnimationFrame(tick);
				}
				requestAnimationFrame(tick);
			});
		}, { threshold: 0.4 });
		document.querySelectorAll('[data-counter]').forEach(function (el) { cObs.observe(el); });
	}

	/* ----- Ink cursor ----- */
	if (fine && !reduced) {
		var cur = document.createElement('div');
		cur.className = 'tz-ink-cursor';
		cur.setAttribute('aria-hidden', 'true');
		document.body.appendChild(cur);
		var x = 0, y = 0, cx = 0, cy = 0;
		window.addEventListener('pointermove', function (e) {
			x = e.clientX;
			y = e.clientY;
			cur.classList.add('is-on');
		}, { passive: true });
		window.addEventListener('pointerdown', function () { cur.classList.add('is-press'); });
		window.addEventListener('pointerup', function () { cur.classList.remove('is-press'); });
		document.documentElement.addEventListener('mouseleave', function () {
			cur.classList.remove('is-on');
		});
		function loop() {
			cx += (x - cx) * 0.22;
			cy += (y - cy) * 0.22;
			cur.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0)';
			requestAnimationFrame(loop);
		}
		requestAnimationFrame(loop);
	}

	/* ----- Magnetic primary CTAs ----- */
	if (fine && !reduced) {
		document.querySelectorAll('.btn-primary-tz, .nav-cta-solid, .hero-order-button').forEach(function (btn) {
			btn.classList.add('tz-magnet');
			btn.addEventListener('pointermove', function (e) {
				var r = btn.getBoundingClientRect();
				var dx = (e.clientX - (r.left + r.width / 2)) / 8;
				var dy = (e.clientY - (r.top + r.height / 2)) / 8;
				btn.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
			});
			btn.addEventListener('pointerleave', function () {
				btn.style.transform = '';
			});
		});
	}

	/* ----- Card tilt ----- */
	if (fine && !reduced) {
		document.querySelectorAll('.service-card, .article-card').forEach(function (card) {
			card.classList.add('tz-tilt');
			card.addEventListener('pointermove', function (e) {
				var r = card.getBoundingClientRect();
				var px = (e.clientX - r.left) / r.width - 0.5;
				var py = (e.clientY - r.top) / r.height - 0.5;
				card.style.transform = 'rotateY(' + (px * -6) + 'deg) rotateX(' + (py * 6) + 'deg) translateY(-4px)';
			});
			card.addEventListener('pointerleave', function () {
				card.style.transform = '';
			});
		});
	}

	/* ----- Hash links offset for sticky header ----- */
	document.querySelectorAll('a[href^="#"]').forEach(function (a) {
		a.addEventListener('click', function (e) {
			var id = a.getAttribute('href').slice(1);
			if (!id) return;
			var target = document.getElementById(id);
			if (!target) return;
			e.preventDefault();
			var top = target.getBoundingClientRect().top + window.scrollY - 96;
			window.scrollTo({ top: top, behavior: reduced ? 'auto' : 'smooth' });
		});
	});

	/* ----- Contact / inquiry forms: required toast ----- */
	document.querySelectorAll('form.tz-form, #contactForm, .lead-card').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			var bad = form.querySelector(':invalid');
			if (bad) {
				e.preventDefault();
				bad.focus();
			}
		});
	});
})();
