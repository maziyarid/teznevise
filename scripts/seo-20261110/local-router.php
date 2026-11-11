<?php
/** Isolated existing WordPress render; never writes configuration or page content. */
$root = getenv( 'WP_TEST_ROOT' );
if ( ! $root || ! is_file( $root . '/index.php' ) ) {
    http_response_code( 503 );
    exit( 'Local WordPress root is unavailable.' );
}
$request_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$resolved = realpath( $root . $request_path );
if ( $resolved && strpos( $resolved, realpath( $root ) . DIRECTORY_SEPARATOR ) === 0 && is_file( $resolved ) ) {
    return false;
}
define( 'DISABLE_WP_CRON', true );
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'WP_HOME', 'http://127.0.0.1:8089' );
define( 'WP_SITEURL', 'http://127.0.0.1:8089' );
require $root . '/index.php';
