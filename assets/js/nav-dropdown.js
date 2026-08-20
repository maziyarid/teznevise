/**
 * Accessible toggle behaviour for dropdown submenus (.nav-dropdown), shared
 * by the desktop primary menu (.main-nav) and the mobile drawer menu
 * (.mobile-nav-links) -- both are rendered by the same Teznevise_Nav_Walker.
 *
 * Desktop hover only follows the in-flow nav link plus the mega panel
 * after it is open. The closed panel is `display:none`, so hovering the
 * page below the bar cannot keep or open a menu.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  var toggles = document.querySelectorAll('.menu-item.has-dropdown > .nav-dropdown-toggle');
  if (!toggles.length) return;

  var supportsHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
  var closeDelay = 160;
  var closeTimer = null;

  function closeAll(except) {
    toggles.forEach(function (toggle) {
      if (toggle === except) return;
      toggle.setAttribute('aria-expanded', 'false');
      var panel = toggle.parentElement.querySelector(':scope > .nav-dropdown, :scope > ul.nav-dropdown');
      if (!panel) panel = toggle.parentElement.querySelector('.nav-dropdown');
      if (panel) panel.classList.remove('is-open');
      toggle.parentElement.classList.remove('is-open');
    });
  }

  function openToggle(toggle, panel) {
    if (closeTimer) {
      clearTimeout(closeTimer);
      closeTimer = null;
    }
    closeAll(toggle);
    toggle.setAttribute('aria-expanded', 'true');
    panel.classList.add('is-open');
    toggle.parentElement.classList.add('is-open');
  }

  function closeToggle(toggle, panel) {
    toggle.setAttribute('aria-expanded', 'false');
    panel.classList.remove('is-open');
    toggle.parentElement.classList.remove('is-open');
  }

  function scheduleClose(toggle, panel) {
    if (closeTimer) clearTimeout(closeTimer);
    closeTimer = setTimeout(function () {
      closeToggle(toggle, panel);
      closeTimer = null;
    }, closeDelay);
  }

  toggles.forEach(function (toggle) {
    var parentItem = toggle.parentElement;
    var panel = parentItem.querySelector(':scope > .nav-dropdown, :scope > ul.nav-dropdown');
    if (!panel) panel = parentItem.querySelector('.nav-dropdown');
    if (!panel) return;
    var inMobileDrawer = !!toggle.closest('.mobile-nav-links');

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var isOpen = toggle.getAttribute('aria-expanded') === 'true';
      if (isOpen) {
        closeToggle(toggle, panel);
      } else {
        openToggle(toggle, panel);
      }
    });

    if (supportsHover && !inMobileDrawer) {
      var link = parentItem.querySelector(':scope > a.nav-link, :scope > a, :scope > .nav-link');
      var hoverTargets = [panel];
      if (link) hoverTargets.push(link);
      hoverTargets.forEach(function (el) {
        el.addEventListener('mouseenter', function () {
          openToggle(toggle, panel);
        });
        el.addEventListener('mouseleave', function () {
          scheduleClose(toggle, panel);
        });
      });
    }

    parentItem.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeToggle(toggle, panel);
        toggle.focus();
      }
    });
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.menu-item.has-dropdown')) {
      closeAll();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll();
  });
});
