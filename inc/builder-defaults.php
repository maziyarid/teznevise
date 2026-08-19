<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 *
 * HTML → Flexible Page Builder default sections.
 * Source of truth: inc/builder-defaults.json (extracted from teznevise_work/).
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Absolute path to the defaults JSON file.
 *
 * @return string
 */
function teznevise_builder_defaults_path() {
	return TEZNEVISE_DIR . '/inc/builder-defaults.json';
}

/**
 * Load and cache the defaults document.
 *
 * @return array{meta:array,pages:array,excluded:array}
 */
function teznevise_builder_defaults_document() {
	static $doc = null;
	if ( null !== $doc ) {
		return $doc;
	}

	$path = teznevise_builder_defaults_path();
	$doc  = array(
		'meta'     => array(),
		'pages'    => array(),
		'excluded' => array(),
	);

	if ( ! is_readable( $path ) ) {
		return $doc;
	}

	$raw  = file_get_contents( $path );
	$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
	if ( ! is_array( $data ) ) {
		return $doc;
	}

	$doc['meta']     = isset( $data['meta'] ) && is_array( $data['meta'] ) ? $data['meta'] : array();
	$doc['pages']    = isset( $data['pages'] ) && is_array( $data['pages'] ) ? $data['pages'] : array();
	$doc['excluded'] = isset( $data['excluded'] ) && is_array( $data['excluded'] ) ? $data['excluded'] : array();

	return $doc;
}

/**
 * Defaults entry for a conversion key (page slug, "index", or "post-sample").
 *
 * @param string $key Conversion key.
 * @return array<string,mixed>
 */
function teznevise_builder_defaults_entry( $key ) {
	$doc = teznevise_builder_defaults_document();
	$key = (string) $key;
	if ( isset( $doc['pages'][ $key ] ) && is_array( $doc['pages'][ $key ] ) ) {
		return $doc['pages'][ $key ];
	}
	return array();
}

/**
 * Sanitized builder sections for a conversion key.
 *
 * @param string $key Conversion key.
 * @return array
 */
function teznevise_builder_default_sections( $key ) {
	$entry = teznevise_builder_defaults_entry( $key );
	if ( ! empty( $entry['builder'] ) && ! empty( $entry['sections'] ) && is_array( $entry['sections'] ) ) {
		return teznevise_builder_sanitize_sections( $entry['sections'] );
	}
	if ( function_exists( 'teznevise_extracted_entry' ) ) {
		$extracted = teznevise_extracted_entry( $key );
		if ( ! empty( $extracted['sections'] ) && is_array( $extracted['sections'] ) ) {
			return teznevise_builder_sanitize_sections( $extracted['sections'] );
		}
	}
	return array();
}

/**
 * Resolve the conversion key for a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function teznevise_builder_conversion_key( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id && $front_id === $post_id ) {
		return 'index';
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	if ( 'post' === $post->post_type ) {
		$entry = teznevise_builder_defaults_entry( 'post-sample' );
		if ( ! empty( $entry['slug'] ) && $entry['slug'] === $post->post_name ) {
			return 'post-sample';
		}
		return '';
	}

	if ( 'page' === $post->post_type && function_exists( 'teznevise_page_path' ) ) {
		$path = teznevise_page_path( $post_id );
		if ( $path && function_exists( 'teznevise_extracted_entry' ) && teznevise_extracted_entry( $path ) ) {
			return $path;
		}
	}

	$slug = (string) $post->post_name;
	if ( teznevise_builder_defaults_entry( $slug ) ) {
		return $slug;
	}
	if ( function_exists( 'teznevise_extracted_entry' ) && teznevise_extracted_entry( $slug ) ) {
		return $slug;
	}

	return '';
}

/**
 * Whether the current (or given) post has an enabled builder section of a type.
 *
 * @param string $type    Section type.
 * @param int    $post_id Optional post ID.
 * @return bool
 */
function teznevise_builder_has_type( $type, $post_id = 0 ) {
	$type = sanitize_key( $type );
	if ( ! $type ) {
		return false;
	}
	foreach ( teznevise_builder_get_sections( $post_id ) as $section ) {
		if ( ! empty( $section['enabled'] ) && isset( $section['type'] ) && $section['type'] === $type ) {
			return true;
		}
	}
	return false;
}

/**
 * Inventory used by the conversion validator (and docs).
 *
 * @return array<string,mixed>
 */
function teznevise_builder_inventory() {
	$doc = teznevise_builder_defaults_document();
	return array(
		'html_inventory'            => isset( $doc['meta']['html_inventory'] ) ? (int) $doc['meta']['html_inventory'] : 0,
		'singular_builder_sources'  => count( $doc['pages'] ),
		'excluded_from_builder'     => count( $doc['excluded'] ),
		'pages'                     => array_keys( $doc['pages'] ),
		'excluded'                  => array_keys( $doc['excluded'] ),
	);
}
