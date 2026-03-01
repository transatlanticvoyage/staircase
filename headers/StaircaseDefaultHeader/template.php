<?php
/**
 * StaircaseDefaultHeader Template
 * 
 * Clean HTML structure for the header
 * All styling handled via CSS files
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;
?>

<header class="sdhdr-header" role="banner">
    <div class="sdhdr-container">
        <div class="sdhdr-inner">
            
            <!-- Logo Area -->
            <div class="sdhdr-logo-area">
                <div class="sdhdr-branding">
                    <?php echo $this->get_logo(); ?>
                </div>
            </div>
            
            <!-- Navigation Area -->
            <div class="sdhdr-nav-area">
                
                <!-- Mobile Menu Toggle -->
                <button class="sdhdr-menu-toggle" aria-controls="sdhdr-primary-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sdhdr-toggle-line"></span>
                    <span class="sdhdr-toggle-line"></span>
                    <span class="sdhdr-toggle-line"></span>
                </button>
                
                <!-- Main Navigation -->
                <nav class="sdhdr-navigation" role="navigation" aria-label="Primary navigation">
                    <?php echo $this->get_navigation(); ?>
                </nav>
                
                <!-- Phone Button -->
                <?php echo $this->get_phone_button(); ?>
                
            </div>
            
        </div>
    </div>
</header>