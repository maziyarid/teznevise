<?php
/**
 * Database Handler for TezNevise AI
 */

class TezNevise_AI_Database {
    
    public static function init() {
        add_action('init', [__CLASS__, 'check_database']);
    }
    
    public static function activate() {
        self::create_tables();
        self::insert_default_data();
        flush_rewrite_rules();
    }
    
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    public static function check_database() {
        if (get_option('teznevise_ai_db_version') !== '2.0.0') {
            self::create_tables();
            self::insert_default_data();
            update_option('teznevise_ai_db_version', '2.0.0');
        }
    }
    
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $prefix = 'teznevise_ai_';
        
        // Chat sessions
        $sql_sessions = "CREATE TABLE {$wpdb->prefix}{$prefix}chat_sessions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            session_id VARCHAR(255) NOT NULL,
            tool_id VARCHAR(255) NOT NULL DEFAULT '',
            agent_id VARCHAR(100) NOT NULL DEFAULT 'general',
            model VARCHAR(100) NOT NULL DEFAULT 'gpt-4',
            collaboration_mode ENUM('single', 'collaborative', 'separate') NOT NULL DEFAULT 'single',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY tool_id (tool_id)
        ) $charset_collate;";
        
        // Chat messages
        $sql_messages = "CREATE TABLE {$wpdb->prefix}{$prefix}chat_messages (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            session_id BIGINT(20) UNSIGNED NOT NULL,
            role ENUM('user', 'assistant', 'system') NOT NULL,
            content TEXT NOT NULL,
            agent_name VARCHAR(255) NULL,
            model VARCHAR(100) NULL,
            thinking_process TEXT NULL,
            token_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id)
        ) $charset_collate;";
        
        // User credits
        $sql_credits = "CREATE TABLE {$wpdb->prefix}{$prefix}user_credits (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            credit_balance DECIMAL(10,2) NOT NULL DEFAULT 0,
            total_earned DECIMAL(10,2) NOT NULL DEFAULT 0,
            total_used DECIMAL(10,2) NOT NULL DEFAULT 0,
            last_credit_date DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        
        // Usage tracking
        $sql_usage = "CREATE TABLE {$wpdb->prefix}{$prefix}usage_tracking (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            date DATE NOT NULL,
            message_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            token_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            cost DECIMAL(10,4) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY date (date),
            UNIQUE KEY user_date (user_id, date)
        ) $charset_collate;";
        
        // Agents
        $sql_agents = "CREATE TABLE {$wpdb->prefix}{$prefix}agents (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            agent_id VARCHAR(100) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            api_endpoint VARCHAR(500) NOT NULL,
            api_key TEXT NULL,
            model VARCHAR(100) NOT NULL DEFAULT 'gpt-4',
            color VARCHAR(50) NOT NULL DEFAULT '#3b82f6',
            icon VARCHAR(100) NULL,
            thinking_enabled TINYINT(1) NOT NULL DEFAULT 1,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT(11) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY agent_id (agent_id)
        ) $charset_collate;";
        
        // Settings
        $sql_settings = "CREATE TABLE {$wpdb->prefix}{$prefix}settings (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            setting_name VARCHAR(255) NOT NULL,
            setting_value TEXT NULL,
            autoload TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_name (setting_name)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_sessions);
        dbDelta($sql_messages);
        dbDelta($sql_credits);
        dbDelta($sql_usage);
        dbDelta($sql_agents);
        dbDelta($sql_settings);
    }
    
    public static function insert_default_data() {
        global $wpdb;
        
        $prefix = 'teznevise_ai_';
        
        // Insert default agents
        $agents_table = $wpdb->prefix . $prefix . 'agents';
        $default_agents = [
            [
                'agent_id' => 'general',
                'name' => 'Assistants',
                'description' => 'General purpose assistant for all calculation tools',
                'api_endpoint' => 'https://api.openai.com/v1/chat/completions',
                'api_key' => '',
                'model' => 'gpt-4',
                'color' => '#3b82f6',
                'icon' => 'brain',
                'thinking_enabled' => 1,
                'is_active' => 1,
                'sort_order' => 0,
            ],
            [
                'agent_id' => 'math',
                'name' => 'Math Expert',
                'description' => 'Specialized in mathematical calculations and explanations',
                'api_endpoint' => 'https://api.openai.com/v1/chat/completions',
                'api_key' => '',
                'model' => 'gpt-4',
                'color' => '#10b981',
                'icon' => 'sparkles',
                'thinking_enabled' => 1,
                'is_active' => 1,
                'sort_order' => 1,
            ],
            [
                'agent_id' => 'stats',
                'name' => 'Statistics Helper',
                'description' => 'Expert in statistical analysis and interpretation',
                'api_endpoint' => 'https://api.openai.com/v1/chat/completions',
                'api_key' => '',
                'model' => 'gpt-4',
                'color' => '#8b5cf6',
                'icon' => 'brain',
                'thinking_enabled' => 1,
                'is_active' => 1,
                'sort_order' => 2,
            ],
        ];
        
        foreach ($default_agents as $agent) {
            $wpdb->replace($agents_table, $agent);
        }
        
        // Insert default settings
        $settings_table = $wpdb->prefix . $prefix . 'settings';
        $default_settings = [
            ['setting_name' => 'free_tier_limit', 'setting_value' => '10', 'autoload' => 1],
            ['setting_name' => 'signed_in_limit', 'setting_value' => '100', 'autoload' => 1],
            ['setting_name' => 'cost_per_message', 'setting_value' => '0.01', 'autoload' => 1],
            ['setting_name' => 'default_agent', 'setting_value' => 'general', 'autoload' => 1],
            ['setting_name' => 'collaboration_mode', 'setting_value' => 'single', 'autoload' => 1],
            ['setting_name' => 'thinking_process_enabled', 'setting_value' => '1', 'autoload' => 1],
            ['setting_name' => 'persian_initial_message', 'setting_value' => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری', 'autoload' => 1],
        ];
        
        foreach ($default_settings as $setting) {
            $wpdb->replace($settings_table, $setting);
        }
    }
    
    // Helper methods
    public static function get_setting($name, $default = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_settings';
        $value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $table WHERE setting_name = %s", $name));
        return $value !== null ? maybe_unserialize($value) : $default;
    }
    
    public static function update_setting($name, $value) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_settings';
        $wpdb->replace($table, ['setting_name' => $name, 'setting_value' => maybe_serialize($value)]);
    }
    
    public static function get_agent($agent_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_agents';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE agent_id = %s AND is_active = 1", $agent_id), ARRAY_A);
    }
    
    public static function get_all_agents() {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_agents';
        return $wpdb->get_results("SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order ASC");
    }
}
