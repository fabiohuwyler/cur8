<?php

if (!defined('ABSPATH')) {
    exit;
}

class Cur8_Database {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'cur8_updates';
    }
    
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            update_type varchar(50) NOT NULL,
            content longtext,
            link_url varchar(500),
            link_text varchar(255),
            image_url varchar(500),
            location varchar(255),
            quote_text text,
            quote_author varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'published',
            PRIMARY KEY (id),
            KEY update_type (update_type),
            KEY created_at (created_at),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function insert_update($data) {
        global $wpdb;
        
        $defaults = array(
            'update_type' => 'status',
            'content' => '',
            'link_url' => '',
            'link_text' => '',
            'image_url' => '',
            'location' => '',
            'quote_text' => '',
            'quote_author' => '',
            'status' => 'published'
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'update_type' => sanitize_text_field($data['update_type']),
                'content' => wp_kses_post($data['content']),
                'link_url' => esc_url_raw($data['link_url']),
                'link_text' => sanitize_text_field($data['link_text']),
                'image_url' => esc_url_raw($data['image_url']),
                'location' => sanitize_text_field($data['location']),
                'quote_text' => sanitize_textarea_field($data['quote_text']),
                'quote_author' => sanitize_text_field($data['quote_author']),
                'status' => sanitize_text_field($data['status'])
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_insert_error', 'Failed to insert update', array('status' => 500));
        }
        
        return $wpdb->insert_id;
    }
    
    public function get_updates($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 10,
            'offset' => 0,
            'update_type' => '',
            'status' => 'published',
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array("status = %s");
        $where_values = array($args['status']);
        
        if (!empty($args['update_type'])) {
            $where[] = "update_type = %s";
            $where_values[] = $args['update_type'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            WHERE {$where_clause} 
            ORDER BY {$args['orderby']} {$args['order']} 
            LIMIT %d OFFSET %d",
            array_merge($where_values, array($args['limit'], $args['offset']))
        );
        
        return $wpdb->get_results($query);
    }
    
    public function get_update($id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id)
        );
    }
    
    public function update_update($id, $data) {
        global $wpdb;
        
        $update_data = array();
        $update_format = array();
        
        if (isset($data['update_type'])) {
            $update_data['update_type'] = sanitize_text_field($data['update_type']);
            $update_format[] = '%s';
        }
        if (isset($data['content'])) {
            $update_data['content'] = wp_kses_post($data['content']);
            $update_format[] = '%s';
        }
        if (isset($data['image_url'])) {
            $update_data['image_url'] = esc_url_raw($data['image_url']);
            $update_format[] = '%s';
        }
        if (isset($data['location'])) {
            $update_data['location'] = sanitize_text_field($data['location']);
            $update_format[] = '%s';
        }
        if (isset($data['quote_text'])) {
            $update_data['quote_text'] = sanitize_textarea_field($data['quote_text']);
            $update_format[] = '%s';
        }
        if (isset($data['quote_author'])) {
            $update_data['quote_author'] = sanitize_text_field($data['quote_author']);
            $update_format[] = '%s';
        }
        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
            $update_format[] = '%s';
        }
        
        return $wpdb->update(
            $this->table_name,
            $update_data,
            array('id' => $id),
            $update_format,
            array('%d')
        );
    }
    
    public function delete_update($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    public function get_table_name() {
        return $this->table_name;
    }
}
