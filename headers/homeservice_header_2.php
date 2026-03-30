<?php
/**
 * Homeservice Header 2
 * A1 Chimney style header with Elementor-based design
 * Features: Sticky header, mega menu dropdowns, mobile responsive
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the homeservice header 2
 */
function render_homeservice_header_2() {
    // Add body class for CSS targeting
    add_filter('body_class', function($classes) {
        $classes[] = 'hs2-active';
        return $classes;
    });
    
    // Enqueue header-specific assets - SIMPLIFIED VERSION
    $assets_url = get_template_directory_uri() . '/headers/homeservice_header_2_assets/';
    
    // Only load the simplified CSS
    wp_enqueue_style('homeservice-header-2-simplified', $assets_url . 'css/simplified-styles.css', array(), '2.0.0');
    wp_enqueue_script('homeservice-header-2-simplified-scripts', $assets_url . 'js/simplified-scripts.js', array('jquery'), '2.0.0', true);
    
    // Get the SIMPLIFIED header HTML content
    $header_file = get_template_directory() . '/headers/homeservice_header_2_assets/header-content-simplified.html';
    
    if (file_exists($header_file)) {
        $header_html = file_get_contents($header_file);
        
        // Replace logo placeholder with actual logo
        $logo_url = get_option('staircase_header_logo', '');
        if (empty($logo_url) && has_custom_logo()) {
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
            if ($logo_data) {
                $logo_url = $logo_data[0];
            }
        }
        
        if (!empty($logo_url)) {
            $logo_html = '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '">';
        } else {
            $logo_html = '<h1 class="hs2-site-title">' . get_bloginfo('name') . '</h1>';
        }
        
        $header_html = str_replace('{{LOGO_PLACEHOLDER}}', $logo_html, $header_html);
        
        // Generate navigation menu
        $nav_html = '';
        if (function_exists('silkweaver_render_menu') && get_option('silkweaver_use_system', true)) {
            // Use Silkweaver menu system with custom wrapper
            $silkweaver_menu = silkweaver_render_menu();
            // Transform silkweaver menu to our header 2 structure
            $nav_html = homeservice_header_2_transform_menu($silkweaver_menu);
        } else {
            // Fallback to WordPress menu
            ob_start();
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'hs2-menu',
                'container' => false,
                'walker' => new Homeservice_Header_2_Walker_Nav_Menu(),
                'fallback_cb' => 'homeservice_header_2_fallback_menu'
            ));
            $nav_html = ob_get_clean();
        }
        
        // Replace menu placeholder
        $header_html = str_replace('{{MENU_PLACEHOLDER}}', $nav_html, $header_html);
        
        // Replace phone number if available
        $phone_html = '';
        if (function_exists('staircase_get_formatted_phone')) {
            $phone_raw = staircase_get_header_phone();
            $phone_formatted = staircase_get_formatted_phone();
            if (!empty($phone_raw)) {
                $phone_html = '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone_raw)) . '" class="hs2-phone-button"><svg class="hs2-phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>' . esc_html($phone_formatted) . '</a>';
            }
        }
        
        $header_html = str_replace('{{PHONE_PLACEHOLDER}}', $phone_html, $header_html);
        $header_html = str_replace('{{HOME_URL}}', esc_url(home_url('/')), $header_html);
        
        echo $header_html;
    } else {
        // Fallback header
        ?>
        <header class="site-header homeservice-header-2">
            <div class="homeservice_header_2_container">
                <p style="padding: 20px; text-align: center;">Header files not found. Please check installation.</p>
            </div>
        </header>
        <?php
    }
}

/**
 * Transform Silkweaver menu HTML to simplified homeservice_header_2 structure
 */
function homeservice_header_2_transform_menu($silkweaver_html) {
    if (empty($silkweaver_html)) {
        return homeservice_header_2_fallback_menu();
    }
    
    // Parse the silkweaver menu and transform it
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $silkweaver_html);
    
    $output = '<ul class="hs2-menu">';
    
    $xpath = new DOMXPath($dom);
    $menu_items = $xpath->query('//ul[@class="silkweaver-menu"]/li');
    
    foreach ($menu_items as $item) {
        $link = $item->getElementsByTagName('a')->item(0);
        if ($link) {
            $href = $link->getAttribute('href');
            $text = $link->textContent;
            
            // Check for submenu
            $submenu = $xpath->query('.//ul', $item);
            $has_submenu = $submenu->length > 0;
            
            $output .= '<li class="hs2-menu-item' . ($has_submenu ? ' hs2-has-dropdown' : '') . '">';
            $output .= '<a class="hs2-menu-link" href="' . esc_url($href) . '">';
            $output .= esc_html($text);
            $output .= '</a>';
            
            if ($has_submenu) {
                $output .= '<button class="hs2-dropdown-icon" aria-haspopup="true" aria-expanded="false">';
                $output .= '<svg width="12" height="8" viewBox="0 0 12 8" fill="currentColor"><path d="M6 8L0 0h12L6 8z"/></svg>';
                $output .= '</button>';
                
                // Add dropdown content
                $output .= '<div class="hs2-dropdown">';
                $output .= '<ul>';
                
                foreach ($submenu as $sub) {
                    $sub_items = $sub->getElementsByTagName('li');
                    foreach ($sub_items as $sub_item) {
                        $sub_link = $sub_item->getElementsByTagName('a')->item(0);
                        if ($sub_link) {
                            $output .= '<li><a href="' . esc_url($sub_link->getAttribute('href')) . '">' . esc_html($sub_link->textContent) . '</a></li>';
                        }
                    }
                }
                
                $output .= '</ul></div>';
            }
            
            $output .= '</li>';
        }
    }
    
    $output .= '</ul>';
    
    return $output;
}

/**
 * Custom Walker for WordPress menu in simplified homeservice_header_2
 */
class Homeservice_Header_2_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="hs2-dropdown"><ul>';
        } else {
            $output .= '<ul>';
        }
    }
    
    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
        if ($depth === 0) {
            $output .= '</div>';
        }
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $has_children = in_array('menu-item-has-children', $item->classes);
        
        if ($depth === 0) {
            $output .= '<li class="hs2-menu-item' . ($has_children ? ' hs2-has-dropdown' : '') . '">';
            $output .= '<a class="hs2-menu-link" href="' . esc_url($item->url) . '">';
            $output .= esc_html($item->title);
            $output .= '</a>';
            
            if ($has_children) {
                $output .= '<button class="hs2-dropdown-icon" aria-haspopup="true" aria-expanded="false">';
                $output .= '<svg width="12" height="8" viewBox="0 0 12 8" fill="currentColor"><path d="M6 8L0 0h12L6 8z"/></svg>';
                $output .= '</button>';
            }
        } else {
            $output .= '<li>';
            $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

/**
 * Fallback menu when no menu is assigned
 */
function homeservice_header_2_fallback_menu() {
    return '<ul class="hs2-menu"><li class="hs2-menu-item"><a class="hs2-menu-link" href="' . esc_url(home_url('/')) . '">Home</a></li></ul>';
}