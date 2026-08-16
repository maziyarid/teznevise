/* Teznevise — Main JavaScript */
(function () {
  'use strict';

  if (!document.querySelector('link[href*="site-polish.css"]')) {
    var polish = document.createElement('link');
    polish.rel = 'stylesheet';
    polish.href = 'assets/css/site-polish.css';
    document.head.appendChild(polish);
  }
  if (!document.querySelector('link[href*="batch-fixes.css"]')) {
    var batch = document.createElement('link');
    batch.rel = 'stylesheet';
    batch.href = 'assets/css/batch-fixes.css';
    document.head.appendChild(batch);
  }

  document.querySelectorAll('.footer-bottom-links span, .footer-bottom span').forEach(function (el) {
    if (el.textContent && el.textContent.indexOf('\u0645\u06cc\u0627\u0646\u0628\u0631') !== -1) {
      el.remove();
    }
  });

  var FA_DIGITS = '\u06f0\u06f1\u06f2\u06f3\u06f4\u06f5\u06f6\u06f7\u06f8\u06f9';
  function toFa(num) {
    return String(num).replace(/\d/g, function (d) { return FA_DIGITS[d]; });
  }

  var htmlEl = document.documentElement;
  htmlEl.setAttribute('data-theme', 'light');
  document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
    btn.style.display = 'none';
  });

  var header = document.querySelector('.site-header');
  function handleScroll() {
    var scrollY = window.scrollY;
    if (header) {
      if (scrollY > 10) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    }
    var toTop = document.getElementById('toTop');
    if (toTop) {
      if (scrollY > 400) toTop.classList.add('show');
      else toTop.classList.remove('show');
    }
  }
  window.addEventListener('scroll', handleScroll, { passive: true });

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

  document.querySelectorAll('.faq-question').forEach(function (q) {
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
  }

  var toTopBtn = document.getElementById('toTop');
  if (toTopBtn) {
    toTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
})();
