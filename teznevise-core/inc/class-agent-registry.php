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
		$all = self::profiles();
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

	public static function get( $agent_id ) {
		$agent_id = sanitize_key( $agent_id );
		if ( class_exists( 'TezNevise_AI_Database' ) ) {
			$row = TezNevise_AI_Database::get_agent( $agent_id );
			if ( $row ) {
				return self::hydrate( (array) $row );
			}
		}
		return null;
	}

	public static function hydrate( $row ) {
		$id       = sanitize_key( $row['agent_id'] ?? '' );
		$profiles = self::profiles();
		$extra    = isset( $profiles[ $id ] ) && is_array( $profiles[ $id ] ) ? $profiles[ $id ] : array();
		$alias    = trim( (string) ( $extra['alias'] ?? '' ) );
		if ( '' === $alias ) {
			$alias = (string) ( $row['name'] ?? $id );
		}
		$display = trim( (string) ( $extra['displayed_model_name'] ?? '' ) );
		if ( '' === $display ) {
			$display = $alias;
		}
		$row['alias']                = $alias;
		$row['displayed_model_name'] = $display;
		$row['avatar']               = (string) ( $extra['avatar'] ?? '' );
		$row['pre_computed']         = (string) ( $extra['pre_computed'] ?? '' );
		return $row;
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
}
