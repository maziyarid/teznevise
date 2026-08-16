document.addEventListener('DOMContentLoaded', function () {
  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---- Mobile drawer (from the right) ----
  var toggle = document.querySelector('[data-menu-toggle]');
  var menu = document.querySelector('[data-mobile-menu]');
  var menuIcon = document.querySelector('[data-menu-icon]');
  var closeBtn = document.querySelector('[data-menu-close]');

  function setMenuOpen(open) {
    if (!menu) return;
    menu.classList.toggle('open', open);
    if (toggle) toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
    if (menuIcon) {
      menuIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    }
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      setMenuOpen(!menu.classList.contains('open'));
    });
  }
  if (closeBtn) {
    closeBtn.addEventListener('click', function () { setMenuOpen(false); });
  }
  if (menu) {
    menu.addEventListener('click', function (e) {
      if (e.target === menu) setMenuOpen(false);
    });
  }
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function () { setMenuOpen(false); });
  });

  // ---- FAQ ----
  document.querySelectorAll('.faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.faq-item');
      var wasOpen = item.classList.contains('open');
      item.parentElement.querySelectorAll('.faq-item.open').forEach(function (el) {
        if (el !== item) el.classList.remove('open');
      });
      item.classList.toggle('open', !wasOpen);
      btn.setAttribute('aria-expanded', String(!wasOpen));
    });
  });

  // ---- SEO smooth expand (all pages) ----
  document.querySelectorAll('[data-seo-toggle]').forEach(function (seoToggle) {
    var targetId = seoToggle.getAttribute('aria-controls');
    var seoMore = targetId ? document.getElementById(targetId) : document.getElementById('seoMoreContent');
    if (!seoMore) {
      seoMore = seoToggle.parentElement && seoToggle.parentElement.querySelector('.seo-more-content');
    }
    if (!seoMore) return;

    if (seoMore.hasAttribute('hidden')) {
      seoMore.removeAttribute('hidden');
      seoMore.classList.remove('is-open');
    }

    seoToggle.addEventListener('click', function () {
      var isOpen = seoToggle.getAttribute('aria-expanded') === 'true';
      var next = !isOpen;
      seoToggle.setAttribute('aria-expanded', String(next));
      if (next) {
        seoMore.classList.add('is-open');
        seoMore.hidden = false;
      } else {
        seoMore.classList.remove('is-open');
        setTimeout(function () {
          if (seoToggle.getAttribute('aria-expanded') !== 'true') {
            seoMore.hidden = true;
          }
        }, prefersReduced ? 0 : 450);
      }
      var label = seoToggle.querySelector('.seo-more-text');
      var mark = seoToggle.querySelector('.seo-more-mark');
      if (label) label.textContent = next ? 'مشاهده کمتر' : 'مشاهده بیشتر';
      if (mark) mark.textContent = next ? '⌃' : '‹';
    });
  });

  // ---- Scroll reveal ----
  if (!prefersReduced && 'IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
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

  // ---- Counters ----
  function animateValue(el, end, duration, suffix) {
    if (prefersReduced) {
      el.textContent = end + (suffix || '');
      return;
    }
    var start = 0;
    var startTime = performance.now();
    var isPlus = suffix === '+';
    function tick(now) {
      var progress = Math.min((now - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 4);
      var current = Math.round(start + (end - start) * eased);
      el.textContent = current + (isPlus ? '+' : (suffix || ''));
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }
  if ('IntersectionObserver' in window) {
    var statsObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.querySelectorAll('[data-count]').forEach(function (num) {
          animateValue(num, parseInt(num.getAttribute('data-count'), 10), 1400, num.getAttribute('data-suffix') || '');
        });
        statsObserver.unobserve(entry.target);
      });
    }, { threshold: 0.4 });
    document.querySelectorAll('.reason-stats').forEach(function (stats) {
      stats.querySelectorAll('div > b').forEach(function (b) {
        var text = b.textContent.trim();
        if (text.indexOf('۲۷') !== -1 || text.indexOf('27') !== -1) {
          b.setAttribute('data-count', '27'); b.setAttribute('data-suffix', '+'); b.textContent = '۰';
        } else if (text.indexOf('۱۸') !== -1 || text.indexOf('18') !== -1) {
          b.setAttribute('data-count', '18'); b.setAttribute('data-suffix', '+'); b.textContent = '۰';
        } else if (text.indexOf('۲۴') !== -1 || text.toLowerCase().indexOf('24') !== -1) {
          b.setAttribute('data-count', '24'); b.setAttribute('data-suffix', 'h'); b.textContent = '۰';
        }
      });
      statsObserver.observe(stats);
    });
  }
});
