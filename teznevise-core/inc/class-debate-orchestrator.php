<?php
/**
 * Async research → debate pipeline. Never blocks save_post.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Debate_Orchestrator {

	public static function schedule( $post_id, $force = false ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return false;
		}
		if ( $force ) {
			delete_post_meta( $post_id, '_teznevise_ai_content_hash' );
		}
		update_post_meta( $post_id, '_teznevise_ai_job', 'queued' );
		if ( ! wp_next_scheduled( 'teznevise_core_run_debate', array( $post_id ) ) ) {
			wp_schedule_single_event( time() + 5, 'teznevise_core_run_debate', array( $post_id ) );
		}
		spawn_cron();
		return true;
	}

	public static function run( $post_id ) {
		$post_id = (int) $post_id;
		$post    = get_post( $post_id );
		if ( ! $post || ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
			return 0;
		}
		update_post_meta( $post_id, '_teznevise_ai_job', 'running' );

		$article = wp_trim_words( wp_strip_all_tags( $post->post_content ), 900, '' );
		$query   = $post->post_title . "\n\n" . $article;
		$hash    = Teznevise_Research_Oracle::hash( $query, '' );
		$prev    = (string) get_post_meta( $post_id, '_teznevise_ai_content_hash', true );
		if ( $prev === $hash && get_post_meta( $post_id, '_teznevise_ai_discussion', true ) ) {
			update_post_meta( $post_id, '_teznevise_ai_job', 'done' );
			return 0;
		}

		$research = Teznevise_Research_Oracle::brief( $query, $post_id );
		update_post_meta( $post_id, '_teznevise_ai_research', $research );
		update_post_meta( $post_id, '_teznevise_ai_research_hash', $hash );
		if ( is_array( $research ) && ! empty( $research['brief'] ) ) {
			// Keep the 1.9.8 string meta readable in the classic textarea.
			update_post_meta( $post_id, '_teznevise_ai_research_text', $research['brief'] );
		}

		$skill   = (string) get_post_meta( $post_id, '_teznevise_skill_md', true );
		$pre     = (string) get_post_meta( $post_id, '_pre_computed_thoughts', true );
		$refs    = get_post_meta( $post_id, '_teznevise_references', true );
		$refs    = is_array( $refs ) ? $refs : array();
		$chosen  = get_post_meta( $post_id, '_teznevise_debate_agents', true );
		$agents  = array();
		if ( is_array( $chosen ) && $chosen ) {
			foreach ( $chosen as $id ) {
				$row = Teznevise_Agent_Registry::get( $id );
				if ( $row && ( $row['role'] ?? '' ) !== 'researcher' ) {
					$agents[] = $row;
				}
			}
		}
		if ( ! $agents && function_exists( 'teznevise_ai_comment_settings' ) ) {
			$settings = teznevise_ai_comment_settings();
			$primary  = Teznevise_Agent_Registry::get( $settings['agent_id'] ?? 'general' );
			if ( $primary ) {
				$agents[] = $primary;
			}
		}
		if ( ! $agents ) {
			$fallback = Teznevise_Agent_Registry::get( 'general' );
			if ( $fallback ) {
				$agents[] = $fallback;
			}
		}

		$turns    = 4;
		if ( function_exists( 'teznevise_ai_comment_settings' ) ) {
			$turns = max( 1, min( 8, (int) teznevise_ai_comment_settings()['max_turns'] ) );
		}
		$speakers = array();
		if ( function_exists( 'teznevise_ai_comment_settings' ) ) {
			$speakers = array_values(
				array_filter(
					(array) teznevise_ai_comment_settings()['speakers'],
					static function ( $row ) {
						return ! empty( $row['active'] ) && ! empty( $row['name'] );
					}
				)
			);
		}

		$parent  = 0;
		$prior   = '';
		$items   = array();
		$created = 0;
		$old     = get_comments(
			array(
				'post_id' => $post_id,
				'type'    => 'tz_ai',
				'status'  => 'all',
				'fields'  => 'ids',
				'number'  => 200,
			)
		);
		foreach ( (array) $old as $cid ) {
			wp_delete_comment( (int) $cid, true );
		}

		$brief_text = is_array( $research ) ? (string) $research['brief'] : '';
		$n_speakers = $speakers ? count( $speakers ) : 1;

		for ( $i = 0; $i < $turns; $i++ ) {
			$speaker = $speakers ? $speakers[ $i % $n_speakers ] : array( 'name' => $agents[0]['alias'] ?? 'عامل', 'role' => 'analyst', 'color' => '#145d4a', 'slug' => 'agent', 'prompt' => '', 'tags' => '' );
			$agent   = $agents[ $i % count( $agents ) ];
			$prompt  = Teznevise_Agent_Registry::identity_lock( $agent, $skill, $refs, $pre );
			if ( ! empty( $speaker['prompt'] ) ) {
				$prompt .= "\nSpeaker instructions: " . $speaker['prompt'];
			}
			if ( $brief_text ) {
				$prompt .= "\n\nResearch brief:\n" . $brief_text;
			}
			if ( $prior ) {
				$prompt .= "\n\nPrevious panel remarks:\n" . $prior;
			}
			$message = 'Article title: ' . $post->post_title . "\n\nFull article:\n" . $article . "\n\nWrite the next discussion comment in Persian.";
			$body    = self::complete_cascade( $message, $prompt, $agent, $article . ' ' . $brief_text );
			if ( '' === $body ) {
				continue;
			}
			$parsed = self::split_thought( $body );
			$cid    = wp_insert_comment(
				array(
					'comment_post_ID'      => $post_id,
					'comment_author'       => $speaker['name'],
					'comment_author_email' => sanitize_key( $speaker['slug'] ?? $speaker['name'] ) . '@ai.teznevise.ir',
					'comment_author_url'   => home_url( '/' ),
					'comment_content'      => wp_kses_post( $parsed['public'] ),
					'comment_type'         => 'tz_ai',
					'comment_parent'       => $parent,
					'comment_approved'     => 1,
					'user_id'              => 0,
				)
			);
			if ( ! $cid ) {
				continue;
			}
			update_comment_meta( $cid, '_is_ai_agent', '1' );
			update_comment_meta( $cid, 'tz_ai_slug', sanitize_title( $speaker['slug'] ?? $speaker['name'] ) );
			update_comment_meta( $cid, 'tz_ai_role', sanitize_text_field( $speaker['role'] ?? '' ) );
			update_comment_meta( $cid, 'tz_ai_tags', sanitize_text_field( $speaker['tags'] ?? '' ) );
			update_comment_meta( $cid, 'tz_ai_name', sanitize_text_field( $speaker['name'] ) );
			update_comment_meta( $cid, 'tz_ai_color', sanitize_hex_color( $speaker['color'] ?? '#145d4a' ) ?: '#145d4a' );
			update_comment_meta( $cid, 'tz_ai_thought', $parsed['thought'] );
			update_comment_meta( $cid, 'tz_ai_alias', sanitize_text_field( $agent['alias'] ?? $speaker['name'] ) );
			update_comment_meta( $cid, 'tz_ai_displayed_model', sanitize_text_field( $agent['displayed_model_name'] ?? '' ) );
			$items[] = array(
				'id'      => (string) $cid,
				'parent'  => (string) $parent,
				'name'    => $speaker['name'],
				'slug'    => $speaker['slug'] ?? '',
				'role'    => $speaker['role'] ?? '',
				'color'   => $speaker['color'] ?? '#145d4a',
				'tags'    => $speaker['tags'] ?? '',
				'content' => $parsed['public'],
				'thought' => $parsed['thought'],
				'alias'   => $agent['alias'] ?? $speaker['name'],
			);
			$parent = (int) $cid;
			$prior .= "\n- " . $speaker['name'] . ': ' . wp_strip_all_tags( $parsed['public'] );
			++$created;
		}

		$thread = array(
			'version'   => 2,
			'research'  => $brief_text,
			'sources'   => is_array( $research ) ? ( $research['sources'] ?? array() ) : array(),
			'generated' => time(),
			'items'     => $items,
		);
		if ( function_exists( 'teznevise_ai_discussion_save' ) ) {
			teznevise_ai_discussion_save( $post_id, $thread );
		} else {
			update_post_meta( $post_id, '_teznevise_ai_discussion', wp_slash( wp_json_encode( $thread, JSON_UNESCAPED_UNICODE ) ) );
		}
		update_post_meta( $post_id, '_teznevise_ai_job', 'done' );
		update_post_meta( $post_id, '_teznevise_ai_content_hash', $hash );
		return $created;
	}

	public static function complete_cascade( $message, $prompt, $agent, $corpus ) {
		if ( ! class_exists( 'TezNevise_AI_API' ) || ! method_exists( 'TezNevise_AI_API', 'complete' ) ) {
			return '';
		}
		$chain = Teznevise_Model_Router::chain( $agent, $corpus, 'debate' );
		foreach ( $chain as $i => $attempt ) {
			$try            = $agent;
			$try['provider'] = $attempt['provider'];
			$try['model']    = $attempt['model'];
			if ( 'openrouter' === $attempt['provider'] ) {
				$try['api_endpoint'] = 'https://openrouter.ai/api/v1/chat/completions';
				$try['api_key']      = Teznevise_Key_Vault::get_provider_key( 'openrouter' );
			}
			$response = TezNevise_AI_API::complete( $message, $prompt, $try, $attempt['model'], true );
			if ( ! is_wp_error( $response ) && ! empty( $response['content'] ) ) {
				return (string) $response['content'];
			}
			Teznevise_Logger::log(
				'llm_fail',
				is_wp_error( $response ) ? $response->get_error_message() : 'empty',
				array(
					'provider' => $attempt['provider'],
					'model'    => $attempt['model'],
				)
			);
			if ( $i < count( $chain ) - 1 ) {
				Teznevise_Model_Router::sleep_backoff( $i );
			}
		}
		return '';
	}

	public static function split_thought( $body ) {
		$thought = '';
		$public  = (string) $body;
		if ( preg_match( '/<thought>(.*?)<\/thought>/is', $public, $m ) ) {
			$thought = trim( $m[1] );
			$public  = trim( preg_replace( '/<thought>.*?<\/thought>/is', '', $public ) );
		} elseif ( preg_match( '/<think>(.*?)<\/think>/is', $public, $m ) ) {
			$thought = trim( $m[1] );
			$public  = trim( preg_replace( '/<think>.*?<\/think>/is', '', $public ) );
		}
		return array(
			'thought' => $thought,
			'public'  => $public,
		);
	}

	public static function summarise( $post_id ) {
		$post_id = (int) $post_id;
		$cached  = get_post_meta( $post_id, '_teznevise_ai_summary_bullets', true );
		$hash    = (string) get_post_meta( $post_id, '_teznevise_ai_summary_hash', true );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'missing', 'Post not found', array( 'status' => 404 ) );
		}
		$article = wp_trim_words( wp_strip_all_tags( $post->post_content ), 900, '' );
		$now     = sha1( $article );
		if ( is_array( $cached ) && $hash === $now ) {
			return $cached;
		}
		$agent = Teznevise_Agent_Registry::get( 'general' );
		if ( ! $agent ) {
			$agent = array(
				'provider' => 'openrouter',
				'model'    => '',
				'alias'    => 'خلاصه تزنویسه',
				'displayed_model_name' => 'خلاصه تزنویسه',
			);
		}
		$prompt  = Teznevise_Agent_Registry::identity_lock( $agent );
		$prompt .= "\nReturn 5–8 Persian bullet key points. No preamble.";
		$body    = self::complete_cascade( $post->post_title . "\n\n" . $article, $prompt, $agent, $article );
		$lines   = array_values(
			array_filter(
				array_map(
					static function ( $line ) {
						$line = trim( preg_replace( '/^[\-\*\d\.\)\s]+/u', '', $line ) );
						return $line;
					},
					preg_split( '/\r\n|\r|\n/', wp_strip_all_tags( Teznevise_Debate_Orchestrator::split_thought( $body )['public'] ) )
				)
			)
		);
		if ( ! $lines && $body ) {
			$lines = array( wp_strip_all_tags( $body ) );
		}
		update_post_meta( $post_id, '_teznevise_ai_summary_bullets', $lines );
		update_post_meta( $post_id, '_teznevise_ai_summary_hash', $now );
		return $lines;
	}
}
