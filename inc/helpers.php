<?php
/**
 * Shared theme helpers.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function teznevise_fallback_menu( $args = array() ) {
	$defaults = array(
		'theme_location' => '',
		'container'      => false,
		'fallback_cb'    => false,
		'echo'           => false,
		'menu_class'     => 'nav-links',
	);
	$args = wp_parse_args( $args, $defaults );
	$locations = array(
		'primary' => array( 'home', 'blog', 'services', 'about', 'contact' ),
		'mobile'  => array( 'home', 'services', 'tools', 'blog', 'team', 'about', 'contact' ),
		'bottom'  => array( 'home', 'services', 'tools', 'blog', 'contact' ),
		'footer'  => array( 'blog', 'about', 'team', 'contact', 'privacy', 'sitemap' ),
	);
	$items = isset( $locations[ $args['theme_location'] ] ) ? $locations[ $args['theme_location'] ] : $locations['primary'];
	$labels = array(
		'home' => __( 'خانه', 'teznevise' ), 'blog' => __( 'بلاگ', 'teznevise' ), 'services' => __( 'خدمات', 'teznevise' ), 'about' => __( 'درباره ما', 'teznevise' ), 'contact' => __( 'تماس با ما', 'teznevise' ), 'tools' => __( 'ابزارهای آنلاین', 'teznevise' ), 'team' => __( 'تیم پژوهشگران', 'teznevise' ), 'privacy' => __( 'حریم خصوصی', 'teznevise' ), 'sitemap' => __( 'نقشه سایت', 'teznevise' ),
	);
	$urls = array(
		'home'     => home_url( '/' ),
		'blog'     => teznevise_posts_url(),
		'services' => home_url( '/service-thesis/' ),
		'about'    => home_url( '/about/' ),
		'contact'  => home_url( '/contact/' ),
		'tools'    => home_url( '/tools/' ),
		'team'     => home_url( '/team/' ),
		'privacy'  => home_url( '/privacy/' ),
		'sitemap'  => home_url( '/sitemap/' ),
	);
	$html = '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	foreach ( $items as $key ) {
		$html .= '<li class="menu-item"><a href="' . esc_url( $urls[ $key ] ) . '">' . esc_html( $labels[ $key ] ) . '</a></li>';
	}
	return $html . '</ul>';
}

/**
 * Contact / site-info value (phone, WhatsApp, email, …).
 *
 * Used by header.php, footer.php, template-parts/fab.php,
 * template-parts/mobile-nav.php and several page templates.
 * Values come from the Customizer with Persian defaults as fallback.
 *
 * @param string $key Setting key (see teznevise_content_defaults()).
 * @return string
 */
function teznevise_get_contact( $key ) {
	return teznevise_mod( $key );
}

/**
 * Site logo URL for header and footer.
 *
 * @return string
 */
function teznevise_logo_url() {
	if ( has_custom_logo() ) {
		$url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

/**
 * Icon class for a bottom-nav destination.
 *
 * @param string $url   Item URL.
 * @param string $label Item label.
 * @return string
 */
function teznevise_bottom_icon( $url, $label ) {
	$u = strtolower( (string) $url );
	$l = (string) $label;
	if ( 0 === strpos( $u, 'tel:' ) || false !== strpos( $l, 'تماس' ) ) {
		return 'fa-solid fa-phone';
	}
	if ( false !== strpos( $u, '/tools' ) || false !== strpos( $l, 'ابزار' ) ) {
		return 'fa-solid fa-calculator';
	}
	if ( false !== strpos( $u, '/blog' ) || false !== strpos( $l, 'بلاگ' ) ) {
		return 'fa-solid fa-book-open';
	}
	if ( false !== strpos( $u, '/account' ) || false !== strpos( $l, 'حساب' ) || false !== strpos( $l, 'ورود' ) ) {
		return 'fa-solid fa-user';
	}
	if ( false !== strpos( $u, '/wallet' ) || false !== strpos( $l, 'کیف' ) ) {
		return 'fa-solid fa-wallet';
	}
	if ( trailingslashit( $u ) === trailingslashit( home_url( '/' ) ) || 'خانه' === $l ) {
		return 'fa-solid fa-house';
	}
	return 'fa-solid fa-layer-group';
}

/**
 * Posts-index URL: configured page_for_posts, then a `blog` page, then home.
 *
 * @return string
 */
function teznevise_posts_url() {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id > 0 ) {
		$url = get_permalink( $id );
		if ( $url ) {
			return $url;
		}
	}
	$page = get_page_by_path( 'blog' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return home_url( '/' );
}

/**
 * Permalink for a seeded page slug, with a path fallback.
 *
 * @param string $slug          Page slug.
 * @param string $fallback_path Optional path if the page does not exist.
 * @return string
 */
function teznevise_page_url( $slug, $fallback_path = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		$url = get_permalink( $page );
		if ( $url ) {
			return $url;
		}
	}
	$path = $fallback_path ? $fallback_path : '/' . trim( (string) $slug, '/' ) . '/';
	return home_url( $path );
}

