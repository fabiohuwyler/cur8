<?php

if (!defined('ABSPATH')) {
    exit;
}

class Cur8_Frontend {
    
    public function __construct() {
        add_shortcode('cur8_updates', array($this, 'render_updates_shortcode'));
        add_action('widgets_init', array($this, 'register_widget'));
    }
    
    public function render_updates_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'type' => '',
            'layout' => 'grid'
        ), $atts);
        
        $db = new Cur8_Database();
        $updates = $db->get_updates(array(
            'limit' => intval($atts['limit']),
            'update_type' => sanitize_text_field($atts['type'])
        ));
        
        ob_start();
        include CUR8_PLUGIN_DIR . 'templates/updates-display.php';
        return ob_get_clean();
    }
    
    public function register_widget() {
        register_widget('CUR8_Updates_Widget');
    }
}

class CUR8_Updates_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'cur8_updates_widget',
            'CUR8 Updates',
            array('description' => 'Display CUR8 updates in a widget')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $limit = !empty($instance['limit']) ? $instance['limit'] : 5;
        $type = !empty($instance['type']) ? $instance['type'] : '';
        
        $db = new CUR8_Database();
        $updates = $db->get_updates(array(
            'limit' => intval($limit),
            'update_type' => sanitize_text_field($type)
        ));
        
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'list';
        
        include CUR8_PLUGIN_DIR . 'templates/updates-display.php';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 5;
        $type = !empty($instance['type']) ? $instance['type'] : '';
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'list';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">Number of updates:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" 
                   value="<?php echo esc_attr($limit); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('type')); ?>">Update Type:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('type')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('type')); ?>">
                <option value="" <?php selected($type, ''); ?>>All Types</option>
                <option value="status" <?php selected($type, 'status'); ?>>Status</option>
                <option value="image" <?php selected($type, 'image'); ?>>Image</option>
                <option value="quote" <?php selected($type, 'quote'); ?>>Quote</option>
                <option value="location" <?php selected($type, 'location'); ?>>Location</option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>">Layout:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
                <option value="list" <?php selected($layout, 'list'); ?>>List</option>
                <option value="grid" <?php selected($layout, 'grid'); ?>>Grid</option>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 5;
        $instance['type'] = (!empty($new_instance['type'])) ? sanitize_text_field($new_instance['type']) : '';
        $instance['layout'] = (!empty($new_instance['layout'])) ? sanitize_text_field($new_instance['layout']) : 'list';
        return $instance;
    }
}
