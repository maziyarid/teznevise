<?php
/**
 * Low-risk application security defaults.
 *
 * Web-server controls such as HSTS and a production CSP remain deployment
 * responsibilities; they must not be guessed by a theme.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Hide REST user enumeration from unauthenticated/non-privileged visitors. */
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
