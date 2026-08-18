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
		'home' => home_url( '/' ), 'blog' => home_url( '/blog/' ), 'services' => home_url( '/service-thesis/' ), 'about' => home_url( '/about/' ), 'contact' => home_url( '/contact/' ), 'tools' => home_url( '/tools/' ), 'team' => home_url( '/team/' ), 'privacy' => home_url( '/privacy/' ), 'sitemap' => home_url( '/sitemap/' ),
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
