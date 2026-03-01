<?php
/**
 * Staircase Header Loader
 * 
 * Manages header selection and initialization
 * Allows for multiple header types and A/B testing
 */

class StaircaseHeaderLoader {
    
    private static $instance = null;
    private $current_header = null;
    private $available_headers = [];
    
    /**
     * Singleton pattern
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->discover_headers();
        $this->init();
    }
    
    /**
     * Discover available headers
     */
    private function discover_headers() {
        $headers_dir = get_template_directory() . '/headers';
        
        if (!is_dir($headers_dir)) {
            return;
        }
        
        $folders = scandir($headers_dir);
        
        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..' || !is_dir($headers_dir . '/' . $folder)) {
                continue;
            }
            
            $class_file = $headers_dir . '/' . $folder . '/' . $folder . '.php';
            
            if (file_exists($class_file)) {
                $this->available_headers[$folder] = [
                    'name' => $folder,
                    'path' => $class_file,
                    'class' => $folder,
                    'dir' => $headers_dir . '/' . $folder
                ];
            }
        }
    }
    
    /**
     * Initialize the header system
     */
    private function init() {
        // Get selected header from options or use default
        $selected_header = get_option('staircase_header_type', 'StaircaseDefaultHeader');
        
        // Check if selected header exists
        if (!isset($this->available_headers[$selected_header])) {
            // Fallback to first available or none
            $selected_header = !empty($this->available_headers) ? 
                array_key_first($this->available_headers) : null;
        }
        
        if ($selected_header) {
            $this->load_header($selected_header);
        }
    }
    
    /**
     * Load a specific header
     */
    private function load_header($header_name) {
        if (!isset($this->available_headers[$header_name])) {
            return false;
        }
        
        $header_info = $this->available_headers[$header_name];
        
        // Include the class file
        require_once $header_info['path'];
        
        // Check if class exists
        if (!class_exists($header_info['class'])) {
            return false;
        }
        
        // Instantiate the header
        $this->current_header = new $header_info['class']();
        
        return true;
    }
    
    /**
     * Render the current header
     */
    public function render() {
        if ($this->current_header && method_exists($this->current_header, 'render')) {
            $this->current_header->render();
        } else {
            // Fallback to original header.php content if no header loaded
            $this->render_fallback();
        }
    }
    
    /**
     * Render fallback header (original implementation)
     */
    private function render_fallback() {
        ?>
        <header class="site-header">
            <div class="container">
                <div class="header-inner">
                    <div class="header-logo-area">
                        <div class="site-branding">
                            <?php 
                            $staircase_logo = get_option('staircase_header_logo', '');
                            if (!empty($staircase_logo)): 
                            ?>
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="staircase-logo-link">
                                    <img src="<?php echo esc_url($staircase_logo); ?>" alt="<?php bloginfo('name'); ?>" class="staircase-logo">
                                </a>
                            <?php elseif (has_custom_logo()): ?>
                                <?php the_custom_logo(); ?>
                            <?php else: ?>
                                <h1 class="site-title">
                                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                        <?php bloginfo('name'); ?>
                                    </a>
                                </h1>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="header-nav-area">
                        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                        
                        <nav class="main-navigation">
                            <?php
                            if (function_exists('silkweaver_render_menu') && get_option('silkweaver_use_system', true)) {
                                echo silkweaver_render_menu();
                            } else {
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'menu_id'        => 'primary-menu',
                                    'container'      => false,
                                    'depth'          => 3,
                                    'walker'         => class_exists('Staircase_Walker_Nav_Menu') ? new Staircase_Walker_Nav_Menu() : null,
                                ));
                            }
                            ?>
                        </nav>
                        
                        <?php 
                        if (function_exists('staircase_get_formatted_phone')):
                            $header_phone_raw = staircase_get_header_phone();
                            $header_phone_formatted = staircase_get_formatted_phone();
                            if (!empty($header_phone_raw)):
                        ?>
                            <div class="header-phone">
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $header_phone_raw)); ?>" class="header-phone-button staircase_main_cta_button">
                                    <svg class="phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                    <span class="phone-number"><?php echo esc_html($header_phone_formatted); ?></span>
                                </a>
                            </div>
                        <?php 
                            endif;
                        endif; 
                        ?>
                    </div>
                </div>
            </div>
        </header>
        <?php
    }
    
    /**
     * Get available headers
     */
    public function get_available_headers() {
        return $this->available_headers;
    }
    
    /**
     * Get current header name
     */
    public function get_current_header_name() {
        return $this->current_header ? get_class($this->current_header) : null;
    }
    
    /**
     * Switch header (for A/B testing or user preference)
     */
    public function switch_header($header_name) {
        if ($this->load_header($header_name)) {
            update_option('staircase_header_type', $header_name);
            return true;
        }
        return false;
    }
}

/**
 * Helper function to get header loader instance
 */
function staircase_header_loader() {
    return StaircaseHeaderLoader::get_instance();
}

/**
 * Helper function to render header
 */
function staircase_render_header() {
    staircase_header_loader()->render();
}

/**
 * Initialize the header system early
 * This ensures hooks are registered before they fire
 */
add_action('after_setup_theme', function() {
    staircase_header_loader();
}, 1);