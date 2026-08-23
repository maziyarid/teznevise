<?php
/**
 * TezNevise AI Database - Per-tool database tables
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_Database {
    const VERSION = '2.4.0';
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
            collaboration_mode VARCHAR(32) NOT NULL DEFAULT 'single',
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
            provider VARCHAR(50) NOT NULL DEFAULT 'openai',
            api_endpoint VARCHAR(500) NOT NULL,
            api_key TEXT NULL,
            model VARCHAR(100) NOT NULL DEFAULT 'gpt-4',
            color VARCHAR(50) NOT NULL DEFAULT '#3b82f6',
            icon VARCHAR(100) NULL,
            thinking_enabled TINYINT(1) NOT NULL DEFAULT 1,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT(11) NOT NULL DEFAULT 0,
            system_prompt TEXT NULL,
            role VARCHAR(40) NOT NULL DEFAULT 'general',
            language VARCHAR(12) NOT NULL DEFAULT 'fa',
            temperature FLOAT NOT NULL DEFAULT 0.7,
            max_tokens INT(11) NOT NULL DEFAULT 1500,
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
        $sessions_table = $wpdb->prefix . $prefix . 'chat_sessions';
        // Widen ENUM so research collaboration can persist. Ignore if already VARCHAR.
        $suppress = $wpdb->hide_errors();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
        $wpdb->query( "ALTER TABLE {$sessions_table} MODIFY collaboration_mode VARCHAR(32) NOT NULL DEFAULT 'single'" );
        $wpdb->show_errors( $suppress );
    }
    
    public static function insert_defaults() {
        global $wpdb;
        $prefix = self::PREFIX;
        $agents_table = $wpdb->prefix . $prefix . 'agents';
        $default_agents = [
            ['agent_id' => 'you', 'name' => 'You', 'description' => 'پژوهشگر You.com: جستجوی جامع و بازگرداندن یافته‌ها با منبع برای بقیه عامل‌ها', 'provider' => 'you', 'api_endpoint' => 'https://api.ydc-index.io/v1/search', 'api_key' => '', 'model' => 'you-search', 'color' => '#111827', 'icon' => 'search', 'thinking_enabled' => 0, 'is_active' => 1, 'sort_order' => -1, 'system_prompt' => 'You are You.com, the research agent for Teznevise. Search comprehensively. Return Findings (claims, counterpoints) and Sources. Prefer Persian academic sources when the user writes in Persian. Never invent citations.', 'role' => 'researcher', 'language' => 'fa', 'temperature' => 0.2, 'max_tokens' => 1800],
            ['agent_id' => 'general', 'name' => 'دستیار پژوهشی', 'description' => 'دستیار عمومی برای روش تحقیق، نگارش و ابزارها', 'provider' => 'openai', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4o-mini', 'color' => '#145d4a', 'icon' => 'brain', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 0, 'system_prompt' => 'You are Teznevise’s research assistant. Read the user question and any You.com research brief. Answer in Persian for Persian questions. Cite the brief. Be concrete for graduate students.', 'role' => 'general', 'language' => 'fa', 'temperature' => 0.6, 'max_tokens' => 1800],
            ['agent_id' => 'math', 'name' => 'متخصص ریاضی', 'description' => 'محاسبات و توضیح گام‌به‌گام ریاضی', 'provider' => 'openai', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4o-mini', 'color' => '#1d4ed8', 'icon' => 'sparkles', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 1, 'system_prompt' => 'You are a mathematics specialist. Show steps. If a You.com brief is present, use its formulas only when they match the question.', 'role' => 'analyst', 'language' => 'fa', 'temperature' => 0.2, 'max_tokens' => 2000],
            ['agent_id' => 'stats', 'name' => 'یاور آمار', 'description' => 'انتخاب آزمون و تفسیر نتایج آماری', 'provider' => 'openai', 'api_endpoint' => 'https://api.openai.com/v1/chat/completions', 'api_key' => '', 'model' => 'gpt-4o-mini', 'color' => '#7c3aed', 'icon' => 'brain', 'thinking_enabled' => 1, 'is_active' => 1, 'sort_order' => 2, 'system_prompt' => 'You are an applied statistician. Recommend tests, assumptions, and interpretation. Ground answers in the user’s data description and any You research brief.', 'role' => 'statistician', 'language' => 'fa', 'temperature' => 0.35, 'max_tokens' => 1800],
            ['agent_id' => 'gemini_flash', 'name' => 'Gemini Flash', 'description' => 'مدل رایگان‌تر گوگل برای پاسخ سریع فارسی', 'provider' => 'gemini', 'api_endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', 'api_key' => '', 'model' => 'gemini-2.0-flash', 'color' => '#0369a1', 'icon' => 'sparkles', 'thinking_enabled' => 1, 'is_active' => 0, 'sort_order' => 3],
            ['agent_id' => 'openrouter_free', 'name' => 'OpenRouter', 'description' => 'مسیریاب مدل‌های متن‌باز و رایگان OpenRouter', 'provider' => 'openrouter', 'api_endpoint' => 'https://openrouter.ai/api/v1/chat/completions', 'api_key' => '', 'model' => 'openrouter/auto', 'color' => '#b45309', 'icon' => 'brain', 'thinking_enabled' => 1, 'is_active' => 0, 'sort_order' => 4],
        ];
        if ( function_exists( 'teznevise_core_agent_roster' ) ) {
            foreach ( teznevise_core_agent_roster() as $id => $row ) {
                $models = function_exists( 'teznevise_core_agent_models' ) ? teznevise_core_agent_models( $id ) : array( 'primary' => 'google/gemma-4-31b-it:free' );
                $default_agents[] = array(
                    'agent_id' => $id,
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'provider' => 'openrouter',
                    'api_endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
                    'api_key' => '',
                    'model' => $models['primary'],
                    'color' => $row['color'],
                    'icon' => $row['icon'],
                    'thinking_enabled' => 1,
                    'is_active' => 1,
                    'sort_order' => (int) $row['sort_order'],
                    'system_prompt' => $row['system_prompt'],
                    'role' => $row['role'],
                    'language' => 'fa',
                    'temperature' => (float) $row['temperature'],
                    'max_tokens' => (int) $row['max_tokens'],
                );
            }
        }
        foreach ($default_agents as $agent) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT agent_id FROM $agents_table WHERE agent_id = %s", $agent['agent_id']));
            if (!$exists) {
                $wpdb->insert($agents_table, $agent);
            }
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
        if ( function_exists( 'teznevise_core_agent_skills' ) ) {
            foreach ( teznevise_core_agent_skills() as $agent_id => $skills ) {
                foreach ( (array) $skills as $skill ) {
                    $default_skills[] = array(
                        'agent_id'    => $agent_id,
                        'skill_id'    => $skill['skill_id'],
                        'name'        => $skill['name'],
                        'description' => $skill['description'],
                        'prompt'      => $skill['prompt'],
                        'temperature' => (float) ( $skill['temperature'] ?? 0.5 ),
                        'max_tokens'  => (int) ( $skill['max_tokens'] ?? 900 ),
                    );
                }
            }
        }
        foreach ($default_skills as $skill) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT skill_id FROM $skills_table WHERE agent_id = %s AND skill_id = %s", $skill['agent_id'], $skill['skill_id']));
            if (!$exists) {
                $wpdb->insert($skills_table, $skill);
            }
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
        return $wpdb->get_results("SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order ASC", ARRAY_A);
    }
    
    public static function get_skills($agent_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'skills';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE agent_id = %s AND is_active = 1", $agent_id));
    }
    
    public static function save_session($data) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'chat_sessions';
        $token = isset($data['session_id']) ? (string) $data['session_id'] : '';
        $uid   = isset($data['user_id']) ? (int) $data['user_id'] : 0;
        if ($token !== '') {
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id, user_id FROM $table WHERE session_id = %s LIMIT 1", $token), ARRAY_A);
            if ($existing) {
                if ((int) $existing['user_id'] !== $uid) {
                    $data['session_id'] = wp_generate_uuid4();
                    $wpdb->insert($table, $data);
                    return array('id' => (int) $wpdb->insert_id, 'session_id' => (string) $data['session_id']);
                }
                return array('id' => (int) $existing['id'], 'session_id' => $token);
            }
        } else {
            $data['session_id'] = wp_generate_uuid4();
        }
        $wpdb->insert($table, $data);
        return array('id' => (int) $wpdb->insert_id, 'session_id' => (string) $data['session_id']);
    }

    public static function save_agent($data) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'agents';
        $agent_id = sanitize_key($data['agent_id'] ?? '');
        if ($agent_id === '') {
            return false;
        }
        $row = [
            'agent_id' => $agent_id,
            'name' => sanitize_text_field($data['name'] ?? $agent_id),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'provider' => sanitize_key($data['provider'] ?? 'openai'),
            'api_endpoint' => esc_url_raw($data['api_endpoint'] ?? ''),
            'api_key' => sanitize_text_field($data['api_key'] ?? ''),
            'model' => sanitize_text_field($data['model'] ?? ''),
            'color' => sanitize_hex_color($data['color'] ?? '#145d4a') ?: '#145d4a',
            'icon' => sanitize_text_field($data['icon'] ?? 'brain'),
            'thinking_enabled' => empty($data['thinking_enabled']) ? 0 : 1,
            'is_active' => empty($data['is_active']) ? 0 : 1,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'system_prompt' => sanitize_textarea_field($data['system_prompt'] ?? ''),
            'role' => sanitize_key($data['role'] ?? 'general') ?: 'general',
            'language' => sanitize_text_field($data['language'] ?? 'fa'),
            'temperature' => (float) ($data['temperature'] ?? 0.7),
            'max_tokens' => (int) ($data['max_tokens'] ?? 1500),
        ];
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE agent_id = %s", $agent_id));
        if ($exists) {
            if ($row['api_key'] === '') {
                unset($row['api_key']);
            }
            return false !== $wpdb->update($table, $row, ['agent_id' => $agent_id]);
        }
        return false !== $wpdb->insert($table, $row);
    }

    public static function delete_agent($agent_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'agents';
        return false !== $wpdb->delete($table, ['agent_id' => sanitize_key($agent_id)]);
    }

    public static function get_all_agents_admin() {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'agents';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, id ASC", ARRAY_A);
    }
    
    public static function save_message($data) {
        global $wpdb;
        $table = $wpdb->prefix . self::PREFIX . 'chat_messages';
        $wpdb->insert($table, $data);
        return $wpdb->insert_id;
    }
}