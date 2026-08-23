/**
 * Front-end extras that must ship with chrome.js: crawler-safe tabs, AI summary, SERP overview.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-comment-tabs]').forEach(function (tabs) {
    var root = tabs.closest('.tz-comments') || document;
    tabs.querySelectorAll('[role="tab"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = btn.getAttribute('aria-controls');
        tabs.querySelectorAll('[role="tab"]').forEach(function (other) {
          other.setAttribute('aria-selected', other === btn ? 'true' : 'false');
        });
        root.querySelectorAll('.tz-comment-panel').forEach(function (panel) {
          var on = panel.id === target;
          panel.hidden = !on;
        });
      });
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-ai-summary]').forEach(function (box) {
    var btn = box.querySelector('[data-ai-summary-btn]');
    var out = box.querySelector('[data-ai-summary-out]');
    if (!btn || !out) return;
    btn.addEventListener('click', function () {
      var postId = box.getAttribute('data-ai-summary');
      btn.disabled = true;
      btn.textContent = 'در حال خلاصه‌کردن…';
      out.hidden = false;
      out.textContent = 'در حال آماده‌سازی نکات کلیدی…';
      var rest = (window.tezneviseProduct && window.tezneviseProduct.restUrl) || '/wp-json/';
      var nonce = (window.tezneviseProduct && window.tezneviseProduct.restNonce) || '';
      fetch(rest + 'teznevise-core/v1/summarise', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce
        },
        body: JSON.stringify({ post_id: parseInt(postId, 10) || 0, nonce: nonce })
      })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          btn.disabled = false;
          btn.textContent = 'خلاصه‌کردن نکات کلیدی';
          if (!j || !j.success || !j.bullets || !j.bullets.length) {
            out.textContent = 'خلاصه‌ای در دسترس نیست.';
            return;
          }
          var ul = document.createElement('ul');
          j.bullets.forEach(function (line) {
            var li = document.createElement('li');
            li.textContent = line;
            ul.appendChild(li);
          });
          out.replaceChildren(ul);
        })
        .catch(function () {
          btn.disabled = false;
          btn.textContent = 'خلاصه‌کردن نکات کلیدی';
          out.textContent = 'ارتباط برقرار نشد.';
        });
    });
  });

  document.querySelectorAll('[data-search-overview]').forEach(function (box) {
    var q = box.getAttribute('data-q') || '';
    var body = box.querySelector('[data-overview-body]');
    if (!q || !body) return;
    var rest = (window.tezneviseProduct && window.tezneviseProduct.restUrl) || '/wp-json/';
    fetch(rest + 'teznevise-core/v1/search-overview?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        body.textContent = (j && j.overview) ? j.overview : 'جمع‌بندی در دسترس نیست.';
      })
      .catch(function () {
        body.textContent = 'جمع‌بندی در دسترس نیست.';
      });
  });
});
