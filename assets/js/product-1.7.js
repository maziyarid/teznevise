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
      return !el.hasAttribute('disabled') && el.offsetParent !== null;
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
})();
