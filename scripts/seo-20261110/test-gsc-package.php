<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Exercise the frozen, reviewed package through isolated WP/SQL adapters.
 */
define( 'TZSEO_MOCKS_ONLY', true ); define( 'TZGSC_TESTING', true );
require __DIR__ . '/test-wordpress.php'; require __DIR__ . '/gsc.php';
$package = realpath( __DIR__ . '/../../seo/20261110' );
try {
    foreach ( array( false, true ) as $existing_option ) {
        $GLOBALS['db'] = array( 'posts' => array(), 'meta' => array(), 'options' => array() );
        $manifest = tzseo_json( $package . '/manifest.json' ); $base_pages = $manifest['pages'];
        usort( $base_pages, static fn( $a, $b ) => substr_count( $a['path'], '/' ) <=> substr_count( $b['path'], '/' ) );
        $seeded = array();
        foreach ( $base_pages as $page ) {
            $path = $page['path']; $parent_path = '/' === $path ? '.' : dirname( trim( $path, '/' ) );
            $parent = '.' === $parent_path ? 0 : ( $seeded[ '/' . $parent_path . '/' ]['id'] ?? 0 );
            $id = wp_insert_post( array( 'post_name' => '/' === $path ? 'fixture-front-page' : basename( trim( $path, '/' ) ), 'post_parent' => $parent, 'post_title' => $page['title'], 'post_content' => 'Existing reviewed base package content.', 'post_type' => 'page', 'post_status' => 'publish' ) );
            $page['id'] = $id; $seeded[ $path ] = $page;
            if ( '/' === $path ) { update_option( 'page_on_front', $id ); update_option( 'show_on_front', 'page' ); }
        }
        $base_redirects = array(); foreach ( tzseo_csv( $package . '/audit/redirect-map.csv' ) as $redirect ) { $base_redirects[ tzseo_path( $redirect['old_url'] ) ] = tzseo_path( $redirect['new_url'] ); }
        update_option( TZSEO_OPTION, array( 'enabled' => true, 'pages' => $seeded, 'redirects' => $base_redirects ) );
        update_option( 'blog_public', '1' );
        if ( $existing_option ) { update_option( TZGSC_OPTION, array( 'enabled' => false, 'owner_data' => 'preserve original' ), true ); }
        $baseline = $GLOBALS['db'];
        $robots = file_get_contents( $package . '/audit/snapshots/robots.txt' ); file_put_contents( ABSPATH . 'robots.txt', $robots ); chmod( ABSPATH . 'robots.txt', 0640 );
        foreach ( array( 'restores', 'project', 'redirects', 'gone', 'indexing', 'links' ) as $batch ) {
            $backup = $test_root . '/actual-' . (int) $existing_option . '-' . $batch;
            $mutations = $GLOBALS['mutations'];
            tzgsc_run( array( 'apply-dry-run', $package, $backup, $batch ) ); check( $mutations === $GLOBALS['mutations'] && ! file_exists( $backup ), 'frozen package dry-run is read-only' );
            tzgsc_run( array( 'apply', $package, $backup, $batch ) );
            $mutations = $GLOBALS['mutations']; $file_after = file_get_contents( ABSPATH . 'robots.txt' );
            tzgsc_run( array( 'apply', $package, $backup, $batch ) ); check( $mutations === $GLOBALS['mutations'] && $file_after === file_get_contents( ABSPATH . 'robots.txt' ), 'frozen package repeat batch is no-op' );
        }
        $applied = get_option( TZGSC_OPTION );
        $expected_decisions = array(); foreach ( tzgsc_decision_rows( $package ) as $decision ) { if ( 'keep' !== $decision['action'] && 'none' !== $decision['batch'] ) { $expected_decisions[ tzgsc_key( $decision['request_url'] ) ] = true; } }
        $expected_href_sources = array(); foreach ( tzseo_csv( $package . '/audit/internal-redirect-links.csv' ) as $link ) { $expected_href_sources[ tzgsc_key( $link['source_url'] ) ] = true; }
        check( count( $expected_decisions ) === count( $applied['decisions'] ) && count( $expected_href_sources ) === count( $applied['href_rewrites'] ?? array() ), 'actual runtime includes exactly the reviewed decisions and href sources' );
        $catalog = tzgsc_catalog( $package ); check( count( $catalog ) === count( $applied['restored'] ?? array() ), 'actual restoration count follows source-backed catalog, including an empty catalog' );
        check( str_contains( file_get_contents( ABSPATH . 'robots.txt' ), 'Allow: /order/$' ) && str_contains( file_get_contents( ABSPATH . 'robots.txt' ), 'Allow: /order/?service$' ), 'frozen physical robots exact allowances are present' );
        foreach ( array( 'links', 'indexing', 'gone', 'redirects', 'project', 'restores' ) as $batch ) {
            $backup = $test_root . '/actual-' . (int) $existing_option . '-' . $batch;
            tzgsc_run( array( 'rollback-dry-run', $package, $backup, 'from-backup' ) );
            tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) );
        }
        check( $baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'frozen package reverse rollback restores exact DB and robots baseline' );
    }
    // The documented 84-route recovery must work without applying the wider content package.
    $GLOBALS['db'] = array( 'posts' => array(), 'meta' => array(), 'options' => array() );
    foreach ( tzseo_csv( $package . '/audit/project-dead-targets-20261110.csv' ) as $target ) {
        wp_insert_post( array( 'post_name' => basename( trim( tzgsc_key( $target['target_url'] ), '/' ) ), 'post_parent' => 0, 'post_title' => 'Existing live project destination', 'post_content' => 'Existing owner content, unchanged by project recovery.', 'post_type' => 'page', 'post_status' => 'publish' ) );
    }
    update_option( 'blog_public', '1' );
    $project_baseline = $GLOBALS['db']; $project_backup = $test_root . '/actual-project-without-base';
    file_put_contents( ABSPATH . 'robots.txt', $robots ); chmod( ABSPATH . 'robots.txt', 0640 );
    $mutations = $GLOBALS['mutations'];
    tzgsc_run( array( 'apply-dry-run', $package, $project_backup, 'project' ) );
    check( $mutations === $GLOBALS['mutations'] && ! file_exists( $project_backup ), 'project-only dry run with absent base is read-only' );
    tzgsc_run( array( 'apply', $package, $project_backup, 'project' ) );
    $project_config = get_option( TZGSC_OPTION );
    check( 84 === count( $project_config['decisions'] ) && ! tzseo_option_snapshot( TZSEO_OPTION )['exists'], 'actual 84 project redirects apply without enabling the wider base package' );
    check( $project_baseline['posts'] === $GLOBALS['db']['posts'] && $project_baseline['meta'] === $GLOBALS['db']['meta'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'project-only apply leaves every post, metadata and robots byte unchanged' );
    $mutations = $GLOBALS['mutations'];
    tzgsc_run( array( 'apply', $package, $project_backup, 'project' ) );
    check( $mutations === $GLOBALS['mutations'], 'project-only repeat apply with absent base is a no-op' );
    tzgsc_run( array( 'rollback-dry-run', $package, $project_backup, 'project' ) );
    check( $mutations === $GLOBALS['mutations'], 'project-only rollback dry run has zero database writes' );
    tzgsc_run( array( 'rollback', $package, $project_backup, 'project' ) );
    check( $project_baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'project-only rollback restores exact absent-option baseline without any base apply' );
    echo "PASS: actual reviewed package six-batch contracts and independent 84-route project-only dry-run/apply/repeat/rollback with no base content option.\n";
} finally {
    $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $test_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ); foreach ( $it as $file ) { $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); } rmdir( $test_root );
}
