<?php
/**
 * Staircase Template Selector
 * 
 * Centralized logic for determining which header, footer, and sidebar 
 * templates to use based on database settings with fallback defaults.
 * 
 * Hierarchy:
 * 1. Page-specific setting (wp_pylons.header_desired/footer_desired/sidebar_desired)
 * 2. Site default setting (wp_zen_sitespren.site_default_header/footer/sidebar)
 * 3. Hard-coded defaults (homeservice_header_1/homeservice_footer_1/homeservice_sidebar_1)
 */

/**
 * Get the header template to use for the current page
 * 
 * @return string The header template name (e.g., 'homeservice_header_1')
 */
function staircase_get_header_template() {
    global $wpdb;
    
    // Default fallback
    $default_header = 'homeservice_header_1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific header in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_header = $wpdb->get_var($wpdb->prepare(
            "SELECT header_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific header set, use it
        if (!empty($page_header)) {
            return $page_header;
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_header FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, use it
    if (!empty($site_default)) {
        return $site_default;
    }
    
    // Otherwise return hard-coded default
    return $default_header;
}

/**
 * Get the footer template to use for the current page
 * 
 * @return string The footer template name (e.g., 'homeservice_footer_1')
 */
function staircase_get_footer_template() {
    global $wpdb;
    
    // Default fallback
    $default_footer = 'homeservice_footer_1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific footer in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_footer = $wpdb->get_var($wpdb->prepare(
            "SELECT footer_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific footer set, use it
        if (!empty($page_footer)) {
            return $page_footer;
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_footer FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, use it
    if (!empty($site_default)) {
        return $site_default;
    }
    
    // Otherwise return hard-coded default
    return $default_footer;
}

/**
 * Get the sidebar template to use for the current page
 * 
 * @return string The sidebar template name (e.g., 'homeservice_sidebar_1')
 */
function staircase_get_sidebar_template() {
    global $wpdb;
    
    // Default fallback
    $default_sidebar = 'homeservice_sidebar_1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific sidebar in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_sidebar = $wpdb->get_var($wpdb->prepare(
            "SELECT sidebar_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific sidebar set, use it
        if (!empty($page_sidebar)) {
            return $page_sidebar;
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_sidebar FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, use it
    if (!empty($site_default)) {
        return $site_default;
    }
    
    // Otherwise return hard-coded default
    return $default_sidebar;
}

/**
 * Render the selected header template
 * This function loads and executes the appropriate header file
 */
function staircase_render_selected_header() {
    $header_template = staircase_get_header_template();
    $header_file = get_template_directory() . '/headers/' . $header_template . '.php';
    
    // Check if header file exists
    if (file_exists($header_file)) {
        require_once $header_file;
        
        // Call the render function for the specific header
        $render_function = 'render_' . $header_template;
        if (function_exists($render_function)) {
            echo "<!-- Rendering Header: $header_template -->\n";
            $render_function();
        } else {
            // Fallback to default header
            echo "<!-- Header function not found, falling back to homeservice_header_1 -->\n";
            require_once get_template_directory() . '/headers/homeservice_header_1.php';
            if (function_exists('render_homeservice_header_1')) {
                render_homeservice_header_1();
            }
        }
    } else {
        // Fallback to default header if file doesn't exist
        echo "<!-- Header file not found, falling back to homeservice_header_1 -->\n";
        $fallback_file = get_template_directory() . '/headers/homeservice_header_1.php';
        if (file_exists($fallback_file)) {
            require_once $fallback_file;
            if (function_exists('render_homeservice_header_1')) {
                render_homeservice_header_1();
            }
        }
    }
}

/**
 * Render the selected footer template
 * This function loads and executes the appropriate footer file
 */
function staircase_render_selected_footer() {
    $footer_template = staircase_get_footer_template();
    $footer_file = get_template_directory() . '/footers/' . $footer_template . '.php';
    
    // Check if footer file exists
    if (file_exists($footer_file)) {
        require_once $footer_file;
        
        // Call the render function for the specific footer
        $render_function = 'render_' . $footer_template;
        if (function_exists($render_function)) {
            echo "<!-- Rendering Footer: $footer_template -->\n";
            $render_function();
        } else {
            // Fallback to default footer
            echo "<!-- Footer function not found, falling back to homeservice_footer_1 -->\n";
            require_once get_template_directory() . '/footers/homeservice_footer_1.php';
            if (function_exists('render_homeservice_footer_1')) {
                render_homeservice_footer_1();
            }
        }
    } else {
        // Fallback to default footer if file doesn't exist
        echo "<!-- Footer file not found, falling back to homeservice_footer_1 -->\n";
        $fallback_file = get_template_directory() . '/footers/homeservice_footer_1.php';
        if (file_exists($fallback_file)) {
            require_once $fallback_file;
            if (function_exists('render_homeservice_footer_1')) {
                render_homeservice_footer_1();
            }
        }
    }
}

/**
 * Render the selected sidebar template
 * This function loads and executes the appropriate sidebar file
 */
function staircase_render_selected_sidebar() {
    $sidebar_template = staircase_get_sidebar_template();
    $sidebar_file = get_template_directory() . '/sidebars/' . $sidebar_template . '.php';
    
    // Check if sidebar file exists
    if (file_exists($sidebar_file)) {
        require_once $sidebar_file;
        
        // Call the render function for the specific sidebar
        $render_function = 'render_' . $sidebar_template;
        if (function_exists($render_function)) {
            echo "<!-- Rendering Sidebar: $sidebar_template -->\n";
            $render_function();
        } else {
            // For now, just output a placeholder since sidebars don't exist yet
            echo "<!-- Sidebar function not found: $sidebar_template -->\n";
        }
    } else {
        // For now, just output a placeholder since sidebars don't exist yet
        echo "<!-- Sidebar template not implemented: $sidebar_template -->\n";
    }
}