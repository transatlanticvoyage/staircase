/**
 * File navigation.js
 *
 * Handles toggling the navigation menu for small screens
 */
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNavigation = document.querySelector('.main-navigation');
        
        if (!menuToggle || !mainNavigation) {
            return;
        }
        
        // Sticky header scroll behavior
        const siteHeader = document.querySelector('.site-header');
        const body = document.body;
        
        if (siteHeader) {
            let lastScrollTop = 0;
            let isScrolling = false;
            
            // Get the actual header height for accurate calculations
            const getHeaderHeight = () => {
                return siteHeader.offsetHeight;
            };
            
            // Adjust body padding dynamically
            const adjustBodyPadding = () => {
                const headerHeight = getHeaderHeight();
                const adminBar = document.getElementById('wpadminbar');
                const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
                body.style.paddingTop = (headerHeight + adminBarHeight) + 'px';
            };
            
            function handleScroll() {
                if (!isScrolling) {
                    window.requestAnimationFrame(function() {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        
                        // Add/remove scrolled class based on scroll position
                        if (scrollTop > 10) {
                            siteHeader.classList.add('scrolled');
                            body.classList.add('header-scrolled');
                        } else {
                            siteHeader.classList.remove('scrolled');
                            body.classList.remove('header-scrolled');
                        }
                        
                        // Adjust padding after class changes
                        setTimeout(adjustBodyPadding, 310); // Wait for transition to complete
                        
                        // Optional: Hide/show header on scroll direction
                        // Uncomment if you want the header to hide on scroll down
                        /*
                        if (scrollTop > lastScrollTop && scrollTop > 100) {
                            // Scrolling down & past 100px
                            siteHeader.classList.add('header-hidden');
                        } else {
                            // Scrolling up
                            siteHeader.classList.remove('header-hidden');
                        }
                        */
                        
                        lastScrollTop = scrollTop;
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            }
            
            // Listen for scroll events
            window.addEventListener('scroll', handleScroll);
            
            // Adjust padding on load and resize
            window.addEventListener('load', adjustBodyPadding);
            window.addEventListener('resize', adjustBodyPadding);
            
            // Check initial scroll position and padding
            handleScroll();
            adjustBodyPadding();
        }
        
        // Toggle menu on button click
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mainNavigation.classList.toggle('toggled');
            
            // Update aria-expanded attribute
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            
            // Animate hamburger menu
            menuToggle.classList.toggle('is-active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!menuToggle.contains(e.target) && !mainNavigation.contains(e.target)) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.classList.remove('is-active');
            }
        });
        
        // Close menu when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.classList.remove('is-active');
            }
        });
        
        // Handle dropdown menus
        const dropdownToggleLinks = document.querySelectorAll('.main-navigation .menu-item-has-children > a');
        const dropdownToggleButtons = document.querySelectorAll('.dropdown-toggle');
        
        // Handle parent menu item clicks on mobile
        dropdownToggleLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Only handle clicks on mobile
                if (window.innerWidth <= 768) {
                    // Check if this is a parent item without a real URL (# or empty)
                    const href = this.getAttribute('href');
                    if (!href || href === '#' || href === '') {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const parentLi = this.parentNode;
                        const submenu = parentLi.querySelector('.sub-menu');
                        const isOpen = parentLi.classList.contains('dropdown-open');
                        
                        // Close all other dropdowns
                        document.querySelectorAll('.menu-item-has-children').forEach(function(item) {
                            if (item !== parentLi) {
                                item.classList.remove('dropdown-open');
                            }
                        });
                        
                        // Toggle current dropdown
                        if (submenu) {
                            parentLi.classList.toggle('dropdown-open');
                            
                            // Update aria-expanded if there's a dropdown indicator
                            const indicator = this.querySelector('.dropdown-indicator');
                            if (indicator) {
                                this.setAttribute('aria-expanded', !isOpen);
                            }
                        }
                    }
                }
            });
        });
        
        // Handle dropdown toggle button clicks (for mobile) - if they exist
        dropdownToggleButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const parentLi = this.parentNode;
                const submenu = parentLi.querySelector('.sub-menu');
                const isOpen = parentLi.classList.contains('dropdown-open');
                
                // Close all other dropdowns
                document.querySelectorAll('.menu-item-has-children').forEach(function(item) {
                    if (item !== parentLi) {
                        item.classList.remove('dropdown-open');
                    }
                });
                
                // Toggle current dropdown
                if (submenu) {
                    parentLi.classList.toggle('dropdown-open');
                    
                    // Update aria-expanded
                    button.setAttribute('aria-expanded', !isOpen);
                }
            });
        });
        
        // Handle dropdown hover for desktop
        const menuItemsWithChildren = document.querySelectorAll('.main-navigation .menu-item-has-children');
        
        menuItemsWithChildren.forEach(function(menuItem) {
            let hoverTimer;
            
            menuItem.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768) {
                    clearTimeout(hoverTimer);
                    this.classList.add('dropdown-open');
                }
            });
            
            menuItem.addEventListener('mouseleave', function() {
                if (window.innerWidth > 768) {
                    const self = this;
                    hoverTimer = setTimeout(function() {
                        self.classList.remove('dropdown-open');
                    }, 150);
                }
            });
            
            // Handle keyboard navigation
            const link = menuItem.querySelector('a');
            if (link) {
                link.addEventListener('focus', function() {
                    if (window.innerWidth > 768) {
                        this.parentNode.classList.add('dropdown-open');
                    }
                });
                
                link.addEventListener('blur', function() {
                    if (window.innerWidth > 768) {
                        const self = this;
                        setTimeout(function() {
                            if (!self.parentNode.contains(document.activeElement)) {
                                self.parentNode.classList.remove('dropdown-open');
                            }
                        }, 150);
                    }
                });
            }
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    mainNavigation.classList.remove('toggled');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.classList.remove('is-active');
                    
                    // Reset mobile dropdown states
                    document.querySelectorAll('.menu-item-has-children').forEach(function(item) {
                        item.classList.remove('dropdown-open');
                        const toggleButton = item.querySelector('.dropdown-toggle');
                        if (toggleButton) {
                            toggleButton.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            }, 250);
        });
    });
})();

// Add CSS for hamburger animation and mobile menu fixes
const style = document.createElement('style');
style.textContent = `
    .menu-toggle.is-active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }
    
    .menu-toggle.is-active span:nth-child(2) {
        opacity: 0;
    }
    
    .menu-toggle.is-active span:nth-child(3) {
        transform: rotate(-45deg) translate(7px, -6px);
    }
    
    /* Mobile menu fixes - ensure sub-menus are hidden by default */
    @media (max-width: 768px) {
        .main-navigation .sub-menu {
            display: none !important;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .main-navigation .menu-item-has-children.dropdown-open > .sub-menu {
            display: block !important;
            max-height: 1000px;
            transition: max-height 0.3s ease-in;
        }
        
        /* Add visual indicator for expandable parent items on mobile */
        .main-navigation .menu-item-has-children > a {
            position: relative;
            padding-right: 40px;
        }
        
        .main-navigation .menu-item-has-children > a::after {
            content: '+';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        
        .main-navigation .menu-item-has-children.dropdown-open > a::after {
            content: '−';
            transform: translateY(-50%) rotate(180deg);
        }
        
        /* Style the dropdown indicator if it exists */
        .main-navigation .dropdown-indicator {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
            pointer-events: none;
        }
        
        .main-navigation .menu-item-has-children.dropdown-open .dropdown-indicator {
            transform: translateY(-50%) rotate(180deg);
        }
    }
`;
document.head.appendChild(style);