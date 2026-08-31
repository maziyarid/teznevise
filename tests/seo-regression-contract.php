<?php
/**
 * Static/runtime-light contract tests for SEO recovery safeguards.
 *
 * Runs without WordPress so CI can catch regressions before activation tests.
 */

$root = dirname( __DIR__ );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $root . '/' );
}

$GLOBALS['tez_test_pages']           = array();
$GLOBALS['tez_test_is_page']         = false;
$GLOBALS['tez_test_queried_id']      = 0;
$GLOBALS['tez_test_permalink_by_id'] = array();

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '/' ) {
		$path = (string) $path;
		if ( '' === $path ) {
			return 'https://teznevise.ir';
		}
		return 'https://teznevise.ir/' . ltrim( $path, '/' );
	}
}
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $key ) {
		return 'home' === $key ? 'https://teznevise.ir' : false;
	}
}
if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}
if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) { return $value; }
}
if ( ! function_exists( 'untrailingslashit' ) ) {
	function untrailingslashit( $value ) { return rtrim( (string) $value, '/' ); }
}
if ( ! function_exists( 'add_action' ) ) {
	function add_action() { return true; }
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() { return true; }
}
if ( ! function_exists( 'is_page' ) ) {
	function is_page() { return ! empty( $GLOBALS['tez_test_is_page'] ); }
}
if ( ! function_exists( 'get_queried_object_id' ) ) {
	function get_queried_object_id() { return (int) $GLOBALS['tez_test_queried_id']; }
}
if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		public $ID = 0;
		public $post_name = '';
		public $post_status = 'publish';
		public $permalink = '';
	}
}
if ( ! function_exists( 'get_page_by_path' ) ) {
	function get_page_by_path( $path ) {
		return isset( $GLOBALS['tez_test_pages'][ $path ] ) ? $GLOBALS['tez_test_pages'][ $path ] : null;
	}
}
if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = 0 ) {
		if ( $post instanceof WP_Post ) {
			return $post->permalink;
		}
		$id = (int) $post;
		return isset( $GLOBALS['tez_test_permalink_by_id'][ $id ] ) ? $GLOBALS['tez_test_permalink_by_id'][ $id ] : false;
	}
}

require_once $root . '/inc/defaults.php';
require_once $root . '/inc/seo-regressions.php';

$failures = array();
$assert_same = static function ( $expected, $actual, $label ) use ( &$failures ) {
	if ( $expected !== $actual ) {
		$failures[] = $label . ': expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true );
	}
};
$assert_true = static function ( $condition, $label ) use ( &$failures ) {
	if ( ! $condition ) {
		$failures[] = $label;
	}
};

// Treat runtime warnings (including malformed preg_match delimiters) as test failures.
set_error_handler(
	static function ( $severity, $message, $file, $line ) {
		throw new ErrorException( $message, 0, $severity, $file, $line );
	}
);

try {
	// The canonical URL helper must never convert action/absolute URLs into local paths.
	$assert_same( 'tel:09302822091', teznevise_url( 'tel:09302822091' ), 'tel URL preservation' );
	$assert_same( 'mailto:seo@example.com', teznevise_url( 'mailto:seo@example.com' ), 'mailto URL preservation' );
	$assert_same( 'https://example.com/path', teznevise_url( 'https://example.com/path' ), 'absolute HTTPS preservation' );
	$assert_same( '#faq', teznevise_url( '#faq' ), 'fragment URL preservation' );
	$assert_same( 'https://teznevise.ir/proposal/', teznevise_url( '/proposal/' ), 'relative internal URL resolution' );

	// The defensive home_url filter repairs same-site malformed URLs and schemes,
	// but must not promote an arbitrary malformed external host.
	$assert_same(
		'https://teznevise.ir/inquiry/',
		teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/https:/teznevise.ir/inquiry/', 'https:/teznevise.ir/inquiry/', 'https', 1 ),
		'malformed same-domain HTTPS recovery'
	);
	$assert_same(
		'tel:09302822091',
		teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/tel:09302822091', 'tel:09302822091', 'https', 1 ),
		'legacy tel home_url recovery'
	);
	$assert_same(
		'https://teznevise.ir/https:/untrusted.example/path',
		teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/https:/untrusted.example/path', 'https:/untrusted.example/path', 'https', 1 ),
		'untrusted malformed host is not promoted'
	);

	$map = teznevise_seo_regression_redirect_map();
	$expected_routes = array(
		'/article'                                             => '/service-article/',
		'/article/isi'                                         => '/service-article/',
		'/article/academic'                                    => '/service-article/',
		'/project/assignments/mechanical-civil/thermodynamics' => '/service-project/',
		'/project/engineering/electrical/gams'                 => '/gams/',
		'/thesis/engineering/electrical'                       => '/thesis/engineering/',
		'/thesis/other-fields/space-science'                   => '/thesis/pure-science/',
		'/how-to-write-research-proposal'                      => '/proposal/',
	);
	foreach ( $expected_routes as $legacy => $canonical ) {
		$assert_same( $canonical, isset( $map[ $legacy ] ) ? $map[ $legacy ] : null, 'redirect contract ' . $legacy );
	}

	$GLOBALS['tez_test_pages'] = array();
	$assert_same(
		'https://teznevise.ir/service-article/?utm_source=gsc&x=1',
		teznevise_seo_regression_resolve_target( '/article/?utm_source=gsc&x=1', 'utm_source=gsc&x=1' ),
		'recovery redirect preserves query string'
	);
	$assert_same( false, teznevise_seo_regression_resolve_target( '/not-a-legacy-route/' ), 'unknown route is not redirected' );

	$published              = new WP_Post();
	$published->ID          = 99;
	$published->post_name   = 'article';
	$published->post_status = 'publish';
	$published->permalink   = 'https://teznevise.ir/article/';
	$GLOBALS['tez_test_pages']['article'] = $published;
	$assert_same( false, teznevise_seo_regression_resolve_target( '/article/' ), 'recreated published legacy page wins' );
	$GLOBALS['tez_test_pages'] = array();

	// GAMS: stored Yoast canonical at the dead origin must be reversed only when
	// that origin maps exactly to the current page permalink.
	$GLOBALS['tez_test_is_page'] = true;
	$GLOBALS['tez_test_queried_id'] = 1678;
	$GLOBALS['tez_test_permalink_by_id'][1678] = 'https://teznevise.ir/gams/';
	$assert_same(
		'https://teznevise.ir/gams/',
		teznevise_seo_regression_canonical( 'https://teznevise.ir/project/engineering/electrical/gams/' ),
		'migrated GAMS page self-canonicalises'
	);
	$assert_same(
		'https://example.com/custom-canonical/',
		teznevise_seo_regression_canonical( 'https://example.com/custom-canonical/' ),
		'unrelated canonical remains unchanged'
	);
	$GLOBALS['tez_test_is_page'] = false;
} catch ( Throwable $error ) {
	$failures[] = 'runtime warning/error: ' . $error->getMessage();
}
restore_error_handler();

// Bootstrap and ownership contracts: the recovery file must actually load,
// while the explicit noindex list lives only in inc/seo.php.
$functions_source  = (string) file_get_contents( $root . '/functions.php' );
$seo_source        = (string) file_get_contents( $root . '/inc/seo.php' );
$regression_source = (string) file_get_contents( $root . '/inc/seo-regressions.php' );
$assert_true( false !== strpos( $functions_source, "/inc/seo-regressions.php'" ), 'functions.php must require inc/seo-regressions.php' );
$assert_true( false !== strpos( $seo_source, 'function teznevise_explicit_noindex_slugs()' ), 'inc/seo.php must own explicit noindex slugs' );
$assert_true( substr_count( $seo_source, 'teznevise_explicit_noindex_slugs()' ) >= 3, 'robots and sitemap must share explicit noindex source' );
$assert_true( false === strpos( $regression_source, "'corporate-social-responsibility'" ), 'seo-regressions.php must not duplicate the noindex slug list' );
$assert_true( false !== strpos( $regression_source, 'wp_safe_redirect( $target, 301' ), 'runtime recovery must issue HTTP 301' );

if ( $failures ) {
	fwrite( STDERR, "SEO regression contract failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "SEO regression contract passed\n";
