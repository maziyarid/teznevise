<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Separate, ordered GSC batches; never runs during theme load.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) { throw new RuntimeException( 'Use WP-CLI eval-file.' ); }
if ( ! defined( 'TZSEO_TESTING' ) ) { define( 'TZSEO_TESTING', true ); }
require_once __DIR__ . '/wordpress.php';
require_once __DIR__ . '/../../inc/seo-gsc-20261110.php';
const TZGSC_OPTION = 'teznevise_gsc_20261110';
const TZGSC_MANAGED = '_tez_gsc20261110_managed';
const TZGSC_ORIGINAL = '_tez_gsc20261110_original_path';
const TZGSC_CREATED = '_tez_gsc20261110_created';
function tzgsc_key( $url ) {
    $parts = parse_url( str_starts_with( $url, '//' ) ? 'https://teznevise.ir' . $url : $url );
    if ( false === $parts || ( isset( $parts['scheme'] ) && ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || isset( $parts['port'] ) || ( isset( $parts['host'] ) && ! in_array( strtolower( $parts['host'] ), array( 'teznevise.ir', 'www.teznevise.ir' ), true ) ) ) { throw new RuntimeException( 'Expected an internal GSC URL: ' . $url ); }
    $path = rawurldecode( $parts['path'] ?? '/' );
    if ( ! str_starts_with( $path, '/' ) || str_contains( $path, '..' ) || preg_match( '/[\x00-\x1f\x7f]/', $path ) ) { throw new RuntimeException( 'Unsafe GSC path.' ); }
    return $path . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
}
function tzgsc_path( $key ) { return explode( '?', $key, 2 )[0]; }
function tzgsc_query( $key ) { return explode( '?', $key, 2 )[1] ?? ''; }
function tzgsc_protected( $key ) {
    $path = tzgsc_path( $key ) ?: '/';
    if ( preg_match( '#^/(?:wp-admin|wp-json|wp-content|wp-includes|cart|checkout|my-account|account|staging|test|cgi-bin|\.git|search)(?:/|$)#i', $path ) || preg_match( '#(?:^/wp-[^/]+\.php$|/xmlrpc\.php$|/feed/?$|/trackback/?$|\.(?:php|css|js|map|woff2?|ttf|eot|svg|png|jpe?g|gif|webp|ico|pdf|zip|rar|7z|docx?|xlsx?|pptx?|csv|xml|txt)$)#i', $path ) ) { return true; }
    parse_str( tzgsc_query( $key ), $query );
    return (bool) array_intersect( array_keys( $query ), array( 's', 'rest_route', 'preview', 'preview_id', '_wpnonce', 'nonce', 'customize_changeset_uuid', 'feed', 'embed', 'edd_action', 'download_file', 'attachment_id' ) );
}
function tzgsc_internal_slug( $key ) { return 'seo-restore-20261110-' . substr( hash( 'sha256', $key ), 0, 20 ); }
function tzgsc_catalog( $package ) {
    if ( ! is_readable( $package . '/restored-pages.json' ) ) { return array(); }
    $document = tzseo_json( $package . '/restored-pages.json' ); $catalog = array();
    foreach ( $document['pages'] ?? $document as $page ) {
        foreach ( array( 'path', 'title', 'seo_title', 'description', 'content_file' ) as $field ) { if ( empty( $page[ $field ] ) ) { throw new RuntimeException( 'Restore metadata missing: ' . $field ); } }
        $key = tzgsc_key( $page['path'] );
        if ( '/' === $key || str_starts_with( $key, '//' ) || str_contains( $key, '?' ) || tzgsc_protected( $key ) || isset( $catalog[ $key ] ) ) { throw new RuntimeException( 'Unsafe or duplicate restored path.' ); }
        $file = realpath( $package . '/' . $page['content_file'] );
        if ( ! $file || ! str_starts_with( $file, realpath( $package ) . '/' ) ) { throw new RuntimeException( 'Restore content missing or outside package.' ); }
        $body = file_get_contents( $file );
        preg_match_all( '#<script\b[^>]*type=[\'\"]application/ld\+json[\'\"][^>]*>(.*?)</script>#si', $body, $matches );
        if ( count( $matches[1] ) > 1 ) { throw new RuntimeException( 'Reviewed restoration may contain at most one matching FAQ schema.' ); }
        $faq = array();
        if ( count( $matches[1] ) ) {
            $faq = json_decode( $matches[1][0], true, 512, JSON_THROW_ON_ERROR );
            if ( 'FAQPage' !== ( $faq['@type'] ?? '' ) || count( $faq['mainEntity'] ?? array() ) < 2 ) { throw new RuntimeException( 'Invalid restored FAQ schema.' ); }
        }
        $body = trim( preg_replace( '#<script\b[^>]*>.*?</script>#si', '', $body ) );
        if ( str_contains( $body, "\u{200C}" ) || preg_match( '#<script\b#i', $body ) || ! trim( strip_tags( $body ) ) ) { throw new RuntimeException( 'Restored copy must be reviewed nonempty HTML without scripts or half spaces.' ); }
        $page['path'] = $key; $page['body'] = $body; $page['faq'] = $faq; $page['role'] = 'restore'; $catalog[ $key ] = $page;
    }
    return $catalog;
}
function tzgsc_decision_semantics( $row ) {
    $semantics = array();
    foreach ( array( 'action', 'target_url', 'batch', 'expected_status', 'expected_canonical', 'implementation' ) as $field ) { $semantics[ $field ] = trim( (string) ( $row[ $field ] ?? '' ) ); }
    $robots = strtolower( $row['expected_robots'] ?? '' );
    $allowed = $row['expected_robots_allowed'] ?? 'unknown'; $indexable = $row['expected_indexable'] ?? 'unknown';
    if ( ! in_array( $allowed, array( '0', '1' ), true ) ) { $allowed = preg_match( '/blocked|disallow/', $robots ) ? '0' : ( str_contains( $robots, 'allow' ) ? '1' : 'unknown' ); }
    if ( ! in_array( $indexable, array( '0', '1' ), true ) ) { $indexable = str_contains( $robots, 'noindex' ) ? '0' : ( preg_match( '/\bindex\b/', $robots ) ? '1' : 'unknown' ); }
    $semantics['robots_allowed'] = $allowed; $semantics['indexable'] = $indexable;
    $semantics['unresolved'] = '' !== trim( $row['unresolved_reason'] ?? '' );
    if ( array_intersect( explode( '|', $row['action'] ), array( 'restore', 'remove_noindex' ) ) ) {
        $semantics['post_id'] = (int) ( $row['post_id'] ?? 0 ); $semantics['post_type'] = $row['post_type'] ?? 'page'; $semantics['content_file'] = $row['content_file'] ?? '';
    }
    return $semantics;
}
function tzgsc_decision_rows( $package ) {
    $files = array( $package . '/audit/gsc-decisions-20261110.csv' );
    if ( is_readable( $package . '/audit/project-dead-decisions-20261110.csv' ) ) { $files[] = $package . '/audit/project-dead-decisions-20261110.csv'; }
    $merged = array();
    foreach ( $files as $file ) {
        foreach ( tzseo_csv( $file ) as $row ) {
            $url = $row['request_url'];
            if ( isset( $merged[ $url ] ) ) {
                if ( tzgsc_decision_semantics( $merged[ $url ] ) !== tzgsc_decision_semantics( $row ) ) { throw new RuntimeException( 'Conflicting reviewed decisions for exact request URL: ' . $url ); }
                // Preserve the original GSC row ID and observation, while adding
                // independent project-source evidence and performance fields.
                $primary = $merged[ $url ]; $merged[ $url ] = array_replace( $row, $primary );
                if ( $row['row_id'] !== $primary['row_id'] ) { $merged[ $url ]['project_row_id'] = $row['row_id']; }
            } else { $merged[ $url ] = $row; }
        }
    }
    return array_values( $merged );
}
function tzgsc_plan( $package, $batch ) {
    if ( ! in_array( $batch, array( 'restores', 'project', 'redirects', 'gone', 'indexing', 'links' ), true ) ) { throw new RuntimeException( 'Unknown GSC batch.' ); }
    $catalog = tzgsc_catalog( $package );
    $plan = array( 'batch' => $batch, 'decisions' => array(), 'href_rewrites' => array() );
    foreach ( tzgsc_decision_rows( $package ) as $row ) {
        if ( 'keep' === $row['action'] || $row['batch'] !== $batch ) { continue; }
        if ( trim( $row['unresolved_reason'] ?? '' ) ) { throw new RuntimeException( 'Unresolved decision cannot be applied: ' . $row['row_id'] ); }
        $key = tzgsc_key( $row['request_url'] );
        $login_exception = '301' === $row['action'] && 'exact_guest_login_redirect' === ( $row['implementation'] ?? '' ) && '/wp-login.php' === tzgsc_path( $key ) && '/inquiry/' === tzgsc_key( $row['target_url'] );
        if ( $login_exception ) { $row['exact_guest_login_redirect'] = true; if ( ! tezgsc20261110_login_exception( $key, $row + array( 'target_key' => '/inquiry/' ) ) ) { throw new RuntimeException( 'Unapproved login query shape.' ); } }
        $legacy_exception = '410' === $row['action'] && 'exact_legacy_sample' === ( $row['implementation'] ?? '' ) && in_array( $key, array( '/wp-content/themes/flatsome/*', '/wp-content/plugins/gravityforms', '/wp-content/plugins/gravityforms/images' ), true );
        if ( $legacy_exception ) { $row['exact_legacy_sample'] = true; }
        $feed_exception = '301' === $row['action'] && 'exact_obsolete_feed' === ( $row['implementation'] ?? '' ) && '404' === $row['observed_initial_status'] && preg_match( '#^/(?:download|en/blog)/.+/feed/$#', tzgsc_path( $key ) ) && ! str_contains( $key, '?' );
        if ( $feed_exception ) { $row['exact_obsolete_feed'] = true; }
        if ( tzgsc_protected( $key ) && ! $login_exception && ! $legacy_exception && ! $feed_exception ) { throw new RuntimeException( 'Protected endpoint or asset cannot be changed: ' . $key ); }
        $actions = explode( '|', $row['action'] );
        if ( array_diff( $actions, array( '301', '410', 'restore', 'unblock', 'remove_noindex' ) ) || ( count( $actions ) > 1 && array( 'unblock', 'remove_noindex' ) !== $actions ) ) { throw new RuntimeException( 'Unsupported GSC action combination.' ); }
        if ( in_array( '301', $actions, true ) ) {
            $target = tzgsc_key( $row['target_url'] );
            if ( '/' === $target || str_starts_with( $target, '//' ) || str_contains( $target, '?' ) || $target === $key || tzgsc_protected( $target ) ) { throw new RuntimeException( 'Unsafe redirect target.' ); }
            $row['target_key'] = $target;
        }
        if ( in_array( 'unblock', $actions, true ) && ( str_contains( $key, '*' ) || str_contains( $key, '$' ) || '/' === $key ) ) { throw new RuntimeException( 'Robots exceptions must name one exact public path.' ); }
        if ( in_array( 'restore', $actions, true ) ) {
            if ( isset( $catalog[ $key ] ) ) { $row['managed_page'] = $catalog[ $key ]; $row['body'] = $catalog[ $key ]['body']; $row['content_file'] = $catalog[ $key ]['content_file']; }
            elseif ( ! empty( $row['content_file'] ) ) {
                $file = realpath( $package . '/' . $row['content_file'] );
                if ( ! $file || ! str_starts_with( $file, realpath( $package ) . '/' ) ) { throw new RuntimeException( 'Restore content missing or outside package.' ); }
                $body = file_get_contents( $file );
                if ( str_contains( $body, "\u{200C}" ) || preg_match( '#<script\b#i', $body ) || ! trim( strip_tags( $body ) ) ) { throw new RuntimeException( 'Restore requires reviewed, nonempty content without scripts or half spaces.' ); }
                $row['body'] = $body;
            } elseif ( empty( $row['post_id'] ) ) { throw new RuntimeException( 'Restore requires a real post ID or reviewed local content.' ); }
        }
        $row['key'] = $key; $row['actions'] = $actions;
        if ( isset( $plan['decisions'][ $key ] ) ) {
            $prior = $plan['decisions'][ $key ];
            foreach ( array( 'action', 'target_url', 'expected_status', 'expected_canonical', 'post_id', 'content_file' ) as $field ) { if ( ( $prior[ $field ] ?? '' ) !== ( $row[ $field ] ?? '' ) ) { throw new RuntimeException( 'Conflicting decisions for ' . $key ); } }
        } else { $plan['decisions'][ $key ] = $row; }
    }
    if ( 'links' === $batch ) {
        foreach ( tzseo_csv( $package . '/audit/internal-redirect-links.csv' ) as $row ) {
            $source = tzgsc_key( $row['source_url'] ); $old = tzgsc_key( $row['old_url'] ); $new = tzgsc_key( $row['new_url'] );
            if ( tzgsc_protected( $old ) || tzgsc_protected( $new ) || str_contains( $source, '?' ) || '/' === $new || $old === $new ) { throw new RuntimeException( 'Unsafe internal href rewrite.' ); }
            $plan['href_rewrites'][ $source ][ $old ] = $new;
        }
    }
    return $plan;
}
function tzgsc_find_post( $row ) {
    $config = get_option( TZGSC_OPTION, array() );
    if ( ! empty( $config['restored'][ $row['key'] ]['id'] ) ) {
        $owned = $config['restored'][ $row['key'] ]; $id = (int) $owned['id'];
        if ( get_post_meta( $id, TZGSC_MANAGED, true ) !== $owned['ownership_token'] ) { throw new RuntimeException( 'Restored page ownership changed.' ); }
        return $id;
    }
    $id = (int) ( $row['post_id'] ?? 0 );
    if ( $id ) {
        $snapshot = tzseo_snapshot( $id );
        if ( ! $snapshot || ! in_array( $snapshot['post']['post_type'], array( 'post', 'page', 'download', 'services' ), true ) ) { throw new RuntimeException( 'Reviewed post ID is missing or unsupported.' ); }
        return $id;
    }
    return tzseo_existing( tzgsc_path( $row['key'] ) );
}
function tzgsc_database_originals( $before ) {
    if ( tzseo_option_snapshot( TZGSC_OPTION ) !== $before['option'] ) { return false; }
    foreach ( $before['posts'] as $id => $post ) { if ( tzseo_snapshot( (int) $id ) !== $post ) { return false; } }
    foreach ( $before['new_pages'] ?? array() as $slug => $path ) { if ( tzseo_existing( '/' . $slug . '/' ) ) { return false; } }
    return true;
}
function tzgsc_originals( $before ) { return tzgsc_database_originals( $before ) && tzgsc_robots_snapshot() === ( $before['robots_file'] ?? null ); }
function tzgsc_robots_snapshot() {
    $path = ABSPATH . 'robots.txt';
    if ( is_link( $path ) || ( file_exists( $path ) && ! is_file( $path ) ) ) { throw new RuntimeException( 'Refusing a symlink or non-file robots.txt.' ); }
    if ( ! file_exists( $path ) ) { return null; }
    $bytes = file_get_contents( $path ); if ( false === $bytes ) { throw new RuntimeException( 'Cannot read physical robots.txt.' ); }
    return array( 'bytes_base64' => base64_encode( $bytes ), 'mode' => fileperms( $path ) & 0777 );
}
function tzgsc_robots_write( $snapshot ) {
    if ( null === $snapshot ) { return; } // A virtual robots.txt remains virtual.
    $path = ABSPATH . 'robots.txt'; $temp = tempnam( ABSPATH, '.tezgsc-robots-' );
    try {
        if ( false === file_put_contents( $temp, base64_decode( $snapshot['bytes_base64'], true ), LOCK_EX ) || ! chmod( $temp, $snapshot['mode'] ) || ! rename( $temp, $path ) ) { throw new RuntimeException( 'Atomic robots.txt write failed.' ); }
    } finally { if ( is_file( $temp ) ) { unlink( $temp ); } }
}
function tzgsc_guard( $before, $state ) {
    global $wpdb;
    if ( $before['site'] !== untrailingslashit( home_url( '/' ) ) ) { throw new RuntimeException( 'GSC backup belongs to another site.' ); }
    $database_original = tzgsc_database_originals( $before );
    if ( ! $database_original && tzseo_option_snapshot( TZGSC_OPTION ) !== $state['option_after'] ) { throw new RuntimeException( 'A later GSC batch or owner edit exists. Roll back batches in reverse order.' ); }
    $file = tzgsc_robots_snapshot();
    $owns_robots_change = ( $state['robots_after'] ?? null ) !== ( $before['robots_file'] ?? null );
    if ( $owns_robots_change && $file !== ( $before['robots_file'] ?? null ) && $file !== ( $state['robots_after'] ?? null ) ) { throw new RuntimeException( 'Physical robots.txt has newer owner edits; refusing overwrite.' ); }
    if ( $database_original ) { return; }
    if ( tzseo_option_snapshot( TZGSC_OPTION ) !== $state['option_after'] ) { throw new RuntimeException( 'A later GSC batch or owner edit exists. Roll back batches in reverse order.' ); }
    foreach ( $state['posts'] as $id => $hash ) { if ( tzseo_hash( tzseo_snapshot( (int) $id ) ) !== $hash ) { throw new RuntimeException( 'A GSC-managed post has newer edits. Refusing overwrite.' ); } }
    foreach ( $state['created_ids'] ?? array() as $id ) {
        if ( get_post_meta( $id, TZGSC_CREATED, true ) !== $before['fingerprint'] ) { throw new RuntimeException( 'Created restoration ownership changed; refusing deletion.' ); }
        if ( in_array( (int) $id, array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ) ), true ) ) { throw new RuntimeException( 'A created restoration is now assigned to newer reading settings; refusing deletion.' ); }
        $children = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_parent = %d", $id ) );
        $comments = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_post_ID = %d", $id ) );
        if ( $children || $comments ) { throw new RuntimeException( 'A created restoration has newer children, attachments, revisions or comments; refusing deletion.' ); }
    }
}
function tzgsc_rules( $package, $enabled ) {
    $rules = array();
    if ( is_readable( $package . '/audit/url-status.csv' ) ) {
        foreach ( tzseo_csv( $package . '/audit/url-status.csv' ) as $observed ) {
            if ( ! str_contains( strtolower( $observed['content_type'] ?? '' ), 'text/html' ) ) { continue; }
            try { $observed_key = tzgsc_key( $observed['url'] ); } catch ( RuntimeException $e ) { continue; }
            if ( tzgsc_protected( $observed_key ) ) { continue; }
            $rules[ $observed_key ] = array( 'status' => (int) $observed['initial_status'], 'canonical' => $observed['canonical'], 'indexable' => str_contains( $observed['robots_meta'], 'noindex' ) ? '0' : ( str_contains( $observed['robots_meta'], 'index' ) ? '1' : 'unknown' ) );
        }
    }
    foreach ( tzgsc_decision_rows( $package ) as $row ) {
        $key = tzgsc_key( $row['request_url'] ); $active = isset( $enabled[ $key ] );
        $status = $active ? (int) $row['expected_status'] : (int) $row['observed_initial_status'];
        $canonical = $row['expected_canonical'] ?? '';
        $indexable = $row['expected_indexable'] ?? 'unknown';
        if ( ! $active && 'keep' !== $row['action'] && str_contains( $row['action'], 'remove_noindex' ) ) { $indexable = '0'; }
        if ( 'unknown' === $indexable || '' === $indexable ) { $indexable = str_contains( $row['expected_robots'] ?? '', 'noindex' ) ? '0' : 'unknown'; }
        $rules[ $key ] = array( 'status' => $status, 'canonical' => $canonical, 'indexable' => $indexable );
    }
    return $rules;
}
function tzgsc_run( $args ) {
    global $wpdb;
    if ( 4 !== count( $args ) ) { throw new RuntimeException( 'Expected mode, package path, backup path, and batch.' ); }
    [ $mode, $package, $backup, $batch ] = $args;
    if ( ! in_array( $mode, array( 'apply', 'apply-dry-run', 'rollback', 'rollback-dry-run' ), true ) ) { throw new RuntimeException( 'Invalid GSC mode.' ); }
    $dry = str_ends_with( $mode, '-dry-run' ); $rollback = str_starts_with( $mode, 'rollback' );
    if ( ! $rollback ) {
        $plan = tzgsc_plan( $package, $batch ); $fingerprint = tzseo_hash( $plan );
        foreach ( $plan['decisions'] as &$row ) {
            if ( array_intersect( $row['actions'], array( 'remove_noindex', 'restore' ) ) ) {
                $id = tzgsc_find_post( $row );
                if ( ! $id ) {
                    if ( ! in_array( 'restore', $row['actions'], true ) || empty( $row['managed_page'] ) ) { throw new RuntimeException( 'Missing original pages require reviewed restoration metadata and content.' ); }
                    $slug = tzgsc_internal_slug( $row['key'] );
                    if ( tzseo_existing( '/' . $slug . '/' ) || tzseo_existing( '/' . $slug . '__trashed/' ) || tezgsc20261110_route_path_exists( '/' . $slug ) || tezgsc20261110_route_path_exists( rtrim( $row['key'], '/' ) ) ) { throw new RuntimeException( 'Restoration URL or internal slug collides with an existing resource.' ); }
                    $row['create_slug'] = $slug; continue;
                }
                $snapshot = tzseo_snapshot( $id );
                if ( in_array( 'restore', $row['actions'], true ) && ! empty( $row['managed_page'] ) && tzseo_existing( $row['key'] ) !== $id && get_post_meta( $id, TZGSC_ORIGINAL, true ) !== $row['key'] ) { throw new RuntimeException( 'Reviewed post ID does not own the original restoration URL.' ); }
                if ( 'private' === $snapshot['post']['post_status'] || ! empty( $snapshot['post']['post_password'] ) ) { throw new RuntimeException( 'Private or password-protected content must remain private.' ); }
                if ( in_array( 'restore', $row['actions'], true ) && empty( trim( strip_tags( $row['body'] ?? $snapshot['post']['post_content'] ) ) ) ) { throw new RuntimeException( 'Cannot publish an empty restoration.' ); }
                if ( in_array( 'remove_noindex', $row['actions'], true ) && ! in_array( 'restore', $row['actions'], true ) && 'publish' !== $snapshot['post']['post_status'] ) { throw new RuntimeException( 'Noindex removal requires an existing published post.' ); }
                $row['resolved_id'] = $id;
            }
        }
        unset( $row );
        $base_dependency = get_option( TZSEO_OPTION, array() );
        $restore_dependencies = tzgsc_catalog( $package ); $active_restores = get_option( TZGSC_OPTION, array() );
        $dependency_targets = array_column( array_values( $plan['decisions'] ), 'target_key' );
        foreach ( $plan['href_rewrites'] as $mapping ) { $dependency_targets = array_merge( $dependency_targets, array_values( $mapping ) ); }
        foreach ( array_unique( $dependency_targets ) as $target ) {
            if ( isset( $restore_dependencies[ $target ] ) ) {
                $restored = $active_restores['restored'][ $target ] ?? array(); $post = ! empty( $restored['id'] ) ? tzseo_snapshot( $restored['id'] ) : null;
                if ( ! $post || 'publish' !== $post['post']['post_status'] || get_post_meta( $restored['id'], TZGSC_MANAGED, true ) !== ( $restored['ownership_token'] ?? '' ) ) { throw new RuntimeException( 'Apply the restoration batch first: missing published owned original URL: ' . $target ); }
            }
            if ( in_array( $target, array( '/student-projects/', '/thesis/master/' ), true ) ) {
                $id = tzseo_existing( $target ); $snapshot = $id ? tzseo_snapshot( $id ) : null;
                if ( empty( $base_dependency['enabled'] ) || ! $snapshot || 'publish' !== $snapshot['post']['post_status'] || $id !== (int) ( $base_dependency['pages'][ $target ]['id'] ?? 0 ) ) { throw new RuntimeException( 'Apply the base metadata/content package first: planned target is not a published package-owned page: ' . $target ); }
            }
        }
    }
    $backup = tzseo_backup_dir( $backup, $dry ); $site = untrailingslashit( home_url( '/' ) );
    $before_file = $backup . '/before.json'; $state_file = $backup . '/state.json';
    if ( $dry ) {
        if ( $rollback ) { $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file ); if ( 'from-backup' !== $batch && $batch !== $before['batch'] ) { throw new RuntimeException( 'Requested rollback batch does not match the backup.' ); } tzgsc_guard( $before, $state ); }
        WP_CLI::log( $rollback ? 'DRY RUN: GSC rollback validated; zero writes.' : 'DRY RUN: GSC batch ' . $batch . ', ' . count( $plan['decisions'] ) . ' URL decisions and ' . count( $plan['href_rewrites'] ) . ' href source pages; zero writes.' ); return;
    }
    $lock = fopen( $backup . '/lock', 'c' ); chmod( $backup . '/lock', 0600 );
    if ( ! flock( $lock, LOCK_EX | LOCK_NB ) ) { throw new RuntimeException( 'GSC backup is in use.' ); }
    $lock_name = 'tezseo20261110-' . substr( hash( 'sha256', $site ), 0, 32 );
    if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', $lock_name ) ) ) { flock( $lock, LOCK_UN ); fclose( $lock ); throw new RuntimeException( 'Another GSC operation is running.' ); }
    $transaction = false; $robots_changed = false; $failure_robots = null; $written_robots = null;
    try {
        if ( $rollback ) {
            $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file ); if ( 'from-backup' !== $batch && $batch !== $before['batch'] ) { throw new RuntimeException( 'Requested rollback batch does not match the backup.' ); } tzgsc_guard( $before, $state );
            if ( ! empty( $state['rolled_back'] ) || tzgsc_originals( $before ) ) { $state['rolled_back'] = true; tzseo_write( $state_file, $state ); WP_CLI::success( 'GSC batch already restored; no database changes.' ); return; }
            $failure_robots = tzgsc_robots_snapshot();
            tzseo_begin(); $transaction = true;
            foreach ( $state['created_ids'] ?? array() as $created_id ) { if ( ! wp_delete_post( $created_id, true ) ) { throw new RuntimeException( 'Created restoration removal failed.' ); } }
            foreach ( $before['posts'] as $id => $saved ) {
                $row = $saved['post']; unset( $row['ID'] );
                if ( false === $wpdb->update( $wpdb->posts, $row, array( 'ID' => (int) $id ) ) || false === $wpdb->delete( $wpdb->postmeta, array( 'post_id' => (int) $id ) ) ) { throw new RuntimeException( 'GSC post restoration failed.' ); }
                foreach ( $saved['meta'] as $key => $values ) { foreach ( $values as $value ) { if ( false === $wpdb->insert( $wpdb->postmeta, array( 'post_id' => (int) $id, 'meta_key' => $key, 'meta_value' => $value ) ) ) { throw new RuntimeException( 'GSC metadata restoration failed.' ); } } }
                clean_post_cache( (int) $id ); wp_cache_delete( (int) $id, 'post_meta' );
            }
            tzseo_restore_option( TZGSC_OPTION, $before['option'] );
            if ( ( $state['robots_after'] ?? null ) !== ( $before['robots_file'] ?? null ) && tzgsc_robots_snapshot() !== ( $before['robots_file'] ?? null ) ) { $written_robots = $before['robots_file']; tzgsc_robots_write( $written_robots ); $robots_changed = true; }
            if ( false === $wpdb->query( 'COMMIT' ) ) { throw new RuntimeException( 'GSC rollback commit failed.' ); } $transaction = false;
            tzseo_invalidate_cache( array_merge( array_keys( $before['posts'] ), array_keys( $state['posts'] ) ), array( TZGSC_OPTION ) );
            $state['rolled_back'] = true; tzseo_write( $state_file, $state ); WP_CLI::success( 'Restored prior GSC batch option and all backed-up post fields/meta.' ); return;
        }
        if ( is_file( $before_file ) ) {
            $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file );
            if ( $before['site'] !== $site || $before['fingerprint'] !== $fingerprint || $before['batch'] !== $batch ) { throw new RuntimeException( 'Backup is for another GSC batch or revision.' ); }
            if ( ! empty( $state['rolled_back'] ) ) { throw new RuntimeException( 'Batch was rolled back; use a new backup directory.' ); }
            if ( empty( $state['complete'] ) || tzgsc_database_originals( $before ) ) { throw new RuntimeException( 'Incomplete prior batch. Roll it back before retrying.' ); }
            tzgsc_guard( $before, $state ); WP_CLI::success( 'Identical GSC batch already applied; no changes.' ); return;
        }
        $config = get_option( TZGSC_OPTION, array() );
        if ( ! is_array( $config ) ) { throw new RuntimeException( 'Unexpected existing GSC option type.' ); }
        if ( isset( $config['applied_batches'][ $batch ] ) ) { throw new RuntimeException( 'Batch already applied with another backup directory; use that original backup.' ); }
        // Project-only recovery uses existing live targets and is independently reversible.
        // New money-page dependencies above still require the owned base pages.
        $base = get_option( TZSEO_OPTION, array() );
        if ( 'project' !== $batch && empty( $base['enabled'] ) ) { throw new RuntimeException( 'Apply the reviewed metadata/content package before GSC batches.' ); }
        $before = array( 'version' => 1, 'site' => $site, 'batch' => $batch, 'fingerprint' => $fingerprint, 'created_utc' => gmdate( 'c' ), 'posts' => array(), 'new_pages' => array(), 'option' => tzseo_option_snapshot( TZGSC_OPTION ), 'robots_file' => tzgsc_robots_snapshot() );
        foreach ( $plan['decisions'] as $row ) { if ( ! empty( $row['resolved_id'] ) ) { $before['posts'][ $row['resolved_id'] ] = tzseo_snapshot( $row['resolved_id'] ); } if ( ! empty( $row['create_slug'] ) ) { $before['new_pages'][ $row['create_slug'] ] = $row['key']; } }
        tzseo_write( $before_file, $before ); file_put_contents( $backup . '/before.sha256', hash_file( 'sha256', $before_file ) . "\n", LOCK_EX ); chmod( $backup . '/before.sha256', 0600 );
        $state = array( 'complete' => false, 'rolled_back' => false, 'posts' => array(), 'created_ids' => array(), 'option_after' => $before['option'], 'robots_after' => $before['robots_file'] ); tzseo_write( $state_file, $state );
        tzseo_begin(); $transaction = true;
        remove_action( 'post_updated', 'wp_save_post_revision' ); remove_action( 'wp_after_insert_post', 'wp_save_post_revision_on_insert', 9 );
        foreach ( $plan['decisions'] as $key => $row ) {
            $id = $row['resolved_id'] ?? 0;
            if ( ! empty( $row['create_slug'] ) ) {
                $page = $row['managed_page'];
                $result = wp_insert_post( wp_slash( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_parent' => 0, 'post_name' => $row['create_slug'], 'post_title' => $page['title'], 'post_content' => $page['body'], 'post_excerpt' => $page['description'] ) ), true );
                if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
                $id = (int) $result; $current_id = $id; $state['created_ids'][] = $id;
                $state['posts'][ $id ] = tzseo_hash( tzseo_snapshot( $id ) ); tzseo_write( $state_file, $state );
                update_post_meta( $id, TZGSC_CREATED, $fingerprint );
                if ( get_post_meta( $id, TZGSC_CREATED, true ) !== $fingerprint ) { throw new RuntimeException( 'Created restoration ownership marker write failed.' ); }
            }
            if ( $id ) {
                if ( in_array( 'restore', $row['actions'], true ) && empty( $row['managed_page'] ) ) {
                    $post = array( 'ID' => $id, 'post_status' => 'publish' ); if ( isset( $row['body'] ) ) { $post['post_content'] = $row['body']; }
                    $result = wp_update_post( wp_slash( $post ), true ); if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
                }
                if ( ! empty( $row['managed_page'] ) ) {
                    $page = $row['managed_page'];
                    if ( empty( $row['create_slug'] ) ) {
                        $updated = wp_update_post( wp_slash( array( 'ID' => $id, 'post_title' => $page['title'], 'post_content' => $page['body'], 'post_excerpt' => $page['description'], 'post_status' => 'publish' ) ), true );
                        if ( is_wp_error( $updated ) ) { throw new RuntimeException( $updated->get_error_message() ); }
                    }
                    $written = tzseo_snapshot( $id );
                    if ( $written['post']['post_title'] !== $page['title'] || $written['post']['post_content'] !== $page['body'] || $written['post']['post_excerpt'] !== $page['description'] || 'publish' !== $written['post']['post_status'] ) { throw new RuntimeException( 'Restored post content write verification failed.' ); }
                    foreach ( array( TZGSC_MANAGED => $fingerprint, TZGSC_ORIGINAL => $row['key'], '_tez_seo20261110_faq' => $page['faq'], '_yoast_wpseo_title' => $page['seo_title'], '_yoast_wpseo_metadesc' => $page['description'], '_yoast_wpseo_focuskw' => $page['keyword'] ?? '', '_yoast_wpseo_canonical' => home_url( $row['key'] ), 'rank_math_title' => $page['seo_title'], 'rank_math_description' => $page['description'], 'rank_math_focus_keyword' => $page['keyword'] ?? '', 'rank_math_canonical_url' => home_url( $row['key'] ) ) as $field => $value ) {
                        update_post_meta( $id, $field, wp_slash( $value ) ); if ( get_post_meta( $id, $field, true ) !== $value ) { throw new RuntimeException( 'Restored metadata verification failed: ' . $field ); }
                    }
                    unset( $page['body'], $page['faq'] ); $page['id'] = $id; $page['ownership_token'] = $fingerprint;
                    $page['internal_path'] = ! empty( $row['create_slug'] ) ? '/' . $row['create_slug'] . '/' : ( $config['restored'][ $row['key'] ]['internal_path'] ?? '' );
                    $config['restored'][ $row['key'] ] = $page;
                    if ( $page['internal_path'] ) { $config['internal_aliases'][ $page['internal_path'] ] = $row['key']; }
                    $slash_alias = str_ends_with( $row['key'], '/' ) ? rtrim( $row['key'], '/' ) : $row['key'] . '/';
                    if ( $slash_alias && $slash_alias !== $row['key'] ) { $config['internal_aliases'][ $slash_alias ] = $row['key']; }
                    $row['resolved_id'] = $id;
                }
                if ( in_array( 'remove_noindex', $row['actions'], true ) || ( in_array( 'restore', $row['actions'], true ) && '1' === ( $row['expected_indexable'] ?? '' ) ) ) {
                    update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '2' );
                    $robots = get_post_meta( $id, 'rank_math_robots', true ); $robots = is_array( $robots ) ? $robots : array();
                    $robots = array_values( array_unique( array_merge( array_diff( $robots, array( 'noindex' ) ), array( 'index' ) ) ) );
                    update_post_meta( $id, 'rank_math_robots', wp_slash( $robots ) );
                    if ( '2' !== get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true ) || $robots !== get_post_meta( $id, 'rank_math_robots', true ) ) { throw new RuntimeException( 'GSC indexing metadata verification failed.' ); }
                }
                $state['posts'][ $id ] = tzseo_hash( tzseo_snapshot( $id ) ); tzseo_write( $state_file, $state );
            }
            unset( $row['body'], $row['managed_page'] ); $config['decisions'][ $key ] = $row;
        }
        foreach ( $plan['href_rewrites'] as $source => $mapping ) { $config['href_rewrites'][ $source ] = array_replace( $config['href_rewrites'][ $source ] ?? array(), $mapping ); }
        foreach ( $config['decisions'] ?? array() as $key => $row ) { if ( isset( $row['target_key'], $config['decisions'][ $row['target_key'] ] ) && in_array( '301', $config['decisions'][ $row['target_key'] ]['actions'], true ) ) { throw new RuntimeException( 'GSC redirect chain in reviewed map.' ); } }
        $config['sitemap_rules'] = tzgsc_rules( $package, $config['decisions'] ?? array() ); $config['enabled'] = true; $config['package'] = '20261110'; $config['applied_batches'][ $batch ] = $fingerprint;
        update_option( TZGSC_OPTION, $config, false ); $readback = tzseo_option_snapshot( TZGSC_OPTION );
        if ( empty( $readback['exists'] ) || maybe_unserialize( $readback['raw'] ) !== $config ) { throw new RuntimeException( 'GSC option verification failed.' ); }
        $state['option_after'] = $readback;
        $batch_allows = tezgsc20261110_allows( array( 'decisions' => $plan['decisions'] ) );
        if ( null !== $before['robots_file'] && $batch_allows && '1' === (string) get_option( 'blog_public' ) ) {
            $state['robots_after'] = array( 'bytes_base64' => base64_encode( tezgsc20261110_robots_merge( base64_decode( $before['robots_file']['bytes_base64'], true ), $batch_allows ) ), 'mode' => $before['robots_file']['mode'] );
        }
        $state['complete'] = true; tzseo_write( $state_file, $state );
        if ( $state['robots_after'] !== $before['robots_file'] ) { $failure_robots = $before['robots_file']; $written_robots = $state['robots_after']; tzgsc_robots_write( $written_robots ); $robots_changed = true; }
        if ( false === $wpdb->query( 'COMMIT' ) ) { throw new RuntimeException( 'GSC batch commit failed.' ); } $transaction = false;
        tzseo_invalidate_cache( array_merge( array_keys( $before['posts'] ), array_keys( $state['posts'] ) ), array( TZGSC_OPTION ) );
        WP_CLI::success( 'Applied staged GSC batch ' . $batch . '; immutable private backup retained. No deployment or cache purge.' );
    } finally { if ( $transaction ) { $wpdb->query( 'ROLLBACK' ); tzseo_invalidate_cache( array_merge( array( $current_id ?? 0 ), array_keys( $before['posts'] ?? array() ), array_keys( $state['posts'] ?? array() ) ), array( TZGSC_OPTION ) ); if ( $robots_changed && tzgsc_robots_snapshot() === $written_robots ) { tzgsc_robots_write( $failure_robots ); } } $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) ); flock( $lock, LOCK_UN ); fclose( $lock ); }
}
if ( ! defined( 'TZGSC_TESTING' ) ) { try { tzgsc_run( $args ); } catch ( Throwable $e ) { WP_CLI::error( $e->getMessage() ); } }
