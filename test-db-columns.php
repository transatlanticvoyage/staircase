<?php
/**
 * Test script to check and add missing database columns
 */

// Load WordPress
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb;

$pylons_table = $wpdb->prefix . 'pylons';
$zen_table = $wpdb->prefix . 'zen_sitespren';

echo "<h2>Database Column Check</h2>";

// Check if anteheader_desired exists in pylons
$column_exists = $wpdb->get_var("SHOW COLUMNS FROM $pylons_table LIKE 'anteheader_desired'");
if ($column_exists) {
    echo "✅ Column 'anteheader_desired' exists in $pylons_table<br>";
} else {
    echo "❌ Column 'anteheader_desired' missing in $pylons_table<br>";
    echo "Adding column...<br>";
    $result = $wpdb->query("ALTER TABLE $pylons_table ADD COLUMN anteheader_desired TEXT DEFAULT NULL");
    if ($result !== false) {
        echo "✅ Successfully added 'anteheader_desired' column<br>";
    } else {
        echo "❌ Failed to add column. Error: " . $wpdb->last_error . "<br>";
    }
}

// Check if site_default_anteheader exists in zen_sitespren
$column_exists = $wpdb->get_var("SHOW COLUMNS FROM $zen_table LIKE 'site_default_anteheader'");
if ($column_exists) {
    echo "✅ Column 'site_default_anteheader' exists in $zen_table<br>";
} else {
    echo "❌ Column 'site_default_anteheader' missing in $zen_table<br>";
    echo "Adding column...<br>";
    $result = $wpdb->query("ALTER TABLE $zen_table ADD COLUMN site_default_anteheader TEXT DEFAULT NULL");
    if ($result !== false) {
        echo "✅ Successfully added 'site_default_anteheader' column<br>";
    } else {
        echo "❌ Failed to add column. Error: " . $wpdb->last_error . "<br>";
    }
}

echo "<br><h3>All columns in $pylons_table:</h3>";
$columns = $wpdb->get_results("SHOW COLUMNS FROM $pylons_table");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Column Name</th><th>Type</th><th>Default</th></tr>";
foreach ($columns as $col) {
    $highlight = in_array($col->Field, ['header_desired', 'footer_desired', 'sidebar_desired', 'anteheader_desired']) ? 'style="background: #ffeb3b;"' : '';
    echo "<tr $highlight><td>{$col->Field}</td><td>{$col->Type}</td><td>{$col->Default}</td></tr>";
}
echo "</table>";
?>