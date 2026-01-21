<?php
// Check which footer system to use
$use_zaramax_footer = get_option('zaramax_use_custom_footer', false);

// Debug: Add HTML comment to show footer system status
echo "<!-- Footer Debug: use_zaramax_footer = " . ($use_zaramax_footer ? 'true' : 'false') . ", function_exists = " . (function_exists('zaramax_render_custom_footer') ? 'true' : 'false') . " -->\n";

if ($use_zaramax_footer && function_exists('zaramax_render_custom_footer')) {
    echo "<!-- Using Zaramax Custom Footer -->\n";
    zaramax_render_custom_footer();
} else {
    echo "<!-- Using Default WordPress Footer -->\n";
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