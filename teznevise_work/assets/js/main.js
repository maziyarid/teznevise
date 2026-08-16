/* Teznevise — Main JavaScript
   Theme toggle · Mobile nav · Search · FAQ · Scroll effects · Counters
   */

(function () {
  'use strict';

  // --- Persian number helper ---
  var FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
  function toFa(num) {
    return String(num).replace(/\d/g, function (d) { return FA_DIGITS[d]; });
  }

  // --- Theme Toggle ---
  var themeToggle = document.querySelector('[data-theme-toggle]');
  var htmlEl = document.documentElement;
  var currentTheme = 'light';

  // Check system preference or stored preference
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    currentTheme = 'dark';
  }
  // Check if user previously set a theme (via URL param or data attribute)
  var storedTheme = document.body.getAttribute('data-pref-theme');
  if (storedTheme) currentTheme = storedTheme;

  htmlEl.setAttribute('data-theme', currentTheme);

  function updateThemeIcon() {
    if (!themeToggle) return;
    if (currentTheme === 'dark') {
      themeToggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>';
      themeToggle.setAttribute('aria-label', 'حالت روشن');
    } else {
      themeToggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
      themeToggle.setAttribute('aria-label', 'حالت تاریک');
    }
  }
  updateThemeIcon();

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
      htmlEl.setAttribute('data-theme', currentTheme);
      document.body.setAttribute('data-pref-theme', currentTheme);
      updateThemeIcon();
    });
  }

  // --- Header scroll behavior ---
  var header = document.querySelector('.site-header');
  var lastScrollY = 0;

  function handleScroll() {
    var scrollY = window.scrollY;
    if (header) {
      if (scrollY > 10) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }

    // Back to top button
    var toTop = document.getElementById('toTop');
    if (toTop) {
      if (scrollY > 400) {
        toTop.classList.add('show');
      } else {
        toTop.classList.remove('show');
      }
    }

    lastScrollY = scrollY;
  }

  window.addEventListener('scroll', handleScroll, { passive: true });

  // --- Mobile nav toggle ---
  var menuToggle = document.querySelector('.menu-toggle');
  var mobileNav = document.querySelector('.mobile-nav');
  var mobileNavClose = document.querySelector('.mobile-nav-close');

  if (menuToggle && mobileNav) {
    menuToggle.addEventListener('click', function () {
      mobileNav.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  }

  if (mobileNavClose && mobileNav) {
    mobileNavClose.addEventListener('click', function () {
      mobileNav.classList.remove('open');
      document.body.style.overflow = '';
    });
  }

  if (mobileNav) {
    mobileNav.addEventListener('click', function (e) {
      if (e.target === mobileNav) {
        mobileNav.classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  }

  // --- Search overlay ---
  var searchBtn = document.querySelector('[data-search-open]');
  var searchOverlay = document.querySelector('.search-overlay');
  var searchClose = document.querySelector('.search-close');
  var searchInput = document.querySelector('.search-input');

  if (searchBtn && searchOverlay) {
    searchBtn.addEventListener('click', function () {
      searchOverlay.classList.add('open');
      document.body.style.overflow = 'hidden';
      if (searchInput) setTimeout(function () { searchInput.focus(); }, 100);
    });
  }

  if (searchClose && searchOverlay) {
    searchClose.addEventListener('click', function () {
      searchOverlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }

  if (searchOverlay) {
    searchOverlay.addEventListener('click', function (e) {
      if (e.target === searchOverlay) {
        searchOverlay.classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  }

  // Keyboard shortcut: '/' to open search
  document.addEventListener('keydown', function (e) {
    if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
      e.preventDefault();
      if (searchOverlay && !searchOverlay.classList.contains('open')) {
        searchOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        if (searchInput) setTimeout(function () { searchInput.focus(); }, 100);
      }
    }
    if (e.key === 'Escape') {
      if (searchOverlay && searchOverlay.classList.contains('open')) {
        searchOverlay.classList.remove('open');
        document.body.style.overflow = '';
      }
      if (mobileNav && mobileNav.classList.contains('open')) {
        mobileNav.classList.remove('open');
        document.body.style.overflow = '';
      }
    }
  });

  // --- FAQ Accordion ---
  var faqQuestions = document.querySelectorAll('.faq-question');
  faqQuestions.forEach(function (q) {
    q.addEventListener('click', function () {
      var item = q.closest('.faq-item');
      var isOpen = item.classList.contains('open');

      // Close all others in the same group
      var group = item.closest('.faq-group');
      if (group) {
        group.querySelectorAll('.faq-item.open').forEach(function (openItem) {
          if (openItem !== item) openItem.classList.remove('open');
        });
      }

      item.classList.toggle('open');
    });
  });

  // --- Fade-in on scroll ---
  if ('IntersectionObserver' in window) {
    var fadeElements = document.querySelectorAll('.fade-in');
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    fadeElements.forEach(function (el) { observer.observe(el); });
  } else {
    document.querySelectorAll('.fade-in').forEach(function (el) {
      el.classList.add('visible');
    });
  }

  // --- Counter animation ---
  if ('IntersectionObserver' in window) {
    var counters = document.querySelectorAll('[data-counter]');
    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-counter'), 10);
          var duration = 1500;
          var startTime = null;

          function animate(time) {
            if (!startTime) startTime = time;
            var progress = Math.min((time - startTime) / duration, 1);
            var value = Math.floor(progress * target);
            el.textContent = toFa(value) + (el.getAttribute('data-suffix') || '');
            if (progress < 1) {
              requestAnimationFrame(animate);
            } else {
              el.textContent = toFa(target) + (el.getAttribute('data-suffix') || '');
            }
          }
          requestAnimationFrame(animate);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { counterObserver.observe(el); });
  }

  // --- Back to top ---
  var toTopBtn = document.getElementById('toTop');
  if (toTopBtn) {
    toTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // --- Cookie banner ---
  var cookieBanner = document.getElementById('cookieBanner');
  if (cookieBanner) {
    // Show after 2 seconds if not previously dismissed
    setTimeout(function () {
      cookieBanner.classList.add('show');
    }, 2000);
  }

  var cookieAccept = document.querySelector('[data-cookie-accept]');
  var cookieReject = document.querySelector('[data-cookie-reject]');
  if (cookieAccept) {
    cookieAccept.addEventListener('click', function () {
      cookieBanner.classList.remove('show');
    });
  }
  if (cookieReject) {
    cookieReject.addEventListener('click', function () {
      cookieBanner.classList.remove('show');
    });
  }

  // --- Filter tabs ---
  var filterTabs = document.querySelectorAll('.filter-tab');
  filterTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var group = tab.closest('.filter-tabs');
      if (group) {
        group.querySelectorAll('.filter-tab.active').forEach(function (t) {
          t.classList.remove('active');
        });
      }
      tab.classList.add('active');

      // Filter items if data-filter is set
      var filter = tab.getAttribute('data-filter');
      var target = tab.getAttribute('data-filter-target');
      if (filter && target) {
        var items = document.querySelectorAll(target + ' [data-category]');
        items.forEach(function (item) {
          if (filter === 'all' || item.getAttribute('data-category') === filter) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      }
    });
  });

})();
