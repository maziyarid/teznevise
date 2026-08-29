<?php
/**
 * Contract tests for Classic Editor ownership, display thresholds, and ID namespacing.
 *
 * Runs without WordPress by stubbing a small subset of core helpers.
 *
 * @package Teznevise
 */

define( 'ABSPATH', __DIR__ . '/' );

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function is_admin() { return false; }
function is_singular( $type = '' ) { return false; }
function in_the_loop() { return false; }
function is_main_query() { return false; }
function is_front_page() { return false; }
function is_page_template( $template = '' ) { return false; }
function the_content() {}
function get_the_ID() { return 0; }
function get_post_field( $field, $post_id ) { return ''; }
function get_post_meta( $post_id, $key, $single = false ) { return $single ? '' : array(); }
function get_post_type( $post_id ) { return 'page'; }
function strip_shortcodes( $content ) {
	return preg_replace( '/\[\/?[a-zA-Z][\w-]*(?:[^\]]*)\]/', '', (string) $content );
}
function wp_strip_all_tags( $content ) {
	return trim( preg_replace( '/\s+/u', ' ', strip_tags( (string) $content ) ) );
}
function sanitize_title( $title ) {
	$title = (string) $title;
	$title = strtolower( $title );
	$title = preg_replace( '/[^\p{L}\p{N}]+/u', '-', $title );
	return trim( (string) $title, '-' );
}
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}
function wp_kses_post( $content ) {
	return (string) $content;
}
function esc_html__( $text, $domain = 'default' ) {
	return $text;
}
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

require dirname( __DIR__ ) . '/inc/extracted-pages.php';

$failures = 0;
function tz_assert( $condition, $message ) {
	global $failures;
	if ( $condition ) {
		echo "PASS  {$message}\n";
		return;
	}
	++$failures;
	echo "FAIL  {$message}\n";
}

tz_assert( false === teznevise_page_has_owned_editor_content( '' ), 'empty content is not owned' );
tz_assert( false === teznevise_page_has_owned_editor_content( '[tz_home]' ), 'shortcode-only content is not owned' );
tz_assert( false === teznevise_page_has_owned_editor_content( "[gravityform id='1']\n[tz_thesis_hub]" ), 'mixed layout shortcodes are not owned' );
tz_assert( '' === teznevise_interactive_shortcodes_markup( '[tz_thesis_hub]' ), 'hub shortcodes are not kept as functional widgets' );
tz_assert( '' === teznevise_interactive_shortcodes_markup( '[tz_proposal_hub][tz_home]' ), 'proposal/home hubs are layout-only' );
tz_assert( true === teznevise_is_layout_hub_shortcode( 'tz_thesis_hub' ), 'thesis hub is a layout shortcode' );
tz_assert( true === teznevise_page_has_owned_editor_content( 'x' ), '1-character editor copy is owned' );
tz_assert( true === teznevise_page_has_owned_editor_content( str_repeat( 'a', 20 ) ), '20-character editor copy is owned' );
tz_assert( true === teznevise_page_has_owned_editor_content( str_repeat( 'a', 39 ) ), '39-character editor copy is owned' );
tz_assert( true === teznevise_page_has_owned_editor_content( str_repeat( 'a', 40 ) ), '40-character editor copy is owned' );
tz_assert( true === teznevise_page_has_owned_editor_content( 'سلام [tz_home]' ), 'mixed shortcode/prose is owned' );
tz_assert( '' === teznevise_page_classic_copy( '' ), 'empty classic source has no leftover copy' );
tz_assert( '' === teznevise_page_classic_copy( '[tz_home]' ), 'layout-only shortcodes leave no leftover copy' );
tz_assert( function_exists( 'teznevise_the_classic_page_content' ), 'in-place classic helper is available' );

tz_assert( false === teznevise_page_has_editorial_copy( 'x' ), 'display threshold rejects 1-character copy' );
tz_assert( false === teznevise_page_has_editorial_copy( str_repeat( 'a', 39 ) ), 'display threshold rejects 39-character copy' );
tz_assert( true === teznevise_page_has_editorial_copy( str_repeat( 'a', 40 ) ), 'display threshold accepts 40-character copy' );

$html = teznevise_prepare_classic_disclosure_html(
	'<h1>Title</h1><p id="dup">One</p><p id="dup">Two</p><a href="#dup">ref</a><button aria-controls="dup">open</button><p id="عنوان">fa</p><p id="">empty</p>',
	12
);
tz_assert( false === strpos( $html, '<h1' ) && false !== strpos( $html, '<h2' ), 'embedded H1 is demoted to H2' );
tz_assert( false !== strpos( $html, 'id="tz-editor-12-dup"' ), 'first duplicate ID is namespaced' );
tz_assert( false !== strpos( $html, 'id="tz-editor-12-dup-2"' ), 'second duplicate ID gets an occurrence suffix' );
tz_assert( 1 === preg_match( '/id="tz-editor-12-dup"/', $html ) && 1 === preg_match( '/id="tz-editor-12-dup-2"/', $html ), 'duplicate IDs are unique after sanitizing' );
tz_assert( false !== strpos( $html, 'href="#tz-editor-12-dup"' ), 'href fragments follow the first namespaced ID' );
tz_assert( false !== strpos( $html, 'aria-controls="tz-editor-12-dup"' ), 'aria-controls follows the first namespaced ID' );
tz_assert( false === strpos( $html, 'id=""' ), 'empty IDs are replaced rather than left blank' );
tz_assert( 1 === preg_match( '/id="tz-editor-12-[^"]+"/', $html ), 'Persian or sanitized IDs still receive a prefix' );

if ( $failures ) {
	fwrite( STDERR, $failures . " classic-content contract test(s) failed\n" );
	exit( 1 );
}
echo "classic-content contract tests passed\n";
