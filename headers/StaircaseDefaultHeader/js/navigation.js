/**
 * StaircaseDefaultHeader - Navigation JavaScript
 * Handles menu interactions, mobile toggle, dropdowns, and scroll effects
 */

(function($) {
    'use strict';

    const StaircaseHeaderNav = {
        
        // Cache DOM elements
        elements: {
            header: null,
            menuToggle: null,
            navigation: null,
            dropdownToggles: null,
            menuItems: null
        },

        // Configuration
        config: {
            scrollThreshold: 100,
            mobileBreakpoint: 768,
            scrollClass: 'is-scrolled',
            activeClass: 'is-active',
            openClass: 'is-open'
        },

        // Initialize
        init: function() {
            this.cacheElements();
            
            if (!this.elements.header) return;
            
            this.bindEvents();
            this.handleInitialScroll();
            this.setupAccessibility();
        },

        // Cache DOM elements
        cacheElements: function() {
            this.elements.header = $('.sdhdr-header');
            this.elements.menuToggle = $('.sdhdr-menu-toggle');
            this.elements.navigation = $('.sdhdr-navigation');
            this.elements.dropdownToggles = $('.sdhdr-dropdown-toggle');
            this.elements.menuItems = $('.sdhdr-has-children');
        },

        // Bind events
        bindEvents: function() {
            const self = this;

            // Mobile menu toggle
            this.elements.menuToggle.on('click', function(e) {
                e.preventDefault();
                self.toggleMobileMenu();
            });

            // Dropdown toggles for mobile/tablet
            this.elements.dropdownToggles.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggleDropdown($(this).parent());
            });

            // Close menu on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.sdhdr-header').length) {
                    self.closeMobileMenu();
                    self.closeAllDropdowns();
                }
            });

            // Handle scroll
            let scrollTimer;
            $(window).on('scroll', function() {
                if (scrollTimer) {
                    window.cancelAnimationFrame(scrollTimer);
                }
                scrollTimer = window.requestAnimationFrame(function() {
                    self.handleScroll();
                });
            });

            // Handle resize
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    self.handleResize();
                }, 250);
            });

            // Keyboard navigation
            this.elements.navigation.on('keydown', 'a', function(e) {
                self.handleKeyboard(e, $(this));
            });

            // Desktop hover intent for better UX
            if (this.isDesktop()) {
                this.initHoverIntent();
            }
        },

        // Toggle mobile menu
        toggleMobileMenu: function() {
            const isOpen = this.elements.navigation.hasClass(this.config.activeClass);
            
            if (isOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        },

        // Open mobile menu
        openMobileMenu: function() {
            this.elements.navigation.addClass(this.config.activeClass);
            this.elements.menuToggle.addClass(this.config.activeClass);
            this.elements.menuToggle.attr('aria-expanded', 'true');
            $('body').addClass('sdhdr-menu-open');
            
            // Trap focus for accessibility
            this.trapFocus(this.elements.navigation);
        },

        // Close mobile menu
        closeMobileMenu: function() {
            this.elements.navigation.removeClass(this.config.activeClass);
            this.elements.menuToggle.removeClass(this.config.activeClass);
            this.elements.menuToggle.attr('aria-expanded', 'false');
            $('body').removeClass('sdhdr-menu-open');
            
            // Release focus trap
            this.releaseFocus();
        },

        // Toggle dropdown
        toggleDropdown: function($parent) {
            const isOpen = $parent.hasClass(this.config.openClass);
            
            // Close siblings
            $parent.siblings().removeClass(this.config.openClass);
            $parent.siblings().find('.sdhdr-has-children').removeClass(this.config.openClass);
            
            // Toggle current
            if (isOpen) {
                $parent.removeClass(this.config.openClass);
                $parent.find('.sdhdr-has-children').removeClass(this.config.openClass);
            } else {
                $parent.addClass(this.config.openClass);
            }
        },

        // Close all dropdowns
        closeAllDropdowns: function() {
            this.elements.menuItems.removeClass(this.config.openClass);
        },

        // Handle scroll event
        handleScroll: function() {
            const scrollTop = $(window).scrollTop();
            
            if (scrollTop > this.config.scrollThreshold) {
                this.elements.header.addClass(this.config.scrollClass);
                $('body').addClass('header-scrolled');
            } else {
                this.elements.header.removeClass(this.config.scrollClass);
                $('body').removeClass('header-scrolled');
            }
        },

        // Handle initial scroll position
        handleInitialScroll: function() {
            this.handleScroll();
        },

        // Handle resize event
        handleResize: function() {
            if (this.isDesktop()) {
                this.closeMobileMenu();
                this.closeAllDropdowns();
            }
        },

        // Handle keyboard navigation
        handleKeyboard: function(e, $link) {
            const $parent = $link.parent();
            const $submenu = $parent.children('.sdhdr-submenu');
            
            switch(e.keyCode) {
                case 27: // ESC
                    if ($parent.hasClass(this.config.openClass)) {
                        $parent.removeClass(this.config.openClass);
                        $link.focus();
                    }
                    break;
                    
                case 32: // Space
                    if ($submenu.length && !this.isDesktop()) {
                        e.preventDefault();
                        this.toggleDropdown($parent);
                    }
                    break;
                    
                case 40: // Down arrow
                    if ($submenu.length) {
                        e.preventDefault();
                        $submenu.find('a').first().focus();
                    }
                    break;
                    
                case 38: // Up arrow
                    if ($link.parent().parent().hasClass('sdhdr-submenu')) {
                        e.preventDefault();
                        const $prevLink = $link.parent().prev().find('a');
                        if ($prevLink.length) {
                            $prevLink.focus();
                        } else {
                            $link.closest('.sdhdr-has-children').children('a').focus();
                        }
                    }
                    break;
            }
        },

        // Setup accessibility
        setupAccessibility: function() {
            // Add ARIA labels
            this.elements.navigation.attr('aria-label', 'Primary navigation');
            
            // Setup submenu ARIA
            this.elements.menuItems.each(function() {
                const $this = $(this);
                const $link = $this.children('a');
                const $submenu = $this.children('.sdhdr-submenu');
                
                if ($submenu.length) {
                    $link.attr('aria-haspopup', 'true');
                    $link.attr('aria-expanded', 'false');
                    $submenu.attr('aria-hidden', 'true');
                }
            });
        },

        // Initialize hover intent for better desktop UX
        initHoverIntent: function() {
            let hoverTimer;
            
            this.elements.menuItems.on('mouseenter', function() {
                const $this = $(this);
                clearTimeout(hoverTimer);
                
                hoverTimer = setTimeout(function() {
                    $this.addClass('hover-intent');
                }, 300);
            }).on('mouseleave', function() {
                const $this = $(this);
                clearTimeout(hoverTimer);
                
                hoverTimer = setTimeout(function() {
                    $this.removeClass('hover-intent');
                }, 100);
            });
        },

        // Check if desktop
        isDesktop: function() {
            return $(window).width() > this.config.mobileBreakpoint;
        },

        // Trap focus for accessibility
        trapFocus: function($container) {
            const $focusable = $container.find('a, button, [tabindex]:not([tabindex="-1"])');
            const $firstFocusable = $focusable.first();
            const $lastFocusable = $focusable.last();
            
            $container.on('keydown.focustrap', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === $firstFocusable[0]) {
                            e.preventDefault();
                            $lastFocusable.focus();
                        }
                    } else {
                        if (document.activeElement === $lastFocusable[0]) {
                            e.preventDefault();
                            $firstFocusable.focus();
                        }
                    }
                }
            });
        },

        // Release focus trap
        releaseFocus: function() {
            $('.sdhdr-navigation').off('keydown.focustrap');
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        StaircaseHeaderNav.init();
    });

    // Re-initialize after AJAX operations (for compatibility)
    $(document).on('ajaxComplete', function() {
        StaircaseHeaderNav.init();
    });

})(jQuery);