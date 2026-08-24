<?php
/**
 * Enamad-safe public copy: consulting and tools, not ghostwriting.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rewrite service phrases so they always include مشاوره and drop ghostwriting claims.
 *
 * @param string $text Raw text.
 * @return string
 */
function teznevise_consult_copy( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return $text;
	}

	$protected = array(
		'مشاوره انجام پایان‌نامه' => "\x01",
		'مشاوره انجام پایان نامه' => "\x02",
		'مشاوره انجام پروپوزال'   => "\x03",
		'مشاوره انجام رساله'      => "\x04",
		'مشاوره نگارش مقاله'      => "\x05",
		'مشاوره پروژه دانشجویی'   => "\x06",
		'مشاوره پایان‌نامه'       => "\x07",
		'مشاوره پایان نامه'       => "\x08",
	);
	$text = str_replace( array_keys( $protected ), array_values( $protected ), $text );

	$pairs = array(
		'آیا استاد راهنما متوجه می‌شود که پروپوزال را خودم ننوشتم؟' => 'در مشاوره پروپوزال نقش دانشجو چیست؟',
		'پروپوزال را خودم ننوشتم' => 'چگونه با مشاور پروپوزال کار می‌کنم',
		'توسط اساتید و فارغ‌التحصیلان دکتری در رشته خودتان نوشته می‌شوند' => 'با راهنمایی مشاوران هم‌رشته و بازبینی علمی پیش می‌روند',
		'توسط فارغ‌التحصیلان دکتری هم‌رشته نوشته می‌شوند' => 'با مشاوره متخصص هم‌رشته تدوین می‌شوند',
		'پژوهشگران ما برای پروپوزال‌هایی که می‌نویسند، پایان نامه نیز انجام می‌دهند' => 'مشاوران ما پس از پروپوزال، مسیر مشاوره پایان‌نامه را نیز ادامه می‌دهند',
		'پایان نامه نیز انجام می‌دهند' => 'مشاوره پایان‌نامه را نیز ارائه می‌دهند',
		'از ابتدا به انگلیسی برایتان می‌نویسیم' => 'از ابتدا به انگلیسی همراهی‌تان می‌کنیم',
		'برایتان می‌نویسیم' => 'در تدوین آن همراهی‌تان می‌کنیم',
		'را می‌نویسیم' => 'را با راهنمایی مشاور تدوین می‌کنیم',
		'می‌نویسیم' => 'همراهی می‌کنیم',
		'انجام می‌دهید' => 'مشاوره می‌دهید',
		'سفارش تحلیل آماری' => 'مشاوره تحلیل آماری',
		'ثبت سفارش پروژه' => 'ثبت درخواست مشاوره',
		'ثبت سفارش' => 'ثبت درخواست',
		'خدمات نگارش پایان‌نامه' => 'خدمات مشاوره پایان‌نامه',
		'خدمات نگارش پایان نامه' => 'خدمات مشاوره پایان نامه',
		'سفارش مشاوره انجام پایان نامه' => 'درخواست مشاوره انجام پایان‌نامه',
		'فصل‌ها به‌صورت مرحله‌ای نوشته می‌شوند' => 'فصل‌ها به‌صورت مرحله‌ای با راهنمایی مشاور تدوین می‌شوند',
		'فصل‌ها مرحله‌ای نوشته می‌شوند' => 'فصل‌ها مرحله‌ای با راهنمایی مشاور تدوین می‌شوند',
		'سفارش پایان‌نامه' => 'مشاوره پایان‌نامه',
		'سفارش پایان نامه' => 'مشاوره پایان نامه',
		'سفارش پروپوزال' => 'مشاوره پروپوزال',
		'سفارش مقاله' => 'مشاوره مقاله',
		'انجام پایان‌نامه' => 'مشاوره انجام پایان‌نامه',
		'انجام پایان نامه' => 'مشاوره انجام پایان نامه',
		'انجام پروپوزال' => 'مشاوره انجام پروپوزال',
		'انجام رساله' => 'مشاوره انجام رساله',
		'انجام مقاله' => 'مشاوره نگارش مقاله',
		'انجام پروژه دانشجویی' => 'مشاوره پروژه دانشجویی',
	);
	$text = str_replace( array_keys( $pairs ), array_values( $pairs ), $text );

	return str_replace( array_values( $protected ), array_keys( $protected ), $text );
}

/**
 * Recursively rewrite strings in arrays (builder sections, page fields).
 *
 * @param mixed $data Text or nested array.
 * @return mixed
 */
function teznevise_consult_copy_deep( $data ) {
	if ( is_string( $data ) ) {
		return teznevise_consult_copy( $data );
	}
	if ( is_array( $data ) ) {
		foreach ( $data as $k => $v ) {
			$data[ $k ] = teznevise_consult_copy_deep( $v );
		}
	}
	return $data;
}

function teznevise_consult_copy_filter( $text ) {
	return teznevise_consult_copy( $text );
}

add_filter( 'the_title', 'teznevise_consult_copy_filter', 9 );
add_filter( 'the_content', 'teznevise_consult_copy_filter', 9 );
add_filter( 'the_excerpt', 'teznevise_consult_copy_filter', 9 );
add_filter( 'widget_title', 'teznevise_consult_copy_filter', 9 );
add_filter( 'widget_text', 'teznevise_consult_copy_filter', 9 );
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
