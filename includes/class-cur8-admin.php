<?php

if (!defined('ABSPATH')) {
    exit;
}

class Cur8_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Cur8',
            'Cur8',
            'manage_options',
            'cur8-updates',
            array($this, 'admin_page'),
            'dashicons-megaphone',
            30
        );
        
        add_submenu_page(
            'cur8-updates',
            'All Updates',
            'All Updates',
            'manage_options',
            'cur8-updates',
            array($this, 'admin_page')
        );
        
        add_submenu_page(
            'cur8-updates',
            'Settings',
            'Settings',
            'manage_options',
            'cur8-settings',
            array($this, 'settings_page')
        );
    }
    
    public function admin_page() {
        $db = new Cur8_Database();
        $updates = $db->get_updates(array('limit' => 50, 'status' => 'published'));
        
        include CUR8_PLUGIN_DIR . 'templates/admin-updates-list.php';
    }
    
    public function settings_page() {
        if (isset($_POST['cur8_settings_submit'])) {
            check_admin_referer('cur8_settings_nonce');
            
            // Use hex input if provided, otherwise use color picker
            $color = !empty($_POST['cur8_primary_color_hex']) ? $_POST['cur8_primary_color_hex'] : $_POST['cur8_primary_color'];
            update_option('cur8_primary_color', sanitize_hex_color($color));
            update_option('cur8_design_theme', sanitize_text_field($_POST['cur8_design_theme']));
            update_option('cur8_access_password', sanitize_text_field($_POST['cur8_access_password']));
            
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
        
        include CUR8_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    public function register_settings() {
        register_setting('cur8_settings', 'cur8_primary_color');
        register_setting('cur8_settings', 'cur8_design_theme');
        register_setting('cur8_settings', 'cur8_access_password');
    }
    
    public static function get_primary_color() {
        return get_option('cur8_primary_color', '#667eea');
    }
    
    public static function get_design_theme() {
        return get_option('cur8_design_theme', 'modern');
    }
}
