<?php
/**
 * Teznevise theme functions.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/blog.php';

function teznevise_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => __( 'Primary Menu', 'teznevise' ) ) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_enqueue_assets() {
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), '1.3.0' );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );
