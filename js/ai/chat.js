(function () {
  'use strict';

  function cfg() {
    return window.tezneviseAiConfig || {};
  }

  function appendMsg(log, role, text, name, thinking) {
    var art = document.createElement('article');
    art.className = 'tz-ai-msg is-' + role;
    if (thinking) {
      var det = document.createElement('details');
      det.className = 'tz-ai-think';
      det.innerHTML = '<summary>فرآیند فکر</summary>';
      var pre = document.createElement('pre');
      pre.textContent = thinking;
      det.appendChild(pre);
      art.appendChild(det);
    }
    var bubble = document.createElement('div');
    bubble.className = 'tz-ai-msg__bubble';
    bubble.textContent = text;
    art.appendChild(bubble);
    if (name) {
      var foot = document.createElement('footer');
      foot.className = 'tz-ai-msg__name';
      foot.textContent = name;
      art.appendChild(foot);
    }
    log.appendChild(art);
    log.scrollTop = log.scrollHeight;
  }

  function bind(root) {
    var form = root.querySelector('[data-ai-form]');
    var input = root.querySelector('[data-ai-input]');
    var log = root.querySelector('[data-ai-log]');
    var status = root.querySelector('[data-ai-status]');
    var agentSel = root.querySelector('[data-ai-agent]');
    var collabSel = root.querySelector('[data-ai-collab]');
    var thinkBox = root.querySelector('[data-ai-thinking]');
    if (!form || !input || !log) return;
    var sessionId = '';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = (input.value || '').trim();
      if (text.length < 4) return;
      appendMsg(log, 'user', text, cfg().isLoggedIn ? 'شما' : 'مهمان');
      input.value = '';
      status.hidden = false;
      status.textContent = 'در حال فکر کردن…';
      var body = {
        tool_id: root.getAttribute('data-tool-id') || 'general',
        message: text,
        session_id: sessionId,
        agent_id: agentSel ? agentSel.value : root.getAttribute('data-agent-id'),
        collaboration_mode: collabSel ? collabSel.value : 'single',
        thinking_enabled: !!(thinkBox && thinkBox.checked),
      };
      fetch((cfg().rest_url || '') + 'chat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': cfg().nonce || '',
        },
        body: JSON.stringify(body),
      })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
        .then(function (res) {
          status.hidden = true;
          if (!res.ok || !res.json || !res.json.success) {
            var err = (res.json && (res.json.message || res.json.code)) || 'ارسال ناموفق بود';
            appendMsg(log, 'assistant', String(err), 'سیستم');
            return;
          }
          sessionId = res.json.session_id || sessionId;
          var replies = res.json.replies || [{ content: res.json.content, agent_name: res.json.agent_name, thinking_process: res.json.thinking_process }];
          replies.forEach(function (rep) {
            appendMsg(log, 'assistant', rep.content || '', rep.agent_name || '', thinkBox && thinkBox.checked ? (rep.thinking_process || '') : '');
          });
        })
        .catch(function () {
          status.hidden = true;
          appendMsg(log, 'assistant', 'ارتباط برقرار نشد. دوباره تلاش کنید.', 'سیستم');
        });
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
