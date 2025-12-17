<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-inner">
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
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'depth'          => 3, // Allow up to 3 levels
                    'walker'         => new Staircase_Walker_Nav_Menu(),
                    'fallback_cb'    => function() {
                        echo '<ul id="primary-menu">';
                        wp_list_pages(array(
                            'title_li' => '',
                            'depth'    => 1,
                        ));
                        echo '</ul>';
                    }
                ));
                ?>
            </nav>
            
            <?php 
            // Get formatted phone number from database
            if (function_exists('staircase_get_formatted_phone')):
                $header_phone_raw = staircase_get_header_phone();
                $header_phone_formatted = staircase_get_formatted_phone();
                if (!empty($header_phone_raw)):
            ?>
                <div class="header-phone">
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $header_phone_raw)); ?>" class="header-phone-button">
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
</header>

<?php
// Display hero section if applicable
if (function_exists('staircase_should_show_hero') && staircase_should_show_hero()) {
    if (is_front_page()) {
        // Default hero for homepage/front page
        $hero_title = get_bloginfo('name');
        $hero_subtitle = get_bloginfo('description');
        $hero_button_text = '';
        $hero_button_url = '';
    } elseif (is_home() && !is_front_page()) {
        // Hero for blog listing page
        $blog_page_id = get_option('page_for_posts');
        $hero_title = $blog_page_id ? get_the_title($blog_page_id) : 'Blog';
        $hero_subtitle = '';
        $hero_button_text = '';
        $hero_button_url = '';
    } else {
        // Get custom hero data for regular pages
        $hero_title = get_post_meta(get_the_ID(), 'hero_title', true);
        $hero_subtitle = get_post_meta(get_the_ID(), 'hero_subtitle', true);
        $hero_button_text = get_post_meta(get_the_ID(), 'hero_button_text', true);
        $hero_button_url = get_post_meta(get_the_ID(), 'hero_button_url', true);
        
        if (empty($hero_title)) {
            $hero_title = get_the_title();
        }
    }
    
    staircase_hero_section($hero_title, $hero_subtitle, $hero_button_text, $hero_button_url);
}
?>