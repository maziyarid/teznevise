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
		'primary' => array( 'home', 'services', 'tools', 'blog', 'about' ),
		'mobile'  => array( 'home', 'services', 'tools', 'blog', 'team', 'about', 'contact' ),
		'bottom'  => array( 'home', 'tools', 'blog', 'contact' ),
		'footer'  => array( 'blog', 'about', 'team', 'contact', 'privacy', 'sitemap' ),
	);
	$items = isset( $locations[ $args['theme_location'] ] ) ? $locations[ $args['theme_location'] ] : $locations['primary'];
	$labels = array(
		'home' => __( 'خانه', 'teznevise' ), 'blog' => __( 'بلاگ', 'teznevise' ), 'services' => __( 'خدمات', 'teznevise' ), 'about' => __( 'درباره ما', 'teznevise' ), 'contact' => __( 'تماس با ما', 'teznevise' ), 'tools' => __( 'ابزارها', 'teznevise' ), 'team' => __( 'تیم پژوهشگران', 'teznevise' ), 'privacy' => __( 'حریم خصوصی', 'teznevise' ), 'sitemap' => __( 'نقشه سایت', 'teznevise' ),
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

/**
 * CSS class fragment when the current request matches a chrome nav target.
 * Used by the HTML-parity header / mobile / bottom nav (not wp_nav_menu).
 *
 * @param string $which home|blog|services|tools|about|contact|team|inquiry|thesis|proposal|statistics.
 * @return string ' active' or empty.
 */
function teznevise_nav_current_class( $which ) {
	$on = false;
	switch ( $which ) {
		case 'home':
			$on = is_front_page();
			break;
		case 'blog':
			$on = ( is_home() && ! is_front_page() ) || is_singular( 'post' ) || is_category() || is_tag();
			break;
		case 'services':
			$on = is_page( array( 'service-thesis', 'service-proposal', 'service-statistics', 'service-simulation', 'services' ) );
			break;
		case 'tools':
			$on = is_page( array( 'tools' ) ) || is_page_template( 'page-tool.php' ) || is_page_template( 'page-tools.php' ) || is_post_type_archive( 'download' ) || is_singular( 'download' );
			break;
		case 'about':
			$on = is_page( 'about' );
			break;
		case 'contact':
			$on = is_page( array( 'contact', 'contact-us' ) );
			break;
		case 'team':
			$on = is_page( 'team' );
			break;
		case 'inquiry':
			$on = is_page( array( 'inquiry', 'order' ) );
			break;
		case 'thesis':
			$on = is_page( 'service-thesis' );
			break;
		case 'proposal':
			$on = is_page( 'service-proposal' );
			break;
		case 'statistics':
			$on = is_page( array( 'service-statistics', 'statistics' ) );
			break;
		default:
			$on = is_page( $which );
	}
	return $on ? ' active' : '';
}
