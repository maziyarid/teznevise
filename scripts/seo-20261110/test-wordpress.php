<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Isolated contract tests: in-memory WordPress/SQL adapters, no WP install/config.
 */
const WP_CLI = true;
const TZSEO_TESTING = true;
const ARRAY_A = 'ARRAY_A';
const OBJECT = 'OBJECT';
$test_root = sys_get_temp_dir() . '/tezseo-contract-' . bin2hex( random_bytes( 5 ) );
mkdir( $test_root, 0700 ); mkdir( $test_root . '/web', 0700 );
define( 'ABSPATH', $test_root . '/web/' );
$GLOBALS['db'] = array( 'posts' => array(), 'meta' => array(), 'options' => array() );
$GLOBALS['next_id'] = 50;
$GLOBALS['mutations'] = 0;
$GLOBALS['fail_after_post'] = false;
class WP_CLI {
    public static function log( $s ) {}
    public static function success( $s ) {}
    public static function error( $s ) { throw new RuntimeException( $s ); }
}
class TestDB {
    public $comments = 'wp_comments'; public $posts = 'wp_posts'; public $postmeta = 'wp_postmeta'; public $options = 'wp_options'; public $last_error = ''; private $transaction;
    public function prepare( $query, ...$args ) { foreach ( $args as $arg ) { $query = preg_replace_callback( '/%[ds]/', static fn( $m ) => '%d' === $m[0] ? (string) (int) $arg : "'" . str_replace( "'", "''", (string) $arg ) . "'", $query, 1 ); } return $query; }
    public function get_results( $sql, $format ) {
        if ( ! preg_match( '/WHERE post_id = (\d+)/', $sql, $m ) ) { throw new RuntimeException( 'Unexpected metadata query.' ); }
        $rows = array(); foreach ( $GLOBALS['db']['meta'][ (int) $m[1] ] ?? array() as $key => $values ) { foreach ( $values as $value ) { $rows[] = array( 'meta_key' => $key, 'meta_value' => $value ); } } return $rows;
    }
    public function get_var( $sql ) { return str_contains( $sql, 'COUNT(*)' ) ? (string) ( $GLOBALS['extra_data'] ?? 0 ) : '1'; }
    public function get_row( $sql, $format ) {
        if ( str_starts_with( $sql, 'SHOW TABLE STATUS' ) ) { return array( 'Engine' => 'InnoDB' ); }
        if ( preg_match( '/FROM wp_posts WHERE ID = (\d+)/', $sql, $m ) ) { return $GLOBALS['db']['posts'][ (int) $m[1] ] ?? null; }
        if ( preg_match( "/option_name = '([^']+)'/", $sql, $m ) ) { return $GLOBALS['db']['options'][ $m[1] ] ?? null; }
        throw new RuntimeException( 'Unexpected SQL: ' . $sql );
    }
    public function query( $sql ) {
        if ( 'START TRANSACTION' === $sql ) { $this->transaction = unserialize( serialize( $GLOBALS['db'] ) ); return 1; }
        if ( 'COMMIT' === $sql ) { if ( ! empty( $GLOBALS['fail_commit'] ) ) { return false; } $this->transaction = null; return 1; }
        if ( 'ROLLBACK' === $sql ) { if ( null !== $this->transaction ) { $GLOBALS['db'] = $this->transaction; } $this->transaction = null; return 1; }
        throw new RuntimeException( 'Unexpected statement.' );
    }
    public function update( $table, $row, $where ) {
        ++$GLOBALS['mutations'];
        if ( 'wp_posts' === $table ) { $id = (int) $where['ID']; $GLOBALS['db']['posts'][ $id ] = array_replace( $GLOBALS['db']['posts'][ $id ], $row ); }
        else { $GLOBALS['db']['options'][ $where['option_name'] ] = $row; }
        return 1;
    }
    public function insert( $table, $row ) { ++$GLOBALS['mutations']; if ( 'wp_postmeta' === $table ) { $GLOBALS['db']['meta'][ $row['post_id'] ][ $row['meta_key'] ][] = $row['meta_value']; return 1; } $GLOBALS['db']['options'][ $row['option_name'] ] = array( 'option_value' => $row['option_value'], 'autoload' => $row['autoload'] ); return 1; }
    public function delete( $table, $where ) { ++$GLOBALS['mutations']; unset( $GLOBALS['db']['meta'][ $where['post_id'] ] ); return 1; }
}
$wpdb = new TestDB();
function home_url( $path = '/' ) { return ( $GLOBALS['mock_home_origin'] ?? 'https://teznevise.ir' ) . $path; }
function untrailingslashit( $s ) { return rtrim( $s, '/' ); }
function get_option( $name, $default = false ) { return isset( $GLOBALS['db']['options'][ $name ] ) ? unserialize( $GLOBALS['db']['options'][ $name ]['option_value'] ) : $default; }
function update_option( $name, $value, $autoload = false ) { ++$GLOBALS['mutations']; $GLOBALS['db']['options'][ $name ] = array( 'option_value' => serialize( $value ), 'autoload' => $autoload ? 'yes' : 'no' ); return true; }
function delete_option( $name ) { ++$GLOBALS['mutations']; unset( $GLOBALS['db']['options'][ $name ] ); }
function get_post_meta( $id, $key = '', $single = false ) { $meta = $GLOBALS['db']['meta'][ $id ] ?? array(); if ( '' === $key ) { return $meta; } $v = $meta[ $key ] ?? array(); return $single ? ( isset( $v[0] ) ? maybe_unserialize( $v[0] ) : '' ) : array_map( 'maybe_unserialize', $v ); }
function update_post_meta( $id, $key, $value ) { ++$GLOBALS['mutations']; if ( ( $GLOBALS['fail_meta_key'] ?? null ) === $key ) { unset( $GLOBALS['fail_meta_key'] ); return false; } if ( $GLOBALS['fail_after_post'] ) { throw new RuntimeException( 'Injected metadata failure after post mutation.' ); } $GLOBALS['db']['meta'][ $id ][ $key ] = array( serialize( wp_unslash( $value ) ) ); }
function add_post_meta( $id, $key, $value ) { ++$GLOBALS['mutations']; $GLOBALS['db']['meta'][ $id ][ $key ][] = serialize( wp_unslash( $value ) ); }
function maybe_unserialize( $v ) { $x = @unserialize( $v ); return false !== $x || 'b:0;' === $v ? $x : $v; }
function wp_cache_delete( ...$args ) { $GLOBALS['cache_deletions'][] = $args; }
function clean_post_cache( $id ) {}
function remove_action( ...$args ) { $GLOBALS['removed_actions'][] = $args; }
function add_action( ...$args ) {}
function add_filter( ...$args ) { $GLOBALS['registered_filters'][] = $args[0]; }
function wp_slash( $v ) { return is_array( $v ) ? array_map( 'wp_slash', $v ) : ( is_string( $v ) ? addslashes( $v ) : $v ); }
function wp_unslash( $v ) { return is_array( $v ) ? array_map( 'wp_unslash', $v ) : ( is_string( $v ) ? stripslashes( $v ) : $v ); }
function is_404() { return false; }
function is_search() { return false; }
function is_feed() { return false; }
function is_paged() { return false; }
function is_wp_error( $v ) { return false; }
function wp_insert_post( $row, $error = false ) { $id = ++$GLOBALS['next_id']; $row = wp_unslash( $row ); $row['ID'] = (string) $id; $row['guid'] = home_url( '/?page_id=' . $id ); $row['post_modified'] = '2026-11-10 12:00:00'; $GLOBALS['db']['posts'][ $id ] = $row; ++$GLOBALS['mutations']; return $id; }
function wp_update_post( $row, $error = false ) { $row = wp_unslash( $row ); $id = (int) $row['ID']; $row['ID'] = (string) $id; $GLOBALS['db']['posts'][ $id ] = array_replace( $GLOBALS['db']['posts'][ $id ], $row ); ++$GLOBALS['mutations']; return $id; }
function wp_delete_post( $id, $force = false ) { ++$GLOBALS['mutations']; unset( $GLOBALS['db']['posts'][ $id ], $GLOBALS['db']['meta'][ $id ] ); return true; }
function get_page_by_path( $path, $format = OBJECT, $type = 'page' ) {
    $parent = 0; $found = null;
    foreach ( explode( '/', trim( $path, '/' ) ) as $slug ) {
        $found = null;
        foreach ( $GLOBALS['db']['posts'] as $post ) { if ( $slug === $post['post_name'] && (int) $post['post_parent'] === $parent ) { $found = (object) $post; break; } }
        if ( ! $found ) { return null; } $parent = (int) $found->ID;
    }
    return $found;
}
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function esc_attr( $v ) { return htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $v ) { return htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $v ) { return esc_attr( $v ); }
function get_header() { echo '<main>'; }
function get_footer() { teznevise_the_page_leftover_content(); echo '</main><footer>Footer</footer>'; }
function have_posts() { return ( $GLOBALS['template_iteration'] ?? 0 ) < 1; }
function the_post() { ++$GLOBALS['template_iteration']; }
function get_the_ID() { return $GLOBALS['template_post_id']; }
function get_post_field( $field, $id ) { return $GLOBALS['db']['posts'][ $id ][ $field ]; }
function the_title() { echo esc_html( get_post_field( 'post_title', get_the_ID() ) ); }
function teznevise_the_page_leftover_content() {
    static $printed = array(); $id = get_the_ID();
    if ( isset( $printed[ $id ] ) ) { return; } $printed[ $id ] = true;
    echo '<div class="legacy-leftovers">' . get_post_field( 'post_content', $id ) . '</div>';
}

function check( $bool, $message ) { if ( ! $bool ) { throw new RuntimeException( 'FAIL: ' . $message ); } }
function expect_error( $call, $contains ) { try { $call(); } catch ( Throwable $e ) { check( str_contains( $e->getMessage(), $contains ), $e->getMessage() ); return; } throw new RuntimeException( 'Expected refusal: ' . $contains ); }
if ( defined( 'TZSEO_MOCKS_ONLY' ) ) { return; }
require __DIR__ . '/wordpress.php';
update_option( TZSEO_OPTION, array( 'enabled' => true ), false );
require __DIR__ . '/../../inc/seo-onpage-20261110.php';
check( in_array( 'theme_mod_teznevise_hero_title_1', $GLOBALS['registered_filters'], true ), 'homepage uses actual prefixed theme mod hook' );
$package = $test_root . '/package'; mkdir( $package, 0700 ); mkdir( $package . '/content', 0700 ); mkdir( $package . '/audit', 0700 );
$faq = array( '@type' => 'FAQPage', 'mainEntity' => array( array( 'name' => 'الف' ), array( 'name' => 'ب' ) ) );
file_put_contents( $package . '/content/a.html', '<h2>آموزش پژوهش</h2><p>متن آزمایشی.</p><script type="application/ld+json">' . json_encode( $faq ) . '</script>' );
$records = array();
foreach ( array( '/thesis/', '/thesis/master/' ) as $path ) { $records[] = array( 'path' => $path, 'title' => 'عنوان', 'seo_title' => 'عنوان جستجو', 'description' => 'شرح', 'keyword' => 'پژوهش', 'content_file' => 'content/a.html' ); }
file_put_contents( $package . '/content/home.html', '<h2>منابع آموزشی</h2><p>خانه منابع و ابزار پژوهش.</p>' );
$records[] = array( 'path' => '/', 'title' => 'خانه', 'seo_title' => 'خانه', 'description' => 'شرح خانه', 'content_file' => 'content/home.html' );
file_put_contents( $package . '/manifest.json', json_encode( array( 'pages' => $records ) ) );
file_put_contents( $package . '/audit/redirect-map.csv', "old_url,new_url,reason\n/service-thesis/,/thesis/,alias\n" );
file_put_contents( $package . '/audit/internal-links.csv', "source_url,target_url,anchor,reason\n/blog/example/,/thesis/,راهنمای پژوهش,context\n" );
file_put_contents( $package . '/audit/bluethesis-links.csv', "source_url,target_url,action\n/blog/example/,https://bluethesis.ir/statistical-analysis/,nofollow\n" );
file_put_contents( $package . '/audit/exact-anchor-rewrites.csv', "source_url,target_url,old_anchor,new_anchor,reason\n/blog/example/,/thesis/,انجام پایان نامه,آموزش مسیر پژوهش,ratio\n/blog/example/,/,انجام پروژه های دانشجویی,راهنمای عمومی پژوهش,home-scope\n" );
try {
    foreach ( array( false, true ) as $existing_option ) {
        $GLOBALS['db'] = array( 'posts' => array(), 'meta' => array(), 'options' => array() );
        $id = wp_insert_post( array( 'post_name' => 'thesis', 'post_parent' => 0, 'post_title' => 'Original title', 'post_content' => 'Original content', 'post_type' => 'page', 'post_status' => 'publish', 'post_excerpt' => 'Original excerpt' ) );
        add_post_meta( $id, 'arbitrary', array( 'nested' => 'original' ) ); add_post_meta( $id, 'arbitrary', 'second-value' ); add_post_meta( $id, 'regex-and-json', wp_slash( array( 'json' => '{"path":"C:\\folder"}', 'regex' => '\\w+\\s+' ) ) );
        if ( $existing_option ) { update_option( TZSEO_OPTION, array( 'enabled' => false, 'preexisting' => 'preserve me' ), true ); }
        $home_id = wp_insert_post( array( 'post_name' => 'original-home-slug', 'post_parent' => 0, 'post_title' => 'Original home title', 'post_content' => 'Original home content', 'post_type' => 'page', 'post_status' => 'publish', 'post_excerpt' => 'Original home excerpt' ) );
        update_option( 'page_on_front', $home_id ); update_option( 'show_on_front', 'page' );
        $baseline = $GLOBALS['db']; $backup = $test_root . '/backup-' . (int) $existing_option;
        $count = $GLOBALS['mutations']; tzseo_run( array( 'apply-dry-run', $package, $backup ) );
        check( $count === $GLOBALS['mutations'] && ! file_exists( $backup ), 'dry run performs zero writes' );
        tzseo_run( array( 'apply', $package, $backup ) );
        check( in_array( array( 'wp_after_insert_post', 'wp_save_post_revision_on_insert', 9 ), $GLOBALS['removed_actions'], true ), 'suppresses modern automatic revision writes' );
        check( 3 === count( $GLOBALS['db']['posts'] ), 'creates exactly one child and reuses existing homepage' );
        check( 'original-home-slug' === $GLOBALS['db']['posts'][ $home_id ]['post_name'] && 0 === $GLOBALS['db']['posts'][ $home_id ]['post_parent'], 'homepage slug and parent preserved' );
        check( ! get_post_meta( $home_id, '_tez_seo20261110_faq', true ), 'homepage content requires no FAQ schema' );
        $original_digest = hash_file( 'sha256', $backup . '/before.json' );
        $count = $GLOBALS['mutations']; tzseo_run( array( 'apply', $package, $backup ) );
        check( $count === $GLOBALS['mutations'], 'repeat apply is database no-op' );
        check( $original_digest === hash_file( 'sha256', $backup . '/before.json' ), 'repeat preserves immutable original' );
        $GLOBALS['extra_data'] = 1; expect_error( static fn() => tzseo_run( array( 'rollback', $package, $backup ) ), 'newer comments' ); $GLOBALS['extra_data'] = 0;
        $GLOBALS['db']['posts'][ $id ]['post_title'] = 'Newer owner edit';
        expect_error( static fn() => tzseo_run( array( 'rollback', $package, $backup ) ), 'newer edits' );
        $GLOBALS['db']['posts'][ $id ]['post_title'] = 'عنوان';
        $count = $GLOBALS['mutations']; tzseo_run( array( 'rollback-dry-run', $package, $backup ) ); check( $count === $GLOBALS['mutations'], 'rollback dry run zero mutations' );
        tzseo_run( array( 'rollback', $package, $backup ) );
        check( $baseline === $GLOBALS['db'], 'restores every post field, duplicate/nested metadata and present/absent option exactly' );
        $count = $GLOBALS['mutations']; tzseo_run( array( 'rollback', $package, $backup ) ); check( $count === $GLOBALS['mutations'], 'repeat rollback no-op' );
        file_put_contents( $backup . '/before.json', "\n", FILE_APPEND );
        expect_error( static fn() => tzseo_run( array( 'rollback', $package, $backup ) ), 'checksum' );
    }
    $baseline = $GLOBALS['db']; $GLOBALS['fail_after_post'] = true;
    expect_error( static fn() => tzseo_run( array( 'apply', $package, $test_root . '/crash-backup' ) ), 'Injected' );
    $GLOBALS['fail_after_post'] = false;
    check( $baseline === $GLOBALS['db'], 'failure after post update rolls back database transaction' );
    check( in_array( array( TZSEO_OPTION, 'options' ), $GLOBALS['cache_deletions'], true ) && in_array( array( $id, 'post_meta' ), $GLOBALS['cache_deletions'], true ), 'failed base transaction invalidates targeted option and metadata cache' );
    tzseo_run( array( 'rollback', $package, $test_root . '/crash-backup' ) );
    check( $baseline === $GLOBALS['db'], 'rollback safely closes incomplete transaction journal' );
    expect_error( static fn() => tzseo_run( array( 'apply', $package, ABSPATH . 'unsafe-backup' ) ), 'outside the web root' );
    update_option( TZSEO_OPTION, tzseo_plan( $package ), false ); $_SERVER['REQUEST_URI'] = '/blog/example/';
    $html = '<html><body><main><p><a href="/">انجام پروژه های دانشجویی</a><a href="/thesis/" class="x"><span>انجام پایان نامه</span></a> <a href="https://other.ir/thesis/">انجام پایان نامه</a> <a rel="noopener" href="https://bluethesis.ir/statistical-analysis/">تحلیل</a> <a href="https://bluethesis.ir/other/">دیگر</a></p></main></body></html>';
    $after = tezseo20261110_transform_html( $html );
    check( 1 === substr_count( $after, 'id="tezseo-20261110-context"' ), 'one context paragraph' );
    check( str_contains( $after, 'class="x">آموزش مسیر پژوهش</a>' ), 'existing exact anchor rewritten, attributes retained' );
    check( str_contains( $after, 'href="/">راهنمای عمومی پژوهش</a>' ), 'homepage anchor rewrite normalizes root as one slash' );
    check( str_contains( $after, 'https://other.ir/thesis/">انجام پایان نامه' ), 'external same-path link untouched' );
    check( str_contains( $after, 'rel="noopener nofollow"' ), 'nofollow appends without dropping existing rel' );
    check( ! str_contains( $after, 'rel="nofollow" href="https://bluethesis.ir/other/' ), 'unrelated external link untouched' );
    check( $after === tezseo20261110_transform_html( $after ), 'HTML transform idempotent' );
    $_SERVER['REQUEST_URI'] = '/thesis/';
    $graph = array( array( '@type' => 'WebSite' ), array( '@type' => 'FAQPage', 'mainEntity' => array() ), array( '@type' => array( 'WebPage', 'FAQPage' ), 'mainEntity' => array() ) );
    $cleaned = tezseo20261110_schema_without_faq( $graph );
    check( array_is_list( $cleaned ) && 2 === count( $cleaned ) && array( 'WebPage' ) === $cleaned[1]['@type'] && ! isset( $cleaned[1]['mainEntity'] ), 'FAQ removal preserves graph list and multi-type WebPage' );
    check( array() === tezseo20261110_schema_without_faq( array( '@type' => 'FAQPage' ) ), 'root FAQ removed' );
    update_option( 'blog_public', '0' ); check( array( 'noindex' => true ) === tezseo20261110_robots( array( 'noindex' => true ) ), 'staging noindex preserved' );
    update_option( 'blog_public', '1' ); check( ! isset( tezseo20261110_robots( array( 'noindex' => true ) )['noindex'] ), 'public managed page allows indexing' );
    require __DIR__ . '/../../inc/seo-onpage-20261110.php'; // second MU/theme load must safely skip declarations and hooks
    $home_config = get_option( TZSEO_OPTION ); $home_config['pages']['/']['id'] = $home_id;
    update_option( TZSEO_OPTION, $home_config, false ); $_SERVER['REQUEST_URI'] = '/';
    $GLOBALS['db']['posts'][ $home_id ]['post_content'] = file_get_contents( $package . '/content/home.html' );
    $home_rendered = tezseo20261110_transform_html( '<main><h1>خانه</h1></main>' );
    check( 1 === substr_count( $home_rendered, 'منابع آموزشی' ) && str_contains( $home_rendered, 'tezseo-20261110-home-intro' ), 'missing homepage classic copy is added once' );
    check( $home_rendered === tezseo20261110_transform_html( $home_rendered ), 'homepage fallback idempotent' );
    $existing_home = '<main>' . $GLOBALS['db']['posts'][ $home_id ]['post_content'] . '</main>';
    check( $existing_home === tezseo20261110_transform_html( $existing_home ), 'already rendered homepage heading prevents duplicate copy' );
    $GLOBALS['template_post_id'] = $id; $GLOBALS['template_iteration'] = 0;
    ob_start(); require __DIR__ . '/../../inc/seo-onpage-20261110-template.php'; $rendered = ob_get_clean();
    check( 1 === substr_count( $rendered, 'Original content' ) && ! str_contains( $rendered, 'legacy-leftovers' ), 'managed template prints body once and consumes legacy footer leftovers' );
    echo "PASS: apply/rollback dry-run, repeat idempotence, backups, exact restoration, newer-edit guard, transaction failure recovery, checksum, unsafe backup refusal, scoped rendered links and nofollow.\n";
} finally {
    $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $test_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
    foreach ( $it as $file ) { $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() ); } rmdir( $test_root );
}
