/* Teznevise 1.7 — search overlay, share rewards, FAB polish */
(function () {
  'use strict';

  var searchBtn = document.querySelector('[data-search-open]');
  var searchOverlay = document.querySelector('.search-overlay');
  var searchClose = document.querySelector('.search-close');
  var searchInput = document.querySelector('.search-input');

  function openSearch(e) {
    if (e) e.preventDefault();
    if (!searchOverlay) return;
    searchOverlay.hidden = false;
    searchOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    if (searchInput) setTimeout(function () { searchInput.focus(); }, 80);
  }
  function closeSearch() {
    if (!searchOverlay) return;
    searchOverlay.classList.remove('open');
    searchOverlay.hidden = true;
    document.body.style.overflow = '';
  }
  if (searchBtn) searchBtn.addEventListener('click', openSearch);
  if (searchClose) searchClose.addEventListener('click', closeSearch);
  if (searchOverlay) {
    searchOverlay.addEventListener('click', function (e) {
      if (e.target === searchOverlay) closeSearch();
    });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSearch();
  });

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
})();
