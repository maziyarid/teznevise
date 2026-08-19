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
  var triggers = document.querySelectorAll('.main-nav .menu-item.has-dropdown > .nav-dropdown-toggle');
  if (!triggers.length) return;

  function setOpen(trigger, open) {
    var panel = trigger.parentElement.querySelector('.nav-dropdown');
    trigger.setAttribute('aria-expanded', String(open));
    if (panel) panel.classList.toggle('is-open', open);
  }

  function closeAll(except) {
    triggers.forEach(function (trigger) {
      if (trigger !== except) setOpen(trigger, false);
    });
  }

  triggers.forEach(function (trigger) {
    var parentItem = trigger.parentElement;
    var panel = parentItem.querySelector('.nav-dropdown');
    if (!panel) return;

    trigger.addEventListener('click', function () {
      var isOpen = trigger.getAttribute('aria-expanded') === 'true';
      closeAll(trigger);
      setOpen(trigger, !isOpen);
    });

    parentItem.addEventListener('pointerenter', function () {
      closeAll(trigger);
      setOpen(trigger, true);
    });

    parentItem.addEventListener('pointerleave', function () {
      setOpen(trigger, false);
    });

    parentItem.addEventListener('focusin', function () {
      closeAll(trigger);
      setOpen(trigger, true);
    });

    parentItem.addEventListener('focusout', function (e) {
      if (!parentItem.contains(e.relatedTarget)) setOpen(trigger, false);
    });

    parentItem.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        setOpen(trigger, false);
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
