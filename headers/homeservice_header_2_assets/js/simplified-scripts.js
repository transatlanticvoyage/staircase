/**
 * Homeservice Header 2 - Simplified JavaScript
 * Clean, minimal scripts for header functionality
 */

jQuery(document).ready(function($) {
    
    // Mobile menu toggle
    $('.hs2-mobile-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.hs2-menu-wrapper').toggleClass('active');
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.hs2-mobile-toggle, .hs2-menu-wrapper').length) {
            $('.hs2-mobile-toggle').removeClass('active');
            $('.hs2-menu-wrapper').removeClass('active');
        }
    });
    
    // Sticky header functionality - Simple and stable
    var header = $('.hs2-header[data-sticky="true"]');
    if (header.length) {
        var headerOffset;
        var isSticky = false;
        
        // Initialize on load
        $(window).on('load', function() {
            headerOffset = header.offset().top;
        });
        
        // Simple scroll handler
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
    
    // Dropdown menu functionality for mobile
    $('.hs2-dropdown-icon').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var menuItem = $(this).closest('.hs2-has-dropdown');
        var dropdown = menuItem.find('.hs2-dropdown').first();
        
        if ($(window).width() <= 1024) {
            menuItem.toggleClass('active');
            dropdown.slideToggle(300);
        }
    });
    
    // Handle window resize
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Reset mobile menu on resize to desktop
            if ($(window).width() > 1024) {
                $('.hs2-mobile-toggle').removeClass('active');
                $('.hs2-menu-wrapper').removeClass('active');
            }
        }, 250);
    });
    
    // Initialize fade-in animation
    var invisibleHeader = $('.hs2-header.hs2-invisible');
    if (invisibleHeader.length) {
        setTimeout(function() {
            invisibleHeader.removeClass('hs2-invisible');
        }, 100);
    }
});