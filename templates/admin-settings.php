<?php
if (!defined('ABSPATH')) {
    exit;
}

$primary_color = CUR8_Admin::get_primary_color();
$design_theme = CUR8_Admin::get_design_theme();
?>

<div class="wrap">
    <h1>CUR8 Settings</h1>
    
    <div class="cur8-settings-card">
        <h2>Design & Appearance</h2>
        
        <form method="post" action="">
            <?php wp_nonce_field('cur8_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cur8_primary_color">Primary Color</label>
                    </th>
                    <td>
                        <?php
                        // Get theme colors from FSE
                        $theme_colors = array();
                        if (function_exists('wp_get_global_settings')) {
                            $global_settings = wp_get_global_settings();
                            if (isset($global_settings['color']['palette']['theme'])) {
                                $theme_colors = $global_settings['color']['palette']['theme'];
                            }
                        }
                        ?>
                        
                        <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                            <input type="color" name="cur8_primary_color" id="cur8_primary_color" 
                                   value="<?php echo esc_attr($primary_color); ?>" class="cur8-color-picker"
                                   style="width: 60px; height: 40px; border: 2px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <input type="text" name="cur8_primary_color_hex" id="cur8_primary_color_hex" 
                                   value="<?php echo esc_attr($primary_color); ?>" 
                                   placeholder="#667eea" pattern="^#[0-9A-Fa-f]{6}$"
                                   class="regular-text" style="max-width: 120px;">
                        </div>
                        
                        <?php if (!empty($theme_colors)): ?>
                            <div style="margin-top: 16px;">
                                <p style="margin-bottom: 8px; font-weight: 600;">Theme Colors:</p>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php foreach ($theme_colors as $color): ?>
                                        <button type="button" class="cur8-theme-color" 
                                                data-color="<?php echo esc_attr($color['color']); ?>"
                                                style="width: 40px; height: 40px; border: 2px solid #ddd; border-radius: 4px; 
                                                       background: <?php echo esc_attr($color['color']); ?>; cursor: pointer;
                                                       transition: transform 0.2s, border-color 0.2s;"
                                                title="<?php echo esc_attr($color['name']); ?>">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <p class="description" style="margin-top: 12px;">
                            Choose a color using the picker, enter a HEX code, or select from your theme colors.
                        </p>
                        
                        <script>
                        jQuery(document).ready(function($) {
                            // Sync color picker and hex input
                            $('#cur8_primary_color').on('input', function() {
                                $('#cur8_primary_color_hex').val($(this).val());
                            });
                            
                            $('#cur8_primary_color_hex').on('input', function() {
                                var hex = $(this).val();
                                if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                                    $('#cur8_primary_color').val(hex);
                                }
                            });
                            
                            // Theme color buttons
                            $('.cur8-theme-color').on('click', function(e) {
                                e.preventDefault();
                                var color = $(this).data('color');
                                $('#cur8_primary_color').val(color);
                                $('#cur8_primary_color_hex').val(color);
                                
                                // Visual feedback
                                $('.cur8-theme-color').css('border-color', '#ddd');
                                $(this).css('border-color', '#667eea');
                            });
                            
                            // Hover effects
                            $('.cur8-theme-color').hover(
                                function() { $(this).css('transform', 'scale(1.1)'); },
                                function() { $(this).css('transform', 'scale(1)'); }
                            );
                        });
                        </script>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cur8_design_theme">Design Theme</label>
                    </th>
                    <td>
                        <select name="cur8_design_theme" id="cur8_design_theme" class="regular-text">
                            <option value="minimalist" <?php selected($design_theme, 'minimalist'); ?>>Minimalist (Border Only)</option>
                            <option value="modern" <?php selected($design_theme, 'modern'); ?>>Modern (Default)</option>
                            <option value="bold" <?php selected($design_theme, 'bold'); ?>>Bold (Filled Background)</option>
                            <option value="shadow" <?php selected($design_theme, 'shadow'); ?>>Shadow (Elevated Cards)</option>
                            <option value="gradient" <?php selected($design_theme, 'gradient'); ?>>Gradient (Colorful)</option>
                        </select>
                        <p class="description">Select the visual style for displaying updates.</p>
                        
                        <div class="wptext-theme-previews" style="margin-top: 20px;">
                            <h4>Theme Previews:</h4>
                            
                            <div class="wptext-preview-item">
                                <strong>Minimalist:</strong> Clean design with only a colored border, white background, maximum simplicity.
                            </div>
                            
                            <div class="wptext-preview-item">
                                <strong>Modern:</strong> Subtle styling with rounded corners, clean and professional.
                            </div>
                            
                            <div class="wptext-preview-item">
                                <strong>Bold:</strong> Filled background with your primary color, high contrast text.
                            </div>
                            
                            <div class="wptext-preview-item">
                                <strong>Shadow:</strong> Elevated cards with drop shadows for depth.
                            </div>
                            
                            <div class="wptext-preview-item">
                                <strong>Gradient:</strong> Gradient backgrounds using your primary color.
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cur8_access_password">Access Password</label>
                    </th>
                    <td>
                        <input type="text" name="cur8_access_password" id="cur8_access_password" 
                               value="<?php echo esc_attr(get_option('cur8_access_password', '')); ?>" class="regular-text">
                        <p class="description">Set a password to protect access to <strong><?php echo home_url('/cur8'); ?></strong>. Leave empty to disable password protection.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="cur8_settings_submit" class="button button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    
    <div class="wptext-settings-card" style="margin-top: 30px;">
        <h2>Usage Instructions</h2>
        
        <h3>Web Form Access</h3>
        <p>Users can submit updates by visiting:</p>
        <p><strong><?php echo home_url('/cur8'); ?></strong></p>
        
        <h3>Display Updates on Your Site</h3>
        
        <h4>Using Shortcode</h4>
        <p>Add this shortcode to any page or post:</p>
        <pre><code>[cur8_updates limit="10" type="" layout="grid"]</code></pre>
        
        <p><strong>Shortcode Parameters:</strong></p>
        <ul>
            <li><code>limit</code> - Number of updates to display (default: 10)</li>
            <li><code>type</code> - Filter by type: status, image, quote, location (default: all)</li>
            <li><code>layout</code> - Display layout: grid or list (default: grid)</li>
        </ul>
        
        <h4>Examples:</h4>
        <pre><code>[wptext_updates limit="5" type="image" layout="grid"]</code></pre>
        <pre><code>[wptext_updates limit="20" type="status" layout="list"]</code></pre>
        
        <h4>Using Widget</h4>
        <p>Go to <strong>Appearance → Widgets</strong> and add the "Cur8 Updates" widget to any widget area.</p>
        
        <h4>Using PHP in Theme</h4>
        <p>Add this code to your theme files:</p>
        <pre><code>&lt;?php echo do_shortcode('[wptext_updates limit="10"]'); ?&gt;</code></pre>
        
        <h3>Update Types</h3>
        <ul>
            <li><strong>Status</strong> - Text-based status updates</li>
            <li><strong>Image</strong> - Image uploads with optional captions</li>
            <li><strong>Quote</strong> - Quotes with author attribution</li>
            <li><strong>Location</strong> - Location check-ins with descriptions</li>
        </ul>
    </div>
</div>
