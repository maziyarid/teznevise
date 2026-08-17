<?php
/**
 * Enqueue menu assets - temporary fix
 * This should be moved to functions.php
 */
function teznevise_enqueue_menu_assets() {
	// CSS files from assets directory - loaded in order
	wp_enqueue_style( 'teznevise-redesign', TEZNEVISE_URI . '/assets/css/redesign.css', array( 'teznevise-style' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-layout-refinements', TEZNEVISE_URI . '/assets/css/layout-refinements.css', array( 'teznevise-redesign' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-site-polish', TEZNEVISE_URI . '/assets/css/site-polish.css', array( 'teznevise-layout-refinements' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-header-fix', TEZNEVISE_URI . '/assets/css/header-fix.css', array( 'teznevise-site-polish' ), TEZNEVISE_VERSION );
	
	// JavaScript files - loaded in order, in footer
	wp_enqueue_script( 'teznevise-redesign', TEZNEVISE_URI . '/assets/js/redesign.js', array(), TEZNEVISE_VERSION, true );
	wp_enqueue_script( 'teznevise-main', TEZNEVISE_URI . '/assets/js/main.js', array( 'teznevise-redesign' ), TEZNEVISE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_menu_assets', 20 );
