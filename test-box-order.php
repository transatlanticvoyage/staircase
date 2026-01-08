<?php
/**
 * Template Name: Debug Box Order
 */

// WordPress is loaded, so we have access to $wpdb
global $wpdb, $post;

// Allow direct access or use on a page
$test_post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 1977;

echo "<h1>Box Ordering Debug for Post {$test_post_id}</h1>";

// Check if table exists
$table_name = $wpdb->prefix . 'box_orders';
echo "<h2>1. Table Check</h2>";
echo "Table name: <code>{$table_name}</code><br>";

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");
echo "Table exists: <strong>" . ($table_exists ? "YES ✓" : "NO ✗") . "</strong><br><br>";

if ($table_exists) {
    // Check records for the post
    echo "<h2>2. Records for Post {$test_post_id}</h2>";
    $records = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE rel_post_id = %d",
        $test_post_id
    ));
    
    if ($records) {
        foreach ($records as $record) {
            echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 3px solid #0073aa;'>";
            echo "<strong>Record ID:</strong> {$record->item_id}<br>";
            echo "<strong>Post ID:</strong> {$record->rel_post_id}<br>";
            echo "<strong>Is Active:</strong> <span style='color: " . ($record->is_active ? "green" : "red") . "'>" . ($record->is_active ? "YES (1)" : "NO (0)") . "</span><br>";
            echo "<strong>JSON Data:</strong><br>";
            echo "<pre style='background: white; padding: 10px; overflow-x: auto;'>" . htmlspecialchars($record->box_order_json) . "</pre>";
            
            // Try to decode JSON
            if ($record->box_order_json) {
                $decoded = json_decode($record->box_order_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<strong>Decoded Order:</strong><br>";
                    asort($decoded);
                    echo "<ol>";
                    foreach ($decoded as $box => $order) {
                        echo "<li>{$box} (order: {$order})</li>";
                    }
                    echo "</ol>";
                } else {
                    echo "<strong style='color: red;'>JSON Error:</strong> " . json_last_error_msg() . "<br>";
                }
            }
            echo "</div>";
        }
    } else {
        echo "<strong style='color: red;'>No records found for post {$test_post_id}</strong><br>";
    }
    
    // Test the exact query from template
    echo "<h2>3. Template Query Test</h2>";
    echo "<p>Testing the exact query used in cherry template:</p>";
    
    $custom_order = $wpdb->get_row($wpdb->prepare(
        "SELECT box_order_json FROM {$table_name} WHERE rel_post_id = %d AND is_active = 1",
        $test_post_id
    ));
    
    echo "<strong>SQL Query:</strong><br>";
    echo "<code style='background: #f5f5f5; padding: 5px; display: block; margin: 10px 0;'>" . $wpdb->last_query . "</code>";
    
    if ($custom_order) {
        echo "<strong style='color: green;'>✓ CUSTOM ORDER FOUND!</strong><br>";
        echo "<strong>JSON Data:</strong><br>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($custom_order->box_order_json) . "</pre>";
    } else {
        echo "<strong style='color: red;'>✗ NO ACTIVE CUSTOM ORDER FOUND</strong><br>";
        echo "<p>This means the cherry template will use the default hardcoded order.</p>";
    }
    
    // Check for any database errors
    if ($wpdb->last_error) {
        echo "<strong style='color: red;'>Database Error:</strong> " . $wpdb->last_error . "<br>";
    }
} else {
    echo "<h2 style='color: red;'>⚠ Table Does Not Exist!</h2>";
    echo "<p>The <code>wp_box_orders</code> table needs to be created. Try deactivating and reactivating the Ruplin plugin.</p>";
}

// Check post template
echo "<h2>4. Post Template Check</h2>";
$pylons_table = $wpdb->prefix . 'pylons';
$template = $wpdb->get_var($wpdb->prepare(
    "SELECT staircase_page_template_desired FROM {$pylons_table} WHERE rel_wp_post_id = %d",
    $test_post_id
));
echo "Post {$test_post_id} template: <strong>" . ($template ?: "NOT FOUND") . "</strong><br>";

if ($template !== 'cherry') {
    echo "<p style='color: orange;'>⚠ Note: Post is not using cherry template. Box ordering only works for cherry template.</p>";
}

// Show current post ID for reference
echo "<h2>5. Current Page Info</h2>";
echo "Current Post ID (this debug page): " . (isset($post->ID) ? $post->ID : "N/A") . "<br>";
echo "Test different post: Add <code>?post_id=XXX</code> to the URL<br>";
?>