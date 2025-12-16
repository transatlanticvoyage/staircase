<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme.
 *
 * @package Staircase
 */

get_header();
?>

<main class="site-content">
    <div class="container">
        <?php
        if (have_posts()):
            
            // Check if this is a blog listing page
            if (is_home() && !is_front_page()):
                ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                </header>
                <?php
            endif;
            
            // Start the Loop
            while (have_posts()): the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php
                        if (is_singular()):
                            the_title('<h1 class="entry-title">', '</h1>');
                        else:
                            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                        endif;
                        
                        // Display post meta for blog posts
                        if ('post' === get_post_type()):
                            ?>
                            <div class="entry-meta">
                                <span class="posted-on">
                                    Posted on <?php echo get_the_date(); ?>
                                </span>
                                <span class="byline">
                                    by <?php the_author(); ?>
                                </span>
                            </div>
                            <?php
                        endif;
                        ?>
                    </header>
                    
                    <?php if (has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entry-content">
                        <?php
                        if (is_singular()):
                            the_content();
                            
                            wp_link_pages(array(
                                'before' => '<div class="page-links">Pages: ',
                                'after'  => '</div>',
                            ));
                        else:
                            the_excerpt();
                            ?>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">
                                Read More &rarr;
                            </a>
                            <?php
                        endif;
                        ?>
                    </div>
                    
                    <?php if (is_singular() && 'post' === get_post_type()): ?>
                        <footer class="entry-footer">
                            <div class="entry-categories">
                                Categories: <?php the_category(', '); ?>
                            </div>
                            <?php if (has_tag()): ?>
                                <div class="entry-tags">
                                    Tags: <?php the_tags('', ', '); ?>
                                </div>
                            <?php endif; ?>
                        </footer>
                    <?php endif; ?>
                </article>
                
                <?php
                // If comments are open or we have at least one comment, load up the comment template
                if (is_singular() && (comments_open() || get_comments_number())):
                    comments_template();
                endif;
                
            endwhile;
            
            // Pagination for archive pages
            if (!is_singular()):
                ?>
                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => '&larr; Previous',
                        'next_text' => 'Next &rarr;',
                    ));
                    ?>
                </div>
                <?php
            endif;
            
        else:
            ?>
            <div class="no-content">
                <h2>Nothing Found</h2>
                <p>Sorry, but nothing matched your criteria. Please try again with different keywords.</p>
                <?php get_search_form(); ?>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();