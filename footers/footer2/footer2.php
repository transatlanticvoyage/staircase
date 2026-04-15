<?php
/**
 * Footer 2
 * Alternative footer design with same data sources as footer1
 * All classes/IDs prefixed with zh_ft2_ for style isolation
 * (Previously homeservice_footer_2)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render footer 2
 */
function render_footer2() {
    global $wpdb;

    // Enqueue footer-specific assets
    $assets_url = get_template_directory_uri() . '/footers/footer2/assets/';
    wp_enqueue_style('footer2-styles', $assets_url . 'css/footer-styles.css', array(), '1.0.1');

    // Conditionally enqueue custom enhancement stylesheet (toggle via zaramax admin)
    if (get_option('zh_ft2_custom_styles_enabled', '0') === '1') {
        wp_enqueue_style('footer2-custom-7423', $assets_url . 'css/zh_ft2_custom_7423.css', array('footer2-styles'), '1.0.0');
    }

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
    $footer_disclaimer = !empty($new_footer_disclaimer) ? $new_footer_disclaimer : $legacy_footer_disclaimer;
    $footer_legal_links = !empty($new_footer_legal_links) ? $new_footer_legal_links : $legacy_footer_legal_links;

    if (!empty($new_footer_disclaimer)) {
        $footer_hide_disclaimer = $new_hide_footer_disclaimer;
    } else {
        $footer_hide_disclaimer = $legacy_hide_disclaimer;
    }

    if (!empty($new_footer_legal_links)) {
        $footer_hide_legal_links = $new_hide_footer_legal_links;
    } else {
        $footer_hide_legal_links = 0;
    }

    // TEMPORARY: Get footer blurb from WordPress options for testing
    $footer_blurb = get_option('zaramax_temp_footer_blurb', '');

    // Get logo and phone
    $site_logo = get_option('staircase_header_logo', '');
    $phone_raw = staircase_get_header_phone();
    $phone_formatted = staircase_get_formatted_phone();
    ?>
    <footer class="zh_ft2_footer">
        <!-- Main Footer Boxes -->
        <div class="zh_ft2_main">
            <div class="zh_ft2_container">
                <div class="zh_ft2_grid">

                    <!-- Box 1: Logo & Contact -->
                    <div class="zh_ft2_box zh_ft2_box_1">
                        <?php if (!empty($site_logo)): ?>
                            <div class="zh_ft2_logo">
                                <div class="zh_ft2_logo_wrapper">
                                    <img src="<?php echo esc_url($site_logo); ?>" alt="<?php bloginfo('name'); ?>" class="zh_ft2_logo_img">
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="zh_ft2_site_title">
                                <h3><?php bloginfo('name'); ?></h3>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($footer_blurb)): ?>
                            <div class="zh_ft2_blurb">
                                <p><?php echo do_shortcode(nl2br($footer_blurb)); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($phone_raw)): ?>
                            <div class="zh_ft2_phone">
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone_raw)); ?>" class="zh_ft2_phone_button">
                                    <svg class="zh_ft2_phone_icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                    <span><?php echo esc_html($phone_formatted); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Box 2: Custom HTML -->
                    <div class="zh_ft2_box zh_ft2_box_2">
                        <?php echo wpautop(do_shortcode($footer_box2_content)); ?>
                    </div>

                    <!-- Box 3: Custom HTML -->
                    <div class="zh_ft2_box zh_ft2_box_3">
                        <?php echo wpautop(do_shortcode($footer_box3_content)); ?>
                    </div>

                    <!-- Box 4: Google Maps -->
                    <div class="zh_ft2_box zh_ft2_box_4">
                        <?php if (!empty($footer_map_heading)): ?>
                            <h3 class="zh_ft2_map_heading"><?php echo esc_html($footer_map_heading); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($footer_map_location)): ?>
                            <div class="zh_ft2_map">
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
                            <p style="color: #999; font-style: italic;">Map location not set</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="zh_ft2_bottom">
            <div class="zh_ft2_bottom_container">
                <div class="zh_ft2_bottom_grid">
                    <?php
                    // Show disclaimer only if NOT hidden
                    if (!($footer_hide_disclaimer == '1' || $footer_hide_disclaimer == 1)):
                    ?>
                        <!-- Disclaimer Area -->
                        <div class="zh_ft2_disclaimer">
                            <?php echo wpautop(do_shortcode($footer_disclaimer)); ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Show legal links only if NOT hidden
                    if (!($footer_hide_legal_links == '1' || $footer_hide_legal_links == 1)):
                    ?>
                        <!-- Legal Links Area -->
                        <div class="zh_ft2_legal_links">
                            <?php
                            if (!empty($footer_legal_links)) {
                                $legal_links_html = do_shortcode($footer_legal_links);
                                $legal_links_html = str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', $legal_links_html);
                                $legal_links_html = str_replace('   ', '&nbsp;&nbsp;&nbsp;', $legal_links_html);
                                $legal_links_html = str_replace('  ', '&nbsp;&nbsp;', $legal_links_html);
                                echo wpautop($legal_links_html);
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <?php
}
