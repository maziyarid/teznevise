/**
 * Accessible toggle behaviour for desktop dropdown submenus (.nav-dropdown).
 *
 * Hover already works via CSS for mouse users. This adds click/keyboard/touch
 * support: toggles aria-expanded on the trigger, shows/hides the `.nav-dropdown`
 * panel, closes other open dropdowns, and closes on outside click or Escape.
 *
 * @package Teznevise
 */
document.addEventListener('DOMContentLoaded', function () {
  var triggers = document.querySelectorAll('.main-nav .menu-item.has-dropdown > a[aria-haspopup="true"]');
  if (!triggers.length) return;

  function closeAll(except) {
    triggers.forEach(function (trigger) {
      if (trigger === except) return;
      trigger.setAttribute('aria-expanded', 'false');
      var panel = trigger.parentElement.querySelector('.nav-dropdown');
      if (panel) panel.classList.remove('is-open');
    });
  }

  triggers.forEach(function (trigger) {
    var parentItem = trigger.parentElement;
    var panel = parentItem.querySelector('.nav-dropdown');
    if (!panel) return;

    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      var isOpen = trigger.getAttribute('aria-expanded') === 'true';
      closeAll(trigger);
      trigger.setAttribute('aria-expanded', String(!isOpen));
      panel.classList.toggle('is-open', !isOpen);
    });

    parentItem.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        trigger.setAttribute('aria-expanded', 'false');
        panel.classList.remove('is-open');
        trigger.focus();
      }
    });
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.main-nav .menu-item.has-dropdown')) {
      closeAll();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll();
  });
});
