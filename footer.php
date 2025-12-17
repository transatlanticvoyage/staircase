<?php
// Check which footer system to use
$use_zaramax_footer = get_option('zaramax_use_custom_footer', false);

if ($use_zaramax_footer && function_exists('zaramax_render_custom_footer')) {
    zaramax_render_custom_footer();
} else {
    // Default footer system
    ?>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-branding">
                    <h2><?php bloginfo('name'); ?></h2>
                    <p><?php bloginfo('description'); ?></p>
                </div>
                
                <?php if (has_nav_menu('footer')): ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'container'      => false,
                            'depth'          => 1,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
            
            <div class="site-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <?php
}
?>

<?php wp_footer(); ?>
</body>
</html>