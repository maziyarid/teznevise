<?php
/**
 * Teznevise theme bootstrap.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'TEZNEVISE_VERSION', '1.5.0' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

require_once TEZNEVISE_DIR . '/inc/helpers.php';
require_once TEZNEVISE_DIR . '/inc/defaults.php';
require_once TEZNEVISE_DIR . '/inc/blog.php';
require_once TEZNEVISE_DIR . '/inc/seo.php';

/**
 * Get contact information from theme mods with defaults fallback.
 *
 * @param string $key Contact key (phone_display, hours, etc.)
 * @return string
 */
function teznevise_get_contact( $key, $default = '' ) {
	if ( function_exists( 'teznevise_mod' ) ) {
		return teznevise_mod( $key, $default );
	}
	$defaults = array(
		'phone'         => '09302822091',
		'phone_display' => '۰۹۳۰۲۸۲۲۰۹۱',
		'hours'         => 'شنبه تا پنجشنبه، ۹ تا ۲۱',
	);
	return get_theme_mod( 'teznevise_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default );
}

function teznevise_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'teznevise' ),
		'mobile'  => __( 'Mobile Menu', 'teznevise' ),
		'bottom'  => __( 'Bottom Mobile Menu', 'teznevise' ),
		'footer'  => __( 'Footer Menu', 'teznevise' ),
	) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_enqueue_assets() {
	// Main stylesheet
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), TEZNEVISE_VERSION );
	
	// Font Awesome 6 (Free) - required for icon classes used in header and footer
	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
	
	// CSS files from assets directory - loaded in order
	wp_enqueue_style( 'teznevise-redesign', TEZNEVISE_URI . '/assets/css/redesign.css', array( 'teznevise-style', 'font-awesome' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-layout-refinements', TEZNEVISE_URI . '/assets/css/layout-refinements.css', array( 'teznevise-redesign' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-site-polish', TEZNEVISE_URI . '/assets/css/site-polish.css', array( 'teznevise-layout-refinements' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-header-fix', TEZNEVISE_URI . '/assets/css/header-fix.css', array( 'teznevise-site-polish' ), TEZNEVISE_VERSION );
	
	// JavaScript files - loaded in order, in footer
	wp_enqueue_script( 'teznevise-redesign', TEZNEVISE_URI . '/assets/js/redesign.js', array(), TEZNEVISE_VERSION, true );
	wp_enqueue_script( 'teznevise-main', TEZNEVISE_URI . '/assets/js/main.js', array( 'teznevise-redesign' ), TEZNEVISE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );
