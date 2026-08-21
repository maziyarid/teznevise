<?php
/**
 * TezNevise AI Chat - Chat component rendering
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_Chat {
    
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_shortcode('teznevise_ai_chat', [__CLASS__, 'render_generic_chat']);
    }
    
    public static function enqueue_assets() {
        wp_enqueue_script('wp-element');
        wp_enqueue_script('wp-i18n');
        wp_enqueue_script('wp-url');
        
        wp_enqueue_script(
            'teznevise-ai',
            get_template_directory_uri() . '/js/teznevise-ai.js',
            ['wp-element', 'wp-i18n', 'wp-url', 'wp-api-fetch'],
            filemtime(get_template_directory() . '/js/teznevise-ai.js'),
            true
        );
        
        wp_enqueue_script(
            'teznevise-ai-chat',
            get_template_directory_uri() . '/js/ai/chat.js',
            ['teznevise-ai'],
            filemtime(get_template_directory() . '/js/ai/chat.js'),
            true
        );
        
        wp_enqueue_style(
            'teznevise-ai',
            get_template_directory_uri() . '/css/teznevise-ai.css',
            [],
            filemtime(get_template_directory() . '/css/teznevise-ai.css')
        );
        
        wp_localize_script('teznevise-ai', 'tezneviseAiConfig', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url(),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'currentUserId' => get_current_user_id(),
            'settings' => [
                'persian_initial_message' => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
                'free_tier_limit' => 10,
                'signed_in_limit' => 100,
            ],
            'agents' => self::get_agent_configs(),
        ]);
    }
    
    private static function get_agent_configs() {
        $agents = TezNevise_AI_Database::get_all_agents();
        $configs = [];
        foreach ($agents as $agent) {
            $configs[] = [
                'id' => $agent['agent_id'],
                'name' => $agent['name'],
                'description' => $agent['description'],
                'model' => $agent['model'],
                'color' => $agent['color'],
                'icon' => $agent['icon'],
                'thinking_enabled' => (bool) $agent['thinking_enabled'],
            ];
        }
        return $configs;
    }
    
    public static function render_chat($atts) {
        $atts = shortcode_atts([
            'tool_id' => '',
            'agent_id' => 'general',
            'collaboration_mode' => 'single',
            'thinking_enabled' => true,
            'tool_config' => [],
        ], $atts);
        
        $tool_id = $atts['tool_id'];
        $tool_config = $atts['tool_config'];
        
        if (empty($tool_config)) {
            $tool_config = TezNevise_AI_Core::get_tool($tool_id) ?: [];
        }
        
        self::enqueue_assets();
        
        $instance_id = 'teznevise-ai-chat-' . $tool_id . '-' . wp_generate_password(4, false);
        
        $tool_config_json = wp_json_encode([
            'id' => $tool_id,
            'name' => $tool_config['name'] ?? '',
            'description' => $tool_config['description'] ?? '',
            'default_agent' => $tool_config['default_agent'] ?? 'general',
            'default_agent_name' => $tool_config['default_agent_name'] ?? 'Assistants',
            'initial_message' => $tool_config['initial_message'] ?? 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
            'free_tier_limit' => $tool_config['free_tier_limit'] ?? 10,
            'signed_in_limit' => $tool_config['signed_in_limit'] ?? 100,
            'cost_per_message' => $tool_config['cost_per_message'] ?? 0.01,
            'recommended_agents' => $tool_config['recommended_agents'] ?? ['general'],
            'collaboration_mode' => $atts['collaboration_mode'],
            'thinking_enabled' => $atts['thinking_enabled'],
        ]);
        
        ob_start();
        ?>
        <div id="<?php echo esc_attr($instance_id); ?>" 
             class="teznevise-ai-chat-wrapper"
             data-tool-id="<?php echo esc_attr($tool_id); ?>"
             data-agent-id="<?php echo esc_attr($atts['agent_id']); ?>"
             data-collaboration-mode="<?php echo esc_attr($atts['collaboration_mode']); ?>"
             data-thinking-enabled="<?php echo esc_attr($atts['thinking_enabled'] ? 'true' : 'false'); ?>"
             data-tool-config="<?php echo esc_attr($tool_config_json); ?>">
        </div>
        
        <script type="text/javascript">
        (function() {
            var container = document.getElementById('<?php echo esc_js($instance_id); ?>');
            if (container) {
                var toolConfig = <?php echo $tool_config_json; ?>;
                ReactDOM.createRoot(container).render(
                    React.createElement(TezNeviseAIChat, {
                        toolId: '<?php echo esc_js($tool_id); ?>',
                        agentId: '<?php echo esc_js($atts["agent_id"]); ?>',
                        collaborationMode: '<?php echo esc_js($atts["collaboration_mode"]); ?>',
                        thinkingEnabled: <?php echo $atts['thinking_enabled'] ? 'true' : 'false'; ?>,
                        toolConfig: toolConfig
                    })
                );
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
    
    public static function render_generic_chat($atts) {
        $atts = shortcode_atts([
            'agent_id' => 'general',
            'collaboration_mode' => 'single',
            'thinking_enabled' => true,
        ], $atts);
        
        return self::render_chat([
            'tool_id' => 'generic',
            'agent_id' => $atts['agent_id'],
            'collaboration_mode' => $atts['collaboration_mode'],
            'thinking_enabled' => $atts['thinking_enabled'],
            'tool_config' => [
                'id' => 'generic',
                'name' => 'General AI Chat',
                'description' => 'General purpose AI assistant',
                'default_agent' => $atts['agent_id'],
                'default_agent_name' => 'Assistants',
                'initial_message' => 'اگه سوالی داری میتونی از من بپرسی',
                'free_tier_limit' => 10,
                'signed_in_limit' => 100,
                'cost_per_message' => 0.01,
                'recommended_agents' => ['general'],
            ],
        ]);
    }
}