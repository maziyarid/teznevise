<?php
/**
 * Classic-editor HTML recovered from the 2026-08-21 WordPress export.
 *
 * Used when a live page still only contains a leftover shortcode.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Path to the exported classic-content document.
 *
 * @return string
 */
function teznevise_wxr_classic_path() {
	return TEZNEVISE_DIR . '/inc/wxr-classic-content.json';
}

/**
 * Slug => array{ title, content } from the WXR export.
 *
 * @return array<string,array{title:string,content:string}>
 */
function teznevise_wxr_classic_document() {
	static $doc = null;
	if ( null !== $doc ) {
		return $doc;
	}
	$doc  = array();
	$path = teznevise_wxr_classic_path();
	if ( ! is_readable( $path ) ) {
		return $doc;
	}
	$raw  = file_get_contents( $path );
	$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
	if ( ! is_array( $data ) ) {
		return $doc;
	}
	$doc = $data;
	return $doc;
}

/**
 * Classic HTML for a page slug, if the export had real copy.
 *
 * @param string $slug Page slug.
 * @return string
 */
function teznevise_wxr_classic_for_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( '' === $slug ) {
		return '';
	}
	$doc = teznevise_wxr_classic_document();
	if ( empty( $doc[ $slug ]['content'] ) ) {
		return '';
	}
	$html = (string) $doc[ $slug ]['content'];
	if ( function_exists( 'strip_shortcodes' ) ) {
		$html = strip_shortcodes( $html );
	}
	if ( function_exists( 'teznevise_rewrite_tel_html' ) ) {
		$html = teznevise_rewrite_tel_html( $html );
	}
	return $html;
}

/**
 * One-time: write exported HTML into classic editor when the live page
 * still only contains a leftover shortcode.
 */
function teznevise_wxr_import_classic_editor() {
	if ( get_option( 'teznevise_wxr_classic_imported' ) ) {
		return;
	}
	if ( ! is_admin() || ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$doc = teznevise_wxr_classic_document();
	if ( ! $doc ) {
		return;
	}
	$updated = 0;
	foreach ( $doc as $slug => $row ) {
		$html = isset( $row['content'] ) ? trim( (string) $row['content'] ) : '';
		if ( '' === $html ) {
			continue;
		}
		$page = get_page_by_path( $slug );
		if ( ! $page || 'page' !== $page->post_type ) {
			continue;
		}
		$raw = trim( (string) $page->post_content );
		$is_shortcode_only = (bool) preg_match( '/^\[[a-zA-Z][^\]]{0,120}\]\s*$/', wp_strip_all_tags( $raw ) );
		if ( '' !== $raw && ! $is_shortcode_only ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => (int) $page->ID,
				'post_content' => $html,
			)
		);
		++$updated;
	}
	update_option( 'teznevise_wxr_classic_imported', $updated, false );
}
add_action( 'admin_init', 'teznevise_wxr_import_classic_editor', 40 );
