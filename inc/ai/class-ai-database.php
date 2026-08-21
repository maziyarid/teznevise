<?php
/**
 * TezNevise AI Database - Per-tool database tables
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_Database {
    const VERSION = '2.0.0';
    const PREFIX = 'teznevise_ai_';
    
    public static function init() {
        add_action('init', [__CLASS__, 'check_database']);
    }
    
    public static function activate() {
        self::create_tables();
        self::insert_defaults();
        flush_rewrite_rules();
    }
    
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    public static function check_database() {
        if (get_option('teznevise_ai_db_version') !== self::VERSION) {
            self::create_tables();
            self::insert_defaults();
            update_option('teznevise_ai_db_version', self::VERSION);
        }
    }
    
    public static function create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        $prefix = self::PREFIX;
        
        $sql_sessions = "CREATE TABLE {$wpdb->prefix}{$prefix}chat_sessions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            tool_id VARCHAR(100) NOT NULL,
            session_id VARCHAR(255) NOT NULL,
            agent_id VARCHAR(100) NOT NULL,
            model VARCHAR(100) NOT NULL,
            collaboration_mode ENUM('single', 'collaborative', 'separate') NOT NULL DEFAULT 'single',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY tool_id (tool_id),
            KEY session_id (session_id),
            KEY created_at (created_at)
        ) $charset;";
        
        $sql_messages = "CREATE TABLE {$wpdb->prefix}{$prefix}chat_messages (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            session_id BIGINT(20) UNSIGNED NOT NULL,
            tool_id VARCHAR(100) NOT NULL,
            role ENUM('user', 'assistant', 'system') NOT NULL,
            content TEXT NOT NULL,
            agent_name VARCHAR(255) NULL,
            model VARCHAR(100) NULL,
            thinking_process TEXT NULL,
            token_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY tool_id (tool_id),
            KEY created_at (created_at)
        ) $charset;";
        
        $sql_usage = "CREATE TABLE {$wpdb->prefix}{$prefix}usage_tracking (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            tool_id VARCHAR(100) NOT NULL,
            date DATE NOT NULL,
            message_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            token_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            cost DECIMAL(10,4) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY tool_id (tool_id),
            KEY date (date),
            UNIQUE KEY user_tool_date (user_id, tool_id, date)
        ) $charset;";
        
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
        ) $charset;";
        
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
        ) $charset;";
        
        $sql_tool_settings = "CREATE TABLE {$wpdb->prefix}{$prefix}tool_settings (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tool_id VARCHAR(100) NOT NULL,
            setting_name VARCHAR(255) NOT NULL,
            setting_value TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY tool_setting (tool_id, setting_name)
        ) $charset;";
        
        $sql_skills = "CREATE TABLE {$wpdb->prefix}{$prefix}skills (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            agent_id VARCHAR(100) NOT NULL,
            skill_id VARCHAR(100) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            prompt TEXT NULL,
            temperature FLOAT NOT NULL DEFAULT 0.7,
            max_tokens INT(11) NOT NULL DEFAULT 1500,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY agent_skill (agent_id, skill_id)
        ) $charset;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_sessions);
        dbDelta($sql_messages);
        dbDelta($sql_usage);
        dbDelta($sql_credits);
        dbDelta($sql_agents);
        dbDelta($sql_tool_settings);
        dbDelta($sql_skills);
    }
    
    public static function insert_defaults() {
        global $wpdb;
        $prefix = self::PREFIX;
        $agents_table = $wpdb->prefix . $prefix . 'agents';
        $default_agents = [
            ['agent_id' => 'general', 'name' => 'Assistants', 'description' => 'General purpose assistant for all tools', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4', 'color' => '#3b82f6', 'icon' => 'brain', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 0],
            ['agent_id' => 'math', 'name' => 'Math Expert', 'description' => 'Specialized in mathematical calculations and explanations', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4', 'color' => '#10b981', 'icon' => 'sparkles', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['agent_id' => 'stats', 'name' => 'Statistics Helper', 'description' => 'Expert in statistical analysis and interpretation', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4', 'color' => '#8b5cf6', 'icon' => 'brain', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 2],
        ];
        foreach ($default_agents as $agent) {
            $wpdb->replace($agents_table, $agent);
        }
        $skills_table = $wpdb->prefix . $prefix . 'skills';
        $default_skills = [
            ['agent_id' => 'general', 'skill_id' => 'general_help', 'name' => 'General Help', 'description' => 'Provides general assistance', 'prompt' => 'You are a helpful AI assistant. Answer questions clearly and concisely.', 'temperature' => 0.7, 'max_tokens' => 1500],
            ['agent_id' => 'general', 'skill_id' => 'explain_concepts', 'name' => 'Explain Concepts', 'description' => 'Explains statistical concepts', 'prompt' => 'You are an expert in statistics. Explain concepts in simple terms with examples.', 'temperature' => 0.5, 'max_tokens' => 2000],
            ['agent_id' => 'math', 'skill_id' => 'solve_equations', 'name' => 'Solve Equations', 'description' => 'Solves mathematical equations', 'prompt' => 'You are a math expert. Solve equations step by step showing all work.', 'temperature' => 0.3, 'max_tokens' => 2000],
            ['agent_id' => 'math', 'skill_id' => 'calculate_values', 'name' => 'Calculate Values', 'description' => 'Performs calculations', 'prompt' => 'You are a calculator. Perform calculations accurately and show the steps.', 'temperature' => 0.2, 'max_tokens' => 1000],
            ['agent_id' => 'stats', 'skill_id' => 'interpret_results', 'name' => 'Interpret Results', 'description' => 'Interprets statistical results', 'prompt' => 'You are a statistics expert. Interpret p-values, confidence intervals, and effect sizes for non-technical users.', 'temperature' => 0.4, 'max_tokens' => 1800],
            ['agent_id' => 'stats', 'skill_id' => 'select_tests', 'name' => 'Select Tests', 'description' => 'Helps select statistical tests', 'prompt' => 'You are a statistics consultant. Help users select the appropriate statistical test for their data and research question.', 'temperature' => 0.5, 'max_tokens' => 1500],
        ];
        foreach ($default_skills as $skill) {
            $wpdb->replace($skills_table, $skill);
        }
    }
    
    public static function get_setting($tool_id, $name, $default = null) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'tool_settings';
        $value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $table WHERE tool_id = %s AND setting_name = %s", $tool_id, $name));
        return $value !== null ? maybe_unserialize($value) : $default;
    }
    
    public static function update_setting($tool_id, $name, $value) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'tool_settings';
        $wpdb->replace($table, ['tool_id' => $tool_id, 'setting_name' => $name, 'setting_value' => maybe_serialize($value)]);
    }
    
    public static function get_agent($agent_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'agents';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE agent_id = %s AND is_active = 1", $agent_id), ARRAY_A);
    }
    
    public static function get_all_agents() {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'agents';
        return $wpdb->get_results("SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order ASC");
    }
    
    public static function get_skills($agent_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'skills';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE agent_id = %s AND is_active = 1", $agent_id));
    }
    
    public static function save_session($data) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'chat_sessions';
        $wpdb->insert($table, $data);
        return $wpdb->insert_id;
    }
    
    public static function save_message($data) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'chat_messages';
        $wpdb->insert($table, $data);
        return $wpdb->insert_id;
    }
}