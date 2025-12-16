<?php
/**
 * Staircase Theme Functions
 * 
 * @package Staircase
 */

// Theme Setup
function staircase_theme_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    
    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary'   => __('Primary Menu', 'staircase'),
        'footer'    => __('Footer Menu', 'staircase'),
    ));
}
add_action('after_setup_theme', 'staircase_theme_setup');

// Enqueue styles and scripts
function staircase_enqueue_assets() {
    // Enqueue main stylesheet with timestamp for cache busting
    wp_enqueue_style(
        'staircase-style',
        get_stylesheet_uri(),
        array(),
        filemtime(get_template_directory() . '/style.css')
    );
    
    // Enqueue navigation script for mobile menu
    wp_enqueue_script(
        'staircase-navigation',
        get_template_directory_uri() . '/js/navigation.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'staircase_enqueue_assets');

// Custom hero section function
function staircase_hero_section($title = '', $subtitle = '', $button_text = '', $button_url = '') {
    if (empty($title)) {
        $title = get_bloginfo('name');
    }
    if (empty($subtitle)) {
        $subtitle = get_bloginfo('description');
    }
    ?>
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo esc_html($title); ?></h1>
                <?php if ($subtitle): ?>
                    <p><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
                <?php if ($button_text && $button_url): ?>
                    <a href="<?php echo esc_url($button_url); ?>" class="hero-button">
                        <?php echo esc_html($button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

// Custom function to check if hero should be displayed
function staircase_should_show_hero() {
    // Show hero on homepage by default
    if (is_front_page() || is_home()) {
        return true;
    }
    
    // Check for custom field on individual pages
    if (is_page() && get_post_meta(get_the_ID(), 'show_hero', true) === 'yes') {
        return true;
    }
    
    return false;
}

// Set content width
function staircase_content_width() {
    $GLOBALS['content_width'] = apply_filters('staircase_content_width', 1200);
}
add_action('after_setup_theme', 'staircase_content_width', 0);

// Custom excerpt length
function staircase_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'staircase_excerpt_length');

// Custom excerpt more
function staircase_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'staircase_excerpt_more');

// Add custom meta boxes for hero section
function staircase_add_meta_boxes() {
    add_meta_box(
        'staircase_hero_options',
        'Hero Section Options',
        'staircase_hero_meta_box_callback',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'staircase_add_meta_boxes');

// Hero meta box callback
function staircase_hero_meta_box_callback($post) {
    wp_nonce_field('staircase_hero_meta_box', 'staircase_hero_meta_box_nonce');
    
    $show_hero = get_post_meta($post->ID, 'show_hero', true);
    $hero_title = get_post_meta($post->ID, 'hero_title', true);
    $hero_subtitle = get_post_meta($post->ID, 'hero_subtitle', true);
    $hero_button_text = get_post_meta($post->ID, 'hero_button_text', true);
    $hero_button_url = get_post_meta($post->ID, 'hero_button_url', true);
    ?>
    <p>
        <label for="show_hero">
            <input type="checkbox" id="show_hero" name="show_hero" value="yes" <?php checked($show_hero, 'yes'); ?>>
            Show Hero Section
        </label>
    </p>
    <p>
        <label for="hero_title">Hero Title:</label><br>
        <input type="text" id="hero_title" name="hero_title" value="<?php echo esc_attr($hero_title); ?>" style="width:100%;">
    </p>
    <p>
        <label for="hero_subtitle">Hero Subtitle:</label><br>
        <textarea id="hero_subtitle" name="hero_subtitle" style="width:100%;" rows="3"><?php echo esc_textarea($hero_subtitle); ?></textarea>
    </p>
    <p>
        <label for="hero_button_text">Button Text:</label><br>
        <input type="text" id="hero_button_text" name="hero_button_text" value="<?php echo esc_attr($hero_button_text); ?>" style="width:100%;">
    </p>
    <p>
        <label for="hero_button_url">Button URL:</label><br>
        <input type="url" id="hero_button_url" name="hero_button_url" value="<?php echo esc_url($hero_button_url); ?>" style="width:100%;">
    </p>
    <?php
}

// Save hero meta box data
function staircase_save_hero_meta($post_id) {
    // Check nonce
    if (!isset($_POST['staircase_hero_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['staircase_hero_meta_box_nonce'], 'staircase_hero_meta_box')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save data
    $show_hero = isset($_POST['show_hero']) ? 'yes' : 'no';
    update_post_meta($post_id, 'show_hero', $show_hero);
    
    if (isset($_POST['hero_title'])) {
        update_post_meta($post_id, 'hero_title', sanitize_text_field($_POST['hero_title']));
    }
    
    if (isset($_POST['hero_subtitle'])) {
        update_post_meta($post_id, 'hero_subtitle', sanitize_textarea_field($_POST['hero_subtitle']));
    }
    
    if (isset($_POST['hero_button_text'])) {
        update_post_meta($post_id, 'hero_button_text', sanitize_text_field($_POST['hero_button_text']));
    }
    
    if (isset($_POST['hero_button_url'])) {
        update_post_meta($post_id, 'hero_button_url', esc_url_raw($_POST['hero_button_url']));
    }
}
add_action('save_post', 'staircase_save_hero_meta');