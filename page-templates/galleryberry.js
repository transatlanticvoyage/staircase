/**
 * Galleryberry lightbox — ADA / WCAG 2.1 AA compliant
 * - Click/tap/Enter-Space on a card opens the lightbox for that project
 * - Prev/Next buttons, arrow keys, and touch swipe navigate within the project
 * - Esc closes the lightbox
 * - Focus is trapped inside the lightbox while open
 * - Background page content is hidden from assistive tech via aria-hidden
 * - Photo navigation announced to screen readers via a polite live region
 * - Image alt falls back to a meaningful descriptor when attachment lacks a title
 * All identifiers prefixed `galleryberry_` for isolation.
 */

(function () {
    'use strict';

    var galleryberry_lightbox = document.getElementById('galleryberry_lightbox');
    if (!galleryberry_lightbox) return;

    var galleryberry_img       = document.getElementById('galleryberry_lightbox_img');
    var galleryberry_infoTitle = document.getElementById('galleryberry_lightbox_info_title');
    var galleryberry_infoDesc  = document.getElementById('galleryberry_lightbox_info_desc');
    var galleryberry_clientWrap = document.getElementById('galleryberry_lightbox_info_client');
    var galleryberry_clientName = document.getElementById('galleryberry_lightbox_info_client_name');
    var galleryberry_clientLoc  = document.getElementById('galleryberry_lightbox_info_client_location');
    var galleryberry_counter   = document.getElementById('galleryberry_lightbox_counter');
    var galleryberry_announce  = document.getElementById('galleryberry_lightbox_announce');
    var galleryberry_closeBtn  = document.getElementById('galleryberry_lightbox_close');
    var galleryberry_prevBtn   = document.getElementById('galleryberry_lightbox_prev');
    var galleryberry_nextBtn   = document.getElementById('galleryberry_lightbox_next');
    var galleryberry_main      = document.getElementById('galleryberry_main');

    var galleryberry_state = {
        photos: [],
        index: 0,
        projectName: '',
        lastFocusedCard: null
    };

    function galleryberry_parsePhotos(card) {
        try {
            var raw = card.getAttribute('data-galleryberry-photos');
            if (!raw) return [];
            return JSON.parse(raw);
        } catch (e) {
            return [];
        }
    }

    /**
     * Returns the array of focusable controls inside the lightbox in tab order.
     * Used to implement focus trap (Tab / Shift+Tab cycling).
     */
    function galleryberry_getFocusableButtons() {
        var buttons = [galleryberry_closeBtn];
        if (galleryberry_state.photos.length > 1) {
            buttons.push(galleryberry_prevBtn, galleryberry_nextBtn);
        }
        return buttons;
    }

    /**
     * Update the per-image client info line in the lightbox.
     * Pulls client_name + client_location from the CURRENT photo (per-image,
     * not per-project) and applies the rule: the word "from" only appears
     * when BOTH name and location have non-empty values.
     */
    function galleryberry_updateClientInfo(photo) {
        if (!galleryberry_clientWrap) return;
        var cliName = photo && photo.client_name ? String(photo.client_name).trim() : '';
        var cliLoc  = photo && photo.client_location ? String(photo.client_location).trim() : '';
        var fromSpan = galleryberry_clientWrap.querySelector('.galleryberry_lightbox_info_client_from');

        if (cliName && cliLoc) {
            // Both present → "Name from Location"
            galleryberry_clientName.textContent = cliName;
            galleryberry_clientLoc.textContent  = cliLoc;
            galleryberry_clientWrap.hidden = false;
            if (fromSpan) fromSpan.style.display = '';
        } else if (cliName) {
            // Only name → just "Name" (no "from", no location)
            galleryberry_clientName.textContent = cliName;
            galleryberry_clientLoc.textContent  = '';
            galleryberry_clientWrap.hidden = false;
            if (fromSpan) fromSpan.style.display = 'none';
        } else if (cliLoc) {
            // Only location → just "Location" (no "from", no name)
            galleryberry_clientName.textContent = '';
            galleryberry_clientLoc.textContent  = cliLoc;
            galleryberry_clientWrap.hidden = false;
            if (fromSpan) fromSpan.style.display = 'none';
        } else {
            // Neither → hide entire line
            galleryberry_clientName.textContent = '';
            galleryberry_clientLoc.textContent  = '';
            galleryberry_clientWrap.hidden = true;
            if (fromSpan) fromSpan.style.display = '';
        }
    }

    function galleryberry_render() {
        var photo = galleryberry_state.photos[galleryberry_state.index];
        if (!photo) return;
        var idx = galleryberry_state.index;
        var total = galleryberry_state.photos.length;
        var name = galleryberry_state.projectName || '';

        galleryberry_img.src = photo.full || photo.thumb || '';

        // Meaningful alt fallback: attachment title, else project + photo number,
        // else generic photo number. Empty alt is not appropriate for a content image.
        var altFallback = name
            ? (name + ' — photo ' + (idx + 1) + ' of ' + total)
            : ('Photo ' + (idx + 1) + ' of ' + total);
        galleryberry_img.alt = photo.alt || altFallback;

        // Visual counter (aria-hidden in HTML so SR doesn't read "3 slash 5")
        galleryberry_counter.textContent = (idx + 1) + ' / ' + total;

        // Screen-reader announcement
        if (galleryberry_announce) {
            galleryberry_announce.textContent = 'Photo ' + (idx + 1) + ' of ' + total;
        }

        // Per-image client info: updates on every photo change (prev/next/swipe)
        galleryberry_updateClientInfo(photo);

        var multiple = total > 1;
        galleryberry_prevBtn.style.display = multiple ? '' : 'none';
        galleryberry_nextBtn.style.display = multiple ? '' : 'none';
    }

    function galleryberry_setProjectInfo(card) {
        var name = card.getAttribute('data-galleryberry-project-name') || '';
        var desc = card.getAttribute('data-galleryberry-project-description') || '';
        galleryberry_state.projectName = name;
        if (galleryberry_infoTitle) galleryberry_infoTitle.textContent = name;
        if (galleryberry_infoDesc)  galleryberry_infoDesc.textContent  = desc;
        // Note: client_name + client_location are now per-photo (not per-project).
        // They get populated by galleryberry_updateClientInfo() inside render().
    }

    function galleryberry_open(card) {
        var photos = galleryberry_parsePhotos(card);
        if (!photos.length) return;
        galleryberry_state.photos = photos;
        galleryberry_state.index = 0;
        galleryberry_state.lastFocusedCard = card;

        galleryberry_setProjectInfo(card);
        galleryberry_render();
        galleryberry_lightbox.hidden = false;

        // Hide the rest of the page from assistive tech while modal is open
        if (galleryberry_main) galleryberry_main.setAttribute('aria-hidden', 'true');

        // Next tick so the opacity transition runs
        requestAnimationFrame(function () {
            galleryberry_lightbox.classList.add('is-visible');
        });
        document.body.style.overflow = 'hidden';

        // Focus the close button — keyboard users start there
        galleryberry_closeBtn.focus();
    }

    function galleryberry_close() {
        galleryberry_lightbox.classList.remove('is-visible');
        setTimeout(function () {
            galleryberry_lightbox.hidden = true;
            galleryberry_img.src = '';
            galleryberry_img.alt = '';
        }, 200);
        document.body.style.overflow = '';

        // Restore page content visibility for assistive tech
        if (galleryberry_main) galleryberry_main.removeAttribute('aria-hidden');

        // Clear announcement region so it doesn't repeat
        if (galleryberry_announce) galleryberry_announce.textContent = '';

        // Return focus to the originating card
        if (galleryberry_state.lastFocusedCard) {
            galleryberry_state.lastFocusedCard.focus();
        }
    }

    function galleryberry_next() {
        if (galleryberry_state.photos.length < 2) return;
        galleryberry_state.index = (galleryberry_state.index + 1) % galleryberry_state.photos.length;
        galleryberry_render();
    }

    function galleryberry_prev() {
        if (galleryberry_state.photos.length < 2) return;
        galleryberry_state.index = (galleryberry_state.index - 1 + galleryberry_state.photos.length) % galleryberry_state.photos.length;
        galleryberry_render();
    }

    // ---- Card click / keyboard activation ----
    document.addEventListener('click', function (e) {
        var card = e.target.closest ? e.target.closest('.galleryberry_card') : null;
        if (card) galleryberry_open(card);
    });

    document.addEventListener('keydown', function (e) {
        // Cards: Enter or Space activates
        var active = document.activeElement;
        if (active && active.classList && active.classList.contains('galleryberry_card')) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                galleryberry_open(active);
                return;
            }
        }

        // Lightbox-global keys only when visible
        if (galleryberry_lightbox.hidden) return;

        if (e.key === 'Escape') {
            galleryberry_close();
            return;
        }
        if (e.key === 'ArrowRight') {
            galleryberry_next();
            return;
        }
        if (e.key === 'ArrowLeft') {
            galleryberry_prev();
            return;
        }

        // Focus trap: Tab / Shift+Tab cycle within visible lightbox controls
        if (e.key === 'Tab') {
            var focusables = galleryberry_getFocusableButtons();
            if (!focusables.length) return;
            var first = focusables[0];
            var last = focusables[focusables.length - 1];
            var current = document.activeElement;
            var insideLightbox = focusables.indexOf(current) !== -1;

            if (e.shiftKey) {
                // Shift+Tab from first (or outside) → wrap to last
                if (!insideLightbox || current === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab from last (or outside) → wrap to first
                if (!insideLightbox || current === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    });

    // ---- Lightbox button clicks ----
    galleryberry_closeBtn.addEventListener('click', galleryberry_close);
    galleryberry_prevBtn.addEventListener('click', galleryberry_prev);
    galleryberry_nextBtn.addEventListener('click', galleryberry_next);

    // Click outside the image to close
    galleryberry_lightbox.addEventListener('click', function (e) {
        if (e.target === galleryberry_lightbox) galleryberry_close();
    });

    // ---- Touch swipe (mobile) ----
    var galleryberry_touchStartX = null;
    var galleryberry_touchStartY = null;
    var galleryberry_touchMoved = false;

    galleryberry_lightbox.addEventListener('touchstart', function (e) {
        if (!e.touches || !e.touches.length) return;
        galleryberry_touchStartX = e.touches[0].clientX;
        galleryberry_touchStartY = e.touches[0].clientY;
        galleryberry_touchMoved = false;
    }, { passive: true });

    galleryberry_lightbox.addEventListener('touchmove', function () {
        galleryberry_touchMoved = true;
    }, { passive: true });

    galleryberry_lightbox.addEventListener('touchend', function (e) {
        if (galleryberry_touchStartX === null) return;
        if (!e.changedTouches || !e.changedTouches.length) return;
        var dx = e.changedTouches[0].clientX - galleryberry_touchStartX;
        var dy = e.changedTouches[0].clientY - galleryberry_touchStartY;
        galleryberry_touchStartX = null;
        galleryberry_touchStartY = null;
        if (!galleryberry_touchMoved) return;
        // Horizontal swipe threshold, and must dominate vertical movement
        if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
            if (dx < 0) galleryberry_next();
            else galleryberry_prev();
        }
    }, { passive: true });
})();
