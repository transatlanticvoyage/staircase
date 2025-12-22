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
    
    // Add header phone button styles
    wp_add_inline_style('staircase-style', '
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        
        .header-phone {
            margin-left: 20px;
        }
        
        .header-phone-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid #0073aa;
        }
        
        .header-phone-button:hover {
            background: white;
            color: #0073aa;
            text-decoration: none;
        }
        
        .phone-icon {
            flex-shrink: 0;
        }
        
        .phone-number {
            white-space: nowrap;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .header-inner {
                flex-wrap: wrap;
            }
            
            .header-phone {
                order: 3;
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
                text-align: center;
            }
            
            .header-phone-button {
                display: inline-flex;
                width: auto;
            }
        }
        
        @media (max-width: 480px) {
            .header-phone-button {
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .phone-icon {
                width: 16px;
                height: 16px;
            }
        }
        
        /* Navigation Dropdown Styles */
        .main-navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        
        .main-navigation li {
            position: relative;
        }
        
        .main-navigation a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .main-navigation a:hover {
            color: #0073aa;
        }
        
        /* Dropdown Indicator */
        .dropdown-indicator {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }
        
        .dropdown-open .dropdown-indicator {
            transform: rotate(180deg);
        }
        
        /* Submenu Styles */
        .main-navigation .sub-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .main-navigation .menu-item-has-children:hover .sub-menu,
        .main-navigation .dropdown-open .sub-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .main-navigation .sub-menu li {
            width: 100%;
        }
        
        .main-navigation .sub-menu a {
            padding: 12px 20px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
        }
        
        .main-navigation .sub-menu a:hover {
            background: #f8f9fa;
            color: #0073aa;
        }
        
        .main-navigation .sub-menu li:last-child a {
            border-bottom: none;
        }
        
        /* Third level dropdowns */
        .main-navigation .sub-menu .sub-menu {
            top: 0;
            left: 100%;
        }
        
        /* Mobile Menu Styles */
        @media (max-width: 768px) {
            .main-navigation ul {
                flex-direction: column;
            }
            
            .main-navigation {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #e1e1e1;
                border-radius: 6px;
                margin-top: 10px;
                z-index: 999;
            }
            
            .main-navigation.toggled {
                display: block;
            }
            
            .main-navigation .sub-menu {
                position: static;
                box-shadow: none;
                border: none;
                border-radius: 0;
                background: #f8f9fa;
                opacity: 1;
                visibility: visible;
                transform: none;
                display: none;
            }
            
            .main-navigation .dropdown-open .sub-menu {
                display: block !important;
            }
            
            .main-navigation .sub-menu a {
                padding-left: 40px;
                background: #f8f9fa;
            }
            
            .main-navigation .sub-menu .sub-menu a {
                padding-left: 60px;
                background: #f0f0f0;
            }
        }
        
        /* Menu Toggle Button */
        .menu-toggle {
            display: none;
            flex-direction: column;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 8px;
            z-index: 1001;
        }
        
        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 3px 0;
            transition: all 0.3s ease;
            display: block;
        }
        
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }
            
            .main-navigation ul {
                display: none;
            }
            
            .main-navigation.toggled ul {
                display: flex;
                flex-direction: column;
            }
        }
    ');
}
add_action('wp_enqueue_scripts', 'staircase_enqueue_assets');

// Custom Walker for Navigation Menu
class Staircase_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    // Start Level - Opens the sub-menu
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    // End Level - Closes the sub-menu
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    // Start Element - Individual menu item
    function start_el(&$output, $item, $depth = 0, $args = null, $current_object_id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Check if item has children
        $has_children = in_array('menu-item-has-children', $classes);
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        // Add dropdown indicator for parent items
        if ($has_children) {
            $item_output .= ' <span class="dropdown-indicator"><svg width="12" height="8" viewBox="0 0 12 8" fill="currentColor"><path d="M6 8L0 0h12z"/></svg></span>';
        }
        
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    // End Element - Close individual menu item
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

// Custom hero section function
function staircase_hero_section($title = '', $subtitle = '', $button_text = '', $button_url = '') {
    $hero_type = staircase_get_hero_type();
    
    if ($hero_type === 'homepage-cherry') {
        staircase_homepage_cherry_hero();
        return;
    }
    
    if ($hero_type === 'homepage-apple') {
        staircase_homepage_apple_hero();
        return;
    }
    
    // Default hero for other templates
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

// Homepage Cherry hero section
function staircase_homepage_cherry_hero() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Debug: Add HTML comment to verify function is being called
    echo "<!-- Homepage Cherry Hero: Post ID = $post_id -->\n";
    
    // Get data from wp_posts and wp_pylons tables
    $cherry_heading = get_the_title(); // wp_posts.post_title
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Debug: Check if pylon data exists
    if ($pylon_data) {
        echo "<!-- Pylon data found for post $post_id -->\n";
        echo "<!-- hero_subheading: " . (!empty($pylon_data['hero_subheading']) ? $pylon_data['hero_subheading'] : 'empty') . " -->\n";
        echo "<!-- chenblock_card1_title: " . (!empty($pylon_data['chenblock_card1_title']) ? $pylon_data['chenblock_card1_title'] : 'empty') . " -->\n";
    } else {
        echo "<!-- No pylon data found for post $post_id -->\n";
    }
    
    // Get hero subheading from wp_pylons
    $cherry_subheading = '';
    if ($pylon_data && !empty($pylon_data['hero_subheading'])) {
        $cherry_subheading = $pylon_data['hero_subheading'];
    }
    
    // Keep existing button logic for now
    $cherry_button_left_text = get_post_meta($post_id, 'cherry_button_left_text', true);
    $cherry_button_left_url = get_post_meta($post_id, 'cherry_button_left_url', true);
    $cherry_button_right_text = get_post_meta($post_id, 'cherry_button_right_text', true);
    $cherry_button_right_url = get_post_meta($post_id, 'cherry_button_right_url', true);
    // Get formatted phone number from database (same as header phone button)
    $cherry_phone_number_raw = staircase_get_header_phone();
    $cherry_phone_number_formatted = staircase_get_formatted_phone();
    
    // Get chenblock card data from wp_pylons
    $zarl_card_1_title = '';
    $zarl_card_1_description = '';
    $zarl_card_2_title = '';
    $zarl_card_2_description = '';
    $zarl_card_3_title = '';
    $zarl_card_3_description = '';
    
    if ($pylon_data) {
        $zarl_card_1_title = !empty($pylon_data['chenblock_card1_title']) ? $pylon_data['chenblock_card1_title'] : '';
        $zarl_card_1_description = !empty($pylon_data['chenblock_card1_desc']) ? $pylon_data['chenblock_card1_desc'] : '';
        
        $zarl_card_2_title = !empty($pylon_data['chenblock_card2_title']) ? $pylon_data['chenblock_card2_title'] : '';
        $zarl_card_2_description = !empty($pylon_data['chenblock_card2_desc']) ? $pylon_data['chenblock_card2_desc'] : '';
        
        $zarl_card_3_title = !empty($pylon_data['chenblock_card3_title']) ? $pylon_data['chenblock_card3_title'] : '';
        $zarl_card_3_description = !empty($pylon_data['chenblock_card3_desc']) ? $pylon_data['chenblock_card3_desc'] : '';
    }
    
    // Keep icons from post_meta for now (or remove if not needed)
    $zarl_card_1_icon = get_post_meta($post_id, 'zarl_card_1_icon', true);
    $zarl_card_2_icon = get_post_meta($post_id, 'zarl_card_2_icon', true);
    $zarl_card_3_icon = get_post_meta($post_id, 'zarl_card_3_icon', true);
    
    // Default values
    if (empty($cherry_subheading)) {
        $cherry_subheading = get_bloginfo('description');
    }
    ?>
    <section class="hero-section homepage-cherry-hero">
        <div class="container">
            <div class="cherry-hero-content">
                <h1 class="cherry-heading"><?php echo esc_html($cherry_heading); ?></h1>
                <?php if ($cherry_subheading): ?>
                    <p class="cherry-subheading"><?php echo esc_html($cherry_subheading); ?></p>
                <?php endif; ?>
                
                <?php if ($cherry_button_left_text || $cherry_button_right_text): ?>
                    <div class="cherry-buttons-container">
                        <?php if ($cherry_button_left_text && $cherry_button_left_url): ?>
                            <a href="<?php echo esc_url($cherry_button_left_url); ?>" class="cherry-button cherry-button-left">
                                <?php echo esc_html($cherry_button_left_text); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($cherry_button_right_text && $cherry_button_right_url): ?>
                            <a href="<?php echo esc_url($cherry_button_right_url); ?>" class="cherry-button cherry-button-right">
                                <?php echo esc_html($cherry_button_right_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($cherry_phone_number_raw): ?>
                    <div class="cherry-phone-container">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $cherry_phone_number_raw)); ?>" class="cherry-phone">
                            <?php echo esc_html($cherry_phone_number_formatted); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php 
    // Show zarl card block if any cards have content
    $has_zarl_cards = !empty($zarl_card_1_title) || !empty($zarl_card_2_title) || !empty($zarl_card_3_title);
    if ($has_zarl_cards): 
    ?>
    <section class="zarl-card-block">
        <div class="container">
            <div class="zarl-cards-grid">
                <?php if (!empty($zarl_card_1_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_1_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_1_icon); ?>" alt="<?php echo esc_attr($zarl_card_1_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_1_title); ?></h3>
                        <?php if (!empty($zarl_card_1_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_1_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($zarl_card_2_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_2_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_2_icon); ?>" alt="<?php echo esc_attr($zarl_card_2_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_2_title); ?></h3>
                        <?php if (!empty($zarl_card_2_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_2_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($zarl_card_3_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_3_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_3_icon); ?>" alt="<?php echo esc_attr($zarl_card_3_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_3_title); ?></h3>
                        <?php if (!empty($zarl_card_3_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_3_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <style>
    .homepage-cherry-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 80px 0;
        position: relative;
    }
    
    .cherry-hero-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .cherry-heading {
        font-size: 3.5rem;
        font-weight: 700;
        margin: 0 0 20px 0;
        line-height: 1.1;
    }
    
    .cherry-subheading {
        font-size: 1.4rem;
        margin: 0 0 40px 0;
        opacity: 0.9;
        line-height: 1.4;
        font-weight: 300;
    }
    
    .cherry-buttons-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 0 0 30px 0;
        flex-wrap: wrap;
    }
    
    .cherry-button {
        display: inline-block;
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease;
        min-width: 180px;
        text-align: center;
    }
    
    .cherry-button-left {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid white;
    }
    
    .cherry-button-left:hover {
        background: white;
        color: #667eea;
    }
    
    .cherry-button-right {
        background: white;
        color: #667eea;
        border: 2px solid white;
    }
    
    .cherry-button-right:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: white;
    }
    
    .cherry-phone-container {
        margin-top: 20px;
    }
    
    .cherry-phone {
        display: inline-block;
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .cherry-phone:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .homepage-cherry-hero {
            padding: 60px 0;
        }
        
        .cherry-heading {
            font-size: 2.5rem;
        }
        
        .cherry-subheading {
            font-size: 1.2rem;
        }
        
        .cherry-buttons-container {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .cherry-button {
            min-width: 250px;
        }
        
        .cherry-phone {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .cherry-heading {
            font-size: 2rem;
        }
        
        .cherry-subheading {
            font-size: 1.1rem;
        }
        
        .cherry-phone {
            font-size: 1.3rem;
        }
    }
    
    /* Zarl Card Block Styles */
    .zarl-card-block {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .zarl-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .zarl-card {
        background: white;
        padding: 40px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .zarl-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .zarl-card-icon {
        margin-bottom: 20px;
    }
    
    .zarl-card-icon img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(230deg) brightness(91%) contrast(80%);
    }
    
    .zarl-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 15px 0;
        line-height: 1.3;
    }
    
    .zarl-card-description {
        font-size: 1rem;
        color: #6c757d;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Responsive Design for Zarl Cards */
    @media (max-width: 992px) {
        .zarl-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .zarl-card {
            padding: 30px 25px;
        }
    }
    
    @media (max-width: 768px) {
        .zarl-card-block {
            padding: 60px 0;
        }
        
        .zarl-cards-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .zarl-card {
            padding: 30px 20px;
        }
        
        .zarl-card-title {
            font-size: 1.2rem;
        }
        
        .zarl-card-icon img {
            width: 50px;
            height: 50px;
        }
    }
    
    @media (max-width: 480px) {
        .zarl-card-block {
            padding: 40px 0;
        }
        
        .zarl-card {
            padding: 25px 15px;
        }
        
        .zarl-card-description {
            font-size: 0.9rem;
        }
    }
    
    /* Paragon Card Styles - Our Services Section */
    .paragon-services-section {
        padding: 80px 0;
        background: #ffffff;
        border-top: 1px solid #e9ecef;
    }
    
    .paragon-section-title {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 50px 0;
        line-height: 1.2;
    }
    
    .paragon-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .paragon-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        text-decoration: none;
        color: inherit;
    }
    
    .paragon-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: inherit;
    }
    
    .paragon-card-image {
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .paragon-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .paragon-card:hover .paragon-card-image img {
        transform: scale(1.05);
    }
    
    .paragon-card-content {
        padding: 25px 20px;
    }
    
    .paragon-card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 12px 0;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .paragon-card-description {
        font-size: 0.95rem;
        color: #6c757d;
        line-height: 1.5;
        margin: 0;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    
    /* Responsive Design for Paragon Cards */
    @media (max-width: 1200px) {
        .paragon-cards-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }
    }
    
    @media (max-width: 992px) {
        .paragon-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .paragon-section-title {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .paragon-services-section {
            padding: 60px 0;
        }
        
        .paragon-cards-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .paragon-section-title {
            font-size: 2rem;
            margin: 0 0 40px 0;
        }
        
        .paragon-card-image {
            height: 200px;
        }
        
        .paragon-card-content {
            padding: 20px 18px;
        }
        
        .paragon-card-title {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 480px) {
        .paragon-services-section {
            padding: 50px 0;
        }
        
        .paragon-section-title {
            font-size: 1.8rem;
            margin: 0 0 30px 0;
        }
        
        .paragon-card-image {
            height: 160px;
        }
        
        .paragon-card-content {
            padding: 18px 15px;
        }
        
        .paragon-card-description {
            font-size: 0.9rem;
        }
    }
    </style>
    <?php
}

// Homepage Apple hero section
function staircase_homepage_apple_hero() {
    $post_id = get_the_ID();
    
    // Get Homepage Apple fields - fallback to cherry fields for identical content
    $apple_heading = get_post_meta($post_id, 'apple_hero_heading', true);
    if (empty($apple_heading)) {
        $apple_heading = get_post_meta($post_id, 'cherry_hero_heading', true);
    }
    
    $apple_subheading = get_post_meta($post_id, 'apple_hero_subheading', true);
    if (empty($apple_subheading)) {
        $apple_subheading = get_post_meta($post_id, 'cherry_hero_subheading', true);
    }
    
    $apple_button_left_text = get_post_meta($post_id, 'apple_button_left_text', true);
    if (empty($apple_button_left_text)) {
        $apple_button_left_text = get_post_meta($post_id, 'cherry_button_left_text', true);
    }
    
    $apple_button_left_url = get_post_meta($post_id, 'apple_button_left_url', true);
    if (empty($apple_button_left_url)) {
        $apple_button_left_url = get_post_meta($post_id, 'cherry_button_left_url', true);
    }
    
    $apple_button_right_text = get_post_meta($post_id, 'apple_button_right_text', true);
    if (empty($apple_button_right_text)) {
        $apple_button_right_text = get_post_meta($post_id, 'cherry_button_right_text', true);
    }
    
    $apple_button_right_url = get_post_meta($post_id, 'apple_button_right_url', true);
    if (empty($apple_button_right_url)) {
        $apple_button_right_url = get_post_meta($post_id, 'cherry_button_right_url', true);
    }
    // Get formatted phone number from database (same as header phone button)
    $apple_phone_number_raw = staircase_get_header_phone();
    $apple_phone_number_formatted = staircase_get_formatted_phone();
    
    // Get zarl card data - shared with cherry template
    $zarl_card_1_icon = get_post_meta($post_id, 'zarl_card_1_icon', true);
    $zarl_card_1_title = get_post_meta($post_id, 'zarl_card_1_title', true);
    $zarl_card_1_description = get_post_meta($post_id, 'zarl_card_1_description', true);
    
    $zarl_card_2_icon = get_post_meta($post_id, 'zarl_card_2_icon', true);
    $zarl_card_2_title = get_post_meta($post_id, 'zarl_card_2_title', true);
    $zarl_card_2_description = get_post_meta($post_id, 'zarl_card_2_description', true);
    
    $zarl_card_3_icon = get_post_meta($post_id, 'zarl_card_3_icon', true);
    $zarl_card_3_title = get_post_meta($post_id, 'zarl_card_3_title', true);
    $zarl_card_3_description = get_post_meta($post_id, 'zarl_card_3_description', true);
    
    // Default values (same logic as cherry)
    if (empty($apple_heading)) {
        $apple_heading = get_the_title();
    }
    if (empty($apple_subheading)) {
        $apple_subheading = get_bloginfo('description');
    }
    
    ?>
    <section class="hero-section homepage-apple-hero">
        <div class="container">
            <div class="apple-hero-content">
                <h1 class="apple-heading"><?php echo esc_html($apple_heading); ?></h1>
                <?php if ($apple_subheading): ?>
                    <p class="apple-subheading"><?php echo esc_html($apple_subheading); ?></p>
                <?php endif; ?>
                
                <?php if ($apple_button_left_text || $apple_button_right_text): ?>
                    <div class="apple-buttons-container">
                        <?php if ($apple_button_left_text && $apple_button_left_url): ?>
                            <a href="<?php echo esc_url($apple_button_left_url); ?>" class="apple-button apple-button-left">
                                <?php echo esc_html($apple_button_left_text); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($apple_button_right_text && $apple_button_right_url): ?>
                            <a href="<?php echo esc_url($apple_button_right_url); ?>" class="apple-button apple-button-right">
                                <?php echo esc_html($apple_button_right_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($apple_phone_number_raw): ?>
                    <div class="apple-phone-container">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $apple_phone_number_raw)); ?>" class="apple-phone">
                            <?php echo esc_html($apple_phone_number_formatted); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php 
    // Show zarl card block if any cards have content
    $has_zarl_cards = !empty($zarl_card_1_title) || !empty($zarl_card_2_title) || !empty($zarl_card_3_title);
    if ($has_zarl_cards): 
    ?>
    <section class="zarl-card-block">
        <div class="container">
            <div class="zarl-cards-grid">
                <?php if (!empty($zarl_card_1_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_1_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_1_icon); ?>" alt="<?php echo esc_attr($zarl_card_1_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_1_title); ?></h3>
                        <?php if (!empty($zarl_card_1_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_1_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($zarl_card_2_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_2_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_2_icon); ?>" alt="<?php echo esc_attr($zarl_card_2_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_2_title); ?></h3>
                        <?php if (!empty($zarl_card_2_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_2_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($zarl_card_3_title)): ?>
                    <div class="zarl-card">
                        <?php if (!empty($zarl_card_3_icon)): ?>
                            <div class="zarl-card-icon">
                                <img src="<?php echo esc_url($zarl_card_3_icon); ?>" alt="<?php echo esc_attr($zarl_card_3_title); ?>" />
                            </div>
                        <?php endif; ?>
                        <h3 class="zarl-card-title"><?php echo esc_html($zarl_card_3_title); ?></h3>
                        <?php if (!empty($zarl_card_3_description)): ?>
                            <p class="zarl-card-description"><?php echo esc_html($zarl_card_3_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <style>
    .homepage-apple-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 80px 0;
        position: relative;
    }
    
    .apple-hero-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .apple-heading {
        font-size: 3.5rem;
        font-weight: 700;
        margin: 0 0 20px 0;
        line-height: 1.1;
    }
    
    .apple-subheading {
        font-size: 1.4rem;
        margin: 0 0 40px 0;
        opacity: 0.9;
        line-height: 1.4;
        font-weight: 300;
    }
    
    .apple-buttons-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 0 0 30px 0;
        flex-wrap: wrap;
    }
    
    .apple-button {
        display: inline-block;
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease;
        min-width: 180px;
        text-align: center;
    }
    
    .apple-button-left {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid white;
    }
    
    .apple-button-left:hover {
        background: white;
        color: #667eea;
    }
    
    .apple-button-right {
        background: white;
        color: #667eea;
        border: 2px solid white;
    }
    
    .apple-button-right:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: white;
    }
    
    .apple-phone-container {
        margin-top: 20px;
    }
    
    .apple-phone {
        display: inline-block;
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .apple-phone:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .homepage-apple-hero {
            padding: 60px 0;
        }
        
        .apple-heading {
            font-size: 2.5rem;
        }
        
        .apple-subheading {
            font-size: 1.2rem;
        }
        
        .apple-buttons-container {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .apple-button {
            min-width: 250px;
        }
        
        .apple-phone {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .apple-heading {
            font-size: 2rem;
        }
        
        .apple-subheading {
            font-size: 1.1rem;
        }
        
        .apple-phone {
            font-size: 1.3rem;
        }
    }
    </style>
    <?php
}

// Get the template for current page
function staircase_get_current_template() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // First try to get template from wp_pylons table (imported pages)
    $pylon_template = $wpdb->get_var($wpdb->prepare(
        "SELECT staircase_page_template_desired 
         FROM {$wpdb->prefix}pylons 
         WHERE rel_wp_post_id = %d 
         AND staircase_page_template_desired IS NOT NULL 
         AND staircase_page_template_desired != ''", 
        $post_id
    ));
    
    if ($pylon_template) {
        // Normalize the template name to match available templates
        return staircase_normalize_template_name($pylon_template);
    }
    
    // Fallback to existing logic for manually created pages
    $template = get_post_meta($post_id, 'staircase_page_template', true);
    
    // If no template set or set to default, use theme settings
    if (empty($template) || $template === 'default') {
        $template = get_option('staircase_default_template', 'hero-full');
    }
    
    return $template;
}

// Custom function to check if hero should be displayed based on template
function staircase_should_show_hero() {
    $template = staircase_get_current_template();
    
    // Hero templates
    return in_array($template, array('hero-full', 'hero-minimal', 'homepage-cherry', 'homepage-apple'));
}

// Get hero type for current page
function staircase_get_hero_type() {
    $template = staircase_get_current_template();
    
    if ($template === 'hero-full') {
        return 'full';
    } elseif ($template === 'hero-minimal') {
        return 'minimal';
    } elseif ($template === 'homepage-cherry') {
        return 'homepage-cherry';
    } elseif ($template === 'homepage-apple') {
        return 'homepage-apple';
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

// Add Staircase theme page options meta box
function staircase_add_meta_boxes() {
    add_meta_box(
        'staircase_page_options',
        'Staircase Theme Page Options',
        'staircase_page_options_meta_box_callback',
        'page',
        'side',
        'high'
    );
    
    // Also add to posts if desired
    add_meta_box(
        'staircase_page_options_post',
        'Staircase Theme Page Options',
        'staircase_page_options_meta_box_callback',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'staircase_add_meta_boxes');

// Get available page templates
function staircase_get_page_templates() {
    return array(
        'default' => 'Default (use theme settings)',
        'hero-full' => 'Full Hero Layout',
        'hero-minimal' => 'Minimal Hero Layout',
        'homepage-cherry' => 'Homepage Cherry',
        'homepage-apple' => 'Homepage Apple',
        'no-hero' => 'Standard Layout',
        'content-only' => 'Content Only',
        'sections-builder' => 'Sections Builder'
    );
}

// Normalize template name from user input to match available templates
function staircase_normalize_template_name($user_input) {
    if (empty($user_input)) {
        return 'default';
    }
    
    // Get available templates
    $templates = staircase_get_page_templates();
    
    // Normalize user input: lowercase, remove spaces, trim
    $normalized_input = strtolower(trim(str_replace(' ', '', $user_input)));
    
    // Check for exact matches first
    foreach ($templates as $template_key => $template_label) {
        $normalized_key = strtolower(str_replace(' ', '', $template_key));
        $normalized_label = strtolower(str_replace(' ', '', $template_label));
        
        // Match against template key or label
        if ($normalized_input === $normalized_key || $normalized_input === $normalized_label) {
            return $template_key;
        }
    }
    
    // If no match found, return default
    return 'default';
}

// Page options meta box callback
function staircase_page_options_meta_box_callback($post) {
    wp_nonce_field('staircase_page_options_meta_box', 'staircase_page_options_meta_box_nonce');
    
    $selected_template = get_post_meta($post->ID, 'staircase_page_template', true);
    $templates = staircase_get_page_templates();
    
    // Get default template from theme settings
    $default_template = get_option('staircase_default_template', 'hero-full');
    ?>
    <p>
        <label for="staircase_page_template"><strong>Page Template:</strong></label><br>
        <select id="staircase_page_template" name="staircase_page_template" style="width: 100%; margin-top: 5px;">
            <?php foreach ($templates as $value => $label): ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_template, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    
    <?php if ($selected_template === '' || $selected_template === 'default'): ?>
        <p style="margin-top: 8px; padding: 8px; background: #f0f0f1; border-radius: 4px; font-size: 12px; color: #646970;">
            <strong>Currently using:</strong> <?php echo esc_html($templates[$default_template] ?? 'Default'); ?><br>
            <em>Set in <a href="<?php echo admin_url('admin.php?page=staircase-settings'); ?>" target="_blank">Staircase Settings</a></em>
        </p>
    <?php endif; ?>
    
    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #ddd;">
        <p style="margin: 0; font-size: 12px; color: #646970;">
            <strong>Template Guide:</strong><br>
            • <strong>Full Hero:</strong> Large hero with background<br>
            • <strong>Minimal Hero:</strong> Compact title section<br>
            • <strong>Homepage Cherry:</strong> Hero with buttons & phone<br>
            • <strong>Standard:</strong> Traditional page layout<br>
            • <strong>Content Only:</strong> Just page content<br>
            • <strong>Sections Builder:</strong> Custom sections
        </p>
    </div>
    
    <?php if ($selected_template === 'homepage-cherry'): ?>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #0073aa;">
            <h4 style="margin: 0 0 10px 0; color: #0073aa;">Homepage Cherry Options</h4>
            
            <p style="margin-bottom: 8px;">
                <label for="cherry_hero_heading"><strong>Hero Heading:</strong></label><br>
                <input type="text" id="cherry_hero_heading" name="cherry_hero_heading" 
                       value="<?php echo esc_attr(get_post_meta($post->ID, 'cherry_hero_heading', true)); ?>" 
                       style="width: 100%; margin-top: 3px;" 
                       placeholder="<?php echo esc_attr(get_the_title($post->ID) ?: 'Main Heading'); ?>">
            </p>
            
            <p style="margin-bottom: 8px;">
                <label for="cherry_hero_subheading"><strong>Hero Subheading:</strong></label><br>
                <input type="text" id="cherry_hero_subheading" name="cherry_hero_subheading" 
                       value="<?php echo esc_attr(get_post_meta($post->ID, 'cherry_hero_subheading', true)); ?>" 
                       style="width: 100%; margin-top: 3px;" 
                       placeholder="Subtitle or tagline">
            </p>
            
            <div style="margin-bottom: 8px;">
                <strong>Buttons:</strong>
                <div style="display: flex; gap: 8px; margin-top: 5px;">
                    <div style="flex: 1;">
                        <input type="text" id="cherry_button_left_text" name="cherry_button_left_text" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'cherry_button_left_text', true)); ?>" 
                               style="width: 100%; margin-bottom: 3px; font-size: 11px;" 
                               placeholder="Left Button Text">
                        <input type="url" id="cherry_button_left_url" name="cherry_button_left_url" 
                               value="<?php echo esc_url(get_post_meta($post->ID, 'cherry_button_left_url', true)); ?>" 
                               style="width: 100%; font-size: 11px;" 
                               placeholder="Left Button URL">
                    </div>
                    <div style="flex: 1;">
                        <input type="text" id="cherry_button_right_text" name="cherry_button_right_text" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'cherry_button_right_text', true)); ?>" 
                               style="width: 100%; margin-bottom: 3px; font-size: 11px;" 
                               placeholder="Right Button Text">
                        <input type="url" id="cherry_button_right_url" name="cherry_button_right_url" 
                               value="<?php echo esc_url(get_post_meta($post->ID, 'cherry_button_right_url', true)); ?>" 
                               style="width: 100%; font-size: 11px;" 
                               placeholder="Right Button URL">
                    </div>
                </div>
            </div>
            
            <p style="font-size: 11px; color: #666; margin-top: 10px;">
                <strong>Note:</strong> Phone number is automatically pulled from the database (same as header phone button).<br>
                Leave button fields empty to hide those elements.
            </p>
        </div>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #28a745;">
            <h4 style="margin: 0 0 15px 0; color: #28a745;">Zarl Card Block (3 Cards)</h4>
            
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div style="margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                    <h5 style="margin: 0 0 8px 0; color: #495057;">Card <?php echo $i; ?></h5>
                    
                    <p style="margin-bottom: 6px;">
                        <label for="zarl_card_<?php echo $i; ?>_icon"><strong>Icon URL:</strong></label><br>
                        <input type="url" id="zarl_card_<?php echo $i; ?>_icon" name="zarl_card_<?php echo $i; ?>_icon" 
                               value="<?php echo esc_url(get_post_meta($post->ID, "zarl_card_{$i}_icon", true)); ?>" 
                               style="width: 100%; font-size: 11px; margin-top: 2px;" 
                               placeholder="https://example.com/icon.png">
                    </p>
                    
                    <p style="margin-bottom: 6px;">
                        <label for="zarl_card_<?php echo $i; ?>_title"><strong>Title:</strong></label><br>
                        <input type="text" id="zarl_card_<?php echo $i; ?>_title" name="zarl_card_<?php echo $i; ?>_title" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, "zarl_card_{$i}_title", true)); ?>" 
                               style="width: 100%; font-size: 11px; margin-top: 2px;" 
                               placeholder="Card Title">
                    </p>
                    
                    <p style="margin-bottom: 6px;">
                        <label for="zarl_card_<?php echo $i; ?>_description"><strong>Description:</strong></label><br>
                        <textarea id="zarl_card_<?php echo $i; ?>_description" name="zarl_card_<?php echo $i; ?>_description" 
                                  rows="2" style="width: 100%; font-size: 11px; margin-top: 2px; resize: vertical;" 
                                  placeholder="Card description text"><?php echo esc_textarea(get_post_meta($post->ID, "zarl_card_{$i}_description", true)); ?></textarea>
                    </p>
                </div>
            <?php endfor; ?>
            
            <p style="font-size: 11px; color: #666; margin-top: 10px;">
                Cards will only display if they have a title. Leave title empty to hide a card.
            </p>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('staircase_page_template');
            const cherryOptions = templateSelect.parentNode.querySelector('[style*="border-top: 2px solid #0073aa"]');
            
            function toggleCherryOptions() {
                if (templateSelect.value === 'homepage-cherry') {
                    cherryOptions.style.display = 'block';
                } else {
                    cherryOptions.style.display = 'none';
                }
            }
            
            templateSelect.addEventListener('change', toggleCherryOptions);
            toggleCherryOptions(); // Initial state
        });
        </script>
    <?php endif; ?>
    
    <?php if ($selected_template === 'homepage-apple'): ?>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #ff6b6b;">
            <h4 style="margin: 0 0 10px 0; color: #ff6b6b;">Homepage Apple Options</h4>
            
            <p style="margin-bottom: 8px;">
                <label for="apple_hero_heading"><strong>Hero Heading:</strong></label><br>
                <input type="text" id="apple_hero_heading" name="apple_hero_heading" 
                       value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_hero_heading', true)); ?>" 
                       style="width: 100%; margin-top: 3px;" 
                       placeholder="<?php echo esc_attr(get_the_title($post->ID) ?: 'Main Heading'); ?>">
            </p>
            
            <p style="margin-bottom: 8px;">
                <label for="apple_hero_subheading"><strong>Hero Subheading:</strong></label><br>
                <input type="text" id="apple_hero_subheading" name="apple_hero_subheading" 
                       value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_hero_subheading', true)); ?>" 
                       style="width: 100%; margin-top: 3px;" 
                       placeholder="Subtitle or tagline">
            </p>
            
            <div style="margin-bottom: 8px;">
                <strong>Buttons:</strong>
                <div style="display: flex; gap: 8px; margin-top: 5px;">
                    <div style="flex: 1;">
                        <input type="text" id="apple_button_left_text" name="apple_button_left_text" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_button_left_text', true)); ?>" 
                               style="width: 100%; margin-bottom: 3px;" placeholder="Left Button Text">
                        <input type="text" id="apple_button_left_url" name="apple_button_left_url" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_button_left_url', true)); ?>" 
                               style="width: 100%;" placeholder="Left Button URL">
                    </div>
                    <div style="flex: 1;">
                        <input type="text" id="apple_button_right_text" name="apple_button_right_text" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_button_right_text', true)); ?>" 
                               style="width: 100%; margin-bottom: 3px;" placeholder="Right Button Text">
                        <input type="text" id="apple_button_right_url" name="apple_button_right_url" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, 'apple_button_right_url', true)); ?>" 
                               style="width: 100%;" placeholder="Right Button URL">
                    </div>
                </div>
            </div>
            
            <h4 style="margin: 15px 0 5px 0; color: #ff6b6b;">Apple Card Blocks (Zarl)</h4>
            <p style="font-size: 12px; margin-bottom: 10px; color: #666;">Add up to 3 cards that will display below the hero section.</p>
            
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div style="margin-bottom: 10px; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                    <strong>Card <?php echo $i; ?>:</strong>
                    <p style="margin: 5px 0 3px 0;">
                        <input type="text" name="zarl_card_<?php echo $i; ?>_title" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, "zarl_card_{$i}_title", true)); ?>" 
                               style="width: 100%;" placeholder="Card <?php echo $i; ?> title">
                    </p>
                    <p style="margin: 3px 0;">
                        <input type="url" name="zarl_card_<?php echo $i; ?>_icon" 
                               value="<?php echo esc_attr(get_post_meta($post->ID, "zarl_card_{$i}_icon", true)); ?>" 
                               style="width: 100%;" placeholder="Icon URL (optional)">
                    </p>
                    <p style="margin: 3px 0 0 0;">
                        <textarea name="zarl_card_<?php echo $i; ?>_description" 
                                  style="width: 100%; height: 50px; resize: vertical;" 
                                  placeholder="Card description text"><?php echo esc_textarea(get_post_meta($post->ID, "zarl_card_{$i}_description", true)); ?></textarea>
                    </p>
                </div>
            <?php endfor; ?>
            
            <p style="font-size: 11px; color: #666; margin-top: 10px;">
                Cards will only display if they have a title. Leave title empty to hide a card.
            </p>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('staircase_page_template');
            const appleOptions = templateSelect.parentNode.querySelector('[style*="border-top: 2px solid #ff6b6b"]');
            
            function toggleAppleOptions() {
                if (templateSelect.value === 'homepage-apple') {
                    appleOptions.style.display = 'block';
                } else {
                    appleOptions.style.display = 'none';
                }
            }
            
            templateSelect.addEventListener('change', toggleAppleOptions);
            toggleAppleOptions(); // Initial state
        });
        </script>
    <?php endif; ?>
    
    <?php
}

// Save page options meta box data
function staircase_save_page_options_meta($post_id) {
    // Check nonce
    if (!isset($_POST['staircase_page_options_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['staircase_page_options_meta_box_nonce'], 'staircase_page_options_meta_box')) {
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
    
    // Save template selection
    if (isset($_POST['staircase_page_template'])) {
        $template = sanitize_text_field($_POST['staircase_page_template']);
        update_post_meta($post_id, 'staircase_page_template', $template);
    }
    
    // Save Homepage Cherry fields
    if (isset($_POST['cherry_hero_heading'])) {
        update_post_meta($post_id, 'cherry_hero_heading', sanitize_text_field($_POST['cherry_hero_heading']));
    }
    
    if (isset($_POST['cherry_hero_subheading'])) {
        update_post_meta($post_id, 'cherry_hero_subheading', sanitize_text_field($_POST['cherry_hero_subheading']));
    }
    
    if (isset($_POST['cherry_button_left_text'])) {
        update_post_meta($post_id, 'cherry_button_left_text', sanitize_text_field($_POST['cherry_button_left_text']));
    }
    
    if (isset($_POST['cherry_button_left_url'])) {
        update_post_meta($post_id, 'cherry_button_left_url', esc_url_raw($_POST['cherry_button_left_url']));
    }
    
    if (isset($_POST['cherry_button_right_text'])) {
        update_post_meta($post_id, 'cherry_button_right_text', sanitize_text_field($_POST['cherry_button_right_text']));
    }
    
    if (isset($_POST['cherry_button_right_url'])) {
        update_post_meta($post_id, 'cherry_button_right_url', esc_url_raw($_POST['cherry_button_right_url']));
    }
    
    // Save Homepage Apple fields
    if (isset($_POST['apple_hero_heading'])) {
        update_post_meta($post_id, 'apple_hero_heading', sanitize_text_field($_POST['apple_hero_heading']));
    }
    
    if (isset($_POST['apple_hero_subheading'])) {
        update_post_meta($post_id, 'apple_hero_subheading', sanitize_text_field($_POST['apple_hero_subheading']));
    }
    
    if (isset($_POST['apple_button_left_text'])) {
        update_post_meta($post_id, 'apple_button_left_text', sanitize_text_field($_POST['apple_button_left_text']));
    }
    
    if (isset($_POST['apple_button_left_url'])) {
        update_post_meta($post_id, 'apple_button_left_url', esc_url_raw($_POST['apple_button_left_url']));
    }
    
    if (isset($_POST['apple_button_right_text'])) {
        update_post_meta($post_id, 'apple_button_right_text', sanitize_text_field($_POST['apple_button_right_text']));
    }
    
    if (isset($_POST['apple_button_right_url'])) {
        update_post_meta($post_id, 'apple_button_right_url', esc_url_raw($_POST['apple_button_right_url']));
    }
    
    // Phone number is now pulled directly from database, no need to save it as post meta
    
    // Save Zarl Card fields
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["zarl_card_{$i}_icon"])) {
            update_post_meta($post_id, "zarl_card_{$i}_icon", esc_url_raw($_POST["zarl_card_{$i}_icon"]));
        }
        
        if (isset($_POST["zarl_card_{$i}_title"])) {
            update_post_meta($post_id, "zarl_card_{$i}_title", sanitize_text_field($_POST["zarl_card_{$i}_title"]));
        }
        
        if (isset($_POST["zarl_card_{$i}_description"])) {
            update_post_meta($post_id, "zarl_card_{$i}_description", sanitize_textarea_field($_POST["zarl_card_{$i}_description"]));
        }
    }
}
add_action('save_post', 'staircase_save_page_options_meta');

// Get phone number from zen_sitespren table
function staircase_get_header_phone() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'zen_sitespren';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        return '';
    }
    
    // Get phone number from first row
    $phone_number = $wpdb->get_var("SELECT driggs_phone_1 FROM {$table_name} LIMIT 1");
    
    return $phone_number ? $phone_number : '';
}

// Format phone number based on country code
function staircase_format_phone_number($phone_number) {
    global $wpdb;
    
    if (empty($phone_number)) {
        return '';
    }
    
    $table_name = $wpdb->prefix . 'zen_sitespren';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        return $phone_number;
    }
    
    // Get country code from database
    $country_code = $wpdb->get_var("SELECT driggs_phone_country_code FROM {$table_name} LIMIT 1");
    
    // If country code is 1 (US/Canada), format as (xxx) xxx-xxxx
    if ($country_code === '1') {
        // Remove all non-numeric characters
        $cleaned_phone = preg_replace('/[^0-9]/', '', $phone_number);
        
        // Format as (xxx) xxx-xxxx if we have 10 digits
        if (strlen($cleaned_phone) === 10) {
            return '(' . substr($cleaned_phone, 0, 3) . ') ' . substr($cleaned_phone, 3, 3) . '-' . substr($cleaned_phone, 6, 4);
        } elseif (strlen($cleaned_phone) === 11 && substr($cleaned_phone, 0, 1) === '1') {
            // Handle numbers that include country code 1
            return '(' . substr($cleaned_phone, 1, 3) . ') ' . substr($cleaned_phone, 4, 3) . '-' . substr($cleaned_phone, 7, 4);
        }
    }
    
    // For other country codes or invalid formats, return original number
    return $phone_number;
}

// Get formatted phone number for display
function staircase_get_formatted_phone() {
    $raw_phone = staircase_get_header_phone();
    return staircase_format_phone_number($raw_phone);
}

// Add Staircase theme management menu
function staircase_add_admin_menu() {
    // Add main Staircase menu at position 2 (right after Dashboard)
    add_menu_page(
        'Staircase Theme',          // Page title
        'Staircase',                // Menu title
        'manage_options',           // Capability
        'staircase-theme',          // Menu slug
        'staircase_main_page',      // Callback function
        'dashicons-admin-appearance', // Icon
        2                          // Position (top of menu)
    );
    
    // Add submenu pages
    add_submenu_page(
        'staircase-theme',
        'Template Management',
        'Templates',
        'manage_options',
        'staircase-templates',
        'staircase_templates_page'
    );
    
    add_submenu_page(
        'staircase-theme',
        'Page Archetypes',
        'Page Archetypes',
        'manage_options',
        'staircase-archetypes',
        'staircase_archetypes_page'
    );
    
    add_submenu_page(
        'staircase-theme',
        'Theme Settings',
        'Settings',
        'manage_options',
        'staircase-settings',
        'staircase_settings_page'
    );
    
    add_submenu_page(
        'staircase-theme',
        'Component Library',
        'Components',
        'manage_options',
        'staircase-components',
        'staircase_components_page'
    );
    
    add_submenu_page(
        'staircase-theme',
        'Zaramax Footer Management',
        'Footer',
        'manage_options',
        'zaramax_footer_mar',
        'zaramax_footer_management_page'
    );
}
add_action('admin_menu', 'staircase_add_admin_menu');

// Main Staircase page
function staircase_main_page() {
    ?>
    <div class="wrap">
        <h1>Staircase Theme Management</h1>
        <div class="staircase-dashboard">
            <div class="staircase-overview-cards">
                <div class="staircase-card">
                    <h3>Template System</h3>
                    <p>Manage page templates and layouts for your site.</p>
                    <a href="<?php echo admin_url('admin.php?page=staircase-templates'); ?>" class="button button-primary">Manage Templates</a>
                </div>
                
                <div class="staircase-card">
                    <h3>Page Archetypes</h3>
                    <p>Define and configure different page types and their options.</p>
                    <a href="<?php echo admin_url('admin.php?page=staircase-archetypes'); ?>" class="button button-primary">Configure Archetypes</a>
                </div>
                
                <div class="staircase-card">
                    <h3>Theme Settings</h3>
                    <p>Global theme configuration and customization options.</p>
                    <a href="<?php echo admin_url('admin.php?page=staircase-settings'); ?>" class="button button-primary">Theme Settings</a>
                </div>
                
                <div class="staircase-card">
                    <h3>Component Library</h3>
                    <p>Reusable components and sections for building pages.</p>
                    <a href="<?php echo admin_url('admin.php?page=staircase-components'); ?>" class="button button-primary">View Components</a>
                </div>
            </div>
            
            <div class="staircase-quick-stats">
                <h2>Quick Overview</h2>
                <div class="stats-grid">
                    <?php
                    // Get some basic stats
                    $total_pages = wp_count_posts('page');
                    $total_posts = wp_count_posts('post');
                    
                    // Check if Ruplin plugin is active
                    $ruplin_active = class_exists('SnefuruPlugin');
                    
                    global $wpdb;
                    $orbitposts_count = 0;
                    if ($ruplin_active) {
                        $table_name = $wpdb->prefix . 'zen_orbitposts';
                        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
                            $orbitposts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
                        }
                    }
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_pages->publish; ?></span>
                        <span class="stat-label">Pages</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_posts->publish; ?></span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $ruplin_active ? '✓' : '✗'; ?></span>
                        <span class="stat-label">Ruplin Plugin</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $orbitposts_count; ?></span>
                        <span class="stat-label">Enhanced Pages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .staircase-dashboard {
        max-width: 1200px;
        margin-top: 20px;
    }
    
    .staircase-overview-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .staircase-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .staircase-card h3 {
        margin: 0 0 10px 0;
        color: #23282d;
        font-size: 18px;
    }
    
    .staircase-card p {
        margin: 0 0 15px 0;
        color: #666;
    }
    
    .staircase-quick-stats {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
    }
    
    .staircase-quick-stats h2 {
        margin: 0 0 15px 0;
        font-size: 20px;
        color: #23282d;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 6px;
    }
    
    .stat-number {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color: #0073aa;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    </style>
    <?php
}

// Templates management page
function staircase_templates_page() {
    ?>
    <div class="wrap">
        <h1>Template Management</h1>
        <p>Manage and configure page templates for the Staircase theme.</p>
        
        <div class="staircase-templates-section">
            <h2>Available Templates</h2>
            <div class="template-list">
                <?php
                $templates = array(
                    'hero-full' => array(
                        'name' => 'Full Hero Layout',
                        'description' => 'Large hero section with background image or video, followed by content',
                        'features' => array('Hero Section', 'Page Content', 'Optional CTA')
                    ),
                    'hero-minimal' => array(
                        'name' => 'Minimal Hero Layout',
                        'description' => 'Compact hero section with title and subtitle',
                        'features' => array('Minimal Hero', 'Page Content', 'Clean Design')
                    ),
                    'homepage-cherry' => array(
                        'name' => 'Homepage Cherry',
                        'description' => 'Centered hero with heading, subheading, dual buttons, and phone number',
                        'features' => array('Centered Hero', 'Two Buttons', 'Phone Number', 'Page Content')
                    ),
                    'homepage-apple' => array(
                        'name' => 'Homepage Apple',
                        'description' => 'Centered hero with warm gradient, heading, subheading, dual buttons, and phone number',
                        'features' => array('Warm Gradient Hero', 'Two Buttons', 'Phone Number', 'Page Content')
                    ),
                    'no-hero' => array(
                        'name' => 'Standard Layout',
                        'description' => 'Traditional page layout without hero section',
                        'features' => array('Page Title', 'Page Content', 'Sidebar Optional')
                    ),
                    'content-only' => array(
                        'name' => 'Content Only',
                        'description' => 'Clean content-focused layout without distractions',
                        'features' => array('Page Content Only', 'Full Width', 'Minimal UI')
                    ),
                    'sections-builder' => array(
                        'name' => 'Sections Builder',
                        'description' => 'Flexible layout with customizable sections',
                        'features' => array('Multiple Sections', 'Drag & Drop', 'Custom Components')
                    )
                );
                
                foreach ($templates as $key => $template) {
                    ?>
                    <div class="template-item">
                        <div class="template-preview">
                            <div class="template-mockup template-<?php echo $key; ?>">
                                <!-- Template preview mockup -->
                                <div class="mockup-content"></div>
                            </div>
                        </div>
                        <div class="template-info">
                            <h3><?php echo $template['name']; ?></h3>
                            <p><?php echo $template['description']; ?></p>
                            <ul class="template-features">
                                <?php foreach ($template['features'] as $feature): ?>
                                    <li><?php echo $feature; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="template-actions">
                                <button class="button button-secondary">Preview</button>
                                <button class="button button-primary">Configure</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    
    <style>
    .staircase-templates-section {
        margin-top: 20px;
    }
    
    .template-list {
        display: grid;
        gap: 20px;
        margin-top: 15px;
    }
    
    .template-item {
        display: flex;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .template-preview {
        width: 200px;
        padding: 20px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .template-mockup {
        width: 120px;
        height: 90px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        position: relative;
        overflow: hidden;
    }
    
    .template-info {
        flex: 1;
        padding: 20px;
    }
    
    .template-info h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
        color: #23282d;
    }
    
    .template-info p {
        margin: 0 0 15px 0;
        color: #666;
    }
    
    .template-features {
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
    }
    
    .template-features li {
        display: inline-block;
        background: #e8f4f8;
        color: #0073aa;
        padding: 3px 8px;
        margin: 2px 5px 2px 0;
        border-radius: 12px;
        font-size: 12px;
    }
    
    .template-actions {
        display: flex;
        gap: 10px;
    }
    </style>
    <?php
}

// Page Archetypes management page
function staircase_archetypes_page() {
    ?>
    <div class="wrap">
        <h1>Page Archetypes</h1>
        <p>Define and manage different page types and their configuration options.</p>
        
        <div class="staircase-archetypes-section">
            <h2>Available Archetypes</h2>
            
            <div class="archetypes-grid">
                <?php
                $archetypes = array(
                    'homepage' => array(
                        'name' => 'Homepage',
                        'description' => 'Main landing page with hero and feature sections',
                        'template' => 'hero-full',
                        'options' => array('Hero Section', 'Feature Blocks', 'CTA Section', 'Latest Posts')
                    ),
                    'about' => array(
                        'name' => 'About Page',
                        'description' => 'Company or personal information page',
                        'template' => 'hero-minimal',
                        'options' => array('Team Section', 'Timeline', 'Values Section')
                    ),
                    'services' => array(
                        'name' => 'Services Page',
                        'description' => 'Service listings and descriptions',
                        'template' => 'no-hero',
                        'options' => array('Service Grid', 'Pricing Tables', 'FAQ Section')
                    ),
                    'contact' => array(
                        'name' => 'Contact Page',
                        'description' => 'Contact information and forms',
                        'template' => 'hero-minimal',
                        'options' => array('Contact Form', 'Location Map', 'Contact Info')
                    ),
                    'landing' => array(
                        'name' => 'Landing Page',
                        'description' => 'Conversion-focused single page',
                        'template' => 'hero-full',
                        'options' => array('Hero CTA', 'Benefits Section', 'Testimonials', 'Conversion Form')
                    ),
                    'blog' => array(
                        'name' => 'Blog/News',
                        'description' => 'Blog posts and news articles',
                        'template' => 'content-only',
                        'options' => array('Post Grid', 'Categories', 'Tags', 'Search')
                    )
                );
                
                foreach ($archetypes as $key => $archetype) {
                    ?>
                    <div class="archetype-card">
                        <div class="archetype-header">
                            <h3><?php echo $archetype['name']; ?></h3>
                            <span class="archetype-template">Template: <?php echo $archetype['template']; ?></span>
                        </div>
                        <p><?php echo $archetype['description']; ?></p>
                        
                        <div class="archetype-options">
                            <h4>Available Options:</h4>
                            <ul>
                                <?php foreach ($archetype['options'] as $option): ?>
                                    <li><?php echo $option; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="archetype-actions">
                            <button class="button button-secondary" onclick="editArchetype('<?php echo $key; ?>')">Edit</button>
                            <button class="button button-primary" onclick="createPageFromArchetype('<?php echo $key; ?>')">Create Page</button>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <div class="add-archetype-section">
                <button class="button button-primary">+ Create New Archetype</button>
            </div>
        </div>
    </div>
    
    <style>
    .staircase-archetypes-section {
        margin-top: 20px;
    }
    
    .archetypes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }
    
    .archetype-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .archetype-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .archetype-header h3 {
        margin: 0;
        font-size: 18px;
        color: #23282d;
    }
    
    .archetype-template {
        background: #f0f0f0;
        color: #666;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
    }
    
    .archetype-options h4 {
        margin: 15px 0 8px 0;
        font-size: 14px;
        color: #555;
    }
    
    .archetype-options ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .archetype-options li {
        color: #666;
        font-size: 13px;
        margin-bottom: 3px;
    }
    
    .archetype-actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
    }
    
    .add-archetype-section {
        margin-top: 30px;
        text-align: center;
        padding: 30px;
        background: #f9f9f9;
        border: 2px dashed #ddd;
        border-radius: 8px;
    }
    </style>
    
    <script>
    function editArchetype(key) {
        alert('Edit archetype: ' + key + ' (functionality to be implemented)');
    }
    
    function createPageFromArchetype(key) {
        if (confirm('Create a new page using the ' + key + ' archetype?')) {
            // This would redirect to wp-admin/post-new.php with archetype parameter
            window.location.href = 'post-new.php?post_type=page&archetype=' + key;
        }
    }
    </script>
    <?php
}

// Enqueue media uploader scripts for admin
function staircase_admin_scripts($hook) {
    // Debug: Let's see what hooks are available
    error_log('Admin Hook: ' . $hook);
    
    // Load media scripts on all Staircase admin pages
    if (strpos($hook, 'staircase') !== false || 
        (isset($_GET['page']) && strpos($_GET['page'], 'staircase') !== false)) {
        
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        
        // Add inline script to test if media is loaded
        wp_add_inline_script('jquery', '
            console.log("Media scripts loading for hook: ' . $hook . '");
            console.log("wp.media available:", typeof wp !== "undefined" && typeof wp.media !== "undefined");
        ');
    }
}
add_action('admin_enqueue_scripts', 'staircase_admin_scripts');

// Theme Settings page
function staircase_settings_page() {
    // Handle form submission
    if (isset($_POST['submit']) && check_admin_referer('staircase_settings', 'staircase_settings_nonce')) {
        // Save settings
        update_option('staircase_default_template', sanitize_text_field($_POST['default_template']));
        update_option('staircase_enable_breadcrumbs', isset($_POST['enable_breadcrumbs']));
        update_option('staircase_show_page_titles', isset($_POST['show_page_titles']));
        update_option('staircase_enable_ruplin_integration', isset($_POST['enable_ruplin_integration']));
        update_option('staircase_custom_css', wp_strip_all_tags($_POST['custom_css']));
        
        // Handle logo upload
        if (!empty($_POST['header_logo_url'])) {
            update_option('staircase_header_logo', esc_url_raw($_POST['header_logo_url']));
        } else {
            delete_option('staircase_header_logo');
        }
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    // Get current settings
    $default_template = get_option('staircase_default_template', 'hero-full');
    $enable_breadcrumbs = get_option('staircase_enable_breadcrumbs', false);
    $show_page_titles = get_option('staircase_show_page_titles', true);
    $enable_ruplin_integration = get_option('staircase_enable_ruplin_integration', true);
    $custom_css = get_option('staircase_custom_css', '');
    $header_logo = get_option('staircase_header_logo', '');
    ?>
    <div class="wrap">
        <h1>Staircase Theme Settings</h1>
        <p>Configure global settings for the Staircase theme.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('staircase_settings', 'staircase_settings_nonce'); ?>
            
            <div class="settings-sections">
                <div class="settings-section">
                    <h2>Header Logo</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Site Logo</th>
                            <td>
                                <div class="logo-upload-container">
                                    <input type="url" name="header_logo_url" id="header_logo_url" value="<?php echo esc_url($header_logo); ?>" class="regular-text" placeholder="Logo image URL">
                                    <button type="button" class="button" id="upload_logo_button">Upload Logo</button>
                                    <?php if ($header_logo): ?>
                                        <button type="button" class="button" id="remove_logo_button">Remove Logo</button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($header_logo): ?>
                                    <div style="margin-top: 10px;">
                                        <strong>Current Logo:</strong><br>
                                        <img src="<?php echo esc_url($header_logo); ?>" alt="Current Logo" style="max-width: 200px; max-height: 60px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                <?php endif; ?>
                                <p class="description">Upload or specify a URL for your site logo. This will appear in the header to the left of the navigation menu.</p>
                            </td>
                        </tr>
                    </table>
                    
                </div>
                
                <div class="settings-section">
                    <h2>Template Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Default Template</th>
                            <td>
                                <select name="default_template">
                                    <option value="hero-full" <?php selected($default_template, 'hero-full'); ?>>Full Hero Layout</option>
                                    <option value="hero-minimal" <?php selected($default_template, 'hero-minimal'); ?>>Minimal Hero Layout</option>
                                    <option value="homepage-cherry" <?php selected($default_template, 'homepage-cherry'); ?>>Homepage Cherry</option>
                                    <option value="homepage-apple" <?php selected($default_template, 'homepage-apple'); ?>>Homepage Apple</option>
                                    <option value="no-hero" <?php selected($default_template, 'no-hero'); ?>>Standard Layout</option>
                                    <option value="content-only" <?php selected($default_template, 'content-only'); ?>>Content Only</option>
                                    <option value="sections-builder" <?php selected($default_template, 'sections-builder'); ?>>Sections Builder</option>
                                </select>
                                <p class="description">Default template for new pages</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Display Options</th>
                            <td>
                                <fieldset>
                                    <label for="enable_breadcrumbs">
                                        <input name="enable_breadcrumbs" type="checkbox" id="enable_breadcrumbs" value="1" <?php checked($enable_breadcrumbs); ?>>
                                        Enable Breadcrumbs
                                    </label><br>
                                    <label for="show_page_titles">
                                        <input name="show_page_titles" type="checkbox" id="show_page_titles" value="1" <?php checked($show_page_titles); ?>>
                                        Show Page Titles (when no hero)
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="settings-section">
                    <h2>Integration Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Ruplin Plugin</th>
                            <td>
                                <label for="enable_ruplin_integration">
                                    <input name="enable_ruplin_integration" type="checkbox" id="enable_ruplin_integration" value="1" <?php checked($enable_ruplin_integration); ?>>
                                    Enable Ruplin Integration
                                </label>
                                <p class="description">Use Ruplin plugin data for page configuration</p>
                                <?php if (!class_exists('SnefuruPlugin')): ?>
                                    <p class="description" style="color: #d63638;">⚠️ Ruplin plugin is not currently active</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="settings-section">
                    <h2>Custom Styling</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Custom CSS</th>
                            <td>
                                <textarea name="custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
                                <p class="description">Add custom CSS that will be applied to all pages using Staircase theme</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <style>
    .settings-sections {
        max-width: 800px;
    }
    
    .settings-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .settings-section h2 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #23282d;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .logo-upload-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .logo-upload-container input[type="url"] {
        flex: 1;
    }
    </style>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        console.log('jQuery ready, checking for wp.media...');
        console.log('wp.media available:', typeof wp !== 'undefined' && typeof wp.media !== 'undefined');
        
        var mediaUploader;
        
        function initializeMediaUploader() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.error('WordPress media scripts not loaded');
                alert('Media uploader not available. Please refresh the page.');
                return false;
            }
            
            if (mediaUploader) {
                mediaUploader.open();
                return true;
            }
            
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Select Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                console.log('Selected attachment:', attachment);
                $('#header_logo_url').val(attachment.url);
                
                // Show immediate feedback
                if (attachment.url) {
                    var preview = '<div style="margin-top: 10px;"><strong>Selected Logo:</strong><br><img src="' + attachment.url + '" style="max-width: 200px; max-height: 60px; border: 1px solid #ddd; padding: 5px;"></div>';
                    $('.logo-upload-container').after(preview);
                }
            });
            
            mediaUploader.open();
            return true;
        }
        
        $('#upload_logo_button').click(function(e) {
            e.preventDefault();
            console.log('Upload button clicked');
            
            // Wait a moment for scripts to load if needed
            setTimeout(function() {
                initializeMediaUploader();
            }, 100);
        });
        
        $('#remove_logo_button').click(function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove the logo?')) {
                $('#header_logo_url').val('');
            }
        });
        
        // Wait for media scripts to load
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.log('Waiting for wp.media to load...');
            var checkMedia = setInterval(function() {
                if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
                    console.log('wp.media is now available');
                    clearInterval(checkMedia);
                }
            }, 500);
        }
    });
    </script>
    <?php
}

// Component Library page
function staircase_components_page() {
    ?>
    <div class="wrap">
        <h1>Component Library</h1>
        <p>Browse and manage reusable components for building pages.</p>
        
        <div class="components-section">
            <div class="components-categories">
                <button class="component-category active" data-category="all">All Components</button>
                <button class="component-category" data-category="hero">Hero Sections</button>
                <button class="component-category" data-category="content">Content Blocks</button>
                <button class="component-category" data-category="cta">Call to Action</button>
                <button class="component-category" data-category="layout">Layout Elements</button>
            </div>
            
            <div class="components-grid">
                <?php
                $components = array(
                    'hero-banner' => array(
                        'name' => 'Hero Banner',
                        'category' => 'hero',
                        'description' => 'Large banner with title, subtitle, and CTA button',
                        'preview' => 'hero-preview.png'
                    ),
                    'feature-grid' => array(
                        'name' => 'Feature Grid',
                        'category' => 'content',
                        'description' => '3-column grid showing key features or services',
                        'preview' => 'feature-grid-preview.png'
                    ),
                    'testimonial-slider' => array(
                        'name' => 'Testimonial Slider',
                        'category' => 'content',
                        'description' => 'Rotating testimonials with client photos',
                        'preview' => 'testimonials-preview.png'
                    ),
                    'cta-banner' => array(
                        'name' => 'CTA Banner',
                        'category' => 'cta',
                        'description' => 'Prominent call-to-action section with button',
                        'preview' => 'cta-preview.png'
                    ),
                    'contact-form' => array(
                        'name' => 'Contact Form',
                        'category' => 'content',
                        'description' => 'Standard contact form with validation',
                        'preview' => 'form-preview.png'
                    ),
                    'pricing-table' => array(
                        'name' => 'Pricing Table',
                        'category' => 'content',
                        'description' => 'Comparison table for pricing plans',
                        'preview' => 'pricing-preview.png'
                    )
                );
                
                foreach ($components as $key => $component) {
                    ?>
                    <div class="component-card" data-category="<?php echo $component['category']; ?>">
                        <div class="component-preview">
                            <div class="component-mockup">
                                <!-- Component preview mockup -->
                                <div class="mockup-placeholder"><?php echo substr($component['name'], 0, 1); ?></div>
                            </div>
                        </div>
                        <div class="component-info">
                            <h3><?php echo $component['name']; ?></h3>
                            <p><?php echo $component['description']; ?></p>
                            <div class="component-meta">
                                <span class="category-tag"><?php echo ucfirst($component['category']); ?></span>
                            </div>
                            <div class="component-actions">
                                <button class="button button-secondary">Preview</button>
                                <button class="button button-primary">Add to Page</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    
    <style>
    .components-section {
        margin-top: 20px;
    }
    
    .components-categories {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .component-category {
        background: #f1f1f1;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .component-category.active,
    .component-category:hover {
        background: #0073aa;
        color: white;
    }
    
    .components-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .component-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .component-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .component-preview {
        height: 150px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .component-mockup {
        width: 80px;
        height: 60px;
        background: #e0e0e0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .mockup-placeholder {
        font-size: 24px;
        font-weight: bold;
        color: #999;
    }
    
    .component-info {
        padding: 15px;
    }
    
    .component-info h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        color: #23282d;
    }
    
    .component-info p {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 13px;
        line-height: 1.4;
    }
    
    .component-meta {
        margin-bottom: 12px;
    }
    
    .category-tag {
        background: #e8f4f8;
        color: #0073aa;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    
    .component-actions {
        display: flex;
        gap: 8px;
    }
    
    .component-actions .button {
        flex: 1;
        text-align: center;
        font-size: 12px;
        padding: 6px 12px;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.component-category');
        const componentCards = document.querySelectorAll('.component-card');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update active button
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter components
                const category = this.dataset.category;
                componentCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
    <?php
}

// Zaramax Footer Management page
function zaramax_footer_management_page() {
    // Handle form submission
    if (isset($_POST['submit']) && check_admin_referer('zaramax_footer', 'zaramax_footer_nonce')) {
        // Save footer system choice
        update_option('zaramax_use_custom_footer', isset($_POST['use_custom_footer']));
        
        // TEMPORARY: Save footer blurb to WordPress options for testing
        // TODO: Remove this when proper database column is created
        if (isset($_POST['footer_blurb'])) {
            update_option('zaramax_temp_footer_blurb', sanitize_textarea_field($_POST['footer_blurb']));
        }
        
        // Save other footer settings
        update_option('zaramax_footer_box2_content', wp_kses_post($_POST['footer_box2_content']));
        update_option('zaramax_footer_box3_content', wp_kses_post($_POST['footer_box3_content']));
        update_option('zaramax_footer_map_heading', sanitize_text_field($_POST['footer_map_heading']));
        update_option('zaramax_footer_map_location', sanitize_text_field($_POST['footer_map_location']));
        update_option('zaramax_footer_disclaimer', wp_kses_post($_POST['footer_disclaimer']));
        update_option('zaramax_footer_legal_links', wp_kses_post($_POST['footer_legal_links']));
        
        echo '<div class="notice notice-success"><p>Footer settings saved successfully!</p></div>';
    }
    
    // Get current settings
    $use_custom_footer = get_option('zaramax_use_custom_footer', false);
    $footer_box2_content = get_option('zaramax_footer_box2_content', '');
    $footer_box3_content = get_option('zaramax_footer_box3_content', '');
    $footer_map_heading = get_option('zaramax_footer_map_heading', '');
    $footer_map_location = get_option('zaramax_footer_map_location', '');
    $footer_disclaimer = get_option('zaramax_footer_disclaimer', '');
    $footer_legal_links = get_option('zaramax_footer_legal_links', '');
    
    // TEMPORARY: Get footer blurb from WordPress options for testing
    // TODO: Remove this when proper database column is created
    $footer_blurb = get_option('zaramax_temp_footer_blurb', '');
    ?>
    <div class="wrap">
        <h1>Zaramax Footer Management</h1>
        <p>Configure your custom footer layout and content.</p>
        
        <!-- TEMPORARY: Testing notice -->
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong>⚠️ TEMPORARY TESTING MODE:</strong> Footer blurb is currently stored in WordPress options for testing. This will be moved to proper database storage later.</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('zaramax_footer', 'zaramax_footer_nonce'); ?>
            
            <div style="margin-bottom: 20px;">
                <?php submit_button(); ?>
            </div>
            
            <div class="footer-system-choice" style="background: #fff; border: 1px solid #ddd; padding: 20px; margin-bottom: 30px; border-radius: 8px;">
                <h2>Footer System Selection</h2>
                <fieldset>
                    <legend style="font-weight: bold; margin-bottom: 10px;">Choose which footer system to use:</legend>
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="radio" name="footer_system" value="zaramax" <?php checked($use_custom_footer, true); ?> onchange="toggleFooterSystem(true)">
                        Use Zaramax Footer System
                    </label>
                    <label style="display: block;">
                        <input type="radio" name="footer_system" value="default" <?php checked($use_custom_footer, false); ?> onchange="toggleFooterSystem(false)">
                        Use default/normal footer system
                    </label>
                    <input type="hidden" name="use_custom_footer" id="use_custom_footer" value="<?php echo $use_custom_footer ? '1' : '0'; ?>">
                </fieldset>
            </div>
            
            <div id="zaramax-footer-content" style="<?php echo $use_custom_footer ? '' : 'display: none;'; ?>">
                <div class="footer-boxes-section">
                    <h2>Footer Content Boxes</h2>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
                        
                        <!-- Box 1 -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #0073aa;">
                            <h3 style="margin-top: 0; color: #0073aa;">Box 1 - Logo & Contact</h3>
                            <p style="margin-bottom: 15px; font-size: 14px; color: #666;">
                                This box automatically displays:<br>
                                • Site logo<br>
                                • Footer blurb<br>
                                • Click-to-call phone button
                            </p>
                            
                            <label for="footer_blurb"><strong>Footer Blurb (1-2 sentences):</strong></label>
                            <textarea id="footer_blurb" name="footer_blurb" rows="3" style="width: 100%; margin-top: 5px;" placeholder="Enter your footer description..."><?php echo esc_textarea($footer_blurb); ?></textarea>
                            <small style="color: #666;">Line breaks will be preserved automatically</small>
                        </div>
                        
                        <!-- Box 2 -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #28a745;">
                            <h3 style="margin-top: 0; color: #28a745;">Box 2 - Custom HTML</h3>
                            <label for="footer_box2_content"><strong>Content:</strong></label>
                            <textarea id="footer_box2_content" name="footer_box2_content" rows="8" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter HTML content..."><?php echo esc_textarea($footer_box2_content); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                        
                        <!-- Box 3 -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #ffc107;">
                            <h3 style="margin-top: 0; color: #e0a800;">Box 3 - Custom HTML</h3>
                            <label for="footer_box3_content"><strong>Content:</strong></label>
                            <textarea id="footer_box3_content" name="footer_box3_content" rows="8" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter HTML content..."><?php echo esc_textarea($footer_box3_content); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                        
                        <!-- Box 4 -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;">
                            <h3 style="margin-top: 0; color: #dc3545;">Box 4 - Google Maps</h3>
                            
                            <label for="footer_map_heading"><strong>Heading:</strong></label>
                            <input type="text" id="footer_map_heading" name="footer_map_heading" value="<?php echo esc_attr($footer_map_heading); ?>" style="width: 100%; margin-bottom: 10px;" placeholder="e.g., Visit Our Office">
                            
                            <label for="footer_map_location"><strong>Location/Address:</strong></label>
                            <input type="text" id="footer_map_location" name="footer_map_location" value="<?php echo esc_attr($footer_map_location); ?>" style="width: 100%;" placeholder="e.g., 123 Main St, City, State">
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom-section">
                    <h2>Footer Bottom Section</h2>
                    <div style="max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 30px;">
                        
                        <!-- Disclaimer Area -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                            <h3 style="margin-top: 0;">Disclaimer Area</h3>
                            <label for="footer_disclaimer"><strong>Disclaimer Content (HTML allowed):</strong></label>
                            <textarea id="footer_disclaimer" name="footer_disclaimer" rows="6" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter disclaimer HTML..."><?php echo esc_textarea($footer_disclaimer); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                        
                        <!-- Footer Legal Links Area -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                            <h3 style="margin-top: 0;">Footer Legal Links Area</h3>
                            <label for="footer_legal_links"><strong>Legal Links Content (HTML allowed):</strong></label>
                            <textarea id="footer_legal_links" name="footer_legal_links" rows="6" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter legal links HTML..."><?php echo esc_textarea($footer_legal_links); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script type="text/javascript">
    function toggleFooterSystem(useZaramax) {
        document.getElementById('use_custom_footer').value = useZaramax ? '1' : '0';
        const content = document.getElementById('zaramax-footer-content');
        content.style.display = useZaramax ? 'block' : 'none';
    }
    
    // Initialize radio buttons
    jQuery(document).ready(function($) {
        $('input[name="footer_system"]').change(function() {
            const useZaramax = $(this).val() === 'zaramax';
            toggleFooterSystem(useZaramax);
        });
    });
    </script>
    
    <style>
    .footer-boxes-section h2 {
        margin-bottom: 20px;
        border-bottom: 2px solid #0073aa;
        padding-bottom: 10px;
    }
    
    .footer-bottom-section h2 {
        margin-bottom: 20px;
        border-bottom: 2px solid #28a745;
        padding-bottom: 10px;
    }
    
    @media (max-width: 1200px) {
        .footer-boxes-section > div {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 768px) {
        .footer-boxes-section > div {
            grid-template-columns: 1fr !important;
        }
    }
    </style>
    <?php
}

// TEMPORARY: Commented out database column creation for testing
// TODO: Uncomment this when ready to use proper database storage
/*
// Add footer_blurb column to zen_sitespren table if it doesn't exist
function zaramax_add_footer_blurb_column() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'zen_sitespren';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        return;
    }
    
    // Check if column exists
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'footer_blurb'");
    
    if (empty($column_exists)) {
        // Add the column
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN footer_blurb TEXT");
    }
}
add_action('admin_init', 'zaramax_add_footer_blurb_column');
*/

// Render Zaramax custom footer
function zaramax_render_custom_footer() {
    // Get all footer settings
    $footer_box2_content = get_option('zaramax_footer_box2_content', '');
    $footer_box3_content = get_option('zaramax_footer_box3_content', '');
    $footer_map_heading = get_option('zaramax_footer_map_heading', '');
    $footer_map_location = get_option('zaramax_footer_map_location', '');
    $footer_disclaimer = get_option('zaramax_footer_disclaimer', '');
    $footer_legal_links = get_option('zaramax_footer_legal_links', '');
    
    // TEMPORARY: Get footer blurb from WordPress options for testing
    // TODO: Remove this when proper database column is created  
    $footer_blurb = get_option('zaramax_temp_footer_blurb', '');
    
    // Get logo and phone
    $site_logo = get_option('staircase_header_logo', '');
    $phone_raw = staircase_get_header_phone();
    $phone_formatted = staircase_get_formatted_phone();
    ?>
    <footer class="zaramax-footer">
        <!-- Main Footer Boxes -->
        <div class="zaramax-footer-main">
            <div class="container">
                <div class="footer-boxes-grid">
                    
                    <!-- Box 1: Logo & Contact -->
                    <div class="footer-box footer-box-1">
                        <?php if (!empty($site_logo)): ?>
                            <div class="footer-logo">
                                <img src="<?php echo esc_url($site_logo); ?>" alt="<?php bloginfo('name'); ?>" class="footer-logo-img">
                            </div>
                        <?php else: ?>
                            <div class="footer-site-title">
                                <h3><?php bloginfo('name'); ?></h3>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($footer_blurb)): ?>
                            <div class="footer-blurb">
                                <p><?php echo nl2br(esc_html($footer_blurb)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($phone_raw)): ?>
                            <div class="footer-phone">
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone_raw)); ?>" class="footer-phone-button">
                                    <svg class="phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                    <span><?php echo esc_html($phone_formatted); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Box 2: Custom HTML -->
                    <div class="footer-box footer-box-2">
                        <?php echo wpautop(wp_kses_post($footer_box2_content)); ?>
                    </div>
                    
                    <!-- Box 3: Custom HTML -->
                    <div class="footer-box footer-box-3">
                        <?php echo wpautop(wp_kses_post($footer_box3_content)); ?>
                    </div>
                    
                    <!-- Box 4: Google Maps -->
                    <div class="footer-box footer-box-4">
                        <?php if (!empty($footer_map_heading)): ?>
                            <h3 class="map-heading"><?php echo esc_html($footer_map_heading); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (!empty($footer_map_location)): ?>
                            <div class="footer-map">
                                <iframe 
                                    src="https://www.google.com/maps?q=<?php echo urlencode($footer_map_location); ?>&output=embed"
                                    width="100%" 
                                    height="200" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy">
                                </iframe>
                            </div>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic;">Map location not set</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom Section -->
        <div class="zaramax-footer-bottom">
            <div class="footer-bottom-container">
                <div class="footer-bottom-grid">
                    <!-- Disclaimer Area -->
                    <div class="footer-disclaimer">
                        <?php echo wpautop(wp_kses_post($footer_disclaimer)); ?>
                    </div>
                    
                    <!-- Legal Links Area -->
                    <div class="footer-legal-links">
                        <?php 
                        // Debug: Show legal links variable status
                        echo '<!-- Legal Links Debug: footer_legal_links = "' . esc_attr($footer_legal_links) . '", length = ' . strlen($footer_legal_links) . ' -->';
                        
                        if (!empty($footer_legal_links)) {
                            // Preserve multiple spaces by replacing them with non-breaking spaces
                            $legal_links_html = wp_kses_post($footer_legal_links);
                            // Simple replacement of multiple spaces with non-breaking spaces
                            $legal_links_html = str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', $legal_links_html); // 4 spaces
                            $legal_links_html = str_replace('   ', '&nbsp;&nbsp;&nbsp;', $legal_links_html); // 3 spaces  
                            $legal_links_html = str_replace('  ', '&nbsp;&nbsp;', $legal_links_html); // 2 spaces
                            echo wpautop($legal_links_html); 
                        } else {
                            echo '<!-- No legal links content -->';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php
}

// Our Services Section - Paragon Cards
function staircase_our_services_section() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get OSB configuration from current page's pylon data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT osb_box_title, osb_services_per_row, osb_max_services_display FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Debug: Check table names and existence
    echo "<!-- Our Services Debug: Using pylons table = '{$pylons_table}' -->\n";
    echo "<!-- Our Services Debug: Using posts table = '{$wpdb->posts}' -->\n";
    
    // Get configuration values with defaults
    $services_title = !empty($pylon_data['osb_box_title']) ? $pylon_data['osb_box_title'] : 'Our Services';
    $services_per_row = !empty($pylon_data['osb_services_per_row']) ? (int)$pylon_data['osb_services_per_row'] : 4;
    $max_services = !empty($pylon_data['osb_max_services_display']) ? (int)$pylon_data['osb_max_services_display'] : 0;
    
    // Debug: First check how many service pages exist without description requirement
    $debug_services = $wpdb->get_results("
        SELECT 
            p.ID, 
            p.post_title, 
            py.moniker, 
            py.paragon_description,
            py.pylon_archetype
        FROM {$wpdb->posts} p 
        INNER JOIN {$pylons_table} py ON p.ID = py.rel_wp_post_id 
        WHERE py.pylon_archetype = 'servicepage' 
        AND p.post_status = 'publish'
    ");
    
    // Debug output
    echo "<!-- Our Services Debug: Found " . count($debug_services) . " total service pages -->\n";
    foreach ($debug_services as $service) {
        echo "<!-- Service: ID=" . $service->ID . ", title='" . esc_attr($service->post_title) . "', moniker='" . esc_attr($service->moniker) . "', description='" . esc_attr($service->paragon_description) . "' -->\n";
    }
    
    // Get service pages from pylons table with optional limit
    // Order by creation date (primary) and alphabetical by moniker/post_title (secondary)
    $limit_clause = $max_services > 0 ? "LIMIT " . $max_services : "";
    // Debug: Use EXACT same query as working dioptra (without post_status filter first)
    $services_test = $wpdb->get_results("
        SELECT py.*, p.post_title, p.post_status 
        FROM {$pylons_table} py 
        LEFT JOIN {$wpdb->posts} p ON p.ID = py.rel_wp_post_id 
        WHERE py.pylon_archetype = 'servicepage'
    ", ARRAY_A);
    
    echo "<!-- Our Services Debug: Exact dioptra query found " . count($services_test) . " records -->\n";
    
    // Now filter for published only
    $published_services = array_filter($services_test, function($row) {
        return $row['post_status'] === 'publish';
    });
    
    echo "<!-- Our Services Debug: Published services after filter = " . count($published_services) . " -->\n";
    
    // Convert to proper format for rendering
    $services = array();
    foreach ($published_services as $row) {
        $service = (object) array(
            'ID' => $row['rel_wp_post_id'],
            'post_title' => $row['post_title'],
            'moniker' => $row['moniker'],
            'paragon_description' => $row['paragon_description'],
            'paragon_featured_image_id' => $row['paragon_featured_image_id'],
            'created_at' => $row['created_at']
        );
        $services[] = $service;
    }
    
    // Apply limit
    if ($max_services > 0 && count($services) > $max_services) {
        $services = array_slice($services, 0, $max_services);
    }
    
    // Debug: Show first few service results if any found
    if (!empty($services)) {
        $sample = array_slice($services, 0, 3);
        foreach ($sample as $i => $svc) {
            echo "<!-- Our Services Debug: Service $i = ID:{$svc->ID}, Title:'" . esc_attr($svc->post_title) . "', Status: (checking...) -->\n";
        }
    }
    
    echo "<!-- Our Services Debug: After filtering, showing " . count($services) . " service pages -->\n";
    echo "<!-- Our Services Debug: Max services limit = $max_services -->\n";
    echo "<!-- Our Services Debug: Limit clause = '$limit_clause' -->\n";
    
    // Debug: Show the actual SQL query being executed  
    $debug_query = "SELECT p.ID, p.post_title, py.moniker, py.paragon_description, py.paragon_featured_image_id, py.created_at 
                   FROM {$pylons_table} py 
                   LEFT JOIN {$wpdb->posts} p ON p.ID = py.rel_wp_post_id 
                   WHERE py.pylon_archetype = 'servicepage' 
                   AND p.post_status = 'publish' 
                   ORDER BY py.created_at ASC, 
                            CASE WHEN py.moniker IS NULL OR py.moniker = '' THEN p.post_title ELSE py.moniker END ASC 
                   {$limit_clause}";
    echo "<!-- Our Services Debug: SQL Query = " . esc_html(str_replace(["\n", "\t", "  "], [" ", " ", " "], $debug_query)) . " -->\n";
    
    // Debug: Test if the issue is with the INNER JOIN
    $posts_check = $wpdb->get_results("SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_status = 'publish' AND ID IN (1705, 1706, 1709, 1658, 1739, 1740, 1753)");
    echo "<!-- Our Services Debug: Published posts check found " . count($posts_check) . " posts -->\n";
    
    $pylons_check = $wpdb->get_results("SELECT rel_wp_post_id, pylon_archetype FROM {$pylons_table} WHERE pylon_archetype = 'servicepage' AND rel_wp_post_id IN (1705, 1706, 1709, 1658, 1739, 1740, 1753)");
    echo "<!-- Our Services Debug: Pylons servicepage check found " . count($pylons_check) . " records -->\n";
    
    // Don't render section if no services found
    if (empty($services)) {
        echo "<!-- Our Services: No services found after filtering -->\n";
        return;
    }
    
    ?>
    <!-- Dynamic OSB Grid Style -->
    <style>
        .paragon-cards-grid {
            grid-template-columns: repeat(<?php echo esc_attr($services_per_row); ?>, 1fr) !important;
        }
        
        /* Responsive overrides for smaller screens */
        @media (max-width: 1200px) {
            .paragon-cards-grid {
                grid-template-columns: repeat(<?php echo min(3, $services_per_row); ?>, 1fr) !important;
            }
        }
        
        @media (max-width: 992px) {
            .paragon-cards-grid {
                grid-template-columns: repeat(<?php echo min(2, $services_per_row); ?>, 1fr) !important;
            }
        }
        
        @media (max-width: 768px) {
            .paragon-cards-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    
    <section class="paragon-services-section">
        <div class="container">
            <h2 class="paragon-section-title"><?php echo esc_html($services_title); ?></h2>
            <div class="paragon-cards-grid">
                <?php foreach ($services as $service): ?>
                    <?php
                    $service_url = get_permalink($service->ID);
                    $service_title = !empty($service->moniker) ? $service->moniker : $service->post_title;
                    
                    // Get featured image
                    $image_html = '';
                    if (!empty($service->paragon_featured_image_id)) {
                        $image_src = wp_get_attachment_image_src($service->paragon_featured_image_id, 'medium');
                        if ($image_src) {
                            $image_html = sprintf(
                                '<img src="%s" alt="%s" />',
                                esc_url($image_src[0]),
                                esc_attr($service_title)
                            );
                        }
                    }
                    
                    // Fallback to featured image if no paragon image
                    if (empty($image_html)) {
                        $featured_image_id = get_post_thumbnail_id($service->ID);
                        if ($featured_image_id) {
                            $image_html = get_the_post_thumbnail($service->ID, 'medium', array(
                                'alt' => esc_attr($service_title)
                            ));
                        }
                    }
                    
                    // Default placeholder if no images found
                    if (empty($image_html)) {
                        $image_html = '<div style="width:100%;height:180px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:14px;">No Image Available</div>';
                    }
                    ?>
                    
                    <a href="<?php echo esc_url($service_url); ?>" class="paragon-card">
                        <div class="paragon-card-image">
                            <?php echo $image_html; ?>
                        </div>
                        <div class="paragon-card-content">
                            <h3 class="paragon-card-title"><?php echo esc_html($service_title); ?></h3>
                            <p class="paragon-card-description"><?php echo esc_html($service->paragon_description); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}