<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Dormant until the owner applies the reviewed WP-CLI package.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( is_readable( __DIR__ . '/seo-gsc-20261110.php' ) ) { require_once __DIR__ . '/seo-gsc-20261110.php'; }
if ( ! function_exists( 'tezseo20261110_config' ) ) {

function tezseo20261110_config() { $value = get_option( 'teznevise_onpage_20261110', array() ); return is_array( $value ) && ! empty( $value['enabled'] ) ? $value : array(); }
function tezseo20261110_request_path() {
    $path = rawurldecode( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ), PHP_URL_PATH ) );
    return '/' === $path || '' === $path ? '/' : '/' . trim( $path, '/' ) . '/';
}
function tezseo20261110_page() {
    $config = tezseo20261110_config(); $path = tezseo20261110_request_path();
    if ( is_404() || is_search() || is_feed() || is_paged() ) { return array(); }
    if ( empty( $config['pages'][ $path ] ) ) { return function_exists( 'tezgsc20261110_restored_page' ) ? tezgsc20261110_restored_page() : array(); }
    return $config['pages'][ $path ];
}
function tezseo20261110_redirect() {
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) ) { return; }
    $config = tezseo20261110_config(); $path = tezseo20261110_request_path();
    if ( ! empty( $config['redirects'][ $path ] ) ) {
        wp_safe_redirect( home_url( $config['redirects'][ $path ] ), 301, 'Teznevise reviewed SEO map 20261110' ); exit;
    }
}
function tezseo20261110_title( $title ) { $page = tezseo20261110_page(); return $page['seo_title'] ?? $title; }
function tezseo20261110_description( $description ) { $page = tezseo20261110_page(); return $page['description'] ?? $description; }
function tezseo20261110_canonical( $canonical ) { $page = tezseo20261110_page(); return ! empty( $page ) ? home_url( $page['path'] ) : $canonical; }
function tezseo20261110_prepare() {
    $page = tezseo20261110_page();
    if ( empty( $page ) ) { return; }
    if ( '/' !== $page['path'] ) {
        remove_action( 'wp_footer', 'teznevise_output_faq_schema', 22 );
        remove_action( 'wp_footer', 'teznevise_output_service_schema', 23 );
    }
    // This package owns the managed head and prevents duplicate fallback metadata.
    if ( ! function_exists( 'teznevise_seo_plugin_active' ) || ! teznevise_seo_plugin_active() ) {
        remove_action( 'wp_head', 'teznevise_seo_output_head', 1 );
        add_action( 'wp_head', 'tezseo20261110_fallback_head', 1 );
    }
}
function tezseo20261110_fallback_head() {
    $page = tezseo20261110_page(); if ( ! $page ) { return; }
    echo '<meta name="description" content="' . esc_attr( $page['description'] ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $page['seo_title'] ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $page['description'] ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( home_url( $page['path'] ) ) . '">' . "\n";
}
function tezseo20261110_robots( $robots ) {
    if ( tezseo20261110_page() && '1' === (string) get_option( 'blog_public' ) ) {
        unset( $robots['noindex'], $robots['nofollow'] ); $robots['index'] = true; $robots['follow'] = true;
    }
    return $robots;
}
function tezseo20261110_template( $template ) {
    $page = tezseo20261110_page();
    return ! empty( $page['id'] ) && '/' !== $page['path'] && is_page( (int) $page['id'] ) ? __DIR__ . '/seo-onpage-20261110-template.php' : $template;
}
function tezseo20261110_schema_without_faq( $data ) {
    if ( ! tezseo20261110_page() || '/' === tezseo20261110_request_path() || ! is_array( $data ) ) { return $data; }
    $was_list = array_is_list( $data );
    $root_types = $data['@type'] ?? array(); $root_types = is_array( $root_types ) ? $root_types : array( $root_types );
    if ( in_array( 'FAQPage', $root_types, true ) ) {
        if ( 1 === count( $root_types ) ) { return array(); }
        $data['@type'] = array_values( array_diff( $root_types, array( 'FAQPage' ) ) ); unset( $data['mainEntity'] );
    }
    foreach ( $data as $key => $value ) {
        if ( is_array( $value ) ) {
            $types = $value['@type'] ?? array(); $types = is_array( $types ) ? $types : array( $types );
            if ( in_array( 'FAQPage', $types, true ) ) {
                if ( 1 === count( $types ) ) { unset( $data[ $key ] ); continue; }
                $data[ $key ]['@type'] = array_values( array_diff( $types, array( 'FAQPage' ) ) );
                unset( $data[ $key ]['mainEntity'] );
            } else { $data[ $key ] = tezseo20261110_schema_without_faq( $value ); }
        }
    }
    return $was_list ? array_values( $data ) : $data;
}
function tezseo20261110_faq() {
    $page = tezseo20261110_page(); if ( empty( $page['id'] ) || '/' === $page['path'] ) { return; }
    $faq = get_post_meta( $page['id'], '_tez_seo20261110_faq', true );
    if ( is_array( $faq ) && 'FAQPage' === ( $faq['@type'] ?? '' ) ) {
        echo '<script id="tezseo-20261110-faq" type="application/ld+json">' . wp_json_encode( $faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n";
    }
}
function tezseo20261110_external_anchor( $match ) {
    $tag = $match[0];
    if ( ! preg_match( '#\bhref\s*=\s*([\'\"])(.*?)\1#si', $tag, $href ) ) { return $tag; }
    $url = wp_parse_url( html_entity_decode( $href[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
    if ( ! is_array( $url ) || ! in_array( strtolower( $url['host'] ?? '' ), array( 'bluethesis.ir', 'www.bluethesis.ir' ), true ) || '/statistical-analysis' !== rtrim( $url['path'] ?? '', '/' ) ) { return $tag; }
    $config = tezseo20261110_config(); $action = $config['external_sources'][ tezseo20261110_request_path() ] ?? '';
    if ( 'remove' === $action ) { return strip_tags( $tag ); }
    if ( 'nofollow' !== $action ) { return $tag; }
    if ( preg_match( '#\brel\s*=\s*([\'\"])(.*?)\1#si', $tag, $rel ) ) {
        $tokens = preg_split( '/\s+/', trim( $rel[2] ) );
        if ( ! in_array( 'nofollow', array_map( 'strtolower', $tokens ), true ) ) { $tokens[] = 'nofollow'; }
        return substr_replace( $tag, 'rel="' . esc_attr( implode( ' ', $tokens ) ) . '"', strpos( $tag, $rel[0] ), strlen( $rel[0] ) );
    }
    return preg_replace( '#<a\b#i', '<a rel="nofollow"', $tag, 1 );
}
function tezseo20261110_normalize_anchor( $text ) {
    $text = html_entity_decode( strip_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    return trim( preg_replace( '/\s+/u', ' ', str_replace( "\u{200C}", ' ', $text ) ) );
}
function tezseo20261110_rewrite_anchor( $match ) {
    $tag = $match[0];
    if ( ! preg_match( '#\bhref\s*=\s*([\'\"])(.*?)\1#si', $tag, $href ) ) { return $tag; }
    $url = wp_parse_url( html_entity_decode( $href[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
    if ( ! is_array( $url ) || ( ! empty( $url['host'] ) && ! in_array( strtolower( $url['host'] ), array( 'teznevise.ir', 'www.teznevise.ir' ), true ) ) ) { return $tag; }
    $trimmed_path = trim( rawurldecode( $url['path'] ?? '/' ), '/' ); $path = '' === $trimmed_path ? '/' : '/' . $trimmed_path . '/';
    $config = tezseo20261110_config(); $text = tezseo20261110_normalize_anchor( $tag );
    foreach ( $config['anchor_rewrites'][ tezseo20261110_request_path() ] ?? array() as $row ) {
        if ( $path === $row['target'] && $text === tezseo20261110_normalize_anchor( $row['old_anchor'] ) ) {
            return preg_replace( '#^(<a\b[^>]*>).*?(</a>)$#si', '$1' . str_replace( '$', '\\$', esc_html( $row['new_anchor'] ) ) . '$2', $tag );
        }
    }
    return $tag;
}
function tezseo20261110_transform_html( $html ) {
    $config = tezseo20261110_config(); $path = tezseo20261110_request_path();
    $home = $config['pages']['/'] ?? array();
    if ( '/' === $path && ! empty( $home['id'] ) && ! empty( $home['content_file'] ) ) {
        $copy = (string) get_post_field( 'post_content', (int) $home['id'] );
        if ( preg_match( '#<h2\b[^>]*>(.*?)</h2>#si', $copy, $heading ) ) {
            $text = tezseo20261110_normalize_anchor( $heading[1] );
            if ( '' !== $text && ! str_contains( tezseo20261110_normalize_anchor( $html ), $text ) ) {
                $block = '<section id="tezseo-20261110-home-intro" class="section"><div class="container longcopy article-content">' . $copy . '</div></section>';
                $position = strripos( $html, '</main>' );
                if ( false !== $position ) { $html = substr_replace( $html, $block, $position, 0 ); }
                else { $position = strripos( $html, '</body>' ); if ( false !== $position ) { $html = substr_replace( $html, $block, $position, 0 ); } }
            }
        }
    }
    if ( ! empty( $config['anchor_rewrites'][ $path ] ) ) { $html = preg_replace_callback( '#<a\b[^>]*>.*?</a>#si', 'tezseo20261110_rewrite_anchor', $html ); }
    if ( ! empty( $config['external_sources'][ $path ] ) ) { $html = preg_replace_callback( '#<a\b[^>]*>.*?</a>#si', 'tezseo20261110_external_anchor', $html ); }
    if ( ! empty( $config['links'][ $path ] ) && ! str_contains( $html, 'id="tezseo-20261110-context"' ) ) {
        $links = '<aside id="tezseo-20261110-context" class="section"><div class="container"><p>';
        $parts = array();
        foreach ( $config['links'][ $path ] as $row ) { $parts[] = '<a href="' . esc_url( home_url( $row['target'] ) ) . '">' . esc_html( $row['anchor'] ) . '</a>'; }
        $links .= implode( ' · ', $parts ) . '</p></div></aside>';
        // Covers builder pages and custom download/calculator templates, once per HTML response.
        $position = strripos( $html, '</main>' );
        if ( false !== $position ) { $html = substr_replace( $html, $links, $position, 0 ); }
        else { $position = strripos( $html, '</body>' ); if ( false !== $position ) { $html = substr_replace( $html, $links, $position, 0 ); } }
    }
    return $html;
}
function tezseo20261110_buffer() {
    if ( is_admin() || is_feed() || is_404() || is_search() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
    $config = tezseo20261110_config(); $path = tezseo20261110_request_path();
    if ( ( '/' === $path && ! empty( $config['pages']['/']['id'] ) && ! empty( $config['pages']['/']['content_file'] ) ) || ! empty( $config['links'][ $path ] ) || ! empty( $config['external_sources'][ $path ] ) || ! empty( $config['anchor_rewrites'][ $path ] ) ) { ob_start( 'tezseo20261110_transform_html' ); }
}
function tezseo20261110_home_mod( $value, $field ) {
    $config = tezseo20261110_config(); $page = $config['pages']['/'] ?? array();
    return $page['hero'][ $field ] ?? $value;
}

if ( tezseo20261110_config() ) {
    add_action( 'init', 'tezseo20261110_redirect', -2000 );
    add_action( 'template_redirect', 'tezseo20261110_buffer', -2000 );
    add_action( 'wp', 'tezseo20261110_prepare', 999 );
    add_filter( 'wp_robots', 'tezseo20261110_robots', 999 );
    add_filter( 'template_include', 'tezseo20261110_template', 999 );
    foreach ( array( 'pre_get_document_title', 'wpseo_title', 'rank_math/frontend/title', 'aioseo_title', 'seopress_titles_title', 'wpseo_opengraph_title', 'wpseo_twitter_title', 'rank_math/opengraph/facebook/og_title', 'rank_math/opengraph/twitter/twitter_title' ) as $hook ) { add_filter( $hook, 'tezseo20261110_title', 999 ); }
    foreach ( array( 'wpseo_metadesc', 'rank_math/frontend/description', 'aioseo_description', 'seopress_titles_desc', 'wpseo_opengraph_desc', 'wpseo_twitter_description', 'rank_math/opengraph/facebook/og_description', 'rank_math/opengraph/twitter/twitter_description' ) as $hook ) { add_filter( $hook, 'tezseo20261110_description', 999 ); }
    foreach ( array( 'get_canonical_url', 'wpseo_canonical', 'rank_math/frontend/canonical', 'aioseo_canonical_url' ) as $hook ) { add_filter( $hook, 'tezseo20261110_canonical', 999 ); }
    add_filter( 'wpseo_schema_graph', 'tezseo20261110_schema_without_faq', 999 );
    add_filter( 'rank_math/json_ld', 'tezseo20261110_schema_without_faq', 999 );
    add_action( 'wp_footer', 'tezseo20261110_faq', 24 );
    foreach ( array( 'hero_title_1', 'hero_title_grad', 'hero_title_2', 'hero_text' ) as $field ) {
        add_filter( 'theme_mod_teznevise_' . $field, static function ( $value ) use ( $field ) { return tezseo20261110_home_mod( $value, $field ); }, 999 );
    }
}

} // Standalone MU-plugin and theme loading share one runtime.
