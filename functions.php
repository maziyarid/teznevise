<?php
/**
 * Teznevise theme bootstrap.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'TEZNEVISE_VERSION', '1.6.2' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

require_once TEZNEVISE_DIR . '/inc/defaults.php';
require_once TEZNEVISE_DIR . '/inc/helpers.php';
require_once TEZNEVISE_DIR . '/inc/nav-walker.php';
require_once TEZNEVISE_DIR . '/inc/brand.php';
require_once TEZNEVISE_DIR . '/inc/customizer.php';
require_once TEZNEVISE_DIR . '/inc/page-meta.php';
require_once TEZNEVISE_DIR . '/inc/page-meta-extra.php';
require_once TEZNEVISE_DIR . '/inc/class-teznevise-builder.php';
require_once TEZNEVISE_DIR . '/inc/builder-defaults.php';
require_once TEZNEVISE_DIR . '/inc/builder-seed.php';
if ( is_admin() ) {
require_once TEZNEVISE_DIR . '/inc/admin/builder-admin.php';
require_once TEZNEVISE_DIR . '/inc/admin/builder-assets.php';
}
require_once TEZNEVISE_DIR . '/inc/cpts.php';
require_once TEZNEVISE_DIR . '/inc/blog.php';
require_once TEZNEVISE_DIR . '/inc/seo.php';
require_once TEZNEVISE_DIR . '/inc/setup-pages.php';
require_once TEZNEVISE_DIR . '/inc/promote-assets.php';
require_once TEZNEVISE_DIR . '/inc/screenshot-data.php';
require_once TEZNEVISE_DIR . '/inc/migration/shortcode-to-builder-migrator.php';
require_once TEZNEVISE_DIR . '/inc/migration/auto-run.php';

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
wp_enqueue_style( 'teznevise-font-vazirmatn', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
wp_enqueue_style( 'teznevise-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
wp_enqueue_style( 'teznevise-redesign', TEZNEVISE_URI . '/assets/css/redesign.css', array( 'teznevise-style' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-layout-refinements', TEZNEVISE_URI . '/assets/css/layout-refinements.css', array( 'teznevise-redesign' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-motion', TEZNEVISE_URI . '/assets/css/motion.css', array( 'teznevise-layout-refinements' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-batch-fixes', TEZNEVISE_URI . '/assets/css/batch-fixes.css', array( 'teznevise-motion' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-ui-round2', TEZNEVISE_URI . '/assets/css/ui-round2.css', array( 'teznevise-batch-fixes' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-site-polish', TEZNEVISE_URI . '/assets/css/site-polish.css', array( 'teznevise-ui-round2' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-header-fix', TEZNEVISE_URI . '/assets/css/header-fix.css', array( 'teznevise-site-polish' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-mobile-fixes', TEZNEVISE_URI . '/assets/css/mobile-fixes.css', array( 'teznevise-header-fix' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-blog', TEZNEVISE_URI . '/assets/css/blog.css', array( 'teznevise-mobile-fixes' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-nav-dropdown', TEZNEVISE_URI . '/assets/css/nav-dropdown.css', array( 'teznevise-mobile-fixes' ), TEZNEVISE_VERSION );
wp_enqueue_script( 'teznevise-redesign', TEZNEVISE_URI . '/assets/js/redesign.js', array(), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-main', TEZNEVISE_URI . '/assets/js/main.js', array( 'teznevise-redesign' ), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-nav-dropdown', TEZNEVISE_URI . '/assets/js/nav-dropdown.js', array( 'teznevise-main' ), TEZNEVISE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );
