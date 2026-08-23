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

  function agentMeta(idOrName) {
    var list = cfg().agents || [];
    for (var i = 0; i < list.length; i++) {
      if (list[i].id === idOrName || list[i].name === idOrName) return list[i];
    }
    return null;
  }

  function autosize(ta) {
    ta.style.height = 'auto';
    ta.style.height = Math.min(220, Math.max(24, ta.scrollHeight)) + 'px';
  }

  function avatarNode(role, name, agentId) {
    var meta = agentMeta(agentId) || agentMeta(name);
    var wrap = el('span', 'tz-ai-msg__avatar');
    wrap.setAttribute('aria-hidden', 'true');
    if (meta && meta.avatar) {
      var img = document.createElement('img');
      img.src = meta.avatar;
      img.alt = meta.name || name || '';
      img.title = meta.display || meta.name || '';
      img.width = 36;
      img.height = 36;
      wrap.appendChild(img);
    } else {
      wrap.textContent = (name || (role === 'user' ? 'ش' : 'ت')).slice(0, 1);
    }
    if (meta && meta.color) wrap.style.background = meta.color;
    return wrap;
  }

  function typewrite(node, text, done) {
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce || !text) {
      renderRich(node, text);
      if (done) done();
      return;
    }
    var i = 0;
    var step = Math.max(1, Math.ceil(text.length / 90));
    node.textContent = '';
    function tick() {
      i += step;
      node.textContent = text.slice(0, i);
      if (i < text.length) {
        window.requestAnimationFrame(tick);
      } else {
        node.textContent = '';
        renderRich(node, text);
        if (done) done();
      }
    }
    window.requestAnimationFrame(tick);
  }

  function appendMsg(log, role, text, name, thinking, model, agentId, stream) {
    var parsed = splitThought(text);
    if (!thinking && parsed.thought) thinking = parsed.thought;
    text = parsed.public;
    var art = el('article', 'tz-ai-msg is-' + role);
    art.setAttribute('role', 'listitem');
    art.appendChild(avatarNode(role, name, agentId));
    var stack = el('div', 'tz-ai-msg__stack');
    if (name) {
      var meta = el('header', 'tz-ai-msg__meta');
      meta.appendChild(el('strong', '', name));
      stack.appendChild(meta);
    }
    if (thinking) {
      var det = el('details', 'tz-ai-think');
      var sum = el('summary', '', 'مشاهده استدلال درونی');
      det.appendChild(sum);
      var pre = el('pre');
      pre.textContent = thinking;
      det.appendChild(pre);
      if (stream) det.open = true;
      stack.appendChild(det);
    }
    var bubble = el('div', 'tz-ai-msg__bubble');
    if (stream && role !== 'user') {
      typewrite(bubble, text);
    } else {
      renderRich(bubble, text);
    }
    stack.appendChild(bubble);
    if (role !== 'user') {
      var copy = el('button', 'tz-gpt__iconbtn', 'کپی');
      copy.type = 'button';
      copy.addEventListener('click', function () {
        if (navigator.clipboard) navigator.clipboard.writeText(text);
      });
      stack.appendChild(copy);
    }
    art.appendChild(stack);
    log.appendChild(art);
    log.scrollTop = log.scrollHeight;
    return art;
  }

  function splitThought(text) {
    text = String(text || '');
    var m = text.match(/<thought>([\s\S]*?)<\/thought>/i) || text.match(/<think>([\s\S]*?)<\/think>/i);
    if (!m) return { thought: '', public: text };
    return { thought: m[1].trim(), public: text.replace(m[0], '').trim() };
  }

  function renderRich(node, text) {
    var parts = String(text || '').split(/```/);
    parts.forEach(function (part, i) {
      if (i % 2 === 1) {
        var pre = el('pre', 'tz-ai-code');
        pre.textContent = part.replace(/^\w+\n/, '');
        node.appendChild(pre);
      } else {
        var span = el('span');
        span.textContent = part;
        node.appendChild(span);
      }
    });
  }

  function bind(root) {
    var form = root.querySelector('[data-ai-form]');
    var input = root.querySelector('[data-ai-input]');
    var log = root.querySelector('[data-ai-log]');
    var status = root.querySelector('[data-ai-status]');
    var agentSel = root.querySelector('[data-ai-agent]');
    var collabSel = root.querySelector('[data-ai-collab]');
    var thinkBox = root.querySelector('[data-ai-thinking]');
    var researchBox = root.querySelector('[data-ai-research]');
    var fullBtn = root.querySelector('[data-ai-full]');
    var newBtn = root.querySelector('[data-ai-new]');
    var chips = root.querySelectorAll('[data-agent-pick]');
    if (!form || !input || !log) return;
    var sessionId = '';
    var greeting = log.innerHTML;
    var history = [];
    var storageKey = 'tz-ai-chat-' + (root.getAttribute('data-tool-id') || 'general');

    function syncChips(id) {
      chips.forEach(function (btn) {
        var on = btn.getAttribute('data-agent-pick') === id;
        btn.classList.toggle('is-on', on);
        btn.setAttribute('aria-selected', on ? 'true' : 'false');
      });
    }

    chips.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-agent-pick');
        if (agentSel) agentSel.value = id;
        root.setAttribute('data-agent-id', id);
        syncChips(id);
      });
    });
    if (agentSel) {
      agentSel.addEventListener('change', function () {
        syncChips(agentSel.value);
      });
    }

    function persist() {
      try {
        window.localStorage.setItem(storageKey, JSON.stringify({ sessionId: sessionId, messages: history }));
      } catch (err) {}
    }

    function restore() {
      try {
        var raw = window.localStorage.getItem(storageKey);
        if (!raw) return;
        var data = JSON.parse(raw);
        if (!data || !data.messages || !data.messages.length) return;
        sessionId = data.sessionId || '';
        log.innerHTML = '';
        data.messages.forEach(function (m) {
          appendMsg(log, m.role, m.text, m.name, m.thinking || '', '', m.agentId || '');
          history.push(m);
        });
      } catch (err) {}
    }

    function remember(role, text, name, thinking, agentId) {
      history.push({ role: role, text: text, name: name || '', thinking: thinking || '', agentId: agentId || '' });
      if (history.length > 40) history = history.slice(-40);
      persist();
    }

    if (input) {
      input.addEventListener('input', function () { autosize(input); });
      autosize(input);
    }

    if (fullBtn) {
      fullBtn.addEventListener('click', function () {
        var on = root.classList.toggle('is-full');
        fullBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
        fullBtn.textContent = on ? 'خروج از تمام‌صفحه' : 'تمام‌صفحه';
        document.body.classList.toggle('tz-ai-lock', on);
      });
    }

    if (newBtn) {
      newBtn.addEventListener('click', function () {
        sessionId = '';
        history = [];
        persist();
        log.innerHTML = greeting;
        input.value = '';
        autosize(input);
        input.focus();
      });
    }

    restore();

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = (input.value || '').trim();
      if (text.length < 4) return;
      appendMsg(log, 'user', text, cfg().isLoggedIn ? 'شما' : 'مهمان');
      remember('user', text, cfg().isLoggedIn ? 'شما' : 'مهمان', '');
      input.value = '';
      autosize(input);
      var agentId = agentSel ? agentSel.value : root.getAttribute('data-agent-id');
      var meta = agentMeta(agentId);
      if (status) {
        status.hidden = false;
        status.innerHTML = '<span class="tz-ai-dots" aria-hidden="true"><i></i><i></i><i></i></span> ' +
          ((meta && meta.name) ? (meta.name + ' در حال فکر کردن…') : 'در حال فکر کردن…');
      }
      var thinkingOn = !!(thinkBox && thinkBox.checked);
      var collab = collabSel ? collabSel.value : 'single';
      if (researchBox && researchBox.checked) collab = 'research';
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
          agent_id: agentId,
          collaboration_mode: collab,
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
            appendMsg(log, 'assistant', rep.content || '', rep.agent_name || '', thinkingOn ? (rep.thinking_process || '') : '', '', agentId, true);
            remember('assistant', rep.content || '', rep.agent_name || '', thinkingOn ? (rep.thinking_process || '') : '', agentId);
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
        form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true }));
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
