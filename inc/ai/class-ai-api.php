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
            'collaboration_mode' => in_array($collaboration_mode, ['single','collaborative','separate'], true) ? $collaboration_mode : 'collaborative',
			'ip_address' => null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 255) : '',
        ];
        
        $session_saved = TezNevise_AI_Database::save_session($session_data);
        if (is_array($session_saved)) {
            $session_token = $session_saved['session_id'] ?: $session_token;
            $session_id_db = (int) $session_saved['id'];
        } else {
            $session_id_db = (int) $session_saved;
        }
        
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
        $replies = array();
        $prior   = '';
        $agents  = array($primary);

        if ($mode === 'research') {
            $researcher = TezNevise_AI_Database::get_agent('you');
            if (!$researcher) {
                foreach ((array) TezNevise_AI_Database::get_all_agents() as $row) {
                    if (($row['role'] ?? '') === 'researcher' || ($row['agent_id'] ?? '') === 'you') {
                        $researcher = $row;
                        break;
                    }
                }
            }
            if ($researcher) {
                $rprompt = trim((string) ($researcher['system_prompt'] ?? '')) ?: 'You are You.com, a research agent. Search and summarize the topic with sources, claims, and counterpoints. Prefer Persian academic sources when the user writes in Persian. Return markdown with a Findings section and a Sources section.';
                $research = self::call_ai_api($message, $rprompt, $researcher, $researcher['model'] ?? $model, $thinking_enabled);
                if (is_wp_error($research)) {
                    return $research;
                }
                $replies[] = array(
                    'content' => $research['content'],
                    'agent_name' => $researcher['name'] ?? 'You',
                    'model' => $researcher['model'] ?? $model,
                    'thinking_process' => $research['thinking_process'] ?? null,
                );
                $prior = $research['content'];
            }
        }

        if (in_array($mode, array('collaborative', 'separate', 'research'), true)) {
            $ids = $tool['recommended_agents'] ?? array();
            foreach ((array) $ids as $id) {
                $row = TezNevise_AI_Database::get_agent($id);
                if ($row && ($row['agent_id'] ?? '') !== ($primary['agent_id'] ?? '') && ($row['role'] ?? '') !== 'researcher') {
                    $agents[] = $row;
                }
            }
        }
        foreach ($agents as $i => $ag) {
            if (($ag['role'] ?? '') === 'researcher' && $mode === 'research') {
                continue;
            }
            $is_last = ($i === count($agents) - 1);
            $prompt = self::build_system_prompt($tool, $ag, $skill_id, $mode);
            if ($prior !== '' && in_array($mode, array('collaborative', 'research'), true)) {
                $prompt .= "\n\nResearch brief from You:\n" . $prior;
            }
            if ($mode === 'separate' && $is_last && $prior !== '') {
                $prompt .= "\n\nYou are the reflecting agent. Summarize and reconcile these independent answers:\n" . $prior;
            }
            $response = self::call_ai_api($message, $prompt, $ag, $ag['model'] ?? $model, $thinking_enabled);
            if (is_wp_error($response)) {
                return $response;
            }
            $replies[] = array(
                'content' => $response['content'],
                'agent_name' => $ag['name'] ?? 'Assistants',
                'model' => $ag['model'] ?? $model,
				'thinking_process' => $response['thinking_process'] ?? null,
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
        $lock = apply_filters( 'teznevise_ai_system_prompt_prefix', '', $agent );
        if ( $lock ) {
            $prompt_parts[] = $lock;
        } elseif (!empty($agent['system_prompt'])) {
            $prompt_parts[] = $agent['system_prompt'];
        }
        if (!empty($agent['description']) && ! $lock) $prompt_parts[] = $agent['description'];
        if (!empty($agent['role'])) $prompt_parts[] = 'Role: ' . $agent['role'];
        if (!empty($tool['context'])) $prompt_parts[] = "Tool Context: " . json_encode($tool['context']);
        if ($skill_id && isset($tool['skills'][$skill_id])) {
            $prompt_parts[] = $tool['skills'][$skill_id]['prompt'];
        }
        $prompt_parts[] = "Collaboration Mode: {$collaboration_mode}";
        $lang = !empty($agent['language']) ? $agent['language'] : 'fa';
        if ($lang === 'fa') {
            $prompt_parts[] = "If the user writes in Persian, always respond in Persian. Otherwise, respond in English.";
        } else {
            $prompt_parts[] = "Respond in language code: {$lang}.";
        }
		$prompt_parts[] = "Give a concise, evidence-based answer. First enclose ALL internal reasoning in <thought>...</thought>, then the public reply outside those tags.";
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
    
    public static function complete($message, $system_prompt, $agent, $model, $thinking_enabled = false) {
        return self::call_ai_api($message, $system_prompt, $agent, $model, $thinking_enabled);
    }

    public static function research($query, $agent = null) {
        if (!$agent && class_exists('TezNevise_AI_Database')) {
            $agent = TezNevise_AI_Database::get_agent('you');
        }
        if (!$agent && class_exists('TezNevise_AI_Database')) {
            foreach ((array) TezNevise_AI_Database::get_all_agents() as $row) {
                if (($row['role'] ?? '') === 'researcher') {
                    $agent = $row;
                    break;
                }
            }
        }
        if (!$agent) {
            return new WP_Error('no_researcher', 'عامل You/پژوهش پیکربندی نشده است', ['status' => 400]);
        }
        $prompt = trim((string) ($agent['system_prompt'] ?? '')) ?: 'You are You.com. Research comprehensively. Return Findings and Sources in Persian if the query is Persian.';
        return self::call_ai_api($query, $prompt, $agent, $agent['model'] ?? '', false);
    }

    private static function you_search($api_key, $query, $endpoint) {
        $base = $endpoint ? $endpoint : 'https://api.ydc-index.io/v1/search';
        $url  = add_query_arg('query', rawurlencode(wp_strip_all_tags((string) $query)), $base);
        $response = wp_remote_get($url, array(
            'headers' => array(
                'X-API-Key' => $api_key,
                'Accept'    => 'application/json',
            ),
            'timeout' => 45,
            'redirection' => 0,
        ));
        if (is_wp_error($response)) {
            return $response;
        }
        $status = (int) wp_remote_retrieve_response_code($response);
        if ($status < 200 || $status >= 300) {
            return new WP_Error('api_http_error', 'You.com returned an unsuccessful response', ['status' => 502]);
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($body)) {
            return new WP_Error('api_error', 'پاسخ You.com نامعتبر بود', ['status' => 502]);
        }
        $hits = $body['hits'] ?? $body['results'] ?? $body['news'] ?? array();
        $lines = array("## یافته‌های You");
        $n = 0;
        foreach ((array) $hits as $hit) {
            if ($n >= 8) {
                break;
            }
            $title = $hit['title'] ?? $hit['name'] ?? '';
            $snip  = $hit['snippet'] ?? $hit['description'] ?? $hit['summary'] ?? '';
            $link  = $hit['url'] ?? $hit['link'] ?? '';
            if (!$title && !$snip) {
                continue;
            }
            ++$n;
            $lines[] = ($n) . '. **' . $title . '** — ' . wp_strip_all_tags((string) $snip) . ($link ? ' (' . $link . ')' : '');
        }
        if ($n === 0 && !empty($body['answer'])) {
            $lines[] = (string) $body['answer'];
        }
        if ($n === 0 && count($lines) === 1) {
            $lines[] = wp_json_encode($body, JSON_UNESCAPED_UNICODE);
        }
        return array('content' => implode("\n", $lines), 'thinking_process' => null);
    }

    private static function call_ai_api($message, $system_prompt, $agent, $model, $thinking_enabled) {
        $provider = self::detect_provider($agent);
        $api_endpoint = self::endpoint_for($agent, $provider, $model);
		$parsed = is_string($api_endpoint) ? wp_parse_url($api_endpoint) : false;
		if (!is_array($parsed)) {
			return new WP_Error('invalid_api_endpoint', 'AI provider URL is invalid', ['status' => 400]);
		}
		$scheme = strtolower((string) ($parsed['scheme'] ?? ''));
		$api_host = strtolower((string) ($parsed['host'] ?? ''));
		if ($scheme !== 'https' || $api_host === '' || !empty($parsed['user']) || !empty($parsed['pass'])) {
			return new WP_Error('invalid_api_endpoint', 'AI provider must use HTTPS without embedded credentials', ['status' => 400]);
		}
		$allowed_hosts = (array) apply_filters('teznevise_ai_allowed_api_hosts', self::allowed_hosts());
		if (!in_array($api_host, array_map('strtolower', $allowed_hosts), true)) {
			return new WP_Error('invalid_api_host', 'AI provider host is not allow-listed', ['status' => 400]);
		}
		$api_key = self::key_for($agent, $provider);
        if (empty($api_key)) return new WP_Error('no_api_key', 'کلید API این عامل تنظیم نشده است', ['status' => 400]);

        if ($thinking_enabled) {
            $system_prompt .= "\n\nWhen useful, wrap a short working outline in <think>...</think> before the final answer. Keep the visible answer concise and in the user's language.";
        }

        if ($provider === 'you') {
            return self::you_search($api_key, $message, $api_endpoint);
        }

        $built = self::build_provider_request($provider, $api_endpoint, $api_key, $model, $system_prompt, $message, $agent);
        if (is_wp_error($built)) {
            return $built;
        }

        $response = wp_remote_post($built['url'], $built['args']);
        if (is_wp_error($response)) return $response;
		$status = (int) wp_remote_retrieve_response_code($response);
		if ($status < 200 || $status >= 300) {
			return new WP_Error('api_http_error', 'AI provider returned an unsuccessful response', ['status' => 502]);
		}

        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($response_body)) {
            return new WP_Error('api_error', 'پاسخ ارائه‌دهنده نامعتبر بود', ['status' => 502]);
        }
        if (isset($response_body['error'])) {
            $err = $response_body['error'];
            $msg = is_array($err) ? ($err['message'] ?? 'API error') : (string) $err;
            return new WP_Error('api_error', $msg);
        }

        $content = self::extract_content($provider, $response_body);
        $thinking = '';
        if ($thinking_enabled && preg_match('/<think>(.*?)<\/think>/is', $content, $m)) {
            $thinking = trim($m[1]);
            $content = trim(preg_replace('/<think>.*?<\/think>/is', '', $content));
        }
		return ['content' => $content, 'thinking_process' => $thinking !== '' ? $thinking : null];
    }

    public static function allowed_hosts() {
        return array(
            'api.openai.com',
            'generativelanguage.googleapis.com',
            'openrouter.ai',
            'api.groq.com',
            'api.x.ai',
            'api.mistral.ai',
            'api.together.xyz',
            'api.anthropic.com',
            'api.deepseek.com',
            'api.ydc-index.io',
            'api.you.com',
            'api.tavily.com',
        );
    }

    public static function providers() {
        return array(
            'openai'     => array('label' => 'OpenAI', 'option' => 'teznevise_ai_openai_key', 'host' => 'api.openai.com', 'endpoint' => 'https://api.openai.com/v1/chat/completions'),
            'gemini'     => array('label' => 'Google Gemini', 'option' => 'teznevise_ai_gemini_key', 'host' => 'generativelanguage.googleapis.com', 'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent'),
            'openrouter' => array('label' => 'OpenRouter', 'option' => 'teznevise_ai_openrouter_key', 'host' => 'openrouter.ai', 'endpoint' => 'https://openrouter.ai/api/v1/chat/completions'),
            'groq'       => array('label' => 'Groq', 'option' => 'teznevise_ai_groq_key', 'host' => 'api.groq.com', 'endpoint' => 'https://api.groq.com/openai/v1/chat/completions'),
            'xai'        => array('label' => 'xAI', 'option' => 'teznevise_ai_xai_key', 'host' => 'api.x.ai', 'endpoint' => 'https://api.x.ai/v1/chat/completions'),
            'anthropic'  => array('label' => 'Anthropic', 'option' => 'teznevise_ai_anthropic_key', 'host' => 'api.anthropic.com', 'endpoint' => 'https://api.anthropic.com/v1/messages'),
            'mistral'    => array('label' => 'Mistral', 'option' => 'teznevise_ai_mistral_key', 'host' => 'api.mistral.ai', 'endpoint' => 'https://api.mistral.ai/v1/chat/completions'),
            'together'   => array('label' => 'Together', 'option' => 'teznevise_ai_together_key', 'host' => 'api.together.xyz', 'endpoint' => 'https://api.together.xyz/v1/chat/completions'),
            'deepseek'   => array('label' => 'DeepSeek', 'option' => 'teznevise_ai_deepseek_key', 'host' => 'api.deepseek.com', 'endpoint' => 'https://api.deepseek.com/v1/chat/completions'),
            'you'        => array('label' => 'You.com Research', 'option' => 'teznevise_ai_you_key', 'host' => 'api.ydc-index.io', 'endpoint' => 'https://api.ydc-index.io/v1/search'),
            'tavily'     => array('label' => 'Tavily Research', 'option' => 'teznevise_ai_tavily_key', 'host' => 'api.tavily.com', 'endpoint' => 'https://api.tavily.com/search'),
        );
    }

    private static function detect_provider($agent) {
        $explicit = sanitize_key($agent['provider'] ?? '');
        $catalog  = self::providers();
        if ($explicit && isset($catalog[$explicit])) {
            return $explicit;
        }
        $host = strtolower((string) (wp_parse_url((string) ($agent['api_endpoint'] ?? ''), PHP_URL_HOST) ?: ''));
        foreach ($catalog as $id => $row) {
            if ($host && false !== strpos($host, str_replace('api.', '', $row['host']))) {
                return $id;
            }
            if ($host === $row['host']) {
                return $id;
            }
        }
        return 'openai';
    }

    private static function endpoint_for($agent, $provider, $model) {
        $custom = esc_url_raw($agent['api_endpoint'] ?? '');
        if ($custom) {
            return $custom;
        }
        $catalog = self::providers();
        $url = $catalog[$provider]['endpoint'] ?? $catalog['openai']['endpoint'];
        return str_replace('{model}', rawurlencode((string) $model), $url);
    }

    private static function key_for($agent, $provider) {
        if (!empty($agent['api_key'])) {
            $own = (string) $agent['api_key'];
            return class_exists('Teznevise_Key_Vault') ? Teznevise_Key_Vault::decrypt($own) : $own;
        }
        if (class_exists('Teznevise_Key_Vault')) {
            $from_vault = Teznevise_Key_Vault::get_provider_key($provider);
            if ($from_vault !== '') {
                return $from_vault;
            }
        }
        $catalog = self::providers();
        $option = $catalog[$provider]['option'] ?? 'teznevise_ai_openai_key';
        if ($provider === 'openai' && defined('TEZNEVISE_AI_OPENAI_KEY') && TEZNEVISE_AI_OPENAI_KEY) {
            return TEZNEVISE_AI_OPENAI_KEY;
        }
        $raw = (string) get_option($option, '');
        return class_exists('Teznevise_Key_Vault') ? Teznevise_Key_Vault::decrypt($raw) : $raw;
    }

    private static function build_provider_request($provider, $url, $api_key, $model, $system_prompt, $message, $agent = null) {
        $headers = array('Content-Type' => 'application/json');
        $temp = 0.7;
        $tokens = 1500;
        if (is_array($agent)) {
            if (isset($agent['temperature'])) {
                $temp = max(0, min(2, (float) $agent['temperature']));
            }
            if (!empty($agent['max_tokens'])) {
                $tokens = max(64, min(8000, (int) $agent['max_tokens']));
            }
        }
        if ($provider === 'gemini') {
            $headers['x-goog-api-key'] = $api_key;
            $body = array(
                'systemInstruction' => array('parts' => array(array('text' => $system_prompt))),
                'contents' => array(array('role' => 'user', 'parts' => array(array('text' => $message)))),
                'generationConfig' => array('temperature' => $temp, 'maxOutputTokens' => $tokens),
            );
        } elseif ($provider === 'anthropic') {
            $headers['x-api-key'] = $api_key;
            $headers['anthropic-version'] = '2023-06-01';
            $body = array(
                'model' => $model,
                'max_tokens' => $tokens,
                'system' => $system_prompt,
                'messages' => array(array('role' => 'user', 'content' => $message)),
            );
        } else {
            $headers['Authorization'] = 'Bearer ' . $api_key;
            if ($provider === 'openrouter') {
                $headers['HTTP-Referer'] = home_url('/');
                $headers['X-Title'] = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
            }
            $body = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'system', 'content' => $system_prompt),
                    array('role' => 'user', 'content' => $message),
                ),
                'temperature' => $temp,
                'max_tokens' => $tokens,
            );
        }
        return array(
            'url' => $url,
            'args' => array(
                'headers' => $headers,
                'body' => wp_json_encode($body),
                'timeout' => 60,
                'redirection' => 0,
            ),
        );
    }

    private static function extract_content($provider, $body) {
        if ($provider === 'gemini') {
            return (string) ($body['candidates'][0]['content']['parts'][0]['text'] ?? '');
        }
        if ($provider === 'anthropic') {
            $parts = $body['content'] ?? array();
            $text = '';
            foreach ((array) $parts as $part) {
                if (($part['type'] ?? '') === 'text') {
                    $text .= (string) ($part['text'] ?? '');
                }
            }
            return $text;
        }
        return (string) ($body['choices'][0]['message']['content'] ?? '');
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
            $formatted[] = ['id' => $agent['agent_id'], 'name' => $agent['name'], 'description' => $agent['description'], 'model' => $agent['model'], 'provider' => $agent['provider'] ?? 'openai', 'color' => $agent['color'], 'icon' => $agent['icon'], 'thinking_enabled' => (bool) $agent['thinking_enabled']];
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
        $session_saved = TezNevise_AI_Database::save_session($session_data);
        if (is_array($session_saved)) {
            $session_id = $session_saved['session_id'] ?: $session_id;
            $session_id_db = (int) $session_saved['id'];
        } else {
            $session_id_db = (int) $session_saved;
        }
        return ['success' => true, 'session_id' => $session_id, 'session_id_db' => $session_id_db];
    }
}
