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
        $thesis = wp_insert_post( array( 'post_name' => 'thesis', 'post_parent' => 0, 'post_title' => 'مسیر پژوهش', 'post_content' => 'محتوای تایید شده', 'post_type' => 'page', 'post_status' => 'publish' ) );
        $master = wp_insert_post( array( 'post_name' => 'master', 'post_parent' => $thesis, 'post_title' => 'پژوهش ارشد', 'post_content' => 'محتوای تایید شده', 'post_type' => 'page', 'post_status' => 'publish' ) );
        $student = wp_insert_post( array( 'post_name' => 'student-projects', 'post_parent' => 0, 'post_title' => 'پروژه درسی', 'post_content' => 'محتوای تایید شده', 'post_type' => 'page', 'post_status' => 'publish' ) );
        update_option( TZSEO_OPTION, array( 'enabled' => true, 'pages' => array( '/thesis/master/' => array( 'id' => $master ), '/student-projects/' => array( 'id' => $student ) ), 'redirects' => array( '/service-project/' => '/student-projects/' ) ) );
        update_option( 'blog_public', '1' );
        if ( $existing_option ) { update_option( TZGSC_OPTION, array( 'enabled' => false, 'owner_data' => 'preserve original' ), true ); }
        $baseline = $GLOBALS['db'];
        $robots = file_get_contents( $package . '/audit/snapshots/robots.txt' ); file_put_contents( ABSPATH . 'robots.txt', $robots ); chmod( ABSPATH . 'robots.txt', 0640 );
        foreach ( array( 'redirects', 'gone', 'indexing', 'links' ) as $batch ) {
            $backup = $test_root . '/actual-' . (int) $existing_option . '-' . $batch;
            $mutations = $GLOBALS['mutations'];
            tzgsc_run( array( 'apply-dry-run', $package, $backup, $batch ) ); check( $mutations === $GLOBALS['mutations'] && ! file_exists( $backup ), 'frozen package dry-run is read-only' );
            tzgsc_run( array( 'apply', $package, $backup, $batch ) );
            $mutations = $GLOBALS['mutations']; $file_after = file_get_contents( ABSPATH . 'robots.txt' );
            tzgsc_run( array( 'apply', $package, $backup, $batch ) ); check( $mutations === $GLOBALS['mutations'] && $file_after === file_get_contents( ABSPATH . 'robots.txt' ), 'frozen package repeat batch is no-op' );
        }
        $applied = get_option( TZGSC_OPTION );
        check( 171 === count( $applied['decisions'] ) && 5 === count( $applied['href_rewrites'] ), 'frozen runtime has only 165 redirects, four gone and two unblock decisions plus five href sources' );
        check( str_contains( file_get_contents( ABSPATH . 'robots.txt' ), 'Allow: /order/$' ) && str_contains( file_get_contents( ABSPATH . 'robots.txt' ), 'Allow: /order/?service$' ), 'frozen physical robots exact allowances are present' );
        foreach ( array( 'links', 'indexing', 'gone', 'redirects' ) as $batch ) {
            $backup = $test_root . '/actual-' . (int) $existing_option . '-' . $batch;
            tzgsc_run( array( 'rollback-dry-run', $package, $backup, 'from-backup' ) );
            tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) );
        }
        check( $baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'frozen package reverse rollback restores exact DB and robots baseline' );
    }
    echo "PASS: frozen 387-row package dry-run/apply/repeat/reverse rollback across all four batches, with absent and preexisting GSC options.\n";
} finally {
    $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $test_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ); foreach ( $it as $file ) { $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); } rmdir( $test_root );
}
