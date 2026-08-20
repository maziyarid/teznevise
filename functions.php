<?php
/**
 * Teznevise theme bootstrap.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'TEZNEVISE_VERSION', '1.8.2' );
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
require_once TEZNEVISE_DIR . '/inc/extracted-pages.php';
require_once TEZNEVISE_DIR . '/inc/builder-seed.php';
if ( is_admin() ) {
require_once TEZNEVISE_DIR . '/inc/admin/builder-admin.php';
require_once TEZNEVISE_DIR . '/inc/admin/builder-assets.php';
}
require_once TEZNEVISE_DIR . '/inc/cpts.php';
require_once TEZNEVISE_DIR . '/inc/builder-download-catalog.php';
require_once TEZNEVISE_DIR . '/inc/blog.php';
require_once TEZNEVISE_DIR . '/inc/seo.php';
require_once TEZNEVISE_DIR . '/inc/setup-pages.php';
require_once TEZNEVISE_DIR . '/inc/promote-assets.php';
require_once TEZNEVISE_DIR . '/inc/screenshot-data.php';
/*
 * Never load the shortcode migrator on the public front-end.
 * A parse error in that file (Unclosed '{' on line 370 / issue #425)
 * fatals every request that includes functions.php. Admin + WP-CLI only.
 */
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	$tez_migrator = TEZNEVISE_DIR . '/inc/migration/shortcode-to-builder-migrator.php';
	if ( is_readable( $tez_migrator ) ) {
		require_once $tez_migrator;
	}
	require_once TEZNEVISE_DIR . '/inc/migration/auto-run.php';
}
require_once TEZNEVISE_DIR . '/inc/frontend-compat.php';
require_once TEZNEVISE_DIR . '/inc/tezcoin.php';
require_once TEZNEVISE_DIR . '/inc/legal-pages.php';
require_once TEZNEVISE_DIR . '/inc/dashboard.php';
require_once TEZNEVISE_DIR . '/inc/ai-agents.php';

// Defensive fallback: this repo's functions.php has repeatedly lost this
// helper during merges/rewrites (see commits 085c9c44, 75a32ee6). If none of
// the required inc/ files above defined it, provide a safe default so
// header.php and footer.php never fatal-error on a missing symbol. Covers
// every contact type actually referenced in the templates (header.php uses
// phone_display and hours; footer.php uses telegram, whatsapp, phone_intl,
// phone_display, email, address).
if ( ! function_exists( 'teznevise_get_contact' ) ) {
	/**
	 * Get a contact channel value for header/footer template display.
	 *
	 * @param string $type Contact channel key.
	 * @return string
	 */
	function teznevise_get_contact( $type = 'phone' ) {
		$contacts = array(
			'phone'         => get_option( 'teznevise_contact_phone', '+98 21 0000 0000' ),
			'phone_intl'    => get_option( 'teznevise_contact_phone_intl', '982100000000' ),
			'phone_display' => get_option( 'teznevise_contact_phone_display', '+98 21 0000 0000' ),
			'whatsapp'      => get_option( 'teznevise_contact_whatsapp', '+98 21 0000 0000' ),
			'telegram'      => get_option( 'teznevise_contact_telegram', '' ),
			'email'         => get_option( 'admin_email' ),
			'address'       => get_option( 'teznevise_contact_address', '' ),
			'hours'         => get_option( 'teznevise_contact_hours', 'شنبه تا پنجشنبه: ۹ صبح تا ۲۱' ),
		);
		return isset( $contacts[ $type ] ) ? $contacts[ $type ] : '';
	}
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
wp_enqueue_style( 'teznevise-nav-touch', TEZNEVISE_URI . '/assets/css/nav-touch.css', array( 'teznevise-blog' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-product', TEZNEVISE_URI . '/assets/css/product-1.7.css', array( 'teznevise-nav-touch' ), TEZNEVISE_VERSION );
wp_enqueue_style( 'teznevise-nav-dropdown', TEZNEVISE_URI . '/assets/css/nav-dropdown.css', array( 'teznevise-product' ), TEZNEVISE_VERSION );

wp_enqueue_script( 'teznevise-redesign', TEZNEVISE_URI . '/assets/js/redesign.js', array(), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-main', TEZNEVISE_URI . '/assets/js/main.js', array( 'teznevise-redesign' ), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-nav-touch', TEZNEVISE_URI . '/assets/js/nav-touch.js', array( 'teznevise-main' ), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-product', TEZNEVISE_URI . '/assets/js/product-1.7.js', array( 'teznevise-nav-touch' ), TEZNEVISE_VERSION, true );
wp_enqueue_script( 'teznevise-nav-dropdown', TEZNEVISE_URI . '/assets/js/nav-dropdown.js', array( 'teznevise-product' ), TEZNEVISE_VERSION, true );
if ( function_exists( 'teznevise_localize_front_script' ) ) {
	teznevise_localize_front_script();
}
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );

function teznevise_enqueue_parity_css() {
	wp_enqueue_style(
		'teznevise-parity',
		TEZNEVISE_URI . '/assets/css/react-parity.css',
		array(),
		TEZNEVISE_VERSION
	);
	wp_enqueue_style(
		'teznevise-react-loader',
		TEZNEVISE_URI . '/assets/css/react-loader.css',
		array( 'teznevise-parity' ),
		TEZNEVISE_VERSION
	);
	wp_enqueue_script(
		'teznevise-react-loader',
		TEZNEVISE_URI . '/assets/js/react-loader.js',
		array( 'teznevise-nav-dropdown' ),
		TEZNEVISE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_parity_css', 999 );
