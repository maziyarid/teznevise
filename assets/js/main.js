/* Teznevise — Main JavaScript */
(function () {
  'use strict';

  // Dynamically load additional CSS files if not already loaded
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

  // Search overlay functionality
  const searchBtn = document.querySelector('[data-search-open]');
  const searchOverlay = document.querySelector('.search-overlay');
  const searchClose = document.querySelector('.search-close');
  const searchInput = document.querySelector('.search-input');
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

  // Close overlays with Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (searchOverlay && searchOverlay.classList.contains('open')) {
        searchOverlay.classList.remove('open');
        document.body.style.overflow = '';
      }
    }
  });

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
