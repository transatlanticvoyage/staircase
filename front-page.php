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

// Add Nile Map Box section for homepage-cherry template
if ($current_template === 'homepage-cherry') {
    ?>
    <!-- Nile Map Box Section -->
    <section class="nile-map-box">
        <div class="map-header">
            <h3>Find us on the map (nile map box)</h3>
        </div>
        <div class="map-embed-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d429178.08504434285!2d-97.06860229062499!3d32.8209745!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864c19f77b45974b%3A0xb9ec9ba4f647678f!2sDallas%2C%20TX!5e0!3m2!1sen!2sus!4v1703100000000!5m2!1sen!2sus"
                width="100%" 
                height="275" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Our Location - Dallas, TX">
            </iframe>
        </div>
    </section>
    
    <!-- Victoria Blog Box Section -->
    <section class="victoria-blog-box">
        <div class="blog-box-container">
            <h2>Top Tips (victoria blog box)</h2>
            <p class="blog-box-subtitle">Key insights from our team</p>
            
            <div class="blog-posts-grid">
                <?php
                // Get the 3 most recent blog posts
                $recent_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_posts->have_posts()) :
                    while ($recent_posts->have_posts()) : $recent_posts->the_post();
                        $author_id = get_the_author_meta('ID');
                        $author_name = get_the_author();
                        $post_date = get_the_date('M j, Y');
                        $post_title = get_the_title();
                        $post_excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20, '...');
                        $post_link = get_permalink();
                        ?>
                        <div class="blog-post-card">
                            <div class="post-meta">
                                <span class="post-date"><?php echo esc_html($post_date); ?></span>
                                <span class="post-author">By <?php echo esc_html($author_name); ?></span>
                            </div>
                            <h3 class="post-title">
                                <a href="<?php echo esc_url($post_link); ?>"><?php echo esc_html($post_title); ?></a>
                            </h3>
                            <div class="post-excerpt"><?php echo esc_html($post_excerpt); ?></div>
                            <a href="<?php echo esc_url($post_link); ?>" class="read-more-link">Read More</a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    // Fallback if no posts exist
                    ?>
                    <div class="no-posts-message">
                        <p>No blog posts available yet. Check back soon!</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            
            <?php 
            // Get the blog page URL
            $blog_page_id = get_option('page_for_posts');
            if ($blog_page_id) :
                $blog_page_url = get_permalink($blog_page_id);
            ?>
                <div class="blog-button-container">
                    <a href="<?php echo esc_url($blog_page_url); ?>" class="go-to-blog-btn">Go To Blog</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
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

/* Nile Map Box Styles */
.nile-map-box {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nile-map-box .map-header {
    background-color: #f8f9fa;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: 1px solid #e9ecef;
}

.nile-map-box .map-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    color: #2c3e50;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.nile-map-box .map-embed-container {
    width: 100%;
    height: 275px;
    position: relative;
    overflow: hidden;
}

.nile-map-box .map-embed-container iframe {
    width: 100%;
    height: 100%;
    display: block;
}

/* Mobile optimization for Nile Map Box */
@media (max-width: 768px) {
    .nile-map-box .map-header {
        height: 50px;
    }
    
    .nile-map-box .map-header h3 {
        font-size: 14px;
        padding: 0 15px;
        text-align: center;
    }
    
    .nile-map-box .map-embed-container {
        height: 250px;
    }
}

@media (max-width: 480px) {
    .nile-map-box .map-embed-container {
        height: 200px;
    }
}

/* Victoria Blog Box Styles */
.victoria-blog-box {
    width: 100%;
    padding: 60px 20px;
    background-color: #ffffff;
}

.victoria-blog-box .blog-box-container {
    max-width: 1280px;
    margin: 0 auto;
    text-align: center;
}

.victoria-blog-box h2 {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.victoria-blog-box .blog-box-subtitle {
    font-size: 20px;
    font-weight: normal;
    color: #6c757d;
    margin: 0 0 40px 0;
}

.victoria-blog-box .blog-posts-grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 40px;
    flex-wrap: nowrap;
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
}

.victoria-blog-box .blog-post-card {
    flex: 1;
    max-width: 400px;
    height: 300px;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    text-align: left;
}

.victoria-blog-box .blog-post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

.victoria-blog-box .post-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 14px;
    color: #6c757d;
}

.victoria-blog-box .post-date {
    font-weight: 500;
}

.victoria-blog-box .post-author {
    font-style: italic;
}

.victoria-blog-box .post-title {
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 15px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.victoria-blog-box .post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.victoria-blog-box .post-title a:hover {
    color: #275fd2;
}

.victoria-blog-box .post-excerpt {
    font-size: 15px;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: auto;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.victoria-blog-box .read-more-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    margin-top: 15px;
    display: inline-block;
    transition: color 0.3s ease;
}

.victoria-blog-box .read-more-link:hover {
    color: #0056b3;
    text-decoration: underline;
}

.victoria-blog-box .blog-button-container {
    margin-top: 40px;
}

.victoria-blog-box .go-to-blog-btn {
    display: inline-block;
    background-color: #007bff;
    color: #ffffff;
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.victoria-blog-box .go-to-blog-btn:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.victoria-blog-box .no-posts-message {
    padding: 40px;
    background-color: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;
    font-size: 16px;
}

/* Mobile optimization for Victoria Blog Box */
@media (max-width: 1200px) {
    .victoria-blog-box .blog-posts-grid {
        gap: 20px;
        padding: 0 20px;
    }
    
    .victoria-blog-box .blog-post-card {
        max-width: 350px;
    }
}

@media (max-width: 768px) {
    .victoria-blog-box {
        padding: 40px 15px;
    }
    
    .victoria-blog-box h2 {
        font-size: 20px;
    }
    
    .victoria-blog-box .blog-box-subtitle {
        font-size: 18px;
        margin-bottom: 30px;
    }
    
    .victoria-blog-box .blog-posts-grid {
        flex-direction: column;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .victoria-blog-box .blog-post-card {
        flex: none;
        width: 100%;
        max-width: 400px;
        height: auto;
        min-height: 280px;
    }
    
    .victoria-blog-box .post-title {
        font-size: 16px;
    }
    
    .victoria-blog-box .post-excerpt {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .victoria-blog-box h2 {
        font-size: 18px;
    }
    
    .victoria-blog-box .blog-box-subtitle {
        font-size: 16px;
    }
    
    .victoria-blog-box .blog-post-card {
        padding: 20px;
    }
    
    .victoria-blog-box .go-to-blog-btn {
        padding: 10px 24px;
        font-size: 15px;
    }
}
</style>

<?php
get_footer();