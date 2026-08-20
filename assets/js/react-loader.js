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
  if (overlay) {
    overlay.style.zIndex = '5000';
  }
});
