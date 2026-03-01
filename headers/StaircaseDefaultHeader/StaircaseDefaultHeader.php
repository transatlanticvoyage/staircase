<?php
/**
 * StaircaseDefaultHeader Class
 * 
 * Encapsulated header component for Staircase theme
 * Handles all header functionality, styling, and rendering
 */

class StaircaseDefaultHeader {
    
    private $config;
    private $version = '1.0.0';
    
    public function __construct() {
        $this->load_config();
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        
        // Add body class for header type
        add_filter('body_class', [$this, 'add_body_class']);
        
        // Register navigation menu location if not exists
        add_action('after_setup_theme', [$this, 'register_nav_menu']);
    }
    
    /**
     * Load configuration
     */
    private function load_config() {
        $config_file = dirname(__FILE__) . '/config.json';
        if (file_exists($config_file)) {
            $this->config = json_decode(file_get_contents($config_file), true);
        } else {
            $this->config = $this->get_default_config();
        }
    }
    
    /**
     * Get default configuration
     */
    private function get_default_config() {
        return [
            'breakpoints' => [
                'mobile' => 480,
                'tablet' => 768,
                'desktop' => 769
            ],
            'colors' => [
                'background' => '#ffffff',
                'text' => '#333333',
                'accent' => '#23bb73',
                'hover' => '#fd9f1f'
            ],
            'spacing' => [
                'container_padding' => '20px',
                'nav_gap' => '30px'
            ],
            'features' => [
                'sticky' => true,
                'transparent' => false,
                'search' => false
            ]
        ];
    }
    
    /**
     * Enqueue styles and scripts
     */
    public function enqueue_assets() {
        $base_url = get_template_directory_uri() . '/headers/StaircaseDefaultHeader';
        
        // Base styles (mobile-first)
        wp_enqueue_style(
            'sdhdr-base',
            $base_url . '/styles/base.css',
            [],
            $this->version
        );
        
        // Mobile styles
        wp_enqueue_style(
            'sdhdr-mobile',
            $base_url . '/styles/mobile.css',
            ['sdhdr-base'],
            $this->version,
            '(max-width: 480px)'
        );
        
        // Tablet styles
        wp_enqueue_style(
            'sdhdr-tablet',
            $base_url . '/styles/tablet.css',
            ['sdhdr-base'],
            $this->version,
            '(min-width: 481px) and (max-width: 768px)'
        );
        
        // Desktop styles
        wp_enqueue_style(
            'sdhdr-desktop',
            $base_url . '/styles/desktop.css',
            ['sdhdr-base'],
            $this->version,
            '(min-width: 769px)'
        );
        
        // Admin bar adjustments
        if (is_admin_bar_showing()) {
            wp_enqueue_style(
                'sdhdr-admin-bar',
                $base_url . '/styles/admin-bar.css',
                ['sdhdr-base'],
                $this->version
            );
        }
        
        // Navigation JavaScript
        wp_enqueue_script(
            'sdhdr-navigation',
            $base_url . '/js/navigation.js',
            ['jquery'],
            $this->version,
            true
        );
        
        // Localize script with config
        wp_localize_script('sdhdr-navigation', 'sdhdr_config', $this->config);
        
        // Add dynamic styles for CTA button colors
        $this->add_dynamic_styles();
    }
    
    /**
     * Add dynamic styles based on settings
     */
    private function add_dynamic_styles() {
        $cta_color = get_option('staircase_cta_button_color', '#23bb73');
        $cta_hover = get_option('staircase_cta_button_hover_color', '#fd9f1f');
        
        $custom_css = "
            .sdhdr-phone-button {
                background-color: {$cta_color} !important;
            }
            .sdhdr-phone-button:hover {
                background-color: {$cta_hover} !important;
            }
        ";
        
        wp_add_inline_style('sdhdr-base', $custom_css);
    }
    
    /**
     * Add body class
     */
    public function add_body_class($classes) {
        $classes[] = 'has-staircase-default-header';
        return $classes;
    }
    
    /**
     * Register navigation menu
     */
    public function register_nav_menu() {
        register_nav_menus([
            'staircase-primary' => __('Staircase Primary Menu', 'staircase')
        ]);
    }
    
    /**
     * Render the header
     */
    public function render() {
        include dirname(__FILE__) . '/template.php';
    }
    
    /**
     * Get logo HTML
     */
    public function get_logo() {
        $logo_url = get_option('staircase_header_logo', '');
        $site_name = get_bloginfo('name');
        $home_url = esc_url(home_url('/'));
        
        if (!empty($logo_url)) {
            return sprintf(
                '<a href="%s" class="sdhdr-logo-link" rel="home">
                    <img src="%s" alt="%s" class="sdhdr-logo">
                </a>',
                $home_url,
                esc_url($logo_url),
                esc_attr($site_name)
            );
        } elseif (has_custom_logo()) {
            return get_custom_logo();
        } else {
            return sprintf(
                '<h1 class="sdhdr-site-title">
                    <a href="%s" rel="home">%s</a>
                </h1>',
                $home_url,
                esc_html($site_name)
            );
        }
    }
    
    /**
     * Get navigation menu
     */
    public function get_navigation() {
        // Check if Silkweaver menu system is enabled
        if (function_exists('silkweaver_render_menu') && get_option('silkweaver_use_system', true)) {
            return silkweaver_render_menu();
        }
        
        // Use WordPress menu with custom walker
        return wp_nav_menu([
            'theme_location' => 'primary',
            'menu_id' => 'sdhdr-primary-menu',
            'menu_class' => 'sdhdr-menu',
            'container' => false,
            'depth' => 3,
            'walker' => new StaircaseDefaultHeader_Walker(),
            'fallback_cb' => [$this, 'menu_fallback'],
            'echo' => false
        ]);
    }
    
    /**
     * Menu fallback
     */
    public function menu_fallback() {
        $pages = wp_list_pages([
            'title_li' => '',
            'depth' => 1,
            'echo' => false
        ]);
        return '<ul id="sdhdr-primary-menu" class="sdhdr-menu">' . $pages . '</ul>';
    }
    
    /**
     * Get phone button
     */
    public function get_phone_button() {
        if (!function_exists('staircase_get_formatted_phone')) {
            return '';
        }
        
        $phone_raw = staircase_get_header_phone();
        $phone_formatted = staircase_get_formatted_phone();
        
        if (empty($phone_raw)) {
            return '';
        }
        
        $phone_clean = preg_replace('/[^0-9+]/', '', $phone_raw);
        
        return sprintf(
            '<div class="sdhdr-phone">
                <a href="tel:%s" class="sdhdr-phone-button">
                    <svg class="sdhdr-phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    <span class="sdhdr-phone-number">%s</span>
                </a>
            </div>',
            esc_attr($phone_clean),
            esc_html($phone_formatted)
        );
    }
}

/**
 * Custom Walker for Navigation Menu
 */
class StaircaseDefaultHeader_Walker extends Walker_Nav_Menu {
    
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $classes = 'sdhdr-submenu sdhdr-depth-' . $depth;
        $output .= "\n$indent<ul class=\"$classes\">\n";
    }
    
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'sdhdr-menu-item-' . $item->ID;
        
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'sdhdr-has-children';
        }
        
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'sdhdr-current';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'sdhdr-menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names . '>';
        
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        
        $item_output = $args->before ?? '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= ($args->link_before ?? '') . apply_filters('the_title', $item->title, $item->ID) . ($args->link_after ?? '');
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';
        
        if (in_array('menu-item-has-children', $classes)) {
            $item_output .= '<button class="sdhdr-dropdown-toggle" aria-expanded="false">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M6 9L2 5h8z"/>
                </svg>
            </button>';
        }
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}