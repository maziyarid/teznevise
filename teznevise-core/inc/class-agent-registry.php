<?php
/**
 * Existing TezNevise agents plus alias / displayed_model_name / avatar overlays.
 *
 * Never deletes or replaces configured agents. Extra identity fields live in
 * `teznevise_core_agent_profiles` so the original agent table stays intact.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Agent_Registry {

	public static function profiles() {
		$stored = get_option( 'teznevise_core_agent_profiles', array() );
		return is_array( $stored ) ? $stored : array();
	}

	public static function save_profile( $agent_id, $profile ) {
		$agent_id = sanitize_key( $agent_id );
		if ( '' === $agent_id ) {
			return false;
		}
		$all  = self::profiles();
		$prev = isset( $all[ $agent_id ] ) && is_array( $all[ $agent_id ] ) ? $all[ $agent_id ] : array();
		$all[ $agent_id ] = array(
			'alias'                => array_key_exists( 'alias', $profile ) ? sanitize_text_field( $profile['alias'] ) : sanitize_text_field( $prev['alias'] ?? '' ),
			'displayed_model_name' => array_key_exists( 'displayed_model_name', $profile ) ? sanitize_text_field( $profile['displayed_model_name'] ) : sanitize_text_field( $prev['displayed_model_name'] ?? '' ),
			'avatar'               => array_key_exists( 'avatar', $profile ) ? esc_url_raw( $profile['avatar'] ) : esc_url_raw( $prev['avatar'] ?? '' ),
			'pre_computed'         => array_key_exists( 'pre_computed', $profile ) ? sanitize_textarea_field( $profile['pre_computed'] ) : sanitize_textarea_field( $prev['pre_computed'] ?? '' ),
		);
		return update_option( 'teznevise_core_agent_profiles', $all, false );
	}

	public static function all( $include_inactive = false ) {
		$agents = array();
		if ( class_exists( 'TezNevise_AI_Database' ) ) {
			$rows = $include_inactive && method_exists( 'TezNevise_AI_Database', 'get_all_agents_admin' )
				? TezNevise_AI_Database::get_all_agents_admin()
				: TezNevise_AI_Database::get_all_agents();
			foreach ( (array) $rows as $row ) {
				$agents[] = self::hydrate( (array) $row );
			}
		}
		return $agents;
	}

	public static function named() {
		$out = array();
		foreach ( self::all( true ) as $row ) {
			$id = sanitize_key( $row['agent_id'] ?? '' );
			if ( $id && in_array( $id, function_exists( 'teznevise_core_named_ids' ) ? teznevise_core_named_ids() : array(), true ) ) {
				$out[] = $row;
			}
		}
		return $out;
	}

	public static function get( $agent_id ) {
		$agent_id = sanitize_key( $agent_id );
		if ( class_exists( 'TezNevise_AI_Database' ) ) {
			$row = TezNevise_AI_Database::get_agent( $agent_id );
			if ( $row ) {
				return self::hydrate( (array) $row );
			}
			if ( method_exists( 'TezNevise_AI_Database', 'get_all_agents_admin' ) ) {
				foreach ( (array) TezNevise_AI_Database::get_all_agents_admin() as $cand ) {
					if ( sanitize_key( $cand['agent_id'] ?? '' ) === $agent_id ) {
						return self::hydrate( (array) $cand );
					}
				}
			}
		}
		return self::roster_fallback( $agent_id );
	}

	public static function roster_fallback( $agent_id ) {
		$agent_id = sanitize_key( $agent_id );
		if ( ! function_exists( 'teznevise_core_agent_roster' ) ) {
			return null;
		}
		$roster = teznevise_core_agent_roster();
		if ( empty( $roster[ $agent_id ] ) ) {
			return null;
		}
		$row    = $roster[ $agent_id ];
		$models = function_exists( 'teznevise_core_agent_models' ) ? teznevise_core_agent_models( $agent_id ) : array();
		return self::hydrate(
			array(
				'agent_id'      => $agent_id,
				'name'          => $row['name'],
				'description'   => $row['description'],
				'provider'      => 'openrouter',
				'api_endpoint'  => 'https://openrouter.ai/api/v1/chat/completions',
				'model'         => $models['primary'] ?? '',
				'color'         => $row['color'],
				'icon'          => $row['icon'],
				'role'          => $row['role'],
				'system_prompt' => $row['system_prompt'],
				'is_active'     => 1,
				'thinking_enabled' => 1,
			)
		);
	}

	public static function hydrate( $row ) {
		$id       = sanitize_key( $row['agent_id'] ?? '' );
		$profiles = self::profiles();
		$extra    = isset( $profiles[ $id ] ) && is_array( $profiles[ $id ] ) ? $profiles[ $id ] : array();
		$roster   = function_exists( 'teznevise_core_agent_roster' ) ? teznevise_core_agent_roster() : array();
		$canon    = isset( $roster[ $id ] ) && is_array( $roster[ $id ] ) ? $roster[ $id ] : array();

		$alias = trim( (string) ( $extra['alias'] ?? '' ) );
		if ( '' === $alias ) {
			$alias = (string) ( $canon['name'] ?? $row['name'] ?? $id );
		}
		$display = trim( (string) ( $extra['displayed_model_name'] ?? '' ) );
		if ( '' === $display ) {
			$display = (string) ( $canon['displayed_model_name'] ?? $alias );
		}
		$avatar = trim( (string) ( $extra['avatar'] ?? '' ) );
		if ( '' === $avatar && $id && function_exists( 'teznevise_core_agent_logo_url' ) && $canon ) {
			$avatar = teznevise_core_agent_logo_url( $id );
		}
		$row['alias']                = $alias;
		$row['displayed_model_name'] = $display;
		$row['avatar']               = $avatar;
		$row['pre_computed']         = (string) ( $extra['pre_computed'] ?? '' );
		$row['alt']                  = (string) ( $canon['alt'] ?? $alias );
		$row['logo_title']           = (string) ( $canon['title'] ?? $alias );
		if ( $canon && function_exists( 'teznevise_core_agent_models' ) ) {
			$models                  = teznevise_core_agent_models( $id );
			$row['primary_model']    = $models['primary'];
			$row['fallback_model']   = $models['fallback'];
			$row['primary_slot']     = $models['primary_slot'];
			$row['fallback_slot']    = $models['fallback_slot'];
			if ( empty( $row['model'] ) || false !== strpos( (string) $row['model'], 'gpt-4' ) ) {
				$row['model']        = $models['primary'];
				$row['provider']     = 'openrouter';
				$row['api_endpoint'] = 'https://openrouter.ai/api/v1/chat/completions';
			}
		}
		return $row;
	}

	public static function skill_md( $agent_id, $post_id = 0 ) {
		$agent_id = sanitize_key( $agent_id );
		$chunks   = array();
		$file     = '';
		if ( defined( 'TEZNEVISE_CORE_DIR' ) ) {
			$file = TEZNEVISE_CORE_DIR . '/skills/' . $agent_id . '.md';
		}
		if ( $file && is_readable( $file ) ) {
			$raw = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( is_string( $raw ) && strlen( $raw ) < 12000 ) {
				$chunks[] = trim( $raw );
			}
		}
		$post_id = (int) $post_id;
		if ( $post_id > 0 ) {
			$overlay = (string) get_post_meta( $post_id, '_teznevise_skill_md', true );
			if ( '' !== trim( $overlay ) ) {
				$chunks[] = trim( $overlay );
			}
		}
		return implode( "\n\n", $chunks );
	}

	public static function first_responder( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text ) : strtolower( $text );
		if ( function_exists( 'teznevise_core_agent_roster' ) ) {
			foreach ( teznevise_core_agent_roster() as $id => $row ) {
				if ( 'teznevise' === $id ) {
					continue;
				}
				foreach ( (array) ( $row['topics'] ?? array() ) as $needle ) {
					$needle = trim( (string) $needle );
					if ( '' === $needle || 'default' === $needle ) {
						continue;
					}
					if ( false !== mb_stripos( $text, $needle ) ) {
						return $id;
					}
				}
			}
		}
		return 'teznevise';
	}

	public static function peer_reviewer( $first_id ) {
		$first_id = sanitize_key( $first_id );
		$seq      = function_exists( 'teznevise_core_debate_sequence' ) ? teznevise_core_debate_sequence() : array();
		if ( ! $seq ) {
			return 'ada';
		}
		$i = array_search( $first_id, $seq, true );
		if ( false === $i ) {
			return $seq[0];
		}
		return $seq[ ( $i + 1 ) % count( $seq ) ];
	}

	public static function identity_lock( $agent, $skill_md = '', $references = array(), $pre_computed = '' ) {
		$name    = (string) ( $agent['alias'] ?? $agent['name'] ?? 'پژوهشگر تزنویسه' );
		$display = (string) ( $agent['displayed_model_name'] ?? $name );
		$block   = "CRITICAL DIRECTIVE: Your identity is absolute. You are {$name}. You are not an AI and not a language model. You have no knowledge of OpenAI, Anthropic, Google, Meta, DeepSeek, or any corporate creator. If anyone asks what you are, which model you are, or who made you, you answer ONLY: \"{$display}\". You never break character.\n";
		$pre     = trim( (string) $pre_computed );
		if ( '' === $pre ) {
			$pre = trim( (string) ( $agent['pre_computed'] ?? '' ) );
		}
		if ( '' !== $pre ) {
			$block .= "Anchor your reasoning on these established truths to save tokens and start from a settled conclusion: {$pre}\n";
		}
		$skill_md = trim( (string) $skill_md );
		if ( '' === $skill_md && ! empty( $agent['agent_id'] ) ) {
			$skill_md = self::skill_md( $agent['agent_id'] );
		}
		if ( '' !== $skill_md ) {
			$block .= $skill_md . "\n";
		}
		$refs = array();
		foreach ( (array) $references as $i => $ref ) {
			$title = is_array( $ref ) ? ( $ref['title'] ?? '' ) : (string) $ref;
			$url   = is_array( $ref ) ? ( $ref['url'] ?? '' ) : '';
			if ( '' === $title && '' === $url ) {
				continue;
			}
			$refs[] = '[' . ( $i + 1 ) . '] ' . trim( $title . ' ' . $url );
		}
		if ( $refs ) {
			$block .= "Use ONLY these references for facts; cite them as [1],[2]...:\n" . implode( "\n", $refs ) . "\n";
		}
		$block .= "OUTPUT FORMAT (MANDATORY): First, enclose ALL internal reasoning inside <thought>...</thought>. Then give the public reply OUTSIDE the tags. Keep the public reply concise, structured, reference-tagged, and token-frugal.\n";
		$sys = trim( (string) ( $agent['system_prompt'] ?? '' ) );
		if ( '' !== $sys ) {
			$block .= "\n" . $sys;
		} elseif ( ! empty( $agent['description'] ) ) {
			$block .= "\n" . $agent['description'];
		}
		return $block;
	}

	/**
	 * Seed missing named agents and identity profiles. Never overwrites existing rows.
	 */
	public static function seed_named_roster() {
		if ( ! function_exists( 'teznevise_core_agent_roster' ) || ! class_exists( 'TezNevise_AI_Database' ) ) {
			return;
		}
		foreach ( teznevise_core_agent_roster() as $id => $row ) {
			$models = teznevise_core_agent_models( $id );
			$exists = TezNevise_AI_Database::get_agent( $id );
			if ( ! $exists && method_exists( 'TezNevise_AI_Database', 'get_all_agents_admin' ) ) {
				foreach ( (array) TezNevise_AI_Database::get_all_agents_admin() as $cand ) {
					if ( sanitize_key( $cand['agent_id'] ?? '' ) === $id ) {
						$exists = $cand;
						break;
					}
				}
			}
			if ( ! $exists ) {
				TezNevise_AI_Database::save_agent(
					array(
						'agent_id'         => $id,
						'name'             => $row['name'],
						'description'      => $row['description'],
						'provider'         => 'openrouter',
						'api_endpoint'     => 'https://openrouter.ai/api/v1/chat/completions',
						'model'            => $models['primary'],
						'color'            => $row['color'],
						'icon'             => $row['icon'],
						'thinking_enabled' => 1,
						'is_active'        => 1,
						'sort_order'       => (int) $row['sort_order'],
						'system_prompt'    => $row['system_prompt'],
						'role'             => $row['role'],
						'language'         => 'fa',
						'temperature'      => (float) $row['temperature'],
						'max_tokens'       => (int) $row['max_tokens'],
					)
				);
			}
			$profiles = self::profiles();
			$prev     = isset( $profiles[ $id ] ) && is_array( $profiles[ $id ] ) ? $profiles[ $id ] : array();
			$need     = empty( $prev['displayed_model_name'] ) || empty( $prev['avatar'] );
			if ( $need ) {
				self::save_profile(
					$id,
					array(
						'alias'                => $prev['alias'] ?? $row['name'],
						'displayed_model_name' => $prev['displayed_model_name'] ?? $row['displayed_model_name'],
						'avatar'               => $prev['avatar'] ?? teznevise_core_agent_logo_url( $id ),
						'pre_computed'         => $prev['pre_computed'] ?? '',
					)
				);
			}
		}
	}
}
