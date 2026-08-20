/**
 * Front-end boot that matches the React SiteShell loader.
 * Does not replace WordPress content — it only wires chrome the same way
 * SiteShell.tsx does: sticky header, search overlay above the bar, and a
 * column flex page so the footer sits at the bottom.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  var root = document.documentElement;
  if (!root.getAttribute('lang')) {
    root.setAttribute('lang', 'fa');
  }
  if (!root.getAttribute('dir')) {
    root.setAttribute('dir', 'rtl');
  }
  document.body.classList.add('tz-react-shell');

  var overlay = document.getElementById('teznevise-search-overlay');
  if (!overlay) {
    return;
  }

  overlay.style.position = 'fixed';
  overlay.style.inset = '0px';
  overlay.style.top = '0px';
  overlay.style.right = '0px';
  overlay.style.bottom = '0px';
  overlay.style.left = '0px';
  overlay.style.zIndex = '5000';
  overlay.style.transform = 'none';
  overlay.style.alignItems = 'center';
  overlay.style.justifyContent = 'center';

  function searchNodes() {
    return Array.prototype.slice.call(
      overlay.querySelectorAll('a[href], button:not([disabled]), input, textarea, select, [tabindex]:not([tabindex="-1"])')
    ).filter(function (el) {
      return !el.hasAttribute('disabled') && el.getClientRects().length > 0;
    });
  }

  overlay.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || overlay.hidden) {
      return;
    }
    var nodes = searchNodes();
    if (!nodes.length) {
      return;
    }
    var first = nodes[0];
    var last = nodes[nodes.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  });
});
