/**
 * Hydrate dumped calculator widgets (sample-size and FAQ) when the
 * original WPCode script is missing from post_content.
 *
 * @package Teznevise
 */
(function () {
  'use strict';

  function toFa(n) {
    return String(n).replace(/\d/g, function (d) {
      return '۰۱۲۳۴۵۶۷۸۹'[d];
    });
  }

  function zFor(conf) {
    if (conf === 90) return 1.64485;
    if (conf === 99) return 2.57583;
    return 1.95996;
  }

  function cochran(z, p, e, N) {
    var q = 1 - p;
    var n0 = (z * z * p * q) / (e * e);
    if (!N || N <= 0) return Math.ceil(n0);
    return Math.ceil(n0 / (1 + (n0 - 1) / N));
  }

  function morgan(N) {
    if (!N || N <= 0) return 0;
    var chi2 = 3.841;
    var P = 0.5;
    var d = 0.05;
    return Math.ceil((chi2 * N * P * (1 - P)) / (d * d * (N - 1) + chi2 * P * (1 - P)));
  }

  function stripEmoji(el) {
    if (!el) return;
    el.innerHTML = el.innerHTML
      .replace(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE0F}]/gu, '')
      .trim();
  }

  function iconizeTabs() {
    document.querySelectorAll('.tzss-tab').forEach(function (btn) {
      var t = (btn.textContent || '').replace(/\s+/g, ' ').trim();
      if (t.indexOf('کوکران') !== -1 && !btn.querySelector('i')) {
        btn.innerHTML = '<i class="fa-solid fa-square-root-variable" aria-hidden="true"></i> فرمول کوکران';
      } else if (t.indexOf('مورگان') !== -1 && !btn.querySelector('i')) {
        btn.innerHTML = '<i class="fa-solid fa-table" aria-hidden="true"></i> جدول مورگان';
      } else {
        stripEmoji(btn);
      }
    });
    document.querySelectorAll('.tzss-hero-tag').forEach(stripEmoji);
  }

  function hydrateSampleSize() {
    var card = document.querySelector('.tzss-calc-card');
    if (!card) return;

    var tabs = card.querySelectorAll('.tzss-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var name = tab.getAttribute('data-tab');
        tabs.forEach(function (t) {
          t.classList.toggle('tzss-tab-active', t === tab);
        });
        card.querySelectorAll('.tzss-pane').forEach(function (pane) {
          pane.classList.toggle('tzss-pane-active', pane.id === 'tzss-pane-' + name);
        });
      });
    });

    var pop = document.getElementById('tzss-population');
    var margin = document.getElementById('tzss-margin');
    var proportion = document.getElementById('tzss-proportion');
    var out = document.getElementById('tzss-result-number');
    var rdConf = document.getElementById('tzss-rd-conf');
    var rdMargin = document.getElementById('tzss-rd-margin');
    var rdPop = document.getElementById('tzss-rd-pop');
    var pills = card.querySelectorAll('.tzss-pill[data-conf]');
    var conf = 95;

    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        pills.forEach(function (p) {
          p.classList.toggle('tzss-pill-active', p === pill);
        });
        conf = Number(pill.getAttribute('data-conf')) || 95;
        calc();
      });
    });

    function calc() {
      var N = pop && pop.value !== '' ? Number(pop.value) : 0;
      var e = Number(margin && margin.value ? margin.value : 5) / 100;
      var p = Number(proportion && proportion.value ? proportion.value : 50) / 100;
      if (!e || e <= 0 || !p || p <= 0 || p >= 1) {
        if (out) out.textContent = '—';
        return;
      }
      var n = cochran(zFor(conf), p, e, N);
      if (out) out.textContent = toFa(n);
      if (rdConf) rdConf.textContent = toFa(conf) + '٪';
      if (rdMargin) rdMargin.textContent = toFa(margin && margin.value ? margin.value : 5) + '٪';
      if (rdPop) rdPop.textContent = N ? toFa(N.toLocaleString('en-US')) : 'نامحدود';
    }

    ['input', 'change'].forEach(function (ev) {
      if (pop) pop.addEventListener(ev, calc);
      if (margin) margin.addEventListener(ev, calc);
      if (proportion) proportion.addEventListener(ev, calc);
    });
    calc();

    var morganPop = document.getElementById('tzss-morgan-pop');
    var morganOut = document.getElementById('tzss-morgan-num');
    function calcMorgan() {
      if (!morganOut) return;
      var N = Number(morganPop && morganPop.value ? morganPop.value : 0);
      morganOut.textContent = N > 0 ? toFa(morgan(N)) : '—';
    }
    if (morganPop) morganPop.addEventListener('input', calcMorgan);
    calcMorgan();
  }

  function hydrateFaqs() {
    document.querySelectorAll('.tzss-faq-q').forEach(function (q) {
      q.addEventListener('click', function () {
        var item = q.closest('.tzss-faq-item');
        if (!item) return;
        item.classList.toggle('tzss-faq-open');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    iconizeTabs();
    hydrateSampleSize();
    hydrateFaqs();
  });
})();
