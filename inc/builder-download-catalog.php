<?php
/**
 * When a software_catalog builder section has no manual items, fill from
 * the download CPT so migrated shortcode pages still show a grid.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Swap software_catalog renderer to one that hydrates empty items.
 *
 * @param array $types Section types.
 * @return array
 */
function teznevise_builder_download_catalog_types( $types ) {
	if ( isset( $types['software_catalog'] ) ) {
		$types['software_catalog']['render'] = 'teznevise_builder_render_software_catalog';
	}
	return $types;
}
add_filter( 'teznevise_builder_section_types', 'teznevise_builder_download_catalog_types' );

/**
 * Render software catalog; query downloads when items are empty.
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_software_catalog( $section ) {
	if ( empty( $section['items'] ) && function_exists( 'teznevise_downloads_as_builder_items' ) ) {
		$section['items'] = teznevise_downloads_as_builder_items( 12 );
	}

	if ( empty( $section['items'] ) ) {
		return;
	}

	if ( function_exists( 'teznevise_builder_render_card_grid' ) ) {
		teznevise_builder_render_card_grid( $section );
	}
}
