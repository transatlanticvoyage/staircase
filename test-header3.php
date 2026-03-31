<?php
/**
 * Template Name: Test Header3
 * Description: Test page specifically for header3
 */

// Force header3 for this test
add_filter('staircase_header_override', function() {
    return 'header3';
});

get_header();
?>

<div style="padding: 40px; min-height: 500px; background: #f5f5f5;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1>Header3 Test Page</h1>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h2>Header3 Features:</h2>
            <ul style="line-height: 2;">
                <li>✅ Simple, clean design with semantic HTML</li>
                <li>✅ Logo area (image or text fallback)</li>
                <li>✅ Silkweaver menu integration</li>
                <li>✅ Mobile responsive with hamburger menu</li>
                <li>✅ Smooth hover effects</li>
                <li>✅ Dropdown menu support</li>
                <li>✅ Current page highlighting</li>
            </ul>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h2>Database Integration:</h2>
            <p>This header will be selected when:</p>
            <ul style="line-height: 2;">
                <li>Page-specific: <code>wp_pylons.header_desired = 'header3'</code></li>
                <li>Site-wide default: <code>wp_zen_sitespren.site_default_header = 'header3'</code></li>
                <li>Legacy value: <code>'homeservice_header_3'</code> automatically converts to <code>'header3'</code></li>
            </ul>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h2>File Structure:</h2>
            <pre style="background: #f5f5f5; padding: 15px; overflow-x: auto;">
/headers/header3/
├── header3.php           (Main PHP file with render_header3())
└── assets/
    └── css/
        └── styles.css    (All styles with h3- prefix)
            </pre>
        </div>
    </div>
</div>

<?php
get_footer();
?>