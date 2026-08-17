<?php
/**
 * Teznevise theme functions.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/seo.php';

function teznevise_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 48, 'width' => 106, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	register_nav_menus( array( 'primary' => __( 'Primary Menu', 'teznevise' ), 'footer' => __( 'Footer Menu', 'teznevise' ), 'mobile' => __( 'Mobile Menu', 'teznevise' ), 'bottom' => __( 'Bottom Nav (Mobile)', 'teznevise' ) ) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_resolve_asset( $relative_path ) {
	$relative_path = ltrim( $relative_path, '/' );
	foreach ( array( array( get_template_directory() . '/' . $relative_path, get_template_directory_uri() . '/' . $relative_path ), array( get_template_directory() . '/teznevise_work/' . $relative_path, get_template_directory_uri() . '/teznevise_work/' . $relative_path ) ) as $pair ) {
		if ( file_exists( $pair[0] ) && is_file( $pair[0] ) && filesize( $pair[0] ) > 0 ) { return array( 'url' => $pair[1], 'ver' => filemtime( $pair[0] ) ?: '1.3.0' ); }
	}
	return null;
}

function teznevise_enqueue_assets() {
	wp_enqueue_style( 'teznevise-vazirmatn', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
	wp_enqueue_style( 'teznevise-bootstrap-rtl', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'teznevise-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
	$css_files = array( 'redesign' => 'assets/css/redesign.css', 'layout-refinements' => 'assets/css/layout-refinements.css', 'motion' => 'assets/css/motion.css', 'batch-fixes' => 'assets/css/batch-fixes.css', 'ui-round2' => 'assets/css/ui-round2.css', 'site-polish' => 'assets/css/site-polish.css' );
	$prev = array( 'teznevise-bootstrap-rtl', 'teznevise-fontawesome', 'teznevise-vazirmatn' );
	foreach ( $css_files as $handle => $path ) { $resolved = teznevise_resolve_asset( $path ); if ( $resolved ) { wp_enqueue_style( 'teznevise-' . $handle, $resolved['url'], $prev, $resolved['ver'] ); $prev = array( 'teznevise-' . $handle ); } }
	$js_resolved = teznevise_resolve_asset( 'assets/js/redesign.js' );
	if ( $js_resolved ) { wp_enqueue_script( 'teznevise-main', $js_resolved['url'], array(), $js_resolved['ver'], true ); }
	wp_enqueue_script( 'teznevise-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', array(), '5.3.8', true );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );

function teznevise_get_contact( $key, $default = '' ) {
	if ( function_exists( 'teznevise_mod' ) ) { return teznevise_mod( $key, $default ); }
	$defaults = array( 'phone' => '09302822091', 'phone_display' => '۰۹۳۰۲۸۲۲۰۹۱', 'phone_intl' => '+989302822091', 'whatsapp' => 'https://wa.me/989302822091', 'telegram' => 'https://t.me/Teznevise', 'bale' => 'https://ble.ir/teznevise', 'email' => 'teznevisan@gmail.com', 'address' => 'تهران، انقلاب، خیابان ۱۲ فروردین', 'hours' => 'شنبه تا پنجشنبه، ۹ تا ۲۱' );
	return get_theme_mod( 'teznevise_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default );
}

function teznevise_logo_url() {
	$path = get_template_directory() . '/assets/img/logo.jpg';
	if ( file_exists( $path ) && filesize( $path ) > 0 ) { return get_template_directory_uri() . '/assets/img/logo.jpg'; }
	$path = get_template_directory() . '/teznevise_work/assets/img/logo.jpg';
	return file_exists( $path ) ? get_template_directory_uri() . '/teznevise_work/assets/img/logo.jpg' : '';
}
