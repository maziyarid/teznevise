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
	header( 'X-Content-Type-Options: nosniff' );
}
add_action( 'send_headers', 'teznevise_send_security_headers' );

/** Hide the WordPress generator fingerprint from markup and feeds. */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
add_filter( 'xmlrpc_enabled', '__return_false' );

/** Do not advertise the exact WordPress version on enqueued core assets. */
function teznevise_hide_asset_version( $src ) {
	if ( is_admin() || ! is_string( $src ) || false === strpos( $src, 'ver=' ) ) {
		return $src;
	}
	if ( false !== strpos( $src, 'wp-includes' ) || false !== strpos( $src, 'wp-admin' ) ) {
		return remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'teznevise_hide_asset_version', 15 );
add_filter( 'script_loader_src', 'teznevise_hide_asset_version', 15 );

/** Subscribers never land in wp-admin; they use the custom /account/ dashboard. */
function teznevise_redirect_subscribers_from_admin() {
	if ( ! is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_options' ) ) {
		return;
	}
	wp_safe_redirect( home_url( '/account/' ) );
	exit;
}
add_action( 'admin_init', 'teznevise_redirect_subscribers_from_admin', 0 );

/** Hide the core admin bar for customers; staff keep it. */
function teznevise_hide_admin_bar_for_customers( $show ) {
	if ( is_admin() ) {
		return $show;
	}
	if ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_options' ) ) {
		return $show;
	}
	return false;
}
add_filter( 'show_admin_bar', 'teznevise_hide_admin_bar_for_customers' );

/** Send customers to the front-end account, never stock wp-login, unless staff. */
function teznevise_customer_login_url( $login_url, $redirect = '', $force_reauth = false ) {
	unset( $force_reauth );
	if ( is_string( $redirect ) && false !== strpos( $redirect, 'wp-admin' ) ) {
		return $login_url;
	}
	return home_url( '/account/' );
}
add_filter( 'login_url', 'teznevise_customer_login_url', 10, 3 );

function teznevise_block_stock_login_for_customers() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		return;
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $action, array( 'lostpassword', 'retrievepassword' ), true ) ) {
		wp_safe_redirect( home_url( '/account/?view=lost' ) );
		exit;
	}
	if ( in_array( $action, array( 'resetpass', 'rp' ), true ) ) {
		$key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$login = isset( $_GET['login'] ) ? sanitize_user( wp_unslash( $_GET['login'] ) ) : '';
		wp_safe_redirect(
			add_query_arg(
				array(
					'view'  => 'reset',
					'key'   => $key,
					'login' => $login,
				),
				home_url( '/account/' )
			)
		);
		exit;
	}
	if ( in_array( $action, array( 'logout', 'confirmaction' ), true ) ) {
		return;
	}
	if ( ! empty( $_GET['staff'] ) || ! empty( $_REQUEST['interim-login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$redirect_to = isset( $_REQUEST['redirect_to'] ) ? (string) wp_unslash( $_REQUEST['redirect_to'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( false !== strpos( $redirect_to, 'wp-admin' ) ) {
		return;
	}
	wp_safe_redirect( home_url( '/account/' ) );
	exit;
}
add_action( 'login_init', 'teznevise_block_stock_login_for_customers', 1 );

/** Brand the login screen so it does not look like stock WordPress. */
function teznevise_login_brand() {
	$logo = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
	echo '<style>
		body.login { background: #f4fbf8; }
		.login h1 a { background-size: contain; width: 220px; height: 64px; }
		.login form { border-radius: 16px; border: 0; box-shadow: 0 16px 40px rgba(9,40,32,.08); }
		.login #nav, .login #backtoblog { text-align: center; }
	</style>';
	if ( $logo ) {
		echo '<style>.login h1 a { background-image: url(' . esc_url( $logo ) . ') !important; }</style>';
	}
}
add_action( 'login_enqueue_scripts', 'teznevise_login_brand' );

function teznevise_login_logo_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'teznevise_login_logo_url' );

function teznevise_login_logo_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'teznevise_login_logo_title' );

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
