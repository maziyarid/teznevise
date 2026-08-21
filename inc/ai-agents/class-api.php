<?php
/**
 * REST API Endpoints for TezNevise AI
 */

class TezNevise_AI_API {
    
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }
    
    public static function register_routes() {
        // Chat endpoint
        register_rest_route('teznevise-ai/v1', '/chat', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_chat'],
            'permission_callback' => '__return_true',
            'args' => [
                'message' => ['required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
                'session_id' => ['required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
                'tool_id' => ['required' => false, 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field'],
                'agent_id' => ['required' => false, 'type' => 'string', 'default' => 'general', 'sanitize_callback' => 'sanitize_text_field'],
                'model' => ['required' => false, 'type' => 'string', 'default' => 'gpt-4', 'sanitize_callback' => 'sanitize_text_field'],
                'collaboration_mode' => ['required' => false, 'type' => 'string', 'default' => 'single', 'sanitize_callback' => 'sanitize_text_field'],
                'thinking_enabled' => ['required' => false, 'type' => 'boolean', 'default' => true],
            ],
        ]);
        
        // Chat history
        register_rest_route('teznevise-ai/v1', '/chat/history', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_chat_history'],
            'permission_callback' => '__return_true',
            'args' => [
                'session_id' => ['required' => false, 'type' => 'string'],
                'tool_id' => ['required' => false, 'type' => 'string'],
                'limit' => ['required' => false, 'type' => 'integer', 'default' => 50],
            ],
        ]);
        
        // Get agents
        register_rest_route('teznevise-ai/v1', '/agents', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_agents'],
            'permission_callback' => '__return_true',
        ]);
        
        // Get settings
        register_rest_route('teznevise-ai/v1', '/settings', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_settings'],
            'permission_callback' => '__return_true',
        ]);
        
        // Get usage
        register_rest_route('teznevise-ai/v1', '/usage', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_usage'],
            'permission_callback' => '__return_true',
        ]);
        
        // Purchase credits (admin only)
        register_rest_route('teznevise-ai/v1', '/credits/purchase', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'purchase_credits'],
            'permission_callback' => [__CLASS__, 'check_admin'],
            'args' => [
                'user_id' => ['required' => true, 'type' => 'integer'],
                'amount' => ['required' => true, 'type' => 'number'],
            ],
        ]);
    }
    
    public static function check_admin($request) {
        return current_user_can('manage_options');
    }
    
    public static function handle_chat($request) {
        $params = $request->get_params();
        
        $message = sanitize_text_field($params['message']);
        $session_id = sanitize_text_field($params['session_id'] ?? '');
        $tool_id = sanitize_text_field($params['tool_id'] ?? '');
        $agent_id = sanitize_text_field($params['agent_id'] ?? 'general');
        $model = sanitize_text_field($params['model'] ?? 'gpt-4');
        $collaboration_mode = sanitize_text_field($params['collaboration_mode'] ?? 'single');
        $thinking_enabled = isset($params['thinking_enabled']) ? (bool)$params['thinking_enabled'] : true;
        
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $ip_address = self::get_client_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Check usage limits
        $usage_check = self::check_usage_limit($user_id);
        if (!$usage_check['allowed']) {
            return new WP_Error('usage_limit_exceeded', $usage_check['message'], ['status' => 429]);
        }
        
        // Get agent
        $agent = TezNevise_AI_Database::get_agent($agent_id);
        if (!$agent) {
            $agent = TezNevise_AI_Database::get_agent('general');
        }
        
        // Generate session ID
        if (empty($session_id)) {
            $session_id = uniqid('session_', true);
        }
        
        global $wpdb;
        $sessions_table = $wpdb->prefix . 'teznevise_ai_chat_sessions';
        $messages_table = $wpdb->prefix . 'teznevise_ai_chat_messages';
        
        // Get or create session
        $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM $sessions_table WHERE session_id = %s", $session_id));
        
        $session_data = [
            'user_id' => $user_id,
            'session_id' => $session_id,
            'tool_id' => $tool_id,
            'agent_id' => $agent_id,
            'model' => $model,
            'collaboration_mode' => $collaboration_mode,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
        ];
        
        if ($existing) {
            $wpdb->update($sessions_table, $session_data, ['id' => $existing->id]);
            $session_id_num = $existing->id;
        } else {
            $wpdb->insert($sessions_table, $session_data);
            $session_id_num = $wpdb->insert_id;
        }
        
        // Save user message
        $wpdb->insert($messages_table, [
            'session_id' => $session_id_num,
            'role' => 'user',
            'content' => $message,
            'agent_name' => 'User',
            'model' => $model,
            'token_count' => self::count_tokens($message),
        ]);
        
        // Call AI API
        $ai_response = self::call_ai_api($agent, $message, $thinking_enabled, $collaboration_mode);
        
        if (is_wp_error($ai_response)) {
            return $ai_response;
        }
        
        // Save assistant message
        $wpdb->insert($messages_table, [
            'session_id' => $session_id_num,
            'role' => 'assistant',
            'content' => $ai_response['content'],
            'agent_name' => $agent['name'],
            'model' => $model,
            'thinking_process' => $ai_response['thinking_process'] ?? null,
            'token_count' => self::count_tokens($ai_response['content']),
        ]);
        
        // Update usage
        self::update_usage($user_id, $session_id_num);
        
        return [
            'success' => true,
            'content' => $ai_response['content'],
            'agent_name' => $agent['name'],
            'model' => $model,
            'thinking_process' => $ai_response['thinking_process'] ?? null,
            'session_id' => $session_id,
            'message_id' => $wpdb->insert_id,
            'usage' => self::get_user_usage($user_id),
        ];
    }
    
    private static function call_ai_api($agent, $message, $thinking_enabled, $collaboration_mode) {
        // Detect API provider
        $api_url = $agent['api_endpoint'];
        $api_key = $agent['api_key'];
        $model = $agent['model'];
        
        if (strpos($api_url, 'openai.com') !== false) {
            return self::call_openai($api_url, $api_key, $model, $message, $thinking_enabled);
        } elseif (strpos($api_url, 'anthropic.com') !== false) {
            return self::call_anthropic($api_url, $api_key, $model, $message, $thinking_enabled);
        } elseif (strpos($api_url, 'google.com') !== false) {
            return self::call_google($api_url, $api_key, $model, $message, $thinking_enabled);
        }
        
        // Default to OpenAI or demo mode
        if (empty($api_key)) {
            return [
                'content' => self::generate_demo_response($message),
                'thinking_process' => $thinking_enabled ? self::generate_thinking_process($message) : null,
            ];
        }
        
        return self::call_openai($api_url, $api_key, $model, $message, $thinking_enabled);
    }
    
    private static function call_openai($api_url, $api_key, $model, $message, $thinking_enabled) {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful AI assistant for statistical calculations. Respond in Persian or English based on the user message.'],
            ['role' => 'user', 'content' => $message],
        ];
        
        $body = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1500,
        ];
        
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
            'timeout' => 60,
        ];
        
        $response = wp_remote_post($api_url, $args);
        
        if (is_wp_error($response)) {
            return ['content' => 'Error: ' . $response->get_error_message(), 'thinking_process' => null];
        }
        
        $json = json_decode($response['body'], true);
        
        if (isset($json['choices'][0]['message']['content'])) {
            return [
                'content' => $json['choices'][0]['message']['content'],
                'thinking_process' => $thinking_enabled ? 'AI processed your request...' : null,
            ];
        }
        
        return ['content' => 'Error: Invalid response from API', 'thinking_process' => null];
    }
    
    private static function generate_demo_response($message) {
        $lower = strtolower($message);
        
        if (strpos($lower, 'mann-whitney') !== false || strpos($lower, 'statistics') !== false || strpos($lower, 'u test') !== false) {
            return 'I can help you with the Mann-Whitney U test! This non-parametric test compares two independent samples to assess whether their distributions differ. To use the calculator: 1) Enter your data for Group 1 and Group 2, 2) Click Calculate, 3) View the U statistic, p-value, and effect size. Would you like me to explain how to interpret the results?';
        }
        
        if (strpos($lower, 't-test') !== false || strpos($lower, 'student') !== false) {
            return 'The T-test is used to compare the means of two groups. To use the calculator: 1) Enter your sample data, 2) Select the type of t-test (independent or paired), 3) Click Calculate. The results will show you the t-statistic, p-value, and confidence intervals. Would you like help interpreting your results?';
        }
        
        if (strpos($lower, 'correlation') !== false || strpos($lower, 'pearson') !== false || strpos($lower, 'spearman') !== false) {
            return 'Correlation measures the strength and direction of the relationship between two variables. Pearson correlation is for linear relationships with normally distributed data, while Spearman correlation is for monotonic relationships or non-normal data. Would you like me to explain the difference or help you choose the right test?';
        }
        
        if (strpos($lower, 'regression') !== false) {
            return 'Regression analysis helps you understand how the typical value of the dependent variable changes when any one of the independent variables is varied. To use the calculator: 1) Enter your independent (X) and dependent (Y) variables, 2) Click Calculate, 3) View the regression equation and R-squared value. Would you like help interpreting the coefficients?';
        }
        
        if (strpos($lower, 'help') !== false || strpos($lower, '؟') !== false || strpos($lower, '?') !== false) {
            return 'How can I assist you with this calculation tool? I can: explain how to use the tool, interpret the results, help you understand the statistical concepts, or guide you through entering your data. What would you like help with?';
        }
        
        if (strpos($lower, 'hi') !== false || strpos($lower, 'hello') !== false || strpos($lower, 'سلام') !== false) {
            return 'Hello! I am your AI assistant for statistical calculations. I can help you understand and use all the calculation tools on this site. If you have any questions about statistics, calculations, or how to use a specific tool, just ask!';
        }
        
        return 'I understand you are working with this calculation tool. I am here to help! Please tell me more about what you need assistance with, and I will do my best to guide you.';
    }
    
    private static function generate_thinking_process($message) {
        return "Analyzing your request:
- Understanding the context of your question
- Reviewing relevant statistical concepts
- Formulating a clear and helpful response
- Ensuring accuracy and clarity";
    }
    
    private static function get_client_ip() {
        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
    
    private static function count_tokens($text) {
        return str_word_count(strip_tags($text));
    }
    
    private static function check_usage_limit($user_id) {
        $free_limit = (int) TezNevise_AI_Database::get_setting('free_tier_limit', 10);
        $signed_limit = (int) TezNevise_AI_Database::get_setting('signed_in_limit', 100);
        
        if ($user_id > 0) {
            $usage = self::get_user_usage($user_id);
            $limit = $signed_limit;
        } else {
            $ip = self::get_client_ip();
            $usage = self::get_ip_usage($ip);
            $limit = $free_limit;
        }
        
        if ($usage >= $limit) {
            return [
                'allowed' => false,
                'message' => $user_id > 0 
                    ? sprintf(__('You have reached your daily limit of %d messages. Please purchase more credits.', 'teznevise'), $limit)
                    : sprintf(__('Free tier limit reached (%d messages). Please sign in or purchase credits.', 'teznevise'), $limit),
            ];
        }
        
        return ['allowed' => true, 'message' => ''];
    }
    
    private static function get_user_usage($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_usage_tracking';
        $today = date('Y-m-d');
        return (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(message_count) FROM $table WHERE user_id = %d AND date = %s", $user_id, $today));
    }
    
    private static function get_ip_usage($ip) {
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_chat_sessions';
        $today = date('Y-m-d');
        return (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ip_address = %s AND DATE(created_at) = %s", $ip, $today));
    }
    
    private static function update_usage($user_id, $session_id_num) {
        global $wpdb;
        
        $usage_table = $wpdb->prefix . 'teznevise_ai_usage_tracking';
        $messages_table = $wpdb->prefix . 'teznevise_ai_chat_messages';
        
        $message_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $messages_table WHERE session_id = %d", $session_id_num));
        $token_count = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(token_count) FROM $messages_table WHERE session_id = %d", $session_id_num));
        $cost_per_message = (float) TezNevise_AI_Database::get_setting('cost_per_message', 0.01);
        $cost = $message_count * $cost_per_message;
        
        $today = date('Y-m-d');
        $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM $usage_table WHERE user_id = %d AND date = %s", $user_id, $today));
        
        if ($existing) {
            $wpdb->query($wpdb->prepare(
                "UPDATE $usage_table SET message_count = message_count + %d, token_count = token_count + %d, cost = cost + %f WHERE id = %d",
                $message_count, $token_count, $cost, $existing->id
            ));
        } else {
            $wpdb->insert($usage_table, [
                'user_id' => $user_id,
                'date' => $today,
                'message_count' => $message_count,
                'token_count' => $token_count,
                'cost' => $cost,
            ]);
        }
        
        // Update credits
        if ($user_id > 0) {
            $credits_table = $wpdb->prefix . 'teznevise_ai_user_credits';
            $wpdb->query($wpdb->prepare(
                "UPDATE $credits_table SET credit_balance = credit_balance - %f, total_used = total_used + %f WHERE user_id = %d",
                $cost, $cost, $user_id
            ));
        }
    }
    
    public static function get_chat_history($request) {
        $params = $request->get_params();
        $session_id = sanitize_text_field($params['session_id'] ?? '');
        $tool_id = sanitize_text_field($params['tool_id'] ?? '');
        $limit = intval($params['limit'] ?? 50);
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        
        global $wpdb;
        $sessions_table = $wpdb->prefix . 'teznevise_ai_chat_sessions';
        $messages_table = $wpdb->prefix . 'teznevise_ai_chat_messages';
        
        if (!empty($session_id)) {
            $session = $wpdb->get_row($wpdb->prepare("SELECT id FROM $sessions_table WHERE session_id = %s AND (user_id = %d OR user_id = 0)", $session_id, $user_id));
            if (!$session) {
                return new WP_Error('not_found', 'Session not found', ['status' => 404]);
            }
            $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $messages_table WHERE session_id = %d ORDER BY created_at ASC LIMIT %d", $session->id, $limit));
        } else {
            $messages = [];
        }
        
        return array_map(function($m) {
            return [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'agent_name' => $m->agent_name,
                'model' => $m->model,
                'thinking_process' => $m->thinking_process,
                'created_at' => $m->created_at,
            ];
        }, $messages);
    }
    
    public static function get_agents($request) {
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
    
    public static function get_settings($request) {
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
    
    public static function get_usage($request) {
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_usage_tracking';
        
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $month_start = date('Y-m-01');
        
        $today_usage = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(message_count) FROM $table WHERE user_id = %d AND date = %s", $user_id, $today));
        $week_usage = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(message_count) FROM $table WHERE user_id = %d AND date >= %s", $user_id, $week_start));
        $month_usage = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(message_count) FROM $table WHERE user_id = %d AND date >= %s", $user_id, $month_start));
        $total_usage = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(message_count) FROM $table WHERE user_id = %d", $user_id));
        
        $credits_table = $wpdb->prefix . 'teznevise_ai_user_credits';
        $credits = (float) $wpdb->get_var($wpdb->prepare("SELECT credit_balance FROM $credits_table WHERE user_id = %d", $user_id));
        
        return [
            'today' => $today_usage,
            'this_week' => $week_usage,
            'this_month' => $month_usage,
            'total' => $total_usage,
            'credits' => $credits,
        ];
    }
    
    public static function purchase_credits($request) {
        $params = $request->get_params();
        $user_id = intval($params['user_id']);
        $amount = floatval($params['amount']);
        
        if ($amount <= 0) {
            return new WP_Error('invalid_amount', 'Amount must be positive', ['status' => 400]);
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'teznevise_ai_user_credits';
        
        $current = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
        
        if ($current) {
            $wpdb->update($table, [
                'credit_balance' => $current->credit_balance + $amount,
                'total_earned' => $current->total_earned + $amount,
                'last_credit_date' => current_time('mysql'),
            ], ['user_id' => $user_id]);
        } else {
            $wpdb->insert($table, [
                'user_id' => $user_id,
                'credit_balance' => $amount,
                'total_earned' => $amount,
                'total_used' => 0,
                'last_credit_date' => current_time('mysql'),
            ]);
        }
        
        return [
            'success' => true,
            'user_id' => $user_id,
            'amount' => $amount,
            'new_balance' => $current ? $current->credit_balance + $amount : $amount,
        ];
    }
}
