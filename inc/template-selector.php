<?php
/**
 * Staircase Template Selector
 * 
 * Centralized logic for determining which header, footer, sidebar, and anteheader
 * templates to use based on database settings with fallback defaults.
 * 
 * Hierarchy:
 * 1. Page-specific setting (wp_pylons.header_desired/footer_desired/sidebar_desired/anteheader_desired)
 * 2. Site default setting (wp_zen_sitespren.site_default_header/footer/sidebar/anteheader)
 * 3. Hard-coded defaults (header1/footer1/sidebar1/anteheader1)
 */

/**
 * Validate template name - only accept new naming convention
 * @param string $template_name The template name to validate
 * @param string $type The type of template (header/footer/sidebar/anteheader)
 * @return string|null The validated template name or null if invalid
 */
function staircase_validate_template_name($template_name, $type = 'header') {
    // Only accept new naming convention
    $valid_templates = [
        'header' => ['header1', 'header2', 'header3'],
        'footer' => ['footer1', 'footer2', 'footer3'],
        'sidebar' => ['sidebar1', 'sidebar2'],
        'anteheader' => ['anteheader1', 'anteheader2']
    ];
    
    // Check if template is valid for this type
    if (isset($valid_templates[$type]) && in_array($template_name, $valid_templates[$type])) {
        return $template_name;
    }
    
    // Invalid template name - return null
    return null;
}

/**
 * Get the header template to use for the current page
 * 
 * @return string The header template name (e.g., 'header1')
 */
function staircase_get_header_template() {
    global $wpdb;
    
    // Check for override filter (useful for testing)
    $override = apply_filters('staircase_header_override', false);
    if ($override) {
        return $override;
    }
    
    // Default fallback
    $default_header = 'header1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific header in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_header = $wpdb->get_var($wpdb->prepare(
            "SELECT header_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific header set, validate and use it
        if (!empty($page_header)) {
            $validated = staircase_validate_template_name($page_header, 'header');
            if ($validated) {
                return $validated;
            }
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_header FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, validate and use it
    if (!empty($site_default)) {
        $validated = staircase_validate_template_name($site_default, 'header');
        if ($validated) {
            return $validated;
        }
    }
    
    // Otherwise return hard-coded default
    return $default_header;
}

/**
 * Get the footer template to use for the current page
 * 
 * @return string The footer template name (e.g., 'footer1')
 */
function staircase_get_footer_template() {
    global $wpdb;
    
    // Default fallback
    $default_footer = 'footer1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific footer in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_footer = $wpdb->get_var($wpdb->prepare(
            "SELECT footer_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific footer set, validate and use it
        if (!empty($page_footer)) {
            $validated = staircase_validate_template_name($page_footer, 'footer');
            if ($validated) {
                return $validated;
            }
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_footer FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, validate and use it
    if (!empty($site_default)) {
        $validated = staircase_validate_template_name($site_default, 'footer');
        if ($validated) {
            return $validated;
        }
    }
    
    // Otherwise return hard-coded default
    return $default_footer;
}

/**
 * Get the sidebar template to use for the current page
 * 
 * @return string The sidebar template name (e.g., 'sidebar1')
 */
function staircase_get_sidebar_template() {
    global $wpdb;
    
    // Default fallback
    $default_sidebar = 'sidebar1';
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific sidebar in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        $page_sidebar = $wpdb->get_var($wpdb->prepare(
            "SELECT sidebar_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
            $post_id
        ));
        
        // If page has specific sidebar set, validate and use it
        if (!empty($page_sidebar)) {
            $validated = staircase_validate_template_name($page_sidebar, 'sidebar');
            if ($validated) {
                return $validated;
            }
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    $site_default = $wpdb->get_var(
        "SELECT site_default_sidebar FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
    );
    
    // If site default exists, validate and use it
    if (!empty($site_default)) {
        $validated = staircase_validate_template_name($site_default, 'sidebar');
        if ($validated) {
            return $validated;
        }
    }
    
    // Otherwise return hard-coded default
    return $default_sidebar;
}

/**
 * Get the anteheader template to use for the current page
 * 
 * @return string|null The anteheader template name (e.g., 'anteheader1') or null if none
 */
function staircase_get_anteheader_template() {
    global $wpdb;
    
    // Default - no anteheader
    $default_anteheader = null;
    
    // Get current post/page ID
    $post_id = get_the_ID();
    
    // First check for page-specific anteheader in wp_pylons
    if ($post_id) {
        $pylons_table = $wpdb->prefix . 'pylons';
        
        // Check if column exists first
        $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $pylons_table LIKE 'anteheader_desired'");
        if ($column_exists) {
            $page_anteheader = $wpdb->get_var($wpdb->prepare(
                "SELECT anteheader_desired FROM $pylons_table WHERE rel_wp_post_id = %d",
                $post_id
            ));
            
            // If page has specific anteheader set, validate and use it
            if (!empty($page_anteheader)) {
                $validated = staircase_validate_template_name($page_anteheader, 'anteheader');
                if ($validated) {
                    return $validated;
                }
            }
        }
    }
    
    // Check for site-wide default in wp_zen_sitespren
    $sitespren_table = $wpdb->prefix . 'zen_sitespren';
    
    // Check if column exists first
    $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $sitespren_table LIKE 'site_default_anteheader'");
    if ($column_exists) {
        $site_default = $wpdb->get_var(
            "SELECT site_default_anteheader FROM $sitespren_table WHERE wppma_id = 1 LIMIT 1"
        );
        
        // If site default exists, validate and use it
        if (!empty($site_default)) {
            $validated = staircase_validate_template_name($site_default, 'anteheader');
            if ($validated) {
                return $validated;
            }
        }
    }
    
    // Otherwise return null (no anteheader)
    return $default_anteheader;
}

/**
 * Render the selected anteheader template (if any)
 * This function loads and executes the appropriate anteheader file
 */
function staircase_render_selected_anteheader() {
    $anteheader_template = staircase_get_anteheader_template();
    
    // If no anteheader, return early
    if (empty($anteheader_template)) {
        return;
    }
    
    // First try new directory structure
    $anteheader_file = get_template_directory() . '/anteheaders/' . $anteheader_template . '/' . $anteheader_template . '.php';
    
    // Check if anteheader file exists
    if (file_exists($anteheader_file)) {
        require_once $anteheader_file;
        
        // Call the render function for the specific anteheader
        $render_function = 'render_' . $anteheader_template;
        if (function_exists($render_function)) {
            echo "<!-- Rendering AnteHeader: $anteheader_template -->\n";
            $render_function();
        }
    }
}

/**
 * Render the selected header template
 * This function loads and executes the appropriate header file
 */
function staircase_render_selected_header() {
    $header_template = staircase_get_header_template();
    
    // Build path to header file (new structure only)
    $header_file = get_template_directory() . '/headers/' . $header_template . '/' . $header_template . '.php';
    
    // Check if header file exists
    if (file_exists($header_file)) {
        require_once $header_file;
        
        // Call the render function for the specific header
        $render_function = 'render_' . $header_template;
        
        if (function_exists($render_function)) {
            echo "<!-- Rendering Header: $header_template -->\n";
            $render_function();
        } else {
            echo "<!-- Header function not found: $render_function -->\n";
        }
    } else {
        echo "<!-- Header file not found: $header_file -->\n";
    }
}

/**
 * Render the selected footer template
 * This function loads and executes the appropriate footer file
 */
function staircase_render_selected_footer() {
    $footer_template = staircase_get_footer_template();
    
    // Build path to footer file (new structure only)
    $footer_file = get_template_directory() . '/footers/' . $footer_template . '/' . $footer_template . '.php';
    
    // Check if footer file exists
    if (file_exists($footer_file)) {
        require_once $footer_file;
        
        // Call the render function for the specific footer
        $render_function = 'render_' . $footer_template;
        
        if (function_exists($render_function)) {
            echo "<!-- Rendering Footer: $footer_template -->\n";
            $render_function();
        } else {
            echo "<!-- Footer function not found: $render_function -->\n";
        }
    } else {
        echo "<!-- Footer file not found: $footer_file -->\n";
    }
}

/**
 * Render the selected sidebar template
 * This function loads and executes the appropriate sidebar file
 */
function staircase_render_selected_sidebar() {
    $sidebar_template = staircase_get_sidebar_template();
    
    // First try new directory structure
    $sidebar_file = get_template_directory() . '/sidebars/' . $sidebar_template . '/' . $sidebar_template . '.php';
    
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
        // For now, sidebars are optional
        echo "<!-- Sidebar not implemented: $sidebar_template -->\n";
    }
}