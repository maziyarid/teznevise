<?php
/**
 * REST: summarise, regenerate, search overview.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_REST {

	public static function register() {
		register_rest_route(
			'teznevise-core/v1',
			'/summarise',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'summarise' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'post_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'nonce'   => array(
						'required' => false,
					),
				),
			)
		);
		register_rest_route(
			'teznevise-core/v1',
			'/regenerate',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'regenerate' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_rest_route(
			'teznevise-core/v1',
			'/search-overview',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'search_overview' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'teznevise-core/v1',
			'/debate-run',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'debate_run' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'teznevise-core/v1',
			'/debate',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'debate_status' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public static function summarise( $request ) {
		$post_id = (int) $request->get_param( 'post_id' );
		$post    = get_post( $post_id );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return new WP_Error( 'missing', 'Not found', array( 'status' => 404 ) );
		}
		$overview = Teznevise_Debate_Orchestrator::ensure_overview( $post_id );
		$lines    = Teznevise_Debate_Orchestrator::summarise( $post_id );
		if ( is_wp_error( $lines ) ) {
			return $lines;
		}
		if ( ! $lines ) {
			return new WP_Error( 'llm_empty', 'مدل پاسخی برنگرداند. کلید OpenRouter را در تنظیمات هوش مصنوعی بررسی کنید.', array( 'status' => 502 ) );
		}
		$job = (string) get_post_meta( $post_id, '_teznevise_ai_job', true );
		if ( ! in_array( $job, array( 'queued', 'running' ), true ) ) {
			$thread = get_post_meta( $post_id, '_teznevise_ai_discussion', true );
			$empty  = ! is_array( $thread ) || empty( $thread['items'] );
			if ( $empty || 'failed' === $job ) {
				Teznevise_Debate_Orchestrator::schedule( $post_id, true );
			}
		}
		return array(
			'success'  => true,
			'bullets'  => array_map( 'wp_strip_all_tags', (array) $lines ),
			'overview' => $overview,
		);
	}

	public static function debate_run( $request ) {
		$post_id = (int) $request->get_param( 'post_id' );
		$post    = get_post( $post_id );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return new WP_Error( 'missing', 'Not found', array( 'status' => 404 ) );
		}
		$state = Teznevise_Debate_Orchestrator::seed_from_overview( $post_id );
		$job   = (string) ( $state['job'] ?? '' );
		if ( 'running' !== $job ) {
			Teznevise_Debate_Orchestrator::schedule( $post_id, $state['count'] < 2 );
		}
		$state            = Teznevise_Debate_Orchestrator::thread_state( $post_id );
		$state['success'] = true;
		$state['queued']  = true;
		return $state;
	}

	public static function debate_status( $request ) {
		$post_id = (int) $request->get_param( 'post_id' );
		$post    = get_post( $post_id );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return new WP_Error( 'missing', 'Not found', array( 'status' => 404 ) );
		}
		$state            = Teznevise_Debate_Orchestrator::thread_state( $post_id );
		$state['success'] = true;
		return $state;
	}

	public static function regenerate( $request ) {
		$params  = $request->get_json_params();
		$post_id = isset( $params['post_id'] ) ? absint( $params['post_id'] ) : 0;
		if ( $post_id <= 0 || ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error( 'forbidden', 'Cannot edit this post', array( 'status' => 403 ) );
		}
		Teznevise_Debate_Orchestrator::schedule( $post_id, true );
		return array(
			'success' => true,
			'queued'  => true,
		);
	}

	public static function search_overview( $request ) {
		$q = sanitize_text_field( (string) $request->get_param( 'q' ) );
		if ( strlen( $q ) < 2 ) {
			return new WP_Error( 'short', 'Query too short', array( 'status' => 400 ) );
		}
		$key  = 'tz_serp_' . md5( mb_strtolower( $q ) );
		$hit  = get_transient( $key );
		if ( is_array( $hit ) ) {
			return $hit;
		}
		$tools = self::collect_titles( $q, array( 'page' ), 6 );
		$posts = self::collect_titles( $q, array( 'post' ), 6 );
		$blob  = $q . "\n" . implode( "\n", $tools ) . "\n" . implode( "\n", $posts );
		$agent = Teznevise_Agent_Registry::get( 'teznevise' ) ?: Teznevise_Agent_Registry::get( 'general' ) ?: array(
			'alias'                => 'تزنویسه',
			'displayed_model_name' => 'تزنویسه',
			'provider'             => 'openrouter',
			'model'                => '',
			'agent_id'             => 'teznevise',
		);
		$prompt = Teznevise_Agent_Registry::identity_lock( $agent );
		$prompt .= "\nWrite a short Persian AI overview of this search, citing [1] [2] for the listed results. 80–120 words.";
		$body    = Teznevise_Debate_Orchestrator::complete_cascade( $blob, $prompt, $agent, $blob );
		$parsed  = Teznevise_Debate_Orchestrator::split_thought( $body );
		$payload = array(
			'success' => true,
			'overview'=> wp_strip_all_tags( $parsed['public'] ),
			'citations'=> array_merge( $tools, $posts ),
		);
		set_transient( $key, $payload, 12 * HOUR_IN_SECONDS );
		return $payload;
	}

	private static function collect_titles( $q, $types, $n ) {
		$query = new WP_Query(
			array(
				's'              => $q,
				'post_type'      => $types,
				'post_status'    => 'publish',
				'posts_per_page' => $n,
				'no_found_rows'  => true,
			)
		);
		$out = array();
		foreach ( $query->posts as $p ) {
			$out[] = get_the_title( $p );
		}
		return $out;
	}
}
