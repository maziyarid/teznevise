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

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '/' ) {
		$path = (string) $path;
		if ( '' === $path ) {
			return 'https://teznevise.ir';
		}
		return 'https://teznevise.ir/' . ltrim( $path, '/' );
	}
}
if ( ! function_exists( 'add_action' ) ) {
	function add_action() { return true; }
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() { return true; }
}
if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		public $post_name = '';
		public $post_status = 'publish';
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

// The canonical URL helper must never convert action/absolute URLs into local paths.
$assert_same( 'tel:09302822091', teznevise_url( 'tel:09302822091' ), 'tel URL preservation' );
$assert_same( 'mailto:seo@example.com', teznevise_url( 'mailto:seo@example.com' ), 'mailto URL preservation' );
$assert_same( 'https://example.com/path', teznevise_url( 'https://example.com/path' ), 'absolute HTTPS preservation' );
$assert_same( 'https://teznevise.ir/proposal/', teznevise_url( '/proposal/' ), 'relative internal URL resolution' );

// The defensive home_url filter must repair stale malformed values found by the live crawl.
$assert_same(
	'https://teznevise.ir/inquiry/',
	teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/https:/teznevise.ir/inquiry/', 'https:/teznevise.ir/inquiry/', 'https', 1 ),
	'malformed same-domain HTTPS recovery'
);
$assert_same(
	'https://wa.me/989302822091',
	teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/https:/wa.me/989302822091', 'https:/wa.me/989302822091', 'https', 1 ),
	'malformed external HTTPS recovery'
);
$assert_same(
	'tel:09302822091',
	teznevise_seo_recover_malformed_home_url( 'https://teznevise.ir/tel:09302822091', 'tel:09302822091', 'https', 1 ),
	'legacy tel home_url recovery'
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

$self_canonical_slugs = teznevise_seo_regression_self_canonical_slugs();
if ( ! in_array( 'gams', $self_canonical_slugs, true ) ) {
	$failures[] = 'stale canonical recovery missing current GAMS slug';
}

$noindex = teznevise_seo_regression_noindex_slugs();
foreach ( array( 'join-us', 'corporate-social-responsibility', 'fair-use-policy', 'revision-policy', 'originality-guarantee', 'service-commitments', 'achievements' ) as $slug ) {
	if ( ! in_array( $slug, $noindex, true ) ) {
		$failures[] = 'noindex/sitemap alignment missing slug: ' . $slug;
	}
}

if ( $failures ) {
	fwrite( STDERR, "SEO regression contract failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "SEO regression contract passed\n";
