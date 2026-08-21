<?php
/**
 * TezNevise AI API - REST API endpoints for per-tool chat
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_API {
    
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }
    
    public static function register_routes() {
        register_rest_route('teznevise-ai/v1', '/chat', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_chat'],
            'permission_callback' => [__CLASS__, 'check_chat_permission'],
        ]);
        
        register_rest_route('teznevise-ai/v1', '/skills', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_skills'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('teznevise-ai/v1', '/agents', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_agents'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('teznevise-ai/v1', '/usage', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_usage'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('teznevise-ai/v1', '/session/start', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'start_session'],
            'permission_callback' => '__return_true',
        ]);
    }
    
    public static function check_chat_permission($request) {
        $tool_id = $request->get_param('tool_id');
        $tool = TezNevise_AI_Core::get_tool($tool_id);
        if (!$tool) return new WP_Error('invalid_tool', 'Invalid tool ID', ['status' => 400]);
        
        $user_id = get_current_user_id();
        $is_logged_in = $user_id > 0;
        $today = date('Y-m-d');
        $usage_key = 'usage_' . $today . '_' . $user_id;
        $usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
        $message_count = $usage['message_count'] ?? 0;
        $limit = $is_logged_in ? ($tool['signed_in_limit'] ?? 100) : ($tool['free_tier_limit'] ?? 10);
        
        if ($message_count >= $limit) {
            return new WP_Error('limit_reached', 'Message limit reached', ['status' => 402]);
        }
        return true;
    }
    
    public static function handle_chat($request) {
        $params = $request->get_params();
        $tool_id = $params['tool_id'];
        $message = $params['message'];
        $session_id = $params['session_id'];
        $agent_id = $params['agent_id'] ?? 'general';
        $model = $params['model'] ?? 'gpt-4';
        $collaboration_mode = $params['collaboration_mode'] ?? 'single';
        $thinking_enabled = $params['thinking_enabled'] ?? true;
        $skill_id = $params['skill_id'] ?? null;
        
        $tool = TezNevise_AI_Core::get_tool($tool_id);
        if (!$tool) return new WP_Error('invalid_tool', 'Invalid tool ID', ['status' => 400]);
        
        $user_id = get_current_user_id();
        $is_logged_in = $user_id > 0;
        $today = date('Y-m-d');
        $usage_key = 'usage_' . $today . '_' . $user_id;
        $usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
        $message_count = $usage['message_count'] ?? 0;
        $limit = $is_logged_in ? ($tool['signed_in_limit'] ?? 100) : ($tool['free_tier_limit'] ?? 10);
        
        if ($message_count >= $limit) {
            return ['success' => false, 'message' => 'Limit reached'];
        }
        
        $agent = TezNevise_AI_Database::get_agent($agent_id);
        if (!$agent) $agent = TezNevise_AI_Database::get_agent('general');
        
        $system_prompt = self::build_system_prompt($tool, $agent, $skill_id, $collaboration_mode);
        $response = self::call_ai_api($message, $system_prompt, $agent, $model, $thinking_enabled);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        
        $session_data = [
            'user_id' => $user_id,
            'tool_id' => $tool_id,
            'session_id' => $session_id,
            'agent_id' => $agent_id,
            'model' => $model,
            'collaboration_mode' => $collaboration_mode,
            'ip_address' => self::get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];
        
        $session_id_db = TezNevise_AI_Database::save_session($session_data);
        
        TezNevise_AI_Database::save_message([
            'session_id' => $session_id_db,
            'tool_id' => $tool_id,
            'role' => 'user',
            'content' => $message,
            'agent_name' => 'User',
            'model' => $model,
            'token_count' => self::count_tokens($message),
        ]);
        
        $thinking_process = $response['thinking_process'] ?? null;
        TezNevise_AI_Database::save_message([
            'session_id' => $session_id_db,
            'tool_id' => $tool_id,
            'role' => 'assistant',
            'content' => $response['content'],
            'agent_name' => $agent['name'],
            'model' => $model,
            'thinking_process' => $thinking_process,
            'token_count' => self::count_tokens($response['content']),
        ]);
        
        $usage['message_count'] = ($usage['message_count'] ?? 0) + 1;
        $usage['token_count'] = ($usage['token_count'] ?? 0) + self::count_tokens($message) + self::count_tokens($response['content']);
        $usage['cost'] = ($usage['cost'] ?? 0) + ($tool['cost_per_message'] ?? 0.01);
        TezNevise_AI_Database::update_setting($tool_id, $usage_key, $usage);
        
        if ($is_logged_in && $tool['cost_per_message'] > 0) {
            self::update_user_credits($user_id, -$tool['cost_per_message']);
        }
        
        return [
            'success' => true,
            'content' => $response['content'],
            'agent_name' => $agent['name'],
            'model' => $model,
            'thinking_process' => $thinking_process,
            'skill_used' => $skill_id,
            'usage' => ['today' => $usage['message_count'], 'tokens' => $usage['token_count'], 'cost' => $usage['cost']],
        ];
    }
    
    private static function build_system_prompt($tool, $agent, $skill_id, $collaboration_mode) {
        $prompt_parts = [];
        if (!empty($agent['description'])) $prompt_parts[] = $agent['description'];
        if (!empty($tool['context'])) $prompt_parts[] = "Tool Context: " . json_encode($tool['context']);
        if ($skill_id && isset($tool['skills'][$skill_id])) {
            $prompt_parts[] = $tool['skills'][$skill_id]['prompt'];
        }
        $prompt_parts[] = "Collaboration Mode: {$collaboration_mode}";
        $prompt_parts[] = "If the user writes in Persian, always respond in Persian. Otherwise, respond in English.";
        $prompt_parts[] = "If thinking is enabled, show your thought process step by step before giving the final answer.";
        return implode("\n\n", $prompt_parts);
    }
    
    private static function call_ai_api($message, $system_prompt, $agent, $model, $thinking_enabled) {
        $api_endpoint = $agent['api_endpoint'] ?? 'https://api.openai.com/v1/chat/completions';
        $api_key = $agent['api_key'] ?? get_option('teznevise_ai_openai_key', '');
        if (empty($api_key)) return new WP_Error('no_api_key', 'No API key configured');
        
        $temperature = 0.7;
        $max_tokens = 1500;
        
        $body = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
        ];
        
        $args = [
            'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $api_key],
            'body' => json_encode($body),
            'timeout' => 60,
        ];
        
        $response = wp_remote_post($api_endpoint, $args);
        if (is_wp_error($response)) return $response;
        
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($response_body['error'])) return new WP_Error('api_error', $response_body['error']['message'] ?? 'API error');
        
        $content = $response_body['choices'][0]['message']['content'] ?? '';
        return ['content' => $content, 'thinking_process' => $thinking_enabled ? $content : null];
    }
    
    private static function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
        return '0.0.0.0';
    }
    
    private static function count_tokens($text) {
        return max(1, ceil(strlen($text) / 4));
    }
    
    private static function update_user_credits($user_id, $amount) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_user_credits';
        $current = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
        $new_balance = ($current ? $current->credit_balance : 0) + $amount;
        if ($current) {
            $wpdb->update($table, ['credit_balance' => $new_balance, 'total_used' => $current->total_used + abs($amount), 'updated_at' => current_time('mysql')], ['user_id' => $user_id]);
        } else {
            $wpdb->insert($table, ['user_id' => $user_id, 'credit_balance' => $new_balance, 'total_used' => abs($amount), 'created_at' => current_time('mysql'), 'updated_at' => current_time('mysql')]);
        }
    }
    
    public static function get_skills($request) {
        $agent_id = $request->get_param('agent_id') ?? 'general';
        $skills = TezNevise_AI_Database::get_skills($agent_id);
        $formatted = [];
        foreach ($skills as $skill) {
            $formatted[] = ['skill_id' => $skill->skill_id, 'name' => $skill->name, 'description' => $skill->description, 'temperature' => $skill->temperature, 'max_tokens' => $skill->max_tokens];
        }
        return $formatted;
    }
    
    public static function get_agents($request) {
        $agents = TezNevise_AI_Database::get_all_agents();
        $formatted = [];
        foreach ($agents as $agent) {
            $formatted[] = ['id' => $agent['agent_id'], 'name' => $agent['name'], 'description' => $agent['description'], 'model' => $agent['model'], 'color' => $agent['color'], 'icon' => $agent['icon'], 'thinking_enabled' => (bool) $agent['thinking_enabled']];
        }
        return $formatted;
    }
    
    public static function get_usage($request) {
        $tool_id = $request->get_param('tool_id') ?? '';
        $user_id = get_current_user_id();
        $is_logged_in = $user_id > 0;
        $today = date('Y-m-d');
        $usage_key = 'usage_' . $today . '_' . $user_id;
        $usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
        $tool = TezNevise_AI_Core::get_tool($tool_id);
        $limit = $is_logged_in ? ($tool['signed_in_limit'] ?? 100) : ($tool['free_tier_limit'] ?? 10);
        $credits = 0;
        if ($is_logged_in) {
            global $wpdb;
            $table = $wpdb->prefix . 'teznevise_ai_user_credits';
            $credit_row = $wpdb->get_row($wpdb->prepare("SELECT credit_balance FROM $table WHERE user_id = %d", $user_id));
            $credits = $credit_row ? (float) $credit_row->credit_balance : 0;
        }
        return ['today' => $usage['message_count'] ?? 0, 'thisWeek' => 0, 'thisMonth' => 0, 'total' => 0, 'credits' => $credits, 'limit' => $limit];
    }
    
    public static function start_session($request) {
        $params = $request->get_params();
        $session_id = 'session-' . $params['tool_id'] . '-' . time() . '-' . wp_generate_password(8, false);
        $session_data = [
            'user_id' => get_current_user_id(),
            'tool_id' => $params['tool_id'],
            'session_id' => $session_id,
            'agent_id' => $params['agent_id'] ?? 'general',
            'model' => $params['model'] ?? 'gpt-4',
            'collaboration_mode' => $params['collaboration_mode'] ?? 'single',
            'ip_address' => self::get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];
        $session_id_db = TezNevise_AI_Database::save_session($session_data);
        return ['success' => true, 'session_id' => $session_id, 'session_id_db' => $session_id_db];
    }
}