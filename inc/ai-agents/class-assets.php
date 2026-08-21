<?php
/**
 * Assets for TezNevise AI
 */

class TezNevise_AI_Assets {
    
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        add_action('wp_enqueue_styles', [__CLASS__, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts']);
        add_action('admin_enqueue_styles', [__CLASS__, 'admin_enqueue_styles']);
    }
    
    public static function enqueue_scripts() {
        global $post;
        
        // Check if we need to enqueue scripts
        $needs_chat = false;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'teznevise_ai_chat')) {
            $needs_chat = true;
        }
        
        // Always enqueue React for WordPress
        if ($needs_chat) {
            wp_enqueue_script('wp-element');
            
            wp_enqueue_script(
                'teznevise-ai-chat',
                get_template_directory_uri() . '/js/teznevise-ai-chat.js',
                ['wp-element', 'wp-i18n', 'wp-api-fetch', 'wp-url'],
                '2.0.0',
                true
            );
        }
    }
    
    public static function enqueue_styles() {
        global $post;
        
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'teznevise_ai_chat')) {
            wp_enqueue_style(
                'teznevise-ai-chat',
                get_template_directory_uri() . '/css/teznevise-ai-chat.css',
                [],
                '2.0.0'
            );
        }
    }
    
    public static function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'teznevise-ai') !== false) {
            wp_enqueue_script('jquery');
        }
    }
    
    public static function admin_enqueue_styles($hook) {
        if (strpos($hook, 'teznevise-ai') !== false) {
            wp_enqueue_style(
                'teznevise-ai-admin',
                get_template_directory_uri() . '/css/teznevise-ai-admin.css',
                [],
                '2.0.0'
            );
        }
    }
}
