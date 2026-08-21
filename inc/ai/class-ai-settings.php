<?php
/**
 * TezNevise AI Settings - Settings management
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_Settings {
    const OPTION_PREFIX = 'teznevise_ai_';
    
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }
    
    public static function add_admin_menu() {
        add_submenu_page(
            'options-general.php',
            'TezNevise AI',
            'TezNevise AI',
            'manage_options',
            'teznevise-ai-settings',
            [__CLASS__, 'render_settings_page']
        );
    }
    
    public static function register_settings() {
        add_settings_section(
            'teznevise_ai_general_settings',
            'General Settings',
            [__CLASS__, 'render_general_settings_section'],
            'teznevise-ai-settings'
        );
        
        register_setting('teznevise_ai_settings', self::OPTION_PREFIX . 'openai_key');
        register_setting('teznevise_ai_settings', self::OPTION_PREFIX . 'default_agent');
        register_setting('teznevise_ai_settings', self::OPTION_PREFIX . 'free_tier_limit');
        register_setting('teznevise_ai_settings', self::OPTION_PREFIX . 'signed_in_limit');
        register_setting('teznevise_ai_settings', self::OPTION_PREFIX . 'cost_per_message');
        
        add_settings_field(
            self::OPTION_PREFIX . 'openai_key',
            'OpenAI API Key',
            [__CLASS__, 'render_input_field'],
            'teznevise-ai-settings',
            'teznevise_ai_general_settings',
            ['label_for' => self::OPTION_PREFIX . 'openai_key']
        );
        
        add_settings_field(
            self::OPTION_PREFIX . 'default_agent',
            'Default Agent',
            [__CLASS__, 'render_select_field'],
            'teznevise-ai-settings',
            'teznevise_ai_general_settings',
            ['label_for' => self::OPTION_PREFIX . 'default_agent', 'options' => self::get_agent_options()]
        );
        
        add_settings_field(
            self::OPTION_PREFIX . 'free_tier_limit',
            'Free Tier Limit (messages per day)',
            [__CLASS__, 'render_input_field'],
            'teznevise-ai-settings',
            'teznevise_ai_general_settings',
            ['label_for' => self::OPTION_PREFIX . 'free_tier_limit', 'type' => 'number']
        );
        
        add_settings_field(
            self::OPTION_PREFIX . 'signed_in_limit',
            'Signed-In User Limit (messages per day)',
            [__CLASS__, 'render_input_field'],
            'teznevise-ai-settings',
            'teznevise_ai_general_settings',
            ['label_for' => self::OPTION_PREFIX . 'signed_in_limit', 'type' => 'number']
        );
        
        add_settings_field(
            self::OPTION_PREFIX . 'cost_per_message',
            'Cost Per Message (credits)',
            [__CLASS__, 'render_input_field'],
            'teznevise-ai-settings',
            'teznevise_ai_general_settings',
            ['label_for' => self::OPTION_PREFIX . 'cost_per_message', 'type' => 'number', 'step' => '0.01']
        );
    }
    
    public static function get_agent_options() {
        $agents = TezNevise_AI_Database::get_all_agents();
        $options = [];
        foreach ($agents as $agent) {
            $options[$agent['agent_id']] = $agent['name'];
        }
        return $options;
    }
    
    public static function render_general_settings_section() {
        echo '<p>Configure general AI settings for TezNevise.</p>';
    }
    
    public static function render_input_field($args) {
        $option = get_option($args['label_for']);
        $type = $args['type'] ?? 'text';
        $step = $args['step'] ?? '1';
        echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" value="' . esc_attr($option) . '" step="' . esc_attr($step) . '" class="regular-text" />';
    }
    
    public static function render_select_field($args) {
        $option = get_option($args['label_for']);
        $options = $args['options'] ?? [];
        echo '<select id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '">';
        foreach ($options as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($option, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }
    
    public static function render_settings_page() {
        if (!current_user_can('manage_options')) return;
        ?>
        <div class="wrap">
            <h1>TezNevise AI Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('teznevise_ai_settings');
                do_settings_sections('teznevise-ai-settings');
                submit_button();
                ?>
            </form>
            <hr>
            <h2>Tools Configuration</h2>
            <p>Each tool has its own AI configuration file in <code>inc/ai/tools/</code></p>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr><th>Tool ID</th><th>Name</th><th>Default Agent</th><th>Free Tier Limit</th><th>Signed-In Limit</th><th>Cost Per Message</th></tr>
                </thead>
                <tbody>
                    <?php
                    $tools = TezNevise_AI_Core::get_all_tools();
                    foreach ($tools as $tool_id => $tool) {
                        echo '<tr>';
                        echo '<td>' . esc_html($tool_id) . '</td>';
                        echo '<td>' . esc_html($tool['name'] ?? '') . '</td>';
                        echo '<td>' . esc_html($tool['default_agent'] ?? '') . '</td>';
                        echo '<td>' . esc_html($tool['free_tier_limit'] ?? 10) . '</td>';
                        echo '<td>' . esc_html($tool['signed_in_limit'] ?? 100) . '</td>';
                        echo '<td>' . esc_html($tool['cost_per_message'] ?? 0.01) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}