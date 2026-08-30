<?php
/**
 * SEO regression recovery discovered during the Teznevise 2.0 pre-merge audit.
 *
 * These compatibility rules protect search equity while legacy URLs are
 * consolidated into the current information architecture. Keep this file
 * narrow: canonical SEO behaviour remains owned by inc/seo.php.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy URLs that still carry Google impressions/indexing signals but no
 * longer resolve in the current site structure.
 *
 * @return array<string,string> Untrailingslashed legacy path => canonical path.
 */
function teznevise_seo_regression_redirect_map() {
	return array(
		'/article'                                             => '/service-article/',
		'/article/isi'                                         => '/service-article/',
		'/article/academic'                                    => '/service-article/',
		'/project/assignments'                                 => '/service-project/',
		'/project/assignments/mechanical-civil'                => '/service-project/',
		'/project/assignments/mechanical-civil/thermodynamics' => '/service-project/',
		'/project/engineering/electrical/gams'                 => '/gams/',
		'/thesis/engineering/electrical'                       => '/thesis/engineering/',
		'/thesis/other-fields'                                 => '/thesis/',
		'/thesis/other-fields/space-science'                   => '/thesis/pure-science/',
		'/how-to-write-research-proposal'                      => '/proposal/',
	);
}

/**
 * Preserve accumulated search signals by 301ing broken legacy routes to the
 * closest current published equivalent.
 */
function teznevise_seo_regression_redirects() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
	$path = untrailingslashit( $path );
	$map  = teznevise_seo_regression_redirect_map();
	if ( ! isset( $map[ $path ] ) ) {
		return;
	}

	$target_path = $map[ $path ];
	$page        = get_page_by_path( trim( $target_path, '/' ) );
	$target      = ( $page instanceof WP_Post && 'publish' === $page->post_status )
		? get_permalink( $page )
		: home_url( $target_path );

	if ( ! $target ) {
		return;
	}
	$target_path_resolved = untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
	if ( $target_path_resolved === $path ) {
		return;
	}

	wp_safe_redirect( $target, 301, 'Teznevise SEO recovery' );
	exit;
}
add_action( 'template_redirect', 'teznevise_seo_regression_redirects', -50 );

/**
 * Current pages whose stored Yoast canonical still points at a removed route.
 *
 * @return string[]
 */
function teznevise_seo_regression_self_canonical_slugs() {
	return array( 'gams' );
}

/**
 * Reverse stale canonicals after a route migration. The old route is 301ed to
 * the new page, so the new page must self-canonicalise rather than point back
 * to the removed URL.
 *
 * @param string $canonical Yoast canonical URL.
 * @return string
 */
function teznevise_seo_regression_canonical( $canonical ) {
	if ( ! is_page() ) {
		return $canonical;
	}
	$post_id = get_queried_object_id();
	$slug    = (string) get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, teznevise_seo_regression_self_canonical_slugs(), true ) ) {
		return $canonical;
	}
	$self = get_permalink( $post_id );
	return $self ? $self : $canonical;
}
add_filter( 'wpseo_canonical', 'teznevise_seo_regression_canonical', 40 );

/**
 * Noindex slugs declared by teznevise_should_noindex(). The legacy Yoast
 * sitemap filter historically covered only a subset, which allowed noindex
 * pages to remain in page-sitemap.xml and sent mixed indexation signals.
 *
 * @return string[]
 */
function teznevise_seo_regression_noindex_slugs() {
	return array(
		'corporate-social-responsibility',
		'account',
		'join-us',
		'careers',
		'fair-use-policy',
		'revision-policy',
		'originality-guarantee',
		'service-commitments',
		'achievements',
	);
}

/**
 * Keep Yoast sitemap membership aligned with the theme's explicit noindex
 * policy. This runs after the canonical inc/seo.php sitemap filter.
 *
 * @param array|false $url    Sitemap entry.
 * @param string      $type   Object type.
 * @param object      $object Post/term object.
 * @return array|false
 */
function teznevise_seo_regression_sitemap_entry( $url, $type, $object ) {
	unset( $type );
	if ( false === $url || ! is_array( $url ) ) {
		return $url;
	}
	if ( $object instanceof WP_Post && in_array( $object->post_name, teznevise_seo_regression_noindex_slugs(), true ) ) {
		return false;
	}
	return $url;
}
add_filter( 'wpseo_sitemap_entry', 'teznevise_seo_regression_sitemap_entry', 30, 3 );

/**
 * Repair legacy malformed URL values before WordPress turns them into local
 * paths (for example https:/teznevise.ir/... or tel:... passed to home_url()).
 * Current teznevise_url() already preserves safe schemes; this is a defensive
 * compatibility layer for stale builder/database values and older callers.
 *
 * @param string $url         URL generated by WordPress.
 * @param string $path        Original path passed to home_url().
 * @param string $orig_scheme Requested scheme.
 * @param int    $blog_id     Site ID.
 * @return string
 */
function teznevise_seo_recover_malformed_home_url( $url, $path, $orig_scheme, $blog_id ) {
	unset( $orig_scheme, $blog_id );
	$path = trim( (string) $path );
	if ( preg_match( '#^(?:tel|mailto|sms):#i', $path ) ) {
		return $path;
	}
	if ( preg_match( '#^(https?):/([^/].*)$#i', $path, $matches ) ) {
		return strtolower( $matches[1] ) . '://' . $matches[2];
	}
	return $url;
}
add_filter( 'home_url', 'teznevise_seo_recover_malformed_home_url', 99, 4 );
