<?php
/**
 * The template for displaying all pages
 *
 * @package Staircase
 */
// Test comment for VSCode trigger - page.php

get_header();
?>

<main id="main" role="main">
<?php
$current_template = staircase_get_current_template();

// Cherry, vibrantcashew, and galleryberry templates need full width rendering, others use container
if ($current_template === 'cherry' || $current_template === 'homepage-cherry' || $current_template === 'vibrantcashew' || $current_template === 'galleryberry') {
    // These templates render without container constraint for full width sections
    while (have_posts()): the_post();
        staircase_render_template();
    endwhile;
} else {
    // Other templates use standard container layout
    ?>
    <div class="site-content">
        <div class="container">
            <?php
            while (have_posts()): the_post();
                staircase_render_template();
            endwhile;
            ?>
        </div>
    </div>
    <?php
}
?>
</main>

<?php
get_footer();