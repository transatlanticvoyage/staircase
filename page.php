<?php
/**
 * The template for displaying all pages
 *
 * @package Staircase
 */

get_header();
?>

<?php
// Check if this is a bilberry template page
$current_template = staircase_get_current_template();

if ($current_template === 'bilberry') {
    // Use bilberry template rendering
    while (have_posts()): the_post();
        staircase_bilberry_template();
    endwhile;
} else {
    // Use standard template rendering
    
    // Render Chen cards for cherry template right after hero
    if ($current_template === 'cherry' || $current_template === 'homepage-cherry') {
        staircase_render_chen_cards_box();
    }
    ?>
    <main class="site-content">
        <div class="container">
            <?php
            while (have_posts()): the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if (!staircase_should_show_hero()): // Only show title if hero is not displayed ?>
                        <header class="entry-header">
                            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        </header>
                    <?php endif; ?>
                    
                    <?php if (has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entry-content">
                        <?php
                        the_content();
                        
                        wp_link_pages(array(
                            'before' => '<div class="page-links">Pages: ',
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                    
                    <?php if (get_edit_post_link()): ?>
                        <footer class="entry-footer">
                            <?php
                            edit_post_link(
                                sprintf(
                                    wp_kses(
                                        __('Edit <span class="screen-reader-text">%s</span>', 'staircase'),
                                        array(
                                            'span' => array(
                                                'class' => array(),
                                            ),
                                        )
                                    ),
                                    wp_kses_post(get_the_title())
                                ),
                                '<span class="edit-link">',
                                '</span>'
                            );
                            ?>
                        </footer>
                    <?php endif; ?>
                </article>
                
                <?php
                // If comments are open or we have at least one comment, load up the comment template
                if (comments_open() || get_comments_number()):
                    comments_template();
                endif;
                
            endwhile;
            ?>
        </div>
    </main>
    <?php
}
?>

<?php
// Only render Cherry template boxes for non-bilberry templates
if ($current_template !== 'bilberry') {
    staircase_render_cherry_template_boxes();
}

get_footer();