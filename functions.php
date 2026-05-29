<?php
// TEST COMMENT — modified by chat session 2026-04-15
// Test comment: VSCode source control sync test - 2026-05-28
/**
 * Staircase Theme Functions
 * 
 * @package Staircase
 */

// Load the header system early
require_once get_template_directory() . '/headers/header-loader.php';

// Load the centralized template selector system
require_once get_template_directory() . '/inc/template-selector.php';

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
    
    // Enqueue Galleryberry assets only on pages using the galleryberry template
    if (is_page() || is_single()) {
        global $wpdb, $post;
        if (!empty($post) && !empty($post->ID)) {
            $gby_template = $wpdb->get_var($wpdb->prepare(
                "SELECT staircase_page_template_desired FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d LIMIT 1",
                $post->ID
            ));
            if ($gby_template === 'galleryberry') {
                $gby_css = get_template_directory() . '/page-templates/galleryberry.css';
                $gby_js  = get_template_directory() . '/page-templates/galleryberry.js';
                if (file_exists($gby_css)) {
                    wp_enqueue_style(
                        'staircase-galleryberry',
                        get_template_directory_uri() . '/page-templates/galleryberry.css',
                        array('staircase-style'),
                        filemtime($gby_css)
                    );
                }
                if (file_exists($gby_js)) {
                    wp_enqueue_script(
                        'staircase-galleryberry',
                        get_template_directory_uri() . '/page-templates/galleryberry.js',
                        array(),
                        filemtime($gby_js),
                        true
                    );
                }
            }
        }
    }

    // Enqueue Silkweaver menu styles if system is enabled
    if (function_exists('silkweaver_render_menu') && get_option('silkweaver_use_system', true)) {
        wp_enqueue_style(
            'staircase-silkweaver',
            get_template_directory_uri() . '/css/silkweaver-core.css',
            array('staircase-style'),
            filemtime(get_template_directory() . '/css/silkweaver-core.css')
        );
        wp_enqueue_style(
            'staircase-silkweaver-elegant',
            get_template_directory_uri() . '/css/silkweaver-elegant.css',
            array('staircase-silkweaver'),
            filemtime(get_template_directory() . '/css/silkweaver-elegant.css')
        );
        wp_enqueue_script(
            'staircase-silkweaver-elegant-mobile',
            get_template_directory_uri() . '/js/silkweaver-elegant-mobile.js',
            array(),
            filemtime(get_template_directory() . '/js/silkweaver-elegant-mobile.js'),
            true
        );
    }
    
    // Conditionally enqueue TPCom nav styles
    if (get_option('staircase_use_tpcom_nav_styles', false)) {
        wp_enqueue_style(
            'staircase-tpcom-nav-styles',
            get_template_directory_uri() . '/TPComExtractedNavStyles.css',
            array('staircase-style'),
            filemtime(get_template_directory() . '/TPComExtractedNavStyles.css')
        );
    }
    
    // Enqueue header2 styles early to prevent FOUC — must be here (wp_enqueue_scripts)
    // so the <link> tag lands in <head> when wp_head() fires, not after body render
    $header2_css_path = get_template_directory() . '/headers/header2/assets/css/styles.css';
    if (file_exists($header2_css_path)) {
        wp_enqueue_style(
            'header2-styles',
            get_template_directory_uri() . '/headers/header2/assets/css/styles.css',
            array(),
            filemtime($header2_css_path)
        );
    }

    // Enqueue company info table component (footer-agnostic, reusable)
    $cit_css_path = get_template_directory() . '/special-elements/zh_agnostic_company_info_table.css';
    if (file_exists($cit_css_path)) {
        wp_enqueue_style(
            'zh-agnostic-company-info-table',
            get_template_directory_uri() . '/special-elements/zh_agnostic_company_info_table.css',
            array(),
            filemtime($cit_css_path)
        );
        // Inject dynamic label cell bg-color and text-color from WP options
        $cit_label_color      = sanitize_hex_color(get_option('zh_agnostic_cit_label_bg_color',   '#101722')) ?: '#101722';
        $cit_label_text_color = sanitize_hex_color(get_option('zh_agnostic_cit_label_text_color', '#ffffff')) ?: '#ffffff';
        wp_add_inline_style(
            'zh-agnostic-company-info-table',
            '.zh_agnostic_cit_label_cell { background-color: ' . $cit_label_color . ' !important; border-color: ' . $cit_label_color . ' !important; color: ' . $cit_label_text_color . ' !important; }'
        );
    }

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

// Add vibrantcashew-template body class server-side to prevent FOUC.
// Must hook to 'wp' (after WP knows the current page) so the filter runs
// before body_class() is called in the <body> tag.
add_action('wp', function() {
    $post_id = get_queried_object_id();
    if (!$post_id) return;
    global $wpdb;
    $template = $wpdb->get_var($wpdb->prepare(
        "SELECT staircase_page_template_desired FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $post_id
    ));
    if ($template === 'vibrantcashew') {
        add_filter('body_class', function($classes) {
            $classes[] = 'vibrantcashew-template';
            return $classes;
        });
    }
});

// Frontend Jezel Widget for Admin Users
function staircase_frontend_jezel_widget() {
    // Only load for logged-in users who can edit posts
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        return; // Public users get nothing - zero performance impact
    }
    
    // Don't show in admin area or on login page
    if (is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
        return;
    }
    
    global $post;
    $post_id = is_singular() ? get_the_ID() : 0;
    
    ?>
    <!-- Frontend Jezel Widget - Admin Only -->
    <div id="jezel-frontend-widget" class="jezel-frontend-container">
        <!-- Collapse Toggle -->
        <button id="jezel-toggle" class="jezel-btn jezel-toggle-btn" onclick="toggleJezelWidget()" title="Toggle Jezel Widget">
            <span class="jezel-toggle-icon">▶</span>
        </button>
        
        <div id="jezel-buttons" class="jezel-buttons-wrapper">
            <!-- Scroll Navigation Buttons -->
            <button class="jezel-btn jezel-scroll-btn" onclick="jezelScrollToTop()" title="Scroll to top">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            </button>
            
            <button class="jezel-btn jezel-scroll-btn" onclick="jezelScrollTo(0.25)" title="Scroll to 25%">
                <span>25</span>
            </button>
            
            <button class="jezel-btn jezel-scroll-btn" onclick="jezelScrollTo(0.50)" title="Scroll to middle">
                <span>M</span>
            </button>
            
            <button class="jezel-btn jezel-scroll-btn" onclick="jezelScrollTo(0.75)" title="Scroll to 75%">
                <span>75</span>
            </button>
            
            <button class="jezel-btn jezel-scroll-btn" onclick="jezelScrollToBottom()" title="Scroll to bottom">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            
            <?php if ($post_id): ?>
            <div class="jezel-divider"></div>
            
            <!-- Quick Jump to Sections -->
            <button class="jezel-btn jezel-section-btn" onclick="jezelJumpToSection('.hero-section, .batman-hero-section')" title="Jump to Hero">
                <span>H</span>
            </button>
            
            <button class="jezel-btn jezel-section-btn" onclick="jezelJumpToSection('.chen-cards-section')" title="Jump to Cards">
                <span>C</span>
            </button>
            
            <button class="jezel-btn jezel-section-btn" onclick="jezelJumpToSection('.serena-faq-section')" title="Jump to FAQ">
                <span>F</span>
            </button>
            
            <div class="jezel-divider"></div>
            
            <!-- Edit Links -->
            <button class="jezel-btn jezel-edit-btn" onclick="window.open('<?php echo admin_url('admin.php?page=telescope&post_id=' . $post_id); ?>', '_blank')" title="Edit in Telescope">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                </svg>
            </button>
            
            <button class="jezel-btn jezel-wp-btn" onclick="window.open('<?php echo admin_url('post.php?post=' . $post_id . '&action=edit'); ?>', '_blank')" title="Edit in WordPress">
                <span style="font-size: 11px;">WP</span>
            </button>
            <?php endif; ?>
            
            <!-- Cache Clear (if available) -->
            <?php if (function_exists('wp_cache_clear_cache')): ?>
            <button class="jezel-btn jezel-cache-btn" onclick="jezelClearCache()" title="Clear Page Cache">
                <span style="font-size: 11px;">CC</span>
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    /* Frontend Jezel Widget Styles */
    .jezel-frontend-container {
        position: fixed;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 99999;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .jezel-buttons-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2px;
        transition: all 0.3s ease;
        opacity: 1;
        transform: translateX(0);
    }
    
    .jezel-frontend-container.collapsed .jezel-buttons-wrapper {
        opacity: 0;
        transform: translateX(-100px);
        pointer-events: none;
        width: 0;
        overflow: hidden;
    }
    
    .jezel-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        background: rgba(168, 197, 230, 0.95);
        border: 1px solid rgba(75, 85, 99, 0.8);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1f2937;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s ease;
        backdrop-filter: blur(5px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .jezel-btn:hover {
        background: rgba(107, 114, 128, 0.95);
        transform: translateX(3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
    
    .jezel-toggle-btn {
        background: rgba(74, 74, 74, 0.9);
        color: white;
        width: 25px;
        height: 45px;
        border-radius: 0 6px 6px 0;
        padding: 0 5px;
    }
    
    .jezel-toggle-btn:hover {
        background: rgba(90, 90, 90, 0.95);
        transform: none;
    }
    
    .jezel-toggle-icon {
        transition: transform 0.3s ease;
        display: inline-block;
        font-size: 12px;
    }
    
    .jezel-frontend-container.collapsed .jezel-toggle-icon {
        transform: rotate(180deg);
    }
    
    .jezel-divider {
        height: 1px;
        background: rgba(75, 85, 99, 0.3);
        margin: 3px 5px;
    }
    
    .jezel-section-btn {
        background: rgba(168, 230, 168, 0.95);
    }
    
    .jezel-edit-btn {
        background: rgba(59, 130, 246, 0.95);
        color: white;
    }
    
    .jezel-edit-btn:hover {
        background: rgba(37, 99, 235, 0.95);
    }
    
    .jezel-wp-btn {
        background: rgba(33, 37, 41, 0.95);
        color: white;
    }
    
    .jezel-wp-btn:hover {
        background: rgba(0, 0, 0, 0.95);
    }
    
    .jezel-cache-btn {
        background: rgba(239, 68, 68, 0.95);
        color: white;
    }
    
    .jezel-cache-btn:hover {
        background: rgba(220, 38, 38, 0.95);
    }
    
    /* Hide on mobile */
    @media (max-width: 768px) {
        .jezel-frontend-container {
            display: none;
        }
    }
    
    /* Adjust position when admin bar is present */
    body.admin-bar .jezel-frontend-container {
        top: calc(50% + 16px);
    }
    
    /* Animation for widget appearance */
    @keyframes jezelSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50%) translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
    }
    
    .jezel-frontend-container {
        animation: jezelSlideIn 0.5s ease;
    }
    </style>
    
    <script>
    // Jezel Frontend Widget JavaScript
    (function() {
        // Scroll Functions
        window.jezelScrollToTop = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
        
        window.jezelScrollToBottom = function() {
            window.scrollTo({
                top: document.documentElement.scrollHeight,
                behavior: 'smooth'
            });
        };
        
        window.jezelScrollTo = function(percentage) {
            const targetPosition = document.documentElement.scrollHeight * percentage;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        };
        
        window.jezelJumpToSection = function(selector) {
            const element = document.querySelector(selector);
            if (element) {
                element.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
            }
        };
        
        window.toggleJezelWidget = function() {
            const widget = document.getElementById('jezel-frontend-widget');
            widget.classList.toggle('collapsed');
            
            // Save state to localStorage
            const isCollapsed = widget.classList.contains('collapsed');
            localStorage.setItem('jezelWidgetCollapsed', isCollapsed);
        };
        
        <?php if (function_exists('wp_cache_clear_cache')): ?>
        window.jezelClearCache = function() {
            if (confirm('Clear cache for this page?')) {
                // Create AJAX request to clear cache
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=jezel_clear_cache&nonce=<?php echo wp_create_nonce('jezel_cache_nonce'); ?>&post_id=<?php echo $post_id; ?>',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache cleared! Reloading page...');
                        location.reload();
                    } else {
                        alert('Failed to clear cache');
                    }
                });
            }
        };
        <?php endif; ?>
        
        // Restore collapsed state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const widget = document.getElementById('jezel-frontend-widget');
            const isCollapsed = localStorage.getItem('jezelWidgetCollapsed') === 'true';
            
            if (isCollapsed) {
                widget.classList.add('collapsed');
            }
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Alt+J to toggle widget
                if (e.altKey && e.key === 'j') {
                    e.preventDefault();
                    toggleJezelWidget();
                }
                
                // Alt+T for top
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    jezelScrollToTop();
                }
                
                // Alt+B for bottom
                if (e.altKey && e.key === 'b') {
                    e.preventDefault();
                    jezelScrollToBottom();
                }
            });
        });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'staircase_frontend_jezel_widget', 999);

// AJAX handler for cache clearing (if needed)
add_action('wp_ajax_jezel_clear_cache', 'staircase_jezel_clear_cache_handler');
function staircase_jezel_clear_cache_handler() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'jezel_cache_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    // Clear cache if function exists
    if (function_exists('wp_cache_clear_cache')) {
        global $file_prefix;
        wp_cache_clear_cache($file_prefix);
        wp_send_json_success('Cache cleared');
    } else {
        wp_send_json_error('Cache function not available');
    }
}

// End of Frontend Jezel Widget

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
    
    // Get hero background size setting from wp_pylons (default to 'cover' if not set)
    $hero_background_size = 'cover'; // Default value
    if ($pylon_data && !empty($pylon_data['hero_style_setting_background_size'])) {
        $hero_background_size = $pylon_data['hero_style_setting_background_size'];
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
    
    // Get hero overlay opacity (default to 50 if not set)
    $hero_overlay_opacity = 50; // Default value
    if ($pylon_data && isset($pylon_data['hero_overlay_opacity'])) {
        $hero_overlay_opacity = intval($pylon_data['hero_overlay_opacity']);
        // Ensure opacity is between 0 and 100
        $hero_overlay_opacity = max(0, min(100, $hero_overlay_opacity));
    }
    // Convert to decimal for CSS
    $hero_overlay_opacity_decimal = $hero_overlay_opacity / 100;
    
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
                        <a href="<?php echo esc_url($cherry_button_left_url); ?>" class="batman-hero-button batman-hero-button-left" aria-label="<?php echo esc_attr($cherry_button_left_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_left_text); ?></span>
                        </a>
                    <?php else: ?>
                        <button class="batman-hero-button batman-hero-button-left batman-hero-button-disabled" disabled aria-label="<?php echo esc_attr($cherry_button_left_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_left_text); ?></span>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($cherry_button_right_url): ?>
                        <a href="<?php echo esc_url($cherry_button_right_url); ?>" class="batman-hero-button batman-hero-button-right staircase_main_cta_button" aria-label="<?php echo esc_attr($cherry_button_right_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_right_text); ?></span>
                        </a>
                    <?php else: ?>
                        <button class="batman-hero-button batman-hero-button-right batman-hero-button-disabled staircase_main_cta_button" disabled aria-label="<?php echo esc_attr($cherry_button_right_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_right_text); ?></span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($cherry_phone_number_raw): ?>
                    <div class="phone_display_only_holder_div">
                        <span class="phone-display-only" aria-label="Phone number: <?php echo esc_attr($cherry_phone_number_formatted); ?>">
                            <span class="screen-reader-text">Phone: </span>
                            <?php echo esc_html($cherry_phone_number_formatted); ?>
                        </span>
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
        background-size: <?php echo esc_attr($hero_background_size); ?>;
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
        gap: 1rem;
        margin: 2rem 0 30px 0;
        flex-wrap: wrap;
        padding: 0 20px;
    }
    
    /* Quantum System Button Styles */
    .batman-hero-button {
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        min-height: 48px; /* Touch-friendly size from quantum */
    }
    
    .batman-hero-button svg {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
    }
    
    .batman-hero-button-left {
        background-color: white !important;
        color: #414449 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
        border: none !important;
    }
    
    .batman-hero-button-left:hover,
    .batman-hero-button-left:focus {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
        background-color: #f9f9f9 !important;
        color: #414449 !important;
    }
    
    .batman-hero-button-right {
        background-color: #23bb72 !important;
        color: white !important;
        border: 2px solid #23bb72 !important;
        box-shadow: none !important;
    }
    
    .batman-hero-button-right:hover,
    .batman-hero-button-right:focus {
        background-color: #fd9f1f !important;
        border-color: #fd9f1f !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(253, 159, 31, 0.4) !important;
        color: white !important;
    }
    
    .batman-hero-button-disabled {
        cursor: default;
        pointer-events: none;
        opacity: 0.6;
        background-color: #cccccc !important;
        color: #666666 !important;
        border: 2px solid #cccccc !important;
    }
    
    /* Keep old cherry-button classes for backward compatibility */
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
        background-color: white;
        color: #414449;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        border: none;
    }
    
    .cherry-button-left:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        background-color: #f9f9f9;
        color: #414449;
    }
    
    .cherry-button-right {
        background-color: #23bb72;
        color: white;
        border: 2px solid #23bb72;
    }
    
    .cherry-button-right:hover {
        background-color: #fd9f1f;
        border-color: #fd9f1f;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(253, 159, 31, 0.4);
        color: white;
    }
    
    /* Phone display only - not a button */
    .phone_display_only_holder_div {
        margin-top: 25px;
        text-align: center;
    }
    
    .phone-display-only {
        display: inline-block;
        font-size: 2rem; /* Enlarged from 1.8rem */
        font-weight: 700;
        color: white;
        /* Plain text - no link styling */
        cursor: default;
        user-select: text; /* Allow text selection */
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
        
        .phone-display-only {
            font-size: 1.7rem; /* Enlarged for tablet */
        }
    }
    
    @media (max-width: 480px) {
        .cherry-heading {
            font-size: 2rem;
        }
        
        .cherry-subheading {
            font-size: 1.1rem;
        }
        
        .phone-display-only {
            font-size: 1.5rem; /* Enlarged for mobile */
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
        padding: 40px 0;
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
            padding: 30px 0;
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
            padding: 25px 0;
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
        <div class="bilberry-inner">
            <?php if (get_post_type() === 'post' && has_post_thumbnail()): ?>
                <div class="bilberry-featured-image">
                    <?php the_post_thumbnail('large', array('alt' => esc_attr(get_the_title()), 'loading' => 'eager')); ?>
                </div>
            <?php endif; ?>

            <div class="bilberry-inner-body">
                <header class="bilberry-header">
                    <h1 class="bilberry-title"><?php the_title(); ?></h1>
                </header>

                <div class="bilberry-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </article>
    
    <?php
    // Check if this page has contactpage archetype in wp_pylons and render monica contact box
    $current_post_id = get_the_ID();
    global $wpdb;
    
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT pylon_archetype, enable_nectar_blog_feed, nectar_blog_feed_items_qty, nectar_blog_is_excerpt 
         FROM {$wpdb->prefix}pylons 
         WHERE rel_wp_post_id = %d",
        $current_post_id
    ), ARRAY_A);
    
    // If this is a contactpage archetype, render the monica contact box
    if ($pylon_data && $pylon_data['pylon_archetype'] === 'contactpage') {
        staircase_render_monica_contact_box();
    }
    
    // Check if nectar blog feed is enabled for this page
    if ($pylon_data && isset($pylon_data['enable_nectar_blog_feed']) && $pylon_data['enable_nectar_blog_feed'] == 1) {
        // Get number of items to display (default to 6 if null)
        $items_qty = !empty($pylon_data['nectar_blog_feed_items_qty']) ? 
                     intval($pylon_data['nectar_blog_feed_items_qty']) : 6;
        
        // Get excerpt flag (default to true if not set)
        $use_excerpt = isset($pylon_data['nectar_blog_is_excerpt']) ? 
                       (bool)$pylon_data['nectar_blog_is_excerpt'] : true;
        
        // Render the nectar blog feed
        staircase_render_nectar_blog_feed($items_qty, $use_excerpt);
    }
    ?>
    
    <style>
    .bilberry-article {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: #333;
    }

    .bilberry-inner {
        border: 1px solid #d0d0d0;
        border-radius: 20px;
        overflow: hidden;
    }

    .bilberry-inner-body {
        padding: 2rem;
    }

    .bilberry-featured-image {
        /* border-radius handled by overflow:hidden on .bilberry-inner */
        max-height: 480px;
        overflow: hidden;
    }

    .bilberry-featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
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
            staircase_render_cherry_full_template();
            break;
            
        case 'vibrantcashew':
            get_template_part('page-templates/vibrantcashew');
            break;

        case 'galleryberry':
            get_template_part('page-templates/galleryberry');
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
 * Render Vibrantberry Template (Custom HTML with Tailwind CDN - Full Width with WordPress Integration)
 */
function staircase_render_vibrantberry_template() {
    global $post;
    
    // Add Tailwind CSS CDN to wp_head
    add_action('wp_head', function() {
        echo '<script src="https://cdn.tailwindcss.com"></script>';
        echo '<style>
            /* Full-width content specific to vibrantberry template */
            .vibrantberry-content {
                width: 100vw !important;
                margin-left: calc(-50vw + 50%) !important;
                max-width: none !important;
                padding: 0 !important;
            }
            
            /* Ensure the main content area can accommodate full-width */
            .site-content,
            .entry-content,
            .content-area {
                overflow: visible !important;
            }
        </style>';
    });
    
    // Get WordPress header
    get_header();
    
    // Get the custom HTML content from post meta
    $custom_html = get_post_meta($post->ID, 'vibrantberry_content_ocean_1', true);
    
    // If no custom HTML is set, show a placeholder message with Tailwind classes
    if (empty($custom_html)) {
        $custom_html = '<div class="p-10 text-center bg-gray-50 m-5 rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Vibrantberry Template</h2>
            <p class="text-gray-600">No custom HTML content has been added yet. Edit this page to add your custom HTML content.</p>
        </div>';
    }
    
    ?>
    <main class="site-content">
        <div class="vibrantberry-content">
            <?php echo $custom_html; ?>
        </div>
    </main>
    <?php
    
    // Get WordPress footer
    get_footer();
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
 * Render Polyansk Service Categories Tiles box
 */
function staircase_render_polyansk_tiles_box() {
    if (class_exists('Polyansk_Service_Categories_Tiles')) {
        echo Polyansk_Service_Categories_Tiles::get_instance()->render();
    }
}

/**
 * Render Cherry Full Template
 */
function staircase_render_cherry_full_template() {
    global $wpdb, $post;

    // Fetch box_hide flags from wp_pylons for the current post (single query)
    $pylons_table = $wpdb->prefix . 'pylons';
    $box_hide_flags = $wpdb->get_row($wpdb->prepare(
        "SELECT
            batman_hero_box_hide,
            avg_rating_box_hide,
            derek_blog_post_meta_box_hide,
            chen_cards_box_hide,
            polyansk_tiles_box_hide,
            kristina_cta_box_instance_1_hide,
            content_bay_1_box_hide,
            content_bay_2_box_hide,
            content_lake_box_hide,
            content_sea_box_hide,
            osb_box_hide,
            reviews_box_hide,
            serena_faq_box_hide,
            nile_map_box_hide,
            kristina_cta_box_instance_2_hide,
            victoria_blog_box_hide,
            ocean_1_box_hide,
            ocean_2_box_hide,
            ocean_3_box_hide,
            brook_video_box_hide,
            olivia_auth_links_box_hide,
            ava_why_choose_us_box_hide,
            kendall_our_process_box_hide,
            sara_custom_html_box_hide,
            liz_pricing_box_hide,
            chenblock_card1_title,
            chenblock_card2_title,
            chenblock_card3_title
        FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post->ID
    ), ARRAY_A);

    // Helper: check if a box_hide flag is true (defaults to false if data missing)
    $is_hidden = function($flag_name) use ($box_hide_flags) {
        return !empty($box_hide_flags) && !empty($box_hide_flags[$flag_name]);
    };

    // Map box names (used in custom order JSON) to their hide flag column names
    // kristina_cta_box uses instance_1 hide in the custom order path (appears once there)
    $box_hide_map = array(
        'batman_hero_box'          => 'batman_hero_box_hide',
        'avg_rating_box'           => 'avg_rating_box_hide',
        'derek_blog_post_meta_box' => 'derek_blog_post_meta_box_hide',
        'chen_cards_box'           => 'chen_cards_box_hide',
        'polyansk_tiles_box'       => 'polyansk_tiles_box_hide',
        'kristina_cta_box'         => 'kristina_cta_box_instance_1_hide',
        'content_bay_1_box'        => 'content_bay_1_box_hide',
        'content_bay_2_box'        => 'content_bay_2_box_hide',
        'content_lake_box'         => 'content_lake_box_hide',
        'content_sea_box'          => 'content_sea_box_hide',
        'osb_box'                  => 'osb_box_hide',
        'reviewsbox'               => 'reviews_box_hide',
        'serena_faq_box'           => 'serena_faq_box_hide',
        'nile_map_box'             => 'nile_map_box_hide',
        'victoria_blog_box'        => 'victoria_blog_box_hide',
        'ocean1_box'               => 'ocean_1_box_hide',
        'ocean2_box'               => 'ocean_2_box_hide',
        'ocean3_box'               => 'ocean_3_box_hide',
        'brook_video_box'          => 'brook_video_box_hide',
        'olivia_authlinks_box'     => 'olivia_auth_links_box_hide',
        'ava_whychooseus_box'      => 'ava_why_choose_us_box_hide',
        'kendall_ourprocess_box'   => 'kendall_our_process_box_hide',
        'sara_customhtml_box'      => 'sara_custom_html_box_hide',
        'liz_pricing_box'          => 'liz_pricing_box_hide',
    );

    // Open semantic main element for accessibility and SEO
    ?>
    <main role="main">
    <?php

    // ALWAYS render batman_hero_box first - it's not subject to reordering
    if (!$is_hidden('batman_hero_box_hide')) {
        staircase_render_batman_hero_box();
    }

    // ALWAYS render avg_rating_box second (after hero, before chen cards)
    if (!$is_hidden('avg_rating_box_hide')) {
        staircase_render_avg_rating_box();
    }

    // Check for custom box ordering for remaining boxes
    $box_orders_table = $wpdb->prefix . 'box_orders';

    $custom_order = $wpdb->get_row($wpdb->prepare(
        "SELECT box_order_json FROM {$box_orders_table} WHERE rel_post_id = %d AND is_active = 1",
        $post->ID
    ));


    if ($custom_order && !empty($custom_order->box_order_json)) {
        // Use custom box ordering
        $box_order = json_decode($custom_order->box_order_json, true);
        if ($box_order && is_array($box_order)) {
            // Sort boxes by their order value
            asort($box_order);

            // Define mapping from box names to functions (batman_hero_box and baynar boxes removed)
            $box_functions = array(
                'derek_blog_post_meta_box' => 'staircase_render_derek_blog_post_meta_box',
                'chen_cards_box' => 'staircase_render_chen_cards_box',
                'plain_post_content' => 'staircase_render_plain_post_content',
                'osb_box' => 'staircase_render_osb_box',
                'reviewsbox' => 'staircase_render_reviewsbox',
                'serena_faq_box' => 'staircase_render_serena_faq_box',
                'nile_map_box' => 'staircase_render_nile_map_box',
                'kristina_cta_box' => 'staircase_render_kristina_cta_box',
                'victoria_blog_box' => 'staircase_render_victoria_blog_box',
                'content_bay_1_box' => 'staircase_render_content_bay_1_box',
                'content_bay_2_box' => 'staircase_render_content_bay_2_box',
                'content_lake_box' => 'staircase_render_content_lake_box',
                'content_sea_box' => 'staircase_render_content_sea_box',
                'ocean1_box' => 'staircase_render_ocean1_box',
                'ocean2_box' => 'staircase_render_ocean2_box',
                'ocean3_box' => 'staircase_render_ocean3_box',
                'brook_video_box' => 'staircase_render_brook_video_box',
                'olivia_authlinks_box' => 'staircase_render_olivia_authlinks_box',
                'ava_whychooseus_box' => 'staircase_render_ava_whychooseus_box',
                'kendall_ourprocess_box' => 'staircase_render_kendall_ourprocess_box',
                'sara_customhtml_box' => 'staircase_render_sara_customhtml_box',
                'liz_pricing_box' => 'staircase_render_liz_pricing_box',
                'polyansk_tiles_box' => 'staircase_render_polyansk_tiles_box'
            );

            // Always render derek_blog_post_meta_box first (after hero) — unless hidden
            if (!$is_hidden('derek_blog_post_meta_box_hide')) {
                staircase_render_derek_blog_post_meta_box();
            }

            // Render boxes in custom order
            foreach ($box_order as $box_name => $order) {
                // Skip batman_hero_box and avg_rating_box if they exist in the JSON (for backward compatibility)
                // These boxes are always rendered at the top in fixed positions
                if ($box_name === 'batman_hero_box') {
                    continue;
                }
                if ($box_name === 'avg_rating_box') {
                    continue;
                }

                if (isset($box_functions[$box_name]) && function_exists($box_functions[$box_name])) {
                    // Skip derek_blog_post_meta_box as it's already rendered
                    if ($box_name !== 'derek_blog_post_meta_box') {
                        // Check box_hide flag before rendering
                        $hide_flag = isset($box_hide_map[$box_name]) ? $box_hide_map[$box_name] : null;
                        if ($hide_flag && $is_hidden($hide_flag)) {
                            continue;
                        }
                        call_user_func($box_functions[$box_name]);
                    } else {
                    }
                } else {
                }
            }

            // Close semantic main element before returning
            ?>
            </main>
            <?php

            return;
        } else {
        }
    } else {
    }

    // Use default hardcoded ordering if no custom order exists
    // Batman hero box and avg_rating_box already rendered at the top

    // Add blog post meta information for posts only
    if (!$is_hidden('derek_blog_post_meta_box_hide')) {
        staircase_render_derek_blog_post_meta_box();
    }

    // Cherry template includes chen cards before content
    if (!$is_hidden('chen_cards_box_hide')) {
        staircase_render_chen_cards_box();
    }

    // Polyansk service categories tiles (shown if pylon flag is true)
    if (!$is_hidden('polyansk_tiles_box_hide')) {
        staircase_render_polyansk_tiles_box();
    }

    // Kristina CTA Box instance 1 (before content bays)
    // Only show if chen cards are also showing (not hidden and has content)
    $chen_cards_will_show = !$is_hidden('chen_cards_box_hide')
        && (!empty($box_hide_flags['chenblock_card1_title'])
            || !empty($box_hide_flags['chenblock_card2_title'])
            || !empty($box_hide_flags['chenblock_card3_title']));
    if (!$is_hidden('kristina_cta_box_instance_1_hide') && $chen_cards_will_show) {
        staircase_render_kristina_cta_box();
    }

    if (!$is_hidden('content_bay_1_box_hide')) {
        staircase_render_content_bay_1_box();
    }
    if (!$is_hidden('content_bay_2_box_hide')) {
        staircase_render_content_bay_2_box();
    }

    // Render content_lake and content_sea boxes after bay boxes
    if (!$is_hidden('content_lake_box_hide')) {
        staircase_render_content_lake_box();
    }
    if (!$is_hidden('content_sea_box_hide')) {
        staircase_render_content_sea_box();
    }

    // Cherry template doesn't need WordPress post_content (uses pylons content fields instead)
    // staircase_render_plain_post_content(); // Removed - not needed for cherry template

    // Cherry template includes OSB box
    if (!$is_hidden('osb_box_hide')) {
        staircase_render_osb_box();
    }

    // Cherry template includes reviewsbox before FAQ
    if (!$is_hidden('reviews_box_hide')) {
        staircase_render_reviewsbox();
    }

    // Cherry template includes all the boxes at the end
    if (!$is_hidden('serena_faq_box_hide')) {
        staircase_render_serena_faq_box();
    }
    if (!$is_hidden('nile_map_box_hide')) {
        staircase_render_nile_map_box();
    }
    // Kristina CTA Box instance 2
    if (!$is_hidden('kristina_cta_box_instance_2_hide')) {
        staircase_render_kristina_cta_box();
    }
    if (!$is_hidden('victoria_blog_box_hide')) {
        staircase_render_victoria_blog_box();
    }

    // Additional ocean and new box functions
    if (!$is_hidden('ocean_1_box_hide')) {
        staircase_render_ocean1_box();
    }
    if (!$is_hidden('ocean_2_box_hide')) {
        staircase_render_ocean2_box();
    }
    if (!$is_hidden('ocean_3_box_hide')) {
        staircase_render_ocean3_box();
    }
    if (!$is_hidden('brook_video_box_hide')) {
        staircase_render_brook_video_box();
    }
    if (!$is_hidden('olivia_auth_links_box_hide')) {
        staircase_render_olivia_authlinks_box();
    }
    if (!$is_hidden('ava_why_choose_us_box_hide')) {
        staircase_render_ava_whychooseus_box();
    }
    if (!$is_hidden('kendall_our_process_box_hide')) {
        staircase_render_kendall_ourprocess_box();
    }
    if (!$is_hidden('sara_custom_html_box_hide')) {
        staircase_render_sara_customhtml_box();
    }
    if (!$is_hidden('liz_pricing_box_hide')) {
        staircase_render_liz_pricing_box();
    }

    // Close semantic main element
    ?>
    </main>
    <?php
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
    
    // First check if a pylons entry exists for this post
    $pylon_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d", 
        $post_id
    ));
    
    // If no pylons entry exists, default to bilberry (minimal template)
    if (!$pylon_exists) {
        return 'bilberry';
    }
    
    // Get template from wp_pylons table
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
        $normalized = staircase_normalize_template_name($pylon_template);
        return $normalized;
    }
    
    // If pylons entry exists but no template specified, return cherry as default (full-featured)
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
        padding: 40px 0;
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
            padding: 30px 0;
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
            padding: 25px 0;
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

// Add dynamic CTA button colors and footer styles
function staircase_cta_button_dynamic_styles() {
    $cta_bg_color = get_option('staircase_cta_bg_color', '#23bb73');
    $footer_link_color = get_option('staircase_footer_default_link_color', '#fdfdfd');
    $footer_logo_bg_color = get_option('staircase_footer_logo_bg_color', '#fdfdfd');
    $footer_logo_border_radius = get_option('staircase_footer_logo_border_radius', '8'); // Default 8px border radius
    ?>
    <style>
    /* Dynamic CTA Button Colors */
    .staircase_main_cta_button {
        background-color: <?php echo esc_attr($cta_bg_color); ?> !important;
        color: white !important;
    }
    
    /* Header phone button with dynamic color */
    .header-phone-button.staircase_main_cta_button {
        background-color: <?php echo esc_attr($cta_bg_color); ?> !important;
    }
    
    /* Hero Call Us Now button with dynamic color */
    .batman-hero-button-right.staircase_main_cta_button {
        background-color: <?php echo esc_attr($cta_bg_color); ?> !important;
        border-color: <?php echo esc_attr($cta_bg_color); ?> !important;
    }
    
    /* Kristina CTA button with dynamic color */
    .kristina-cta-button.staircase_main_cta_button {
        background-color: <?php echo esc_attr($cta_bg_color); ?> !important;
    }
    
    /* Footer phone button with dynamic color */
    .footer-phone-button.staircase_main_cta_button {
        background-color: <?php echo esc_attr($cta_bg_color); ?> !important;
    }
    
    /* Hover states remain orange as per quantum styling */
    .staircase_main_cta_button:hover {
        background-color: #fd9f1f !important;
        border-color: #fd9f1f !important;
    }
    
    /* Dynamic Footer Link Colors */
    footer a,
    .site-footer a,
    #footer a,
    .footer a {
        color: <?php echo esc_attr($footer_link_color); ?> !important;
    }
    
    footer a:hover,
    .site-footer a:hover,
    #footer a:hover,
    .footer a:hover {
        opacity: 0.8;
    }
    
    /* Footer Logo Wrapper Styles */
    .footer-logo {
        margin: 0; /* Remove any margin from container */
        padding: 0;
    }
    
    .footer-logo-wrapper {
        display: inline-block;
        background-color: <?php echo esc_attr($footer_logo_bg_color); ?>;
        border-radius: <?php echo intval($footer_logo_border_radius); ?>px;
        overflow: hidden;
        line-height: 0; /* Remove spacing below image */
    }
    
    .footer-logo-wrapper img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 0; /* Remove any margin */
        border-radius: <?php echo intval($footer_logo_border_radius); ?>px; /* Apply to image as well for consistency */
    }
    </style>
    <?php
}
add_action('wp_head', 'staircase_cta_button_dynamic_styles');

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
        'vibrantberry' => 'Vibrantberry (Custom HTML)',
        'vibrantcashew' => 'Vibrantcashew (Full HTML Expanse)',
        'galleryberry' => 'Galleryberry (Dark Project Gallery)'
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
    
    $templates = staircase_get_page_templates();
    
    // Get current value from wp_pylons table ONLY
    $selected_template = $wpdb->get_var($wpdb->prepare(
        "SELECT staircase_page_template_desired 
         FROM {$wpdb->prefix}pylons 
         WHERE rel_wp_post_id = %d", 
        $post->ID
    ));
    
    // If no template set in pylons, default to empty (will show first option in dropdown)
    if (empty($selected_template)) {
        $selected_template = '';
    }
    
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
               value="<?php echo esc_attr($selected_template ?: ''); ?>" 
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
            • <strong>Vibrantberry:</strong> Custom HTML (vibe coded)<br>
            • <strong>Standard:</strong> Traditional page layout<br>
            • <strong>Content Only:</strong> Just page content<br>
            • <strong>Sections Builder:</strong> Custom sections
        </p>
    </div>
    
    <!-- Cherry and zarl card admin interface removed - no longer using postmeta system -->
    
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
    
    <?php if ($selected_template === 'vibrantcashew'): ?>
        <div style="margin-top: 20px; padding: 20px; border: 2px solid #8B4513; border-radius: 8px; background: #FFF8F0;">
            <h4 style="margin: 0 0 15px 0; color: #8B4513; font-weight: bold; font-size: 18px;">
                🥜 Vibrantcashew Template - Full HTML Expanse
            </h4>
            
            <div style="margin-bottom: 15px;">
                <label for="cashew_html_expanse" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
                    HTML Content (cashew_html_expanse):
                </label>
                <?php 
                    // Get existing content from pylons table
                    global $wpdb;
                    $cashew_content = $wpdb->get_var($wpdb->prepare(
                        "SELECT cashew_html_expanse FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
                        $post->ID
                    ));
                ?>
                <textarea id="cashew_html_expanse" name="cashew_html_expanse" 
                          style="width: 100%; height: 300px; font-family: 'Courier New', monospace; background-color: #2d3748; color: #e2e8f0; padding: 15px; border: 1px solid #4a5568; border-radius: 4px; line-height: 1.5;"
                          placeholder="Enter your full HTML content here..."><?php echo esc_textarea($cashew_content); ?></textarea>
            </div>
            
            <p style="font-size: 13px; color: #666; margin-top: 10px; padding: 10px; background: #fff; border-left: 3px solid #8B4513;">
                <strong>Note:</strong> This HTML replaces everything between header and footer. 
                Perfect for custom landing pages, full-width designs, or embedded content.
                <br><span style="color: #999;">Script tags will be removed for security.</span>
            </p>
            
            <!-- Character counter -->
            <div style="margin-top: 10px; color: #666; font-size: 12px;">
                <span id="cashew_char_count">0</span> characters
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const textarea = document.getElementById('cashew_html_expanse');
                    const counter = document.getElementById('cashew_char_count');
                    
                    if (textarea && counter) {
                        function updateCount() {
                            counter.textContent = textarea.value.length.toLocaleString();
                        }
                        textarea.addEventListener('input', updateCount);
                        updateCount();
                    }
                });
            </script>
        </div>
    <?php endif; ?>
    
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('staircase_page_template');
        const pylonRawInput = document.getElementById('staircase_pylon_raw_template');
        
        // Template value mappings to raw format
        const templateMappings = {
            'homepage-cherry': 'cherry',
            'content-only': 'content_only',
            'vibrantberry': 'vibrantberry'
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
    
    // Cherry fields and zarl cards removed - no longer using postmeta system
    
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
    
    // Save Vibrantcashew HTML field to pylons table
    if (isset($_POST['cashew_html_expanse'])) {
        global $wpdb;
        
        // Get the content and strip script tags for security
        $cashew_html = $_POST['cashew_html_expanse'];
        $cashew_html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $cashew_html);
        
        // Check if pylons entry exists
        $pylon_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        if ($pylon_exists) {
            // Update existing pylons entry with cashew content
            $result = $wpdb->update(
                $wpdb->prefix . 'pylons',
                array('cashew_html_expanse' => $cashew_html),
                array('rel_wp_post_id' => $post_id),
                array('%s'),
                array('%d')
            );
            
            if ($result !== false) {
                error_log("STAIRCASE SAVE: Updated cashew_html_expanse for post {$post_id}");
            } else {
                error_log("STAIRCASE SAVE ERROR: Failed to update cashew_html_expanse for post {$post_id}: " . $wpdb->last_error);
            }
        } else {
            // If no pylons entry exists yet, log a warning
            // The main staircase save should have created it already
            error_log("STAIRCASE SAVE WARNING: No pylons entry found for post {$post_id} when saving cashew_html_expanse");
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
        'Settings / Logo',
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
    
    // Add Contact Forms submenu
    add_submenu_page(
        'staircase-theme',
        'Contact Form Options',
        'Contact Forms',
        'manage_options',
        'staircase-contact-forms',
        'staircase_contact_form_options_page'
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
        update_option('staircase_use_tpcom_nav_styles', isset($_POST['use_tpcom_nav_styles']));
        
        // Save CTA button color
        $cta_bg_color = sanitize_hex_color($_POST['staircase_cta_bg_color']);
        if ($cta_bg_color) {
            update_option('staircase_cta_bg_color', $cta_bg_color);
        }
        
        // Save footer link color
        $footer_link_color = sanitize_hex_color($_POST['staircase_footer_default_link_color']);
        if ($footer_link_color) {
            update_option('staircase_footer_default_link_color', $footer_link_color);
        }
        
        // Save footer logo bg color
        $footer_logo_bg_color = sanitize_hex_color($_POST['staircase_footer_logo_bg_color']);
        if ($footer_logo_bg_color) {
            update_option('staircase_footer_logo_bg_color', $footer_logo_bg_color);
        }
        
        // Save footer logo border radius
        $footer_logo_border_radius = intval($_POST['staircase_footer_logo_border_radius']);
        if ($footer_logo_border_radius >= 0) {
            update_option('staircase_footer_logo_border_radius', $footer_logo_border_radius);
        }
        
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
    $use_tpcom_nav_styles = get_option('staircase_use_tpcom_nav_styles', false);
    $cta_bg_color = get_option('staircase_cta_bg_color', '#23bb73'); // Default green color
    $footer_link_color = get_option('staircase_footer_default_link_color', '#fdfdfd'); // Default light color
    $footer_logo_bg_color = get_option('staircase_footer_logo_bg_color', '#fdfdfd'); // Default light gray background
    $footer_logo_border_radius = get_option('staircase_footer_logo_border_radius', '8'); // Default 8px border radius
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
                    <h2>Navigation Styles</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Navigation Menu Styling</th>
                            <td>
                                <label for="use_tpcom_nav_styles">
                                    <input name="use_tpcom_nav_styles" type="checkbox" id="use_tpcom_nav_styles" value="1" <?php checked($use_tpcom_nav_styles); ?>>
                                    Use TPCom Extracted Nav Styles
                                </label>
                                <p class="description">Apply extracted navigation menu text styles with enhanced typography and spacing</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="settings-section">
                    <h2>Main CTA Button Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">BG Color</th>
                            <td>
                                <input type="color" name="staircase_cta_bg_color" id="staircase_cta_bg_color" value="<?php echo esc_attr($cta_bg_color); ?>" />
                                <input type="text" id="staircase_cta_bg_color_text" value="<?php echo esc_attr($cta_bg_color); ?>" class="regular-text" style="margin-left: 10px;" readonly />
                                <button type="button" id="reset_cta_color" class="button" style="margin-left: 10px;">Reset to Default</button>
                                <p class="description">Set the background color for main CTA buttons (header phone, hero Call Us Now, CTA section, footer phone). Default: <code>#23bb73</code></p>
                                <p class="description" style="margin-top: 5px;"><strong>Affected buttons:</strong> Header phone button, Hero "Call Us Now" button, CTA section button, Footer phone button</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="settings-section">
                    <h2>Footer Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Footer Link Color</th>
                            <td>
                                <input type="color" name="staircase_footer_default_link_color" id="staircase_footer_default_link_color" value="<?php echo esc_attr($footer_link_color); ?>" />
                                <input type="text" id="staircase_footer_default_link_color_text" value="<?php echo esc_attr($footer_link_color); ?>" class="regular-text" style="margin-left: 10px;" readonly />
                                <button type="button" id="reset_footer_link_color" class="button" style="margin-left: 10px;">Reset to Default</button>
                                <p class="description">Set the default color for all links (&lt;a&gt; elements) in the footer. Default: <code>#fdfdfd</code></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Footer Logo Background Color</th>
                            <td>
                                <input type="color" name="staircase_footer_logo_bg_color" id="staircase_footer_logo_bg_color" value="<?php echo esc_attr($footer_logo_bg_color); ?>" />
                                <input type="text" id="staircase_footer_logo_bg_color_text" value="<?php echo esc_attr($footer_logo_bg_color); ?>" class="regular-text" style="margin-left: 10px;" readonly />
                                <button type="button" id="reset_footer_logo_bg_color" class="button" style="margin-left: 10px;">Reset to Default</button>
                                <p class="description">Set the background color behind the footer logo. Useful for logos with transparent backgrounds. Default: <code>#fdfdfd</code></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Footer Logo Border Radius</th>
                            <td>
                                <input type="number" name="staircase_footer_logo_border_radius" id="staircase_footer_logo_border_radius" value="<?php echo esc_attr($footer_logo_border_radius); ?>" min="0" max="50" class="small-text" />
                                <span style="margin-left: 5px;">pixels</span>
                                <button type="button" id="reset_footer_logo_border_radius" class="button" style="margin-left: 10px;">Reset to Default</button>
                                <p class="description">Set the border radius for rounded corners on the footer logo. Default: <code>8</code> pixels</p>
                                <p class="description" style="margin-top: 5px;">Use 0 for square corners, or increase for more rounded corners (max 50px).</p>
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
            var checkMedia = setInterval(function() {
                if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
                    clearInterval(checkMedia);
                }
            }, 500);
        }
        
        // Color picker functionality
        $('#staircase_cta_bg_color').on('input', function() {
            $('#staircase_cta_bg_color_text').val($(this).val());
        });
        
        $('#staircase_cta_bg_color_text').on('input', function() {
            var color = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                $('#staircase_cta_bg_color').val(color);
            }
        });
        
        $('#reset_cta_color').on('click', function() {
            $('#staircase_cta_bg_color').val('#23bb73');
            $('#staircase_cta_bg_color_text').val('#23bb73');
        });
        
        // Footer link color picker functionality
        $('#staircase_footer_default_link_color').on('input', function() {
            $('#staircase_footer_default_link_color_text').val($(this).val());
        });
        
        $('#staircase_footer_default_link_color_text').on('input', function() {
            var color = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                $('#staircase_footer_default_link_color').val(color);
            }
        });
        
        $('#reset_footer_link_color').on('click', function() {
            $('#staircase_footer_default_link_color').val('#fdfdfd');
            $('#staircase_footer_default_link_color_text').val('#fdfdfd');
        });
        
        // Footer logo bg color picker functionality
        $('#staircase_footer_logo_bg_color').on('input', function() {
            $('#staircase_footer_logo_bg_color_text').val($(this).val());
        });
        
        $('#staircase_footer_logo_bg_color_text').on('input', function() {
            var color = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                $('#staircase_footer_logo_bg_color').val(color);
            }
        });
        
        $('#reset_footer_logo_bg_color').on('click', function() {
            $('#staircase_footer_logo_bg_color').val('#fdfdfd');
            $('#staircase_footer_logo_bg_color_text').val('#fdfdfd');
        });
        
        // Footer logo border radius reset
        $('#reset_footer_logo_border_radius').on('click', function() {
            $('#staircase_footer_logo_border_radius').val('8');
        });
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
    global $wpdb;
    
    // Handle form submission
    if (isset($_POST['submit']) && check_admin_referer('zaramax_footer', 'zaramax_footer_nonce')) {
        // Save footer system choice
        update_option('zaramax_use_custom_footer', isset($_POST['use_custom_footer']));

        // Save footer2 custom styles toggle
        update_option('zh_ft2_custom_styles_enabled', isset($_POST['zh_ft2_custom_styles_enabled']) ? '1' : '0');

        // Save company info table label cell bg color
        if (isset($_POST['zh_agnostic_cit_label_bg_color'])) {
            $submitted_color = sanitize_hex_color(wp_unslash($_POST['zh_agnostic_cit_label_bg_color']));
            if ($submitted_color) {
                update_option('zh_agnostic_cit_label_bg_color', $submitted_color);
            }
        }
        // Save company info table label cell text color
        if (isset($_POST['zh_agnostic_cit_label_text_color'])) {
            $submitted_text_color = sanitize_hex_color(wp_unslash($_POST['zh_agnostic_cit_label_text_color']));
            if ($submitted_text_color) {
                update_option('zh_agnostic_cit_label_text_color', $submitted_text_color);
            }
        }
        
        // TEMPORARY: Save footer blurb to WordPress options for testing
        // TODO: Remove this when proper database column is created
        if (isset($_POST['footer_blurb'])) {
            // Use wp_unslash to remove magic quotes, then trim whitespace
            // This preserves shortcodes without adding unwanted backslashes
            $footer_blurb = trim(wp_unslash($_POST['footer_blurb']));
            update_option('zaramax_temp_footer_blurb', $footer_blurb);
        }
        
        // Save legacy footer settings (WordPress Options)
        // Use wp_unslash to preserve shortcodes without adding backslashes
        update_option('zaramax_footer_box2_content', wp_unslash($_POST['footer_box2_content']));
        update_option('zaramax_footer_box3_content', wp_unslash($_POST['footer_box3_content']));
        update_option('zaramax_footer_map_heading', sanitize_text_field($_POST['footer_map_heading']));
        // Use wp_unslash to preserve shortcodes without adding backslashes
        update_option('zaramax_footer_map_location', wp_unslash($_POST['footer_map_location']));
        // Toggle to hide box 4 (map widget) and reflow boxes 1-3 across the grid
        update_option('zaramax_footer_hide_box4_map', isset($_POST['zaramax_footer_hide_box4_map']) ? '1' : '0');
        update_option('zaramax_footer_hide_disclaimer', isset($_POST['footer_hide_disclaimer']) ? '1' : '0');
        update_option('zaramax_footer_disclaimer', wp_unslash($_POST['footer_disclaimer']));
        update_option('zaramax_footer_legal_links', wp_unslash($_POST['footer_legal_links']));
        
        // Save new system footer settings (wp_zen_sitespren table)
        $zen_table = $wpdb->prefix . 'zen_sitespren';
        
        // Check if table exists and has our new columns
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$zen_table}'");
        if ($table_exists) {
            // Check if row with wppma_id=1 exists
            $row_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$zen_table} WHERE wppma_id = 1");
            
            if ($row_exists) {
                // Update the row
                $update_data = array(
                    'footer_disclaimer' => wp_unslash($_POST['new_footer_disclaimer']),
                    'hide_footer_disclaimer' => isset($_POST['new_hide_footer_disclaimer']) ? 1 : 0,
                    'footer_legal_links' => wp_unslash($_POST['new_footer_legal_links']),
                    'hide_footer_legal_links' => isset($_POST['new_hide_footer_legal_links']) ? 1 : 0
                );
                
                $wpdb->update(
                    $zen_table,
                    $update_data,
                    array('wppma_id' => 1)
                );
            }
        }
        
        echo '<div class="notice notice-success"><p>Footer settings saved successfully!</p></div>';
    }
    
    // Get current legacy settings (WordPress Options)
    $use_custom_footer = get_option('zaramax_use_custom_footer', false);
    $zh_ft2_custom_styles_enabled = get_option('zh_ft2_custom_styles_enabled', '0');
    $zh_agnostic_cit_label_bg_color   = sanitize_hex_color(get_option('zh_agnostic_cit_label_bg_color',   '#101722')) ?: '#101722';
    $zh_agnostic_cit_label_text_color = sanitize_hex_color(get_option('zh_agnostic_cit_label_text_color', '#ffffff')) ?: '#ffffff';
    $footer_box2_content = get_option('zaramax_footer_box2_content', '');
    $footer_box3_content = get_option('zaramax_footer_box3_content', '');
    $footer_map_heading = get_option('zaramax_footer_map_heading', '');
    $footer_map_location = get_option('zaramax_footer_map_location', '');
    $zaramax_footer_hide_box4_map = get_option('zaramax_footer_hide_box4_map', '0');
    $footer_hide_disclaimer = get_option('zaramax_footer_hide_disclaimer', '0');
    $footer_disclaimer = get_option('zaramax_footer_disclaimer', '');
    $footer_legal_links = get_option('zaramax_footer_legal_links', '');
    
    // TEMPORARY: Get footer blurb from WordPress options for testing
    // TODO: Remove this when proper database column is created
    $footer_blurb = get_option('zaramax_temp_footer_blurb', '');
    
    // Get new system settings from wp_zen_sitespren
    $zen_table = $wpdb->prefix . 'zen_sitespren';
    $new_footer_disclaimer = '';
    $new_hide_footer_disclaimer = 0;
    $new_footer_legal_links = '';
    $new_hide_footer_legal_links = 0;
    
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$zen_table}'");
    if ($table_exists) {
        $zen_data = $wpdb->get_row("SELECT footer_disclaimer, hide_footer_disclaimer, footer_legal_links, hide_footer_legal_links FROM {$zen_table} WHERE wppma_id = 1", ARRAY_A);
        if ($zen_data) {
            $new_footer_disclaimer = $zen_data['footer_disclaimer'] ?? '';
            $new_hide_footer_disclaimer = $zen_data['hide_footer_disclaimer'] ?? 0;
            $new_footer_legal_links = $zen_data['footer_legal_links'] ?? '';
            $new_hide_footer_legal_links = $zen_data['hide_footer_legal_links'] ?? 0;
        }
    }
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

            <!-- ── Footer 2: Custom Styles Toggle ── -->
            <div class="zh_ft2_admin_toggle_block" style="background: #1b2a3d; border: 2px solid #b1250d; border-radius: 8px; padding: 18px 24px; margin-bottom: 28px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 0 0 auto;">
                    <span style="font-size: 18px;">🎨</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 4px;">(if using footer2 system only)</strong>
                    <span style="color: #d0d8e0; font-size: 13px;">Enable custom styles — <code style="background: rgba(255,255,255,0.1); color: #fde9e3; padding: 1px 6px; border-radius: 3px;">zh_ft2_custom_7423.css</code></span>
                    <div style="color: #7a8a98; font-size: 11px; margin-top: 4px;">When disabled, the stylesheet is not loaded at all — zero impact on the page.</div>
                </div>
                <div style="flex: 0 0 auto;">
                    <label class="zh_ft2_toggle_switch" style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;" title="Toggle footer2 custom styles">
                        <input type="checkbox" name="zh_ft2_custom_styles_enabled" value="1" <?php checked($zh_ft2_custom_styles_enabled, '1'); ?>
                            style="opacity: 0; width: 0; height: 0; position: absolute;"
                            onchange="document.getElementById('zh_ft2_toggle_label').textContent = this.checked ? 'ON' : 'OFF';">
                        <span class="zh_ft2_toggle_slider" style="
                            position: absolute; cursor: pointer; inset: 0;
                            background-color: <?php echo $zh_ft2_custom_styles_enabled === '1' ? '#b1250d' : '#4a5568'; ?>;
                            transition: background-color 0.25s;
                            border-radius: 28px;
                        "></span>
                        <span style="
                            position: absolute; top: 4px; left: <?php echo $zh_ft2_custom_styles_enabled === '1' ? '28px' : '4px'; ?>;
                            width: 20px; height: 20px;
                            background: #fff; border-radius: 50%;
                            transition: left 0.25s;
                            pointer-events: none;
                        " class="zh_ft2_toggle_knob"></span>
                    </label>
                </div>
                <div style="flex: 0 0 auto; min-width: 36px;">
                    <strong id="zh_ft2_toggle_label" style="color: <?php echo $zh_ft2_custom_styles_enabled === '1' ? '#e84b2a' : '#7a8a98'; ?>; font-size: 13px;">
                        <?php echo $zh_ft2_custom_styles_enabled === '1' ? 'ON' : 'OFF'; ?>
                    </strong>
                </div>
            </div>
            <script>
            (function() {
                var cb = document.querySelector('input[name="zh_ft2_custom_styles_enabled"]');
                if (!cb) return;
                cb.addEventListener('change', function() {
                    var slider = cb.parentElement.querySelector('.zh_ft2_toggle_slider');
                    var knob   = cb.parentElement.querySelector('.zh_ft2_toggle_knob');
                    var label  = document.getElementById('zh_ft2_toggle_label');
                    if (cb.checked) {
                        slider.style.backgroundColor = '#b1250d';
                        knob.style.left = '28px';
                        label.style.color = '#e84b2a';
                        label.textContent = 'ON';
                    } else {
                        slider.style.backgroundColor = '#4a5568';
                        knob.style.left = '4px';
                        label.style.color = '#7a8a98';
                        label.textContent = 'OFF';
                    }
                });
            })();
            </script>
            <!-- ── /Footer 2: Custom Styles Toggle ── -->

            <!-- ── Company Info Table: Label Cell Color ── -->
            <div class="zh_ft2_admin_toggle_block" style="background: #1b2a3d; border: 2px solid #4a5568; border-radius: 8px; padding: 18px 24px; margin-bottom: 28px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 0 0 auto;">
                    <span style="font-size: 18px;">🎨</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 4px;">(footer2 only) modify company info table label cells bg color</strong>
                    <span style="color: #d0d8e0; font-size: 13px;">Sets background of rows 1, 3, 5 in the Box 3 company info table — <code style="background: rgba(255,255,255,0.1); color: #fde9e3; padding: 1px 6px; border-radius: 3px;">zh_agnostic_cit_label_cell</code></span>
                    <div style="color: #7a8a98; font-size: 11px; margin-top: 4px;">Default: <code style="color: #7a8a98;">#101722</code> — injected via <code style="color: #7a8a98;">wp_add_inline_style()</code>, no extra HTTP request.</div>
                </div>
                <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 12px;">
                    <label for="zh_agnostic_cit_color_picker" style="color: #d0d8e0; font-size: 13px; font-weight: 600;">Color:</label>
                    <input
                        type="color"
                        id="zh_agnostic_cit_color_picker"
                        name="zh_agnostic_cit_label_bg_color"
                        value="<?php echo esc_attr($zh_agnostic_cit_label_bg_color); ?>"
                        style="width: 48px; height: 36px; padding: 2px; border: 2px solid #4a5568; border-radius: 6px; background: transparent; cursor: pointer;"
                        title="Pick label cell background color"
                    >
                    <code id="zh_agnostic_cit_color_hex" style="color: #e0d8d2; font-size: 12px; min-width: 60px;"><?php echo esc_html($zh_agnostic_cit_label_bg_color); ?></code>
                    <!-- Preset pill: dark default -->
                    <button type="button"
                        onclick="zh_cit_set_color('#101722')"
                        style="display:inline-flex; align-items:center; gap:6px; background:#101722; color:#ffffff; border:2px solid #4a5568; border-radius:20px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;"
                        title="Apply dark default #101722">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#101722;border:1px solid #4a5568;flex-shrink:0;"></span>
                        #101722 (dark)
                    </button>
                    <!-- Preset pill: lighter -->
                    <button type="button"
                        onclick="zh_cit_set_color('#253649')"
                        style="display:inline-flex; align-items:center; gap:6px; background:#253649; color:#ffffff; border:2px solid #4a5568; border-radius:20px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;"
                        title="Apply lighter blue #253649">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#253649;border:1px solid #4a5568;flex-shrink:0;"></span>
                        #253649 (lighter)
                    </button>
                </div>
            </div>
            <script>
            function zh_cit_set_color(val) {
                var picker = document.getElementById('zh_agnostic_cit_color_picker');
                var hex    = document.getElementById('zh_agnostic_cit_color_hex');
                if (!picker || !hex) return;
                picker.value    = val;
                hex.textContent = val;
            }
            (function() {
                var picker = document.getElementById('zh_agnostic_cit_color_picker');
                var hex    = document.getElementById('zh_agnostic_cit_color_hex');
                if (!picker || !hex) return;
                picker.addEventListener('input', function() {
                    hex.textContent = picker.value;
                });
            })();
            </script>
            <!-- ── Company Info Table: Label Cell Text Color ── -->
            <div class="zh_ft2_admin_toggle_block" style="background: #1b2a3d; border: 2px solid #4a5568; border-radius: 8px; padding: 18px 24px; margin-bottom: 28px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 0 0 auto;">
                    <span style="font-size: 18px;">🔤</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 4px;">(footer2 only) text color for agnostic company info table label cells</strong>
                    <span style="color: #d0d8e0; font-size: 13px;">Sets text color of rows 1, 3, 5 — <code style="background: rgba(255,255,255,0.1); color: #fde9e3; padding: 1px 6px; border-radius: 3px;">zh_agnostic_cit_label_cell</code></span>
                    <div style="color: #7a8a98; font-size: 11px; margin-top: 4px;">Default: <code style="color: #7a8a98;">#ffffff</code></div>
                </div>
                <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <label for="zh_agnostic_cit_text_color_picker" style="color: #d0d8e0; font-size: 13px; font-weight: 600;">Color:</label>
                    <input
                        type="color"
                        id="zh_agnostic_cit_text_color_picker"
                        name="zh_agnostic_cit_label_text_color"
                        value="<?php echo esc_attr($zh_agnostic_cit_label_text_color); ?>"
                        style="width: 48px; height: 36px; padding: 2px; border: 2px solid #4a5568; border-radius: 6px; background: transparent; cursor: pointer;"
                        title="Pick label cell text color"
                    >
                    <code id="zh_agnostic_cit_text_hex" style="color: #e0d8d2; font-size: 12px; min-width: 60px;"><?php echo esc_html($zh_agnostic_cit_label_text_color); ?></code>
                    <!-- Preset pill: white -->
                    <button type="button"
                        onclick="zh_cit_set_text_color('#ffffff')"
                        style="display:inline-flex; align-items:center; gap:6px; background:#ffffff; color:#1b2a3d; border:2px solid #4a5568; border-radius:20px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ffffff;border:1px solid #4a5568;flex-shrink:0;"></span>
                        #ffffff
                    </button>
                    <!-- Preset pill: muted blue -->
                    <button type="button"
                        onclick="zh_cit_set_text_color('#95abcc')"
                        style="display:inline-flex; align-items:center; gap:6px; background:#95abcc; color:#1b2a3d; border:2px solid #4a5568; border-radius:20px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#95abcc;border:1px solid #4a5568;flex-shrink:0;"></span>
                        #95abcc
                    </button>
                </div>
            </div>
            <script>
            function zh_cit_set_text_color(val) {
                var picker = document.getElementById('zh_agnostic_cit_text_color_picker');
                var hex    = document.getElementById('zh_agnostic_cit_text_hex');
                if (!picker || !hex) return;
                picker.value    = val;
                hex.textContent = val;
            }
            (function() {
                var picker = document.getElementById('zh_agnostic_cit_text_color_picker');
                var hex    = document.getElementById('zh_agnostic_cit_text_hex');
                if (!picker || !hex) return;
                picker.addEventListener('input', function() { hex.textContent = picker.value; });
            })();
            </script>
            <!-- ── /Company Info Table: Label Cell Text Color ── -->

            <!-- ── /Company Info Table: Label Cell Color ── -->

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

                            <!-- Hide Box 4 toggle: removes the map widget and reflows boxes 1-3 across the grid -->
                            <div class="zh_ft2_hide_box4_toggle_block" style="background: #1b2a3d; border: 2px solid #b1250d; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                <div style="flex: 0 0 auto;">
                                    <span style="font-size: 18px;">🗺️</span>
                                </div>
                                <div style="flex: 1; min-width: 180px;">
                                    <strong style="color: #ffffff; font-size: 13px; display: block; margin-bottom: 2px;">Hide map widget</strong>
                                    <span style="color: #d0d8e0; font-size: 12px;">When ON, Box 4 (the map) is removed and Boxes 1-3 spread across the row.</span>
                                </div>
                                <div style="flex: 0 0 auto;">
                                    <label class="zh_ft2_hide_box4_toggle_switch" style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;" title="Toggle hiding the map widget (Box 4)">
                                        <input type="checkbox" name="zaramax_footer_hide_box4_map" value="1" <?php checked($zaramax_footer_hide_box4_map, '1'); ?>
                                            style="opacity: 0; width: 0; height: 0; position: absolute;">
                                        <span class="zh_ft2_hide_box4_toggle_slider" style="
                                            position: absolute; cursor: pointer; inset: 0;
                                            background-color: <?php echo $zaramax_footer_hide_box4_map === '1' ? '#b1250d' : '#4a5568'; ?>;
                                            transition: background-color 0.25s;
                                            border-radius: 28px;
                                        "></span>
                                        <span style="
                                            position: absolute; top: 4px; left: <?php echo $zaramax_footer_hide_box4_map === '1' ? '28px' : '4px'; ?>;
                                            width: 20px; height: 20px;
                                            background: #fff; border-radius: 50%;
                                            transition: left 0.25s;
                                            pointer-events: none;
                                        " class="zh_ft2_hide_box4_toggle_knob"></span>
                                    </label>
                                </div>
                                <div style="flex: 0 0 auto; min-width: 36px;">
                                    <strong id="zh_ft2_hide_box4_toggle_label" style="color: <?php echo $zaramax_footer_hide_box4_map === '1' ? '#e84b2a' : '#7a8a98'; ?>; font-size: 13px;">
                                        <?php echo $zaramax_footer_hide_box4_map === '1' ? 'ON' : 'OFF'; ?>
                                    </strong>
                                </div>
                            </div>
                            <script>
                            (function() {
                                var cb = document.querySelector('input[name="zaramax_footer_hide_box4_map"]');
                                if (!cb) return;
                                cb.addEventListener('change', function() {
                                    var slider = cb.parentElement.querySelector('.zh_ft2_hide_box4_toggle_slider');
                                    var knob   = cb.parentElement.querySelector('.zh_ft2_hide_box4_toggle_knob');
                                    var label  = document.getElementById('zh_ft2_hide_box4_toggle_label');
                                    if (cb.checked) {
                                        slider.style.backgroundColor = '#b1250d';
                                        knob.style.left = '28px';
                                        label.style.color = '#e84b2a';
                                        label.textContent = 'ON';
                                    } else {
                                        slider.style.backgroundColor = '#4a5568';
                                        knob.style.left = '4px';
                                        label.style.color = '#7a8a98';
                                        label.textContent = 'OFF';
                                    }
                                });
                            })();
                            </script>

                            <label for="footer_map_heading"><strong>Heading:</strong></label>
                            <input type="text" id="footer_map_heading" name="footer_map_heading" value="<?php echo esc_attr($footer_map_heading); ?>" style="width: 100%; margin-bottom: 10px;" placeholder="e.g., Visit Our Office">

                            <label for="footer_map_location"><strong>Location/Address:</strong></label>
                            <input type="text" id="footer_map_location" name="footer_map_location" value="<?php echo esc_attr($footer_map_location); ?>" style="width: 100%;" placeholder="e.g., 123 Main St, City, State">
                        </div>
                    </div>
                </div>
                
                <!-- NEW SYSTEM - Footer Bottom Section -->
                <div class="footer-bottom-section" style="border: 3px solid #28a745; background: #f0fff4; padding: 20px; margin-bottom: 30px; border-radius: 8px;">
                    <h2 style="color: #28a745;">Footer Bottom Section (new system)</h2>
                    <p style="color: #666; font-style: italic;">These settings are stored in the wp_zen_sitespren database table and can sync with external systems.</p>
                    <div style="max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 30px;">
                        
                        <!-- New System - Disclaimer Area -->
                        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 2px solid #28a745;">
                            <h3 style="margin-top: 0; color: #28a745;">Disclaimer Area (New System)</h3>
                            <p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">
                                DB Columns: <code style="background: #f4f4f4; padding: 2px 6px;">footer_disclaimer</code>, <code style="background: #f4f4f4; padding: 2px 6px;">hide_footer_disclaimer</code>
                            </p>
                            <div style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="new_hide_footer_disclaimer" name="new_hide_footer_disclaimer" value="1" <?php checked($new_hide_footer_disclaimer, 1); ?> style="margin-right: 8px;">
                                    <strong>Hide disclaimer (do not output in the footer)</strong>
                                </label>
                                <small style="color: #666; display: block; margin-top: 5px;">When checked, the disclaimer will not be displayed in the footer at all.</small>
                            </div>
                            <label for="new_footer_disclaimer"><strong>Disclaimer Content (HTML allowed):</strong></label>
                            <textarea id="new_footer_disclaimer" name="new_footer_disclaimer" rows="6" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter disclaimer HTML..."><?php echo esc_textarea($new_footer_disclaimer); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                        
                        <!-- New System - Footer Legal Links Area -->
                        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 2px solid #28a745;">
                            <h3 style="margin-top: 0; color: #28a745;">Footer Legal Links Area (New System)</h3>
                            <p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">
                                DB Columns: <code style="background: #f4f4f4; padding: 2px 6px;">footer_legal_links</code>, <code style="background: #f4f4f4; padding: 2px 6px;">hide_footer_legal_links</code>
                            </p>
                            <div style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="new_hide_footer_legal_links" name="new_hide_footer_legal_links" value="1" <?php checked($new_hide_footer_legal_links, 1); ?> style="margin-right: 8px;">
                                    <strong>Hide legal links (do not output in the footer)</strong>
                                </label>
                                <small style="color: #666; display: block; margin-top: 5px;">When checked, the legal links will not be displayed in the footer at all.</small>
                            </div>
                            <label for="new_footer_legal_links"><strong>Legal Links Content (HTML allowed):</strong></label>
                            <textarea id="new_footer_legal_links" name="new_footer_legal_links" rows="6" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter legal links HTML..."><?php echo esc_textarea($new_footer_legal_links); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                    </div>
                </div>
                
                <!-- LEGACY SYSTEM - Footer Bottom Section -->
                <div class="footer-bottom-section" style="border: 3px solid #6c757d; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: #6c757d;">Footer Bottom Section (legacy)</h2>
                    <p style="color: #666; font-style: italic;">These settings are stored in WordPress Options table using the get_option() / update_option() system.</p>
                    <div style="max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 30px;">
                        
                        <!-- Legacy - Disclaimer Area -->
                        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                            <h3 style="margin-top: 0; color: #6c757d;">Disclaimer Area (Legacy)</h3>
                            <p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">
                                WP Options Keys: <code style="background: #f4f4f4; padding: 2px 6px;">zaramax_footer_disclaimer</code>, <code style="background: #f4f4f4; padding: 2px 6px;">zaramax_footer_hide_disclaimer</code>
                            </p>
                            <div style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="footer_hide_disclaimer" name="footer_hide_disclaimer" value="1" <?php checked($footer_hide_disclaimer, '1'); ?> style="margin-right: 8px;">
                                    <strong>Hide disclaimer (do not output in the footer)</strong>
                                </label>
                                <small style="color: #666; display: block; margin-top: 5px;">When checked, the disclaimer will not be displayed in the footer at all.</small>
                            </div>
                            <label for="footer_disclaimer"><strong>Disclaimer Content (HTML allowed):</strong></label>
                            <textarea id="footer_disclaimer" name="footer_disclaimer" rows="6" style="width: 100%; margin-top: 5px; font-family: monospace; font-size: 12px;" placeholder="Enter disclaimer HTML..."><?php echo esc_textarea($footer_disclaimer); ?></textarea>
                            <small style="color: #666;">HTML allowed. Line breaks preserved automatically.</small>
                        </div>
                        
                        <!-- Legacy - Footer Legal Links Area -->
                        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                            <h3 style="margin-top: 0; color: #6c757d;">Footer Legal Links Area (Legacy)</h3>
                            <p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">
                                WP Options Keys: <code style="background: #f4f4f4; padding: 2px 6px;">zaramax_footer_legal_links</code>
                            </p>
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

/**
 * Contact Form Options Page
 * Allows switching between database and file-based contact forms
 */
function staircase_contact_form_options_page() {
    // Aggressive notice suppression
    ?>
    <style>
        .notice, .notice-error, .updated, .update-nag, .notice-warning, .notice-info, .notice-success,
        #message, .no-js, .index-php, #wpfooter, .media-upload-form .notice,
        .wrap > h2:first-child + .notice, .wrap > h1:first-child + .notice {
            display: none !important;
        }
        .wrap {
            margin-top: 20px;
        }
        .staircase-notice {
            display: block !important;
            background: #fff;
            border-left: 4px solid #00a0d2;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            padding: 12px;
            margin: 10px 0;
        }
        .form-table th {
            width: 250px;
        }
        .form-table td {
            vertical-align: top;
        }
        .description {
            font-size: 13px;
            font-style: italic;
            margin-top: 5px;
        }
        .status-table {
            margin-top: 30px;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
        }
        .status-table th,
        .status-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e4e7;
        }
        .status-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
    
    <div class="wrap">
        <h1>Contact Form Options</h1>
        
        <?php if (isset($_GET['settings-updated'])): ?>
            <div class="staircase-notice">
                <p><strong>Settings saved successfully!</strong></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="options.php">
            <?php 
            settings_fields('staircase_contact_forms');
            $current_source = get_option('staircase_contact_form_1_source', 'file');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Contact Form Source</th>
                    <td>
                        <select name="staircase_contact_form_1_source" id="contact_form_source">
                            <option value="file" <?php selected($current_source, 'file'); ?>>
                                Theme Files (Recommended - Auto Updates)
                            </option>
                            <option value="database" <?php selected($current_source, 'database'); ?>>
                                Database (Legacy - Manual Updates)
                            </option>
                        </select>
                        <p class="description">
                            <strong>Theme Files:</strong> Contact form updates automatically with theme updates.<br>
                            <strong>Database:</strong> Uses existing database values (current method).
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <hr style="margin: 40px 0;">
        
        <h2>Current Configuration</h2>
        <table class="status-table">
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><strong>Active Source</strong></td>
                <td><?php echo $current_source === 'file' ? 'Theme Files' : 'Database'; ?></td>
                <td>
                    <span class="status-badge active">Active</span>
                </td>
            </tr>
            <tr>
                <td><strong>Theme Files Location</strong></td>
                <td><code>/wp-content/themes/staircase/contact-forms/</code></td>
                <td>
                    <?php 
                    $files_exist = file_exists(get_template_directory() . '/contact-forms/contact-form-1-main-code.php');
                    ?>
                    <span class="status-badge <?php echo $files_exist ? 'active' : 'inactive'; ?>">
                        <?php echo $files_exist ? 'Files Found' : 'Files Missing'; ?>
                    </span>
                </td>
            </tr>
            <?php
            // Check if database has form data and endpoint
            global $wpdb;
            $db_data = $wpdb->get_row("SELECT contact_form_1_main_code, contact_form_1_endpoint FROM {$wpdb->prefix}zen_sitespren LIMIT 1");
            $has_db_form = !empty($db_data->contact_form_1_main_code);
            $has_db_endpoint = !empty($db_data->contact_form_1_endpoint);
            ?>
            <tr>
                <td><strong>Database Form Data</strong></td>
                <td>wp_zen_sitespren.contact_form_1_main_code</td>
                <td>
                    <span class="status-badge <?php echo $has_db_form ? 'active' : 'inactive'; ?>">
                        <?php echo $has_db_form ? 'Available' : 'Not Found'; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Database Endpoint</strong></td>
                <td>wp_zen_sitespren.contact_form_1_endpoint</td>
                <td>
                    <span class="status-badge <?php echo $has_db_endpoint ? 'active' : 'inactive'; ?>">
                        <?php echo $has_db_endpoint ? 'Configured' : 'Not Set'; ?>
                    </span>
                </td>
            </tr>
        </table>
        
        <?php if ($current_source === 'file' && !$files_exist): ?>
            <div class="notice notice-warning" style="display: block !important; margin-top: 20px;">
                <p>
                    <strong>Warning:</strong> Theme files are selected but not found. 
                    The system will fall back to database values.
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// Register settings for contact forms
add_action('admin_init', function() {
    register_setting('staircase_contact_forms', 'staircase_contact_form_1_source', [
        'default' => 'file',
        'sanitize_callback' => function($value) {
            return in_array($value, ['file', 'database']) ? $value : 'file';
        }
    ]);
});

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
    global $wpdb;
    
    // Get all legacy footer settings (WordPress Options)
    $footer_box2_content = get_option('zaramax_footer_box2_content', '');
    $footer_box3_content = get_option('zaramax_footer_box3_content', '');
    $footer_map_heading = get_option('zaramax_footer_map_heading', '');
    $footer_map_location = get_option('zaramax_footer_map_location', '');
    $legacy_hide_disclaimer = get_option('zaramax_footer_hide_disclaimer', '0');
    $legacy_footer_disclaimer = get_option('zaramax_footer_disclaimer', '');
    $legacy_footer_legal_links = get_option('zaramax_footer_legal_links', '');
    
    // Get new system settings from wp_zen_sitespren
    $zen_table = $wpdb->prefix . 'zen_sitespren';
    $new_footer_disclaimer = '';
    $new_hide_footer_disclaimer = 0;
    $new_footer_legal_links = '';
    $new_hide_footer_legal_links = 0;
    
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$zen_table}'");
    if ($table_exists) {
        $zen_data = $wpdb->get_row("SELECT footer_disclaimer, hide_footer_disclaimer, footer_legal_links, hide_footer_legal_links FROM {$zen_table} WHERE wppma_id = 1", ARRAY_A);
        if ($zen_data) {
            $new_footer_disclaimer = $zen_data['footer_disclaimer'] ?? '';
            $new_hide_footer_disclaimer = $zen_data['hide_footer_disclaimer'] ?? 0;
            $new_footer_legal_links = $zen_data['footer_legal_links'] ?? '';
            $new_hide_footer_legal_links = $zen_data['hide_footer_legal_links'] ?? 0;
        }
    }
    
    // Determine which values to use (new system takes priority if it has content)
    // For content fields: use new if not empty
    $footer_disclaimer = !empty($new_footer_disclaimer) ? $new_footer_disclaimer : $legacy_footer_disclaimer;
    $footer_legal_links = !empty($new_footer_legal_links) ? $new_footer_legal_links : $legacy_footer_legal_links;
    
    // For hide fields: if new system has content, use its hide settings; otherwise use legacy
    // This ensures hide settings are properly paired with their content
    if (!empty($new_footer_disclaimer)) {
        $footer_hide_disclaimer = $new_hide_footer_disclaimer;
    } else {
        $footer_hide_disclaimer = $legacy_hide_disclaimer;
    }
    
    if (!empty($new_footer_legal_links)) {
        $footer_hide_legal_links = $new_hide_footer_legal_links;
    } else {
        // Legacy system doesn't have hide_footer_legal_links, so default to not hiding
        $footer_hide_legal_links = 0;
    }
    
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
                                <div class="footer-logo-wrapper">
                                    <img src="<?php echo esc_url($site_logo); ?>" alt="<?php bloginfo('name'); ?>" class="footer-logo-img">
                                </div>
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
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone_raw)); ?>" class="footer-phone-button staircase_main_cta_button">
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
                    <?php 
                    // Show disclaimer only if NOT hidden
                    // Check for both string '1' (legacy) and boolean/integer 1 (new system)
                    if (!($footer_hide_disclaimer == '1' || $footer_hide_disclaimer == 1)): 
                    ?>
                        <!-- Disclaimer Area -->
                        <div class="footer-disclaimer">
                            <?php 
                            // Debug: Show which system is being used and hide status
                            echo '<!-- Footer Disclaimer: Using ' . (!empty($new_footer_disclaimer) ? 'NEW SYSTEM' : 'LEGACY SYSTEM') . ', hide=' . var_export($footer_hide_disclaimer, true) . ' -->';
                            echo wpautop(do_shortcode($footer_disclaimer)); 
                            ?>
                        </div>
                    <?php else: ?>
                        <!-- Footer Disclaimer: Hidden (hide_footer_disclaimer = <?php echo var_export($footer_hide_disclaimer, true); ?>) -->
                    <?php endif; ?>
                    
                    <?php 
                    // Show legal links only if NOT hidden
                    // Check for both string '1' (legacy) and boolean/integer 1 (new system)
                    if (!($footer_hide_legal_links == '1' || $footer_hide_legal_links == 1)): 
                    ?>
                        <!-- Legal Links Area -->
                        <div class="footer-legal-links">
                            <?php 
                            // Debug: Show which system is being used and hide status
                            echo '<!-- Footer Legal Links: Using ' . (!empty($new_footer_legal_links) ? 'NEW SYSTEM' : 'LEGACY SYSTEM') . ', hide=' . var_export($footer_hide_legal_links, true) . ' -->';
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
                    <?php else: ?>
                        <!-- Footer Legal Links: Hidden (hide_footer_legal_links = <?php echo var_export($footer_hide_legal_links, true); ?>) -->
                    <?php endif; ?>
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
    
    // Get hero background size setting from wp_pylons (default to 'cover' if not set)
    $hero_background_size = 'cover'; // Default value
    if ($pylon_data && !empty($pylon_data['hero_style_setting_background_size'])) {
        $hero_background_size = $pylon_data['hero_style_setting_background_size'];
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
    
    // Get hero overlay opacity (default to 50 if not set)
    $hero_overlay_opacity = 50; // Default value
    if ($pylon_data && isset($pylon_data['hero_overlay_opacity'])) {
        $hero_overlay_opacity = intval($pylon_data['hero_overlay_opacity']);
        // Ensure opacity is between 0 and 100
        $hero_overlay_opacity = max(0, min(100, $hero_overlay_opacity));
    }
    // Convert to decimal for CSS
    $hero_overlay_opacity_decimal = $hero_overlay_opacity / 100;
    
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
                        <a href="<?php echo esc_url($cherry_button_left_url); ?>" class="batman-hero-button batman-hero-button-left" aria-label="<?php echo esc_attr($cherry_button_left_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_left_text); ?></span>
                        </a>
                    <?php else: ?>
                        <button class="batman-hero-button batman-hero-button-left batman-hero-button-disabled" disabled aria-label="<?php echo esc_attr($cherry_button_left_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_left_text); ?></span>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($cherry_button_right_url): ?>
                        <a href="<?php echo esc_url($cherry_button_right_url); ?>" class="batman-hero-button batman-hero-button-right staircase_main_cta_button" aria-label="<?php echo esc_attr($cherry_button_right_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_right_text); ?></span>
                        </a>
                    <?php else: ?>
                        <button class="batman-hero-button batman-hero-button-right batman-hero-button-disabled staircase_main_cta_button" disabled aria-label="<?php echo esc_attr($cherry_button_right_text); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span><?php echo esc_html($cherry_button_right_text); ?></span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($cherry_phone_number_raw): ?>
                    <div class="phone_display_only_holder_div">
                        <span class="phone-display-only" aria-label="Phone number: <?php echo esc_attr($cherry_phone_number_formatted); ?>">
                            <span class="screen-reader-text">Phone: </span>
                            <?php echo esc_html($cherry_phone_number_formatted); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <style>
    .cherry-hero {
        <?php if (!empty($paragon_image_url)): ?>
        /* Image background with opacity overlay */
        background: 
            linear-gradient(rgba(0, 0, 0, <?php echo esc_attr($hero_overlay_opacity_decimal); ?>), rgba(0, 0, 0, <?php echo esc_attr($hero_overlay_opacity_decimal); ?>)),
            url('<?php echo esc_url($paragon_image_url); ?>');
        background-size: <?php echo esc_attr($hero_background_size); ?>;
        background-position: center center;
        background-repeat: no-repeat;
        <?php else: ?>
        /* Quantum system gradient background when no image */
        background: linear-gradient(135deg, #414449 0%, #2a2b2e 100%);
        <?php endif; ?>
        color: white;
        text-align: center;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    /* Diagonal pattern overlay from quantum system */
    .cherry-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
        background-image: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 35px,
            rgba(255, 255, 255, 0.05) 35px,
            rgba(255, 255, 255, 0.05) 70px
        );
        pointer-events: none;
        z-index: 1;
    }
    
    .cherry-hero .container {
        position: relative;
        z-index: 2;
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
        gap: 1rem;
        margin: 2rem 0 30px 0;
        flex-wrap: wrap;
        padding: 0 20px;
    }
    
    /* Quantum System Button Styles */
    .batman-hero-button {
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        min-height: 48px; /* Touch-friendly size from quantum */
    }
    
    .batman-hero-button svg {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
    }
    
    .batman-hero-button-left {
        background-color: white !important;
        color: #414449 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
        border: none !important;
    }
    
    .batman-hero-button-left:hover,
    .batman-hero-button-left:focus {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
        background-color: #f9f9f9 !important;
        color: #414449 !important;
    }
    
    .batman-hero-button-right {
        background-color: #23bb72 !important;
        color: white !important;
        border: 2px solid #23bb72 !important;
        box-shadow: none !important;
    }
    
    .batman-hero-button-right:hover,
    .batman-hero-button-right:focus {
        background-color: #fd9f1f !important;
        border-color: #fd9f1f !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(253, 159, 31, 0.4) !important;
        color: white !important;
    }
    
    .batman-hero-button-disabled {
        cursor: default;
        pointer-events: none;
        opacity: 0.6;
        background-color: #cccccc !important;
        color: #666666 !important;
        border: 2px solid #cccccc !important;
    }
    
    /* Keep old cherry-button classes for backward compatibility */
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
        background-color: white;
        color: #414449;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        border: none;
    }
    
    .cherry-button-left:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        background-color: #f9f9f9;
        color: #414449;
    }
    
    .cherry-button-right {
        background-color: #23bb72;
        color: white;
        border: 2px solid #23bb72;
    }
    
    .cherry-button-right:hover {
        background-color: #fd9f1f;
        border-color: #fd9f1f;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(253, 159, 31, 0.4);
        color: white;
    }
    
    /* Phone display only - not a button */
    .phone_display_only_holder_div {
        margin-top: 25px;
        text-align: center;
    }
    
    .phone-display-only {
        color: white;
        font-size: 1.6rem; /* Enlarged from 1.3rem */
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: color 0.3s ease;
        /* No border, padding, or button-like styling */
    }
    
    .phone-display-only:hover {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
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
        
        .phone-display-only {
            font-size: 1.4rem; /* Enlarged for mobile */
        }
    }
    
    @media (max-width: 768px) {
        .cherry-heading {
            font-size: 2.5rem;
        }
        
        .cherry-subheading {
            font-size: 1.1rem;
        }
        
        .phone-display-only {
            font-size: 1.2rem; /* Enlarged for small mobile */
        }
    }
    
    /* Fix spacing issue - remove padding between header and hero */
    .site-content {
        padding-top: 0 !important;
    }
    
    /* Ensure hero is flush with header - pull up to compensate for body padding */
    .cherry-hero {
        margin-top: -80px !important; /* Negative margin to offset body padding */
        padding-top: calc(80px + 80px) !important; /* Header height + original padding */
    }
    
    /* Responsive negative margins to match header heights */
    @media (max-width: 480px) {
        .cherry-hero {
            margin-top: -63px !important;
            padding-top: calc(63px + 60px) !important; /* Header + reduced mobile padding */
        }
    }
    
    @media (min-width: 481px) and (max-width: 768px) {
        .cherry-hero {
            margin-top: -75px !important;
            padding-top: calc(75px + 60px) !important; /* Header + reduced tablet padding */
        }
    }
    
    @media (min-width: 769px) {
        .cherry-hero {
            margin-top: -85px !important;
            padding-top: calc(85px + 80px) !important; /* Header + original padding */
        }
    }
    </style>
    <?php
}

/**
 * Render Average Rating Box Section
 */
function staircase_render_avg_rating_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // First check sitewide settings from zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $sitespren_data = $wpdb->get_row("SELECT ratingvalue_for_schema, reviewcount_for_schema, avg_rating_box_hide_sitewide FROM {$sitespren_table} LIMIT 1", ARRAY_A);
    
    // Check if sitewide hide is enabled
    if ($sitespren_data && $sitespren_data['avg_rating_box_hide_sitewide']) {
        return; // Don't render if sitewide hide is true
    }
    
    // Get page-specific hide setting from wp_pylons
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT avg_rating_box_hide FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Check if page-specific hide is enabled (only if sitewide is not hiding)
    if ($pylon_data && $pylon_data['avg_rating_box_hide']) {
        return; // Don't render if page-specific hide is true
    }
    
    // Get the rating value from sitespren (sitewide value)
    $rating = ($sitespren_data && $sitespren_data['ratingvalue_for_schema']) ? floatval($sitespren_data['ratingvalue_for_schema']) : 0;
    
    // Get the review count from sitespren (sitewide value)
    $review_count = ($sitespren_data && $sitespren_data['reviewcount_for_schema']) ? intval($sitespren_data['reviewcount_for_schema']) : 0;
    
    // Only show the box if there's a rating
    if ($rating <= 0) {
        return;
    }
    
    // Calculate star fills
    $full_stars = floor($rating);
    $partial_star = $rating - $full_stars;
    ?>
    
    <section class="avg-rating-box-section">
        <div class="avg-rating-container">
            <div class="review-images">
                <!-- Rating Display Element -->
                <div class="avg-rating-display">
                    <div class="avg-rating-label">Reviews<?php echo $review_count > 0 ? ' (' . $review_count . ')' : ''; ?>:</div>
                    <div class="avg-rating-score">
                        <div class="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $full_stars): ?>
                                    <span class="star filled">★</span>
                                <?php elseif ($i == $full_stars + 1 && $partial_star > 0): ?>
                                    <span class="star partial" style="--partial-fill: <?php echo $partial_star * 100; ?>%">★</span>
                                <?php else: ?>
                                    <span class="star empty">★</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text"><?php echo number_format($rating, 1); ?>/5</span>
                    </div>
                </div>
                
                <!-- Review Images -->
                <img src="/wp-content/uploads/2026/03/review1.webp" />
                <img src="/wp-content/uploads/2026/03/review2.webp" />
                <img src="/wp-content/uploads/2026/03/review3.webp" />
                <img src="/wp-content/uploads/2026/03/review4.webp" />
                <img src="/wp-content/uploads/2026/03/review5.webp" />
            </div>
        </div>
    </section>
    
    <style>
    .avg-rating-box-section {
        background: #f8f9fa;
        padding: 0;
        margin: 0;
        border-bottom: 1px solid #d3d3d3;
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
    }
    
    .avg-rating-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .review-images {
        display: flex;
        gap: 20px;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
    }
    
    /* Rating Display Box Styling */
    .avg-rating-display {
        background: white;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-width: 150px;
        max-width: 200px;
        height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .avg-rating-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 6px;
        font-weight: 600;
    }
    
    .avg-rating-score {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow: hidden;
    }
    
    .star-rating {
        display: flex;
        gap: 1px;
        flex-shrink: 0;
    }
    
    .star {
        font-size: 18px;
        line-height: 1;
    }
    
    .star.filled {
        color: #ffc107;
    }
    
    .star.empty {
        color: #ddd;
    }
    
    .star.partial {
        position: relative;
        color: #ddd;
    }
    
    .star.partial::before {
        content: '★';
        position: absolute;
        left: 0;
        top: 0;
        width: var(--partial-fill);
        overflow: hidden;
        color: #ffc107;
    }
    
    .rating-text {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        flex-shrink: 0;
        white-space: nowrap;
    }
    
    /* Review Images Styling */
    .review-images img {
        height: 70px;
        width: auto;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        object-fit: cover;
    }
    
    .review-images img:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
    }
    
    /* Add subtle animation on load */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .review-images > * {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .review-images > *:nth-child(1) { animation-delay: 0.1s; }
    .review-images > *:nth-child(2) { animation-delay: 0.2s; }
    .review-images > *:nth-child(3) { animation-delay: 0.3s; }
    .review-images > *:nth-child(4) { animation-delay: 0.4s; }
    .review-images > *:nth-child(5) { animation-delay: 0.5s; }
    .review-images > *:nth-child(6) { animation-delay: 0.6s; }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .review-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 15px 10px;
        }
        
        .avg-rating-display {
            width: 100%;
            text-align: left;
            height: 60px;
            padding: 8px 12px;
        }
        
        .avg-rating-score {
            justify-content: flex-start;
            gap: 8px;
        }
        
        .review-images img {
            width: 100%;
            height: 60px;
            object-fit: cover;
        }
        
        .star {
            font-size: 18px;
        }
        
        .rating-text {
            font-size: 16px;
        }
        
        .avg-rating-label {
            font-size: 12px;
            margin-bottom: 4px;
        }
    }
    
    @media (max-width: 480px) {
        .review-images {
            gap: 10px;
            padding: 10px 5px;
        }
        
        .review-images img {
            height: 50px;
        }
        
        .avg-rating-display {
            height: 50px;
            padding: 6px 10px;
        }
        
        .star {
            font-size: 16px;
        }
        
        .rating-text {
            font-size: 14px;
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
    <section class="chen-cards-section">
        <div class="chen-container">
            <div class="cards-grid">
                <?php if (!empty($chen_card_1_title)): ?>
                    <div class="service-card">
                        <!-- Card Icon -->
                        <div class="card-icon">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <h3 class="card-title"><?php echo esc_html($chen_card_1_title); ?></h3>
                        <?php if (!empty($chen_card_1_description)): ?>
                            <p class="card-description"><?php echo esc_html($chen_card_1_description); ?></p>
                        <?php endif; ?>
                        <!-- Decorative corner accent -->
                        <div class="card-accent"></div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($chen_card_2_title)): ?>
                    <div class="service-card">
                        <!-- Card Icon -->
                        <div class="card-icon">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3 class="card-title"><?php echo esc_html($chen_card_2_title); ?></h3>
                        <?php if (!empty($chen_card_2_description)): ?>
                            <p class="card-description"><?php echo esc_html($chen_card_2_description); ?></p>
                        <?php endif; ?>
                        <!-- Decorative corner accent -->
                        <div class="card-accent"></div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($chen_card_3_title)): ?>
                    <div class="service-card">
                        <!-- Card Icon -->
                        <div class="card-icon">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h3 class="card-title"><?php echo esc_html($chen_card_3_title); ?></h3>
                        <?php if (!empty($chen_card_3_description)): ?>
                            <p class="card-description"><?php echo esc_html($chen_card_3_description); ?></p>
                        <?php endif; ?>
                        <!-- Decorative corner accent -->
                        <div class="card-accent"></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <style>
    /* Quantum System Chen Cards Styles */
    .chen-cards-section {
        padding: 80px 20px;
        background: #f8f9fa; /* Light gray background from quantum system */
    }
    
    .chen-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Quantum system responsive grid */
        gap: 30px;
        margin-top: 20px;
    }
    
    .service-card {
        background: white;
        border-radius: 12px;
        padding: 40px 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); /* Quantum system shadow */
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12); /* Enhanced shadow on hover */
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #2563eb, #1e40af); /* Gradient from quantum system */
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .card-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 15px;
        color: #1a1a1a;
        line-height: 1.3;
    }
    
    .card-description {
        color: #6b7280; /* Quantum system gray */
        line-height: 1.7;
        font-size: 1rem;
        margin: 0;
    }
    
    .card-accent {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(30, 64, 175, 0.1));
        border-radius: 50%;
    }
    
    /* Ensure proper grid layout on larger tablets */
    @media (max-width: 1024px) {
        .cards-grid {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }
    }
    
    /* Tablet Styles */
    @media (max-width: 768px) {
        .chen-cards-section {
            padding: 60px 20px;
        }
        
        .cards-grid {
            grid-template-columns: 1fr;
            gap: 25px;
        }
        
        .service-card {
            padding: 35px 25px;
        }
        
        .card-title {
            font-size: 1.3rem;
        }
    }
    
    /* Mobile Styles */
    @media (max-width: 480px) {
        .chen-cards-section {
            padding: 40px 15px;
        }
        
        .cards-grid {
            gap: 20px;
        }
        
        .service-card {
            padding: 30px 20px;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 1.25rem;
        }
        
        .card-description {
            font-size: 0.95rem;
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
 * Render Reviewsbox Section
 */
function staircase_render_reviewsbox() {
    $current_post_id = get_the_ID();
    global $wpdb;
    
    // Get reviews data from wp_pylons table for current post
    $reviews_data = $wpdb->get_row($wpdb->prepare(
        "SELECT reviewsbox_heading, reviewsbox_subheading, reviewsbox_description,
                reviewsbox_review1_stars, reviewsbox_review1_content, reviewsbox_review1_location, 
                reviewsbox_review1_name, reviewsbox_review1_image_id, reviewsbox_review1_service, reviewsbox_review1_date,
                reviewsbox_review2_stars, reviewsbox_review2_content, reviewsbox_review2_location,
                reviewsbox_review2_name, reviewsbox_review2_image_id, reviewsbox_review2_service, reviewsbox_review2_date,
                reviewsbox_review3_stars, reviewsbox_review3_content, reviewsbox_review3_location,
                reviewsbox_review3_name, reviewsbox_review3_image_id, reviewsbox_review3_service, reviewsbox_review3_date,
                reviewsbox_review4_stars, reviewsbox_review4_content, reviewsbox_review4_location,
                reviewsbox_review4_name, reviewsbox_review4_image_id, reviewsbox_review4_service, reviewsbox_review4_date,
                reviewsbox_review5_stars, reviewsbox_review5_content, reviewsbox_review5_location,
                reviewsbox_review5_name, reviewsbox_review5_image_id, reviewsbox_review5_service, reviewsbox_review5_date
         FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $current_post_id
    ), ARRAY_A);
    
    // Check if any review content exists
    $has_review_content = false;
    if ($reviews_data) {
        // Check if we have heading or any reviews
        if (!empty($reviews_data['reviewsbox_heading']) || !empty($reviews_data['reviewsbox_subheading'])) {
            $has_review_content = true;
        } else {
            // Check all review fields for content
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($reviews_data["reviewsbox_review{$i}_content"]) || !empty($reviews_data["reviewsbox_review{$i}_name"])) {
                    $has_review_content = true;
                    break;
                }
            }
        }
    }
    
    // Only render the reviews box if content exists
    if ($has_review_content) {
    ?>
    <!-- Reviewsbox Section -->
    <section class="reviewsbox-section">
        <div class="reviewsbox-container">
            <?php if (!empty($reviews_data['reviewsbox_heading'])): ?>
                <h2 class="reviewsbox-heading"><?php echo esc_html($reviews_data['reviewsbox_heading']); ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($reviews_data['reviewsbox_subheading'])): ?>
                <p class="reviewsbox-subheading"><?php echo esc_html($reviews_data['reviewsbox_subheading']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($reviews_data['reviewsbox_description'])): ?>
                <div class="reviewsbox-description">
                    <?php echo wp_kses_post(wpautop($reviews_data['reviewsbox_description'])); ?>
                </div>
            <?php endif; ?>
            
            <div class="reviewsbox-grid">
                <?php
                // Loop through reviews and display non-empty items
                for ($i = 1; $i <= 5; $i++) {
                    $review_stars = $reviews_data["reviewsbox_review{$i}_stars"] ?? 0;
                    $review_content = $reviews_data["reviewsbox_review{$i}_content"] ?? '';
                    $review_location = $reviews_data["reviewsbox_review{$i}_location"] ?? '';
                    $review_name = $reviews_data["reviewsbox_review{$i}_name"] ?? '';
                    $review_image_id = $reviews_data["reviewsbox_review{$i}_image_id"] ?? null;
                    $review_service = $reviews_data["reviewsbox_review{$i}_service"] ?? '';
                    $review_date = $reviews_data["reviewsbox_review{$i}_date"] ?? '';
                    
                    // Only display if review has content
                    if (!empty(trim($review_content)) || !empty(trim($review_name))) {
                ?>
                    <div class="review-card">
                        <?php if ($review_stars > 0): ?>
                            <div class="review-stars">
                                <?php 
                                for ($star = 1; $star <= 5; $star++) {
                                    echo $star <= $review_stars ? '★' : '☆';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($review_content)): ?>
                            <div class="review-content">
                                <p><?php echo esc_html($review_content); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="review-footer">
                            <div class="review-author">
                                <?php if ($review_image_id && $image_url = wp_get_attachment_image_url($review_image_id, 'thumbnail')): ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($review_name); ?>" class="review-author-image">
                                <?php endif; ?>
                                
                                <div class="review-author-info">
                                    <?php if (!empty($review_name)): ?>
                                        <div class="review-author-name"><?php echo esc_html($review_name); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($review_location)): ?>
                                        <div class="review-author-location"><?php echo esc_html($review_location); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($review_service) || !empty($review_date)): ?>
                                <div class="review-meta">
                                    <?php if (!empty($review_service)): ?>
                                        <div class="review-service">Service: <?php echo esc_html($review_service); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($review_date)): ?>
                                        <div class="review-date">
                                            <?php echo date('M d, Y', strtotime($review_date)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        
        <style>
        .reviewsbox-section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .reviewsbox-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .reviewsbox-heading {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .reviewsbox-subheading {
            font-size: 1.25rem;
            text-align: center;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .reviewsbox-description {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
            color: #555;
        }
        
        .reviewsbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .review-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        
        .review-stars {
            font-size: 1.25rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        
        .review-content {
            margin-bottom: 1.5rem;
        }
        
        .review-content p {
            line-height: 1.6;
            color: #333;
            font-style: italic;
        }
        
        .review-footer {
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
        
        .review-author {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .review-author-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }
        
        .review-author-info {
            flex: 1;
        }
        
        .review-author-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        
        .review-author-location {
            font-size: 0.875rem;
            color: #666;
        }
        
        .review-meta {
            font-size: 0.875rem;
            color: #888;
        }
        
        .review-service {
            margin-bottom: 5px;
        }
        
        .review-date {
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .reviewsbox-heading {
                font-size: 2rem;
            }
            
            .reviewsbox-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .review-card {
                padding: 20px;
            }
        }
        </style>
    </section>
    <?php
    } // End if ($has_review_content)
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
    
    // Don't render the map if no valid location data is available
    if (empty($location_string)) {
        return;
    }
    
    // URL encode the location for the Google Maps embed
    $encoded_location = urlencode($location_string);
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
                title="Our Location - <?php echo esc_attr($location_string); ?>">
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
                <a href="tel:<?php echo esc_attr($clean_phone); ?>" class="kristina-cta-button staircase_main_cta_button">
                    Call Now
                </a>
            <?php else: ?>
                <a href="tel:555-0123" class="kristina-cta-button staircase_main_cta_button">
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
    global $wpdb;
    $post_id = get_the_ID();
    
    // Check if blog box should be hidden
    $hide_blog_box = $wpdb->get_var($wpdb->prepare(
        "SELECT victoria_blog_box_hide FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $post_id
    ));
    
    // If hide is TRUE (1), don't render the blog box
    if ($hide_blog_box == 1 || $hide_blog_box === true || $hide_blog_box === 'true') {
        return;
    }
    
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

/**
 * Render Ocean1 Box - Article Content Area
 */
function staircase_render_ocean1_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_ocean_1 FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content exists and is not null
    if ($pylon_data && !empty($pylon_data['content_ocean_1'])) {
        $content = $pylon_data['content_ocean_1'];
        ?>
        <section class="ocean1-content-box" style="padding: 40px 20px; background: #ffffff;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="article-content" style="font-size: 16px; line-height: 1.6; color: #333; border: 2px solid gray; border-radius: 20px; padding: 30px;">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    .ocean1-content-box {
                        padding: 20px 0 !important;
                    }
                    .ocean1-content-box .container {
                        margin: 0 !important;
                        padding: 0 15px;
                    }
                    .ocean1-content-box .article-content {
                        padding: 20px 15px !important;
                        border-radius: 10px !important;
                    }
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Ocean2 Box - Article Content Area
 */
function staircase_render_ocean2_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_ocean_2 FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content exists and is not null
    if ($pylon_data && !empty($pylon_data['content_ocean_2'])) {
        $content = $pylon_data['content_ocean_2'];
        ?>
        <section class="ocean2-content-box" style="padding: 40px 20px; background: #f8f9fa;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="article-content" style="font-size: 16px; line-height: 1.6; color: #333; border: 2px solid gray; border-radius: 20px; padding: 30px; background: white;">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    .ocean2-content-box {
                        padding: 20px 0 !important;
                    }
                    .ocean2-content-box .container {
                        margin: 0 !important;
                        padding: 0 15px;
                    }
                    .ocean2-content-box .article-content {
                        padding: 20px 15px !important;
                        border-radius: 10px !important;
                    }
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Ocean3 Box - Article Content Area
 */
function staircase_render_ocean3_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_ocean_3 FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content exists and is not null
    if ($pylon_data && !empty($pylon_data['content_ocean_3'])) {
        $content = $pylon_data['content_ocean_3'];
        ?>
        <section class="ocean3-content-box" style="padding: 40px 20px; background: #ffffff;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="article-content" style="font-size: 16px; line-height: 1.6; color: #333; border: 2px solid gray; border-radius: 20px; padding: 30px;">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    .ocean3-content-box {
                        padding: 20px 0 !important;
                    }
                    .ocean3-content-box .container {
                        margin: 0 !important;
                        padding: 0 15px;
                    }
                    .ocean3-content-box .article-content {
                        padding: 20px 15px !important;
                        border-radius: 10px !important;
                    }
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Brook Video Box - Video Embeds Section
 */
function staircase_render_brook_video_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            brook_video_heading,
            brook_video_subheading,
            brook_video_description,
            brook_video_1,
            brook_video_2,
            brook_video_3,
            brook_video_4,
            brook_video_outro
        FROM {$pylons_table} 
        WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Check if any field has content
    $has_content = false;
    if ($pylon_data) {
        foreach ($pylon_data as $value) {
            if (!empty($value)) {
                $has_content = true;
                break;
            }
        }
    }
    
    // Only render if at least one field has content
    if ($has_content) {
        ?>
        <section class="brook-video-box" style="padding: 40px 20px; background: #fff; border-top: 2px solid #333; border-bottom: 2px solid #333;">
            <div class="brook-container" style="max-width: 1200px; margin: 0 auto;">
                <?php if (!empty($pylon_data['brook_video_heading'])): ?>
                    <h2 style="font-size: 32px; color: #333; margin: 0 0 15px 0; text-align: center;">
                        <?php echo esc_html($pylon_data['brook_video_heading']); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['brook_video_subheading'])): ?>
                    <h3 style="font-size: 20px; color: #666; margin: 0 0 20px 0; text-align: center; font-weight: normal;">
                        <?php echo esc_html($pylon_data['brook_video_subheading']); ?>
                    </h3>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['brook_video_description'])): ?>
                    <div style="font-size: 16px; color: #555; margin: 0 0 30px 0; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
                        <?php echo wp_kses_post(wpautop($pylon_data['brook_video_description'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="brook-videos" style="margin: 30px 0;">
                    <div class="video-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <?php 
                        for ($i = 1; $i <= 4; $i++) {
                            $video_content = $pylon_data["brook_video_$i"] ?? '';
                            if (!empty($video_content)): 
                        ?>
                            <div class="video-item" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                                <?php 
                                // Check if it's an iframe/embed code or plain text
                                if (strpos($video_content, '<iframe') !== false || strpos($video_content, '<embed') !== false) {
                                    // It's an embed code - wrap it in responsive container
                                    ?>
                                    <div class="video-responsive" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                            <?php echo wp_kses($video_content, array(
                                                'iframe' => array(
                                                    'src' => true,
                                                    'width' => true,
                                                    'height' => true,
                                                    'frameborder' => true,
                                                    'allow' => true,
                                                    'allowfullscreen' => true,
                                                    'title' => true,
                                                ),
                                                'embed' => array(
                                                    'src' => true,
                                                    'width' => true,
                                                    'height' => true,
                                                    'type' => true,
                                                )
                                            )); ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    // It's text content
                                    ?>
                                    <div style="font-size: 16px; line-height: 1.6; color: #333;">
                                        <?php echo wp_kses_post($video_content); ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php 
                            endif;
                        }
                        ?>
                    </div>
                </div>
                
                <?php if (!empty($pylon_data['brook_video_outro'])): ?>
                    <div style="font-size: 16px; color: #555; margin: 30px 0 0 0; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
                        <?php echo wp_kses_post(wpautop($pylon_data['brook_video_outro'])); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <style>
                /* Mobile optimization for Brook box */
                @media (max-width: 768px) {
                    .brook-video-box {
                        padding: 25px 15px !important;
                    }
                    
                    .brook-video-box h2 {
                        font-size: 24px !important;
                    }
                    
                    .brook-video-box h3 {
                        font-size: 18px !important;
                    }
                    
                    .brook-videos .video-grid {
                        grid-template-columns: 1fr !important;
                        gap: 15px !important;
                    }
                    
                    .video-item {
                        padding: 10px !important;
                    }
                }
                
                /* Make iframe responsive */
                .video-responsive iframe,
                .video-responsive embed {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100% !important;
                    height: 100% !important;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Olivia Auth Links Box - Authority Links Section
 */
function staircase_render_olivia_authlinks_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            olivia_authlinks_heading,
            olivia_authlinks_subheading,
            olivia_authlinks_description,
            olivia_authlinks_1,
            olivia_authlinks_2,
            olivia_authlinks_3,
            olivia_authlinks_4,
            olivia_authlinks_5,
            olivia_authlinks_6,
            olivia_authlinks_7,
            olivia_authlinks_8,
            olivia_authlinks_9,
            olivia_authlinks_10,
            olivia_authlinks_outro
        FROM {$pylons_table} 
        WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Check if any field has content
    $has_content = false;
    if ($pylon_data) {
        foreach ($pylon_data as $value) {
            if (!empty($value)) {
                $has_content = true;
                break;
            }
        }
    }
    
    // Only render if at least one field has content
    if ($has_content) {
        ?>
        <section class="olivia-authlinks-box" style="padding: 40px 20px; background: #f8f9fa; border-top: 2px solid #333; border-bottom: 2px solid #333;">
            <div class="olivia-container" style="max-width: 1200px; margin: 0 auto;">
                <?php if (!empty($pylon_data['olivia_authlinks_heading'])): ?>
                    <h2 style="font-size: 32px; color: #333; margin: 0 0 15px 0; text-align: center;">
                        <?php echo esc_html($pylon_data['olivia_authlinks_heading']); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['olivia_authlinks_subheading'])): ?>
                    <h3 style="font-size: 20px; color: #666; margin: 0 0 20px 0; text-align: center; font-weight: normal;">
                        <?php echo esc_html($pylon_data['olivia_authlinks_subheading']); ?>
                    </h3>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['olivia_authlinks_description'])): ?>
                    <div style="font-size: 16px; color: #555; margin: 0 0 30px 0; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
                        <?php echo wp_kses_post(wpautop($pylon_data['olivia_authlinks_description'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="olivia-links" style="margin: 30px 0;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php 
                        for ($i = 1; $i <= 10; $i++) {
                            $link_content = $pylon_data["olivia_authlinks_$i"] ?? '';
                            if (!empty($link_content)): 
                        ?>
                            <li style="margin: 15px 0; padding: 10px 20px; background: white; border-left: 3px solid #0073aa; border-radius: 3px;">
                                <div style="font-size: 16px; line-height: 1.6; color: #333;">
                                    <?php echo wp_kses_post($link_content); ?>
                                </div>
                            </li>
                        <?php 
                            endif;
                        }
                        ?>
                    </ul>
                </div>
                
                <?php if (!empty($pylon_data['olivia_authlinks_outro'])): ?>
                    <div style="font-size: 16px; color: #555; margin: 30px 0 0 0; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
                        <?php echo wp_kses_post(wpautop($pylon_data['olivia_authlinks_outro'])); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <style>
                /* Mobile optimization for Olivia box */
                @media (max-width: 768px) {
                    .olivia-authlinks-box {
                        padding: 25px 15px !important;
                    }
                    
                    .olivia-authlinks-box h2 {
                        font-size: 24px !important;
                    }
                    
                    .olivia-authlinks-box h3 {
                        font-size: 18px !important;
                    }
                    
                    .olivia-links ul li {
                        margin: 10px 0 !important;
                        padding: 8px 15px !important;
                    }
                }
                
                /* Style links within the content */
                .olivia-links a {
                    color: #0073aa;
                    text-decoration: underline;
                }
                
                .olivia-links a:hover {
                    color: #005a87;
                    text-decoration: none;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Ava Why Choose Us Box - Placeholder Implementation
 */
function staircase_render_ava_whychooseus_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            ava_why_choose_us_heading,
            ava_why_choose_us_subheading,
            ava_why_choose_us_description,
            ava_why_choose_us_reason_1,
            ava_why_choose_us_reason_2,
            ava_why_choose_us_reason_3,
            ava_why_choose_us_reason_4,
            ava_why_choose_us_reason_5,
            ava_why_choose_us_reason_6,
            ava_why_choose_us_reason_7,
            ava_why_choose_us_reason_8,
            ava_why_choose_us_reason_9,
            ava_why_choose_us_reason_10
        FROM {$pylons_table} 
        WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Check if any field has content
    $has_content = false;
    if ($pylon_data) {
        foreach ($pylon_data as $value) {
            if (!empty($value)) {
                $has_content = true;
                break;
            }
        }
    }
    
    // Only render if at least one field has content
    if ($has_content) {
        ?>
        <section class="ava-whychooseus-box" style="padding: 50px 20px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); position: relative; overflow: hidden;">
            <!-- Quantum system background pattern -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            
            <div class="ava-container" style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
                <?php if (!empty($pylon_data['ava_why_choose_us_heading'])): ?>
                    <h2 style="font-size: 36px; color: white; margin: 0 0 15px 0; text-align: center; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                        <?php echo esc_html($pylon_data['ava_why_choose_us_heading']); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['ava_why_choose_us_subheading'])): ?>
                    <h3 style="font-size: 22px; color: rgba(255,255,255,0.95); margin: 0 0 25px 0; text-align: center; font-weight: 300;">
                        <?php echo esc_html($pylon_data['ava_why_choose_us_subheading']); ?>
                    </h3>
                <?php endif; ?>
                
                <?php if (!empty($pylon_data['ava_why_choose_us_description'])): ?>
                    <div style="font-size: 17px; color: rgba(255,255,255,0.9); margin: 0 0 40px 0; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                        <?php echo wp_kses_post(wpautop($pylon_data['ava_why_choose_us_description'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="ava-reasons" style="margin: 30px 0;">
                    <div class="reasons-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        <?php 
                        for ($i = 1; $i <= 10; $i++) {
                            $reason_content = $pylon_data["ava_why_choose_us_reason_$i"] ?? '';
                            if (!empty($reason_content)): 
                        ?>
                            <div class="reason-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                <div style="display: flex; align-items: start;">
                                    <div style="flex-shrink: 0; width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1e40af); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);">
                                        <span style="color: white; font-weight: bold; font-size: 18px;"><?php echo $i; ?></span>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 16px; line-height: 1.6; color: #333;">
                                            <?php echo wp_kses_post($reason_content); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endif;
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Hover effect for reason cards */
                .reason-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
                }
                
                /* Mobile optimization for Ava box */
                @media (max-width: 768px) {
                    .ava-whychooseus-box {
                        padding: 35px 15px !important;
                    }
                    
                    .ava-whychooseus-box h2 {
                        font-size: 28px !important;
                    }
                    
                    .ava-whychooseus-box h3 {
                        font-size: 19px !important;
                    }
                    
                    .ava-reasons .reasons-grid {
                        grid-template-columns: 1fr !important;
                        gap: 15px !important;
                    }
                    
                    .reason-card {
                        padding: 15px !important;
                    }
                }
                
                /* Style links within the reasons */
                .ava-reasons a {
                    color: #667eea;
                    text-decoration: underline;
                    font-weight: 500;
                }
                
                .ava-reasons a:hover {
                    color: #764ba2;
                    text-decoration: none;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Kendall Our Process Box
 */
function staircase_render_kendall_ourprocess_box() {
    $current_post_id = get_the_ID();
    global $wpdb;
    
    // Get kendall our process data from wp_pylons table for current post
    $process_data = $wpdb->get_row($wpdb->prepare(
        "SELECT kendall_our_process_heading, kendall_our_process_subheading, kendall_our_process_description,
                kendall_our_process_step_1, kendall_our_process_step_2, kendall_our_process_step_3,
                kendall_our_process_step_4, kendall_our_process_step_5, kendall_our_process_step_6,
                kendall_our_process_step_7, kendall_our_process_step_8, kendall_our_process_step_9,
                kendall_our_process_step_10
         FROM {$wpdb->prefix}pylons WHERE rel_wp_post_id = %d",
        $current_post_id
    ), ARRAY_A);
    
    // Check if any process content exists
    $has_content = false;
    if ($process_data) {
        $has_content = !empty($process_data['kendall_our_process_heading']) || 
                      !empty($process_data['kendall_our_process_subheading']) || 
                      !empty($process_data['kendall_our_process_description']);
        
        // Check if any steps have content
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($process_data["kendall_our_process_step_{$i}"])) {
                $has_content = true;
                break;
            }
        }
    }
    
    // Only render if there's content
    if (!$has_content) {
        return;
    }
    
    ?>
    <!-- Kendall Our Process Box Section -->
    <section class="kendall-process-box" style="width: 100%; max-width: 1200px; margin: 40px auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-sizing: border-box;">
        <div class="kendall-process-container" style="text-align: center;">
            
            <?php if (!empty($process_data['kendall_our_process_heading'])): ?>
                <h2 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700; color: #333; line-height: 1.2;">
                    <?php echo esc_html($process_data['kendall_our_process_heading']); ?>
                </h2>
            <?php endif; ?>
            
            <?php if (!empty($process_data['kendall_our_process_subheading'])): ?>
                <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 400; color: #666; line-height: 1.3;">
                    <?php echo esc_html($process_data['kendall_our_process_subheading']); ?>
                </h3>
            <?php endif; ?>
            
            <?php if (!empty($process_data['kendall_our_process_description'])): ?>
                <div style="margin: 0 0 30px 0; font-size: 16px; color: #555; line-height: 1.5; max-width: 800px; margin-left: auto; margin-right: auto;">
                    <?php echo wp_kses_post(wpautop($process_data['kendall_our_process_description'])); ?>
                </div>
            <?php endif; ?>
            
            <!-- Process steps with quantum styling -->
            <div class="kendall-process-steps" style="position: relative; max-width: 900px; margin: 0 auto;">
                <!-- Vertical line connector (quantum style) -->
                <div class="process-line-connector" style="position: absolute; left: 30px; top: 0; bottom: 0; width: 2px; background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%); opacity: 0.3;"></div>
                
                <?php 
                $step_count = 0;
                for ($i = 1; $i <= 10; $i++): 
                    $step_content = $process_data["kendall_our_process_step_{$i}"] ?? '';
                    if (!empty($step_content)):
                        $step_count++;
                ?>
                    <div class="process-step" style="display: flex; align-items: flex-start; margin-bottom: 40px; position: relative;">
                        <!-- Step number circle (quantum gradient style) -->
                        <div class="step-number" style="min-width: 60px; height: 60px; background: linear-gradient(135deg, #2563eb, #1e40af); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.25rem; position: relative; z-index: 1; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);">
                            <?php echo $step_count; ?>
                        </div>
                        
                        <!-- Step content (quantum card style) -->
                        <div class="step-content" style="flex: 1; margin-left: 30px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); position: relative; transition: all 0.3s ease;">
                            <!-- Arrow pointing from circle to content -->
                            <div class="step-arrow" style="position: absolute; left: -10px; top: 20px; width: 0; height: 0; border-top: 10px solid transparent; border-bottom: 10px solid transparent; border-right: 10px solid white;"></div>
                            
                            <span class="step-text" style="color: #374151; line-height: 1.7; font-size: 1.05rem; display: block;">
                                <?php echo wp_kses_post($step_content); ?>
                            </span>
                        </div>
                    </div>
                <?php 
                    endif;
                endfor; 
                ?>
            </div>
            
        </div>
    </section>
    
    <style>
    /* Hover effect for process steps (quantum enhancement) */
    .process-step:hover .step-content {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12) !important;
        transform: translateX(5px) !important;
    }
    
    /* Mobile responsiveness for kendall process box */
    @media (max-width: 768px) {
        .kendall-process-box {
            margin: 20px 10px !important;
            padding: 15px !important;
        }
        .kendall-process-box h2 {
            font-size: 24px !important;
        }
        .kendall-process-box h3 {
            font-size: 16px !important;
        }
        .kendall-process-steps {
            padding: 0 10px !important;
        }
        .process-line-connector {
            left: 20px !important;
        }
        .process-step {
            margin-bottom: 30px !important;
        }
        .process-step .step-number {
            min-width: 50px !important;
            height: 50px !important;
            font-size: 1.1rem !important;
        }
        .process-step .step-content {
            margin-left: 20px !important;
            padding: 20px !important;
        }
        .step-text {
            font-size: 0.95rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .kendall-process-box {
            margin: 15px 5px !important;
            padding: 12px !important;
        }
        .kendall-process-box h2 {
            font-size: 22px !important;
        }
        .process-line-connector {
            left: 15px !important;
            width: 1px !important;
        }
        .process-step .step-number {
            min-width: 40px !important;
            height: 40px !important;
            font-size: 1rem !important;
        }
        .process-step .step-content {
            margin-left: 15px !important;
            padding: 15px !important;
        }
        .step-arrow {
            display: none !important;
        }
        .step-text {
            font-size: 0.9rem !important;
        }
    }
    </style>
    <?php
}

/**
 * Render Sara Custom HTML Box - Placeholder Implementation
 */
function staircase_render_sara_customhtml_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get sara_customhtml_datum from wp_pylons
    $pylons_table = $wpdb->prefix . 'pylons';
    $custom_html = $wpdb->get_var($wpdb->prepare(
        "SELECT sara_customhtml_datum FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ));
    
    // Only render if sara_customhtml_datum has content
    if (!empty($custom_html)) {
        ?>
        <section class="sara-customhtml-box" style="width: 100%;">
            <?php echo $custom_html; ?>
        </section>
        <?php
    }
}

/**
 * Render Liz Pricing Box - Service pricing and information display
 */
function staircase_render_liz_pricing_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get pricing data from wp_pylons
    $pylons_table = $wpdb->prefix . 'pylons';
    $pricing_data = $wpdb->get_row($wpdb->prepare(
        "SELECT liz_pricing_heading, liz_pricing_description, liz_pricing_body 
         FROM {$pylons_table} 
         WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Get values directly from database without fallbacks
    $heading = $pricing_data['liz_pricing_heading'] ?? '';
    $description = $pricing_data['liz_pricing_description'] ?? '';
    $body = $pricing_data['liz_pricing_body'] ?? '';
    
    // Only render if there is actual content from database
    if (!empty($heading) || !empty($description) || !empty($body)) {
        ?>
        <section class="liz-pricing-box" style="background: #f8f9fa; padding: 40px 20px;">
            <div class="container" style="max-width: 800px; margin: 0 auto;">
                
                <?php if (!empty($heading)): ?>
                <h2 style="font-size: 32px; color: #333; margin-bottom: 15px; text-align: center;">
                    <?php echo esc_html($heading); ?>
                </h2>
                <?php endif; ?>
                
                <?php if (!empty($description)): ?>
                <p style="color: #666; font-size: 18px; margin-bottom: 30px; text-align: center;">
                    <?php echo esc_html($description); ?>
                </p>
                <?php endif; ?>
                
                <?php if (!empty($body)): ?>
                <div class="pricing-body" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <ul style="list-style: none; padding: 0; margin: 0; font-size: 16px; line-height: 1.8;">
                        <?php
                        // Split body content by newlines and create list items
                        $lines = explode("\n", trim($body));
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                echo '<li style="margin-bottom: 10px; color: #555;">• ' . esc_html($line) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
            </div>
        </section>
        <?php
    }
}

/**
 * Render Baynar1 Box - Mobile-optimized image and text layout
 */
function staircase_render_baynar1_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT baynar1_main FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if baynar1_main exists and is not null
    if ($pylon_data && !empty($pylon_data['baynar1_main'])) {
        $content = $pylon_data['baynar1_main'];
        ?>
        <section class="baynar1-box" style="border-top: 1px solid black; border-bottom: 1px solid black;">
            <div class="baynar1-container" style="display: flex; flex-direction: row; width: 100%; max-width: 1200px; margin: 0 auto;">
                <!-- Left side - Image -->
                <div class="baynar1-image-container" style="flex: 1; background: #f5f5f5; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                    <div style="color: #666; font-size: 14px;">
                        [Image Area]
                    </div>
                </div>
                
                <!-- Vertical divider -->
                <div class="baynar1-divider" style="width: 1px; background: gray; flex-shrink: 0;"></div>
                
                <!-- Right side - Text content -->
                <div class="baynar1-text-container" style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="baynar1-content">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Mobile optimization */
                @media (max-width: 768px) {
                    .baynar1-container {
                        flex-direction: column !important;
                    }
                    
                    .baynar1-divider {
                        display: none;
                    }
                    
                    .baynar1-image-container {
                        min-height: 150px;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    .baynar1-text-container {
                        padding: 15px;
                    }
                }
                
                /* Ensure text doesn't exceed ~250 words visually */
                .baynar1-content {
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                }
                
                .baynar1-content p {
                    margin-bottom: 1em;
                }
                
                .baynar1-content p:last-child {
                    margin-bottom: 0;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Baynar2 Box - Mobile-optimized image and text layout
 */
function staircase_render_baynar2_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT baynar2_main FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if baynar2_main exists and is not null
    if ($pylon_data && !empty($pylon_data['baynar2_main'])) {
        $content = $pylon_data['baynar2_main'];
        ?>
        <section class="baynar2-box" style="border-top: 1px solid black; border-bottom: 1px solid black;">
            <div class="baynar2-container" style="display: flex; flex-direction: row; width: 100%; max-width: 1200px; margin: 0 auto;">
                <!-- Left side - Image -->
                <div class="baynar2-image-container" style="flex: 1; background: #f5f5f5; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                    <div style="color: #666; font-size: 14px;">
                        [Image Area]
                    </div>
                </div>
                
                <!-- Vertical divider -->
                <div class="baynar2-divider" style="width: 1px; background: gray; flex-shrink: 0;"></div>
                
                <!-- Right side - Text content -->
                <div class="baynar2-text-container" style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="baynar2-content">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Mobile optimization */
                @media (max-width: 768px) {
                    .baynar2-container {
                        flex-direction: column !important;
                    }
                    
                    .baynar2-divider {
                        display: none;
                    }
                    
                    .baynar2-image-container {
                        min-height: 150px;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    .baynar2-text-container {
                        padding: 15px;
                    }
                }
                
                /* Ensure text doesn't exceed ~250 words visually */
                .baynar2-content {
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                }
                
                .baynar2-content p {
                    margin-bottom: 1em;
                }
                
                .baynar2-content p:last-child {
                    margin-bottom: 0;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Content Bay 1 Box - Mobile-optimized image and text layout
 */
function staircase_render_content_bay_1_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data including image ID
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_bay_1, content_bay_1_image_id FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content_bay_1 exists and is not null
    if ($pylon_data && !empty($pylon_data['content_bay_1'])) {
        $content = $pylon_data['content_bay_1'];
        $image_id = !empty($pylon_data['content_bay_1_image_id']) ? intval($pylon_data['content_bay_1_image_id']) : 0;
        ?>
        <section class="content-bay-1-box" style="border-top: 1px solid black; border-bottom: 1px solid black;">
            <div class="content-bay-1-container" style="display: flex; flex-direction: row; width: 100%; max-width: 1200px; margin: 0 auto;">
                <!-- Left side - Text content (on desktop) -->
                <div class="content-bay-1-text-container" style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: center; order: 1;">
                    <div class="content-bay-1-content">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>
                </div>
                
                <!-- Vertical divider -->
                <div class="content-bay-1-divider" style="width: 1px; background: gray; flex-shrink: 0; order: 2;"></div>
                
                <!-- Right side - Image (on desktop) -->
                <?php 
                $has_image = $image_id && wp_attachment_is_image($image_id);
                $align_items = $has_image ? 'flex-start' : 'center';
                ?>
                <div class="content-bay-1-image-container" style="flex: 1; background: #f5f5f5; min-height: 200px; display: flex; align-items: <?php echo $align_items; ?>; justify-content: center; order: 3;">
                    <div class="content-bay-1-image-container-inner" style="width: 100%; max-height: 500px; display: flex; align-items: <?php echo $align_items; ?>; justify-content: center; overflow: hidden;">
                        <?php if ($has_image): 
                            $image_url = wp_get_attachment_image_url($image_id, 'large');
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" style="width: 100%; height: auto; max-height: 500px; object-fit: contain;">
                        <?php else: ?>
                            <!-- Star icon fallback -->
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="#4a4a4a">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Mobile optimization */
                @media (max-width: 768px) {
                    .content-bay-1-container {
                        flex-direction: column !important;
                    }
                    
                    .content-bay-1-divider {
                        display: none;
                    }
                    
                    .content-bay-1-image-container {
                        min-height: 150px;
                        border-bottom: 1px solid #ddd;
                        order: 1 !important; /* Image first on mobile */
                    }
                    
                    .content-bay-1-text-container {
                        padding: 15px;
                        order: 2 !important; /* Text second on mobile */
                    }
                }
                
                /* Ensure text doesn't exceed ~250 words visually */
                .content-bay-1-content {
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                }
                
                .content-bay-1-content p {
                    margin-bottom: 1em;
                }
                
                .content-bay-1-content p:last-child {
                    margin-bottom: 0;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Content Bay 2 Box - Mobile-optimized image and text layout
 */
function staircase_render_content_bay_2_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data including image ID
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_bay_2, content_bay_2_image_id FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content_bay_2 exists and is not null
    if ($pylon_data && !empty($pylon_data['content_bay_2'])) {
        $content = $pylon_data['content_bay_2'];
        $image_id = !empty($pylon_data['content_bay_2_image_id']) ? intval($pylon_data['content_bay_2_image_id']) : 0;
        ?>
        <section class="content-bay-2-box" style="border-top: 1px solid black; border-bottom: 1px solid black;">
            <div class="content-bay-2-container" style="display: flex; flex-direction: row; width: 100%; max-width: 1200px; margin: 0 auto;">
                <!-- Left side - Image -->
                <?php 
                $has_image = $image_id && wp_attachment_is_image($image_id);
                $align_items = $has_image ? 'flex-start' : 'center';
                ?>
                <div class="content-bay-2-image-container" style="flex: 1; background: #f5f5f5; min-height: 200px; display: flex; align-items: <?php echo $align_items; ?>; justify-content: center;">
                    <div class="content-bay-2-image-container-inner" style="width: 100%; max-height: 500px; display: flex; align-items: <?php echo $align_items; ?>; justify-content: center; overflow: hidden;">
                        <?php if ($has_image): 
                            $image_url = wp_get_attachment_image_url($image_id, 'large');
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" style="width: 100%; height: auto; max-height: 500px; object-fit: contain;">
                        <?php else: ?>
                            <!-- Star icon fallback -->
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="#4a4a4a">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Vertical divider -->
                <div class="content-bay-2-divider" style="width: 1px; background: gray; flex-shrink: 0;"></div>
                
                <!-- Right side - Text content -->
                <div class="content-bay-2-text-container" style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="content-bay-2-content">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Mobile optimization */
                @media (max-width: 768px) {
                    .content-bay-2-container {
                        flex-direction: column !important;
                    }
                    
                    .content-bay-2-divider {
                        display: none;
                    }
                    
                    .content-bay-2-image-container {
                        min-height: 150px;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    .content-bay-2-text-container {
                        padding: 15px;
                    }
                }
                
                /* Ensure text doesn't exceed ~250 words visually */
                .content-bay-2-content {
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                }
                
                .content-bay-2-content p {
                    margin-bottom: 1em;
                }
                
                .content-bay-2-content p:last-child {
                    margin-bottom: 0;
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Content Lake Box - Ocean-style content section
 */
function staircase_render_content_lake_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_lake FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content_lake exists and is not null
    if ($pylon_data && !empty($pylon_data['content_lake'])) {
        $content = $pylon_data['content_lake'];
        ?>
        <section class="content-lake-box" style="padding: 40px 20px; background: #ffffff;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="article-content" style="font-size: 16px; line-height: 1.6; color: #333; border: 2px solid gray; border-radius: 20px; padding: 30px;">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    .content-lake-box {
                        padding: 20px 0 !important;
                    }
                    .content-lake-box .container {
                        margin: 0 !important;
                        padding: 0 15px;
                    }
                    .content-lake-box .article-content {
                        padding: 20px 15px !important;
                        border-radius: 10px !important;
                    }
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Content Sea Box - Ocean-style content section
 */
function staircase_render_content_sea_box() {
    global $wpdb;
    $post_id = get_the_ID();
    
    // Get wp_pylons data
    $pylons_table = $wpdb->prefix . 'pylons';
    $pylon_data = $wpdb->get_row($wpdb->prepare(
        "SELECT content_sea FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ), ARRAY_A);
    
    // Only render if content_sea exists and is not null
    if ($pylon_data && !empty($pylon_data['content_sea'])) {
        $content = $pylon_data['content_sea'];
        ?>
        <section class="content-sea-box" style="padding: 40px 20px; background: #ffffff;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="article-content" style="font-size: 16px; line-height: 1.6; color: #333; border: 2px solid gray; border-radius: 20px; padding: 30px;">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    .content-sea-box {
                        padding: 20px 0 !important;
                    }
                    .content-sea-box .container {
                        margin: 0 !important;
                        padding: 0 15px;
                    }
                    .content-sea-box .article-content {
                        padding: 20px 15px !important;
                        border-radius: 10px !important;
                    }
                }
            </style>
        </section>
        <?php
    }
}

/**
 * Render Monica Contact Box Section
 * Displays a contact form with map embed for contactpage archetype
 */
function staircase_render_monica_contact_box() {
    global $wpdb;
    
    // Get data from wp_zen_sitespren table (always needed for map info)
    $brand_name = $wpdb->get_var("SELECT driggs_brand_name FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $city = $wpdb->get_var("SELECT driggs_city FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $state_code = $wpdb->get_var("SELECT driggs_state_code FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    $phone = $wpdb->get_var("SELECT driggs_phone_1 FROM {$wpdb->prefix}zen_sitespren LIMIT 1") ?: '';
    
    // Format the phone number for display
    $phone_formatted = $phone ? staircase_format_phone_number($phone) : '';
    
    // Clean phone number for tel: link (remove all non-numeric characters)
    $phone_clean = preg_replace('/[^0-9]/', '', $phone);
    
    // Build location string for map embed (same logic as nile map)
    $location_parts = array();
    if (!empty($city)) {
        $location_parts[] = trim($city);
    }
    if (!empty($state_code)) {
        $location_parts[] = trim($state_code);
    }
    
    $location_string = implode(', ', $location_parts);
    $encoded_location = urlencode($location_string);
    
    // Don't render if no location data
    if (empty($location_string)) {
        return;
    }
    ?>
    <!-- Monica Contact Box Section -->
    <section class="monica-contact-box">
        <div class="monica-container">
            <!-- Left Side - Map and Info -->
            <div class="monica-left-section">
                <div class="monica-contact-info">
                    <h2 class="monica-heading"><strong>Get In Touch Today</strong></h2>
                    <h3 class="monica-brand-name"><strong><?php echo esc_html($brand_name); ?></strong></h3>
                    <p class="monica-service-area">Providing service throughout <?php echo esc_html($city); ?>, <?php echo esc_html($state_code); ?>.</p>
                    <?php if ($phone_formatted): ?>
                        <p class="monica-phone">
                            <a href="tel:<?php echo esc_attr($phone_clean); ?>" class="monica-phone-link">
                                <?php echo esc_html($phone_formatted); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="monica-map-container">
                    <iframe 
                        src="https://www.google.com/maps?q=<?php echo $encoded_location; ?>&output=embed"
                        width="100%" 
                        height="318" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Our Location - <?php echo esc_attr($location_string); ?>">
                    </iframe>
                </div>
            </div>
            
            <!-- Right Side - Contact Form -->
            <div class="monica-right-section">
                <div class="monica-form-container">
                    <?php 
                    // Check contact form source setting
                    $source = get_option('staircase_contact_form_1_source', 'file');
                    $template_path = get_template_directory() . '/contact-forms/';
                    
                    if ($source === 'file' && file_exists($template_path . 'contact-form-1-main-code.php')) {
                        // Load from theme files
                        
                        // Enqueue CSS file
                        wp_enqueue_style('weasel-contact-form', 
                            get_template_directory_uri() . '/contact-forms/weasel_header_code_for_contact_form.css',
                            [], 
                            filemtime($template_path . 'weasel_header_code_for_contact_form.css')
                        );
                        
                        // Enqueue JS file
                        wp_enqueue_script('weasel-contact-form',
                            get_template_directory_uri() . '/contact-forms/weasel_footer_code_for_contact_form.js',
                            [], 
                            filemtime($template_path . 'weasel_footer_code_for_contact_form.js'),
                            true // Load in footer
                        );
                        
                        // Include the PHP template
                        include $template_path . 'contact-form-1-main-code.php';
                        
                    } else {
                        // Legacy: Load from database
                        $site_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zen_sitespren LIMIT 1");
                        
                        if ($site_data) {
                            // Add header CSS inline
                            if (!empty($site_data->weasel_header_code_for_contact_form)) {
                                echo '<style>' . $site_data->weasel_header_code_for_contact_form . '</style>';
                            }
                            
                            // Render form HTML
                            if (!empty($site_data->contact_form_1_main_code)) {
                                echo do_shortcode($site_data->contact_form_1_main_code);
                            }
                            
                            // Add footer JS inline
                            if (!empty($site_data->weasel_footer_code_for_contact_form)) {
                                echo '<script>' . $site_data->weasel_footer_code_for_contact_form . '</script>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    
    <style>
    /* Monica Contact Box Styles */
    .monica-contact-box {
        width: 100%;
        padding: 40px 0;
        background: transparent;
    }
    
    .monica-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        gap: 40px;
        align-items: flex-start;
    }
    
    .monica-left-section {
        flex: 1;
        max-width: 636px;
    }
    
    .monica-contact-info {
        margin-bottom: 30px;
    }
    
    .monica-heading {
        font-size: 2rem;
        margin: 0 0 10px 0;
        color: #333;
    }
    
    .monica-brand-name {
        font-size: 1.5rem;
        margin: 0 0 15px 0;
        color: #333;
    }
    
    .monica-service-area {
        font-size: 1rem;
        margin: 0 0 10px 0;
        color: #666;
    }
    
    .monica-phone {
        font-size: 1.1rem;
        margin: 0 0 20px 0;
        font-weight: 500;
    }
    
    .monica-phone-link {
        color: #009bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .monica-phone-link:hover {
        color: #0073aa;
        text-decoration: underline;
    }
    
    .monica-map-container {
        width: 100%;
        max-width: 636px;
        height: 318px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .monica-map-container iframe {
        width: 100%;
        height: 100%;
        display: block;
    }
    
    .monica-right-section {
        flex: 1;
        min-width: 300px;
    }
    
    .monica-form-container {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .monica-container {
            flex-direction: column;
            gap: 30px;
            padding: 0 15px;
        }
        
        .monica-left-section {
            max-width: 100%;
        }
        
        .monica-map-container {
            max-width: 100%;
            height: 250px;
        }
        
        .monica-heading {
            font-size: 1.5rem;
        }
        
        .monica-brand-name {
            font-size: 1.3rem;
        }
        
        .monica-form-container {
            padding: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .monica-map-container {
            height: 200px;
        }
        
        .monica-heading {
            font-size: 1.3rem;
        }
        
        .monica-brand-name {
            font-size: 1.1rem;
        }
    }
    </style>
    <?php
}

/**
 * Render the clastic form standalone (contact form without monica wrapper)
 * Used by [staircase_clastic_form] shortcode
 */
function staircase_render_clastic_form() {
    global $wpdb;

    $source = get_option('staircase_contact_form_1_source', 'file');
    $template_path = get_template_directory() . '/contact-forms/';

    ob_start();

    if ($source === 'file' && file_exists($template_path . 'contact-form-1-main-code.php')) {
        wp_enqueue_style('weasel-contact-form',
            get_template_directory_uri() . '/contact-forms/weasel_header_code_for_contact_form.css',
            [],
            filemtime($template_path . 'weasel_header_code_for_contact_form.css')
        );

        wp_enqueue_script('weasel-contact-form',
            get_template_directory_uri() . '/contact-forms/weasel_footer_code_for_contact_form.js',
            [],
            filemtime($template_path . 'weasel_footer_code_for_contact_form.js'),
            true
        );

        include $template_path . 'contact-form-1-main-code.php';
    } else {
        $site_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zen_sitespren LIMIT 1");

        if ($site_data) {
            if (!empty($site_data->weasel_header_code_for_contact_form)) {
                echo '<style>' . $site_data->weasel_header_code_for_contact_form . '</style>';
            }
            if (!empty($site_data->contact_form_1_main_code)) {
                echo '<div class="clastic-form">' . do_shortcode($site_data->contact_form_1_main_code) . '</div>';
            }
            if (!empty($site_data->weasel_footer_code_for_contact_form)) {
                echo '<script>' . $site_data->weasel_footer_code_for_contact_form . '</script>';
            }
        }
    }

    return ob_get_clean();
}
add_shortcode('staircase_clastic_form', 'staircase_render_clastic_form');

/**
 * Render Nectar Blog Feed
 * Custom blog feed display independent of WordPress blog page settings
 *
 * @param int $items_qty Number of blog posts to display (default 6)
 * @param bool $use_excerpt Whether to show excerpt (true) or full content (false) - default true
 */
function staircase_render_nectar_blog_feed($items_qty = 6, $use_excerpt = true) {
    // Query for recent blog posts
    $blog_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $items_qty,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'ignore_sticky_posts' => true
    ));
    
    if ($blog_query->have_posts()) {
        ?>
        <section class="nectar-blog-feed">
            <div class="nectar-blog-container">
                <h2 class="nectar-blog-title">Latest Blog Posts</h2>
                <div class="nectar-blog-grid">
                    <?php
                    while ($blog_query->have_posts()) {
                        $blog_query->the_post();
                        ?>
                        <article class="nectar-blog-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="nectar-blog-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="nectar-blog-content">
                                <h3 class="nectar-blog-item-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="nectar-blog-meta">
                                    <span class="nectar-blog-date"><?php echo get_the_date(); ?></span>
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) {
                                        echo '<span class="nectar-blog-category">' . esc_html($categories[0]->name) . '</span>';
                                    }
                                    ?>
                                </div>
                                
                                <div class="nectar-blog-post-content">
                                    <?php 
                                    if ($use_excerpt) {
                                        // Show excerpt when nectar_blog_is_excerpt is TRUE
                                        echo wp_trim_words(get_the_excerpt(), 20, '...');
                                    } else {
                                        // Show full content when nectar_blog_is_excerpt is FALSE
                                        the_content();
                                    }
                                    ?>
                                </div>
                                
                                <?php if ($use_excerpt) : ?>
                                    <a href="<?php the_permalink(); ?>" class="nectar-blog-read-more">
                                        Read More →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <style>
        .nectar-blog-feed {
            margin-top: 4rem;
            padding: 3rem 0;
            background: #f8f9fa;
        }
        
        .nectar-blog-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .nectar-blog-title {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3rem;
            color: #222;
        }
        
        .nectar-blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .nectar-blog-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .nectar-blog-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .nectar-blog-thumbnail {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .nectar-blog-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .nectar-blog-item:hover .nectar-blog-thumbnail img {
            transform: scale(1.05);
        }
        
        .nectar-blog-content {
            padding: 1.5rem;
        }
        
        .nectar-blog-item-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .nectar-blog-item-title a {
            color: #222;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .nectar-blog-item-title a:hover {
            color: #0066cc;
        }
        
        .nectar-blog-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #666;
        }
        
        .nectar-blog-category {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        .nectar-blog-post-content {
            margin-bottom: 1rem;
            color: #444;
            line-height: 1.6;
        }
        
        /* Style for full content display */
        .nectar-blog-post-content p {
            margin-bottom: 1rem;
        }
        
        .nectar-blog-post-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }
        
        .nectar-blog-post-content h2,
        .nectar-blog-post-content h3,
        .nectar-blog-post-content h4 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .nectar-blog-post-content ul,
        .nectar-blog-post-content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        
        .nectar-blog-read-more {
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nectar-blog-read-more:hover {
            color: #0052a3;
        }
        
        @media (max-width: 768px) {
            .nectar-blog-grid {
                grid-template-columns: 1fr;
            }
            
            .nectar-blog-title {
                font-size: 1.5rem;
            }
        }
        </style>
        <?php
    }
    
    // Reset post data
    wp_reset_postdata();
}

// Add body class for TPCom nav styles
function staircase_add_tpcom_nav_body_class($classes) {
    if (get_option("staircase_use_tpcom_nav_styles", false)) {
        $classes[] = "tpcom-nav-styles";
    }
    return $classes;
}
add_filter("body_class", "staircase_add_tpcom_nav_body_class");

// ============================================================
// Sitewide config injection — makes wp_zen_sitespren values
// available to all frontend JS via window.siteConfig
// ============================================================
function staircase_inject_site_config() {
    global $wpdb;
    $endpoint = $wpdb->get_var( "SELECT contact_form_1_endpoint FROM {$wpdb->prefix}zen_sitespren LIMIT 1" );
    if ( empty( $endpoint ) ) {
        return;
    }
    echo '<script>window.siteConfig = window.siteConfig || {}; window.siteConfig.formEndpoint = ' . json_encode( esc_url_raw( $endpoint ) ) . ';</script>' . "\n";
}
add_action( 'wp_head', 'staircase_inject_site_config', 5 );
