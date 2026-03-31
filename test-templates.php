<?php
/**
 * Template Name: Test Templates
 * Description: Test page to verify all headers and footers work with new naming
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the template selector
require_once get_template_directory() . '/inc/template-selector.php';

// Test conversion functions
$test_conversions = [
    'header' => [
        'homeservice_header_1' => 'header1',
        'homeservice_header_2' => 'header2',
        'homeservice_header_3' => 'header3',
        'casino_header_1' => 'header1',
        'casino_header_2' => 'header1',
        'header1' => 'header1', // Already in new format
        'header2' => 'header2', // Already in new format
    ],
    'footer' => [
        'homeservice_footer_1' => 'footer1',
        'homeservice_footer_2' => 'footer2',
        'homeservice_footer_3' => 'footer3',
        'casino_footer_1' => 'footer1',
        'casino_footer_2' => 'footer1',
        'footer1' => 'footer1', // Already in new format
        'footer2' => 'footer2', // Already in new format
    ],
    'sidebar' => [
        'homeservice_sidebar_1' => 'sidebar1',
        'homeservice_sidebar_2' => 'sidebar2',
        'casino_sidebar_1' => 'sidebar1',
        'sidebar1' => 'sidebar1', // Already in new format
    ],
    'anteheader' => [
        'homeservice_anteheader_1' => 'anteheader1',
        'homeservice_anteheader_2' => 'anteheader2',
        'casino_anteheader_1' => null,
        'anteheader1' => 'anteheader1', // Already in new format
    ]
];

// Output test results
get_header();
?>

<div style="padding: 40px; background: #f5f5f5;">
    <h1>Template System Test Results</h1>
    
    <h2>1. Conversion Function Tests</h2>
    <table border="1" cellpadding="10" style="background: white; width: 100%;">
        <thead>
            <tr>
                <th>Type</th>
                <th>Input</th>
                <th>Expected Output</th>
                <th>Actual Output</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($test_conversions as $type => $conversions): ?>
                <?php foreach ($conversions as $input => $expected): ?>
                    <?php 
                    $actual = staircase_convert_legacy_template_name($input, $type);
                    $status = ($actual === $expected) ? '✅ PASS' : '❌ FAIL';
                    $row_color = ($actual === $expected) ? '#d4edda' : '#f8d7da';
                    ?>
                    <tr style="background: <?php echo $row_color; ?>;">
                        <td><?php echo $type; ?></td>
                        <td><?php echo $input; ?></td>
                        <td><?php echo $expected ?: 'null'; ?></td>
                        <td><?php echo $actual ?: 'null'; ?></td>
                        <td><?php echo $status; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h2>2. Current Template Selection</h2>
    <table border="1" cellpadding="10" style="background: white; width: 100%; margin-top: 20px;">
        <tr>
            <th>Template Type</th>
            <th>Selected Template</th>
            <th>File Exists?</th>
        </tr>
        <tr>
            <td>Header</td>
            <td><?php $header = staircase_get_header_template(); echo $header; ?></td>
            <td>
                <?php 
                $header_file = get_template_directory() . '/headers/' . $header . '/' . $header . '.php';
                $old_header_file = get_template_directory() . '/headers/homeservice_' . str_replace('header', 'header_', $header) . '.php';
                if (file_exists($header_file)) {
                    echo '✅ Yes (new structure)';
                } elseif (file_exists($old_header_file)) {
                    echo '✅ Yes (old structure)';
                } else {
                    echo '❌ No';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Footer</td>
            <td><?php $footer = staircase_get_footer_template(); echo $footer; ?></td>
            <td>
                <?php 
                $footer_file = get_template_directory() . '/footers/' . $footer . '/' . $footer . '.php';
                $old_footer_file = get_template_directory() . '/footers/homeservice_' . str_replace('footer', 'footer_', $footer) . '.php';
                if (file_exists($footer_file)) {
                    echo '✅ Yes (new structure)';
                } elseif (file_exists($old_footer_file)) {
                    echo '✅ Yes (old structure)';
                } else {
                    echo '❌ No';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Sidebar</td>
            <td><?php $sidebar = staircase_get_sidebar_template(); echo $sidebar; ?></td>
            <td>
                <?php 
                $sidebar_file = get_template_directory() . '/sidebars/' . $sidebar . '/' . $sidebar . '.php';
                echo file_exists($sidebar_file) ? '✅ Yes' : '⚠️ Not implemented';
                ?>
            </td>
        </tr>
        <tr>
            <td>Anteheader</td>
            <td><?php $anteheader = staircase_get_anteheader_template(); echo $anteheader ?: 'none'; ?></td>
            <td>
                <?php 
                if ($anteheader) {
                    $anteheader_file = get_template_directory() . '/anteheaders/' . $anteheader . '/' . $anteheader . '.php';
                    echo file_exists($anteheader_file) ? '✅ Yes' : '⚠️ Not implemented';
                } else {
                    echo 'N/A';
                }
                ?>
            </td>
        </tr>
    </table>
    
    <h2>3. Available Template Files</h2>
    <table border="1" cellpadding="10" style="background: white; width: 100%; margin-top: 20px;">
        <tr>
            <th>Type</th>
            <th>Available Files</th>
        </tr>
        <tr>
            <td>Headers</td>
            <td>
                <?php
                $headers_dir = get_template_directory() . '/headers/';
                if (is_dir($headers_dir)) {
                    $headers = array_filter(scandir($headers_dir), function($item) use ($headers_dir) {
                        return is_dir($headers_dir . $item) && $item !== '.' && $item !== '..';
                    });
                    $header_files = array_filter(scandir($headers_dir), function($item) use ($headers_dir) {
                        return is_file($headers_dir . $item) && strpos($item, '.php') !== false;
                    });
                    
                    echo '<strong>New structure directories:</strong> ' . implode(', ', $headers) . '<br>';
                    echo '<strong>Old structure files:</strong> ' . implode(', ', $header_files);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Footers</td>
            <td>
                <?php
                $footers_dir = get_template_directory() . '/footers/';
                if (is_dir($footers_dir)) {
                    $footers = array_filter(scandir($footers_dir), function($item) use ($footers_dir) {
                        return is_dir($footers_dir . $item) && $item !== '.' && $item !== '..';
                    });
                    $footer_files = array_filter(scandir($footers_dir), function($item) use ($footers_dir) {
                        return is_file($footers_dir . $item) && strpos($item, '.php') !== false;
                    });
                    
                    echo '<strong>New structure directories:</strong> ' . implode(', ', $footers) . '<br>';
                    echo '<strong>Old structure files:</strong> ' . implode(', ', $footer_files);
                }
                ?>
            </td>
        </tr>
    </table>
    
    <h2>4. Backwards Compatibility Functions</h2>
    <table border="1" cellpadding="10" style="background: white; width: 100%; margin-top: 20px;">
        <tr>
            <th>Old Function</th>
            <th>New Function</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>render_homeservice_header_1()</td>
            <td>render_header1()</td>
            <td><?php echo (function_exists('render_homeservice_header_1') && function_exists('render_header1')) ? '✅ Both exist' : '❌ Missing'; ?></td>
        </tr>
        <tr>
            <td>render_homeservice_header_2()</td>
            <td>render_header2()</td>
            <td><?php echo (function_exists('render_homeservice_header_2') && function_exists('render_header2')) ? '✅ Both exist' : '❌ Missing'; ?></td>
        </tr>
        <tr>
            <td>render_homeservice_footer_1()</td>
            <td>render_footer1()</td>
            <td><?php echo (function_exists('render_homeservice_footer_1') && function_exists('render_footer1')) ? '✅ Both exist' : '❌ Missing'; ?></td>
        </tr>
        <tr>
            <td>render_homeservice_footer_2()</td>
            <td>render_footer2()</td>
            <td><?php echo (function_exists('render_homeservice_footer_2') && function_exists('render_footer2')) ? '✅ Both exist' : '❌ Missing'; ?></td>
        </tr>
    </table>
</div>

<?php
get_footer();
?>