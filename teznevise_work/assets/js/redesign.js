document.addEventListener('DOMContentLoaded', function () {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---- Mobile menu with icon swap ----
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');
  const menuIcon = document.querySelector('[data-menu-icon]');

  function setMenuOpen(open) {
    if (!menu || !toggle) return;
    menu.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
    if (menuIcon) {
      menuIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    }
  }

  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      const isOpen = !menu.classList.contains('open');
      setMenuOpen(isOpen);
    });
  }

  // Close on in-page anchors
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', () => setMenuOpen(false));
  });

  // Close when clicking a mobile menu link that navigates away
  if (menu) {
    menu.querySelectorAll('a[href]:not([href^="#"])').forEach((a) => {
      a.addEventListener('click', () => {
        setTimeout(() => setMenuOpen(false), 80);
      });
    });
  }

  // FAQ accordion
  document.querySelectorAll('.faq-q').forEach((btn) => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const wasOpen = item.classList.contains('open');
      item.parentElement.querySelectorAll('.faq-item.open').forEach((el) => {
        if (el !== item) el.classList.remove('open');
      });
      item.classList.toggle('open', !wasOpen);
      btn.setAttribute('aria-expanded', String(!wasOpen));
    });
  });

  // SEO progressive disclosure
  const seoToggle = document.querySelector('[data-seo-toggle]');
  const seoMore = document.getElementById('seoMoreContent');
  if (seoToggle && seoMore) {
    seoToggle.addEventListener('click', () => {
      const isOpen = seoToggle.getAttribute('aria-expanded') === 'true';
      seoToggle.setAttribute('aria-expanded', String(!isOpen));
      seoMore.hidden = isOpen;
      const label = seoToggle.querySelector('.seo-more-text');
      const mark = seoToggle.querySelector('.seo-more-mark');
      if (label) label.textContent = isOpen ? 'مشاهده بیشتر' : 'مشاهده کمتر';
      if (mark) mark.textContent = isOpen ? '‹' : '⌃';
    });
  }

  // Scroll reveal
  if (!prefersReduced && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach((el) => {
      revealObserver.observe(el);
    });
  } else {
    document.querySelectorAll('[data-reveal], [data-reveal-stagger]').forEach((el) => {
      el.classList.add('is-visible');
    });
  }

  // Animated counters
  function animateValue(el, end, duration, suffix) {
    if (prefersReduced) {
      el.textContent = end + (suffix || '');
      return;
    }
    const start = 0;
    const startTime = performance.now();
    const isPlus = suffix === '+';
    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 4);
      const current = Math.round(start + (end - start) * eased);
      el.textContent = current + (isPlus ? '+' : (suffix || ''));
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const panel = entry.target;
        panel.querySelectorAll('[data-count]').forEach((num) => {
          const target = parseInt(num.getAttribute('data-count'), 10);
          const suffix = num.getAttribute('data-suffix') || '';
          animateValue(num, target, 1400, suffix);
        });
        statsObserver.unobserve(panel);
      });
    },
    { threshold: 0.4 }
  );

  document.querySelectorAll('.reason-stats').forEach((stats) => {
    stats.querySelectorAll('div > b').forEach((b) => {
      const text = b.textContent.trim();
      if (text.includes('۲۷') || text.includes('27')) {
        b.setAttribute('data-count', '27');
        b.setAttribute('data-suffix', '+');
        b.textContent = '۰';
      } else if (text.includes('۱۸') || text.includes('18')) {
        b.setAttribute('data-count', '18');
        b.setAttribute('data-suffix', '+');
        b.textContent = '۰';
      } else if (text.includes('۲۴') || text.toLowerCase().includes('24')) {
        b.setAttribute('data-count', '24');
        b.setAttribute('data-suffix', 'h');
        b.textContent = '۰';
      }
    });
    statsObserver.observe(stats);
  });

  // Light parallax on hero visual
  if (!prefersReduced) {
    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual) {
      window.addEventListener(
        'scroll',
        () => {
          const rect = heroVisual.getBoundingClientRect();
          if (rect.bottom < 0 || rect.top > window.innerHeight) return;
          const progress = (window.innerHeight - rect.top) / (window.innerHeight + rect.height);
          const y = (progress - 0.5) * 18;
          heroVisual.style.transform = `translateY(${y}px)`;
        },
        { passive: true }
      );
    }
  }
});
