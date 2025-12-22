<?php
/**
 * The template for displaying the homepage
 *
 * @package Staircase
 */

get_header();
?>

<main class="site-content">
    <div class="container">
        <?php
        // If this is a static page
        if (have_posts()):
            while (have_posts()): the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if (!is_front_page()): // Only show title if not set as front page ?>
                        <header class="entry-header">
                            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        </header>
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
                </article>
                <?php
            endwhile;
            
        // If this is set to show latest posts
        elseif (is_home()):
            ?>
            <div class="latest-posts">
                <h2>Latest Posts</h2>
                <div class="posts-grid">
                    <?php
                    $recent_posts = new WP_Query(array(
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                    ));
                    
                    if ($recent_posts->have_posts()):
                        while ($recent_posts->have_posts()): $recent_posts->the_post();
                            ?>
                            <article class="post-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="post-card-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-card-content">
                                    <h3 class="post-card-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <div class="post-card-meta">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                    <div class="post-card-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="post-card-link">
                                        Read More &rarr;
                                    </a>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                        ?>
                        <p>No posts found.</p>
                        <?php
                    endif;
                    ?>
                </div>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
// Add Our Services section for homepage cherry template when enabled
$current_template = staircase_get_current_template();
$post_id = get_the_ID();

// Debug output
echo "<!-- OSB Debug: Post ID = $post_id, Template = '$current_template' -->\n";

if ($current_template === 'homepage-cherry') {
    // Check if OSB is enabled for this page
    global $wpdb;
    $pylons_table = $wpdb->prefix . 'pylons';
    
    $osb_enabled = $wpdb->get_var($wpdb->prepare(
        "SELECT osb_is_enabled FROM {$pylons_table} WHERE rel_wp_post_id = %d",
        $post_id
    ));
    
    echo "<!-- OSB Debug: OSB Enabled = " . ($osb_enabled ? 'YES' : 'NO') . " -->\n";
    
    if ($osb_enabled) {
        echo "<!-- OSB Debug: Rendering Our Services Section -->\n";
        staircase_our_services_section();
        echo "<!-- OSB Debug: Our Services Section Rendered -->\n";
    } else {
        echo "<!-- OSB Debug: OSB not enabled for this page -->\n";
    }
} else {
    echo "<!-- OSB Debug: Not a homepage-cherry template, OSB will not render -->\n";
}
?>

<style>
/* Homepage specific styles */
.latest-posts {
    margin: 3rem 0;
}

.latest-posts h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    text-align: center;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.post-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.post-card-thumbnail img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.post-card-content {
    padding: 1.5rem;
}

.post-card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.post-card-title a {
    color: #333;
    text-decoration: none;
}

.post-card-title a:hover {
    color: #0073aa;
}

.post-card-meta {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.post-card-excerpt {
    color: #666;
    margin-bottom: 1rem;
}

.post-card-link {
    color: #0073aa;
    text-decoration: none;
    font-weight: 500;
}

.post-card-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();