/**
 * Header 1 — silkweaver dropdown tap toggle (mobile).
 *
 * The silkweaver dynamic dropdowns (.silkweaver-dropdown-menu) are revealed by CSS
 * :hover only (see style.css), so on touch devices tapping the "Services" / "Areas
 * We Serve" buttons does nothing. header1 relies on the GLOBAL js/navigation.js,
 * which has no handler for .silkweaver-dropdown-toggle. This script adds a tap
 * toggle for header1 ONLY: clicking a .silkweaver-dropdown-toggle toggles .is-open
 * on its parent .silkweaver-dropdown (the companion CSS in styles.css reveals the
 * menu when .is-open). It is guarded to mobile widths so desktop keeps its existing
 * pure-hover behavior, and it does not touch other headers.
 */
(function () {
  'use strict';

  var MOBILE_MAX = 768;

  function init() {
    var header = document.querySelector('.site-header.header1');
    if (!header) return;

    var toggles = header.querySelectorAll('.silkweaver-dropdown-toggle');
    if (!toggles.length) return;

    function closeAll(except) {
      var open = header.querySelectorAll('.silkweaver-dropdown.is-open');
      for (var i = 0; i < open.length; i++) {
        if (open[i] === except) continue;
        open[i].classList.remove('is-open');
        var b = open[i].querySelector('.silkweaver-dropdown-toggle');
        if (b) b.setAttribute('aria-expanded', 'false');
      }
    }

    for (var i = 0; i < toggles.length; i++) {
      toggles[i].addEventListener('click', function (e) {
        // Desktop keeps its pure-hover behavior; only intercept taps on mobile.
        if (window.innerWidth > MOBILE_MAX) return;
        var dropdown = this.closest('.silkweaver-dropdown');
        if (!dropdown) return;
        e.preventDefault();
        e.stopPropagation();
        var isOpen = dropdown.classList.contains('is-open');
        closeAll(dropdown);
        dropdown.classList.toggle('is-open', !isOpen);
        this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      });
    }

    // Tapping outside an open dropdown closes it.
    document.addEventListener('click', function (e) {
      if (window.innerWidth > MOBILE_MAX) return;
      if (!e.target.closest('.site-header.header1 .silkweaver-dropdown')) {
        closeAll(null);
      }
    });

    // Returning to desktop width clears any mobile-open state.
    window.addEventListener('resize', function () {
      if (window.innerWidth > MOBILE_MAX) closeAll(null);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
