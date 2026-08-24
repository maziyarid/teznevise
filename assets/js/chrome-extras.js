/**
 * Front-end extras that must ship with chrome.js: crawler-safe tabs, AI summary, SERP overview.
 *
 * @package Teznevise
 */
function tzRestBase() {
  var rest = (window.tezneviseProduct && window.tezneviseProduct.restUrl)
    || (window.tezneviseAiConfig && window.tezneviseAiConfig.wpjson)
    || '/wp-json/';
  if (rest.charAt(rest.length - 1) !== '/') rest += '/';
  return rest;
}
function tzRestNonce() {
	return (window.tezneviseProduct && window.tezneviseProduct.restNonce)
		|| (window.tezneviseAiConfig && window.tezneviseAiConfig.nonce)
		|| '';
}

function tzJsonResponse(response) {
  return response.text().then(function (raw) {
    var json = null;
    try { json = raw ? JSON.parse(raw) : null; } catch (err) {}
    if (!response.ok) {
      var message = json && (json.message || json.code)
        ? String(json.message || json.code)
        : 'خطای سرور (' + response.status + ')';
      throw new Error(message);
    }
    if (!json) throw new Error('پاسخ سرور معتبر نبود.');
    return json;
  });
}

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
    out.setAttribute('role', 'status');
    out.setAttribute('aria-live', 'polite');
    var idleLabel = btn.textContent;
    btn.addEventListener('click', function () {
      var postId = box.getAttribute('data-ai-summary');
      btn.disabled = true;
      btn.textContent = 'در حال خلاصه‌کردن…';
      out.hidden = false;
      out.classList.remove('is-error');
      out.textContent = 'در حال آماده‌سازی نکات کلیدی…';
      fetch(tzRestBase() + 'teznevise-core/v1/summarise', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': tzRestNonce()
        },
        body: JSON.stringify({ post_id: parseInt(postId, 10) || 0 })
      })
        .then(tzJsonResponse)
        .then(function (j) {
          btn.disabled = false;
          btn.textContent = idleLabel;
          if (!j || !j.success || !j.bullets || !j.bullets.length) {
            out.classList.add('is-error');
            out.textContent = (j && (j.message || j.code)) ? String(j.message || j.code) : 'خلاصه‌ای در دسترس نیست.';
            return;
          }
          var ul = document.createElement('ul');
          j.bullets.forEach(function (line) {
            var li = document.createElement('li');
            li.textContent = line;
            ul.appendChild(li);
          });
          out.replaceChildren(ul);
          if (j.overview) {
            var body = box.querySelector('.tz-ai-overview__body');
            var pending = box.querySelector('.tz-ai-overview__pending');
            if (!body) {
              body = document.createElement('div');
              body.className = 'tz-ai-overview__body';
              box.insertBefore(body, out);
            }
            body.textContent = j.overview;
            if (pending) pending.remove();
            box.classList.add('is-ready');
            box.classList.remove('is-pending');
          }
        })
        .catch(function (err) {
          btn.disabled = false;
          btn.textContent = idleLabel;
          out.classList.add('is-error');
          out.textContent = (err && err.message) ? err.message : 'ارتباط برقرار نشد. دوباره تلاش کنید.';
        });
    });
  });

  document.querySelectorAll('[data-search-overview]').forEach(function (box) {
    var q = box.getAttribute('data-q') || '';
    var body = box.querySelector('[data-overview-body]');
    if (!q || !body) return;
    fetch(tzRestBase() + 'teznevise-core/v1/search-overview?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
      .then(tzJsonResponse)
      .then(function (j) {
        body.textContent = (j && j.overview) ? j.overview : 'جمع‌بندی در دسترس نیست.';
      })
      .catch(function () {
        body.textContent = 'جمع‌بندی در دسترس نیست.';
      });
  });

  function paintDebate(host, items) {
    if (!host || !items || !items.length) return;
    host.innerHTML = '';
    var ol = document.createElement('ol');
    ol.className = 'comment-list tz-ai-thread tz-thread';
    items.forEach(function (item) {
      var li = document.createElement('li');
      li.className = 'tz-ai-comment tz-thread-item';
      var art = document.createElement('article');
      var head = document.createElement('header');
      head.className = 'comment-author tz-thread-item__meta';
      if (item.avatar) {
        var img = document.createElement('img');
        img.className = 'tz-agent-mark';
        img.src = item.avatar;
        img.width = 36;
        img.height = 36;
        img.alt = item.name || '';
        head.appendChild(img);
      }
      var strong = document.createElement('strong');
      strong.className = 'tz-thread-item__name';
      strong.textContent = item.name || '';
      head.appendChild(strong);
      art.appendChild(head);
      var body = document.createElement('div');
      body.className = 'comment-content';
      body.textContent = item.content || '';
      art.appendChild(body);
      li.appendChild(art);
      ol.appendChild(li);
    });
    host.appendChild(ol);
  }

  function pollDebate(postId, host, btn, tries) {
    var status = btn && btn.parentNode ? btn.parentNode.querySelector('[data-ai-debate-status]') : null;
    if (tries > 24) {
      if (btn) { btn.disabled = false; btn.textContent = 'تلاش دوباره'; }
      if (status) status.textContent = 'ادامه گفتگو در صف پردازش ماند. دوباره تلاش کنید یا زمان‌بندی WP-Cron را بررسی کنید.';
      return;
    }
    fetch(tzRestBase() + 'teznevise-core/v1/debate?post_id=' + encodeURIComponent(postId), { credentials: 'same-origin' })
      .then(tzJsonResponse)
      .then(function (j) {
        if (j && j.items && j.items.length) paintDebate(host, j.items);
        if (status) status.textContent = 'گفتگو در حال تکمیل است — ' + String((j && j.count) || 0) + ' پاسخ آماده شده.';
        if (j && (j.job === 'done' || (j.count && j.count > 3))) {
          if (btn) { btn.hidden = true; }
          if (status) status.textContent = 'گفتگوی عامل‌ها کامل شد.';
          return;
        }
        window.setTimeout(function () { pollDebate(postId, host, btn, tries + 1); }, 4000);
      })
      .catch(function () {
        window.setTimeout(function () { pollDebate(postId, host, btn, tries + 1); }, 6000);
      });
  }

  document.querySelectorAll('[data-ai-debate-run]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var postId = btn.getAttribute('data-ai-debate-run');
      var host = document.querySelector('[data-ai-debate-thread]') || btn.parentNode;
      var status = btn.parentNode ? btn.parentNode.querySelector('[data-ai-debate-status]') : null;
      btn.disabled = true;
      btn.textContent = 'در حال تولید…';
      if (status) status.textContent = 'عامل اول در حال بررسی مقاله است…';
      fetch(tzRestBase() + 'teznevise-core/v1/debate-run', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': tzRestNonce() },
        body: JSON.stringify({ post_id: parseInt(postId, 10) || 0 })
      })
        .then(tzJsonResponse)
        .then(function (j) {
          if (j && j.items && j.items.length) {
            paintDebate(host, j.items);
            btn.textContent = 'ادامه گفتگو در صف است…';
            if (status) status.textContent = 'پاسخ اول آماده شد؛ عامل‌های بعدی در صف هستند.';
            pollDebate(postId, host, btn, 0);
            return;
          }
          btn.textContent = (j && j.success)
            ? 'در صف است — چند ثانیه دیگر همین‌جا پر می‌شود.'
            : ((j && j.message) || 'ناموفق بود');
          if (j && j.success) pollDebate(postId, host, btn, 0);
          else btn.disabled = false;
        })
        .catch(function (err) {
          btn.disabled = false;
          btn.textContent = 'تلاش دوباره';
          if (status) status.textContent = (err && err.message) ? err.message : 'ارتباط برقرار نشد.';
        });
    });
  });
});
