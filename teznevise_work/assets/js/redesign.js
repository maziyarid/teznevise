document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');
  if (toggle && menu) toggle.addEventListener('click', () => menu.classList.toggle('open'));

  document.querySelectorAll('.faq-q').forEach((btn) => {
    btn.addEventListener('click', () => btn.closest('.faq-item').classList.toggle('open'));
  });

  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', () => {
      if(menu) menu.classList.remove('open');
    });
  });


  // Progressive disclosure for the long SEO copy on the home page.
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
});
