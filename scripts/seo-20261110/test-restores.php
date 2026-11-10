<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Original-URI restoration without fabricated parent pages; isolated adapters.
 */
define( 'TZSEO_MOCKS_ONLY', true ); define( 'TZGSC_TESTING', true );
require __DIR__ . '/test-wordpress.php'; require __DIR__ . '/gsc.php';
function get_post( $id ) { return isset( $GLOBALS['db']['posts'][ $id ] ) ? (object) $GLOBALS['db']['posts'][ $id ] : null; }
function is_admin() { return false; }
function is_page( $id = null ) { return null === $id || (int) $id === (int) ( $GLOBALS['queried_id'] ?? 0 ); }
function get_queried_object_id() { return $GLOBALS['queried_id'] ?? 0; }
function get_permalink( $post ) { $post = is_object( $post ) ? $post : get_post( $post ); return tezgsc20261110_page_link( home_url( '/' . $post->post_name . '/' ), (int) $post->ID ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function restores_csv( $path, $rows ) { $f = fopen( $path, 'w' ); fputcsv( $f, array_keys( $rows[0] ), ',', '"', '' ); foreach ( $rows as $row ) { fputcsv( $f, array_values( $row ), ',', '"', '' ); } fclose( $f ); }
$package = $test_root . '/restore-package'; mkdir( $package, 0700 ); mkdir( $package . '/audit', 0700 ); mkdir( $package . '/content', 0700 );
$pages = array(); $rows = array();
foreach ( array( '/project/electronics/hspice/', '/project/simulation/lammps/' ) as $i => $path ) {
    $title = 0 === $i ? 'آموزش شبیه سازی مدار' : 'آموزش مدل سازی ماده';
    $faq = array( '@type' => 'FAQPage', 'mainEntity' => array( array( '@type' => 'Question', 'name' => 'پرسش اول', 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => 'پاسخ اول' ) ), array( '@type' => 'Question', 'name' => 'پرسش دوم', 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => 'پاسخ دوم' ) ) ) );
    $file = 'content/restore-' . $i . '.html';
    file_put_contents( $package . '/' . $file, '<h2>راهنمای نرم افزار</h2><p>متن آموزشی بررسی شده با مسئولیت علمی دانشجو.</p><h2>پرسش ها</h2><h3>پرسش اول</h3><p>پاسخ اول</p><h3>پرسش دوم</h3><p>پاسخ دوم</p><script type="application/ld+json">' . json_encode( $faq ) . '</script>' );
    $pages[] = array( 'path' => $path, 'title' => $title, 'seo_title' => $title . ' | تزنویسه', 'description' => 'راهنمای آموزشی بررسی شده', 'keyword' => $title, 'content_file' => $file, 'role' => 'restore' );
    $rows[] = array( 'row_id' => 'restore-' . $i, 'request_url' => home_url( $path ), 'source_bucket' => 'fixture', 'action' => 'restore', 'target_url' => '', 'batch' => 'restores', 'reason' => 'Distinct technical educational intent.', 'observed_initial_status' => '404', 'observed_final_status' => '404', 'expected_status' => '200', 'expected_canonical' => home_url( $path ), 'expected_robots' => 'allow/index', 'post_id' => '', 'content_file' => $file, 'implementation' => 'owned_original_uri', 'unresolved_reason' => '', 'expected_indexable' => '1' );
}
file_put_contents( $package . '/restored-pages.json', json_encode( array( 'pages' => $pages ), JSON_UNESCAPED_UNICODE ) ); restores_csv( $package . '/audit/gsc-decisions-20261110.csv', $rows );
try {
    update_option( TZSEO_OPTION, array( 'enabled' => true, 'pages' => array(), 'redirects' => array() ) ); update_option( 'blog_public', '1' );
    update_option( TZGSC_OPTION, array( 'enabled' => true, 'package' => '20261110', 'decisions' => array( $pages[0]['path'] => array( 'actions' => array( '301' ), 'target_key' => '/student-projects/' ) ), 'applied_batches' => array( 'redirects' => 'older-reviewed-batch' ) ) );
    $baseline = $GLOBALS['db']; $backup = $test_root . '/restores';
    $count = $GLOBALS['mutations']; tzgsc_run( array( 'apply-dry-run', $package, $backup, 'restores' ) ); check( $count === $GLOBALS['mutations'] && ! file_exists( $backup ), 'restore dry run writes nothing' );
    tzgsc_run( array( 'apply', $package, $backup, 'restores' ) );
    $config = get_option( TZGSC_OPTION ); check( 2 === count( $GLOBALS['db']['posts'] ) && 2 === count( $config['restored'] ), 'exactly two leaf pages, no fabricated parents' );
    check( ! tzseo_existing( '/project/' ) && ! tzseo_existing( '/project/electronics/' ), 'hierarchical scaffolding absent' );
    $count = $GLOBALS['mutations']; tzgsc_run( array( 'apply', $package, $backup, 'restores' ) ); check( $count === $GLOBALS['mutations'], 'restore repeat no-op' );
    $first = $config['restored'][ $pages[0]['path'] ]; $id = $first['id']; $GLOBALS['queried_id'] = $id;
    $_SERVER['REQUEST_URI'] = $first['path']; $_SERVER['REQUEST_METHOD'] = 'GET'; $original_uri = $_SERVER['REQUEST_URI'];
    $conflicting = array( 'pagename' => 'wrong/parent', 'name' => 'wrong', 'error' => '404', 'attachment_id' => 9, 'feed' => 'rss2' );
    check( array( 'page_id' => $id, 'post_type' => 'page' ) === tezgsc20261110_request( $conflicting ) && $original_uri === $_SERVER['REQUEST_URI'], 'owned original path resolves queried page without altering request URI' );
    check( array( 'restore' ) === tezgsc20261110_route_row( $first['path'], 'GET', false )['actions'], 'restoration replaces previous broad redirect' );
    check( home_url( $first['path'] ) === get_permalink( $id ) && home_url( '/?page_id=' . $id ) === tezgsc20261110_url_to_postid( home_url( $first['path'] ) ), 'permalink and URL-to-ID roundtrip use original URI' );
    check( $first['path'] === tezgsc20261110_route_row( rtrim( $first['path'], '/' ), 'GET', false )['target_key'], 'slash variant redirects directly to original canonical URI' );
    check( $first['path'] === tezgsc20261110_route_row( $first['internal_path'], 'HEAD', false )['target_key'] && $first['path'] === tezgsc20261110_route_row( '/?page_id=' . $id, 'GET', false )['target_key'], 'generated slug and page-id aliases redirect directly to original URI' );
    check( false === tezgsc20261110_restore_canonical_redirect( home_url( '/wrong/' ) ), 'core canonical redirect disabled only for owned original request' );
    check( tezgsc20261110_sitemap_eligible( home_url( $first['path'] ) ) && ! tezgsc20261110_sitemap_eligible( home_url( $first['internal_path'] ), get_post( $id ) ), 'sitemap emits original restored URL only' );
    require __DIR__ . '/../../inc/seo-onpage-20261110.php';
    check( $id === tezseo20261110_page()['id'] && $first['seo_title'] === tezseo20261110_title( 'Old title' ), 'restoration reuses managed page metadata and rendering' );
    ob_start(); tezseo20261110_faq(); $faq_html = ob_get_clean(); check( 1 === substr_count( $faq_html, '"FAQPage"' ) && ! str_contains( $GLOBALS['db']['posts'][ $id ]['post_content'], '<script' ), 'exactly one FAQ emitted separately from saved visible content' );
    eval( 'namespace RankMath\\Redirections; class Redirections { public function do_redirection() {} }' );
    $rank = new \RankMath\Redirections\Redirections(); $other = new stdClass();
    $wp_filter = array( 'wp' => (object) array( 'callbacks' => array( 11 => array( array( 'function' => array( $rank, 'do_redirection' ) ) ), 12 => array( array( 'function' => array( $other, 'other_redirect' ) ) ) ) ), 'template_redirect' => (object) array( 'callbacks' => array( 11 => array( array( 'function' => array( $rank, 'do_redirection' ) ) ) ) ) );
    tezgsc20261110_prepare_restore(); check( in_array( array( 'wp', array( $rank, 'do_redirection' ), 11 ), $GLOBALS['removed_actions'], true ) && in_array( array( 'template_redirect', array( $rank, 'do_redirection' ), 11 ), $GLOBALS['removed_actions'], true ) && ! in_array( array( 'wp', array( $other, 'other_redirect' ), 12 ), $GLOBALS['removed_actions'], true ), 'plugin redirect suppression matches only known manager on owned restore request' );
    foreach ( array( '/unrelated/', $first['path'] . '?s=search', $first['path'] . '?preview=true', $first['path'] . '?feed=rss2', $first['path'] . '?embed=true', '/wp-json/' ) as $untouched ) { $_SERVER['REQUEST_URI'] = $untouched; check( $conflicting === tezgsc20261110_request( $conflicting ), 'unrelated, search, preview and REST routes remain untouched' ); }
    $_SERVER['REQUEST_URI'] = $first['path']; $_SERVER['REQUEST_METHOD'] = 'POST'; check( $conflicting === tezgsc20261110_request( $conflicting ), 'POST remains untouched' ); $_SERVER['REQUEST_METHOD'] = 'GET';
    $GLOBALS['mock_home_origin'] = 'https://stage.example.test'; check( 'https://stage.example.test' . $first['path'] === get_permalink( $id ), 'restored permalink uses actual staging origin' ); $GLOBALS['mock_home_origin'] = 'https://teznevise.ir';
    $title = $GLOBALS['db']['posts'][ $id ]['post_title']; $GLOBALS['db']['posts'][ $id ]['post_title'] = 'Owner changed this'; expect_error( static fn() => tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) ), 'newer edits' ); $GLOBALS['db']['posts'][ $id ]['post_title'] = $title;
    $GLOBALS['extra_data'] = 1; expect_error( static fn() => tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) ), 'newer children' ); $GLOBALS['extra_data'] = 0;
    tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) ); check( $baseline === $GLOBALS['db'], 'rollback removes only created leaves and restores exact prior redirect decisions/options' );
    $count = $GLOBALS['mutations']; tzgsc_run( array( 'rollback', $package, $backup, 'from-backup' ) ); check( $count === $GLOBALS['mutations'], 'restore rollback repeats safely' );
    // Original source prose can legitimately contain no FAQ; do not invent one.
    $first_file = $package . '/' . $pages[0]['content_file']; $original_copy = file_get_contents( $first_file );
    file_put_contents( $first_file, '<h2>متن اصلی بازیابی شده</h2><p>این متن موجود بدون افزودن پرسش تازه حفظ می شود.</p>' );
    check( array() === tzgsc_catalog( $package )[ $pages[0]['path'] ]['faq'], 'real legacy source without FAQ is accepted without invented questions' ); file_put_contents( $first_file, $original_copy );
    $GLOBALS['fail_after_post'] = true; expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/insert-failure', 'restores' ) ), 'Injected' ); $GLOBALS['fail_after_post'] = false;
    check( $baseline === $GLOBALS['db'], 'metadata failure after insert rolls back new post and original option' ); tzgsc_run( array( 'rollback', $package, $test_root . '/insert-failure', 'from-backup' ) );
    $GLOBALS['fail_meta_key'] = TZGSC_CREATED; expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/false-marker-write', 'restores' ) ), 'ownership marker write failed' );
    check( $baseline === $GLOBALS['db'], 'false-return ownership marker failure rolls back new page and option' ); tzgsc_run( array( 'rollback', $package, $test_root . '/false-marker-write', 'from-backup' ) );
    $GLOBALS['fail_commit'] = true; expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/create-commit-failure', 'restores' ) ), 'commit failed' ); $GLOBALS['fail_commit'] = false;
    check( $baseline === $GLOBALS['db'], 'failed creation commit removes both new leaves and restores prior option' );
    expect_error( static fn() => tzgsc_run( array( 'apply', $package, $test_root . '/create-commit-failure', 'restores' ) ), 'Incomplete prior batch' );
    tzgsc_run( array( 'rollback', $package, $test_root . '/create-commit-failure', 'from-backup' ) );
    check( $baseline === $GLOBALS['db'], 'new-page pre-commit journal recovery safely recognizes original absence' );
    echo "PASS: original-URI restores create no empty parents, preserve public routing/canonical/FAQ, replace old redirects, scope plugin suppression, protect edits/children and roll back created pages exactly.\n";
} finally {
    $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $test_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ); foreach ( $it as $file ) { $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); } rmdir( $test_root );
}
