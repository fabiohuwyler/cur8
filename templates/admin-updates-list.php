<?php
if (!defined('ABSPATH')) {
    exit;
}

$filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';
$total_updates = count($updates);
?>

<div class="wrap wptext-admin-wrap">
    <h1 class="wp-heading-inline">Cur8 Updates</h1>
    <a href="<?php echo home_url('/cur8'); ?>" class="page-title-action" target="_blank">➕ Add New Update</a>
    <hr class="wp-header-end">
    
    <div class="wptext-admin-stats">
        <div class="wptext-stat-card">
            <div class="stat-number"><?php echo $total_updates; ?></div>
            <div class="stat-label">Total Updates</div>
        </div>
        <div class="wptext-stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-label">Status</div>
            <div class="stat-count"><?php echo count(array_filter($updates, function($u) { return $u->update_type === 'status'; })); ?></div>
        </div>
        <div class="wptext-stat-card">
            <div class="stat-icon">📷</div>
            <div class="stat-label">Images</div>
            <div class="stat-count"><?php echo count(array_filter($updates, function($u) { return $u->update_type === 'image'; })); ?></div>
        </div>
        <div class="wptext-stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-label">Quotes</div>
            <div class="stat-count"><?php echo count(array_filter($updates, function($u) { return $u->update_type === 'quote'; })); ?></div>
        </div>
        <div class="wptext-stat-card">
            <div class="stat-icon">📍</div>
            <div class="stat-label">Locations</div>
            <div class="stat-count"><?php echo count(array_filter($updates, function($u) { return $u->update_type === 'location'; })); ?></div>
        </div>
    </div>

    <div class="wptext-admin-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="wptext-updates">
            <select name="filter_type" id="filter_type">
                <option value="">All Types</option>
                <option value="status" <?php selected($filter_type, 'status'); ?>>Status</option>
                <option value="image" <?php selected($filter_type, 'image'); ?>>Image</option>
                <option value="quote" <?php selected($filter_type, 'quote'); ?>>Quote</option>
                <option value="location" <?php selected($filter_type, 'location'); ?>>Location</option>
            </select>
            <button type="submit" class="button">Filter</button>
            <?php if ($filter_type): ?>
                <a href="?page=wptext-updates" class="button">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped wptext-updates-table">
        <thead>
            <tr>
                <th class="column-id">ID</th>
                <th class="column-type">Type</th>
                <th class="column-preview">Preview</th>
                <th class="column-content">Content</th>
                <th class="column-date">Date</th>
                <th class="column-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($updates)) : ?>
                <?php 
                $filtered_updates = $filter_type ? array_filter($updates, function($u) use ($filter_type) {
                    return $u->update_type === $filter_type;
                }) : $updates;
                ?>
                <?php if (!empty($filtered_updates)) : ?>
                    <?php foreach ($filtered_updates as $update) : ?>
                        <tr class="wptext-update-row">
                            <td class="column-id"><strong>#<?php echo esc_html($update->id); ?></strong></td>
                            <td class="column-type">
                                <span class="wptext-type-badge wptext-type-<?php echo esc_attr($update->update_type); ?>">
                                    <?php 
                                    $icons = ['status' => '📝', 'image' => '📷', 'quote' => '💬', 'location' => '📍'];
                                    echo $icons[$update->update_type] . ' ' . esc_html(ucfirst($update->update_type)); 
                                    ?>
                                </span>
                            </td>
                            <td class="column-preview">
                                <?php if ($update->update_type === 'image' && !empty($update->image_url)) : ?>
                                    <img src="<?php echo esc_url($update->image_url); ?>" alt="" class="wptext-admin-thumbnail">
                                <?php endif; ?>
                            </td>
                            <td class="column-content">
                                <div class="wptext-content-preview">
                                    <?php 
                                    if ($update->update_type === 'status') {
                                        echo '<p>' . wp_trim_words($update->content, 20) . '</p>';
                                    } elseif ($update->update_type === 'image') {
                                        echo !empty($update->content) ? '<p>' . wp_trim_words($update->content, 15) . '</p>' : '<em>No caption</em>';
                                    } elseif ($update->update_type === 'quote') {
                                        echo '<blockquote>"' . wp_trim_words($update->quote_text, 15) . '"</blockquote>';
                                        if (!empty($update->quote_author)) {
                                            echo '<cite>— ' . esc_html($update->quote_author) . '</cite>';
                                        }
                                    } elseif ($update->update_type === 'location') {
                                        echo '<strong>📍 ' . esc_html($update->location) . '</strong>';
                                        if (!empty($update->content)) {
                                            echo '<p>' . wp_trim_words($update->content, 15) . '</p>';
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="column-date">
                                <div class="wptext-date-info">
                                    <strong><?php echo esc_html(human_time_diff(strtotime($update->created_at), current_time('timestamp'))); ?> ago</strong>
                                    <br>
                                    <small><?php echo date('M j, Y g:i a', strtotime($update->created_at)); ?></small>
                                </div>
                            </td>
                            <td class="column-actions">
                                <button class="button button-small wptext-view-update" data-id="<?php echo esc_attr($update->id); ?>" title="View">👁️</button>
                                <button class="button button-small button-link-delete wptext-delete-update" data-id="<?php echo esc_attr($update->id); ?>" title="Delete">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="wptext-no-results">
                            <div class="wptext-empty-state">
                                <span class="dashicons dashicons-filter" style="font-size: 48px; opacity: 0.3;"></span>
                                <p>No updates found with the selected filter.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" class="wptext-no-results">
                        <div class="wptext-empty-state">
                            <span class="dashicons dashicons-megaphone" style="font-size: 48px; opacity: 0.3;"></span>
                            <p>No updates yet. <a href="<?php echo home_url('/cur8'); ?>">Create your first update</a>!</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="wptext-view-modal" class="wptext-modal" style="display: none;">
    <div class="wptext-modal-backdrop"></div>
    <div class="wptext-modal-dialog">
        <div class="wptext-modal-header">
            <h2>View Update</h2>
            <button class="wptext-modal-close">&times;</button>
        </div>
        <div class="wptext-modal-body" id="wptext-modal-content">
            <p>Loading...</p>
        </div>
    </div>
</div>

<style>
.wptext-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 100000;
}

.wptext-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
}

.wptext-modal-dialog {
    position: relative;
    max-width: 600px;
    margin: 50px auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 100001;
}

.wptext-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e0e0e0;
}

.wptext-modal-header h2 {
    margin: 0;
    font-size: 20px;
}

.wptext-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #999;
    line-height: 1;
    padding: 0;
    width: 30px;
    height: 30px;
}

.wptext-modal-close:hover {
    color: #333;
}

.wptext-modal-body {
    padding: 24px;
    max-height: 70vh;
    overflow-y: auto;
}

.wptext-modal-update {
    line-height: 1.6;
}

.wptext-modal-update .update-type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 16px;
}

.wptext-modal-update img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 16px 0;
}

.wptext-modal-update blockquote {
    margin: 16px 0;
    padding: 16px;
    background: #f7f7f7;
    border-left: 4px solid #667eea;
    font-size: 18px;
    font-style: italic;
}

.wptext-modal-update .update-meta {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
    font-size: 13px;
    color: #999;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.wptext-view-update').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        $('#wptext-view-modal').show();
        $('#wptext-modal-content').html('<p>Loading...</p>');
        
        $.ajax({
            url: wptextData.restUrl + 'updates/' + id,
            type: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wptextData.restNonce);
            },
            success: function(update) {
                let html = '<div class="wptext-modal-update">';
                html += '<span class="update-type-badge wptext-type-' + update.update_type + '">';
                
                const icons = {status: '📝', image: '📷', quote: '💬', location: '📍'};
                html += icons[update.update_type] + ' ' + update.update_type.charAt(0).toUpperCase() + update.update_type.slice(1);
                html += '</span>';
                
                if (update.update_type === 'status') {
                    html += '<p style="font-size: 16px;">' + update.content + '</p>';
                } else if (update.update_type === 'image') {
                    if (update.image_url) {
                        html += '<img src="' + update.image_url + '" alt="">';
                    }
                    if (update.content) {
                        html += '<p>' + update.content + '</p>';
                    }
                } else if (update.update_type === 'quote') {
                    html += '<blockquote>' + update.quote_text + '</blockquote>';
                    if (update.quote_author) {
                        html += '<cite>— ' + update.quote_author + '</cite>';
                    }
                } else if (update.update_type === 'location') {
                    html += '<p style="font-size: 18px;"><strong>📍 ' + update.location + '</strong></p>';
                    if (update.content) {
                        html += '<p>' + update.content + '</p>';
                    }
                }
                
                html += '<div class="update-meta">';
                html += '<strong>Created:</strong> ' + new Date(update.created_at).toLocaleString();
                html += '<br><strong>ID:</strong> #' + update.id;
                html += '</div>';
                html += '</div>';
                
                $('#wptext-modal-content').html(html);
            },
            error: function() {
                $('#wptext-modal-content').html('<p>Error loading update.</p>');
            }
        });
    });
    
    $('.wptext-modal-close, .wptext-modal-backdrop').on('click', function() {
        $('#wptext-view-modal').hide();
    });
    
    $('.wptext-delete-update').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this update?')) {
            return;
        }
        
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        $.ajax({
            url: wptextData.restUrl + 'updates/' + id,
            type: 'DELETE',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wptextData.restNonce);
            },
            success: function() {
                row.fadeOut(function() {
                    row.remove();
                    
                    if ($('.wptext-update-row').length === 0) {
                        location.reload();
                    }
                });
            },
            error: function() {
                alert('Error deleting update.');
            }
        });
    });
});
</script>
