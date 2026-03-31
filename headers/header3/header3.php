<?php
/**
 * Header 3
 * Simple, clean header with logo and navigation
 * Features: Logo area, Silkweaver menu integration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render header 3
 */
function render_header3() {
    // Enqueue header-specific styles
    $assets_url = get_template_directory_uri() . '/headers/header3/assets/';
    wp_enqueue_style('header3-styles', $assets_url . 'css/styles.css', array(), '1.0.0');
    
    // Get logo
    $site_logo = get_option('staircase_header_logo', '');
    $site_name = get_bloginfo('name');
    
    ?>
    <header class="h3-header">
        <div class="h3-container">
            <!-- Logo Area -->
            <div class="h3-logo-area">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="h3-logo-link">
                    <?php if (!empty($site_logo)): ?>
                        <img src="<?php echo esc_url($site_logo); ?>" 
                             alt="<?php echo esc_attr($site_name); ?>" 
                             class="h3-logo-image">
                    <?php else: ?>
                        <span class="h3-logo-text"><?php echo esc_html($site_name); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- Navigation Area -->
            <nav class="h3-navigation" aria-label="Main navigation">
                <?php
                // Use Silkweaver menu if available
                if (function_exists('silkweaver_render_menu')) {
                    silkweaver_render_menu([
                        'menu_class' => 'h3-menu',
                        'container' => false,
                        'fallback_cb' => 'header3_fallback_menu'
                    ]);
                } else {
                    // WordPress default menu
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_class' => 'h3-menu',
                        'container' => false,
                        'fallback_cb' => 'header3_fallback_menu'
                    ]);
                }
                ?>
            </nav>
            
            <!-- Mobile Menu Toggle -->
            <button class="h3-mobile-toggle" aria-label="Toggle navigation" aria-expanded="false">
                <span class="h3-toggle-line"></span>
                <span class="h3-toggle-line"></span>
                <span class="h3-toggle-line"></span>
            </button>
        </div>
    </header>
    
    <script>
    // Simple mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('.h3-mobile-toggle');
        const nav = document.querySelector('.h3-navigation');
        
        if (toggle && nav) {
            toggle.addEventListener('click', function() {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', !isOpen);
                nav.classList.toggle('h3-navigation--open');
                toggle.classList.toggle('h3-mobile-toggle--active');
            });
        }
    });
    </script>
    <?php
}

/**
 * Fallback menu when no menu is assigned
 */
function header3_fallback_menu() {
    ?>
    <ul class="h3-menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
        <?php
        // Get top-level pages
        $pages = get_pages(['parent' => 0, 'number' => 5]);
        foreach ($pages as $page) {
            ?>
            <li><a href="<?php echo get_permalink($page->ID); ?>"><?php echo esc_html($page->post_title); ?></a></li>
            <?php
        }
        ?>
    </ul>
    <?php
}
?>