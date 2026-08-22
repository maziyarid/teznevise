(function () {
  'use strict';

  function cfg() {
    return window.tezneviseAiConfig || {};
  }

  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text) n.textContent = text;
    return n;
  }

  function appendMsg(log, role, text, name, thinking, model) {
    var art = el('article', 'tz-ai-msg is-' + role);
    if (name || model) {
      var meta = el('header', 'tz-ai-msg__meta');
      if (name) meta.appendChild(el('strong', '', name));
      if (model) meta.appendChild(el('span', 'tz-ai-msg__model', model));
      art.appendChild(meta);
    }
    if (thinking) {
      var det = el('details', 'tz-ai-think');
      det.open = false;
      var sum = el('summary', '', 'فرآیند فکر');
      det.appendChild(sum);
      var pre = el('pre');
      pre.textContent = thinking;
      det.appendChild(pre);
      art.appendChild(det);
    }
    var bubble = el('div', 'tz-ai-msg__bubble');
    bubble.textContent = text;
    art.appendChild(bubble);
    log.appendChild(art);
    log.scrollTop = log.scrollHeight;
    return art;
  }

  function bind(root) {
    var form = root.querySelector('[data-ai-form]');
    var input = root.querySelector('[data-ai-input]');
    var log = root.querySelector('[data-ai-log]');
    var status = root.querySelector('[data-ai-status]');
    var agentSel = root.querySelector('[data-ai-agent]');
    var collabSel = root.querySelector('[data-ai-collab]');
    var thinkBox = root.querySelector('[data-ai-thinking]');
    var fullBtn = root.querySelector('[data-ai-full]');
    if (!form || !input || !log) return;
    var sessionId = '';

    if (fullBtn) {
      fullBtn.addEventListener('click', function () {
        var on = root.classList.toggle('is-full');
        fullBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
        fullBtn.textContent = on ? 'خروج از تمام‌صفحه' : 'تمام‌صفحه';
        document.body.classList.toggle('tz-ai-lock', on);
      });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = (input.value || '').trim();
      if (text.length < 4) return;
      appendMsg(log, 'user', text, cfg().isLoggedIn ? 'شما' : 'مهمان');
      input.value = '';
      if (status) {
        status.hidden = false;
        status.innerHTML = '<span class="tz-ai-dots" aria-hidden="true"><i></i><i></i><i></i></span> در حال فکر کردن…';
      }
      var thinkingOn = !!(thinkBox && thinkBox.checked);
      fetch((cfg().rest_url || '') + 'chat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': cfg().nonce || '',
        },
        body: JSON.stringify({
          tool_id: root.getAttribute('data-tool-id') || 'general',
          message: text,
          session_id: sessionId,
          agent_id: agentSel ? agentSel.value : root.getAttribute('data-agent-id'),
          collaboration_mode: collabSel ? collabSel.value : 'single',
          thinking_enabled: thinkingOn,
        }),
      })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
        .then(function (res) {
          if (status) status.hidden = true;
          if (!res.ok || !res.json || !res.json.success) {
            var err = (res.json && (res.json.message || res.json.code)) || 'ارسال ناموفق بود';
            appendMsg(log, 'assistant', String(err), 'سیستم');
            return;
          }
          sessionId = res.json.session_id || sessionId;
          var replies = res.json.replies || [{ content: res.json.content, agent_name: res.json.agent_name, thinking_process: res.json.thinking_process, model: res.json.model }];
          replies.forEach(function (rep) {
            appendMsg(log, 'assistant', rep.content || '', rep.agent_name || '', thinkingOn ? (rep.thinking_process || '') : '', rep.model || '');
          });
        })
        .catch(function () {
          if (status) status.hidden = true;
          appendMsg(log, 'assistant', 'ارتباط برقرار نشد. دوباره تلاش کنید.', 'سیستم');
        });
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit', { cancelable: true }));
      }
    });
  }

  function boot() {
    document.querySelectorAll('.tz-ai-chat').forEach(bind);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
