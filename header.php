<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip to main content link for keyboard navigation -->
<a class="skip-link screen-reader-text" href="#main">Skip to main content</a>

<?php
// Use the centralized template selector for ALL pages
// This checks wp_pylons.header_desired first, then wp_zen_sitespren.site_default_header, 
// then falls back to homeservice_header_1
staircase_render_selected_header();
?>

<?php
// Display hero section if applicable
// Note: Cherry templates handle their own hero rendering internally
if (function_exists('staircase_should_show_hero') && staircase_should_show_hero()) {
    $current_template = staircase_get_current_template();
    
    // Only show hero for non-cherry templates
    // Cherry templates render hero as part of their template structure
    if ($current_template !== 'cherry' && $current_template !== 'homepage-cherry') {
        // Standard hero for content-only and other pages
        // Check if this is the blog page (not front page)
        if (is_home() && !is_front_page()) {
            $blog_page_id = get_option('page_for_posts');
            $hero_title = $blog_page_id ? get_the_title($blog_page_id) : 'Blog';
            $hero_subtitle = ''; // Blog page typically doesn't need subtitle
        } else {
            $hero_title = get_the_title() ?: get_bloginfo('name');
            $hero_subtitle = get_bloginfo('description');
        }
        staircase_hero_section($hero_title, $hero_subtitle);
    }
}
?>