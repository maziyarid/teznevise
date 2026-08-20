/**
 * Accessible toggle behaviour for dropdown submenus (.nav-dropdown), shared
 * by the desktop primary menu (.main-nav) and the mobile drawer menu
 * (.mobile-nav-links) -- both are rendered by the same Teznevise_Nav_Walker.
 *
 * The submenu is opened/closed exclusively by the dedicated
 * `.nav-dropdown-toggle` button, never by intercepting the parent link
 * itself, so the link always stays navigable. On pointer-capable
 * ("hover: hover") devices, hovering the parent item also opens it, and
 * `aria-expanded` is kept in sync in both cases so the visual state and the
 * announced state never disagree.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  var toggles = document.querySelectorAll('.menu-item.has-dropdown > .nav-dropdown-toggle');
  if (!toggles.length) return;

  var supportsHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;

  function closeAll(except) {
    toggles.forEach(function (toggle) {
      if (toggle === except) return;
      toggle.setAttribute('aria-expanded', 'false');
      var panel = toggle.parentElement.querySelector('.nav-dropdown');
      if (panel) panel.classList.remove('is-open');
    });
  }

  function openToggle(toggle, panel) {
    closeAll(toggle);
    toggle.setAttribute('aria-expanded', 'true');
    panel.classList.add('is-open');
  }

  function closeToggle(toggle, panel) {
    toggle.setAttribute('aria-expanded', 'false');
    panel.classList.remove('is-open');
  }

  toggles.forEach(function (toggle) {
    var parentItem = toggle.parentElement;
    var panel = parentItem.querySelector('.nav-dropdown');
    if (!panel) return;
    var inMobileDrawer = !!toggle.closest('.mobile-nav-links');

    toggle.addEventListener('click', function () {
      var isOpen = toggle.getAttribute('aria-expanded') === 'true';
      if (isOpen) {
        closeToggle(toggle, panel);
      } else {
        openToggle(toggle, panel);
      }
    });

    // Hover-to-open only makes sense for the desktop floating panel; the
    // mobile drawer's inline accordion is click/tap-only.
    if (supportsHover && !inMobileDrawer) {
      parentItem.addEventListener('mouseenter', function () {
        openToggle(toggle, panel);
      });
      parentItem.addEventListener('mouseleave', function () {
        closeToggle(toggle, panel);
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
