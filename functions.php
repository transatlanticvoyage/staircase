<?php
/**
 * Staircase Theme Functions
 * 
 * @package Staircase
 */
// Test comment for VSCode trigger - functions.php

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
        'height'      => 86,
        'width'       => 251,
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
    
    // Enqueue Dashicons for frontend use (for service icons)
    wp_enqueue_style('dashicons');
    
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
    $template = staircase_get_current_template();
    
    switch ($template) {
        case 'cherry':
        case 'homepage-cherry': // Legacy compatibility
            staircase_render_batman_hero_box();
            break;
        default:
            // Default hero for content-only and other templates
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
            break;
    }
}

// Cherry hero section
function staircase_cherry_hero() {
    global $wpdb;
    
    // Check if this is the blog page
    if (is_home() && !is_front_page()) {
        $blog_page_id = get_option('page_for_posts');
        $post_id = $blog_page_id ?: get_the_ID();
        $cherry_heading = $blog_page_id ? get_the_title($blog_page_id) : 'Blog';
    } else {
        $post_id = get_the_ID();
        $cherry_heading = get_the_title(); // wp_posts.post_title
    }
    
    // Debug: Add HTML comment to verify function is being called
    echo "<!-- Cherry Hero: Post ID = $post_id -->\n";
    
    // Get data from wp_posts and wp_pylons tables
    
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
    
    // Always show buttons with default text
    $cherry_button_left_text = get_post_meta($post_id, 'cherry_button_left_text', true) ?: 'Get Your Estimate';
    $cherry_button_left_url = get_post_meta($post_id, 'cherry_button_left_url', true) ?: '';
    $cherry_button_right_text = get_post_meta($post_id, 'cherry_button_right_text', true) ?: 'Call Us Now';
    $cherry_button_right_url = get_post_meta($post_id, 'cherry_button_right_url', true) ?: '';
    
    // Get phone number from wp_zen_sitespren.driggs_phone_1
    global $wpdb;
    $cherry_phone_number_raw = $wpdb->get_var("SELECT driggs_phone_1 FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $cherry_phone_number_formatted = $cherry_phone_number_raw ? staircase_format_phone_number($cherry_phone_number_raw) : '';
    
    // Set right button as call link if no custom URL is set
    if (empty($cherry_button_right_url) && !empty($cherry_phone_number_raw)) {
        $cherry_button_right_url = 'tel:' . preg_replace('/[^0-9]/', '', $cherry_phone_number_raw);
    }
    
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
    
    // Get paragon featured image for hero background
    $paragon_image_id = '';
    $paragon_image_url = '';
    if ($pylon_data && !empty($pylon_data['paragon_featured_image_id'])) {
        $paragon_image_id = $pylon_data['paragon_featured_image_id'];
        $paragon_image_url = wp_get_attachment_image_url($paragon_image_id, 'full');
        echo "<!-- Background Image: $paragon_image_url (ID: $paragon_image_id) -->\n";
    } else {
        echo "<!-- Background Image: None set -->\n";
    }
    
    // Default values
    if (empty($cherry_subheading)) {
        $cherry_subheading = get_bloginfo('description');
    }
    ?>
    <section class="hero-section cherry-hero">
        <div class="container">
            <div class="cherry-hero-content">
                <h1 class="cherry-heading"><?php echo esc_html($cherry_heading); ?></h1>
                <?php if ($cherry_subheading): ?>
                    <p class="cherry-subheading"><?php echo esc_html($cherry_subheading); ?></p>
                <?php endif; ?>
                
                <div class="cherry-buttons-container">
                    <?php if ($cherry_button_left_url): ?>
                        <a href="<?php echo esc_url($cherry_button_left_url); ?>" class="batman-hero-button batman-hero-button-left">
                            <?php echo esc_html($cherry_button_left_text); ?>
                        </a>
                    <?php else: ?>
                        <span class="batman-hero-button batman-hero-button-left batman-hero-button-disabled">
                            <?php echo esc_html($cherry_button_left_text); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($cherry_button_right_url): ?>
                        <a href="<?php echo esc_url($cherry_button_right_url); ?>" class="batman-hero-button batman-hero-button-right">
                            <?php echo esc_html($cherry_button_right_text); ?>
                        </a>
                    <?php else: ?>
                        <span class="batman-hero-button batman-hero-button-right batman-hero-button-disabled">
                            <?php echo esc_html($cherry_button_right_text); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($cherry_phone_number_raw): ?>
                    <div class="cherry-phone-container" style="text-align: center; margin-top: 15px;">
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
    .cherry-hero {
        <?php if (!empty($paragon_image_url)): ?>
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo esc_url($paragon_image_url); ?>');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        <?php else: ?>
        background: #53565b;
        <?php endif; ?>
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
        background: #3f72d7;
        color: white;
        border: 2px solid #3f72d7;
    }
    
    .cherry-button-left:hover {
        background: #2d5cbf;
        color: white;
        border-color: #2d5cbf;
    }
    
    .cherry-button-right {
        background: #3f72d7;
        color: white;
        border: 2px solid #3f72d7;
    }
    
    .cherry-button-right:hover {
        background: #2d5cbf;
        color: white;
        border-color: #2d5cbf;
    }
    
    .batman-hero-button-disabled {
        cursor: default;
        pointer-events: none;
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
        .cherry-hero {
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

// Bilberry Template - Bare bones content display
function staircase_bilberry_template() {
    ?>
    <article class="bilberry-article">
        <header class="bilberry-header">
            <h1 class="bilberry-title"><?php the_title(); ?></h1>
        </header>
        
        <div class="bilberry-content">
            <?php the_content(); ?>
        </div>
    </article>
    
    <style>
    .bilberry-article {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    
    .bilberry-header {
        margin-bottom: 2rem;
    }
    
    .bilberry-title {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 0;
        color: #222;
        line-height: 1.2;
    }
    
    .bilberry-content {
        font-size: 1.1rem;
        color: #444;
    }
    
    .bilberry-content p {
        margin-bottom: 1.5rem;
    }
    
    .bilberry-content h2, .bilberry-content h3, .bilberry-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #222;
    }
    
    .bilberry-content ul, .bilberry-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    @media (max-width: 768px) {
        .bilberry-article {
            padding: 1rem;
        }
        
        .bilberry-title {
            font-size: 2rem;
        }
    }
    </style>
    <?php
}

/**
 * Centralized Template Rendering System
 * Single source of truth for all template rendering
 */
function staircase_render_template() {
    $current_template = staircase_get_current_template();
    
    // Route to appropriate template based on current selection
    switch($current_template) {
        case 'bilberry':
            staircase_render_bilberry_template();
            break;
            
        case 'sarsaparilla':
            staircase_render_sarsaparilla_template();
            break;
            
        case 'gooseberry':
            staircase_render_gooseberry_template();
            break;
            
        case 'vibrantberry':
            staircase_render_vibrantberry_template();
            break;
            
        case 'cherry':
        case 'homepage-cherry':
            staircase_render_cherry_full_template();
            break;
            
        case 'content-only':
        default:
            staircase_render_default_template();
            break;
    }
}

/**
 * Render Bilberry Template
 */
function staircase_render_bilberry_template() {
    staircase_bilberry_template();
}

/**
 * Render Sarsaparilla Template
 * TODO: Implement sarsaparilla template
 */
function staircase_render_sarsaparilla_template() {
    // For now, use default template
    staircase_render_default_template();
}

/**
 * Render Gooseberry Template
 * TODO: Implement gooseberry template
 */
function staircase_render_gooseberry_template() {
    // For now, use default template
    staircase_render_default_template();
}

/**
 * Render Vibrantberry Template (Custom HTML)
 */
function staircase_render_vibrantberry_template() {
    global $post;
    
    // Get the custom HTML content from post meta
    $custom_html = get_post_meta($post->ID, 'vibrantberry_content_ocean_1', true);
    
    // If no custom HTML is set, show a placeholder message
    if (empty($custom_html)) {
        $custom_html = '<div style="padding: 40px; text-align: center; background: #f8f9fa; margin: 20px; border-radius: 8px;">
            <h2>Vibrantberry Template</h2>
            <p>No custom HTML content has been added yet. Edit this page to add your custom HTML content.</p>
        </div>';
    }
    
    ?>
    <main class="site-content vibrantberry-template">
        <?php echo $custom_html; ?>
    </main>
    <style>
    .vibrantberry-template {
        width: 100%;
        min-height: 400px;
    }
    </style>
    <?php
}

/**
 * Render Plain Post Content (containerized)
 */
function staircase_render_plain_post_content() {
    ?>
    <main class="site-content">
        <div class="container">
            <?php staircase_render_default_template(); ?>
        </div>
    </main>
    <?php
}

/**
 * Render Cherry Full Template
 */
function staircase_render_cherry_full_template() {
    // Cherry template includes batman hero box first
    staircase_render_batman_hero_box();
    
    // Add blog post meta information for posts only
    staircase_render_derek_blog_post_meta_box();
    
    // Cherry template includes chen cards before content
    staircase_render_chen_cards_box();
    
    // Render main content in container
    staircase_render_plain_post_content();
    
    // Cherry template includes OSB box
    staircase_render_osb_box();
    
    // Cherry template includes all the boxes at the end
    staircase_render_serena_faq_box();
    staircase_render_nile_map_box();
    staircase_render_kristina_cta_box();
    staircase_render_victoria_blog_box();
}

/**
 * Render Default Template (content-only and fallback)
 */
function staircase_render_default_template() {
    $post_type = get_post_type();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php 
        // Show title if hero is not displayed
        if (!staircase_should_show_hero()): 
        ?>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                
                <?php 
                // Add post meta for single posts
                if ($post_type === 'post' && is_single()): 
                ?>
                    <div class="entry-meta">
                        <span class="posted-on">
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </span>
                        <span class="byline">
                            by <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                <?php the_author(); ?>
                            </a>
                        </span>
                        <?php if (get_comments_number()): ?>
                            <span class="comments-link">
                                <?php comments_popup_link('No comments', '1 comment', '% comments'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>
        <?php endif; ?>
        
        <?php if (has_post_thumbnail() && !is_front_page()): ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
        
        <div class="entry-content">
            <?php
            the_content();
            
            wp_link_pages(array(
                'before' => '<div class="page-links">Pages: ',
                'after'  => '</div>',
            ));
            ?>
        </div>
        
        <?php if (get_edit_post_link()): ?>
            <footer class="entry-footer">
                <?php
                edit_post_link(
                    sprintf(
                        wp_kses(
                            __('Edit <span class="screen-reader-text">%s</span>', 'staircase'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        wp_kses_post(get_the_title())
                    ),
                    '<span class="edit-link">',
                    '</span>'
                );
                ?>
            </footer>
        <?php endif; ?>
    </article>
    
    <?php
    // Add comments for single posts
    if (is_singular() && (comments_open() || get_comments_number())):
        comments_template();
    endif;
}

// Get the template for current page
function staircase_get_current_template() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get template from wp_pylons table only
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
    
    // If no pylon template found, return cherry as default
    return 'cherry';
}

// Custom function to check if hero should be displayed based on template
function staircase_should_show_hero() {
    $template = staircase_get_current_template();
    
    // Hero templates (includes legacy compatibility)
    return in_array($template, array('hero-full', 'hero-minimal', 'cherry', 'homepage-cherry'));
}

// Get hero type for current page
function staircase_get_hero_type() {
    $template = staircase_get_current_template();
    
    if ($template === 'hero-full') {
        return 'full';
    } elseif ($template === 'hero-minimal') {
        return 'minimal';
    } elseif ($template === 'cherry' || $template === 'homepage-cherry') {
        return 'cherry';
    }
    
    return false;
}

// Set content width
function staircase_content_width() {
    $GLOBALS['content_width'] = apply_filters('staircase_content_width', 1200);
}
add_action('after_setup_theme', 'staircase_content_width', 0);

// Add OSB Box Paragon styles to wp_head
function staircase_osb_box_paragon_styles() {
    ?>
    <style>
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
add_action('wp_head', 'staircase_osb_box_paragon_styles');

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
        'cherry' => 'Cherry (default)', 
        'content-only' => 'Content Only',
        'bilberry' => 'bilberry (bare bones)',
        'sarsaparilla' => 'sarsaparilla',
        'gooseberry' => 'gooseberry',
        'vibrantberry' => 'Vibrantberry (Custom HTML)'
    );
}

// Normalize template name from user input to match available templates
function staircase_normalize_template_name($user_input) {
    if (empty($user_input)) {
        return 'cherry';
    }
    
    // Legacy compatibility mapping
    $legacy_mappings = [
        'homepage-cherry' => 'cherry',
        'homepagecherry' => 'cherry', 
        'Homepage Cherry' => 'cherry',
        'Homepage Cherry (default)' => 'cherry'
    ];
    
    // Check for direct legacy match first
    if (isset($legacy_mappings[$user_input])) {
        return $legacy_mappings[$user_input];
    }
    
    // Get available templates
    $templates = staircase_get_page_templates();
    
    // Normalize user input: lowercase, remove spaces/hyphens/underscores, trim
    $normalized_input = strtolower(trim(str_replace([' ', '-', '_'], '', $user_input)));
    
    // Check for matches treating spaces, hyphens, and underscores as equivalent
    foreach ($templates as $template_key => $template_label) {
        $normalized_key = strtolower(str_replace([' ', '-', '_'], '', $template_key));
        $normalized_label = strtolower(str_replace([' ', '-', '_'], '', $template_label));
        
        // Match against template key or label
        if ($normalized_input === $normalized_key || $normalized_input === $normalized_label) {
            return $template_key;
        }
    }
    
    // If no match found, return cherry as default
    return 'cherry';
}

// Page options meta box callback
function staircase_page_options_meta_box_callback($post) {
    global $wpdb;
    wp_nonce_field('staircase_page_options_meta_box', 'staircase_page_options_meta_box_nonce');
    
    $selected_template = get_post_meta($post->ID, 'staircase_page_template', true);
    $templates = staircase_get_page_templates();
    
    // Get current value from wp_pylons table
    $pylon_template = $wpdb->get_var($wpdb->prepare(
        "SELECT staircase_page_template_desired 
         FROM {$wpdb->prefix}pylons 
         WHERE rel_wp_post_id = %d", 
        $post->ID
    ));
    
    // Get default template from theme settings
    $default_template = get_option('staircase_default_template', 'hero-full');
    ?>
    <p>
        <label for="staircase_page_template"><strong>page-template-dropdown:</strong></label><br>
        <select id="staircase_page_template" name="staircase_page_template" style="width: 100%; margin-top: 5px;">
            <?php foreach ($templates as $value => $label): ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_template, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    
    <p style="margin-top: 15px;">
        <label for="staircase_pylon_raw_template" style="display: block; word-wrap: break-word; overflow-wrap: break-word;"><strong>Raw value of db column:<br>wp_pylons.staircase_page_template_desired</strong></label><br>
        <input type="text" id="staircase_pylon_raw_template" name="staircase_pylon_raw_template" 
               value="<?php echo esc_attr($pylon_template ?: ''); ?>" 
               style="width: 100%; margin-top: 5px; font-family: monospace; background-color: #f9f9f9;" 
               placeholder="Select from dropdown to populate">
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
            • <strong>Cherry:</strong> Hero with buttons & phone<br>
            • <strong>Standard:</strong> Traditional page layout<br>
            • <strong>Content Only:</strong> Just page content<br>
            • <strong>Sections Builder:</strong> Custom sections
        </p>
    </div>
    
    <?php if ($selected_template === 'cherry' || $selected_template === 'homepage-cherry'): ?>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #0073aa;">
            <h4 style="margin: 0 0 10px 0; color: #0073aa;">Cherry Options</h4>
            
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
            
            <p style="font-size: 11px; color: #666; margin-top: 10px;">
                <strong>Note:</strong> Hero buttons are automatically configured with default text and phone number from the database.
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
        
    <?php endif; ?>
    
    <?php if ($selected_template === 'vibrantberry'): ?>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #6f42c1;">
            <h4 style="margin: 0 0 15px 0; color: #000; font-weight: bold; font-size: 16px;">vibrantberry_content_ocean_1</h4>
            
            <div style="margin-bottom: 15px;">
                <textarea id="vibrantberry_content_ocean_1" name="vibrantberry_content_ocean_1" 
                          style="width: 100%; height: 150px; font-family: monospace; background-color: #2d3748; color: #e2e8f0; padding: 10px; border: 1px solid #4a5568; border-radius: 4px;"
                          placeholder="Enter your custom HTML code here..."><?php echo esc_textarea(get_post_meta($post->ID, 'vibrantberry_content_ocean_1', true)); ?></textarea>
            </div>
            
            <p style="font-size: 12px; color: #666; margin-top: 10px;">
                <strong>Note:</strong> This HTML will replace the entire content area between the header and footer. 
                Commonly used for rendering "vibe coded" pages from external tools.
            </p>
        </div>
    <?php endif; ?>
    
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('staircase_page_template');
        const pylonRawInput = document.getElementById('staircase_pylon_raw_template');
        
        // Template value mappings to raw format
        const templateMappings = {
            'homepage-cherry': 'cherry',
            'content-only': 'content_only'
        };
        
        function updateRawValue() {
            const selectedValue = templateSelect.value;
            const rawValue = templateMappings[selectedValue] || selectedValue;
            pylonRawInput.value = rawValue;
        }
        
        templateSelect.addEventListener('change', updateRawValue);
        updateRawValue(); // Set initial value
    });
    </script>
    
    <?php
}

// Save page options meta box data
function staircase_save_page_options_meta($post_id) {
    // DEBUG: Log function entry
    error_log("===== STAIRCASE SAVE META ENTERED =====");
    error_log("Post ID: " . $post_id);
    error_log("POST data keys: " . implode(', ', array_keys($_POST)));
    
    // Check nonce
    if (!isset($_POST['staircase_page_options_meta_box_nonce'])) {
        error_log("STAIRCASE SAVE: No nonce found - exiting");
        return;
    }
    
    if (!wp_verify_nonce($_POST['staircase_page_options_meta_box_nonce'], 'staircase_page_options_meta_box')) {
        error_log("STAIRCASE SAVE: Nonce verification failed - exiting");
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        error_log("STAIRCASE SAVE: Autosave detected - exiting");
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        error_log("STAIRCASE SAVE: No edit permissions - exiting");
        return;
    }
    
    // Save template selection to wp_pylons table
    if (isset($_POST['staircase_pylon_raw_template'])) {
        global $wpdb;
        $raw_template = sanitize_text_field($_POST['staircase_pylon_raw_template']);
        
        error_log("STAIRCASE SAVE: Raw template value: '" . $raw_template . "'");
        error_log("STAIRCASE SAVE: Raw template empty?: " . (empty($raw_template) ? 'YES' : 'NO'));
        
        // Check if pylon record exists for this post
        $pylon_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT pylon_id FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        error_log("STAIRCASE SAVE: Pylon exists? " . ($pylon_exists ? "YES (ID: $pylon_exists)" : "NO"));
        
        if ($pylon_exists) {
            // Get current value before update
            $current_value = $wpdb->get_var($wpdb->prepare(
                "SELECT staircase_page_template_desired FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
                $post_id
            ));
            error_log("STAIRCASE SAVE: Current DB value before update: '" . $current_value . "'");
            
            // Update existing record
            $result = $wpdb->update(
                $wpdb->prefix . 'pylons',
                array('staircase_page_template_desired' => $raw_template),
                array('rel_wp_post_id' => $post_id),
                array('%s'),
                array('%d')
            );
            
            error_log("STAIRCASE SAVE: Update result: " . var_export($result, true));
            error_log("STAIRCASE SAVE: Last DB error: " . $wpdb->last_error);
            
            // Check value after update
            $new_value = $wpdb->get_var($wpdb->prepare(
                "SELECT staircase_page_template_desired FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
                $post_id
            ));
            error_log("STAIRCASE SAVE: New DB value after update: '" . $new_value . "'");
            error_log("STAIRCASE SAVE: Update successful? " . ($new_value === $raw_template ? "YES" : "NO - values don't match!"));
        } else {
            error_log("STAIRCASE SAVE: Creating new pylon record");
            // Create new pylon record
            $result = $wpdb->insert(
                $wpdb->prefix . 'pylons',
                array(
                    'rel_wp_post_id' => $post_id,
                    'staircase_page_template_desired' => $raw_template,
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%s', '%s')
            );
            
            error_log("STAIRCASE SAVE: Insert result: " . var_export($result, true));
            error_log("STAIRCASE SAVE: Last DB error: " . $wpdb->last_error);
            error_log("STAIRCASE SAVE: Last insert ID: " . $wpdb->insert_id);
        }
    }
    
    // Save Cherry fields
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
    
    // Save Vibrantberry custom HTML field
    if (isset($_POST['vibrantberry_content_ocean_1'])) {
        // Use wp_kses_post to allow safe HTML while stripping dangerous scripts
        $allowed_html = wp_kses_allowed_html('post');
        // Allow more HTML tags and attributes that might be used in custom designs
        $allowed_html = array_merge($allowed_html, array(
            'div' => array('class' => array(), 'id' => array(), 'style' => array()),
            'span' => array('class' => array(), 'id' => array(), 'style' => array()),
            'section' => array('class' => array(), 'id' => array(), 'style' => array()),
            'header' => array('class' => array(), 'id' => array(), 'style' => array()),
            'footer' => array('class' => array(), 'id' => array(), 'style' => array()),
            'main' => array('class' => array(), 'id' => array(), 'style' => array()),
            'article' => array('class' => array(), 'id' => array(), 'style' => array()),
            'aside' => array('class' => array(), 'id' => array(), 'style' => array()),
            'nav' => array('class' => array(), 'id' => array(), 'style' => array()),
            'style' => array(),
            'script' => array('type' => array(), 'src' => array())
        ));
        
        // For vibrantberry, we'll be more permissive and just strip script tags for basic security
        $custom_html = $_POST['vibrantberry_content_ocean_1'];
        // Remove script tags but allow most other HTML
        $custom_html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $custom_html);
        
        update_post_meta($post_id, 'vibrantberry_content_ocean_1', $custom_html);
        
        error_log("STAIRCASE SAVE: Saved vibrantberry_content_ocean_1 for post {$post_id}");
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
                        'name' => 'Cherry',
                        'description' => 'Centered hero with heading, subheading, dual buttons, and phone number',
                        'features' => array('Centered Hero', 'Two Buttons', 'Phone Number', 'Page Content')
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
                                    <option value="homepage-cherry" <?php selected($default_template, 'homepage-cherry'); ?>>Cherry</option>
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
            // Use wp_unslash to remove magic quotes, then trim whitespace
            // This preserves shortcodes without adding unwanted backslashes
            $footer_blurb = trim(wp_unslash($_POST['footer_blurb']));
            update_option('zaramax_temp_footer_blurb', $footer_blurb);
        }
        
        // Save other footer settings
        // Use wp_unslash to preserve shortcodes without adding backslashes
        update_option('zaramax_footer_box2_content', wp_unslash($_POST['footer_box2_content']));
        update_option('zaramax_footer_box3_content', wp_unslash($_POST['footer_box3_content']));
        update_option('zaramax_footer_map_heading', sanitize_text_field($_POST['footer_map_heading']));
        // Use wp_unslash to preserve shortcodes without adding backslashes
        update_option('zaramax_footer_map_location', wp_unslash($_POST['footer_map_location']));
        update_option('zaramax_footer_disclaimer', wp_unslash($_POST['footer_disclaimer']));
        update_option('zaramax_footer_legal_links', wp_unslash($_POST['footer_legal_links']));
        
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
                                <p><?php echo do_shortcode(nl2br($footer_blurb)); ?></p>
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
                        <?php echo wpautop(do_shortcode($footer_box2_content)); ?>
                    </div>
                    
                    <!-- Box 3: Custom HTML -->
                    <div class="footer-box footer-box-3">
                        <?php echo wpautop(do_shortcode($footer_box3_content)); ?>
                    </div>
                    
                    <!-- Box 4: Google Maps -->
                    <div class="footer-box footer-box-4">
                        <?php if (!empty($footer_map_heading)): ?>
                            <h3 class="map-heading"><?php echo esc_html($footer_map_heading); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (!empty($footer_map_location)): ?>
                            <div class="footer-map">
                                <iframe 
                                    src="https://www.google.com/maps?q=<?php echo urlencode(wp_strip_all_tags(do_shortcode($footer_map_location))); ?>&output=embed"
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
                        <?php echo wpautop(do_shortcode($footer_disclaimer)); ?>
                    </div>
                    
                    <!-- Legal Links Area -->
                    <div class="footer-legal-links">
                        <?php 
                        // Debug: Show legal links variable status
                        echo '<!-- Legal Links Debug: footer_legal_links = "' . esc_attr($footer_legal_links) . '", length = ' . strlen($footer_legal_links) . ' -->';
                        
                        if (!empty($footer_legal_links)) {
                            // Process shortcodes and preserve multiple spaces
                            $legal_links_html = do_shortcode($footer_legal_links);
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
                        $image_html = '<div style="width:100%;height:180px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;color:#6c757d;">
                            <span class="dashicons dashicons-admin-tools" style="font-size:48px;"></span>
                        </div>';
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

add_filter('ai1wm_exclude_content_from_export', function($exclude_filters) {

  $exclude_filters[] = 'debug.log';

  return $exclude_filters;

});

/**
 * Render Batman Hero Box Section
 */
function staircase_render_batman_hero_box() {
    global $wpdb;
    
    // Check if this is the blog page
    if (is_home() && !is_front_page()) {
        $blog_page_id = get_option('page_for_posts');
        $post_id = $blog_page_id ?: get_the_ID();
        $cherry_heading = $blog_page_id ? get_the_title($blog_page_id) : 'Blog';
    } else {
        $post_id = get_the_ID();
        $cherry_heading = get_the_title(); // wp_posts.post_title
    }
    
    // Debug: Add HTML comment to verify function is being called
    echo "<!-- Batman Hero: Post ID = $post_id -->\n";
    
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
    } else {
        echo "<!-- No pylon data found for post $post_id -->\n";
    }
    
    // Get hero subheading from wp_pylons
    $cherry_subheading = '';
    if ($pylon_data && !empty($pylon_data['hero_subheading'])) {
        $cherry_subheading = $pylon_data['hero_subheading'];
    }
    
    // Always show buttons with default text
    $cherry_button_left_text = get_post_meta($post_id, 'cherry_button_left_text', true) ?: 'Get Your Estimate';
    $cherry_button_left_url = get_post_meta($post_id, 'cherry_button_left_url', true) ?: '';
    $cherry_button_right_text = get_post_meta($post_id, 'cherry_button_right_text', true) ?: 'Call Us Now';
    $cherry_button_right_url = get_post_meta($post_id, 'cherry_button_right_url', true) ?: '';
    
    // Get phone number from wp_zen_sitespren.driggs_phone_1
    $cherry_phone_number_raw = $wpdb->get_var("SELECT driggs_phone_1 FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $cherry_phone_number_formatted = $cherry_phone_number_raw ? staircase_format_phone_number($cherry_phone_number_raw) : '';
    
    // Set right button as call link if no custom URL is set
    if (empty($cherry_button_right_url) && !empty($cherry_phone_number_raw)) {
        $cherry_button_right_url = 'tel:' . preg_replace('/[^0-9]/', '', $cherry_phone_number_raw);
    }
    
    // Get paragon featured image for hero background
    $paragon_image_id = '';
    $paragon_image_url = '';
    if ($pylon_data && !empty($pylon_data['paragon_featured_image_id'])) {
        $paragon_image_id = $pylon_data['paragon_featured_image_id'];
        $paragon_image_url = wp_get_attachment_image_url($paragon_image_id, 'full');
        echo "<!-- Background Image: $paragon_image_url (ID: $paragon_image_id) -->\n";
    } else {
        echo "<!-- Background Image: None set -->\n";
    }
    
    // Default values
    if (empty($cherry_subheading)) {
        $cherry_subheading = get_bloginfo('description');
    }
    ?>
    <section class="hero-section cherry-hero">
        <div class="container">
            <div class="cherry-hero-content">
                <h1 class="cherry-heading"><?php echo esc_html($cherry_heading); ?></h1>
                <?php if ($cherry_subheading): ?>
                    <p class="cherry-subheading"><?php echo esc_html($cherry_subheading); ?></p>
                <?php endif; ?>
                
                <div class="cherry-buttons-container">
                    <?php if ($cherry_button_left_url): ?>
                        <a href="<?php echo esc_url($cherry_button_left_url); ?>" class="batman-hero-button batman-hero-button-left">
                            <?php echo esc_html($cherry_button_left_text); ?>
                        </a>
                    <?php else: ?>
                        <span class="batman-hero-button batman-hero-button-left batman-hero-button-disabled">
                            <?php echo esc_html($cherry_button_left_text); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($cherry_button_right_url): ?>
                        <a href="<?php echo esc_url($cherry_button_right_url); ?>" class="batman-hero-button batman-hero-button-right">
                            <?php echo esc_html($cherry_button_right_text); ?>
                        </a>
                    <?php else: ?>
                        <span class="batman-hero-button batman-hero-button-right batman-hero-button-disabled">
                            <?php echo esc_html($cherry_button_right_text); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($cherry_phone_number_raw): ?>
                    <div class="cherry-phone-container" style="text-align: center; margin-top: 15px;">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $cherry_phone_number_raw)); ?>" class="cherry-phone">
                            <?php echo esc_html($cherry_phone_number_formatted); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <style>
    .cherry-hero {
        <?php if (!empty($paragon_image_url)): ?>
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo esc_url($paragon_image_url); ?>');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        <?php else: ?>
        background: #53565b;
        <?php endif; ?>
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
        background: #3f72d7;
        color: white;
        border: 2px solid #3f72d7;
    }
    
    .cherry-button-left:hover {
        background: #2d5cbf;
        color: white;
        border-color: #2d5cbf;
    }
    
    .cherry-button-right {
        background: #3f72d7;
        color: white;
        border: 2px solid #3f72d7;
    }
    
    .cherry-button-right:hover {
        background: #2d5cbf;
        color: white;
        border-color: #2d5cbf;
    }
    
    .batman-hero-button-disabled {
        cursor: default;
        opacity: 0.6;
        background: #666;
        border-color: #666;
    }
    
    .cherry-phone-container {
        margin-top: 20px;
    }
    
    .cherry-phone {
        color: white;
        font-size: 1.3rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid white;
        padding: 10px 20px;
        border-radius: 25px;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .cherry-phone:hover {
        background: white;
        color: #333;
    }
    
    @media (max-width: 1200px) {
        .cherry-hero {
            padding: 60px 0;
        }
        
        .cherry-heading {
            font-size: 3rem;
        }
        
        .cherry-subheading {
            font-size: 1.2rem;
        }
        
        .cherry-buttons-container {
            flex-direction: column;
            align-items: center;
        }
        
        .cherry-button {
            width: 220px;
        }
        
        .cherry-phone {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 768px) {
        .cherry-heading {
            font-size: 2.5rem;
        }
        
        .cherry-subheading {
            font-size: 1.1rem;
        }
        
        .cherry-phone {
            font-size: 1rem;
        }
    }
    </style>
    <?php
}

/**
 * Render Chen Cards Box Section
 */
function staircase_render_chen_cards_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Get chenblock card data from wp_pylons
    $chen_card_1_title = '';
    $chen_card_1_description = '';
    $chen_card_2_title = '';
    $chen_card_2_description = '';
    $chen_card_3_title = '';
    $chen_card_3_description = '';
    
    if ($pylon_data) {
        $chen_card_1_title = !empty($pylon_data['chenblock_card1_title']) ? $pylon_data['chenblock_card1_title'] : '';
        $chen_card_1_description = !empty($pylon_data['chenblock_card1_desc']) ? $pylon_data['chenblock_card1_desc'] : '';
        
        $chen_card_2_title = !empty($pylon_data['chenblock_card2_title']) ? $pylon_data['chenblock_card2_title'] : '';
        $chen_card_2_description = !empty($pylon_data['chenblock_card2_desc']) ? $pylon_data['chenblock_card2_desc'] : '';
        
        $chen_card_3_title = !empty($pylon_data['chenblock_card3_title']) ? $pylon_data['chenblock_card3_title'] : '';
        $chen_card_3_description = !empty($pylon_data['chenblock_card3_desc']) ? $pylon_data['chenblock_card3_desc'] : '';
    }
    
    // Show chen card block if any cards have content
    $has_chen_cards = !empty($chen_card_1_title) || !empty($chen_card_2_title) || !empty($chen_card_3_title);
    if ($has_chen_cards): 
    ?>
    <section class="chen-card-block">
        <div class="container">
            <div class="chen-cards-grid">
                <?php if (!empty($chen_card_1_title)): ?>
                    <div class="chen-card">
                        <h3 class="chen-card-title"><?php echo esc_html($chen_card_1_title); ?></h3>
                        <?php if (!empty($chen_card_1_description)): ?>
                            <p class="chen-card-description"><?php echo esc_html($chen_card_1_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($chen_card_2_title)): ?>
                    <div class="chen-card">
                        <h3 class="chen-card-title"><?php echo esc_html($chen_card_2_title); ?></h3>
                        <?php if (!empty($chen_card_2_description)): ?>
                            <p class="chen-card-description"><?php echo esc_html($chen_card_2_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($chen_card_3_title)): ?>
                    <div class="chen-card">
                        <h3 class="chen-card-title"><?php echo esc_html($chen_card_3_title); ?></h3>
                        <?php if (!empty($chen_card_3_description)): ?>
                            <p class="chen-card-description"><?php echo esc_html($chen_card_3_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <style>
    .chen-card-block {
        padding: 60px 0;
        background: #f9f9f9;
    }
    
    .chen-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .chen-card {
        background: white;
        border-radius: 12px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    
    .chen-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .chen-card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 15px 0;
        color: #333;
        line-height: 1.3;
    }
    
    .chen-card-description {
        font-size: 1rem;
        line-height: 1.6;
        color: #666;
        margin: 0;
    }
    
    @media (max-width: 1200px) {
        .chen-cards-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .chen-card {
            padding: 30px 25px;
        }
    }
    
    @media (max-width: 768px) {
        .chen-card-block {
            padding: 40px 0;
        }
        
        .chen-cards-grid {
            grid-template-columns: 1fr;
            gap: 25px;
            padding: 0 20px;
        }
        
        .chen-card {
            padding: 25px 20px;
        }
        
        .chen-card-title {
            font-size: 1.3rem;
        }
        
        .chen-card-description {
            font-size: 0.9rem;
        }
    }
    </style>
    <?php
    endif;
}

/**
 * Render Derek Blog Post Meta Box Section
 * Only displays for posts (wp_posts.post_type = 'post')
 */
function staircase_render_derek_blog_post_meta_box() {
    // Only render for posts, not pages
    if (get_post_type() !== 'post') {
        return;
    }
    
    $post_id = get_the_ID();
    $post_date = get_the_date();
    $author_name = get_the_author();
    
    // Get primary category
    $categories = get_the_category();
    $primary_category = !empty($categories) ? $categories[0]->name : 'Uncategorized';
    ?>
    
    <section class="derek-blog-post-meta-box">
        <div class="container">
            <div class="derek-meta-content">
                <span class="derek-post-date"><?php echo esc_html($post_date); ?></span>
                <span class="derek-meta-separator">|</span>
                <span class="derek-post-author">posted by <?php echo esc_html($author_name); ?></span>
                <span class="derek-meta-separator">in</span>
                <span class="derek-post-category"><?php echo esc_html($primary_category); ?></span>
            </div>
        </div>
    </section>
    
    <style>
    .derek-blog-post-meta-box {
        height: 50px;
        border-bottom: 1px solid #ccc;
        display: flex;
        align-items: center;
        background: #fff;
        padding: 0;
        margin: 0;
    }
    
    .derek-meta-content {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #666;
        line-height: 1;
    }
    
    .derek-post-date,
    .derek-post-author,
    .derek-post-category {
        font-weight: 500;
    }
    
    .derek-meta-separator {
        color: #999;
        margin: 0 4px;
    }
    
    @media (max-width: 768px) {
        .derek-blog-post-meta-box {
            padding: 0 20px;
        }
        
        .derek-meta-content {
            font-size: 13px;
            gap: 6px;
        }
        
        .derek-meta-separator {
            margin: 0 2px;
        }
    }
    </style>
    
    <?php
}

/**
 * Render Serena FAQ Box Section
 */
function staircase_render_serena_faq_box() {
    $current_post_id = get_the_ID();
    global $wpdb;
    
    // Get FAQ data from wp_pylons table for current post (using correct column names with _box suffix)
    $faq_data = $wpdb->get_row($wpdb->prepare(
        "SELECT serena_faq_box_q1, serena_faq_box_a1, serena_faq_box_q2, serena_faq_box_a2, 
                serena_faq_box_q3, serena_faq_box_a3, serena_faq_box_q4, serena_faq_box_a4,
                serena_faq_box_q5, serena_faq_box_a5, serena_faq_box_q6, serena_faq_box_a6,
                serena_faq_box_q7, serena_faq_box_a7, serena_faq_box_q8, serena_faq_box_a8,
                serena_faq_box_q9, serena_faq_box_a9, serena_faq_box_q10, serena_faq_box_a10
         FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $current_post_id
    ), ARRAY_A);
    
    // DEBUG: Show FAQ data retrieval (remove after testing)
    echo "<!-- DEBUG: FAQ Data Retrieved: " . print_r($faq_data, true) . " -->\n";
    
    // Check if any FAQ content exists
    $has_faq_content = false;
    if ($faq_data) {
        // Check all FAQ question and answer fields for content (using correct column names)
        for ($i = 1; $i <= 10; $i++) {
            $question = $faq_data["serena_faq_box_q{$i}"] ?? '';
            $answer = $faq_data["serena_faq_box_a{$i}"] ?? '';
            
            // DEBUG: Show each FAQ field check
            if (!empty(trim($question)) || !empty(trim($answer))) {
                echo "<!-- DEBUG: Found FAQ content in q{$i}/a{$i}: Q='{$question}', A='{$answer}' -->\n";
                $has_faq_content = true;
                break;
            }
        }
    }
    
    // DEBUG: Show final decision
    echo "<!-- DEBUG: Has FAQ content: " . ($has_faq_content ? 'YES' : 'NO') . " -->\n";
    
    // Only render the FAQ box if content exists
    if ($has_faq_content) {
    ?>
    <!-- Serena FAQ Box Section -->
    <section class="serena-faq-box">
        <div class="serena-faq-container">
            <h2 class="serena-faq-title"><span class="serena-faq-star">★</span> FAQ</h2>
            <p class="serena-faq-subtitle">Frequently Asked Questions</p>
            
            <div class="serena-faq-accordion">
                <?php
                // Loop through FAQ data and display non-empty items (using correct column names)
                for ($i = 1; $i <= 10; $i++) {
                    $question = $faq_data["serena_faq_box_q{$i}"] ?? '';
                    $answer = $faq_data["serena_faq_box_a{$i}"] ?? '';
                    
                    // Only display if both question and answer have content
                    if (!empty(trim($question)) && !empty(trim($answer))) {
                ?>
                    <div class="serena-faq-item">
                        <button class="serena-faq-question" onclick="toggleFAQ(this)">
                            <?php echo esc_html(trim($question)); ?>
                            <span class="serena-faq-icon">+</span>
                        </button>
                        <div class="serena-faq-answer">
                            <p><?php echo wp_kses_post(wpautop(trim($answer))); ?></p>
                        </div>
                    </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        
        <script>
        function toggleFAQ(button) {
            const answer = button.nextElementSibling;
            const icon = button.querySelector('.serena-faq-icon');
            const isOpen = answer.style.display === 'block';
            
            // Close all other FAQ items
            document.querySelectorAll('.serena-faq-answer').forEach(item => {
                item.style.display = 'none';
            });
            document.querySelectorAll('.serena-faq-icon').forEach(item => {
                item.textContent = '+';
            });
            document.querySelectorAll('.serena-faq-question').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle current item
            if (!isOpen) {
                answer.style.display = 'block';
                icon.textContent = '-';
                button.classList.add('active');
            }
        }
        </script>
    </section>
    <?php
    } // End if ($has_faq_content)
}

/**
 * Render Nile Map Box Section
 */
function staircase_render_nile_map_box() {
    $current_post_id = get_the_ID();
    global $wpdb;
    
    // Check if this page has a locationpage archetype in wp_pylons
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT pylon_archetype, locpage_gmaps_string FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $current_post_id
    ), ARRAY_A);
    
    $location_string = '';
    
    // If this is a locationpage, use the locpage_gmaps_string
    if ($pylon_data && $pylon_data['pylon_archetype'] === 'locationpage' && !empty($pylon_data['locpage_gmaps_string'])) {
        $location_string = trim($pylon_data['locpage_gmaps_string']);
    } else {
        // Default behavior: Get location data from wp_zen_sitespren table
        $city = $wpdb->get_var("SELECT driggs_city FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
        $state_full = $wpdb->get_var("SELECT driggs_state_full FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
        $state_code = $wpdb->get_var("SELECT driggs_state_code FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
        $country = $wpdb->get_var("SELECT driggs_country FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
        
        // Use state_full if available, otherwise fallback to state_code
        $state = !empty($state_full) ? $state_full : $state_code;
        
        // Build location string with proper comma handling
        $location_parts = array();
        if (!empty($city)) {
            $location_parts[] = trim($city);
        }
        if (!empty($state)) {
            $location_parts[] = trim($state);
        }
        if (!empty($country)) {
            $location_parts[] = trim($country);
        }
        
        $location_string = implode(', ', $location_parts);
    }
    
    $fallback_location = 'Dallas, TX'; // Fallback if no location data
    $final_location = !empty($location_string) ? $location_string : $fallback_location;
    
    // URL encode the location for the Google Maps embed
    $encoded_location = urlencode($final_location);
    ?>
    <!-- Nile Map Box Section -->
    <section class="nile-map-box">
        <div class="map-header">
            <h3><span class="nile-map-pin">📍</span> On the map</h3>
        </div>
        <div class="map-embed-container">
            <iframe 
                src="https://www.google.com/maps?q=<?php echo $encoded_location; ?>&output=embed"
                width="100%" 
                height="275" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Our Location - <?php echo esc_attr($final_location); ?>">
            </iframe>
        </div>
    </section>
    <?php
}

/**
 * Render OSB (Our Services Box)
 */
function staircase_render_osb_box() {
    // Only render on front page when enabled
    if (!is_front_page()) {
        return;
    }
    
    global $wpdb;
    $post_id = get_the_ID();
    $pylons_table = $wpdb->prefix . 'pylons';
    
    // Check if OSB is enabled for this page
    $osb_enabled = $wpdb->get_var($wpdb->prepare(
        "SELECT osb_is_enabled FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ));
    
    if (!$osb_enabled) {
        return;
    }
    
    // Render the Our Services section
    staircase_our_services_section();
}

/**
 * Render Kristina CTA Box Section
 */
function staircase_render_kristina_cta_box() {
    global $wpdb;
    $kristina_phone_number_raw = $wpdb->get_var("SELECT driggs_phone_1 FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $kristina_phone_number_formatted = $kristina_phone_number_raw ? staircase_format_phone_number($kristina_phone_number_raw) : '';
    
    // Clean phone number for tel: link (same as hero logic)
    $clean_phone = preg_replace('/[^0-9+]/', '', $kristina_phone_number_raw);
    ?>
    <!-- Kristina CTA Box Section -->
    <section class="kristina-cta-box">
        <div class="kristina-cta-container">
            <h2 class="kristina-cta-heading">Get Your Free Estimate Today</h2>
            <p class="kristina-cta-subtext">Contact us today for professional service and free estimates</p>
            
            <?php if (!empty($kristina_phone_number_raw)): ?>
                <a href="tel:<?php echo esc_attr($clean_phone); ?>" class="kristina-cta-button">
                    Call Now
                </a>
            <?php else: ?>
                <a href="tel:555-0123" class="kristina-cta-button">
                    Call Now
                </a>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Render Victoria Blog Box Section
 */
function staircase_render_victoria_blog_box() {
    ?>
    <!-- Victoria Blog Box Section -->
    <section class="victoria-blog-box">
        <div class="blog-box-container">
            <h2>Recent Posts</h2>
            <p class="blog-box-subtitle">Key insights from our team</p>
            
            <div class="blog-posts-grid">
                <?php
                // Get the 3 most recent blog posts
                $recent_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_posts->have_posts()) :
                    while ($recent_posts->have_posts()) : $recent_posts->the_post();
                        $author_id = get_the_author_meta('ID');
                        $author_name = get_the_author();
                        $post_date = get_the_date('M j, Y');
                        $post_title = get_the_title();
                        $post_excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20, '...');
                        $post_link = get_permalink();
                        ?>
                        <div class="blog-post-card">
                            <div class="post-meta">
                                <span class="post-date"><?php echo esc_html($post_date); ?></span>
                                <span class="post-author">By <?php echo esc_html($author_name); ?></span>
                            </div>
                            <h3 class="post-title">
                                <a href="<?php echo esc_url($post_link); ?>"><?php echo esc_html($post_title); ?></a>
                            </h3>
                            <div class="post-excerpt"><?php echo esc_html($post_excerpt); ?></div>
                            <a href="<?php echo esc_url($post_link); ?>" class="read-more-link">Read More</a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    // Fallback if no posts exist
                    ?>
                    <div class="no-posts-message">
                        <p>No blog posts available yet. Check back soon!</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            
            <?php 
            // Get the blog page URL
            $blog_page_id = get_option('page_for_posts');
            if ($blog_page_id) :
                $blog_page_url = get_permalink($blog_page_id);
            ?>
                <div class="blog-button-container">
                    <a href="<?php echo esc_url($blog_page_url); ?>" class="go-to-blog-btn">Go To Blog</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Render Nile and Victoria boxes for Cherry template pages
 * Single source of truth for these template sections
 */
function staircase_render_cherry_template_boxes() {
    $current_template = staircase_get_current_template();
    $current_post_id = get_the_ID();
    
    // DEBUG: Add debug output (remove after testing)
    echo "<!-- DEBUG: Current template: {$current_template}, Post ID: {$current_post_id} -->\n";
    
    // Only show on Cherry template pages
    if ($current_template === 'cherry' || $current_template === 'homepage-cherry') {
        // Render all individual component boxes
        staircase_render_serena_faq_box();
        staircase_render_nile_map_box();
        staircase_render_kristina_cta_box();
        staircase_render_victoria_blog_box();
    }
}