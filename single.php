<?php
/**
 * The template for displaying all single posts
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
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    
                    <div class="entry-meta">
                        <span class="posted-on">
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </span>
                        <span class="byline">
                            by <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                <?php the_author(); ?>
                            </a>
                        </span>
                        <?php if (get_comments_number()): ?>
                            <span class="comments-link">
                                <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>
                
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
                
                <footer class="entry-footer">
                    <div class="entry-categories">
                        <span class="cat-label">Categories:</span>
                        <?php the_category(', '); ?>
                    </div>
                    
                    <?php if (has_tag()): ?>
                        <div class="entry-tags">
                            <span class="tag-label">Tags:</span>
                            <?php the_tags('', ', '); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_edit_post_link()): ?>
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
                    <?php endif; ?>
                </footer>
            </article>
            
            <?php
            // Post navigation
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">Previous:</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">Next:</span> <span class="nav-title">%title</span>',
            ));
            
            // If comments are open or we have at least one comment, load up the comment template
            if (comments_open() || get_comments_number()):
                comments_template();
            endif;
            
        endwhile;
        ?>
    </div>
</main>
<?php
} // End bilberry template conditional
?>

<style>
/* Single post specific styles */
.entry-meta {
    color: #666;
    font-size: 0.9rem;
    margin: 1rem 0;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.entry-meta a {
    color: #0073aa;
    text-decoration: none;
}

.entry-meta a:hover {
    text-decoration: underline;
}

.post-thumbnail {
    margin: 2rem 0;
}

.post-thumbnail img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.entry-footer {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.entry-categories,
.entry-tags {
    margin-bottom: 1rem;
}

.cat-label,
.tag-label {
    font-weight: 600;
    margin-right: 0.5rem;
}

.entry-categories a,
.entry-tags a {
    color: #0073aa;
    text-decoration: none;
}

.entry-categories a:hover,
.entry-tags a:hover {
    text-decoration: underline;
}

.post-navigation {
    margin: 3rem 0;
    padding: 2rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.post-navigation .nav-links {
    display: flex;
    justify-content: space-between;
    gap: 2rem;
}

.post-navigation .nav-previous,
.post-navigation .nav-next {
    flex: 1;
}

.post-navigation .nav-next {
    text-align: right;
}

.post-navigation .nav-subtitle {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.post-navigation .nav-title {
    display: block;
    font-weight: 600;
    color: #333;
}

.post-navigation a {
    text-decoration: none;
}

.post-navigation a:hover .nav-title {
    color: #0073aa;
}

@media (max-width: 768px) {
    .post-navigation .nav-links {
        flex-direction: column;
    }
    
    .post-navigation .nav-next {
        text-align: left;
    }
}
</style>

<?php
// Render Cherry template boxes (Nile and Victoria) if applicable (not for bilberry)
if ($current_template !== 'bilberry') {
    staircase_render_cherry_template_boxes();
}

get_footer();