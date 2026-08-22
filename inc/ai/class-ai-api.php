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
			'args' => [
				'tool_id' => ['required' => true, 'sanitize_callback' => 'sanitize_key'],
				'message' => [
					'required' => true,
					'sanitize_callback' => 'sanitize_textarea_field',
					'validate_callback' => static function ($value) {
						$length = function_exists('mb_strlen') ? mb_strlen((string) $value) : strlen((string) $value);
						return $length > 0 && $length <= 12000;
					},
				],
				'session_id' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
				'agent_id' => ['required' => false, 'sanitize_callback' => 'sanitize_key'],
				'model' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
			],
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
        
        register_rest_route('teznevise-ai/v1', '/chat/history', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_history'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);
    }
    
    public static function check_chat_permission($request) {
        $tool_id = $request->get_param('tool_id');
        $tool = TezNevise_AI_Core::get_tool($tool_id);
        if (!$tool) return new WP_Error('invalid_tool', 'Invalid tool ID', ['status' => 400]);
        
        $user_id = get_current_user_id();
        $is_logged_in = $user_id > 0;
		$today = gmdate('Y-m-d');
		$usage_key = 'usage_' . $today . '_' . self::usage_subject();
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
		$message = isset($params['message']) ? sanitize_textarea_field((string) $params['message']) : '';
		$session_id = isset($params['session_id']) ? $params['session_id'] : '';
        $agent_id = $params['agent_id'] ?? 'general';
		$requested_model = isset($params['model']) ? sanitize_text_field((string) $params['model']) : '';
        $collaboration_mode = $params['collaboration_mode'] ?? 'single';
        $thinking_enabled = $params['thinking_enabled'] ?? true;
        $skill_id = $params['skill_id'] ?? null;
        
        $tool = TezNevise_AI_Core::get_tool($tool_id);
        if (!$tool) {
            $tool = TezNevise_AI_Core::get_tool('general');
        }
        if (!$tool) return new WP_Error('invalid_tool', 'Invalid tool ID', ['status' => 400]);
        
        $user_id = get_current_user_id();
        $is_logged_in = $user_id > 0;
        $today = gmdate('Y-m-d');
		$usage_key = 'usage_' . $today . '_' . self::usage_subject();
        $usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
        $message_count = $usage['message_count'] ?? 0;
        $free = (int) get_option('teznevise_ai_free_tier_limit', $tool['free_tier_limit'] ?? 10);
        $signed = (int) get_option('teznevise_ai_signed_in_limit', $tool['signed_in_limit'] ?? 100);
        $limit = $is_logged_in ? $signed : $free;
        
        if ($message_count >= $limit) {
            return new WP_Error('limit_reached', 'سهمیه پیام امروز تمام شده است', ['status' => 402]);
        }

        if ($is_logged_in) {
            $cost = (float) get_option('teznevise_ai_cost_per_message', $tool['cost_per_message'] ?? 0);
            if ($cost > 0 && function_exists('teznevise_tezcoin_balance') && teznevise_tezcoin_balance($user_id) < $cost) {
                return new WP_Error('no_credits', 'تزکوین کافی نیست', ['status' => 402]);
            }
        }

        $session_token = sanitize_text_field((string) $session_id);
        if ($session_token === '') {
            $session_token = wp_generate_uuid4();
        }
        
        $agent = TezNevise_AI_Database::get_agent($agent_id);
        if (!$agent) $agent = TezNevise_AI_Database::get_agent('general');
		$model = (string) ($agent['model'] ?? 'gpt-4');
		$allowed_models = (array) apply_filters('teznevise_ai_allowed_models', [$model], $tool, $agent);
		if ($requested_model && in_array($requested_model, $allowed_models, true)) {
			$model = $requested_model;
		}
		if (!$is_logged_in) {
			$collaboration_mode = 'single';
		}
		$burst_key = 'tez_ai_' . substr(hash('sha256', self::usage_subject() . '|' . $tool_id), 0, 32);
		$burst = self::increment_burst($burst_key, 5);
		if (is_wp_error($burst)) {
			return $burst;
		}

		$reserved = self::reserve_quota($tool_id, $usage_key, $limit);
		if (is_wp_error($reserved)) {
			return $reserved;
		}
		$usage = $reserved;
        
        $replies = self::run_collaboration($message, $tool, $agent, $model, $collaboration_mode, $thinking_enabled, $skill_id);
        if (is_wp_error($replies)) {
			self::release_quota($tool_id, $usage_key);
            return $replies;
        }
        
        $session_data = [
            'user_id' => $user_id,
            'tool_id' => $tool_id,
            'session_id' => $session_token,
            'agent_id' => $agent_id,
            'model' => $model,
            'collaboration_mode' => in_array($collaboration_mode, ['single','collaborative','separate'], true) ? $collaboration_mode : 'single',
			'ip_address' => null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 255) : '',
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

        foreach ($replies as $rep) {
            TezNevise_AI_Database::save_message([
                'session_id' => $session_id_db,
                'tool_id' => $tool_id,
                'role' => 'assistant',
                'content' => $rep['content'],
                'agent_name' => $rep['agent_name'],
                'model' => $rep['model'] ?? $model,
                'thinking_process' => $rep['thinking_process'] ?? null,
                'token_count' => self::count_tokens($rep['content']),
            ]);
        }
        
        $cost = (float) get_option('teznevise_ai_cost_per_message', $tool['cost_per_message'] ?? 0);
        if ($is_logged_in && $cost > 0 && function_exists('teznevise_tezcoin_credit')) {
            teznevise_tezcoin_credit($user_id, -1 * (int) ceil($cost), 'ai-chat', $session_token);
        } elseif ($is_logged_in && $cost > 0) {
            self::update_user_credits($user_id, -$cost);
        }
        
        $last = $replies[count($replies) - 1];
        return [
            'success' => true,
            'session_id' => $session_token,
            'content' => $last['content'],
            'agent_name' => $last['agent_name'],
            'model' => $model,
            'thinking_process' => $last['thinking_process'] ?? null,
            'replies' => $replies,
            'usage' => ['today' => $usage['message_count']],
        ];
    }
    
    private static function run_collaboration($message, $tool, $primary, $model, $mode, $thinking_enabled, $skill_id) {
        $agents = array($primary);
        if (in_array($mode, array('collaborative', 'separate'), true)) {
            $ids = $tool['recommended_agents'] ?? array();
            foreach ((array) $ids as $id) {
                $row = TezNevise_AI_Database::get_agent($id);
                if ($row && ($row['agent_id'] ?? '') !== ($primary['agent_id'] ?? '')) {
                    $agents[] = $row;
                }
            }
        }
        $replies = array();
        $prior = '';
        foreach ($agents as $i => $ag) {
            $is_last = ($i === count($agents) - 1);
            $prompt = self::build_system_prompt($tool, $ag, $skill_id, $mode);
            if ($mode === 'collaborative' && $prior !== '') {
                $prompt .= "\n\nPrevious agent notes:\n" . $prior;
            }
            if ($mode === 'separate' && $is_last && $prior !== '') {
                $prompt .= "\n\nYou are the reflecting agent. Summarize and reconcile these independent answers:\n" . $prior;
            }
            $payload = $message;
            if ($mode === 'separate' && ! $is_last) {
                $payload = $message;
            }
            $response = self::call_ai_api($payload, $prompt, $ag, $ag['model'] ?? $model, $thinking_enabled);
            if (is_wp_error($response)) {
                return $response;
            }
            $replies[] = array(
                'content' => $response['content'],
                'agent_name' => $ag['name'] ?? 'Assistants',
                'model' => $ag['model'] ?? $model,
				'thinking_process' => null,
            );
            $prior .= "\n- " . ($ag['name'] ?? 'agent') . ': ' . $response['content'];
            if ($mode === 'single') {
                break;
            }
        }
        return $replies;
    }

    public static function get_history($request) {
        if (!is_user_logged_in()) {
            return new WP_Error('forbidden', 'Login required', array('status' => 401));
        }
        global $wpdb;
        $uid = get_current_user_id();
        $prefix = TezNevise_AI_Database::PREFIX;
        $sessions = $wpdb->prefix . $prefix . 'chat_sessions';
        $messages = $wpdb->prefix . $prefix . 'chat_messages';
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT m.role, m.content, m.agent_name, m.thinking_process, m.created_at, s.tool_id, s.session_id
             FROM {$messages} m
             INNER JOIN {$sessions} s ON s.id = m.session_id
             WHERE s.user_id = %d
             ORDER BY m.created_at DESC
             LIMIT 100",
            $uid
        ), ARRAY_A);
        return array('success' => true, 'messages' => $rows);
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
		$prompt_parts[] = "Give a concise, evidence-based answer. Do not reveal private chain-of-thought; provide a short conclusion and useful supporting explanation instead.";
        return implode("\n\n", $prompt_parts);
    }

	/** MySQL GET_LOCK wrapper; fails closed when a lock cannot be acquired. */
	private static function with_named_lock($name, $callback) {
		global $wpdb;
		$lock = 'tez_ai_' . substr(md5((string) $name), 0, 24);
		$got = $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, %d)', $lock, 5));
		if ((string) $got !== '1') {
			return new WP_Error('busy', 'سرویس موقتاً شلوغ است؛ دوباره تلاش کنید', ['status' => 429]);
		}
		try {
			return $callback();
		} finally {
			$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $lock));
		}
	}

	/** Reserve one daily quota unit before the provider call. */
	private static function reserve_quota($tool_id, $usage_key, $limit) {
		return self::with_named_lock($usage_key . '|' . $tool_id, static function () use ($tool_id, $usage_key, $limit) {
			$usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
			if (!is_array($usage)) {
				$usage = [];
			}
			$count = (int) ($usage['message_count'] ?? 0);
			if ($count >= (int) $limit) {
				return new WP_Error('limit_reached', 'سهمیه پیام امروز تمام شده است', ['status' => 402]);
			}
			$usage['message_count'] = $count + 1;
			TezNevise_AI_Database::update_setting($tool_id, $usage_key, $usage);
			return $usage;
		});
	}

	/** Release a reserved quota unit after a provider failure. */
	private static function release_quota($tool_id, $usage_key) {
		self::with_named_lock($usage_key . '|' . $tool_id, static function () use ($tool_id, $usage_key) {
			$usage = TezNevise_AI_Database::get_setting($tool_id, $usage_key, []);
			if (!is_array($usage)) {
				$usage = [];
			}
			$count = (int) ($usage['message_count'] ?? 0);
			$usage['message_count'] = max(0, $count - 1);
			TezNevise_AI_Database::update_setting($tool_id, $usage_key, $usage);
			return true;
		});
	}

	/** Atomic 5/min burst counter. */
	private static function increment_burst($burst_key, $max) {
		return self::with_named_lock('burst|' . $burst_key, static function () use ($burst_key, $max) {
			$burst = (int) get_transient($burst_key);
			if ($burst >= (int) $max) {
				return new WP_Error('rate_limited', 'درخواست‌های پیاپی بیش از حد مجاز است', ['status' => 429]);
			}
			set_transient($burst_key, $burst + 1, MINUTE_IN_SECONDS);
			return $burst + 1;
		});
	}
    
    private static function call_ai_api($message, $system_prompt, $agent, $model, $thinking_enabled) {
		$api_endpoint = esc_url_raw($agent['api_endpoint'] ?? 'https://api.openai.com/v1/chat/completions');
		$parsed = is_string($api_endpoint) ? wp_parse_url($api_endpoint) : false;
		if (!is_array($parsed)) {
			return new WP_Error('invalid_api_endpoint', 'AI provider URL is invalid', ['status' => 400]);
		}
		$scheme = strtolower((string) ($parsed['scheme'] ?? ''));
		$api_host = strtolower((string) ($parsed['host'] ?? ''));
		if ($scheme !== 'https' || $api_host === '' || !empty($parsed['user']) || !empty($parsed['pass'])) {
			return new WP_Error('invalid_api_endpoint', 'AI provider must use HTTPS without embedded credentials', ['status' => 400]);
		}
		$allowed_hosts = (array) apply_filters('teznevise_ai_allowed_api_hosts', ['api.openai.com']);
		if (!in_array($api_host, array_map('strtolower', $allowed_hosts), true)) {
			return new WP_Error('invalid_api_host', 'AI provider host is not allow-listed', ['status' => 400]);
		}
		$api_key = defined('TEZNEVISE_AI_OPENAI_KEY') ? TEZNEVISE_AI_OPENAI_KEY : ($agent['api_key'] ?? get_option('teznevise_ai_openai_key', ''));
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
			'redirection' => 0,
        ];
        
        $response = wp_remote_post($api_endpoint, $args);
        if (is_wp_error($response)) return $response;
		$status = (int) wp_remote_retrieve_response_code($response);
		if ($status < 200 || $status >= 300) {
			return new WP_Error('api_http_error', 'AI provider returned an unsuccessful response', ['status' => 502]);
		}
        
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($response_body['error'])) return new WP_Error('api_error', $response_body['error']['message'] ?? 'API error');
        
        $content = $response_body['choices'][0]['message']['content'] ?? '';
		return ['content' => $content, 'thinking_process' => null];
    }
    
    private static function get_client_ip() {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }

	/** Stable, non-reversible daily quota subject for a user or guest. */
	private static function usage_subject() {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			return 'user_' . $user_id;
		}
		$material = self::get_client_ip() . '|' . (isset($_SERVER['HTTP_USER_AGENT']) ? (string) wp_unslash($_SERVER['HTTP_USER_AGENT']) : '');
		return 'guest_' . substr(hash_hmac('sha256', $material, wp_salt('nonce')), 0, 24);
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
		$today = gmdate('Y-m-d');
		$usage_key = 'usage_' . $today . '_' . self::usage_subject();
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
			'ip_address' => null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];
        $session_id_db = TezNevise_AI_Database::save_session($session_data);
        return ['success' => true, 'session_id' => $session_id, 'session_id_db' => $session_id_db];
    }
}
