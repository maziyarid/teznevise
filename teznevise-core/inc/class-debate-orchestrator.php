<?php
/**
 * Async research → overview → first responder → peer → debate → visualizer → synthesis.
 * Never blocks save_post.
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
			update_post_meta( $post_id, '_teznevise_ai_research_text', $research['brief'] );
		}

		$pre  = (string) get_post_meta( $post_id, '_pre_computed_thoughts', true );
		$refs = get_post_meta( $post_id, '_teznevise_references', true );
		$refs = is_array( $refs ) ? $refs : array();
		if ( is_array( $research ) && ! empty( $research['sources'] ) && is_array( $research['sources'] ) ) {
			foreach ( $research['sources'] as $src ) {
				if ( is_array( $src ) ) {
					$refs[] = $src;
				} elseif ( is_string( $src ) ) {
					$refs[] = array( 'title' => $src, 'url' => '' );
				}
			}
		}

		$old = get_comments(
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
		$ctx        = array(
			'post'    => $post,
			'article' => $article,
			'brief'   => $brief_text,
			'pre'     => $pre,
			'refs'    => $refs,
			'post_id' => $post_id,
		);

		$parent  = 0;
		$prior   = '';
		$items   = array();
		$created = 0;
		$spoken  = array();
		$pipeline = array(
			'version'   => 3,
			'research'  => $brief_text,
			'sources'   => is_array( $research ) ? ( $research['sources'] ?? array() ) : array(),
			'generated' => time(),
			'steps'     => array(),
		);

		$overview = self::speak( 'teznevise', 'overview', $ctx, $prior, $parent );
		if ( $overview ) {
			$items[]  = $overview['item'];
			$parent   = (int) $overview['id'];
			$prior   .= $overview['prior'];
			$spoken[] = 'teznevise';
			++$created;
			$pipeline['steps'][] = 'overview';
			update_post_meta( $post_id, '_teznevise_ai_overview', $overview['item']['content'] );
		}

		$first_id = Teznevise_Agent_Registry::first_responder( $query . ' ' . $brief_text );
		$first    = self::speak( $first_id, 'first', $ctx, $prior, $parent );
		if ( $first ) {
			$items[]  = $first['item'];
			$parent   = (int) $first['id'];
			$prior   .= $first['prior'];
			$spoken[] = $first_id;
			++$created;
			$pipeline['steps'][]     = 'first:' . $first_id;
			$pipeline['first']       = $first_id;
		}

		$peer_id = Teznevise_Agent_Registry::peer_reviewer( $first_id );
		if ( $peer_id && $peer_id !== $first_id ) {
			$peer = self::speak( $peer_id, 'peer', $ctx, $prior, $parent );
			if ( $peer ) {
				$items[]  = $peer['item'];
				$parent   = (int) $peer['id'];
				$prior   .= $peer['prior'];
				$spoken[] = $peer_id;
				++$created;
				$pipeline['steps'][] = 'peer:' . $peer_id;
				$pipeline['peer']    = $peer_id;
			}
		}

		$sequence = function_exists( 'teznevise_core_debate_sequence' ) ? teznevise_core_debate_sequence() : array();
		foreach ( $sequence as $id ) {
			if ( in_array( $id, $spoken, true ) ) {
				continue;
			}
			$turn = self::speak( $id, 'debate', $ctx, $prior, $parent );
			if ( ! $turn ) {
				continue;
			}
			$items[]  = $turn['item'];
			$parent   = (int) $turn['id'];
			$prior   .= $turn['prior'];
			$spoken[] = $id;
			++$created;
			$pipeline['steps'][] = 'debate:' . $id;
		}

		$want_viz = (bool) preg_match( '/figure|chart|plot|diagram|جدول|نمودار|شکل|گراف|slide|اسلاید/iu', $query . ' ' . $brief_text );
		if ( $want_viz ) {
			$viz = self::speak( 'ada', 'visualizer', $ctx, $prior, $parent );
			if ( $viz ) {
				$items[]             = $viz['item'];
				$parent              = (int) $viz['id'];
				$prior              .= $viz['prior'];
				++$created;
				$pipeline['steps'][] = 'visualizer';
			}
		}

		$synth = self::speak( 'teznevise', 'synthesis', $ctx, $prior, $parent );
		if ( $synth ) {
			$items[]             = $synth['item'];
			++$created;
			$pipeline['steps'][] = 'synthesis';
		}

		$thread = array(
			'version'   => 3,
			'research'  => $brief_text,
			'sources'   => $pipeline['sources'],
			'generated' => time(),
			'overview'  => (string) get_post_meta( $post_id, '_teznevise_ai_overview', true ),
			'pipeline'  => $pipeline['steps'],
			'items'     => $items,
		);
		if ( function_exists( 'teznevise_ai_discussion_save' ) ) {
			teznevise_ai_discussion_save( $post_id, $thread );
		} else {
			update_post_meta( $post_id, '_teznevise_ai_discussion', wp_slash( wp_json_encode( $thread, JSON_UNESCAPED_UNICODE ) ) );
		}
		update_post_meta( $post_id, '_teznevise_ai_pipeline', $pipeline );
		update_post_meta( $post_id, '_teznevise_ai_job', 'done' );
		update_post_meta( $post_id, '_teznevise_ai_content_hash', $hash );
		return $created;
	}

	/**
	 * One named-agent turn.
	 *
	 * @param string               $agent_id Agent id.
	 * @param string               $job      overview|first|peer|debate|visualizer|synthesis.
	 * @param array<string,mixed>  $ctx      Shared article context.
	 * @param string               $prior    Previous public remarks.
	 * @param int                  $parent   Parent comment id.
	 * @return array{id:int,item:array,prior:string}|null
	 */
	private static function speak( $agent_id, $job, $ctx, $prior, $parent ) {
		$agent = Teznevise_Agent_Registry::get( $agent_id );
		if ( ! $agent ) {
			return null;
		}
		$post_id = (int) $ctx['post_id'];
		$skill   = Teznevise_Agent_Registry::skill_md( $agent_id, $post_id );
		$prompt  = Teznevise_Agent_Registry::identity_lock( $agent, $skill, $ctx['refs'], $ctx['pre'] );
		$prompt .= "\nJob: " . $job . '. Token-frugal. Public reply in Persian, ≤140 words.';
		switch ( $job ) {
			case 'overview':
				$prompt .= "\nWrite the SERP/blog AI overview of this article using the research brief. 80–120 words. Cite [n]. No ghostwriting.";
				break;
			case 'first':
				$prompt .= "\nYou are the first responder for this topic. Open the discussion. Ground every claim in the article and the brief.";
				break;
			case 'peer':
				$prompt .= "\nYou are the peer reviewer. Critique the first response: gaps, overclaim, missing method. Be specific and polite.";
				break;
			case 'debate':
				$prompt .= "\nContinue the panel. Add a new angle. Do not repeat earlier speakers. Reply to the last remark when useful.";
				break;
			case 'visualizer':
				$prompt .= "\nVisualizer job: describe 1–2 figures or slides in text (title, axes/labels, what it shows). No image URLs, no dummy data, no generated pictures.";
				break;
			case 'synthesis':
				$prompt .= "\nSynthesize the whole panel into a final reflection: agreements, remaining disagreement, one practical next step for the graduate student. Consulting only.";
				break;
		}
		if ( ! empty( $ctx['brief'] ) ) {
			$prompt .= "\n\nResearch brief:\n" . $ctx['brief'];
		}
		if ( $prior ) {
			$prompt .= "\n\nPrevious panel remarks:\n" . $prior;
		}
		$post    = $ctx['post'];
		$message = 'Article title: ' . $post->post_title . "\n\nFull article:\n" . $ctx['article'] . "\n\nWrite the next discussion comment in Persian.";
		$body    = self::complete_cascade( $message, $prompt, $agent, $ctx['article'] . ' ' . $ctx['brief'], $post_id );
		if ( '' === $body ) {
			return null;
		}
		$parsed = self::split_thought( $body );
		$role   = (string) ( $agent['role'] ?? $job );
		$tags   = $job;
		$name   = (string) ( $agent['alias'] ?? $agent['name'] ?? $agent_id );
		if ( 'visualizer' === $job ) {
			$name .= ' — تصویرساز';
			$role  = 'visualizer';
		} elseif ( 'overview' === $job ) {
			$name .= ' — نمای کلی';
		} elseif ( 'synthesis' === $job ) {
			$name .= ' — بازتاب';
		} elseif ( 'peer' === $job ) {
			$name .= ' — داور همتا';
		}
		$color = sanitize_hex_color( $agent['color'] ?? '' ) ?: '#145d4a';
		$cid   = wp_insert_comment(
			array(
				'comment_post_ID'      => $post_id,
				'comment_author'       => $name,
				'comment_author_email' => sanitize_key( $agent_id ) . '@ai.teznevise.ir',
				'comment_author_url'   => home_url( '/' ),
				'comment_content'      => wp_kses_post( $parsed['public'] ),
				'comment_type'         => 'tz_ai',
				'comment_parent'       => (int) $parent,
				'comment_approved'     => 1,
				'user_id'              => 0,
			)
		);
		if ( ! $cid ) {
			return null;
		}
		update_comment_meta( $cid, '_is_ai_agent', '1' );
		update_comment_meta( $cid, 'tz_ai_slug', sanitize_title( $agent_id ) );
		update_comment_meta( $cid, 'tz_ai_role', sanitize_text_field( $role ) );
		update_comment_meta( $cid, 'tz_ai_tags', sanitize_text_field( $tags ) );
		update_comment_meta( $cid, 'tz_ai_name', sanitize_text_field( $name ) );
		update_comment_meta( $cid, 'tz_ai_color', $color );
		update_comment_meta( $cid, 'tz_ai_thought', $parsed['thought'] );
		update_comment_meta( $cid, 'tz_ai_alias', sanitize_text_field( $agent['alias'] ?? $name ) );
		update_comment_meta( $cid, 'tz_ai_displayed_model', sanitize_text_field( $agent['displayed_model_name'] ?? '' ) );
		update_comment_meta( $cid, 'tz_ai_avatar', esc_url_raw( $agent['avatar'] ?? '' ) );
		update_comment_meta( $cid, 'tz_ai_job', sanitize_key( $job ) );
		$item = array(
			'id'      => (string) $cid,
			'parent'  => (string) $parent,
			'name'    => $name,
			'slug'    => $agent_id,
			'role'    => $role,
			'color'   => $color,
			'tags'    => $tags,
			'content' => $parsed['public'],
			'thought' => $parsed['thought'],
			'alias'   => $agent['alias'] ?? $name,
			'avatar'  => $agent['avatar'] ?? '',
			'job'     => $job,
		);
		return array(
			'id'    => (int) $cid,
			'item'  => $item,
			'prior' => "\n- " . $name . ': ' . wp_strip_all_tags( $parsed['public'] ),
		);
	}

	public static function complete_cascade( $message, $prompt, $agent, $corpus, $post_id = 0 ) {
		if ( ! class_exists( 'TezNevise_AI_API' ) || ! method_exists( 'TezNevise_AI_API', 'complete' ) ) {
			return '';
		}
		$chain   = Teznevise_Model_Router::chain( $agent, $corpus, 'debate' );
		$post_id = (int) $post_id;
		foreach ( $chain as $i => $attempt ) {
			$try             = $agent;
			$try['provider'] = $attempt['provider'];
			$try['model']    = $attempt['model'];
			$try['api_key']  = Teznevise_Key_Vault::get_provider_key( $attempt['provider'], $post_id );
			if ( 'openrouter' === $attempt['provider'] ) {
				$try['api_endpoint'] = 'https://openrouter.ai/api/v1/chat/completions';
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
		$agent = Teznevise_Agent_Registry::get( 'teznevise' );
		if ( ! $agent ) {
			$agent = Teznevise_Agent_Registry::get( 'general' );
		}
		if ( ! $agent ) {
			$agent = array(
				'provider'             => 'openrouter',
				'model'                => '',
				'alias'                => 'تزنویسه',
				'displayed_model_name' => 'تزنویسه',
				'agent_id'             => 'teznevise',
			);
		}
		$skill   = Teznevise_Agent_Registry::skill_md( 'teznevise', $post_id );
		$prompt  = Teznevise_Agent_Registry::identity_lock( $agent, $skill );
		$prompt .= "\nReturn 5–8 Persian bullet key points. No preamble.";
		$body    = self::complete_cascade( $post->post_title . "\n\n" . $article, $prompt, $agent, $article, (int) $post->ID );
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
