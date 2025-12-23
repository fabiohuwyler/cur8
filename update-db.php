<?php
/**
 * Database Update Script
 * Run this once to add link_url and link_text columns to existing installations
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Unauthorized');
}

global $wpdb;
$table_name = $wpdb->prefix . 'wptext_updates';

// Check if columns exist
$columns = $wpdb->get_results("SHOW COLUMNS FROM {$table_name}");
$column_names = array_column($columns, 'Field');

$updates_made = array();

// Add link_url column if it doesn't exist
if (!in_array('link_url', $column_names)) {
    $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN link_url varchar(500) AFTER content");
    $updates_made[] = 'Added link_url column';
}

// Add link_text column if it doesn't exist
if (!in_array('link_text', $column_names)) {
    $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN link_text varchar(255) AFTER link_url");
    $updates_made[] = 'Added link_text column';
}

if (empty($updates_made)) {
    echo '<h2>Database is already up to date!</h2>';
    echo '<p>All required columns exist.</p>';
} else {
    echo '<h2>Database Updated Successfully!</h2>';
    echo '<ul>';
    foreach ($updates_made as $update) {
        echo '<li>' . esc_html($update) . '</li>';
    }
    echo '</ul>';
}

echo '<p><a href="' . admin_url('admin.php?page=wptext-updates') . '">Go to WPText Admin</a></p>';
