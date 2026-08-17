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
require_once TEZNEVISE_DIR . '/inc/blog.php';
require_once TEZNEVISE_DIR . '/inc/seo.php';

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
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), TEZNEVISE_VERSION );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );
