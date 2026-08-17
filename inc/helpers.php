<?php
/**
 * Theme helpers.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback primary menu when no menu is assigned.
 */
function teznevise_fallback_primary_menu() {
	$items = array(
		array( 'url' => home_url( '/' ), 'label' => __( 'خانه', 'teznevise' ), 'active' => is_front_page() ),
		array( 'url' => home_url( '/service-thesis/' ), 'label' => __( 'خدمات', 'teznevise' ), 'active' => false ),
		array( 'url' => home_url( '/tools/' ), 'label' => __( 'ابزارها', 'teznevise' ), 'active' => is_page( 'tools' ) ),
		array( 'url' => home_url( '/blog/' ), 'label' => __( 'بلاگ', 'teznevise' ), 'active' => is_home() || is_singular( 'post' ) ),
		array( 'url' => home_url( '/about/' ), 'label' => __( 'درباره ما', 'teznevise' ), 'active' => is_page( 'about' ) ),
	);
	echo '<ul class="nav-links">';
	foreach ( $items as $item ) {
		$cls = $item['active'] ? ' class="active"' : '';
		printf(
			'<li><a%s href="%s">%s</a></li>',
			$cls,
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Safe theme image URL helper.
 */
function teznevise_asset( $relative_path ) {
	$resolved = teznevise_resolve_asset( $relative_path );
	return $resolved ? $resolved['url'] : ( TEZNEVISE_URI . '/' . ltrim( $relative_path, '/' ) );
}
