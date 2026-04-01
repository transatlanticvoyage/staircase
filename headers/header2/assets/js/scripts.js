/**
 * Header 2 - Header-Specific JavaScript (Refactored)
 * Minimal scripts for header2-specific functionality only
 * Mobile menu and shared logic handled by Ruplin shared system
 */

jQuery(document).ready(function($) {
    
    // Sticky header functionality - Header2 specific implementation
    var header = $('.zx_hd2_header[data-sticky="true"]');
    if (header.length) {
        var headerOffset;
        var isSticky = false;
        
        // Initialize on load
        $(window).on('load', function() {
            headerOffset = header.offset().top;
        });
        
        // Simple scroll handler with header2-specific classes
        $(window).on('scroll', function() {
            var scrollTop = $(window).scrollTop();
            
            if (scrollTop > headerOffset && !isSticky) {
                header.addClass('zx_hd2_sticky_active');
                isSticky = true;
            } else if (scrollTop <= headerOffset && isSticky) {
                header.removeClass('zx_hd2_sticky_active');
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