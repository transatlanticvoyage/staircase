<?php
/**
 * Sidebar Template
 * 
 * This template uses the centralized template selector to determine
 * which sidebar to display based on the hierarchy:
 * 1. Page-specific setting (wp_pylons.sidebar_desired)
 * 2. Site default setting (wp_zen_sitespren.site_default_sidebar)  
 * 3. Hard-coded default (homeservice_sidebar_1)
 *
 * @package Staircase
 */

// Use the centralized template selector for ALL pages
// This checks wp_pylons.sidebar_desired first, then wp_zen_sitespren.site_default_sidebar, 
// then falls back to homeservice_sidebar_1
staircase_render_selected_sidebar();