/**
 * AUTO-GENERATED — do not edit by hand.
 * Rebuild: python3 scripts/build-frontend-bundles.py
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * @package Teznevise
 */

/* ===== redesign.js ===== */
document.addEventListener('DOMContentLoaded', function () {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');
  const menuIcon = document.querySelector('[data-menu-icon]');
  const closeBtn = document.querySelector('[data-menu-close]');

  let menuHideTimer = null;

  /** Keep mobile navigation state, visibility, and accessibility attributes synchronized. */
  function setMenuOpen(open) {
    if (!menu) return;
    if (open) {
      if (menuHideTimer) { clearTimeout(menuHideTimer); menuHideTimer = null; }
      menu.removeAttribute('hidden');
    }
    menu.classList.toggle('open', open);
    if (!open) {
      menuHideTimer = setTimeout(function () {
        if (!menu.classList.contains('open')) menu.setAttribute('hidden', '');
      }, 450);
    }
    if (toggle) {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
    }
    document.body.classList.toggle('nav-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (menuIcon) {
      menuIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    }
  }

  if (toggle && menu) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setMenuOpen(!menu.classList.contains('open'));
    });
  }
  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      setMenuOpen(false);
    });
  }
  if (menu) {
    menu.addEventListener('click', function (e) {
      if (e.target === menu) setMenuOpen(false);
    });
    menu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setMenuOpen(false); });
    });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && menu && menu.classList.contains('open')) {
      setMenuOpen(false);
    }
  });

  document.querySelectorAll('.faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const item = btn.closest('.faq-item');
      if (!item) return;
      const wasOpen = item.classList.contains('open');
      const parent = item.parentElement;
      if (!parent) return;
      parent.querySelectorAll('.faq-item.open').forEach(function (el) {
        if (el !== item) {
          el.classList.remove('open');
          const q = el.querySelector('.faq-q');
          if (q) q.setAttribute('aria-expanded', 'false');
        }
      });
      item.classList.toggle('open', !wasOpen);
      btn.setAttribute('aria-expanded', String(!wasOpen));
    });
  });

  document.documentElement.classList.add('js');
  document.querySelectorAll('[data-seo-toggle], [data-content-toggle]').forEach(function (seoToggle) {
    const targetId = seoToggle.getAttribute('aria-controls');
    let seoMore = targetId ? document.getElementById(targetId) : null;
    if (!seoMore) {
      seoMore = seoToggle.closest('.seo-disclosure, .seo-panel, .tz-classic-disclosure');
      if (seoMore) seoMore = seoMore.querySelector('.seo-more-content');
    }
    if (!seoMore) return;
    seoMore.hidden = false;
    seoMore.removeAttribute('hidden');
    seoMore.classList.remove('is-open');
    seoToggle.setAttribute('aria-expanded', 'false');
    seoToggle.addEventListener('click', function (e) {
      e.preventDefault();
      const isOpen = seoToggle.getAttribute('aria-expanded') === 'true';
      const next = !isOpen;
      seoToggle.setAttribute('aria-expanded', String(next));
      seoMore.classList.toggle('is-open', next);
      const label = seoToggle.querySelector('.seo-more-text');
      const mark = seoToggle.querySelector('.seo-more-mark');
      if (label) label.textContent = next ? 'مشاهده کمتر' : 'مشاهده بیشتر';
      if (mark) mark.textContent = next ? '⌃' : '‹';
    });
  });

  if (!prefersReduced && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach(function (el) {
      revealObserver.observe(el);
    });
  } else {
    document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  /** Animate a numeric counter, respecting reduced-motion preferences. */
  function animateValue(el, end, duration, suffix) {
    if (prefersReduced) {
      el.textContent = end + (suffix || '');
      return;
    }
    const start = 0;
    const startTime = performance.now();
    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - (1 - progress) ** 3;
      el.textContent = Math.round(start + (end - start) * eased) + (suffix || '');
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  if ('IntersectionObserver' in window) {
    const counterObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const end = parseInt(el.getAttribute('data-count') || el.getAttribute('data-counter') || '0', 10);
        const suffix = el.getAttribute('data-suffix') || '';
        animateValue(el, end, 1400, suffix);
        counterObs.unobserve(el);
      });
    }, { threshold: 0.4 });
    document.querySelectorAll('[data-count], [data-counter]').forEach(function (el) {
      counterObs.observe(el);
    });
  }

  // Desktop FAB
  const fab = document.getElementById('tzFab');
  const fabToggle = document.getElementById('tzFabToggle');
  const fabMenu = document.getElementById('tzFabMenu');
  if (fab && fabToggle && fabMenu) {
    fabToggle.addEventListener('click', function (e) {
      e.preventDefault();
      const open = fab.classList.toggle('is-open');
      fabToggle.setAttribute('aria-expanded', String(open));
      fabMenu.hidden = !open;
      const icon = fabToggle.querySelector('[data-fab-icon]');
      if (icon) icon.className = open ? 'fa-solid fa-xmark' : 'fa-regular fa-comments';
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && fab.classList.contains('is-open')) {
        fab.classList.remove('is-open');
        fabToggle.setAttribute('aria-expanded', 'false');
        fabMenu.hidden = true;
      }
    });
    document.addEventListener('click', function (e) {
      if (fab.classList.contains('is-open') && !fab.contains(e.target)) {
        fab.classList.remove('is-open');
        fabToggle.setAttribute('aria-expanded', 'false');
        fabMenu.hidden = true;
      }
    });
  }

  document.querySelectorAll('.mobile-nav-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const group = btn.closest('.mobile-nav-group');
      if (!group) return;
      const was = group.classList.contains('open');
      document.querySelectorAll('.mobile-nav-group.open').forEach(function (g) {
        if (g !== group) g.classList.remove('open');
      });
      group.classList.toggle('open', !was);
      btn.setAttribute('aria-expanded', String(!was));
    });
  });

  document.querySelectorAll('[data-filter]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const value = btn.getAttribute('data-filter') || 'all';
      const root = btn.closest('section, .container, main') || document;
      root.querySelectorAll('[data-filter]').forEach(function (el) {
        el.classList.toggle('is-active', el === btn);
        el.classList.toggle('active', el === btn);
      });
      root.querySelectorAll('[data-cat], [data-category]').forEach(function (card) {
        const cat = card.getAttribute('data-cat') || card.getAttribute('data-category') || '';
        const show = value === 'all' || cat === value || cat.split(/\s+/).indexOf(value) !== -1;
        card.hidden = !show;
      });
    });
  });
});

/* ===== main.js (search overlay removed) ===== */
/* Teznevise — Main JavaScript */
(function () {
  'use strict';

  // NOTE: site-polish.css and batch-fixes.css are properly enqueued by
  // functions.php. The old dynamic injection used relative URLs which
  // 404'd on every subpage — removed.

  // Remove specific elements
  document.querySelectorAll('.footer-bottom-links span, .footer-bottom span').forEach(function (el) {
    if (el.textContent && el.textContent.indexOf('میانبر') !== -1) {
      el.remove();
    }
  });

  // Persian digit conversion
  const FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
  function toFa(num) {
    return String(num).replace(/\d/g, function (d) { return FA_DIGITS[d]; });
  }

  const htmlEl = document.documentElement;
  htmlEl.setAttribute('data-theme', 'light');
  document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
    btn.style.display = 'none';
  });

  // Header scroll effect
  const header = document.querySelector('.site-header, .site-header-new');
  function handleScroll() {
    const scrollY = window.scrollY;
    if (header) {
      if (scrollY > 10) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    }
    const toTop = document.getElementById('toTop');
    if (toTop) {
      if (scrollY > 400) toTop.classList.add('show');
      else toTop.classList.remove('show');
    }
  }
  window.addEventListener('scroll', handleScroll, { passive: true });

  /* search overlay: owned by product-1.7 block in this bundle */

  // FAQ accordion
  document.querySelectorAll('.faq-question').forEach(function (q) {
    q.addEventListener('click', function () {
      const item = q.closest('.faq-item');
      if (!item) return;
      const group = item.closest('.faq-group');
      if (group) {
        group.querySelectorAll('.faq-item.open').forEach(function (openItem) {
          if (openItem !== item) openItem.classList.remove('open');
        });
      }
      item.classList.toggle('open');
    });
  });

  // Intersection Observer for fade-in elements
  if ('IntersectionObserver' in window) {
    const fadeElements = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    fadeElements.forEach(function (el) { observer.observe(el); });
  }

  // Back to top button
  const toTopBtn = document.getElementById('toTop');
  if (toTopBtn) {
    toTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
})();

/* ===== nav-touch.js ===== */
/**
 * Touch-device support for the dropdown submenus defined in redesign.css.
 * On a device with no hover capability, the first tap on a parent link
 * (.menu-item-has-children > a) opens its submenu instead of navigating;
 * a second tap on the same link follows it. Tapping outside or pressing
 * Escape closes any open submenu. Devices with real hover are untouched
 * and keep using redesign.css's existing :hover/:focus-within behavior.
 */
( function () {
	'use strict';

	if ( window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		return;
	}

	function closeAll( except ) {
		document.querySelectorAll( '.nav-links li.submenu-open' ).forEach( function ( li ) {
			if ( li === except ) { return; }
			li.classList.remove( 'submenu-open' );
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var link = event.target.closest( '.nav-links .menu-item-has-children > a' );
		if ( link ) {
			var li = link.parentElement;
			if ( li.querySelector( ':scope > .nav-dropdown-toggle' ) ) {
				return;
			}
			if ( ! li.classList.contains( 'submenu-open' ) ) {
				event.preventDefault();
				var parentOpen = link.closest( '.sub-menu' ) ? li.parentElement.closest( 'li.submenu-open' ) : null;
				closeAll( li );
				li.classList.add( 'submenu-open' );
				if ( parentOpen ) { parentOpen.classList.add( 'submenu-open' ); }
			}
			return;
		}
		if ( ! event.target.closest( '.nav-links' ) ) {
			closeAll( null );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' ) {
			closeAll( null );
		}
	} );
} )();

/* ===== product-1.7.js ===== */
/* Teznevise 1.7 — search overlay, share rewards, FAB polish */
(function () {
  'use strict';

  var searchBtn = document.querySelector('[data-search-open]');
  var searchOverlay = document.querySelector('.search-overlay');
  var searchClose = document.querySelector('.search-close');
  var searchInput = document.querySelector('.search-input');
  var lastTrigger = null;

  function searchFocusables() {
    if (!searchOverlay) return [];
    return Array.prototype.slice.call(
      searchOverlay.querySelectorAll('a[href], button:not([disabled]), input, textarea, select, [tabindex]:not([tabindex="-1"])')
    ).filter(function (el) {
      return !el.hasAttribute('disabled') && el.getClientRects().length > 0;
    });
  }

  function openSearch(e) {
    if (e) e.preventDefault();
    if (!searchOverlay) return;
    lastTrigger = (e && e.currentTarget) ? e.currentTarget : searchBtn;
    searchOverlay.hidden = false;
    searchOverlay.classList.add('open');
    searchOverlay.setAttribute('aria-hidden', 'false');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    if (searchInput) setTimeout(function () { searchInput.focus(); }, 80);
  }
  function closeSearch() {
    if (!searchOverlay || searchOverlay.hidden) return;
    searchOverlay.classList.remove('open');
    searchOverlay.hidden = true;
    searchOverlay.setAttribute('aria-hidden', 'true');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    if (lastTrigger && typeof lastTrigger.focus === 'function') {
      lastTrigger.focus();
    }
  }
  if (searchBtn) searchBtn.addEventListener('click', openSearch);
  if (searchClose) searchClose.addEventListener('click', closeSearch);
  if (searchOverlay) {
    searchOverlay.addEventListener('click', function (e) {
      if (e.target === searchOverlay) closeSearch();
    });
  }
  document.addEventListener('keydown', function (e) {
    if (!searchOverlay || searchOverlay.hidden) return;
    if (e.key === 'Escape') {
      closeSearch();
      return;
    }
    if (e.key !== 'Tab') return;
    var list = searchFocusables();
    if (!list.length) return;
    var first = list[0];
    var last = list[list.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  });

  /* Teznevise Design System v2 — progressively enhanced instant search. */
  var instantForm = document.querySelector('[data-instant-search]');
  var instantResults = document.querySelector('[data-search-results]');
  var instantStatus = document.querySelector('[data-search-status]');
  var instantList = document.querySelector('[data-search-list]');
  var searchTimer = 0;
  var searchRequest = null;

  function setSearchStatus(message) {
    if (instantStatus) instantStatus.textContent = message;
  }
  function clearInstantResults() {
    if (instantList) instantList.textContent = '';
    if (instantResults) instantResults.hidden = true;
    setSearchStatus('');
  }
  function renderInstantResults(items) {
    if (!instantList || !instantResults) return;
    instantList.textContent = '';
    items.forEach(function (item) {
      var row = document.createElement('li');
      var link = document.createElement('a');
      link.href = item.url;
      link.textContent = item.title;
      row.appendChild(link);
      instantList.appendChild(row);
    });
    instantResults.hidden = false;
    setSearchStatus(items.length ? items.length + ' نتیجه پیدا شد.' : 'نتیجه‌ای پیدا نشد.');
  }
  function requestInstantSearch(query) {
    if (!instantForm) return;
    var endpoint = instantForm.getAttribute('data-search-endpoint');
    if (!endpoint) return;
    if (searchRequest) searchRequest.abort();
    searchRequest = new AbortController();
    if (instantResults) instantResults.hidden = false;
    setSearchStatus('در حال جستجو…');
    fetch(endpoint + '?search=' + encodeURIComponent(query) + '&per_page=6&_fields=id,title,url,subtype', {
      credentials: 'same-origin',
      signal: searchRequest.signal
    })
      .then(function (response) {
        if (!response.ok) throw new Error('search');
        return response.json();
      })
      .then(renderInstantResults)
      .catch(function (error) {
        if (error.name === 'AbortError') return;
        if (instantResults) instantResults.hidden = false;
        setSearchStatus('جستجوی سریع در دسترس نیست؛ برای جستجوی کامل Enter را بزنید.');
      });
  }
  if (instantForm && searchInput) {
    searchInput.setAttribute('autocomplete', 'off');
    searchInput.addEventListener('input', function () {
      window.clearTimeout(searchTimer);
      var query = searchInput.value.trim();
      if (query.length < 2) {
        if (searchRequest) searchRequest.abort();
        clearInstantResults();
        return;
      }
      searchTimer = window.setTimeout(function () { requestInstantSearch(query); }, 220);
    });
  }

  document.querySelectorAll('[data-share]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var net = btn.getAttribute('data-share');
      var url = btn.getAttribute('data-url') || window.location.href;
      var title = btn.getAttribute('data-title') || document.title;
      var href = url;
      if (net === 'telegram') href = 'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
      if (net === 'whatsapp') href = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
      if (net === 'x') href = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
      if (net === 'linkedin') href = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
      window.open(href, '_blank', 'noopener,noreferrer');
      if (window.tezneviseProduct && tezneviseProduct.logged && window.tezneviseProduct.ajax) {
        var body = new FormData();
        body.append('action', 'teznevise_share_reward');
        body.append('nonce', tezneviseProduct.nonce);
        body.append('slug', btn.getAttribute('data-slug') || '');
        body.append('network', net);
        fetch(tezneviseProduct.ajax, { method: 'POST', body: body, credentials: 'same-origin' });
      }
    });
  });
  var ask = document.getElementById('tzAskAi');
  if (ask && window.tezneviseProduct) {
    ask.addEventListener('submit', function (e) {
      e.preventDefault();
      var out = document.getElementById('tzAskOut');
      var q = ask.querySelector('textarea');
      var agent = ask.querySelector('input[name="agent"]:checked');
      if (!q || !out) return;
      out.hidden = false;
      out.textContent = '…';
      var body = new FormData();
      body.append('action', 'teznevise_ask_ai');
      body.append('nonce', tezneviseProduct.nonce);
      body.append('q', q.value);
      if (agent) body.append('agent', agent.value);
      fetch(tezneviseProduct.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (d) {
          if (d && d.success && d.data && d.data.text) out.textContent = d.data.text;
          else if (d && d.data && d.data.message === 'no-key') out.textContent = 'کلید OpenRouter در تنظیمات تزکوین وارد نشده است.';
          else if (d && d.data && d.data.message === 'login') out.textContent = 'وارد شوید.';
          else if (d && d.data && d.data.message === 'no-coins') out.textContent = 'موجودی تزکوین کافی نیست.';
          else out.textContent = 'پاسخ آماده نشد.';
        })
        .catch(function () { out.textContent = 'ارتباط برقرار نشد.'; });
    });
  }

  /* Teznevise Design System v2 — focus mode and bounded type controls. */
  var focusButton = document.querySelector('[data-reading-focus]');
  var readingSize = 1;
  if (focusButton) {
    focusButton.addEventListener('click', function () {
      var active = document.body.classList.toggle('tz-reading-focus');
      focusButton.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }
  document.querySelectorAll('[data-reading-size]').forEach(function (button) {
    button.addEventListener('click', function () {
      readingSize = button.getAttribute('data-reading-size') === 'increase' ? Math.min(1.25, readingSize + 0.1) : 1;
      document.documentElement.style.setProperty('--tz-reading-size', readingSize + 'rem');
    });
  });

  /* 1.8.7 — reading progress + TOC spy on single posts */
  var progressFill = document.querySelector('.reading-progress span');
  var articleBody = document.querySelector('.blog-post__content');
  if (progressFill && articleBody) {
    var updateProgress = function () {
      var top = articleBody.getBoundingClientRect().top + window.scrollY;
      var height = Math.max(1, articleBody.offsetHeight - window.innerHeight * 0.45);
      var p = (window.scrollY - top + 80) / height;
      progressFill.style.width = (Math.max(0, Math.min(1, p)) * 100) + '%';
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }
  var tocLinks = document.querySelectorAll('.blog-post__toc .post-toc-item');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var tocMap = [];
    tocLinks.forEach(function (link) {
      var raw = (link.getAttribute('href') || '').replace(/^#/, '');
      var id = raw;
      try { id = decodeURIComponent(raw); } catch (e) { id = raw; }
      var heading = document.getElementById(raw) || document.getElementById(id);
      if (heading) tocMap.push({ heading: heading, link: link });
    });
    if (tocMap.length) {
      var setActive = function (active) {
        tocLinks.forEach(function (l) { l.classList.remove('is-active'); });
        if (active) active.classList.add('is-active');
      };
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          tocMap.forEach(function (item) {
            if (item.heading === entry.target) setActive(item.link);
          });
        });
      }, { rootMargin: '-20% 0px -70% 0px', threshold: 0 });
      tocMap.forEach(function (item) { observer.observe(item.heading); });
    }
  }
})();

/* ===== nav-dropdown.js ===== */
/**
 * Accessible toggle behaviour for dropdown submenus (.nav-dropdown), shared
 * by the desktop primary menu (.main-nav) and the mobile drawer menu
 * (.mobile-nav-links) -- both are rendered by the same Teznevise_Nav_Walker.
 *
 * Desktop hover only follows the in-flow nav link plus the mega panel
 * after it is open. The closed panel is `display:none`, so hovering the
 * page below the bar cannot keep or open a menu.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  var toggles = document.querySelectorAll('.menu-item.has-dropdown > .nav-dropdown-toggle');
  if (!toggles.length) return;

  var supportsHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
  var openDelay = 180;
  var closeDelay = 220;
  var closeTimer = null;
  var openTimer = null;

  function closeAll(except) {
    toggles.forEach(function (toggle) {
      if (toggle === except) return;
      toggle.setAttribute('aria-expanded', 'false');
      var panel = toggle.parentElement.querySelector(':scope > .nav-dropdown, :scope > ul.nav-dropdown');
      if (!panel) panel = toggle.parentElement.querySelector('.nav-dropdown');
      if (panel) panel.classList.remove('is-open');
      toggle.parentElement.classList.remove('is-open');
    });
  }

  function openToggle(toggle, panel) {
    if (closeTimer) {
      clearTimeout(closeTimer);
      closeTimer = null;
    }
    if (openTimer) {
      clearTimeout(openTimer);
      openTimer = null;
    }
    closeAll(toggle);
    toggle.setAttribute('aria-expanded', 'true');
    panel.classList.add('is-open');
    toggle.parentElement.classList.add('is-open');
  }

  function scheduleOpen(toggle, panel) {
    if (closeTimer) {
      clearTimeout(closeTimer);
      closeTimer = null;
    }
    if (openTimer) clearTimeout(openTimer);
    openTimer = setTimeout(function () {
      openToggle(toggle, panel);
      openTimer = null;
    }, openDelay);
  }

  function closeToggle(toggle, panel) {
    toggle.setAttribute('aria-expanded', 'false');
    panel.classList.remove('is-open');
    toggle.parentElement.classList.remove('is-open');
  }

  function scheduleClose(toggle, panel) {
    if (closeTimer) clearTimeout(closeTimer);
    closeTimer = setTimeout(function () {
      closeToggle(toggle, panel);
      closeTimer = null;
    }, closeDelay);
  }

  toggles.forEach(function (toggle) {
    var parentItem = toggle.parentElement;
    var panel = parentItem.querySelector(':scope > .nav-dropdown, :scope > ul.nav-dropdown');
    if (!panel) panel = parentItem.querySelector('.nav-dropdown');
    if (!panel) return;
    var inMobileDrawer = !!toggle.closest('.mobile-nav-links');

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var isOpen = toggle.getAttribute('aria-expanded') === 'true';
      if (isOpen) {
        closeToggle(toggle, panel);
      } else {
        openToggle(toggle, panel);
      }
    });

    if (supportsHover && !inMobileDrawer) {
      parentItem.addEventListener('mouseenter', function () {
        scheduleOpen(toggle, panel);
      });
      parentItem.addEventListener('mouseleave', function () {
        if (openTimer) {
          clearTimeout(openTimer);
          openTimer = null;
        }
        scheduleClose(toggle, panel);
      });
    }

    parentItem.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeToggle(toggle, panel);
        toggle.focus();
      }
    });
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.menu-item.has-dropdown')) {
      closeAll();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll();
  });
});

/* ===== chrome-extras.js ===== */
/**
 * Front-end extras that must ship with chrome.js: crawler-safe tabs, AI summary, SERP overview.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-comment-tabs]').forEach(function (tabs) {
    var root = tabs.closest('.tz-comments') || document;
    tabs.querySelectorAll('[role="tab"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = btn.getAttribute('aria-controls');
        tabs.querySelectorAll('[role="tab"]').forEach(function (other) {
          other.setAttribute('aria-selected', other === btn ? 'true' : 'false');
        });
        root.querySelectorAll('.tz-comment-panel').forEach(function (panel) {
          var on = panel.id === target;
          panel.hidden = !on;
        });
      });
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-ai-summary]').forEach(function (box) {
    var btn = box.querySelector('[data-ai-summary-btn]');
    var out = box.querySelector('[data-ai-summary-out]');
    if (!btn || !out) return;
    btn.addEventListener('click', function () {
      var postId = box.getAttribute('data-ai-summary');
      btn.disabled = true;
      btn.textContent = 'در حال خلاصه‌کردن…';
      out.hidden = false;
      out.textContent = 'در حال آماده‌سازی نکات کلیدی…';
      var rest = (window.tezneviseProduct && window.tezneviseProduct.restUrl) || '/wp-json/';
      var nonce = (window.tezneviseProduct && window.tezneviseProduct.restNonce) || '';
      fetch(rest + 'teznevise-core/v1/summarise', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce
        },
        body: JSON.stringify({ post_id: parseInt(postId, 10) || 0, nonce: nonce })
      })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          btn.disabled = false;
          btn.textContent = 'خلاصه‌کردن نکات کلیدی';
          if (!j || !j.success || !j.bullets || !j.bullets.length) {
            out.textContent = 'خلاصه‌ای در دسترس نیست.';
            return;
          }
          var ul = document.createElement('ul');
          j.bullets.forEach(function (line) {
            var li = document.createElement('li');
            li.textContent = line;
            ul.appendChild(li);
          });
          out.replaceChildren(ul);
        })
        .catch(function () {
          btn.disabled = false;
          btn.textContent = 'خلاصه‌کردن نکات کلیدی';
          out.textContent = 'ارتباط برقرار نشد.';
        });
    });
  });

  document.querySelectorAll('[data-search-overview]').forEach(function (box) {
    var q = box.getAttribute('data-q') || '';
    var body = box.querySelector('[data-overview-body]');
    if (!q || !body) return;
    var rest = (window.tezneviseProduct && window.tezneviseProduct.restUrl) || '/wp-json/';
    fetch(rest + 'teznevise-core/v1/search-overview?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        body.textContent = (j && j.overview) ? j.overview : 'جمع‌بندی در دسترس نیست.';
      })
      .catch(function () {
        body.textContent = 'جمع‌بندی در دسترس نیست.';
      });
  });
});

/* ===== react-loader.js ===== */
/**
 * Front-end boot that matches the React SiteShell loader.
 * Search overlay behaviour lives in the product-1.7 block of chrome.js.
 */
document.addEventListener('DOMContentLoaded', function () {
  var root = document.documentElement;
  if (!root.getAttribute('lang')) {
    root.setAttribute('lang', 'fa');
  }
  if (!root.getAttribute('dir')) {
    root.setAttribute('dir', 'rtl');
  }
  document.body.classList.add('tz-react-shell');
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.tz-interactive-page-content table, .tool-body table, [class*="tz"] table').forEach(function (table) {
    if (table.querySelector('caption')) return;
    var caption = document.createElement('caption');
    caption.className = 'screen-reader-text';
    caption.textContent = 'نتایج محاسبه';
    table.insertBefore(caption, table.firstChild);
  });
  document.querySelectorAll('.tz-interactive-page-content, .tool-body').forEach(function (box) {
    if (!box.hasAttribute('aria-live')) {
      box.setAttribute('aria-live', 'polite');
    }
  });
});
