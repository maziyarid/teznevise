<?php
/**
 * Low-risk application security defaults.
 *
 * Web-server controls such as HSTS and a production CSP remain deployment
 * responsibilities; they must not be guessed by a theme.
 *
 * This module restricts REST user routes, public author archives, and the
 * `?author=` canonical redirect. That is not a complete anti-enumeration
 * guarantee if a plugin later re-exposes user identity.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Hide REST user routes from unauthenticated/non-privileged visitors. */
function teznevise_restrict_rest_user_routes( $endpoints ) {
	if ( current_user_can( 'list_users' ) ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'teznevise_restrict_rest_user_routes' );

/** Disable the core user sitemap so login slugs are not listed publicly. */
function teznevise_disable_user_sitemap( $provider, $name ) {
	if ( 'users' === $name ) {
		return false;
	}
	return $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'teznevise_disable_user_sitemap', 10, 2 );

/**
 * Redirect public author archives and `?author=` probes.
 *
 * Administrators who can list users keep the normal author templates.
 */
function teznevise_block_public_author_enumeration() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( current_user_can( 'list_users' ) ) {
		return;
	}
	$author_query = isset( $_GET['author'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $author_query || is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'teznevise_block_public_author_enumeration', 0 );

/** Disable the legacy XML-RPC application interface. */
add_filter( 'xmlrpc_enabled', '__return_false' );

/** Avoid confirming whether a username or email exists at login. */
function teznevise_generic_login_error() {
	return __( 'اطلاعات ورود صحیح نیست.', 'teznevise' );
}
add_filter( 'login_errors', 'teznevise_generic_login_error' );

/** Add browser hardening headers that are safe at theme scope. */
function teznevise_send_security_headers() {
	if ( headers_sent() ) {
		return;
	}
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
	header( 'X-Frame-Options: SAMEORIGIN' );
}
add_action( 'send_headers', 'teznevise_send_security_headers' );
