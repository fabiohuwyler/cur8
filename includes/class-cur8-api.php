<?php

if (!defined('ABSPATH')) {
    exit;
}

class CUR8_API {
    
    private $namespace = 'cur8/v1';
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('wp_ajax_cur8_upload_image', array($this, 'handle_image_upload'));
        add_action('wp_ajax_nopriv_cur8_upload_image', array($this, 'handle_image_upload'));
        add_action('wp_ajax_wptext_upload_image', array($this, 'handle_image_upload'));
        add_action('wp_ajax_nopriv_wptext_upload_image', array($this, 'handle_image_upload'));
    }
    
    public function register_routes() {
        register_rest_route($this->namespace, '/updates', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_updates'),
                'permission_callback' => '__return_true'
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_update'),
                'permission_callback' => array($this, 'create_update_permissions_check'),
                'args' => $this->get_update_schema()
            )
        ));
        
        register_rest_route($this->namespace, '/updates/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_update'),
                'permission_callback' => '__return_true'
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_update'),
                'permission_callback' => array($this, 'update_permissions_check')
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_update'),
                'permission_callback' => array($this, 'delete_permissions_check')
            )
        ));
    }
    
    public function get_updates($request) {
        $db = new Cur8_Database();
        
        $args = array(
            'limit' => $request->get_param('per_page') ?: 10,
            'offset' => $request->get_param('offset') ?: 0,
            'update_type' => $request->get_param('type') ?: '',
            'status' => 'published'
        );
        
        $updates = $db->get_updates($args);
        
        return rest_ensure_response($updates);
    }
    
    public function get_update($request) {
        $db = new Cur8_Database();
        $update = $db->get_update($request['id']);
        
        if (!$update) {
            return new WP_Error('not_found', 'Update not found', array('status' => 404));
        }
        
        return rest_ensure_response($update);
    }
    
    public function create_update($request) {
        $db = new Cur8_Database();
        
        $data = array(
            'update_type' => $request->get_param('update_type'),
            'content' => $request->get_param('content'),
            'link_url' => $request->get_param('link_url'),
            'link_text' => $request->get_param('link_text'),
            'image_url' => $request->get_param('image_url'),
            'location' => $request->get_param('location'),
            'quote_text' => $request->get_param('quote_text'),
            'quote_author' => $request->get_param('quote_author'),
            'status' => 'published'
        );
        
        $result = $db->insert_update($data);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        $update = $db->get_update($result);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => $update,
            'message' => 'Update created successfully'
        ));
    }
    
    public function update_update($request) {
        $db = new Cur8_Database();
        
        $update = $db->get_update($request['id']);
        if (!$update) {
            return new WP_Error('not_found', 'Update not found', array('status' => 404));
        }
        
        $data = array();
        $params = array('update_type', 'content', 'image_url', 'location', 'quote_text', 'quote_author', 'status');
        
        foreach ($params as $param) {
            if ($request->get_param($param) !== null) {
                $data[$param] = $request->get_param($param);
            }
        }
        
        $result = $db->update_update($request['id'], $data);
        
        if ($result === false) {
            return new WP_Error('update_failed', 'Failed to update', array('status' => 500));
        }
        
        $updated = $db->get_update($request['id']);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => $updated,
            'message' => 'Update updated successfully'
        ));
    }
    
    public function delete_update($request) {
        $db = new Cur8_Database();
        
        $update = $db->get_update($request['id']);
        if (!$update) {
            return new WP_Error('not_found', 'Update not found', array('status' => 404));
        }
        
        $result = $db->delete_update($request['id']);
        
        if ($result === false) {
            return new WP_Error('delete_failed', 'Failed to delete', array('status' => 500));
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Update deleted successfully'
        ));
    }
    
    public function handle_image_upload() {
        check_ajax_referer('cur8_nonce', 'nonce');
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $uploadedfile = $_FILES['file'];
        $upload_overrides = array('test_form' => false);
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(array(
                'url' => $movefile['url'],
                'file' => $movefile['file']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $movefile['error']
            ));
        }
    }
    
    public function create_update_permissions_check($request) {
        return true;
    }
    
    public function update_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    public function delete_permissions_check($request) {
        return current_user_can('delete_posts');
    }
    
    private function get_update_schema() {
        return array(
            'update_type' => array(
                'required' => true,
                'type' => 'string',
                'enum' => array('status', 'image', 'quote', 'location')
            ),
            'content' => array(
                'type' => 'string',
                'default' => ''
            ),
            'image_url' => array(
                'type' => 'string',
                'format' => 'uri',
                'default' => ''
            ),
            'location' => array(
                'type' => 'string',
                'default' => ''
            ),
            'quote_text' => array(
                'type' => 'string',
                'default' => ''
            ),
            'quote_author' => array(
                'type' => 'string',
                'default' => ''
            )
        );
    }
}
