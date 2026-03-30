/**
 * Homeservice Header 2 JavaScript
 * Minimal scripts for header functionality
 */

jQuery(document).ready(function($) {
    
    // Mobile menu toggle
    $('.homeservice_header_2_elementor-menu-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('homeservice_header_2_elementor-active');
        $('.homeservice_header_2_elementor-nav-menu--dropdown').toggleClass('homeservice_header_2_elementor-active');
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.homeservice_header_2_elementor-menu-toggle, .homeservice_header_2_elementor-nav-menu--dropdown').length) {
            $('.homeservice_header_2_elementor-menu-toggle').removeClass('homeservice_header_2_elementor-active');
            $('.homeservice_header_2_elementor-nav-menu--dropdown').removeClass('homeservice_header_2_elementor-active');
        }
    });
    
    // Sticky header functionality - Rewritten for stability
    var header = $('.homeservice_header_2_elementor-sticky');
    if (header.length) {
        var originalHeaderOffset;
        var headerHeight;
        var isSticky = false;
        var ticking = false;
        
        // Initialize values after page load
        $(window).on('load', function() {
            originalHeaderOffset = header.offset().top;
            headerHeight = header.outerHeight();
            // Set CSS custom property for the placeholder height
            header[0].style.setProperty('--header-height', headerHeight + 'px');
        });
        
        // Throttled scroll handler for better performance
        function handleScroll() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    var scrollTop = $(window).scrollTop();
                    
                    // Use a simple threshold - when scroll passes original header position
                    if (scrollTop > originalHeaderOffset && !isSticky) {
                        // Becoming sticky
                        header.addClass('homeservice_header_2_elementor-sticky--active');
                        isSticky = true;
                    } else if (scrollTop <= originalHeaderOffset && isSticky) {
                        // Becoming non-sticky
                        header.removeClass('homeservice_header_2_elementor-sticky--active');
                        isSticky = false;
                    }
                    
                    ticking = false;
                });
                ticking = true;
            }
        }
        
        $(window).on('scroll', handleScroll);
    }
    
    // Dropdown menu functionality for mobile
    $('.homeservice_header_2_e-n-menu-dropdown-icon').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var menuItem = $(this).closest('.homeservice_header_2_has-dropdown');
        var dropdown = menuItem.find('.homeservice_header_2_e-n-menu-content').first();
        
        if ($(window).width() <= 1024) {
            menuItem.toggleClass('active');
            dropdown.slideToggle(300);
        }
    });
    
    // Hover functionality for desktop
    if ($(window).width() > 1024) {
        $('.homeservice_header_2_e-n-menu-item--has-dropdown').hover(
            function() {
                $(this).find('.homeservice_header_2_e-n-menu-dropdown').addClass('homeservice_header_2_e-n-menu-dropdown--active');
            },
            function() {
                $(this).find('.homeservice_header_2_e-n-menu-dropdown').removeClass('homeservice_header_2_e-n-menu-dropdown--active');
            }
        );
    }
    
    // Tab functionality in mega menu
    $('.homeservice_header_2_e-n-tab-title').on('click', function(e) {
        e.preventDefault();
        
        var tabId = $(this).data('tab-index');
        var tabsContainer = $(this).closest('.homeservice_header_2_e-n-tabs');
        
        // Remove active classes
        tabsContainer.find('.homeservice_header_2_e-n-tab-title').removeClass('homeservice_header_2_e-active');
        tabsContainer.find('.homeservice_header_2_e-n-tab-content').removeClass('homeservice_header_2_e-active');
        
        // Add active classes
        $(this).addClass('homeservice_header_2_e-active');
        tabsContainer.find('[data-tab-index="' + tabId + '"]').addClass('homeservice_header_2_e-active');
    });
    
    // Accessibility: Handle keyboard navigation
    $('.homeservice_header_2_e-n-menu-item__anchor').on('keydown', function(e) {
        if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
            e.preventDefault();
            $(this).click();
        }
    });
    
    // Handle window resize
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Reset mobile menu on resize to desktop
            if ($(window).width() > 1024) {
                $('.homeservice_header_2_elementor-menu-toggle').removeClass('homeservice_header_2_elementor-active');
                $('.homeservice_header_2_elementor-nav-menu--dropdown').removeClass('homeservice_header_2_elementor-active');
            }
        }, 250);
    });
    
});