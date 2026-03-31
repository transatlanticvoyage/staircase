<?php
/**
 * Header 1
 * Default header for home service businesses
 * This is the original header from the staircase theme
 * (Previously homeservice_header_1)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render header 1
 */
function render_header1() {
    ?>
    <header class="site-header header1">
        <div class="container">
            <div class="header-inner">
                <div class="header-logo-area">
                    <div class="site-branding">
                        <?php 
                        $staircase_logo = get_option('staircase_header_logo', '');
                        if (!empty($staircase_logo)): 
                        ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="staircase-logo-link">
                                <img src="<?php echo esc_url($staircase_logo); ?>" alt="<?php bloginfo('name'); ?>" class="staircase-logo">
                            </a>
                        <?php elseif (has_custom_logo()): ?>
                            <?php the_custom_logo(); ?>
                        <?php else: ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                    <?php bloginfo('name'); ?>
                                </a>
                            </h1>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="header-nav-area">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    
                    <nav class="main-navigation">
                        <?php
                        if (function_exists('silkweaver_render_menu') && get_option('silkweaver_use_system', true)) {
                            echo silkweaver_render_menu();
                        } else {
                            wp_nav_menu(array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'primary-menu',
                                'container'      => false,
                                'depth'          => 3,
                                'walker'         => class_exists('Staircase_Walker_Nav_Menu') ? new Staircase_Walker_Nav_Menu() : null,
                            ));
                        }
                        ?>
                    </nav>
                    
                    <?php 
                    if (function_exists('staircase_get_formatted_phone')):
                        $header_phone_raw = staircase_get_header_phone();
                        $header_phone_formatted = staircase_get_formatted_phone();
                        if (!empty($header_phone_raw)):
                    ?>
                        <div class="header-phone">
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $header_phone_raw)); ?>" class="header-phone-button staircase_main_cta_button">
                                <svg class="phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                                <span class="phone-number"><?php echo esc_html($header_phone_formatted); ?></span>
                            </a>
                        </div>
                    <?php 
                        endif;
                    endif; 
                    ?>
                </div>
            </div>
        </div>
    </header>
    <?php
}