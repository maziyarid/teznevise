<?php
/**
 * Chat Functionality for TezNevise AI
 */

class TezNevise_AI_Chat {
    
    public static function init() {
        // Register shortcode
        add_shortcode('teznevise_ai_chat', [__CLASS__, 'render_chat']);
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        
        // Localize script
        add_action('wp_enqueue_scripts', [__CLASS__, 'localize_script']);
    }
    
    public static function render_chat($atts) {
        $atts = shortcode_atts([
            'tool_id' => '',
            'agent_id' => 'general',
            'collaboration_mode' => 'single',
            'thinking_enabled' => 'true',
        ], $atts);
        
        wp_enqueue_script('teznevise-ai-chat');
        wp_enqueue_style('teznevise-ai-chat');
        
        ob_start();
        ?>
        <div 
            class="teznevise-ai-chat-container" 
            id="teznevise-ai-chat-<?php echo esc_attr($atts['tool_id'] ?: uniqid()); ?>"
            data-tool-id="<?php echo esc_attr($atts['tool_id']); ?>"
            data-agent-id="<?php echo esc_attr($atts['agent_id']); ?>"
            data-collaboration-mode="<?php echo esc_attr($atts['collaboration_mode']); ?>"
            data-thinking-enabled="<?php echo esc_attr($atts['thinking_enabled']); ?>"
        ></div>
        <?php
        return ob_get_clean();
    }
    
    public static function enqueue_assets() {
        // Only enqueue if shortcode is present
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'teznevise_ai_chat')) {
            self::enqueue_scripts_and_styles();
        }
    }
    
    private static function enqueue_scripts_and_styles() {
        // Enqueue React (if not already enqueued)
        if (!wp_script_is('wp-element', 'enqueued')) {
            wp_enqueue_script('wp-element');
        }
        
        // Enqueue main script
        wp_enqueue_script(
            'teznevise-ai-chat',
            get_template_directory_uri() . '/js/teznevise-ai-chat.js',
            ['wp-element', 'wp-i18n', 'wp-api-fetch', 'wp-url'],
            '2.0.0',
            true
        );
        
        // Enqueue styles
        wp_enqueue_style(
            'teznevise-ai-chat',
            get_template_directory_uri() . '/css/teznevise-ai-chat.css',
            [],
            '2.0.0'
        );
    }
    
    public static function localize_script() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'teznevise_ai_chat')) {
            wp_localize_script('teznevise-ai-chat', 'tezneviseAiConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('teznevise-ai/v1'),
                'nonce' => wp_create_nonce('wp_rest'),
                'isLoggedIn' => is_user_logged_in(),
                'currentUserId' => is_user_logged_in() ? get_current_user_id() : 0,
                'settings' => self::get_settings_for_js(),
                'agents' => self::get_agents_for_js(),
            ]);
        }
    }
    
    private static function get_settings_for_js() {
        return [
            'free_tier_limit' => (int) TezNevise_AI_Database::get_setting('free_tier_limit', 10),
            'signed_in_limit' => (int) TezNevise_AI_Database::get_setting('signed_in_limit', 100),
            'cost_per_message' => (float) TezNevise_AI_Database::get_setting('cost_per_message', 0.01),
            'default_agent' => TezNevise_AI_Database::get_setting('default_agent', 'general'),
            'collaboration_mode' => TezNevise_AI_Database::get_setting('collaboration_mode', 'single'),
            'thinking_process_enabled' => (bool) TezNevise_AI_Database::get_setting('thinking_process_enabled', true),
            'persian_initial_message' => TezNevise_AI_Database::get_setting('persian_initial_message', 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری'),
        ];
    }
    
    private static function get_agents_for_js() {
        $agents = TezNevise_AI_Database::get_all_agents();
        return array_map(function($a) {
            return [
                'id' => $a->agent_id,
                'name' => $a->name,
                'description' => $a->description,
                'model' => $a->model,
                'color' => $a->color,
                'icon' => $a->icon,
                'thinking_enabled' => (bool) $a->thinking_enabled,
            ];
        }, $agents);
    }
}
