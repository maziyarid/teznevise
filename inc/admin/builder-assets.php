<?php
/**
 * Flexible page builder — admin asset loading.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue builder admin assets on supported edit screens.
 *
 * @param string $hook Current admin page.
 */
function teznevise_builder_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, teznevise_builder_post_types(), true ) ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_style(
		'teznevise-builder-admin',
		TEZNEVISE_URI . '/assets/css/builder-admin.css',
		array(),
		TEZNEVISE_VERSION
	);
	wp_enqueue_style(
		'teznevise-fa-admin',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);

	wp_enqueue_script(
		'teznevise-builder-admin',
		TEZNEVISE_URI . '/assets/js/builder-admin.js',
		array( 'jquery', 'jquery-ui-sortable' ),
		TEZNEVISE_VERSION,
		true
	);
	wp_localize_script( 'teznevise-builder-admin', 'teznevise_builder_config', teznevise_builder_admin_config() );
}
add_action( 'admin_enqueue_scripts', 'teznevise_builder_admin_assets' );
