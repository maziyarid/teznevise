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
    var showForm = String(text || '').indexOf('[[SHOW_CONTACT_FORM]]') !== -1;
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
    text = String(text || '').replace(FORM_TOKEN, '').trim();
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
    if (role !== 'user' && showForm) {
      renderContactForm(log);
    }
    log.scrollTop = log.scrollHeight;
    return art;
  }

  var FORM_TOKEN = '[[SHOW_CONTACT_FORM]]';

  function renderContactForm(container) {
    if (!container) return;
    try {
      if (sessionStorage.getItem('tz_contact_done')) return;
    } catch (err) {}
    if (container.querySelector('.tz-contact-form-inline')) return;
    var wrap = document.createElement('div');
    wrap.className = 'tz-contact-form-inline';
    wrap.innerHTML = '<form class="tz-cf-form" novalidate>' +
      '<h4 class="tz-cf-title">اطلاعات تماس</h4>' +
      '<p class="tz-cf-sub">برای پیگیری توسط متخصص و دریافت رونوشت این مکالمه، اطلاعاتتان را وارد کنید.</p>' +
      '<div class="tz-cf-row"><div class="tz-cf-field"><label>نام کامل *</label>' +
      '<input name="name" type="text" required placeholder="علی محمدی" autocomplete="name"/></div>' +
      '<div class="tz-cf-field"><label>موبایل *</label>' +
      '<input name="phone" type="tel" required placeholder="09XXXXXXXXX" dir="ltr" autocomplete="tel"/></div></div>' +
      '<div class="tz-cf-field"><label>ایمیل <span>(اختیاری — رونوشت دریافت کنید)</span></label>' +
      '<input name="email" type="email" placeholder="you@example.com" dir="ltr" autocomplete="email"/></div>' +
      '<div class="tz-cf-field"><label>موضوع پژوهش</label>' +
      '<input name="subject" type="text" placeholder="مثلاً پروپوزال دکتری روانشناسی"/></div>' +
      '<div class="tz-cf-actions"><button type="submit" class="tz-cf-submit">ارسال و دریافت رونوشت</button>' +
      '<button type="button" class="tz-cf-skip">بعداً</button></div>' +
      '<p class="tz-cf-privacy">اطلاعات شما کاملاً محرمانه است.</p></form>' +
      '<div class="tz-cf-success" hidden><span class="tz-cf-icon">✓</span><strong>ممنون!</strong> تیم ما با شما تماس خواهد گرفت.</div>';
    var form = wrap.querySelector('form');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = new FormData(form);
      var payload = {
        name: (fd.get('name') || '').toString().trim(),
        phone: (fd.get('phone') || '').toString().trim(),
        email: (fd.get('email') || '').toString().trim(),
        subject: (fd.get('subject') || '').toString().trim(),
        agent: (container.closest('.tz-ai-chat') && container.closest('.tz-ai-chat').getAttribute('data-agent-id')) || 'general',
        history: (window.__tzChatHistory || []).map(function (m) {
          return { role: m.role, content: m.text || m.content || '', name: m.name || '' };
        }),
      };
      if (!payload.name || !payload.phone) {
        if (form.reportValidity) form.reportValidity();
        return;
      }
      fetch((cfg().rest_url || '') + 'contact-lead', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg().nonce || '' },
        body: JSON.stringify(payload),
      }).then(function (r) {
        if (r.ok) {
          wrap.querySelector('.tz-cf-form').hidden = true;
          wrap.querySelector('.tz-cf-success').hidden = false;
          try { sessionStorage.setItem('tz_contact_done', '1'); } catch (err) {}
        }
      });
    });
    wrap.querySelector('.tz-cf-skip').addEventListener('click', function () {
      wrap.remove();
      try { sessionStorage.setItem('tz_contact_done', 'skipped'); } catch (err) {}
    });
    container.appendChild(wrap);
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
    var skillBox = root.querySelector('[data-ai-skills]');
    var activeSkill = '';
    if (!form || !input || !log) return;
    var sessionId = '';
    var greeting = log.innerHTML;
    var history = [];
    var storageKey = 'tz-ai-chat-' + (root.getAttribute('data-tool-id') || 'general');

    function renderSkills(id) {
      activeSkill = '';
      if (!skillBox) return;
      var list = (cfg().skills && cfg().skills[id]) ? cfg().skills[id] : [];
      skillBox.innerHTML = '';
      if (!list.length) {
        skillBox.hidden = true;
        return;
      }
      skillBox.hidden = false;
      list.forEach(function (sk) {
        var b = el('button', 'tz-gpt-skill', sk.name || sk.id);
        b.type = 'button';
        b.setAttribute('data-skill-pick', sk.id);
        b.title = sk.description || sk.name || '';
        b.addEventListener('click', function () {
          var on = activeSkill === sk.id;
          activeSkill = on ? '' : sk.id;
          skillBox.querySelectorAll('[data-skill-pick]').forEach(function (n) {
            n.classList.toggle('is-on', n.getAttribute('data-skill-pick') === activeSkill);
          });
        });
        skillBox.appendChild(b);
      });
    }

    function syncChips(id) {
      chips.forEach(function (btn) {
        var on = btn.getAttribute('data-agent-pick') === id;
        btn.classList.toggle('is-on', on);
        btn.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      renderSkills(id);
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
      renderSkills(agentSel.value);
    } else {
      renderSkills(root.getAttribute('data-agent-id') || '');
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
      history.push({ role: role, text: String(text || '').replace('[[SHOW_CONTACT_FORM]]', '').trim(), name: name || '', thinking: thinking || '', agentId: agentId || '' });
      window.__tzChatHistory = history;
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
      var thinkingOn = !!(thinkBox && thinkBox.checked);
      var collabToggle = root.querySelector('[data-ai-collab-toggle]');
      var collab = collabSel ? collabSel.value : (root.getAttribute('data-collaboration-mode') || 'single');
      if (collabToggle && collabToggle.checked) collab = 'collaborative';
      if (collabToggle && !collabToggle.checked && collab === 'collaborative') collab = 'single';
      if (researchBox && researchBox.checked) collab = 'research';
      if (status) {
        status.hidden = false;
        if (collab === 'research') {
          status.textContent = 'در حال اتصال به رایانه…';
        } else if (collab === 'collaborative' || collab === 'separate') {
          status.innerHTML = '<span class="tz-ai-dots" aria-hidden="true"><i></i><i></i><i></i></span> عامل‌ها در حال هم‌فکری…';
        } else {
          status.innerHTML = '<span class="tz-ai-dots" aria-hidden="true"><i></i><i></i><i></i></span> ' +
            ((meta && meta.name) ? (meta.name + ' در حال فکر کردن…') : 'در حال فکر کردن…');
        }
      }
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
          skill_id: activeSkill || '',
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
          if (replies.length > 1) {
            var banner = el('p', 'tz-ai-collab-label', 'هم‌فکری عامل‌ها');
            log.appendChild(banner);
          }
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

    var handoff = root.querySelector('[data-ai-handoff]');
    if (handoff) {
      handoff.addEventListener('submit', function (e) {
        e.preventDefault();
        var st = handoff.querySelector('[data-handoff-status]');
        var fd = new FormData(handoff);
        var payload = {
          name: fd.get('name') || '',
          phone: fd.get('phone') || '',
          email: fd.get('email') || '',
          history: history,
          session_id: sessionId,
        };
        if (st) { st.hidden = false; st.textContent = 'در حال ارسال…'; }
        fetch((cfg().rest_url || '') + 'chat/handoff', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': cfg().nonce || '',
          },
          body: JSON.stringify(payload),
        })
          .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
          .then(function (res) {
            if (st) st.textContent = (res.ok && res.json && res.json.success)
              ? 'درخواست تماس ثبت شد. تاریخچه گفتگو ایمیل شد.'
              : ((res.json && res.json.message) || 'ارسال ناموفق بود');
          })
          .catch(function () {
            if (st) st.textContent = 'ارسال ناموفق بود';
          });
      });
    }
  }

  function boot() {
    document.querySelectorAll('.tz-ai-chat').forEach(bind);
    var wrap = document.getElementById('tzLiveChat');
    var toggle = document.getElementById('tzLiveChatToggle');
    var panel = document.getElementById('tzLiveChatPanel');
    var closeBtn = document.getElementById('tzLiveChatClose');
    function setOpen(on) {
      if (!panel || !toggle) return;
      panel.hidden = !on;
      toggle.setAttribute('aria-expanded', on ? 'true' : 'false');
      if (on) {
        var ta = panel.querySelector('[data-ai-input]');
        if (ta) ta.focus();
      }
    }
    if (toggle && panel) {
      toggle.addEventListener('click', function () {
        setOpen(!!panel.hidden);
      });
    }
    if (closeBtn) {
      closeBtn.addEventListener('click', function () { setOpen(false); });
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
