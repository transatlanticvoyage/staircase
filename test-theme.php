<?php
/**
 * Template Name: Test Theme
 */

get_header();
?>

<div style="background: #e2e2e2; padding: 40px; text-align: center; margin: 20px;">
    <h1>Theme Test Page</h1>
    <p>If you can see this gray background (#e2e2e2), the Staircase theme is active and working!</p>
    <p>Current Theme: <?php echo get_template(); ?></p>
    <p>Stylesheet Directory: <?php echo get_stylesheet_directory_uri(); ?></p>
</div>

<?php
get_footer();