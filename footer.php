<?php
// Use the centralized template selector for ALL pages
// This checks wp_pylons.footer_desired first, then wp_zen_sitespren.site_default_footer, 
// then falls back to homeservice_footer_1
staircase_render_selected_footer();
?>

<?php wp_footer(); ?>
</body>
</html>