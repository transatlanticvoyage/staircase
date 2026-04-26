/**
 * Silkweaver Elegant — mobile-only category accordion.
 *
 * On mobile (<= 1024px), each category heading inside a silkweaver-elegant
 * panel becomes a tap-to-expand accordion handle. Only one category can be
 * open at a time. Desktop is unaffected.
 *
 * Header-agnostic: works on header1, header2, header3, etc. — anywhere the
 * elegant child area is rendered.
 *
 * No jQuery dependency.
 */
(function () {
    'use strict';

    var MOBILE_MAX = 1024;
    var BOUND_FLAG = 'swElegantCatBound';

    function isMobile() {
        return window.matchMedia('(max-width: ' + MOBILE_MAX + 'px)').matches;
    }

    function getColumns() {
        return document.querySelectorAll('.silkweaver-elegant-child-area .silkweaver-elegant-column');
    }

    function bindHandlers() {
        var columns = getColumns();
        columns.forEach(function (col, idx) {
            var title = col.querySelector('.silkweaver-elegant-category-title');
            if (!title) return;
            if (title.dataset[BOUND_FLAG] === '1') return;

            var childPages = col.querySelector('.silkweaver-elegant-child-pages');
            // Ensure the child-pages list has an id so aria-controls can target it.
            if (childPages && !childPages.id) {
                var seed = title.id ? title.id : 'sw-elegant-cat-pages-' + idx;
                childPages.id = seed + '-pages';
            }

            title.dataset[BOUND_FLAG] = '1';

            title.addEventListener('click', function (e) {
                if (!isMobile()) return;
                // Block any default behavior + halt bubbling so the parent
                // dropdown's :hover/:click handlers don't reinterpret this tap.
                e.preventDefault();
                e.stopPropagation();
                toggleColumn(col, title);
            });

            // Keyboard: Enter and Space activate, mirroring native button.
            title.addEventListener('keydown', function (e) {
                if (!isMobile()) return;
                var key = e.key;
                if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
                    e.preventDefault();
                    toggleColumn(col, title);
                }
            });
        });
    }

    function toggleColumn(col, title) {
        var wasExpanded = col.classList.contains('is-cat-expanded');

        // Close any sibling that's currently open within the same panel.
        var panel = col.closest('.silkweaver-elegant-child-area');
        if (panel) {
            var openOthers = panel.querySelectorAll('.silkweaver-elegant-column.is-cat-expanded');
            openOthers.forEach(function (other) {
                if (other !== col) {
                    other.classList.remove('is-cat-expanded');
                    var otherTitle = other.querySelector('.silkweaver-elegant-category-title');
                    if (otherTitle) otherTitle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        if (wasExpanded) {
            col.classList.remove('is-cat-expanded');
            title.setAttribute('aria-expanded', 'false');
        } else {
            col.classList.add('is-cat-expanded');
            title.setAttribute('aria-expanded', 'true');
        }
    }

    function applyMobileAttrs() {
        var columns = getColumns();
        var mobile = isMobile();
        columns.forEach(function (col) {
            var title = col.querySelector('.silkweaver-elegant-category-title');
            if (!title) return;
            var childPages = col.querySelector('.silkweaver-elegant-child-pages');

            if (mobile) {
                title.setAttribute('role', 'button');
                title.setAttribute('tabindex', '0');
                if (!title.hasAttribute('aria-expanded')) {
                    var isOpen = col.classList.contains('is-cat-expanded');
                    title.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                }
                if (childPages && childPages.id) {
                    title.setAttribute('aria-controls', childPages.id);
                }
            } else {
                // Desktop: strip the mobile interactive attrs and reset state.
                title.removeAttribute('role');
                title.removeAttribute('tabindex');
                title.removeAttribute('aria-expanded');
                title.removeAttribute('aria-controls');
                col.classList.remove('is-cat-expanded');
            }
        });
    }

    function init() {
        bindHandlers();
        applyMobileAttrs();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-evaluate on resize crossing the breakpoint.
    var resizeTimer = null;
    window.addEventListener('resize', function () {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            // Re-bind in case new panels were inserted dynamically (e.g. via SPA nav).
            bindHandlers();
            applyMobileAttrs();
        }, 150);
    });
})();
