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
 * Classic HTML for a page path or unique slug, if the export had real copy.
 *
 * @param string $slug Page slug.
 * @return string
 */
function teznevise_wxr_classic_for_slug( $slug ) {
	$key = trim( (string) $slug, '/' );
	if ( '' === $key ) {
		return '';
	}
	$doc = teznevise_wxr_classic_document();
	$row = isset( $doc[ $key ] ) && is_array( $doc[ $key ] ) ? $doc[ $key ] : array();
	if ( empty( $row['content'] ) ) {
		return '';
	}
	$html = (string) $row['content'];
	if ( function_exists( 'strip_shortcodes' ) ) {
		$html = strip_shortcodes( $html );
	}
	if ( function_exists( 'teznevise_rewrite_tel_html' ) ) {
		$html = teznevise_rewrite_tel_html( $html );
	}
	return $html;
}

/**
 * Exported Classic Editor HTML for a concrete page. Full page paths are tried
 * first so child pages with the same leaf slug cannot be mixed up.
 *
 * @param int    $post_id Page ID.
 * @param string $path    Optional full page path.
 * @return string
 */
function teznevise_wxr_classic_for_page( $post_id, $path = '' ) {
	global $wpdb;
	$post_id = (int) $post_id;
	$path    = $path ? trim( (string) $path, '/' ) : ( function_exists( 'teznevise_page_path' ) ? teznevise_page_path( $post_id ) : '' );
	$html    = $path ? teznevise_wxr_classic_for_slug( $path ) : '';
	if ( '' !== $html ) {
		return $html;
	}
	$slug = (string) get_post_field( 'post_name', $post_id );
	$count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_name = %s",
			$slug
		)
	);
	return 1 === $count ? teznevise_wxr_classic_for_slug( $slug ) : '';
}

/**
 * Versioned, non-destructive Classic Editor bootstrap.
 *
 * Only empty/shortcode-only pages are updated. Existing human-authored copy
 * is never overwritten, including copy shorter than the display-quality
 * threshold. Functional calculator/form shortcodes are preserved
 * in private post meta and rendered by the theme outside the editor content.
 * The previous post_content is stored in `_teznevise_classic_import_backup`
 * and a revision is created before the write.
 *
 * @param int $limit  Number of pages; -1 means all.
 * @param int $offset Query offset.
 * @return array{scanned:int,updated:int,skipped:int,errors:array<int,string>}
 */
function teznevise_wxr_import_classic_editor( $limit = -1, $offset = 0 ) {
	$result = array( 'scanned' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => array() );
	$pages  = get_posts(
		array(
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'posts_per_page'   => (int) $limit,
			'offset'           => max( 0, (int) $offset ),
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'suppress_filters' => false,
		)
	);
	foreach ( $pages as $page ) {
		++$result['scanned'];
		$raw = (string) $page->post_content;
		if ( function_exists( 'teznevise_page_has_owned_editor_content' ) && teznevise_page_has_owned_editor_content( $raw ) ) {
			++$result['skipped'];
			continue;
		}
		$interactive = function_exists( 'teznevise_interactive_shortcodes_markup' ) ? teznevise_interactive_shortcodes_markup( $raw ) : '';
		$html        = teznevise_wxr_classic_for_page( (int) $page->ID );
		if ( ( ! function_exists( 'teznevise_page_has_editorial_copy' ) || ! teznevise_page_has_editorial_copy( $html ) ) && function_exists( 'teznevise_classic_html_from_extracted_page' ) ) {
			$html = teznevise_classic_html_from_extracted_page( (int) $page->ID );
		}
		if ( ( ! function_exists( 'teznevise_page_has_editorial_copy' ) || ! teznevise_page_has_editorial_copy( $html ) ) && function_exists( 'teznevise_classic_html_from_page_fields' ) ) {
			$html = teznevise_classic_html_from_page_fields( (int) $page->ID );
		}
		if ( ( ! function_exists( 'teznevise_page_has_editorial_copy' ) || ! teznevise_page_has_editorial_copy( $html ) ) && function_exists( 'teznevise_classic_html_from_builder' ) ) {
			$html = teznevise_classic_html_from_builder( (int) $page->ID );
		}
		if ( ! function_exists( 'teznevise_page_has_editorial_copy' ) || ! teznevise_page_has_editorial_copy( $html ) ) {
			++$result['skipped'];
			continue;
		}
		if ( '' !== $interactive ) {
			update_post_meta( (int) $page->ID, '_teznevise_functional_shortcodes', $interactive );
		}
		update_post_meta(
			(int) $page->ID,
			'_teznevise_classic_import_backup',
			array(
				'version' => '1.9.7',
				'time'    => time(),
				'content' => $raw,
			)
		);
		if ( function_exists( 'wp_save_post_revision' ) ) {
			wp_save_post_revision( (int) $page->ID );
		}
		$updated = wp_update_post(
			array(
				'ID'           => (int) $page->ID,
				'post_content' => wp_kses_post( $html ),
			),
			true
		);
		if ( is_wp_error( $updated ) ) {
			$result['errors'][ (int) $page->ID ] = $updated->get_error_message();
			continue;
		}
		update_post_meta( (int) $page->ID, '_teznevise_classic_seed_version', '1.9.7' );
		++$result['updated'];
	}
	return $result;
}

/** Run the import once per release for authorized administrators. */
function teznevise_maybe_import_classic_editor() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || '1.9.7' === get_option( 'teznevise_classic_import_version' ) ) {
		return;
	}
	$batch_size = 10;
	$cursor     = max( 0, (int) get_option( 'teznevise_classic_import_cursor', 0 ) );
	$result     = teznevise_wxr_import_classic_editor( $batch_size, $cursor );
	$report     = get_option( 'teznevise_classic_import_report', array() );
	if ( ! is_array( $report ) || 0 === $cursor ) {
		$report = array( 'scanned' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => array() );
	}
	foreach ( array( 'scanned', 'updated', 'skipped' ) as $key ) {
		$report[ $key ] = (int) ( $report[ $key ] ?? 0 ) + (int) $result[ $key ];
	}
	$report['errors'] = array_replace( (array) ( $report['errors'] ?? array() ), (array) $result['errors'] );
	update_option( 'teznevise_classic_import_report', $report, false );
	if ( $result['scanned'] < $batch_size ) {
		update_option( 'teznevise_classic_import_version', '1.9.7', false );
		delete_option( 'teznevise_classic_import_cursor' );
	} else {
		update_option( 'teznevise_classic_import_cursor', $cursor + $result['scanned'], false );
	}
}
add_action( 'admin_init', 'teznevise_maybe_import_classic_editor', 40 );

function teznevise_classic_import_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( '1.9.7' === get_option( 'teznevise_classic_import_version' ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'تزنویسه ۱.۹.۷ در حال انتقال محتوای قدیمی به ویرایشگر کلاسیک است. همین صفحه مدیریت را چند بار باز بگذارید تا نوار پیشرفت تمام شود، یا از WP-CLI دستور wp teznevise classic-content import را اجرا کنید.', 'teznevise' ) . '</p></div>';
}
add_action( 'admin_notices', 'teznevise_classic_import_admin_notice' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'teznevise classic-content import', static function () {
		$result = teznevise_wxr_import_classic_editor();
		WP_CLI::log( wp_json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		if ( $result['errors'] ) {
			WP_CLI::error( 'Classic content import completed with errors.' );
		}
		WP_CLI::success( 'Classic content import completed.' );
	} );
}
