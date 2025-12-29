<?php
/**
 * The template for displaying all single posts
 *
 * @package Staircase
 */
// Test comment for VSCode trigger - single.php

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
get_footer();