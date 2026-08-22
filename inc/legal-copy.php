<?php
/**
 * Enamad-safe public copy: consulting, not ghostwriting.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rewrite service phrases so they always include مشاوره.
 *
 * @param string $text Raw text.
 * @return string
 */
function teznevise_consult_copy( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return $text;
	}
	$text = str_replace(
		array(
			'مشاوره انجام پایان‌نامه',
			'مشاوره انجام پایان نامه',
			'مشاوره انجام پروپوزال',
			'مشاوره انجام رساله',
		),
		array( "\x01", "\x02", "\x03", "\x04" ),
		$text
	);
	$text = str_replace( 'انجام پایان‌نامه', 'مشاوره انجام پایان‌نامه', $text );
	$text = str_replace( 'انجام پایان نامه', 'مشاوره انجام پایان نامه', $text );
	$text = str_replace( 'انجام پروپوزال', 'مشاوره انجام پروپوزال', $text );
	$text = str_replace( 'انجام رساله', 'مشاوره انجام رساله', $text );
	return str_replace(
		array( "\x01", "\x02", "\x03", "\x04" ),
		array(
			'مشاوره انجام پایان‌نامه',
			'مشاوره انجام پایان نامه',
			'مشاوره انجام پروپوزال',
			'مشاوره انجام رساله',
		),
		$text
	);
}

function teznevise_consult_copy_filter( $text ) {
	return teznevise_consult_copy( $text );
}

add_filter( 'the_title', 'teznevise_consult_copy_filter', 9 );
add_filter( 'the_content', 'teznevise_consult_copy_filter', 9 );
add_filter( 'widget_title', 'teznevise_consult_copy_filter', 9 );
add_filter( 'nav_menu_item_title', 'teznevise_consult_copy_filter', 9, 1 );
add_filter(
	'wp_title',
	static function ( $title ) {
		return teznevise_consult_copy( $title );
	},
	9
);
add_filter(
	'document_title_parts',
	static function ( $parts ) {
		if ( is_array( $parts ) ) {
			foreach ( $parts as $k => $v ) {
				$parts[ $k ] = teznevise_consult_copy( $v );
			}
		}
		return $parts;
	}
);
