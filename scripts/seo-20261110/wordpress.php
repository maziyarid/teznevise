<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * WP-CLI eval-file only. No network requests, configuration edits, or deployment.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) { throw new RuntimeException( 'Run with WP-CLI eval-file.' ); }

const TZSEO_OPTION = 'teznevise_onpage_20261110';
const TZSEO_MARKER = '_tez_seo20261110_package';

function tzseo_json( $path ) {
    $data = json_decode( file_get_contents( $path ), true, 512, JSON_THROW_ON_ERROR );
    if ( ! is_array( $data ) ) { throw new RuntimeException( 'Invalid JSON: ' . $path ); }
    return $data;
}
function tzseo_write( $path, $value ) {
    $temp = $path . '.tmp-' . getmypid();
    if ( false === file_put_contents( $temp, json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR ), LOCK_EX ) ) { throw new RuntimeException( 'Cannot write ' . $temp ); }
    chmod( $temp, 0600 );
    if ( ! rename( $temp, $path ) ) { throw new RuntimeException( 'Cannot atomically publish ' . $path ); }
}
function tzseo_read_before( $path ) {
    $digest = trim( file_get_contents( dirname( $path ) . '/before.sha256' ) );
    if ( ! hash_equals( $digest, hash_file( 'sha256', $path ) ) ) { throw new RuntimeException( 'Immutable baseline checksum mismatch.' ); }
    return tzseo_json( $path );
}
function tzseo_begin() {
    global $wpdb;
    foreach ( array( $wpdb->posts, $wpdb->postmeta, $wpdb->options ) as $table ) {
        $row = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS WHERE Name = %s', $table ), ARRAY_A );
        if ( ! $row || 'InnoDB' !== $row['Engine'] ) { throw new RuntimeException( 'Transactional InnoDB posts, postmeta and options tables are required.' ); }
    }
    if ( false === $wpdb->query( 'START TRANSACTION' ) ) { throw new RuntimeException( 'Cannot start database transaction.' ); }
}
function tzseo_invalidate_cache( $ids, $option_names ) {
    foreach ( array_unique( array_filter( array_map( 'intval', $ids ) ) ) as $id ) { clean_post_cache( $id ); wp_cache_delete( $id, 'post_meta' ); }
    foreach ( $option_names as $name ) { wp_cache_delete( $name, 'options' ); }
    wp_cache_delete( 'alloptions', 'options' ); wp_cache_delete( 'notoptions', 'options' );
}
function tzseo_hash( $value ) { return hash( 'sha256', serialize( $value ) ); }
function tzseo_snapshot( $id ) {
    global $wpdb;
    $post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID = %d", $id ), ARRAY_A );
    if ( ! $post ) { return null; }
    // Read raw rows: persistent object-cache state can outlive a killed SQL transaction.
    $meta = array();
    $rows = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d ORDER BY meta_id", $id ), ARRAY_A );
    if ( null === $rows && $wpdb->last_error ) { throw new RuntimeException( 'Cannot back up post metadata.' ); }
    foreach ( $rows as $row ) { $meta[ $row['meta_key'] ][] = $row['meta_value']; }
    ksort( $meta );
    return array( 'post' => $post, 'meta' => $meta );
}
function tzseo_option_snapshot( $name ) {
    global $wpdb;
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value, autoload FROM {$wpdb->options} WHERE option_name = %s", $name ), ARRAY_A );
    return $row ? array( 'exists' => true, 'raw' => $row['option_value'], 'autoload' => $row['autoload'] ) : array( 'exists' => false );
}
function tzseo_restore_option( $name, $saved ) {
    global $wpdb;
    if ( $saved['exists'] ) {
        $existing = tzseo_option_snapshot( $name );
        if ( $existing['exists'] ) { if ( false === $wpdb->update( $wpdb->options, array( 'option_value' => $saved['raw'], 'autoload' => $saved['autoload'] ), array( 'option_name' => $name ) ) ) { throw new RuntimeException( 'Option restore failed.' ); } }
        else { if ( false === $wpdb->insert( $wpdb->options, array( 'option_name' => $name, 'option_value' => $saved['raw'], 'autoload' => $saved['autoload'] ) ) ) { throw new RuntimeException( 'Option restore failed.' ); } }
    } else { delete_option( $name ); }
    wp_cache_delete( $name, 'options' ); wp_cache_delete( 'alloptions', 'options' ); wp_cache_delete( 'notoptions', 'options' );
}
function tzseo_csv( $path ) {
    if ( ! is_readable( $path ) ) { throw new RuntimeException( 'Missing reviewed CSV: ' . $path ); }
    $f = fopen( $path, 'r' ); $header = fgetcsv( $f, 0, ',', '"', '' ); $rows = array();
    while ( false !== ( $row = fgetcsv( $f, 0, ',', '"', '' ) ) ) {
        if ( array( null ) === $row ) { continue; }
        if ( count( $header ) !== count( $row ) ) { throw new RuntimeException( 'Malformed CSV: ' . $path ); }
        $rows[] = array_combine( $header, $row );
    }
    fclose( $f ); return $rows;
}
function tzseo_path( $url ) {
    $p = parse_url( $url );
    if ( false === $p || isset( $p['query'] ) || isset( $p['fragment'] ) || isset( $p['user'] ) || isset( $p['pass'] ) || isset( $p['port'] ) ) { throw new RuntimeException( 'Expected clean URL: ' . $url ); }
    if ( isset( $p['host'] ) && ! in_array( strtolower( $p['host'] ), array( 'teznevise.ir', 'www.teznevise.ir' ), true ) ) { throw new RuntimeException( 'External URL not allowed: ' . $url ); }
    $path = rawurldecode( $p['path'] ?? '/' );
    if ( ! str_starts_with( $path, '/' ) || str_contains( $path, '..' ) || str_contains( $path, "\0" ) ) { throw new RuntimeException( 'Unsafe path: ' . $url ); }
    return '/' === $path ? '/' : '/' . trim( $path, '/' ) . '/';
}
function tzseo_plan( $package ) {
    $manifest = tzseo_json( $package . '/manifest.json' );
    $pages = $manifest['pages'] ?? $manifest;
    $plan = array( 'enabled' => true, 'package' => '20261110', 'pages' => array(), 'redirects' => array(), 'links' => array(), 'external_sources' => array(), 'anchor_rewrites' => array() );
    foreach ( $pages as $page ) {
        foreach ( array( 'path', 'title', 'seo_title', 'description' ) as $key ) { if ( empty( $page[ $key ] ) ) { throw new RuntimeException( 'Missing page ' . $key ); } }
        $path = tzseo_path( $page['path'] ); $page['path'] = $path;
        if ( isset( $plan['pages'][ $path ] ) ) { throw new RuntimeException( 'Duplicate target path.' ); }
        if ( ! empty( $page['content_file'] ) ) {
            $file = realpath( $package . '/' . $page['content_file'] );
            if ( ! $file || ! str_starts_with( $file, realpath( $package ) . '/' ) ) { throw new RuntimeException( 'Content escapes package.' ); }
            $body = file_get_contents( $file );
            if ( str_contains( $body, "\u{200C}" ) ) { throw new RuntimeException( 'Half space in new content.' ); }
            preg_match_all( '#<script\b[^>]*type=[\'\"]application/ld\+json[\'\"][^>]*>(.*?)</script>#si', $body, $matches );
            if ( '/' !== $path ) {
                if ( 1 !== count( $matches[1] ) ) { throw new RuntimeException( 'Expected one FAQ schema per money page.' ); }
                $faq = json_decode( $matches[1][0], true, 512, JSON_THROW_ON_ERROR );
                if ( 'FAQPage' !== ( $faq['@type'] ?? '' ) || count( $faq['mainEntity'] ?? array() ) < 2 ) { throw new RuntimeException( 'Invalid FAQ.' ); }
                $page['faq'] = $faq;
            } elseif ( count( $matches[1] ) ) { throw new RuntimeException( 'Homepage resource copy must not contain FAQ schema.' ); }
            $page['body'] = trim( preg_replace( '#<script\b[^>]*>.*?</script>#si', '', $body ) );
            if ( preg_match( '#<script\b#i', $page['body'] ) ) { throw new RuntimeException( 'Script in body.' ); }
        }
        $plan['pages'][ $path ] = $page;
    }
    foreach ( tzseo_csv( $package . '/audit/redirect-map.csv' ) as $row ) {
        $old = tzseo_path( $row['old_url'] ); $new = tzseo_path( $row['new_url'] );
        if ( $old === $new || '/' === $old ) { throw new RuntimeException( 'Unsafe redirect.' ); }
        if ( isset( $plan['redirects'][ $old ] ) && $new !== $plan['redirects'][ $old ] ) { throw new RuntimeException( 'Conflicting redirects.' ); }
        $plan['redirects'][ $old ] = $new;
    }
    foreach ( $plan['redirects'] as $target ) { if ( isset( $plan['redirects'][ $target ] ) ) { throw new RuntimeException( 'Redirect chain or loop in map.' ); } }
    foreach ( tzseo_csv( $package . '/audit/internal-links.csv' ) as $row ) {
        $source = tzseo_path( $row['source_url'] ); $target = tzseo_path( $row['target_url'] );
        if ( ! isset( $plan['pages'][ $target ] ) || empty( trim( $row['anchor'] ) ) ) { throw new RuntimeException( 'Invalid contextual link target.' ); }
        $plan['links'][ $source ][] = array( 'target' => $target, 'anchor' => $row['anchor'], 'reason' => $row['reason'] ?? '' );
    }
    foreach ( tzseo_csv( $package . '/audit/exact-anchor-rewrites.csv' ) as $row ) {
        $source = tzseo_path( $row['source_url'] ); $target = tzseo_path( $row['target_url'] );
        if ( empty( $row['old_anchor'] ) || empty( $row['new_anchor'] ) || ! isset( $plan['pages'][ $target ] ) ) { throw new RuntimeException( 'Invalid existing-anchor rewrite.' ); }
        $plan['anchor_rewrites'][ $source ][] = array( 'target' => $target, 'old_anchor' => $row['old_anchor'], 'new_anchor' => $row['new_anchor'] );
    }
    foreach ( tzseo_csv( $package . '/audit/bluethesis-links.csv' ) as $row ) {
        $target = parse_url( $row['target_url'] );
        if ( ! in_array( strtolower( $target['host'] ?? '' ), array( 'bluethesis.ir', 'www.bluethesis.ir' ), true ) || '/statistical-analysis' !== rtrim( $target['path'] ?? '', '/' ) ) { throw new RuntimeException( 'Unexpected external target.' ); }
        if ( ! in_array( $row['action'], array( 'nofollow', 'remove' ), true ) ) { throw new RuntimeException( 'Unknown external action.' ); }
        $plan['external_sources'][ tzseo_path( $row['source_url'] ) ] = $row['action'];
    }
    uksort( $plan['pages'], static fn( $a, $b ) => substr_count( $a, '/' ) <=> substr_count( $b, '/' ) ?: strcmp( $a, $b ) );
    return $plan;
}
function tzseo_existing( $path ) {
    if ( '/' === $path ) { return (int) get_option( 'page_on_front' ); }
    $post = get_page_by_path( trim( $path, '/' ), OBJECT, 'page' );
    return $post ? (int) $post->ID : 0;
}
function tzseo_backup_dir( $backup, $dry ) {
    if ( ! str_starts_with( $backup, '/' ) ) { throw new RuntimeException( 'Absolute backup directory required.' ); }
    $parent = realpath( dirname( $backup ) );
    $resolved = realpath( $backup );
    if ( ! $resolved ) { if ( ! $parent ) { throw new RuntimeException( 'Backup parent directory must already exist.' ); } $resolved = $parent . '/' . basename( $backup ); }
    $root = realpath( ABSPATH );
    $document_root = ! empty( $_SERVER['DOCUMENT_ROOT'] ) ? realpath( $_SERVER['DOCUMENT_ROOT'] ) : false;
    if ( $document_root && ( $resolved === $document_root || str_starts_with( $resolved, $document_root . '/' ) ) ) { throw new RuntimeException( 'Backup must be outside the served document root.' ); }
    if ( $resolved === $root || str_starts_with( $resolved, $root . '/' ) ) { throw new RuntimeException( 'Backup must be outside the web root.' ); }
    if ( is_link( $backup ) ) { throw new RuntimeException( 'Backup symlink refused.' ); }
    if ( ! $dry && ! is_dir( $backup ) && ! mkdir( $backup, 0700 ) ) { throw new RuntimeException( 'Cannot create private backup.' ); }
    if ( is_dir( $backup ) && 0 !== ( fileperms( $backup ) & 0077 ) ) { throw new RuntimeException( 'Backup directory must have mode 0700.' ); }
    return $resolved;
}
function tzseo_run( $args ) {
    global $wpdb;
    if ( count( $args ) !== 3 ) { throw new RuntimeException( 'Expected mode, package path, private backup path.' ); }
    [ $mode, $package, $backup ] = $args;
    if ( ! in_array( $mode, array( 'apply', 'apply-dry-run', 'rollback', 'rollback-dry-run' ), true ) ) { throw new RuntimeException( 'Invalid mode.' ); }
    $dry = str_ends_with( $mode, '-dry-run' ); $rollback = str_starts_with( $mode, 'rollback' );
    if ( $rollback ) { $gsc = get_option( 'teznevise_gsc_20261110', array() ); if ( ! empty( $gsc['enabled'] ) && ! empty( $gsc['applied_batches'] ) ) { throw new RuntimeException( 'Roll back all GSC batches in reverse order before the base metadata/content package.' ); } }
    $backup = tzseo_backup_dir( $backup, $dry );
    $site = untrailingslashit( home_url( '/' ) );
    // Never read credentials or write configuration. Owner chooses WordPress root via WP-CLI.
    if ( ! $rollback ) {
        $plan = tzseo_plan( $package ); $fingerprint = tzseo_hash( $plan );
        foreach ( $plan['pages'] as $path => $page ) {
            if ( empty( $page['body'] ) ) { continue; }
            if ( '/' === $path && ( ! tzseo_existing( '/' ) || 'page' !== get_option( 'show_on_front' ) ) ) { throw new RuntimeException( 'Homepage content requires an existing static front page; configure it before applying.' ); }
            $parent = '/' === $path ? '.' : dirname( trim( $path, '/' ) );
            if ( '.' !== $parent && ! isset( $plan['pages'][ '/' . $parent . '/' ] ) && ! tzseo_existing( '/' . $parent . '/' ) ) { throw new RuntimeException( 'Missing parent page: ' . $path ); }
        }
    }
    $before_file = $backup . '/before.json'; $state_file = $backup . '/state.json';
    if ( $dry ) {
        if ( $rollback ) {
            if ( ! is_readable( $before_file ) || ! is_readable( $state_file ) ) { throw new RuntimeException( 'No backup state available.' ); }
            $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file ); tzseo_check_rollback( $before, $state, $site );
            WP_CLI::log( 'DRY RUN: rollback would restore ' . count( $before['posts'] ) . ' original posts and the prior package option; no writes.' );
        } else {
            WP_CLI::log( 'DRY RUN: ' . count( $plan['pages'] ) . ' metadata targets, ' . count( $plan['redirects'] ) . ' redirects, ' . count( $plan['links'] ) . ' contextual link source pages; no writes or backups created.' );
            foreach ( $plan['pages'] as $path => $page ) { WP_CLI::log( ( tzseo_existing( $path ) ? 'UPDATE ' : ( '/' === $path ? 'HOME METADATA ' : 'CREATE ' ) ) . $path ); }
        }
        return;
    }
    $transaction = false;
    $lock = fopen( $backup . '/lock', 'c' ); chmod( $backup . '/lock', 0600 );
    if ( ! flock( $lock, LOCK_EX | LOCK_NB ) ) { throw new RuntimeException( 'Package is already running.' ); }
    $db_lock_name = 'tezseo20261110-' . substr( hash( 'sha256', $site ), 0, 32 );
    if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', $db_lock_name ) ) ) { flock( $lock, LOCK_UN ); fclose( $lock ); throw new RuntimeException( 'Another SEO apply/rollback is running for this site.' ); }
    try {
        if ( $rollback ) {
            $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file ); tzseo_check_rollback( $before, $state, $site );
            if ( ! empty( $state['rolled_back'] ) || tzseo_originals_unchanged( $before ) ) {
                $state['rolled_back'] = true; tzseo_write( $state_file, $state ); WP_CLI::success( 'Original state already restored; no database changes.' ); return;
            }
            tzseo_begin(); $transaction = true;
            foreach ( $state['posts'] as $path => $entry ) {
                $id = $entry['id']; $original = $before['posts'][ $path ]['snapshot'];
                if ( null === $original ) {
                    // Full snapshot guard above confirms the page is still package-owned and unchanged.
                    if ( ! wp_delete_post( $id, true ) ) { throw new RuntimeException( 'New page removal failed.' ); }
                } else {
                    $row = $original['post']; unset( $row['ID'] );
                    if ( false === $wpdb->update( $wpdb->posts, $row, array( 'ID' => $id ) ) ) { throw new RuntimeException( 'Post restore failed.' ); }
                    if ( false === $wpdb->delete( $wpdb->postmeta, array( 'post_id' => $id ) ) ) { throw new RuntimeException( 'Metadata restore failed.' ); }
                    foreach ( $original['meta'] as $key => $values ) { foreach ( $values as $value ) {
                        if ( false === $wpdb->insert( $wpdb->postmeta, array( 'post_id' => $id, 'meta_key' => $key, 'meta_value' => $value ) ) ) { throw new RuntimeException( 'Metadata restore failed.' ); }
                    } }
                    clean_post_cache( $id ); wp_cache_delete( $id, 'post_meta' );
                }
            }
            tzseo_restore_option( TZSEO_OPTION, $before['option'] );
            if ( false === $wpdb->query( 'COMMIT' ) ) { throw new RuntimeException( 'Rollback commit failed.' ); } $transaction = false;
            tzseo_invalidate_cache( array_merge( array_column( $before['posts'], 'id' ), array_column( $state['posts'], 'id' ) ), array( TZSEO_OPTION ) );
            $state['rolled_back'] = true; tzseo_write( $state_file, $state );
            WP_CLI::success( 'Restored original posts/meta and package option. Kept private backups.' ); return;
        }
        if ( is_file( $before_file ) ) {
            $before = tzseo_read_before( $before_file ); $state = tzseo_json( $state_file );
            if ( $before['site'] !== $site || $before['fingerprint'] !== $fingerprint ) { throw new RuntimeException( 'Backup belongs to another site or package revision; use a new private directory.' ); }
            if ( ! empty( $state['rolled_back'] ) ) { throw new RuntimeException( 'Already rolled back. Start a new apply with a new backup directory.' ); }
            if ( ! empty( $state['complete'] ) ) {
                if ( tzseo_originals_unchanged( $before ) ) { throw new RuntimeException( 'Prior transaction did not commit. Run rollback to close this backup, then use a new directory.' ); }
                tzseo_check_rollback( $before, $state, $site );
                WP_CLI::success( 'Identical package is already applied; no changes.' ); return;
            }
            throw new RuntimeException( 'Incomplete prior apply: run rollback with this backup before retrying. No originals overwritten.' );
        }
        $already = get_option( TZSEO_OPTION, array() );
        if ( is_array( $already ) && ! empty( $already['enabled'] ) && '20261110' === ( $already['package'] ?? '' ) ) { throw new RuntimeException( 'This package is already enabled. Use its original backup directory for repeat apply or rollback.' ); }
        $before = array( 'version' => 1, 'site' => $site, 'fingerprint' => $fingerprint, 'created_utc' => gmdate( 'c' ), 'posts' => array(), 'option' => tzseo_option_snapshot( TZSEO_OPTION ), 'redirect_options_read_only' => array() );
        // Read-only snapshots of existing plugin redirect stores. They are never changed or restored by this package.
        foreach ( array( 'wpseo-premium-redirects-base', 'wpseo-premium-redirects-export-plain', 'wpseo-premium-redirects-export-regex', 'rank_math_redirections', 'redirection_options' ) as $name ) { $before['redirect_options_read_only'][ $name ] = tzseo_option_snapshot( $name ); }
        foreach ( $plan['pages'] as $path => $page ) {
            if ( empty( $page['body'] ) ) { continue; }
            $id = tzseo_existing( $path );
            $before['posts'][ $path ] = array( 'id' => $id, 'snapshot' => $id ? tzseo_snapshot( $id ) : null );
        }
        // Immutable baseline is saved before the first database mutation.
        tzseo_write( $before_file, $before ); file_put_contents( $backup . '/before.sha256', hash_file( 'sha256', $before_file ) . "\n", LOCK_EX ); chmod( $backup . '/before.sha256', 0600 );
        $state = array( 'complete' => false, 'rolled_back' => false, 'posts' => array(), 'option_after' => $before['option'] ); tzseo_write( $state_file, $state );
        tzseo_begin(); $transaction = true;
        remove_action( 'post_updated', 'wp_save_post_revision' );
        remove_action( 'wp_after_insert_post', 'wp_save_post_revision_on_insert', 9 );
        foreach ( $plan['pages'] as $path => &$page ) {
            $id = tzseo_existing( $path );
            if ( ! empty( $page['body'] ) ) {
                $parent_path = '/' === $path ? '.' : dirname( trim( $path, '/' ) ); $parent = '.' === $parent_path ? 0 : tzseo_existing( '/' . $parent_path . '/' );
                if ( '.' !== $parent_path && ! $parent ) { throw new RuntimeException( 'Missing parent page: ' . $path ); }
                $post = array( 'post_title' => $page['title'], 'post_content' => $page['body'], 'post_excerpt' => $page['description'] );
                if ( '/' !== $path ) { $post = array_merge( $post, array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => basename( trim( $path, '/' ) ), 'post_parent' => $parent ) ); }
                if ( $id ) { $post['ID'] = $id; $result = wp_update_post( wp_slash( $post ), true ); }
                else { $result = wp_insert_post( wp_slash( $post ), true ); }
                if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
                $id = (int) $result;
                update_post_meta( $id, TZSEO_MARKER, $fingerprint );
                if ( '/' !== $path ) {
                    update_post_meta( $id, '_tez_seo20261110_faq', wp_slash( $page['faq'] ) );
                    if ( get_post_meta( $id, '_tez_seo20261110_faq', true ) !== $page['faq'] ) { throw new RuntimeException( 'FAQ metadata write failed.' ); }
                }
                if ( get_post_meta( $id, TZSEO_MARKER, true ) !== $fingerprint ) { throw new RuntimeException( 'Package marker metadata write failed.' ); }
                foreach ( array( '_yoast_wpseo_title' => $page['seo_title'], '_yoast_wpseo_metadesc' => $page['description'], '_yoast_wpseo_focuskw' => $page['keyword'] ?? '', '_yoast_wpseo_canonical' => home_url( $path ), 'rank_math_title' => $page['seo_title'], 'rank_math_description' => $page['description'], 'rank_math_focus_keyword' => $page['keyword'] ?? '', 'rank_math_canonical_url' => home_url( $path ), '_yoast_wpseo_meta-robots-noindex' => '2', '_yoast_wpseo_meta-robots-nofollow' => '0', 'rank_math_robots' => array( 'index', 'follow' ) ) as $key => $value ) { update_post_meta( $id, $key, wp_slash( $value ) ); if ( get_post_meta( $id, $key, true ) !== $value ) { throw new RuntimeException( 'Metadata write verification failed: ' . $key ); } }
                $state['posts'][ $path ] = array( 'id' => $id, 'hash' => tzseo_hash( tzseo_snapshot( $id ) ) ); tzseo_write( $state_file, $state );
            }
            $page['id'] = $id;
            unset( $page['body'], $page['faq'] );
        }
        unset( $page );
        update_option( TZSEO_OPTION, $plan, false );
        $option_readback = tzseo_option_snapshot( TZSEO_OPTION );
        if ( empty( $option_readback['exists'] ) || maybe_unserialize( $option_readback['raw'] ) !== $plan ) { throw new RuntimeException( 'Package option write verification failed.' ); }
        $state['option_after'] = tzseo_option_snapshot( TZSEO_OPTION ); $state['complete'] = true; tzseo_write( $state_file, $state );
        // Journal is durable before commit; a pre-commit crash leaves the untouched baseline.
        if ( false === $wpdb->query( 'COMMIT' ) ) { throw new RuntimeException( 'Database commit failed.' ); } $transaction = false;
        tzseo_invalidate_cache( array_merge( array_column( $before['posts'], 'id' ), array_column( $state['posts'], 'id' ) ), array( TZSEO_OPTION ) );
        WP_CLI::success( 'SEO package applied. Original posts/meta and prior redirect option retained in private backup. No cache purge or deployment performed.' );
    } finally { if ( $transaction ) {
        $wpdb->query( 'ROLLBACK' );
        $cache_ids = array( $id ?? 0 );
        foreach ( $before['posts'] ?? array() as $entry ) { $cache_ids[] = $entry['id']; }
        foreach ( $state['posts'] ?? array() as $entry ) { $cache_ids[] = $entry['id']; }
        tzseo_invalidate_cache( $cache_ids, array( TZSEO_OPTION ) );
    } $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $db_lock_name ) ); flock( $lock, LOCK_UN ); fclose( $lock ); }
}
function tzseo_originals_unchanged( $before ) {
    if ( tzseo_option_snapshot( TZSEO_OPTION ) !== $before['option'] ) { return false; }
    foreach ( $before['posts'] as $path => $entry ) {
        if ( null === $entry['snapshot'] ) { if ( tzseo_existing( $path ) ) { return false; } }
        elseif ( tzseo_snapshot( $entry['id'] ) !== $entry['snapshot'] ) { return false; }
    }
    return true;
}
function tzseo_check_rollback( $before, $state, $site ) {
    global $wpdb;
    if ( $before['site'] !== $site ) { throw new RuntimeException( 'Backup is for another site.' ); }
    if ( ! empty( $state['rolled_back'] ) || tzseo_originals_unchanged( $before ) ) { return; }
    if ( tzseo_option_snapshot( TZSEO_OPTION ) !== $state['option_after'] ) { throw new RuntimeException( 'Package option changed since apply. Refusing to overwrite newer work.' ); }
    foreach ( $state['posts'] as $path => $entry ) {
        if ( tzseo_hash( tzseo_snapshot( $entry['id'] ) ) !== $entry['hash'] ) { throw new RuntimeException( 'A managed page changed since apply. Refusing to overwrite newer edits; inspect the private backup.' ); }
        if ( null === $before['posts'][ $path ]['snapshot'] ) {
            if ( in_array( (int) $entry['id'], array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ) ), true ) ) { throw new RuntimeException( 'A package-created page is assigned to newer reading settings; refusing deletion.' ); }
            $children = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_parent = %d", $entry['id'] ) );
            $comments = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_post_ID = %d", $entry['id'] ) );
            if ( $children || $comments ) { throw new RuntimeException( 'A package-created page has newer comments, revisions, attachments or child posts. Refusing deletion.' ); }
        }
    }
}
if ( ! defined( 'TZSEO_TESTING' ) ) {
    try { tzseo_run( $args ); } catch ( Throwable $e ) { WP_CLI::error( $e->getMessage() ); }
}
