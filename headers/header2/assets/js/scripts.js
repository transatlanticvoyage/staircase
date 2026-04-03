/**
 * Header 2 - Header-Specific JavaScript (Refactored)
 * Minimal scripts for header2-specific functionality only
 * Mobile menu and shared logic handled by Ruplin shared system
 */

jQuery(document).ready(function($) {
    
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

        $(window).on('scroll', function() {
            var scrollTop = $(window).scrollTop();
            var offset = getHeaderOffset();

            if (scrollTop > offset && !isSticky) {
                // Insert a placeholder to prevent content jump
                if (!placeholder) {
                    placeholder = $('<div class="zx_hd2_sticky_placeholder"></div>');
                }
                placeholder.css('height', header.outerHeight() + 'px');
                header.before(placeholder);
                header.addClass('zx_hd2_sticky_active');
                isSticky = true;
            } else if (scrollTop <= offset && isSticky) {
                header.removeClass('zx_hd2_sticky_active');
                if (placeholder) {
                    placeholder.detach();
                }
                isSticky = false;
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
});