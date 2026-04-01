/**
 * Header 2 - Header-Specific JavaScript (Refactored)
 * Minimal scripts for header2-specific functionality only
 * Mobile menu and shared logic handled by Ruplin shared system
 */

jQuery(document).ready(function($) {
    
    // Sticky header functionality - Header2 specific implementation
    var header = $('.hs2-header[data-sticky="true"]');
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
                header.addClass('hs2-sticky-active');
                isSticky = true;
            } else if (scrollTop <= headerOffset && isSticky) {
                header.removeClass('hs2-sticky-active');
                isSticky = false;
            }
        });
    }
    
    // Header2-specific animations and effects
    var invisibleHeader = $('.hs2-header.hs2-invisible');
    if (invisibleHeader.length) {
        setTimeout(function() {
            invisibleHeader.removeClass('hs2-invisible');
        }, 100);
    }
    
    // Header2-specific hover effects for desktop
    if ($(window).width() > 1024) {
        $('.hs2-menu-item').hover(
            function() {
                $(this).addClass('hs2-hover');
            },
            function() {
                $(this).removeClass('hs2-hover');
            }
        );
    }
    
    // Header2-specific phone button tracking (if analytics enabled)
    $('.hs2-phone-button').on('click', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'Header2',
                'event_label': 'Phone Button',
                'value': 1
            });
        }
    });
    
    // Header2-specific accessibility enhancements
    $('.hs2-menu-link').on('focus', function() {
        $(this).closest('.hs2-menu-item').addClass('hs2-focused');
    }).on('blur', function() {
        $(this).closest('.hs2-menu-item').removeClass('hs2-focused');
    });
});