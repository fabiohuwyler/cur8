<?php
if (!defined('ABSPATH')) {
    exit;
}

$layout_class = isset($layout) ? 'wptext-layout-' . esc_attr($layout) : 'wptext-layout-grid';
?>

<div class="wptext-updates-container <?php echo $layout_class; ?>">
    <?php if (!empty($updates)) : ?>
        <?php foreach ($updates as $update) : ?>
            <div class="wptext-update wptext-update-<?php echo esc_attr($update->update_type); ?>">
                <div class="wptext-update-inner">
                    <?php if ($update->update_type === 'status') : ?>
                        <div class="wptext-update-icon">📝</div>
                        <div class="wptext-update-content">
                            <?php echo wp_kses_post($update->content); ?>
                            <?php if (!empty($update->link_url)) : ?>
                                <br><br>
                                <a href="<?php echo esc_url($update->link_url); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo !empty($update->link_text) ? esc_html($update->link_text) : esc_html($update->link_url); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    
                    <?php elseif ($update->update_type === 'image') : ?>
                        <div class="wptext-update-icon">📷</div>
                        <?php if (!empty($update->image_url)) : ?>
                            <div class="wptext-update-image">
                                <img src="<?php echo esc_url($update->image_url); ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($update->content)) : ?>
                            <div class="wptext-update-content">
                                <?php echo wp_kses_post($update->content); ?>
                            </div>
                        <?php endif; ?>
                    
                    <?php elseif ($update->update_type === 'quote') : ?>
                        <div class="wptext-update-icon">💬</div>
                        <div class="wptext-update-quote">
                            <blockquote>
                                <?php echo esc_html($update->quote_text); ?>
                            </blockquote>
                            <?php if (!empty($update->quote_author)) : ?>
                                <cite>— <?php echo esc_html($update->quote_author); ?></cite>
                            <?php endif; ?>
                        </div>
                    
                    <?php elseif ($update->update_type === 'location') : ?>
                        <div class="wptext-update-icon">📍</div>
                        <div class="wptext-update-location">
                            <div class="wptext-location-name">
                                <strong><?php echo esc_html($update->location); ?></strong>
                            </div>
                            <?php if (!empty($update->content)) : ?>
                                <div class="wptext-update-content">
                                    <?php echo wp_kses_post($update->content); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="wptext-update-meta">
                        <time datetime="<?php echo esc_attr($update->created_at); ?>">
                            <?php echo esc_html(human_time_diff(strtotime($update->created_at), current_time('timestamp')) . ' ago'); ?>
                        </time>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="wptext-no-updates">No updates yet.</p>
    <?php endif; ?>
</div>
