<?php
/**
 * Teznevise theme bootstrap.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'TEZNEVISE_VERSION', '1.9.6' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

/** Load an optional theme module without taking down the site if a patch is partial. */
function teznevise_require_module( $relative_path ) {
	$path = TEZNEVISE_DIR . '/' . ltrim( $relative_path, '/' );
	if ( is_readable( $path ) ) {
		require_once $path;
	}
}

foreach ( array(
	'inc/defaults.php', 'inc/helpers.php', 'inc/nav-walker.php', 'inc/brand.php',
	'inc/customizer.php', 'inc/page-meta.php', 'inc/page-meta-extra.php',
	'inc/class-teznevise-builder.php', 'inc/builder-defaults.php', 'inc/extracted-pages.php',
	'inc/wxr-classic-content.php', 'inc/builder-seed.php', 'inc/cpts.php',
	'inc/builder-download-catalog.php', 'inc/blog.php', 'inc/seo.php', 'inc/security.php',
	'inc/setup-pages.php', 'inc/promote-assets.php', 'inc/screenshot-data.php',
	'inc/frontend-compat.php', 'inc/tezcoin.php', 'inc/legal-pages.php', 'inc/dashboard.php',
	'inc/ai-agents.php',
) as $tez_module ) {
	teznevise_require_module( $tez_module );
}
if ( is_admin() ) {
	teznevise_require_module( 'inc/admin/builder-admin.php' );
	teznevise_require_module( 'inc/admin/builder-assets.php' );
}
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
	teznevise_require_module( 'inc/migration/auto-run.php' );
}

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
add_theme_support( 'responsive-embeds' );
add_theme_support( 'align-wide' );
add_theme_support( 'editor-styles' );
add_editor_style( 'assets/css/tokens.css' );
add_image_size( 'teznevise-card', 720, 450, true );
add_image_size( 'teznevise-hero', 1440, 810, true );
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
register_nav_menus( array(
'primary' => __( 'Primary Menu', 'teznevise' ),
'mobile'  => __( 'Mobile Menu', 'teznevise' ),
'bottom'  => __( 'Bottom Mobile Menu', 'teznevise' ),
'footer'  => __( 'Footer Menu', 'teznevise' ),
) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_asset_url( $relative_path ) {
	$relative_path = '/' . ltrim( $relative_path, '/' );
	if ( is_readable( TEZNEVISE_DIR . $relative_path ) ) {
		return TEZNEVISE_URI . $relative_path;
	}
	// This patch bundle may be deployed alongside the repository-level assets.
	return home_url( '/assets' . $relative_path );
}

function teznevise_enqueue_assets() {
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-tokens', teznevise_asset_url( '/assets/css/tokens.css' ), array( 'teznevise-style' ), TEZNEVISE_VERSION );
	$fa_rel = '/assets/vendor/fontawesome/css/all.min.css';
	if ( is_readable( TEZNEVISE_DIR . $fa_rel ) ) {
		wp_enqueue_style( 'teznevise-fontawesome', TEZNEVISE_URI . $fa_rel, array(), TEZNEVISE_VERSION );
	} else {
		wp_enqueue_style( 'teznevise-fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/css/all.min.css', array(), '7.1.0' );
	}
	wp_enqueue_style( 'teznevise-components', teznevise_asset_url( '/assets/css/components.css' ), array( 'teznevise-tokens' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-pages', TEZNEVISE_URI . '/assets/css/pages.css', array( 'teznevise-components' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-chrome', TEZNEVISE_URI . '/assets/css/chrome.css', array( 'teznevise-pages' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-modernization', teznevise_asset_url( '/assets/css/modernization.css' ), array( 'teznevise-chrome' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-legacy-wpcode', teznevise_asset_url( '/assets/css/legacy-wpcode.css' ), array( 'teznevise-modernization' ), TEZNEVISE_VERSION );
	wp_enqueue_script( 'teznevise-calculators', teznevise_asset_url( '/assets/js/calculators.js' ), array(), TEZNEVISE_VERSION, true );
	wp_script_add_data( 'teznevise-calculators', 'strategy', 'defer' );

	wp_enqueue_script( 'teznevise-chrome', teznevise_asset_url( '/assets/js/chrome.js' ), array(), TEZNEVISE_VERSION, true );
	wp_script_add_data( 'teznevise-chrome', 'strategy', 'defer' );
	if ( function_exists( 'teznevise_localize_front_script' ) ) {
		teznevise_localize_front_script();
	}
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );

/**
 * Preload only the regular local font used above the fold.
 *
 * @param array $preload_resources Existing preload resources.
 * @return array
 */
function teznevise_preload_resources( $preload_resources ) {
	$preload_resources[] = array(
		'href'        => teznevise_asset_url( '/assets/fonts/Vazirmatn-Regular.woff2' ),
		'as'          => 'font',
		'type'        => 'font/woff2',
		'crossorigin' => 'anonymous',
	);
	return $preload_resources;
}
add_filter( 'wp_preload_resources', 'teznevise_preload_resources' );

/**
 * 1.8.5: parity CSS/JS now lives in chrome.css / chrome.js.
 * Keep this hook as a no-op so old callers do not fatal.
 */
function teznevise_enqueue_parity_css() {
	return;
}
