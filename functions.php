<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 * https://github.com/maziyarid/M-Z
 *
 * @package Teznevise
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_VERSION', '1.5.0' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

function teznevise_setup() {
	load_theme_textdomain( 'teznevise', TEZNEVISE_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 48, 'width' => 106, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'teznevise' ),
		'footer'  => __( 'Footer Menu', 'teznevise' ),
		'mobile'  => __( 'Mobile Menu', 'teznevise' ),
		'bottom'  => __( 'Bottom Nav (Mobile)', 'teznevise' ),
	) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_resolve_asset( $relative_path ) {
	$relative_path = ltrim( $relative_path, '/' );
	$candidates = array(
		array( TEZNEVISE_DIR . '/' . $relative_path, TEZNEVISE_URI . '/' . $relative_path ),
		array( TEZNEVISE_DIR . '/teznevise_work/' . $relative_path, TEZNEVISE_URI . '/teznevise_work/' . $relative_path ),
	);
	foreach ( $candidates as $pair ) {
		if ( file_exists( $pair[0] ) && is_file( $pair[0] ) && filesize( $pair[0] ) > 0 ) {
			return array( 'url' => $pair[1], 'ver' => filemtime( $pair[0] ) ?: TEZNEVISE_VERSION );
		}
	}
	return null;
}

function teznevise_enqueue_assets() {
	wp_enqueue_style( 'teznevise-vazirmatn', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
	wp_enqueue_style( 'teznevise-bootstrap-rtl', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'teznevise-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );

	$css_files = array(
		'redesign'           => 'assets/css/redesign.css',
		'layout-refinements' => 'assets/css/layout-refinements.css',
		'motion'             => 'assets/css/motion.css',
		'batch-fixes'        => 'assets/css/batch-fixes.css',
		'ui-round2'          => 'assets/css/ui-round2.css',
		'site-polish'        => 'assets/css/site-polish.css',
	);
	$prev = array( 'teznevise-bootstrap-rtl', 'teznevise-fontawesome', 'teznevise-vazirmatn' );
	foreach ( $css_files as $handle => $path ) {
		$resolved = teznevise_resolve_asset( $path );
		if ( $resolved ) {
			wp_enqueue_style( 'teznevise-' . $handle, $resolved['url'], $prev, $resolved['ver'] );
			$prev = array( 'teznevise-' . $handle );
		}
	}

	$blog_css = teznevise_resolve_asset( 'assets/css/blog.css' );
	if ( $blog_css ) {
		wp_enqueue_style( 'teznevise-blog', $blog_css['url'], $prev, $blog_css['ver'] );
	}

	$service_css = array(
		'service-thesis'     => 'service-thesis',
		'service-proposal'   => 'service-proposal',
		'service-statistics' => 'service-statistics',
		'service-simulation' => 'service-simulation',
	);
	foreach ( $service_css as $slug => $handle ) {
		if ( is_page( $slug ) ) {
			teznevise_enqueue_optional_css( $handle, 'assets/css/' . $handle . '.css' );
		}
	}

	$js_resolved = teznevise_resolve_asset( 'assets/js/redesign.js' );
	if ( $js_resolved ) {
		wp_enqueue_script( 'teznevise-main', $js_resolved['url'], array(), $js_resolved['ver'], true );
	}
	wp_enqueue_script( 'teznevise-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', array(), '5.3.8', true );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );

function teznevise_enqueue_optional_css( $handle, $relative_path ) {
	$resolved = teznevise_resolve_asset( $relative_path );
	if ( $resolved ) {
		wp_enqueue_style( 'teznevise-' . $handle, $resolved['url'], array( 'teznevise-ui-round2' ), $resolved['ver'] );
	}
}

$teznevise_includes = array(
	'/inc/brand.php',
	'/inc/screenshot-data.php',
	'/inc/defaults.php',
	'/inc/helpers.php',
	'/inc/customizer.php',
	'/inc/page-meta.php',
	'/inc/page-meta-extra.php',
	'/inc/blog.php',
	'/inc/setup-pages.php',
	'/inc/promote-assets.php',
	'/inc/seo.php',
);
foreach ( $teznevise_includes as $file ) {
	$path = TEZNEVISE_DIR . $file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

function teznevise_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'teznevise-home';
	}
	$classes[] = 'teznevise-theme';
	return $classes;
}
add_filter( 'body_class', 'teznevise_body_classes' );

function teznevise_get_contact( $key, $default = '' ) {
	if ( function_exists( 'teznevise_mod' ) ) {
		return teznevise_mod( $key, $default );
	}
	$defaults = array(
		'phone'         => '09302822091',
		'phone_display' => '۰۹۳۰۲۸۲۲۰۹۱',
		'phone_intl'    => '+989302822091',
		'whatsapp'      => 'https://wa.me/989302822091',
		'telegram'      => 'https://t.me/Teznevise',
		'bale'          => 'https://ble.ir/teznevise',
		'email'         => 'teznevisan@gmail.com',
		'address'       => 'تهران، انقلاب، خیابان ۱۲ فروردین',
		'hours'         => 'شنبه تا پنجشنبه، ۹ تا ۲۱',
	);
	return get_theme_mod( 'teznevise_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default );
}

function teznevise_logo_url() {
	if ( file_exists( TEZNEVISE_DIR . '/assets/img/logo.jpg' ) && filesize( TEZNEVISE_DIR . '/assets/img/logo.jpg' ) > 0 ) {
		return TEZNEVISE_URI . '/assets/img/logo.jpg';
	}
	if ( file_exists( TEZNEVISE_DIR . '/teznevise_work/assets/img/logo.jpg' ) ) {
		return TEZNEVISE_URI . '/teznevise_work/assets/img/logo.jpg';
	}
	return '';
}
