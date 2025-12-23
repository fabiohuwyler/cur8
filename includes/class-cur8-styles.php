<?php

if (!defined('ABSPATH')) {
    exit;
}

class CUR8_Styles {
    
    public static function get_dynamic_css() {
        $primary_color = CUR8_Admin::get_primary_color();
        $theme = CUR8_Admin::get_design_theme();
        
        $css = '';
        
        switch ($theme) {
            case 'minimalist':
                $css .= "
                .cur8-update {
                .wptext-update {
                    background: white;
                    border: 3px solid {$primary_color};
                    border-radius: 0;
                    box-shadow: none;
                }
                
                .wptext-update:hover {
                    opacity: 1;
                    border-color: {$primary_color};
                    filter: brightness(0.98);
                }
                
                .wptext-update-meta {
                    color: {$primary_color};
                    border-top-color: {$primary_color};
                }
                
                .wptext-update-quote blockquote {
                    background: transparent;
                    border-left-color: {$primary_color};
                    color: #333;
                }
                
                .wptext-update-location .wptext-location-name {
                    color: {$primary_color};
                }
                ";
                break;
                
            case 'bold':
                $css .= "
                .wptext-update {
                    background: {$primary_color};
                    border-radius: 12px;
                    color: white;
                }
                
                .wptext-update-content {
                    color: white;
                }
                
                .wptext-update:hover {
                    opacity: 0.9;
                }
                
                .wptext-update-meta {
                    color: rgba(255, 255, 255, 0.8);
                    border-top-color: rgba(255, 255, 255, 0.3);
                }
                
                .wptext-update-quote blockquote {
                    background: rgba(255, 255, 255, 0.2);
                    border-left-color: white;
                    color: white;
                }
                
                .wptext-update-location .wptext-location-name {
                    color: white;
                }
                ";
                break;
                
            case 'shadow':
                $css .= "
                .wptext-update {
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                }
                
                .wptext-update:hover {
                    opacity: 1;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
                    transform: translateY(-4px);
                }
                
                .wptext-update-meta {
                    color: {$primary_color};
                }
                
                .wptext-update-quote blockquote {
                    background: #f7f7f7;
                    border-left-color: {$primary_color};
                }
                
                .wptext-update-location .wptext-location-name {
                    color: {$primary_color};
                }
                ";
                break;
                
            case 'gradient':
                $css .= "
                .wptext-update {
                    background: linear-gradient(135deg, {$primary_color} 0%, " . self::adjust_brightness($primary_color, -30) . " 100%);
                    border-radius: 12px;
                    color: white;
                }
                
                .wptext-update-content {
                    color: white;
                }
                
                .wptext-update:hover {
                    opacity: 0.9;
                }
                
                .wptext-update-meta {
                    color: rgba(255, 255, 255, 0.9);
                    border-top-color: rgba(255, 255, 255, 0.3);
                }
                
                .wptext-update-quote blockquote {
                    background: rgba(255, 255, 255, 0.2);
                    border-left-color: white;
                    color: white;
                }
                
                .wptext-update-location .wptext-location-name {
                    color: white;
                }
                ";
                break;
                
            case 'modern':
            default:
                $css .= "
                .wptext-update {
                    background: white;
                    border-radius: 12px;
                }
                
                .wptext-update:hover {
                    opacity: 0.9;
                }
                
                .wptext-update-meta {
                    color: {$primary_color};
                }
                
                .wptext-update-quote blockquote {
                    background: #f7f7f7;
                    border-left-color: {$primary_color};
                }
                
                .wptext-update-location .wptext-location-name {
                    color: {$primary_color};
                }
                ";
                break;
        }
        
        return $css;
    }
    
    private static function adjust_brightness($hex, $steps) {
        $hex = str_replace('#', '', $hex);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
