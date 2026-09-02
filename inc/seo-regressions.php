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
 * Add the original request query to a recovery target.
 *
 * @param string $target       Target URL.
 * @param string $query_string Raw query string.
 * @return string
 */
function teznevise_seo_regression_append_query( $target, $query_string ) {
	$query_string = str_replace( array( "\r", "\n" ), '', ltrim( (string) $query_string, '?' ) );
	if ( '' === $query_string ) {
		return $target;
	}
	return $target . ( false === strpos( $target, '?' ) ? '?' : '&' ) . $query_string;
}

/**
 * Resolve a legacy route to its current same-site destination.
 *
 * @param string $request_uri  Request URI.
 * @param string $query_string Raw query string.
 * @return string|false
 */
function teznevise_seo_regression_resolve_target( $request_uri, $query_string = '' ) {
	$path = (string) wp_parse_url( wp_unslash( (string) $request_uri ), PHP_URL_PATH );
	$path = untrailingslashit( $path );
	$map  = teznevise_seo_regression_redirect_map();
	if ( ! isset( $map[ $path ] ) ) {
		return false;
	}

	// These aliases exist only to recover removed routes. A later published
	// page at the old path must win instead of being redirected away.
	$legacy_page = get_page_by_path( trim( $path, '/' ) );
	if ( $legacy_page instanceof WP_Post && 'publish' === $legacy_page->post_status ) {
		return false;
	}

	$target_path = $map[ $path ];
	$page        = get_page_by_path( trim( $target_path, '/' ) );
	$target      = ( $page instanceof WP_Post && 'publish' === $page->post_status )
		? get_permalink( $page )
		: home_url( $target_path );
	if ( ! $target ) {
		return false;
	}

	$target_path_resolved = untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
	if ( $target_path_resolved === $path ) {
		return false;
	}
	return teznevise_seo_regression_append_query( $target, $query_string );
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

	$target = teznevise_seo_regression_resolve_target(
		wp_unslash( $_SERVER['REQUEST_URI'] ),
		isset( $_SERVER['QUERY_STRING'] ) ? wp_unslash( $_SERVER['QUERY_STRING'] ) : ''
	);
	if ( ! $target ) {
		return;
	}

	wp_safe_redirect( $target, 301, 'Teznevise SEO recovery' );
	exit;
}
add_action( 'after_setup_theme', 'teznevise_seo_regression_redirects', 1 );
add_action( 'init', 'teznevise_seo_regression_redirects', -998 );
add_action( 'template_redirect', 'teznevise_seo_regression_redirects', -50 );

/**
 * Reverse stale canonicals after a route migration. The old route is 301ed to
 * the new page, so the new page must self-canonicalise rather than point back
 * to the removed URL.
 *
 * @param string $canonical Yoast canonical URL.
 * @return string
 */
function teznevise_seo_regression_canonical( $canonical ) {
	if ( ! is_page() || ! $canonical ) {
		return $canonical;
	}
	$post_id = get_queried_object_id();
	$self    = get_permalink( $post_id );
	if ( ! $self ) {
		return $canonical;
	}
	$old_path  = untrailingslashit( (string) wp_parse_url( $canonical, PHP_URL_PATH ) );
	$self_path = untrailingslashit( (string) wp_parse_url( $self, PHP_URL_PATH ) );
	$map       = teznevise_seo_regression_redirect_map();
	if ( ! isset( $map[ $old_path ] ) ) {
		return $canonical;
	}
	$mapped_path = untrailingslashit( (string) wp_parse_url( home_url( $map[ $old_path ] ), PHP_URL_PATH ) );
	return $mapped_path === $self_path ? $self : $canonical;
}
add_filter( 'wpseo_canonical', 'teznevise_seo_regression_canonical', 40 );

/**
 * Repair legacy malformed URL values before WordPress turns them into local
 * paths. Action schemes are restored directly. A malformed single-slash HTTP
 * URL is repaired only when it points back to this WordPress site's host.
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
	if ( preg_match( '~^(?:tel|mailto|sms):~i', $path ) ) {
		return $path;
	}
	if ( preg_match( '~^(https?):/([^/].*)$~i', $path, $matches ) ) {
		$candidate      = strtolower( $matches[1] ) . '://' . $matches[2];
		$candidate_host = strtolower( (string) wp_parse_url( $candidate, PHP_URL_HOST ) );
		$home_host      = strtolower( (string) wp_parse_url( (string) get_option( 'home' ), PHP_URL_HOST ) );
		if ( $candidate_host && $home_host && $candidate_host === $home_host ) {
			return $candidate;
		}
	}
	return $url;
}
add_filter( 'home_url', 'teznevise_seo_recover_malformed_home_url', 99, 4 );
