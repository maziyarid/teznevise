<?php
/**
 * Plugin Name: Teznevise Core
 * Description: Key vault, free-first model router, research oracle, async debate engine, named 8-agent roster.
 * Version: 1.9.22
 * Author: MAZ//ID (Maziyar)
 * Text Domain: teznevise
 *
 * Loaded from the theme when the file is not in wp-content/plugins, so cPanel
 * theme sync still activates the engine.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'TEZNEVISE_CORE_LOADED' ) ) {
	return;
}
define( 'TEZNEVISE_CORE_LOADED', true );
define( 'TEZNEVISE_CORE_DIR', __DIR__ );
define( 'TEZNEVISE_CORE_VERSION', '1.9.22' );

require_once TEZNEVISE_CORE_DIR . '/config/free-models.php';
require_once TEZNEVISE_CORE_DIR . '/config/agents.php';
require_once TEZNEVISE_CORE_DIR . '/config/skills.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-key-vault.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-logger.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-agent-registry.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-model-router.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-research-oracle.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-debate-orchestrator.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-rest.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-meta-boxes.php';
require_once TEZNEVISE_CORE_DIR . '/inc/class-admin-hub.php';

Teznevise_Key_Vault::hook_option_encryption();

add_action( 'rest_api_init', array( 'Teznevise_REST', 'register' ) );
add_action( 'add_meta_boxes', array( 'Teznevise_Meta_Boxes', 'register' ) );
add_action( 'admin_enqueue_scripts', array( 'Teznevise_Meta_Boxes', 'enqueue_admin' ) );
add_action( 'save_post_post', array( 'Teznevise_Meta_Boxes', 'save' ), 20, 2 );
add_action( 'save_post_page', array( 'Teznevise_Meta_Boxes', 'save' ), 20, 2 );
add_action( 'teznevise_core_run_debate', array( 'Teznevise_Debate_Orchestrator', 'run' ) );
add_action( 'teznevise_core_backfill_tick', array( 'Teznevise_Debate_Orchestrator', 'tick_queue' ) );
add_action( 'admin_menu', array( 'Teznevise_Admin_Hub', 'register' ) );
add_action( 'admin_init', array( 'Teznevise_Admin_Hub', 'handle_post' ) );
add_action( 'admin_init', array( 'Teznevise_Agent_Registry', 'seed_named_roster' ), 20 );
add_action( 'init', array( 'Teznevise_Debate_Orchestrator', 'maybe_start_backfill' ), 30 );
add_action(
	'transition_post_status',
	static function ( $new, $old, $post ) {
		if ( ! $post || 'post' !== $post->post_type ) {
			return;
		}
		if ( 'publish' === $new && 'publish' !== $old && class_exists( 'Teznevise_Debate_Orchestrator' ) ) {
			Teznevise_Debate_Orchestrator::schedule( (int) $post->ID, false );
		}
	},
	20,
	3
);

add_action(
	'admin_menu',
	static function () {
		add_submenu_page(
			'edit.php',
			__( 'هویت عامل‌ها', 'teznevise' ),
			__( 'هویت عامل‌ها', 'teznevise' ),
			'manage_options',
			'teznevise-core-agents',
			array( 'Teznevise_Meta_Boxes', 'render_agent_profiles' )
		);
	}
);

add_filter(
	'teznevise_ai_system_prompt_prefix',
	static function ( $prefix, $agent ) {
		if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
			$agent = Teznevise_Agent_Registry::hydrate( (array) $agent );
			$post_id = get_the_ID();
			if ( ! $post_id && ! empty( $GLOBALS['post']->ID ) ) {
				$post_id = (int) $GLOBALS['post']->ID;
			}
			$skill = Teznevise_Agent_Registry::skill_md( $agent['agent_id'] ?? '', (int) $post_id );
			return Teznevise_Agent_Registry::identity_lock( $agent, $skill );
		}
		return $prefix;
	},
	10,
	2
);
