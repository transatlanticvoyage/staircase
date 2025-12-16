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

<?php wp_footer(); ?>
</body>
</html>