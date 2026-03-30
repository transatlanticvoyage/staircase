<?php
/**
 * Homeservice Footer 2
 * A1 Chimney style footer with service links and contact information
 * Features: Service categories, contact info, copyright section
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the homeservice footer 2
 */
function render_homeservice_footer_2() {
    // Enqueue footer-specific assets
    $assets_url = get_template_directory_uri() . '/footers/homeservice_footer_2_assets/';
    
    wp_enqueue_style('homeservice-footer-2-styles', $assets_url . 'css/footer-styles.css', array(), '1.0.0');
    
    // Get the footer HTML content
    $footer_file = get_template_directory() . '/footers/homeservice_footer_2_assets/footer-content.html';
    
    if (file_exists($footer_file)) {
        $footer_html = file_get_contents($footer_file);
        
        // Replace dynamic content
        $footer_html = str_replace('{{CURRENT_YEAR}}', date('Y'), $footer_html);
        $footer_html = str_replace('{{SITE_NAME}}', get_bloginfo('name'), $footer_html);
        $footer_html = str_replace('{{HOME_URL}}', esc_url(home_url('/')), $footer_html);
        
        // Replace phone if available
        $phone_html = '';
        if (function_exists('staircase_get_formatted_phone')) {
            $phone_raw = staircase_get_header_phone();
            $phone_formatted = staircase_get_formatted_phone();
            if (!empty($phone_raw)) {
                $phone_html = '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone_raw)) . '">' . esc_html($phone_formatted) . '</a>';
            }
        }
        $footer_html = str_replace('{{PHONE_PLACEHOLDER}}', $phone_html, $footer_html);
        
        echo $footer_html;
    } else {
        // Fallback footer
        ?>
        <footer class="zaramax-footer homeservice-footer-2">
            <div class="homeservice_footer_2_container">
                <p style="padding: 40px; text-align: center; background: #222; color: #fff;">
                    © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.
                </p>
            </div>
        </footer>
        <?php
    }
}