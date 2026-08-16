/* Teznevise — Main JavaScript
   Theme toggle · Mobile nav · Search · FAQ · Scroll effects · Counters
   */

(function () {
  'use strict';

  // --- Inject site-polish.css if missing (tools/post/legacy pages) ---
  if (!document.querySelector('link[href*="site-polish.css"]')) {
    var polish = document.createElement('link');
    polish.rel = 'stylesheet';
    polish.href = 'assets/css/site-polish.css';
    document.head.appendChild(polish);
  }

  // --- Remove irrelevant footer shortcut text ---
  document.querySelectorAll('.footer-bottom-links span, .footer-bottom span').forEach(function (el) {
    if (el.textContent && el.textContent.indexOf('میانبر') !== -1) {
      el.remove();
    }
  });

  // --- Persian number helper ---
  var FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
  function toFa(num) {
    return String(num).replace(/\d/g, function (d) { return FA_DIGITS[d]; });
  }

  // --- Theme Toggle DISABLED (force light) ---
  var htmlEl = document.documentElement;
  htmlEl.setAttribute('data-theme', 'light');
  document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
    btn.style.display = 'none';
  });

  // --- Header scroll behavior ---
  var header = document.querySelector('.site-header');

  function handleScroll() {
    var scrollY = window.scrollY;
    if (header) {
      if (scrollY > 10) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }

    var toTop = document.getElementById('toTop');
    if (toTop) {
      if (scrollY > 400) {
        toTop.classList.add('show');
      } else {
        toTop.classList.remove('show');
      }
    }
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

  document.addEventListener('keydown', function (e) {
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
