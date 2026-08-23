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
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $brief_text ) > 1400 ) {
			$brief_text = mb_substr( $brief_text, 0, 1400 ) . '…';
		}
		$passages = self::extract_passages( $post->post_content, 12 );
		$ctx        = array(
			'post'         => $post,
			'article'      => $article,
			'brief'        => $brief_text,
			'pre'          => $pre,
			'refs'         => $refs,
			'post_id'      => $post_id,
			'passages'     => $passages,
			'used_quotes'  => array(),
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
			self::store_overview( $post_id, $overview['item']['content'], $force );
		}

		$first_id = Teznevise_Agent_Registry::first_responder( $query . ' ' . $brief_text );
		if ( 'teznevise' === $first_id ) {
			$first_id = 'christina';
		}
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
		if ( $created < 1 ) {
			update_post_meta( $post_id, '_teznevise_ai_job', 'failed' );
			return 0;
		}
		update_post_meta( $post_id, '_teznevise_ai_job', 'done' );
		update_post_meta( $post_id, '_teznevise_ai_content_hash', $hash );
		if ( get_option( 'teznevise_core_backfill_queue', array() ) ) {
			if ( ! wp_next_scheduled( 'teznevise_core_backfill_tick' ) ) {
				wp_schedule_single_event( time() + 90, 'teznevise_core_backfill_tick' );
			}
		}
		return $created;
	}

	/**
	 * Public snapshot of the AI discussion for REST.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	public static function thread_state( $post_id ) {
		$post_id = (int) $post_id;
		$thread  = function_exists( 'teznevise_ai_discussion_get' ) ? teznevise_ai_discussion_get( $post_id ) : array( 'items' => array() );
		$items   = array();
		foreach ( (array) ( $thread['items'] ?? array() ) as $item ) {
			$item    = (array) $item;
			$items[] = array(
				'id'      => (string) ( $item['id'] ?? '' ),
				'parent'  => (string) ( $item['parent'] ?? '0' ),
				'name'    => (string) ( $item['name'] ?? '' ),
				'slug'    => (string) ( $item['slug'] ?? '' ),
				'role'    => (string) ( $item['role'] ?? '' ),
				'color'   => (string) ( $item['color'] ?? '' ),
				'content' => wp_strip_all_tags( (string) ( $item['content'] ?? '' ) ),
				'thought' => (string) ( $item['thought'] ?? '' ),
				'avatar'  => (string) ( $item['avatar'] ?? '' ),
				'job'     => (string) ( $item['job'] ?? '' ),
			);
		}
		return array(
			'job'      => (string) get_post_meta( $post_id, '_teznevise_ai_job', true ),
			'overview' => (string) get_post_meta( $post_id, '_teznevise_ai_overview', true ),
			'count'    => count( $items ),
			'items'    => $items,
		);
	}

	/**
	 * Seed the debate tab immediately from the overview so the UI is not empty
	 * while WP-Cron finishes the rest of the panel.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	public static function seed_from_overview( $post_id ) {
		$post_id  = (int) $post_id;
		$overview = self::ensure_overview( $post_id );
		$state    = self::thread_state( $post_id );
		if ( $state['count'] > 0 ) {
			return $state;
		}
		if ( '' === $overview ) {
			self::schedule( $post_id, true );
			return self::thread_state( $post_id );
		}
		$agent = class_exists( 'Teznevise_Agent_Registry' ) ? ( Teznevise_Agent_Registry::get( 'teznevise' ) ?: array() ) : array();
		$name  = (string) ( $agent['alias'] ?? $agent['fa_name'] ?? 'تزنویسه' ) . ' — نمای کلی';
		$color = sanitize_hex_color( $agent['color'] ?? '' ) ?: '#145d4a';
		$cid   = wp_insert_comment(
			array(
				'comment_post_ID'      => $post_id,
				'comment_author'       => $name,
				'comment_author_email' => 'teznevise@ai.teznevise.ir',
				'comment_author_url'   => home_url( '/' ),
				'comment_content'      => wp_kses_post( $overview ),
				'comment_type'         => 'tz_ai',
				'comment_parent'       => 0,
				'comment_approved'     => 1,
				'user_id'              => 0,
			)
		);
		$item  = array(
			'id'      => (string) $cid,
			'parent'  => '0',
			'name'    => $name,
			'slug'    => 'teznevise',
			'role'    => 'synthesizer',
			'color'   => $color,
			'tags'    => 'overview',
			'content' => $overview,
			'thought' => '',
			'alias'   => $agent['alias'] ?? 'تزنویسه',
			'avatar'  => $agent['avatar'] ?? '',
			'job'     => 'overview',
		);
		$thread = array(
			'version'   => 3,
			'research'  => (string) get_post_meta( $post_id, '_teznevise_ai_research_text', true ),
			'generated' => time(),
			'overview'  => $overview,
			'items'     => array( $item ),
		);
		if ( function_exists( 'teznevise_ai_discussion_save' ) ) {
			teznevise_ai_discussion_save( $post_id, $thread );
		} else {
			update_post_meta( $post_id, '_teznevise_ai_discussion', wp_slash( wp_json_encode( $thread, JSON_UNESCAPED_UNICODE ) ) );
		}
		if ( 'running' !== (string) get_post_meta( $post_id, '_teznevise_ai_job', true ) ) {
			update_post_meta( $post_id, '_teznevise_ai_job', 'queued' );
		}
		return self::thread_state( $post_id );
	}

	/**
	 * Persist AI overview unless a human already reviewed it.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $text    Generated overview.
	 * @param bool   $force   Force overwrite (explicit regenerate).
	 */
	public static function store_overview( $post_id, $text, $force = false ) {
		$post_id = (int) $post_id;
		$text    = trim( wp_strip_all_tags( (string) $text ) );
		if ( $post_id <= 0 || '' === $text ) {
			return;
		}
		$force    = $force || '1' === (string) get_post_meta( $post_id, '_teznevise_ai_overview_force', true );
		$reviewed = '1' === (string) get_post_meta( $post_id, '_teznevise_ai_overview_reviewed', true );
		update_post_meta( $post_id, '_teznevise_ai_overview_ai', $text );
		if ( $reviewed && ! $force ) {
			delete_post_meta( $post_id, '_teznevise_ai_overview_force' );
			return;
		}
		update_post_meta( $post_id, '_teznevise_ai_overview', $text );
		if ( $force ) {
			delete_post_meta( $post_id, '_teznevise_ai_overview_force' );
			delete_post_meta( $post_id, '_teznevise_ai_overview_reviewed' );
			delete_post_meta( $post_id, '_teznevise_ai_overview_reviewed_at' );
			delete_post_meta( $post_id, '_teznevise_ai_overview_reviewed_by' );
		}
	}

	/**
	 * Generate the SERP/blog overview now (one LLM call) if missing.
	 *
	 * @param int  $post_id Post ID.
	 * @param bool $force   Overwrite.
	 * @return string
	 */
	public static function ensure_overview( $post_id, $force = false ) {
		$post_id  = (int) $post_id;
		$existing = (string) get_post_meta( $post_id, '_teznevise_ai_overview', true );
		if ( $existing && ! $force ) {
			return $existing;
		}
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}
		$article = wp_trim_words( wp_strip_all_tags( $post->post_content ), 900, '' );
		$agent   = class_exists( 'Teznevise_Agent_Registry' ) ? ( Teznevise_Agent_Registry::get( 'teznevise' ) ?: Teznevise_Agent_Registry::get( 'general' ) ) : array();
		if ( ! $agent ) {
			$agent = array(
				'provider'             => 'openrouter',
				'model'                => '',
				'alias'                => 'تزنویسه',
				'displayed_model_name' => 'تزنویسه',
				'agent_id'             => 'teznevise',
			);
		}
		$skill  = class_exists( 'Teznevise_Agent_Registry' ) ? Teznevise_Agent_Registry::skill_md( 'teznevise', $post_id ) : '';
		$prompt = class_exists( 'Teznevise_Agent_Registry' ) ? Teznevise_Agent_Registry::identity_lock( $agent, $skill ) : 'You are Teznevise.';
		$prompt .= "\nWrite the SERP/blog AI overview of this article. 80–120 Persian words. Consulting only. No ghostwriting.";
		$body    = self::complete_cascade( $post->post_title . "\n\n" . $article, $prompt, $agent, $article, $post_id );
		$parsed  = self::split_thought( $body );
		$text    = trim( wp_strip_all_tags( $parsed['public'] ) );
		if ( $text ) {
			self::store_overview( $post_id, $text, $force );
		}
		return $text;
	}

	/**
	 * Queue published posts that still need overview + debate.
	 *
	 * @param bool $force Re-queue even if already done.
	 * @return int Queued count.
	 */
	public static function enqueue_published( $force = false ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => 200,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		$ids = array();
		foreach ( (array) $query->posts as $id ) {
			$id = (int) $id;
			if ( $id <= 0 ) {
				continue;
			}
			if ( ! $force ) {
				$job      = (string) get_post_meta( $id, '_teznevise_ai_job', true );
				$overview = (string) get_post_meta( $id, '_teznevise_ai_overview', true );
				$disc     = get_post_meta( $id, '_teznevise_ai_discussion', true );
				if ( in_array( $job, array( 'queued', 'running' ), true ) ) {
					continue;
				}
				if ( '' !== $overview && $disc && 'done' === $job ) {
					continue;
				}
			}
			$ids[] = $id;
		}
		$existing = get_option( 'teznevise_core_backfill_queue', array() );
		$existing = is_array( $existing ) ? array_map( 'intval', $existing ) : array();
		$merged   = array_values( array_unique( array_merge( $existing, $ids ) ) );
		update_option( 'teznevise_core_backfill_queue', $merged, false );
		update_option(
			'teznevise_core_backfill_status',
			array(
				'total'   => count( $merged ),
				'started' => time(),
			),
			false
		);
		if ( $merged && ! wp_next_scheduled( 'teznevise_core_backfill_tick' ) ) {
			wp_schedule_single_event( time() + 8, 'teznevise_core_backfill_tick' );
			if ( function_exists( 'spawn_cron' ) ) {
				spawn_cron();
			}
		}
		return count( $merged );
	}

	/**
	 * Process one queued post, then reschedule.
	 */
	public static function tick_queue() {
		$queue = get_option( 'teznevise_core_backfill_queue', array() );
		if ( ! is_array( $queue ) || ! $queue ) {
			return;
		}
		$id = (int) array_shift( $queue );
		update_option( 'teznevise_core_backfill_queue', array_values( $queue ), false );
		if ( $id > 0 ) {
			self::schedule( $id, false );
		}
		if ( $queue && ! wp_next_scheduled( 'teznevise_core_backfill_tick' ) ) {
			wp_schedule_single_event( time() + 120, 'teznevise_core_backfill_tick' );
		}
	}

	/**
	 * One-shot after deploy: enable auto-on-publish and queue existing posts.
	 */
	public static function maybe_start_backfill() {
		if ( '1.9.13' === (string) get_option( 'teznevise_ai_auto_all_v' ) ) {
			return;
		}
		if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
			Teznevise_Agent_Registry::seed_named_roster();
		}
		$stored = get_option( 'teznevise_ai_comments', array() );
		if ( is_array( $stored ) ) {
			$stored['auto_on_publish'] = '1';
			$stored['enabled']         = '1';
			update_option( 'teznevise_ai_comments', $stored, false );
		}
		self::enqueue_published( false );
		update_option( 'teznevise_ai_auto_all_v', '1.9.13', false );
	}

	/**
	 * Distinct quote-worthy sentences from the article.
	 *
	 * @param string $html  Post content.
	 * @param int    $limit Max passages.
	 * @return string[]
	 */
	public static function extract_passages( $html, $limit = 10 ) {
		$text  = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $html ) ) );
		$parts = preg_split( '/(?<=[.!؟?؛;\n])\s+/u', $text );
		$out   = array();
		$seen  = array();
		foreach ( (array) $parts as $p ) {
			$p = trim( $p );
			$len = function_exists( 'mb_strlen' ) ? mb_strlen( $p ) : strlen( $p );
			if ( $len < 42 || $len > 260 ) {
				continue;
			}
			$key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $p ) : strtolower( $p );
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;
			$out[]        = $p;
			if ( count( $out ) >= (int) $limit ) {
				break;
			}
		}
		return $out;
	}

	/**
	 * Drop instruction leakage and CJK garbage from a model turn.
	 *
	 * @param array{thought:string,public:string} $parsed Split body.
	 * @return array{thought:string,public:string}
	 */
	public static function scrub_turn( $parsed ) {
		$thought = trim( (string) ( $parsed['thought'] ?? '' ) );
		$public  = trim( (string) ( $parsed['public'] ?? '' ) );
		$public  = preg_replace( '/[\x{4E00}-\x{9FFF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]+/u', '', $public );
		$public  = trim( preg_replace( '/[ \t]{2,}/', ' ', $public ) );
		if ( preg_match( '/CRITICAL DIRECTIVE|token-frugal|ghostwrite|OUTPUT FORMAT|I need to outline|You are (Teznevise|Christina|Ada|Professor|Parantez|Elara|Cyrus|Mira)|Public reply ≤|never break character|SKILL\.md|Job: overview/i', $thought ) ) {
			$thought = '';
		}
		if ( preg_match( '/CRITICAL DIRECTIVE|OUTPUT FORMAT|token-frugal/i', $public ) ) {
			$public = trim( preg_replace( '/CRITICAL DIRECTIVE[\s\S]{0,400}/i', '', $public ) );
		}
		return array(
			'thought' => $thought,
			'public'  => $public,
		);
	}

	/**
	 * Pick unused article quotes for this speaker.
	 *
	 * @param array<string,mixed> $ctx Context.
	 * @param int                 $n   How many.
	 * @return string[]
	 */
	private static function take_quotes( &$ctx, $n = 2 ) {
		$pool = isset( $ctx['passages'] ) && is_array( $ctx['passages'] ) ? $ctx['passages'] : array();
		$used = isset( $ctx['used_quotes'] ) && is_array( $ctx['used_quotes'] ) ? $ctx['used_quotes'] : array();
		$out  = array();
		foreach ( $pool as $p ) {
			if ( in_array( $p, $used, true ) ) {
				continue;
			}
			$out[]                = $p;
			$ctx['used_quotes'][] = $p;
			if ( count( $out ) >= $n ) {
				break;
			}
		}
		if ( ! $out && $pool ) {
			$out[] = $pool[0];
		}
		return $out;
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
	private static function speak( $agent_id, $job, &$ctx, $prior, $parent ) {
		$agent = Teznevise_Agent_Registry::get( $agent_id );
		if ( ! $agent ) {
			return null;
		}
		$post_id = (int) $ctx['post_id'];
		$quotes  = self::take_quotes( $ctx, 'overview' === $job || 'synthesis' === $job ? 3 : 2 );
		$qblock  = '';
		foreach ( $quotes as $i => $q ) {
			$qblock .= ( $i + 1 ) . '. «' . $q . "»\n";
		}
		$fa_name = (string) ( $agent['fa_name'] ?? $agent['alias'] ?? $agent['name'] ?? $agent_id );
		$role    = (string) ( $agent['role'] ?? $job );
		$prompt  = "CRITICAL DIRECTIVE: You are {$fa_name} ({$role}). Stay in character. Public reply MUST be Persian. Never English outlines, never Chinese, never restate these instructions.\n";
		$prompt .= "Debate architecture: quote the article with « », name the previous speaker when you attack or defend them, and try to prove or refute a concrete claim. Consulting only — no ghostwriting.\n";
		$prompt .= 'Private reasoning goes in <thought>…</thought> and may only discuss which quote you picked and the hole in the previous claim. Then the public Persian reply outside the tags.' . "\n";
		switch ( $job ) {
			case 'overview':
				$prompt .= "Job: frame. List the 3 actual claims THIS article makes (not generic SERP fluff). Each claim in one Persian sentence. Then name the clash the panel should fight over. 80–120 words.";
				break;
			case 'first':
				$prompt .= "Job: AFFIRM. Open by quoting one unused passage with « ». Prove that the article's claim stands. State a falsifiable claim others can attack. 90–160 Persian words.";
				break;
			case 'peer':
				$prompt .= "Job: DISSENT. Address the last speaker by name. Quote a DIFFERENT unused passage with « ». Refute or limit their claim. Point at overclaim, missing method, or a counter-example in the same article. 90–160 Persian words.";
				break;
			case 'debate':
				$prompt .= "Job: CROSS. Name the speaker you answer. Quote an unused passage. Either prove their claim with new evidence from the article, or refute it. Do not repeat their quote. 90–160 Persian words.";
				break;
			case 'visualizer':
				$prompt .= "Job: visualizer. Describe 1 figure the article implies (title, axes, what it would show) using a quote. No image URLs, no dummy data. Persian. ≤120 words.";
				break;
			case 'synthesis':
				$prompt .= "Job: synthesis/verdict. For each disputed claim: stood, fell, or unresolved — one line. Then one practical next step for the graduate student. Consulting only. 90–140 Persian words.";
				break;
		}
		if ( $qblock ) {
			$prompt .= "\n\nUnused article quotes (pick from these, mark them with « »):\n" . $qblock;
		}
		if ( ! empty( $ctx['brief'] ) ) {
			$prompt .= "\n\nLive research brief (do not dump; cite [n] only if it matches a quote):\n" . $ctx['brief'];
		}
		if ( $prior ) {
			$tail = $prior;
			if ( function_exists( 'mb_strlen' ) && mb_strlen( $tail ) > 1800 ) {
				$tail = mb_substr( $tail, -1800 );
			}
			$prompt .= "\n\nPrevious panel remarks:\n" . $tail;
		}
		$post    = $ctx['post'];
		$message = 'عنوان مقاله: ' . $post->post_title . "\n\nمتن مقاله (خلاصه):\n" . $ctx['article'] . "\n\nپاسخ عمومی را فقط به فارسی بنویس. یک نقل‌قول «» از مقاله بیاور و ادعا را ثابت یا رد کن.";
		$body    = self::complete_cascade( $message, $prompt, $agent, $ctx['article'] . ' ' . $ctx['brief'], $post_id );
		if ( '' === $body ) {
			return null;
		}
		$parsed = self::scrub_turn( self::split_thought( $body ) );
		if ( '' === $parsed['public'] ) {
			return null;
		}
		$tags   = $job;
		$name   = $fa_name;
		if ( 'visualizer' === $job ) {
			$name .= ' — تصویرساز';
			$role  = 'visualizer';
		} elseif ( 'overview' === $job ) {
			$name .= ' — نمای کلی';
		} elseif ( 'synthesis' === $job ) {
			$name .= ' — رأی نهایی';
		} elseif ( 'peer' === $job ) {
			$name .= ' — مخالف';
		} elseif ( 'first' === $job ) {
			$name .= ' — مدافع';
		} elseif ( 'debate' === $job ) {
			$name .= ' — تقاطع';
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
