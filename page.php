<?php
/**
 * The template for displaying all pages
 *
 * @package Staircase
 */
// Test comment for VSCode trigger - page.php

get_header();
?>

<?php
$current_template = staircase_get_current_template();

// Cherry templates need full width rendering, others use container
if ($current_template === 'cherry' || $current_template === 'homepage-cherry') {
    // Cherry template renders without container constraint for full width sections
    while (have_posts()): the_post();
        staircase_render_template();
    endwhile;
} else {
    // Other templates use standard container layout
    ?>
    <main class="site-content">
        <div class="container">
            <?php
            while (have_posts()): the_post();
                staircase_render_template();
            endwhile;
            ?>
        </div>
    </main>
    <?php
}
?>

<?php
get_footer();