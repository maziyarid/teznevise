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
      if (label) label.textContent = next ? 'بستن' : 'ادامه مطلب';
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
