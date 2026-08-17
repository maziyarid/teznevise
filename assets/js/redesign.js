document.addEventListener('DOMContentLoaded', function () {
  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var toggle = document.querySelector('[data-menu-toggle]');
  var menu = document.querySelector('[data-mobile-menu]');
  var menuIcon = document.querySelector('[data-menu-icon]');
  var closeBtn = document.querySelector('[data-menu-close]');
  var previousFocus = null;

  function getMenuFocusables() {
    if (!menu) return [];
    return Array.prototype.slice.call(menu.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')).filter(function (el) { return !el.hidden; });
  }

  function setMenuOpen(open) {
    if (!menu) return;
    if (open) {
      previousFocus = document.activeElement;
      menu.hidden = false;
    }
    menu.classList.toggle('open', open);
    if (!open) menu.hidden = true;
    if (toggle) {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
    }
    document.body.classList.toggle('nav-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (menuIcon) menuIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    if (open && closeBtn) closeBtn.focus();
    if (!open && previousFocus && typeof previousFocus.focus === 'function') {
      previousFocus.focus();
      previousFocus = null;
    }
  }

  if (toggle && menu) toggle.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); setMenuOpen(!menu.classList.contains('open')); });
  if (closeBtn) closeBtn.addEventListener('click', function (e) { e.preventDefault(); setMenuOpen(false); });
  if (menu) {
    menu.addEventListener('click', function (e) { if (e.target === menu) setMenuOpen(false); });
    menu.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { setMenuOpen(false); }); });
  }
  document.addEventListener('keydown', function (e) {
    if (!menu || !menu.classList.contains('open')) return;
    if (e.key === 'Escape') {
      setMenuOpen(false);
      return;
    }
    if (e.key === 'Tab') {
      var focusables = getMenuFocusables();
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });

  document.querySelectorAll('.faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.faq-item'); if (!item) return;
      var wasOpen = item.classList.contains('open');
      var parent = item.parentElement;
      parent.querySelectorAll('.faq-item.open').forEach(function (el) { if (el !== item) { el.classList.remove('open'); var q = el.querySelector('.faq-q'); if (q) q.setAttribute('aria-expanded', 'false'); } });
      item.classList.toggle('open', !wasOpen);
      btn.setAttribute('aria-expanded', String(!wasOpen));
    });
  });

  document.querySelectorAll('[data-seo-toggle]').forEach(function (seoToggle) {
    var targetId = seoToggle.getAttribute('aria-controls');
    var seoMore = targetId ? document.getElementById(targetId) : null;
    if (!seoMore) { seoMore = seoToggle.closest('.seo-disclosure, .seo-panel'); if (seoMore) seoMore = seoMore.querySelector('.seo-more-content'); }
    if (!seoMore) return;
    seoMore.hidden = false; seoMore.removeAttribute('hidden'); seoMore.classList.remove('is-open'); seoToggle.setAttribute('aria-expanded', 'false');
    seoToggle.addEventListener('click', function (e) {
      e.preventDefault();
      var next = seoToggle.getAttribute('aria-expanded') !== 'true';
      seoToggle.setAttribute('aria-expanded', String(next)); seoMore.classList.toggle('is-open', next);
      var label = seoToggle.querySelector('.seo-more-text'); var mark = seoToggle.querySelector('.seo-more-mark');
      if (label) label.textContent = next ? 'مشاهده کمتر' : 'مشاهده بیشتر'; if (mark) mark.textContent = next ? '⌃' : '‹';
    });
  });

  if (!prefersReduced && 'IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); revealObserver.unobserve(entry.target); } }); }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach(function (el) { revealObserver.observe(el); });
  } else document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach(function (el) { el.classList.add('is-visible'); });

  function animateValue(el, end, duration, suffix) {
    if (prefersReduced) { el.textContent = end + (suffix || ''); return; }
    var start = 0, startTime = performance.now();
    function tick(now) { var progress = Math.min((now - startTime) / duration, 1); var eased = 1 - Math.pow(1 - progress, 3); el.textContent = Math.round(start + (end - start) * eased) + (suffix || ''); if (progress < 1) requestAnimationFrame(tick); }
    requestAnimationFrame(tick);
  }
  if ('IntersectionObserver' in window) {
    var counterObs = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (!entry.isIntersecting) return; var el = entry.target; var end = parseInt(el.getAttribute('data-count') || el.getAttribute('data-counter') || '0', 10); var suffix = el.getAttribute('data-suffix') || ''; animateValue(el, end, 1400, suffix); counterObs.unobserve(el); }); }, { threshold: 0.4 });
    document.querySelectorAll('[data-count], [data-counter]').forEach(function (el) { counterObs.observe(el); });
  }

  var fab = document.getElementById('tzFab'), fabToggle = document.getElementById('tzFabToggle'), fabMenu = document.getElementById('tzFabMenu');
  if (fab && fabToggle && fabMenu) {
    fabToggle.addEventListener('click', function (e) { e.preventDefault(); var open = fab.classList.toggle('is-open'); fabToggle.setAttribute('aria-expanded', String(open)); fabMenu.hidden = !open; var icon = fabToggle.querySelector('[data-fab-icon]'); if (icon) icon.className = open ? 'fa-solid fa-xmark' : 'fa-regular fa-comments'; });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && fab.classList.contains('is-open')) { fab.classList.remove('is-open'); fabToggle.setAttribute('aria-expanded', 'false'); fabMenu.hidden = true; } });
    document.addEventListener('click', function (e) { if (fab.classList.contains('is-open') && !fab.contains(e.target)) { fab.classList.remove('is-open'); fabToggle.setAttribute('aria-expanded', 'false'); fabMenu.hidden = true; } });
  }

  document.querySelectorAll('.mobile-nav-toggle').forEach(function (btn) { btn.addEventListener('click', function () { var group = btn.closest('.mobile-nav-group'); if (!group) return; var was = group.classList.contains('open'); document.querySelectorAll('.mobile-nav-group.open').forEach(function (g) { if (g !== group) g.classList.remove('open'); }); group.classList.toggle('open', !was); btn.setAttribute('aria-expanded', String(!was)); }); });
});
