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
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    mainNavigation.classList.remove('toggled');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.classList.remove('is-active');
                }
            }, 250);
        });
    });
})();

// Add CSS for hamburger animation
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
`;
document.head.appendChild(style);