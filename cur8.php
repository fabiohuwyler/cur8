<?php
/**
 * Plugin Name: Cur8
 * Plugin URI: https://github.com/yourusername/cur8
 * Description: Curate and share beautiful status updates, images, quotes, and locations with a modern web interface
 * Version: 1.0.0
 * Author: Fabio Huwyler
 * Author URI: https://fabiohuwyler.ch
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cur8
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CUR8_VERSION', '1.0.0');
define('CUR8_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUR8_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CUR8_DB_VERSION', '1.0.0');

class Cur8 {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'check_db_version'));
        add_action('init', array($this, 'init'), 1);
        add_action('init', array($this, 'start_session'), 1);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        require_once CUR8_PLUGIN_DIR . 'includes/class-cur8-database.php';
        require_once CUR8_PLUGIN_DIR . 'includes/class-cur8-api.php';
        require_once CUR8_PLUGIN_DIR . 'includes/class-cur8-frontend.php';
        require_once CUR8_PLUGIN_DIR . 'includes/class-cur8-admin.php';
        require_once CUR8_PLUGIN_DIR . 'includes/class-cur8-styles.php';
        
        new Cur8_Database();
        new Cur8_API();
        new Cur8_Frontend();
        new Cur8_Admin();
    }
    
    public function check_db_version() {
        $installed_version = get_option('cur8_db_version', '0');
        
        if (version_compare($installed_version, CUR8_DB_VERSION, '<')) {
            $this->update_database();
            update_option('cur8_db_version', CUR8_DB_VERSION);
        }
    }
    
    public function update_database() {
        $db = new Cur8_Database();
        $db->create_tables();
    }
    
    public function start_session() {
        if (!session_id()) {
            session_start();
        }
    }
    
    public function init() {
        add_rewrite_rule('^cur8/?$', 'index.php?cur8_page=1', 'top');
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'template_redirect'));
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'cur8_page';
        return $vars;
    }
    
    public function template_redirect() {
        if (get_query_var('cur8_page')) {
            // Check password protection
            if ($this->is_password_protected() && !$this->check_password()) {
                include CUR8_PLUGIN_DIR . 'templates/cur8-password.php';
                exit;
            }
            
            wp_enqueue_style('cur8-admin', CUR8_PLUGIN_URL . 'assets/css/admin.css', array(), CUR8_VERSION . '.' . time());
            wp_enqueue_script('cur8-admin', CUR8_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CUR8_VERSION . '.' . time(), true);
            include CUR8_PLUGIN_DIR . 'templates/cur8-form.php';
            exit;
        }
    }
    
    private function is_password_protected() {
        $password = get_option('cur8_access_password', '');
        return !empty($password);
    }
    
    private function check_password() {
        if (isset($_SESSION['cur8_authenticated']) && $_SESSION['cur8_authenticated'] === true) {
            return true;
        }
        
        if (isset($_POST['cur8_password'])) {
            $password = get_option('cur8_access_password', '');
            if (!empty($password) && $_POST['cur8_password'] === $password) {
                if (!session_id()) {
                    session_start();
                }
                $_SESSION['cur8_authenticated'] = true;
                return true;
            }
        }
        
        return false;
    }
    
    public function activate() {
        $this->init();
        flush_rewrite_rules();
        
        $db = new Cur8_Database();
        $db->create_tables();
        
        update_option('cur8_db_version', CUR8_DB_VERSION);
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style('cur8-frontend', CUR8_PLUGIN_URL . 'assets/css/frontend.css', array(), CUR8_VERSION);
        wp_enqueue_script('cur8-frontend', CUR8_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), CUR8_VERSION, true);
        
        $dynamic_css = Cur8_Styles::get_dynamic_css();
        wp_add_inline_style('cur8-frontend', $dynamic_css);
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'cur8') !== false || get_query_var('cur8_page')) {
            wp_enqueue_style('cur8-admin', CUR8_PLUGIN_URL . 'assets/css/admin.css', array(), CUR8_VERSION);
            wp_enqueue_script('cur8-admin', CUR8_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CUR8_VERSION, true);
            wp_localize_script('cur8-admin', 'cur8Data', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cur8_nonce'),
                'restUrl' => rest_url('cur8/v1/'),
                'restNonce' => wp_create_nonce('wp_rest')
            ));
        }
    }
}

function cur8() {
    return Cur8::get_instance();
}

cur8();
