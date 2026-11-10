<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Isolated GSC batches and runtime contracts. No WordPress config/install.
 */
define( 'TZSEO_MOCKS_ONLY', true ); define( 'TZGSC_TESTING', true );
require __DIR__ . '/test-wordpress.php';
require __DIR__ . '/gsc.php';
$package = $test_root . '/gsc-package'; mkdir( $package, 0700 ); mkdir( $package . '/audit', 0700 ); mkdir( $package . '/content', 0700 );
function gsc_test_csv( $path, $rows ) { $f = fopen( $path, 'w' ); fputcsv( $f, array_keys( $rows[0] ), ',', '"', '' ); foreach ( $rows as $row ) { fputcsv( $f, array_values( $row ), ',', '"', '' ); } fclose( $f ); }
function gsc_test_row( $id, $url, $action, $batch, $target = '', $post_id = '', $implementation = 'runtime' ) {
    return array( 'row_id' => $id, 'request_url' => home_url( $url ), 'source_bucket' => 'fixture', 'action' => $action, 'target_url' => $target ? home_url( $target ) : '', 'batch' => $batch, 'reason' => 'Reviewed fixture.', 'observed_initial_status' => '404', 'observed_final_status' => '404', 'expected_status' => '301' === $action ? '301' : ( '410' === $action ? '410' : '200' ), 'expected_canonical' => '410' === $action ? '' : home_url( $target ?: $url ), 'expected_robots' => 'allow', 'post_id' => $post_id, 'content_file' => '', 'implementation' => $implementation, 'unresolved_reason' => '', 'expected_robots_allowed' => '1', 'expected_indexable' => '410' === $action ? '0' : '1', 'post_type' => 'page', 'robots_rule' => '' );
}
try {
    $useful = wp_insert_post( array( 'post_name' => 'valuable-guide', 'post_parent' => 0, 'post_title' => 'راهنمای آموزشی', 'post_content' => 'متن اصلی قابل نگهداری', 'post_type' => 'page', 'post_status' => 'publish', 'post_password' => '' ) );
    $draft = wp_insert_post( array( 'post_name' => 'restored-guide', 'post_parent' => 0, 'post_title' => 'محتوای بررسی شده', 'post_content' => 'محتوای واقعی موجود', 'post_type' => 'page', 'post_status' => 'draft', 'post_password' => '' ) );
    update_post_meta( $useful, '_yoast_wpseo_meta-robots-noindex', '1' ); update_post_meta( $useful, 'rank_math_robots', array( 'noindex', 'nofollow', 'max-image-preview:large' ) );
    update_option( TZSEO_OPTION, array( 'enabled' => true, 'redirects' => array( '/service-project/' => '/student-projects/' ) ) ); update_option( 'blog_public', '1' );
    $rows = array(
        gsc_test_row( 'r1', '/old-guide/', '301', 'redirects', '/valuable-guide/' ),
        gsc_test_row( 'r2', '/junk/?raw=literal|pipe', '410', 'gone' ),
        gsc_test_row( 'r3', '/order/?service', 'unblock', 'indexing' ),
        gsc_test_row( 'r4', '/valuable-guide/', 'remove_noindex', 'indexing', '', $useful ),
        gsc_test_row( 'r5', '/wp-admin/', 'keep', 'none' ),
        gsc_test_row( 'r6', '/wp-login.php?redirect_to=%2Faccount%2F&reauth=1', '301', 'redirects', '/inquiry/', '', 'exact_guest_login_redirect' ),
        gsc_test_row( 'r7', '/wp-content/themes/flatsome/*', '410', 'gone', '', '', 'exact_legacy_sample' ),
        gsc_test_row( 'r8', '//statistics/', '301', 'redirects', '/service-statistics/' ),
        gsc_test_row( 'r9', '/download/old-resource/feed/', '301', 'redirects', '/download/current-resource/', '', 'exact_obsolete_feed' ),
        gsc_test_row( 'r10', '/restored-guide/', 'restore', 'indexing', '', $draft ),
    );
    gsc_test_csv( $package . '/audit/gsc-decisions-20261110.csv', $rows );
    gsc_test_csv( $package . '/audit/internal-redirect-links.csv', array( array( 'source_url' => home_url( '/public-post/' ), 'old_url' => home_url( '/old-guide/' ), 'new_url' => home_url( '/valuable-guide/' ), 'reason' => 'Final HTML target.', 'batch' => 'links' ) ) );
    $robots = "User-agent: *\nDisallow: /wp-admin/\nDisallow: /order/\nUser-agent: Googlebot\nDisallow: /wp-admin/\nDisallow: /order/\n";
    file_put_contents( ABSPATH . 'robots.txt', $robots ); chmod( ABSPATH . 'robots.txt', 0640 ); $baseline = $GLOBALS['db'];
    $count = $GLOBALS['mutations']; tzgsc_run( array( 'apply-dry-run', $package, $test_root . '/dry', 'indexing' ) ); check( ! file_exists( $test_root . '/dry' ) && $count === $GLOBALS['mutations'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'GSC dry run has zero DB/filesystem writes' );
    foreach ( array( 'redirects', 'gone', 'indexing', 'links' ) as $batch ) { tzgsc_run( array( 'apply', $package, $test_root . '/batch-' . $batch, $batch ) ); }
    expect_error( static fn() => tzseo_run( array( 'rollback-dry-run', $package, $test_root . '/not-a-base-backup' ) ), 'GSC batches in reverse order' );
    $count = $GLOBALS['mutations']; tzgsc_run( array( 'apply', $package, $test_root . '/batch-links', 'links' ) ); check( $count === $GLOBALS['mutations'], 'GSC repeat batch no-op' );
    check( 'publish' === $GLOBALS['db']['posts'][ $draft ]['post_status'], 'restores real reviewed draft content' );
    check( in_array( 'nofollow', get_post_meta( $useful, 'rank_math_robots', true ), true ) && ! in_array( 'noindex', get_post_meta( $useful, 'rank_math_robots', true ), true ), 'noindex removal preserves nofollow and unrelated directives' );
    $changed_robots = file_get_contents( ABSPATH . 'robots.txt' ); check( 2 === substr_count( $changed_robots, 'Allow: /order/?service$' ) && 2 === substr_count( $changed_robots, 'Disallow: /wp-admin/' ) && 2 === substr_count( $changed_robots, 'Disallow: /order/' ), 'physical robots adds only exact reviewed allowances in all groups' );
    clearstatcache(); check( 0640 === ( fileperms( ABSPATH . 'robots.txt' ) & 0777 ), 'physical robots mode preserved' );
    check( $changed_robots === tezgsc20261110_robots_merge( $changed_robots, array( '/order/?service' ) ), 'robots merge is idempotent' );
    check( tezgsc20261110_route_row( '/junk/?raw=literal|pipe', 'GET', false ) && ! tezgsc20261110_route_row( '/junk/?raw=other', 'GET', false ) && ! tezgsc20261110_route_row( '/junk/?raw=literal|pipe', 'POST', false ), 'query 410 is exact and GET/HEAD only' );
    check( ! tezgsc20261110_route_path_exists( '//statistics/' ) && '//statistics/' === tezgsc20261110_path( '//statistics/' ) && tezgsc20261110_route_row( '//statistics/', 'HEAD', false ), 'double slash source retained exactly' );
    $login = '/wp-login.php?redirect_to=%2Faccount%2F&reauth=1'; check( tezgsc20261110_route_row( $login, 'GET', false ) && ! tezgsc20261110_route_row( $login, 'GET', true ) && ! tezgsc20261110_route_row( '/wp-login.php', 'GET', false ), 'only sampled guest login query redirected' );
    check( tezgsc20261110_route_row( '/wp-content/themes/flatsome/*', 'GET', false ) && ! tezgsc20261110_route_row( '/wp-content/themes/flatsome/real.css', 'GET', false ), 'legacy literal wildcard is not expanded' );
    check( ! tezgsc20261110_route_row( '/wp-json/', 'GET', false ) && ! tezgsc20261110_route_row( '/?s=query', 'GET', false ), 'REST and search unaffected' );
    $_SERVER['REQUEST_URI'] = '/public-post/';
    $html = '<a class="link" href="/old-guide/#method">Guide</a> <a download href="/old-guide/">File</a> <a href="https://external.ir/old-guide/">External</a> <a href="/wp-content/uploads/test.pdf">PDF</a>';
    $after = tezgsc20261110_html( $html ); check( str_contains( $after, 'href="https://teznevise.ir/valuable-guide/#method"' ) && str_contains( $after, '<a download href="/old-guide/">' ) && str_contains( $after, 'https://external.ir/old-guide/' ), 'href rewrite preserves fragments, download attributes and external URLs' ); check( $after === tezgsc20261110_html( $after ), 'href rewrite idempotent' );
    $config = get_option( TZGSC_OPTION ); $config['sitemap_rules']['/wrong-canonical/'] = array( 'status' => 200, 'canonical' => home_url( '/valuable-guide/' ), 'indexable' => '1' ); $config['sitemap_rules']['/observed-404/'] = array( 'status' => 404, 'canonical' => '', 'indexable' => '0' ); update_option( TZGSC_OPTION, $config );
    check( ! tezgsc20261110_sitemap_eligible( home_url( '/old-guide/' ) ) && ! tezgsc20261110_sitemap_eligible( home_url( '/service-project/' ) ) && ! tezgsc20261110_sitemap_eligible( home_url( '/wrong-canonical/' ) ) && ! tezgsc20261110_sitemap_eligible( home_url( '/observed-404/' ) ), 'sitemap excludes GSC/base redirects, crosscanonicals and observed errors' );
    check( tezgsc20261110_sitemap_eligible( home_url( '/valuable-guide/' ), (object) $GLOBALS['db']['posts'][ $useful ] ), 'public self-canonical indexable post eligible' );
    $canonical_fixture = get_option( TZGSC_OPTION );
    foreach ( array( 'http://teznevise.ir/valuable-guide/', 'https://www.teznevise.ir/valuable-guide/' ) as $mismatch ) {
        $canonical_fixture['sitemap_rules']['/valuable-guide/'] = array( 'status' => 200, 'canonical' => $mismatch, 'indexable' => '1' ); update_option( TZGSC_OPTION, $canonical_fixture );
        check( ! tezgsc20261110_sitemap_eligible( home_url( '/valuable-guide/' ), (object) $GLOBALS['db']['posts'][ $useful ] ), 'offline canonical scheme/host mismatch excluded' );
    }
    $canonical_fixture['sitemap_rules']['/valuable-guide/']['canonical'] = 'https://teznevise.ir/valuable-guide/'; update_option( TZGSC_OPTION, $canonical_fixture );
    $GLOBALS['mock_home_origin'] = 'https://staging.example.test';
    check( tezgsc20261110_sitemap_eligible( home_url( '/valuable-guide/' ), (object) $GLOBALS['db']['posts'][ $useful ] ), 'production offline self-canonical evidence translates only its origin on staging' );
    $saved_meta = $GLOBALS['db']['meta'][ $useful ];
    update_post_meta( $useful, 'rank_math_canonical_url', 'https://teznevise.ir/valuable-guide/' );
    check( ! tezgsc20261110_sitemap_eligible( home_url( '/valuable-guide/' ), (object) $GLOBALS['db']['posts'][ $useful ] ), 'actual production canonical on staging is not self-canonical' );
    update_post_meta( $useful, 'rank_math_canonical_url', home_url( '/valuable-guide/' ) );
    check( tezgsc20261110_sitemap_eligible( home_url( '/valuable-guide/' ), (object) $GLOBALS['db']['posts'][ $useful ] ), 'actual staging self-canonical accepted' );
    $GLOBALS['db']['meta'][ $useful ] = $saved_meta; $GLOBALS['mock_home_origin'] = 'https://teznevise.ir'; update_option( TZGSC_OPTION, $config );
    $xml = '<?xml version="1.0"?><urlset><url><loc>https://teznevise.ir/old-guide/</loc></url><url><loc>https://teznevise.ir/valuable-guide/</loc></url></urlset>';
    $config['sitemap_rules']['/valuable-guide/'] = array( 'status' => 200, 'canonical' => home_url( '/valuable-guide/' ), 'indexable' => '1' );
    update_option( TZGSC_OPTION, $config ); $xml_after = tezgsc20261110_xml( $xml ); check( ! str_contains( $xml_after, '/old-guide/' ) && str_contains( $xml_after, '/valuable-guide/' ), 'custom XML final guard excludes only ineligible URL entries' );
    // Restore exact expected option before exercising the strict rollback chain.
    $latest = tzseo_json( $test_root . '/batch-links/state.json' ); tzseo_restore_option( TZGSC_OPTION, $latest['option_after'] );
    expect_error( static fn() => tzgsc_run( array( 'rollback', $package, $test_root . '/batch-redirects', 'from-backup' ) ), 'reverse order' );
    file_put_contents( ABSPATH . 'robots.txt', "# newer owner edit\n", FILE_APPEND );
    expect_error( static fn() => tzgsc_run( array( 'rollback', $package, $test_root . '/batch-links', 'from-backup' ) ), 'newer owner edits' ); file_put_contents( ABSPATH . 'robots.txt', $changed_robots ); chmod( ABSPATH . 'robots.txt', 0640 );
    foreach ( array( 'links', 'indexing', 'gone', 'redirects' ) as $batch ) { tzgsc_run( array( 'rollback', $package, $test_root . '/batch-' . $batch, 'from-backup' ) ); }
    check( $baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'reverse batch rollback restores exact baseline posts/meta/options/robots bytes' );
    $GLOBALS['fail_commit'] = true; expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/failed-indexing', 'indexing' ) ), 'commit failed' ); $GLOBALS['fail_commit'] = false;
    check( $baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'failed transaction restores physical robots as well as DB' ); check( in_array( array( TZGSC_OPTION, 'options' ), $GLOBALS['cache_deletions'], true ) && in_array( array( $useful, 'post_meta' ), $GLOBALS['cache_deletions'], true ), 'failed GSC transaction invalidates targeted persistent cache' ); tzgsc_run( array( 'rollback', $package, $test_root . '/failed-indexing', 'from-backup' ) );
    tzgsc_run( array( 'apply', $package, $test_root . '/rollback-commit-failure', 'indexing' ) );
    $applied_db = $GLOBALS['db']; $applied_robots = file_get_contents( ABSPATH . 'robots.txt' );
    $GLOBALS['fail_commit'] = true; expect_error( static fn() => tzgsc_run( array( 'rollback', $package, $test_root . '/rollback-commit-failure', 'from-backup' ) ), 'rollback commit failed' ); $GLOBALS['fail_commit'] = false;
    check( $applied_db === $GLOBALS['db'] && $applied_robots === file_get_contents( ABSPATH . 'robots.txt' ), 'failed rollback commit preserves active DB and physical robots state' );
    tzgsc_run( array( 'rollback', $package, $test_root . '/rollback-commit-failure', 'from-backup' ) );
    tzgsc_run( array( 'apply', $package, $test_root . '/abrupt-file-gap', 'indexing' ) );
    $GLOBALS['db'] = $baseline; // Model a killed process: SQL rolled back, atomic robots write survived.
    expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/abrupt-file-gap', 'indexing' ) ), 'Incomplete prior batch' );
    tzgsc_run( array( 'rollback', $package, $test_root . '/abrupt-file-gap', 'from-backup' ) );
    check( $baseline === $GLOBALS['db'] && $robots === file_get_contents( ABSPATH . 'robots.txt' ), 'abrupt pre-commit robots gap recovers exact originals' );
    $GLOBALS['mock_home_origin'] = 'https://staging.example.test';
    check( '/valuable-guide/' === tezgsc20261110_key( 'https://staging.example.test/valuable-guide/' ), 'runtime accepts actual staging home host' );
    $GLOBALS['mock_home_origin'] = 'https://teznevise.ir';
    update_option( TZGSC_OPTION, array( 'enabled' => false, 'owner_setting' => 'preserve original' ), true );
    $custom_option_before = tzseo_option_snapshot( TZGSC_OPTION );
    tzgsc_run( array( 'apply', $package, $test_root . '/custom-option', 'redirects' ) );
    tzgsc_run( array( 'rollback', $package, $test_root . '/custom-option', 'from-backup' ) );
    check( $custom_option_before === tzseo_option_snapshot( TZGSC_OPTION ), 'restores a preexisting GSC option and autoload exactly' );
    unlink( ABSPATH . 'robots.txt' ); tzgsc_run( array( 'apply', $package, $test_root . '/virtual-robots', 'indexing' ) );
    check( ! file_exists( ABSPATH . 'robots.txt' ), 'virtual robots remains virtual' ); tzgsc_run( array( 'rollback', $package, $test_root . '/virtual-robots', 'from-backup' ) );
    $planned_rows = $rows; $planned_rows[0]['target_url'] = home_url( '/student-projects/' );
    gsc_test_csv( $package . '/audit/gsc-decisions-20261110.csv', $planned_rows );
    expect_error( static fn() => tzgsc_run( array( 'apply-dry-run', $package, $test_root . '/missing-base-target', 'redirects' ) ), 'base metadata/content package first' );
    $new_target = wp_insert_post( array( 'post_name' => 'student-projects', 'post_parent' => 0, 'post_title' => 'مسیر پروژه درسی', 'post_content' => 'محتوای مورد تایید مالک', 'post_type' => 'page', 'post_status' => 'publish', 'post_password' => '' ) );
    $base_config = get_option( TZSEO_OPTION ); $base_config['pages']['/student-projects/']['id'] = $new_target; update_option( TZSEO_OPTION, $base_config );
    tzgsc_run( array( 'apply-dry-run', $package, $test_root . '/published-base-target', 'redirects' ) );
    $invalid = $rows; $invalid[0]['target_url'] = home_url( '/' ); gsc_test_csv( $package . '/audit/gsc-decisions-20261110.csv', $invalid ); expect_error( static fn() => tzgsc_plan( $package, 'redirects' ), 'Unsafe redirect target' );
    $invalid = $rows; $invalid[0]['action'] = 'restore'; $invalid[0]['batch'] = 'indexing'; $invalid[0]['content_file'] = 'content/missing.html'; gsc_test_csv( $package . '/audit/gsc-decisions-20261110.csv', $invalid ); expect_error( static fn() => tzgsc_plan( $package, 'indexing' ), 'Restore content missing' );
    echo "PASS: GSC ordered batches, dry runs, immutable backup/reverse rollback, exact query/legacy/login/feed scope, private/assets guards, robots bytes/mode, transaction failure recovery, noindex preservation, hrefs and sitemap eligibility.\n";
} finally {
    $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $test_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ); foreach ( $it as $file ) { $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); } rmdir( $test_root );
}
