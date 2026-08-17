<?php
/**
 * Teznevise theme functions and definitions.
 *
 * @package Teznevise
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_VERSION', '1.0.0' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function teznevise_setup() {
	load_theme_textdomain( 'teznevise', TEZNEVISE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );

	register_nav_menus( array(
		'primary'   => __( 'Primary Menu', 'teznevise' ),
		'footer'    => __( 'Footer Menu', 'teznevise' ),
		'mobile'    => __( 'Mobile Menu', 'teznevise' ),
		'bottom'    => __( 'Bottom Nav (Mobile)', 'teznevise' ),
	) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

/**
 * Enqueue scripts and styles.
 */
function teznevise_enqueue_assets() {
	$ver = TEZNEVISE_VERSION;

	// Fonts (Vazirmatn via CDN for now; can be localized later).
	wp_enqueue_style(
		'teznevise-vazirmatn',
		'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css',
		array(),
		'33.003'
	);

	// Bootstrap RTL (kept for compatibility with existing markup).
	wp_enqueue_style(
		'teznevise-bootstrap-rtl',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css',
		array(),
		'5.3.8'
	);

	// Font Awesome.
	wp_enqueue_style(
		'teznevise-fontawesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);

	// Core design system.
	$css_files = array(
		'redesign'            => 'assets/css/redesign.css',
		'layout-refinements'  => 'assets/css/layout-refinements.css',
		'motion'              => 'assets/css/motion.css',
		'batch-fixes'         => 'assets/css/batch-fixes.css',
		'ui-round2'           => 'assets/css/ui-round2.css',
		'site-polish'         => 'assets/css/site-polish.css',
	);

	$prev = array( 'teznevise-bootstrap-rtl', 'teznevise-fontawesome', 'teznevise-vazirmatn' );
	foreach ( $css_files as $handle => $path ) {
		$full = TEZNEVISE_DIR . '/' . $path;
		if ( file_exists( $full ) ) {
			$dep_ver = filemtime( $full ) ?: $ver;
			wp_enqueue_style(
				'teznevise-' . $handle,
				TEZNEVISE_URI . '/' . $path,
				$prev,
				$dep_ver
			);
			$prev = array( 'teznevise-' . $handle );
		}
	}

	// Conditional service styles.
	if ( is_page_template( 'page-service-thesis.php' ) || is_page( 'service-thesis' ) ) {
		teznevise_enqueue_optional_css( 'service-thesis', 'assets/css/service-thesis.css' );
	}
	if ( is_page_template( 'page-service-proposal.php' ) || is_page( 'service-proposal' ) ) {
		teznevise_enqueue_optional_css( 'service-proposal', 'assets/css/service-proposal.css' );
	}
	if ( is_page_template( 'page-service-statistics.php' ) || is_page( 'service-statistics' ) ) {
		teznevise_enqueue_optional_css( 'service-statistics', 'assets/css/service-statistics.css' );
	}
	if ( is_page_template( 'page-service-simulation.php' ) || is_page( 'service-simulation' ) ) {
		teznevise_enqueue_optional_css( 'service-simulation', 'assets/css/service-simulation.css' );
	}

	// Theme JS (motion + navigation + FAQ + FAB).
	$js_path = TEZNEVISE_DIR . '/assets/js/redesign.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'teznevise-main',
			TEZNEVISE_URI . '/assets/js/redesign.js',
			array(),
			filemtime( $js_path ) ?: $ver,
			true
		);
	}

	// Bootstrap JS (for any residual components).
	wp_enqueue_script(
		'teznevise-bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
		array(),
		'5.3.8',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );

/**
 * Helper: enqueue optional CSS if file exists.
 */
function teznevise_enqueue_optional_css( $handle, $relative_path ) {
	$full = TEZNEVISE_DIR . '/' . $relative_path;
	if ( file_exists( $full ) ) {
		wp_enqueue_style(
			'teznevise-' . $handle,
			TEZNEVISE_URI . '/' . $relative_path,
			array( 'teznevise-ui-round2' ),
			filemtime( $full ) ?: TEZNEVISE_VERSION
		);
	}
}

/**
 * Include modular files.
 */
$teznevise_includes = array(
	'/inc/setup.php',
	'/inc/enqueue.php',
	'/inc/customizer.php',
	'/inc/seo.php',
	'/inc/helpers.php',
);

foreach ( $teznevise_includes as $file ) {
	$path = TEZNEVISE_DIR . $file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

/**
 * Body classes.
 */
function teznevise_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'teznevise-home';
	}
	$classes[] = 'teznevise-theme';
	return $classes;
}
add_filter( 'body_class', 'teznevise_body_classes' );

/**
 * Default contact values (can be overridden via Customizer).
 */
function teznevise_get_contact( $key, $default = '' ) {
	$defaults = array(
		'phone'        => '09302822091',
		'phone_display'=> '۰۹۳۰۲۸۲۲۰۹۱',
		'phone_intl'   => '+989302822091',
		'whatsapp'     => 'https://wa.me/989302822091',
		'telegram'     => 'https://t.me/Teznevise',
		'bale'         => 'https://ble.ir/teznevise',
		'email'        => 'teznevisan@gmail.com',
		'address'      => 'تهران، انقلاب، خیابان ۱۲ فروردین',
		'hours'        => 'شنبه تا پنجشنبه، ۹ تا ۲۱',
	);
	$value = get_theme_mod( 'teznevise_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default );
	return $value;
}
