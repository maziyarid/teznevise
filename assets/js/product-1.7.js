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
      return !el.hasAttribute('disabled') && el.getClientRects().length > 0;
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

  /* Teznevise Design System v2 — progressively enhanced instant search. */
  var instantForm = document.querySelector('[data-instant-search]');
  var instantResults = document.querySelector('[data-search-results]');
  var instantStatus = document.querySelector('[data-search-status]');
  var instantList = document.querySelector('[data-search-list]');
  var searchTimer = 0;
  var searchRequest = null;

  function setSearchStatus(message) {
    if (instantStatus) instantStatus.textContent = message;
  }
  function clearInstantResults() {
    if (instantList) instantList.textContent = '';
    if (instantResults) instantResults.hidden = true;
    setSearchStatus('');
  }
  function renderInstantResults(items) {
    if (!instantList || !instantResults) return;
    instantList.textContent = '';
    items.forEach(function (item) {
      var row = document.createElement('li');
      var link = document.createElement('a');
      link.href = item.url;
      link.textContent = item.title;
      row.appendChild(link);
      instantList.appendChild(row);
    });
    instantResults.hidden = false;
    setSearchStatus(items.length ? items.length + ' نتیجه پیدا شد.' : 'نتیجه‌ای پیدا نشد.');
  }
  function requestInstantSearch(query) {
    if (!instantForm) return;
    var endpoint = instantForm.getAttribute('data-search-endpoint');
    if (!endpoint) return;
    if (searchRequest) searchRequest.abort();
    searchRequest = new AbortController();
    if (instantResults) instantResults.hidden = false;
    setSearchStatus('در حال جستجو…');
    fetch(endpoint + '?search=' + encodeURIComponent(query) + '&per_page=6&_fields=id,title,url,subtype', {
      credentials: 'same-origin',
      signal: searchRequest.signal
    })
      .then(function (response) {
        if (!response.ok) throw new Error('search');
        return response.json();
      })
      .then(renderInstantResults)
      .catch(function (error) {
        if (error.name === 'AbortError') return;
        if (instantResults) instantResults.hidden = false;
        setSearchStatus('جستجوی سریع در دسترس نیست؛ برای جستجوی کامل Enter را بزنید.');
      });
  }
  if (instantForm && searchInput) {
    searchInput.setAttribute('autocomplete', 'off');
    searchInput.addEventListener('input', function () {
      window.clearTimeout(searchTimer);
      var query = searchInput.value.trim();
      if (query.length < 2) {
        if (searchRequest) searchRequest.abort();
        clearInstantResults();
        return;
      }
      searchTimer = window.setTimeout(function () { requestInstantSearch(query); }, 220);
    });
  }

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
          else if (d && d.data && d.data.message === 'no-coins') out.textContent = 'امکان ثبت این درخواست نیست.';
          else out.textContent = 'پاسخ آماده نشد.';
        })
        .catch(function () { out.textContent = 'ارتباط برقرار نشد.'; });
    });
  }

  /* Teznevise Design System v2 — focus mode and bounded type controls. */
  var focusButton = document.querySelector('[data-reading-focus]');
  var readingSize = 1;
  if (focusButton) {
    focusButton.addEventListener('click', function () {
      var active = document.body.classList.toggle('tz-reading-focus');
      focusButton.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }
  document.querySelectorAll('[data-reading-size]').forEach(function (button) {
    button.addEventListener('click', function () {
      readingSize = button.getAttribute('data-reading-size') === 'increase' ? Math.min(1.25, readingSize + 0.1) : 1;
      document.documentElement.style.setProperty('--tz-reading-size', readingSize + 'rem');
    });
  });

  /* 1.8.7 — reading progress + TOC spy on single posts */
  var progressFill = document.querySelector('.reading-progress span');
  var articleBody = document.querySelector('.blog-post__content');
  if (progressFill && articleBody) {
    var updateProgress = function () {
      var top = articleBody.getBoundingClientRect().top + window.scrollY;
      var height = Math.max(1, articleBody.offsetHeight - window.innerHeight * 0.45);
      var p = (window.scrollY - top + 80) / height;
      progressFill.style.width = (Math.max(0, Math.min(1, p)) * 100) + '%';
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }
  var tocLinks = document.querySelectorAll('.blog-post__toc .post-toc-item');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var tocMap = [];
    tocLinks.forEach(function (link) {
      var raw = (link.getAttribute('href') || '').replace(/^#/, '');
      var id = raw;
      try { id = decodeURIComponent(raw); } catch (e) { id = raw; }
      var heading = document.getElementById(raw) || document.getElementById(id);
      if (heading) tocMap.push({ heading: heading, link: link });
    });
    if (tocMap.length) {
      var setActive = function (active) {
        tocLinks.forEach(function (l) { l.classList.remove('is-active'); });
        if (active) active.classList.add('is-active');
      };
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          tocMap.forEach(function (item) {
            if (item.heading === entry.target) setActive(item.link);
          });
        });
      }, { rootMargin: '-20% 0px -70% 0px', threshold: 0 });
      tocMap.forEach(function (item) { observer.observe(item.heading); });
    }
  }
})();
