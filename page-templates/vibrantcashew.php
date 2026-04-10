<?php
/**
 * Vibrantcashew Page Template
 * Full HTML expanse template for complete custom design control
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb, $post;

// Get the current post ID
$post_id = get_the_ID();

// Fetch cashew_html_expanse and expanse_width from wp_pylons
$pylons_table = $wpdb->prefix . 'pylons';
$pylon_data = $wpdb->get_row($wpdb->prepare(
    "SELECT cashew_html_expanse, expanse_width, show_polyansk_custom_page_section,
            expanse1, expanse2, expanse3, expanse4, expanse5,
            expanse6, expanse7, expanse8, expanse9, expanse10,
            trinket1include, trinket1position, trinket1command,
            trinket2include, trinket2position, trinket2command,
            trinket3include, trinket3position, trinket3command
     FROM {$pylons_table} WHERE rel_wp_post_id = %d",
    $post_id
), ARRAY_A);

$cashew_content = $pylon_data['cashew_html_expanse'] ?? '';
$expanse_width = $pylon_data['expanse_width'] ?? 'full';

// Normalize expanse_width value
if (empty($expanse_width) || !in_array($expanse_width, ['full', 'partial'])) {
    $expanse_width = 'full';
}

// Build ordered slots: position 0 = cashew_html_expanse, positions 1-10 = expanse1-10
$slots = array();
$slots[0] = $cashew_content;
for ($i = 1; $i <= 10; $i++) {
    $slots[$i] = $pylon_data['expanse' . $i] ?? '';
}

// Collect active trinkets and render their output
$trinket_outputs = array(); // keyed by position
if (class_exists('TrinketCommands')) {
    for ($t = 1; $t <= 3; $t++) {
        if (!empty($pylon_data['trinket' . $t . 'include']) && !empty($pylon_data['trinket' . $t . 'command'])) {
            $pos = intval($pylon_data['trinket' . $t . 'position'] ?? 0);
            $html = TrinketCommands::execute($pylon_data['trinket' . $t . 'command']);
            if ($html !== null) {
                $trinket_outputs[$pos][] = $html;
            }
        }
    }
}

// Build final output: each slot followed by any trinkets at that position
$final_output = '';
for ($i = 0; $i <= 10; $i++) {
    if (!empty($slots[$i])) {
        $final_output .= $slots[$i];
    }
    if (isset($trinket_outputs[$i])) {
        foreach ($trinket_outputs[$i] as $trinket_html) {
            $final_output .= $trinket_html;
        }
    }
}

// If nothing at all, show placeholder
if (empty(trim($final_output))) {
    $final_output = '<div style="padding: 50px; text-align: center; background: #f5f5f5; margin: 20px;">
        <h2 style="color: #8B4513; font-size: 2em; margin-bottom: 20px;">Vibrantcashew Template</h2>
        <p style="color: #666; font-size: 1.1em;">No content has been added to cashew_html_expanse yet.</p>
        <p style="color: #999; font-size: 0.9em; margin-top: 20px;">Edit this page in WordPress admin to add your custom HTML content.</p>
    </div>';
}

?>
<style>
    /* Vibrantcashew specific styles */
    <?php if ($expanse_width === 'partial'): ?>
        /* Partial width - constrained with max-width */
        .vibrantcashew-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Allow theme's default container behavior for partial */
        .site-content .vibrantcashew-wrapper,
        .entry-content .vibrantcashew-wrapper,
        .content-area .vibrantcashew-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
    <?php else: ?>
        /* Full width - default behavior */
        .vibrantcashew-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Remove any container constraints from theme */
        .site-content .vibrantcashew-wrapper,
        .entry-content .vibrantcashew-wrapper,
        .content-area .vibrantcashew-wrapper {
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Ensure full width even with theme constraints */
        body.page-template-vibrantcashew .site-content,
        body.page-template-vibrantcashew .entry-content,
        body.page-template-vibrantcashew .content-area,
        body.vibrantcashew-template .site-content,
        body.vibrantcashew-template .entry-content,
        body.vibrantcashew-template .content-area {
            max-width: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Remove gap between cashew content and footer */
        body.vibrantcashew-template main#main {
            padding: 0 !important;
            margin: 0 !important;
        }
    <?php endif; ?>
</style>

<?php
// Polyansk service categories tiles (shown if pylon flag is true)
if (!empty($pylon_data['show_polyansk_custom_page_section']) && class_exists('Polyansk_Service_Categories_Tiles')) {
    echo Polyansk_Service_Categories_Tiles::get_instance()->render($post_id);
}
?>

<div class="vibrantcashew-wrapper">
    <?php
    // Output raw HTML - no wpautop or processing
    // User has full control over HTML structure
    // Includes cashew_html_expanse, expanse1-10, and trinkets in position order
    echo $final_output;
    ?>
</div>

