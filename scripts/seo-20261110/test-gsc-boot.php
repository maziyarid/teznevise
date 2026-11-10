<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Model MU-plugin loading before WordPress pluggable functions exist.
 */
define( 'ABSPATH', sys_get_temp_dir() . '/' );
function get_option( $key, $default = false ) { return array( 'enabled' => true ); }
function add_filter( ...$args ) {}
function add_action( ...$args ) { $GLOBALS['actions'][] = $args; }
function did_action( $name ) { return 0; }
$_SERVER['REQUEST_URI'] = '/old-guide/';
require __DIR__ . '/../../inc/seo-gsc-20261110.php';
if ( ! in_array( array( 'plugins_loaded', 'tezgsc20261110_route', -9999 ), $GLOBALS['actions'] ?? array(), true ) ) { throw new RuntimeException( 'MU routing must wait for pluggable functions.' ); }
if ( function_exists( 'wp_safe_redirect' ) || function_exists( 'is_user_logged_in' ) ) { throw new RuntimeException( 'Boot fixture unexpectedly provides pluggable functions.' ); }
echo "PASS: active MU runtime defers routing until plugins_loaded.\n";
