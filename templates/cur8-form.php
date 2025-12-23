<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cur8 - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body class="wptext-form-page">
    <div class="wptext-container">
        <div class="wptext-header">
            <h1>✨ Create Update</h1>
            <p>Share your thoughts, images, quotes, or locations</p>
            <a href="<?php echo home_url(); ?>" class="wptext-home-link">← Back to Site</a>
        </div>

        <div class="wptext-form-wrapper">
            <div class="wptext-type-selector">
                <button type="button" class="wptext-type-btn active" data-type="status">
                    <span class="icon">📝</span>
                    <span>Status</span>
                </button>
                <button type="button" class="wptext-type-btn" data-type="image">
                    <span class="icon">📷</span>
                    <span>Image</span>
                </button>
                <button type="button" class="wptext-type-btn" data-type="quote">
                    <span class="icon">💬</span>
                    <span>Quote</span>
                </button>
                <button type="button" class="wptext-type-btn" data-type="location">
                    <span class="icon">📍</span>
                    <span>Location</span>
                </button>
            </div>

            <form id="wptext-update-form" class="wptext-form">
                <input type="hidden" name="update_type" id="update_type" value="status">

                <div class="wptext-form-section wptext-section-status active">
                    <div class="wptext-form-group">
                        <label for="status_content">What's on your mind?</label>
                        <textarea name="content" id="status_content" rows="4" placeholder="Share your thoughts..."></textarea>
                    </div>
                    <div class="wptext-form-group">
                        <label for="link_url">Add a Link (optional)</label>
                        <input type="text" name="link_url" id="link_url" placeholder="https://example.com">
                    </div>
                    <div class="wptext-form-group">
                        <label for="link_text">Link Text (optional)</label>
                        <input type="text" name="link_text" id="link_text" placeholder="Click here">
                    </div>
                </div>

                <div class="wptext-form-section wptext-section-image">
                    <div class="wptext-form-group">
                        <label for="image_file">Upload Image</label>
                        <label for="image_file" class="wptext-image-upload" id="image_upload_area" style="cursor: pointer;">
                            <span class="wptext-image-upload-label">📤</span>
                            <span class="wptext-image-upload-text">Click to upload image</span>
                        </label>
                        <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;">
                        <div class="wptext-image-preview" id="image_preview" style="display: none;"></div>
                        <input type="hidden" name="image_url" id="image_url">
                    </div>
                    <div class="wptext-form-group">
                        <label for="image_content">Caption (optional)</label>
                        <textarea name="image_content" id="image_content" rows="2" placeholder="Add a caption..."></textarea>
                    </div>
                </div>

                <div class="wptext-form-section wptext-section-quote">
                    <div class="wptext-form-group">
                        <label for="quote_text">Quote</label>
                        <textarea name="quote_text" id="quote_text" rows="3" placeholder="Enter the quote..."></textarea>
                    </div>
                    <div class="wptext-form-group">
                        <label for="quote_author">Author</label>
                        <input type="text" name="quote_author" id="quote_author" placeholder="Quote author">
                    </div>
                </div>

                <div class="wptext-form-section wptext-section-location">
                    <div class="wptext-form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" placeholder="Where are you?">
                    </div>
                    <div class="wptext-form-group">
                        <label for="location_content">Description (optional)</label>
                        <textarea name="location_content" id="location_content" rows="2" placeholder="Tell us about this place..."></textarea>
                    </div>
                </div>

                <div class="wptext-form-actions">
                    <button type="submit" class="wptext-submit-btn">
                        <span class="btn-text">Post Update</span>
                        <span class="btn-loading" style="display: none;">Posting...</span>
                    </button>
                </div>

                <div class="wptext-message" id="wptext_message" style="display: none;"></div>
            </form>
        </div>

        <div class="wptext-recent-updates">
            <h2>Recent Updates</h2>
            <div id="recent_updates_list">
                <p class="loading">Loading...</p>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>

    <script>
    const wptextData = {
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('cur8_nonce'); ?>',
        restUrl: '<?php echo rest_url('cur8/v1/'); ?>',
        restNonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
    };
    
    jQuery(document).ready(function($) {
        const typeButtons = $('.wptext-type-btn');
        const formSections = $('.wptext-form-section');
        const updateTypeInput = $('#update_type');
        const form = $('#wptext-update-form');
        const messageDiv = $('#wptext_message');

        typeButtons.on('click', function() {
            const type = $(this).data('type');
            
            typeButtons.removeClass('active');
            $(this).addClass('active');
            
            formSections.removeClass('active');
            $(`.wptext-section-${type}`).addClass('active');
            
            updateTypeInput.val(type);
        });

        $('#image_file').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').html(`<img src="${e.target.result}" alt="Preview">`).show();
                $('#image_upload_area').hide();
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('action', 'wptext_upload_image');
            formData.append('nonce', wptextData.nonce);
            formData.append('file', file);

            $.ajax({
                url: wptextData.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#image_url').val(response.data.url);
                    }
                }
            });
        });

        form.on('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.find('.wptext-submit-btn');
            const updateType = updateTypeInput.val();
            
            // Validation
            let isValid = true;
            let errorMsg = '';
            
            switch(updateType) {
                case 'status':
                    if (!$('#status_content').val().trim()) {
                        isValid = false;
                        errorMsg = 'Please enter a status message';
                    }
                    break;
                case 'image':
                    if (!$('#image_url').val()) {
                        isValid = false;
                        errorMsg = 'Please upload an image';
                    }
                    break;
                case 'quote':
                    if (!$('#quote_text').val().trim()) {
                        isValid = false;
                        errorMsg = 'Please enter a quote';
                    }
                    break;
                case 'location':
                    if (!$('#location').val().trim()) {
                        isValid = false;
                        errorMsg = 'Please enter a location';
                    }
                    break;
            }
            
            if (!isValid) {
                messageDiv.removeClass('success').addClass('error').text(errorMsg).show();
                setTimeout(function() { messageDiv.fadeOut(); }, 3000);
                return;
            }

            submitBtn.find('.btn-text').hide();
            submitBtn.find('.btn-loading').show();
            submitBtn.prop('disabled', true);

            let data = {
                update_type: updateType
            };

            switch(updateType) {
                case 'status':
                    data.content = $('#status_content').val();
                    data.link_url = $('#link_url').val();
                    data.link_text = $('#link_text').val();
                    break;
                case 'image':
                    data.image_url = $('#image_url').val();
                    data.content = $('#image_content').val();
                    break;
                case 'quote':
                    data.quote_text = $('#quote_text').val();
                    data.quote_author = $('#quote_author').val();
                    break;
                case 'location':
                    data.location = $('#location').val();
                    data.content = $('#location_content').val();
                    break;
            }

            $.ajax({
                url: wptextData.restUrl + 'updates',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wptextData.restNonce);
                },
                success: function(response) {
                    messageDiv.removeClass('error').addClass('success')
                        .text('Update posted successfully!').show();
                    form[0].reset();
                    $('#image_preview').empty().hide();
                    $('#image_upload_area').show();
                    $('#image_url').val('');
                    loadRecentUpdates();
                    
                    setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 3000);
                },
                error: function(xhr) {
                    messageDiv.removeClass('success').addClass('error')
                        .text('Error posting update. Please try again.').show();
                },
                complete: function() {
                    submitBtn.find('.btn-text').show();
                    submitBtn.find('.btn-loading').hide();
                    submitBtn.prop('disabled', false);
                }
            });
        });

        function loadRecentUpdates() {
            $.ajax({
                url: wptextData.restUrl + 'updates?per_page=5',
                type: 'GET',
                success: function(updates) {
                    if (updates.length === 0) {
                        $('#recent_updates_list').html('<p>No updates yet.</p>');
                        return;
                    }

                    let html = '<div class="wptext-updates-list">';
                    updates.forEach(function(update) {
                        html += renderUpdate(update);
                    });
                    html += '</div>';
                    
                    $('#recent_updates_list').html(html);
                }
            });
        }

        function renderUpdate(update) {
            const date = new Date(update.created_at);
            const timeAgo = getTimeAgo(date);
            const icons = {status: '📝', image: '📷', quote: '💬', location: '📍'};
            let content = '';

            switch(update.update_type) {
                case 'status':
                    content = `<div class="update-content">${update.content}</div>`;
                    break;
                case 'image':
                    content = `
                        <div class="update-image">
                            <img src="${update.image_url}" alt="" style="max-width: 100%; border-radius: 12px; margin: 8px 0;">
                        </div>
                        ${update.content ? `<div class="update-content" style="margin-top: 12px;">${update.content}</div>` : ''}
                    `;
                    break;
                case 'quote':
                    content = `
                        <div class="update-quote">
                            <blockquote style="margin: 8px 0; padding: 16px; background: #f7f7f7; border-left: 4px solid #667eea; border-radius: 8px; font-style: italic; color: #555;">${update.quote_text}</blockquote>
                            ${update.quote_author ? `<cite style="display: block; margin-top: 8px; text-align: right; color: #999; font-size: 14px;">— ${update.quote_author}</cite>` : ''}
                        </div>
                    `;
                    break;
                case 'location':
                    content = `
                        <div class="update-location" style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
                            <span class="location-icon" style="font-size: 20px;">📍</span>
                            <strong style="font-size: 16px;">${update.location}</strong>
                        </div>
                        ${update.content ? `<div class="update-content" style="margin-top: 12px;">${update.content}</div>` : ''}
                    `;
                    break;
            }

            return `
                <div class="wptext-update-item wptext-update-${update.update_type}">
                    <div class="update-meta" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span class="update-type" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #667eea; color: white; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            <span>${icons[update.update_type]}</span>
                            <span>${update.update_type}</span>
                        </span>
                        <span class="update-date" style="color: #999; font-size: 13px;">${timeAgo}</span>
                    </div>
                    ${content}
                </div>
            `;
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60
            };

            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                if (interval >= 1) {
                    return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
                }
            }
            return 'just now';
        }

        loadRecentUpdates();
    });
    </script>
</body>
</html>
