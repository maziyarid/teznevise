<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Dormant until reviewed GSC batches are applied by the owner.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! function_exists( 'tezgsc20261110_config' ) ) {
function tezgsc20261110_config() { $value = get_option( 'teznevise_gsc_20261110', array() ); return is_array( $value ) && ! empty( $value['enabled'] ) ? $value : array(); }
function tezgsc20261110_key( $url ) {
    $parts = wp_parse_url( $url );
    $hosts = array( 'teznevise.ir', 'www.teznevise.ir', strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) );
    if ( ! is_array( $parts ) || ( isset( $parts['scheme'] ) && ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) || ( ! empty( $parts['host'] ) && ! in_array( strtolower( $parts['host'] ), $hosts, true ) ) ) { return ''; }
    $path = rawurldecode( $parts['path'] ?? '/' );
    if ( ! str_starts_with( $path, '/' ) || str_contains( $path, '..' ) || preg_match( '/[\x00-\x1f\x7f]/', $path ) ) { return ''; }
    return $path . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
}
function tezgsc20261110_path( $key ) { return explode( '?', $key, 2 )[0]; }
function tezgsc20261110_query( $key ) { return explode( '?', $key, 2 )[1] ?? ''; }
function tezgsc20261110_route_path_exists( $key ) { $path = ABSPATH . ltrim( tezgsc20261110_path( $key ), '/' ); return is_file( $path ) || is_dir( $path ); }
function tezgsc20261110_protected( $key ) {
    $path = tezgsc20261110_path( $key ) ?: '/';
    if ( preg_match( '#^/(?:wp-admin|wp-json|wp-content|wp-includes|cart|checkout|my-account|account|staging|test|cgi-bin|\.git|search)(?:/|$)#i', $path ) || preg_match( '#(?:^/wp-[^/]+\.php$|/xmlrpc\.php$|/feed/?$|/trackback/?$|\.(?:php|css|js|map|woff2?|ttf|eot|svg|png|jpe?g|gif|webp|ico|pdf|zip|rar|7z|docx?|xlsx?|pptx?|csv|xml|txt)$)#i', $path ) ) { return true; }
    parse_str( tezgsc20261110_query( $key ), $query );
    return (bool) array_intersect( array_keys( $query ), array( 's', 'rest_route', 'preview', 'preview_id', '_wpnonce', 'nonce', 'customize_changeset_uuid', 'feed', 'embed', 'edd_action', 'download_file', 'attachment_id' ) );
}
function tezgsc20261110_login_exception( $key, $row ) {
    if ( empty( $row['exact_guest_login_redirect'] ) || '/wp-login.php' !== tezgsc20261110_path( $key ) || '/inquiry/' !== ( $row['target_key'] ?? '' ) ) { return false; }
    parse_str( tezgsc20261110_query( $key ), $query );
    if ( empty( $query['redirect_to'] ) || array_diff( array_keys( $query ), array( 'redirect_to', 'reauth', 'action' ) ) ) { return false; }
    if ( isset( $query['action'] ) && 'lostpassword' !== $query['action'] ) { return false; }
    $redirect = tezgsc20261110_key( $query['redirect_to'] );
    return '' !== $redirect && ! str_starts_with( $redirect, '/wp-admin' ) && ! str_starts_with( $redirect, '/wp-login' );
}
function tezgsc20261110_owned_restore( $key ) {
    $config = tezgsc20261110_config(); $path = tezgsc20261110_path( $key ); $page = $config['restored'][ $path ] ?? array();
    if ( ! $page || tezgsc20261110_protected( $key ) || empty( $page['id'] ) ) { return array(); }
    $post = get_post( (int) $page['id'] );
    if ( ! $post || 'page' !== $post->post_type || 'publish' !== $post->post_status || ! empty( $post->post_password ) || get_post_meta( $page['id'], '_tez_gsc20261110_managed', true ) !== $page['ownership_token'] || get_post_meta( $page['id'], '_tez_gsc20261110_original_path', true ) !== $page['path'] ) { return array(); }
    return $page;
}
function tezgsc20261110_restored_page() {
    if ( ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin() || is_search() || is_feed() || is_404() ) { return array(); }
    $key = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ); $page = tezgsc20261110_owned_restore( $key );
    return $page && (int) get_queried_object_id() === (int) $page['id'] ? $page : array();
}
function tezgsc20261110_request( $vars ) {
    if ( ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin() ) { return $vars; }
    $key = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
    $page = tezgsc20261110_owned_restore( $key );
    // Replace conflicting pagename/name/error/attachment/feed/date variables. The
    // original request URI stays intact; no parent pages or rewrite flush needed.
    return $page ? array( 'page_id' => (int) $page['id'], 'post_type' => 'page' ) : $vars;
}
function tezgsc20261110_page_link( $url, $id, $sample = false ) {
    $config = tezgsc20261110_config();
    foreach ( $config['restored'] ?? array() as $path => $page ) { if ( (int) $id === (int) $page['id'] && tezgsc20261110_owned_restore( $path ) ) { return home_url( $path ); } }
    return $url;
}
function tezgsc20261110_url_to_postid( $url ) {
    $page = tezgsc20261110_owned_restore( tezgsc20261110_key( $url ) );
    return $page ? home_url( '/?page_id=' . (int) $page['id'] ) : $url;
}
function tezgsc20261110_restore_canonical_redirect( $redirect, $requested = '' ) { return tezgsc20261110_restored_page() ? false : $redirect; }
function tezgsc20261110_prepare_restore() {
    if ( ! tezgsc20261110_restored_page() ) { return; }
    global $wp_filter;
    foreach ( array( 'wp', 'template_redirect' ) as $hook ) {
        foreach ( $wp_filter[ $hook ]->callbacks ?? array() as $priority => $callbacks ) {
            foreach ( $callbacks as $entry ) {
                $callback = $entry['function'];
                if ( ! is_array( $callback ) || ! is_object( $callback[0] ) ) { continue; }
                $class = get_class( $callback[0] ); $method = $callback[1];
                $rank_math = 'RankMath\\Redirections\\Redirections' === $class && 'do_redirection' === $method;
                $yoast = ( str_starts_with( $class, 'WPSEO_Redirect' ) || ( str_contains( $class, 'Yoast' ) && str_contains( $class, 'Redirect' ) ) ) && in_array( $method, array( 'do_redirect', 'do_redirection', 'redirect', 'process_redirects' ), true );
                if ( $rank_math || $yoast ) { remove_action( $hook, $callback, $priority ); }
            }
        }
    }
}
function tezgsc20261110_route_row( $key, $method, $logged_in ) {
    if ( ! in_array( $method, array( 'GET', 'HEAD' ), true ) ) { return array(); }
    $config = tezgsc20261110_config(); $row = $config['decisions'][ $key ] ?? array();
    $alias = $config['internal_aliases'][ $key ] ?? '';
    if ( ! $alias && '/' === tezgsc20261110_path( $key ) ) {
        parse_str( tezgsc20261110_query( $key ), $query );
        if ( 1 === count( $query ) && ! empty( $query['page_id'] ) ) { foreach ( $config['restored'] ?? array() as $path => $page ) { if ( (int) $page['id'] === (int) $query['page_id'] ) { $alias = $path; break; } } }
    }
    if ( $alias && tezgsc20261110_owned_restore( $alias ) ) { return array( 'actions' => array( '301' ), 'target_key' => $alias, 'generated_restore_alias' => true ); }
    $legacy = ! empty( $row['exact_legacy_sample'] ) && in_array( $key, array( '/wp-content/themes/flatsome/*', '/wp-content/plugins/gravityforms', '/wp-content/plugins/gravityforms/images' ), true );
    if ( ! $row || ( tezgsc20261110_protected( $key ) && ! tezgsc20261110_login_exception( $key, $row ) && ! $legacy && empty( $row['exact_obsolete_feed'] ) ) || ( ! empty( $row['exact_guest_login_redirect'] ) && $logged_in ) ) { return array(); }
    return $row;
}
function tezgsc20261110_route() {
    if ( ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() || wp_doing_cron() || ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) ) { return; }
    $key = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
    $row = tezgsc20261110_route_row( $key, $_SERVER['REQUEST_METHOD'] ?? 'GET', is_user_logged_in() );
    if ( ! $row ) { return; }
    $path = tezgsc20261110_path( $key );
    $page_id_alias = ! empty( $row['generated_restore_alias'] ) && '/' === tezgsc20261110_path( $key ) && str_starts_with( tezgsc20261110_query( $key ), 'page_id=' );
    if ( tezgsc20261110_route_path_exists( $key ) && empty( $row['exact_guest_login_redirect'] ) && ! $page_id_alias ) { return; }
    if ( in_array( '301', $row['actions'] ?? array(), true ) ) { wp_safe_redirect( home_url( $row['target_key'] ), 301, 'Teznevise reviewed GSC map 20261110' ); exit; }
    if ( in_array( '410', $row['actions'] ?? array(), true ) ) {
        status_header( 410 ); nocache_headers(); header( 'X-Robots-Tag: noindex, follow', true );
        if ( 'HEAD' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) { echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>این نشانی حذف شده است</title><h1>این نشانی دیگر در دسترس نیست</h1><p>این نشانی قدیمی از سایت حذف شده است. از فهرست صفحات سایت برای یافتن محتوای مرتبط استفاده کنید.</p></html>'; }
        exit;
    }
}
function tezgsc20261110_robots_merge( $output, $allows ) {
    if ( ! $allows ) { return $output; }
    $lines = preg_split( '/\r\n|\n|\r/', $output ); $result = array(); $group = array(); $has_agent = false; $has_directive = false;
    $finish = static function () use ( &$result, &$group, &$has_agent, &$has_directive, $allows ) {
        if ( $has_agent ) {
            foreach ( $allows as $allow ) { $rule = 'Allow: ' . $allow . '$'; if ( ! in_array( $rule, array_map( 'trim', $group ), true ) ) { $group[] = $rule; } }
        }
        $result = array_merge( $result, $group ); $group = array(); $has_agent = false; $has_directive = false;
    };
    foreach ( $lines as $line ) {
        if ( preg_match( '/^\s*User-agent\s*:/i', $line ) ) { if ( $has_agent && $has_directive ) { $finish(); } $has_agent = true; }
        elseif ( preg_match( '/^\s*(?:Allow|Disallow|Crawl-delay|Host)\s*:/i', $line ) ) { $has_directive = true; }
        $group[] = $line;
    }
    if ( ! $has_agent && ! $result ) { $group[] = 'User-agent: *'; $has_agent = true; }
    $finish(); return implode( "\n", $result );
}
function tezgsc20261110_allows( $config ) {
    $paths = array(); foreach ( $config['decisions'] ?? array() as $key => $row ) { if ( in_array( 'unblock', $row['actions'] ?? array(), true ) ) { $paths[] = $key; } } return array_values( array_unique( $paths ) );
}
function tezgsc20261110_robots_txt( $output, $public ) {
    return $public && '1' === (string) get_option( 'blog_public' ) ? tezgsc20261110_robots_merge( $output, tezgsc20261110_allows( tezgsc20261110_config() ) ) : $output;
}
function tezgsc20261110_index_row() {
    $key = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ); $config = tezgsc20261110_config(); $row = $config['decisions'][ $key ] ?? array();
    return '1' === (string) get_option( 'blog_public' ) && ! tezgsc20261110_protected( $key ) && ! is_404() && ! is_search() && ! is_feed() && ! empty( array_intersect( $row['actions'] ?? array(), array( 'remove_noindex', 'restore' ) ) ) ? $row : array();
}
function tezgsc20261110_robots( $robots ) {
    if ( ! tezgsc20261110_index_row() ) { return $robots; }
    if ( is_array( $robots ) ) { $string_style = in_array( 'noindex', $robots, true ) || in_array( 'index', $robots, true ); unset( $robots['noindex'] ); foreach ( $robots as $key => $value ) { if ( 'noindex' === $value ) { unset( $robots[ $key ] ); } } $robots['index'] = $string_style ? 'index' : true; return $robots; }
    return preg_replace( '/\bnoindex\b/i', 'index', (string) $robots );
}
function tezgsc20261110_full_url( $url ) {
    $parts = wp_parse_url( $url ); $home = wp_parse_url( home_url( '/' ) );
    if ( ! is_array( $parts ) || ! is_array( $home ) || isset( $parts['user'] ) || isset( $parts['pass'] ) ) { return ''; }
    $scheme = strtolower( $parts['scheme'] ?? $home['scheme'] ?? 'https' );
    $host = strtolower( $parts['host'] ?? $home['host'] ?? '' );
    if ( ! in_array( $scheme, array( 'http', 'https' ), true ) || '' === $host ) { return ''; }
    $port = $parts['port'] ?? ( isset( $parts['host'] ) ? null : ( $home['port'] ?? null ) );
    $port_text = $port && ! ( ( 'https' === $scheme && 443 === (int) $port ) || ( 'http' === $scheme && 80 === (int) $port ) ) ? ':' . $port : '';
    return $scheme . '://' . $host . $port_text . rawurldecode( $parts['path'] ?? '/' ) . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
}
function tezgsc20261110_origin( $url ) {
    $parts = wp_parse_url( tezgsc20261110_full_url( $url ) );
    return is_array( $parts ) ? $parts['scheme'] . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' ) : '';
}
function tezgsc20261110_observed_canonical( $canonical ) {
    // Offline evidence was captured on production. Translate only its known HTTPS
    // apex origin when validating a staging clone; actual post metadata is never translated.
    if ( 'https://teznevise.ir' !== tezgsc20261110_origin( home_url( '/' ) ) && preg_match( '#^https://teznevise\.ir(?=/|$)#i', $canonical ) ) {
        $canonical = preg_replace( '#^https://teznevise\.ir(?=/|$)#i', tezgsc20261110_origin( home_url( '/' ) ), $canonical, 1 );
    }
    return $canonical;
}
function tezgsc20261110_sitemap_eligible( $url, $post = null ) {
    $key = tezgsc20261110_key( $url ); $full = tezgsc20261110_full_url( $url );
    if ( tezgsc20261110_origin( $url ) !== tezgsc20261110_origin( home_url( '/' ) ) ) { return false; }
    if ( ! $key || str_contains( $key, '?' ) || tezgsc20261110_protected( $key ) || '1' !== (string) get_option( 'blog_public' ) ) { return false; }
    $config = tezgsc20261110_config(); $row = $config['decisions'][ $key ] ?? array();
    $base = get_option( 'teznevise_onpage_20261110', array() ); $base_key = rtrim( $key, '/' ) . '/';
    if ( ! empty( $base['enabled'] ) && isset( $base['redirects'][ $base_key ] ) ) { return false; }
    $managed_base = ! empty( $base['enabled'] ) && ! empty( $base['pages'][ $base_key ]['id'] );
    if ( array_intersect( $row['actions'] ?? array(), array( '301', '410' ) ) ) { return false; }
    $rule = $config['sitemap_rules'][ $key ] ?? array();
    if ( ! $managed_base && $rule && ( 200 !== (int) $rule['status'] || '0' === $rule['indexable'] || ( ! empty( $rule['canonical'] ) && $full !== tezgsc20261110_full_url( tezgsc20261110_observed_canonical( $rule['canonical'] ) ) ) ) ) { return false; }
    if ( ! $post && ! empty( $config['restored'][ $key ]['id'] ) ) { $post = get_post( (int) $config['restored'][ $key ]['id'] ); }
    if ( ! $post && function_exists( 'url_to_postid' ) && function_exists( 'get_post' ) ) { $id = url_to_postid( $url ); $post = $id ? get_post( $id ) : null; }
    $native_public = false;
    if ( $post && isset( $post->post_status ) ) {
        if ( 'publish' !== $post->post_status || ! empty( $post->post_password ) || 'attachment' === $post->post_type ) { return false; }
        $id = (int) $post->ID;
        if ( function_exists( 'get_permalink' ) ) { $native_canonical = get_permalink( $post ); if ( $native_canonical && $full !== tezgsc20261110_full_url( $native_canonical ) ) { return false; } }
        if ( '1' === get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true ) ) { return false; }
        $robots = get_post_meta( $id, 'rank_math_robots', true ); if ( is_array( $robots ) && in_array( 'noindex', $robots, true ) ) { return false; }
        $native_public = true;
        foreach ( array( '_yoast_wpseo_canonical', 'rank_math_canonical_url' ) as $field ) { $canonical = get_post_meta( $id, $field, true ); if ( $canonical && $full !== tezgsc20261110_full_url( $canonical ) ) { return false; } }
    }
    return $managed_base || $native_public || ( ! empty( $rule ) && 200 === (int) $rule['status'] && '0' !== $rule['indexable'] );
}
function tezgsc20261110_sitemap_entry( $entry, $type = '', $object = null ) { return is_array( $entry ) && ! empty( $entry['loc'] ) && tezgsc20261110_sitemap_eligible( $entry['loc'], $object ) ? $entry : false; }
function tezgsc20261110_core_sitemap( $list, $subtype, $page, $provider_name ) {
    static $inside = false;
    if ( $inside ) { return $list; }
    if ( null === $list ) {
        $provider = wp_sitemaps_get_server()->registry->get_provider( $provider_name ); if ( ! $provider ) { return $list; }
        $inside = true; try { $list = $provider->get_url_list( $page, $subtype ); } finally { $inside = false; }
    }
    return array_values( array_filter( (array) $list, static fn( $entry ) => is_array( $entry ) && ! empty( $entry['loc'] ) && tezgsc20261110_sitemap_eligible( $entry['loc'] ) ) );
}
function tezgsc20261110_xml( $xml ) {
    return preg_replace_callback( '#<((?:[A-Za-z_][\w.-]*:)?)(?:url)\b[^>]*>.*?</\1url>#s', static function ( $match ) {
        if ( ! preg_match( '#<(?:[A-Za-z_][\w.-]*:)?loc\b[^>]*>(.*?)</(?:[A-Za-z_][\w.-]*:)?loc>#s', $match[0], $loc ) ) { return ''; }
        $url = html_entity_decode( strip_tags( $loc[1] ), ENT_QUOTES | ENT_XML1, 'UTF-8' );
        return tezgsc20261110_sitemap_eligible( $url ) ? $match[0] : '';
    }, $xml );
}
function tezgsc20261110_href( $match ) {
    $tag = $match[0]; if ( preg_match( '#\sdownload(?:\s|=|>)#i', $tag ) || ! preg_match( '#\bhref\s*=\s*([\'\"])(.*?)\1#si', $tag, $href ) ) { return $tag; }
    $url = html_entity_decode( $href[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' ); $key = tezgsc20261110_key( $url );
    if ( ! $key ) { return $tag; }
    $config = tezgsc20261110_config(); $decision = $config['decisions'][ $key ] ?? array();
    if ( tezgsc20261110_protected( $key ) && empty( $decision['exact_obsolete_feed'] ) && ( ! tezgsc20261110_login_exception( $key, $decision ) || ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) ) ) { return $tag; }
    $source = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ); $target = $config['href_rewrites'][ $source ][ $key ] ?? '';
    if ( ! $target ) { return $tag; }
    $fragment = wp_parse_url( $url, PHP_URL_FRAGMENT ); if ( null !== $fragment && false !== $fragment && '' !== $fragment && ! str_contains( $target, '#' ) ) { $target .= '#' . $fragment; }
    $new = preg_replace( '#^(href\s*=\s*[\'\"]).*?([\'\"])$#si', '$1' . str_replace( '$', '\\$', esc_attr( home_url( $target ) ) ) . '$2', $href[0] );
    return substr_replace( $tag, $new, strpos( $tag, $href[0] ), strlen( $href[0] ) );
}
function tezgsc20261110_html( $html ) { return preg_replace_callback( '#<a\b[^>]*>.*?</a>#si', 'tezgsc20261110_href', $html ); }
function tezgsc20261110_buffer() {
    if ( is_admin() || is_feed() || is_search() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
    $config = tezgsc20261110_config(); $key = tezgsc20261110_key( 'https://teznevise.ir' . wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
    if ( ! empty( $config['href_rewrites'][ $key ] ) ) { ob_start( 'tezgsc20261110_html' ); }
}
if ( tezgsc20261110_config() ) {
    add_filter( 'request', 'tezgsc20261110_request', 9999 );
    add_filter( 'page_link', 'tezgsc20261110_page_link', 9999, 3 );
    add_filter( 'url_to_postid', 'tezgsc20261110_url_to_postid', 9999 );
    add_filter( 'redirect_canonical', 'tezgsc20261110_restore_canonical_redirect', 9999, 2 );
    add_action( 'wp', 'tezgsc20261110_prepare_restore', -9999 );
    add_action( 'init', 'tezgsc20261110_route', -4000 );
    add_action( 'template_redirect', 'tezgsc20261110_buffer', -4000 );
    add_filter( 'robots_txt', 'tezgsc20261110_robots_txt', 1000, 2 );
    foreach ( array( 'wp_robots', 'wpseo_robots', 'rank_math/frontend/robots' ) as $hook ) { add_filter( $hook, 'tezgsc20261110_robots', 1000 ); }
    foreach ( array( 'wpseo_sitemap_entry', 'rank_math/sitemap/entry' ) as $hook ) { add_filter( $hook, 'tezgsc20261110_sitemap_entry', 1000, 3 ); }
    add_filter( 'wp_sitemaps_posts_pre_url_list', static fn( $list, $subtype, $page ) => tezgsc20261110_core_sitemap( $list, $subtype, $page, 'posts' ), 1000, 3 );
    add_filter( 'wp_sitemaps_taxonomies_pre_url_list', static fn( $list, $subtype, $page ) => tezgsc20261110_core_sitemap( $list, $subtype, $page, 'taxonomies' ), 1000, 3 );
    if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        // Runs before legacy theme aliases that redirect while the theme is being included.
        if ( did_action( 'plugins_loaded' ) ) { tezgsc20261110_route(); }
        else { add_action( 'plugins_loaded', 'tezgsc20261110_route', -9999 ); }
        if ( preg_match( '#/[^/?]*sitemap[^/?]*\.xml(?:\?|$)#i', $_SERVER['REQUEST_URI'] ?? '' ) ) { ob_start( 'tezgsc20261110_xml' ); }
    }
}
}
