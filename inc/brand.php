<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * License: GPL-2.0-or-later
 *
 * Brand constants and footer credit.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_AUTHOR', 'MAZ//ID (Maziyar)' );
define( 'TEZNEVISE_BRAND_URL', 'https://github.com/maziyarid/M-Z' );
define( 'TEZNEVISE_AUTHOR_URL', 'https://maziyarid.com/' );

/**
 * Optional subtle credit in admin footer on theme screens.
 *
 * @param string $text Text.
 * @return string
 */
function teznevise_admin_footer_credit( $text ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && false !== strpos( (string) $screen->id, 'teznevise' ) ) {
		return 'Teznevise · ' . TEZNEVISE_AUTHOR . ' · <a href="' . esc_url( TEZNEVISE_BRAND_URL ) . '" target="_blank" rel="noopener">MΛZ</a>';
	}
	return $text;
}
add_filter( 'admin_footer_text', 'teznevise_admin_footer_credit' );
