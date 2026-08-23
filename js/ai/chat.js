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

  function icon(name) {
    var i = document.createElement('i');
    i.className = 'fa-solid ' + name;
    i.setAttribute('aria-hidden', 'true');
    return i;
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

  function thinkSummary(live) {
    var sum = el('summary', 'tz-ai-think__sum');
    sum.appendChild(icon(live ? 'fa-spinner fa-spin' : 'fa-lightbulb'));
    var lab = el('span', 'tz-ai-think__label', live ? 'در حال استدلال' : 'استدلال');
    sum.appendChild(lab);
    var chev = el('span', 'tz-ai-think__chev');
    chev.appendChild(icon('fa-chevron-down'));
    sum.appendChild(chev);
    return sum;
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
    var det = null;
    if (thinking || stream) {
      det = el('details', 'tz-ai-think' + (stream ? ' is-live' : ''));
      det.appendChild(thinkSummary(!!stream && !thinking));
      var pre = el('pre', 'tz-ai-think__stream');
      pre.textContent = thinking || '';
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
      var copy = el('button', 'tz-gpt__iconbtn');
      copy.type = 'button';
      copy.setAttribute('aria-label', 'کپی');
      copy.title = 'کپی';
      copy.appendChild(icon('fa-copy'));
      copy.addEventListener('click', function () {
        if (!navigator.clipboard) return;
        navigator.clipboard.writeText(text).then(function () {
          var ic = copy.querySelector('i');
          if (ic) ic.className = 'fa-solid fa-check';
          copy.setAttribute('aria-label', 'کپی شد');
          window.setTimeout(function () {
            if (ic) ic.className = 'fa-solid fa-copy';
            copy.setAttribute('aria-label', 'کپی');
          }, 1400);
        });
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

  function startLiveThought(log, name, agentId) {
    var art = el('article', 'tz-ai-msg is-assistant is-pending');
    art.setAttribute('role', 'listitem');
    art.appendChild(avatarNode('assistant', name, agentId));
    var stack = el('div', 'tz-ai-msg__stack');
    if (name) {
      var meta = el('header', 'tz-ai-msg__meta');
      meta.appendChild(el('strong', '', name));
      stack.appendChild(meta);
    }
    var det = el('details', 'tz-ai-think is-live');
    det.open = true;
    var sum = thinkSummary(true);
    det.appendChild(sum);
    var pre = el('pre', 'tz-ai-think__stream');
    var phases = ['در حال اتصال به مدل…', 'خواندن سؤال پژوهشی…', 'چیدن استدلال…', 'نوشتن پاسخ…'];
    pre.textContent = phases[0];
    det.appendChild(pre);
    stack.appendChild(det);
    var bubble = el('div', 'tz-ai-msg__bubble tz-ai-msg__bubble--wait');
    bubble.innerHTML = '<span class="tz-ai-dots" aria-hidden="true"><i></i><i></i><i></i></span>';
    stack.appendChild(bubble);
    art.appendChild(stack);
    log.appendChild(art);
    log.scrollTop = log.scrollHeight;
    var closedByUser = false;
    det.addEventListener('toggle', function () {
      if (!det.open) closedByUser = true;
    });
    var started = Date.now();
    var step = 0;
    function tickLabel() {
      var sec = Math.max(1, Math.round((Date.now() - started) / 1000));
      var lab = sum.querySelector('.tz-ai-think__label');
      if (lab) lab.textContent = 'در حال استدلال · ' + sec + 'ث';
    }
    tickLabel();
    var timer = window.setInterval(function () {
      step = (step + 1) % phases.length;
      if (!pre.dataset.locked) pre.textContent = phases[step];
      tickLabel();
      if (!closedByUser) log.scrollTop = log.scrollHeight;
    }, 1200);
    return {
      art: art,
      det: det,
      pre: pre,
      bubble: bubble,
      stop: function (thought, answer, stream) {
        window.clearInterval(timer);
        pre.dataset.locked = '1';
        var sec = Math.max(1, Math.round((Date.now() - started) / 1000));
        var spin = sum.querySelector('.fa-spinner');
        if (spin) {
          spin.className = 'fa-solid fa-lightbulb';
        }
        var lab = sum.querySelector('.tz-ai-think__label');
        if (lab) lab.textContent = thought ? ('استدلال · ' + sec + 'ث') : 'استدلال';
        det.classList.remove('is-live');
        if (thought) pre.textContent = thought;
        else if (!pre.textContent) pre.textContent = 'استدلال کوتاه برای این پاسخ ثبت نشد.';
        if (!closedByUser) det.open = !!thought;
        bubble.classList.remove('tz-ai-msg__bubble--wait');
        bubble.innerHTML = '';
        if (stream) typewrite(bubble, answer || '');
        else renderRich(bubble, answer || '');
        art.classList.remove('is-pending');
        if (!closedByUser) log.scrollTop = log.scrollHeight;
      },
      fail: function (msg) {
        window.clearInterval(timer);
        det.classList.remove('is-live');
        pre.textContent = '';
        det.hidden = true;
        bubble.classList.remove('tz-ai-msg__bubble--wait');
        bubble.textContent = msg || 'ارسال ناموفق بود';
        art.classList.remove('is-pending');
      }
    };
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

  function bindToggle(btn, box) {
    if (!btn || !box) return;
    btn.addEventListener('click', function () {
      box.checked = !box.checked;
      btn.setAttribute('aria-pressed', box.checked ? 'true' : 'false');
      btn.classList.toggle('is-on', box.checked);
    });
    btn.classList.toggle('is-on', !!box.checked);
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
    var menu = root.querySelector('[data-agent-menu]');
    var menuBtn = root.querySelector('[data-agent-menu-toggle]');
    var menuList = menu ? menu.querySelector('.tz-gpt-model__list') : null;
    var labelEl = root.querySelector('[data-agent-label]');
    var activeSkill = '';
    if (!form || !input || !log) return;
    var sessionId = '';
    var greeting = log.innerHTML;
    var history = [];
    var storageKey = 'tz-ai-chat-' + (root.getAttribute('data-tool-id') || 'general');
    var busy = false;
    var abortCtl = null;
    var stopBtn = root.querySelector('[data-ai-stop]');
    var sendBtn = form.querySelector('.tz-gpt-send');

    function setBusy(on) {
      busy = !!on;
      log.setAttribute('aria-busy', on ? 'true' : 'false');
      if (sendBtn) sendBtn.hidden = !!on;
      if (stopBtn) stopBtn.hidden = !on;
      if (input) input.disabled = !!on;
    }

    bindToggle(root.querySelector('[data-ai-thinking-btn]'), thinkBox);
    bindToggle(root.querySelector('[data-ai-collab-btn]'), root.querySelector('[data-ai-collab-toggle]'));
    bindToggle(root.querySelector('[data-ai-research-btn]'), researchBox);

    var handoffToggle = root.querySelector('[data-ai-handoff-toggle]');
    var handoffForm = root.querySelector('[data-ai-handoff]');
    if (handoffToggle && handoffForm) {
      handoffToggle.addEventListener('click', function () {
        var on = handoffForm.hidden;
        handoffForm.hidden = !on;
        handoffToggle.setAttribute('aria-expanded', on ? 'true' : 'false');
        handoffToggle.classList.toggle('is-on', on);
      });
    }

    function closeMenu() {
      if (!menuList || !menuBtn) return;
      menuList.hidden = true;
      menuBtn.setAttribute('aria-expanded', 'false');
      root.classList.remove('is-picking');
      var scrim = root.querySelector('[data-agent-menu-scrim]');
      if (scrim) scrim.hidden = true;
    }
    function openMenu() {
      if (!menuList || !menuBtn) return;
      menuList.hidden = false;
      menuBtn.setAttribute('aria-expanded', 'true');
      root.classList.add('is-picking');
      var scrim = root.querySelector('[data-agent-menu-scrim]');
      if (!scrim) {
        scrim = el('button', 'tz-gpt-model__scrim');
        scrim.type = 'button';
        scrim.setAttribute('data-agent-menu-scrim', '');
        scrim.setAttribute('aria-label', 'بازگشت به گفتگو');
        scrim.addEventListener('click', function (ev) {
          ev.preventDefault();
          closeMenu();
        });
        var header = root.querySelector('.tz-gpt__top');
        if (header && header.nextSibling) root.insertBefore(scrim, header.nextSibling);
        else root.appendChild(scrim);
      }
      scrim.hidden = false;
    }
    if (menuBtn && menuList) {
      menuBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (menuList.hidden) openMenu(); else closeMenu();
      });
      document.addEventListener('click', function (e) {
        var s = root.querySelector('[data-agent-menu-scrim]');
        if (menu && !menu.contains(e.target) && !(s && s.contains(e.target))) {
          closeMenu();
        }
      });
      var doneBtn = menu.querySelector('[data-agent-menu-done]');
      if (doneBtn) {
        doneBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          closeMenu();
          menuBtn.focus();
        });
      }
      menuBtn.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openMenu();
          var first = menuList.querySelector('[data-agent-pick]');
          if (first) first.focus();
        } else if (e.key === 'Escape') {
          closeMenu();
        }
      });
      menuList.addEventListener('keydown', function (e) {
        var opts = Array.prototype.slice.call(menuList.querySelectorAll('[data-agent-pick]'));
        var i = opts.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          (opts[i + 1] || opts[0]).focus();
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          (opts[i - 1] || opts[opts.length - 1]).focus();
        } else if (e.key === 'Home') {
          e.preventDefault();
          if (opts[0]) opts[0].focus();
        } else if (e.key === 'End') {
          e.preventDefault();
          if (opts.length) opts[opts.length - 1].focus();
        } else if (e.key === 'Escape') {
          e.preventDefault();
          closeMenu();
          menuBtn.focus();
        }
      });
    }

    if (stopBtn) {
      stopBtn.addEventListener('click', function () {
        if (abortCtl) abortCtl.abort();
      });
    }

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
      if (labelEl) {
        var meta = agentMeta(id);
        labelEl.textContent = (meta && (meta.name || meta.display)) || id;
        var img = menuBtn && menuBtn.querySelector('img');
        if (img && meta && meta.avatar) img.src = meta.avatar;
      }
      renderSkills(id);
    }

    chips.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-agent-pick');
        if (agentSel) agentSel.value = id;
        root.setAttribute('data-agent-id', id);
        syncChips(id);
        closeMenu();
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
        var ic = fullBtn.querySelector('i');
        if (ic) ic.className = 'fa-solid ' + (on ? 'fa-compress' : 'fa-expand');
        document.body.classList.toggle('tz-ai-lock', on);
      });
    }

    if (newBtn) {
      newBtn.addEventListener('click', function () {
        if (abortCtl) abortCtl.abort();
        setBusy(false);
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
      if (busy) return;
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
      var agentLabel = (meta && meta.name) ? meta.name : 'تزنویسه';
      var live = startLiveThought(log, agentLabel, agentId);
      abortCtl = (typeof AbortController !== 'undefined') ? new AbortController() : null;
      setBusy(true);
      if (status) {
        status.hidden = true;
      }
      fetch((cfg().rest_url || '') + 'chat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': cfg().nonce || '',
        },
        signal: abortCtl ? abortCtl.signal : undefined,
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
          setBusy(false);
          if (!res.ok || !res.json || !res.json.success) {
            var err = (res.json && (res.json.message || res.json.code)) || 'ارسال ناموفق بود';
            live.fail(String(err));
            return;
          }
          sessionId = res.json.session_id || sessionId;
          var replies = res.json.replies || [{ content: res.json.content, agent_name: res.json.agent_name, thinking_process: res.json.thinking_process, model: res.json.model }];
          var first = replies[0] || {};
          var parsed = splitThought(first.content || '');
          var thought = thinkingOn ? (first.thinking_process || parsed.thought || '') : (parsed.thought || '');
          var answer = parsed.public || first.content || '';
          live.stop(thought, String(answer || '').replace(FORM_TOKEN, '').trim(), true);
          remember('assistant', first.content || '', first.agent_name || agentLabel, thought, agentId);
          if (replies.length > 1) {
            var banner = el('p', 'tz-ai-collab-label', 'هم‌فکری عامل‌ها');
            log.appendChild(banner);
            replies.slice(1).forEach(function (rep) {
              appendMsg(log, 'assistant', rep.content || '', rep.agent_name || '', thinkingOn ? (rep.thinking_process || '') : '', '', agentId, true);
              remember('assistant', rep.content || '', rep.agent_name || '', thinkingOn ? (rep.thinking_process || '') : '', agentId);
            });
          }
          if (String(first.content || '').indexOf(FORM_TOKEN) !== -1) {
            renderContactForm(log);
          }
        })
        .catch(function (err) {
          setBusy(false);
          if (err && err.name === 'AbortError') {
            live.fail('تولید متوقف شد.');
            return;
          }
          live.fail('ارتباط برقرار نشد. دوباره تلاش کنید.');
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
    function dismissPicker(rootEl) {
      if (!rootEl) return;
      rootEl.classList.remove('is-picking');
      var list = rootEl.querySelector('.tz-gpt-model__list');
      var btn = rootEl.querySelector('[data-agent-menu-toggle]');
      var scrim = rootEl.querySelector('[data-agent-menu-scrim]');
      if (list) list.hidden = true;
      if (btn) btn.setAttribute('aria-expanded', 'false');
      if (scrim) scrim.hidden = true;
    }
    function setOpen(on) {
      if (!panel || !toggle) return;
      if (!on) dismissPicker(panel);
      panel.hidden = !on;
      toggle.setAttribute('aria-expanded', on ? 'true' : 'false');
      document.body.classList.toggle('tz-livechat-open', on);
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
      closeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (panel && panel.classList.contains('is-picking')) {
          dismissPicker(panel);
          return;
        }
        setOpen(false);
      });
    }
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape' || !panel || panel.hidden) return;
      if (panel.classList.contains('is-picking')) {
        dismissPicker(panel);
        var mb = panel.querySelector('[data-agent-menu-toggle]');
        if (mb) mb.focus();
        return;
      }
      setOpen(false);
      if (toggle) toggle.focus();
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
