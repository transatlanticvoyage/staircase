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
                <?php if (has_custom_logo()): ?>
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
                    'fallback_cb'    => function() {
                        echo '<ul>';
                        wp_list_pages(array(
                            'title_li' => '',
                            'depth'    => 1,
                        ));
                        echo '</ul>';
                    }
                ));
                ?>
            </nav>
        </div>
    </div>
</header>

<?php
// Display hero section if applicable
if (function_exists('staircase_should_show_hero') && staircase_should_show_hero()) {
    if (is_front_page() || is_home()) {
        // Default hero for homepage
        $hero_title = get_bloginfo('name');
        $hero_subtitle = get_bloginfo('description');
        $hero_button_text = '';
        $hero_button_url = '';
    } else {
        // Get custom hero data for pages
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