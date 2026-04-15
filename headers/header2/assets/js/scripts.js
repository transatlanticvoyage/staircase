/**
 * Header 2 - Header-Specific JavaScript (Refactored)
 * Minimal scripts for header2-specific functionality only
 * Mobile menu and shared logic handled by Ruplin shared system
 */

jQuery(document).ready(function($) {

    // -------------------------------------------------------------------------
    // Mobile hamburger toggle
    // -------------------------------------------------------------------------
    var $toggle  = $('.zx_hd2_mobile_toggle');
    var $wrapper = $('.zx_hd2_menu_wrapper');

    // Open / close panel
    $toggle.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var isOpen = $wrapper.hasClass('active');
        $toggle.toggleClass('active').attr('aria-expanded', !isOpen);
        $wrapper.toggleClass('active');
        $('body').toggleClass('mobile-menu-open');
    });

    // Close when clicking outside the panel or toggle
    $(document).on('click', function(e) {
        if ($wrapper.hasClass('active') &&
            !$(e.target).closest('.zx_hd2_mobile_toggle, .zx_hd2_menu_wrapper').length) {
            $toggle.removeClass('active').attr('aria-expanded', false);
            $wrapper.removeClass('active');
            $('body').removeClass('mobile-menu-open');
        }
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $wrapper.hasClass('active')) {
            $toggle.removeClass('active').attr('aria-expanded', false);
            $wrapper.removeClass('active');
            $('body').removeClass('mobile-menu-open');
            $toggle.focus();
        }
    });

    // zx_hd2 dropdown icon: expand child items on mobile
    $(document).on('click', '.zx_hd2_dropdown_icon', function(e) {
        if ($(window).width() > 1024) return;
        e.preventDefault();
        e.stopPropagation();
        var $item     = $(this).closest('.zx_hd2_has_dropdown');
        var $dropdown = $item.find('.zx_hd2_dropdown').first();
        $item.toggleClass('active');
        $dropdown.slideToggle(250);
    });

    // Reset mobile state on resize to desktop
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($(window).width() > 1024) {
                $toggle.removeClass('active').attr('aria-expanded', false);
                $wrapper.removeClass('active');
                $('body').removeClass('mobile-menu-open');
                $('.zx_hd2_has_dropdown').removeClass('active');
                $('.zx_hd2_dropdown').removeAttr('style');
            }
        }, 250);
    });

    // -------------------------------------------------------------------------
    // Sticky header functionality - Header2 specific implementation
    var header = $('.zx_hd2_header[data-sticky="true"]');
    if (header.length) {
        var isSticky = false;
        var placeholder = null;

        function getAdminBarHeight() {
            var ab = document.getElementById('wpadminbar');
            return ab ? ab.offsetHeight : 0;
        }

        function getHeaderOffset() {
            // When sticky, placeholder holds the original position
            if (placeholder && placeholder.is(':visible')) {
                return placeholder.offset().top - getAdminBarHeight();
            }
            return header.offset().top - getAdminBarHeight();
        }

        function applySticky() {
            if (!placeholder) {
                placeholder = $('<div class="zx_hd2_sticky_placeholder"></div>');
            }
            placeholder.css('height', header.outerHeight() + 'px');
            header.before(placeholder);
            header.addClass('zx_hd2_sticky_active');
            // Dynamically set top based on actual admin bar height —
            // avoids gap on tablet where hardcoded CSS values may not match
            header.css('top', getAdminBarHeight() + 'px');
            isSticky = true;
        }

        function removeSticky() {
            header.removeClass('zx_hd2_sticky_active');
            header.css('top', ''); // clear inline style, return to CSS
            if (placeholder) {
                placeholder.detach();
            }
            isSticky = false;
        }

        $(window).on('scroll', function() {
            var scrollTop = $(window).scrollTop();
            var offset = getHeaderOffset();

            if (scrollTop > offset && !isSticky) {
                applySticky();
            } else if (scrollTop <= offset && isSticky) {
                removeSticky();
            }
        });

        // On orientation change / resize, recalculate top if already sticky
        $(window).on('resize orientationchange', function() {
            if (isSticky) {
                header.css('top', getAdminBarHeight() + 'px');
            }
        });
    }
    
    // Header2-specific animations and effects
    var invisibleHeader = $('.zx_hd2_header.zx_hd2_invisible');
    if (invisibleHeader.length) {
        setTimeout(function() {
            invisibleHeader.removeClass('zx_hd2_invisible');
        }, 100);
    }
    
    // Header2-specific hover effects for desktop
    if ($(window).width() > 1024) {
        $('.zx_hd2_menu_item').hover(
            function() {
                $(this).addClass('zx_hd2_hover');
            },
            function() {
                $(this).removeClass('zx_hd2_hover');
            }
        );
    }
    
    // Header2-specific phone button tracking (if analytics enabled)
    $('.zx_hd2_phone_button').on('click', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'Header2',
                'event_label': 'Phone Button',
                'value': 1
            });
        }
    });
    
    // Header2-specific accessibility enhancements
    $('.zx_hd2_menu_link').on('focus', function() {
        $(this).closest('.zx_hd2_menu_item').addClass('zx_hd2_focused');
    }).on('blur', function() {
        $(this).closest('.zx_hd2_menu_item').removeClass('zx_hd2_focused');
    });

    // Silkweaver mobile accordion (header2-specific, only active on mobile widths)
    // Standard dropdowns use slideToggle; robust items use class-based toggle
    // to avoid conflicts with the plugin's :hover CSS rules on touch devices.
    $('.zx_hd2_header .silkweaver-parent-button').on('click', function(e) {
        if ($(window).width() <= 1024) {
            e.preventDefault();
            e.stopPropagation();
            var parentLi = $(this).closest('.silkweaver-dropdown');
            var isRobust = parentLi.hasClass('silkweaver-robust-dropdown');

            if (isRobust) {
                // Pure class toggle — no inline styles to fight CSS
                parentLi.toggleClass('active');
            } else {
                // Standard dropdown: slideToggle is fine here
                var submenu = parentLi.find('.silkweaver-dropdown-menu').first();
                parentLi.toggleClass('active');
                submenu.slideToggle(300);
            }
        }
    });

    // Reset silkweaver submenus when resizing back to desktop
    $(window).on('resize', function() {
        if ($(window).width() > 1024) {
            $('.zx_hd2_header .silkweaver-dropdown').removeClass('active');
            $('.zx_hd2_header .silkweaver-dropdown-menu').removeAttr('style');
        }
    });

    // -------------------------------------------------------------------------
    // Mobile sticky call banner — show after user begins scrolling, mobile only
    // -------------------------------------------------------------------------
    var $callBanner = $('.zx_hd2_call_banner');
    if ($callBanner.length) {
        // Sync tel: href and display number from the header phone button.
        // Shared system uses .zx_hd2_phone_button; fallback uses .hs2-phone-button.
        var $phoneBtn = $('.zx_hd2_phone_button');
        if (!$phoneBtn.length) {
            $phoneBtn = $('.hs2-phone-button');
        }
        var phoneHref = $phoneBtn.attr('href');
        if (phoneHref) {
            $callBanner.attr('href', phoneHref);
            // Get the visible phone number text (skip the SVG icon)
            var $clone = $phoneBtn.clone();
            $clone.find('svg').remove();
            var phoneText = $clone.text().trim();
            if (phoneText) {
                $callBanner.find('.zx_hd2_call_banner_number').text(phoneText);
            }
        }

        function updateCallBanner() {
            if ($(window).width() > 1024) {
                $callBanner.removeClass('zx_hd2_call_banner_visible');
                return;
            }
            if ($(window).scrollTop() > 0) {
                $callBanner.addClass('zx_hd2_call_banner_visible');
            } else {
                $callBanner.removeClass('zx_hd2_call_banner_visible');
            }
        }

        $(window).on('scroll', updateCallBanner);
        $(window).on('resize', updateCallBanner);
        updateCallBanner();
    }
});